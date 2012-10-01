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

<form  dojoType="dijit.form.Form" id="queryStudent" name="queryStudent" method="post" 
       action="==URL==">
<div class="formTitle">Student Lookup</div>
<table >
  <tr>
    <td style="padding-right:20px;">
    ID: <input data-dojo-type="dijit.form.ValidationTextBox" id="studentId" name="studentId" 
    size="4"
    maxLength="4"
    style="width: 40px;" 
    regExp="^\d{4}$"
    invalidmessage="Four Digit number expected for student id"

    value="0000" /></td>
    </td>
    <td style="padding-right:20px;">
    First: <input data-dojo-type="dijit.form.TextBox" id="First" name="First" value="==FIRST=="  /></td>
    </td>
    <td style="padding-right:20px;">
    Last: <input data-dojo-type="dijit.form.TextBox" id="Last" name="Last"  value="==LAST==" /></td>
    </td>
  </tr>
</table>


<button dojoType="dijit.form.Button" type="submit" >Lookup</button>
<button dojoType="dijit.form.Button" type="reset" >Reset</button>

</form>
</div>
