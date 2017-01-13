<?php

namespace OPI\BackupModel;

function getquota()
{
	$b = \OPIBackend::instance();
	list($status,$res) = $b->backupgetQuota( \OPI\session\gettoken());
	
	return array(
		"total"		=> $res['quota'],
		"used"		=> $res['bytes_used']
		);
}

function getstatus()
{

	$b = \OPIBackend::instance();
	list($status,$res) = $b->backupgetstatus( \OPI\session\gettoken());
	
	//return $res;
	if(isset($res['log']) )
		$log = $res['log'];
	else
		$log = "";

	return array(
		"date"		=> $res["date"], 
		"status"	=> $res["backup_status"],
		"info"		=> $res["info"],
		"log"		=> $res["log"]
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
	list($status,$res) = $b->backupgetsettings( \OPI\session\gettoken());
	
	return $res;
/*
	return array(
		"enabled"	=> true,
		"location"	=> "remote",
		"type"		=> "timeline"
	);
*/
}

function setsettings($location, $type, $AWSkey, $AWSseckey, $AWSbucket)
{
	$b = \OPIBackend::instance();
	list($status,$res) = $b->backupsetsettings( \OPI\session\gettoken(),$location, $type, $AWSkey, $AWSseckey, $AWSbucket);
	
	return $status;
	
}
