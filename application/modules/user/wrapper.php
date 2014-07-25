<?php
/**
 * url Wrapper
 * A wrapper round the user and password functions, to allow a future move to OO design.
 *
 * @author John Brookes <john@RSintheCloud.com>
 * @package RSintheClouds
 * @subpackage Refactor
 */
//  Dependency Injection
//  Our DIC needs to know about all our classes,
//  but this is an attempt to add them in an orderly manner.
//  Once the system is all OO, this can be moved to the main container->init method.
//  User Table and User Mapper added in the database wrapper
//  User model requires user mapper.
//  There may be multiple users at any one time, so use 'factory'
$container['user'] = $container->factory(function ($c) {
    return new user_model_user($c['userMapper']);
});

////////////////////////////////////////////  LEGACY FUNCTIONS WRAPPED INTO OBJECTS


function get_user($ref)
{
    global $container;

    $user = $container['user']->fetchOne(array('ref' => $ref));

    return $user->toLegacyArray();
}

function user_email_exists($email)
{
# Returns true if a user account exists with e-mail address $email
    $email = escape_check(trim(strtolower($email)));

    global $container;

    return $container['user']->isMailInUse($email);
}

///////////////////////////////////////////  LEGACY CODE

function get_users($group = 0, $find = "", $order_by = "u.username",
        $usepermissions = false, $fetchrows = -1)
{
# Returns a user list. Group or search term is optional.
# The standard user group names are translated using $lang. Custom user group names are i18n translated.

    $sql = "";
    if ($group != 0) {
        $sql = "where usergroup IN ($group)";
    }
    if (strlen($find) > 1) {
        if ($sql == "") {
            $sql = "where ";
        } else {
            $sql.= " and ";
        }
        $sql .= "(username like '%$find%' or fullname like '%$find%' or email like '%$find%')";
    }
    if (strlen($find) == 1) {
        if ($sql == "") {
            $sql = "where ";
        } else {
            $sql.= " and ";
        }
        $sql .= "username like '$find%'";
    }
    if ($usepermissions && checkperm("U")) {
# Only return users in children groups to the user's group
        global $usergroup;
        if ($sql == "") {
            $sql = "where ";
        } else {
            $sql.= " and ";
        }
        $sql.= "find_in_set('" . $usergroup . "',g.parent) ";
        $sql.= hook("getuseradditionalsql");
    }
# Executes query.
    $r = sql_query("select u.*,g.name groupname,g.ref groupref,g.parent groupparent,u.approved,u.created from user u left outer join usergroup g on u.usergroup=g.ref $sql order by $order_by",
            false, $fetchrows);

# Translates group names in the newly created array.
    for ($n = 0; $n < count($r); $n++) {
        if (!is_array($r[$n])) {
            break;
        } # The padded rows can't be and don't need to be translated.
        $r[$n]["groupname"] = lang_or_i18n_get_translated($r[$n]["groupname"],
                "usergroup-");
    }

    return $r;
}

function get_users_with_permission($permission)
{
# Returns all the users who have the permission $permission.
# The standard user group names are translated using $lang. Custom user group names are i18n translated.
# First find all matching groups.
    $groups = sql_query("select ref,permissions from usergroup");
    $matched = array();
    for ($n = 0; $n < count($groups); $n++) {
        $perms = trim_array(explode(",", $groups[$n]["permissions"]));
        if (in_array($permission, $perms)) {
            $matched[] = $groups[$n]["ref"];
        }
    }
# Executes query.
    $r = sql_query("select u.*,g.name groupname,g.ref groupref,g.parent groupparent from user u left outer join usergroup g on u.usergroup=g.ref where g.ref in ('" . join("','",
                    $matched) . "') order by username", false);

# Translates group names in the newly created array.
    $return = array();
    for ($n = 0; $n < count($r); $n++) {
        $r[$n]["groupname"] = lang_or_i18n_get_translated($r[$n]["groupname"],
                "usergroup-");
        $return[] = $r[$n]; # Adds to return array.
    }

    return $return;
}

function get_usergroups($usepermissions = false, $find = "")
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


function email_user_welcome($email, $username, $password, $usergroup)
{
    global $applicationname, $email_from, $baseurl, $lang, $email_url_save_user;

# Fetch any welcome message for this user group
    $welcome = sql_value("select welcome_message value from usergroup where ref='" . $usergroup . "'",
            "");
    if (trim($welcome) != "") {
        $welcome.="\n\n";
    }

    $templatevars['welcome'] = $welcome;
    $templatevars['username'] = $username;
    $templatevars['password'] = $password;
    if (trim($email_url_save_user) != "") {
        $templatevars['url'] = $email_url_save_user;
    } else {
        $templatevars['url'] = $baseurl;
    }

    $message = $templatevars['welcome'] . $lang["newlogindetails"] . "\n\n" . $lang["username"] . ": " . $templatevars['username'] . "\n" . $lang["password"] . ": " . $templatevars['password'] . "\n\n" . $templatevars['url'];

    send_mail($email, $applicationname . ": " . $lang["youraccountdetails"],
            $message, "", "", "emaillogindetails", $templatevars);
}

function email_reminder($email)
{
# Send a password reminder.
    global $password_brute_force_delay;
    if ($email == "") {
        return false;
    }
    $details = sql_query("select username from user where email like '$email' and approved=1");
    if (count($details) == 0) {
        sleep($password_brute_force_delay);
        return false;
    }
    $details = $details[0];
    global $applicationname, $email_from, $baseurl, $lang, $email_url_remind_user;
    $password = make_password();
    $password_hash = md5("RS" . $details["username"] . $password);

    sql_query("update user set password='$password_hash' where username='" . escape_check($details["username"]) . "'");

    $templatevars['username'] = $details["username"];
    $templatevars['password'] = $password;
    if (trim($email_url_remind_user) != "") {
        $templatevars['url'] = $email_url_remind_user;
    } else {
        $templatevars['url'] = $baseurl;
    }


    $message = $lang["newlogindetails"] . "\n\n" . $lang["username"] . ": " . $templatevars['username'] . "\n" . $lang["password"] . ": " . $templatevars['password'] . "\n\n" . $templatevars['url'];
    send_mail($email, $applicationname . ": " . $lang["newpassword"], $message,
            "", "", "emailreminder", $templatevars);
    return true;
}

function new_user($newuser)
{
# Username already exists?
    $c = sql_value("select count(*) value from user where username='$newuser'",
            0);
    if ($c > 0) {
        return false;
    }

# Create a new user with username $newuser. Returns the created user reference.
    sql_query("insert into user(username) values ('" . escape_check($newuser) . "')");

    $newref = sql_insert_id();

# Create a collection for this user, the collection name is translated when displayed!
    global $lang;
    $new = create_collection($newref, "My Collection", 0, 1); # Do not translate this string!
# set this to be the user's current collection
    sql_query("update user set current_collection='$new' where ref='$newref'");

    return $newref;
}


function change_password($password)
{
# Sets a new password for the current user.
    global $userref, $username, $lang, $userpassword;

# Check password
    $message = check_password($password);
    if ($message !== true) {
        return $message;
    }

# Generate new password hash
    $password_hash = md5("RS" . $username . $password);

# Check password is not the same as the current
    if ($userpassword == $password_hash) {
        return $lang["password_matches_existing"];
    }

    sql_query("update user set password='$password_hash',password_last_change=now() where ref='$userref' limit 1");
    return true;
}

function make_password()
{
# Generate a password using the configured settings.

    global $password_min_length, $password_min_alpha, $password_min_uppercase, $password_min_numeric, $password_min_special;

    $lowercase = "abcdefghijklmnopqrstuvwxyz";
    $uppercase = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $alpha = $uppercase . $lowercase;
    $numeric = "0123456789";
    $special = "!@$%^&*().?";

    $password = "";

# Add alphanumerics
    for ($n = 0; $n < $password_min_alpha; $n++) {
        $password.=substr($alpha, rand(0, strlen($alpha) - 1), 1);
    }

# Add upper case
    for ($n = 0; $n < $password_min_uppercase; $n++) {
        $password.=substr($uppercase, rand(0, strlen($uppercase) - 1), 1);
    }

# Add numerics
    for ($n = 0; $n < $password_min_numeric; $n++) {
        $password.=substr($numeric, rand(0, strlen($numeric) - 1), 1);
    }

# Add special
    for ($n = 0; $n < $password_min_special; $n++) {
        $password.=substr($special, rand(0, strlen($special) - 1), 1);
    }

# Pad with lower case
    $padchars = $password_min_length - strlen($password);
    for ($n = 0; $n < $padchars; $n++) {
        $password.=substr($lowercase, rand(0, strlen($lowercase) - 1), 1);
    }

# Shuffle the password.
    $password = str_shuffle($password);

# Check the password
    $check = check_password($password);
    if ($check !== true) {
        exit("Error: unable to automatically produce a password that met the criteria. Please check the password criteria in config.php. Generated password was '$password'. Error was: " . $check);
    }

    return $password;
}

function get_user_log($user, $fetchrows = -1)
{
# Returns a user action log for $user.
# Standard field titles are translated using $lang.  Custom field titles are i18n translated.
    global $view_title_field;
# Executes query.
    $r = sql_query("select r.ref resourceid,r.field" . $view_title_field . " resourcetitle,l.date,l.type,f.title,l.purchase_size,l.purchase_price, l.notes from resource_log l left outer join resource r on l.resource=r.ref left outer join resource_type_field f on f.ref=l.resource_type_field where l.user='$user' order by l.date desc",
            false, $fetchrows);

# Translates field titles in the newly created array.
    $return = array();
    for ($n = 0; $n < count($r); $n++) {
        if (is_array($r[$n])) {
            $r[$n]["title"] = lang_or_i18n_get_translated($r[$n]["title"],
                    "fieldtitle-");
        }
        $return[] = $r[$n];
    }
    return $return;
}

function resolve_userlist_groups($userlist)
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

function check_password($password)
{
# Checks that a password conforms to the configured paramaters.
# Returns true if it does, or a descriptive string if it doesn't.
    global $lang, $password_min_length, $password_min_alpha, $password_min_uppercase, $password_min_numeric, $password_min_special;

    if (strlen($password) < $password_min_length) {
        return str_replace("?", $password_min_length,
                $lang["password_not_min_length"]);
    }

    $uppercase = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $alpha = $uppercase . "abcdefghijklmnopqrstuvwxyz";
    $numeric = "0123456789";

    $a = 0;
    $u = 0;
    $n = 0;
    $s = 0;
    for ($m = 0; $m < strlen($password); $m++) {
        $l = substr($password, $m, 1);
        if (strpos($uppercase, $l) !== false) {
            $u++;
        }

        if (strpos($alpha, $l) !== false) {
            $a++;
        } elseif (strpos($numeric, $l) !== false) {
            $n++;
        } else {
            $s++;
        } # Not alpha/numeric, must be a special char.
    }

    if ($a < $password_min_alpha) {
        return str_replace("?", $password_min_alpha,
                $lang["password_not_min_alpha"]);
    }
    if ($u < $password_min_uppercase) {
        return str_replace("?", $password_min_uppercase,
                $lang["password_not_min_uppercase"]);
    }
    if ($n < $password_min_numeric) {
        return str_replace("?", $password_min_numeric,
                $lang["password_not_min_numeric"]);
    }
    if ($s < $password_min_special) {
        return str_replace("?", $password_min_special,
                $lang["password_not_min_special"]);
    }


    return true;
}

function resolve_users($users)
{
# For a given comma-separated list of user refs (e.g. returned from a group_concat()), return a string of matching usernames.
    if (trim($users) == "") {
        return "";
    }
    $resolved = sql_array("select concat(fullname,' (',username,')') value from user where ref in ($users)");
    return join(", ", $resolved);
}

function send_statistics()
{
# Sorry Montala.
    return false;
}

function check_access_key($resource, $key)
{
# Verify a supplied external access key
# Option to plugin in some extra functionality to check keys
    if (hook("check_access_key", "", array($resource, $key)) === true) {
        return true;
    }

    $keys = sql_query("select user,expires from external_access_keys where resource='$resource' and access_key='$key' and (expires is null or expires>now())");

    if (count($keys) == 0) {
        return false;
    } else {
# "Emulate" the user that e-mailed the resource by setting the same group and permissions

        $user = $keys[0]["user"];
        $expires = $keys[0]["expires"];

# Has this expired?
        if ($expires != "" && strtotime($expires) < time()) {
            global $lang;
            ?>
            <script type="text/javascript">
                alert("<?php echo $lang["externalshareexpired"] ?>");
                history.go(-1);
            </script>
            <?php
            exit();
        }

        global $usergroup, $userpermissions, $userrequestmode, $userfixedtheme;
        $userinfo = sql_query("select u.usergroup,g.permissions,g.fixed_theme from user u join usergroup g on u.usergroup=g.ref where u.ref='$user'");
        if (count($userinfo) > 0) {
            $usergroup = $userinfo[0]["usergroup"];
            $userpermissions = explode(",", $userinfo[0]["permissions"]);
            if (trim($userinfo[0]["fixed_theme"]) != "") {
                $userfixedtheme = $userinfo[0]["fixed_theme"];
            } # Apply fixed theme also

            if (hook("modifyuserpermissions")) {
                $userpermissions = hook("modifyuserpermissions");
            }
            $userrequestmode = 0; # Always use 'email' request mode for external users
        }

# Special case for anonymous logins.
# When a valid key is present, we need to log the user in as the anonymous user so they will be able to browse the public links.
        global $anonymous_login;
        if (isset($anonymous_login)) {
            global $username;
            $username = $anonymous_login;
        }

# Set the 'last used' date for this key
        sql_query("update external_access_keys set lastused=now() where resource='$resource' and access_key='$key'");

        return true;
    }
}

function check_access_key_collection($collection, $key)
{
    if ($collection == "" || !is_numeric($collection)) {
        return false;
    }
    $r = get_collection_resources($collection);
    if (count($r) == 0) {
        return false;
    }

    for ($n = 0; $n < count($r); $n++) {
# Verify a supplied external access key for all resources in a collection
        if (!check_access_key($r[$n], $key)) {
            return false;
        }
    }

# Set the 'last used' date for this key
    sql_query("update external_access_keys set lastused=now() where collection='$collection' and access_key='$key'");
    return true;
}

if (!function_exists("auto_create_user_account")) {

    function auto_create_user_account()
    {
# Automatically creates a user account (which requires approval unless $auto_approve_accounts is true).
        global $applicationname, $user_email, $email_from, $baseurl, $email_notify, $lang, $custom_registration_fields, $custom_registration_required, $user_account_auto_creation_usergroup, $registration_group_select, $auto_approve_accounts, $auto_approve_domains;

# Add custom fields
        $c = "";
        if (isset($custom_registration_fields)) {
            $custom = explode(",", $custom_registration_fields);

# Required fields?
            if (isset($custom_registration_required)) {
                $required = explode(",", $custom_registration_required);
            }

            for ($n = 0; $n < count($custom); $n++) {
                if (isset($required) && in_array($custom[$n], $required) && getval("custom" . $n,
                                "") == "") {
                    return false; # Required field was not set.
                }

                $c.=i18n_get_translated($custom[$n]) . ": " . getval("custom" . $n,
                                "") . "\n\n";
            }
        }

# Required fields (name, email) not set?
        if (getval("name", "") == "") {
            return $lang['requiredfields'];
        }
        if (getval("email", "") == "") {
            return $lang['requiredfields'];
        }

# Work out which user group to set. Allow a hook to change this, if necessary.
        $altgroup = hook("auto_approve_account_switch_group");
        if ($altgroup !== false) {
            $usergroup = $altgroup;
        } else {
            $usergroup = $user_account_auto_creation_usergroup;
        }

        if ($registration_group_select) {
            $usergroup = getvalescaped("usergroup", "", true);
# Check this is a valid selectable usergroup (should always be valid unless this is a hack attempt)
            if (sql_value("select allow_registration_selection value from usergroup where ref='$usergroup'",
                            0) != 1) {
                exit("Invalid user group selection");
            }
        }

        $username = escape_check(make_username(getval("name", "")));

#check if account already exists
        $check = sql_value("select email value from user where email = '$user_email'",
                "");
        if ($check != "") {
            return $lang["useremailalreadyexists"];
        }

# Prepare to create the user.
        $email = trim(getvalescaped("email", ""));
        $password = make_password();

# Work out if we should automatically approve this account based on $auto_approve_accounts or $auto_approve_domains
        $approve = false;
        if ($auto_approve_accounts == true) {
            $approve = true;
        } elseif (count($auto_approve_domains) > 0) {
# Check e-mail domain.
            foreach ($auto_approve_domains as $domain => $set_usergroup) {
// If a group is not specified the variables don't get set correctly so we need to correct this
                if (is_numeric($domain)) {
                    $domain = $set_usergroup;
                    $set_usergroup = "";
                }
                if (substr(strtolower($email),
                                strlen($email) - strlen($domain) - 1) == ("@" . strtolower($domain))) {
# E-mail domain match.
                    $approve = true;

# If user group is supplied, set this
                    if (is_numeric($set_usergroup)) {
                        $usergroup = $set_usergroup;
                    }
                }
            }
        }


# Create the user
        sql_query("insert into user (username,password,fullname,email,usergroup,comments,approved) values ('" . $username . "','" . $password . "','" . getvalescaped("name",
                        "") . "','" . $email . "','" . $usergroup . "','" . escape_check($c) . "'," . (($approve) ? 1 : 0) . ")");
        $new = sql_insert_id();
        hook("afteruserautocreated", "all", array("new" => $new));
        if ($approve) {
# Auto approving, send mail direct to user
            email_user_welcome($email, $username, $password, $usergroup);
        } else {
# Not auto approving.
# Build a message to send to an admin notifying of unapproved user
            $message = $lang["userrequestnotification1"] . "\n\n" . $lang["name"] . ": " . getval("name",
                            "") . "\n\n" . $lang["email"] . ": " . getval("email",
                            "") . "\n\n" . $lang["comment"] . ": " . getval("userrequestcomment",
                            "") . "\n\n" . $lang["ipaddress"] . ": '" . $_SERVER["REMOTE_ADDR"] . "'\n\n" . $c . "\n\n" . $lang["userrequestnotification3"] . "\n$baseurl?u=" . $new;


            send_mail($email_notify,
                    $applicationname . ": "
                        . $lang["requestuserlogin"] . " - "
                        . getval("name",""),
                    $message, "", "", 
                    $user_email, "",
                    getval("name", ""));
        }

        return true;
    }

} //end function replace hook

function make_username($name)
{
# Generates a unique username for the given name
# First compress the various name parts
    $s = trim_array(explode(" ", $name));

    $name = $s[count($s) - 1];
    for ($n = count($s) - 2; $n >= 0; $n--) {
        $name = substr($s[$n], 0, 1) . $name;
    }
    $name = safe_file_name(strtolower($name));

# Check for uniqueness... append an ever-increasing number until unique.
    $unique = false;
    $num = -1;
    while (!$unique) {
        $num++;
        $c = sql_value("select count(*) value from user where username='" . escape_check($name . (($num == 0) ? "" : $num)) . "'",
                0);
        $unique = ($c == 0);
    }
    return $name . (($num == 0) ? "" : $num);
}

function get_registration_selectable_usergroups()
{
# Returns a list of  user groups selectable in the registration . The standard user groups are translated using $lang. Custom user groups are i18n translated.
# Executes query.
    $r = sql_query("select ref,name from usergroup where allow_registration_selection=1 order by name");

# Translates group names in the newly created array.
    $return = array();
    for ($n = 0; $n < count($r); $n++) {
        $r[$n]["name"] = lang_or_i18n_get_translated($r[$n]["name"],
                "usergroup-");
        $return[] = $r[$n]; # Adds to return array.
    }

    return $return;
}

function open_access_to_user($user, $resource, $expires)
{
# Give the user full access to the given resource.
# Used when approving requests.
# Delete any existing custom access
    sql_query("delete from resource_custom_access where user='$user' and resource='$resource'");

# Insert new row
    sql_query("insert into resource_custom_access(resource,access,user,user_expires) values ('$resource','0','$user'," . ($expires == "" ? "null" : "'$expires'") . ")");

    return true;
}

function remove_access_to_user($user, $resource)
{
# Remove any user-specific access granted by an 'approve'.
# Used when declining requests.
# Delete any existing custom access
    sql_query("delete from resource_custom_access where user='$user' and resource='$resource'");

    return true;
}


function resolve_user_emails($ulist)
{
    global $lang;
// return an array of emails from a list of usernames and email addresses.
// with 'key_required' sibling array preserving the intent of internal/external sharing.
    $emails_key_required = array();
    for ($n = 0; $n < count($ulist); $n++) {
        $uname = $ulist[$n];
        $email = sql_value("select email value from user where username='" . escape_check($uname) . "'",
                '');
        if ($email == '') {
# Not a recognised user, if @ sign present, assume e-mail address specified
            if (strpos($uname, "@") === false) {
                error_alert($lang["couldnotmatchallusernames"] . ": " . escape_check($uname));
                die();
            }
            $emails_key_required['unames'][$n] = $uname;
            $emails_key_required['emails'][$n] = $uname;
            $emails_key_required['key_required'][$n] = true;
        } else {
# Add e-mail address from user account
            $emails_key_required['unames'][$n] = $uname;
            $emails_key_required['emails'][$n] = $email;
            $emails_key_required['key_required'][$n] = false;
        }
    }
    return $emails_key_required;
}
