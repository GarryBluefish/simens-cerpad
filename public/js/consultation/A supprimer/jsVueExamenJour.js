    var base_url = window.location.toString();
    var tabUrl = base_url.split("public");

    /************************************************************************************************************************/
    /************************************************************************************************************************/
    /************************************************************************************************************************/
    
    /************************************************************************************************************************/
    /************************************************************************************************************************/
    /************************************************************************************************************************/
    function affichervue(id_demande_hospi){ 
    	var id_cons = $("#"+id_demande_hospi).val();
    	var id_personne = $("#"+id_demande_hospi+"idPers").val();
    	var chemin = tabUrl[0]+'public/consultation/info-patient';
        $.ajax({
            type: 'POST',
            url: chemin ,
            data:{'id_personne':id_personne, 'id_cons':id_cons, 'encours':111, 'id_demande_hospi':id_demande_hospi},
            success: function(data) {
           	         
            	$("#titre").replaceWith("<div id='titre2' style='font-family: police2; color: green; font-size: 20px; font-weight: bold; padding-left:20px;'><iS style='font-size: 25px;'>&curren;</iS> INFORMATIONS </div>");
            	var result = jQuery.parseJSON(data);
            	$("#contenu").fadeOut(function(){$("#vue_patient").html(result).fadeIn("fast"); }); 
            	     
            },
            error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
            dataType: "html"
        });
     }
    /************************************************************************************************************************/
    /************************************************************************************************************************/
    /************************************************************************************************************************/
    function administrerSoin(id_personne){
    	var chemin = tabUrl[0]+'public/consultation/info-patient-hospi';
        $.ajax({
            type: 'POST',
            url: chemin ,
            data:{'id_personne':id_personne, 'administrerSoin':111},
            success: function(data) {
            	var result = jQuery.parseJSON(data);
            	$("#vue_patient_hospi").html(result);
            },
            error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
            dataType: "html"
        });
        
        setTimeout(function(){
            $("#respond input").attr('disabled', true).css({'background':'#f8f8f8'});
        }, 1000);

    }
    
    /*************************************************************************************************************************/
    /*************************************************************************************************************************/
    /*************************************************************************************************************************/
    
    // ******************* Tranfert ******************************** 
	// ******************* Tranfert ******************************** 
    $(function(){
    	var motif_transfert = $("#motif_transfert");
    	var hopital_accueil = $("#hopital_accueil");
    	var service_accueil = $("#service_accueil");

    	$( "#bouton_transfert_valider" ).toggle(true);
    	$( "#bouton_transfert_modifier" ).toggle(false);

    	$( "#bouton_transfert_valider" ).click(function(){
    		motif_transfert.attr( 'disabled', true ).css({'background':'#f8f8f8'});     
    		hopital_accueil.attr( 'disabled', true ).css({'background':'#f8f8f8'});     
    		service_accueil.attr( 'disabled', true ).css({'background':'#f8f8f8'});   
    		$("#bouton_transfert_modifier").toggle(true);  
    		$("#bouton_transfert_valider").toggle(false); 
    		return false; 
    	});
    	
    	$( "#bouton_transfert_modifier" ).click(function(){
    		motif_transfert.attr( 'disabled', false ).css({'background':'#fff'});
    		hopital_accueil.attr( 'disabled', false ).css({'background':'#fff'});
    		service_accueil.attr( 'disabled', false ).css({'background':'#fff'});
    	 	$("#bouton_transfert_modifier").toggle(false);  
    	 	$("#bouton_transfert_valider").toggle(true);    
    	 	return  false;
    	});
    });    

    
    $(function(){
    	var poids = $('#poids');
    	var taille = $('#taille');
    	var tension = $('#tension');
    	var bu = $('#bu');
    	var temperature = $('#temperature');
    	var glycemie_capillaire = $('#glycemie_capillaire');
    	var pouls = $('#pouls');
    	var frequence_respiratoire = $('#frequence_respiratoire');
    	var pressionarterielle = $("#pressionarterielle");
    	
  	    //Au debut on cache le bouton modifier et on affiche le bouton valider
    	$( "#bouton_constantes_valider" ).toggle(true);
    	$( "#bouton_constantes_modifier" ).toggle(false);

    	//Au debut on active tous les champs
    	poids.attr( 'readonly', false ).css({'background':'#fff'});
    	taille.attr( 'readonly', false ).css({'background':'#fff'});
    	tension.attr( 'readonly', false).css({'background':'#fff'}); 
    	bu.attr( 'readonly', false).css({'background':'#fff'});  
    	temperature.attr( 'readonly', false).css({'background':'#fff'}); 
    	glycemie_capillaire.attr( 'readonly', false).css({'background':'#fff'});
    	pouls.attr( 'readonly', false).css({'background':'#fff'});
    	frequence_respiratoire.attr( 'readonly', false).css({'background':'#fff'});
    	pressionarterielle.attr( 'readonly', false ).css({'background':'#fff'});

    	$( "#bouton_constantes_valider" ).click(function(){
    		if(valid == true){
  	   		poids.attr( 'readonly', true ).css({'background':'#f8f8f8'});    
  	   		taille.attr( 'readonly', true ).css({'background':'#f8f8f8'});
  	   		tension.attr( 'readonly', true).css({'background':'#f8f8f8'});
  	   		bu.attr( 'readonly', true).css({'background':'#f8f8f8'});
  	   		temperature.attr( 'readonly', true).css({'background':'#f8f8f8'});
  	   		glycemie_capillaire.attr( 'readonly', true).css({'background':'#f8f8f8'});
  	   		pouls.attr( 'readonly', true).css({'background':'#f8f8f8'});
  	   		frequence_respiratoire.attr( 'readonly', true).css({'background':'#f8f8f8'});
  	   		pressionarterielle.attr( 'readonly', true ).css({'background':'#f8f8f8'});
  	   		
    		    $("#bouton_constantes_modifier").toggle(true);  //on affiche le bouton permettant de modifier les champs
    		    $("#bouton_constantes_valider").toggle(false); //on cache le bouton permettant de valider les champs
    		}
    		return false; 
    	});
    	
    	$( "#bouton_constantes_modifier" ).click(function(){
    		poids.attr( 'readonly', false ).css({'background':'#fff'});
    		taille.attr( 'readonly', false ).css({'background':'#fff'}); 
    		tension.attr( 'readonly', false).css({'background':'#fff'}); 
    		bu.attr( 'readonly', false).css({'background':'#fff'});
    		temperature.attr( 'readonly', false).css({'background':'#fff'});
    		glycemie_capillaire.attr( 'readonly', false).css({'background':'#fff'});
    		pouls.attr( 'readonly', false).css({'background':'#fff'});
    		frequence_respiratoire.attr( 'readonly', false).css({'background':'#fff'});
    		pressionarterielle.attr( 'readonly', false ).css({'background':'#fff'});
    		
    	 	$("#bouton_constantes_modifier").toggle(false);   //on cache le bouton permettant de modifier les champs
    	 	$("#bouton_constantes_valider").toggle(true);    //on affiche le bouton permettant de valider les champs
    	 	return  false;
    	});
    });
