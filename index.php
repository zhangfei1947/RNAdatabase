<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>The Arabidopsis SmallRNA Database(ASRD), Zhailab@SUSTech, Jixian Zhai, Zhai Jixian</title>
<link href="db.css" type="text/css" rel="stylesheet" />

<!--Plugin CSS file with desired skin-->
<link rel="stylesheet" href="js/ion-rangeslider-2.3.0/ion.rangeSlider.min.css"/>
<!--jQuery-->
<script src="js/jquery-3.3.1/jquery.min.js"></script>
<!--Plugin JavaScript file-->
<script src="js/ion-rangeslider-2.3.0/ion.rangeSlider.min.js"></script> 


<script type="text/javascript" src="js/momentjs-latest/moment.min.js"></script>
<script type="text/javascript" src="js/npm-daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="js/npm-daterangepicker/daterangepicker.css" />

<script src="js/unpkg.com/canvas-datagrid.js"></script>

<!--
<script src="js/igvdist/igv.min.js"></script>
-->
<script src="js/igv-2.2.14/igv.min.js"></script>


<script src="js/jquery-ui-1.12.1/jquery-ui.min.js"></script>
<link rel="stylesheet" href="js/jquery-ui-1.12.1/jquery-ui.min.css">
<link rel="stylesheet" href="js/jquery-ui-1.12.1/jquery-ui.theme.min.css">
<link rel="stylesheet" href="js/jquery-ui-1.12.1/jquery-ui.structure.min.css">


<script src="js/cdn-plot-ly/plotly-1.32.0.min.js"></script>
<script src="js/cdn-plot-ly/plotly-latest.min.js"></script>

<?php
include("trackip.php");
?>
</head>
<body>
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

setCookie("newtable", "no", 1);
setCookie("newigv", "yes", 1);
setCookie("newplot", "no", 1);
setCookie("newinfo", "no", 1);
//setCookie("searchtype", "no", 1);
var searchtype = 'none';
</script>

<div class="waiting">
	<img src="image/waf.gif" />
</div>

<div class="theadertop">
	<a href="http://sustech.edu.cn"><div class="logo"><img src="image/sustc.jpg" /></div></a>
	<div class="verline"></div>
	<a href="http://ipf.sustech.edu.cn"><div class="logo"><img src="image/ipf.jpg" /></div></a>
	<div class="verline"></div>
	<a href="http://jixianzhai.org"><div class="logo"><img src="image/jxz2.jpg" /></div></a>
</div>

<div class="theaderup">
</div>

<div class="headtitle" >
	<p class="title1">Arabidopsis Small RNA Database</p>
	<p class="title2">Search sequence and library information from 2,000+ published small RNA libraries</p>
</div>

<div class="input1bg">
	<form role="search">
	<div class="input1">
		<div class="input11" id="dataInput">
			<input type="text" id="search" placeholder="Sequence/GEO Acc/miRNA/Keyword eg:CAGCC.../GSM707678/miR158a-5p/DCL4">
		</div>
		<div class="input12" onclick="mainsearch()">
			<button class="" type="submit" disabled="" >
				<svg viewBox="0 0 24 24" width="45px" height="45px">
					<path d="M9.5,3A6.5,6.5 0 0,1 16,9.5C16,11.11 15.41,12.59 14.44,13.73L14.71,14H15.5L20.5,19L19,20.5L14,15.5V14.71L13.73,14.44C12.59,15.41 11.11,16 9.5,16A6.5,6.5 0 0,1 3,9.5A6.5,6.5 0 0,1 9.5,3M9.5,5C7,5 5,7 5,9.5C5,12 7,14 9.5,14C12,14 14,12 14,9.5C14,7 12,5 9.5,5Z">
					</path>
				</svg>
			</button>
<script>
$(document).ready(function(){
	$('#search').bind('keypress',function(event){
		if(event.keyCode == "13"){
			if ($('#search').val() != ""){
				mainsearch();
			};
		};
	});
});

function mainsearch(){
	var word = $('#search').val();
	var advance = "";
	if ($(".input2") != ""){
		$(".input2 .advanceinput").each(function(){
			advance += $(this).children(".adinput_relation").val() + ",";
			advance += $(this).children(".adinput_select").val() + ",";
			advance += $(this).children(".adinput_text").val();
			advance +=":"
		});
	};
	word = word + "&advance=" +advance;
//	alert(word);
	$(".infobg").html('<div style="width:100%;text-align:center"><img src="image/info.gif"/></div>');
	$(".infobg").load("Search.php?query="+word);
	$(".outputlabelsbg").show();
	$(".labelbg li").eq(0).addClass("active");
	$(".labelbg li").eq(1).removeClass("active");
	$(".labelbg li").eq(2).removeClass("active");
	$(".labelbg li").eq(3).removeClass("active");
	$(".outputigv").hide();
	$(".outputtable").hide();
	$(".outputplot").hide();
	$(".outputinfo").show();
//	showinfo();
	$("#TPMviolin").html("");
	$("#TPMinterval").html("");
	$("#TPMheatmap").html("");
	$("#Libclass").html("");
	$("#Sizedistri").html("");
	$("#geneTPM").html("");
	$("#showtable").html("");
	$("#tbanno").hide();
	$("#tbdl").hide();
};

function mainsearch2(word){
	$(".infobg").html('<div style="width:100%;text-align:center"><img src="image/info.gif"/></div>');
	$(".infobg").load("Search.php?query="+word);
	$(".outputlabelsbg").show();
	$(".labelbg li").eq(0).addClass("active");
	$(".labelbg li").eq(1).removeClass("active");
	$(".labelbg li").eq(2).removeClass("active");
	$(".labelbg li").eq(3).removeClass("active");
	$(".outputigv").hide();
	$(".outputtable").hide();
	$(".outputplot").hide();
	$(".outputinfo").show();
//	showinfo();
	$("#TPMviolin").html("");
	$("#TPMinterval").html("");
	$("#TPMheatmap").html("");
	$("#Libclass").html("");
	$("#Sizedistri").html("");
	$("#geneTPM").html("");
	$("#showtable").html("");
	$("#tbanno").hide();
	$("#tbdl").hide();
};

</script>
		</div>
	</div>
	</form>
</div>

<div class="underinputbg">
	<div class="underinput">
		<div class="underinput0" onclick="alllib()">
			ALL libraries
		</div>
		<div class="underinput1">
			&nbsp;&nbsp;&nbsp;
			<select  id="demo" onchange="var ss=this.options[this.options.selectedIndex].value; mainsearch2(ss); $('#search').val(ss); changeSelectType(ss);">
				<option value="" style="cursor:none" disabled selected hidden>&nbsp;Examples</option>
				<option value="TGACAGAAGAGAGTGAGCAC">&nbsp;query miRNA sequence: TGACAGAAGAGAGTGAGCAC&nbsp;</option>
				<option value="ATAACAGGTTGTTGGTGAAAAT">&nbsp;query sRNA sequence: ATAACAGGTTGTTGGTGAAAAT&nbsp;</option>
				<option value="miR158a-5p">&nbsp;query miRNA name: (ath-)miR158a-5p&nbsp;</option>
				<option value="MIMAT0000180">&nbsp;query miRBaseID: MIMAT0000180&nbsp;</option>
				<option value="GSM707679">&nbsp;query GEO Accession: GSM707679&nbsp;</option>
				<option value="AT2G14380">&nbsp;query gene/TE ID: AT2G14380&nbsp;</option>
				<option value="flower">&nbsp;search keyword: flower&nbsp;</option>
			</select>
			&nbsp;&nbsp;&nbsp;
			<a href="tutorials.php" target="_blank">Tutorials</a>
			&nbsp;&nbsp;&nbsp;
			<a href="">Please Cite</a>
		</div>
		<div class="underinput2 underinputclick">
			&nbsp;Advanced Options
		</div>
		<div class="underinput3 underinputclick">
			+
		</div>
<script>
function alllib(){
	$(".outputlabelsbg").show();
	$(".labelbg li").eq(0).removeClass("active");
	$(".labelbg li").eq(1).addClass("active");
	$(".labelbg li").eq(2).removeClass("active");
	$(".labelbg li").eq(3).removeClass("active");
	$(".outputigv").hide();
	$(".outputplot").hide();
	$(".outputinfo").hide();
	$(".outputtable").show();
//	showinfo();
	$(".outputtable").css("width","100%");
	$(".infobg").html("");
	$("#TPMviolin").html("");
	$("#TPMinterval").html("");
	$("#TPMheatmap").html("");
	$("#Libclass").html("");
	$("#Sizedistri").html("");
	$("#geneTPM").html("");
	$("#showtable").html("");
	$("#tbanno").hide();
	$("#tbdl").show();
	$("#tbdl").html("download table with more information");
	setCookie("table", "alllib", 1);
	setCookie("stype", "alllib", 1);
	loadtable('data/libraryInfor','500px');
};

$(document).ready(function(){
	$(".underinputclick").click(function(){
		if ($(".underinput3").text()=="-"){
			$(".underinput3").text("+")
		}else{
			$(".underinput3").text("-")
			if ($(".input2").html() == ""){
				var advance_line = '\
		<div class="advanceinput">\
			<select class="adinput_select" style="margin-left:80px">\
				<option value="Tissue">Tissue</option>\
				<option value="Ecotype">Ecotype</option>\
				<option value="Genotype">Genotype</option>\
				<option value="TitleKeyword">TitleKeyword</option>\
				<option value="Date">Date</option>\
				<option value="TPM">TPM</option>\
				<option value="miRNATPM">miRNA TPM</option>\
				<option value="TASTPM">TAS TPM</option>\
				<option value="PolIVTPM">PolIV TPM</option>\
				<option value="TETPM">TE TPM</option>\
				<option value="PCTPM">PC TPM</option>\
			</select>\
			<input type="text" class="adinput_text">\
			<a class="removeline">&nbsp;&nbsp;</a>\
			<a class="addline" style="display:none">&nbsp;&nbsp;</a>\
		</div>\
		<div class="advanceinput">\
			<select class="adinput_relation">\
				<option value="AND">AND</option>\
				<option value="OR">OR</option>\
				<option value="NOT">NOT</option>\
			</select>\
			<select class="adinput_select">\
				<option value="Tissue">Tissue</option>\
				<option value="Ecotype">Ecotype</option>\
				<option value="Genotype">Genotype</option>\
				<option value="TitleKeyword">TitleKeyword</option>\
				<option value="Date">Date</option>\
				<option value="TPM">TPM</option>\
				<option value="miRNATPM">miRNA TPM</option>\
				<option value="TASTPM">TAS TPM</option>\
				<option value="PolIVTPM">PolIV TPM</option>\
				<option value="TETPM">TE TPM</option>\
				<option value="PCTPM">PC TPM</option>\
			</select>\
			<input type="text" class="adinput_text">\
			<a class="removeline" >&nbsp;&nbsp;</a>\
			<a class="addline">&nbsp;&nbsp;</a>\
		</div>\
		';
				if (searchtype == 'seq' || searchtype == 'gene'){
					var advance_line = '\
			<div class="advanceinput">\
			<select class="adinput_select" style="margin-left:80px">\
				<option value="Tissue">Tissue</option>\
				<option value="Ecotype">Ecotype</option>\
				<option value="Genotype">Genotype</option>\
				<option value="TitleKeyword">TitleKeyword</option>\
				<option value="Date">Date</option>\
				<option value="TPM">TPM</option>\
			</select>\
			<input type="text" class="adinput_text">\
			<a class="removeline">&nbsp;&nbsp;</a>\
			<a class="addline" style="display:none">&nbsp;&nbsp;</a>\
			</div>\
			<div class="advanceinput">\
			<select class="adinput_relation">\
				<option value="AND">AND</option>\
				<option value="OR">OR</option>\
				<option value="NOT">NOT</option>\
			</select>\
			<select class="adinput_select">\
				<option value="Tissue">Tissue</option>\
				<option value="Ecotype">Ecotype</option>\
				<option value="Genotype">Genotype</option>\
				<option value="TitleKeyword">TitleKeyword</option>\
				<option value="Date">Date</option>\
				<option value="TPM">TPM</option>\
			</select>\
			<input type="text" class="adinput_text">\
			<a class="removeline">&nbsp;&nbsp;</a>\
			<a class="addline">&nbsp;&nbsp;</a>\
		</div>';
				}else if(searchtype == 'lib'){
					var advance_line = '\
		<div class="advanceinput">\
			<select class="adinput_select" style="margin-left:80px">\
				<option value="miRNATPM">miRNA TPM</option>\
				<option value="TASTPM">TAS TPM</option>\
				<option value="PolIVTPM">PolIV TPM</option>\
				<option value="TETPM">TE TPM</option>\
				<option value="PCTPM">PC TPM</option>\
			</select>\
			<input type="text" class="adinput_text" value="0-10000">\
			<a class="removeline">&nbsp;&nbsp;</a>\
			<a class="addline" style="display:none">&nbsp;&nbsp;</a>\
		</div>\
		<div class="advanceinput">\
			<select class="adinput_relation">\
				<option value="AND">AND</option>\
			</select>\
			<select class="adinput_select">\
				<option value="miRNATPM">miRNA TPM</option>\
				<option value="TASTPM">TAS TPM</option>\
				<option value="PolIVTPM">PolIV TPM</option>\
				<option value="TETPM">TE TPM</option>\
				<option value="PCTPM">PC TPM</option>\
			</select>\
			<input type="text" class="adinput_text" value="0-10000">\
			<a class="removeline">&nbsp;&nbsp;</a>\
			<a class="addline">&nbsp;&nbsp;</a>\
		</div>';
				}else if(searchtype == 'kw'){
					var advance_line = '\
			<div class="advanceinput">\
			<select class="adinput_select" style="margin-left:80px">\
				<option value="Tissue">Tissue</option>\
				<option value="Ecotype">Ecotype</option>\
				<option value="Genotype">Genotype</option>\
				<option value="TitleKeyword">TitleKeyword</option>\
				<option value="Date">Date</option>\
			</select>\
			<input type="text" class="adinput_text">\
			<a class="removeline">&nbsp;&nbsp;</a>\
			<a class="addline" style="display:none">&nbsp;&nbsp;</a>\
			</div>\
			<div class="advanceinput">\
			<select class="adinput_relation">\
				<option value="AND">AND</option>\
				<option value="OR">OR</option>\
				<option value="NOT">NOT</option>\
			</select>\
			<select class="adinput_select">\
				<option value="Tissue">Tissue</option>\
				<option value="Ecotype">Ecotype</option>\
				<option value="Genotype">Genotype</option>\
				<option value="TitleKeyword">TitleKeyword</option>\
				<option value="Date">Date</option>\
			</select>\
			<input type="text" class="adinput_text">\
			<a class="removeline">&nbsp;&nbsp;</a>\
			<a class="addline">&nbsp;&nbsp;</a>\
		</div>';
	};
				$(".input2").append(advance_line);
			};
		};
		$(".input2bg").slideToggle(400);
	});
});
</script>

	</div>
</div>

<div class="input2bg">
	<div class="input2"></div>
<!--
		<div class="advanceinput">
			<select class="adinput_select" style="margin-left:80px">
				<option value="">LibraryTPM</option>
				<option value="">Tissue</option>
				<option value="">Ecotype</option>
				<option value="">Genotype</option>
				<option value="">Keyword</option>
				<option value="">Date</option>
				<option value="">miRNATPM</option>
				<option value="">sRNATPM</option>
				<option value="">TASTPM</option>
				<option value="">PolIVTPM</option>
				<option value="">locusTPM</option>
			</select>
			<input type="text" class="adinput_text">
			<a class="removeline" style="display:none">&nbsp;&nbsp;</a>
			<a class="addline">&nbsp;&nbsp;</a>
		</div>
-->
	<div class="input2button">
		<button id="advancesearch" type="button" onmousedown="mouseDown(this)" onmouseup="mouseUp(this)" onclick="mainsearch()">search</button> 
	</div>
</div>

<script>

function mouseDown(e) {
  e.style="background-color:#C8C8C8";
}

function mouseUp(e) {
 e.style="background-color:#DCDCDC";
}

$(document).on('click', '.input2 .advanceinput .addline',function(){
//	$(".removeline").css("display","inline-block");
	$(".addline").css("display","none");
	var advance_line = '<div class="advanceinput">\
			<select class="adinput_relation">\
				<option value="AND">AND</option>\
				<option value="OR">OR</option>\
				<option value="NOT">NOT</option>\
			</select>\
			<select class="adinput_select">\
				<option value="Tissue">Tissue</option>\
				<option value="Ecotype">Ecotype</option>\
				<option value="Genotype">Genotype</option>\
				<option value="TitleKeyword">TitleKeyword</option>\
				<option value="Date">Date</option>\
				<option value="TPM">TPM</option>\
				<option value="miRNATPM">miRNA TPM</option>\
				<option value="TASTPM">TAS TPM</option>\
				<option value="PolIVTPM">PolIV TPM</option>\
				<option value="TETPM">TE TPM</option>\
				<option value="PCTPM">PC TPM</option>\
			</select>\
			<input type="text" class="adinput_text">\
			<a class="removeline">&nbsp;&nbsp;</a>\
			<a class="addline">&nbsp;&nbsp;</a>\
		</div>';
	if (searchtype == 'seq' || searchtype == 'gene'){
		var advance_line = '<div class="advanceinput">\
			<select class="adinput_relation">\
				<option value="AND">AND</option>\
				<option value="OR">OR</option>\
				<option value="NOT">NOT</option>\
			</select>\
			<select class="adinput_select">\
				<option value="Tissue">Tissue</option>\
				<option value="Ecotype">Ecotype</option>\
				<option value="Genotype">Genotype</option>\
				<option value="TitleKeyword">TitleKeyword</option>\
				<option value="Date">Date</option>\
				<option value="TPM">TPM</option>\
			</select>\
			<input type="text" class="adinput_text">\
			<a class="removeline">&nbsp;&nbsp;</a>\
			<a class="addline">&nbsp;&nbsp;</a>\
		</div>';
	}else if(searchtype == 'lib'){
		var advance_line = '<div class="advanceinput">\
			<select class="adinput_relation">\
				<option value="AND">AND</option>\
			</select>\
			<select class="adinput_select">\
				<option value="miRNATPM">miRNA TPM</option>\
				<option value="TASTPM">TAS TPM</option>\
				<option value="PolIVTPM">PolIV TPM</option>\
				<option value="TETPM">TE TPM</option>\
				<option value="PCTPM">PC TPM</option>\
			</select>\
			<input type="text" class="adinput_text">\
			<a class="removeline">&nbsp;&nbsp;</a>\
			<a class="addline">&nbsp;&nbsp;</a>\
		</div>';
	}else if(searchtype == 'kw'){
		var advance_line = '<div class="advanceinput">\
			<select class="adinput_relation">\
				<option value="AND">AND</option>\
				<option value="OR">OR</option>\
				<option value="NOT">NOT</option>\
			</select>\
			<select class="adinput_select">\
				<option value="Tissue">Tissue</option>\
				<option value="Ecotype">Ecotype</option>\
				<option value="Genotype">Genotype</option>\
				<option value="TitleKeyword">TitleKeyword</option>\
				<option value="Date">Date</option>\
			</select>\
			<input type="text" class="adinput_text">\
			<a class="removeline">&nbsp;&nbsp;</a>\
			<a class="addline">&nbsp;&nbsp;</a>\
		</div>';
	};
	
	$(".input2").append(advance_line);
	$(".removeline:first").css("display","inline-block");
});

$(document).on('click', '.input2 .advanceinput .removeline',function(){
//	$(".removeline").css("display","inline-block");
	$(".addline").css("display","none");
	if ($(".input2 .advanceinput").length == 2){
		$(this).parent().remove();
		$(".removeline:last").css("display","none");
		$(".addline:last").css("display","inline-block");
	}else{
		$(this).parent().remove();
		$(".addline:last").css("display","inline-block");
	};
	$(".advanceinput:first .adinput_relation").css("display","none");
	$(".advanceinput:first .adinput_select").css("margin-left","80px");
});

$(document).on('change', '.input2 .advanceinput .adinput_select',function(){
	var opt_val = this.options[this.options.selectedIndex].value;
	var opt_end = opt_val.substring(opt_val.length-3);
//	alert(opt_end);
	if (opt_end == 'TPM'){
		$(this).parent().children(".adinput_text").val("0-10000");
	}else if (opt_end == 'ate'){
		$(this).parent().children(".adinput_text").val("2001/1/1-2100/1/1");
	}else{
		$(this).parent().children(".adinput_text").val("");
	}
});

function detertype(search){
	search = search.toUpperCase()
	var ATCG = ['A','T','C','G'];
	var tip = 0;
	for(i=0;i<search.length;i++){
		if (ATCG.indexOf(search.charAt(i)) == -1){
			tip += 1;
			break;
		};
	};
	if (tip == 0){
		return 'seq';
	};
	if (search.slice(0,3) == 'MIR' || search.slice(0,4) == 'ATH-' || search.slice(0,5) == 'MIMAT'){return 'seq';};
	if (search.slice(0,3) == 'GSM' || search.slice(0,3) == 'DRX' || search.slice(0,3) == 'ERX' || search.slice(0,3) == 'SRX'){return 'lib';};
	if (search.slice(0,4) == 'AT1G' || search.slice(0,4) == 'AT2G' || search.slice(0,4) == 'AT3G' || search.slice(0,4) == 'AT4G' || search.slice(0,4) == 'AT5G' || search.slice(0,4) == 'ATCG' || search.slice(0,4) == 'ATMG'){return 'gene'};
	return 'kw';
};


$('#search').on('input propertychange',function(){
　　var search = $("#search").val();
	if (search.length>2){
		stype = detertype(search);
		if (stype != searchtype){
			if (stype == 'seq' || stype == 'gene'){
				$(".adinput_select").empty();
				$(".adinput_select").append("<option value='Tissue'>Tissue</option>");
				$(".adinput_select").append("<option value='Ecotype'>Ecotype</option>");
				$(".adinput_select").append("<option value='Genotype'>Genotype</option>");
				$(".adinput_select").append("<option value='TitleKeyword'>TitleKeyword</option>");
				$(".adinput_select").append("<option value='Date'>Date</option>");
				$(".adinput_select").append("<option value='TPM'>TPM</option>");
				searchtype = stype;
			}else if (stype == 'lib'){
				$(".adinput_select").empty();
				$(".adinput_select").append("<option value='miRNATPM'>miRNA TPM</option>");
				$(".adinput_select").append("<option value='TASTPM'>TAS TPM</option>");
				$(".adinput_select").append("<option value='PolIVTPM'>PolIV TPM</option>");
				$(".adinput_select").append("<option value='TETPM'>TE TPM</option>");
				$(".adinput_select").append("<option value='PCTPM'>PC TPM</option>");
				searchtype = stype;
			}else if (stype == 'kw'){
				$(".adinput_select").empty();
				$(".adinput_select").append("<option value='Tissue'>Tissue</option>");
				$(".adinput_select").append("<option value='Ecotype'>Ecotype</option>");
				$(".adinput_select").append("<option value='Genotype'>Genotype</option>");
				$(".adinput_select").append("<option value='TitleKeyword'>TitleKeyword</option>");
				$(".adinput_select").append("<option value='Date'>Date</option>");
				searchtype = stype;
			};
		}; 
	};
});

function changeSelectType(search){
	if (search.length>2){
		stype = detertype(search);
//		alert(search+stype);
		if (stype == 'seq' || stype == 'gene'){
			$(".adinput_relation").empty();
			$(".adinput_relation").append("<option value='AND'>AND</option>");
			$(".adinput_relation").append("<option value='OR'>OR</option>");
			$(".adinput_relation").append("<option value='NOT'>NOT</option>");
			$(".adinput_select").empty();
			$(".adinput_select").append("<option value='Tissue'>Tissue</option>");
			$(".adinput_select").append("<option value='Ecotype'>Ecotype</option>");
			$(".adinput_select").append("<option value='Genotype'>Genotype</option>");
			$(".adinput_select").append("<option value='TitleKeyword'>TitleKeyword</option>");
			$(".adinput_select").append("<option value='Date'>Date</option>");
			$(".adinput_select").append("<option value='TPM'>TPM</option>");
			searchtype = stype;
		}else if (stype == 'lib'){
			$(".adinput_relation").empty();
			$(".adinput_relation").append("<option value='AND'>AND</option>");
			$(".adinput_select").empty();
			$(".adinput_select").append("<option value='miRNATPM'>miRNA TPM</option>");
				$(".adinput_select").append("<option value='TASTPM'>TAS TPM</option>");
				$(".adinput_select").append("<option value='PolIVTPM'>PolIV TPM</option>");
				$(".adinput_select").append("<option value='TETPM'>TE TPM</option>");
				$(".adinput_select").append("<option value='PCTPM'>PC TPM</option>");
				$(".adinput_text").val("0-10000");
			searchtype = stype;
		}else if (stype == 'kw'){
			$(".adinput_relation").empty();
			$(".adinput_relation").append("<option value='AND'>AND</option>");
			$(".adinput_relation").append("<option value='OR'>OR</option>");
			$(".adinput_relation").append("<option value='NOT'>NOT</option>");
			$(".adinput_select").empty();
			$(".adinput_select").append("<option value='Tissue'>Tissue</option>");
			$(".adinput_select").append("<option value='Ecotype'>Ecotype</option>");
			$(".adinput_select").append("<option value='Genotype'>Genotype</option>");
			$(".adinput_select").append("<option value='TitleKeyword'>TitleKeyword</option>");
			$(".adinput_select").append("<option value='Date'>Date</option>");
			searchtype = stype;
		};
	};
};



</script>


<div class="outputlabelsbg">
	<div class="outputlabels">
		<ul class="labelbg" >
			<li>
			Information
			</li>
			<li>
			Data Table
			</li>
			<li class="label">
			Data Plot
			</li>
			<li class="label">
			IGV online
			</li>
		</ul>
		<div id="sizectrl" onclick="changesize()">
		</div>
		<script>
function changesize(){
	var bgimg = $("#sizectrl").css("background-image");
	if (bgimg.search("smallsize") == -1){
		$("#sizectrl").css("background-image","url(image/smallsize.png)");
		$(".theadertop").slideToggle(400);
		$(".theaderup").slideToggle(400);
		$(".headtitle").slideToggle(400);
		$(".input1bg").slideToggle(400);
		$(".underinputbg").slideToggle(400);
		$(".input2bg").slideUp(400);
	}else{
		$("#sizectrl").css("background-image","url(image/fullsize.png)");
		$(".theadertop").slideToggle(400);
		$(".theaderup").slideToggle(400);
		$(".headtitle").slideToggle(400);
		$(".input1bg").slideToggle(400);
		$(".underinputbg").slideToggle(400);
	}
}
		</script>
	</div>

</div>

<div class="outputinfo">
	<div class="infobg" id="infobg">
	</div>

</div>

<div class="outputplot">
	<div class="plotbg">
		<div class="plot" id="TPMviolin">
		</div>
	</div>
    <div class="plotbg">
		<div  id="TPMinterval">
		</div>
	</div>
	<div class="plotbg">
		<div class="plot" id="TPMheatmap">
		</div>
	</div>
	<div class="plotbg">
		<div class="plot" id="Libclass">
		</div>
	</div>
	<div class="plotbg">
		<div class="plot" id="Sizedistri">
		</div>
	</div>
	<div class="plotbg">
		<div class="plot" id="geneTPM">
		</div>
	</div>
<script>
function TPMintervalplot(){
//	alert(1234);
	var plot_tpm1 = getCookie("plot_tpm1");
	var plot_tpm2 = getCookie("plot_tpm2");
//	alert(plot_tpm);
	plot_tpm_list1 = eval(plot_tpm1);
	plot_tpm_list2 = eval(plot_tpm2);
	var trace1 = {
//		x: ['0','0-1','1-2','2-3','3-4','4-5','5-6','6-7','7-8','8-9','9-10','10-100','>100'],
//		y: [0,8,0,0,0,0,0,0,0,0,0,0,0],
		x: plot_tpm_list1,
		y: plot_tpm_list2,
		type: 'bar',
		marker: {
			color: '#69A4CF'
		}
	};

	var data = [trace1];	
	var layout = {
		title: {
			text: 'The library statistic in TPM intervals',
			font: {
				family: 'Helvetica',
				size: 18	
			},
		},
		showlegend: false,
		xaxis: {
			title: {
				text: '',
				font: {
					family: 'Helvetica',
					size: 16
				}
			},
			tickangle: -45,
			tickfont: {
				family: 'Helvetica',
				size: 12
			},
			showgrid: false
		},
		yaxis: {
			title: {
				text: 'Library Number',
				font: {
					family: 'Helvetica',
					size: 16
				}
			},
			zeroline: false,
			gridwidth: 2,
			tickfont: {
				family: 'Helvetica',
				size: 12
			},
			showgrid: false
		},
		bargap :0.3,
		margin: {
		l: 100,
		r: 10,
		b: 70,
		t: 90
		}
	};

Plotly.newPlot('TPMinterval', data, layout);
};


function TPMheatmapplot(){
	var plot_heatmap1 = getCookie("plot_heatmap1");
	var plot_heatmap2 = getCookie("plot_heatmap2");
//	alert (plot_heatmap1);
//	alert(plot_heatmap2);
	zval = eval(plot_heatmap1);
	ztxt = eval(plot_heatmap2);

	var xValues = ['1', '2', '3', '4', '5'];
	var yValues = ['4', '3', '2', '1'];
//	var zValues = [
//		[2767.89,1374.49,1128.87,657.61,627.63],
//		[579.66,569.07,508.05,443.48,440.34],
//		[432.43,418.38,413.96,409.02,393.8],
//		[393.39,381.08,372.95,362.18,361.71]
//	];
	var zValues = zval;
//	var zTexts = [
//		['GSM2787772</br></br>2767.89','GSM2787771</br></br>1374.49','SRX317205</br></br>1128.87','GSM1583119</br></br>657.61','GSM1583118</br></br>627.63'],
//		['GSM2787769</br></br>579.66','GSM1583135</br></br>569.07','GSM1583134</br></br>508.05','GSM1583074</br></br>443.48','SRX317207</br></br>440.34'],
//		['ERX1409514</br></br>432.43','GSM1583079</br></br>418.38','GSM1583115</br></br>413.96','GSM1583075</br></br>409.02','GSM1583114</br></br>393.8'],
//		['GSM1402268</br></br>393.39','GSM2522278</br></br>381.08','SRX1041175</br></br>372.95','SRX1041174</br></br>362.18','SRX2255546</br></br>361.71']
//	];
	var zTexts = ztxt;
	var colorscaleValue = [
		[0, '#3D9970'],
		[1, '#DC143C']
	];
	var data = [{
		x: xValues,
		y: yValues,
		z: zValues,
		type: 'heatmap',
		colorscale: colorscaleValue,
		showscale: true
	}];

	var layout = {
		title: 'Top20 TPM Library Heatmap',
		annotations: [],
		xaxis: {visible:false},
		yaxis: {visible:false}
};

for ( var i = 0; i < yValues.length; i++ ) {
  for ( var j = 0; j < xValues.length; j++ ) {
    var currentValue = zValues[i][j];
    if (currentValue != 0.0) {
      var textColor = 'white';
    }else{
      var textColor = 'black';
    }
    var result = {
      xref: 'x1',
      yref: 'y1',
      x: xValues[j],
      y: yValues[i],
      text: zTexts[i][j],
      font: {
        family: 'Helvetica',
        size: 6,
        color: 'rgb(50, 171, 96)'
      },
      showarrow: false,
      font: {
        color: textColor
      }
    };
    layout.annotations.push(result);
  }
}

Plotly.newPlot('TPMheatmap', data, layout);
};

function Libclassplot(){
	var class_txt = getCookie("plot_class");
//	alert(class_txt);
	class_txt = eval(class_txt);
	var data = [{
//		values: [88,16,55,47,5],
		values: class_txt,
		labels: ['miRNA', 'TAS', 'TE', 'ProteinCoding', 'Others'],
		type: 'pie'
	}];
	var layout = {
		height: 350,
		title: {
			text: "The percentage of sRNAs",
			font: {
				family: 'Helvetica',
				size: 18
			}
		},
		annotations: [],
		margin: {
        l: 180,
        r: 160,
        b: 30,
        t: 70
		}
	};
	Plotly.newPlot('Libclass', data, layout);
};

function Sizedistriplot(){
	var size_txt = getCookie("plot_size");
//	alert(size_txt);
	size_arr = size_txt.split("@");
//	$("#Sizedistri").css("height", 1000);
	var trace1 = {
		x: [18,19,20,21,22,23,24,25,26,27,28],
//		y: [10,11,13,19,14,10,21,10,9,11,12],
		y: eval(size_arr[0]),
		xaxis: 'x1',
		yaxis: 'y1',
		type: 'scatter',
		name: 'sRNA size distribution on all loci',
		showlegend: false,
	};

	var trace2 = {
		x: [18,19,20,21,22,23,24,25,26,27,28],
		y: eval(size_arr[1]),
		xaxis: 'x2',
		yaxis: 'y2',
		type: 'scatter',
		name: 'sRNA categories on all loci',
		showlegend: false,
	};
	
	var trace3 = {
		x: [18,19,20,21,22,23,24,25,26,27,28],
		y: eval(size_arr[2]),
		xaxis: 'x3',
		yaxis: 'y3',
		type: 'scatter',
		name: 'sRNA size distribution on miRNA loci',
		showlegend: false,
	};
	
	var trace4 = {
		x: [18,19,20,21,22,23,24,25,26,27,28],
		y: eval(size_arr[3]),
		xaxis: 'x4',
		yaxis: 'y4',
		type: 'scatter',
		name: 'sRNA categories on miRNA loci',
		showlegend: false,
	};
	
	var trace5 = {
		x: [18,19,20,21,22,23,24,25,26,27,28],
		y: eval(size_arr[4]),
		xaxis: 'x5',
		yaxis: 'y5',
		type: 'scatter',
		name: 'sRNA size distribution on TAS loci',
		showlegend: false,
	};
	
	var trace6 = {
		x: [18,19,20,21,22,23,24,25,26,27,28],
		y: eval(size_arr[5]),
		xaxis: 'x6',
		yaxis: 'y6',
		type: 'scatter',
		name: 'sRNA categories on TAS loci',
		showlegend: false,
	};
	
	var trace7 = {
		x: [18,19,20,21,22,23,24,25,26,27,28],
		y: eval(size_arr[6]),
		xaxis: 'x7',
		yaxis: 'y7',
		type: 'scatter',
		name: 'sRNA size distribution on PolIV loci',
		showlegend: false,
	};
	
	var trace8 = {
		x: [18,19,20,21,22,23,24,25,26,27,28],
		y: eval(size_arr[7]),
		xaxis: 'x8',
		yaxis: 'y8',
		type: 'scatter',
		name: 'sRNA categories on PolIV loci',
		showlegend: false,
	};
	
	var trace9 = {
		x: [18,19,20,21,22,23,24,25,26,27,28],
		y: eval(size_arr[8]),
		xaxis: 'x9',
		yaxis: 'y9',
		type: 'scatter',
		name: 'sRNA size distribution on TE loci',
		showlegend: false,
	};
	
	var trace10 = {
		x: [18,19,20,21,22,23,24,25,26,27,28],
		y: eval(size_arr[9]),
		xaxis: 'x10',
		yaxis: 'y10',
		type: 'scatter',
		name: 'sRNA categories on TE loci',
		showlegend: false,
	};
	
	var trace11 = {
		x: [18,19,20,21,22,23,24,25,26,27,28],
		y: eval(size_arr[10]),
		xaxis: 'x11',
		yaxis: 'y11',
		type: 'scatter',
		name: 'sRNA size distribution on PC loci',
		showlegend: false,
	};
	
	var trace12 = {
		x: [18,19,20,21,22,23,24,25,26,27,28],
		y: eval(size_arr[11]),
		xaxis: 'x12',
		yaxis: 'y12',
		type: 'scatter',
		name: 'sRNA categories on PC loci',
		showlegend: false,
	};
	

	var data = [trace1,trace2,trace3,trace4,trace5,trace6,trace7,trace8,trace9,trace10,trace11,trace12];

	var layout = {
		title: {
			text: 'The size distribution of sRNAs(TPM)',
			font: {
				family: 'Helvetica',
				size: 18
			}
		},
		height: 900,
		margin: {
        l: 30,
        r: 60,
		},
		annotations: [
			{
				xref: 'paper',
				yref: 'paper',
				x: 0.2,
				xanchor: 'center',
				y: 1,
				yanchor: 'bottom',
				text: 'genome-matched all sRNAs',
				showarrow: false
			},
			{
				xref: 'paper',
				yref: 'paper',
				x: 0.75,
				xanchor: 'center',
				y: 1,
				yanchor: 'bottom',
				text: 'genome-matched distinct sRNAs',
				showarrow: false
			},
			{
				xref: 'paper',
				yref: 'paper',
				x: 0.2,
				xanchor: 'center',
				y: 0.824,
				yanchor: 'bottom',
				text: 'all sRNAs on miRNA loci',
				showarrow: false
			},
			{
				xref: 'paper',
				yref: 'paper',
				x: 0.75,
				xanchor: 'center',
				y: 0.824,
				yanchor: 'bottom',
				text: 'distinct sRNAs on miRNA loci',
				showarrow: false
			},
			{
				xref: 'paper',
				yref: 'paper',
				x: 0.2,
				xanchor: 'center',
				y: 0.647,
				yanchor: 'bottom',
				text: 'all sRNAs on TAS loci',
				showarrow: false
			},
			{
				xref: 'paper',
				yref: 'paper',
				x: 0.75,
				xanchor: 'center',
				y: 0.647,
				yanchor: 'bottom',
				text: 'distinct sRNAs on TAS loci',
				showarrow: false
			},
			{
				xref: 'paper',
				yref: 'paper',
				x: 0.2,
				xanchor: 'center',
				y: 0.472,
				yanchor: 'bottom',
				text: 'all sRNAs on PolIV loci',
				showarrow: false
			},
			{
				xref: 'paper',
				yref: 'paper',
				x: 0.75,
				xanchor: 'center',
				y: 0.472,
				yanchor: 'bottom',
				text: 'distinct sRNAs on PolIV loci',
				showarrow: false
			},
			{
				xref: 'paper',
				yref: 'paper',
				x: 0.2,
				xanchor: 'center',
				y: 0.297,
				yanchor: 'bottom',
				text: 'all sRNAs on TE loci',
				showarrow: false
			},
			{
				xref: 'paper',
				yref: 'paper',
				x: 0.75,
				xanchor: 'center',
				y: 0.297,
				yanchor: 'bottom',
				text: 'distinct sRNAs on TE loci',
				showarrow: false
			},
			{
				xref: 'paper',
				yref: 'paper',
				x: 0.2,
				xanchor: 'center',
				y: 0.122,
				yanchor: 'bottom',
				text: 'all sRNAs on PC loci',
				showarrow: false
			},
			{
				xref: 'paper',
				yref: 'paper',
				x: 0.75,
				xanchor: 'center',
				y: 0.122,
				yanchor: 'bottom',
				text: 'distinct sRNAs on PC loci',
				showarrow: false
			},
			
		],
		 xaxis: {
			showline: false,
			autotick: false,
			showgrid: false,
			tickfont: {
				family: 'Helvetica',	
			},
		 },
		 yaxis: {
			showline: true,
			showgrid: false,
			tickfont: {
				family: 'Helvetica',	
			},			
		 },
		 xaxis2: {
			showline: false,
			autotick: false,
			showgrid: false,
			tickfont: {
				family: 'Helvetica',	
			},
		 },
		 yaxis2: {
			showline: true,
			showgrid: false,
			tickfont: {
				family: 'Helvetica',	
			},		
		 },
		 xaxis3: {
			showline: false,
			autotick: false,
			showgrid: false,
			tickfont: {
				family: 'Helvetica',	
			},
		 },
		 yaxis3: {
			showline: true,
			showgrid: false,
			tickfont: {
				family: 'Helvetica',	
			},		
		 },
		 xaxis4: {
			showline: false,
			autotick: false,
			showgrid: false,
			tickfont: {
				family: 'Helvetica',	
			},
		 },
		 yaxis4: {
			showline: true,
			showgrid: false,
			tickfont: {
				family: 'Helvetica',	
			},		
		 },
		 xaxis5: {
			showline: false,
			autotick: false,
			showgrid: false,
			tickfont: {
				family: 'Helvetica',	
			},
		 },
		 yaxis5: {
			showline: true,
			showgrid: false,
			tickfont: {
				family: 'Helvetica',	
			},		
		 },
		 xaxis6: {
			showline: false,
			autotick: false,
			showgrid: false,
			tickfont: {
				family: 'Helvetica',	
			},
		 },
		 yaxis6: {
			showline: true,
			showgrid: false,
			tickfont: {
				family: 'Helvetica',	
			},		
		 },
		 xaxis7: {
			showline: false,
			autotick: false,
			showgrid: false,
			tickfont: {
				family: 'Helvetica',	
			},
		 },
		 yaxis7: {
			showline: true,
			showgrid: false,
			tickfont: {
				family: 'Helvetica',	
			},		
		 },
		 xaxis8: {
			showline: false,
			autotick: false,
			showgrid: false,
			tickfont: {
				family: 'Helvetica',	
			},
		 },
		 yaxis8: {
			showline: true,
			showgrid: false,
			tickfont: {
				family: 'Helvetica',	
			},		
		 },
		 xaxis9: {
			showline: false,
			autotick: false,
			showgrid: false,
			tickfont: {
				family: 'Helvetica',	
			},
		 },
		 yaxis9: {
			showline: true,
			showgrid: false,
			tickfont: {
				family: 'Helvetica',	
			},		
		 },
		 xaxis10: {
			showline: false,
			autotick: false,
			showgrid: false,
			tickfont: {
				family: 'Helvetica',	
			},
		 },
		 yaxis10: {
			showline: true,
			showgrid: false,
			tickfont: {
				family: 'Helvetica',	
			},		
		 },
		 xaxis11: {
			showline: false,
			autotick: false,
			showgrid: false,
			tickfont: {
				family: 'Helvetica',	
			},
		 },
		 yaxis11: {
			showline: true,
			showgrid: false,
			tickfont: {
				family: 'Helvetica',	
			},		
		 },
		 xaxis12: {
			showline: false,
			autotick: false,
			showgrid: false,
			tickfont: {
				family: 'Helvetica',	
			},
		 },
		 yaxis12: {
			showline: true,
			showgrid: false,
			tickfont: {
				family: 'Helvetica',	
			},		
		 },
		grid: {rows: 6, columns: 2, pattern: 'independent'}
	};
	Plotly.newPlot('Sizedistri', data, layout);
};
function geneTPMplot(){
	var tmp = getCookie("plot_box").split('$');
	var plot_file = tmp[0];
	var y_range = Number(tmp[1]);
	var y_tick = y_range/5;
	if (y_tick>1){
		y_tick = Math.round(y_tick);
	};
	plot_file = "user/"+plot_file;
//	alert(plot_file);
	var htmlobj=$.ajax({url:plot_file, async:false});
	var yText = htmlobj.responseText;
//	alert(yText);
	var yData = eval(yText);
	
	var xData = ['18-nt', '19-nt', '20-nt', '21-nt', '22-nt', '23-nt', '24-nt', '25-nt', '26-nt', '27-nt', '28-nt',  'Sense', 'Antisense'];


/*
function getrandom(num , mul) {
    var value = [ ];
    for ( i = 0; i <= num; i++ ) {
        var rand = Math.random() * mul;
        value.push(rand);
    }
    return value;
};

var yData = [
   getrandom(30 ,10),
    getrandom(30, 20),
   getrandom(30, 25),
   getrandom(30, 40),
    getrandom(30, 45),
   getrandom(30, 30),
   getrandom(30, 20),
   getrandom(30, 15),
   getrandom(30, 15),
   getrandom(30, 15),
   getrandom(30, 15),
   getrandom(30, 15),
   getrandom(30, 43)
];
*/

//var colors = ['rgba(93, 164, 214, 0.5)', 'rgba(255, 144, 14, 0.5)', 'rgba(44, 160, 101, 0.5)', 'rgba(255, 65, 54, 0.5)', 'rgba(207, 114, 255, 0.5)', 'rgba(127, 96, 0, 0.5)', 'rgba(255, 140, 184, 0.5)', 'rgba(79, 90, 117, 0.5)', 'rgba(222, 223, 0, 0.5)', 'rgba(148,0,211,0.5)', 'rgba(255,165,0,0.5)', 'rgba(72,209,204,0.5)'];

var data = [];
var colors = ['#797D7F','#797D7F','#797D7F','#797D7F','#797D7F','#797D7F','#797D7F','#797D7F','#797D7F','#797D7F','#797D7F','#CA6F1E','#2471A3']   // by fengli modified colors in 20190724
for ( var i = 0; i < xData.length; i ++ ) {
  var result = {
    type: 'box',
    y: yData[i],
    name: xData[i],
    boxpoints: 'all',
    jitter: 0.5,
    whiskerwidth: 0.2,
    //fillcolor: 'cls',
    marker: {
      size: 2,
	  color: colors[i]
    },
    line: {
      width: 1
    }
  };
  data.push(result);
};

layout = {
    title: {
		text: 'The size distribution of sRNAs on gene locus',
		font:{
			family: 'Helvetica',
			size: 18
		}
	},
	height: 600,
    yaxis: {
//        autorange: true,  by fengli —— <b>XXXX</b> set Bold
		title: {
			text: 'The TPM in each library',
			font:{
				family: 'Helvetica',
				size: 16
			}
		},
		tickfont: {
			family: 'Helvetica',
			size: 14	
		},
		range: [0,y_range],
        showgrid: false,
        zeroline: true,
        dtick: y_tick,
        gridcolor: 'rgb(240, 240, 240)',
        gridwidth: 1,
        zerolinecolor: 'rgb(240, 240, 240)',
        zerolinewidth: 2
    },
	xaxis: {
		title: '',
	},
    margin: {
        l: 45,
        r: 45,
        b: 70,
        t: 90
    },
//	paper_bgcolor: 'rgb(245, 245, 245)',
//	plot_bgcolor: 'rgb(245, 245, 245)',
    showlegend: false
};

Plotly.newPlot('geneTPM', data, layout, {showSendToCloud: true});
	
};


function TPMviolinplot(){
	var violinfile = "user/"+getCookie("table")+".violin";
	Plotly.d3.csv(violinfile, function(err, rows){
		
		function unpack(rows, key) {
			return rows.map(function(row) { return row[key]; });
		}
var data = [{
  type: 'violin',
  x: unpack(rows, 'TPMs'),
  points: 'none',
  box: {
    visible: true
  },
  line: {
    color: '#DEA64A',
  },
  meanline: {
    visible: true
  },
  transforms: [{
     type: 'groupby'
    }],
  name: ''
}]

var layout = {
  title: {
    text: "The TPM distribution in libraries",
	font: {
        family: 'Helvetica',
        size: 18
	}
  },
  yaxis: {
    zeroline: false,
    tickfont: {
        family: 'Helvetica',
        size: 12
    },
    font: {
        family: 'Helvetica',
        size: 16
    }
  },
  xaxis: {
    zeroline: false,
    tickfont: {
        family: 'Helvetica',
        size: 12	
    },
    font: {
        family: 'Helvetica',
        size: 16
    }
  }
}
Plotly.plot('TPMviolin', data, layout);
}); 
};

</script>
</div>

<div class="outputtable">
	<span id="tbanno" >*All abundances shown here are normalied to TPM</span>
	<span id="tbdl" >download</span>
	<div class="showtable" id="showtable">
<script>
function loadtable(tb,tb_height){

var xhr = new XMLHttpRequest(),

grid = canvasDatagrid({
	parentNode: document.getElementById("showtable"),
	editable: false,
//	showCopy: true,
//	autoResizeColumns: true,
//	copyText: "copy",
//	multiLine: true,

});
grid.style.height = tb_height;
grid.style.width = '100%';
function parseOpenData(openData) {
    var data, schema = openData.meta.view.columns;
    data = openData.data.map(function (row) {
        var r = {};
        schema.forEach(function (column, index) {
            r[column.name] = row[index];
        });
        return r;
    });
    return {
        data: data,
        schema: schema
    };
}
xhr.addEventListener('progress', function (e) {
    grid.data = [{ status: 'Loading data: ' + e.loaded + ' of ' + (e.total || 'unknown') + ' bytes...'}];
});
xhr.addEventListener('load', function (e) {
    grid.data = [{ status: 'Loading data ' + e.loaded + '...'}];
    var openData = parseOpenData(JSON.parse(this.responseText));
    grid.schema = openData.schema;
    grid.data = openData.data;
});
xhr.open('GET', tb);
xhr.send();

grid.addEventListener('contextmenu', function (e) {
	e.items.push({
		title: 'add to igv',
		click: function(){
			if (e.cell.columnIndex == 0){
//				alert(e.cell.value);
				$(".labelbg li").eq(1).removeClass("active");
				$(".labelbg li").eq(3).addClass("active");
				$(".outputtable").hide();
				$(".outputigv").show();
				addalignment(e.cell.value);
//				return;
			};
		},
	});
});

grid.addEventListener('contextmenu', function (e) {
	e.items.push({
		title: 'NCBI/SRA link',
		click: function(){
			if (e.cell.columnIndex == 0){
//				alert(e.cell.value);
				var cell_val = e.cell.value;
				if (cell_val.indexOf("GSM")==0){
					window.open("https://www.ncbi.nlm.nih.gov/geo/query/acc.cgi?acc="+cell_val, "_blank");
				}else {
					window.open("https://www.ncbi.nlm.nih.gov/sra/"+cell_val, "_blank");
				};
				
//				addalignment(e.cell.value);
//				return;
			};
		},
	});
});


};

</script>	
	</div>
</div>

<div class="outputigv">
	<div class="igvctrlbg">
		<div class="igvctrl">
			<div class="igvct1">
				Load Track:
			</div>
			<div class="igvct2">
				<input class="autocomp1" id="libid" placeholder="LibraryID" >

<style>
       .ui-autocomplete {
            max-height: 200px;
            overflow-y: auto;
            overflow-x: hidden;
            padding-right: 20px;
        } 
</style>

<script>
$("#libid").autocomplete({
source: "autocompletelib.php",
//source: availableTags,
minLength: 0,
}).bind('focus', function () {
        $(this).autocomplete("search");
    });
</script>
				<button class="acsubmit1" onclick="addalignment2();$('#libid').val('')">submit</button>
<!--			
				<input id="libid" type="text" placeholder="LibraryID" onkeyup=" if(event.keyCode==13){addalignment2()}">
-->
			</div>

			<div class="igvct4">
				<button class="acsubmit2" onclick="zoomin();$('#mirnaid').val('');$('#tasid').val('');$('#geneid').val('');">submit</button>
				<input class="autocomp2" id="mirnaid" type="text" placeholder="miRNA" >
<script>
$("#mirnaid").autocomplete({
source: "autocompletemirna.php",
//source: availableTags,
minLength: 0,
}).bind('focus', function () {
        $(this).autocomplete("search");
    });
</script>

				<input class="autocomp2" id="tasid" type="text" placeholder="TAS" >
<script>
var availableTags = ["AT1G50055","AT2G27400","AT2G39675","AT2G39681","AT3G17185","AT3G25795","AT5G49615","AT5G57735"]
$("#tasid").autocomplete({
source: availableTags,
minLength: 0,
}).bind('focus', function () {
        $(this).autocomplete("search");
    });
</script>
				<input class="autocomp2" id="geneid" type="text" placeholder="geneID" >
<script>
$("#geneid").autocomplete({
source: "autocompletegene.php",
//source: availableTags,
minLength: 0,
});
$("#geneid").on('focus', function () {
	$(this).autocomplete("search");	
});
//$("#geneid").on('autocompleteclose', function () {
//        $(this).autocomplete("search"); 
//});
</script>

			</div>
			<div class="igvct3">
				Zoom into Locus:
			</div>
		</div>
	</div>
	<div  class="showigv" id="showigv">
<script>
function loadigv(){
	var div = $("#showigv"),
	options = {
		showNavigation: true,
		showRuler: true,
		reference: {
			"id": "TAIR10",
			"name": "TAIR10",
			"fastaURL": "data/Ath_ChrAll.fa",
			"indexURL": "data/Ath_ChrAll.fa.fai",
		},	
		tracks: [
			{
				name: "GFF",
				type: "annotation",
				format: "gff3",
				sourceType: "file",
//				url: "data/Araport11.sort.gff3.gz",
				url: "data/GFF3.gz",
//				indexURL: "data/Araport11.sort.gff3.gz.tbi",
				indexURL: "data/GFF3.gz.tbi",
				order: Number.MAX_VALUE,
				visibilityWindow: 30000000
//				displayMode: "EXPANDED"
			},
		]
	};

igv.createBrowser(div, options)
.then(function (browser) {
                   // browser is initialized and can now be used
               });

};

</script>	
	</div>
</div>
<script>
$(document).ready(function(){
	$(".labelbg li").click(function(){
		$(".labelbg li").eq(0).removeClass("active");	
		$(".labelbg li").eq(1).removeClass("active");
		$(".labelbg li").eq(2).removeClass("active");
		$(".labelbg li").eq(3).removeClass("active");
		$(this).addClass("active");
		var idx = $(".labelbg li").index(this);
		$(".outputinfo").css("display","none");
		$(".outputplot").css("display","none");
		$(".outputtable").css("display","none");
		$(".outputigv").css("display","none");
		var newtb = getCookie("newtable");
		var newigv = getCookie("newigv");
		var newplot = getCookie("newplot");
		var newinfo = getCookie("newinfo");
		var stype = getCookie("stype");
		if (idx==0){
			$(".outputinfo").show();
			if (newinfo=="yes"){
//				showinfo();
				setCookie("newinfo", "no", 1);
			};
		};
		if (idx==2){  //by fengli 20190725
			$(".outputplot").show();
			if (newplot=="yes"){
				if (stype=="seq"){
//					$("#TPMviolin").html("");  #clear this div don't work, try delete it and create a new one! -- worked
					$("#TPMviolin").remove();
					$("#TPMinterval").before('<div class="plotbg"> <div class="plot" id="TPMviolin"> </div> </div>')
					$("#TPMinterval").html("");
					$("#TPMheatmap").html("");
					$("#Libclass").html("");
					$("#Sizedistri").html("");
					$("#geneTPM").html("");
					TPMviolinplot();
					TPMintervalplot();
//					TPMheatmapplot();
				}else if(stype=="gene"){
					$("#TPMviolin").html("");
					$("#TPMinterval").html("");
					$("#TPMheatmap").html("");
					$("#Libclass").html("");
					$("#Sizedistri").html("");
					$("#geneTPM").html("");
					geneTPMplot();
				}else if(stype=="lib"){
					$("#TPMviolin").html("");
					$("#TPMinterval").html("");
					$("#TPMheatmap").html("");
					$("#Libclass").html("");
					$("#Sizedistri").html("");
					$("#geneTPM").html("");
					Libclassplot();
					Sizedistriplot();
				}else if(stype=="kw"){
					$("#TPMviolin").html("");
					$("#TPMinterval").html("");
					$("#TPMheatmap").html("");
					$("#Libclass").html("");
					$("#Sizedistri").html("");
					$("#geneTPM").html("");
				};
				setCookie("newplot", "no", 1);
			};
		};
		if (idx==1){     //by fengli 20190725
			$(".outputtable").show();
			if (newtb=="yes"){
				var tb = getCookie("table");
				tb = 'user/'+tb;
				$("#showtable").html("");
				if (stype=="seq"){
					$(".outputtable").css("width","100%")
					$("#tbanno").show();
					$("#tbdl").show();
					$("#tbdl").html("download");
					$("#showtable").css("width", "90%");
					loadtable(tb,'500px');
				}else if (stype=="gene"){
					$(".outputtable").css("width","100%")
					$("#tbanno").show();
					$("#tbdl").show();
					$("#tbdl").html("download");
					$("#showtable").css("width", "90%");
					loadtable(tb,'500px');
				}else if(stype=="lib"){
					$(".outputtable").css("width","600px");
					$(".outputtable").css("margin-left","auto");
					$(".outputtable").css("margin-right","auto");
					$("#tbanno").show();
					$("#tbdl").show();
					$("#tbdl").html("download");
					$("#showtable").css("width", "530px");
					loadtable(tb+'.1','250px');
					$("#showtable").append("</br>");
					loadtable(tb+'.2','250px');
					$("#showtable").append("</br>");
					loadtable(tb+'.3','250px');
					$("#showtable").append("</br>");
					loadtable(tb+'.4','250px');
					$("#showtable").append("</br>");
					loadtable(tb+'.5','250px');
					$("#showtable").append("</br>");
				}else if(stype=="kw"){
					$("#tbanno").hide();
					$("#tbdl").hide();
				};
				setCookie("newtable", "no", 1);
			};
		};
		if (idx==3){
			$(".outputigv").show()
/*
			if (newigv=="yes"){
				loadigv();
				setCookie("newigv", "no", 1);
			};
*/
		};

	});
});
</script>
<div class="bottombg" id="bottombg">
</div>



<script>

function addgff(){
	igv.browser.loadTrack({
				type: "annotation",
				format: "gff3",
				name: "GFF",
				sourceType: "file",
				url: "data/Araport11.sort.gff3.gz",
				indexURL: "data/Araport11.sort.gff3.gz.tbi",
				order: "Number.MAX_VALUE",
				visibilityWindow: 10000000,
				displayMode: "EXPANDED"
	});
};


function addalignment(vv){
	igv.browser.loadTrack({
		type: "alignment",
		format: "bam",
		name: vv,
		url: "bam/typed_"+vv+".updated.sorted.bam",
		indexURL: "bam/typed_"+vv+".updated.sorted.bam.bai",
		height: 100,
		minHeight: 50,
		maxHeight: 200,
		autoHeight:true,
		visibilityWindow: 50000,
		colorBy: "strand",
		order: "Number.MAX_VALUE",
	});
};

function addalignment3(vv){
	$(".labelbg li").eq(0).removeClass("active");
	$(".labelbg li").eq(3).addClass("active");
	$(".outputinfo").hide();
	$(".outputigv").show();
	addalignment(vv);
};

function addalignment2(){
	var libID = $("#libid").val();
	var bamfile = "bam/typed_" + libID + ".updated.sorted.bam";
	$.ajax(bamfile, {
		type : 'HEAD',
		async:false,
		timeout: 1000,
		success: function(){
			igv.browser.loadTrack({
				type: "alignment",
				format: "bam",
				name: libID,
				url: bamfile,
				indexURL: bamfile+".bai",
				height: 100,
				minHeight: 50,
				maxHeight: 200,
				autoHeight:true,
				visibilityWindow: 50000,
				colorBy: "strand",
				order: "Number.MAX_VALUE",
			});
		},
		error: function(){
			alert("LibraryID not exist"); 
		}
	});
};


function zoomin(){
	var vv = $("#mirnaid").val();
	if(vv==""){
		vv = $("#tasid").val();
	};
	if(vv==""){
		vv = $("#geneid").val();
	};
	$.get("find_locus.php?geneid="+vv, function(data,status){
		if (data==""){
			alert("cannot find locus");
		}else{
			igv.browser.search(data);
		};
	});
};

function zoomin2(location){
	$(".labelbg li").eq(0).removeClass("active");
	$(".labelbg li").eq(3).addClass("active");
	$(".outputinfo").hide();
	$(".outputigv").show();
	igv.browser.search(location);
};



</script>
<script>
$(document).ready(function(){
	$(".outputigv").show();
	loadigv();
	setTimeout(function(){$(".outputigv").hide()}, 500);
	setTimeout(function(){$(".waiting").hide()}, 500);
});

function datadl(aa,bb){
	$("#bottombg").load("download.php?lib="+aa+"&type="+bb);
};

$("#tbdl").click(function(){
	$("#bottombg").load("download_csv.php?tb="+getCookie("table"));
});
</script>
</body>
</html>

