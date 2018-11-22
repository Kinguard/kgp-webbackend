<?php

namespace OPI\NetworkModel;

function getsettings()
{
	$b = \OPIBackend::instance();
	list($status,$res) = $b->networkgetsettings( \OPI\session\gettoken());

	if( $status )
	{
		$ret = array();
		$ret["type"] = $res["type"] == "static" ? "static" : "dynamic";
		$ret["ipnumber"] = $res["ipnumber"];
		$ret["netmask"] = $res["netmask"];
		$ret["gateway"] = $res["gateway"];

		$len = count( $res["dns"] );

		if( $len > 0 )
		{
			$ret["dns1"] = $res["dns"][0];

			if( $len > 1 )
			{
				$ret["dns2"] = $res["dns"][1];
			}
			else
			{
				$ret["dns2"] = "";
			}
		}
		else
		{
			$ret["dns1"] = "";
			$ret["dns2"] = "";
		}

		return $ret;
	}

	return false;
}

function setdynamic()
{
	$b = \OPIBackend::instance();

	list($status,$res) = $b->networksetsettings(
			\OPI\session\gettoken(),
			"dhcp",
			"",
			"",
			"",
			array()
		);
	return $status;
}

function setstatic($ip, $netmask, $gw = "", $dns1="", $dns2="")
{
	$b = \OPIBackend::instance();

	$nss = array();
	if( $dns1 != "" )
	{
		$nss[] = $dns1;
	}
	if( $dns2 != "" )
	{
		$nss[] = $dns2;
	}
	
	list($status,$res) = $b->networksetsettings(
			\OPI\session\gettoken(),
			"static",
			$ip,
			$netmask,
			$gw,
			$nss
		);
	return $status;
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
	return $status;
}

function getdomains()
{
	$b = \OPIBackend::instance();
	list($status,$res) = $b->networkgetdomains( \OPI\session\gettoken());
	if ($status)
	{
		return array( "status" => $status, "availabledomains" => $res["availabledomains"]);	
	}
	else
	{
		return array( "status" => $status, "availabledomains" => "", "message" => $res);	
	}
}

function getopiname()
{
	$b = \OPIBackend::instance();
	list($status,$res) = $b->networkgetopiname( \OPI\session\gettoken());
	if($status)
	{
		return $res;
	}
	else
	{
		return false;
	}
}

function setopiname( $settings )
{
	$b = \OPIBackend::instance();

	return $b->networksetopiname( \OPI\session\gettoken(), $settings );

}

function disabledns()
{
	$b = \OPIBackend::instance();
	list($status,$res) = $b->networkdisabledns( \OPI\session\gettoken());
	return $status;
}

function getCert()
{
	$b = \OPIBackend::instance();
	list($status,$res) = $b->networkgetcert( \OPI\session\gettoken());
	if($status)
	{
		return $res;
	}
	else
	{
		return false;
	}
}

/* Moved to setopiname call **
function setCert($settings)
{
	$b = \OPIBackend::instance();
	$status = $b->networksetcert( \OPI\session\gettoken(), $settings);
	return $status;

}
*/

function checkCert($CertVal)
{
	$b = \OPIBackend::instance();
	list($status,$res) = $b->networkcheckcert( \OPI\session\gettoken(),"cert",$CertVal);
	if($status)
	{
		return $res;
	}
	else
	{
		return false;
	}
}

function checkKey($CertVal)
{
	$b = \OPIBackend::instance();
	list($status,$res) = $b->networkcheckcert( \OPI\session\gettoken(),"key",$CertVal);
	if($status)
	{
		return $res;
	}
	else
	{
		return false;
	}
}
