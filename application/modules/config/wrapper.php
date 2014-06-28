<?php

/**
 * config Wrapper
 * A wrapper round the config class,
 * to allow a future move to OO design.
 *
 * @author John Brookes <john@RSintheCloud.com>
 * @package RSintheClouds
 * @subpackage Refactor
 */


# *** LOAD CONFIG ***
# This is legacy - all new code should be using the config class from the DIC
# 
# Load the default config first
# so all settings are present 
# Throw an exception if not found
require_once APPLICATION_PATH . "../_config/config.default.php";
# Load the real config
//if (!file_exists(APPLICATION_PATH . "../_config/config.php")) {
////  DON'T TRY TO ENTER SETUP    header ("Location: pages/setup.php" );
//    die;
//}
require_once APPLICATION_PATH . "../_config/config.php";
require_once APPLICATION_PATH . "../_config/secure.config.php";

# Set the storage directory and URL if not already set.
#  This will not be needed once we get S3 upload working properly
if (!isset($storagedir)) {die('Storage dir not set in config');}


# Set time limit from config value
set_time_limit($php_time_limit);

# Set a base URL part consisting of the part after the server name, i.e. for absolute URLs and cookie paths.
$baseurl=str_replace(" ","%20",$baseurl);
$bs=explode("/",$baseurl);
$bs=array_slice($bs,3);
$baseurl_short="/" . join("/",$bs) . (count($bs)>0?"/":"");


$pagename=str_replace(".php","",  basename($_SERVER["PHP_SELF"]));


function get_utility_path($utilityname, &$checked_path = null)
{
    # !!! Under development - only some of the utilities are implemented!!!
    # Returns the full path to a utility if installed, else returns false.
    # Note that this function doesn't check that the utility is working.

    global $imagemagick_path, $ghostscript_path, $ghostscript_executable, $ffmpeg_path, $exiftool_path, $antiword_path, $pdftotext_path, $blender_path, $archiver_path, $archiver_executable;

    $checked_path = null;

    switch (strtolower($utilityname)) {
        case "im-convert":
            if (!isset($imagemagick_path)) {
                return false;
            } # ImageMagick convert path not configured.
            return get_executable_path($imagemagick_path,
                    array("unix" => "convert", "win" => "convert.exe"),
                    $checked_path);
            break;
        case "im-identify":
            if (!isset($imagemagick_path)) {
                return false;
            } # ImageMagick identify path not configured.
            return get_executable_path($imagemagick_path,
                    array("unix" => "identify", "win" => "identify.exe"),
                    $checked_path);
            break;
        case "im-composite":
            if (!isset($imagemagick_path)) {
                return false;
            } # ImageMagick composite path not configured.
            return get_executable_path($imagemagick_path,
                    array("unix" => "composite", "win" => "composite.exe"),
                    $checked_path);
            break;
        case "im-mogrify":
            if (!isset($imagemagick_path)) {
                return false;
            } # ImageMagick mogrify path not configured.
            return get_executable_path($imagemagick_path,
                    array("unix" => "mogrify", "win" => "mogrify.exe"),
                    $checked_path);
            break;
        case "ghostscript":
            if (!isset($ghostscript_path)) {
                return false;
            } # Ghostscript path not configured.
            if (!isset($ghostscript_executable)) {
                return false;
            } # Ghostscript executable not configured.
            return get_executable_path($ghostscript_path,
                    array("unix" => $ghostscript_executable, "win" => $ghostscript_executable),
                    $checked_path, true); # Note that $check_exe is set to true. In that way get_utility_path() becomes backwards compatible with get_ghostscript_command().
            break;
        case "ffmpeg":
            if (!isset($ffmpeg_path)) {
                return false;
            } # FFmpeg path not configured.
            return get_executable_path($ffmpeg_path,
                    array("unix" => "ffmpeg", "win" => "ffmpeg.exe"),
                    $checked_path);
            break;
        case "exiftool":
            if (!isset($exiftool_path)) {
                return false;
            } # Exiftool path not configured.
            return get_executable_path($exiftool_path,
                    array("unix" => "exiftool", "win" => "exiftool.exe"),
                    $checked_path);
            break;
        case "antiword":
            break;
        case "pdftotext":
            break;
        case "blender":
            break;
        case "archiver":
            if (!isset($archiver_path)) {
                return false;
            } # Archiver path not configured.
            if (!isset($archiver_executable)) {
                return false;
            } # Archiver executable not configured.
            return get_executable_path($archiver_path,
                    array("unix" => $archiver_executable, "win" => $archiver_executable),
                    $checked_path);
            break;
    }
}

function get_executable_path($path, $executable, &$checked_path,
        $check_exe = false)
{
    $checked_path = stripslashes($path) . "/" . $executable["unix"];
    if (file_exists($checked_path)) {
        return escapeshellarg($checked_path);
    }
    return false; # No path found.
}

