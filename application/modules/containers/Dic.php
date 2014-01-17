<?php


/**
 * pimple DI container class
 *
 * @author John
 */
require_once 'pimple/lib/Pimple.php';

class containers_Dic extends Pimple
{

    public function init()
    {

        /**
         * Configuration
         */
        $this['config'] = $this->share( function ($c) {

            return new config_config();
        });

        /**
         * Database Connector
         */
        $this['PdoConnector'] = $this->share( function ($c) {
            $conn = new database_PdoConnector(
                $c['config']->getDbConfig()
                );
            return $conn->getConnection();
        });

        /**
         * DB Query Runner
         */
        $this['db'] = $this->share( function ($c) {
            return new database_Db($c['PdoConnector']);

        });


    }

}