import MySQLdb
import sys,hashlib
import numpy as np

#configure list 
mysql_user = 'root'
mysql_pwd = 'ipf2018123456'
mysql_db = 'rnaseqdb'
mysql_libinfo_tb = 'libinfo'
mysql_conn = MySQLdb.connect(host="localhost", user=mysql_user, passwd=mysql_pwd, db=mysql_db)

lib = sys.argv[1]
try:
	advan = sys.argv[2]
except:
	advan = 0
#specific json file will add a number
json_file = 'user/'+hashlib.md5(lib).hexdigest()

def search_library(lib):
	cur = mysql_conn.cursor()
	cur.execute("SELECT * FROM %s WHERE Lib_Name = '%s'" % (mysql_libinfo_tb, lib))
	lib_query = cur.fetchone()
	cur.close()
	if lib_query:
		class_plot_txt = res2class(lib_query)
		info_txt = res2info(lib_query)
		table_dir = 'table/sRNAdb/'+lib
		table2json(table_dir, json_file, advan)
		size_plot_txt = table2size(table_dir)
		return info_txt+';;;'+class_plot_txt+';;;'+size_plot_txt
		return info_txt 
	else:
		return 'None'

def res2info(lib_query):
	reads_txt = '<tr><th onclick="datadl(\'%s\',\'rawfq\')"><span style="cursor:pointer;color:blue;text-decoration:underline">Raw Reads #</span></th><th onclick="datadl(\'%s\',\'trimfq\')"><span style="cursor:pointer;color:blue;text-decoration:underline">Trimmed Reads #</span></th><th onclick="datadl(\'%s\',\'bam\')"><span style="cursor:pointer;color:blue;text-decoration:underline">Mapped Reads #</span></th><th>t/r/sn/snoRNA Matched Reads</th><th>Genome Matched Reads*</th><th>Distinct Genome Matched Reads*</th></tr>' % (lib, lib, lib) 
	#
	dgm_reads = int(lib_query[29])-int(lib_query[31])  
	# 
	#by fengli converting format 
	rc_num = [format(int(a),',') for a in [lib_query[26], lib_query[27], lib_query[28], lib_query[29], lib_query[30], lib_query[31], lib_query[32],dgm_reads]]
	tpm_num = [format(int(a),',')  for a in [lib_query[60], lib_query[59], lib_query[58], lib_query[57], lib_query[56], lib_query[61]]]
	#
	reads_txt += '<tr><td align="center">%s</td><td align="center">%s</td><td align="center">%s</td><td align="center">%s</td><td align="center">%s</td><td align="center">%s</td></tr>' % (rc_num[0], rc_num[1], rc_num[2], rc_num[4], rc_num[6],rc_num[7])   
	tpm_txt = '<tr><th >miRNA loci</th><th>PolIV loci</th><th>TAS loci</th><th>TE loci*</th><th>PC loci*</th><th>Other loci</th></tr>'                   
	tpm_txt += '<tr><td align="center">%s</td><td align="center">%s</td><td align="center">%s</td><td align="center">%s</td><td align="center">%s</td><td align="center">%s</td></tr>' % (tpm_num[0], tpm_num[1], tpm_num[2], tpm_num[3], tpm_num[4], tpm_num[5]) 

	ncbi_link = ''
	if lib_query[1].startswith('GSM'):
		ncbi_link = 'https://www.ncbi.nlm.nih.gov/geo/query/acc.cgi?acc='+lib_query[1]
	else:
		ncbi_link = 'https://www.ncbi.nlm.nih.gov/sra/'+lib_query[1]
		
	# by fengli adding style="width:740px;"
	info_txt = '''
<table class="info" style="width:800px;border-top:2px solid #BEBEBE">
	<tr> 
		<th class="alignright">Read count:&nbsp;&nbsp;</th>
		<td>
			<div style="margin:10px 0px 5px 5px">
				<table>
					%s
				</table>
				<span style="font-size:12px;line-height:12px;"># Click for download.&nbsp;&nbsp;</br>* 
				 Does not include the data listed in the column "t/r/sn/snoRNA Matched Reads".</span>   
			</div>
		</td>
	</tr>
	<tr>
		<th class="alignright" style="border-bottom:2px solid #BEBEBE">TPM stat:&nbsp;&nbsp;</th>
		<td style="border-bottom:2px solid #BEBEBE">
			<div style="margin:10px 0px 5px 5px">
				<table style="width:680px;">
					%s
				</table>
				<span style="font-size:12px;line-height:12px">* TE and PC represent Transposable element and Protein coding, respectively.</span>
			</div>
		</td>
	</tr>
	<tr>
		<th class="alignright" style="border-top:2px solid #BEBEBE">Sample Acc:&nbsp;&nbsp;&nbsp;</th>
		<td style="border-top:2px solid #BEBEBE">%s&nbsp;&nbsp;&nbsp;&nbsp;
			<span>
				<a href="%s" target="_blank" style="font-size:12px;color:blue;text-decoration:underline;cursor:pointer;">to GEO/SRA
				</a>&nbsp;&nbsp;&nbsp;
			</span>
			<span style="font-size:12px;color:blue;text-decoration:underline;cursor:pointer" onclick="addalignment3('%s')">see in igv
			</span>
		</td>
	</tr>
	<tr><th class="alignright">Title:&nbsp;&nbsp;&nbsp;</th><td>%s</td></tr>
	<tr><th class="alignright">Run:&nbsp;&nbsp;&nbsp;</th><td>%s</td></tr>
	<tr><th class="alignright">Experiment:&nbsp;&nbsp;&nbsp;</th><td>%s</td></tr>
	<tr><th class="alignright">SRA study:&nbsp;&nbsp;&nbsp;</th><td>%s</td></tr>
	<tr><th class="alignright">SRA sample:&nbsp;&nbsp;&nbsp;</th><td>%s</td></tr>
	<tr><th class="alignright">Bio sample:&nbsp;&nbsp;&nbsp;</th><td>%s</td></tr>
	<tr><th class="alignright">Bio project:&nbsp;&nbsp;&nbsp;</th><td>%s</td></tr>
	<tr><th class="alignright">Layout:&nbsp;&nbsp;&nbsp;</th><td>%s</td></tr>
	<tr><th class="alignright">Instrument:&nbsp;&nbsp;&nbsp;</th><td>%s</td></tr>
	<tr><th class="alignright">Assaytype:&nbsp;&nbsp;&nbsp;</th><td>%s</td></tr>
	<tr><th class="alignright">Genotype:&nbsp;&nbsp;&nbsp;</th><td>%s</td></tr>
	<tr><th class="alignright">Ecotype:&nbsp;&nbsp;&nbsp;</th><td>%s</td></tr>
	<tr><th class="alignright">Tissue:&nbsp;&nbsp;&nbsp;</th><td>%s</td></tr>
	<tr><th class="alignright">AGE:&nbsp;&nbsp;&nbsp;</th><td>%s</td></tr>
	<tr><th class="alignright" style="border-bottom:2px solid #BEBEBE">ReleaseDate:&nbsp;&nbsp;&nbsp;</th><td style="border-bottom:2px solid #BEBEBE">%s</td></tr>
</table>
''' % (reads_txt, tpm_txt, lib_query[1], ncbi_link, lib_query[1], lib_query[3], lib_query[7], lib_query[8], lib_query[9], lib_query[10], lib_query[12], lib_query[13], lib_query[15], lib_query[16], lib_query[17], lib_query[18], lib_query[19], lib_query[20], lib_query[21], lib_query[24])
	return info_txt.replace('\n','')

def res2class(lib_query):
	#'miRNA', 'TAS', 'TE', 'ProteinCoding', 'Others'
	class_txt = '[%s, %s, %s, %s, %s]' % (lib_query[60], lib_query[58], lib_query[57], lib_query[56], lib_query[61])
	return class_txt

def table2json(table_dir, json_file, advan):
	csv_files = ['all.TPM.miRNA.csv', 'all.TPM.TAS.csv', 'Top100.TPM.PolIV.csv', 'Top100.TPM.TE.csv', 'Top100.TPM.PC.csv']
	if advan:
		dic_tmp = {'MIRNATPM':0, 'TASTPM':1, 'POLIVTPM':2, 'TETPM':3, 'PCTPM':4}
		advan_list = [[], [], [], [], []]
		for each in advan.strip(':').split(':'):
			logic,type,word = each.split(',')
			if word:
				advan_list[dic_tmp[type]].append(word)

	i = 0
	for file in csv_files:
		json = json_file+'.'+str(i+1)
		csv = table_dir+'/'+file
		csv = open(csv,'r').read().strip().split('\n')
		csv = [each.split(',') for each in csv]
		if advan and advan_list[i]:
			for cond in advan_list[i]:
				l,r = map(int,cond.split('-'))
				csv2 = [[csv[0][0]], [csv[1][0]], [csv[2][0]]]
				for j in range(1,len(csv[0])):
					if float(csv[2][j]) >= l and float(csv[2][j]) <= r:
						csv2[0].append(csv[0][j])
						csv2[1].append(csv[1][j])
						csv2[2].append(csv[2][j])
				csv = csv2	
		rowcsv2json(csv, json)
		i += 1
	
def rowcsv2json(tmp_list, json):
#	tmp_list = open(csv,'r').read().strip().split('\n')
#	tmp_list = [each.split(',') for each in tmp_list]
	tmp_list = np.asarray(tmp_list).T
	tmp_list = list(tmp_list)
#	l = len(tmp_list)
#	c = np.asarray(['"']*l)
#	tmp_list[...,0] = np.char.add(c,tmp_list[...,0])
#	tmp_list[...,0] = np.char.add(tmp_list[...,0],c)
	json_txt = '''
{
	"meta":{
		"view":{
			"columns":[
				%s
			],
			"flags":["default", "restorable", "restorePossibleForType"]
		}
	},
	"data":[
		%s
	]
}
'''
	tbcolname = tmp_list[0][0].strip('"')
	col_txt = '{"id" : -1, "name" : "%s", "dataTypeName" : "meta_data", "fieldName" : ":%s", "position" : 0, "renderTypeName" : "meta_data", "format" : { }, "flags" : [ "hidden" ], "width" : 240 },' % (tbcolname, tbcolname)
	for each in tmp_list[0][1:]:
		col_txt += '{"id" : -1, "name" : "%s", "dataTypeName" : "meta_data", "fieldName" : ":%s", "position" : 0, "renderTypeName" : "meta_data", "format" : { }, "flags" : [ "hidden" ], "width" : 110, "type" : "number"},' % (each, each)
	data_txt = ''
	for line in tmp_list[1:]:
		data_txt += '["%s", %s, %s],' % (line[0], line[1], line[2])
	open(json,'w').write(json_txt % (col_txt.strip(','), data_txt.strip(',')))

def table2size(table_dir):
	csv_list = ['size.all.TPM.csv', 'category.all.csv', 'size.miRNA.TPM.csv', 'category.miRNA.csv', 'size.TAS.TPM.csv', 'category.TAS.csv', 'size.PolIV.TPM.csv', 'category.PolIV.csv', 'size.TE.TPM.csv', 'category.TE.csv', 'size.PC.TPM.csv', 'category.PC.csv']
	size_txt = ''
	for each in csv_list:
		nums = open(table_dir+'/'+each, 'r').read().replace('%','').strip().strip(',').split(',')
		size_txt += '['+','.join(nums)+']@'
	return size_txt.strip('@')

if __name__ == "__main__":
	print search_library(lib)