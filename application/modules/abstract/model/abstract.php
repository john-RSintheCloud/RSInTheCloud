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
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->setOptions($options);
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
    public function setOptions(array $options = [])
    {
        if (empty($options)) {
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

            if($this->$key ) {  //  magic getter
                continue;
            }
            
            $this->$key = $value;
            
        }
        return $this;
    }

}
