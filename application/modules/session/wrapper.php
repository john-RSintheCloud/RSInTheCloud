<?php
/**
 * session Wrapper
 * RS uses lots of cookies to keep track of people.
 * This wrapper is for moving to a standard PHP session format
 *
 * @author John Brookes <john@RSintheCloud.com>
 * @package RSintheClouds
 * @subpackage Refactor
*/


function checkperm($perm) {
    # check that the user has the $perm permission
    global $userpermissions;
    if (!(isset($userpermissions))) {
        return false;
    }
    if (in_array($perm, $userpermissions)) {
        return true;
    } else {
        return false;
    }
}

function rs_setcookie($name, $value, $daysexpire = 0, $path = "", $domain = "",
        $secure = false, $httponly = false)
{
    # Note! The argument $daysexpire is not the same as the argument $expire in the PHP internal function setcookie.
    # Note! The $path argument is not used if $global_cookies = true

    global $baseurl_short, $global_cookies;
    if ($daysexpire == 0) {
        $expire = 0;
    } else {
        $expire = time() + (3600 * 24 * $daysexpire);
    }

    if ($global_cookies) {
        # Remove previously set cookies to avoid clashes
        setcookie($name, "", time() - 3600, $baseurl_short . "pages/");
        setcookie($name, "", time() - 3600, $baseurl_short);
        # Set new cookie
        setcookie($name, $value, $expire, "/");
    } else {
        # Set new cookie
        setcookie($name, $value, $expire, $path);
    }
}
