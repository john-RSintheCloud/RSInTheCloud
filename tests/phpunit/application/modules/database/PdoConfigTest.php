<?php

/**
 * PDO config tests
 * 
 * @author John
 */
class PdoConfigTest extends PHPUnit_Framework_TestCase
{

    protected $object;
    protected $config;

    protected function setUp()
    {
        $this->config = new config_config();
        $testFile = 'tests/phpunit/application/modules/config/testcase.php';
        $this->config->readConfig($testFile);
        $this->config->readConfig($this->config->secureConfigLocation);
        $testSql = $this->config->getDbConfig();
        
        $this->object = new database_PdoConfig(
                $testSql);
    }

    protected function tearDown()
    {
    }

    public function testGetUsername()
    {
        $this->assertEquals( 'RS', $this->object->getUsername());
    }

    public function testGetPassword()
    {
        $this->assertEquals( 'bob', $this->object->getPassword());
    }

    public function testGetConfig()
    {
        $this->assertEquals( 
           'mysql:host=localhost; dbname=RS',
                 $this->object->getConfig());
    }


}
