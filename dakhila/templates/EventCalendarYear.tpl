<style type="text/css">
td { width:50px; padding: 0px 5px 0px 0px;  white-space: nowrap;}
td.calcol {border-left: 1px dotted #8B9FB5; text-align:center; font: 9px Tahoma, sans-serif;}
td.grey {width:2em; background-color:grey;vertical-align:middle; text-align:center;}

td.heading {text-align:left; padding-left:10px;font-weight: bold; font:14px Tahoma, Geneva, sans-serif;}
td.calcol1 {border-left: 1px dotted #8B9FB5; text-align:center; font-weight: bold; font:14px Tahoma, Geneva, sans-serif;}
tr.firstrow {background-color: #4040FF; color:white; }

table { border-collapse: collapse;}
table tbody { border-top: 3px groove #010191; margin-top: 10px;padding-top: 10px;}
</style>

<!-- BEGIN SESSIONBLK -->
<h3>Calendar: {SESSION}</h3>
<!-- END SESSIONBLK -->


<table>
<!-- BEGIN MONTHLY -->
<tbody>
<tr class="firstrow"><td class="heading">{MONTH}</td>
	<td class="calcol1">{SCHOOLDAY0}</td>
	<td class="calcol1">{SCHOOLDAY1}</td>
	<td class="calcol1">{SCHOOLDAY2}</td>
	<td class="calcol1">{SCHOOLDAY3}</td>
	<td class="calcol1">{SCHOOLDAY4}</td>
	<td rowspan=4 class="grey">{MONTHLYTOTAL}</td><td class="calcol">&nbsp;</td>
</tr>

<tr><td class="calcol" style="text-align:left;padding-left:10px;">Yoga</td>
	<td class="calcol">{YOGA0}</td><td class="calcol">{YOGA1}</td><td class="calcol">{YOGA2}</td><td class="calcol">{YOGA3}</td><td class="calcol">{YOGA4}</td><td class="calcol" style="text-align:left;padding-left:5px;">{WHOLEDAY1}</td>
</tr>

<tr style="background-color:#F0F0F6;"><td class="calcol" style="text-align:left;padding-left:10px;">Presentation</td>
	<td class="calcol">{PRESENTATION0}</td><td class="calcol">{PRESENTATION1}</td><td class="calcol">{PRESENTATION2}</td><td class="calcol">{PRESENTATION3}</td><td class="calcol">{PRESENTATION4}</td><td class="calcol" style="text-align:left;padding-left:5px;">{WHOLEDAY2}</td>
</tr>

<tr><td class="calcol" style="text-align:left;padding-left:10px;">Activity</td>
	<td class="calcol">{ACTIVITY0}</td><td class="calcol">{ACTIVITY1}</td><td class="calcol">{ACTIVITY2}</td><td class="calcol">{ACTIVITY3}</td><td class="calcol">{ACTIVITY4}</td><td class="calcol" style="text-align:left;padding-left:5px;">{WHOLEDAY3}</td>
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
<table>
<tr><td>Class:</td><td> {TOTAL}</td></tr>
<tr><td>No Class:</td><td> {TOTALWHOLEDAY}</td></tr>
<tr><td>Summer:</td><td> {SUMMER}</td></tr>
<tr><td>Total:</td><td> 52</td></tr>
</table>
(Culture classes allocate a week to Yoga)
<!-- END TOTALWEEKS -->
</p>
