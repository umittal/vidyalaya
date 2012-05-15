<script>
	function showContactDetails(phone) {
	var form=dijit.byId("studentForm");
	var idfield=dijit.byId("phone");
	idfield.attr("value", phone);
	if (form) {form.submit();} else {alert ("form not found");}
      }
</script>


<form method="post" action="/dakhila/php/dataViewer2.php?command=OtherContacts" style="display:none" id="studentForm" dojoType="dijit.form.Form">
   Phone: <input type="text" dojoType="dijit.form.TextBox" name="phone" id="phone"> 
   <input type="submit" name="go" value="GO"><br>
</form>

