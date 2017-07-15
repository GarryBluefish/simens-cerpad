var tampon;  

function initialisation(){
	var asInitVals = new Array();
	var oTable = $('#patient').dataTable
	( {
					//"bJQueryUI": true,
					"sPaginationType": "full_numbers",
					"aaSorting": "", //pour trier la liste affichée
					"aLengthMenu": [5,10,15],
					"oLanguage": { 
						//"sProcessing":   "Traitement en cours...",
						//"sLengthMenu":   "Afficher _MENU_ &eacute;l&eacute;ments",
						"sZeroRecords":  "Aucun &eacute;l&eacute;ment &agrave; afficher",
						"sInfo": "_START_ &agrave; _END_ sur _TOTAL_ &eacute;l&eacute;ments",
						"sInfoEmpty": "0 &eacute;l&eacute;ment &agrave; afficher",
						"sInfoFiltered": "",
						"sInfoPostFix":  "",
						"sSearch": "",
						"sUrl": "",
						"sWidth": "",
						"oPaginate": {
							"sFirst":    "|<",
							"sPrevious": "<",
							"sNext":     ">",
							"sLast":     ">|"
							}
					   },

	} );
    tampon = oTable;

    $('#calendrier').keyup(function()  //Assure la recherche dès la saisie d'une valeur
	{					
    	oTable.fnFilter( $('#calendrier').val() );
	});
    
//    //le filtre du calendrier
//    $('#calendrier').mouseover(function()  //Assure la recherche 
//	{					
//    	oTable.fnFilter( $('#calendrier').val() );
//	});
	
    $('#rechercher').click(function(){
    	oTable.fnFilter( $('#calendrier').val() );
	});
    
	//$("#calendrier").keyup( function () {  //Assure la recherche automatique par élément 
		/* Filter on the column (the index) of this element */
		//oTable.fnFilter( this.value );
	//} );
	
	//le filtre du select
	$('#filter_statut').change(function() 
	{					
		oTable.fnFilter( this.value, 6 );
	});
	$("tfoot input").keyup( function () {
		/* Filter on the column (the index) of this element */
		oTable.fnFilter( this.value, $("tfoot input").index(this) );
	} );
	
	/*
	 * Support functions to provide a little bit of 'user friendlyness' to the textboxes in 
	 * the footer
	 */
	$("tfoot input").each( function (i) {
		asInitVals[i] = this.value;
	} );
	
	$("tfoot input ").focus( function () {
		if ( this.className == "search_init" )
		{
			this.className = "";
			this.value = "";
		}
	});
	
	$("tfoot input ").blur( function (i) {
		if ( this.value == "" )
		{
			this.className = "search_init";
			this.value = asInitVals[$("tfoot input").index(this)];
		}
	} );
}

function calendrier(){

    	$('#calendrier').datepicker(
    			$.datepicker.regional['fr'] = {
    					closeText: 'Fermer',
    					changeYear: true,
    					yearRange: 'c-80:c',
    					prevText: '&#x3c;PrÃ©c',
    					nextText: 'Suiv&#x3e;',
    					currentText: 'Courant',
    					monthNames: ['Janvier','Fevrier','Mars','Avril','Mai','Juin',
    					'Juillet','Aout','Septembre','Octobre','Novembre','Decembre'],
    					monthNamesShort: ['Jan','Fev','Mar','Avr','Mai','Jun',
    					'Jul','Aout','Sep','Oct','Nov','Dec'],
    					dayNames: ['Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'],
    					dayNamesShort: ['Dim','Lun','Mar','Mer','Jeu','Ven','Sam'],
    					dayNamesMin: ['Di','Lu','Ma','Me','Je','Ve','Sa'],
    					weekHeader: 'Sm',
    					dateFormat: 'dd/mm/yy',
    					firstDay: 1,
    					isRTL: false,
    					showMonthAfterYear: false,
    					yearRange: '1910:2035',
    					showAnim : 'bounce',
    					changeMonth: true,
    					changeYear: true,
    					yearSuffix: ''}			
    	);

}

