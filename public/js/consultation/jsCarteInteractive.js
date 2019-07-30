
var GrandTotal = 50;

function cercleMap(backgroundColor, xLon, yLat, profil, effectif){
	
	 /* 
      *  Gestion du cercle
      */ 
	 var size1 = (effectif/GrandTotal)*12;
	 var size2 = size1+3;
	 
	 var cercleMapOb = ''+
	   '{'+
    	  'type:"circle",'+
    	  'map:"sensl",'+
    	  'id:"circle'+profil+'",'+
    	  'backgroundColor:"#'+backgroundColor+'",'+
    	  'size:'+size1+','+
    	  'x:"'+xLon+'lon",'+
    	  'y:"'+yLat+'lat",'+
    	  'alpha:.6,'+
    	  'borderWidth:3,'+
    	  'borderColor:"none",'+
    	  'hoverState:{'+
    		  'size:'+size2+','+
    		  'borderColor:"#'+backgroundColor+'",'+
    		  'alpha:.1,'+
    		  'borderAlpha:1'+
    	  '},'+
    	  'tooltip:{'+
    		  'text:"'+profil+': '+effectif+'",'+
    		  'borderRadius:5,'+
    		  'borderWidth:2,'+
    		  'borderColor:"#'+backgroundColor+'",'+
    		  'fontSize:14,'+
    		  'padding:"10",'+
    		  'backgroundColor:"#FFF",'+
    		  'textAlign:"left"'+
    	  '}'+
	   '},';
  
  return cercleMapOb;
}

function getIdZoneSel(id){
	return "chartSaintlouis-graph-id0-shape-"+id+"-gshape-path";
}
function carteZonesSaintLouis(){
	
	var carteString = "<script> $(function(){" +
	     
      'zingchart.loadModules("maps, maps-sensl");'+
	  'var arrayOfColors = ["#EF9A9A #E57373", "#90CAF9 #64B5F6", "#B39DDB #9575CD", "#90CAF9 #64B5F6", "#80DEEA #4DD0E1", "#80CBC4 #4DB6AC", "#A5D6A7 #81C784","#E6EE9C #DCE775","#FFE082 #FFD54F","#FFAB91 #FF8A65", "#EF9A9A #E57373", "#F48FB1 #F06292","#B39DDB #9575CD", "#90CAF9 #64B5F6","#80DEEA #4DD0E1", "#80CBC4 #4DB6AC"];'+
	  'var colorIndex = 0;'+
	  'var listOfStates = [];'+
	  'var myConfig = {'+
			'shapes: [{'+
		        'type: "zingchart.maps",'+
		        'options: {'+
		        	'name: "sensl",'+
		        	'zooming: false,'+
		        	'panning: false,'+
		        	'scrolling: false,'+
		        	'style: {'+
		        		'controls: {'+
		        			'visible: false'+
		        		'},'+
		        		'fillType: "radial",'+
		        		'cursor: "pointer",'+
		        		'hoverState: {'+
		        			'alpha: 0.3,'+
		        			'backgroundColor: "white",'+
		        		'},'+
		        		
		        		'tooltip: {'+
		        			'alpha: 0.8,'+
		        			'backgroundColor: "white",'+
		        			'borderColor: "white",'+
		        			'borderRadius: 3,'+
		        			'fontColor: "black",'+
		        			'fontFamily: "Georgia",'+
		        			'fontSize: 12,'+
		        			'textAlpha: 1'+
		        		'}'+
		        	'}'+
		        '}'+
		      '},'+

		      /*Bango --- Bango --- Bango*/
		      cercleMap('FF0000', '-16.473356018066406', '16.05567075569004', 'SS', 25)+
		      cercleMap('ffc107', '-16.461339721679688', '16.06532037024618', 'AS', 25)+
		      
		      
		    ']};'+
			    
		    
		    'zingchart.render({'+
		  	  	'id : "chartSaintlouis",'+ 
		  	  	'data : myConfig,'+ 
		  	  	'height: 500,'+ 
		  	  	'width: "97%",'+ 
		  	'});'+
		  	
		  	
		  	'zingchart.bind(null, "shape_click", function(e) {'+
		  	  'if (e.shapeid === "SL1"){ carteBango("Bango"); $("#'+getIdZoneSel('SL1')+'").css({"fill":"#cccccc"}); }else{ $("#'+getIdZoneSel('SL1')+'").css({"fill":"#dddddd"}); }'+
		  	  'if (e.shapeid === "SL6" || e.shapeid === "SL8" || e.shapeid === "SL9"){ carteNdar("Ndar"); $("#'+getIdZoneSel('SL6')+',#'+getIdZoneSel('SL8')+',#'+getIdZoneSel('SL9')+'").css({"fill":"#cccccc"}); }else{ $("#'+getIdZoneSel('SL6')+',#'+getIdZoneSel('SL8')+',#'+getIdZoneSel('SL9')+'").css({"fill":"#dddddd"}); }'+
		  	'});'+
		  	
      
      ' });'+
      
      '</script>';
	  
      $('#chartSaintlouisScript').html(carteString);	
      
}

/* Bango --- Bango --- Bango */
function carteBango(nomZone){
	var carteString = '<script> $(function(){'+
    'zingchart.loadModules("maps, maps-senbango");'+
  	  'zingchart.render({'+
  		   'id: "chartSenegal",'+ 
  		   'data: {'+
  			   'shapes: ['+
	           '{'+
  		   		 'type: "zingchart.maps",'+
	               'options: {'+
	                  'name: "senbango",'+
	                  'zooming: false,'+
			          'panning: false,'+
			          'scrolling: false,'+
			          'style: {'+
		        		'controls: {'+
		        			'visible: false'+
		        		'},'+
		        	  '},'+	
	               '}'+
	           '}'+
	           ']'+
	       '},'+
	       'height: 400,'+ 
	       'width: "90%"'+
           '},'+
	       
	       ');'+
    '});  </script>';

	$('#chartSenegalScript').html(carteString);
	$('#colZoneSelect span').html(nomZone);
}



/* Ndar --- Ndar --- Ndar */
function carteNdar(nomZone){
	var carteString = '<script> $(function(){'+
    'zingchart.loadModules("maps, maps-senndar");'+
  	  'zingchart.render({'+
  		   'id: "chartSenegal",'+ 
  		   'data: {'+
  			   'shapes: ['+
	           '{'+
  		   		 'type: "zingchart.maps",'+
	               'options: {'+
	                  'name: "senndar",'+
	                  'zooming: false,'+
			          'panning: false,'+
			          'scrolling: false,'+
			          'style: {'+
		        		'controls: {'+
		        			'visible: false'+
		        		'},'+
		        	  '},'+	
	               '}'+
	           '}'+
	           ']'+
	       '},'+
	       'height: 400,'+ 
	       'width: "90%"'+
           '},'+
	       
	       ');'+
    '});  </script>';

	$('#chartSenegalScript').html(carteString);
	$('#colZoneSelect span').html(nomZone);
}











/* Carte du Sénégal --- Carte du sénégal */
function carteSenegal(){
	var carteString = '<script> $(function(){'+
	      'zingchart.loadModules("maps, maps-sen");'+
  	  	  'zingchart.render({'+
  	  		   'id: "chartSenegal",'+ 
  	  		   'data: {'+
  	  			   'shapes: ['+
  		           '{'+
  	  		   		 'type: "zingchart.maps",'+
  		               'options: {'+
  		                  'name: "sen"'+
  		               '}'+
  		           '}'+
  		           ']'+
  		       '},'+
  		       'height: 500,'+ 
  		       'width: "95%"'+
  	           '},'+
  		       
  		       ');'+
  	    '});  </script>';
	
	$('#chartSenegalScript').html(carteString);
}
