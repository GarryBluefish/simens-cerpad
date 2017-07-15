function ugbForm(lien,contenantId,popupId,formName)
	{
		$.ajax({
			type :"get",
			url : lien,
			success : function (reponse) {
				$("#"+contenantId).html(reponse);
			},
			async:   false
		});
		$("#"+popupId).dialog({
			autoOpen: false,
			height: "auto",
			width: "auto",
			modal: true,
			buttons: 
			{
				"Fermer": function() 
				{
					$(this).dialog("close");
					$(this).remove();
				},
				"Valider": function() 
				{
					var options = { 
									//target:        '#output2',   // target element(s) to be updated with server response 
									//beforeSubmit:  validate,  // pre-submit callback 
									success:       showResponse,  // post-submit callback 
									async:   false,
									// other available options: 
									//url:       url         // override for form's 'action' attribute 
									//type:      type        // 'get' or 'post', override for form's 'method' attribute 
									//dataType:  null        // 'xml', 'script', or 'json' (expected server response type) 
									// clearForm: true        // clear all form fields after successful submit 
									// resetForm: true        // reset the form after successful submit 

									// $.ajax options can be used here too, for example: 
									timeout:   3000 
								}; 
								$("#"+popupId).dialog("close");
								$('#'+formName).ajaxSubmit(options);
								return false; 
				}
			}
		});
		$("#"+popupId).dialog( "open" );
	}
	function showResponse(responseText, statusText, xhr, $form, contenantId, popupId, formName)  
	{ 
		
		$("#"+popupId).remove();
		$("#"+contenantId).html(responseText);
		if(document.getElementById("#"+formName))
		{
			$("#"+popupId).dialog({
				autoOpen: false,
				height: "auto",
				width: "auto",
				modal: true,
				buttons: 
				{
					"Fermer": function() 
					{
						$(this).dialog("close");
						$(this).remove();
					},
					"Valider": function() 
					{
						var options = { 
										success:       showResponse,
										async:   false
									}; 
									$("#"+popupId).dialog("close");
									$('#'+formName).ajaxSubmit(options);
									return false; 
					}
				}
			});
		}
		else
		{
			$("#"+popupId).dialog({
				autoOpen: false,
				height: "auto",
				width: "auto",
				modal: true,
				buttons: 
				{
					"OK": function() 
					{
						$(this).dialog("close");
						$(this).remove();
					}
				}
			});
		}
		$("#"+popupId).dialog("open");
	}