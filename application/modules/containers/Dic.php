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

        $this['basePath'] = $this->share( function ($c) {

            return realpath(dirname(__FILE__) . "/../../../");
        });

        
        /**
         * Configuration
         */
        $this['config'] = $this->share( function ($c) {

            return new config_config([
                'genericConfigFilePath' => '_config/generic.config.php',
                'configFilePath' => '_config/config.php'
            ]);
        });

        /**
         * Bucket for Plupload - shouldn't be in here!
         */
        $this['Bucket'] = $this->share( function ($c) {
            return new s3_model_bucket(['config' => $c['config']]);

        });

        /**
         * simple timer - uses an array to hold multiple start times
         */
        $this['timer'] = $this->share( function ($c) {
            return new timer;

        });


    }

}