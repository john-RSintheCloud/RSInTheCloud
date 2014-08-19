<?php
####################################
## RSitC 
## Application Configuration Script
####################################
 /**
 *  config.php is the application specific config for OORS and RSitC
 *  and is only committed to the relevant repo.  
 * Options are available through the DIC ['config']
 * and as globals for legacy
 * 
 *
 * @package RSitC
 * @subpackage Configuration
 */


##   Turn on DEBUG and test

$devMode = 1;
$config_show_performance_footer = true;
$css_reload_key=00;


##  APPLICATION SETTINGS

$applicationname="RS In The Cloud"; #  implementation name, eg 'SportArchive.tv'
                        #  can be overridden in secure.config
$productname="RS In The Cloud"; # Product name. Please do not over-ride
$productversion="Alpha";

##  Passwords and keys should be stored in the secure config at
$secure_config_location = '../_config/secure.config.php';


# Paths - using cloud transcode so these should not be needed
$imagemagick_path = '/usr/bin';
$ghostscript_path = '/usr/bin';
$ffmpeg_path = '/usr/bin';
$exiftool_path = '/usr/bin';
$antiword_path = '/usr/bin';
$pdftotext_path = '/usr/bin';
$imagemagick_colorspace = "sRGB";

##  storage is in S3, so these should not be needed
$storagedir = '/var/www/RS/filestore';
$storageurl = 'http://filestore.local';
##  though we may want this one
$tempdir = '/var/www/RS/filestore/tmp';
$tempUrl = 'http://filestore.local/tmp';

#  optional plugins to load in dic
$diPlugins = 'organisation';

