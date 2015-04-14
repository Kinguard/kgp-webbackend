<?php

namespace OPI\FetchmailModel;

require_once 'models/OPIBackend.php';


function _toid($host,$id)
{
	return urlencode($host."\0".$id);
}

function _fromid($id)
{
	return explode("\0",  urldecode($id), 2);
}

function getaccountbyhost($host, $identity)
{
	$b = \OPIBackend::instance();

	list($status, $res) = $b->fetchmailgetaccount( \OPI\session\gettoken(),
			$host, $identity );

	if( $status )
	{
		return array(
			"email"		=> $res["email"],
			"host"		=> $res["host"],
			"identity"	=> $res["identity"],
			"username"	=> $res["username"],
			"encrypt"	=> $res["ssl"] == "true" ? "1":"0"
		);
	}
	return array();
}

function getaccount($id)
{
	list($host, $identity) = _fromid($id);

	return getaccountbyhost($host, $identity);
}

function getaccounts( $user = NULL)
{
	$b = \OPIBackend::instance();

	list($status, $res) = $b->fetchmailgetaccounts( \OPI\session\gettoken(), $user );

	if( $status )
	{
		$ret = array();
		foreach( $res["accounts"] as $account )
		{
			$ret[] = array(
			"id"		=> _toid($account["host"], $account["identity"]),
			"email"		=> $account["email"],
			"host"		=> $account["host"],
			"identity"	=> $account["identity"],
			"username"	=> $account["username"],
			"encrypt"	=> $account["ssl"] == "true" ? "1":"0"
			);
		}

		return $ret;
	}

	return false;
}

function addaccount( $email, $host, $identity, $password, $username, $ssl)
{
	$b = \OPIBackend::instance();

	list($status, $res) = $b->fetchmailaddaccount( \OPI\session\gettoken(),
		$email,
		$host,
		$identity,
		$password,
		$username,
		$ssl);

	return $status?_toid($host, $identity):$status;
}

function updateaccount( $id, $email, $host, $identity, $password, $username, $ssl)
{
	$b = \OPIBackend::instance();

	list($orighost, $origidentity) = _fromid($id);

	list($status, $res) = $b->fetchmailupdateaccount( \OPI\session\gettoken(),
		$email,
		$orighost,
		$host,
		$origidentity,
		$identity,
		$password,
		$username,
		$ssl);

	return $status?_toid($host, $identity):$status;
}

function deleteaccount( $id )
{
	list($host, $identity) = _fromid($id);
	
	$b = \OPIBackend::instance();

	list($status, $res) = $b->fetchmaildeleteaccount( \OPI\session\gettoken(),
		$host,
		$identity );

	return $status?_toid($host, $identity):$status;
}

