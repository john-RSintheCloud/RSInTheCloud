<?php

namespace Qe;

class Debug 
{

    /**
     *use:
     *  \Qe\Debug::debugShow(get_class(), __FUNCTION__ , 0);
     * 
     * @param type $class
     * @param type $function
     * @param type $die 
     */
    static public function debugShow($class, $function, $die = false, $msg = false)
    {
        if (APPLICATION_ENV == 'development'){
          echo  $function ." in ". $class ;
          if ($msg) echo ' with ' . $msg;
          echo " <br>";
          if ($die) die();
        }
    }

    /**
     *use:
     *  \Qe\Debug::dump($var, 1, 'label');
     * 
     * @param mixed $var variable to var_dump
     * @param bool $die whether to die after dump
     * @param string $msg - label or message to display
     */
    static public function dump($var, $die = false, $msg = false)
    {
        if (APPLICATION_ENV == 'development'){
            
         \Zend_Debug::dump($var, $msg);
            
          echo " <br>";
          if ($die) die();
        }
    }

}