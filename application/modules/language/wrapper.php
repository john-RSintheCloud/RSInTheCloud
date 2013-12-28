<?php

/**
 * Language Wrapper
 * A wrapper round the language functions, to allow future rationalisation.
 *
 * @author John Brookes <john@RSintheClouds.com>
 * @package RSintheClouds
 * @subpackage Refactor
 */

require_once ("language_functions.php");


# Include the appropriate language file

if (isset($defaultlanguage)) {
    $language = $defaultlanguage;
} else {
    $language = http_get_preferred_language();
}

if (isset($_COOKIE["language"])) {
    $language = $_COOKIE["language"];
}
if (isset($_GET["language_set"])) {
    $language = $_GET["language_set"];
    # Needs rewriting to use the general.php: rs_setcookie() here.
    if ($global_cookies) {
        # Remove previously set cookies to avoid clashes
        setcookie("language", "", time() - 3600, $baseurl_short . "pages/");
        setcookie("language", "", time() - 3600, $baseurl_short);
        # Set new cookie
        setcookie("language", $language, time() + (3600 * 24 * 1000), "/");
    } else {
        # Set new cookie
        setcookie("language", $language, time() + (3600 * 24 * 1000));
        setcookie("language", $language, time() + (3600 * 24 * 1000), $baseurl_short . "pages/");
    }
}

# Languages disabled - always use the default.
if ($disable_languages) {
    $language = $defaultlanguage;
}

# Fix due to rename of US English language file
if ($language == "us") {
    $language = "en-US";
}

# Make sure the provided language is a valid language
if (empty($language) || !array_key_exists($language, $languages)) {
    if (isset($defaultlanguage))
        $language = $defaultlanguage;
    else
        $language = 'en';
}

# Always include the english pack (in case items have not yet been translated)
include "languages/en.php";
if ($language != "en") {
    if (substr($language, 2, 1) == '-' && substr($language, 0, 2) != 'en'){
        include "languages/" . safe_file_name(substr($language, 0, 2)) . ".php";
    }
    include "languages/" . safe_file_name($language) . ".php";
}



# Pre-load all text for this page.
$site_text=array();
$results=sql_query("select language,name,text from site_text where (page='$pagename' or page='all') and (specific_to_group is null or specific_to_group=0)");
for ($n=0;$n<count($results);$n++) {$site_text[$results[$n]["language"] . "-" . $results[$n]["name"]]=$results[$n]["text"];}



function http_get_preferred_language($strict_mode = false) {
    global $languages;

    if (!isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))
        return null;
    $lang_variable = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
    if (empty($lang_variable))
        return null;

    $accepted_languages = preg_split('/,\s*/', $lang_variable);
    $current_lang = false;
    $current_quality = 0;

    foreach ($accepted_languages as $accepted_language) {
        $res = preg_match('/^([a-z]{1,8}(?:-[a-z]{1,8})*)(?:;\s*q=(0(?:\.[0-9]{1,3})?|1(?:\.0{1,3})?))?$/i', $accepted_language, $matches);
        if (!$res)
            continue;

        $lang_code = explode('-', $matches[1]);

        // Use specified quality, if any
        if (isset($matches[2]))
            $lang_quality = (float) $matches[2];
        else
            $lang_quality = 1.0;

        while (count($lang_code)) {
            $found = false;
            foreach ($languages as $short => $name) {
                if (strtolower($short) == strtolower(join('-', $lang_code))) {
                    if ($lang_quality > $current_quality) {
                        $current_lang = $short;
                        $current_quality = $lang_quality;
                        $found = true;
                        break;
                    }
                }
            }

            if ($strict_mode || $found)
                break;

            array_pop($lang_code);
        }
    }

    return $current_lang;
}

function text($name) {
    global $config_disable_nohelp_warning;

    # Returns site text with name $name, or failing that returns dummy text.
    global $site_text, $pagename, $language, $languages, $usergroup;
    if (array_key_exists($language . "-" . $name, $site_text)) {
        return $site_text[$language . "-" . $name];
    }

    # Can't find the language key? Look for it in other languages.
    reset($languages);
    foreach ($languages as $key => $value) {
        if (array_key_exists($key . "-" . $name, $site_text)) {
            return $site_text[$key . "-" . $name];
        }
    }
    if (!array_key_exists('en', $languages)) {
        if (array_key_exists("en-" . $name, $site_text)) {
            return $site_text["en-" . $name];
        }
    }

    return "";
}

function get_section_list($page) {
    return sql_array("select name value from site_text where page='$page' and name<>'introtext' order by name");
}
