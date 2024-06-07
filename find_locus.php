<?php

$conn = mysqli_connect("localhost","root","********","rnaseqdb");
if (!$conn){
	die ("不能连接数据库".mysql_error()."<br/>");
}

$geneid = $_GET["geneid"];

if ($geneid[0] == "A" or $geneid[0] == "a"){
	$sql = "SELECT igvlocus FROM geneinfo WHERE geneid = '".strtoupper($geneid)."' limit 1";
	$result = mysqli_query($conn, $sql);
	while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)){
        	echo $row['igvlocus'];
	}
}elseif($geneid[0] == "m"){
        $sql = "SELECT Coordinate FROM miRNA WHERE miRNAName = '".$geneid."' limit 1";
        $result = mysqli_query($conn, $sql);
        while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)){
                echo $row['Coordinate'];
        }
}


?>
