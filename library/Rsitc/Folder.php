<?php

namespace Qe;

/**
 * General folder manipulation
 * 
 * Each folder needs a base and a (base-relative) path
 * Base ($basePath) defaults to the data directory 
 * ( realpath(APPLICATION_PATH . '/../' ) . '/data'; )
 * 
 */
class Folder
{

    /**
     * Path to the file base folder
     * set in setBasePath
     * default = realpath(APPLICATION_PATH . '/../' ) . '/data';
     *
     * @var string  $basePath
     */
    protected $basePath = ''; // /data

    /**
     * Path to the folder
     * relative to basepath
     *
     * eg images/news/the-best-year-ever
     *
     * @var string $folderPath 
     */
    protected $folderPath = '';


    //  contents and attributes of the folder
    protected $crumbs = array();
    protected $subfolders = array();
    protected $files = array();
    protected $flags = array();
    




    /**
     * Constructor
     * 
     * Pass in the path relative to the base directory
     * eg images/news/the-best-year-ever/02.jpg
     *
     * if folder does not exist, create it.
     */
    public function __construct($path = '')
    {

 //       \Qe\Debug::dump($path, 0, 'folder 52 - passed in:');
        //  set $folderPath
        //  and run init (for extending)
        return $this->setPath($path)
            ->init();
    }

    public function init()
    {
        //  stub
        return $this;
    }

    /**
     * 
     * @return string folderPath
     * @throws \InvalidArgumentException
     */
    public function getPath()
    {
        return $this->folderPath;
    }

    /**
     * set path
     * eg images/news/the-best-year-ever/02.jpg
     * 
     * @param string $path
     * @return this
     * @throws \InvalidArgumentException
     */
    public function setPath($path = '')
    {
//        \Qe\Debug::dump($path, 0, 'folder 88 - passed in:');
        $path = trim($path, '/ ');
        $basePath = $this->getBasePath() . '/' . $path;
        
        //  This may be a file (second pass)
        if (file_exists($basePath)  && !is_dir($basePath)) {
            $this->flags['isFile'] = true;
            $basePath = dirname($basePath);
            // reset the path name
            $pathArray = explode('/', $path);
            array_pop($pathArray);
            $path = implode('/', $pathArray);
        } elseif (! is_dir($basePath)) {
            //  folder does not exist so make it.
                if (! mkdir($basePath, 0777, true)){
                    throw new \InvalidArgumentException('Cannot create folder: ' . $basePath);
                }
        }
        //  folder exists, so get it
        $this->folderPath = $path;
//        \Qe\Debug::dump($path, 0, 'folder 104 - passed in:');

        return $this->populate();
    }

    public function getFullPath()
    {
//#     \Qe\Debug::debugShow(get_class(), __FUNCTION__, 0);
        return $this->getBasePath() . '/' . $this->getPath();
    }

    protected function getBasePath()
    {
//#     \Qe\Debug::debugShow(get_class(), __FUNCTION__, 0);

        if (!$this->basePath) {
            $this->setBasePath();
        }

        return $this->basePath;
    }

    protected function setBasePath($path = '')
    {
 //    \Qe\Debug::debugShow(get_class(), __FUNCTION__, 0);

        $this->basePath = $path ? $path : realpath(APPLICATION_PATH . '/../') . '/data';

        if (!$this->basePath || !is_dir($this->basePath)) {
            throw new \InvalidArgumentException('Invalid base path - ' . $this->basePath);
        }

        return $this;
    }


    /**
     * Get flags for this folder.
     * For a basic folder, these are
     * parent (popped path)
     * hasChildren
     * hasFiles
     * isEmpty
     * 
     * @return type
     */
    public function populate()
    {
 //#    \Qe\Debug::debugShow(get_class(), __FUNCTION__, 0);
        $baseName = $this->getFullPath();
        $path = $this->getPath();
        $ret = array();
 //#    \Qe\Debug::debugShow(get_class(), __FUNCTION__, 0,$baseName);

        //  iterate through file path to create breadcrumbs, to allow upward navigation.
        $dir = array();
        $dir['editUrl'] = '/image/index/';
        $dir['path'] = 'top';
        $dir['type'] = 'crumb';
        $this->crumbs[] = $dir;

        if ($path ) {
            $newPath = '';
            $parts = explode('/', $path);
            foreach ($parts as $upLink){
                if ($upLink){
                    if ('thumbs' == $upLink  && $this->isImage){
                        $this->flags['isThumb'] = true;
                        break;
                    }
                    if ('images' == $upLink){
                        //  only create galleries under the image folder
                        $this->flags['isImage'] = true;
                    }
                    $newPath .= '/' . $upLink ;
                    $safeName = str_replace('/', '£', $newPath );
                    $dir = array();
                    $dir['editUrl'] = '/image/index/pf/' . $safeName;
                    $dir['path'] = $upLink;
                    $dir['type'] = 'crumb';
                    $this->crumbs[] = $dir;
                    //  Work out the gallery url
                    $this->flags['galUrl'] = '/image/gallery/pf/' . $safeName;

                }
            }
        }

        $dirlist = new \DirectoryIterator($baseName);
        foreach($dirlist as $fileInfo) {

            $fileName = $fileInfo->__toString();
            $pathName = $path . '/' . $fileName;
            $safeName = str_replace('/', '£', $pathName);
            if($fileInfo->isDot()) {
                // do nothing - use crumbs for upward navigation
            } elseif ($fileInfo->isDir()) {
                $dir = array();
                $dir['editUrl'] = '/image/index/pf/' . $safeName;
                $dir['path'] = $fileName ;
                if ('thumbs' == $dir['path']  && $this->isImage){
                    $this->flags['hasThumb'] = true;
                    //  do not show the thumbs folder
                    continue;
                }
                $dir['type'] = 'dir';
                $this->subfolders[] = $dir;
                //  gallery flags
            } elseif($fileInfo->isFile()) {
                $file = array();
                $file['badName'] = false;

                $ext = strtolower($fileInfo->getExtension());
                if ('jpg' == $ext || 'png' == $ext || 'jpeg' == $ext ){

                    $file['badName'] = $this->checkImageName($pathName);
                    //  checkImageName may change $pathName
                    $safeName = str_replace('/', '£', $pathName);
                    
                    $file['type'] = 'image';
                    $file['showUrl'] = '/image/show/wMax/200/file/' . $safeName;
                    $file['editUrl'] = '/image/edit/pf/' . $safeName;
                } elseif ('qin' == $ext ){
                    $file['type'] = 'gal';
                    $this->flags['isGal'] = true;
                    $file['showUrl'] = $file['editUrl'] = $pathName;
                } else {
                    $file['type'] = 'file';
                    $file['showUrl'] = $file['editUrl'] = $pathName;
                }
                    
                $file['path'] = $pathName;
                    
                if ('gal' == $file['type']){
                    $this->flags['gal'] = $file;
                } else {
                    $this->files[] = $file;
                }
            }
        }
        //  if there is a thumbs directory, does it have a gal file?
        if ($this->hasThumb  && $this->isImage){
            $findGal = $this->getFullPath() . '/thumbs/gal.qin';
 //               echo $findGal; die;
            $this->flags['hasGal'] = file_exists($findGal)  ;
            $this->flags['qinPath'] = $findGal  ;
        }


        $parts = explode('/', trim($path, '/ .'));
        $this->flags['name'] = array_pop($parts);
        $this->flags['parent'] = implode('/', $parts);

         return $this;
    }

    /**
     * Validate and update $fileName
     * Replace spaces with -
     * Make all lower case
     *
     * If anything changes - copy file to new name.
     *
     * @param pointer to $fileName
     * @return string|boolean
     */
    protected function checkImageName(&$fileName)
    {
        $newName = strtolower($fileName);
        $newName = str_replace(' ', '-', $newName);

        if ($newName != $fileName){
            if (! $this->moveFile($fileName,$newName)){
                return 'FILE CANNOT BE RENAMED';
            }
            $fileName = $newName;
            return 'File has been renamed';
        }
        return false;

    }
    /**
     * Create the magic getter
     * If a method getName exists it is called,
     * else if a key 'name' exists, return it
     * else return false
     *
     * @param string $name field / info to get
     *
     * @throws  Exception
     * @return  mixed - value of item being got
     */
    public function __get($name)
    {
//#     \Qe\Debug::debugShow(get_class(), __FUNCTION__, 0, $name);
        $method = 'get' . ucfirst($name);

        if (method_exists($this, $method)) {
            return $this->$method();
        }
        if (array_key_exists($name, $this->flags)){
            return $this->flags[$name];
        } else {
            return false;
        }
        // should never get here
        throw new InvalidArgumentException('Invalid get property');

    }

    protected function getCrumbs() { return $this->crumbs; }
    protected function getSubfolders() { return $this->subfolders; }
    protected function getFiles() { return $this->files; }
    protected function getFlags() { return $this->flags; }

    protected function getQinPath(){

        if ($this->isThumb){
            return $this->getFullPath() . '/gal.qin';
        }
        return false;
    }

    public function  makeThumbs()
    {
        //  check we have a thumbs folder and create - return a new folder object
            $filePath = $this->path . '/thumbs/';
            return new  Folder($filePath);

    }

    /**
     *   base relative file copy
     */
    public function copyFile($fileName,$newName)
    {
        return copy($this->getBasePath() . '/' . $fileName,
        $this->getBasePath() . '/' . $newName);
    }

    /**
     *   base relative file move
     */
    public function moveFile($fileName,$newName)
    {
        if ($this->copyFile($fileName, $newName)){
            return unlink($this->getBasePath() . '/' . $fileName);
        }
    }


}
    /**
     * Running populate creates a structure like this:
     *
     * Crumbs subarray:
  ["crumb"] => array(3) {
        [0] => array(3) {
              ["editUrl"] => string(13) "/image/index/"
              ["path"] => string(3) "top"
              ["type"] => string(5) "crumb"
        }
        [1] => array(3) {
              ["editUrl"] => string(24) "/image/index/pf/£images"
              ["path"] => string(6) "images"
              ["type"] => string(5) "crumb"
        }
        [2] => array(3) {
              ["editUrl"] => string(39) "/image/index/pf/£images£sportsvillage"
              ["path"] => string(13) "sportsvillage"
              ["type"] => string(5) "crumb"
        }
  }
     *
     * subfolder subarray
     *
  ["dir"] => array(9) {
        [0] => array(3) {
              ["editUrl"] => string(45) "/image/index/pf/£images£sportsvillage£swim"
              ["path"] => string(4) "swim"
              ["type"] => string(3) "dir"
        }
        [1] => array(3) {
              ["editUrl"] => string(47) "/image/index/pf/£images£sportsvillage£change"
              ["path"] => string(6) "change"
              ["type"] => string(3) "dir"
        }
  }
     *
     * Files subarray
     *
  ["file"] => array(15) {
        [0] => array(4) {
              ["type"] => string(5) "image"
              ["showUrl"] => string(64) "/image/show/wMax/200/file/£images£sportsvillage£Picture31.jpg"
              ["editUrl"] => string(53) "/image/edit/pf/£images£sportsvillage£Picture31.jpg"
              ["path"] => string(35) "/images/sportsvillage/Picture31.jpg"
        }
        [1] => array(4) {
              ["type"] => string(5) "image"
              ["showUrl"] => string(74) "/image/show/wMax/200/file/£images£sportsvillage£computer-facilities.jpg"
              ["editUrl"] => string(63) "/image/edit/pf/£images£sportsvillage£computer-facilities.jpg"
              ["path"] => string(45) "/images/sportsvillage/computer-facilities.jpg"
        }
  }
}
     * 
     */
