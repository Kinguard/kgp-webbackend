<?php

namespace OPI\users;

require_once 'models/UserModel.php';
require_once 'Utils.php';

function getuser($id)
{
	$app = \Slim\Slim::getInstance();

        $user = \OPI\UserModel\getuser( $id );

        if( ! $user )
        {
            $app->halt(404);
        }


        if( ! isadminoruser( $user["username"] ) )
	{
		$app->halt(401);
	}

	$app->response->headers->set('Content-Type', 'application/json');
	print json_encode( $user );

}

function getusers()
{
	$app = \Slim\Slim::getInstance();

	$app->response->headers->set('Content-Type', 'application/json');

	$users = \OPI\UserModel\getusers();

	print json_encode( $users );
}

function getgroups( $username )
{
	$app = \Slim\Slim::getInstance();

	$app->response->headers->set('Content-Type', 'application/json');

	$groups = \OPI\UserModel\getgroups( $username );
	$o_groups = Array();
	foreach($groups as $group) {
		$o_groups[] = array('id' => $group);
	}
	print json_encode( $o_groups );
}

function deleteuser($id)
{
	$app = \Slim\Slim::getInstance();

	if( !\OPI\UserModel\deleteuser( $id ) )
	{
		$app->halt(404);
	}
}

function deleteusers()
{
    \OPI\UserModel\deleteusers();
}

function updatepassword($id)
{
    $app = \Slim\Slim::getInstance();
    $old   = $app->request->post('oldpassword');
    $new   = $app->request->post('newpassword');

    if( !checknull( $new) )
    {
        print_r($app->request->params());
        $app->halt(400);
    }

    $u = \OPI\UserModel\getuser( $id );
    if (! $u )
    {
        $app->halt(404);
    }

    if( ! isadminoruser( $u["username"] ) )
    {
        $app->halt(401);
    }

    if( !\OPI\UserModel\updatepassword($u["username"], $old, $new) )
    {
        $app->halt(406);
    }

}


/* Todo: skall man kunna ändra userid? */
function updateuser($id)
{
    $app = \Slim\Slim::getInstance();
    $user = array();
    $user['username']   = $app->request->put('username');
    $user['displayname']= $app->request->put('displayname');

    if( !checknullarray( $user ) )
    {
        print_r($app->request->params());
        $app->halt(400);
    }

    if( ! isadminoruser( $user["username"] ) )
    {
            $app->halt(401);
    }

    $u = \OPI\UserModel\getuser( $id );

    if (! $u )
    {
        $app->halt(404);
    }

    if( ! isadminoruser( $u["username"] ) )
    {
        $app->halt(401);
    }

    if( !\OPI\UserModel\updateuser($user) )
    {
       $app->halt(404);
    }
}

function createuser()
{
    $app = \Slim\Slim::getInstance();
    $user = array();
    $user['username']   = $app->request->post('username');
    $user['displayname']= $app->request->post('displayname');
    $user['password']   = $app->request->post('password');

    if( !checknullarray( $user  ) )
    {
            print_r($app->request->params());
            $app->halt(400);
    }

    $id = \OPI\UserModel\createuser( $user );

    if (! $id )
    {
        $app->halt(409);
    }

    $app->response->headers->set('Content-Type', 'application/json');

    print '{ "id": "'.$id.'"}';

}

