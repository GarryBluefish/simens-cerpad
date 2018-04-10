var base_url = window.location.toString();
var tabUrl = base_url.split("public");
var entre = 0;
var siege;
var intensite;


function getMotifAdmissionDouleurFievre(indexMotif){
	
	if(indexMotif == -1 ){
		alert('Un autre motif à ajouter');
	}
	
	//Dès que fièvre est selectionné mettre la température superieure à 38,5
	//if(indexMotif == 1){
		//$('#temperatureFievre, #temperature').val(38.5).trigger('click');
	//}
}

function affichagePriseEnCharge(){
	popListeMedicaments();
}

function listeMotifsConsultation(){
	
	  setTimeout(function(){
		  $("#deuxiemeColonneIndicateur").html('<div id="artistiqueDesign" style="position: relative; background: fcfcfc; height: 40px; width: 50px;">  </div>');
	  },1000);

	
	  $( "#respondeMotif select" ).change(function(){

		  //Lorsqu'on sélectionne "Fièvre" == 1
		  //Lorsqu'on sélectionne "Fièvre" == 1
		  if($(this).val() == 1){
			  //popListeMedicamentsFievre();
		  }
		  
	   	  //Lorsqu'on sélectionne "Douleur" == 2
		  //Lorsqu'on sélectionne "Douleur" == 2
		  if($(this).val() == 2){
			  var top   = $(this).position().top-55; 
			  var right = $(this).position().right; 
			  
			  $("#deuxiemeColonneIndicateur").html('<table style="width: 100%; position: relative; top: '+top+'; right: '+right+';" ><tr style="width: 100%; vertical-align: top;"><td style="width: 6%; padding-right: 0px;"><div id="indiceDeuxiemeCol" >  </div></td> <td style="width: 94%; border: 3px solid #cccccc; " id="zoneTableauMotifDouleur"> </td></tr></table> ');
			  $("#zoneTableauMotifDouleur").html($('#tableauMotifDouleur').html());
			 
			  entre = 1;
			  recupDonnees();
			  $( "#siege" ).val(siege);
			  $( "#intensite" ).val(intensite);
			  
			  stopPropagation(); 
		  }else{
			  entre = 0;
			  $('#motif_admission1, #motif_admission2, #motif_admission3, #motif_admission4, #motif_admission5').trigger('click');
		  }
		  
		  
	  }).click(function(){
		
		  //Lorsqu'on sélectionne "Douleur"
		  //Lorsqu'on sélectionne "Douleur"
		  if($(this).val() == 2){ 
			  var top   = $(this).position().top-55; 
			  var right = $(this).position().right; 
			  
			  $("#deuxiemeColonneIndicateur").html('<table style="width: 100%; position: relative; top: '+top+'; right: '+right+';" ><tr style="width: 100%; vertical-align: top;"><td style="width: 6%; padding-right: 0px;"><div id="indiceDeuxiemeCol" >  </div></td> <td style="width: 94%; border: 3px solid #cccccc; " id="zoneTableauMotifDouleur"> </td></tr></table> ');
			  $("#zoneTableauMotifDouleur").html($('#tableauMotifDouleur').html());

			  entre = 1;
			  recupDonnees();
			  $( "#siege" ).val(siege);
			  $( "#intensite" ).val(intensite);
			  
			  stopPropagation();
		  }
		  
		  if(entre == 0){ $("#deuxiemeColonneIndicateur").html('<div id="artistiqueDesign" style="position: relative; background: fcfcfc; height: 40px; width: 50px;">  </div>'); }
	  });
	  
}


function placeSiegeDouleur(i, pos, val, T){
	if(i==1){
		siege = val;
		intensite = T;
	    $('#motif_admission'+pos).trigger('click');
	}
}


//Recuperer les donnees des champs
function recupDonnees(){
	  
	$( "#siege" ).keyup(function(){
		siege = $( "#siege").val(); 
	}).blur(function(){
		siege = $( "#siege").val(); 
	});
	
	$( "#intensite" ).keyup(function(){
		intensite = $( "#intensite").val(); 
		if(intensite > 3 && intensite < 11){ popListeMedicaments(); $('#poids').val($('#poidsP1 input').val()).attr('readonly', false); }
		else{$('#poids').attr('readonly', false);}
	}).blur(function(){
		intensite = $( "#intensite").val(); 
		if(intensite > 3 && intensite < 11){ popListeMedicaments(); $('#poids').val($('#poidsP1 input').val()).attr('readonly', false);}
		else{$('#poids').attr('readonly', false);}
	});

}


//Fonction appel du pop-pup
function popListeMedicamentsFievre(){
	listeMedicamentsFievre();
    $("#listeDesMedicamentsFievre").dialog('open');
} 


//Fonction appel du pop-pup
function popListeMedicaments(){
	listeMedicaments();
    $("#listeDesMedicaments").dialog('open');
} 

//================================================================================
//================================================================================
//================================================================================
//================================================================================
function listeMedicamentsFievre(){
	  $( "#listeDesMedicamentsFievre" ).dialog({
	    resizable: false,
	    height:330,
	    width:700,
	    autoOpen: false,
	    modal: true,
	    buttons: {
	    	
	        "Terminer": function() {
	        	
	        	$( this ).dialog( "close" );
	        },
	        
	   }
	  });
}

//================================================================================
//================================================================================
//================================================================================
//================================================================================
function listeMedicaments(){
	  $( "#listeDesMedicaments" ).dialog({
	    resizable: false,
	    height:480,
	    width:700,
	    autoOpen: false,
	    modal: true,
	    buttons: {
	    	
	        "Terminer": function() {
	        	
//	            $( this ).dialog( "close" ); 
//	            
//	            var chemin = tabUrl[0]+'public/facturation/supprimer-facturation';
//	            $.ajax({
//	                type: 'POST',
//	                url: chemin ,
//	                data:{ 'idfacturation':idfacturation },
//	                success: function(data) {
//	                	     var result = jQuery.parseJSON(data);  
//	                	     if(result == 1){
//	                	    	 alert('impossible de supprimer il y a des analyses ayant deja des resultats '); return false;
//	                	     } else {
//		                	     $("#"+idfacturation).parent().parent().parent().fadeOut(function(){ 
//		                	    	 $(location).attr("href",tabUrl[0]+"public/facturation/liste-patients-admis");
//		                	     });
//	                	     }
//	                	     
//	                },
//	                error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
//	                dataType: "html"
//	            });
	    	     
	        	$( this ).dialog( "close" );
	        },
	        
	   }
	  });
  }
  

function cacherToutMotif(){
 $(function(){
	$('#motif1').toggle(false);
	$('#motif2').toggle(false);
	$('#motif3').toggle(false);
	$('#motif4').toggle(false);
	$('#motif5').toggle(false);
 });
}
var nbChampMotif;
function afficherMotif(nbmotif_admission) {
 $(function(){
	for(var i = 1; i<=nbmotif_admission ; i++){ 
		$('#motif'+i).toggle(true);
	}
 });
 nbChampMotif = nbmotif_admission;
 if(nbChampMotif == 1){
		$('#supprimer_motif_img').toggle(false);
	}
 if(nbChampMotif == 5){
		$('#ajouter_motif_img').toggle(false);
	}
 if(nbChampMotif == 1){
		$(".supprimerMotif1" ).replaceWith(
				"<img class='supprimerMotif' src='"+tabUrl[0]+"public/images/images/sup2.png' />"
			);
	}
 ajouterMotif();
 supprimerMotif();
 supprimerLeMotif1();
}

function ajouterMotif(){ 
	$('#ajouter_motif_img').click(function(){ 
		nbChampMotif++;
		$('#motif'+(nbChampMotif)).toggle(true);
		
		if(nbChampMotif == 5){
			$('#ajouter_motif_img').toggle(false);
		}
		if(nbChampMotif == 2){
			$('#supprimer_motif_img').toggle(true);
			$(".supprimerMotif" ).replaceWith(
					"<img class='supprimerMotif1' style='cursor: pointer;' src='"+tabUrl[0]+"public/images/images/sup.png' title='supprimer' />"
				);
			supprimerLeMotif1();
		}
	});
	if(nbChampMotif == 5){
		$('#ajouter_motif_img').toggle(false);
	}
}

function supprimerMotif(){ 
	$('#supprimer_motif_img').click(function(){ entre = 0;
		$("#motif_admission"+nbChampMotif).val('');
		$('#motif'+nbChampMotif).toggle(false);
		nbChampMotif--;
		if(nbChampMotif == 1){
			$('#supprimer_motif_img').toggle(false);
			$(".supprimerMotif1" ).replaceWith(
					"<img class='supprimerMotif' src='"+tabUrl[0]+"public/images/images/sup2.png' />"
				);
		}
		if(nbChampMotif == 4){
			$('#ajouter_motif_img').toggle(true);
		}
		
		//Faire remonter les infos complémentaires sur la douleur
		$('#motif_admission1, #motif_admission2, #motif_admission3, #motif_admission4, #motif_admission5').trigger('click');
		
		
	});
}

function supprimerLeMotif1(){
	$(".supprimerMotif1").click(function(){ entre = 0;
		for(var i=1; i<nbChampMotif; i++){
			$("#motif_admission"+i).val( $("#motif_admission"+(i+1)).val() );
		}
		$("#motif_admission"+i).val('');
		$('#motif'+i).toggle(false);
		if(nbChampMotif == 5){
			$('#ajouter_motif_img').toggle(true);
		}
		if(nbChampMotif == 2){
			$('#supprimer_motif_img').toggle(false);
			$(".supprimerMotif1" ).replaceWith(
					"<img class='supprimerMotif' src='"+tabUrl[0]+"public/images/images/sup2.png' />"
				);
		}
		nbChampMotif--;
		
		//Faire remonter les infos complémentaires sur la douleur
		$('#motif_admission1, #motif_admission2, #motif_admission3, #motif_admission4, #motif_admission5').trigger('click');
		
		return false;
	});
}

function supprimerUnMotif(){
	$(".supprimerMotif2").click(function(){ entre = 0;
		for(var i=2; i<nbChampMotif; i++){
			$("#motif_admission"+i).val( $("#motif_admission"+(i+1)).val() );
		}
		$("#motif_admission"+i).val('');
		$('#motif'+i).toggle(false);
		if(nbChampMotif == 5){
			$('#ajouter_motif_img').toggle(true);
		}
		if(nbChampMotif == 2){
			$('#supprimer_motif_img').toggle(false);
			$(".supprimerMotif1" ).replaceWith(
					"<img class='supprimerMotif' src='"+tabUrl[0]+"public/images/images/sup2.png' />"
				);
		}
		nbChampMotif--;
		
		//Faire remonter les infos complémentaires sur la douleur
		$('#motif_admission1, #motif_admission2, #motif_admission3, #motif_admission4, #motif_admission5').trigger('click');
		
		return false;
	});
	
	$(".supprimerMotif3").click(function(){ entre = 0;
		for(var i=3; i<nbChampMotif; i++){
			$("#motif_admission"+i).val( $("#motif_admission"+(i+1)).val() );
		}
		$("#motif_admission"+i).val('');
		$('#motif'+i).toggle(false);
		if(nbChampMotif == 5){
			$('#ajouter_motif_img').toggle(true);
		}
		if(nbChampMotif == 2){
			$('#supprimer_motif_img').toggle(false);
			$(".supprimerMotif1" ).replaceWith(
					"<img class='supprimerMotif' src='"+tabUrl[0]+"public/images/images/sup2.png' />"
				);
		}
		nbChampMotif--;
		
		//Faire remonter les infos complémentaires sur la douleur
		$('#motif_admission1, #motif_admission2, #motif_admission3, #motif_admission4, #motif_admission5').trigger('click');
		
		return false;
	});
	
	$(".supprimerMotif4").click(function(){ entre = 0;
		for(var i=4; i<nbChampMotif; i++){
			$("#motif_admission"+i).val( $("#motif_admission"+(i+1)).val() );
		}
		$("#motif_admission"+i).val('');
		$('#motif'+i).toggle(false);
		if(nbChampMotif == 5){
			$('#ajouter_motif_img').toggle(true);
		}
		if(nbChampMotif == 2){
			$('#supprimer_motif_img').toggle(false);
			$(".supprimerMotif1" ).replaceWith(
					"<img class='supprimerMotif' src='"+tabUrl[0]+"public/images/images/sup2.png' />"
				);
		}
		nbChampMotif--;
		
		//Faire remonter les infos complémentaires sur la douleur
		$('#motif_admission1, #motif_admission2, #motif_admission3, #motif_admission4, #motif_admission5').trigger('click');
		
		return false;
	});
	
	$(".supprimerMotif5").click(function(){ entre = 0;
		for(var i=5; i<nbChampMotif; i++){
			$("#motif_admission"+i).val( $("#motif_admission"+(i+1)).val() );
		}
		$("#motif_admission"+i).val('');
		$('#motif'+i).toggle(false);
		if(nbChampMotif == 5){
			$('#ajouter_motif_img').toggle(true);
		}
		if(nbChampMotif == 2){
			$('#supprimer_motif_img').toggle(false);
			$(".supprimerMotif1" ).replaceWith(
					"<img class='supprimerMotif' src='"+tabUrl[0]+"public/images/images/sup2.png' />"
				);
		}
		nbChampMotif--;
		
		//Faire remonter les infos complémentaires sur la douleur
		$('#motif_admission1, #motif_admission2, #motif_admission3, #motif_admission4, #motif_admission5').trigger('click');
		
		return false;
	});
}


//********************* motif_admission *****************************
//********************* motif_admission *****************************
$(function(){
	var motif1 = $("#motif_admission1");
	var motif2 = $("#motif_admission2");
	var motif3 = $("#motif_admission3");
	var motif4 = $("#motif_admission4");
	var motif5 = $("#motif_admission5");
	
	
	//Au debut on affiche pas le bouton modifier
	$("#bouton_motif_modifier").toggle(false);
	//Au debut on affiche le bouton valider
	$("#bouton_motif_valider").toggle(true);
	
	//Au debut on desactive tous les champs
	motif1.attr( 'readonly', false);
	motif2.attr( 'readonly', false);
	motif3.attr( 'readonly', false);
	motif4.attr( 'readonly', false);
	motif5.attr( 'readonly', false);
	$( "#siege").attr( 'readonly', false);
	$("#intensite").attr( 'readonly', false);
	
	$("#bouton_motif_valider").click(function(){  
		motif1.attr( 'readonly', true);
		motif2.attr( 'readonly', true);
		motif3.attr( 'readonly', true);
		motif4.attr( 'readonly', true);
		motif5.attr( 'readonly', true);
		$( "#siege").attr( 'readonly', true);
		$("#intensite").attr( 'readonly', true);
		
		$("#bouton_motif_modifier").toggle(true);
		$("#bouton_motif_valider").toggle(false);
		
		$('#ajouter_motif_img').toggle(false);
		$('#supprimer_motif_img').toggle(false);
		
		$('.supprimerMotif1, .supprimerMotif2, .supprimerMotif3, .supprimerMotif4, .supprimerMotif5').toggle(false);
		return false;
	});
	
	$("#bouton_motif_modifier").click(function(){
		motif1.attr( 'readonly', false);
		motif2.attr( 'readonly', false);
		motif3.attr( 'readonly', false);
		motif4.attr( 'readonly', false);
		motif5.attr( 'readonly', false);
	    $( "#siege").attr( 'readonly', false);
	    $("#intensite").attr( 'readonly', false);
		
		$("#bouton_motif_modifier").toggle(false);
		$("#bouton_motif_valider").toggle(true);
		
		if(nbChampMotif != 5) { $('#ajouter_motif_img').toggle(true); }
		if(nbChampMotif != 1) { $('#supprimer_motif_img').toggle(true); }
		
		$('.supprimerMotif1, .supprimerMotif2, .supprimerMotif3, .supprimerMotif4, .supprimerMotif5').toggle(true);
		return false;
	});
	
});