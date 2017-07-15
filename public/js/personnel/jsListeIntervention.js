    var base_url = window.location.toString();
    var tabUrl = base_url.split("public");

	//BOITE DE DIALOG POUR LA CONFIRMATION DE SUPPRESSION
    function confirmation(id){
	  $( "#confirmation" ).dialog({
	    resizable: false,
	    height:170,
	    width:405,
	    autoOpen: false,
	    modal: true,
	    buttons: {
	        "Oui": function() {
	            $( this ).dialog( "close" );
	            
	            var cle = id;
	            var chemin = tabUrl[0]+'public/personnel/supprimer-transfert';
	            $.ajax({
	                type: 'POST',
	                url: chemin ,
	                data: $(this).serialize(),  
	                data:'id='+cle,
	                success: function(data) { 
	                	$("#"+cle).parent().parent().fadeOut(function(){
	                	 	 $("#"+cle).empty();
	                	 });
	                },
	                error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
	                dataType: "html"
	            });
	    	     return false;
	    	     
	        },
	        "Annuler": function() {
                $( this ).dialog( "close" );
            }
	   }
	  });
    }
    
    function supprimer(id){
   	   confirmation(id);
       $("#confirmation").dialog('open');
   	}
    
    /************************************************************************************************************************/
    /************************************************************************************************************************/
    /************************************************************************************************************************/
    function initialisation(){
        var  oTable = $('#personnel').dataTable
    	( {
    					"sPaginationType": "full_numbers",
    					"aLengthMenu": [5,7,10,15],
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

    					"sAjaxSource": ""+tabUrl[0]+"public/personnel/liste-intervention-ajax", 
    					
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

    }
    
    /************************************************************************************************************************/
    /************************************************************************************************************************/
    /************************************************************************************************************************/
    function affichervue(id){
    	var cle = id;
        var chemin = tabUrl[0]+'public/personnel/info-personnel-intervention';
        $.ajax({
            type: 'POST',
            url: chemin ,
            data: $(this).serialize(),  
            data: ({'id':cle, 'identif': 2}),
            success: function(data) {
           	         
            	$("#titre").replaceWith("<div id='titre2' style='font-family: police2; color: green; font-size: 20px; font-weight: bold; padding-left:20px;'><iS style='font-size: 25px;'>&curren;</iS> INFORMATIONS </div>");
            	var result = jQuery.parseJSON(data);  
            	$("#contenu").fadeOut(function(){$("#vue_patient").html(result).fadeIn("fast"); }); 
            	     
            },
            error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
            dataType: "html"
        });
     }
    
    function listepatient(){
	    $("#terminer").click(function(){
	    	$("#titre2").replaceWith("<div id='titre' style='font-family: police2; color: green; font-size: 20px; font-weight: bold; padding-left:20px;'><iS style='font-size: 25px;'>&curren;</iS> LE PERSONNEL </div>");
  	    	$("#vue_patient").fadeOut(function(){$("#contenu").fadeIn("fast"); 
  	    	$("div .dataTables_paginate").css(
  	              {
  	  			    'margin-right' : '0'
  	              });
  	    	
  	    	});
  	    	
  	    	//CSS
  	    	$("#listeDataTable").css({'margin-left' : '-10'});
  	    	
  	    	return false;
  	    });
	    
	        $('#listeintervention').dataTable
	    	( {
	    					"sPaginationType": "full_numbers",
	    					"aLengthMenu": [3,5,7],
	    					"iDisplayLength": 3,
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
    /************************************************************************************************************************/
    /************************************************************************************************************************/
    /************************************************************************************************************************/
    function modifiertransfert(id){
    	var cle = id;
        var chemin = tabUrl[0]+'public/personnel/vue-agent-personnel';
        $.ajax({
            type: 'POST',
            url: chemin ,
            data: $(this).serialize(),  
            data: ({'id':cle, 'identif': 1}),
            success: function(data) {
           	         
            	$("#titre").replaceWith("<div id='titre2' style='font-family: police2; color: green; font-size: 20px; font-weight: bold; padding-left:20px;'><iS style='font-size: 25px;'>&curren;</iS> MODIFIER LES DONNEES </div>");
            	var result = jQuery.parseJSON(data);  
            	$("#vue_agent_personnel_et_formulaire_modification").html(result);
            	$("#contenu").fadeOut(function(){ $("#modification_transfert").fadeIn("fast"); }); 
            	     
            },
            error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
            dataType: "html"
        });
        
    }
    
    function scriptModification(){
    	$("#annuler_modif").click(function(){ 
    		$("#modification_transfert").fadeOut(function(){ $("#contenu").fadeIn("fast"); });
    		return false;
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
    			
    			$(".transfert_externe").toggle(false);
    			$(".transfert_interne").toggle(true);
    			
    			$("#service_accueil").attr({'required': true});
    			$("#motif_transfert").attr({'required': true});
    			
    			$("#service_accueil_externe").attr({'required': false});
    			$("#motif_transfert_externe").attr({'required': false});
    			
    			return false;
    		}
    		else
    			if(id=="Externe"){
    				$("#vider_champ_interne").toggle(false);
    				$("#vider_champ_externe").toggle(true);
    				
    				$(".transfert_interne").toggle(false);
    				$(".transfert_externe").toggle(true);
    				
    				$("#service_accueil_externe").attr({'required': true});
    				$("#motif_transfert_externe").attr({'required': true});
    				
    				$("#service_accueil").attr({'required': false});
    				$("#motif_transfert").attr({'required': false});
    				
    				return false;
    			}
    	}

    /************************************************************************************************************************/
    /************************************************************************************************************************/
    /************************************************************************************************************************/
    	
    	function vueinterventionAgent(){
        	$( "#informations" ).dialog({
        	    resizable: false,
        	    height:325,
        	    width:600,
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
        
        function vueintervention(idIntervention){
        	vueinterventionAgent();
            var chemin = tabUrl[0]+'public/personnel/vue-intervention-agent';
            $.ajax({
                type: 'POST',
                url: chemin ,
                data:({'idIntervention':idIntervention}),
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
        
        function confirmationSuppressionDef(id){
      	  $( "#confirmationSuppressionInterventionDef" ).dialog({
      	    resizable: false,
      	    height:190,
      	    width:405,
      	    autoOpen: false,
      	    modal: true,
      	    buttons: {
      	        "Oui": function() {
      	            $( this ).dialog( "close" );
      	            var cle = id;
      	            var chemin = tabUrl[0]+'public/personnel/supprimer-intervention';
      	            $.ajax({
      	                type: 'POST',
      	                url: chemin ,
      	                data:'id_personne='+cle,
      	                success: function() { 
      	                	$("#"+cle).parent().parent().fadeOut(function(){
      	                	 	 $("#"+cle).empty();
      	                	});
      	                },
      	                error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
      	                dataType: "html"
      	            });
      	    	     return false;
      	    	     
      	        },
      	        "Annuler": function() {
                      $( this ).dialog( "close" );
                  }
      	   }
      	  });
          }
        
      //BOITE DE DIALOG POUR LA CONFIRMATION DE SUPPRESSION
        function confirmationSuppression(id){
    	  $( "#confirmationSuppressionIntervention" ).dialog({
    	    resizable: false,
    	    height:230,
    	    width:405,
    	    autoOpen: false,
    	    modal: true,
    	    buttons: {
    	        "Oui": function() {
    	            $( this ).dialog( "close" );

    	            confirmationSuppressionDef(id);
      	            $("#confirmationSuppressionInterventionDef").dialog('open');
    	        },
    	        "Annuler": function() {
                    $( this ).dialog( "close" );
                }
    	   }
    	  });
        }
        
        function supprimerintervention(id){
        	confirmationSuppression(id);
           $("#confirmationSuppressionIntervention").dialog('open');
       	}
        
      //BOITE DE DIALOG POUR LA CONFIRMATION DE SUPPRESSION
        function confirmationSuppressionUneIntervention(numero_intervention, id_personne){
    	  $( "#confirmationSuppressionUneIntervention" ).dialog({
    	    resizable: false,
    	    height:180,
    	    width:405,
    	    autoOpen: false,
    	    modal: true,
    	    buttons: {
    	        "Oui": function() {
    	            $( this ).dialog( "close" );

    	            var cle = numero_intervention;
      	            var chemin = tabUrl[0]+'public/personnel/supprimer-une-intervention';
      	            $.ajax({
      	                type: 'POST',
      	                url: chemin ,
      	                data: {'numero_intervention': cle, 'id_personne': id_personne},
      	                success: function(data) { 
      	                	$("#"+cle).fadeOut(function(){
      	                	 	 $("#"+cle).empty();
      	                	 	var result = jQuery.parseJSON(data); 
       	        	    	    $("#listeinterventionbis").html(result);
      	                	});
      	                },
      	                error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
      	                dataType: "html"
      	            });
      	    	     return false;
    	            
    	        },
    	        "Annuler": function() {
                    $( this ).dialog( "close" );
                }
    	   }
    	  });
        }
        
        function supprimeruneintervention(numero_intervention, id_personne){
        	confirmationSuppressionUneIntervention(numero_intervention, id_personne);
        	$( "#confirmationSuppressionUneIntervention" ).dialog('open');
        }
        
        /************************************************************************************************************************/
        /************************************************************************************************************************/
        /************************************************************************************************************************/

        /***************************************************************************************/
        
        /**======================== MODIFICATION INTERVENTION ================================**/
        
        /***************************************************************************************/
        function Modifierinterventions(id, idAgent){
        	$( "#modifications" ).dialog({
        	    resizable: false,
        	    height:455,
        	    width:1150,
        	    autoOpen: false,
        	    modal: true,
        	    buttons: {
        	        "Enregistrer": function() {
        	            $( this ).dialog( "close" );  
        	            var type_intervention = $('#type_intervention').val();
        	            
        	            var service_origine = $('#service_origine').val();
        	            var id_service = $('#id_service').val();
        	            var date_debut = $('#date_debut').val();
        	            var date_fin = $('#date_fin').val();
        	            var motif_intervention = $('#motif_intervention').val();
        	            var note = $('#note').val();
        	            
        	            var service_origine_externe = $('#service_origine_externe').val();
        	            var hopital_accueil  = $('#hopital_accueil').val();
        	            var id_service_externe = $('#id_service_externe').val();
        	            var date_debut_externe = $('#date_debut_externe').val();
        	            var date_fin_externe = $('#date_fin_externe').val();
        	            var motif_intervention_externe = $('#motif_intervention_externe').val();
        	            var note_externe = $('#note_externe').val();
        	            
        	            $.ajax({
        	                type: 'POST',  
        	                url: tabUrl[0]+'public/personnel/save-intervention' ,  
        	                data: ({'numero_intervention':id ,'id_personne':idAgent , 'id_verif':1, 'type_intervention':type_intervention , 
        	                	'service_origine':service_origine , 'id_service':id_service, 'date_debut':date_debut, 
        	                	'date_fin':date_fin, 'motif_intervention':motif_intervention, 'note':note, 
        	                	'service_origine_externe':service_origine_externe, 'hopital_accueil':hopital_accueil, 
        	                	'id_service_externe':id_service_externe, 'date_debut_externe':date_debut_externe, 
        	                	'date_fin_externe':date_fin_externe, 'motif_intervention_externe':motif_intervention_externe,
        	                	'note_externe':note_externe
        	                	}),
        	        	    success: function(data) {   /* C JUSTE POUR RAFFRAICHIR*/ 
        	        	    	 var result = jQuery.parseJSON(data); 
        	        	    	 $("#listeinterventionbis").html(result);
        	        	    },
        	                error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
        	                dataType: "html"
        	        	});
        	        },
        	        "Annuler": function() {
                        $( this ).dialog( "close" );             	     
                        return false;
                    }
        	   }
        	  });
          }
        
        function modifierintervention(idIntervention, idAgent){
        	Modifierinterventions(idIntervention,idAgent);
            var chemin = tabUrl[0]+'public/personnel/modifier-intervention-agent';
            $.ajax({
                type: 'POST',
                url: chemin ,
                data: $(this).serialize(),  
                data:({'idIntervention':idIntervention , 'idAgent':idAgent}),
                success: function(data) {    
                	     var result = jQuery.parseJSON(data);   
                	     $("#info_modif").html(result);
                	     
                	     $("#modifications").dialog('open'); 
                },
                error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
                dataType: "html"
            });
        }
        
        /************************************************************************************************************************/
        /************************************************************************************************************************/
        /************************************************************************************************************************/
        
        function calendrier() {
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
        	
        	/***************************************************************
        	 *======= LISTE DES SERVICES D'UN HOPITAL -- ===================
        	 ***************************************************************
        	 **************************************************************/
        }