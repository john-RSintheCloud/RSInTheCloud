<?php
/**
 * timer class
 * for timing things - trivially 
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


    /**
     *
     * @var array of start times
     */
    protected $start;


    public function start($id='first') {
        $this->start[$id] = microtime(true);
        return $this ;
    }
    public function show($id = 'first') {
        $time = microtime(true);
        $total_time = round(($time - $this->start[$id]), 4);
        return $total_time ;
    }
}
