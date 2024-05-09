import MySQLdb
import sys
import numpy as np
from datetime import datetime
import re

#configure list 
mysql_user = 'root'
mysql_pwd = 'ipf2018123456'
mysql_db = 'rnaseqdb'
mysql_libinfo_tb = 'libinfo'
mysql_mirna_tb = 'miRNA'
mysql_conn = MySQLdb.connect(host="localhost", user=mysql_user, passwd=mysql_pwd, db=mysql_db)

kw = sys.argv[1]
try:
	advan = sys.argv[2]
except:
	advan = 0
	
def search_keyword(kw):
	cur = mysql_conn.cursor()
	query = []

	cur.execute("SELECT Lib_Name,Title,Genotype,Ecotype,Tissue,ReleaseDate  FROM "+mysql_libinfo_tb+" WHERE Title like '%"+kw+"%'")
	query += list(cur.fetchall())

	cur.execute("SELECT Lib_Name,Title,Genotype,Ecotype,Tissue,ReleaseDate FROM "+mysql_libinfo_tb+" WHERE Genotype like '%"+kw+"%'")
	query += list(cur.fetchall())

	cur.execute("SELECT Lib_Name,Title,Genotype,Ecotype,Tissue,ReleaseDate FROM "+mysql_libinfo_tb+" WHERE Ecotype like '%"+kw+"%'")
	query += list(cur.fetchall())

	cur.execute("SELECT Lib_Name,Title,Genotype,Ecotype,Tissue,ReleaseDate FROM "+mysql_libinfo_tb+" WHERE Tissue like '%"+kw+"%'")
	query += list(cur.fetchall())
	
	query = list(set(query))
	if advan:
		advans = advan.strip(':').split(':')
		query = np.asarray(query)
		b = np.asarray(['1']*len(query))
		query = np.c_[query,b]
#		print query[0:2]
		
		for cond in advans:
			logic,type,word = cond.split(',')
			if type == 'DATE':
				minv,maxv = word.replace(' ','').split('-')
			else:
				word = word.replace(' ','')
#			if type in ['LibraryTPM' 'Tissue' 'Ecotype' 'Genotype' 'Keyword' 'Date']:
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
#			print final_str_in, final_str_out, final_str_out_not

			if type == 'TITLEKEYWORD':
				for line in query:
					if line[6] == final_str_in:
						m = re.search(word, line[1], re.IGNORECASE)
						if not m:
							line[6] = final_str_out
						else:
							line[6] = final_str_out_not
			elif type == 'TISSUE' or type == 'ECOTYPE' or type == 'GENOTYPE':
				idx = {'TISSUE':4, 'ECOTYPE':3, 'GENOTYPE':2}[type]
				for line in query:
					if line[6] == final_str_in:
						if line[idx].upper() != word :
							line[6] = final_str_out
						else:
							line[6] = final_str_out_not
			elif type == 'DATE':
				minv = datetime.strptime(minv, '%Y/%m/%d')
				maxv = datetime.strptime(maxv, '%Y/%m/%d')
				for line in query:
					if line[6] == final_str_in:
						if line[5] != '-':
							releasedate = datetime.strptime(line[5], '%Y/%m/%d')
							if releasedate < minv or releasedate > maxv:
								line[6] = final_str_out
							else:
								line[6] = final_str_out_not

		query = [list(each[:6]) for each in query if each[6]=='1']
		#print query
		if query == []:
			return 'none'
	
#	print query[0:2] 
	info_txt = '''
<table class="info" style="width:800px;">
	<tr>
		<th class="alignright" style="border-top:2px solid #BEBEBE">found in:&nbsp;&nbsp;&nbsp;</th>
		<td style="border-top:2px solid #BEBEBE;font-weight: 800;color:#DF0101">%s<a style="font-weight:1;">&nbsp;libraries</a></td>
	</tr>
%s
</table>
'''
	info_txt_inner = ''
	for each2 in query:
		ncbi_link = ''
		# by fengli adding GEO link and see in IGV
		if each2[0].startswith('GSM'):
			ncbi_link = 'https://www.ncbi.nlm.nih.gov/geo/query/acc.cgi?acc='+each2[0]
		else:
			ncbi_link = 'https://www.ncbi.nlm.nih.gov/sra/'+each2[0]
		if each2 == query[-1]:
			tmp = '''<tr><th class="alignright" style="border-bottom:2px solid #BEBEBE">Library:&nbsp;&nbsp;&nbsp;</th><td style="border-bottom:2px solid #BEBEBE">'''
		else:
			tmp = '''<tr><th class="alignright">Library:&nbsp;&nbsp;&nbsp;</th><td>'''
				
		info_txt_inner += tmp+'''
					<div style="margin:10px 0px 10px 10px">
						<table class="alignright">
							<tr>
								<th style="border-top:1px solid #BEBEBE">&nbsp;&nbsp;&nbsp;Sample Acc:</th>
								<td style="border-top:1px solid #BEBEBE;" align="left">%s&nbsp;&nbsp;&nbsp;&nbsp;
									<span>
										<a href="%s" target="_blank" style="font-size:12px;color:blue;text-decoration:underline;">to GEO/SRA
										</a>&nbsp;&nbsp;&nbsp;
									</span>
									<span style="font-size:12px;color:blue;text-decoration:underline;cursor:pointer" onclick="addalignment3('%s')">see in igv<span>
								</td>
							</tr>
													
							<tr><td style="font-weight:bold;">Description:</td><td style="width:400px;text-align:left">%s</td></tr>
							<tr><td style="font-weight:bold;">Genotype:</td><td style="width:400px;text-align:left">%s</td></tr>
							<tr><td style="font-weight:bold;">Ecotype:</td><td style="width:400px;text-align:left">%s</td></tr>
							<tr><td style="font-weight:bold;">Tissue:</td><td style="width:400px;text-align:left">%s</td></tr>
							<tr><td style="font-weight:bold;">Date:</td><td style="width:400px;text-align:left">%s</td></tr>
						</table>
					</div>
				</td>
			</tr>
''' % (each2[0], ncbi_link, each2[0], each2[1], each2[2], each2[3], each2[4], each2[5])
	return (info_txt % (format(len(query),','), info_txt_inner)).replace('\n',' ')

if __name__ == "__main__":
	print search_keyword(kw)