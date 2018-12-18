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
	            
	            var chemin = tabUrl[0]+'public/facturation/supprimer-facturation';
	            $.ajax({
	                type: 'POST',
	                url: chemin ,
	                data:{ 'idfacturation':idfacturation },
	                success: function(data) {
	                	     var result = jQuery.parseJSON(data);  
	                	     if(result == 1){
	                	    	 alert('impossible de supprimer il y a des analyses faisant l\'objet de prelevement'); return false;
	                	     } else {
		                	     $("#"+idfacturation).parent().parent().parent().fadeOut(function(){ 
		                	    	 $(location).attr("href",tabUrl[0]+"public/facturation/liste-patients-admis");
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
	    	$("#titre2").replaceWith("<div id='titre' style='font-family: police2; color: green; font-size: 18px; font-weight: bold; padding-left: 20px;'><iS style='font-size: 25px;'>&curren;</iS> LISTE DES PATIENTS ADMIS </div>");
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
					   	
			   "sAjaxSource": ""+tabUrl[0]+"public/facturation/liste-patients-priseencharge-ajax", 
			   
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
    				if(id){ listeAnalysesFacturees(id); }
    			} 
    			
    		}
    	
    	}).bind('mousedown', function (e) {
    			var aData = oTable.fnGetData( this );
    		    id = aData[7]; 
    	});
    	
    	
    	
    	$("#patientAdmis tbody tr").bind('dblclick', function (event) {
    		var aData = oTable.fnGetData( this );
    		var id = aData[7]; 
    		if(id){ listeAnalysesFacturees(id); }
    	});
    	
    	$('a,img,hass').tooltip({ animation: true, html: true, placement: 'bottom', show: { effect: 'slideDown', } });
    }
    
    
    function listeAnalysesFacturees(idfacturation){
        var chemin = tabUrl[0]+'public/facturation/liste-analyses-facturees';
        $.ajax({
            type: 'POST',
            url: chemin ,
            data:{'idfacturation':idfacturation, 'priseencharge' : 1},
            success: function(data) {
       	    
            	$('#titre span').html('INFORMATIONS SUR LE PATIENT');
            	     var result = jQuery.parseJSON(data);  
            	     $("#contenu").fadeOut(function(){ $("#vue_patient").html(result); $("#interfaceListeFactures").fadeIn("fast"); }); 
            	     
            	     $('.boutonTerminer').click(function(){
            	    	 $('#interfaceListeFactures').fadeOut(function(){
            	    		 $('#titre span').html('LISTE DES PATIENTS PRISES EN CHARGE'); 
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
    	},3000);
    	
    }
    
    function imprimerFacture(){
    	var idfacturation = $('#idfacturation').val();

    	if(idfacturation != 0){
        	impressionFacture(idfacturation);
    	}
    }

    function initForm(){
    	
    	$('.date_heure_facturation').datetimepicker(
    		$.datepicker.regional['fr'] = {
    			dateFormat: 'dd/mm/yy -', 
    			timeText: 'H:M', 
    			hourText: 'Heure', 
    			minuteText: 'Minute', 
    			currentText: 'Actuellement', 
    			closeText: 'F',
    			//showAnim : 'bounce',
    			maxDate : '0',
    		} 
    	);
    	
    }
    
    var idfacturationAReglerPeC = 0;
	function reglementPriseEnCharge(idfacturation){
		idfacturationAReglerPeC = idfacturation;
		
		$('#priseenchargeARegler_'+idfacturation).w2overlay({ html: "" +
    		"" +
    		"<div style='border-bottom:1px solid green; height: 30px; background: #f9f9f9; width: 300px; text-align:center; padding-top: 10px; font-size: 13px; color: green; font-weight: bold;'><img style='padding-right: 10px; width: 32px; height: 16px;' src='../images_icons/regler_PeC_2.png' >R&egrave;glement de la prise en charge</div>" +
    		"<div style='height: 175px; width: 300px; padding-top:5px; padding-left: 6px; text-align:center;'>" +
    		"<span id='labelHeureLABEL' style='float: left; margin-left: 3px;'>Date & Heure </span> <input required id='zoneChampInfo1' class='date_heure_reglement' type='datetime-local' style='padding-left: 10px; font-size: 19px; height: 25px; width: 95%; max-width: 95%;'> " +
    		"<span id='labelHeureLABEL' style='float: left; margin-left: 3px;'>Type de paiement </span> <select id='zoneChampInfo1' class='type_reglement' type='text' style='height: 25px; width: 95%; max-width: 95%; padding-left: 10px; font-size: 19px;'> <option></option> <option value='espece'>Esp&egrave;ce</option> <option value='cheque'>Ch&egrave;que</option> <option value='virement'>Virement</option> </select>" +
    		"<button class='btn' style='cursor:pointer; margin-top: 7px;' onclick='popupAnnuler(); return false;'>Annuler</button>" +
    		"<button class='btn' style='cursor:pointer; margin-top: 7px;' onclick='popupValider(); return false;'>Valider</button>" +
    		"</div>"+
    		"<script> $('.date_heure_reglement').val($('#date_heure_reglement').val()); </script>"+
    		"<script> $('.type_reglement').val($('#type_reglement').val()); </script>"+
    		
    		"<script> $('.date_heure_reglement').change(function(){ $('#date_heure_reglement').val($('.date_heure_reglement').val()); }); </script>"+
    		"<script> $('.date_heure_reglement').keyup(function(){ $('#date_heure_reglement').val($('.date_heure_reglement').val()); }); </script>"+
    		"<script> $('.type_reglement').change(function(){ $('#type_reglement').val($('.type_reglement').val()); }); </script>"+
    		"<script> $('.type_reglement').keyup(function(){ $('#type_reglement').val($('.type_reglement').val()); }); </script>"
    	});
		
	}
	
	
	function popupAnnuler(){
		$(null).w2overlay(null);
		$('#date_heure_reglement').val("");
		$('#type_reglement').val("");
	}

	function popupValider(){
		$(null).w2overlay(null);
		var date_heure_reglement = $('#date_heure_reglement').val();
		var type_reglement = $('#type_reglement').val();
		var idfacturation = idfacturationAReglerPeC;
		
		$('#priseenchargeARegler_'+idfacturation).html('<table style=""> <tr> <td> <img style="width: 48px; height: 48px;" src="../images/loading/Chargement_1.gif" /> </td> <td style="padding-left: 6px;"> Validation en cours ... </td> </tr><table>');
		
        $.ajax({
            type: 'POST',
            url: tabUrl[0]+'public/facturation/reglement_priseencharge',
            data:{'idfacturation':idfacturation, 'date_heure_reglement' : date_heure_reglement, 'type_reglement' : type_reglement },
            success: function(data) {
       	    
            	var result = jQuery.parseJSON(data); 
            	if(result == 1){
                	$('#priseenchargeARegler_'+idfacturation).html('<table style=""> <tr> <td> <img style="width: 48px; height: 48px;" src="../images_icons/Valider_1.png" /> </td> <td style="padding-left: 6px; color: green; font-style: italic; font-family: time new roman; font-size: 20px;"> Facture r&eacute;gl&eacute;e </td> <td style="width: 100px; padding-left: 6px;"> <img onclick="annulerReglementPriseEnCharge('+idfacturation+');" style="width: 18px; height: 18px; float: right; cursor: pointer;" src="../images_icons/annuler_reg.png" title="Annuler" /> </td> </tr></table>');	
            	}else{
            		$('#priseenchargeARegler_'+idfacturation).html("<img onclick='reglementPriseEnCharge("+idfacturation+");' style='width: 48px; height: 48px; float: left; cursor: pointer;' src='../images_icons/regler_PeC_2.png' title='r&eacute;gler la prise en charge' /> <span class='info_date_nonrenseigne' style='padding-left: 10px; color: red; font-style: italic; font-family: time new roman; font-size: 14px;'> impossible de valider - date non renseign&eacute;e </span>");
            		$('.info_date_nonrenseigne').fadeOut(20000);
            	}

            	$('a,img,hass').tooltip({ animation: true, html: true, placement: 'bottom', show: { effect: 'slideDown', } });
            }
        });
        
	}
	
	function annulerReglementPriseEnCharge(idfacturation){ 
		$('#priseenchargeARegler_'+idfacturation).html('<table style=""> <tr> <td> <img style="width: 48px; height: 48px;" src="../images/loading/Chargement_1.gif" /> </td> <td style="padding-left: 6px;"> Annulation en cours ... </td> </tr><table>');
		
		$.ajax({
            type: 'POST',
            url: tabUrl[0]+'public/facturation/annuler_reglement_priseencharge',
            data:{'idfacturation':idfacturation},
            success: function(data) {
            
            	$('#priseenchargeARegler_'+idfacturation).html("<img onclick='reglementPriseEnCharge("+idfacturation+");' style='width: 48px; height: 48px; float: left; cursor: pointer;' src='../images_icons/regler_PeC_2.png' title='r&eacute;gler la prise en charge' /> <span class='info_date_nonrenseigne' style='padding-left: 10px; color: red; font-style: italic; font-family: time new roman; font-size: 14px;'> r&egrave;glement annul&eacute; </span>");
            	$('.info_date_nonrenseigne').fadeOut(20000);

            	$('a,img,hass').tooltip({ animation: true, html: true, placement: 'bottom', show: { effect: 'slideDown', } });
            }
        });
		
	}
	
	
	
	