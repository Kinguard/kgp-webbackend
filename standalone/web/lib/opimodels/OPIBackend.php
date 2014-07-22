<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

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

        fwrite($this->sock,json_encode($req));

        $res=json_decode(fgets($this->sock,16384),true);
        
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
 
    function updateuser($token, $user, $display)
    {
        $req = array();
        $req["cmd"] = "updateuser";
        $req["token"] = $token;
        $req["username"] = $user;
        $req["displayname"] = $display;
        
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
    
    function deleteuser($token, $username)
    {
        $req = array();
        $req["cmd"] = "deleteuser";
        $req["token"] = $token;
        $req["username"] = $username;
        
        return $this->_dorequest($req);
    }
}
