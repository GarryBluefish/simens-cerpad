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

#import rainbowbox
#Element, Property = rainbowbox.Property, rainbowbox.Element

def label(text):
  return """<div style="width: 1em; height: 10em; transform: rotate(270deg);">%s</div>""" % text

def label(text):
  return "".join(i[0] for i in text.replace("-", " ").split())

element_JBL  = Element(None, label("Jean-Baptiste Lamy"))
element_AV   = Element(None, label("Alain Venot"))
element_CD   = Element(None, label("Catherine Duclos"))
element_SD   = Element(None, label("Sylvie Després"))
element_MCJ  = Element(None, label("Marie-Christine Jaulent"))
element_BS   = Element(None, label("Brigitte Séroussi"))
element_JB   = Element(None, label("Jacques Bouaud"))
element_RN   = Element(None, label("Romain Ng"))
element_KS   = Element(None, label("Karima Sedki"))
element_PV   = Element(None, label("Pascal Vaillant"))
element_JC   = Element(None, label("Jean Charlet"))
element_BV   = Element(None, label("Bernard Virginie"))
element_EL   = Element(None, label("Eugénia Lamas"))
element_LT   = Element(None, label("Laurent Toubiana"))
element_CB   = Element(None, label("Cédric Bousquet"))
element_JMR  = Element(None, label("Jean-Marie Rodrigues"))
element_RT   = Element(None, label("Rosy Tsopra"))
element_XA   = Element(None, label("Xavier Aimé"))
element_GC   = Element(None, label("Gaoussou Camara"))
element_YP   = Element(None, label("Yves Parès"))
element_SDa  = Element(None, label("Stéfan Darmoni"))
element_LS   = Element(None, label("Lina Soualmia"))
element_MC   = Element(None, label("Mélanie Courtine"))
element_JN   = Element(None, label("Jérôme Nobécourt"))
element_FB   = Element(None, label("Fadi Badra"))

property_p13           = Property(None, "Université Paris 13")
property_smbh          = Property(None, "UFR SMBH")
property_iut           = Property(None, "IUT de Bobigny")
property_p6            = Property(None, "Université Paris 6")
property_st_etienne    = Property(None, "Université St É- tienne Lyon")
property_rouen         = Property(None, "Université Rouen")
property_bambey        = Property(None, "Université Bambey Sénégal")
property_avicenne      = Property(None, "Hôpital Avicenne")
property_tenon         = Property(None, "Hôpital Tenon")
property_aphp          = Property(None, "APHP")
property_chu_st_etienne= Property(None, "CHU St Étienne")
property_chu_rouen     = Property(None, "CHU Rouen")
property_inserm        = Property(None, "INSERM")

property_enseignant    = Property(None, "Enseignant")
property_chercheur     = Property(None, "Chercheur")
property_hospitalier   = Property(None, "Hospitalier")

property_permanent     = Property(None, "Permanent")
property_non_permanent = Property(None, "Non permanent")
#property_rang_a        = Property(None, "Rang A")
#property_rang_b        = Property(None, "Rang B")

relations = [
  Relation(element_JBL, property_p13),
  Relation(element_JBL, property_smbh),
  Relation(element_JBL, property_enseignant),
  Relation(element_JBL, property_chercheur),
  Relation(element_JBL, property_permanent),
  #Relation(element_JBL, property_rang_b),

  Relation(element_AV, property_p13),
  Relation(element_AV, property_chercheur),
  Relation(element_AV, property_permanent),

  Relation(element_CD, property_p13),
  Relation(element_CD, property_smbh),
  Relation(element_CD, property_avicenne),
  Relation(element_CD, property_enseignant),
  Relation(element_CD, property_chercheur),
  Relation(element_CD, property_hospitalier),
  Relation(element_CD, property_permanent),
  #Relation(element_CD, property_rang_a),

  Relation(element_SD, property_p13),
  Relation(element_SD, property_smbh),
  Relation(element_SD, property_enseignant),
  Relation(element_SD, property_chercheur),
  Relation(element_SD, property_permanent),
  #Relation(element_SD, property_rang_a),

  Relation(element_MCJ, property_inserm),
  Relation(element_MCJ, property_chercheur),
  Relation(element_MCJ, property_permanent),
  #Relation(element_MCJ, property_rang_a),
  
  Relation(element_BS, property_tenon),
  Relation(element_BS, property_p6),
  Relation(element_BS, property_enseignant),
  Relation(element_BS, property_chercheur),
  Relation(element_BS, property_hospitalier),
  Relation(element_BS, property_permanent),
  #Relation(element_BS, property_rang_b),
  
  Relation(element_JB, property_aphp),
  Relation(element_JB, property_permanent),
  Relation(element_JB, property_chercheur),
  
  Relation(element_JC, property_aphp),
  Relation(element_JC, property_permanent),
  Relation(element_JC, property_chercheur),
  
  Relation(element_RN, property_p13),
  Relation(element_RN, property_chercheur),
  Relation(element_RN, property_non_permanent),
  
  Relation(element_KS, property_p13),
  Relation(element_KS, property_iut),
  Relation(element_KS, property_enseignant),
  Relation(element_KS, property_chercheur),
  Relation(element_KS, property_permanent),
  #Relation(element_KS, property_rang_b),
  
  Relation(element_PV, property_p13),
  Relation(element_PV, property_iut),
  Relation(element_PV, property_enseignant),
  Relation(element_PV, property_chercheur),
  Relation(element_PV, property_permanent),
  #Relation(element_PV, property_rang_b),
  
  Relation(element_BV, property_p13),
  Relation(element_BV, property_smbh),
  Relation(element_BV, property_permanent),
  
  Relation(element_EL, property_inserm),
  Relation(element_EL, property_permanent),
  Relation(element_EL, property_chercheur),
  
  Relation(element_LT, property_inserm),
  Relation(element_LT, property_permanent),
  Relation(element_LT, property_chercheur),
  
  Relation(element_CB, property_chu_st_etienne),
  Relation(element_CB, property_chercheur),
  Relation(element_CB, property_hospitalier),
  Relation(element_CB, property_permanent),
  
  Relation(element_JMR, property_st_etienne),
  Relation(element_JMR, property_chu_st_etienne),
  Relation(element_JMR, property_enseignant),
  Relation(element_JMR, property_chercheur),
  Relation(element_JMR, property_hospitalier),
  Relation(element_JMR, property_permanent),

  Relation(element_RT, property_p13),
  Relation(element_RT, property_smbh),
  Relation(element_RT, property_avicenne),
  Relation(element_RT, property_enseignant),
  Relation(element_RT, property_chercheur),
  Relation(element_RT, property_hospitalier),
  Relation(element_RT, property_non_permanent),
  
  Relation(element_XA, property_inserm),
  Relation(element_XA, property_chercheur),
  Relation(element_XA, property_non_permanent),

  Relation(element_GC, property_bambey),
  Relation(element_GC, property_enseignant),
  Relation(element_GC, property_chercheur),
  Relation(element_GC, property_permanent),

  Relation(element_YP, property_inserm),
  Relation(element_YP, property_chercheur),
  Relation(element_YP, property_non_permanent),

  Relation(element_SDa, property_rouen),
  Relation(element_SDa, property_chu_rouen),
  Relation(element_SDa, property_enseignant),
  Relation(element_SDa, property_chercheur),
  Relation(element_SDa, property_hospitalier),
  Relation(element_SDa, property_permanent),

  Relation(element_LS, property_rouen),
  Relation(element_LS, property_enseignant),
  Relation(element_LS, property_chercheur),
  Relation(element_LS, property_permanent),
  
  Relation(element_FB, property_p13),
  Relation(element_FB, property_smbh),
  Relation(element_FB, property_enseignant),
  Relation(element_FB, property_chercheur),
  Relation(element_FB, property_permanent),
  
  Relation(element_MC, property_p13),
  Relation(element_MC, property_iut),
  Relation(element_MC, property_enseignant),
  Relation(element_MC, property_chercheur),
  Relation(element_MC, property_permanent),

  Relation(element_JN, property_p13),
  Relation(element_JN, property_iut),
  Relation(element_JN, property_enseignant),
  Relation(element_JN, property_chercheur),
  Relation(element_JN, property_permanent),
]


order = best_elements_order_heuristic(relations)
#order = best_elements_order_tree(relations)

html_page = HTMLPage()
html_page.rainbowbox(relations, order = order)
html_page.show()
