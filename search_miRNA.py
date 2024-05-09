import lmdb,MySQLdb
import numpy as np
import sys,hashlib,glob
from seqCompress import *
from search_Sequences import res2info, array2plot, array2json,seq_advan_filt

#configure list 
mysql_user = 'root'
mysql_pwd = '**********'
mysql_db = 'rnaseqdb'
mysql_miRNA_tb = 'miRNA'
mysql_libinfo_tb = 'libinfo'
#read2hit_db will add the length of seq to be specific one
#read2hit_db = 'table/read2hit.lmdb'
#read2libtpm_db = 'table/read2libTPM.lmdb'
readinfo_db = 'table/ReadInfo.lmdb'
mysql_conn = MySQLdb.connect(host="localhost", user=mysql_user, passwd=mysql_pwd, db=mysql_db)

instring = sys.argv[1]
intype = sys.argv[2]
try:
	advan = sys.argv[3]
except:
	advan = 0
json_file = 'user/'+hashlib.md5(instring+intype).hexdigest()
violin_file = json_file + '.violin'

def search_miRNA(instring, intype):
	instring = instring.replace('ath-','')
	#search it in miRNA mySQL table
	cur = mysql_conn.cursor()
	if intype == 'miRNAName':
		cur.execute("SELECT miRNAName,miRBaseID,GeneID,Coordinate,Strand,Sequence FROM %s WHERE miRNAName = '%s'" % (mysql_miRNA_tb, instring))
	elif intype == 'miRBaseID':
		cur.execute("SELECT miRNAName,miRBaseID,GeneID,Coordinate,Strand,Sequence FROM %s WHERE miRBaseID = '%s'" % (mysql_miRNA_tb, instring))
	else:
		return 'None';
	miRNA_query = cur.fetchone()
	cur.close()
	if miRNA_query:
		miRNA_query = [miRNA_query]
		#instring in miRNA mySQL table, get sequence
		seq = miRNA_query[0][5]
		#find read in read2hit
		glob.glob(readinfo_db+str(len(seq)))
		env = lmdb.open(readinfo_db+str(len(seq)))
		txn = env.begin()
		readhit_query = txn.get(seqzip(seq))
		env.close()
		hit_txt,exp_txt = readhit_query.split('$')
		LibTPMs = [('0-'+each+'-0-0-0-0-0-0-1').split('-') for each in exp_txt.strip('/').split('/')]
		env.close()
		#get each library's infor,ation
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
		varTPM = '-'   #by fengli
		stdTPM = '-'   #by fengli
		plot_txt = ';;;-;;;-;;;-'
		if lib_nums > 1:
			TPMs = np.array(nums[...,1])
			#array2plot - generate plot txt data
			maxTPM, minTPM, medianTPM, meanTPM, varTPM, stdTPM, plot_txt = array2plot(LibTPMs, TPMs, violin_file)                  #by fengli
			#res2info - generate information data
		info_txt = res2info(seq, miRNA_query, hit_txt, lib_nums, maxTPM, minTPM, medianTPM, meanTPM, varTPM, stdTPM)  #by fengli
		return info_txt + plot_txt
	else:
		cur = mysql_conn.cursor()
		cur.execute("SELECT miRNAName FROM {} WHERE miRNAName like '%{}%' limit 10".format(mysql_miRNA_tb, instring))
		res = cur.fetchall()
		if len(res) > 0:
			return 'None</br>Do you mean: '+ ', '.join([each[0] for each in res])
		cur.execute("SELECT miRNAName FROM {} WHERE miRBaseID like '%{}%' limit 10".format(mysql_miRNA_tb, instring))
		res = cur.fetchall()
		if len(res) > 0:
			return 'None</br>Do you mean: '+ ', '.join([each[0] for each in res])
		cur.close()
		#sequence has not in our big list, no result, query finish.
		return 'None'

if __name__ == "__main__":
	print search_miRNA(instring, intype)
