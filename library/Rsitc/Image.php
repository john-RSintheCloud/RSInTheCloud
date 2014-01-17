<?php

namespace Qe;

class Image extends File
{
    
    /**
     * @var string ftp folder 
     */
    protected $ftpBase = '/httpdocs/images/';
    /**
     *
     * @var int FTP transfer type
     * - needs to be set to Binary for images
     */
    protected $ftpType = FTP_BINARY;

    /**
     *
     * @var \Zim_Image
     */
    protected $_image = null;
    
    /**
     * Array of sizes for resize / crop.
     * 
     */
    protected $_sizes = array();

    /**
     * post-Constructor init
     * 
     */ 
    public function init()
     {
        //   load image
        return $this->loadImage();

     }


    /**
     * Set the target (save) location.
     * 
     * By default, save folder is images/; 
     * the 'images' folder will be added if not already present.
     * Images cannot exist elsewhere in the path, so /news/images will not work
     * (although /images/news/images should)
     * 
     * Parent will then add (prepend) /data/ to give /data/images/news...
     * 
     * @param string $target file path
     * @return type
     * @throws \InvalidArgumentException
     */
     protected function setTarget($target)
     {

        //  validate the extension and base, then run parent to 
        //  check directory exists and is writable, 
        $baseParts = $this->fileInfo($target);
        $baseParts = $this->checkExt($baseParts);
        //  check we are saving to the correct place.  
        //  Target should be images/subfolder/name.ext
        //  or images/news/slug/name.jpg for galleries
        
        $loc = array_search('images', $baseParts['path']);
        if ($loc > 0 ){
            //  images folder is not first in the path
            throw new \InvalidArgumentException(
                'Path to save images must be relative to /images folder');
        } elseif ($loc === false) {
            //  not found, so add it
            array_unshift($baseParts['path'], 'images');
        }
        
        return parent::setTarget(implode('/', $baseParts['path']));

     }
     
     protected function checkExt($baseParts)
     {
        if (! $baseParts['ext']){
            $baseParts['ext'] = $this->_image->getType();
            //  pop off the filename
            array_pop($baseParts['path']);
            //  and replace with new one.
            array_push($baseParts['path'],
                $baseParts['base'] . '.' . $baseParts['ext']);


        } elseif ($baseParts['ext'] != $this->_image->getType()) {
            throw new \InvalidArgumentException ('Supplied extension ('.$baseParts['ext']
                .') does not match image type (' . $this->_image->getType()
                . ') to save image: '  . $target);
        }

        return  $baseParts ;

     }


     public function loadImage($path = '')
     {
        if ($path)
            $this->setPath ($path);

        /**
         * @todo check we have a valid image name 
         */
        
        try {
            $this->_image = new \Zim_Image($this->getPath());
    
        } catch (Exception $exc) {
            throw new \InvalidArgumentException ('Cannot create image from ' . $this->getPath() . ' - message was: ' . $exc->getMessage());
        }
        return $this;
     }
     
     public function saveAs($path = '')
     {
        if ($path) {
            $this->setTarget ($path);
            $this->_image->save($this->getTarget());
            return $this;
        }
        //  no path provided, so use existing
        if ( file_exists($this->getPath())){
            //  do not overwrite existing file by accident
            return $this->saveAs($this->getPathIncremented());

        }
        //  creating file from scratch
        //  validate new name
        $this->setTarget($this->getPath());
        $this->_image->save($this->getTarget());
        return $this;
     }


     public function pushFtp($path = '')
     {
        if ($path) {
            //  check extension
            $baseParts = $this->fileInfo($path);
            $baseParts = $this->checkExt($baseParts);
            $path = implode('/', $baseParts['path']);

            if (!file_exists($this->getTarget()))
                throw new \InvalidArgumentException ('You must save image before pushing');
            $this->ftpPush($this->getTarget(), $path);
        }
        return $this;
     }


     /**
      * Convert x1,x2,y1,y2 to x1,y1,w,h - or vice versa
      * 
      * Limit to real image (reduce w, h to suit)
      * 
      * @param array $sizes 
      * @return $this
      */
     public function checkSizes($sizes = array())
    {

        if (empty($sizes['x1'])){
            $sizes['x1'] = 0;
        }
        if (empty($sizes['y1'])){
            $sizes['y1'] = 0;
        }
        if (empty($sizes['x2'])){
            $sizes['x2'] = 0;
        }
        if (empty($sizes['y2'])){
            $sizes['y2'] = 0;
        }
        if (empty($sizes['w'])){
            $sizes['w'] = 0;
        }
        if (empty($sizes['h'])){
            $sizes['h'] = 0;
        }
        
        if (empty($sizes['ar'])){
            $sizes['ar'] = 0;
        }
        
        $this->_sizes = $sizes;
       
        $this->setWidth()->setHeight();
 
        return $this;
        
    }
    
    public function getSizes()
    {
        return $this->_sizes;
    }

    protected function setWidth()
    {
        //  do we have enough to calculate width?
        //  Priority is:
        //  Already set
        //  h > o && ar > 0 : h * ar
        //  x2 > 0 : x2 - x1
        //  y2 > 0 && ar > 0 : y2 - y1 * ar
        
        if ($this->_sizes['w']) {
            //  we have width  so set x2
            $this->_sizes['x2'] = $this->_sizes['x1'] + $this->_sizes['w'];
        }
        elseif ($this->_sizes['h'] && $this->_sizes['ar']){
            $this->_sizes['w'] = $this->_sizes['h'] * $this->_sizes['ar'];
            $this->_sizes['x2'] = $this->_sizes['x1'] + $this->_sizes['w'];
        }
        elseif ($this->_sizes['x2']) {
            $this->_sizes['w'] = $this->_sizes['x2'] - $this->_sizes['x1'];
            
        }
        elseif ($this->_sizes['y2'] && $this->_sizes['ar']){
            $this->setHeight();
            $this->_sizes['w'] =  $this->_sizes['h'] * $this->_sizes['ar'];
            $this->_sizes['x2'] = $this->_sizes['x1'] + $this->_sizes['w'];
        }
        
        return $this;
    }

        protected function setHeight()
    {
        //  do we have enough to calculate height?
        //  Priority is:
        //  Already set
        //  w > o && ar > 0 : w / ar
        //  y2 > 0 : y2 - y1

        if ($this->_sizes['h']) {
            //  we have h  so set y2
            $this->_sizes['y2'] = $this->_sizes['y1'] + $this->_sizes['h'];
        }
        elseif ($this->_sizes['w'] && $this->_sizes['ar']){
            $this->_sizes['h'] = $this->_sizes['w'] / $this->_sizes['ar'];
            $this->_sizes['y2'] = $this->_sizes['y1'] + $this->_sizes['h'];
        }
        elseif ($this->_sizes['y2']) {
            $this->_sizes['h'] = $this->_sizes['y2'] - $this->_sizes['y1'];

        }
        return $this;
    }

     public function getImage()
     {
         return $this->_image->getBinary();

  //       return readfile( $this->getPath());
     }

    public function getType()
    {

    //# \Qe\Debug::dump($this->_image, 0, 'image 120');
        return $this->_image->getType();
    }

    public function getWidth()
    {

    //# \Qe\Debug::dump($this->_image, 0, 'image 120');
        return $this->_image->getWidth();
    }

    public function getHeight()
    {
        return $this->_image->getHeight();
    }

    public function isLoaded()
    {
        return $this->_image->getDriver()->isImageLoaded();
    }

    /**
     * generate a float of aspect ratio ,
     * eg 1.2 or 1.4444 (width / height)
     * 
     * @return float
     */
    public function getAspectRatio() {

        return $this->getWidth() / $this->getHeight();

    }

    /**
     * generate a string of aspect ratio (simple),
     * eg 4/5 or 13/9 (width / height)
     * 
     * @todo Make it fuzzy - eg 641 x 479 > 4/3
     * 
     * @return string
     */
    public function getAspect() {

        $width = $this->getWidth();
        $height = $this->getHeight();

        $divisor = $this->gcd($width, $height);

        $ratio = ($width / $divisor) . "/" . ($height / $divisor);
        return $ratio;
    }

    protected function gcd($x, $y) {

        $x = abs($x);
        $y = abs($y);
        if ($x * $y == 0) {  //  x or y = 0
            return "0";
        } else {
            while ($x > 0) {
                $z = $x;
                $x = $y % $x;
                $y = $z;
            }
            return $z;
        }
    }

    public function cropImage( $imageWidth = 0, $imageHeight = 0, $left = 0, $top = 0) {

        if (! $imageWidth){
            $imageHeight = $this->_sizes['h'];
            $imageWidth = $this->_sizes['w'];
            $left = $this->_sizes['x1'];
            $top = $this->_sizes['y1'];
        }
        
        $this->_image->getDriver()
            ->crop($left, $top, $imageWidth, $imageHeight);
    
        return $this;
        
    }

    /**
     * resize to width x height.
     * If height or width not given (0) retain aspect ratio.
     * If $limit = 'max' limit max size  to given parameters 
     *          (retain aspect ratio)
     * If $limit = 'min' limit smaller dimension  to given parameters 
     *          (retain aspect ratio)
     * If $limit = 'grow' allow the image to stretch to fill the parameters.
     *          By default we do not stretch images.
     * 
     * @param int $width
     * @param int $height
     * @param string $limit 'min', 'max', 'grow' or ''
     * 
     * @return \Qe\Image
     */
    public function resize( $width = 1000, $height = 0, $limit = 'max')
    {
  
        //  Validate input and calculate values
        $tWidth = 0;
        $tHeight = 0;

        if ($height + $width == 0){
            throw new \InvalidArgumentException('Must supply height or width to resize');
        }
        if ($height * $width == 0){
            //  one is zero so work out aspect ratio and value
            $ratio = $this->getAspectRatio();
            if ( $width ) {
                $tHeight = (int) $width / $ratio;
                $tWidth = $width;
            }
            if ( $height ) {
                $tHeight = $height;
                $tWidth = (int) $height * $ratio;
            }
        }
        elseif ('max' == $limit){
            //  max is set so work out aspect ratio and max values
            $ratio = $this->getAspectRatio();
            if ($height > $width / $ratio){
                $tHeight = (int) $width / $ratio;
                $tWidth = $width;
            } elseif ($width > $height * $ratio){
                $tHeight = $height;
                $tWidth = (int) $height * $ratio;
            } else {
                //  already got right aspect ratio
                $tWidth = $width;
                $tHeight = $height;
                
            }
        }
        elseif ('min' == $limit){
            //  min is set so work out aspect ratio and min values
            $ratio = $this->getAspectRatio();

            if ($height < $width / $ratio){
                $tHeight = (int) $width / $ratio;
                $tWidth = $width;
            } elseif ($width < $height * $ratio){
                $tHeight = $height;
                $tWidth = (int) $height * $ratio;
            }
        }
        else {
            //  just resize to values set
            $tWidth = $width;
            $tHeight = $height;
        }

        if ('grow' != $limit){
            //  do not allow stretching of the image
            if ($this->getWidth() < $tWidth){
                $tHeight = (int)  $tHeight * $this->getWidth() / $tWidth;
                $tWidth = $this->getWidth();
            }
            elseif ($this->getHeight() < $tHeight){
                $tWidth = (int) $tWidth * $this->getHeight() / $tHeight;
                $tHeight =  $this->getHeight();
            }
        }
        
        if ($tHeight < 10 ) {
            $tHeight = 10 ;
        }
        if ($tWidth < 10) {
            $tWidth = 10;
        }
        
        $this->_image->getDriver()->resize((int) $tWidth, (int) $tHeight);
                
        return $this;
    }

}