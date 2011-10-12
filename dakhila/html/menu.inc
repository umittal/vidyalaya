<div dojoType="dijit.MenuBar" id="navMenu">
    <div dojoType="dijit.PopupMenuBarItem">
        <span>
            Roster
        </span>
        <div dojoType="dijit.Menu" id="fileMenu">
            <div dojoType="dijit.MenuItem" onClick='window.location="/dakhila/php/dataViewer2.php?command=home"'>
                Home
            </div>
            <div dojoType="dijit.MenuItem" onClick='window.location="/dakhila/php/dataViewer2.php?command=Teachers"'>
                Teachers
            </div>
            <div dojoType="dijit.MenuItem" onClick='window.location="/dakhila/php/dataViewer2.php?command=ClassRoster"'>
                Roster
            </div>
            <div dojoType="dijit.MenuItem" onClick='window.location="/dakhila/php/dataViewer2.php?command=Volunteers"'>
                Volunteers
            </div>
            <div dojoType="dijit.MenuItem" onClick='window.location="/dakhila/php/dataViewer2.php?command=lcmatrix"'>
                LC Matrix
            </div>
            <div dojoType="dijit.MenuItem" onClick='window.location="/dakhila/php/dataViewer2.php?command=RegistrationSummary"'>
                Registration
            </div>
            <div dojoType="dijit.MenuItem" onClick='window.location="/dakhila/php/dataViewer2.php?command=FamilyTracker"'>
                Family Tracker
            </div>
        </div>
    </div>
    <div dojoType="dijit.PopupMenuBarItem">
        <span>
            Admission
        </span>
        <div dojoType="dijit.Menu" id="editMenu">
            <div dojoType="dijit.MenuItem" onClick='window.location="/dakhila/php/dataViewer2.php?command=Family"'>
                Family
            </div>
            <div dojoType="dijit.MenuItem" onClick='window.location="/dakhila/php/dataViewer2.php?command=Student"'>
                Student
            </div>
            <div dojoType="dijit.MenuItem" onClick='window.location="/dakhila/php/dataViewer2.php?command=person"'>
                Person
            </div>
            <div dojoType="dijit.MenuItem" onClick='window.location="/dakhila/php/dataViewer2.php?command=Room"'>
                Room
            </div>
            <div dojoType="dijit.MenuItem" onClick='window.location="/dakhila/html/grid.php"'>
                Courses
            </div>
            <div dojoType="dijit.MenuItem" onClick='window.location="/dakhila/php/dataViewer2.php?command=AvailableCourse"'>
                Classes
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
