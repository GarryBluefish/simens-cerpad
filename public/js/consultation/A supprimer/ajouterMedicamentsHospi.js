function chargerMedicaments(myArrayMedicament, myArrayForme, myArrayTypeQuantiteMedicament){
	
$(function(){
  $( "LesMedicaments khass input" ).autocomplete({
	  source: myArrayMedicament
  });
  
  $( "LesMedicaments khassForme input" ).autocomplete({
	  source: myArrayForme
  });
  
  $( "LesMedicaments khassQuantite input" ).autocomplete({
	  source: myArrayTypeQuantiteMedicament
  });
});

}

function creerLalisteMedicament ($listeDesElements, $ListeForme, $ListeQuantite) {
    	var index = $("LesMedicaments").length; 
			        $liste = "<div id='Medicament_"+(index+1)+"'>"+
				             "<LesMedicaments>"+
				             "<table class='table table-bordered' style='margin-bottom: 0px; width: 100%;'>"+
                             "<tr style='width: 100%;'>" +
                             
                             "<th style='width: 4%;'>"+
                             "<label style='width: 100%; margin-top: 10px; margin-left: 5px; font-weight: bold; font-family: police2; font-size: 14px;' >"+(index+1)+"</label>" +
                             "</th >"+
                             
                             "<th id='SelectMedicament_"+(index+1)+"' style='width: 29%;'>";
			        $liste +="<khass> <input style='width: 100%; margin-top: 3px; margin-bottom: 0px; font-size: 13px; height: 30px; font-size: 15px; padding-left: 10px;' id='medicament_0"+(index+1)+"' name='medicament_0"+(index+1)+"' type='text' > </khass>";
                    $liste +="</th>"+
                             
                             "<th id='noteMedicament_"+(index+1)+"' style='width: 29%;'  >"+
                             "<khassForme><input type='text' id='forme_"+(index+1)+"' name='forme_"+(index+1)+"' style='width: 100%; margin-top: 3px; height: 30px; margin-bottom: 0px; font-size: 15px; padding-left: 10px;' > </khassForme>" +
                             "</th >"+
                             
                             "<th id='noteMedicament2_"+(index+1)+"' style='width: 29%;'  >"+
                             "<input type='text' id='nb_medicament_"+(index+1)+"' name='nb_medicament_"+(index+1)+"' style=' float: left; width: 13%; margin-top: 3px; height: 30px; margin-bottom: 0px; font-size: 15px; padding-left: 5px;' >"+
                             "<div id='increm_decrem' style='float: left; height: 30px; width: 7%;'> " +
                             "<img id='incrementer_"+(index+1)+"' style='cursor:pointer; position:absolute; height: 16px; width: 16px; margin-top: 1px; margin-bottom: 0px; font-size: 15px;' src='../images_icons/increment2.png'/> " +
                             "<img id='decrementer_"+(index+1)+"' style='cursor:pointer; position:absolute; height: 16px; width: 16px; margin-top: 19px; margin-bottom: 0px; font-size: 15px; padding-left: 1px;' src='../images_icons/decrement2.png'/> " +
                             "</div>"+
                             "<khassQuantite><input type='text' id='quantite_"+(index+1)+"' name='quantite_"+(index+1)+"' style='float: left; width: 80%; margin-top: 3px; height: 30px; margin-bottom: 0px; font-size: 15px; padding-left: 10px;' > </khassQuantite>" +
                             "</th >"+
                             
                             "<th id='iconeMedicament_supp_vider' style='width: 9%;'  >"+
                             "<a id='supprimer_medicament_selectionne_"+ (index+1) +"'  style='width:50%;' >"+
                             "<img class='supprimerMedicament' style='margin-left: 5px; margin-top: 10px; cursor: pointer;' src='../images/images/sup.png' title='supprimer' />"+
                             "</a>"+
                             
                             "<a id='vider_medicament_selectionne_"+ (index+1) +"'  style='width:50%;' >"+
                             "<img class='viderMedicament' style='margin-left: 15px; margin-top: 10px; cursor: pointer;' src='../images_icons/gomme.png' title='vider' />"+
                             "</a>"+
                             "</th >"+
                             
                             "</tr>" +
                             "</table>" +
                             "</LesMedicaments>" +
                             "</div>"+
                             
                             
                             "<script>"+
                                "$('#supprimer_medicament_selectionne_"+ (index+1) +"').click(function(){ " +
                                		"supprimer_medicament_selectionne("+ (index+1) +"); });" +
                                				
                                "$('#vider_medicament_selectionne_"+ (index+1) +"').click(function(){ " +
                                		"vider_medicament_selectionne("+ (index+1) +"); });" +
                             "</script>";
                    
                    //AJOUTER ELEMENT SUIVANT
                    $("#Medicament_"+index).after($liste);
                    
                    //CACHER L'ICONE AJOUT QUAND ON A CINQ LISTES
                    if((index+1) == 6){
                    	$("#ajouter_medicament").toggle(false);
                    }
                    
                    //AFFICHER L'ICONE SUPPRIMER QUAND ON A DEUX LISTES ET PLUS
                    if((index+1) == 2){
                    	$("#supprimer_medicament").toggle(true);
                    }
                    
                    //CHARGEMENT DES AUTO-COMPLETIONS
                    chargerMedicaments($listeDesElements, $ListeForme, $ListeQuantite);
                    
                    var $nb = 0;
                    //INCREMENTER OU DECREMENTER AU CLICK
                    $('#incrementer_'+(index+1)).click(function(){ 
                    	if($nb < 10){
                    		$("#nb_medicament_"+(index+1)).val(++$nb); 
                    	}
                    });
                    $('#decrementer_'+(index+1)).click(function(){
                    	if($nb > 1){
                    		$("#nb_medicament_"+(index+1)).val(--$nb); 
                    	}
                    });
}

//NOMBRE DE LISTE AFFICHEES
function nbListeMedicaments () {
	return $("LesMedicaments").length;
}


//SUPPRIMER LE DERNIER ELEMENT
$(function () {
	//Au début on cache la suppression
	$("#supprimer_medicament").click(function(){
		//ON PEUT SUPPRIMER QUAND C'EST PLUS DE DEUX LISTE
		if(nbListeMedicaments () >  1){$("#Medicament_"+nbListeMedicaments ()).remove();}
		//ON CACHE L'ICONE SUPPRIMER QUAND ON A UNE LIGNE
		if(nbListeMedicaments () == 1) {
			$("#supprimer_medicament").toggle(false);
			$(".supprimerMedicament" ).replaceWith(
			  "<img class='supprimerMedicament' style='margin-left: 5px; margin-top: 10px;' src='../images/images/sup2.png' />"
			);
		}
		//Afficher L'ICONE AJOUT QUAND ON A CINQ LIGNES
		if((nbListeMedicaments()+1) == 6){
			$("#ajouter_medicament").toggle(true);
		}    
		Event.stopPropagation();
	});
});


//FONCTION INITIALISATION (Par défaut)
function partDefautMedicament (Liste, ListeForme, ListeQuantite, n) { 
	var i = 0;
	for( i ; i < n ; i++){
		creerLalisteMedicament(Liste, ListeForme, ListeQuantite);
	}
	if(n == 1){
		$(".supprimerMedicament" ).replaceWith(
				"<img class='supprimerMedicament' style='margin-left: 5px; margin-top: 10px;' src='../images/images/sup2.png' />"
			);
	}
	$('#ajouter_medicament').click(function(){ 
		creerLalisteMedicament(Liste, ListeForme, ListeQuantite);
		if(nbListeMedicaments() == 2){
		$(".supprimerMedicament" ).replaceWith(
				"<img class='supprimerMedicament' style='margin-left: 5px; margin-top: 10px; cursor: pointer;' src='../images/images/sup.png' title='Supprimer' />"
		);
		}
	});

	//AFFICHER L'ICONE SUPPRIMER QUAND ON A DEUX LISTES ET PLUS
    if(nbListeMedicaments () > 1){
    	$("#supprimer_medicament").toggle(true);
    } else {
    	$("#supprimer_medicament").toggle(false);
      }
}

//SUPPRIMER ELEMENT SELECTIONNER
function supprimer_medicament_selectionne(id) { 

	for(var i = (id+1); i <= nbListeMedicaments(); i++ ){
		var element = $('#medicament_0'+i).val();
		$("#medicament_0"+(i-1)).val(element);
		
		var note = $('#noteMedicament_'+i+' input').val();
		$("#noteMedicament_"+(i-1)+" input").val(note);
		
		var note2 = $('#noteMedicament2_'+i+'  input').val();
		$("#noteMedicament2_"+(i-1)+"  input").val(note2);
		
		var note2 = $('#noteMedicament2_'+i+' khassQuantite input').val();
		$("#noteMedicament2_"+(i-1)+" khassQuantite input").val(note2);
	}

	if(nbListeMedicaments() <= 2 && id <= 2){
		$(".supprimerMedicament" ).replaceWith(
			"<img class='supprimerMedicament' style='margin-left: 5px; margin-top: 10px;' src='../images/images/sup2.png' />"
		);
	}
	if(nbListeMedicaments() != 1) {
		$('#Medicament_'+nbListeMedicaments()).remove();
	}
	if(nbListeMedicaments() == 1) {
		$("#supprimer_medicament").toggle(false);
	}
	if((nbListeMedicaments()+1) == 6){
		$("#ajouter_medicament").toggle(true);
	}
   
}

//VIDER LES CHAMPS DE L'ELEMENT SELECTIONNER
function vider_medicament_selectionne(id) {
	$("#medicament_0"+id).val("");
	$("#noteMedicament_"+id+" input").val("");
	$("#noteMedicament2_"+id+" input").val("");
}


var base_url = window.location.toString();
var tabUrl = base_url.split("public");
//VALIDATION VALIDATION VALIDATION
//********************* EXAMEN MORPHOLOGIQUE *****************************
//********************* EXAMEN MORPHOLOGIQUE *****************************
//********************* EXAMEN MORPHOLOGIQUE *****************************

function ValiderOrdonnance(){
$(function(){
	//Au debut on affiche pas le bouton modifier
	$("#bouton_Medicament_modifier_demande").toggle(false);
	//Au debut on affiche le bouton valider
	$("#bouton_Medicament_valider_demande").toggle(true);
	
	$("#bouton_Medicament_valider_demande button").click(function(){ 
//		//RECUPERATION DES DONNEES DU TABLEAU
//		var id_cons = $('#id_cons').val();
//		var examensBio = [];
//		var notesBio = [];
//		for(var i = 1, j = 1; i <= nbListeMedicaments(); i++ ){
//			if($('#medicament_0'+i).val()) {
//				examensBio[j] = $('#medicament_0'+i).val();
//				notesBio[j] = $('#noteMedicament_'+i+' input').val();
//				j++;
//			}
//		}
//		
//		$.ajax({
//	        type: 'POST',
//	        url: tabUrl[0]+'public/consultation/demande-examen-biologique',
//	        data: {'id_cons':id_cons, 'examensBio': examensBio, 'notesBio':notesBio},
//	        success: function(data) {

		        for(var i = 1; i <= nbListeMedicaments(); i++ ){
	    			$('#medicament_0'+i).attr('disabled',true); $('#medicament_0'+i).css({'background':'#f8f8f8'});
	    			$("#noteMedicament_"+i+" input").attr('disabled',true); $("#noteMedicament_"+i+" input").css({'background':'#f8f8f8'});
	    			$("#noteMedicament2_"+i+" input").attr('disabled',true); $("#noteMedicament2_"+i+" input").css({'background':'#f8f8f8'});
	    		}
	    		$("#controls_medicament div").toggle(false);
	    		$("#iconeMedicament_supp_vider a img").toggle(false);
	    		$("#bouton_Medicament_modifier_demande").toggle(true);
	    		$("#bouton_Medicament_valider_demande").toggle(false);
	    		$("#increm_decrem img").toggle(false);
	    		return false;
//	      },
//	      error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
//	      dataType: "html"
//		});
//		return false;
	});
	
	$("#ordonnance").click(function(){ 
		for(var i = 1; i <= nbListeMedicaments(); i++ ){
			$('#medicament_0'+i).attr('disabled',false); $('#medicament_0'+i).css({'background':'white'});
			$("#noteMedicament_"+i+" input").attr('disabled',false); $("#noteMedicament_"+i+" input").css({'background':'white'});
			$("#noteMedicament2_"+i+" input").attr('disabled',false); $("#noteMedicament2_"+i+" input").css({'background':'white'});
		}
		setTimeout(function(){
			for(var i = 1; i <= nbListeMedicaments(); i++ ){
				$('#medicament_0'+i).attr('disabled',true); $('#medicament_0'+i).css({'background':'#f8f8f8'});
				$("#noteMedicament_"+i+" input").attr('disabled',true); $("#noteMedicament_"+i+" input").css({'background':'#f8f8f8'});
				$("#noteMedicament2_"+i+" input").attr('disabled',true); $("#noteMedicament2_"+i+" input").css({'background':'#f8f8f8'});
			}
			$("#controls_medicament div").toggle(false);
			$("#iconeMedicament_supp_vider a img").toggle(false);
			$("#bouton_Medicament_modifier_demande").toggle(true);
			$("#bouton_Medicament_valider_demande").toggle(false);
			$("#increm_decrem img").toggle(false);
		}, 1500);
	});
	
	$("#bouton_Medicament_modifier_demande").click(function(){
		for(var i = 1; i <= nbListeMedicaments(); i++ ){
			$('#medicament_0'+i).attr('disabled',false); $('#medicament_0'+i).css({'background':'white'});
			$("#noteMedicament_"+i+" input").attr('disabled',false); $("#noteMedicament_"+i+" input").css({'background':'white'});
			$("#noteMedicament2_"+i+" input").attr('disabled',false); $("#noteMedicament2_"+i+" input").css({'background':'white'});
		}
		$("#controls_medicament div").toggle(true);
		if(nbListeMedicaments() == 1){
			$("#supprimer_medicament").toggle(false);
		}
		$("#iconeMedicament_supp_vider a img").toggle(true);
		$("#bouton_Medicament_modifier_demande").toggle(false);
		$("#bouton_Medicament_valider_demande").toggle(true);
		$("#increm_decrem img").toggle(true);
		return false;
	});
});
}
