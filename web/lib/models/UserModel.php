<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace OPI\UserModel;

require_once 'models/OPIBackend.php';


function authenticateuser( $user, $password )
{
    $b = \OPIBackend::instance();
    
    list($status, $res) = $b->login($user, $password);
    
    return $status?$res["token"]:false;
}

/*
 * $user: array with at least username and password
 */
function createuser( $user )
{
    $username   = $user["username"];
    $display    = $user["displayname"];
    $password   = $user["password"];

    $b = \OPIBackend::instance();
    list($status, $rep) = $b->createuser( \OPI\session\gettoken(), $username, $password, $display );

    return $status?$username:false;
}

function updateuser($user)
{
    $username   = $user["username"];
    $display    = $user["displayname"];
    $defaultemail = $user["defaultemail"];

    $b = \OPIBackend::instance();
    list($status, $rep) = $b->updateuser( \OPI\session\gettoken(), $username, $display, $defaultemail );

    return $status;
}

function deleteuser( $username )
{
    $b = \OPIBackend::instance();

    list($status, $rep ) = $b->deleteuser( \OPI\session\gettoken(), $username );
    
    return $status;
}

function deleteusers()
{
    return false;
}


function getusers()
{
    $b = \OPIBackend::instance();

    list($status, $rep ) = $b->getusers( \OPI\session\gettoken() );

    return $status ? $rep["users"] : false;
}


function getuser( $username )
{
	$b = \OPIBackend::instance();

	list($status, $rep) = $b->getuser( \OPI\session\gettoken(), $username );

	if( $status )
	{
		$rep["id"] = $rep["username"];
		$rep["displayname"] = $rep["displayname"];
        if ( isset($rep["defaultemail"]) ) {
            $rep["defaultemail"] = $rep["defaultemail"];
        }

	}

	return $status ? $rep : false;
}

function getgroups( $username )
{
    $b = \OPIBackend::instance();

    list($status, $rep ) = $b->getusergroups( \OPI\session\gettoken(), $username );

    return $status ? $rep["groups"] : false;
}

function getidentities( $username )
{
	$b = \OPIBackend::Instance();
	list( $status,$ids) = $b->getuseridentities( \OPI\session\gettoken(), $username );
	return $status ? $ids : false;

}
function updatepassword( $user, $old, $new)
{
    $b = \OPIBackend::instance();

    list($status, $rep) = $b->updateuserpassword( \OPI\session\gettoken(), $user, $old, $new );

    return $status;
}
