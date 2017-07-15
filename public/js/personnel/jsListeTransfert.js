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

    					"sAjaxSource": ""+tabUrl[0]+"public/personnel/liste-transfert-ajax", 
    					
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
        var chemin = tabUrl[0]+'public/personnel/info-personnel';
        $.ajax({
            type: 'POST',
            url: chemin ,
            data: $(this).serialize(),  
            data: ({'id':cle, 'identif': 1}),
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
  	    	$("#vue_patient").fadeOut(function(){$("#contenu").fadeIn("fast"); });
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
    	            $("#service_accueil_externe").html(result);
    	      },
    	      error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
    	        dataType: "html"
    	    });

    	    return false;
    	 }
    /************************************************************************************************************************/
    /************************************************************************************************************************/
    /************************************************************************************************************************/