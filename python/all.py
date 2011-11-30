import sys
sys.path.append('/home/umesh/lib/python')
import gdata.apps.groups.service

service = gdata.apps.groups.service.GroupsService(email="umesh@vidyalaya.us", domain="vidyalaya.us", password="Praveen38")
service.ProgrammaticLogin()

#groupFeedObject = service.RetrieveAllMembers('all2011@vidyalaya.us')
groupFeedObject = service.RetrieveAllMembers('volunteer2011@vidyalaya.us')
for group in groupFeedObject:
  print group['memberId']

