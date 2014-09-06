<?php

/**
 * RS uses dbStruct files to manage schema changes.  It's not perfect,
 * but it works and we will carry on using it for now.
 * 
 * As they hold the definitive structure, we will use the dbstruct files 
 * in our mappers so we can make them generic
 *
 * This class tests their creation and use.
 *
 * @author John
 */
class database_tableStructTest extends PHPUnit_Framework_TestCase
{

    protected $object;

    protected function setUp()
    {
        $dbStruct = new database_dbStruct([
            'dbStructPath' => realpath(dirname(__FILE__))  . '/dbStruct/'
        ]);
        $this->object = $dbStruct->getDbStruct('user');
    }

    protected function tearDown()
    {
    }


    public function testGetDbStruct()
    {
        
        $this->assertInstanceOf('database_tableStruct', $this->object);
        $this->assertEquals('YES', $this->object->fields->password->fNull);
//        echo $tableStruct->printRecursive();

    }

    public function testSetFields()
    {
        
        $this->assertEquals(false, empty($this->object->fields->password->fNull));
//        echo $tableStruct->printRecursive();

    }

    public function testGetFieldNames()
    {
        $names = $this->object->getFieldNames();
        $this->assertArrayHasKey('password', $names);
        $this->assertArrayHasKey('usergroup', $names);
    }
}
