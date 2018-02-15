
function majModifierConsultation(){
	$('.image2_TP').click(function(){
		//Consanguinité - Fratrie --- Consanguinité - Fratrie --- Consanguinité - Fratrie
		$('#consanguiniteAF, #fratrieRangAF').trigger('change');
		
		
		//Autres maladies familiales --- Autres maladies familiales --- Autres maladies familiales
		var boutons = $('#autresMaladiesFamiliales input[name="AllergiesAF"]');
		if( boutons[1].checked){ $("#libelleAllergiesAF").html('&#10003; Allergie').css({'color':'red', 'font-weight':'bold'}); }
	
		var boutons = $('#autresMaladiesFamiliales input[name="AsthmeAF"]');
		if( boutons[1].checked){ $("#libelleAsthmeAF").html('&#10003; Asthme').css({'color':'red', 'font-weight':'bold'}); }
		 
		var boutons = $('#autresMaladiesFamiliales input[name="DiabeteAF"]');
		if( boutons[1].checked){ $("#libelleDiabeteAF").html('&#10003; Diabete').css({'color':'red', 'font-weight':'bold'}); }
	
		var boutons = $('#autresMaladiesFamiliales input[name="HtaAF"]');
		if( boutons[1].checked){ $("#libelleHtaAF").html('&#10003; Hta').css({'color':'red', 'font-weight':'bold'}); }
	});
	
	//Histoire de la maladie --- Histoire de la maladie
	$('#criseHM, #episodeFievreHM, #hospitalisationHM, #priseEnChargeHospitalisationHM').trigger('change');
	
	//Suivi des traitements --- Mise à jour des vaccins 
	$('#suiviDesTraitements, #misesAJourDesVaccins').trigger('change');
	
	//Donnees de l'examen --- Examen des poumons --- Examen du coeur 
	$('#splenomegalieDonneesExamen, #examenDesPoumonsDonneesExamen, #examenDuCoeurDonneesExamen').trigger('change');
	
	//ORL Donnees examen
	var boutons = $('#donneesExamenOrlCheckbox input[name="orlObstructionNasaleDonneesExamen"]');
	if( boutons[1].checked){ $("#obstructionNasaleDE").html('<span style="color: green;">&#10003;</span> Obstruction nasale').css({'color':'black', 'font-weight':'bold'}); }

	var boutons = $('#donneesExamenOrlCheckbox input[name=orlRhiniteDonneesExamen]');
	if( boutons[1].checked){ $("#rhiniteDE").html('<span style="color: green;">&#10003;</span> Rhinite').css({'color':'black', 'font-weight':'bold'}); }
	
	var boutons = $('#donneesExamenOrlCheckbox input[name=orlHypertrophieAmygdalesDonneesExamen]');
	if( boutons[1].checked){ $("#hypertrophieAmygdalesDE").html('<span style="color: green;">&#10003;</span> Hypertrophie des amygdales').css({'color':'black', 'font-weight':'bold'}); }
	
	var boutons = $('#donneesExamenOrlCheckbox input[name=orlAngineDonneesExamen]');
	if( boutons[1].checked){ $("#angineDE").html('<span style="color: green;">&#10003;</span> Angine').css({'color':'black', 'font-weight':'bold'}); }
	
	var boutons = $('#donneesExamenOrlCheckbox input[name=orlOtiteDonneesExamen]');
	if( boutons[1].checked){ $("#otiteDE").html('<span style="color: green;">&#10003;</span> Otite').css({'color':'black', 'font-weight':'bold'}); }
	
	
	//Diagnostic -- Complication aigue -- Complication aigue
	setTimeout(function(){
		for(var i=0 ; i<listeInfosComplicationsAigues.length ; i++){
			if(i>0){ $("#ajouterComplicationAigueBouton").trigger('click'); }
			$("#diagnosticComplicationsAiguesChamp_"+(i+1)+" select").val(listeInfosComplicationsAigues[i]);
		}
	});
	
	//Diagnostic -- Complication chronique -- Complication chronique
	setTimeout(function(){
		for(var i=0 ; i<listeInfosComplicationsChroniques.length ; i++){
			if(i>0){ $("#ajouterComplicationChroniqueBouton").trigger('click'); }
			$("#diagnosticComplicationsChroniquesChamp_"+(i+1)+" select").val(listeInfosComplicationsChroniques[i]);
		}
	});
	
}