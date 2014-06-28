<?php

/**
 * Resources - the normal work unit
 *
 * This table model acts as mapper, accepting and returning resource models
 * and mapping these to table rows.
 *
 * @author John
 */
class database_table_resource extends database_table_abstract
{
    protected $table = 'res';

    protected $_title; // varchar(200) NOT NULL,
    protected $_slug; // varchar(100) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
    protected $_status; // char(1) DEFAULT '0',
    protected $_owner; // int(11) NOT NULL,
    protected $_date_created = 'now'; //  datetime NOT NULL,
    protected $_date_modified = 'now'; //  datetime DEFAULT NULL,

    protected $_original_asset; // varchar(100)  NOT NULL,
    protected $_preview_asset; // varchar(100)  NOT NULL,
    protected $_basket; // varchar(100)  NOT NULL,
    protected $_alt_basket; // varchar(100)  NOT NULL,,


    //  Asset-specific Functionality

    /**
     * Deleting a resource involves deleting all the assets and baskets.
     *
     * @param string $slug
     */
    public function deleteResourceBySlug($slug)
    {
        //  get the fully populated resource
        $this->fetchAllAssets();
        $this->delete(array('slug' => $slug));
    }

    public function fetchAllAssets()
    {
        return $this->fetch();
    }

    public function saveAsset(resource_model_assetAbstract $asset)
    {
        $this->save($asset->toArray);
    }



   public function getSlug()
    {
        return $this->_slug;
    }

    public function getStatus()
    {
        return $this->_status;
    }

    public function getOwner()
    {
        return $this->_owner;
    }

    public function setSlug($slug)
    {
        $this->_slug = $slug;
        return $this;
    }

    public function setStatus($status)
    {
        $this->_status = $status;
        return $this;
    }

    public function setOwner($owner)
    {
        $this->_owner = $owner;
        return $this;
    }

    public function setFile_path($file_path)
    {
        $this->_file_path = $file_path;
        return $this;
    }

    public function setFile_extension($file_extension)
    {
        $this->_file_extension = $file_extension;
        return $this;
    }

    public function setFile_checksum($file_checksum)
    {
        $this->_file_checksum = $file_checksum;
        return $this;
    }

    public function setFile_size($file_size)
    {
        $this->_file_size = $file_size;
        return $this;
    }



}
