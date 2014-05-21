import unittest
from opi import OPI

URL = "http://localhost:8000/index.php"

class TestUser(unittest.TestCase):

	def setUp(self):
		self.opi = OPI(URL)

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
		self.assertFalse( self.opi.loggedin() )
		self.assertFalse( self.opi.login("Wrong","info") )
		self.assertFalse( self.opi.loggedin() )
		self.assertTrue( self.opi.login("user","secret") )
		self.assertTrue( self.opi.loggedin() )

	def testLogout(self):
		self.assertTrue( self.opi.logout() )
		self.assertTrue( self.opi.login("user","secret") )
		self.assertTrue( self.opi.loggedin() )
		self.assertTrue( self.opi.logout() )
		self.assertTrue( self.opi.logout() )
		self.assertFalse( self.opi.loggedin() )

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
		

if __name__=='__main__':
	unittest.main()

