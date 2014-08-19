<?php

namespace Framework; 

/**
 * WW Abstract class
 * Constructor
 * Magic getters and setters,
 * setOptions
 * Container
 * @author JohnB
 */
class wwAbstract
{
    
    /**
     * constructor
     *
     * @param array $options Options sent to the constructor
     *
     * @return  pdfView
     */
    public function __construct($options = array())
    {
        // we are expecting an array, so see if we can get one
        if(!is_array($options)) $options = $options->toArray();
        
        if(!is_array($options)) return $this;
        
        return $this->setOptions($options);
    }

    /**
     * Magic setter 
     *
     * @param string $name  name of field to set
     * @param string $value Value to set it to
     *
     * @throws  Exception
     * @return  pdfView
     */
    public function __set($name, $value)
    {
        //  handle the possibility of a variable being called 'options'
        //  otherwise $this->options will call setOptions
        if (ucfirst($name) == 'Options'){
            $name = 'wwOptions';
        }
        $method = 'set' . ucfirst($name);

        if (method_exists($this, $method)) {
            return ($this->$method($value));
        }

        $this->$name = $value;
        return $this;
    }

    /**
     * magic getter
     *
     * @param string $name field / info to get
     *
     * @return  mixed - value of item being got
     */
    public function __get($name)
    {
        //  handle the possibility of a variable being called 'options'
        if (ucfirst($name) == 'Options'){
            $name = 'wwOptions';
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
     * setOptions
     *
     * @param array $options Take the options passed and check if a setter exists
     *
     * @return  pdfView
     */
    public function setOptions($options = null)
    {
        if (!is_array($options)) {
            return $this;
        }

        foreach ($options as $key => $value) {

            try {
                $this->__set($key, $value);
            } catch (Exception $exc) {
                //  do nothing
            }
        }
        return $this;
    }

}
