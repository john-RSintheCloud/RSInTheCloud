<?php

/**
 * RS uses dbStruct files to manage schema changes.  It's not perfect,
 * but it works and we will carry on using it for now.
 *
 * This class encapulates their creation and use.
 *
 * @author John
 */
class database_dbStruct
{

    /**
     *
     * @var string default dbstruct file path
     */
    static protected $_structPath = 'modules/database/dbstruct/';

    /**
     *
     * @var string - full path to dbstruct
     */
    protected $dbstructPath = '';

    /**
     *
     * @var string - table name to look up
     */
    protected $table = '';

    /**
     *
     * @var array of arrays - table description pulled from dbstruct
     */
    protected $description = array();


    public function __construct($param = array())
    {
        $this->dbstructPath = APPLICATION_PATH . self::$_structPath;
        foreach ($param as $pkey => $pvalue) {
            $this->$pkey = $pvalue;
        }
    }


    public function getDbStruct($table = '')
    {
        if (!empty($table)){
            $this->table = (string) $table;
        }

        $f = fopen($this->dbstructPath . "table_" . $this->table . ".txt", "r");

        $describe = array();
        while ( ($describe[] = fgetcsv($f)) !== false);

        fclose($f);
        array_pop($describe); //  take the false off the end

        $this->description = $describe;

        return $this;

    }

    public function getFieldNames()
    {
        if (empty($this->description)){
            throw new RuntimeException('no table loaded to get field names');
        }
        $names = array();
        foreach ($this->description as $field) {
            $names[] = $field[0];
        }

        return $names;
    }

    static public function createDbStruct($param = array())
    {
        //  Defaults

        $dbstructPath = APPLICATION_PATH . self::$_structPath;

        # Specify whether you want to have table_* and index_* files created
        $createTableStructure = true;
        $createIndices = true;
        $createData = true;

        # Use the below to set which tables we will extract data for - empty array means all tables.
        #$dataFor=array("usergroup","resource_type_field","site_text","user","collection","user_collection","report","preview_size","resource_type");
        $dataFor = array();
        $tableFor = array();
        $indicesFor = array();

        //  db must be injected
        $db = new database_Db();

        //  overwrite defaults

        foreach ($param as $pkey => $pvalue) {
            $$pkey = $pvalue;
        }


        # Fetch all tables
        if (! $db->sqlQuery("show tables")){
                return 'Invalid Database';
        }
        $tables = $db->getData();

        foreach( $tables as $tableArray) {
            $table = reset($tableArray);

            # Table structure
            if ($createTableStructure && (in_array($table, $tableFor) || count($tableFor) === 0)) {
                $f = fopen($dbstructPath . "table_" . $table . ".txt", "w");
                $describe = sql_query("describe $table");
                for ($m = 0; $m < count($describe); $m++) {
                    fputcsv($f, $describe[$m]);
                }
                fclose($f);
            }

            # Indices
            if ($createIndices && (in_array($table, $indicesFor) || count($indicesFor) === 0)) {
                $f = fopen($dbstructPath . "index_" . $table . ".txt", "w");
                $index = sql_query("show index from $table");
                for ($m = 0; $m < count($index); $m++) {
                    fputcsv($f, $index[$m]);
                }
                fclose($f);
            }

            # Data
            if ($createData && (in_array($table, $dataFor) || count($dataFor) === 0)) {
                $f = fopen($dbstructPath . "data_" . $table . ".txt", "w");
                $index = sql_query("select * from $table");
                for ($m = 0; $m < count($index); $m++) {
                    fputcsv($f, $index[$m]);
                }
                fclose($f);
            }
        }
        $ret = '<p><b>Writing to ' . $dbstructPath . '</b></p>';
        $ret .= self::printArray('Created tables', $createTableStructure, $tableFor);
        $ret .= self::printArray('Created indices for tables', $createIndices,
                $indicesFor);
        $ret .= self::printArray('Created data for tables', $createData, $dataFor);

        return $ret;
    }

    static public function printArray($label, $show, $array)
    {
//        var_dump($show); die;
        if (!$show)
            return;
        $ret = '<p><b>' . $label . ':</b> ';
        if (count($array) == 0) {
            $ret .= 'for all tables</p>';
            return $ret;
        }

        $first = true;
        foreach ($array as $item) {
            if ($first)
                $first = false;
            else
                $ret .= ', ';
            $ret .= $item;
        }
        $ret .= '</p>';

        return $ret;
    }

}
