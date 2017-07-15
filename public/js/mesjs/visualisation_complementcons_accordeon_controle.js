
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
  
//********************* MOTIFS D'ADMISSION ***************************
//********************* MOTIFS D'ADMISSION ***************************
  $(function(){
   setTimeout(function(){
    var motif1 = $("#motif_admission1");
	var motif2 = $("#motif_admission2");
	var motif3 = $("#motif_admission3");
	var motif4 = $("#motif_admission4");
	var motif5 = $("#motif_admission5");
	
    motif1.attr( 'disabled', true).css({'background':'#f8f8f8'});
	motif2.attr( 'disabled', true).css({'background':'#f8f8f8'});
	motif3.attr( 'disabled', true).css({'background':'#f8f8f8'});
	motif4.attr( 'disabled', true).css({'background':'#f8f8f8'});
	motif5.attr( 'disabled', true).css({'background':'#f8f8f8'});
	$("#bouton_motif_modifier").toggle(false);
	$("#bouton_motif_valider").toggle(false);
	
	$('#ajouter_motif_img').toggle(false);
	$('#supprimer_motif_img').toggle(false);
	
	$('.supprimerMotif1, .supprimerMotif2, .supprimerMotif3, .supprimerMotif4, .supprimerMotif5').toggle(false);
	  }, 2000);
  });
  
	//******************* VALIDER LES DONNEES DU TABLEAU DES CONSTANTES ******************************** 
	//******************* VALIDER LES DONNEES DU TABLEAU DES CONSTANTES ******************************** 

    setTimeout(function(){
	
    var poids = $('#poids');
	var taille = $('#taille');
	var temperature = $('#temperature');
	var glycemie_capillaire = $('#glycemie_capillaire');
	var pouls = $('#pouls');
	var frequence_respiratoire = $('#frequence_respiratoire');
	var tensionmaximale = $("#tensionmaximale");
	var tensionminimale = $("#tensionminimale");
		  
	poids.attr( 'disabled', true ).css({'background':'#f8f8f8'});    
	taille.attr( 'disabled', true ).css({'background':'#f8f8f8'});
	temperature.attr( 'disabled', true).css({'background':'#f8f8f8'});
	glycemie_capillaire.attr( 'disabled', true).css({'background':'#f8f8f8'});
	pouls.attr( 'disabled', true).css({'background':'#f8f8f8'});
	frequence_respiratoire.attr( 'disabled', true).css({'background':'#f8f8f8'});
	tensionmaximale.attr( 'disabled', true ).css({'background':'#f8f8f8'});
	tensionminimale.attr( 'disabled', true ).css({'background':'#f8f8f8'});
		   		
	$("#bouton_constantes_modifier").toggle(false);  
	$("#bouton_constantes_valider").toggle(false); 
	
		 $("#albumine, #sucre, #corpscetonique").attr( 'disabled' , true).css({'background':'#f8f8f8'});
	}, 2000);
	
	
	//********************* examen_donnee *****************************
	//********************* examen_donnee ***************************** 
	setTimeout(function(){
	var donnee1 = $("#examen_donnee1");
	var donnee2 = $("#examen_donnee2");
	var donnee3 = $("#examen_donnee3");
	var donnee4 = $("#examen_donnee4");
	var donnee5 = $("#examen_donnee5");
	
	donnee1.attr( 'disabled', true).css({'background':'#f8f8f8'});
	donnee2.attr( 'disabled', true).css({'background':'#f8f8f8'});
	donnee3.attr( 'disabled', true).css({'background':'#f8f8f8'});
	donnee4.attr( 'disabled', true).css({'background':'#f8f8f8'});
	donnee5.attr( 'disabled', true).css({'background':'#f8f8f8'});
	$("#bouton_donnee_modifier").toggle(false);
	$("#bouton_donnee_valider").toggle(false);
	
	$('#ajouter_donnee_img').toggle(false);
	$('#supprimer_donnee_img').toggle(false);
	
	$('.supprimerDonnee1, .supprimerDonnee2, .supprimerDonnee3, .supprimerDonnee4, .supprimerDonnee5').toggle(false);
	 }, 2000);
	  		
//********************* ANALYSE MORPHOLOGIQUE *****************************
//********************* ANALYSE MORPHOLOGIQUE *****************************
$(function(){
	setTimeout(function(){
		var radio = $("#radio");
		var ecographie = $("#ecographie");
		var fibrocospie = $("#fibrocospie");
		var scanner = $("#scanner");
		var irm = $("#irm");
	
		radio.attr( 'disabled', true).css({'background':'#f8f8f8'});
		ecographie.attr( 'disabled', true).css({'background':'#f8f8f8'});
		fibrocospie.attr( 'disabled', true).css({'background':'#f8f8f8'});
		scanner.attr( 'disabled', true).css({'background':'#f8f8f8'});
		irm.attr( 'disabled', true).css({'background':'#f8f8f8'});
	
		$("#bouton_morpho_modifier").toggle(false);
		$("#bouton_morpho_valider").toggle(false);
	}, 2000);
});

//********************* POUR LES DIAGNOSTICS *****************************
//********************* POUR LES DIAGNOSTICS *****************************
setTimeout(function(){
var diagnostic1 = $("#diagnostic1");
var diagnostic2 = $("#diagnostic2");
var diagnostic3 = $("#diagnostic3");
var diagnostic4 = $("#diagnostic4");

	diagnostic1.attr( 'disabled', true).css({'background':'#f8f8f8'});;
	diagnostic2.attr( 'disabled', true).css({'background':'#f8f8f8'});;
	diagnostic3.attr( 'disabled', true).css({'background':'#f8f8f8'});;
	diagnostic4.attr( 'disabled', true).css({'background':'#f8f8f8'});;
	//$("#bouton_diagnostic_modifier").toggle(false);
	//$("#bouton_diagnostic_valider").toggle(false);
	
	$('#ajouter_diagnostic_img').toggle(false);
	$('#supprimer_diagnostic_img').toggle(false);
	
	$('.supprimerDiag1, .supprimerDiag2, .supprimerDiag3, .supprimerDiag4').toggle(false);
}, 2000);

//********************* POUR LES DEMANDES D'EXAMENS *****************************
//********************* POUR LES DEMANDES D'EXAMENS ***************************** 
setTimeout(function(){
	//LES EXAMENS BIOLOGIQUES
	//LES EXAMENS BIOLOGIQUES
	$("#lesExamensBiologiques input, #lesExamensBiologiques select").attr( "disabled", true ).css({'background':'#f8f8f8'});
	
	$("#controls_examenBio div").toggle(false);
	$("#iconeExamenBio_supp_vider a img").toggle(false);
	$("#bouton_ExamenBio_modifier_demande").toggle(false);
	$("#bouton_ExamenBio_valider_demande").toggle(false);
	
	//LES EXAMENS MORPHOLOGIQUES
	//LES EXAMENS MORPHOLOGIQUES
	$("#lesElements input, #lesElements select, " +
	  "#AjoutImage input[name=fichier], " +
	  "#AjoutImageEchographie input[name=fichierEchographie], " +
	  "#AjoutImageIRM input[name=fichierIRM], " +
	  "#AjoutImageScanner input[name=fichierScanner], " +
	  "#AjoutImageFibroscopie input[name=fichierFibroscopie]").attr( "disabled", true ).css({'background':'#f8f8f8'});
	
	$(".confirmation").toggle(false);
	
	$("#controls_element div").toggle(false);
	$("#icone_supp_vider a img").toggle(false);
	//$("#bouton_morpho_modifier_demande").toggle(false);
	//$("#bouton_morpho_valider_demande").toggle(false);
	
},2000);
  
//********************* POUR LES MEDICAMENTS *****************************
//********************* POUR LES MEDICAMENTS ***************************** 
setTimeout(function(){
	$("#listeMedicaments input, .form-duree_ input, .ordonnance input").attr("disabled", true).css({'background':'#f8f8f8'});
	
	$("#controls_medicament div").toggle(false);
	$("#iconeMedicament_supp_vider a img").toggle(false);
	//$("#bouton_Medicament_modifier_demande").toggle(false);
	//$("#bouton_Medicament_valider_demande").toggle(false);
	$("#increm_decrem img").toggle(false);
}, 2000);

setTimeout(function(){ 
//********************* TRAITEMENTS CHIRURGICAUX *****************************
//********************* TRAITEMENTS CHIRURGICAUX ***************************** 
	$(".rendezvous input, .annulertransfert input").attr("disabled", true).css({'background':'#f8f8f8'});
	
	var diagnostic_traitement_chirurgical = $("#diagnostic_traitement_chirurgical");
	var intervention_prevue = $("#intervention_prevue");
	var observation = $("#observation");
	
	diagnostic_traitement_chirurgical.attr( 'disabled', true).css({'background':'#f8f8f8'});
	intervention_prevue.attr( 'disabled', true).css({'background':'#f8f8f8'});
	observation.attr( 'disabled', true).css({'background':'#f8f8f8'});

	$("#bouton_chirurgical_modifier").toggle(false);
	$("#bouton_chirurgical_valider").toggle(false);
	



//********************* TRAITEMENTS INSTRUMENTAUX *****************************
//********************* TRAITEMENTS INSTRUMENTAUX ***************************** 
//********************* TRAITEMENTS INSTRUMENTAUX ***************************** 
$(function(){
	var endoscopieInterventionnelle = $("#endoscopieInterventionnelle");
	var radiologieInterventionnelle = $("#radiologieInterventionnelle");
	var cardiologieInterventionnelle = $("#cardiologieInterventionnelle"); 
	var autresIntervention = $("#autresIntervention");
	
	endoscopieInterventionnelle.attr( 'disabled', true).css({'background':'#f8f8f8'});
	radiologieInterventionnelle.attr( 'disabled', true).css({'background':'#f8f8f8'});
	autresIntervention.attr( 'disabled', true).css({'background':'#f8f8f8'});
	cardiologieInterventionnelle.attr( 'disabled', true).css({'background':'#f8f8f8'});
		
	$("#bouton_instrumental_modifier").toggle(false);
	$("#bouton_instrumental_valider").toggle(false);
	
});
}, 2000);
// *************Autres(Transfert/Hospitalisation/ Rendez-Vous )***************
// *************Autres(Transfert/Hospitalisation/ Rendez-Vous )***************
// *************Autres(Transfert/Hospitalisation/ Rendez-Vous )***************
setTimeout(function(){
// ******************* Tranfert ******************************** 
// ******************* Tranfert ******************************** 
	$(".transf input, .annulertransfert input").attr("disabled", true).css({'background':'#f8f8f8'});
	var motif_transfert = $("#motif_transfert");
	var hopital_accueil = $("#hopital_accueil");
	var service_accueil = $("#service_accueil");
	
	motif_transfert.attr( 'disabled', true ).css({'background':'#f8f8f8'});    
	hopital_accueil.attr( 'disabled', true ).css({'background':'#f8f8f8'});    
	service_accueil.attr( 'disabled', true ).css({'background':'#f8f8f8'});   
	$("#bouton_transfert_modifier").toggle(false); 
	$("#bouton_transfert_valider").toggle(false); 

//********************* HOSPITALISATION *****************************
//********************* HOSPITALISATION *****************************
    $(".rendezvous input").attr("disabled", true).css({'background':'#f8f8f8'});
	var motif_hospitalisation = $("#motif_hospitalisation");
	var date_fin_hospitalisation_prevue = $("#date_fin_hospitalisation_prevue");
	
	motif_hospitalisation.attr( 'disabled', true).css({'background':'#f8f8f8'});
	date_fin_hospitalisation_prevue.attr( 'disabled', true).css({'background':'#f8f8f8'});
	
	$('.annulerhospitalisation input').attr('disabled', true);
	$("#bouton_hospi_modifier").toggle(false);
	$("#bouton_hospi_valider").toggle(false);
	
//********************* RENDEZ VOUS *****************************
//********************* RENDEZ VOUS *****************************
	$(".annulerrendezvous input, .rendezvous input").attr("disabled", true).css({'background':'#f8f8f8'});
	
	var motif_rv = $('#motif_rv');
	var date_rv = $( "#date_rv" );
	var heure_rv = $("#heure_rv");
	
	motif_rv.attr( 'disabled', true ).css({'background':'#f8f8f8'});     
	date_rv.attr( 'disabled', true ).css({'background':'#f8f8f8'});    
	heure_rv.attr( 'disabled', true ).css({'background':'#f8f8f8'});   
	$("#disable_bouton").toggle(false);  
	$("#enable_bouton").toggle(false); 
 
}, 2000);
  

	/****** ======================================================================= *******/
	/****** ======================================================================= *******/
	/****** ======================================================================= *******/
	/****** MASK DE SAISIE ********/ 
   	/****** MASK DE SAISIE ********/ 
 	/****** MASK DE SAISIE ********/
	function maskSaisie() {}
	
	/****** CONTROLE APRES VALIDATION ********/ 
	/****** CONTROLE APRES VALIDATION ********/ 

//-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*--*-*-*-*-*-*-*-*-*--*-*-*-*--*-*-*-*-*-**--*-**-*--**-*-*-*-*-*-*-*-*-*-*-*-*-*--**-*-*-*-*-	
	
	//Annuler le transfert au clic
	$("#annulertransfert").click(function() {
		$("#motif_transfert").val("");
		//document.getElementById('hopital_accueil').value="";
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
		document.getElementById('type_anesthesie_demande').value="";
		$("#numero_vpa").val("");
		$("#observation").val("");
		return false;
	});

 /**************************************************************************************************************/
 
 /*======================================== MENU ANTECEDENTS MEDICAUX =========================================*/
 
 /**************************************************************************************************************/
 function AntecedentScript(){
	 $(function(){
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
			
			
		 });

	 /*************************************************************************************************************/
	 
	 /*=================================== MENU ANTECEDENTS TERRAIN PARTICULIER ==================================*/
	 
	 /*************************************************************************************************************/
		 
	 $(function(){
		    //ANTECEDENTS PERSONNELS
			//ANTECEDENTS PERSONNELS
			$("#antecedentsPersonnels").toggle(false);
			$("#AntecedentsFamiliaux").toggle(false);
			$("#MenuAntecedentPersonnel").toggle(false);
			$("#HabitudesDeVie").toggle(false);
			$("#AntecedentMedicaux").toggle(false);
			$("#AntecedentChirurgicaux").toggle(false);
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
			
			//HABITUDES DE VIE
			//HABITUDES DE VIE
			$(".image1_AP").click(function(){
				 $("#MenuAntecedentPersonnel").fadeOut(function(){ 
					 $("#HabitudesDeVie").fadeIn("fast");
				 });
			});
			
			$("#TerminerHabitudeDeVie").click(function(){
				$("#HabitudesDeVie").fadeOut(function(){ 
					 $("#MenuAntecedentPersonnel").fadeIn("fast");
				 });
			});
			
			//ANTECEDENTS MEDICAUX
			//ANTECEDENTS MEDICAUX
			$(".image2_AP").click(function(){
				 $("#MenuAntecedentPersonnel").fadeOut(function(){ 
					 $("#AntecedentMedicaux").fadeIn("fast");
				 });
			});
			
			$("#TerminerAntecedentMedicaux").click(function(){
				$("#AntecedentMedicaux").fadeOut(function(){ 
					 $("#MenuAntecedentPersonnel").fadeIn("fast");
				 });
			});
			
			//ANTECEDENTS CHIRURGICAUX
			//ANTECEDENTS CHIRURGICAUX
			$(".image3_AP").click(function(){
				 $("#MenuAntecedentPersonnel").fadeOut(function(){ 
					 $("#AntecedentChirurgicaux").fadeIn("fast");
				 });
			});
			
			$("#TerminerAntecedentChirurgicaux").click(function(){
				$("#AntecedentChirurgicaux").fadeOut(function(){ 
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
			
			
			//HABITUDES DE VIE TESTER SI UNE HABITUDE EST COCHEE OU PAS
			//HABITUDES DE VIE TESTER SI UNE HABITUDE EST COCHEE OU PAS
			setTimeout(function(){
			 //HABITUDE DE VIE
			 $("#DateDebutAlcooliqueHV, #DateFinAlcooliqueHV," +
			   "#DateDebutFumeurHV, #DateFinFumeurHV, #nbPaquetFumeurHV," +
			   "#DateDebutDroguerHV, #DateFinDroguerHV," +
			   "#AlcooliqueHV, #FumeurHV, #DroguerHV, #AutresHV").attr( 'disabled' , true).css({'background':'#f8f8f8'});
			}, 1000);
			
			if(temoinAlcoolique != 1){
				$("#dateDebAlcoolique, #dateFinAlcoolique").toggle(false);
			}
			if(temoinFumeurHV != 1){
				$("#dateDebFumeur, #dateFinFumeur, #nbPaquetJour, #nbPaquetAnnee").toggle(false);
				$('#nbPaquetFumeurHV').val("");
				$('#nbPaquetAnnee').toggle(false);
			}else{
				if(nbPaquetFumeurHV != 0 ){
					var nbPaquetAnnee = nbPaquetFumeurHV*365;
					$("#nbPaquetAnnee label").html("<span style='font-weight: bold; color: green;'>"+nbPaquetAnnee+"</span> paquets/an");
				}else{
					$('#nbPaquetFumeurHV').val("");
					$('#nbPaquetAnnee').toggle(false);
				}
			}
			if(temoinDroguerHV != 1){
				$("#dateDebDroguer, #dateFinDroguer").toggle(false);
			}
			
			$("#DivNoteAutresHV").toggle(false);
			
			if($('#DateDebutAlcooliqueHV').val() == '00/00/0000'){ $('#DateDebutAlcooliqueHV').val("");}
			if($('#DateFinAlcooliqueHV').val() == '00/00/0000'){ $('#DateFinAlcooliqueHV').val("");}
			$('#HabitudesDeVie input[name=AlcooliqueHV]').click(function(){
				var boutons = $('#HabitudesDeVie input[name=AlcooliqueHV]');
				if( boutons[1].checked){ $("#dateDebAlcoolique, #dateFinAlcoolique").toggle(true); }
				if(!boutons[1].checked){ $("#dateDebAlcoolique, #dateFinAlcoolique").toggle(false); }
			});
			
			if($('#DateDebutFumeurHV').val() == '00/00/0000'){ $('#DateDebutFumeurHV').val("");}
			if($('#DateFinFumeurHV').val() == '00/00/0000'){ $('#DateFinFumeurHV').val("");}
			$('#HabitudesDeVie input[name=FumeurHV]').click(function(){
				var boutons = $('#HabitudesDeVie input[name=FumeurHV]');
				if( boutons[1].checked){ $("#dateDebFumeur, #dateFinFumeur, #nbPaquetJour, #nbPaquetAnnee").toggle(true); }
				if(!boutons[1].checked){ $("#dateDebFumeur, #dateFinFumeur, #nbPaquetJour, #nbPaquetAnnee").toggle(false); }
				if($('#nbPaquetFumeurHV').val() == ""){ $('#nbPaquetAnnee').toggle(false);} 
			});
			
			$('#nbPaquetFumeurHV').keyup(function(){
				var valeur = $('#nbPaquetFumeurHV').val();
				if(isNaN(valeur/1) || valeur > 10){
					$('#nbPaquetFumeurHV').val("");
					valeur = null;
				}
				if(valeur){
					var nbPaquetAnnee = valeur*365;
					$("#nbPaquetAnnee").toggle(true);
					$("#nbPaquetAnnee label").html("<span style='font-weight: bold; color: green;'>"+nbPaquetAnnee+"</span> paquets/an");
				}else{
					$("#nbPaquetAnnee").toggle(false);
				}
			}); 
			
			if($('#DateDebutDroguerHV').val() == '00/00/0000'){ $('#DateDebutDroguerHV').val("");}
			if($('#DateFinDroguerHV').val() == '00/00/0000'){ $('#DateFinDroguerHV').val("");}
			$('#HabitudesDeVie input[name=DroguerHV]').click(function(){
				var boutons = $('#HabitudesDeVie input[name=DroguerHV]');
				if( boutons[1].checked){ $("#dateDebDroguer, #dateFinDroguer").toggle(true); }
				if(!boutons[1].checked){ $("#dateDebDroguer, #dateFinDroguer").toggle(false); }
			});
			
			$('#HabitudesDeVie input[name=AutresHV]').click(function(){
				var boutons = $('#HabitudesDeVie input[name=AutresHV]');
				if( boutons[1].checked){ $("#DivNoteAutresHV").toggle(true); }
				if(!boutons[1].checked){ $("#DivNoteAutresHV").toggle(false); }
			});
			
			//ANTECEDENTS MEDICAUX TESTER SI C'EST COCHE
			//ANTECEDENTS MEDICAUX TESTER SI C'EST COCHE
			setTimeout(function(){
				 $("#DiabeteAM, #htaAM, #drepanocytoseAM, #dislipidemieAM, #asthmeAM, #autresAM").attr( 'disabled' , true).css({'background':'#f8f8f8'});
			}, 1000);
	 
			if(temoinDiabeteAM != 1){
				$(".imageValiderDiabeteAM").toggle(false);
			}
			if(temoinhtaAM != 1){
				$(".imageValiderHtaAM").toggle(false);
			}
			if(temoindrepanocytoseAM != 1){
				$(".imageValiderDrepanocytoseAM").toggle(false);
			}
			if(temoindislipidemieAM != 1){
				$(".imageValiderDislipidemieAM").toggle(false);
			}
			if(temoinasthmeAM != 1){
				$(".imageValiderAsthmeAM").toggle(false);
			}
			
			$('#AntecedentMedicaux input[name=DiabeteAM]').click(function(){
				var boutons = $('#AntecedentMedicaux input[name=DiabeteAM]');
				if( boutons[1].checked){ $(".imageValiderDiabeteAM").toggle(true); }
				if(!boutons[1].checked){ $(".imageValiderDiabeteAM").toggle(false); }
			});
			
			$('#AntecedentMedicaux input[name=htaAM]').click(function(){
				var boutons = $('#AntecedentMedicaux input[name=htaAM]');
				if( boutons[1].checked){ $(".imageValiderHtaAM").toggle(true); }
				if(!boutons[1].checked){ $(".imageValiderHtaAM").toggle(false); }
			});
			
			$('#AntecedentMedicaux input[name=drepanocytoseAM]').click(function(){
				var boutons = $('#AntecedentMedicaux input[name=drepanocytoseAM]');
				if( boutons[1].checked){ $(".imageValiderDrepanocytoseAM").toggle(true); }
				if(!boutons[1].checked){ $(".imageValiderDrepanocytoseAM").toggle(false); }
			});
			
			$('#AntecedentMedicaux input[name=dislipidemieAM]').click(function(){
				var boutons = $('#AntecedentMedicaux input[name=dislipidemieAM]');
				if( boutons[1].checked){ $(".imageValiderDislipidemieAM").toggle(true); }
				if(!boutons[1].checked){ $(".imageValiderDislipidemieAM").toggle(false); }
			});
			
			$('#AntecedentMedicaux input[name=asthmeAM]').click(function(){
				var boutons = $('#AntecedentMedicaux input[name=asthmeAM]');
				if( boutons[1].checked){ $(".imageValiderAsthmeAM").toggle(true); }
				if(!boutons[1].checked){ $(".imageValiderAsthmeAM").toggle(false); }
			});
			
			//GYNECO-OBSTETRIQUE TESTER SI C'EST COCHE
			//GYNECO-OBSTETRIQUE TESTER SI C'EST COCHE
			setTimeout(function(){
				 $("#MenarcheGO, #GestiteGO, #PariteGO, #CycleGO, #AutresGO, #AutresGO," +
				   "#NoteMenarcheGO, #NoteGestiteGO, #NotePariteGO, #DureeCycleGO, #RegulariteCycleGO, #DysmenorrheeCycleGO, #NoteAutresGO").attr( 'disabled' , true).css({'background':'#f8f8f8'});
			}, 1000);
			if(temoinMenarcheGO != 1){
				$("#NoteMonarche").toggle(false);
			}
			if(temoinGestiteGO != 1){
				$("#NoteGestite").toggle(false);
			}
			if(temoinPariteGO != 1){
				$("#NoteParite").toggle(false);
			}
			if(temoinCycleGO != 1){
				$("#RegulariteON, #DysmenorrheeON, #DureeGO").toggle(false);
			}
			$("#DivNoteAutresGO").toggle(false);
			
			$('#GynecoObstetrique input[name=MenarcheGO]').click(function(){
				var boutons = $('#GynecoObstetrique input[name=MenarcheGO]');
				if( boutons[1].checked){ $("#NoteMonarche").toggle(true); }
				if(!boutons[1].checked){ $("#NoteMonarche").toggle(false); }
			});
			
			$('#GynecoObstetrique input[name=GestiteGO]').click(function(){
				var boutons = $('#GynecoObstetrique input[name=GestiteGO]');
				if( boutons[1].checked){ $("#NoteGestite").toggle(true); }
				if(!boutons[1].checked){ $("#NoteGestite").toggle(false); }
			});
			
			$('#GynecoObstetrique input[name=PariteGO]').click(function(){
				var boutons = $('#GynecoObstetrique input[name=PariteGO]');
				if( boutons[1].checked){ $("#NoteParite").toggle(true); }
				if(!boutons[1].checked){ $("#NoteParite").toggle(false); }
			});
			
			$('#GynecoObstetrique input[name=CycleGO]').click(function(){
				var boutons = $('#GynecoObstetrique input[name=CycleGO]');
				if( boutons[1].checked){ $("#RegulariteON, #DysmenorrheeON, #DureeGO").toggle(true); }
				if(!boutons[1].checked){ $("#RegulariteON, #DysmenorrheeON, #DureeGO").toggle(false); }
			});
			
			$('#GynecoObstetrique input[name=AutresGO]').click(function(){
				var boutons = $('#GynecoObstetrique input[name=AutresGO]');
				if( boutons[1].checked){ $("#DivNoteAutresGO").toggle(true); }
				if(!boutons[1].checked){ $("#DivNoteAutresGO").toggle(false); }
			});
			
			//ANTECEDENTS FAMILIAUX TESTER SI C'EST COCHE
			//ANTECEDENTS FAMILIAUX TESTER SI C'EST COCHE
			setTimeout(function(){
				 $("#DiabeteAF, #NoteDiabeteAF, #DrepanocytoseAF, #NoteDrepanocytoseAF, " +
				   "#htaAF, #NoteHtaAF, #autresAF, #NoteAutresAF").attr( 'disabled' , true).css({'background':'#f8f8f8'});
			}, 1000);
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
	 });
}
 
 
 /***************************************************************************************/
 
 /**========================== PAGINATION INTERVENTION ================================**/
 
 /***************************************************************************************/

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
		function dep1(){
			$('#depliantBandelette').click(function(){
				$("#depliantBandelette").replaceWith("<img id='depliantBandelette' style='cursor: pointer; position: absolute; padding-right: 120px; margin-left: -5px;' src='../img/light/plus.png' />");
				dep();
			    $('#BUcheckbox').animate({
			        height : 'toggle'
			    },1000);
			 return false;
			});
		}
		
		function dep(){ 
			$('#depliantBandelette').click(function(){
				$("#depliantBandelette").replaceWith("<img id='depliantBandelette' style='cursor: pointer; position: absolute; padding-right: 120px; margin-left: -5px;' src='../img/light/minus.png' />");
				dep1();
			    $('#BUcheckbox').animate({
			        height : 'toggle'
			    },1000);
			 return false;
			});
		}
			
 
    //TESTER LEQUEL DES CHECKBOX est coché
	//TESTER LEQUEL DES CHECKBOX est coché
	//maskDeSaisie();
	OptionCochee();
	function OptionCochee() {
	$("#labelAlbumine").toggle(false);
	$("#labelSucre").toggle(false);
	$("#labelCorpscetonique").toggle(false);

	//AFFICHER SI C'EST COCHE
	//AFFICHER SI C'EST COCHE
	var boutonsAlbumine = $('#BUcheckbox input[name=albumine]');
	if(boutonsAlbumine[1].checked){ $("#labelAlbumine").toggle(true); }
	
	var boutonsSucre = $('#BUcheckbox input[name=sucre]');
	if(boutonsSucre[1].checked){ $("#labelSucre").toggle(true); }

	var boutonsCorps = $('#BUcheckbox input[name=corpscetonique]');
	if(boutonsCorps[1].checked){ $("#labelCorpscetonique").toggle(true); }

	}
	
	//CHOIX DU CROIX
	//========================================================
	$("#ChoixPlus").toggle(false);
	albumineOption();
	function albumineOption(){
		var boutons = $('#BUcheckbox input[name=croixalbumine]');
		if(boutons[0].checked){
			$("#labelCroixAlbumine").toggle(false); 
			$("#ChoixPlus").toggle(true); 
			$("#ChoixPlus label").html("1+");

		}
		if(boutons[1].checked){ 
			$("#labelCroixAlbumine").toggle(false); 
			$("#ChoixPlus").toggle(true); 
			$("#ChoixPlus label").html("2+");

		}
		if(boutons[2].checked){ 
			$("#labelCroixAlbumine").toggle(false); 
			$("#ChoixPlus").toggle(true); 
			$("#ChoixPlus label").html("3+");
			
		}
		if(boutons[3].checked){ 
			$("#labelCroixAlbumine").toggle(false); 
			$("#ChoixPlus").toggle(true); 
			$("#ChoixPlus label").html("4+");

		}
	}
	
	$('#BUcheckbox input[name=croixalbumine]').click(function(){
		//albumineOption();
	});

	//========================================================
	$("#ChoixPlus2").toggle(false);
	sucreOption();
	function sucreOption(){
		var boutons = $('#BUcheckbox input[name=croixsucre]');
		if(boutons[0].checked){
			$("#labelCroixSucre").toggle(false); 
			$("#ChoixPlus2").toggle(true); 
			$("#ChoixPlus2 label").html("1+");

		}
		if(boutons[1].checked){ 
			$("#labelCroixSucre").toggle(false); 
			$("#ChoixPlus2").toggle(true); 
			$("#ChoixPlus2 label").html("2+");

		}
		if(boutons[2].checked){ 
			$("#labelCroixSucre").toggle(false); 
			$("#ChoixPlus2").toggle(true); 
			$("#ChoixPlus2 label").html("3+");
			
		}
		if(boutons[3].checked){ 
			$("#labelCroixSucre").toggle(false); 
			$("#ChoixPlus2").toggle(true); 
			$("#ChoixPlus2 label").html("4+");

		}
	}
	$('#BUcheckbox input[name=croixsucre]').click(function(){
		//sucreOption();
	});

	//========================================================
	$("#ChoixPlus3").toggle(false);
	corpscetoniqueOption();
	function corpscetoniqueOption(){
		var boutons = $('#BUcheckbox input[name=croixcorpscetonique]');
		if(boutons[0].checked){
			$("#labelCroixCorpscetonique").toggle(false); 
			$("#ChoixPlus3").toggle(true); 
			$("#ChoixPlus3 label").html("1+");

		}
		if(boutons[1].checked){ 
			$("#labelCroixCorpscetonique").toggle(false); 
			$("#ChoixPlus3").toggle(true); 
			$("#ChoixPlus3 label").html("2+");

		}
		if(boutons[2].checked){ 
			$("#labelCroixCorpscetonique").toggle(false); 
			$("#ChoixPlus3").toggle(true); 
			$("#ChoixPlus3 label").html("3+");
			
		}
		if(boutons[3].checked){ 
			$("#labelCroixCorpscetonique").toggle(false); 
			$("#ChoixPlus3").toggle(true); 
			$("#ChoixPlus3 label").html("4+");

		}
	}
	$('#BUcheckbox input[name=croixcorpscetonique]').click(function(){
		//corpscetoniqueOption();
	});
	
	
	
	
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
            "       <a href='javascript:supprimerLabelAM("+ligne+","+i+");' ></a> "+ 
            "       <img class='imageValider_"+ligne+"_"+i+"'  style='cursor: pointer; margin-left: -15px;' src='"+tabUrl[0]+"public/images_icons/tick-icon2.png' /> "+  
            "    </span> "+
            nomLabel +"  <input type='checkbox' disabled='true' checked='${this.checked}' name='champValider_"+ligne+"_"+i+"' id='champValider_"+ligne+"_"+i+"' > "+
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
  	
  	