var base_url = window.location.toString();
var tabUrl = base_url.split("public");

//*********************************************************************
//*********************************************************************

function affichageInfosDepistageMensuel(){
	
	$( "#affichageInfosStatistiques" ).dialog({
		resizable: false,
	    height:690,
	    width:1300,
	    autoOpen: false,
	    modal: true,
	    buttons: {
	        "Fermer": function() {
              $( this ).dialog( "close" );
	        }
	    }
	});
  
	$("#affichageInfosStatistiques").dialog('open');
}

function getInfosStatistiquesParDefaut(){
	$('.affichageInfosTotalDepistage').toggle(false);
	$.ajax({
		type : 'POST',
		url : tabUrl[0] + 'public/technicien/infos-statistiques-resultats-depistages',
		data : null,
		success : function(data) {
			var result = jQuery.parseJSON(data); 
			$('.affichageInfosTotalDepistage').toggle(true);
			result +="<script>$('.champOP1 input, .champOP2 input').trigger('change'); </script>";
			$('.zoneResultatsInfosStatiquesDepistage').html(result);
			
			//Auto-ajustement
			if(nbkligne > 10){
				$('.affichageInfosTotalDepistage .ajustementColonne').css({'height':'1px'});
			}else{
				$('.affichageInfosTotalDepistage .ajustementColonne').css({'height':'0px'});
			}
			
		}
	});
	
	//Gestion du bouton qui affiche les infos
	//Gestion du bouton qui affiche les infos
	
	$('.optionsPeriodePath button').css({'visibility':'hidden'});
	$('.champOP1 input, .champOP2 input').keypress(function(event) { if (event.keyCode == 13) { return false; } });
	$('.champOP1 input, .champOP2 input').change(function(){
		var date_debut = $('.champOP1 input').val();
		var date_fin = $('.champOP2 input').val();
		
		if(date_debut && date_fin){
			if($('#formOtionsPeriodePath')[0].checkValidity() == true){
				$('.optionsPeriodePath button').css({'visibility':'visible'});
			}else{
				$('.optionsPeriodePath button').css({'visibility':'hidden'});
			}
		}else{ 
			$('.optionsPeriodePath button').css({'visibility':'hidden'});
		}
	});
	
	$('span').tooltip({ animation: true, html: true, placement: 'bottom', show: { effect: 'slideDown', } });
	$('.champOP2 .typeTR').css({'color':'green', 'text-shadow':'1px 0px 2px green', 'font-size':'18px'});
}

var typeInfos = 0;

function afficherTousLesResultats(){
	typeInfos = 0;
	tamponChaineProfil = new Array();
	$('.champOP2 .typeRV, .champOP2 .typeRNV').css({'color':'#C8C6C6', 'text-shadow':'0px 0px 0px #C8C6C6', 'font-size':'15px'});
	$('.champOP2 .typeTR').css({'color':'green', 'text-shadow':'1px 0px 2px green', 'font-size':'18px'});
	var date_debut = $('.champOP1 input').val();
	var date_fin = $('.champOP2 input').val();
	
	if($('#formOtionsPeriodePath')[0].checkValidity() == false){ date_debut = null; date_fin = null; }
	
	$('.affichageInfosTotalDepistage').toggle(false);
	$('#listeTableauInfosStatistiques').html('<table align="center" style="margin-top: 25px; margin-bottom: 15px;"> <tr> <td style="margin-top: 35px; border: 1px solid #ffffff; text-align: center;"> Chargement </td> </tr>  <tr> <td align="center" style="border: 1px solid #ffffff; text-align: center;"> <img style="margin-top: 13px; width: 50px; height: 50px;" src="../images/loading/Chargement_1.gif" /> </td> </tr><table>');

	$.ajax({
		type : 'POST',
		url : tabUrl[0] + 'public/technicien/infos-statistiques-resultats-depistages-optionnelles',
		data : {'typeInfos':0, 'date_debut':date_debut, 'date_fin':date_fin},
		success : function(data) {
			var result = jQuery.parseJSON(data); 
			$('.affichageInfosTotalDepistage').toggle(true);
			$('.zoneResultatsInfosStatiquesDepistage').html(result);
			
			//Auto-ajustement
			if(nbkligne > 10){
				$('.affichageInfosTotalDepistage .ajustementColonne').css({'height':'1px'});
			}else{
				$('.affichageInfosTotalDepistage .ajustementColonne').css({'height':'0px'});
			}
			
		}
	});
		
}

function afficherLesResultatsValides(){
	typeInfos = 1;
	tamponChaineProfil = new Array();
	$('.champOP2 .typeTR, .champOP2 .typeRNV').css({'color':'#C8C6C6', 'text-shadow':'0px 0px 0px #C8C6C6', 'font-size':'15px'});
	$('.champOP2 .typeRV').css({'color':'green', 'text-shadow':'1px 0px 2px green', 'font-size':'18px'});
	var date_debut = $('.champOP1 input').val();
	var date_fin = $('.champOP2 input').val();
	
	if($('#formOtionsPeriodePath')[0].checkValidity() == false){ date_debut = null; date_fin = null; }
	
	$('.affichageInfosTotalDepistage').toggle(false);
	$('#listeTableauInfosStatistiques').html('<table align="center" style="margin-top: 25px; margin-bottom: 15px;"> <tr> <td style="margin-top: 35px; border: 1px solid #ffffff; text-align: center;"> Chargement </td> </tr>  <tr> <td align="center" style="border: 1px solid #ffffff; text-align: center;"> <img style="margin-top: 13px; width: 50px; height: 50px;" src="../images/loading/Chargement_1.gif" /> </td> </tr><table>');

	$.ajax({
		type : 'POST',
		url : tabUrl[0] + 'public/technicien/infos-statistiques-resultats-depistages-optionnelles',
		data : {'typeInfos':1, 'date_debut':date_debut, 'date_fin':date_fin},
		success : function(data) {
			var result = jQuery.parseJSON(data); 
			$('.affichageInfosTotalDepistage').toggle(true);
			$('.zoneResultatsInfosStatiquesDepistage').html(result);
			
			//Auto-ajustement
			if(nbkligne > 10){
				$('.affichageInfosTotalDepistage .ajustementColonne').css({'height':'1px'});
			}else{
				$('.affichageInfosTotalDepistage .ajustementColonne').css({'height':'0px'});
			}
		}
	});
		
}

function afficherLesResultatsNonValides(){
	typeInfos = 2;
	tamponChaineProfil = new Array();
	$('.champOP2 .typeTR, .champOP2 .typeRV').css({'color':'#C8C6C6', 'text-shadow':'0px 0px 0px #C8C6C6', 'font-size':'15px'});
	$('.champOP2 .typeRNV').css({'color':'green', 'text-shadow':'1px 0px 2px green', 'font-size':'18px'});
	var date_debut = $('.champOP1 input').val();
	var date_fin = $('.champOP2 input').val();
	
	if($('#formOtionsPeriodePath')[0].checkValidity() == false){ date_debut = null; date_fin = null; }
	
	$('.affichageInfosTotalDepistage').toggle(false);
	$('#listeTableauInfosStatistiques').html('<table align="center" style="margin-top: 25px; margin-bottom: 15px;"> <tr> <td style="margin-top: 35px; border: 1px solid #ffffff; text-align: center;"> Chargement </td> </tr>  <tr> <td align="center" style="border: 1px solid #ffffff; text-align: center;"> <img style="margin-top: 13px; width: 50px; height: 50px;" src="../images/loading/Chargement_1.gif" /> </td> </tr><table>');

	$.ajax({
		type : 'POST',
		url : tabUrl[0] + 'public/technicien/infos-statistiques-resultats-depistages-optionnelles',
		data : {'typeInfos':2, 'date_debut':date_debut, 'date_fin':date_fin},
		success : function(data) {
			var result = jQuery.parseJSON(data); 
			$('.affichageInfosTotalDepistage').toggle(true);
			$('.zoneResultatsInfosStatiquesDepistage').html(result);
			
			//Auto-ajustement
			if(nbkligne > 10){
				$('.affichageInfosTotalDepistage .ajustementColonne').css({'height':'1px'});
			}else{
				$('.affichageInfosTotalDepistage .ajustementColonne').css({'height':'0px'});
			}
			
		}
	});
		
}

function afficherLesDepistagesNayantPasEncoreDeResultat(){
	getListeDesDepistagesNayantPasEncoreDeResultat();
	
	$( "#affichageInfosDepistesPasEncoreResultats" ).dialog({
		resizable: false,
	    height:650,
	    width:600,
	    autoOpen: false,
	    modal: true,
	    buttons: {
	        "Fermer": function() {
              $( this ).dialog( "close" );
	        }
	    }
	});
  
	$("#affichageInfosDepistesPasEncoreResultats").dialog('open');
	
}


function getListeDesDepistagesNayantPasEncoreDeResultat(){
	
	$.ajax({
		type : 'POST',
		url : tabUrl[0] + 'public/technicien/infos-statistiques-depistages-nayant-pas-resultat',
		data : null,
		success : function(data) {
			
			var result = jQuery.parseJSON(data); 
			$('.zoneaffichageInfosDepistesPasEncoreResultats').html(result);

		}
	});
}




function popupTerminer() {
	$(null).w2overlay(null);
}

function voirPlusNumeroDossier(iannee,jmois,listeNumDossierChaine)
{
	var listeNumDossierTab = listeNumDossierChaine.split(',');
	var listeNumDossierTabStyle = "<table class='table table-bordered tab_list_mini' style='width: 100%;'>";
	
	for(var ik=0 ; ik<listeNumDossierTab.length-1 ; ik++){
		if(ik%2 == 0){
			listeNumDossierTabStyle += "<tr style='width: 100%; background: #e9e9e9;'><td style='width: 100%; height: 40px; text-align: center; padding-right: 15px; font-family: Goudy Old Style; font-size: 18px; font-weight: normal; color: green;'><i style='font-size: 10px; color: black; margin-left:-10px;'>"+(ik+1)+" -  </i>"+listeNumDossierTab[ik]+"</td></tr>";			
		}else{
			listeNumDossierTabStyle += "<tr style='width: 100%; '><td style='width: 100%; height: 40px; text-align: center; padding-right: 15px; font-family: Goudy Old Style; font-size: 18px; font-weight: normal; color: green;'><i style='font-size: 10px; color: black; margin-left:-10px;'>"+(ik+1)+" -  </i>"+listeNumDossierTab[ik]+"</td></tr>";			
		}

	}
	listeNumDossierTabStyle += "</table>";
	
	$('.voirPlusNumDossier_'+iannee+''+jmois).w2overlay({ html: "" +
		"<div style='height: 245px; width: 170px; padding-top:10px; text-align:center;'>" +
		"<div style='height: 99%; width: 100%; max-height: 99%; max-width: 100%; overflow: auto;' class='listeNumDossierVoirPlusDNPR' align='right'> "+listeNumDossierTabStyle+" </div>" +
		"</div>"+
		"<style> .w2ui-overlay:before{right: 7px;  border: 1px solid green; top: 2px; border-bottom: 0px solid transparent; border-left:1px solid transparent;}  .w2ui-overlay{margin-left: 50px; border: 1px solid green; } </style>"
	});
	
}

var tamponChaineProfil = new Array();

function voirPlusNumeroDossierStatInfo(i,j,iprof,mois,annee,profil,date_debut,date_fin)
{
	var chaineChargementOuPofil = '<div align="center" style="width: 100%; "> <img style="margin-left: 10px; margin-top: 15px; width: 50px; height: 50px;" src="../images/loading/Chargement_1.gif"></div>';
	if(tamponChaineProfil[i+''+j+''+iprof]){ chaineChargementOuPofil = tamponChaineProfil[i+''+j+''+iprof]; }
	
	$('.voirPlusNumDossierSTAT_'+i+''+j+''+iprof).w2overlay({ html: "" +
		"<div style='height: 245px; width: 170px; padding-top:10px; text-align:center;'>" +
		"<div style='height: 99%; width: 100%; max-height: 99%; max-width: 100%; overflow: auto;' class='listeNumDossierVoirPlusSTATINFO' align='right'> "+chaineChargementOuPofil+" </div>" +
		"</div>"+
		"<style> .w2ui-overlay:before{left: 7px;  border: 1px solid green; top: 2px; border-bottom: 0px solid transparent; border-left:1px solid transparent;}  .w2ui-overlay{margin-left: -100px; margin-top:5px; border: 1px solid green; } </style>"+
		"<style> .w2ui-overlay:after { border: 1px solid green; bottom: -8px; border-bottom: 0px solid transparent; border-left:1px solid transparent;} </style>"
	});
	
	if(!tamponChaineProfil[i+''+j+''+iprof]){
		$.ajax({
			type : 'POST',
			url : tabUrl[0] + 'public/technicien/infos-statistiques-voir-plus-numero-dossier',
			data : {'mois':mois, 'annee':annee, 'profil':profil, 'date_debut':date_debut, 'date_fin':date_fin, 'typeInfos':typeInfos },
			success : function(data) {
				var listeNumDossierChaine = jQuery.parseJSON(data); 
				
				//PREPARATION DE LA LISTE --- PREPARATION DE LA LISTE
				var listeNumDossierTab = listeNumDossierChaine.split(',');
				var listeNumDossierTabStyle = "<table class='table table-bordered tab_list_mini' style='width: 100%;'>";
				
				for(var ik=0 ; ik<listeNumDossierTab.length-1 ; ik++){
					if(ik%2 == 0){
						listeNumDossierTabStyle += "<tr style='width: 100%; background: #e9e9e9;'><td style='width: 100%; height: 40px; text-align: center; padding-right: 15px; font-family: Goudy Old Style; font-size: 18px; font-weight: normal; color: green;'><i style='font-size: 10px; color: black; margin-left:-10px;'>"+(ik+1)+" -  </i>"+listeNumDossierTab[ik]+"</td></tr>";			
					}else{
						listeNumDossierTabStyle += "<tr style='width: 100%; '><td style='width: 100%; height: 40px; text-align: center; padding-right: 15px; font-family: Goudy Old Style; font-size: 18px; font-weight: normal; color: green;'><i style='font-size: 10px; color: black; margin-left:-10px;'>"+(ik+1)+" -  </i>"+listeNumDossierTab[ik]+"</td></tr>";			
					}
				}
				listeNumDossierTabStyle += "</table>";
				
				//fin PREPARATION DE LA LISTE --- fin PREPARATION DE LA LISTE
				tamponChaineProfil[i+''+j+''+iprof] = listeNumDossierTabStyle;
				$(".listeNumDossierVoirPlusSTATINFO").html(listeNumDossierTabStyle);
			}
		});
	}

	
}




















//En cliquant sur le bouton afficher
//En cliquant sur le bouton afficher
//En cliquant sur le bouton afficher

function getInfosStatistiquesDepistageParPeriode(){
	typeInfos = 0;
	var date_debut = $('.champOP1 input').val();
	var date_fin = $('.champOP2 input').val();

	$('.champOP2 .typeRV, .champOP2 .typeRNV').css({'color':'#C8C6C6', 'text-shadow':'0px 0px 0px #C8C6C6', 'font-size':'15px'});
	$('.champOP2 .typeTR').css({'color':'green', 'text-shadow':'1px 0px 2px green', 'font-size':'18px'});
	
	$('.affichageInfosTotalDepistage').toggle(false);
	$('#listeTableauInfosStatistiques').html('<table align="center" style="margin-top: 25px; margin-bottom: 15px;"> <tr> <td style="margin-top: 35px; border: 1px solid #ffffff; text-align: center;"> Chargement </td> </tr>  <tr> <td align="center" style="border: 1px solid #ffffff; text-align: center;"> <img style="margin-top: 13px; width: 50px; height: 50px;" src="../images/loading/Chargement_1.gif" /> </td> </tr><table>');
	$.ajax({
		type : 'POST',
		url : tabUrl[0] + 'public/technicien/infos-statistiques-resultats-depistages-optionnelles',
		data : {'typeInfos':0, 'date_debut':date_debut, 'date_fin':date_fin },
		success : function(data) {
			var result = jQuery.parseJSON(data); 
			
			$('.affichageInfosTotalDepistage').toggle(true);
			$('.zoneResultatsInfosStatiquesDepistage').html(result);
			
			//Auto-ajustement
			if(nbkligne > 10){
				$('.affichageInfosTotalDepistage .ajustementColonne').css({'height':'1px'});
			}else{
				$('.affichageInfosTotalDepistage .ajustementColonne').css({'height':'0px'});
			}
			
		}
	});

}


































function imprimerInformationsStatistiques(){
	
	var date_debut = $('.champOP1 input').val();
	var date_fin = $('.champOP2 input').val();
	if($('#formOtionsPeriodePath')[0].checkValidity() == false){ date_debut = ''; date_fin = ''; }
	
	var lienImpression =  tabUrl[0]+'public/technicien/imprimer-informations-statistiques-depistages';
	var imprimerInformationsStatistiques = document.getElementById("imprimerInformationsStatistiques");
	imprimerInformationsStatistiques.setAttribute("action", lienImpression);
	imprimerInformationsStatistiques.setAttribute("method", "POST");
	imprimerInformationsStatistiques.setAttribute("target", "_blank");
	
	// Ajout dynamique de champs dans le formulaire
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
	champ3.setAttribute("name", 'typeInfos');
	champ3.setAttribute("value", typeInfos);
	imprimerInformationsStatistiques.appendChild(champ3);
	
	$("#imprimerInformationsStatistiques button").trigger('click');
	
}












function afficherInformationsStatistiquesDiagramme(){
	
	if(nbkligne > 5){
		var x=900;
	}else{
		var x=700;
	}
	
	$( "#affichageInfosStatistiquesDiagramme" ).dialog({
		resizable: false,
	    height: 640,
	    width: x,
	    autoOpen: false,
	    modal: true,
	    buttons: {
	        "Fermer": function() {
              $( this ).dialog( "close" );
	        }
	    }
	});
  
	$("#affichageInfosStatistiquesDiagramme").dialog('open');
	affichageDiagrammeColumn();
}

function affichageDiagrammeColumn(){
	$('.iconeDiag').css({'opacity': '0.4', 'color': 'white', 'box-shadow' : '0pt 2pt 6px rgba(0, 0, 0, 0.4)'});
	$('.typeDiagrammeColumn').css({'opacity': '10', 'box-shadow' : '0pt 2pt 6px rgba(0, 0, 0, 0.7)'});
	
	$(document).ready(function($) {

		/**
		 * ANCIENNE FONCTION
		 */
		/*
		function diagrammeInfosStatistiques() {
			var diagrammeInfosStatistiques = new CanvasJS.Chart("diagrammeInfosStatistiques", {
				data: [{
					type: "column",
					dataPoints: Pile,
				}]

			});
			diagrammeInfosStatistiques.render();
		}

		diagrammeInfosStatistiques(); 
		*/
		
		/**
		 * NOUVELLE FONCTION
		 */
		$("#diagrammeInfosStatistiques").html("");
		anychart.onDocumentReady(function () {
		    // create column chart
		    var chart = anychart.column3d();

		    // turn on chart animation
		    chart.animation(true);

		    // set chart title text settings
		    chart.title('');

		    // create area series with passed data
		    chart.column( PileAnyChart );

		    chart.tooltip()
		            .position('center-top')
		            .anchor('center-bottom')
		            .offsetX(0)
		            .offsetY(5)
		            .format('{%Value}');

		    // set scale minimum
		    chart.yScale().minimum(0);

		    // set yAxis labels formatter
		    chart.yAxis().labels().format('{%Value}{groupsSeparator: }');

		    chart.tooltip().positionMode('point');
		    chart.interactivity().hoverMode('by-x');

		    chart.xAxis().title('');
		    chart.yAxis().title('');

		    // set container id for the chart
		    chart.container('diagrammeInfosStatistiques');

		    // initiate chart drawing
		    chart.draw();
		});

		
	});
	
}

function affichageDiagrammePie(){
	$('.iconeDiag').css({'opacity': '0.4', 'color': 'white', 'box-shadow' : '0pt 2pt 6px rgba(0, 0, 0, 0.4)'});
	$('.typeDiagrammePie').css({'opacity': '10', 'box-shadow' : '0pt 2pt 6px rgba(0, 0, 0, 0.7)'});
	
	$(document).ready(function($) {

		/**
		 * ANCIENNE FONCTION
		 */
		/*
		function diagrammeInfosStatistiques() {
			var diagrammeInfosStatistiques = new CanvasJS.Chart("diagrammeInfosStatistiques", {
				data: [{
					type: "pie",
					dataPoints: Pile,
				}]

			});
			diagrammeInfosStatistiques.render();
		}

		diagrammeInfosStatistiques(); 
		*/
		
		/**
		 * NOUVELLE FONCTION
		 */
		$("#diagrammeInfosStatistiques").html("");
		anychart.onDocumentReady(function () {
        	
    	    // create pie chart with passed data
    	    var chart = anychart.pie3d( PileAnyChart );

       	    // turn on chart animation
		    chart.animation(true);
		    
    	    // set chart title text settings
    	    chart.title('')
    	    // set chart radius
    	    .radius('43%');

    	    // set container id for the chart
    	    chart.container('diagrammeInfosStatistiques');
    	    // initiate chart drawing
    	    chart.draw();
    	});
		
		

	});
	
}

function affichageDiagrammeBar(){
	$('.iconeDiag').css({'opacity': '0.4', 'color': 'white', 'box-shadow' : '0pt 2pt 6px rgba(0, 0, 0, 0.4)'});
	$('.typeDiagrammeBar').css({'opacity': '10', 'box-shadow' : '0pt 2pt 6px rgba(0, 0, 0, 0.7)'});
	
	$(document).ready(function($) {

		/**
		 * ANCIENNE FONCTION
		 */
		/*
		function diagrammeInfosStatistiques() {
			var diagrammeInfosStatistiques = new CanvasJS.Chart("diagrammeInfosStatistiques", {
				data: [{
					type: "bar",
					dataPoints: Pile,
				}]

			});
			diagrammeInfosStatistiques.render();
		}

		diagrammeInfosStatistiques(); 
		*/
		
		/**
		 * NOUVELLE FONCTION
		 */
		$("#diagrammeInfosStatistiques").html("");
		anychart.onDocumentReady(function () {
    	    // create bar chart
    	    var chart = anychart.bar3d();

    	    // turn on chart animation
    	    chart.animation(true);

    	    // set chart padding
    	    chart.padding([10, 40, 5, 20]);

    	    // set chart title text settings
    	    chart.title('');

    	    // create area series with passed data
    	    chart.bar( PileAnyChart );

    	    // set tooltip settings
    	    chart.tooltip()
    	            .positionMode('point')
    	            .format('{%Value}')
    	            .position('right')
    	            .anchor('left-center')
    	            .offsetX(5)
    	            .offsetY(0);

    	    // set yAxis labels formatter (axeY)
    	    chart.yAxis().labels().format('{%Value}');

    	    // set titles for axises
    	    chart.xAxis().title('');
    	    chart.yAxis().title('');
    	    chart.interactivity().hoverMode('by-x');

    	    // set scale minimum
    	    chart.yScale().minimum(0);

    	    // set container id for the chart
    	    chart.container('diagrammeInfosStatistiques');

    	    // initiate chart drawing
    	    chart.draw();
    	});
		

	});
	
}

function affichageDiagrammeLine(){
	$('.iconeDiag').css({'opacity': '0.4', 'color': 'white', 'box-shadow' : '0pt 2pt 6px rgba(0, 0, 0, 0.4)'});
	$('.typeDiagrammeLine').css({'opacity': '10', 'box-shadow' : '0pt 2pt 6px rgba(0, 0, 0, 0.7)'});
	
	$(document).ready(function($) {

		/**
		 * ANCIENNE FONCTION
		 */
		/*
		function diagrammeInfosStatistiques() {
			var diagrammeInfosStatistiques = new CanvasJS.Chart("diagrammeInfosStatistiques", {
				data: [{
					type: "line",
					dataPoints: Pile,
				}]

			});
			diagrammeInfosStatistiques.render();
		}

		diagrammeInfosStatistiques(); 
		*/
		
		/**
		 * NOUVELLE FONCTION
		 */
		$("#diagrammeInfosStatistiques").html("");
		/*
		anychart.onDocumentReady(function () {
		    // create area chart
		    var chart = anychart.line3d();

		    // turn on chart animation
		    chart.animation(true);

		    // set chart title text settings
		    
		    chart.title()
		            .enabled(true)
		            .useHtml(true)
		            .text('Site Visits During 2017 Year<br/>' +
		                    '<span style="color:#212121; font-size: 13px;">' +
		                    'Considered to be the total amount of visits, including repeatable' +
		                    '</span>');

		    chart.yAxis().title('');

		    // create area series on passed data
		    var series = chart.line();
		    series.data(getData());
		    series.name('');

		    // set series data labels settings
		    series.labels()
		            .enabled(true)
		            .fontColor('#212121');

		    // set series data markers settings
		    series.markers(true);

		    // set tooltip settings
		    chart.tooltip()
		            .positionMode('point')
		            .anchor('left-top')
		            .offsetX(5)
		            .offsetY(5);

		    // set interactivity settings
		    chart.interactivity().hoverMode('by-x');

		    // set container for the chart
		    chart.container('diagrammeInfosStatistiques');

		    // initiate chart drawing
		    chart.draw();
		});

		function getData() {
		    return PileAnyChart;
		}*/
		
		anychart.onDocumentReady(function () {
		    // create data set on our data
		    chartData = {
		        header: ListeProfilsDataAnyChartLine,
		        rows: getDataLineAnyChart()
		    };

		    // create area chart
		    var chart = anychart.line3d();

		    // set chart data
		    chart.data(chartData);

		    // turn on chart animation
		    chart.animation(true);

		    chart.yAxis().title('');
		    chart.yAxis().labels().format('{%Value}');

		    // turn on legend
		    chart.legend()
		            .enabled(true)
		            .fontSize(13)
		            .padding([0, 0, 20, 0]);

		    // set 3D settings
		    chart.zAspect('70%')
		            .zAngle(60);

		    // set interactivity and tooltips settings
		    chart.interactivity().hoverMode('by-x');
		    chart.tooltip().displayMode('union');

		    // set container id for the chart
		    chart.container('diagrammeInfosStatistiques');

		    // initiate chart drawing
		    chart.draw();
		});

		function getDataLineAnyChart() {
		    return DataAnyChartLine;
		}

	});
	
}
