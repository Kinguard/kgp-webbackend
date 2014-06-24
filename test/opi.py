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
			return r.json()
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

	def deleteuser(self, uid):
		r = self.s.delete(self.url+"/users/%s"%uid)
		return r.status_code == 200

	def createuser(self, user):
		r = self.s.post(self.url+"/users", user )
		if r.status_code == 200:
			return r.json()["id"]
		return False

	def updateuser(self, id, user):
		r = self.s.put(self.url+"/users/%s"%id, user )
		return r.status_code == 200

	def updatepassword(self, user, new, old):
		r = self.s.post(self.url+"/users/%s/changepassword" % user
						, {"oldpassword":old, "newpassword":new} )
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
		r = self.s.post(self.url+"/updates", {'doupdates': update})
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

	def deleteaddresses(self, domain):
		r = self.s.delete(self.url + "/smtp/domains/%s/addresses" % domain)
		return r.status_code == 200

	def deleteaddress(self, domain, address):
		r = self.s.delete(self.url + "/smtp/domains/%s/addresses/%s" % (domain, address))
		print r.text
		return r.status_code == 200

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

	#
	# Backup
	#
	def getbackupquota(self):
		r = self.s.get(self.url+"/backup/quota" )
		if r.status_code == 200:
			return r.json()
		return False

	def getbackupstatus(self):
		r = self.s.get(self.url+"/backup/status" )
		if r.status_code == 200:
			return r.json()
		return False


	#
	# Backup purchasecodes
	#
	def deletebackupcodes(self):
		r = self.s.delete(self.url+"/backup/subscriptions" )
		return r.status_code == 200

	def deletebackupcode(self, id):
		r = self.s.delete(self.url+"/backup/subscriptions/%d" % id )
		return r.status_code == 200

	def addbackupcode(self, code):
		r = self.s.post(self.url+"/backup/subscriptions", { "code":code} )
		if r.status_code == 200:
			return r.json()["id"]
		return False

	def getbackupcodes(self):
		r = self.s.get(self.url+"/backup/subscriptions" )
		if r.status_code == 200:
			return r.json()
		return False

	def getbackupcode(self, id):
		r = self.s.get(self.url+"/backup/subscriptions/%d" % id )
		if r.status_code == 200:
			return r.json()
		return False

	#
	# Backup settings
	#
	def getbackupsettings(self):
		r = self.s.get(self.url+"/backup/settings" )
		if r.status_code == 200:
			return r.json()
		return False

	def setbackupsettings(self, settings):
		r = self.s.post(self.url+"/backup/settings", settings )
		return r.status_code == 200

	#
	# Network settings
	#
	def getnetworksettings(self):
		r = self.s.get(self.url+"/network/settings" )
		if r.status_code == 200:
			return r.json()
		return False

	def setnetworksettings(self, settings):
		r = self.s.post(self.url+"/network/settings", settings )
		return r.status_code == 200

	def getports(self):
		r = self.s.get(self.url+"/network/ports")
		if r.status_code == 200:
			return r.json()
		return False

	def getport(self, port):
		r = self.s.get(self.url+"/network/ports/%d" % port )
		if r.status_code == 200:
			return r.json()
		return False

	def setports(self, ports):
		r = self.s.post(self.url+"/network/ports", ports )
		return r.status_code == 200

	def setport(self, port, value):
		r = self.s.put(self.url+"/network/ports/%d" % port, {"enabled":value} )
		return r.status_code == 200
	#
	# Group functions
	#
	def deletegroups(self):
		r = self.s.delete(self.url+"/groups" )
		return r.status_code == 200

	def addgroup(self, group):
		r = self.s.post(self.url+"/groups", {'group':group})
		if r.status_code == 200:
			return r.json()["id"]
		return False

	def addusergroup(self, group, user ):
		r = self.s.post(self.url+"/groups/%s"%group, {'user':user})
		return r.status_code == 200

	def getgroupusers(self, group):
		r = self.s.get(self.url+"/groups/%s"%group )
		if r.status_code == 200:
			return r.json()
		return False

	def getgroups(self):
		r = self.s.get(self.url+"/groups" )
		if r.status_code == 200:
			return r.json()
		return False

	def deletegroup(self, group ):
		r = self.s.delete(self.url+"/groups/%s"%group )
		return r.status_code == 200

	def deletegroupuser(self, group, user ):
		r = self.s.delete(self.url+"/groups/%s/%s"%(group, user) )
		return r.status_code == 200

