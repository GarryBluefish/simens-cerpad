var base_url = window.location.toString();
var tabUrl = base_url.split("public");

$(function(){
	//Les accordeons
	$( "#accordionsssss").accordion();
    $( "#accordionssss").accordion();
	$( "#accordions_resultat" ).accordion();
	$( "#accordions_demande" ).accordion();
	$( "#accordionsss" ).accordion();
	$( "#accordionss" ).accordion();
    $( "#accordions" ).accordion();
    
    //Les boutons
    $( "button" ).button();
    
    //Les tables
    $( "#tabsAntecedents" ).tabs();
	$( "#tabs" ).tabs();
	$( "#tabsInstrumental,#tabsChirurgical" ).tabs();
});
  

var temoinTaille = 0;
var temoinPoids = 0;
var temoinTemperature = 0;
var temoinPouls = 0;
var temoinTensionMaximale = 0;
var temoinTensionMinimale = 0;
	
/****** ======================================================================= *******/
/****** ======================================================================= *******/
/****** ======================================================================= *******/
/****** CONTROLE APRES VALIDATION ********/ 
/****** CONTROLE APRES VALIDATION ********/ 

function initialisationScript(agePatient) {
	//******************* VALIDER LES DONNEES DU TABLEAU DES CONSTANTES ******************************** 
	//******************* VALIDER LES DONNEES DU TABLEAU DES CONSTANTES ******************************** 
	var id_cons = $("#id_cons");
	var date_cons = $("#date_cons");
	id_cons.attr('readonly',true);
	date_cons.attr('readonly',true);
	
	var poids = $('#poids');
 	var taille = $('#taille');
 	var temperature = $('#temperature');
 	var perimetre_cranien = $('#perimetre_cranien');
 	
 	var poidsVerif = 0;
 	var tailleVerif = 0;
 	var temperatureVerif = 0; 
 	var perimetrecranienVerif = 0; 
 	
 	if(agePatient >= 5){ $('#perimetre_cranien').attr('required', true); }
 	
 	
	/****** CONTROLE APRES VALIDATION ********/ 
	/****** CONTROLE APRES VALIDATION ********/ 
	$("#terminer, #bouton_constantes_valider").click(function(){
		
		//Affichage du pop-pup des m�dicaments lors d'une douleur
		//Affichage du pop-pup des m�dicaments lors d'une douleur
		if($('#motif_admission1').val() == 2 || 
	       $('#motif_admission2').val() == 2 ||
	       $('#motif_admission3').val() == 2 ||
	       $('#motif_admission4').val() == 2 ||
	       $('#motif_admission5').val() == 2
	      ){
			
			if($('#intensite').val() > 3 && entrePriseEnCharge == 0){ 
				popListeMedicaments();
				return false;
			}
	    	
		}

		//Affichage du pop-pup des m�dicaments lors d'une fi�vre (temp�rature 38.5)
		//Affichage du pop-pup des m�dicaments lors d'une fi�vre (temp�rature 38.5)
		if($('#temperatureFievre').val() >= 38.5 || $('#temperature').val() >= 38.5){
			
			if(entrePriseEnChargeFievre == 0){
				popListeMedicamentsFievre();
				return false;
			}
			
		}
		
		
		if(!document.getElementById('poids').validity.valid){ 
		    document.getElementById('poids').validationMessage; 
		    poidsVerif = 0;
		}else{ poidsVerif = 1; }
		
		if(!document.getElementById('taille').validity.valid){
		    document.getElementById('taille').validationMessage; 
		    tailleVerif = 0;
		}else{ tailleVerif = 1; }
		
		if(!document.getElementById('temperature').validity.valid){ 
		    document.getElementById('temperature').validationMessage; 
		    temperatureVerif = 0;
		}else{ temperatureVerif = 1; }
		
		if(agePatient >= 5){
			if(!document.getElementById('perimetre_cranien').validity.valid){ 
    		    document.getElementById('perimetre_cranien').validationMessage; 
    		    perimetrecranienVerif = 0;
    		}else{ perimetrecranienVerif = 1; }
		}
		
		
	});


	//Au debut on cache le bouton modifier et on affiche le bouton valider
	$( "#bouton_constantes_valider" ).toggle(true);
	$( "#bouton_constantes_modifier" ).toggle(false);

	//Au debut on active tous les champs
	poids.attr( 'readonly', false );
	taille.attr( 'readonly', false );
	temperature.attr( 'readonly', false);
	perimetre_cranien.attr( 'readonly', false);

	$( "#bouton_constantes_valider" ).click(function(){
		if(poidsVerif == 1 && tailleVerif == 1 && temperatureVerif == 1){
			if(agePatient >= 5){
				if(perimetrecranienVerif == 1){
        			poids.attr( 'readonly', true );    
            		taille.attr( 'readonly', true );
            		temperature.attr( 'readonly', true);
            		perimetre_cranien.attr( 'readonly', true);
            		
            		$("#bouton_constantes_modifier").toggle(true); 
            		$("#bouton_constantes_valider").toggle(false); 
            		
            		return false;
				}
			}else{
    			poids.attr( 'readonly', true );    
        		taille.attr( 'readonly', true );
        		temperature.attr( 'readonly', true);
        		perimetre_cranien.attr( 'readonly', true);
        		
        		$("#bouton_constantes_modifier").toggle(true); 
        		$("#bouton_constantes_valider").toggle(false); 
        		
        		return false;
			}

		}
	});

	$( "#bouton_constantes_modifier" ).click(function(){
		poids.attr( 'readonly', false );
		taille.attr( 'readonly', false ); 
		temperature.attr( 'readonly', false );
		perimetre_cranien.attr( 'readonly', false );
 		
		$("#bouton_constantes_modifier").toggle(false);   
		$("#bouton_constantes_valider").toggle(true);    

		return  false;
	});
	
	$( "#terminer" ).click(function(){
		
		if(entrePriseEnCharge == 0){
			popListeMedicaments();
			return false; 
		}
		
		//OUVERTURE FORCEE DES DEPLIANTS
		if(agePatient >= 5){
			if( poidsVerif == 0 || tailleVerif == 0 || temperatureVerif == 0 || perimetrecranienVerif == 0){
				$('#constantesClick').trigger('click');
        		setTimeout(function(){
        			$('#motifsAdmissionConstanteClick').trigger('click'); 
        			$('#bouton_constantes_valider').trigger('click');
        		},100);
        		
        		return false;
			}else
				if( poidsVerif == 1 && tailleVerif == 1 && temperatureVerif == 1 && perimetrecranienVerif == 1){
	    			return true;
	    		}
		}else {
			
			if( poidsVerif == 0 || tailleVerif == 0 || temperatureVerif == 0 ){
				$('#constantesClick').trigger('click');
        		setTimeout(function(){
        			$('#motifsAdmissionConstanteClick').trigger('click'); 
        			$('#bouton_constantes_valider').trigger('click');
        		},100);
        		
        		return false;
			}else 
				if( poidsVerif == 1 && tailleVerif == 1 && temperatureVerif == 1 ){
					return true;
				}

		}
		
	});
	
	
	
	//APPLICATION DE LA POSOLOGIE AUTOMATIQUEMENT POUR LE CAS "DOULEUR"
	//APPLICATION DE LA POSOLOGIE AUTOMATIQUEMENT POUR LE CAS "DOULEUR"
	var poidsPatient = 0;
	
	
	$('#poidsP1 input').change(function(){
		poidsPatient = $(this).val();
		if($(this).val()){
    		$('#poids').val($(this).val()).attr('readonly', true);
    		
    		$('#poidsP1Fievre input').val($(this).val());
    		var palier1 = 15 * $(this).val();
    		$('.poidsP1Fievre').html(palier1+" <span style='font-size: 13px;'> mg/j </span>");
		}else{
    		$('#poids').val($(this).val()).attr('readonly', false);
    		
    		$('#poidsP1Fievre input').val($(this).val());
    		var palier1 = 15 * $(this).val();
    		$('.poidsP1Fievre').html(palier1+" <span style='font-size: 13px;'> mg/j </span>");
		}
		
		$('#poidsP2 input, #poidsP2a input, #poidsP2b input, #poidsP3 input').val($(this).val()).attr('readonly', true); 
	    
		//Affichage des posologies pour les m�dicaments
		//Affichage des posologies pour les m�dicaments
		var palier1 = 15 * $(this).val();
		$('.poidsP1').html(palier1+" <span style='font-size: 13px;'> mg/j </span>");
		
		var palier2 = 30 * $(this).val();
		$('.poidsP2').html(palier2+" <span style='font-size: 13px;'> mg/j </span>");
		
		var palier2a = 1 * $(this).val();
		$('.poidsP2a').html(palier2a+" <span style='font-size: 13px;'> mg/j </span>");
		
		var palier2b = 2 * $(this).val();
		$('.poidsP2b').html(palier2b+" <span style='font-size: 13px;'> mg/j </span>");
		
		/*Palier 3 deux cas possibles*/
		var voieAdminM5 = $('#voieAdminM5').val();
		if(voieAdminM5 == 1){
			$('#MorphineDosageInfos').html(" (0,1mg/kg)");
    		var palier3 = 0.1 * $(this).val();
    		$('.poidsP3').html(palier3.toFixed(1)+" <span style='font-size: 13px;'> mg/j </span>");
		}else if(voieAdminM5 == 2){
			$('#MorphineDosageInfos').html(" (15ug/kg)");
    		var palier3 = 15 * $(this).val();
    		$('.poidsP3').html(palier3+" <span style='font-size: 13px;'> ug/j </span>");
		}
	    
	}).keyup(function(){
		poidsPatient = $(this).val();
		if($(this).val()){
    		$('#poids').val($(this).val()).attr('readonly', true);
    		
    		$('#poidsP1Fievre input').val($(this).val());
    		var palier1 = 15 * $(this).val();
    		$('.poidsP1Fievre').html(palier1+" <span style='font-size: 13px;'> mg/j </span>");
		}else{
    		$('#poids').val($(this).val()).attr('readonly', false);
    		
    		$('#poidsP1Fievre input').val($(this).val());
    		var palier1 = 15 * $(this).val();
    		$('.poidsP1Fievre').html(palier1+" <span style='font-size: 13px;'> mg/j </span>");
		}
		
		$('#poidsP2 input, #poidsP2a input, #poidsP2b input, #poidsP3 input').val($(this).val()).attr('readonly', true); 
	
		//Affichage des posologies pour les m�dicaments
		//Affichage des posologies pour les m�dicaments
		var palier1 = 15 * $(this).val();
		$('.poidsP1').html(palier1+" <span style='font-size: 13px;'> mg/j </span>");
		
		var palier2 = 30 * $(this).val();
		$('.poidsP2').html(palier2+" <span style='font-size: 13px;'> mg/j </span>");
		
		var palier2a = 1 * $(this).val();
		$('.poidsP2a').html(palier2a+" <span style='font-size: 13px;'> mg/j </span>");
		
		var palier2b = 2 * $(this).val();
		$('.poidsP2b').html(palier2b+" <span style='font-size: 13px;'> mg/j </span>");
		
		/*Palier 3 deux cas possibles*/
		var voieAdminM5 = $('#voieAdminM5').val();
		if(voieAdminM5 == 1){
			$('#MorphineDosageInfos').html(" (0,1mg/kg)");
    		var palier3 = 0.1 * $(this).val();
    		$('.poidsP3').html(palier3.toFixed(1)+" <span style='font-size: 13px;'> mg/j </span>");
		}else if(voieAdminM5 == 2){
			$('#MorphineDosageInfos').html(" (15ug/kg)");
    		var palier3 = 15 * $(this).val();
    		$('.poidsP3').html(palier3+" <span style='font-size: 13px;'> ug/j </span>");
		}
	});
	
	
	$('#voieAdminM5').change(function(){
		var voieAdminM5 = $(this).val();
		if(voieAdminM5 == 1){
			$('#MorphineDosageInfos').html(" (0,1mg/kg)");
			if(poidsPatient != 0){
        		var palier3 = 0.1 * poidsPatient;
        		$('.poidsP3').html(palier3.toFixed(1)+" <span style='font-size: 13px;'> mg/j </span>");
			}
		}else if(voieAdminM5 == 2){
			$('#MorphineDosageInfos').html(" (15ug/kg)");
			if(poidsPatient != 0){
        		var palier3 = 15 * poidsPatient;
        		$('.poidsP3').html(palier3+" <span style='font-size: 13px;'> ug/j </span>");
			}
		}else if(voieAdminM5 == 0){
			$('#MorphineDosageInfos').html("");
			$('.poidsP3').html("");
		}
		
	});
	
	
	$('#voieAdminM2').change(function(){
		$('#voieAdminM3').val(0);
	});
	
	$('#voieAdminM3').change(function(){
		$('#voieAdminM2').val(0);
	});
	
	
	
	//APPLICATION DE LA POSOLOGIE AUTOMATIQUEMENT POUR LE CAS "FIEVRE"
	//APPLICATION DE LA POSOLOGIE AUTOMATIQUEMENT POUR LE CAS "FIEVRE"
	$('#poidsP1Fievre input').change(function(){
		var palier1 = 15 * $(this).val();
		$('.poidsP1Fievre').html(palier1+" <span style='font-size: 13px;'> mg/j </span>");
		
		if($(this).val()){
    		$('#poids').val($(this).val()).attr('readonly', true);
    		
    		$('#poidsP1 input').val($(this).val());
    		$('#poidsP2 input, #poidsP2a input, #poidsP2b input, #poidsP3 input').val($(this).val()).attr('readonly', true); 
    		var palier1 = 15 * $(this).val();
    		$('.poidsP1').html(palier1+" <span style='font-size: 13px;'> mg/j </span>");
    		var palier2 = 30 * $(this).val();
    		$('.poidsP2').html(palier2+" <span style='font-size: 13px;'> mg/j </span>");


		}else{ 
			$('#alertePriseEnChargeFievre input').trigger("click");
    		$('#poids').val($(this).val()).attr('readonly', false);
    		
    		$('#poidsP1 input').val($(this).val());
    		$('#poidsP2 input, #poidsP2a input, #poidsP2b input, #poidsP3 input').val($(this).val()).attr('readonly', true); 
    		var palier1 = 15 * $(this).val();
    		$('.poidsP1').html(palier1+" <span style='font-size: 13px;'> mg/j </span>");
    		var palier2 = 30 * $(this).val();
    		$('.poidsP2').html(palier2+" <span style='font-size: 13px;'> mg/j </span>");

		}
	    	    
		$('#poidsP1 input').trigger('keyup');
		
	}).keyup(function(){
		var palier1 = 15 * $(this).val();
		$('.poidsP1Fievre').html(palier1+" <span style='font-size: 13px;'> mg/j </span>");
		
		if($(this).val()){
    		$('#poids').val($(this).val()).attr('readonly', true);
    		
    		$('#poidsP1 input').val($(this).val());
    		$('#poidsP2 input, #poidsP2a input, #poidsP2b input, #poidsP3 input').val($(this).val()).attr('readonly', true); 
    		var palier1 = 15 * $(this).val();
    		$('.poidsP1').html(palier1+" <span style='font-size: 13px;'> mg/j </span>");
    		var palier2 = 30 * $(this).val();
    		$('.poidsP2').html(palier2+" <span style='font-size: 13px;'> mg/j </span>");

		}else{
			$('#alertePriseEnChargeFievre input').trigger("click");
    		$('#poids').val($(this).val()).attr('readonly', false);
    		
    		$('#poidsP1 input').val($(this).val());
    		$('#poidsP2 input, #poidsP2a input, #poidsP2b input, #poidsP3 input').val($(this).val()).attr('readonly', true); 
    		var palier1 = 15 * $(this).val();
    		$('.poidsP1').html(palier1+" <span style='font-size: 13px;'> mg/j </span>");
    		var palier2 = 30 * $(this).val();
    		$('.poidsP2').html(palier2+" <span style='font-size: 13px;'> mg/j </span>");


		}
		
		$('#poidsP1 input').trigger('keyup');
	});
	
	//Lors de la saisie de la temp�rature au pop-pup
	//Lors de la saisie de la temp�rature au pop-pup
	$('#infoTemperatureFievre input').change(function(){
		var valeur = $(this).val();
		$("#temperature").val(valeur);
		
		if( valeur >= 38.5 ){
			$('#infoPriseEnChargeFievre').toggle(true);
		}else{
			$('#infoPriseEnChargeFievre').toggle(false);
		}
		
	});
	
	//Lors de la saisie de la temp�rature sur les constantes
	//Lors de la saisie de la temp�rature sur les constantes
	$('#temperature').change(function(){
		var valeur = $(this).val();
		
		if( valeur >= 38.5 ){
			
			$('#infoTemperatureFievre input').val(valeur);
			popListeMedicamentsFievre();
			$('#infoPriseEnChargeFievre').toggle(true);
			
			var existeFievre = 0;
			for(var ind = 1 ; ind <= nbChampMotif ; ind++){
				var val = $("#motif_admission"+ind).val();
				if(val == 1){ existeFievre = 1; break;}
			}
			if(existeFievre == 0){
				if(nbChampMotif == 1 && $('#motif_admission'+(nbChampMotif)).val()=="" || nbChampMotif == 5){
					$('#motif_admission'+(nbChampMotif)).val(1);
				}else{
					$('#ajouter_motif_img').trigger('click'); 
					$('#motif_admission'+(nbChampMotif)).val(1);    
				}
			}
			
		}else{
			$('#infoPriseEnChargeFievre').toggle(false);
			$('#infoTemperatureFievre input').val(valeur);
			
			//V�rifier s'il y a un motif_admission 'Fievre' et l'enlever
			for(var ind = 1 ; ind <= 5 ; ind++){
				var val = $("#motif_admission"+ind).val();
				if(val == 1){
					if(ind==1){
    					$("#motif_admission"+ind).val(0);
					}else{
    					$(".supprimerMotif"+ind).trigger('click');
					}
				}
			}
		}
		
	}).click(function(){
		 
		var valeur = $(this).val();
		
		if( valeur >= 38.5 ){
			
			$('#infoTemperatureFievre input').val(valeur);
			popListeMedicamentsFievre();
			$('#infoPriseEnChargeFievre').toggle(true);
			
			var existeFievre = 0;
			for(var ind = 1 ; ind <= nbChampMotif ; ind++){
				var val = $("#motif_admission"+ind).val();
				if(val == 1){ existeFievre = 1; break;} //1 == 'Fièvre'
			}
			
			if(existeFievre == 0){
				if(nbChampMotif == 1 && $('#motif_admission'+(nbChampMotif)).val()==0 || nbChampMotif == 5){
					$('#motif_admission'+(nbChampMotif)).val(1);
				}else{
					$('#ajouter_motif_img').trigger('click'); 
					$('#motif_admission'+(nbChampMotif)).val(1);    
				}
			}
			
		}else{
			$('#infoPriseEnChargeFievre').toggle(false);
			$('#infoTemperatureFievre input').val(valeur);
			
			//V�rifier s'il y a un motif_admission 'Fievre' et l'enlever
			for(var ind = 1 ; ind <= 5 ; ind++){
				var val = $("#motif_admission"+ind).val();
				if(val == 1){
					if(ind==1){
    					$("#motif_admission"+ind).val(0);
					}else{
    					$(".supprimerMotif"+ind).trigger('click');
					}
				}
			}
		}
	
	});
	
	
	$('#temperatureFievre').change(function(){ 
		
		var valeur = $(this).val();
		if( valeur < 38.5 ){
			//V�rifier s'il y a un motif_admission 'Fievre' et l'enlever
			for(var ind = 1 ; ind <= 5 ; ind++){
				var val = $("#motif_admission"+ind).val();
				if(val == 1){
					if(ind==1){
    					$("#motif_admission"+ind).val(0);
					}else{
    					$(".supprimerMotif"+ind).trigger('click');
					}
				}
			}
		}else{
			$('#temperature').trigger('click');
		}
		
	}).keyup(function(){
		
		var valeur = $(this).val();
		if( valeur < 38.5 ){
			//V�rifier s'il y a un motif_admission 'Fievre' et l'enlever
			for(var ind = 1 ; ind <= 5 ; ind++){
				var val = $("#motif_admission"+ind).val();
				if(val == 1){
					if(ind==1){
    					$("#motif_admission"+ind).val(0);
					}else{
    					$(".supprimerMotif"+ind).trigger('click');
					}
				}
			}
		}else{
			$('#temperature').trigger('click');
		}
		
	});
	
	//Gestion des voies d'administration
	//Gestion des voies d'administration
	$("#voieAdminM1").change(function(){ $("#voie_med_1").val($(this).val()); });
	$("#voieAdminM2").change(function(){ $("#voie_med_2").val($(this).val()); });
	$("#voieAdminM3").change(function(){ $("#voie_med_3").val($(this).val()); });
	$("#voieAdminM4").change(function(){ $("#voie_med_4").val($(this).val()); });
	$("#voieAdminM5").change(function(){ $("#voie_med_5").val($(this).val()); });
	$("#voieAdminM6").change(function(){ $("#voie_med_6").val($(this).val()); });
	
	
	
	//Gestion de l'interface de la consultation du jour
	//Gestion de l'interface de la consultation du jour
	$(".titreInterrogatoireStyle .designHistoireMaladie, .titreInterrogatoireStyle label").toggle(false);

	for(var i=1 ; i<=nbChampMotif ; i++){
		//Augmenter la hauteur de l'espace en fonction des motifs 
		if(i == 1){ $(".ligneInterLigne1").toggle(true); $(".titreInterrogatoireStyle").css('height','45px'); }
		if(i == 3){ $(".ligneInterLigne2").toggle(true); $(".titreInterrogatoireStyle").css('height','90px'); }
		if(i == 5){ $(".ligneInterLigne3").toggle(true); $(".titreInterrogatoireStyle").css('height','135px'); }
		
		//Afficher le motif
		var motif = $('#motif_admission'+i).val();
		$("#interrogatoireDescSympMotif"+i).toggle(true);
		var leMotif = listeMotifsAdmission[motif];
		$("#interrogatoireDescSympMotif"+i+" span").html(leMotif);
		
		//Si c'est un des motifs suivant augmenter la largeur des champs de saisi
		if(leMotif=='Fièvre' || leMotif=='Douleur' || leMotif=='Priapisme'){
			$("#motif_interrogatoire_"+i).css('width','80%');
		}
	}
	
}

$("#labelSuiviDesTraitementsPre, #labelMisesAJourDesVaccinsPre, .hospitalisationClassHM, .hospitalisationNombreClassHM").toggle(false);
function getSuiviDesTraitements(id){

	if(id == 2 || id == 3){
		$("#labelSuiviDesTraitementsPre").fadeIn();
	}else if(id == 1 || id == ''){
		$("#labelSuiviDesTraitementsPre").fadeOut(false);
	}

}

function getMisesAJourDesVaccins(id){

	if(id == 1){
		$("#labelMisesAJourDesVaccinsPre").fadeIn();
	}else if(id == 2 || id == ''){
		$("#labelMisesAJourDesVaccinsPre").fadeOut(false);
	}

}

function getHospitalisationHM(id){
	
	if(id == 1){
		$(".titreHistoireMaladieStyle").css({'height':'180px'});
		$(".hospitalisationClassHM").fadeIn();
	}else if(id == 0 || id == ''){
		$(".hospitalisationClassHM").fadeOut(function(){
			$(".titreHistoireMaladieStyle").css({'height':'135px'});
		});
	}
	
}

function getPriseEnChargeHospitalisationHM(id){
	
	if(id == 1){
		$(".hospitalisationNombreClassHM").fadeIn();
	}else {
		$(".hospitalisationNombreClassHM").fadeOut(false);
	}
	
}