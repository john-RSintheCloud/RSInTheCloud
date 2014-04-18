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

    //  Asset-specific Functionality

    public function fetchAssets(array $options = array())
    {
        $rowset = $this->fetch($options);
        $ret = array();
        foreach ($rowset as $row) {
            $ret = new resource_model_assetAbstract ($row);
        }
        return $ret;
    }

    public function save(asset_model_assetAbstract $asset)
    {
        parent::save($asset);
    }



}
