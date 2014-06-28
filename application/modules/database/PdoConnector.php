<?php

/**
 * The PDO connector we are using - this one is mySQL
 *
 * @author John
 */
class database_PdoConnector
{

    /**
     *
     * @var array
     */
    private $_config = array();

    /**
     *
     * @var PDO
     */
    protected $_connection;


    public function __construct($config = array() )
    {
        if (is_array($config)){
            $this->setConfig($config);
        }
        return $this;
    }

    public function setConfig( array $config)
    {
        $this->_config = $config;
        return $this;
    }

    public function getUsername()
    {
        return $this->_config['username'];
    }

    public function getPassword()
    {
        return $this->_config['password'];
    }

    public function getHost()
    {
        return $this->_config['server'];
    }

    public function getDbName()
    {
        return $this->_config['db'];
    }

    public function getConnection()
    {
//        var_dump($this->_config); die;
        //  Late Binding
        if (!$this->_connection instanceof PDO){
            $conn = new PDO(
                'mysql:host=' . $this->getHost() . '; dbname=' . $this->getDbName(),
                $this->getUsername(), $this->getPassword());
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->setConnection($conn);
        }
        return $this->_connection;
    }

    public function setUsername($username)
    {
        $this->_config['username'] = $username;
        return $this;
    }

    public function setPassword($password)
    {
        $this->_config['password'] = $password;
        return $this;
    }

    public function setHost($host)
    {
        $this->_config['server'] = $host;
        return $this;
    }

    public function setDbName($dbName)
    {
        $this->_config['db'] = $dbName;
        return $this;
    }

    public function setConnection(PDO $connection)
    {
        $this->_connection = $connection;
        return $this;
    }


}
