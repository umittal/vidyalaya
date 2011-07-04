<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Vidyalaya</title>

	<link rel="stylesheet" type="text/css" media="screen" href="/jqsuite/themes/redmond/jquery-ui-1.8.2.custom.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="/jqsuite/themes/ui.jqgrid.css" />

	<script src="/jqsuite/js/jquery.js" type="text/javascript"></script>
	<script src="/jqsuite/js/i18n/grid.locale-en.js" type="text/javascript"></script>
	<script src="/jqsuite/js/jquery.jqGrid.min.js" type="text/javascript"></script>

</head>
<body>
    Group By: <select id="chngroup"> 
        <option value="Dept">Dept</option> 
        <option value="Room">Room</option> 
        <option value="clear">Remove Grouping</option>     
        </select><br/> 
      <div> 

      <?php 
        require_once "grid2.php";
?>

</div>

</body>
</html>
