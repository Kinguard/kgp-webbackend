<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace OPI\status;

require_once 'models/StatusModel.php';

function getmessages()
{
	$app = \Slim\Slim::getInstance();
	$app->response->headers->set('Content-Type', 'application/json');

	$resp = \OPI\StatusModel\getmessages();
	if ( ! $resp)
	{
		$app->halt(405);
	}
	else
	{
		print json_encode($resp);	
	}

}

function ackmessage()
{
	$app = \Slim\Slim::getInstance();
	$app->response->headers->set('Content-Type', 'application/json');

	$id = $app->request->post('id');

	if ($id == null)
	{
		$app->response->setStatus(400);
		print_r($app->request->params());
	}
	else 
	{
		print json_encode( \OPI\StatusModel\ackmessage($id) );	
	}
}

function getstatus()
{
	$app = \Slim\Slim::getInstance();
	$app->response->headers->set('Content-Type', 'application/json');

	print json_encode( \OPI\StatusModel\getstatus() );

}

function getstorage()
{
	$app = \Slim\Slim::getInstance();
	$app->response->headers->set('Content-Type', 'application/json');

	print json_encode( \OPI\StatusModel\getstorage() );

}
function getpackages()
{
	$app = \Slim\Slim::getInstance();
	$app->response->headers->set('Content-Type', 'application/json');

	$resp = \OPI\StatusModel\getpackages();
	if ( ! $resp)
	{
		$app->halt(405);
	}
	else
	{
		print json_encode($resp);	
	}
}
