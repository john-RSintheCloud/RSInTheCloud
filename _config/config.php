<?php
####################################
## OORS
## Application Configuration Script
####################################
 /**
 *  config.php is the application specific config for OORS and RSitC
 *  and is only committed to the relevant repo.  
 * Options are available through the DIC ['config']
 * and as globals for legacy
 * 
 *
 * @package OORS
 * @subpackage Configuration
 */


##   Turn on DEBUG and test

$devMode = 1;
$config_show_performance_footer = true;
$css_reload_key=00;


##  APPLICATION SETTINGS

$applicationname="OO RS"; #  implementation name, eg 'Bioquell'
                        #  can be overridden in secure.config
$productname="OO RS"; # Product name. Please do not over-ride
$productversion="Alpha";

##  Passwords and keys should be stored in the secure config at
$secureConfigLocation = '_config/secure.config.php';


# Paths - if not using cloud transcode
$imagemagick_path = '/usr/bin';
$ghostscript_path = '/usr/bin';
$ffmpeg_path = '/usr/bin';
$exiftool_path = '/usr/bin';
$antiword_path = '/usr/bin';
$pdftotext_path = '/usr/bin';
$imagemagick_colorspace = "sRGB";

$storagedir = '/var/www/RS/filestore';
$storageurl = 'http://filestore.local';
$tempdir = '/var/www/RS/filestore/tmp';
$tempUrl = 'http://filestore.local/tmp';

#  optional plugins to load in dic
#  OORS does not currently have any.
//  $diPlugins = 'organisation';
$diPlugins = '';