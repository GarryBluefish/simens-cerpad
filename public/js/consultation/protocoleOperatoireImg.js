var base_url = window.location.toString();
var tabUrl = base_url.split("public");

/***
*=======================================================================================================================================
*=======================================================================================================================================
*=======================================================================================================================================
*================ ***************SUPPRESSION ======== SUPPRESSION ======== SUPPRESSION*************** ===============
**======================================================================================================================================
*=======================================================================================================================================
*=======================================================================================================================================
*/
function raffraichirimagesExamensMorphologiques(id_admission)
{
     $.ajax({
        type: 'POST',
        url: tabUrl[0]+'public/consultation/image-protocole-operatoire',
        data: { 'ajout':0 , 'id_admission': id_admission},
        success: function(data) {
            var result = jQuery.parseJSON(data);
            	
                if(result != "") {
            		$("#pika2").fadeOut(function(){ 
                    	$("#pika").html(result);
                    	$("#imageWaiting").html("");
                    	return false;
                    });
            	}
                
                $("#imageWaiting").html("");
            	
      },
      error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
      dataType: "html"
    });
 }

/**
 * Appel du pop-up de confirmation de la suppression
 * Appel du pop-up de confirmation de la suppression
 * Appel du pop-up de confirmation de la suppression
 */
function confirmerSuppression(id, id_admission){
 $( "#confirmation" ).dialog({
  resizable: false,
  height:170,
  width:420,
  autoOpen: false,
  modal: true,
  buttons: {
      "Oui": function() {
          $( this ).dialog( "close" );

          $("#imageWaiting").html('<table> <tr> <td style=" font-size: 12px; color: red;"> Suppression en cours </td> </tr>  <tr> <td align="center"> <img style="margin-top: 20px; width: 30px; height: 30px;" src="../images/loading/Chargement_5.gif" /> </td> </tr> </table>');
          
          var id_cons = $("#id_cons").val();
          var chemin = tabUrl[0]+'public/consultation/supprimer-image-protocole-operatoire';
          $.ajax({
              type: 'POST',
              url: chemin ,
              data: { 'id':id, 'id_admission':id_admission },
              success: function() {
            	  raffraichirimagesExamensMorphologiques(id_admission);
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
	var id_admission = $("#id_admission").val();
	
	confirmerSuppression(id, id_admission);
	$("#confirmation").dialog('open');
}

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
 * PREMIER APPEL POUR LE CHARGEMENT DES IMAGES 
 * PREMIER APPEL POUR LE CHARGEMENT DES IMAGES 
 * PREMIER APPEL POUR LE CHARGEMENT DES IMAGES 
 */
var entre = 0;
function getimagesExamensMorphologiques(id_admission)
{
	$.ajax({
          type: 'POST',
          url: tabUrl[0]+'public/consultation/image-protocole-operatoire',
          data: { 'ajout':0, 'id_admission': id_admission},
          success: function(data) {
        	  
        	  if(entre == 0){ RecupererImageAjouterDansBD(); entre = 1;}
        	  
              var result = jQuery.parseJSON(data);
              if(result != "") {
              		$("#pika2").fadeOut(function(){ 
              			$("#AjoutImage").toggle(true);
                      	$("#pika").html(result);
                      	return false;
                    });
              }
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
    function RecupererImageAjouterDansBD(){
    	$('#AjoutImage input[type="file"]').change(function() {
    	   var file = $(this);
 		   var reader = new FileReader;
 		   
 		   var id_admission = $("#id_admission").val(); // On l'a utiliser ici pour aller vite et n'est valable que lorsqu'une admission c'est pour un et un seul protocole
 		   $("#imageWaiting").html('<table> <tr> <td style=" font-size: 12px; color: red;"> Chargement en cours </td> </tr>  <tr> <td align="center"> <img style="margin-top: 20px; width: 30px; height: 30px;" src="../images/loading/Chargement_5.gif" /> </td> </tr> </table>');
          
	       reader.onload = function(event) {
	    		var img = new Image();
                //Ici on recupere l'image 
			    document.getElementById('fichier_tmp').value = img.src = event.target.result;
			    
			    /**
			     * CODE AJAX POUR L'AJOUT DE L'IMAGE DANS LA BASE DE DONNEES
			     */
			    
		    	$.ajax({
		            type: 'POST',
		            url: tabUrl[0]+'public/consultation/image-protocole-operatoire',
		            data: {'ajout': 1, 'fichier_tmp': $("#fichier_tmp").val(), 'id_admission': id_admission },
		            success: function(data) {
		                var result = jQuery.parseJSON(data); 
		                if(result != ""){
		                	$("#pika2").fadeOut(function(){ 
			                	$("#pika").html(result);
			                	$("#imageWaiting").html("");
			                	return false;
			                });
		                }else{
		                	$("#imageWaiting").html("");
		                	alert("Fichier non reconnu"); return false;
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