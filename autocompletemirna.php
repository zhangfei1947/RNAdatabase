<?php

$conn = mysqli_connect("localhost","root","ipf2018123456","rnaseqdb");
if (!$conn){
	die ("不能连接数据库".mysql_error()."<br/>");
}

$lib = strtoupper($_GET["term"]);

$sql = "SELECT miRNAName FROM miRNA WHERE miRNAName  like '".$lib."%' limit 50";
$query = mysqli_query($conn, $sql);

while ($row=mysqli_fetch_array($query)){ 
	$result[] = array( 
		'label' => $row['miRNAName']
	); 
}
echo json_encode($result);

?>
