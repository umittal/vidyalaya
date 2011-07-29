<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
                      "http://www.w3.org/TR/html401/loose.dtd">
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <title>Student List</title>
  <link rel="stylesheet" href="../css/csstg.css" type="text/css">
  <link rel="StyleSheet" href="../css/itable/itunes.css" type="text/css">
</head>
<body id="home">
<div id="boundary">
<div id="content">


<h1>Student List by Classes</h1>
<p><a href="/dakhila/php/dataViewer2.php?command=home">Home</a>, <a href="studentListByLanguage.php">Language</a>, <a href="studentListByCulture.php">Culture</a></p>
	  <div id="itsthetable">
	  <table>
	  <thead><tr><th scope="col">#<th>Class</tr></thead>
	  <tbody>
	  <!-- BEGIN LANGUAGE -->  
	  <tr><td>{ITEM}<td><b>{NAME}</b> (Room: {ROOM}, Count: {SIZE})</tr>
	  <tr><td><td><b>Teacher:</b> {TEACHERS}</tr>
	  <tr><td><td><b>{PARTICIPANTS}:</b> {VALUE}</tr>
	  <!-- END LANGUAGE -->  
	  </tbody>
	  </table>
	  </div>

</div>
</div>
</body>
</html>

