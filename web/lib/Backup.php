<?php
namespace OPI\backup;

require_once 'Utils.php';
require_once 'models/BackupModel.php';

function getquota()
{
	$app = \Slim\Slim::getInstance();

	$app->response->headers->set('Content-Type', 'application/json');

	print json_encode( \OPI\BackupModel\getquota() );
}

function getstatus()
{
	$app = \Slim\Slim::getInstance();

	$app->response->headers->set('Content-Type', 'application/json');

	print json_encode( \OPI\BackupModel\getstatus() );
}


function codeexists($code)
{
	$a = \OPI\BackupModel\getsubscription($code);

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

	$id = \OPI\BackupModel\addsubscription($code);
	
	$app->response->headers->set('Content-Type', 'application/json');

	print '{ "id": "'.$id.'"}';
}

function getsubscription( $id )
{
	$app = \Slim\Slim::getInstance();

	$a = \OPI\BackupModel\getsubscription( $id);

	if( count($a) == 0 )
	{
		$app->halt(404);
	}

	$app->response->headers->set('Content-Type', 'application/json');

	print json_encode( $a );
}


function getsubscriptions()
{
	$app = \Slim\Slim::getInstance();

	$app->response->headers->set('Content-Type', 'application/json');

	print json_encode( \OPI\BackupModel\getsubscriptions() );
}

function deletesubscription($id)
{
	$app = \Slim\Slim::getInstance();

	$a = \OPI\BackupModel\getsubscription( $id );

	if( count($a) == 0 )
	{
		$app->halt(404);
	}

	\OPI\BackupModel\deletesubscription( $id );
}

function getsettings()
{
	$app = \Slim\Slim::getInstance();

	$app->response->headers->set('Content-Type', 'application/json');

	print json_encode( \OPI\BackupModel\getsettings() );
}

function setsettings()
{
	$app = \Slim\Slim::getInstance();

	$enabled 	= $app->request->post('enabled');
	$location	= $app->request->post('location');
	$type 		= $app->request->post('type');
	$AWSkey		= $app->request->post('AWSkey') ? $app->request->post('AWSkey') : "";
	$AWSseckey	= $app->request->post('AWSseckey') ? $app->request->post('AWSseckey') : "";
	$AWSbucket	= $app->request->post('AWSbucket') ? $app->request->post('AWSbucket') : "";

	// Validate indata
	//if( !checknull( $enabled, $location, $type ) )
	if( !checknull( $enabled, $location ) )
	{
		$app->halt(400);
	}

	if( ! in_array( $location, array( "op", "local", "amazon") ) )
	{
		$app->halt(400);
	}

	// validate bucket 
	if(! preg_match ('/^[a-z0-9]([\-a-z0-9]*[a-z0-9])*(\.?[a-z0-9]([\-a-z0-9]*[a-z0-9])*)*$/',$AWSbucket) ) {
		$app->halt(404);	
	}


	/*
	if( ! in_array( $type, array( "timeline", "mirror") ) )
	{
		$app->halt(400);
	}
	*/
	if($enabled == "false")
	{
		$location = "none";
	}
	
	\OPI\BackupModel\setsettings($location, $type, $AWSkey, $AWSseckey, $AWSbucket);
}
