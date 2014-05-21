<?php

	set_include_path("../aux".PATH_SEPARATOR ."../lib");

	require 'Slim/Slim.php';
	require 'rb/rb.php';
	require 'User.php';
	require 'Updates.php';
	require 'Smtp.php';

	require_once 'Session.php';
	\OPI\session\setup();


	\Slim\Slim::registerAutoloader();
	
	$db_path = realpath(dirname(__FILE__)."/../data/");
	R::setup('sqlite:'.$db_path.'/test.db');


	$app = new \Slim\Slim(array(
    		'debug' => true
		)
	);

	// Todo: FIX user get own settings

	// Session and authorization
	$app->post(		'/api/session',		"\OPI\session\login");
	$app->get(		'/api/session',		"\OPI\session\loggedin");
	$app->delete(	'/api/session',		"\OPI\session\logout");

	// User functions
	$app->post(		'/api/users', 		"\OPI\session\\requireadmin",	"\OPI\users\createuser");
	$app->put(		'/api/users/:id', 									"\OPI\users\updateuser");
	$app->get(		'/api/users',										"\OPI\users\getusers" );
	$app->get(		'/api/users/:id',									"\OPI\users\getuser");
	$app->delete(	'/api/users/:id',	"\OPI\session\\requireadmin",	"\OPI\users\deleteuser");
	$app->delete(	'/api/users', 		"\OPI\session\\requireadmin",	"\OPI\users\deleteusers");

	// Update functions
	$app->get(		'/api/updates',		"\OPI\session\\requireadmin",	"\OPI\updates\getstate");
	$app->post(		'/api/updates',		"\OPI\session\\requireadmin",	"\OPI\updates\setstate");
	$app->put(		'/api/updates',		"\OPI\session\\requireadmin",	"\OPI\updates\setstate");

	// SMTP
	$app->get(		'/api/smtp/domains',		"\OPI\session\\requireadmin",	"\OPI\smtp\getdomains");
	$app->post(		'/api/smtp/domains',		"\OPI\session\\requireadmin",	"\OPI\smtp\adddomain");
	$app->delete(	'/api/smtp/domains',		"\OPI\session\\requireadmin",	"\OPI\smtp\deletedomains");
	$app->delete(	'/api/smtp/domains/:id',	"\OPI\session\\requireadmin",	"\OPI\smtp\deletedomain");

	// Mail-addresses
	$app->post(		'/api/smtp/domains/:name/addresses',		"\OPI\session\\requireadmin",	"\OPI\smtp\addaddress");
	$app->get(		'/api/smtp/domains/:name/addresses',		"\OPI\session\\requireadmin",	"\OPI\smtp\getaddresses");

	$app->run();

?>
