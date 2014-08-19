<?php
todo
namespace Framework; 

use Merlin\application\controller\Home;


/**
 * OK, this is the bit that does it all.
 * 
 * The application consists of a DIC, web (request / response) object,
 * router, controller, models 
 * and a view / layout / partials set to output.
 * 
 * The philosophical question is do you have the application inside the container
 * or do you declare the container inside the application.
 * This framework is based on having modular configuration / applications, so 
 * we hold the base container inside the application and extend it in the modules.
 * 
 */
class Application
{
    /** @var null The container */
    private $container = null;
    
    /**
     * "Start" the application:
     * Create the container
     * start the request object and pass to the router
     * and load the controller
     * 
     * This is largely handled by the DIC
     */
    
    
    public function __construct()
    {
        //  create container
        $container = new Dic();
        
        //  get the route - this triggers getting the request object.
        
        $route = $container['route'];
        $controller = $container['controller'];
        $controller->index();

 //       echo('<pre>'); var_dump($route); die('app38');
        
    }
}
