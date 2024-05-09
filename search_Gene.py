import lmdb,MySQLdb
import numpy as np
import sys,hashlib
import os,random
from datetime import datetime
import re

#configure list 
mysql_user = 'root'
mysql_pwd = '**********'
mysql_db = 'rnaseqdb'
mysql_geneinfo_tb = 'geneinfo'
mysql_libinfo_tb = 'libinfo'
#read2hit_db will add the length of seq to be specific one
geneTPM_db = 'table/geneTPM.lmdb'
mysql_conn = MySQLdb.connect(host="localhost", user=mysql_user, passwd=mysql_pwd, db=mysql_db)
gene = sys.argv[1]
try:
	advan = sys.argv[2]
except:
	advan = 0
json_file = 'user/'+hashlib.md5(gene).hexdigest()
boxplot_file = json_file+'.boxplot'

def search_gene(gene):
	os.system('ls '+geneTPM_db)
	env = lmdb.open(geneTPM_db)
	txn = env.begin()
	gene_query = txn.get(gene)
	env.close()
	if gene_query:
		LibTPMs = [each.split(',') for each in gene_query.strip('/').split('/')]
		#
		cur = mysql_conn.cursor()
		for i in range(len(LibTPMs)):
			cur.execute("SELECT Lib_Name,Title,Tissue,Ecotype,Genotype,ReleaseDate FROM %s WHERE Lib_Index = '%s' limit 1" % (mysql_libinfo_tb, LibTPMs[i][0]))
			tmp = list(cur.fetchone())
			LibTPMs[i]= tmp[0:2] + LibTPMs[i][1:] + tmp[2:]
		cur.close()
#		lib_nums = len(LibTPMs)
		LibTPMs = np.asarray(LibTPMs)
		if advan:
			LibTPMs = seq_advan_filt(LibTPMs, advan)
			if len(LibTPMs) == 0:
				return 'none'
		#print LibTPMs[0]
		lib_nums = len(LibTPMs)
		cur = mysql_conn.cursor()
		cur.execute("SELECT igvlocus,strand,type,symbol,alias,locustype FROM %s WHERE geneid = '%s' limit 1" % (mysql_geneinfo_tb, gene))
		igvlocus,strand,type,symbol,alias,locustype = cur.fetchone()
		cur.close()
		#
		if symbol=='--': symbol=' '
		if alias=='--': alias=' '
		if locustype=='--': locustype=' '
		#
		info_txt = res2info(gene, igvlocus, strand, type, symbol, alias, locustype, lib_nums, LibTPMs) #TPM stat
		#
		plot_txt = res2plot(LibTPMs[...,2:15], boxplot_file)
		#
#		table_head = ['Accession','Description','18-nt','19-nt','20-nt','21-nt','22-nt','23-nt','24-nt','25-nt','26-nt','27-nt','28-nt','Sense','Antisense', 'Tissue', 'Ecotype', 'Genotype', 'ReleaseDate']
#		table_width = ['120','180','65','65','65','65','65','65','65','65','65','65','65','90','90','120','120','120','120','120']
		table_head = ['Accession','Description','TotalTPM','18-nt','19-nt','20-nt','21-nt','22-nt','23-nt','24-nt','25-nt','26-nt','27-nt','28-nt','Sense','Antisense', 'Tissue', 'Ecotype', 'Genotype', 'ReleaseDate']
		table_width = ['120','180','90','65','65','65','65','65','65','65','65','65','65','65','90','90','120','120','120','120','120']
		totaltpm = LibTPMs[...,13].astype(float)  + LibTPMs[...,14].astype(float)
		totaltpm = np.around(totaltpm, decimals=1)
		totaltpm = totaltpm.astype(str)
		LibTPMs = np.insert(LibTPMs, 2, values=totaltpm, axis=1)
		res2json(table_head, table_width, LibTPMs, json_file)
		return info_txt+';;;'+plot_txt
	else:
		return 'none'

def seq_advan_filt(LibTPMs, advan):
	#LibTPMs:Lib_Name,Title,tpm18-28,senseTPM,antisenseTPM,Tissue,Ecotype,Genotype,ReleaseDate
	#dict_type = {LibraryTPM Tissue Ecotype Genotype Keyword Date miRNATPM sRNATPM TASTPM PolIVTPM locusTPM}
	b = np.asarray(['1']*len(LibTPMs))
	LibTPMs = np.c_[LibTPMs,b]
	#print LibTPMs[0]
	#LibTPMs:Lib_Name,Title,tpm18...28,senseTPM,antisenseTPM,Tissue,Ecotype,Genotype,ReleaseDate,1
	advans = advan.strip(':').split(':')
	for cond in advans:
		logic,type,word = cond.split(',')
		if type == 'TPM':
			minv,maxv = map(float, word.replace(' ','').split('-'))
		elif type == 'DATE':
			minv,maxv = word.replace(' ','').split('-')
		else:
			word = word.replace(' ','')
#		if type in ['LibraryTPM' 'Tissue' 'Ecotype' 'Genotype' 'Keyword' 'Date']:
		if logic == 'UNDEFINED' or logic == 'AND':
			final_str_in = '1'
			final_str_out = '0'
			final_str_out_not = '1'
		elif logic == 'OR':
			final_str_in = '0'
			final_str_out = '0'
			final_str_out_not = '1'
		elif logic == 'NOT':
			final_str_in = '1'
			final_str_out = '1'
			final_str_out_not = '0'	
#		print final_str_in, final_str_out, final_str_out_not
		if type == 'TPM':
#			print type
			for line in LibTPMs:
				if line[19] == final_str_in:
					if float(line[13])+float(line[14]) < minv or float(line[13])+float(line[14]) > maxv:
						line[19] = final_str_out
					else:
						line[19] = final_str_out_not
		elif type == 'TITLEKEYWORD':
			for line in LibTPMs:
				if line[19] == final_str_in:
					m = re.search(word, line[1], re.IGNORECASE)
					if not m:
						line[19] = final_str_out
					else:
						line[19] = final_str_out_not
		elif type == 'TISSUE' or type == 'ECOTYPE' or type == 'GENOTYPE':
			idx = {'TISSUE':15, 'ECOTYPE':16, 'GENOTYPE':17}[type]
			for line in LibTPMs:
				if line[19] == final_str_in:
					if line[idx].upper() != word:
						line[19] = final_str_out
					else:
						line[19] = final_str_out_not
		elif type == 'DATE':
			minv = datetime.strptime(minv, '%Y/%m/%d')
			maxv = datetime.strptime(maxv, '%Y/%m/%d')
			for line in LibTPMs:
				if line[19] == final_str_in:
					if line[18] != '-':
						releasedate = datetime.strptime(line[18], '%Y/%m/%d')
						if releasedate < minv or releasedate > maxv:
							line[19] = final_str_out
						else:
							line[19] = final_str_out_not

	LibTPMs = [each for each in LibTPMs if each[19]=='1']
	LibTPMs = np.delete(LibTPMs, [19], axis=1)
	return LibTPMs


def res2json(table_head, table_width, LibTPMs, json_file):
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
	col_txt = ''
	i = 0
	for each in table_head:
		typenumber = ''
		try:
			float(['lib','desc','1','18','19','20','21','22','23','24','25','26','27','28','29','30','Tissue','Ecotype','Genotype','ReleaseDate'][i])
			typenumber = ', "type" : "number"'
		except:
			pass
		col_txt += '{"id" : -1, "name" : "%s", "dataTypeName" : "meta_data", "fieldName" : ":%s", "position" : 0, "renderTypeName" : "meta_data", "format" : { }, "flags" : [ "hidden" ], "width" : %s %s},' % (each,each,table_width[i],typenumber)
		i += 1
	data_txt = ''
	for line in LibTPMs:
		data_txt += '["' + '","'.join(line[0:2]) + '",' + ','.join(line[2:15]) + ',"' + '","'.join(line[15:]) + '"],'
	open(json_file,'w').write(json_txt % (col_txt.strip(','), data_txt.strip(',')))
	
def res2info(gene, igvlocus, strand, type, symbol, alias, locustype, lib_nums, LibTPMs):
    #  by fengli adding TAIR link of gene
	allTPMs = LibTPMs[...,13].astype('float64') + LibTPMs[...,14].astype('float64')
	maxTPM = format(int(allTPMs.max()),',')
	minTPM = format(int(allTPMs.min()),',')
	medianTPM = format(int(np.median(allTPMs)),',')
	meanTPM = format(int(np.mean(allTPMs)),',')
	varTPM = format(int(np.var(allTPMs)),',')  #by fengli
	stdTPM = format(int(np.std(allTPMs)),',')  #by fengli
	info_txt = '''
<table class="info" style="width:800px;">
	<tr>
		<th class="alignright" style="border-top:2px solid #BEBEBE">found in:&nbsp;&nbsp;&nbsp;</th>
		<td style="border-top:2px solid #BEBEBE;font-weight: 800;color:#DF0101">%s<a style="font-weight:1;">&nbsp;libraries</a></td>
	</tr>
	<tr>
        <th class="alignright">TPM stat:&nbsp;&nbsp;&nbsp;</th>
        <td>
            <div style="margin:10px 0px 10px 10px">
            <table style="width:670px;">
                <tr><th align="center">Maximum</th><th align="center">Median</th><th align="center">Mean</th><th align="center">Minimum</th></tr>      
                <tr><td align="center">%s</td><td align="center">%s</td><td align="center">%s</td><td align="center">%s</td></tr>
            </table>
            </div>
        </td>
    </tr>
    <tr>
		<th class="alignright" style="width:130px;border-top:2px solid #BEBEBE">geneID:&nbsp;&nbsp;&nbsp;</th>
		<td style="border-top:2px solid #BEBEBE">%s&nbsp;&nbsp;&nbsp;&nbsp;
			<span>
				<a href="https://www.arabidopsis.org/servlets/TairObject?type=locus&name=%s" target="_blank" style="font-size:12px;color:blue;text-decoration:underline;cursor:pointer;">to TAIR
				</a>&nbsp;&nbsp;&nbsp;
			</span>
		</td>
	</tr>
	<tr>
		<th class="alignright">locus:&nbsp;&nbsp;&nbsp;</th>
		<td>%s&nbsp;&nbsp;&nbsp;
			<span style="font-size:12px;color:blue;text-decoration:underline;cursor:pointer" onclick="zoomin2('%s')">see in igv</span>
		</td>
	</tr>
	<tr><th class="alignright">strand:&nbsp;&nbsp;&nbsp;</th><td>%s</td></tr>
	<tr><th class="alignright">symbol:&nbsp;&nbsp;&nbsp;</th><td>%s</td></tr>
	<tr><th class="alignright">alias:&nbsp;&nbsp;&nbsp;</th><td>%s</td></tr>
    <tr><th class="alignright">locus type:&nbsp;&nbsp;&nbsp;</th><td>%s</td></tr>
</table>
''' % (format(lib_nums,','), maxTPM, medianTPM, meanTPM, minTPM, gene, gene, igvlocus, igvlocus, strand, symbol, alias, locustype)
	return info_txt.replace('\n','')

def res2plot(results, boxplot_file):
	plot_txt = '['
#	results = np.asarray(results[1:])
	#print results[0]
	l = len(results)
	s = 0
	if l > 250:
		s = 200
	y_range = 0
	for i in range(13):
		tmp = results[...,i]
		if i > 11:
			y_range = max(y_range, int(np.percentile(map(float,tmp), 90)*2))
		if s:
			tmp = random.sample(tmp, s)
		plot_txt += '['+ ','.join(tmp)+'],'
	plot_txt = plot_txt.strip(',')+']'
	open(boxplot_file,'w').write(plot_txt)
	return str(y_range)

if __name__ == "__main__":
	print search_gene(gene)
	
