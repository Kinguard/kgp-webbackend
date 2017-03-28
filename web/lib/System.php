<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace OPI\system;

require_once 'models/SystemModel.php';

function getmessages()
{
	$app = \Slim\Slim::getInstance();
	$app->response->headers->set('Content-Type', 'application/json');

	print \OPI\SystemModel\getmessages();

}

function ackmessage()
{
	$app = \Slim\Slim::getInstance();
	$app->response->headers->set('Content-Type', 'application/json');

	$id = $app->request->post('id');

	if ($id == null) {
		$app->response->setStatus(400);
		print_r($app->request->params());
	}
	else 
	{
		print \OPI\SystemModel\ackmessage($id);	
	}
}

function getstatus()
{
	$app = \Slim\Slim::getInstance();
	$app->response->headers->set('Content-Type', 'application/json');

	print \OPI\SystemModel\getstatus();

}

function getstorage()
{
	$app = \Slim\Slim::getInstance();
	$app->response->headers->set('Content-Type', 'application/json');

	print \OPI\SystemModel\getstorage();

}
function getpackages()
{
	$app = \Slim\Slim::getInstance();
	$app->response->headers->set('Content-Type', 'application/json');

	print \OPI\SystemModel\getpackages();

}
