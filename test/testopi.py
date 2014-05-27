import unittest
from opi import OPI
import pprint

pp = pprint.PrettyPrinter(indent=4)
URL = "http://localhost:8000/index.php"

class TestUser(unittest.TestCase):

	def setUp(self):
		self.opi = OPI(URL)

	def testSelfOperations(self):
		self.assertTrue( self.opi.login("admin","secret") )

		self.assertTrue( self.opi.deleteusers() )

		user = { "username":"test","displayname":"Test User","password":"secret"}
		id = self.opi.createuser( user )
		self.assertTrue( id )

		user = { "username":"user","displayname":"User User","password":"secret"}
		id = self.opi.createuser( user )
		self.assertTrue( id )

		self.assertTrue( self.opi.logout() )
		self.assertTrue( self.opi.login("user","secret") )

		self.assertFalse( self.opi.getuser("test") )

		uc = self.opi.getuser( "user" )
		self.assertTrue( uc )

		uc["displayname"] = "updated name"
		self.assertTrue( self.opi.updateuser( int(uc["id"]), uc) )

		u2 = self.opi.getuser( "user" )
		self.assertEqual( uc, u2)

		uc["username"] = "fake"
		self.assertFalse( self.opi.updateuser( int(uc["id"]), uc) )



	def testCreateDelete(self):

		self.assertTrue( self.opi.login("admin","secret") )

		self.assertTrue( self.opi.deleteusers() )

		users = self.opi.getusers()
		self.assertTrue( len(users) == 0 )

		user = { "username":"tempuser","displayname":"Temp User","password":"secret"}
		id = self.opi.createuser( user )
		self.assertTrue( id )

		self.assertEqual( len(self.opi.getusers()), 1)

		uc = self.opi.getuser( id )
		self.assertEqual( int(uc["id"]), id)
		self.assertEqual( uc["username"], user["username"])
		self.assertEqual( uc["displayname"], user["displayname"])
		self.assertEqual( uc["password"], user["password"])

		user["username"]="tempuser2"
		self.assertTrue( self.opi.createuser( user ) )
		self.assertEqual( len(self.opi.getusers()), 2)

		self.assertTrue( self.opi.logout() )


	def testGet(self):

		self.assertTrue( self.opi.login("admin","secret") )

		self.assertTrue( self.opi.deleteusers() )

		user = { "username":"tempuser","displayname":"Temp User","password":"secret"}
		id = self.opi.createuser( user )
		self.assertTrue( id )

		# Get by ID
		uc = self.opi.getuser( id )
		self.assertEqual( int(uc["id"]), id)
		self.assertEqual( uc["username"], user["username"])
		self.assertEqual( uc["displayname"], user["displayname"])
		self.assertEqual( uc["password"], user["password"])

		self.assertFalse( self.opi.getuser( 0 ) )

		# Get by Username
		uc = self.opi.getuser( "tempuser" )
		self.assertEqual( int(uc["id"]), id)
		self.assertEqual( uc["username"], user["username"])
		self.assertEqual( uc["displayname"], user["displayname"])
		self.assertEqual( uc["password"], user["password"])

		self.assertFalse( self.opi.getuser( "Unknown user" ) )


	def testDelete(self):
		self.assertTrue( self.opi.login("admin","secret") )

		self.assertTrue( self.opi.deleteusers() )

		users = self.opi.getusers()
		self.assertTrue( len(users) == 0 )

		self.assertFalse( self.opi.deleteuser(0) )

		user = { "username":"tempuser","displayname":"Temp User","password":"secret"}
		id = self.opi.createuser( user )
		self.assertTrue( id )

		self.assertTrue( self.opi.deleteuser( id ) )

		self.assertEqual( len(self.opi.getusers()), 0)

		self.opi.createuser( user )
		user["username"] = "Usertwo"
		id = self.opi.createuser( user )
		self.assertEqual( len(self.opi.getusers()), 2)

		self.assertTrue( self.opi.deleteuser( id ) )
		self.assertEqual( len(self.opi.getusers()), 1)

	def testUpdate(self):
		self.assertTrue( self.opi.login("admin","secret") )

		self.assertTrue( self.opi.deleteusers() )

		user = { "username":"tempuser","displayname":"Temp User","password":"secret"}
		id = self.opi.createuser( user )
		self.assertTrue( id )

		user["displayname"] = "updated"
		self.assertTrue( self.opi.updateuser(id, user) )

		uc = self.opi.getuser( id )
		self.assertEqual( int(uc["id"]), id)
		self.assertEqual( uc["username"], user["username"])
		self.assertEqual( uc["displayname"], "updated")
		self.assertEqual( uc["displayname"], user["displayname"])
		self.assertEqual( uc["password"], user["password"])

		self.assertEqual( len(self.opi.getusers()), 1)

class TestAuth(unittest.TestCase):

	def setUp(self):
		self.opi = OPI(URL)

	def testLogin(self):
		self.assertFalse( self.opi.loggedin()["authenticated"] )
		self.assertFalse( self.opi.login("Wrong","info") )
		self.assertFalse( self.opi.loggedin()["authenticated"] )
		self.assertTrue( self.opi.login("user","secret") )
		s = self.opi.loggedin()
		self.assertTrue( s )
		self.assertTrue( s["authenticated" ] )
		self.assertEqual( s["user"], "user" )
		self.assertEqual( s["admin"], False )
		self.assertTrue( isinstance( s["displayname"], unicode))
		self.assertTrue( self.opi.logout() )
		self.assertTrue( self.opi.login("admin","secret") )
		s = self.opi.loggedin()
		self.assertTrue( s )
		self.assertTrue( s["authenticated" ] )
		self.assertEqual( s["user"], "admin" )
		self.assertEqual( s["admin"], True )

	def testLogout(self):
		self.assertTrue( self.opi.logout() )
		self.assertTrue( self.opi.login("user","secret") )
		self.assertTrue( self.opi.loggedin()["authenticated"] )
		self.assertTrue( self.opi.logout() )
		self.assertTrue( self.opi.logout() )
		self.assertFalse( self.opi.loggedin()["authenticated"] )

class TestUpdates(unittest.TestCase):

	def setUp(self):
		self.opi = OPI(URL)

	def testUpdates(self):
		# Make sure not logged in can do updates
		self.assertFalse( self.opi.getupdates() )
		self.assertFalse( self.opi.setupdates(1) )

		# Login and test updates
		self.assertTrue( self.opi.login("admin","secret") )
		self.assertTrue( self.opi.setupdates(1) )

		up = self.opi.getupdates()
		self.assertTrue( up["doupdates"] == "1")

		self.assertTrue( self.opi.setupdates(0) )
		up = self.opi.getupdates()
		self.assertTrue( up["doupdates"] == "0")

		self.assertFalse( up["doupdates"] == "Nan")

		up = self.opi.getupdates()
		self.assertTrue( up["doupdates"] == "0")

		# Make sure logout actually works
		self.assertTrue( self.opi.logout() )

		self.assertFalse( self.opi.getupdates() )
		self.assertFalse( self.opi.setupdates(1) )

		# Make sure ordinary user cant access feature
		self.assertTrue( self.opi.login("user","secret") )
		self.assertFalse( self.opi.getupdates() )
		self.assertFalse( self.opi.setupdates(1) )

class TestSmtp(unittest.TestCase):

	def setUp(self):
		self.opi = OPI(URL)

	def testDomains(self):
		# test without login
		self.assertFalse( self.opi.getdomains() )
		self.assertFalse( self.opi.adddomain("example.com") )
		self.assertFalse( self.opi.deletedomains() )

		# Test functions
		self.assertTrue( self.opi.login("admin","secret") )

		# Make sure we have an empty sheet
		self.assertTrue( self.opi.deletedomains() )

		domains = self.opi.getdomains()
		self.assertEqual( len(domains), 0 )

		# Try add
		id = self.opi.adddomain("example.com")
		self.assertTrue( id > 0 )

		domains = self.opi.getdomains()
		self.assertEqual( len(domains), 1 )
		self.assertEqual( domains[0]["domain"], "example.com" )

		# Try add again
		id1 = self.opi.adddomain("example.com")
		self.assertFalse( id1 > 0 )

		id2 = self.opi.adddomain("google.com")
		self.assertTrue( id2 > 0 )

		# Delete by ID
		self.assertTrue( self.opi.deletedomain(id2) )

		domains = self.opi.getdomains()
		self.assertEqual( len(domains), 1 )
		self.assertEqual( domains[0]["domain"], "example.com" )

		self.assertTrue( self.opi.deletedomain(id) )
		domains = self.opi.getdomains()
		self.assertEqual( len(domains), 0 )

		# Verify delete all
		id1 = self.opi.adddomain("example.com")
		self.assertTrue( id1 > 0 )

		id2 = self.opi.adddomain("google.com")
		self.assertTrue( id2 > 0 )

		domains = self.opi.getdomains()
		self.assertEqual( len(domains), 2 )

		self.assertTrue( self.opi.deletedomains() )

		domains = self.opi.getdomains()
		self.assertEqual( len(domains), 0 )

		# Check that normal user cant edit
		self.assertTrue( self.opi.logout() )
		self.assertTrue( self.opi.login("user","secret") )
		self.assertFalse( self.opi.getdomains() )
		self.assertFalse( self.opi.adddomain("example.com") )
		self.assertFalse( self.opi.deletedomains() )

	def testAdresses(self):
		self.assertTrue( self.opi.login("admin","secret") )

		self.assertTrue( self.opi.deletedomains() )


		id1 = self.opi.adddomain("example.com")
		self.assertTrue( id1 > 0 )

		self.assertTrue( self.opi.addaddress("example.com","tor", "tor") )
		self.assertTrue( self.opi.addaddress("example.com","tar", "tor") )
		# Add duplicate
		self.assertFalse( self.opi.addaddress("example.com","tar", "tor") )

		ad = self.opi.getaddresses("example.com")
		self.assertTrue( ad )
		self.assertEqual( len(ad), 2)

	def testSettings(self):
		self.assertFalse( self.opi.getsmtpsettings() )
		self.assertTrue( self.opi.login("admin","secret") )

		s = self.opi.getsmtpsettings()
		self.assertTrue( s )
		settings = { "username":"u1","password":"MySecret","relay":"gmail.com", "port":"22"}

		self.assertTrue( self.opi.setsmtpsettings(settings) )

		del settings["port"]
		self.assertFalse( self.opi.setsmtpsettings(settings) )

		settings["port"] = "443"
		settings["username"] = "u2"
		self.assertTrue( self.opi.setsmtpsettings(settings) )

		s = self.opi.getsmtpsettings()
		self.assertTrue( s )

		self.assertEqual( s["port"], "443")
		self.assertEqual( s["username"], "u2")
		self.assertEqual( s["password"], "MySecret")
		self.assertEqual( s["relay"], "gmail.com")

class TestFetchmail(unittest.TestCase):

	def setUp(self):
		self.opi = OPI(URL)

	def testOwnOperations(self):
		self.assertTrue( self.opi.login("admin","secret") )
		self.assertTrue( self.opi.deletefetchmailaccounts()  )

		a = { "host":"gmail.com", "identity":"other@account", "password":"secret","username":"dadida" }
		id = self.opi.addfetchmailaccount( a )
		self.assertTrue( id  )

		self.assertTrue( self.opi.logout() )
		self.assertTrue( self.opi.login("user","secret") )

		#
		# Add account
		#
		a = { "host":"gmail.com", "identity":"user1", "password":"secret","username":"user" }
		self.assertTrue( self.opi.addfetchmailaccount( a ) )
		self.assertFalse( self.opi.addfetchmailaccount( a ) )

		a = { "host":"gmail.com", "identity":"other", "password":"secret","username":"wronguser" }
		self.assertFalse( self.opi.addfetchmailaccount( a ) )

		#
		# Get accounts
		#
		acs = self.opi.getfetchmailaccounts()
		self.assertTrue( isinstance( acs, list ) )
		self.assertEqual( len(acs), 1)

		#
		# Get account
		#

		ac = self.opi.getfetchmailaccount(int(acs[0]["id"]))
		self.assertTrue( isinstance( ac, dict) )
		self.assertEqual( ac["identity"], "user1")

		self.assertFalse( self.opi.getfetchmailaccount(id) )

		#
		# Update account
		#
		ac["identity"] = "updated"

		self.assertTrue( self.opi.updatefetchmailaccount( ac["id"], ac) )
		self.assertFalse( self.opi.updatefetchmailaccount( id, ac) )

		ac2 = self.opi.getfetchmailaccount(int(acs[0]["id"]))
		self.assertEqual( ac["identity"], ac2["identity"])

		#
		# Delete account
		#
		self.assertFalse( self.opi.deletefetchmailaccount( id ) )
		self.assertTrue( self.opi.deletefetchmailaccount( int(ac["id"]) ) )

		acs = self.opi.getfetchmailaccounts()
		self.assertTrue( isinstance( acs, list ) )
		self.assertEqual( len(acs), 0)

		#
		# Delete accounts
		#
		a = { "host":"gmail.com", "identity":"user1", "password":"secret","username":"user" }
		self.assertTrue( self.opi.addfetchmailaccount( a ) )

		self.assertTrue( self.opi.deletefetchmailaccounts() )

		acs = self.opi.getfetchmailaccounts()
		self.assertTrue( isinstance( acs, list ) )
		self.assertEqual( len(acs), 0)

		#
		# verify as admin
		#
		self.assertTrue( self.opi.logout() )
		self.assertTrue( self.opi.login("admin","secret") )

		acs = self.opi.getfetchmailaccounts()
		self.assertTrue( isinstance( acs, list ) )
		self.assertEqual( len(acs), 1)

		self.assertTrue( self.opi.deletefetchmailaccounts() )

		acs = self.opi.getfetchmailaccounts()
		self.assertTrue( isinstance( acs, list ) )
		self.assertEqual( len(acs), 0)


	def testFetchmail(self):
		account = { "host":"gmail.com", "identity":"user1", "password":"secret","username":"localuser" }

		# Try add account while not logged in
		self.assertFalse( self.opi.addfetchmailaccount(account) )

		self.assertTrue( self.opi.login("admin","secret") )

		self.assertTrue( self.opi.deletefetchmailaccounts()  )


