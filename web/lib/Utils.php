<?php

function checknull()
{
	$args = func_get_args();

	foreach( $args as $arg )
	{
		if( $arg == null )
		{
			return false;
		}
	}

	return true;
}

function checknullarray( $arr )
{
    foreach( $arr as $arg )
    {
            if( $arg == null )
            {
                    return false;
            }
    }

    return true;
}

function isloggedin()
{
	return isset($_SESSION["AUTHENTICATED"]) && $_SESSION["AUTHENTICATED"];
}

function isadmin()
{
	return isloggedin() && (isset($_SESSION["ADMIN"]) && $_SESSION["ADMIN"]);
}

function isadminoruser( $user )
{
	if( isadmin() )
	{
		return true;
	}
	else if ( getuser() == $user )
	{
		return true;
	}

	return false;
}

function getuser()
{
	return isset($_SESSION['USER']) ? $_SESSION['USER'] : false;
}

function errmsg($code, $msg)
{
    $app = \Slim\Slim::getInstance();

    $rep = array();
    $rep["errorcode"]=$code;
    $rep["errormessage"]=$msg;

    $app->status($code);
    print json_encode($rep);
    $app->stop();
}