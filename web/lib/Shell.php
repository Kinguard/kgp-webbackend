<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace OPI\shell;

require_once 'models/ShellModel.php';

function getsettings()
{
	$ret = \OPI\ShellModel\getenabled();

	if( $ret === false )
	{
		$app->response->setStatus(500);
		print_r($app->request->params());
	}
	else
	{
		$app = \Slim\Slim::getInstance();
		$app->response->headers->set('Content-Type', 'application/json');

		print json_encode($ret);
	}
}

function setsettings()
{
	$app = \Slim\Slim::getInstance();



	$curstatus = \OPI\ShellModel\getenabled();

	if( !$curstatus["available"] )
	{
		$app->response->setStatus(409);
		print "SSH control currently not available";
		return;
	}

	$enable = $app->request->post('enabled');

	if ($enable == null) {
		$app->response->setStatus(400);
		print_r($app->request->params());
	} else {
		if (gettype($enable)  == "string")
		{
			$res = array();

			if( $enable == "true" )
			{
				$res["available"] = true;
				$res["status"] = \OPI\ShellModel\enable();
				$res["enabled"] = $res["status"];
				print json_encode($res);
			}
			else if( $enable == "false" )
			{
				$res["available"] = true;
				$res["status"] = \OPI\ShellModel\disable();
				$res["enabled"] = !$res["status"];
				print json_encode($res);
			}
			else
			{
				$app->response->setStatus(400);
				print_r($app->request->params());
				echo "\nType: " . gettype($enable)." ".$enable;
			}
		}
		else
		{
			$app->response->setStatus(400);
			print_r($app->request->params());
		}
	}

}
