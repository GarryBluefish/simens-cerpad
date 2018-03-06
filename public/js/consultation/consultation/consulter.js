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
		
		//Affichage du pop-pup des médicaments lors d'une douleur
		//Affichage du pop-pup des médicaments lors d'une douleur
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

		//Affichage du pop-pup des médicaments lors d'une fièvre (température 38.5)
		//Affichage du pop-pup des médicaments lors d'une fièvre (température 38.5)
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
	    
		//Affichage des posologies pour les médicaments
		//Affichage des posologies pour les médicaments
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
	
		//Affichage des posologies pour les médicaments
		//Affichage des posologies pour les médicaments
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
	
	//Lors de la saisie de la température au pop-pup
	//Lors de la saisie de la température au pop-pup
	$('#infoTemperatureFievre input').change(function(){
		var valeur = $(this).val();
		$("#temperature").val(valeur);
		
		if( valeur >= 38.5 ){
			$('#infoPriseEnChargeFievre').toggle(true);
		}else{
			$('#infoPriseEnChargeFievre').toggle(false);
		}
		
	});
	
	//Lors de la saisie de la température sur les constantes
	//Lors de la saisie de la température sur les constantes
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
			
			//Vérifier s'il y a un motif_admission 'Fievre' et l'enlever
			/*
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
			*/
			
			
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
				if(val == 1){ existeFievre = 1; break;} //1 == 'FiÃ¨vre'
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
			
			//Vérifier s'il y a un motif_admission 'Fievre' et l'enlever
			/*
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
			*/
		}
	
	});
	
	
	$('#temperatureFievre').change(function(){ 
		
		var valeur = $(this).val();
		if( valeur < 38.5 ){
			//Vérifier s'il y a un motif_admission 'Fievre' et l'enlever
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
			//Vérifier s'il y a un motif_admission 'Fievre' et l'enlever
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
		$("#interrogatoireDescSympMotif"+i+" span").html("<span style='font-size: 12px;'>&#11166; </span>"+leMotif);
		
		//Si c'est un des motifs suivant augmenter la largeur des champs de saisi
		if(leMotif=='FiÃ¨vre' || leMotif=='Douleur' || leMotif=='Priapisme'){
			$("#motif_interrogatoire_"+i).css('width','80%');
		}
	}
	
	
	/*** Données de l'examen ***/
	$('#donneesExamenOrlCheckbox input[name=orlObstructionNasaleDonneesExamen]').click(function(){ 
		var boutons = $('#donneesExamenOrlCheckbox input[name=orlObstructionNasaleDonneesExamen]');
		if( boutons[1].checked){ $("#obstructionNasaleDE").html('<span style="color: green;">&#10003;</span> Obstruction nasale').css({'color':'black', 'font-weight':'bold'}); }
		if(!boutons[1].checked){ $("#obstructionNasaleDE").html('Obstruction nasale').css({'color':'black', 'font-weight':'normal'}); }
	});
	
	$('#donneesExamenOrlCheckbox input[name=orlRhiniteDonneesExamen]').click(function(){ 
		var boutons = $('#donneesExamenOrlCheckbox input[name=orlRhiniteDonneesExamen]');
		if( boutons[1].checked){ $("#rhiniteDE").html('<span style="color: green;">&#10003;</span> Rhinite').css({'color':'black', 'font-weight':'bold'}); }
		if(!boutons[1].checked){ $("#rhiniteDE").html('Rhinite').css({'color':'black', 'font-weight':'normal'}); }
	});
	
	$('#donneesExamenOrlCheckbox input[name=orlHypertrophieAmygdalesDonneesExamen]').click(function(){ 
		var boutons = $('#donneesExamenOrlCheckbox input[name=orlHypertrophieAmygdalesDonneesExamen]');
		if( boutons[1].checked){ $("#hypertrophieAmygdalesDE").html('<span style="color: green;">&#10003;</span> Hypertrophie des amygdales').css({'color':'black', 'font-weight':'bold'}); }
		if(!boutons[1].checked){ $("#hypertrophieAmygdalesDE").html('Hypertrophie des amygdales').css({'color':'black', 'font-weight':'normal'}); }
	});
	
	$('#donneesExamenOrlCheckbox input[name=orlAngineDonneesExamen]').click(function(){ 
		var boutons = $('#donneesExamenOrlCheckbox input[name=orlAngineDonneesExamen]');
		if( boutons[1].checked){ $("#angineDE").html('<span style="color: green;">&#10003;</span> Angine').css({'color':'black', 'font-weight':'bold'}); }
		if(!boutons[1].checked){ $("#angineDE").html('Angine').css({'color':'black', 'font-weight':'normal'}); }
	});
	
	$('#donneesExamenOrlCheckbox input[name=orlOtiteDonneesExamen]').click(function(){ 
		var boutons = $('#donneesExamenOrlCheckbox input[name=orlOtiteDonneesExamen]');
		if( boutons[1].checked){ $("#otiteDE").html('<span style="color: green;">&#10003;</span> Otite').css({'color':'black', 'font-weight':'bold'}); }
		if(!boutons[1].checked){ $("#otiteDE").html('Otite').css({'color':'black', 'font-weight':'normal'}); }
	});
	
}

$("#labelSuiviDesTraitementsPre, #labelMisesAJourDesVaccinsPre, .hospitalisationClassHM, .hospitalisationNombreClassHM, #labelDonneesExamenTaille, #labelPrecisionExamenDesPoumonsDE, #labelPrecisionExamenDuCoeurDE").toggle(false);

$(".criseInfo div").toggle(false);
function getInfoCrise(id){

	if(id == 1){
		$(".criseInfo div").fadeIn();
	}else if(id == -1 || id == ''){
		$(".criseInfo div").fadeOut(false);
	}
	
}

$(".episodeFievreClassHM div").toggle(false);
function getInfoEpisodeFievre(id){

	if(id == 1){
		$(".episodeFievreClassHM div").fadeIn();
	}else if(id == -1 || id == ''){
		$(".episodeFievreClassHM div").fadeOut(false);
	}
	
}


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
	}else if(id == -1 || id == ''){
		$("#labelMisesAJourDesVaccinsPre").fadeOut(false);
	}

}

function getHospitalisationHM(id){
	
	if(id == 1){
		$(".titreHistoireMaladieStyle").css({'height':'180px'});
		$(".hospitalisationClassHM").fadeIn();
	}else if(id == -1 || id == ''){
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
		//$("#nombreHospitalisationHM").val('');
	}
	
}

function getSplenomegalieDonneesExamen(id){
	
	if(id == 1){
		$("#labelDonneesExamenTaille").fadeIn();
	}else {
		$("#labelDonneesExamenTaille").fadeOut(false);
	}
	
}

function getExamenDesPoumonsDonneesExamen(id){

	if(id == -1){
		$("#labelPrecisionExamenDesPoumonsDE").fadeIn();
	}else{
		$("#labelPrecisionExamenDesPoumonsDE").fadeOut(false);
	}

}

function getExamenDuCoeurDonneesExamen(id){
	
	if(id == -1){
		$("#labelPrecisionExamenDuCoeurDE").fadeIn();
	}else{
		$("#labelPrecisionExamenDuCoeurDE").fadeOut(false);
	}
	
}





/*
 * GESTION Des Examens complémentaires --- GESTION Des Examens complémentaires
 * GESTION Des Examens complémentaires --- GESTION Des Examens complémentaires
 * GESTION Des Examens complémentaires --- GESTION Des Examens complémentaires
 */

$('.designLabelExamenComplementaire').toggle(false);

function getExamensEffecutesDansExamComp(nbExamensEffectues,tabIndexAnalyses,tabAnalyses){
	//Ajout de l'interface d'affichage des analyses effectuées
	//Ajout de l'interface d'affichage des analyses effectuées
	var nbLigneAAfficher = parseInt(nbExamensEffectues/3);
	if((nbExamensEffectues%3) != 0){ nbLigneAAfficher += 1; }
	
	for(var i = 1 ; i <= nbLigneAAfficher ; i++){
		var hauteur = 45*i;  
		$('.contenuExamensEffectuesStyle').css('height',hauteur+'px');
	}

	//Placer les analyses déjà effectuées
	//Placer les analyses déjà effectuées
	if(tabAnalyses.length == 0){
		$('.contenuExamensEffectuesStyle table').html("<tr style='width: 100%'><td style='width: 100%; color: red; padding-top: 7px; text-align: center; font-family: times new roman;'>Aucune analyse effectu&eacute;e</td></tr>");
	}else{
		for(var i = 1 ; i < tabAnalyses.length ; i++){
			$('#idAnalyseExamComp'+i).html("<label style='width: 100%; height:30px; text-align:left;'>" +
					                       " <span style='font-size: 12px;'>&#11166; </span>" +
					                       " <span style='font-size: 13px;'>"+tabAnalyses[i]+"</span>" +
					                       " <span style='color: green;'>&#10003;</span>" +
					                       " <span style='color: green; font-family: Tempus Sans ITC; float: right;'> "+tabIndexAnalyses[i]+" </span>" +
					                       "</label>");
		}
	}

	//Affichage des labels
	//Affichage des labels
	setTimeout(function(){
		for(var i = 1 ; i <= nbLigneAAfficher ; i++){
			$('#examensEffectuesECLigne'+i).toggle(true);
		}
	},2000);
	
}

function getExamensNonFaitsDansExamComp(nbExamensNonFaits,tabIndexAnalysesNonFaits,tabAnalysesNonFaits){
	//Ajout de l'interface d'affichage des analyses effectuées
	//Ajout de l'interface d'affichage des analyses effectuées
	var nbLigneAAfficher = parseInt(nbExamensNonFaits/3);
	if((nbExamensNonFaits%3) != 0){ nbLigneAAfficher += 1; }
	
	for(var i = 1 ; i <= nbLigneAAfficher ; i++){
		var hauteur = 45*i;  
		$('.contenuExamensAFaireStyle').css('height',hauteur+'px');
	}

	//Placer les analyses déjà effectuées
	//Placer les analyses déjà effectuées
	if(tabAnalysesNonFaits.length == 0){
		$('#demanderLesAnalysesAFaireIcone').toggle(false);
		$('.contenuExamensAFaireStyle table').html("<tr style='width: 100%'><td style='width: 100%; color: red; padding-top: 7px; text-align: center; font-family: times new roman;'>Aucune analyse &agrave; faire </td></tr>");
	}else{
		for(var i = 1 ; i < tabAnalysesNonFaits.length ; i++){
			$('#idAnalyseAFExamComp'+i).html("<label style='width: 100%; height:30px; text-align:left;'>" +
	                " <span style='font-size: 12px;'>&#11166; </span> <span style='font-size: 13px;'>" +
	                ""+tabAnalysesNonFaits[i]+"</span> <span style='color: red;'>&#x2717;</span>" +
	                " <span style='color: green; float: right;' id='iconeIndicateurSelection_"+tabIndexAnalysesNonFaits[i]+"' title='Demande effectu&eacute;e'></span>" +
	                "</label>");
		}
	}

	//Affichage des labels
	//Affichage des labels
	setTimeout(function(){
		for(var i = 1 ; i <= nbLigneAAfficher ; i++){
			$('#examensAFaireECLigne'+i).toggle(true);
		}
	},2000);
	
}


function gestionAlerteExistanceAnalyseNonFait(nbExamensNonFaits){
	
	$('#volet').dblclick(function(){
	   $(this).animate({'top': -120}, 'slow');
	   setTimeout(function(){ $('#volet').hide(); },1500);
	});

	$('#clickOuvrirPopup').click(function(){
		$('#volet').show('slow');
		//Lors d'un scroll
		$(window).scroll(function(){
			var top = ($(window).scrollTop()); 
			if(top > 52){
				$('#volet').css({'position' : 'fixed', 'top': 0});
			}else{
				$('#volet').css({'position' : 'fixed', 'top': 0}); //52-top
			}
		});
		
		//Au click
		var top = ($(window).scrollTop()); 
		if(top > 52){
			$('#volet').css({'position' : 'fixed','top': 0})
		}else{
			$('#volet').css({'position' : 'fixed','top': 0}); //52-top
		}
	});
	
	//Afficher lorsqu'il y a au moins une analyse non faite 
	if(nbExamensNonFaits > 0){
		
		setTimeout(function(){

			$('#clickOuvrirPopup').trigger('click');
			$('.messageAlertVoletPopup').html('<div style="color: black; font-size: 17px; font-weight: bold; width: 100%; font-family: Tempus Sans ITC;">'
					                          +'<span style="color: red; font-size: 18px;">'+nbExamensNonFaits+'</span> analyses obligatoires &agrave; faire <img onclick="ouvrirLeDepliantExamenComplementaireAFaire();" style="float: right; cursor: pointer;" src="../images_icons/voirAlert_24.png" title="Voir" /> </div>');
			alerteSonore(); 
		 
			//Fermer le pop-up au click sur le depliant
			$('.examenComplementaireAFaireDelpiantAlert').click(function(){ $('#volet').trigger('dblclick'); });
			
		},1000);
	}

}

function ouvrirLeDepliantExamenComplementaireAFaire(){
	$('#volet').trigger('dblclick');
	$('.examenComplementaireDelpiantAlert').trigger('click');
	$('.examenComplementaireAFaireDelpiantAlert').trigger('click');
}

function alerteSonore() { 
	var player = document.querySelector('#audioAlerteAutoAnalyse');
	setTimeout(function(){ player.play(); },500);
	//player.pause(); //pour la pause
}



var iaj = 1;
var jsel = 1; 
var jsupsel = 1;
var jsupselDebut = 1;
var nbExamensNonFaitsAFaire = 0;
function demanderLesAnalysesAFaire() {
	
	$('#examenComplementaireDemandeDelpiant').trigger('click');
	
	var nbListeAnalyseActu = nbListeActe();
	var valeurType = $("#SelectTypeAnalyse_"+nbListeAnalyseActu+" select").val();
	
	if(valeurType != ''){
		jsel = nbListeAnalyseActu+1; 
		jsupsel = jsel;
		jsupselDebut = jsel;
		nbExamensNonFaitsAFaire = parseInt(nbExamensNonFaits)+parseInt(nbListeAnalyseActu);
		
		//Placer les lignes des analyses
		for( ; iaj <= (nbExamensNonFaits) ; iaj++){
			$('#ajouter_acte').trigger('click');
		}
	}else{
		nbExamensNonFaitsAFaire = nbExamensNonFaits;
		jsupsel = jsel;
		jsupselDebut = jsel;
		
		//Placer les lignes des analyses
		for( ; iaj <= (nbExamensNonFaits-1) ; iaj++){
			$('#ajouter_acte').trigger('click');
		}
	}

	var isel = 1;
	//Selectionner les analyses sur les lignes placées
	for( ; jsel <= nbExamensNonFaitsAFaire ; jsel++){ 
		var idtype = tabTypesAnalysesNonFaits[isel];
		
		$("#SelectTypeAnalyse_"+parseInt(jsel)+" option[value='"+idtype+"']").attr('selected','selected'); 
		$("#SelectTypeAnalyse_"+parseInt(jsel)+" select").val(idtype);
		//Chargement des listes des analyses
		$("#analyse_name_"+(jsel)).html(arrayListeAnalysesParTypeDansExamenAFaire[idtype]);
		//SÃ©lection des analyses sur les listes 
		var indexanalyse = tabIndexAnalysesNonFaits[isel];
		$("#SelectAnalyse_"+(jsel)+" option[value='"+indexanalyse+"']").attr('selected','selected');

		//Affichage des tarifs pour chaque analyse sÃ©lectionnÃ©e
		var tarif = tabTarifAnalysesNonFaits[isel];
		$("#tarifActe"+(jsel)).val(prixMillTarifAnalyseAFaire(tarif));
		
		//Calcul de la somme Ã  afficher
		$("#tarifAnalyse"+(jsel)).val(tarif);
		montantTotal();
		
		//Placer icone indicateur
		$("#iconeIndicateurSelection_"+indexanalyse).html("<span style='font-size: 12px;'>&#9993;</span>");
		
		isel++;
		
		
		//Signaler de quel type d'analyse il s'agit
		$("#analyse_effectuee_"+(jsel)).toggle(true).html("<span class='signalDemandeEffectueIcon' style='margin-left: 3px; margin-top: 10px; cursor: pointer;' title='analyse obligatoire demand&eacute;e'>&#9993;</span>");
		
		//Appliquer le script bull info
	    $('a,img,span').tooltip({ animation: true, html: true, placement: 'bottom', show: { effect: 'slideDown', } });
	
	}
	
	$("#iconeVoirSupprimerAlert").html('<img onclick="annulerDemandesDesAnalysesAFaire();" style="float: right; cursor: pointer;" src="../images_icons/annuleDemandeAlert_24.png" title="Annuler la demande" />');
		
}

function prixMillTarifAnalyseAFaire(num) {
	return ("" + num).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, function($1) { return $1 + " " });
}

function annulerDemandesDesAnalysesAFaire() {
	$('#examenComplementaireDemandeDelpiant').trigger('click');
	
	var isel = 1;
	for( ; jsupsel <= nbExamensNonFaitsAFaire ; jsupsel++){
		
		//Si la liste commence par le premier élément
		//On supprime un par un jusqu'au dernier
		if(jsupselDebut == 1){
			setTimeout(function(){ 
				//Enlever les icones indicateurs des analyses demandées
				$(".signalDemandeEffectueIcon").remove();
				supprimer_acte_selectionne(1); 
			},500);
		}else{
			setTimeout(function(){
				//Enlever les icones indicateurs des analyses demandées
			    $(".signalDemandeEffectueIcon").remove();
			    supprimer_acte_selectionne(jsupselDebut); 
			},500);
		}
		
	    //Enlever les icones indicateurs
	    var indexanalyse = tabIndexAnalysesNonFaits[isel++];
		$("#iconeIndicateurSelection_"+indexanalyse).html("");
	    
	    //Placer l'icone de demande
		$("#iconeVoirSupprimerAlert").html('<img  id="demanderLesAnalysesAFaireIcone"  onclick="demanderLesAnalysesAFaire();" style="float: right; cursor: pointer; " src="../images_icons/demander_analyses.png" title="Effectuer la demande des analyses">');
	}
	jsel = 1;
	iaj = 1;
}



//RESULTAT -- DEMANDE EXAMEN RADIOLOGIQUE -- DEMANDE EXAMEN RADIOLOGIQUE 
//RESULTAT -- DEMANDE EXAMEN RADIOLOGIQUE -- DEMANDE EXAMEN RADIOLOGIQUE 
var hauteurLabelContenu = 0;
function ajoutChampResultatExamenRadio($id, $idexamenRadio, $libelleexamenRadio){
	
	var nbExamenRadio = $("#contenuResultatExamenRadio table tr").length;
	
	var champResultatExamen = ''+
		           '<tr id="contenuResultExamen'+(nbExamenRadio+1)+'" class="designLabelResultatExamenComplementaire positionResultatExamenRadio'+$id+'" style="height:40px; width:100%;" >'+
                   '<th style="width:100%; padding-right: 25px; vertical-align: top;" > '+
                   '<div style="float:left; width: 100%;">'+
                     '<label style="width: 100%; height:30px; text-align:left;" >'+
                       '<span style="font-size: 12px;">&#11166; </span> <span id="textExamenRadio_'+$idexamenRadio+'" style="font-size: 13px; "> '+$libelleexamenRadio+' </span> '+
                       '<input name="resultatExamenRadio_'+$idexamenRadio+'" id="resultatExamenRadio_'+$idexamenRadio+'" type="text" style="width: 75%; float: right;"> '+
                     '</label>'+
                   '</div>'+ 
                   '</th>'+
                   '</tr>';
	
	var existeResultatRadio = $('#contenuResultatExamenRadio table tr').hasClass('positionResultatExamenRadio'+$id);
	
	if(existeResultatRadio){
		
		var champResultatExamenReplace = ''+
                   '<th style="width:100%; padding-right: 25px; vertical-align: top;" > '+
                     '<div style="float:left; width: 100%;">'+
                       '<label style="width: 100%; height:30px; text-align:left;" >'+
                         '<span style="font-size: 12px;">&#11166; </span> <span id="textExamenRadio_'+$idexamenRadio+'" style="font-size: 13px; "> '+$libelleexamenRadio+' </span> '+
                         '<input name="resultatExamenRadio_'+$idexamenRadio+'" id="resultatExamenRadio_'+$idexamenRadio+'" type="text" style="width: 75%; float: right;"> '+
                         '</label>'+
                     '</div>'+ 
                   '</th>';
		
		$('.positionResultatExamenRadio'+$id).empty().html(champResultatExamenReplace);
	}else{ 

		hauteurLabelContenu = 45*parseInt(nbExamenRadio);
		$(".titreResultatExamenRadioStyle").css({'height':hauteurLabelContenu+'px'});
		$('#contenuResultatExamenRadio #contenuResultExamen'+nbExamenRadio).after(champResultatExamen);
	}
	
}

function supprimeChampResultatExamenRadio($id){
	$('.positionResultatExamenRadio'+$id).remove();
	hauteurLabelContenu -= 45;
	$(".titreResultatExamenRadioStyle").css({'height':hauteurLabelContenu+'px'});
	
}

function supprimeDroiteChampResultatExamenRadio($id, nbListeActe, elsup){
	
	//On supprime a partir de la ligne demandée et recréer toutes les lignes en bas
	var tabPos = new Array();
	var itp = 0;
	for(var i = $id ; i <= nbListeActe ; i++ ){
		
		//On supprime les resultats unpar un 
		$('.positionResultatExamenRadio'+i).remove();
		
		//On crée la nouvelle table avec les données à sauvegarder
		if($('#type_analyse_name_'+i).val() == 6){
			tabPos[itp++] = i;
		}
	}
	
	//Un champ résultat supprimé du fait de la suppression
	//d'une ligne est supprimée dans la liste des demandes
	if(elsup == 0){
		var indiceDep = 0;
		if($('#type_analyse_name_'+$id).val() == 6){
			indiceDep = 1;
		}
		
		setTimeout(function(){ 
			//On recrée les différents champs
			for(var i = indiceDep ; i < tabPos.length ; i++ ){
				var $pos = tabPos[i]-1;
				ajoutAutomatiqueChampsResultats($pos);
			}
		},1000);
		
	}
	//Un champ résultat supprimé sans la suppression
	//d'une ligne dans la liste des demandes
	else{
		setTimeout(function(){ 
			//On recrée les différents champs
			for(var i = 0 ; i < tabPos.length ; i++ ){
				var $pos = tabPos[i];
				ajoutAutomatiqueChampsResultats($pos);
			}
		},1000);
	}

	hauteurLabelContenu -= 45;
	$(".titreResultatExamenRadioStyle").css({'height':hauteurLabelContenu+'px'});
	
}




































//DIAGNOSTIC --- COMPLICATIONS AIGUES & COMPLICATIONS CHRONIQUES
//DIAGNOSTIC --- COMPLICATIONS AIGUES & COMPLICATIONS CHRONIQUES
/**
 * Complications aigues
 */
$("#enleverComplicationAigueBouton").toggle(false);
function ajouterComplicationAigue(){
	var i = nbChampDiagComplicationsAigues +=1;
	
	$("#diagnosticComplicationsAiguesChamp_"+i).html("<label style='width: 100%; height:30px; text-align:left;'>" +
			                                          " " +
			                                          "<select name='diagnosticComplicationsAiguesChamp_"+i+"' style='font-size: 14.5px; width: 95%; margin-left: 5px;'>" +
			                     					  "<option value=''></option>" +
			                     					  "<option value='1'>An&eacute;mie aigue</option>" +
			                     					  "<option value='2'>Pneumonie</option>" +
			                     					  "<option value='3'>M&eacute;ningite</option>" +
			                     					  "<option value='4'>Septic&eacute;mie</option>" +
			                     					  "<option value='5'>Infection ost&eacute;o-articulaire</option>" +
			                     					  "<option value='6'>Syndrome thoracique aigu</option>" +
			                     					  "<option value='7'>Priapisme</option>" +
			                     					  "<option value='8'>Vasculopathie C&eacute;r&eacute;brale</option>" +
			                     					  "</select>" +
			                     					  "<div style='color: green; font-size: 8px; font-weight: bold; float: right; margin-top: -13px; height: 10px;' title='supprimer' onclick='supprimerDiagnosticComplicationAigue("+i+")'> X </div>" +
			                                         "</label>");
	if(i==9){ $("#ajouterComplicationAigueBouton").toggle(false); }
	if(i==2){ $("#enleverComplicationAigueBouton").toggle(true); }
	//Ajustement de l'interface d'affichage des champs
	if(i==4){ $(".contenuComplicationsAiguesStyle").css({'height':'120px'});  }
	if(i==7){ $(".contenuComplicationsAiguesStyle").css({'height':'165px'});  }
	
	$("#nbDiagnosticComplicationsAigues").val(i);
	
    $('a,img,div,span').tooltip({ animation: true, html: true, placement: 'bottom', show: { effect: 'slideDown', } });

}

function enleverComplicationAigue(){
	var i = nbChampDiagComplicationsAigues--;
	$("#diagnosticComplicationsAiguesChamp_"+i).html("");
	
	if(i==2){ $("#enleverComplicationAigueBouton").toggle(false); }
	if(i==9){ $("#ajouterComplicationAigueBouton").toggle(true); }
	//Ajustement de l'interface d'affichage des champs
	if(i==4){ $(".contenuComplicationsAiguesStyle").css({'height':'75px'});  }
	if(i==7){ $(".contenuComplicationsAiguesStyle").css({'height':'120px'});  }
	
	$("#nbDiagnosticComplicationsAigues").val(i-1);
}

function supprimerDiagnosticComplicationAigue(id){
	
	for(var i=id ; i<9 ; i++){
		var valSuiv= $("#diagnosticComplicationsAiguesChamp_"+(i+1)+" select").val();
		$("#diagnosticComplicationsAiguesChamp_"+i+" select").val(valSuiv);
	}
	enleverComplicationAigue();
}


/**
 * Complications chroniques
 */

$("#enleverComplicationChroniqueBouton").toggle(false);
function ajouterComplicationChronique(){
	var i = nbChampDiagComplicationsChroniques +=1;
	
	$("#diagnosticComplicationsChroniquesChamp_"+i).html("<label style='width: 100%; height:30px; text-align:left;'>" +
			                                          " " +
			                                          "<select name='diagnosticComplicationsChroniquesChamp_"+i+"' style='font-size: 15px; width: 95%; margin-left: 5px;'>" +
			                     					  "<option value=''></option>" +
			                     					  "<option value='1'>Lithiase biliaire</option>" +
			                     					  "<option value='2'>Ost&eacute;o-n&eacute;crose hanche</option>" +
			                     					  "<option value='3'>Ost&eacute;o-n&eacute;crose &eacute;paule</option>" +
			                     					  "<option value='4'>R&eacute;tinopathie</option>" +
			                     					  "<option value='5'>Nephropathie</option>" +
			                     					  "<option value='6'>Cardiomyopathie</option>" +
			                     					  "<option value='7'>HTAP</option>" +
			                     					  "</select>" +
			                     					  "<div style='color: green; font-size: 8px; font-weight: bold; float: right; margin-top: -13px; height: 10px;' title='supprimer' onclick='supprimerDiagnosticComplicationChronique("+i+")'> X </div>" +
			                                         "</label>");
	if(i==9){ $("#ajouterComplicationChroniqueBouton").toggle(false); }
	if(i==2){ $("#enleverComplicationChroniqueBouton").toggle(true); }
	//Ajustement de l'interface d'affichage des champs
	if(i==4){ $(".contenuComplicationsChroniquesStyle").css({'height':'120px'});  }
	if(i==7){ $(".contenuComplicationsChroniquesStyle").css({'height':'165px'});  }
	
	$("#nbDiagnosticComplicationsChroniques").val(i);
}

function enleverComplicationChronique(){
	var i = nbChampDiagComplicationsChroniques--;
	$("#diagnosticComplicationsChroniquesChamp_"+i).html("");
	
	if(i==2){ $("#enleverComplicationChroniqueBouton").toggle(false); }
	if(i==9){ $("#ajouterComplicationChroniqueBouton").toggle(true); }
	//Ajustement de l'interface d'affichage des champs
	if(i==4){ $(".contenuComplicationsChroniquesStyle").css({'height':'75px'});  }
	if(i==7){ $(".contenuComplicationsChroniquesStyle").css({'height':'120px'});  }
	
	$("#nbDiagnosticComplicationsChroniques").val(i-1);
}

function supprimerDiagnosticComplicationChronique(id){

	for(var i=id ; i<9 ; i++){
		var valSuiv= $("#diagnosticComplicationsChroniquesChamp_"+(i+1)+" select").val();
		$("#diagnosticComplicationsChroniquesChamp_"+i+" select").val(valSuiv);
	}
	enleverComplicationChronique();
}



//TRAITEMENT MEDICAMENTEUX --- TRAITEMENT MEDICAMENTEUX
//TRAITEMENT MEDICAMENTEUX --- TRAITEMENT MEDICAMENTEUX
function imprimerTraitementMedicamenteux(){
	var idpatient = $("#idpatient").val();
	var id_cons = $('#idcons').val();
	
	var medicamentLibelle = [];
	var formeMedicament = [];
	var nbMedicament = [];
	var quantiteMedicament = [];
	for(var i = 1, j = 1; i <= nbListeMedicaments(); i++ ){
		if($('#medicament_0'+i).val()) {
			medicamentLibelle[j] = $('#medicament_0'+i).val();
			formeMedicament[j] = $('#noteMedicament_'+i+' input').val();
			nbMedicament[j] = $('#nb_medicament_'+i).val();
			quantiteMedicament[j] = $('#quantite_'+i).val();
			j++;
		}
	}
	
	var lienUrl = tabUrl[0]+'public/consultation/impression-ordonnance';
	var formulaireImprimerDemandesAnalyses = document.getElementById("formulaireImprimerOrdonnance");
	formulaireImprimerDemandesAnalyses.setAttribute("action", lienUrl);
	formulaireImprimerDemandesAnalyses.setAttribute("method", "POST");
	formulaireImprimerDemandesAnalyses.setAttribute("target", "_blank");
	
	// Ajout dynamique de champs dans le formulaire
	var champ = document.createElement("input");
	champ.setAttribute("type", "hidden");
	champ.setAttribute("name", 'idpatient');
	champ.setAttribute("value", idpatient);
	formulaireImprimerDemandesAnalyses.appendChild(champ);
	
	
	var champ2 = document.createElement("input");
	champ2.setAttribute("type", "hidden");
	champ2.setAttribute("name", 'idcons');
	champ2.setAttribute("value", idcons);
	formulaireImprimerDemandesAnalyses.appendChild(champ2);
	
	var champ3 = document.createElement("input");
	champ3.setAttribute("type", "hidden");
	champ3.setAttribute("name", 'medicamentLibelle');
	champ3.setAttribute("value", medicamentLibelle);
	formulaireImprimerDemandesAnalyses.appendChild(champ3);
	
	var champ4 = document.createElement("input");
	champ4.setAttribute("type", "hidden");
	champ4.setAttribute("name", 'formeMedicament');
	champ4.setAttribute("value", formeMedicament);
	formulaireImprimerDemandesAnalyses.appendChild(champ4);
	
	var champ5 = document.createElement("input");
	champ5.setAttribute("type", "hidden");
	champ5.setAttribute("name", 'nbMedicament');
	champ5.setAttribute("value", nbMedicament);
	formulaireImprimerDemandesAnalyses.appendChild(champ5);
	
	var champ6 = document.createElement("input");
	champ6.setAttribute("type", "hidden");
	champ6.setAttribute("name", 'quantiteMedicament');
	champ6.setAttribute("value", quantiteMedicament);
	formulaireImprimerDemandesAnalyses.appendChild(champ6);
	
	$("#imprimerOrdonnance").trigger('click');
}
