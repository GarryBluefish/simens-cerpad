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
	                	    	 alert('impossible de supprimer il y a des analyses ayant deja des resultats '); return false;
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
    var nbDemandes;
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
					   	
			   "sAjaxSource": ""+tabUrl[0]+"public/infirmerie/liste-patient-ajax", 
			   
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
    $(".boutonTerminer").html('<button type="submit" id="terminer" style=" font-family: police2; font-size: 17px; font-weight: bold;"> Valider </button>');

    
    //raffraichirListeDemandeAdmission();
    }
    
    var deplierFormulaireAdmission = 0;
    function raffraichirListeDemandeAdmission() {
    	setTimeout(function(){
    		//alert(nbDemandes);
    		$.ajax({
    	        type: 'POST',
    	        url: tabUrl[0]+'public/infirmerie/get-nb-patient-admis',
    	        data: {'id':1},
    	        success: function(data) {    
    	        	var result = jQuery.parseJSON(data);  
    	        	//alert(result);
    	        	if(result > nbDemandes){
    	        		if(deplierFormulaireAdmission == 0){
    			        	$(location).attr("href",tabUrl[0]+"public/infirmerie/liste-patient");
    	        		}
    	        	}
    	        	raffraichirListeDemandeAdmission();
    	        }
    		});
    	},30000);
    }
    
    function clickRowHandler() 
    {
    	var id;
    	$('#patientAdmis tbody tr').contextmenu({
    		target: '#context-menu',
    		onItem: function (context, e) { 
    			
    			if($(e.target).text() == 'Visualiser' || $(e.target).is('#visualiserCTX')){
    				if(id){ listeAnalyses(id); }
    			} 
    			
    		}
    	
    	}).bind('mousedown', function (e) {
    			var aData = oTable.fnGetData( this );
    		    id = aData[7]; 
    	});
    	
    	
    	
    	$("#patientAdmis tbody tr").bind('dblclick', function (event) {
    		var aData = oTable.fnGetData( this );
    		var id = aData[7]; 
    		if(id){ listeAnalyses(id); }
    	});
    	
    	$('a,img,hass').tooltip({ animation: true, html: true, placement: 'bottom', show: { effect: 'slideDown', } });
    }
    
    var entreeValidation = 0;
    function listeAnalyses(idfacturation){
        var chemin = tabUrl[0]+'public/infirmerie/liste-analyses-facturees';
        $.ajax({
            type: 'POST',
            url: chemin ,
            data:{'idfacturation':idfacturation},
            success: function(data) {
       	    
            	$('#titre span').html('INFORMATIONS SUR L\'ADMISSION');
            	     var result = jQuery.parseJSON(data);  
            	     $("#contenu").fadeOut(function(){ $("#vue_patient").html(result); $("#interfaceListeFactures").fadeIn("fast"); }); 
            	     
            	     $('.boutonAnnuler').click(function(){
            	    	 
            	    	 $('#interfaceListeFactures').fadeOut(function(){
            	    		$('#titre span').html('LISTE DES PATIENTS ADMIS'); 
            	    		$('#contenu').fadeIn(300);
            	    	 });
            	    		 
            	     });
            	     
            	     if(entreeValidation == 0){
            	    	 entreeValidation = 1;
                         $('.boutonTerminer').click(function(){
                         
                        	 if($('#formEnregistrementBilan')[0].checkValidity() == true){
                        		 //formulaire valide et envoi des donn�es
                        		 $('.boutonTerminer button').attr('disabled', true);
                        		 $('#validerForm').trigger('click');
                        	 }else{
                        		 $('#validerForm').trigger('click');
                        	 }
                         });
            	     }
                     
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
    	$('#difficultes').val(0);
    	$('.reductTextarea textarea').val('Néant').attr({'readonly':true});
    	
    	$('#transfuser').val(0);
    	$('.reductSelect2 select').val('').attr({'disabled':true});
    	
    	
    	$('#date_heure').datetimepicker(
    		$.datepicker.regional['fr'] = {
    			dateFormat: 'dd/mm/yy -', 
    			timeText: 'H:M', 
    			hourText: 'Heure', 
    			minuteText: 'Minute', 
    			currentText: 'Actuellement', 
    			closeText: 'F',
    			showAnim : 'bounce',
    			maxDate : '0',
    		} 
    	);
    	
    }
    
    
    function nouvelleLigneGPPHPVO(idanalyse, id)
	{
    	var infoSuiv = "";
    	//if(idanalyse == 72){ infoSuiv = "de l'HPVO"; }else{ infoSuiv = "de la GPP"; }
    	
    	var ligne = ""+
		 '<div id="prelevementGPPHPVO_AutoAjoutLigne'+id+'"  class="prelevementGPPHPVO_AutoAjout">'+
	     '<div class="autre_prelev_GPP_HPVO">'+(id+1)+'<sup>&egrave;me</sup> pr&eacute;l&egrave;vement '+infoSuiv+
	     '</div>'+
	     '<div class="barre_separat_prelev_GPP_HPVO"></div>'+
	    
	     '<table id="form_patient" style="margin-left:17.5%; width: 80%; border-bottom: 1px solid #F1F1F1;">'+
	           '<tr style="vertical-align: top; background: re;">'+
	             '<td class="comment-form-patient " style="width: 20%; vertical-align:top; margin-right:0px;">'+
	               '<label>Date & Heure</label>'+
	               '<input type="text" style="width:90%;" id="dateHeureGPPHPVO'+id+'">'+
	             '</td>'+
	             '<td class="comment-form-patient " style="width: 12%; vertical-align:top; margin-right:0px;">'+
	               '<label>Intervalle </label>'+
	               '<input type="text" style="width:80%;" id="intervalleGPPHPVO'+id+'">'+
	             '</td>'+
                 '<td class="comment-form-patient " style="width: 45%; vertical-align:top; margin-right:0px;">'+
	               '<label>Difficult&eacute;s rencontr&eacute;es</label>'+
	               '<input type="text" style="width:95%;" value="Aucune" id="difficultesGPPHPVO'+id+'">'+
	             '</td>'+
                 '<td class="comment-form-patient " style="width: 23%; vertical-align:top; margin-right:0px;">'+
	               '<label>Origine</label>'+
	               '<input type="text" style="width:90%;" id="origineGPPHPVO'+id+'">'+
	             '</td>'+
	           '</tr>'+
	     '</table>'+
	    
	     '</div>';
    	
    	return ligne
	}
    
    
	function ajoutAutoLigneGPPHPVO(idanalyse)
	{
		var nbLigne = $('.prelevementGPPHPVO_AutoAjout').length;
		if( nbLigne+1 == 5 ){
			$("#autre_prelevGPPHPVO_plus").css({'visibility':'hidden'}); 
		}
		var nouvelleLigne = nouvelleLigneGPPHPVO(idanalyse, nbLigne+1);
		$("#prelevementGPPHPVO_AutoAjoutLigne"+nbLigne).after(nouvelleLigne);
		
		if( nbLigne+1 == 2 ){
			 $("#autre_prelevGPPHPVO_moins").css({'visibility':'visible'}); 
		}
	}
	
	
	function suppressionAutoLigneGPPHPVO(idanalyse)
	{
		var nbLigne = $('.prelevementGPPHPVO_AutoAjout').length;
		if(nbLigne-1 == 1){ $("#autre_prelevGPPHPVO_moins").css({'visibility':'hidden'}); }
		$("#prelevementGPPHPVO_AutoAjoutLigne"+nbLigne).remove();
		if(nbLigne-1 == 4){ $("#autre_prelevGPPHPVO_plus").css({'visibility':'visible'}); }
	}
	
	