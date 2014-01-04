<?php

namespace Qe;

class Gallery extends File
{
    
    /**
     * @var string ftp folder 
     */
    protected $ftpBase = '/httpdocs/';

    /**
     * Array of image details
     * read from / written to qin
     * 
     */
    protected $_images = array();

    /**
     * post-Constructor init
     * 
     */ 
    public function init()
     {
        //   load image details
        return $this->loadQin();

     }
     
    /**
     * set path - overwrite parent as 
     * we want to create the file if it does not exist
     * 
     * @param string $path - RELATIVE
     * @return this
     * @throws \InvalidArgumentException
     */
    public function setPath($path = '')
    {
//         \Qe\Debug::dump($path, 0, 'gal40 - path:');
//         /home/jbrookes/sites/local.admin.qe/data/images/news/the-best-year-ever/thumbs/gal.qin
       //  create folder and file if it does not exist
        $this->setTarget($path);
        $path = $this->getTarget();
        $this->filePath = $path;
        $this->_fileParts = $this->fileInfo($path);
        return $this->init();

//        parent::setPath($this->getTarget());
    }


    /**
     * Set the target (save) location
     * and create the file
     * 
     * By default, save folder is images/; 
     * the 'images' will be added if not already present.
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
//        \Qe\Debug::dump($baseParts, 0, 'gal74 - baseParts: ');
        $baseParts = $this->checkExt($baseParts);
        //  check we are saving to the correct place.  
        //  Target should be images/subfolder/slug/thumbs/gal.qin
        
        $loc = array_search('images', $baseParts['path']);
        if ($loc !== false ){
            //  images folder is present, so remove it and
            //  anything before it
//           throw new \InvalidArgumentException(
//                'Path to save images must be relative to /images folder - path given: '
//                . $target);
            while (array_shift($baseParts['path']) != 'images'){}

        }
        //  either not found or shifted off, so add it
        array_unshift($baseParts['path'], 'images');

//        \Qe\Debug::dump($baseParts, 0, 'gal88 - baseParts:');
        
        return parent::setTarget(implode('/', $baseParts['path']));

     }
     
     /**
      * Check the file name.
      * File is called <basepart>/thumbs/xx.qin
      * We check the ext is .qin 
      * if not, we check the thumbs folder exists.
      * We may just get <basepart> or <basepart>/thumbs so we add gal.qin
      * 
      * @param array $baseParts
      * @return array
      * @throws \InvalidArgumentException
      */
     protected function checkExt(&$baseParts)
     {
//        \Qe\Debug::dump($baseParts, 0, 'gal107 - baseParts: ');
        if (! 'qin' == $baseParts['ext']){
            //  assume we have a folder.  Is the last subfolder 'thumbs'?
            if (! 'thumbs' == $baseParts['base']){
                //  add 'thumbs/gal.qin'
                array_push($baseParts['path'],'thumbs' );
            }
            //  we have 'thumbs'
            $baseParts['ext'] = 'qin';
            $baseParts['base'] = 'gal';
            array_push($baseParts['path'],'gal.qin' );
//        \Qe\Debug::dump($baseParts, 0, 'gal118 - baseParts: ');
        }

        return  $baseParts ;
    }

     public function loadQin()
     {
        $path = $this->getPath();
        //does it exist?
        if (!file_exists($path)){
            //  create it when we save
            return $this;
//            throw new \InvalidArgumentException ('Cannot read gallery from ' . $path . ' - message was: ' . $exc->getMessage());
        }
        // Read it into _images
        try {
            $this->_images = json_decode(file_get_contents($this->getPath()), true);
        } catch (Exception $exc) {
            throw new \InvalidArgumentException ('Cannot read gallery from ' . $path . ' - message was: ' . $exc->getMessage());
        }
        return $this;
     }
     
     public function saveQin()
     {
        try {
            $putString = json_encode($this->_images);
//        \Qe\Debug::dump($putString, 0, 'gal150 - qin to write: ');

            file_put_contents($this->getTarget(), $putString);
        } catch (Exception $exc) {
            throw new \InvalidArgumentException ('Cannot save gallery to ' . $this->getPath() . ' - message was: ' . $exc->getMessage());
        }
        return 'Gallery (qin file) saved <br />';
    }

    public function pushQin($filePath)
    {
 //         \Qe\Debug::dump($this, 1, 'gal163');
        try {
            $errorMsg .= $this->saveImage($filePath);
        } catch (Exception $exc) {
            throw new \InvalidArgumentException ('Cannot save gallery to ' . $this->getPath() . ' - message was: ' . $exc->getMessage());
        }
            
        try {
            $this->pushFtp();
            $errorMsg .= 'Gallery pushed to media.qe.<br>';
        } catch (Exception $exc) {
            $errorMsg .= 'Cannot push file via ftp: ' . $exc->getMessage() ;
        }

        return $errorMsg;
    }


    public function getQin()
    {
        return $this->_images;
    }

    
    public function findImage($filePath)
    {
        //  file may include path
        $fileName = basename($filePath);
        
        foreach ($this->_images as $details) {
            if ($details['href'] == $fileName){
                return $details;
            }
        }
    }
    
    public function saveImage($newDetails)
    {
        //  map newdatails t0 qin.
        //  newDetails is probably everything on the form.
        
        //  map form fields onto object

 
        if (empty($newDetails['href'])){
            $tmpDetails['href'] = array_pop(explode('/', $newDetails['pf']));
        } else {
            $tmpDetails['href'] = $newDetails['href'] ;
        }
        if (empty($newDetails['title'])){
            $tmpDetails['title'] = $newDetails['galTitle'];
        } else {
            $tmpDetails['title'] = $newDetails['title'];
        }
        if (empty($newDetails['caption'])){
            $tmpDetails['caption'] = $newDetails['galCaption'];
        } else {
            $tmpDetails['caption'] = $newDetails['caption'];
        }
        if (empty($newDetails['class'])){
            $tmpDetails['class'] = '';
        } else {
            $tmpDetails['class'] = $newDetails['class'] ;
        }
            
        
        
        $fileName = $tmpDetails['href'];
        $oldImages = $this->_images;
        $newImages = array();
        $added = false;
        foreach ($oldImages as $details) {
            if ($details['href'] == $tmpDetails['href']){
                //  replace and exit
                
                $newImages[] = $tmpDetails;
                $added = true;
            }  else {
                //  not changed so just write it in
                $newImages[] = $details;
            }
        }
        
        if (! $added){
            $newImages[] = $tmpDetails;
        }
        $this->_images = $newImages;
//        \Qe\Debug::dump($newImages, 1, 'gal170');
        return $this->saveQin();
    }


    
}