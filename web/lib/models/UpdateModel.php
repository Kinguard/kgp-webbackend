<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace OPI\UpdateModel;

require_once 'models/OPIBackend.php';

function getstate()
{
	$b = \OPIBackend::instance();
	list($status,$res) = $b->updategetstate( \OPI\session\gettoken());	
	if($status && isset($res['update']) && $res['update'] == "yes") {
		return "1";
	} else {
		return "0";
	}
}

function setstate($state)
{
	$b = \OPIBackend::instance();
	list($status,$res) = $b->updatesetstate( \OPI\session\gettoken(),$state);
	
	return $status;
}

function startupdate()
{
	$b = \OPIBackend::instance();
	list($status,$res) = $b->systemstartupdate( \OPI\session\gettoken());

	return $status;
}

function getupgrade()
{
	$b = \OPIBackend::instance();
	list($status,$res) = $b->systemgetupgrade( \OPI\session\gettoken());

    if( $status )
    {
       return $res;
    }

    return false;
}

function startupgrade()
{
	$b = \OPIBackend::instance();
	list($status,$res) = $b->systemstartupgrade( \OPI\session\gettoken());

	return $status;
}
