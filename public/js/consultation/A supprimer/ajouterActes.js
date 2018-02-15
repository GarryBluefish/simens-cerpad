function creerLalisteActe ($listeDesElements) {
    	var index = $("LesActes").length; 
			        $liste = "<div id='Acte_"+(index+1)+"'>"+
				             "<LesActes>"+
				             "<table class='table table-bordered' id='Examen' style='margin-bottom: 0px; width: 100%;'>"+
                             "<tr style='width: 100%;'>" +
                             
                             "<th style='width: 4%;'>"+
                             "<label style='width: 100%; margin-top: 10px; margin-left: 5px; font-weight: bold; font-family: police2; font-size: 14px;' >"+(index+1)+"<span id='element_label'></span></label>" +
                             "</th >"+
                             
                             "<th id='SelectActe_"+(index+1)+"' style='width: 35%;'>"+
                             "<select   onchange='getTarifActe(this.value,"+(index+1)+")'  style='width: 100%; margin-top: 3px; margin-bottom: 0px; font-size: 13px;' name='acte_name_"+(index+1)+"' id='acte_name_"+(index+1)+"'>"+
			                 "<option value='' > -- S&eacute;l&eacute;ctionner un examen -- </option>";
                             for(var i = 1 ; i < $listeDesElements.length ; i++){
                            	 if($listeDesElements[i]){
                    $liste +="<option value='"+i+"'>"+$listeDesElements[i]+"</option>";
                            	 }
                             }   
                    $liste +="</select>"+                           
                             "</th>"+
                             
                             "<th id='tarifActe_"+(index+1)+"'  style='width: 15%;'  >"+
                             "<input id='tarifActe"+(index+1)+"' readonly='true' name='tarifActe_"+(index+1)+"' type='text' style='background: #f8f8f8; text-align: right; padding-right: 10px;  width: 100%; margin-top: 3px; height: 30px; margin-bottom: 0px; font-size: 15px; padding-left: 10px; font-family: Iskoola Pota; font-size: 20px; font-weight: bold;' >" +
                             "</th >"+
                             
                             "<th id='noteActe_"+(index+1)+"'  style='width: 37%;'  >"+
                             "<input name='noteActe_"+(index+1)+"' type='text' style='width: 100%; margin-top: 3px; height: 30px; margin-bottom: 0px; font-size: 15px; padding-left: 10px;' >" +
                             "</th >"+
                             
                             "<th id='iconeActe_supp_vider' style='width: 9%;'  >"+
                             "<a id='supprimer_acte_selectionne_"+ (index+1) +"'  style='width:50%;' >"+
                             "<img class='supprimerActe' style='margin-left: 5px; margin-top: 10px; cursor: pointer;' src='../images/images/sup.png' title='supprimer' />"+
                             "</a>"+
                             
                             "<a id='vider_acte_selectionne_"+ (index+1) +"'  style='width:50%;' >"+
                             "<img class='viderActe' style='margin-left: 15px; margin-top: 10px; cursor: pointer;' src='../images_icons/gomme.png' title='vider' />"+
                             "</a>"+
                             "</th >"+
                             
                             "</tr>" +
                             "</table>" +
                             "</LesActes>" +
                             "</div>"+
                             
                             
                             "<script>"+
                                "$('#supprimer_acte_selectionne_"+ (index+1) +"').click(function(){ " +
                                		"supprimer_acte_selectionne("+ (index+1) +"); });" +
                                				
                                "$('#vider_acte_selectionne_"+ (index+1) +"').click(function(){ " +
                                		"vider_acte_selectionne("+ (index+1) +"); });" +
                             "</script>";
                    
                    //AJOUTER ELEMENT SUIVANT
                    $("#Acte_"+index).after($liste);
                    
                    //CACHER L'ICONE AJOUT QUAND ON A CINQ LISTES
                    if((index+1) == 6){
                    	$("#ajouter_acte").toggle(false);
                    }
                    
                    //AFFICHER L'ICONE SUPPRIMER QUAND ON A DEUX LISTES ET PLUS
                    if((index+1) == 2){
                    	$("#supprimer_acte").toggle(true);
                    }
}


//NOMBRE DE LISTE AFFICHEES
function nbListeActe () {
	return $("LesActes").length;
}


//SUPPRIMER LE DERNIER ELEMENT
	//Au début on cache la suppression
	$("#supprimer_acte").click(function(){
		//ON PEUT SUPPRIMER QUAND C'EST PLUS DE DEUX LISTE
		if(nbListeActe () >  1){$("#Acte_"+nbListeActe ()).remove();}
		//ON CACHE L'ICONE SUPPRIMER QUAND ON A UNE LIGNE
		if(nbListeActe () == 1) {
			$("#supprimer_acte").toggle(false);
			$(".supprimerActe" ).replaceWith(
			  "<img class='supprimerActe' style='margin-left: 5px; margin-top: 10px;' src='../images/images/sup2.png' />"
			);
		}
		//Afficher L'ICONE AJOUT QUAND ON A CINQ LIGNES
		if((nbListeActe()+1) == 6){
			$("#ajouter_acte").toggle(true);
		}    
		Event.stopPropagation();
	});


//FONCTION INITIALISATION (Par défaut)
function partDefautActe (Liste, n) { 
	var i = 0;
	for( i ; i < n ; i++){
		creerLalisteActe(Liste);
	}
	if(n == 1){
		$(".supprimerActe" ).replaceWith(
				"<img class='supprimerActe' style='margin-left: 5px; margin-top: 10px;' src='../images/images/sup2.png' />"
			);
	}
	$('#ajouter_acte').click(function(){ 
		creerLalisteActe(Liste);
		if(nbListeActe() == 2){
		$(".supprimerActe" ).replaceWith(
				"<img class='supprimerActe' style='margin-left: 5px; margin-top: 10px; cursor: pointer;' src='../images/images/sup.png' title='Supprimer' />"
		);
		}
	});

	//AFFICHER L'ICONE SUPPRIMER QUAND ON A DEUX LISTES ET PLUS
    if(nbListeActe () > 1){
    	$("#supprimer_acte").toggle(true);
    } else {
    	$("#supprimer_acte").toggle(false);
      }
}

//SUPPRIMER ELEMENT SELECTIONNER
function supprimer_acte_selectionne(id) { 

	for(var i = (id+1); i <= nbListeActe(); i++ ){
		var element = $('#acte_name_'+i).val();
		$("#SelectActe_"+(i-1)+" option[value='"+element+"']").attr('selected','selected');
		
		var note = $('#noteActe_'+i+' input').val();
		$("#noteActe_"+(i-1)+" input").val(note);
		
		var note = $('#tarifActe_'+i+' input').val();
		$("#tarifActe_"+(i-1)+" input").val(note);
	}

	if(nbListeActe() <= 2 && id <= 2){
		$(".supprimerActe" ).replaceWith(
			"<img class='supprimerActe' style='margin-left: 5px; margin-top: 10px;' src='../images/images/sup2.png' />"
		);
	}
	if(nbListeActe() != 1) {
		$('#Acte_'+nbListeActe()).remove();
	}
	if(nbListeActe() == 1) {
		$("#supprimer_acte").toggle(false);
	}
	if((nbListeActe()+1) == 6){
		$("#ajouter_acte").toggle(true);
	}
   
}

//VIDER LES CHAMPS DE L'ELEMENT SELECTIONNER
function vider_acte_selectionne(id) {
	$("#SelectActe_"+id+" option[value='']").attr('selected','selected');
	$("#noteActe_"+id+" input").val("");
	$("#tarifActe_"+id+" input").val("");
}

//CHARGEMENT DES ELEMENTS SELECTIONNES POUR LA MODIFICATION
//CHARGEMENT DES ELEMENTS SELECTIONNES POUR LA MODIFICATION
//CHARGEMENT DES ELEMENTS SELECTIONNES POUR LA MODIFICATION
function desactiverResutatsActe () {
	//ON CACHE TOUT
	var i = 1;
	for(i; i <= 19; i++){
		$('#acte_'+i).toggle(false); 
	}
}

function chargementModificationActe (index, element, tarif, note) {
	$("#SelectActe_"+(index+1)+" option[value='"+element+"']").attr('selected','selected'); 
	$("#tarifActe_"+(index+1)+" input").val(tarif);
	$("#noteActe_"+(index+1)+" input").val(note);
	
	//ON AFFICHE UNIQUEMENT CEUX AYANT ETE DEMANDE
	setTimeout(function(){ $('#acte_'+element).toggle(true) },1000); 
}

var base_url = window.location.toString();
var tabUrl = base_url.split("public");

//VALIDATION VALIDATION VALIDATION
//********************* EXAMEN MORPHOLOGIQUE *****************************
//********************* EXAMEN MORPHOLOGIQUE *****************************
//********************* EXAMEN MORPHOLOGIQUE *****************************

function ValiderDemandeActe(){
	//Au debut on affiche pas le bouton modifier
	$("#bouton_Acte_modifier_demande").toggle(false);
	//Au debut on affiche le bouton valider
	$("#bouton_Acte_valider_demande").toggle(true);
	
	$("#bouton_Acte_valider_demande button").click(function(){
		//ON CACHE TOUT
    	desactiverResutatsActe();

    	//RECUPERATION DES DONNEES DU TABLEAU
		var id_cons = $('#id_cons').val();
		var examensActe = [];
		var notesActe = [];
		for(var i = 1, j = 1; i <= nbListeActe(); i++ ){
			if($('#acte_name_'+i).val()) {
				examensActe[j] = $('#acte_name_'+i).val();
				notesActe[j] = $('#noteActe_'+i+' input').val();
				j++;
				
				//ON AFFICHE UNIQUEMENT CEUX AYANT ETE DEMANDE
    			$('#acte_'+$('#acte_name_'+i).val()).toggle(true); 
			}
		}
		
		return false;
		
		$.ajax({
	        type: 'POST',
	        url: tabUrl[0]+'public/consultation/demande-acte',
	        data: {'id_cons':id_cons, 'examensActe': examensActe, 'notesActe':notesActe},
	        success: function() {
	        	
	        	for(var i = 1; i <= nbListeActe(); i++ ){
	    			$('#acte_name_'+i).attr('disabled',true); $('#acte_name_'+i).css({'background':'#f8f8f8'});
	    			$("#noteActe_"+i+" input").attr('disabled',true); $("#noteActe_"+i+" input").css({'background':'#f8f8f8'});
	    		}
	            
	    		$("#controls_acte div").toggle(false);
	    		$("#iconeActe_supp_vider a img").toggle(false);
	    		$("#bouton_Acte_modifier_demande").toggle(true);
	    		$("#bouton_Acte_valider_demande").toggle(false);
	    		return false;
	      },
	      
	      error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
	      dataType: "html"
		});
		
		return false;

	});
	
	$("#bouton_Acte_modifier_demande").click(function(){
		for(var i = 1; i <= nbListeActe(); i++ ){
			$('#acte_name_'+i).attr('disabled',false); $('#acte_name_'+i).css({'background':'white'});
			$("#noteActe_"+i+" input").attr('disabled',false); $("#noteActe_"+i+" input").css({'background':'white'});
		}
		$("#controls_acte div").toggle(true);
		if(nbListeActe() == 1){
			$("#supprimer_acte").toggle(false);
		}
		$("#iconeActe_supp_vider a img").toggle(true);
		$("#bouton_Acte_modifier_demande").toggle(false);
		$("#bouton_Acte_valider_demande").toggle(true);
		return false;
	});
	
}


function desactivationChamps(){
	
	for(var i = 1; i <= nbListeActe(); i++ ){
		$('#acte_name_'+i).attr('disabled',true).css({'background':'#f8f8f8'}); 
		$("#noteActe_"+i+" input").attr('disabled',true).css({'background':'#f8f8f8'});
	}
	$("#iconeActe_supp_vider a img").toggle(false);
	
}

function getTarifActe(id, pos){

	$.ajax({
		type: 'POST',
		url: tabUrl[0]+'public/consultation/tarifacte',
		data:{'id':id},
		success: function(data) {    
			var result = jQuery.parseJSON(data);  
			$("#tarifActe"+pos).val(result);
		},
    
		error:function(e){ console.log(e); alert("Une erreur interne est survenue!"); },
		dataType: "html"
	});

}