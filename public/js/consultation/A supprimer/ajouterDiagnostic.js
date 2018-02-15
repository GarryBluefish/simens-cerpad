function cacherTout(){
	$('#diag1').toggle(false);
	$('#diag2').toggle(false);
	$('#diag3').toggle(false);
	$('#diag4').toggle(false);
}
var nbChamp;
function afficher(nbDiag) {
	for(var i = 1; i<=nbDiag ; i++){ 
		$('#diag'+i).toggle(true);
	}
 nbChamp = nbDiag;

 if(nbChamp == 4){
		$('#ajouter_diagnostic_img').toggle(false);
	}
 
 if(nbChamp == 1){
		$('#supprimer_diagnostic_img').toggle(false);
		$(".supprimerDiag1" ).replaceWith(
				"<img class='supprimerDiag' src='../images/images/sup2.png' />"
			);
	}
 
 ajouterDiagnostic();
 supprimerDiagnostic();
 supprimerLeDiagnostic1();
}

function ajouterDiagnostic(){ 
	$('#ajouter_diagnostic_img').click(function(){ 
		nbChamp++;
		$('#diag'+(nbChamp)).toggle(true);
		
		if(nbChamp == 4){
			$('#ajouter_diagnostic_img').toggle(false);
		}
		if(nbChamp == 2){
			$('#supprimer_diagnostic_img').toggle(true);
			$(".supprimerDiag" ).replaceWith(
					"<img class='supprimerDiag1' style='cursor: pointer;' src='../images/images/sup.png' title='supprimer' />"
				);
			supprimerLeDiagnostic1();
		}
	});
	if(nbChamp == 4){
		$('#ajouter_diagnostic_img').toggle(false);
	}
}

function supprimerDiagnostic(){ 
	$('#supprimer_diagnostic_img').click(function(){ 
		$("#diagnostic"+nbChamp).val('');
		$('#diag'+nbChamp).toggle(false);
		nbChamp--;
		if(nbChamp == 1){
			$('#supprimer_diagnostic_img').toggle(false);
			$(".supprimerDiag1" ).replaceWith(
					"<img class='supprimerDiag' src='../images/images/sup2.png' />"
				);
		}
		if(nbChamp == 3){
			$('#ajouter_diagnostic_img').toggle(true);
		}
	});
}

function supprimerLeDiagnostic1(){
	$(".supprimerDiag1").click(function(){ 
		for(var i=1; i<nbChamp; i++){
			$("#diagnostic"+i).val( $("#diagnostic"+(i+1)).val() );
		}
		$("#diagnostic"+i).val('');
		$('#diag'+i).toggle(false);
		if(nbChamp == 4){
			$('#ajouter_diagnostic_img').toggle(true);
		}
		if(nbChamp == 2){
			$('#supprimer_diagnostic_img').toggle(false);
			$(".supprimerDiag1" ).replaceWith(
					"<img class='supprimerDiag' src='../images/images/sup2.png' />"
				);
		}
		nbChamp--;
		return false;
	});
}

function supprimerUnDiagnostic(){
	$(".supprimerDiag2").click(function(){ 
		for(var i=2; i<nbChamp; i++){
			$("#diagnostic"+i).val( $("#diagnostic"+(i+1)).val() );
		}
		$("#diagnostic"+i).val('');
		$('#diag'+i).toggle(false);
		if(nbChamp == 4){
			$('#ajouter_diagnostic_img').toggle(true);
		}
		if(nbChamp == 2){
			$('#supprimer_diagnostic_img').toggle(false);
			$(".supprimerDiag1" ).replaceWith(
					"<img class='supprimerDiag' src='../images/images/sup2.png' />"
				);
		}
		nbChamp--;
		return false;
	});
	
	$(".supprimerDiag3").click(function(){ 
		for(var i=3; i<nbChamp; i++){
			$("#diagnostic"+i).val( $("#diagnostic"+(i+1)).val() );
		}
		$("#diagnostic"+i).val('');
		$('#diag'+i).toggle(false);
		if(nbChamp == 4){
			$('#ajouter_diagnostic_img').toggle(true);
		}
		if(nbChamp == 2){
			$('#supprimer_diagnostic_img').toggle(false);
			$(".supprimerDiag1" ).replaceWith(
					"<img class='supprimerDiag' src='../images/images/sup2.png' />"
				);
		}
		nbChamp--;
		return false;
	});
	
	$(".supprimerDiag4").click(function(){ 
		for(var i=4; i<nbChamp; i++){
			$("#diagnostic"+i).val( $("#diagnostic"+(i+1)).val() );
		}
		$("#diagnostic"+i).val('');
		$('#diag'+i).toggle(false);
		if(nbChamp == 4){
			$('#ajouter_diagnostic_img').toggle(true);
		}
		if(nbChamp == 2){
			$('#supprimer_diagnostic_img').toggle(false);
			$(".supprimerDiag1" ).replaceWith(
					"<img class='supprimerDiag' src='../images/images/sup2.png' />"
				);
		}
		nbChamp--;
		return false;
	});
}


//********************* DIAGNOSTIC *****************************
//********************* DIAGNOSTIC *****************************
	var diagnostic1 = $("#diagnostic1");
	var diagnostic2 = $("#diagnostic2");
	var diagnostic3 = $("#diagnostic3");
	var diagnostic4 = $("#diagnostic4");
	
	//Au debut on affiche pas le bouton modifier
	$("#bouton_diagnostic_modifier").toggle(false);
	//Au debut on affiche le bouton valider
	$("#bouton_diagnostic_valider").toggle(true);
	
	//Au debut on desactive tous les champs
	diagnostic1.attr( 'readonly', false); 
	diagnostic2.attr( 'readonly', false);
	diagnostic3.attr( 'readonly', false);
	diagnostic4.attr( 'readonly', false);
	
	$("#bouton_diagnostic_valider").click(function(){
		diagnostic1.attr( 'readonly', true);
		diagnostic2.attr( 'readonly', true);
		diagnostic3.attr( 'readonly', true);
		diagnostic4.attr( 'readonly', true);
		$("#bouton_diagnostic_modifier").toggle(true);
		$("#bouton_diagnostic_valider").toggle(false);
		
		$('#ajouter_diagnostic_img').toggle(false);
		$('#supprimer_diagnostic_img').toggle(false);
		
		$('.supprimerDiag1, .supprimerDiag2, .supprimerDiag3, .supprimerDiag4').toggle(false);
		return false;
	});
	
	$("#bouton_diagnostic_modifier").click(function(){
		diagnostic1.attr( 'readonly', false);
		diagnostic2.attr( 'readonly', false);
		diagnostic3.attr( 'readonly', false);
		diagnostic4.attr( 'readonly', false);
		$("#bouton_diagnostic_modifier").toggle(false);
		$("#bouton_diagnostic_valider").toggle(true);
		
		if(nbChamp != 4) { $('#ajouter_diagnostic_img').toggle(true); }
		if(nbChamp != 1) { $('#supprimer_diagnostic_img').toggle(true); }
		
		$('.supprimerDiag1, .supprimerDiag2, .supprimerDiag3, .supprimerDiag4').toggle(true);
		return false;
	});
	
