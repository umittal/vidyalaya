python all.py | sort -u > /tmp/old
cat ~/Dropbox/Vidyalaya-Roster/2011-12/mailinglist/*.txt | tr '[A-Z]' '[a-z]' | sort -u | sed 's/^ *//g'  > /tmp/new
diff /tmp/old /tmp/new