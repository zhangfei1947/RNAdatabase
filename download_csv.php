<?php
$table = $_GET["tb"];
//echo "<script>alert('".$table."')</script>";
if ($table == "alllib"){
	$csv = "data/library_info.xls";
	echo "<script>window.open('".$csv."')</script>";
}else{
	$table = "user/".$table;
	$csv = $table.".xls";
	exec("python download_csv.py ".$table." ".$csv);
	echo "<script>window.open('".$csv."')</script>";
};
?>