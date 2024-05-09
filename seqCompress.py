import sys


dict1 = {"AAA":"a", "AAT":"b", "AAC":"c", "AAG":"d", "ATA":"e", "ATT":"f", "ATC":"g", "ATG":"h", "ACA":"i", "ACT":"j", "ACC":"k", "ACG":"l", "AGA":"m", "AGT":"n", "AGC":"o", "AGG":"p", "TAA":"q", "TAT":"r", "TAC":"s", "TAG":"t", "TTA":"u", "TTT":"v", "TTC":"w", "TTG":"x", "TCA":"y", "TCT":"z", "TCC":"B", "TCG":"C", "TGA":"D", "TGT":"E", "TGC":"F", "TGG":"H", "CAA":"I", "CAT":"J", "CAC":"K", "CAG":"L", "CTA":"M", "CTT":"N", "CTC":"O", "CTG":"P", "CCA":"Q", "CCT":"R", "CCC":"S", "CCG":"U", "CGA":"V", "CGT":"W", "CGC":"X", "CGG":"Y", "GAA":"Z", "GAT":"0", "GAC":"1", "GAG":"2", "GTA":"3", "GTT":"4", "GTC":"5", "GTG":"6", "GCA":"7", "GCT":"8", "GCC":"9", "GCG":"~", "GGA":"!", "GGT":"@", "GGC":"#", "GGG":"$"}

dict2 = {"a":"AAA", "b":"AAT", "c":"AAC", "d":"AAG", "e":"ATA", "f":"ATT", "g":"ATC", "h":"ATG", "i":"ACA", "j":"ACT", "k":"ACC", "l":"ACG", "m":"AGA", "n":"AGT", "o":"AGC", "p":"AGG", "q":"TAA", "r":"TAT", "s":"TAC", "t":"TAG", "u":"TTA", "v":"TTT", "w":"TTC", "x":"TTG", "y":"TCA", "z":"TCT", "B":"TCC", "C":"TCG", "D":"TGA", "E":"TGT", "F":"TGC", "H":"TGG", "I":"CAA", "J":"CAT", "K":"CAC", "L":"CAG", "M":"CTA", "N":"CTT", "O":"CTC", "P":"CTG", "Q":"CCA", "R":"CCT", "S":"CCC", "U":"CCG", "V":"CGA", "W":"CGT", "X":"CGC", "Y":"CGG", "Z":"GAA", "0":"GAT", "1":"GAC", "2":"GAG", "3":"GTA", "4":"GTT", "5":"GTC", "6":"GTG", "7":"GCA", "8":"GCT", "9":"GCC", "~":"GCG", "!":"GGA", "@":"GGT", "#":"GGC", "$":"GGG", "A":"A", "T":"T", "C":"C", "G":"G"}


def seqzip(seq):
    txt = ''
    i = 0
    while i < len(seq):
        tmp = seq[i:i+3]
        if len(tmp) == 3:
            txt += dict1[tmp]
        else:
            txt += tmp
        i += 3
    return txt

def sequnzip(seq):
    return ''.join([dict2[each] for each in seq]) 

if __name__ == '__main__':
    mod = sys.argv[1]
    instring = sys.argv[2]
    if mod == 'zip':
        print seqzip(instring)
    elif mod == 'unzip':
        print sequnzip(instring)

