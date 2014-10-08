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
	$ret["enabled"] = \OPI\ShellModel\getenabled();

	$app = \Slim\Slim::getInstance();
	$app->response->headers->set('Content-Type', 'application/json');

	print json_encode($ret);
}

function setsettings()
{
	$app = \Slim\Slim::getInstance();

	$enable = $app->request->post('enable');

	if ($enable == null) {
		$app->response->setStatus(400);
		print_r($app->request->params());
	} else {
		if ($enable == "0" or $enable == "1") {
			$res = array();

			if( $enable == 0 )
			{
				$res["status"] = \OPI\ShellModel\enable();
			}
			else
			{
				$res["status"] = \OPI\ShellModel\disable();
			}
			print json_encode($res);
		} else {
			$app->response->setStatus(400);
			print_r($app->request->params());
		}
	}

}
