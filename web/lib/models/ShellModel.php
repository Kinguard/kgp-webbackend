<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace OPI\ShellModel;

require_once 'models/OPIBackend.php';

function getenabled()
{
	$b = \OPIBackend::instance();
	list($status,$res) = $b->shellgetsettings( \OPI\session\gettoken() );

	if( ! $status )
	{
		return false;
	}

	$ret = array();
	$ret["enabled"] = $res["enabled"];
	$ret["available"] = $res["available"];

	return $ret;
}

function enable()
{
	$b = \OPIBackend::instance();
	list($status,$res) = $b->shellsetenabled( \OPI\session\gettoken() );

	return $status;
}

function disable()
{
	$b = \OPIBackend::instance();
	list($status,$res) = $b->shellsetdisabled( \OPI\session\gettoken() );

	return $status;
}
