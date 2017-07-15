	menuListEtudiant1 = [
		{'Dossier &eacute;tudiant':
			{ 
				onclick:function(menuItem,menu) 
				{
					dossierSelect(code);	
				}, 
				icon:imagesPath+'menu_icones/page_white_carte.png', 
				disabled:false 
			} 
		},
		{'Inscription p&eacute;dagogique ':
			{
				onclick:function(menuItem,menu) 
				{
					inscriptionPedagogique(iId);
				}, 
				icon:imagesPath+'menu_icones/page_white_archive.png',
				disabled:true 
			} 
		},
		{'Emploi du Temps ':
			{
				onclick:function(menuItem,menu) 
				{
					alert("Emploi du temps : "+iId);
				}, 
				icon:imagesPath+'menu_icones/page_white_planning.png',
				disabled:false 
			} 
		},
		{'Responsable': 
			{
				onclick:function(menuItem,menu) 
					{
						setResponsable(iId);
						//$(this).removeClass('row_responsable');
					}, 
					icon:imagesPath+'menu_icones/page_white_ok.png',
					disabled:false 
			}
		}
	]; 
	
	menuListEtudiant2 = [
		{'Dossier &eacute;tudiant':
			{ 
				onclick:function(menuItem,menu) 
				{
					dossierSelect(code);
				}, 
				icon:imagesPath+'menu_icones/page_white_carte.png', 
				disabled:false 
			} 
		},
		{'Inscription p&eacute;dagogique ':
			{
				onclick:function(menuItem,menu) 
				{
					inscriptionPedagogique(iId);
				}, 
				icon:imagesPath+'menu_icones/page_white_archive.png',
				disabled:true 
			} 
		},
		{'Emploi du Temps ':
			{
				onclick:function(menuItem,menu) 
				{
					alert("Emploi du temps : "+iId);
				}, 
				icon:imagesPath+'menu_icones/page_white_planning.png',
				disabled:false 
			} 
		},
		{'Responsable': 
			{
				onclick:function(menuItem,menu) 
					{
						setResponsable(iId);
					}, 
					disabled:false 
			}
		}
	]; 
	
	menuListEtudiant3 = [
		{'Dossier &eacute;tudiant':
			{ 
				onclick:function(menuItem,menu) 
				{
					dossierSelect(code);
				}, 
				icon:imagesPath+'menu_icones/page_white_carte.png', 
				disabled:false 
			} 
		},
		{'Inscription p&eacute;dagogique ':
			{
				onclick:function(menuItem,menu) 
				{
					inscriptionPedagogique(iId);
				}, 
				icon:imagesPath+'menu_icones/page_white_archive.png',
				disabled:false 
			} 
		},
		{'Emploi du Temps ':
			{
				onclick:function(menuItem,menu) 
				{
					alert("Emploi du temps : "+iId);
				}, 
				icon:imagesPath+'menu_icones/page_white_planning.png',
				disabled:false 
			} 
		},
		{'Responsable': 
			{
				onclick:function(menuItem,menu) 
					{
						setResponsable(iId);
					}, 
					disabled:true 
			}
		}
	]; 
	
	