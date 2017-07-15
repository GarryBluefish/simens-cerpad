var base_url = window.location.toString();
var tabUrl = base_url.split("public");
                   
function AppelLecteurMp3(id_admission){

	var chemin = tabUrl[0]+'public/consultation/afficher-mp3-protocole';

	$.ajax({
		url: chemin ,
		type: 'POST',
		data: { 'id_admission':id_admission },
		success: function (response) {
			var result = jQuery.parseJSON(response);
			$('#AfficherLecteur').html(result);
		},
		error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
		dataType: "html"
	});
    
}
                    
function scriptAjoutMp3(){
                    
	var id_admission = $('#id_admission').val();
	
	$(function () {
		$('#my_form').change(function (e) {
			//On empêche le navigateur de soumettre le formulaire
			e.preventDefault();
			var $form = $(this);
			var formdata = (window.FormData) ? new FormData($form[0]) : null;
			var data = (formdata !== null) ? formdata : $form.serialize();
                    	        
			if($("#fichier")[0].files[0].size > 12582912 ){
				alert("La taille maximale est depassee: Choisissez une taille <= 12Mo"); 
				return false;
			}

			$("#mp3Waiting").html('<table> <tr> <td style=" font-size: 12px; color: red;"> Chargement en cours </td> </tr>  <tr> <td align="center"> <img style="margin-top: 20px; width: 30px; height: 30px;" src="../images/loading/Chargement_5.gif" /> </td> </tr> </table>');

			var chemin = tabUrl[0]+'public/consultation/ajouter-mp3-protocole';
			$.ajax({
				url: chemin ,
				type: $form.attr('method'),
				contentType: false, // obligatoire pour de l'upload
				processData: false, // obligatoire pour de l'upload
				data: data,
				success: function (response) {
					// La réponse du serveur
					var result = jQuery.parseJSON(response); //alert(result); return false;
					if(result == 0){
						alert('format non reconnu: Choissisez un fichier mp3');
						$("#mp3Waiting").html("");
						return false;
					}
					$.ajax({
						url: tabUrl[0]+'public/consultation/inserer-bd-mp3-protocole',
						type: $form.attr('method'),
						data: {'id_admission':id_admission, 'nom_file': result},
						success: function (response) { 
							// La réponse du serveur
							var result = jQuery.parseJSON(response); 
							$('#AfficherLecteur').empty(); 
							$('#AfficherLecteur').html(result);
							$("#mp3Waiting").html("");
						}
					});
				}
			});
			
			
		});
	});
}
                    
                   	
function supprimerAudioMp3(id){ 
	
	var id_admission = $("#id_admission").val();
	var chemin = tabUrl[0]+'public/consultation/supprimer-mp3-protocole';
	
	$("#mp3Waiting").html('<table> <tr> <td style=" font-size: 12px; color: red;"> Suppression en cours </td> </tr>  <tr> <td align="center"> <img style="margin-top: 20px; width: 30px; height: 30px;" src="../images/loading/Chargement_5.gif" /> </td> </tr> </table>');
	
	$.ajax({
		url: chemin ,
		type: 'POST',
		data: {'id':id, 'id_admission':id_admission },
		success: function (response) {
			var result = jQuery.parseJSON(response);

			$('#AfficherLecteur').empty(); 
			$('#AfficherLecteur').html(result);
			$("#mp3Waiting").html("");
            
		}
	});
    
	stopPropagation();
}
                   	
                   	
                   	