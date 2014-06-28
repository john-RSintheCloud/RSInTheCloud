<?php


/**
 * Takes config and create config objects.
 *
 * @author John
 */
class config_config
{

    public function __construct()
    {
        //  read in the config.

        if (($handle = fopen(APPLICATION_PATH . '../_config/config.php', "r")) === FALSE) {
            throw new InvalidArgumentException ("Where's the config file gone?");
        }
        while (($data = fgetcsv($handle, 1000, "=")) !== FALSE) {
            $num = count($data);
            $prefix = '';
            if ($num == 2){
                $this->setVar ( $data[0], $data[1]);

            }
        }
        fclose($handle);
        if (($handle = fopen(APPLICATION_PATH . $secure_config_location, "r")) === FALSE) {
            throw new InvalidArgumentException ("Don't forget the secure config file");
        }
        while (($data = fgetcsv($handle, 1000, "=")) !== FALSE) {
            $num = count($data);
            $prefix = '';
            if ($num == 2){
                $this->setVar ( $data[0], $data[1]);

            }
        }
        fclose($handle);
        return $this;
   }

   protected function setVar($var, $val)
   {
       $var = trim($var);
       $val = trim($val, "' ;");
       $prefix = '';

        if (substr($var, 0, 1) == '$'){
            $var = ltrim($var, '$');
            $pref = strpos($var, '_');
            if ($pref){
                $prefix = substr($var, 0, $pref);
                $var = substr($var,$pref + 1);
            }
        }

        if ($prefix){
            $prefArray = $this->$prefix;
            if(! is_array($prefArray)){
                $prefArray = array();
            }
            $prefArray[$var] =  $val;
            $this->$prefix = $prefArray;
        } else {
            $this->$var = $val;
        }

        return $this;


   }

   public function __get($name)
   {
       if (isset($this->$name)){
           return ($this->$name);
       } else {
           return '';
       }
   }

   public function getDbConfig()
   {
       return $this->mysql;
   }


}
