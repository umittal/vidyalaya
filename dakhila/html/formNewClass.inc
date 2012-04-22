      <style type="text/css">
       @import "/dakhila/css/form.css"; 
       </style>

<script>
  dojo.require("dojo.parser");  
  dojo.require("dijit.form.Button"); 
  dojo.require("dijit.form.Form");
  dojo.require("dijit.form.ValidationTextBox");    
  dojo.require("dijit.form.DateTextBox");
</script>

<div class="formContainer"  style="width:800px">

<form  dojoType="dijit.form.Form" id="newClass" name="newClass">
    <script type="dojo/event" event="onSubmit" args="e">
    dojo.stopEvent(e); // prevent the default submit
if (!this.isValid()) {
  window.alert('Please fix fields');
  return;
}


qObject = new Object();

qObject.course =dijit.byId("course").value;
qObject.section =dijit.byId("section").value;
qObject.startTime =dijit.byId("startTime").value;
qObject.endTime =dijit.byId("endTime").value;

var queryString = dojo.objectToQuery(qObject);

r = dijit.byId("response");
r.set("value"," Form being sent...");
var xhrArgs ={
url: '/dakhila/php/datainsert.php?command=InsertClass',
postData:queryString,
load: function(data, ioArgs) {
    r.set("value","Success ..." + data + " Created");		    
  },
error: function(error, ioArgs) {
    //We'll 404 in the demo, but that's okay.  We don't have a 'postIt' service on the
    //docs server.
    r.set("value","Failed... " + error);		    
  }
};

var deferred = dojo.xhrPost(xhrArgs);

</script>


<div class="formTitle">Create New Class</div>

<table >
  <tr>
  <td style="padding-right:20px;">

  <table>
  <tr>
  <td>Year</td>
  <td>2012-13</td>
  </tr>
	
  <tr>
  <td><label for="course">Course</label></td>
 <td><input  type="text" data-dojo-type="dijit.form.TextBox" id="course" name="course" /></td>
  </tr>
	
  <tr>
  <td><label for="section">Section</label></td>
  <td> <select name="section" dojoType="dijit.form.FilteringSelect" id="section">
       <option value=" " selected="selected"></option>
       <option value="A">A</option>
       <option value="B">B</option>
    </select>
  </td>
  </tr>
	
	
  <tr>
  <td><label for="startTime">Start Time</label></td>
  <td><input  data-dojo-type="dijit.form.TextBox" id="startTime" name="startTime" /></td>
  </tr>

  <tr>
  <td><label for="endTime">End Time</label></td>
  <td><input  data-dojo-type="dijit.form.TextBox" id="endTime" name="endTime" /></td>
  </tr>

</table>

<button dojoType="dijit.form.Button" type="submit" >Create</button>
<button dojoType="dijit.form.Button" type="reset" >Reset</button>

        <div class="dijitDialogPaneContentArea">
	<div dojoType="dijit.form.ValidationTextBox" id="response" disabled="disabled">nothing</div>
	</div>

</form>


</div>

