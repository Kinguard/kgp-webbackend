<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace OPI\SystemModel;

require_once 'models/OPIBackend.php';

function getmessages()
{
	$b = \OPIBackend::instance();
	list($status,$res) = $b->systemgetmessages( \OPI\session\gettoken() );
	// messages are returned as an array of strings
	if( ! $status )
	{
		return false;
	}
	foreach ($res['messages'] as $key => $msg) {
		$res['messages'][$key] = json_decode($msg);
	}
	return $res;
}

function ackmessage($msgid)
{
	$b = \OPIBackend::instance();
	list($status,$res) = $b->systemackmessage( \OPI\session\gettoken(), $msgid );

	return $res;
}


function getstatus()
{
	$b = \OPIBackend::instance();
	list($status,$res) = $b->systemgetstatus( \OPI\session\gettoken() );

	if( ! $status )
	{
		return false;
	}

	return $res;
}

function getstorage()
{
	$b = \OPIBackend::instance();
	list($status,$res) = $b->systemgetstorage( \OPI\session\gettoken() );

	if( ! $status )
	{
		return false;
	}
	//$storage = explode(' ',$res["storage"]);
	//unset($res["storage"]);

	// returned result is in order of "total, used, available" in 1k blocks
	/*$res["storage"] = 	[
							"total" => $storage[0] * 1024,
							"used" => $storage[1] * 1024,
							"available" => $storage[2] * 1024
						];
	*/
	return $res;
}
function getpackages()
{
	$b = \OPIBackend::instance();
	list($status,$res) = $b->systemgetpackages( \OPI\session\gettoken() );

	if( ! $status )
	{
		return false;
	}
	return $res;
}
