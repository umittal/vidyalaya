<script type="text/javascript">
$(document).ready(function() {
    $table = $("#maintable")
        .tablesorter({
	widthFixed: true, 
	widgets: ['zebra'], 
	sortList:[ [1,0], [3,0] ],
	headers : { 4:{sorter: false}}
	});
    FilterText = "";
    ColumnArray = ["Week", "", "Type", "Start", "End"];


      for (i=0;i<ColumnArray.length;i++) {
      if (ColumnArray[i] == '') {continue;}
        $("#maintable tbody tr").find("td:eq(" + i + ")").click( function() {
            clickedText = $(this).text();
            FilterText = ((FilterText == clickedText) ? "" : clickedText );
            $.uiTableFilter( $table, FilterText, ColumnArray[i]); 
	    $("#maintable").trigger("applyWidgets");
         });
    }
});


</script>

<!-- BEGIN AUTHORIZED -->
<a href="/dakhila/php/dataViewer2.php?command=newEvent">New Event</a>{EVENTID}
<!-- END  AUTHORIZED -->

<h3>Events</h3>
<table id="maintable" cellspacing="0" class="tablesorter" width="600px">
<thead>
<tr><th>Week</th><th>Date</th><th>Type</th><th>Start</th><th>End</th><th>Description</th></tr>
</thead>

<tbody>
<!-- BEGIN EVENTS -->
<tr><td>{WEEK}</td><td class="ou" onclick="showEventCalendarDetails({ID})" onmouseover="this.style.cursor='pointer'">{DATE}</td><td>{TYPE}</td><td>{START}</td><td>{END}</td><td>{DESCRIPTION}</td></tr>
<!-- END EVENTS -->
</tbody>
</table>

<!-- BEGIN HIDDENFORMS -->
{EVENTCALENDARFORM}
<!-- END HIDDENFORMS -->
