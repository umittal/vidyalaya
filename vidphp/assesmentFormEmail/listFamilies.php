<?php
require "/home/umesh/htdocs/htdocs/libVidyalaya/vidyalaya.inc";
$students = GetAllData();

foreach (Family::$objArray as $family) {
    echo "$family->id\n";

}


?>
