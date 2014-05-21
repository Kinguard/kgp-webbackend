<?php
namespace OPI\updates;


function getcurrentstate()
{
	$updates = \R::findAll( "updates" );
	
	if( count( $updates) == 0 )
	{
		// First get nothing here, create
		$up = \R::dispense( "updates" );
		$up->doupdates = true;
		\R::store( $up );
	}
	else
	{
		// There can be only one!
		$up = reset($updates);
	}

	return $up;
}

function getstate()
{
	$up = getcurrentstate();

	$app = \Slim\Slim::getInstance();
	$app->response->headers->set('Content-Type', 'application/json');

	print json_encode( $up->export() );
}

function setstate()
{
	$app = \Slim\Slim::getInstance();

	$update	= $app->request->put('updates');

	if( $update == null )
	{
		$app->response->setStatus(400);
		print_r($app->request->params());
	}
	else
	{
		if ( $update == "0" or $update == "1")
		{
			$up = getcurrentstate();
			
			$up->doupdates = $update;
			
			\R::store( $up );	
		}
		else
		{
			$app->response->setStatus(400);
			print_r($app->request->params());
		}
	}

}
