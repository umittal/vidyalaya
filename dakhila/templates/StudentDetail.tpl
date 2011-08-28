        <style type="text/css">
            td { padding-left:10px; }
            .ou { font-style:oblique;text-decoration:underline; }
        </style>
    <script>
         dojo.require("dijit.form.TextBox");    
dojo.require("dijit.form.Form");
dojo.require("dijit.Tooltip");
			// When the DOM and reources are ready....
			dojo.ready(function(){
				// Add tooltip of his picture
				new dijit.Tooltip({
					connectId: ["tooltipField"],
					label: "Click here to see details of Family"
				});
			});

	function showFamilyDetails(familyId) {
	var form=dijit.byId("familyForm");
	var idfield=dijit.byId("familyId");
	idfield.attr("value", familyId);
	if (form) {form.submit();} else {alert ("form not found");}
      }
    </script>

	<form method="post" action="/dakhila/php/dataViewer2.php?command=Family" style="display:none" id="familyForm"
	dojoType="dijit.form.Form"
	>
	Family ID: <input type="text" dojoType="dijit.form.TextBox" name="ID" id="familyId"> 
	<input type="submit" name="go" value="GO"><br>
	</form>

<h3>Student Details</h3>
<ol>

<li class="section">Student</li>

<table>
<!-- BEGIN STUDENT -->

<tr><td>ID</td><td>{ID}</td></tr>
<tr><td>Name</td><td>{NAME}</td></tr>
<tr><td>Date of Birth</td><td>{DOB}</td></tr>
<tr><td rowspan=2>Address</td><td>{LINE1} {LINE2}</td></tr>
<tr><td>{CITY}, {STATE} {ZIP}</td></tr>
<tr><td>Phone</td><td>{HOMEPHONE}</td></tr>
<tr><td>Cell</td><td>{CELL}</td></tr>
<tr><td>Email</td><td>{EMAIL}</td></tr>
<!-- END STUDENT -->
</table>


<li class="section">Parents</li>


<table>  	  	  
 <thead><tr><th>M/F<th>NAME<th>EMAIL<th>WORK<th>CELL<th>Call</tr></thead>
<!-- BEGIN FATHER -->
<tr><td>Father </td><td>{NAME}</td><td> {EMAIL}</td><td>{WORK}</td><td>{CELL}</td><td>{ISCONTACTABLE}</td></tr>
<!-- END FATHER -->  	  	  
<!-- BEGIN MOTHER -->
<tr><td>Mother </td><td id="tooltipField" class="ou" onclick="showFamilyDetails({FAMILYID})" onmouseover="this.style.cursor='pointer'">{NAME}</td><td> {EMAIL}</td><td>{WORK}</td><td>{CELL}</td><td>{ISCONTACTABLE}</td></tr>
<!-- END MOTHER -->  	  	  
</table>
<p>


<li class="section">Class Assignment</li>
<table>
<thead>
<tr><th>Session</th><th>Language</th><th>Culture</th></tr>
</thead>
<tbody>
<!-- BEGIN ENROLLMENT -->
<tr><td>{SESSION}</td><td>{LANGUAGE}</td><td>{CULTURE}</td></tr>
<!-- END ENROLLMENT -->
<!-- BEGIN KINDERGARTEN -->
<tr><td>{SESSION}</td><td colspan=2>{KG}</td></tr>
<!-- END KINDERGARTEN -->
</tbody>
</table>

</ol>
