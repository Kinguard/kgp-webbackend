<?php

namespace OPI\NetworkModel;

function getsettings()
{
	return array(
		"type"			=> "dynamic",
		"ipnumber"		=> "192.168.1.82",
		"netmask"		=> "255.255.255.0",
		"gateway"		=> "192.168.1.1",
		"dns1"			=> "8.8.8.8",
		"dns2"			=> "4.4.4.4"
	);
}

function setdynamic()
{
	
}

function setstatic($ip, $netmask, $gw = "", $dns1="", $dns2="")
{
	
}


function setports( $ports )
{
	foreach( $ports as $port )
	{
		
	}
}

function getports()
{
	return array(
		array( "port"	=> 25, "enabled"	=> True),
		array( "port"	=> 80, "enabled"	=> True),
		array( "port"	=> 143, "enabled"	=> True),
		array( "port"	=> 443, "enabled"	=> True),
		array( "port"	=> 993, "enabled"	=> True),
	);
}


function getportstatus( $port )
{
	$b = \OPIBackend::instance();
	list($status,$res) = $b->networkgetportstatus( \OPI\session\gettoken(),$port);
	
	if($status && isset($res['is_open']) && $res['is_open'] == "yes") {
		return "1";
	} else {
		return "0";
	}
}


function setport( $port, $state )
{
	$b = \OPIBackend::instance();
	list($status,$res) = $b->networksetportstatus( \OPI\session\gettoken(),$port,$state);
	if(!$status) error_log($res."\n",3,"/tmp/webbackend.log");
	return $status;
}

function getopiname()
{
	return "my-opi";
}
function setopiname( $name )
{
}
