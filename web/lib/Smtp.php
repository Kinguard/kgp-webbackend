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

	if( ! \OPI\SMTPModel\addaddress($domain, $address, $user) )
	{
		$app->status(403);
	}
}

function deleteaddress( $domain, $address )
{
	$app = \Slim\Slim::getInstance();

    // Check if domain exists
	if( !\OPI\SMTPModel\domainexists($domain) )
	{
		$app->halt(404);
	}

	list($adr_part,$dmn_part) = explode( "@", $address,2);

	if( $dmn_part != $domain )
	{
		$app->halt(400);
	}
	$address = $adr_part;

	// Check if address exists
	if(\OPI\SMTPModel\addressexists($domain, $address))
	{
		$app->halt(404);
	}

	\OPI\SMTPModel\deleteaddress($domain, $address);
}

function getaddresses( $domain, $userfilter="" )
{

	$app = \Slim\Slim::getInstance();

	// Check if domain exists
	if( !\OPI\SMTPModel\domainexists($domain) )
	{
		$app->halt(404);
	}

	$app->response->headers->set('Content-Type', 'application/json');

	print json_encode(\OPI\SMTPModel\getaddresses($domain,$userfilter) );

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

	$type		= $app->request->post('smtpsettings');
	$relay 		= $app->request->post('relay');
	$username	= $app->request->post('username');
	$password 	= $app->request->post('password');
	$port 		= $app->request->post('port');
	$send 		= filter_var($app->request->post('sendexternal'), FILTER_VALIDATE_BOOLEAN);
	$receive 	= filter_var($app->request->post('receiverelay'), FILTER_VALIDATE_BOOLEAN);

	if( !checknull( $type, $relay, $username, $password, $port, $send, $receive ) )
	{
		$app->halt(400);
	}
	if ( $type == "useexternal" )
	{
		print_r($send);
		print_r($receive);
		if( ! ($send || $receive ) )
		{
			$app->halt(400);
		}
	}
	$settings = array(
		"type"		=> $type,
		"relay"		=> $relay,
		"username"	=> $username,
		"password"	=> $password,
		"port"		=> $port,
		"send"		=> $send=="true",
		"receive"	=> $receive=="true"
	);

	\OPI\SMTPModel\setsettings($settings);
}
