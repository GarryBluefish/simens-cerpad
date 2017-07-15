var ledoc = 0;
    
function jouer_son() {
	$("sonAuSurvol").innerHTML = '<p><object type="audio/mpeg" width="0" height="0" data="/simens/public/images_icons/bipSurvol.mp3" ><param name="filename" value="/simens/public/images_icons/bipSurvol.mp3" /><param name="autostart" value="true" /><param name="loop" value="false" /></object></p>';
}
     
     
function initialise(docu) {
	ledoc = docu;
}


function $(x) {
	return ledoc.getElementById(x);
}