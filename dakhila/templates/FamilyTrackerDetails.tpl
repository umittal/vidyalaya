<style type="text/css">
.ou { font-style:oblique;text-decoration:underline; }
</style>

<script>
  dojo.require("dijit.form.TextBox");    
  dojo.require("dijit.form.Form");
  dojo.require("dijit.Tooltip");

  $(document).ready(function() {
  $table = $("#maintable")
  .tablesorter({
  widthFixed: true, 
  widgets: ['zebra'], 
  sortList:[ [2,0] ],
  headers : { }
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




<h3>Family Tracker Lookup </h3>

<table id="maintable" cellspacing="0" class="tablesorter" width="800px">
 <thead><tr><th>Parents</th>
<!--
<th>Student<th>Age<th>Grade<th>Language</th>
-->
<th>Previous</th><th>Current</th><th>Year</th></tr></thead>
<!-- BEGIN TRACKER -->
<tr>
  <td class="ou" onclick="showFamilyDetails({FAMILYID})" onmouseover="this.style.cursor='pointer'">{PARENT} </td>
<!--
  <td class="ou" onclick="showStudentDetails({STUDENTID})" onmouseover="this.style.cursor='pointer'">{STUDENT}</td>
  <td align="right"> {AGE}</td><td align="right">{GRADE}</td><td align="right">{LANGUAGE}</td>
-->
  <td>{PREVIOUS}</td>
  <td>{CURRENT}</td>
  <td>{YEAR}</td>
</tr>
<!-- END TRACKER -->  	  	  
</table>


