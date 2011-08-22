<h3>Available classes</h3>

<script type="text/javascript">
$(document).ready(function() {
    $table = $("#maintable")
        .tablesorter({
	widthFixed: true, 
	widgets: ['zebra'], 
	sortList:[ [1,0] ],
	headers : { 4:{sorter: false}}
	});

    FilterText = "";
    ColumnArray = ["Department","Room"];
    for (i=0;i<ColumnArray.length;i++) {
        $("#maintable tbody tr").find("td:eq(" + i + ")").click( function() {
            clickedText = $(this).text();
            FilterText = ((FilterText == clickedText) ? "" : clickedText );
            $.uiTableFilter( $table, FilterText, ColumnArray[i]);
        });
    }
});
</script>

<table id="maintable" cellspacing="0" class="tablesorter" width="400px">


<thead>
<tr><th>Department</th><th>Short</th>
<th>Room</th><th>Count</th><th>Roster</th>
</tr>
</thead>


<tbody>
<!-- BEGIN CLASS -->
<tr>
	<td>{DEPARTMENT}</td><td>{SHORT}</td>
	<td align="right">{ROOM}</td>
	<td align="right">{COUNT}</td><td>{ID}</td>
</tr>
<!-- END CLASS -->
</tbody>
</table>
