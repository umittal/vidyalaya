<script>
dojo.require("dijit.Dialog");
function showPersonDetails(emailzigzag) {
  var form=dijit.byId("PersonForm");
  var idfield=dijit.byId("emailzigzag");
  idfield.attr("value", emailzigzag);
  if (form) {form.submit();} else {alert ("form not found");}
}
</script>
<form method="post" action="/dakhila/php/dataViewer2.php?command=person" style="display:none" id="PersonForm" dojoType="dijit.form.Form">

  <input type="text" dojoType="dijit.form.TextBox" name="email" id="emailzigzag"> 
  <input type="submit" name="go" value="GO"><br>
</form>
