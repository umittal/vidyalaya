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

<form  dojoType="dijit.form.Form" id="newFamily" name="newFamily">
    <script type="dojo/event" event="onSubmit" args="e">
    dojo.stopEvent(e); // prevent the default submit
if (!this.isValid()) {
  window.alert('Please fix fields');
  return;
}


qObject = new Object();

a = dijit.byId("homePhone");
b=a.value;
qObject.homePhone =b;

a = dijit.byId("addr1");
b=a.value;
qObject.addr1 =b;

a = dijit.byId("city");
b=a.value;
qObject.city =b;

a = dijit.byId("state");
b=a.value;
qObject.state =b;

a = dijit.byId("zip");
b=a.value;
qObject.zip =b;

a = dijit.byId("mFirst");
b=a.value;
qObject.mFirst =b;

a = dijit.byId("fFirst");
b=a.value;
qObject.fFirst =b;

a = dijit.byId("mLast");
b=a.value;
qObject.mLast =b;

a = dijit.byId("fLast");
b=a.value;
qObject.fLast =b;


a = dijit.byId("mEmail");
b=a.value;
qObject.mEmail =b;
a = dijit.byId("fEmail");
b=a.value;
qObject.fEmail =b;

a = dijit.byId("mWork");
b=a.value;
qObject.mWork =b;

a = dijit.byId("fWork");
b=a.value;
qObject.fWork =b;

a = dijit.byId("mCell");
b=a.value;
qObject.mCell =b;

qObject.fCell =dijit.byId("fCell").value;
qObject.priority=dojo.date.locale.format(dijit.byId('priority').value, 
{datePattern: "yyyy-MM-dd", selector: "date"})

var queryString = dojo.objectToQuery(qObject);


r = dijit.byId("response");
f = dijit.byId("familyId");
r.set("value"," Form being sent...");
var xhrArgs ={
url: '/dakhila/php/datainsert.php?command=InsertFamily',
postData:queryString,
load: function(data, ioArgs) {
    r.set("value","Success ..." + data + " Created");		    
    f.set("value", data);
    f2 = dijit.byId("ID");
    f2.set("value", data);
    b =     dijit.byId("addChildButton");
    b.set("disabled", false);
    b.disabled=false;
  },
error: function(error, ioArgs) {
    //We'll 404 in the demo, but that's okay.  We don't have a 'postIt' service on the
    //docs server.
    r.set("value","Failed... " + error);		    
  }
};

var deferred = dojo.xhrPost(xhrArgs);

</script>


<div class="formTitle">New Family Registration</div>

<table >
  <tr>
    <td style="padding-right:20px;">

      <table>
	<tr>
	  <td>Home Phone</td>
    <td>
    <input type="text" id="homePhone" name="homePhone"
    dojoType="dijit.form.ValidationTextBox"
    promptMessage="Enter Home Phone(xxx-xxx-xxxx)." 
    size="12"
    maxLength="12"
    style="width: 120px;" 
    regExp="^[0-9]\d{2}-\d{3}-\d{4}$"
    invalidmessage="Telephone number format: xxx-xxx-xxxx"
    />
    
    </td>
	</tr>
	
	<tr>
	  <td>Street</td>
	  <td><input  type="text" data-dojo-type="dijit.form.TextBox" id="addr1" name="addr1" /></td>
	</tr>
	
	<tr>
	  <td>City</td>
	  <td><input  data-dojo-type="dijit.form.TextBox" id="city" name="city" /></td>
	</tr>
	
	<tr>
    <td>State</td>
    <td>
  <select name="state" dojoType="dijit.form.FilteringSelect" id="state">
    <option value="NY">New York</option>
    <option value="NJ" selected="selected">New Jersey</option>
    <option value="PA">Pennsylvania</option>
    </select>
    </td>
	</tr>
	
	<tr>
	  <td><label for="zip">Zipcode</label></td>
	  <td>
<input type="text" name="zip" required="true" size="5" id="zip"
    dojoType="dijit.form.ValidationTextBox"
    maxLength="5"
    promptMessage="Enter zip code"
    style="width: 50px;" 
    regExp="^\d{5}$"
    invalidmessage="Invalid Zip, Format: xxxxx"
    trim="true"
 />
</td>
	</tr>

  <tr><td>Priority Date</td>
  <td><input type="text" name="priority" id="priority" value=""
    data-dojo-type="dijit.form.DateTextBox"
    required="true" />
  </td>
  </tr>

      </table>

    </td>
    <td>
      <table>
	<thead>
	  <tr>
	    <th></th>
	    <th>Mother</td>
	    <th>Father</th>
	  </tr>
	</thead>
	
	<tr>
	  <td>First Name</td>
	  <td><input data-dojo-type="dijit.form.TextBox" id="mFirst" name="mFirst" /></td>
	  <td><input data-dojo-type="dijit.form.TextBox" id="fFirst" name="fFirst" /></td>
	</tr>
	
	<tr>
	  <td>Last Name</td>
	  <td><input data-dojo-type="dijit.form.TextBox" id="mLast" name="mLast" /></td>
	  <td><input data-dojo-type="dijit.form.TextBox" id="fLast" name="fLast" /></td>
	</tr>
	
	<tr>
	  <td><label>Email Address:</label></td>
	  <td>
	    <input type="text" size="20" name="mEmail" id="mEmail" 
		   dojoType="dijit.form.ValidationTextBox" 
    validator="dojox.validate.isEmailAddress"
		   promptMessage="Enter mother  email address."
		   invalidMessage="Invalid Email Address." 
		   trim="true"
		   />
	  </td>
	  <td>
	    <input type="text" size="20" name="fEmail" id="fEmail" 
		   dojoType="dijit.form.ValidationTextBox" 
		   regExp="\b[a-zA-Z0-9._%-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}\b"
		   promptMessage="Enter father email address."
		   invalidMessage="Invalid Email Address." 
		   trim="true"
		   />
	  </td>
	</tr>
	
	<tr>
	  <td>Work Phone</td>
    <td>
    <input type="text" id="mWork" name="mWork"
    dojoType="dijit.form.ValidationTextBox"
    promptMessage="Enter Mother Work Phone(xxx-xxx-xxxx)." 
    size="12"
    maxLength="12"
    style="width: 120px;" 
    regExp="^[0-9]\d{2}-\d{3}-\d{4}$"
    invalidmessage="Telephone number format: xxx-xxx-xxxx"
    />
    
    </td>
    <td>
    <input type="text" id="fWork" name="fWork"
    dojoType="dijit.form.ValidationTextBox"
    promptMessage="Enter Father Work Phone(xxx-xxx-xxxx)." 
    size="12"
    maxLength="12"
    style="width: 120px;" 
    regExp="^[0-9]\d{2}-\d{3}-\d{4}$"
    invalidmessage="Telephone number format: xxx-xxx-xxxx"
    />
    </td>
	</tr>
	
	<tr>
	  <td>Cell Phone</td>
	  <td>
    <input type="text" id="mCell" name="mCell"
    dojoType="dijit.form.ValidationTextBox"
    promptMessage="Enter Mother Cell Phone(xxx-xxx-xxxx)." 
    size="12"
    maxLength="12"
    style="width: 120px;" 
    regExp="^[0-9]\d{2}-\d{3}-\d{4}$"
    invalidmessage="Telephone number format: xxx-xxx-xxxx"
    />

</td>
	  <td>
    <input type="text" id="fCell" name="fCell"
    dojoType="dijit.form.ValidationTextBox"
    promptMessage="Enter Father Cell Phone(xxx-xxx-xxxx)." 
    size="12"
    maxLength="12"
    style="width: 120px;" 
    regExp="^[0-9]\d{2}-\d{3}-\d{4}$"
    invalidmessage="Telephone number format: xxx-xxx-xxxx"
    />

</td>
	</tr>
	
      </table>

    </td>
  </tr>
</table>

<button dojoType="dijit.form.Button" type="submit" >Register</button>
<button dojoType="dijit.form.Button" type="reset" >Reset</button>

        <div class="dijitDialogPaneContentArea">
	<div dojoType="dijit.form.ValidationTextBox" id="response" disabled="disabled">nothing</div>
	</div>

</form>

<form target="_blank" dojoType="dijit.form.Form" method="post" action="/dakhila/php/userdata.php?command=addChild">
<input type="text" data-dojo-type="dijit.form.TextBox" style="display:none;"  name="familyId" id="familyId" /> 
<button dojoType="dijit.form.Button" type="submit" id="addChildButton"  disabled="true" >add child</button>
<button dojoType="dijit.form.Button" type="submit" id="waitlist"  disabled="true" >WaitList</button>
</form>

<form target="_blank" dojoType="dijit.form.Form" method="post" action="/dakhila/php/dataViewer2.php?command=Family">
<input type="text" data-dojo-type="dijit.form.TextBox" style="display:none;"  name="ID" id="ID" /> 
<button dojoType="dijit.form.Button" type="submit" id="showFamily"  >View Family</button>
</form>

</div>

