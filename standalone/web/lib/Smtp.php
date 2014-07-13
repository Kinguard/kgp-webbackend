<?php
namespace OPI\smtp;

require_once 'Utils.php';

function getdomains()
{
	$app = \Slim\Slim::getInstance();

	$app->response->headers->set('Content-Type', 'application/json');

	$domains = \R::findAll( "domains" );

	print json_encode( \R::exportAll( $domains ) );
}

function adddomain()
{
	$app = \Slim\Slim::getInstance();

	$domain = $app->request->post('domain');

	if ( $domain == null )
	{
		$app->halt(400);
	}

	// Check if domain exists
	$tmpdomain = \R::find( "domains", "where domain = :domain", [ ':domain' => $domain]);
	if( count( $tmpdomain ) > 0 )
	{
		$app->halt(409);
	}

	$d = \R::dispense( "domains" );
	$d->domain = $domain;

	$id = \R::store( $d );

	$app->response->headers->set('Content-Type', 'application/json');

	print '{ "id": '.$id.'}';
}

function deletedomains()
{
	$app = \Slim\Slim::getInstance();

	// TODO: domainaddresses seems to not be cascade deleted
	$domains = \R::wipe( "domains" );
}

function deletedomain($id)
{
	$app = \Slim\Slim::getInstance();

	$domain = \R::load( "domains", $id );

	if ( $domain->id == 0 )
	{
		$app->response->setStatus(404);
	}
	else
	{
		\R::trash($domain);
	}
}

function addaddress( $domain )
{
	$app = \Slim\Slim::getInstance();

	$address = $app->request->post('address');
	$user = $app->request->post('user');

	if ( $address == null || $user == null )
	{
		$app->halt(400);
	}

	// Check if domain exists
	$domainbeans = \R::find( "domains", "where domain = :domain", [ ':domain' => $domain]);
	if( count( $domainbeans ) == 0 )
	{
		$app->halt(401);
	}

	$domainbean = reset($domainbeans);

	// Check that name isn't used
	$domainbeans = \R::find(
		"domainaddress",
		"where address = :address and domains_id = :domid",
		[ ':address' => $address, ':domid' => $domainbean->id]);

	if( count( $domainbeans ) != 0 )
	{
		$app->halt(400);
	}

	$domainaddress = \R::dispense( "domainaddress");

	$domainaddress->address	= $address;
	$domainaddress->user	= $user;

	$domainbean->xownDomainaddressList[] = $domainaddress;

	\R::store( $domainbean );
}

function deleteaddresses( $domain )
{
	$app = \Slim\Slim::getInstance();

        // Check if domain exists
	$domainbeans = \R::find( "domains", "where domain = :domain", [ ':domain' => $domain]);
	if( count( $domainbeans ) == 0 )
	{
		$app->halt(404);
	}

	$domainbean = reset($domainbeans);

	$domainbean->xownDomainaddressList = array();

        \R::store($domainbean);
}

function deleteaddress( $domain, $address )
{
	$app = \Slim\Slim::getInstance();

        // Check if domain exists
	$domainbeans = \R::find( "domains", "where domain = :domain", [ ':domain' => $domain]);
	if( count( $domainbeans ) == 0 )
	{
		$app->halt(404);
	}

	$domainbean = reset($domainbeans);

        $id = 0;
        foreach ( $domainbean->ownDomainaddressList as $a )
        {
            if( $a->address == $address )
            {
                $id = $a->id;
            }
        }

        if( $id == 0)
        {
            $app->halt(404);
        }

        unset($domainbean->ownDomainaddressList[$id]);

        \R::store($domainbean);

}


function getaddresses( $domain )
{

	$app = \Slim\Slim::getInstance();

	// Check if domain exists
	$domainbeans = \R::find( "domains", "where domain = :domain", [ ':domain' => $domain]);
	if( count( $domainbeans ) == 0 )
	{
		$app->halt(404);
	}

	$domainbean = reset($domainbeans);

	$app->response->headers->set('Content-Type', 'application/json');

	print json_encode( \R::exportAll( $domainbean->ownDomainaddressList ) );

}

function getsettings( )
{
	$app = \Slim\Slim::getInstance();

	$app->response->headers->set('Content-Type', 'application/json');

	// Check if domain exists
	$smtp = \R::findAll( "smtpsettings");

	if( count( $smtp ) == 0 )
	{
		$s = \R::dispense( "smtpsettings" );
		$s->relay = "";
		$s->username = "";
		$s->password = "";
		$s->port = 25;

		\R::store( $s );

		print json_encode( $s->export() );
	}
	else
	{
		$smtp = reset($smtp);
		print json_encode( $smtp->export() );
	}

}

function setsettings( )
{
	$app = \Slim\Slim::getInstance();

	$relay 		= $app->request->post('relay');
	$username	= $app->request->post('username');
	$password 	= $app->request->post('password');
	$port 		= $app->request->post('port');

	if( !checknull( $relay, $username, $password, $port ) )
	{
		$app->halt(400);
	}

	// Check if domain exists
	$s = \R::findAll( "smtpsettings");

	if( count( $s ) == 0 )
	{
		$s = \R::dispense( "smtpsettings" );
	}
	else
	{
		$s = reset($s);
	}
	$s->relay 		= $relay;
	$s->username	= $username;
	$s->password 	= $password;
	$s->port 		= $port;

	\R::store( $s );
}
