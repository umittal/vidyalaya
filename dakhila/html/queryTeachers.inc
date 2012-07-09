    <script>
      dojo.require("dijit.form.ComboBox");    
      dojo.require("dijit.form.Form");    
      dojo.require("dijit.form.ValidationTextBox");    
    </script>

<div class="formContainer">
	<form method="post" action="==URL=="
	dojoType = "dijit.form.Form">

	<div class="formRow"> 
	Year: <input type="text" name="year" id="year"  size=6
           dojoType="dijit.form.ValidationTextBox" 
           required="true"  
	regExp="\b201\d\b"
           promptMessage="Enter School Start Year."
           invalidMessage="Invalid School Start Year." 
	value="==CURRENTYEAR=="
	trim="true"> 
   <input type="submit" name="submit" value="GO"><br>
    </div>




</form>
</div>
