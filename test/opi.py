import requests

class OPI:

	def __init__(self, url):
		self.url = url+"/api"
		self.s = requests.session()

	#
	# Session management
	#
	def login(self, user, password):
		r = self.s.post(self.url+"/session", {"username":user, "password":password})
		return  r.status_code == 200

	def loggedin(self):
		r = self.s.get( self.url+"/session" )
		if  r.status_code == 200:
			return r.json()["authenticated"]
		return False

	def logout(self):
		r = self.s.delete( self.url+"/session" )
		return  r.status_code == 200

	#
	# User functions
	#
	def deleteusers(self):
		r = self.s.delete(self.url+"/users")
		return  r.status_code == 200

	def deleteuser(self, id):
		r = self.s.delete(self.url+"/users/%d"%id)
		return r.status_code == 200
	
	def createuser(self, user):
		r = self.s.post(self.url+"/users", user )
		if r.status_code == 200:
			return r.json()["id"]
		return False

	def updateuser(self, id, user):
		r = self.s.put(self.url+"/users/%d"%id, user )
		return r.status_code == 200


	def getuser(self, id):
		r = self.s.get(self.url+"/users/%s"%id)
		if r.status_code == 200:
			return r.json()
		return False

	def getusers(self):
		r = self.s.get(self.url+"/users")
		if r.status_code == 200:
			return r.json()
		return False

	#
	# Update functions
	#
	def getupdates(self):
		r = self.s.get(self.url+"/updates")
		if r.status_code == 200:
			return r.json()
		return False
	
	def setupdates(self, update):
		r = self.s.post(self.url+"/updates", {'updates': update})
		return r.status_code == 200

	#
	# SMTP functions
	#
	def getdomains(self):
		r = self.s.get(self.url+"/smtp/domains")
		if r.status_code == 200:
			return r.json()
		return False
	
	def adddomain(self, domain):
		r = self.s.post(self.url+"/smtp/domains", {'domain':domain})
		if r.status_code == 200:
			return r.json()["id"]
		return False

	def deletedomains(self):
		r = self.s.delete(self.url+"/smtp/domains")
		return r.status_code == 200

	def deletedomain(self, domain):
		r = self.s.delete(self.url+"/smtp/domains/%s"%domain)
		return r.status_code == 200

	def addaddress(self, domain, address, user):
		r = self.s.post(self.url+"/smtp/domains/%s/addresses" % domain, {'address':address, 'user':user})
		return r.status_code == 200
	
	def getaddresses(self, domain):
		r = self.s.get(self.url+"/smtp/domains/%s/addresses" % domain )
		if r.status_code == 200:
			return r.json()
		return False

	def getsmtpsettings(self):
		r = self.s.get(self.url+"/smtp/settings" )
		if r.status_code == 200:
			return r.json()
		return False
		
	def setsmtpsettings(self, settings):
		r = self.s.post(self.url+"/smtp/settings", settings )
		return r.status_code == 200

	#
	# Fetchmail
	#
	def getfetchmailaccounts(self):
		r = self.s.get(self.url+"/fetchmail/accounts" )
		if r.status_code == 200:
			return r.json()
		return False

	def getfetchmailaccount(self, id):
		r = self.s.get(self.url+"/fetchmail/accounts/%d" % id )
		if r.status_code == 200:
			return r.json()
		return False


	def addfetchmailaccount(self, account):
		r = self.s.post(self.url+"/fetchmail/accounts", account )
		if r.status_code == 200:
			return r.json()["id"]
		return False

	def updatefetchmailaccount(self, id, account):
		r = self.s.put(self.url+"/fetchmail/accounts/%s" % id, account )
		return r.status_code == 200

	def deletefetchmailaccount(self, id):
		r = self.s.delete(self.url+"/fetchmail/accounts/%d" % id )
		return r.status_code == 200

	def deletefetchmailaccounts(self):
		r = self.s.delete(self.url+"/fetchmail/accounts" )
		return r.status_code == 200

