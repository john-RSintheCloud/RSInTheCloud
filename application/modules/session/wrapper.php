<?php
/**
 * session Wrapper
 * RS uses lots of cookies to keep track of people.
 * This wrapper is for moving to a standard PHP session format
 *
 * @author John Brookes <john@RSintheClouds.com>
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

