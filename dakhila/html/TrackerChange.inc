    <form dojoType="dijit.form.Form" id="ChgForm" name="doineedit">
        <script type="dojo/event" event="onSubmit" args="e">
            dojo.stopEvent(e); // prevent the default submit
            if (!this.isValid()) {
                window.alert('Please fix fields');
                return;
            }

	    qObject = new Object();
	    qObject.familyId =dijit.byId("familyId").value;
	    qObject.status =dijit.byId("status").value;
	    qObject.tuition =dijit.byId("tuition").value;
	    var queryString = dojo.objectToQuery(qObject);
	    
             var xhrArgs ={
                  url: '/dakhila/php/dataserver.php?command=TrackerChange',
		  postData:queryString,
                load: function(data, ioArgs) {
		    window.alert("Success ..." + data);		    
                },
                error: function(error, ioArgs) {
                    //We'll 404 in the demo, but that's okay.  We don't have a 'postIt' service on the
                    //docs server.
		    window.alert("Failed... " + error);		    
                }
            };
            var deferred = dojo.xhrPost(xhrArgs);

        </script>
        <div class="dijitDialogPaneContentArea">
	<label for="status">Status:</label> <select name="status" dojoType="dijit.form.FilteringSelect" id="status">
	<option value="1">Registered</option>
	<option value="2">Waitlist</option>
	<option value="3">Pending</option>
	<option value="4">Moved Out</option>
	<option value="5">Not Interested</option>
	<option value="6">Next Year</option>
	<option value="7">Too Expensive</option>
	<option value="8">Schedule Conflict</option>
	<option value="9">All Graduating</option>
	<option value="10">To be invited</option>
	<option value="11">Cancelled</option>
    </select>


	<label for="tuition">Tuition:</label>    <div dojoType="dijit.form.ValidationTextBox" id="tuition"  style="width:50px" value=0>0</div>
        </div>
	<div dojoType="dijit.form.ValidationTextBox" id="familyId"  style="width:50px;display:none;" value="FAMILYID" >nothing</div>
        <div class="dijitDialogPaneActionBar">
            <button dojoType="dijit.form.Button" type="submit">
                Change
            </button>
	</div>
    </form>
