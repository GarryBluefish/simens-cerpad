

#from doc.test3  import ajoute_trente
#import sys 


import os
from rainbowbox import *
#from collections import defaultdict
#from rainbowbox.color import hsv2rgb
#from rainbowbox.order_base import all_subsets
from rainbowbox.order_elements import *
#from rainbowbox.order_boxes import *
#from rainbowbox.draw import *


#result = ajoute_trente(int(sys.argv[2]))
#print (result)


allele_A = Element(None, "A")
allele_C = Element(None, "C")

genotype_AA = Property(None, "AA", weight = 5 * 3)
genotype_AC = Property(None, "AC", weight = 5 * 10)

relations = [
        Relation(genotype_AA, allele_A),
        Relation(genotype_AC, allele_A),
        Relation(genotype_AC, allele_C),
]

html_page = HTMLPage()
html_page.rainbowbox(relations)

# affiche dans le navigateur
html_page.show() 
# enregistre dans un fichier HTML
open("nom_de_fichier.html", "w").write(html_page.get_html_with_header()) 

