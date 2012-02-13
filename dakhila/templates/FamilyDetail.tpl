        <style type="text/css">
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

	function showStudentDetails(studentId) {
	var form=dijit.byId("studentForm");
	var idfield=dijit.byId("studentId");
	idfield.attr("value", studentId);
	if (form) {form.submit();} else {alert ("form not found");}
      }
    </script>

	<form method="post" action="/dakhila/php/dataViewer2.php?command=Student" style="display:none" id="studentForm"
	dojoType="dijit.form.Form"
	>
	Student ID: <input type="text" dojoType="dijit.form.TextBox" name="ID" id="studentId"> 
	<input type="submit" name="go" value="GO"><br>
	</form>


<div class="heading1">Family Details </div>

<table>
<!-- BEGIN HOME -->
<tr><td>ID</td><td>{ID} (Home Phone: {HOMEPHONE})</td></tr>
<tr><td rowspan=2>Home Address</td><td>{LINE1} {LINE2}</td></tr>
<tr><td>{CITY}, {STATE} {ZIP}</td></tr>
<!-- END HOME -->
</table>

<ol>

<li class="section">Parents</li>


<table>  	  	  
 <thead><tr><th>M/F<th>NAME<th>EMAIL<th>WORK<th>CELL<th>Call</tr></thead>
<!-- BEGIN PARENTS -->
<tr><td>{MF} </td><td>{NAME}</td><td> {EMAIL}</td><td>{WORK}</td><td>{CELL}</td><td>{ISCONTACTABLE}</td></tr>
<!-- END PARENTS -->  	  	  
</table>

<li class="section">Children</li>
<table>
<thead><tr><th scope="col">ID<th>Gender<th>NAME<th>DOB</th><th>LANGUAGE</th><th>EMAIL<th width=30px>CELL</th></tr></thead>
<!-- BEGIN CHILDREN -->
<tr><td class="ou" onclick="showStudentDetails({ID})" onmouseover="this.style.cursor='pointer'">{ID}</td>
  <td>{GENDER}</td>
  <td>{NAME}</td>
  <td nowrap="nowrap">{DOB}</td>
  <td>{LANGUAGE}</td>
  <td>{EMAIL}</td>
  <td>{CELL}</td>
</tr>
<!-- END CHILDREN -->
</table>



<li class="section">Class Assignment (2011-12)</li>
<table>
<thead>
<tr><th scope="col">Name<th>Class<th>Room<th width='400px'>Teachers</tr>
</thead>
<tbody>
<!-- BEGIN REGISTRATION -->
<tr><td rowspan=2>{FIRSTNAME}</td><td>{LANGUAGE}</td><td>{LROOM}</td><td>{LTEACHER}</td></tr>
<tr><td>{CULTURE}</td><td>{CROOM}</td><td>{CTEACHER}</td></tr>
<!-- END REGISTRATION -->
</tbody>
</table>

<li class="section">Registration History</li>
<table>
<thead>
<tr><th scope="col">Year<th>Previous<th>Current<th>Tuition</tr>
</thead>
<tbody>
<!-- BEGIN TRACKER -->
<tr><td>{YEAR}</td><td>{PREVIOUS}</td><td>{CURRENT}</td><td align="right">{TUITION}</td></tr>
<!-- END TRACKER -->
</tbody>
</table>

</ol>

<p></p>
<div class="explanation">
Note: Please email any changes to admission2012@vidyalaya.us and we will send you an updated package. 
</div>