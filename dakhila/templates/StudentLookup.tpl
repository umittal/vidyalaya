
<h3>Student Lookup</h3>

<!-- BEGIN MORETHAN20 -->
<p>Found: {COUNT}, Displaying: {MAXDISPLAY}</p>
<!-- END MORETHAN20 -->


<table id="rowspan" cellspacing="0" class="tablesorter">
<thead>
<tr><th>ID</th><th>First</th><th>Last</th><th>Parents</th></tr>
</thead>
<tbody>

<!-- BEGIN STUDENTLOOKUP -->
<tr><td class="ou" onclick="showStudentDetails({ID})" onmouseover="this.style.cursor='pointer'">{ID}</td>
    <td>{FIRST}</td><td>{LAST}</td><td>{PARENT}</td>
</tr>    
<!-- END STUDENTLOOKUP -->
</tbody>

</table>

<!-- BEGIN HIDDENFORMS -->
{STUDENTFORM}
<!-- END HIDDENFORMS -->

