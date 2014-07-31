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

    if( $status )
    {
       $ret = array();
       foreach($res["members"] as $user )
       {
           $ret[] = array( "id"=>$user,"name"=>$user);
       }
       return $ret;
    }

    return false;
}


function getgroups()
{
    $b = \OPIBackend::instance();

    list($status, $res) = $b->getgroups( \OPI\session\gettoken() );

    if( $status )
    {
        $ret = array();
        foreach($res["groups"] as $group)
        {
          $ret[]=array("id"=>$group, "name"=>$group);
        }
        return $ret;
    }
    return false;
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

    if( $groups )
    {
        foreach($groups as $grp)
        {
            if( $group == $grp["name"] )
            {
                return true;
            }
        }
    }

    return false;
}


function useringroup($group, $user)
{
    $users = getusers($group);

    if( $users )
    {
        foreach( $users as $usr )
        {
            if( $user == $usr["name"] )
            {
                return true;
            }
        }
    }

    return false;
}
