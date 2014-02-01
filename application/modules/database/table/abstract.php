<?php

/**
 * abstract table class
 *
 * @author John
 */
class database_table_abstract
{

    protected $table;

    /**
     *   Database injected via DIC
     *
     * @var  database_Db
     */
    protected $db;
    //  common fields - all tables should have these
    protected $_date_created = 'now'; //  datetime NOT NULL,
    protected $_date_modified = 'now'; //  datetime DEFAULT NULL,

    //  Standard getters and setters

    final public function getTable()
    {
        return $this->table;
    }

    final public function setTable($table)
    {
        throw new RuntimeException('You cannot set a table name');
        return $this;
    }

    public function getDb()
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

    public function getDate_created()
    {
        return $this->_date_created;
    }

    public function setDate_created($date_created)
    {
        $this->_date_created = $date_created;
        return $this;
    }

    public function getDate_modified()
    {
        return $this->_date_modified;
    }

    public function setDate_modified($date_modified)
    {
        $this->_date_modified = $date_modified;
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
//        } else {
//            throw new InvalidArgumentException('Invalid get property');
        }
//        return false;
        throw new InvalidArgumentException('Invalid get property');
    }

    /**
     * Creates the setOptions helper
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

    /**
     *
     * @param array $options
     */
    public function save(array $options = null)
    {
        if ($this->_ref) {
            return $this->update($options);
        } else {
            return $this->insert($options);
        }
    }

    protected function insert(array $options = null)
    {
        $this->setOptions($options);
        //  Do we have a slug?
        if ($this->slug) {
            $this->slug = $this->getNextSlug($this->slug);
        }
        $sql = 'INSERT INTO `' . $this->table;
        $fields = '` ( ';
        $values = " VALUES ( ";

        foreach (get_object_vars($this) as $key => $value) {

            if (substr($key, 0, 1) != '_')
                continue;
            if ($key == '_ref')
                continue;

            $fields .= substr($key, 1) . ', ';


            if (substr($key, 0, 5) == '_date') {
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
        $fields = trim($fields, ', ') . ') ';
        $values = trim($values, ', ') . ');';
        $sql .= $fields;
        $sql .= $values;

        echo $sql;

        return ($this->getDb()->sqlQuery($sql));
    }

    protected function delete(array $options = null)
    {
        $this->setOptions($options);

        $sql = 'DELETE FROM `' . $this->table;
        $where = '` WHERE ';

        foreach ($options as $key => $value) {

            $where .= $key . " = '" . $value . "', ";
        }
        $where = trim($where, ', ') . ' ; ';
        $sql .= $where;

        //       echo $sql;

        return ($this->getDb()->sqlQuery($sql));
    }

    public function fetch(array $options = null)
    {
        $this->setOptions($options);

        $sql = 'SELECT * FROM `' . $this->table . '`';

        if (is_array($options && count($options) > 0)) {
            $where = ' WHERE ';

            foreach ($options as $key => $value) {

                $where .= $key . " = '" . $value . "', ";
            }
            $where = trim($where, ', ') . ' ; ';
            $sql .= $where;
        }

        //       echo $sql;

        if ($this->getDb()->sqlQuery($sql)) {
            return $this->getDb()->fetchAll();
        }

        return array();
    }

    public function getNextSlug($slug)
    {
        return $this->getDb()->getNextSlug($slug, $this->table);
    }

}
