<?php
namespace OPI\network;

require_once 'Utils.php';

function getsettings()
{
	$app = \Slim\Slim::getInstance();

	$app->response->headers->set('Content-Type', 'application/json');

	// Check if settings exists
	$settings = \R::findAll( "networksettings");

	if( count( $settings ) == 0 )
	{
		$s = \R::dispense( "networksettings" );
		$s->type		= "dynamic";
		$s->ipnumber	= "192.168.1.82";
		$s->netmask		= "255.255.255.0";
		$s->gateway		= "192.168.1.1";
		$s->dns1		= "8.8.8.8";
		$s->dns2		= "4.4.4.4";
		\R::store( $s );

		print json_encode( $s->export() );
	}
	else
	{
		$s = reset($settings);
		if( $s->type == "dynamic" )
		{
			$s->currentipnumber	= "192.168.1.82";
			$s->currentnetmask	= "255.255.255.0";
			$s->currentgateway	= "192.168.1.1";
			$s->currentdns1		= "8.8.8.8";
			$s->currentdns2		= "4.4.4.4";
		}

		print json_encode( $s->export() );
	}

}

function setsettings()
{
	$app = \Slim\Slim::getInstance();

	$type 		= $app->request->post('type');
	$ipnumber 	= $app->request->post('ipnumber');
	$netmask	= $app->request->post('netmask');
	$gateway 	= $app->request->post('gateway');
	$dns1	 	= $app->request->post('dns1');
	$dns2		= $app->request->post('dns2');

	// Type always required
	if( !checknull( $type ) )
	{
		$app->halt(400);
	}

	if( ! in_array( $type, array( "dynamic", "static") ) )
	{
		$app->halt(400);
	}

	// Check if settings exists
	$s = \R::findAll( "networksettings");

	if( count( $s ) == 0 )
	{
		$s = \R::dispense( "networksettings" );
	}
	else
	{
		$s = reset($s);
	}


	if( $type == "dynamic" )
	{
		$s->type		= $type;
		$s->ipnumber	= "";
		$s->netmask		= "";
		$s->gateway		= "";
		$s->dns1		= "";
		$s->dns2		= "";
	}
	else
	{
		// Static
		// Validate indata, all other optional
		if( !checknull( $ipnumber, $netmask ) )
		{
			$app->halt(400);
		}
		$s->type		= $type;
		$s->ipnumber	= $ipnumber;
		$s->netmask		= $netmask;
		$s->gateway		= $gateway	? $gateway:	"";
		$s->dns1		= $dns1		? $dns1:	"";
		$s->dns2		= $dns1		? $dns2:	"";
	}

	\R::store( $s );

}
