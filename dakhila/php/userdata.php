<?php
$rootDir = $_SERVER["DOCUMENT_ROOT"] . "/dakhila";
require_once "HTML/Template/ITX.php";
require_once "$rootDir/libVidyalaya/db.inc";
require_once "$rootDir/libVidyalaya/vidyalaya.inc";
require_once "$rootDir/libVidyalaya/HtmlFactory.inc";
require_once "$rootDir/libVidyalaya/reports.inc";



// register is special since it is not authenticated
$command=isset($_GET["command"]) ? $_GET["command"] : "register";
if (empty($command)) $command="register";
$userdata = new UserData("../templates");
switch ($command) {
case "register":
  $userdata->register();
  break;
default:
  $userdata->$command();

}


class UserData {
  private $family=null;
  private $template = null;
  private $thispage = null;
  private $ip=null;
  
  public function home() {
    VidSession::sessionAuthenticate();
    $this->SetMenu();
    $email = $_SESSION["loginUsername"];
    $person = Person::PersonFromEmail($email); 
    if (!is_null($person)) 
      DisplayPerson($this->template, $person);
    print $this->template->get();
    break;		
  }

  private function PersonFromCode($code) {
    if (is_null($code)) return null;
    $reset = ResetCode::ObjectFromCodeIp($code, $this->ip);
    if (is_null($reset)) return null;
    $person = Person::PersonFromId($reset->MFS, $reset->mfsId);
    if (is_null($person)) return null;
    return $reset;
  }

  // ************************************************************
  public function resetpassword() {
    $code=isset($_GET["code"]) ? $_GET["code"] : "";
    $this->template->setCurrentBlock('RESULT');
    $reset=self::PersonFromCode($code);
    $person = Person::PersonFromId($reset->MFS, $reset->mfsId);
    $new = isset($_POST['password']) ?  $_POST['password'] : null;

    if (is_null($person)) {
      $html = "This code is not valid, please request a new code at <a href='$this->thispage?command=register'>here</a>\n";
    } elseif (!is_null($new)) {
      $change = 
      $html = ResetCode::SetPassword($reset, $this->ip, $new) ?
	"<p>go to <a href='/dakhila/php/dataViewer2.php?command=login'>login</a> screen now\n" :
	"<p>password not set, contact info@vidyalaya.us";
    } else  {
      $email = $reset->email;
      $code = $reset->code;

      $html = <<<RESETFORM
      <style type="text/css">
       @import "/dakhila/css/form.css"; 
       </style>
    <script>
      dojo.require("dojo.parser");  
       dojo.require("dijit.form.Button"); 
dojo.require("dojox.form.PasswordValidator");
dojo.require("dijit.form.Form");
      dojo.require("dijit.form.ValidationTextBox");    
    </script>

<div style="position:absolute; top:50%; left:25%; right:25%; overflow:auto; text-align:left">
<div class="formContainer">
      <form method="POST" action="$this->thispage?command=resetpassword&code=$code" dojoType="dijit.form.Form" >
    <div class="formTitle">Dakhila: Set New Password</div>

      <div class="formRow">
      <label for="email">Email Address: $email</label>
   </div>
			   <br />
<div dojoType="dojox.form.PasswordValidator" name="password">
    <label>
        Password:
      <input type="password" pwType="new" name="newp" id="newp" />
    </label>
    <br>
    <label>
        Validate:
        <input type="password" pwType="verify" />
    </label>
    <br>
</div>
      <div class="formRow">
	      <button dojoType="dijit.form.Button" type="submit" >
	Set Password
	      </button>
</div>

</form>
</div>

</div>

RESETFORM;
    }
    $this->template->setVariable("RESULT", $html);
    $this->template->parseCurrentBlock();
    print $this->template->get();
    
  }

  // ************************************************************
  public function register() {
    $this->template->setCurrentBlock('RESULT');
    $email = isset($_POST['email']) ?  $_POST['email'] : null;
    if ($email == "") {
      $html = file_get_contents("../html/register.inc");
    } else {
      $mfs = Emails::GetMFS($email); $mfsId=Emails::Getmfsid($email);
      $html = "<p>Request received from $this->ip </p>\n";
      if ($mfs == 0 || $mfsId == 0) {
	$html .= "<p>Sorry, we do not know $email, please contact info@vidyalaya.us. email address and IP address is being recorded to prevent abuse";
      } else {
	$person=Person::PersonFromEmail($email);
	$code = ResetCode::InsertCode($person, $this->ip, $email);
	$url="$this->thispage?command=resetpassword&code=$code";
	Mail::mailResetPasswordCode($email, $person, $url, $this->ip, 0);
	$html .= "Please see email sent to $email and follow instructions to reset the password.\n";
	$html .= "Click <a href='/dakhila/php/dataViewer2.php?command=login'>login</a> to login\n";
      }
    }
    $this->template->setVariable("RESULT", $html);
    $this->template->parseCurrentBlock();
    print $this->template->get();
  }


  private function SetMenu() {
    $dojomenu = file_get_contents("../html/usermenu.inc");

    $this->template->setCurrentBlock('MENU');
    $this->template->setVariable('MENU', $dojomenu);
    $this->template->parseCurrentBlock();

    $this->template->addBlockFile('BOTTOM', 'F_BOTTOM', 'LayoutBottom.tpl');
    $this->template->touchBlock('F_BOTTOM');
	
    $username=$_SESSION["loginUsername"];
    $dbserver=$_SESSION["dbserver"];
    $count=$_SESSION['count'];

    $this->template->setCurrentBlock('FOOTER');
    $this->template->setVariable("FOOTER", "Copyright (c) 2011 Vidyalaya Inc., ($username,$dbserver, $count )");
    $this->template->parseCurrentBlock();
  }

  private function Layout() {
    $this->template->loadTemplatefile("Layout.tpl", true, true);

    $this->template->addBlockFile('TOP', 'F_TOP', 'LayoutTop2.tpl');
    $this->template->touchBlock('F_TOP');

    $this->template->addBlockFile('CONTENT', 'F_CONTENT', 'LayoutContent.tpl');
    $this->template->touchBlock('F_CONTENT');
	
    $this->template->setCurrentBlock('HEADER');
    $this->template->setVariable("HEADER", '<a href=""><img src="http://www.vidyalaya.us/modx/assets/templates/vidyalaya/images/Vheader2.jpg"
		width="700" height="70" 
		alt="php5 logo"/></a>');
    $this->template->parseCurrentBlock();
  }

  public function __construct($templateDir) {
    $this->template = new HTML_Template_ITX($templateDir);
    $this->thispage = "http://" . $_SERVER['SERVER_NAME'] . "/" . $_SERVER['PHP_SELF'];
    $this->ip = $_SERVER["REMOTE_ADDR"];
    $this->Layout();
  }
}