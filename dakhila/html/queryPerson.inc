<style type="text/css">
 @import "/dakhila/css/form.css"; 
</style>
<script>
  dojo.require("dojo.parser");  
  dojo.require("dijit.form.Button"); 
  dojo.require("dijit.form.Form");
  dojo.require("dojox.validate");    
  dojo.require("dijit.form.ValidationTextBox");    

</script>


<div class="formContainer">
<form  dojoType="dijit.form.Form" id="queryPerson" name="queryPerson" method="post" 
       action="==URL==">
<div class="formTitle">Person Lookup</div>

<table >
  <tr>
    <td style="padding-right:20px;">Email</td>
    <td> <input type="text" size="35" name="email" id="email" ==EMAIL==
    required="false"
           dojoType="dijit.form.ValidationTextBox" 
	  placeholder="Enter email address"
           invalidMessage="Invalid Email Address" 
           trim="true"
/>
    </td>
</tr>

<tr>
    <td style="padding-right:20px;">First: </td>
    <td><input data-dojo-type="dijit.form.TextBox" id="First" name="First" value="==FIRST=="  /></td>
</tr>
<tr>
    <td style="padding-right:20px;">Last: </td>
    <td><input data-dojo-type="dijit.form.TextBox" id="Last" name="Last"  value="==LAST==" /></td>
  </tr>
</table>

<button dojoType="dijit.form.Button" type="submit" >Lookup</button>
<button dojoType="dijit.form.Button" type="reset" >Reset</button>


</form>
</div>
