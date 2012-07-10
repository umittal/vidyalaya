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

<div class="formContainer"  style="width:500px">

<form  dojoType="dijit.form.Form" id="newFamily" name="addChild">


<script type="dojo/event" event="onSubmit" args="e">
    
    dojo.stopEvent(e); // prevent the default submit

if (!this.isValid()) {
  window.alert('Please fix fields');
  return;
}


qObject = new Object();

a = dijit.byId("Family");
b=a.value;
qObject.Family =b;

a = dijit.byId("First");
b=a.value;
qObject.First =b;


a = dijit.byId("Last");
b=a.value;
qObject.Last =b;

a = dijit.byId("Email");
b=a.value;
qObject.Email =b;

a = dijit.byId("Cell");
b=a.value;
qObject.Cell =b;

a = dijit.byId("gender");
b=a.value;
qObject.gender =b;

a = dijit.byId("language");
b=a.value;
qObject.language =b;

qObject.yearFirst =dijit.byId("yearFirst").value;

qObject.dob=dojo.date.locale.format(dijit.byId('dob').value, 
{datePattern: "yyyy-MM-dd", selector: "date"})


var queryString = dojo.objectToQuery(qObject);


r = dijit.byId("response");
r.set("value"," Form being sent...");
var xhrArgs ={
url: '/dakhila/php/datainsert.php?command=InsertChild',
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


<div class="formTitle">Add a Child to Family ==FAMILYID==</div>


  <input data-dojo-type="dijit.form.TextBox" id="Family" name="Family" style="display:none;" value="==FAMILYID==" />

<table >
	
	<tr>
	  <td>First Name</td>
	  <td><input data-dojo-type="dijit.form.TextBox" id="First" name="First" /></td>
	</tr>
	
	<tr>
	  <td>Last Name</td>
	  <td><input data-dojo-type="dijit.form.TextBox" id="Last" name="Last" /></td>
	</tr>
	
	<tr>
	  <td><label>Email Address:</label></td>
	  <td>
	    <input type="text" size="20" name="Email" id="Email" 
 		   dojoType="dijit.form.ValidationTextBox" 
  value="a@b.c"
  required="false"
    validator="dojox.validate.isEmailAddress"
		   promptMessage="Enter Child  email address."
		   invalidMessage="Invalid Email Address." 
		   trim="true"
		   />
	  </td>

	</tr>
	
	<tr>
	  <td>Cell Phone</td>
	  <td>
    <input type="text" id="Cell" name="Cell"
    dojoType="dijit.form.ValidationTextBox"
    promptMessage="Enter Child Cell Phone(xxx-xxx-xxxx)." 
    size="12"
    maxLength="12"
    style="width: 120px;" 
    regExp="^[0-9]\d{2}-\d{3}-\d{4}$"
    invalidmessage="Telephone number format: xxx-xxx-xxxx"
    />

</td>
	</tr>

  <tr><td>Gender</td>
  <td>
  <select name="gender" dojoType="dijit.form.FilteringSelect" id="gender">
    <option value="2">Boy</option>
    <option value="1" selected="selected">Girl</option>
    </select>
</td>
  </tr>
  <tr><td>Language</td>
  <td>
  <select name="language" dojoType="dijit.form.FilteringSelect" id="language">
    <option value="1">Hindi</option>
    <option value="2" selected="selected">Gujarati</option>
    <option value="3" selected="selected">Telugu</option>
    </select>
</td>
  </tr>
  <tr><td>Date of Birth</td>
  <td><input type="text" name="dob" id="dob" value=""
    data-dojo-type="dijit.form.DateTextBox"
    required="true" />
  </td>
  </tr>
  <tr><td>Year of First Grade</td>
    <td>
    <input type="text" id="yearFirst" name="yearFirst"
    dojoType="dijit.form.ValidationTextBox"
    promptMessage="Year when Child goes to First Grade(xxxx)." 
    size="4"
    maxLength="4"
    style="width: 40px;" 
    regExp="^\d{4}$"
    invalidmessage="Use 4 digts for year"
    />
    
    </td>
  </tr>

</table>

<button dojoType="dijit.form.Button" type="submit" >Register</button>
<button dojoType="dijit.form.Button" type="reset" >Reset</button>

        <div class="dijitDialogPaneContentArea">
	<div dojoType="dijit.form.ValidationTextBox" id="response" disabled="disabled">nothing</div>
	</div>

</form>
</div>

