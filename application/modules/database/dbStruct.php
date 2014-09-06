<?php

/**
 * RS uses dbStruct files to manage schema changes.  It's not perfect,
 * but it works and we will carry on using it for now.
 * 
 * As they hold the definitive structure, we will use the dbstruct files 
 * in our mappers so we can make them generic
 * 
 * This is a factory class to create, cache and manage tableStruct objects
 *
 * 
 * $this->description holds a cached set of tableStruct objects
 * format $this->description->tableName->object
 *
 * @author John
 */
class database_dbStruct extends abstract_model_arrayAbstract
{


    /**
     *
     * @var string - full path to dbstruct - injected
     */
    protected $dbStructPath = '';


    public function getDbStruct($table = '')
    {
        $table = (string) $table;
        if (empty($table)){
            throw new \InvalidArgumentException ('no table name given in getDbStruct');
            
        }

        if (! isset($this->description)){
            $this->description = new abstract_model_arrayAbstract();
        }
        //  see if already loaded
        if (isset($this->description->$table)){
            return $this->description->$table;
        } 
        
        
        $path = $this->dbStructPath . "table_" . $table . ".txt";
        $f = @fopen($path, "r");
        if ($f === false) {
            throw new \InvalidArgumentException ('File: ' . $path . ' not found');
        }

        $fields = array();
        while ( ($fields[] = fgetcsv($f)) !== false){
//            load the csv into the array
        }

        fclose($f);
        
        $this->description->$table = new database_tableStruct(['fields' => $fields]);
       return $this->description->$table;

    }

    public function getTableNames()
    {
        if (empty($this->description)){
            throw new RuntimeException('no tables loaded to get table names');
        }
        $names = array();
        foreach ($this->description as $tableName => $table) {
            $names[] = $tableName;
        }

        return $names;
    }


    public function createDbStruct($param = array())
    {
        //  Defaults

        $dbstructPath = $this->dbStructPath . 'xx';

        # Specify whether you want to have table_* and index_* files created
        $createTableStructure = true;
        $createIndices = true;
        $createData = true;

        # Use the below to set which tables we will extract data for - empty array means all tables.
        #$dataFor=array("usergroup","resource_type_field","site_text","user","collection","user_collection","report","preview_size","resource_type");
        $dataFor = array();
        $tableFor = array();
        $indicesFor = array();

        //  db must be injected
        $db = null;

        //  overwrite defaults - including db

        foreach ($param as $pkey => $pvalue) {
            $$pkey = $pvalue;
        }

        if ($db instanceof database_Db){
            return 'No database injected';
        }

        # Fetch all tables
        if (! $db->sqlQuery("show tables")){
                return 'Invalid Database';
        }
        $tables = $db->getData();

        foreach( $tables as $tableArray) {
            $table = reset($tableArray);

            # Table structure
            if ($createTableStructure && (in_array($table, $tableFor) || count($tableFor) === 0)) {
                $f = fopen($dbstructPath . "table_" . $table . ".txt", "w");
                $describe = sql_query("describe $table");
                for ($m = 0; $m < count($describe); $m++) {
                    fputcsv($f, $describe[$m]);
                }
                fclose($f);
            }

            # Indices
            if ($createIndices && (in_array($table, $indicesFor) || count($indicesFor) === 0)) {
                $f = fopen($dbstructPath . "index_" . $table . ".txt", "w");
                $index = sql_query("show index from $table");
                for ($m = 0; $m < count($index); $m++) {
                    fputcsv($f, $index[$m]);
                }
                fclose($f);
            }

            # Data
            if ($createData && (in_array($table, $dataFor) || count($dataFor) === 0)) {
                $f = fopen($dbstructPath . "data_" . $table . ".txt", "w");
                $index = sql_query("select * from $table");
                for ($m = 0; $m < count($index); $m++) {
                    fputcsv($f, $index[$m]);
                }
                fclose($f);
            }
        }
        $ret = '<p><b>Writing to ' . $dbstructPath . '</b></p>';
        $ret .= self::printArray('Created tables', $createTableStructure, $tableFor);
        $ret .= self::printArray('Created indices for tables', $createIndices,
                $indicesFor);
        $ret .= self::printArray('Created data for tables', $createData, $dataFor);

        return $ret;
    }

    static public function printArray($label, $show, $array)
    {
//        var_dump($show); die;
        if (!$show)
            return;
        $ret = '<p><b>' . $label . ':</b> ';
        if (count($array) == 0) {
            $ret .= 'for all tables</p>';
            return $ret;
        }

        $first = true;
        foreach ($array as $item) {
            if ($first)
                $first = false;
            else
                $ret .= ', ';
            $ret .= $item;
        }
        $ret .= '</p>';

        return $ret;
    }

}
