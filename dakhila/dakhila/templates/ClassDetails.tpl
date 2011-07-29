<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
                      "http://www.w3.org/TR/html401/loose.dtd">
<html>
<head>
  <script type="text/javascript" src="../js/validate.js">
  </script>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <title>Student Details by Class</title>
  <link rel="stylesheet" href="/dakhila/css/csstg.css" type="text/css">
  <link rel="StyleSheet" href="/dakhila/css/itable/itunes.css" type="text/css">
</head>
<body id="home">
<div id="boundary">
<div id="content">
<form name="{FORMNAME}" method="post" action="test.php"
      onsubmit="{SUBMITACTION}">
</form>
<h1>Student List by Classes</h1>
<p><a href="/dakhila/php/studentListByLanguage.php">Language</a>, <a href="/htdocs/php/studentListByCulture.php">Culture</a></p>
	  <!-- BEGIN CLASSHEAD -->  
 <table>
 <tr><td>Class:</td><td>{CLASS}</td></tr>
 <tr><td>Teachers:</td><td>{TEACHERS}</td></tr>
 <tr><td>Room:</td><td>{ROOM}</td></tr>
 <tr><td>Size:</td><td>{SIZE}</td></tr>
</table>
	  <!-- END CLASSHEAD -->  

	  <div id="itsthetable">
	  <table>
	  <thead><tr><th scope="col">#<th>ID<th>Name<th>DOB<th>emails</tr></thead>
	  <tbody>
	  <!-- BEGIN STUDENT -->  

	  <tr> <td>{COUNT}</td>
	       <td>{ID}</td>
	       <td>{FullName}</td>
	       <td>{DOB}</td>
	       <td>{EMAIL}</td>
	   </tr>

	  <!-- END STUDENT -->  
	  </tbody>
	  </table>
	  </div>

</div>
</div>
</body>
</html>
