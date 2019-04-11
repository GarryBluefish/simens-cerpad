# RainbowBox
# Copyright (C) 2015-2016 Jean-Baptiste LAMY
# LIMICS (Laboratoire d'informatique médicale et d'ingénierie des connaissances en santé), UMR_S 1142
# University Paris 13, Sorbonne paris-Cité, Bobigny, France

# This program is free software: you can redistribute it and/or modify
# it under the terms of the GNU Lesser General Public License as published by
# the Free Software Foundation, either version 3 of the License, or
# (at your option) any later version.

# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU Lesser General Public License for more details.

# You should have received a copy of the GNU Lesser General Public License
# along with this program.  If not, see <http://www.gnu.org/licenses/>.

from rainbowbox import *
from rainbowbox.order_elements import *

import rainbowbox
Element, Property = rainbowbox.Property, rainbowbox.Element

element_1 = Element(None, "Sténose valvulaire pulmonaire")
element_2 = Element(None, "Sténose valvulaire tricuspide")
element_3 = Element(None, "Sténose valvulaire aortique")
element_4 = Element(None, "Sténose mitrale")
element_5 = Element(None, "Sténose aortique")
element_6 = Element(None, "Sténose aortique sous valvulaire")
element_7 = Element(None, "Atrésie congénitale de la valve tricuspide")
element_8 = Element(None, "Insuffisance congénitale de l'aorte")

property_congenital = Property(None, "Congénital")

property_stenose = Property(None, "Sténose")
property_atresie = Property(None, "Atrésie")
property_insuffisance = Property(None, "Insuffisance")

property_aorte = Property(None, "Aorte")

property_valve = Property(None, "Valve")
property_valve_aortique = Property(None, "Valve aortique")
property_valve_mitrale = Property(None, "Valve mitrale")
property_valve_tricuspide = Property(None, "Valve tricuspide")
property_valve_pulmonaire = Property(None, "Valve pulmonaire")

property_gauche = Property(None, "Coeur gauche")
property_droit = Property(None, "Coeur droit")

relations = [
  Relation(element_1, property_stenose),
#  Relation(element_1, property_valve),
  Relation(element_1, property_valve_pulmonaire),
  Relation(element_1, property_droit),

  Relation(element_2, property_stenose),
#  Relation(element_2, property_valve),
  Relation(element_2, property_valve_tricuspide),
  Relation(element_2, property_droit),

  Relation(element_3, property_stenose),
#  Relation(element_3, property_valve),
  Relation(element_3, property_valve_aortique),
  Relation(element_3, property_gauche),
  Relation(element_3, property_aorte),

  Relation(element_4, property_stenose),
#  Relation(element_4, property_valve),
  Relation(element_4, property_valve_mitrale),
  Relation(element_4, property_gauche),

  Relation(element_5, property_stenose),
#  Relation(element_5, property_valve),
  Relation(element_5, property_valve_aortique),
  Relation(element_5, property_gauche),
  Relation(element_5, property_aorte),

  Relation(element_6, property_stenose),
  Relation(element_6, property_gauche),
  Relation(element_6, property_aorte),

  Relation(element_7, property_congenital),
  Relation(element_7, property_atresie),
#  Relation(element_7, property_valve),
  Relation(element_7, property_valve_tricuspide),
  Relation(element_7, property_droit),

  Relation(element_8, property_congenital),
  Relation(element_8, property_insuffisance),
  Relation(element_8, property_aorte),
  
]


order = best_elements_order_heuristic(relations)
#order = best_elements_order_pca(relations)
#order = best_elements_order_tree(relations)

html_page = HTMLPage()
html_page.rainbowbox(relations, order = order, use_element_details_in_relation = True)
html_page.show()
