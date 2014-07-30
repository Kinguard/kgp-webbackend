<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace OPI\SMTPModel;


function getdomains()
{
	$ret = array(
		array("domain" => "example.com"),
		array("domain" => "example2.com"),
	);
	
	return $ret;
}

function domainexists( $domain )
{
	return True;
}

function adddomain( $domain )
{
	return $domain;
}

function deletedomain( $domain )
{
	
}

function addaddress( $domain, $address )
{
	
}

function addressexists( $domain, $address )
{
	return false;
}

function deleteaddress( $domain, $address )
{
	
}

function getaddresses( $domain )
{
	$ret = array(
		array( "address"=>"tor", "user"=>"tor"),
		array( "address"=>"jenny", "user"=>"jenny"),
	);
	
	return $ret;
}

function getsettings()
{
	$ret = array(
		"relay"	=> "gmail.com",
		"username"	=> "u2",
		"password"	=> "MySecret",
		"port"		=> "25"
	);
	
	return $ret;
}

function setsettings( $settings )
{
	
}
