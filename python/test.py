import gdata.apps.groups.service

def parse_groupfeed(groupFeedObject):
    """Parse a groupFeedObject and get email for each entry."""
    groups = []
    for group in groupFeedObject:
        group_email = group['groupId']
        print group_email
        groups.append(group_email)
    return groups

service = gdata.apps.groups.service.GroupsService(email="umesh@vidyalaya.us", domain="vidyalaya.us", password="Praveen38")
service.ProgrammaticLogin()

groupFeedObject = service.RetrieveAllGroups()
for group in groupFeedObject:
  print group

