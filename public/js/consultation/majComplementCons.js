var base_url = window.location.toString();
var tabUrl = base_url.split("public");

$(function() { 
	/**
	 * CACHER TOUTES LES LIGNES DES EXAMENS MORPHOLOGIQUES
	 * CACHER TOUTES LES LIGNES DES EXAMENS MORPHOLOGIQUES
	 */
	 $('.imageRadio').toggle(false);
	 $('.imageEchographie').toggle(false);
	 $('.imageIRM').toggle(false);
	 $('.imageScanner').toggle(false);
	 $('.imageFibroscopie').toggle(false);

	/**
	 * RECUPER LE TYPE D'EXAMEN AU SURVOL SUR RADIO
	 */
	 $('.imageRadio').hover(function(){
		 document.getElementById('typeExamen_tmp').value = 8;
	 });
	 /**
      * RECUPER LE TYPE D'EXAMEN AU SURVOL SUR ECHOGRAPHIE
	  */
	 $('.imageEchographie').hover(function(){
		 document.getElementById('typeExamen_tmp').value = 9;
	 });
	 /**
      * RECUPER LE TYPE D'EXAMEN AU SURVOL SUR IRM
	  */
	 $('.imageIRM').hover(function(){
		 document.getElementById('typeExamen_tmp').value = 10;
	 });
	 /**
      * RECUPER LE TYPE D'EXAMEN AU SURVOL SUR SCANNER
	  */
	 $('.imageScanner').hover(function(){
		 document.getElementById('typeExamen_tmp').value = 11;
	 });
	 /**
      * RECUPER LE TYPE D'EXAMEN AU SURVOL SUR FIBROSCOPIE
	  */
	 $('.imageFibroscopie').hover(function(){
		 document.getElementById('typeExamen_tmp').value = 12;
	 });
});

/***
*=======================================================================================================================================
*=======================================================================================================================================
*=======================================================================================================================================
*================ ***************SUPPRESSION ======== SUPPRESSION ======== SUPPRESSION*************** ===============
**======================================================================================================================================
*=======================================================================================================================================
*=======================================================================================================================================
*/
/**
 * Appel du pop-up de confirmation de la suppression
 * Appel du pop-up de confirmation de la suppression
 * Appel du pop-up de confirmation de la suppression
 */
function raffraichirimagesExamensMorphologiques(typeExamen)
{
	 var id_cons = $("#id_cons").val();
     $.ajax({
        type: 'POST',
        url: tabUrl[0]+'public/consultation/imagesExamensMorphologiques',
        data: {'id_cons':id_cons, 'ajout':0, 'typeExamen':typeExamen},
        success: function(data) {
            var result = jQuery.parseJSON(data);
            if(typeExamen == 8){
            	if(result != "") {
            		$("#pika2").fadeOut(function(){ 
                    	$("#pika").html(result);
                    	return false;
                    });
            	} else {
            		$html = "<div id='pika2'>"+
				             "<div class='pikachoose' style='height: 210px;'>"+
                                "<ul id='pikame' class='jcarousel-skin-pika'>"+
                                "</ul>"+
                             "</div>"+
				             "</div>"+

			                 "<script>"+
					         "scriptExamenMorpho();"+
					         "</script>";
					         		
            		$("#pika2").fadeOut(function(){ 
                    	$("#pika").html($html);
                    	return false;
                    });
            	}
            	
            } else
            	if(typeExamen ==9){
            		if(result != "") {
                		$("#pika4").fadeOut(function(){ 
                        	$("#pika3").html(result);
                        });
                	} else { 
                		$html = "<div id='pika4'>"+
        			             "<div class='pikachoose' style='height: 210px;'>"+
                                    "<ul id='pikameEchographie' class='jcarousel-skin-pika'>"+
                                    "</ul>"+
                                 "</div>"+
        			             "</div>"+

        		                 "<script>"+
        				         "$(function(){ $('.imageEchographie').toggle(true);});"+ //On fait apparaitre au niveau du controller
        				         "scriptEchographieExamenMorpho();"+
        				         "</script>";
        				         		
                		$("#pika4").fadeOut(function(){ 
                        	$("#pika3").html($html);
                        });
                	}
            	} else 
                    	if(typeExamen ==10){
                    		if(result != "") {
                        		$("#pika6").fadeOut(function(){ 
                                	$("#pika5").html(result);
                                });
                        	} else { 
                        		$html = "<div id='pika6'>"+
                			             "<div class='pikachoose' style='height: 210px;'>"+
                                            "<ul id='pikameIRM' class='jcarousel-skin-pika'>"+
                                            "</ul>"+
                                         "</div>"+
                			             "</div>"+

                		                 "<script>"+
                				         "$(function(){ $('.imageIRM').toggle(true);});"+ //On fait apparaitre au niveau du controller
                				         "scriptIRMExamenMorpho();"+
                				         "</script>";
                				         		
                        		$("#pika6").fadeOut(function(){ 
                                	$("#pika5").html($html);
                                });
                        	}
                    	} else 
                    		if(typeExamen == 11){
                        		if(result != "") {
                            		$("#pika8").fadeOut(function(){ 
                                    	$("#pika7").html(result);
                                    });
                            	} else { 
                            		$html = "<div id='pika8'>"+
                    			             "<div class='pikachoose' style='height: 210px;'>"+
                                                "<ul id='pikameScanner' class='jcarousel-skin-pika'>"+
                                                "</ul>"+
                                             "</div>"+
                    			             "</div>"+

                    		                 "<script>"+
                    				         "$(function(){ $('.imageScanner').toggle(true);});"+ //On fait apparaitre au niveau du controller
                    				         "scriptScannerExamenMorpho();"+
                    				         "</script>";
                    				         		
                            		$("#pika8").fadeOut(function(){ 
                                    	$("#pika7").html($html);
                                    });
                            	}
                        	} else 
                        		if(typeExamen == 12){
                            		if(result != "") {
                                		$("#pika10").fadeOut(function(){ 
                                        	$("#pika9").html(result);
                                        });
                                	} else { 
                                		$html = "<div id='pika10'>"+
                        			             "<div class='pikachoose' style='height: 210px;'>"+
                                                    "<ul id='pikameFibroscopie' class='jcarousel-skin-pika'>"+
                                                    "</ul>"+
                                                 "</div>"+
                        			             "</div>"+

                        		                 "<script>"+
                        				         "$(function(){ $('.imageFibroscopie').toggle(true);});"+ //On fait apparaitre au niveau du controller
                        				         "scriptFibroscopieExamenMorpho();"+
                        				         "</script>";
                        				         		
                                		$("#pika10").fadeOut(function(){ 
                                        	$("#pika9").html($html);
                                        });
                                	}
                            	}
            
             
      },
      error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
      dataType: "html"
    });
 }

function confirmerSuppression(id, typeExamen){
 $( "#confirmation" ).dialog({
  resizable: false,
  height:170,
  width:420,
  autoOpen: false,
  modal: true,
  buttons: {
      "Oui": function() {
          $( this ).dialog( "close" );

          var id_cons = $("#id_cons").val();
          var chemin = tabUrl[0]+'public/consultation/supprimerImage';
          $.ajax({
              type: 'POST',
              url: chemin ,
              data: {'id_cons':id_cons, 'id':id , 'typeExamen':typeExamen},
              success: function() {
            	  raffraichirimagesExamensMorphologiques(typeExamen);
            	  return false;
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

/**
 * Appel de la suppression
 * Appel de la suppression
 * Appel de la suppression
 */

function supprimerImage(id){
	var typeExamen = $('#typeExamen_tmp').val();
	confirmerSuppression(id, typeExamen);
	$("#confirmation").dialog('open');
}

/***
*=======================================================================================================================================
*=======================================================================================================================================
*=======================================================================================================================================
*================ ***************RADIO ======== RADIO ======== RADIO*************** ===============
**======================================================================================================================================
*=======================================================================================================================================
*=======================================================================================================================================
*/
/**
 * Application du plugin PIKACHOOSE
 * Application du plugin PIKACHOOSE
 * Application du plugin PIKACHOOSE
 */
function scriptExamenMorpho() {
   	   var a = function(self){
       	  self.anchor.fancybox();
        };
        $("#pikame").PikaChoose({buildFinished:a,carousel:true,carouselOptions:{wrap:'circular'}});
}
/***
 * PREMIER APPEL POUR LE CHARGEMENT DES IMAGES RADIO (EXAMEN MORPHOLOGIQUES)
 * PREMIER APPEL POUR LE CHARGEMENT DES IMAGES RADIO (EXAMEN MORPHOLOGIQUES)
 * PREMIER APPEL POUR LE CHARGEMENT DES IMAGES RADIO (EXAMEN MORPHOLOGIQUES)
 */
function getimagesExamensMorphologiques()
{
	 var id_cons = $("#id_cons").val();
     $.ajax({
        type: 'POST',
        url: tabUrl[0]+'public/consultation/imagesExamensMorphologiques',
        data: {'id_cons':id_cons, 'ajout':0, 'typeExamen':8},
        success: function(data) {
        	Recupererimage();
            var result = jQuery.parseJSON(data);
            if(result != "") {
        		$("#pika2").fadeOut(function(){ 
                	$("#pika").html(result);
                });
        	} else { 
        		$html = "<div id='pika2'>"+
			             "<div class='pikachoose' style='height: 210px;'>"+
                            "<ul id='pikame' class='jcarousel-skin-pika'>"+
                            "</ul>"+
                         "</div>"+
			             "</div>"+

		                 "<script>"+
				         "$(function(){ $('.imageRadio').toggle(true);});"+ //On fait apparaitre au niveau du controller
				         "scriptExamenMorpho();"+
				         "</script>";
                	
        		$("#pika").html($html);
        	}
            return false;
      },
      error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
      dataType: "html"
    });
 }

	/**
	 * AJOUTER DES IMAGES RADIOS
	 * AJOUTER DES IMAGES RADIOS
	 * AJOUTER DES IMAGES RADIOS
	 */
    function Recupererimage(){
    	$('#AjoutImage input[type="file"]').change(function() {
    	   var file = $(this);
 		   var reader = new FileReader;
 		   
	       reader.onload = function(event) { 
	    		var img = new Image();
                //Ici on recupere l'image 
			    document.getElementById('fichier_tmp').value = img.src = event.target.result;
			    
			    /**
			     * CODE AJAX POUR L'AJOUT DE L'IMAGE DANS LA BASE DE DONNEES
			     */
			    var typeExamen = $('#typeExamen_tmp').val();
			    var id_cons = $("#id_cons").val();
		    	$.ajax({
		            type: 'POST',
		            url: tabUrl[0]+'public/consultation/imagesExamensMorphologiques',
		            data: {'ajout':1 , 'id_cons':id_cons , 'fichier_tmp': $("#fichier_tmp").val() , 'typeExamen':typeExamen},
		            success: function(data) {
		                var result = jQuery.parseJSON(data); 
		                if(result !=""){
		                	$("#pika2").fadeOut(function(){ 
			                	$("#pika").html(result);
			                	return false;
			                });
		                }else {
		                	alert('En cours de traitement! Veuillez attendre les resultats ...'); return false;
		                }
		          },
		          error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
		            dataType: "html"
		        });
		    	/**
			     * FIN CODE AJAX POUR L'AJOUT DE L'IMAGE DANS LA BASE DE DONNEES
			     */
    	};
    	reader.readAsDataURL(file[0].files[0]);
    	
      });
    }
    
/***
*=======================================================================================================================================
*=======================================================================================================================================
*=======================================================================================================================================
*================ ***************ECHOGRAPHIE ======== ECHOGRAPHIE ======== ECHOGRAPHIE*************** ===============
**======================================================================================================================================
*=======================================================================================================================================
*=======================================================================================================================================
*/
/**
 * Application du plugin PIKACHOOSE
 * Application du plugin PIKACHOOSE
 * Application du plugin PIKACHOOSE
 */
function scriptEchographieExamenMorpho() {
   	   var a = function(self){
       	  self.anchor.fancybox();
        };
        $("#pikameEchographie").PikaChoose({buildFinished:a,carousel:true,carouselOptions:{wrap:'circular'}});
}
/***
 * PREMIER APPEL POUR LE CHARGEMENT DES IMAGES ECHOGRAPHIE (EXAMEN MORPHOLOGIQUES)
 * PREMIER APPEL POUR LE CHARGEMENT DES IMAGES ECHOGRAPHIE (EXAMEN MORPHOLOGIQUES)
 * PREMIER APPEL POUR LE CHARGEMENT DES IMAGES ECHOGRAPHIE (EXAMEN MORPHOLOGIQUES)
 */
function getimagesEchographieExamensMorphologiques()
{
	 var id_cons = $("#id_cons").val();
     $.ajax({
        type: 'POST',
        url: tabUrl[0]+'public/consultation/imagesExamensMorphologiques',
        data: {'id_cons':id_cons, 'ajout':0, 'typeExamen':9},
        success: function(data) {
        	RecupererImageEchographie();
            var result = jQuery.parseJSON(data);
            if(result != "") {
        		$("#pika4").fadeOut(function(){ 
                	$("#pika3").html(result);
                });
        	} else { 
        		$html = "<div id='pika4'>"+
			             "<div class='pikachoose' style='height: 210px;'>"+
                            "<ul id='pikameEchographie' class='jcarousel-skin-pika'>"+
                            "</ul>"+
                         "</div>"+
			             "</div>"+

		                 "<script>"+
				         "$(function(){ $('.imageEchographie').toggle(true);});"+ //On fait apparaitre au niveau du controller
				         "scriptEchographieExamenMorpho();"+
				         "</script>";
				       
        		$("#pika3").html($html);
        	}
            return false;
      },
      error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
      dataType: "html"
    });
 }

/**
 * AJOUTER DES IMAGES ECHOGRAPHIE
 * AJOUTER DES IMAGES ECHOGRAPHIE
 * AJOUTER DES IMAGES ECHOGRAPHIE
 */
function RecupererImageEchographie(){
	$('#AjoutImageEchographie input[type="file"]').change(function() {
	   var file = $(this);
		   var reader = new FileReader;
		   
       reader.onload = function(event) { 
    		var img = new Image();
            //Ici on recupere l'image 
		    document.getElementById('fichierEchographie_tmp').value = img.src = event.target.result;
		    
		    /**
		     * CODE AJAX POUR L'AJOUT DE L'IMAGE DANS LA BASE DE DONNEES
		     */
		    var id_cons = $("#id_cons").val();
	    	$.ajax({
	            type: 'POST',
	            url: tabUrl[0]+'public/consultation/imagesExamensMorphologiques',
	            data: {'ajout':1 , 'id_cons':id_cons , 'fichier_tmp': $("#fichierEchographie_tmp").val() , 'typeExamen':9},
	            success: function(data) {
	                var result = jQuery.parseJSON(data);
	                if(result != ""){
	                	$("#pika4").fadeOut(function(){ 
	                		$("#pika3").html(result);
	                		return false;
	                	}); 
	                }else {
	                	alert('En cours de traitement! Veuillez attendre les resultats ...'); return false;
	                }
	          },
	          error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
	            dataType: "html"
	        });
	    	/**
		     * FIN CODE AJAX POUR L'AJOUT DE L'IMAGE DANS LA BASE DE DONNEES
		     */
	};
	reader.readAsDataURL(file[0].files[0]);
	
  });
}


/***
*=======================================================================================================================================
*=======================================================================================================================================
*=======================================================================================================================================
*================ *************** IRM ======== IRM ======== IRM *************** ===============
**======================================================================================================================================
*=======================================================================================================================================
*=======================================================================================================================================
*/
/**
 * Application du plugin PIKACHOOSE
 * Application du plugin PIKACHOOSE
 * Application du plugin PIKACHOOSE
 */
function scriptIRMExamenMorpho() {
   	   var a = function(self){
       	  self.anchor.fancybox();
        };
        $("#pikameIRM").PikaChoose({buildFinished:a,carousel:true,carouselOptions:{wrap:'circular'}});
}
/***
 * PREMIER APPEL POUR LE CHARGEMENT DES IMAGES IRM (EXAMEN MORPHOLOGIQUES)
 * PREMIER APPEL POUR LE CHARGEMENT DES IMAGES IRM (EXAMEN MORPHOLOGIQUES)
 * PREMIER APPEL POUR LE CHARGEMENT DES IMAGES IRM (EXAMEN MORPHOLOGIQUES)
 */
function getimagesIRMExamensMorphologiques()
{
	 var id_cons = $("#id_cons").val();
     $.ajax({
        type: 'POST',
        url: tabUrl[0]+'public/consultation/imagesExamensMorphologiques',
        data: {'id_cons':id_cons, 'ajout':0, 'typeExamen':10},
        success: function(data) {
        	RecupererImageIRM();
            var result = jQuery.parseJSON(data);
            if(result != "") {
        		$("#pika6").fadeOut(function(){ 
                	$("#pika5").html(result);
                });
        	} else { 
        		$html = "<div id='pika6'>"+
			             "<div class='pikachoose' style='height: 210px;'>"+
                            "<ul id='pikameIRM' class='jcarousel-skin-pika'>"+
                            "</ul>"+
                         "</div>"+
			             "</div>"+

		                 "<script>"+
				         "$(function(){ $('.imageIRM').toggle(true);});"+ //On fait apparaitre au niveau du controller
				         "scriptIRMExamenMorpho();"+
				         "</script>";
                	
        		$("#pika5").html($html);
        	}
      },
      error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
      dataType: "html"
    });
 }

/**
 * AJOUTER DES IMAGES ECHOGRAPHIE
 * AJOUTER DES IMAGES ECHOGRAPHIE
 * AJOUTER DES IMAGES ECHOGRAPHIE
 */
function RecupererImageIRM(){
	$('#AjoutImageIRM input[type="file"]').change(function() {
	   var file = $(this);
		   var reader = new FileReader;
		   
       reader.onload = function(event) { 
    		var img = new Image();
            //Ici on recupere l'image 
		    document.getElementById('fichierIRM_tmp').value = img.src = event.target.result;
		    
		    /**
		     * CODE AJAX POUR L'AJOUT DE L'IMAGE DANS LA BASE DE DONNEES
		     */
		    var id_cons = $("#id_cons").val();
	    	$.ajax({
	            type: 'POST',
	            url: tabUrl[0]+'public/consultation/imagesExamensMorphologiques',
	            data: {'ajout':1 , 'id_cons':id_cons , 'fichier_tmp': $("#fichierIRM_tmp").val() , 'typeExamen':10},
	            success: function(data) {
	                var result = jQuery.parseJSON(data); 
	                if(result != ""){
	                	$("#pika6").fadeOut(function(){ 
	                		$("#pika5").html(result);
	                		return false;
	                	}); 
	                }else {
	                	alert('En cours de traitement! Veuillez attendre les resultats ...'); return false;
	                }
	          },
	          error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
	            dataType: "html"
	        });
	    	/**
		     * FIN CODE AJAX POUR L'AJOUT DE L'IMAGE DANS LA BASE DE DONNEES
		     */
	};
	reader.readAsDataURL(file[0].files[0]);
	
  });
}

/***
*=======================================================================================================================================
*=======================================================================================================================================
*=======================================================================================================================================
*================ *************** SCANNER ======== SCANNER ======== SCANNER *************** ===============
**======================================================================================================================================
*=======================================================================================================================================
*=======================================================================================================================================
*/
/**
 * Application du plugin PIKACHOOSE
 * Application du plugin PIKACHOOSE
 * Application du plugin PIKACHOOSE
 */
function scriptScannerExamenMorpho() {
   	   var a = function(self){
       	  self.anchor.fancybox();
        };
        $("#pikameScanner").PikaChoose({buildFinished:a,carousel:true,carouselOptions:{wrap:'circular'}});
}
/***
 * PREMIER APPEL POUR LE CHARGEMENT DES IMAGES SCANNER (EXAMEN MORPHOLOGIQUES)
 * PREMIER APPEL POUR LE CHARGEMENT DES IMAGES SCANNER (EXAMEN MORPHOLOGIQUES)
 * PREMIER APPEL POUR LE CHARGEMENT DES IMAGES SCANNER (EXAMEN MORPHOLOGIQUES)
 */
function getimagesScannerExamensMorphologiques()
{
	 var id_cons = $("#id_cons").val();
     $.ajax({
        type: 'POST',
        url: tabUrl[0]+'public/consultation/imagesExamensMorphologiques',
        data: {'id_cons':id_cons, 'ajout':0, 'typeExamen':11},
        success: function(data) {
        	RecupererImageScanner();
            var result = jQuery.parseJSON(data);
            if(result != "") {
        		$("#pika8").fadeOut(function(){ 
                	$("#pika7").html(result);
                });
        	} else { 
        		$html = "<div id='pika8'>"+
			             "<div class='pikachoose' style='height: 210px;'>"+
                            "<ul id='pikameScanner' class='jcarousel-skin-pika'>"+
                            "</ul>"+
                         "</div>"+
			             "</div>"+

		                 "<script>"+
				         "$(function(){ $('.imageScanner').toggle(true);});"+ //On fait apparaitre au niveau du controller
				         "scriptScannerExamenMorpho();"+
				         "</script>";
                	
        		$("#pika7").html($html);
        	}
      },
      error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
      dataType: "html"
    });
}

/**
 * AJOUTER DES IMAGES ECHOGRAPHIE
 * AJOUTER DES IMAGES ECHOGRAPHIE
 * AJOUTER DES IMAGES ECHOGRAPHIE
 */
function RecupererImageScanner(){
	$('#AjoutImageScanner input[type="file"]').change(function() {
	   var file = $(this);
		   var reader = new FileReader;
		   
       reader.onload = function(event) { 
    		var img = new Image();
            //Ici on recupere l'image 
		    document.getElementById('fichierScanner_tmp').value = img.src = event.target.result;
		    
		    /**
		     * CODE AJAX POUR L'AJOUT DE L'IMAGE DANS LA BASE DE DONNEES
		     */
		    var id_cons = $("#id_cons").val();
	    	$.ajax({
	            type: 'POST',
	            url: tabUrl[0]+'public/consultation/imagesExamensMorphologiques',
	            data: {'ajout':1 , 'id_cons':id_cons , 'fichier_tmp': $("#fichierScanner_tmp").val() , 'typeExamen':11},
	            success: function(data) {
	                var result = jQuery.parseJSON(data); 
	                if(result != ""){
	                	$("#pika8").fadeOut(function(){ 
	                		$("#pika7").html(result);
	                		return false;
	                	}); 
	                }else {
	                	alert('En cours de traitement! Veuillez attendre les resultats ...'); return false;
	                }
	          },
	          error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
	            dataType: "html"
	        });
	    	/**
		     * FIN CODE AJAX POUR L'AJOUT DE L'IMAGE DANS LA BASE DE DONNEES
		     */
	};
	reader.readAsDataURL(file[0].files[0]);
	
  });
}

/***
*=======================================================================================================================================
*=======================================================================================================================================
*=======================================================================================================================================
*================ *************** FIBROSCOPIE ======== FIBROSCOPIE ======== FIBROSCOPIE *************** ===============
**======================================================================================================================================
*=======================================================================================================================================
*=======================================================================================================================================
*/
/**
 * Application du plugin PIKACHOOSE
 * Application du plugin PIKACHOOSE
 * Application du plugin PIKACHOOSE
 */
function scriptFibroscopieExamenMorpho() {
   	   var a = function(self){
       	  self.anchor.fancybox();
        };
        $("#pikameFibroscopie").PikaChoose({buildFinished:a,carousel:true,carouselOptions:{wrap:'circular'}});
}
/***
 * PREMIER APPEL POUR LE CHARGEMENT DES IMAGES FIBROSCOPIE (EXAMEN MORPHOLOGIQUES)
 * PREMIER APPEL POUR LE CHARGEMENT DES IMAGES FIBROSCOPIE (EXAMEN MORPHOLOGIQUES)
 * PREMIER APPEL POUR LE CHARGEMENT DES IMAGES FIBROSCOPIE (EXAMEN MORPHOLOGIQUES)
 */
function getimagesFibroscopieExamensMorphologiques()
{
	 var id_cons = $("#id_cons").val();
     $.ajax({
        type: 'POST',
        url: tabUrl[0]+'public/consultation/imagesExamensMorphologiques',
        data: {'id_cons':id_cons, 'ajout':0, 'typeExamen':12},
        success: function(data) {
        	RecupererImageFibroscopie();
            var result = jQuery.parseJSON(data);
            if(result != "") {
        		$("#pika10").fadeOut(function(){ 
                	$("#pika9").html(result);
                });
        	} else { 
        		$html = "<div id='pika10'>"+
			             "<div class='pikachoose' style='height: 210px;'>"+
                            "<ul id='pikameFibroscopie' class='jcarousel-skin-pika'>"+
                            "</ul>"+
                         "</div>"+
			             "</div>"+

		                 "<script>"+
				         "$(function(){ $('.imageFibroscopie').toggle(true);});"+ //On fait apparaitre au niveau du controller
				         "scriptFibroscopieExamenMorpho();"+
				         "</script>";
				         		
        		$("#pika9").html($html);
        	}
      },
      error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
      dataType: "html"
    });
}

/**
 * AJOUTER DES IMAGES FIBROSCOPIE
 * AJOUTER DES IMAGES FIBROSCOPIE
 * AJOUTER DES IMAGES FIBROSCOPIE
 */
function RecupererImageFibroscopie(){
	$('#AjoutImageFibroscopie input[type="file"]').change(function() {
	   var file = $(this);
		   var reader = new FileReader;
		   
       reader.onload = function(event) { 
    		var img = new Image();
            //Ici on recupere l'image 
		    document.getElementById('fichierFibroscopie_tmp').value = img.src = event.target.result;
		    
		    /**
		     * CODE AJAX POUR L'AJOUT DE L'IMAGE DANS LA BASE DE DONNEES
		     */
		    var id_cons = $("#id_cons").val();
	    	$.ajax({
	            type: 'POST',
	            url: tabUrl[0]+'public/consultation/imagesExamensMorphologiques',
	            data: {'ajout':1 , 'id_cons':id_cons , 'fichier_tmp': $("#fichierFibroscopie_tmp").val() , 'typeExamen':12},
	            success: function(data) {
	                var result = jQuery.parseJSON(data); 
	                if(result != ""){
	                	$("#pika10").fadeOut(function(){ 
	                		$("#pika9").html(result);
	                		return false;
	                	}); 
	                }else {
	                	alert('En cours de traitement! Veuillez attendre les resultats ...'); return false;
	                }
	          },
	          error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
	            dataType: "html"
	        });
	    	/**
		     * FIN CODE AJAX POUR L'AJOUT DE L'IMAGE DANS LA BASE DE DONNEES
		     */
	};
	reader.readAsDataURL(file[0].files[0]);
	
  });
}




/**
 * ***************** AFFICHAGE DES EXAMENS N'AYANT PAS ENCORE D'IMAGES ****************
 * ***************** AFFICHAGE DES EXAMENS N'AYANT PAS ENCORE D'IMAGES ****************
 * ************************************************************************************
 */
function cacherExamenMorphoSansImages(){
}
var ind = 0;
function affichageExamenMorphoSansImages(element){
$(function(){
	setTimeout(function() {
		
		if(ind == 0){
			$('.imageRadio').toggle(false);
			$('.imageEchographie').toggle(false);
			$('.imageIRM').toggle(false);
			$('.imageScanner').toggle(false); 
			$('.imageFibroscopie').toggle(false);
			$('.bouton_valider_examen_morpho').toggle(false);
			ind++;
		}

		if(element == 8){ 
			$('.imageRadio').toggle(true); 
			$('.bouton_valider_examen_morpho').toggle(true);
		} 
		if(element == 9){ 
			$('.imageEchographie').toggle(true); 
			$('.bouton_valider_examen_morpho').toggle(true);
		} 
		if(element == 10){ 
			$('.imageIRM').toggle(true); 
			$('.bouton_valider_examen_morpho').toggle(true);
		} 
		if(element == 11){ 
			$('.imageScanner').toggle(true); 
			$('.bouton_valider_examen_morpho').toggle(true);
		} 
		if(element == 12){ 
			$('.imageFibroscopie').toggle(true); 
			$('.bouton_valider_examen_morpho').toggle(true);
		}
	}, 1500); //Attendre une seconde et demi avant de s'executer
});
}