<?php

class OPIBackend
{
	
	private static $instance;
	
	public static function instance()  {
		if ( !isset(self::$instance) ) {
			self::$instance = new self;
		}

		return self::$instance;
	}
	
	var $sock;
	
	function _connect()
	{
		$this->sock = stream_socket_client("unix:///tmp/opib");
		return ! ( $this->sock === FALSE );
	}
	
	function _processreply( $res )
	{
		if( $res["status"]["value"] == 0 )
		{
			return array(true, $res);
		}
		else
		{
			return array(false, $res["status"]["desc"]);
		}
	}
	
	function _dorequest($req)
	{
		if( !$this->_connect() )
		{
			return array(false, "Not connected");
		}

	fwrite($this->sock,json_encode($req, JSON_UNESCAPED_UNICODE ));

		$res=json_decode(fgets($this->sock,16384),true);

	fclose( $this->sock);
	$this->connected = FALSE;

	return $this->_processreply($res);
	}
			
	function login($user, $password)
	{        
		$req = array();
		$req["cmd"] = "login";
		$req["username"] = $user;
		$req["password"] = $password;
		
		return $this->_dorequest($req);
	}
	
	function createuser($token, $user, $password, $display)
	{
		$req = array();
		$req["cmd"] = "createuser";
		$req["token"] = $token;
		$req["username"] = $user;
		$req["password"] = $password;
		$req["displayname"] = $display;
		
		return $this->_dorequest($req);
	}

	function updateuserpassword($token, $user, $password, $newpassword)
	{
		$req = array();
		$req["cmd"] = "updateuserpassword";
		$req["token"] = $token;
		$req["username"] = $user;
		$req["password"] = $password?$password:"";
		$req["newpassword"] = $newpassword;

		return $this->_dorequest($req);
	}

	function updateuser($token, $user, $display, $defaultemail)
	{
		$req = array();
		$req["cmd"] = "updateuser";
		$req["token"] = $token;
		$req["username"] = $user;
		$req["displayname"] = $display;
		$req["defaultemail"] = $defaultemail;
		
		return $this->_dorequest($req);
	}

	function getuser($token, $user)
	{
		$req = array();
		$req["cmd"] = "getuser";
		$req["token"] = $token;
		$req["username"] = $user;
		
		return $this->_dorequest($req);
	}

	function getusers($token)
	{
		$req = array();
		$req["cmd"] = "getusers";
		$req["token"] = $token;
		
		return $this->_dorequest($req);
	}

	function getusergroups( $token, $username )
	{
		$req = array();
		$req["cmd"] = "getusergroups";
		$req["token"] = $token;
		$req["username"] = $username;

		return $this->_dorequest($req);
	}

	function getuseridentities( $token, $username)
	{
		$req = array();
		$req["cmd"]="getuseridentities";
		$req["token"]=$token;
		$req["username"]=$username;
		
		return $this->_dorequest($req);
	}

	function deleteuser($token, $username)
	{
		$req = array();
		$req["cmd"] = "deleteuser";
		$req["token"] = $token;
		$req["username"] = $username;
		
		return $this->_dorequest($req);
	}


	function getgroups($token)
	{
		$req = array();
		$req["cmd"] = "groupsget";
		$req["token"] = $token;

		return $this->_dorequest($req);
	}

	function addgroup($token, $group)
	{
		$req = array();
		$req["cmd"] = "groupadd";
		$req["token"] = $token;
		$req["group"] = $group;

		return $this->_dorequest($req);
	}

	function addgroupmember($token, $group, $member)
	{
		$req = array();
		$req["cmd"] = "groupaddmember";
		$req["token"] = $token;
		$req["group"] = $group;
		$req["member"] = $member;

		return $this->_dorequest($req);
	}

	function getgroupmembers($token, $group)
	{
		$req = array();
		$req["cmd"] = "groupgetmembers";
		$req["token"] = $token;
		$req["group"] = $group;

		return $this->_dorequest($req);
	}

	function deletegroup($token, $group)
	{
		$req = array();
		$req["cmd"] = "groupremove";
		$req["token"] = $token;
		$req["group"] = $group;

		return $this->_dorequest($req);
	}

	function deletegroupuser($token, $group, $member)
	{
		$req = array();
		$req["cmd"] = "groupremovemember";
		$req["token"] = $token;
		$req["group"] = $group;
		$req["member"] = $member;

		return $this->_dorequest($req);
	}

	function shutdown( $token, $action)
	{
		$req = array();
		$req["cmd"] = "shutdown";
		$req["token"] = $token;
		$req["action"] = $action;

		return $this->_dorequest($req);
	}
	
	function updategetstate($token) 
	{
		$req = array();
		$req["cmd"] = "updategetstate";
		$req["token"] = $token;

		return $this->_dorequest($req);
	}

	function updatesetstate($token,$state) 
	{
		$req = array();
		$req["cmd"] = "updatesetstate";
		$req["token"] = $token;
		$req["state"] = $state;

		return $this->_dorequest($req);
	}

	function backupgetsettings($token) 
	{
		$req = array();
		$req["cmd"] = "backupgetsettings";
		$req["token"] = $token;

		return $this->_dorequest($req);
	}

	function backupsetsettings($token,$location, $type, $AWSkey, $AWSseckey, $AWSbucket) 
	{
		$req = array();
		$req["cmd"] = "backupsetsettings";
		$req["token"] = $token;
		$req["type"] = $type;
		$req["location"] = $location;
		$req["AWSkey"] = $AWSkey;
		$req["AWSseckey"] = $AWSseckey;
		$req["AWSbucket"] = $AWSbucket;

		return $this->_dorequest($req);
	}

	function backupgetQuota($token) 
	{
		$req = array();
		$req["cmd"] = "backupgetQuota";
		$req["token"] = $token;

		return $this->_dorequest($req);
	}

	function backupgetstatus($token) 
	{
		$req = array();
		$req["cmd"] = "backupgetstatus";
		$req["token"] = $token;

		return $this->_dorequest($req);
	}

	function smtpgetdomains($token)
	{
		$req = array();
		$req["cmd"] = "smtpgetdomains";
		$req["token"] = $token;

		return $this->_dorequest($req);
	}

	function smtpadddomain($token, $domain)
	{
		$req = array();
		$req["cmd"] = "smtpadddomain";
		$req["token"] = $token;
		$req["domain"] = $domain;

		return $this->_dorequest($req);
	}

	function smtpdeletedomain($token, $domain)
	{
		$req = array();
		$req["cmd"] = "smtpdeletedomain";
		$req["token"] = $token;
		$req["domain"] = $domain;

		return $this->_dorequest($req);
	}

	function smtpgetaddresses($token, $domain)
	{
		$req = array();
		$req["cmd"] = "smtpgetaddresses";
		$req["token"] = $token;
		$req["domain"] = $domain;

		return $this->_dorequest($req);
	}

	function smtpaddaddress($token, $domain, $address, $username)
	{
		$req = array();
		$req["cmd"] = "smtpaddaddress";
		$req["token"] = $token;
		$req["domain"] = $domain;
		$req["address"] = $address;
		$req["username"] = $username;

		return $this->_dorequest($req);
	}

	function smtpdeleteaddress($token, $domain, $address)
	{
		$req = array();
		$req["cmd"] = "smtpdeleteaddress";
		$req["token"] = $token;
		$req["domain"] = $domain;
		$req["address"] = $address;

		return $this->_dorequest($req);
	}

	function smtpgetsettings($token)
	{
		$req = array();
		$req["cmd"] = "smtpgetsettings";
		$req["token"] = $token;

		return $this->_dorequest($req);
	}

	function smtpsetsettings($token, $type, $host, $port, $user, $password, $send, $receive)
	{
		$req = array();
		$req["cmd"] =		"smtpsetsettings";
		$req["token"] =		$token;
		$req["type"] =	$type;
		$req["hostname"] =	$host;
		$req["port"] =		$port;
		$req["username"] =	$user;
		$req["password"] =	$password;
		$req["send"] =		$send;
		$req["receive"] =	$receive;

		return $this->_dorequest($req);
	}

	function networkgetportstatus($token,$port) 
	{
			$req = array();
			$req["cmd"] = "networkgetportstatus";
			$req["token"] = $token;
			$req["port"] = $port;
		
			return $this->_dorequest($req);
	}

	function networksetportstatus($token,$port,$state)
	{
			$req = array();
			$req["cmd"] = "networksetportstatus";
			$req["token"] = $token;
			$req["port"] = $port;
			$req["set_open"] = $state;
			 
			return $this->_dorequest($req);
	}

	function networkgetdomains($token) 
	{
			$req = array();
			$req["cmd"] = "networkgetdomains";
			$req["token"] = $token;
		
			return $this->_dorequest($req);
	}

	function networkgetopiname($token) 
	{
			$req = array();
			$req["cmd"] = "networkgetopiname";
			$req["token"] = $token;
		
			return $this->_dorequest($req);
	}

	function networksetopiname($token, $settings)
	{
		$req = array();
		$req["cmd"] = "networksetopiname";
		$req["token"] = $token;
		$req["hostname"] = $settings['name'];
		$req["domain"] = $settings['domain'];
		$req["dnsenabled"] = $settings['dnsenabled'];
		$req["CertType"] = $settings["CertType"];
		$req["CustomCertVal"] = $settings["CustomCertVal"];
		if (isset($settings["CustomKeyVal"]) && $settings["CustomKeyVal"]) {
			$req["CustomKeyVal"] = $settings["CustomKeyVal"];
		}
		else
		{
			$req["CustomKeyVal"]="";	
		}


		return $this->_dorequest($req);
	}
	
	function networkgetcert($token)
	{
		$req = array();
		$req["cmd"] = "networkgetcert";
		$req["token"] = $token;
		return $this->_dorequest($req);
	}

	function networksetcert($token,$settings)
	{
		$req = array();
		$req["cmd"] = "networksetcert";
		$req["token"] = $token;
		$req["CertType"] = $settings["CertType"];
		$req["CustomCertVal"] = $settings["CustomCertVal"];
		if (isset($settings["CustomKeyVal"]) && $settings["CustomKeyVal"]) {
			$req["CustomKeyVal"] = $settings["CustomKeyVal"];
		}
		else
		{
			$req["CustomKeyVal"]="";	
		}
		return $this->_dorequest($req);
	}

	function networkcheckcert($token,$type,$certval)
	{
		$req = array();
		$req["cmd"] = "networkcheckcert";
		$req["token"] = $token;
		$req["type"] = $type;
		$req["CertVal"] = $certval;
		return $this->_dorequest($req);
	}


	function networkdisabledns($token)
	{
		$req = array();
		$req["cmd"] = "networkdisabledns";
		$req["token"] = $token;
	
		return $this->_dorequest($req);
	}
	function networkgetsettings($token)
	{
		$req = array();
		$req["cmd"] = "getnetworksettings";
		$req["token"] = $token;

		return $this->_dorequest($req);
	}

	function networksetsettings($token, $type, $ipnumber, $netmask, $gateway, $dns)
	{
		$req = array();
		$req["cmd"] = "setnetworksettings";
		$req["token"] = $token;
		$req["type"] = $type;
		$req["ipnumber"] = $ipnumber;
		$req["netmask"] = $netmask;
		$req["gateway"] = $gateway;
		$req["dns"] = $dns;

		return $this->_dorequest($req);
	}

	function fetchmailgetaccounts($token, $username)
	{
		$req = array();
		$req["cmd"] = "fetchmailgetaccounts";
		$req["token"] = $token;
		$req["username"] = $username;

		return $this->_dorequest($req);
	}


	function fetchmailgetaccount($token, $hostname, $identity)
	{
		$req = array();
		$req["cmd"] = "fetchmailgetaccount";
		$req["token"] = $token;
		$req["hostname"] = $hostname;
		$req["identity"] = $identity;

		return $this->_dorequest($req);
	}

	function fetchmailaddaccount($token, $email, $hostname, $identity, $password, $username, $ssl)
	{
		$req = array();
		$req["cmd"] = "fetchmailaddaccount";
		$req["token"] = $token;
		$req["email"] = $email;
		$req["hostname"] = $hostname;
		$req["identity"] = $identity;
		$req["password"] = $password;
		$req["username"] = $username;
		$req["ssl"] = $ssl;

		return $this->_dorequest($req);
	}

	function fetchmailupdateaccount($token, $email, $orighostname, $hostname, 
		$origidentity, $identity, $password, $username, $ssl)
	{
		$req = array();
		$req["cmd"] = "fetchmailupdateaccount";
		$req["token"] = $token;
		$req["email"] = $email;
		$req["orighostname"] = $orighostname;
		$req["hostname"] = $hostname;
		$req["origidentity"] = $origidentity;
		$req["identity"] = $identity;
		$req["password"] = $password;
		$req["username"] = $username;
		$req["ssl"] = $ssl;

		return $this->_dorequest($req);
	}

	function fetchmaildeleteaccount($token, $hostname, $identity)
	{
		$req = array();
		$req["cmd"] = "fetchmaildeleteaccount";
		$req["token"] = $token;
		$req["hostname"] = $hostname;
		$req["identity"] = $identity;

		return $this->_dorequest($req);
	}

	function shellgetsettings($token)
	{
		$req = array();
		$req["cmd"] = "getshellsettings";
		$req["token"] = $token;

		return $this->_dorequest($req);
	}

	function shellsetenabled($token)
	{
		$req = array();
		$req["cmd"] = "doshellenable";
		$req["token"] = $token;

		return $this->_dorequest($req);
	}

	function shellsetdisabled($token)
	{
		$req = array();
		$req["cmd"] = "doshelldisable";
		$req["token"] = $token;

		return $this->_dorequest($req);
	}

	function statusgetmessages($token)
	{
		$req = [
			"cmd" => "dosystemgetmessages",
			"token" => $token
		];
		return $this->_dorequest($req);
	}

	function statusackmessage($token,$id)
	{

		$req = [
			"cmd" => "dosystemackmessage",
			"token" => $token,
			"id" => $id
		];

		return $this->_dorequest($req);

	}

	function statusgetstatus($token)
	{
		$req = [
			"cmd" => "dosystemgetstatus",
			"token" => $token
		];

		return $this->_dorequest($req);
	}

	function statusgetstorage($token)
	{
		$req = [
			"cmd" => "dosystemgetstorage",
			"token" => $token
		];

		return $this->_dorequest($req);
	}

	function statusgetpackages($token)
	{
		$req = [
			"cmd" => "dosystemgetpackages",
			"token" => $token
		];

		return $this->_dorequest($req);
	}

	function systemgetunitid($token)
	{
		$req = [
			"cmd" => "dosystemgetunitid",
			"token" => $token
		];

		$res = $this->_dorequest($req);
		if (isset($res[1]["provider"]) && ($res[1]["provider"] == "kgp" ))
		{
			$res[1]["provider"] = "openproducts";
		}
		return $res;
	}

	function systemsetunitid($token,$unitid,$mpwd,$enabled)
	{
		$req = [
			"cmd" => "dosystemsetunitid",
			"token" => $token,
			"unitid" => $unitid,
			"mpwd" => $mpwd,
			"enabled" => $enabled
		];

		return $this->_dorequest($req);
	}

	function systemgettype($token)
	{
		$req = [
			"cmd" => "dosystemgettype",
			"token" => $token
		];

		return $this->_dorequest($req);
	}

	function systemgetmoduleproviders($token)
	{
		/*
		$req = [
			"cmd" => "dosystemgetmoduleproviders",
			"token" => $token
		];

		return $this->_dorequest($req);
		*/
		$providers = array("providers" => array("OpenProducts","Dummy Provider"));
		return array(true,$providers);				
	}

	function systemgetmoduleproviderinfo($token,$provider)
	{
		// remove all whitespace and make lowercase.
		$provider=str_replace(' ', '', strtolower($provider));
		/*
		$req = [
			"cmd" => "dosystemgetmoduleproviderinfo",
			"token" => $token,
			"provider" => $provider
		];

		return $this->_dorequest($req);
		*/

		switch ($provider) {
			case "openproducts";
				$providerinfo = array(
					"enabled" => true,
					"unitid" => "a6e8e79c-4ffa-42ff-84ae-0e0892c524f1",
					"password" => true
				);
				break;
			case "dummyprovider":
				$providerinfo = array( 
					"enabled" => false,
					"username" => "somedummyvalue",
					"password" => false
				);
				break;
			default:
				return array(false,"");
				break;
			}

		return array(true,$providerinfo);				
	}


	function systemupdatemoduleproviders($token)
	{
		$req = [
			"cmd" => "dosystemupdatemoduleproviders",
			"token" => $token
		];

		return $this->_dorequest($req);
	}


}
