	menuDeliberation1 = [
					{'Fiche Jury':
						{ 
							onclick:function(menuItem,menu) 
							{
								OuvrirPopupJury(iIdJury);
							}, 
							icon:imagesPath+'menu_icones/page_white_fiche.png', 
							disabled:false 
						} 
					},	
					{'D&eacute;lib&eacute;rer Formation':
					{ 
						onclick:function(menuItem,menu) 
						{
							delibererFormation(iIdJury);	
						}, 
						icon:imagesPath+'menu_icones/page_white_carte.png', 
						disabled:false 
					} 
					},	
	]; 
	
	menuDeliberation2 = [
					{'Fiche Jury':
					{ 
						onclick:function(menuItem,menu) 
						{
							OuvrirPopupJury(iIdJury);
						}, 
						icon:imagesPath+'menu_icones/page_white_fiche.png', 
						disabled:false 
					} 
					},	
	 ]; 
	
	