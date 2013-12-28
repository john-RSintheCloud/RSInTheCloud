<?php

/**
 * files Wrapper
 * A wrapper round the file and folder handling functions,
 * to allow a future move to OO design.
 *
 * @author John Brookes <john@RSintheClouds.com>
 * @package RSintheClouds
 * @subpackage Refactor
 */
function safe_file_name($name) {
    # Returns a file name stipped of all non alphanumeric values
    # Spaces are replaced with underscores
    $alphanum = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_-";
    $name = str_replace(" ", "_", $name);
    $newname = "";
    for ($n = 0; $n < strlen($name); $n++) {
        $c = substr($name, $n, 1);
        if (strpos($alphanum, $c) !== false) {
            $newname.=$c;
        }
    }
    $newname = substr($newname, 0, 30);
    return $newname;
}

/**
 * Recursively removes a directory.
 *
 * Recursively removes a directory.  Currently this is only used by plugins
 * This function does not check to see  if php can delete the specified
 * path.
 *
 * @todo ADD - Check that PHP has permissions to delete $path
 * @param string $path Directory path to remove.
 */
function rcRmdir($path) { # Recursive rmdir function.
    if (is_dir($path)) {
        $dirh = opendir($path);
        while (false !== ($file = readdir($dirh))) {
            if (is_dir($path . '/' . $file)) {
                if (!((strlen($file) == 1 && $file[0] == '.') || (substr($file, 0, 2) == '..'))) {
                    rcRmdir($path . '/' . $file);
                }
            } else {
                unlink($path . '/' . $file);
            }
        }
        closedir($dirh);
        rmdir($path);
    }
}

function debug($text) {
    # Output some text to a debug file.
    # For developers only
    global $debug_log, $debug_log_location;
    if (!$debug_log) {
        return true;
    } # Do not execute if switched off.
    # Cannot use the general.php: get_temp_dir() method here since general may not have been included.
    if (isset($debug_log_location)) {
        $debugdir = dirname($debug_log_location);
        if (!is_dir($debugdir)) {
            mkdir($debugdir, 0755, true);
        }
        $f = fopen($debug_log_location, "a");
    } else {
        $f = fopen(get_debug_log_dir() . "/debug.txt", "a");
    }
    fwrite($f, date("Y-m-d H:i:s") . " " . $text . "\n");
    fclose($f);
    return true;
}

/**
 * Determines where the debug log will live.  Typically, same as tmp dir (See general.php: get_temp_dir().
 * Since general.php may not be included, we cannot use that method so I have created this one too.
 * @return string - The path to the debug_log directory.
 */
function get_debug_log_dir() {
    // Set up the default.
    $result = dirname(dirname(__FILE__)) . "/filestore/tmp";

    // if $tempdir is explicity set, use it.
    if (isset($tempdir)) {
        // Make sure the dir exists.
        if (!is_dir($tempdir)) {
            // If it does not exist, create it.
            mkdir($tempdir, 0777);
        }
        $result = $tempdir;
    }
    // Otherwise, if $storagedir is set, use it.
    else if (isset($storagedir)) {
        // Make sure the dir exists.
        if (!is_dir($storagedir . "/tmp")) {
            // If it does not exist, create it.
            mkdir($storagedir . "/tmp", 0777);
        }
        $result = $storagedir . "/tmp";
    } else {
        // Make sure the dir exists.
        if (!is_dir($result)) {
            // If it does not exist, create it.
            mkdir($result, 0777);
        }
    }
    // return the result.
    return $result;
}
