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
class database_dbStructTest extends PHPUnit_Framework_TestCase
{

    protected $object;

    protected function setUp()
    {
        $this->object = new database_dbStruct([
            'dbStructPath' => realpath(dirname(__FILE__))  . '/dbStruct/'
        ]);
//        echo $this->object->printRecursive();
    }

    protected function tearDown()
    {
    }


    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage no table name given in getDbStruct
     */
    public function testGetEmptyDbStruct()
    {
        
        $this->object->getDbStruct('');

    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage File: /var/www/RS/OORS/tests/phpunit/application/modules/database/dbStruct/table_bob.txt not found
     */
    public function testGetWrongDbStruct()
    {
        
        $this->object->getDbStruct('bob');

    }

    public function testGetDbStruct()
    {
        
        $tableStruct = $this->object->getDbStruct('user');
        $this->assertInstanceOf('database_tableStruct', $tableStruct);
        $this->assertEquals('YES', $tableStruct->fields->password->fNull);
//        echo $tableStruct->printRecursive();

    }
}
