<?php

namespace OPI\UserModel;

require_once 'rb/rb.php';

function userexists( $id )
{
	$user = \R::find( "user", "where username = :id", [ ':id' => $id]);

	if( count( $user ) == 0 )
	{
		return False;
	}

	return true;
}


function getuser( $id )
{
    // First try by id

    $user = \R::load( "user", $id );

    if ( $user->id == 0 )
    {
            // Try by username
            $user = \R::find( "user", "where username = :id", [ ':id' => $id]);
            if( count($user) > 0 )
            {
                    $user = reset( $user) ;
            }
    }

    return $user ? $user->export() : false;
}

function getusers()
{
	$users = \R::findAll( "user" );

	return \R::exportAll( $users );
}

function deleteuser( $id )
{
    $user = \R::load( "user", $id );

    if ( $user->id == 0 )
    {
            // Try by username
            $user = \R::find( "user", "where username = :id", [ ':id' => $id]);
            if( count($user) > 0 )
            {
                    $user = reset( $user) ;
            }
            else
            {
                    return false;
            }
    }

    \R::trash($user);

    return true;
}

function deleteusers()
{
    return \R::wipe( "user" );
}

/*
 * $user: array with at least username and password
 */
function createuser( $user )
{
    // Check if user exists
    $tmpuser = \R::find( "user", "where username = :id", [ ':id' => $user["username"]]);
    if( count($tmpuser) > 0 )
    {
        return false;
    }

    $u = \R::dispense("user");

    $u["username"] 		= $user["username"];
    $u["displayname"]           = $user["displayname"];
    $u["password"]		= $user["password"];

    return \R::store( $u );

}

/*
 * $user, array with at least username or id
 */
function updateuser($user)
{
    $tmpuser = array();

    if(array_key_exists("id", $user) )
    {
        $tmpuser = \R::find( "user", "where id = :id", [ ':id' => $user["id"]]);
    }
    else if(array_key_exists("username", $user) )
    {
        $tmpuser = \R::find( "user", "where username = :id", [ ':id' => $user["username"]]);
    }
    else
    {
        return false;
    }

    if( count($tmpuser) == 0 )
    {
        return false;
    }
    $tmpuser = reset($tmpuser);
    $tmpuser->username      = $user['username'];
    $tmpuser->displayname   = $user['displayname'];
    $tmpuser->password      = $user['password'];
    \R::store( $tmpuser );
    return true;
}
