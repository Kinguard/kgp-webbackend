<?php

namespace OPI\BackupModel;

function getquota()
{
	return array(
		"total"		=> 8589934592,
		"used"		=> 2061584302
		);
}

function getstatus()
{
	return array(
		"date"		=> 1401134452, 
		"status"	=> "successful",
		"info"		=> "Some interesting info"
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
	return array(
		"enabled"	=> true,
		"location"	=> "remote",
		"type"		=> "timeline"
	);
}

function setsettings($enabled, $location, $type)
{
	
}