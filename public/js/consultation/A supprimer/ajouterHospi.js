function creerLaliste ($listeDesElements) {
    	var index = $("hassim").length;
			        $liste = "<div id='Element_"+(index+1)+"'>"+
				             "<hassim>"+
				             "<table class='table table-bordered' id='Element' style='margin-bottom: 0px; width: 100%;'>"+
                             "<tr style='width: 100%;'>" +
                             
                             "<th style='width: 4%;'>"+
                             "<label style='width: 100%; margin-top: 10px; margin-left: 5px; font-weight: bold; font-family: police2; font-size: 14px;' >"+(index+1)+"<span id='element_label'></span></label>" +
                             "</th >"+
                             
                             "<th id='SelectElement_"+(index+1)+"' style='width: 28%;'>"+
                             "<select style='width: 100%; margin-top: 3px; margin-bottom: 0px; font-size: 13px;' class='validate[required]' id='element_name_"+(index+1)+"'>"+
			                 "<option value=''> -- S&eacute;l&eacute;ctionner un examen -- </option>";
                             for(var i = 1 ; i < $listeDesElements.length ; i++){
                            	 if($listeDesElements[i]){
                    $liste +="<option value='"+i+"'>"+$listeDesElements[i]+"</option>";
                            	 }
                             }   
                    $liste +="</select>"+                           
                             "</th>"+
                             
                             "<th id='note_"+(index+1)+"' style='width: 59%;'  >"+
                             "<input type='text' style='width: 100%; margin-top: 3px; height: 30px; margin-bottom: 0px; font-size: 15px; padding-left: 10px;' >" +
                             "</th >"+
                             
                             "<th id='icone_supp_vider' style='width: 9%;'  >"+
                             "<a id='supprimer_element_selectionne_"+ (index+1) +"'  style='width:50%;' >"+
                             "<img class='supprimer' style='margin-left: 5px; margin-top: 10px; cursor: pointer;' src='../images/images/sup.png' title='supprimer' />"+
                             "</a>"+
                             
                             "<a id='vider_element_selectionne_"+ (index+1) +"'  style='width:50%;' >"+
                             "<img class='vider' style='margin-left: 15px; margin-top: 10px; cursor: pointer;' src='../images_icons/gomme.png' title='vider' />"+
                             "</a>"+
                             "</th >"+
                             
                             "</tr>" +
                             "</table>" +
                             "</hassim>" +
                             "</div>"+
                             
                             
                             "<script>"+
                                "$('#supprimer_element_selectionne_"+ (index+1) +"').click(function(){ " +
                                		"supprimer_element_selectionne("+ (index+1) +"); });" +
                                				
                                "$('#vider_element_selectionne_"+ (index+1) +"').click(function(){ " +
                                		"vider_element_selectionne("+ (index+1) +"); });" +
                             "</script>";
                    
                    //AJOUTER ELEMENT SUIVANT
                    $("#Element_"+index).after($liste);
                    
                    //NOMBRE DE LISTES
                    $("#Element_0").html("<input type='hidden' id='nbElement' value='"+(index+1)+"'>");
                    
                    //CACHER L'ICONE AJOUT QUAND ON A CINQ LISTES
                    if((index+1) == 5){
                    	$("#ajouter_element").toggle(false);
                    }
                    
                    //AFFICHER L'ICONE SUPPRIMER QUAND ON A DEUX LISTES ET PLUS
                    if((index+1) == 2){
                    	$("#supprimer_element").toggle(true);
                    }
}

//NOMBRE DE LISTE AFFICHEES
function nbListe () {
	return $("hassim").length;
}

//SUPPRIMER LE DERNIER ELEMENT
$(function () {
	//Au début on cache la suppression
	$("#supprimer_element").click(function(){
		//ON PEUT SUPPRIMER QUAND C'EST PLUS DE DEUX LISTE
		if(nbListe () >  1){$("#Element_"+nbListe ()).remove();}
		//NOMBRE DE LISTES
        $("#Element_0").html("<input type='hidden' id='nbElement' value='"+nbListe()+"'>");
		//ON CACHE L'ICONE SUPPRIMER QUAND ON A UNE LIGNE
		if(nbListe () == 1) {
			$("#supprimer_element").toggle(false);
			$(".supprimer" ).replaceWith(
			  "<img class='supprimer' style='margin-left: 5px; margin-top: 10px;' src='../images/images/sup2.png' />"
			);
		}
		//Afficher L'ICONE AJOUT QUAND ON A CINQ LIGNES
		if((nbListe()+1) == 5){
			$("#ajouter_element").toggle(true);
		}    
		Event.stopPropagation();
	});
});

//FONCTION INITIALISATION (Par défaut)
function partDefaut (Liste, n) {
	var i = 0;
	for( i ; i < n ; i++){
		creerLaliste(Liste);
	}
	if(n == 1){
		$(".supprimer" ).replaceWith(
				"<img class='supprimer' style='margin-left: 5px; margin-top: 10px;' src='../images/images/sup2.png' />"
			);
	}
	$('#ajouter_element').click(function(){ 
		creerLaliste(Liste);
		if(nbListe() == 2){
		$(".supprimer" ).replaceWith(
				"<img class='supprimer' style='margin-left: 5px; margin-top: 10px; cursor: pointer;' src='../images/images/sup.png' title='Supprimer' />"
		);
		}
	});

	//AFFICHER L'ICONE SUPPRIMER QUAND ON A DEUX LISTES ET PLUS
    if(nbListe () > 1){
    	$("#supprimer_element").toggle(true);
    } else {
    	$("#supprimer_element").toggle(false);
      }
}

//SUPPRIMER ELEMENT SELECTIONNER
function supprimer_element_selectionne(id) { 

	for(var i = (id+1); i <= nbListe(); i++ ){
		var element = $('#element_name_'+i).val();
		$("#SelectElement_"+(i-1)+" option[value='"+element+"']").attr('selected','selected');
		
		var note = $('#note_'+i+' input').val();
		$("#note_"+(i-1)+" input").val(note);
	}

	if(nbListe() <= 2 && id <= 2){
		$(".supprimer" ).replaceWith(
			"<img class='supprimer' style='margin-left: 5px; margin-top: 10px;' src='../images/images/sup2.png' />"
		);
	}
	if(nbListe() != 1) {
		$('#Element_'+nbListe()).remove();
	}
	if(nbListe() == 1) {
		$("#supprimer_element").toggle(false);
	}
	if((nbListe()+1) == 5){
		$("#ajouter_element").toggle(true);
	}
   
}

//VIDER LES CHAMPS DE L'ELEMENT SELECTIONNER
function vider_element_selectionne(id) {
	$("#SelectElement_"+id+" option[value='']").attr('selected','selected');
	$("#note_"+id+" input").val("");
}
//CHARGEMENT DES ELEMENTS SELECTIONNES POUR LA MODIFICATION
//CHARGEMENT DES ELEMENTS SELECTIONNES POUR LA MODIFICATION
//CHARGEMENT DES ELEMENTS SELECTIONNES POUR LA MODIFICATION

function chargementModification (index , element , note) { 
	$("#SelectElement_"+(index+1)+" option[value='"+element+"']").attr('selected','selected'); 
	$("#note_"+(index+1)+" input").val(note);
}

var base_url = window.location.toString();
var tabUrl = base_url.split("public");

//VALIDATION VALIDATION VALIDATION
//********************* EXAMEN MORPHOLOGIQUE *****************************
//********************* EXAMEN MORPHOLOGIQUE *****************************
//********************* EXAMEN MORPHOLOGIQUE *****************************
function ValiderDemande(){
$(function(){
	//Au debut on affiche pas le bouton modifier
	$("#bouton_morpho_modifier_demande").toggle(false);
	//Au debut on affiche le bouton valider
	$("#bouton_morpho_valider_demande").toggle(true);
	
	//Au debut on desactive tous les champs
	for(var i = 1; i <= nbListe(); i++ ){
		$('#element_name_'+i).attr('disabled',false);
		$("#note_"+i+" input").attr('disabled',false);
	}
	
	$("#bouton_morpho_valider_demande button").click(function(){ 
		//RECUPERATION DES DONNEES DU TABLEAU
		var id_cons = $('#id_cons').val();
		var examens = [];
		var notes = [];
		for(var i = 1, j = 1; i <= nbListe(); i++ ){
			if($('#element_name_'+i).val()) {
				examens[j] = $('#element_name_'+i).val();
				notes[j] = $('#note_'+i+' input').val();
				j++;
			}
		}
		
		
//		$.ajax({
//	        type: 'POST',
//	        url: tabUrl[0]+'public/consultation/demande-examen',
//	        data: {'id_cons':id_cons, 'examens': examens, 'notes':notes},
//	        success: function(data) {
//	            //var result = jQuery.parseJSON(data); 
//	            //alert(result);
//	        	
//	        	//ON CACHE TOUT
//	        	$('.imageRadio').toggle(false); 
//        		$('.imageEchographie').toggle(false);
//        		$('.imageIRM').toggle(false);
//        		$('.imageScanner').toggle(false); 
//        		$('.imageFibroscopie').toggle(false);
//        		$('.bouton_valider_examen_morpho').toggle(false);
//        		
//	        	//ON AFFICHE UNIQUEMENT CEUX AYANT ETE DEMANDE
//	        	for(var k = 1; k<=nbListe(); k++){
//	        		if(examens[k] == 8){ $('.imageRadio').toggle(true); $('.bouton_valider_examen_morpho').toggle(true);} 
//	        		if(examens[k] == 9){ $('.imageEchographie').toggle(true); $('.bouton_valider_examen_morpho').toggle(true);} 
//	        		if(examens[k] == 10){ $('.imageIRM').toggle(true); $('.bouton_valider_examen_morpho').toggle(true);} 
//	        		if(examens[k] == 11){ $('.imageScanner').toggle(true); $('.bouton_valider_examen_morpho').toggle(true);} 
//	        		if(examens[k] == 12){ $('.imageFibroscopie').toggle(true); $('.bouton_valider_examen_morpho').toggle(true);} 
//	        	}
//	        	
//	        	
	            for(var i = 1; i <= nbListe(); i++ ){
	    			$('#element_name_'+i).attr('disabled',true); $('#element_name_'+i).css({'background':'#f8f8f8'});
	    			$("#note_"+i+" input").attr('disabled',true); $("#note_"+i+" input").css({'background':'#f8f8f8'});
	    		}
	    		$("#controls_element div").toggle(false);
	    		$("#icone_supp_vider a img").toggle(false);
	    		$("#bouton_morpho_modifier_demande").toggle(true);
	    		$("#bouton_morpho_valider_demande").toggle(false);
	    		//return false;
//	      },
//	      error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
//	      dataType: "html"
//		});
	    alert('en cours de developpement'); exit();
		return false;
	});
	
	$("#bouton_morpho_modifier_demande").click(function(){
		for(var i = 1; i <= nbListe(); i++ ){
			$('#element_name_'+i).attr('disabled',false); $('#element_name_'+i).css({'background':'white'});
			$("#note_"+i+" input").attr('disabled',false); $("#note_"+i+" input").css({'background':'white'});
		}
		$("#controls_element div").toggle(true);
		if(nbListe() == 1){
			$("#supprimer_element").toggle(false);
		}
		$("#icone_supp_vider a img").toggle(true);
		$("#bouton_morpho_modifier_demande").toggle(false);
		$("#bouton_morpho_valider_demande").toggle(true);
		return false;
	});
});
}