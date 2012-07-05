<?php
$rootDir = $_SERVER["DOCUMENT_ROOT"] . "/dakhila";
require_once "HTML/Template/ITX.php";
require_once "$rootDir/libVidyalaya/db.inc";
require_once "$rootDir/libVidyalaya/vidyalaya.inc";
require_once "$rootDir/libVidyalaya/HtmlFactory.inc";
require_once "$rootDir/libVidyalaya/reports.inc";

VidSession::startSession(); //let us start a session, not same as authenticating user
$command=isset($_GET["command"]) ? $_GET["command"] : "register";
if (empty($command)) $command="register";
$userdata = new UserData("../templates");
switch ($command) {
case "register":// register is special since it is not authenticated
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
    if (!is_null($person))  DisplayPerson($this->template, $person);
    print $this->template->get();
  }

  public function formhelp() {
    $this->SetMenu();
    $html = "";

    $html .= "<h3>Form Parameters are </h3>";
    $html .= "<ol>\n";
    foreach ($_POST as $key => $value) {
      $html .= "<li>$key</li>$value\n";
    }
    $html .= "</ol>\n";


    $html .= "<h3>Server Parameters are </h3>";
    $html .= "<ol>\n";
    foreach ($_SERVER as $key => $value) {
      $html .= "<li>$key</li>$value\n";
    }
    $html .= "</ol>\n";

    $this->template->setCurrentBlock('RESULT');
    $this->template->setVariable("RESULT", $html);
    $this->template->parseCurrentBlock();
    print $this->template->get();
  }

  public function editor() {
    VidSession::sessionAuthenticate();
    $this->SetMenu();
    $email = $_SESSION["loginUsername"];
    $person = Person::PersonFromEmail($email); 
    if (is_null($person)) {
      print "person is not defined, I did not want to be here\n";
      print $this->template->get();
      return;
    }

    $name = $person->fullName();
    $content = isset($_POST['content']) ?  $_POST['content'] : null;
    $classId = isset($_POST['classId']) ?  $_POST['classId'] : null;
    $role = isset($_POST['role']) ?  $_POST['role'] : null;
    $date = isset($_POST['date']) ?  $_POST['date'] : "2011-10-23";


    $class = AvailableClass::GetItemById($classId);
    if (is_null($class)) {
      $this->template->setCurrentBlock('RESULT');
      $this->template->setVariable("RESULT", "Sorry, you currently do not have the edit privileges yet");
      $this->template->parseCurrentBlock();
      print $this->template->get();
      return;
    }
    $short = is_null($class) ? "" : $class->short();

    if ( !empty($content) && !is_null($class))  {
      $content = VidDb::mysqlclean($content, 5000);
      Newsletter::Save($content, $class, $date, $person, $role);
    }

    if (empty($content)) {
      $newsletter = Newsletter::Get($class, $date, $role);
      $content = $newsletter->content;
      //      print "<p>found content: $content\n";
    }

    $html = <<<TINYMCE
<script type="text/javascript" src="/js/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript">
tinyMCE.init({
       // General options
        mode : "textareas",
        theme : "advanced",
        plugins : "autolink,lists,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

        // Theme options
        theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
        theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
        theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
        theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,blockquote,pagebreak,|,insertfile,insertimage",
        theme_advanced_toolbar_location : "top",
        theme_advanced_toolbar_align : "left",
        theme_advanced_statusbar_location : "bottom",
        theme_advanced_resizing : true,

        // Skin options
        skin : "o2k7",
        skin_variant : "silver",

        // Example content CSS (should be your site CSS)
      //  content_css : "css/example.css",

        // Drop lists for link/image/media/template dialogs
        template_external_list_url : "js/template_list.js",
        external_link_list_url : "js/link_list.js",
        external_image_list_url : "js/image_list.js",
        media_external_list_url : "js/media_list.js",

        // Replace values for the template plugin
        template_replace_values : {
                username : "Some User",
                staffid : "991234"
        }
});
</script>

<form method="post" action="$this->thispage?command=editor">
    Class: <input type="text" name="class" value="$short" readonly="readonly"/>
    Role: <input type="text" name="role" value="$role" readonly="readonly"/>
    <input type="text" name="classId" value="$classId" readonly="readonly">$classId</input>
    Date: <input type="text" name="date" value="$date" readonly="readonly"/>
        <p>     
                <textarea name="content" cols="80" rows="15">$content</textarea>
                <input type="submit" value="Save" />
        </p>
</form>
TINYMCE;
    $this->template->setCurrentBlock('RESULT');
    $this->template->setVariable("RESULT", $html);
    $this->template->parseCurrentBlock();
    print $this->template->get();
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

  public function newFamily() {

    $this->template->setCurrentBlock('RESULT');
    $html = file_get_contents("../html/formNewFamily.inc");
    $this->template->setVariable("RESULT", $html);
    $this->template->parseCurrentBlock();
    $this->template->addBlockFile('BOTTOM', 'F_BOTTOM', 'LayoutBottom.tpl');
    $this->template->touchBlock('F_BOTTOM');

    print $this->template->get();
  }

  // ************************************************************
  public function addChild() {
    VidSession::sessionAuthenticate();
    $this->SetMenu();
    $this->template->setCurrentBlock('RESULT');


    $familyId = isset($_POST['familyId']) ?  $_POST['familyId'] : null;
    $family=Family::GetItemById($familyId);
    if (is_null($family)) {
      $html ="<p>Cannot find any Family in the database</p>\n";
    } else {
      $html="<p>" . $family->parentsName() . "<br />\n";
      $addr = $family->address;
      $html.="$addr->addr1, $addr->city, $addr->state, $addr->zipcode ($family->phone)<br />\n";
      $html.="</p>\n";

      $html .= str_replace("==FAMILYID==", $family->id, file_get_contents("../html/formAddChild.inc"));
}
    $this->template->setVariable("RESULT", $html);
    $this->template->parseCurrentBlock();
    print $this->template->get();
    return;
    
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
	Mail::mailResetPasswordCode($email, $person, $url, $this->ip, 1);
	$html .= "Please see email sent to $email and follow instructions to reset the password.\n";
	$html .= "Click <a href='/dakhila/php/dataViewer2.php?command=login'>login</a> to login\n";
      }
    }
    $this->template->setVariable("RESULT", $html);
    $this->template->parseCurrentBlock();
    print $this->template->get();
  }

  // ************************************************************
  public function EventCalendar() {
    $this->SetMenu();
    $this->template->setCurrentBlock('QUERY');
    $html = file_get_contents("../html/queryEventCalendar.inc");
    $this->template->setVariable("QUERY", $html);
    $this->template->parseCurrentBlock();

    $params=null;
    Reports::DisplayEventCalendar($this->template, 0, $params);

    print $this->template->get();
  }


  private function SetMenu() {
    //    $dojomenu = file_get_contents("../html/usermenu.inc");
    $dojomenu = VidSession::Menu();

    // todo: switch logout/login

    $this->template->setCurrentBlock('MENU');
    $this->template->setVariable('MENU', $dojomenu);
    $this->template->parseCurrentBlock();

    $this->template->addBlockFile('BOTTOM', 'F_BOTTOM', 'LayoutBottom.tpl');
    $this->template->touchBlock('F_BOTTOM');
	
    $this->template->setCurrentBlock('FOOTER');
    $this->template->setVariable("FOOTER", VidSession::FooterWeb());
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

?>
