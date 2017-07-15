var base_url = window.location.toString();
var tabUrl = base_url.split("public");
                   	 
//POUR LA VIDEO DU SCANNER DES EXAMENS COMPLEMENTAIRES 
//POUR LA VIDEO DU SCANNER DES EXAMENS COMPLEMENTAIRES 
//POUR LA VIDEO DU SCANNER DES EXAMENS COMPLEMENTAIRES 
//POUR LA VIDEO DU SCANNER DES EXAMENS COMPLEMENTAIRES 
//POUR LA VIDEO DU SCANNER DES EXAMENS COMPLEMENTAIRES 
//POUR LA VIDEO DU SCANNER DES EXAMENS COMPLEMENTAIRES 
function AppelLecteurVideo_Scanner(){

	var $id_cons = $('#id_cons').val();
		
	var chemin = tabUrl[0]+'public/consultation/afficher-video';
	$.ajax({
		url: chemin ,
		type: 'POST',
		data: {'id_cons': $id_cons},
		success: function (response) { 
			// La réponse du serveur
			var result = jQuery.parseJSON(response); 
                    	            	
			$('#AfficherLecteurVideoScanner').empty(); 
			$('#AfficherLecteurVideoScanner').html(result);
                  			   
			$('#divImageScannerPourMenu').toggle(false);
			$('#divVideoScannerPourMenu').toggle(false);
			GestionDuMenuVideosImages();
            
		}
	});
    
}

    
AppelLecteurVideo_Scanner();
                     

function GestionDuMenuVideosImages(){

	$('#IconeImagesMenu').click(function(){
		$('#divPourMenuImagesVideos').fadeOut(function(){
			$('#divImageScannerPourMenu').fadeIn('fast');
			
			$('#RetourDeImagesVersMenu').click(function(){
				$('#divImageScannerPourMenu').fadeOut(function(){
					$('#divPourMenuImagesVideos').fadeIn('fast');
				});
			});
		});
        
		return false;
        
	});
                  	   
    
	$('#IconeVideosMenu').click(function(){
	
		$('#divPourMenuImagesVideos').fadeOut(function(){
			$('#divVideoScannerPourMenu').fadeIn('fast');
                  			   
			$('#RetourDeVideosVersMenu').click(function(){
				$('#divVideoScannerPourMenu').fadeOut(function(){
					$('#divPourMenuImagesVideos').fadeIn('fast');
				});
			});
		});
        
		return false;
        
	});
    
}

function scriptAjoutVideo(){
 	$(function () {
 	    $('#my_form_video').change(function (e) {
 	        // On empêche le navigateur de soumettre le formulaire
 	        e.preventDefault();
 	        var id_cons = $('#id_cons').val(); 
 	        var $form = $(this);
 	        var formdata = (window.FormData) ? new FormData($form[0]) : null;
 	        var data = (formdata !== null) ? formdata : $form.serialize();

 	        if($("#fichier-video")[0].files[0].size > 12582912 ){
 	        	alert("La taille maximale est depassee: Choisissez une taille <= 12Mo"); 
	        	return false;
	        }
 	        
 	        var chemin = tabUrl[0]+'public/consultation/ajouter-video';
 	        
	        $.ajax({
	        	url: chemin ,
	            type: $form.attr('method'),
	            contentType: false, // obligatoire pour de l'upload
	            processData: false, // obligatoire pour de l'upload
	            data: data,
	            success: function (response) { 
	                // La réponse du serveur
	            	var result = jQuery.parseJSON(response); 

	            	$.ajax({
        	        	url: tabUrl[0]+'public/consultation/inserer-bd-video',
        	            type: $form.attr('method'), //Cela signifie 'POST'
        	            data: {'id_cons':id_cons, 'nom_file':result[0], 'type_file':result[1]},
        	            success: function (response) { 
        	                // La réponse du serveur
        	            	var result = jQuery.parseJSON(response); 
        	            	if(result == 0){
        	            		alert("format non reconnu"); return false;
        	            	} else {
            	            	$('#AfficherLecteurVideoScanner').empty(); 
            	    			$('#AfficherLecteurVideoScanner').html(result);
        	            	}

        	            }
        	        });
	            }
	        });
		});

 	});
 }

function supprimerVideo(id) {
    var chemin = tabUrl[0]+'public/consultation/supprimer-video';
    $.ajax({
    	url: chemin ,
    	type: 'POST',
        data: {'id' : id},
        success: function (response) { 
        	var result = jQuery.parseJSON(response); 
            //------------------------------------------------------------------
        	//------------------------------------------------------------------
        	//------------------------------------------------------------------
        	var $id_cons = $('#id_cons').val();
        	var chemin = tabUrl[0]+'public/consultation/afficher-video';

        	$.ajax({
        		url: chemin ,
        		type: 'POST',
        		data: {'id_cons': $id_cons},
        		success: function (response) { 
        			var result = jQuery.parseJSON(response); 
                            	            	
        			$('#AfficherLecteurVideoScanner').empty(); 
        			$('#AfficherLecteurVideoScanner').html(result);
                    
        		}
        	});
        	//------------------------------------------------------------------
        	//------------------------------------------------------------------
        	//------------------------------------------------------------------
        	
        }
    });
    
    stopPropagation();
}