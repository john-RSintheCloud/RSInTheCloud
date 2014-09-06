<?php

/**
 * RS uses dbStruct files to manage schema changes.  It's not perfect,
 * but it works and we will carry on using it for now.
 * 
 * As they hold the definitive structure, we will use the dbstruct files 
 * in our mappers so we can make them generic
 *
 * This class encapulates the individual tables.
 *
 * @author John
 */
class database_tableStruct extends abstract_model_arrayAbstract
{

//    ref,int(11),NO,PRI,,auto_increment
//    username,varchar(50),YES,,,

    public function setFields($desc)
    {
        if (!is_array($desc)){
            $desc = $desc->toArray();
        }
        $ret = [];
        foreach ($desc as $field) {
            if (!is_array($field) || count($field) != 6){
                continue;
            }
            $fname = $field[0];
//            echo $fname;
            $ret[$fname] = [
                'fName' => $field[0],
                'fType' => $field[1],
                'fNull' => $field[2],
                'fKey' => $field[3],
                'fDefault' => $field[4],
                'fTag' => $field[5],
//                'ftype' => $field[6],
            ];
            $this->fields = new abstract_model_arrayAbstract($ret);
        }
        
    }

    public function getFieldNames()
    {
        $names = array();
        foreach ($this->fields as $fName => $field) {
            $names[$fName] = $fName;
        }

        return $names;
    }


}
