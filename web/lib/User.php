<?php

namespace OPI\users;

function getuser($id)
{
	$app = \Slim\Slim::getInstance();

	// First try by id

	$user = \R::load( "user", $id );
	
	if ( $user->id == 0 )
	{
		// Try by username
		$user = \R::find( "user", "where username = :id", [ ':id' => $id]);
		if( count($user) > 0 )
		{
			print json_encode( reset($user)->export()  );
		}
		else
		{
			$app->response->setStatus(404);
		}
	}
	else
	{
		$app->response->headers->set('Content-Type', 'application/json');
		print json_encode( $user->export()  );

	}			
}

function getusers()
{
	$app = \Slim\Slim::getInstance();

	$app->response->headers->set('Content-Type', 'application/json');
	
	$users = \R::findAll( "user" );
	
	print json_encode( \R::exportAll( $users ) );
}

function deleteuser($id)
{
	$app = \Slim\Slim::getInstance();

	$user = \R::load( "user", $id );
	
	if ( $user->id == 0 )
	{
		$app->response->setStatus(404);
	}
	else
	{
		\R::trash($user);
	}			
}

function deleteusers()
{
	$app = \Slim\Slim::getInstance();

	$user = \R::wipe( "user" );
}

/* Todo: skall man kunna ändra userid? */
function updateuser($id)
{
	$app = \Slim\Slim::getInstance();

	$user 		= $app->request->put('username');
	$display 	= $app->request->put('displayname');
	$password	= $app->request->put('password');

	if( $user == null || $display == null || $password == null )
	{
		$app->response->setStatus(400);
		print_r($app->request->params());
	}
	else
	{
		$u = \R::load( "user", $id );
	
		if ( $u->id == 0 )
		{
			$app->response->setStatus(404);
		}
		else
		{
			$u->username	= $user;
			$u->displayname	= $display;
			$u->password	= $password;
			\R::store( $u );
		}
	}
}

function createuser()
{
	$app = \Slim\Slim::getInstance();

	$user 		= $app->request->post('username');
	$display 	= $app->request->post('displayname');
	$password	= $app->request->post('password');

	if( $user == null || $display == null || $password == null )
	{
		$app->response->setStatus(400);
		print_r($app->request->params());
	}
	else
	{

		// Check if user exists
		$tmpuser = \R::find( "user", "where username = :id", [ ':id' => $user]);
		if( count($tmpuser) > 0 )
		{
			$app->halt(409);
		}

		$u = \R::dispense("user");
	
		$u["username"] 		= $user;
		$u["displayname"] 	= $display;
		$u["password"]		= $password;

		$id = \R::store( $u );

		$app->response->headers->set('Content-Type', 'application/json');
		
		print '{ "id": '.$id.'}';			

	}
}

