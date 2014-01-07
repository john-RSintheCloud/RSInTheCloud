<?php
/**
 * timer class
 * for timing things - trivially - and counting things.
 *
 * @author John Brookes <john@RSintheCloud.com>
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
        return$this;
    }

    public function show() {
        $time = microtime(true);
        $total_time = round(($time - $this->start), 4);
        return $total_time ;
    }
}
