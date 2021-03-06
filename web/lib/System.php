<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace OPI\system;

require_once 'models/SystemModel.php';


function getunitid()
{
	$app = \Slim\Slim::getInstance();
	$app->response->headers->set('Content-Type', 'application/json');
	$resp = \OPI\SystemModel\getunitid();
	if ( ! $resp)
	{
		$app->halt(405);
	}
	else
	{
		print json_encode($resp);	
	}
}

function setunitid()
{
	$app = \Slim\Slim::getInstance();
	$settings = $app->request->post();
	$app->response->headers->set('Content-Type', 'application/json');

	$unitid	= $app->request->post('unitid');
	$mpwd 	= $app->request->post('mpwd');
	$enabled= filter_var($app->request->post('enabled'),FILTER_VALIDATE_BOOLEAN);

	// check paramerters
	if( !checknull($unitid) || !checknull($mpwd) )
	{
		print('{"errormsg" : "Invalid arguments"}');
		$app->halt(400);
	}
	list($status,$resp) = \OPI\SystemModel\setunitid($unitid,$mpwd,$enabled);
	if ( ! $status)
	{
		$app->response->setStatus(405);
		print("{\"errormsg\" : \"$resp\" }");
	}
	else
	{
		print json_encode($resp);	
	}
}

function gettype()
{
	$app = \Slim\Slim::getInstance();
	$app->response->headers->set('Content-Type', 'application/json');
	$resp = \OPI\SystemModel\gettype();
	if ( ! $resp)
	{
		$app->halt(405);
	}
	else
	{
		print json_encode($resp);	
	}
}

function getmoduleproviders()
{
	$app = \Slim\Slim::getInstance();
	$app->response->headers->set('Content-Type', 'application/json');
	$resp = \OPI\SystemModel\getmoduleproviders();
	if ( ! $resp)
	{
		$app->halt(405);
	}
	else
	{
		print json_encode($resp);	
	}
}

function getmoduleproviderinfo($provider)
{
	$app = \Slim\Slim::getInstance();
	$app->response->headers->set('Content-Type', 'application/json');
	$resp = \OPI\SystemModel\getmoduleproviderinfo($provider);
	if ( ! $resp)
	{
		$app->halt(405);
	}
	else
	{
		print json_encode($resp);	
	}
}


function updatemoduleproviders()
{
	$app = \Slim\Slim::getInstance();
	$app->response->headers->set('Content-Type', 'application/json');
	$settings = $app->request->post();

	list($status,$res) = \OPI\SystemModel\updatemoduleproviders();

	if( ! $status )
	{
		$app->response->setStatus(400);
		printf('{"status": "fail"}');
	}
	else
	{
		print json_encode($res);
	}
}
