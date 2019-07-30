<?php
namespace Laboratoire\View\Helper;

use Laboratoire\View\Helper\fpdf181\fpdf;

class infosStatistiquePdf extends fpdf
{

	function Footer()
	{
		// Positionnement à 1,5 cm du bas
		$this->SetY(-15);
		
		$this->SetFillColor(0,128,0);
		$this->Cell(0,0.3,"",0,1,'C',true);
		$this->SetTextColor(0,0,0);
		$this->SetFont('Times','',9.5);
		$this->Cell(125,5,'Téléphone: 77 680 69 69 ',0,0,'L',0);
		$this->SetTextColor(128);
		$this->SetFont('Times','I',9);
		$this->Cell(20,8,'Page '.$this->PageNo(),0,0,'C',false);
		$this->SetTextColor(0,0,0);
		$this->SetFont('Times','',9.5);
		$this->Cell(125,5,'SIMENS+: www.simens.sn',0,0,'R',0);
	}
	
	protected $B = 0;
	protected $I = 0;
	protected $U = 0;
	protected $HREF = '';
	
	function WriteHTML($html)
	{
		// Parseur HTML
		$html = str_replace("\n",' ',$html);
		$a = preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE);
		foreach($a as $i=>$e)
		{
			if($i%2==0)
			{
				// Texte
				if($this->HREF)
					$this->PutLink($this->HREF,$e);
				else
					$this->Write(5,$e);
			}
			else
			{
				// Balise
				if($e[0]=='/')
					$this->CloseTag(strtoupper(substr($e,1)));
				else
				{
					// Extraction des attributs
					$a2 = explode(' ',$e);
					$tag = strtoupper(array_shift($a2));
					$attr = array();
					foreach($a2 as $v)
					{
						if(preg_match('/([^=]*)=["\']?([^"\']*)/',$v,$a3))
							$attr[strtoupper($a3[1])] = $a3[2];
					}
					$this->OpenTag($tag,$attr);
				}
			}
		}
	}
	
	function OpenTag($tag, $attr)
	{
		// Balise ouvrante
		if($tag=='B' || $tag=='I' || $tag=='U')
			$this->SetStyle($tag,true);
		if($tag=='A')
			$this->HREF = $attr['HREF'];
		if($tag=='BR')
			$this->Ln(5);
	}
	
	function CloseTag($tag)
	{
		// Balise fermante
		if($tag=='B' || $tag=='I' || $tag=='U')
			$this->SetStyle($tag,false);
		if($tag=='A')
			$this->HREF = '';
	}
	
	function SetStyle($tag, $enable)
	{
		// Modifie le style et sélectionne la police correspondante
		$this->$tag += ($enable ? 1 : -1);
		$style = '';
		foreach(array('B', 'I', 'U') as $s)
		{
			if($this->$s>0)
				$style .= $s;
		}
		$this->SetFont('',$style);
	}
	
	function PutLink($URL, $txt)
	{
		// Place un hyperlien
		$this->SetTextColor(0,0,255);
		$this->SetStyle('U',true);
		$this->Write(5,$txt,$URL);
		$this->SetStyle('U',false);
		$this->SetTextColor(0);
	}
	
	
	
	
	
	
	
	
	protected $tabInformations ;
	protected $nomService;
	protected $infosComp;
	protected $PeriodePrelevement;
	protected $typeInfos;
	
	public function setTabInformations($tabInformations)
	{
		$this->tabInformations = $tabInformations;
	}
	
	public function getTabInformations()
	{
		return $this->tabInformations;
	}
	
	public function getNomService()
	{
		return $this->nomService;
	}
	
	public function setNomService($nomService)
	{
		$this->nomService = $nomService;
	}
	
	public function getPeriodePrelevement()
	{
		return $this->PeriodePrelevement;
	}
	
	public function setPeriodePrelevement($PeriodePrelevement)
	{
		$this->PeriodePrelevement = $PeriodePrelevement;
	}

	public function getInfosComp()
	{
		return $this->infosComp;
	}
	
	public function setInfosComp($infosComp)
	{
		$this->infosComp = $infosComp;
	}
	
	public function getTypeInfos()
	{
		return $this->typeInfos;
	}
	
	public function setTypeInfos($typeInfos)
	{
		$this->typeInfos = $typeInfos;
	}
	
	function EnTetePage()
	{
		$this->SetFont('Times','',10.3);
		$this->SetTextColor(0,0,0);
		$this->Cell(0,4,"République du Sénégal");
		$this->SetFont('Times','',8.5);
		$this->Cell(0,4,"",0,0,'R'); 
		$this->SetFont('Times','',10.3);
		$this->Ln(5.4);
		$this->Cell(100,4,"Ministère de la santé et de l'action sociale");
		
		$this->AddFont('timesbi','','timesbi.php');
		$this->Ln(5.4);
		$this->Cell(100,4,"C.H.R de Saint-louis");
		$this->Ln(5.4);
		$this->SetFont('timesbi','',10.3);
		$this->Cell(14,4,"Service : ",0,0,'L');
		$this->SetFont('Times','',10.3);
		$this->Cell(86,4,$this->getNomService(),0,0,'L');
		
		$this->Ln(8);
		$this->SetFont('Times','',14.3);
		$this->SetTextColor(0,128,0);
		$this->Cell(0,5,"DEPISTAGES MENSUELS PAR PROFIL",0,0,'C');
		$this->Ln(6);
		$this->SetFont('Times','',12.3);
		$this->SetFillColor(0,128,0);
		$this->Cell(0,0.3,"",0,1,'C',true);
		$this->Ln(4);
	
		// EMPLACEMENT DU LOGO
		// EMPLACEMENT DU LOGO
		$baseUrl = $_SERVER['SCRIPT_FILENAME'];
		$tabURI  = explode('public', $baseUrl);
		$this->Image($tabURI[0].'public/images_icons/CERPAD_UGB_LOGO_M.png', 249, 13, 35, 22);
		
	}
	
	public function moisEnLettre($mois){
		$lesMois = array('','Janvier','Février','Mars','Avril',
				'Mai','Juin','Juillet','Aout','Septembre','Octobre','Novembre','Décembre');
		return $lesMois[$mois];
	}
	
	function item_percentage($item, $total){
	
		if($total){
				
			$valeur = ($item * 100 / $total);
			if(fmod($valeur, 1) !== 0.00){$valeur = number_format($valeur, 2, ',', ' ');}
	
			return $valeur;
				
			//return number_format(($item * 100 / $total), 1, ',', ' ');
				
		}else{
			return 0;
		}
	
	}
	
	function CorpsDocument()
	{
		$date_debut = "";
		$date_fin = "";
		 
		if($this->getPeriodePrelevement()){
			$dateConvert = new DateHelper();
			$date_debut = $dateConvert->convertDate($this->getPeriodePrelevement()[0]);
			$date_fin   = $dateConvert->convertDate($this->getPeriodePrelevement()[1]);
			
			$this->SetFillColor(220,220,220);
			$this->SetDrawColor(204,204,204);
			$this->SetTextColor(0,0,0);
			$this->AddFont('zap','','zapfdingbats.php');
			
			$this->AddFont('timesb','','timesb.php');
			$this->AddFont('timesbi','','timesbi.php');
			$this->AddFont('timesi','','timesi.php');
			$this->AddFont('times','','times.php');
			
			//largeur de la ligne = 270
			
			$this->SetFillColor(255,255,255);
			
			$this->SetFont('timesbi','',12);
			if($this->getTypeInfos() == 1){
				$this->Cell(98,7,'RV','',0,'L',1);
			}elseif ($this->getTypeInfos() == 2){
				$this->Cell(98,7,'RX','',0,'L',1);
			}else{
				$this->Cell(98,7,'TR','',0,'L',1);
			}
			
			$this->SetFont('zap','',13);
			$this->SetFillColor(241,241,241);
			$this->SetLineWidth(0.5);
			$this->Cell(5,8,'B','BLT',0,'L',1);
			
			$this->SetFont('times','',12.5);
			$this->Cell(70,8,"Periode du ".$date_debut." au ".$date_fin,'BRT',0,'L',1);
			
			$this->SetFont('timesi','',10);
			$this->SetFillColor(255,255,255);
			$this->Cell(97,7,'imprimé le : '.$this->getInfosComp()['dateDuJour'],'L',0,'R',1);
			
			$this->Ln(12);
			$this->SetLineWidth(0);
		}

		$tabInformations = $this->getTabInformations(); 
		if($tabInformations){
			
			$tabAnnees = $tabInformations[0];
			$tabDonneesAnnuelle = $tabInformations[1];
			$tabMois = $tabInformations[2];
			$tabProfils = $tabInformations[3];
			$tabProfilsAnneesMois = $tabInformations[4];
			$grandTotalNbPatient = $tabInformations[5];
			
			/*
			 * EN TETE DU TABLEAU
			 */

			$this->Ln(0.5);
			$this->SetFillColor(235,235,235);
			$this->SetDrawColor(205,193,197);
			$this->SetTextColor(0,0,0);
			$this->AddFont('zap','','zapfdingbats.php');
			$this->SetFont('zap','',13);
				
			$this->AddFont('timesb','','timesb.php');
			$this->AddFont('timesi','','timesi.php');
			$this->AddFont('times','','times.php');

			$this->Cell(270,0.2,'', 'BLRT',1,'R',1);
			$this->Ln(0.1);
			$this->SetFont('times','',13.5);
			$this->Cell(5,7, '', 'BLT',0,'L',1);
			$this->Cell(35,7, 'Période', 'BRT',0,'L',1);
			
			$this->SetFont('timesb','',11.5);
			
			$totalCol = array();
			
			if(count($tabProfils) == 0){ $largeur = 118; }else{ $largeur = 195/count($tabProfils); }
			
			for($iProf=0 ; $iProf<count($tabProfils) ; $iProf++){
			
				$this->Cell($largeur,7, $tabProfils[$iProf], 'BLRT',0,'C',1);
				$totalCol[$iProf] = 0;

			}
			
			$this->SetFont('times','',13.5);
			$this->Cell(35,7,'Total ', 'BLRT',0,'C',1);
			$this->Ln(7);
			$this->Cell(270,0.2,'', 'BLRT',1,'R',1);
			$this->Ln(0.5);
			/*
			 * FIN DE L'EN TETE DU TABLEAU
			 */
			
			$nombrePatientDepistes = 0;
			$kligne = 0;
			
			for($i=0 ; $i<count($tabAnnees) ; $i++){
			
				$annee = $tabAnnees[$i];
				$tabDonneesAnnee = array_count_values($tabDonneesAnnuelle[$annee]); 
				sort($tabMois[$annee]); //ordonne la liste des mois
				$tabIndexDonnees = $tabMois[$annee];

				
				for($ij=0 ; $ij<count($tabIndexDonnees) ; $ij++){
					$mois = $tabIndexDonnees[$ij];
					$listeProfils = array_count_values($tabProfilsAnneesMois[$annee][$mois]);
					
					if(($kligne%2)==0){
						$this->SetFillColor(249,249,249);
						$this->SetDrawColor(220,220,220);
						$this->SetFont('zap','',10);
						$this->Cell(5,7,'+','BLT',0,'C');
						$this->SetFont('times','',12);
					
						$this->Cell(35,7,$this->moisEnLettre($mois).' '.$annee,'BRT',0,'L',1);
							
						for($iProf=0 ; $iProf<count($tabProfils) ;$iProf++){
								
							$leProfil = $tabProfils[$iProf];
							if(array_key_exists($leProfil, $listeProfils)){
					
								//Pourcentage pour chaque valeur
								$pourValeur = $this->item_percentage($listeProfils[$leProfil], $tabDonneesAnnee[$mois]);
								$this->SetFont('times','',13);
								$this->Cell($largeur/2,7, $listeProfils[$leProfil], 'BLT',0,'R',1);
								$this->SetFont('timesi','',8);
								$this->Cell($largeur/2,7, '('.$pourValeur.'%)', 'BRT',0,'L',1);
								
								$totalCol[$iProf] += $listeProfils[$leProfil];
					
							}else{
								$this->SetFont('times','',12);
								$this->Cell($largeur/2,7, '0', 'BLT',0,'R',1);
								$this->Cell($largeur/2,7, '', 'BRT',0,'L',1);
							}
								
						}
						
						$this->SetFont('timesb','',12);
						$this->Cell(20,7,$tabDonneesAnnee[$mois],'BLT',0,'R',1);
						
						//Pourcentage pour chaque valeur
						$pourValeurTotalColLigne = $this->item_percentage($tabDonneesAnnee[$mois], $grandTotalNbPatient);
						$this->SetFont('timesi','',8);
						$this->Cell(15,7,'('.$pourValeurTotalColLigne.'%)','BRT',0,'L',1);
						
						$this->Ln(7);
							
					}else{
						$this->SetFillColor(255,255,255);
						$this->SetDrawColor(220,220,220);
						$this->SetFont('zap','',10);
						$this->Cell(5,7,'+','BLT',0,'C');
						$this->SetFont('times','',12);
							
						$this->Cell(35,7,$this->moisEnLettre($mois).' '.$annee,'BRT',0,'L',1);
							
						for($iProf=0 ; $iProf<count($tabProfils) ;$iProf++){

							
							$leProfil = $tabProfils[$iProf];
							if(array_key_exists($leProfil, $listeProfils)){
									
								//Pourcentage pour chaque valeur
								$pourValeur = $this->item_percentage($listeProfils[$leProfil], $tabDonneesAnnee[$mois]);
								$this->SetFont('times','',13);
								$this->Cell($largeur/2,7, $listeProfils[$leProfil], 'BLT',0,'R',1);
								$this->SetFont('timesi','',8);
								$this->Cell($largeur/2,7, '('.$pourValeur.'%)', 'BRT',0,'L',1);
							
								$totalCol[$iProf] += $listeProfils[$leProfil];
									
							}else{
								$this->SetFont('times','',12);
								$this->Cell($largeur/2,7, '0', 'BLT',0,'R',1);
								$this->Cell($largeur/2,7, '', 'BRT',0,'L',1);
							}
						}
					
						$this->SetFont('timesb','',12);
						$this->Cell(20,7,$tabDonneesAnnee[$mois],'BLT',0,'R',1);
						
						//Pourcentage pour chaque valeur
						$pourValeurTotalColLigne = $this->item_percentage($tabDonneesAnnee[$mois], $grandTotalNbPatient);
						$this->SetFont('timesi','',8);
						$this->Cell(15,7,'('.$pourValeurTotalColLigne.'%)','BRT',0,'L',1);
						
						$this->Ln(7);
					}
						
					$nombrePatientDepistes += $tabDonneesAnnee[$mois];
					$kligne++;
				}
			
			
			}
			$this->Ln(0.5);
			$this->Cell(270,0.2,'', 'BLRT',1,'R',1);
			$this->Ln(0.1);
			$this->SetFillColor(235,235,235);
			$this->SetDrawColor(205,193,197);
			$this->SetFont('timesi','',11.3);
			$this->Cell(5,7,' ','BLT',0,'C', 1);
			$this->SetFont('times','',13.5);
			
			$grandTotal = 0;
			$this->Cell(35,7,'Total ','BRT',0,'R',1);
			$this->SetFont('timesb','',12);
			
			for($i=0 ; $i<count($totalCol) ; $i++){
				$this->SetFont('timesb','',12);
				$this->Cell($largeur/2,7, $totalCol[$i], 'BLT',0,'R',1);
				
				//Pourcentage pour chaque valeur
				$pourValeurTotalLigneCol = $this->item_percentage($totalCol[$i], $grandTotalNbPatient);
				$this->SetFont('timesi','',8);
				$this->Cell($largeur/2,7, '('.$pourValeurTotalLigneCol.'%)', 'BRT',0,'L',1);
				
				$grandTotal += $totalCol[$i];
			}
			
			$this->SetFont('timesb','',12);
			$this->Cell(20,7,$grandTotal,'BLT',0,'R',1);
			$this->Cell(15,7,' ','BRT',1,'L',1);
			
			$this->Cell(270,0.2,'', 'BLRT',1,'R',1);
			$this->Ln(0.5);

			$this->Ln(7);
			
		}
		else{
			
		}
		
		
		
	}
	
	//IMPRESSION DES INFOS STATISTIQUES
	//IMPRESSION DES INFOS STATISTIQUES
	function ImpressionInfosStatistiques()
	{
		$this->AddPage();
		$this->EnTetePage();
		$this->CorpsDocument();
	}

}

?>
