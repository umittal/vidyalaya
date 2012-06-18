        <style type="text/css">
            .ou { font-style:oblique;text-decoration:underline; }
        </style>
    <script>
         dojo.require("dijit.form.TextBox");    
dojo.require("dijit.form.Form");
    dojo.require("dijit.form.Button");
	function showStudentDetails(studentId) {
	var form=dijit.byId("studentForm");
	var idfield=dijit.byId("studentId");
	idfield.attr("value", studentId);
	if (form) {form.submit();} else {alert ("form not found");}
      }

	function showFamilyDetails(familyId) {
	var form=dijit.byId("familyForm");
	var idfield=dijit.byId("familyId");
	idfield.attr("value", familyId);
	if (form) {form.submit();} else {alert ("form not found");}
      }
	function showFamilyPdf(familyId) {
	var form=dijit.byId("familyPdf");
	var idfield=dijit.byId("familyId");
	idfield.attr("value", familyId);
	if (form) {form.submit();} else {alert ("form not found");}
      }
	function addnewsletter(role, classId) {
	var form=dijit.byId("newsletterForm");
	var idfield=dijit.byId("role");
	idfield.attr("value", role);
	var idfield=dijit.byId("classId");
	idfield.attr("value", classId);
	if (form) {form.submit();} else {alert ("form not found");}
      }

	function registerevent(mfs, mfsid, item, action) {
	var form=dijit.byId("registereventform");
	var idfield=dijit.byId("MFS");	idfield.attr("value", mfs);
	var idfield=dijit.byId("mfsId");	idfield.attr("value", mfsid);
	var idfield=dijit.byId("itemId");	idfield.attr("value", item);
	var idfield=dijit.byId("action");	idfield.attr("value", action);
	if (form) {form.submit();} else {alert ("form not found");}
      }

    </script>

	<form method="post" action="/dakhila/php/dataViewer2.php?command=Student" style="display:none" id="studentForm"
	dojoType="dijit.form.Form"
	>
	Student ID: <input type="text" dojoType="dijit.form.TextBox" name="ID" id="studentId"> 
	<input type="submit" name="go" value="GO"><br>
	</form>

	<form method="post" action="/dakhila/php/dataViewer2.php?command=Family" style="display:none" id="familyForm"
	dojoType="dijit.form.Form"
	>
	Family ID: <input type="text" dojoType="dijit.form.TextBox" name="ID" id="familyId"> 
	<input type="submit" name="go" value="GO"><br>
	</form>

	<form method="post" action="/dakhila/php/dataViewer2.php?command=FamilyPdf" style="display:none" id="familyPdf"
	dojoType="dijit.form.Form"
	>
	Family ID: <input type="text" dojoType="dijit.form.TextBox" name="ID" id="familyId"> 
	<input type="submit" name="go" value="GO"><br>
	</form>

	<form method="post" action="/dakhila/php/userdata.php?command=editor" style="display:none" id="newsletterForm"
	dojoType="dijit.form.Form"
	>
	Role ID: <input type="text" dojoType="dijit.form.TextBox" name="role" id="role"> 
	Class ID: <input type="text" dojoType="dijit.form.TextBox" name="classId" id="classId"> 
	<input type="submit" name="go" value="GO"><br>
	</form>

	<form method="post" action="/dakhila/php/dataserver.php?command=RegisterEvent" style="display:none" id="registereventform"
	dojoType="dijit.form.Form"
	>
	MFS: <input type="text" dojoType="dijit.form.TextBox" name="MFS" id="MFS"> 
	MFS ID: <input type="text" dojoType="dijit.form.TextBox" name="mfsId" id="mfsId"> 
	ITEM ID: <input type="text" dojoType="dijit.form.TextBox" name="itemId" id="itemId"> 
	Action: <input type="text" dojoType="dijit.form.TextBox" name="action" id="action"> 
	<input type="submit" name="go" value="GO"><br>
	</form>

	

<h3>Person Details</h3>
 
<ol>
<li class="section">Home</li>

<table class="tablesorter">
<!-- BEGIN HOME -->
<tr><td>ID</td><td>{ID} (Home: {HOMEPHONE})</td></tr>
<tr><td rowspan=2>Address</td><td>{LINE1} {LINE2}</td></tr>
<tr><td>{CITY}, {STATE} {ZIP}</td></tr>
<!-- END HOME -->
<!-- BEGIN STUDENTLINK -->
<tr><td colspan=2 class="ou" onclick="showStudentDetails({ID})" onmouseover="this.style.cursor='pointer'">STUDENT DETAILS</td><tr>
<!-- END STUDENTLINK -->

<!-- BEGIN FAMILYLINK -->
<tr><td colspan=2 class="ou" onclick="showFamilyDetails({ID})" onmouseover="this.style.cursor='pointer'">Family Details</td><tr>
<tr><td colspan=2 class="ou" onclick="showFamilyPdf({ID})" onmouseover="this.style.cursor='pointer'">Registration Packet</td><tr>
<!-- END FAMILYLINK -->

</table>

<li class="section">Person</li>
<table class="tablesorter" >  	  	  
<!-- BEGIN PERSON -->
<tr><td>Name</td><td>{NAME}</td></tr>
<tr><td>Gender</td><td>{GENDER}</td></tr>
<tr><td>Email</td><td>{EMAIL}</td></tr>
<tr><td>Work Phone</td><td>{WORK}</td></tr>
<tr><td>Cell Phone</td><td>{CELL}</td></tr>
<!-- END PERSON -->
</table>


<!-- BEGIN SHOWPARENTS -->
<li class="section">Parents</li>
<table class="tablesorter" >  	  	  
 <thead><tr><th>M/F<th>NAME<th>EMAIL<th>WORK<th>CELL<th>Directory</tr></thead>
<!-- BEGIN PARENTS -->
<tr><td  class="ou" onclick="showFamilyDetails({ID})" onmouseover="this.style.cursor='pointer'">{MF} </td><td>{NAME}</td><td> {EMAIL}</td><td>{WORK}</td><td>{CELL}</td><td>{ISCONTACTABLE}</td></tr>
<!-- END PARENTS -->  	  	  
</table>
<!-- END SHOWPARENTS -->  	  	  

<!-- BEGIN VOLUNTEERROLES -->
<li class="section">Volunteer Roles</li>
<table class="tablesorter" >  	  	  
 <thead><tr><th>Role<th>CLASS</th><th>ROOM</th><th>Newsletter</th></tr></thead>
<!-- BEGIN ROLE -->
<tr><td>{ROLE} </td><td>{CLASS}</td><td>{ROOM}</td><td  class="ou" onclick="addnewsletter({ROLE}, {CLASSID})" onmouseover="this.style.cursor='pointer'">Newsletter</td></tr>
<!-- END ROLE -->  	  	  
</table>
<!-- END VOLUNTEERROLES -->  	  	  

<!-- BEGIN SHOWEVENTS -->
<li class="section">Events</li>
<table class="tablesorter" >  	  	  
 <thead><tr><th>ID<th>Event<th>Date</th><th>Cost</th><th>Status</th><th>Action</th></tr></thead>
<!-- BEGIN EVENTREGISTRATION -->
<tr><td>{ID} </td><td><a href="{URL}">{EVENT}</a></td><td> {DATE}</td><td>{COST}</td><td>{STATUS}</td><td>
<div style="display:{DISPLAY}">

<!-- BEGIN ACTIONBUTTON -->
<button data-dojo-type="dijit.form.Button" type="button" >
    <script type="dojo/method" event="onClick" args="evt">
        // Do something:
        registerevent({MFS}, {MFSID}, {ITEM}, "{ACTION}");
    </script>
    {ACTIONLABEL}
</button>
<!-- END ACTIONBUTTON -->
</div>
</td></tr>
<!-- END EVENTREGISTRATION -->
</table>
<!-- END SHOWEVENTS -->  	  	  


</ol>
