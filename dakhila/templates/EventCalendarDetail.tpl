<script>
    dojo.require("dijit.form.Button");
</script>

<!-- BEGIN EVENTCALENDARDETAIL -->
<h4>Calendar Event Detail</h4>
<table class="tablesorter" >  	  	  
<tr><td width="100px">ID</td><td>{ID}</td></tr>
<tr><td>Type</td><td>{TYPE}</td></tr>
<tr><td>Date</td><td>{DATE}</td></tr>
<tr><td>Week Number</td><td>{WEEKNUMBER}</td></tr>
<tr><td>Portal</td><td>{PORTAL}</td></tr>
<tr><td>Start</td><td>{START}</td></tr>
<tr><td>End</td><td>{END}</td></tr>
<tr><td>Class</td><td>{CLASS}</td></tr>
<tr><td>Description</td><td>{DESCRIPTION}</td></tr>
</table>
<!-- END EVENTCALENDARDETAIL -->


<!-- BEGIN BUTTONS -->
<button dojoType="dijit.form.Button" type="button" onClick="deleteme('EventCalendar', {EVENTID});">Deleteme</button>
<!-- END BUTTONS -->
