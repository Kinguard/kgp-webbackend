<?php
namespace OPI\groups;

require_once 'Utils.php';

function groupexists( $group )
{
	$g = \R::find( "group", "where name = :group", [ ':group' => $group]);

	if( count( $g ) == 0 )
	{
		return False;
	}

	return reset($g);
}

function userexists( $id )
{
	$user = \R::find( "user", "where username = :id", [ ':id' => $id]);

	if( count( $user ) == 0 )
	{
		return False;
	}

	return reset($user);
}

function getgroups()
{
	$app = \Slim\Slim::getInstance();

	$app->response->headers->set('Content-Type', 'application/json');

	$groups = \R::findAll( "group" );

	print json_encode( \R::exportAll( $groups ) );
}

function getusers( $group )
{
	$app = \Slim\Slim::getInstance();

	$g = groupexists( $group );

	if( ! $g )
	{
		$app->halt( 404 );
	}

	$app->response->headers->set('Content-Type', 'application/json');

	print json_encode( \R::exportAll( $g->sharedUserList ) );
}

function creategroup()
{

	$app = \Slim\Slim::getInstance();

	$group = $app->request->post('group');

	if ( $group == null )
	{
		$app->halt(400);
	}

	if( groupexists( $group ) )
	{
		$app->halt(409);
	}

	$g = \R::dispense( "group" );

	$g->name = $group;

	$id = \R::store( $g );

	$app->response->headers->set('Content-Type', 'application/json');

	print '{ "id": '.$id.'}';
}

function adduser($group)
{
	$app = \Slim\Slim::getInstance();

	$user = $app->request->post('user');

	if ( $user == null )
	{
		$app->halt(400);
	}

	$gid = groupexists( $group );
	$uid = userexists( $user );

	if( !$gid || !$uid )
	{
		$app->halt(404);
	}

	$gid->sharedUserList[] = $uid;

	\R::storeAll( [$gid, $uid] );
}

function deletegroups()
{
	$groups = \R::wipe( "group" );
}


function deletegroup($group)
{
	$app = \Slim\Slim::getInstance();

	$g = groupexists( $group );

	if( ! $g )
	{
		$app->halt( 404 );
	}

	\R::trash( $g );
}

function removeuser($group, $user)
{
	$app = \Slim\Slim::getInstance();

	$gid = groupexists( $group );
	$uid = userexists( $user );

	if( !$uid || !$gid )
	{
		$app->halt(404);
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
		$app->halt(404);
	}

	unset( $gid->sharedUserList[$uid->id] );

	\R::storeAll( [$uid, $gid] );
}

