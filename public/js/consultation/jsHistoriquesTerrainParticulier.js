
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
			$('#degreAF').val(0);
		}
		
	});
	
	//FRATRIE --- FRATRIE --- FRATRIE --- FRATRIE
	//FRATRIE --- FRATRIE --- FRATRIE --- FRATRIE
	//FRATRIE --- FRATRIE --- FRATRIE --- FRATRIE
	var maxTaille = 15;
	var minTaille = 1;
	
	$('#fratrieTailleAF').change(function(){
		$('#fratrieTailleFilleAF, #fratrieTailleGarconAF').val('');
		$('#fratrieRangAF').attr({'max':$(this).val()});
	}).keyup(function(){
		$('#fratrieTailleFilleAF, #fratrieTailleGarconAF').val('');
		$('#fratrieRangAF').attr({'max':$(this).val()});
	}).click(function(){
		$('#fratrieTailleFilleAF, #fratrieTailleGarconAF').val('');
		$('#fratrieRangAF').attr({'max':$(this).val()});
	});;
	
	$('#fratrieTailleFilleAF').change(function(){
		var tailleFilleAF = $(this).val();
		var tailleFratrieAF = $('#fratrieTailleAF').val();
		$(this).attr({'max':tailleFratrieAF});
		
		if(tailleFilleAF){ $('#fratrieTailleGarconAF').val(tailleFratrieAF - tailleFilleAF); }
	}).keyup(function(){
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

	}).keyup(function(){
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
	
	$('#fratrieRangAF').change(function(){
		var rang = $(this).val();
		if(rang == 1){ $('#fratrieTailleAFSuff').html('er'); }
		else if(rang > 1){  $('#fratrieTailleAFSuff').html('&egrave;me');  }
		else{ $('#fratrieTailleAFSuff').html(''); }
	}).keyup(function(){
		var rang = $(this).val();
		if(rang == 1){ $('#fratrieTailleAFSuff').html('er'); }
		else if(rang > 1){  $('#fratrieTailleAFSuff').html('&egrave;me');  }
		else{ $('#fratrieTailleAFSuff').html(''); }
	}).click(function(){
		var rang = $(this).val();
		if(rang == 1){ $('#fratrieTailleAFSuff').html('er'); }
		else if(rang > 1){  $('#fratrieTailleAFSuff').html('&egrave;me');  }
		else{ $('#fratrieTailleAFSuff').html(''); }
	});
	
	/** Choix statut drépanocytose enfants **/
	var indiceSDEnf = 0;
	$('#ajoutChoixStatutDrepanoEnfants').click(function(){
		if(indiceSDEnf < 9){
			//var largeurAjout = 18.25;
			if(indiceSDEnf == 4){ $('#choixStatutDrepanoEnfantLabel').css({'height':'67px'}); }
			if(indiceSDEnf <  4){ var largeurAuto = 25+(18.25*(indiceSDEnf+1)); $('#choixStatutDrepanoEnfantDiv').css({'width':largeurAuto+'%'}); }
			
			var html = "";
			if(indiceSDEnf > 0){ html +="<span id='Ecommercial_"+indiceSDEnf+"' style='margin-left: 8px; font-weight: bold; font-size: 19px;'>&#38;</span>"; $('#supprimeChoixStatutDrepanoEnfants').toggle(true); }
			$('#choixStatutEnfant_'+indiceSDEnf++).after( html +
					"<span id='choixStatutEnfant_"+indiceSDEnf+"' >"+
					"<select  id='choixStatutEnfant"+indiceSDEnf+"' name='choixStatutEnfant"+indiceSDEnf+"' >" +
					"<option value=''></option>" +
					"<option value='1'>AS</option>" +
					"<option value='2'>AC</option>" +
					"<option value='3'>A-Bth</option>" +
					"<option value='4'>SS</option>" +
					"<option value='5'>SC</option>" +
					"<option value='6'>S-Bth</option>" +
					"<option value='-1'>Autres..</option>" +
					"<option value='-2'>Inconnu</option>" +
					"</select>" +
					"<input type='number' id='choixStatutEnfantNb"+indiceSDEnf+"' name='choixStatutEnfantNb"+indiceSDEnf+"' >" +
					"</span>");
			$('#nbChoixStatutEnfantAF').val(indiceSDEnf);
			
			if(indiceSDEnf == 9){ $('#ajoutChoixStatutDrepanoEnfants').toggle(false); }
		}
	});
	
	$('#supprimeChoixStatutDrepanoEnfants').click(function(){
		if(indiceSDEnf > 0){
			$('#Ecommercial_'+(indiceSDEnf-1)).remove();
			$('#choixStatutEnfant_'+(indiceSDEnf--)).remove();
			if(indiceSDEnf <  4){ var largeurAuto = 25+(18.25*(indiceSDEnf)); $('#choixStatutDrepanoEnfantDiv').css({'width':largeurAuto+'%'}); }
			if(indiceSDEnf == 1){ $('#supprimeChoixStatutDrepanoEnfants').toggle(false); }
			if(indiceSDEnf == 8){ $('#ajoutChoixStatutDrepanoEnfants').toggle(true); }
			if(indiceSDEnf == 4){ $('#choixStatutDrepanoEnfantLabel').css({'height':'30px'}); }
			
			$('#nbChoixStatutEnfantAF').val(indiceSDEnf);
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
			 * EMplacement pour la gestion des ajouts des autres maladies familiales
			 */
		}
		if(!boutons[1].checked){
			/*
			 * EMplacement pour la gestion des ajouts des autres maladies familiales
			 */
		}
	});

		
	//AFFICHAGE AU DEMARRAGE --- AFFICHAGE AU DEMARRAGE
	//AFFICHAGE AU DEMARRAGE --- AFFICHAGE AU DEMARRAGE
	/*
	 * A mettre uniquement les éléments à appliquer automatiquement
	 */
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

	//Statuts drepanocytoses enfants
	setTimeout(function(){
		for(var i=0 ; i<choixStatutEnfant.length ; i++){
			$('#ajoutChoixStatutDrepanoEnfants').trigger('click');
			$('#choixStatutEnfant'+(i+1)).val(choixStatutEnfant[i]);
			$('#choixStatutEnfantNb'+(i+1)).val(choixStatutEnfantNb[i]);
		}
	});
	
	
}




































//GESTION DES HISTORIQUES DES CONSULTATIONS --- GESTION DES HISTORIQUES DES CONSULTATIONS 
//GESTION DES HISTORIQUES DES CONSULTATIONS --- GESTION DES HISTORIQUES DES CONSULTATIONS 
//GESTION DES HISTORIQUES DES CONSULTATIONS --- GESTION DES HISTORIQUES DES CONSULTATIONS 
var oTableHistoriqueConsultationPatient;

function historiquesDesConsultations(idpatient){
  
	var asInitVals = new Array();
	oTableHistoriqueConsultationPatient = $('#ListeConsultationPatient').dataTable( {
				"sPaginationType": "full_numbers",
				"aLengthMenu": [3,5,7],
				"iDisplayLength": 5,
				"aaSorting": [], //On ne trie pas la liste automatiquement
				"oLanguage": {
					"sInfo": "_START_ &agrave; _END_ sur _TOTAL_ consultations",
					"sInfoEmpty": "0 &eacute;l&eacute;ment &agrave; afficher",
					"sInfoFiltered": "",
					"sUrl": "",
					"oPaginate": {
						"sFirst":    "|<",
						"sPrevious": "<",
						"sNext":     ">",
						"sLast":     ">|"
					}
				},

				"sAjaxSource":  tabUrl[0] + "public/consultation/historiques-des-consultations-du-patient-ajax/"+idpatient,
				"fnDrawCallback": function() 
				{
					//markLine();
					//clickRowHandler();
				}
  } );
  
}

function listeAnalysesValidees(idpatient){
	
    var chemin = tabUrl[0]+'public/biologiste/get-informations-resultats-analyses-validees-patient';
    $.ajax({
        type: 'POST',
        url: chemin ,
        data:'idpatient='+idpatient,
        success: function(data) {
        	     var result = jQuery.parseJSON(data); 
        	     
        	     $('#listeAnalysesParDemande').html(result[1]);
        	     $('.visualiser'+result[2]).html("<img style='padding-left: 3px; ' src='../images_icons/transfert_droite.png' />");
        	     $('.dateAffichee_'+result[2]).css({'color' : 'green'});
        	     
        	     $('#listeAnalysesParDemande table td').css({'vertical-align':'top'});
        	     
        	     $('.barreVerticalSeparateurHauteurChangeModCons').css({'height':'300px'});
        	     $('.listeDemandeHauteurChangeModCons').css({'height':'300px', 'padding-top':'18px'});
        }
    });
	
}

function listeDemandesAnalyses()
{
    var oTable2 = $('#listeDemandesFiltre').dataTable
    ( {
    	"bDestroy":true,
		"sPaginationType": "full_numbers",
		"aLengthMenu": [3,5],
		"iDisplayLength": 3,
    	"aaSorting": [],
    	"oLanguage": {
    		"sInfo": "_START_ &agrave; _END_ sur _TOTAL_ ",
    		"sInfoEmpty": "0 &eacute;l&eacute;ment &agrave; afficher",
    		"sInfoFiltered": "",
    		"sUrl": "",
    		"oPaginate": {
    			"sFirst":    "|<",
    			"sPrevious": "<",
    			"sNext":     ">",
    			"sLast":     ">|",
    		},
    		
    	},

    } );
    
    var asInitVals = new Array();
	
	//le filtre du select
	$('#filter_statut').change(function() 
	{					
		oTable2.fnFilter( this.value );
	});
	
	$(".foot_style_demande input").keyup( function () {
		/* Filter on the column (the index) of this element */
		oTable2.fnFilter( this.value, $(".foot_style_demande input").index(this) );
	} );
	
	/*
	 * Support functions to provide a little bit of 'user friendlyness' to the textboxes in 
	 * the footer
	 */
	$(".foot_style_demande input").each( function (i) {
		asInitVals[i] = this.value;
	} );
	
	$(".foot_style_demande input").focus( function () {
		if ( this.className == "search_init" )
		{
			this.className = "";
			this.value = "";
		}
	} );
	
	$(".foot_style_demande input").blur( function (i) {
		if ( this.value == "" )
		{
			this.className = "search_init";
			this.value = asInitVals[$(".foot_style_demande input").index(this)];
		}
	} );
	
}



function listeAnalysesDemandes()
{
    var oTable2 = $('#listeAnalyseFiltre').dataTable
    ( {
    	"bDestroy":true,
		"sPaginationType": "full_numbers",
		"aLengthMenu": [5,10],
		"iDisplayLength": 5,
    	"aaSorting": [],
    	"oLanguage": {
    		"sInfo": "_START_ &agrave; _END_ sur _TOTAL_ analyses",
    		"sInfoEmpty": "0 &eacute;l&eacute;ment &agrave; afficher",
    		"sInfoFiltered": "",
    		"sUrl": "",
    		"oPaginate": {
    			"sFirst":    "|<",
    			"sPrevious": "<",
    			"sNext":     ">",
    			"sLast":     ">|",
    		},
    		
    	},

    } );
    
    var asInitVals = new Array();
	
	//le filtre du select
	$('#filter_statut').change(function() 
	{					
		oTable2.fnFilter( this.value );
	});
	
	$(".foot_style_analyse input").keyup( function () {
		/* Filter on the column (the index) of this element */
		oTable2.fnFilter( this.value, $(".foot_style_analyse input").index(this) );
	} );
	
	/*
	 * Support functions to provide a little bit of 'user friendlyness' to the textboxes in 
	 * the footer
	 */
	$(".foot_style_analyse input").each( function (i) {
		asInitVals[i] = this.value;
	} );
	
	$(".foot_style_analyse input").focus( function () {
		if ( this.className == "search_init" )
		{
			this.className = "";
			this.value = "";
		}
	} );
	
	$(".foot_style_analyse input").blur( function (i) {
		if ( this.value == "" )
		{
			this.className = "search_init";
			this.value = asInitVals[$(".foot_style_analyse input").index(this)];
		}
	} );
	
}


function vueListeAnalysesValidees(iddemande){
	var chemin = tabUrl[0]+'public/biologiste/get-liste-analyses-demandees-validees';
    $.ajax({
        type: 'POST',
        url: chemin ,
        data:'iddemande='+iddemande,
        success: function(data) {
        	     var result = jQuery.parseJSON(data);
        	     //Ici on modifie les icones 
        	     $('.iconeListeAffichee').html("<img style='padding-left: 3px; cursor: pointer;' src='../images_icons/transfert_droite2.png' />");
        	     $('.visualiser'+iddemande).html("<img style='padding-left: 3px; ' src='../images_icons/transfert_droite.png' />");
        	     $('.dateAffichee').css({'color' : 'black'});
        	     $('.dateAffichee_'+iddemande).css({'color' : 'green'});
        	     
        	     
        	     $('#liste_analyses_demandes').html(result);
        	     listeAnalysesDemandes();
        }
    });
}

//Impression des résultats des analyses demandées
//Impression des résultats des analyses demandées
//Impression des résultats des analyses demandées
function imprimerResultatsAnalysesDemandees(iddemande)
{
	if(iddemande){
		var vart = tabUrl[0]+'public/biologiste/impression-resultats-analyses-demandees';
		var FormulaireImprimerAnalysesDemandees = document.getElementById("FormulaireImprimerDemandesAnalysesHistorique");
		FormulaireImprimerAnalysesDemandees.setAttribute("action", vart);
		FormulaireImprimerAnalysesDemandees.setAttribute("method", "POST");
		FormulaireImprimerAnalysesDemandees.setAttribute("target", "_blank");
		
		//Ajout dynamique de champs dans le formulaire
		var champ = document.createElement("input");
		champ.setAttribute("type", "hidden");
		champ.setAttribute("name", 'iddemande');
		champ.setAttribute("value", iddemande);
		FormulaireImprimerAnalysesDemandees.appendChild(champ);
		$("#ImprimerDemandesAnalysesHistorique").trigger('click');
	}
	
}










//Resultats d'une seule analyse
//Resultats d'une seule analyse
//Resultats d'une seule analyse
function resultatsAnalyses(idanalyse, iddemande){
	var tab = [];
	$( "#resultatsAnalyses" ).dialog({
		resizable: false,
		height:670,
		width:730,
		autoOpen: false,
		modal: true,
		buttons: {
			
			"Terminer": function() {
				$(this).dialog( "close" );
			}
		}
	});
}

function resultatAnalyse(iddemande){
	var chemin = tabUrl[0]+'public/biologiste/recuperer-analyse';
    $.ajax({
        type: 'POST',
        url: chemin ,
        data:'iddemande='+iddemande,
        success: function(data) {
        	     var result = jQuery.parseJSON(data); 
        	     var idanalyse = result[0];
        	     resultatsAnalyses(idanalyse, iddemande);
        	     
        	     //$('#contenuResultatsAnalysesParType div').empty();
        	     $('#contenuResultatsAnalysesDuneDemande div').empty();
        	     $('#contenuResultatsAnalyses div').empty();
        	     $('#contenuResultatsAnalyses div').html(result[1]);
        	     
        	     gestionFormuleLeucocytaire();
        	     rapportCHOL_HDL();
        	     getCreatininemie_umol();
        	     getTcaRatio();
        	     getCholesterolHDL();
        	     getCholesterolLDL();
        	     getCholesterolTotal();
        	     getTriglycerides();
        	     getGlycemieFormule();
        	     getElectrophoreseProteinesFormule();
        	     getElectroHemo();
        	     getTestCombsDirect();
        	     getTestCombsIndirect();
        	     getTestCompatibilite();
        	     getAsatAlatAuto();
        	     
        	     
        	     $("#resultatsAnalyses").dialog('open');
        	     $('#commentaire_hemogramme').attr('readonly', true);

        }
    });
	
}



//Resultats des analyses d'une seule demande
//Resultats des analyses d'une seule demande
//Resultats des analyses d'une seule demande
function resultatsDesAnalysesDeLaDemande(iddemande, tabAnalyses, tabDemandes){
	    
	$( "#resultatsAnalysesDuneDemande" ).dialog({
	
		resizable: false,
	    height:670,
	    width:720,
	    autoOpen: false,
	    modal: true,
	    buttons: {

	    	"Terminer": function() {
  	        	$(this).dialog( "close" );
  	        }
  	   }
  	  
	});    
}


function resultatsDesAnalyses(iddemande){
	var typeResultat = 1; 
	
    var chemin = tabUrl[0]+'public/biologiste/recuperer-les-analyses-de-la-demande';
    $.ajax({
        type: 'POST',
        url: chemin ,
        data: {'iddemande':iddemande, 'typeResultat':typeResultat },
        success: function(data) {
        	     var result = jQuery.parseJSON(data); 

        	     var html = result[0];
        	     var tabAnalyses = result[1];
        	     var tabDemandes = result[2];
        	     resultatsDesAnalysesDeLaDemande(iddemande, tabAnalyses, tabDemandes);
        	     
        	     //$('#contenuResultatsAnalysesParType div').empty();
        	     $('#contenuResultatsAnalyses div').empty();
           	     $('#contenuResultatsAnalysesDuneDemande div').empty();
        	     $('#contenuResultatsAnalysesDuneDemande div').html(html);
        	     
        	     gestionFormuleLeucocytaire();
        	     rapportCHOL_HDL();
        	     getCreatininemie_umol();
        	     getTcaRatio();
        	     getCholesterolHDL();
        	     getCholesterolLDL();
        	     getCholesterolTotal();
        	     getTriglycerides();
        	     getGlycemieFormule();
        	     getElectrophoreseProteinesFormule();
        	     getElectroHemo();
        	     getTestCombsDirect();
        	     getTestCombsIndirect();
        	     getTestCompatibilite();
        	     getAsatAlatAuto();

        	     
        	     $("#resultatsAnalysesDuneDemande").dialog('open');
        	     $('#commentaire_hemogramme').attr('readonly', true);
        }
    });
}

//Automatisation des champs calculables  -----  Automatisation des champs calculables
//Automatisation des champs calculables  -----  Automatisation des champs calculables
//Automatisation des champs calculables  -----  Automatisation des champs calculables

function gestionFormuleLeucocytaire(){
	
	//PolynuclÃ©aires neutrophiles
	//PolynuclÃ©aires neutrophiles
	$("#champ1, #champ7").keyup( function () {
		var champ1 = $("#champ1").val();
		var champ7 = $("#champ7").val();
		if( champ1 && champ7 ){
			var resultatChamp2 = (champ1*champ7)/100;
			$("#champ2").val(resultatChamp2);
		}
		else { $("#champ2").val(null); }
		
	} ).change( function () {
		var champ1 = $("#champ1").val();
		var champ7 = $("#champ7").val();
		if( champ1 && champ7 ){
			var resultatChamp2 = (champ1*champ7)/100;
			$("#champ2").val(resultatChamp2);
		}
		else { $("#champ2").val(null); }
	} );
	
	//PolynuclÃ©aires eosinophiles
	//PolynuclÃ©aires eosinophiles
	$("#champ1, #champ8").keyup( function () {
		var champ1 = $("#champ1").val();
		var champ8 = $("#champ8").val();
		if( champ1 && champ8 ){
			var resultatChamp3 = (champ1*champ8)/100;
			$("#champ3").val(resultatChamp3);
		}
		else { $("#champ3").val(null); }
		
	} ).change( function () {
		var champ1 = $("#champ1").val();
		var champ8 = $("#champ8").val();
		if( champ1 && champ8 ){
			var resultatChamp3 = (champ1*champ8)/100;
			$("#champ3").val(resultatChamp3);
		}
		else { $("#champ3").val(null); }
	} );
	
	//PolynuclÃ©aires basophiles
	//PolynuclÃ©aires basophiles
	$("#champ1, #champ9").keyup( function () {
		var champ1 = $("#champ1").val();
		var champ9 = $("#champ9").val();
		if( champ1 && champ9 ){
			var resultatChamp4 = (champ1*champ9)/100;
			$("#champ4").val(resultatChamp4);
		}
		else { $("#champ4").val(null); }
		
	} ).change( function () {
		var champ1 = $("#champ1").val();
		var champ9 = $("#champ9").val();
		if( champ1 && champ9 ){
			var resultatChamp4 = (champ1*champ9)/100;
			$("#champ4").val(resultatChamp4);
		}
		else { $("#champ4").val(null); }
		
	} );
	
	//Lymphocytes Lymphocytes
	//Lymphocytes Lymphocytes
	$("#champ1, #champ10").keyup( function () {
		var champ1 = $("#champ1").val();
		var champ10 = $("#champ10").val();
		if( champ1 && champ10 ){
			var resultatChamp5 = (champ1*champ10)/100;
			$("#champ5").val(resultatChamp5);
		}
		else { $("#champ5").val(null); }
		
	} ).change( function () {
		var champ1 = $("#champ1").val();
		var champ10 = $("#champ10").val();
		if( champ1 && champ10 ){
			var resultatChamp5 = (champ1*champ10)/100;
			$("#champ5").val(resultatChamp5);
		}
		else { $("#champ5").val(null); }
	} );
	
	//Monocytes Monocytes
	//Monocytes Monocytes
	$("#champ1, #champ11").keyup( function () {
		var champ1 = $("#champ1").val();
		var champ11 = $("#champ11").val();
		if( champ1 && champ11 ){
			var resultatChamp6 = (champ1*champ11)/100;
			$("#champ6").val(resultatChamp6);
		}
		else { $("#champ6").val(null); }
		
	} ).change( function () {
		var champ1 = $("#champ1").val();
		var champ11 = $("#champ11").val();
		if( champ1 && champ11 ){
			var resultatChamp6 = (champ1*champ11)/100;
			$("#champ6").val(resultatChamp6);
		}
		else { $("#champ6").val(null); }
	} );
	
	
	//Taux de réticulocytes -- Taux de réticulocytes
	//Taux de réticulocytes -- Taux de réticulocytes
	$("#champ12, #champ25").keyup( function () {
		var champ12 = $("#champ12").val();
		var champ25 = $("#champ25").val();
		if( champ12 && champ25 ){
			var resultatChamp24 = champ12*10000*champ25;
			$("#champ24").val(resultatChamp24);
		}
		else { $("#champ24").val(null); }
		
	} ).change( function () {
		var champ12 = $("#champ12").val();
		var champ25 = $("#champ25").val();
		if( champ12 && champ25 ){
			var resultatChamp24 = champ12*10000*champ25;
			$("#champ24").val(resultatChamp24);
		}
		else { $("#champ24").val(null); }
	} );
}

function rapportCHOL_HDL(){
	var cholesterol_total_1 = $('#cholesterol_total_1').val();
	var cholesterol_HDL_1 = $('#cholesterol_HDL_1').val();
	var rapport = null;
	
	if(cholesterol_total_1 && cholesterol_HDL_1){
		rapport = cholesterol_total_1/cholesterol_HDL_1;
		
		$('.rapport_chol_hdl').toggle(true);
		$('#rapport_chol_hdl').val(rapport.toFixed(2));
		
		//Affichage de la conclusion du rapport
		if(rapport >= 3.5 && rapport <= 5){
			$('#conclusion_rapport_chol_hdl').html('<span style="color: orange; float: left"> Risque d\'ath&eacute;rog&egrave;ne faible </span>');
		}else if(rapport > 5 && rapport <= 6.5){
			$('#conclusion_rapport_chol_hdl').html('<span style="color: orange; float: left"> Risque d\'ath&eacute;rog&egrave;ne mod&eacute;r&eacute; </span>');
		}else if(rapport > 6.5){
			$('#conclusion_rapport_chol_hdl').html('<span style="color: red; float: left"> Risque d\'ath&eacute;rog&egrave;ne &eacute;lev&eacute; </span>');
		}else{
			$('#conclusion_rapport_chol_hdl').html('<span style="color: green; float: left;"> RAS </span>');
		}
	}
	
	$("#cholesterol_total_1, #cholesterol_HDL_1").keyup( function () {
		var cholesterol_total_1 = $("#cholesterol_total_1").val();
		var cholesterol_HDL_1 = $("#cholesterol_HDL_1").val();
		
		if( cholesterol_total_1 == "" || cholesterol_total_1 == 0 || cholesterol_HDL_1 == "" || cholesterol_HDL_1 == 0 ){
			$('.rapport_chol_hdl table').toggle(false);
		}else
		if( cholesterol_total_1 && cholesterol_HDL_1 ){
			var rapport = cholesterol_total_1/cholesterol_HDL_1;
			$('.rapport_chol_hdl').toggle(true);
			$("#rapport_chol_hdl").val(rapport.toFixed(2));
			$('.rapport_chol_hdl table').toggle(true);
			
			//Affichage de la conclusion du rapport
			if(rapport >= 3.5 && rapport <= 5){
				$('#conclusion_rapport_chol_hdl').html('<span style="color: orange; float: left"> Risque d\'ath&eacute;rog&egrave;ne faible </span>');
			}else if(rapport > 5 && rapport <= 6.5){
				$('#conclusion_rapport_chol_hdl').html('<span style="color: orange; float: left"> Risque d\'ath&eacute;rog&egrave;ne mod&eacute;r&eacute; </span>');
			}else if(rapport > 6.5){
				$('#conclusion_rapport_chol_hdl').html('<span style="color: red; float: left"> Risque d\'ath&eacute;rog&egrave;ne &eacute;lev&eacute; </span>');
			}else{
				$('#conclusion_rapport_chol_hdl').html('<span style="color: green; float: left;"> RAS </span>');
			}
		}
		else { 
			$("#rapport_chol_hdl").val(null); 
			$('#conclusion_rapport_chol_hdl').html('<span style="color: green; float: left;"> RAS </span>');	
		}
		
	} ).change( function () {
		var cholesterol_total_1 = $("#cholesterol_total_1").val();
		var cholesterol_HDL_1 = $("#cholesterol_HDL_1").val();
		
		if( cholesterol_total_1 == "" || cholesterol_total_1 == 0 || cholesterol_HDL_1 == "" || cholesterol_HDL_1 == 0 ){
			$('.rapport_chol_hdl table').toggle(false);
		}else
		if( cholesterol_total_1 && cholesterol_HDL_1 ){
			var rapport = cholesterol_total_1/cholesterol_HDL_1;
			$('.rapport_chol_hdl').toggle(true);
			$("#rapport_chol_hdl").val(rapport.toFixed(2));
			$('.rapport_chol_hdl table').toggle(true);

			//Affichage de la conclusion du rapport
			if(rapport >= 3.5 && rapport <= 5){
				$('#conclusion_rapport_chol_hdl').html('<span style="color: orange; float: left"> Risque d\'ath&eacute;rog&egrave;ne faible </span>');
			}else if(rapport > 5 && rapport <= 6.5){
				$('#conclusion_rapport_chol_hdl').html('<span style="color: orange; float: left"> Risque d\'ath&eacute;rog&egrave;ne mod&eacute;r&eacute; </span>');
			}else if(rapport > 6.5){
				$('#conclusion_rapport_chol_hdl').html('<span style="color: red; float: left"> Risque d\'ath&eacute;rog&egrave;ne &eacute;lev&eacute; </span>');
			}else{
				$('#conclusion_rapport_chol_hdl').html('<span style="color: green; float: left;"> RAS </span>');
			}
			
		}
		else {
			$("#rapport_chol_hdl").val(null); 
			$('#conclusion_rapport_chol_hdl').html('<span style="color: green; float: left"> RAS </span>');	
		}
		
	} );
	
	
}

function getCreatininemie_umol(){
	var creatininemie = $('#creatininemie').val();
	var valeur_umol = null;
	if(creatininemie){
		valeur_umol = creatininemie * 8.84;
		$('#creatininemie_umol').val(valeur_umol.toFixed(2));
	}else{
		$('#creatininemie_umol').val(null);
	}
	
	$('#creatininemie').keyup( function () {
		creatininemie = $('#creatininemie').val();
		if(creatininemie){
    		valeur_umol = creatininemie * 8.84;
    		$('#creatininemie_umol').val(valeur_umol.toFixed(2));
    	}else{
    		$('#creatininemie_umol').val(null);
    	}
	}).change( function(){
		creatininemie = $('#creatininemie').val();
		if(creatininemie){
    		valeur_umol = creatininemie * 8.84;
    		$('#creatininemie_umol').val(valeur_umol.toFixed(2));
    	}else{
    		$('#creatininemie_umol').val(null);
    	}
	});
	
}

function getTcaRatio(){
	var tca_patient = $('#tca_patient').val();
	var temoin_patient = $('#temoin_patient').val();
	
	if(tca_patient && temoin_patient){
		var tca_ratio = tca_patient/temoin_patient;
		$('#tca_ratio').val(tca_ratio.toFixed(2));
	}else{
		$('#tca_ratio').val(null);
	}
	
	$('#tca_patient, #temoin_patient').keyup( function () {
		var tca_patient = $('#tca_patient').val();
    	var temoin_patient = $('#temoin_patient').val();
    	
		if(tca_patient && temoin_patient){
    		var tca_ratio = tca_patient/temoin_patient;
    		$('#tca_ratio').val(tca_ratio.toFixed(2));
    	}else{
    		$('#tca_ratio').val(null);
    	}
		
	}).change( function(){
		var tca_patient = $('#tca_patient').val();
    	var temoin_patient = $('#temoin_patient').val();
    	
		if(tca_patient && temoin_patient){
    		var tca_ratio = tca_patient/temoin_patient;
    		$('#tca_ratio').val(tca_ratio.toFixed(2));
    	}else{
    		$('#tca_ratio').val(null);
    	}
	});
	
}

function getCholesterolTotal(){
	var cholesterol_total_1 = $('#cholesterol_total_1').val();
	var valeur_mmol = null;
	
	$('#cholesterol_total_1').keyup( function () {
		cholesterol_total_1 = $('#cholesterol_total_1').val();
		if(cholesterol_total_1){
    		valeur_mmol = cholesterol_total_1 * 2.587;
    		$('#cholesterol_total_2').val(valeur_mmol.toFixed(2));
    	}else{
    		$('#cholesterol_total_2').val(null);
    	}
	}).change( function(){
		cholesterol_total_1 = $('#cholesterol_total_1').val();
		if(cholesterol_total_1){
    		valeur_mmol = cholesterol_total_1 * 2.587;
    		$('#cholesterol_total_2').val(valeur_mmol.toFixed(2));
    	}else{
    		$('#cholesterol_total_2').val(null);
    	}
	});
	
}

function getCholesterolHDL(){
	var cholesterol_HDL_1 = $('#cholesterol_HDL_1').val();
	var valeur_mmol = null;
	
	$('#cholesterol_HDL_1').keyup( function () {
		cholesterol_HDL_1 = $('#cholesterol_HDL_1').val();
		if(cholesterol_HDL_1){
    		valeur_mmol = cholesterol_HDL_1 * 2.587;
    		$('#cholesterol_HDL_2').val(valeur_mmol.toFixed(2));
    	}else{
    		$('#cholesterol_HDL_2').val(null);
    	}
	}).change( function(){
		cholesterol_HDL_1 = $('#cholesterol_HDL_1').val();
		if(cholesterol_HDL_1){
    		valeur_mmol = cholesterol_HDL_1 * 2.587;
    		$('#cholesterol_HDL_2').val(valeur_mmol.toFixed(2));
    	}else{
    		$('#cholesterol_HDL_2').val(null);
    	}
	});
	
}

function getCholesterolLDL(){
	var cholesterol_LDL_1 = $('#cholesterol_LDL_1').val();
	var valeur_mmol = null;
	
	$('#cholesterol_LDL_1').keyup( function () {
		cholesterol_LDL_1 = $('#cholesterol_LDL_1').val();
		if(cholesterol_LDL_1){
    		valeur_mmol = cholesterol_LDL_1 * 2.587;
    		$('#cholesterol_LDL_2').val(valeur_mmol.toFixed(2));
    	}else{
    		$('#cholesterol_LDL_2').val(null);
    	}
	}).change( function(){
		cholesterol_LDL_1 = $('#cholesterol_LDL_1').val();
		if(cholesterol_LDL_1){
    		valeur_mmol = cholesterol_LDL_1 * 2.587;
    		$('#cholesterol_LDL_2').val(valeur_mmol.toFixed(2));
    	}else{
    		$('#cholesterol_LDL_2').val(null);
    	}
	});
	
}


function getTriglycerides(){
	var triglycerides_1 = $('#triglycerides_1').val();
	var valeur_mmol = null;
	
	$('#triglycerides_1').keyup( function () {
		triglycerides_1 = $('#triglycerides_1').val();
		if(triglycerides_1){
    		valeur_mmol = triglycerides_1 * 1.143;
    		$('#triglycerides_2').val(valeur_mmol.toFixed(3));
    	}else{
    		$('#triglycerides_2').val(null);
    	}
	}).change( function(){
		triglycerides_1 = $('#triglycerides_1').val();
		if(triglycerides_1){
    		valeur_mmol = triglycerides_1 * 1.143;
    		$('#triglycerides_2').val(valeur_mmol.toFixed(3));
    	}else{
    		$('#triglycerides_2').val(null);
    	}
	});
	
}


function getGlycemieFormule(){
	var glycemie_1 = $('#glycemie_1').val();
	var valeur_mmol = null;
	
	$('#glycemie_1').keyup( function () {
		glycemie_1 = $('#glycemie_1').val();
		if(glycemie_1){
    		valeur_mmol = glycemie_1 * 5.55;
    		$('#glycemie_2').val(valeur_mmol.toFixed(2));
    	}else{
    		$('#glycemie_2').val(null);
    	}
	}).change( function(){
		glycemie_1 = $('#glycemie_1').val();
		if(glycemie_1){
    		valeur_mmol = glycemie_1 * 5.55;
    		$('#glycemie_2').val(valeur_mmol.toFixed(2));
    	}else{
    		$('#glycemie_2').val(null);
    	}
	});
	
}


function getElectrophoreseProteinesFormule(){
	var albumine = $('#albumine').val();
	var alpha_1  = $('#alpha_1').val();
	var alpha_2  = $('#alpha_2').val();
	var beta_1   = $('#beta_1').val();
	var beta_2   = $('#beta_2').val();
	var gamma    = $('#gamma').val();
	var proteine_totale    = $('#proteine_totale').val();
	
	$('#albumine, #proteine_totale').keyup( function () {
		albumine = $('#albumine').val();
		proteine_totale    = $('#proteine_totale').val();
		if(albumine && proteine_totale){ 
    		var albumine_abs = (albumine * proteine_totale)/100;
    		$('#albumine_abs').val(albumine_abs.toFixed(1));
    	}else{
    		$('#albumine_abs').val(null);
    	}
	}).change( function(){
		albumine = $('#albumine').val();
		proteine_totale    = $('#proteine_totale').val();
		if(albumine && proteine_totale){ 
    		var albumine_abs = (albumine * proteine_totale)/100;
    		$('#albumine_abs').val(albumine_abs.toFixed(1));
    	}else{
    		$('#albumine_abs').val(null);
    	}
	});
	
	$('#alpha_1, #proteine_totale').keyup( function () {
		alpha_1 = $('#alpha_1').val();
		proteine_totale    = $('#proteine_totale').val();
		if(alpha_1 && proteine_totale){ 
    		var alpha_1_abs = (alpha_1 * proteine_totale)/100;
    		$('#alpha_1_abs').val(alpha_1_abs.toFixed(1));
    	}else{
    		$('#alpha_1_abs').val(null);
    	}
	}).change( function(){
		alpha_1 = $('#alpha_1').val();
		proteine_totale    = $('#proteine_totale').val();
		if(alpha_1 && proteine_totale){ 
    		var alpha_1_abs = (alpha_1 * proteine_totale)/100;
    		$('#alpha_1_abs').val(alpha_1_abs.toFixed(1));
    	}else{
    		$('#alpha_1_abs').val(null);
    	}
	});
	
	$('#alpha_2, #proteine_totale').keyup( function () {
		alpha_2 = $('#alpha_2').val();
		proteine_totale    = $('#proteine_totale').val();
		if(alpha_2 && proteine_totale){ 
    		var alpha_2_abs = (alpha_2 * proteine_totale)/100;
    		$('#alpha_2_abs').val(alpha_2_abs.toFixed(1));
    	}else{
    		$('#alpha_2_abs').val(null);
    	}
	}).change( function(){
		alpha_2 = $('#alpha_2').val();
		proteine_totale    = $('#proteine_totale').val();
		if(alpha_2 && proteine_totale){ 
    		var alpha_2_abs = (alpha_2 * proteine_totale)/100;
    		$('#alpha_2_abs').val(alpha_2_abs.toFixed(1));
    	}else{
    		$('#alpha_2_abs').val(null);
    	}
	});
	
	$('#beta_1, #proteine_totale').keyup( function () {
		beta_1 = $('#beta_1').val();
		proteine_totale    = $('#proteine_totale').val();
		if(beta_1 && proteine_totale){ 
    		var beta_1_abs = (beta_1 * proteine_totale)/100;
    		$('#beta_1_abs').val(beta_1_abs.toFixed(1));
    	}else{
    		$('#beta_1_abs').val(null);
    	}
	}).change( function(){
		beta_1 = $('#beta_1').val();
		proteine_totale    = $('#proteine_totale').val();
		if(beta_1 && proteine_totale){ 
    		var beta_1_abs = (beta_1 * proteine_totale)/100;
    		$('#beta_1_abs').val(beta_1_abs.toFixed(1));
    	}else{
    		$('#beta_1_abs').val(null);
    	}
	});
	
	$('#beta_2, #proteine_totale').keyup( function () {
		beta_2 = $('#beta_2').val();
		proteine_totale    = $('#proteine_totale').val();
		if(beta_2 && proteine_totale){ 
    		var beta_2_abs = (beta_2 * proteine_totale)/100;
    		$('#beta_2_abs').val(beta_2_abs.toFixed(1));
    	}else{
    		$('#beta_2_abs').val(null);
    	}
	}).change( function(){
		beta_2 = $('#beta_2').val();
		proteine_totale    = $('#proteine_totale').val();
		if(beta_2 && proteine_totale){ 
    		var beta_2_abs = (beta_2 * proteine_totale)/100;
    		$('#beta_2_abs').val(beta_2_abs.toFixed(1));
    	}else{
    		$('#beta_2_abs').val(null);
    	}
	});
	
	$('#gamma, #proteine_totale').keyup( function () {
		gamma = $('#gamma').val();
		proteine_totale    = $('#proteine_totale').val();
		if(gamma && proteine_totale){ 
    		var gamma_abs = (gamma * proteine_totale)/100;
    		$('#gamma_abs').val(gamma_abs.toFixed(1));
    	}else{
    		$('#gamma_abs').val(null);
    	}
	}).change( function(){
		gamma = $('#gamma').val();
		proteine_totale    = $('#proteine_totale').val();
		if(gamma && proteine_totale){ 
    		var gamma_abs = (gamma * proteine_totale)/100;
    		$('#gamma_abs').val(gamma_abs.toFixed(1));
    	}else{
    		$('#gamma_abs').val(null);
    	}
	});
}

function getAsatAlatAuto(){
	$('#type_materiel_tgo_asat').keyup( function () {
		var type_materiel_tgo_asat = $('#type_materiel_tgo_asat').val();
		
		if(type_materiel_tgo_asat){ 
    		$('#type_materiel_tgp_alat').val(type_materiel_tgo_asat);
    	}else{
    		$('#type_materiel_tgp_alat').val(null);
    	}
	}).change( function(){
		var type_materiel_tgo_asat = $('#type_materiel_tgo_asat').val();
		
		if(type_materiel_tgo_asat){ 
    		$('#type_materiel_tgp_alat').val(type_materiel_tgo_asat);
    	}else{
    		$('#type_materiel_tgp_alat').val(null);
    	}
	});
}

function getAlbumineUrinaireVal(resultat){
	
	if(resultat == 'positif'){
		$('#albumine_urinaire_degres').fadeIn(500);
	}else{
		$('#albumine_urinaire_degres').toggle(false);
	}
	
}

function getSucreUrinaireVal(resultat){
	
	if(resultat == 'positif'){
		$('#sucre_urinaire_degres').fadeIn(500);
	}else{
		$('#sucre_urinaire_degres').toggle(false);
	}
	
}

function getCorpsCetoniqueUrinaireVal(resultat){
	
	if(resultat == 'positif'){
		$('#corps_cetonique_urinaire_degres').fadeIn(500);
	}else{
		$('#corps_cetonique_urinaire_degres').toggle(false);
	}
	
}

function getElectroHemo(){
	$('#electro_hemo_moins').toggle(false);
    
	$('#electro_hemo_plus').click(function(){
    	var nbLigne = $("#electro_hemo tr").length;
    	$('#electro_hemo_moins').toggle(true);
    	
    	if(nbLigne < 10){
    		var html ="<tr id='electro_hemo_"+nbLigne+"' class='ligneAnanlyse' style='width: 100%;'>"+
                        "<td style='width: 45%;'><label class='lab1'><span style='font-weight: bold; '>  <input id='electro_hemo_label_"+nbLigne+"' type='text' style='font-weight: bold; padding-right: 5px; margin-right: 30px;' readonly> </span></label></td>"+
                        "<td style='width: 35%;'><label class='lab2' style='padding-top: 5px;'> <input id='electro_hemo_valeur_"+nbLigne+"' type='number' step='any' readonly> % </label></td>"+
                        "<td style='width: 20%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> </label></td>"+
                      "</tr>";

	    	$('#electro_hemo_'+(nbLigne-1)).after(html);
	    	
	    	if(nbLigne == 9){
	    		$('#electro_hemo_plus').toggle(false);
	    	}
    	}

    });
    
    $('#electro_hemo_moins').click(function(){ 
    	var nbLigne = $("#electro_hemo tr").length;
    	
    	if(nbLigne > 2){
	    	$('#electro_hemo_'+(nbLigne-1)).remove();
	    	if(nbLigne == 3){ 
	    		$('#electro_hemo_moins').toggle(false);
	    	}
	    	
	    	if(nbLigne == 10){
	    		$('#electro_hemo_plus').toggle(true);
	    	}
    	}

    });
}

function getElectrophoreseHemoglobine(){
	var tab = [];
	var nbLigne = $("#electro_hemo tr").length;
	var j = 1;
	
	tab[0] = $('#type_materiel_electro_hemo').val();
	tab[1] = new Array(); 
	tab[2] = new Array(); 
	for(var i=1 ; i<nbLigne ; i++){
		var label  = $('#electro_hemo_label_'+i ).val();
		var valeur = $('#electro_hemo_valeur_'+i).val();
		if(label && valeur){
    		tab[1][j]   = label;
    		tab[2][j++] = valeur;
		}
	}
    
    return tab;
}


function getTestCombsDirect(val){
	if(val == 'Positif'){
		$('.titre_combs_direct').toggle(true);
	}else{
		$('.titre_combs_direct').toggle(false).val(null);
	}
}

function getTestCombsIndirect(val){
	if(val == 'Positif'){
		$('.titre_combs_indirect').toggle(true);
	}else{
		$('.titre_combs_indirect').toggle(false).val(null);
	}
}

function getTestCompatibilite(val){
	if(val == 'Compatible'){
		$('.titre_test_compatibilite').toggle(true);
	}else{
		$('.titre_test_compatibilite').toggle(false).val(null);
	}
}

/**
 * ************************************************
 * ------------------------------------------------
 * ************************************************
 * ------------------------------------------------
 */