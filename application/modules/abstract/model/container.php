<?php

/**
 * abstract container class
 *
 * A container is simply an array of things, with some metadata.
 *   The things should be able to manage themselves,
 * so we can hydrate the list by fetching each element, or we can
 * use the container persistance layer to pull all the things
 * ready populated from the database.
 *
 * Containers need the ability to add and remove things.
 *
 * Containers allow us to abstract out the idea of a one to many or many to many relationship, 
 * and it will generally be stored in the DB as a many to many link table.
 * using slugs or refs
 *
 * examples
 * Organisation has a container of users with metadata (organisation name and address, etc)
 * stored in the organisation table and user links stored in the org_user table.
 *
 *
 * @author John
 */
class abstract_model_container
{

    /**
     * @var array
     */
    protected $things = array();  //  array of abstract_model_abstract objects
                                  //  or refs waiting to be hydrated

    /**
     *
     * @var abstract_model_dataAbstract
     */
    protected $metadata;

    /**
     * @var database_table_abstract
     */
    protected $links;  //  link table, injected, eg org_users

    protected $linkField; //  field name of link table, eg org_slug
    protected $multiField;  //  field name for many side, eg user_ref


    public function save()
    {

        //  three parts to saving this.
        //  firstly save the metadata
        $this->metadata->save();
        //  then delete any links which are not in the linktable:
        $linkArray = $this->links->fetch(array(
            $this->linkField => $this->metadata->ref
        ));

        var_dump ($linkArray);
        die ('abstract model container save 58');
    }

    public function toArray ()
    {
        $ret = array();

        /** @var $thing abstract_model_abstract */
        foreach ($this->things as $thing){
            if (is_string($thing)){
                $ret[] = $thing;
            }elseif ($this instanceof abstract_model_dataAbstract){
                $ret[] = $thing->toArray();
            } elseif ( null !== $thing->ref) {
                $ret[] = (string) $thing->ref;
            } else {
                throw new RuntimeException('invalid thing found in container');
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
    public function __construct(array $options = null)
    {
        return parent::$this;
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


    public function addOne(abstract_model_dataAbstract $thing)
    {
        $this->things[] = $thing;

    }


    public function hydrate()
    {
        foreach ($this->things as $thing) {

            if (! $thing instanceof abstract_model_dataAbstract){
                throw new RuntimeException ('Invalid thing stored in a container!');
            }
            //  check the thing is populated
            $thing->hydrate();
        }
    }

}
