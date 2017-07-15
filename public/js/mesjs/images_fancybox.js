function confirmation(){
	/***BOITE DE DIALOG POUR LA CONFIRMATION DE SUPPRESSION**/
	$( "#confirmation" ).dialog({
	    resizable: false,
	    height:170,
	    width:485,
	    autoOpen: false,
	    modal: true,
	    buttons: {
	        "Oui": function() {
	            $( this ).dialog( "close" );

	            //si c'est sup1
	            if(img == 1) {
	                
	            	$('#sup, #zooom').remove(); //on efface les icons  
	                
	                $('#frm').children().remove(); // on supprime l'image 

	                $('<input type="file" />').appendTo($('#frm'));  // on créer un nouveau input

	                $('#cpt').remove(); // on supprime le input hidden qu on avait créer pour gérer le comptage dans traitement médical

	                image(); // on appel la fonction recursivement dans js/mesjs/images.js
	                       	       
	    	    	return false;

	            }
	            //si c'est sup2
	            else
	            	if(img == 2){
	            		$('#sup2, #zoom2').remove(); //on efface les icons  
	                    
	                    $('#frm2').children().remove(); // on supprime l'image 

	                    $('<input type="file" />').appendTo($('#frm2'));  // on créer un nouveau input

	                    $('#cpt2').remove(); // on supprime le input hidden qu on avait créer pour gérer le comptage dans traitement médical

	                    image2(); // on appel la fonction recursivement dans js/mesjs/images.js
	                           	       
	        	    	return false;
	            	
	            	}
	            	else
	            		if(img == 3){
	            			$('#sup3, #zoom3').remove(); //on efface les icons  
	                        
	                        $('#frm3').children().remove(); // on supprime l'image 

	                        $('<input type="file" />').appendTo($('#frm3'));  // on créer un nouveau input

	                        $('#cpt3').remove(); // on supprime le input hidden qu on avait créer pour gérer le comptage dans traitement médical

	                        image3(); // on appel la fonction recursivement dans js/mesjs/images.js
	                               	       
	            	    	return false;
	            			
	            		}
	            		else
	            			if(img == 4){
	            				$('#sup4, #zoom4').remove(); //on efface les icons  
	            	            
	            	            $('#frm4').children().remove(); // on supprime l'image 

	            	            $('<input type="file" />').appendTo($('#frm4'));  // on créer un nouveau input

	            	            $('#cpt4').remove(); // on supprime le input hidden qu on avait créer pour gérer le comptage dans traitement médical

	            	            image4(); // on appel la fonction recursivement dans js/mesjs/images.js
	            	                   	       
	            		    	return false;
	            				
	            			}
	            			else
	            				if(img == 5){

	            			    	$('#sup5, #zoom5').remove(); //on efface les icons  
	            		            
	            		            $('#frm5').children().remove(); // on supprime l'image 

	            		            $('<input type="file" />').appendTo($('#frm5'));  // on créer un nouveau input

	            		            $('#cpt5').remove(); // on supprime le input hidden qu on avait créer pour gérer le comptage dans traitement médical

	            		            image5(); // on appel la fonction recursivement dans js/mesjs/images.js
	            		                   	       
	            			    	return false;
	            				}
	                
	        },
	        "Annuler": function() {
	            $( this ).dialog( "close" );
	        }
	    }
	});
}

function images_fancybox(){
$(document).ready(function() {

/*** zoom image 1 ***/
    $("#zooom").fancybox({
	   openEffect	: 'elastic',
	   closeEffect	: 'elastic',

	   helpers : {
		    title : {
			type : 'inside'
	        }
	   },
	   afterLoad : function() {
		   this.title = 'Image Radio';
	   }
	   
    });
/*** zoom Image 2 ***/
    $("#zoom2").fancybox({
  	   openEffect	: 'elastic',
  	   closeEffect	: 'elastic',

  	   helpers : {
  		    title : {
  			type : 'inside'
  	        }
  	   },
  	   afterLoad : function() {
  		 this.title = 'Image Ecographie';
  	   }
  	   
      });
/*** zoom Image 3 ***/
    $("#zoom3").fancybox({
  	   openEffect	: 'elastic',
  	   closeEffect	: 'elastic',

  	   helpers : {
  		    title : {
  			type : 'inside'
  	        }
  	   },
  	   afterLoad : function() {
  			this.title = 'Image Fibroscopie'; //+ (this.index + 1) + ' of ' + this.group.length + (this.title ? ' - ' + this.title : '');
  	   }
  	   
      });
/*** zoom Image 4 ***/
    $("#zoom4").fancybox({
  	   openEffect	: 'elastic',
  	   closeEffect	: 'elastic',

  	   helpers : {
  		    title : {
  			type : 'inside'
  	        }
  	   },
  	   afterLoad : function() {
  			this.title = 'Image Scanner';
  	   }
  	   
      });
/*** zoom Image 5 ***/
    $("#zoom5").fancybox({
  	   openEffect	: 'elastic',
  	   closeEffect	: 'elastic',

  	   helpers : {
  		    title : {
  			type : 'inside'
  	        }
  	   },
  	   afterLoad : function() {
  			this.title = 'Image IRM';
  	   }
  	   
      });
    
}); 

$(document).ready(function() {
   if(app1 == 1){
	   $('#frm input[type="file"]').replaceWith('<div id="imge1" style=""><img style="height: 130px; width: 250px; border-radius: 10px;" src="/simens/public/images_icons/images/'+nom1+'.jpg" /></div>');
	   $('<div class="supp" id="sup" ><a href=""><img src="/simens/public/images_icons/sup.png" /> </a></div>').insertAfter('div.icon');
       $('<div class="zoom_" ><a class="various" id="zooom" href="/simens/public/images_icons/images/'+nom1+'.jpg"><img src="/simens/public/images_icons/search_16.png" /></a></div>').insertBefore('div.supp');
       /**** On créer un input pour gérer le comptage des champs dans le script traitement médical****/
       $('<div id="cpt"><input type="hidden" ></div>').insertAfter('div.icon'); /** on met type hidden et div.icon parceque c'est pas visible juste pour gérer le comptage dans traitement médical**/

       /*** Gestion de l'évènement clique sur supprimer***/
      $("#sup").click(function(e) { img=1; 
          e.preventDefault();
          confirmation();
          $("#confirmation").dialog('open');
       });
	}
	
   if(app2 == 2){
	   $('#frm2 input[type="file"]').replaceWith('<div id="imge1" style=""><img style="height: 130px; width: 250px; border-radius: 10px;" src="/simens/public/images_icons/images/'+nom2+'.jpg" /></div>');
       $('<div class="supp2" id="sup2" ><a href=""><img src="/simens/public/images_icons/sup.png" /> </a></div>').insertAfter('div.icon2');
   	   $('<div class="zoom_2" ><a class="various" id="zoom2" href="/simens/public/images_icons/images/'+nom2+'.jpg"><img src="/simens/public/images_icons/search_16.png" /></a></div>').insertBefore('div.supp2');
       /**** On créer un input pour gérer le comptage des champs dans le script traitement médical****/
       $('<div id="cpt2"><input type="hidden" ></div>').insertAfter('div.icon2'); /** on met div.icon parceque c'est pas visible**/

       /*** Gestion de l'évènement clique sur supprimer***/
	    $('#sup2').click(function(e){ img=2;
	       e.preventDefault();
          confirmation();
          $("#confirmation").dialog('open');	
	    }); 
	}
   
	
	$('#frm input[type="file"], #frm2 input[type="file"], #frm3 input[type="file"], #frm4 input[type="file"], #frm5 input[type="file"]').change(function() {
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
	    $('<div class="zoom_" ><a class="various" id="zooom" href="/simens/public/images_icons/images/1_radio-c.jpg"><img src="/simens/public/images_icons/search_16.png" /></a></div>').insertBefore('div.supp');
	    /**** On créer un input pour gérer le comptage des champs dans le script traitement médical****/
	    $('<div id="cpt"><input type="hidden" ></div>').insertAfter('div.icon'); /** on met type hidden et div.icon parceque c'est pas visible juste pour gérer le comptage dans traitement médical**/

	    /*** Gestion de l'évènement clique sur supprimer***/
	    $("#sup").click(function(e) { img=1; 
           e.preventDefault();
           confirmation();
           $("#confirmation").dialog('open');
        });
	    
	});
/*** icons image 2 ***/
	$('#frm2 input[type="file"]').change(function(){
		$('<div class="supp2" id="sup2" ><a href=""><img src="/simens/public/images_icons/sup.png" /> </a></div>').insertAfter('div.icon2');
    	$('<div class="zoom_2" ><a class="various" id="zoom2" href="/simens/public/images_icons/images/echographie3_b.jpg"><img src="/simens/public/images_icons/search_16.png" /></a></div>').insertBefore('div.supp2');
        /**** On créer un input pour gérer le comptage des champs dans le script traitement médical****/
        $('<div id="cpt2"><input type="hidden" ></div>').insertAfter('div.icon2'); /** on met div.icon parceque c'est pas visible**/

        /*** Gestion de l'évènement clique sur supprimer***/
	    $('#sup2').click(function(e){ img=2;
	       e.preventDefault();
           confirmation();
           $("#confirmation").dialog('open');	
	    }); 
    }); 
/*** icons image 3 ***/
	$('#frm3 input[type="file"]').change(function(){
		$('<div class="supp3" id="sup3" ><a href=""><img src="/simens/public/images_icons/sup.png" /> </a></div>').insertAfter('div.icon3');
    	$('<div class="zoom_3" ><a class="various" id="zoom3" href="/simens/public/images_icons/images/3_fibro.jpg"><img src="/simens/public/images_icons/search_16.png" /></a></div>').insertBefore('div.supp3');
        /**** On créer un input pour gérer le comptage des champs dans le script traitement médical****/
        $('<div id="cpt3"><input type="hidden" ></div>').insertAfter('div.icon3'); /** on met div.icon parceque c'est pas visible**/

        /*** Gestion de l'évènement clique sur supprimer***/
        $('#sup3').click(function(e){ img=3;
	       e.preventDefault();
           confirmation();
           $("#confirmation").dialog('open');	
	    });  
    }); 
/*** icons image 4 ***/
	$('#frm4 input[type="file"]').change(function(){
		$('<div class="supp4" id="sup4" ><a href=""><img src="/simens/public/images_icons/sup.png" /> </a></div>').insertAfter('div.icon4');
    	$('<div class="zoom_4" ><a class="various" id="zoom4" href="/simens/public/images_icons/images/4_Scanner-a.jpg"><img src="/simens/public/images_icons/search_16.png" /></a></div>').insertBefore('div.supp4');
        /**** On créer un input pour gérer le comptage des champs dans le script traitement médical****/
        $('<div id="cpt4"><input type="hidden" ></div>').insertAfter('div.icon4'); /** on met div.icon parceque c'est pas visible**/

        /*** Gestion de l'évènement clique sur supprimer***/
        $('#sup4').click(function(e){ img=4;
	       e.preventDefault();
           confirmation();
           $("#confirmation").dialog('open');	
	    }); 
    });
/*** icons image 5 ***/
	$('#frm5 input[type="file"]').change(function(){
		$('<div class="supp5" id="sup5" ><a href=""><img src="/simens/public/images_icons/sup.png" /> </a></div>').insertAfter('div.icon5');
    	$('<div class="zoom_5" ><a class="various" id="zoom5" href="/simens/public/images_icons/images/5_IRM.jpg"><img src="/simens/public/images_icons/search_16.png" /></a></div>').insertBefore('div.supp5');
        /**** On créer un input pour gérer le comptage des champs dans le script traitement médical****/
        $('<div id="cpt5"><input type="hidden" ></div>').insertAfter('div.icon5'); /** on met div.icon parceque c'est pas visible**/

        /*** Gestion de l'évènement clique sur supprimer***/
        $('#sup5').click(function(e){ img=5;
	       e.preventDefault();
           confirmation();
           $("#confirmation").dialog('open');	
	    });
    }); 

});

}