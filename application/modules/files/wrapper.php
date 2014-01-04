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


$GLOBALS['get_resource_path_fpcache'] = array();

function get_resource_path($ref, $getfilepath, $size, $generate = true, $extension = "jpg", $scramble = -1, $page = 1, $watermarked = false, $file_modified = "", $alternative = -1, $includemodified = true) {
    # returns the correct path to resource $ref of size $size ($size==empty string is original resource)
    # If one or more of the folders do not exist, and $generate=true, then they are generated
    if (!preg_match('/^[a-zA-Z0-9]+$/', $extension)) {
        $extension = "jpg";
    }
//	if(preg_match('/\w/', $extension)){$extension="jpg";}

    $override = hook("get_resource_path_override", "general", array($ref, $getfilepath, $size, $generate, $extension, $scramble, $page, $watermarked, $file_modified, $alternative, $includemodified));
    if (is_string($override)) {
        return $override;
    }

    global $storagedir;

    if ($size == "") {
        # For the full size, check to see if the full path is set and if so return that.
        global $get_resource_path_fpcache;
        truncate_cache_arrays();

        if (!isset($get_resource_path_fpcache[$ref])) {
            $get_resource_path_fpcache[$ref] = sql_value("select file_path value from resource where ref='$ref'", "");
        }
        $fp = $get_resource_path_fpcache[$ref];

        # Test to see if this nosize file is of the extension asked for, else skip the file_path and return a $storagedir path.
        # If using staticsync, file path will be set already, but we still want the $storagedir path for a nosize preview jpg.
        # Also, returning the original filename when a nosize 'jpg' is looked for is no good, since preview_preprocessing.php deletes $target.

        $test_ext = explode(".", $fp);
        $test_ext = trim(strtolower($test_ext[count($test_ext) - 1]));

        if (($test_ext == $extension) && (strlen($fp) > 0) && (strpos($fp, "/") !== false) && !($alternative > 0)) {

            if ($getfilepath) {
                global $syncdir;
                $syncdirmodified = hook("modifysyncdir", "all", array($ref));
                if ($syncdirmodified != "") {
                    return $syncdirmodified;
                }
                return $syncdir . "/" . $fp;
            } else {
                global $baseurl_short, $k;
                return $baseurl_short . "pages/download.php?ref={$ref}&size={$size}&ext={$extension}&noattach=true&k={$k}&page={$page}";
            }
        }
    }

    global $scramble_key;
    if ($scramble === -1) {
        # Find the system default scramble setting if not specified
        if (isset($scramble_key) && ($scramble_key != "")) {
            $scramble = true;
        } else {
            $scramble = false;
        }
    }

    if ($scramble) {
        # Create a scrambled path using the scramble key
        # It should be very difficult or impossible to work out the scramble key, and therefore access
        # other resources, based on the scrambled path of a single resource.
        $scramblepath = substr(md5($ref . "_" . $scramble_key), 0, 15);
    }

    if ($extension == "") {
        $extension = "jpg";
    }

    $folder = "";
    #if (!file_exists(dirname(__FILE__) . $folder)) {mkdir(dirname(__FILE__) . $folder,0777);}

    for ($n = 0; $n < strlen($ref); $n++) {
        $folder.=substr($ref, $n, 1);
        if (($scramble) && ($n == (strlen($ref) - 1))) {
            $folder.="_" . $scramblepath;
        }
        $folder.="/";
        #echo "<li>" . $folder;
        if ((!(file_exists($storagedir . "/" . $folder))) && $generate) {
            mkdir($storagedir . "/" . $folder, 0777);
            chmod($storagedir . "/" . $folder, 0777);
        }
    }

    # Add the page to the filename for everything except page 1.
    if ($page == 1) {
        $p = "";
    } else {
        $p = "_" . $page;
    }

    # Add the alternative file ID to the filename if provided
    if ($alternative > 0) {
        $a = "_alt_" . $alternative;
    } else {
        $a = "";
    }

    # Add the watermarked url too
    if ($watermarked) {
        $p.="_wm";
    }

    # Fetching the file path? Add the full path to the file
    $filefolder = $storagedir . "/" . $folder;
    if ($getfilepath) {
        $folder = $filefolder;
    } else {
        global $storageurl;
        $folder = $storageurl . "/" . $folder;
    }

    if ($scramble) {
        $file_old = $filefolder . $ref . $size . $p . $a . "." . $extension;
        $file_new = $filefolder . $ref . $size . $p . $a . "_" . substr(md5($ref . $size . $p . $a . $scramble_key), 0, 15) . "." . $extension;
        $file = $folder . $ref . $size . $p . $a . "_" . substr(md5($ref . $size . $p . $a . $scramble_key), 0, 15) . "." . $extension;
        if (file_exists($file_old)) {
            rename($file_old, $file_new);
        }
    } else {
        $file = $folder . $ref . $size . $p . $a . "." . $extension;
    }

# Append modified date/time to the URL so the cached copy is not used if the file is changed.
    if (!$getfilepath && $includemodified) {
        if ($file_modified == "") {
            $data = get_resource_data($ref);
            $file .= "?v=" . urlencode($data['file_modified']);
        } else {
            # Use the provided value
            $file .= "?v=" . urlencode($file_modified);
        }
    }

    return $file;
}


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
