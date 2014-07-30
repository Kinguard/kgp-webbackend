<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace OPI\GroupModel;

require_once 'models/OPIBackend.php';


function creategroup( $group )
{
    $b = \OPIBackend::instance();

    list($status, $res) = $b->addgroup( \OPI\session\gettoken(), $group );

    return $status?$group:false;
}

function adduser($group, $member)
{

    $b = \OPIBackend::instance();

    list($status, $res) = $b->addgroupmember( \OPI\session\gettoken(), $group, $member );

    return $status;
}

function getusers( $group )
{
    $b = \OPIBackend::instance();

    list($status, $res) = $b->getgroupmembers( \OPI\session\gettoken(), $group );

    return $status?$res["members"]:false;
}


function getgroups()
{
    $b = \OPIBackend::instance();

    list($status, $res) = $b->getgroups( \OPI\session\gettoken() );

    return $status?$res["groups"]:false;
}


function deletegroup($group)
{
    $b = \OPIBackend::instance();

    list($status, $res) = $b->deletegroup( \OPI\session\gettoken(), $group );

    return $status;
}

function removeuser($group, $user)
{
    $b = \OPIBackend::instance();

    list($status, $res) = $b->deletegroupuser( \OPI\session\gettoken(), $group, $user );

    return $status;
}


function groupexists( $group )
{
    $groups = getgroups();

    return in_array($group, $groups);
}


function useringroup($group, $user)
{
    $users = getusers($group);

    return in_array($user, $users);
}
