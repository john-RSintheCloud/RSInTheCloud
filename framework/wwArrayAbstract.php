<?php

namespace Framework; 

/**
 * An abstract array class for converting nested arrays into usable classes
 * without enforcing a schema.
 *
 * @author JohnB
 */
class wwArrayAbstract extends wwAbstract
{

    /** The big difference between this and the normal
     * abstract class is that this stores nested arrays as nested objects
     * 
     * @param string $name  name of field to set
     * @param string $value Value to set it to
     *
     * @return  wwArrayAbstract
     */
    public function __set($name, $value)
    {
        if (is_array($value)){
            $this->$name = new wwArrayAbstract($value);
            return $this;
        }
        return parent::__set($name, $value);
    }

    /**
     * magic getter
     *
     * For deeply nested arrays we want to ensure we do not get 
     * Trying to get property of non-object warnings,
     * so always return something!
     * 
     * @param string $name field / info to get
     *
     * @return  mixed - value of item being got
     */
    public function __get($name)
    {
        $value = parent::__get($name);
        if (is_null($value)){
            return new self;
        }
        return $value;
    }

    
    
    public function toArray()
    {
        $ret = [];
        foreach($this as $key => $value) {
            if ($value instanceof wwArrayAbstract){
                $ret[$key] = $value->toArray();
            } elseif (is_int($key)) {
                $ret[] = $value;
            } else {
                $ret[$key] = $value;
            }
        }
        return $ret;
    }
    //
    public function __toString()
    {
        return '';
    }

    public function printRecursive($indent = 1)
    {
        $spacer = '_ _ _';
        $starter = "->";
        $ind = '<br>' .  $indent. ': ';
        
        for ( $nx = 0; $nx < $indent; $nx++){
            $ind .= $spacer;
        }
        $ind .= $starter;
        $indent++;
        $ret = '' ;
        foreach($this as $key => $value) {
            if ($value instanceof wwArrayAbstract){
                $ret .= $ind . $key . '=>' . $value->printRecursive($indent);
            } elseif (is_array ($value)){
                $valObj = new wwArrayAbstract($value);
                $ret .= $ind . $key . '=>' . $valObj->printRecursive($indent);
            } else {
                $ret .= $ind . $key . ' = ' . $value ;
            }
        }
        return $ret;
       
        
    }

}
