# -*- coding: utf-8 -*-
import sys, json, glob, os
reload(sys)  
sys.setdefaultencoding('utf8')

json_file = sys.argv[1]
csv_file = sys.argv[2]

if os.path.isfile(json_file):
	json_txt = open(json_file,'r').read()
	json_dict = json.loads(json_txt.decode('gbk'))
	csv_txt =  '\t'.join([each['name'] for each in json_dict['meta']['view']['columns']])+'\n'
	for each in json_dict['data']:
		csv_txt += '\t'.join(map(str,each))+'\n'
	open(csv_file,'w').write(csv_txt)
elif os.path.isfile(json_file+'.1'):
	csv_txt = ''
	for jfile in glob.glob(json_file+'.[12345]'):
		print jfile
		json_txt = open(jfile,'r').read()
		json_dict = json.loads(json_txt.decode('gbk'))
		csv_txt +=  '\t'.join([each['name'] for each in json_dict['meta']['view']['columns']])+'\n'
		for each in json_dict['data']:
			csv_txt += '\t'.join(map(str,each))+'\n'
	open(csv_file,'w').write(csv_txt)
