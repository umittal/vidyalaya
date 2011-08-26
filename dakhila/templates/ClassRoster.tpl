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
					label: "Click on First Name to see details of Student"
				});
				new dijit.Tooltip({
					connectId: ["tooltipMother"],
					label: "Click on Mother First Name to see details of Family"
				});
			});
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


<h3>Class Roster</h3>


<!-- BEGIN ROSTERHEAD -->
<table>
<tr><td>Class</td><td>{SESSION} - {SHORT} - {LONG}</td></tr>
<tr><td>Location</td><td>Room {ROOM} - {FACILITY}</td></tr>
<tr><td width=100px>Teachers</td><td>{TEACHERS}</td></tr>
</table>
<!-- END ROSTERHEAD -->

<table id="rowspan" cellspacing="0" class="tablesorter">
<colgroup span=5><colgroup span=4>
<thead>
<tr><th colspan=5>Students</th><th colspan=4>Parents</th></tr>
<tr>
<th id="tooltipField" class="ou">First</th><th>Last</th><th>Age</th><th>Grade</th><th>email</th>
<th  id="tooltipMother" class="ou">First</th><th>Last</th><th>Phone</th><th>email</th>
</tr>
</thead>
<!-- BEGIN ROSTERROW -->
<tbody>
<!-- BEGIN MOTHERROW -->
<tr>
<td rowspan=2 class="ou" onclick="showStudentDetails({ID})" onmouseover="this.style.cursor='pointer'">{SFIRST}</td><td rowspan=2>{SLAST}</td><td rowspan=2>{AGE}</td><td rowspan=2>{GRADE}</td><td rowspan=2>{SEMAIL}</td>
<td class="ou" onclick="showFamilyDetails({FAMILYID})" onmouseover="this.style.cursor='pointer'">{PFIRST}</td><td>{PLAST}</td><td>{PHONE}</td><td>{PEMAIL}</td></tr>
<!-- END MOTHERROW -->
<!-- BEGIN FATHERROW -->
<tr style="border-bottom: thin solid  black;"><td>{PFIRST}</td><td>{PLAST}</td><td>{PHONE}</td><td>{PEMAIL}</td></tr>
<!-- END FATHERROW -->
</tbody>
<!-- END ROSTERROW -->
</table>

<script>

