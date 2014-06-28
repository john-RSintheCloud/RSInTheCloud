<?php

/**
 * files Wrapper
 * A wrapper round the file and folder handling functions,
 * to allow a future move to OO design.
 *
 * @author John Brookes <john@RSintheCloud.com>
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

        // truncate_cache_arrays
        //  to prevent cache arrays from going rogue
        // this will prevent long-running scripts from dying as these
        // caches exhaust available memory.
        if (count($GLOBALS['get_resource_path_fpcache']) > 2000) {
            $GLOBALS['get_resource_path_fpcache'] = array();
        }

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
 * Determines where the debug log will live.
 *
 * @return string - The path to the debug_log directory.
 */
function get_debug_log_dir() {

    return get_temp_dir(false, "logs");
}

/**
 * Determines where the tmp directory is.  There is only 1 option! :
 * 1. tempdir - If set in config.php, use this value.
 * if a uniqid is provided, create a folder within tmp and return the full path
 *
 * @param bool $asUrl - If we want the return to be a url like http://my.resourcespace.install/path set this as true.
 * @param string $uniqid subfolder (no /)
 *
 * @return string Path to the tmp directory.
 */
function get_temp_dir($asUrl = false, $uniqid = "") {
    global $tempUrl, $tempdir;

    if ($uniqid != "") {
        //restrict to forward-only movements
        $uniqid = str_replace("../", "", $uniqid);
        //  Add leading /
        $uniqid = '/' . trim($uniqid, ' /');
    }

    // if $tempdir and tempUrl not set, throw exception.
    if (! $asUrl ) {
        if ( empty($tempdir)) {
            throw new Exception('No tempdir defined in config');
        }
        // Make sure the dir exists.
        if (!is_dir($tempdir)) {
            // If it does not exist, create it.
            mkdir($tempdir, 0777);
        }
        $result = $tempdir . $uniqid;
        if (!is_dir($result)) {
            // If it does not exist, create it.
            mkdir($result, 0777, true);
        }

    } else if (empty($tempUrl)) {
        throw new Exception('No tempUrl defined in config');
    } else {
         $result = $tempUrl . $uniqid ;
    }

    return $result;
}


function get_image_sizes($ref, $internal = false, $extension = "jpg", $onlyifexists = true) {
    # Returns a table of available image sizes for resource $ref. The standard image sizes are translated using $lang. Custom image sizes are i18n translated.
    # The original image file assumes the name of the 'nearest size (up)' in the table

    # Work out resource type
    $resource_type = sql_value("select resource_type value from resource where ref='$ref'", "");

    # add the original image
    $return = array();
    $lastname = sql_value("select name value from preview_size where width=(select max(width) from preview_size)", ""); # Start with the highest resolution.
    $lastpreview = 0;
    $lastrestricted = 0;
    $path2 = get_resource_path($ref, true, '', false, $extension);

    if (file_exists($path2) && !checkperm("T" . $resource_type . "_")) {
        $returnline = array();
        $returnline["name"] = lang_or_i18n_get_translated($lastname, "imagesize-");
        $returnline["allow_preview"] = $lastpreview;
        $returnline["allow_restricted"] = $lastrestricted;
        $returnline["path"] = $path2;
        $returnline["id"] = "";
        $dimensions = sql_query("select width,height,file_size,resolution,unit from resource_dimensions where resource=" . $ref);

        if (count($dimensions)) {
            $sw = $dimensions[0]['width'];
            if ($sw == 0) {
                $sw = "?";
            }
            $sh = $dimensions[0]['height'];
            if ($sh == 0) {
                $sh = "?";
            }
            $filesize = $dimensions[0]['file_size'];
            # resolution and unit are not necessarily available, set to empty string if so.
            $resolution = ($dimensions[0]['resolution']) ? $dimensions[0]['resolution'] : "";
            $unit = ($dimensions[0]['unit']) ? $dimensions[0]['unit'] : "";
        } else {
            $fileinfo = get_original_imagesize($ref, $path2, $extension);
            $filesize = $fileinfo[0];
            $sw = $fileinfo[1];
            $sh = $fileinfo[2];
        }
        if (!is_numeric($filesize)) {
            $returnline["filesize"] = "?";
            $returnline["filedown"] = "?";
        } else {
            $returnline["filedown"] = ceil($filesize / 50000) . " seconds @ broadband";
            $returnline["filesize"] = formatfilesize($filesize);
        }
        $returnline["width"] = $sw;
        $returnline["height"] = $sh;
        $returnline["extension"] = $extension;
        (isset($resolution)) ? $returnline["resolution"] = $resolution : $returnline["resolution"] = "";
        (isset($unit)) ? $returnline["unit"] = $unit : $returnline["unit"] = "";
        $return[] = $returnline;
    }
    # loop through all image sizes
    $sizes = sql_query("select * from preview_size order by width desc");
    for ($n = 0; $n < count($sizes); $n++) {
        $path = get_resource_path($ref, true, $sizes[$n]["id"], false, "jpg");

        $resource_type = sql_value("select resource_type value from resource where ref='$ref'", "");
        $file_exists = file_exists($path);
        if (($file_exists || (!$onlyifexists)) && !checkperm("T" . $resource_type . "_" . $sizes[$n]["id"])) {
            if (($sizes[$n]["internal"] == 0) || ($internal)) {
                $returnline = array();
                $returnline["name"] = lang_or_i18n_get_translated($sizes[$n]["name"], "imagesize-");
                $returnline["allow_preview"] = $sizes[$n]["allow_preview"];

                # The ability to restrict download size by user group and resource type.
                if (checkperm("X" . $resource_type . "_" . $sizes[$n]["id"])) {
                    # Permission set. Always restrict this download if this resource is restricted.
                    $returnline["allow_restricted"] = false;
                } else {
                    # Take the restriction from the settings for this download size.
                    $returnline["allow_restricted"] = $sizes[$n]["allow_restricted"];
                }
                $returnline["path"] = $path;
                $returnline["id"] = $sizes[$n]["id"];
                if ((list($sw, $sh) = @getimagesize($path)) === false) {
                    $sw = 0;
                    $sh = 0;
                }
                if ($file_exists)
                    $filesize = @filesize_unlimited($path);
                else
                    $filesize = 0;
                if ($filesize === false) {
                    $returnline["filesize"] = "?";
                    $returnline["filedown"] = "?";
                } else {
                    $returnline["filedown"] = ceil($filesize / 50000) . " seconds @ broadband";
                    $filesize = formatfilesize($filesize);
                }
                $returnline["filesize"] = $filesize;
                $returnline["width"] = $sw;
                $returnline["height"] = $sh;
                $returnline["extension"] = 'jpg';
                $return[] = $returnline;
            }
        }
        $lastname = lang_or_i18n_get_translated($sizes[$n]["name"], "imagesize-");
        $lastpreview = $sizes[$n]["allow_preview"];
        $lastrestricted = $sizes[$n]["allow_restricted"];
    }
    return $return;
}

function remove_extension($strName)
{
    $ext = strrchr($strName, '.');
    if ($ext !== false) {
        $strName = substr($strName, 0, -strlen($ext));
    }
    return $strName;
}


function verify_extension($filename, $allowed_extensions = "")
{
    # Allowed extension?
    $extension = explode(".", $filename);
    if (count($extension) > 1) {
        $extension = trim(strtolower($extension[count($extension) - 1]));
    } else {
        return false;
    }

    if ($allowed_extensions != "") {
        $allowed_extensions = explode(",", strtolower($allowed_extensions));
        if (!in_array($extension, $allowed_extensions)) {
            return false;
        }
    }


    return true;
}

function get_allowed_extensions($ref)
{
    $type = sql_value("select resource_type value from resource where ref=$ref",
            "");
    $allowed_extensions = sql_value("select allowed_extensions value from resource_type where ref=$type",
            "");
    return $allowed_extensions;
}

function get_allowed_extensions_by_type($resource_type)
{
    $allowed_extensions = sql_value("select allowed_extensions value from resource_type where ref='$resource_type'",
            "");
    return $allowed_extensions;
}

/**
 * Detect if a path is relative or absolute.
 * If it is relative, we compute its absolute location by assuming it is
 * relative to the application root (parent folder).
 *
 * @param string $path A relative or absolute path
 * @param boolean $create_if_not_exists Try to create the path if it does not exists. Default to False.
 * @access public
 * @return string A absolute path
 */
function getAbsolutePath($path, $create_if_not_exists = false)
{
    if (preg_match('/^(\/|[a-zA-Z]:[\\/]{1})/', $path)) { // If the path start by a '/' or 'c:\', it is an absolute path.
        $folder = $path;
    } else { // It is a relative path.
        $folder = sprintf('%s%s..%s%s', dirname(__FILE__), DIRECTORY_SEPARATOR,
                DIRECTORY_SEPARATOR, $path);
    }

    if ($create_if_not_exists && !file_exists($folder)) { // Test if the path need to be created.
        mkdir($folder, 0777);
    } // Test if the path need to be created.

    return $folder;
}

// getAbsolutePath()

/**
 * Find the files present in a folder, and sub-folder.
 *
 * @param string $path The path to look into.
 * @param boolean $recurse Trigger the recursion, default to True.
 * @param boolean $include_hidden Trigger the listing of hidden files / hidden directories, default to False.
 * @access public
 * @return array A list of files present in the inspected folder (paths are relative to the inspected folder path).
 */
function getFolderContents($path, $recurse = true, $include_hidden = false)
{
    if (!is_dir($path)) { // Test if the path is not a folder.
        return array();
    } // Test if the path is not a folder.

    $directory_handle = opendir($path);
    if ($directory_handle === false) { // Test if the directory listing failed.
        return array();
    } // Test if the directory listing failed.

    $files = array();
    while (($file = readdir($directory_handle)) !== false) { // For each directory listing entry.
        if (!in_array($file, array('.', '..'))) { // Test if file is not unix parent and current path.
            if ($include_hidden || !preg_match('/^\./', $file)) { // Test if the file can be listed.
                $complete_path = $path . DIRECTORY_SEPARATOR . $file;
                if (is_dir($complete_path) && $recurse) { // If the path is a directory, and need to be explored.
                    $sub_dir_files = getFolderContents($complete_path, $recurse,
                            $include_hidden);
                    foreach ($sub_dir_files as $sub_dir_file) { // For each subdirectory contents.
                        $files[] = $file . DIRECTORY_SEPARATOR . $sub_dir_file;
                    } // For each subdirectory contents.
                } elseif (is_file($complete_path)) { // If the path is a file.
                    $files[] = $file;
                }
            } // Test if the file can be listed.
        } // Test if file is not unix parent and current path.
    } // For each directory listing entry.
    // We close the directory handle:
    closedir($directory_handle);

    // We sort the files alphabetically.
    natsort($files);

    return $files;
}

// getPathFiles()

/**
 * Returns filename component of path
 * This version is UTF-8 proof.
 * Thanks to nasretdinov at gmail dot com
 * @link http://www.php.net/manual/en/function.basename.php#85369
 *
 * @param string $file A path.
 * @access public
 * @return string Returns the base name of the given path.
 */
function mb_basename($file)
{
    $exploded_path = preg_split('/[\\/]+/', $file);
    return end($exploded_path);
}

// mb_basename()

/**
 * Remove the extension part of a filename.
 * Thanks to phparadise
 * http://fundisom.com/phparadise/php/file_handling/strip_file_extension
 *
 * @param string $name A file name.
 * @access public
 * @return string Return the file name without the extension part.
 */
function strip_extension($name)
{
    $ext = strrchr($name, '.');
    if ($ext !== false) {
        $name = substr($name, 0, -strlen($ext));
    }
    return $name;
}

// strip_extension()

function get_nopreview_icon($resource_type, $extension, $col_size,
        $deprecated1 = false, $deprecated2 = false)
{
    # Returns the path (relative to the gfx folder) of a suitable folder to represent
    # a resource with the given resource type or extension
    # Extension matches are tried first, followed by resource type matches
    # Finally, if there are no matches then the 'type1' image will be used.
    # set contactsheet to true to cd up one more level.

    global $language;

    $col = ($col_size ? "_col" : "");
    $folder = dirname(dirname(__FILE__)) . "/gfx/";

    # Metadata template? Always use icon for 'mdtr', although typically no file will be attached.
    global $metadata_template_resource_type;
    if (isset($metadata_template_resource_type) && $metadata_template_resource_type == $resource_type) {
        $extension = "mdtr";
    }


    # Try extension (language specific)
    $try = "no_preview/extension/" . $extension . $col . "_" . $language . ".png";
    if (file_exists($folder . $try)) {
        return $try;
    }
    # Try extension (default)
    $try = "no_preview/extension/" . $extension . $col . ".png";
    if (file_exists($folder . $try)) {
        return $try;
    }

    # --- Legacy ---
    # Support the old location for resource type and GIF format (root of gfx folder)
    # Some installations use custom types in this location.
    $try = "type" . $resource_type . $col . ".gif";
    if (file_exists($folder . $try)) {
        return $try;
    }


    # Try resource type (language specific)
    $try = "no_preview/resource_type/type" . $resource_type . $col . "_" . $language . ".png";
    if (file_exists($folder . $try)) {
        return $try;
    }
    # Try resource type (default)
    $try = "no_preview/resource_type/type" . $resource_type . $col . ".png";
    if (file_exists($folder . $try)) {
        return $try;
    }

    # Fall back to the 'no preview' icon used for type 1.
    return "no_preview/resource_type/type1" . $col . ".png";
}

function filesize_unlimited($path)
{
    # A resolution for PHP's issue with large files and filesize().

    #Use stat
    $bytesize = exec("stat -c '%s' " . escapeshellarg($path));
    if (!is_int($bytesize)) {
        return @filesize($path); # Bomb out, the output wasn't as we expected. Return the filesize() output.
    }
    return $bytesize;
}

function get_mime_type($path, $ext = null)
{
    global $mime_type_by_extension;
    if (empty($ext))
        $ext = pathinfo($path, PATHINFO_EXTENSION);
    if (isset($mime_type_by_extension[$ext])) {
        return $mime_type_by_extension[$ext];
    }

    # Get mime type via exiftool if possible
    $exiftool_fullpath = get_utility_path("exiftool");
    if ($exiftool_fullpath != false) {
        $command = $exiftool_fullpath . " -s -s -s -t -mimetype " . escapeshellarg($path);
        return run_command($command);
    }

    return "application/octet-stream";
}
