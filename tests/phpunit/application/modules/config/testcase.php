<?php
####################################
## OORS
## TEST FILE - does not contain a real config
####################################
 /**
 *  TEST COMMENT - should be ignored
 */
                        #  indented comment

#*  This does not have to be valid PHP!

# basic variable
$applicationname="OO RS";

//  everything is stored as a string
$devMode = 1;
//  so this works as expected config->css->reload_key == '00'
$css_reload_key=00;

//  but this doesn't - config->config->show_performance_footer == 'false'
//  and as false == '', config->config->show_performance_footer == true
$config_show_performance_footer = false;

$false = '';
$true = 1;
$notFalse = 0 ; //  ?? because '0' != '', only empty string is false
        


##  need to read this so we can test loading it!
# should be able to use an absolute path in here - todo
$secureConfigLocation = 'tests/phpunit/application/modules/config/testsec.sec';

