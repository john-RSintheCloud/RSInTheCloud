<?php

namespace Framework;

use MongoClient;
use MongoGridFS;
use MongoId;
use RuntimeException;

/**
 * WW wrapper round Mongo Grid FS
 *
 * @author JohnB
 */
class wwMongoFile extends wwAbstract
{
    /**
     *
     * @var MongoClient injected
     */
    protected $db; 
    /**
     *
     * @var string default container
     */
    protected $fileStore = 'filestore'; 
    /**
     *
     * @var MongoGridFS 
     */
    protected  $gridfs ;
    
    public function setOptions($options = null)
    {
        parent::setOptions($options);
        
        if (! $this->db instanceof MongoClient){
            throw new RuntimeException('No Mongo client passed in');
        }
        
        $this->gridfs = $this->db->selectDB($this->fileStore)->getGridFS();
        
        return $this;
    }
    
        public function findOne($query = array(), $fields = array())
    {
        return $this->gridfs->findOne($query, $fields);
    }
    

    
    public function storePDF(array $options = [])
    {
        if (isset($options['fileName'])){
            $fileName = $options['fileName'];
        }else{
            $fileName= WkHtPd::getTempFileName('.pdf');
            $options['fileName'] = $fileName;
        }
        
        $options['contentType'] = 'application/pdf';
        
        return $this->gridfs->storeFile($fileName, $options);
        
    }
    
    /**
     * Store the given string as a pdf.
     * useage:
     * $id = $gridfs->storeStringAsPDF( $WkHtPd->getContents());
     * 
     * @param string $string file contents to save
     * @param array $options options for storeBytes
     * @return MongoId object with new id
     */
    public function storeStringAsPDF($string, array $options = [])
    {
        if (isset($options['fileName'])){
            $fileName = $options['fileName'];
        }else{
            $fileName= WkHtPd::getTempFileName('.pdf');
            $options['fileName'] = $fileName;
        }
        
        $options['contentType'] = 'application/pdf';
        
        return $this->gridfs->storeBytes($string, $options);
        
    }
    

}
