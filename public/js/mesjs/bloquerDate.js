function bloquerDate(){
  $(function () {
	 
    var daysToDisable = [];
    var j = 0;

    /*************************************************************************************************************/
    /*************************************************************************************************************/
    /*************************************************************************************************************/
    /********PREMIER MOIS*********/
    var lemois = mois1;
    var amois = dimanche1;
    for(var cpt=0; cpt<5 ; cpt++){
    	if(amois   < 10){daysToDisable[j++] = annee+'-'+lemois+'-0'+amois;    }else daysToDisable[j++] = annee+'-'+lemois+'-'+amois;
    	if(amois-5 < 10){daysToDisable[j++] = annee+'-'+lemois+'-0'+(amois-5);}else daysToDisable[j++] = annee+'-'+lemois+'-'+(amois-5);
    	if(amois-3 < 10){daysToDisable[j++] = annee+'-'+lemois+'-0'+(amois-3);}else daysToDisable[j++] = annee+'-'+lemois+'-'+(amois-3);
    	if(amois-1 < 10){daysToDisable[j++] = annee+'-'+lemois+'-0'+(amois-1);}else daysToDisable[j++] = annee+'-'+lemois+'-'+(amois-1);
    	amois = amois+07;
    }
    
    /********DEUXIEME MOIS*********/
    lemois = mois2;
    var amois = dimanche2;
    for(var cpt=0; cpt<5 ; cpt++){
    	if(amois   < 10){daysToDisable[j++] = annee+'-'+lemois+'-0'+amois;    }else daysToDisable[j++] = annee+'-'+lemois+'-'+amois;
    	if(amois-5 < 10){daysToDisable[j++] = annee+'-'+lemois+'-0'+(amois-5);}else daysToDisable[j++] = annee+'-'+lemois+'-'+(amois-5);
    	if(amois-3 < 10){daysToDisable[j++] = annee+'-'+lemois+'-0'+(amois-3);}else daysToDisable[j++] = annee+'-'+lemois+'-'+(amois-3);
    	if(amois-1 < 10){daysToDisable[j++] = annee+'-'+lemois+'-0'+(amois-1);}else daysToDisable[j++] = annee+'-'+lemois+'-'+(amois-1);
    	amois = amois+07;
    }
    
    /********TROISIEME MOIS*********/
    lemois = mois3;
    var amois = dimanche3;
    for(var cpt=0; cpt<5 ; cpt++){
    	if(amois   < 10){daysToDisable[j++] = annee+'-'+lemois+'-0'+amois;    }else daysToDisable[j++] = annee+'-'+lemois+'-'+amois;
    	if(amois-5 < 10){daysToDisable[j++] = annee+'-'+lemois+'-0'+(amois-5);}else daysToDisable[j++] = annee+'-'+lemois+'-'+(amois-5);
    	if(amois-3 < 10){daysToDisable[j++] = annee+'-'+lemois+'-0'+(amois-3);}else daysToDisable[j++] = annee+'-'+lemois+'-'+(amois-3);
    	if(amois-1 < 10){daysToDisable[j++] = annee+'-'+lemois+'-0'+(amois-1);}else daysToDisable[j++] = annee+'-'+lemois+'-'+(amois-1);
    	amois = amois+07;
    }
    /********QUATRIEME MOIS*********/
    lemois = mois4;
    var amois = dimanche4;
    for(var cpt=0; cpt<5 ; cpt++){
    	if(amois   < 10){daysToDisable[j++] = annee+'-'+lemois+'-0'+amois;    }else daysToDisable[j++] = annee+'-'+lemois+'-'+amois;
    	if(amois-5 < 10){daysToDisable[j++] = annee+'-'+lemois+'-0'+(amois-5);}else daysToDisable[j++] = annee+'-'+lemois+'-'+(amois-5);
    	if(amois-3 < 10){daysToDisable[j++] = annee+'-'+lemois+'-0'+(amois-3);}else daysToDisable[j++] = annee+'-'+lemois+'-'+(amois-3);
    	if(amois-1 < 10){daysToDisable[j++] = annee+'-'+lemois+'-0'+(amois-1);}else daysToDisable[j++] = annee+'-'+lemois+'-'+(amois-1);
    	amois = amois+07;
    }
    /*************************************************************************************************************/
    /*************************************************************************************************************/
    /*************************************************************************************************************/
	
    $('#date_rv').datepicker({
        beforeShowDay: disableSpecificDates,
        dateFormat: 'dd/mm/yy',
        showAnim : 'bounce',
        minDate : '1',
        maxDate : '90',
        yearRange : '2013:2050',
        
    });

    function disableSpecificDates(date) {
    	date = $.datepicker.formatDate('yy-mm-dd', date);
        return [$.inArray(date, daysToDisable) == -1];
    }
  });
}