<?php

/**
 * abstract basket class
 *
 * A basket is simply an array of things, with minimal metadata,
 * generally representing a link table.
 *   The things should be able to manage themselves,
 * so we can hydrate the list by fetching each element, or we can
 * use the basket persistance layer to pull all the things
 * ready populated from the database.
 *
 * baskets need the ability to add and remove things.
 *
 * baskets will generally be stored in the DB as a many to many link table,
 * using  refs - slugs or IDs depending on how legacy the code is.
 * The link table may also hold metadata, eg the collection_resource table
 * holds data added, comment and rating.
 *
 * examples
 * Organisation is a container, holding a basket of users.  Metadata
 * (organisation name and address, etc) is stored in the organisation table
 * and the basket is stored in the org_user table
 *
 *
 * @author John
 */
class abstract_model_basket
{

    /**
     * @var array
     */
    protected $things = array();  //  array of abstract_model_abstract objects
                                  //  or refs waiting to be hydrated

    /**
     *
     * @var array
     */
    protected $metadata;

    /**
     * @var abstract_mapper_basket
     */
    protected $links;  //  link table mapper, injected, eg org_users


    public function save()
    {

        die ('abstract model basket save 50');
    }

    public function toArray ()
    {
        $ret = array();

        /** @var $thing abstract_model_abstract */
        foreach ($this->things as $thing){
            if (is_string($thing)){
                $ret[] = $thing;
            }elseif ($this instanceof abstract_model_abstract){
                $ret[] = $thing->toArray();
            } elseif ( null !== $thing->ref) {
                $ret[] = (string) $thing->ref;
            } else {
                throw new RuntimeException('invalid thing found in basket');
            }
        }
        return array($this->slug => $ret);
    }


    /**
     * constructor
     *
     * Assign mapper to metadata
     *
     * @param array $options Options sent to the constructor
     *
     * @return  database_table_abstract
     */
    public function __construct(array $options = array())
    {
        $this->setOptions($options);
    }

    /**
     * Magic setter - inherited
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


    public function addOne(abstract_model_abstract $thing)
    {
        $this->things[] = $thing;

    }


    public function hydrate()
    {
        foreach ($this->things as $thing) {

            if (! $thing instanceof abstract_model_abstract){
                throw new RuntimeException ('Invalid thing stored in a basket!');
            }
            //  check the thing is populated
            $thing->hydrate();
        }
    }

}
