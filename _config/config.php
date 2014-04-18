<?php
$config_show_performance_footer = true;

###############################
## ResourceSpace
## Local Configuration Script
###############################

# All custom settings should be entered in this file.
# Options may be copied from config.default.php and configured here.

# MySQL database settings
$mysql_server = 'localhost';
$mysql_username = 'RS';
$mysql_password = 'rs123';
$mysql_db = 'RS';

$mysql_bin_path = '/usr/bin';

# Base URL of the installation
$baseurl = 'http://local.rs';

# Email settings
$email_from = 'resourcespace@rsinthecloud.com';
$email_notify = 'resourcespace@rsinthecloud.com';

$spider_password = 'PYgULEQepybE';
$scramble_key = '8APEjUpAReQE';

$api_scramble_key = 'HazasyTU3UvA';

# Paths
$imagemagick_path = '/usr/bin';
$ghostscript_path = '/usr/bin';
$ffmpeg_path = '/usr/bin';
$exiftool_path = '/usr/bin';
$antiword_path = '/usr/bin';
$pdftotext_path = '/usr/bin';

$storagedir = '/var/www/filestore';
$storageurl = 'http://local.filestore';
$tempdir = '/var/www/filestore/tmp';
$tempUrl = 'http://local.filestore/tmp';
$ftp_server = 'my.ftp.server';
$ftp_username = 'my_username';
$ftp_password = 'my_password';
$ftp_defaultfolder = 'temp/';
$thumbs_display_fields = array(8,3);
$list_display_fields = array(8,3,12);
$sort_fields = array(12);
$imagemagick_colorspace = "sRGB";
