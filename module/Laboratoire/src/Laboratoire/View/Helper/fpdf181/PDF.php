<?php
namespace Consultation\View\Helpers\fpdf181;

use Consultation\View\Helpers\fpdf181\fpdf;

class PDF extends fpdf
{

	function Footer()
	{
		// Positionnement à 1,5 cm du bas
		$this->SetY(-15);
		
		$this->SetFillColor(0,128,0);
		$this->Cell(0,0.3,"",0,1,'C',true);
		$this->SetTextColor(0,0,0);
		$this->SetFont('Times','',9.5);
		$this->Cell(81,5,'Téléphone: 33 961 00 21 ',0,0,'L',false);
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
	protected $nbInformations;
	protected $nomService;
	protected $infosPatients;
	protected $infosMedecin;
	protected $infosInfirmiers;
	protected $infosAdmission;
	protected $infosComp;
	protected $listeLits;
	protected $listeSalles;
	
	public function setTabInformations($tabInformations)
	{
		$this->tabInformations = $tabInformations;
	}
	
	public function getTabInformations()
	{
		return $this->tabInformations;
	}
	
	public function setNbInformations($nbInformations)
	{
		$this->nbInformations = $nbInformations;
	}
	
	public function getNbInformations()
	{
		return $this->nbInformations;
	}
	
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

	public function getInfosMedecin()
	{
		return $this->infosMedecin;
	}
	
	public function setInfosMedecin($infosMedecin)
	{
		$this->infosMedecin = $infosMedecin;
	}
	
	public function getInfosInfirmiers()
	{
		return $this->infosInfirmiers;
	}
	
	public function setInfosInfirmiers($infosInfirmiers)
	{
		$this->infosInfirmiers = $infosInfirmiers;
	}
	
	public function getInfosAdmission()
	{
		return $this->infosAdmission;
	}
	
	public function setInfosAdmission($infosAdmission)
	{
		$this->infosAdmission = $infosAdmission;
	}
	
	public function getInfosComp()
	{
		return $this->infosComp;
	}
	
	public function setInfosComp($infosComp)
	{
		$this->infosComp = $infosComp;
	}
	
	public function getListeLits()
	{
		return $this->listeLits;
	}
	
	public function setListeLits($listeLits)
	{
		$this->listeLits = $listeLits;
	}
	
	public function getListeSalles()
	{
		return $this->listeSalles;
	}
	
	public function setListeSalles($listeSalles)
	{
		$this->listeSalles = $listeSalles;
	}
	
	
	function EnTetePage()
	{
		$this->SetFont('Times','',10.3);
		$this->SetTextColor(0,0,0);
		$this->Cell(0,4,"République du Sénégal");
		$this->SetFont('Times','',8.5);
		$this->Cell(0,4,"Saint-Louis, le ".$this->getInfosComp()['dateConsultation'],0,0,'R');
		$this->SetFont('Times','',10.3);
		$this->Ln(5.4);
		$this->Cell(100,4,"Ministère de la santé et de l'action sociale");
		
		$this->AddFont('timesbi','','timesbi.php');
		
		//***infos de la salle
		//***infos de la salle
		$this->SetFont('timesbi','',10.3);
		$this->Cell(11,4,"Salle :",0,0,'L');
		$this->SetFont('Times','',10.3);
		
		if($this->getInfosComp()['salle']){
			$salle = $this->getInfosComp()['salle'];
			$this->Cell(18,4,$this->listeSalles[$salle],0,0,'L');
			if($salle != 7){ 
				$lit = $this->getInfosComp()['lit'];
				$this->SetFont('timesbi','',10.3);
				$this->Cell(8,4,"Lit :",0,0,'L');
				$this->SetFont('Times','',10.3);
				$this->Cell(20,4,$this->listeLits[$lit],0,0,'L');
			}
		}else {
			$this->Cell(42,4,"Couloir",0,0,'L');
		}
		
		$this->Ln(5.4);
		$this->Cell(100,4,"C.H.R de Saint-louis");
		
		//***Infos du médecin
		//***Infos du médecin
		$this->SetFont('timesbi','',10.3);
		$this->Cell(16,4,"Médecin :",0,0,'L');
		$this->SetFont('Times','',10.3);
		$this->Cell(42,4, iconv ('UTF-8' , 'windows-1252', $this->getInfosMedecin()['prenomMedecin']).' '.iconv ('UTF-8' , 'windows-1252', $this->getInfosMedecin()['nomMedecin']),0,0,'L');
		
		$this->Ln(5.4);
		$this->SetFont('timesbi','',10.3);
		$this->Cell(14,4,"Service : ",0,0,'L');
		$this->SetFont('Times','',10.3);
		$this->Cell(86,4,$this->getNomService(),0,0,'L');
		
		//***Infos de l'infirmier de service
		//***Infos de l'infirmier de service
		$infirmierTriRenseigne = 0;
		$id_infirmier_service = null;
		$id_infirmier_tri = null;
		if($this->getInfosAdmission()){
			$id_infirmier_service = $this->getInfosAdmission()->id_infirmier_service;
			$id_infirmier_tri = $this->getInfosAdmission()->id_infirmier_tri;
		}
		
		if($id_infirmier_service){
			$infosInfService = $this->getInfosInfirmiers()[$id_infirmier_service];
			if($infosInfService){
				$this->SetFont('timesbi','',10.3);
				$this->Cell(23,4,"Inf de service :",0,0,'L');
				$this->SetFont('Times','',10.3);
				$this->Cell(42,4,$infosInfService,0,0,'L');
			}else{
				if($id_infirmier_tri){
					$infosInfTri = $this->getInfosInfirmiers()[$id_infirmier_tri];
					$this->SetFont('timesbi','',10.3);
					$this->Cell(16,4,"Inf de tri :",0,0,'L');
					$this->SetFont('Times','',10.3);
					$this->Cell(42,4,$infosInfTri,0,0,'L');
					$infirmierTriRenseigne = 1;
				}
			}
		}
		
		$this->Ln(5.4);
		$this->Cell(100,4," ",0,0,'L');
		
		//***Infos de l'infirmier de tri
		//***Infos de l'infirmier de tri
		if($infirmierTriRenseigne == 0 && $id_infirmier_tri){
			$infosInfTri = $this->getInfosInfirmiers()[$id_infirmier_tri];
			$this->SetFont('timesbi','',10.3);
			$this->Cell(16,4,"Inf de tri :",0,0,'L');
			$this->SetFont('Times','',10.3);
			$this->Cell(42,4,$infosInfTri,0,0,'L');
		}
		
		
		$this->Ln(5.4);
		$this->SetFont('Times','I',10);
		$this->Cell(52,5,"n°: ".$this->getInfosPatients()['NUMERO_DOSSIER'],0,0,'L');
		
		$this->SetFont('Times','',12.3);
		$this->SetTextColor(0,128,0);
		$this->Cell(131,5,"RESUME DU PASSAGE AUX URGENCES",0,0,'L');
		$this->Ln(5.5);
		$this->SetFillColor(0,128,0);
		$this->Cell(0,0.3,"",0,1,'C',true);
	
		// EMPLACEMENT DU LOGO
		// EMPLACEMENT DU LOGO
		$baseUrl = $_SERVER['SCRIPT_FILENAME'];
		$tabURI  = explode('public', $baseUrl);
		$this->Image($tabURI[0].'public/images_icons/hrsl.png', 15, 47, 35, 18);
	
		// EMPLACEMENT DES INFORMATIONS SUR LE PATIENT
		// EMPLACEMENT DES INFORMATIONS SUR LE PATIENT
		$infoPatients = $this->getInfosPatients();
		$this->SetFont('Times','B',8.5);
		$this->SetTextColor(0,0,0);
		$this->Ln(1);
		$this->Cell(90,4,"PRENOM ET NOM :",0,0,'R',false);
		$this->SetFont('Times','',10);
		if($infoPatients){ $this->Cell(92,4,iconv ('UTF-8' , 'windows-1252', $infoPatients['PRENOM']).' '.iconv ('UTF-8' , 'windows-1252', $infoPatients['NOM']),0,0,'L'); }
	
		$this->SetFont('Times','B',8.5);
		$this->SetTextColor(0,0,0);
		$this->Ln(4.5);
		$this->Cell(90,4,"SEXE :",0,0,'R',false);
		$this->SetFont('Times','',10);
		if($infoPatients){ $this->Cell(92,4,iconv ('UTF-8' , 'windows-1252', $infoPatients['SEXE']),0,0,'L'); }
	
		$this->SetFont('Times','B',8.5);
		$this->SetTextColor(0,0,0);
		$this->Ln(4.5);
		$this->Cell(90,4,"AGE :",0,0,'R',false);
		$this->SetFont('Times','',10);
		if($infoPatients){ $this->Cell(92,4,$infoPatients['AGE'].' ans',0,0,'L'); }
	
		$this->SetFont('Times','B',8.5);
		$this->SetTextColor(0,0,0);
		$this->Ln(4.5);
		$this->Cell(90,4,"TELEPHONE :",0,0,'R',false);
		$this->SetFont('Times','',10);
		if($infoPatients){ $this->Cell(72,4,$infoPatients['TELEPHONE'],0,0,'L'); }
		
		//Pour le niveau d'urgence
		//Pour le niveau d'urgence
		$this->SetFont('Times','',8);
		$this->Cell(17,4,"N.U :",0,0,'R');
		$this->SetFont('Times','B',10.3);
		$this->Cell(3,4,$this->getInfosComp()['niveau'],0,0,'R');
	
		$this->Ln(5);
		$this->SetFillColor(0,128,0);
		$this->Cell(0,0.3,"",0,1,'C',true);
	}
	
	function CorpsDocument()
	{
		$this->AddFont('zap','','zapfdingbats.php');
		
		for($i = 0 ; $i < $this->getNbInformations() ; $i++){
			if($this->getTabInformations()[$i]['texte']){
				$this->Ln(2.3);
				if($this->getTabInformations()[$i]['type'] == 1){
					$this->SetFont('zap','',13);
					$this->Cell(4,5,"o",0,0,'L'); //le 'b' est interessant
					
					$this->SetFont('Times','',12);
					$this->WriteHTML('<B><U>'.$this->getTabInformations()[$i]['titre'].' :</U></B>');
					$this->Ln(6);
					$this->MultiCell(0, 6.8, $this->getTabInformations()[$i]['texte']);
				}else 
					if($this->getTabInformations()[$i]['type'] == 2){
						$this->SetFont('zap','',13);
						$this->Cell(4,5.5,"p",0,0,'L');
						
						$this->SetFont('Times','',12);
						$this->WriteHTML('<B><U>'.$this->getTabInformations()[$i]['titre'].' :</U></B>  ');
						$this->MultiCell(0, 5, $this->getTabInformations()[$i]['texte']);
					}
			}
		}
	}
	
	//IMPRESSION DES RPU HOSPITALISATION
	//IMPRESSION DES RPU HOSPITALISATION
	function ImpressionRpuHospitalisation()
	{
		$this->AddPage();
		$this->EnTetePage();
		$this->CorpsDocument();
	}
	
	//IMPRESSION DES RPU TRAUMATOLOGIE
	//IMPRESSION DES RPU TRAUMATOLOGIE
	function ImpressionRpuTraumatologie()
	{
		$this->AddPage();
		$this->EnTetePage();
		$this->CorpsDocument();
	}

	//IMPRESSION DES RPU SORTIE
	//IMPRESSION DES RPU SORTIE
	function ImpressionRpuSortie()
	{
		$this->AddPage();
		$this->EnTetePage();
		$this->CorpsDocument();
	}

}

?>
