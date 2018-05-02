<?php
namespace Consultation\View\Helper;

use Consultation\View\Helper\fpdf181\fpdf;
use Secretariat\View\Helper\DateHelper;

class ImprimerExamensRadioDemandes extends fpdf
{

	function Footer()
	{
		// Positionnement � 1,5 cm du bas
		$this->SetY(-15);
		
		$this->SetFillColor(0,128,0);
		$this->Cell(0,0.3,"",0,1,'C',true);
		$this->SetTextColor(0,0,0);
		$this->SetFont('Times','',9.5);
		$this->Cell(81,5,'T�l�phone: 33 000 00 00 ',0,0,'L',false);
		$this->SetTextColor(128);
		$this->SetFont('Times','I',9);
		$this->Cell(20,8,'' /*'Page '.$this->PageNo()*/,0,0,'C',false);
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
		// Modifie le style et s�lectionne la police correspondante
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
	protected $motifExamenDem;
	protected $typeExamen;
	protected $idExamen;
	protected $libelleExamen;
	
	
	public function getInfosPatients()
	{
		return $this->infosPatients;
	}
	
	public function setInfosPatients($infosPatients)
	{
		$this->infosPatients = $infosPatients;
	}
	
	public function getMotifExamenDem()
	{
		return $this->motifExamenDem;
	}
	
	public function setMotifExamenDem($motifExamenDem)
	{
		$this->motifExamenDem = $motifExamenDem;
	}
	
	public function getTypeExamen()
	{
		return $this->typeExamen;
	}
	
	public function setTypeExamen($typeExamen)
	{
		$this->typeExamen = $typeExamen;
	}
	
	public function getIdExamen()
	{
		return $this->idExamen;
	}
	
    public function setIdExamen($idExamen)
	{
		$this->idExamen = $idExamen;
	}
	
	public function getLibelleExamen()
	{
		return $this->libelleExamen;
	}
	
	public function setLibelleExamen($libelleExamen)
	{
		$this->libelleExamen = $libelleExamen;
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
		$this->Cell(0,4,"R�publique du S�n�gal");
		$this->SetFont('Times','',8.5);
		$this->Cell(0,4,"",0,0,'R');
		$this->SetFont('Times','',10.3);
		$this->Ln(5.4);
		$this->Cell(100,4,"Minist�re de la sant� et de l'action sociale");
		
		$this->AddFont('timesbi','','timesbi.php');
		$this->Ln(5.4);
		$this->Cell(100,4,"C.E.R.P.A.D de Saint-louis");
		$this->Ln(5.4);
		$this->SetFont('timesbi','',10.3);
		$this->Cell(14,4,"Service : ",0,0,'L');
		$this->SetFont('Times','',10.3);
		$this->Cell(86,4,$this->getNomService(),0,0,'L');
		
		$this->Ln(6);
		$this->SetFont('Times','',13);
		$this->SetTextColor(0,128,0);
		$this->Cell(0,-3,"DEMANDE D'EXAMEN",0,0,'C');
		$this->Ln(1);
		$this->SetFont('Times','',12.3);
		$this->SetFillColor(0,128,0);
		$this->Cell(0,0.3,"",0,1,'C',true);
	
		// EMPLACEMENT DU LOGO
		// EMPLACEMENT DU LOGO
		$baseUrl = $_SERVER['SCRIPT_FILENAME'];
		$tabURI  = explode('public', $baseUrl);
		$this->Image($tabURI[0].'public/images_icons/CERPAD_UGB_LOGO_M.png', 162, 12, 35, 22);
		
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
		
		//GESTION DES AGES
		//GESTION DES AGES
		$age = $infoPatients->age;
		$aujourdhui = (new \DateTime() ) ->format('Y-m-d');
		if(!$age){
		
			$age_jours = $this->nbJours($infoPatients->date_naissance, $aujourdhui);
			if($age_jours < 31) {
				$age = $age_jours." jours";
			}
			else
			if($age_jours >= 31) {
				$nb_mois = (int)($age_jours/30);
				$nb_jours = $age_jours - ($nb_mois*30);
				$age = $nb_mois."m ".$nb_jours."j";
			}
		
		}else{
		
			$age = $age." ans";
		
		}
		
		$convertDate = new DateHelper();
		
		$this->SetFont('Times','B',8.5);
		$this->SetTextColor(0,0,0);
		$this->Ln(5);
		$this->Cell(90,4,"DATE DE NAISSANCE (age) :",0,0,'R',false);
		$this->SetFont('Times','',11);
		if($infoPatients){ $this->Cell(92,4, $convertDate->convertDate($infoPatients->date_naissance).' ('.$age.')',0,0,'L'); }
		
		$this->SetFont('Times','B',8.5);
		$this->SetTextColor(0,0,0);
		$this->Ln(5);
		$this->Cell(90,4,"TELEPHONE :",0,0,'R',false);
		$this->SetFont('Times','',11);
		if($infoPatients){ $this->Cell(72,4,$infoPatients->telephone,0,0,'L'); }
		
		$this->Ln(5);
		$this->SetFillColor(0,128,0);
		$this->Cell(0,0.3,"",0,1,'C',true);
		
		$this->Ln(2);
		$this->AddFont('timesi','','timesi.php');
		$this->SetFont('timesi','',8);
		$this->Cell(183,1,"Imprim� le : ".$convertDate->convertDate($aujourdhui),0,1,'R');
	}
	
	public function moisEnLettre($mois){
		$lesMois = array('','Janvier','Fevrier','Mars','Avril',
				'Mais','Juin','Juillet','Aout','Septembre','Octobre','Novembre','Decembre');
		return $lesMois[$mois];
	}
	
	function CorpsDocumentExamensRadio($libelleExamen, $motifExamenDem)
	{
		$this->AddFont('zap','','zapfdingbats.php');
		$this->AddFont('timesb','','timesb.php');
		$this->AddFont('timesi','','timesi.php');
		$this->AddFont('times','','times.php');

		$this->Ln(2);
		
		//AFFICHAGE DE L'EXAMEN DEMANDE
		//AFFICHAGE DE L'EXAMEN DEMANDE
		$this->SetFillColor(249,249,249);
		$this->SetDrawColor(220,220,220);
		$this->SetFont('zap','',11.3);
		$this->Cell(3,7,'o','BT',0,'C',1);
		
		$this->SetFont('times','',12);
		$this->Cell(180,7,'Examen demand� : ','BT',1,'L',1);
				
		$this->SetFont('zap','',11.3);
		$this->Cell(10,7,'','',0,'C');
		$this->Cell(3,7,'+','',0,'C');
		$this->SetFont('timesi','',10);
		$this->Cell(170,7, iconv ('UTF-8' , 'windows-1252', ' '.$libelleExamen),'',1,'L',0);
			
		
		//AFFICHAGE DU MOTIF DE L'EXAMEN
		//AFFICHAGE DU MOTIF DE L'EXAMEN
		$this->Ln(2);
		$this->SetFillColor(249,249,249);
		$this->SetDrawColor(220,220,220);
		$this->SetFont('zap','',11.3);
		$this->Cell(3,7,'o','BT',0,'C',1);
		
		$this->SetFont('times','',12);
		$this->Cell(180,7,"Motif de l'examen : ",'BT',1,'L',1);
		
		$this->SetFont('zap','',11.3);
		$this->Cell(3,8,'','',0,'C');
		$this->SetFont('timesi','',11);
		$this->MultiCell(180,8, iconv ('UTF-8' , 'windows-1252', ' '.$motifExamenDem),'','L',0);

	}
	
	//IMPRESSION DES INFOS STATISTIQUES
	//IMPRESSION DES INFOS STATISTIQUES
	function impressionExamensRadioDemandes()
	{
		$motifExamenDem = explode(',',$this->getMotifExamenDem());
		$typeExamen =  explode(',',$this->getTypeExamen());
		$idExamen =  explode(',',$this->getIdExamen());
		$libelleExamen =  explode(',',$this->getLibelleExamen());
		
		for($i=1 ; $i < count($libelleExamen) ; $i++){
			
			if($typeExamen[1] == 6){
				$this->AddPage();
				$this->EnTetePage();
				$this->CorpsDocumentExamensRadio($libelleExamen[$i], $motifExamenDem[$i]);
			}		
		}
		
	}

}

?>
