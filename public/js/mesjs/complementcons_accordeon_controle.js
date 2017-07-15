 var base_url = window.location.toString();
 var tabUrl = base_url.split("public");

  $(function(){
	$( "#accordionsssss").accordion();
  });
  
  $(function(){
    $( "#accordionssss").accordion();
  });

  $(function() {
	$( "#accordions_resultat" ).accordion();
	$( "#accordions_demande" ).accordion();
	$( "#accordionsss" ).accordion();
  });

  $(function() {
	$( "#accordionss" ).accordion();
  });

  $(function() {
    $( "#accordions" ).accordion();
  });
  
//********************* ANALYSE MORPHOLOGIQUE *****************************
//********************* ANALYSE MORPHOLOGIQUE *****************************
$(function(){
	var radio = $("#radio");
	var ecographie = $("#ecographie");
	var fibrocospie = $("#fibrocospie");
	var scanner = $("#scanner");
	var irm = $("#irm");
	
	//Au debut on affiche pas le bouton modifier
	$("#bouton_morpho_modifier").toggle(false);
	//Au debut on affiche le bouton valider
	$("#bouton_morpho_valider").toggle(true);
	
	//Au debut on desactive tous les champs
	radio.attr( 'readonly', false);
	ecographie.attr( 'readonly', false);
	fibrocospie.attr( 'readonly', false);
	scanner.attr( 'readonly', false);
	irm.attr( 'readonly', false);
	
	$("#bouton_morpho_valider").click(function(){
		radio.attr( 'readonly', true);
		ecographie.attr( 'readonly', true);
		fibrocospie.attr( 'readonly', true);
		scanner.attr( 'readonly', true);
		irm.attr( 'readonly', true);
		
		$("#bouton_morpho_modifier").toggle(true);
		$("#bouton_morpho_valider").toggle(false);
		return false;
	});
	
	$("#bouton_morpho_modifier").click(function(){
		radio.attr( 'readonly', false);
		ecographie.attr( 'readonly', false);
		fibrocospie.attr( 'readonly', false);
		scanner.attr( 'readonly', false);
		irm.attr( 'readonly', false);
		
		$("#bouton_morpho_modifier").toggle(false);
		$("#bouton_morpho_valider").toggle(true);
		return false;
	});
	
});
  
  
//********************* TRAITEMENTS CHIRURGICAUX *****************************
//********************* TRAITEMENTS CHIRURGICAUX ***************************** 
//********************* TRAITEMENTS CHIRURGICAUX ***************************** 
$(function(){
	var diagnostic_traitement_chirurgical = $("#diagnostic_traitement_chirurgical");
	var intervention_prevue = $("#intervention_prevue");
	var observation = $("#observation");
	
	$("#chirurgical1").click(function(){
		diagnostic_traitement_chirurgical.attr( 'readonly', true);
		intervention_prevue.attr( 'readonly', true);
		observation.attr( 'readonly', true);
		
		$("#bouton_chirurgical_modifier").toggle(true);
		$("#bouton_chirurgical_valider").toggle(false);	
	});
	
	//Au debut on affiche pas le bouton modifier, on l'affiche seulement apres impression
	$("#bouton_chirurgical_modifier").toggle(false);
	//Au debut on affiche le bouton valider
	$("#bouton_chirurgical_valider").toggle(true);
	
	//Au debut on desactive tous les champs
	diagnostic_traitement_chirurgical.attr( 'readonly', false);
	intervention_prevue.attr( 'readonly', false);
	observation.attr( 'readonly', false);
	
	$("#bouton_chirurgical_valider").click(function(){
		diagnostic_traitement_chirurgical.attr( 'readonly', true);
		intervention_prevue.attr( 'readonly', true);
		observation.attr( 'readonly', true);
		
		$("#bouton_chirurgical_modifier").toggle(true);
		$("#bouton_chirurgical_valider").toggle(false);
		
		$("#annuler_traitement_chirurgical").attr('disabled', true);
		return false;
	});
	
	$("#bouton_chirurgical_modifier").click(function(){
		diagnostic_traitement_chirurgical.attr( 'readonly', false);
		intervention_prevue.attr( 'readonly', false);
		observation.attr( 'readonly', false);
		
		$("#bouton_chirurgical_modifier").toggle(false);
		$("#bouton_chirurgical_valider").toggle(true);
		
		$("#annuler_traitement_chirurgical").attr('disabled', false);
		return false;
	});
	
});

//********************* TRAITEMENTS INSTRUMENTAUX *****************************
//********************* TRAITEMENTS INSTRUMENTAUX ***************************** 
//********************* TRAITEMENTS INSTRUMENTAUX ***************************** 
$(function(){
	var endoscopieInterventionnelle = $("#endoscopieInterventionnelle");
	var radiologieInterventionnelle = $("#radiologieInterventionnelle");
	var cardiologieInterventionnelle = $("#cardiologieInterventionnelle"); 
	var autresIntervention = $("#autresIntervention");
	
	$("#chirurgicalImpression").click(function(){
		endoscopieInterventionnelle.attr( 'readonly', true);
		radiologieInterventionnelle.attr( 'readonly', true);
		autresIntervention.attr( 'readonly', true);
		cardiologieInterventionnelle.attr( 'readonly', true);
		
		$("#bouton_chirurgical_modifier").toggle(true);
		$("#bouton_chirurgical_valider").toggle(false);	
	});
	
	$("#bouton_instrumental_modifier").toggle(false);
	$("#bouton_instrumental_valider").toggle(true);
	
	endoscopieInterventionnelle.attr( 'readonly', false);
	radiologieInterventionnelle.attr( 'readonly', false);
	autresIntervention.attr( 'readonly', false);
	cardiologieInterventionnelle.attr( 'readonly', false);
	
	$("#bouton_instrumental_valider").click(function(){
		endoscopieInterventionnelle.attr( 'readonly', true);
		radiologieInterventionnelle.attr( 'readonly', true);
		autresIntervention.attr( 'readonly', true);
		cardiologieInterventionnelle.attr( 'readonly', true);
		
		$("#bouton_instrumental_modifier").toggle(true);
		$("#bouton_instrumental_valider").toggle(false);
		
		$('#annuler_traitement_instrumental').attr('disabled', true);
		return false;
	});
	
	$("#bouton_instrumental_modifier").click(function(){
		endoscopieInterventionnelle.attr( 'readonly', false);
		radiologieInterventionnelle.attr( 'readonly', false);
		autresIntervention.attr( 'readonly', false);
		cardiologieInterventionnelle.attr( 'readonly', false);
		
		$("#bouton_instrumental_modifier").toggle(false);
		$("#bouton_instrumental_valider").toggle(true);
		
		$('#annuler_traitement_instrumental').attr('disabled', false);
		return false;
	});
	
	
	$('#annuler_traitement_instrumental').click(function(){
		endoscopieInterventionnelle.val('');
		radiologieInterventionnelle.val('');
		autresIntervention.val('');
		cardiologieInterventionnelle.val('');
		return false;
	});
	
	
	//COMPTE RENDU OPERATOIRE CHIRURGICAL
	//COMPTE RENDU OPERATOIRE CHIRURGICAL
	$("#bouton_compte_rendu_chirurgical_valider").toggle(true);
	$("#bouton_compte_rendu_chirurgical_modifier").toggle(false);
	
	$("#bouton_compte_rendu_chirurgical_valider").click(function(){
		$("#note_compte_rendu_operatoire").attr( 'readonly', true);
		
		$("#bouton_compte_rendu_chirurgical_valider").toggle(false);
		$("#bouton_compte_rendu_chirurgical_modifier").toggle(true);
		
		return false;
	});
	
	$("#bouton_compte_rendu_chirurgical_modifier").click(function(){
		$("#note_compte_rendu_operatoire").attr( 'readonly', false);
		
		$("#bouton_compte_rendu_chirurgical_valider").toggle(true);
		$("#bouton_compte_rendu_chirurgical_modifier").toggle(false);
		
		return false;
	});
	
	//COMPTE RENDU OPERATOIRE INSTRUMENTAL
	//COMPTE RENDU OPERATOIRE INSTRUMENTAL
	$("#bouton_compte_rendu_instrumental_valider").toggle(true);
	$("#bouton_compte_rendu_instrumental_modifier").toggle(false);
	
	
	$("#bouton_compte_rendu_instrumental_valider").click(function(){
		$("#note_compte_rendu_operatoire_instrumental").attr( 'readonly', true);
		
		$("#bouton_compte_rendu_instrumental_valider").toggle(false);
		$("#bouton_compte_rendu_instrumental_modifier").toggle(true);
		
		return false;
	});
	
	$("#bouton_compte_rendu_instrumental_modifier").click(function(){
		$("#note_compte_rendu_operatoire_instrumental").attr( 'readonly', false);
		
		$("#bouton_compte_rendu_instrumental_valider").toggle(true);
		$("#bouton_compte_rendu_instrumental_modifier").toggle(false);
		
		return false;
	});
	
});

// *************Autres(Transfert/Hospitalisation/ Rendez-Vous )***************
// *************Autres(Transfert/Hospitalisation/ Rendez-Vous )***************
// *************Autres(Transfert/Hospitalisation/ Rendez-Vous )***************

// ******************* Tranfert ******************************** 
// ******************* Tranfert ******************************** 
$(function(){
	var motif_transfert = $("#motif_transfert");
	var hopital_accueil = $("#hopital_accueil");
	var service_accueil = $("#service_accueil");
//	$("#transfert").click(function(){ 
//		motif_transfert.attr( 'readonly', true);
//		$("#hopital_accueil_tampon").val(hopital_accueil.val());
//		//hopital_accueil.attr( 'disabled', true);
//		$("#service_accueil_tampon").val(service_accueil.val());
//		//service_accueil.attr( 'disabled', true);
//		$("#bouton_transfert_modifier").toggle(true);  //on affiche le bouton permettant de modifier les champs
//	    $("#bouton_transfert_valider").toggle(false); //on cache le bouton permettant de valider les champs
//	});

	$( "bouton_valider_transfert" ).button();
	$( "bouton_modifier_transfert" ).button();

	//Au debut on cache le bouton modifier et on affiche le bouton valider
	$( "#bouton_transfert_valider" ).toggle(true);
	$( "#bouton_transfert_modifier" ).toggle(false);

	//Au debut on desactive tous les champs
	motif_transfert.attr( 'readonly', false );;
	hopital_accueil.attr( 'disabled', false );;
	service_accueil.attr( 'disabled', false );;

	//Valider(cachï¿½) avec le bouton 'valider'
	$( "#bouton_transfert_valider" ).click(function(){
		motif_transfert.attr( 'readonly', true );     //dï¿½sactiver le motif transfert
		$("#hopital_accueil_tampon").val(hopital_accueil.val());
		hopital_accueil.attr( 'disabled', true );     //dï¿½sactiver hopital accueil
		$("#service_accueil_tampon").val(service_accueil.val());
		service_accueil.attr( 'disabled', true );   //dï¿½sactiver service accueil
		$("#bouton_transfert_modifier").toggle(true);  //on affiche le bouton permettant de modifier les champs
		$("#bouton_transfert_valider").toggle(false); //on cache le bouton permettant de valider les champs
		
		$("#annulertransfert").attr('disabled', true);
		return false; 
	});
	//Activer(dï¿½cachï¿½) avec le bouton 'modifier'
	$( "#bouton_transfert_modifier" ).click(function(){
		motif_transfert.attr( 'readonly', false );
		hopital_accueil.attr( 'disabled', false );
		service_accueil.attr( 'disabled', false );
	 	$("#bouton_transfert_modifier").toggle(false);   //on cache le bouton permettant de modifier les champs
	 	$("#bouton_transfert_valider").toggle(true);    //on affiche le bouton permettant de valider les champs
	 	
	 	$("#annulertransfert").attr('disabled', false);
	 	return  false;
	});
});

//********************* HOSPITALISATION *****************************
//********************* HOSPITALISATION *****************************
$(function(){
	var motif_hospitalisation = $("#motif_hospitalisation");
	var date_fin_hospitalisation = $("#date_fin_hospitalisation_prevue");
//	$("#hospitalisation").click(function(){
//		motif_hospitalisation.attr( 'disabled', true);
//		date_fin_hospitalisation.attr( 'disabled', true);
//		$("#bouton_hospi_modifier").toggle(true);
//		$("#bouton_hospi_valider").toggle(false);	
//	});
	
	$("#annulerhospitalisation").click(function(){
		motif_hospitalisation.val("");
		date_fin_hospitalisation.val("");
		return false;
	});
	//Au debut on affiche pas le bouton modifier
	$("#bouton_hospi_modifier").toggle(false);
	//Au debut on affiche le bouton valider
	$("#bouton_hospi_valider").toggle(true);
	
	//Au debut on desactive tous les champs
	motif_hospitalisation.attr( 'disabled', false);
	date_fin_hospitalisation.attr( 'disabled', false);
	
	$("#bouton_hospi_valider").click(function(){
		motif_hospitalisation.attr( 'disabled', true);
		date_fin_hospitalisation.attr( 'disabled', true);
		$("#bouton_hospi_modifier").toggle(true);
		$("#bouton_hospi_valider").toggle(false);
		
	 	$("#annulerhospitalisation").attr('disabled', true);
		return false;
	});
	
	$("#bouton_hospi_modifier").click(function(){
		motif_hospitalisation.attr( 'disabled', false);
		date_fin_hospitalisation.attr( 'disabled', false);
		$("#bouton_hospi_modifier").toggle(false);
		$("#bouton_hospi_valider").toggle(true);
		
	 	$("#annulerhospitalisation").attr('disabled', false);
		return false;
	});
	
	
	
});

//********************* RENDEZ VOUS *****************************
//********************* RENDEZ VOUS *****************************
 $(function() {
 var motif_rv = $('#motif_rv');
 var date_rv = $( "#date_rv" );
 var heure_rv = $("#heure_rv");
   date_rv.attr('autocomplete', 'off');
   $( "#disable" ).click(function(){
	  motif_rv.attr( 'readonly', true );     //dï¿½sactiver le motif
	  $("#date_rv_tampon").val(date_rv.val()); //Placer la date dans date_rv_tampon avant la desacivation
      date_rv.attr( 'disabled', true );     //dï¿½sactiver la date
      $("#heure_rv_tampon").val(heure_rv.val()); //Placer l'heure dans heure_rv_tampon avant la desacivation
      heure_rv.attr( 'disabled', true );   //dï¿½sactiver l'heure
      $("#disable_bouton").toggle(true);  //on affiche le bouton permettant de modifier les champs
      $("#enable_bouton").toggle(false); //on cache le bouton permettant de valider les champs
 
      date_rv.val(date);
   });
   
   $( "button" ).button();
   //$( "bouton_valider" ).button();

   //Au debut on affiche pas le bouton modifier, on l'affiche seulement apres impression
   $("#disable_bouton").toggle(false);
   //Au debut on affiche le bouton valider
   $("#enable_bouton").toggle(true);
   
   //Au debut on desactive tous les champs
   motif_rv.attr( 'readonly', false );
   date_rv.attr( 'disabled', false );
   heure_rv.attr( 'disabled', false );

   //Valider(cachï¿½) avec le bouton 'valider'
   $( "#enable_bouton" ).click(function(){
	  motif_rv.attr( 'readonly', true );     //dï¿½sactiver le motif
	  $("#date_rv_tampon").val(date_rv.val()); //Placer la date dans date_rv_tampon avant la desacivation
      date_rv.attr( 'disabled', true );     //dï¿½sactiver la date
      $("#heure_rv_tampon").val(heure_rv.val()); //Placer l'heure dans heure_rv_tampon avant la desacivation
	  heure_rv.attr( 'disabled', true );   //dï¿½sactiver l'heure
	  $("#disable_bouton").toggle(true);  //on affiche le bouton permettant de modifier les champs
	  $("#enable_bouton").toggle(false); //on cache le bouton permettant de valider les champs
	  
	  $("#annulerrendezvous").attr('disabled', true);
	  return false; 
   });
   //Activer(dï¿½cachï¿½) avec le bouton 'modifier'
   $( "#disable_bouton" ).click(function(){
	  motif_rv.attr( 'readonly', false );      //activer le motif
	  date_rv.attr( 'disabled', false );      //activer la date
	  heure_rv.attr( 'disabled', false );    //activer l'heure
 	  $("#disable_bouton").toggle(false);   //on cache le bouton permettant de modifier les champs
 	  $("#enable_bouton").toggle(true);    //on affiche le bouton permettant de valider les champs
 	  
	  $("#annulerrendezvous").attr('disabled', false);
 	  return  false;
   });
   
 });
 
//Boite de dialogue de confirmation d'annulation
//Boite de dialogue de confirmation d'annulation
//Boite de dialogue de confirmation d'annulation

/***BOITE DE DIALOG POUR LA CONFIRMATION DE SUPPRESSION**/
/***BOITE DE DIALOG POUR LA CONFIRMATION DE SUPPRESSION**/

	var theHREF = tabUrl[0]+"public/consultation/liste-consultations";
	function confirmation(){
		
 		$( "#confirmation2" ).dialog({
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
	
	$("#annuler2").click(function() {
       confirmation(); 
       $("#confirmation2").dialog('open');
       
       return false;
    }); 
	
	
	
	var temoinTaille = 0;
	var temoinPoids = 0;
	var temoinTemperature = 0;
	var temoinPouls = 0;
	var temoinTensionMaximale = 0;
	var temoinTensionMinimale = 0;
		
	var valid = true;  // VARIABLE GLOBALE utilisï¿½e dans 'VALIDER LES DONNEES DU TABLEAU DES CONSTANTES'
	/****** ======================================================================= *******/
	/****** ======================================================================= *******/
	/****** ======================================================================= *******/
	/****** CONTROLE APRES VALIDATION ********/ 
	/****** CONTROLE APRES VALIDATION ********/ 
	
	
	
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
    		if($('#motif_admission1').val() == 'Douleur' || 
    	       $('#motif_admission2').val() == 'Douleur' ||
    	       $('#motif_admission3').val() == 'Douleur' ||
    	       $('#motif_admission4').val() == 'Douleur' ||
    	       $('#motif_admission5').val() == 'Douleur'
    	      ){
    			
    			if($('#intensite').val() > 3 && entrePriseEnCharge == 0){ 
    				popListeMedicaments();
    				return false;
    			}
    	    	
    		}

    		//Affichage du pop-pup des médicaments lors d'une fièvre (température 37.5)
    		//Affichage du pop-pup des médicaments lors d'une fièvre (température 37.5)
    		if($('#temperatureFievre').val() >= 37.5 || $('#temperature').val() >= 37.5){
    			
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
		    			//alert('formulaire envoye');
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
						//alert('formulaire envoye');
						return true;
					}

			}
    		
    	});
    	
    	
    	
    	//APPLICATION DE LA POSOLOGIE AUTOMATIQUEMENT POUR LE CAS "DOULEUR"
    	//APPLICATION DE LA POSOLOGIE AUTOMATIQUEMENT POUR LE CAS "DOULEUR"
    	var poidsPatient = 0;
    	
    	
    	$('#poidsP1 input').change(function(){ 
    		var palier1 = 15 * $(this).val();
    		$('.poidsP1').html(palier1+" <span style='font-size: 13px;'> mg/j </span>");
    		
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
    		var palier2 = 30 * $(this).val();
    		$('.poidsP2').html(palier2+" <span style='font-size: 13px;'> mg/j </span>");
    	    
    	}).keyup(function(){
    		var palier1 = 15 * $(this).val();
    		$('.poidsP1').html(palier1+" <span style='font-size: 13px;'> mg/j </span>");
    		
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
    		var palier2 = 30 * $(this).val();
    		$('.poidsP2').html(palier2+" <span style='font-size: 13px;'> mg/j </span>");
    	
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
    		
    	});
    	
    	//Lors de la saisie de la température au pop-pup
    	//Lors de la saisie de la température au pop-pup
    	$('#infoTemperatureFievre input').change(function(){
    		var valeur = $(this).val();
    		$("#temperature").val(valeur);
    		
    		if( valeur >= 37.5 ){
    			$('#infoPriseEnChargeFievre').toggle(true);
    		}else{
    			$('#infoPriseEnChargeFievre').toggle(false);
    		}
    		
    	});
    	
    	//Lors de la saisie de la température sur les constantes
    	//Lors de la saisie de la température sur les constantes
    	$('#temperature').change(function(){
    		var valeur = $(this).val();
    		
    		if( valeur >= 37.5 ){
    			
    			$('#infoTemperatureFievre input').val(valeur);
    			popListeMedicamentsFievre();
    			$('#infoPriseEnChargeFievre').toggle(true);
    			
    			var existeFievre = 0;
    			for(var ind = 1 ; ind <= nbChampMotif ; ind++){
    				var val = $("#motif_admission"+ind).val();
    				if(val == 'FiÃ¨vre'){ existeFievre = 1; break;}
    			}
    			if(existeFievre == 0){
    				if(nbChampMotif == 1 && $('#motif_admission'+(nbChampMotif)).val()=="" || nbChampMotif == 5){
    					$('#motif_admission'+(nbChampMotif)).val('FiÃ¨vre');
    				}else{
    					$('#ajouter_motif_img').trigger('click'); 
    					$('#motif_admission'+(nbChampMotif)).val('FiÃ¨vre');    
    				}
    			}
    			
    		}else{
    			$('#infoPriseEnChargeFievre').toggle(false);
    			$('#infoTemperatureFievre input').val(valeur);
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
	
	
	
	
	
	
	
	
	
	
	
	
	
//	$("#terminer,#bouton_constantes_valider, #terminer2, #terminer3").click(function(){
//
//	     	 valid = true;
////	         if( $("#taille").val() == "" || temoinTaille == 1){
////	             $("#taille").css("border-color","#FF0000");
////	             $("#erreur_taille").fadeIn().text("Max: 250cm").css({"color":"#ff5b5b","padding":" 0 10px 0 10px","margin-top":"-18px","font-size":"13px","font-style":"italic"});
////	             valid = false;
////	         }
////	         else{
////	         	$("#taille").css("border-color","");
////	         	$("#erreur_taille").fadeOut();
////	         }
//	         
//	         if( $("#poids").val() == "" || temoinPoids == 2){
//	         	 $("#poids").css("border-color","#FF0000");
//	             $("#erreur_poids").fadeIn().text("Max: 300kg").css({"color":"#ff5b5b","padding":" 0 10px 0 10px","margin-top":"-18px","font-size":"13px","font-style":"italic"});
//	             valid = false;
//	         }
//	         else{
//	         	$("#poids").css("border-color", "");
//	         	$("#erreur_poids").fadeOut();
//	         }
//	         
//	         if( $('#temperature').val() == "" || temoinTemperature == 3){
//	         	$("#temperature").css("border-color","#FF0000");
//	         	$("#erreur_temperature").fadeIn().text("Min: 34Â°C, Max: 45Â°C").css({"color":"#ff5b5b","padding":" 0 10px 0 10px","margin-top":"-18px","font-size":"13px","font-style":"italic"});
//	             valid = false;
//	         }
//	         else{
//	         	$("#temperature").css("border-color", "");
//	         	$("#erreur_temperature").fadeOut();
//	         }
//	         
////	         if( $("#pouls").val() == "" || temoinPouls == 4){
////	         	 $("#pouls").css("border-color","#FF0000");
////	             $("#erreur_pouls").fadeIn().text("Max: 150 battements").css({"color":"#ff5b5b","padding":" 0 10px 0 10px","margin-top":"-18px","font-size":"13px","font-style":"italic"});
////	             valid = false;
////	         }
////	         else{
////	         	 $("#pouls").css("border-color", "");
////	             $("#erreur_pouls").fadeOut();
////	         }
//	         
//	         if( $("#tensionmaximale").val() == "" || temoinTensionMaximale == 5){
//	         	 $("#tensionmaximale").css("border-color","#FF0000");
//		    	 $("#erreur_tensionmaximale").fadeIn().text("300mmHg").css({"color":"#ff5b5b","padding":" 0 10px 0 10px","margin-top":"-18px","font-size":"13px","font-style":"italic"});
//	             valid = false;
//	         }
//	         else{
//	         	 $("#tensionmaximale").css("border-color", "");
//	             $("#erreur_tensionmaximale").fadeOut();
//	         }
//	         
//	         if( $("#tensionminimale").val() == "" || temoinTensionMinimale == 6 ){
//	         	 $("#tensionminimale").css("border-color","#FF0000");
//		    	 $("#erreur_tensionminimale").fadeIn().text("200mmHg").css({"color":"#ff5b5b","padding":" 0 10px 0 105px","margin-top":"-18px","font-size":"13px","font-style":"italic"});
//	             valid = false;
//	         }
//	         else{
//	         	 $("#tensionminimale").css("border-color", "");
//	             $("#erreur_tensionminimale").fadeOut();
//	         }
//	         
//	         return false;
//	 	}); 

//-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*--*-*-*-*-*-*-*-*-*--*-*-*-*--*-*-*-*-*-**--*-**-*--**-*-*-*-*-*-*-*-*-*-*-*-*-*--**-*-*-*-*-	
	//Method envoi POST pour updatecomplementconsultation
	//Method envoi POST pour updatecomplementconsultation
	//Method envoi POST pour updatecomplementconsultation
//	function updateexecuterRequetePost(donnees) {
//		// Le formulaire monFormulaire existe deja dans la page
//	    var formulaire = document.createElement("form");
//	 
//	    formulaire.setAttribute("action",tabUrl[0]+"public/consultation/update-complement-consultation"); 
//	    formulaire.setAttribute("method","POST"); 
//	    for( donnee in donnees){
//	     // Ajout dynamique de champs dans le formulaire
//	        var champ = document.createElement("input");
//	        champ.setAttribute("type", "hidden");
//	        champ.setAttribute("name", donnee);
//	        champ.setAttribute("value", donnees[donnee]);
//	        formulaire.appendChild(champ);
//	    }
//        
//	    // Envoi de la requete
//	    formulaire.submit();
//	    // Suppression du formulaire
//	    document.body.removeChild(formulaire);
//	}
	
    /***LORS DU CLICK SUR 'Terminer' ****/
	/***LORS DU CLICK SUR 'Terminer' ****/
//	$("#terminer2, #terminer3").click(function() {
//		if (valid == false){ 
//			$('#motifsAdmissionConstanteClick').trigger('click');
//			$('#constantesClick').trigger('click');
//			return false;
//		}
//		
//		$('#bouton_Acte_valider_demande button, #bouton_ExamenBio_valider_demande button, #bouton_morpho_valider_demande button').trigger('click');
//		
//	    var donnees = new Array();
//	    donnees['id_cons']    = $("#id_cons").val();
//	    donnees['terminer'] = 'save';
//	    
//	    // **********-- Donnees de l'examen physique --*******
//        // **********-- Donnees de l'examen physique --*******
//	    donnees['examen_donnee1'] = $("#examen_donnee1").val();
//	    donnees['examen_donnee2'] = $("#examen_donnee2").val();
//	    donnees['examen_donnee3'] = $("#examen_donnee3").val();
//	    donnees['examen_donnee4'] = $("#examen_donnee4").val();
//	    donnees['examen_donnee5'] = $("#examen_donnee5").val();
//	    
//	    //**********-- ANALYSE BIOLOGIQUE --************
//        //**********-- ANALYSE BIOLOGIQUE --************
//	    donnees['groupe_sanguin']      = $("#groupe_sanguin").val();
//	    donnees['hemogramme_sanguin']  = $("#hemogramme_sanguin").val();
//	    donnees['bilan_hemolyse']      = $("#bilan_hemolyse").val();
//	    donnees['bilan_hepatique']     = $("#bilan_hepatique").val();
//	    donnees['bilan_renal']         = $("#bilan_renal").val();
//	    donnees['bilan_inflammatoire'] = $("#bilan_inflammatoire").val();
//	    
//	    //**********-- ANALYSE MORPHOLOGIQUE --************
//        //**********-- ANALYSE MORPHOLOGIQUE --************
//	    donnees['radio_']        = $("#radio").val();
//	    donnees['ecographie_']   = $("#ecographie").val();
//	    donnees['fibroscopie_']  = $("#fibrocospie").val();
//	    donnees['scanner_']      = $("#scanner").val();
//	    donnees['irm_']          = $("#irm").val();
//	    
//	    //*********** DIAGNOSTICS ************
//	    //*********** DIAGNOSTICS ************
//	    donnees['diagnostic1'] = $("#diagnostic1").val();
//	    donnees['diagnostic2'] = $("#diagnostic2").val();
//	    donnees['diagnostic3'] = $("#diagnostic3").val();
//	    donnees['diagnostic4'] = $("#diagnostic4").val();
//	    
//	    //*********** ORDONNACE (Mï¿½dical) ************
//	    //*********** ORDONNACE (Mï¿½dical) ************
//	    donnees['duree_traitement_ord'] = $("#duree_traitement_ord").val();
//	     
//	    for(var i = 1 ; i < 10 ; i++ ){
//	     	if($("#medicament_0"+i).val()){
//	     		donnees['medicament_0'+i] = $("#medicament_0"+i).val();
//	     		donnees['forme_'+i] = $("#forme_"+i).val();
//	     		donnees['nb_medicament_'+i] = $("#nb_medicament_"+i).val();
//	     		donnees['quantite_'+i] = $("#quantite_"+i).val();
//	     	}
//	     }
//	    
//	    //*********** TRAITEMENTS CHIRURGICAUX ************
//		//*********** TRAITEMENTS CHIRURGICAUX ************
//	    donnees['diagnostic_traitement_chirurgical'] = $("#diagnostic_traitement_chirurgical").val();
//	    donnees['intervention_prevue'] = $("#intervention_prevue").val();
//	    donnees['type_anesthesie_demande'] = $("#type_anesthesie_demande").val();
//	    donnees['numero_vpa'] = $("#numero_vpa").val();
//	    donnees['observation'] = $("#observation").val();
//	    donnees['note_compte_rendu_operatoire'] = $("#note_compte_rendu_operatoire").val();
//
//	    //*********** TRAITEMENTS INSTRUMENTAL ************
//		//*********** TRAITEMENTS INSTRUMENTAL ************
//	    donnees['endoscopieInterventionnelle'] = $("#endoscopieInterventionnelle").val();
//	    donnees['radiologieInterventionnelle'] = $("#radiologieInterventionnelle").val();
//	    donnees['cardiologieInterventionnelle'] = $("#cardiologieInterventionnelle").val();
//	    donnees['autresIntervention'] = $("#autresIntervention").val();
//	    donnees['note_compte_rendu_operatoire_instrumental'] = $("#note_compte_rendu_operatoire_instrumental").val();
//
//	    
//	    // **********-- Rendez Vous --*******
//        // **********-- Rendez Vous --*******
//		donnees['id_patient'] = $("#id_patient").val();
//		//Au cas ou l'utilisateur ne valide pas ou n'imprime pas cela veut dire que le champ n'est pas dï¿½sactiver
//		   if($("#date_rv").val()){$("#date_rv_tampon").val($("#date_rv").val());}
//		donnees['date_rv']    = $("#date_rv_tampon").val();
//		donnees['motif_rv']   = $("#motif_rv").val();
//		donnees['heure_rv']   = $("#heure_rv").val();
//		
//		// **********-- Hospitalisation --*******
//        // **********-- Hospitalisation --*******
//		//Desactivation des champs pour la recuperation des donnees
//		$("#motif_hospitalisation").attr( 'disabled', false);
//		$("#date_fin_hospitalisation_prevue").attr( 'disabled', false);
//		donnees['motif_hospitalisation'] = $("#motif_hospitalisation").val();
//		donnees['date_fin_hospitalisation_prevue'] = $("#date_fin_hospitalisation_prevue").val();
//		
//		// **********-- Transfert --*******
//        // **********-- Transfert --*******
//		//Au cas ou l'utilisateur ne valide pas ou n'imprime pas cela veut dire que le champ n'est pas dï¿½sactiver
//		   if($("#service_accueil").val()){$("#service_accueil_tampon").val($("#service_accueil").val());};
//		
//		donnees['id_service']      = $("#service_accueil_tampon").val();
//		donnees['med_id_personne'] = $("#id_medecin").val();
//		donnees['date']            = $("#date_cons").val();
//		donnees['motif_transfert'] = $("#motif_transfert").val();
//	    
//		//**********-- LES MOTIFS D'ADMISSION --********
//		//**********-- LES MOTIFS D'ADMISSION --********
//		//**********-- LES MOTIFS D'ADMISSION --********
//		donnees['motif_admission1'] = $("#motif_admission1").val();
//		donnees['motif_admission2'] = $("#motif_admission2").val();
//		donnees['motif_admission3'] = $("#motif_admission3").val();
//		donnees['motif_admission4'] = $("#motif_admission4").val();
//		donnees['motif_admission5'] = $("#motif_admission5").val();
//		
//		//**********-- LES CONSTANTES CONSTANTES CONSTANTES --********
//		//**********-- LES CONSTANTES CONSTANTES CONSTANTES --********
//		//**********-- LES CONSTANTES CONSTANTES CONSTANTES --********
//		//Recuperer les valeurs des champs
//		//Recuperer les valeurs des champs
//		donnees['poids'] = $("#poids").val();
//		donnees['taille'] = $("#taille").val();
//		donnees['temperature'] = $("#temperature").val();
//		donnees['tensionmaximale'] = $("#tensionmaximale").val();
//		donnees['tensionminimale'] = $("#tensionminimale").val();
//		donnees['pouls'] = $("#pouls").val();
//		donnees['frequence_respiratoire'] = $("#frequence_respiratoire").val();
//		donnees['glycemie_capillaire'] = $("#glycemie_capillaire").val();
//		
//		//Recuperer les donnees sur les bandelettes urinaires
//		//Recuperer les donnees sur les bandelettes urinaires
//		donnees['albumine'] = $('#BUcheckbox input[name=albumine]:checked').val();
//		if(!donnees['albumine']){ donnees['albumine'] = 0;}
//		donnees['croixalbumine'] = $('#BUcheckbox input[name=croixalbumine]:checked').val();
//		if(!donnees['croixalbumine']){ donnees['croixalbumine'] = 0;}
//
//		donnees['sucre'] = $('#BUcheckbox input[name=sucre]:checked').val();
//		if(!donnees['sucre']){ donnees['sucre'] = 0;}
//		donnees['croixsucre'] = $('#BUcheckbox input[name=croixsucre]:checked').val();
//		if(!donnees['croixsucre']){ donnees['croixsucre'] = 0;}
//		
//		donnees['corpscetonique'] = $('#BUcheckbox input[name=corpscetonique]:checked').val();
//		if(!donnees['corpscetonique']){ donnees['corpscetonique'] = 0;}
//		donnees['croixcorpscetonique'] = $('#BUcheckbox input[name=croixcorpscetonique]:checked').val();
//		if(!donnees['croixcorpscetonique']){ donnees['croixcorpscetonique'] = 0;}
//		
//		//GESTION DES ANDECEDENTS
//		//GESTION DES ANDECEDENTS
//		//GESTION DES ANDECEDENTS
//		//GESTION DES ANDECEDENTS
//		//**=== ANTECEDENTS PERSONNELS
//		//**=== ANTECEDENTS PERSONNELS
//		
//		//LES HABITUDES DE VIE DU PATIENTS
//		/*Alcoolique*/
//		donnees['AlcooliqueHV'] = $("#AlcooliqueHV:checked").val();
//		if(!donnees['AlcooliqueHV']){ donnees['AlcooliqueHV'] = 0;}
//		donnees['DateDebutAlcooliqueHV'] = $("#DateDebutAlcooliqueHV").val();
//		donnees['DateFinAlcooliqueHV'] = $("#DateFinAlcooliqueHV").val();
//		/*Fumeur*/
//		donnees['FumeurHV'] = $("#FumeurHV:checked").val();
//		if(!donnees['FumeurHV']){ donnees['FumeurHV'] = 0;}
//		donnees['DateDebutFumeurHV'] = $("#DateDebutFumeurHV").val();
//		donnees['DateFinFumeurHV'] = $("#DateFinFumeurHV").val();
//		donnees['nbPaquetFumeurHV'] = $("#nbPaquetFumeurHV").val();
//		/*Droguer*/ 
//		donnees['DroguerHV'] = $("#DroguerHV:checked").val(); 
//		if(!donnees['DroguerHV']){ donnees['DroguerHV'] = 0;}
//		donnees['DateDebutDroguerHV'] = $("#DateDebutDroguerHV").val();
//		donnees['DateFinDroguerHV'] = $("#DateFinDroguerHV").val();
//		/*AutresHV*/
//		donnees['AutresHV'] = $("#AutresHV:checked").val(); 
//		if(!donnees['AutresHV']){ donnees['AutresHV'] = 0;}
//		donnees['NoteAutresHV'] = $("#NoteAutresHV").val();
//		
//		//LES ANTECEDENTS MEDICAUX
//		/*Diabete*/ 
//		donnees['DiabeteAM'] = $("#DiabeteAM:checked").val(); 
//		if(!donnees['DiabeteAM']){ donnees['DiabeteAM'] = 0;}
//		/*Hta*/
//		donnees['htaAM'] = $("#htaAM:checked").val();
//		if(!donnees['htaAM']){ donnees['htaAM'] = 0;}
//		/*Drepanocytose*/
//		donnees['drepanocytoseAM'] = $("#drepanocytoseAM:checked").val(); 
//		if(!donnees['drepanocytoseAM']){ donnees['drepanocytoseAM'] = 0;}
//		/*Dislipidémie*/
//		donnees['dislipidemieAM'] = $("#dislipidemieAM:checked").val(); 
//		if(!donnees['dislipidemieAM']){ donnees['dislipidemieAM'] = 0;}
//		/*Asthme*/ 
//		donnees['asthmeAM'] = $("#asthmeAM:checked").val(); 
//		if(!donnees['asthmeAM']){ donnees['asthmeAM'] = 0;}
//		
//		/*Ajout automatique des antecedents medicaux*/
//		var $nbCheckboxAM = ($('#nbCheckboxAM').val())+1;
//		var $nbCheck = 0;
//		var $ligne;
//		var $reste = ( $nbCheckboxAM - 1 ) % 5;
//  		var $nbElement = parseInt( ( $nbCheckboxAM - 1 ) / 5 ); 
//  		if($reste != 0){ $ligne = $nbElement + 1; }
//  		else { $ligne = $nbElement; }
//  		
//  		var k=0;
//  		var i;
//		for(var j=1 ; j<=$ligne ; j++){
//			for( i=0 ; i<5 ; i++){
//				var $champValider = $('#champValider_'+j+'_'+i+':checked').val();
//				if($champValider == 'on'){
//					donnees['champValider_'+k] = 1;
//					donnees['champTitreLabel_'+k] = $('#champTitreLabel_'+j+'_'+i).val();
//					k++;
//					$nbCheck++;
//				}
//			}
//			i=0; 
//		}
//		
//		donnees['nbCheckboxAM'] = $nbCheck;
//
//		//GYNECO-OBSTETRIQUE
//		/*Menarche*/
//		donnees['MenarcheGO'] = $("#MenarcheGO:checked").val(); 
//		if(!donnees['MenarcheGO']){ donnees['MenarcheGO'] = 0;}
//		donnees['NoteMenarcheGO'] = $("#NoteMenarcheGO").val();
//		/*Gestite*/
//		donnees['GestiteGO'] = $("#GestiteGO:checked").val(); 
//		if(!donnees['GestiteGO']){ donnees['GestiteGO'] = 0;}
//		donnees['NoteGestiteGO'] = $("#NoteGestiteGO").val();
//		/*Parite*/
//		donnees['PariteGO'] = $("#PariteGO:checked").val(); 
//		if(!donnees['PariteGO']){ donnees['PariteGO'] = 0;}
//		donnees['NotePariteGO'] = $("#NotePariteGO").val();
//		/*Cycle*/
//		donnees['CycleGO'] = $("#CycleGO:checked").val(); 
//		if(!donnees['CycleGO']){ donnees['CycleGO'] = 0;}
//		donnees['DureeCycleGO'] = $("#DureeCycleGO").val();
//		donnees['RegulariteCycleGO'] = $("#RegulariteCycleGO").val(); 
//		donnees['DysmenorrheeCycleGO'] = $("#DysmenorrheeCycleGO").val();
//		/*Autres*/
//		donnees['AutresGO'] = $("#AutresGO:checked").val(); 
//		if(!donnees['AutresGO']){ donnees['AutresGO'] = 0;}
//		donnees['NoteAutresGO'] = $("#NoteAutresGO").val();
//
//		//**=== ANTECEDENTS FAMILIAUX
//		//**=== ANTECEDENTS FAMILIAUX 
//		donnees['DiabeteAF'] = $("#DiabeteAF:checked").val(); 
//		if(!donnees['DiabeteAF']){ donnees['DiabeteAF'] = 0;}
//		donnees['NoteDiabeteAF'] = $("#NoteDiabeteAF").val();
//		
//		donnees['DrepanocytoseAF'] = $("#DrepanocytoseAF:checked").val(); 
//		if(!donnees['DrepanocytoseAF']){ donnees['DrepanocytoseAF'] = 0;}
//		donnees['NoteDrepanocytoseAF'] = $("#NoteDrepanocytoseAF").val();
//		
//		donnees['htaAF'] = $("#htaAF:checked").val(); 
//		if(!donnees['htaAF']){ donnees['htaAF'] = 0;}
//		donnees['NoteHtaAF'] = $("#NoteHtaAF").val();
//		
//		donnees['autresAF'] = $("#autresAF:checked").val(); 
//		if(!donnees['autresAF']){ donnees['autresAF'] = 0;}
//		donnees['NoteAutresAF'] = $("#NoteAutresAF").val();
//		
//		updateexecuterRequetePost(donnees);
//	});
	
	
	
	//Annuler le transfert au clic
	$("#annulertransfert").click(function() {
		$("#motif_transfert").val("");
		document.getElementById('service_accueil').value="";
		return false;
	});
	
	//Annuler le rendez-vous au clic
	$("#annulerrendezvous").click(function() {
		$("#motif_rv").val("");
		$("#date_rv").val("");
		document.getElementById('heure_rv').value="";
		return false;
	});
	
	//Annuler le traitement chirurgical au clic
	$("#annuler_traitement_chirurgical").click(function() {
		$("#diagnostic_traitement_chirurgical").val("");
		$("#intervention_prevue").val("");
		$("#observation").val("");
		return false;
	});

 function jsPagination() {
	    $('#ListeConsultationPatient, #ListeHospitalisation').dataTable
		( {
						"sPaginationType": "full_numbers",
						"aaSorting": [], //pour trier la liste affichee
						"oLanguage": {
							"sZeroRecords":  "Aucun &eacute;l&eacute;ment &agrave; afficher",
							"sInfo": "_START_ &agrave; _END_ sur _TOTAL_ &eacute;l&eacute;ments",
							"sInfoEmpty": "0 &eacute;l&eacute;ment &agrave; afficher",
							"sInfoFiltered": "",
							"sInfoPostFix":  "",
							"sSearch": "",
							"sUrl": "",
							"sWidth": "30px",
							"oPaginate": {
								"sFirst":    "|<",
								"sPrevious": "<",
								"sNext":     ">",
								"sLast":     ">|"
								}
						   },
						   "iDisplayLength": 3,
							"aLengthMenu": [1,2,3],
		} );
 }
 
    /***************************************************************************************/
 
    /**========================== CONSTANTES CONSTANTES  ================================**/
 
    /***************************************************************************************/
		
    $('table input').attr('autocomplete', 'off');
	//*********************************************************************
	//*********************************************************************
	//*********************************************************************
		
	
	//******************* VALIDER LES DONNEES DU TABLEAU DES MOTIFS ******************************** 
	//******************* VALIDER LES DONNEES DU TABLEAU DES MOTIFS ******************************** 
	 	     
	/****** ======================================================================= *******/
	/****** ======================================================================= *******/
	/****** ======================================================================= *******/
	//******************* VALIDER LES DONNEES DU TABLEAU DES CONSTANTES ******************************** 
	 //******************* VALIDER LES DONNEES DU TABLEAU DES CONSTANTES ******************************** 

	   //Au debut on dï¿½sactive le code cons et la date de consultation qui sont non modifiables
//	  	var id_cons = $("#id_cons");
//	  	var date_cons = $("#date_cons");
//	  	id_cons.attr('readonly',true);
//	  	date_cons.attr('readonly',true);
//
//	  	var poids = $('#poids');
//	  	var taille = $('#taille');
//	  	var tension = $('#tension');
//	  	var bu = $('#bu');
//	  	var temperature = $('#temperature');
//	  	var glycemie_capillaire = $('#glycemie_capillaire');
//	  	var pouls = $('#pouls');
//	  	var frequence_respiratoire = $('#frequence_respiratoire');
//	  	var tensionmaximale = $("#tensionmaximale");
//	  	var tensionminimale = $("#tensionminimale");
//	  	
//		  //Au debut on cache le bouton modifier et on affiche le bouton valider
//	  	$( "#bouton_constantes_valider" ).toggle(true);
//	  	$( "#bouton_constantes_modifier" ).toggle(false);
//
//	  	//Au debut on active tous les champs
//	  	poids.attr( 'readonly', false );
//	  	taille.attr( 'readonly', false );
//	  	tension.attr( 'readonly', false); 
//	  	bu.attr( 'readonly', false);  
//	  	temperature.attr( 'readonly', false); 
//	  	glycemie_capillaire.attr( 'readonly', false);
//	  	pouls.attr( 'readonly', false);
//	  	frequence_respiratoire.attr( 'readonly', false);
//	  	tensionmaximale.attr( 'readonly', false );
//	  	tensionminimale.attr( 'readonly', false );
//
//	  	$( "#bouton_constantes_valider" ).click(function(){
//	  		if(valid == true){
//		   		poids.attr( 'readonly', true );    
//		   		taille.attr( 'readonly', true );
//		   		tension.attr( 'readonly', true);
//		   		bu.attr( 'readonly', true);
//		   		temperature.attr( 'readonly', true);
//		   		glycemie_capillaire.attr( 'readonly', true);
//		   		pouls.attr( 'readonly', true);
//		   		frequence_respiratoire.attr( 'readonly', true);
//		   		tensionmaximale.attr( 'readonly', true );
//		   		tensionminimale.attr( 'readonly', true );
//		   		
//	  		    $("#bouton_constantes_modifier").toggle(true);  //on affiche le bouton permettant de modifier les champs
//	  		    $("#bouton_constantes_valider").toggle(false); //on cache le bouton permettant de valider les champs
//	  		}
//	  		return false; 
//	  	});
//	  	
//	  	$( "#bouton_constantes_modifier" ).click(function(){
//	  		poids.attr( 'readonly', false );
//	  		taille.attr( 'readonly', false ); 
//	  		tension.attr( 'readonly', false); 
//	  		bu.attr( 'readonly', false);
//	  		temperature.attr( 'readonly', false);
//	  		glycemie_capillaire.attr( 'readonly', false);
//	  		pouls.attr( 'readonly', false);
//	  		frequence_respiratoire.attr( 'readonly', false);
//	  		tensionmaximale.attr( 'readonly', false );
//	  		tensionminimale.attr( 'readonly', false );
//	  		
//	  	 	$("#bouton_constantes_modifier").toggle(false);   //on cache le bouton permettant de modifier les champs
//	  	 	$("#bouton_constantes_valider").toggle(true);    //on affiche le bouton permettant de valider les champs
//	  	 	return  false;
//	  	});

	  	$('#dateDebAlcoolique input, #dateFinAlcoolique input, #dateDebFumeur input, #dateFinFumeur input, #dateDebDroguer input, #dateFinDroguer input').datepicker(
				$.datepicker.regional['fr'] = {
						closeText: 'Fermer',
						changeYear: true,
						yearRange: 'c-80:c',
						prevText: '&#x3c;PrÃ©c',
						nextText: 'Suiv&#x3e;',
						currentText: 'Courant',
						monthNames: ['Janvier','Fevrier','Mars','Avril','Mai','Juin',
						'Juillet','Aout','Septembre','Octobre','Novembre','Decembre'],
						monthNamesShort: ['Jan','Fev','Mar','Avr','Mai','Jun',
						'Jul','Aout','Sep','Oct','Nov','Dec'],
						dayNames: ['Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'],
						dayNamesShort: ['Dim','Lun','Mar','Mer','Jeu','Ven','Sam'],
						dayNamesMin: ['Di','Lu','Ma','Me','Je','Ve','Sa'],
						weekHeader: 'Sm',
						dateFormat: 'dd/mm/yy',
						firstDay: 1,
						isRTL: false,
						showMonthAfterYear: false,
						yearRange: '1990:2025',
						showAnim : 'bounce',
						changeMonth: true,
						changeYear: true,
						yearSuffix: ''}
		);
	  	
	  	
	  	$('#date_fin_hospitalisation_prevue').datepicker(
				$.datepicker.regional['fr'] = {
						closeText: 'Fermer',
						changeYear: true,
						yearRange: 'c-80:c',
						prevText: '&#x3c;PrÃ©c',
						nextText: 'Suiv&#x3e;',
						currentText: 'Courant',
						monthNames: ['Janvier','Fevrier','Mars','Avril','Mai','Juin',
						'Juillet','Aout','Septembre','Octobre','Novembre','Decembre'],
						monthNamesShort: ['Jan','Fev','Mar','Avr','Mai','Jun',
						'Jul','Aout','Sep','Oct','Nov','Dec'],
						dayNames: ['Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'],
						dayNamesShort: ['Dim','Lun','Mar','Mer','Jeu','Ven','Sam'],
						dayNamesMin: ['Di','Lu','Ma','Me','Je','Ve','Sa'],
						weekHeader: 'Sm',
						dateFormat: 'dd/mm/yy',
						firstDay: 1,
						isRTL: false,
						minDate: 1,
						showMonthAfterYear: false,
						yearRange: '1990:2025',
						showAnim : 'bounce',
						changeMonth: true,
						changeYear: true,
						yearSuffix: '',
				}
		);
	  	
	  	//===================================================================================================================
	  	//===================================================================================================================
	  	//===================================================================================================================
	  	//===================================================================================================================
	  	//===================================================================================================================
	  	//===================================================================================================================
	  	//===================================================================================================================
	  	//===================================================================================================================
	  	//===================================================================================================================
	  	//===================================================================================================================
	  	//===================================================================================================================
	  	//===================================================================================================================
	  	//===================================================================================================================
	  	//===================================================================================================================
	  	//===================================================================================================================
	  	//===================================================================================================================
	  	//===================================================================================================================
	  	//===================================================================================================================
	  	var itab = 1;
	  	var ligne = 0; 
	  	var tableau = [];
	  	
	  	function ajouterToutLabelAntecedentsMedicaux(tableau_){
	  		for(var l = 1; l <= ligne; l++){
	  			if( l == 1 ){
		  			$("#labelDesAntecedentsMedicaux_"+1).html("").css({'height' : '0px'});
		  			itab = 1;
	  			} else {
		  			$("#labelDesAntecedentsMedicaux_"+l).remove();
	  			}
	  		}
	  		
	  		var tab = [];
	  		var j = 1;
	  		
	  		for(var i=1 ; i<tableau_.length ; i++){
	  			if( tableau_[i] ){
	  				tab[j++] = tableau_[i];
	  				itab++;
	  				ajouterLabelAntecedentsMedicaux(tableau_[i]);
	  			}
	  		}

	  		tableau = tab;
	  		itab = j;
	  		$('#nbCheckboxAM').val(itab);

	  		stopPropagation();
	  	}
	  	
	  	
	  	//Ajouter des labels au click sur ajouter
	  	//Ajouter des labels au click sur ajouter
	  	//Ajouter des labels au click sur ajouter
	  	var scriptLabel = "";
	  	function ajouterLabelAntecedentsMedicaux(nomLabel){
	  		
	  		if(!nomLabel){ stopPropagation(); }
	  		
	  		var reste = ( itab - 1 ) % 5; 
	  		var nbElement = parseInt( ( itab - 1 ) / 5 ); 
	  		if(reste != 0){ ligne = nbElement + 1; }
	  		else { ligne = nbElement; }
	  		
	  		var i = 0;
	  		if(ligne == 1){
		  		i = $("#labelDesAntecedentsMedicaux_"+ligne+" td").length;
	  		} else {
	  			if(reste == 1){
		  			$("#labelDesAntecedentsMedicaux_"+(ligne-1)).after(
	            			"<tr id='labelDesAntecedentsMedicaux_"+ligne+"' style='width:100%; '>"+
	            			"</tr>");
	  			}
	  			i = $("#labelDesAntecedentsMedicaux_"+ligne+" td").length;
	  		}
	  		
	  		scriptLabel = 
  				"<td id='BUcheckbox' class='label_"+ligne+"_"+i+"' style='width: 20%; '> "+
                "<div > "+
                " <label style='width: 90%; height:30px; text-align:right; font-family: time new romans; font-size: 18px;'> "+
                "    <span style='padding-left: -10px;'> "+
                "       <a href='javascript:supprimerLabelAM("+ligne+","+i+");' ><img class='imageSupprimerAsthmeAM' style='cursor: pointer; float: right; margin-right: -10px; width:10px; height: 10px;' src='"+tabUrl[0]+"public/images_icons/sup.png' /></a> "+ 
                "       <img class='imageValider_"+ligne+"_"+i+"'  style='cursor: pointer; margin-left: -15px;' src='"+tabUrl[0]+"public/images_icons/tick-icon2.png' /> "+  
                "    </span> "+
                nomLabel +"  <input type='checkbox' checked='${this.checked}' name='champValider_"+ligne+"_"+i+"' id='champValider_"+ligne+"_"+i+"' > "+
                " <input type='hidden'  id='champTitreLabel_"+ligne+"_"+i+"' value='"+nomLabel+"' > "+
                " </label> "+
                "</div> "+
                "</td> "+
                
                "<script>"+
                "$('#champValider_"+ligne+"_"+i+"').click(function(){"+
	  			"var boutons = $('#champValider_"+ligne+"_"+i+"');"+
	  			"if( boutons[0].checked){ $('.imageValider_"+ligne+"_"+i+"').toggle(true);  }"+
	  			"if(!boutons[0].checked){ $('.imageValider_"+ligne+"_"+i+"').toggle(false); }"+
	  		    "});"+
	  		    "</script>"
                ;
	  		
	  		if( i == 0 ){
	  			//AJOUTER ELEMENT SUIVANT
	            $("#labelDesAntecedentsMedicaux_"+ligne).html(scriptLabel);
	            $("#labelDesAntecedentsMedicaux_"+ligne).css({'height' : '50px'});
	  	    } else if( i < 5 ){
	  	    	//AJOUTER ELEMENT SUIVANT
	            $("#labelDesAntecedentsMedicaux_"+ligne+" .label_"+ligne+"_"+(i-1)).after(scriptLabel);
	  	    }
	  		
	  	}

	  	//Ajouter un label --- Ajouter un label
	  	//Ajouter un label --- Ajouter un label
	  	//Ajouter un label --- Ajouter un label

	  	$('#imgIconeAjouterLabel').click(function(){
	  		if(!$('#autresAM').val()){ stopPropagation(); }
	  		else{
	  			tableau[itab++] = $('#autresAM').val();
	  			ajouterLabelAntecedentsMedicaux($('#autresAM').val());
	  			$('#nbCheckboxAM').val(itab);
	  			$('#autresAM').val("");
	  		}
	  		stopPropagation();
	  	});
	  	
	  	
	  	//Supprimer un label ajouter --- Supprimer un label ajouter
	  	//Supprimer un label ajouter --- Supprimer un label ajouter
	  	//Supprimer un label ajouter --- Supprimer un label ajouter
	  	function supprimerLabelAM(ligne, i){
	  		
	  		var pos = ((ligne - 1)*5)+i;
	  		var indiceTableau = pos+1; 
	  		tableau[indiceTableau] = "";
	  		
	  		$("#labelDesAntecedentsMedicaux_"+ligne+" .label_"+ligne+"_"+i).fadeOut(
	  			function(){	ajouterToutLabelAntecedentsMedicaux(tableau); }
	  		);
		  	
	  	}
        
	  	//Ajout de l'auto-completion sur le champ autre
	    //Ajout de l'auto-completion sur le champ autre
	  	
	  	function autocompletionAntecedent(myArrayMedicament){
		  	$( "#imageIconeAjouterLabel label input" ).autocomplete({
			  	  source: myArrayMedicament
			    });
	  	}
	  	
	  	
	  	function affichageAntecedentsMedicauxDuPatient(nbElement, tableau_){
	  		for(var i=1 ; i<=nbElement ; i++){
	  			itab++;
	  			ajouterLabelAntecedentsMedicaux(tableau_[i]);
	  		}
	  		tableau = tableau_;
	  	}
	  	
	    //===================================================================================================================
	  	//===================================================================================================================
	  	//===================================================================================================================
	  	//===================================================================================================================
	  	//===================================================================================================================
	  	//===================================================================================================================
	  	//===================================================================================================================
	  	//===================================================================================================================
	  	
	  	