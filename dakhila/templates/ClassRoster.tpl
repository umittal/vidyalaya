<h3>Class Roster</h3>


<!-- BEGIN ROSTERHEAD -->
<table>
<tr><td>Class</td><td>{SESSION} - {SHORT} - {LONG}</td></tr>
<tr><td>Location</td><td>Room {ROOM} - {FACILITY}</td></tr>
<tr><td>Teachers</td><td>{TEACHERS}</td></tr>
</table>
<!-- END ROSTERHEAD -->

<table id="rowspan" cellspacing="0" class="tablesorter">
<colgroup span=5><colgroup span=4>
<thead>
<tr><th colspan=5>Students</th><th colspan=4>Parents</th></tr>
<tr>
<th>First</th><th>Last</th><th>Age</th><th>Grade</th><th>email</th><th>First</th><th>Last</th><th>Phone</th><th>email</th>
</tr>
</thead>
<!-- BEGIN ROSTERROW -->
<tbody>
<!-- BEGIN MOTHERROW -->
<tr>
<td rowspan=2>{SFIRST}</td><td rowspan=2>{SLAST}</td><td rowspan=2>{AGE}</td><td rowspan=2>{GRADE}</td><td rowspan=2>{SEMAIL}</td><td>{PFIRST}</td><td>{PLAST}</td><td>{PHONE}</td><td>{PEMAIL}</td></tr>
<!-- END MOTHERROW -->
<!-- BEGIN FATHERROW -->
<tr style="border-bottom: thin solid  black;"><td>{PFIRST}</td><td>{PLAST}</td><td>{PHONE}</td><td>{PEMAIL}</td></tr>
<!-- END FATHERROW -->
</tbody>
<!-- END ROSTERROW -->
</table>
