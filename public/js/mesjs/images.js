
/* Fonction IMAGE 1 */
function image(){
$('#frm input[type="file"]').change(function() {
		var file = $(this);
 		var reader = new FileReader;
 
		reader.onload = function(event) {
	    		var img = new Image();
 
        		img.onload = function() {
				var width  = 250;
				var height = 130;
				
				var canvas = $('<canvas></canvas>').attr({ width: width, height: height });
				file.replaceWith(canvas);
				var context = canvas[0].getContext('2d');
	        	    	context.drawImage(img, 0, 0, width, height);
			    };
 
        		img.src = event.target.result;
    	};
    	reader.readAsDataURL(file[0].files[0]);
    	
    }); 

/*** icons image 1 ***/
	$('#frm input[type="file"]').change(function(){
		$('<div class="supp" id="sup" ><a href=""><img src="/simens/public/images_icons/sup.png" /> </a></div>').insertAfter('div.icon');
	    $('<div class="zoom_" ><a class="various" id="zooom" href="/simens/public/images_icons/images/1_radio-b.jpg"><img src="/simens/public/images_icons/search_16.png" /></a></div>').insertBefore('div.supp');
	    /**** On créer un input pour gérer le comptage des champs dans le script traitement médical****/
	    $('<div id="cpt"><input type="hidden" ></div>').insertAfter('div.icon'); /** on met type hidden et div.icon parceque c'est pas visible juste pour gérer le comptage dans traitement médical**/

	    /*** Gestion de l'évènement clique sur supprimer***/
	    /*** Gestion de l'évènement clique sur supprimer***/
	    $("#sup").click(function(e) { img=1; 
           e.preventDefault();
           confirmation();
           $("#confirmation").dialog('open');
        });
   
	});
}

/*Fonction IMAGE 2*/

function image2(){
	$('#frm2 input[type="file"]').change(function() {
			var file = $(this);
	 		var reader = new FileReader;
	 
			reader.onload = function(event) {
		    		var img = new Image();
	 
	        		img.onload = function() {
					var width  = 250;
					var height = 130;
					
					var canvas = $('<canvas></canvas>').attr({ width: width, height: height });
					file.replaceWith(canvas);
					var context = canvas[0].getContext('2d');
		        	    	context.drawImage(img, 0, 0, width, height);
				    };
	 
	        		img.src = event.target.result;
	    	};
	    	reader.readAsDataURL(file[0].files[0]);
	    	
	    }); 

	/*** icons image 2 ***/
		$('#frm2 input[type="file"]').change(function(){
			$('<div class="supp2" id="sup2" ><a href=""><img src="/simens/public/images_icons/sup.png" /> </a></div>').insertAfter('div.icon2');
		    $('<div class="zoom_2" ><a class="various" id="zoom2" href="/simens/public/images_icons/images/2_echo.jpg"><img src="/simens/public/images_icons/search_16.png" /></a></div>').insertBefore('div.supp2');
		    /**** On créer un input pour gérer le comptage des champs dans le script traitement médical****/
		    $('<div id="cpt2"><input type="hidden" ></div>').insertAfter('div.icon2'); /** on met type hidden et div.icon parceque c'est pas visible juste pour gérer le comptage dans traitement médical**/

		    /*** Gestion de l'évènement clique sur supprimer***/
		    $('#sup2').click(function(e){ img=2;
		       e.preventDefault();
	           confirmation();
	           $("#confirmation").dialog('open');	
		    }); 
	   
		});
}

/*Fonction IMAGE 3*/

function image3(){
	$('#frm3 input[type="file"]').change(function() {
			var file = $(this);
	 		var reader = new FileReader;
	 
			reader.onload = function(event) {
		    		var img = new Image();
	 
	        		img.onload = function() {
					var width  = 250;
					var height = 130;
					
					var canvas = $('<canvas></canvas>').attr({ width: width, height: height });
					file.replaceWith(canvas);
					var context = canvas[0].getContext('2d');
		        	    	context.drawImage(img, 0, 0, width, height);
				    };
	 
	        		img.src = event.target.result;
	    	};
	    	reader.readAsDataURL(file[0].files[0]);
	    	
	    }); 

	/*** icons image 3 ***/
		$('#frm3 input[type="file"]').change(function(){
			$('<div class="supp3" id="sup3" ><a href=""><img src="/simens/public/images_icons/sup.png" /> </a></div>').insertAfter('div.icon3');
		    $('<div class="zoom_3" ><a class="various" id="zoom3" href="/simens/public/images_icons/images/3_fibro.jpg"><img src="/simens/public/images_icons/search_16.png" /></a></div>').insertBefore('div.supp3');
		    /**** On créer un input pour gérer le comptage des champs dans le script traitement médical****/
		    $('<div id="cpt3"><input type="hidden" ></div>').insertAfter('div.icon3'); /** on met type hidden et div.icon parceque c'est pas visible juste pour gérer le comptage dans traitement médical**/

		    /*** Gestion de l'évènement clique sur supprimer***/
		    $('#sup3').click(function(e){ img=3;
		       e.preventDefault();
	           confirmation();
	           $("#confirmation").dialog('open');	
		    });  
	   
		});
}

/*Fonction IMAGE 4*/

function image4(){
	$('#frm4 input[type="file"]').change(function() {
			var file = $(this);
	 		var reader = new FileReader;
	 
			reader.onload = function(event) {
		    		var img = new Image();
	 
	        		img.onload = function() {
					var width  = 250;
					var height = 130;
					
					var canvas = $('<canvas></canvas>').attr({ width: width, height: height });
					file.replaceWith(canvas);
					var context = canvas[0].getContext('2d');
		        	    	context.drawImage(img, 0, 0, width, height);
				    };
	 
	        		img.src = event.target.result;
	    	};
	    	reader.readAsDataURL(file[0].files[0]);
	    	
	    }); 

	/*** icons image 4 ***/
		$('#frm4 input[type="file"]').change(function(){
			$('<div class="supp4" id="sup4" ><a href=""><img src="/simens/public/images_icons/sup.png" /> </a></div>').insertAfter('div.icon4');
		    $('<div class="zoom_4" ><a class="various" id="zoom4" href="/simens/public/images_icons/images/4_Scanner-a.jpg"><img src="/simens/public/images_icons/search_16.png" /></a></div>').insertBefore('div.supp4');
		    /**** On créer un input pour gérer le comptage des champs dans le script traitement médical****/
		    $('<div id="cpt4"><input type="hidden" ></div>').insertAfter('div.icon4'); /** on met type hidden et div.icon parceque c'est pas visible juste pour gérer le comptage dans traitement médical**/

		    /*** Gestion de l'évènement clique sur supprimer***/
		    $('#sup4').click(function(e){ img=4;
		       e.preventDefault();
	           confirmation();
	           $("#confirmation").dialog('open');	
		    });  
	   
		});
}

/*Fonction IMAGE 5*/

function image5(){
	$('#frm5 input[type="file"]').change(function() {
			var file = $(this);
	 		var reader = new FileReader;
	 
			reader.onload = function(event) {
		    		var img = new Image();
	 
	        		img.onload = function() {
					var width  = 250;
					var height = 130;
					
					var canvas = $('<canvas></canvas>').attr({ width: width, height: height });
					file.replaceWith(canvas);
					var context = canvas[0].getContext('2d');
		        	    	context.drawImage(img, 0, 0, width, height);
				    };
	 
	        		img.src = event.target.result;
	    	};
	    	reader.readAsDataURL(file[0].files[0]);
	    	
	    }); 

	/*** icons image 5 ***/
		$('#frm5 input[type="file"]').change(function(){
			$('<div class="supp5" id="sup5" ><a href=""><img src="/simens/public/images_icons/sup.png" /> </a></div>').insertAfter('div.icon5');
		    $('<div class="zoom_5" ><a class="various" id="zoom5" href="/simens/public/images_icons/images/5_IRM.jpg"><img src="/simens/public/images_icons/search_16.png" /></a></div>').insertBefore('div.supp5');
		    /**** On créer un input pour gérer le comptage des champs dans le script traitement médical****/
		    $('<div id="cpt5"><input type="hidden" ></div>').insertAfter('div.icon5'); /** on met type hidden et div.icon parceque c'est pas visible juste pour gérer le comptage dans traitement médical**/

		    /*** Gestion de l'évènement clique sur supprimer***/
		    $('#sup5').click(function(e){ img=5;
		       e.preventDefault();
	           confirmation();
	           $("#confirmation").dialog('open');	
		    });  
	   
		});
}