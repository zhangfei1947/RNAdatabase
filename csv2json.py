
def csv2json(csv_in,json_out):
    lines = open(csv_in,'r').readlines()
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
    for each in lines[0].strip().split(','):
        typenumber = ''
        try: 
            float(lines[1].strip().split(',')[i])
            typenumber = ', "type" : "number"'
        except:
            pass
        i += 1
        col_txt += '{"id" : -1, "name" : "%s", "dataTypeName" : "meta_data", "fieldName" : ":%s", "position" : 0, "renderTypeName" : "meta_data", "format" : { }, "flags" : [ "hidden" ] %s},' % (each,each,typenumber)
    data_txt = ''
    for line in lines[1:]:
        line = line.strip().split(',')
        data_txt += '[' + ','.join(line) +'],'

    open('json_out','w').write(json_txt % (col_txt.strip(','), data_txt.strip(',')))

csv2json("serach.miRNA.clean.desc.csv", "output.json")
