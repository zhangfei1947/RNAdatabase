<?php
$lib = $_GET["lib"];
$type = $_GET["type"];

$fake_name = md5($lib."and".$type);
if ($type == "rawfq"){
	$fake_name = "download/".$fake_name.".fastq.gz";
	$file = "../rawfq/".$lib.".fastq.gz";
	exec("ln -s ".$file." ".$fake_name);
	echo "<script>window.open('".$fake_name."')</script>";
}else if ($type == "trimfq"){
	$fake_name = "download/".$fake_name.".fastq.gz";
	$file = "../trimfq/trimmed_".$lib.".fastq.gz";
	exec("ln -s ".$file." ".$fake_name);
	echo "<script>window.open('".$fake_name."')</script>";
}else if ($type == "bam"){
	$fake_name = "download/".$fake_name.".bam";
	$file = "../bam/typed_".$lib.".updated.sorted.bam";
	exec("ln -s ".$file." ".$fake_name);
	echo "<script>window.open('".$fake_name."')</script>";
}

?>
