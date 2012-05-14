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

<form  dojoType="dijit.form.Form" id="newRegistration" name="newRegistration" method="post" action="URL">


<div class="formTitle">New Registration</div>

<table>
<tr><td>
  <label for="registrationDate">Date:</label></td><td>

  <input type="text" name="registrationDate" id="registrationDate" value="now"
    data-dojo-type="dijit.form.DateTextBox"
    required="true" />
</td></tr>
<tr>
<td>Family</td>
<td><input  type="text" data-dojo-type="dijit.form.TextBox" id="familyId" name="familyId" size=4 /></td>
<td><input type="submit" name="update" value="Update"></td>
</tr>
<tr>
<td>Children</td>
<td><input  type="text" data-dojo-type="dijit.form.TextBox" id="child1" name="child1" size=4 /></td>
<td><input  type="text" data-dojo-type="dijit.form.TextBox" id="child2" name="child2" size=4 /></td>
<td><input  type="text" data-dojo-type="dijit.form.TextBox" id="child3" name="child3" size=4 /></td>
</tr>

<tr><td>Tuition</td><td>
<input  type="text" data-dojo-type="dijit.form.TextBox" id="tuition" name="tuition" />
</td><td>
Adjustment
</td><td>
<input  type="text" data-dojo-type="dijit.form.TextBox" id="adjustment" name="adjustment" />
</td></tr>

<tr><td>New Student</td><td>
<input  type="text" data-dojo-type="dijit.form.TextBox" id="newstudent" name="newstudent" />
</td></tr>

  <tr><td colspan=4>Optional Items</td></tr>
<tr><td>Book</td><td>
<input  type="text" data-dojo-type="dijit.form.TextBox" id="book" name="book" />
</td><td>
Bag
</td><td>
<input  type="text" data-dojo-type="dijit.form.TextBox" id="bag" name="bag" />
</td></tr>

<tr><td>CD</td><td>
<input  type="text" data-dojo-type="dijit.form.TextBox" id="cd" name="cd" />
</td><td>
DVD
</td><td>
<input  type="text" data-dojo-type="dijit.form.TextBox" id="dvd" name="dvd" />
</td></tr>

<tr><td>Check</td><td>
<input  type="text" data-dojo-type="dijit.form.TextBox" id="checknumber" name="checknumber" />
</td><td>
Amount
</td><td>
<input  type="text" data-dojo-type="dijit.form.TextBox" id="total" name="total" />
</td></tr>
</table>


<button dojoType="dijit.form.Button" type="submit" name="register" value="Register">Register</button>
<button dojoType="dijit.form.Button" type="reset" >Reset</button>
  <div class="dijitDialogPaneContentArea">
  <div dojoType="dijit.form.ValidationTextBox" id="response" disabled="disabled">nothing</div>
  </div>


</form>
</div>



