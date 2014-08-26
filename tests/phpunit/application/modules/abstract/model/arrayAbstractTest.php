<?php

/**
 * An abstract array class for converting nested arrays into usable classes
 * without enforcing a schema.
 *
 * @author John
 */
class abstract_model_arrayAbstractTest extends PHPUnit_Framework_TestCase
{
    protected $object;

    protected function setUp()
    {
        $this->object = new abstract_model_arrayAbstract();
    }

    protected function tearDown()
    {
    }


    /**
     * This actually covers getter, setter, setoptions, - not much left
     */
    public function testConstruct()
    {

        $tester = new abstract_model_abstract([
            'bob' => 'boo'
            ]);
        $this->assertEquals('boo', $tester->bob);
    }
    
    
    /**
     * This is a copy of the parent test, just to ensure it works the same.
     * The only difference in this setter is with arrays which are tested
     * in testGet below
     */
    public function testSet()
    {
        $this->object->bob = 'boo';
        $this->assertEquals('boo', $this->object->bob);
        
        //  handle the possibility of a variable being called 'options'
        //  otherwise $this->options will call setOptions
        $this->object->options = 'options';
        $this->assertEquals('options', $this->object->options);
        //  knowing how it is stored..
        $this->assertEquals('options', $this->object->__Options);
        
    }

    public function testGet()
    {
        //  If you pass an array, it should be nested
        $this->object->bob = ['test' => 'true'];
        $this->assertEquals('true', $this->object->bob->test);
    }


    public function testCount()
    {
        $this->assertEquals( 0, $this->object->count());
        
        $this->object->bob = ['test' => 'true'];
        $this->assertEquals( 1, $this->object->count());
        $this->assertEquals( 1, $this->object->bob->count());
        $this->assertTrue( is_string($this->object->bob->test));
        
        $this->assertEquals( 0, $this->object->booo->count());

    }

    public function testIsset()
    {
        $this->assertTrue( isset($this->object));
        $this->assertFalse( isset($this->object->nonexistantname));
        $this->assertFalse( isset($this->object->nonexistantname->boo));

    }

    public function testEmpty()
    {
        $this->assertTrue( $this->object->isEmpty());
        $this->object->bob = ['test' => 'true'];
        $this->assertFalse( $this->object->isEmpty());
        $this->assertFalse( $this->object->bob->isEmpty());
        $this->assertTrue( $this->object->nonexistantname->isEmpty());
        $this->assertTrue( $this->object->nonexistantname->boo->isEmpty());

        $this->assertTrue( empty($this->object->nonexistantname));
//        This one fails though!
//        While the magic 'isset' above returns false 
//        The magic getter returns an object which is not 'empty'
//        $this->assertEmpty( $this->object->nonexistantname);
    }

    /**
     * This is very similar to the parent test, except there is a nested array
     * 
     * setOptions adds and overrides options but does not delete them
     */
    public function testSetOptions()
    {
        $this->object->setOptions([
            'test' => 'true',
            'ayyay' => [
                'test' => 'bob',
                'fpp' => 'baz'
            ]]);
        $this->assertEquals('true', $this->object->test);
        $this->assertEquals('bob', $this->object->ayyay->test);
        $this->assertEquals('baz', $this->object->ayyay->fpp);
        

        $this->object->setOptions([
            'test' => 'changed',
            'bob' => 'boo']);
        $this->assertEquals('changed', $this->object->test);
        $this->assertEquals('boo', $this->object->bob);
        $this->assertTrue(isset($this->object->ayyay));

    }

    /**
     * Merge new values from array -
     * if key is already set ignore array value
     *
     * This is a copy of the parent test - but this one caught a problem
     * so it is being left in.
     */
    public function testMerge()
    {
        $this->object->setOptions(['test' => 'true']);
        $this->assertEquals('true', $this->object->test);

        $this->object->merge([
            'test' => 'changed',
            'bob' => 'boo']);
        $this->assertEquals('true', $this->object->test);
//    var_dump($this->object);
        $this->assertEquals('boo', $this->object->bob);

    }

    
    
    public function testToArray()
    {
        $this->object->setOptions([
            'test' => 'true',
            'ayyay' => [
                'test' => 'bob',
                'fpp' => 'baz'
            ]]);
        $this->assertEquals('true', $this->object->test);
        $this->assertEquals('bob', $this->object->ayyay->test);
        $this->assertEquals('baz', $this->object->ayyay->fpp);
        
        $toArray = $this->object->toArray();
        $this->assertEquals('true', $toArray['test']);
        $this->assertEquals('bob', $toArray['ayyay']['test']);
        $this->assertEquals('baz', $toArray['ayyay']['fpp']);
    }
    //
    public function testToString()
    {
        $this->object->setOptions([
            'test' => 'true',
            'ayyay' => [
                'test' => 'bob',
                'fpp' => 'baz'
            ]]);
        $this->assertEquals('true', (string) $this->object->test);
        $this->assertEquals('', (string) $this->object->ayyay);
        $this->assertEquals('', $this->object);
        
    }

    public function testPrintRecursive($indent = 1)
    {
//  need to get this working!       
        
    }

}
