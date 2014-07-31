<?php
namespace OPI\network;

require_once 'Utils.php';
require_once 'models/NetworkModel.php';

function getsettings()
{
	$app = \Slim\Slim::getInstance();

	$app->response->headers->set('Content-Type', 'application/json');

	print json_encode(\OPI\NetworkModel\getsettings() );
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

	if( $type == "dynamic" )
	{
		\OPI\NetworkModel\setdynamic();
	}
	else
	{
		// Static
		// Validate indata, all other optional
		if( !checknull( $ipnumber, $netmask ) )
		{
			$app->halt(400);
		}

		// TODO: This wont work depending on which args that are set
		\OPI\NetworkModel\setstatic(
			$ipnumber, 
			$netmask, 
			$gateway	? $gateway:	"", 
			$dns1		? $dns1:	"", 
			$dns2		? $dns2:	"");
		
	}
}

function getports()
{
	$app = \Slim\Slim::getInstance();

	$app->response->headers->set('Content-Type', 'application/json');

    print json_encode(\OPI\NetworkModel\getports() );
}

function _validateport($portno)
{
	return !(False === array_search( $portno, [25,80,443,143,993]));
}

function setports()
{
    $app = \Slim\Slim::getInstance();

    $ports 	= $app->request->post();

	$set = array();

    foreach ($ports as $key => $value) {

        $portno = intval($key);
        if( $portno == 0)
        {
            $app->halt(400);
        }
        if( False === _validateport( $portno) )
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

		$set[$key] = $enabled;
    }

	\OPI\NetworkModel\setports($set);
}

function getport($port)
{
    $app = \Slim\Slim::getInstance();

	if( !_validateport($port) )
    {
        $app->halt(404);
    }

    $app->response->headers->set('Content-Type', 'application/json');

    $res = array( "enabled"=> \OPI\NetworkModel\getportstatus( $port ) );

    print json_encode( $res );
}

function setport($port)
{
    $app = \Slim\Slim::getInstance();

    if( !_validateport( $port) )
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

	\OPI\NetworkModel\setport($port, $enabled);
}
function getopiname()
{
	$app = \Slim\Slim::getInstance();
	$app->response->headers->set('Content-Type', 'application/json');
	printf('{"opiname": "%s"}',\OPI\NetworkModel\getopiname() );
}

function setopiname($name)
{
	//\OPI\NetworkModel\setopiname($name);
}
