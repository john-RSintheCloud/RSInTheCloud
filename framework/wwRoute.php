<?php
todo
namespace Framework; 

use RuntimeException;

/**
 * Our route model - used to pass the route out of the router.
 * It doesn't actually do a lot at this stage!
 * 
 */
class wwRoute extends wwArrayAbstract
{
    
    public function __construct(array $options = array())
    {
        parent::__construct($options);
        //  Check our app
        
        //  we have 3 apps - engine, merlin and pdf
        //  engine handles all UI and ajax traffic
        //  merlin is admin
        //  pdf delivers files stored in mongo
        //  anything else is an exception.
        
//        var_dump($this->app); die;
        switch ($this->app) {
            case 'engine':
                //  the engine is not yet part of the framework.
                //  traffic for the engine should be redirected via .htaccess
                //  so for now throw an exception
                throw new RuntimeException('looks like .htaccess is failing - url was'
                        . $route->path);

                break;

            case 'merlin':
                //  The controller for Merlin is in the merlin folder

            case 'pdf':
                //  The pdf controller pulls files from Mongo and 
                //  delivers them to the browser
                break;

            default:
                //  At present we only handle these, so anything else is an error
                throw new RuntimeException('invalid route - url was'
                        . $route->path);
                break;
        }
        
        return $this;
    }
}
