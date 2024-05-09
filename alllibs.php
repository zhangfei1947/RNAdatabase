<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">


<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>


<title>Arabidopsis SmallRNA Database(ASD), Zhailab@SUSTech, Jixian Zhai, Zhai Jixian</title>
<link href="db.css" type="text/css" rel="stylesheet" />
</head>

<?php

$conn = mysqli_connect("localhost","root","ipf2018123456","rnaseqdb");
if (!$conn){
        die ("不能连接数据库".mysql_error()."<br/>");
};


$sql = "select libID from libinfo";
$result = mysqli_query($conn, $sql);
$row = mysqli_num_rows($result);
$maxpage = ceil($row/30);
setCookie("maxpage", ceil($row/30));

$num_rec_per_page=30; 
if (isset($_GET["page"])) { $page  = $_GET["page"]; } else { $page=1; }; 
$start_from = ($page-1) * $num_rec_per_page; 

?>

<body>

<div id="alllib">
<div id="alllibdown"><a href="data/libraryInfor.xls">download</a></div>
<div id="libtb">
	<table cellspacing="0" cellpadding="0">
		<tr>
<?php
$fields = array("Lib_Name","Title","ShortName","Adapter","Barcode5","Run","Experiment","SRAStudy","SRASample","PlatformID","BioSample","BioProject","Series","LibraryLayout","Instrument","AssayType","Genotype","Ecotype","Tissue","AGE","CenterName","INSDCCenterName","ReleaseDate","RawSize","RawCount","TrimmedReads","MappedReads","DistinctMappedReads","trsnsnoRNAMatchedReads","Distinct_trsnsnoRNAMatchedReads","GenomeMatchedReads","DistinctGenomeMatchedReads","TPMonPC","TPMonTE","TPMonTAS","TPMonPolIV","TPMonmiRNA","TPMonOtherLoci");
$widths = array(100,100,100,100,100,100,100,100,100,100,100,100,100,100,160,100,200,100,100,100,100,100,100,100,100,100,100,100,100,136,100,100,100,100,100,100,100,110);
$arrlength=count($fields);
for($x=0;$x<$arrlength;$x++)
{
	echo '<th style="background-color:#E6E6E6"><div style="width:'.$widths[$x].'px">'.$fields[$x].'</div></th>';
}

?>
		</tr>
<?php
$sql = "SELECT * FROM libinfo LIMIT ".$start_from.", ".$num_rec_per_page;
$query = mysqli_query($conn, $sql);

while($row = mysqli_fetch_array($query,MYSQLI_ASSOC)){
	echo "<tr style='background-color:#F0F0F0'>";
	for($x=0;$x<$arrlength;$x++){
		echo "<td>".$row[$fields[$x]]."</td>";
	};
	echo "</tr>";
}

?>
	</table>
</div>

<div id="libpage">
	<table id="pagectrl">
		<tr>
			<td>|<</td>
			<td><<</td>
			<?php
			for ($i=(ceil($page/20)-1)*20+1; $i<=ceil($page/20)*20; $i++){
				if ($i == $page){
					echo "<td style='background-color:#DCDCDC'>" . $i . "</td>";
				}else if ($i <= $maxpage){
					echo "<td>" . $i . "</td>";
				}; 
			}
			?>
			<td>>></td>
			<td>>|</td>
			
		</tr>
	</table>
</div>

</div>
</body>
<script>
function setCookie(name,value,d){
var Days = 2;
var exp = new Date();
exp.setTime(exp.getTime() + Days*24*60*60*1000);
document.cookie = name + "="+ escape (value) + ";expires=" + exp.toGMTString();
};

function getCookie(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for(var i = 0; i <ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
         }
         if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
         }
     }
    return "";
};

function getUrlParam(name){
	var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
	var r = window.location.search.substr(1).match(reg);
	if (r != null) return unescape(r[2]); return 1;
};

$(document).on('click', '#pagectrl td',function(){
	var p = getUrlParam('page');
	var maxp = getCookie("maxpage");
	var txt = $(this).text();
	if (txt == '|<'){
		window.location.href='alllibs.php?page=1';
	}else if (txt == '<<'){
		if ((Number(p)-20) > 0){
			window.location.href='alllibs.php?page='+(Number(p)-20);
		}else{
			window.location.href='alllibs.php?page=1';
		};
	}else if (txt == '>>'){
		if (Number(p)+20>maxp){
			window.location.href='alllibs.php?page='+maxp;
		}else{
			window.location.href='alllibs.php?page='+(Number(p)+20);
		}
	}else if (txt == '>|'){
		window.location.href='alllibs.php?page='+maxp;
	}else{
		window.location.href='alllibs.php?page='+txt;
	}
});

</script>
</html>