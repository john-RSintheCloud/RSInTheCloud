<?php

/**
 * Resource model - the most important model in RS
 *
 * @author John
 */
class resource_model_resource extends resource_model_abstract
{

    protected $_slug = ''; // varchar(200) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,

    //  a resource consists of a collection of assets and related metadata.
    //  2 assets are very important and are tied closely to a resource:

    protected $_original;
    protected $_preview;

    //  Other assets are stored in a basket
    protected $_assets;

    //  There may also be alternative assets - different views, shorter versions, crops, etc
    protected $_alternativeAssets ;


    //  Metadata:  Some metadata is sortable;  this is ept in fields.
    //  The abstract defines date-created, date-modified, etc.  We want resource-specific fields

    protected $_owner;

    protected $_title; // varchar(200) NOT NULL,
    protected $_status; // char(1) DEFAULT '0',


    //  the abstract defines magic getters and setters, which will do for now.

}
