<script type="text/javascript">
$(document).ready(function() {
    $table = $("#maintable")
        .tablesorter({
	widthFixed: true, 
	widgets: ['zebra'], 
	sortList:[ [0,0] ],
	headers : { 4:{sorter: false}}
	});

    FilterText = "";
    ColumnArray = ["", "Language"];


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

<!-- BEGIN HIDDENFORMS -->
{STUDENTFORM}
<!-- END HIDDENFORMS -->

<h3>Wait list (Students) </h3>

<table id="maintable" cellspacing="0" class="tablesorter" width="800px">
 <thead><tr><th>ID</th><th>Language</th><th>First</th><th>Last</th><th>Parents</th><th>Status</th></tr></thead>
<tbody>
<!-- BEGIN WAITLIST -->
<tr>
<td class="ou" onclick="showStudentDetails({ID})" onmouseover="this.style.cursor='pointer'">{ID}</td>
<td>{LANGUAGE}</td>
<td>{FIRST}</td>
<td>{LAST}</td>
<td>{PARENTS}</td>
<td>{STATUS}</td>

</tr>
<!-- END WAITLIST -->
</tbody>
</table>


<!-- BEGIN COUNT -->
<p>{COUNT} rows displayed </p>
<!-- END COUNT -->
