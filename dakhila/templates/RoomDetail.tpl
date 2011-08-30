        <style type="text/css">
            td { padding-left:10px; }
            th { padding-left:10px; }
            .ou { font-style:oblique;text-decoration:underline; }
        </style>


<h3>Room Detail</h3>
<ol>

<li class="section">Room</li>

 <!-- BEGIN ROOMHEAD -->  
 <table style="padding-left: 20px">
 <tr><td>ID:</td><td align="right">{ID}</td></tr>
 <tr><td>Room Number:</td><td align="right">{ROOMNUMBER}</td></tr>
 <tr><td>CAPACITY:</td><td align="right">{CAPACITY}</td></tr>
 <tr><td>Facility:</td><td>{FACILITY}</td></tr>
</table>
<!-- END ROOMHEAD -->  


<li class="section">Usage</li>
<script type="text/javascript">
$(document).ready(function() {
    $table = $("#maintable")
        .tablesorter({
	widthFixed: true, 
	widgets: ['zebra'], 
	sortList:[ [0,1], [1,0] ],
	headers : { 2:{sorter: false}}
	});

    FilterText = "";
    ColumnArray = ["Session", "Time"];
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
<tr><th>Session</th><th>Time</th>
<th>Class</th><th>Students</th>
</tr>
</thead>


<tbody>
<!-- BEGIN USAGE -->
<tr>
	<td>{SESSION}</td>
	<td>{TIME}</td>
	<td>{CLASS}</td>
	<td>{COUNT}</td>
</tr>
<!-- END USAGE -->
</tbody>
</table>

</ol>
