<?php

/**
 * userGroup Wrapper
 * A wrapper round the userGroup functions, to allow a future move to OO design.
 *
 * @author John Brookes <john@RSintheCloud.com>
 * @package RSintheClouds
 * @subpackage Refactor
 */


//  Dependency Injection
//  Our DIC needs to know about all our classes,
//  but this is an attempt to add them in an orderly manner.
//  Once the system is all OO, this can be moved to the main container->init method.

//  UserGroup Table requires db injection
$container['userGroupTable'] = $container->share( function ($c) {
    $table = new userGroup_userGroupTable();
    $table->setDb($c['db']);
    return $table;
});

//  UserGroup Mapper requires userGroup table
$container['userGroupMapper'] = $container->share( function ($c) {
    $mapper = new userGroup_userGroupMapper();
    $mapper->setTable($c['userGroupTable']);
    return $mapper;
});

//  UserGroup model requires userGroup mapper.
//  There may be multiple userGroups at any one time
$container['userGroup'] =  function ($c) {
    $userGroup = new userGroup_model_userGroup();
    $userGroup->setMapper($c['userGroupMapper']);
    return $userGroup;
};


///////////////////////////////////////////  LEGACY CODE

function getUsergroups($usepermissions = false, $find = "")
{
    # Returns a list of user groups. The standard user groups are translated using $lang. Custom user groups are i18n translated.
    # Puts anything starting with 'General Staff Users' - in the English default names - at the top (e.g. General Staff).
    # Creates a query, taking (if required) the permissions  into account.
    $sql = "";
    if ($usepermissions && checkperm("U")) {
        # Only return users in children groups to the user's group
        global $usergroup, $U_perm_strict;
        if ($sql == "") {
            $sql = "where ";
        } else {
            $sql.= " and ";
        }
        if ($U_perm_strict) {
            //$sql.= "(parent='$usergroup')";
            $sql.= "find_in_set('" . $usergroup . "',parent)";
        } else {
            //$sql.= "(ref='$usergroup' or parent='$usergroup')";
            $sql.= "(ref='$usergroup' or find_in_set('" . $usergroup . "',parent))";
        }
    }

    # Executes query.
    global $default_group;
    $r = sql_query("select * from usergroup $sql order by (ref='$default_group') desc,name");

    # Translates group names in the newly created array.
    $return = array();
    for ($n = 0; $n < count($r); $n++) {
        $r[$n]["name"] = lang_or_i18n_get_translated($r[$n]["name"],
                "usergroup-");
        $return[] = $r[$n]; # Adds to return array.
    }

    if (strlen($find) > 0) {
        # Searches for groups with names which contains the string defined in $find.
        $initial_length = count($return);
        for ($n = 0; $n < $initial_length; $n++) {
            if (strpos(strtolower($return[$n]["name"]), strtolower($find)) === false) {
                unset($return[$n]); # Removes this group.
            }
        }
        $return = array_values($return); # Reassigns the indices.
    }

    return $return;
}

function getUsergroupName($ref)
{
    # Returns the user group corresponding to the group ref. 
    # A standard user group name is translated using $lang. 
    # A custom user group name is i18n translated.

    $return = sql_query("select * from usergroup where ref='$ref'");
    if (count($return) == 0) {
        return false;
    } else {
        $return[0]["name"] = lang_or_i18n_get_translated($return[0]["name"],
                "usergroup-");
        return $return[0];
    }
}


function resolveUserlistGroups($userlist)
{
    # Given a comma separated user list (from the user select include file) turn all Group: entries into fully resolved list of usernames.
    # Note that this function can't decode default groupnames containing special characters.

    global $lang;
    $ulist = explode(",", $userlist);
    $newlist = "";
    for ($n = 0; $n < count($ulist); $n++) {
        $u = trim($ulist[$n]);
        if (strpos($u, $lang["group"] . ": ") === 0) {
            # Group entry, resolve
            # Find the translated groupname.
            $translated_groupname = trim(substr($u,
                            strlen($lang["group"] . ": ")));
            # Search for corresponding $lang indices.
            $default_group = false;
            $langindices = array_keys($lang, $translated_groupname);
            if (count($langindices) > 0)
                ; {
                foreach ($langindices as $langindex) {
                    # Check if it is a default group
                    if (strstr($langindex, "usergroup-") !== false) {
                        # Decode the groupname by using the code from lang_or_i18n_get_translated the other way around (it could be possible that someone have renamed the English groupnames in the language file).
                        $untranslated_groupname = trim(substr($langindex,
                                        strlen("usergroup-")));
                        $untranslated_groupname = str_replace(array("_", "and"),
                                array(" "), $untranslated_groupname);
                        $groupref = sql_value("select ref as value from usergroup where lower(name)='$untranslated_groupname'",
                                false);
                        if ($groupref !== false) {
                            $default_group = true;
                            break;
                        }
                    }
                }
            }
            if ($default_group == false) {
                # Custom group
                # Decode the groupname
                $untranslated_groups = sql_query("select ref, name from usergroup");
                foreach ($untranslated_groups as $group) {
                    if (i18n_get_translated($group['name']) == $translated_groupname) {
                        $groupref = $group['ref'];
                        break;
                    }
                }
            }

            # Find and add the users.
            $users = sql_array("select username value from user where usergroup='$groupref'");
            if ($newlist != "") {
                $newlist.=",";
            }
            $newlist.=join(",", $users);
        } else {
            # Username, just add as-is
            if ($newlist != "") {
                $newlist.=",";
            }
            $newlist.=$u;
        }
    }
    return $newlist;
}


