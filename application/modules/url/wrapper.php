<?php

/**
 * url Wrapper
 * A wrapper round the url and IP functions, to allow a future move to OO design.
 *
 * @author John Brookes <john@RSintheCloud.com>
 * @package RSintheClouds
 * @subpackage Refactor
*/


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


function resolve_user_agent($agent) {
    if ($agent == "") {
        return "-";
    }
    $agent = strtolower($agent);
    $bmatches = array(# Note - order is important - first come first matched
        "firefox" => "Firefox",
        "chrome" => "Chrome",
        "opera" => "Opera",
        "safari" => "Safari",
        "applewebkit" => "Safari",
        "msie 3." => "IE3",
        "msie 4." => "IE4",
        "msie 5.5" => "IE5.5",
        "msie 5." => "IE5",
        "msie 6." => "IE6",
        "msie 7." => "IE7",
        "msie 8." => "IE8",
        "msie 9." => "IE9",
        "msie 10." => "IE10",
        "msie" => "IE",
        "netscape" => "Netscape",
        "mozilla" => "Mozilla"
            #catch all for mozilla references not specified above
    );
    $osmatches = array(
        "iphone" => "iPhone",
        "nt 6.1" => "Windows 7",
        "nt 6.0" => "Vista",
        "nt 5.2" => "WS2003",
        "nt 5.1" => "XP",
        "nt 5.0" => "2000",
        "nt 4.0" => "NT4",
        "windows 98" => "98",
        "linux" => "Linux",
        "freebsd" => "FreeBSD",
        "os x" => "OS X",
        "mac_powerpc" => "Mac",
        "sunos" => "Sun",
        "psp" => "Sony PSP",
        "api" => "Api Client"
    );
    $b = "???";
    $os = "???";
    foreach ($bmatches as $key => $value) {
        if (!strpos($agent, $key) === false) {
            $b = $value;
            break;
        }
    }
    foreach ($osmatches as $key => $value) {
        if (!strpos($agent, $key) === false) {
            $os = $value;
            break;
        }
    }
    return $os . " / " . $b;
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
