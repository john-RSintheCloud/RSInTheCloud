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
         * Bucket for Plupload
         */
        $this['Bucket'] = $this->share( function ($c) {
            return new s3_model_bucket();

        });



    }

}