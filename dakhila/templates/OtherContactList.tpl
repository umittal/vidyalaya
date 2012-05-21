<script type="text/javascript" src="/dakhila/js/jquery.tablesorter.pager.js"></script>

<script>
$(document).ready(function() { 
    $("table") 
    .tablesorter({widthFixed: true, widgets: ['zebra']}) 
    .tablesorterPager({container: $("#pager")}); 
}); 
</script>

<h3>Other Contact List</h3>
<table id="maintable" cellspacing="0" class="tablesorter" width="600px">


<thead>
<tr><th>Phone</th><th>Name</th><th>Usage</th></tr>
</thead>

<tbody>
<!-- BEGIN CONTACTS -->
<tr>
	<td class="ou" onclick="showContactDetails('{PHONE}')" onmouseover="this.style.cursor='pointer'">{PHONE}</td>
	<td>{NAME}</td>
	<td>{COUNT}</td>
</tr>
<!-- END CONTACTS -->

</tbody>

</table>

<div id="pager" class="pager">
	<form>
		<img src="/dakhila/icons/first.png" class="first"/>
		<img src="/dakhila/icons/prev.png" class="prev"/>
		<input type="text" class="pagedisplay"/>
		<img src="/dakhila/icons/next.png" class="next"/>
		<img src="/dakhila/pager/icons/last.png" class="last"/>
		<select class="pagesize">
			<option selected="selected"  value="10">10</option>
			<option value="20">20</option>
			<option value="30">30</option>
			<option  value="40">40</option>
		</select>
	</form>
</div>


<!-- BEGIN HIDDENFORMS -->
{CONTACTFORM}
<!-- END HIDDENFORMS -->
