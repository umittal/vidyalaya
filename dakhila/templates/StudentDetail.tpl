        <style type="text/css">
            td { padding-left:10px; }
            .ou { font-style:oblique;text-decoration:underline; }
        </style>
    <script>
         dojo.require("dijit.form.TextBox");    
dojo.require("dijit.form.Form");
dojo.require("dijit.Tooltip");
    dojo.require("dijit.form.Button");
    dojo.require("dijit.Dialog");
    dojo.require("dijit.form.ValidationTextBox");

    function changeClass(name, classId, session, short, studentId) {
        var formDlg = dijit.byId("formDialog2");
	a = dijit.byId("chgSession");
	a.set("value", session);
	a = dijit.byId("chgName");
	a.set("value", name);
	a = dijit.byId("chgCurrent");
	a.set("value", short);
	a = dijit.byId("chgClass");
	a.set("value", classId);
	a = dijit.byId("chgStudent");
	a.set("value", studentId);
	r = dijit.byId("response");
	r.set("value","");		    
	formDlg.show();
    }

			// When the DOM and reources are ready....
			dojo.ready(function(){
				// Add tooltip of his picture
				new dijit.Tooltip({
					connectId: ["tooltipField"],
					label: "Click here to see details of Family"
				});
			});

	function showFamilyDetails(familyId) {
	var form=dijit.byId("familyForm");
	var idfield=dijit.byId("familyId");
	idfield.attr("value", familyId);
	if (form) {form.submit();} else {alert ("form not found");}
      }
    </script>

	<form method="post" action="/dakhila/php/dataViewer2.php?command=Family" style="display:none" id="familyForm"
	dojoType="dijit.form.Form"
	>
	Family ID: <input type="text" dojoType="dijit.form.TextBox" name="ID" id="familyId"> 
	<input type="submit" name="go" value="GO"><br>
	</form>


<div dojoType="dijit.Dialog" id="formDialog2" title="Change Enrollment of a Student" style="display: none">
    <form dojoType="dijit.form.Form" id="ChgForm" name="doineedit">
        <script type="dojo/event" event="onSubmit" args="e">
            dojo.stopEvent(e); // prevent the default submit
            if (!this.isValid()) {
                window.alert('Please fix fields');
                return;
            }

	a = dijit.byId("chgClass");
	classId=a.value;
	a = dijit.byId("chgStudent");
	studentId=a.value;
	a = dijit.byId("newClass");
	newClass=a.value;


	r = dijit.byId("response");
	r.set("value"," Form being sent...");
	    
	    qObject = new Object();
	    qObject.studentId = studentId;
	    qObject.currentClass = classId;
	    qObject.newClass = newClass;
	    qObject.owner = qObject;
	    var queryString = dojo.objectToQuery(qObject);
	    
             var xhrArgs ={
                  url: '/dakhila/php/dataserver.php?command=ChangeClass',
//                form: dojo.byId("ChgForm"),
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
	<label>Session:</label> <div dojoType="dijit.form.ValidationTextBox" id="chgSession" disabled="disabled" style="width:50px" >nothing</div>
	<label>Current:</label>    <div dojoType="dijit.form.ValidationTextBox" id="chgCurrent" disabled="disabled" style="width:50px">nothing</div>
	</div>
        <div class="dijitDialogPaneContentArea">
        <label>Name:</label>   <div dojoType="dijit.form.ValidationTextBox" id="chgName" disabled="disabled">nothing</div>
	</div>
        <div class="dijitDialogPaneContentArea" style="display:none">
	<div dojoType="dijit.form.ValidationTextBox" id="chgClass" disabled="disabled">nothing</div>
	<div dojoType="dijit.form.ValidationTextBox" id="chgStudent" disabled="disabled">nothing</div>
        </div>


        <div class="dijitDialogPaneContentArea">
            <label for='newClass'>
                New Class:
            </label>
            <div dojoType="dijit.form.ValidationTextBox" id="newClass" required="true">
            </div>
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


<h3>Student Details</h3>
<ol>

<li class="section">Student</li>

<table>
<!-- BEGIN STUDENT -->

<tr><td>ID</td><td>{ID}</td></tr>
<tr><td>Name</td><td>{NAME}</td></tr>
<tr><td>Date of Birth</td><td>{DOB}</td></tr>
<tr><td rowspan=2>Address</td><td>{LINE1} {LINE2}</td></tr>
<tr><td>{CITY}, {STATE} {ZIP}</td></tr>
<tr><td>Phone</td><td>{HOMEPHONE}</td></tr>
<tr><td>Cell</td><td>{CELL}</td></tr>
<tr><td>Email</td><td>{EMAIL}</td></tr>
<!-- END STUDENT -->
</table>


<li class="section">Parents</li>


<table>  	  	  
 <thead><tr><th>M/F<th>NAME<th>EMAIL<th>WORK<th>CELL<th>Call</tr></thead>
<!-- BEGIN FATHER -->
<tr><td>Father </td><td>{NAME}</td><td> {EMAIL}</td><td>{WORK}</td><td>{CELL}</td><td>{ISCONTACTABLE}</td></tr>
<!-- END FATHER -->  	  	  
<!-- BEGIN MOTHER -->
<tr><td>Mother </td><td id="tooltipField" class="ou" onclick="showFamilyDetails({FAMILYID})" onmouseover="this.style.cursor='pointer'">{NAME}</td><td> {EMAIL}</td><td>{WORK}</td><td>{CELL}</td><td>{ISCONTACTABLE}</td></tr>
<!-- END MOTHER -->  	  	  
</table>
<p>


<li class="section">Class Assignment</li>
<table>
<thead>
<tr><th>Session</th><th>Language</th><th>Culture</th></tr>
</thead>
<tbody>
<!-- BEGIN ENROLLMENT -->
<tr><td>{SESSION}</td><td>{LANGUAGE}</td><td>{CULTURE}</td></tr>
<!-- END ENROLLMENT -->
<!-- BEGIN KINDERGARTEN -->
<tr><td>{SESSION}</td><td colspan=2>{KG}</td></tr>
<!-- END KINDERGARTEN -->
</tbody>
</table>

</ol>

