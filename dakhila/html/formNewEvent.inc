      <style type="text/css">
       @import "/dakhila/css/form.css"; 
       </style>

<script>
  dojo.require("dojo.parser");  
  dojo.require("dijit.form.Button"); 
  dojo.require("dijit.form.Form");
  dojo.require("dijit.form.ValidationTextBox");    
  dojo.require("dijit.form.DateTextBox");
</script>

<div class="formContainer"  style="width:800px">

<form  dojoType="dijit.form.Form" id="newEvent" name="newEvent">
<div class="formTitle">New Event</div>
<table>
<tr><td>Type</td></tr>
<tr><td>Date</td>
    <td>  <input type="text" name="eventDate" id="eventDate" value="now"
    data-dojo-type="dijit.form.DateTextBox"
    required="true" />
    </td>
</tr>
<tr><td>Week Number</td></tr>
<tr><td>Shiksha Portal ID</td>
    <td>
    <input id="portalId" name="portalId"
    style="width: 3em;" 
    data-dojo-type="dijit.form.ValidationTextBox"
    data-dojo-props="validator:dojox.validate.isInRange,
        constraints:{ min:1, max:999},
        promptMessage: 'Shiksha Portal Document Id',
        invalidMessage:'Enter valid document id between 0 and 999'"
    </td>
</tr>
<tr><td>Start Time</td>
    <td>
    <input id="startHour" name="startHour"
    style="width: 2em;" 
    data-dojo-type="dijit.form.ValidationTextBox"
    data-dojo-props="validator:dojox.validate.isInRange,
        constraints:{ min:0, max:12 },
        promptMessage: 'Start Time Hour',
        invalidMessage:'Enter valid hour between 0 and 12'"
    /> :
    <input id="startMinute" name="startMinute"
    data-dojo-type="dijit.form.ValidationTextBox"
    style="width: 2em;" 
    data-dojo-props="validator:dojox.validate.isInRange,
        constraints:{ min:0, max:59 },
        promptMessage: 'Start Time Minute',
        invalidMessage:'Enter valid Minute between 0 and 59'"
    />
    </td>
</tr>
<tr><td>End Time</td>
    <td>
    <input id="endHour" name="endHour"
    style="width: 2em;" 
    data-dojo-type="dijit.form.ValidationTextBox"
    data-dojo-props="validator:dojox.validate.isInRange,
        constraints:{ min:0, max:12 },
        promptMessage: 'End Time Hour',
        invalidMessage:'Enter valid hour between 0 and 12'"
    /> :
    <input id="endMinute" name="endMinute"
    data-dojo-type="dijit.form.ValidationTextBox"
    style="width: 2em;" 
    data-dojo-props="validator:dojox.validate.isInRange,
        constraints:{ min:0, max:59 },
        promptMessage: 'End Time Minute',
        invalidMessage:'Enter valid Minute between 0 and 59'"
    />
    </td>
</tr>
<tr><td>Class</td></tr>
<tr><td>Description</td></tr>
</table>
<button dojoType="dijit.form.Button" type="submit" >Add Event</button>
<button dojoType="dijit.form.Button" type="reset" >Reset</button>

</form>
</div>