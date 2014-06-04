<?php
namespace OPI\GroupModel;

require_once 'rb/rb.php';

/*
 * internal helper functions
 */
function getgroup( $group )
{
	$g = \R::find( "group", "where name = :group", [ ':group' => $group]);

	if( count( $g ) == 0 )
	{
		return False;
	}

	return reset($g);
}

function getuser( $id )
{
	$user = \R::find( "user", "where username = :id", [ ':id' => $id]);

	if( count( $user ) == 0 )
	{
		return False;
	}

	return reset($user);
}

/*
 * Public functions
 */
function groupexists( $group )
{
	$g = \R::find( "group", "where name = :group", [ ':group' => $group]);

	if( count( $g ) == 0 )
	{
		return False;
	}

        return true;
}

function getgroups()
{

	$groups = \R::findAll( "group" );

	return \R::exportAll( $groups );
}

function getusers( $group )
{
	$g = getgroup( $group );

	return \R::exportAll( $g->sharedUserList );
}

function creategroup( $group )
{
	$g = \R::dispense( "group" );

	$g->name = $group;

	return \R::store( $g );
}

function adduser($group, $user)
{

	$gid = getgroup( $group );
	$uid = getuser( $user );

	if( !$gid || !$uid )
	{
		return false;
	}

	$gid->sharedUserList[] = $uid;

	\R::storeAll( [$gid, $uid] );

        return true;
}

function deletegroups()
{
	\R::wipe( "group" );
}

function deletegroup($group)
{

	$g = getgroup( $group );

	if( ! $g )
	{
            return false;
	}

	\R::trash( $g );

        return true;
}

function useringroup($group, $user)
{
    $gid = getgroup( $group );
    $uid = getuser( $user );

    if( !$uid || !$gid )
    {
            return false;
    }

    $found = false;
    foreach( $gid->sharedUserList as $guser )
    {
        if( $uid->id == $guser->id )
        {
            $found = true;
        }
    }

    return $found;
}

function removeuser($group, $user)
{
    $gid = getgroup( $group );
    $uid = getuser( $user );

    if( !$uid || !$gid )
    {
            return false;
    }

    $found = false;
    foreach( $gid->sharedUserList as $guser )
    {
        if( $uid->id == $guser->id )
        {
            $found = true;
        }
    }

    if( ! $found )
    {
        return false;
    }

    unset( $gid->sharedUserList[$uid->id] );

    \R::storeAll( [$uid, $gid] );
    return true;
}
