<div dojoType="dijit.MenuBar" id="navMenu">
    <div dojoType="dijit.PopupMenuBarItem">
        <span>
            File
        </span>
        <div dojoType="dijit.Menu" id="toolsMenu">
            <div dojoType="dijit.MenuItem" onClick='window.location="/dakhila/php/userdata.php?command=editor"'>
                Newsletter
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
