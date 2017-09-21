    var base_url = window.location.toString();
	var tabUrl = base_url.split("public");
    
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
		 * INFO BULLE DE LA LISTE
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
					   	
			   "sAjaxSource": ""+tabUrl[0]+"public/infirmerie/liste-non-conformite-ajax", 
			   
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

    $(".boutonAnnuler").html('<button type="submit" id="terminer" style=" font-family: police2; font-size: 17px; font-weight: bold;"> Annuler </button>');
    $(".boutonValider").html('<button type="submit" id="terminer" style=" font-family: police2; font-size: 17px; font-weight: bold;"> Valider </button>');

    $(".boutonTerminer").html('<button type="submit" id="terminer" style=" font-family: police2; font-size: 17px; font-weight: bold;"> Terminer </button>');

    }
    
    
    function clickRowHandler() 
    {
    	var idPatient;
    	var idFacturation;
    	
    	$('#patientAdmis tbody tr').contextmenu({
    		target: '#context-menu',
    		onItem: function (context, e) { 
    			
    			if($(e.target).text() == 'Visualiser' || $(e.target).is('#visualiserCTX')){
    				if(idPatient && idFacturation){ PrelevementTrieNonConforme(idPatient,idFacturation); }
    			} 
    			
    		}
    	
    	}).bind('mousedown', function (e) {
    			var aData = oTable.fnGetData( this );
    		    idPatient = aData[8];
    		    idFacturation = aData[7];
    	});
    	
    	
    	
    	$("#patientAdmis tbody tr").bind('dblclick', function (event) {
    		var aData = oTable.fnGetData( this );
    		var idPatient = aData[8];
		    var idFacturation = aData[7];
    		
    		if(idPatient && idFacturation){ PrelevementTrieNonConforme(idPatient,idFacturation); }
    	});
    	
    	$('a,img,hass').tooltip({ animation: true, html: true, placement: 'bottom', show: { effect: 'slideDown', } });
    }
    
    
    function bilanPrelevement(idfacturation){
    	
    	$('#boutonAnnulerTerminer').toggle(false);
    	$('#boutonTerminer').toggle(true);
    	
        var chemin = tabUrl[0]+'public/infirmerie/bilan-analyses-facturees';
        $.ajax({
            type: 'POST',
            url: chemin ,
            data:{'idfacturation':idfacturation},
            success: function(data) {
       	    
            	$('#titre span').html('INFORMATIONS SUR LE BILAN');
            	     var result = jQuery.parseJSON(data);  
            	     $("#contenu").fadeOut(function(){ $("#vue_patient").html(result); $("#interfaceListeFactures").fadeIn("fast"); }); 
            	     
                     $('.boutonTerminer').click(function(){
                    	 
                    	 $('#interfaceListeFactures').fadeOut(function(){
             	    		$('#titre span').html('LISTE DES PATIENTS'); 
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
    
    function getdifficultes(val)
    {
    	if(val == 0){
    		$('.reductTextarea textarea').val('Néant').attr({'readonly':true});
    	}else{
    		$('.reductTextarea textarea').val('').attr({'readonly':false});
    	}
    }

    function getMomentTransfusion(val)
    {
    	if(val == 0){
    		$('.reductSelect2 select').val('').attr({'disabled':true});
    	}else{
    		$('.reductSelect2 select').val(1).attr({'disabled':false});
    	}
    }
    
    
    function initForm()
    {
    	
    	$('#date_heure').datetimepicker(
    		$.datepicker.regional['fr'] = {
    			dateFormat: 'dd/mm/yy -', 
    			timeText: 'H:M', 
    			hourText: 'Heure', 
    			minuteText: 'Minute', 
    			currentText: 'Actuellement', 
    			closeText: 'F',
    		} 
    	);
    	
    }
    
  
    
    function PrelevementTrieNonConforme(idpatient, idfacturation)
    {
    	$('#boutonTerminer').toggle(true);
    	
        var chemin = tabUrl[0]+'public/infirmerie/liste-analyses-triees-non-conformes';
        $.ajax({
            type: 'POST',
            url: chemin ,
            data:{'idpatient':idpatient, 'idfacturation':idfacturation},
            success: function(data) {
       	    
            	$('#titre span').html('TRI DES PRELEVEMENTS');
            	var result = jQuery.parseJSON(data);  
            	$("#contenu").fadeOut(function(){ $("#vue_patient").html(result); $("#interfaceListePrelevement").fadeIn("fast"); }); 
            	     
            	$('.boutonTerminer').click(function(){
            	    	 
            		$('#interfaceListePrelevement').fadeOut(function(){
            			$('#titre span').html('LISTE DES PATIENTS'); 
            			$('#contenu').fadeIn(300);
            		});
            	    		 
            	});
            	     
            },
            error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
            dataType: "html"
        });
    }
	
    
    
    function getConformite(val, id){
    	
    	if(val == 0){ 
    		$('#conformitePrelevement_'+id+' select').css({'color':'red'});
    		$('#noteConformite_'+id).html('<input type="text" name="noteNonConformite_'+id+'" required=true style="width: 100%; height: 100%; padding-left: 5px; font-size: 13px; font-family: time new romans;" >');
    	}
    	else
    	if(val == 1){
    		$('#conformitePrelevement_'+id+' select').css({'color':'black'});
    		$('#noteConformite_'+id).html('<img style="margin-left: 15px;" src="'+tabUrl[0]+'public/images_icons/tick_16.png"  />');
    	}
    	else{
    		$('#noteConformite_'+id).html('');
    	}
    	
    }
    
    
    function popupReprise(){
    	  
    	$( "#reprisePrelevement" ).dialog({
    		resizable: false,
    		height:600,
    	    width:800,
    	    autoOpen: false,
    	    modal: true,
    	    buttons: {
    	        
    	    	"Annuler": function() {
    	            $( this ).dialog( "close" );             	     
    	            return false;
    	        },
    	        "Valider": function() {
    	        	
    	        	if( $('#difficultes_prelevement').val()=='' ){
    	        		$('#difficultes_prelevement').attr('required', true); 
    	        	}
    	        	$('#formulaireBilanRepris').trigger('click');

    	        }
    	    
    	    }

    	});
    	
    }
    
    function popupReprisePrelevement(idfacturation, idbilan){

    	popupReprise();
    	
    	var chemin = tabUrl[0]+'public/infirmerie/reprise-prelevement';
        $.ajax({
            type: 'POST',
            url: chemin ,
            data:{'idfacturation': idfacturation, 'idbilan': idbilan},
            success: function(data) {
       	    
            	var result = jQuery.parseJSON(data); 
            	
            	$("#reprisePrelevement").html(result); 
            	$("#reprisePrelevement").dialog('open'); 
            	     
            },
        });
            
    }