<?php

/*
 *  Extend the user model to add the organisation field
 *
 * @author John
 */
class organisation_user_user extends user_model_user
{
    protected $_orgSlug = '' ;  // slug for this users organisation

    //  Org User specific methods

    public function isLoggedIn()
    {
        return $this->loggedIn;
    }
}
