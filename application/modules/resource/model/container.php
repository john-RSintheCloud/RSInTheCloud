<?php

/**
 * abstract container class
 *
 * A container is simply an array of things.  The things should be able to manage themselves,
 * so we can hydrate the list by fetching each element, or we can
 * use the container persistance layer to pull all the things ready populated from the database.
 *
 * Containers need the ability to add and remove things.
 *
 * Containers are stored in the DB as a many to many link table, using slugs.
 *
 * @author John
 */
class resource_model_container
{

    protected $dbClass; //  persistance layer

    protected $things = array();  //  array of abstract models or slugs
    protected $_slug ;

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


    public function addOne($thing)
    {
        if ($thing instanceof resource_model_abstract){
            $slug = $thing->slug;
        } elseif (is_string($thing)){
            $slug = $thing;
        } else {
            throw new RuntimeException ('Invalid thing passed to store in a container!');
        }
    }


    public function hydrate()
    {
        foreach ($this->things as $thing) {

            if (is_string($thing)){
                $thing = new resource_model_abstract (array('slug' => $thing));

            } elseif (! $thing instanceof resource_model_abstract){
                throw new RuntimeException ('Invalid thing stored in a container!');
            }

            $thing->hydrate();
        }
    }

}
