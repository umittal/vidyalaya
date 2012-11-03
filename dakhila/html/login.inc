      <style type="text/css">
       @import "/dakhila/css/form.css"; 
       </style>
    <script>
      dojo.require("dojo.parser");  
       dojo.require("dijit.form.Button"); 
dojo.require("dijit.form.Form");
      dojo.require("dijit.form.ValidationTextBox");    
    </script>

<div style="position:absolute; top:50%; left:25%; right:25%; overflow:auto; text-align:left">
<div class="formContainer">
      <form method="POST" action="/dakhila/logincheck.php" dojoType="dijit.form.Form" >
    <div class="formTitle">Dakhila Portal Login</div>

      <div class="formRow">
      <label for="email">Email Address:</label>
<input type="text" size="35" name="email" id="email" 
           dojoType="dijit.form.ValidationTextBox" 
           required="true"  
	regExp="\b[a-zA-Z0-9._%-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}\b"
           promptMessage="Enter email address."
           invalidMessage="Invalid Email Address." 
           trim="true"
/>
	</div>

      <div class="formRow">
      <label for="password">Password:</label>
       <input type="password" size="10" name="password" id="password" 
           dojoType="dijit.form.ValidationTextBox" 
           required="true"  
           promptMessage="Enter password."
           trim="true"

/>
	</div>
	      <button dojoType="dijit.form.Button" type="submit" >
	Login
	      </button>

<p>
<a href="/dakhila/php/userdata.php?command=register">New User Registration</a>
<a href="/shiksha/dakhila.html">Help</a>

</form>
</div>
	      

      <p><i>The access to this system is restricted to authorized users. If you are not an authorized user, please exit immediately. 
      To request a login to this system, please send an email to info@vidyalaya.us</i>
</div>
