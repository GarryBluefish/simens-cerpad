    var nb="_TOTAL_";
    var asInitVals = new Array();
    var base_url = window.location.toString();
	var tabUrl = base_url.split("public");
	//BOITE DE DIALOG POUR LA CONFIRMATION DE SUPPRESSION
    function confirmation(id){
	  $( "#confirmation" ).dialog({
	    resizable: false,
	    height:170,
	    width:485,
	    autoOpen: false,
	    modal: true,
	    buttons: {
	        "Oui": function() {
	            $( this ).dialog( "close" );
	            
	            var cle = id;
	            var chemin = tabUrl[0]+'public/facturation/supprimer';
	            $.ajax({
	                type: 'POST',
	                url: chemin ,
	                data: $(this).serialize(),  
	                data:'id='+cle,
	                success: function(data) {
	                	     var result = jQuery.parseJSON(data);  
	                	     nb = result;
	                	     $("#"+cle).parent().parent().fadeOut(function(){
		                	 	 $("#"+cle).empty();
		                	 });
	                },
	                error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
	                dataType: "html"
	            });
	        },
	        "Annuler": function() {
                $(this).dialog( "close" );
            }
	   }
	  });
    }
    
    function envoyer(id){
   	 confirmation(id);
     $("#confirmation").dialog('open');
   	}
    
   
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
  
    // LISTE DES PATIENTS POUR LES DEMANDES D'AUJOURD'HUI
    // LISTE DES PATIENTS POUR LES DEMANDES D'AUJOURD'HUI
    var oTableL1;
    function initialisationListe1(){
        	
        $( "#accordions" ).accordion();
        $( "button" ).button();
        	
        oTableL1 = $('#patientAujourdhui').dataTable
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

        	"sAjaxSource":  tabUrl[0]+"public/secretariat/liste-demandes-aujourdhui-ajax",
        	"fnDrawCallback": function() 
        	{
        		clickRowHandler1();
        	}
        	
        } );
        	
        var asInitValsPA = new Array();
    	
    	$("#patientAujourdhui tfoot input").keyup( function () { 
    		oTableL1.fnFilter( this.value, $("#patientAujourdhui tfoot input").index(this) );
    	} );
    	
    	/*
    	 * Support functions to provide a little bit of 'user friendlyness' to the textboxes in 
    	 * the footer
    	 */
    	$("#patientAujourdhui tfoot input").each( function (i) {
    		asInitValsPA[i] = this.value;
    	} );
    	
    	$("#patientAujourdhui tfoot input").focus( function () {
    		if ( this.className == "search_init" )
    		{
    			this.className = "";
    			this.value = "";
    		}
    	} );
    	
    	$("#patientAujourdhui tfoot input").blur( function (i) {
    		if ( this.value == "" )
    		{
    			this.className = "search_init";
    			this.value = asInitValsPA[$("#patientAujourdhui tfoot input").index(this)];
    		}
    	} );
    	
    	
        $(".boutonTerminer").html('<button type="submit" id="terminer" style=" font-family: police2; font-size: 17px; font-weight: bold;"> Terminer </button>');

        $('#afficherAujourdhui').css({'font-weight':'bold', 'font-size': '17px' });
        $('.patientTous').toggle(false);
    	
    	$('#afficherAujourdhui').click(function(){
    		$('.patientAujourdhui').toggle(true);
    		$('.patientTous').toggle(false);
    		$('#afficherAujourdhui').css({'font-weight':'bold', 'font-size': '17px' });
    		$('#afficherTous').css({'font-weight':'normal', 'font-size': '13px' });
    	});

    	$('#afficherTous').click(function(){
    		$('.patientTous').toggle(true);
    		$('.patientAujourdhui').toggle(false);
    		$('#afficherAujourdhui').css({'font-weight':'normal', 'font-size': '13px'});
    		$('#afficherTous').css({'font-weight':'bold', 'font-size': '17px' });
    	});
    }
    
    function clickRowHandler1() 
    {
     	var id2; 
     	$('#patientAujourdhui tbody tr').contextmenu({
     		target: '#context-menu',
     		onItem: function (context, e) {
  
     			if($(e.target).text() == 'DÃ©tails' || $(e.target).is('#visualiserCTX')){
     				visualiser(id2);
     			} else 
     				if($(e.target).text() == 'Liste demandes' || $(e.target).is('#modifierCTX')){
     					listeDemandes(id2);
     				}
     			
     		}
     	
     	}).bind('mousedown', function (e) {
     			var aData = oTableL1.fnGetData( this );
     		    id2 = aData[7];
     	});
     	
    	$('a,img,hass').tooltip({ animation: true, html: true, placement: 'bottom', show: { effect: 'slideDown', } });
    }
    
    function formatVirguleParMill(num) { 
    	return ("" + num).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, function($1) { return $1 + "," });
    }
    
    function effectuerRechercheAvancee(valInfosUrl){
    	
         $.ajax({
             type: 'POST',
             url: tabUrl[0]+'public/secretariat/effectuer-recherche-avancee-ajax?infosUrl='+valInfosUrl,
             data:'valInfosUrl='+valInfosUrl,
             success: function(data) {
             	     var result = jQuery.parseJSON(data);  
             	     $('#patientTous tbody').html(result);
             }
         });
         
    }
    
	// LISTE DE TOUS LES PATIENTS POUR TOUTES LES DEMANDES EFFECTUEES
	// LISTE DE TOUS LES PATIENTS POUR TOUTES LES DEMANDES EFFECTUEES
	var oTablePTous;
	var valInfosUrl = "";
	function initialisationListe2(){
	    	
	    $( "#accordions" ).accordion();
	    $( "button" ).button();
	    	
	    oTablePTous = $('#patientTous').dataTable
	    ( {
	    	"sPaginationType": "full_numbers",
	    	"aLengthMenu": [5,7,10,15],
	    	"aaSorting": [],
	    	"oLanguage": {
	    		"sInfo": "_START_ &agrave; _END_ sur _TOTAL_ patients  <div class='nbTotalPatientTous'></div>",
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
	
	    	"sAjaxSource":  tabUrl[0]+"public/secretariat/liste-demandes-tous-ajax?infosUrl="+valInfosUrl,
	    	"fnDrawCallback": function() 
	    	{
	    		clickRowHandler2();
	    	},
	    	"sServerMethod": "GET", 
	    	"fnServerData": function (sSource, aoData, fnCallback) {
	            $.getJSON(sSource, aoData, function (json) {
	            	//alert(json.iTotalDisplayRecords); 
	            	fnCallback(json); 
	            	$(".dataTables_info .nbTotalPatientTous").parent().after("<div style='display: inline; float: left;'><span style='font-style: italic;'>Eff. total : </span>"+formatVirguleParMill(json.iTotalDisplayRecords)+" patients</div>");
	            }); 
	        }, 
	    	
	    
	    } );
	    	
	    
	    var asInitValsPTous = new Array();
		
		$("#patientTous tfoot input").keyup( function () {
			
			oTablePTous.fnFilter( this.value, $("#patientTous tfoot input").index(this) );
			
			/** Effectuer une recherche avancée (i.e: Rechercher directement dans la base de données) **/
			if($('#patientTous tbody tr').length == 1){
				if($('#patientTous tbody tr td').length == 1){ // Ce qui signifie que le tableau est vide
					
					/*
					if(
						$(this).parent().attr('class') == 'num_dossier_l2' ||
						$(this).parent().attr('class') == 'nom_l2'
					){
						if (event.keyCode == 13) { 
							alert('oo'); 
							return false; 
						}
					}
					*/
					
					valInfosUrl = "";
					
					var num_dosier = $(".num_dossier_l2 input").val();
					if( num_dosier !== " Num. dossier"){
						valInfosUrl += "0!"+num_dosier+";";
					}
					
					var nom_l2 = $(".nom_l2 input").val();
					if( nom_l2 !== " Nom"){
						valInfosUrl += "1!"+nom_l2+";";
					}
					
					var prenom_l2 = $(".prenom_l2 input").val();
					if( prenom_l2 !== " PrÃ©nom"){
						valInfosUrl += "2!"+prenom_l2+";";
					}
					
					var adresse_l2 = $(".adresse_l2 input").val();
					if( adresse_l2  !== " Adresse"){
						valInfosUrl += "3!"+adresse_l2+";";
					}
					
					var date_l2 = $(".date_l2 input").val(); 
					if( date_l2  !== " Date naiss."){
						valInfosUrl += "4!"+date_l2+";";
					}
					
					$('#patientTous tbody tr td').html('Recherche avanc&eacute;e ...');
					effectuerRechercheAvancee(valInfosUrl);

				}
			}
			
			/** Obtenir le nombre de ligne du tableau **/
			//alert(oTablePTous.fnSettings().aData.length); return false;

		} );    
		
		
		
		
		
		/*
		 * Support functions to provide a little bit of 'user friendlyness' to the textboxes in 
		 * the footer
		 */
		$("#patientTous tfoot input").each( function (i) {
			asInitValsPTous[i] = this.value;
		} );
		
		$("#patientTous tfoot input").focus( function () {
			if ( this.className == "search_init" )
			{
				this.className = "";
				this.value = "";
			}
		} );
		
		$("#patientTous tfoot input").blur( function (i) {
			if ( this.value == "" )
			{
				this.className = "search_init";
				this.value = asInitValsPTous[$("#patientTous tfoot input").index(this)];
			}
		} );
		
	    $(".boutonTerminer").html('<button type="submit" id="terminer" style=" font-family: police2; font-size: 17px; font-weight: bold;"> Terminer </button>');
	    
	}
	    
	 
	function clickRowHandler2() 
	{
		var id;
		$('#patientTous tbody tr').contextmenu({
			target: '#context-menu',
			onItem: function (context, e) {
	    	
				if($(e.target).text() == 'DÃ©tails' || $(e.target).is('#visualiserCTX')){
					visualiser(id);
				} else 
					
					if($(e.target).text() == 'Liste demandes' || $(e.target).is('#modifierCTX')){
						listeDemandes(id);
					}
	    		
			}
	    	
		}).bind('mousedown', function (e) {
			var aData = oTablePTous.fnGetData( this );
			id = aData[7];
		});
	    
		$('a,img,hass').tooltip({ animation: true, html: true, placement: 'bottom', show: { effect: 'slideDown', } });
	}

    
    function visualiser(id){
    	var cle = id;
        var chemin = tabUrl[0]+'public/secretariat/infos-patient';
        $.ajax({
            type: 'POST',
            url: chemin ,
            data: $(this).serialize(),  
            data:'id='+cle,
            success: function(data) {
            	     var result = jQuery.parseJSON(data);  
            	     
            	     $('#vue_patient').html(result);
            	     $('#contenu').fadeOut(function(){
            	    	 $('#titre span').html('ETAT CIVIL DU PATIENT'); 
            	    	 $('#vue_patient').fadeIn();
            	     });
            	     
            	     $('#terminer').click(function(){
            	    	 $('#vue_patient').fadeOut(function(){
            	    		 $('#titre span').html('LISTE DES DEMANDES PAR PATIENT'); 
                	    	 $('#contenu').fadeIn();
                	    	 $('#vue_patient').html("");
                	     });
            	     });
            }
        
        });
    	
    }
    
    function listeDemandes(id){
    	
    	var cle = id;
        var chemin = tabUrl[0]+'public/secretariat/get-informations-patient';
        $.ajax({
            type: 'POST',
            url: chemin ,
            data:'id='+cle,
            success: function(data) {
            	     var result = jQuery.parseJSON(data);
            	     $('#info_patient').html(result[0]);
            	     $('#listeAnalysesParDemande').html(result[1]);
            	     $('.visualiser'+result[2]).html("<img style='padding-left: 3px; ' src='../images_icons/transfert_droite.png' />");
            	     
            	     $('#contenu').fadeOut(function(){
            	    	 $('#titre span').html('LISTE DES DEMANDES D\'ANALYSE'); 
            	    	 $('#liste_demandes').fadeIn(100);
            	     });
        	            	 
            	     $('.boutonTerminer').click(function(){
            	    	 $('#liste_demandes').fadeOut(function(){
            	    		 $('#titre span').html('LISTE DES DEMANDES PAR PATIENT'); 
            	    		 $('#contenu').fadeIn(300);
            	    	 });
            	    		 
            	     });
            }
        });
    	
    }
    
    
    function listeDemandesAnalyses()
    {
        var oTable2 = $('#listeDemandesFiltre').dataTable
        ( {
        	"bDestroy":true,
    		"sPaginationType": "full_numbers",
    		"aLengthMenu": [3,5],
    		"iDisplayLength": 3,
        	"aaSorting": [],
        	"oLanguage": {
        		"sInfo": "_START_ &agrave; _END_ sur _TOTAL_ ",
        		"sInfoEmpty": "0 &eacute;l&eacute;ment &agrave; afficher",
        		"sInfoFiltered": "",
        		"sUrl": "",
        		"oPaginate": {
        			"sFirst":    "|<",
        			"sPrevious": "<",
        			"sNext":     ">",
        			"sLast":     ">|",
        		},
        		
        	},

        } );
        
        var asInitVals = new Array();
    	
    	//le filtre du select
    	$('#filter_statut').change(function() 
    	{					
    		oTable2.fnFilter( this.value );
    	});
    	
    	$(".foot_style_demande input").keyup( function () {
    		/* Filter on the column (the index) of this element */
    		oTable2.fnFilter( this.value, $(".foot_style_demande input").index(this) );
    	} );
    	
    	/*
    	 * Support functions to provide a little bit of 'user friendlyness' to the textboxes in 
    	 * the footer
    	 */
    	$(".foot_style_demande input").each( function (i) {
    		asInitVals[i] = this.value;
    	} );
    	
    	$(".foot_style_demande input").focus( function () {
    		if ( this.className == "search_init" )
    		{
    			this.className = "";
    			this.value = "";
    		}
    	} );
    	
    	$(".foot_style_demande input").blur( function (i) {
    		if ( this.value == "" )
    		{
    			this.className = "search_init";
    			this.value = asInitVals[$(".foot_style_demande input").index(this)];
    		}
    	} );
    	
    }
    
    
    function listeAnalysesDemandes()
    {
        var oTable2 = $('#listeAnalyseFiltre').dataTable
        ( {
        	"bDestroy":true,
    		"sPaginationType": "full_numbers",
    		"aLengthMenu": [3,5],
    		"iDisplayLength": 3,
        	"aaSorting": [],
        	"oLanguage": {
        		"sInfo": "_START_ &agrave; _END_ sur _TOTAL_ analyses",
        		"sInfoEmpty": "0 &eacute;l&eacute;ment &agrave; afficher",
        		"sInfoFiltered": "",
        		"sUrl": "",
        		"oPaginate": {
        			"sFirst":    "|<",
        			"sPrevious": "<",
        			"sNext":     ">",
        			"sLast":     ">|",
        		},
        		
        	},
        	
        	"fnDrawCallback": function() 
        	{
        		clickRowHandlerListeAnalysesDemandes();
        	}

        } );
        
        var asInitVals = new Array();
    	
    	//le filtre du select
    	$('#filter_statut').change(function() 
    	{					
    		oTable2.fnFilter( this.value );
    	});
    	
    	$(".foot_style_analyse input").keyup( function () {
    		/* Filter on the column (the index) of this element */
    		oTable2.fnFilter( this.value, $(".foot_style_analyse input").index(this) );
    	} );
    	
    	/*
    	 * Support functions to provide a little bit of 'user friendlyness' to the textboxes in 
    	 * the footer
    	 */
    	$(".foot_style_analyse input").each( function (i) {
    		asInitVals[i] = this.value;
    	} );
    	
    	$(".foot_style_analyse input").focus( function () {
    		if ( this.className == "search_init" )
    		{
    			this.className = "";
    			this.value = "";
    		}
    	} );
    	
    	$(".foot_style_analyse input").blur( function (i) {
    		if ( this.value == "" )
    		{
    			this.className = "search_init";
    			this.value = asInitVals[$(".foot_style_analyse input").index(this)];
    		}
    	} );
    	
    }
    
    
    function clickRowHandlerListeAnalysesDemandes(){
    	$('a,img,hass').tooltip({ animation: true, html: true, placement: 'bottom', show: { effect: 'slideDown', } });
    }
    
    
    function vueListeAnalyses(iddemande){
    	var chemin = tabUrl[0]+'public/secretariat/get-liste-analyses-demandees';
        $.ajax({
            type: 'POST',
            url: chemin ,
            data:'iddemande='+iddemande,
            success: function(data) {
            	     var result = jQuery.parseJSON(data);
            	     //Ici on modifie les icones 
            	     $('.iconeListeAffichee').html("<img style='padding-left: 3px; cursor: pointer;' src='../images_icons/transfert_droite2.png' />");
            	     $('.visualiser'+iddemande).html("<img style='padding-left: 3px; ' src='../images_icons/transfert_droite.png' />");

            	     $('#liste_analyses_demandes').html(result);
            	     listeAnalysesDemandes();
            }
        });
    }
    
    function popupFermer() {
    	$(null).w2overlay(null);
    }
    
    function diagnostic(id){
    	
    	$('#diagnostic_'+id).w2overlay({ html: "" +
    		"" +
    		"<div style='border-bottom:1px solid green; height: 30px; background: #f9f9f9; width: 300px; text-align:center; padding-top: 10px; font-size: 13px; color: green; font-weight: bold;'><img style='padding-right: 10px;' src='"+tabUrl[0]+"public/images_icons/detailsd.png' >Diagnostic</div>" +
    		"<div style='height: 195px; width: 300px; padding-top:10px; text-align:center;'>" +
    		"<textarea style='height: 77%; width: 95%; max-height: 77%; max-width: 95%;' id='diagnostic_demande' > </textarea>" +
    		"<button class='btn' style='cursor:pointer; margin-top: 7px;' onclick='popupFermer(); return false;'>Fermer</button>" +
    		"</div>" +
    		"<script> $('#diagnostic_demande').val($('#diagnostic_demande_text').val()).attr({'readonly': true}).css({'background':'#fefefe'}); </script>" 
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
    
    
    function annulerAnalyseDemandee(iddemande1, iddemande2, idpatient){
    	
    	$('#AnalyseDemandeePopupCofirmAnnulation_'+iddemande2).w2overlay({ html: "" +
    		"" +
    		"<div style='border-bottom:1px solid green; height: 30px; background: #f9f9f9; width: 180px; text-align:center; padding-top: 10px; font-size: 13px; color: green; font-weight: bold;'>Confirmer l'annulation</div>" +
  			"<div style='height: 50px; width: 180px; padding-top:10px; text-align:center;'>" +
  			"<button class='btn' style='cursor:pointer;' onclick='popupFermer(); return false;'>Non</button>" +
  			"<button class='btn' style='cursor:pointer;' onclick='annulationAnalyseDemandee("+iddemande1+","+iddemande2+","+idpatient+"); return false;'>Oui</button>" +
  			"</div>"
    	});

    }
    
    
    function annulationAnalyseDemandee(iddemande1, iddemande2, idpatient){
    	
    	if(iddemande1 != iddemande2){
    		$('#AnalyseLigneDemande_'+iddemande2).fadeOut();
            $.ajax({
                type: 'POST',
                url: tabUrl[0]+'public/secretariat/annuler-analyse-demandee' ,
                data:'iddemande='+iddemande2,
                success: function(data) {
                	$('.visualiser'+iddemande1+' img').trigger('click');
                }
            });
            
    	}else{
    		$('#AnalyseLigneDemande_'+iddemande2).fadeOut();
    		$.ajax({
                type: 'POST',
                url: tabUrl[0]+'public/secretariat/annuler-analyse-demandee' ,
                data:'iddemande='+iddemande2,
                success: function(data) {
            		listeDemandes(idpatient);
                }
            });
    	}
    }
    