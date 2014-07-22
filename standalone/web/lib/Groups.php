<?php
namespace OPI\groups;

require_once 'Utils.php';
require_once 'opimodels/UserModel.php';
require_once 'opimodels/GroupModel.php';

function getgroups()
{
	$app = \Slim\Slim::getInstance();

	$app->response->headers->set('Content-Type', 'application/json');

	$groups = \OPI\GroupModel\getgroups();

	print json_encode( $groups );
}

function getusers( $group )
{
	$app = \Slim\Slim::getInstance();

	if( ! \OPI\GroupModel\groupexists( $group ) )
        {
		$app->halt( 404 );
	}

	$app->response->headers->set('Content-Type', 'application/json');

	print json_encode( \OPI\GroupModel\getusers($group) );
}

function creategroup()
{

	$app = \Slim\Slim::getInstance();

	$group = $app->request->post('group');

	if ( $group == null )
	{
		$app->halt(400);
	}

        if( \OPI\GroupModel\groupexists( $group ) )
	{
		$app->halt(409);
	}


	$id = \OPI\GroupModel\creategroup($group);

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

        if( !\OPI\GroupModel\adduser($group, $user) )
        {
		$app->halt(404);
        }

}

function deletegroups()
{
    \OPI\GroupModel\deletegroups();
}


function deletegroup($group)
{

        if( !\OPI\GroupModel\deletegroup($group) )
        {
            $app->halt( 404 );
        }
}

function removeuser($group, $user)
{
    $app = \Slim\Slim::getInstance();

    if( !\OPI\GroupModel\removeuser($group, $user) )
    {
		$app->halt(404);
    }
}

