<?php

namespace OPI\BackupModel;

function getquota()
{
	$b = \OPIBackend::instance();
	list($status,$res) = $b->backup_getQuota( \OPI\session\gettoken());
	
	return array(
		"total"		=> $res['quota'],
		"used"		=> $res['bytes_used']
		);
}

function getstatus()
{

	$b = \OPIBackend::instance();
	list($status,$res) = $b->backup_getstatus( \OPI\session\gettoken());
	
	return array(
		"date"		=> $res["date"], 
		"status"	=> $res["backup_status"],
		"info"		=> $res["info"]
	);
}

function addsubscription( $code )
{
	return $code;
}

function getsubscription( $id )
{
	return array( 
		"id"	=> $id,
		"code"	=> $id
		);
}

function getsubscriptions()
{
	return array(
		array( "id"		=> "ett" ,"code"	=> "ett"),
		array( "id"		=> "två" ,"code"	=> "två"),
		array( "id"		=> "tre" ,"code"	=> "tre"),
		);
}

function deletesubscription( $id )
{
	
}

function getsettings()
{
	$b = \OPIBackend::instance();
	list($status,$res) = $b->backup_getsettings( \OPI\session\gettoken());
	
	return $res;
/*
	return array(
		"enabled"	=> true,
		"location"	=> "remote",
		"type"		=> "timeline"
	);
*/
}

function setsettings($location, $type)
{
	$b = \OPIBackend::instance();
	list($status,$res) = $b->backup_setsettings( \OPI\session\gettoken(),$location,$type);
	
	return $status;
	
}
