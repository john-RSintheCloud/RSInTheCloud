<?php
/**
 * Process Lock Wrapper
 * Used to ensure an expensive process does not run too often.
 *
 * @author John Brookes <john@RSintheCloud.com>
 * @package RSintheClouds
 * @subpackage Refactor
*/


function is_process_lock($name)
{
    # Checks to see if a process lock exists for the given process name.
    global $storagedir, $process_locks_max_seconds;

    # Check that tmp/process_locks exists, create if not.
    # Since the get_temp_dir() method does this checking, omit: if(!is_dir($storagedir . "/tmp")){mkdir($storagedir . "/tmp",0777);}
    if (!is_dir(get_temp_dir() . "/process_locks")) {
        mkdir(get_temp_dir() . "/process_locks", 0777);
    }

    # No lock file? return false
    if (!file_exists(get_temp_dir() . "/process_locks/" . $name)) {
        return false;
    }

    $time = trim(file_get_contents(get_temp_dir() . "/process_locks/" . $name));
    if ((time() - $time) > $process_locks_max_seconds) {
        return false;
    } # Lock has expired

    return true; # Lock is valid
}

function set_process_lock($name)
{
    # Set a process lock
    file_put_contents(get_temp_dir() . "/process_locks/" . $name, time());
    // make sure this is editable by the server in case a process lock could be set by different system users
    chmod(get_temp_dir() . "/process_locks/" . $name, 0777);
    return true;
}

function clear_process_lock($name)
{
    # Clear a process lock
    unlink(get_temp_dir() . "/process_locks/" . $name);
    return true;
}

