<html>
  <head>


    <link type="text/css" href="/dakhila/css/ui-lightness/jquery-ui-1.8.16.custom.css" rel="Stylesheet" />	

    <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
    <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js"></script>

  </head>
  <body>
    <script>
      $(function() {	$( "#tabs" ).tabs();});
    </script>


    <div id="tabs">
      <ul>
	<li><a href="#tabs-1">Roster</a></li>
	<li><a href="#tabs-2">Admission</a></li>
	<li><a href="#tabs-3">Legacy</a></li>
	<li><a href="#tabs-4">Support</a></li>
      </ul>

      <div id="tabs-1">
	<table>

	  <tr>
	    <td><a href="/dakhila/php/dataViewer2.php?command=AvailableCourse&facility=2&year=2011">Available Courses</td>
	    <td>Classes (Department, course, room, count) setup for any year/facility </td>
	  </tr>

	  <tr><td><a href="/dakhila/php/dataViewer2.php?command=ClassRoster&classId=75">Class Roster</td>
	    <td>Roster for any class </td>
	  </tr>

	  <tr><td><a href="/dakhila/php/dataViewer2.php?command=CourseCatalog">Course Catalog</td>
	    <td>Vidyalaya Course Catalog - All courses may not be offered every year</td>
	  </tr>

	</table>
      </div>

      <div id="tabs-2">
	<table>
	  <tr><td><a href="/dakhila/php/dataViewer2.php?command=Family&familyId=47">Family Detail</tr>
	  <tr><td><a href="/dakhila/php/dataViewer2.php?command=Registration&familyId=47">Registration Form</tr>
	  <tr><td><a href="/dakhila/php/dataViewer2.php?command=MedicalForm&studentId=1446">Medical Form</tr>
	  <tr><td><a href="/dakhila/php/dataViewer2.php?command=RegistrationSummary">2011 Registration Summary</tr>
	</table>
      </div>

      <div id="tabs-3">
	<table>
	  <tr><td><a href="/dakhila/php/classsize.php">Class Size</td></tr>
	  <tr><td><a href="/dakhila/php/studentListByLanguage.php">Student By Language Class</tr>
	  <tr><td><a href="/dakhila/php/studentListByCulture.php">Student By Culture Class</tr>
	  <tr><td><a href="/dakhila/php/familyList.php">Families</td></tr>
	</table>
      </div>

      <div id="tabs-4">
	<table>
	  <tr><td><a href="/dakhila/password.php">Change Password</td></tr>
	</table>
      </div>

    </div>
  </body>
</html>
