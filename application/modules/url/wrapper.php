<?php

/**
 * url Wrapper
 * A wrapper round the url and IP functions, to allow a future move to OO design.
 *
 * @author John Brookes <john@RSintheCloud.com>
 * @package RSintheClouds
 * @subpackage Refactor
*/


/**
 * return a value from POST, GET or COOKIE (in that order),
 * or $default if none set
 *
 * @param string $val               name of value to get
 * @param mixed $default            default value = '' or 0
 * @param boolean $force_numeric    force numeric
 * @return mixed                    value passed in
 */
function getval($val, $default = '', $force_numeric = false) {
    #
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

/**
 * return a value from get/post, escaped, SQL-safe and XSS-free
 *
 * @param string $val name of value to get
 * @param mixed $default default value
 * @param boolean $force_numeric
 * @return mixed escaped value passed in
 */
function getvalescaped($val, $default = '', $force_numeric = false) {

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



function get_ip() {

    # Attempt to read Apache forwarding header first
    $headers = @apache_request_headers();
    if (@array_key_exists('X-Forwarded-For', $headers)) {
        return $headers["X-Forwarded-For"];
    }

    # Not forwarded.
    if (array_key_exists("REMOTE_ADDR", $_SERVER)) {
        return $_SERVER["REMOTE_ADDR"];
    }


    # Can't find an IP address.
    return "???";
}

function redirect($url) {
    # Redirect to the provided URL using a HTTP header Location directive.
    global $baseurl;

    if (getval("ajax", "") != "") {
        # When redirecting from an AJAX loaded page, forward the AJAX parameter automatically so headers and footers are removed.
        if (strpos($url, "?") !== false) {
            $url.="&ajax=true";
        } else {
            $url.="?ajax=true";
        }
    }

    if (substr($url, 0, 1) == "/") {
        # redirect to an absolute URL
        header("Location: " . (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? "https://" : "http://") . $_SERVER["HTTP_HOST"] . $url);
    } else {
        # redirect to a relative URL
        header("Location: " . $baseurl . "/" . $url);
    }
    exit();
}
