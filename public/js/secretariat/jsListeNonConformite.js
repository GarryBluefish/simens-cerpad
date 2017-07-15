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
					   	
			   "sAjaxSource": ""+tabUrl[0]+"public/secretariat/liste-non-conformite-ajax", 
			   
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
    	var id;
    	$('#patientAdmis tbody tr').contextmenu({
    		target: '#context-menu',
    		onItem: function (context, e) { 
    			
    			if($(e.target).text() == 'Visualiser' || $(e.target).is('#visualiserCTX')){
    				if(id){ visualiser(id); }
    			} 
    			
    		}
    	
    	}).bind('mousedown', function (e) {
    			var aData = oTable.fnGetData( this );
    		    id = aData[0]; 
    	});
    	
    	
    	
    	$("#patientAdmis tbody tr").bind('dblclick', function (event) {
    		var aData = oTable.fnGetData( this );
    		var id = aData[0]; 
    		if(id){ visualiser(id); }
    	});
    	
    }
    
    
    function visualiser(id)
    {
        $.ajax({
            type: 'POST',
            url:  tabUrl[0]+'public/secretariat/infos-patient' ,
            data:'id='+id,
            success: function(data) {
            	     var result = jQuery.parseJSON(data);  
            	     
            	     $('#vue_patient').html(result);
            	     $('#contenu').fadeOut(function(){
            	    	 $('#titre span').html('ETAT CIVIL DU PATIENT'); 
            	    	 $('#vue_patient').fadeIn();
            	     });
            	     
            	     $('#terminer').click(function(){
            	    	 $('#vue_patient').fadeOut(function(){
            	    		 $('#titre span').html('LISTE DES PATIENTS'); 
                	    	 $('#contenu').fadeIn();
                	    	 $('#vue_patient').html("");
                	     });
            	     });
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
    
    
    //Rappel du patient
    //Rappel du patient
    //Rappel du patient
    function patientRappeler(idbilan)
    {
    	$('#rappel_patient_'+idbilan).w2overlay({ html: "" +
    		"" +
  			"<div style='border-bottom:1px solid green; height: 30px; background: #f9f9f9; width: 180px; text-align:center; padding-top: 10px; font-size: 13px; color: green; font-weight: bold;'>Patient rappel&eacute; </div>" +
  			"<div style='height: 50px; width: 180px; padding-top:10px; text-align:center;'>" +
  			"<button class='btn' style='cursor:pointer;' onclick='popupFermer(); return false;'>Non</button>" +
  			"<button class='btn' style='cursor:pointer;' onclick='patientRappelerConfirmer("+idbilan+"); return false;'>Oui</button>" +
  			"</div>" +
  			"" 
    	});
    	
    }
    
    
    function popupFermer() {
    	$(null).w2overlay(null);
    }
     
    
    function patientRappelerConfirmer(idbilan) {
    	$(null).w2overlay(null); 
    	
        $.ajax({
            type: 'POST',
            url: tabUrl[0]+'public/secretariat/patient-rappeler',
            data:'idbilan='+idbilan,
            success: function(data) {
            	     
            	var result = jQuery.parseJSON(data);  
            	
            	$('.rappel_patient_'+idbilan).html("");
            	$('.rappel_patient_'+idbilan).html("<a id='rappel_patient_"+idbilan+"' href='javascript:patientNonRappeler("+idbilan+")' ><img style='display: inline; margin-right: 5%;' src='"+tabUrl[0]+"public/images_icons/applik.png' ></a>");
            	
            	
            }
        });
    	
    }
    
    
    
    //Annuler le rappel du patient
    //Annuler le rappel du patient
    //Annuler le rappel du patient
    function patientNonRappeler(idbilan)
    {
    	$('#rappel_patient_'+idbilan).w2overlay({ html: "" +
    		"" +
  			"<div style='border-bottom:1px solid green; height: 30px; background: #f9f9f9; width: 180px; text-align:center; padding-top: 10px; font-size: 13px; color: green; font-weight: bold;'>Patient non joint </div>" +
  			"<div style='height: 50px; width: 180px; padding-top:10px; text-align:center;'>" +
  			"<button class='btn' style='cursor:pointer;' onclick='popupFermer(); return false;'>Non</button>" +
  			"<button class='btn' style='cursor:pointer;' onclick='patientNonRappelerConfirmer("+idbilan+"); return false;'>Oui</button>" +
  			"</div>" +
  			"" 
    	});
    	
    }
    
    
    function popupFermer() {
    	$(null).w2overlay(null);
    }
     
    
    function patientNonRappelerConfirmer(idbilan) {
    	$(null).w2overlay(null); 
    	
        $.ajax({
            type: 'POST',
            url: tabUrl[0]+'public/secretariat/patient-non-rappeler' ,
            data:'idbilan='+idbilan,
            success: function(data) {
            	     
            	var result = jQuery.parseJSON(data);  
            	
            	$('.rappel_patient_'+idbilan).html("");
            	$('.rappel_patient_'+idbilan).html("<a id='rappel_patient_"+idbilan+"' href='javascript:patientRappeler("+idbilan+")' ><img style='display: inline; margin-right: 5%;' src='"+tabUrl[0]+"public/images_icons/appel-telephone-25.png' ></a>");

            }
        });
    	
    }
    
    
    