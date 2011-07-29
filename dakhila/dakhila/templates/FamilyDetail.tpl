<h3>Family Details</h3>
<!-- 
<p><a href="/dakhila/php/studentListByLanguage.php">Language</a>, <a href="/htdocs/php/studentListByCulture.php">Culture</a></p>
 -->
 
<ol>

<li class="section">Home</li>

<table>
<!-- BEGIN HOME -->

<tr><td>ID</td><td>{ID}</td></tr>
<tr><td rowspan=2>Address</td><td>{LINE1} {LINE2}</td></tr>
<tr><td>{CITY}, {STATE} {ZIP}</td></tr>
<tr><td>Phone</td><td>{HOMEPHONE}</td></tr>
<tr><td>Category</td><td width="200px">{CATEGORY}</td></tr>
<tr><td>Session</td><td>2010-11</td></tr>
<!-- END HOME -->
</table>


<li class="section">Parents</li>


<table>  	  	  
 <thead><tr><th>M/F<th>NAME<th>EMAIL<th>WORK<th>CELL<th>Call</tr></thead>
<!-- BEGIN PARENTS -->
<tr><td>{MF} </td><td>{NAME}</td><td> {EMAIL}</td><td>{WORK}</td><td>{CELL}</td><td>{ISCONTACTABLE}</td></tr>
<!-- END PARENTS -->  	  	  
</table>
<p>

<li class="section">Children</li>
<table>
<thead><tr><th scope="col">ID<th>Gender<th>NAME<th>DOB<th>EMAIL<th>CELL<th>2010-11</tr></thead>
<!-- BEGIN CHILDREN -->
<tr><td>{ID}</td><td>{GENDER}</td><td>{NAME}</td><td nowrap="nowrap">{DOB}</td><td>{EMAIL}</td><td>{CELL}</td><td>{ENROLLED}</td></tr>

<!-- END CHILDREN -->
</table>



<li class="section">Class Assignment</li>
<table>
<thead>
<tr><th scope="col">Name<th>Class<th>Room<th>Teachers</tr>
</thead>
<tbody>
<!-- BEGIN REGISTRATION -->
<tr><td rowspan=2>{FIRSTNAME}</td><td>{LANGUAGE}</td><td>{LROOM}</td><td>{LTEACHER}</td></tr>
<tr><td>{CULTURE}</td><td>{CROOM}</td><td>{CTEACHER}</td></tr>
<!-- END REGISTRATION -->
</tbody>
</table>

</ol>
