<?php
####################################
## OORS / RSitC
## Generic Configuration Script
####################################
 /**
 *  generic.config.php is the new generic config for OORS and RSitC
  * replacing config.default
 *  and is committed to both repos.  
  * Only string options can be set in here
  * no arrays, true / false, etc
 * Options are available through the DIC ['config']
 * 
 *
 * @package OORS/RSitC Generic
 * @subpackage Configuration
 */


##  Default  DEBUG and test turned off

$config_show_performance_footer = false;

# External items reload key
# Increment the below value to force a reload of the CSS, JavaScripts and other included items.
# This is intended for developer use when altering such files. 
# It will force a reload on all client browsers.
#
#TODO - tie in with dev environment variable
#  and tie in with file date/time stamp to automate reload
#
# used by legacy code so defined in config.php as well
# but that can be removed eventually

$css_reload_key=00;



##  Passwords and keys should be stored in the secure config at
## location set in config.php

# Email settings
$email_from = 'resourcespace@rsinthecloud.com';
$email_notify = 'resourcespace@rsinthecloud.com';

