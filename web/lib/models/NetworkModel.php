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
	return True;
}

function setports( $ports )
{
	foreach( $ports as $port )
	{
		
	}
}

function setport( $port, $status )
{
	
}

function getopiname()
{
	return "my-opi";
}
function setopiname( $name )
{
}
