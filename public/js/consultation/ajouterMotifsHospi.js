var base_url = window.location.toString();
var tabUrl = base_url.split("public");

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
	$('#supprimer_motif_img').click(function(){ 
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
	});
}

function supprimerLeMotif1(){
	$(".supprimerMotif1").click(function(){ 
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
		return false;
	});
}

function supprimerUnMotif(){
	$(".supprimerMotif2").click(function(){ 
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
		return false;
	});
	
	$(".supprimerMotif3").click(function(){ 
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
		return false;
	});
	
	$(".supprimerMotif4").click(function(){ 
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
		return false;
	});
	
	$(".supprimerMotif5").click(function(){ 
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
	
	$("#bouton_motif_valider").click(function(){
		motif1.attr( 'readonly', true).css({'background':'#f8f8f8'});
		motif2.attr( 'readonly', true).css({'background':'#f8f8f8'});
		motif3.attr( 'readonly', true).css({'background':'#f8f8f8'});
		motif4.attr( 'readonly', true).css({'background':'#f8f8f8'});
		motif5.attr( 'readonly', true).css({'background':'#f8f8f8'});
		$("#bouton_motif_modifier").toggle(true);
		$("#bouton_motif_valider").toggle(false);
		
		$('#ajouter_motif_img').toggle(false);
		$('#supprimer_motif_img').toggle(false);
		
		$('.supprimerMotif1, .supprimerMotif2, .supprimerMotif3, .supprimerMotif4, .supprimerMotif5').toggle(false);
		return false;
	});
	
	$("#bouton_motif_modifier").click(function(){
		motif1.attr( 'readonly', false).css({'background':'#fff'});
		motif2.attr( 'readonly', false).css({'background':'#fff'});
		motif3.attr( 'readonly', false).css({'background':'#fff'});
		motif4.attr( 'readonly', false).css({'background':'#fff'});
		motif5.attr( 'readonly', false).css({'background':'#fff'});
		$("#bouton_motif_modifier").toggle(false);
		$("#bouton_motif_valider").toggle(true);
		
		if(nbChampMotif != 5) { $('#ajouter_motif_img').toggle(true); }
		if(nbChampMotif != 1) { $('#supprimer_motif_img').toggle(true); }
		
		$('.supprimerMotif1, .supprimerMotif2, .supprimerMotif3, .supprimerMotif4, .supprimerMotif5').toggle(true);
		return false;
	});
	
});