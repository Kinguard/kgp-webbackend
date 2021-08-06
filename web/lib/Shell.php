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

	$enable = $app->request->post('enabled');

	if ($enable == null) {
		$app->response->setStatus(400);
		print_r($app->request->params());
	} else {
		if ($enable == "1" or $enable == "0")
		{
			$res = array();

			if( $enable == "1" )
			{
				$res["status"] = \OPI\ShellModel\enable();
				if ($res["status"])
				{
					$res['enabled'] = "1";
				}
				else
				{
					$res['enabled'] = "0";
				}
			}
			else
			{
				$res["status"] = \OPI\ShellModel\disable();
				if ($res["status"])
				{
					$res['enabled'] = "0";
				}
				else
				{
					$res['enabled'] = "1";
				}
			}
			print json_encode($res);
		}
		else
		{
			$app->response->setStatus(400);
			print_r($app->request->params());
		}
	}

}
