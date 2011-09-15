mysqldump --host=vidyalaya.db.4718809.hostedresource.com --user=vidyalaya --password=Praveen38 --add-drop-table vidyalaya > dump
mysql --user=vidyalaya --password=Praveen38 vidyalaya < dump
rm dump
