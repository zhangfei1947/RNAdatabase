<?php

function decideType($instring){
	if (strlen($instring) < 3){
		return "None";
	};
	
	$upstring = strtoupper($instring);
	$atcg = array("A", "T", "C", "G");
	$stringlen = strlen($upstring);
	for($i=0;$i<$stringlen;$i++){
		if (in_array($upstring[$i], $atcg)){
			if ($i == $stringlen-1){
				return "seq";
			};
		}else{
			break;
		};
	};
	
	if (substr($upstring,0,3) == "MIR" or substr($upstring,0,4) == "ATH-"){
		return "miRNAName";
	};
	
	if (substr($upstring,0,5) == "MIMAT"){
		return "miRBaseID";
	};
	
	$geo = array("GSM", "DRX", "ERX", "SRX");
	if (in_array(substr($upstring,0,3), $geo)){
		return "GEO";
	};
	
	$genestart = array("AT1G", "AT2G", "AT3G", "AT4G", "AT5G", "ATCG", "ATMG");
	if (in_array(substr($upstring,0,4), $genestart)){
		return "gene";
	};
	
	return "kw";
};


$query = $_GET['query'];
$upquery = strtoupper($query);
$advan = $_GET['advance'];
$upadvan = strtoupper($advan);

$type = decideType($query);
//echo $type;


if ($type == "None"){
	echo "None";
}else if($type == "seq"){
		setCookie("newtable", "yes", time()+3600);
		setCookie("newplot", "yes", time()+3600);
		setCookie("newinfo", "yes", time()+3600);
		setCookie("stype", "seq", time()+3600);
		setCookie("table", md5($query), time()+3600);
		$output = exec('python search_Sequences.py '.$upquery.' '.$upadvan, $arr, $stat);
//		echo 'python search_Sequences.py '.$upquery.' '.$upadvan.'</br>';
		if ($stat == '0'){
			$out_array = explode(';;;',$output);
			setCookie("plot_tpm1", $out_array[1], time()+3600);
			setCookie("plot_tpm2", $out_array[2], time()+3600);
			setCookie("plot_heatmap1", $out_array[3], time()+3600);
			setCookie("plot_heatmap2", $out_array[4], time()+3600);
			echo $out_array[0];
//			echo "<table><td>1234455</td></table>";
		}else{
			echo "something wrong, please contact the author";
		};
}else if ($type == "miRNAName" or $type == "miRBaseID"){
			setCookie("newtable", "yes", time()+3600);
			setCookie("newplot", "yes", time()+3600);
			setCookie("newinfo", "yes", time()+3600);
			setCookie("stype", "seq", time()+3600);
			setCookie("table", md5($query.$type));
//			echo 'python search_miRNA.py '.$query.' '.$type;
			$output = exec('python search_miRNA.py '.$query.' '.$type.' '.$upadvan, $arr, $stat);
			if ($stat == '0'){
				$out_array = explode(';;;',$output);
				setCookie("plot_tpm1", $out_array[1], time()+3600);
				setCookie("plot_tpm2", $out_array[2], time()+3600);
				setCookie("plot_heatmap1", $out_array[3], time()+3600);
				setCookie("plot_heatmap2", $out_array[4], time()+3600);
				echo $out_array[0];
			}else{
				echo "something wrong, please contact the author";
			};
}else if ($type == "GEO"){
	setCookie("newtable", "yes", time()+3600);
	setCookie("newplot", "yes", time()+3600);
	setCookie("newinfo", "yes", time()+3600);
	setCookie("stype", "lib", time()+3600);
	setCookie("table", md5($upquery), time()+3600);
//	echo 'python search_Library.py '.$upquery.' '.$upadvan;
	$output = exec('python search_Library.py '.$upquery.' '.$upadvan, $arr, $stat);
	if ($stat == '0'){
		$out_array = explode(';;;',$output);
		setCookie("plot_class", $out_array[1], time()+3600);
		setCookie("plot_size", $out_array[2], time()+3600);
		echo $out_array[0];
//		echo "</br>";
//		echo $out_array[1];
//		echo $out_array[2];
	}else{
		echo "something wrong, please contact the author";
	};
}else if ($type == "gene"){
	setCookie("newtable", "yes", time()+3600);
	setCookie("newplot", "yes", time()+3600);
	setCookie("newinfo", "yes", time()+3600);
	setCookie("stype", "gene", time()+3600);
	setCookie("table", md5($upquery), time()+3600);
//	echo 'python search_Gene.py '.$upquery.' '.$upadvan;
	$output = exec('python search_Gene.py '.$upquery.' '.$upadvan, $arr, $stat);
	if ($stat == '0'){
		$out_array = explode(';;;',$output);
		setCookie("plot_box", md5($upquery).".boxplot$".$out_array[1]);
		echo $out_array[0];
	}else{
		echo "something wrong, please contact the author";
	};
}else if($type == "kw"){
	setCookie("newinfo", "yes", time()+3600);
	setCookie("stype", "kw", time()+3600);
	setCookie("newplot", "no", time()+3600);
	setCookie("newinfo", "no", time()+3600);
//	echo 'python search_Keyword.py '.$upquery.' '.$upadvan;
	$output = exec('python search_Keyword.py '.$upquery.' '.$upadvan, $arr, $stat);
	if ($stat == '0'){
		echo $output;
	}else{
		echo "something wrong, please contact the author";
	}
}else{
	echo "None";
}



?>