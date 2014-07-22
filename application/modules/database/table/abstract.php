<?php

/**
 * abstract table class
 *
 * Provides the following methods:
 *
 * save
 * delete
 * fetch
 * magic getters and setters for passed in parameters
 *
 * Most table models can be created by passing the relevant
 * parameters in the DIC :
 *     $container['*****Table'] = $container->share( function ($c) {
 *         $table = new database_table_abstract(array(
 *         'db' => $c['db'],
 *         'table' => '*****',
 *         'fields' => $c['dbStruct']->getDbStruct('*****')->getFieldNames()
 *          ));
 *          return $table;
 *      });
 *
 *
 * A table can be shared by several objects, so it cannot retain data between requests
 *
 *
 * @author John
 *
 */
class database_table_abstract
{

    /**
     *
     * @var string table name - usually injected via DIC
     */
    protected $table;

    /**
     *   Database injected via DIC
     *
     * @var  database_Db
     */
    protected $db;

    /**
     *
     * @var array of field names - usually injected via DIC
     */
    protected $fields = array();

    /**
     *
     * @var array of passed in params - key / value pairs
     *          or where / order subarrays  (TODO)
     */
    private $params = array();


    //  Standard getters and setters

    public function getTable()
    {
        return $this->table;
    }

    public function setTable($table)
    {
        if (! is_string($table) || empty($table)){
            throw new RuntimeException('No table name supplied');
        }
        $this->table = $table;
        return $this;
    }

    protected function getDb()
    {
        if (!$this->db instanceof database_Db) {
            throw new RuntimeException('No database passed in to table constructor');
        }
        return $this->db;
    }

    public function setDb(database_Db $db)
    {
        $this->db = $db;
        return $this;
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function setFields($fields)
    {
        $this->fields = $fields;
        return $this;
    }

    private function getParam($name)
    {
        if (!isset($this->params[$name])){
            return null;
        }
        return $this->params[$name];
    }

    private function setParam($name, $value)
    {
        $this->params[$name] = $value;
        return $this;
    }

    private function clearParams()
    {
        $this->params = array();
    }
    /**
     * set date created if not already set.
     * This means a new record will always have a date created.
     *
     * Date modified is set on save,
     * so a new record will not have a date modified set
     *
     * @param string $date_created
     * @return \database_table_abstract
     */
    public function setDate_created($date_created)
    {
        if ($date_created){
            $this->_date_created = $date_created;
        } else {
            $this->_date_created = 'now';
        }
        return $this;
    }

    /**
     * constructor
     * use:
     *     $container['*****Table'] = $container->share( function ($c) {
     *         $table = new database_table_abstract(array(
     *         'db' => $c['db'],
     *         'table' => '*****',
     *         'fields' => $c['dbStruct']->getDbStruct('*****')->getFieldNames()
     *          ));
     *          return $table;
     *      });
     *
     * @param array $options Options sent to the constructor
     *                  usually set db, table and fields
     *                  but can be anything.
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
     * else  just set it as a parameter,
     *
     * @param string $name  name of field to set
     * @param string $value Value to set it to
     *
     * @return  database_table_abstract
     */
    public function __set($name, $value)
    {
        $method = 'set' . ucfirst($name);

        if (method_exists($this, $method)) {
            return ($this->$method($value));
        }

        $this->setParam($name, $value);
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

        return $this->getParam($name);
    }

    /**
     * Creates the setOptions method
     * Take the options passed and check if a setter exists
     *
     * @param array | abstract_model_abstract $options
     *
     * @return  database_table_abstract
     */
    public function setOptions($options = null)
    {
        //  Clear any existing data
        $this->clearParams();

        if ($options instanceof abstract_model_dataAbstract){
            $options = $options->toArray();
        }

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

    /**
     *  Single save method - but it won't work as it stands.
     * If we are using a slug as primary key, we need to pass in a stub
     * for the database to create a unique slug - so there will always be
     * a slug passed in even for an insert.  Any ideas?
     *
     * @todo - work out how to handle new slug
     *
     * @param array $options - field names and values to save
     */
    public function save( $options = null)
    {
        $this->setOptions($options);


        if ($this->getParam('ref') || $this->getParam('slug')) {
            return $this->update($options);
        } else {
            return $this->insert($options);
        }
    }

    protected function update(array $options = null)
    {
        $this->setOptions($options);

        $sql = 'UPDATE `' . $this->table;
        $fields = '` SET  `';
        $where = " WHERE `";

        foreach ($this->fields as $key ) {

            $value = $this->$key;

            //  do we need a ref?
            if ($key == 'ref'){
                //  Do we have a ref?
                if (! $value) {
                    throw new RuntimeException('No ref or ID passed in to save ' . $this->table);
                }
                //  ref goes in the WHERE clause
                $where .= "ref` = '" . $value . "';";
                continue;
            }

            //  do we need a slug?
            if ($key == 'slug'){
                //  Do we have a slug?
                if (! $value) {
                    throw new RuntimeException('No slug passed in to save ' . $this->table);
                }
                //  slug goes in the WHERE clause
                $where .= "slug` = '" . $value . "';";
                continue;
            }

            if (empty($value)){
                continue;
            }


            if (stripos($key, 'date') !== false) {
                if ($value == 'now') {
                    $value = 'now()';
                } else {
                    try {
                        $date = new \DateTime($value);
                        $value = $date->format('Y-m-d H:i:s');
                    } catch (Exception $exc) {
                        //  it may not be a valid date, but save it all the same
                    }
                }
            }
            $fields .= $key . "` =  '" . $value . "', `";

        }
        $fields = rtrim($fields, '`, ') . ' ';
        $sql .= $fields;
        $sql .= $where;

//        echo $sql; die;

        $this->getDb()->updateQuery($sql);
        return $this->toArray();
    }
    protected function insert(array $options = null)
    {
        $this->setOptions($options);

        //  do we need a slug?
        if (in_array('slug', $this->fields)){
            //  as this is an insert, assume there is no slug
            $this->slug = $this->getNextSlug();
        }
        $sql = 'INSERT INTO `' . $this->table;
        $fields = '` ( `';
        $values = " VALUES ( ";

        foreach ($this->fields as $key ) {

            $value = $this->$key;
            if (empty($value)){
                continue;
            }
            if ($key == 'ref'){
                //  We are inserting, so ignore any existing key.
                continue;
            }
            $fields .= $key . '`, `';


            if (stripos($key, 'date') !== false) {
                if ($value == 'now') {
                    $values .= ' now(), ';
                    continue;
                }
                try {
                    $date = new \DateTime($value);
                    $values .= " '" . $date->format('Y-m-d H:i:s') . "', ";
                    continue;
                } catch (Exception $exc) {
                    throw new InvalidArgumentException('Invalid date format to save record');
                }
            }

            $values .= " '" . $value . "', ";
        }
        $fields = rtrim($fields, '`');
        $fields = trim($fields, ', ') . ') ';
        $values = trim($values, ', ') . ');';
        $sql .= $fields;
        $sql .= $values;

//        echo $sql; die;

        $this->ref = ($this->getDb()->insertQuery($sql));
        return $this->toArray();
    }

    public function delete( array $where = array())
    {
        if (empty($where)){
            return false;
        }

        $sql = 'DELETE FROM `' . $this->table;
        $sql .= '` WHERE `';

        foreach ($where as $key => $value) {

            $sql .= $key . "` = '" . $value . "' AND `";
        }
        $sql = rtrim($sql, 'AND `');
        $sql = rtrim($sql, ', ') . ' ; ';

 //       echo $sql; die;
        try {
            $ret = ($this->getDb()->sqlQuery($sql));
        } catch (InvalidArgumentException $exc){
            throw new InvalidArgumentException('invalid argument passed to delete method', null, $esc);
        }

        return true;

    }


    /**
     *
     * @param array $options
     * @return array of arrays of fields => values
     */
    public function fetch( $options = null)
    {
        $this->setOptions($options);

        $sql = 'SELECT * FROM `' . $this->table . '`';

        if (is_array($options) && count($options) > 0) {
            $where = ' WHERE '
                    . $this->keyValuePairs($options)
                    ;
            $sql .= $where . ' ; ';
        }

   //      echo $sql;  die;

        return ($this->getDb()->selectQuery($sql)) ;
    }

    /**
     * Given a slug, go away and check it is unique.
     * For insert, this must be provided with appropriate starter value
     * @param string $slug
     * @return string next slug
     */
    public function getNextSlug($slug = 'slug')
    {
        return $this->getDb()->getNextSlug($slug, $this->table);
    }

    public function toArray()
    {

        $ret = array();

        foreach ($this->fields as $field) {
            $ret[$field] = $this->$field;
        }

        return $ret;

    }

    protected function keyValuePairs(array $options)
    {
        $newArray = array();
        foreach ($options as $key => $value) {
            $newArray[] = " `" . $key . "` = '" . $value . "'";
        }
        return implode(',', $newArray);

    }



}
