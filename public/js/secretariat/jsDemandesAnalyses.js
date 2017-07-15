var base_url = window.location.toString();
var tabUrl = base_url.split("public");

//BOITE DE DIALOG POUR LA CONFIRMATION DE SUPPRESSION
function visualisation(id){
  $( "#visualisation" ).dialog({
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

$(function(){
	setTimeout(function() {
		infoBulle();
	}, 1000);
});

function infoBulle(){
	
	/***
	 * INFO BULLE DE LA LISTE
	 */

	var tooltips = $( 'table tbody tr td a' ).tooltip({show: {effect: 'slideDown', delay: 250}});
	tooltips.tooltip( 'close' );
	$('table tbody tr td a').mouseenter(function(){
		var tooltips = $( 'table tbody tr td a' ).tooltip({show: {effect: 'slideDown', delay: 250}});
		tooltips.tooltip( 'open' );
	});
}

function visualiser(id){
	visualisation(id);
	var cle = id;
	var chemin = tabUrl[0]+'public/secretariat/popup-vue-patient';
	$.ajax({
         type: 'POST',
         url: chemin ,
         data:'id='+cle,
         success: function(data) {    
         	    var result = jQuery.parseJSON(data);   
         	     $("#info").html(result);
         	     
         	     $("#visualisation").dialog('open'); 
         	       
         }
     });
}

var diagnostic_demande = "";
var temoinExisteADA = 0;
var nbEntrees = 0;

function demandesAnalyses(id){
	
	$("#idpatient").val(id);
	
    $("#titre span").html("DEMANDES D'ANALYSES");	

    var chemin = tabUrl[0]+'public/secretariat/demandes-analyses-vue';
    $.ajax({
        type: 'POST',
        url: chemin ,
        data:'id='+id,
        success: function(data) {    
        	    var result = jQuery.parseJSON(data);  
        	    
        	    var vuePatient = result[0];
        	    var existeADA = result[1];
        	    var listeAnalysesDemandees = result[2];
        	    var tabTypesAnalyses = result[3];
        	    var tabListeAnalysesParType = result[4];
        	    
        	    var myArrayTypeAnalyse = [''];
        	    for(var i=1 ; i<=tabTypesAnalyses.length ; i++){
        	    	myArrayTypeAnalyse[i] = tabTypesAnalyses[i];
        	    }
        	    
        	    if(existeADA == 1){ 
        	    	$('LesActes').remove();
        	    	$("#montantTotal span").html("");
        	    	diagnostic_demande = ""; $('#diagnostic_demande_text').val("");
        	    	$('#verifModifier').val(1);
        	    	temoinExisteADA = 1;
        	    	
        	    	var nbAnalyses = listeAnalysesDemandees.length;
        	    	partDefautActe(myArrayTypeAnalyse, nbAnalyses);
        	    	
        	    	chargementModificationAnalyses(listeAnalysesDemandees, tabListeAnalysesParType);
        	    	
        	    }else{
        	    	if(temoinExisteADA == 1){ 
           	    	    $('LesActes').remove();
           	    	    $("#montantTotal span").html("");
           	    	    diagnostic_demande = ""; $('#diagnostic_demande_text').val("");
           	    	    $('#verifModifier').val(0);
           	    	    temoinExisteADA = 0; nbEntrees = 1;
           	    	    
            	    	partDefautActe(myArrayTypeAnalyse, 1);
            	    	$('#bouton_Acte_modifier_demande button').trigger('click');
        	    	}
        	    	if(nbEntrees == 0){
        	    		$('#verifModifier').val(0);
        	    		partDefautActe(myArrayTypeAnalyse, 1);
        	    		nbEntrees = 1;
        	    	}
        	    }
        	    
        	     $("#vue_patient").html(vuePatient);
        	     
        	     //PASSER A SUIVANT
        	     $('#demandesAnalyses').animate({
        	         height : 'toggle'
        	      },1000);
        	     $('#contenu').animate({
        	         height : 'toggle'
        	     },1000);
        	     
        	     
        	     //PRECEDENT --- PRECEDENT --- PRECEDENT --- PRECEDENT
        	     $('#precedent').click(function(){
        	    	$("#titre span").html("RECHERCHER LE PATIENT");	
        	    	$('#contenu').animate({
        	            height : 'toggle'
        	         },1000);
        	         $('#demandesAnalyses').animate({
        	            height : 'toggle'
        	         },1000);
        	         return false;
        	    });
        }
    });
    
    $("#annuler").click(function(){
	    vart = tabUrl[0]+'public/secretariat/demandes-analyses';
	    $(location).attr("href",vart);
        return false;
    });
    
}

var  oTable;
function initialisation(){
	$( "#accordions" ).accordion();
	$( "button" ).button();
	$( "#demandesAnalyses" ).toggle(false);
    
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

		"sAjaxSource": ""+tabUrl[0]+"public/secretariat/liste-recherche-patient-ajax", 

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
	
	$(".boutonAnnuler").html('<button type="submit" id="annuler" style=" font-family: police2; font-size: 17px; font-weight: bold;"> Annuler </button>');
	$(".boutonTerminer").html('<button type="submit" id="terminer" style=" font-family: police2; font-size: 17px; font-weight: bold;"> Terminer </button>');
}


function clickRowHandler() 
{
	var id;
	$('#patient tbody tr').contextmenu({
		target: '#context-menu',
		onItem: function (context, e) {
			
			if($(e.target).text() == 'Visualiser' || $(e.target).is('#visualiserCTX')){
				visualiser(id);
			} else 
				if($(e.target).text() == 'Suivant' || $(e.target).is('#suivantCTX')){
					demandesAnalyses(id);
				}
			
		}
	
	}).bind('mousedown', function (e) {
			var aData = oTable.fnGetData( this );
		    id = aData[6];
	});
	
	
	
	$("#patient tbody tr").bind('dblclick', function (event) {
		var aData = oTable.fnGetData( this );
		var id = aData[6];
		visualiser(id);
	});
	
}


function ajouterPatient()
{
	vart = tabUrl[0]+'public/secretariat/ajouter-patient';
    $(location).attr("href",vart);
    return false;
}


function modification() {
	diagnostic_demande = $('#diagnostic_demande_text').val();
}

function popupFermer() {
	$(null).w2overlay(null);
	diagnostic_demande = "";
	$('#diagnostic_demande_text').val("");
}

function popupTerminer() {
	$(null).w2overlay(null);
}

function diagnostic(id)
{

	$('#diagnostic_'+id).w2overlay({ html: "" +
		"" +
		"<div style='border-bottom:1px solid green; height: 30px; background: #f9f9f9; width: 300px; text-align:center; padding-top: 10px; font-size: 13px; color: green; font-weight: bold;'><img style='padding-right: 10px;' src='"+tabUrl[0]+"public/images_icons/detailsd.png' >Diagnostic</div>" +
		"<div style='height: 195px; width: 300px; padding-top:10px; text-align:center;'>" +
		"<textarea style='height: 77%; width: 95%; max-height: 77%; max-width: 95%;' id='diagnostic_demande' > </textarea>" +
		"<button class='btn' style='cursor:pointer; margin-top: 7px;' onclick='popupFermer(); return false;'>Annuler</button>" +
		"<button class='btn' style='cursor:pointer; margin-top: 7px;' onclick='popupTerminer(); return false;'>Terminer</button>" +
		"</div>" +
		"<script> $('#diagnostic_demande').val(diagnostic_demande); </script>" 
	});
	
	$("#diagnostic_demande").keyup( function () {
		diagnostic_demande = $("#diagnostic_demande").val();
		$('#diagnostic_demande_text').val(diagnostic_demande);
	}).change(function(){
		diagnostic_demande = $("#diagnostic_demande").val();
		$('#diagnostic_demande_text').val(diagnostic_demande);
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