function action(){
$('#submit').click(function(){
	
  var login    = $('#username').val();
  var password = $('#password').val();
  
  $.ajax({
    type: 'POST',
    url: '/simens/public/login/process/connexion-service' ,
    data: {'username':login, 'password':password},
    success: function(data) {    
    	     var result = jQuery.parseJSON(data);  

      	    if(result){
          	     if(result == 1){
                    alert("vous n etes dans aucun service");
              	 }else{
    	               vart='/simens/public/consultation/recherche/service/'+result;
                       $(location).attr("href",vart);
              	 }
      	     }else{
          	     alert("Login ou mot de pass incorrect");
          	     }
    },
    error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
    dataType: "html"
  });
});
}