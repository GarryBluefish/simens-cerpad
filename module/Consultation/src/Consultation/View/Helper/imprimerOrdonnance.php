<?php
namespace Consultation\View\Helper;

use Consultation\View\Helper\fpdf181\fpdf;

class imprimerOrdonnance extends fpdf
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
	
	
	protected $infosPatients;
	protected $medicamentLibelle;
	protected $formeMedicament;
	protected $nbMedicament;
	protected $quantiteMedicament;
	
	
	public function getInfosPatients()
	{
		return $this->infosPatients;
	}
	
	public function setInfosPatients($infosPatients)
	{
		$this->infosPatients = $infosPatients;
	}
	
	public function getMedicamentLibelle()
	{
		return $this->medicamentLibelle;
	}
	
	public function setMedicamentLibelle($medicamentLibelle)
	{
		$this->medicamentLibelle = $medicamentLibelle;
	}
	
	public function getFormeMedicament()
	{
		return $this->formeMedicament;
	}
	
	public function setFormeMedicament($formeMedicament)
	{
		$this->formeMedicament = $formeMedicament;
	}
	
	public function getNbMedicament()
	{
		return $this->nbMedicament;
	}
	
	public function setNbMedicament($nbMedicament)
	{
		$this->nbMedicament = $nbMedicament;
	}
	
	public function getQuantiteMedicament()
	{
		return $this->quantiteMedicament;
	}
	
	public function setQuantiteMedicament($quantiteMedicament)
	{
		$this->quantiteMedicament = $quantiteMedicament;
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
		$this->Cell(0,5,"ORDONNANCE",0,0,'C');
		$this->Ln(6);
		$this->SetFont('Times','',12.3);
		$this->SetFillColor(0,128,0);
		$this->Cell(0,0.3,"",0,1,'C',true);
	
		// EMPLACEMENT DU LOGO
		// EMPLACEMENT DU LOGO
		$baseUrl = $_SERVER['SCRIPT_FILENAME'];
		$tabURI  = explode('public', $baseUrl);
		$this->Image($tabURI[0].'public/images_icons/CERPAD_UGB_LOGO_M.png', 162, 20, 35, 18);
		
		// EMPLACEMENT DES INFORMATIONS SUR LE PATIENT
		// EMPLACEMENT DES INFORMATIONS SUR LE PATIENT
		$infoPatients = $this->getInfosPatients();
		$this->SetFont('Times','B',8.5);
		$this->SetTextColor(0,0,0);
		$this->Ln(1);
		$this->Cell(90,4,"PRENOM ET NOM :",0,0,'R',false);
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
		if($infoPatients){ $this->Cell(92,4,$infoPatients->age.' ans',0,0,'L'); }
		
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
	
	//IMPRESSION DES INFOS STATISTIQUES
	//IMPRESSION DES INFOS STATISTIQUES
	function impressionOrdonnance()
	{
		$this->AddPage();
		$this->EnTetePage();
		$this->CorpsDocument();
	}

}

?>
