<?php


/**
 * config model test class
 *
 * @author John
 */
class config_configTest extends PHPUnit_Framework_TestCase
{

    protected $object;

    protected function setUp()
    {
        $this->object = new config_config();
    }

    protected function tearDown()
    {
    }

    /**
     * @expectedException        RuntimeException
     * @expectedExceptionMessage Where has 'bob' gone?
     */
    public function testReadConfigBob()
    {
        $testFile = 'tests/phpunit/application/modules/config/testcase.php';
        $this->object->readConfig('bob');
        
    }

    /**
     *
     */
    public function testReadConfig()
    {
        $testFile = 'tests/phpunit/application/modules/config/testcase.php';
        $this->object->readConfig($testFile);
        //  ok, we have the file, now see if it has worked
        
        $this->assertEquals('OO RS', $this->object->applicationname);
        $this->assertEquals('1', $this->object->devMode);
        $this->assertEquals(1, $this->object->devMode);
        $this->assertEquals('00', $this->object->css->reload_key);
        $this->assertEquals(0, $this->object->css->reload_key);
        $this->assertEquals(false,  $this->object->false);
        //  assertTrue and assertFalse are === tests,
        //  assertEquals is an == test
//        $this->assertTrue( $this->object->true);
        $this->assertEquals(true, $this->object->true);
        $this->assertNotEquals(false, $this->object->notFalse);
        
    }
        
    /**
     *
     */
    public function testReadSecureConfig()
    {
        $testFile = 'tests/phpunit/application/modules/config/testcase.php';
        $this->object->readConfig($testFile);
        $this->assertEquals(
                'tests/phpunit/application/modules/config/testsec.sec', 
                $this->object->secureConfigLocation);
        $this->object->readSecureConfig();
        //  ok, we have the file, now see if it has worked
        
        $this->assertEquals('localhost', $this->object->mysql->server);
        $this->assertEquals('bob', $this->object->mysql->password);
        $this->assertEquals('AKIAIHCX', $this->object->s3->key);
        $this->assertEquals('/HMdyMdh', $this->object->s3->secret);
        $this->assertEquals('rsinthecloud-upload', $this->object->s3->bucket);

        //  these should still be set!
        $this->assertEquals('OO RS', $this->object->applicationname);
        $this->assertEquals('00', $this->object->css->reload_key);
        
        //  and one last test
        $this->assertEquals('', $this->object->nonexistantname);
        $this->assertEquals('', $this->object->CSS->reload_key);
        
        

        
        
        
        
    }
    
    
   
   /**
    * Takes a key / value pair and sets $this->$key = $value
    * except
    * key is the name of a variable (eg $s3_bucket) and 
    * the result is nested ($this->s3->bucket)
    * 
    */
   public function TestSetVar()
   {
       $var = '$bob';
       $val = 'boo';
       $var = trim($var, ' $');
       $val = trim($val, "<' ;\"");

       
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

   public function readSecureConfig()
    {
        $this->readConfig($this->secureConfigLocation);
        
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
                    $this->setVar ( $key, $val);
                break;
                default:
                    //  if it's not any of the above, it needs checking.
                    die('invalid config option:' . $data);
                break;
            }
        
        return $this;
    }
   

}
