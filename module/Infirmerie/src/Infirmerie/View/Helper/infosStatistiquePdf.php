<?php
namespace Infirmerie\View\Helper;

use Infirmerie\View\Helper\fpdf181\fpdf;

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
		$this->Cell(81,5,'Téléphone: 33 000 00 00 ',0,0,'L',false);
		$this->SetTextColor(128);
		$this->SetFont('Times','I',9);
		$this->Cell(20,8,'Page '.$this->PageNo(),0,0,'C',false);
		$this->SetTextColor(0,0,0);
		$this->SetFont('Times','',9.5);
		$this->Cell(81,5,'SIMENS+: www.simens.sn',0,0,'R',false);
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
	
	function EnTetePage()
	{
		$this->SetFont('Times','',10.3);
		$this->SetTextColor(0,0,0);
		$this->Cell(0,4,"République du Sénégal");
		$this->SetFont('Times','',8.5);
		$this->Cell(0,4,"Saint-Louis, le ".$this->getInfosComp()['dateDuJour'],0,0,'R');
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
		$this->Cell(0,5,"NOMBRE DE DEPISTAGES MENSUELS",0,0,'C');
		$this->Ln(6);
		$this->SetFont('Times','',12.3);
		$this->SetFillColor(0,128,0);
		$this->Cell(0,0.3,"",0,1,'C',true);
		$this->Ln(4);
	
		// EMPLACEMENT DU LOGO
		// EMPLACEMENT DU LOGO
		$baseUrl = $_SERVER['SCRIPT_FILENAME'];
		$tabURI  = explode('public', $baseUrl);
		$this->Image($tabURI[0].'public/images_icons/CERPAD_UGB_LOGO_M.png', 162, 20, 35, 18);
		
	}
	
	public function moisEnLettre($mois){
		$lesMois = array('','Janvier','Fevrier','Mars','Avril',
				'Mais','Juin','Juillet','Aout','Septembre','Octobre','Novembre','Decembre');
		return $lesMois[$mois];
	}
	
	function CorpsDocument()
	{
		$date_debut = "";
		$date_fin = "";
		 
		if($this->getPeriodePrelevement()){
			$dateConvert = new DateHelper();
			$date_debut = $dateConvert->convertDate($this->getPeriodePrelevement()[0]);
			$date_fin   = $dateConvert->convertDate($this->getPeriodePrelevement()[1]);
			
			//$this->Ln(5.4);
			$this->SetFillColor(220,220,220);
			$this->SetDrawColor(205,193,197);
			$this->SetTextColor(0,0,0);
			$this->AddFont('zap','','zapfdingbats.php');
			$this->SetFont('zap','',13);
			
			$this->SetFillColor(255,255,255);
			$this->Cell(55,7,'','',0,'L',1);
			
			$this->SetFillColor(220,220,220);
			$this->SetLineWidth(1);
			$this->Cell(5,8,'B','BLT',0,'L',1);
			
			$this->AddFont('timesb','','timesb.php');
			$this->AddFont('timesi','','timesi.php');
			$this->AddFont('times','','times.php');
			
			$this->SetFont('times','',12.5);
			$this->Cell(70,8,"Periode du ".$date_debut." au ".$date_fin,'BRT',0,'L',1);
			
			$this->SetFillColor(255,255,255);
			$this->Cell(53,7,'','L',0,'L',1);
			
			$this->Ln(12);
			$this->SetLineWidth(0);
		}

		$tabInformations = $this->getTabInformations(); 
		if($tabInformations){
			
			/*
			 * EN TETE DU TABLEAU
			 */

			$this->Ln(0.5);
			$this->SetFillColor(220,220,220);
			$this->SetDrawColor(205,193,197);
			$this->SetTextColor(0,0,0);
			$this->AddFont('zap','','zapfdingbats.php');
			$this->SetFont('zap','',13);
				
			$this->AddFont('timesb','','timesb.php');
			$this->AddFont('timesi','','timesi.php');
			$this->AddFont('times','','times.php');
				
			$this->SetFont('times','',13.5);
			$this->Cell(10,7, '', 'BLT',0,'L',1);
			$this->Cell(59,7, 'Périodes', 'BRT',0,'L',1);
			$this->Cell(57,7,' '. 'Nombre de dépistés', 'BLRT',0,'L',1);
			$this->Cell(57,7,' '. 'Nombre de naissances', 'BLRT',0,'L',1);
			
			$this->Ln(8);

			/*
			 * FIN DE L'EN TETE DU TABLEAU
			 */
			
			
			$tabAnnees = $tabInformations[0];
			$tabDonneesAnnuelle = $tabInformations[1];
			$tabMois = $tabInformations[2];
			
			$indice = 1;
			$kligne = 0;
			$nombrePatientDepistes = 0;
			
			for($i=0 ; $i<count($tabAnnees) ; $i++){
			
				$annee = $tabAnnees[$i];
				$tabDonneesAnnee = array_count_values($tabDonneesAnnuelle[$annee]);
				$tabIndexDonnees = $tabMois[$annee];
				for($ij=0 ; $ij<count($tabIndexDonnees) ; $ij++){
					$mois = $tabIndexDonnees[$ij];
					$dernierJourMois = cal_days_in_month(CAL_GREGORIAN, $mois, $annee);
				
					if($kligne==0){
						
						$this->SetFillColor(249,249,249);
						$this->SetDrawColor(220,220,220);
						$this->SetFont('timesi','',11.3);
						$this->Cell(10,7,$indice++.'.','BT',0,'C');
						$this->SetFont('times','',11.3);
						
						if($date_debut){
							if($date_fin && (($ij+1)==count($tabIndexDonnees))){
								$this->Cell(10,7,'du '.substr($date_debut, 0, 2) , 'BT',0,'L',1);
								$this->Cell(49,7,iconv ('UTF-8' , 'windows-1252', ' au '.substr($date_fin, 0, 2).' '. $this->moisEnLettre($mois).' '.$annee),'BRT',0,'L',1);
							}else{
								$this->Cell(10,7,'du '.substr($date_debut, 0, 2) , 'BT',0,'L',1);
								$this->Cell(49,7,iconv ('UTF-8' , 'windows-1252', 'au '.$dernierJourMois.' '. $this->moisEnLettre($mois).' '.$annee),'BRT',0,'L',1);
							}
						}else{
							$this->Cell(59,7,iconv ('UTF-8' , 'windows-1252', 'du 1er au '.$dernierJourMois.' '. $this->moisEnLettre($mois).' '.$annee),'BRT',0,'L',1);
						}

						$this->SetFont('times','',13.3);
						$this->Cell(57,7,$tabDonneesAnnee[$mois].' ','BLRT',0,'R',1);
						$this->Cell(57,7,'_  ','BLT',0,'R',1);
						$this->Ln(7);
						
					}else{
					
						if(($kligne%2)==0){
							$this->SetFillColor(249,249,249);
							$this->SetDrawColor(220,220,220);
							$this->SetFont('timesi','',11.3);
							$this->Cell(10,7,$indice++.'.','BT',0,'C');
							$this->SetFont('times','',11.3);
							
							if($date_fin && (($ij+1)==count($tabIndexDonnees))){
								if(($i+1)==count($tabAnnees)){
									if($mois != (int)substr($date_fin, 3, 2)){
										$this->Cell(59,7,iconv ('UTF-8' , 'windows-1252', 'du 1er au '.$dernierJourMois.' '. $this->moisEnLettre($mois).' '.$annee),'BRT',0,'L',1);
									}else{
										$this->Cell(59,7,iconv ('UTF-8' , 'windows-1252', 'du 1er au '.substr($date_fin, 0, 2).' '. $this->moisEnLettre($mois).' '.$annee),'BRT',0,'L',1);
									}
								}else{
									$this->Cell(59,7,iconv ('UTF-8' , 'windows-1252', 'du 1er au '.$dernierJourMois.' '. $this->moisEnLettre($mois).' '.$annee),'BRT',0,'L',1);
								}
							}else{
								$this->Cell(59,7,iconv ('UTF-8' , 'windows-1252', 'du 1er au '.$dernierJourMois.' '. $this->moisEnLettre($mois).' '.$annee),'BRT',0,'L',1);
							}
							
							$this->SetFont('times','',13.3);
							$this->Cell(57,7,$tabDonneesAnnee[$mois].' ','BLRT',0,'R',1);
							$this->Cell(57,7,'_  ','BLT',0,'R',1);
							$this->Ln(7);
						}else{
							$this->SetFillColor(255,255,255);
							$this->SetDrawColor(220,220,220);
							$this->SetFont('timesi','',11.3);
							$this->Cell(10,7,$indice++.'.','BT',0,'C');
							$this->SetFont('times','',11.3);
							
							if($date_fin && (($ij+1)==count($tabIndexDonnees))){
								if(($i+1)==count($tabAnnees)){

									if($mois != (int)substr($date_fin, 3, 2)){
										$this->Cell(59,7,iconv ('UTF-8' , 'windows-1252', 'du 1er au '.$dernierJourMois.' '. $this->moisEnLettre($mois).' '.$annee),'BRT',0,'L',1);
									}else{
										$this->Cell(59,7,iconv ('UTF-8' , 'windows-1252', 'du 1er au '.substr($date_fin, 0, 2).' '. $this->moisEnLettre($mois).' '.$annee),'BRT',0,'L',1);
									}
									
								}else{
									$this->Cell(59,7,iconv ('UTF-8' , 'windows-1252', 'du 1er au '.$dernierJourMois.' '. $this->moisEnLettre($mois).' '.$annee),'BRT',0,'L',1);
								}
							}else{
								$this->Cell(59,7,iconv ('UTF-8' , 'windows-1252', 'du 1er au '.$dernierJourMois.' '. $this->moisEnLettre($mois).' '.$annee),'BRT',0,'L',1);
							}
							
							$this->SetFont('times','',13.3);
							$this->Cell(57,7,$tabDonneesAnnee[$mois].' ','BLRT',0,'R',1);
							$this->Cell(57,7,'_  ','BLT',0,'R',1);
							$this->Ln(7);
						}
					}
					
					
					
					$nombrePatientDepistes += $tabDonneesAnnee[$mois];
					$kligne++;
				}
				
			}
			
			
			$this->Ln(3);
			$this->SetFillColor(220,220,220);
			$this->SetDrawColor(205,193,197);
			$this->SetFont('timesi','',11.3);
			$this->Cell(10,7,'','',0,'C');
			$this->SetFont('times','',11.3);
			$this->Cell(59,7,'','',0,'L');
			$this->SetFont('timesi','',13.3);
			$this->Cell(17,7,' Total:','BLT',0,'L',1);
			$this->SetFont('times','',14);
			$this->Cell(40,7,$nombrePatientDepistes.' ','BRT',0,'R',1);
			$this->Cell(57,7,'_  ','BLT',0,'R',1);
			
			
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
