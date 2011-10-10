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
      <form method="POST" action="/dakhila/php/userdata.php?command=register" dojoType="dijit.form.Form" >
    <div class="formTitle">Dakhila: New User Registration</div>

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
<p>I confirm

<ol>
<li>I am a participant in Vidyalaya for the current session
<li>I have read the Participation Agreement and submitted a signed copy with Vidyalaya
<li>I intend to use the systerm for the role assigned to me and will not use it for any unauthorized activity
</ol>

<p>We will validate the information and send you an email with a temporary password that you will need to change on first login.

	</div>
	      <button dojoType="dijit.form.Button" type="submit" >
	Register
	      </button>

</form>
</div>
	      

      <p><i>The access to this system is restricted to authorized users. If you are not an authorized user, please exit immediately. 
      To request a login to this system, please send an email to info@vidyalaya.us</i>
</div>
