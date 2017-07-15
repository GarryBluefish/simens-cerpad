    var base_url = window.location.toString();
    var tabUrl = base_url.split("public");

    /************************************************************************************************************************/
    /************************************************************************************************************************/
    /************************************************************************************************************************/
    $(function(){
    	initialisation();
    	setTimeout(function() {
    		infoBulle();
    	}, 1000);
    });
    
    function infoBulle(){
    	/***
    	 * INFO BULLE DE LA LISTE
    	 */
    	 var tooltips = $( 'table tbody tr td infoBulleVue' ).tooltip({show: {effect: 'slideDown', delay: 250}});
 	     tooltips.tooltip( 'close' );
    	  $('table tbody tr td infoBulleVue').mouseenter(function(){
    	    var tooltips = $( 'table tbody tr td infoBulleVue' ).tooltip({show: {effect: 'slideDown', delay: 250}});
    	    tooltips.tooltip( 'open' );
    	  });
    }
    
    var  oTable
    
    function initialisation(){
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

    					"sAjaxSource": ""+tabUrl[0]+"public/consultation/liste-patient-encours-ajax", 
    					"fnDrawCallback": function() 
    					{
    						//markLine();
    						clickRowHandler();
    					}
    					
    	}); 
        
        var asInitVals = new Array();
    
   	//le filtre du select
   	$('#filter_statut').change(function() 
   	{					
   		oTable.fnFilter( this.value );
   	});

   	//le filtre du select du type personnel
	$('#type_personnel').change(function() 
	{					
		oTable.fnFilter( this.value );
	});
   	
   	$("tfoot input").keyup( function () {
   		/* Filter on the column (the index) of this element */
   		oTable.fnFilter( this.value, $("tfoot input").index(this) );
   	} );
   	
   	/*
   	 * Support functions to provide a little bit of 'user friendlyness' to the textboxes in 
   	 * the footer
   	 */
   	$("tfoot input").each( function (i) {
   		asInitVals[i] = this.value;
   	} );
   	
   	$("tfoot input").focus( function () {
   		if ( this.className == "search_init" )
   		{
   			this.className = "";
   			this.value = "";
   		}
   		
   	} );
   	
   	$("tfoot input").blur( function (i) {
   		if ( this.value == "" )
   		{
   			this.className = "search_init";
   			this.value = asInitVals[$("tfoot input").index(this)];
   		}
   	} );

    $("#terminersoin").click(function(){
    	var tooltips = $("#medicament, #voie_administration, #frequence, #dosage, #date_application, #heure_recommandee_, #duree").tooltip();
    	tooltips.tooltip( "close" );
    	$("#medicament, #voie_administration, #frequence, #dosage, #date_application, #heure_recommandee_, #duree").attr({'title':''});
    	
    	$("#titre").html("<iS style='font-size: 25px;'>&curren;</iS> LISTE DES PATIENTS");
    	
    	$('#listeDataTable').css({'margin-left' : '-10'});
    	
 		$("#medicament, #voie_administration, #frequence, #dosage, #date_application, #heure_recommandee_, #duree").css("border-color","");
 		$("#medicament, #voie_administration, #frequence, #dosage, #date_application, #heure_recommandee_, #duree").val('');
 		
 		LaDuree = 0;
    	//POUR LA SUPPRESSION DES ELEMENTS SELECTIONNES SUR LA LISTE
    	for(var j = 0; j < 24; j++){
    		$('.SlectBox')[0].sumo.unSelectItem(j);
    	}
    	//POUR LA SUPPRESSION DES ICONES COCHES SUR LA LISTE
    	$(function(){
            $('select.SlectBox')[0].sumo.unload();
            $('.SlectBox').SumoSelect({ csvDispCount: 6 });
           });
    	
    	
    	vart=tabUrl[0]+'public/consultation/en-cours';
        $(location).attr("href",vart);
    	
	    return false;
	});
    
    listepatient();
    }
    
    

    function clickRowHandler() 
    {
    	var id;
    	var terminer; //Pour les hospitalisations termin�e
    	
    	$('#patient tbody tr').contextmenu({
    		target: '#context-menu',
    		onItem: function (context, e) {
    			
    			if($(e.target).text() == 'Détails' || $(e.target).is('#detailsCTX')){

    				if(terminer == 0){
        				affichervue(id);
    				} else if(terminer == 1){
    					affichervuedetailhospi(id);
    				}

    			} else 
    				if($(e.target).text() == 'Administrer' || $(e.target).is('#administrerCTX')){
    					administrerSoin(id);
    				} else 
        				if($(e.target).text() == 'Libérer' || $(e.target).is('#libererCTX')){
        					liberer(id);
        				}
    			
    		}
    	
    	}).bind('mousedown', function (e) {
    			var aData = oTable.fnGetData( this );
    		    id = aData[8];
    		    terminer = aData[9];
    		    
    		    if(terminer == 1){
    		    	$('.lien1, .divider2').toggle(false);
    		    } else if(terminer == 0){
    		    	$('.lien1, .divider2').toggle(true);
    		    }
    		    
    	});
    	
    	
    	
    	$("#patient tbody tr").bind('dblclick', function (event) {
    		var aData = oTable.fnGetData( this );
    		var id = aData[8];
    		terminer = aData[9];
    		if(terminer == 0){
				affichervue(id);
			} else if(terminer == 1){
				affichervuedetailhospi(id);
			}
    	});
    	
    }
    
    /************************************************************************************************************************/
    /************************************************************************************************************************/
    /************************************************************************************************************************/
    function affichervue(id_demande_hospi){ 
    	var id_cons = $("#"+id_demande_hospi).val();
    	var id_personne = $("#"+id_demande_hospi+"idPers").val();
    	var chemin = tabUrl[0]+'public/consultation/info-patient';
        $.ajax({
            type: 'POST',
            url: chemin ,
            data:{'id_personne':id_personne, 'id_cons':id_cons, 'encours':111, 'id_demande_hospi':id_demande_hospi},
            success: function(data) {
           	         
            	$("#titre").html("<iS style='font-size: 25px;'>&curren;</iS> INFORMATIONS");
            	var result = jQuery.parseJSON(data);
            	$("#contenu").fadeOut(function(){$("#vue_patient").html(result).fadeIn("fast"); }); 
            	     
            },
            error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
            dataType: "html"
        });
     }
    
    function listepatient(){
	    $("#terminerVisualisationHosp").click(function(){
	    	$("#titre").html("<iS style='font-size: 25px;'>&curren;</iS> LISTE DES PATIENTS");
  	    	$("#vue_patient").fadeOut(function(){
  	    		$("#contenu").fadeIn("fast"); 
  	    	});
  	    	
//  	    	return false;
  	    });
	    
	    $('#date_recommandee, #date_recommandee_m, #date_application, #date_application_m').datepicker($.datepicker.regional['fr'] = {
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
				minDate : '0',
				yearRange: '1990:2020',
				showAnim : 'bounce',
				changeMonth: true,
				changeYear: true,
				yearSuffix: ''
		});
	   
	    
	    $("#terminerLiberer").click(function(){
	    	$("#titre").html("<iS style='font-size: 25px;'>&curren;</iS> LISTE DES PATIENTS");
  	    	$("#vue_liberer_patient").fadeOut(function(){
  	    		$("#contenu").fadeIn("fast"); 
  	    	});
  	    	return false;
  	    });
	    
	    $("#terminerdetailhospi").click(function(){
	    	$("#titre").html("<iS style='font-size: 25px;'>&curren;</iS> LISTE DES PATIENTS");
	    	$("#vue_detail_hospi_patient").fadeOut(function(){$("#contenu").fadeIn(100); });
	    	
	    	return false;
  	    });
	    
	    //Pour la lib�ration du patient en cours d'hospitalisation
	    //Pour la lib�ration du patient en cours d'hospitalisation
	    //Pour la lib�ration du patient en cours d'hospitalisation
	    //Pour la lib�ration du patient en cours d'hospitalisation
	    $("#liberer").click(function(){
	    	if($("#resumer_medical").val()=="" || $("#motif_sorti").val()==""){
	    		return true; //Pour afficher le message de champ requis
	    	} else {
	    		ConfirmationLiberationPopup();
				$("#confirmationDeLaLiberation").dialog('open'); 
	    		return false;
	    	}
	    });
	    
     }
    
	 function ConfirmationLiberationPopup(){
    	$( "#confirmationDeLaLiberation" ).dialog({
    	    resizable: false,
    	    height:260,
    	    width:390,
    	    autoOpen: false,
    	    modal: true,
    	    buttons: {
    	   
   	            "Annuler": function() {
   	               $( this ).dialog( "close" );             	     
   	               return false;
   	            },
    	
    	        "Terminer": function() {
    	        	var formulaire = document.getElementById("Formulaire_Liberer_Patient");
    	        	formulaire.submit();
        	       
    	        	$( this ).dialog( "close" );             	     
    	        	return false;
                }
    	       
    	    }
    	});
	 }
	  
	 
	 function PrescriptionOrdonnancePopup(){
	    	$( "#PrescriptionOrdonnancePopupInterface" ).dialog({
	    	    resizable: false,
	    	    height:460,
	    	    width:930,
	    	    autoOpen: false,
	    	    modal: true,
	    	    buttons: {
	    	   
	   	            "Annuler": function() {
	   	               $( this ).dialog( "close" );  
	   	               
	   	               ConfirmationLiberationPopup();
					   $("#confirmationDeLaLiberation").dialog('open'); 
					   
	   	               return false;
	   	            },
	    	
	    	        "Terminer": function() {
	    	        	$( this ).dialog( "close" );   
	    	        	
	    	        	var id_personne = $('#id_personneForOrdonnance').val();
	    	        	var id_cons = $('#id_consForOrdonnance').val();
	    	        	
	    	        	var formulaireImprimerOrdonnance = document.getElementById("Formulaire_Imprimer_Ordonnance");
	    	        	
	    	        	var champ1 = document.createElement("input");
	    	        	champ1.setAttribute("type", "hidden");
	    	        	champ1.setAttribute("name", "id_patient");
	    	        	champ1.setAttribute("value", id_personne);
	    	        	formulaireImprimerOrdonnance.appendChild(champ1);
	    	        	
	    	        	var champ2 = document.createElement("input");
	    	        	champ2.setAttribute("type", "hidden");
	    	        	champ2.setAttribute("name", "id_cons");
	    	        	champ2.setAttribute("value", id_cons);
	    	        	formulaireImprimerOrdonnance.appendChild(champ2);
	    	        	
	    	        	var champ3 = document.createElement("input");
	    	        	champ3.setAttribute("type", "hidden");
	    	        	champ3.setAttribute("name", "temoin_ordonnance");
	    	        	champ3.setAttribute("value", 1);
	    	        	formulaireImprimerOrdonnance.appendChild(champ3);
	    	        	
	    	        	$("#ordonnance").trigger('click');
	    	        	
	    	        	setTimeout(function(){
	    	        		var formulaireLibererPatient = document.getElementById("Formulaire_Liberer_Patient");
	    	        		
	    	        		var elementsFormulaireImprimerOrdonnance = document.getElementById("Formulaire_Imprimer_Ordonnance");
	    	        		for(var cpt=0 ; cpt<elementsFormulaireImprimerOrdonnance.length; cpt++) {
	    	                    var element = elementsFormulaireImprimerOrdonnance[cpt];
	    	                    
	    	                    var champ = document.createElement("input");
	    	    	        	champ.setAttribute("type", "hidden");
	    	    	        	champ.setAttribute("name", element.name);
	    	    	        	champ.setAttribute("value", element.value);
	    	    	        	formulaireLibererPatient.appendChild(champ);
	    	                }
	    	        		
	    	        		formulaireLibererPatient.submit();
	    	        	}, 1000);
	    	        	          	     
	    	        	return false;
	                }
	    	       
	    	    }
	    	});
	}
	 
	 
	 function AffichageOrdonnancePopup(){
	    	$( "#PrescriptionOrdonnancePopupInterface" ).dialog({
	    	    resizable: false,
	    	    height:460,
	    	    width:930,
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
	 

	 
	/*************************************************************************************************************************/
    /*************************************************************************************************************************/
    /*************************************************************************************************************************/
    function getsalle(id_batiment){
      var chemin = tabUrl[0]+'public/hospitalisation/salles';
      $.ajax({
        type: 'POST',
        url: chemin ,
        data:'id_batiment='+id_batiment,
        success: function(data) {
        	     var result = jQuery.parseJSON(data);  
        	     $("#salle").html(result); 
        },
        error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
        dataType: "html"
      });
    }
    
    function getlit(id_salle){ 
    	var chemin = tabUrl[0]+'public/hospitalisation/lits';
    	$.ajax({
            type: 'POST',
            url: chemin ,
            data:'id_salle='+id_salle,
            success: function(data) {
            	     var result = jQuery.parseJSON(data);  
            	     $("#lit").html(result); 
            },
            error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
            dataType: "html"
        });
    }
    
    /************************************************************************************************************************/
    /************************************************************************************************************************/
    /************************************************************************************************************************/
    function administrerSoin(id_demande_hospi){
    	var id_hosp = $("#"+id_demande_hospi+"hp").val(); 
    	AppelListeExamensDuJoursHospi(id_hosp);
    	
    	var id_personne = $("#"+id_demande_hospi+"idPers").val();
    	var chemin = tabUrl[0]+'public/consultation/info-patient-hospi';
        $.ajax({
            type: 'POST',
            url: chemin ,
            data:{'id_personne':id_personne, 'administrerSoin':111},
            success: function(data) {
           	         
            	$("#titre").html("<iS style='font-size: 25px;'>&curren;</iS> ADMINISTRER DES SOINS");
            	var result = jQuery.parseJSON(data);
            	$("#vue_patient_hospi").html(result);
            	$("#division,#salle,#lit").val("");
            	$("#code_demande").val($("#"+id_demande_hospi+"dh").val());
            	listeSoinsPrescrits(id_hosp);
            	
            	$("#vue_detail_hospi_patient").html("");
            	$("#contenu").fadeOut(function(){
            		 $("#hospitaliser").toggle(true);
            		 
            		 $("#listeDesExamens").css({'height':'340px'});
            		 $("#accordionListeDesPlaintesEtExamensDuJour").css({'height':'340px'});
            		 $("#accordionListeDeToutesLesConstantes").css({'height':'350px'});
            		 $("#accordionDonneesExamensPhysiques").css({'height':'350px'});
            		 $("#accordionExamensComplementaires").css({'height':'380px'});
            		    $("#accordionExamensBiologiques").css({'height':'270px'});
            		    $("#accordionExamensMorphologiques").css({'height':'250px'});
            		 $("#accordionTransfert").css({'height':'300px'});
            	}); 
            },
            error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
            dataType: "html"
        });
        
        $('#id_soins').val('');
        $('#id_hosp').val(id_hosp);
        $('#id_personne').val(id_personne);
        $('#id_demande_hospi').val(id_demande_hospi);
        
        $('#lebererPatientHospi'+id_demande_hospi).click(function(){
        	$('#vue_patient').html("");
        	LibererPourTransfererPatient(id_demande_hospi);
        });
	    controle_saisie();
    }
    
    function listeSoinsPrescrits(id_hosp) { 
    	var chemin = tabUrl[0]+'public/consultation/liste-soins-prescrits';
        $.ajax({
            type: 'POST',
            url: chemin ,
            data:{'id_hosp': id_hosp},
            success: function(data) {
            	var result = jQuery.parseJSON(data);
            	$("#liste_soins").html(result); 
            	     
            },
            error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
            dataType: "html"
        });
        
    }
    
    function listeSoins(id_hosp) { 
    	var chemin = tabUrl[0]+'public/consultation/liste-soin';
        $.ajax({
            type: 'POST',
            url: chemin ,
            data:{'id_hosp': id_hosp},
            success: function(data) {
            	var result = jQuery.parseJSON(data);
            	$("#liste_soins").html(result); 
            	     
            },
            error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
            dataType: "html"
        });
        
    }
    
    function listeDesSoins() {
    	var  oTable = $('#listeSoin').dataTable
     	( {
     					"sPaginationType": "full_numbers",
     					"aLengthMenu": [3,5,7],
     					"iDisplayLength": 3,
     					"aaSorting": [], //On ne trie pas la liste automatiquement
     					"oLanguage": {
     						//"sInfo": "_START_ &agrave; _END_ sur _TOTAL_ &eacute;l&eacute;ments",
     						//"sInfoEmpty": "0 &eacute;l&eacute;ment &agrave; afficher",
     						"sInfoFiltered": "",
     						"sUrl": "",
     						"oPaginate": {
     							"sFirst":    "|<",
     							"sPrevious": "<",
     							"sNext":     ">",
     							"sLast":     ">|"
     							}
     					   },
     	});

    	//POUR LE FILTRE DES SOINS
    	$('#afficherEncours').css({'font-weight':'bold', 'font-size': '17px' });
    	oTable.fnFilter( 'soin_encours' );
    	
    	$('#afficherEncours').click(function(){
    		oTable.fnFilter( 'soin_encours' );
    		$('#afficherEncours').css({'font-weight':'bold', 'font-size': '17px' });
    		$('#afficherTerminer').css({'font-weight':'normal', 'font-size': '15px' });
    		$('#afficherAvenir').css({'font-weight':'normal', 'font-size': '15px'});
    	});

    	$('#afficherTerminer').click(function(){
    		oTable.fnFilter( 'soin_terminer' );
    		$('#afficherTerminer').css({'font-weight':'bold', 'font-size': '17px' });
    		$('#afficherEncours').css({'font-weight':'normal', 'font-size': '15px'});
    		$('#afficherAvenir').css({'font-weight':'normal', 'font-size': '15px'});
    	});
    	
    	$('#afficherAvenir').click(function(){
    		oTable.fnFilter( 'soin_avenir' );
    		$('#afficherAvenir').css({'font-weight':'bold', 'font-size': '17px' });
    		$('#afficherEncours').css({'font-weight':'normal', 'font-size': '15px'});
    		$('#afficherTerminer').css({'font-weight':'normal', 'font-size': '15px'});
    	});
    	
    }
    
    function vider_tout() {
    	$('#medicament, #voie_administration, #frequence, #dosage, #date_application, #motif_, #note_, #duree').val('');

    	LaDuree = 0;
    	//POUR LA SUPPRESSION DES ELEMENTS SELECTIONNES SUR LA LISTE
    	for(var j = 0; j < 24; j++){
    		$('.SlectBox')[0].sumo.unSelectItem(j);
    	}
    	//POUR LA SUPPRESSION DES ICONES COCHES SUR LA LISTE
    	$(function(){
            $('select.SlectBox')[0].sumo.unload();
            $('.SlectBox').SumoSelect({ csvDispCount: 6 });
           });
    }
    
    /*************************************************************************************************************************/
    /*************************************************************************************************************************/
    /*************************************************************************************************************************/
    function confirmation(){
	  $( "#confirmation" ).dialog({
	    resizable: false,
	    height:170,
	    width:370,
	    autoOpen: false,
	    modal: true,
	    buttons: {
	        "Oui": function() {
	            $( this ).dialog( "close" );
	            
	            	var chemin = tabUrl[0]+'public/consultation/en-cours';
	            	var id_sh = $('#id_sh').val();
	            	var id_hosp = $('#id_hosp').val();
	            	
	            	var medicament = $('#medicament').val();
	            	var voie_administration = $('#voie_administration').val();
	            	var frequence = $('#frequence').val();
	            	var dosage = $('#dosage').val();
	            	var date_application = $('#date_application').val();
	            	var duree = $('#duree').val();
	            	var heure_recommandee = $('#heure_recommandee_').val();
	            	var motif = $('#motif_').val();
	            	var note = $('#note_').val();

	            	$.ajax({
	                    type: 'POST',
	                    url: chemin ,
	                    data:{'id_sh':id_sh, 'id_hosp':id_hosp, 
	                    	  'medicament':medicament, 'voie_administration':voie_administration, 
	                    	  'frequence':frequence, 'dosage':dosage, 'date_application':date_application,
	                    	  'heure_recommandee':heure_recommandee, 'motif': motif, 'note':note, 'duree':duree
	                    	  },
	                    success: function() {
	                    	 listeSoinsPrescrits(id_hosp);
	                    	 vider_tout();
	                    },
	                    error:function(e){ console.log(e);alert("Une erreur interne est survenue!"); },
	                    dataType: "html"
	                });
	            	
	        },
	        "Annuler": function() {
                $( this ).dialog( "close" );
            }
	   }
	  });
    }
    
    /**
     * Lors d'un hover
     */
    function hover() {
    	$('#medicament, #voie_administration, #frequence, #dosage, #date_application, #heure_recommandee_, #duree').hover(function(){
    		$("#medicament, #voie_administration, #frequence, #dosage, #date_application, #heure_recommandee_, #duree").attr({'title':''});
    		var tooltips = $( "#medicament, #voie_administration, #frequence, #dosage, #date_application, #heure_recommandee_, #duree" ).tooltip();
    		tooltips.tooltip( "hide" );
    	});
    }
    
    /**
     * Lors d'un click
     */
    function click() {
    	$('#titre_info_admis, #medicament, #voie_administration, #frequence, #dosage, #date_application, #heure_recommandee_, #duree, #fleche_plus, #fleche_moins').click(function(){
			var tooltips = $( "#medicament, #voie_administration, #frequence, #dosage, #date_application, #heure_recommandee_, #duree" ).tooltip();
			tooltips.tooltip( "close" );
			$("#medicament, #voie_administration, #frequence, #dosage, #date_application, #heure_recommandee_, #duree").attr({'title':''});
	    });
    	
    	$('#date_application, #duree, #fleche_plus').mouseover(function(){
    		var tooltips = $( "#date_application, #duree" ).tooltip();
			tooltips.tooltip( "hide" );
    	});
    }
    
    function ajouter() {
    	
    	hover();
    	click();
    	
		$("#medicament, #voie_administration, #frequence, #dosage, #date_application, #heure_recommandee_").css("border-color","");
    	if(!$('#medicament').val()){
    		$("#medicament").css("border-color","#FF0000");
    		$('#medicament').attr({'title': 'Veuillez ajouter un médicament'});
    			var tooltips = $( "#medicament" ).tooltip();
    			tooltips.tooltip( "open" );
    			
    	}else if(!$('#voie_administration').val()){
    		$("#voie_administration").css("border-color","#FF0000");
    		$('#voie_administration').attr({'title': 'Ce champ est requis'});
    			var tooltips = $( "#voie_administration" ).tooltip();
    			tooltips.tooltip( "open" );
    			
        		$("#medicament").css("border-color","");
        		
    	}else if(!$('#frequence').val()){
    		$("#frequence").css("border-color","#FF0000");
    		$('#frequence').attr({'title': 'Ce champ est requis'});
    			var tooltips = $( "#frequence" ).tooltip();
    			tooltips.tooltip( "open" );
    			
        		$("#voie_administration").css("border-color","");
        		
    	}else if(!$('#dosage').val()){
    		$("#dosage").css("border-color","#FF0000");
    		$('#dosage').attr({'title': 'Ce champ est requis'});
    			var tooltips = $( "#dosage" ).tooltip();
    			tooltips.tooltip( "open" );
    			
        		$("#frequence").css("border-color","");
        		
    	}else if(!$('#date_application').val()){
    		$("#date_application").css("border-color","#FF0000");
    		$('#date_application').attr({'title': 'Ce champ est requis'});
    			var tooltips = $( "#date_application" ).tooltip();
    			tooltips.tooltip( "open" );
    			
        		$("#dosage").css("border-color","");
        		
    	}else if(!$('#duree').val()){
    		$("#duree").css("border-color","#FF0000");
    		$('#duree').attr({'title': 'Ce champ est requis'});
    			var tooltips = $( "#duree" ).tooltip();
    			tooltips.tooltip( "open" );
    			
        		$("#date_application").css("border-color","");
    	}
    	else if(!$('#heure_recommandee_').val()){
    		$("#heure_recommandee_").css("border-color","#FF0000");
    		$('#heure_recommandee_').attr({'title': 'Ce champ est requis'});
    			//var tooltips = $( "#heure_recommandee_" ).tooltip();
    			//tooltips.tooltip( "open" );
    			
        		$("#duree").css("border-color","");
        		
    	}else {
    			confirmation();
    			$("#confirmation").dialog('open');
    	}

   	}
    

    /************************************************************************************************************************/
    /************************************************************************************************************************/
    /************************************************************************************************************************/
    	function vueSoinAppliquer(x, y){
        	$( "#informations" ).dialog({
        	    resizable: false,
        	    width: x, 
        	    height: y, 
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
        
        function vuesoin(id_sh){
        	vueSoinAppliquer(750, 650);
            var chemin = tabUrl[0]+'public/consultation/vue-soin-appliquer';
            $.ajax({
                type: 'POST',
                url: chemin ,
                data:({'id_sh':id_sh}),
                success: function(data) {    
                	    var result = jQuery.parseJSON(data);   
                	     $("#info").html(result);
                	     
                	     $("#informations").dialog('open'); 
                	       
                },
                error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
                dataType: "html"
            });
        }
        
        
        function vuesoinApp(id_sh){
        	vueSoinAppliquer(750, 640);
            var chemin = tabUrl[0]+'public/consultation/vue-soin-appliquer';
            $.ajax({
                type: 'POST',
                url: chemin ,
                data:({'id_sh':id_sh}),
                success: function(data) {    
                	    var result = jQuery.parseJSON(data);   
                	     $("#info").html(result);
                	     
                	     $("#informations").dialog('open'); 
                	       
                },
                error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
                dataType: "html"
            });
        }
        
        
        /************************************************************************************************************************/
        /************************************************************************************************************************/
        /************************************************************************************************************************/
        	function supprimersoinConfirm(id_sh,id_hosp){
            	$( "#confirmationSup" ).dialog({
            	    resizable: false,
            	    height:170,
            	    width:380,
            	    autoOpen: false,
            	    modal: true,
            	    buttons: {
            	        "Oui": function() {
            	        	
            	        	 var chemin = tabUrl[0]+'public/consultation/supprimer-soin';
            	                $.ajax({
            	                    type: 'POST',
            	                    url: chemin ,
            	                    data:({'id_sh':id_sh}),
            	                    success: function() {    
            	                    	$('#'+id_sh).fadeOut(function(){
            	                    		listeSoinsPrescrits(id_hosp);
                        	        	});
            	                    },
            	                    error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
            	                    dataType: "html"
            	                });
            	        	
            	            $( this ).dialog( "close" );             	     
            	            return false;
            	        },
            	   
           	            "Non": function() {
           	            	$( this ).dialog( "close" );             	     
           	            	return false;
           	            }
            	       
            	    }
            	   
            	});
        	}
            
            function supprimersoin(id_sh,id_hosp){
            	supprimersoinConfirm(id_sh,id_hosp);
            	$("#confirmationSup").dialog('open'); 
            }
            /************************************************************************************************************************/
            /************************************************************************************************************************/
            /************************************************************************************************************************/
            	function modifiersoinPopup(id_sh,id_hosp){
                	$( "#modification" ).dialog({
                	    resizable: false,
                	    height:430,
                	    width:930,
                	    autoOpen: false,
                	    modal: true,
                	    buttons: {
                	        "Enregistrer": function() {
                	        	
                	        	result = controleSaisiPopup(id_sh,id_hosp);
                	        	
                	        	if(result == true) {
                	        		$( this ).dialog( "close" );             	     
                    	            return false;
                	        	}
                	        	
                	        },
                	   
               	        "Annuler": function() {
               	        	/**
                    		 * On enleve l'info bulle actif au cas ou
                    		 */
                    		 var tooltips = $( "#id_soins_m, #duree_m, #date_recommandee_m, #heure_recommandee_m" ).tooltip();
                			 tooltips.tooltip( "close" );
                			 
               	            $( this ).dialog( "close" );             	     
               	            return false;
               	        }
                	       
                	    }
                	});
            	}
                
                function modifiersoin(id_sh , id_hosp){ 
                	modifiersoinPopup(id_sh,id_hosp);
                	 var chemin = tabUrl[0]+'public/consultation/modifier-soin';
 	                $.ajax({
 	                    type: 'POST',
 	                    url: chemin ,
 	                    data:({'id_sh':id_sh, 'id_hosp':id_hosp}),
 	                    success: function(data) {    
 	                    	var result = jQuery.parseJSON(data);   
 	                	    $("#info_modif").html(result);
 	                    	$("#modification").dialog('open'); 
 	                    },
 	                    error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
 	                    dataType: "html"
 	                });
                }

                /************************************************************************************************************************/
                /************************************************************************************************************************/
                /************************************************************************************************************************/
                function controleSaisiPopup(id_sh,id_hosp) {
                	
            		$("#medicament_m, #voie_administration_m, #frequence_m, #dosage_m, #date_application_m, #heure_recommandee_m, #motif_m, #note_m").css("border-color","");
                	if(!$('#medicament_m').val()){
                		$("#medicament_m").css("border-color","#FF0000");
                		$('#medicament_m').attr({'title': 'Veuillez séléctionner un médicament'});
                			var tooltips = $( "#medicament_m" ).tooltip();
                			tooltips.tooltip( "open" );
                			
                	}else if(!$('#voie_administration_m').val()){
                		$("#voie_administration_m").css("border-color","#FF0000");
                		$('#voie_administration_m').attr({'title': 'Ce champ est requis'});
                			var tooltips = $( "#voie_administration_m" ).tooltip();
                			tooltips.tooltip( "open" );
                			
                    		$("#medicament_m").css("border-color","");
                    		
                	}else if(!$('#frequence_m').val()){
                		$("#frequence_m").css("border-color","#FF0000");
                		$('#frequence_m').attr({'title': 'Ce champ est requis'});
                			var tooltips = $( "#frequence_m" ).tooltip();
                			tooltips.tooltip( "open" );
                			
                    		$("#voie_administration_m").css("border-color","");
                    		
                	}else if(!$('#dosage_m').val()){
                		$("#dosage_m").css("border-color","#FF0000");
                		$('#dosage_m').attr({'title': 'Ce champ est requis'});
                			var tooltips = $( "#dosage_m" ).tooltip();
                			tooltips.tooltip( "open" );
                			
                    		$("#frequence_m").css("border-color","");
                    		
                	}else if(!$('#date_application_m').val()){
                		$("#date_application_m").css("border-color","#FF0000");
                		$('#date_application_m').attr({'title': 'Ce champ est requis'});
                			var tooltips = $( "#date_application_m" ).tooltip();
                			tooltips.tooltip( "open" );
                			
                    		$("#dosage_m").css("border-color","");
                    		
                	}
                	else if(!$('#heure_recommandee_m').val()){
                		$("#heure_recommandee_m").css("border-color","#FF0000");
                		$('#heure_recommandee_m').attr({'title': 'Ce champ est requis'});
                			var tooltips = $( "#heure_recommandee_m" ).tooltip();
                			tooltips.tooltip( "open" );
                			
                    		$("#date_application_m").css("border-color","");
                    		
                	}
                	else if(!$('#motif_m').val()){
                		$("#motif_m").css("border-color","#FF0000");
                		$('#motif_m').attr({'title': 'Ce champ est requis'});
                			var tooltips = $( "#motif_m" ).tooltip();
                			tooltips.tooltip( "open" );
                			
                    		$("#heure_recommandee_m").css("border-color","");
                    		
                	}
                	else if(!$('#note_m').val()){
                		$("#note_m").css("border-color","#FF0000");
                		$('#note_m').attr({'title': 'Ce champ est requis'});
                			var tooltips = $( "#note_m" ).tooltip();
                			tooltips.tooltip( "open" );
                			
                    		$("#motif_m").css("border-color","");
                    		
                	}
                	else {
                		/**
        	        	 * Enregistrer les modifications
        	        	 */
                		 var medicament_m = $('#medicament_m').val();
                		 var voie_administration_m = $('#voie_administration_m').val();
                		 var frequence_m = $('#frequence_m').val();
                		 var dosage_m = $('#dosage_m').val();
                		 var date_application_m = $('#date_application_m').val();
                		 var heure_recommandee_m = null;//$('#heure_recommandee_m').val();
                		 var motif_m = $('#motif_m').val();
                		 var note_m = $('#note_m').val();
                		 var duree_m = $('#duree_m').val();

        	        	 var chemin = tabUrl[0]+'public/consultation/en-cours';
     	                 $.ajax({
     	                    type: 'POST',
     	                    url: chemin ,
     	                    data:({
     	                    	'id_sh':id_sh, 'medicament':medicament_m, 'voie_administration':voie_administration_m, 
     	                    	'frequence':frequence_m, 'dosage':dosage_m, 'date_application':date_application_m, 
     	                    	'heure_recommandee':heure_recommandee_m, 'motif':motif_m, 'note':note_m, 'duree':duree_m,
     	                    }),
     	                    success: function() {  
     	                    	listeSoinsPrescritsModifierRaf(id_hosp);
     	                    },
     	                    error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
     	                    dataType: "html",
     	                 });
     	        	
     	                /**
     	                 * *******************************
     	                 * *******************************
     	                 */
        	            return true;
                	}

               	}
                
                function listeSoinsPrescritsModifierRaf(id_hosp){
                	var chemin = tabUrl[0]+'public/consultation/liste-soins-prescrits';
                    $.ajax({
                        type: 'POST',
                        url: chemin ,
                        data:{'id_hosp': id_hosp},
                        success: function(data) {
                        	var result = jQuery.parseJSON(data);
                        	$("#liste_soins").html(result); 
                        	$("#Liste_soins_deja_prescrit").fadeOut(0).fadeIn(800);
                        },
                        error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
                        dataType: "html"
                    });
                }

     /************************************************************************************************************************/
     /************************************************************************************************************************/
     /************************************************************************************************************************/
     function liberer(id_demande_hospi) {
       	var id_cons = $("#"+id_demande_hospi).val();
       	var id_personne = $("#"+id_demande_hospi+"idPers").val();
       	var chemin = tabUrl[0]+'public/consultation/info-patient';
        $.ajax({
                type: 'POST',
                url: chemin ,
                data:{'id_personne':id_personne, 'id_cons':id_cons, 'encours':111, 'terminer':111, 'id_demande_hospi':id_demande_hospi},
                success: function(data) {
                       	         
                   	$("#titre").html("<iS style='font-size: 25px;'>&curren;</iS> LIBERATION DU PATIENT ");
                   	var result = jQuery.parseJSON(data);
                   	$("#vue_patient").html("");
                   	$("#contenu").fadeOut(function(){$("#vue_liberer_patient").html(result).fadeIn("fast"); }); 
                        	     
                },
                error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
                dataType: "html"
        });
    }
                
    /************************************************************************************************************************/
    /************************************************************************************************************************/
    /************************************************************************************************************************/
    var entre = 1;
    function affichervuedetailhospi(id_demande_hospi){
    	var id_cons = $("#"+id_demande_hospi).val();
    	var id_personne = $("#"+id_demande_hospi+"idPers").val();
    	
    	var chemin = tabUrl[0]+'public/consultation/detail-info-liberation-patient';
    	$.ajax({
    		type: 'POST',
    		url: chemin ,
    		data:{'id_personne':id_personne, 'id_cons':id_cons, 'encours':111, 'id_demande_hospi':id_demande_hospi},
    		success: function(data) {
    			$("#titre").html("<iS style='font-size: 25px;'>&curren;</iS> INFORMATIONS D&Eacute;TAILL&Eacute;ES");
    			var result = jQuery.parseJSON(data);
    			$("#contenu").fadeOut(function(){
    				$("#vue_detail_hospi_patient").html(result).fadeIn("fast"); 
    				
    			  var $nbMedPrescrit = $("#nbMedecamentPourVisualisation").val();
    			  if(entre == 1 && $nbMedPrescrit != 0){
    				  
      				  var i=0;
      				  for( i ; i < $nbMedPrescrit ; i++){
      				     $('#ajouter_medicament').trigger('click');
      				  } 
      				  entre = 0;
    			  }
    		      
    			  $('#afficherOrdonnance').click(function(){ 
   				     $('#impressionPdf').toggle(false);
   				     AffichageOrdonnancePopup();
   			         $('#PrescriptionOrdonnancePopupInterface').dialog('open'); 
   	              });
    			  
    			}); 
    		},
    		error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
    		dataType: "html"
    	});
    }
    
    
    /************************************************************************************************************************/
    /************************************************************************************************************************/
    /************************************************************************************************************************/
    /****** GESTION DES DEPLIANTS DE L'AFFICHAGE DES INFORMATIONS APRES LIBERATION DU PATIENT******/
    
    /**INFO LIBERATION DU PATIENT**/
    function depliantPlus() {
    	$('#titre_info_liberation').click(function(){
    		$("#titre_info_liberation").replaceWith(
    			"<span id='titre_info_liberation' style='margin-left:-5px; cursor:pointer;'>" +
    			"<img src='"+tabUrl[0]+"public/img/light/plus.png' /> Infos sur la lib&eacute;ration du patient"+
    		    "</span>");
    		animationPliantDepliant();
    		$('#info_liberation').animate({
    			height : 'toggle'
    		},1000);
    		return false;
    	});
    }
    
    function animationPliantDepliant() {
    	$('#titre_info_liberation').click(function(){
    		$("#titre_info_liberation").replaceWith(
    			"<span id='titre_info_liberation' style='margin-left:-5px; cursor:pointer;'>" +
    			"<img src='"+tabUrl[0]+"public/img/light/minus.png' /> Infos sur la lib&eacute;ration du patient"+
    		    "</span>");
    		depliantPlus();
    		$('#info_liberation').animate({
    			height : 'toggle'
    		},1000);
    		return false;
    	});
    }
    
    /**INFO HOSPITALISATION**/
    function depliantPlus2() {
    	$('#titre_info_hospitalisation').click(function(){
    		$("#titre_info_hospitalisation").replaceWith(
    			"<span id='titre_info_hospitalisation' style='margin-left:-5px; cursor:pointer;'>" +
    			"<img src='"+tabUrl[0]+"public/img/light/plus.png' /> Infos sur l'hospitalisation "+
    		    "</span>");
    		animationPliantDepliant2();
    		$('#info_hospitalisation').animate({
    			height : 'toggle'
    		},1000);
    		return false;
    	});
    }
    
    function animationPliantDepliant2() {
    	$('#titre_info_hospitalisation').click(function(){
    		$("#titre_info_hospitalisation").replaceWith(
    			"<span id='titre_info_hospitalisation' style='margin-left:-5px; cursor:pointer;'>" +
    			"<img src='"+tabUrl[0]+"public/img/light/minus.png' /> Infos sur l'hospitalisation"+
    		    "</span>");
    		depliantPlus2();
    		$('#info_hospitalisation').animate({
    			height : 'toggle'
    		},1000);
    		return false;
    	});
    }
    
    /**INFO LISTE**/
    function depliantPlus3() {
    	$('#titre_info_liste').click(function(){
    		$("#titre_info_liste").replaceWith(
    			"<span id='titre_info_liste' style='margin-left:-5px; cursor:pointer;'>" +
    			"<img src='"+tabUrl[0]+"public/img/light/plus.png' /> Liste des soins "+
    		    "</span>");
    		animationPliantDepliant3();
    		$('#info_liste').animate({
    			height : 'toggle'
    		},1000);
    		return false;
    	});
    }
    
    function animationPliantDepliant3() {
    	$('#titre_info_liste').click(function(){
    		$("#titre_info_liste").replaceWith(
    			"<span id='titre_info_liste' style='margin-left:-5px; cursor:pointer;'>" +
    			"<img src='"+tabUrl[0]+"public/img/light/minus.png' /> Liste des soins "+
    		    "</span>");
    		depliantPlus3();
    		$('#info_liste').animate({
    			height : 'toggle'
    		},1000);
    		return false;
    	});
    }
    
    /**INFO DEMANDE**/
    function depliantPlus4() {
    	$('#titre_info_demande').click(function(){
    		$("#titre_info_demande").replaceWith(
    			"<span id='titre_info_demande' style='margin-left:-5px; cursor:pointer;'>" +
    			"<img src='"+tabUrl[0]+"public/img/light/plus.png' /> D&eacute;tails des infos sur la demande "+
    		    "</span>");
    		animationPliantDepliant4();
    		$('#info_demande').animate({
    			height : 'toggle'
    		},1000);
    		return false;
    	});
    }
    
    function animationPliantDepliant4() {
    	$('#titre_info_demande').click(function(){
    		$("#titre_info_demande").replaceWith(
    			"<span id='titre_info_demande' style='margin-left:-5px; cursor:pointer;'>" +
    			"<img src='"+tabUrl[0]+"public/img/light/minus.png' /> D&eacute;tails des infos sur la demande "+
    		    "</span>");
    		depliantPlus4();
    		$('#info_demande').animate({
    			height : 'toggle'
    		},1000);
    		return false;
    	});
    }
    
    /** CETTE PARTIE CONCERNE LES SOINS PRESCRITS PAR LE MEDECIN LORS DE L'HOSPITALISATION DU PATIENT**/
    /** CETTE PARTIE CONCERNE LES SOINS PRESCRITS PAR LE MEDECIN LORS DE L'HOSPITALISATION DU PATIENT**/
    /** CETTE PARTIE CONCERNE LES SOINS PRESCRITS PAR LE MEDECIN LORS DE L'HOSPITALISATION DU PATIENT**/
    /** CETTE PARTIE CONCERNE LES SOINS PRESCRITS PAR LE MEDECIN LORS DE L'HOSPITALISATION DU PATIENT**/
    /**FORMULAIRE AJOUT D'UN SOIN**/ 
    function depliantPlus5() {
    	$('#titre_info_admis').click(function(){
    		$("#titre_info_admis").replaceWith(
    			"<span id='titre_info_admis' style='margin-left:-5px; cursor:pointer;'>" +
    			"<img src='"+tabUrl[0]+"public/img/light/plus.png' /> Ajout d'un soin "+
    		    "</span>");
    		animationPliantDepliant5();
    		$('#form_ajout_soins').animate({
    			height : 'toggle'
    		},1000);
    		return false;
    	});
    }
    
    function animationPliantDepliant5() {
    	$('#titre_info_admis').click(function(){
    		$("#titre_info_admis").replaceWith(
    			"<span id='titre_info_admis' style='margin-left:-5px; cursor:pointer;'>" +
    			"<img src='"+tabUrl[0]+"public/img/light/minus.png' /> Ajout d'un soin "+
    		    "</span>");
    		depliantPlus5();
    		$('#form_ajout_soins').animate({
    			height : 'toggle'
    		},1000);
    		return false;
    	});
    }
    
    /**LISTE DES SOINS **/
    function depliantPlus6() {
    	$('#titre_info_liste_soin').click(function(){
    		$("#titre_info_liste_soin").replaceWith(
    			"<span id='titre_info_liste_soin' style='margin-left:-5px; cursor:pointer;'>" +
    			"<img src='"+tabUrl[0]+"public/img/light/plus.png' /> Liste des soins "+
    		    "</span>");
    		animationPliantDepliant6();
    		$('#Liste_soins_deja_prescrit').animate({
    			height : 'toggle'
    		},1000);
    		return false;
    	});
    }
    
    function animationPliantDepliant6() {
    	$('#titre_info_liste_soin').click(function(){
    		$("#titre_info_liste_soin").replaceWith(
    			"<span id='titre_info_liste_soin' style='margin-left:-5px; cursor:pointer;'>" +
    			"<img src='"+tabUrl[0]+"public/img/light/minus.png' /> Liste des soins "+
    		    "</span>");
    		depliantPlus6();
    		$('#Liste_soins_deja_prescrit').animate({
    			height : 'toggle'
    		},1000);
    		return false;
    	});
    }
    
    function initAnimation() {
    	$('#info_liberation').toggle(false);
    	$('#info_hospitalisation').toggle(false);
    	$('#info_liste').toggle(false);
    	$('#info_demande').toggle(false);
    }
    
    
    /** CETTE PARTIE CONCERNE LA VUE DES DETAILS SUR LES INFOS DEMANDE D'HOSPITALISATION ET INFOS HOSPITALISATION **/
    /** CETTE PARTIE CONCERNE LA VUE DES DETAILS SUR LES INFOS DEMANDE D'HOSPITALISATION ET INFOS HOSPITALISATION**/
    /** CETTE PARTIE CONCERNE LA VUE DES DETAILS SUR LES INFOS DEMANDE D'HOSPITALISATION ET INFOS HOSPITALISATION**/
    /** CETTE PARTIE CONCERNE LA VUE DES DETAILS SUR LES INFOS DEMANDE D'HOSPITALISATION ET INFOS HOSPITALISATION**/
    
    /**INFO HOSPITALISATION**/
    function depliantPlus21() {
    	$('#titre_info_hospitalisation21').click(function(){
    		$("#titre_info_hospitalisation21").replaceWith(
    			"<span id='titre_info_hospitalisation21' style='margin-left:-5px; cursor:pointer;'>" +
    			"<img src='"+tabUrl[0]+"public/img/light/plus.png' /> Infos sur l'hospitalisation "+
    		    "</span>");
    		animationPliantDepliant21();
    		$('#info_hospitalisation21').animate({
    			height : 'toggle'
    		},1000);
    		return false;
    	});
    }
    
    function animationPliantDepliant21() {
    	$('#titre_info_hospitalisation21').click(function(){
    		$("#titre_info_hospitalisation21").replaceWith(
    			"<span id='titre_info_hospitalisation21' style='margin-left:-5px; cursor:pointer;'>" +
    			"<img src='"+tabUrl[0]+"public/img/light/minus.png' /> Infos sur l'hospitalisation"+
    		    "</span>");
    		depliantPlus21();
    		$('#info_hospitalisation21').animate({
    			height : 'toggle'
    		},1000);
    		return false;
    	});
    }
    
    /**INFO DEMANDE**/
    function depliantPlus41() {
    	$('#titre_info_demande41').click(function(){
    		$("#titre_info_demande41").replaceWith(
    			"<span id='titre_info_demande41' style='margin-left:-5px; cursor:pointer;'>" +
    			"<img src='"+tabUrl[0]+"public/img/light/plus.png' /> D&eacute;tails des infos sur la demande "+
    		    "</span>");
    		animationPliantDepliant41();
    		$('#info_demande41').animate({
    			height : 'toggle'
    		},1000);
    		return false;
    	});
    }
    
    function animationPliantDepliant41() {
    	$('#titre_info_demande41').click(function(){
    		$("#titre_info_demande41").replaceWith(
    			"<span id='titre_info_demande41' style='margin-left:-5px; cursor:pointer;'>" +
    			"<img src='"+tabUrl[0]+"public/img/light/minus.png' /> D&eacute;tails des infos sur la demande "+
    		    "</span>");
    		depliantPlus41();
    		$('#info_demande41').animate({
    			height : 'toggle'
    		},1000);
    		return false;
    	});
    }
    
    function initAnimationVue() {
    	$('#info_hospitalisation21').toggle(false);
    	$('#info_demande41').toggle(false);
    }
       // ******************* Tranfert ******************************** 
	   // ******************* Tranfert ******************************** 
	    $(function(){
	    	var motif_transfert = $("#motif_transfert");
	    	var hopital_accueil = $("#hopital_accueil");
	    	var service_accueil = $("#service_accueil");

	    	$( "#bouton_transfert_valider" ).toggle(true);
	    	$( "#bouton_transfert_modifier" ).toggle(false);

	    	$( "#bouton_transfert_valider" ).click(function(){
	    		if(service_accueil.val() != 0){
	    			motif_transfert.attr( 'disabled', true ).css({'background':'#f8f8f8'});     
		    		hopital_accueil.attr( 'disabled', true ).css({'background':'#f8f8f8'});     
		    		service_accueil.attr( 'disabled', true ).css({'background':'#f8f8f8'});   
		    		$("#bouton_transfert_modifier").toggle(true);  
		    		$("#bouton_transfert_valider").toggle(false); 
		    		$("#service_accueil").css({'border-color': ''});
	    		} else {
	    			//alert('pas de service accueil');
	    			$("#service_accueil").css({'border-color': 'red'});
	    		}
	    		
	    		return false; 
	    	});
	    	
	    	$( "#bouton_transfert_modifier" ).click(function(){
	    		motif_transfert.attr( 'disabled', false ).css({'background':'#fff'});
	    		hopital_accueil.attr( 'disabled', false ).css({'background':'#fff'});
	    		service_accueil.attr( 'disabled', false ).css({'background':'#fff'});
	    		$("#bouton_transfert_modifier").toggle(false);  
	    		$("#bouton_transfert_valider").toggle(true);  
	    	 	return  false;
	    	});
	    });
	    
    /**** RECUPEARTION DE LA LISTE DES SERVIVES ****/
	  var base_url = window.location.toString();
	  var tabUrl = base_url.split("public");
		
    var theHREF = tabUrl[0]+"public/consultation/services";
	  function getservices(cle)
	  {
	      $.ajax({
	          type: 'POST',
	          url: theHREF,
	          data: 'id='+cle,
	          success: function(data) {
	              var result = jQuery.parseJSON(data); 
	              var optionVide = "<option value=0> </option>";
	              result = optionVide + result;
	              $("#service_accueil").html(result);
	          },
	       error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
           dataType: "html"
	      });

	      return false;
	  }
	  
	  
	  function ListeSoinsVisualisationHospi(){  
		  $idHospVH = $('#idHospVH').val(); 
		  $.ajax({
			  type: 'POST',
			  url: tabUrl[0]+"public/consultation/liste-soins-visualisation-hosp",
			  data: {'id_hosp' : $idHospVH },
			  success: function(data) {
				  var result = jQuery.parseJSON(data);
				  $("#info_liste").html(result); 
			  },
			  error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
			  dataType: "html"
		  });
		  return false;
	  }
	  
	  
	  function listeDesSoinsVisualisation() {
	    	var  oTable = $('#listeSoinVisualisation').dataTable
	     	( {
	     					"sPaginationType": "full_numbers",
	     					"aLengthMenu": [3,5,7],
	     					"iDisplayLength": 3,
	     					"aaSorting": [], //On ne trie pas la liste automatiquement
	     					"oLanguage": {
	     						//"sInfo": "_START_ &agrave; _END_ sur _TOTAL_ &eacute;l&eacute;ments",
	     						//"sInfoEmpty": "0 &eacute;l&eacute;ment &agrave; afficher",
	     						"sInfoFiltered": "",
	     						"sUrl": "",
	     						"oPaginate": {
	     							"sFirst":    "|<",
	     							"sPrevious": "<",
	     							"sNext":     ">",
	     							"sLast":     ">|"
	     							}
	     					   },
	     	});

	    }
	  
	  
	  
	  
	  
	  
	  
	  
	  
	  
	  
	  
	  
	  
	  
	  
	  
	  
	  
	  
	  
	  
	  
	  
	  
	  
	  
	  
	  
	  
	  

	  //LIBERER LORS D4UN TRANSFERT DU PATIENT HOSPITALISER
	  function LibererPourTransfererPatient(id_demande_hospi){
		  
	    var tooltips = $("#medicament, #voie_administration, #frequence, #dosage, #date_application, #heure_recommandee_, #duree").tooltip();
	    tooltips.tooltip( "close" );
	    $("#medicament, #voie_administration, #frequence, #dosage, #date_application, #heure_recommandee_, #duree").attr({'title':''});
	    	
	 	$("#medicament, #voie_administration, #frequence, #dosage, #date_application, #heure_recommandee_, #duree").css("border-color","");
	 	$("#medicament, #voie_administration, #frequence, #dosage, #date_application, #heure_recommandee_, #duree").val('');
	 		
	 	LaDuree = 0;
	    //POUR LA SUPPRESSION DES ELEMENTS SELECTIONNES SUR LA LISTE
	    for(var j = 0; j < 24; j++){
	    	$('.SlectBox')[0].sumo.unSelectItem(j);
	    }
	    //POUR LA SUPPRESSION DES ICONES COCHES SUR LA LISTE
	    $(function(){
	        $('select.SlectBox')[0].sumo.unload();
	        $('.SlectBox').SumoSelect({ csvDispCount: 6 });
	    });
		  
		  
		var id_cons = $("#"+id_demande_hospi).val();
      	var id_personne = $("#"+id_demande_hospi+"idPers").val();
      	var chemin = tabUrl[0]+'public/consultation/info-patient';
          $.ajax({
              type: 'POST',
              url: chemin ,
              data:{'id_personne':id_personne, 'id_cons':id_cons, 'encours':111, 'terminer':111, 'id_demande_hospi':id_demande_hospi},
              success: function(data) {
              	var result = jQuery.parseJSON(data);
              	$("#hospitaliser").fadeOut(function(){
            		$("#titre").html("<iS style='font-size: 25px;'>&curren;</iS> LIBERATION DU PATIENT <i>(Transfert)</i>");
            		$("#vue_liberer_patient").html(result).fadeIn(0); 
                  	$("#terminerLiberer").replaceWith("<button id='terminerLiberer2'>Annuler</button>");
                  	$("#liberer").replaceWith("<button type='submit' id='liberer' style='' >Transf&eacute;rer</button>");
                  	$("#motif_sorti").val("Transfert:  "+$("#motif_transfert").val());
                  	$("#id_cons").val(id_cons);
                  	$("#temoin_transfert").val($("#service_accueil").val()); //C'est la valeur du service car le patient est transferer dans un service
                  	
                  	
                  	$("#terminerLiberer2").click(function(){
            	    	$("#titre").html("<iS style='font-size: 25px;'>&curren;</iS> ADMINISTRER DES SOINS");
                  		
                  		$("#vue_liberer_patient").fadeOut(function(){
                  			$("#hospitaliser").toggle(true);
                  		}); 
              	    	return false;
              	    });
                  	
              	});
              	
              },
              error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
              dataType: "html"
          });
          
	  }
	  
	  
	  
	  
	  
	  var temoinTaille;
	  var temoinPoids;
	  var temoinTemperature;
	  var temoinPouls;
	  var valid = true;
	  
	  function envoiDesDonnees(){

 	      //******************* VALIDER LES DONNEES DU TABLEAU DES MOTIFS ******************************** 
		  //******************* VALIDER LES DONNEES DU TABLEAU DES MOTIFS ******************************** 
			
	 	    $("#taille").blur(function(){
		    	var valeur = $('#taille').val();
		    	if(isNaN(valeur/1) || valeur > 250 || valeur == ""){
					valeur = null;
					$("#taille").css("border-color","#FF0000");
		            $("#erreur_taille").fadeIn().text("Max: 250cm").css({"color":"#ff5b5b","padding":" 0 10px 0 10px","margin-top":"-18px","font-size":"13px","font-style":"italic"});
		            temoinTaille = 1;
		    	} 
		    	else{
		    		$("#taille").css("border-color","");
					$("#erreur_taille").fadeOut();
					temoinTaille = 0;
		    	}
		    	return false;
		    });
		    
		    $("#poids").blur(function(){
		    	var valeur = $('#poids').val();
		    	if(isNaN(valeur/1) || valeur > 300 || valeur == ""){
					valeur = null;
					$("#poids").css("border-color","#FF0000");
					$("#erreur_poids").fadeIn().text("Max: 300kg").css({"color":"#ff5b5b","padding":" 0 10px 0 10px","margin-top":"-18px","font-size":"13px","font-style":"italic"});
					temoinPoids = 2;
		    	} 
		    	else{
		    		$("#poids").css("border-color","");
					$("#erreur_poids").fadeOut();
					temoinPoids = 0;
		    	}
		    	return false;
		    });
		    
		    $("#temperature").blur(function(){
		    	var valeur = $('#temperature').val();
		    	if(isNaN(valeur/1) || valeur > 45 || valeur < 34  || valeur == ""){
					$("#temperature").css("border-color","#FF0000");
		    		$("#erreur_temperature").fadeIn().text("Min: 34°C, Max: 45°C").css({"color":"#ff5b5b","padding":" 0 10px 0 10px","margin-top":"-18px","font-size":"13px","font-style":"italic"});
					temoinTemperature = 3;
		    	} 
		    	else{
		    		$("#temperature").css("border-color","");
					$("#erreur_temperature").fadeOut();
					temoinTemperature = 0;
		    	}
		    	return false;
		    });
		    
		    
		    $("#pressionarterielle").blur(function(){
		    	if( $("#pressionarterielle").val() == "___/___" || $("#pressionarterielle").val() == ""){
		    		$("#pressionarterielle").val('');
		    		$("#pressionarterielle").mask("299/299");
		    		$("#pressionarterielle").css("border-color","#FF0000");
		    		$("#erreur_pressionarterielle").fadeIn().text("300mmHg / 300mmHg").css({"color":"#ff5b5b","padding":" 0 10px 0 10px","margin-top":"-18px","font-size":"13px","font-style":"italic"});
		    	} else{
		    		$("#pressionarterielle").css("border-color","");
		    		$("#erreur_pressionarterielle").fadeOut();
		    	}
		    	return false;
		    });
		    
		    $("#pouls").blur(function(){
		    	var valeur = $('#pouls').val();
				if(isNaN(valeur/1) || valeur > 150 || valeur == ""){
					$("#pouls").css("border-color","#FF0000");
					$("#erreur_pouls").fadeIn().text("Max: 150 battements").css({"color":"#ff5b5b","padding":" 0 10px 0 10px","margin-top":"-18px","font-size":"13px","font-style":"italic"});
					temoinPouls = 4;
				}else{
					$("#pouls").css("border-color","");
					$("#erreur_pouls").fadeOut();
					temoinPouls = 0;
				}
		    });
		    
		  /****** CONTROLE APRES VALIDATION ********/ 
		  /****** CONTROLE APRES VALIDATION ********/
		  $("#bouton_constantes_valider, #terminerExamenDuJour").click(function(){

			     valid = true;
		         if( $("#taille").val() == "" || temoinTaille == 1){
		             $("#taille").css("border-color","#FF0000");
		             $("#erreur_taille").fadeIn().text("Max: 250cm").css({"color":"#ff5b5b","padding":" 0 10px 0 10px","margin-top":"-18px","font-size":"13px","font-style":"italic"});
		             valid = false;
		         }
		         else{
		         	$("#taille").css("border-color","");
		         	$("#erreur_taille").fadeOut();
		         }
		         
		         if( $("#poids").val() == "" || temoinPoids == 2){
		         	 $("#poids").css("border-color","#FF0000");
		             $("#erreur_poids").fadeIn().text("Max: 300kg").css({"color":"#ff5b5b","padding":" 0 10px 0 10px","margin-top":"-18px","font-size":"13px","font-style":"italic"});
		             valid = false;
		         }
		         else{
		         	$("#poids").css("border-color", "");
		         	$("#erreur_poids").fadeOut();
		         }
		         
		         if( $('#temperature').val() == "" || temoinTemperature == 3){
		         	$("#temperature").css("border-color","#FF0000");
		         	$("#erreur_temperature").fadeIn().text("Min: 34°C, Max: 45°C").css({"color":"#ff5b5b","padding":" 0 10px 0 10px","margin-top":"-18px","font-size":"13px","font-style":"italic"});
		             valid = false;
		         }
		         else{
		         	$("#temperature").css("border-color", "");
		         	$("#erreur_temperature").fadeOut();
		         }
		         
		         if( $("#pouls").val() == "" || temoinPouls == 4){
		         	 $("#pouls").css("border-color","#FF0000");
		             $("#erreur_pouls").fadeIn().text("Max: 150 battements").css({"color":"#ff5b5b","padding":" 0 10px 0 10px","margin-top":"-18px","font-size":"13px","font-style":"italic"});
		             valid = false;
		         }
		         else{
		         	 $("#pouls").css("border-color", "");
		             $("#erreur_pouls").fadeOut();
		         }
		         
		         if( $("#pressionarterielle").val() == ""){
		        	 $("#pressionarterielle").css("border-color","#FF0000");
		        	 $("#erreur_pressionarterielle").fadeIn().text("300mmHg / 300mmHg").css({"color":"#ff5b5b","padding":" 0 10px 0 10px","margin-top":"-18px","font-size":"13px","font-style":"italic"});
		        	 valid = false;
		         }
		         else{
		        	 $("#pressionarterielle").css("border-color", "");
		        	 $("#erreur_pressionarterielle").fadeOut();
		         }
		         
		         return false;
		 	});
		  
		  //******************* VALIDER LES DONNEES DU TABLEAU DES CONSTANTES ******************************** 
		  //******************* VALIDER LES DONNEES DU TABLEAU DES CONSTANTES ******************************** 
		    $(function(){
		    	var poids = $('#poids');
		    	var taille = $('#taille');
		    	var tension = $('#tension');
		    	var bu = $('#bu');
		    	var temperature = $('#temperature');
		    	var glycemie_capillaire = $('#glycemie_capillaire');
		    	var pouls = $('#pouls');
		    	var frequence_respiratoire = $('#frequence_respiratoire');
		    	var pressionarterielle = $("#pressionarterielle");
		    	
		  	    //Au debut on cache le bouton modifier et on affiche le bouton valider
		    	$( "#bouton_constantes_valider" ).toggle(true);
		    	$( "#bouton_constantes_modifier" ).toggle(false);

		    	//Au debut on active tous les champs
		    	poids.attr( 'readonly', false ).css({'background':'#fff'});
		    	taille.attr( 'readonly', false ).css({'background':'#fff'});
		    	tension.attr( 'readonly', false).css({'background':'#fff'}); 
		    	bu.attr( 'readonly', false).css({'background':'#fff'});  
		    	temperature.attr( 'readonly', false).css({'background':'#fff'}); 
		    	glycemie_capillaire.attr( 'readonly', false).css({'background':'#fff'});
		    	pouls.attr( 'readonly', false).css({'background':'#fff'});
		    	frequence_respiratoire.attr( 'readonly', false).css({'background':'#fff'});
		    	pressionarterielle.attr( 'readonly', false ).css({'background':'#fff'});

		    	$( "#bouton_constantes_valider" ).click(function(){
		    		if(valid == true){
		  	   		poids.attr( 'readonly', true ).css({'background':'#f8f8f8'});    
		  	   		taille.attr( 'readonly', true ).css({'background':'#f8f8f8'});
		  	   		tension.attr( 'readonly', true).css({'background':'#f8f8f8'});
		  	   		bu.attr( 'readonly', true).css({'background':'#f8f8f8'});
		  	   		temperature.attr( 'readonly', true).css({'background':'#f8f8f8'});
		  	   		glycemie_capillaire.attr( 'readonly', true).css({'background':'#f8f8f8'});
		  	   		pouls.attr( 'readonly', true).css({'background':'#f8f8f8'});
		  	   		frequence_respiratoire.attr( 'readonly', true).css({'background':'#f8f8f8'});
		  	   		pressionarterielle.attr( 'readonly', true ).css({'background':'#f8f8f8'});
		  	   		
		    		    $("#bouton_constantes_modifier").toggle(true);  //on affiche le bouton permettant de modifier les champs
		    		    $("#bouton_constantes_valider").toggle(false); //on cache le bouton permettant de valider les champs
		    		}
		    		return false; 
		    	});
		    	
		    	$( "#bouton_constantes_modifier" ).click(function(){
		    		poids.attr( 'readonly', false ).css({'background':'#fff'});
		    		taille.attr( 'readonly', false ).css({'background':'#fff'}); 
		    		tension.attr( 'readonly', false).css({'background':'#fff'}); 
		    		bu.attr( 'readonly', false).css({'background':'#fff'});
		    		temperature.attr( 'readonly', false).css({'background':'#fff'});
		    		glycemie_capillaire.attr( 'readonly', false).css({'background':'#fff'});
		    		pouls.attr( 'readonly', false).css({'background':'#fff'});
		    		frequence_respiratoire.attr( 'readonly', false).css({'background':'#fff'});
		    		pressionarterielle.attr( 'readonly', false ).css({'background':'#fff'});
		    		
		    	 	$("#bouton_constantes_modifier").toggle(false);   //on cache le bouton permettant de modifier les champs
		    	 	$("#bouton_constantes_valider").toggle(true);    //on affiche le bouton permettant de valider les champs
		    	 	return  false;
		    	});
		    });
		  
		  $("#terminerExamenDuJour").click(function(){ 
			  
			  if($("#service_accueil").val() != 0) {
				  $("#service_accueil").css({'border-color': ''});
				  
				  id_demande_hospi = $('#id_demande_hospi').val();
				  $('#lebererPatientHospi'+id_demande_hospi).trigger('click');
				  
				  return false;
			  } else
			  
			  if(valid == true ) { 
				  var id_hosp = $('#id_hosp').val();
				  var id_personne = $('#id_personne').val();
				  
				  //Les motifs d'admission
				  //Les motifs d'admission
				  //Les motifs d'admission
				  var motif_admission1 = $("#motif_admission1").val();
				  var motif_admission2 = $("#motif_admission2").val();
				  var motif_admission3 = $("#motif_admission3").val();
				  var motif_admission4 = $("#motif_admission4").val();
				  var motif_admission5 = $("#motif_admission5").val();
				  
				  //Les constantes
				  //Les constantes
				  //Les constantes
				  var poids = $("#poids").val();
				  var taille = $("#taille").val();
				  var temperature = $("#temperature").val();
				  var pressionarterielle = $("#pressionarterielle").val();
				  var pouls = $("#pouls").val();
				  var frequence_respiratoire = $("#frequence_respiratoire").val();
				  var glycemie_capillaire = $("#glycemie_capillaire").val();
				  
				  //Les diagnostics
				  //Les diagnostics
				  //Les diagnostics
				  var diagnostic1 = $("#diagnostic1").val();
				  var diagnostic2 = $("#diagnostic2").val();
				  var diagnostic3 = $("#diagnostic3").val();
				  var diagnostic4 = $("#diagnostic4").val();
				  
				  //Les donnees de l'examen physique
			      //Les donnees de l'examen physique
				  var examen_donnee1 = $("#examen_donnee1").val();
				  var examen_donnee2 = $("#examen_donnee2").val();
				  var examen_donnee3 = $("#examen_donnee3").val();
				  var examen_donnee4 = $("#examen_donnee4").val();
				  var examen_donnee5 = $("#examen_donnee5").val();
				  
				  //Recuperer les donnees sur les bandelettes urinaires
				  //Recuperer les donnees sur les bandelettes urinaires
				  var albumine = $('#BUcheckbox input[name=albumine]:checked').val();
				  if(!albumine){ albumine = 0;}
				  var croixalbumine = $('#BUcheckbox input[name=croixalbumine]:checked').val();
				  if(!croixalbumine){ croixalbumine = 0;}
					
				  var sucre = $('#BUcheckbox input[name=sucre]:checked').val();
				  if(!sucre){ sucre = 0;}
				  var croixsucre = $('#BUcheckbox input[name=croixsucre]:checked').val();
				  if(!croixsucre){ croixsucre = 0;}
					
				  var corpscetonique = $('#BUcheckbox input[name=corpscetonique]:checked').val();
				  if(!corpscetonique){ corpscetonique = 0;}
				  var croixcorpscetonique = $('#BUcheckbox input[name=croixcorpscetonique]:checked').val();
				  if(!croixcorpscetonique){ croixcorpscetonique = 0;}
					
				    
				  $.ajax({
					  type: 'POST',
					  url: tabUrl[0]+"public/consultation/enregistrer-examen-du-jour",
					  data: {'id_hosp' : id_hosp, 'id_personne':id_personne,
						     'motif_admission1' : motif_admission1, 'motif_admission2' : motif_admission2, 
						     'motif_admission3' : motif_admission3, 'motif_admission4' : motif_admission4,
						     'motif_admission5' : motif_admission5,
						     
						     'poids' : poids, 'taille' : taille, 'temperature' : temperature, 'pressionarterielle' : pressionarterielle,
						     'pouls' : pouls, 'frequence_respiratoire' : frequence_respiratoire, 'glycemie_capillaire' : glycemie_capillaire,
						     
						     'diagnostic1' : diagnostic1, 'diagnostic2' : diagnostic2, 
						     'diagnostic3' : diagnostic3, 'diagnostic4' : diagnostic4,
						     
						     'examen_donnee1' : examen_donnee1, 'examen_donnee2' : examen_donnee2, 
						     'examen_donnee3' : examen_donnee3, 'examen_donnee4' : examen_donnee4, 
						     'examen_donnee5' : examen_donnee5,
						     
						     'albumine' : albumine, 'croixalbumine' : croixalbumine,
						     'sucre' : sucre, 'croixsucre' : croixsucre,
						     'corpscetonique' : corpscetonique, 'croixcorpscetonique' : croixcorpscetonique,
					  },
					  success: function(data) {
						  var result = jQuery.parseJSON(data); //alert(result); exit();
						  if(result){
							  AppelListeExamensDuJoursHospi(id_hosp);
							  viderTousLesChamps();
							  $('#listeDeTousLesExamens').trigger('click');
							  
						  }
					  },
					  error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
					  dataType: "html"
				  });
				  
			  }else{
				  $("#service_accueil").css({'border-color': ''});
				  $('#listeDeToutesLesConstantes').trigger('click');
			  }

			  return false;
		  });
		  //confirmationAnnulationExamenJour
		  $("#annulerExamenDuJour").click(function(){
			  annulerExamenDuJourConfirm();
		      $("#confirmationAnnulationExamenJour").dialog('open');
		  });
	  }
	  
	  function annulerExamenDuJourConfirm(){
		  $( "#confirmationAnnulationExamenJour" ).dialog({
	      	    resizable: false,
	      	    height:170,
	      	    width:420,
	      	    autoOpen: false,
	      	    modal: true,
	      	    buttons: {
	      	        "Oui": function() {
	      	        	viderTousLesChamps();
	      			    $('#listeDeTousLesExamens').trigger('click');
	      			  
	      	            $( this ).dialog( "close" );             	     
	      	            return false;
	      	        },
	      	   
	     	            "Non": function() {
	     	            	$( this ).dialog( "close" );             	     
	     	            	return false;
	     	            }
	      	       
	      	    }
		  });
	  }
	  
	  function viderTousLesChamps(){
		  //Les Motifs d'admission
	      //Les Motifs d'admission
		  //Les Motifs d'admission
		  $("#motif_admission1").val("");
		  $("#motif_admission2").val("");
		  $("#motif_admission3").val("");
		  $("#motif_admission4").val("");
		  $("#motif_admission5").val("");
		  
		  //Les constantes
		  //Les constantes
		  //Les constantes
		  $("#poids").val("");
		  $("#poids").css("border-color", "");
       	  $("#erreur_poids").fadeOut();
       	
		  $("#taille").val("");
		  $("#taille").css("border-color","");
       	  $("#erreur_taille").fadeOut();
       	
		  $("#temperature").val("");
		  $("#temperature").css("border-color", "");
       	  $("#erreur_temperature").fadeOut();
       	
		  $("#pressionarterielle").val("");
		  $("#pressionarterielle").css("border-color", "");
     	  $("#erreur_pressionarterielle").fadeOut();
		  
		  $("#pouls").val("");
		  $("#pouls").css("border-color", "");
          $("#erreur_pouls").fadeOut();
          
		  $("#frequence_respiratoire").val("");
		  $("#glycemie_capillaire").val("");
		  
		  //Les bandelettes
		  //Les bandelettes
		  //Les bandelettes
		  $("#BUcheckbox input[name=albumine]").attr('checked', false);
		  $("#BUcheckbox input[name=croixalbumine]").attr('checked', false);
		  $("#BUcheckbox input[name=sucre]").attr('checked', false);
		  $("#BUcheckbox input[name=croixsucre]").attr('checked', false);
		  $("#BUcheckbox input[name=corpscetonique]").attr('checked', false);
		  $("#BUcheckbox input[name=croixcorpscetonique]").attr('checked', false);
		  $("#labelAlbumine").toggle(false);
		  $("#labelSucre").toggle(false);
		  $("#labelCorpscetonique").toggle(false);
		  
		  //Donnees de l'examen physique
	      //Donnees de l'examen physique
		  $("#examen_donnee1").val("");
		  $("#examen_donnee2").val("");
		  $("#examen_donnee3").val("");
		  $("#examen_donnee4").val("");
		  $("#examen_donnee5").val("");
		  
		  //Transfert
		  //Transfert
		  $("#service_accueil").val(0);
		  $("#service_accueil").css({'border-color': ''});
	  }
	  
	  
	  function initialisationScriptDataTable(){
		var  oTable = $('#ListingExamens').dataTable
	    ( {
	    					"sPaginationType": "full_numbers",
	    					"aLengthMenu": [3,5],
	    					"iDisplayLength": 3,
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
	    }); 
	        
	   	//le filtre du select
	   	$('#filter_statut').change(function() 
	   	{					
	   		oTable.fnFilter( this.value );
	   	});
	  }
	  
	  function AppelListeExamensDuJoursHospi(id_hosp){
		  $.ajax({
			  type: 'POST',
			  url: tabUrl[0]+"public/consultation/liste-examen-du-jour",
			  data: {'id_hosp':id_hosp },
			  success: function(data) {
				  var result = jQuery.parseJSON(data);
				  $("#listeDesExamens").html(result);
			  },
			  error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
			  dataType: "html"
		  });
	  }
	  
	  function visualiserExamenDuJour(id_examen_jour){
		  //alert(id_examen_jour);
		  var formulaire = document.createElement("form");
		  formulaire.setAttribute("action","/simens/public/consultation/vue-examen-jour"); 
		  formulaire.setAttribute("method","POST"); 
		  formulaire.setAttribute("target","_blank"); 
		  
		  //Ajout dynamique de champs dans le formulaire
		  var champ = document.createElement("input");
		  champ.setAttribute("type", "hidden");
		  champ.setAttribute("name", "id_examen_jour");
		  champ.setAttribute("value", id_examen_jour);
		  formulaire.appendChild(champ);
	        
		  formulaire.submit();
	  }
	  
	  function supprimerExamenDuJourConfirm(id_examen_jour){
      	$( "#confirmationSuppExamenJour" ).dialog({
      	    resizable: false,
      	    height:170,
      	    width:400,
      	    autoOpen: false,
      	    modal: true,
      	    buttons: {
      	        "Oui": function() {
      	        	
      	        	 var chemin = tabUrl[0]+'public/consultation/supprimer-examen-jour';
      	                $.ajax({
      	                    type: 'POST',
      	                    url: chemin ,
      	                    data:({'id_examen_jour':id_examen_jour}),
      	                    success: function(data) {
      	                    	var result = jQuery.parseJSON(data);
      	                    	$('#ExamenDuJourLigne'+id_examen_jour).fadeOut(function(){
      	                    		AppelListeExamensDuJoursHospi(result);
                  	        	});
      	                    },
      	                    error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
      	                    dataType: "html"
      	                });
      	        	
      	            $( this ).dialog( "close" );             	     
      	            return false;
      	        },
      	   
     	            "Non": function() {
     	            	$( this ).dialog( "close" );             	     
     	            	return false;
     	            }
      	       
      	    }
      	   
      	});
  	}
	  
	function supprimerExamenDuJour(id_examen_jour){ 
	  supprimerExamenDuJourConfirm(id_examen_jour);
      $("#confirmationSuppExamenJour").dialog('open');
	}
	  
	  
	
