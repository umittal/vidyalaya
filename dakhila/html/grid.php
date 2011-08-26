<?php

$rootDir = $_SERVER["DOCUMENT_ROOT"] . "/dakhila";
//$rootDir =  "/var/www/dakhila";
require_once "$rootDir/libVidyalaya/db.inc";
require_once "$rootDir/libVidyalaya/vidyalaya.inc";

    VidSession::sessionAuthenticate();
?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Umesh: is the best</title>
    <!-- load Dojo -->
<link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/dojo/1.6/dijit/themes/claro/claro.css"
        />

<link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/dojo/1.6/dojox/grid/enhanced/resources/claro/EnhancedGrid.css" />
<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/dojo/1.6/dojox/highlight/resources/highlight.css" />

  <script src="http://ajax.googleapis.com/ajax/libs/dojo/1.6.0/dojo/dojo.xd.js"></script>
</head>
    
<body class=" claro ">
<div id="grid1" dojoType="dojox.grid.EnhancedGrid" 
    plugins='{
     nestedSorting: true, 
     dnd: true, 
     "filter": {closeFilterbarButton: true,ruleCount: 0}, 
     }' 
    store="jsonStore" structure="layout" 
     style="height:730px; width:600px;"
>

</div>
<script>
    //requires
    dojo.require('dojox.grid.EnhancedGrid');
    dojo.require("dojox.grid.enhanced.plugins.Pagination");
    dojo.require('dojox.grid.enhanced.plugins.DnD');
    dojo.require('dojox.grid.enhanced.plugins.NestedSorting');
    dojo.require('dojo.data.ItemFileReadStore');
dojo.require("dojox.grid.enhanced.plugins.Filter");
    dojo.require('dijit.form.Button');
    dojo.require('dojo.parser');

    //when resources are loaded
    dojo.ready(function() {
        //layout
        layout = [{
            defaultCell: { width: 8, editable: false, type: dojox.grid.cells._Widget },
            rows:
            [
                { field: 'department', name:'Department', width: '6' },
                { field: 'level', name:'Level', width: '2' },
                { field: 'short', name:'Short', width: '2' },
                { field: 'full', name:'Description', width: '24' },
                { field: 'id', name:'ID',  width: '2', datatype: "number" },
            ]}
        ];
        //get data store
        jsonStore = new dojo.data.ItemFileReadStore({ id:'jsonStore', 

<?php
	      echo "url:'/dakhila/php/dataserver.php?command=CourseCatalog'";
?>


});
        //parse!
        dojo.parser.parse();
    });
</script>
</body>
</html>
