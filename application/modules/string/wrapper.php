<?php
/**
 * string Wrapper
 * RS does lots of string processing, without validating the object is a string.
 * This wrapper is for moving to a proper string class
 *
 * @author John Brookes <john@RSintheCloud.com>
 * @package RSintheClouds
 * @subpackage Refactor
*/

function trim_spaces($text)
{
    # replace multiple spaces with a single space
    while (strpos($text, "  ") !== false) {
        $text = str_replace("  ", " ", $text);
    }
    return trim($text);
}

function trim_array($array, $trimchars = '')
{
    # removes whitespace from the beginning/end of all elements in an array

    for ($n = 0; $n < count($array); $n++) {
        $array[$n] = trim($array[$n]);
        if (strlen($trimchars) > 0) {
            // also trim off extra characters they want gone
            $array[$n] = trim($array[$n], $trimchars);
        }
    }
    return $array;
}

function tidylist($list)
{
    # Takes a value as returned from a check-list field type and reformats to be more display-friendly.
    # Check-list fields have a leading comma.
    $list = trim($list);
    if (strpos($list, ",") === false) {
        return $list;
    }
    $list = explode(",", $list);
    if (trim($list[0]) == "") {
        array_shift($list);
    } # remove initial comma used to identify item is a list
    $op = join(", ", trim_array($list));
    #if (strpos($op,".")!==false) {$op=str_replace(", ","<br/>",$op);}
    return $op;
}

function tidy_trim($text, $length)
{
    # Trims $text to $length if necessary. Tries to trim at a space if possible. Adds three full stops
    # if trimmed...
    $text = trim($text);
    if (strlen($text) > $length) {
        $text = mb_substr($text, 0, $length - 3, 'utf-8');
        # Trim back to the last space
        $t = strrpos($text, " ");
        $c = strrpos($text, ",");
        if ($c !== false) {
            $t = $c;
        }
        if ($t > 5) {
            $text = substr($text, 0, $t);
        }
        $text = $text . "...";
    }
    return $text;
}

function average_length($array)
{
    # Returns the average length of the strings in an array
    $total = 0;
    for ($n = 0; $n < count($array); $n++) {
        $total+=strlen(i18n_get_translated($array[$n]));
    }
    return ($total / count($array));
}


function newlines($text)
{
    # Replace escaped newlines with real newlines.
    $text = str_replace("\\n", "\n", $text);
    $text = str_replace("\\r", "\r", $text);
    return $text;
}

function string_similar($string1, $string2)
{
    # Returns an integer score based on how similar the two strings are.
    # This was used when importing data for "fuzzy" keyword/option matching.
    $score = 0;
    $string1 = trim(strtolower($string1));
    $string2 = trim(strtolower($string2));
    if ($string1 == $string2) {
        return 9999;
    }
    if (substr($string1, 0, 1) == substr($string2, 0, 1)) {
        $score+=10;
    }
    for ($n = 0; $n < strlen($string1) - 1; $n++) {
        $pair = substr($string1, $n, 2);
        for ($m = 0; $m < strlen($string2) - 1; $m++) {
            if ($pair == substr($string2, $m, 2)) {
                $score++;
            }
        }
    }

    return $score;
}

function formatfilesize($bytes)
{
    # Return a human-readable string representing $bytes in either KB or MB.

    global $lang;
    if ($bytes < 1024) {
        return number_format((double) $bytes) . "&nbsp;" . $lang["byte-symbol"];
    } elseif ($bytes < pow(1024, 2)) {
        return number_format((double) ceil($bytes / 1024)) . "&nbsp;" . $lang["kilobyte-symbol"];
    } elseif ($bytes < pow(1024, 3)) {
        return number_format((double) $bytes / pow(1024, 2), 1) . "&nbsp;" . $lang["megabyte-symbol"];
    } elseif ($bytes < pow(1024, 4)) {
        return number_format((double) $bytes / pow(1024, 3), 1) . "&nbsp;" . $lang["gigabyte-symbol"];
    } else {
        return number_format((double) $bytes / pow(1024, 4), 1) . "&nbsp;" . $lang["terabyte-symbol"];
    }
}

function filesize2bytes($str)
{
    /**
     * Converts human readable file size (e.g. 10 MB, 200.20 GB) into bytes.
     *
     * @param string $str
     * @return int the result is in bytes
     * @author Svetoslav Marinov
     * @author http://slavi.biz
     */
    $bytes = 0;

    $bytes_array = array(
        'b' => 1,
        'kb' => 1024,
        'mb' => 1024 * 1024,
        'gb' => 1024 * 1024 * 1024,
        'tb' => 1024 * 1024 * 1024 * 1024,
        'pb' => 1024 * 1024 * 1024 * 1024 * 1024,
    );

    $bytes = floatval($str);

    if (preg_match('#([KMGTP]?B)$#si', $str, $matches) && !empty($bytes_array[strtolower($matches[1])])) {
        $bytes *= $bytes_array[strtolower($matches[1])];
    }

    $bytes = intval(round($bytes, 2));

    #add leading zeroes (as this can be used to format filesize data in resource_data for sorting)
    return sprintf("%010d", $bytes);
}

function txt2html($txt)
{
// Transforms txt in html
// based on http://blog.matrixresources.com/blog/using-php-html-ize-plain-text
    $txt = htmlentities($txt, ENT_COMPAT, "UTF-8");
    // keep whitespacing
    while (!( strpos($txt, '  ') === FALSE ))
        $txt = str_replace('  ', '&nbsp; ', $txt);

    //Basic formatting
    $eol = ( strpos($txt, "\r") === FALSE ) ? "\n" : "\r\n";
    $html = str_replace("$eol", " <br/> ", $txt);


    /* General rules for replacing images */
    $imgReplacement = "<img align=left width=180 src=../..$5$6$7$8 /><br/>";

    /* Rules per supported file type */
    $extArray = array(
//	".html" => "<" . "a href=../..$5$6$7$8>$4$5$6$7$8"."</a>",
//	".php" => "<" . "a href=../..$5$6$7$8>$4$5$6$7$8"."</a>",
        ".jpg" => $imgReplacement,
        ".png" => $imgReplacement,
        ".gif" => $imgReplacement,
        "" => "<" . "a href=http://$4$5$6$7$8>$4$5$6$7$8" . "</a>");
    /* $1 = http:
     * $2 = http
     * $3 = //www.eilertech.com
     * $4 = www.eilertech.com
     * $5 = /stories/powernaut/
     * $6 = 1941
     * $7 = .htm
     * $8 = #1
     * $9 = 1
     * Excluded:  ?fn=britannia_beach.txt */

// For each supported file type, up to and including Blank
    foreach ($extArray as $ext => $replacement) {

        // Define the search pattern here
        $pattern = "|((http):)(//([^/?# ]*))([^?# ,\.\)]*/)([^\.]*)?(" . $ext
                //12       3  4          5               6        7
                . "[^# ,\)]*)(#([^ ,\.]*))?|i";
        //           8 9

        /* We have the pattern, the replacement, and the HTML being built;
         * do the replacement. */
        $html = preg_replace($pattern, $replacement, $html);
    }

    $html = preg_replace('/\*(\w.*?)\*/', '<b>$1</b>', $html);

    return $html;
}

function is_html($string)
{
    return preg_match("/<[^<]+>/", $string, $m) != 0;
}

function make_api_key($username, $password)
{
    // this is simply an encryption for username and password that will work as an alternative way to log in for remote access pages such as rss and apis
    // this is simply to avoid sending username and password plainly in the url.
    global $api_scramble_key;
    if (extension_loaded('mcrypt') && extension_loaded('hash')) {
        require_once 'Cipher.php';
        $cipher = new Cipher($api_scramble_key);
        return $cipher->encrypt($username . "|" . $password, $api_scramble_key);
    } else {
        throw new Exception ('mcrypt and hash not loaded');
    }
}

function decrypt_api_key($key)
{
    global $api_scramble_key;
    if (extension_loaded('mcrypt') && extension_loaded('hash')) {
        require_once 'Cipher.php';
        $cipher = new Cipher($api_scramble_key);
        $key = $cipher->decrypt($key);
    } else {
        throw new Exception ('mcrypt and hash not loaded');
    }
    return explode("|", $key);
}
