<script>

function addTeacher(classId1, short1) {
  var formDlg = dijit.byId("formAddTeacher");
  a = dijit.byId("classId");
  a.set("value", classId1);
  a = dijit.byId("short");
  a.set("value", short1);
  formDlg.show();
}
</script>

<div dojoType="dijit.Dialog" id="formAddTeacher" title="Add Teacher for a class" style="display: none">
    <form dojoType="dijit.form.Form" id="AddForm" name="doineedit">
        <script type="dojo/event" event="onSubmit" args="e">
            dojo.stopEvent(e); // prevent the default submit
            if (!this.isValid()) {
                window.alert('Please fix fields');
                return;
            }

	r = dijit.byId("response");
	r.set("value"," Form being sent...");
	    
	    qObject = new Object();
	    qObject.classId = dijit.byId("classId").value;
	    qObject.email = dijit.byId("emailteacher").value;
	    var queryString = dojo.objectToQuery(qObject);

             var xhrArgs ={
                  url: '/dakhila/php/datainsert.php?command=AddTeacher',
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
	<div dojoType="dijit.form.ValidationTextBox" id="classId" disabled="disabled" style="width:50px; display:none;" >nothing</div>
	<label for='short'>Class:</label> <div dojoType="dijit.form.ValidationTextBox" id="short" disabled="disabled" style="width:50px" >nothing</div>
	</div>

        <div class="dijitDialogPaneContentArea">
            <label for='email'>Email:</label>
	    <input type="text" size="20" name="emailteacher" id="emailteacher" dojoType="dijit.form.ValidationTextBox" required="true" validator="dojox.validate.isEmailAddress"
		   promptMessage="Enter Teacher's  email address." invalidMessage="Invalid Email Address." trim="true"   />

        </div>

        <div class="dijitDialogPaneActionBar">
            <button dojoType="dijit.form.Button" type="submit">
                Add
            </button>
            <button dojoType="dijit.form.Button" type="button" onClick="dijit.byId('formAddTeacher').hide();">
                GO Back
            </button>
	    <div class="dijitDialogPaneContentArea">
	    <div dojoType="dijit.form.ValidationTextBox" id="response" disabled="disabled">nothing</div>
	    </div>
        </div>
    </form>
</div>

