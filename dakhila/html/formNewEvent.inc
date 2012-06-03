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
    <script type="dojo/event" event="onSubmit" args="e">
    dojo.stopEvent(e); // prevent the default submit
    if (!this.isValid()) {
      window.alert('Please fix fields');
      return;
    }
    qObject = new Object();
    qObject.eventType =dijit.byId("eventType").value;
    qObject.eventDate=dojo.date.locale.format(dijit.byId('eventDate').value, {datePattern: "yyyy-MM-dd", selector: "date"});
    qObject.portalId =dijit.byId("portalId").value;
    qObject.classId =dijit.byId("classId").value;
    qObject.description =dijit.byId("description").value;

    qObject.startHour =dijit.byId("startHour").value;
    qObject.startMinute =dijit.byId("startMinute").value;
    qObject.startampm =dijit.byId("startampm").value;

    qObject.endHour =dijit.byId("endHour").value;
    qObject.endMinute =dijit.byId("endMinute").value;
    qObject.endampm =dijit.byId("endampm").value;


    var queryString = dojo.objectToQuery(qObject);
    r = dijit.byId("response");
    r.set("value"," Form being sent...");
    var xhrArgs ={
    url: '/dakhila/php/datainsert.php?command=InsertEvent',
    postData:queryString,
    load: function(data, ioArgs) {
	r.set("value","Success ..." + data + " Created");		    
      },
    error: function(error, ioArgs) {
	//We'll 404 in the demo, but that's okay.  We don't have a 'postIt' service on the
	//docs server.
	r.set("value","Failed... " + error);		    
      }
    };

   var deferred = dojo.xhrPost(xhrArgs);
</script>

<div class="formTitle">New Event</div>
<table>
<tr class="formRow"><td>Type</td>
    <td>
    <select name="eventType" dojoType="dijit.form.FilteringSelect" id="eventType" >
    <option value="1">School Day</option>
    <option value="2">Yoga</option>
    <option value="4">Presentation</option>
    <option value="8" selected="selected">Meeting</option>
    <option value="16">Activity</option>
    </select>
    </td>

</tr>
<tr class="formRow"><td>Date</td>
    <td>  <input type="text" name="eventDate" id="eventDate" value="now"
    data-dojo-type="dijit.form.DateTextBox"
    required="true" />
    </td>
</tr>

<tr class="formRow"><td>Shiksha Portal ID</td>
    <td>
    <input id="portalId" name="portalId"
    style="width: 3em;" 
    data-dojo-type="dijit.form.ValidationTextBox"
    data-dojo-props="validator:dojox.validate.isInRange,
        constraints:{ min:0, max:999},
        promptMessage: 'Shiksha Portal Document Id',
        invalidMessage:'Enter valid document id between 0 and 999'"
    </td>
</tr>
<tr class="formRow"><td>Start Time</td>
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
    &nbsp;
    <select name="startampm" dojoType="dijit.form.FilteringSelect" id="startampm" style="width: 4em;" >
    <option value="AM" selected="selected">AM</option>
    <option value="PM">PM</option>
    </select>

    </td>
</tr>
<tr class="formRow"><td>End Time</td>
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
    &nbsp;
    <select name="endampm" dojoType="dijit.form.FilteringSelect" id="endampm" style="width: 4em;" >
    <option value="AM" selected="selected">AM</option>
    <option value="PM">PM</option>
    </select>
    </td>
</tr>
<tr class="formRow"><td>Class</td>
    <td><input  type="text" data-dojo-type="dijit.form.TextBox" id="classId" name="classId" promptMessage="Enter class ID for now" /></td>
</tr>
<tr class="formRow"><td>Description</td>
    <td><input  type="text" data-dojo-type="dijit.form.TextBox" id="description" name="description" /></td>
</tr>
</table>
<button dojoType="dijit.form.Button" type="submit" >Add Event</button>
<button dojoType="dijit.form.Button" type="reset" >Reset</button>

        <div class="dijitDialogPaneContentArea">
	<div dojoType="dijit.form.ValidationTextBox" id="response" disabled="disabled">nothing</div>
	</div>

</form>

     <a href="/dakhila/php/userdata.php?command=EventCalendar">Calendar Viewer</a>
</div>