      <style type="text/css">
       @import "/dakhila/css/form.css"; 
       </style>

<script>
  dojo.require("dojo.parser");  
  dojo.require("dijit.form.Button"); 
  dojo.require("dijit.form.Form");
  dojo.require("dijit.form.ValidationTextBox");    
</script>

<div class="formContainer">

<form method="POST" action="/dakhila/php/userdata.php?command=formhelp" dojoType="dijit.form.Form">

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
	  <td><input  type="text" name="addr1" /></td>
	</tr>
	
	<tr>
	  <td>City</td>
	  <td><input  type="text" name="city" /></td>
	</tr>
	
	<tr>
    <td>State</td>
    <td>
    <select name="state" dojoType="dijit.form.FilteringSelect">
    <option value="NY">New York</option>
    <option value="NJ" selected="selected">New Jersey</option>
    <option value="PA">Pennsylvania</option>
    </select>
    </td>
	</tr>
	
	<tr>
	  <td><label for="zip">Zipcode</label></td>
	  <td>
<input type="text" name="zip" required="true" size="5"
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
	  <td><input  type="text" name="mFirst" /></td>
	  <td><input  type="text" name="fFirst" /></td>
	</tr>
	
	<tr>
	  <td>Last Name</td>
	  <td><input  type="text" name="mLast" /></td>
	  <td><input  type="text" name="fLast" /></td>
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
</form>
</div>

