<script>
$(document).ready(function() { 
    $("table") 
    .tablesorter({widthFixed: true, widgets: ['zebra']}) 
}); 
</script>

<h4>1. Class Room</h4>

<table class="vidyalaya"  width="800px">
<thead>
<tr><th rowspan=2>&nbsp;</th><th colspan=3 align="center">Language</th><th rowspan=2>Culture</th><th rowspan=2>Public Speaking</th></tr>
<tr><th>Hindi</th><th>Gujarati</th><th>Telugu</th></tr>
</thead>
<tbody>
<tr><td>Lead Teacher</td><td>1011</td><td>1021</td><td>1031</td><td>1041</td><td>1061</td></tr>
<tr><td>Co- Teacher</td><td>1012</td><td>1022</td><td>1032</td><td>1042</td><td>1065</td></tr>
<tr><td>Helper</td><td>1013</td><td>1023</td><td>1034</td><td>1043</td><td>1065</td></tr>
<tr><td>Recruiter</td><td>1014</td><td>1024</td><td>1034</td><td>1044</td><td>1065</td></tr>
</tbody>
</table>

<!-- BEGIN CATEGORYBLOCK -->

<!-- BEGIN CATEGORY -->
<h4>{CATEGORYID}: {DESC}</h4>
<!-- END CATEGORY -->

<table class="tablesorter" width="800px">
<thead>
<tr><th>ID</th>
<!-- <th>Code</th> -->
<th width="160px">Sub-Category</th><th width="100px">Task</th>
<!-- <th>Hours</th><th>Requirement</th> -->
<th>Description</th>
</tr>
</thead>
<tbody>

<!-- BEGIN VOLUNTEERCODE -->
<tr><td>{ID}</td>
<!-- <td>{CODE}</td> -->

<td>{DEPARTMENT}</td><td>{ROLE}</td>
<!-- <td>{HOURS}</td><td>{REQUIREMENT}</td></tr><tr> -->
<td>{DESCRIPTION}</td></tr>
<!-- END VOLUNTEERCODE -->

</tbody>
</table>

<!-- END CATEGORYBLOCK -->
