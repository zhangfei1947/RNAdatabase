<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">


<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>


<title>Arabidopsis Small RNA Database(ASRD), Zhailab@SUSTech, Jixian Zhai, Zhai Jixian</title>
<link href="db.css" type="text/css" rel="stylesheet" />
</head>
<body>
<table id="doc"  cellspacing="0" cellpadding="0">
	<tr>
		<td id="docleft">
		    <p class="cont cont1">Table Of Contents</p>
			<p class="cont cont2">An overview of ASRD</p>
			<p class="cont cont2">Data collection</p>
			<p class="cont cont2">Data re-process and analysis</p>
			<p class="cont cont2" style="text-indent:70px;font-size:14px">Data re-processing</p>
			<p class="cont cont2" style="text-indent:70px;font-size:14px">Data analysis</p>
			<p class="cont cont2">Database construction</p>
			<p class="cont cont2" style="text-indent:70px;font-size:14px">Search sRNA and miRNA</p>
			<p class="cont cont2" style="text-indent:70px;font-size:14px">Search library</p>
			<p class="cont cont2" style="text-indent:70px;font-size:14px">Search gene</p>
			<p class="cont cont2" style="text-indent:70px;font-size:14px">IGV visualization</p>
			<p class="cont cont2">References</p>
		</td>
		
		<td id="docright">
			<p class="tuto1" id="ASRD">An overview of ASRD</p>
			<p class="tuto2" style="margin:0px 100px 0px 0px">
</br>Welcome to Arabidopsis SmallRNA Database(ASRD), an online database for exploring 2,000+ published Arabidopsis small RNA libraries.</br></br></p>
            <p class="tuto2" style="margin:0px 100px 0px 0px">
ASRD, a web-based interface, contributes to search, filter, visualize, browse, and download the sRNA-seq data, is described in Figure 1.</p>
			<p class="tuto2" style="margin:0px 100px 0px 40px">
● ASRD in totally integrates 2,357,941,025 genome matched sRNAs representing 254,678,199 unique sRNAs in 2024 Arabidopsis sRNA-seq libraries, collected from Gene Expression Omnibus (GEO)(Clough and Barrett, 2016) and Sequence Read Archive (SRA)(Leinonen et al., 2011) databases. </br>       
● ASRD we developed has a unified pipeline to process and analyze sRNA-seq data. </br>
● ASRD supports search input such as read sequence, miRNA ID, miRNA sequence, miRNA Name, gene ID, library Accession Number, and library-related keyword, and can return the basic description, expression levels, and has a built-in IGV-web interface (Figure 2).</br>
● ASRD is a free, web-accessible, and user-friendly database that supports direct query of over 2,000 Arabidopsis sRNA-seq libraries.</br></p>
            <p class="tuto2">
ASRD mainly contains three parts, including data collection, data process and database construction, as shown in the folowing Figure1,</br></br></p>
            <p align="center" style="margin:0px 100px 0px 0px"><img src="image/20180730111.jpg" style="width:700px;height:400px"></p>
			<p></br></br><img src="image/line.jpg" style="width:92%;height:1%"></br></br></p>
			<p class="tuto1" id="Datacollection">Data collection</p>
			<p class="tuto2" style="margin:0px 100px 0px 0px"></br>
We collected Arabidopsis sRNA datasets published till July, 2019 from GEO and SRA database using keywords — ”((sRNA) OR (sRNAs) OR siRNA OR smallRNA OR smallRNAs OR miRNA OR sRNA OR sRNAs OR siRNAs OR miRNAs) and Arabidopsis”, and obtained a total of 2024 libraries from Illumina NGS system with raw sequencing data. Because of the GEO and SRA database shared some libraries, we gave priority to name library with GEO accession number.</br></br></p>
            <p class="tuto1" id="Datare-processandanalysis">Data re-process and analysis</p>
			<p class="tuto2">
			<p class="tuto1" id="Datare-processing">Data re-processing</p>
			<p class="tuto2">
Figure 1 describes the data re-process and analysis pipeline. The raw data in sra format are downloaded, processed, and analyzed by in-house scripts. We use fastq-dump from SRA Toolkit (2.8.2; https://www.ncbi.nlm.nih.gov/books/NBK158900/) to convert raw data from sra to fastq format. The 3’ adapter sequence of the library is predicted by DNApi if there is no related information provided (Tsuji and Weng, 2016) and trimmed through Cutadapt v1.16 (Martin, 2011), and the 5’ barcode also chopped if it exists. We then process the remaining 18-28 nt reads to fasta file in tag_count format. To characterized sRNA features, we map these reads to the Arabidopsis reference genome (TAIR10) using Bowtie v1.2.1.1(Langmead et al., 2009), allowing zero mismatches (-v 0) and multiple hits (-a). We use t/r/sn/snoRNAs annotated in Araport11(Cheng et al., 2017) to flag corresponding types of sRNAs in each bam. Finally, our database approximately involves 2,357,941,025 genome-matched sRNAs representing 254,678,199 distinct sRNAs.</p>
			<p class="tuto1" id="Dataanalysis">Data analysis</p>
			<p class="tuto2">
The 426 mature miRNA annotations are from miRbase (version 22.1) (Kozomara et al., 2019), and eight TAS loci: TAS1a, TAS1b, TAS1c, TAS2, TAS3, TAS3b, TAS3c and TAS4 for trans-acting siRNAs calculation. The 69,810 gene IDs obtained from Araport11 annotation (Cheng et al., 2017). The list of 7,632 P4-siRNA loci was previously described (Zhai et al., 2015). The 27,655 protein-coding genes and 3,901 transposon elements annotated in Araport11 are used to calculate the abundance of Protein-coding gene-generating siRNAs (PC-siRNAs) and Transposon element-generating siRNAs (TE-siRNAs), respectively. The normalized abundance of sRNAs in each library is calculated as transcripts per million (TPM) by the count of genome-matched reads excluding t/r/sn/snoRNA-derived reads. The TPM of sRNAs on a given locus is the sum of genome hits-normalized TPM for all mapped reads on that locus. The flowchart of the data collection, processing, and database functions illustrated in Figure 1.</p>
			</br></p>
      		<p class="tuto1" id="Databaseconstruction">Database construction</p>
			<p class="tuto2">
ASRD contributes to search, visualize, browse, and download the sRNA-seq data, as Figure1 described. ASRD operates flexible that only need to input one read, miRNA, gene, library Accession Number, and library-related keyword, and returns the fundamental description, exhibit and visualize expression levels, and support IGV-web (https://igvteam.github.io/igv-webapp/) interfaced genomic alignment. We show the overflow in Figure2.</br></br></p>
			<p align="center" style="margin:0px 100px 0px 0px"><img src="image/20180730112.jpg" style="width:1000px;height:1000px;"></br></br></br></p>
			<p class="tuto1" id="SearchsRNAandmiRNA">Search sRNA and miRNA</p>
			<p class="tuto2">
For searching one sRNA or miRNA, ASRD permits one read sequence, miRNA Name, miRbase miRNA ID as input, and quickly determines whether it is a mature miRNA, returns the statistics of expression, genome hits, and genome annotation. Here, the query formiR158a-5p as an example, the expression part shows the maximum, median, mean and minimum value of TPM levels in all detected libraries (Figure 3A). The violin plot shows the distribution of expressions, and bar diagram displays the number of libraries corresponding different TPM intervals (Figure 3B). For facilitate comparison, one table the user can download shows the expression levels adjusted different sequencing depths, including the raw count, TPM, TP5M, and TP10M levels. More convenient, the advanced options can be used to filter the results by tissue, ecotype, genotype, release date, TPM level, and library-related keyword (Figure 3C).</p>
            <p align="center" style="margin:0px 100px 0px 0px"><img src="image/20180730113.jpg" style="width:1000px;height:1050px"></br></br></br></p>
			<p class="tuto1" id="Searchlibrary">Search library</p>
			<p class="tuto2">
This function supports to the query of one Library ID or keyword. For one library, apart from related information, ASRD statistics the read count and TPM level of raw, trimmed, mapped, t/r/sn/snoRNA matched, genome matched, and distinct genome matched reads, also the TPM levels of sRNAs generating from miRNA, Pol-IV, ta-siRNA-generating loci (TAS), transposable element (TE), PC, and Other loci. The files of raw, trimmed, and mapped reads can be download (Figure 4A). To more intuitively, a pie diagram used to exhibits the percentage of sRNAs on miRNA, Pol-IV, TAS, TE, PC, and Other loci. The line diagrams further describe the size distribution of all and distinct genome matched sRNAs on above mentioned five sRNA-generating loci (Figure 4B). More exploration, ASRD also give the read count and TPM level of sRNAs from a single miRNA and TAS locus, and the Pol-IV, TE and PC locus(with Top 100 TPM level) (Figure 4C).</br></br></br></p>	
            <p align="center"><img src="image/20180730114.jpg" style="width:900px;height:1160px"></br></br></br></p>
			<p class="tuto1" id="Searchgene">Search gene</p>
			<p class="tuto2">
ASRD allows searching one of all 69,810 genes annotated in Araport11 database (Figure 4A). For visualization result, scatter and box plots describe the global TPM levels of 18-28nt sRNAs from the queried gene locus (Figure 4D). Here, one table exhibits the size distribution of 18-28 nt sRNAs, sense and antisense sRNAs on gene locus in each library. As to the capabilities of miRNA query, this table also supports advanced filter and can be download (Figure 4E).</br></p>
            <p class="tuto1" id="IGVvisualization">IGV visualization</p>
			<p class="tuto2">
ASRD integrates an online IGV interface to browse and compare the genome matched sRNAs in one or more libraries. The links of online IGV interface are added into the results of each type of query. It supports to browse the sRNA abundance on one gene, TAS, miRNA locus, or genomic region. Also, the IGV online can add multiple libraries convenient for comparison.</br></p>
			<p align="center"><img src="image/20180730115.jpg" style="width:1000px;height:500px"></br></br></br></p>
            <p class="tuto1" id="References">References</p>
			<p class="tuto2" style="margin:20px 100px 0px 0px">
Allen E, Xie Z, Gustafson AM, Carrington JC (2005) microRNA-directed phasing during trans-acting siRNA biogenesis in plants. Cell 121: 207-221</br>
Axtell MJ (2013) Classification and comparison of small RNAs from plants. Annu Rev Plant Biol 64: 137-159</br>
Axtell MJ, Jan C, Rajagopalan R, Bartel DP (2006) A two-hit trigger for siRNA biogenesis in plants. Cell 127: 565-577</br>
Baulcombe D (2004) RNA silencing in plants. Nature 431: 356-363</br>
Bologna NG, Voinnet O (2014) The diversity, biogenesis, and activities of endogenous silencing small RNAs in Arabidopsis. Annu Rev Plant Biol 65: 473-503</br>
Borges F, Martienssen RA (2015) The expanding world of small RNAs in plants. Nat Rev Mol Cell Biol 16: 727-741</br>
Carthew RW, Sontheimer EJ (2009) Origins and Mechanisms of miRNAs and siRNAs. Cell 136: 642-655</br>
Chen X (2009) Small RNAs and their roles in plant development. Annu Rev Cell Dev Biol 25: 21-44</br>
Cheng CY, Krishnakumar V, Chan AP, Thibaud-Nissen F, Schobel S, Town CD (2017)</br>
 Araport11: a complete reannotation of the Arabidopsis thaliana reference genome. Plant J 89: 789-804</br>
Clough E, Barrett T (2016) The Gene Expression Omnibus Database. Methods Mol Biol 1418: 93-110</br>
Cuerda-Gil D, Slotkin RK (2016) Non-canonical RNA-directed DNA methylation. Nat Plants 2: 16163</br>
Fei Q, Xia R, Meyers BC (2013) Phased, secondary, small interfering RNAs in posttranscriptional regulatory networks. Plant Cell 25: 2400-2415</br>
Kent WJ, Sugnet CW, Furey TS, Roskin KM, Pringle TH, Zahler AM, Haussler D (2002) The human genome browser at UCSC. Genome Res 12: 996-1006</br>
Kozomara A, Birgaoanu M, Griffiths-Jones S (2019) miRBase: from microRNA sequences to function. Nucleic Acids Res 47: D155-D162</br>
Langmead B, Trapnell C, Pop M, Salzberg SL (2009) Ultrafast and memory-efficient alignment of short DNA sequences to the human genome. Genome Biol 10: R25</br>
Leinonen R, Sugawara H, Shumway M, International Nucleotide Sequence Database C (2011) The sequence read archive. Nucleic Acids Res 39: D19-21</br>
Lister R, O'Malley RC, Tonti-Filippini J, Gregory BD, Berry CC, Millar AH, Ecker JR (2008) Highly integrated single-base resolution maps of the epigenome in Arabidopsis. Cell 133: 523-536</br>
Martin M (2011) Cutadapt removes adapter sequences from high-throughput sequencing reads. 2011 17: 3</br>
Matzke MA, Mosher RA (2014) RNA-directed DNA methylation: an epigenetic pathway of increasing complexity. Nat Rev Genet 15: 394-408</br>
Meyers BC, Axtell MJ (2019) MicroRNAs in Plants: Key Findings from the Early Years. Plant Cell 31: 1206-1207</br>
Nakano M, Nobuta K, Vemaraju K, Tej SS, Skogen JW, Meyers BC (2006) Plant MPSS databases: signature-based transcriptional resources for analyses of mRNA and small RNA. Nucleic Acids Res 34: D731-735</br>
Nelson ADL, Haug-Baltzell AK, Davey S, Gregory BD, Lyons E (2018) EPIC-CoGe: managing and analyzing genomic data. Bioinformatics 34: 2651-2653</br>
Robinson JT, Thorvaldsdottir H, Winckler W, Guttman M, Lander ES, Getz G, Mesirov JP (2011) Integrative genomics viewer. Nat Biotechnol 29: 24-26</br>
Ruiz-Ferrer V, Voinnet O (2009) Roles of plant small RNAs in biotic stress responses. Annu Rev Plant Biol 60: 485-510</br>
Tsuji J, Weng Z (2016) DNApi: A De Novo Adapter Prediction Algorithm for Small RNA Sequencing Data. PLoS One 11: e0164228</br>
Voinnet O (2009) Origin, biogenesis, and activity of plant microRNAs. Cell 136: 669-687</br>
Zhai J, Bischof S, Wang H, Feng S, Lee TF, Teng C, Chen X, Park SY, Liu L, Gallego-Bartolome J, Liu W, Henderson IR, Meyers BC, Ausin I, Jacobsen SE (2015) A One Precursor One siRNA Model for Pol IV-Dependent siRNA Biogenesis. Cell 163: 445-455</br>
			</p>
		</td>
	</tr>
</table>
<script>
$(function(){  
	$(".cont").click(function(){ 
		window.location.hash = "#"+$(this).html().replace(/ /g,'');
	});  
});  
</script>
</body>
</html>