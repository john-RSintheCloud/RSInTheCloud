<?php

/**
 * This is the "base controller class". All other "real" controllers extend this class.
 */
class Controller
{
    /**
     * @var null DIC
     */
    public $container = null;

    /**
     * Whenever a controller is created, open the DI container. 
     * The idea is to have ONE container and pull all models out of it.
     */
    function __construct()
    {
        $this->openContainer();
    }

    /**
     * Open the DIC from application/config/config.php
     */
    private function openContainer()
    {
        $this->container = new Pimple ;
    }

    /**
     * Load the model with the given name.
     * loadModel("SongModel") would include models/songmodel.php and create the object in the controller, like this:
     * $songs_model = $this->loadModel('SongsModel');
     * Note that the model class name is written in "CamelCase", the model's filename is the same in lowercase letters
     * @param string $model_name The name of the model
     * @return object model
     */
    public function loadModel($model_name)
    {
        require 'application/models/' . strtolower($model_name) . '.php';
        // return new model (and pass the database connection to the model)
        return new $model_name($this->db);
    }
}
