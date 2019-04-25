    var nb="_TOTAL_";
    var asInitVals = new Array();
    var base_url = window.location.toString();
	var tabUrl = base_url.split("public");
   
	
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
	
	
    var oTable;
    function initialisation(){
    	 $( "#accordions" ).accordion();
    	 $( "button" ).button();
    	    
   
    	 oTable = $('#patient').dataTable
    	 ( {
    	
    		 "sPaginationType": "full_numbers",
    		 "aLengthMenu": [5,7,10,15],
    		 "aaSorting": [],
    		 "oLanguage": {
    			 "sInfo": "_START_ &agrave; _END_ sur _TOTAL_ patients",
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

    		 "sAjaxSource":  tabUrl[0]+"public/technicien/liste-bilans-tries-resultats-ajax",
    		 "fnDrawCallback": function() 
    	
    		 {
    			 clickRowHandler();
    		 }
    	 } );
    	
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

    $(".boutonTerminer").html('<button type="submit" id="terminer" style=" font-family: police2; font-size: 17px; font-weight: bold;"> Terminer </button>');

   }
    

 
   function clickRowHandler() 
   {
    	var id;
    	$('#patient tbody tr').contextmenu({
    		target: '#context-menu',
    		onItem: function (context, e) {
    			
    			if($(e.target).text() == 'Visualiser' || $(e.target).is('#visualiserCTX')){
    				visualiser(id);
    			} else 
    				if($(e.target).text() == 'Liste demandes' || $(e.target).is('#listeDemandesCTX')){
    					if(id){ listeAnalysesTriees(id); }
    				}
    			
    		}
    	
    	}).bind('mousedown', function (e) {
    			var aData = oTable.fnGetData( this );
    		    id = aData[8];
    	});
    	
    	
    	
    	$("#patient tbody tr").bind('dblclick', function (event) {
    		var aData = oTable.fnGetData( this );
    		var id = aData[7];
    		visualiser(id);
    	});
    	
    	$('a,img,hass').tooltip({ animation: true, html: true, placement: 'bottom', show: { effect: 'slideDown', } });
    }
    
    function visualiser(id){
    	var cle = id;
        var chemin = tabUrl[0]+'public/technicien/infos-patient';
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
            	    		 $('#titre span').html('LISTE DES DEMANDES PAR PATIENTS'); 
                	    	 $('#contenu').fadeIn();
                	    	 $('#vue_patient').html("");
                	     });
            	     });
            }
        
        });
    	
    }
    
    function listeAnalysesTriees(id){
    	
        var chemin = tabUrl[0]+'public/technicien/get-informations-patient';
        $.ajax({
            type: 'POST',
            url: chemin ,
            data:'id='+id,
            success: function(data) {
            	     var result = jQuery.parseJSON(data); 
            	     $('#info_patient').html(result[0]);
            	     $('#listeAnalysesParDemande').html(result[1]);
            	     $('.visualiser'+result[2]).html("<img style='padding-left: 3px; ' src='../images_icons/transfert_droite.png' />");
            	     $('.dateAffichee_'+result[2]).css({'color' : 'green'});
            	     
            	     $('#contenu').fadeOut(function(){
            	    	 $('#titre span').html('LISTE DES ANALYSES DEMANDEES'); 
            	    	 $('#liste_demandes').fadeIn(100);
            	     });
        	            	 
            	     $('.boutonTerminer').click(function(){
            	    	 $('#liste_demandes').fadeOut(function(){
            	    		 $('#titre span').html('LISTE DES PATIENTS'); 
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
    		"aLengthMenu": [5,10],
    		"iDisplayLength": 5,
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
    
    function vueListeAnalyses(iddemande){
    	var chemin = tabUrl[0]+'public/technicien/get-liste-analyses-demandees';
        $.ajax({
            type: 'POST',
            url: chemin ,
            data:'iddemande='+iddemande,
            success: function(data) {
            	     var result = jQuery.parseJSON(data);
            	     //Ici on modifie les icones 
            	     $('.iconeListeAffichee').html("<img style='padding-left: 3px; cursor: pointer;' src='../images_icons/transfert_droite2.png' />");
            	     $('.visualiser'+iddemande).html("<img style='padding-left: 3px; ' src='../images_icons/transfert_droite.png' />");
            	     $('.dateAffichee').css({'color' : 'black'});
            	     $('.dateAffichee_'+iddemande).css({'color' : 'green'});
            	     
            	     
            	     $('#liste_analyses_demandes').html(result);
            	     listeAnalysesDemandes();
            }
        });
    }
    
    function gestionFormuleLeucocytaire(){
    	
    	//Polynucléaires neutrophiles
    	//Polynucléaires neutrophiles
    	$("#champ1, #champ7").keyup( function () {
    		var champ1 = $("#champ1").val();
    		var champ7 = $("#champ7").val();
    		if( champ1 && champ7 ){
    			var resultatChamp2 = (champ1*champ7)/100;
    			$("#champ2").val(resultatChamp2);
    		}
    		else { $("#champ2").val(null); }
    		
    	} ).change( function () {
    		var champ1 = $("#champ1").val();
    		var champ7 = $("#champ7").val();
    		if( champ1 && champ7 ){
    			var resultatChamp2 = (champ1*champ7)/100;
    			$("#champ2").val(resultatChamp2);
    		}
    		else { $("#champ2").val(null); }
    	} );
    	
    	//Polynucléaires eosinophiles
    	//Polynucléaires eosinophiles
    	$("#champ1, #champ8").keyup( function () {
    		var champ1 = $("#champ1").val();
    		var champ8 = $("#champ8").val();
    		if( champ1 && champ8 ){
    			var resultatChamp3 = (champ1*champ8)/100;
    			$("#champ3").val(resultatChamp3);
    		}
    		else { $("#champ3").val(null); }
    		
    	} ).change( function () {
    		var champ1 = $("#champ1").val();
    		var champ8 = $("#champ8").val();
    		if( champ1 && champ8 ){
    			var resultatChamp3 = (champ1*champ8)/100;
    			$("#champ3").val(resultatChamp3);
    		}
    		else { $("#champ3").val(null); }
    	} );
    	
    	//Polynucléaires basophiles
    	//Polynucléaires basophiles
    	$("#champ1, #champ9").keyup( function () {
    		var champ1 = $("#champ1").val();
    		var champ9 = $("#champ9").val();
    		if( champ1 && champ9 ){
    			var resultatChamp4 = (champ1*champ9)/100;
    			$("#champ4").val(resultatChamp4);
    		}
    		else { $("#champ4").val(null); }
    		
    	} ).change( function () {
    		var champ1 = $("#champ1").val();
    		var champ9 = $("#champ9").val();
    		if( champ1 && champ9 ){
    			var resultatChamp4 = (champ1*champ9)/100;
    			$("#champ4").val(resultatChamp4);
    		}
    		else { $("#champ4").val(null); }
    		
    	} );
    	
    	//Lymphocytes Lymphocytes
    	//Lymphocytes Lymphocytes
    	$("#champ1, #champ10").keyup( function () {
    		var champ1 = $("#champ1").val();
    		var champ10 = $("#champ10").val();
    		if( champ1 && champ10 ){
    			var resultatChamp5 = (champ1*champ10)/100;
    			$("#champ5").val(resultatChamp5);
    		}
    		else { $("#champ5").val(null); }
    		
    	} ).change( function () {
    		var champ1 = $("#champ1").val();
    		var champ10 = $("#champ10").val();
    		if( champ1 && champ10 ){
    			var resultatChamp5 = (champ1*champ10)/100;
    			$("#champ5").val(resultatChamp5);
    		}
    		else { $("#champ5").val(null); }
    	} );
    	
    	//Monocytes Monocytes
    	//Monocytes Monocytes
    	$("#champ1, #champ11").keyup( function () {
    		var champ1 = $("#champ1").val();
    		var champ11 = $("#champ11").val();
    		if( champ1 && champ11 ){
    			var resultatChamp6 = (champ1*champ11)/100;
    			$("#champ6").val(resultatChamp6);
    		}
    		else { $("#champ6").val(null); }
    		
    	} ).change( function () {
    		var champ1 = $("#champ1").val();
    		var champ11 = $("#champ11").val();
    		if( champ1 && champ11 ){
    			var resultatChamp6 = (champ1*champ11)/100;
    			$("#champ6").val(resultatChamp6);
    		}
    		else { $("#champ6").val(null); }
    	} );
    	
    	
    	//Taux de r�ticulocytes -- Taux de r�ticulocytes
    	//Taux de r�ticulocytes -- Taux de r�ticulocytes
    	
    	$("#champ12, #champ25").keyup( function () {
    		var champ12 = $("#champ12").val();
    		var champ25 = $("#champ25").val();
    		if( champ12 && champ25 ){
    			var resultatChamp24 = champ12*10000*champ25;
    			$("#champ24").val(resultatChamp24);
    		}
    		else { $("#champ24").val(null); }
    		
    	} ).change( function () {
    		var champ12 = $("#champ12").val();
    		var champ25 = $("#champ25").val();
    		if( champ12 && champ25 ){
    			var resultatChamp24 = champ12*10000*champ25;
    			$("#champ24").val(resultatChamp24);
    		}
    		else { $("#champ24").val(null); }
    	} );
    	
    }
    
    function getChampsNfs(){
    	var tab = new Array();
    	var i;
    	for(i = 1 ; i <= 23 ; i++){
    		if($('#champ'+i).val()){ tab[i] = $('#champ'+i).val(); }
    		else { tab[i] = null; }
    	}
    	tab[i] = $('#type_materiel_nfs').val(); 
    	tab[i+1] = $('#commentaire_hemogramme').val();
    	
    	return tab;
    }
    
    
    function getTypageHemoglobine(){
    	var tab = [];
    	tab[1] = $('#type_materiel_typage_hemoglobine').val();
    	tab[2] = $('#typage_hemoglobine').val();
    	tab[3] = $('#autre_typage_hemoglobine').val();
    	
    	return tab;
    }
    
    function getElectroHemo(){
    	$('#electro_hemo_moins').toggle(false);
	    
    	$('#electro_hemo_plus').click(function(){
	    	var nbLigne = $("#electro_hemo tr").length;
	    	$('#electro_hemo_moins').toggle(true);
	    	
	    	if(nbLigne < 10){
	    		var html ="<tr id='electro_hemo_"+nbLigne+"' class='ligneAnanlyse' style='width: 100%;'>"+
                            "<td style='width: 45%;'><label class='lab1'><span style='font-weight: bold; '>  <input id='electro_hemo_label_"+nbLigne+"' type='text' style='font-weight: bold; padding-right: 5px; margin-right: 30px;' maxlength=4 onkeydown='if(event.keyCode==32) return false;'> </span></label></td>"+
                            "<td style='width: 35%;'><label class='lab2' style='padding-top: 5px;'> <input id='electro_hemo_valeur_"+nbLigne+"' type='number' step='any'> % </label></td>"+
                            "<td style='width: 20%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> </label></td>"+
                          "</tr>";

		    	$('#electro_hemo_'+(nbLigne-1)).after(html);
		    	
		    	if(nbLigne == 9){
		    		$('#electro_hemo_plus').toggle(false);
		    	}
	    	}

	    });
	    
	    $('#electro_hemo_moins').click(function(){ 
	    	var nbLigne = $("#electro_hemo tr").length;
	    	
	    	if(nbLigne > 2){
		    	$('#electro_hemo_'+(nbLigne-1)).remove();
		    	if(nbLigne == 3){ 
		    		$('#electro_hemo_moins').toggle(false);
		    	}
		    	
		    	if(nbLigne == 10){
		    		$('#electro_hemo_plus').toggle(true);
		    	}
	    	}

	    });
	    
	    
    }

    function getElectrophoreseHemoglobine(){
    	var tab = [];
    	var nbLigne = $("#electro_hemo tr").length;
    	var j = 1;
    	
    	tab[0] = $('#type_materiel_electro_hemo').val();
    	tab[1] = new Array(); 
    	tab[2] = new Array(); 
    	for(var i=1 ; i<nbLigne ; i++){
    		var label  = $('#electro_hemo_label_'+i ).val();
    		var valeur = $('#electro_hemo_valeur_'+i).val();
    		if(label && valeur){
        		tab[1][j]   = label;
        		tab[2][j++] = valeur;
    		}
    	}
	    tab[3] = $('#conclusion_electro_hemo_valeur').val();
	    
	    return tab;
    }
    
    function getFerSeriqueFormule(){ 
    	var fer_serique_ug = $('#fer_serique_ug').val();
    	var valeur_umol = null;
    	
    	$('#fer_serique_ug').keyup( function () { 
    		fer_serique_ug = $('#fer_serique_ug').val(); 
    		if(fer_serique_ug){
        		valeur_umol = fer_serique_ug * 0.1791;
        		$('#fer_serique_umol').val(valeur_umol.toFixed(1));
        	}else{
        		$('#fer_serique_umol').val(null);
        	}
    	}).change( function(){
    		fer_serique_ug = $('#fer_serique_ug').val();
    		if(fer_serique_ug){
        		valeur_umol = fer_serique_ug * 0.1791;
        		$('#fer_serique_umol').val(valeur_umol.toFixed(1));
        	}else{
        		$('#fer_serique_umol').val(null);
        	}
    	});
    	
    }
    
    function getAzotemieFormule(){ 
    	var uree_sanguine = $('#uree_sanguine').val();
    	var valeur_mmol = null;
    	
    	$('#uree_sanguine').keyup( function () { 
    		uree_sanguine = $('#uree_sanguine').val(); 
    		if(uree_sanguine){
        		valeur_mmol = uree_sanguine * 16.65;
        		$('#uree_sanguine_mmol').val(valeur_mmol.toFixed(2));
        	}else{
        		$('#uree_sanguine_mmol').val(null);
        	}
    	}).change( function(){
    		uree_sanguine = $('#uree_sanguine').val(); 
    		if(uree_sanguine){
        		valeur_mmol = uree_sanguine * 16.65;
        		$('#uree_sanguine_mmol').val(valeur_mmol.toFixed(2));
        	}else{
        		$('#uree_sanguine_mmol').val(null);
        	}
    	});
    	
    }
    
    function getAcideUriqueFormule(){ 
    	var acide_urique = $('#acide_urique').val();
    	var valeur_umol = null;
    	
    	$('#acide_urique').keyup( function () { 
    		acide_urique = $('#acide_urique').val(); 
    		if(acide_urique){
        		valeur_umol = acide_urique * 5.949;
        		$('#acide_urique_umol').val(valeur_umol.toFixed(0));
        	}else{
        		$('#acide_urique_umol').val(null);
        	}
    	}).change( function(){
    		acide_urique = $('#acide_urique').val(); 
    		if(acide_urique){
        		valeur_umol = acide_urique * 5.949;
        		$('#acide_urique_umol').val(valeur_umol.toFixed(0));
        	}else{
        		$('#acide_urique_umol').val(null);
        	}
    	});
    	
    }
    
    
    function getCalcemieFormule(){ 
    	var calcemie = $('#calcemie').val();
    	var valeur_mmol = null;
    	
    	$('#calcemie').keyup( function () { 
    		calcemie = $('#calcemie').val(); 
    		if(calcemie){
        		valeur_mmol = calcemie * 0.0249;
        		$('#calcemie_mmol').val(valeur_mmol.toFixed(2));
        	}else{
        		$('#calcemie_mmol').val(null);
        	}
    	}).change( function(){
    		calcemie = $('#calcemie').val(); 
    		if(calcemie){
        		valeur_mmol = calcemie * 0.0249;
        		$('#calcemie_mmol').val(valeur_mmol.toFixed(2));
        	}else{
        		$('#calcemie_mmol').val(null);
        	}
    	});
    	
    }
    
    
    function getPhosphoremieFormule(){ 
    	var phosphoremie = $('#phosphoremie').val();
    	var valeur_mmol = null;
    	
    	$('#phosphoremie').keyup( function () { 
    		phosphoremie = $('#phosphoremie').val(); 
    		if(phosphoremie){
        		valeur_mmol = phosphoremie * 0.323;
        		$('#phosphoremie_mmol').val(valeur_mmol.toFixed(2));
        	}else{
        		$('#phosphoremie_mmol').val(null);
        	}
    	}).change( function(){
    		phosphoremie = $('#phosphoremie').val(); 
    		if(phosphoremie){
        		valeur_mmol = phosphoremie * 0.323;
        		$('#phosphoremie_mmol').val(valeur_mmol.toFixed(2));
        	}else{
        		$('#phosphoremie_mmol').val(null);
        	}
    	});
    	
    }
    
    
    function getAlbuminemieFormule(){ 
    	var albuminemie = $('#albuminemie').val();
    	var valeur_umol = null;
    	
    	$('#albuminemie').keyup( function () { 
    		albuminemie = $('#albuminemie').val(); 
    		if(albuminemie){
        		valeur_umol = albuminemie * 14.493;
        		$('#albuminemie_umol').val(valeur_umol.toFixed(2));
        	}else{
        		$('#albuminemie_umol').val(null);
        	}
    	}).change( function(){
    		albuminemie = $('#albuminemie').val(); 
    		if(albuminemie){
        		valeur_umol = albuminemie * 14.493;
        		$('#albuminemie_umol').val(valeur_umol.toFixed(2));
        	}else{
        		$('#albuminemie_umol').val(null);
        	}
    	});
    	
    }
    
    function getProteinurieFormule(){ 
    	var proteinurie_1 = $('#proteinurie_1').val();
    	var valeur_g24h = null;
    	
    	$('#proteinurie_1, #proteinurie_2').keyup( function () { 
    		proteinurie_1 = $('#proteinurie_1').val(); 
    		proteinurie_2 = $('#proteinurie_2').val(); 
    		valeur_g24h = proteinurie_1*proteinurie_2;
    		
    		if(valeur_g24h){
        		$('#proteinurie_g24h').val(valeur_g24h.toFixed(2));
        	}else{
        		$('#proteinurie_g24h').val(null);
        	}
    	}).change( function(){
    		proteinurie_1 = $('#proteinurie_1').val(); 
    		proteinurie_2 = $('#proteinurie_2').val(); 
    		valeur_g24h = proteinurie_1*proteinurie_2;
    		
    		if(valeur_g24h){
        		$('#proteinurie_g24h').val(valeur_g24h.toFixed(2));
        	}else{
        		$('#proteinurie_g24h').val(null);
        	}
    	});
    	
    }
    
    
    // GESTION DE L'ANALYSE Culot_urinaire 
    // GESTION DE L'ANALYSE Culot_urinaire 
    // GESTION DE L'ANALYSE Culot_urinaire 
    
    var tabInfosCulotUrinaire = new Array();
    tabInfosCulotUrinaire[0] = "";
    tabInfosCulotUrinaire[1] = '<input type="text" name="culot_urinaire_val_1" id="culot_urinaire_val_1" style="width:95%; text-align: left; padding-left: 3px;">';
    tabInfosCulotUrinaire[2] = '<input type="text" name="culot_urinaire_val_2" id="culot_urinaire_val_2" style="width:95%; text-align: left; padding-left: 3px;">';
    tabInfosCulotUrinaire[3] = '<select name="culot_urinaire_val_3" id="culot_urinaire_val_3" style="width: 95%;"> ' +
    		                   "  <option></option> " +
    		                   "  <option value=1>Oxalate de potassium | calcium</option> " +
    		                   "  <option value=2>Phosphate</option> " +
    		                   "  <option value=3>Cystine</option> " +
    		                   "  <option value=4>Acide Urique</option> " +
    		                   "</select>"; 
    
    tabInfosCulotUrinaire[4] = '<select name="culot_urinaire_val_4" id="culot_urinaire_val_4" style="width: 95%;"> ' +
                               "  <option></option> " +
                               "  <option value=1>Schistoma hematobium</option> " +
                               "</select>"; 
    
    tabInfosCulotUrinaire[5] = '<select name="culot_urinaire_val_5" id="culot_urinaire_val_5" style="width: 95%;"> ' +
                               "  <option></option> " +
                               "  <option value=1>Trichomonas vaginale</option> " +
                               "  <option value=2>Schistosoma hematobium</option> " +
                               "</select>"; 
    
    function listeElemtsCulotUrinaireSelect(indice, val){
    	$('#culot_urinaire_ligne_'+indice+' .emplaceListeElemtsCUSelect').html(tabInfosCulotUrinaire[val]);
    }
    
    function ajoutCulotUrinaireAuto(){
    	
    	$('#culot_urinaire_plus').click(function(){
	    	var nbLigne = $("#culot_urinaire_tableau tr").length;
	    	$('#culot_urinaire_moins').toggle(true);
	    	
	    	if(nbLigne < 10){
	    		var html ="<tr id='culot_urinaire_ligne_"+nbLigne+"' class='ligneAnanlyse' style='width: 100%;'>"+
	    		          "  <td style='width: 40%;'><label class='lab1 listeSelect'><span style='font-weight: bold; '> <select onchange='listeElemtsCulotUrinaireSelect("+nbLigne+",this.value);' name='culot_urinaire_select' id='culot_urinaire_select' > <option value=0>  </option> <option value='1' >Leucocytes</option> <option value='2' >H&eacute;maties</option> <option value='3' >Cristaux</option> <option value='4' >Oeufs</option> <option value='5' >Parasites</option> </select> </span></label></td>"+
	    	              "  <td style='width: 40%;'><label class='lab2 emplaceListeElemtsCUSelect' style='padding-top: 5px;'>  </label></td>"+
	    	              "  <td style='width: 20%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> </label></td>"+
                          "</tr>";

		    	$('#culot_urinaire_ligne_'+(nbLigne-1)).after(html);
		    	
		    	if(nbLigne == 9){
		    		$('#culot_urinaire_plus').toggle(false);
		    	}
	    	}

	    });
    	
    	$('#culot_urinaire_moins').click(function(){ 
	    	var nbLigne = $("#culot_urinaire_tableau tr").length;
	    	
	    	if(nbLigne > 2){
		    	$('#culot_urinaire_ligne_'+(nbLigne-1)).remove();
		    	if(nbLigne == 3){ 
		    		$('#culot_urinaire_moins').toggle(false);
		    	}
		    	
		    	if(nbLigne == 10){
		    		$('#culot_urinaire_plus').toggle(true);
		    	}
	    	}

	    });
    	
    }
    
    function getCulotUrinaireListeSelect(){
    	var tab = [];
    	var nbLigne = $("#culot_urinaire_tableau tr").length;
    	var j = 1;
    	
    	tab[0] = $('#type_materiel_culot_urinaire').val();
    	tab[1] = new Array(); 
    	tab[2] = new Array(); 
    	for(var i=1 ; i<nbLigne ; i++){
    		var listeSelect1  = $('#culot_urinaire_ligne_'+i+' .listeSelect select' ).val();
    		var listeSelect2 = $('#culot_urinaire_ligne_'+i+' .emplaceListeElemtsCUSelect select').val();
    		tab[1][j]   = listeSelect1;
    		if(listeSelect2){ 
    			tab[2][j++] = listeSelect2;
    		}else{
    			tab[2][j++] = $('#culot_urinaire_ligne_'+i+' .emplaceListeElemtsCUSelect input').val();;
    		}
    	}
	    tab[3] = $('#conclusion_culot_urinaire_valeur').val();
	    
	    return tab;
    }
    // FIN DE GESTION DE L'ANALYSE Culot_urinaire 
    // FIN DE GESTION DE L'ANALYSE Culot_urinaire 
    // FIN DE GESTION DE L'ANALYSE Culot_urinaire 
    
    
    function testCombsDirect(){
    	var tab = [];
    	tab[1] = $('#test_combs_direct').val(); 
		tab[2] = $('#titre_combs_direct').val();
		tab[3] = $('#type_materiel_test_combs_direct').val();
    	
    	return tab;
    }
    
    function getTestCombsDirect(val, iddemande){ 
    	if(val == 'Positif'){
    		$('.ER_'+iddemande+' .titre_combs_direct').toggle(true);
    	}else{
    		$('.ER_'+iddemande+' .titre_combs_direct').toggle(false).val(null);
    	}
    }
    
    function testCompatibilite(){
    	var tab = [];
    	tab[1] = $('#test_compatibilite').val(); 
		tab[2] = $('#titre_test_compatibilite').val();
		tab[3] = $('#type_materiel_test_compatibilite').val();
    	
    	return tab;
    }
    
    function getTestCompatibilite(val, iddemande){
    	if(val == 'Compatible'){
    		$('.ER_'+iddemande+' .titre_test_compatibilite').toggle(true);
    	}else{
    		$('.ER_'+iddemande+' .titre_test_compatibilite').toggle(false).val(null);
    	}
    }
    
    function groupageRhesus(){
    	var tab = [];
    	tab[1] = $('#groupe').val(); 
		tab[2] = $('#rhesus').val();
		tab[3] = $('#type_materiel_gsrh_groupage').val();
    	
    	return tab;
    }
    
    function antigeneDFaible(){
    	var tab = [];
    	
    	tab[1] = $('#antigene_d_faible').val(); 
		tab[2] = $('#type_materiel_recherche_antigene').val();
		tab[3] = $('#conclusion_antigene_d_faible').val();
    	
    	return tab;
    }
    
    function vitesseSedimentation(){
    	var tab = [];
    	tab[1] = $('#vitesse_sedimentation').val(); 
		tab[2] = $('#type_materiel_vitesse_sedimentation').val();
		tab[3] = $('#vitesse_sedimentation_2').val(); 
		
    	return tab;
    }
    
    function testDemmel(){
    	var tab = [];
    	tab[1] = $('#test_demmel').val(); 
		tab[2] = $('#type_materiel_test_demmel').val();
    	
    	return tab;
    }
    
    function azotemie(){
    	var tab = [];
    	tab[1] = $('#uree_sanguine').val(); 
		tab[2] = $('#type_materiel_azotemie').val();
		tab[3] = $('#uree_sanguine_mmol').val();
		
    	return tab;
    }
    
    function getDDimeres(){
    	var tab = [];
    	tab[1] = $('#d_dimeres').val();
    	tab[2] = $('#type_materiel_dimeres').val();
    	
    	return tab;
    }
    
    //Resultats d'une seule analyse
    //Resultats d'une seule analyse
    //Resultats d'une seule analyse
    function resultatsAnalyses(idanalyse, iddemande){
    	var tab = [];
    	$( "#resultatsAnalyses" ).dialog({
    		resizable: false,
    		height:670,
    		width:730,
    		autoOpen: false,
    		modal: true,
    		buttons: {
    			"Enregistrer": function() {
    				     if(idanalyse ==  1) { tab    = getChampsNfs(); }
    				else if(idanalyse ==  2) { tab    = groupageRhesus(); }
    				else if(idanalyse ==  3) { tab    = antigeneDFaible(); }
    				else if(idanalyse ==  4) { tab    = testCombsDirect(); }
    				else if(idanalyse ==  5) { tab    = testCombsIndirect(); }
       				else if(idanalyse ==  6) { tab    = testCompatibilite(); }
       				else if(idanalyse ==  7) { tab    = vitesseSedimentation(); }
    				else if(idanalyse ==  8) { tab    = testDemmel(); }
    				else if(idanalyse ==  9) { tab[1] = $('#taux_reticulocyte').val(); tab[2] = $('#type_materiel_taux_reticulocytes').val(); } 
    				else if(idanalyse == 10) { tab[1] = $('#goutte_epaisse').val(); tab[2] = $('#densite_parasitaire').val(); tab[3] = $('#type_materiel_goutte_epaisse').val(); tab[4] = $('#commentaire_goutte_epaisse').val(); }
    				     
    				else if(idanalyse == 14) { tab    = getTpInr(); }
    				else if(idanalyse == 15) { tab    = getTca(); }
    				else if(idanalyse == 16) { tab[1] = $('#fibrinemie').val(); tab[2] = $('#type_materiel_fibrinemie').val(); }
    				else if(idanalyse == 17) { tab[1] = $('#temps_saignement').val(); tab[2] = $('#type_materiel_temps_saignement').val();  }
    				else if(idanalyse == 18) { tab    = getFacteur8();  }
    				else if(idanalyse == 19) { tab    = getFacteur9();  }
    				else if(idanalyse == 20) { tab    = getDDimeres();  }
    				else if(idanalyse == 21) { tab[1] = $('#glycemie_1').val(); tab[2] = $('#glycemie_2').val(); tab[3] = $('#type_materiel_glycemie').val();}
    				else if(idanalyse == 22) { tab[1] = $('#creatininemie').val(); tab[2] = $('#type_materiel_creatininemie').val(); }
    				else if(idanalyse == 23) { tab    = azotemie(); }
    				else if(idanalyse == 24) { tab[1] = $('#acide_urique').val(); tab[2] = $('#type_materiel_acide_urique').val(); tab[3] = $('#acide_urique_umol').val(); }
    				else if(idanalyse == 25) { tab[1] = $('#cholesterol_total_1').val(); tab[2] = $('#cholesterol_total_2').val(); tab[3] = $('#type_materiel_cholesterol_total').val(); } 
    				else if(idanalyse == 26) { tab[1] = $('#triglycerides_1').val(); tab[2] = $('#triglycerides_2').val(); tab[3] = $('#type_materiel_triglycerides').val(); }
    				else if(idanalyse == 27) { tab[1] = $('#cholesterol_HDL_1').val(); tab[2] = $('#cholesterol_HDL_2').val(); tab[3] = $('#type_materiel_cholesterol_HDL').val(); }
    				else if(idanalyse == 28) { tab[1] = $('#cholesterol_LDL_1').val(); tab[2] = $('#cholesterol_LDL_2').val(); tab[3] = $('#type_materiel_cholesterol_LDL').val(); }
    				else if(idanalyse == 29) { 
    					tab[1] = $('#cholesterol_total_1').val(); tab[2] = $('#cholesterol_total_2').val(); tab[9]  = $('#type_materiel_cholesterol_total').val(); 
    				    tab[3] = $('#cholesterol_HDL_1').val();   tab[4] = $('#cholesterol_HDL_2').val();   tab[10] = $('#type_materiel_cholesterol_HDL').val(); 
    				    tab[5] = $('#cholesterol_LDL_1').val();   tab[6] = $('#cholesterol_LDL_2').val();   tab[11] = $('#type_materiel_cholesterol_LDL').val(); 
    				    tab[7] = $('#triglycerides_1').val();     tab[8] = $('#triglycerides_2').val();     tab[12] = $('#type_materiel_triglycerides').val(); 
    				}
    				else if(idanalyse == 30) { tab = getLipidesTotaux(); }   
    				else if(idanalyse == 31) { tab = getIonogramme(); }     
    				else if(idanalyse == 32) { tab[1] = $('#calcemie').val(); tab[2] = $('#type_materiel_calcemie').val(); tab[3] = $('#calcemie_mmol').val(); }
    				else if(idanalyse == 33) { tab[1] = $('#magnesemie').val(); tab[2] = $('#type_materiel_magnesemie').val(); }
    				else if(idanalyse == 34) { tab[1] = $('#phosphoremie').val(); tab[2] = $('#type_materiel_phosphoremie').val(); tab[3] = $('#phosphoremie_mmol').val(); }     
    				else if(idanalyse == 35) { tab = getAsat(); } 
    				else if(idanalyse == 36) { tab = getAlat(); }  
    				else if(idanalyse == 37) { tab = getAsatAlat(); }
    				else if(idanalyse == 38) { tab = phosphatageAlcaline(); }   
    				else if(idanalyse == 39) { tab = gamaGT(); }   
    				else if(idanalyse == 40) { tab = getFerSerique(); }   
    				else if(idanalyse == 41) { tab = getFerritinine(); }   
    				else if(idanalyse == 42) { tab = getBilirubineTotaleDirecte(); }
    				else if(idanalyse == 43) { tab = getHemoglobineGlyqueeHBAC(); } 
    				else if(idanalyse == 44) { tab = getElectrophoreseHemoglobine(); }
    				else if(idanalyse == 45) { tab = getElectrophoreseProteines(); }
    				else if(idanalyse == 46) { tab = getAlbuminemie(); } 
    				else if(idanalyse == 47) { tab = getAlbumineUrinaire(); } 
    				else if(idanalyse == 48) { tab = getProtidemie(); } 
    				else if(idanalyse == 49) { tab = getProteinurie(); } 
    				else if(idanalyse == 50) { tab = getHlmCompteDaddis(); } 
    				else if(idanalyse == 51) { tab = getBetaHcgPlasmatique(); } 
    				else if(idanalyse == 52) { tab = getPsa(); } 
    				else if(idanalyse == 53) { tab = getCrp(); } 
    				else if(idanalyse == 54) { tab = getFacteursRhumatoides(); } 
    				else if(idanalyse == 55) { tab = getRfWaalerRose(); } 
    				else if(idanalyse == 56) { tab = getToxoplasmose(); } 
    				else if(idanalyse == 57) { tab = getRubeole(); } 
    				else if(idanalyse == 58) { tab = /*getCulotUrinaire();*/ getCulotUrinaireListeSelect(); } 
    				else if(idanalyse == 59) { tab = getSerologieChlamydiae(); }
    				else if(idanalyse == 60) { tab = getSerologieSyphilitique(); } 
    				else if(idanalyse == 61) { tab = getAslo(); } 
    				else if(idanalyse == 62) { tab = getWidal(); } 
    				else if(idanalyse == 63) { tab = getAgHbs(); } 
    				else if(idanalyse == 64) { tab = getHIV(); }
    				else if(idanalyse == 65) { tab = getPV(); }
    				     
    				     
    				else if(idanalyse == 68) { tab = getTypageHemoglobine(); }
    				     
    				else if(idanalyse == 70) { tab = getLDH(); }     
    				else if(idanalyse == 71) { tab = getChampsNfs_TAD(iddemande); }
    				     
    				     /*
    				     alert(tab[17]); 
    				     alert(tab[28]); 
    				     alert(tab[28][10]); 
    				     
    				     return false;
    				     */
    				     
    				$( this ).dialog( "close" );

    				$.ajax({
    					type: 'POST',
    					url: tabUrl[0]+'public/technicien/enregistrer-resultat',
    					data: {'idanalyse':idanalyse, 'iddemande':iddemande, 'tab':tab},
    					success: function(data) {
    						var result = jQuery.parseJSON(data);  
    						var resultatExiste = result[1];
    						if(resultatExiste == 0){
    							$('.resultat_existe'+result[0]).empty();
    						}else {
    							$('.resultat_existe'+result[0]).html("<img  src='../images_icons/tick_16.png' />");
    						}
    					}
    				});
    			},
    			"Fermer": function() {
    				$(this).dialog( "close" );
    			}
    		}
    	});
    }
    
    function resultatAnalyse(iddemande){
    	var chemin = tabUrl[0]+'public/technicien/recuperer-analyse';
        $.ajax({
            type: 'POST',
            url: chemin ,
            data:'iddemande='+iddemande,
            success: function(data) {
            	     var result = jQuery.parseJSON(data); 
            	     var idanalyse = result[0];
            	     resultatsAnalyses(idanalyse, iddemande);
            	     
            	     $('#contenuResultatsAnalysesParType div').empty();
            	     $('#contenuResultatsAnalysesDuneDemande div').empty();
            	     $('#contenuResultatsAnalyses div').empty();
            	     $('#contenuResultatsAnalyses div').html(result[1]);
            	     gestionFormuleLeucocytaire();
            	     rapportCHOL_HDL();
            	     getCreatininemie_umol();
            	     getTcaRatio();
            	     getCholesterolHDLFormule();
            	     getCholesterolLDLFormule();
            	     getFormuleCholesterolTotal();
            	     getTriglyceridesFormule();
            	     getGlycemieFormule();
            	     getElectrophoreseProteinesFormule();
            	     getAsatAlatAuto();
            	     getFerSeriqueFormule();
            	     getAzotemieFormule();
            	     getAcideUriqueFormule();
            	     getCalcemieFormule();
            	     getPhosphoremieFormule();
            	     getAlbuminemieFormule();
            	     getProteinurieFormule();
            	     
            	     ajoutCulotUrinaireAuto();
            	     getBilirubineTotaleDirecteAuto();
            	     getHemoglobineGlyqueeHbA1cFormule();
            	     
            	     //Ajouter des lignes
            	     getTestCombsIndirectAjout();
            	     getElectroHemo();
            	     
            	     $("#resultatsAnalyses").dialog('open');
            }
        });
    	
    }
    
    
    function getLipidesTotaux(){
    	var tab = [];
    	tab[1] = $('#type_materiel_lipides_totaux').val();
    	tab[2] = $('#lipides_totaux').val();
    	
    	return tab;
    }
    
    function getChampsGroupeRhesus(){
    	var tab = [];
    	tab[1] = $('#groupe').val();
    	tab[2] = $('#rhesus').val();
    	
    	return tab;
    }
    
    function getGoutteEpaisse(){
    	var tab = [];
    	tab[1] = $('#goutte_epaisse').val();
    	tab[2] = $('#densite_parasitaire').val();
    	tab[3] = $('#type_materiel_goutte_epaisse').val();
    	tab[4] = $('#commentaire_goutte_epaisse').val();
    	
    	return tab;
    }
    
    function getTpInr(){
    	var tab = [];
    	tab[1] = $('#temps_quick_temoin').val(); 
    	tab[2] = $('#temps_quick_patient').val(); 
    	tab[3] = $('#taux_prothrombine_patient').val(); 
    	tab[4] = $('#inr_patient').val();
    	tab[5] = $('#type_materiel_tp_inr').val();
    	
    	return tab;
    }
    
    function getTca(){
    	var tab = [];
    	tab[1] = $('#tca_patient').val(); 
    	tab[2] = $('#temoin_patient').val();
    	tab[3] = $('#type_materiel_tca').val();
    	
    	return tab;
    }
    
    function getGlycemie(){
    	var tab = [];
    	tab[1] = $('#glycemie_1').val(); 
    	tab[2] = $('#glycemie_2').val();
    	tab[3] = $('#type_materiel_glycemie').val();
    	
    	return tab;
    }
    
    function getCholesterolTotal(){
    	var tab = [];
    	tab[1] = $('#cholesterol_total_1').val(); 
    	tab[2] = $('#cholesterol_total_2').val();
    	tab[3] = $('#type_materiel_cholesterol_total').val();
    	
    	return tab;
    }
    
    function getTriglycerides(){
    	var tab = [];
    	tab[1] = $('#triglycerides_1').val(); 
    	tab[2] = $('#triglycerides_2').val();
    	tab[3] = $('#type_materiel_triglycerides').val();
    	
    	return tab;
    }
    
    function getCholesterolHDL(){
    	var tab = [];
    	tab[1] = $('#cholesterol_HDL_1').val(); 
    	tab[2] = $('#cholesterol_HDL_2').val();
    	tab[3] = $('#type_materiel_cholesterol_HDL').val();
    	
    	return tab;
    }
    
    function getCholesterolLDL(){
    	var tab = [];
    	tab[1] = $('#cholesterol_LDL_1').val(); 
    	tab[2] = $('#cholesterol_LDL_2').val();
    	tab[3] = $('#type_materiel_cholesterol_LDL').val();
    	
    	return tab;
    }
    
    function getIonogramme(){
    	var tab = [];
    	tab[1] = $('#sodium_sanguin').val(); 
		tab[2] = $('#potassium_sanguin').val();
		tab[3] = $('#chlore_sanguin').val();
		tab[4] = $('#type_materiel_ionogramme').val();
    	
    	return tab;
    }
    
    function getChol_Total_HDL_LDL_Trigly(iddemande){
    	var tab = [];
	    tab[1] = $('.ER_'+iddemande+' #cholesterol_total_1').val(); tab[2] = $('.ER_'+iddemande+' #cholesterol_total_2').val(); tab[9]  = $('.ER_'+iddemande+' #type_materiel_cholesterol_total').val(); 
	    tab[3] = $('.ER_'+iddemande+' #cholesterol_HDL_1').val();   tab[4] = $('.ER_'+iddemande+' #cholesterol_HDL_2').val();   tab[10] = $('.ER_'+iddemande+' #type_materiel_cholesterol_HDL').val(); 
	    tab[5] = $('.ER_'+iddemande+' #cholesterol_LDL_1').val();   tab[6] = $('.ER_'+iddemande+' #cholesterol_LDL_2').val();   tab[11] = $('.ER_'+iddemande+' #type_materiel_cholesterol_LDL').val(); 
	    tab[7] = $('.ER_'+iddemande+' #triglycerides_1').val();     tab[8] = $('.ER_'+iddemande+' #triglycerides_2').val();     tab[12] = $('.ER_'+iddemande+' #type_materiel_triglycerides').val(); 
		
	    return tab;
    }
    
    function getAsatAlat(){
    	var tab = [];
    	tab[1] = $('#type_materiel_tgp_alat').val();
    	tab[2] = $('#tgp_alat').val();
	    tab[3] = $('#type_materiel_tgo_asat').val();
	    tab[4] = $('#tgo_asat').val();
	    
	    return tab;
    }
    
    function getAlat(){
    	var tab = [];
    	tab[1] = $('#type_materiel_tgp_alat').val();
    	tab[2] = $('#tgp_alat').val();
	    
	    return tab;
    }
    
    function getAsat(){
    	var tab = [];
	    tab[1] = $('#type_materiel_tgo_asat').val();
	    tab[2] = $('#tgo_asat').val();
	    
	    return tab;
    }
    
    function phosphatageAlcaline(){
    	var tab = [];
    	
    	tab[1] = $('#phosphatage_alcaline').val(); 
    	tab[2] = $('#type_materiel_phosphatage_alcaline').val();
	    
    	return tab;
    }
    
    function gamaGT(){
    	var tab = [];
    	
    	tab[1] = $('#gama_gt').val(); 
    	tab[2] = $('#type_materiel_gama_gt_ygt').val();
	    
    	return tab;
    }
    
    function getFerritinine(){
    	var tab = [];
    	
    	tab[1] = $('#type_materiel_ferritinine').val();
    	tab[2] = $('#ferritinine').val();
	    
	    return tab;
    }
    
    function getBilirubineTotaleDirecte(){
    	var tab = [];
    	
    	tab[1] = $('#type_materiel_bilirubine_totale_directe').val();
    	tab[2] = $('#bilirubine_totale').val();
    	tab[3] = $('#bilirubine_totale_auto').val();
    	tab[4] = $('#bilirubine_directe').val();
    	tab[5] = $('#bilirubine_directe_auto').val();
    	tab[6] = $('#bilirubine_indirecte').val();
    	tab[7] = $('#bilirubine_indirecte_auto').val();
	    
	    return tab;
    }
    
    function getBilirubineTotaleDirecteAuto(){
    	var bilirubine_totale    = $('#bilirubine_totale').val();
    	var bilirubine_directe   = $('#bilirubine_directe').val();
    	var bilirubine_indirecte = $('#bilirubine_indirecte').val();
    	
    	$("#bilirubine_totale").keyup( function () {
    		var bilirubine_totale = $('#bilirubine_totale').val();
    		
    		if(bilirubine_totale){
    			var bilirubine_totale_auto = bilirubine_totale*1.7105;
    			$('#bilirubine_totale_auto').val(bilirubine_totale_auto.toFixed(2));
    		}else{
    			$('#bilirubine_totale_auto').val('');
    		}
    		
    		
    		
    		var bilirubine_directe = $('#bilirubine_directe').val();
    		if(bilirubine_totale && bilirubine_directe){
    			var bilirubine_indirecte = bilirubine_totale-bilirubine_directe;
    			$('#bilirubine_indirecte').val(bilirubine_indirecte);
    			
    			var bilirubine_indirecte_auto = bilirubine_indirecte*1.7105;
    			$('#bilirubine_indirecte_auto').val(bilirubine_indirecte_auto.toFixed(2));
    		}else{
    			$('#bilirubine_indirecte, #bilirubine_indirecte_auto').val('');
    		}
    		
    	}).change( function() {
    		var bilirubine_totale = $('#bilirubine_totale').val();
    		
    		if(bilirubine_totale){
    			var bilirubine_totale_auto = bilirubine_totale*1.7105;
    			$('#bilirubine_totale_auto').val(bilirubine_totale_auto.toFixed(2));
    		}else{
    			$('#bilirubine_totale_auto').val('');
    		}
    		
    		
    		
    		var bilirubine_directe = $('#bilirubine_directe').val();
    		if(bilirubine_totale && bilirubine_directe){
    			var bilirubine_indirecte = bilirubine_totale-bilirubine_directe;
    			$('#bilirubine_indirecte').val(bilirubine_indirecte);
    			
    			var bilirubine_indirecte_auto = bilirubine_indirecte*1.7105;
    			$('#bilirubine_indirecte_auto').val(bilirubine_indirecte_auto.toFixed(2));
    		}else{
    			$('#bilirubine_indirecte, #bilirubine_indirecte_auto').val('');
    		}
    	});
    	
    	
    	$("#bilirubine_directe").keyup( function () {
    		var bilirubine_directe = $('#bilirubine_directe').val();
    		
    		if(bilirubine_directe){
    			var bilirubine_directe_auto = bilirubine_directe*1.7105;
    			$('#bilirubine_directe_auto').val(bilirubine_directe_auto.toFixed(2));
    		}else{
    			$('#bilirubine_directe_auto').val('');
    		}
    		
    		
    		
    		var bilirubine_totale = $('#bilirubine_totale').val();
    		if(bilirubine_totale && bilirubine_directe){
    			var bilirubine_indirecte = bilirubine_totale-bilirubine_directe;
    			$('#bilirubine_indirecte').val(bilirubine_indirecte);
    			
    			var bilirubine_indirecte_auto = bilirubine_indirecte*1.7105;
    			$('#bilirubine_indirecte_auto').val(bilirubine_indirecte_auto.toFixed(2));
    		}else{
    			$('#bilirubine_indirecte, #bilirubine_indirecte_auto').val('');
    		}
    		
    	}).change( function() {
    		var bilirubine_directe = $('#bilirubine_directe').val();
    		
    		if(bilirubine_directe){
    			var bilirubine_directe_auto = bilirubine_directe*1.7105;
    			$('#bilirubine_directe_auto').val(bilirubine_directe_auto.toFixed(2));
    		}else{
    			$('#bilirubine_directe_auto').val('');
    		}
    		
    		
    		
    		var bilirubine_totale = $('#bilirubine_totale').val();
    		if(bilirubine_totale && bilirubine_directe){
    			var bilirubine_indirecte = bilirubine_totale-bilirubine_directe;
    			$('#bilirubine_indirecte').val(bilirubine_indirecte);
    			
    			var bilirubine_indirecte_auto = bilirubine_indirecte*1.7105;
    			$('#bilirubine_indirecte_auto').val(bilirubine_indirecte_auto.toFixed(2));
    		}else{
    			$('#bilirubine_indirecte, #bilirubine_indirecte_auto').val('');
    		}
    	});
    	
    	
    }
    
    function getHemoglobineGlyqueeHBAC(){
    	var tab = [];
    	tab[1] = $('#type_materiel_hemo_gly_hbac').val();
    	tab[2] = $('#hemoglobine_glyquee_hbac').val();
    	tab[3] = $('#hemoglobine_glyquee_hbac_mmol').val();
	    
	    return tab;
    }
    
    function getFerSerique(){
    	var tab = [];
    	tab[1] = $('#fer_serique_ug').val();
    	tab[2] = $('#fer_serique_umol').val();
    	tab[3] = $('#type_materiel_fer_serique').val();
    	
    	return tab;
    }
    
    function getFacteur8(){
    	var tab = [];
    	tab[1] = $('#facteur_8').val(); 
    	tab[2] = $('#type_materiel_facteur_8').val(); 
    	
    	return tab;
    }
    
    function getFacteur9(){
    	var tab = [];
    	tab[1] = $('#facteur_9').val(); 
    	tab[2] = $('#type_materiel_facteur_9').val(); 
    	
    	return tab;
    }
    
    function getElectrophoreseProteines(){
    	var tab = [];
    	
    	tab[1]  = $('#type_materiel_electro_proteine').val();
    	tab[2]  = $('#albumine').val();
    	tab[3]  = $('#albumine_abs').val();
    	tab[4]  = $('#alpha_1').val();
    	tab[5]  = $('#alpha_1_abs').val();
    	tab[6]  = $('#alpha_2').val();
    	tab[7]  = $('#alpha_2_abs').val();
    	tab[8]  = $('#beta_1').val();
    	tab[9]  = $('#beta_1_abs').val();
    	tab[10]  = $('#beta_2').val();
    	tab[11] = $('#beta_2_abs').val();
    	tab[12] = $('#gamma').val();
    	tab[13] = $('#gamma_abs').val();
    	tab[14] = $('#proteine_totale').val();
    	tab[15] = $('#commentaire_electrophorese_proteine').val();
    	
    	return tab;
    }
    
    function getAlbuminemie(){
    	var tab = [];
    	tab[1] = $('#type_materiel_albuminemie').val();
    	tab[2] = $('#albuminemie').val();
    	tab[3] = $('#albuminemie_umol').val();
	    
	    return tab;
    }
    
    function getAlbumineUrinaire(){
    	var tab = [];
    	tab[1] = $('#type_materiel_albumine_urinaire').val();
    	
    	var albumine_urinaire = $('#albumine_urinaire').val();
    	tab[2] = albumine_urinaire;
    	tab[3] = null;
    	if(albumine_urinaire == 'positif'){ tab[3] = $('#albumine_urinaire_degres').val(); }
    	
    	var sucre_urinaire = $('#sucre_urinaire').val();
    	tab[4] = sucre_urinaire;
    	tab[5] = null;
    	if(sucre_urinaire == 'positif'){ tab[5] = $('#sucre_urinaire_degres').val(); }
    	
    	var corps_cetonique_urinaire = $('#corps_cetonique_urinaire').val();
    	tab[6] = corps_cetonique_urinaire;
    	tab[7] = null;
    	if(corps_cetonique_urinaire == 'positif'){ tab[7] = $('#corps_cetonique_urinaire_degres').val(); }
	    
	    return tab;
    }
    
    function getProtidemie(){
    	var tab = [];
    	tab[1] = $('#type_materiel_protidemie').val();
    	tab[2] = $('#protidemie').val();
	    
	    return tab;
    }
    
    function getProteinurie(){
    	var tab = [];
    	tab[1] = $('#type_materiel_proteinurie').val();
    	tab[2] = $('#proteinurie_1').val();
    	tab[3] = $('#proteinurie_2').val();
    	tab[4] = $('#proteinurie_g24h').val();
	    
	    return tab;
    }
    
    function getHlmCompteDaddis(){
    	var tab = [];
    	tab[1] = $('#type_materiel_hlm_compte_daddis').val();
    	tab[2] = $('#hematies_hlm').val();
    	tab[3] = $('#leucocytes_hlm').val();
    	tab[4] = $('#commentaire_hlm_compte_daddis').val();
	    
	    return tab;
    }
    
    function getBetaHcgPlasmatique(){
    	var tab = [];
    	tab[1] = $('#type_materiel_beta_hcg').val();
    	tab[2] = $('#beta_hcg_plasmatique').val();
	    
	    return tab;
    }
    
    function getPsa(){
    	var tab = [];
    	tab[1] = $('#type_materiel_psa').val();
    	tab[2] = $('#psa').val();
    	tab[3] = $('#psa_qualitatif').val();
	    
	    return tab;
    }
    
    function getCrp(){
    	var tab = [];
    	tab[1] = $('#type_materiel_crp').val();
    	tab[2] = $('#optionResultatCrp').val();
    	tab[3] = $('#crpValeurResultat').val();
	    
	    return tab;
    }
    
    function getChoixResultatCrp(id){
    	if(id == 'positif'){
    		$('#crpValeurResultatChamp').css({'visibility':'visible'});
    	}else{
    		$('#crpValeurResultatChamp').css({'visibility':'hidden'});
    		$('#crpValeurResultat').val('')
    	}
    }
    
    function getFacteursRhumatoides(){
    	var tab = [];
    	tab[1] = $('#type_materiel_facteurs_rhumatoides').val();
    	tab[2] = $('#facteurs_rhumatoides').val();
    	tab[3] = $('#facteurs_rhumatoides_titre').val();
	    
	    return tab;
    }
    
    function getRfWaalerRose(){
    	var tab = [];
    	tab[1] = $('#type_materiel_rf_waaler_rose').val();
    	tab[2] = $('#rf_waaler_rose').val();
    	tab[3] = $('#rf_waaler_rose_titre').val();
	    
	    return tab;
    }
    
    function getToxoplasmose(){
    	var tab = [];
    	tab[1] = $('#type_materiel_toxoplasmose').val();
    	tab[2] = $('#toxoplasmose_igm').val();
    	tab[3] = $('#toxoplasmose_igm_titre').val();
    	tab[4] = $('#toxoplasmose_igg').val();
    	tab[5] = $('#toxoplasmose_igg_titre').val();
    	tab[6] = $('#toxoplasmose_commentaire').val();
	    
	    return tab;
    }
    
    function getRubeole(){
    	var tab = [];
    	tab[1] = $('#type_materiel_rubeole').val();
    	tab[2] = $('#rubeole_igm').val();
    	tab[3] = $('#rubeole_igm_titre').val();
    	tab[4] = $('#rubeole_igg').val();
    	tab[5] = $('#rubeole_igg_titre').val();
    	tab[6] = $('#rubeole_commentaire').val();
	    
	    return tab;
    }
    
    function getCulotUrinaire(){
    	var tab = [];
    	tab[1] = $('#type_materiel_culot_urinaire').val();
    	tab[2] = $('#culot_urinaire_1').val();
    	tab[3] = $('#culot_urinaire_2').val();
	    
	    return tab;
    }
    
    function getSerologieChlamydiae(){
    	var tab = [];
    	tab[1] = $('#type_materiel_serologie_chlamydiae').val();
    	tab[2] = $('#serologie_chlamydiae').val();
	    
	    return tab;
    }
    
    function getSerologieSyphilitique(){
    	var tab = [];
    	tab[1] = $('#type_materiel_serologie_syphilitique').val();
    	tab[2] = $('#serologie_syphilitique_rpr').val();
    	tab[3] = $('#serologie_syphilitique_tpha').val();
    	tab[4] = $('#serologie_syphilitique_tpha_titre').val();
	    
	    return tab;
    }
    
    function getAslo(){
    	var tab = [];
    	tab[1] = $('#type_materiel_aslo').val();
    	tab[2] = $('#aslo_select').val();
    	tab[3] = $('#aslo_titre').val();
	    
	    return tab;
    }
    
    function getWidal(){
    	var tab = [];
    	tab[1]  = $('#type_materiel_widal').val();
    	
    	tab[2]  = $('#widal_to').val();
    	tab[3]  = $('#widal_titre_to').val();
    	
    	tab[4]  = $('#widal_th').val();
    	tab[5]  = $('#widal_titre_th').val();
    	
    	tab[6]  = $('#widal_ao').val();
    	tab[7]  = $('#widal_titre_ao').val();
    	
    	tab[8]  = $('#widal_ah').val();
    	tab[9]  = $('#widal_titre_ah').val();
    	
    	tab[10] = $('#widal_bo').val();
    	tab[11] = $('#widal_titre_bo').val();
	    
    	tab[12] = $('#widal_bh').val();
    	tab[13] = $('#widal_titre_bh').val();
    	
    	tab[14] = $('#widal_co').val();
    	tab[15] = $('#widal_titre_co').val();
    	
    	tab[16] = $('#widal_ch').val();
    	tab[17] = $('#widal_titre_ch').val();
    	
	    return tab;
    }
    
    function getAgHbs(){
    	var tab = [];
    	tab[1] = $('#type_materiel_ag_hbs').val();
    	tab[2] = $('#ag_hbs').val();
	    
	    return tab;
    }
    
    function getHIV(){
    	var tab = [];
    	tab[1] = $('#type_materiel_hiv').val();
    	tab[2] = $('#hiv').val();
    	tab[3] = $('#hiv_typage').val();
	    
	    return tab;
    }
    
    function getPV(){
    	var tab = [];
    	tab[1] = $('#type_materiel_pv').val();
    	tab[2] = $('#aspect_pertes_abondance_pv').val();
    	tab[3] = $('#aspect_pertes_couleurs_pv').val();
    	tab[4] = $('#aspect_pertes_odeurs_pv').val();
    	tab[5] = $('#aspect_organe_pv').val();
    	
    	tab[6]  = $('#leucocytes_champ_pv').val();
    	tab[7]  = $('#hematies_champ_pv').val();
    	tab[8]  = $('#cellules_epitheliales_champ_pv').val();
    	tab[9]  = $('#trichomonas_vaginalis_pv').val();
    	tab[10] = $('#levures_filaments_myceliens_pv').val();
    	
    	tab[11] = $('#gardnerella_vaginalis_pv').val();
    	tab[12] = $('#mobiluncus_spp_pv').val();
    	tab[13] = $('#clue_cells_pv').val();
    	tab[14] = $('#lactobacillus_pv').val();
    	tab[15] = $('#autre_flore_pv').val();

    	tab[16] = $('#culture_pv').val();
    	tab[17] = $('#identification_culture_pv').val();
    	tab[18] = $('#recherche_directe_mycoplasmes_pv').val();
    	//tab[19] = $('#identification_rdm_pv').val();
    	tab[20] = $('#commentaire_pv').val();
    	
    	tab[21] = $('#leucocytes_champ_valeur_pv').val();
    	tab[22] = $('#hematies_champ_valeur_pv').val();
    	tab[23] = $('#cellules_epitheliales_champ_valeur_pv').val();
    	tab[24] = $('#flore_pv').val();
    	tab[25] = $('#flore_note_pv').val();
    	tab[26] = $('#recherche_directe_antigene_chlamydia_pv').val();
    	tab[27] = $('#identification_rdm_positive_pv').val();
    	
    	/**-- Les infos de l'antibiogramme ---**/
    	if(Number(tab[17]) != 0){
    		tab[28] = new Array();
    		 
    		/**
    		 * PARTIE B-lactamines
    		 */
    		if($("#choixAmpicillineAMABG").get(0).checked){ tab[28][1] = $("#ampicillineAMABG").val(); }else{ tab[28][1] = -1; } 
    		if($("#choixAmoxillineAMXABG").get(0).checked){ tab[28][2] = $("#amoxillineAMXABG").val(); }else{ tab[28][2] = -1; } 
    		if($("#choixTicarcillineTICABG").get(0).checked){ tab[28][3] = $("#ticarcillineTICABG").val(); }else{ tab[28][3] = -1; } 
    		if($("#choixPiperacillinePIPABG").get(0).checked){ tab[28][4] = $("#piperacillinePIPABG").val(); }else{ tab[28][4] = -1; } 
    		if($("#choixAcideClavulaniqueAmoxicillineAMCABG").get(0).checked){ tab[28][5] = $("#acideClavulaniqueAmoxicillineAMCABG").val(); }else{ tab[28][5] = -1; } 
    		
    		if($("#choixGentamicineGMABG").get(0).checked){ tab[28][6] = $("#gentamicineGMABG").val(); }else{ tab[28][6] = -1; } 
    		if($("#choixTicAcClavTCCABG").get(0).checked){ tab[28][7] = $("#ticAcClavTCCABG").val(); }else{ tab[28][7] = -1; } 
    		if($("#choixErtapenemeETPABG").get(0).checked){ tab[28][8] = $("#ertapenemeETPABG").val(); }else{ tab[28][8] = -1; } 
    		if($("#choixImipenemeIPMABG").get(0).checked){ tab[28][9] = $("#imipenemeIPMABG").val(); }else{ tab[28][9] = -1; } 
    		if($("#choixOxacillineOXABG").get(0).checked){ tab[28][10] = $("#oxacillineOXABG").val(); }else{ tab[28][10] = -1; } 
    		
    		if($("#choixPenicillinePABG").get(0).checked){ tab[28][11] = $("#penicillinePABG").val(); }else{ tab[28][11] = -1; } 
    		if($("#choixCefalotineCFABG").get(0).checked){ tab[28][12] = $("#cefalotineCFABG").val(); }else{ tab[28][12] = -1; } 
    		if($("#choixCefoxitineFOXABG").get(0).checked){ tab[28][13] = $("#cefoxitineFOXABG").val(); }else{ tab[28][13] = -1; } 
    		if($("#choixPiperacillineTazobactamePPTABG").get(0).checked){ tab[28][14] = $("#piperacillineTazobactamePPTABG").val(); }else{ tab[28][14] = -1; } 
    		if($("#choixCefotaximeCTXABG").get(0).checked){ tab[28][15] = $("#cefotaximeCTXABG").val(); }else{ tab[28][15] = -1; } 
    		
    		if($("#choixCefsulodineCFSABG").get(0).checked){ tab[28][16] = $("#cefsulodineCFSABG").val(); }else{ tab[28][16] = -1; } 
    		if($("#choixCFPABG").get(0).checked){ tab[28][17] = $("#CFPABG").val(); }else{ tab[28][17] = -1; } 
    		if($("#choixCeftazidimeCAZABG").get(0).checked){ tab[28][18] = $("#ceftazidimeCAZABG").val(); }else{ tab[28][18] = -1; } 
    		if($("#choixCeftriaxoneCROABG").get(0).checked){ tab[28][19] = $("#ceftriaxoneCROABG").val(); }else{ tab[28][19] = -1; } 
    		if($("#choixCefepimeFEPABG").get(0).checked){ tab[28][20] = $("#cefepimeFEPABG").val(); }else{ tab[28][20] = -1; } 
    		if($("#choixAztreonamATMABG").get(0).checked){ tab[28][21] = $("#aztreonamATMABG").val(); }else{ tab[28][21] = -1; } 
    		
    		/**
    		 * FIN PARTIE B-lactamines
    		 */
    		
    		/**
    		 * PARTIE Polymyxine
    		 */
    		if($("#choixFosfomycineFOSABG").get(0).checked){ tab[28][22] = $("#fosfomycineFOSABG").val(); }else{ tab[28][22] = -1; } 
    		if($("#choixVancomycineVAABG").get(0).checked){ tab[28][23]  = $("#vancomycineVAABG").val(); }else{ tab[28][23] = -1; } 
    		if($("#choixColistineCSABG").get(0).checked){ tab[28][24]    = $("#colistineCSABG").val(); }else{ tab[28][24] = -1; } 
    		/**
    		 * FIN PARTIE Polymyxine
    		 */
    		
    		/**
    		 * PARTIE Aminosides
    		 */
    		if($("#choixKanamycineKABG").get(0).checked){ tab[28][25]   = $("#kanamycineKABG").val(); }else{ tab[28][25] = -1; } 
    		if($("#choixTobramycineTBABG").get(0).checked){ tab[28][26] = $("#tobramycineTBABG").val(); }else{ tab[28][26] = -1; } 
    		if($("#choixAmikacineANABG").get(0).checked){ tab[28][27] = $("#amikacineANABG").val(); }else{ tab[28][27] = -1; } 
    		if($("#choixNetilmycineABG").get(0).checked){ tab[28][28] = $("#netilmycineABG").val(); }else{ tab[28][28] = -1; } 
    		/**
    		 * FIN PARTIE Aminosides
    		 */
    		
    		/**
    		 * PARTIE Phénicolés
    		 */
    		if($("#choixChloramphenicolCABG").get(0).checked){ tab[28][29] = $("#chloramphenicolCABG").val(); }else{ tab[28][29] = -1; } 
    		/**
    		 * FIN PARTIE Phénicolés
    		 */
    		
    		/**
    		 * PARTIE Cyclines
    		 */
    		if($("#choixTetracyclineTEABG").get(0).checked){ tab[28][30] = $("#tetracyclineTEABG").val(); }else{ tab[28][30] = -1; } 
    		if($("#choixDoxycyclineDOABG").get(0).checked){ tab[28][31] = $("#doxycyclineDOABG").val(); }else{ tab[28][31] = -1; } 
    		/**
    		 * FIN PARTIE Cyclines
    		 */
    		
    		/** 
    		 * PARTIE Macrolides et apparentés 
    		 */
    		if($("#choixErythromycineEABG").get(0).checked){ tab[28][32] = $("#erythromycineEABG").val(); }else{ tab[28][32] = -1; } 
    		if($("#choixLincomycineLABG").get(0).checked){ tab[28][33] = $("#lincomycineLABG").val(); }else{ tab[28][33] = -1; } 
    		if($("#choixPristinamycinePTABG").get(0).checked){ tab[28][34] = $("#pristinamycinePTABG").val(); }else{ tab[28][34] = -1; } 
    		
    		/** 
    		 * FIN PARTIE Macrolides et apparentés 
    		 */
    		
    		/**
    		 * PARTIE Fluoroquinolones
    		 */
    		if($("#choixAcideFusidiqueFAABG").get(0).checked){ tab[28][35] = $("#acideFusidiqueFAABG").val(); }else{ tab[28][35] = -1; } 
    		if($("#choixAcideNalidixiqueNAABG").get(0).checked){ tab[28][36] = $("#acideNalidixiqueNAABG").val(); }else{ tab[28][36] = -1; } 
    		if($("#choixPefloxacinePEFABG").get(0).checked){ tab[28][37] = $("#pefloxacinePEFABG").val(); }else{ tab[28][37] = -1; } 
    		if($("#choixNorfloxacineNORABG").get(0).checked){ tab[28][38] = $("#norfloxacineNORABG").val(); }else{ tab[28][38] = -1; } 
    		if($("#choixCiprofloxacineCIPABG").get(0).checked){ tab[28][39] = $("#ciprofloxacineCIPABG").val(); }else{ tab[28][39] = -1; } 
    		if($("#choixLEVABG").get(0).checked){ tab[28][40] = $("#LEVABG").val(); }else{ tab[28][40] = -1; } 
    		/**
    		 * FIN PARTIE Fluoroquinolones
    		 */
    		
    		/** 
    		 * PARTIE Imidazolés 
    		 */
    		if($("#choixRifampicineRAABG").get(0).checked){ tab[28][41] = $("#rifampicineRAABG").val(); }else{ tab[28][41] = -1; } 
    		if($("#choixCotrimoxazoleSXTABG").get(0).checked){ tab[28][42] = $("#cotrimoxazoleSXTABG").val(); }else{ tab[28][42] = -1; } 
    		/** 
    		 * FIN PARTIE Imidazolés 
    		 */
    		
    		/**
    		 * Conclusion
    		 */
    		 tab[28][43] = $("#conclusion_pv_ABG").val();
    		/**
    		 * ==========
    		 */
    	}else{ tab[28] = -1; }
    	
	    return tab;
    }
    
    function getChampFloreNote(id){
    	if(id == ''){
    		$('.flore_note_class_pv').css({'visibility':'hidden'});
    	}else{
    		$('.flore_note_class_pv').css({'visibility':'visible'}).val('');
    	}
    }
    
    function getChampIdentificationRdmPositive(id){
    	if(id == '' || id == 2){
    		$('.identification_rdm_positive_class_pv').css({'visibility':'hidden'});
    	}else{
    		$('.identification_rdm_positive_class_pv').css({'visibility':'visible'}).val('');
    	}
    }
    
    function getIconeAntibiogrammeIdentCulture(id,i){ 
    	if(id == 0){
    		$('.antibiogrammeButtonAffInterface'+i).toggle(false);
    	}else{
    		$('.antibiogrammeButtonAffInterface'+i).toggle(true);
    	}
    }
    
    
    function getLDH(){
    	var tab = [];
    	tab[1] = $('#type_materiel_ldh').val();
    	tab[2] = $('#valeur_ldh').val();
	    
	    return tab;
    }
    
    function getDensiteGoutteEpaisse(valeur, iddemande){
    	
    	if(valeur == 'Positif'){ 
    		$('.ER_'+iddemande+' #goutte_epaisse_positif').toggle(true); 
    	}else{ 
    		$('.ER_'+iddemande+' #goutte_epaisse_positif').toggle(false); 
    	} 
    };
    
    function getListeIdentificationCulture(id){
    	
    	 var listeIdentificationCulture = new Array();
    	 listeIdentificationCulture[1]='Candida albicans';
    	 listeIdentificationCulture[2]='Escherichia coli';
    	 listeIdentificationCulture[3]='Staphylococcus aureus';
    	 
    	 return listeIdentificationCulture[id];
    }
    
    function antibiogrammeAfficherInterface(){
    	
    	$( "#resultatsAnalysesPVAntiBioGramme" ).dialog({
    		resizable: false,
    		height:680,
    		width:800,
    		autoOpen: false,
    		modal: true,
    		buttons: { 			

    			"Terminer": function() {
    				$(this).dialog( "close" );
    			}
    		}
    	});
    	
    	var id = $('#identification_culture_pv').val();
    	
    	$("#valeurSoucheIsoleeIdentificationCulturePV").html(getListeIdentificationCulture(id));
    	$("#resultatsAnalysesPVAntiBioGramme").dialog('open');
    	
    }
    
    function getNombreCultureIdentifier(id){
    	if(id==1){
        	$('.nombreCultureIdentifierABG').css('visibility','visible').val(1);
        	$('.identificationCultureChampsABR_1').toggle(true);
    	}else{
        	$('.nombreCultureIdentifierABG').css('visibility','hidden');
        	$('.identificationCultureChamps').toggle(false);
        	$('#identification_culture_pv').val(0).trigger('change');
    	}
    }
    
    function getChampIdentificationCulture(nb){ 
    	
    	for(var i=1 ; i<nb ; i++){ 
    		var champIdentCult =""+
        	
			  '<table style="width: 100%;" class="identificationCultureChamps champIdentCultABG identificationCultureChampsABR_'+(i+1)+'"  style="visibility:hidden;">'+
		      '<tr class="ligneAnanlyse" style="width: 100%;">'+
		      '  <td style="width: 25%;"><label class="lab1" style="padding-top: 5px;"><span style="font-weight: bold;"> Identification </span></label></td>'+
		      '  <td style="width: 27%;">'+
		      '              <label class="lab2" style="padding-top: 5px;">'+
		      '                <select id="identification_culture_pv" style="width: 190px;" onchange="getIconeAntibiogrammeIdentCulture(this.value,'+(i+1)+')">'+
		      '                 <option value=0 > </option>'+
		      '                  <option value=1 >Candida albicans</option>'+
		      '                  <option value=2 >Escherichia coli</option>'+
		      '                  <option value=3 >Staphylococcus aureus</option>'+
		      '                </select>'+
		      '              </label>'+
		      '          </td>'+
		      '  <td style="width: 48%;"><label class="lab1 antibiogrammeButtonAffInterface'+(i+1)+'" style="padding-top: 0px; margin-top: 3px; margin-left: 10px; width: 30%; height: 15px; font-style: italic; border-radius: 35%; border: 3px solid #d8d8d8; padding-left: 10px; display: none;"> Antibiogramme </label></td>'+
		      '</tr>'+
		      '</table>';
    		
    		$('.identificationCultureChampsABR_'+i).after(champIdentCult);
    		
    	}
    	
    }
    
    function getChampsCultureIdentifierABG(id){ 
    	$(".champIdentCultABG").remove();
    	getChampIdentificationCulture(id);
    }
    
    //Automatisation des champs calculables  -----  Automatisation des champs calculables
    //Automatisation des champs calculables  -----  Automatisation des champs calculables
    //Automatisation des champs calculables  -----  Automatisation des champs calculables
    function rapportCHOL_HDL(){
    	var cholesterol_total_1 = $('#cholesterol_total_1').val();
    	var cholesterol_HDL_1 = $('#cholesterol_HDL_1').val();
    	var rapport = null;
    	
    	if(cholesterol_total_1 && cholesterol_HDL_1){
    		rapport = cholesterol_total_1/cholesterol_HDL_1;
    		
    		$('.rapport_chol_hdl').toggle(true);
    		$('#rapport_chol_hdl').val(rapport.toFixed(2));
    		
    		//Affichage de la conclusion du rapport
			if(rapport >= 4.5 && rapport <= 5){
				$('#conclusion_rapport_chol_hdl').html('<span style="color: orange; float: left"> Risque d\'ath&eacute;rog&egrave;ne faible </span>');
			}else if(rapport > 5 && rapport <= 6.5){
				$('#conclusion_rapport_chol_hdl').html('<span style="color: orange; float: left"> Risque d\'ath&eacute;rog&egrave;ne mod&eacute;r&eacute; </span>');
			}else if(rapport > 6.5){
				$('#conclusion_rapport_chol_hdl').html('<span style="color: red; float: left"> Risque d\'ath&eacute;rog&egrave;ne &eacute;lev&eacute; </span>');
			}else{
				$('#conclusion_rapport_chol_hdl').html('<span style="color: green; float: left;"> RAS </span>');
			}
    	}
    	
    	$("#cholesterol_total_1, #cholesterol_HDL_1").keyup( function () {
    		var cholesterol_total_1 = $("#cholesterol_total_1").val();
    		var cholesterol_HDL_1 = $("#cholesterol_HDL_1").val();
    		
    		if( cholesterol_total_1 == "" || cholesterol_total_1 == 0 || cholesterol_HDL_1 == "" || cholesterol_HDL_1 == 0 ){
    			$('.rapport_chol_hdl table').toggle(false);
    		}else
    		if( cholesterol_total_1 && cholesterol_HDL_1 ){
    			var rapport = cholesterol_total_1/cholesterol_HDL_1;
    			$('.rapport_chol_hdl').toggle(true);
    			$("#rapport_chol_hdl").val(rapport.toFixed(2));
    			$('.rapport_chol_hdl table').toggle(true);
    			
    			//Affichage de la conclusion du rapport
    			if(rapport >= 4.5 && rapport <= 5){
    				$('#conclusion_rapport_chol_hdl').html('<span style="color: orange; float: left"> Risque d\'ath&eacute;rog&egrave;ne faible </span>');
    			}else if(rapport > 5 && rapport <= 6.5){
    				$('#conclusion_rapport_chol_hdl').html('<span style="color: orange; float: left"> Risque d\'ath&eacute;rog&egrave;ne mod&eacute;r&eacute; </span>');
    			}else if(rapport > 6.5){
    				$('#conclusion_rapport_chol_hdl').html('<span style="color: red; float: left"> Risque d\'ath&eacute;rog&egrave;ne &eacute;lev&eacute; </span>');
    			}else{
    				$('#conclusion_rapport_chol_hdl').html('<span style="color: green; float: left;"> RAS </span>');
    			}
    		}
    		else { 
    			$("#rapport_chol_hdl").val(null); 
    			$('#conclusion_rapport_chol_hdl').html('<span style="color: green; float: left;"> RAS </span>');	
    		}
    		
    	} ).change( function () {
    		var cholesterol_total_1 = $("#cholesterol_total_1").val();
    		var cholesterol_HDL_1 = $("#cholesterol_HDL_1").val();
    		
    		if( cholesterol_total_1 == "" || cholesterol_total_1 == 0 || cholesterol_HDL_1 == "" || cholesterol_HDL_1 == 0 ){
    			$('.rapport_chol_hdl table').toggle(false);
    		}else
    		if( cholesterol_total_1 && cholesterol_HDL_1 ){
    			var rapport = cholesterol_total_1/cholesterol_HDL_1;
    			$('.rapport_chol_hdl').toggle(true);
    			$("#rapport_chol_hdl").val(rapport.toFixed(2));
    			$('.rapport_chol_hdl table').toggle(true);

    			//Affichage de la conclusion du rapport
    			if(rapport >= 4.5 && rapport <= 5){
    				$('#conclusion_rapport_chol_hdl').html('<span style="color: orange; float: left"> Risque d\'ath&eacute;rog&egrave;ne faible </span>');
    			}else if(rapport > 5 && rapport <= 6.5){
    				$('#conclusion_rapport_chol_hdl').html('<span style="color: orange; float: left"> Risque d\'ath&eacute;rog&egrave;ne mod&eacute;r&eacute; </span>');
    			}else if(rapport > 6.5){
    				$('#conclusion_rapport_chol_hdl').html('<span style="color: red; float: left"> Risque d\'ath&eacute;rog&egrave;ne &eacute;lev&eacute; </span>');
    			}else{
    				$('#conclusion_rapport_chol_hdl').html('<span style="color: green; float: left;"> RAS </span>');
    			}
    			
    		}
    		else {
    			$("#rapport_chol_hdl").val(null); 
    			$('#conclusion_rapport_chol_hdl').html('<span style="color: green; float: left"> RAS </span>');	
    		}
    		
    	} );
    	
    	
    }
    
    function getCreatininemie_umol(){
    	var creatininemie = $('#creatininemie').val();
    	var valeur_umol = null;
    	if(creatininemie){
    		valeur_umol = creatininemie * 8.84;
    		$('#creatininemie_umol').val(valeur_umol.toFixed(2));
    	}else{
    		$('#creatininemie_umol').val(null);
    	}
    	
    	$('#creatininemie').keyup( function () {
    		creatininemie = $('#creatininemie').val();
    		if(creatininemie){
        		valeur_umol = creatininemie * 8.84;
        		$('#creatininemie_umol').val(valeur_umol.toFixed(2));
        	}else{
        		$('#creatininemie_umol').val(null);
        	}
    	}).change( function(){
    		creatininemie = $('#creatininemie').val();
    		if(creatininemie){
        		valeur_umol = creatininemie * 8.84;
        		$('#creatininemie_umol').val(valeur_umol.toFixed(2));
        	}else{
        		$('#creatininemie_umol').val(null);
        	}
    	});
    	
    }
    
    function getTcaRatio(){
    	var tca_patient = $('#tca_patient').val();
    	var temoin_patient = $('#temoin_patient').val();
    	
    	if(tca_patient && temoin_patient){
    		var tca_ratio = tca_patient/temoin_patient;
    		$('#tca_ratio').val(tca_ratio.toFixed(2));
    	}else{
    		$('#tca_ratio').val(null);
    	}
    	
    	$('#tca_patient, #temoin_patient').keyup( function () {
    		var tca_patient = $('#tca_patient').val();
        	var temoin_patient = $('#temoin_patient').val();
        	
    		if(tca_patient && temoin_patient){
        		var tca_ratio = tca_patient/temoin_patient;
        		$('#tca_ratio').val(tca_ratio.toFixed(2));
        	}else{
        		$('#tca_ratio').val(null);
        	}
    		
    	}).change( function(){
    		var tca_patient = $('#tca_patient').val();
        	var temoin_patient = $('#temoin_patient').val();
        	
    		if(tca_patient && temoin_patient){
        		var tca_ratio = tca_patient/temoin_patient;
        		$('#tca_ratio').val(tca_ratio.toFixed(2));
        	}else{
        		$('#tca_ratio').val(null);
        	}
    	});
    	
    }
    
    function getFormuleCholesterolTotal(){
    	var cholesterol_total_1 = $('#cholesterol_total_1').val();
    	var valeur_mmol = null;
    	
    	$('#cholesterol_total_1').keyup( function () {
    		cholesterol_total_1 = $('#cholesterol_total_1').val();
    		if(cholesterol_total_1){
        		valeur_mmol = cholesterol_total_1 * 2.587;
        		$('#cholesterol_total_2').val(valeur_mmol.toFixed(2));
        	}else{
        		$('#cholesterol_total_2').val(null);
        	}
    	}).change( function(){
    		cholesterol_total_1 = $('#cholesterol_total_1').val();
    		if(cholesterol_total_1){
        		valeur_mmol = cholesterol_total_1 * 2.587;
        		$('#cholesterol_total_2').val(valeur_mmol.toFixed(2));
        	}else{
        		$('#cholesterol_total_2').val(null);
        	}
    	});
    	
    }
    
    function getCholesterolHDLFormule(){
    	var cholesterol_HDL_1 = $('#cholesterol_HDL_1').val();
    	var valeur_mmol = null;
    	
    	$('#cholesterol_HDL_1').keyup( function () {
    		cholesterol_HDL_1 = $('#cholesterol_HDL_1').val();
    		if(cholesterol_HDL_1){
        		valeur_mmol = cholesterol_HDL_1 * 2.587;
        		$('#cholesterol_HDL_2').val(valeur_mmol.toFixed(2));
        	}else{
        		$('#cholesterol_HDL_2').val(null);
        	}
    	}).change( function(){
    		cholesterol_HDL_1 = $('#cholesterol_HDL_1').val();
    		if(cholesterol_HDL_1){
        		valeur_mmol = cholesterol_HDL_1 * 2.587;
        		$('#cholesterol_HDL_2').val(valeur_mmol.toFixed(2));
        	}else{
        		$('#cholesterol_HDL_2').val(null);
        	}
    	});
    	
    }
    
    function getCholesterolLDLFormule(){
    	var cholesterol_LDL_1 = $('#cholesterol_LDL_1').val();
    	var valeur_mmol = null;
    	
    	$('#cholesterol_LDL_1').keyup( function () {
    		cholesterol_LDL_1 = $('#cholesterol_LDL_1').val();
    		if(cholesterol_LDL_1){
        		valeur_mmol = cholesterol_LDL_1 * 2.587;
        		$('#cholesterol_LDL_2').val(valeur_mmol.toFixed(2));
        	}else{
        		$('#cholesterol_LDL_2').val(null);
        	}
    	}).change( function(){
    		cholesterol_LDL_1 = $('#cholesterol_LDL_1').val();
    		if(cholesterol_LDL_1){
        		valeur_mmol = cholesterol_LDL_1 * 2.587;
        		$('#cholesterol_LDL_2').val(valeur_mmol.toFixed(2));
        	}else{
        		$('#cholesterol_LDL_2').val(null);
        	}
    	});
    	
    }
    

    function getTriglyceridesFormule(){
    	var triglycerides_1 = $('#triglycerides_1').val();
    	var valeur_mmol = null;
    	
    	$('#triglycerides_1').keyup( function () {
    		triglycerides_1 = $('#triglycerides_1').val();
    		if(triglycerides_1){
        		valeur_mmol = triglycerides_1 * 1.143;
        		$('#triglycerides_2').val(valeur_mmol.toFixed(2));
        	}else{
        		$('#triglycerides_2').val(null);
        	}
    	}).change( function(){
    		triglycerides_1 = $('#triglycerides_1').val();
    		if(triglycerides_1){
        		valeur_mmol = triglycerides_1 * 1.143;
        		$('#triglycerides_2').val(valeur_mmol.toFixed(2));
        	}else{
        		$('#triglycerides_2').val(null);
        	}
    	});
    	
    }
    
    
    function getGlycemieFormule(){
    	var glycemie_1 = $('#glycemie_1').val();
    	var valeur_mmol = null;
    	
    	$('#glycemie_1').keyup( function () {
    		glycemie_1 = $('#glycemie_1').val();
    		if(glycemie_1){
        		valeur_mmol = glycemie_1 * 5.551;
        		$('#glycemie_2').val(valeur_mmol.toFixed(2));
        	}else{
        		$('#glycemie_2').val(null);
        	}
    	}).change( function(){
    		glycemie_1 = $('#glycemie_1').val();
    		if(glycemie_1){
        		valeur_mmol = glycemie_1 * 5.551;
        		$('#glycemie_2').val(valeur_mmol.toFixed(2));
        	}else{
        		$('#glycemie_2').val(null);
        	}
    	});
    	
    }
    
    
    function getElectrophoreseProteinesFormule(){
    	var albumine = $('#albumine').val();
    	var alpha_1  = $('#alpha_1').val();
    	var alpha_2  = $('#alpha_2').val();
    	var beta_1   = $('#beta_1').val();
    	var beta_2   = $('#beta_2').val();
    	var gamma    = $('#gamma').val();
    	var proteine_totale    = $('#proteine_totale').val();
    	
    	$('#albumine, #proteine_totale').keyup( function () {
    		albumine = $('#albumine').val();
    		proteine_totale    = $('#proteine_totale').val();
    		if(albumine && proteine_totale){ 
        		var albumine_abs = (albumine * proteine_totale)/100;
        		$('#albumine_abs').val(albumine_abs.toFixed(1));
        	}else{
        		$('#albumine_abs').val(null);
        	}
    	}).change( function(){
    		albumine = $('#albumine').val();
    		proteine_totale    = $('#proteine_totale').val();
    		if(albumine && proteine_totale){ 
        		var albumine_abs = (albumine * proteine_totale)/100;
        		$('#albumine_abs').val(albumine_abs.toFixed(1));
        	}else{
        		$('#albumine_abs').val(null);
        	}
    	});
    	
    	$('#alpha_1, #proteine_totale').keyup( function () {
    		alpha_1 = $('#alpha_1').val();
    		proteine_totale    = $('#proteine_totale').val();
    		if(alpha_1 && proteine_totale){ 
        		var alpha_1_abs = (alpha_1 * proteine_totale)/100;
        		$('#alpha_1_abs').val(alpha_1_abs.toFixed(1));
        	}else{
        		$('#alpha_1_abs').val(null);
        	}
    	}).change( function(){
    		alpha_1 = $('#alpha_1').val();
    		proteine_totale    = $('#proteine_totale').val();
    		if(alpha_1 && proteine_totale){ 
        		var alpha_1_abs = (alpha_1 * proteine_totale)/100;
        		$('#alpha_1_abs').val(alpha_1_abs.toFixed(1));
        	}else{
        		$('#alpha_1_abs').val(null);
        	}
    	});
    	
    	$('#alpha_2, #proteine_totale').keyup( function () {
    		alpha_2 = $('#alpha_2').val();
    		proteine_totale    = $('#proteine_totale').val();
    		if(alpha_2 && proteine_totale){ 
        		var alpha_2_abs = (alpha_2 * proteine_totale)/100;
        		$('#alpha_2_abs').val(alpha_2_abs.toFixed(1));
        	}else{
        		$('#alpha_2_abs').val(null);
        	}
    	}).change( function(){
    		alpha_2 = $('#alpha_2').val();
    		proteine_totale    = $('#proteine_totale').val();
    		if(alpha_2 && proteine_totale){ 
        		var alpha_2_abs = (alpha_2 * proteine_totale)/100;
        		$('#alpha_2_abs').val(alpha_2_abs.toFixed(1));
        	}else{
        		$('#alpha_2_abs').val(null);
        	}
    	});
    	
    	$('#beta_1, #proteine_totale').keyup( function () {
    		beta_1 = $('#beta_1').val();
    		proteine_totale    = $('#proteine_totale').val();
    		if(beta_1 && proteine_totale){ 
        		var beta_1_abs = (beta_1 * proteine_totale)/100;
        		$('#beta_1_abs').val(beta_1_abs.toFixed(1));
        	}else{
        		$('#beta_1_abs').val(null);
        	}
    	}).change( function(){
    		beta_1 = $('#beta_1').val();
    		proteine_totale    = $('#proteine_totale').val();
    		if(beta_1 && proteine_totale){ 
        		var beta_1_abs = (beta_1 * proteine_totale)/100;
        		$('#beta_1_abs').val(beta_1_abs.toFixed(1));
        	}else{
        		$('#beta_1_abs').val(null);
        	}
    	});
    	
    	$('#beta_2, #proteine_totale').keyup( function () {
    		beta_2 = $('#beta_2').val();
    		proteine_totale    = $('#proteine_totale').val();
    		if(beta_2 && proteine_totale){ 
        		var beta_2_abs = (beta_2 * proteine_totale)/100;
        		$('#beta_2_abs').val(beta_2_abs.toFixed(1));
        	}else{
        		$('#beta_2_abs').val(null);
        	}
    	}).change( function(){
    		beta_2 = $('#beta_2').val();
    		proteine_totale    = $('#proteine_totale').val();
    		if(beta_2 && proteine_totale){ 
        		var beta_2_abs = (beta_2 * proteine_totale)/100;
        		$('#beta_2_abs').val(beta_2_abs.toFixed(1));
        	}else{
        		$('#beta_2_abs').val(null);
        	}
    	});
    	
    	$('#gamma, #proteine_totale').keyup( function () {
    		gamma = $('#gamma').val();
    		proteine_totale    = $('#proteine_totale').val();
    		if(gamma && proteine_totale){ 
        		var gamma_abs = (gamma * proteine_totale)/100;
        		$('#gamma_abs').val(gamma_abs.toFixed(1));
        	}else{
        		$('#gamma_abs').val(null);
        	}
    	}).change( function(){
    		gamma = $('#gamma').val();
    		proteine_totale    = $('#proteine_totale').val();
    		if(gamma && proteine_totale){ 
        		var gamma_abs = (gamma * proteine_totale)/100;
        		$('#gamma_abs').val(gamma_abs.toFixed(1));
        	}else{
        		$('#gamma_abs').val(null);
        	}
    	});
    }
    
    function getAsatAlatAuto(){
    	$('#type_materiel_tgo_asat').keyup( function () {
    		var type_materiel_tgo_asat = $('#type_materiel_tgo_asat').val();
    		
    		if(type_materiel_tgo_asat){ 
        		$('#type_materiel_tgp_alat').val(type_materiel_tgo_asat);
        	}else{
        		$('#type_materiel_tgp_alat').val(null);
        	}
    	}).change( function(){
    		var type_materiel_tgo_asat = $('#type_materiel_tgo_asat').val();
    		
    		if(type_materiel_tgo_asat){ 
        		$('#type_materiel_tgp_alat').val(type_materiel_tgo_asat);
        	}else{
        		$('#type_materiel_tgp_alat').val(null);
        	}
    	});
    }
    
    function getAlbumineUrinaireVal(resultat, iddemande){
    	
    	if(resultat == 'positif'){
    		$('.ER_'+iddemande+' #albumine_urinaire_degres').fadeIn(500);
    	}else{
    		$('.ER_'+iddemande+' #albumine_urinaire_degres').toggle(false);
    	}
    	
    }
    
    function getSucreUrinaireVal(resultat, iddemande){
    	
    	if(resultat == 'positif'){
    		$('.ER_'+iddemande+' #sucre_urinaire_degres').fadeIn(500);
    	}else{
    		$('.ER_'+iddemande+' #sucre_urinaire_degres').toggle(false);
    	}
    	
    }
    
    function getCorpsCetoniqueUrinaireVal(resultat, iddemande){
    	
    	if(resultat == 'positif'){
    		$('.ER_'+iddemande+' #corps_cetonique_urinaire_degres').fadeIn(500);
    	}else{
    		$('.ER_'+iddemande+' #corps_cetonique_urinaire_degres').toggle(false);
    	}
    	
    }
    
    function getHemoglobineGlyqueeHbA1cFormule(){
    	
    	var hemoglobine_glyquee_hbac = $('#hemoglobine_glyquee_hbac').val();
    	var valeur_mmol = null;
    	
    	$('#hemoglobine_glyquee_hbac').keyup( function () {
    		hemoglobine_glyquee_hbac = $('#hemoglobine_glyquee_hbac').val();
    		if(hemoglobine_glyquee_hbac){
        		valeur_mmol = (hemoglobine_glyquee_hbac - 2.152) / 0.09148;
        		$('#hemoglobine_glyquee_hbac_mmol').val(valeur_mmol.toFixed(1));
        	}else{
        		$('#hemoglobine_glyquee_hbac_mmol').val(null);
        	}
    	}).change( function(){
    		hemoglobine_glyquee_hbac = $('#hemoglobine_glyquee_hbac').val();
    		if(hemoglobine_glyquee_hbac){
        		valeur_mmol = (hemoglobine_glyquee_hbac - 2.152) / 0.09148;
        		$('#hemoglobine_glyquee_hbac_mmol').val(valeur_mmol.toFixed(1));
        	}else{
        		$('#hemoglobine_glyquee_hbac_mmol').val(null);
        	}
    	});
    	
    }
    
    
    
    
    
    
    
    /**
     * ************************************************
     * ------------------------------------------------
     * ************************************************
     * ------------------------------------------------
     */
    
    //Resultats des analyses d'une seule demande
    //Resultats des analyses d'une seule demande
    //Resultats des analyses d'une seule demande
    function resultatsDesAnalysesDeLaDemande(iddemande, tabAnalyses, tabDemandes){
  	    $( "#resultatsAnalysesDuneDemande" ).dialog({
  	    resizable: false,
  	    height:670,
  	    width:720,
  	    autoOpen: false,
  	    modal: true,
  	    buttons: {
  	    	"Enregistrer": function() {
  	          
  	    		var tab = []; 
  	    		var tabIdDemande = [];
  	    		for(var i = 0 ;  i<tabAnalyses.length ; i++){
  	    			var idanalyse = tabAnalyses[i];
  	    			    tabIdDemande[idanalyse] = tabDemandes[i];
  	    			
  	    			     if(idanalyse ==  1) { tab  [1] = getChampsNfs(); }
    				else if(idanalyse ==  2) { tab  [2] = groupageRhesus(); }
    				else if(idanalyse ==  3) { tab  [3] = antigeneDFaible(); }
    				else if(idanalyse ==  4) { tab  [4] = testCombsDirect(); }
    				else if(idanalyse ==  5) { tab  [5] = testCombsIndirect(); }
       				else if(idanalyse ==  6) { tab  [6] = testCompatibilite(); }
       				else if(idanalyse ==  7) { tab  [7] = vitesseSedimentation(); }
    				else if(idanalyse ==  8) { tab  [8] = testDemmel(); }
    				else if(idanalyse ==  9) { tab  [9] = new Array("", $('#taux_reticulocyte').val(), $('#type_materiel_taux_reticulocytes').val()); }
    				else if(idanalyse == 10) { tab [10] = getGoutteEpaisse(); }
  	    			     
    				else if(idanalyse == 14) { tab [14] = getTpInr(); }
    				else if(idanalyse == 15) { tab [15] = getTca();  }
    				else if(idanalyse == 16) { tab [16] = new Array("", $('#fibrinemie').val(), $('#type_materiel_fibrinemie').val());  }
    				else if(idanalyse == 17) { tab [17] = new Array("", $('#temps_saignement').val(), $('#type_materiel_temps_saignement').val());  }
  	    			    
    				else if(idanalyse == 18) { tab [18] = getFacteur8(); }
    				else if(idanalyse == 19) { tab [19] = getFacteur9(); }
    				else if(idanalyse == 20) { tab [20] = getDDimeres(); }
    				else if(idanalyse == 21) { tab [21] = getGlycemie(); }
    				else if(idanalyse == 22) { tab [22] = new Array("", $('#creatininemie').val(), $('#type_materiel_creatininemie').val()); }
    				else if(idanalyse == 23) { tab [23] = azotemie(); }
    				else if(idanalyse == 24) { tab [24] = new Array("", $('#acide_urique').val(), $('#type_materiel_acide_urique').val(), $('#acide_urique_umol').val()); }
    				else if(idanalyse == 25) { tab [25] = getCholesterolTotal(); }
    				else if(idanalyse == 26) { tab [26] = new Array("", $('#triglycerides_1').val(), $('#triglycerides_2').val(), $('#type_materiel_triglycerides').val()); }
    				else if(idanalyse == 27) { tab [27] = new Array("", $('#cholesterol_HDL_1').val(), $('#cholesterol_HDL_2').val(), $('#type_materiel_cholesterol_HDL').val()); }
    				else if(idanalyse == 28) { tab [28] = new Array("", $('#cholesterol_LDL_1').val(), $('#cholesterol_LDL_2').val(), $('#type_materiel_cholesterol_LDL').val()); }
    				else if(idanalyse == 29) { tab [29] = getChol_Total_HDL_LDL_Trigly(tabIdDemande[29]); }
    				else if(idanalyse == 30) { tab [30] = getLipidesTotaux(); } 
    			    else if(idanalyse == 31) { tab [31] = getIonogramme(); }
    				else if(idanalyse == 32) { tab [32] = new Array("", $('#calcemie').val(), $('#type_materiel_calcemie').val(), $('#calcemie_mmol').val()); } 
    				else if(idanalyse == 33) { tab [33] = new Array("", $('#magnesemie').val(), $('#type_materiel_magnesemie').val()); }
    				else if(idanalyse == 34) { tab [34] = new Array("", $('#phosphoremie').val(), $('#type_materiel_phosphoremie').val(), $('#phosphoremie_mmol').val());  }
    				else if(idanalyse == 35) { tab [35] = getAsat(); }
    				else if(idanalyse == 36) { tab [36] = getAlat(); }
    				else if(idanalyse == 37) { tab [37] = getAsatAlat(); }
    				else if(idanalyse == 38) { tab [38] = phosphatageAlcaline(); }
    				else if(idanalyse == 39) { tab [39] = gamaGT(); }
    				else if(idanalyse == 40) { tab [40] = getFerSerique(); }   
    				else if(idanalyse == 41) { tab [41] = getFerritinine(); } 
    				else if(idanalyse == 42) { tab [42] = getBilirubineTotaleDirecte(); } 
    				else if(idanalyse == 43) { tab [43] = getHemoglobineGlyqueeHBAC(); } 
    				else if(idanalyse == 44) { tab [44] = getElectrophoreseHemoglobine(); }     
    				else if(idanalyse == 45) { tab [45] = getElectrophoreseProteines(); }     
    				else if(idanalyse == 46) { tab [46] = getAlbuminemie(); }    
    				else if(idanalyse == 47) { tab [47] = getAlbumineUrinaire(); } 
    				else if(idanalyse == 48) { tab [48] = getProtidemie(); } 
    				else if(idanalyse == 49) { tab [49] = getProteinurie(); } 
    				else if(idanalyse == 50) { tab [50] = getHlmCompteDaddis(); } 
    				else if(idanalyse == 51) { tab [51] = getBetaHcgPlasmatique(); } 
    				else if(idanalyse == 52) { tab [52] = getPsa(); } 
    				else if(idanalyse == 53) { tab [53] = getCrp(); } 
    				else if(idanalyse == 54) { tab [54] = getFacteursRhumatoides(); } 
    				else if(idanalyse == 55) { tab [55] = getRfWaalerRose(); } 
    				else if(idanalyse == 56) { tab [56] = getToxoplasmose(); } 
    				else if(idanalyse == 57) { tab [57] = getRubeole(); }
    				else if(idanalyse == 58) { tab [58] = /*getCulotUrinaire();*/ getCulotUrinaireListeSelect(); } 
    				else if(idanalyse == 59) { tab [59] = getSerologieChlamydiae(); } 
    				else if(idanalyse == 60) { tab [60] = getSerologieSyphilitique(); } 
    				else if(idanalyse == 61) { tab [61] = getAslo(); } 
    				else if(idanalyse == 62) { tab [62] = getWidal(); } 
    				else if(idanalyse == 63) { tab [63] = getAgHbs(); } 
    				else if(idanalyse == 64) { tab [64] = getHIV(); }
    				else if(idanalyse == 65) { tab [65] = getPV(); }
  	    			     
  	    			     
    				else if(idanalyse == 68) { tab [68] = getTypageHemoglobine(); }
  	    			     
    				else if(idanalyse == 70) { tab [70] = getLDH(); }   
    				else if(idanalyse == 71) { tab [71] = getChampsNfs_TAD(tabDemandes[i]); }
  	    		}
  	    		
  	    		
  	    		
  	        	$( this ).dialog( "close" );
  	            
  	            $.ajax({
  	                type: 'POST',
  	                url: tabUrl[0]+'public/technicien/enregistrer-resultats-demande',
  	                data:{'tabAnalyses':tabAnalyses, 'tabDemandes':tabDemandes, 'tab':tab},
  	                success: function(data) {
  	                	     var iddemande = jQuery.parseJSON(data);
  	                	     $('.visualiser'+iddemande+' img').trigger('click');
  	                }
  	            });
  	        	
  	        },
  	        
  	        "Fermer": function() {
  	        	$(this).dialog( "close" );
  	        }
  	   }
  	  });
    }
    
    
    function resultatsDesAnalyses(iddemande){ 
        var chemin = tabUrl[0]+'public/technicien/recuperer-les-analyses-de-la-demande';
        $.ajax({
            type: 'POST',
            url: chemin ,
            data:'iddemande='+iddemande,
            success: function(data) {
            	     var result = jQuery.parseJSON(data); 

            	     var html = result[0];
            	     var tabAnalyses = result[1];
            	     var tabDemandes = result[2];
            	     resultatsDesAnalysesDeLaDemande(iddemande, tabAnalyses, tabDemandes);
            	     
            	     $('#contenuResultatsAnalysesParType div').empty();
            	     $('#contenuResultatsAnalyses div').empty();
               	     $('#contenuResultatsAnalysesDuneDemande div').empty();
            	     $('#contenuResultatsAnalysesDuneDemande div').html(html);
            	     
            	     rapportCHOL_HDL();
            	     
            	     var scriptFormule;
            	     scriptFormule  = gestionFormuleLeucocytaire_TAD(listeDesDemandesSelect);
            	     scriptFormule += gestionFormuleGlycemie_TAD(listeDesDemandesSelect);
            	     scriptFormule += gestionFormuleTCA_TAD(listeDesDemandesSelect);
            	     scriptFormule += gestionFormuleCreatininemie_TAD(listeDesDemandesSelect);
            	     scriptFormule += gestionFormuleCholesterolTotal_TAD(listeDesDemandesSelect);
            	     scriptFormule += gestionFormuleTriglycerides_TAD(listeDesDemandesSelect);
            	     scriptFormule += gestionFormuleCholesterolHDL_TAD(listeDesDemandesSelect);
            	     scriptFormule += gestionFormuleCholesterolLDL_TAD(listeDesDemandesSelect);
            	     scriptFormule += gestionFormuleRapportCholHdl_TAD(listeDesDemandesSelect);
                 	
            	     $('#scriptFormules').html(scriptFormule);
            	    
            	     getElectrophoreseProteinesFormule();
            	     getAsatAlatAuto();
            	     getFerSeriqueFormule();
            	     getAzotemieFormule();
            	     getAcideUriqueFormule();
            	     getCalcemieFormule();
            	     getPhosphoremieFormule();
            	     getAlbuminemieFormule();
            	     getProteinurieFormule();
            	     
            	     ajoutCulotUrinaireAuto();
            	     getBilirubineTotaleDirecteAuto();
            	     getHemoglobineGlyqueeHbA1cFormule();
            	     
            	     //Ajouter des lignes
            	     getTestCombsIndirectAjout();
            	     getElectroHemo();
            	     
            	     $("#resultatsAnalysesDuneDemande").dialog('open');
            }
        });
    }
    
    
    /**
     * AJOUTER DE PLUSIEURS RESULTAT PAR LES '+' & '-'
     * AJOUTER DE PLUSIEURS RESULTAT PAR LES '+' & '-'
     * AJOUTER DE PLUSIEURS RESULTAT PAR LES '+' & '-'
     */
    function getTestCombsIndirectAjout(){ 
    	
    	$('#test_combs_indirect_moins').toggle(false);
	    
    	$('#test_combs_indirect_plus').click(function(){
	    	var nbLigne = $("#test_combs_rai tr").length;
	    	$('#test_combs_indirect_moins').toggle(true);
	    	
	    	if(nbLigne < 10){
	    		var html ="<tr id='test_combs_rai_"+nbLigne+"' class='ligneAnanlyse' style='width: 100%;'>"+
                            
                            "<td style='width: 30%;'><label class='lab1' ><span style='font-weight: bold;'> RAI <select id='test_combs_indirect_"+nbLigne+"' > <option >  </option> <option value='Positif' >Positif</option> <option value='Negatif' >N&eacute;gatif</option> </select></span></label></td>"+
                    	    "<td style='width: 25%;'><label class='lab2' style='padding-top: 5px; text-align: right; '>  Titre <input id='titre_combs_indirect_"+nbLigne+"' type='text'> </label></td>"+
                    	    "<td style='width: 45%;'><label class='lab3' style='padding-top: 5px; width: 80%; padding-left: 25px;'> Temp&eacute;rature <input id='titre_combs_temperature_"+nbLigne+"' type='number' > </label></td>"+
                            
                          "</tr>";

		    	$('#test_combs_rai_'+(nbLigne-1)).after(html);
		    	$('#test_combs_indirect_'+nbLigne).val($('#test_combs_indirect_'+(nbLigne-1)).val());
		    	
		    	if(nbLigne == 9){
		    		$('#test_combs_indirect_plus').toggle(false);
		    	}
		    	
		    	//Blocage du champ titre lorsque la valeur est n�gative
			    $('#test_combs_indirect_'+nbLigne).attr('onchange', 'getTestCombsIndirectBlocTitre('+nbLigne+')');
			    if($('#test_combs_indirect_'+nbLigne).val() == 'Negatif'){ $('#test_combs_indirect_'+nbLigne).trigger('change'); }
	    	}

	    });
	    
	    $('#test_combs_indirect_moins').click(function(){ 
	    	var nbLigne = $("#test_combs_rai tr").length;
	    	
	    	if(nbLigne > 2){
		    	$('#test_combs_rai_'+(nbLigne-1)).remove();
		    	if(nbLigne == 3){ 
		    		$('#test_combs_indirect_moins').toggle(false);
		    	}
		    	
		    	if(nbLigne == 10){
		    		$('#test_combs_indirect_plus').toggle(true);
		    	}
	    	}

	    });
	    
	    
    }
    
    function testCombsIndirect(){
    	var tab = [];
    	var nbLigne = $("#test_combs_rai tr").length;
    	var j = 1;
    	
    	tab[0] = $('#type_materiel_test_combs_indirect').val();
    	tab[1] = new Array(); 
    	tab[2] = new Array(); 
    	tab[3] = new Array(); 
    	for(var i=1 ; i<nbLigne ; i++){
    		var test  = $('#test_combs_indirect_'+i ).val();
    		var titre = $('#titre_combs_indirect_'+i).val();
    		var temperature = $('#titre_combs_temperature_'+i).val();
    		if(test){
        		tab[1][j]   = test;
        		tab[2][j] = titre;
        		tab[3][j++] = temperature;
    		}
    	}
	    tab[4] = $('#commentaire_test_combs_indirect').val();
	    
	    return tab;
    
    }
    
    
    function getTestCombsIndirectBlocTitre(nbLigne){
    	
    	var val = $('#test_combs_indirect_'+nbLigne).val();
    	
    	if(val == 'Negatif'){
    		$('#titre_combs_indirect_'+nbLigne).val('').attr('readonly',true);
    	}else{
    		$('#titre_combs_indirect_'+nbLigne).attr('readonly',false);
    	}
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    /**GESTION DE L'INTERFACE D'ENREGISTREMENT MULTIPLE DES RESULTATS D'ANALYSES **/
    /**GESTION DE L'INTERFACE D'ENREGISTREMENT MULTIPLE DES RESULTATS D'ANALYSES **/
    /**GESTION DE L'INTERFACE D'ENREGISTREMENT MULTIPLE DES RESULTATS D'ANALYSES **/
    /**GESTION DE L'INTERFACE D'ENREGISTREMENT MULTIPLE DES RESULTATS D'ANALYSES **/
    /**GESTION DE L'INTERFACE D'ENREGISTREMENT MULTIPLE DES RESULTATS D'ANALYSES **/
    
    function enregistrementResultatsAnalyses_TAD(idanalyse, iddemande, tab){

    	$(".examenSaveTick_"+iddemande).html('<img style="margin-left: 10px; width: 16px; height: 16px;" src="../images/loading/Chargement_1.gif">');
    	$.ajax({
			type: 'POST',
			url: tabUrl[0]+'public/technicien/enregistrer-resultat',
			data: {'idanalyse':idanalyse, 'iddemande':iddemande, 'tab':tab},
			success: function(data) {
				var result = jQuery.parseJSON(data);  
				var resultatExiste = result[1];
				
				if(resultatExiste == 1){
					$(".examenSaveTick_"+iddemande).html("<img style='margin-left: 10px;'  src='../images_icons/tick_16.png'>");
				}else{
					$(".examenSaveTick_"+iddemande).html("");
				}
				
			}
		});
    	
    }
    
    
    
    
    
    
    
    
    
    
    var listeDemandesSelectionnees;
    var listeAnalysesSelectionnees;
    /**
     * RECUPERER LES DONNEES POUR L'ENREGISTREMENT 
     * id = iddemande
     */
    /* Analyse_1
     * tamponChampsNfs
     */
    var tamponChampsNfsTab = new Array();
    
    function getChampsNfs_TAD(id){
    	var tab = new Array();
    	var i;
    	for(i = 1 ; i <= 25 ; i++){
    		if($('.ER_'+id+' #champ'+i).val()){ tab[i] = $('.ER_'+id+' #champ'+i).val(); }
    		else { tab[i] = null; }
    	}
    	tab[i] = $('.ER_'+id+' #type_materiel_nfs').val();
    	tab[i+1] = $('.ER_'+id+' #commentaire_hemogramme').val();
    	
    	return tab;
    }
    
    /* Analyse_2
     * tamponGroupageRhesus
     */
    var tamponGroupageRhesusTab = new Array();
    
    function getGroupageRhesus_TAD(id){
    	var tab = new Array();
    	tab[1] = $('.ER_'+id+' #groupe').val(); 
		tab[2] = $('.ER_'+id+' #rhesus').val();
		tab[3] = $('.ER_'+id+' #type_materiel_gsrh_groupage').val();
    	
    	return tab;
    }

    /*
     * Analyse_3
     * tamponAntigeneDFaible
     */
    var tamponAntigeneDFaible = new Array();
    
    function getAntigeneDFaible_TAD(id){
    	var tab = new Array();
    	tab[1] = $('.ER_'+id+' #antigene_d_faible').val(); 
		tab[2] = $('.ER_'+id+' #type_materiel_recherche_antigene').val();
		tab[3] = $('.ER_'+id+' #conclusion_antigene_d_faible').val();
    	
    	return tab;
    }
    
    /*
     * Analyse_4
     * tamponTestCombsDirect
     */
    var tamponTestCombsDirect = new Array();
    
    function getTestCombsDirect_TAD(id){
    	var tab = [];
    	tab[1] = $('.ER_'+id+' #test_combs_direct').val(); 
		tab[2] = $('.ER_'+id+' #titre_combs_direct').val();
		tab[3] = $('.ER_'+id+' #type_materiel_test_combs_direct').val();
    	
    	return tab;
    }
    
    /*
     * Analyse_5
     * tamponTestCombsIndirect
     */
    var tamponTestCombsIndirect = new Array();
    
    function getTestCombsIndirect_TAD(id){
    	var tab = [];
    	tab[1] = $('.ER_'+id+' #test_combs_indirect').val(); 
		tab[2] = $('.ER_'+id+' #titre_combs_indirect').val();
		tab[3] = $('.ER_'+id+' #type_materiel_test_combs_indirect').val();
    	
    	return tab;
    }
    
    /*
     * Analyse_6
     * tamponTestCompatibilite
     */
    var tamponTestCompatibilite = new Array();
    
    function getTestCompatibilite_TAD(id){
    	var tab = new Array();
    	tab[1] = $('.ER_'+id+' #test_compatibilite').val(); 
		tab[2] = $('.ER_'+id+' #titre_test_compatibilite').val();
		tab[3] = $('.ER_'+id+' #type_materiel_test_compatibilite').val();
    	
    	return tab;
    }
    
    /*
     * Analyse_7
     * tamponVitesseSedimentation
     */
    var tamponVitesseSedimentation = new Array();
    
    function getVitesseSedimentation_TAD(id){
    	var tab = [];
    	tab[1] = $('.ER_'+id+' #vitesse_sedimentation').val(); 
		tab[2] = $('.ER_'+id+' #type_materiel_vitesse_sedimentation').val();
		tab[3] = $('.ER_'+id+' #vitesse_sedimentation_2').val(); 
    	
    	return tab;
    }
    
    /*
     * Analyse_8
     * tamponTestDemmel
     */
    var tamponTestDemmel = new Array();
    
    function getTestDemmel_TAD(id){
    	var tab = [];
    	tab[1] = $('.ER_'+id+' #test_demmel').val(); 
		tab[2] = $('.ER_'+id+' #type_materiel_test_demmel').val();
    	
    	return tab;
    }
    
    /*
     * Analyse_9
     * tamponTauxReticulocytes
     */
    var tamponTauxReticulocytes = new Array();
    
    function getTauxReticulocytes_TAD(id){
    	var tab = new Array();
    	tab[1] = $('.ER_'+id+' #taux_reticulocyte').val(); 
    	tab[2] = $('.ER_'+id+' #type_materiel_taux_reticulocytes').val(); 

    	return tab;
    }
    
    /*
     * Analyse_10
     * tamponGoutteEpaisse
     */
    var tamponGoutteEpaisse = new Array();
    
    function getGoutteEpaisse_TAD(id){
    	var tab = new Array();
    	tab[1] = $('.ER_'+id+' #goutte_epaisse').val();
    	tab[2] = $('.ER_'+id+' #densite_parasitaire').val();
    	tab[3] = $('.ER_'+id+' #type_materiel_goutte_epaisse').val();
    	tab[4] = $('.ER_'+id+' #commentaire_goutte_epaisse').val();
    	
    	return tab;
    }
    
    
    /*
     * Analyse_14
     * tamponTpInr
     */
    var tamponTpInr = new Array();
    
    function getTpInr_TAD(id){
    	var tab = new Array();
    	
    	tab[1] = $('.ER_'+id+' #temps_quick_temoin').val(); 
    	tab[2] = $('.ER_'+id+' #temps_quick_patient').val(); 
    	tab[3] = $('.ER_'+id+' #taux_prothrombine_patient').val(); 
    	tab[4] = $('.ER_'+id+' #inr_patient').val();
    	tab[5] = $('.ER_'+id+' #type_materiel_tp_inr').val();
    	
    	return tab;
    }
    
    /*
     * Analyse_15
     * tamponTca
     */
    var tamponTca = new Array();
    
    function getTca_TAD(id){
    	var tab = new Array();
    	
    	tab[1] = $('.ER_'+id+' #tca_patient').val(); 
    	tab[2] = $('.ER_'+id+' #temoin_patient').val();
    	tab[3] = $('.ER_'+id+' #type_materiel_tca').val();
    	
    	return tab;
    }
    
    /*
     * Analyse_16
     * tamponFibrinemie
     */
    var tamponFibrinemie = new Array();
    
    function getFibrinemie_TAD(id){
    	var tab = new Array();

    	tab[1] = $('.ER_'+id+' #fibrinemie').val(); 
    	tab[2] = $('.ER_'+id+' #type_materiel_fibrinemie').val(); 
    	
    	return tab;
    }
    
    /*
     * Analyse_17
     * tamponTempsSaignement
     */
    var tamponTempsSaignement = new Array();
    
    function getTempsSaignement_TAD(id){
    	var tab = new Array();

    	tab[1] = $('.ER_'+id+' #temps_saignement').val(); 
    	tab[2] = $('.ER_'+id+' #type_materiel_temps_saignement').val();  
    	
    	return tab;
    }
    
    /*
     * Analyse_18
     * tamponFacteur8
     */
    var tamponFacteur8 = new Array();
    
    function getFacteur8_TAD(id){
    	var tab = new Array();
    	
    	tab[1] = $('.ER_'+id+' #facteur_8').val(); 
    	tab[2] = $('.ER_'+id+' #type_materiel_facteur_8').val(); 
    	
    	return tab;
    }
    
    /*
     * Analyse_19
     * tamponFacteur9
     */
    var tamponFacteur9 =  new Array();
    
    function getFacteur9_TAD(id){
    	var tab = new Array();
    	
    	tab[1] = $('.ER_'+id+' #facteur_9').val(); 
    	tab[2] = $('.ER_'+id+' #type_materiel_facteur_9').val(); 
    	
    	return tab;
    }
    
    /*
     * Analyse_20
     * tamponDDimeres
     */
    var tamponDDimeres = new Array();
    
    function getDDimeres_TAD(id){
    	var tab = new Array();
    	
    	tab[1] = $('.ER_'+id+' #d_dimeres').val();
    	tab[2] = $('.ER_'+id+' #type_materiel_dimeres').val();
    	
    	return tab;
    }
    
    /*
     * Analyse_21
     * tamponGlycemie 
     */
    var tamponGlycemie = new Array();
    
    function getGlycemie_TAD(id){
    	var tab = new Array();
    	
    	tab[1] = $('.ER_'+id+' #glycemie_1').val(); 
    	tab[2] = $('.ER_'+id+' #glycemie_2').val();
    	tab[3] = $('.ER_'+id+' #type_materiel_glycemie').val();
    	
    	return tab;
    }
    
    /*
     * Analyse_22
     * tamponCreatininemie
     */
    var tamponCreatininemie = new Array();
    
    function getCreatininemie_TAD(id){
    	var tab = new Array();

    	tab[1] = $('.ER_'+id+' #creatininemie').val(); 
    	tab[2] = $('.ER_'+id+' #type_materiel_creatininemie').val(); 
    	
    	return tab;
    }
    
    /*
     * Analyse_23
     * tamponAzotemie
     */
    var tamponAzotemie = new Array();
    
    function getAzotemie_TAD(id){
    	var tab = new Array();
    	
    	tab[1] = $('.ER_'+id+' #uree_sanguine').val(); 
		tab[2] = $('.ER_'+id+' #type_materiel_azotemie').val();
		tab[3] = $('.ER_'+id+' #uree_sanguine_mmol').val();
    	
    	return tab;
    }
    
    /*
     * Analyse_24
     * tamponAcideUrique
     */
    var tamponAcideUrique = new Array();
    
    function getAcideUrique_TAD(id){
    	var tab = new Array();
    	
    	tab[1] = $('.ER_'+id+' #acide_urique').val(); 
    	tab[2] = $('.ER_'+id+' #type_materiel_acide_urique').val(); 
    	tab[3] = $('.ER_'+id+' #acide_urique_umol').val(); 
    	
    	return tab;
    }
    
    /*
     * Analyse_25
     * tamponCholesterolTotal
     */
    var tamponCholesterolTotal = new Array();
    
    function getCholesterolTotal_TAD(id){
    	var tab = new Array();
    	
    	tab[1] = $('.ER_'+id+' #cholesterol_total_1').val(); 
    	tab[2] = $('.ER_'+id+' #cholesterol_total_2').val();
    	tab[3] = $('.ER_'+id+' #type_materiel_cholesterol_total').val();
    	
    	return tab;
    }
    
    /*
     * Analyse_26
     * tamponTriglycerides
     */
    var tamponTriglycerides = new Array();
    
    function getTriglycerides_TAD(id){
    	var tab = new Array();
    	
    	tab[1] = $('.ER_'+id+' #triglycerides_1').val(); 
    	tab[2] = $('.ER_'+id+' #triglycerides_2').val();
    	tab[3] = $('.ER_'+id+' #type_materiel_triglycerides').val();
    	
    	return tab;
    }
    
    /*
     * Analyse_27
     * tamponCholesterolHDL
     */
    var tamponCholesterolHDL = new Array();
    
    function getCholesterolHDL_TAD(id){
    	var tab = new Array();
    	
    	tab[1] = $('.ER_'+id+' #cholesterol_HDL_1').val(); 
    	tab[2] = $('.ER_'+id+' #cholesterol_HDL_2').val();
    	tab[3] = $('.ER_'+id+' #type_materiel_cholesterol_HDL').val();
    	
    	return tab;
    }
    
    /*
     * Analyse_28
     * tamponCholesterolLDL
     */
    var tamponCholesterolLDL = new Array();
    
    function getCholesterolLDL_TAD(id){
    	var tab = new Array();
    	
    	tab[1] = $('.ER_'+id+' #cholesterol_LDL_1').val(); 
    	tab[2] = $('.ER_'+id+' #cholesterol_LDL_2').val();
    	tab[3] = $('.ER_'+id+' #type_materiel_cholesterol_LDL').val();
    	
    	return tab;
    }
    
    /*
     * Analyse_29
     * tamponChol_Total_HDL_LDL_Trigly
     */
    var tamponChol_Total_HDL_LDL_Trigly = new Array();
    
    function getChol_Total_HDL_LDL_Trigly_TAD(id){
    	var tab = new Array();
    	
	    tab[1] = $('.ER_'+id+' #cholesterol_total_1').val(); tab[2] = $('.ER_'+id+' #cholesterol_total_2').val(); tab[9]  = $('.ER_'+id+' #type_materiel_cholesterol_total').val(); 
	    tab[3] = $('.ER_'+id+' #cholesterol_HDL_1').val();   tab[4] = $('.ER_'+id+' #cholesterol_HDL_2').val();   tab[10] = $('.ER_'+id+' #type_materiel_cholesterol_HDL').val(); 
	    tab[5] = $('.ER_'+id+' #cholesterol_LDL_1').val();   tab[6] = $('.ER_'+id+' #cholesterol_LDL_2').val();   tab[11] = $('.ER_'+id+' #type_materiel_cholesterol_LDL').val(); 
	    tab[7] = $('.ER_'+id+' #triglycerides_1').val();     tab[8] = $('.ER_'+id+' #triglycerides_2').val();     tab[12] = $('.ER_'+id+' #type_materiel_triglycerides').val(); 
		
	    return tab;
    }
    
    /*
     * Analyse_30
     * tamponLipidesTotaux
     */
    var tamponLipidesTotaux = new Array();
    

    function getLipidesTotaux_TAD(id){
    	var tab = new Array();
    	
    	tab[1] = $('.ER_'+id+' #type_materiel_lipides_totaux').val();
    	tab[2] = $('.ER_'+id+' #lipides_totaux').val();
    	
    	return tab;
    }
    
    /*
     * Analyse_31
     * tamponIonogramme
     */
    var tamponIonogramme = new Array();
    
    function getIonogramme_TAD(id){
    	var tab = new Array();
    	
    	tab[1] = $('.ER_'+id+' #sodium_sanguin').val(); 
		tab[2] = $('.ER_'+id+' #potassium_sanguin').val();
		tab[3] = $('.ER_'+id+' #chlore_sanguin').val();
		tab[4] = $('.ER_'+id+' #type_materiel_ionogramme').val();
    	
    	return tab;
    }
    
    /*
     * Analyse_32
     * tamponCalcemie
     */
    var tamponCalcemie = new Array();
    
    function getCalcemie_TAD(id){
    	var tab = new Array();
    	
    	tab[1] = $('.ER_'+id+' #calcemie').val();
    	tab[2] = $('.ER_'+id+' #type_materiel_calcemie').val();
    	tab[3] = $('.ER_'+id+' #calcemie_mmol').val();
    	
    	return tab;
    }
    
    /*
     * Analyse_33
     * tamponMagnesemie
     */
    var tamponMagnesemie = new Array();
    
    function getMagnesemie_TAD(id){
    	var tab = new Array();
    	
    	 tab[1] = $('.ER_'+id+' #magnesemie').val();
    	 tab[2] = $('.ER_'+id+' #type_materiel_magnesemie').val(); 
    	 
    	 return tab;
    }
    
    /*
     * Analyse_34
     * tamponPhosphoremie
     */
    var tamponPhosphoremie = new Array();
    
    function getPhosphoremie_TAD(id){
    	var tab = new Array();
    	 
    	tab[1] = $('.ER_'+id+' #phosphoremie').val();
    	tab[2] = $('.ER_'+id+' #type_materiel_phosphoremie').val(); 

    	return tab;
    }
    
    /*
     * Analyse_35
     * tamponTgoAsat
     */
    var tamponTgoAsat = new Array();
    
    function getTgoAsat_TAD(id){
    	var tab = new Array();
    	
	    tab[1] = $('.ER_'+id+' #type_materiel_tgo_asat').val();
	    tab[2] = $('.ER_'+id+' #tgo_asat').val();
	    
	    return tab;
    }
    
    /*
     * Analyse_36
     * tamponTgpAlat
     */
    var tamponTgpAlat = new Array();
    
    function getTgpAlat_TAD(id){
    	var tab = new Array();
    	
    	tab[1] = $('.ER_'+id+' #type_materiel_tgp_alat').val();
    	tab[2] = $('.ER_'+id+' #tgp_alat').val();
	    
	    return tab;
    }
    
    /*
     * Analyse_37
     * tamponAsatAlat
     */
    var tamponAsatAlat = new Array();
    
    function getAsatAlat_TAD(id){
    	var tab = new Array();
    	
    	tab[1] = $('.ER_'+id+' #type_materiel_tgp_alat').val();
    	tab[2] = $('.ER_'+id+' #tgp_alat').val();
	    tab[3] = $('.ER_'+id+' #type_materiel_tgo_asat').val();
	    tab[4] = $('.ER_'+id+' #tgo_asat').val();
	    
	    return tab;
    }
    
    /*
     * Analyse_38
     * tamponPhosphatageAlcaline
     */
    var tamponPhosphatageAlcaline = new Array();
    
    function getPhosphatageAlcaline_TAD(id){
    	var tab = new Array();
    	
    	tab[1] = $('.ER_'+id+' #phosphatage_alcaline').val(); 
    	tab[2] = $('.ER_'+id+' #type_materiel_phosphatage_alcaline').val();
	    
    	return tab;
    }
    
    /*
     * Analyse_39
     * tamponGamaGT
     */
    var tamponGamaGT = new Array();
    
    function getGamaGT_TAD(id){
    	var tab = new Array();
    	
    	tab[1] = $('.ER_'+id+' #gama_gt').val(); 
    	tab[2] = $('.ER_'+id+' #type_materiel_gama_gt_ygt').val();
	    
    	return tab;
    }
    
    /*
     * Analyse_40
     * tamponFerserique
     */
    var tamponFerSerique = new Array();
    
    function getFerSerique_TAD(id){
    	var tab = new Array();
    	
    	tab[1] = $('.ER_'+id+' #fer_serique_ug').val();
    	tab[2] = $('.ER_'+id+' #fer_serique_umol').val();
    	tab[3] = $('.ER_'+id+' #type_materiel_fer_serique').val();
    	
    	return tab;
    }
    
    /*
     * Analyse_41
     * tamponFerritinine
     */
    var tamponFerritinine = new Array();
    
    function getFerritinine_TAD(id){
    	var tab = new Array();
    	
    	tab[1] = $('.ER_'+id+' #type_materiel_ferritinine').val();
    	tab[2] = $('.ER_'+id+' #ferritinine').val();
	    
	    return tab;
    }
   
    /*
     * Analyse_42
     * tamponBilirubineTotaleDirecte
     */
    var tamponBilirubineTotalDirecte = new Array();
    
    function getBilirubineTotaleDirecte_TAD(id){
    	var tab = new Array();
    	
    	tab[1] = $('.ER_'+id+' #type_materiel_bilirubine_totale_directe').val();
    	tab[2] = $('.ER_'+id+' #bilirubine_totale').val();
    	tab[3] = $('.ER_'+id+' #bilirubine_totale_auto').val();
    	tab[4] = $('.ER_'+id+' #bilirubine_directe').val();
    	tab[5] = $('.ER_'+id+' #bilirubine_directe_auto').val();
    	tab[6] = $('.ER_'+id+' #bilirubine_indirecte').val();
    	tab[7] = $('.ER_'+id+' #bilirubine_indirecte_auto').val();
	    
	    return tab;
    }
    
    /*
     * Analyse_43
     * tamponHemoglobineGlyqueeHBAC
     */
    var tamponHemoglobineGlyqueeHBAC = new Array();
    
    function getHemoglobineGlyqueeHBAC_TAD(id){
    	var tab = new Array();
    	
    	tab[1] = $('.ER_'+id+' #type_materiel_hemo_gly_hbac').val();
    	tab[2] = $('.ER_'+id+' #hemoglobine_glyquee_hbac').val();
    	tab[3] = $('.ER_'+id+' #hemoglobine_glyquee_hbac_mmol').val();
	    
	    return tab;
    }
    
    /*
     * Analyse_44
     * tamponElectrophoreseHemoglobine
     */
    var tamponElectrophoreseHemoglobine = new Array();
    
    function getElectrophoreseHemoglobine_TAD(id){
    	var tab = new Array();
    	
    	var nbLigne = $('.ER_'+id+' #electro_hemo tr').length;
    	var j = 1;
    	
    	tab[0] = $('.ER_'+id+' #type_materiel_electro_hemo').val();
    	tab[1] = new Array(); 
    	tab[2] = new Array(); 
    	for(var i=1 ; i<nbLigne ; i++){
    		var label  = $('.ER_'+id+' #electro_hemo_label_'+i ).val();
    		var valeur = $('.ER_'+id+' #electro_hemo_valeur_'+i).val();
    		if(label && valeur){
        		tab[1][j]   = label;
        		tab[2][j++] = valeur;
    		}
    	}
	    
    	tab[3] =  $('.ER_'+id+' #conclusion_electro_hemo_valeur').val(); 
    	
	    return tab;
    }
    
    /*
     * Analyse_45
     * tamponElectrophoreseProteine
     */
    var tamponElectrophoreseProteine = new Array();
    
    function getElectrophoreseProteine_TAD(id){
    	var tab = new Array();
    	
    	tab[1]   = $('.ER_'+id+' #type_materiel_electro_proteine').val();
    	tab[2]   = $('.ER_'+id+' #albumine').val();
    	tab[3]   = $('.ER_'+id+' #albumine_abs').val();
    	tab[4]   = $('.ER_'+id+' #alpha_1').val();
    	tab[5]   = $('.ER_'+id+' #alpha_1_abs').val();
    	tab[6]   = $('.ER_'+id+' #alpha_2').val();
    	tab[7]   = $('.ER_'+id+' #alpha_2_abs').val();
    	tab[8]   = $('.ER_'+id+' #beta_1').val();
    	tab[9]   = $('.ER_'+id+' #beta_1_abs').val();
    	tab[10]  = $('.ER_'+id+' #beta_2').val();
    	tab[11]  = $('.ER_'+id+' #beta_2_abs').val();
    	tab[12]  = $('.ER_'+id+' #gamma').val();
    	tab[13]  = $('.ER_'+id+' #gamma_abs').val();
    	tab[14]  = $('.ER_'+id+' #proteine_totale').val();
    	tab[15]  = $('.ER_'+id+' #commentaire_electrophorese_proteine').val();
    	
    	return tab;
    }
    
    /*
     * Analyse_46
     * tamponAlbuminemie
     */
    var tamponAlbuminemie = new Array();
    
    function getAlbuminemie_TAD(id){
    	var tab = new Array();
    	
    	tab[1] = $('.ER_'+id+' #type_materiel_albuminemie').val();
    	tab[2] = $('.ER_'+id+' #albuminemie').val();
    	tab[3] = $('.ER_'+id+' #albuminemie_umol').val();
	    
	    return tab;
    }
    
    /*
     * Analyse_47
     * tamponAlbumineUrinaire
     */
    var tamponAlbumineUrinaire = new Array();

    function getAlbumineUrinaire_TAD(id){
    	var tab = new Array();
    	
    	tab[1] = $('.ER_'+id+' #type_materiel_albumine_urinaire').val();
    	
    	var albumine_urinaire = $('.ER_'+id+' #albumine_urinaire').val();
    	tab[2] = albumine_urinaire;
    	tab[3] = null;
    	if(albumine_urinaire == 'positif'){ tab[3] = $('.ER_'+id+' #albumine_urinaire_degres').val(); }
    	
    	var sucre_urinaire = $('.ER_'+id+' #sucre_urinaire').val();
    	tab[4] = sucre_urinaire;
    	tab[5] = null;
    	if(sucre_urinaire == 'positif'){ tab[5] = $('.ER_'+id+' #sucre_urinaire_degres').val(); }
    	
    	var corps_cetonique_urinaire = $('.ER_'+id+' #corps_cetonique_urinaire').val();
    	tab[6] = corps_cetonique_urinaire;
    	tab[7] = null;
    	if(corps_cetonique_urinaire == 'positif'){ tab[7] = $('.ER_'+id+' #corps_cetonique_urinaire_degres').val(); }
	    
	    return tab;
    }
    
    /*
     * Analyse_48
     * tamponProtidemie
     */
    var tamponProtidemie = new Array();
    
    function getProtidemie_TAD(id){
    	var tab = new Array();
    	
    	tab[1] = $('.ER_'+id+' #type_materiel_protidemie').val();
    	tab[2] = $('.ER_'+id+' #protidemie').val();
	    
	    return tab;
    }
    
    /*
     * Analyse_49
     * tamponProteinurie
     */
    var tamponProteinurie = new Array();
    
    function getProteinurie_TAD(id){
    	var tab = new Array();
    	
    	tab[1] = $('.ER_'+id+' #type_materiel_proteinurie').val();
    	tab[2] = $('.ER_'+id+' #proteinurie_1').val();
    	tab[3] = $('.ER_'+id+' #proteinurie_2').val();
    	tab[4] = $('.ER_'+id+' #proteinurie_g24h').val();
	    
	    return tab;
    }
    
    /*
     * Analyse_50
     * tamponHlmCompteDaddis
     */
    var tamponHlmCompteDaddis = new Array();
    
    function getHlmCompteDaddis_TAD(id){
    	var tab = new Array();
    	
    	tab[1] = $('.ER_'+id+' #type_materiel_hlm_compte_daddis').val();
    	tab[2] = $('.ER_'+id+' #hematies_hlm').val();
    	tab[3] = $('.ER_'+id+' #leucocytes_hlm').val();
    	tab[4] = $('.ER_'+id+' #commentaire_hlm_compte_daddis').val();
	    
	    return tab;
    }
    
    /*
     * Analyse_51
     * tamponBetaHcgPlasmatique
     */
    var tamponBetaHcgPlasmatique = new Array();
    
    function getBetaHcgPlasmatique_TAD(id){
    	var tab = new Array();
    	
    	tab[1] = $('.ER_'+id+' #type_materiel_beta_hcg').val();
    	tab[2] = $('.ER_'+id+' #beta_hcg_plasmatique').val();
	    
	    return tab;
    }
    
    /*
     * Analyse_52
     * tamponPsa
     */
    var tamponPsa = new Array();

    function getPsa_TAD(id){
    	var tab = new Array();
    	
    	tab[1] = $('.ER_'+id+' #type_materiel_psa').val();
    	tab[2] = $('.ER_'+id+' #psa').val();
    	tab[3] = $('.ER_'+id+' #psa_qualitatif').val();
	    
	    return tab;
    }
    
    /*
     * Analyse_53
     * tamponCrp
     */
    var tamponCrp = new Array();

    function getCrp_TAD(id){
    	var tab = new Array();
    	
    	tab[1] = $('.ER_'+id+' #type_materiel_crp').val();
    	tab[2] = $('.ER_'+id+' #optionResultatCrp').val();
    	tab[3] = $('.ER_'+id+' #crpValeurResultat').val();
    	
	    return tab;
    }
    
    /*
     * Analyse_54
     * tamponFacteursRhumatoides
     */
    var tamponFacteurRhumatoides = new Array();

    function getFacteursRhumatoides_TAD(id){
    	var tab = new Array();
    	
    	tab[1] = $('.ER_'+id+' #type_materiel_facteurs_rhumatoides').val();
    	tab[2] = $('.ER_'+id+' #facteurs_rhumatoides').val();
    	tab[3] = $('.ER_'+id+' #facteurs_rhumatoides_titre').val();
	    
	    return tab;
    }
    
    /*
     * Analyse_55
     * tamponRfWaalerRose
     */
    var tamponRfWaalerRose = new Array();

    function getRfWaalerRose_TAD(id){
    	var tab = new Array();
    	
    	tab[1] = $('.ER_'+id+' #type_materiel_rf_waaler_rose').val();
    	tab[2] = $('.ER_'+id+' #rf_waaler_rose').val();
    	tab[3] = $('.ER_'+id+' #rf_waaler_rose_titre').val();
	    
	    return tab;
    }
    
    /*
     * Analyse_56
     * tamponToxoplasmose
     */
    var tamponToxoplasmose = new Array();
    
    function getToxoplasmose_TAD(id){
    	var tab = new Array();
    	
    	tab[1] = $('.ER_'+id+' #type_materiel_toxoplasmose').val();
    	tab[2] = $('.ER_'+id+' #toxoplasmose_igm').val();
    	tab[3] = $('.ER_'+id+' #toxoplasmose_igm_titre').val();
    	tab[4] = $('.ER_'+id+' #toxoplasmose_igg').val();
    	tab[5] = $('.ER_'+id+' #toxoplasmose_igg_titre').val();
    	
	    return tab;
    }
    
    /*
     * Analyse_57
     * tamponRubeole
     */
    var tamponRubeole = new Array();

    function getRubeole_TAD(id){
    	var tab = new Array();
    	
    	tab[1] = $('.ER_'+id+' #type_materiel_rubeole').val();
    	tab[2] = $('.ER_'+id+' #rubeole_igm').val();
    	tab[3] = $('.ER_'+id+' #rubeole_igm_titre').val();
    	tab[4] = $('.ER_'+id+' #rubeole_igg').val();
    	tab[5] = $('.ER_'+id+' #rubeole_igg_titre').val();
    	
	    return tab;
    }
    
    /*
     * Analyse_58
     * tamponCulotUrinaire
     */
    var tamponCulotUrinaire = new Array();

    function getCulotUrinaire_TAD(id){
    	var tab = new Array();
    	
    	tab[1] = $('.ER_'+id+' #type_materiel_culot_urinaire').val();
    	tab[2] = $('.ER_'+id+' #culot_urinaire_1').val();
    	tab[3] = $('.ER_'+id+' #culot_urinaire_2').val();
	    
	    return tab;
    }
    
    /*
     * Analyse_59
     * tamponSerologieChlamydiae
     */
    var tamponSerologieChlamydiae = new Array();
    
    function getSerologieChlamydiae_TAD(id){
    	var tab = new Array();
    	
    	tab[1] = $('.ER_'+id+' #type_materiel_serologie_chlamydiae').val();
    	tab[2] = $('.ER_'+id+' #serologie_chlamydiae').val();
	    
	    return tab;
    }
    
    /*
     * Analyse_60
     * tamponSerologieSyphilitique
     */
    var tamponSerologieSyphilitique = new Array();

    function getSerologieSyphilitique_TAD(id){
    	var tab = new Array();
    	
    	tab[1] = $('.ER_'+id+' #type_materiel_serologie_syphilitique').val();
    	tab[2] = $('.ER_'+id+' #serologie_syphilitique_rpr').val();
    	tab[3] = $('.ER_'+id+' #serologie_syphilitique_tpha').val();
    	tab[4] = $('.ER_'+id+' #serologie_syphilitique_tpha_titre').val();
	    
	    return tab;
    }
    
    /*
     * Analyse_61
     * tamponAslo
     */
    var tamponAslo = new Array();

    function getAslo_TAD(id){
    	var tab = new Array();
    	
    	tab[1] = $('.ER_'+id+' #type_materiel_aslo').val();
    	tab[2] = $('.ER_'+id+' #aslo_select').val();
    	tab[3] = $('.ER_'+id+' #aslo_titre').val();
	    
	    return tab;
    }
    
    /*
     * Analyse_62
     * tamponWidal
     */
    var tamponWidal = new Array();

    function getWidal_TAD(id){
    	var tab = new Array();
    	
    	tab[1]  = $('.ER_'+id+' #type_materiel_widal').val();
    	
    	tab[2]  = $('.ER_'+id+' #widal_to').val();
    	tab[3]  = $('.ER_'+id+' #widal_titre_to').val();
    	
    	tab[4]  = $('.ER_'+id+' #widal_th').val();
    	tab[5]  = $('.ER_'+id+' #widal_titre_th').val();
    	
    	tab[6]  = $('.ER_'+id+' #widal_ao').val();
    	tab[7]  = $('.ER_'+id+' #widal_titre_ao').val();
    	
    	tab[8]  = $('.ER_'+id+' #widal_ah').val();
    	tab[9]  = $('.ER_'+id+' #widal_titre_ah').val();
    	
    	tab[10] = $('.ER_'+id+' #widal_bo').val();
    	tab[11] = $('.ER_'+id+' #widal_titre_bo').val();
	    
    	tab[12] = $('.ER_'+id+' #widal_bh').val();
    	tab[13] = $('.ER_'+id+' #widal_titre_bh').val();
    	
    	tab[14] = $('.ER_'+id+' #widal_co').val();
    	tab[15] = $('.ER_'+id+' #widal_titre_co').val();
    	
    	tab[16] = $('.ER_'+id+' #widal_ch').val();
    	tab[17] = $('.ER_'+id+' #widal_titre_ch').val();
    	
	    return tab;
    }
    
    /*
     * Analyse_63
     * tamponAgHbs
     */
    var tamponAgHbs = new Array();

    function getAgHbs_TAD(id){
    	var tab = new Array();
    	
    	tab[1] = $('.ER_'+id+' #type_materiel_ag_hbs').val();
    	tab[2] = $('.ER_'+id+' #ag_hbs').val();
	    
	    return tab;
    }
    
    /*
     * Analyse_64
     * tampon
     */
    function getHIV_TAD(id){
    	var tab = new Array();
    	
    	tab[1] = $('.ER_'+id+' #type_materiel_hiv').val();
    	tab[2] = $('.ER_'+id+' #hiv').val();
    	tab[3] = $('.ER_'+id+' #hiv_typage').val();
	    
	    return tab;
    }
    
    
    
    
    /*
     * Analyse_68
     */
    var tamponTypageHemoglobine = new Array();
    
    function getTypageHemoglobine_TAD(id){
    	var tab = new Array();
    	tab[1] = $('.ER_'+id+' #type_materiel_typage_hemoglobine').val();
    	tab[2] = $('.ER_'+id+' #typage_hemoglobine').val();
    	tab[3] = $('.ER_'+id+' #autre_typage_hemoglobine').val();
    	
    	return tab;
    }
    
    
    
    
    /*
     * Analyse_70
     */
    var tamponLDH = new Array();
    
    function getLDH_TAD(id){
    	var tab = new Array();
    	tab[1] = $('.ER_'+id+' #type_materiel_ldh').val();
    	tab[2] = $('.ER_'+id+' #valeur_ldh').val();
	    
	    return tab;
    }
    
    //Resultats des analyses demandées pour un type et/ou une date donnée
    //Resultats des analyses demandées pour un type et/ou une date donnée
    //Resultats des analyses demandées pour un type et/ou une date donnée
    function resultatsAnalysesTypes(){
    	  $('#listeAnalyseParType').html('');
    	  $('#infosNbPatientParAnalyse img').attr('title', '').css({'opacity' : '0'});
    	  $( "#resultatsAnalysesParType" ).dialog({
    	    resizable: false,
    	    height:670,
    	    width:720,
    	    autoOpen: false,
    	    modal: true,
    	    buttons: {
    	        "Enregistrer": function() {
    	            
    	        	for(var i=0 ; i<listeAnalysesSelectionnees.length ; i++){
    	        		var idanalyse = listeAnalysesSelectionnees[i];
    	        		var iddemande = listeDemandesSelectionnees[i];
    	        		
    	        		if(idanalyse == 1){
        	        		
    	        			var tab = getChampsNfs_TAD(iddemande);
    	        			if(JSON.stringify(tamponChampsNfsTab[iddemande]) === JSON.stringify(tab)){}
	        				else{
	        					tamponChampsNfsTab[iddemande] = tab;
	        					enregistrementResultatsAnalyses_TAD(idanalyse, iddemande, tab);
	        				}
    	        			
    	        		}else
    	        		if(idanalyse == 2){
    	        			
    	        			var tab = getGroupageRhesus_TAD(iddemande);
    	        			if(JSON.stringify(tamponGroupageRhesusTab[iddemande]) === JSON.stringify(tab)){}
    	        			else{
    	        				tamponGroupageRhesusTab[iddemande] = tab;
        	        			enregistrementResultatsAnalyses_TAD(idanalyse, iddemande, tab);
    	        			}
    	        				
    	        		}else
        				if(idanalyse == 3){
        	        				
   	        				var tab = getAntigeneDFaible_TAD(iddemande);
   	        				if(JSON.stringify(tamponAntigeneDFaible[iddemande]) === JSON.stringify(tab)){}
   	        				else{
   	        					tamponAntigeneDFaible[iddemande] = tab;
       	        				enregistrementResultatsAnalyses_TAD(idanalyse, iddemande, tab);
   	        				}
        	        				
   	        			}else
   	        			if(idanalyse == 4){
            	        				
       	        			var tab = getTestCombsDirect_TAD(iddemande);
       	        			if(JSON.stringify(tamponTestCombsDirect[iddemande]) === JSON.stringify(tab)){}
       	        			else{
       	        				tamponTestCombsDirect[iddemande] = tab;
           	        			enregistrementResultatsAnalyses_TAD(idanalyse, iddemande, tab);
       	        			}
            	        				
   	        			}else
   	        			if(idanalyse == 5){
                	        				
       	        			var tab = getTestCombsIndirect_TAD(iddemande);
      	        			if(JSON.stringify(tamponTestCombsIndirect[iddemande]) === JSON.stringify(tab)){}
      	        			else{
       	        				tamponTestCombsIndirect[iddemande] = tab;
           	        			enregistrementResultatsAnalyses_TAD(idanalyse, iddemande, tab);
       	        			}
                	        				
   	        			}else
   	        			if(idanalyse == 6){
    	        				
       	        			var tab = getTestCompatibilite_TAD(iddemande);
      	        			if(JSON.stringify(tamponTestCompatibilite[iddemande]) === JSON.stringify(tab)){}
      	        			else{
      	        				tamponTestCompatibilite[iddemande] = tab;
           	        			enregistrementResultatsAnalyses_TAD(idanalyse, iddemande, tab);
       	        			}
                	        				
   	        			}else
                        if(idanalyse == 7){
    	        				
       	        			var tab = getVitesseSedimentation_TAD(iddemande);
      	        			if(JSON.stringify(tamponVitesseSedimentation[iddemande]) === JSON.stringify(tab)){}
      	        			else{
      	        				tamponVitesseSedimentation[iddemande] = tab;
           	        			enregistrementResultatsAnalyses_TAD(idanalyse, iddemande, tab);
       	        			}
                	        				
   	        			}else
   	        			if(idanalyse == 8){
    	        				
       	        			var tab = getTestDemmel_TAD(iddemande);
      	        			if(JSON.stringify(tamponTestDemmel[iddemande]) === JSON.stringify(tab)){}
      	        			else{
      	        				tamponTestDemmel[iddemande] = tab;
           	        			enregistrementResultatsAnalyses_TAD(idanalyse, iddemande, tab);
       	        			}
                	        				
   	        			}else
   	        			if(idanalyse == 9){
    	        				
       	        			var tab = getTauxReticulocytes_TAD(iddemande);
      	        			if(JSON.stringify(tamponTauxReticulocytes[iddemande]) === JSON.stringify(tab)){}
      	        			else{
      	        				tamponTauxReticulocytes[iddemande] = tab;
           	        			enregistrementResultatsAnalyses_TAD(idanalyse, iddemande, tab);
       	        			}
                	        				
   	        			}else
   	        			if(idanalyse == 10){
    	        				
       	        			var tab = getGoutteEpaisse_TAD(iddemande);
      	        			if(JSON.stringify(tamponGoutteEpaisse[iddemande]) === JSON.stringify(tab)){}
      	        			else{
      	        				tamponGoutteEpaisse[iddemande] = tab;
           	        			enregistrementResultatsAnalyses_TAD(idanalyse, iddemande, tab);
       	        			}

   	        			}else
   	        				
   	        				
   	        			if(idanalyse == 14){
    	        				
       	        			var tab = getTpInr_TAD(iddemande);
      	        			if(JSON.stringify(tamponTpInr[iddemande]) === JSON.stringify(tab)){}
      	        			else{
      	        				tamponTpInr[iddemande] = tab;
           	        			enregistrementResultatsAnalyses_TAD(idanalyse, iddemande, tab);
       	        			}

   	        			}else
   	        			if(idanalyse == 15){
    	        				
       	        			var tab = getTca_TAD(iddemande);
      	        			if(JSON.stringify(tamponTca[iddemande]) === JSON.stringify(tab)){}
      	        			else{
      	        				tamponTca[iddemande] = tab;
           	        			enregistrementResultatsAnalyses_TAD(idanalyse, iddemande, tab);
       	        			}

   	        			}else
   	        			if(idanalyse == 16){
    	        				
       	        			var tab = getFibrinemie_TAD(iddemande);
      	        			if(JSON.stringify(tamponFibrinemie[iddemande]) === JSON.stringify(tab)){}
      	        			else{
      	        				tamponFibrinemie[iddemande] = tab;
           	        			enregistrementResultatsAnalyses_TAD(idanalyse, iddemande, tab);
       	        			}

   	        			}else
   	        			if(idanalyse == 17){
    	        				
       	        			var tab = getTempsSaignement_TAD(iddemande);
      	        			if(JSON.stringify(tamponTempsSaignement[iddemande]) === JSON.stringify(tab)){}
      	        			else{
      	        				tamponTempsSaignement[iddemande] = tab;
           	        			enregistrementResultatsAnalyses_TAD(idanalyse, iddemande, tab);
       	        			}

   	        			}else
   	        			if(idanalyse == 18){
    	        				
       	        			var tab = getFacteur8_TAD(iddemande);
      	        			if(JSON.stringify(tamponFacteur8[iddemande]) === JSON.stringify(tab)){}
      	        			else{
      	        				tamponFacteur8[iddemande] = tab;
           	        			enregistrementResultatsAnalyses_TAD(idanalyse, iddemande, tab);
       	        			}

   	        			}else
   	        			if(idanalyse == 19){
    	        				
       	        			var tab = getFacteur9_TAD(iddemande);
      	        			if(JSON.stringify(tamponFacteur9[iddemande]) === JSON.stringify(tab)){}
      	        			else{
      	        				tamponFacteur9[iddemande] = tab;
           	        			enregistrementResultatsAnalyses_TAD(idanalyse, iddemande, tab);
       	        			}

   	        			}else
   	        			if(idanalyse == 20){
    	        				
       	        			var tab = getDDimeres_TAD(iddemande);
      	        			if(JSON.stringify(tamponDDimeres[iddemande]) === JSON.stringify(tab)){}
      	        			else{
      	        				tamponDDimeres[iddemande] = tab;
           	        			enregistrementResultatsAnalyses_TAD(idanalyse, iddemande, tab);
       	        			}

   	        			}else
   	        			if(idanalyse == 21){
    	        				
       	        			var tab = getGlycemie_TAD(iddemande);
      	        			if(JSON.stringify(tamponGlycemie[iddemande]) === JSON.stringify(tab)){}
      	        			else{
      	        				tamponGlycemie[iddemande] = tab;
           	        			enregistrementResultatsAnalyses_TAD(idanalyse, iddemande, tab);
       	        			}

   	        			}else
   	        			if(idanalyse == 22){
    	        				
       	        			var tab = getCreatininemie_TAD(iddemande);
      	        			if(JSON.stringify(tamponCreatininemie[iddemande]) === JSON.stringify(tab)){}
      	        			else{
      	        				tamponCreatininemie[iddemande] = tab;
           	        			enregistrementResultatsAnalyses_TAD(idanalyse, iddemande, tab);
       	        			}

   	        			}else
   	        			if(idanalyse == 23){
    	        				
       	        			var tab = getAzotemie_TAD(iddemande);
      	        			if(JSON.stringify(tamponAzotemie[iddemande]) === JSON.stringify(tab)){}
      	        			else{
      	        				tamponAzotemie[iddemande] = tab;
           	        			enregistrementResultatsAnalyses_TAD(idanalyse, iddemande, tab);
       	        			}

   	        			}else
   	        			if(idanalyse == 24){
    	        				
       	        			var tab = getAcideUrique_TAD(iddemande);
      	        			if(JSON.stringify(tamponAcideUrique[iddemande]) === JSON.stringify(tab)){}
      	        			else{
      	        				tamponAcideUrique[iddemande] = tab;
           	        			enregistrementResultatsAnalyses_TAD(idanalyse, iddemande, tab);
       	        			}

   	        			}else
   	        			if(idanalyse == 25){
    	        				
       	        			var tab = getCholesterolTotal_TAD(iddemande);
      	        			if(JSON.stringify(tamponCholesterolTotal[iddemande]) === JSON.stringify(tab)){}
      	        			else{
      	        				tamponCholesterolTotal[iddemande] = tab;
           	        			enregistrementResultatsAnalyses_TAD(idanalyse, iddemande, tab);
       	        			}

   	        			}else
   	        			if(idanalyse == 26){
    	        				
       	        			var tab = getTriglycerides_TAD(iddemande);
      	        			if(JSON.stringify(tamponTriglycerides[iddemande]) === JSON.stringify(tab)){}
      	        			else{
      	        				tamponTriglycerides[iddemande] = tab;
           	        			enregistrementResultatsAnalyses_TAD(idanalyse, iddemande, tab);
       	        			}

   	        			}else
   	        			if(idanalyse == 27){
    	        				
       	        			var tab = getCholesterolHDL_TAD(iddemande);
      	        			if(JSON.stringify(tamponCholesterolHDL[iddemande]) === JSON.stringify(tab)){}
      	        			else{
      	        				tamponCholesterolHDL[iddemande] = tab;
           	        			enregistrementResultatsAnalyses_TAD(idanalyse, iddemande, tab);
       	        			}

   	        			}else
   	        			if(idanalyse == 28){
    	        				
       	        			var tab = getCholesterolLDL_TAD(iddemande);
      	        			if(JSON.stringify(tamponCholesterolLDL[iddemande]) === JSON.stringify(tab)){}
      	        			else{
      	        				tamponCholesterolLDL[iddemande] = tab;
           	        			enregistrementResultatsAnalyses_TAD(idanalyse, iddemande, tab);
       	        			}

   	        			}else
   	        			if(idanalyse == 29){
    	        				
       	        			var tab = getChol_Total_HDL_LDL_Trigly_TAD(iddemande);
      	        			if(JSON.stringify(tamponChol_Total_HDL_LDL_Trigly[iddemande]) === JSON.stringify(tab)){}
      	        			else{
      	        				tamponChol_Total_HDL_LDL_Trigly[iddemande] = tab;
           	        			enregistrementResultatsAnalyses_TAD(idanalyse, iddemande, tab);
       	        			}

   	        			}else
   	        			if(idanalyse == 30){
    	        				
       	        			var tab = getLipidesTotaux_TAD(iddemande);
      	        			if(JSON.stringify(tamponLipidesTotaux[iddemande]) === JSON.stringify(tab)){}
      	        			else{
      	        				tamponLipidesTotaux[iddemande] = tab;
           	        			enregistrementResultatsAnalyses_TAD(idanalyse, iddemande, tab);
       	        			}

   	        			}else
   	        			if(idanalyse == 31){
    	        				
       	        			var tab = getIonogramme_TAD(iddemande);
      	        			if(JSON.stringify(tamponIonogramme[iddemande]) === JSON.stringify(tab)){}
      	        			else{
      	        				tamponIonogramme[iddemande] = tab;
           	        			enregistrementResultatsAnalyses_TAD(idanalyse, iddemande, tab);
       	        			}

   	        			}else
   	        		    if(idanalyse == 32){
    	        				
       	        			var tab = getCalcemie_TAD(iddemande);
      	        			if(JSON.stringify(tamponCalcemie[iddemande]) === JSON.stringify(tab)){}
      	        			else{
      	        				tamponCalcemie[iddemande] = tab;
           	        			enregistrementResultatsAnalyses_TAD(idanalyse, iddemande, tab);
       	        			}

   	        			}else
   	        			if(idanalyse == 33){
    	        				
       	        			var tab = getMagnesemie_TAD(iddemande);
      	        			if(JSON.stringify(tamponMagnesemie[iddemande]) === JSON.stringify(tab)){}
      	        			else{
      	        				tamponMagnesemie[iddemande] = tab;
           	        			enregistrementResultatsAnalyses_TAD(idanalyse, iddemande, tab);
       	        			}

   	        			}else
   	        			if(idanalyse == 34){
    	        				
       	        			var tab = getPhosphoremie_TAD(iddemande);
      	        			if(JSON.stringify(tamponPhosphoremie[iddemande]) === JSON.stringify(tab)){}
      	        			else{
      	        				tamponPhosphoremie[iddemande] = tab;
           	        			enregistrementResultatsAnalyses_TAD(idanalyse, iddemande, tab);
       	        			}

   	        			}else
   	        			if(idanalyse == 35){
    	        				
       	        			var tab = getTgoAsat_TAD(iddemande);
      	        			if(JSON.stringify(tamponTgoAsat[iddemande]) === JSON.stringify(tab)){}
      	        			else{
      	        				tamponTgoAsat[iddemande] = tab;
           	        			enregistrementResultatsAnalyses_TAD(idanalyse, iddemande, tab);
       	        			}

   	        			}else
   	        			if(idanalyse == 36){
    	        				
       	        			var tab = getTgpAlat_TAD(iddemande);
      	        			if(JSON.stringify(tamponTgpAlat[iddemande]) === JSON.stringify(tab)){}
      	        			else{
      	        				tamponTgpAlat[iddemande] = tab;
           	        			enregistrementResultatsAnalyses_TAD(idanalyse, iddemande, tab);
       	        			}

   	        			}else
   	        			if(idanalyse == 37){
    	        				
       	        			var tab = getAsatAlat_TAD(iddemande);
      	        			if(JSON.stringify(tamponAsatAlat[iddemande]) === JSON.stringify(tab)){}
      	        			else{
      	        				tamponAsatAlat[iddemande] = tab;
           	        			enregistrementResultatsAnalyses_TAD(idanalyse, iddemande, tab);
       	        			}

   	        			}else
   	        			if(idanalyse == 38){
    	        				
       	        			var tab = getPhosphatageAlcaline_TAD(iddemande);
      	        			if(JSON.stringify(tamponPhosphatageAlcaline[iddemande]) === JSON.stringify(tab)){}
      	        			else{
      	        				tamponPhosphatageAlcaline[iddemande] = tab;
           	        			enregistrementResultatsAnalyses_TAD(idanalyse, iddemande, tab);
       	        			}

   	        			}else
   	        			if(idanalyse == 39){
    	        				
       	        			var tab = getGamaGT_TAD(iddemande);
      	        			if(JSON.stringify(tamponGamaGT[iddemande]) === JSON.stringify(tab)){}
      	        			else{
      	        				tamponGamaGT[iddemande] = tab;
           	        			enregistrementResultatsAnalyses_TAD(idanalyse, iddemande, tab);
       	        			}

   	        			}else
   	        			if(idanalyse == 40){
    	        				
       	        			var tab = getFerSerique_TAD(iddemande);
      	        			if(JSON.stringify(tamponFerSerique[iddemande]) === JSON.stringify(tab)){}
      	        			else{
      	        				tamponFerSerique[iddemande] = tab;
           	        			enregistrementResultatsAnalyses_TAD(idanalyse, iddemande, tab);
       	        			}

   	        			}else
   	        			if(idanalyse == 41){
    	        				
       	        			var tab = getFerritinine_TAD(iddemande);
      	        			if(JSON.stringify(tamponFerritinine[iddemande]) === JSON.stringify(tab)){}
      	        			else{
      	        				tamponFerritinine[iddemande] = tab;
           	        			enregistrementResultatsAnalyses_TAD(idanalyse, iddemande, tab);
       	        			}

   	        			}else
   	        			if(idanalyse == 42){
    	        				
       	        			var tab = getBilirubineTotaleDirecte_TAD(iddemande);
      	        			if(JSON.stringify(tamponBilirubineTotalDirecte[iddemande]) === JSON.stringify(tab)){}
      	        			else{
      	        				tamponBilirubineTotalDirecte[iddemande] = tab;
           	        			enregistrementResultatsAnalyses_TAD(idanalyse, iddemande, tab);
       	        			}

   	        			}else
   	        			if(idanalyse == 43){
    	        				
       	        			var tab = getHemoglobineGlyqueeHBAC_TAD(iddemande);
      	        			if(JSON.stringify(tamponHemoglobineGlyqueeHBAC[iddemande]) === JSON.stringify(tab)){}
      	        			else{
      	        				tamponHemoglobineGlyqueeHBAC[iddemande] = tab;
           	        			enregistrementResultatsAnalyses_TAD(idanalyse, iddemande, tab);
       	        			}

   	        			}else
   	        			if(idanalyse == 44){
    	        				
       	        			var tab = getElectrophoreseHemoglobine_TAD(iddemande);
      	        			if(JSON.stringify(tamponElectrophoreseHemoglobine[iddemande]) === JSON.stringify(tab)){}
      	        			else{
      	        				tamponElectrophoreseHemoglobine[iddemande] = tab;
           	        			enregistrementResultatsAnalyses_TAD(idanalyse, iddemande, tab);
       	        			}

   	        			}else
   	        			if(idanalyse == 45){
    	        				
       	        			var tab = getElectrophoreseProteine_TAD(iddemande);
      	        			if(JSON.stringify(tamponElectrophoreseProteine[iddemande]) === JSON.stringify(tab)){}
      	        			else{
      	        				tamponElectrophoreseProteine[iddemande] = tab;
           	        			enregistrementResultatsAnalyses_TAD(idanalyse, iddemande, tab);
       	        			}

   	        			}else
   	        			if(idanalyse == 46){
    	        				
       	        			var tab = getAlbuminemie_TAD(iddemande);
      	        			if(JSON.stringify(tamponAlbuminemie[iddemande]) === JSON.stringify(tab)){}
      	        			else{
      	        				tamponAlbuminemie[iddemande] = tab;
           	        			enregistrementResultatsAnalyses_TAD(idanalyse, iddemande, tab);
       	        			}

   	        			}else
   	        			if(idanalyse == 47){
    	        				
       	        			var tab = getAlbumineUrinaire_TAD(iddemande);
      	        			if(JSON.stringify(tamponAlbumineUrinaire[iddemande]) === JSON.stringify(tab)){}
      	        			else{
      	        				tamponAlbumineUrinaire[iddemande] = tab;
           	        			enregistrementResultatsAnalyses_TAD(idanalyse, iddemande, tab);
       	        			}

   	        			}else
   	        			if(idanalyse == 48){
    	        				
       	        			var tab = getProtidemie_TAD(iddemande);
      	        			if(JSON.stringify(tamponProtidemie[iddemande]) === JSON.stringify(tab)){}
      	        			else{
      	        				tamponProtidemie[iddemande] = tab;
           	        			enregistrementResultatsAnalyses_TAD(idanalyse, iddemande, tab);
       	        			}

   	        			}else
   	        			if(idanalyse == 49){
    	        				
       	        			var tab = getProteinurie_TAD(iddemande);
      	        			if(JSON.stringify(tamponProteinurie[iddemande]) === JSON.stringify(tab)){}
      	        			else{
      	        				tamponProteinurie[iddemande] = tab;
           	        			enregistrementResultatsAnalyses_TAD(idanalyse, iddemande, tab);
       	        			}

   	        			}else
   	        			if(idanalyse == 50){
    	        				
       	        			var tab = getHlmCompteDaddis_TAD(iddemande);
      	        			if(JSON.stringify(tamponHlmCompteDaddis[iddemande]) === JSON.stringify(tab)){}
      	        			else{
      	        				tamponHlmCompteDaddis[iddemande] = tab;
           	        			enregistrementResultatsAnalyses_TAD(idanalyse, iddemande, tab);
       	        			}

   	        			}else
   	        			if(idanalyse == 51){
    	        				
       	        			var tab = getBetaHcgPlasmatique_TAD(iddemande);
      	        			if(JSON.stringify(tamponBetaHcgPlasmatique[iddemande]) === JSON.stringify(tab)){}
      	        			else{
      	        				tamponBetaHcgPlasmatique[iddemande] = tab;
           	        			enregistrementResultatsAnalyses_TAD(idanalyse, iddemande, tab);
       	        			}

   	        			}else
   	        			if(idanalyse == 52){
    	        				
       	        			var tab = getPsa_TAD(iddemande);
      	        			if(JSON.stringify(tamponPsa[iddemande]) === JSON.stringify(tab)){}
      	        			else{
      	        				tamponPsa[iddemande] = tab;
           	        			enregistrementResultatsAnalyses_TAD(idanalyse, iddemande, tab);
       	        			}

   	        			}else
   	        			if(idanalyse == 53){
    	        				
       	        			var tab = getCrp_TAD(iddemande);
      	        			if(JSON.stringify(tamponCrp[iddemande]) === JSON.stringify(tab)){}
      	        			else{
      	        				tamponCrp[iddemande] = tab;
           	        			enregistrementResultatsAnalyses_TAD(idanalyse, iddemande, tab);
       	        			}

   	        			}else
   	        			if(idanalyse == 54){
    	        				
       	        			var tab = getFacteursRhumatoides_TAD(iddemande);
      	        			if(JSON.stringify(tamponFacteurRhumatoides[iddemande]) === JSON.stringify(tab)){}
      	        			else{
      	        				tamponFacteurRhumatoides[iddemande] = tab;
           	        			enregistrementResultatsAnalyses_TAD(idanalyse, iddemande, tab);
       	        			}

   	        			}else
   	        			if(idanalyse == 55){
    	        				
       	        			var tab = getRfWaalerRose_TAD(iddemande);
      	        			if(JSON.stringify(tamponRfWaalerRose[iddemande]) === JSON.stringify(tab)){}
      	        			else{
      	        				tamponRfWaalerRose[iddemande] = tab;
           	        			enregistrementResultatsAnalyses_TAD(idanalyse, iddemande, tab);
       	        			}

   	        			}else
   	        			if(idanalyse == 56){
    	        				
       	        			var tab = getToxoplasmose_TAD(iddemande);
      	        			if(JSON.stringify(tamponToxoplasmose[iddemande]) === JSON.stringify(tab)){}
      	        			else{
      	        				tamponToxoplasmose[iddemande] = tab;
           	        			enregistrementResultatsAnalyses_TAD(idanalyse, iddemande, tab);
       	        			}

   	        			}else
   	        			if(idanalyse == 57){
    	        				
       	        			var tab = getRubeole_TAD(iddemande);
      	        			if(JSON.stringify(tamponRubeole[iddemande]) === JSON.stringify(tab)){}
      	        			else{
      	        				tamponRubeole[iddemande] = tab;
           	        			enregistrementResultatsAnalyses_TAD(idanalyse, iddemande, tab);
       	        			}

   	        			}else
   	        			if(idanalyse == 58){
    	        				
       	        			var tab = getCulotUrinaire_TAD(iddemande);
      	        			if(JSON.stringify(tamponCulotUrinaire[iddemande]) === JSON.stringify(tab)){}
      	        			else{
      	        				tamponCulotUrinaire[iddemande] = tab;
           	        			enregistrementResultatsAnalyses_TAD(idanalyse, iddemande, tab);
       	        			}

   	        			}else
   	        			if(idanalyse == 59){
    	        				
       	        			var tab = getSerologieChlamydiae_TAD(iddemande);
      	        			if(JSON.stringify(tamponSerologieChlamydiae[iddemande]) === JSON.stringify(tab)){}
      	        			else{
      	        				tamponSerologieChlamydiae[iddemande] = tab;
           	        			enregistrementResultatsAnalyses_TAD(idanalyse, iddemande, tab);
       	        			}

   	        			}else
   	        			if(idanalyse == 60){
    	        				
       	        			var tab = getSerologieSyphilitique_TAD(iddemande);
      	        			if(JSON.stringify(tamponSerologieSyphilitique[iddemande]) === JSON.stringify(tab)){}
      	        			else{
      	        				tamponSerologieSyphilitique[iddemande] = tab;
           	        			enregistrementResultatsAnalyses_TAD(idanalyse, iddemande, tab);
       	        			}

   	        			}else
   	        			if(idanalyse == 61){
    	        				
       	        			var tab = getAslo_TAD(iddemande);
      	        			if(JSON.stringify(tamponAslo[iddemande]) === JSON.stringify(tab)){}
      	        			else{
      	        				tamponAslo[iddemande] = tab;
           	        			enregistrementResultatsAnalyses_TAD(idanalyse, iddemande, tab);
       	        			}

   	        			}else
   	        			if(idanalyse == 62){
    	        				
       	        			var tab = getWidal_TAD(iddemande);
      	        			if(JSON.stringify(tamponWidal[iddemande]) === JSON.stringify(tab)){}
      	        			else{
      	        				tamponWidal[iddemande] = tab;
           	        			enregistrementResultatsAnalyses_TAD(idanalyse, iddemande, tab);
       	        			}

   	        			}else
   	        			if(idanalyse == 63){
    	        				
       	        			var tab = getWidal_TAD(iddemande);
      	        			if(JSON.stringify(tamponAgHbs[iddemande]) === JSON.stringify(tab)){}
      	        			else{
      	        				tamponAgHbs[iddemande] = tab;
           	        			enregistrementResultatsAnalyses_TAD(idanalyse, iddemande, tab);
       	        			}

   	        			}else
   	        				
   	        				

   	        				
   	        				

   	        				
   	        	
   	        				
   	        				

    	        				
             			if(idanalyse == 68){
    	        					
   	        				var tab = getTypageHemoglobine_TAD(iddemande);
       	        			if(JSON.stringify(tamponTypageHemoglobine[iddemande]) === JSON.stringify(tab)){}
       	        			else{
       	        				tamponTypageHemoglobine[iddemande] = tab;
          	        			enregistrementResultatsAnalyses_TAD(idanalyse, iddemande, tab);
       	        			}
        	        				
         				}
    	        				
    	        	}
    	        	
    	            
    	            return false;
    	            
    	        },
    	        
    	        "Fermer": function() {
                    $(this).dialog( "close" );
                }
    	   }
    	  });
        
    }
    
    
    //Recupérer la liste des analyses à l'entrée pour initialisation
    //Recupérer la liste des analyses à l'entrée pour initialisation
    function resultatsDesAnalysesParType(){
    	$('#typeAnalyseDesign').val(1);
    	$('#contenuResultatsAnalyses div').empty();
    	$('#contenuResultatsAnalysesDuneDemande div').empty();
    	$('#contenuResultatsAnalysesParType div').empty();
    	$('#contenuResultatsAnalysesParType div').html('<table> <tr> <td style="margin-top: 20px;"> Chargement </td> </tr>  <tr> <td align="center"> <img style="margin-top: 20px; width: 50px; height: 50px;" src="../images/loading/Chargement_1.gif" /> </td> </tr> </table>');
    	resultatsAnalysesTypes();
    	$("#resultatsAnalysesParType").dialog('open');
    	
        $.ajax({
            type: 'POST',
            url: tabUrl[0]+'public/technicien/recuperer-les-analyses-demandees-par-type',
            data:{ 'idtype': 1 },
            success: function(data) {
            	var result = jQuery.parseJSON(data);  
            	$('#contenuResultatsAnalysesParType div').html(result);
            	listeDemandesSelectionnees = listeDesDemandesSelect;
            	listeAnalysesSelectionnees = listeDesAnalysesSelect;
            	
            	var scriptFormule;
            	
            	scriptFormule  = gestionFormuleLeucocytaire_TAD(listeDesDemandesSelect);
            	scriptFormule += gestionFormuleGlycemie_TAD(listeDesDemandesSelect);
            	scriptFormule += gestionFormuleTCA_TAD(listeDesDemandesSelect);
            	scriptFormule += gestionFormuleCreatininemie_TAD(listeDesDemandesSelect);
            	scriptFormule += gestionFormuleCholesterolTotal_TAD(listeDesDemandesSelect);
            	scriptFormule += gestionFormuleTriglycerides_TAD(listeDesDemandesSelect);
            	scriptFormule += gestionFormuleCholesterolHDL_TAD(listeDesDemandesSelect);
            	scriptFormule += gestionFormuleCholesterolLDL_TAD(listeDesDemandesSelect);
            	scriptFormule += gestionFormuleRapportCholHdl_TAD(listeDesDemandesSelect);
            	scriptFormule += gestionFormuleElectrophoreseProteines_TAD(listeDesDemandesSelect);
            	scriptFormule += gestionFormuleElectrophoreseHemoglobine(listeDesDemandesSelect);
            	
            	$('#scriptFormules').html(scriptFormule);
            	
            }
        });
    	
    }
    
    //Récupérer la liste des analyses en sélectionnant un type
    //Récupérer la liste des analyses en sélectionnant un type
    function getListeAnalysesDemandees(idtype){ 
    	$('#listeAnalyseParType').html('');
    	$('#listeAnalyseParTypeParDate').html('');
    	$('#listeCodesDesPatients').html('');
    	$('#contenuResultatsAnalysesParType div').empty();
    	$('#contenuResultatsAnalysesParType div').html('<table> <tr> <td style="margin-top: 20px;"> Chargement </td> </tr>  <tr> <td align="center"> <img style="margin-top: 20px; width: 50px; height: 50px;" src="../images/loading/Chargement_1.gif" /> </td> </tr> </table>');
    	$('#infosNbPatientParAnalyse img').attr('title', '').css({'opacity' : '0'});
    	$('#infosNbPatientParAnalyseParDate img').attr('title', '').css({'opacity' : '0'});
    	$.ajax({
             type: 'POST',
             url: tabUrl[0]+'public/technicien/recuperer-les-analyses-demandees-par-type',
             data:{ 'idtype': idtype },
             success: function(data) {
             	var result = jQuery.parseJSON(data);  
            	$('#contenuResultatsAnalysesParType div').html(result);
            	listeDemandesSelectionnees = listeDesDemandesSelect;
            	listeAnalysesSelectionnees = listeDesAnalysesSelect;
            	
            	var scriptFormule;
            	
            	scriptFormule  = gestionFormuleLeucocytaire_TAD(listeDesDemandesSelect);
            	scriptFormule += gestionFormuleGlycemie_TAD(listeDesDemandesSelect);
            	scriptFormule += gestionFormuleTCA_TAD(listeDesDemandesSelect);
            	scriptFormule += gestionFormuleCreatininemie_TAD(listeDesDemandesSelect);
            	scriptFormule += gestionFormuleCholesterolTotal_TAD(listeDesDemandesSelect);
            	scriptFormule += gestionFormuleTriglycerides_TAD(listeDesDemandesSelect);
            	scriptFormule += gestionFormuleCholesterolHDL_TAD(listeDesDemandesSelect);
            	scriptFormule += gestionFormuleCholesterolLDL_TAD(listeDesDemandesSelect);
            	scriptFormule += gestionFormuleRapportCholHdl_TAD(listeDesDemandesSelect);
            	scriptFormule += gestionFormuleElectrophoreseProteines_TAD(listeDesDemandesSelect);
            	scriptFormule += gestionFormuleElectrophoreseHemoglobine(listeDesDemandesSelect);
            	
            	$('#scriptFormules').html(scriptFormule);
             }
    	});
    }
    
    //Recupérer la liste des patients pour un type et une analyse donnée
    //Recupérer la liste des patients pour un type et une analyse donnée
    function getListePatientsParAnalyse(idanalyse){
    	var idtype = $('#typeAnalyseDesign').val();
    	
    	$('#listeAnalyseParTypeParDate').html('');
    	$('#listeCodesDesPatients').html('');
    	$('#contenuResultatsAnalysesParType div').empty();
    	$('#contenuResultatsAnalysesParType div').html('<table> <tr> <td style="margin-top: 20px;"> Chargement </td> </tr>  <tr> <td align="center"> <img style="margin-top: 20px; width: 50px; height: 50px;" src="../images/loading/Chargement_1.gif" /> </td> </tr> </table>');
    	$('#infosNbPatientParAnalyseParDate img').attr('title', '').css({'opacity' : '0'});
    	$.ajax({
             type: 'POST',
             url: tabUrl[0]+'public/technicien/recuperer-les-analyses-demandees-par-type-et-analyse',
             data:{ 'idtype': idtype, 'idanalyse': idanalyse },
             success: function(data) {
             	var result = jQuery.parseJSON(data);
            	$('#contenuResultatsAnalysesParType div').html(result);
            	listeDemandesSelectionnees = listeDesDemandesSelect;
            	listeAnalysesSelectionnees = listeDesAnalysesSelect;
            	
            	var scriptFormule;
            	
            	scriptFormule  = gestionFormuleLeucocytaire_TAD(listeDesDemandesSelect);
            	scriptFormule += gestionFormuleGlycemie_TAD(listeDesDemandesSelect);
            	scriptFormule += gestionFormuleTCA_TAD(listeDesDemandesSelect);
            	scriptFormule += gestionFormuleCreatininemie_TAD(listeDesDemandesSelect);
            	scriptFormule += gestionFormuleCholesterolTotal_TAD(listeDesDemandesSelect);
            	scriptFormule += gestionFormuleTriglycerides_TAD(listeDesDemandesSelect);
            	scriptFormule += gestionFormuleCholesterolHDL_TAD(listeDesDemandesSelect);
            	scriptFormule += gestionFormuleCholesterolLDL_TAD(listeDesDemandesSelect);
            	scriptFormule += gestionFormuleRapportCholHdl_TAD(listeDesDemandesSelect);
            	scriptFormule += gestionFormuleElectrophoreseProteines_TAD(listeDesDemandesSelect);
            	scriptFormule += gestionFormuleElectrophoreseHemoglobine(listeDesDemandesSelect);
            	
            	
            	$('#scriptFormules').html(scriptFormule);
            	
             }
    	});
    	
    }
    
    
    
    //Récupérer la liste des patients pour un type, une analyse et une date donnée
    //Récupérer la liste des patients pour un type, une analyse et une date donnée
    function getListePatientsParAnalyseParDate(date){
    	var idtype = $('#typeAnalyseDesign').val();
    	var idanalyse = $('#listeAnalyseParType').val();
    	
    	$('#listeCodesDesPatients').html('');
    	$('#contenuResultatsAnalysesParType div').empty();
    	$('#contenuResultatsAnalysesParType div').html('<table> <tr> <td style="margin-top: 20px;"> Chargement </td> </tr>  <tr> <td align="center"> <img style="margin-top: 20px; width: 50px; height: 50px;" src="../images/loading/Chargement_1.gif" /> </td> </tr> </table>');
    	$.ajax({
             type: 'POST',
             url: tabUrl[0]+'public/technicien/recuperer-les-analyses-demandees-par-type-et-analyse-et-date',
             data:{ 'idtype': idtype, 'idanalyse': idanalyse, 'date': date },
             success: function(data) {
             	var result = jQuery.parseJSON(data);
            	$('#contenuResultatsAnalysesParType div').html(result);
            	listeDemandesSelectionnees = listeDesDemandesSelect;
            	listeAnalysesSelectionnees = listeDesAnalysesSelect;
            	
            	var scriptFormule;
            	
            	scriptFormule  = gestionFormuleLeucocytaire_TAD(listeDesDemandesSelect);
            	scriptFormule += gestionFormuleGlycemie_TAD(listeDesDemandesSelect);
            	scriptFormule += gestionFormuleTCA_TAD(listeDesDemandesSelect);
            	scriptFormule += gestionFormuleCreatininemie_TAD(listeDesDemandesSelect);
            	scriptFormule += gestionFormuleCholesterolTotal_TAD(listeDesDemandesSelect);
            	scriptFormule += gestionFormuleTriglycerides_TAD(listeDesDemandesSelect);
            	scriptFormule += gestionFormuleCholesterolHDL_TAD(listeDesDemandesSelect);
            	scriptFormule += gestionFormuleCholesterolLDL_TAD(listeDesDemandesSelect);
            	scriptFormule += gestionFormuleRapportCholHdl_TAD(listeDesDemandesSelect);
            	scriptFormule += gestionFormuleElectrophoreseProteines_TAD(listeDesDemandesSelect);
            	scriptFormule += gestionFormuleElectrophoreseHemoglobine(listeDesDemandesSelect);
            	
            	$('#scriptFormules').html(scriptFormule);
             }
    	});
    }
    
    
    //Filtrer par patient
    //Filtrer par patient
    function getListeCodesDesPatients(id){
    	
    	if(id){
        	$('.patientListeAnalyse').toggle(false);
        	$('.pat_'+id).toggle(true);
    	}else{
        	$('.patientListeAnalyse').toggle(true);
    	}

    }
    
    
    
    /***
     * ==============================================================================
     * ==============================================================================
     * ==============================================================================
     * ==============================================================================
     */
    
    /**
     * SCRIPT PERMETTANT D'APPLIQUER LA FORMULE POUR L'ENREGISTREMENT DES DONNEES NFS
     * @param demande : liste des demandes
     */
    function gestionFormuleLeucocytaire_TAD(demande){
    	
    	var scriptFormule = "<script>";
    	for(var i=0 ; i<demande.length ; i++){
        	var iddemande = demande[i];
        	
        	//Polynucléaires neutrophiles
        	//Polynucléaires neutrophiles
        	scriptFormule += "$('.ER_"+iddemande+" #champ1, .ER_"+iddemande+" #champ7').keyup( function () {"+
        	                   
        	                    "var champ1 = $('.ER_"+iddemande+" #champ1').val();"+
        	                    "var champ7 = $('.ER_"+iddemande+" #champ7').val();"+
        	                    "var champ2 = $('.ER_"+iddemande+" #champ2');"+
        	                    "if( champ1 && champ7 ){"+
        	         			    "var resultatChamp2 = (champ1*champ7)/100;"+
        	         			    "champ2.val(resultatChamp2);"+
        	         		    "}else{ champ2.val(null); }"+
        	                 
        	                 "}).change( function () {"+
        	                 
        	                    "var champ1 = $('.ER_"+iddemande+" #champ1').val();"+
     	                        "var champ7 = $('.ER_"+iddemande+" #champ7').val();"+
     	                        "var champ2 = $('.ER_"+iddemande+" #champ2');"+
     	                        "if( champ1 && champ7 ){"+
     	         			        "var resultatChamp2 = (champ1*champ7)/100;"+
     	         			        "champ2.val(resultatChamp2);"+
     	         		        "}else{ champ2.val(null); }"+
        	                 
        	                 "}).trigger('keyup');";
        		
        	
        	//Polynucléaires eosinophiles
        	//Polynucléaires eosinophiles
        	scriptFormule += "$('.ER_"+iddemande+" #champ1, .ER_"+iddemande+" #champ8').keyup( function () {"+
            
                                "var champ1 = $('.ER_"+iddemande+" #champ1').val();"+
                                "var champ8 = $('.ER_"+iddemande+" #champ8').val();"+
                                "var champ3 = $('.ER_"+iddemande+" #champ3');"+
                                "if( champ1 && champ8 ){"+
 			                        "var resultatChamp3 = (champ1*champ8)/100;"+
 			                        "champ3.val(resultatChamp3);"+
 		                        "}else{ champ3.val(null); }"+
         
                                "}).change( function () {"+
         
                                   "var champ1 = $('.ER_"+iddemande+" #champ1').val();"+
                                   "var champ8 = $('.ER_"+iddemande+" #champ8').val();"+
                                   "var champ3 = $('.ER_"+iddemande+" #champ3');"+
                                   "if( champ1 && champ8 ){"+
			                           "var resultatChamp3 = (champ1*champ8)/100;"+
			                           "champ3.val(resultatChamp3);"+
		                           "}else{ champ3.val(null); }"+
         
                                "}).trigger('keyup');";
        	
        	
        	//Polynucléaires basophiles
        	//Polynucléaires basophiles
        	scriptFormule += "$('.ER_"+iddemande+" #champ1, .ER_"+iddemande+" #champ9').keyup( function () {"+
            
                                "var champ1 = $('.ER_"+iddemande+" #champ1').val();"+
                                "var champ9 = $('.ER_"+iddemande+" #champ9').val();"+
                                "var champ4 = $('.ER_"+iddemande+" #champ4');"+
                                "if( champ1 && champ9 ){"+
                                    "var resultatChamp4 = (champ1*champ9)/100;"+
                                    "champ4.val(resultatChamp4);"+
                                "}else{ champ4.val(null); }"+

                                "}).change( function () {"+

                                   "var champ1 = $('.ER_"+iddemande+" #champ1').val();"+
                                   "var champ9 = $('.ER_"+iddemande+" #champ9').val();"+
                                   "var champ4 = $('.ER_"+iddemande+" #champ4');"+
                                   "if( champ1 && champ9 ){"+
                                       "var resultatChamp4 = (champ1*champ9)/100;"+
                                       "champ4.val(resultatChamp4);"+
                                   "}else{ champ4.val(null); }"+

                                "}).trigger('keyup');";
        	
        	
        	//Lymphocytes Lymphocytes
        	//Lymphocytes Lymphocytes
        	scriptFormule += "$('.ER_"+iddemande+" #champ1, .ER_"+iddemande+" #champ10').keyup( function () {"+
            
                                "var champ1  = $('.ER_"+iddemande+" #champ1').val();"+
                                "var champ10 = $('.ER_"+iddemande+" #champ10').val();"+
                                "var champ5  = $('.ER_"+iddemande+" #champ5');"+
                                "if( champ1 && champ10 ){"+
                                    "var resultatChamp5 = (champ1*champ10)/100;"+
                                    "champ5.val(resultatChamp5);"+
                                "}else{ champ5.val(null); }"+

                                "}).change( function () {"+

                                   "var champ1  = $('.ER_"+iddemande+" #champ1').val();"+
                                   "var champ10 = $('.ER_"+iddemande+" #champ10').val();"+
                                   "var champ5  = $('.ER_"+iddemande+" #champ5');"+
                                   "if( champ1 && champ10 ){"+
                                       "var resultatChamp5 = (champ1*champ10)/100;"+
                                       "champ5.val(resultatChamp5);"+
                                   "}else{ champ5.val(null); }"+

                                "}).trigger('keyup');";
        	
        	
        	//Monocytes Monocytes
        	//Monocytes Monocytes
        	scriptFormule += "$('.ER_"+iddemande+" #champ1, .ER_"+iddemande+" #champ11').keyup( function () {"+
            
                                "var champ1  = $('.ER_"+iddemande+" #champ1').val();"+
                                "var champ11 = $('.ER_"+iddemande+" #champ11').val();"+
                                "var champ6  = $('.ER_"+iddemande+" #champ6');"+
                                "if( champ1 && champ11 ){"+
                                    "var resultatChamp6 = (champ1*champ11)/100;"+
                                    "champ6.val(resultatChamp6);"+
                                "}else{ champ6.val(null); }"+

                                "}).change( function () {"+

                                   "var champ1  = $('.ER_"+iddemande+" #champ1').val();"+
                                   "var champ11 = $('.ER_"+iddemande+" #champ11').val();"+
                                   "var champ6  = $('.ER_"+iddemande+" #champ6');"+
                                   "if( champ1 && champ11 ){"+
                                       "var resultatChamp6 = (champ1*champ11)/100;"+
                                       "champ6.val(resultatChamp6);"+
                                   "}else{ champ6.val(null); }"+

                                "}).trigger('keyup');";
        	
        	
        	//Taux de r�ticulocytes -- Taux de r�ticulocytes
        	//Taux de r�ticulocytes -- Taux de r�ticulocytes
        	
        	scriptFormule += "$('.ER_"+iddemande+" #champ12, .ER_"+iddemande+" #champ25').keyup( function () {"+
            
                                "var champ12 = $('.ER_"+iddemande+" #champ12').val();"+
                                "var champ25 = $('.ER_"+iddemande+" #champ25').val();"+
                                "var champ24 = $('.ER_"+iddemande+" #champ24');"+
                                "if( champ12 && champ25 ){"+
                                    "var resultatChamp24 = champ12*10000*champ25;"+
                                    "champ24.val(resultatChamp24);"+
                                "}else{ champ24.val(null); }"+

                                "}).change( function () {"+

                                   "var champ12 = $('.ER_"+iddemande+" #champ12').val();"+
                                   "var champ25 = $('.ER_"+iddemande+" #champ25').val();"+
                                   "var champ24 = $('.ER_"+iddemande+" #champ24');"+
                                   "if( champ12 && champ25 ){"+
                                       "var resultatChamp24 = champ12*10000*champ25;"+
                                       "champ24.val(resultatChamp24);"+
                                   "}else{ champ24.val(null); }"+

                                "}).trigger('keyup');";
        	
    	}
    	scriptFormule += "</script>";
    	
    	return scriptFormule;
    }
    
    function gestionFormuleGlycemie_TAD(demande){
    	
    	var scriptFormule = "<script>";
    	for(var i=0 ; i<demande.length ; i++){
        	var iddemande = demande[i];
        	
        	scriptFormule += "$('.ER_"+iddemande+" #glycemie_1').keyup( function () {"+
        	                    
        	                    "var glycemie_1 = $('.ER_"+iddemande+" #glycemie_1').val();"+
        	                    "if(glycemie_1){"+
        	                        "var valeur_mmol = glycemie_1 * 5.551;"+
        	         			    "$('.ER_"+iddemande+" #glycemie_2').val(valeur_mmol.toFixed(2));"+
        	         		    "}else{ $('.ER_"+iddemande+" #glycemie_2').val(null); }"+
        	                 
        	                 "}).change( function () {"+
        	                 
        	                    "var glycemie_1 = $('.ER_"+iddemande+" #glycemie_1').val();"+
     	                        "if(glycemie_1){"+
     	                            "var valeur_mmol = glycemie_1 * 5.551;"+
     	         			        "$('.ER_"+iddemande+" #glycemie_2').val(valeur_mmol.toFixed(2));"+
     	         		        "}else{ $('.ER_"+iddemande+" #glycemie_2').val(null); }"+
        	                 
        	                 "}).trigger('keyup');";
        	
    	}
    	scriptFormule += "</script>";
        
    	return scriptFormule;
    }
    
    function gestionFormuleTCA_TAD(demande){
    	
    	var scriptFormule = "<script>";
    	for(var i=0 ; i<demande.length ; i++){
        	var iddemande = demande[i];
        	
        	scriptFormule += "$('.ER_"+iddemande+" #tca_patient, .ER_"+iddemande+" #temoin_patient').keyup( function () {"+
        	                    
        	                    "var tca_patient = $('.ER_"+iddemande+" #tca_patient').val();"+
        	                    "var temoin_patient = $('.ER_"+iddemande+" #temoin_patient').val();"+
        	                    
        	                    "if(tca_patient && temoin_patient){"+
        		                    "var tca_ratio = tca_patient/temoin_patient;"+
        	                  	    "$('.ER_"+iddemande+" #tca_ratio').val(tca_ratio.toFixed(2));"+
        	                    "}else{ $('.ER_"+iddemande+" #tca_ratio').val(null); }"+
        	                 
        	                 "}).change( function () {"+
        	                 
        	                    "var tca_patient = $('.ER_"+iddemande+" #tca_patient').val();"+
     	                        "var temoin_patient = $('.ER_"+iddemande+" #temoin_patient').val();"+
     	                    
     	                        "if(tca_patient && temoin_patient){"+
     		                        "var tca_ratio = tca_patient/temoin_patient;"+
     	                  	        "$('.ER_"+iddemande+" #tca_ratio').val(tca_ratio.toFixed(2));"+
     	                        "}else{ $('.ER_"+iddemande+" #tca_ratio').val(null); }"+
        	                    
        	                 "}).trigger('keyup');";
        	
    	}
    	scriptFormule += "</script>";
        
    	return scriptFormule;
    }
    
    function gestionFormuleCreatininemie_TAD(demande){
    	
    	var scriptFormule = "<script>";
    	for(var i=0 ; i<demande.length ; i++){
        	var iddemande = demande[i];
        	
        	scriptFormule += "$('.ER_"+iddemande+" #creatininemie').keyup( function () {"+
        	                    
        	                    "var creatininemie = $('.ER_"+iddemande+" #creatininemie').val();"+
    	   	                    "if(creatininemie){"+
        		                    "var valeur_umol = creatininemie * 8.84;"+
        		                    "$('.ER_"+iddemande+" #creatininemie_umol').val(valeur_umol.toFixed(2));"+
        	                    "}else{	$('.ER_"+iddemande+" #creatininemie_umol').val(null); }"+
        	                 
        	                 "}).change( function () {"+
        	                 
        	                    "var creatininemie = $('.ER_"+iddemande+" #creatininemie').val();"+
 	   	                        "if(creatininemie){"+
     		                        "var valeur_umol = creatininemie * 8.84;"+
     		                        "$('.ER_"+iddemande+" #creatininemie_umol').val(valeur_umol.toFixed(2));"+
     	                        "}else{	$('.ER_"+iddemande+" #creatininemie_umol').val(null); }"+
        	                    
        	                 "}).trigger('keyup');";
        	
    	}
    	scriptFormule += "</script>";
        
    	return scriptFormule;
    }
    
    function gestionFormuleCholesterolTotal_TAD(demande){
    	
    	var scriptFormule = "<script>";
    	for(var i=0 ; i<demande.length ; i++){
        	var iddemande = demande[i];
        	
        	scriptFormule += "$('.ER_"+iddemande+" #cholesterol_total_1').keyup( function () {"+
        	                    
        	                    "var cholesterol_total_1 = $('.ER_"+iddemande+" #cholesterol_total_1').val();"+
    	   	                    "if(cholesterol_total_1){"+
        		                    "var valeur_mmol = cholesterol_total_1 * 2.587;"+
        		                    "$('.ER_"+iddemande+" #cholesterol_total_2').val(valeur_mmol.toFixed(2));"+
        	                    "}else{	$('.ER_"+iddemande+" #cholesterol_total_2').val(null); }"+
        	                 
        	                 "}).change( function () {"+
        	                 
        	                    "var cholesterol_total_1 = $('.ER_"+iddemande+" #cholesterol_total_1').val();"+
 	   	                        "if(cholesterol_total_1){"+
     		                        "var valeur_mmol = cholesterol_total_1 * 2.587;"+
     		                        "$('.ER_"+iddemande+" #cholesterol_total_2').val(valeur_mmol.toFixed(2));"+
     	                        "}else{	$('.ER_"+iddemande+" #cholesterol_total_2').val(null); }"+
     	                 
        	                 "}).trigger('keyup');";
        	
    	}
    	scriptFormule += "</script>";
        
    	return scriptFormule;
    	
    }
    
    function gestionFormuleTriglycerides_TAD(demande){

    	var scriptFormule = "<script>";
    	for(var i=0 ; i<demande.length ; i++){
        	var iddemande = demande[i];
        	
        	scriptFormule += "$('.ER_"+iddemande+" #triglycerides_1').keyup( function () {"+
        	                    
        	                    "var triglycerides_1 = $('.ER_"+iddemande+" #triglycerides_1').val();"+
    	   	                    "if(triglycerides_1){"+
        		                    "var valeur_mmol = triglycerides_1 * 1.143;"+
        		                    "$('.ER_"+iddemande+" #triglycerides_2').val(valeur_mmol.toFixed(2));"+
        	                    "}else{	$('.ER_"+iddemande+" #triglycerides_2').val(null); }"+
        	                 
        	                 "}).change( function () {"+
        	                 
        	                    "var triglycerides_1 = $('.ER_"+iddemande+" #triglycerides_1').val();"+
 	   	                        "if(triglycerides_1){"+
     		                        "var valeur_mmol = triglycerides_1 * 1.143;"+
     		                        "$('.ER_"+iddemande+" #triglycerides_2').val(valeur_mmol.toFixed(2));"+
     	                        "}else{	$('.ER_"+iddemande+" #triglycerides_2').val(null); }"+
     	                 
        	                 "}).trigger('keyup');";
        	
    	}
    	scriptFormule += "</script>";
        
    	return scriptFormule;
    	
    }
    
    function gestionFormuleCholesterolHDL_TAD(demande){
    	
    	var scriptFormule = "<script>";
    	for(var i=0 ; i<demande.length ; i++){
        	var iddemande = demande[i];
        	
        	scriptFormule += "$('.ER_"+iddemande+" #cholesterol_HDL_1').keyup( function () {"+
        	                    
        	                    "var cholesterol_HDL_1 = $('.ER_"+iddemande+" #cholesterol_HDL_1').val();"+
    	   	                    "if(cholesterol_HDL_1){"+
        		                    "var valeur_mmol = cholesterol_HDL_1 * 2.587;"+
        		                    "$('.ER_"+iddemande+" #cholesterol_HDL_2').val(valeur_mmol.toFixed(2));"+
        	                    "}else{	$('.ER_"+iddemande+" #cholesterol_HDL_2').val(null); }"+
        	                 
        	                 "}).change( function () {"+
        	                 
        	                    "var cholesterol_HDL_1 = $('.ER_"+iddemande+" #cholesterol_HDL_1').val();"+
 	   	                        "if(cholesterol_HDL_1){"+
     		                        "var valeur_mmol = cholesterol_HDL_1 * 2.587;"+
     		                        "$('.ER_"+iddemande+" #cholesterol_HDL_2').val(valeur_mmol.toFixed(2));"+
     	                        "}else{	$('.ER_"+iddemande+" #cholesterol_HDL_2').val(null); }"+
     	                 
        	                 "}).trigger('keyup');";
        	
    	}
    	scriptFormule += "</script>";
        
    	return scriptFormule;
    }
    
    function gestionFormuleCholesterolLDL_TAD(demande){
    	
    	var scriptFormule = "<script>";
    	for(var i=0 ; i<demande.length ; i++){
        	var iddemande = demande[i];
        	
        	scriptFormule += "$('.ER_"+iddemande+" #cholesterol_LDL_1').keyup( function () {"+
        	                    
        	                    "var cholesterol_LDL_1 = $('.ER_"+iddemande+" #cholesterol_LDL_1').val();"+
    	   	                    "if(cholesterol_LDL_1){"+
        		                    "var valeur_mmol = cholesterol_LDL_1 * 2.587;"+
        		                    "$('.ER_"+iddemande+" #cholesterol_LDL_2').val(valeur_mmol.toFixed(2));"+
        	                    "}else{	$('.ER_"+iddemande+" #cholesterol_LDL_2').val(null); }"+
        	                 
        	                 "}).change( function () {"+
        	                 
        	                    "var cholesterol_LDL_1 = $('.ER_"+iddemande+" #cholesterol_LDL_1').val();"+
 	   	                        "if(cholesterol_LDL_1){"+
     		                        "var valeur_mmol = cholesterol_LDL_1 * 2.587;"+
     		                        "$('.ER_"+iddemande+" #cholesterol_LDL_2').val(valeur_mmol.toFixed(2));"+
     	                        "}else{	$('.ER_"+iddemande+" #cholesterol_LDL_2').val(null); }"+
     	                 
        	                 "}).trigger('keyup');";
        	
    	}
    	scriptFormule += "</script>";
        
    	return scriptFormule;
    
    }
    
    function gestionFormuleRapportCholHdl_TAD(demande){
    	
    	var scriptFormule = "<script> ";
    	for(var i=0 ; i<demande.length ; i++){
        	var iddemande = demande[i];
        	
        	scriptFormule += "$('.ER_"+iddemande+" #cholesterol_total_1, .ER_"+iddemande+" #cholesterol_HDL_1').keyup( function () {"+
        	                    
        	                    "var cholesterol_total_1 = $('.ER_"+iddemande+" #cholesterol_total_1').val();"+
    		                    "var cholesterol_HDL_1 = $('.ER_"+iddemande+" #cholesterol_HDL_1').val();"+
        	
    		                    "if( cholesterol_total_1 == '' || cholesterol_total_1 == 0 || cholesterol_HDL_1 == '' || cholesterol_HDL_1 == 0 ){"+   
    		            			"$('.ER_"+iddemande+" .rapport_chol_hdl table').toggle(false);"+
    		            		"}"+
    		                	"else if( cholesterol_total_1 && cholesterol_HDL_1 ){"+
    		                	
    		                		"var rapport = cholesterol_total_1/cholesterol_HDL_1;"+
    		                		"$('.ER_"+iddemande+" .rapport_chol_hdl').toggle(true);"+
    		                		"$('.ER_"+iddemande+" #rapport_chol_hdl').val(rapport.toFixed(2));"+
    		                		"$('.ER_"+iddemande+" .rapport_chol_hdl table').toggle(true);"+
    		                		
    		                		//Affichage de la conclusion du rapport
    		                		"if(rapport >= 4.5 && rapport <= 5){"+
    		                			"$('.ER_"+iddemande+" #conclusion_rapport_chol_hdl').html('<span style=\"color: orange; float: left;\"> Risque dath&eacute;rog&egrave;ne faible </span>');"+
    		                		"}else if(rapport > 5 && rapport <= 6.5){"+
    		                			"$('.ER_"+iddemande+" #conclusion_rapport_chol_hdl').html('<span style=\"color: orange; float: left;\"> Risque dath&eacute;rog&egrave;ne mod&eacute;r&eacute; </span>');"+
    		                		"}else if(rapport > 6.5){"+
    		                			"$('.ER_"+iddemande+" #conclusion_rapport_chol_hdl').html('<span style=\"color: red; float: left;\"> Risque dath&eacute;rog&egrave;ne &eacute;lev&eacute; </span>');"+
    		                		"}else{"+
    		                			"$('.ER_"+iddemande+" #conclusion_rapport_chol_hdl').html('<span style=\"color: green; float: left;\"> RAS </span>');"+
    		                		"}"+
    		                		
    	                        "}"+
    		                    
    		                    
        	                 "}).change( function() { "+

        	                    "var cholesterol_total_1 = $('.ER_"+iddemande+" #cholesterol_total_1').val();"+
 		                        "var cholesterol_HDL_1 = $('.ER_"+iddemande+" #cholesterol_HDL_1').val();"+
     	
 		                        "if( cholesterol_total_1 == '' || cholesterol_total_1 == 0 || cholesterol_HDL_1 == '' || cholesterol_HDL_1 == 0 ){"+   
 		            			    "$('.ER_"+iddemande+" .rapport_chol_hdl table').toggle(false);"+
 		            		    "}"+
 		                	    "else if( cholesterol_total_1 && cholesterol_HDL_1 ){"+
 		                	
 		                		    "var rapport = cholesterol_total_1/cholesterol_HDL_1;"+
 		                		    "$('.ER_"+iddemande+" .rapport_chol_hdl').toggle(true);"+
 		                		    "$('.ER_"+iddemande+" #rapport_chol_hdl').val(rapport.toFixed(2));"+
 		                		    "$('.ER_"+iddemande+" .rapport_chol_hdl table').toggle(true);"+
 		                		
 		                		    //Affichage de la conclusion du rapport
 		                		    "if(rapport >= 4.5 && rapport <= 5){"+
 		                			    "$('.ER_"+iddemande+" #conclusion_rapport_chol_hdl').html('<span style=\"color: orange; float: left;\"> Risque dath&eacute;rog&egrave;ne faible </span>');"+
 		                		    "}else if(rapport > 5 && rapport <= 6.5){"+
 		                			    "$('.ER_"+iddemande+" #conclusion_rapport_chol_hdl').html('<span style=\"color: orange; float: left;\"> Risque dath&eacute;rog&egrave;ne mod&eacute;r&eacute; </span>');"+
 		                		    "}else if(rapport > 6.5){"+
 		                			    "$('.ER_"+iddemande+" #conclusion_rapport_chol_hdl').html('<span style=\"color: red; float: left;\"> Risque dath&eacute;rog&egrave;ne &eacute;lev&eacute; </span>');"+
 		                		    "}else{"+
 		                			    "$('.ER_"+iddemande+" #conclusion_rapport_chol_hdl').html('<span style=\"color: green; float: left;\"> RAS </span>');"+
 		                		    "}"+
 		                		
 	                            "}"+
        	                 
        	                 "}).trigger('keyup');";
        	
    	}
    	
    	scriptFormule += "</script>";
        
    	return scriptFormule;
    
    }
    
    
    function gestionFormuleElectrophoreseProteines_TAD(demande){
    	
    	var scriptFormule = "<script>";
    	for(var i=0 ; i<demande.length ; i++){
        	var iddemande = demande[i];
        	
        	//Albumine
        	scriptFormule += "$('.ER_"+iddemande+" #albumine, .ER_"+iddemande+" #proteine_totale').keyup( function () { "+
        
        	                     "var albumine = $('.ER_"+iddemande+" #albumine').val();"+
    		                     "var proteine_totale = $('.ER_"+iddemande+" #proteine_totale').val();"+
    		                     "if(albumine && proteine_totale){"+ 
    		                         "var albumine_abs = (albumine * proteine_totale)/100;"+
    		                         "$('.ER_"+iddemande+" #albumine_abs').val(albumine_abs.toFixed(1));"+
    	                         "}else{"+
    		                         "$('.ER_"+iddemande+" #albumine_abs').val(null);"+
    		                     "}"+
    				
        	                 "}).change( function() { "+
        	                 
        	                     "var albumine = $('.ER_"+iddemande+" #albumine').val();"+
		                         "var proteine_totale = $('.ER_"+iddemande+" #proteine_totale').val();"+
		                         "if(albumine && proteine_totale){"+ 
		                             "var albumine_abs = (albumine * proteine_totale)/100;"+
		                             "$('.ER_"+iddemande+" #albumine_abs').val(albumine_abs.toFixed(1));"+
	                             "}else{"+
		                             "$('.ER_"+iddemande+" #albumine_abs').val(null);"+
		                         "}"+
		                     
        	                 "}).trigger('keyup');";
        	
        	//Alpha 1
        	scriptFormule += "$('.ER_"+iddemande+" #alpha_1, .ER_"+iddemande+" #proteine_totale').keyup( function () { "+
            
                                 "var alpha_1 = $('.ER_"+iddemande+" #alpha_1').val();"+
                                 "var proteine_totale = $('.ER_"+iddemande+" #proteine_totale').val();"+
                                 "if(alpha_1 && proteine_totale){"+ 
                                     "var alpha_1_abs = (alpha_1 * proteine_totale)/100;"+
                                     "$('.ER_"+iddemande+" #alpha_1_abs').val(alpha_1_abs.toFixed(1));"+
                                 "}else{"+
                                     "$('.ER_"+iddemande+" #alpha_1_abs').val(null);"+
                                 "}"+

                             "}).change( function() { "+
        
                                 "var alpha_1 = $('.ER_"+iddemande+" #alpha_1').val();"+
                                 "var proteine_totale = $('.ER_"+iddemande+" #proteine_totale').val();"+
                                 "if(alpha_1 && proteine_totale){"+ 
                                     "var alpha_1_abs = (alpha_1 * proteine_totale)/100;"+
                                     "$('.ER_"+iddemande+" #alpha_1_abs').val(alpha_1_abs.toFixed(1));"+
                                 "}else{"+
                                     "$('.ER_"+iddemande+" #alpha_1_abs').val(null);"+
                                 "}"+
        
                             "}).trigger('keyup');";
        	
        	//Alpha 2
        	scriptFormule += "$('.ER_"+iddemande+" #alpha_2, .ER_"+iddemande+" #proteine_totale').keyup( function () { "+
            
                                 "var alpha_2 = $('.ER_"+iddemande+" #alpha_2').val();"+
                                 "var proteine_totale = $('.ER_"+iddemande+" #proteine_totale').val();"+
                                 "if(alpha_2 && proteine_totale){"+ 
                                     "var alpha_2_abs = (alpha_2 * proteine_totale)/100;"+
                                     "$('.ER_"+iddemande+" #alpha_2_abs').val(alpha_2_abs.toFixed(1));"+
                                 "}else{"+
                                     "$('.ER_"+iddemande+" #alpha_2_abs').val(null);"+
                                 "}"+

                             "}).change( function() { "+
        
                                 "var alpha_2 = $('.ER_"+iddemande+" #alpha_2').val();"+
                                 "var proteine_totale = $('.ER_"+iddemande+" #proteine_totale').val();"+
                                 "if(alpha_2 && proteine_totale){"+ 
                                     "var alpha_2_abs = (alpha_2 * proteine_totale)/100;"+
                                     "$('.ER_"+iddemande+" #alpha_2_abs').val(alpha_2_abs.toFixed(1));"+
                                 "}else{"+
                                     "$('.ER_"+iddemande+" #alpha_2_abs').val(null);"+
                                 "}"+
        
                             "}).trigger('keyup');";
        	
        	//Beta 1
        	scriptFormule += "$('.ER_"+iddemande+" #beta_1, .ER_"+iddemande+" #proteine_totale').keyup( function () { "+
            
                                 "var beta_1 = $('.ER_"+iddemande+" #beta_1').val();"+
                                 "var proteine_totale = $('.ER_"+iddemande+" #proteine_totale').val();"+
                                 "if(beta_1 && proteine_totale){"+ 
                                     "var beta_1_abs = (beta_1 * proteine_totale)/100;"+
                                     "$('.ER_"+iddemande+" #beta_1_abs').val(beta_1_abs.toFixed(1));"+
                                 "}else{"+
                                     "$('.ER_"+iddemande+" #beta_1_abs').val(null);"+
                                 "}"+

                             "}).change( function() { "+
        
                                 "var beta_1 = $('.ER_"+iddemande+" #beta_1').val();"+
                                 "var proteine_totale = $('.ER_"+iddemande+" #proteine_totale').val();"+
                                 "if(beta_1 && proteine_totale){"+ 
                                     "var beta_1_abs = (beta_1 * proteine_totale)/100;"+
                                     "$('.ER_"+iddemande+" #beta_1_abs').val(beta_1_abs.toFixed(1));"+
                                 "}else{"+
                                     "$('.ER_"+iddemande+" #beta_1_abs').val(null);"+
                                 "}"+
        
                             "}).trigger('keyup');";
        	
        	//Beta 2
        	scriptFormule += "$('.ER_"+iddemande+" #beta_2, .ER_"+iddemande+" #proteine_totale').keyup( function () { "+
            
                                 "var beta_2 = $('.ER_"+iddemande+" #beta_2').val();"+
                                 "var proteine_totale = $('.ER_"+iddemande+" #proteine_totale').val();"+
                                 "if(beta_2 && proteine_totale){"+ 
                                     "var beta_2_abs = (beta_2 * proteine_totale)/100;"+
                                     "$('.ER_"+iddemande+" #beta_2_abs').val(beta_2_abs.toFixed(1));"+
                                 "}else{"+
                                     "$('.ER_"+iddemande+" #beta_2_abs').val(null);"+
                                 "}"+

                             "}).change( function() { "+
        
                                 "var beta_2 = $('.ER_"+iddemande+" #beta_2').val();"+
                                 "var proteine_totale = $('.ER_"+iddemande+" #proteine_totale').val();"+
                                 "if(beta_2 && proteine_totale){"+ 
                                     "var beta_2_abs = (beta_2 * proteine_totale)/100;"+
                                     "$('.ER_"+iddemande+" #beta_2_abs').val(beta_2_abs.toFixed(1));"+
                                 "}else{"+
                                     "$('.ER_"+iddemande+" #beta_2_abs').val(null);"+
                                 "}"+
        
                             "}).trigger('keyup');";
    	
        	//Gamma
        	scriptFormule += "$('.ER_"+iddemande+" #gamma, .ER_"+iddemande+" #proteine_totale').keyup( function () { "+
            
                                 "var gamma = $('.ER_"+iddemande+" #gamma').val();"+
                                 "var proteine_totale = $('.ER_"+iddemande+" #proteine_totale').val();"+
                                 "if(gamma && proteine_totale){"+ 
                                     "var gamma_abs = (gamma * proteine_totale)/100;"+
                                     "$('.ER_"+iddemande+" #gamma_abs').val(gamma_abs.toFixed(1));"+
                                 "}else{"+
                                     "$('.ER_"+iddemande+" #gamma_abs').val(null);"+
                                 "}"+

                             "}).change( function() { "+
        
                                 "var gamma = $('.ER_"+iddemande+" #gamma').val();"+
                                 "var proteine_totale = $('.ER_"+iddemande+" #proteine_totale').val();"+
                                 "if(gamma && proteine_totale){"+ 
                                     "var gamma_abs = (gamma * proteine_totale)/100;"+
                                     "$('.ER_"+iddemande+" #gamma_abs').val(gamma_abs.toFixed(1));"+
                                 "}else{"+
                                     "$('.ER_"+iddemande+" #gamma_abs').val(null);"+
                                 "}"+
        
                             "}).trigger('keyup');";
    	   
    	}
    	
    	scriptFormule += "</script>";
        
    	return scriptFormule;
    }
    
    
    function gestionFormuleElectrophoreseHemoglobine(demande){
    	
    	var scriptFormule = "<script>";
    	for(var i=0 ; i<demande.length ; i++){
        	var iddemande = demande[i];

        	scriptFormule += "$('.ER_"+iddemande+" #electro_hemo_moins').toggle(false);";
        	
        	scriptFormule += "$('.ER_"+iddemande+" #electro_hemo_plus').click( function (){ "+
        	
        	                     "var nbLigne = $('.ER_"+iddemande+" #electro_hemo tr').length;"+
	    	                     "$('.ER_"+iddemande+" #electro_hemo_moins').toggle(true);"+
	    	                     "if(nbLigne < 10){"+
	    	                        "var html ='<tr id=electro_hemo_\'+nbLigne+\' class=ligneAnanlyse style=width: 100%; >"+
	    	                        
	                                           "<td style=\"width: 45%;\"><label class=\"lab1\"><span style=\"font-weight: bold;\" >  <input id=\"electro_hemo_label_\'+nbLigne+\'\" type=\"text\" style=\"font-weight: bold; padding-right: 5px; margin-right: 30px;\"  maxlength=4 onkeydown=\"if(event.keyCode==32) return false;\"> </span></label></td>"+
	                                           "<td style=\"width: 35%;\"><label class=\"lab2\" style=\"padding-top: 5px;\"> <input id=\"electro_hemo_valeur_\'+nbLigne+\'\" type=\"number\" step=\"any\"> % </label></td>"+
	                                           "<td style=\"width: 20%;\"><label class=\"lab3\" style=\"padding-top: 5px; width: 80%;\"> </label></td>"+
	                                           
	    	                                   "</tr>';"+
	    	                     
	                                 "$('.ER_"+iddemande+" #electro_hemo_'+(nbLigne-1)).after(html);"+
	    	         		    	
	    	         		    	"if(nbLigne == 9){"+
	    	         		    		"$('.ER_"+iddemande+" #electro_hemo_plus').toggle(false);"+
	    	         		    	"}"+
	    	         	    	 "}"+
	    	         	    	
	    	         	     "});";
        	
        	scriptFormule += "$('.ER_"+iddemande+" #electro_hemo_moins').click( function (){ "+
        	                      
        	                     "var nbLigne = $('.ER_"+iddemande+" #electro_hemo tr').length;"+
        	                     
        	                     "if(nbLigne > 2){"+
        	         		    	"$('.ER_"+iddemande+" #electro_hemo_'+(nbLigne-1)).remove();"+
        	         		    	"if(nbLigne == 3){"+ 
        	         		    		"$('.ER_"+iddemande+" #electro_hemo_moins').toggle(false);"+
        	         		    	"}"+
        	         		    	
        	         		    	"if(nbLigne == 10){"+
        	         		    		"$('.ER_"+iddemande+" #electro_hemo_plus').toggle(true);"+
        	         		    	"}"+
        	         	    	 "}"+
        	
        	
        	
        	                 "});";
        	

    	}
    	
    	scriptFormule += "</script>";
        
    	return scriptFormule;
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    //Impression des r�sultats des analyses demand�es
    //Impression des r�sultats des analyses demand�es
    //Impression des r�sultats des analyses demand�es
    function imprimerResultatsAnalysesDemandees(iddemande){
    	
    	if(iddemande){
    		var vart = tabUrl[0]+'public/technicien/impression-resultats-analyses-demandees';
    		var FormulaireImprimerAnalysesDemandees = document.getElementById("FormulaireImprimerDemandesAnalyses");
    		FormulaireImprimerAnalysesDemandees.setAttribute("action", vart);
    		FormulaireImprimerAnalysesDemandees.setAttribute("method", "POST");
    		FormulaireImprimerAnalysesDemandees.setAttribute("target", "_blank");
    		
    		//Ajout dynamique de champs dans le formulaire
    		var champ = document.createElement("input");
    		champ.setAttribute("type", "hidden");
    		champ.setAttribute("name", 'iddemande');
    		champ.setAttribute("value", iddemande);
    		FormulaireImprimerAnalysesDemandees.appendChild(champ);
    		$("#ImprimerDemandesAnalyses").trigger('click');
    	}
    	
    }
    
    
    
    
    //Recupère la liste des analyses par type
    function getListeAnalysesParType(id){
    	
    	$.ajax({
    		type: 'POST',
    		url: tabUrl[0]+'public/technicien/get-liste-analyses',
    		data:{'id':id},
    		success: function(data) {    
    			var result = jQuery.parseJSON(data);  
    			$("#listeAnalyseParType").html(result);
    		},
        
    		error:function(e){ console.log(e); alert("Une erreur interne est survenue! voir -- laboratoire --- getListeAnalysesAction() "); },
    		dataType: "html"
    	});

    }
    

    function popupFermer() {
    	$(null).w2overlay(null);
    }
    
    
    function diagnostic(id){
    	
    	$('#diagnostic_'+id).w2overlay({ html: "" +
    		"" +
    		"<div style='border-bottom:1px solid green; height: 30px; background: #f9f9f9; width: 300px; text-align:center; padding-top: 10px; font-size: 13px; color: green; font-weight: bold;'>Diagnostic</div>" +
    		"<div style='height: 195px; width: 300px; padding-top:10px; text-align:center;'>" +
    		"<textarea style='height: 90%; width: 95%; max-height: 90%; max-width: 95%;' id='diagnostic_demande' > </textarea>" +
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