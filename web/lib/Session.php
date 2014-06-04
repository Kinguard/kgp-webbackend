<?php
namespace OPI\session;

require_once 'models/UserModel.php';
require_once 'models/GroupModel.php';

const TIMEOUT = 1800; // 30 min

function setup()
{
	initXcacheSessionHandler();

	// ini_set('session.cookie_secure',1);
	ini_set('session.cookie_httponly',1);
	ini_set('session.use_only_cookies',1);

	ini_set('session.name','OPISESSION');

	session_start();

	// Manage timeout ourselfs
	if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > TIMEOUT)) {
		session_unset();
		session_destroy();
	}

	$_SESSION['LAST_ACTIVITY'] = time();

}


function requireloggedin()
{
	if ( ! isloggedin() )
	{
		\Slim\Slim::getInstance()->halt(401);
	}
}

function requireadmin()
{
	if ( ! isadmin() )
	{
		\Slim\Slim::getInstance()->halt(401);
	}
}

function loggedin()
{
	$res["authenticated"] = isloggedin();
	if( isloggedin() )
	{
		$res["user"]["username"] = $_SESSION['USER'];
		$res["user"]["admin"] = $_SESSION['ADMIN'];
		$res["user"]["displayname"] = $_SESSION["DISPLAYNAME"];
	}
	//$res["session"] = $_SESSION;
	echo json_encode( $res );
}

function validateuser($user, $password)
{
    $u = \OPI\UserModel\getuser($user);
    if( ! $u )
    {
        return false;
    }

    return $u["password"] == $password;
}

function login()
{
    $app = \Slim\Slim::getInstance();

    $user 		= $app->request->post('username');
    $password	= $app->request->post('password');

    if( $user == null || $password == null )
    {
            print_r($app->request->params());
            logout();
            $app->halt(401);
    }

    if( ($user == "admin" || $user == "user") && $password == "secret" )
    {
        session_regenerate_id(true);
        $_SESSION["AUTHENTICATED"] = true;
        $_SESSION['USER'] = $user;
        $_SESSION['ADMIN'] = $user == "admin";
        $_SESSION['DISPLAYNAME'] = "Test Användare";
    }
    else if(validateuser($user, $password) )
    {
        $u = \OPI\UserModel\getuser($user);

        session_regenerate_id(true);
        $_SESSION["AUTHENTICATED"] = true;
        $_SESSION['USER'] = $u["username"];
        $_SESSION['ADMIN'] = \OPI\GroupModel\useringroup("admin", $user);
        $_SESSION['DISPLAYNAME'] = $u["displayname"];
    }
    else
    {
        $app->response->setStatus(401);
        print_r($app->request->params());
        logout();
    }
}

function logout()
{
	session_unset();
	session_destroy();
}

function initXcacheSessionHandler()
{

	$open = function($s, $n) {
		return true;
	};

	$read = function($id) {
		if( !xcache_isset( $id ) )
		{
			return '';   //must return '' if no value, as per PHP docs
		}

		return xcache_get($id);
	};

	$write = function($id, $data) {
		if(!$data)
		{
			return false;
		}
		return xcache_set($id, $data, 3600);
	};

	$close = function() {
		return true;
	};

	$destroy = function($id) {
		xcache_unset($id);
	};

	$gc = function($expire) {
		return true;
	};

	session_set_save_handler($open, $close, $read, $write, $destroy, $gc);
}

