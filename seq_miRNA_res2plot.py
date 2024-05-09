
def seq_miRNA_res2plot(csv_in):
    txt_out = ''
    lines = open(csv_in,'r').readlines()
    tmp_list = []
    for line in lines[1:]:
        tmp = line.replace('"','').strip().split(',')
        tmp_list.append([tmp[0],float(tmp[3])])
    tmp_list = sorted(tmp_list, key=lambda tmp : tmp[1])

    interval = [[-1,0],[0,1],[1,2],[2,3],[3,4],[4,5],[5,6],[6,7],[7,8],[8,9],[9,10],[10,100],[100,9999999]]
    interval_out = [0]*13

    i = 0
    for each in tmp_list:
        if each[1] <= interval[i][1]:
            interval_out[i] += 1
        else:
            i += 1
    txt_out += ','.join(map(str,interval_out))

    if len(tmp_list) > 19:
        i = 0
        zval = ''
        ztxt = ''
        for each in tmp_list[-20:][::-1]:
            if i%5 == 0:
                zval += ';'
                ztxt += ';'
            zval += str(each[1])+','
            ztxt += each[0]+':'+str(each[1])+','
            i += 1
    txt_out += zval+ztxt
    print txt_out
    return txt_out
            

seq_miRNA_res2plot('serach.miRNA.clean.desc.csv')
