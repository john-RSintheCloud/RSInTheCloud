<?php


/**
 * pimple DI container test class
 * 
 * need to work out how to unit test the wrappers
 * but will move to pimple 3 first
 *
 * @author John
 */

class containers_DicTest extends PHPUnit_Framework_TestCase
{

    protected $object;

    protected function setUp()
    {
        $this->object = new containers_Dic();
        $this->object->init();
    }

    protected function tearDown()
    {
    }


    public function testinit()
    {

        $this->assertInstanceOf('containers_Dic', $this->object);
        $basePath = realpath(dirname(__FILE__) . "/../../../../../");
        $this->assertEquals($basePath, $this->object['basePath']);
        
        /**
         * Configuration
         */
        $config = $this->object['config'];
        $this->assertEquals('_config/config.php', $config->configFilePath);

        
//        /**
//         * DB Struct 
//         */
//        $dbStruct = $this->object['dbStruct'];
//        $this->assertEquals($basePath . '/application/modules/database/dbstruct/', $dbStruct->dbStructPath);
//        
//        $tableStruct = $dbStruct->getDbStruct('user');
//        $this->assertInstanceOf('database_tableStruct', $tableStruct);
//        $this->assertEquals('YES', $tableStruct->fields->password->fNull);
//        $names = $tableStruct->getFieldNames();
//        $this->assertArrayHasKey('password', $names);
//
//        $names = $this->object['dbStruct']->getDbStruct('user')->getFieldNames();
//        $this->assertArrayHasKey('password', $names);
//        
    }
    
    /**
     * simple timer - uses an array to hold multiple start times
     */
    public function testTimer()
    {

        $this->assertInstanceOf('timer', $this->object['timer']);

    }
    
    /**
     * extend the dic
     */
//    public function testUserTable()
//    {
//
//        $this->object['userTable'] = $this->object->share( function ($c) {
//            $table = new database_table_abstract(array(
//                'db' => $c['db'],
//                'table' => 'user',
//                'fields' => $c['dbStruct']->getDbStruct('user')->getFieldNames()
//            ));
//            return $table;
//        });
//        $this->assertInstanceOf('database_table_abstract', $this->object['userTable']);
//
//    }

}