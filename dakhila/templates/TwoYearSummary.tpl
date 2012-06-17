<script type="text/javascript">
$(document).ready(function() {
    $table = $("#maintable")
        .tablesorter({
	widthFixed: true, 
	widgets: ['zebra'], 
	sortList:[ [0,0] ],
	headers : { 5:{sorter: false}}
	});
});


</script>
<!-- BEGIN HIDDENFORMS -->
{TWOYEARDETAIL}
<!-- END HIDDENFORMS -->


<h3>Two Year Summary</h3>
<table id="maintable" cellspacing="0" class="tablesorter" width="600px">


<thead>
<tr><th>Course</th>
<th>Previous</th>
<th>Continuing</th>
<th>Leaving</th>
<th>Retension</th>
<th>New</th>
</tr>
</thead>


<tbody>
<!-- BEGIN SUMMARY -->
<tr>
	<td>{COURSE}</td>
	<td align="right">{PREVIOUS}</td>
	<td align="right">{CONTINUING}</td>
	<td align="right">{LEAVING}</td>
	<td align="right">{RETENTION}</td>
	<td align="right">{NEW}</td>
</tr>
<!-- END SUMMARY -->
</tbody>

<tfoot>
 <!-- BEGIN TOTAL -->
<tr><td>Total</td>
	<td align="right">{PREVIOUS}</td>
	<td align="right">{CONTINUING}</td>
	<td align="right">{LEAVING}</td>
	<td align="right">{RETENTION}</td>
	<td align="right">{NEW}</td>
</tr>
<!-- END TOTAL -->
</tfoot>

</table>

<h4>Notes</h4>
<ul>
<li>Leaving and Continuging counts are from previous year register.</li>
<li>New is count from this year register.</li>
<li>We cannot use this to find total students for any course due to data from two years in a row.</li>
</ul>
