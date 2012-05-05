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

function AddTeacher(classId) {
  var formDlg = dijit.byId("formAddTeacher");
  a = dijit.byId("classId");
  a.set("value", classId);
  formDlg.show();
}
    </script>

	<form method="post" action="/dakhila/php/dataViewer2.php?command=Room" style="display:none" id="RoomForm"
	dojoType="dijit.form.Form"
	>
	Room ID: <input type="text" dojoType="dijit.form.TextBox" name="ID" id="what"> 
	<input type="submit" name="go" value="GO"><br>
	</form>

<div dojoType="dijit.Dialog" id="formAddTeacher" title="Add Teacher for a class="display: none">
    <form dojoType="dijit.form.Form" id="AddForm" name="doineedit">
        <script type="dojo/event" event="onSubmit" args="e">
            dojo.stopEvent(e); // prevent the default submit
            if (!this.isValid()) {
                window.alert('Please fix fields');
                return;
            }

	a = dijit.byId("classId");
	classId=a.value;
	r = dijit.byId("response");
	r.set("value"," Form being sent...");
	    
	    qObject = new Object();
	    qObject.studentId = studentId;
             var xhrArgs ={
                  url: '/dakhila/php/dataserver.php?command=ChangeClass',
		  postData:queryString,
                load: function(data, ioArgs) {
		    r.set("value","Success ..." + data);		    
                },
                error: function(error, ioArgs) {
                    //We'll 404 in the demo, but that's okay.  We don't have a 'postIt' service on the
                    //docs server.
		    r.set("value","Failed... " + error);		    
                }
            };
            var deferred = dojo.xhrPost(xhrArgs);

        </script>
        <div class="dijitDialogPaneContentArea">
	<label>email:</label> <div dojoType="dijit.form.ValidationTextBox" id="classId" disabled="disabled" style="width:50px" >nothing</div>
	</div>
        <div class="dijitDialogPaneContentArea">
            <label for='email'>Email:</label>
            <div dojoType="dijit.form.ValidationTextBox" id="email" required="true"> </div>
        </div>
        <div class="dijitDialogPaneActionBar">
            <button dojoType="dijit.form.Button" type="submit">
                Change
            </button>
            <button dojoType="dijit.form.Button" type="button" onClick="dijit.byId('formDialog2').hide();">
                GO Back
            </button>
        <div class="dijitDialogPaneContentArea">
	<div dojoType="dijit.form.ValidationTextBox" id="response" disabled="disabled">nothing</div>
	</div>
        </div>
    </form>
</div>


<h3>Available classes</h3>
<table id="maintable" cellspacing="0" class="tablesorter" width="600px">


<thead>
<tr><th>Department</th>
<th>Short</th>
<th>Room</th>
<th>Count</th>
<th>Capacity</th>
<th>Free</th>
<th>Teachers</th>
</tr>
</thead>


<tbody>
<!-- BEGIN CLASS -->
<tr>
	<td>{DEPARTMENT}</td>
	<td align="right">{SHORT}</td>
	<td class="ou" onclick="showRoomDetails({ROOMID})" onmouseover="this.style.cursor='pointer'" align="right">{ROOM}</td>
	<td align="right">{COUNT}</td>
	<td align="right">{CAPACITY}</td>
	<td align="right">{FREE}</td>
	<td>{TEACHERS}</td>
</tr>
<!-- END CLASS -->
</tbody>
</table>
