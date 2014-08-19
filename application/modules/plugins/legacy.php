<?php
/**
 * plugin Wrapper
 * somewhere to hold legacy plugin code before it is permanently scrapped..
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
$use_plugins_manager = true;

# Allow Plugin Upload
$enable_plugin_upload = true;

# List of active plugins.
# Note that multiple plugins must be specified within array() as follows:
# $plugins=array("loader","rss","messaging","googledisplay");
$plugins=array();




# Setup plugin configurations
if ($use_plugins_manager){
	include "plugin_functions.php";
	$legacy_plugins = $plugins; # Make a copy of plugins activated via config.php
	#Check that manually (via config.php) activated plugins are included in the plugins table.
	foreach($plugins as $plugin_name){
		if ($plugin_name!=''){
			if(sql_value("SELECT inst_version AS value FROM plugins WHERE name='$plugin_name'",'')==''){
				#Installed plugin isn't marked as installed in the DB.  Update it now.
				#Check if there's a plugin.yaml file to get version and author info.
				$plugin_yaml_path = dirname(__FILE__)."/../plugins/{$plugin_name}/{$plugin_name}.yaml";
				$p_y = get_plugin_yaml($plugin_yaml_path, false);
				#Write what information we have to the plugin DB.
				sql_query("REPLACE plugins(inst_version, author, descrip, name, info_url, update_url, config_url) ".
						  "VALUES ('{$p_y['version']}','{$p_y['author']}','{$p_y['desc']}','{$plugin_name}'," .
						  "'{$p_y['info_url']}','{$p_y['update_url']}','{$p_y['config_url']}')");
			}
		}
	}
    # Need verbatum queries for this query
    $mysql_vq = $mysql_verbatim_queries;
    $mysql_verbatim_queries = true;
	$active_plugins = (sql_query("SELECT name,enabled_groups,config,config_json FROM plugins WHERE inst_version>=0 order by priority DESC"));
    $mysql_verbatim_queries = $mysql_vq;
	foreach($active_plugins as $plugin){

		# Check group access, only enable for global access at this point
		if ($plugin['enabled_groups']=='')
			{
			# Add to the plugins array if not already present which is what we are working with
			# later on.
			if (!in_array($plugin['name'],$plugins)) {$plugins[]=$plugin['name'];}
			include_plugin_config($plugin['name'], $plugin['config'], $plugin['config_json']);
			}
	}
} else {
	for ($n=0;$n<count($plugins);$n++)
		include_plugin_config($plugins[$n]);
}

# Register all plugins
for ($n=0;$n<count($plugins);$n++)
	{
	register_plugin($plugins[$n]);
	}


    function hook($name, $pagename = "", $params = array()) {
    # Plugin architecture. Look for a hook with this name and execute.
    if ($pagename == "") {
        global $pagename;
    } # If page name not provided, use global page name.
    global $plugins;

    $found = false;
    for ($n = 0; $n < count($plugins); $n++) {
        # "All" hooks
        $function = "Hook" . ucfirst($plugins[$n]) . "All" . ucfirst($name);
        if (function_exists($function)) {
            # Function must return 'true' if successful (so existing functionality is replaced)
            $found = call_user_func_array($function, $params);
        } else { //use 'else' here because without it it's possible to end up running hook functions twice when the hook passes pagename and params directly.
            # Specific hook
            $function = "Hook" . ucfirst($plugins[$n]) . ucfirst($pagename) . ucfirst($name);
            if (function_exists($function)) {
                # Function must return 'true' if successful (so existing functionality is replaced)
                $found = call_user_func_array($function, $params);
            }
        }
    }
    return $found;
}


function include_plugin_config($plugin_name, $config = "", $config_json = "") {
    global $mysql_charset;
    $configpath = dirname(__FILE__) . "/../plugins/" . $plugin_name . "/config/config.default.php";
    if (file_exists($configpath)) {
        include $configpath;
    }

    $configpath2 = dirname(__FILE__) . "/../plugins/" . $plugin_name . "/config/config.php";
    if (file_exists($configpath2)) {
        include $configpath2;
    }


    if ($config_json != "" && function_exists('json_decode')) {
        if (!isset($mysql_charset)) {
            $config_json = iconv('ISO-8859-1', 'UTF-8', $config_json);
        }
        $config_json = json_decode($config_json, true);
        if ($config_json) {
            foreach ($config_json as $key => $value) {
                $$key = $value;
            }
        }
    } elseif ($config != "") {
        $config = unserialize(base64_decode($config));
        foreach ($config as $key => $value)
            $$key = $value;
    }

    # Copy config variables to global scope.
    unset($plugin_name, $config, $config_json, $configpath);
    $vars = get_defined_vars();
    foreach ($vars as $name => $value) {
        global $$name;
        $$name = $value;
    }
}

function register_plugin($plugin) {
    global $plugins, $language, $pagename, $lang, $applicationname;

    # Include language file
    $langpath = dirname(__FILE__) . "/../plugins/" . $plugin . "/languages/";
    if (file_exists($langpath . "en.php")) {
        include $langpath . "en.php";
    }
    if ($language != "en") {
        if (substr($language, 2, 1) == '-' && substr($language, 0, 2) != 'en')
            @include $langpath . safe_file_name(substr($language, 0, 2)) . ".php";
        @include $langpath . safe_file_name($language) . ".php";
    }

    # Also include plugin hook file for this page.
    if ($pagename == "collections_frameless_loader") {
        $pagename = "collections";
    }
    $hookpath = dirname(__FILE__) . "/../plugins/" . $plugin . "/hooks/" . $pagename . ".php";
    if (file_exists($hookpath)) {
        include $hookpath;
    }

    # Support an 'all' hook
    $hookpath = dirname(__FILE__) . "/../plugins/" . $plugin . "/hooks/all.php";
    if (file_exists($hookpath)) {
        include $hookpath;
    }

    return true;
}

/**
 * Returns an xml compliant string in UTF-8
 *
 * Built upon a code snippet from steve at mcdragonsoftware dot com
 * @link http://php.net/manual/en/function.htmlentities.php#106535
 *
 * @param string $string A string to be made xml compliant.
 * @param string $fromcharset The charset of $string.
 * @access public
 * @return string Returns the xml compliant UTF-8 encoded string.
 */
function xml_entities($string, $fromcharset = "")
{
    # Convert the data to UTF-8 if not already.
    if ($fromcharset == "") {
        global $mysql_charset;
        if (isset($mysql_charset)) {
            $fromcharset = $mysql_charset;
        } else {
            $fromcharset = "UTF-8";
        } # Default to UTF-8.
    }
    if (strtolower($fromcharset) != "utf-8") {
        $string = mb_convert_encoding($string, 'UTF-8', $fromcharset);
    }

    # Sanitize the string to comply with xml:
    # http://en.wikipedia.org/wiki/Valid_characters_in_XML?section=1#XML_1.0
    $not_in_list = "A-Z0-9a-z\s_-";
    return preg_replace_callback("/[^{$not_in_list}]/u",
            'get_xml_entity_at_index_0', $string);
}

