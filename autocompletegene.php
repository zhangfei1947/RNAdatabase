<?php

$conn = mysqli_connect("localhost","root","ipf2018123456","rnaseqdb");
if (!$conn){
	die ("不能连接数据库".mysql_error()."<br/>");
}

$lib = strtoupper($_GET["term"]);

$sql = "SELECT geneid FROM geneinfo WHERE geneid like '".$lib."%' limit 50";
$query = mysqli_query($conn, $sql);

/*

$lib = str_replace("...","",$lib);
$id = "";
$label = "";

if (strlen($lib) < 3){
	$sql = "SELECT BCID,BC FROM geneidBC";
	$id = "BCID";
	$label = "BC";
}elseif(strlen($lib) == 3){
	if ($lib=="ATM" or $lib=="ATC"){
		$sql = "SELECT geneID,gene FROM genelocus WHERE gene like '".$lib."%'";
		$id = "geneID";
		$label = "gene";
	}else{
		$sql = "SELECT BCID,BC FROM geneidBC WHERE BC like '".$lib."%'";
		$id = "BCID";
		$label = "BC";
	}
}elseif(strlen($lib) == 4){
	if (substr($lib,0,3)=="ATM" or substr($lib,0,3)=="ATC"){
		$sql = "SELECT geneID,gene FROM genelocus WHERE gene like '".$lib."%'";
		$id = "geneID";
                $label = "gene";
	}else{
		$sql = "SELECT BCID,BC FROM geneidBC WHERE BC like '".$lib."%'";
		$id = "BCID";
                $label = "BC";
	}
}elseif(strlen($lib) == 5){
	if (substr($lib,0,3)=="ATM" or substr($lib,0,3)=="ATC" or $lib=="AT1G8" or $lib=="AT4G4"){
		$sql = "SELECT geneID,gene FROM genelocus WHERE gene like '".$lib."%'";
		$id = "geneID";
                $label = "gene";
	}else{
		$sql = "SELECT BCID,BC FROM geneidBC WHERE BC like '".$lib."%'";
		$id = "BCID";
                $label = "BC";
	}
}else{
	$sql = "SELECT geneID,gene FROM genelocus WHERE gene like '".$lib."%'";
	$id = "geneID";
        $label = "gene";
}




*/
while ($row=mysqli_fetch_array($query)){ 
	$result[] = array( 
		'label' => $row['geneid']
	); 
}
echo json_encode($result);

?>
