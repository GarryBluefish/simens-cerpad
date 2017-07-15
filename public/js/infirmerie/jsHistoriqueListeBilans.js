    var base_url = window.location.toString();
	var tabUrl = base_url.split("public");
	//BOITE DE DIALOG POUR LA CONFIRMATION DE SUPPRESSION
    
    
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
					   	
			   //"sAjaxSource": ""+tabUrl[0]+"public/facturation/historique-liste-bilans-ajax", 
			   
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
    	
    }
    
    
    function listeAnalysesFacturees(idpersonne){ 
        var chemin = tabUrl[0]+'public/facturation/historique-liste-analyses-facturees';
        $.ajax({
            type: 'POST',
            url: chemin ,
            data:{'idpersonne':idpersonne},
            success: function(data) {
       	    
            	$('#titre span').html('HISTORIQUES DES FACTURATIONS DU PATIENT');
            	     var result = jQuery.parseJSON(data);  
            	     
            	     result+= "<script>"+
	                          "$('#listeDesFacturesHistoriquesTableau .listeDataTable').toggle(false);"+
	                          "</script>";
	        
	                 result+= "<style>"+
 	                          "#listeDesFacturesHistoriquesTableau div .dataTables_paginate{ margin-top: -15px; }"+
	                          "</style>";
            	     
            	     
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

	function afficherListeAnalysesDeLaFacture(idfacturation, numeroFacture) { 
		
    	$('#historiqueListeDesAnalysesDemandees').html('<table style="width: 100%; margin-top: 30px;" align: center;> <tr> <td style="margin-top: 20px; text-align: center; "> Chargement </td> </tr>  <tr> <td align="center"> <img style="margin-top: 20px; width: 50px; height: 50px;" src="../images/loading/Chargement_1.gif" /> </td> </tr> </table>');

		var chemin = tabUrl[0]+'public/facturation/historique-liste-analyses-de-la-facture';
        $.ajax({
            type: 'POST',
            url: chemin ,
            data:{'idfacturation':idfacturation, 'numeroFacture':numeroFacture},
            success: function(data) {
       	    
            	var result = jQuery.parseJSON(data);
            	
            	$('#historiqueListeDesAnalysesDemandees').html(result);
            	
            },
            error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
            dataType: "html"
        });
	}
	

	function listeDesFacturesHistoriques()
	{
		
	    var oTable2 = $('#listeDesFacturesHistoriques').dataTable
	    ( {
			"sPaginationType": "full_numbers",
			"aLengthMenu": [],
			"iDisplayLength": 10,
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

	    } );
	    
	}
	
	
	
	