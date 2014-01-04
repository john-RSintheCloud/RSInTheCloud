<?php
namespace Rsitc\Logger;

/**
 * CSV log File Handling Class
 * 
 * Designed to write one line at a time into a log
 * 
 * Expects (fully justified) file name and array of data to write
 * If file does not exist, it is created and 
 * the array keys are used as column heads,
 * otherwise the data is just written into the file.
 * 
 * Uses common interface, so isValid and errorList work as expected.
 * 
 * use
 *      $log = new Rsitc_Logger_CsvFile($filename, $data)
 *      if (! $log->isValid){var_dump ($log->errorList);}
 * 
 * Code loosely based on 
 * http://www.phpclasses.org/browse/file/5802.html
 * 
 * JBB  19/06/2012
 * 
 * LICENSE: RSitC
 * 
 * PHP Version  PHP 5.3.10
 * 
 * @category  Library
 * @package   Logger
 * @author    John Brookes <John@RSitC.com>
 * @copyright 2012 John Brookes
*/

/**
 * use
 * $log = new EAndL_Logger_CsvFile($filename, $data);
 * if (! $log.isValid){var_dump ($log->errorList);}
 * 
 * @category  Library
 * @package   Logger
 * @author    John Brookes <John@RSitC.com>
*/
class CsvFile
{

    /**
     * File base path
     * 
     * @var string 
     */
    protected $_baseDir;
    
    /**
     * File Handle
     * 
     * @var File Handle
     */
    protected $_file;
    
    /**
     * File name (full path relative to root -
     * relative paths will work but you might not be able to find the file...)
     * 
     * @var string
     */
    protected $_name;
    
    /**
     * File is valid and data saved
     * 
     * @var boolean
     */
    protected $_isValid = true;
    
    /**
     * error array
     * 
     * @var array
     */
    protected $_errors = array();
    
    /**
     * file open mode - default to 'read' (r)
     * do not use binary mode
     * 
     * @var string
     */
    protected $_openMode = 'r';
    
    /**
     * File stats - no idea what they mean, except $stats['size']
     * 
     * @var array
     */
    protected $_stats = array();

    /**
     * Constructor - 
     * 
     * checks file exists, (tries to open it, if it fails tries to create it) then 
     * saves (writes or appends) the data, if supplied.
     *
     * If file is empty, it writes a header row based on the data field names
     * but it does not validate or sort data - just writes everything.
     * 
     * @param string $filename The name of the file
     * @param array  $data     (optional) data to write
     * 
     * @return EAndL_Logger_CsvFile
     */
    
    public function __construct($filename, $data = array(), $fileParts = array())
    {
        $this->_baseDir = APPLICATION_PATH . '/../logs/';

        $this->makeFile($filename, $fileParts)
            ->saveData($data);

        return $this;
    }

    /**
     * makeFile
     * 
     * checks file exists, (tries to open it, if it fails tries to create it)
     * 
     * @param string $filename The name of the file
     * 
     * @return EAndL_Logger_CsvFile
     */
    protected function makeFile($filename = '', $fileParts = array())
    {
        //  construct the filename
        $path = '';
        If (empty($filename)){
            if (! empty($fileParts['baseDir'])) {
                $filename .= $fileParts['baseDir'];
            } else {
                $filename .= $this->_baseDir;
            }
            if (! empty($fileParts['folder'])) {
                $filename .= $fileParts['folder'];
            } else {
                $filename .= 'test/';
            }
            $path = $filename;
            if (! empty($fileParts['fileName'])) {
                $filename .= $fileParts['fileName'];
            } else {
                $filename .=  date('Y-m');
            }
            if (! empty($fileParts['day'])) {
                $filename .=  date('-d') . '-day.csv';
            } else {
                $filename .= '-month.csv';
            }
            
        }
        $this->_name = $filename;
        //  Try to read the file
        $this->_file = @fopen($filename, "r");

        //  If we cannot open it, try to create it
        if ($this->_file === false) {
            $this->_file = @fopen($filename, "w+");
            //  if still not there, try to create the path
            if ($this->_file === false && ! empty($path)) {
                mkdir($path);
                $this->_file = @fopen($filename, "w+");

            }
            //  giv up - return an error
            if ($this->_file === false) {
                $this->_isValid = false;
                $this->_errors[] = 'Cannot read or write to file: ' . $this->_name;
                return $this;
            }
        }
        $this->setStats();
        @fclose($this->_file);

        return $this;
    }

    /**
     * saveData - saves (writes or appends) the data, if supplied.
     *
     * If file is empty, it writes a header row based on the data field names
     * but it does not validate or sort data in any way.
     * 
     * @param array $data (optional) data to write
     * 
     * @return EAndL_Logger_CsvFile
     */
    public function saveData($data = array())
    {
        if ($this->getValid()) {
            //  if we have data, insert it.
            if (!empty($data)) {
                if ($this->getSize() < 1) { //  if new file, write header
                    $this->newFile($data);
                    
                } else { //  append data
                    $this->appendFile($data);
                }
            }
        }
        return $this;
    }

    /**
     * newFile - checks isValid then saves (writes) the data.
     *
     * Does not check if file is empty,
     * writes a header row based on the data field names
     * 
     * @param array $data (optional) data to write
     * 
     * @return EAndL_Logger_CsvFile
     */
    protected function newFile($data)
    {
        $csvHead = array_keys($data);
        
        $this->openFile('w');
        if ($this->getValid()) {
            // acquire an exclusive lock
            if (flock($this->_file, LOCK_EX)) {  
                fputcsv($this->_file, $csvHead);
                fputcsv($this->_file, $data);
                // flush output before releasing the lock
                fflush($this->_file);   
                // release the lock
                flock($this->_file, LOCK_UN);    

                $this->setStats();
            } else {
                $this->_isValid = false;
                $this->_errors[] = 'Cannot get lock on file: ' . $this->_name;
            }
        }
        // and close it outside the if
        @fclose($this->_file);    
        return $this;
    }

    /**
     * appendFile - checks is valid then saves (appends) the data. Does not validate 
     * or sort data.
     *
     * @param array $data (optional) data to write
     * 
     * @return EAndL_Logger_CsvFile
     */
    protected function appendFile($data = array())
    {
        $this->openFile('a');
        if ($this->getValid()) {
            // acquire an exclusive lock
            if (flock($this->_file, LOCK_EX)) {  
                fputcsv($this->_file, $data);
                // flush output before releasing the lock   
                fflush($this->_file);   
                // release the lock
                flock($this->_file, LOCK_UN);    
                $this->setStats();
            } else {
                $this->_isValid = false;
                $this->_errors[] = 'Cannot get lock on file: ' . $this->_name;
            }
        }
        // and close it outside the condition
        @fclose($this->_file);
        return $this;
    }


    /**
     * Returns the filesize in bytes
     * 
     * @return int
     */
    public function getSize()
    {
        if (empty($this->_stats)) {
            $this->setStats();
        }
        if ($this->getValid()) {
            return $this->_stats['size'];
        }
        return 0; // empty
    }

    /**
     * sets the file stats
     * 
     * @return EAndL_Logger_CsvFile
     */
    protected function setStats()
    {
        if ($this->getHandle() !== false) {
            $this->_stats = fstat($this->getHandle());
        }
        return $this;
    }

    /**
     * Returns the filename with path info
     * 
     * @return string $filename The filename
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Returns the file handle
     * 
     * @return File Pointer|boolean
     */
    protected function getHandle()
    {
        if ($this->getValid()) {
            return $this->_file;
        } else {
            return false;
        }
    }

    /**
     * returns isValid,
     *
     * @return int
     */
    protected function getValid()
    {
        return $this->_isValid;
    }

    /**
     * Rewinds file (return pointer to the start)
     * 
     * @param boolean $rewind Whether to reset the pointer
     * 
     * @return EAndL_Logger_CsvFile
     */
    protected function rewind($rewind)
    {
        if ($rewind) {
            if (! $this->getValid() || rewind($this->_file) === false) {
                $this->_isValid = false;
                $this->_errors[] = 'Could not reset the pointer in file: ' 
                    . $this->_name;
            }
        }
        return $this;
    }

    /**
     * Opens the file if it is closed.
     * 
     * @param string $mode Permissions to open the file with
     * 
     * @return EAndL_Logger_CsvFile
     */
    protected function openFile($mode = '')
    {
        //  Check file is valid
        if ($this->getValid()) {
            //  is file open (get pointer position)
            if (@ftell($this->_file) === false) { //  file not open
                //  get mode
                if (!$mode) {
                    $mode = $this->_openMode;
                } else {
                    if ($mode == 'w' || $mode == 'w+'
                        || $mode == 'c' || $mode == 'c+'
                    ) {
                        //  We do not want to re-open in the same mode
                        //  as it will empty the file
                        $this->_openMode = 'a';
                    } else {
                        $this->_openMode = $mode;
                    }
                }
                //  open file
                $this->_file = @fopen($this->_name, $mode);
            }
            if ($this->_file === false) {
                $this->_isValid = false;
                $this->_errors[] = 'Could not reopen file: ' . $this->_name;
            }
        }
        return $this;
    }

    /**
     * Reads a line from the file
     * 
     * @param boolean $rewind Rewind parameter to pass into openFile()
     *
     * @return string
     */
    public function readLine($rewind = false)
    {
        //  open the file if not already open
        $this->openFile('r')
            ->rewind($rewind);
        $data = array();
        if ($this->getValid()) {
            if (flock($this->_file, LOCK_SH)) {  // acquire a shared lock
                $data = fgetcsv($this->_file);
                flock($this->_file, LOCK_UN);    // release the lock
                $this->setStats();
            } else {
                $this->_isValid = false;
                $this->_errors[] = 'Cannot get lock on file: ' . $this->_name;
            }
        }
        return $data;
    }

    /**
     * Copy this file elsewhere - useful for testing. Copies a file to the given
     * destination.
     * 
     * @param string $destination The new file destination
     * 
     * @return string|EAndL_Logger_CsvFile
     */
    function copy($destination)
    {
        if ($this->getValid()) {
            if (strlen($destination) > 0) {
                //  check file is closed
                @fclose($this->_file);
                if (copy($this->_name, $destination)) {
                    return new self($destination);
                }
            } else {
                return 'No destination supplied';
            }
        } else {
            return 'Cannot copy invalid file';
        }
    }

    
    /**
     * Provides isvalid and errors functionality.
     * Key is not case sensitive
     * eg
     * var_dump $newLog->errorList;
     * 
     * Keys:
     * 
     * isValid - data is valid and no errors detected
     * isError - returns error count
     * errorList - returns errors array
     * 
     * filePath - returns file name
     * handle - the file handle, if it is open
     * 
     * anything else - throws exception
     *
     * @param string $key Value to get
     * 
     * @return mixed
     * @throws BadMethodCallException
     */
    public function __get($key)
    {
        $key = strtolower($key);
        switch ($key) {
            case 'isvalid':
            return $this->getValid();
                break;
            case 'iserror':
            return count($this->_errors);
                break;
            case 'errorlist':
            return $this->_errors;
                break;
            case 'filepath':
            return $this->_name;
                break;
            case 'handle':
            return $this->getHandle();
                break;
        }
        throw new BadMethodCallException('Undefined property.');
    }

    /**
     * print this file to a table (very crude)
     *
     * @return this
     */
    function printCsv()
    {
        if ($this->getValid()) {
            echo '<table>';
            //  get first line
            $data = $this->readLine(true);
            while ($data !== false) {
                echo '<tr>';
                foreach ($data as $field){
                    echo '<td>' . $field . '</td>';
                }
                echo '</tr>';
                $data = $this->readLine(false);
            }
            echo '</table>';
        } else {
            echo 'Cannot print invalid file';
        }
        return $this;
    }

    /**
     * Empty the file
     *
     * @return this
     */
    function flush()
    {
        if ($this->getValid()) {
            if (@ftell($this->_file) !== false) { //  file open
                @fclose($this->_file);
            }
            
            $this->_file = @fopen($this->_name, 'w');
            
            if ($this->_file === false) {
                $this->_isValid = false;
                $this->_errors[] = 'Could not purge file: ' . $this->_name;
            }

        } else {
            echo 'Cannot purge invalid file';
        }
        return $this;

    }

}

