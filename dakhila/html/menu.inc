<div dojoType="dijit.MenuBar" id="navMenu">
    <div dojoType="dijit.PopupMenuBarItem">
        <span>
            Admission
        </span>
        <div dojoType="dijit.Menu" id="admissionMenu">
            <div dojoType="dijit.MenuItem" onClick='window.location="/dakhila/php/dataViewer2.php?command=home"'>
                Home
            </div>
        <div dojoType="dijit.MenuItem" onClick='window.location="/dakhila/php/dataViewer2.php?command=RegistrationSummary"'>
            Registration
        </div>
            <div dojoType="dijit.MenuItem" onClick='window.location="/dakhila/php/dataViewer2.php?command=FamilyFeeCheck"'>
                Family Fee
            </div>
            <div dojoType="dijit.MenuItem" onClick='window.location="/dakhila/php/dataViewer2.php?command=FeeCheckReena"'>
                Fee Reena
            </div>
            <div dojoType="dijit.MenuItem" onClick='window.location="/dakhila/php/dataViewer2.php?command=WaitlistStudents"'>
                Waitlist
            </div>
            <div dojoType="dijit.MenuItem" onClick='window.location="/dakhila/php/dataViewer2.php?command=FamilyTracker"'>
                Family Tracker
            </div>
            <div dojoType="dijit.MenuItem" onClick='window.location="/dakhila/php/dataViewer2.php?command=TwoYearSummary"'>
                Two Year
            </div>
            <div dojoType="dijit.MenuItem" onClick='window.location="/dakhila/php/dataViewer2.php?command=Rooms&facility=3"'>
                Room List
            </div>
   </div>
   </div>

    <div dojoType="dijit.PopupMenuBarItem">
        <span>
            Roster
        </span>
        <div dojoType="dijit.Menu" id="rosterMenu">
            <div dojoType="dijit.MenuItem" onClick='window.location="/dakhila/php/dataViewer2.php?command=Teachers"'>
                Teachers
            </div>
            <div dojoType="dijit.MenuItem" onClick='window.location="/dakhila/php/dataViewer2.php?command=AvailableCourse"'>
                Classes
            </div>
            <div dojoType="dijit.MenuItem" onClick='window.location="/dakhila/php/dataViewer2.php?command=lcmatrix"'>
                LC Matrix
            </div>
            <div dojoType="dijit.MenuItem" onClick='window.location="/dakhila/html/grid.php"'>
                Courses
            </div>
        </div>
    </div>

    <div dojoType="dijit.PopupMenuBarItem">
        <span>
            View
        </span>
        <div dojoType="dijit.Menu" id="viewMenu">
            <div dojoType="dijit.MenuItem" onClick='window.location="/dakhila/php/dataViewer2.php?command=Family"'>
                Family
            </div>
            <div dojoType="dijit.MenuItem" onClick='window.location="/dakhila/php/dataViewer2.php?command=Student"'>
                Student
            </div>
            <div dojoType="dijit.MenuItem" onClick='window.location="/dakhila/php/dataViewer2.php?command=person"'>
                Person
            </div>
            <div dojoType="dijit.MenuItem" onClick='window.location="/dakhila/php/userdata.php?command=Volunteers"'>
                Volunteers
            </div>
            <div dojoType="dijit.MenuItem" onClick='window.location="/dakhila/php/dataViewer2.php?command=OtherContacts"'>
                Contacts
            </div>
            <div dojoType="dijit.MenuItem" onClick='window.location="/dakhila/php/userdata.php?command=EventCalendar" '>
                Calendar
            </div>
            <div dojoType="dijit.MenuItem" onClick='window.location="/dakhila/php/dataViewer2.php?command=Room"'>
                Room
            </div>
        </div>
    </div>
    <div dojoType="dijit.PopupMenuBarItem">
        <span>
            New
        </span>
        <div dojoType="dijit.Menu" id="newMenu">
            <div dojoType="dijit.MenuItem" onClick='window.location="/dakhila/php/dataViewer2.php?command=newFamily"'>
                Family
            </div>
            <div dojoType="dijit.MenuItem" onClick='window.location="/dakhila/php/dataViewer2.php?command=newClass"'>
                Class
            </div>
            <div dojoType="dijit.MenuItem" onClick='window.location="/dakhila/php/dataViewer2.php?command=newRegistration"'>
                Registration
            </div>
            <div dojoType="dijit.MenuItem" onClick='window.location="/dakhila/php/dataViewer2.php?command=newEvent"'>
                Event
            </div>
        </div>
    </div>

    <div dojoType="dijit.PopupMenuBarItem">
        <span>
            Misc
        </span>
        <div dojoType="dijit.Menu" id="miscMenu">
            <div dojoType="dijit.MenuItem" onClick='window.location="/dakhila/php/dataViewer2.php?command=VolunteerCodes"'>
                Codes
            </div>
            <div dojoType="dijit.MenuItem" onClick='window.location="/dakhila/php/dataViewer2.php?command=EventRSVP"'>
                RSVP
            </div>
        </div>
    </div>

    <div dojoType="dijit.PopupMenuBarItem">
        <span>
            Legacy
        </span>
        <div dojoType="dijit.Menu" id="legacyMenu">
            <div dojoType="dijit.MenuItem" onClick='window.location="/dakhila/php/dataViewer2.php?command=ClassRoster"'>
                Roster
            </div>
            <div dojoType="dijit.MenuItem" onClick='window.location="/dakhila/php/dataViewer2.php?command=Registration"'>
                Registration Form
            </div>
            <div dojoType="dijit.MenuItem" onClick='window.location="/dakhila/php/dataViewer2.php?command=MedicalForm"'>
                Medical Form
            </div>
        </div>
   </div>

    <div dojoType="dijit.PopupMenuBarItem">
        <span>
            Tools
        </span>
        <div dojoType="dijit.Menu" id="toolsMenu">
            <div dojoType="dijit.MenuItem" onClick='window.location="/dakhila/php/dataViewer2.php?command=logout"'>
                Logout
            </div>
            <div dojoType="dijit.MenuItem" onClick='window.location="/dakhila/password.php"'>
                Password
            </div>
        </div>
    </div>
</div>
