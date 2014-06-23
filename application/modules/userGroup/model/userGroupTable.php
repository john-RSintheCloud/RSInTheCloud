<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of userTable
 *
 * @author John
 */
class user_model_userTable extends database_table_abstract
{
    protected $table = 'user';


    protected $_username = '' ;  //varchar(50) DEFAULT NULL,
    protected $_password = '' ;  //varchar(50) DEFAULT NULL,
    protected $_fullname = '' ;  //varchar(100) DEFAULT NULL,
    protected $_email = '' ;  //varchar(100) DEFAULT NULL,
    protected $_usergroup = 0 ;  //int(11) DEFAULT NULL,
    protected $_last_active = 'now' ;  //datetime DEFAULT NULL,
    protected $_logged_in = '' ;  //int(11) DEFAULT NULL,
    protected $_last_browser = '' ;  //text,
    protected $_last_ip = '' ;  //varchar(100) DEFAULT NULL,
    protected $_current_collection = '' ;  //int(11) DEFAULT NULL,
    protected $_accepted_terms = '' ;  //int(11) DEFAULT '0',
    protected $_account_expires = '' ;  //datetime DEFAULT NULL,
    protected $_comments = '' ;  //text,
    protected $_session = '' ;  //varchar(50) DEFAULT NULL,
    protected $_ip_restrict = '' ;  //text,
    protected $_password_last_change = '' ;  //datetime DEFAULT NULL,
    protected $_login_tries = '' ;  //int(11) DEFAULT '0',
    protected $_login_last_try = '' ;  //datetime DEFAULT NULL,
    protected $_approved = '' ;  //int(11) DEFAULT '1',
    protected $_lang = '' ;  //varchar(11) DEFAULT NULL,
    protected $_created = '' ;  //timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,

    //  use magic getters and setters in abstract.

    //  User specific methods

        public function toArray()
    {

        $ret = parent::toArray();

        $ret['username'] = $this->username;
        $ret['password'] = $this->password;
        $ret['fullname'] = $this->fullname;
        $ret['email'] = $this->email;
        $ret['usergroup'] = $this->usergroup;
        $ret['last_active'] = $this->last_active;
        $ret['logged_in'] = $this->logged_in;
        $ret['last_browser'] = $this->last_browser;
        $ret['last_ip'] = $this->last_ip;
        $ret['current_collection'] = $this->current_collection;
        $ret['accepted_terms'] = $this->accepted_terms;
        $ret['account_expires'] = $this->account_expires;
        $ret['comments'] = $this->comments;
        $ret['session'] = $this->session;
        $ret['ip_restrict'] = $this->ip_restrict;
        $ret['password_last_change'] = $this->password_last_change;
        $ret['login_tries'] = $this->login_tries;
        $ret['login_last_try'] = $this->login_last_try;
        $ret['approved'] = $this->approved;
        $ret['lang'] = $this->lang;
        $ret['created'] = $this->created;

        return $ret;
    }

}
