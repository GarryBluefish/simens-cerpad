var base_url = window.location.toString();
var tabUrl = base_url.split("public");
	
var saveStatOption1 = 0;
var saveStatOption2 = 0;
var saveStatOption3 = 0;
var saveStatOption4 = 0;

function initialisation (){
	//GESTION DE LA PAGE INFOS 1
	//GESTION DE LA PAGE INFOS 1
	$('#menuOption1').click(function(){
		$('#menuGeneral').fadeOut(function(){
			$('#menu_infos').html('INFOS STATISTIQUES');
			$('#contenuPageA').fadeIn();
		});
	});

	$('#retourPageAMenuInfos').click(function(){
		if(saveStatOption1 == 1){
			//vart = tabUrl[0]+'public/facturation/informations-statistiques';
		    //$(location).attr("href",vart);
		}else{ 
			$('#contenuPageA').fadeOut(function(){
				$('#menu_infos').html('MENU');
				$('#menuGeneral').fadeIn();
			});
		}
	});

	//GESTION DE LA PAGE INFOS 2
	//GESTION DE LA PAGE INFOS 2
	$('#menuOption2').click(function(){
		$('#menuGeneral').fadeOut(function(){
			$('#menu_infos').html('AUTRES INFOS (concerne les d&eacute;pistages valid&eacute;s)');
			$('#contenuPageB').fadeIn();
		});
	});

	$('#retourPageBMenuInfos').click(function(){
		if(saveStatOption2 == 1){
			//vart = tabUrl[0]+'public/facturation/informations-statistiques';
		    //$(location).attr("href",vart);
		}else{
			$('#contenuPageB').fadeOut(function(){
				$('#menu_infos').html('MENU');
				$('#menuGeneral').fadeIn();
				//$('#iconeInfosPremiereIntervention').css({'visibility' : 'hidden'});
			});
		}
	});
	
	
	
	//GESTION DE LA PAGE INFOS 3
	//GESTION DE LA PAGE INFOS 3
	$('#menuOption3').click(function(){
		$('#menuGeneral').fadeOut(function(){
			$('#menu_infos').html('INFOS STATISTIQUES OPTIONNELLES');
			$('#contenuPageC').fadeIn();
		});
	});
	
	$('#retourPageCMenuInfos').click(function(){
		if(saveStatOption3 == 1){
			//vart = tabUrl[0]+'public/facturation/informations-statistiques';
		    //$(location).attr("href",vart);
		}else{
			$('#contenuPageC').fadeOut(function(){
				$('#menu_infos').html('MENU');
				$('#menuGeneral').fadeIn();
				//$('#iconeInfosPremiereIntervention').css({'visibility' : 'hidden'});
			});
		}
	});
	

	//GESTION DE LA PAGE INFOS 4
	//GESTION DE LA PAGE INFOS 4
	$('#menuOption4').click(function(){
		$('#menuGeneral').fadeOut(function(){
			$('#menu_infos').html('CARTE INTERACTIVE');
			$('#contenuPageD').fadeIn();
		});
	});
	
	$('#retourPageDMenuInfos').click(function(){
		if(saveStatOption4 == 1){
			//vart = tabUrl[0]+'public/facturation/informations-statistiques';
		    //$(location).attr("href",vart);
		}else{
			$('#contenuPageD').fadeOut(function(){
				$('#menu_infos').html('MENU');
				$('#menuGeneral').fadeIn();
				//$('#iconeInfosPremiereIntervention').css({'visibility' : 'hidden'});
			});
		}
	});
	
	
	
	
	
	
	
	
	

	/*
	infosStatistiquesRapport();
	$('#date_debut_rapport, #date_fin_rapport').change(function(){
		var date_debut_rapport = $('#date_debut_rapport').val();
		var date_fin_rapport = $('#date_fin_rapport').val();
		
		if(date_debut_rapport && date_fin_rapport){
			$('.boutonAfficherInfosInervalleDateIntervention img').toggle(true);
		}else{
			$('.boutonAfficherInfosInervalleDateIntervention img').toggle(false);
		}
	}).keyup(function(){
		var date_debut_rapport = $('#date_debut_rapport').val();
		var date_fin_rapport = $('#date_fin_rapport').val();
		
		if(date_debut_rapport && date_fin_rapport){
			$('.boutonAfficherInfosInervalleDateIntervention img').toggle(true);
		}else{
			$('.boutonAfficherInfosInervalleDateIntervention img').toggle(false);
		}
	});
	*/


	
	
	/*
	$('#id_medecin, #age_min, #age_max').attr('disabled', true);
	$('#visualiserResultatParAge').click(function(){
		getInformationsStatistiquesParAge();
	});
	$('#visualiserResultatParDateIntervention').click(function(){
		getInformationsStatistiquesParDateIntervention();
	});
	
	
	$('#iconeReinitialiserAge').click(function(){
		getFonctionReinitialisationAge();
	});
	$('#iconeReinitialiserDateIntervention').click(function(){
		getFonctionReinitialisationDateIntervention();
	});
	*/
}






























/*
//GESTION DE L'AFFICHAGE DES STATISTIQUES LORS DE LA SELECTION D'UN SERVICE
//GESTION DE L'AFFICHAGE DES STATISTIQUES LORS DE LA SELECTION D'UN SERVICE
function getInformationsService(id_service){ 
	$('.affichageResultatOptionsChoisi div').html('<table> <tr> <td style="padding-top: 60px;"> Chargement </td> </tr>  <tr> <td align="center"> <img style="margin-top: 20px; width: 70px; height: 70px;" src="../images/loading/Chargement_1.gif" /> </td> </tr> </table>');
	$('#titreResultatOptionChoisi').html("");
	$('#tableauResultatOptionChoisi').html("");
	getInformationsStatistiquesOptionnelles(id_service);
}

function getInformationsStatistiquesOptionnelles(id_service){
	
    $.ajax({
        type: 'POST',
        url: tabUrl[0]+'public/facturation/get-informations-statistique-optionnelles' ,
        data: {'id_service': id_service},
        success: function(data) {    
        	var result = jQuery.parseJSON(data);  
  
        	$('.affichageResultatOptionsChoisi div').html('');
        	
        	$('#affichageResultatOptionsChoisiScript').html(result[0]);
        	$('#tableauResultatOptionChoisi').html(result[1]);
        	$('#titreResultatOptionChoisi').html(result[2]);

        	//INITIALISATION DES CHAMPS DATE_DEBUT et DATE_FIN
        	$('#visualiserResultatParDateIntervention').toggle(false);
    		$('#date_debut, #date_fin').val(''); 
    		$('#iconeReinitialiserDateIntervention').css({'visibility':'hidden'});
    		
    		//INITIALISATION DU CHAMPS DES DIAGNOSTICS
    		$('#diagnostic').val(0);
        	return false;
        }
    
    });

}


function informationsOptionnelles(PileOPS) {
	var affichageResultatOptionsChoisi = new CanvasJS.Chart("affichageResultatOptionsChoisi", {
		data: [{
			type: "column",
			dataPoints: PileOPS
		}]

	});
	
	affichageResultatOptionsChoisi.render();
}

function nombrePatientsParServiceOp(PileOPS) {
	var nombrePatientsParServiceOp = new CanvasJS.Chart("affichageResultatOptionsChoisi", {
		data: [{
			type: "bar",
			dataPoints: PileOPS
		}]

	});
	nombrePatientsParServiceOp.render();
}

//GESTION DE L'AFFICHAGE DES STATISTIQUES LORS DE LA SELECTION D'UN MEDECIN
//GESTION DE L'AFFICHAGE DES STATISTIQUES LORS DE LA SELECTION D'UN MEDECIN
function getInformationsMedecin(id_medecin) {
	$('.affichageResultatOptionsChoisi div').html('<table> <tr> <td style="padding-top: 60px;"> Chargement </td> </tr>  <tr> <td align="center"> <img style="margin-top: 20px; width: 70px; height: 70px;" src="../images/loading/Chargement_1.gif" /> </td> </tr> </table>');
	$('#titreResultatOptionChoisi').html("");
	$('#tableauResultatOptionChoisi').html("");
	getInformationsStatistiquesSurLeMedecin(id_medecin);
}

function getInformationsStatistiquesSurLeMedecin(id_medecin){ 
	var id_service = $('#id_service').val();
	
    $.ajax({
        type: 'POST',
        url: tabUrl[0]+'public/facturation/get-informations-statistique-medecin' ,
        data: {'id_medecin': id_medecin, 'id_service':id_service},
        success: function(data) {    
        	var result = jQuery.parseJSON(data);  
  
        	$('.affichageResultatOptionsChoisi div').html('');
        	
        	$('#affichageResultatOptionsChoisiScript').html(result[0]);
        	$('#tableauResultatOptionChoisi').html(result[1]);
        	$('#titreResultatOptionChoisi').html(result[2]);
        	
        	//INITIALISATION DES CHAMPS DATE_DEBUT et DATE_FIN
        	$('#visualiserResultatParDateIntervention').toggle(false);
    		$('#date_debut, #date_fin').val(''); 
    		$('#iconeReinitialiserDateIntervention').css({'visibility':'hidden'});
    		
    		//INITIALISATION DU CHAMPS DES DIAGNOSTICS
    		$('#diagnostic').val(0);
    		
    		return false;
        }
    
    });

}

function nombrePatientsDifferentSexePourLeMedecin(nbPatientF, nbPatientM){
	var nombrePatientsDifferentSexePourLeMedecin = new CanvasJS.Chart("affichageResultatOptionsChoisi", {
		data: [{
			type: "pie",
			dataPoints: [

				{ y: nbPatientF, label: "Feminin" },
				{ y: nbPatientM, label: "Masculin" },
			]
		}]

	});
	nombrePatientsDifferentSexePourLeMedecin.render();
}

function nombrePatientsSexeMasculinPourLeMedecin(nbPatientM){
	var nombrePatientsDifferentSexePourLeMedecin = new CanvasJS.Chart("affichageResultatOptionsChoisi", {
		data: [{
			type: "pie",
			dataPoints: [
				{ y: nbPatientM, label: "Masculin" },
			]
		}]

	});
	nombrePatientsDifferentSexePourLeMedecin.render();
}

function nombrePatientsSexeFemininPourLeMedecin(nbPatientF){
	var nombrePatientsDifferentSexePourLeMedecin = new CanvasJS.Chart("affichageResultatOptionsChoisi", {
		data: [{
			type: "pie",
			dataPoints: [
				{ y: nbPatientF, label: "Feminin" },
			]
		}]

	});
	nombrePatientsDifferentSexePourLeMedecin.render();
}


//AFFICHAGE STATISTIQUES LORS DE LA SELECTION DES AGES
//AFFICHAGE STATISTIQUES LORS DE LA SELECTION DES AGES
function getListeAgeMin(val){
	val  = parseInt(val);
	val2 = parseInt($('#age_max').val());
	
	if( !isNaN(val) && val > 0 && val < 151 ){
		$('#age_max').attr({'disabled':false, 'min':val}); 
	}else{ 
		$('#age_max').val("");
		$('#age_max').attr('disabled', true); 
		$('#visualiserResultatParAge').toggle(false);
	}
	
	if( !isNaN(val2) && val > 0 && val < 151 ){ 
		if(val >= val2){ 
			$('#age_max').val(val); 
		}
	}
}

function getListeAgeMax(val){ 
	val = parseInt(val);
	val2 = parseInt($('#age_min').val());
	
	if( !isNaN(val) && val > 0 && val < 151 ){ 
	    if(val < val2){
	    	$('#age_max').attr({'min':val})
	    	$('#age_min').attr({'min':1}).val(val)
	    }
	    $('#visualiserResultatParAge').fadeIn(true); 
	    $('#iconeReinitialiserAge').css({'visibility':'visible'});
	}else{ 
		$('#visualiserResultatParAge').toggle(false); 
	}
	
}

function getInformationsStatistiquesParAge(){

	var id_medecin = $('#id_medecin').val();
	var age_min = parseInt($('#age_min').val());
	var age_max = parseInt($('#age_max').val());

	
	if(age_min <= age_max){
		$('.affichageResultatOptionsChoisi div').html('<table> <tr> <td style="padding-top: 60px;"> Chargement </td> </tr>  <tr> <td align="center"> <img style="margin-top: 20px; width: 70px; height: 70px;" src="../images/loading/Chargement_1.gif" /> </td> </tr> </table>');
		$('#visualiserResultatParAge').toggle(false);
		$('#age_max, #age_min').attr('disabled', true); 
		$('#titreResultatOptionChoisi').html("");
		$('#tableauResultatOptionChoisi').html("");
		$('#iconeReinitialiserAge').css({'visibility':'hidden'});
		
		//EFFACER LES CHAMPS DES INTERVALLES DES DATES 
		$('#visualiserResultatParDateIntervention').toggle(false);
		$('#iconeReinitialiserDateIntervention').css({'visibility':'hidden'});
		$('#date_debut, #date_fin').val(''); 
		
		$.ajax({
	        type: 'POST',
	        url: tabUrl[0]+'public/facturation/get-informations-statistique-age' ,
	        data: {'id_medecin': id_medecin, 'age_min':age_min, 'age_max':age_max},
	        success: function(data) {    
	        	var result = jQuery.parseJSON(data);  
	  
	        	$('.affichageResultatOptionsChoisi div').html('');
	        	
	        	$('#affichageResultatOptionsChoisiScript').html(result[0]);
	        	$('#tableauResultatOptionChoisi').html(result[1]);
	        	$('#titreResultatOptionChoisi').html(result[2]);

	        	$('#visualiserResultatParAge').toggle(true);
	    		$('#age_max, #age_min').attr('disabled', false); 
	    		
	    		$('#iconeReinitialiserAge').css({'visibility':'visible'});
	    		
	    		//INITIALISATION DU CHAMPS DES DIAGNOSTICS
	    		$('#diagnostic').val(0);
	        }
	    
	    });
		
	}else{
		$('#visualiserResultatParAge').toggle(false);
		alert("age max doit etre superieur ou egal a age min");
	}

};

function getFonctionReinitialisationAge(){
	var id_medecin = $('#id_medecin').val();
	$('#iconeReinitialiserAge').css({'visibility':'hidden'});
	getInformationsMedecin(id_medecin);
}


//AFFICHAGE STATISTIQUES LORS DE LA SELECTION DES DATES
//AFFICHAGE STATISTIQUES LORS DE LA SELECTION DES DATES
function getListeDateDebut(infos){
	
	var date_debut = document.getElementById('date_debut');
	var date_fin = document.getElementById('date_fin');
	
	if(!date_debut.validity.valid){ 
		$('#visualiserResultatParDateIntervention').toggle(false);
		$('#ValidationInformation').trigger('click'); 
	}
	else{
		if(date_fin.validity.valid){
			if(date_debut.value > date_fin.value){
				date_fin.value = date_debut.value;
			}
			$('#visualiserResultatParDateIntervention').toggle(true);
			$('#iconeReinitialiserDateIntervention').css({'visibility':'visible'});
		}else{
			$('#visualiserResultatParDateIntervention').toggle(false);
		}
	}
	
}


function getListeDateFin(infos){

	var date_fin = document.getElementById('date_fin');
	var date_debut = document.getElementById('date_debut');
	
	if(!date_fin.validity.valid){ 
		$('#visualiserResultatParDateIntervention').toggle(false);
		$('#ValidationInformation').trigger('click'); 
	}
	else{
		if(date_debut.validity.valid){ 
			if(date_debut.value > date_fin.value){
				date_debut.value = date_fin.value;
			}
			$('#visualiserResultatParDateIntervention').toggle(true);
			$('#iconeReinitialiserDateIntervention').css({'visibility':'visible'});
		}else{
			$('#visualiserResultatParDateIntervention').toggle(false);
		}
	}
}

var entreeReinit;
function getInformationsStatistiquesParDateIntervention(){

	var id_service = $('#id_service').val();
	var id_medecin = $('#id_medecin').val();
	var age_min = parseInt($('#age_min').val());
	var age_max = parseInt($('#age_max').val());
	var date_debut = $("#date_debut").val();
	var date_fin = $('#date_fin').val();

	//1---))) POUR LA GESTION DU CAS SDI ( SERVICE - DATE_INTERVENTION ) 
	//1---))) POUR LA GESTION DU CAS SDI ( SERVICE - DATE_INTERVENTION ) 
	//**** Tous les médecins de tous les services confondus
	//**** id_service == 0
	if(id_service == 0){
		entreeReinit = 1;
		getInformationsSDI(date_debut, date_fin); 
	}
	//2---))) POUR LA GESTION DU CAS SDI ( SERVICE - DATE_INTERVENTION ) 
	//2---))) POUR LA GESTION DU CAS SDI ( SERVICE - DATE_INTERVENTION ) 
	//**** Tous les médecins du service sélectionné
	//**** id_service != 0 && id_medecin == 0
	else
		if(id_service != 0 && id_medecin == 0){
			entreeReinit = 2;
			getInformationsSDIM(id_service, date_debut, date_fin); 
		}
	//3---))) POUR LA GESTION DU CAS SMDI ( SERVICE - MEDECIN - DATE_INTERVENTION ) (même chose que MDI ( MEDECIN DATE_INTERVENTION ) )
	//3---))) POUR LA GESTION DU CAS SMDI ( SERVICE - MEDECIN - DATE_INTERVENTION ) (même chose que MDI ( MEDECIN DATE_INTERVENTION ) )
	//**** Tous les médecins du service sélectionné pour récupérer les patients de tous les ages
	//**** id_service != 0 && id_medecin != 0 && AGE == 0
		else
			if(id_service != 0 && id_medecin != 0 && isNaN(age_max)){
				entreeReinit = 3;
				getInformationsSMDI(id_medecin, date_debut, date_fin);
			}
	//4---))) *** POUR LA GESTION DU CAS SMADI ( SERVICE - MEDECIN - AGE - DATE_INTERVENTION )
	//4---))) *** POUR LA GESTION DU CAS SMADI ( SERVICE - MEDECIN - AGE - DATE_INTERVENTION )
	//**** Tous les médecins du service sélectionné pour récupérer les patients de l'intervalle d'ages choisi
	//**** id_service != 0 && id_medecin != 0 && AGE != 0
			else
				if(id_service != 0 && id_medecin != 0 && !isNaN(age_max)){
					entreeReinit = 4;
					getInformationsSMADI(id_medecin, age_min, age_max, date_debut, date_fin);
				}
	
}


function getFonctionReinitialisationDateIntervention(){
	var id_service = $('#id_service').val();
	var id_medecin = $('#id_medecin').val();
	
	if(entreeReinit == 1){
		getInformationsService(id_service);
	}else
		if(entreeReinit == 2){
			getInformationsMedecin(0);
		}else 
			if(entreeReinit == 3){
				getInformationsMedecin(id_medecin);
			}else
				if(entreeReinit == 4){
					getInformationsStatistiquesParAge();
				}
	
	$('#visualiserResultatParDateIntervention').toggle(false);
	$('#iconeReinitialiserDateIntervention').css({'visibility':'hidden'});
	$('#date_debut, #date_fin').val("");

}


//GESTION DU PREMIER CAS
//GESTION DU PREMIER CAS
//GESTION DU PREMIER CAS
function getInformationsSDI(date_debut, date_fin){
	
	$('.affichageResultatOptionsChoisi div').html('<table> <tr> <td style="padding-top: 60px;"> Chargement </td> </tr>  <tr> <td align="center"> <img style="margin-top: 20px; width: 70px; height: 70px;" src="../images/loading/Chargement_1.gif" /> </td> </tr> </table>');
	$('#visualiserResultatParDateIntervention').toggle(false);
	$('#date_debut, #date_fin').attr('disabled', true); 
	$('#titreResultatOptionChoisi').html("");
	$('#tableauResultatOptionChoisi').html("");
	$('#iconeReinitialiserDateIntervention').css({'visibility':'hidden'});
	
	$.ajax({
        type: 'POST',
        url: tabUrl[0]+'public/facturation/get-informations-statistique-date-intervention' ,
        data: {'date_debut': date_debut, 'date_fin':date_fin },
        success: function(data) {    
        	var result = jQuery.parseJSON(data);  
	
        	$('.affichageResultatOptionsChoisi div').html('');
  	
        	//AFFICHAGE DES RESULTATS
        	$('#affichageResultatOptionsChoisiScript').html(result[0]);
        	$('#tableauResultatOptionChoisi').html(result[1]);
        	$('#titreResultatOptionChoisi').html(result[2]);

        	$('#visualiserResultatParDateIntervention').toggle(true);
        	$('#date_debut, #date_fin').attr('disabled', false); 
        	$('#iconeReinitialiserDateIntervention').css({'visibility':'visible'});
        	
        	//INITIALISATION DU CHAMPS DES DIAGNOSTICS
    		$('#diagnostic').val(0);
        	
        }
    
    });

}

//GESTION DU DEUXIEME CAS
//GESTION DU DEUXIEME CAS
//GESTION DU DEUXIEME CAS
function getInformationsSDIM(id_service, date_debut, date_fin){
	
	$('.affichageResultatOptionsChoisi div').html('<table> <tr> <td style="padding-top: 60px;"> Chargement </td> </tr>  <tr> <td align="center"> <img style="margin-top: 20px; width: 70px; height: 70px;" src="../images/loading/Chargement_1.gif" /> </td> </tr> </table>');
	$('#visualiserResultatParDateIntervention').toggle(false);
	$('#date_debut, #date_fin').attr('disabled', true); 
	$('#titreResultatOptionChoisi').html("");
	$('#tableauResultatOptionChoisi').html("");
	$('#iconeReinitialiserDateIntervention').css({'visibility':'hidden'});
	
	$.ajax({
        type: 'POST',
        url: tabUrl[0]+'public/facturation/get-informations-statistique-service-medecin-date-intervention' ,
        data: {'id_service': id_service, 'date_debut': date_debut, 'date_fin':date_fin },
        success: function(data) {    
        	var result = jQuery.parseJSON(data);  
	
        	$('.affichageResultatOptionsChoisi div').html('');
  	
        	//AFFICHAGE DES RESULTATS
        	$('#affichageResultatOptionsChoisiScript').html(result[0]);
        	$('#tableauResultatOptionChoisi').html(result[1]);
        	$('#titreResultatOptionChoisi').html(result[2]);

        	$('#visualiserResultatParDateIntervention').toggle(true);
        	$('#date_debut, #date_fin').attr('disabled', false); 
        	$('#iconeReinitialiserDateIntervention').css({'visibility':'visible'});
        	
        	$('#diagnostic').val(0);
        }
    
    });

}

//GESTION DU TROISIEME CAS
//GESTION DU TROISIEME CAS
//GESTION DU TROISIEME CAS
function getInformationsSMDI(id_medecin, date_debut, date_fin){
	
	$('.affichageResultatOptionsChoisi div').html('<table> <tr> <td style="padding-top: 60px;"> Chargement </td> </tr>  <tr> <td align="center"> <img style="margin-top: 20px; width: 70px; height: 70px;" src="../images/loading/Chargement_1.gif" /> </td> </tr> </table>');
	$('#visualiserResultatParDateIntervention').toggle(false);
	$('#date_debut, #date_fin').attr('disabled', true); 
	$('#titreResultatOptionChoisi').html("");
	$('#tableauResultatOptionChoisi').html("");
	$('#iconeReinitialiserDateIntervention').css({'visibility':'hidden'});
	
	$.ajax({
        type: 'POST',
        url: tabUrl[0]+'public/facturation/get-informations-statistique-service-par-medecin-date-intervention' ,
        data: {'id_medecin': id_medecin, 'date_debut': date_debut, 'date_fin':date_fin },
        success: function(data) {    
        	var result = jQuery.parseJSON(data);  
	
        	$('.affichageResultatOptionsChoisi div').html('');
  	
        	//AFFICHAGE DES RESULTATS
        	$('#affichageResultatOptionsChoisiScript').html(result[0]);
        	$('#tableauResultatOptionChoisi').html(result[1]);
        	$('#titreResultatOptionChoisi').html(result[2]);

        	$('#visualiserResultatParDateIntervention').toggle(true);
        	$('#date_debut, #date_fin').attr('disabled', false); 
        	$('#iconeReinitialiserDateIntervention').css({'visibility':'visible'});
        	
        }
    
    });
	
}



//GESTION DU QUATRIEME CAS
//GESTION DU QUATRIEME CAS
//GESTION DU QUATRIEME CAS
function getInformationsSMADI(id_medecin, age_min, age_max, date_debut, date_fin){
	
	$('.affichageResultatOptionsChoisi div').html('<table> <tr> <td style="padding-top: 60px;"> Chargement </td> </tr>  <tr> <td align="center"> <img style="margin-top: 20px; width: 70px; height: 70px;" src="../images/loading/Chargement_1.gif" /> </td> </tr> </table>');
	$('#visualiserResultatParDateIntervention').toggle(false);
	$('#date_debut, #date_fin').attr('disabled', true); 
	$('#titreResultatOptionChoisi').html("");
	$('#tableauResultatOptionChoisi').html("");
	$('#iconeReinitialiserDateIntervention').css({'visibility':'hidden'});
	//DESACTIVATION DES ICONES POUR LES CHAMPS AGES
	$('#visualiserResultatParAge').toggle(false);
	$('#age_max, #age_min').attr('disabled', true); 
	$('#iconeReinitialiserAge').css({'visibility':'hidden'});
	
	$.ajax({
        type: 'POST',
        url: tabUrl[0]+'public/facturation/get-informations-statistique-service-par-medecin-age-date-intervention' ,
        data: {'id_medecin': id_medecin, 'age_min':age_min, 'age_max':age_max, 'date_debut':date_debut, 'date_fin':date_fin },
        success: function(data) {    
        	var result = jQuery.parseJSON(data);  
	
        	$('.affichageResultatOptionsChoisi div').html('');
  	
        	//AFFICHAGE DES RESULTATS
        	$('#affichageResultatOptionsChoisiScript').html(result[0]);
        	$('#tableauResultatOptionChoisi').html(result[1]);
        	$('#titreResultatOptionChoisi').html(result[2]);

        	$('#visualiserResultatParDateIntervention').toggle(true);
        	$('#date_debut, #date_fin').attr('disabled', false); 
        	$('#iconeReinitialiserDateIntervention').css({'visibility':'visible'});
        	
        	//ACTIVATION DES ICONES POUR LES CHAMPS AGES
        	$('#visualiserResultatParAge').toggle(true);
        	$('#age_max, #age_min').attr('disabled', false); 
        	$('#iconeReinitialiserAge').css({'visibility':'visible'});
        }
    
    });
	
}


//AFFICHAGE DES STATISTIQUES SUR LES DIAGNOSTICS 
//AFFICHAGE DES STATISTIQUES SUR LES DIAGNOSTICS 
function getListeDiagnostic(val){
	var id_service = $('#id_service').val();
	var id_medecin = $('#id_medecin').val();
	var age_min = parseInt($('#age_min').val());
	var age_max = parseInt($('#age_max').val());
	var date_debut = $("#date_debut").val();
	var date_fin = $('#date_fin').val();
	var diagnostic = $('#diagnostic').val();

	//1---))) POUR LA GESTION DU CAS SDiag ( SERVICE - DIAGNOSTIC ) 
	//1---))) POUR LA GESTION DU CAS SDiag ( SERVICE - DIAGNOSTIC ) 
	//**** Tous les médecins de tous les services confondus
	//**** id_service == 0
	if(id_service == 0){
		if(date_debut == "" || date_fin == ""){
			getInformationsSDiag(diagnostic); 
		}else
			if(date_debut != "" && date_fin != ""){
				getInformationsSDiagDateIntervention(diagnostic, date_debut, date_fin);
			}
	}
	//2---))) POUR LA GESTION DU CAS SMDiag ( SERVICE - MEDECIN - DIAGNOSTIC ) 
	//2---))) POUR LA GESTION DU CAS SMDiag ( SERVICE - MEDECIN - DIAGNOSTIC ) 
	//**** Tous les médecins du service sélectionné
	//**** id_service != 0 && id_medecin == 0
	else
		if(id_service != 0 && id_medecin == 0){
			if(date_debut == "" || date_fin == ""){
				getInformationsSDiagService(id_service, diagnostic);
			}else
				if(date_debut != "" && date_fin != ""){
					alert("entrer 4 - en cours de developpement");
					$('#diagnostic').val('');
				}
		}
	//3---))) POUR LA GESTION DU CAS SMDI ( SERVICE - MEDECIN - DATE_INTERVENTION ) (même chose que MDI ( MEDECIN DATE_INTERVENTION ) )
	//3---))) POUR LA GESTION DU CAS SMDI ( SERVICE - MEDECIN - DATE_INTERVENTION ) (même chose que MDI ( MEDECIN DATE_INTERVENTION ) )
	//**** Tous les médecins du service sélectionné pour récupérer les patients de tous les ages
	//**** id_service != 0 && id_medecin != 0 && AGE == 0
		else
			if(id_service != 0 && id_medecin != 0 && isNaN(age_max)){
				if(date_debut == "" || date_fin == ""){
					alert("entrer 5 - en cours de developpement");
					$('#diagnostic').val('');
				}else
					if(date_debut != "" && date_fin != ""){
						alert("entrer 6 - en cours de developpement");
						$('#diagnostic').val('');
					}
			}
	//4---))) *** POUR LA GESTION DU CAS SMADI ( SERVICE - MEDECIN - AGE - DATE_INTERVENTION )
	//4---))) *** POUR LA GESTION DU CAS SMADI ( SERVICE - MEDECIN - AGE - DATE_INTERVENTION )
	//**** Tous les médecins du service sélectionné pour récupérer les patients de l'intervalle d'ages choisi
	//**** id_service != 0 && id_medecin != 0 && AGE != 0
			else
				if(id_service != 0 && id_medecin != 0 && !isNaN(age_max)){
					if(date_debut == "" || date_fin == ""){
						alert("entrer 7 - en cours de developpement");
						$('#diagnostic').val('');
					}else
						if(date_debut != "" && date_fin != ""){
							alert("entrer 8 - en cours de developpement");
							$('#diagnostic').val('');
						}
				}
	
}

//PREMIER CAS -- PREMIER CAS -- PREMIER CAS
function getInformationsSDiag(diagnostic){
	
	$('.affichageResultatOptionsChoisi div').html('<table> <tr> <td style="padding-top: 60px;"> Chargement </td> </tr>  <tr> <td align="center"> <img style="margin-top: 20px; width: 70px; height: 70px;" src="../images/loading/Chargement_1.gif" /> </td> </tr> </table>');
	$('#visualiserResultatParDateIntervention').toggle(false);
	$('#date_debut, #date_fin').attr('disabled', true); 
	$('#titreResultatOptionChoisi').html("");
	$('#tableauResultatOptionChoisi').html("");
	$('#iconeReinitialiserDateIntervention').css({'visibility':'hidden'});
	//DESACTIVATION DES ICONES POUR LES CHAMPS AGES
	$('#visualiserResultatParAge').toggle(false);
	$('#age_max, #age_min').attr('disabled', true); 
	$('#iconeReinitialiserAge').css({'visibility':'hidden'});
	
	$.ajax({
        type: 'POST',
        url: tabUrl[0]+'public/facturation/get-informations-statistique-service-par-diagnostic' ,
        data: {'diagnostic': diagnostic },
        success: function(data) {    
        	var result = jQuery.parseJSON(data);  
	
        	$('.affichageResultatOptionsChoisi div').html('');
  	
        	//AFFICHAGE DES RESULTATS
        	$('#affichageResultatOptionsChoisiScript').html(result[0]);
        	$('#tableauResultatOptionChoisi').html(result[1]);
        	$('#titreResultatOptionChoisi').html(result[2]);

        	//$('#visualiserResultatParDateIntervention').toggle(true);
        	$('#date_debut, #date_fin').val('').attr('disabled', false); 
        	//$('#iconeReinitialiserDateIntervention').css({'visibility':'visible'});
        	
        	//ACTIVATION DES ICONES POUR LES CHAMPS AGES
        	//$('#visualiserResultatParAge').toggle(true);
        	//$('#age_max, #age_min').attr('disabled', false); 
        	//$('#iconeReinitialiserAge').css({'visibility':'visible'});
        }
    
    });
	
}

//DEUXIEME CAS -- DEUXIEME CAS -- DEUXIEME CAS
function getInformationsSDiagDateIntervention(diagnostic, date_debut, date_fin){

	$('.affichageResultatOptionsChoisi div').html('<table> <tr> <td style="padding-top: 60px;"> Chargement </td> </tr>  <tr> <td align="center"> <img style="margin-top: 20px; width: 70px; height: 70px;" src="../images/loading/Chargement_1.gif" /> </td> </tr> </table>');
	$('#visualiserResultatParDateIntervention').toggle(false);
	$('#date_debut, #date_fin').attr('disabled', true); 
	$('#titreResultatOptionChoisi').html("");
	$('#tableauResultatOptionChoisi').html("");
	$('#iconeReinitialiserDateIntervention').css({'visibility':'hidden'});
	//DESACTIVATION DES ICONES POUR LES CHAMPS AGES
	$('#visualiserResultatParAge').toggle(false);
	$('#age_max, #age_min').attr('disabled', true); 
	$('#iconeReinitialiserAge').css({'visibility':'hidden'});
	
	$.ajax({
        type: 'POST',
        url: tabUrl[0]+'public/facturation/get-informations-statistique-service-date-intervention-diagnostic' ,
        data: {'diagnostic': diagnostic, 'date_debut':date_debut, 'date_fin':date_fin},
        success: function(data) {  
        	var result = jQuery.parseJSON(data);  
	
        	$('.affichageResultatOptionsChoisi div').html('');
  	
        	//AFFICHAGE DES RESULTATS
        	$('#affichageResultatOptionsChoisiScript').html(result[0]);
        	$('#tableauResultatOptionChoisi').html(result[1]);
        	$('#titreResultatOptionChoisi').html(result[2]);

        	$('#visualiserResultatParDateIntervention').toggle(true);
        	$('#date_debut, #date_fin').attr('disabled', false); 
        	$('#iconeReinitialiserDateIntervention').css({'visibility':'visible'});
        	
        }
    
    });
	
}

//TROISIEME CAS -- TROISIEME CAS -- TROISIEME CAS
function getInformationsSDiagService(id_service, diagnostic){

	$('.affichageResultatOptionsChoisi div').html('<table> <tr> <td style="padding-top: 60px;"> Chargement </td> </tr>  <tr> <td align="center"> <img style="margin-top: 20px; width: 70px; height: 70px;" src="../images/loading/Chargement_1.gif" /> </td> </tr> </table>');
	$('#visualiserResultatParDateIntervention').toggle(false);
	$('#date_debut, #date_fin').attr('disabled', true); 
	$('#titreResultatOptionChoisi').html("");
	$('#tableauResultatOptionChoisi').html("");
	$('#iconeReinitialiserDateIntervention').css({'visibility':'hidden'});
	//DESACTIVATION DES ICONES POUR LES CHAMPS AGES
	$('#visualiserResultatParAge').toggle(false);
	$('#age_max, #age_min').attr('disabled', true); 
	$('#iconeReinitialiserAge').css({'visibility':'hidden'});
	
	$.ajax({
        type: 'POST',
        url: tabUrl[0]+'public/facturation/get-informations-statistique-un-service-diagnostic' ,
        data: {'id_service':id_service, 'diagnostic': diagnostic},
        success: function(data) {  
        	var result = jQuery.parseJSON(data);  
	
        	$('.affichageResultatOptionsChoisi div').html('');
  	
        	//AFFICHAGE DES RESULTATS
        	$('#affichageResultatOptionsChoisiScript').html(result[0]);
        	$('#tableauResultatOptionChoisi').html(result[1]);
        	$('#titreResultatOptionChoisi').html(result[2]);

        	$('#date_debut, #date_fin').val('').attr('disabled', false); 
        	
        }
    
    });
	
}

//BOITE DE DIALOG POUR L'ENREGISTREMENT DE L'IMAGE 
function enregistrementImage(){
  $( "#enregistrementImage" ).dialog({
    resizable: false,
    height:290,
    width:485,
    autoOpen: false,
    modal: true,
    closeOnEscape: false,
    open: function(event, ui) {  $(".ui-dialog-titlebar-close", $(this).parent()).hide(); },
    buttons: {
    	"Fermer": function() {
    		$(this).dialog( "close" );
    		var enregImage = document.getElementById('contenuPageA');
    		enregImage.style.boxShadow = "0pt 5pt 12px rgba(0, 0, 0, 0.5)";
    		enregImage.style.border = "2px solid #ccc";
    		enregImage.style.backgroundColor = "#ffffff";
    		enregImage.style.maxHeight = "1000px";
    		enregImage.style.borderBottomRightRadius = "10px";
    		enregImage.style.borderBottomLeftRadius = "10px";
    		enregImage.style.borderTop = "2px solid #cccccc";
    		enregImage.style.paddingLeft = "20px";
    		enregImage.style.paddingRight = "25px";
    		enregImage.style.paddingTop = "5px";
    		enregImage.style.paddingBottom = "10px";

    	}
    }
  });
}

//BOITE DE DIALOG POUR LA CONFIRMATION DE L'IMPRESSION
function confirmationImpression(){
  $( "#confirmationImpression" ).dialog({
    resizable: false,
    height:290,
    width:485,
    autoOpen: false,
    modal: true,
    buttons: {
        "Enregistrer": function() {
        	enregistrementImage();
        	$("#confirmationImpression").dialog('close');
        	$("#enregistrementImage").dialog('open');
        	captureImageLancer();
            
        },
        "Annuler": function() {
            $(this).dialog( "close" );
        }
   }
  });
}

function captureImage(){
	confirmationImpression();
	$("#confirmationImpression").dialog('open');
}


function captureImageLancer(){ 

	$('#enregistrementImage').html('<div align="center" ><table> <tr> <td style="padding-top: 25px;"> Enregistrement en cours ... </td> </tr>  <tr> <td align="center"> <img style="margin-top: 20px; width: 70px; height: 70px;" src="../images/loading/Chargement_1.gif" /> </td> </tr> </table> </div>');

	saveStatOption1 = 1;
	var capture = {};
	var target = $('#interfaceStatInformation');
	html2canvas(target, { 
		onrendered: function(canvas) { 
			capture.img = canvas.toDataURL( "image/jpg" );
			capture.data = { 'image' : capture.img };
			
 			$.ajax({
 				url: tabUrl[0]+"public/facturation/creation-image",
 				data: capture.data,
 				type: 'post',
 				success: function( result ) {
 					var resultat = jQuery.parseJSON(result);
 					document.getElementById('enregistrementImage').innerHTML = resultat;
 					
 				}
 			});
		}
	});

}

//GESTION DES STATISTIQUES OPTIONNELLES
//GESTION DES STATISTIQUES OPTIONNELLES
//GESTION DES STATISTIQUES OPTIONNELLES

//BOITE DE DIALOG POUR L'ENREGISTREMENT DE L'IMAGE 
function enregistrementImageStatOptionnelle(){
  $( "#enregistrementImage" ).dialog({
    resizable: false,
    height:290,
    width:485,
    autoOpen: false,
    modal: true,
    closeOnEscape: false,
    open: function(event, ui) {  $(".ui-dialog-titlebar-close", $(this).parent()).hide(); },
    buttons: {
    	"Fermer": function() {
    		$(this).dialog( "close" );
    		var enregImage = document.getElementById('contenuPageB');
    		enregImage.style.boxShadow = "0pt 5pt 12px rgba(0, 0, 0, 0.5)";
    		enregImage.style.border = "2px solid #ccc";
    		enregImage.style.backgroundColor = "#ffffff";
    		enregImage.style.maxHeight = "1000px";
    		enregImage.style.borderBottomRightRadius = "10px";
    		enregImage.style.borderBottomLeftRadius = "10px";
    		enregImage.style.borderTop = "2px solid #cccccc";
    		enregImage.style.paddingLeft = "20px";
    		enregImage.style.paddingRight = "25px";
    		enregImage.style.paddingTop = "5px";
    		enregImage.style.paddingBottom = "10px";

    	}
    }
  });
}

//BOITE DE DIALOG POUR LA CONFIRMATION DE L'IMPRESSION
function confirmationImpressionStatOptionnelle(){
  $( "#confirmationImpression" ).dialog({
    resizable: false,
    height:290,
    width:485,
    autoOpen: false,
    modal: true,
    buttons: {
        "Enregistrer": function() {
        	enregistrementImageStatOptionnelle();
        	$("#confirmationImpression").dialog('close');
        	$("#enregistrementImage").dialog('open');
        	captureImageStatOptionnelleLancer();
            
        },
        "Annuler": function() {
            $(this).dialog( "close" );
        }
   }
  });
}

function captureImageStatOptionnelle(){
	confirmationImpressionStatOptionnelle();
	$("#confirmationImpression").dialog('open');
}


function captureImageStatOptionnelleLancer(){
	
	$('#enregistrementImage').html('<div align="center" ><table> <tr> <td style="padding-top: 25px;"> Enregistrement en cours ... </td> </tr>  <tr> <td align="center"> <img style="margin-top: 20px; width: 70px; height: 70px;" src="../images/loading/Chargement_1.gif" /> </td> </tr> </table> </div>');
	
	saveStatOption2 = 1;
	var capture = {};
	var target = $('#resultatOptionsChoisis table'); //interfaceStatInformationOptionnelle
	html2canvas(target, {
		onrendered: function(canvas) {
			capture.img = canvas.toDataURL( "image/jpg" );
			capture.data = { 'image' : capture.img };

 			$.ajax({
 				url: tabUrl[0]+"public/facturation/creation-image",
 				data: capture.data,
 				type: 'post',
 				success: function( result ) {
 					var resultat = jQuery.parseJSON(result);
 					document.getElementById('enregistrementImage').innerHTML = resultat;
 				}
 			});
		}
	});
	
}



*/



























/*



//GESTION DES STATISTIQUES DES RAPPORTS A GENERER
//GESTION DES STATISTIQUES DES RAPPORTS A GENERER
//GESTION DES STATISTIQUES DES RAPPORTS A GENERER
function infosStatistiquesRapport(){
	$.ajax({
		url: tabUrl[0]+"public/facturation/get-tableau-statistiques-diagnostics-bloc",
		type: 'post',
		data: null,
		success: function( data ) {
			var resultat = jQuery.parseJSON(data);
			$('#tableauResultatRapportOptionChoisi div').html(resultat);
		}
	});
}

//Afficger les informations lors de la selection d'un service
//Afficger les informations lors de la selection d'un service
function getInformationsServiceRapport(id_service){

	$('#listeTableauInfosStatistiques').html('<table> <tr> <td style="padding-top: 50px;"> Chargement </td> </tr>  <tr> <td align="center" style="padding-bottom: 40px;"> <img style="margin-top: 20px; width: 70px; height: 70px;" src="../images/loading/Chargement_1.gif" /> </td> </tr> </table>');
	
	if(id_service == 0){
		infosStatistiquesRapport();
		$('#id_service_rapport').val(0);
		$('#date_debut_rapport, #date_fin_rapport').val('').trigger('keyup');
		$('#diagnostic_rapport').val(0);
	}
	else{ 
		$.ajax({
			url: tabUrl[0]+"public/facturation/get-tableau-statistiques-diagnostics-par-service-bloc",
			type: 'post',
			data: {'id_service':id_service},
			success: function( data ) {
				var resultat = jQuery.parseJSON(data);
				$('#tableauResultatRapportOptionChoisi div').html(resultat);
				
				$('#date_debut_rapport, #date_fin_rapport').val('').trigger('keyup');
				$('#diagnostic_rapport').val(0);
			}
		});
	}
}

//Afficher les informations lors de la saisie d'une date_debut et date_fin
//Afficher les informations lors de la saisie d'une date_debut et date_fin
function getInformationsDatedebutDatefinRapport(){
	var id_servive_rapport = $('#id_service_rapport').val();
	var date_debut_rapport = $('#date_debut_rapport').val();
	var date_fin_rapport = $('#date_fin_rapport').val();
	
	if(date_debut_rapport && date_fin_rapport){
		$('#listeTableauInfosStatistiques').html('<table> <tr> <td style="padding-top: 10px;"> Chargement </td> </tr>  <tr> <td align="center" style="padding-bottom: 40px;"> <img style="margin-top: 20px; width: 70px; height: 70px;" src="../images/loading/Chargement_1.gif" /> </td> </tr> </table>');
		$.ajax({
			url: tabUrl[0]+"public/facturation/get-tableau-statistiques-diagnostics-par-service-par-periode-bloc",
			type: 'post',
			data: {'id_service' : id_servive_rapport, 'date_debut' : date_debut_rapport, 'date_fin' : date_fin_rapport},
			success: function( data ) {
				var resultat = jQuery.parseJSON(data); 
				$('#titreResultatRapportOptionChoisi div span').html(resultat[1]);
				$('#tableauResultatRapportOptionChoisi div').html(resultat[0]);
				
				$('#diagnostic_rapport').val(0);
			}
		});
	
	}
}

//Afficher les informations lors de la selection d'un diagnostic
//Afficher les informations lors de la selection d'un diagnostic
function getListeDiagnosticRapport(id_diagnostic){
	
	var id_servive_rapport = $('#id_service_rapport').val();
	var date_debut_rapport = $('#date_debut_rapport').val();
	var date_fin_rapport = $('#date_fin_rapport').val();
	
	if(id_diagnostic == 0){
		if(date_debut_rapport && date_fin_rapport){
			getInformationsDatedebutDatefinRapport();
		}else{
			getInformationsServiceRapport(id_servive_rapport);	
		}
	}else{
	
		$('#listeTableauInfosStatistiques').html('<table> <tr> <td style="padding-top: 10px;"> Chargement </td> </tr>  <tr> <td align="center" style="padding-bottom: 40px;"> <img style="margin-top: 20px; width: 70px; height: 70px;" src="../images/loading/Chargement_1.gif" /> </td> </tr> </table>');
		$.ajax({
			url: tabUrl[0]+"public/facturation/get-tableau-statistiques-diagnostics-par-service-par-periode-par-diagnostic-bloc",
			type: 'post',
			data: $(this).serialize(),  
			data: {'id_diagnostic' : id_diagnostic , 'id_service' : id_servive_rapport, 'date_debut' : date_debut_rapport, 'date_fin' : date_fin_rapport},
			success: function( data ) {
				var resultat = jQuery.parseJSON(data); 
				$('#titreResultatRapportOptionChoisi div span').html(resultat[1]);
				$('#tableauResultatRapportOptionChoisi div').html(resultat[0]);
			}
		});

	}
}

//Imprimer les rapports des diagnostics des intervention
//Imprimer les rapports des diagnostics des intervention
function imprimerRapportStatistique(){
	var id_service = $('#id_service_rapport').val();
	var date_debut = $('#date_debut_rapport').val();
	var date_fin = $('#date_fin_rapport').val();
	var id_diagnostic = $('#diagnostic_rapport').val();
	
	var lienImpression =  tabUrl[0]+'public/facturation/imprimer-rapport-des-diagnostics-des-interventions';
	var imprimerInformationsStatistiques = document.getElementById("imprimerRapportInformationsStatistiques");
	imprimerInformationsStatistiques.setAttribute("action", lienImpression);
	imprimerInformationsStatistiques.setAttribute("method", "POST");
	imprimerInformationsStatistiques.setAttribute("target", "_blank");
	
	// Ajout dynamique de champs dans le formulaire
	var champ = document.createElement("input");
	champ.setAttribute("type", "hidden");
	champ.setAttribute("name", 'date_debut');
	champ.setAttribute("value", date_debut);
	imprimerInformationsStatistiques.appendChild(champ);
	
	var champ2 = document.createElement("input");
	champ2.setAttribute("type", "hidden");
	champ2.setAttribute("name", 'date_fin');
	champ2.setAttribute("value", date_fin);
	imprimerInformationsStatistiques.appendChild(champ2);
	
	var champ3 = document.createElement("input");
	champ3.setAttribute("type", "hidden");
	champ3.setAttribute("name", 'id_diagnostic');
	champ3.setAttribute("value", id_diagnostic);
	imprimerInformationsStatistiques.appendChild(champ3);
	
	var champ4 = document.createElement("input");
	champ4.setAttribute("type", "hidden");
	champ4.setAttribute("name", 'id_service');
	champ4.setAttribute("value", id_service);
	imprimerInformationsStatistiques.appendChild(champ4);

	$("#imprimerRapportInformationsStatistiques button").trigger('click');
	
}

*/