var $isupp = 1;
function creerLalisteActe ($listeDesElements) {
    	var index = $("LesActes").length; 
			        $liste = "<div id='Acte_"+(index+1)+"'>"+
				             "<LesActes>"+
				             "<table class='table table-bordered' id='Examen' style='margin-bottom: 0px; width: 100%;'>"+
                             "<tr style='width: 100%;'>" +
                             
                             "<th style='width: 4%;'>"+
                             "<label style='width: 100%; margin-top: 10px; margin-left: 5px; font-weight: bold; font-family: police2; font-size: 14px;' >"+(index+1)+"<span id='element_label'></span></label>" +
                             "</th >"+
                             
                             "<th id='SelectTypeAnalyse_"+(index+1)+"' style='width: 25%;'>"+
                             "<select  onchange='getListeAnalyses(this.value,"+(index+1)+")'  style='width: 100%; margin-top: 3px; margin-bottom: 0px; font-size: 13px;' name='type_analyse_name_"+(index+1)+"' id='type_analyse_name_"+(index+1)+"' class='type_analyse_name_"+(index+1)+"'>"+
			                 "<option value='' > --- S&eacute;l&eacute;ctionner un type ---  </option>";
                             for(var i = 1 ; i < $listeDesElements.length ; i++){
                            	 if($listeDesElements[i]){
                    $liste +="<option value='"+i+"'>"+$listeDesElements[i]+"</option>";
                            	 }
                             }   
                    $liste +="</select>"+                           
                             "</th>"+
                             
                             
                             
                             "<th id='SelectAnalyse_"+(index+1)+"'  style='width: 47%;'  >"+
                             
                             "<select   onchange='getTarifAnalyse(this.value,"+(index+1)+")'  style='width: 100%; margin-top: 3px; margin-bottom: 0px; font-size: 13px;' name='analyse_name_"+(index+1)+"' id='analyse_name_"+(index+1)+"' class='analyse_name_"+(index+1)+"'>"+
                             "</select>"+   
                    
                             "</th >"+
                             
                             "<th id='tarifActe_"+(index+1)+"'  style='width: 15%;'  >"+
                             "<input id='tarifActe"+(index+1)+"' readonly='true' name='tarifActe_"+(index+1)+"' type='text' style='background: #f8f8f8; text-align: right; padding-right: 10px;  width: 100%; margin-top: 3px; height: 30px; margin-bottom: 0px; font-size: 15px; padding-left: 10px; font-family: Iskoola Pota; font-size: 20px; font-weight: bold;' >" +
                             "<input id='tarifAnalyse"+(index+1)+"' type='hidden'>"+
                             "</th >"+
                             
                             "<th id='iconeActe_supp_vider' style='width: 9%;'  >"+
                             "<a id='supprimer_acte_selectionne_"+ (index+1) +"'  style='width:50%;' >"+
                             "<img class='supprimerActe' style='margin-left: 5px; margin-top: 10px; cursor: pointer;' src='../images/images/sup.png' title='supprimer' />"+
                             "</a>"+
                             
                             "<a id='vider_analyse_selectionne_"+ (index+1) +"'  style='width:30%;' >"+
                             "<img class='viderActe' style='margin-left: 15px; margin-top: 10px; cursor: pointer;' src='../images_icons/gomme.png' title='vider' />"+
                             "</a>"+
                             "<span id='analyse_effectuee_"+ (index+1) +"'  style='display: none;'>"+
                             "<img  style='margin-left: 10px; margin-top: 10px; cursor: pointer;' src='../images_icons/tick_16.png' title='analyse r&eacute;alis&eacute;e' />"+
                             "</span>"+
                             "</th >"+
                             
                             "</tr>" +
                             "</table>" +
                             "</LesActes>" +
                             "</div>"+
                             
                             
                             "<script>"+
                                "$('#supprimer_acte_selectionne_"+ (index+1) +"').click(function(){ " +
                                		"supprimer_acte_selectionne("+ (index+1) +"); });" +
                                				
                                "$('#vider_analyse_selectionne_"+ (index+1) +"').click(function(){ " +
                                		"vider_analyse_selectionne("+ (index+1) +"); });" +
                             "</script>";
                    
                    //AJOUTER ELEMENT SUIVANT
                    $("#Acte_"+index).after($liste);
                    
                    //CACHER L'ICONE AJOUT QUAND ON A CINQ LISTES
                    if((index+1) == 20){
                    	$("#ajouter_acte").toggle(false);
                    }
                    
                    //AFFICHER L'ICONE SUPPRIMER QUAND ON A DEUX LISTES ET PLUS
                    if((index+1) == ($isupp+1)){
                    	$("#supprimer_acte").toggle(true);
                    }
}


//NOMBRE DE LISTE AFFICHEES
function nbListeActe () {
	return $("LesActes").length;
}

//SUPPRIMER LE DERNIER ELEMENT
$(function () {
	//Au debut on cache la suppression
	$("#supprimer_acte").click(function(){
		//ON PEUT SUPPRIMER QUAND C'EST PLUS DE DEUX LISTE
		if(nbListeActe () >  $isupp){ $("#Acte_"+nbListeActe ()).remove(); }
		//ON CACHE L'ICONE SUPPRIMER QUAND ON A UNE LIGNE
		if(nbListeActe () == $isupp){
			$("#supprimer_acte").toggle(false);
			$(".supprimerActe" ).replaceWith(
			  "<img class='supprimerActe' style='margin-left: 5px; margin-top: 10px;' src='../images/images/sup2.png' />"
			);
		}
		//Afficher L'ICONE AJOUT QUAND ON A 20 LIGNES
		if((nbListeActe()+1) == 20){
			$("#ajouter_acte").toggle(true);
		}   
		
		montantTotal();
		Event.stopPropagation();
	});
});


var entre = 1;
//FONCTION INITIALISATION (Par d�faut)
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
	
	if(entre == 1){
		$('#ajouter_acte').click(function(){
			creerLalisteActe(Liste);
			if(nbListeActe() == 2){
			$(".supprimerActe" ).replaceWith(
					"<img class='supprimerActe' style='margin-left: 5px; margin-top: 10px; cursor: pointer;' src='../images/images/sup.png' title='Supprimer' />"
			);
			}
		});
		entre = 0;
	}

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
		
		var element = $('#SelectTypeAnalyse_'+i+' select').val(); 
		$('#SelectTypeAnalyse_'+(i-1)+' select').val(element);
		
		var element2 = $("#SelectAnalyse_"+i+" select").val();
		var liste2 = $("#SelectAnalyse_"+i+" select").html();
		$("#SelectAnalyse_"+(i-1)+" select").html(liste2);
		$("#SelectAnalyse_"+(i-1)+" select").val(element2);;
		
		var tarif = $('#tarifActe_'+i+' input').val();
		$("#tarifActe_"+(i-1)+" input").val(tarif);
		
		var tarifAnalyse =  $("#tarifAnalyse"+i).val();
		 $("#tarifAnalyse"+(i-1)).val(tarifAnalyse);
		
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
	if((nbListeActe()+1) == 20){
		$("#ajouter_acte").toggle(true);
	}
	
	montantTotal();
	stopPropagation();
}

//VIDER LES CHAMPS DE L'ELEMENT SELECTIONNER
function vider_analyse_selectionne(id) {
	$('#SelectTypeAnalyse_'+(id)+' select').val('');
	$("#SelectAnalyse_"+id+" select").val('');
    $("#tarifActe_"+id+" input").val("");
    $("#tarifAnalyse"+id).val("");

    montantTotal();
    stopPropagation();
}

//CHARGEMENT DES ELEMENTS SELECTIONNES POUR LA MODIFICATION
//CHARGEMENT DES ELEMENTS SELECTIONNES POUR LA MODIFICATION
//CHARGEMENT DES ELEMENTS SELECTIONNES POUR LA MODIFICATION
function prixMill(num) {
	return ("" + num).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, function($1) { return $1 + " " });
}

function chargementModificationAnalyses (listeAnalysesDemandees, tabListeAnalysesParType) {
	
	for(var index = 0 ; index < listeAnalysesDemandees.length ; index++){
		var idtype = listeAnalysesDemandees[index]['idtype'];
		$("#SelectTypeAnalyse_"+(index+1)+" option[value='"+idtype+"']").attr('selected','selected'); 
		//Chargement des listes des analyses
		$("#analyse_name_"+(index+1)).html(tabListeAnalysesParType[idtype]);
		
		var idanalyse = listeAnalysesDemandees[index]['idanalyse'];
		//Sélection des analyses sur les listes 
		$("#SelectAnalyse_"+(index+1)+" option[value='"+idanalyse+"']").attr('selected','selected');
		
		//Affichage des tarifs pour chaque analyse sélectionnée
		var tarif = listeAnalysesDemandees[index]['tarif'];
		$("#tarifActe"+(index+1)).val(prixMill(tarif));
		
		//Calcul de la somme à afficher
		$("#tarifAnalyse"+(index+1)).val(tarif);
		montantTotal();
		
		//Verifier si le résultat est déjà appliqué pour l'analyse et afficher l'icône
		var result = listeAnalysesDemandees[index]['facturer'];
		if(result == 1){ $isupp++;
			$('#type_analyse_name_'+(index+1)+', #analyse_name_'+(index+1)).attr('disabled',true).css({'background':'#f8f8f8'});
			$("#SelectTypeAnalyse_"+(index+1)+" select, #SelectAnalyse_"+(index+1)+" select").removeAttr('id');
			$("#supprimer_acte_selectionne_"+(index+1)).remove();
			$("#vider_analyse_selectionne_"+(index+1)).remove();
			$("#analyse_effectuee_"+(index+1)).toggle(true);
		}
		
	}
	
	setTimeout(function(){ $('#bouton_Acte_valider_demande button').trigger('click'); },500);
}

var base_url = window.location.toString();
var tabUrl = base_url.split("public");

//VALIDATION VALIDATION VALIDATION
//********************* EXAMEN MORPHOLOGIQUE *****************************
//********************* EXAMEN MORPHOLOGIQUE *****************************
//********************* EXAMEN MORPHOLOGIQUE *****************************

function ValiderDemandeActe(){
$(function(){
	$("#bouton_Acte_modifier_demande").toggle(false);
	$("#bouton_Acte_valider_demande").toggle(true);
	
	$("#bouton_Acte_valider_demande button").click(function(){
		
		if( $('#type_analyse_name_'+1).val() != "" ){
			$("#controls_acte div").toggle(false);
			$("#iconeActe_supp_vider a img").toggle(false);
			$("#bouton_Acte_modifier_demande").toggle(true);
			$("#bouton_Acte_valider_demande").toggle(false);
			
			for(var i = 1; i <= nbListeActe(); i++ ){
				$('#type_analyse_name_'+i).attr('disabled',true); $('#type_analyse_name_'+i).css({'background':'#f8f8f8'});
				$("#analyse_name_"+i).attr('disabled',true); $("#analyse_name_"+i).css({'background':'#f8f8f8'});
			}
			
			$("#bouton_Acte_modifier_demande").click(function(){
				for(var i = 1; i <= nbListeActe(); i++ ){
					$('#type_analyse_name_'+i).attr('disabled',false); $('#type_analyse_name_'+i).css({'background':'white'});
					$("#analyse_name_"+i).attr('disabled',false); $("#analyse_name_"+i).css({'background':'white'});
				}
				$("#controls_acte div").toggle(true);
				if(nbListeActe() == 1){
					$("#supprimer_acte").toggle(false);
				}
				$("#iconeActe_supp_vider a img").toggle(true);
				$("#bouton_Acte_modifier_demande").toggle(false);
				$("#bouton_Acte_valider_demande").toggle(true);
			});
		}

		stopPropagation();
	});
	
	$("#terminer").click(function(){
		
		var diagnostic_demande = $("#diagnostic_demande_text").val();
		var temoinTypageHemo = $("#temoinTypageHemo").val();
		var TypageHemoSelect = 0;
		var PVSimple = 0;
		var PVComplet = 0;
		
		var typesAnalyses = [];
		var analyses = [];
		for(var i = 1, j = 1; i <= nbListeActe(); i++ ){
			if($('#type_analyse_name_'+i).val()) {
				typesAnalyses[j] = $('#type_analyse_name_'+i).val();
				analyses[j] = $('#analyse_name_'+i).val();
				if(analyses[j] == 68){ TypageHemoSelect = 1; }
				if(analyses[j] == 65){ PVSimple = 1; }
				if(analyses[j] == 74){ PVComplet = 1; }
				j++;
			}
		}
		
		
		if(analyses[1]){
			
			if(temoinTypageHemo == 1 && TypageHemoSelect == 1){
				$('.messageAlertVoletPopup').html('<span style="font-size: 16px; color: red;"> La demande de d&eacute;pistage &agrave; d&eacute;j&agrave; &eacute;t&eacute; faite pour ce patient. Veuillez annuler celle s&eacute;lectionn&eacute;e pour pouvoir continuer. ! </span>');
				$('#volet').fadeIn(1000);
				setTimeout(function(){ $('#volet').fadeOut(1000); }, 15000);
				
			}
			else if(PVSimple == 1 && PVComplet == 1){
				$('.messageAlertVoletPopup').html('<span style="font-size: 16px; color: red;"> Impossible de s&eacute;lectionner les deux types de PV &agrave; la fois. Veuillez en annuler un pour pouvoir continuer. ! </span>');
				$('#volet').fadeIn(1000);
				setTimeout(function(){ $('#volet').fadeOut(1000); }, 15000);
				
			}else{
				$('#volet').fadeOut(1000);
				imprimerAnalyse();
				$.ajax({
			        type: 'POST',
			        url: tabUrl[0]+'public/secretariat/envoyer-demandes-analyses',
			        data: {'analyses':analyses, 'idpatient':$("#idpatient").val(), 'diagnostic_demande':diagnostic_demande , 'verifModifier':$('#verifModifier').val()},
			        success: function() {
			        	vart = tabUrl[0]+'public/secretariat/demandes-analyses';
			    	    $(location).attr("href",vart);
			        },
			        error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
			        dataType: "html"
				});
			}
			
		}else{
			$('.messageAlertVoletPopup').html('<span style="font-size: 22px; color: red;"> Veuillez choisir une analyse ! </span>');
			$('#volet').fadeIn(1000);
			setTimeout(function(){ $('#volet').fadeOut(1000); }, 10000);
		}
		
		return false;

	});
	
});
}


function desactivationChamps(){
	
	for(var i = 1; i <= nbListeActe(); i++ ){
		$('#type_analyse_name_'+i).attr('disabled',true).css({'background':'#f8f8f8'}); 
		$("#noteActe_"+i+" input").attr('disabled',true).css({'background':'#f8f8f8'});
	}
	$("#iconeActe_supp_vider a img").toggle(false);
	
}

function getListeAnalyses(id, pos){ 
	
	$.ajax({
		type: 'POST',
		url: tabUrl[0]+'public/secretariat/get-liste-analyses',
		data:{'id':id},
		success: function(data) {    
			var result = jQuery.parseJSON(data);  
			$("#analyse_name_"+pos).html(result);
			
			getTarifAnalyse($("#analyse_name_"+pos).val(), pos);
		},
    
		error:function(e){ console.log(e); alert("Une erreur interne est survenue! voir -- secretariat --- getListeAnalysesAction() "); },
		dataType: "html"
	});

}


function montantTotal(){ 
	var somme = 0;
	for(var i = 1; i <= nbListeActe(); i++ ){
		if($("#tarifAnalyse"+i).val()){
			somme += parseInt( $("#tarifAnalyse"+i).val() );
		}
	}
	if(somme != 0){
		$("#montantTotal span").html("<div style='float: left; margin-top: -5px; margin-right: 10px;'> Montant total: </div> <div style='margin-top: -8px; margin-right: 50px; font-size: 20px; font-weight: bold; width: 120px; float: left;'>"+prixMill(somme)+" <span style='font-size: 15px;'> FCFA </span></div> ");
	}else {
		$("#montantTotal span").html("");
	}
}

function getTarifAnalyse(id, pos){ 

	$.ajax({
		type: 'POST',
		url: tabUrl[0]+'public/secretariat/get-tarif-analyse',
		data:{'id':id},
		success: function(data) {    
			var result = jQuery.parseJSON(data);  
			$("#tarifActe"+pos).val(result[1]);
			$("#tarifAnalyse"+pos).val(result[0]);
			montantTotal();
		},
    
		error:function(e){ console.log(e); alert("Une erreur interne est survenue! voir -- secretariat ---  getTarifAnalyseAction() "); },
		dataType: "html"
	});

}


function imprimerAnalyse(){
	var idpatient = $("#idpatient").val();

	var typesAnalyses = [];
	var analyses = [];
	var tarifs =[];
	for(var i = 1, j = 1; i <= nbListeActe(); i++ ){
		if($('.type_analyse_name_'+i).val()) {
			typesAnalyses[j] = $('.type_analyse_name_'+i+' option:selected').text(); 
			analyses[j] = $('.analyse_name_'+i+' option:selected').text(); 
			tarifs[j] = $('#tarifActe'+i).val();
			j++;
		}
	}
	
	
	if(analyses[1]){
		var vart = tabUrl[0]+'public/secretariat/impression-demandes-analyses';
		var FormulaireImprimerDemandesAnalyses = document.getElementById("FormulaireImprimerDemandesAnalyses");
		FormulaireImprimerDemandesAnalyses.setAttribute("action", vart);
		FormulaireImprimerDemandesAnalyses.setAttribute("method", "POST");
		FormulaireImprimerDemandesAnalyses.setAttribute("target", "_blank");
		
		// Ajout dynamique de champs dans le formulaire
		var champ = document.createElement("input");
		champ.setAttribute("type", "hidden");
		champ.setAttribute("name", 'idpatient');
		champ.setAttribute("value", idpatient);
		FormulaireImprimerDemandesAnalyses.appendChild(champ);
		
		var champ2 = document.createElement("input");
		champ2.setAttribute("type", "hidden");
		champ2.setAttribute("name", 'typesAnalyses');
		champ2.setAttribute("value", typesAnalyses);
		FormulaireImprimerDemandesAnalyses.appendChild(champ2);
		
		var champ3 = document.createElement("input");
		champ3.setAttribute("type", "hidden");
		champ3.setAttribute("name", 'analyses');
		champ3.setAttribute("value", analyses);
		FormulaireImprimerDemandesAnalyses.appendChild(champ3);
		
		var champ4 = document.createElement("input");
		champ4.setAttribute("type", "hidden");
		champ4.setAttribute("name", 'tarifs');
		champ4.setAttribute("value", tarifs);
		FormulaireImprimerDemandesAnalyses.appendChild(champ4);
		
		$("#ImprimerDemandesAnalyses").trigger('click');
	} else {
		alert('veuillez choisir une analyse');
	}

}

function imprimerAnalysesDemandees(iddemande){
	
	if(iddemande){
		var vart = tabUrl[0]+'public/secretariat/impression-analyses-demandees';
		var FormulaireImprimerAnalysesDemandees = document.getElementById("FormulaireImprimerDemandesAnalyses");
		FormulaireImprimerAnalysesDemandees.setAttribute("action", vart);
		FormulaireImprimerAnalysesDemandees.setAttribute("method", "POST");
		FormulaireImprimerAnalysesDemandees.setAttribute("target", "_blank");
		
		//Ajout dynamique de champs dans le formulaire
		var champ = document.createElement("input");
		champ.setAttribute("type", "hidden");
		champ.setAttribute("name", 'iddemande');
		champ.setAttribute("value", iddemande);
		FormulaireImprimerAnalysesDemandees.appendChild(champ);
		$("#ImprimerDemandesAnalyses").trigger('click');
	}
	
}