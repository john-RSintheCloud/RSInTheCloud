<?php

/**
 * dbWrapper
 * A wrapper round the database functions, to allow a future move to OO design.
 *
 * @author John Brookes <john@RSintheCloud.com>
 * @package RSintheClouds
 * @subpackage Refactor
 */

//  Dependency Injection
//  Our DIC needs to know about all our classes,
//  but this is an attempt to add them in an orderly manner.
//  Once the system is all OO, this can be moved to the main container->init method.

    /**
    * Database Connector
    */
    $container['pdoConfig'] = $container->share( function ($c) {
        $conn = new database_PdoConfig(
            $c['config']->getDbConfig()
            );
            var_dump($c['config']); die;
        return $conn;
    });
    $container['PdoConnector'] = $container->share( function ($c) {
        $conn = new PDO( $c['pdoConfig']->getConfig(), 
                $c['pdoConfig']->getUsername(), $c['pdoConfig']->getPassword());
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn ;
    });

    /**
     * DB Query Runner
     */
    $container['db'] = $container->share( function ($c) {
        return new database_Db($c['PdoConnector']);

    });

    /**
     * DB structure manager
     */
    $container['dbStruct'] = $container->share( function ($c) {
        return new database_dbStruct();

    });

    //  ALL TABLES AND MAPPERS ARE DEFINED IN HERE
    //
    //  User Table
    $container['userTable'] = $container->share( function ($c) {
        $table = new database_table_abstract(array(
            'db' => $c['db'],
            'table' => 'user',
            'fields' => $c['dbStruct']->getDbStruct('user')->getFieldNames()
        ));
        return $table;
            });

     //  User mapper requires table injection
    $container['userMapper'] = $container->share( function ($c) {
        $mapper = new database_mapper_user();
        $mapper->setTable($c['userTable']);
        return $mapper;
            });

   //  Asset Table requires db injection
    $container['table_asset'] = $container->share( function ($c) {
        $table = new database_table_asset();
        $table->setDb($c['db']);
        return $table;
            });

    //  Asset mapper requires table injection
    $container['assetMapper'] = $container->share( function ($c) {
        $mapper = new database_table_assetMapper();
        $mapper->setTable($c['db']);
        return $mapper;
            });

    //  Basket Table requires db injection
    $container['table_basket'] = $container->share( function ($c) {
        $table = new database_table_basket();
        $table->setDb($c['db']);
        return $table;
            });

    //  Resource Table requires db injection
    $container['table_resource'] = $container->share( function ($c) {
        $table = new database_table_resource();
        $table->setDb($c['db']);
        return $table;
            });



//  Config
# If true, it does not remove the backslash from DB queries, and doesn't do any special processing.
# to them. Unless you need to store '\' in your fields, you can safely keep the default.
$mysql_verbatim_queries = false;


# *** CONNECT TO DATABASE ***

$db = mysqli_connect($mysql_server, $mysql_username, $mysql_password, $mysql_db);
mysqli_set_charset($db, "utf8");

# Set MySQL Strict Mode
# The typical error caused is when the empty string ('') is inserted into a
# numeric column when NULL should be inserted instead.
# With Strict Mode turned off, MySQL inserts NULL without complaining.
# With Strict Mode turned on, a warning/error is generated.
# So, stop being lazy and do the job right!
sql_query("SET SESSION sql_mode='STRICT_ALL_TABLES'");


# statistics
$querycount = 0;
$querytime = 0;
$querylog = array();

function sql_query($sql, $cache = false, $fetchrows = -1, $dbstruct = true) {
    # sql_query(sql) - execute a query and return the results as an array.
    # Database functions are wrapped in this way so supporting a database server other than MySQL is
    # easier.
    # $cache is not used at this time - it was intended for disk based results caching which may be added in the future.
    # If $fetchrows is set we don't have to loop through all the returned rows. We
    # just fetch $fetchrows row but pad the array to the full result set size with empty values.
    # This has been added retroactively to support large result sets, yet a pager can work as if a full
    # result set has been returned as an array (as it was working previously).
    global $db, $querycount, $querytime, $config_show_performance_footer, $querylog, $debug_log, $mysql_verbatim_queries;
    $counter = 0;
    if ($config_show_performance_footer) {
        # Stats
        # Start measuring query time
        $time_start = microtime(true);
        $querycount++;
    }

    if ($debug_log) {
        debug("SQL: " . $sql);
    }

    # Execute query
    $result = mysqli_query($db, $sql);

    if ($config_show_performance_footer) {
        # Stats
        # Log performance data
        $time_end = microtime(true);
        $time_total = ($time_end - $time_start);
        if (isset($querylog[$sql])) {
            $querylog[$sql]['dupe'] = $querylog[$sql]['dupe'] + 1;
            $querylog[$sql]['time'] = $querylog[$sql]['time'] + $time_total;
        } else {
            $querylog[$sql]['dupe'] = 1;
            $querylog[$sql]['time'] = $time_total;
        }

        $querytime += $time_total;
    }

    $error = mysqli_error($db);

    if ($error != "") {
        if ($error == "Server shutdown in progress") {
            echo "<span class=error>Sorry, but this query would return too many results. Please try refining your query by adding addition keywords or search parameters.<!--$sql--></span>";
        } elseif (substr($error, 0, 15) == "Too many tables") {
            echo "<span class=error>Sorry, but this query contained too many keywords. Please try refining your query by removing any surplus keywords or search parameters.<!--$sql--></span>";
        } else {
            # Check that all database tables and columns exist using the files in the 'dbstruct' folder.
            if ($dbstruct) { # should we do this?
                CheckDBStruct("dbstruct");
                global $plugins;
                for ($n = 0; $n < count($plugins); $n++) {
                    CheckDBStruct("plugins/" . $plugins[$n] . "/dbstruct");
                }

                # Try again (no dbstruct this time to prevent an endless loop)
                return sql_query($sql, $cache, $fetchrows, false);
                exit();
            }

            errorhandler("N/A", $error . "<br/><br/>" . $sql, "(database)", "N/A");
        }
        exit;
    } elseif ($result === true) {
        # no result set, (query was insert, update etc.)
    } else {
        $row = array();
        while (($rs = mysqli_fetch_assoc($result)) && (($counter < $fetchrows) || ($fetchrows == -1))) {
            while (list($name, $value) = each($rs)) {
                //if (!is_integer($name)) # do not run for integer values (MSSQL returns two keys for each returned column, a numeric and a text)
                //    {
                $row[$counter][$name] = $mysql_verbatim_queries ? $value : str_replace("\\", "", stripslashes($value));
                //    }
            }
            $counter++;
        }

        # If we haven't returned all the rows ($fetchrows isn't -1) then we need to fill the array so the count
        # is still correct (even though these rows won't be shown).
        $rows = count($row);
        $totalrows = mysqli_num_rows($result);
        if (($fetchrows != -1) && ($rows < $totalrows)) {
            $row = array_pad($row, $totalrows, 0);
        }
        return $row;
    }
}

function sql_value($query, $default) {
    # return a single value from a database query, or the default if no rows
    # The value returned must have the column name aliased to 'value'
    $result = sql_query($query);
    if (count($result) == 0) {
        return $default;
    } else {
        return $result[0]["value"];
    }
}

function sql_array($query) {
    # Like sql_value() but returns an array of all values found.
    # The value returned must have the column name aliased to 'value'
    $return = array();
    $result = sql_query($query);
    for ($n = 0; $n < count($result); $n++) {
        $return[] = $result[$n]["value"];
    }
    return $return;
}

function sql_insert_id() {
    # Return last inserted ID (abstraction)
    global $use_mysqli;
    if ($use_mysqli) {
        global $db;
        return mysqli_insert_id($db);
    } else {
        return mysql_insert_id();
    }
}


/**
 * Currently this function is called to escape values before injecting into the query.
 * As we stop creating queries in code, this will no longer be needed.
 *
 * @global MySqli $db
 * @param string $text
 * @return string
 */
function escape_check($text) { #only escape a string if we need to, to prevent escaping an already escaped string
    global $db;
//    $text = mysqli_real_escape_string($db, $text);

    # turn all \\' into \'
    while (!(strpos($text, "\\\\'") === false)) {
        $text = str_replace("\\\\'", "\\'", $text);
    }

    # Remove any backslashes that are not being used to escape single quotes.
    $text = str_replace("\\'", "{bs}'", $text);
    $text = str_replace("\\n", "{bs}n", $text);
    $text = str_replace("\\r", "{bs}r", $text);

    $text = str_replace("\\", "", $text);
    $text = str_replace("{bs}'", "\\'", $text);
    $text = str_replace("{bs}n", "\\n", $text);
    $text = str_replace("{bs}r", "\\r", $text);

    return $text;
}

/**
 * This is only needed because of those stupid magic quotes used in PHP4 -
 * so we'll be glad to see the back of it!
 *
 * @param string $text
 * @return string
 */
function unescape($text) {
    // for comparing escape_checked strings against mysql content because
    // just doing $text=str_replace("\\","",$text);	does not undo escape_check
    # Remove any backslashes that are not being used to escape single quotes.
    $text = str_replace("\\'", "\'", $text);
    $text = str_replace("\\n", "\n", $text);
    $text = str_replace("\\r", "\r", $text);
    $text = str_replace("\\", "", $text);


    return $text;
}

function sql_affected_rows() {
    global $db;
    return mysqli_affected_rows($db);
}




function CheckDBStruct($path) {
    # Check the database structure against the text files stored in $path.
    # Add tables / columns / data / indices as necessary.
    global $mysql_db, $resource_field_column_limit;

    # Check for path
    $path = dirname(__FILE__) . "/../" . $path; # Make sure this works when called from non-root files..
    if (!file_exists($path)) {
        return false;
    }

    # Tables first.
    # Load existing tables list
    $ts = sql_query("show tables");
    $tables = array();
    for ($n = 0; $n < count($ts); $n++) {
        $tables[] = $ts[$n]["Tables_in_" . $mysql_db];
    }
    $dh = opendir($path);
    while (($file = readdir($dh)) !== false) {
        if (substr($file, 0, 6) == "table_") {
            $table = str_replace(".txt", "", substr($file, 6));

            # Check table exists
            if (!in_array($table, $tables)) {
                # Create Table
                $sql = "";
                $f = fopen($path . "/" . $file, "r");
                $hasPrimaryKey = false;
                $pk_sql = "PRIMARY KEY (";
                while (($col = fgetcsv($f, 5000)) !== false) {
                    if ($sql.="") {
                        $sql.=", ";
                    }
                    $sql.=$col[0] . " " . str_replace("Â§", ",", $col[1]);
                    if ($col[4] != "") {
                        $sql.=" default " . $col[4];
                    }
                    if ($col[3] == "PRI") {
                        if ($hasPrimaryKey) {
                            $pk_sql .= ",";
                        }
                        $pk_sql.=$col[0];
                        $hasPrimaryKey = true;
                    }
                    if ($col[5] == "auto_increment") {
                        $sql.=" auto_increment ";
                    }
                }
                $pk_sql .= ")";
                if ($hasPrimaryKey) {
                    $sql.="," . $pk_sql;
                }
                debug($sql);

                sql_query("create table $table ($sql)", false, -1, false);

                # Add initial data
                $data = str_replace("table_", "data_", $file);
                if (file_exists($path . "/" . $data)) {
                    $f = fopen($path . "/" . $data, "r");
                    while (($row = fgetcsv($f, 5000)) !== false) {
                        # Escape values
                        for ($n = 0; $n < count($row); $n++) {
                            $row[$n] = escape_check($row[$n]);
                            $row[$n] = "'" . $row[$n] . "'";
                            if ($row[$n] == "''") {
                                $row[$n] = "null";
                            }
                        }
                        sql_query("insert into $table values (" . join(",", $row) . ")", false, -1, false);
                    }
                }
            } else {
                # Table already exists, so check all columns exist
                # Load existing table definition
                $existing = sql_query("describe $table", false, -1, false);

                ##########
                # Copy needed resource_data into resource for search displays
                if ($table == "resource") {
                    $joins = get_resource_table_joins();
                    for ($m = 0; $m < count($joins); $m++) {

                        # Look for this column in the existing columns.
                        $found = false;

                        for ($n = 0; $n < count($existing); $n++) {
                            if ("field" . $joins[$m] == $existing[$n]["Field"]) {
                                $found = true;
                            }
                        }
                        if (!$found) {
                            # Add this column.
                            $sql = "alter table $table add column ";
                            $sql.="field" . $joins[$m] . " VARCHAR(" . $resource_field_column_limit . ")";
                            sql_query($sql, false, -1, false);
                            $values = sql_query("select resource,value from resource_data where resource_type_field=$joins[$m]");

                            for ($x = 0; $x < count($values); $x++) {
                                $value = $values[$x]['value'];
                                $resource = $values[$x]['resource'];
                                sql_query("update resource set field$joins[$m]='" . escape_check($value) . "' where ref=$resource");
                            }
                        }
                    }
                }
                ##########
                ##########
                ## RS-specific mod:
                # add theme columns to collection table as needed.
                global $theme_category_levels;
                if ($table == "collection") {
                    for ($m = 1; $m <= $theme_category_levels; $m++) {
                        if ($m == 1) {
                            $themeindex = "";
                        } else {
                            $themeindex = $m;
                        }
                        # Look for this column in the existing columns.
                        $found = false;

                        for ($n = 0; $n < count($existing); $n++) {
                            if ("theme" . $themeindex == $existing[$n]["Field"]) {
                                $found = true;
                            }
                        }
                        if (!$found) {
                            # Add this column.
                            $sql = "alter table $table add column ";
                            $sql.="theme" . $themeindex . " VARCHAR(100)";
                            sql_query($sql, false, -1, false);
                        }
                    }
                }

                ##########

                if (file_exists($path . "/" . $file)) {
                    $f = fopen($path . "/" . $file, "r");
                    while (($col = fgetcsv($f, 5000)) !== false) {
                        if (count($col) > 1) {
                            # Look for this column in the existing columns.
                            $found = false;
                            for ($n = 0; $n < count($existing); $n++) {
                                if ($existing[$n]["Field"] == $col[0]) {
                                    $found = true;
                                }
                            }
                            if (!$found) {
                                # Add this column.
                                $sql = "alter table $table add column ";
                                $sql.=$col[0] . " " . str_replace("Â§", ",", $col[1]); # Allow commas to be entered using 'Â§', necessary for a type such as decimal(2,10)
                                if ($col[4] != "") {
                                    $sql.=" default " . $col[4];
                                }
                                if ($col[3] == "PRI") {
                                    $sql.=" primary key";
                                }
                                if ($col[5] == "auto_increment") {
                                    $sql.=" auto_increment ";
                                }
                                sql_query($sql, false, -1, false);
                            }
                        }
                    }
                }
            }

            # Check all indices exist
            # Load existing indexes
            $existing = sql_query("show index from $table", false, -1, false);

            $file = str_replace("table_", "index_", $file);
            if (file_exists($path . "/" . $file)) {
                $done = array(); # List of indices already processed.
                $f = fopen($path . "/" . $file, "r");
                while (($col = fgetcsv($f, 5000)) !== false) {
                    # Look for this index in the existing indices.
                    $found = false;
                    for ($n = 0; $n < count($existing); $n++) {
                        if ($existing[$n]["Key_name"] == $col[2]) {
                            $found = true;
                        }
                    }
                    if (!$found && !in_array($col[2], $done)) {
                        # Add this index.
                        # Fetch list of columns for this index
                        $cols = array();
                        $f2 = fopen($path . "/" . $file, "r");
                        while (($col2 = fgetcsv($f2, 5000)) !== false) {
                            if ($col2[2] == $col[2]) {
                                $cols[] = $col2[4];
                            }
                        }

                        $sql = "create index " . $col[2] . " on $table (" . join(",", $cols) . ")";
                        sql_query($sql, false, -1, false);
                        $done[] = $col[2];
                    }
                }
            }
        }
    }
}

function daily_stat($activity_type, $object_ref) {
//        global $disable_daily_stat;
//        if ($disable_daily_stat === true) {
//            return;
//        }  //can be used to speed up heavy scripts	when stats are less important.
//        Uncomment to use.
//
    # Update the daily statistics after a loggable event.
    $date = getdate();
    $year = $date["year"];
    $month = $date["mon"];
    $day = $date["mday"];


    # Set object ref to zero if not set.

    if ($object_ref == "") {
        $object_ref = 0;
    }


    # Find usergroup
    global $usergroup;
    if (!isset($usergroup)) {
        $usergroup = 0;
    }

    # First check to see if there's a row
    $count = sql_value("select count(*) value from daily_stat where year='$year' and month='$month' and day='$day' and usergroup='$usergroup' and activity_type='$activity_type' and object_ref='$object_ref'", 0);
    if ($count == 0) {
        # insert
        sql_query("insert into daily_stat(year,month,day,usergroup,activity_type,object_ref,count) values ('$year','$month','$day','$usergroup','$activity_type','$object_ref','1')");
    } else {
        # update
        sql_query("update daily_stat set count=count+1 where year='$year' and month='$month' and day='$day' and usergroup='$usergroup' and activity_type='$activity_type' and object_ref='$object_ref'");
    }
}

function get_resource_table_joins() {

    global
    $rating_field,
    $sort_fields,
    $small_thumbs_display_fields,
    $xl_thumbs_display_fields,
    $thumbs_display_fields,
    $list_display_fields,
    $data_joins,
    $metadata_template_title_field,
    $view_title_field,
    $date_field,
    $config_sheetlist_fields,
    $config_sheetthumb_fields,
    $config_sheetsingle_fields;

    $joins = array_merge(
            $sort_fields, $small_thumbs_display_fields, $xl_thumbs_display_fields, $thumbs_display_fields, $list_display_fields, $data_joins, $config_sheetlist_fields, $config_sheetthumb_fields, $config_sheetsingle_fields, array(
        $rating_field,
        $metadata_template_title_field,
        $view_title_field,
        $date_field)
    );
//    $additional_joins = hook("additionaljoints");
//    if ($additional_joins)
//        $joins = array_merge($joins, $additional_joins);
    $joins = array_unique($joins);
    $n = 0;
    foreach ($joins as $join) {
        if ($join != "") {
            $return[$n] = $join;
            $n++;
        }
    }
    return $return;
}
