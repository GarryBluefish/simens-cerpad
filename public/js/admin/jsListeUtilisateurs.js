    var base_url = window.location.toString();
    var tabUrl = base_url.split("public");
    
    /************************************************************************************************************************/
    /************************************************************************************************************************/
    /************************************************************************************************************************/
    $(function(){
    	initialisation();
    	});
    function initialisation(){
        var  oTable = $('#utilisateurs').dataTable
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

    					"sAjaxSource": ""+tabUrl[0]+"public/admin/liste-utilisateurs-ajax", 
    					
    	}); 
        
        var asInitVals = new Array();
    
   	//le filtre du select
   	$('#filter_statut').change(function() 
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

    $("#annuler").click(function(){
    	vart=tabUrl[0]+'public/admin/utilisateurs';
	    $(location).attr("href",vart);
	    return false;
	});
  
    }
    
    /************************************************************************************************************************/
    /************************************************************************************************************************/
    /************************************************************************************************************************/
    function modifier(id){
         
         $.ajax({
            type: 'POST',
            url: tabUrl[0]+'public/admin/modifier-utilisateur' ,
            data:{'id':id},
            success: function(data) {
           	         
            	var result = jQuery.parseJSON(data);
            	$("#titre").replaceWith("<div id='titre' style='font-family: police2; color: green; font-size: 20px; font-weight: bold; padding-left:20px;'><iS style='font-size: 25px;'>&curren;</iS> MODIFICATION DES DONNEES </div>");
            	$("#scriptFormUtilisationPopulate").html(result);
            	$("#contenu").fadeOut(function(){
            		$("#FormUtilisateur").fadeIn("fast"); 
            		$("#previous").toggle(false);
            	}); 
            	     
            },
            error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
            dataType: "html"
        });
         
         
   	}
    
    function viderChamp(){
    	$("#nomUtilisateur").val('');
    	$("#prenomUtilisateur").val('');
    	$("#username").val('');
    	$("#password").val('');
    	$("#fonction").val('');
    	$("#service").val('');
    }
    
    function initialisationListePersonnel(){
    	var asInitValss = new Array();
    	var  oTablee = $('#personnel').dataTable
    	( {
    		"sPaginationType": "full_numbers",
    		"aLengthMenu": [5,7,10,15],
    			"aaSorting": [],
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

    		"sAjaxSource": ""+tabUrl[0]+"public/admin/liste-agent-personnel-ajax", 
    						
    	} );

    	$("thead input").keyup( function () {
    		/* Filter on the column (the index) of this element */
    		oTablee.fnFilter( this.value, $("thead input").index(this) );
    	} );

    	/*
    	* Support functions to provide a little bit of 'user friendlyness' to the textboxes in 
    	* the footer
    	*/
    	$("thead input").each( function (i) {
    		asInitValss[i] = this.value;
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
    			this.value = asInitValss[$("thead input").index(this)];
    		}
    	} );
       
    }
    
    function ajouterUtilisateur(){
    	$("#previous").toggle(true);
    	viderChamp();
    	$('#id').val("");
    	var role = $('#RoleSelect').val();
    	$('input[type=radio][name=role][value='+role+']').attr('checked', false);
    	$("#contenu").fadeOut(function(){
    		$("#listeRecherche").fadeIn("fast"); 
    		$("#titre").replaceWith("<div id='titre' style='font-family: police2; color: green; font-size: 20px; font-weight: bold; padding-left:20px;'><iS style='font-size: 25px;'>&curren;</iS> RECHERCHE DU NOUVEL UTILISATEUR A AJOUTER </div>");
    		
    		$('div .dataTables_paginate').css({ 'margin-right' : '40px'});
	        $('#listeDataTable').css({'padding-left' : '-20px'});
    	});
    }
   
    function listeUtilisateurs(){
    	$("#listeRecherche").fadeOut(function(){
    		$("#contenu").fadeIn("fast"); 
    		$("#titre").replaceWith("<div id='titre' style='font-family: police2; color: green; font-size: 20px; font-weight: bold; padding-left:20px;'><iS style='font-size: 25px;'>&curren;</iS> LISTE DES UTILISATEURS </div>");
    		
    		$('div .dataTables_paginate').css({ 'margin-right' : '0px'});
    	});
    }
    
    function confirmation(id){
    	$( "#visualisation" ).dialog({
    	    resizable: false,
    	    height:400,
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
    
    function visualiser(id){
   	 confirmation(id);
   	 var cle = id;
        var chemin = tabUrl[0]+'public/admin/visualisation';
        $.ajax({
            type: 'POST',
            url: chemin ,
            data: $(this).serialize(),  
            data:'id='+cle,
            success: function(data) {    
            	    var result = jQuery.parseJSON(data);  
            	     $("#info").html(result);
            	     
            	     $("#visualisation").dialog('open'); 
            	       
            },
            error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
            dataType: "html"
        });
   }
    
    
   function nouvelUtilisateur(id){ 
	
		$("#titre").replaceWith("<div id='titre' style='font-family: police2; color: green; font-size: 20px; font-weight: bold; padding-left:20px;'><iS style='font-size: 25px;'>&curren;</iS> NOUVEL UTILISATEUR</div>");
	    var cle = id;
	    var chemin = tabUrl[0]+'public/admin/nouvel-utilisateur';
	    $.ajax({
	        type: 'POST',
	        url: chemin ,
	        data:'id='+cle,
	        success: function(data) {    
	        	    var result = jQuery.parseJSON(data);  
	        	     $("#scriptFormUtilisationPopulate").html(result);
	        	     //PASSER A SUIVANT
	        	     $('#FormUtilisateur').animate({
	        	         height : 'toggle'
	        	      },1000);
	        	     $('#listeRecherche').animate({
	        	         height : 'toggle'
	        	     },1000);
	        },
	        error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
	        dataType: "html"
	    });
   }
   
   function listePrecedent(){
		$("#titre").replaceWith("<div id='titre' style='font-family: police2; color: green; font-size: 20px; font-weight: bold; padding-left:20px;'><iS style='font-size: 25px;'>&curren;</iS> RECHERCHE DU NOUVEL UTILISATEUR A AJOUTER </div>");

		$('#FormUtilisateur').animate({
	         height : 'toggle'
	      },1000);
	     $('#listeRecherche').animate({
	         height : 'toggle'
	     },1000);
	     
	     return false;
   }