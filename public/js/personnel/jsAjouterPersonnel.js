var base_url = window.location.toString();
var tabUrl = base_url.split("public");

$(function(){
	$("#accordions").accordion();
	//$( "button" ).button(); // APPLICATION DU STYLE POUR LES BOUTONS
});

function dialogue(){ 
//BOITE DE DIALOG POUR LA CONFIRMATION DE SUPPRESSION
    function confirmation(){
	  $( "#confirmation" ).dialog({
	    resizable: false,
	    height:170,
	    width:375,
	    autoOpen: false,
	    modal: true,
	    buttons: {
	        "Oui": function() {
	            $( this ).dialog( "close" );

	             $('#photo').children().remove(); 
	             $('<input type="file" />').appendTo($('#photo')); 
	             $("#div_supprimer_photo").children().remove();
	             Recupererimage();          	       
	    	     return false;
	    	     
	        },
	        "Annuler": function() {
                $( this ).dialog( "close" );
            }
	   }
	  });
    }
  //FONCTION QUI RECUPERE LA PHOTO ET LA PLACE SUR L'EMPLACEMENT SOUHAITE
    function Recupererimage(){
    	$('#photo input[type="file"]').change(function() {
    	  
    	   var file = $(this);
 		   var reader = new FileReader;
 		   
	       reader.onload = function(event) {
	    		var img = new Image();
                 
        		img.onload = function() {
				   var width  = 100;
				   var height = 105;
				
				   var canvas = $('<canvas></canvas>').attr({ width: width, height: height });
				   file.replaceWith(canvas);
				   var context = canvas[0].getContext('2d');
	        	    	context.drawImage(img, 0, 0, width, height);
			    };
			    document.getElementById('fichier_tmp').value = img.src = event.target.result;
			   
    	};
    	 $("#modifier_photo").remove(); //POUR LA MODIFICATION
    	reader.readAsDataURL(file[0].files[0]);
    	//Création de l'onglet de suppression de la photo
    	$("#div_supprimer_photo").children().remove();
    	$('<input alt="supprimer_photo" title="Supprimer la photo" name="supprimer_photo" id="supprimer_photo">').appendTo($("#div_supprimer_photo"));
      
    	//SUPPRESSION DE LA PHOTO
        //SUPPRESSION DE LA PHOTO
          $("#supprimer_photo").click(function(e){
        	 e.preventDefault();
        	 confirmation();
             $("#confirmation").dialog('open');
          });
      });
    }
    //AJOUTER LA PHOTO DU PATIENT
    //AJOUTER LA PHOTO DU PATIENT
    Recupererimage();
}
var mdclick = 0;
function debuter(){
	/***********************************************************************************************                              

	========================== LE BOUTON "ANNULER" =================================                              
	                          
	***********************************************************************************************/

    $('#annuler').click(function(){
    	vart= tabUrl[0]+'public/personnel/liste-personnel';
        $(location).attr("href",vart);
        return false;
    });
    
    $('#terminer').click(function(){
    	
    	//On active le read only pour envoyer les donnees
		$("#numero_os").attr( 'disabled', false );
        $("#service_accueil").attr( 'disabled', false );
        $("#date_fin").attr( 'disabled', false );
        $("#date_debut").attr( 'disabled', false );
        //-----------------------------------------------
        //-----------------------------------------------
    	
    	
    	var sexe = $('#sexe').val();
    	var nom = $('#nom').val();
    	var prenom = $('#prenom').val();
    	var type_personnel = $('#type_personnel').val();
    	var service_accueil = $('#service_accueil').val();
    	
    	if(sexe && nom && prenom){
    		if(!type_personnel){
    			$('#depliant2').trigger('click');
    		} else 
    			if(!service_accueil){
    				$('#depliant3').trigger('click');
    			}
    	}else{
    		$('#depliant1').trigger('click');
    	}
    	
    });
    
    /***********************************************************************************************/
    /***********************************************************************************************/
    
    
    //AJOUT LA PHOTO DU PATIENT EN CLIQUANT SUR L'ICONE AJOUTER
    //AJOUT LA PHOTO DU PATIENT EN CLIQUANT SUR L'ICONE AJOUTER
    $("#ajouter_photo").click(function(e){
    	e.preventDefault();
    });
    
    //VALIDATION OU MODIFICATION DU FORMULAIRE ETAT CIVIL DU PATIENT
    //VALIDATION OU MODIFICATION DU FORMULAIRE ETAT CIVIL DU PATIENT
    //VALIDATION OU MODIFICATION DU FORMULAIRE ETAT CIVIL DU PATIENT
    
            var civilite = $("#civilite");
            var      nom = $("#nom");
            var   prenom = $("#prenom");
            var     sexe = $("#sexe");
            var date_naissance = $("#date_naissance");
            var lieu_naissance = $("#lieu_naissance");
            var nationalite_origine = $("#nationalite_origine");
            var nationalite_actuelle = $("#nationalite_actuelle");
            var situation_matrimoniale = $("#situation_matrimoniale");
            var     adresse = $("#adresse");
            var   telephone = $("#telephone");
            var       email = $("#email");
            var  profession = $("#profession");
            var  nationalite = $("#nationalite");
    	
    $( "button" ).button(); // APPLICATION DU STYLE POUR LES BOUTONS
    
    /*******************************************************/
    //En cliquant sur l'icone modifier
    	$( "#modifer_donnees" ).click(function(){
    		if(mdclick == 1){
 		       civilite.attr( 'readonly', false );     
               nom.attr( 'readonly', false );    
            prenom.attr( 'readonly', false );  
              sexe.attr( 'readonly', false );
    date_naissance.attr( 'readonly', false );
    lieu_naissance.attr( 'readonly', false );
    nationalite_origine.attr( 'readonly', false );
    nationalite_actuelle.attr( 'readonly', false );
situation_matrimoniale.attr( 'readonly', false );
        adresse.attr( 'readonly', false );
      telephone.attr( 'readonly', false );
          email.attr( 'readonly', false );
     profession.attr( 'readonly', false );
    nationalite.attr( 'readonly', false );
    			mdclick = 0;
    		} else { 
    			civilite.attr( 'readonly', true );     
	            nom.attr( 'readonly', true );    
	         prenom.attr( 'readonly', true );  
	           sexe.attr( 'readonly', true );
     date_naissance.attr( 'readonly', true );
     lieu_naissance.attr( 'readonly', true );
     nationalite_origine.attr( 'readonly', true );
     nationalite_actuelle.attr( 'readonly', true );
      situation_matrimoniale.attr( 'readonly', true );
          adresse.attr( 'readonly', true );
        telephone.attr( 'readonly', true );
            email.attr( 'readonly', true );
       profession.attr( 'readonly', true );
      nationalite.attr( 'readonly', true );
    			mdclick = 1;
    		}
          
	    });
    	
    /*******************************************************/
    
   
  	//MENU GAUCHE
  	//MENU GAUCHE
  	//MENU GAUCHE
    $("#vider").click(function(){
  		$('#civilite').val("");
  		$('#lieu_naissance').val("");
  		$('#email').val("");
  		$('#nom').val("");
  		$('#telephone').val("");
  		$('#nationalite_origine').val("");
  		$('#nationalite_actuelle').val("");
  		$('#prenom').val("");
  		$('#situation_matrimoniale').val("");
  		$('#date_naissance').val("");
  		$('#adresse').val("");
  		$('#sexe').val("");
  		$('#profession').val("");
  		return false;
  	});
 
  		$('#vider_champ').hover(function(){
  			
  			 $(this).css('background','url("../images_icons/annuler2.png") no-repeat right top');
  		},function(){
  			  $(this).css('background','url("../images_icons/annuler1.png") no-repeat right top');
  	    });

  		$('#div_supprimer_photo').hover(function(){
  			
  			 $(this).css('background','url("../images_icons/mod2.png") no-repeat right top');
  		},function(){
  			  $(this).css('background','url("../images_icons/mod.png") no-repeat right top');
  	    });

  		$('#div_ajouter_photo').hover(function(){
  			
  			 $(this).css('background','url("../images_icons/ajouterphoto2.png") no-repeat right top');
  		},function(){
  			  $(this).css('background','url("../images_icons/ajouterphoto.png") no-repeat right top');
  	    });

  		$('#div_modifier_donnees').hover(function(){
  			
  			 $(this).css('background','url("../images_icons/modifier2.png") no-repeat right top');
  		},function(){
  			  $(this).css('background','url("../images_icons/modifier.png") no-repeat right top');
  	   });
  
  		$('#date_naissance, #date_debut, #date_fin').datepicker( 
  				$.datepicker.regional['fr'] = {
  						closeText: 'Fermer',
  						changeYear: true,
  						yearRange: 'c-80:c',
  						prevText: '&#x3c;PrÃ©c',
  						nextText: 'Suiv&#x3e;',
  						currentText: 'Courant',
  						monthNames: ['Janvier','Fevrier','Mars','Avril','Mai','Juin',
  						'Juillet','Aout','Septembre','Octobre','Novembre','Decembre'],
  						monthNamesShort: ['Jan','Fev','Mar','Avr','Mai','Jun',
  						'Jul','Aout','Sep','Oct','Nov','Dec'],
  						dayNames: ['Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'],
  						dayNamesShort: ['Dim','Lun','Mar','Mer','Jeu','Ven','Sam'],
  						dayNamesMin: ['Di','Lu','Ma','Me','Je','Ve','Sa'],
  						weekHeader: 'Sm',
  						dateFormat: 'dd/mm/yy',
  						firstDay: 1,
  						isRTL: false,
  						showMonthAfterYear: false,
  						yearRange: '1900:2100',
  						//showAnim : 'bounce',
  						changeMonth: true,
  						changeYear: true,
  						yearSuffix: ''
  				}
  		);
  //FIN VALIDATION OU MODIFICATION DU FORMULAIRE ETAT CIVIL DU PATIENT
  //FIN VALIDATION OU MODIFICATION DU FORMULAIRE ETAT CIVIL DU PATIENT
  //FIN VALIDATION OU MODIFICATION DU FORMULAIRE ETAT CIVIL DU PATIENT 
  		
  		
  		
  		
  		
  		
  		
  		
  		
  		
  		
  		
  		
  		
  		



  		function vider_medecin(){
  			document.getElementById('matricule').value="";
  			document.getElementById('grade').value="";
  			document.getElementById('specialite').value="";
  			document.getElementById('fonction').value="";
  			
  			return false;
  		}

  		function vider_medicoTechnique(){
  			document.getElementById('matricule_medico').value="";
  			document.getElementById('grade_medico').value="";
  			document.getElementById('domaine_medico').value="";
  			document.getElementById('autres').value="";
  			
  			return false;
  		}

  		function vider_logistique(){
  			document.getElementById('matricule_logistique').value="";
  			document.getElementById('grade_logistique').value="";
  			document.getElementById('domaine_logistique').value="";
  			document.getElementById('autres_logistique').value="";
  			
  			return false;
  		}
  		
  		function vider_secretaire(){
  			document.getElementById('matricule_secretaire').value="";
  			document.getElementById('grade_secretaire').value="";
  			document.getElementById('domaine_secretaire').value="";
  			document.getElementById('autres_secretaire').value="";
  			
  			return false;
  		}

  		function vider_laborantin(){
  			document.getElementById('matricule_laborantin').value="";
  			document.getElementById('grade_laborantin').value="";
  			document.getElementById('domaine_laborantin').value="";
  			document.getElementById('autres_laborantin').value="";
  			
  			return false;
  		}

  		/************************/
  		/***COMPLEMENT MEDECIN***/
  		/************************/
  		$("#bouton_donnees_valider").click(function(){
  			if(tmp == 1){	
  				$(document).keypress(function(e){
  					if(e.keyCode == 13){
  						e.preventDefault();
  					}
  				});
  			    $("#matricule").attr( 'readonly', true );
  				$("#grade").attr( 'readonly', true );
  				$("#specialite").attr( 'readonly', true );
  				$("#fonction").attr( 'readonly', true );
  				
  				$("#bouton_valider_donnees").toggle(false);
  				$('#modifier_champ2').toggle(true);
  				
  				$('#vider_champ2').toggle(false);
  			}
  			
  			return false;
  		});

  		$("#modifier_champ2").click(function(){
  			 $("#matricule").attr( 'readonly', false );
  		         $("#grade").attr( 'readonly', false );
  		    $("#specialite").attr( 'readonly', false );
  		      $("#fonction").attr( 'readonly', false );
  			$("#bouton_valider_donnees").toggle(true);
  				  $('#modifier_champ2').toggle(false);
  				  
  				$('#vider_champ2').toggle(true);
  				
  				  return false;
  		});

  		$("#vider2").click(function(){
  			vider_medecin();
  		 });

  		/*********************************/
  		/***COMPLEMENT MEDICO-TECHNIQUE***/
  		/*********************************/
  		$("#bouton_donnees_valider_medico").click(function(){
  		    $("#matricule_medico").attr( 'readonly', true );
  		        $("#grade_medico").attr( 'readonly', true );
  		      $("#domaine_medico").attr( 'readonly', true );
  		      $("#autres").attr( 'readonly', true );
  		   $("#bouton_valider_donnees_medico").toggle(false);
  			 $('#modifier_champ_medico').toggle(true);
  			 
  			$('#vider_champ_medico').toggle(false);
  			
  			 return false;
  		   });

  		$("#modifier_champ_medico").click(function(){
  			$("#matricule_medico").attr( 'readonly', false );
  		        $("#grade_medico").attr( 'readonly', false );
  		      $("#domaine_medico").attr( 'readonly', false );
  		      $("#autres").attr( 'readonly', false );
  			$("#bouton_valider_donnees_medico").toggle(true);
  			  $('#modifier_champ_medico').toggle(false);
  			  
  			$('#vider_champ_medico').toggle(true);
  			  return false;
  		});

  		$("#vider_champ_medico").click(function(){
  			vider_medicoTechnique();
  		 });

  		/*********************************/
  		/***COMPLEMENT LOGISTIQUE***/
  		/*********************************/
  		$("#bouton_donnees_valider_logistique").click(function(){
  		    $("#matricule_logistique").attr( 'readonly', true );
  		        $("#grade_logistique").attr( 'readonly', true );
  		      $("#domaine_logistique").attr( 'readonly', true );
  		       $("#autres_logistique").attr( 'readonly', true );
  		   $("#bouton_valider_donnees_logistique").toggle(false);
  			 $('#modifier_champ_logistique').toggle(true);
  			 
  			$('#vider_champ_logistique').toggle(false);
  			 return false;
  		   });

  		$("#modifier_champ_logistique").click(function(){
  			$("#matricule_logistique").attr( 'readonly', false );
  		        $("#grade_logistique").attr( 'readonly', false );
  		      $("#domaine_logistique").attr( 'readonly', false );
  		       $("#autres_logistique").attr( 'readonly', false );
  			$("#bouton_valider_donnees_logistique").toggle(true);
  			  $('#modifier_champ_logistique').toggle(false);
  			  
  			$('#vider_champ_logistique').toggle(true);
  			  return false;
  		});

  		$("#vider_champ_logistique").click(function(){
  			vider_logistique();
  		 });

  		/*********************************/
  		/***COMPLEMENT SECRETAIRE***/
  		/*********************************/
  		$("#bouton_donnees_valider_secretaire").click(function(){
  		    $("#matricule_secretaire").attr( 'readonly', true );
  		        $("#grade_secretaire").attr( 'readonly', true );
  		      $("#domaine_secretaire").attr( 'readonly', true );
  		       $("#autres_secretaire").attr( 'readonly', true );
  		   $("#bouton_valider_donnees_secretaire").toggle(false);
  			 $('#modifier_champ_secretaire').toggle(true);
  			 
  			$('#vider_champ_secretaire').toggle(false);
  			 return false;
  		   });

  		$("#modifier_champ_secretaire").click(function(){
  			$("#matricule_secretaire").attr( 'readonly', false );
  		        $("#grade_secretaire").attr( 'readonly', false );
  		      $("#domaine_secretaire").attr( 'readonly', false );
  		       $("#autres_secretaire").attr( 'readonly', false );
  			$("#bouton_valider_donnees_secretaire").toggle(true);
  			  $('#modifier_champ_secretaire').toggle(false);
  			  
  			$('#vider_champ_secretaire').toggle(true);
  			  return false;
  		});

  		$("#vider_champ_secretaire").click(function(){
  			vider_secretaire();
  		 });

  		/*********************************/
  		/***COMPLEMENT LABORANTIN***/
  		/*********************************/
  		$("#bouton_donnees_valider_laborantin").click(function(){
  		    $("#matricule_laborantin").attr( 'readonly', true );
  		        $("#grade_laborantin").attr( 'readonly', true );
  		      $("#domaine_laborantin").attr( 'readonly', true );
  		       $("#autres_laborantin").attr( 'readonly', true );
  		   $("#bouton_valider_donnees_laborantin").toggle(false);
  			 $('#modifier_champ_laborantin').toggle(true);
  			 
  			$('#vider_champ_laborantin').toggle(false);
  			 return false;
  		   });

  		$("#modifier_champ_laborantin").click(function(){
  			$("#matricule_laborantin").attr( 'readonly', false );
  		        $("#grade_laborantin").attr( 'readonly', false );
  		      $("#domaine_laborantin").attr( 'readonly', false );
  		       $("#autres_laborantin").attr( 'readonly', false );
  			$("#bouton_valider_donnees_laborantin").toggle(true);
  			  $('#modifier_champ_laborantin').toggle(false);
  			  
  			$('#vider_champ_laborantin').toggle(true);
  			  return false;
  		});

  		$("#vider_champ_laborantin").click(function(){
  			vider_laborantin();
  		 });
  		
  		/*********************************/
  		/***AFFECTATION***/
  		/*********************************/
  		$("#bouton_donnees_valider_affectation").click(function(){
  		     $("#numero_os").attr( 'disabled', true );
  		     $("#service_accueil").attr( 'disabled', true );
  		     $("#date_fin").attr( 'disabled', true );
  		     $("#date_debut").attr( 'disabled', true );
  		     
  		     $('#bouton_valider_donnees_facturation').toggle(false);
  		     $("#modifier_champ_affectation").toggle(true);
  		     
  		     $('#vider_champ_affectation').toggle(false);
  		     return false;
  		});

  		$("#modifier_champ_affectation").click(function(){
  			$("#numero_os").attr( 'disabled', false );
  		    $("#service_accueil").attr( 'disabled', false );
  		    $("#date_fin").attr( 'disabled', false );
  		    $("#date_debut").attr( 'disabled', false );
  		    
  		    $('#bouton_valider_donnees_facturation').toggle(true);
  		    $("#modifier_champ_affectation").toggle(false);
  		    
  		    $('#vider_champ_affectation').toggle(true);
  		    return false;
  		});

  		$("#vider_champ_affectation").click(function(){
  			document.getElementById('numero_os').value="";
  			document.getElementById('service_accueil').value="";
  			document.getElementById('date_fin').value="";
  			document.getElementById('date_debut').value="";
  			
  			return false;
  		 });
  		
  		
  		
  		
  		
  		
  		
  		
  		
  		
  		
  		
  		
  		
  		
}

var tmp;
function getChamps(id){ 
	$(document).keypress(function(e){
		if(e.keyCode == 13){
			e.preventDefault();
		}
	});
	
    tmp = id;
	/***COMPLEMENT MEDECIN***/
	if(id==1){ 		
		       $('.complement_medico-technique').toggle(false);
	           $('#modifier_champ_medico').toggle(false);
	           $("#vider_champ_medico").toggle(false);
               $("#bouton_valider_donnees_medico").toggle(false);
               
               $(".complement_logistique").toggle(false);
               $('#modifier_champ_logistique').toggle(false);
               $("#vider_champ_logistique").toggle(false);
               $("#bouton_valider_donnees_logistique").toggle(false);
               
               $('.complement_secretaire').toggle(false);
               $('#bouton_valider_donnees_secretaire').toggle(false);
               
               $('.complement_laborantin').toggle(false);
    	       $('#bouton_valider_donnees_laborantin').toggle(false);
    	       
               $('.complement_medecin').toggle(true); 
	           $('#vider_champ2').toggle(true);
               $("#bouton_valider_donnees").toggle(true);
                  $("#matricule").attr( 'readonly', false );
                  $("#grade").attr( 'readonly', false );
                  $("#specialite").attr( 'readonly', false );
                  $("#fonction").attr( 'readonly', false );
                  
                  vider_medecin();
             }else
	
	/***COMPLEMENT MEDICO-TECHNIQUE***/
	if(id==2){ $('.complement_medecin').toggle(false);
	           $('#modifier_champ2').toggle(false);
	           $('#vider_champ2').toggle(false);
               $("#bouton_valider_donnees").toggle(false);
               
               $(".complement_logistique").toggle(false);
               $('#modifier_champ_logistique').toggle(false);
               $("#vider_champ_logistique").toggle(false);
               $("#bouton_valider_donnees_logistique").toggle(false); 
               
               $('.complement_secretaire').toggle(false);
               $('#bouton_valider_donnees_secretaire').toggle(false);
               
               $('.complement_laborantin').toggle(false);
    	       $('#bouton_valider_donnees_laborantin').toggle(false);
               
               $('.complement_medico-technique').toggle(true);
               $("#vider_champ_medico").toggle(true);
               $("#bouton_valider_donnees_medico").toggle(true);
                  $("#matricule_medico").attr( 'readonly', false );
                  $("#grade_medico").attr( 'readonly', false );
                  $("#domaine_medico").attr( 'readonly', false );
                  $("#autres").attr( 'readonly', false );
                  vider_medicoTechnique();
	         }else
	
	/***COMPLEMENT LOGISTIQUE***/
	if(id==3){
		       $('.complement_medecin').toggle(false);
		       $('#modifier_champ2').toggle(false);
		       $('#vider_champ2').toggle(false);
		       $("#bouton_valider_donnees").toggle(false);
		       
		       $('.complement_medico-technique').toggle(false);
		       $('#modifier_champ_medico').toggle(false);
		       $("#vider_champ_medico").toggle(false);
               $("#bouton_valider_donnees_medico").toggle(false);
               
               $('.complement_secretaire').toggle(false);
               $('#bouton_valider_donnees_secretaire').toggle(false);
               
               $('.complement_laborantin').toggle(false);
    	       $('#bouton_valider_donnees_laborantin').toggle(false);
               
               $(".complement_logistique").toggle(true);
               $("#vider_champ_logistique").toggle(true);
               $("#bouton_valider_donnees_logistique").toggle(true);
               $("#matricule_logistique").attr( 'readonly', false );
               $("#grade_logistique").attr( 'readonly', false );
               $("#domaine_logistique").attr( 'readonly', false );
               $("#autres_logistique").attr( 'readonly', false );
               vider_logistique();
             }else
	
	/***COMPLEMENT SECRETAIRE***/
	if(id==4){
		       $('.complement_medecin').toggle(false);
		       $('#modifier_champ2').toggle(false);
		       $('#vider_champ2').toggle(false);
               $("#bouton_valider_donnees").toggle(false);
               
		       $('.complement_medico-technique').toggle(false);
		       $("#vider_champ_medico").toggle(false);
		       $("#vider_champ_medico").toggle(false);
		       $("#bouton_valider_donnees_medico").toggle(false);
		       
		       $(".complement_logistique").toggle(false);
		       $('#modifier_champ_logistique').toggle(false);
		       $("#vider_champ_logistique").toggle(false);
               $("#bouton_valider_donnees_logistique").toggle(false);
               
               $('.complement_laborantin').toggle(false);
    	       $('#bouton_valider_donnees_laborantin').toggle(false);
               
               $('.complement_secretaire').toggle(true);
               //$('#bouton_valider_donnees_secretaire').toggle(true);
               vider_secretaire();
		     
             }else
	

    /***COMPLEMENT laborantin***/
    if(id==5){
    	       $('.complement_medecin').toggle(false);
    	       $('#modifier_champ2').toggle(false);
    	       $('#vider_champ2').toggle(false);
    	       $("#bouton_valider_donnees").toggle(false);
            	               
    	       $('.complement_medico-technique').toggle(false);
    	       $("#vider_champ_medico").toggle(false);
    	       $("#vider_champ_medico").toggle(false);
    	       $("#bouton_valider_donnees_medico").toggle(false);
            			       
    	       $(".complement_logistique").toggle(false);
    	       $('#modifier_champ_logistique').toggle(false);
    	       $("#vider_champ_logistique").toggle(false);
    	       $("#bouton_valider_donnees_logistique").toggle(false);
            	               
    	       $('.complement_secretaire').toggle(false);
    	       $('#bouton_valider_donnees_secretaire').toggle(false);
    	       
    	       $('.complement_laborantin').toggle(true);
    	       //$('#bouton_valider_donnees_laborantin').toggle(true);
    	       vider_laborantin();
    }else
    	{
    	   $('.complement_medecin').toggle(false);
	       $('#modifier_champ2').toggle(false);
	       $('#vider_champ2').toggle(false);
	       $("#bouton_valider_donnees").toggle(false);
     	               
	       $('.complement_medico-technique').toggle(false);
	       $("#vider_champ_medico").toggle(false);
	       $("#vider_champ_medico").toggle(false);
	       $("#bouton_valider_donnees_medico").toggle(false);
     			       
	       $(".complement_logistique").toggle(false);
	       $('#modifier_champ_logistique').toggle(false);
	       $("#vider_champ_logistique").toggle(false);
	       $("#bouton_valider_donnees_logistique").toggle(false);
     	               
	       $('.complement_secretaire').toggle(false);
	       $('#bouton_valider_donnees_secretaire').toggle(false);
	       
	       $('.complement_laborantin').toggle(false);
	       $('#bouton_valider_donnees_laborantin').toggle(false);
	       
	       vider_medecin();
	       vider_medicoTechnique();
	       vider_logistique();
	       vider_secretaire();
	       vider_laborantin();
    	}
	
return false;
}
