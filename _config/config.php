<?php

##    DEBUG and test

$config_show_performance_footer = true;

###############################
## RSitC Sample Config
## Local Configuration Script
###############################

# All custom settings should be entered in this file.
# Options may be copied from config.default.php and configured here.

##  Passwords and keys should be stored in the secure config at
$secure_config_location = '../_config/secure.config.php';

# MySQL database settings
$mysql_server = 'localhost';
$mysql_username = 'RS';
$mysql_password = 'overwrittenBySecureConfig';
$mysql_db = 'RS';

$mysql_bin_path = '/usr/bin';

# Base URL of the installation
$baseurl = 'http://rsitc.local';

# Email settings
$email_from = 'resourcespace@rsinthecloud.com';
$email_notify = 'resourcespace@rsinthecloud.com';

$spider_password = 'overwrittenBySecureConfig';
$scramble_key = 'overwrittenBySecureConfig';

$api_scramble_key = 'overwrittenBySecureConfig';

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

//  These are workrounds for the lack of adequate database design!
$thumbs_display_fields = array(8,3);
$list_display_fields = array(8,3,12);
$sort_fields = array(12);
