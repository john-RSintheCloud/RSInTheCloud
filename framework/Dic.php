<?php
not currently in use
v1.1 - update before use

/**
 * pimple DI container class for WW framework
 *
 * @author John
 */

namespace Framework;

use Aura\Web\WebFactory;
use email;
use email_validation_template;
use emailConfig;
use Merlin\application\controller\Home;
use MongoClient;
use MongoCollection;
use MongoConnectionException;
use MongoDB;
use PHPMailer;
use Pimple;
use RuntimeException;
use Shared\Pdf\pdfData;
use WkHtPd;



class Dic extends Pimple
{

    public function __construct()
    {
        $this->init();
    }

    /**
     * Build the application
     */
    public function init()
    {

        // ********************  FRAMEWORK
        //  This container is held in the application, 
        //  so there is no application object in here
        
        
        //  ********** Request and response objects
        //  Aura request and response objects wrap the html/http processing
        $this['webFactory'] = $this->share(function ($c) {
            return new WebFactory($GLOBALS);
        });
        $this['request'] = $this->share(function ($c) {
            return $c['webFactory']->newRequest();
        });
        $this['response'] = $this->share(function ($c) {
            return $c['webFactory']->newResponse();
        });
        
        
        //  ********** Router
        //  a configured router is supplied per application
        //  this is default
        //  override this in the application DI
        $this['router'] = $this->share(function ($c) {
            return new wwRouter();
        });
        
        //  the route is generated from the router.
        $this['route'] = $this->share(function ($c) {
            $path = $c['request']->url->get(PHP_URL_PATH);
            return $c['router']->getRoute($path);
        });
        
        //  ********** Dispatch, controller and view
        //  The router identifies a controller and instantiates it here
        //  The view is also identified through the route info.
        $this['controller'] = $this->share(function ($c) {
            $contName = $c['router']->findController($c['route']);
            return $c[$contName];
        });
        
        //  the route is generated from the router.
        $this['view'] = $this->share(function ($c) {
            $viewName = $c['router']->findView($c['route']);
            return $c[$viewName];
        });
        
        // We use a naming convention to identify which controller and view to pick up.
        $this['CMerlinIndex'] = $this->share(function ($c) {
            return new Home();
        });
        
        $this['CMerlinIndexIndex'] = $this->share(function ($c) {
            return new View([
                'content' => 'upload.phtml',
                'layout' => new layout(),
                'data' => [
                    'bob' => 'boo'
                ]
            ]);
        });
            
        
        


        /**
         * *****************  Mongo database
         * @todo Add authentication in here
         */
        $this['mongoClient'] = $this->share(function ($c) {
            return new MongoClient(); // connect
         });

        $this['mongoAe2'] = $this->share(function ($c) {
            try {
                $mongo = $c['mongoClient']; // connect
                /**
                 * @var MongoDB $db
                 */
                $db = $mongo->ae2;

                return $db;
            } catch (MongoConnectionException $e) {
                throw new RuntimeException("Can't connect to database", 1, $e);
            }
        });

        //  *******************  Mongo Grid FS filestore wrapper
        $this['gridFs'] = $this->share(function ($c) {
            return new wwMongoFile([
                'db' => $c['mongoClient']
            ]);
        });
        
        /* Get the consumer client record */
        //   returned as wwArray object

        /* For now we always use the "consumer" client record, 
         * but we'll have a mechanism later for selecing different clients, 
         * perhaps based on subdomain */
        $this['client_id'] = "consumer";

        $this['client'] = $this->share(function ($c) {
            /**
             * @var MongoCollection $clients
             */
            $clients = $c['mongoAe2']->clients;
            $data =  $clients->findOne(array("name" => $c['client_id']));
            return new wwArrayAbstract($data);
        });

        // *********************************  User record 
        //   returned as wwArray object
        
        //  default user ID
        $this['user_id'] = 'testUser';
        
        $this['user'] = $this->share(function ($c) {
            $data =  $c['mongoAe2']->users->findOne(array("personal.email" => $c['user_id']));
            return new wwArrayAbstract($data);
        });

        //  ***********************************  PDF Data record
        //  returned as nested userdata / clientdata objects
        $this['pdfData'] = $this->share(function ($c) {
            return new pdfData(array(
                "userId" => $c['user_id'],
                "userData" => $c['user'],
                "clientData" => $c['client'],
                ));
        });

        
        
        // ******************************  EMAIL
        
        // email config
        
         $this['emailConfig'] = $this->share(function ($c) {
            return new emailConfig();
            
        });
        
        //  mailer
         $this['mailer'] = $this->share(function ($c) {
             $phpm = new PHPMailer;
            return new email($phpm, $c['emailConfig']);
            
        });
        
        //  templates
         $this['emailValidation'] = $this->share(function ($c) {
            return new email_validation_template;
            
        });
        
    

        //*************************  PDF creation

        $this['WkHtPd'] = $this->share(function ($c) {
            return new WkHtPd(); //  
        });
        
        //  pdf view and layout classes are used by pdfCreate.php to supply
        //  HTML snippets for the PDF.
        //  
        //  a view may return a html page or a snippet to be wrapped in layout
        $this['pdfDemoView'] = $this->share(function ($c) {
            return new pdfView(); //  default values
        });

        //  layout adds <html>, <head>, <body> and css / js to view 
        $this['pdfDemoLayout'] = $this->share(function ($c) {
            return new layout(
                [
                    'view' => $c['pdfDemoView']
                ]);
        });
        
        //  You do not need to define the view separately 
        $this['pdfDemoCover'] = $this->share(function ($c) {
            $view = new pdfView([
                'content' => 'demo/cover.phtml'
            ]);
            
            return new layout([
                    'view' => $view
                ]);
        });
        
        //  Confirmation letter copied from V1
        $this['pdfConfirmation'] = $this->share(function ($c) {
            $view = new pdfView([
                'content' => 'confirmation/page_1.phtml'
            ]);
            
            return new layout([
                    'view' => $view
                ]);
        });
        
        
        
    }

}
