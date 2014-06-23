<?php

/**
 * Description of user
 *
 * @author John
 */
class user_model_user extends abstract_model_abstract
{

    //  User specific methods

    public function isLoggedIn()
    {
        return (boolean) $this->loggedIn;
    }

    public function isUsernameInUse($name)
    {

        $user = $this->fetchOne(array('username' => $name));
        return (boolean) $this->ref;
    }

    public function isEmailInUse($email)
    {

        $user = $this->fetchOne(array('email' => $email));
        return (boolean) $this->ref;
    }

}
