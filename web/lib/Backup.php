<?php
namespace OPI\backup;

require_once 'Utils.php';

function getquota()
{
	$app = \Slim\Slim::getInstance();

	$app->response->headers->set('Content-Type', 'application/json');

	// Hardcode for now
	print '{ "total": 8589934592, "used":2061584302}';

}

function codeexists($code)
{
	$a = \R::find( "backupcodes",
		"where code = :code" ,	[ ':code' => $code ] );
	if( count( $a ) > 0 )
	{
		return true;
	}

	return false;
}


function addsubscription()
{
	$app = \Slim\Slim::getInstance();

	$code 		= $app->request->post('code');

	if( ! checknull($code) )
	{
		$app->halt(400);
	}

	if( codeexists( $code ) )
	{
		$app->halt(409);
	}

	$codebean = \R::dispense( "backupcodes" );
	$codebean->code = $code;

	$id = \R::store( $codebean );

	$app->response->headers->set('Content-Type', 'application/json');

	print '{ "id": '.$id.'}';
}

function getsubscription( $id )
{
	$app = \Slim\Slim::getInstance();

	$a = \R::load( "backupcodes", $id);

	if( $a->id == 0 )
	{
		$app->halt(404);
	}

	$app->response->headers->set('Content-Type', 'application/json');

	print json_encode( $a->export() );
}


function getsubscriptions()
{
	$app = \Slim\Slim::getInstance();

	$app->response->headers->set('Content-Type', 'application/json');

	$accs = \R::findAll( "backupcodes" );

	print json_encode( \R::exportAll( $accs ) );

}

function deletesubscription($id)
{
	$app = \Slim\Slim::getInstance();

	$a = \R::load( "backupcodes", $id);

	if( $a->id == 0 )
	{
		$app->halt(404);
	}

	\R::trash( $a );
}


function deletesubscriptions()
{
	\R::wipe( "backupcodes" );
}

function getsettings()
{
	$app = \Slim\Slim::getInstance();

	$app->response->headers->set('Content-Type', 'application/json');

	// Check if settings exists
	$settings = \R::findAll( "backupsettings");

	if( count( $settings ) == 0 )
	{
		$s = \R::dispense( "backupsettings" );
		$s->enabled = true;
		$s->location = "remote";
		$s->type = "timeline";

		\R::store( $s );

		print json_encode( $s->export() );
	}
	else
	{
		$settings = reset($settings);
		print json_encode( $settings->export() );
	}

}

function setsettings()
{
	$app = \Slim\Slim::getInstance();

	$enabled 	= $app->request->post('enabled');
	$location	= $app->request->post('location');
	$type 		= $app->request->post('type');

	// Validate indata
	if( !checknull( $enabled, $location, $type ) )
	{
		$app->halt(400);
	}

	if( ! in_array( $location, array( "remote", "local") ) )
	{
		$app->halt(400);
	}

	if( ! in_array( $type, array( "timeline", "mirror") ) )
	{
		$app->halt(400);
	}


	// Check if settings exists
	$s = \R::findAll( "backupsettings");

	if( count( $s ) == 0 )
	{
		$s = \R::dispense( "backupsettings" );
	}
	else
	{
		$s = reset($s);
	}

	$s->enabled 	= $enabled;
	$s->location	= $location;
	$s->type 		= $type;

	\R::store( $s );

}
