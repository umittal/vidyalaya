

        <style type="text/css">
            .dijitButtonNode { width:150px; }
        </style>




    <script>
      $(function() {	$( "#tabs" ).tabs();});
      document.title = "Vidyalaya - Dakhila Portal";
       dojo.require("dijit.form.Button"); 
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

	  <tr><td>
	      <button dojoType="dijit.form.Button" type="button" >
		Available Courses
		<script type="dojo/method" event="onClick" args="evt">
		  window.open('/dakhila/php/dataViewer2.php?command=AvailableCourse&facility=1&year=2011', '_self', 'location=no' );
		</script>
	      </button>

	    </td><td>		Classes (Department, course, room, count) setup for any year/facility </td>
	  </tr>


	  <tr><td>
	      <button dojoType="dijit.form.Button" type="button" >
		Class Roster
		<script type="dojo/method" event="onClick" args="evt">
		  window.open('/dakhila/php/dataViewer2.php?command=ClassRoster&classId=75', '_self', 'location=no' );
		</script>
	      </button>

	    </td><td>List of Students in any class</td>
	  </tr>

	  <tr><td>
	      <button dojoType="dijit.form.Button" type="button" >
		Language Culture Matrix
		<script type="dojo/method" event="onClick" args="evt">
		  window.open('/dakhila/php/dataViewer2.php?command=lcmatrix', '_self', 'location=no' );
		</script>
	      </button>

	    </td><td>Common students between any two classes</td>
	  </tr>

	  
	  <tr><td colspan=2>&nbsp;</td></tr>
	  <tr><td colspan=2>Database Lookup</td></tr>

	  <tr><td>
	      <button dojoType="dijit.form.Button" type="button" >
		Course Catalog
		<script type="dojo/method" event="onClick" args="evt">
		  window.open('/dakhila/html/grid.php', '_self', 'location=no' );
		</script>
	      </button>
	    </td><td>Vidyalaya Course Catalog - All courses may not be offered every year</td>
	  </tr>

	  <tr><td>
	      <button dojoType="dijit.form.Button" type="button" >
		Rooms
		<script type="dojo/method" event="onClick" args="evt">
		  window.open('/dakhila/php/dataViewer2.php?command=Rooms&facility=1', '_self', 'location=no' );
		</script>
	      </button>
	    </td><td>Rooms available to hold classes</td>
	  </tr>



	</table>
      </div>

      <div id="tabs-2">
	<table>
	  <tr><td><a href="/dakhila/php/dataViewer2.php?command=Family&familyId=47">Family Detail</tr>
	  <tr><td><a href="/dakhila/php/dataViewer2.php?command=Student">Student Detail</tr>
	  <tr><td><a href="/dakhila/php/dataViewer2.php?command=Room">Room Detail</tr>
	  <tr><td><a href="/dakhila/php/dataViewer2.php?command=Teachers">Teachers</tr>
	  <tr><td><a href="/dakhila/php/dataViewer2.php?command=Volunteers">Volunteers</tr>
	  <tr><td><a href="/dakhila/php/dataViewer2.php?command=lcmatrix">Language/Culture Matrix</tr>
	  <tr><td></td></tr>
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
