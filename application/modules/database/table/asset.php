<?php

/**
 * assets - the smallest unit of a resource
 *
 * This is the abstract asset -
 * you need to extend it for any real life situation!
 *
 * This table model acts as mapper, accepting and returning asset models
 * and mapping these to table rows.
 *
 * @author John
 */
class database_table_asset extends database_table_abstract
{

    protected $table = 'asset';

    protected $_ref = ''; // int(11) NOT NULL AUTO_INCREMENT,
    protected $_slug = ''; // varchar(200) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
    protected $_status = '0'; // char(1) DEFAULT '0',
    protected $_owner = 0; // int(11) 0 = global
    protected $_file_path = ''; //  varchar(500) DEFAULT NULL,
    protected $_file_extension = ''; //  varchar(5) NOT NULL,
    protected $_file_checksum = ''; //  varchar(32) DEFAULT NULL,
    protected $_file_size = ''; //  int(11) DEFAULT NULL,

    //  Use magic getters and setters


    //  Asset-specific Functionality

    public function deleteAssetBySlug($slug)
    {
        $this->delete(array('slug' => $slug));
    }

    public function fetchAnAsset(array $options = array())
    {
        $rowset = $this->fetch($options);
        if (count($rowset) > 0 ) {
            return new resource_model_assetAbstract ($rowset[0]);
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

    public function saveAsset(resource_model_assetAbstract $asset)
    {
        $this->save($asset->toArray);
    }


}
