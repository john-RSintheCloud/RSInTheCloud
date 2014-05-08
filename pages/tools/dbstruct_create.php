<?php
include "../../application/bootstrap.php";
include "authenticate.php";
if (!checkperm("a")) {
    exit("Permission denied");
}


$param = array(
    # Specify whether you want to have table_* and index_* files created
    'createTableStructure' => true,
    'createIndices' => true,
    'createData' => false,
    # Use the below to set which tables we will extract data for - empty array means all tables.
    #'dataFor' => array("usergroup","resource_type_field","site_text","user","collection","user_collection","report","preview_size","resource_type"),
    'dataFor' => array(),
    'tableFor' => array(),
    'indicesFor' => array(),
    'dbstructPath' => APPLICATION_PATH . 'modules/database/dbstructxx/',
    'db' => $container['db']
);
if (getval("execute", "") != "") {

    echo database_dbStruct::createDbStruct($param);
} else {
    ?>
    <p>This tool is for developers only.</p>
    <p>It (re)creates the database structures defined in the 'dbstruct' folder
        using the current database as a master. Do not run this unless you are
        sure you want to recreate the files.</p>
    <p>Do not commit the changed dbstruct files to live
        unless you intend to alter the database structure for all installations.</p>
    <p>Set to write files to <?php echo $param['dbstructPath']; ?> </p>
    <?php
    echo database_dbStruct::printArray('Creates tables',
            $param['createTableStructure'], $param['tableFor']);
    echo database_dbStruct::printArray('Creates indices for tables',
            $param['createIndices'], $param['indicesFor']);
    echo database_dbStruct::printArray('Creates data for tables',
            $param['createData'], $param['dataFor']);
    ?>
    <form method="post">
        <input type="submit" name="execute" value="Execute">
    </form>
    <?php
}
?>
