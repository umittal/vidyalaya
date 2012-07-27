<style type="text/css">
div.compact td {
margin:0; 
padding-left: 5px;
}
</style>
<div class="compact">
<!-- BEGIN SESSION -->
<h3>Vidyalaya {SESSION}</h3>
<!-- END SESSION -->

<h4>Physical Layout</h4>
<img style="border:1px; margin:0; padding:0;" src='/home/umesh/Dropbox/Vidyalaya-Roster/2011-12/Layouts/umesh.jpg' width='552' height='600' alt='layout'>

<table>
<!-- BEGIN DEPARTMENT -->
<tr>

<td>{DEPARTMENT}</td>

<!-- BEGIN ROOM -->
<td>{ROOM}</td>
<!-- END ROOM -->

</tr>
<!-- END DEPARTMENT -->
</table>

<h4>School Calendar</h4>
<table>
<tr><td>
<!-- school day goes here -->
<table>
<caption>School Days</caption>
<tbody>
<!-- BEGIN MONTHLY -->
<tr class="firstrow"><td class="heading">{MONTH}</td>
	<td class="calcol">{SCHOOLDAY0}</td><td class="calcol">{SCHOOLDAY1}</td><td class="calcol">{SCHOOLDAY2}</td><td class="calcol">{SCHOOLDAY3}</td><td class="calcol">{SCHOOLDAY4}</td>
</tr>
<!-- END MONTHLY -->
</tbody>
</table>

</td>
<td style="border-left: 1px solid;">
<!-- holiday goes here -->
<table>
<caption>Holidays</caption>
<!-- BEGIN HOLIDAY -->
<tr><td>{HOLIDAYDATE}</td><td>{HOLIDAYDESC}</td></tr>
<!-- END HOLIDAY -->
</table>

</td>
<td style="border-left: 1px solid;">
<!-- activities goes here -->
<table>
<caption>Activities</caption>
<!-- BEGIN ACTIVITY -->
<tr><td>{ACTIVITYDATE}</td><td>{ACTIVITYDESC}</td></tr>
<!-- END ACTIVITY -->
</table>

</td>
<td style="border-left: 1px solid;">
<table>
<caption>Publications</caption>
<tr><td>2012-12-15</td><td>Newsletter </td></tr>
<tr><td>2013-04-01</td><td>Yearbook</td></tr>
</table>
<p style="margin:0; padding:0 0 0 5px;">Topic - Two Cultures</p>
</td>
</tr>
</table>
</div>