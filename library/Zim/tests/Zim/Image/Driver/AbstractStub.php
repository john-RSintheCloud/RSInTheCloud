<?php

/**
 * @see Zend_Image_Driver_Abstract
 */
require_once 'Zend/Image/Driver/Abstract.php';

/**
 * Stab for Zend_Image_Driver_Abstract
 *
 * @category    Zend
 * @package     Zend_Image
 * @subpackage  Zend_Image_Driver
 * @author      Stanislav Seletskiy <s.seletskiy@office.ngs.ru>
 * @copyright   2010 NGS
 */
class Zend_Image_Driver_AbstractStub extends Zend_Image_Driver_Abstract
{
    public function setSize( $width, $height )
    {
        $this->_width = $width;
        $this->_height = $height;
    }


    public function getSize()
    {
        return array( $this->_width, $this->_height );
    }


    public function getBinary()
    {
    }


    public function save( $fileName )
    {
    }


    public function load( $fileName ) {
        parent::load( $fileName );
    }
}
