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
    ColumnArray = ["Date", "Start"];


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
<h3>Events</h3>
<table id="maintable" cellspacing="0" class="tablesorter" width="600px">
<thead>
<tr><th>Week</th><th>Date</th><th>Type</th><th>Start</th><th>End</th><th>Description</th></tr>
</thead>

<tbody>
<!-- BEGIN EVENTS -->
<tr><td>{WEEK}</td><td>{DATE}</td><td>{TYPE}</td><td>{START}</td><td>{END}</td><td>{DESCRIPTION}</td></tr>
<!-- END EVENTS -->
</tbody>
</table>
