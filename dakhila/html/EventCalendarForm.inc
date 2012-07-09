<script>
	function showEventCalendarDetails(EventCalendarId) {
	var form=dijit.byId("EventCalendarDetailForm");
	var idfield=dijit.byId("EventCalendarId");
	idfield.attr("value", EventCalendarId);
	if (form) {form.submit();} else {alert ("form not found");}
      }
</script>


<form method="post" action="/dakhila/php/userdata.php?command=EventCalendarDetail" style="display:none" id="EventCalendarDetailForm" 
   dojoType="dijit.form.Form">
   <input type="text" dojoType="dijit.form.TextBox" name="EventCalendarId"  id="EventCalendarId"> 
   <input type="submit" name="go" value="GO"><br>
</form>

