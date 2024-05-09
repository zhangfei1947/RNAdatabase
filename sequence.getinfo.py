import numpy as np

def SequnceInfo(hit_file,exp_file):
    hits = open(hit_file,'r').read().strip().split()
    seq = hits[0]
    hits = hits[1].split('/')
    exps = open(exp_file,'r').read().strip().split()[1].split('/')
    TPMs = [float(each.split('-')[2]) for each in exps]    
    TPMs = np.array(TPMs)

    hit_num = len(hits)
    lib_num = len(TPMs)
    maxTPM = TPMs.max()
    minTPM = TPMs.min()
    medianTPM = np.median(TPMs)
    meanTPM = round(np.mean(TPMs),3)

    hit_info = '<tr><th>Chr</th><th>Start</th><th>End</th><th>Strand</th><th>Annotation</th><th>explore in igv</th></tr>\n'
    for each in hits[1:]:
        each = each.split('-')
        if len(each) == 4:
            each.append('-')
        if each[3] == '0':
            each[3] = 'Forward'
        else:
            each[3] = 'Reverse'
        hit_info += '<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td style="cursor:pointer" onclick="zoomin2(\'%s\')">see in igv</td></tr>\n' % (each[0],each[1],each[2],each[3],each[4],each[0]+':'+each[1]+'-'+each[2])


    info_html = '''
<table class="info" style="width:800px;">
    <tr><th class="alignright" style="border-top:2px solid #BEBEBE">sequence:&nbsp;&nbsp;&nbsp;</th><td style="border-top:2px solid #BEBEBE">%s</td></tr>
    <tr><th class="alignright">length:&nbsp;&nbsp;&nbsp;</th><td>%s</td></tr>
    <tr><th class="alignright">miRNA:&nbsp;&nbsp;&nbsp;</th><td>not in known miRNA list</td></tr>
    <tr><th class="alignright">genome hits:&nbsp;&nbsp;&nbsp;</th><td>%s</td></tr>
    <tr>
        <th class="alignright">hit info:&nbsp;&nbsp;&nbsp;</th>
        <td>
			<div style="max-height:200px;overflow-y:scroll;margin:10px 0px 10px 10px">
            <table>
                %s
            </table>
			</div>
        </td>
    </tr>
    <tr><th class="alignright">library num:&nbsp;&nbsp;&nbsp;</th><td>%s</td></tr>
    <tr>
        <th class="alignright" style="border-bottom:2px solid #BEBEBE">TPM stat:&nbsp;&nbsp;&nbsp;</th>
        <td style="border-bottom:2px solid #BEBEBE">
			<div style="margin:10px 0px 10px 10px">
            <table>
                <tr><th>maxTPM</th><th>medianTPM</th><th>meanTPM</th><th>minTPM</th></tr>
                <tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>
            </table>
			</div>
        </td>
    </tr>
</table>
''' % (seq, len(seq), hit_num, hit_info, lib_num, maxTPM, medianTPM, meanTPM, minTPM)

    return info_html



print SequnceInfo('read.hit', 'read.express')
