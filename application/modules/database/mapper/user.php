<?php

/**
 *  User table mapper class
 *
 * Extends abstract mapper to supply list of mapped fields.
 *
 * @author John
 */
class database_mapper_user extends database_mapper_abstract
{

    /**
     *
     * @var array of field names which need 'mapping'.
     *
     */
    protected $mapArray = array(

        'last_active' => 'lastActive',
        'lastActive' => 'last_active',

        'logged_in' => 'loggedIn',
        'loggedIn' => 'logged_in',

        'lastBrowser' => 'last_browser',
        'last_browser' => 'lastBrowser',

        'last_ip' => 'lastIp',
        'lastIp' => 'last_ip',

        'current_collection' => 'currentCollection',
        'currentCollection' => 'current_collection',

        'accepted_terms' => 'acceptedTerms',
        'acceptedTerms' => 'accepted_terms',

        'account_expires' => 'accountExpires',
        'accountExpires' => 'account_expires',

        'ip_restrict' => 'ipRestrict',
        'ipRestrict' => 'ip_restrict',

        'password_last_change' => 'passwordLastChange',
        'passwordLastChange' => 'password_last_change',

        'login_tries' => 'loginTries',
        'loginTries' => 'login_tries',

        'login_last_try' => 'loginLastTry',
        'loginLastTry' => 'login_last_try',
    );


}
