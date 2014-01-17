<?php

/**
 * @see Zim_Image_Driver_Exception
 */
# require_once 'Zend/Image/Driver/Exception.php';


/**
 * @see Zim_Image_Driver_Abstract
 */
# require_once 'Zend/Image/Driver/Abstract.php';

/**
 * Imagick driver
 *
 * @category    Zend
 * @package     Zim_Image
 * @subpackage  Zim_Image_Driver
 * @author      Domas Trijonis <leonid@shagabutdinov.com>
 */
class Zim_Image_Driver_Imagick extends Zim_Image_Driver_Abstract {

	protected $_imagick;

	public function __construct() {
		$this->_imagick = new Imagick();
	}

	public function load($fileName) {
		parent::load($fileName);
		if(!file_exists($fileName))
			throw new Zim_Image_Driver_Exception("Image not found,".$fileName);
		$this->_imagick->readimage($fileName);
		$this->_imageLoaded = true;
	}

	public function resize($width, $height) {
		parent::resize($width, $height);
		$this->_imagick->scaleimage($width, $height);
	}

    /**
     * Must have a driver - dependant concrete crop method.
     * This is called by the abstract class 'crop' method.
     *
     * @param int $left
     * @param int $top
     * @param int $targetWidth
     * @param int $targetHeight
     */
	public function conCrop($left, $top, $targetWidth, $targetHeight) {
		$this->_imagick->cropimage($targetWidth, $targetHeight, $left, $top);		
	}
	public function getBinary() {
		return $this->_imagick->getimageblob();
	}

	public function getSize() {
		return array($this->_imagick->getimagewidth(),$this->_imagick->getimageheight());
	}

	public function save($filename, $type = 'auto') {
		$this->_imagick->writeimage($filename);
	}
	
}