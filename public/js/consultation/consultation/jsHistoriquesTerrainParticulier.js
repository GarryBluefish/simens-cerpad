
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
	
	setTimeout(function(){ 
		$(".TerminerConsultation" ).html("<button id='TerminerConsultation' style='height:35px;'>Terminer</button>"); 

		$("#TerminerConsultation").click(function(){
			$("#boutonTerminerConsultation").fadeOut();
			$(".pager").fadeOut();
			$("#titreTableauConsultation").fadeOut();
			$("#ListeCons").fadeOut();
			$("#ListeConsultationPatient").fadeOut(function(){ 
				$("#MenuAntecedent").fadeIn("fast");
			});
		
			return false;
		});
	
	},5000);
	
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
	
	setTimeout(function(){ 
		$(".TerminerHospitalisation" ).html("<button id='TerminerHospitalisation' style='height:35px;'>Terminer</button>"); 
	
		$("#TerminerHospitalisation").click(function(){
			$("#boutonTerminerHospitalisation").fadeOut();
			$("#ListeHospitalisation").fadeOut();
			$("#ListeHospi").fadeOut();
			$("#titreTableauHospitalisation").fadeOut(function(){ 
				$("#MenuAntecedent").fadeIn("fast");
			});
		
			return false;
		});

	},5000);
	
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
	
	setTimeout(function(){ 
		$(".TerminerAnalyses" ).html("<button id='TerminerAnalyses' style='height:35px;'>Terminer</button>"); 
	
		$("#TerminerAnalyses").click(function(){
			$("#boutonTerminerAnalyses").fadeOut();
			$("#titreTableauAnalyses").fadeOut(false);
			$("#TabAnalyses").fadeOut(function(){ 
				$("#MenuAntecedent").fadeIn("fast");
			});
			
			return false;
		});
	},5000);
	
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
	
	setTimeout(function(){ 
		$(".TerminerHistoireMaladie" ).html("<button id='TerminerHistoireMaladie' style='height:35px;'>Terminer</button>"); 
	
		$("#TerminerHistoireMaladie").click(function(){
			$("#boutonTerminerHistoireMaladie").fadeOut();
			$("#titreTableauHistoireMaladie").fadeOut(false);
			$("#TabHistoireMaladie").fadeOut(function(){ 
				$("#MenuAntecedent").fadeIn("fast");
			});
	
			return false;
		});
	},5000);
	
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
		
		setTimeout(function(){ 
			$(".TerminerAntecedentObstetrique" ).html("<button id='TerminerAntecedentObstetrique' style='height:35px;'>Terminer</button>"); 
			$("#TerminerAntecedentObstetrique").click(function(){
				$("#AntecedentObstetrique").fadeOut(function(){ 
					 $("#MenuAntecedentPersonnel").fadeIn("fast");
				 });

				return false;
			});
		},5000);

		
		//ANTECEDENTS MEDICAUX
		//ANTECEDENTS MEDICAUX
		$(".image2_AP").click(function(){
			 $("#MenuAntecedentPersonnel").fadeOut(function(){ 
				 $("#AntecedentPerineonatale").fadeIn("fast");
			 });
		});
		
		setTimeout(function(){ 
			$(".TerminerAntecedentPerineonatale" ).html("<button id='TerminerAntecedentPerineonatale' style='height:35px;'>Terminer</button>"); 
		
			$("#TerminerAntecedentPerineonatale").click(function(){
				$("#AntecedentPerineonatale").fadeOut(function(){ 
					$("#MenuAntecedentPersonnel").fadeIn("fast");
				});
				
				return false;
			});
		},5000);
		
		
		//ANTECEDENTS CHIRURGICAUX
		//ANTECEDENTS CHIRURGICAUX
		$(".image3_AP").click(function(){
			 $("#MenuAntecedentPersonnel").fadeOut(function(){ 
				 $("#AntecedentNutritionnel").fadeIn("fast");
			 });
		});
		
		setTimeout(function(){ 
			$(".TerminerAntecedentNutritionnel" ).html("<button id='TerminerAntecedentNutritionnel' style='height:35px;'>Terminer</button>"); 
		
			$("#TerminerAntecedentNutritionnel").click(function(){
				$("#AntecedentNutritionnel").fadeOut(function(){ 
					$("#MenuAntecedentPersonnel").fadeIn("fast");
				});
				
				return false;
			});
		},5000);
		
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
			
			setTimeout(function(){ 
				$(".TerminerAntecedentsFamiliaux" ).html("<button id='TerminerAntecedentsFamiliaux' style='height:35px;'>Terminer</button>"); 
			
				$("#TerminerAntecedentsFamiliaux").click(function(){
					$("#AntecedentsFamiliaux").fadeOut(function(){ 
						$("#MenuTerrainParticulier").fadeIn("fast");
					});

					return false;
				}); 
			},5000);
		
		scriptAuClick();

}



function scriptAuClick(){
	
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