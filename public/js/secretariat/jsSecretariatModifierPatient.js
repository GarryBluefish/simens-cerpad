var base_url = window.location.toString();
var tabUrl = base_url.split("public");
$(function() {
	//GESTION DES ACCORDEONS
	//GESTION DES ACCORDEONS
	//GESTION DES ACCORDEONS
    
/********************************************************************************************/
/********************************************************************************************/ 
    //BOITE DE DIALOG POUR LA CONFIRMATION DE SUPPRESSION
    function confirmation(){
	  $( "#confirmation" ).dialog({
	    resizable: false,
	    height:170,
	    width:485,
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
				   var width  = 105;
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
    	//Cr�ation de l'onglet de suppression de la photo
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
    
    //AJOUT LA PHOTO DU PATIENT EN CLIQUANT SUR L'ICONE AJOUTER
    //AJOUT LA PHOTO DU PATIENT EN CLIQUANT SUR L'ICONE AJOUTER
    $("#ajouter_photo").click(function(e){
    	e.preventDefault();
    });
    
    //VALIDATION OU MODIFICATION DU FORMULAIRE ETAT CIVIL DU PATIENT
    //VALIDATION OU MODIFICATION DU FORMULAIRE ETAT CIVIL DU PATIENT
    //VALIDATION OU MODIFICATION DU FORMULAIRE ETAT CIVIL DU PATIENT
    
            var      nom = $("#nom");
            var   prenom = $("#prenom");
            var     sexe = $("#sexe");
            var date_naissance = $("#date_naissance");
            var lieu_naissance = $("#lieu_naissance");
            var nationalite_origine = $("#nationalite_origine");
            var nationalite_actuelle = $("#nationalite_actuelle");
            var     adresse = $("#adresse");
            var   telephone = $("#telephone");
            var       email = $("#email");
            var  profession = $("#profession");
            var ages = $("#age");
            var situation_matrimoniale = $("#situation_matrimoniale");
            
            var temoin = 0;
    	
  	$( "#div_modifier_donnees" ).click(function(){
  		
  		if(temoin == 0){
  			nom.attr( 'readonly', true );    
		         prenom.attr( 'readonly', true );  
		           sexe.attr( 'readonly', true );
    date_naissance.attr( 'readonly', true );
    lieu_naissance.attr( 'readonly', true );
nationalite_origine.attr( 'readonly', true );
nationalite_actuelle.attr( 'readonly', true );
           adresse.attr( 'readonly', true );
         telephone.attr( 'readonly', true );
             email.attr( 'readonly', true );
        profession.attr( 'readonly', true );
        situation_matrimoniale.attr( 'readonly', true );
        if(!date_naissance.val()){ ages.attr( 'readonly', true ); }

        
        temoin = 1;
        
  		} else {
  			 nom.attr( 'readonly', false );    
             prenom.attr( 'readonly', false );  
               sexe.attr( 'readonly', false );
     date_naissance.attr( 'readonly', false );
     lieu_naissance.attr( 'readonly', false );
nationalite_origine.attr( 'readonly', false );
nationalite_actuelle.attr( 'readonly', false );
            adresse.attr( 'readonly', false );
          telephone.attr( 'readonly', false );
              email.attr( 'readonly', false );
         profession.attr( 'readonly', false );
         situation_matrimoniale.attr( 'readonly', false );
         if(!date_naissance.val()){ ages.attr( 'readonly', false ); }
         
         
         temoin = 0;
  			
  		}
  		
  	});
  	
  	//MENU GAUCHE
  	//MENU GAUCHE
  	
  	$("#vider").click(function(){
  		if(confirm("Etes-vous sur de vouloir vider les champs")){
  	  		$('#lieu_naissance').val('');
  	  		$('#email').val('');
  	  		$('#nom').val('');
  	  		$('#telephone').val('');
  	  		$('#nationalite_origine').val('');
  	  		$('#prenom').val('');
  	  		$('#nationalite_actuelle').val('');
  	  		$('#date_naissance').val('');
  	  		$('#adresse').val('');
  	  		$('#sexe').val('');
  	  		$('#profession').val('');
  	  		$('#age').val('');
  		}
  		
  		return false;
  	});
  	
  	
 
  		$('#vider_champ').hover(function(){
  			
  			 $(this).css('background','url("'+tabUrl[0]+'public/images_icons/annuler2.png") no-repeat right top');
  		},function(){
  			  $(this).css('background','url("'+tabUrl[0]+'public/images_icons/annuler1.png") no-repeat right top');
  	    });

  		$('#div_supprimer_photo').hover(function(){
  			
  			 $(this).css('background','url("'+tabUrl[0]+'public/images_icons/mod2.png") no-repeat right top');
  		},function(){
  			  $(this).css('background','url("'+tabUrl[0]+'public/images_icons/mod.png") no-repeat right top');
  	    });

  		$('#div_ajouter_photo').hover(function(){
  			
  			 $(this).css('background','url("'+tabUrl[0]+'public/images_icons/ajouterphoto2.png") no-repeat right top');
  		},function(){
  			  $(this).css('background','url("'+tabUrl[0]+'public/images_icons/ajouterphoto.png") no-repeat right top');
  	    });

  		$('#div_modifier_donnees').hover(function(){
  			
  			 $(this).css('background','url("'+tabUrl[0]+'public/images_icons/modifier2.png") no-repeat right top');
  		},function(){
  			  $(this).css('background','url("'+tabUrl[0]+'public/images_icons/modifier.png") no-repeat right top');
  	   });
  
  
  		function lesMois(n)
  		{
  			switch(n) {
  			  case 1: return "January";
  			  case 2: return "February";
  			  case 3: return "March";
  			  case 4: return "April";
  			  case 5: return "May";
  			  case 6: return "June";
  			  case 7: return "July";
  			  case 8: return "August";
  			  case 9: return "September";
  			  case 10: return "October";
  			  case 11: return "November";
  			  case 12: return "December";
  			}
  		}
  		
  		function age(birthday)
  		{
  			birthday = new Date(birthday);
  			return parseInt( new Number((new Date().getTime() - birthday.getTime()) / 31536000000));
  		}

  		$('#date_naissance').change(function(){

  			var date = $('#date_naissance').val(); 
  			var mois = parseInt(date[3]+''+date[4]);
  			var moisEnLettre = lesMois(mois);
  		    var birthday = date[0]+date[1]+' '+moisEnLettre+' '+date[6]+date[7]+date[8]+date[9];
  		    var Age = age(birthday);
 
  		    if( date && !isNaN(Age)){
  		    	$('#age').val(Age).attr('readonly', true).css('background','#efefef');
  		    }else{
  		    	$('#age, #date_naissance').val('');
  		    	if($('#depistage').val() == 0){
  	  		    	$('#age, #date_naissance').attr('readonly', false).css('background','#ffffff');
  		    	}
  		    }

  		});
  		
  		function miseAJourAge(date){
  			var mois = parseInt(date[3]+''+date[4]);
  			var moisEnLettre = lesMois(mois);
  		    var birthday = date[0]+date[1]+' '+moisEnLettre+' '+date[6]+date[7]+date[8]+date[9];
  		    var Age = age(birthday);
  		}
  		
  		
  		//Au click sur terminer
  		//AU click sur terminer
  		$('#terminer').click(function(){
  			var nom = $('#nom').val();
  			var prenom = $('#prenom').val();
  			var sexe = $('#sexe').val();
  			var age = $('#age').val();
  			
  			if(nom && prenom && sexe && age < 19){ 
  				$('#clicPlier').toggle(false); 
  				$('#amontrer').trigger('click'); 
  				
  			    //Rendre les champs suivants obligatoires
    	        $('#prenom_mere, #prenom_pere').attr({'required': true});
    	        $('#nom_mere, #nom_pere').attr({'required': true});
    	        $('#telephone_mere').attr({'required': true});
    	        $('#profession_pere, #profession_mere').attr({'required': true});
  			}
  			else if(nom && prenom && sexe && age > 18){
  				//Rendre les champs suivants non obligatoires
    	        $('#prenom_mere, #prenom_pere').attr({'required': false});
    	        $('#nom_mere, #nom_pere').attr({'required': false});
    	        $('#telephone_mere').attr({'required': false});
    	        $('#profession_pere, #profession_mere').attr({'required': false});
  			}
  			
  		});
  		
});


    function testAgeApplicationScrip(){
    	$('#age, #date_naissance').keyup(function(){
        	var age = $('#age').val();
        	if(age && age <= 18){
        		$('#amontrer').trigger('click'); 
        		
        		//Rendre les champs suivants obligatoires
    	        $('#prenom_mere, #prenom_pere').attr({'required': true});
    	        $('#nom_mere, #nom_pere').attr({'required': true});
    	        $('#telephone_mere').attr({'required': true});
    	        $('#profession_pere, #profession_mere').attr({'required': true});
        	}else{
        		$('#acacher').trigger('click');
        		
        		//Rendre les champs suivants non obligatoires
    	        $('#prenom_mere, #prenom_pere').attr({'required': false});
    	        $('#nom_mere, #nom_pere').attr({'required': false});
    	        $('#telephone_mere').attr({'required': false});
    	        $('#profession_pere, #profession_mere').attr({'required': false});
    	        $('#clicPlier').toggle(false);
        	}
        	
        }).change(function(){
        	var age = $('#age').val();
        	if(age && age <= 18){
        		$('#amontrer').trigger('click'); 
        		
        		//Rendre les champs suivants obligatoires
    	        $('#prenom_mere, #prenom_pere').attr({'required': true});
    	        $('#nom_mere, #nom_pere').attr({'required': true});
    	        $('#telephone_mere').attr({'required': true});
    	        $('#profession_pere, #profession_mere').attr({'required': true});
        	}else{
        		$('#acacher').trigger('click');
        		
        		//Rendre les champs suivants non obligatoires
    	        $('#prenom_mere, #prenom_pere').attr({'required': false});
    	        $('#nom_mere, #nom_pere').attr({'required': false});
    	        $('#telephone_mere').attr({'required': false});
    	        $('#profession_pere, #profession_mere').attr({'required': false});
    	        $('#clicPlier').toggle(false);
        	}
        	
        });
    }

    //GESTION DES DONNEES PARENTALES
	//GESTION DES DONNEES PARENTALES
	//GESTION DES DONNEES PARENTALES
	function gestionDonneesParentales(){
		$('#acacher').css({'visibility':'hidden'});
		$('#clicPlier').click(function(){ $('#clicPlier').toggle(false); $('#acacher').trigger('click'); return false; });
		$('#amontrer').click(function(){ $('#clicPlier').toggle(true); });
	}
	
    //GESTION DES TYPES DE PATIENTS
    //GESTION DES TYPES DE PATIENTS
    //GESTION DES TYPES DE PATIENTS
	var entrer = 0;
	function gestionTypesPatients(depistage){
		
		$('#patientClassique').click(function(){
	        $('#patientDepistage').css({ 'font-weight':'normal', 'font-size':'12px' }); 
	        $(this).css({ 'font-weight':'bold', 'font-size':'18px' });

	        //$('#prenom').val('');
	        //$('#age, #date_naissance').val('');
	        $('#situation_matrimoniale').parent().toggle(true);
	        $('#ethnie').parent().toggle(false);
	        $('#nationalite_origine').parent().toggle(true);
	        $('#depistage').val(0);
	        
	        //Rendre les champs suivants obligatoires
	        $('#prenom_mere, #prenom_pere').attr({'required': false});
	        $('#nom_mere, #nom_pere').attr({'required': false});
	        $('#telephone_mere').attr({'required': false});
	        $('#profession_pere, #profession_mere').attr({'required': false});
	        $('#clicPlier').toggle(false);
	        $('#acacher').trigger('click'); 
	        var age = $('#age').val();
	    	if(age <= 18){
	    		$('#amontrer').trigger('click'); 
	    	}
	        
	        //Gestion du test de l'age pour rendre les informations parentales obligatoires
	        if(entrer == 0){
	        	testAgeApplicationScrip();
		        entrer = 1;
	        }
	        
	        
	        
	        
	        
	        $('#age').attr('readonly', true).css('background', '#efefef');
	        $('#date_naissance').attr('required', false);
	        //GESTION DU CALENDRIER
	        //GESTION DU CALENDRIER
	        $('#date_naissance').removeClass().addClass('date_naissance_class');
	        $('.date_naissance_class').datepicker(
	    			$.datepicker.regional['fr'] = {
	    					
	    					closeText: 'Fermer',
	    					yearRange: 'c-80:c',
	    					prevText: '&#x3c;Préc',
	    					nextText: 'Suiv&#x3e;',
	    					currentText: 'Courant',
	    					monthNames: ['Janvier','Fevrier','Mars','Avril','Mai','Juin',
	    					'Juillet','Aout','Septembre','Octobre','Novembre','Decembre'],
	    					monthNamesShort: ['Jan','Fev','Mar','Avr','Mai','Juin',
	    					'Jul','Aout','Sep','Oct','Nov','Dec'],
	    					dayNames: ['Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'],
	    					dayNamesShort: ['Dim','Lun','Mar','Mer','Jeu','Ven','Sam'],
	    					dayNamesMin: ['Di','Lu','Ma','Me','Je','Ve','Sa'],
	    					weekHeader: 'Sm',
	    					dateFormat: 'dd/mm/yy',
	    					firstDay: 1,
	    					isRTL: false,
	    					showMonthAfterYear: false,
	    					yearRange: '1900:2050',
	    					showAnim : 'bounce',
	    					changeYear: true,
	    					
	    					changeMonth: true,
	    					changeMonth: true,
	    					maxDate: 0,
	    			}
	    	);
	        
	        return false; 
	        
	    });

	    $('#patientDepistage').click(function(){
	    	
	        $('#patientClassique').css({ 'font-weight':'normal', 'font-size':'12px' }); 
	        $(this).css({ 'font-weight':'bold', 'font-size':'18px' });

	        //$('#prenom').val('bébé ');
	        //$('#age, #date_naissance').val('');
	        $('#situation_matrimoniale').parent().toggle(false);
	        $('#ethnie').parent().toggle(true);
	        $('#nationalite_origine').parent().toggle(false);

	        $('#depistage').val(1);
	        
	        //Rendre les champs suivants obligatoires
	        $('#prenom_mere, #prenom_pere').attr({'required': true});
	        $('#nom_mere, #nom_pere').attr({'required': true});
	        $('#telephone_mere').attr({'required': true});
	        $('#profession_pere, #profession_mere').attr({'required': true});
	        $('#amontrer').trigger('click');
	        
	        
	        $('#age').attr('readonly', true).css('background', '#efefef');;
	        $('#date_naissance').attr('required', true);
	        //GESTION DU CALENDRIER
	    	//GESTION DU CALENDRIER
	    	$('#date_naissance').removeClass().addClass('date_naissance_depister');
	    	$('.date_naissance_depister').datepicker(
	    			$.datepicker.regional['fr'] = {
	    					closeText: 'Fermer',
	    					yearRange: 'c-80:c',
	    					prevText: '&#x3c;Préc',
	    					nextText: 'Suiv&#x3e;',
	    					currentText: 'Courant',
	    					monthNames: ['Janvier','Fevrier','Mars','Avril','Mai','Juin',
	    					'Juillet','Aout','Septembre','Octobre','Novembre','Decembre'],
	    					monthNamesShort: ['Jan','Fev','Mar','Avr','Mai','Juin',
	    					'Jul','Aout','Sep','Oct','Nov','Dec'],
	    					dayNames: ['Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'],
	    					dayNamesShort: ['Dim','Lun','Mar','Mer','Jeu','Ven','Sam'],
	    					dayNamesMin: ['Di','Lu','Ma','Me','Je','Ve','Sa'],
	    					weekHeader: 'Sm',
	    					dateFormat: 'dd/mm/yy',
	    					firstDay: 1,
	    					isRTL: false,
	    					showMonthAfterYear: false,
	    					yearRange: '1900:2050',
	    					showAnim : 'bounce',
	    					changeYear: false,
	    					
	    					changeMonth: true,
	    					maxDate: 0,
	    					minDate: -30, //Afficher uniquement que le dernier mois
	    			}
	    	);
	    	
	    });

	    if(depistage == 0){
		    setTimeout(function(){ $('#patientClassique').trigger('click'); },1000);
	    }else{
		    $('#patientDepistage').trigger('click');
	    }
	    
	    $( "#accordions" ).accordion();
	}