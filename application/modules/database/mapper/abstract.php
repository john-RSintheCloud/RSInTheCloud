<?php

/**
 * abstract table mapper class
 *
 * This can be implemented directly in the DIC if there are no fields need mapping,
 * or extended to give mapped field names
 *
 * A mapper can be shared by several models, so it cannot retain data between requests
 *
 * @author John
 */
class database_mapper_abstract
{

    /**
     *   table injected via DIC
     *
     * @var  database_table_abstract
     */
    protected $table;

    /**
     * @var array of field names which need 'mapping'.
     *              All fields not in this array are mapped across unchanged.
     */
    protected $mapArray = array();


    /**
     * Get table
     *
     * @return database_table_abstract
     */
    protected function getTable()
    {
        return $this->table;
    }

    /**
     * Set the table - injected via DIC
     *
     * @param database_table_abstract $table
     * @return \database_mapper_abstract
     */
    public function setTable(database_table_abstract $table)
    {
        $this->table = $table;

        //  retrieve the field list from the table and inject into mapArray
        $fields = $this->table->getFields();
        $newMap = array_combine($fields, $fields);
        $this->mapArray = array_merge($newMap, $this->mapArray);

        return $this;
    }



    //  common methods

    /**
     * Save a model using models to_array method.
     * If Ref / Slug are missing, insert the row;
     * otherwise update it.
     *
     * @param abstract_model_dataAbstract $model
     * @return array model data with new ref / slug if appropriate
     */
    public function save(abstract_model_dataAbstract $model)
    {
        //  overload to validate input type - should be appropriate model

        $inArray = $model->toArray();
        return $this->saveArray($inArray);
    }

    /**
     * Save a record from an array.
     * If Ref / Slug are missing, insert the row;
     * otherwise update it.
     *
     * @param array $inArray
     * @return type
     */
    public function saveArray(array $inArray)
    {
        $retArray = $this->switchArrays($inArray);

        //  currently using arrays to pass parameters to the table,
        //  so our mapper is simply:
        $retval = $this->getTable()->save($retArray);
        return $this->switchArrays($retval);
    }

    /**
     * fetch rows from the database according to $param
     *
     * @param array $param
     * @return array of arrays
     */
    public function fetch(array $param = array())
    {
        $records = $this->table->fetch($param);
        if (empty($records)){
            return false;
        }
        $rowArray = array();
        foreach ($records as $row) {
            $rowArray[] = $this->switchArrays($row);
        }
        return $rowArray;
    }

    /**
     * fetch one row from the database, according to $param
     * This is just the first row found - not guaranteed to be the only one
     *
     * @param array $param
     * @return array
     */
    public function fetchOne($param = array())
    {
        $records = $this->table->fetch($param);
        $row = reset($records);

        return $this->switchArrays($row);
    }

    /**
     *
     * @param array $where
     * @return boolean
     */
    public function delete( $where = array())
    {
        return $this->table->delete(
            $this->switchArrays($where));
    }


    /**
     * The actual mapper - recursively replace keys with mapped keys.
     *
     * @param array $inArray
     * @return array
     */
    protected function switchArrays (array $inArray)
    {

        foreach ($inArray as $inKey => $inValue) {

            if (is_array($inValue)){
                $retArray[$inKey] = $this->switchArrays($inValue);
            } elseif(array_key_exists($inKey, $this->mapArray)) {
                $retArray[$this->mapArray[$inKey]] = $inValue;
            } else {
                $retArray[$inKey] = $inValue;
                
            }
        }

        return $retArray;

    }

    public function getFields()
    {

        $fields = $this->table->getFields();
        $retArray = array();
        foreach($fields as $field){
            $retArray[] = $this->mapArray[$field];
        }
        return $retArray;
    }

    /**
     * Legacy code expects field names from the table, so map them back
     *
     * @param abstract_model_dataAbstract $model
     * @return array
     */
    public function toLegacyArray(abstract_model_dataAbstract $model)
    {
        return $this->switchArrays($model->toArray());
    }

}
