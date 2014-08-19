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
        $this->object = new abstract_model_abstract();
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
    
    public function testSet()
    {

        $this->object->bob = 'boo';
        $this->assertEquals('boo', $this->object->bob);
        
        //  handle the possibility of a variable being called 'options'
        //  otherwise $this->options will call setOptions
        $this->object->options = 'options';
        $this->assertEquals('options', $this->object->options);
        //  knowing how it is stored..
        //  this breaks PHPunit!
        $this->assertEquals('options', $this->object->__Options);
        
    }


    public function testGet()
    {
        //  If you pass an array, it should be nested
        $this->object->bob = ['test' => 'true'];
        $this->assertEquals('true', $this->object->bob->test);
        
        $this->assertNull( $this->object->nonexistantname);
        $this->assertNull( $this->object->CSS->reload_key);

        
        
        
    }

    public function testSetOptions()
    {
        $this->object->setOptions(['test' => 'true']);
        $this->assertEquals('true', $this->object->test);

        $this->object->setOptions([
            'test' => 'changed',
            'bob' => 'boo']);
        $this->assertEquals('changed', $this->object->test);
        $this->assertEquals('boo', $this->object->bob);

    }

    /**
     * Merge new values from array -
     * if key is already set ignore array value
     *
     * @param array $options
     * @return \abstract_model_abstract
     */
    public function testMerge()
    {
        $this->object->setOptions(['test' => 'true']);
        $this->assertEquals('true', $this->object->test);

        $this->object->merge([
            'test' => 'changed',
            'bob' => 'boo']);
        $this->assertEquals('true', $this->object->test);
        $this->assertEquals('boo', $this->object->bob);

    }

    
    
    public function testToArray()
    {
    }
    //
    public function testToString()
    {
    }

    public function testPrintRecursive($indent = 1)
    {
//  need to get this working!       
        
    }

}
