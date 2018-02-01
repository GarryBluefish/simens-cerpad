
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

$(".episodeFievreClassHM div").toggle(false);

function getInfoEpisodeFievre(id){

	if(id == 1){
		$(".episodeFievreClassHM div").fadeIn();
	}else if(id == 0 || id == ''){
		$(".episodeFievreClassHM div").fadeOut(false);
	}
	
}

function getInfoAutresEvenements(id){

	if(id == 1 || id == 2){
		$(".autresInfo div").fadeIn();
	}else{
		$(".autresInfo div").fadeOut(false);
	}
}

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
	
	//ANALYSES
	//ANALYSES
	$("#titreTableauAnalyses").toggle(false);
	$("#boutonTerminerAnalyses").toggle(false);
	$("#TabAnalyses").toggle(false);
	
	$(".image2").click(function(){ 
		
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
	
	//HOSPITALISATION
	//HOSPITALISATION
	$(".image3").click(function(){
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
	
	//TRANSFUSION
	//TRANSFUSION
	$("#titreTableauTransfusion").toggle(false);
	$("#boutonTerminerTransfusion").toggle(false);
	$("#TabTransfusion").toggle(false);
	
	$(".image4").click(function(){ 
		
		 $("#MenuAntecedent").fadeOut(function(){ 
			 $("#TabTransfusion").fadeIn("fast");
		     $("#boutonTerminerTransfusion").toggle(true);
		     $("#titreTableauTransfusion").toggle(true);
		 });
	});
	
	setTimeout(function(){ 
		$(".TerminerTransfusion" ).html("<button id='TerminerTransfusion' style='height:35px;'>Terminer</button>"); 
	
		$("#TerminerTransfusion").click(function(){
			$("#boutonTerminerTransfusion").fadeOut();
			$("#titreTableauTransfusion").fadeOut(false);
			$("#TabTransfusion").fadeOut(function(){ 
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
	
	$(".image5").click(function(){
		
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
		$("#AntecedentScolarite").toggle(false);
		$("#AntecedentVaccination").toggle(false);
		$("#AntecedentMedicaux").toggle(false);
		$("#AntecedentChirurgicaux").toggle(false);
		

		//*****************************************************************
		//*****************************************************************
		//*****************************************************************
		//*****************************************************************
		//ANTECEDENTS PERSONNELS --- ANTECEDENTS PERSONNELS
		//ANTECEDENTS PERSONNELS --- ANTECEDENTS PERSONNELS
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
		
		
		//ANTECEDENT ANTENATAUX  (Obstetrique)
		//ANTECEDENT ANTENATAUX  (Obstetrique)
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

		
		//ANTECEDENTS PERINATAUX
		//ANTECEDENTS PERINATAUX
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
		
		
		//ANTECEDENTS ALIMENTATION
		//ANTECEDENTS ALIMENTATION
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
		
		
		//ANTECEDENTS VACCINATION
		//ANTECEDENTS VACCINATION
		$(".image4_AP").click(function(){
			 $("#MenuAntecedentPersonnel").fadeOut(function(){ 
				 $("#AntecedentVaccination").fadeIn("fast");
			 });
		});
		
		setTimeout(function(){
			$(".TerminerAntecedentVaccination" ).html("<button id='TerminerAntecedentVaccination' style='height:35px;'>Terminer</button>"); 
			$("#TerminerAntecedentVaccination").click(function(){
				$("#AntecedentVaccination").fadeOut(function(){ 
					 $("#MenuAntecedentPersonnel").fadeIn("fast");
				 });

				return false;
			});
		},5000);
		
		//ANTECEDENTS SCOLARITE
		//ANTECEDENTS SCOLARITE
		$(".image5_AP").click(function(){
			 $("#MenuAntecedentPersonnel").fadeOut(function(){ 
				 $("#AntecedentScolarite").fadeIn("fast");
			 });
		});
		
		setTimeout(function(){
			$(".TerminerAntecedentScolarite" ).html("<button id='TerminerAntecedentScolarite' style='height:35px;'>Terminer</button>"); 
			$("#TerminerAntecedentScolarite").click(function(){
				$("#AntecedentScolarite").fadeOut(function(){ 
					 $("#MenuAntecedentPersonnel").fadeIn("fast");
				 });

				return false;
			});
		},5000);
		
		//ANTECEDENTS MEDICAUX
		//ANTECEDENTS MEDICAUX
		$(".image6_AP").click(function(){
			 $("#MenuAntecedentPersonnel").fadeOut(function(){ 
				 $("#AntecedentMedicaux").fadeIn("fast");
			 });
		});
		
		setTimeout(function(){
			$(".TerminerAntecedentMedicaux" ).html("<button id='TerminerAntecedentMedicaux' style='height:35px;'>Terminer</button>"); 
			$("#TerminerAntecedentMedicaux").click(function(){
				$("#AntecedentMedicaux").fadeOut(function(){ 
					 $("#MenuAntecedentPersonnel").fadeIn("fast");
				 });

				return false;
			});
		},5000);
		
		//ANTECEDENTS CHIRURGICAUX
		//ANTECEDENTS CHIRURGICAUX
		$(".image7_AP").click(function(){
			 $("#MenuAntecedentPersonnel").fadeOut(function(){ 
				 $("#AntecedentChirurgicaux").fadeIn("fast");
			 });
		});
		
		setTimeout(function(){
			$(".TerminerAntecedentChirurgicaux" ).html("<button id='TerminerAntecedentChirurgicaux' style='height:35px;'>Terminer</button>"); 
			$("#TerminerAntecedentChirurgicaux").click(function(){
				$("#AntecedentChirurgicaux").fadeOut(function(){ 
					 $("#MenuAntecedentPersonnel").fadeIn("fast");
				 });

				return false;
			});
		},5000);
		
		
		//******************************************************************************
		//******************************************************************************
		//******************************************************************************
		
		//******************************************************************************
		//******************************************************************************
		//ANTECEDENTS FAMILIAUX --- ANTECEDENTS FAMILIAUX
		//ANTECEDENTS FAMILIAUX --- ANTECEDENTS FAMILIAUX
		
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
	
	
	
	//CONSANGUINITE --- CONSANGUINITE --- CONSANGUINITE
	//CONSANGUINITE --- CONSANGUINITE --- CONSANGUINITE
	//CONSANGUINITE --- CONSANGUINITE --- CONSANGUINITE
	$('.labelDegreAF').toggle(false);
	$('#consanguiniteAF').change(function(){
		if($(this).val() == 1){
			$('.labelDegreAF').toggle(true);
		}else{
			$('.labelDegreAF').toggle(false);
		}
		
	});
	
	//FRATRIE --- FRATRIE --- FRATRIE --- FRATRIE
	//FRATRIE --- FRATRIE --- FRATRIE --- FRATRIE
	//FRATRIE --- FRATRIE --- FRATRIE --- FRATRIE
	var maxTaille = 15;
	var minTaille = 1;
	
	$('#fratrieTailleAF').change(function(){
		$('#fratrieTailleFilleAF, #fratrieTailleGarconAF').val('');
	});
	
	$('#fratrieTailleFilleAF').change(function(){
		var tailleFilleAF = $(this).val();
		var tailleFratrieAF = $('#fratrieTailleAF').val();
		$(this).attr({'max':tailleFratrieAF});
		
		if(tailleFilleAF){ $('#fratrieTailleGarconAF').val(tailleFratrieAF - tailleFilleAF); }
	}).click(function(){
		var tailleFilleAF = $(this).val();
		var tailleFratrieAF = $('#fratrieTailleAF').val();
		$(this).attr({'max':tailleFratrieAF});
		
		if(tailleFilleAF){ $('#fratrieTailleGarconAF').val(tailleFratrieAF - tailleFilleAF); }
	});
	
	$('#fratrieTailleGarconAF').change(function(){
		var tailleGarconAF = $(this).val();
		var tailleFratrieAF = $('#fratrieTailleAF').val();
		$(this).attr({'max':tailleFratrieAF});
		
		if(tailleGarconAF){
			$('#fratrieTailleFilleAF').val(tailleFratrieAF - tailleGarconAF);			
		}

	}).click(function(){
		var tailleGarconAF = $(this).val();
		var tailleFratrieAF = $('#fratrieTailleAF').val();
		$(this).attr({'max':tailleFratrieAF});
		
		if(tailleGarconAF){
			$('#fratrieTailleFilleAF').val(tailleFratrieAF - tailleGarconAF);			
		}
	});
	
	/** Choix statut drépanocytose enfants **/
	var indice = 0;
	$('#ajoutChoixStatutDrepanoEnfants').click(function(){
		if(indice < 9){
			//var largeurAjout = 18.25;
			if(indice == 4){ $('#choixStatutDrepanoEnfantLabel').css({'height':'67px'}); }
			if(indice <  4){ var largeurAuto = 25+(18.25*(indice+1)); $('#choixStatutDrepanoEnfantDiv').css({'width':largeurAuto+'%'}); }
			
			var html = "";
			if(indice > 0){ html +="<span style='margin-left: 8px; font-weight: bold; font-size: 19px;'>&#38;</span>";}
			$('#choixStatutEnfant'+indice++).after( html +
					"<span id='choixStatutEnfant"+indice+"' >"+
					"<select  id='choixStatutEnfant"+indice+"' name='choixStatutEnfant"+indice+"' >" +
					"<option value=''></option>" +
					"<option value='AS'>AS</option>" +
					"<option value='AC'>AC</option>" +
					"<option value='A-Bth'>A-Bth</option>" +
					"<option value='SS'>SS</option>" +
					"<option value='SC'>SC</option>" +
					"<option value='S-Bth'>S-Bth</option>" +
					"<option value='Autres'>Autres..</option>" +
					"<option value='Inconnu'>Inconnu</option>" +
					"</select>" +
					"<input type='number' id='choixStatutEnfantNb"+indice+"' name='choixStatutEnfantNb"+indice+"' >" +
					"</span>");
		}
	});
	
	/** Autres maladies familiales **/
	
	$('#autresMaladiesFamiliales input[name=AllergiesAF]').click(function(){ 
		var boutons = $('#autresMaladiesFamiliales input[name=AllergiesAF]');
		if( boutons[1].checked){ $("#libelleAllergiesAF").html('&#10003; Allergie').css({'color':'red', 'font-weight':'bold'}); }
		if(!boutons[1].checked){ $("#libelleAllergiesAF").html('Allergie').css({'color':'black', 'font-weight':'normal'}); }
	});
	
	$('#autresMaladiesFamiliales input[name=AsthmeAF]').click(function(){ 
		var boutons = $('#autresMaladiesFamiliales input[name=AsthmeAF]');
		if( boutons[1].checked){ $("#libelleAsthmeAF").html('&#10003; Asthme').css({'color':'red', 'font-weight':'bold'}); }
		if(!boutons[1].checked){ $("#libelleAsthmeAF").html('Asthme').css({'color':'black', 'font-weight':'normal'}); }
	});
	
	$('#autresMaladiesFamiliales input[name=DiabeteAF]').click(function(){ 
		var boutons = $('#autresMaladiesFamiliales input[name=DiabeteAF]');
		if( boutons[1].checked){ $("#libelleDiabeteAF").html('&#10003; Diabete').css({'color':'red', 'font-weight':'bold'}); }
		if(!boutons[1].checked){ $("#libelleDiabeteAF").html('Diabete').css({'color':'black', 'font-weight':'normal'}); }
	});
	
	$('#autresMaladiesFamiliales input[name=HtaAF]').click(function(){ 
		var boutons = $('#autresMaladiesFamiliales input[name=HtaAF]');
		if( boutons[1].checked){ $("#libelleHtaAF").html('&#10003; Hta').css({'color':'red', 'font-weight':'bold'}); }
		if(!boutons[1].checked){ $("#libelleHtaAF").html('Hta').css({'color':'black', 'font-weight':'normal'}); }
	});
	
	$('#autresMaladiesFamiliales input[name=AutresAF]').click(function(){ 
		var boutons = $('#autresMaladiesFamiliales input[name=AutresAF]');
		if( boutons[1].checked){
			/*
			 * EMplacement de la gestion des ajouts des autres maladies familiales
			 */
		}
		if(!boutons[1].checked){
			/*
			 * EMplacement de la gestion des ajouts des autres maladies familiales
			 */
		}
	});
	
	
	
	
	
	//ANTECEDENTS FAMILIAUX TESTER SI C'EST COCHE
	//ANTECEDENTS FAMILIAUX TESTER SI C'EST COCHE
	/*
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
	*/
	
}