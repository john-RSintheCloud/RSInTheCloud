<?php

/**
 * abstract model class
 *
 * Simple models contain data and metadata.  Most data is simple - strings
 * or numbers written to / read from the database.  For this,
 * the functionality supplied in this abstract is sufficient.
 *
 * This abstract generates a field list from the mapper and adds some default
 *  values.  Any additional special requirements should be set by extending
 * this abstract.
 *
 *
 * @author John
 */
class abstract_model_dataAbstract
{

    /**
     * Field list pulled from mapper
     *
     * @var array
     */
    protected $fields = array();

    /**
     *  default values
     *   Overload in the concrete model
     *
     * @var array
     */
    protected $defaults = array(
        //  common fields - all models should have these
        'dateCreated' => 'now',
        'dateModified' => 'now'
        );

    /**
     *  persistance layer - injected
     *
     * @var database_mapper_abstract
     */
    protected $mapper;

    /**
     *
     * @var array of data
     */
    protected $data = array();


    /**
     *
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     *
     * @return array
     */
    public function getDefaults()
    {
        return $this->defaults;
    }

    /**
     *
     * @return database_mapper_abstract
     */
    public function getMapper()
    {
        return $this->mapper;
    }

    /**
     *
     * @return array of data elements
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Save a data element
     *
     * @param string $name of data element
     * @param mixed $data element to add
     * @return \abstract_model_dataAbstract
     */
    public function setData($name, $data)
    {
        $this->data[$name] = $data;
        return $this;
    }


    /**
     *
     * @param array $fields
     * @return \abstract_model_dataAbstract
     */
    public function setFields(array $fields = array())
    {
        $this->fields = $fields;
        foreach ($this->fields as $field) {
            if (isset($this->defaults[$field])){
                $this->__set($field, $this->defaults[$field]);
            } else {
                $this->__set($field, '');

            }
        }
        return $this;
    }

    /**
     *
     * @param array $defaults
     * @return \abstract_model_dataAbstract
     */
    public function setDefaults(array $defaults = array())
    {
        $this->defaults = $defaults;
        return $this;
    }

    /**
     *
     * @param database_mapper_abstract $mapper
     * @return \abstract_model_dataAbstract
     */
    public function setMapper(database_mapper_abstract $mapper)
    {
        $this->mapper = $mapper;
        $this->setFields($this->mapper->getFields());
        return $this;
    }

    function __construct(database_mapper_abstract $mapper)
    {
        $this->setMapper($mapper);
    }

    //  Use magic getters and setters

        /**
     * Magic setter
     *
     * If there is a setter, use it.
     * else if it is in the field list, just set it
     * else ignore it
     *
     * @param string $name  name of field to set
     * @param string $value Value to set it to
     *
     * @return  abstract_model_dataAbstract
     */
    public function __set($name, $value)
    {
        $method = 'set' . ucfirst($name);

        if (method_exists($this, $method)) {
            return ($this->$method($value));
        }

        if (in_array($name, $this->fields)) {
            $this->data[$name] = $value;
        }
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

        if (isset($this->data[$name])) {
            return $this->data[$name];
        }
        return null;
//        throw new InvalidArgumentException('Invalid get property: ' . $name);
    }

    /**
     * setOptions
     *
     * @param array $options Take the options passed and check if a setter exists
     *
     * @return  database_table_abstract
     */
    public function setOptions(array $options = [])
    {
        if (empty($options)) {
            return $this;
        }

        foreach ($options as $key => $value) {

            try {
                $this->$key = $value;
            } catch (Exception $exc) {
                //  do nothing
            }
        }
        return $this;
    }

    /**
     * Merge new values from array -
     * if key is already set ignore array value
     *
     * @param array $options
     * @return \abstract_model_dataAbstract
     */
    public function merge(array $options = null)
    {

        if (!is_array($options)) {
            return $this;
        }

        foreach ($options as $key => $value) {

            if($this->$key ) {  //  magic getter
                continue;
            }
            try {
                $this->$key = $value;
            } catch (Exception $exc) {
                //  if $this does not have $name, ignore it.
            }
        }

        return $this;
    }

    public function toArray()
    {

        return $this->data;
    }

    public function toLegacyArray()
    {

        return $this->mapper->toLegacyArray($this);
    }

    //  Persistance Layer

    public function fetchOne(array $options = array())
    {
        $this->setOptions($this->mapper->fetchOne($options));
        return $this;
    }

        /**
     *  @return mixed record saved
     */
    public function save()
    {
        $ret = $this->getMapper()->save($this);
        $this->setOptions($ret);
        return $this;
    }

    /**
     * Delete this record by ref if available, or by whatever options are set.
     *
     * @param array $options
     * @return boolean
     */
    public function delete()
    {
        $ref = $this->ref;

        if ( ! empty($ref)){
            return $this->getMapper()->delete(array('ref' => $ref));
        }

        $options = array();
        //  Find any non-empty keys
        foreach ($this->data as $key => $value) {
            if (! empty ($value)){
                $options[$key] = $value;
            }

        }
        if (empty($options)){
            return false;
        }

        return $this->getMapper()->delete($options);
    }

    protected function hydrate()
    {
        if ($this->slug || $this->ref) {
            $this->getDbClass()->hydrate($this);
        }
        return $this;
    }
}
