<?php

/**
 * Abstract asset model - basis for all assets
 *
 * @author John
 */
class asset_model_assetAbstract extends abstract_model_dataAbstract
{

    /**
     *aset type - 2 or 3 character string used as suffix on table and class names.
     * May be the file extension (PDF) or more generic (Img)
     *
     * so, resource_model_assetPDF is stored in a database_table_assetPDF,
     * and will have a slug file-name-and-owner-pdf
     *
     * @var string
     */
    protected $assType = 'abs';


    protected $_ref = ''; // int(11) NOT NULL AUTO_INCREMENT,
    protected $_slug = ''; // varchar(200) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
    protected $_status = '0'; // char(1) DEFAULT '0',
    protected $_owner = 0; // int(11) 0 = global
    protected $_file_path = ''; //  varchar(500) DEFAULT NULL,
    protected $_file_extension = ''; //  varchar(5) NOT NULL,
    protected $_file_checksum = ''; //  varchar(32) DEFAULT NULL,
    protected $_file_size = ''; //  int(11) DEFAULT NULL,


    protected $_metadata = ''; //  json encoded string of metadata


    public function deleteAssetBySlug($slug)
    {
        $this->delete(array('slug' => $slug));
    }

    public function hydrate(array $options = array(), $overwrite = false)
    {
        if (empty($options)) {
            if (empty($this->_slug)){
                return false;
            }
            $options['slug'] = $this->slug;
        }

        $newValues = $this->getDbClass()->fetchAnAsset($options);


        if ($overwrite){
            $this->setOptions($newValues->toArray());
        } else {
            $this->merge($newValues->toArray());
        }


        return false;
    }

    public function fetchAssets(array $options = array())
    {
        $rowset = $this->fetch($options);
        $ret = array();
        foreach ($rowset as $row) {
            $ret = new resource_model_assetAbstract ($row);
        }
        return $ret;
    }

    public function save( $options = null )
    {
        $this->setOptions($options);
        return parent::save();
    }

    public function toArray()
    {

        $ret = parent::toArray();

        $ret['slug'] = $this->slug;
        $ret['status'] = $this->status;
        $ret['owner'] = $this->owner;
        $ret['file_path'] = $this->file_path;
        $ret['file_extension'] = $this->file_extension;
        $ret['file_checksum'] = $this->file_checksum;
        $ret['file_size'] = $this->file_size;
        $ret['metadata'] = $this->metadata;
        $ret['assType'] = $this->assType;

        return $ret;
    }

}
