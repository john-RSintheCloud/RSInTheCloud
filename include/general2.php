<?php

# General functions, useful across the whole solution
// String EnCrypt + DeCrypt function
// Author: halojoy, July 2006
// Modified and commented by: laserlight, August 2006
//
// Exploratory implementation using bitwise ops on strings; Weedpacket September 2006
//function convert($text, $key = '')
//{
//    // return text unaltered if the key is blank
//    if ($key == '') {
//        return $text;
//    }
//
//    // remove the spaces in the key
//    $key = str_replace(' ', '', $key);
//    if (strlen($key) < 8) {
//        exit('key error');
//    }
//    // set key length to be no more than 32 characters
//    $key_len = strlen($key);
//    if ($key_len > 32) {
//        $key_len = 32;
//    }
//
//    // A wee bit of tidying in case the key was too long
//    $key = substr($key, 0, $key_len);
//
//    // We use this a couple of times or so
//    $text_len = strlen($text);
//
//    // fill key with the bitwise AND of the ith key character and 0x1F, padded to length of text.
//    $lomask = str_repeat("\x1f", $text_len); // Probably better than str_pad
//    $himask = str_repeat("\xe0", $text_len);
//    $k = str_pad("", $text_len, $key); // this one _does_ need to be str_pad
//    // {en|de}cryption algorithm
//    $text = (($text ^ $k) & $lomask) | ($text & $himask);
//
//    return $text;
//}
//function run_external($cmd, &$code)
//{
//# Thanks to dk at brightbyte dot de
//# http://php.net/manual/en/function.shell-exec.php
//# Returns an array with the resulting output (stdout & stderr).
//    debug("CLI command: $cmd");
//
//    $descriptorspec = array(
//        0 => array("pipe", "r"), // stdin is a pipe that the child will read from
//        1 => array("pipe", "w"), // stdout is a pipe that the child will write to
//        2 => array("pipe", "w") // stderr is a file to write to
//    );
//
//    $pipes = array();
//    $process = proc_open($cmd, $descriptorspec, $pipes);
//
//    $output = array();
//
//    if (!is_resource($process)) {
//        return false;
//    }
//
//    # Close child's input immediately
//    fclose($pipes[0]);
//
//    stream_set_blocking($pipes[1], false);
//    stream_set_blocking($pipes[2], false);
//
//    while (true) {
//        $read = array();
//        if (!feof($pipes[1])) {
//            $read[] = $pipes[1];
//        }
//        if (!feof($pipes[2])) {
//            $read[] = $pipes[2];
//        }
//
//        if (!$read) {
//            break;
//        }
//
//        $write = NULL;
//        $ex = NULL;
//        $ready = stream_select($read, $write, $ex, 2);
//
//        if ($ready === false) {
//            break; # Should never happen - something died
//        }
//
//        foreach ($read as $r) {
//            $s = rtrim(fgets($r, 1024), "\r\n"); # Reads a line and strips newline and carriage return from the end.
//            $output[] = $s;
//        }
//    }
//
//    fclose($pipes[1]);
//    fclose($pipes[2]);
//
//    debug("CLI output: " . implode("\n", $output));
//
//    $code = proc_close($process);
//
//    return $output;
//}
//


//function get_xml_entity_at_index_0($char)
//{
//    if (!is_string($char[0]) || (mb_strlen($char[0], "UTF-8") > 1)) {
//        die("function: 'get_xml_entity_at_index_0' requires data type: 'char' (single character). '{$char[0]}' does not match this type.");
//    }
//    switch ($char[0]) {
//        # http://en.wikipedia.org/wiki/List_of_XML_and_HTML_character_entity_references#Predefined_entities_in_XML
//        case '"':
//            return "&quot;";
//            break;
//        case '&':
//            return "&amp;";
//            break;
//        case "'":
//            return "&apos;";
//            break;
//        case '<':
//            return "&lt;";
//            break;
//        case '>':
//            return "&gt;";
//            break;
//        default:
//            return sanitize_char($char[0]);
//            break;
//    }
//}
//
//function sanitize_char($char)
//{
//    # http://en.wikipedia.org/wiki/Valid_characters_in_XML?section=1#XML_1.0
//    $mb_ord = trim(mb_encode_numericentity($char,
//                    array(0x0, 0x10FFFF, 0, 0x10FFFF), "UTF-8"), "&#;");
//    if ($mb_ord == 0x0009 || $mb_ord == 0x000A || $mb_ord == 0x000D) {
//        return $char;
//    }
//    if (($mb_ord >= 0x0020 && $mb_ord <= 0xD7FF) || ($mb_ord >= 0xE000 && $mb_ord <= 0xFFFD)) {
//        return $char;
//    }
//    if ($mb_ord >= 0x10000 && $mb_ord <= 0x10FFFF) {
//        return $char;
//    }
//    return ""; # Not a valid char, return an empty string.
//}
//
//
