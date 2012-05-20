<script>
         dojo.require("dijit.form.TextBox");    
dojo.require("dijit.form.Form");
dojo.require("dijit.Tooltip");
    dojo.require("dijit.form.Button");
    dojo.require("dijit.Dialog");
    dojo.require("dijit.form.ValidationTextBox");

    function editContact(phone, name, addr1, addr2, zip, email, url) {
        var formDlg = dijit.byId("editContactForm");
	a = dijit.byId("editphone");
	a.set("value", phone);
	a = dijit.byId("editname");
	a.set("value", name);
	a = dijit.byId("editaddr1");
	a.set("value", addr1);
	a = dijit.byId("editaddr2");
	a.set("value", addr2);
	a = dijit.byId("editzip");
	a.set("value", zip);
	a = dijit.byId("editemail");
	a.set("value", email);
	a = dijit.byId("editurl");
	a.set("value", url);
	formDlg.show();
    }

</script>

<div dojoType="dijit.Dialog" id="editContactForm" title="Edit Contacts" style="display:none">
  <form dojoType="dijit.form.Form" id="edtContact" name="doineedit2">
    <script type="dojo/event" event="onSubmit" args="e">
      dojo.stopEvent(e); // prevent the default submit
      if (!this.isValid()) {
      window.alert('Please fix fields');
      return;
      }

      r = dijit.byId("response2");
      r.set("value"," Form being sent...");
      
      qObject = new Object();
      qObject.phone = dijit.byId("editphone").value;
      qObject.name = dijit.byId("editname").value;
      qObject.addr1 = dijit.byId("editaddr1").value;
      qObject.addr2 = dijit.byId("editaddr2").value;
      qObject.zip = dijit.byId("editzip").value;
      qObject.email = dijit.byId("editemail").value;
      qObject.url = dijit.byId("editurl").value;
      var queryString = dojo.objectToQuery(qObject);
      
      var xhrArgs ={
      url: '/dakhila/php/dataserver.php?command=UpdateContactDetail',
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
      <label>phone:</label> 
      <div dojoType="dijit.form.ValidationTextBox" id="editphone" disabled="disabled" style="width:120px" >nothing</div>
    </div>
    <div class="dijitDialogPaneContentArea">
      <table>
	<tr><td>Name</td><td><input data-dojo-type="dijit.form.TextBox" id="editname" name="name" /></td></tr>
	<tr><td>Addr 1</td><td><input data-dojo-type="dijit.form.TextBox" id="editaddr1" name="addr1" /></td></tr>
	<tr><td>Addr 2</td><td><input data-dojo-type="dijit.form.TextBox" id="editaddr2" name="addr2" /></td></tr>
	<tr><td>Zip</td><td><input type="text" name="zip" size="5" id="editzip"
				   dojoType="dijit.form.ValidationTextBox"
				   maxLength="5"
				   promptMessage="Enter zip code"
				   style="width: 50px;" 
				   regExp="^\d{5}$"
				   invalidmessage="Invalid Zip, Format: xxxxx"
				   trim="true"
				   /></td></tr>
	<tr><td>email</td><td>
	    <input type="text" size="20" name="email" id="editemail" 
		   dojoType="dijit.form.ValidationTextBox" 
		   required="false"
		   validator="dojox.validate.isEmailAddress"
		   promptMessage="Enter contact  email address."
		   invalidMessage="Invalid Email Address." 
		   trim="true"
		   /></td></tr>
	<tr><td>url</td><td>
	    <input type="text" size="80" name="url" id="editurl" 
		   dojoType="dijit.form.ValidationTextBox" 
		   validator="dojox.validate.isUrl"
		   required="false"
		   promptMessage="Enter contact url"
		   invalidMessage="Invalid url." 
		   trim="true"
		   /></td></tr>

      </table>
    </div> 


    <div class="dijitDialogPaneContentArea">
      <button dojoType="dijit.form.Button" type="submit">
        Change
      </button>
      <button dojoType="dijit.form.Button" type="button" onClick="dijit.byId('editContactForm').hide();">
        GO Back
      </button>

      <div dojoType="dijit.form.ValidationTextBox" id="response2" disabled="disabled">nothing</div>
    </div>

  </form>
</div>

