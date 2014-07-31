<?php

namespace OPI\device;

require_once 'models/DeviceModel.php';

function shutdown()
{
	$app = \Slim\Slim::getInstance();
	$action = $app->request->post('action');

	if( !checknull( $action ) )
	{
		$app->halt(400);
	}

	if( ! in_array($action, array("shutdown","reboot") ) )
	{
		$app->halt(400);
	}

	\OPI\DeviceModel\shutdown( $action );
}
