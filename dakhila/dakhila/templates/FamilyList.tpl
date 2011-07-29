<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
                      "http://www.w3.org/TR/html401/loose.dtd">
<html>
<head>
  <script type="text/javascript" src="../js/validate.js">
  </script>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <title>Family Listing</title>
  <link rel="stylesheet" href="/dakhila/css/csstg.css" type="text/css">
  <link rel="StyleSheet" href="/dakhila/css/itable/itunes.css" type="text/css">
</head>
<body id="home">
<div id="boundary">
<div id="content">
<form name="{FORMNAME}" method="post" action="test.php"
      onsubmit="{SUBMITACTION}">
</form>
<h1>Family Listing</h1>
<p><a href="/dakhila/php/dataViewer2.php?command=home">Home</a>, <a href="/dakhila/php/studentListByLanguage.php">Language</a>, <a href="/htdocs/php/studentListByCulture.php">Culture</a></p>

	  <div id="itsthetable">
	  <table>
	  	  <tbody>
	  <!-- BEGIN FAMILYSUMMARY -->  
	  <caption>Summary</caption>
	  
	  <tr><td>Family (1 child)</td><td align="right">{FAMILYONE}</td></tr>
	  <tr><td>Family (2 children)</td><td align="right">{FAMILYTWO}</td></tr>
	  <tr><td>Family (> 2)</td><td align="right">{FAMILYTWOMORE}</td></tr>
	  
	  <tr><td>Total Revenue</td><td align="right">{REVENUE}</td></tr>
	  
	  <tr><td>Total Families</td><td align="right">{TOTALFAMILY}</td></tr>
	  <tr><td>Average Revenue Per Family</td><td align="right">{AVGFAMILY}</td></tr>
	  
	  <tr><td>Total Students</td><td align="right">{STUDENTS}</td></tr>
	  <tr><td>Average Revenue Per Student</td><td align="right">{AVERAGE}</td></tr>
	  
	  <!-- END FAMILYSUMMARY -->  
	  </tbody>
	  </table>
	  </div>
	  
	  <div id="itsthetable">
	  <table>
	  <caption>Family List</caption>
	  <thead><tr><th scope="col">#<th>ID<th>Mother<th>Father<th>Phone</tr></thead>
	  <tbody>
	  <!-- BEGIN FAMILYITEM -->  

	  <tr> <td>{COUNT}</td>
	       <td>{ID}</td>
	       <td>{MOTHER}</td>
	       <td>{FATHER}</td>
	       <td>{PHONE}</td>
	   </tr>

	  <!-- END FAMILYITEM -->  
	  </tbody>
	  </table>
	  </div>

</div>
</div>
</body>
</html>
