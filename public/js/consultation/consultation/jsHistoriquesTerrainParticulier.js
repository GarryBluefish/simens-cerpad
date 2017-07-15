
//A l'entrée
getInfoCrise(0);
getInfoAutresEvenements(0);
//gestionChoixMotifRendezVous();

function getInfoCrise(id){

	if(id == 1){
		$(".criseInfo div").fadeIn();
	}else if(id == 0 || id == ''){
		$(".criseInfo div").fadeOut(false);
	}
	
}

function getInfoAutresEvenements(id){

	if(id == 1 || id == 2){
		$(".autresInfo div").fadeIn();
	}else{
		$(".autresInfo div").fadeOut(false);
	}
}


//function gestionChoixMotifRendezVous(){ //alert();
//	$('#rendervousLabelVS').click(function(){ 
//		//var boutons = $('#rendezvousPreciserVSS input[name=rendezvousPreciserVSS]');
//		
//		alert('boutons'); return false;
//		
//		//if( boutons[1].checked){ $("#DivNoteDiabeteAF").toggle(true); }
//		//if(!boutons[1].checked){ $("#DivNoteDiabeteAF").toggle(false); }
//	});
//}

function scriptHistoriqueTerrainParticulier(){
	
	$( "#tabsAntecedents" ).tabs();
	
	//CONSULTATION
	//CONSULTATION
	$("#titreTableauConsultation").toggle(false);
	$("#ListeConsultationPatient").toggle(false);
	$("#ListeCons").toggle(false);
	$("#boutonTerminerConsultation").toggle(false);
	$(".pager").toggle(false);
	
	//HOSPITALISATION
	//HOSPITALISATION
	$("#titreTableauHospitalisation").toggle(false);
	$("#boutonTerminerHospitalisation").toggle(false);
	$("#ListeHospitalisation").toggle(false);
	$("#ListeHospi").toggle(false);
	
	
	//CONSULTATION
	//CONSULTATION
	$(".image1").click(function(){
		
		 $("#MenuAntecedent").fadeOut(function(){ 
			 $("#titreTableauConsultation").fadeIn("fast");
			 $("#ListeConsultationPatient").fadeIn("fast"); 
			 $("#ListeCons").fadeIn("fast");
		     $("#boutonTerminerConsultation").toggle(true);
		     $(".pager").toggle(true);
		 });
	});
	
	$("#TerminerConsultation").click(function(){
		$("#boutonTerminerConsultation").fadeOut();
		$(".pager").fadeOut();
		$("#titreTableauConsultation").fadeOut();
		$("#ListeCons").fadeOut();
		$("#ListeConsultationPatient").fadeOut(function(){ 
		    $("#MenuAntecedent").fadeIn("fast");
		});
	});
	
	//HOSPITALISATION
	//HOSPITALISATION
	$(".image2").click(function(){
		 $("#MenuAntecedent").fadeOut(function(){ 
			 $("#titreTableauHospitalisation").fadeIn("fast");
		     $("#boutonTerminerHospitalisation").toggle(true);
		     $("#ListeHospitalisation").fadeIn("fast");
		     $("#ListeHospi").fadeIn("fast");
		 });
	});
	
	$("#TerminerHospitalisation").click(function(){
		$("#boutonTerminerHospitalisation").fadeOut();
		$("#ListeHospitalisation").fadeOut();
		$("#ListeHospi").fadeOut();
		$("#titreTableauHospitalisation").fadeOut(function(){ 
		    $("#MenuAntecedent").fadeIn("fast");
		});
	});
	
	
	//ANALYSES
	//ANALYSES
	$("#titreTableauAnalyses").toggle(false);
	$("#boutonTerminerAnalyses").toggle(false);
	$("#TabAnalyses").toggle(false);
	
	$(".image3").click(function(){ 
		
		 $("#MenuAntecedent").fadeOut(function(){ 
			 $("#TabAnalyses").fadeIn("fast");
		     $("#boutonTerminerAnalyses").toggle(true);
		     $("#titreTableauAnalyses").toggle(true);
		 });
	});
	
	$("#boutonTerminerAnalyses").click(function(){
		$("#boutonTerminerAnalyses").fadeOut();
		$("#titreTableauAnalyses").fadeOut(false);
		$("#TabAnalyses").fadeOut(function(){ 
		    $("#MenuAntecedent").fadeIn("fast");
		});
	});
	
	
	
	//HISTOIRE DE LA MALADIE
	//HISTOIRE DE LA MALADIE
	$("#titreTableauHistoireMaladie").toggle(false);
	$("#boutonTerminerHistoireMaladie").toggle(false);
	$("#TabHistoireMaladie").toggle(false);
	
	$(".image4").click(function(){
		
		 $("#MenuAntecedent").fadeOut(function(){ 
			 $("#TabHistoireMaladie").fadeIn("fast");
		     $("#boutonTerminerHistoireMaladie").toggle(true);
		     $("#titreTableauHistoireMaladie").toggle(true);
		 });
	});
	
	$("#boutonTerminerHistoireMaladie").click(function(){
		$("#boutonTerminerHistoireMaladie").fadeOut();
		$("#titreTableauHistoireMaladie").fadeOut(false);
		$("#TabHistoireMaladie").fadeOut(function(){ 
		    $("#MenuAntecedent").fadeIn("fast");
		});
	});
	
	
 /*************************************************************************************************************/
 
 /*=================================== MENU ANTECEDENTS TERRAIN PARTICULIER ==================================*/
 
 /*************************************************************************************************************/
	 
	    //ANTECEDENTS PERSONNELS
		//ANTECEDENTS PERSONNELS
		$("#antecedentsPersonnels").toggle(false);
		$("#AntecedentsFamiliaux").toggle(false);
		$("#MenuAntecedentPersonnel").toggle(false);
		$("#AntecedentObstetrique").toggle(false);
		$("#AntecedentPerineonatale").toggle(false);
		$("#AntecedentNutritionnel").toggle(false);
		$("#GynecoObstetrique").toggle(false);
		
//*****************************************************************
//*****************************************************************
		//ANTECEDENTS PERSONNELS
		//ANTECEDENTS PERSONNELS
		$(".image1_TP").click(function(){
			 $("#MenuTerrainParticulier").fadeOut(function(){ 
				 $("#MenuAntecedentPersonnel").fadeIn("fast");
			 });
		});
		
		$(".image_fleche").click(function(){
			 $("#MenuAntecedentPersonnel").fadeOut(function(){ 
				 $("#MenuTerrainParticulier").fadeIn("fast");
			 });
		});
		
		//ANTECEDENT OBSTETRIQUE
		//ANTECEDENT OBSTETRIQUE
		$(".image1_AP").click(function(){
			 $("#MenuAntecedentPersonnel").fadeOut(function(){ 
				 $("#AntecedentObstetrique").fadeIn("fast");
			 });
		});
		
		$("#TerminerHabitudeDeVie").click(function(){
			$("#AntecedentObstetrique").fadeOut(function(){ 
				 $("#MenuAntecedentPersonnel").fadeIn("fast");
			 });
		});
		
		//ANTECEDENTS MEDICAUX
		//ANTECEDENTS MEDICAUX
		$(".image2_AP").click(function(){
			 $("#MenuAntecedentPersonnel").fadeOut(function(){ 
				 $("#AntecedentPerineonatale").fadeIn("fast");
			 });
		});
		
		$("#TerminerAntecedentPerineonatale").click(function(){
			$("#AntecedentPerineonatale").fadeOut(function(){ 
				 $("#MenuAntecedentPersonnel").fadeIn("fast");
			 });
		});
		
		//ANTECEDENTS CHIRURGICAUX
		//ANTECEDENTS CHIRURGICAUX
		$(".image3_AP").click(function(){
			 $("#MenuAntecedentPersonnel").fadeOut(function(){ 
				 $("#AntecedentNutritionnel").fadeIn("fast");
			 });
		});
		
		$("#TerminerAntecedentNutritionnel").click(function(){
			$("#AntecedentNutritionnel").fadeOut(function(){ 
				 $("#MenuAntecedentPersonnel").fadeIn("fast");
			 });
		});
		
		//ANTECEDENTS CHIRURGICAUX
		//ANTECEDENTS CHIRURGICAUX
		$(".image4_AP").click(function(){
			 $("#MenuAntecedentPersonnel").fadeOut(function(){ 
				 $("#GynecoObstetrique").fadeIn("fast");
			 });
		});
		
		$("#TerminerGynecoObstetrique").click(function(){
			$("#GynecoObstetrique").fadeOut(function(){ 
				 $("#MenuAntecedentPersonnel").fadeIn("fast");
			 });
		});
		
		//******************************************************************************
		//******************************************************************************
			$(".image2_TP").click(function(){ 
				$("#MenuTerrainParticulier").fadeOut(function(){ 
					 $("#AntecedentsFamiliaux").fadeIn("fast");
				 });
			}); 
			
			$("#TerminerAntecedentsFamiliaux").click(function(){
				$("#AntecedentsFamiliaux").fadeOut(function(){ 
					 $("#MenuTerrainParticulier").fadeIn("fast");
				 });
			}); 
		
		
		scriptAuClick();

}





function scriptAuClick(){

	//ANTECEDENT OBSTETRIQUE TESTER SI UNE HABITUDE EST COCHEE OU PAS
	//ANTECEDENT OBSTETRIQUE TESTER SI UNE HABITUDE EST COCHEE OU PAS
	//$("#AntecedentObstetrique input[name=testHV]").attr('checked', true);
	
//	if(temoinAlcoolique != 1){
//		$("#dateDebAlcoolique, #dateFinAlcoolique").toggle(false);
//	}
//	if(temoinFumeurHV != 1){
//		$("#dateDebFumeur, #dateFinFumeur, #nbPaquetJour, #nbPaquetAnnee").toggle(false);
//		$('#nbPaquetFumeurHV').val("");
//		$('#nbPaquetAnnee').toggle(false);
//	}else{
//		if(nbPaquetFumeurHV != 0 ){
//			var nbPaquetAnnee = nbPaquetFumeurHV*365;
//			$("#nbPaquetAnnee label").html("<span style='font-weight: bold; color: green;'>"+nbPaquetAnnee+"</span> paquets/an");
//		}else{
//			$('#nbPaquetFumeurHV').val("");
//			$('#nbPaquetAnnee').toggle(false);
//		}
//	}
//	if(temoinDroguerHV != 1){
//		$("#dateDebDroguer, #dateFinDroguer").toggle(false);
//	}
//	
//	$("#DivNoteAutresHV").toggle(false);
//	
//	if($('#DateDebutAlcooliqueHV').val() == '00/00/0000'){ $('#DateDebutAlcooliqueHV').val("");}
//	if($('#DateFinAlcooliqueHV').val() == '00/00/0000'){ $('#DateFinAlcooliqueHV').val("");}
//	$('#AntecedentObstetrique input[name=AlcooliqueHV]').click(function(){ 
//		var boutons = $('#AntecedentObstetrique input[name=AlcooliqueHV]');
//		if( boutons[1].checked){ $("#dateDebAlcoolique, #dateFinAlcoolique").toggle(true); }
//		if(!boutons[1].checked){ $("#dateDebAlcoolique, #dateFinAlcoolique").toggle(false); }
//	});
//	
//	if($('#DateDebutFumeurHV').val() == '00/00/0000'){ $('#DateDebutFumeurHV').val(""); }
//	if($('#DateFinFumeurHV').val() == '00/00/0000'){ $('#DateFinFumeurHV').val(""); }
//	$('#AntecedentObstetrique input[name=FumeurHV]').click(function(){
//		var boutons = $('#AntecedentObstetrique input[name=FumeurHV]');
//		if( boutons[1].checked){ $("#dateDebFumeur, #dateFinFumeur, #nbPaquetJour, #nbPaquetAnnee").toggle(true); }
//		if(!boutons[1].checked){ $("#dateDebFumeur, #dateFinFumeur, #nbPaquetJour, #nbPaquetAnnee").toggle(false); }
//		if($('#nbPaquetFumeurHV').val() == ""){ $('#nbPaquetAnnee').toggle(false);} 
//	});
//	
//	$('#nbPaquetFumeurHV').keyup(function(){
//		var valeur = $('#nbPaquetFumeurHV').val();
//		if(isNaN(valeur/1) || valeur > 10){
//			$('#nbPaquetFumeurHV').val("");
//			valeur = null;
//		}
//		if(valeur){
//			var nbPaquetAnnee = valeur*365;
//			$("#nbPaquetAnnee").toggle(true);
//			$("#nbPaquetAnnee label").html("<span style='font-weight: bold; color: green;'>"+nbPaquetAnnee+"</span> paquets/an");
//		}else{
//			$("#nbPaquetAnnee").toggle(false);
//		}
//	}); 
//	
//	if($('#DateDebutDroguerHV').val() == '00/00/0000'){ $('#DateDebutDroguerHV').val("");}
//	if($('#DateFinDroguerHV').val() == '00/00/0000'){ $('#DateFinDroguerHV').val("");}
//	$('#AntecedentObstetrique input[name=DroguerHV]').click(function(){
//		var boutons = $('#AntecedentObstetrique input[name=DroguerHV]');
//		if( boutons[1].checked){ $("#dateDebDroguer, #dateFinDroguer").toggle(true); }
//		if(!boutons[1].checked){ $("#dateDebDroguer, #dateFinDroguer").toggle(false); }
//	});
//	
//	$('#AntecedentObstetrique input[name=AutresHV]').click(function(){
//		var boutons = $('#AntecedentObstetrique input[name=AutresHV]');
//		if( boutons[1].checked){ $("#DivNoteAutresHV").toggle(true); }
//		if(!boutons[1].checked){ $("#DivNoteAutresHV").toggle(false); }
//	});
//	
//	//ANTECEDENTS MEDICAUX TESTER SI C'EST COCHE
//	//ANTECEDENTS MEDICAUX TESTER SI C'EST COCHE
//	if(temoinDiabeteAM != 1){
//		$(".imageValiderDiabeteAM").toggle(false);
//	}
//	if(temoinhtaAM != 1){
//		$(".imageValiderHtaAM").toggle(false);
//	}
//	if(temoindrepanocytoseAM != 1){
//		$(".imageValiderDrepanocytoseAM").toggle(false);
//	}
//	if(temoindislipidemieAM != 1){
//		$(".imageValiderDislipidemieAM").toggle(false);
//	}
//	if(temoinasthmeAM != 1){
//		$(".imageValiderAsthmeAM").toggle(false);
//	}
//	
//	$('#AntecedentPerineonatale input[name=DiabeteAM]').click(function(){
//		var boutons = $('#AntecedentPerineonatale input[name=DiabeteAM]');
//		if( boutons[1].checked){ $(".imageValiderDiabeteAM").toggle(true); }
//		if(!boutons[1].checked){ $(".imageValiderDiabeteAM").toggle(false); }
//	});
//	
//	$('#AntecedentPerineonatale input[name=htaAM]').click(function(){
//		var boutons = $('#AntecedentPerineonatale input[name=htaAM]');
//		if( boutons[1].checked){ $(".imageValiderHtaAM").toggle(true); }
//		if(!boutons[1].checked){ $(".imageValiderHtaAM").toggle(false); }
//	});
//	
//	$('#AntecedentPerineonatale input[name=drepanocytoseAM]').click(function(){
//		var boutons = $('#AntecedentPerineonatale input[name=drepanocytoseAM]');
//		if( boutons[1].checked){ $(".imageValiderDrepanocytoseAM").toggle(true); }
//		if(!boutons[1].checked){ $(".imageValiderDrepanocytoseAM").toggle(false); }
//	});
//	
//	$('#AntecedentPerineonatale input[name=dislipidemieAM]').click(function(){
//		var boutons = $('#AntecedentPerineonatale input[name=dislipidemieAM]');
//		if( boutons[1].checked){ $(".imageValiderDislipidemieAM").toggle(true); }
//		if(!boutons[1].checked){ $(".imageValiderDislipidemieAM").toggle(false); }
//	});
//	
//	$('#AntecedentPerineonatale input[name=asthmeAM]').click(function(){
//		var boutons = $('#AntecedentPerineonatale input[name=asthmeAM]');
//		if( boutons[1].checked){ $(".imageValiderAsthmeAM").toggle(true); }
//		if(!boutons[1].checked){ $(".imageValiderAsthmeAM").toggle(false); }
//	});
//	
//	//GYNECO-OBSTETRIQUE TESTER SI C'EST COCHE
//	//GYNECO-OBSTETRIQUE TESTER SI C'EST COCHE
//	if(temoinMenarcheGO != 1){
//		$("#NoteMonarche").toggle(false);
//	}
//	if(temoinGestiteGO != 1){
//		$("#NoteGestite").toggle(false);
//	}
//	if(temoinPariteGO != 1){
//		$("#NoteParite").toggle(false);
//	}
//	if(temoinCycleGO != 1){
//		$("#RegulariteON, #DysmenorrheeON, #DureeGO").toggle(false);
//	}
//	$("#DivNoteAutresGO").toggle(false);
//	
//	$('#GynecoObstetrique input[name=MenarcheGO]').click(function(){
//		var boutons = $('#GynecoObstetrique input[name=MenarcheGO]');
//		if( boutons[1].checked){ $("#NoteMonarche").toggle(true); }
//		if(!boutons[1].checked){ $("#NoteMonarche").toggle(false); }
//	});
//	
//	$('#GynecoObstetrique input[name=GestiteGO]').click(function(){
//		var boutons = $('#GynecoObstetrique input[name=GestiteGO]');
//		if( boutons[1].checked){ $("#NoteGestite").toggle(true); }
//		if(!boutons[1].checked){ $("#NoteGestite").toggle(false); }
//	});
//	
//	$('#GynecoObstetrique input[name=PariteGO]').click(function(){
//		var boutons = $('#GynecoObstetrique input[name=PariteGO]');
//		if( boutons[1].checked){ $("#NoteParite").toggle(true); }
//		if(!boutons[1].checked){ $("#NoteParite").toggle(false); }
//	});
//	
//	$('#GynecoObstetrique input[name=CycleGO]').click(function(){
//		var boutons = $('#GynecoObstetrique input[name=CycleGO]');
//		if( boutons[1].checked){ $("#RegulariteON, #DysmenorrheeON, #DureeGO").toggle(true); }
//		if(!boutons[1].checked){ $("#RegulariteON, #DysmenorrheeON, #DureeGO").toggle(false); }
//	});
//	
//	$('#GynecoObstetrique input[name=AutresGO]').click(function(){
//		var boutons = $('#GynecoObstetrique input[name=AutresGO]');
//		if( boutons[1].checked){ $("#DivNoteAutresGO").toggle(true); }
//		if(!boutons[1].checked){ $("#DivNoteAutresGO").toggle(false); }
//	});
	
	//ANTECEDENTS FAMILIAUX TESTER SI C'EST COCHE
	//ANTECEDENTS FAMILIAUX TESTER SI C'EST COCHE
	
	if(temoinDiabeteAF != 1){ 
		$("#DivNoteDiabeteAF").toggle(false);
	}
	if(temoinDrepanocytoseAF != 1){
		$("#DivNoteDrepanocytoseAF").toggle(false);
	}
	if(temoinhtaAF != 1){
		$("#DivNoteHtaAF").toggle(false);
	}
	$("#DivNoteAutresAF").toggle(false);
	
	$('#AntecedentsFamiliaux input[name=DiabeteAF]').click(function(){ 
		var boutons = $('#AntecedentsFamiliaux input[name=DiabeteAF]');
		if( boutons[1].checked){ $("#DivNoteDiabeteAF").toggle(true); }
		if(!boutons[1].checked){ $("#DivNoteDiabeteAF").toggle(false); }
	});
	
	$('#AntecedentsFamiliaux input[name=DrepanocytoseAF]').click(function(){ 
		var boutons = $('#AntecedentsFamiliaux input[name=DrepanocytoseAF]');
		if( boutons[1].checked){ $("#DivNoteDrepanocytoseAF").toggle(true); }
		if(!boutons[1].checked){ $("#DivNoteDrepanocytoseAF").toggle(false); }
	});
	
	$('#AntecedentsFamiliaux input[name=htaAF]').click(function(){ 
		var boutons = $('#AntecedentsFamiliaux input[name=htaAF]');
		if( boutons[1].checked){ $("#DivNoteHtaAF").toggle(true); }
		if(!boutons[1].checked){ $("#DivNoteHtaAF").toggle(false); }
	});
	
	$('#AntecedentsFamiliaux input[name=autresAF]').click(function(){ 
		var boutons = $('#AntecedentsFamiliaux input[name=autresAF]');
		if( boutons[1].checked){ $("#DivNoteAutresAF").toggle(true); }
		if(!boutons[1].checked){ $("#DivNoteAutresAF").toggle(false); }
	});
	
}