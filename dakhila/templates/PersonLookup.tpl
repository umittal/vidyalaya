
<h3>Student Lookup</h3>

<!-- BEGIN MORETHAN20 -->
<p>Found: {COUNT}, Displaying: {MAXDISPLAY}</p>
<!-- END MORETHAN20 -->


<table id="rowspan" cellspacing="0" class="tablesorter">
<thead>
<tr><th>Family</th><th>First</th><th>Last</th>
<th>Gender </th>
<th>Home </th>
<th>Cell </th>
<th>Email </th>
</tr>
</thead>
<tbody>

<!-- BEGIN LOOKUP -->
<tr><td class="ou" onclick="showFamilyDetails({ID})" onmouseover="this.style.cursor='pointer'">{ID}</td>
    <td>{FIRST}</td><td>{LAST}</td>
<td>{GENDER}</td>
<td>{HOME}</td>
<td>{CELL}</td>
<td>{EMAIL}</td>
</tr>    
<!-- END LOOKUP -->
</tbody>

</table>

<!-- BEGIN HIDDENFORMS -->
{STUDENTFORM}
<!-- END HIDDENFORMS -->

