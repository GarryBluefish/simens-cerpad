
$(function() {
var spinner = $( "#date_rv" ).spinner();
var spinner_heure = $("#motif_rv").spinner();
  $( "#disable" ).click(function(){
      spinner.spinner( "disable" ); //désactiver la date
      spinner_heure.spinner( "disable" ); //désactiver l'heure
      $("#disable_bouton").toggle(true); //on affiche le bouton permettant de désactiver les champs
      
  });
  
  $( "button" ).button();

  //Au debut on affiche pas le bouton modifier, on l'affiche seulement apres impression
  $("#disable_bouton").toggle(false);
  
  //Activer avec le bouton 'modifier'
  $( "#disable_bouton" ).click(function(){
	  spinner_heure.spinner( "enable" ); //activer l'heure
	  spinner.spinner( "enable" ); //activer la date
	  $("#disable_bouton").toggle(false); //on cache le bouton permettant de désactiver
	  return  false;
  });
  
});

