<?php

$basedir = realpath(dirname(__FILE__) . "/../");

set_include_path($basedir . "/aux" . PATH_SEPARATOR . $basedir . "/lib");

require 'Slim/Slim.php';
require_once 'Utils.php';
require 'User.php';
require 'Groups.php';
require 'Updates.php';
require 'Smtp.php';
require 'Fetchmail.php';
require 'Backup.php';
require 'Network.php';
require 'Device.php';
require 'Shell.php';
require 'System.php';
require 'Status.php';

require_once 'Session.php';
\OPI\session\setup();

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim(array(
	//'debug' => true
	)
);

// Todo: FIX user get own settings
// Session and authorization
$app->post('/api/session', "\OPI\session\login");
$app->get('/api/session', "\OPI\session\loggedin");
$app->delete('/api/session', "\OPI\session\logout");

// User functions
$app->post('/api/users', "\OPI\session\\requireadmin", "\OPI\users\createuser");
$app->post('/api/users/:id/changepassword', "\OPI\session\\requireloggedin", "\OPI\users\updatepassword");
$app->put('/api/users/:id', "\OPI\session\\requireloggedin", "\OPI\users\updateuser");
$app->get('/api/users', "\OPI\session\\requireloggedin", "\OPI\users\getusers");
$app->get('/api/users/:id', "\OPI\session\\requireloggedin", "\OPI\users\getuser");
$app->get('/api/users/:id/groups', "\OPI\session\\requireloggedin", "\OPI\users\getgroups");
$app->get('/api/users/:id/identities',"\OPI\session\\requireloggedin","\OPI\users\getidentities");
$app->delete('/api/users/:id', "\OPI\session\\requireadmin", "\OPI\users\deleteuser");
// Only for testing
//$app->delete(	'/api/users', 		"\OPI\session\\requireadmin",		"\OPI\users\deleteusers");

// Group functions
$app->get('/api/groups', "\OPI\session\\requireadmin", "\OPI\groups\getgroups");
$app->get('/api/groups/:name', "\OPI\session\\requireadmin", "\OPI\groups\getusers");
$app->post('/api/groups', "\OPI\session\\requireadmin", "\OPI\groups\creategroup");
$app->post('/api/groups/:name', "\OPI\session\\requireadmin", "\OPI\groups\adduser");
$app->delete('/api/groups/:name', "\OPI\session\\requireadmin", "\OPI\groups\deletegroup");
$app->delete('/api/groups/:name/:user', "\OPI\session\\requireadmin", "\OPI\groups\\removeuser");
// Only for testing
//$app->delete(	'/api/groups',			"\OPI\session\\requireadmin",	"\OPI\groups\deletegroups" );

// SMTP domains
$app->get('/api/smtp/domains', "\OPI\session\\requireloggedin", "\OPI\smtp\getdomains");
$app->post('/api/smtp/domains', "\OPI\session\\requireloggedin", "\OPI\smtp\adddomain");
$app->delete('/api/smtp/domains/:id', "\OPI\session\\requireloggedin", "\OPI\smtp\deletedomain");

// SMTP Mail-addresses
$app->post('/api/smtp/domains/:name/addresses', "\OPI\session\\requireloggedin", "\OPI\smtp\addaddress");
$app->get('/api/smtp/domains/:name/addresses/', "\OPI\session\\requireloggedin", "\OPI\smtp\getaddresses");
$app->get('/api/smtp/domains/:name/addresses/:userfilter', "\OPI\session\\requireloggedin", "\OPI\smtp\getaddresses");
$app->delete('/api/smtp/domains/:name/addresses/:address', "\OPI\session\\requireloggedin", "\OPI\smtp\deleteaddress");

// SMTP Settings
$app->get('/api/smtp/settings', "\OPI\session\\requireadmin", "\OPI\smtp\getsettings");
$app->post('/api/smtp/settings', "\OPI\session\\requireadmin", "\OPI\smtp\setsettings");

// Fetchmail Settings
$app->get('/api/fetchmail/accounts', "\OPI\session\\requireloggedin", "\OPI\\fetchmail\getaccounts");
$app->get('/api/fetchmail/accounts/:id', "\OPI\session\\requireloggedin", "\OPI\\fetchmail\getaccount");
$app->post('/api/fetchmail/accounts', "\OPI\session\\requireloggedin", "\OPI\\fetchmail\addaccount");
$app->put('/api/fetchmail/accounts/:id', "\OPI\session\\requireloggedin", "\OPI\\fetchmail\updateaccount");
$app->delete('/api/fetchmail/accounts/:id', "\OPI\session\\requireloggedin", "\OPI\\fetchmail\deleteaccount");

// Backup Settings
$app->get('/api/backup/quota', "\OPI\session\\requireadmin", "\OPI\\backup\getquota");
$app->get('/api/backup/status', "\OPI\session\\requireadmin", "\OPI\\backup\getstatus");

$app->post('/api/backup/subscriptions', "\OPI\session\\requireadmin", "\OPI\\backup\addsubscription");
$app->get('/api/backup/subscriptions', "\OPI\session\\requireadmin", "\OPI\\backup\getsubscriptions");
$app->get('/api/backup/subscriptions/:id', "\OPI\session\\requireadmin", "\OPI\\backup\getsubscription");
// For debug and test, should not be added in production
//$app->delete('/api/backup/subscriptions', "\OPI\session\\requireadmin", "\OPI\\backup\deletesubscriptions");
//$app->delete('/api/backup/subscriptions/:id', "\OPI\session\\requireadmin", "\OPI\\backup\deletesubscription");

$app->get('/api/backup/settings', "\OPI\session\\requireadmin", "\OPI\\backup\getsettings");
$app->post('/api/backup/settings', "\OPI\session\\requireadmin", "\OPI\\backup\setsettings");
$app->post('/api/backup/start', "\OPI\session\\requireadmin", "\OPI\backup\startbackup");


// Network Settings
$app->get('/api/network/settings', "\OPI\session\\requireadmin", "\OPI\\network\getsettings");
$app->post('/api/network/settings', "\OPI\session\\requireadmin", "\OPI\\network\setsettings");
$app->get('/api/network/ports', "\OPI\session\\requireadmin", "\OPI\\network\getports");
$app->post('/api/network/ports', "\OPI\session\\requireadmin", "\OPI\\network\setports");
$app->get('/api/network/ports/:port', "\OPI\session\\requireadmin", "\OPI\\network\getport");
$app->put('/api/network/ports/:port', "\OPI\session\\requireadmin", "\OPI\\network\setport");

$app->get('/api/network/domains', "\OPI\session\\requireadmin", "\OPI\\network\getdomains");
$app->get('/api/network/opiname', "\OPI\session\\requireadmin", "\OPI\\network\getopiname");
$app->post('/api/network/opiname', "\OPI\session\\requireadmin", "\OPI\\network\setopiname");
$app->post('/api/network/checkopiname', "\OPI\session\\requireloggedin", "\OPI\\network\checkopiname");
$app->get('/api/network/CertSettings', "\OPI\session\\requireadmin", "\OPI\\network\getcert");
//$app->post('/api/network/CertSettings', "\OPI\session\\requireadmin", "\OPI\\network\setcert");
$app->post('/api/network/checkcert', "\OPI\session\\requireadmin", "\OPI\\network\checkcert");
$app->post('/api/network/checkkey', "\OPI\session\\requireadmin", "\OPI\\network\checkkey");

// System settings
$app->get('/api/system/updatesettings', "\OPI\session\\requireadmin", "\OPI\updates\getstate");
$app->post('/api/system/updatesettings', "\OPI\session\\requireadmin", "\OPI\updates\setstate");
$app->get('/api/system/type', "\OPI\\system\gettype");
$app->get('/api/system/unitid', "\OPI\\system\getunitid");
$app->post('/api/system/unitid', "\OPI\\system\setunitid");
$app->get('/api/system/moduleproviders', "\OPI\session\\requireloggedin", "\OPI\\system\getmoduleproviders");
$app->get('/api/system/moduleproviderinfo/:provider', "\OPI\session\\requireloggedin", "\OPI\\system\getmoduleproviderinfo");
$app->post('/api/system/moduleproviders', "\OPI\session\\requireloggedin", "\OPI\\system\updatemoduleproviders");

$app->get('/api/system/upgrade', "\OPI\session\\requireadmin", "\OPI\updates\getupgradeavailable");
$app->post('/api/system/upgrade', "\OPI\session\\requireadmin", "\OPI\updates\startupgrade");
$app->post('/api/system/update', "\OPI\session\\requireadmin", "\OPI\updates\startupdate");

// Status and messages
$app->get('/api/status/messages', "\OPI\session\\requireloggedin", "\OPI\\status\getmessages");
$app->post('/api/status/messages', "\OPI\session\\requireadmin", "\OPI\\status\ackmessage");
$app->get('/api/status/status', "\OPI\session\\requireloggedin", "\OPI\\status\getstatus");
$app->get('/api/status/storage', "\OPI\session\\requireloggedin", "\OPI\\status\getstorage");
$app->get('/api/status/packages', "\OPI\session\\requireloggedin", "\OPI\\status\getpackages");


// Misc other stuff
$app->post('/api/shutdown', "\OPI\session\\requireadmin", "\OPI\\device\shutdown");

$app->get('/api/shell', "\OPI\session\\requireadmin", "\OPI\\shell\getsettings");
$app->post('/api/shell', "\OPI\session\\requireadmin", "\OPI\\shell\setsettings");

$app->run();

