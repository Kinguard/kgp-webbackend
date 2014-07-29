import Secop

s = Secop.Secop()

s.init("Secret password")

s.sockauth()

(status, users) = s.getusers()

if not status:
	print("Failed to get users")
	exit()

# Remove all users
for user in users["users"]:
	s.removeuser( user )

(status, groups) = s.getgroups()

if not status:
	print("Failed to get groups")
	exit()

# Remove all users
for group in groups["groups"]:
	s.removegroup( group )

s.adduser("admin","secret")
s.adduser("user", "secret")
s.addgroup("admin")
s.addgroupmember("admin","admin")
