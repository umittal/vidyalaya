<script>
	function showStudentDetails(studentId) {
	var form=dijit.byId("studentForm");
	var idfield=dijit.byId("studentIdUnique");
	idfield.attr("value", studentId);
	if (form) {form.submit();} else {alert ("form not found");}
      }
</script>


<form method="post" action="/dakhila/php/dataViewer2.php?command=Student" style="display:none" id="studentForm" dojoType="dijit.form.Form">
         Student ID: <input type="text" dojoType="dijit.form.TextBox" name="ID"  id="studentIdUnique"> 
   <input type="submit" name="go" value="GO"><br>
</form>

