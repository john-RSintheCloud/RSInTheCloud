<?php

/**
 * The PDO connector we are using - this one is mySQL
 * 
 * Created in the DIC, has a config injected
 * 
 * DIC uses this to inject into the pdo connector 
 *
 * @author John
 */
class database_PdoConfig
{

    /**
     *
     * @var 
     */
    private $config ;

    public function __construct(abstract_model_arrayAbstract $config )
    {
        $this->config = $config;

        return $this;
    }

    public function getUsername()
    {
        return $this->config->username;
    }

    public function getPassword()
    {
        return $this->config->password;
    }

    protected function getHost()
    {
        return $this->config->server;
    }

    protected function getDbName()
    {
        return $this->config->db;
    }

    public function getConfig()
    {
        return (
           'mysql:host=' . $this->getHost() 
            . '; dbname=' . $this->getDbName());
    }


}
