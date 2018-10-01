<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace OPI\SystemModel;

require_once 'models/OPIBackend.php';

function getunitid()
{
	$b = \OPIBackend::instance();
	list($status,$res) = $b->systemgetunitid( \OPI\session\gettoken() );
	if( ! $status )
	{
		return false;
	}
	return $res;
}

function gettype()
{
	$b = \OPIBackend::instance();
	list($status,$res) = $b->systemgettype( \OPI\session\gettoken() );
	if( ! $status )
	{
		return false;
	}
	return $res;
}

function getmoduleproviders()
{
	$b = \OPIBackend::instance();
	list($status,$res) = $b->systemgetmoduleproviders( \OPI\session\gettoken() );
	if( ! $status )
	{
		return false;
	}
	return $res;
}

function getmoduleproviderinfo($provider)
{
	$b = \OPIBackend::instance();
	list($status,$res) = $b->systemgetmoduleproviderinfo( \OPI\session\gettoken(), $provider );
	if( ! $status )
	{
		return false;
	}
	return $res;
}

function updatemoduleproviders($provider,$settings)
{
	$b = \OPIBackend::instance();

	return $b->systemupdatemoduleproviders( \OPI\session\gettoken(), $providers, $settings);

}


