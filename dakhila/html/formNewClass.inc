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

<form  dojoType="dijit.form.Form" id="newClass" name="newClass">
    <script type="dojo/event" event="onSubmit" args="e">
    dojo.stopEvent(e); // prevent the default submit
if (!this.isValid()) {
  window.alert('Please fix fields');
  return;
}


qObject = new Object();

qObject.course =dijit.byId("course").value;
qObject.section =dijit.byId("section").value;
qObject.startTime =dijit.byId("startTime").value;
qObject.endTime =dijit.byId("endTime").value;

var queryString = dojo.objectToQuery(qObject);

r = dijit.byId("response");
r.set("value"," Form being sent...");
var xhrArgs ={
url: '/dakhila/php/datainsert.php?command=InsertClass',
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


<div class="formTitle">Create New Class</div>

<table >
  <tr>
  <td style="padding-right:20px;">

  <table>
  <tr>
  <td>Year</td>
  <td>2012-13</td>
  </tr>
	
  <tr>
  <td><label for="course">Course</label></td>
 <td><select name="course" dojoType="dijit.form.FilteringSelect" id="course">
       	<option value="14 "> C1    - Fun & Games with Indian Culture                 </option>
       	<option value="15 "> C2    - Indian Stories, Songs & Shlokas                 </option>
       	<option value="16 "> C3    - Who am I? Hindu, Indian, American               </option>
       	<option value="17 "> C4    - Exploring India's Past, Present & Future        </option>
	<option value="18 "> C5    - Understanding our Hindu/Indian Values           </option>
	<option value="19 "> C6    - The Pillars of Hinduism                         </option>
	<option value="20 "> C7    - India's contribution to the World               </option>
	<option value="21 "> C8    - Hinduism and Spirituality                       </option>
	<option value="22 "> C9    - Bhagavad Gita and its Relevance to Modern Times </option>
	<option value=" 7 "> G1    - Gujarati Novice Low                             </option>
	<option value=" 8 "> G2    - Gujarati Novice Mid                             </option>
	<option value=" 9 "> G3    - Gujarati Novice High                            </option>
	<option value="10 "> G4    - Gujarati Intermediate Low                       </option>
	<option value="11 "> G5    - Gujarati Intermediate Mid                       </option>
	<option value="1" selected="selected">H1 - Hindi Novice Low</option>
       	<option value="2">H2 - Hindi Novice Mid</option>
       	<option value="3">H3 - Hindi Novice High</option>
	<option value=" 4 "> H4    - Hindi Intermediate Low                          </option>
	<option value=" 5 "> H5    - Hindi Intermediate Mid                          </option>
	<option value=" 6 "> H6    - Hindi Intermediate High                         </option>
	<option value="23 "> KG    - Kindergarten                                    </option>
	<option value="12 "> T1    - Telugu Novice Low                               </option>
	<option value="24 "> T2    - Telugu Novice Mid                               </option>
	<option value="13 "> T3    - Telugu Novice High                              </option>
	<option value="25 "> T4    - Telugu Intermediate Low                         </option>
	<option value="27 "> HC    - Hindi Conversation                         </option>
	<option value="28 "> GC    - Gujarati Conversation                         </option>
    </select>
  </tr>
	
  <tr>
  <td><label for="section">Section</label></td>
  <td> <select name="section" dojoType="dijit.form.FilteringSelect" id="section">
       <option value=" " selected="selected"></option>
       <option value="A">A</option>
       <option value="B">B</option>
    </select>
  </td>
  </tr>
	
	
  <tr>
  <td><label for="startTime">Start Time</label></td>
  <td><input  data-dojo-type="dijit.form.TextBox" id="startTime" name="startTime" /></td>
  </tr>

  <tr>
  <td><label for="endTime">End Time</label></td>
  <td><input  data-dojo-type="dijit.form.TextBox" id="endTime" name="endTime" /></td>
  </tr>

</table>

<button dojoType="dijit.form.Button" type="submit" >Create</button>
<button dojoType="dijit.form.Button" type="reset" >Reset</button>

        <div class="dijitDialogPaneContentArea">
	<div dojoType="dijit.form.ValidationTextBox" id="response" disabled="disabled">nothing</div>
	</div>

</form>


</div>

