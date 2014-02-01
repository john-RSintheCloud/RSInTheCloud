<?php

/**
 * Abstract asset model - basis for all assets
 *
 * @author John
 */
class resource_model_assetAbstract extends resource_model_abstract
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





}
