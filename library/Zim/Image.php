<?php

/**
 * Zend Image
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * Updated for QE / EandL J Brookes 28/5/2013
 *
 * @category  Zend
 * @package   Zim_Image
 * @author    Stanislav Seletskiy <s.seletskiy@gmail.com>
 * @author    Leonid Shagabutdinov <leonid@shagabutdinov.com>
 * @copyright Copyright (c) 2010
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id: Image.php 53 2012-11-02 08:00:29Z dtr@netimage.dk $
 */

/**
 * Base class for loading and saving images.
 *
 * @category  Zend
 * @package   Zim_Image
 * @copyright Copyright (c) 2010
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Zim_Image
{
    /**
     * Filename of image source.
     *
     * @var string
     */
    private $_filename = '';


    /**
     * Driver for image operations.
     *
     * @var Zim_Image_Driver
     */
    protected $_driver = null;

    /**
     * Constructor for image.
     *
     * @param mixed $filename Filename, Zim_Image or binary.
     * @param Zim_Image_Driver_Abstract $driver Driver for image operations.
     */
    public function __construct( $filename, Zim_Image_Driver_Abstract $driver = null )
    {
        if( ! $filename instanceof Zim_Image ) {
            $this->_driver = $driver;
        }

        if (is_null($this->_driver)){  //  set default driver
            $this->_driver = new Zim_Image_Driver_Imagick ;
        }

        $this->load( $filename );
    }


    /**
     * Loads specified file, or Zim_Image or binary as
     * image source.
     *
     * @param  mixed $filename Filename or instance of Zim_Image.
     * @return Zim_Image
     */
    public function load( $filename )
    {
        if( $filename instanceof Zim_Image ) {
            $this->_driver = clone $filename->getDriver();
        } else {
            $this->_filename = $filename;
            $this->_driver->load( $this->_filename );
        }

        return $this;
    }

    /**
     * Save image to file to disk.
     * @param string filename
		 * @param $type
     * @return bool
     */
    public function save( $filename,$type='auto')
    {
        return $this->_driver->save( $filename ,$type);
    }

    /**
     * @return Zim_Image_Driver_Abstract
     */
    public function getDriver()
    {
        return $this->_driver;
    }


    /**
     * Get image type
     *
     * @return string
     */
    public function getType()
    {
        return $this->_driver->getType();
    }

    /**
     * Get image is binary.
     *
     * @return binary
     */
    public function getBinary()
    {
        return $this->_driver->getBinary();
    }


    /**
     * Returns width of image.
     *
     * @throws Zim_Image_Driver_Exception
     * @return int Width of image.
     */
    public function getWidth()
    {
        $size = $this->_driver->getSize();
        return $size[ 0 ];
    }


    /**
     * Returns height of image.
     *
     * @throws Zim_Image_Driver_Exception
     * @return int Height of image.
     */
    public function getHeight()
    {
        $size = $this->_driver->getSize();
        return $size[ 1 ];
    }



}
