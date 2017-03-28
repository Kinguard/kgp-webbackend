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

	if( ! $status )
	{
		return false;
	}

	return $res;
}

function ackmessage($msgid)
{
	$b = \OPIBackend::instance();
	$status = $b->systemackmessage( \OPI\session\gettoken(), $msgid );

	return $status;
}


function getstatus()
{
	$b = \OPIBackend::instance();
	$res = $b->systemgetstatus( \OPI\session\gettoken() );

	if( ! $res['status'] )
	{
		return false;
	}

	return json_encode($res);
}

function getstorage()
{
	$b = \OPIBackend::instance();
	$res = $b->systemgetstorage( \OPI\session\gettoken() );

	if( ! $res['status'] )
	{
		return false;
	}

	return json_encode($res);
}
function getpackages()
{
	$b = \OPIBackend::instance();
	list($status,$res) = $b->systemgetpackages( \OPI\session\gettoken() );

	if( ! $status )
	{
		return false;
	}

	return json_encode($res);
}
