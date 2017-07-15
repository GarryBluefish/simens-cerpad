  /* ************** Le script du TRAITEMENT D'ORDONNANCE MEDICAL (PRESCRIPTION D'ORDONNANCE)  **************** -->
  <!-- ************** Le script du TRAITEMENT D'ORDONNANCE MEDICAL (PRESCRIPTION D'ORDONNANCE)  **************** -->
  <!-- ************** Le script du TRAITEMENT D'ORDONNANCE MEDICAL (PRESCRIPTION D'ORDONNANCE)  **************** --> */
  

  var n = 0;
  var m = 0;
  var tab = [];
  function RAS(n)
  {   n=n+44;  /* on incrémente 1 a chaque fois qu'on ajoute un champ input // on n'incrémente pas quand on ajoute en bas*/
  	  document.AVIS.elements[n].value="";
  	  document.AVIS.elements[n+1].value="";
      document.AVIS.elements[n+2].value="";
      document.AVIS.elements[n+3].value="";
     
  }

  function charger(lignes){
  	var k = lignes;
  	var cpt = 0;
  	var depart = (lignes - 1)*4;
  	var niv = 0;
      var inputs = document.getElementsByTagName('input');
  	for (var j = depart, c = inputs.length ; j < c-42 ; j++) {  /* j < 41 : valeur à modifier pour le chargement: en ajoutant un type input */ 
  		valeur = document.getElementById('medicament_'+cpt+k); /* On ajoute 1  */
  	    valeur.value = tab[niv++];
  	    cpt++;
  	    if( cpt == 4 ){k=k+1; cpt = 0;}
  	}
  }

  function recuperer(ligne){
  	var k = ligne;
  	var cpt = 0;
  	var depart = (ligne - 1)*4;
  	var niv = 0;
  	var inputs = document.getElementsByTagName('input');
  	for (var j = depart, c = inputs.length ; j < c-42 ; j++) { /* j < 41 : valeur à modifier pour la récupération: en ajoutant un type input */
  		valeur = document.getElementById('medicament_'+cpt+k); /* On ajoute 1*/
  	    val = valeur.value;
  	    tab[niv++] = val;
  	    cpt++;
  	    if( cpt == 4 ){k=k+1; cpt = 0;}
  	}
  	
  }

  function sup(rows){ 
  	dernier = i2;
  	recuperer(rows+1);
  	for(var row=rows; row<i2;row++){
  		tableau = document.getElementById('tableau_'+row);
  		tableau.parentNode.removeChild(tableau);
  	}
  	i2 = rows;
  	for(var j=rows; j<dernier-1; j++){
  	   create_champ(j);
  	}
  	if(i2 == 2 && dernier == 3){ //si la derniere ligne est la deuxieme ligne et quelle est supprimee 		
  		document.getElementById('leschamps_2').innerHTML ='<span id="leschamps_'+i2+'"><a href="javascript:create_champ('+i2+')"><img style="display: inline; float:left; border:transparent; padding-left: 20px;" src="/simens_derniereversion/public/images_icons/add.PNG" alt="Constantes" title="Ajouter un m&eacute;dicament"/></a>';
  	}
  	ln = rows+1;
  	if(rows != 2 && ln == dernier){//si la derniere ligne est differente de la deuxieme et quelle est supprimee
  		document.getElementById('leschamps_'+rows).innerHTML ='<span id="leschamps_'+rows+'"><a href="javascript:create_champ('+rows+')"><img style="display: inline; float:left; border:transparent; padding-left: 20px;" src="/simens_derniereversion/public/images_icons/add.PNG" alt="Constantes" title="Ajouter un m&eacute;dicament"/></a>';
  	}
  	charger(rows);//charger les donnees
  }
 
  
  function create_champ(i) {
	  
  	    i2 = i + 1;
          m = (i-1)*4;
  	document.getElementById('leschamps_'+i).innerHTML = '<table class="table table-bordered" id="tableau_'+i+'"  style="align: left; margin-bottom: 0px; margin-left: 0px;">'+
  	 
                                                        '<tr >'+
                                                        '   <th align="center" id="numero_'+i+'"> '+i+' </th>'+
                                                        '   <th id="medicament"><input type="text" id="medicament_0'+i+'" name="medicament_0'+i+'"></span></th>'+
  	                                                    '   <th id="posologie"><input type="text" id="medicament_1'+i+'" name="medicament_1'+i+'"></span></th>'+
  	                                                    '   <th id="quantite"><input type="text" id="medicament_2'+i+'" name="medicament_2'+i+'"></span></th>'+
  	                                                    '   <th id="duree"><input type="text" id="medicament_3'+i+'" name="medicament_3'+i+'"></span></th>'+
  	                                                    '   <th><a onclick ="sup('+i+'); return false;" href="" id="sup'+i+'">'+
  	                                                           '<img style="display: inline;" src="/simens_derniereversion/public/images_icons/symbol_supprimer.png" alt="Supprimer" title="Supprimer" style="float:left;border:transparent;"/>'+
                                                               '</a>&nbsp;&nbsp;&nbsp;&nbsp;'+
  	                                                           '<span><a href="javascript:RAS('+m+')" id="effacer_'+i+'"><img style="display: inline;" src="/simens_derniereversion/public/images_icons/doctor_16.PNG" alt="Constantes" title="Effacer" style="float:left;border:transparent;"/></a></span></th>'+
  	                                                    '</tr></table>';
  	document.getElementById('leschamps_'+i).innerHTML += (i < 9) ? '<span id="leschamps_'+i2+'"><a href="javascript:create_champ('+i2+')"><img style="display: inline; float:left; border:transparent; padding-left: 20px;" src="/simens_derniereversion/public/images_icons/add.PNG" alt="Constantes" title="Ajouter un m&eacute;dicament"/></a></span>' : '';

  }


/* <!-- ************** FIN du script **************** -->
   <!-- ************** FIN du script **************** -->
   <!-- ************** FIN du script **************** --> */