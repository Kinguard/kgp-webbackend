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

function getports()
{
    // Check if settings exists
    $p = \R::findAll( "networkports");

    if( count( $p ) == 0 )
    {
            $p = \R::dispense( "networksettings" ,3);
            $p[0]->port = 443;
            $p[0]->enabled = True;
            $p[1]->port = 143;
            $p[1]->enabled = True;
            $p[2]->port = 25;
            $p[2]->enabled = True;
            \R::storeAll($p);
    }

    $res = array();
    foreach ($p as $bean) {
        $res[$bean->port]=$bean->enabled;
    }

    $app = \Slim\Slim::getInstance();

    $app->response->headers->set('Content-Type', 'application/json');

    print json_encode( $res );
}

function setports()
{
    $app = \Slim\Slim::getInstance();

    $ports 	= $app->request->post();

    foreach ($ports as $key => $value) {

        $portno = intval($key);
        if( $portno == 0)
        {
            $app->halt(400);
        }
        if( False === array_search( $portno, [25,443,143]))
        {
            $app->halt(400);
        }

        if( $value == "True")
        {
            $enabled = True;
        }
        else if( $value == "False" )
        {
            $enabled = False;
        }
        else
        {
            $app->halt(404);
        }

        $port = \R::find( "networkports", "where port = :port", [ ':port' => $portno]);
        if( count($port) == 0)
        {
            $port = \R::dispense("networkports");
            $port->port= $key;
            $port->enabled = $enabled;
        }
        else if( count($port) > 0 )
        {
                $port = reset( $port) ;
                $port->enabled = $enabled;
        }
        \R::store($port);
    }
}

function getport($port)
{
    $app = \Slim\Slim::getInstance();

    $port = \R::find( "networkports", "where port = :port", [ ':port' => $port]);
    if( count($port) == 0)
    {
        $app->halt(404);
    }
    $port = reset($port);
    $app->response->headers->set('Content-Type', 'application/json');

    $res = array( "enabled"=> $port->enabled );
    print json_encode( $res );
}

function setport($port)
{
    $app = \Slim\Slim::getInstance();

    if( False === array_search( $port, [25,443,143]))
    {
        $app->halt(404);
    }

    $value	= $app->request->put("enabled");
    if( ! $value )
    {
        $app->halt(400);
    }

    if( $value == "True")
    {
        $enabled = True;
    }
    else if( $value == "False" )
    {
        $enabled = False;
    }
    else
    {
        $app->halt(404);
    }

    $port = \R::find( "networkports", "where port = :port", [ ':port' => $port]);
    if( count($port) == 0)
    {
        $app->halt(404);
    }
    $port = reset($port);
    $port->enabled = $enabled;

    \R::store($port);
}
