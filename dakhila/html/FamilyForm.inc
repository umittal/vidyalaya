<script>
	 function showFamilyDetails(familyId) {
	 var form=dijit.byId("familyForm");
	 var idfield=dijit.byId("familyIdUnique");
	 idfield.attr("value", familyId);
	 if (form) {form.submit();} else {alert ("form not found");}
	 }
</script>


	<form method="post" action="/dakhila/php/dataViewer2.php?command=Family" style="display:none" id="familyForm"
	dojoType="dijit.form.Form"
	>
	Family ID: <input type="text" dojoType="dijit.form.TextBox" name="ID" id="familyIdUnique"> 
	<input type="submit" name="go" value="GO"><br>
	</form>
