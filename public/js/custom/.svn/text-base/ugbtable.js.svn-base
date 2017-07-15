function ugbTable(source,columnTable,tableId, maFonction)
{
	var oTable = $('#'+tableId).dataTable( {
					"bJQueryUI": true,
					"sPaginationType": "full_numbers",
					"oLanguage": { 
						"sProcessing":   "Traitement en cours...",
			            "sLengthMenu":   "Afficher _MENU_ &eacute;l&eacute;ments",
				        "sZeroRecords":  "Aucun &eacute;l&eacute;ment &agrave; afficher",
				        "sInfo": "_START_ &agrave; _END_ sur _TOTAL_ &eacute;l&eacute;ments",
				        "sInfoEmpty": "0 &eacute;l&eacute;ment &agrave; afficher",
				        "sInfoFiltered": "",
				        "sInfoPostFix":  "",
				        "sSearch":       "Rechercher : ",
						"sUrl":          "",
				        "oPaginate": {
					        "sFirst":    "|<",
					        "sPrevious": "<",
					        "sNext":     ">",
					        "sLast":     ">|"
							}
				       },
						"aoColumns": columnTable,
				    "bProcessing": true,
					"bServerSide": true,
					"sAjaxSource": source,
					fnDrawCallback: maFonction
				} );
	return oTable;
}