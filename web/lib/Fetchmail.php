<?php
namespace OPI\fetchmail;

require_once 'Utils.php';

function accountexists($host, $identity)
{
	$a = \R::find( "fetchmailaccounts",
		"where host = :host and identity = :identity" ,
		[ ':host' => $host, ':identity' => $identity] );
	if( count( $a ) > 0 )
	{
		return true;
	}

	return false;
}

function getaccount( $id )
{
	$app = \Slim\Slim::getInstance();

	$a = \R::load( "fetchmailaccounts", $id);

	if( $a->id == 0 )
	{
		$app->halt(404);
	}

	if ( ! isadminoruser( $a->username ) )
	{
		$app->halt(401);
	}

	$app->response->headers->set('Content-Type', 'application/json');

	print json_encode( $a->export() );
}

function getaccounts()
{
	$app = \Slim\Slim::getInstance();

	$app->response->headers->set('Content-Type', 'application/json');

	if( isadmin() )
	{
		$accs = \R::findAll( "fetchmailaccounts" );
	}
	else
	{
		$user = getuser();
		$accs = \R::findAll( "fetchmailaccounts", "username = ?", [$user] );
	}

	print json_encode( \R::exportAll( $accs ) );
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

	$a = \R::dispense( "fetchmailaccounts" );
	$a->host		= $host;
	$a->identity	= $identity;
	$a->password	= $password;
	$a->username	= $username;
	$id = \R::store($a);

	$app->response->headers->set('Content-Type', 'application/json');

	print '{ "id": '.$id.'}';
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


	$a = \R::load( "fetchmailaccounts", $id );

	if ( $a->id == 0 )
	{
		$app->response->setStatus(404);
	}
	else
	{
		# Should not be able to overwrite account
		if( ! isadminoruser( $a->username ) )
		{
			$app->halt(401);
		}

		$a->host		= $host;
		$a->identity	= $identity;
		$a->password	= $password;
		$a->username	= $username;
		$id = \R::store($a);
	}



}

function deleteaccount($id)
{
	$app = \Slim\Slim::getInstance();

	$a = \R::load( "fetchmailaccounts", $id);

	if( $a->id == 0 )
	{
		$app->halt(404);
	}

	if( ! isadminoruser( $a->username ) )
	{
		$app->halt(401);
	}

	\R::trash( $a );
}

function deleteaccounts()
{
	$app = \Slim\Slim::getInstance();

	if( isadmin() )
	{
		$domains = \R::wipe( "fetchmailaccounts" );
	}
	else
	{
		$user = getuser();
		$d = \R::findAll( "fetchmailaccounts", "username = ?", [ $user ]);

		\R::trashAll( $d );
	}
}



