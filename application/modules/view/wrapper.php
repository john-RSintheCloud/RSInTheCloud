<?php

/**
 * view Wrapper
 * A wrapper round the view helpers and url handling functions,
 * to allow a future move to OO design.
 *
 * Functions included in here are only required by layout and view scripts,
 * not by business logic.
 *
 * @author John Brookes <john@RSintheClouds.com>
 * @package RSintheClouds
 * @subpackage Refactor
*/

# Blank the header insert - used to add js and styles into the header
$headerinsert = "";

function getval($val, $default, $force_numeric = false) {
    # return a value from POST, GET or COOKIE (in that order), or $default if none set
    if (array_key_exists($val, $_POST)) {
        return ($force_numeric && !is_numeric($_POST[$val]) ? $default : $_POST[$val]);
    }
    if (array_key_exists($val, $_GET)) {
        return ($force_numeric && !is_numeric($_GET[$val]) ? $default : $_GET[$val]);
    }
    if (array_key_exists($val, $_COOKIE)) {
        return ($force_numeric && !is_numeric($_COOKIE[$val]) ? $default : $_COOKIE[$val]);
    }
    return $default;
}

function getvalescaped($val, $default, $force_numeric = false) {
    # return a value from get/post, escaped, SQL-safe and XSS-free
    $value = getval($val, $default, $force_numeric);
    if (is_array($value)) {
        foreach ($value as &$item) {
            $item = escape_check($item);
            if (strpos(strtolower($item), "<script") !== false)
                return $default;
        }
    }
    else {
        $value = escape_check($value);
        if (strpos(strtolower($value), "<script") !== false) {
            return $default;
        }
    }
    return $value;
}
function nicedate($date, $time = false, $wordy = true) {
    # format a MySQL ISO date
    # Always use the 'wordy' style  as this works better internationally.
    global $lang;
    $y = substr($date, 0, 4);
    if (($y == "") || ($y == "0000"))
        return "-";
    $m = @$lang["months"][substr($date, 5, 2) - 1];
    if ($m == "")
        return $y;
    $d = substr($date, 8, 2);
    if ($d == "" || $d == "00")
        return $m . " " . $y;
    $t = $time ? (" @ " . substr($date, 11, 5)) : "";
    return $d . " " . $m . " " . substr($y, 2, 2) . $t;
}
