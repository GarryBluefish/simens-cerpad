var base_url = window.location.toString();
var tabUrl = base_url.split("public");
//BOITE DE DIALOG POUR LA CONFIRMATION DE SUPPRESSION
function confirmation(id){
  $( "#confirmation" ).dialog({
    resizable: false,
    height:375,
    width:485,
    autoOpen: false,
    modal: true,
    buttons: {
        "Terminer": function() {
            $( this ).dialog( "close" );    
            deplierFormulaireAdmission = 0;
            return false;
        }
   }
  });
}

function visualiser(id){ 
  	 deplierFormulaireAdmission = 1;
	 confirmation(id);
	 var cle = id;
     var chemin = tabUrl[0]+'public/facturation/vue-popup';
     $.ajax({
         type: 'POST',
         url: chemin ,
         data: $(this).serialize(),  
         data:'id='+cle,
         success: function(data) {    
         	    var result = jQuery.parseJSON(data);   
         	     $("#info").html(result);
         	     
         	     $("#confirmation").dialog('open'); //Appel du POPUP
         	       
         },
         //error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
         //dataType: "html"
     });
}

$(function(){
setTimeout(function() {
	infoBulle();
}, 1000);
});

function infoBulle(){
	/***
	 * INFO BULLE FE LA LISTE
	 */
	 var tooltips = $( 'table tbody tr td infoBulleVue' ).tooltip({show: {effect: 'slideDown', delay: 250}});
	     tooltips.tooltip( 'close' );
	  $('table tbody tr td infoBulleVue').mouseenter(function(){
	    var tooltips = $( 'table tbody tr td infoBulleVue' ).tooltip({show: {effect: 'slideDown', delay: 250}});
	    tooltips.tooltip( 'open' );
	  });
}

var  oTable;
var nbDemandes;
function initialisation(){
    
	var asInitVals = new Array();
	oTable = $('#patient').dataTable
	( {
		"sPaginationType": "full_numbers",
		"aLengthMenu": [5,7,10,15],
		"aaSorting": [], //On ne trie pas la liste automatiquement
		"oLanguage": {
			"sInfo": "_START_ &agrave; _END_ sur _TOTAL_ &eacute;l&eacute;ments",
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

		"sAjaxSource": ""+tabUrl[0]+"public/facturation/liste-admission-ajax", 

		"fnDrawCallback": function() 
		{
			//markLine();
			clickRowHandler();
		}
						
	} );

	$("thead input").keyup( function () {
		/* Filter on the column (the index) of this element */
		oTable.fnFilter( this.value, $("thead input").index(this) );
	} );

	/*
	* Support functions to provide a little bit of 'user friendlyness' to the textboxes in 
	* the footer
	*/
	$("thead input").each( function (i) {
		asInitVals[i] = this.value;
	} );

	$("thead input").focus( function () {
		if ( this.className == "search_init" )
		{
			this.className = "";
			this.value = "";
		}
	} );

	$("thead input").blur( function (i) {
		if ( this.value == "" )
		{
			this.className = "search_init";
			this.value = asInitVals[$("thead input").index(this)];
		}
	} );

	$('#patient thead th').unbind('click');
	
	
	raffraichirListeDemandeAdmission();
	
}

var deplierFormulaireAdmission = 0;
function raffraichirListeDemandeAdmission() {
	setTimeout(function(){
		//alert(nbDemandes);
		$.ajax({
	        type: 'POST',
	        url: tabUrl[0]+'public/facturation/get-nb-patient-admis',
	        data: {'id':1},
	        success: function(data) {    
	        	var result = jQuery.parseJSON(data);  
	        	//alert(result);
	        	if(result > nbDemandes){
	        		if(deplierFormulaireAdmission == 0){
			        	$(location).attr("href",tabUrl[0]+"public/facturation/admission");
	        		}
	        	}
	        	raffraichirListeDemandeAdmission();
	        }
		});
	},30000);
}


function clickRowHandler() 
{
	var id;
	$('#patient tbody tr').contextmenu({
		target: '#context-menu',
		onItem: function (context, e) {
			
			if($(e.target).text() == 'Visualiser' || $(e.target).is('#visualiserCTX')){
				if(id){ visualiser(id); }
			} else 
				if($(e.target).text() == 'Suivant' || $(e.target).is('#suivantCTX')){
					if(id){ admettre(id); }
				}
			
		}
	
	}).bind('mousedown', function (e) {
			var aData = oTable.fnGetData( this );
		    id = aData[6];
	});
	
	
	
	$("#patient tbody tr").bind('dblclick', function (event) {
		var aData = oTable.fnGetData( this );
		var id = aData[6];
		if(id){ visualiser(id); }
	});
	
	$('a,img,hass').tooltip({ animation: true, html: true, placement: 'bottom', show: { effect: 'slideDown', } });

}

var tarifFact = 0;
function animation(){
//ANIMATION
//ANIMATION
//ANIMATION

$('#info_facturation').toggle(false);

$('#precedent').click(function(){
	deplierFormulaireAdmission = 0;
	$("#titre2").replaceWith("<div id='titre' style='font-family: police2; color: green; font-size: 18px; font-weight: bold; padding-left: 35px;'><iS style='font-size: 25px;'>&curren;</iS> LISTE DES DEMANDES D\'ANALYSES PAR PATIENT </div>");	
    
	
	
	//var boutons = $('input[name=type_facturation]');
    //$(boutons[0]).trigger('click');
  	//$('.organisme').toggle(false); $('#organisme').val('');
	//$('.taux').toggle(false); $('#taux').val('');
	tarifFact = 0;
  	$('#montant_avec_majoration').val('');
  			

	
	$('#contenu').animate({
        height : 'toggle'
     },1000);
     $('#info_facturation').animate({
        height : 'toggle'
     },1000);
	 
     //IL FAUT LE RECREER POUR L'ENLEVER DU DOM A CHAQUE FOIS QU'ON CLIQUE SUR PRECEDENT
     $("#termineradmission").replaceWith("<button id='termineradmission' style='height:35px;'>Terminer</button>");
     
     return false;
});

}


var entreeValidation = 0;

function admettre(idpatient){ 
	deplierFormulaireAdmission = 1;
	$("#termineradmission").replaceWith("<button id='termineradmission' style='height:35px;'>Terminer</button>");
    $("#titre").replaceWith("<div id='titre2' style='font-family: police2; color: green; font-size: 18px; font-weight: bold; padding-left: 35px;'><iS style='font-size: 25px;'>&curren;</iS> ADMISSION </div>");	

    var chemin = tabUrl[0]+'public/facturation/vue-admission';
    var tableauAnalyses = [];
    $.ajax({
        type: 'POST',
        url: chemin ,
        data: $(this).serialize(),  
        data:'idpatient='+idpatient,
        success: function(data) {    
        	    var result = jQuery.parseJSON(data);  
        	    
        	        
        	        result+= "<script>"+
        	                    "$('#listeDemandesAnalysesTableau .listeDataTable').toggle(false);"+
        	                 "</script>";
        	        
        	        result+= "<style>"+
	        	               "#listeDemandesAnalysesTableau div .dataTables_paginate{ margin-top: -15px; }"+
        	                 "</style>";
        	    
        	    $("#info_patient").html(result);
        	    
        	    //PASSER A SUIVANT
        	    $('#info_facturation').animate({
        	         height : 'toggle'
        	      },1000);
        	     $('#contenu').animate({
        	         height : 'toggle'
        	     },1000);
        	     
        	     
        	     
        	     
        	     if(entreeValidation == 0){
        	    	 entreeValidation = 1;
                     $('.termineradmission button').click(function(){
                    	 
                    	 if($('#formulairePrincipal')[0].checkValidity() == true){
                    		 //formulaire valide et envoi des données
                    		 $('.termineradmission button').attr('disabled', true);
                    		 $('#envoyerDonneesAdmission').trigger('click');
            	    	 }else{
            	    		 
            	    		 $('#envoyerDonneesAdmission').trigger('click');
            	    	 }
            	    
                     });
        	     }

        	     
        },
        //error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
        //dataType: "html"
    });
    
    //Annuler l'admission
    $("#annuler").click(function(){
    	$("#annuler").css({"border-color":"#ccc"});
    	
	    $(location).attr("href",tabUrl[0]+"public/facturation/admission");
        return false;
    });
    
    $("#id_patient").val(id);
  
}


function listeDemandesAnalyses()
{
	
    var oTable2 = $('#listeDemandesAnalysesTab').dataTable
    ( {
		"sPaginationType": "full_numbers",
		"aLengthMenu": [],
		"iDisplayLength": 10,
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
	
	$('a,img,hass,infoBulleVue').tooltip({
        animation: true,
        html: true,
        placement: 'bottom',
        show: {
            effect: "slideDown",
            delay: 250
          }
    });
	
}


function infos_parentales(id)
{
	
	$('#infos_parentales_'+id).w2overlay({ html: "" +
		"" +
		"<div style='border-bottom:1px solid green; height: 30px; background: #f9f9f9; width: 600px; text-align:center; padding-top: 10px; font-size: 13px; color: green; font-weight: bold;'><img style='padding-right: 10px;' src='"+tabUrl[0]+"public/images_icons/Infos_parentales.png' >Informations parentales</div>" +
		"<div style='height: 245px; width: 600px; padding-top:10px; text-align:center;'>" +
		"<div style='height: 77%; width: 95%; max-height: 77%; max-width: 95%; ' class='infos_parentales' align='left'>  </div>" +
		"</div>"+
		"<script> $('.infos_parentales').html( $('.infos_parentales_tampon').html() ); </script>" 
	});
	
}

function prixMill(num) {
	return ("" + num).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, function($1) { return $1 + " " });
}

var tableauAnalysesSelectionnees = [];
function supprimerAnalyseDeselectionnees(iddemande) {
	var tableauAnalyses = [];
	var j = 0;
	for(var i = 0 ; i < tableauAnalysesSelectionnees.length ; i++){
		if(tableauAnalysesSelectionnees[i] != iddemande){
			tableauAnalyses[j++] = tableauAnalysesSelectionnees[i];
		}
	}
	tableauAnalysesSelectionnees = tableauAnalyses;
}


function ajouterFacturation(tarif , iddemande){
	
	//Ajout des demandes selectionées dans un tableau
	tableauAnalysesSelectionnees.push(iddemande); 
	$('#listeanalysesselectionnees').val(tableauAnalysesSelectionnees);
	//-----------------------------------------------
	
	tarifFact +=tarif;
	$("#montant").val(tarifFact);
	
	var taux = $("#taux").val();
	if(taux){
		$("#montant_avec_majoration").val(prixMill(tarifFact+(tarifFact*taux)/100));
	} else {
		$("#montant_avec_majoration").val(prixMill(tarifFact));
	}
}

function reduireFacturation(tarif , iddemande){
	
	//Supprimer l'analyse déselectionnée du tableau
	supprimerAnalyseDeselectionnees(iddemande); 
	$('#listeanalysesselectionnees').val(tableauAnalysesSelectionnees);
	//---------------------------------------------
	
	tarifFact -=tarif;
	$("#montant").val(tarifFact);
	
	var taux = $("#taux").val();
	if(taux){
		$("#montant_avec_majoration").val(prixMill(tarifFact+(tarifFact*taux)/100));
	} else {
		$("#montant_avec_majoration").val(prixMill(tarifFact));
	}
}

function getTarif(taux){ 
	var montantMajore;
	if(tarifFact && taux){
		//montantMajore = tarifFact + (tarifFact*taux)/100;
		montantMajore = (tarifFact*taux)/100;
		$('#montant_avec_majoration').val(prixMill(montantMajore));
	} else if(!taux){
		$('#montant_avec_majoration').val(prixMill(tarifFact));
	}
}

var temoin = 0;
function scriptMajorationFacturation(){
	$('.organisme').toggle(false);
	$('.taux').toggle(false);

	var boutons = $('input[name=type_facturation]');
	$(boutons[0]).trigger('click');
	
	$(boutons).click(function(){
		if(boutons[0].checked){
			$('.organisme').toggle(false);
			$('.taux').toggle(false);
			
			if(temoin == 1){
				$('#montant_avec_majoration').val(prixMill(tarifFact));
				temoin = 0;
			}
			$('#taux').val("");

			$('#organisme').attr('required', false);
		} else 
			if(boutons[1].checked){
				$('.organisme').toggle(true);
				$('.taux').toggle(true);

				if(temoin == 0){
					temoin = 1;
				}
				
				$('#organisme').attr('required', true);
			}
	});
	
	
	//Pour l'impression de la facture
	//Pour l'impression de la facture
//	$('.termineradmissions').click(function(){ //alert('imprimer'); return false;
//
//		var donnees = new Array();
//		donnees['idpatient'] = $('#idpatient').val();
//		donnees['numero'] = $('#numero').val();
//		donnees['service'] = $('#service').val();
//		donnees['montant_avec_majoration'] = $('#montant_avec_majoration').val();
//		donnees['montant'] = $('#montant').val();
//		
//		alert(donnees['idpatient']); return false;
//		
//		if( temoin == 0 ){
//			
//			donnees['type_facturation'] = 1;
//			var vart = tabUrl[0]+'public/facturation/impression-facture';
//		    
//			var formulaireImprimerFacture = document.getElementById("FormulaireImprimerFacture");
//			formulaireImprimerFacture.setAttribute("action", vart);
//			formulaireImprimerFacture.setAttribute("method", "POST");
//			formulaireImprimerFacture.setAttribute("target", "_blank");
//			
//			for( donnee in donnees ){
//				// Ajout dynamique de champs dans le formulaire
//				var champ = document.createElement("input");
//				champ.setAttribute("type", "hidden");
//				champ.setAttribute("name", donnee);
//				champ.setAttribute("value", donnees[donnee]);
//				formulaireImprimerFacture.appendChild(champ);
//			}
//
//			if( donnees['service'] ){
//				// Envoi de la requête
//				$("#ImprimerFacture").trigger('click');
//				setTimeout(function(){
//					document.getElementById("formulairePrincipal").submit();
//				});
//				return false;
//			} else if( !donnees['service'] ){
//				return true;
//			}
//		 
//		} else if( temoin == 1 ){
//			
//			donnees['type_facturation'] = 2;
//			donnees['organisme'] = $('#organisme').val();
//			donnees['taux'] = $('#taux').val();
//			var vart = tabUrl[0]+'public/facturation/impression-pdf';
//		    
//			var formulaireImprimerFacture = document.getElementById("FormulaireImprimerFacture");
//			formulaireImprimerFacture.setAttribute("action", vart);
//			formulaireImprimerFacture.setAttribute("method", "POST");
//			formulaireImprimerFacture.setAttribute("target", "_blank");
//			
//			for( donnee in donnees ){
//				// Ajout dynamique de champs dans le formulaire
//				var champ = document.createElement("input");
//				champ.setAttribute("type", "hidden");
//				champ.setAttribute("name", donnee);
//				champ.setAttribute("value", donnees[donnee]);
//				formulaireImprimerFacture.appendChild(champ);
//			}
//
//			if( donnees['service'] && donnees['organisme']){
//				// Envoi de la requête
//				$("#ImprimerFacture").trigger('click');
//				setTimeout(function(){
//					document.getElementById("formulairePrincipal").submit();
//				});
//				return false;
//			} else if( !donnees['service'] || !donnees['organisme']){
//				return true;
//			}
//			
//		}
//		
//	});
}


function afficherLaListeDesAnalysesDelaDemande(idpatient, date, numOrdre){ 
	
	$('#listeDesAnalysesTableau').html('<table style="width: 100%; margin-top: 30px;" align: center;> <tr> <td style="margin-top: 20px; text-align: center; "> Chargement </td> </tr>  <tr> <td align="center"> <img style="margin-top: 20px; width: 50px; height: 50px;" src="../images/loading/Chargement_1.gif" /> </td> </tr> </table>');

	$.ajax({
        type: 'POST',
        url: tabUrl[0]+'public/facturation/liste-des-analyses-dela-demande-selectionnee' ,
        data: ({'idpatient':idpatient, 'date':date, 'numOrdre':numOrdre}),
        success: function(data) {
        	    var result = jQuery.parseJSON(data);  
        	    $('#listeDesAnalysesTableau').html(result);
        },
        
        //error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
        //dataType: "html"
	});
        	
}
