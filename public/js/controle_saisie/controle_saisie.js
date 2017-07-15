//**********CONTROLE MAJUSCULE*************
function supprimer_dernier_caractere(elm) {
	  var val = $(elm).val();
	var cursorPos = elm.selectionStart;
	$(elm).val(
	   val.substr(0,cursorPos-1) + 
	  val.substr(cursorPos,val.length) 
	);
	elm.selectionStart = cursorPos-1; 
	elm.selectionEnd = cursorPos-1;
	return true;
}

function upperText(id) {
	var text = new String( document.getElementById(id).value );
	text = text.toUpperCase();
	document.getElementById(id).value = text;
}

function controle_saisie(){
	$('body').delegate('input.only_Char','keyup',function(){

	    if(!$(this).val().match(/^[a-zA-Z]*$/))
	    	supprimer_dernier_caractere(this);
	    	upperText('NOM');
	});
	
	//Necessite l'utilisation du fichier 'jquery.maskedinput.js'
	jQuery(function($){
		   $("#TELEPHONE, #telephone, #telephone_mere, #telephone_pere, #fax_mere, #fax_pere").mask("99 999 99 99");
	});
	
	jQuery(function($){
		   $("#heure_recommandee").mask("23:59:59");
	});
	
	jQuery(function($){
		   $("#duree").mask("99");
	});
}

function info_bulle(){
	jQuery(document).ready(function($){
	    $('a, img, hass').tooltip({
	        animation: true,
	        html: true,
	        placement: 'bottom',
	        show: {
	            effect: "slideDown",
	            delay: 250
	          }

	    });
	});
}

//******************************************