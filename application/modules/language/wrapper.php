<?php

/**
 * Language Wrapper
 * A wrapper round the language functions, to allow future rationalisation.
 *
 * @author John Brookes <john@RSintheCloud.com>
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


function get_all_site_text($findpage = "", $findname = "", $findtext = "") {
    # Returns a list of all available editable site text (content).
    # If $find is specified a search is performed across page, name and text fields.
    global $defaultlanguage;
    $findname = trim($findname);
    $findpage = trim($findpage);
    $findtext = trim($findtext);
    $sql = "site_text s ";

    if ($findname != "" || $findpage != "" || $findtext != "") {
        $sql.=" where (";
    }


    if ($findname != "") {
        $findnamearray = explode(" ", $findname);
        for ($n = 0; $n < count($findnamearray); $n++) {
            $sql.=' name like "%' . $findnamearray[$n] . '%"';
            if ($n + 1 != count($findnamearray)) {
                $sql.=" and ";
            }
        }
    }

    if ($findpage != "") {
        $findpagearray = explode(" ", $findpage);
        if ($findname != "") {
            $sql.=" and ";
        }
        for ($n = 0; $n < count($findpagearray); $n++) {
            $sql.=' page like "%' . $findpagearray[$n] . '%"';
            if ($n + 1 != count($findpagearray)) {
                $sql.=" and ";
            }
        }
    }

    if ($findtext != "") {
        $findtextarray = explode(" ", $findtext);
        if ($findname != "" || $findpage != "") {
            $sql.=" and ";
        }
        for ($n = 0; $n < count($findtextarray); $n++) {
            $sql.=' text like "%' . $findtextarray[$n] . '%"';
            if ($n + 1 != count($findtextarray)) {
                $sql.=" and ";
            }
        }
    }
    if ($findname != "" || $findpage != "" || $findtext != "") {
        $sql.=" ) ";
    }

    return sql_query("select distinct s.page,s.name,(select text from site_text st where st.name=s.name and st.page=s.page order by (language='$defaultlanguage') desc limit 1) text from $sql order by (s.page='all') desc,s.page,name");
}

function get_site_text($page, $name, $language, $group) {
    # Returns a specific site text entry.
    if ($group == "") {
        $g = "null";
        $gc = "is";
    } else {
        $g = "'" . $group . "'";
        $gc = "=";
    }

    $text = sql_query("select * from site_text where page='$page' and name='$name' and language='$language' and specific_to_group $gc $g");
    if (count($text) == 0) {
        $existing = escape_check(sql_value("select text value from site_text where page='$page' and name='$name' limit 1", ""));
        return $existing;
    }
    return $text[0]["text"];
}

function check_site_text_custom($page, $name) {
    # Check if site text section is custom, i.e. deletable.

    $check = sql_query("select custom from site_text where page='$page' and name='$name'");
    if (isset($check[0]["custom"])) {
        return $check[0]["custom"];
    }
}

function save_site_text($page, $name, $language, $group) {
    # Saves the submitted site text changes to the database.

    if ($group == "") {
        $g = "null";
        $gc = "is";
    } else {
        $g = "'" . $group . "'";
        $gc = "=";
    }

    global $custom, $newcustom;

    if ($newcustom) {
        $test = sql_query("select * from site_text where page='$page' and name='$name'");
        if (count($test) > 0) {
            return true;
        }
    }
    if ($custom == "") {
        $custom = 0;
    }
    if (getval("deletecustom", "") != "") {
        sql_query("delete from site_text where page='$page' and name='$name'");
    } elseif (getval("deleteme", "") != "") {
        sql_query("delete from site_text where page='$page' and name='$name' and specific_to_group $gc $g");
    } elseif (getval("copyme", "") != "") {
        sql_query("insert into site_text(page,name,text,language,specific_to_group,custom) values ('$page','$name','" . getvalescaped("text", "") . "','$language',$g,'$custom')");
    } elseif (getval("newhelp", "") != "") {
        global $newhelp;
        $check = sql_query("select * from site_text where page = 'help' and name='$newhelp'");
        if (!isset($check[0])) {
            sql_query("insert into site_text(page,name,text,language,specific_to_group) values ('$page','$newhelp','','$language',$g)");
        }
    } else {
        $text = sql_query("select * from site_text where page='$page' and name='$name' and language='$language' and specific_to_group $gc $g");
        if (count($text) == 0) {
            # Insert a new row for this language/group.
            sql_query("insert into site_text(page,name,language,specific_to_group,text,custom) values ('$page','$name','$language',$g,'" . getvalescaped("text", "") . "','$custom')");
        } else {
            # Update existing row
            sql_query("update site_text set text='" . getvalescaped("text", "") . "' where page='$page' and name='$name' and language='$language' and specific_to_group $gc $g");
        }
    }
}
