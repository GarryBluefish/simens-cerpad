    var base_url = window.location.toString();
	var tabUrl = base_url.split("public");
	//BOITE DE DIALOG POUR LA CONFIRMATION DE SUPPRESSION
    function confirmation(idfacturation){ 
	  $( "#confirmation" ).dialog({
	    resizable: false,
	    height:170,
	    width:435,
	    autoOpen: false,
	    modal: true,
	    buttons: {
	        "Oui": function() {
	            $( this ).dialog( "close" ); 
	            
	            var chemin = tabUrl[0]+'public/facturation/supprimer-facturation-consultation';
	            $.ajax({
	                type: 'POST',
	                url: chemin ,
	                data:{ 'idfacturation':idfacturation },
	                success: function(data) {
	                	     var result = jQuery.parseJSON(data);  
	                	     if(result == 1){
	                	    	 alert('impossible de supprimer la consultation est deja effecutee '); 
	                	    	 $(location).attr("href",tabUrl[0]+"public/facturation/liste-patients-admis-consultation");
	                	     } else {
		                	     $("#"+idfacturation).parent().parent().parent().fadeOut(function(){ 
		                	    	 $(location).attr("href",tabUrl[0]+"public/facturation/liste-patients-admis-consultation");
		                	     });
	                	     }
	                	     
	                },
	                error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
	                dataType: "html"
	            });
	    	     
	    	     
	        },
	        "Annuler": function() {
                $( this ).dialog( "close" );
            }
	   }
	  });
    }
    
    function supprimer(idfacturation){
   	   confirmation(idfacturation);
       $("#confirmation").dialog('open');
   	}
    
    function listepatient(){
    	//Lorsqu'on clique sur terminer �a ram�ne la liste des ptients admis 
	    $("#terminer").click(function(){
	    	$("#titre2").replaceWith("<div id='titre' style='font-family: police2; color: green; font-size: 18px; font-weight: bold; padding-left: 20px;'><iS style='font-size: 25px;'>&curren;</iS> LISTE DES PATIENTS </div>");
  	    	$("#vue_patient").fadeOut(function(){$("#contenu").fadeIn("fast"); });
  	    });
    }
    
    /**********************************************************************************/
    /**********************************************************************************/
    /**********************************************************************************/
    /**********************************************************************************/

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
    function initialisation(){	
    	
     var asInitVals = new Array();
	 oTable = $('#patientAdmis').dataTable
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
					   	
			   "sAjaxSource": ""+tabUrl[0]+"public/facturation/liste-patients-admis-consultation-ajax", 
			   
			   "fnDrawCallback": function() 
				{
					//markLine();
					clickRowHandler();
				}
	} );

	//le filtre du select
	$('#filter_statut').change(function() 
	{					
		oTable.fnFilter( this.value );
	});
	
	$('#liste_service').change(function()
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

    $(".boutonTerminer").html('<button type="submit" id="terminer" style=" font-family: police2; font-size: 17px; font-weight: bold;"> Terminer </button>');
    
    }
    
    
    function clickRowHandler() 
    {
    	var id;
    	$('#patientAdmis tbody tr').contextmenu({
    		target: '#context-menu',
    		onItem: function (context, e) { 
    			
    			if($(e.target).text() == 'Visualiser' || $(e.target).is('#visualiserCTX')){
    				if(id){ vueAdmissionConsultation(id); }
    			} 
    			
    		}
    	
    	}).bind('mousedown', function (e) {
    			var aData = oTable.fnGetData( this );
    		    id = aData[7]; 
    	});
    	
    	
    	
    	$("#patientAdmis tbody tr").bind('dblclick', function (event) {
    		var aData = oTable.fnGetData( this );
    		var id = aData[7]; 
    		if(id){ vueAdmissionConsultation(id); }
    	});
    	
    	$('a,img,hass').tooltip({ animation: true, html: true, placement: 'bottom', show: { effect: 'slideDown', } });
    }
    
    function vueAdmissionConsultation(idfacturation){ 
        var chemin = tabUrl[0]+'public/facturation/vue-infos-patients-admission-consultation';
        $.ajax({
            type: 'POST',
            url: chemin ,
            data:{'idfacturation':idfacturation},
            success: function(data) {
       	    
            	$('#titre span').html('INFORMATIONS SUR LE PATIENT');
            	     var result = jQuery.parseJSON(data);  
            	     $("#contenu").fadeOut(function(){ $("#vue_patient").html(result); $("#interfaceListeFactures").fadeIn("fast"); }); 
            	     
            	     $('.boutonTerminer').click(function(){
            	    	 $('#interfaceListeFactures').fadeOut(function(){
            	    		 $('#titre span').html('LISTE DES PATIENTS '); 
            	    		 $('#contenu').fadeIn(300);
            	    	 });
            	    		 
            	     });
            },
            error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
            dataType: "html"
        });
    }
    
    
    function listeAnalysesFacturees(idfacturation){ 
        var chemin = tabUrl[0]+'public/facturation/liste-analyses-facturees';
        $.ajax({
            type: 'POST',
            url: chemin ,
            data:{'idfacturation':idfacturation},
            success: function(data) {
       	    
            	$('#titre span').html('INFORMATIONS SUR LE PATIENT');
            	     var result = jQuery.parseJSON(data);  
            	     $("#contenu").fadeOut(function(){ $("#vue_patient").html(result); $("#interfaceListeFactures").fadeIn("fast"); }); 
            	     
            	     $('.boutonTerminer').click(function(){
            	    	 $('#interfaceListeFactures').fadeOut(function(){
            	    		 $('#titre span').html('LISTE DES PATIENTS ADMIS'); 
            	    		 $('#contenu').fadeIn(300);
            	    	 });
            	    		 
            	     });
            },
            error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
            dataType: "html"
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

    
	//Pour l'impression de la facture
	//Pour l'impression de la facture
    
    function impressionFacture(idfacturation) { 
    		
    	var vart = tabUrl[0]+"public/facturation/impression-facture";
            
    	var formulaireImprimerFacture = document.getElementById("FormulaireImprimerFacture");
    	formulaireImprimerFacture.setAttribute("action", vart);
    	formulaireImprimerFacture.setAttribute("method", "POST");
    	formulaireImprimerFacture.setAttribute("target", "_blank");
        
    	var champ = document.createElement('input');
    	champ.setAttribute('type', 'hidden');
    	champ.setAttribute('name', 'idfacturation');
    	champ.setAttribute('value', idfacturation);
    	formulaireImprimerFacture.appendChild(champ);
    	
    	$("#ImprimerFacture").trigger('click');
    	
    	setTimeout(function(){
    		$("#liste_admis_style").trigger('click');
    	},1000);
    	
    }
    
    function imprimerFacture() {
    	var idfacturation = $('#idfacturation').val();
    	
    	if(idfacturation != 0){
        	impressionFacture($('#idfacturation').val());
    	}
    }

	