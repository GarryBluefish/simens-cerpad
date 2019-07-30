<?php
namespace Consultation\View\Helper;

use Consultation\View\Helper\fpdf181\fpdf;
use Infirmerie\View\Helper\DateHelper;

class imprimerDemandesExamens extends fpdf
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
	
	
	
	
	
	
	
	
	protected $nomService;	
	protected $infosPatients;
	protected $infosDetailsPatients;
	
	public function getNomService()
	{
		return $this->nomService;
	}
	
	public function setNomService($nomService)
	{
		$this->nomService = $nomService;
	}
	
	public function getInfosPatients()
	{
		return $this->infosPatients;
	}
	
	public function setInfosPatients($infosPatients)
	{
		$this->infosPatients = $infosPatients;
	}
	
	public function getInfosDetailsPatients()
	{
		return $this->infosDetailsPatients;
	}
	
	public function setInfosDetailsPatients($infosDetailsPatients)
	{
		$this->infosDetailsPatients = $infosDetailsPatients;
	}
	
	
	protected function nbJours($debut, $fin) {
		//60 secondes X 60 minutes X 24 heures dans une journee
		$nbSecondes = 60*60*24;
	
		$debut_ts = strtotime($debut);
		$fin_ts = strtotime($fin);
		$diff = $fin_ts - $debut_ts;
		return ($diff / $nbSecondes);
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
		$this->Cell(100,4,"Université Gaston Berger de Saint-Louis / UFR 2S");
		$this->Ln(5.4);
		$this->Cell(100,4,"Centre de Recherche et de Prise en Charge - ");
		$this->Ln(5.4);
		$this->Cell(100,4,"Ambulatoire de la Drépanocytose (CERPAD)",0,0,'L');
		
		$this->Ln(8);
		$this->SetFont('Times','',14.3);
		$this->SetTextColor(0,128,0);
		$this->Cell(0,5,"DEMANDES D'EXAMENS",0,0,'C');
		$this->Ln(6);
		$this->SetFont('Times','',12.3);
		$this->SetFillColor(0,128,0);
		$this->Cell(0,0.3,"",0,1,'C',true);
	
		// EMPLACEMENT DU LOGO
		// EMPLACEMENT DU LOGO
		$baseUrl = $_SERVER['SCRIPT_FILENAME'];
		$tabURI  = explode('public', $baseUrl);
		$this->Image($tabURI[0].'public/images_icons/CERPAD_UGB_LOGO_M.png', 162, 14, 35, 20);
		
		// EMPLACEMENT DES INFORMATIONS SUR LE PATIENT
		// EMPLACEMENT DES INFORMATIONS SUR LE PATIENT
		$infoPatients = $this->getInfosPatients();
		$this->SetTextColor(0,0,0);
		$this->Ln(1);
		$this->SetFont('Times','',10);
		$this->Cell(40,4,$this->getInfosDetailsPatients()->numero_dossier,0,0,'L');
		
		//GESTION DE LA DATE DE NAISSANCE
		//GESTION DE LA DATE DE NAISSANCE
		
		$date_naissance = $this->getInfosPatients()->date_naissance;
		$age = $this->getInfosPatients()->age;
		
		if($age && !$date_naissance){
			$age = $age." ans ";
		}else{
		
			$aujourdhui = (new \DateTime() ) ->format('Y-m-d');
			$age_jours = $this->nbJours($date_naissance, $aujourdhui);
			$age_annees = (int)($age_jours/365);
		
			if($age_annees == 0){
					
				if($age_jours < 31){
					$age = $age_jours." jours";
				}else if($age_jours >= 31) {
		
					$nb_mois = (int)($age_jours/31);
					$nb_jours = $age_jours - ($nb_mois*31);
					if($nb_jours == 0){
						$age = $nb_mois."m";
					}else{
						$age = $nb_mois."m ".$nb_jours."j";
					}
		
				}
					
			}else{
				$age_jours = $age_jours - ($age_annees*365);
					
				if($age_jours < 31){
		
					if($age_annees == 1){
						if($age_jours == 0){
							$age = $age_annees."an";
						}else{
							$age = $age_annees."an ".$age_jours." j";
						}
					}else{
						if($age_jours == 0){
							$age = $age_annees."ans";
						}else{
							$age = $age_annees."ans ".$age_jours."j";
						}
					}
		
				}else if($age_jours >= 31) {
		
					$nb_mois = (int)($age_jours/31);
					$nb_jours = $age_jours - ($nb_mois*31);
		
					if($age_annees == 1){
						if($nb_jours == 0){
							$age = $age_annees."an ".$nb_mois."m";
						}else{
							$html .= $age_annees."an ".$nb_mois."m ".$nb_jours."j";
						}
							
					}else{
						if($nb_jours == 0){
							$age = $age_annees."ans ".$nb_mois."m";
						}else{
							$age = $age_annees."ans ".$nb_mois."m ".$nb_jours."j";
						}
					}
		
				}
					
			}
		
		}
		
		/**
		 * FIN GESTION DE LA DATE DE NAISSANCE
		 */
		
		$this->SetFont('Times','B',8.5);
		$this->Cell(50,4,"PRENOM ET NOM :",0,0,'R');
		$this->SetFont('Times','',11);
		if($infoPatients){ $this->Cell(92,4,iconv ('UTF-8' , 'windows-1252', $infoPatients->prenom).' '.iconv ('UTF-8' , 'windows-1252', $infoPatients->nom),0,0,'L'); }
		
		$this->SetFont('Times','B',8.5);
		$this->SetTextColor(0,0,0);
		$this->Ln(5);
		$this->Cell(90,4,"SEXE :",0,0,'R',false);
		$this->SetFont('Times','',11);
		if($infoPatients){ $this->Cell(92,4,iconv ('UTF-8' , 'windows-1252', $infoPatients->sexe),0,0,'L'); }
		
		$this->SetFont('Times','B',8.5);
		$this->SetTextColor(0,0,0);
		$this->Ln(5);
		$this->Cell(90,4,"AGE :",0,0,'R',false);
		$this->SetFont('Times','',11);
		if($infoPatients){ $this->Cell(92,4,(new DateHelper())->convertDate($date_naissance).' ('.$age.')',0,0,'L'); }
		
		$this->SetFont('Times','B',8.5);
		$this->SetTextColor(0,0,0);
		$this->Ln(5);
		$this->Cell(90,4,"TELEPHONE :",0,0,'R',false);
		$this->SetFont('Times','',11);
		if($infoPatients){ $this->Cell(72,4,$infoPatients->telephone,0,0,'L'); }
		
		$this->Ln(5);
		$this->SetFillColor(0,128,0);
		$this->Cell(0,0.3,"",0,1,'C',true);
		
		$this->Ln(1);
	}
	
	public function moisEnLettre($mois){
		$lesMois = array('','Janvier','Fevrier','Mars','Avril',
				'Mais','Juin','Juillet','Aout','Septembre','Octobre','Novembre','Decembre');
		return $lesMois[$mois];
	}
	
	/*
	function CorpsDocument()
	{
		$this->AddFont('zap','','zapfdingbats.php');
		$this->AddFont('timesb','','timesb.php');
		$this->AddFont('timesi','','timesi.php');
		$this->AddFont('times','','times.php');

		$medicamentLibelle = $this->getMedicamentLibelle();
		$formeMedicament = $this->getFormeMedicament();
		$nbMedicament = $this->getNbMedicament();
		$quantiteMedicament = $this->getQuantiteMedicament();
		
		$this->Ln(2);
		
		for($i = 1 ; $i < count($medicamentLibelle) ; $i++){
			
			$this->SetFillColor(249,249,249);
			$this->SetDrawColor(220,220,220);
			$this->SetFont('timesi','',11.3);
			$this->Cell(10,7,$i.')','BT',0,'C');

			//Medicament
			$this->SetFont('times','',12);
			$this->Cell(59,7, iconv ('UTF-8' , 'windows-1252', ' '.$medicamentLibelle[$i]),'BRT',0,'L',1);
			
			//Forme
			$this->SetFont('times','',12);
			$this->Cell(57,7, iconv ('UTF-8' , 'windows-1252', ' '.$formeMedicament[$i]),'BT',0,'L',1);
			
			//Nombre de médicament
			$this->SetFont('times','',12);
			$this->Cell(7,7,' '.$nbMedicament[$i],'BT',0,'L',1);
			
			//Quantité
			$this->SetFont('times','',12);
			$this->Cell(50,7, iconv ('UTF-8' , 'windows-1252',' '.$quantiteMedicament[$i]),'BT',0,'L',1);
			
			$this->Ln(8);
			
		}
		
		
	}
	*/
	
	//IMPRESSION DES INFOS STATISTIQUES
	//IMPRESSION DES INFOS STATISTIQUES
	function impressionDemandesAnalyses()
	{
		$this->AddPage();
		$this->EnTetePage();
		//$this->CorpsDocument();
	}

}

?>
