<script>
  dojo.require("dojo.parser");  
  dojo.require("dijit.form.Button"); 
  dojo.require("dijit.form.Form");
  dojo.require("dijit.form.Select");
  dojo.require("dijit.form.ValidationTextBox");    
  dojo.require("dijit.form.DateTextBox");
</script>

<h3>Event Calendar Lookup</h3>
<form dojoType="dijit.form.Form" method="post" action="/dakhila/php/userdata.php?command=EventCalendar">
<label for="startDate">Start</label> 
<input type="text" name="startDate" id="startDate" value="now"
    data-dojo-type="dijit.form.DateTextBox"
    required="true" />

<label for="Laout">Layout</label> 
    <select id="Layout" dojoType="dijit.form.Select" name="Layout">
    <option value=0 selected="selected">Events</option>
    <option value=1>SchoolDays</option>
    <option value=2>Yoga</option>
    </select>

<button dojoType="dijit.form.Button" type="submit" id="submit"   >Go</button>
</form>


