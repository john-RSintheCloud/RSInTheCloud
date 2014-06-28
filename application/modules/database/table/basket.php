<?php

/**
 * Baskets - an assorted group of assets
 *
 * This table model acts as mapper, accepting and returning basket models
 * and mapping these to table rows.
 *
 * @author John
 */
class database_table_basket extends database_table_abstract
{

    protected $table = 'basket';

    protected $_ref; // int(11) NOT NULL AUTO_INCREMENT,
    protected $_slug; // varchar(100) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
    protected $_asset; //  varchar(100) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
    protected $_type; // char(2)

    //  Basket-specific Functionality

    /**
     * delete the basket (not the assets)
     *
     * @param string $slug
     */
    public function deleteBasketBySlug($slug)
    {
        $this->delete(array('slug' => $slug));
    }

    /**
     * Get the basket and all assets
     *
     * @param type $slug
     * @return array of assets
     */
    public function fetchAssetsInBasket($options)
    {
        $this->setOptions($options);

        $sql = 'SELECT asset.* FROM `basket` left join `asset` on basket.asset = asset.slug ';

        if (is_array($options && count($options) > 0)) {
            $where = ' WHERE ';

            foreach ($options as $key => $value) {

                $where .= $key . " = '" . $value . "', ";
            }
            $where = trim($where, ', ') . ' ; ';
            $sql .= $where;
        }

               echo $sql;

        if ($this->getDb()->sqlQuery($sql)) {
            $rowset =  $this->getDb()->fetchAll();
        }

        $ret = array();
        foreach ($rowset as $row) {
            $ret = new resource_model_assetAbstract ($row);
        }
        return $ret;
    }


    public function saveBasket(resource_model_basket $basket)
    {
        $this->save($basket->toArray);
    }


}
