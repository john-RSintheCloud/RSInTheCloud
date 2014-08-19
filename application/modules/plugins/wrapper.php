<?php
/**
 * plugin Wrapper
 * stubs for plugin functions which have been removed
 *
 * @author John Brookes <john@RSintheCloud.com>
 * @package RS Legacy
 * @subpackage Refactor
*/


/**
 * Plugin config
 * As plugins are not currently supported, all config is being extracted to here.
 */
# Use Plugins Manager
$use_plugins_manager = false; //  used on team home

# Allow Plugin Upload
$enable_plugin_upload = false;   //  used on team plugins

# List of active plugins.
$plugins=array();


function hook($name, $pagename = "", $params = array()) {
    return false;
}


function include_plugin_config($plugin_name, $config = "", $config_json = "") {
}

function register_plugin($plugin) {
    return true;
}

/**
 * Returns an xml compliant string in UTF-8
 *
 */
function xml_entities($string, $fromcharset = "")
{
    return '';
}

function get_plugin_css()
{
    return '';
}