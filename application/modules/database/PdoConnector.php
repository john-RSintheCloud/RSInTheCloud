<?php

/**
 * The PDO connector we are using - this one is mySQL
 *
 * @author John
 */
class database_PdoConnector
{

    private $_config;


    protected $connection;

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
        return $this->_config['host'];
    }

    public function getDbName()
    {
        return $this->_config['dbName'];
    }

    public function getConnection()
    {
        //  Late Binding
        if (!$this->connection instanceof PDO){
            $conn = new PDO(
                'mysql:host=' . $this->getHost() . '; dbname=' . $this->getDbName(),
                $this->getUsername(), $this->getPassword());
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->setConnection($conn);
        }
        return $this->connection;
    }

    public function setUsername($username)
    {
        $this->_config['username'] = $username;
    }

    public function setPassword($password)
    {
        $this->_config['password'] = $password;
    }

    public function setHost($host)
    {
        $this->_config['host'] = $host;
    }

    public function setDbName($dbName)
    {
        $this->_config['dbName'] = $dbName;
    }

    public function setConnection(PDO $connection)
    {
        $this->connection = $connection;
    }

    function __construct($config )
    {
        $this->setConfig($config);

    }

    public function setConfig( array $config)
    {
        $this->_config = $config;
    }


}
