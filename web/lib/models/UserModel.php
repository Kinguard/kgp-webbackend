<?php

namespace OPI\UserModel;

require_once 'rb/rb.php';

/*
 * Helper functions
 */
function _getuser( $id )
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

    return $user ? $user : false;
}


/*
 * Public functions
 */
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

    $user = _getuser($id );

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

function updatepassword( $user, $new, $old)
{
    $u = _getuser($user);

    if ( !$u )
    {
        return false;
    }

    // Logged in user has to provide old password to change own pwd
    if( $user == \OPI\session\user() )
    {
        if( ($old=="") || ($old == null) )
        {
            return false;
        }

        if( $old != $u["password"] )
        {
            return false;
        }
    }

    // If not admin old password is needed and you can
    // only change your own password
    if( !isadmin() )
    {
        if( $user != \OPI\session\user() )
        {
            return false;
        }

        if ( $u["password"] != $old )
        {
            return false;
        }
    }

    $u["password"] = $new;

    \R::store($u);

    return true;
}
