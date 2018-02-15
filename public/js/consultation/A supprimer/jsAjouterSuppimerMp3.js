var base_url = window.location.toString();
var tabUrl = base_url.split("public");
$("#AfficherLecteur").hover(function(){ $('#temoinAudio').val(1); });
$("#AfficherLecteurInstrumental").hover(function(){ $('#temoinAudio').val(2); });
                   
                    function AppelLecteurMp3(){
                      var chemin = tabUrl[0]+'public/consultation/afficher-mp3';
                      var id_cons = $('#id_cons').val(); 
                      $.ajax({
 	                    url: chemin ,
                        type: 'POST',
                        data: {'id_cons':id_cons, 'type':1}, //Type = 1 signifie traitement chirurgical
                        success: function (response) {
     	                            var result = jQuery.parseJSON(response); 
     	                            $('#AfficherLecteur').html(result);
                        },
                       error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
                       dataType: "html"
 
                      });
                    }
                    AppelLecteurMp3();
                    
                    function scriptAjoutMp3(){
                    	    $('#my_form').change(function (e) {
                    	        // On empêche le navigateur de soumettre le formulaire
                    	        e.preventDefault();
                    	        var id_cons = $('#id_cons').val(); 
                    	        var $form = $(this);
                    	        var formdata = (window.FormData) ? new FormData($form[0]) : null;
                    	        var data = (formdata !== null) ? formdata : $form.serialize();
                    	        
                    	        if($("#fichier")[0].files[0].size > 12582912 ){
                    	        	alert("La taille maximale est depassee: Choisissez une taille <= 12Mo"); 
                    	        	return false;
                    	        }
                    	        
                    	        var type = $('#temoinAudio').val();
                    	        
                   	        var chemin = tabUrl[0]+'public/consultation/ajouter-mp3';
                   	        $.ajax({
                   	        	url: chemin ,
                   	            type: $form.attr('method'),
                   	            contentType: false, // obligatoire pour de l'upload
                   	            processData: false, // obligatoire pour de l'upload
                   	            data: data,
                   	            success: function (response) { 
                   	                // La réponse du serveur
                   	            	var result = jQuery.parseJSON(response); 
                   	            	if(result == 0){
                   	            		alert('format non reconnu: Choissisez un fichier mp3');
                   	            		return false;
                   	            	}
                   	            	$.ajax({
                           	        	url: tabUrl[0]+'public/consultation/inserer-bd-mp3',
                           	            type: $form.attr('method'),
                           	            data: {'id_cons':id_cons, 'type': type , 'nom_file': result},
                           	            success: function (response) { 
                           	                // La réponse du serveur
                           	            	var result = jQuery.parseJSON(response); 
                       	            		$('#AfficherLecteur').empty(); 
                           	            	$('#AfficherLecteur').html(result);
                           	            }
                           	        });
                   	            }
                   	        });
                   		});

                    }
                    
                   	function supprimerAudioMp3(id){ 
                   		var chemin = tabUrl[0]+'public/consultation/supprimer-mp3';
                   		var id_cons = $('#id_cons').val(); 
                   		var type = $('#temoinAudio').val();
                   	    $.ajax({
                   	    	url: chemin ,
                   	        type: 'POST',
                   	        data: {'id':id, 'id_cons':id_cons, 'type': type},
                   	        success: function (response) {
                   	        	var result = jQuery.parseJSON(response);
                   	        	if(type == 1){
                   	        		$('#AfficherLecteur').empty(); 
                       	        	$('#AfficherLecteur').html(result);
                   	        	} else if(type == 2){
                   	        		$('#AfficherLecteurInstrumental').empty(); 
                    	            $('#AfficherLecteurInstrumental').html(result);
                   	        	}
                   	        	
                   	        }
                   	    });
                   	    stopPropagation();
                   	}
                   	
                   	
                   	
                   	//POUR LES TRAITEMENTS INSTRUMENTAUX
                   	//POUR LES TRAITEMENTS INSTRUMENTAUX
                    //POUR LES TRAITEMENTS INSTRUMENTAUX
                    //POUR LES TRAITEMENTS INSTRUMENTAUX
                    //POUR LES TRAITEMENTS INSTRUMENTAUX
                   	function AppelLecteurMp3_Instrumental(){
                        var chemin = tabUrl[0]+'public/consultation/afficher-mp3';
                        var id_cons = $('#id_cons').val();
                        $.ajax({
   	                    url: chemin ,
                          type: 'POST',
                          data: {'id_cons':id_cons, 'type':2} , //Type = 2 signifie traitement instrumental
                          success: function (response) {
       	                            var result = jQuery.parseJSON(response); 
       	                            $('#AfficherLecteurInstrumental').html(result);
                          },
                         error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
                         dataType: "html"
   
                        });
                      }
                   	  AppelLecteurMp3_Instrumental();
                   	  
                   	 function scriptAjoutMp3_Instrumental(){
                     	    $('#my_form2').change(function (e) {
                     	        // On empêche le navigateur de soumettre le formulaire
                     	        e.preventDefault();
                     	        var id_cons = $('#id_cons').val(); 
                     	        var $form = $(this);
                     	        var formdata = (window.FormData) ? new FormData($form[0]) : null;
                     	        var data = (formdata !== null) ? formdata : $form.serialize();
                     	        
                     	        if($("#ajouter2 input")[0].files[0].size > 12582912 ){
                   	        	  alert("La taille maximale est depassee: Choisissez une taille <= 12Mo"); 
                   	        	  return false;
                     	        }
                     	       
                     	        var type = $('#temoinAudio').val();
                     	        
                    	        var chemin = tabUrl[0]+'public/consultation/ajouter-mp3';
                    	        $.ajax({
                    	        	url: chemin ,
                    	            type: $form.attr('method'),
                    	            contentType: false, // obligatoire pour de l'upload
                    	            processData: false, // obligatoire pour de l'upload
                    	            data: data,
                    	            success: function (response) { 
                    	                // La réponse du serveur
                    	            	var result = jQuery.parseJSON(response); 
                    	            	if(result == 0){
                       	            		alert('format non reconnu: Choissisez un fichier mp3');
                       	            		return false;
                       	            	}
                    	            	$.ajax({
                            	        	url: tabUrl[0]+'public/consultation/inserer-bd-mp3',
                            	            type: $form.attr('method'),
                            	            data: {'id_cons':id_cons, 'type': type , 'nom_file': result},
                            	            success: function (response) { 
                            	                // La réponse du serveur
                            	            	var result = jQuery.parseJSON(response); 
                           	            		$('#AfficherLecteurInstrumental').empty(); 
                                	            $('#AfficherLecteurInstrumental').html(result);
                            	            }
                            	        });
                    	            }
                    	        });
                    		});

                     }
                   	 
                   	 
