<?php


/**
 * Takes config and create config objects.
 * 
 * config files are identified in the DIC
 * and loaded after construction, so constructor does nothing
 * (any options passed in are handled by parent)
 *
 * @author John
 */
class config_config extends abstract_model_arrayAbstract
{

   /**
    * Takes a key / value pair and sets $this->$key = $value
    * except
    * key is the name of a variable (eg $s3_bucket) and 
    * the result is nested ($this->s3->bucket)
    * 
    * @param string $var->getDbConfig()
    * @param any $val
    * @return \config_config
    */
   protected function setVar($key, $val)
   {
        $var = trim($key, ' $');
        $val = $this->fixVal($val);
        if (empty($var)) die('invalid config key: ' . $key);
        //  empty value is ok
        // if (empty($val)) die('invalid config value:' . $key);
       $prefix = '';

        $pref = strpos($var, '_');
        if ($pref){
            $prefix = substr($var, 0, $pref);
            $var = substr($var,$pref + 1);
        }
//    echo $prefix . '<br>';

        if ($prefix){
            $prefArray = $this->$prefix;
//      var_dump($prefArray);
            if (empty($prefArray)){
                $prefArray = new abstract_model_arrayAbstract();
            }
            $prefArray->$var =  $val;
            $this->$prefix = $prefArray;
        } else {
            $this->$var = $val;
        }
        return $this;
   }


   public function getDbConfig()
   {
       return $this->mysql;
   }

   /**
    * Read the secure config from the location
    * just loaded in config!
    * This allows for the file to be kept outside the repo.
    */
   public function readSecureConfig()
    {
        $this->readConfig($this->secureConfigLocation);
        
    }
    
    
    public function readConfig($fileName)
    {
        //  read in the config files
        $path = realpath(APPLICATION_PATH . $fileName );
        if ($path == ''){
            throw new \RuntimeException ("Where has '" . $fileName . "' gone?");
            //  not sure error page is loaded here, so just in case...
            die ("Where has $fileName gone?");
        }
        if (($handle = @fopen($path , "r")) === FALSE) {
            throw new \RuntimeException ("Cannot open config file: '$path' ");
            //  not sure error page is loaded here, so just in case...
            die ("Where has $fileName gone?");
        }
        while (($line = @fgets($handle, 1000)) !== FALSE) {
            $this->readLine($line);
        }
        
        fclose($handle);
        
        return $this;
    }
   
    protected function readLine($line)
    {

        //  read in the config files
        //  typical line is 
        //  $applicationname="OO RS"; #  implementation name, eg 'Bioquell'
        //  so we need to parse it.
        $data = trim($line);
        //  skip empty lines or lines starting with # or //
        if (empty($data) || !is_string($data)) return false;
        $c0 = $data[0];
        switch ($c0) {
            case '#':
            case '/':
            case '*':
            case '<':
                return false;
            break;
            case '$':
                //  find ; - ignore anything after it
                $parts = explode(';', $data);
                $keyVal = trim($parts[0]);
        
                //  find =
                $parts = explode('=', $keyVal);
                $this->setVar ( $parts[0], $parts[1]);
                return true;
            break;
            default:
                //  if it's not any of the above, it needs checking.
                die('invalid config option:' . $line);
            break;
        }

        return false;
    }

    protected function fixVal($val)
    {
        $val = trim($val, "<' ;\"");
        //  fix booleans
        if ($val == 'true') $val = true;
        if ($val == 'false') $val = false;

        return $val;

    }
}
