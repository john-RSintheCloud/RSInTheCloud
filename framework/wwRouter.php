<?php
todo
namespace Framework; 

/**
 * Our router
 * 
 * This router is loaded in the DIC and injected into the application.
 * 
 * It extends the aura router factory 
 * and has an aura web request / response injected.
 * 
 * 
 */
class wwRouter extends \Aura\Router\RouterFactory
{
    /** @var null The Aura router */
    private $router = null;

    /**
     * "Start" the router
     * If a router is passed in, use it
     * otherwise create one from scratch.
     */
    
    /**
     * 
     * @param \Aura\Router\Router $router - passed in router
     */
    function __construct(\Aura\Router\Router $router = null)
    {
        if (empty($router)){
            $router = $this->newInstance();
            //  default routes
            //  expected routes are merlin or pdf
            //  everything else should be handled by .htaccess
            //  but we will handle public and engine just in case
            //  
            $router->add('merlin', '/merlin(/?)({controller}(/?)({action})?)?')
                    ->addValues([
                        'business' => 'WW',
                        'app' => 'merlin',
                        'controller' => 'index',
                        'action' => 'index',
                    ])
                    ->setWildcard('params');

            $router->add('fres', '/fres(/?)({app}(/?)({controller}(/?)({action})?)?)?')
                    ->addValues([
                        'business' => 'fres',
                        'app' => 'engine',
                        'controller' => 'index',
                        'action' => 'index',
                    ])
                    ->setWildcard('params');

            $router->add('pdf', '/pdf((/?){controller}((/?)({action})?)?)?')
                    ->addValues([
                        'business' => 'WW',
                        'app' => 'pdf',
                        'controller' => 'index',
                        'action' => 'index',
                    ])
                    ->setWildcard('params');

            $router->add('default', '')
                    ->addValues([ 
                        'business' => 'WW',
                        'app' => 'engine',
                        'controller' => 'index',
                        'action' => 'index',
                        ])
                    ->setWildcard('params');
        }
        
        $this->router = $router;
        
        return $this;
    }
    
    /**
     * 
     * @param string $path (allow DI)
     * @return \wwRoute
     */
    public function getRoute($path = '')
    {
        //  path is injected
        if (empty($path)){
            throw new RuntimeException('No path injected into get route.');
            // get the incoming request URL path
//           $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        }

        // get the route based on the path and server
        $route = $this->router->match($path, $_SERVER);

 //       echo('<pre>'); var_dump($route); die('wwr70');
        
        //  we don't really want all that crap returning,
        //  so use our own route model
        
        return  new wwRoute([
            'name' => $route->name,
            'app' => $route->params['app'],
            'business' => $route->params['business'],
            'controller' => $route->params['controller'],
            'action' => $route->params['action'],
            'params' => $route->params['params'],
            'path' => $path,
            
        ]);
        
    }
    
    /**
     * Having got a route, make sure the controller exists and call it.
     * 
     * @param wwRoute $route
     * 
     * @return string name of controller in DIC
     */
    public function findController(wwRoute $route)
    {
        
        //  we use a naming convention to pull the correct controller and view
        //  out of the DIC
        
        return 'C' . ucfirst($route->app) . ucfirst($route->controller);
    }
    /**
     * Having got a route, make sure the view exists and call it.
     * 
     * @param wwRoute $route
     * 
     * @return string name of controller in DIC
     */
    public function findview(wwRoute $route)
    {
        
        //  we use a naming convention to pull the correct controller and view
        //  out of the DIC
        
        return 'V' . ucfirst($route->app) . ucfirst($route->controller) . ucfirst($route->action);
    }
}
