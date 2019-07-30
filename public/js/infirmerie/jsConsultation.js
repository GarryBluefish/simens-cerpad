
    $(function() {
	
    	$( "#accordionss" ).accordion();
    	$( "#accordions" ).accordion();
    	$( "button" ).button();

    });	
    
    function initialisationScript(agePatient) {
    	//******************* VALIDER LES DONNEES DU TABLEAU DES CONSTANTES ******************************** 
    	//******************* VALIDER LES DONNEES DU TABLEAU DES CONSTANTES ******************************** 
    	//Au debut on dï¿½sactive le code cons et la date de consultation qui sont non modifiables
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
		    			//alert('formulaire envoye');
						$( "#boutonBaliseTerminer button" ).attr('disabled', true);
						$( "#boutonEnvoyerDonnees" ).trigger('click');
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
						//alert('formulaire envoye');
						$( "#boutonBaliseTerminer button" ).attr('disabled', true);
						$( "#boutonEnvoyerDonnees" ).trigger('click');
					}

			}
    		
    	});
    	
    	
    	
    	//APPLICATION DE LA POSOLOGIE AUTOMATIQUEMENT POUR LE CAS "DOULEUR"
    	//APPLICATION DE LA POSOLOGIE AUTOMATIQUEMENT POUR LE CAS "DOULEUR"
    	var poidsPatient = 0;
    	
    	$('#poidsP1 input').change(function(){ 
    		
    		poidsPatient = $(this).val();
    		
    		if($(this).val()){
        		$('#poids').val($(this).val()).attr('readonly', false);
        		
        		$('#poidsP1Fievre input').val($(this).val());
        		var palier1 = 15 * $(this).val();
        		$('.poidsP1Fievre').html(palier1.toFixed(1)+" <span style='font-size: 13px;'> mg/j </span>");
    		}else{
        		$('#poids').val($(this).val()).attr('readonly', false);
        		
        		$('#poidsP1Fievre input').val($(this).val());
        		var palier1 = 15 * $(this).val();
        		$('.poidsP1Fievre').html(palier1.toFixed(1)+" <span style='font-size: 13px;'> mg/j </span>");
    		}
    		
    		$('#poidsP2 input, #poidsP2a input, #poidsP2b input, #poidsP3 input').val($(this).val()).attr('readonly', true); 
    	    
    		//Affichage des posologies pour les médicaments
    		//Affichage des posologies pour les médicaments
    		var palier1 = 15 * $(this).val();
    		$('.poidsP1').html(palier1.toFixed(1)+" <span style='font-size: 13px;'> mg/j </span>");
    		
    		var palier2 = 30 * $(this).val();
    		$('.poidsP2').html(palier2.toFixed(1)+" <span style='font-size: 13px;'> mg/j </span>");
    		
    		var palier2a = 1 * $(this).val();
    		$('.poidsP2a').html(palier2a.toFixed(1)+" <span style='font-size: 13px;'> mg/j </span>");
    		
    		var palier2b = 2 * $(this).val();
    		$('.poidsP2b').html(palier2b.toFixed(1)+" <span style='font-size: 13px;'> mg/j </span>");
    		
    		/*Palier 3 deux cas possibles*/
    		var voieAdminM5 = $('#voieAdminM5').val();
    		if(voieAdminM5 == 1){
    			$('#MorphineDosageInfos').html(" (0,1mg/kg)");
        		var palier3 = 0.1 * $(this).val();
        		$('.poidsP3').html(palier3.toFixed(1)+" <span style='font-size: 13px;'> mg/j </span>");
    		}else if(voieAdminM5 == 2){
    			$('#MorphineDosageInfos').html(" (15ug/kg)");
        		var palier3 = 15 * $(this).val();
        		$('.poidsP3').html(palier3.toFixed(1)+" <span style='font-size: 13px;'> ug/j </span>");
    		}
    	    
    	}).keyup(function(){
    		
    		poidsPatient = $(this).val();
    		
    		if($(this).val()){
        		$('#poids').val($(this).val()).attr('readonly', false);
        		
        		$('#poidsP1Fievre input').val($(this).val());
        		var palier1 = 15 * $(this).val();
        		$('.poidsP1Fievre').html(palier1.toFixed(1)+" <span style='font-size: 13px;'> mg/j </span>");
    		}else{
        		$('#poids').val($(this).val()).attr('readonly', false);
        		
        		$('#poidsP1Fievre input').val($(this).val());
        		var palier1 = 15 * $(this).val();
        		$('.poidsP1Fievre').html(palier1.toFixed(1)+" <span style='font-size: 13px;'> mg/j </span>");
    		}
    		
    		$('#poidsP2 input, #poidsP2a input, #poidsP2b input, #poidsP3 input').val($(this).val()).attr('readonly', true); 
    	
    		//Affichage des posologies pour les médicaments
    		//Affichage des posologies pour les médicaments
    		var palier1 = 15 * $(this).val();
    		$('.poidsP1').html(palier1.toFixed(1)+" <span style='font-size: 13px;'> mg/j </span>");
    		
    		var palier2 = 30 * $(this).val();
    		$('.poidsP2').html(palier2.toFixed(1)+" <span style='font-size: 13px;'> mg/j </span>");
    		
    		var palier2a = 1 * $(this).val();
    		$('.poidsP2a').html(palier2a.toFixed(1)+" <span style='font-size: 13px;'> mg/j </span>");
    		
    		var palier2b = 2 * $(this).val();
    		$('.poidsP2b').html(palier2b.toFixed(1)+" <span style='font-size: 13px;'> mg/j </span>");
    		
    		/*Palier 3 deux cas possibles*/
    		var voieAdminM5 = $('#voieAdminM5').val();
    		if(voieAdminM5 == 1){
    			$('#MorphineDosageInfos').html(" (0,1mg/kg)");
        		var palier3 = 0.1 * $(this).val();
        		$('.poidsP3').html(palier3.toFixed(1)+" <span style='font-size: 13px;'> mg/j </span>");
    		}else if(voieAdminM5 == 2){
    			$('#MorphineDosageInfos').html(" (15ug/kg)");
        		var palier3 = 15 * $(this).val();
        		$('.poidsP3').html(palier3.toFixed(1)+" <span style='font-size: 13px;'> ug/j </span>");
    		}
    	
    	});
    	
    	
    	$('#poids').change(function(){ 
    		$('#poidsP1Fievre input, #poidsP1 input').val($(this).val());
    		$('#poidsP1Fievre input, #poidsP1 input').trigger('keyup');
    		
    	}).keyup(function(){
    		$('#poidsP1Fievre input, #poidsP1 input').val($(this).val());
    		$('#poidsP1Fievre input, #poidsP1 input').trigger('keyup');
    		
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
            		$('.poidsP3').html(palier3.toFixed(1)+" <span style='font-size: 13px;'> ug/j </span>");
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
        		$('#poids').val($(this).val()).attr('readonly', false);
        		
        		$('#poidsP1 input').val($(this).val());
        		$('#poidsP2 input, #poidsP2a input, #poidsP2b input, #poidsP3 input').val($(this).val()).attr('readonly', true); 
        		var palier1 = 15 * $(this).val();
        		$('.poidsP1').html(palier1.toFixed(1)+" <span style='font-size: 13px;'> mg/j </span>");
        		var palier2 = 30 * $(this).val();
        		$('.poidsP2').html(palier2.toFixed(1)+" <span style='font-size: 13px;'> mg/j </span>");


    		}else{ 
    			$('#alertePriseEnChargeFievre input').trigger("click");
        		$('#poids').val($(this).val()).attr('readonly', false);
        		
        		$('#poidsP1 input').val($(this).val());
        		$('#poidsP2 input, #poidsP2a input, #poidsP2b input, #poidsP3 input').val($(this).val()).attr('readonly', true); 
        		var palier1 = 15 * $(this).val();
        		$('.poidsP1').html(palier1.toFixed(1)+" <span style='font-size: 13px;'> mg/j </span>");
        		var palier2 = 30 * $(this).val();
        		$('.poidsP2').html(palier2.toFixed(1)+" <span style='font-size: 13px;'> mg/j </span>");

    		}
    		
    		$('#poidsP1 input').trigger('keyup');
    	    	    
    	}).keyup(function(){
    		var palier1 = 15 * $(this).val();
    		$('.poidsP1Fievre').html(palier1.toFixed(1)+" <span style='font-size: 13px;'> mg/j </span>");
    		
    		if($(this).val()){
        		$('#poids').val($(this).val()).attr('readonly', false);
        		
        		$('#poidsP1 input').val($(this).val());
        		$('#poidsP2 input, #poidsP2a input, #poidsP2b input, #poidsP3 input').val($(this).val()).attr('readonly', true); 
        		var palier1 = 15 * $(this).val();
        		$('.poidsP1').html(palier1.toFixed(1)+" <span style='font-size: 13px;'> mg/j </span>");
        		var palier2 = 30 * $(this).val();
        		$('.poidsP2').html(palier2.toFixed(1)+" <span style='font-size: 13px;'> mg/j </span>");

    		}else{
    			$('#alertePriseEnChargeFievre input').trigger("click");
        		$('#poids').val($(this).val()).attr('readonly', false);
        		
        		$('#poidsP1 input').val($(this).val());
        		$('#poidsP2 input, #poidsP2a input, #poidsP2b input, #poidsP3 input').val($(this).val()).attr('readonly', true); 
        		var palier1 = 15 * $(this).val();
        		$('.poidsP1').html(palier1.toFixed(1)+" <span style='font-size: 13px;'> mg/j </span>");
        		var palier2 = 30 * $(this).val();
        		$('.poidsP2').html(palier2.toFixed(1)+" <span style='font-size: 13px;'> mg/j </span>");


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
    	
    	
    }
    	
  	

//Boite de dialogue de confirmation d'annulation
//Boite de dialogue de confirmation d'annulation
//Boite de dialogue de confirmation d'annulation

/***BOITE DE DIALOG POUR LA CONFIRMATION**/
$(document).ready(function() {
	var base_url = window.location.toString();
	var tabUrl = base_url.split("public");
	
	var theHREF = tabUrl[0]+"public/infirmerie/liste-consultations";
	function confirmation(){
		
  		$( "#confirmation" ).dialog({
  		    resizable: false,
  		    height:170,
  		    width:505,
  		    autoOpen: false,
  		    modal: true,
  		    buttons: {
  		        "Oui": function() {
  		            $( this ).dialog( "close" );
  		            window.location.href = theHREF;   
  		        },
  		        "Non": function() {
  		            $( this ).dialog( "close" );
  		        }
  		    }
  		});

	}
	
	$("#annuler").click(function() {
        event.preventDefault(); 
        confirmation(); 
        $("#confirmation").dialog('open');
    }); 

});






function infos_parentales()
{
	
	$('#infos_parentales_patient').w2overlay({ html: "" +
		"" +
		"<div style='border-bottom:1px solid green; height: 30px; background: #f9f9f9; width: 600px; text-align:center; padding-top: 10px; font-size: 13px; color: green; font-weight: bold;'><img style='padding-right: 10px;' src='"+tabUrl[0]+"public/images_icons/Infos_parentales.png' >Informations parentales</div>" +
		"<div style='height: 245px; width: 600px; padding-top:10px; text-align:center;'>" +
		"<div style='height: 77%; width: 95%; max-height: 77%; max-width: 95%; ' class='infos_parentales' align='left'>  </div>" +
		"</div>"+
		"<script> $('.infos_parentales').html( $('#infos_parentales_tampon').html() ); </script>" 
	});
	
}

