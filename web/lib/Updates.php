<?php

namespace OPI\updates;

require_once 'models/UpdateModel.php';

function getstate() {
	
	$ret["doupdates"] = \OPI\UpdateModel\getstate();

	$app = \Slim\Slim::getInstance();
	$app->response->headers->set('Content-Type', 'application/json');

	print json_encode($ret);
}

function setstate() {
	$app = \Slim\Slim::getInstance();

	$update = $app->request->put('doupdates');

	if ($update == null) {
		$app->response->setStatus(400);
		print_r($app->request->params());
	} else {
		if ($update == "0" or $update == "1") {

			$status = \OPI\UpdateModel\setstate( $update );
			$res['status'] = $status;
			$res['doupdates'] = $update;
			print json_encode($res);
		} else {
			$app->response->setStatus(400);
			print_r($app->request->params());
		}
	}
}
