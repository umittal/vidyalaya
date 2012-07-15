<style type="text/css">
td { width:80px; padding: 0px 5px 0px 0px;  border-right:1px;}
td.heading {text-align:left; padding-left:15px;}
td.calcol {border-left: 1px solid red; text-align:center;}
td.grey {width:2em; background-color:grey;vertical-align:middle; text-align:center;}

tr.firstrow {background: blue; color:white; font-weight: bold;}
table { border-collapse: collapse;}
table tbody { border-top: 1px solid red; margin-top: 10px;padding-top: 10px;}
</style>

<!-- BEGIN SESSIONBLK -->
<h3>Calendar: {SESSION}</h3>
<!-- END SESSIONBLK -->


<table>
<!-- BEGIN MONTHLY -->
<tbody>
<tr class="firstrow"><td class="heading">{MONTH}</td>
	<td class="calcol">{SCHOOLDAY0}</td><td class="calcol">{SCHOOLDAY1}</td><td class="calcol">{SCHOOLDAY2}</td><td class="calcol">{SCHOOLDAY3}</td><td class="calcol">{SCHOOLDAY4}</td><td rowspan=4 class="grey">{MONTHLYTOTAL}</td>
</tr>

<tr><td>Yoga</td>
	<td class="calcol">{YOGA0}</td><td class="calcol">{YOGA1}</td><td class="calcol">{YOGA2}</td><td class="calcol">{YOGA3}</td><td class="calcol">{YOGA4}</td>
</tr>
<tr style="background-color:#F0F0F6;"><td>Presentation</td>
	<td class="calcol">{PRESENTATION0}</td><td class="calcol">{PRESENTATION1}</td><td class="calcol">{PRESENTATION2}</td><td class="calcol">{PRESENTATION3}</td><td class="calcol">{PRESENTATION4}</td>
</tr>
<tr><td>Activity</td>
	<td class="calcol">{ACTIVITY0}</td><td class="calcol">{ACTIVITY1}</td><td class="calcol">{ACTIVITY2}</td><td class="calcol">{ACTIVITY3}</td><td class="calcol">{ACTIVITY4}</td>
</tr>
<tr><td colspan=6></td>
</tr>
</tbody>
<!-- END MONTHLY -->
<tbody>
</tbody>
</table>

<p>
<!-- BEGIN TOTALWEEKS -->
Total Weeks: {TOTAL} (Culture class allocate a week to Yoga)
<!-- END TOTALWEEKS -->
</p>
