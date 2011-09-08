        <style type="text/css">
            .ou { font-style:oblique;text-decoration:underline; }
        </style>

<script type="text/javascript">
$(document).ready(function() {
    $table = $("#maintable")
        .tablesorter({
	widthFixed: true, 
	widgets: ['zebra'], 
	sortList:[ [2,0] ],
	headers : { 1:{sorter: false}}
	});

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



<h3>Class Rooms</h3>

<table id="maintable" cellspacing="0" class="tablesorter" width="400px">


<thead>
<tr><th  id="tooltipField" class="ou">ID</th><th>Facility</th>
<th>Room Number</th><th>Capacity</th>
</tr>
</thead>


<tbody>
<!-- BEGIN ROOM -->
<tr>
	<td class="ou" onclick="showRoomDetails({ROOMID})" onmouseover="this.style.cursor='pointer'">{ID}</td>
<td>{FACILITY}</td>
	<td align="right">{ROOMNUMNBER}</td>
	<td align="right">{CAPACITY}</td>
</tr>
<!-- END ROOM -->
</tbody>
</table>
