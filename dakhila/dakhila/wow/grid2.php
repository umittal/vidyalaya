<?php

$rootDir = $_SERVER["DOCUMENT_ROOT"] . "/dakhila";
require "$rootDir/libVidyalaya/db.inc";
require "$rootDir/libVidyalaya/vidyalaya.inc";


$jqDir = $_SERVER["DOCUMENT_ROOT"] . "/jqsuite";
require_once "$jqDir/jq-config.php";
// include the jqGrid Class
require_once ABSPATH."php/jqGrid.php";
// include the driver class
require_once ABSPATH."php/jqGridArray.php";

// create the array connection
$conn = new jqGridArray();
// Create the jqGrid instance
$grid = new jqGridRender($conn);
// Create a random array data

$facility=2;
$year=1;
$i=0;
	$classCount = array();
	foreach (Enrollment::GetAllEnrollmentForFacilitySession($facility, $year) as $item) {
		if (empty($classCount[$item->class->id])) $classCount[$item->class->id]=0;
		$classCount[$item->class->id]++;
	}
	
	foreach (AvailableClass::GetAllYear($year) as $item) {
    $data1[$i]['id']    = $item->id;
    $data1[$i]['Dept']    = Department::NameFromId( $item->course->department);
    $data1[$i]['Level']    = $item->course->level;
    $data1[$i]['Short']    = $item->course->short;
    $data1[$i]['Section']    = $item->section;
    $data1[$i]['Year']    = $item->year+2010;
    $data1[$i]['Room']    = $item->room->roomNumber;
    $data1[$i]['Start']    = $item->startTime;
    $data1[$i]['End']    = $item->endTime;
    $students = empty($classCount[$item->id]) ? 0 :$classCount[$item->id];

    $data1[$i]['Students']    = $students;

    $i++;
	}



// Always you can use SELECT * FROM data1
$grid->SelectCommand = "SELECT id, Dept, Level, Short, Section, Students, Year, Room, Start, End FROM data1";

$grid->dataType = 'json';
$grid->setPrimaryKeyId('id');

$grid->setColModel();
// Enable navigator

$grid->setUrl('grid2.php?command=availablegrid');

$grid->setColProperty(
		      "Start", array( 
    "formatter"=>"date", 
    "formatoptions"=>array("srcformat"=>"H:i:s","newformat"=>"H:i") 
				      )
); 

$grid->setColProperty(
		      "End", array( 
    "formatter"=>"date", 
    "formatoptions"=>array("srcformat"=>"H:i:s","newformat"=>"H:i") 
				      )
); 

$grid->setGridOptions(array(
    "rowNum"=>40,
    "rowList"=>array(10,20,30),
    "height" => 'auto',
    "sortname"=>"id",
    "grouping"=>true, 
    "groupingView"=>array( 
			  "groupField" => array('Dept', 'Short'), 
        "groupColumnShow" => array(true), 
        "groupText" =>array('<b>{0}</b>'), 
        "groupDataSorted" => true , 
        "groupSummary" => array(true) 
			   )
));

// Change grouping 
$dynamic = <<<DYNAMIC
jQuery("#chngroup").change(function(){ 
    var vl = $(this).val(); 
    if(vl) { 
        if(vl == "clear") { 
            jQuery("#grid").jqGrid('groupingRemove',true); 
        } else { 
            jQuery("#grid").jqGrid('groupingGroupBy',vl); 
        } 
    } 
}); 
DYNAMIC;

$grid->setJSCode($dynamic); 

// Add a summary property to the Freight Column 
$grid->setColProperty("Students", array("summaryType"=>"sum", "summaryTpl"=>"Sum: {0}", "formatter"=>"integer"));


$grid->navigator = true;
// Enable search
$grid->setNavOptions('navigator', array("excel"=>false,"add"=>false,"edit"=>false,"del"=>false,"view"=>false,"csv"=>true, "pdf"=>true));
// Activate single search
$grid->setNavOptions('search',array("multipleSearch"=>false));
// Enjoy
error_log("i am rendering grid from grid2.php");
$grid->renderGrid('#grid','#pager',true, null, null, true,true);


?>
