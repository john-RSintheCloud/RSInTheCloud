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
 */


//  Overwrite user model in DIC
//  User model requires user mapper.
//  There may be multiple users at any one time
$container['user'] =  function ($c) {
    $user = new organisation_user_user();
    $user->setMapper($c['userMapper']);
    return $user;
};

