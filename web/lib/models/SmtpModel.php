<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace OPI\SMTPModel;

require_once 'models/OPIBackend.php';

function getdomains()
{
	$b = \OPIBackend::instance();

	list($status, $res) = $b->smtpgetdomains(\OPI\session\gettoken() );

	if( $status )
	{
		$ret = array();
		foreach($res["domains"] as $domain )
		{
			$ret[]["domain"] = $domain;
		}
		return  $ret;
	}
	return false;
}

function domainexists( $domain )
{
	$b = \OPIBackend::instance();

	list($status, $res) = $b->smtpgetdomains(\OPI\session\gettoken() );

	if( $status )
	{
		return in_array($domain, $res["domains"]);
	}

	return false;
}

function adddomain( $domain )
{
	$b = \OPIBackend::instance();

	list($status, $res) = $b->smtpadddomain(\OPI\session\gettoken(), $domain );
	if( $status )
	{
		return $domain;
	}

	return false;
}

function deletedomain( $domain )
{
	$b = \OPIBackend::instance();

	list($status, $res) = $b->smtpdeletedomain(\OPI\session\gettoken(), $domain );

	return $status;
}

function addaddress( $domain, $address, $username )
{
	$b = \OPIBackend::instance();

	list($status, $res) = $b->smtpaddaddress(
		\OPI\session\gettoken(), $domain, $address, $username );

	return $status;
}

function addressexists( $domain, $address )
{
	$b = \OPIBackend::instance();

	list($status, $res) = $b->smtpgetaddresses(\OPI\session\gettoken(), $domain );
	if( $status )
	{
		foreach( $res["addresses"] as $address )
		{
			if( $address["address"] == $address )
			{
				return true;
			}
		}
	}
	return false;
}

function deleteaddress( $domain, $address )
{
	$b = \OPIBackend::instance();

	list($status, $res) = $b->smtpdeleteaddress(
		\OPI\session\gettoken(), $domain, $address );

	return $status;
}

function getaddresses( $domain )
{
	$b = \OPIBackend::instance();

	list($status, $res) = $b->smtpgetaddresses(\OPI\session\gettoken(), $domain );

	if( $status )
	{
		$ret = array();
		foreach($res["addresses"] as $address )
		{
			$ret[]= array(
				"address" => $address["address"],
				"user" => $address["username"]);
		}
		return  $ret;
	}

	return false;
}

function getsettings()
{
	$b = \OPIBackend::instance();

	list($status, $res) = $b->smtpgetsettings(\OPI\session\gettoken() );

	if( $status )
	{

		return array(
			"usecustom"	=> $res["usecustom"],
			"relay"		=> $res["relay"],
			"username"	=> $res["username"],
			"password"	=> $res["password"],
			"port"		=> $res["port"]
		);
	}
	return $status;
}

function setsettings( $settings )
{
	$b = \OPIBackend::instance();

	list($status, $res) = $b->smtpsetsettings(
		\OPI\session\gettoken(),
		$settings["usecustom"],
		$settings["relay"],
		$settings["port"],
		$settings["username"],
		$settings["password"]
		);

	return $status;
}
