<?php
namespace OPI\smtp;

require_once 'Utils.php';
require_once 'models/SmtpModel.php';

function getdomains()
{
	$app = \Slim\Slim::getInstance();

	$app->response->headers->set('Content-Type', 'application/json');

	print json_encode( \OPI\SMTPModel\getdomains() );
}

function adddomain()
{
	$app = \Slim\Slim::getInstance();

	$domain = $app->request->post('domain');

	if ( $domain == null )
	{
		$app->halt(400);
	}

	// Check if domain exists
	if( \OPI\SMTPModel\domainexists($domain) )
	{
		$app->halt(409);
	}

	$id = \OPI\SMTPModel\adddomain($domain);

	$app->response->headers->set('Content-Type', 'application/json');

	print '{ "id": "'.$id.'"}';
}

function deletedomain($id)
{
	$app = \Slim\Slim::getInstance();

	if ( !\OPI\SMTPModel\domainexists($id) )
	{
		$app->response->setStatus(404);
	}
	else
	{
		\OPI\SMTPModel\deletedomain($id);
	}
}

function addaddress( $domain )
{
	$app = \Slim\Slim::getInstance();

	$address = $app->request->post('address');
	$user = $app->request->post('user');

	if ( $address == null || $user == null )
	{
		$app->halt(400);
	}

	list($adr_part,$dmn_part) = explode( "@", $address,2);

	if( $dmn_part != $domain )
	{
		$app->halt(400);
	}
	$address = $adr_part;

	// Check if domain exists
	if(!\OPI\SMTPModel\domainexists($domain) )
	{
		$app->halt(404);
	}

	// Check that name isn't used
	if(\OPI\SMTPModel\addressexists($domain, $address) )
	{
		$app->halt(400);
	}

	\OPI\SMTPModel\addaddress($domain, $address, $user);
}

function deleteaddress( $domain, $address )
{
	$app = \Slim\Slim::getInstance();

    // Check if domain exists
	if( !\OPI\SMTPModel\domainexists($domain) )
	{
		$app->halt(404);
	}

	// Check if address exists
	if(\OPI\SMTPModel\addressexists($domain, $address))
	{
		$app->halt(404);
	}

	\OPI\SMTPModel\deleteaddress($domain, $address);
}

function getaddresses( $domain )
{

	$app = \Slim\Slim::getInstance();

	// Check if domain exists
	if( !\OPI\SMTPModel\domainexists($domain) )
	{
		$app->halt(404);
	}

	$app->response->headers->set('Content-Type', 'application/json');

	print json_encode(\OPI\SMTPModel\getaddresses($domain) );

}

function getsettings( )
{
	$app = \Slim\Slim::getInstance();

	$app->response->headers->set('Content-Type', 'application/json');

	print json_encode( \OPI\SMTPModel\getsettings() );
}

function setsettings( )
{
	$app = \Slim\Slim::getInstance();

	$usecustom	= $app->request->post('usecustom');
	$relay 		= $app->request->post('relay');
	$username	= $app->request->post('username');
	$password 	= $app->request->post('password');
	$port 		= $app->request->post('port');

	if( !checknull( $usecustom, $relay, $username, $password, $port ) )
	{
		$app->halt(400);
	}
	$settings = array(
		"usecustom"	=> $usecustom,
		"relay"		=> $relay,
		"username"	=> $username,
		"password"	=> $password,
		"port"		=> $port
	);

	\OPI\SMTPModel\setsettings($settings);
}
