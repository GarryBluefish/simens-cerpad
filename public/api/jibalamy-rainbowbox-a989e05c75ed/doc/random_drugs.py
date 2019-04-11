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

from rainbowbox       import *
from rainbowbox.color import *
from rainbowbox.order_elements import *

groupe_0   = PropertyGroup("Hypersensibility")
groupe_5   = PropertyGroup("Infection")
groupe_7   = PropertyGroup("Tumor")
groupe_10  = PropertyGroup("Hematology")
groupe_20  = PropertyGroup("Endocrine, nutritional et metabolic")
groupe_30  = PropertyGroup("Comportemental")
groupe_40  = PropertyGroup("Nervous system")
groupe_50  = PropertyGroup("Eye and ear")
groupe_60  = PropertyGroup("Cardiovascular")
groupe_70  = PropertyGroup("Pulmonar")
groupe_80  = PropertyGroup("Digestive")
groupe_90  = PropertyGroup("Skin")
groupe_100 = PropertyGroup("Osteomuscular")
groupe_110 = PropertyGroup("Urogenital")
groupe_120 = PropertyGroup("Age")
groupe_130 = PropertyGroup("Pregnancy")

element_1 = Element(None, """A""")
element_2 = Element(None, """B""")
element_3 = Element(None, """C""")
element_4 = Element(None, """D""")
element_5 = Element(None, """E""")
element_6 = Element(None, """F""")
element_7 = Element(None, """G""")
element_8 = Element(None, """H""")
elements = [element_1, element_2, element_3, element_4, element_5, element_6, element_7, element_8]

properties = []
for i in range(26):
  properties.append(Property(None, "Prop %s" % i))

relations = []
import random
already = set()
while len(already) < 79:
  element  = random.choice(elements)
  property = random.choice(properties)
  if not (element, property) in already:
    already.add((element, property))
    relations.append(Relation(element, property))


elements, element_groups, properties, property_groups, element_2_property_2_relation, property_2_element_2_relation = relations_2_model(relations)

def test_element(e, a, b, props):
  for p in props:
    if (p is a) or (p is b):
      if not e in property_2_element_2_relation[p]:return False
      
    else:
      if e in property_2_element_2_relation[p]:return False
      
  return True

def test_elements(a, b, props):
  for e in elements:
    if test_element(e, a, b, props): return e
  return None

def test_order(order):
  els = []
  for i in range(len(order)):
    a = order[i]
    if i == len(order) - 1: b = order[0]
    else:                   b = order[i + 1]
    
    e = test_elements(a, b, order)
    if e: els.append(e)
    else: return None
  return els
    

#for props in all_subsets(properties):
#  if len(props) < 3: continue
#  
#  for order in all_orders(list(props)):
#    els = test_order(order)
#    if els:
#      print(order, els)
#      break
nb = 0
for p1 in properties:
  for p2 in properties:
    for p3 in properties:
     for p4 in properties:
      if p1.label >= p2.label: continue
      if p2.label >= p3.label: continue
      if p3.label >= p4.label: continue
      
      els = test_order([p1, p2, p3, p4])
      if els:
        print(p1, p2, p3, p4, els)
        nb += 1

print(nb)
        
html_page = HTMLPage()

order = best_elements_order_heuristic(relations, elements)

html_page.rainbowbox(relations , elements = elements, order = order)
html_page.show()
