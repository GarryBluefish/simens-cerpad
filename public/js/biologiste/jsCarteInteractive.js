
function cercleMap(backgroundColor, xLon, yLat, size1, size2, profil){
  
	 /* 
      *  Gestion du cercle
      */ 
	 var cercleMapOb = ''+
	   '{'+
    	  'type:"circle",'+
    	  'map:"sen",'+
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
    		  'text:"'+profil+'",'+
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

function carteZonesSaintLouis(){
	
	var carteString = "<script> $(function(){" +
	     
      'zingchart.loadModules("maps, maps-sen-sl");'+
	  'var arrayOfColors = ["#EF9A9A #E57373", "#90CAF9 #64B5F6", "#B39DDB #9575CD", "#90CAF9 #64B5F6", "#80DEEA #4DD0E1", "#80CBC4 #4DB6AC", "#A5D6A7 #81C784","#E6EE9C #DCE775","#FFE082 #FFD54F","#FFAB91 #FF8A65", "#EF9A9A #E57373", "#F48FB1 #F06292","#B39DDB #9575CD", "#90CAF9 #64B5F6","#80DEEA #4DD0E1", "#80CBC4 #4DB6AC"];'+
	  'var colorIndex = 0;'+
	  'var listOfStates = [];'+
	  'var myConfig = {'+
			'shapes: [{'+
		        'type: "zingchart.maps",'+
		        'options: {'+
		        	'name: "sen",'+
		        	'zooming: true,'+
		        	'panning: true,'+
		        	'scrolling: true,'+
		        	'style: {'+
		        		'controls: {'+
		        			'visible: true'+
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
		      cercleMap('FF0000', '-16.463356018066406', '16.05967075569004', 5, 8, 'SS: 3')+
		      cercleMap('ffc107', '-16.451339721679688', '16.06132037024618', 8, 11, 'AS: 23')+
		      
		    ']};'+
			    
		    
		    'zingchart.render({'+
		  	  	'id : "chartSaintlouis",'+ 
		  	  	'data : myConfig,'+ 
		  	  	'height: 600,'+ 
		  	  	'width: "100%",'+ 
		  	'});'+
		  	
      ' }); </script>';
	  
      $('#chartSaintlouisScript').html(carteString);	  	
}


