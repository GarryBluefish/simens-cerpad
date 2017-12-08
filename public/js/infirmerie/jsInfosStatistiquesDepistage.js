var base_url = window.location.toString();
var tabUrl = base_url.split("public");

//*********************************************************************
//*********************************************************************

function affichageInfosDepistageMensuel(){
	$( "#affichageInfosStatistiques" ).dialog({
		resizable: false,
	    height:690,
	    width:750,
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
		url : tabUrl[0] + 'public/infirmerie/infos-statistiques-depistage-mensuel',
		data : null,
		success : function(data) {
			var result = jQuery.parseJSON(data); 
			$('.affichageInfosTotalDepistage').toggle(true);
			result +="<script>$('.champOP1 input, .champOP2 input').val('').trigger('change'); </script>";
			$('.zoneResultatsInfosStatiquesDepistage').html(result);
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
}


function getInfosStatistiquesDepistageParPeriode(){
	var date_debut = $('.champOP1 input').val();
	var date_fin = $('.champOP2 input').val();

	if(date_debut && date_fin){
		$('.affichageInfosTotalDepistage').toggle(false);
		$('#listeTableauInfosStatistiques').html('<table align="center" style="margin-top: 25px; margin-bottom: 15px;"> <tr> <td style="margin-top: 35px; border: 1px solid #ffffff; text-align: center;"> Chargement </td> </tr>  <tr> <td align="center" style="border: 1px solid #ffffff; text-align: center;"> <img style="margin-top: 13px; width: 50px; height: 50px;" src="../images/loading/Chargement_1.gif" /> </td> </tr><table>');
		$.ajax({
			type : 'POST',
			url : tabUrl[0] + 'public/infirmerie/infos-statistiques-optionnelles-depistage-mensuel',
			data : {'date_debut':date_debut, 'date_fin':date_fin },
			success : function(data) {
				var result = jQuery.parseJSON(data); 
				$('.affichageInfosTotalDepistage').toggle(true);
				$('.zoneResultatsInfosStatiquesDepistage').html(result);
			}
		});
		
	}
	
}


function imprimerInformationsStatistiques(){
	
	var date_debut = $('.champOP1 input').val();
	var date_fin = $('.champOP2 input').val();
	var id_type_pathologie = $('.optionsTypePath .listeZRI1 select').val();
	
	var lienImpression =  tabUrl[0]+'public/infirmerie/imprimer-informations-statistiques-depistages';
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
	
	$("#imprimerInformationsStatistiques button").trigger('click');
	
}
