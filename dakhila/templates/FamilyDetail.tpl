    <script>
         dojo.require("dijit.form.TextBox");    
dojo.require("dijit.form.Form");
dojo.require("dijit.Tooltip");
dojo.require("dijit.form.Button");
			// When the DOM and reources are ready....
			dojo.ready(function(){
				// Add tooltip of his picture
				new dijit.Tooltip({
					connectId: ["tooltipField"],
					label: "Click here to see details of Family"
				});
			});

	function showStudentDetails(studentId) {
	var form=dijit.byId("studentForm");
	var idfield=dijit.byId("studentId");
	idfield.attr("value", studentId);
	if (form) {form.submit();} else {alert ("form not found");}
      }

      function waitlistme(familyId) {
      	    qObject = new Object();
	    qObject.familyId = familyId;
	    var queryString = dojo.objectToQuery(qObject);
	    
             var xhrArgs ={
                  url: '/dakhila/php/datainsert.php?command=waitlistme',
		  postData:queryString,
                load: function(data, ioArgs) {
		  window.alert('success ' + data);
                },
                error: function(error, ioArgs) {
		  window.alert('fail ' + error);
		}
	   } //xhrargs
	   var deferred = dojo.xhrPost(xhrArgs);
	} //function waitlistme
    </script>

	<form method="post" action="/dakhila/php/dataViewer2.php?command=Student" style="display:none" id="studentForm"
	dojoType="dijit.form.Form"
	>
	Student ID: <input type="text" dojoType="dijit.form.TextBox" name="ID" id="studentId"> 
	<input type="submit" name="go" value="GO"><br>
	</form>


<h3>Family Details </h3>

<table class="vidyalaya">  	  	  
<!-- BEGIN HOME -->
<tr><td>ID</td><td>{ID} (Home Phone: {HOMEPHONE})</td></tr>
<tr><td rowspan=2>Home Address</td><td>{LINE1} {LINE2}</td></tr>
<tr><td>{CITY}, {STATE} {ZIP}</td></tr>
<!-- BEGIN CURRENTSESSION -->
<tr><td>Session</td><td>{SESSIONSTRING}</td></tr>
<!-- END CURRENTSESSION -->

<!-- END HOME -->
</table>

<ol>

<li class="section">Parents</li>


<table class="vidyalaya">  	  	  
 <thead><tr><th>M/F<th>NAME<th>EMAIL<th>WORK<th>CELL<th>Call</tr></thead>
<!-- BEGIN PARENTS -->
<tr><td>{MF} </td><td>{NAME}</td><td> {EMAIL}</td><td>{WORK}</td><td>{CELL}</td><td>{ISCONTACTABLE}</td></tr>
<!-- END PARENTS -->  	  	  
</table>

<li class="section">Children</li> 
<table class="vidyalaya">  	  	  
<thead><tr><th scope="col">ID<th>Gender<th>NAME<th>DOB</th><th>Grade</th><th>LANGUAGE</th><th>EMAIL<th width=30px>CELL</th></tr></thead>
<!-- BEGIN CHILDREN -->
<tr><td class="ou" onclick="showStudentDetails({ID})" onmouseover="this.style.cursor='pointer'">{ID}</td>
  <td>{GENDER}</td>
  <td>{NAME}</td>
  <td nowrap="nowrap">{DOB}</td>
  <td>{GRADE}</td>
  <td>{LANGUAGE}</td>
  <td>{EMAIL}</td>
  <td>{CELL}</td>
</tr>
<!-- END CHILDREN -->
</table>

<!-- BEGIN ADDCHILD -->
<form method="post" action="/dakhila/php/userdata.php?command=addChild">
<input type="text" style="display:none;"  name="familyId" id="familyId" value={ID} /> 
<input type="submit" value="Add Child" />
</form>
<!-- END ADDCHILD -->

<li class="section">Class Assignment 

</li>
<table class="vidyalaya">  	  	  
<thead>
<tr><th scope="col">Name<th>Class<th>Room<th width='400px' align="left">Teachers</tr>
</thead>
<tbody>
<!-- BEGIN REGISTRATION -->
<tr><td rowspan=2>{FIRSTNAME}</td><td>{LANGUAGE}</td><td>{LROOM}</td><td>{LTEACHER}</td></tr>
<tr><td>{CULTURE}</td><td>{CROOM}</td><td>{CTEACHER}</td></tr>
<!-- END REGISTRATION -->
</tbody>
</table>

<li class="section">Registration History</li>
<table class="vidyalaya">  	  	  
<tr><td>
<table>
<thead>
<tr><th scope="col">Year<th>Previous<th>Current<th>Tuition</th><th>Date </th></tr>
</thead>
<tbody>
<!-- BEGIN TRACKER -->
<tr><td>{YEAR}</td><td>{PREVIOUS}</td><td>{CURRENT}</td><td align="right">{TUITION}</td><td>{REGDDATE}</td></tr>
<!-- END TRACKER -->
</tbody>
</table>
</td>
<td>
<!-- BEGIN CHANGEBLOCK -->
{CHANGE}
<!-- END CHANGEBLOCK -->
</td>
</tr>
</table>

<li class="section">Other Contacts</li>
<table class="tablesorter">
<thead>
<tr><th>Phone</th><th>Name</th><th>Address</th><th>Email</th><th>URL</th></tr>
</thead>
<!-- BEGIN OTHERCONTACT -->
<tr><td>{PHONE}</td><td>{NAME}</td><td>{ADDR}</td><td>{EMAIL}</td><td>{URL}</td></tr>
<!-- END OTHERCONTACT -->
</table>

<!-- BEGIN PACKINGLIST -->
<li class="section">Packing List</li>
<ol style="list-style-type: decimal;list-style-position:inside;padding-left:10px;">
<li>Family Detail Page</li>
<li>Handbook</li>
<li>Photograph Page</li>
<!-- BEGIN ENROLLEDCHILDREN -->
<li>Student Folder - {STUDENTNAME}</li>
<!-- END ENROLLEDCHILDREN -->
<li>Calendar</li>
</ol>
<!-- END PACKINGLIST -->


</ol>
<p></p>
<div class="explanation">
Note: Please email corrections to admission2012@vidyalaya.us
</div>