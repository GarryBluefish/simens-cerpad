 var base_url = window.location.toString();
 var tabUrl = base_url.split("public");

 /***************************************************************
  *========= AFFICHAGE DES INFORMATIONS SUR L'AGENT =============
  ***************************************************************
  **************************************************************/
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
            return false;
        }
   }
  });
}

function affichervue(id){
	 confirmation(id);
	 var cle = id;
     var chemin = tabUrl[0]+'public/personnel/popup-agent-personnel';
     $.ajax({
         type: 'POST',
         url: chemin ,
         data: $(this).serialize(),  
         data:'id='+cle,
         success: function(data) {    
         	    var result = jQuery.parseJSON(data);  
         	     $("#info").html(result);
         	     $("#confirmation").dialog('open');
         },
         error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
         dataType: "html"
     });
}

/***************************************************************
 *============ INITIALISATION DU SCRIPT DATA TABLE =============
 ***************************************************************
 **************************************************************/
function initialisation(){
    var asInitVals = new Array();
    var  oTable = $('#personnel').dataTable
	( {
					"sPaginationType": "full_numbers",
					"aLengthMenu": [5,7,10,15],
					"iDisplayLength": 7,
					"oLanguage": {
						"sProcessing":   "Traitement en cours...",
						"sZeroRecords":  "Aucun &eacute;l&eacute;ment &agrave; afficher",
						"sInfo": "_START_ &agrave; _END_ sur _TOTAL_ &eacute;l&eacute;ments",
						"sInfoEmpty": "0 &eacute;l&eacute;ment &agrave; afficher",
						"sInfoFiltered": "",
						"sInfoPostFix":  "",
						"sUrl": "",
						"oPaginate": {
							"sFirst":    "|<",
							"sPrevious": "<",
							"sNext":     ">",
							"sLast":     ">|"
							}
					   },

					"sAjaxSource": ""+tabUrl[0]+"public/personnel/liste-personnel-intervention-ajax", 
					
	}); 

	//le filtre du select du type personnel
    $('#type_personnel').change(function() 
    {					
	   oTable.fnFilter( this.value );
    });
	
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
	
	$("table thead tr th input").click(function(){
		return false;
	});
	
	
}


/***************************************************************
 *============ ANIMATION -- CHANGEMENT DES INTERFACES ==========
 ***************************************************************
 **************************************************************/
function animation(){

$('#ajouter_naissance').toggle(false);

$('#precedent').click(function(){
	$("#titre2").replaceWith("<div id='titre' style='font-family: police2; color: green; font-size: 20px; font-weight: bold; padding-left: 35px;'><iS style='font-size: 25px;'>&curren;</iS> LE PERSONNEL</div>");	
    
	$('#ajouter_naissance').animate({
        height : 'toggle'
     },1000);
	$('#contenu').animate({
        height : 'toggle'
    },1000);
    
     
     $("#terminer").replaceWith("<button id='terminer' style='height:35px;'>Terminer</button>");
     
     return false;
});
}

/***************************************************************
 *========= TRANSFERT -- CHANGEMENT DES INTERFACES =============
 ***************************************************************
 **************************************************************/
function intervention(id){
	
    $("#titre").replaceWith("<div id='titre2' style='font-family: police2; color: green; font-size: 20px; font-weight: bold; padding-left: 35px;'><iS style='font-size: 25px;'>&curren;</iS> TRANSF&Eacute;RER L'AGENT </div>");	

    var cle = id;
    var chemin =  tabUrl[0]+'public/personnel/vue-agent-personnel';
    $.ajax({
        type: 'POST',
        url: chemin ,
        data:'id='+cle,
        success: function(data) {    
        	    var result = jQuery.parseJSON(data);  
        	     $("#id_personne").val(id);
        	     $("#info_personnel").html(result);
        	     //PASSER A SUIVANT
        	     $('#ajouter_naissance').animate({
        	         height : 'toggle'
        	      },1000);
        	     $('#contenu').animate({
        	         height : 'toggle'
        	     },1000);
        },
        error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
        dataType: "html"
    });

/***************************************************************
 *========= BOUTON ANNULER -- Liste de recherche   =============
 ***************************************************************
 ***************************************************************/
    //Annuler l'enregistrement d'une naissance
    $("#annuler").click(function(){
    	$("#annuler").css({"border-color":"#ccc", "background":"-webkit-linear-gradient( #555, #CCC)", "box-shadow":"1px 1px 10px black inset,0 1px 0 rgba( 255, 255, 255, 0.4)"});
    	vart=  tabUrl[0]+'public/personnel/intervention';
        $(location).attr("href",vart);
        return false;
    });
    
	$('#date_debut, #date_fin, #date_debut_externe, #date_fin_externe').datepicker($.datepicker.regional['fr'] = {
			closeText: 'Fermer',
			changeYear: true,
			yearRange: 'c-80:c',
			prevText: '&#x3c;Préc',
			nextText: 'Suiv&#x3e;',
			currentText: 'Courant',
			monthNames: ['Janvier','Fevrier','Mars','Avril','Mai','Juin',
			'Juillet','Aout','Septembre','Octobre','Novembre','Decembre'],
			monthNamesShort: ['Jan','Fev','Mar','Avr','Mai','Jun',
			'Jul','Aout','Sep','Oct','Nov','Dec'],
			dayNames: ['Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'],
			dayNamesShort: ['Dim','Lun','Mar','Mer','Jeu','Ven','Sam'],
			dayNamesMin: ['Di','Lu','Ma','Me','Je','Ve','Sa'],
			weekHeader: 'Sm',
			dateFormat: 'dd/mm/yy',
			firstDay: 1,
			isRTL: false,
			showMonthAfterYear: false,
			yearRange: '1990:2015',
			showAnim : 'bounce',
			changeMonth: true,
			changeYear: true,
			yearSuffix: ''
	});
}

/***************************************************************
 *======= TYPE DE TRANSFERT -- AFFICHAGE DE L'INTERFACE ========
 ***************************************************************
 **************************************************************/
function getChamps(id){

	if(id=="Interne"){
		$("#vider_champ_externe").toggle(false);
		$("#vider_champ_interne").toggle(true);
		
		$(".intervention_externe").toggle(false);
		$(".intervention_interne").toggle(true);
		
		$("#id_service").attr({'required': true});
		$("#date_debut").attr({'required': true});
		$("#date_fin").attr({'required' : true});
		$("#motif_intervention").attr({'required': true});
		
		$("#id_service_externe").attr({'required': false});
		$("#date_debut_externe").attr({'required': false});
		$("#date_fin_externe").attr({'required' : false});
		$("#motif_intervention_externe").attr({'required': false});

	}
	else
		if(id=="Externe"){
			$("#vider_champ_interne").toggle(false);
			$("#vider_champ_externe").toggle(true);
			
			$(".intervention_interne").toggle(false);
			$(".intervention_externe").toggle(true);
			
			$("#id_service_externe").attr({'required': true});
			$("#date_debut_externe").attr({'required': true});
			$("#date_fin_externe").attr({'required' : true});
			$("#motif_intervention_externe").attr({'required': true});
			
			$("#id_service").attr({'required': false});
			$("#date_debut").attr({'required': false});
			$("#date_fin").attr({'required' : false});
			$("#motif_intervention").attr({'required': false});
			
		}
	
	return false;
}

/***************************************************************
 *======= LISTE DES SERVICES D'UN HOPITAL -- ===================
 ***************************************************************
 **************************************************************/
function getservices(cle)
{    
     $.ajax({
        type: 'POST',
        url:  tabUrl[0]+'public/personnel/services',
        data: 'id='+cle,
        success: function(data) {
            var result = jQuery.parseJSON(data);
            $("#id_service_externe").html(result);
      },
      error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
        dataType: "html"
    });

    return false;
 }