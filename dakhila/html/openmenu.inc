<div dojoType="dijit.MenuBar" id="navMenu">
    <div dojoType="dijit.PopupMenuBarItem">
        <span>
            View
        </span>
        <div dojoType="dijit.Menu" id="home">
            <div dojoType="dijit.MenuItem" onClick='window.location="/dakhila/php/userdata.php?command=EventCalendar"'>
                Calendar
            </div>
            <div dojoType="dijit.MenuItem" onClick='window.location="/dakhila/php/userdata.php?command=Volunteers"'>
                Volunteers
            </div>
        </div>
    </div>

    <div dojoType="dijit.PopupMenuBarItem">
        <span>
            Tools
        </span>
        <div dojoType="dijit.Menu" id="logout">
            <div dojoType="dijit.MenuItem" onClick='window.location="/dakhila/php/dataViewer2.php?command=login"'>
                Login
            </div>
        </div>
    </div>
</div>
