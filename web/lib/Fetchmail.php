<?php
namespace OPI\fetchmail;

require_once 'Utils.php';
require_once 'models/FetchmailModel.php';

function accountexists($host, $identity)
{
	$a = \OPI\FetchmailModel\getaccountbyhost($host, $identity);

	if( count( $a ) > 0 )
	{
		return true;
	}

	return false;
}

function getaccount( $id )
{
	$app = \Slim\Slim::getInstance();

	$a = \OPI\FetchmailModel\getaccount($id);

    $app->response->headers->set('Content-Type', 'application/json');

	if( count($a) == 0 )
	{
            errmsg(404, "User not found");
	}

	if ( ! isadminoruser( $a["username"] ) )
	{
            errmsg(401, "Not allowed");
	}

	print json_encode( $a );
}

function getaccounts()
{
	$app = \Slim\Slim::getInstance();

	$app->response->headers->set('Content-Type', 'application/json');

	$user = NULL;
	if( ! isadmin() )
	{
		$user = getuser();
	}

	print json_encode( \OPI\FetchmailModel\getaccounts( $user ) );
}

function addaccount()
{
	$app = \Slim\Slim::getInstance();

	$host 		= $app->request->post('host');
	$identity	= $app->request->post('identity');
	$password 	= $app->request->post('password');
	$username	= $app->request->post('username');

	if( ! checknull($host, $identity, $password, $username) )
	{
		$app->halt(400);
	}

	if( ! isadminoruser( $username) )
	{
		$app->halt(401);
	}

	if( accountexists( $host, $identity) )
	{
		$app->halt(409);
	}

	$id = \OPI\FetchmailModel\addaccount( $host, $identity, $password, $username );

	$app->response->headers->set('Content-Type', 'application/json');

	print '{ "id": "'.$id.'"}';
}

function updateaccount($id)
{
	$app = \Slim\Slim::getInstance();

	$host 		= $app->request->post('host');
	$identity	= $app->request->post('identity');
	$password 	= $app->request->post('password');
	$username	= $app->request->post('username');

	if( ! checknull($host, $identity, $password, $username) )
	{
		$app->halt(400);
	}

	if( ! isadminoruser( $username ) )
	{
		$app->halt(401);
	}


	$a = \OPI\FetchmailModel\getaccount($id);

	if ( count($a) == 0 )
	{
		$app->response->setStatus(404);
	}
	else
	{
		# Should not be able to overwrite account
		if( ! isadminoruser( $a["username"] ) )
		{
			$app->halt(401);
		}

		\OPI\FetchmailModel\updateaccount($host, $identity, $password, $username );
	}
}

function deleteaccount($id)
{
	$app = \Slim\Slim::getInstance();

	$a = \OPI\FetchmailModel\getaccount($id);

	if( count($a) == 0 )
	{
		$app->halt(404);
	}

	if( ! isadminoruser( $a["username"] ) )
	{
		$app->halt(401);
	}

	\OPI\FetchmailModel\deleteaccount($id);
}
