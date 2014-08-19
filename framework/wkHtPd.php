<?php


namespace Framework;

use WkHtmlToPdf;
// include '../vendor/mikehaertl/phpwkhtmltopdf/WkHtmlToPdf.php';

/**
 * Our version of WkHtmlToPdf.php';
 *
 * @author JohnB
 */
class WkHtPd extends WkHtmlToPdf
{
    //  set base options
    protected $options = array(
    'no-outline',         // Make Chrome not complain
    );
    
    protected $pageOptions = array(
            'encoding' => 'UTF-8',
            );

    public function __construct($options = array())
    {

        parent::__construct();
        //  set base options
        $this->setOptions($this->options);
        //  overwrite / add passed in options
        $this->setOptions($options);
        // Set default page options 
        $this->setPageOptions($this->pageOptions);
    }
    
    /** parent::setPageOptions($options) overwrites the pageoptions array
     *  but we want to extend it
     * 
     * @param type $options
     */
    public function setPageOptions($options = array())
    {
        
        $options = $this->processOptions($options);
        foreach ($options as $key=>$val) {
            $this->options[$key] = $val;
        }
    
        return $this;
    }

    /** generate a unique temp file path
     * 
     * @param string $ext extension (with . , eg '.pdf')
     * @return string unique temp file name
     */
    static public function getTempFileName ($ext = '')
    {
        //  get temp dir
        $newMe = new self;
        $tempDir = $newMe->getTmpDir();
        $filename = microtime(true);
        return $tempDir . '/tmp' . $filename . $ext;
    }
    
    /**
     * return PDF to caller
     *
     * @param mixed $filename the filename to send. If empty, the PDF is streamed inline.
     * @param bool $inline whether to force inline display of the PDF, even if filename is present.
     * @return bool whether PDF was created successfully
     */
    public function getContents()
    {
        if (($pdfFile = $this->getPdfFilename())===false) {
            return false;
        }

        return file_get_contents($pdfFile);
    }

}
