<?php

namespace Framework;

use MongoId;
use RuntimeException;

/**
 * An abstract class for accessing Mongo data
 * without enforcing a schema.
 * 
 * This class holds the 'raw' data in a separate array
 * so it can be saved into Mongo.
 *
 * @author JohnB
 */
class dbDataAbstract extends wwArrayAbstract
{
    
    protected $rawData;

    protected $recordId;
    protected $edited = false;
    protected $newRecord = false;


    /**
     * set by validate, shows dataset is valid or 
     * $errors contains details of error(s)
     * @var boolean 
     */
    protected $isValid = false;

    /**
     * If isValid = false, then this should contain one or more 
     * status/message pairs.
     * @var array of arrays 
     */
    protected $errors;
    
    /**
     * Error level constants - 1 to 9, 9 being failure
     */
    const ERROR_FAIL = '9';
    const ERROR_WARN = '6';
    const ERROR_INCOMPLETE = '4';
    
    
    /**
     * constructor
     * accepts an array or wwArrayAbstract which is stored in rawData
     *
     * @param array|wwArrayAbstract $options Options sent to the constructor
     *
     * @return  dbDataAbstract
     */
    public function __construct($options = array())
    {
        return $this->setRawData($options);
    }

    public function setRawData($options = array())
    {
        if ($options instanceof wwArrayAbstract){
            $this->rawData = $options;
            $this->validate();  
        } elseif(is_array($options)) {
            $this->rawData = new wwArrayAbstract($options);
            $this->validate();
        } else {
//            throw new \Exception('invalid data supplied');
        }
        
        
        return $this;
        
    }

    public function getId()
    {
        if (!$this->recordId) {
            throw new RuntimeException('No record ID supplied or ID not validated');
        }
        return $this->recordId;
    }


    /**
     * Validate data, regenerate compound values.
     * 
     * This method runs multiple times, so it must return consistent data
     * 
     * @return boolean isValid
     */
    public function validate()
    {
        //  set local copies of compound fields
        //  so magic getters work

        //  We must get the record ID as minimum, if it exists.
        //  first check we have raw data
        if(empty($this->rawData)){
            throw new RuntimeException('No data passed in to validate the record');
        }
        $this->recordId = $this->rawData->_id;
        if ($this->recordId instanceof MongoId){
            $this->newRecord = false;
        } else {
            $this->newRecord = true;
            $this->recordId = $this->rawData->_id = new MongoId();
        }

        $this->isValid = false;
        $this->errors[self::ERROR_FAIL] = 'You need to write a validator';
        
        return $this->isValid;
    }

}
