<h3>Contact Detail</h3>
<table class="tablesorter">
<thead>
<tr><th>Phone</th><th>Name</th><th>Address</th><th>Email</th><th>URL</th></tr>
</thead>
<!-- BEGIN OTHERCONTACT -->
<tr><td>{PHONE}</td><td>{NAME}</td><td>{ADDR}</td><td>{EMAIL}</td><td>{URL}</td></tr>
<!-- END OTHERCONTACT -->
</table>
<!-- BEGIN EDITBUTTON -->
{EDITCONTACT}
<!-- END EDITBUTTON -->

<h3>Students</h3>
<table class="tablesorter">
<thead>
<tr><th>Type</th><th>ID</th><th>Name</th><th>PARENTS</th><th>Enrolled</th></tr>
</thead>
<!-- BEGIN STUDENTS -->
<tr><td>{TYPE}</td><td class="ou" onclick="showStudentDetails({ID})" onmouseover="this.style.cursor='pointer'">{ID}</td><td>{NAME}</td><td>{PARENTS}</td><td>{ENROLLED}</tr>
<!-- END STUDENTS -->
</table>

<!-- BEGIN HIDDENFORMS -->
{STUDENTFORM}
{EDITFORM}
<!-- END HIDDENFORMS -->
