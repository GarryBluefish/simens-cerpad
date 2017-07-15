 var base_url = window.location.toString();
 var tabUrl = base_url.split("public");
    
 /************************************************************************************************************************/
 /************************************************************************************************************************/
 /************************************************************************************************************************/
 var compteur = 0; 
 var updateHopital = 0;
 var updateService = 0;
 var updateActe = 0;
 
 function ajouterDonneesParametrages(){
	 $('#ajouterHopitaux, #ajouterServices, #ajouterActes').hover(function(){
		  $(this).css({'font-weight':'bold', 'color':'green', 'font-size': '17px'});
		},function(){
		  $(this).css({'font-weight':'normal', 'color':'black', 'font-size': '14px'});
		});
	 
	 
	 $('#ajouterHopitaux, #ajouterServices, #ajouterActes').click(function(){
		 $(this).css({'font-weight':'normal', 'color':'black', 'font-size': '14px'});
	 });
	 
	 //****** Ajouter un hopital ******************************
	 //****** Ajouter un hopital ******************************
	 //****** Ajouter un hopital ******************************
	 $('#ajouterHopitaux').click(function(){
		 updateHopital = 0;
		 
		 $.ajax({
           type: 'POST',
           url: tabUrl[0]+'public/admin/gestion-des-hopitaux' ,
           data:{'id':1},
           success: function(data) {
        	   var result = jQuery.parseJSON(data);  
            
        	   $('#contenuTable').html(result); 
        	   $('#wait').toggle(false);
        	   $('#VueDetailsHopital').toggle(false);
        	   
        	   $('#contenu').fadeOut(function(){
        		   $("#titre").replaceWith("<div id='titre' style='font-family: police2; color: green; font-size: 20px; font-weight: bold; padding-left:20px;'><img src='/simens/public/images_icons/Table16X16.png' /> GESTION DES H&Ocirc;PITAUX </div>");
        		   $('#contenuTable').fadeIn('fast');
        		   ListeDesHopitaux();
        		   
        		   //Au click sur terminer
        		   $('#terminer').click(function(){
        			   $('#contenuTable').fadeOut(function(){
        				   $("#titre").replaceWith("<div id='titre' style='font-family: police2; color: green; font-size: 20px; font-weight: bold; padding-left:20px;'><iS style='font-size: 25px;'>&curren;</iS> PARAM&Eacute;TRAGES </div>");
        				   $('#contenu').fadeIn('fast'); 
        			   });
        		   });
        		   
        		   //Au click sur annuler
        		   $('#annuler').click(function(){
        			   $('#nom').val('');
        			   $('#region').val('');
        		       $('#departement').val('');
        		       $('#directeur').val('');
	    			   $('#note').val('');
	    			   updateHopital = 0;
        		       
        			   return false;
        		   });
        		   
        		 //Au click sur enregistrer
        		   $('#enregistrer').click(function(){ 
        			   var nom = $('#nom').val();
        			   var region = $('#region').val();
        		       var departement = $('#departement').val();
        		       var directeur = $('#directeur').val();
        		       var note = $('#note').val();
        		       
        		       if(nom == '' || region == '' || departement == null || departement == '' || directeur == ''){
        		    	   return true;
        		       } else {
        		    	   $.ajax({
        		    		   type: 'POST',
        		    		   url: tabUrl[0]+'public/admin/ajouter-hopital' ,
        		    		   data:{'nom': nom, 'departement':departement , 'directeur':directeur, 'note':note, 'updateHopital': updateHopital},
        		    		   success: function() {
        		    	        	 
        		    			   $('#nom').val('');
        		    			   $('#region').val('');
        		    			   $('#departement').val('');$('#departement').html('');
        		    			   $('#directeur').val('');
        		    			   $('#note').val('');
       		    				   $('#labelInfos').replaceWith('<span id="labelInfos"> Cr&eacute;ation d\'un nouvel h&ocirc;pital </span>');
       		    				   updateHopital = 0;

        	            		   
        		    			   //POUR LE RAFFRAICHISSEMENT DU FORMULAIRE
        		    			   if(compteur == 9){
        		    				   $('#contenuTable').toggle(false);
        		    				   $('#wait').toggle(true);
        		    				   $('#ajouterHopitaux').trigger('click');
        		    				   compteur = 0;
        		    			   }else{
        		    				   ListeDesHopitaux(); compteur++;
        		    			   }
        		    		   },
        		    	   
        		    	   });
        		    	   
            			   return false;
        		       }
         		   });
        		   
        	   });
           
           },
           error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
           dataType: "html"
		 
		 });
		
	 }); 
	 
	 //****** Ajouter un service ******************************
	 //****** Ajouter un service ******************************
	 //****** Ajouter un service ******************************
	 $("#ajouterServices").click(function(){
		 updateService = 0;
		 
		 $.ajax({
           type: 'POST',
           url: tabUrl[0]+'public/admin/gestion-des-services' ,
           data:{'id':1},
           success: function(data) {
        	   var result = jQuery.parseJSON(data);  

        	   $('#contenuTable').html(result); 
        	   $('#VueDetailsService').toggle(false);
        	   
        	   $('#contenu').fadeOut(function(){
          		   $("#titre").replaceWith("<div id='titre' style='font-family: police2; color: green; font-size: 20px; font-weight: bold; padding-left:20px;'><img src='/simens/public/images_icons/Table16X16.png' /> GESTION DES SERVICES </div>");
          		   $('#contenuTable').fadeIn('fast');
          		   ListeDesServices();
        	   });
            
        	   //Au click sur terminer
    		   $('#terminer').click(function(){
    			   $('#contenuTable').fadeOut(function(){
    				   $("#titre").replaceWith("<div id='titre' style='font-family: police2; color: green; font-size: 20px; font-weight: bold; padding-left:20px;'><iS style='font-size: 25px;'>&curren;</iS> PARAM&Eacute;TRAGES </div>");
    				   $('#contenu').fadeIn('fast'); 
    			   });
    		   });
    		   
    		   
    		   //Au click sur annuler
    		   $('#annuler').click(function(){
    			   $('#nom').val('');
    			   $('#domaine').val('');
    		       $('#tarif').val('');
    		       updateService = 0;
    		       
    			   return false;
    		   });
    		   
    		   //Au click sur enregistrer
    		   $('#enregistrer').click(function(){
    			   var nom = $('#nom').val();
    			   var domaine = $('#domaine').val();
    		       var tarif = $('#tarif').val();
    		       
    		       if(nom == '' || domaine == '' || tarif == ''){
    		    	   return true;
    		       } else {
    		    	   $.ajax({
    		    		   type: 'POST',
    		    		   url: tabUrl[0]+'public/admin/ajouter-service' ,
    		    		   data:{'nom': nom, 'domaine':domaine , 'tarif':tarif, 'updateService':updateService },
    		    		   success: function(data) {
    		    			   $('#nom').val('');
    		    			   $('#domaine').val('');
    		    		       $('#tarif').val('');
   		    				   $('#labelInfos').replaceWith('<span id="labelInfos"> Cr&eacute;ation d\'un nouveau service </span>');
   		    				   updateService = 0;
   		    				   
   		    				   ListeDesServices();
    		    		   },
    		    	   
    		    	   });
    		    	   
        			   return false;
    		       }
     		   });
    		   
           }
           
		 });
		 
	 });
	 
	 //****** Ajouter un acte ******************************
	 //****** Ajouter un acte ******************************
	 //****** Ajouter un acte ******************************
	 $('#ajouterActes').click(function(){
		 updateActe = 0;
		 
		 $.ajax({
           type: 'POST',
           url: tabUrl[0]+'public/admin/gestion-des-actes' ,
           data:{'id':1},
           success: function(data) {
        	   var result = jQuery.parseJSON(data);  

        	   $('#contenuTable').html(result); 
        	   $('#VueDetailsActe').toggle(false);
        	   
        	   $('#contenu').fadeOut(function(){
          		   $("#titre").replaceWith("<div id='titre' style='font-family: police2; color: green; font-size: 20px; font-weight: bold; padding-left:20px;'><img src='/simens/public/images_icons/Table16X16.png' /> GESTION DES ACTES </div>");
          		   $('#contenuTable').fadeIn('fast');
          		   ListeDesActes();
        	   });
            
        	   //Au click sur terminer
    		   $('#terminer').click(function(){ 
    			   $('#contenuTable').fadeOut(function(){
    				   $("#titre").replaceWith("<div id='titre' style='font-family: police2; color: green; font-size: 20px; font-weight: bold; padding-left:20px;'><iS style='font-size: 25px;'>&curren;</iS> PARAM&Eacute;TRAGES </div>");
    				   $('#contenu').fadeIn('fast'); 
    			   });
    		   });
    		   
    		   
    		   //Au click sur annuler
    		   $('#annuler').click(function(){
    			   $('#designation').val('');
    		       $('#tarif').val('');
    		       $('#labelInfos').replaceWith('<span id="labelInfos"> Cr&eacute;ation d\'un nouvel acte </span>');
    		       updateActe = 0;
    		       
    			   return false;
    		   });
    		   
    		   //Au click sur enregistrer
    		   $('#enregistrer').click(function(){
    			   var designation = $('#designation').val();
    		       var tarif = $('#tarif').val();
    		       
    		       if(designation == '' || tarif == ''){
    		    	   return true;
    		       } else {
    		    	   $.ajax({
    		    		   type: 'POST',
    		    		   url: tabUrl[0]+'public/admin/ajouter-acte' ,
    		    		   data:{'designation': designation, 'tarif':tarif, 'updateActe':updateActe },
    		    		   success: function(data) {
    		    			   $('#designation').val('');
    		    		       $('#tarif').val('');
   		    				   $('#labelInfos').replaceWith('<span id="labelInfos"> Cr&eacute;ation d\'un nouvel acte </span>');
   		    				   updateActe = 0;
   		    				   
   		    				   ListeDesActes();
    		    		   },
    		    	   
    		    	   });
    		    	   
        			   return false;
    		       }
     		   });
    		   
           }
           
		 });
		 
	 });
 }

 //*********************************** AFFICHAGE DE LA LISTE DES HOPITAUX *************************************************/
 //*********************************** AFFICHAGE DE LA LISTE DES HOPITAUX *************************************************/
 //*********************************** AFFICHAGE DE LA LISTE DES HOPITAUX *************************************************/
 function ListeDesHopitaux(){
     var  oTable = $('#listeDesHopitauxAjax').dataTable
 	 ({
 		"bDestroy":true,
 		 "sPaginationType": "full_numbers",
 		 "aLengthMenu": [3,5],
 		"iDisplayLength": 5,
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
 		 "sAjaxSource": ""+tabUrl[0]+"public/admin/liste-hopitaux-ajax", 
 	 }); 
     
     var asInitVals = new Array();
	
     //le filtre du select
     $('#filter_statut').change(function() {					
    	 oTable.fnFilter( this.value );
     });
	
     $("tfoot input").keyup( function () {
    	 /* Filter on the column (the index) of this element */
    	 oTable.fnFilter( this.value, $("tfoot input").index(this) );
     });
	
     /*
	 * Support functions to provide a little bit of 'user friendlyness' to the textboxes in 
	 * the footer
	 */
     $("tfoot input").each( function (i) {
    	 asInitVals[i] = this.value;
     });
	
     $("tfoot input").focus( function () {
    	 if ( this.className == "search_init" )
    	 {
    		 this.className = "";
    		 this.value = "";
    	 }
     });
	
     $("tfoot input").blur( function (i) {
    	 if ( this.value == "" )
    	 {
    		 this.className = "search_init";
    		 this.value = asInitVals[$("tfoot input").index(this)];
    	 }
     });
	
 }
 
 function getDepartement(id){
	 $.ajax({
         type: 'POST',
         url: tabUrl[0]+'public/admin/get-departements' ,
         data:{'id': id},
         success: function(data) {
        	 var result = jQuery.parseJSON(data); 
        	 $('#departement').html(result);
         },
	 });
 }
 
 function visualiserDetails(id){
	 $('#FormulaireAjouterHopitaux').toggle(false);
	 $('#VueDetailsHopital').toggle(true);
	 
	 //Afficher l'interface (formulaire) de saisie des hopitaux
	 $('#PlusFormulaireAjouterHopitaux').click(function(){
		 updateHopital = 0; 
		 $('#labelInfos').replaceWith('<span id="labelInfos"> Cr&eacute;ation d\'un nouvel h&ocirc;pital </span>');

		 $('#VueDetailsHopital').fadeOut(function(){
			 $('#nom').val('');
			 $('#region').val('');
		     $('#departement').val(''); $('#departement').html('');
		     $('#directeur').val('');
			 $('#note').val('');
			   
			 $('#FormulaireAjouterHopitaux').toggle(true);
		 });
	 });
	 
	 $.ajax({
         type: 'POST',
         url: tabUrl[0]+'public/admin/get-infos-hopital' ,
         data:{'id': id},
         success: function(data) {
        	 var result = jQuery.parseJSON(data); 
        	 $('#scriptVue').html(result);
         },
	 });
	 
 }

 function modifier(id){
	 updateHopital = id;
	 $('#labelInfos').replaceWith('<span id="labelInfos"> Modification des infos de l\'h&ocirc;pital </span>');
	 $('#VueDetailsHopital').toggle(false);
	 $('#FormulaireAjouterHopitaux').toggle(true);
	 
	 $.ajax({
         type: 'POST',
         url: tabUrl[0]+'public/admin/get-infos-modification-hopital' ,
         data:{'id': id},
         success: function(data) {
        	 var result = jQuery.parseJSON(data); 
        	 $('#scriptVue').html(result);
         },
	 });
 }
 
 //*********************************** AFFICHAGE DE LA LISTE DES SERVICES *************************************************/
 //*********************************** AFFICHAGE DE LA LISTE DES SERVICES *************************************************/
 //*********************************** AFFICHAGE DE LA LISTE DES SERVICES *************************************************/
 function ListeDesServices(){
     var  oTable2 = $('#listeDesServicesAjax').dataTable
 	 ({
 		"bDestroy":true,
 		 "sPaginationType": "full_numbers",
 		 "aLengthMenu": [3,5],
 		"iDisplayLength": 5,
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
 		 "sAjaxSource": ""+tabUrl[0]+"public/admin/liste-services-ajax", 
 	 }); 
     
     var asInitVals = new Array();
	
     //le filtre du select
     $('#filter_statut').change(function() {					
    	 oTable2.fnFilter( this.value );
     });
	
     $("tfoot input").keyup( function () {
    	 /* Filter on the column (the index) of this element */
    	 oTable2.fnFilter( this.value, $("tfoot input").index(this) );
     });
	
     /*
	 * Support functions to provide a little bit of 'user friendlyness' to the textboxes in 
	 * the footer
	 */
     $("tfoot input").each( function (i) {
    	 asInitVals[i] = this.value;
     });
	
     $("tfoot input").focus( function () {
    	 if ( this.className == "search_init" )
    	 {
    		 this.className = "";
    		 this.value = "";
    	 }
     });
	
     $("tfoot input").blur( function (i) {
    	 if ( this.value == "" )
    	 {
    		 this.className = "search_init";
    		 this.value = asInitVals[$("tfoot input").index(this)];
    	 }
     });
	
 }
 
 function visualiserDetailsService(id) {
	 
	 $('#FormulaireAjouterService').toggle(false);
	 $('#VueDetailsService').toggle(true);
	 
	 //Afficher l'interface (formulaire) de saisie des services
	 $('#PlusFormulaireAjouterService').click(function(){
		 updateService = 0; 
		 $('#labelInfos').replaceWith('<span id="labelInfos"> Cr&eacute;ation d\'un nouveau service </span>');

		 $('#VueDetailsService').fadeOut(function(){
			 $('#nom').val('');
			 $('#domaine').val('');
		     $('#tarif').val('');
		     $('#FormulaireAjouterService').toggle(true);
			   
			 $('#PlusFormulaireAjouterService').toggle(true);
		 });
	 });
	 
	 $.ajax({
         type: 'POST',
         url: tabUrl[0]+'public/admin/get-infos-service' ,
         data:{'id': id},
         success: function(data) {
        	 var result = jQuery.parseJSON(data); 
        	 $('#scriptVueService').html(result);
         },
	 });
	 
 }
 
 function modifierService(id){
	 updateService = id;
	 $('#labelInfos').replaceWith('<span id="labelInfos"> Modification des infos du service </span>');
	 $('#VueDetailsService').toggle(false);
	 $('#FormulaireAjouterService').toggle(true);
	 
	 $.ajax({
         type: 'POST',
         url: tabUrl[0]+'public/admin/get-infos-modification-service' ,
         data:{'id': id},
         success: function(data) {
        	 var result = jQuery.parseJSON(data);
        	 $('#scriptVueService').html(result);
         },
	 });
 }

 function popupFermer() {
	 $(null).w2overlay(null);
 }

 function supprimerService(id){
	$('#service_'+id).w2overlay({ html: "" +
		"" +
		"<div style='border-bottom:1px solid green; height: 30px; background: #f9f9f9; width: 200px; text-align:center; padding-top: 10px; font-size: 13px; color: green; font-weight: bold;'>Confirmer la suppresion</div>" +
		"<div style='height: 50px; width: 200px; padding-top:10px; text-align:center;'>" +
		"<button class='btn' style='cursor:pointer;' onclick='popupFermer(); return false;'>Annuler</button>" +
		"<button class='btn' style='cursor:pointer;' onclick='supprimeService("+id+"); return false;'>Supprimer</button>" +
		"</div>" +
		"" 
	});
 }
 
 function supprimeService(id){
	 $(null).w2overlay(null);
	 $.ajax({
         type: 'POST',
         url: tabUrl[0]+'public/admin/supprimer-service' ,
         data:{'id': id},
         success: function(data) {
        	 var result = jQuery.parseJSON(data); 
        	 if(result == 0){
        		 alert('impossible de supprimer ce service des agents y sont affectes');
        	 } else if(result == 1){
              	 ListeDesServices();
        	 }
         },
	 });
 }
 
 
 //*********************************** AFFICHAGE DE LA LISTE DES ACTES *************************************************/
 //*********************************** AFFICHAGE DE LA LISTE DES ACTES *************************************************/
 //*********************************** AFFICHAGE DE LA LISTE DES ACTES *************************************************/
 function ListeDesActes(){
     var  oTable3 = $('#listeDesActesAjax').dataTable
 	 ({
 		"bDestroy":true,
 		 "sPaginationType": "full_numbers",
 		 "aLengthMenu": [3,5],
 		"iDisplayLength": 5,
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
 		 "sAjaxSource": ""+tabUrl[0]+"public/admin/liste-actes-ajax", 
 	 }); 
     
     var asInitVals = new Array();
	
     //le filtre du select
     $('#filter_statut').change(function() {					
    	 oTable3.fnFilter( this.value );
     });
	
     $("tfoot input").keyup( function () {
    	 /* Filter on the column (the index) of this element */
    	 oTable3.fnFilter( this.value, $("tfoot input").index(this) );
     });
	
     /*
	 * Support functions to provide a little bit of 'user friendlyness' to the textboxes in 
	 * the footer
	 */
     $("tfoot input").each( function (i) {
    	 asInitVals[i] = this.value;
     });
	
     $("tfoot input").focus( function () {
    	 if ( this.className == "search_init" )
    	 {
    		 this.className = "";
    		 this.value = "";
    	 }
     });
	
     $("tfoot input").blur( function (i) {
    	 if ( this.value == "" )
    	 {
    		 this.className = "search_init";
    		 this.value = asInitVals[$("tfoot input").index(this)];
    	 }
     });
	
 }
 
 function visualiserDetailsActe(id) {
	 
	 $('#FormulaireAjouterActe').toggle(false);
	 $('#VueDetailsActe').toggle(true);
	 
	 //Afficher l'interface (formulaire) de saisie des actes
	 $('#PlusFormulaireAjouterActe').click(function(){
		 updateService = 0; 
		 $('#labelInfos').replaceWith('<span id="labelInfos"> Cr&eacute;ation d\'un nouvel acte </span>');

		 $('#VueDetailsActe').fadeOut(function(){
			 $('#designation').val('');
		     $('#tarif').val('');
		     $('#FormulaireAjouterActe').toggle(true);
			   
			 $('#PlusFormulaireAjouterActe').toggle(true);
		 });
	 });
	 
	 $.ajax({
         type: 'POST',
         url: tabUrl[0]+'public/admin/get-infos-acte' ,
         data:{'id': id},
         success: function(data) {
        	 var result = jQuery.parseJSON(data);
        	 $('#scriptVueActe').html(result);
         },
	 });
	 
 }
 
 function modifierActe(id){
	 updateActe = id;
	 $('#labelInfos').replaceWith('<span id="labelInfos"> Modification des infos de l\'acte </span>');
	 $('#VueDetailsActe').toggle(false);
	 $('#FormulaireAjouterActe').toggle(true);
	 
	 $.ajax({
         type: 'POST',
         url: tabUrl[0]+'public/admin/get-infos-modification-acte' ,
         data:{'id': id},
         success: function(data) {
        	 var result = jQuery.parseJSON(data);
        	 $('#scriptVueActe').html(result);
         },
	 });
 }

 function supprimerActe(id){
	$('#acte_'+id).w2overlay({ html: "" +
		"" +
		"<div style='border-bottom:1px solid green; height: 30px; background: #f9f9f9; width: 200px; text-align:center; padding-top: 10px; font-size: 13px; color: green; font-weight: bold;'>Confirmer la suppresion</div>" +
		"<div style='height: 50px; width: 200px; padding-top:10px; text-align:center;'>" +
		"<button class='btn' style='cursor:pointer;' onclick='popupFermer(); return false;'>Annuler</button>" +
		"<button class='btn' style='cursor:pointer;' onclick='supprimeActe("+id+"); return false;'>Supprimer</button>" +
		"</div>" +
		"" 
	});
 }
 
 function supprimeActe(id){
	 $(null).w2overlay(null);
	 $('#designation').val('');
     $('#tarif').val('');
	 $.ajax({
         type: 'POST',
         url: tabUrl[0]+'public/admin/supprimer-acte' ,
         data:{'id': id},
         success: function(data) {
        	 var result = jQuery.parseJSON(data); 
        	 if(result == 0){
        		 alert('impossible de supprimer cet acte car il est prescrit a un patient');
        	 } else if(result == 1){
              	 ListeDesActes();
        	 }
         },
	 });
 }
