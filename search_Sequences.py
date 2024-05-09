import lmdb,MySQLdb
import numpy as np
import sys,hashlib
from seqCompress import *
import os,re
from datetime import datetime

#configure list 
mysql_user = 'root'
mysql_pwd = '**********'
mysql_db = 'rnaseqdb'
mysql_miRNA_tb = 'miRNA'
mysql_libinfo_tb = 'libinfo'
readinfo_db = 'table/ReadInfo.lmdb'
mysql_conn = MySQLdb.connect(host="localhost", user=mysql_user, passwd=mysql_pwd, db=mysql_db)

seq = sys.argv[1]
try:
	advan = sys.argv[2]
except:
	advan = 0
json_file = 'user/'+hashlib.md5(seq).hexdigest()
violin_file = json_file + '.violin'


def search_sequence(seq):
	if len(seq) < 18 or len(seq) > 28:
		return 'Sorry, the length of your sequence is not in 18-28nt !'	  #by fengli
	#decide if it is miRNA
	cur = mysql_conn.cursor()
	cur.execute("SELECT miRNAName,miRBaseID,GeneID,Coordinate,Strand FROM %s WHERE Sequence = '%s'" % (mysql_miRNA_tb, seq))
	miRNA_query = cur.fetchall()
	cur.close()
#if len(miRNA_query) > 0: sequence is miRNA, next to decide it represents one/multiple miRNA, get all information
#if not miRNA_query: sequence is not miRNA, next to decide if it has been sequenced before
	os.system('ls '+readinfo_db+str(len(seq)))
	env = lmdb.open(readinfo_db+str(len(seq)))
	txn = env.begin()
	readhit_query = txn.get(seqzip(seq))
#	print readhit_query
	env.close()
	if readhit_query:
		#sequence in our big list,next query genome hits and each library information
		hit_txt,exp_txt = readhit_query.split('$')
		#LibTPMs:Lib_Name, Title, Count, TPM, TP5M, TP10M, Tissue, Ecotype, Genotype, ReleaseDate, final
		LibTPMs = [('0-'+each+'-0-0-0-0-0-0-1').split('-') for each in exp_txt.strip('/').split('/')]
		env.close()
		cur = mysql_conn.cursor()
		for i in range(len(LibTPMs)):
			cur.execute("SELECT Lib_Name,Title,Tissue,Ecotype,Genotype,ReleaseDate FROM %s WHERE Lib_Index = '%s' limit 1" % (mysql_libinfo_tb, LibTPMs[i][1]))
			libinfo_query = cur.fetchone()
			LibTPMs[i][0] = '"'+libinfo_query[0]+'"'
			LibTPMs[i][1] = '"'+libinfo_query[1].replace('"','')+'"'
			LibTPMs[i][6] = '"'+libinfo_query[2].replace('"','')+'"'
			LibTPMs[i][7] = '"'+libinfo_query[3].replace('"','')+'"'
			LibTPMs[i][8] = '"'+libinfo_query[4].replace('"','')+'"'
			LibTPMs[i][9] = '"'+libinfo_query[5].replace('"','')+'"'
		#filter results use advance options
		if advan:
#			print 'advance'
			LibTPMs = seq_advan_filt(LibTPMs, advan)
			if len(LibTPMs) == 0:
				return 'none'
		cur.close()
		#combine to one array to get a table - for Table
		LibTPMs = np.array(LibTPMs)
		nums = LibTPMs[...,2:6]
		nums = nums.astype(float)
		nums[...,2] = np.around(nums[...,1]*5, decimals=1)
		nums[...,3] = np.around(nums[...,1]*10, decimals=1)
		nums[...,1] = np.around(nums[...,1], decimals=1)
		LibTPMs = np.delete(LibTPMs, [10], axis=1)
		l = len(LibTPMs)
#		c = np.asarray(['"']*l)
#		for i in [6,7,8,9]:
#			LibTPMs[...,i] = np.char.add(np.char.add(c,LibTPMs[...,i]),c)
		LibTPMs[...,2:6] = nums.astype('str')
		table_head = ['Accession','Description','Read_Count','TPM','TP5M','TP10M','Tissue','Ecotype','Genotype','ReleaseDate']
		table_width= ['120','200','110','80','80','80','110','110','110','110']
		#array2json - generate table json file
		array2json(table_head,table_width,LibTPMs,json_file)
		lib_nums = len(LibTPMs)
		plot_txt = ''
		maxTPM = '-'
		minTPM = '-'
		medianTPM = '-'
		meanTPM = '-'
		varTPM = '-'
		stdTPM = '-'
		plot_txt = ';;;-;;;-;;;-'
		if lib_nums > 1:
			TPMs = np.array(nums[...,1])
			#array2plot - generate plot txt data
			maxTPM, minTPM, medianTPM, meanTPM, varTPM, stdTPM, plot_txt = array2plot(LibTPMs, TPMs, violin_file)  #by fengli 
		info_txt = res2info(seq, miRNA_query, hit_txt, lib_nums, maxTPM, minTPM, medianTPM, meanTPM, varTPM, stdTPM)  #by fengli
		return info_txt + plot_txt
	else:
		#sequence has not in our big list, no result, query finish.
		return 'none'

def seq_advan_filt(LibTPMs, advan):
	#LibTPMs:Lib_Name, Title, Count, TPM, TP5M, TP10M, Tissue, Ecotype, Genotyoe, ReleaseDate, final
#	dict_type = {LibraryTPM Tissue Ecotype Genotype Keyword Date miRNATPM sRNATPM TASTPM PolIVTPM locusTPM}
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
				if line[10] == final_str_in:
					if float(line[3]) < minv or float(line[3]) > maxv:
						line[10] = final_str_out
					else:
						line[10] = final_str_out_not
		elif type == 'TITLEKEYWORD':
			for line in LibTPMs:
				if line[10] == final_str_in:
					m = re.search(word, line[1], re.IGNORECASE)
					if not m:
						line[10] = final_str_out
					else:
						line[10] = final_str_out_not
		elif type == 'TISSUE' or type == 'ECOTYPE' or type == 'GENOTYPE':
			idx = {'TISSUE':6, 'ECOTYPE':7, 'GENOTYPE':8}[type]
			for line in LibTPMs:
				if line[10] == final_str_in:
					if line[idx].upper().strip('"') != word:
						line[10] = final_str_out
					else:
						line[10] = final_str_out_not
		elif type == 'DATE':
			minv = datetime.strptime(minv, '%Y/%m/%d')
			maxv = datetime.strptime(maxv, '%Y/%m/%d')
			for line in LibTPMs:
				if line[10] == final_str_in:
					if line[9] != '-':
						releasedate = datetime.strptime(line[9], '"%Y/%m/%d"')
						if releasedate < minv or releasedate > maxv:
							line[10] = final_str_out
						else:
							line[10] = final_str_out_not

	return [each for each in LibTPMs if each[10]=='1' ]


def res2info(seq, miRNA_query, hit_txt, lib_nums, maxTPM, minTPM, medianTPM, meanTPM, varTPM, stdTPM):
	short2type={"alc":"antisense_long_noncoding_rna","atr":"antisense_rna","lnc":"long_noncoding_rna","pim":"miRNA_primary_transcript","ntr":"novel_transcribed_region","otr":"other_rna","prt":"pre_trna","pc":"protein_coding","rbr":"ribosomal_rna","snr":"small_nuclear_rna","sno":"small_nucleolar_rna","mtm":"mature_miRNA","pse":"pseudogene","te":"transposable_element","teg":"transposable_element_gene"}     # by fengli
	hit_info = '<tr><th>Chr</th><th>Start</th><th>End</th><th>Strand</th><th>Annotation</th><th>Browse</th></tr>\n'
	hit_txt = hit_txt.replace("ath-","").replace("-5p",":5p").replace("-3p",":3p")  # by fengli
	hits = hit_txt.split(';')             
	hit_num = len(hits)
	for each in hits:
		each = each.split('-')
		if len(each) == 4:
			each.append('-')
		#  by fengli strand using +/-
		if each[3] == '16':
			each_strand = '-'  
		else:
			each_strand = '+'   
		#  by fengli modify
		type = each[4].replace(':','-')                                
		for k in short2type:                                           
			type = type.replace('('+k+')','('+short2type[k]+')')       
		type = '); '.join(type.replace('_',' ').split(')')[:-1])
		
		if type == '':
			type += '/'
		else:
			type +=')'  
		#
		hit_info += '<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td style="word-break:break-all;">%s</td><td style="font-size:12px;color:blue;text-decoration:underline;cursor:pointer" onclick="zoomin2(\'%s\')">see in igv</td></tr>\n' % (each[0],each[1],each[2],each_strand,type,each[0]+':'+each[1]+'-'+each[2]) # by fengi for each _strand
		#
	miRNA_info = '\n<tr><th class="alignright" style="border-bottom:2px solid #BEBEBE">miRNA:&nbsp;&nbsp;&nbsp;</th><td style="border-bottom:2px solid #BEBEBE">not in known miRNA list</td></tr>\n'
	if miRNA_query:
		miRNA_info = ''
		for each in miRNA_query:
			if each[4] == 'Reverse':
				each_strand = '-'
			else:
				each_strand = '+'
			if each == miRNA_query[-1]:
				miRNA_info += '\n<tr><th class="alignright" style="border-bottom:2px solid #BEBEBE">miRNA:&nbsp;&nbsp;&nbsp;</th><td style="border-bottom:2px solid #BEBEBE">'
			else:
				miRNA_info += '\n<tr><th class="alignright">miRNA:&nbsp;&nbsp;&nbsp;</th><td>'
			miRNA_info += '<div style="margin:10px 0px 10px 10px"><table>'
			miRNA_info += '<tr><th class="alignright" style="width:120px;">Name:</th><td style="width:220px;">%s</td>' % (each[0])
			miRNA_info += '<tr><th class="alignright">miRBaseID:</th><td>%s&nbsp;&nbsp;&nbsp;&nbsp;<span style="font-size:12px;color:blue;text-decoration:underline;cursor:pointer"><a href="http://www.mirbase.org/cgi-bin/mature.pl?mature_acc=%s" target="_blank" style="color:blue;text-decoration:underline">to miRBase</a></span></td>' % (each[1], each[1])
			miRNA_info += '<tr><th class="alignright">Coordinate:</th><td>%s</td>' % (each[3]+'['+each_strand+']') # by fengli
			miRNA_info += '<tr><th class="alignright">Pri-transcript:</th><td>%s</td>' % (each[2])
			miRNA_info += '</table></div>'
			miRNA_info += '</td></tr>\n'
	# 
	# by fengli adding varTPM and stdTPM, style="width:670px;", style="width:650px;"
	info_txt = '''     
<table class="info" style="width:801px;">
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
	<tr><th class="alignright" style="border-top:2px solid #BEBEBE">genome hits:&nbsp;&nbsp;</th><td style="border-top:2px solid #BEBEBE">%s</td></tr>
    <tr>
        <th class="alignright">hits info:&nbsp;&nbsp;&nbsp;</th>
        <td>
            <div style="max-height:200px;overflow-y:scroll;margin:10px 0px 10px 10px">
            <table style="width:650px;text-align:center;style="border-top:2px solid #BEBEBE"">
                %s
            </table>
            </div>
        </td>
    </tr>
    <tr><th class="alignright">sequence:&nbsp;&nbsp;&nbsp;</th><td>%s</td></tr>
    <tr><th class="alignright">length:&nbsp;&nbsp;&nbsp;</th><td>%s</td></tr>
    %s
</table>
''' % (format(lib_nums,','), maxTPM, medianTPM, meanTPM, minTPM, hit_num, hit_info, seq, str(len(seq))+' nt', miRNA_info)   #by fengli
	return info_txt.replace('\n','')

def array2plot(LibTPMs, TPMs, violin_file):
	open(violin_file,'w').write('TPMs\n'+'\n'.join(map(str, list(TPMs))))
	#TPM stat - for Info
    #TPMs = sorted(TPMs)
	# by fengli converting format
	maxTPM = format(int(TPMs.max()),',')
	minTPM = format(int(TPMs.min()),',')
	medianTPM = format(int(np.median(TPMs)),',')
	meanTPM = format(int(np.mean(TPMs)),',')
	varTPM = format(int(np.var(TPMs)),',') 
	stdTPM = format(int(np.std(TPMs)),',') 
	#
	#TPM interval - for Plot
	Lib_TPMs = []
	for each in LibTPMs:
		Lib_TPMs.append([each[0],float(each[3])])
	Lib_TPMs = sorted(Lib_TPMs, key=lambda tmp : tmp[1])	
	interval = [[0,1],[1,2],[2,3],[3,4],[4,5],[5,6],[6,7],[7,8],[8,9],[9,10],[10,100],[100,9999999]]
	interval_txt = "['0-1','1-2','2-3','3-4','4-5','5-6','6-7','7-8','8-9','9-10','10-100','>100']"
	if meanTPM > 50:
		interval = [[0,10],[10,20],[20,30],[30,40],[40,50],[50,60],[60,70],[70,80],[80,90],[90,100],[100,1000],[1000,9999999]]
		interval_txt = "['0-10','10-20','20-30','30-40','40-50','50-60','60-70','70-80','80-90','90-100','100-1000','>1000']"
	if meanTPM > 500:
		interval = [[0,100],[100,200],[200,300],[300,400],[400,500],[500,600],[600,700],[700,800],[800,900],[900,1000],[1000,10000],[10000,9999999]]
		interval_txt = "['0-100','100-200','200-300','300-400','400-500','500-600','600-700','700-800','800-900','900-1000','1000-10000','>10000']"
	interval_out = [0]*12
	#TPM interval - for Plot
	txt_out = ';;;' + interval_txt + ';;;'
	i = 0
	for each in Lib_TPMs:
		if each[1] <= interval[i][1]:
			interval_out[i] += 1
		else:
			while each[1] > interval[i][1]:
				i += 1
			interval_out[i] += 1
	txt_out += '[' + ','.join(map(str,interval_out)) + ']'
	#TPM heatmap - for Plot
	i = 0
	zval = []
	ztxt = []
	for each in Lib_TPMs[-20:][::-1]:
		if i%5 == 0:
			zval.append([])
			ztxt.append([])
		zval[-1].append(str(each[1]))
		ztxt[-1].append('"'+each[0].strip('"')+'</br></br>'+str(each[1])+'"')
		i += 1
	zval = [ '['+','.join(each)+']' for each in zval]
	zval = '['+','.join(zval)+']'
	ztxt = [ '['+','.join(each)+']' for each in ztxt]
	ztxt = '['+','.join(ztxt)+']'
	txt_out += ';;;'+zval+';;;'+ztxt
	return maxTPM, minTPM, medianTPM, meanTPM, varTPM, stdTPM, txt_out   #by fengli
		
def array2json(table_head,table_width,LibTPMs,json_file):
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
			float(LibTPMs[0][i])
			typenumber = ', "type" : "number"'
		except:
			pass
		col_txt += '{"id" : -1, "name" : "%s", "dataTypeName" : "meta_data", "fieldName" : ":%s", "position" : 0, "renderTypeName" : "meta_data", "format" : { }, "flags" : [ "hidden" ], "width" : %s %s},' % (each,each,table_width[i],typenumber)
		i += 1
	data_txt = ''
	for line in LibTPMs:
		data_txt += '[' + ','.join(line) +'],'
	open(json_file,'w').write(json_txt % (col_txt.strip(','), data_txt.strip(',')))

if __name__ == "__main__":
	print search_sequence(seq)
