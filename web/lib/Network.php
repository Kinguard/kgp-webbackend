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
function _validateport($portno)
{
	return !(False === array_search( $portno, [25,80,443,143,587,993,2525]));
}


function getports()
{
	$app = \Slim\Slim::getInstance();

	$app->response->headers->set('Content-Type', 'application/json');

    print json_encode(\OPI\NetworkModel\getports() );
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


    $status = \OPI\NetworkModel\setport($port, $enabled);	
	if(!$status) $app->halt(400);
    $res['status'] = $status;
	$res['enabled'] = $enabled;
	print json_encode($res);
}

function getdomains()
{
	$app = \Slim\Slim::getInstance();
	$app->response->headers->set('Content-Type', 'application/json');

	print json_encode(\OPI\NetworkModel\getdomains());
}

function getopiname()
{
	$app = \Slim\Slim::getInstance();
	$app->response->headers->set('Content-Type', 'application/json');

	$res = \OPI\NetworkModel\getopiname();

	if($res["opiname"] === false)
	{
		$app->halt(400);
	}
	print json_encode($res);
	//printf('{"opiname": "%s","dnsenabled":"%s","domain":"%s"}',$opiname,$enabled,$domain );
}

function setopiname()
{
	$app = \Slim\Slim::getInstance();
	$settings = $app->request->post();
	$settings['dnsenabled'] = filter_var($settings['dnsenabled'], FILTER_VALIDATE_BOOLEAN);
	list($status,$res) = \OPI\NetworkModel\setopiname($settings);
		if( ! $status )
		{
			$app->response->setStatus(400);
		print $res;
		}
		else
		{
			print json_encode($res);
		}

}

function checkopiname()
{
	error_reporting(error_reporting() & ~E_WARNING);
	// turn off error reporting so that slim does not trigger on an expected 403 response from backend servers.
	$app = \Slim\Slim::getInstance();
	
	$data = http_build_query( array(
				"checkname" => true, 
				"fqdn" => $app->request->post("fqdn")
				)
			); 
	$context_options = Array(
		"http" =>	Array(
				"method"  => "POST",
				"timeout" => 1,
				"content" => $data,
				'header' => "Content-Type: application/x-www-form-urlencoded\r\n".
				"Content-Length: ".strlen($data)."\r\n",
			)
		);
	$context = stream_context_create($context_options);
	
	$fp = @fopen("https://auth.openproducts.com/update_dns.php",'r',false,$context);
	if($fp === false) {
		$res['isValid'] = false;
		$res['value'] = "Name not available";
	} else {
		$res['isValid'] = true;
		$res['value'] = "Name available";
	}

	print json_encode($res);	
}

function getcert()
{
	$app = \Slim\Slim::getInstance();
	$app->response->headers->set('Content-Type', 'application/json');

	$res = \OPI\NetworkModel\getCert();

	print json_encode($res);
}

/*
** Included in setopiname 
**
function setcert()
{
	$app = \Slim\Slim::getInstance();
	$settings = $app->request->post();
	$app->response->headers->set('Content-Type', 'application/json');

	list($status,$res) = \OPI\NetworkModel\setCert($settings);
	print json_encode($res);
	if( ! $status )
	{
		$app->response->setStatus(500);
	}
}
*/

function checkcert()
{
	$app = \Slim\Slim::getInstance();
	$app->response->headers->set('Content-Type', 'application/json');
	$post = $app->request->post();

	if (\OPI\NetworkModel\checkCert($post['value']) )
	{
		$msg = '{"isValid": true}';
	}
	else
	{
		$msg = '{"isValid": false,"value":"Invalid Certificate"}';
	}
	print($msg);
}

function checkkey()
{
	$app = \Slim\Slim::getInstance();
	$app->response->headers->set('Content-Type', 'application/json');
	$post = $app->request->post();
	
	if (\OPI\NetworkModel\checkKey($post['value']) )
	{
		$msg = '{"isValid": true}';
	}
	else
	{
		$msg = '{"isValid": false,"value":"Invalid Key"}';
	}
	print($msg);
}

