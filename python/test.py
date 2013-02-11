import sys
sys.path.append('/home/umesh/lib/python')
import gdata.apps.groups.service

service = gdata.apps.groups.service.GroupsService(email="umesh@vidyalaya.us", domain="vidyalaya.us", password="Praveen38")
service.ProgrammaticLogin()

#groupFeedObject = service.RetrieveAllMembers('all2012@vidyalaya.us')
#groupFeedObject = service.RetrieveAllMembers('fundraising@vidyalaya.us')
groupFeedObject = service.RetrieveAllMembers('culture-teachers@vidyalaya.us')

#groupFeedObject = service.RetrieveAllMembers('volunteer2012@vidyalaya.us')
for group in groupFeedObject:
  print group['memberId']

