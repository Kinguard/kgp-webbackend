import unittest
from opi import OPI
import pprint

pp = pprint.PrettyPrinter( indent = 4 )
URL = "http://localhost:8000/index.php"
# URL = "http://localhost:8000/"

class TestUser( unittest.TestCase ):

	def setUp( self ):
		self.opi = OPI( URL )

	def testSelfOperations( self ):
		self.assertTrue( self.opi.login( "admin", "secret" ) )

		self.assertTrue( self.opi.deleteusers() )

		user = { "username":"test", "displayname":"Test User", "password":"secret"}
		userid = self.opi.createuser( user )
		self.assertTrue( userid )

		user = { "username":"user", "displayname":"User User", "password":"secret"}
		userid = self.opi.createuser( user )
		self.assertTrue( id )

		self.assertTrue( self.opi.logout() )
		self.assertTrue( self.opi.login( "user", "secret" ) )

		self.assertFalse( self.opi.getuser( "test" ) )

		uc = self.opi.getuser( "user" )
		self.assertTrue( uc )

		uc["displayname"] = "updated name"
		self.assertTrue( self.opi.updateuser( int( uc["id"] ), uc ) )

		u2 = self.opi.getuser( "user" )
		self.assertEqual( uc, u2 )

		uc["username"] = "fake"
		self.assertFalse( self.opi.updateuser( int( uc["id"] ), uc ) )

	def testPassword(self):
		self.assertTrue( self.opi.login( "admin", "secret" ) )

		self.assertTrue( self.opi.deleteusers() )
		self.assertTrue( self.opi.deletegroups() )

		u1 = { "username":"u1", "displayname":"Temp User1", "password":"secret"}
		u2 = { "username":"u2", "displayname":"Temp User2", "password":"secret"}
		uid1 = self.opi.createuser( u1 )
		self.assertTrue( uid1 )
		uid2 = self.opi.createuser( u2 )
		self.assertTrue( uid2 )

		self.assertTrue( self.opi.addgroup("admin") )
		self.assertTrue( self.opi.addusergroup("admin", "u1"))

		# Admin should be able to change password without knowing old pwd
		self.assertTrue( self.opi.updatepassword("u2", "new", ""))
		self.assertTrue( self.opi.logout() )
		self.assertFalse( self.opi.login( "u2", "secret" ) )
		self.assertTrue( self.opi.login( "u2", "new" ) )

		# Ordinary user should not be able to change others pwd
		self.assertFalse( self.opi.updatepassword("u1", "new", "secret"))
		self.assertFalse( self.opi.updatepassword("u1", "new", ""))

		# Ordinary user should not be able to change own pwd without old
		self.assertFalse( self.opi.updatepassword("u2", "update", ""))
		self.assertTrue( self.opi.updatepassword("u2", "update", "new"))

		self.assertTrue( self.opi.logout() )
		self.assertFalse( self.opi.login( "u2", "secret" ) )
		self.assertFalse( self.opi.login( "u2", "new" ) )
		self.assertTrue( self.opi.login( "u2", "update" ) )

		# Test admin user
		self.assertTrue( self.opi.logout() )
		self.assertTrue( self.opi.login( "u1", "secret" ) )

		self.assertTrue( self.opi.updatepassword("u2", "u1test", ""))
		self.assertTrue( self.opi.updatepassword("u2", "u1t2", "nonsens"))

		self.assertTrue( self.opi.logout() )
		self.assertTrue( self.opi.login( "u2", "u1t2" ) )
		self.assertTrue( self.opi.logout() )
		self.assertTrue( self.opi.login( "u1", "secret" ) )

		# Change own password
		self.assertFalse( self.opi.updatepassword("u1", "u1test", ""))
		self.assertFalse( self.opi.updatepassword("u1", "u1t2", "nonsens"))
		self.assertTrue( self.opi.updatepassword("u1", "u1t2", "secret"))


	def testUser( self ):
		self.assertTrue( self.opi.login( "admin", "secret" ) )

		self.assertTrue( self.opi.deleteusers() )
		self.assertTrue( self.opi.deletegroups() )

		user = { "username":"tempuser", "displayname":"Temp User", "password":"secret"}
		user2 = { "username":"temp2", "displayname":"Temp User2", "password":"secret"}
		userid = self.opi.createuser( user )
		self.assertTrue( userid )
		userid2 = self.opi.createuser( user2 )
		self.assertTrue( userid2 )

		self.assertTrue( self.opi.logout() )
		self.assertTrue( self.opi.login( "tempuser", "secret" ) )

		lg = self.opi.loggedin()
		self.assertTrue( lg["authenticated"])
		self.assertFalse( lg["user"]["admin"])
		self.assertEqual(lg["user"]["displayname"], "Temp User")

		self.assertFalse( self.opi.getuser( "temp2" ) )

		self.assertTrue( self.opi.logout() )
		self.assertTrue( self.opi.login( "admin", "secret" ) )
		self.assertTrue( self.opi.addgroup("admin") )
		self.assertTrue( self.opi.addusergroup("admin", "tempuser"))

		self.assertTrue( self.opi.logout() )
		self.assertTrue( self.opi.login( "tempuser", "secret" ) )

		lg = self.opi.loggedin()
		self.assertTrue( lg["authenticated"])
		self.assertTrue( lg["user"]["admin"])
		self.assertEqual(lg["user"]["displayname"], "Temp User")

		self.assertTrue( self.opi.getuser( "temp2" ) )

	def testCreateDelete( self ):

		self.assertTrue( self.opi.login( "admin", "secret" ) )

		self.assertTrue( self.opi.deleteusers() )

		users = self.opi.getusers()
		self.assertTrue( len( users ) == 0 )

		user = { "username":"tempuser", "displayname":"Temp User", "password":"secret"}
		userid = self.opi.createuser( user )
		self.assertTrue( userid )

		self.assertEqual( len( self.opi.getusers() ), 1 )

		uc = self.opi.getuser( userid )
		self.assertEqual( int( uc["id"] ), userid )
		self.assertEqual( uc["username"], user["username"] )
		self.assertEqual( uc["displayname"], user["displayname"] )
		self.assertEqual( uc["password"], user["password"] )

		user["username"] = "tempuser2"
		uc2id = self.opi.createuser( user )
		self.assertTrue( uc2id )
		self.assertEqual( len( self.opi.getusers() ), 2 )

		self.assertTrue( self.opi.logout() )


	def testGet( self ):

		self.assertTrue( self.opi.login( "admin", "secret" ) )

		self.assertTrue( self.opi.deleteusers() )

		user = { "username":"tempuser", "displayname":"Temp User", "password":"secret"}
		id = self.opi.createuser( user )
		self.assertTrue( id )

		# Get by ID
		uc = self.opi.getuser( id )
		self.assertEqual( int( uc["id"] ), id )
		self.assertEqual( uc["username"], user["username"] )
		self.assertEqual( uc["displayname"], user["displayname"] )
		self.assertEqual( uc["password"], user["password"] )

		self.assertFalse( self.opi.getuser( 0 ) )

		# Get by Username
		uc = self.opi.getuser( "tempuser" )
		self.assertEqual( int( uc["id"] ), id )
		self.assertEqual( uc["username"], user["username"] )
		self.assertEqual( uc["displayname"], user["displayname"] )
		self.assertEqual( uc["password"], user["password"] )

		self.assertFalse( self.opi.getuser( "Unknown user" ) )


	def testDelete( self ):
		self.assertTrue( self.opi.login( "admin", "secret" ) )

		self.assertTrue( self.opi.deleteusers() )

		users = self.opi.getusers()
		self.assertTrue( len( users ) == 0 )

		self.assertFalse( self.opi.deleteuser( 0 ) )

		user = { "username":"tempuser", "displayname":"Temp User", "password":"secret"}
		uid1 = self.opi.createuser( user )
		self.assertTrue( uid1 )

		self.assertTrue( self.opi.deleteuser( uid1 ) )

		self.assertEqual( len( self.opi.getusers() ), 0 )

		uid1 = self.opi.createuser( user )
		user["username"] = "Usertwo"
		uid2 = self.opi.createuser( user )
		self.assertEqual( len( self.opi.getusers() ), 2 )

		self.assertTrue( self.opi.deleteuser( uid1 ) )
		self.assertEqual( len( self.opi.getusers() ), 1 )

		# Delete by name
		self.assertTrue( self.opi.deleteuser( "Usertwo" ) )
		self.assertEqual( len( self.opi.getusers() ), 0 )


	def testUpdate( self ):
		self.assertTrue( self.opi.login( "admin", "secret" ) )

		self.assertTrue( self.opi.deleteusers() )

		user = { "username":"tempuser", "displayname":"Temp User", "password":"secret"}
		uid = self.opi.createuser( user )
		self.assertTrue( uid )

		user["displayname"] = "updated"
		self.assertTrue( self.opi.updateuser( uid, user ) )

		uc = self.opi.getuser( uid )
		self.assertEqual( int( uc["id"] ), uid )
		self.assertEqual( uc["username"], user["username"] )
		self.assertEqual( uc["displayname"], "updated" )
		self.assertEqual( uc["displayname"], user["displayname"] )
		self.assertEqual( uc["password"], user["password"] )

		self.assertEqual( len( self.opi.getusers() ), 1 )

		user["displayname"] = "updated2"
		self.assertTrue( self.opi.updateuser( "tempuser", user ) )

		uc = self.opi.getuser( "tempuser" )
		self.assertEqual( uc["displayname"], "updated2" )

		self.assertEqual( len( self.opi.getusers() ), 1 )

class TestAuth( unittest.TestCase ):

	def setUp( self ):
		self.opi = OPI( URL )

	def testLogin( self ):
		self.assertFalse( self.opi.loggedin()["authenticated"] )
		self.assertFalse( self.opi.login( "Wrong", "info" ) )
		self.assertFalse( self.opi.loggedin()["authenticated"] )
		self.assertTrue( self.opi.login( "user", "secret" ) )
		s = self.opi.loggedin()
		self.assertTrue( s )
		self.assertTrue( s["authenticated" ] )
		self.assertEqual( s["user"]["username"], "user" )
		self.assertEqual( s["user"]["admin"], False )
		self.assertTrue( isinstance( s["user"]["displayname"], unicode ) )
		self.assertTrue( self.opi.logout() )
		self.assertTrue( self.opi.login( "admin", "secret" ) )
		s = self.opi.loggedin()
		self.assertTrue( s )
		self.assertTrue( s["authenticated" ] )
		self.assertEqual( s["user"]["username"], "admin" )
		self.assertEqual( s["user"]["admin"], True )

	def testLogout( self ):
		self.assertTrue( self.opi.logout() )
		self.assertTrue( self.opi.login( "user", "secret" ) )
		self.assertTrue( self.opi.loggedin()["authenticated"] )
		self.assertTrue( self.opi.logout() )
		self.assertTrue( self.opi.logout() )
		self.assertFalse( self.opi.loggedin()["authenticated"] )

class TestUpdates( unittest.TestCase ):

	def setUp( self ):
		self.opi = OPI( URL )

	def testUpdates( self ):
		# Make sure not logged in can do updates
		self.assertFalse( self.opi.getupdates() )
		self.assertFalse( self.opi.setupdates( 1 ) )

		# Login and test updates
		self.assertTrue( self.opi.login( "admin", "secret" ) )
		self.assertTrue( self.opi.setupdates( 1 ) )

		up = self.opi.getupdates()
		self.assertTrue( up["doupdates"] == "1" )

		self.assertTrue( self.opi.setupdates( 0 ) )
		up = self.opi.getupdates()
		self.assertTrue( up["doupdates"] == "0" )

		self.assertFalse( up["doupdates"] == "Nan" )

		up = self.opi.getupdates()
		self.assertTrue( up["doupdates"] == "0" )

		# Make sure logout actually works
		self.assertTrue( self.opi.logout() )

		self.assertFalse( self.opi.getupdates() )
		self.assertFalse( self.opi.setupdates( 1 ) )

		# Make sure ordinary user cant access feature
		self.assertTrue( self.opi.login( "user", "secret" ) )
		self.assertFalse( self.opi.getupdates() )
		self.assertFalse( self.opi.setupdates( 1 ) )

class TestSmtp( unittest.TestCase ):

	def setUp( self ):
		self.opi = OPI( URL )

	def testDomains( self ):
		# test without login
		self.assertFalse( self.opi.getdomains() )
		self.assertFalse( self.opi.adddomain( "example.com" ) )
		self.assertFalse( self.opi.deletedomains() )

		# Test functions
		self.assertTrue( self.opi.login( "admin", "secret" ) )

		# Make sure we have an empty sheet
		self.assertTrue( self.opi.deletedomains() )

		domains = self.opi.getdomains()
		self.assertEqual( len( domains ), 0 )

		# Try add
		id = self.opi.adddomain( "example.com" )
		self.assertTrue( id > 0 )

		domains = self.opi.getdomains()
		self.assertEqual( len( domains ), 1 )
		self.assertEqual( domains[0]["domain"], "example.com" )

		# Try add again
		id1 = self.opi.adddomain( "example.com" )
		self.assertFalse( id1 > 0 )

		id2 = self.opi.adddomain( "google.com" )
		self.assertTrue( id2 > 0 )

		# Delete by ID
		self.assertTrue( self.opi.deletedomain( id2 ) )

		domains = self.opi.getdomains()
		self.assertEqual( len( domains ), 1 )
		self.assertEqual( domains[0]["domain"], "example.com" )

		self.assertTrue( self.opi.deletedomain( id ) )
		domains = self.opi.getdomains()
		self.assertEqual( len( domains ), 0 )

		# Verify delete all
		id1 = self.opi.adddomain( "example.com" )
		self.assertTrue( id1 > 0 )

		id2 = self.opi.adddomain( "google.com" )
		self.assertTrue( id2 > 0 )

		domains = self.opi.getdomains()
		self.assertEqual( len( domains ), 2 )

		self.assertTrue( self.opi.deletedomains() )

		domains = self.opi.getdomains()
		self.assertEqual( len( domains ), 0 )

		# Check that normal user cant edit
		self.assertTrue( self.opi.logout() )
		self.assertTrue( self.opi.login( "user", "secret" ) )
		self.assertFalse( self.opi.getdomains() )
		self.assertFalse( self.opi.adddomain( "example.com" ) )
		self.assertFalse( self.opi.deletedomains() )

	def testAdresses( self ):
		self.assertTrue( self.opi.login( "admin", "secret" ) )

		self.assertTrue( self.opi.deletedomains() )


		id1 = self.opi.adddomain( "example.com" )
		self.assertTrue( id1 > 0 )

		self.assertTrue( self.opi.addaddress( "example.com", "tor", "tor" ) )
		self.assertTrue( self.opi.addaddress( "example.com", "tar", "tor" ) )
		# Add duplicate
		self.assertFalse( self.opi.addaddress( "example.com", "tar", "tor" ) )

		ad = self.opi.getaddresses( "example.com" )
		self.assertTrue( ad )
		self.assertEqual( len( ad ), 2 )

	def testSettings( self ):
		self.assertFalse( self.opi.getsmtpsettings() )
		self.assertTrue( self.opi.login( "admin", "secret" ) )

		s = self.opi.getsmtpsettings()
		self.assertTrue( s )
		settings = { "username":"u1", "password":"MySecret", "relay":"gmail.com", "port":"22"}

		self.assertTrue( self.opi.setsmtpsettings( settings ) )

		del settings["port"]
		self.assertFalse( self.opi.setsmtpsettings( settings ) )

		settings["port"] = "443"
		settings["username"] = "u2"
		self.assertTrue( self.opi.setsmtpsettings( settings ) )

		s = self.opi.getsmtpsettings()
		self.assertTrue( s )

		self.assertEqual( s["port"], "443" )
		self.assertEqual( s["username"], "u2" )
		self.assertEqual( s["password"], "MySecret" )
		self.assertEqual( s["relay"], "gmail.com" )

class TestFetchmail( unittest.TestCase ):

	def setUp( self ):
		self.opi = OPI( URL )

	def testOwnOperations( self ):
		self.assertTrue( self.opi.login( "admin", "secret" ) )
		self.assertTrue( self.opi.deletefetchmailaccounts() )

		a = { "host":"gmail.com", "identity":"other@account", "password":"secret", "username":"dadida" }
		id = self.opi.addfetchmailaccount( a )
		self.assertTrue( id )

		self.assertTrue( self.opi.logout() )
		self.assertTrue( self.opi.login( "user", "secret" ) )

		#
		# Add account
		#
		a = { "host":"gmail.com", "identity":"user1", "password":"secret", "username":"user" }
		self.assertTrue( self.opi.addfetchmailaccount( a ) )
		self.assertFalse( self.opi.addfetchmailaccount( a ) )

		a = { "host":"gmail.com", "identity":"other", "password":"secret", "username":"wronguser" }
		self.assertFalse( self.opi.addfetchmailaccount( a ) )

		#
		# Get accounts
		#
		acs = self.opi.getfetchmailaccounts()
		self.assertTrue( isinstance( acs, list ) )
		self.assertEqual( len( acs ), 1 )

		#
		# Get account
		#

		ac = self.opi.getfetchmailaccount( int( acs[0]["id"] ) )
		self.assertTrue( isinstance( ac, dict ) )
		self.assertEqual( ac["identity"], "user1" )

		self.assertFalse( self.opi.getfetchmailaccount( id ) )

		#
		# Update account
		#
		ac["identity"] = "updated"

		self.assertTrue( self.opi.updatefetchmailaccount( ac["id"], ac ) )
		self.assertFalse( self.opi.updatefetchmailaccount( id, ac ) )

		ac2 = self.opi.getfetchmailaccount( int( acs[0]["id"] ) )
		self.assertEqual( ac["identity"], ac2["identity"] )

		#
		# Delete account
		#
		self.assertFalse( self.opi.deletefetchmailaccount( id ) )
		self.assertTrue( self.opi.deletefetchmailaccount( int( ac["id"] ) ) )

		acs = self.opi.getfetchmailaccounts()
		self.assertTrue( isinstance( acs, list ) )
		self.assertEqual( len( acs ), 0 )

		#
		# Delete accounts
		#
		a = { "host":"gmail.com", "identity":"user1", "password":"secret", "username":"user" }
		self.assertTrue( self.opi.addfetchmailaccount( a ) )

		self.assertTrue( self.opi.deletefetchmailaccounts() )

		acs = self.opi.getfetchmailaccounts()
		self.assertTrue( isinstance( acs, list ) )
		self.assertEqual( len( acs ), 0 )

		#
		# verify as admin
		#
		self.assertTrue( self.opi.logout() )
		self.assertTrue( self.opi.login( "admin", "secret" ) )

		acs = self.opi.getfetchmailaccounts()
		self.assertTrue( isinstance( acs, list ) )
		self.assertEqual( len( acs ), 1 )

		self.assertTrue( self.opi.deletefetchmailaccounts() )

		acs = self.opi.getfetchmailaccounts()
		self.assertTrue( isinstance( acs, list ) )
		self.assertEqual( len( acs ), 0 )

	def testFetchmail( self ):
		account = { "host":"gmail.com", "identity":"user1", "password":"secret", "username":"localuser" }

		# Try add account while not logged in
		self.assertFalse( self.opi.addfetchmailaccount( account ) )

		self.assertTrue( self.opi.login( "admin", "secret" ) )

		self.assertTrue( self.opi.deletefetchmailaccounts() )

		id = self.opi.addfetchmailaccount( account )
		self.assertTrue( id )

		# Try adding same account once more
		self.assertFalse( self.opi.addfetchmailaccount( account ) )

		# Verify get accounts
		a = self.opi.getfetchmailaccounts()
		self.assertTrue( a )
		self.assertEqual( len( a ), 1 )

		# Verify get account
		a = self.opi.getfetchmailaccount( id )
		self.assertTrue( a )

		self.assertEqual( a["host"], 		"gmail.com" )
		self.assertEqual( a["identity"], 	"user1" )
		self.assertEqual( a["password"], 	"secret" )
		self.assertEqual( a["username"], 	"localuser" )

		self.assertTrue( self.opi.deletefetchmailaccount( id ) )
		self.assertFalse( self.opi.deletefetchmailaccount( id ) )

		self.assertFalse( self.opi.getfetchmailaccount( id ) )

		# Verify update
		id = self.opi.addfetchmailaccount( account )
		self.assertTrue( id )

		a = self.opi.getfetchmailaccount( id )
		self.assertTrue( a )

		a["identity"] = "user2"
		self.assertTrue( self.opi.updatefetchmailaccount( a["id"], a ) )

		a = self.opi.getfetchmailaccounts()
		self.assertTrue( a )
		self.assertEqual( len( a ), 1 )

class TestBackup( unittest.TestCase ):

	def setUp( self ):
		self.opi = OPI( URL )

	def testQuota( self ):
		self.assertFalse( self.opi.getbackupquota() )

		self.assertTrue( self.opi.login( "admin", "secret" ) )

		q = self.opi.getbackupquota()
		self.assertTrue( q )
		self.assertTrue( q["total"] > 0 )
		self.assertTrue( q["used"] >= 0 )

	def testPurchase( self ):

		self.assertFalse( self.opi.deletebackupcodes() )

		self.assertTrue( self.opi.login( "admin", "secret" ) )

		# Delete all
		self.assertTrue( self.opi.deletebackupcodes() )

		# Post one
		code = "ThisIsABackupCode"
		id = self.opi.addbackupcode( code )
		self.assertTrue( id )

		# Try add same code again
		self.assertFalse( self.opi.addbackupcode( code ) )

		# Get one
		c2 = self.opi.getbackupcode( id )
		self.assertTrue( c2 )
		self.assertEqual( c2["code"], code )

		# Get all
		codes = self.opi.getbackupcodes()
		self.assertTrue( codes )
		self.assertEqual( len( codes ), 1 )

		id2 = self.opi.addbackupcode( "Code2" )
		self.assertTrue( id2 )

		codes = self.opi.getbackupcodes()
		self.assertTrue( codes )
		self.assertEqual( len( codes ), 2 )

		# Delete one
		self.assertTrue( self.opi.deletebackupcode( id2 ) )

		codes = self.opi.getbackupcodes()
		self.assertTrue( codes )
		self.assertEqual( len( codes ), 1 )

		# Delete all
		self.assertTrue( self.opi.deletebackupcodes() )

		codes = self.opi.getbackupcodes()
		self.assertTrue( codes != False )
		self.assertEqual( len( codes ), 0 )

	def testSettings( self ):

		s1 = { "enabled":True, "location":"remote", "type":"mirror" }

		# try not logged in
		self.assertFalse( self.opi.getbackupsettings() )
		self.assertFalse( self.opi.setbackupsettings( s1 ) )

		self.assertTrue( self.opi.login( "admin", "secret" ) )

		gs = self.opi.getbackupsettings()
		self.assertTrue( gs )
		self.assertTrue( "enabled" in gs )
		self.assertTrue( "type" in gs )
		self.assertTrue( "location" in gs )

		self.assertTrue( self.opi.setbackupsettings( s1 ) )
		gs = self.opi.getbackupsettings()

		self.assertTrue( gs )
		self.assertEqual( s1["enabled"], gs["enabled"] == "True" )
		self.assertEqual( s1["location"], gs["location"] )
		self.assertEqual( s1["type"], gs["type"] )

		del s1["enabled"]
		self.assertFalse( self.opi.setbackupsettings( s1 ) )

class TestGroups( unittest.TestCase ):
	def setUp( self ):
		self.opi = OPI( URL )

	def testNotLoggedin( self ):
		self.assertFalse( self.opi.getgroups() )
		self.assertFalse( self.opi.getgroupusers( "test" ) )
		self.assertFalse( self.opi.addgroup( "test" ) )
		self.assertFalse( self.opi.addusergroup( "test", "user" ) )
		self.assertFalse( self.opi.deletegroups() )
		self.assertFalse( self.opi.deletegroup( "test" ) )
		self.assertFalse( self.opi.deletegroupuser( "test", "user" ) )

	def testUserLoggedin( self ):
		self.assertTrue( self.opi.login( "user", "secret" ) )
		self.assertFalse( self.opi.getgroups() )
		self.assertFalse( self.opi.getgroupusers( "test" ) )
		self.assertFalse( self.opi.addgroup( "test" ) )
		self.assertFalse( self.opi.addusergroup( "test", "user" ) )
		self.assertFalse( self.opi.deletegroups() )
		self.assertFalse( self.opi.deletegroup( "test" ) )
		self.assertFalse( self.opi.deletegroupuser( "test", "user" ) )

	def testCreate( self ):
		self.assertTrue( self.opi.login( "admin", "secret" ) )
		self.assertTrue( self.opi.deletegroups() )
		self.assertTrue( self.opi.deleteusers() )
		self.assertTrue( self.opi.addgroup( "test1" ) )
		self.assertTrue( self.opi.addgroup( "test2" ) )

		gr = self.opi.getgroups()
		self.assertTrue( isinstance( gr, list ) )
		self.assertEqual( len( gr ), 2 )

		self.assertTrue( self.opi.createuser( {"username":"u1", "displayname":"testuser1", "password":"secret"} ) )
		self.assertTrue( self.opi.createuser( {"username":"u2", "displayname":"testuser2", "password":"secret"} ) )
		self.assertTrue( self.opi.addusergroup( "test1", "u1" ) )
		self.assertTrue( self.opi.addusergroup( "test2", "u1" ) )
		self.assertTrue( self.opi.addusergroup( "test1", "u2" ) )

		us1 = self.opi.getgroupusers( "test1" )
		self.assertTrue( isinstance( us1, list ) )
		self.assertEqual( len( us1 ), 2 )

		us2 = self.opi.getgroupusers( "test2" )
		self.assertTrue( isinstance( us2, list ) )
		self.assertEqual( len( us2 ), 1 )

	def testDelete( self ):
		self.assertTrue( self.opi.login( "admin", "secret" ) )

		self.assertTrue( self.opi.deletegroups() )
		self.assertTrue( self.opi.deleteusers() )

		self.assertTrue( self.opi.addgroup( "test1" ) )
		self.assertTrue( self.opi.addgroup( "test2" ) )
		self.assertTrue( self.opi.addgroup( "test3" ) )
		self.assertTrue( self.opi.createuser( {"username":"u1", "displayname":"testuser1", "password":"secret"} ) )
		self.assertTrue( self.opi.createuser( {"username":"u2", "displayname":"testuser2", "password":"secret"} ) )
		self.assertTrue( self.opi.addusergroup( "test1", "u1" ) )
		self.assertTrue( self.opi.addusergroup( "test2", "u1" ) )
		self.assertTrue( self.opi.addusergroup( "test1", "u2" ) )

		self.assertTrue( self.opi.deletegroupuser( "test1", "u2" ) )
		us1 = self.opi.getgroupusers( "test1" )
		self.assertTrue( isinstance( us1, list ) )
		self.assertEqual( len( us1 ), 1 )

		self.assertTrue( self.opi.deletegroup( "test1" ) )
		self.assertFalse( self.opi.getgroupusers( "test1" ) )

		self.assertTrue( self.opi.deletegroups() )
		gr = self.opi.getgroups()
		self.assertTrue( isinstance( gr, list ) )
		self.assertEqual( len( gr ), 0 )

class TestNetwork( unittest.TestCase ):
	def setUp( self ):
		self.opi = OPI( URL )

	def testNotLoggedIn(self):
		self.assertFalse( self.opi.getnetworksettings())
		self.assertFalse( self.opi.setnetworksettings({"type":"dynamic"}))

	def testUserLoggedin( self ):
		self.assertTrue( self.opi.login( "user", "secret" ) )
		self.assertFalse( self.opi.getnetworksettings())
		self.assertFalse( self.opi.setnetworksettings({"type":"dynamic"}))

	def testSettings(self):
		self.assertTrue( self.opi.login( "admin", "secret" ) )

		s = self.opi.getnetworksettings()
		self.assertTrue( isinstance( s, dict) )

		self.assertFalse( self.opi.setnetworksettings( {} ) )
		self.assertFalse( self.opi.setnetworksettings( {"type":"wrong"} ) )
		self.assertFalse( self.opi.setnetworksettings( {"type":"static"} ) )

		self.assertTrue( self.opi.setnetworksettings( {"type":"static", "ipnumber":"192.168.1.4", "netmask":"255.255.255.0"}))
		s = self.opi.getnetworksettings()
		self.assertEqual( s["type"], "static" )
		self.assertEqual( s["ipnumber"], "192.168.1.4" )
		self.assertEqual( s["netmask"], "255.255.255.0" )
		self.assertEqual( s["dns1"], "" )
		self.assertEqual( s["dns2"], "" )
		self.assertEqual( s["gateway"], "" )

		self.assertTrue( self.opi.setnetworksettings( {
													"type":"static",
													"ipnumber":"192.168.1.2",
													"netmask":"255.255.240.0",
													"gateway":"192.168.1.1",
													"dns1":"8.8.8.8",
													"dns2":"4.4.4.4"
		}))
		s = self.opi.getnetworksettings()
		self.assertEqual( s["type"], "static" )
		self.assertEqual( s["ipnumber"], "192.168.1.2" )
		self.assertEqual( s["netmask"], "255.255.240.0" )
		self.assertEqual( s["dns1"], "8.8.8.8" )
		self.assertEqual( s["dns2"], "4.4.4.4" )
		self.assertEqual( s["gateway"], "192.168.1.1" )

		self.assertTrue( self.opi.setnetworksettings( {"type":"dynamic"}))
		s = self.opi.getnetworksettings()

		self.assertEqual( s["type"], "dynamic" )
		self.assertEqual( s["ipnumber"], "" )
		self.assertEqual( s["netmask"], "" )
		self.assertEqual( s["dns1"], "" )
		self.assertEqual( s["dns2"], "" )
		self.assertEqual( s["gateway"], "" )

		self.assertTrue( isinstance( s["currentipnumber"], unicode ) )
		self.assertTrue( isinstance( s["currentnetmask"], unicode ) )
		self.assertTrue( isinstance( s["currentdns1"], unicode ) )
		self.assertTrue( isinstance( s["currentdns2"], unicode ) )
		self.assertTrue( isinstance( s["currentgateway"], unicode ) )


if __name__ == '__main__':
	unittest.main()

