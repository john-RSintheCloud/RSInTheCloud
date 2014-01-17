<?php

namespace Qe;

/**
 * General file manipulation
 */
class File
{

    /**
     * ftp folder 
     *
     * Overwritten in implementations
     * 
     * @var string 
     */
    protected $ftpBase = '/httpdocs/';

    /**
     *
     * @var int FTP transfer type
     * - needs to be set to Binary for images
     */
    protected $ftpType = FTP_ASCII;

    /**
     *relative path to file.
     *  Append to ftpBase or basePath to generate $pushPath and $filePath
     * @var string
     */
    protected $relPath = '';

    /**
     * Path to the file base folder
     * set in setBasePath
     * default = realpath(APPLICATION_PATH . '/../' ) . '/data';
     *
     * @var string $basePath 
     */
    protected $basePath = ''; // /data

    /**
     * Path to the file
     * ALWAYS absolute
     *
     * @var string $filePath 
     */
    protected $filePath = '';

    /**
     * Path to save file - Absolute
     *
     * @var string $filePath
     */
    protected $targetPath = '';

    /**
     * FTP Path to file - Absolute
     *
     * @var string $filePath
     */
    protected $pushPath = '';



    /**
     * File parts - extension, etc
     * eg /home/jb/bob.jpg ...
     * 
     */
    protected $_fileParts = array(
            'ext' => '', // jpg   (no dot)
            'base' => '', //  bob  (no slashes or dots)
            'path' => array(), //  array('home','jb','bob.jpg') 
    );

    /**
     * Constructor
     * 
     * Pass in the full path to the file 
     * or the path relative to the base directory
     * or '' if file not yet written to disk.
     */
    public function __construct($path = '')
    {
//         \Qe\Debug::dump($path, 1, 'file72 - path:');

        //  set filename or base path if not given
        //  and run init (for extending)
        $this->setPath($path)
            ->init();

        return $this;
    }

    public function init()
    {
        return this;
    }

    public function getPath()
    {

        if (!$this->filePath) {
            throw new \InvalidArgumentException('Cannot access a file before locating it!');
        }

        return $this->filePath;
    }

    /**
     * Dirty way to avoid overwriting original file
     *
     * @todo Check new file does not exist
     *
     * @return string file path
     * @throws \InvalidArgumentException
     */
    public function getPathIncremented($filepath = '')
    {

        $filepath = $filepath ? $filepath : $this->getPath();



        return substr($filePath, 0, -4) . '1.' . $this->_image->getType();
    }

    /**
     * set path 
     * 
     * @param string $path
     * @return this
     * @throws \InvalidArgumentException
     */
    public function setPath($path = '')
    {

        if (file_exists($path)) {
            $basePath = $path;
        } else {
            $basePath = $this->getBasePath() . '/' . $path;
        }


        if (file_exists($basePath)) {
            $this->filePath = $basePath;
            $this->_fileParts = $this->fileInfo($basePath);
            $target = array_pop(explode('/', $basePath));
            $relPath = '';
            foreach ($this->_fileParts['path'] as $part) {
                $relPath .= '/' . $part;
                if ($part == $target){
                    $relPath = '';
                }
            }
            $this->relPath = $relPath;
            return $this->init();
        }

        throw new \InvalidArgumentException('Invalid file name supplied to retrieve a file: ' . $basePath);
    }

    public function getPushPath()
    {

        if (!$this->pushPath) {
            $this->setPushPath();
        }

        return $this->pushPath;
    }


    /**
     * set path
     *
     * @param string $path
     * @return this
     * @throws \InvalidArgumentException
     */
    public function setPushPath()
    {

        if (!$this->relPath){
            throw new \InvalidArgumentException('Cannot push a file before locating it!');
        }

        $this->pushPath = $this->ftpBase . $this->relPath;
        
        return $this;
    }

    protected function getBasePath()
    {

        if (!$this->basePath) {
            $this->setBasePath();
        }
        if (!$this->basePath || !is_dir($this->basePath)) {
            throw new \InvalidArgumentException('Invalid base path - ' . $this->basePath);
        }

        return $this->basePath;
    }

    protected function setBasePath($path = '')
    {

        $this->basePath = $path ? $path : realpath(APPLICATION_PATH . '/../') . '/data';

        return $this;
    }

    public function getTarget()
    {

        if (!$this->targetPath) {
            throw new \InvalidArgumentException('Target path not set. ' . $this->targetPath);
        }

        return $this->targetPath;
    }

    protected function setTarget($target)
    {

        //  validate the target.  Check directory exists and is writable, 
        //  and file has a valid extension.
        //  store relpath for use in generating ftp push path.
        $baseParts = $this->fileInfo($target);
        if (!$baseParts['base']) {
            throw new \InvalidArgumentException('No file name supplied to save a file: ' . $target);
        }

        //  pop off the filename
        $baseName = array_pop($baseParts['path']);
        //  what's left is the path
        $dirName = implode('/', $baseParts['path']);

        //  We do not allow absolute paths for security.
        $basePath = $this->getBasePath() . '/' . $dirName;

        if (is_writable($basePath)) {
            $this->relPath = $dirName . '/' . $baseName;
            $this->targetPath = realpath($basePath) . '/' . $baseName;
            return $this;
        }

        //  ok, we cannot write to the folder, so see if it exists

        if (file_exists($basePath) && !is_dir($basePath)) {
            throw new \InvalidArgumentException('Cannot create directory - file exists - ' . $basePath);
        }

        if (is_dir($basePath)) {
            throw new \InvalidArgumentException('Cannot write to directory - ' . $basePath);
        }

        //  try to create it
        if (!mkdir($basePath, 0777, true)) {
            throw new \InvalidArgumentException('Cannot create directory - ' . $basePath);
        }

        //  looks like mkdir worked, so store the target
        $this->targetPath = realpath($basePath) . '/' . $baseName;
# die ($this->targetPath . ' : lib-file 207');
        return $this;
    }

    public function saveAs($path = '')
    {
        if ($path) {
            $this->setTarget($path);
            copy($this->getPath(), $this->getTarget());
            return $this;
        }

        //  no path provided

        throw new \InvalidArgumentException('No path provided to save file.');
    }

    /**
     * Simple ftp push - pushes this file to destination path
     * relative to base path
     * eg pushFtp('/news/test/bob/new/image') with ftpBase set to '/httpdocs/images'
     *  will create a file called media.qe.org/images/news/test/bob/new/image.jpg
     * (including creating missing directories)
     * 
     * @param string       $destPath
     * @return \Qe\File    $this
     * @throws \InvalidArgumentException
     */
    public function pushFtp($destPath = '')
    {
        if (! $destPath){
            $destPath = $this->getPushPath();
        }
//                \Qe\Debug::dump($destPath, 1, 'file300');

        if ($destPath) {
            if (!file_exists($this->getPath())) {
                throw new \InvalidArgumentException('FTP push source file not found: ' . $this->getPath());
            }
            $this->ftpPush($this->getPath(), $destPath);
            return $this;
        }
        //  no path provided
        throw new \InvalidArgumentException('No path provided to save file via ftp.');
    }

    /**
     * More complete ftp pull - requires source and destination paths.
     *
     * @param string $source
     * @param string $target
     * @return \Qe\File
     * @throws \InvalidArgumentException
     */
    public function ftpPull($source, $target)
    {
        if ($source) {
            $conn = $this->ftpConnect();
            if (false === @ftp_get($conn, $target, $source, $this->ftpType)) {
                throw new \InvalidArgumentException("Could not pull file $source from media.qe!");
            }
            $close = ftp_close($conn);
        }
        return $this;
    }
    /**
     * More complete ftp push - requires source and destination paths.
     * Generates missing folders
     *
     * @param string $source
     * @param string $target
     * @return \Qe\File
     * @throws \InvalidArgumentException
     */
    public function ftpPush($source, $target)
    {
        if ($source) {
            if (!file_exists($source))
                throw new \InvalidArgumentException('File must exist before pushing');
            //  push the file
            $conn = $this->ftpConnect();
            if (false === @ftp_put($conn, $target, $source, $this->ftpType)) {
                //  cannot do simple push, so try iterative mkdir.
                if (false === $this->ftpMkDir($conn, $target, $source, $this->ftpType)) {
                    throw new \InvalidArgumentException("Could not save file $target to media.qe!");
                }
                if (false === ftp_put($conn, $target, $source, $this->ftpType)) {
                    //  still cannot push!
                    throw new \InvalidArgumentException("Still could not save file $target to media.qe!");
                }
            }
            $close = ftp_close($conn);
        }
        return $this;
    }

    public function ftpMkDir($conn, $path, $isFile = false)
    {
        if (@ftp_chdir($conn, $path)) {
            if (!$isFile) {
                return true; // path already exists, we're done
            }
            //  we are trying to save a file with the same name as a directory
            throw new \InvalidArgumentException("Could not save file $target to media.qe - a directory with this name already exists!");
        }

        $directories = explode("/", $path);
        if ($isFile) {
            //  last entry is a file name, so pop it
            $fname = array_pop($directories);
        }
        $path = $this->ftpBase; //  ends in /
        ftp_chdir($conn, $this->ftpBase);

        foreach ($directories as $dir) {
            $path .= $dir . '/';

            if (!@ftp_chdir($conn, $path)) {
                if (!ftp_mkdir($conn, $path)) {
                    throw new \InvalidArgumentException("Could not make folder $path on media.qe");
                    return false; // failed to create the directory
                }
            }
        }

        ftp_chdir($conn, $this->ftpBase);
        return true;
    }

    /**
     * 
     * @return resource a FTP stream on success or <b>FALSE</b> on error.
     */
    protected function ftpConnect()
    {
        $conn = ftp_connect("qe.org");
        if ($conn){
            ftp_login($conn, "media", "IWEX8todXHaBpszk");
            ftp_chdir($conn, $this->ftpBase);
        }
        return $conn;
    }

    

    static public function fileInfo($baseName)
    {
        $ret = array();
        $parts = explode('/', trim($baseName, '/ .'));
        $ret['path'] = $parts;

        $base = array_pop($parts);

        $dotpos = strrpos($base, '.');
        if ($dotpos) {
            $ret['ext'] = strtolower( substr($base, $dotpos + 1));
            $ret['base'] = substr($base, 0, $dotpos);
        } else {
            $ret['ext'] = '';
            $ret['base'] = $base;
        }

        return $ret;
    }

    public function listFiles()
    {
        $path = $this->getTarget();
        var_dump (glob($path . '/*')); die;

    }
}