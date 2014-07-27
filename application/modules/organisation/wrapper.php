<?php

/**
 * organisation Wrapper
 * A wrapper round the org plugin
 *
 * @author John Brookes <john@RSintheCloud.com>
 * @package RSintheClouds
 * @subpackage OORS
 */

/**
 * Ogganisations are groups of users, providing authentication and
 * access control features.
 *
 * A user can only belong to one organisation
 *
 * all resources and assets belonging to an organisation are stored in a bucket.
 *
 * This is the first trial plugin using the DIC - so let's see how it works!
 * This iteration has no CRUD; it is intended purely so a user can log in
 * and only see organisation assets.
 */


//  Overwrite user model in DIC
//  we may want to manage multiple users at once
//  so do not share this model.
$container['user'] =  function ($c) {
    //  use organisation user model
    $user = new organisation_user_user();
    $user->setMapper($c['userMapper']);
    return $user;
};

