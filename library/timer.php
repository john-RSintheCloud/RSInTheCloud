<?php
/**
 * timer class
 * for timing things - trivially.
 *
 * @author John Brookes <john@RSintheClouds.com>
 * @package RSintheClouds
 * @subpackage Library
*/

/**
 * Trivial timer class - set it and show it!
 *
 * @author John
 */
class timer {

protected $start;

    public function __construct() {
        $this->start = microtime(true);
    }

    public function show() {
        $time = microtime(true);
        $total_time = round(($time - $this->start), 4);
        echo $total_time . " sec";
    }
}
