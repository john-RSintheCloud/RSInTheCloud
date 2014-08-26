<?php

/**
 * abstract model class
 *
 * for non-database related classes
 * 
 * This abstract does not impose any constraints on the data to be stored - 
 * the magic setter allows anything to be set
 *
 * @author John
 */
class abstract_model_abstract
{

    /**
     * constructor
     *
     * @param array $options Options sent to the constructor
     *
     * @return  this
     */
    public function __construct($options = [])
    {
        
        return $this->setOptions($options);
    }


    //  Use magic getters and setters

        /**
     * Magic setter
     *
     * If there is a setter, use it.
     * else just set it
     *
     * @param string $name  name of field to set
     * @param string $value Value to set it to
     *
     * @return  abstract_model_abstract
     */
    public function __set($name, $value)
    {
        //  handle the possibility of a variable being called 'options'
        //  otherwise $this->options will call setOptions
        if (ucfirst($name) == 'Options'){
            $name = '__Options';
        }
        $method = 'set' . ucfirst($name);

        if (method_exists($this, $method)) {
            return ($this->$method($value));
        }

        $this->$name = $value;
        return $this;
    }


     /**
     *
     * @param string $name field / info to get
     *
     * @return  mixed - value of item being got
     */
    public function __get($name)
    {
        //  handle the possibility of a variable being called 'options'
        if (ucfirst($name) == 'Options'){
            $name = '__Options';
        }
        
        $method = 'get' . ucfirst($name);

        if (method_exists($this, $method)) {
            return $this->$method();
        }

        if (isset($this->$name)) {
            return $this->$name;
        }
        return null;
    }

    /**
     * setOptions using magic setter
     *
     * @param array $options Take the options passed and set them
     *
     * @return  database_table_abstract
     */
    public function setOptions($options = null)
    {
        // we are expecting an array, so see if we can get one
        if(!is_array($options)) {
            $options = $options->toArray();
        }
        
        if (!is_array($options)) {
            return $this;
        }
        foreach ($options as $key => $value) {
            $this->$key = $value;
        }
        return $this;
    }

    /**
     * Merge new values from array -
     * if key is already set ignore array value
     *
     * @param array $options
     * @return \abstract_model_abstract
     */
    public function merge(array $options = [])
    {

        if (empty($options)) {
            return $this;
        }

        foreach ($options as $key => $value) {

            if(isset($this->$key) ) { 
                continue;
            }
            
            $this->$key = $value;
            
        }
        return $this;
    }

}
