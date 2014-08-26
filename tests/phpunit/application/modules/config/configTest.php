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
    public function testReadMissingConfig()
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
        $this->assertEquals('', (string) $this->object->nonexistantname);
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
       $this->object->setVar ($var, $val);
       $this->assertEquals('boo', $this->object->bob);
       
   }

   public function testGetDbConfig()
   {
        $testFile = 'tests/phpunit/application/modules/config/testcase.php';
        $this->object->readConfig($testFile);
        $this->assertEquals(
                'tests/phpunit/application/modules/config/testsec.sec', 
                $this->object->secureConfigLocation);
        $this->object->readSecureConfig();
        $testSql = $this->object->getDbConfig();
        
        $this->assertEquals( 'localhost', $testSql->server);
        $this->assertEquals( 'bob', $testSql->password);
        $this->assertEquals( 'RS', $testSql->db);

   }


}
