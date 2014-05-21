<?php
namespace OPI\smtp;

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
		[ ':address' => $address, 'domid' => $domainbean->id]);

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

