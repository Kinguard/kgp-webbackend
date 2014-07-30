<?php

namespace OPI\FetchmailModel;


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
	return	array(
			"host"		=> $host,
			"identity"	=> $identity,
			"password"	=> "secretthere",
			"username"	=> "userhere"
		);	
}

function getaccount($id)
{
	list($host, $identity) = _fromid($id);

	return getaccountbyhost($host, $identity);
}

function getaccounts( $user = NULL)
{
	// If user not null, only fetch accounts for that user
	// TODO: Implement
	$ret = array(
		array(
			"id"		=> "id1",
			"host"		=> "gmail.com",
			"identity"	=> "userthere",
			"password"	=> "secretthere",
			"username"	=> "userhere"
		),
		array(
			"id"		=> "id2",
			"host"		=> "gmail.com",
			"identity"	=> "user2there",
			"password"	=> "secretthere",
			"username"	=> "user2here"
		),
	);

	return $ret;
}

function addaccount( $host, $identity, $password, $username)
{
	return _toid($host, $identity);
}

function updateaccount( $host, $identity, $password, $username)
{
	
}

function deleteaccount( $id )
{
	list($host, $identity) = _fromid($id);
	
}

