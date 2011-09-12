        <style type="text/css">
            td { padding-left:10px; }
            th { padding-left:10px; }
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


<h3>Family Details</h3>
<!-- 
<p><a href="/dakhila/php/studentListByLanguage.php">Language</a>, <a href="/htdocs/php/studentListByCulture.php">Culture</a></p>
 -->
 
<ol>

<li class="section">Home</li>

<table>
<!-- BEGIN HOME -->

<tr><td>ID</td><td>{ID} (Home: {HOMEPHONE})</td></tr>
<tr><td rowspan=2>Address</td><td>{LINE1} {LINE2}</td></tr>
<tr><td>{CITY}, {STATE} {ZIP}</td></tr>
<!-- END HOME -->
</table>


<li class="section">Parents</li>


<table>  	  	  
 <thead><tr><th>M/F<th>NAME<th>EMAIL<th>WORK<th>CELL<th>Call</tr></thead>
<!-- BEGIN PARENTS -->
<tr><td>{MF} </td><td>{NAME}</td><td> {EMAIL}</td><td>{WORK}</td><td>{CELL}</td><td>{ISCONTACTABLE}</td></tr>
<!-- END PARENTS -->  	  	  
</table>
<p>

<li class="section">Children</li>
<table>
<thead><tr><th scope="col">ID<th>Gender<th>NAME<th>DOB<th>EMAIL<th width=30px>CELL</th></tr></thead>
<!-- BEGIN CHILDREN -->
<tr><td class="ou" onclick="showStudentDetails({ID})" onmouseover="this.style.cursor='pointer'">{ID}</td><td>{GENDER}</td><td>{NAME}</td><td nowrap="nowrap">{DOB}</td><td>{EMAIL}</td><td>{CELL}</td></tr>

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

</ol>
