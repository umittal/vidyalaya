        <style type="text/css">
            .ou { font-style:oblique;text-decoration:underline; }
        </style>


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
    ColumnArray = ["Department"];


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

    <script>
         dojo.require("dijit.form.TextBox");    
dojo.require("dijit.form.Form");
dojo.require("dijit.Tooltip");
			// When the DOM and reources are ready....
			dojo.ready(function(){
				// Add tooltip of his picture
				new dijit.Tooltip({
					connectId: ["tooltipField"],
					label: "Click on room id to see the details and usage history of room"
				});
			});

	function showRoomDetails(roomId) {
	var form=dijit.byId("RoomForm");
	var idfield=dijit.byId("what");
	idfield.attr("value", roomId);
	if (form) {form.submit();} else {alert ("form not found");}
      }
    </script>

	<form method="post" action="/dakhila/php/dataViewer2.php?command=Room" style="display:none" id="RoomForm"
	dojoType="dijit.form.Form"
	>
	Room ID: <input type="text" dojoType="dijit.form.TextBox" name="ID" id="what"> 
	<input type="submit" name="go" value="GO"><br>
	</form>


<h3>Available classes</h3>
<table id="maintable" cellspacing="0" class="tablesorter" width="400px">


<thead>
<tr><th>Department</th>
<th>Short</th>
<th>Room</th>
<th>Count</th>
<th>Roster</th>
<th>Capacity</th>
<th>Free</th>
</tr>
</thead>


<tbody>
<!-- BEGIN CLASS -->
<tr>
	<td>{DEPARTMENT}</td>
	<td align="right">{SHORT}</td>
	<td class="ou" onclick="showRoomDetails({ROOMID})" onmouseover="this.style.cursor='pointer'" align="right">{ROOM}</td>
	<td align="right">{COUNT}</td>
	<td align="center">{ID}</td>
	<td align="right">{CAPACITY}</td>
	<td align="right">{FREE}</td>
</tr>
<!-- END CLASS -->
</tbody>
</table>
