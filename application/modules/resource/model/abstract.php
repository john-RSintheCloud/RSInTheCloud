<?php

/**
 * abstract model class
 *
 * Every model should be able to persist - it must have crud functionality built in.
 *
 * This generally means it knows how to pass values into its table class.
 *
 * @author John
 */
class resource_model_abstract
{

    protected $dbClass; //  persistance layer

    public function getDbClass()
    {
        if (!$this->dbClass instanceof database_table_abstract) {
            throw new RuntimeException('No persistance layer passed in to model constructor');
        }
        return $this->dbClass;
    }

    public function setDbClass(database_table_abstract $db)
    {
        $this->dbClass = $db;
        return $this;
    }



    //  common fields - all models should have these
    protected $_date_created = 'now'; //  datetime NOT NULL,
    protected $_date_modified = 'now'; //  datetime NOT NULL,

    //  Use magic getters and setters

     /**
     * constructor
     *
     * @param array $options Options sent to the constructor
     *
     * @return  database_table_abstract
     */
    public function __construct(array $options = null)
    {
        if (is_array($options)) {
            $this->setOptions($options);
        }

        return $this;
    }

    /**
     * Magic setter
     *
     * If there is a setter, use it.
     * else if it is a parameter, just set it
     * else return an error
     *
     * @param string $name  name of field to set
     * @param string $value Value to set it to
     *
     * @throws  Exception
     * @return  database_table_abstract
     */
    public function __set($name, $value)
    {
        $method = 'set' . ucfirst($name);

        if (method_exists($this, $method)) {
            return ($this->$method($value));
        }
        $prop = '_' . $name;
        if (isset($this->$prop)) {
            $this->$prop = $value;
        } else {
            throw new InvalidArgumentException('Invalid set value');
        }
        return $this;
    }

    /**
     * magic getter
     *
     * @param string $name field / info to get
     *
     * @throws  Exception
     * @return  mixed - value of item being got
     */
    public function __get($name)
    {
        $method = 'get' . ucfirst($name);

        if (method_exists($this, $method)) {
            return $this->$method();
        }

        $prop = '_' . $name;
        if (isset($this->$prop)) {
            return $this->$prop;
        }
        throw new InvalidArgumentException('Invalid get property');
    }

    /**
     * setOptions
     *
     * @param array $options Take the options passed and check if a setter exists
     *
     * @return  database_table_abstract
     */
    public function setOptions(array $options = null)
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
