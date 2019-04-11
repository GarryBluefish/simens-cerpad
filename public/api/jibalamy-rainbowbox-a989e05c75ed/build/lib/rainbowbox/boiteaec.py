
import rainbowbox
from rainbowbox import *
from rainbowbox.order_elements import *
from rainbowbox.color import *


allele_A = Element(None, "<b>A</b>")
allele_C = Element(None, "<b>C</b>")

genotype_AA = Property(None, "AA", weight=float(5))
genotype_AC = Property(None, "AC", weight=float(10))

relations = [
        Relation(genotype_AA, allele_A),
        Relation(genotype_AC, allele_A),
        Relation(genotype_AC, allele_C),
]


element_A = Element(None, "<b>A</b>", "Alanine")
element_G = Element(None, "<b>G</b>", "Glycine")
element_P = Element(None, "<b>P</b>", "Proline")

property_small = Property(None, "Small")

#relations = [
#  Relation(element_P, property_small),
#  Relation(element_A, property_small),
#  Relation(element_G, property_small),
#]


html_page = HTMLPage()
html_page.rainbowbox(relations)

#print(relations)

# affiche dans le navigateur
print(html_page.show()) 
# enregistre dans un fichier HTML
open("nom_de_fichier.html", "w").write(html_page.get_html_with_header()) 

