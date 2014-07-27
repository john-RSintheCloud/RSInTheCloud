<?php
####################################
## OORS
## Application Configuration Script
####################################


##    DEBUG and test

$config_show_performance_footer = true;

# External items reload key
# Increment the below value to force a reload of the CSS, JavaScripts and other included items.
# This is intended for developer use when altering such files. 
# It will force a reload on all client browsers.
#
#TODO - tie in with dev environment variable
#  and tie in with file date/time stamp to automate reload

$css_reload_key=00;


##  APPLICATION SETTINGS

$applicationname="OO RS"; #  implementation name, eg 'Bioquell'
                        #  can be overridden in secure.config
$productname="OO RS"; # Product name. Please do not over-ride
$productversion="Alpha";



##  Passwords and keys should be stored in the secure config at
$secure_config_location = '../_config/secure.config.php';

# MySQL database settings
$mysql_server = 'localhost';
$mysql_username = 'RS';
$mysql_password = 'overwrittenBySecureConfig';
$mysql_db = 'RS';


# Base URL of the installation is set dynamically
#  but we may want to set a base folder, eg '/RS' (leading /)
$baseFolder = '';

# Email settings
$email_from = 'resourcespace@rsinthecloud.com';
$email_notify = 'resourcespace@rsinthecloud.com';

$spider_password = 'overwrittenBySecureConfig';
$scramble_key = 'overwrittenBySecureConfig';

$api_scramble_key = 'overwrittenBySecureConfig';

# Paths 
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

//  These are legacy workrounds for the lack of adequate database design!
$thumbs_display_fields = array(8,3);
$list_display_fields = array(8,3,12);
$sort_fields = array(12);
