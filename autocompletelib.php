<?php

$conn = mysqli_connect("localhost","root","ipf2018123456","rnaseqdb");
if (!$conn){
	die ("不能连接数据库".mysql_error()."<br/>");
}

$lib = strtoupper($_GET["term"]);

$sql = "SELECT libID,lib FROM libbam WHERE lib like '".$lib."%' limit 200";
$query = mysqli_query($conn, $sql);

while ($row=mysqli_fetch_array($query)){ 
	$result[] = array( 
		'id' => $row['libID'],
		'label' => $row['lib']
	); 
}
echo json_encode($result);

?>
