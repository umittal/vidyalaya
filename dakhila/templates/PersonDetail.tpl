<h3>Person Details</h3>
 
<ol>
<li class="section">Home</li>

<table class="tablesorter">
<!-- BEGIN HOME -->
<tr><td>ID</td><td>{ID} (Home: {HOMEPHONE})</td></tr>
<tr><td rowspan=2>Address</td><td>{LINE1} {LINE2}</td></tr>
<tr><td>{CITY}, {STATE} {ZIP}</td></tr>
<!-- END HOME -->
</table>

<li class="section">Person</li>
<table class="tablesorter" >  	  	  
<!-- BEGIN PERSON -->
<tr><td>Name</td><td>{NAME}</td></tr>
<tr><td>Gender</td><td>{GENDER}</td></tr>
<tr><td>Email</td><td>{EMAIL}</td></tr>
<tr><td>Work Phone</td><td>{WORK}</td></tr>
<tr><td>Cell Phone</td><td>{CELL}</td></tr>
<!-- END PERSON -->
</table>


<!-- BEGIN SHOWPARENTS -->
<li class="section">Parents</li>



<table class="tablesorter" >  	  	  
 <thead><tr><th>M/F<th>NAME<th>EMAIL<th>WORK<th>CELL<th>Directory</tr></thead>
<!-- BEGIN PARENTS -->
<tr><td>{MF} </td><td>{NAME}</td><td> {EMAIL}</td><td>{WORK}</td><td>{CELL}</td><td>{ISCONTACTABLE}</td></tr>
<!-- END PARENTS -->  	  	  
</table>

<!-- END SHOWPARENTS -->  	  	  


</ol>
