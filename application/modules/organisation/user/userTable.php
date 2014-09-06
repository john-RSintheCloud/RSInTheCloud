<?php

/*
 *org user table - adds org field to user table
 *
 * @author John
 */
class organisation_user_userTable extends database_table_user
{
    protected $table = 'user';


    protected $_orgSlug = '' ; // org slug

    //  use magic getters and setters in abstract.

    //  User specific methods

        public function toArray()
    {

        $ret = parent::toArray();

        $ret['organisation'] = $this->_orgSlug;

        return $ret;
    }

}
