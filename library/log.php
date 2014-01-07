<?php

/**
 * log class
 * for logging things
 *
 * Expand as required!
 *
 * @author John Brookes <john@RSintheCloud.com>
 * @package RSintheClouds
 * @subpackage Library
*/
class log {

    protected $_logContents = array();

    public function addLogLine($logLine)
    {
        $this->_logContents[] = $logLine;
        return this;
    }

    public function countLogLines() {
        return count($this->_logContents);
    }

}
