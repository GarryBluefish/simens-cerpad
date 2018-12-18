<?php
namespace Laboratoire\View\Helper;

use ZendPdf;
use ZendPdf\Page;
use ZendPdf\Font;

class ResultatsAnalysesDemandeesPdf
{
	protected $_page;
	protected $_yPosition;
	protected $_leftMargin;
	protected $_pageWidth;
	protected $_pageHeight;
	protected $_normalFont;
	protected $_boldFont;
	protected $_newTime;
	protected $_newTimeGras;
	protected $_year;
	protected $_headTitle;
	protected $_introText;
	protected $_graphData;
	protected $_patient;
	protected $_date;
	protected $_note;
	protected $_idPersonne;
	protected $_Service;
	protected $_DonneesPatient;
	protected $_analysesDemandees;
	protected $_resultatsAnalysesDemandees;
	protected $_depistage;
	protected $_anteriorite;
	
	
	public function __construct()
	{
		$this->_page = new Page(Page::SIZE_A4 );
		
 		$this->_yPosition = 750;
 		$this->_leftMargin = 50;
 		$this->_pageHeight = $this->_page->getHeight();
 		$this->_pageWidth = $this->_page->getWidth();
 		/**
 		 * Pas encore utilisé
 		 */
 		$this->_normalFont = Font::fontWithName( ZendPdf\Font::FONT_HELVETICA);
 		$this->_boldFont = Font::fontWithName( ZendPdf\Font::FONT_HELVETICA_BOLD);
 		/**
 		 ***************** 
 		 */
 		$this->_newTime = Font::fontWithName(ZendPdf\Font::FONT_TIMES_ROMAN);
 		$this->_newTimeGras = Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD);
	}
	
	public function getPage(){
		return $this->_page;
	}
	
	public function addNote(){
		$this->_page->saveGS();
		
		$this->setEnTete();
		$this->getNoteInformations();
		$this->getPiedPage();
		
		$this->_page->restoreGS();
	}
	
	public function setEnTete(){
	    
	    $baseUrl = $_SERVER['SCRIPT_FILENAME'];
	    $tabURI  = explode('public', $baseUrl);
	    
 		$imageHeader = ZendPdf\Image::imageWithPath($tabURI[0].'public/images_icons/CERPAD_UGB_LOGO_M.png');
		$this->_page->drawImage($imageHeader, 440, //-x
				$this->_pageHeight - 110, //-y
				535, //+x
				787); //+y
		
		$this->_page->setFont($this->_newTime, 10);
		$this->_page->drawText('République du Sénégal',
				$this->_leftMargin,
				$this->_pageHeight - 50);
		$this->_page->setFont($this->_newTime, 10);
		$this->_page->drawText('Université Gaston Berger / UFR 2S',
				$this->_leftMargin,
				$this->_pageHeight - 65);
		$this->_page->setFont($this->_newTime, 10);
		$this->_page->drawText('Centre de Recherche et de Prise en Charge - ',
				$this->_leftMargin,
				$this->_pageHeight - 80);
		$this->_page->setFont($this->_newTime, 10);
		$this->_page->drawText('Ambulatoire de la Drépanocytose (CERPAD)',
		    $this->_leftMargin,
		    $this->_pageHeight - 95);
		
		$font = ZendPdf\Font::fontWithName(ZendPdf\Font::FONT_TIMES_ROMAN);
		$this->_page->setFont($font, 8);
		$today = new \DateTime ();
		$dateNow = $today->format ( 'd/m/Y' );
		$this->_page->drawText('Saint-Louis le, ' . $dateNow,
				450,
				$this->_pageHeight - 50);
	}
	
	public function setDonneesPatient($donneesPatient){
		$this->_DonneesPatient = $donneesPatient;
	}
	
	public function setService($service){
		$this->_Service = $service;
	}
	
	public function setAnalysesDemandees($analysesDemandees){
	    $this->_analysesDemandees = $analysesDemandees;
	}
	
	public function setResultatsAnalysesDemandees($resultatsAnalysesDemandees){
	    $this->_resultatsAnalysesDemandees = $resultatsAnalysesDemandees;
	}
	
	public function setDepistage($depistage){
	    $this->_depistage = $depistage;
	}
	
	public function setAnterioriteNFS($anteriorite){
	    $this->_anteriorite = $anteriorite;
	}
	
	public function getNewItalique(){
		$font = ZendPdf\Font::fontWithName(ZendPdf\Font::FONT_HELVETICA_OBLIQUE);
		$this->_page->setFont($font, 12);
	}
	
	public function getNewTime(){
		$font = ZendPdf\Font::fontWithName(ZendPdf\Font::FONT_TIMES_ROMAN);
		$this->_page->setFont($font, 10);
	}
	
	public function getNewTime2(){
		$font = ZendPdf\Font::fontWithName(ZendPdf\Font::FONT_TIMES_ROMAN);
		$this->_page->setFont($font, 12);
	}
	
	public function getNewTimeBold(){
		$font = ZendPdf\Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD);
		$this->_page->setFont($font, 16);
	}
	
	public function getNewTimeBold2(){
		$font = ZendPdf\Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD);
		$this->_page->setFont($font, 14);
	}
	
	public function getStyle(){
		$font = ZendPdf\Font::fontWithName(ZendPdf\Font::FONT_TIMES_ITALIC);
		$this->_page->setFont($font, 12);
	}
	
	public function getStyle2(){
		$font = ZendPdf\Font::fontWithName(ZendPdf\Font::FONT_HELVETICA_ITALIC);
		$this->_page->setFont($font, 12);
	}
	
	public function getStyle3(){
		$font = ZendPdf\Font::fontWithName(ZendPdf\Font::FONT_TIMES);
		$this->_page->setFont($font, 12);
	}
	
	public function getStyle4(){
		$font = ZendPdf\Font::fontWithName(ZendPdf\Font::FONT_TIMES_ITALIC);
		$this->_page->setFont($font, 12);
	}
	
	public function getStyle5(){
		$font = ZendPdf\Font::fontWithName(ZendPdf\Font::FONT_HELVETICA);
		$this->_page->setFont($font, 12);
	}
	
	public function getStyle6(){
		$font = ZendPdf\Font::fontWithName(ZendPdf\Font::FONT_TIMES_ROMAN);
		$this->_page->setFont($font, 12);
	}
	
	protected function nbAnnees($debut, $fin) {
		//60 secondes X 60 minutes X 24 heures dans une journee
		$nbSecondes = 60*60*24*365;
	
		$debut_ts = strtotime($debut);
		$fin_ts = strtotime($fin);
		$diff = $fin_ts - $debut_ts;
		return round($diff / $nbSecondes);
	}
	
	function reductionText1($Text){
		$chaine = $Text;
		$nb = 115;
		if(strlen($Text) > $nb){
			$chaine = substr($Text, 0, $nb);
			$nb = strrpos($chaine, ' ');
			$chaine = substr($chaine, 0, $nb);
		}
		
		return array('chaine' => $chaine, 'position' => $nb);
	}
	
	function reductionText2($Text, $init){
		$chaine = $Text;
		$nb = 110;

		$chaine = substr($Text, $init, $nb);
		if(strlen($chaine) > $nb){
		    $nb = strrpos($chaine, ' ');
		    $chaine = substr($chaine, 0, $nb);
		}
		
		//var_dump($chaine); exit();
		
		return array('chaine' => $chaine, 'position' => $nb);
	}
	
	protected function scinderText($Text){
		$tab = array();
		$init = 0;
		$tab[1] = $this->reductionText1($Text)['chaine'];
		$init = $this->reductionText1($Text)['position'];
		
		
		$tab[2] = $this->reductionText2($Text, $init)['chaine'];
		$init += $this->reductionText2($Text, $init)['position'];
		
		$tab[3] = $this->reductionText2($Text, $init)['chaine'];
		$init += $this->reductionText2($Text, $init)['position'];
		
		return $tab;
	}
	
	public function prixMill($prix) {
		$str="";
		$long =strlen($prix)-1;
	
		for($i = $long ; $i>=0; $i--)
		{
		$j=$long -$i;
		if( ($j%3 == 0) && $j!=0)
		{ $str= " ".$str;   }
		$p= $prix[$i];
	
		$str = $p.$str;
		}
	
		if(!$str){ $str = $prix; }
	
		return($str);
	}
	
	protected function nbJours($debut, $fin) {
		//60 secondes X 60 minutes X 24 heures dans une journee
		$nbSecondes = 60*60*24;
	
		$debut_ts = strtotime($debut);
		$fin_ts = strtotime($fin);
		$diff = $fin_ts - $debut_ts;
		return ($diff / $nbSecondes);
	}
		
	protected  function getNoteInformations(){
		$Control = new DateHelper();
		
		$this->_yPosition -= 35;
		$this->_page->setFont($this->_newTime, 15);
		$this->_page->setFillColor(new ZendPdf\Color\Html('green'));
		$this->_page->drawText('RESULTATS D\'ANALYSES',
				$this->_leftMargin+150,
				$this->_yPosition);
		$this->_yPosition -= 5;
		$this->_page->setlineColor(new ZendPdf\Color\Html('green'));
		$this->_page->drawLine($this->_leftMargin,
				$this->_yPosition,
				$this->_pageWidth -
				$this->_leftMargin,
				$this->_yPosition);
		$noteLineHeight = 30;
		$this->_yPosition -= 15;
		
		$this->_page->setFillColor(new ZendPdf\Color\Html('black')); 
		
		$this->_page->setLineColor(new ZendPdf\Color\Html('#999999')); 

		//-----------------------------------------------
		$value = $this->_DonneesPatient;
		$typepatient = "E";
		if($this->_depistage->current()){ 
		    if($this->_depistage->current()['typepatient'] == 1){ $typepatient = "I"; } 
		}
    
		//-----------------------------------------------
		$this->_page->setFont($this->_newTime, 10);
		$this->_page->drawText(iconv ('UTF-8' , 'ISO-8859-1' , 'NÂ°: '.$value->idpersonne.'-'.$typepatient),
	    $this->_leftMargin,
	    $this->_yPosition);
		
		$this->_page->setFont($this->_newTimeGras, 9);
		$this->_page->drawText('PRENOM & NOM :',
				$this->_leftMargin+123,
				$this->_yPosition);
		$this->_page->setFont($this->_newTime, 10);
		$this->_page->drawText(iconv ('UTF-8' ,'ISO-8859-1' , $value->prenom.'  '.$value->nom),
				$this->_leftMargin+210,
				$this->_yPosition);
		//-----------------------------------------------
		$this->_yPosition -= 15;
		//----------------------------------------------
		$this->_page->setFont($this->_newTimeGras, 9);
		$this->_page->drawText('SEXE :',
				$this->_leftMargin+173,
				$this->_yPosition);
		$this->_page->setFont($this->_newTime, 10);
		$this->_page->drawText(iconv ('UTF-8' ,'ISO-8859-1' , $value->sexe),
				$this->_leftMargin+210,
				$this->_yPosition);
		//-----------------------------------------------
   		$this->_yPosition -= 15;
		//----- -----------------------------------------
			
		$date_naissance = $value->date_naissance;
		if($date_naissance){ $date_naissance = $Control->convertDate($date_naissance); } else {$date_naissance = null; }
			
		if($date_naissance){
			$this->_page->setFont($this->_newTimeGras, 9);
			$this->_page->drawText('DATE DE NAISSANCE :',
					$this->_leftMargin+102,
					$this->_yPosition);
			$this->_page->setFont($this->_newTime, 10);
			
			$age = $value->age;
			//GESTION DES AGES 
			//GESTION DES AGES
			if(!$age){
				
				$aujourdhui = (new \DateTime() ) ->format('Y-m-d');
				$age_jours = $this->nbJours($value->date_naissance, $aujourdhui);
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
			
			
			$this->_page->drawText($date_naissance."  (".$age.")",
					$this->_leftMargin+210,
					$this->_yPosition);
			
		}else {
			$this->_page->setFont($this->_newTimeGras, 9);
			$this->_page->drawText('AGE :',
					$this->_leftMargin+176,
					$this->_yPosition);
			$this->_page->setFont($this->_newTime, 10);
			
			$this->_page->drawText($value->age." ans",
					$this->_leftMargin+210,
					$this->_yPosition);
		}

		//-----------------------------------------------
		$this->_yPosition -= 15;
		//----------------------------------------------
		$this->_page->setFont($this->_newTimeGras, 9);
		$this->_page->drawText('ADRESSE :',
				$this->_leftMargin+155,
				$this->_yPosition);
		$this->_page->setFont($this->_newTime, 10);
    	$this->_page->drawText(iconv ('UTF-8' ,'ISO-8859-1' , $value->adresse),
				$this->_leftMargin+210,
				$this->_yPosition);
		//-----------------------------------------------
			
		//-----------------------------------------------------------------------
		//-----------------------------------------------------------------------
		//-----------------------------------------------------------------------
	    
		$analyses = array();
		$idAnalyses = array();
		$typesAnalyses = array();
		$infosAnalyseDemande = array();
			
		$listeAnalysesDemandees = $this->_analysesDemandees;
		
		for($i = 0 ; $i < count($listeAnalysesDemandees) ; $i++){
		    $idanalyse = $listeAnalysesDemandees[$i]['idanalyse'];
		    
		    $analyses[$idanalyse]            = $listeAnalysesDemandees[$i]['Designation'];
		    $idAnalyses[$idanalyse]          = $idanalyse;
		    $typesAnalyses[$idanalyse]       = $listeAnalysesDemandees[$i]['Libelle'];
		    $infosAnalyseDemande[$idanalyse] = $listeAnalysesDemandees[$i];
		}
			
		
		//-----------------------------------------------------------------------
		//-----------------------------------------------------------------------
		//-------- HEMATOLOGIE --------- HEMATOLOGIE ---------- HEMATOLOGIE------
		//-------- HEMATOLOGIE --------- HEMATOLOGIE ---------- HEMATOLOGIE------
		//-------- HEMATOLOGIE --------- HEMATOLOGIE ---------- HEMATOLOGIE------
		//-----------------------------------------------------------------------
		//-----------------------------------------------------------------------
		 		
		$entreeHemato = 0;
		if(in_array('HEMATOLOGIE', $typesAnalyses)){
			$entreeHemato = 1;
			for ($i = 1 ; $i <= count($analyses) ; $i++){
			    if($i == 1 && $idAnalyses[$i] == 1){
			        $this->_page = $this->getNfs($noteLineHeight, $analyses[$i], $infosAnalyseDemande);
			    }
			}
			
 		}
 		//var_dump($analyses); exit();
 			
 		//-----------------------------------------------------------------
 		//-----------------------------------------------------------------
 		//-----------------------------------------------------------------
 		//-----------------------------------------------------------------
 		//-----------------------------------------------------------------
 		//-----------------------------------------------------------------
 		
 		
 		
 		//-----------------------------------------------------------------------
 		//-----------------------------------------------------------------------
 		//-------- HEMATOLOGIE --------- HEMATOLOGIE ---------- HEMATOLOGIE------
 		//-------- HEMATOLOGIE --------- HEMATOLOGIE ---------- HEMATOLOGIE------
 		//-------- HEMATOLOGIE --------- HEMATOLOGIE ---------- HEMATOLOGIE------
 		//-----------------------------------------------------------------------
 		//-----------------------------------------------------------------------
 		
 		if(in_array('DEPISTAGE', $typesAnalyses)){ 
 			if($entreeHemato == 0){
 				$this->_page = $this->typage_hemoglobine($noteLineHeight, $analyses[68], $infosAnalyseDemande, $value);
 			}
 		}
 		
 		//-----------------------------------------------------------------
 		//-----------------------------------------------------------------
 		//-----------------------------------------------------------------
 		//-----------------------------------------------------------------
 		//-----------------------------------------------------------------
 		//-----------------------------------------------------------------
 		
 		
 		//-----------------------------------------------------------------
 		//-----------------------------------------------------------------
 		//-------- BIOCHIMIE --------- BIOCHIMIE ---------- BIOCHIMIE------
 		//-------- BIOCHIMIE --------- BIOCHIMIE ---------- BIOCHIMIE------
 		//-------- BIOCHIMIE --------- BIOCHIMIE ---------- BIOCHIMIE------
 		//-----------------------------------------------------------------
 		//-----------------------------------------------------------------
// 		if(in_array('BIOCHIMIE', $typesAnalyses)){ }
 		
 		//-----------------------------------------------------------------
 		//-----------------------------------------------------------------
 		//-----------------------------------------------------------------
 		//-----------------------------------------------------------------
 		//-----------------------------------------------------------------
 		//-----------------------------------------------------------------
 		
 		
 		//-----------------------------------------------------------------------------
 		//-----------------------------------------------------------------------------
 		//-------- PARASITOLOGIE --------- PARASITOLOGIE ---------- PARASITOLOGIE------
 		//-------- PARASITOLOGIE --------- PARASITOLOGIE ---------- PARASITOLOGIE------
 		//-------- PARASITOLOGIE --------- PARASITOLOGIE ---------- PARASITOLOGIE------
 		//-----------------------------------------------------------------------------
 		//-----------------------------------------------------------------------------
// 		if(in_array('PARASITOLOGIE', $typesAnalyses)){}
 		
 		//---------------------------------------------------------------------------
 		//---------------------------------------------------------------------------
 		//---------------------------------------------------------------------------
 		//---------------------------------------------------------------------------
 		//---------------------------------------------------------------------------
 		//---------------------------------------------------------------------------
 		
 		
 		
 		//-----------------------------------------------------------------------------
 		//-----------------------------------------------------------------------------
 		//-------- BACTERIOLOGIE --------- BACTERIOLOGIE ---------- BACTERIOLOGIE------
 		//-------- BACTERIOLOGIE --------- BACTERIOLOGIE ---------- BACTERIOLOGIE------
 		//-------- BACTERIOLOGIE --------- BACTERIOLOGIE ---------- BACTERIOLOGIE------
 		//-----------------------------------------------------------------------------
 		//-----------------------------------------------------------------------------
// 		if(in_array('BACTERIOLOGIE', $typesAnalyses)){}
 		
 		//---------------------------------------------------------------------------
 		//---------------------------------------------------------------------------
 		//---------------------------------------------------------------------------
 		//---------------------------------------------------------------------------
 		//---------------------------------------------------------------------------
 		//---------------------------------------------------------------------------
 		
		$this->_yPosition -= $noteLineHeight;
		
		
	} 
	
	public function getPiedPage(){
		
		$this->_page->setFont($this->_newTimeGras, 9);
		$this->_page->drawText('Cachet et signature',
				$this->_leftMargin+400,
				$this->_pageWidth - ( 140 + 360));
		
		$this->_page->setlineColor(new ZendPdf\Color\Html('green'));
		$this->_page->setLineWidth(1.5);
		$this->_page->drawLine($this->_leftMargin,
				50,
				$this->_pageWidth -
				$this->_leftMargin,
				50);
		
		$this->_page->setFont($this->_newTime, 10);
		$this->_page->drawText('Téléphone: 77 680 69 69',
				$this->_leftMargin,
				$this->_pageWidth - ( 140 + 420));
		
		$this->_page->setFont($this->_newTime, 10);
		$this->_page->drawText('SIMENS+: ',
				$this->_leftMargin + 355,
				$this->_pageWidth - ( 140 + 420));
		$this->_page->setFont($this->_newTimeGras, 11);
		$this->_page->drawText('www.simens.sn',
				$this->_leftMargin + 405,
				$this->_pageWidth - ( 140 + 420));
	}
	
	//---   GESTION DES ANALYSES   ---   GESTION DES ANALYSES    
	//---   GESTION DES ANALYSES   ---   GESTION DES ANALYSES
	//---   GESTION DES ANALYSES   ---   GESTION DES ANALYSES
	
	public function getNfs($noteLineHeight, $Designation, $infosAnalyseDemande){
	    
	    $this->_page->setlineColor(new ZendPdf\Color\Html('green'));
	    $this->_page->drawLine($this->_leftMargin,
	        $this->_yPosition-10,
	        $this->_pageWidth -
	        $this->_leftMargin,
	        $this->_yPosition-10);
	     
	    //-----------------------------------------------------------------------
	    //-----------------------------------------------------------------------
	    //-----------------------------------------------------------------------
	    
	    $this->_yPosition -= $noteLineHeight;

	    $control = new DateHelper();
	    
	    $resultats = $this->_resultatsAnalysesDemandees;
	    
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 8);
	    $this->_page->drawText('Enregistré le: ',
	        $this->_leftMargin+5,
	        $this->_yPosition+10);
	    
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 8.5);
	    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $control->convertDateTime( $infosAnalyseDemande[1]['DateEnregistrementResultat'] )),
	        $this->_leftMargin+50,
	        $this->_yPosition+10);
	    
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 8);
	    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', 'par: '),
	        $this->_leftMargin+135,
	        $this->_yPosition+10);
	    
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 9);
	    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $infosAnalyseDemande[1]['Prenom'] .' '.$infosAnalyseDemande[1]['Nom']),
	        $this->_leftMargin+150,
	        $this->_yPosition+10);
	    
	    $this->_yPosition -= $noteLineHeight-20; //Allez à la ligne
	    
	    $this->_page->setLineColor(new ZendPdf\Color\Html('#999999'));
	    $this->_page->setLineWidth(1);
	    $this->_page->drawLine($this->_leftMargin+199,
	        $this->_yPosition -3,
	        $this->_pageWidth -
	        $this->_leftMargin-220,
	        $this->_yPosition -3);
	    
	    $this->_page->setLineColor(new ZendPdf\Color\Html('#ffffff'));
	    $this->_page->setLineWidth(0.5);
	     
	    $this->_page->drawLine($this->_leftMargin,
	        $this->_yPosition -2,
	        $this->_pageWidth -
	        $this->_leftMargin,
	        $this->_yPosition -2);
	    
	    $this->getNewTime();
	    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $Designation),
	        $this->_leftMargin+200,
	        $this->_yPosition);
	    
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_HELVETICA_OBLIQUE), 9);
	    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', 'HÃ©matologie'),
	        $this->_leftMargin+5,
	        $this->_yPosition);
	    
	    $this->_yPosition -= $noteLineHeight; //aller a la ligne suivante
	    
	    $this->_page->setLineColor(new ZendPdf\Color\Html('#f2f2f2'));
	    $this->_page->setLineWidth(16);
	    $this->_page->drawLine($this->_leftMargin,
	        $this->_yPosition +4,
	        $this->_pageWidth -
	        $this->_leftMargin,
	        $this->_yPosition +4);

	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10);
	    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "Type de matÃ©riel: " ),
	        $this->_leftMargin+5,
	        $this->_yPosition);
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_ITALIC), 11);
	    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $resultats[1]['type_materiel'] ),
	        $this->_leftMargin+85,
	        $this->_yPosition);
	    
	    	
	    $this->_yPosition -= $noteLineHeight-5; //aller a la ligne suivante
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 9);
	    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "LIBELLE "),
	        $this->_leftMargin+15,
	        $this->_yPosition);
	    
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 8);
	    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "VALEUR ACTUELLE "),
	        $this->_leftMargin+115,
	        $this->_yPosition);
	    
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 8);
	    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "VALEUR DE REFERENCE "),
	    		$this->_leftMargin+255,
	    		$this->_yPosition);
	    
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 8);
	    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "ANTERIORITE "),
	        $this->_leftMargin+390,
	        $this->_yPosition);
	    	
	    //------- GESTION DES ANTERIORITES -------- GESTION DES ANTERIORITES ------- 
	    //------- GESTION DES ANTERIORITES -------- GESTION DES ANTERIORITES ------- 
	    //------- GESTION DES ANTERIORITES -------- GESTION DES ANTERIORITES ------- 
	    $leucocytes = null;
	    $hematies = null;
	    $p_neutrophiles = null;
	    $p_eosinophiles = null;
	    $p_basophiles = null;
	    $lymphocytes = null;
	    $monocytes = null;
	    $hematocrites = null;
	    $vgm = null;
	    $tcmh = null;
	    $ccmh = null;
	    $idr_cv = null;
	    $idr_ds = null;
	    $hemoglobines = null;
	    $plaquettes = null;
	    $vmp = null;
	    $idp = null;
	    $pct = null;
	    $reticulocytes = null;
	    
	    if($this->_anteriorite){
	        $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_ITALIC), 8);
	        $this->_page->drawText( '('.$control->convertDate($this->_anteriorite['demande']['date']).')',
	            $this->_leftMargin+450,
	            $this->_yPosition);
	        
	        //Champ1  --- Champ1  --- Champ1
	        $leucocytes = $this->_anteriorite['resultat']['champ1'];
	        
	        //Champ2  --- Champ2  --- Champ2   
	        $p_neutrophiles = $this->_anteriorite['resultat']['champ2'];
	        
	        //Champ3  --- Champ3  --- Champ3
	        $p_eosinophiles = $this->_anteriorite['resultat']['champ3'];

	        //Champ4  --- Champ4  --- Champ4
	        $p_basophiles = $this->_anteriorite['resultat']['champ4'];
	        
	        //Champ5  --- Champ5  --- Champ5
	        $lymphocytes = $this->_anteriorite['resultat']['champ5'];
	        
	        //Champ6  --- Champ6  --- Champ6
	        $monocytes = $this->_anteriorite['resultat']['champ6'];
	        
	        //-----------------------------------------------------------
	        //-----------------------------------------------------------
	        
	        //Champ12  --- Champ12  --- Champ12
	        $hematies = $this->_anteriorite['resultat']['champ12'];
	        
	        //Champ13  --- Champ13  --- Champ13
	        $hemoglobines = $this->_anteriorite['resultat']['champ13'];
	        
	        //Champ14  --- Champ14  --- Champ14
	        $hematocrites = $this->_anteriorite['resultat']['champ14'];
	        
	        //Champ15 --- Champ15 --- Champ15
	        $vgm = $this->_anteriorite['resultat']['champ15'];
	        
	        //Champ16  --- Champ16  --- Champ16
	        $tcmh = $this->_anteriorite['resultat']['champ16'];
	        
	        //Champ17  --- Champ17  --- Champ17
	        $ccmh = $this->_anteriorite['resultat']['champ17'];
	        
	        //Champ18  --- Champ18  --- Champ18
	        $idr_cv = $this->_anteriorite['resultat']['champ18'];
	        
	        //Champ19  --- Champ19  --- Champ19
	        $idr_ds = $this->_anteriorite['resultat']['champ19'];
	        
	        //--------------------------------------------------------
	        //--------------------------------------------------------
	        
	        //Champ20 --- Champ20 --- Champ20
	        $plaquettes = $this->_anteriorite['resultat']['champ20'];
	        
	        //Champ21 --- Champ21 --- Champ21
	        $vmp = $this->_anteriorite['resultat']['champ21'];
	        
	        //Champ22 --- Champ22 --- Champ22
	        $idp = $this->_anteriorite['resultat']['champ22'];
	        
	        //Champ23 --- Champ23 --- Champ23
	        $pct = $this->_anteriorite['resultat']['champ23'];
	        
	        //Champ24 --- Champ24 --- Champ24
	        $reticulocytes = $this->_anteriorite['resultat']['champ24'];
	        
	        
	    }else{
	        $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_ITALIC), 8);
	        $this->_page->drawText('(Néant)',
	            $this->_leftMargin+455,
	            $this->_yPosition);
	    }
	    
	    //GESTION DES RESULTATS DE L'ANALYSE
	    //GESTION DES RESULTATS DE L'ANALYSE
	    //GESTION DES RESULTATS DE L'ANALYSE
	    /**
	     * CHAMP 1 -- Leucocytes ------ CHAMP 1 -- Leucocytes ------ CHAMP 1
	     */
	    $this->_yPosition -= $noteLineHeight; //aller a la ligne suivante
	    
	    $this->_page->setLineColor(new ZendPdf\Color\Html('#e1e1e1'));
	    $this->_page->setLineWidth(15);
	    $this->_page->drawLine($this->_leftMargin,
	        $this->_yPosition +15,
	        $this->_pageWidth -
	        $this->_leftMargin,
	        $this->_yPosition +15);
 	    //------- Separateur actuelle et précédent ------- 
	    $this->_page->setLineColor(new ZendPdf\Color\Html('#ffffff'))->setLineWidth(15) ->drawLine($this->_leftMargin+386, $this->_yPosition +15, $this->_pageWidth - $this->_leftMargin-110, $this->_yPosition +15);
	    
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 10.5);
	    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', 'Leucocytes'),
	        $this->_leftMargin+15,
	        $this->_yPosition +12);
	    
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 10.5);
	    $this->_page->drawText($this->prixMill($resultats[1]['champ1']),
	        $this->_leftMargin+115,
	        $this->_yPosition +12);
	    
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 10.5);
	    $this->_page->drawText('/mm', $this->_leftMargin+160, $this->_yPosition +12);
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 8.5);
	    $this->_page->drawText('3', $this->_leftMargin+181, $this->_yPosition +15);
	    
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 10.5);
	    $this->_page->drawText('(4 000 - 10 000)',
	        $this->_leftMargin+255,
	        $this->_yPosition +12);
	    
	    //-----ANTERIORITE -------- ANTERIORITE -------- ANTERIORITE --------
	    //-----ANTERIORITE -------- ANTERIORITE -------- ANTERIORITE --------
	    if($leucocytes){
	        $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 10.5);
	        $this->_page->drawText($this->prixMill($leucocytes),
	            $this->_leftMargin+425,
	            $this->_yPosition +12);
	         
	        //$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 10.5);
	        //$this->_page->drawText('/mm', $this->_leftMargin+415, $this->_yPosition +12);
	        //$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 8.5);
	        //$this->_page->drawText('3', $this->_leftMargin+435, $this->_yPosition +15);
	    }

	    
	    /**
	     * CHAMP 2 -- Polynucléaires neutrophiles ------ CHAMP 2 -- Polynucléaires neutrophiles ------ CHAMP 2
	     */
	    
	    $this->_yPosition -= $noteLineHeight-14; //aller a la ligne suivante
	    
	    $this->_page->setLineColor(new ZendPdf\Color\Html('#f6f6f6'));
	    $this->_page->setLineWidth(15);
	    $this->_page->drawLine($this->_leftMargin,
	        $this->_yPosition +15,
	        $this->_pageWidth -
	        $this->_leftMargin,
	        $this->_yPosition +15);
	    //------- Separateur actuelle et précédent -------
	    $this->_page->setLineColor(new ZendPdf\Color\Html('#ffffff'))->setLineWidth(15) ->drawLine($this->_leftMargin+386, $this->_yPosition +15, $this->_pageWidth - $this->_leftMargin-110, $this->_yPosition +15);
	    	     
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', 'P. Neutrophiles'),
	        $this->_leftMargin+15,
	        $this->_yPosition +12);
	     
	    //------------- valeur en mm3 -------------
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText($this->prixMill($resultats[1]['champ2']),
	        $this->_leftMargin+115,
	        $this->_yPosition +12);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText('/mm', $this->_leftMargin+160, $this->_yPosition +12);
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 8.5);
	    $this->_page->drawText('3', $this->_leftMargin+181, $this->_yPosition +15);
	    //----------------------------------------- 
	    
	    //---------------- valeur en % -----------
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText($this->prixMill($resultats[1]['champ7']),
	        $this->_leftMargin+210,
	        $this->_yPosition +12);
	    
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText('%', $this->_leftMargin+230, $this->_yPosition +12);
	    //---------------------------------------
	    
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText('(2 000 - 7 000)',
	        $this->_leftMargin+255,
	        $this->_yPosition +12);
	    
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText('(45 - 70)',
	        $this->_leftMargin+340,
	        $this->_yPosition +12);
	    
	    //-----ANTERIORITE -------- ANTERIORITE -------- ANTERIORITE --------
	    //-----ANTERIORITE -------- ANTERIORITE -------- ANTERIORITE --------
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText("",
	        $this->_leftMargin+425,
	        $this->_yPosition +12);
	     
	    //-----ANTERIORITE -------- ANTERIORITE -------- ANTERIORITE --------
	    //-----ANTERIORITE -------- ANTERIORITE -------- ANTERIORITE --------
	    if($p_neutrophiles){
	        $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	        $this->_page->drawText($this->prixMill($p_neutrophiles),
	            $this->_leftMargin+425,
	            $this->_yPosition +12);
	    }
	    
	    /**
	     * CHAMP 3 -- Polynucléaire éosinophiles ------ CHAMP 3 -- Polynucléaires éosinophiles ------ CHAMP 3
	     */
	     
	    $this->_yPosition -= $noteLineHeight-14; //aller a la ligne suivante
	    
	    $this->_page->setLineColor(new ZendPdf\Color\Html('#e1e1e1'));
	    $this->_page->setLineWidth(15);
	    $this->_page->drawLine($this->_leftMargin,
	        $this->_yPosition +15,
	        $this->_pageWidth -
	        $this->_leftMargin,
	        $this->_yPosition +15);
	    //------- Separateur actuelle et précédent -------
	    $this->_page->setLineColor(new ZendPdf\Color\Html('#ffffff'))->setLineWidth(15) ->drawLine($this->_leftMargin+386, $this->_yPosition +15, $this->_pageWidth - $this->_leftMargin-110, $this->_yPosition +15);
	    	     
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText('P. Eosinophiles',
	        $this->_leftMargin+15,
	        $this->_yPosition +12);
	     
	    //------------- valeur en mm3 -------------
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText($this->prixMill($resultats[1]['champ3']),
	        $this->_leftMargin+115,
	        $this->_yPosition +12);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText('/mm', $this->_leftMargin+160, $this->_yPosition +12);
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 8.5);
	    $this->_page->drawText('3', $this->_leftMargin+181, $this->_yPosition +15);
	    //----------------------------------------
	    
	    //---------------- valeur en % -----------
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText($this->prixMill($resultats[1]['champ8']),
	        $this->_leftMargin+210,
	        $this->_yPosition +12);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText('%', $this->_leftMargin+230, $this->_yPosition +12);
	    //---------------------------------------
	    
	    
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText('(20 - 500)',
	        $this->_leftMargin+255,
	        $this->_yPosition +12);
	    
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText('(0 - 5)',
	        $this->_leftMargin+340,
	        $this->_yPosition +12);
	    
	    
	    //-----ANTERIORITE -------- ANTERIORITE -------- ANTERIORITE --------
	    //-----ANTERIORITE -------- ANTERIORITE -------- ANTERIORITE --------
	    if($p_eosinophiles){
	    	$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    	$this->_page->drawText('328'.$this->prixMill($p_eosinophiles),
	    			$this->_leftMargin+425,
	    			$this->_yPosition +12);
	    }
	    
	    /**
	     * CHAMP 4 -- Polynucléaires basophiles ------ CHAMP 4 -- Polynucléaire basophiles ------ CHAMP 4
	     */
	    
	    $this->_yPosition -= $noteLineHeight-14; //aller a la ligne suivante
	    
	    $this->_page->setLineColor(new ZendPdf\Color\Html('#f6f6f6'));
	    $this->_page->setLineWidth(15);
	    $this->_page->drawLine($this->_leftMargin,
	        $this->_yPosition +15,
	        $this->_pageWidth -
	        $this->_leftMargin,
	        $this->_yPosition +15);
	    //------- Separateur actuelle et précédent -------
	    $this->_page->setLineColor(new ZendPdf\Color\Html('#ffffff'))->setLineWidth(15) ->drawLine($this->_leftMargin+386, $this->_yPosition +15, $this->_pageWidth - $this->_leftMargin-110, $this->_yPosition +15);
	    	     
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText('P. Basophiles',
	        $this->_leftMargin+15,
	        $this->_yPosition +12);
	     
	    //------------- valeur en mm3 -------------
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText($this->prixMill($resultats[1]['champ4']),
	        $this->_leftMargin+115,
	        $this->_yPosition +12);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText('/mm', $this->_leftMargin+160, $this->_yPosition +12);
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 8.5);
	    $this->_page->drawText('3', $this->_leftMargin+181, $this->_yPosition +15);
	    //----------------------------------------
	     
	    //---------------- valeur en % -----------
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText($this->prixMill($resultats[1]['champ9']),
	        $this->_leftMargin+210,
	        $this->_yPosition +12);
	    
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText('%', $this->_leftMargin+230, $this->_yPosition +12);
	    //---------------------------------------
	    
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText('(0 - 100)',
	        $this->_leftMargin+255,
	        $this->_yPosition +12);
	    
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText('(0 - 3)',
	        $this->_leftMargin+340,
	        $this->_yPosition +12);
	    
	    //-----ANTERIORITE -------- ANTERIORITE -------- ANTERIORITE --------
	    //-----ANTERIORITE -------- ANTERIORITE -------- ANTERIORITE --------
	    if($p_basophiles){
	    	$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    	$this->_page->drawText($this->prixMill($p_basophiles),
	    			$this->_leftMargin+425,
	    			$this->_yPosition +12);
	    }
	     
	    /**
	     * CHAMP 5 -- Lymphocytes ------ CHAMP 5 -- Lymphocytes ------ CHAMP 5
	     */
	    
	    $this->_yPosition -= $noteLineHeight-14; //aller a la ligne suivante
	    
	    $this->_page->setLineColor(new ZendPdf\Color\Html('#e1e1e1'));
	    $this->_page->setLineWidth(15);
	    $this->_page->drawLine($this->_leftMargin,
	        $this->_yPosition +15,
	        $this->_pageWidth -
	        $this->_leftMargin,
	        $this->_yPosition +15);
	    //------- Separateur actuelle et précédent -------
	    $this->_page->setLineColor(new ZendPdf\Color\Html('#ffffff'))->setLineWidth(15) ->drawLine($this->_leftMargin+386, $this->_yPosition +15, $this->_pageWidth - $this->_leftMargin-110, $this->_yPosition +15);
	    	     
	    
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText('Lymphocytes',
	        $this->_leftMargin+15,
	        $this->_yPosition +12);
	     
	    //------------- valeur en mm3 -------------
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText($this->prixMill($resultats[1]['champ5']),
	        $this->_leftMargin+115,
	        $this->_yPosition +12);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText('/mm', $this->_leftMargin+160, $this->_yPosition +12);
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 8.5);
	    $this->_page->drawText('3', $this->_leftMargin+181, $this->_yPosition +15);
	    //----------------------------------------

	    //---------------- valeur en % -----------
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText($this->prixMill($resultats[1]['champ10']),
	        $this->_leftMargin+210,
	        $this->_yPosition +12);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText('%', $this->_leftMargin+230, $this->_yPosition +12);
	    //---------------------------------------
	    
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText('(800 - 4 000)',
	        $this->_leftMargin+255,
	        $this->_yPosition +12);
	    
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText('(20 - 40)',
	        $this->_leftMargin+340,
	        $this->_yPosition +12);
	     
	    //-----ANTERIORITE -------- ANTERIORITE -------- ANTERIORITE --------
	    //-----ANTERIORITE -------- ANTERIORITE -------- ANTERIORITE --------
	    if($lymphocytes){
	    	$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    	$this->_page->drawText($this->prixMill($lymphocytes),
	    			$this->_leftMargin+425,
	    			$this->_yPosition +12);
	    }
	    
	    /**
	     * CHAMP 6 -- Monocytes ------ CHAMP 6 -- Monocytes ------ CHAMP 6
	     */
	    
	    $this->_yPosition -= $noteLineHeight-14; //aller a la ligne suivante
	    
	    $this->_page->setLineColor(new ZendPdf\Color\Html('#f6f6f6'));
	    $this->_page->setLineWidth(15);
	    $this->_page->drawLine($this->_leftMargin,
	        $this->_yPosition +15,
	        $this->_pageWidth -
	        $this->_leftMargin,
	        $this->_yPosition +15);
	    //------- Separateur actuelle et précédent -------
	    $this->_page->setLineColor(new ZendPdf\Color\Html('#ffffff'))->setLineWidth(15) ->drawLine($this->_leftMargin+386, $this->_yPosition +15, $this->_pageWidth - $this->_leftMargin-110, $this->_yPosition +15);
	    	     
	    
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText('Monocytes',
	        $this->_leftMargin+15,
	        $this->_yPosition +12);
	     
	    //------------- valeur en mm3 -------------
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText($this->prixMill($resultats[1]['champ6']),
	        $this->_leftMargin+115,
	        $this->_yPosition +12);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText('/mm', $this->_leftMargin+160, $this->_yPosition +12);
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 8.5);
	    $this->_page->drawText('3', $this->_leftMargin+181, $this->_yPosition +15);
	    //-----------------------------------------
	    
	    //---------------- valeur en % ------------
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText($this->prixMill($resultats[1]['champ11']),
	        $this->_leftMargin+210,
	        $this->_yPosition +12);
	    
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText('%', $this->_leftMargin+230, $this->_yPosition +12);
	    //---------------------------------------
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText('(120 - 1 200)',
	        $this->_leftMargin+255,
	        $this->_yPosition +12);
	    
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText('(3 - 15)',
	        $this->_leftMargin+340,
	        $this->_yPosition +12);
	    
	    //-----ANTERIORITE -------- ANTERIORITE -------- ANTERIORITE --------
	    //-----ANTERIORITE -------- ANTERIORITE -------- ANTERIORITE --------
	    if($monocytes){
	    	$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    	$this->_page->drawText($this->prixMill($monocytes),
	    			$this->_leftMargin+425,
	    			$this->_yPosition +12);
	    }
	    
	    
	    /**
	     * CHAMP 12 -- Hematies ------ CHAMP 12 -- Hematies ------ CHAMP 12
	     * ON DESCEND *** ON DESCEND *** ON DESCEND
	     */
	    
	    $this->_yPosition -= $noteLineHeight; //aller a la ligne suivante
	    
	    $this->_page->setLineColor(new ZendPdf\Color\Html('#e1e1e1'));
	    $this->_page->setLineWidth(15);
	    $this->_page->drawLine($this->_leftMargin,
	        $this->_yPosition +15,
	        $this->_pageWidth -
	        $this->_leftMargin,
	        $this->_yPosition +15);
	    //------- Separateur actuelle et précédent -------
	    $this->_page->setLineColor(new ZendPdf\Color\Html('#ffffff'))->setLineWidth(15) ->drawLine($this->_leftMargin+386, $this->_yPosition +15, $this->_pageWidth - $this->_leftMargin-110, $this->_yPosition +15);
	    	     
	    
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 10.5);
	    $this->_page->drawText('Hématies',
	        $this->_leftMargin+15,
	        $this->_yPosition +12);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 10.5);
	    $this->_page->drawText($this->prixMill($resultats[1]['champ12']),
	        $this->_leftMargin+115,
	        $this->_yPosition +12);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 10.5);
	    $this->_page->drawText('10', $this->_leftMargin+160, $this->_yPosition +12);
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 8.5);
	    $this->_page->drawText('6', $this->_leftMargin+172, $this->_yPosition +15);
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 10.5);
	    $this->_page->drawText('/mm', $this->_leftMargin+178, $this->_yPosition +12);
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 8.5);
	    $this->_page->drawText('3', $this->_leftMargin+199, $this->_yPosition +15);
	    
	    
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 10.5);
	    $this->_page->drawText('(3,5 - 5,0)',
	        $this->_leftMargin+255,
	        $this->_yPosition +12);
	    
	    
	    //-----ANTERIORITE -------- ANTERIORITE -------- ANTERIORITE --------
	    //-----ANTERIORITE -------- ANTERIORITE -------- ANTERIORITE --------
	    if($hematies){
	        $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 10.5);
	        $this->_page->drawText($this->prixMill($hematies),
	            $this->_leftMargin+425,
	            $this->_yPosition +12);
	    
	        //$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 10.5);
	        //$this->_page->drawText('10', $this->_leftMargin+415, $this->_yPosition +12);
	        //$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 8.5);
	        //$this->_page->drawText('6', $this->_leftMargin+427, $this->_yPosition +15);
	        //$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 10.5);
	        //$this->_page->drawText('/mm', $this->_leftMargin+433, $this->_yPosition +12);
	        //$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 8.5);
	        //$this->_page->drawText('3', $this->_leftMargin+451, $this->_yPosition +15);
	    }
	    
	    /** 
	     * CHAMP 13 -- Hémoglobine  ------ CHAMP 13 -- Hémoglobine  ------ CHAMP 13
	     */
	    
	    $this->_yPosition -= $noteLineHeight-14; //aller a la ligne suivante
	    
	    $this->_page->setLineColor(new ZendPdf\Color\Html('#f6f6f6'));
	    $this->_page->setLineWidth(15);
	    $this->_page->drawLine($this->_leftMargin,
	        $this->_yPosition +15,
	        $this->_pageWidth -
	        $this->_leftMargin,
	        $this->_yPosition +15);
	    //------- Separateur actuelle et précédent -------
	    $this->_page->setLineColor(new ZendPdf\Color\Html('#ffffff'))->setLineWidth(15) ->drawLine($this->_leftMargin+386, $this->_yPosition +15, $this->_pageWidth - $this->_leftMargin-110, $this->_yPosition +15);
	    	     
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 10.5);
	    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', 'HÃ©moglobine'),
	        $this->_leftMargin+15,
	        $this->_yPosition +12);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 10.5);
	    $this->_page->drawText($this->prixMill($resultats[1]['champ13']),
	        $this->_leftMargin+115,
	        $this->_yPosition +12);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 10.5);
	    $this->_page->drawText('g/dl', $this->_leftMargin+160, $this->_yPosition +12);
	    
	    
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 10.5);
	    $this->_page->drawText('(11 - 15)',
	        $this->_leftMargin+255,
	        $this->_yPosition +12);
	    
	    //-----ANTERIORITE -------- ANTERIORITE -------- ANTERIORITE --------
	    //-----ANTERIORITE -------- ANTERIORITE -------- ANTERIORITE --------
	    if($hemoglobines){
	        $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 10.5);
	        $this->_page->drawText($this->prixMill($hemoglobines),
	            $this->_leftMargin+425,
	            $this->_yPosition +12);
	         
	        //$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 10.5);
	        //$this->_page->drawText('g/dl', $this->_leftMargin+415, $this->_yPosition +12);
	        
	    }
	    
	    /** 
	     * CHAMP 14 -- Hématocrite ------ CHAMP 14 -- Hématocrite ------ CHAMP 14
	     */
	    
	    $this->_yPosition -= $noteLineHeight-14; //aller a la ligne suivante
	    
	    $this->_page->setLineColor(new ZendPdf\Color\Html('#e1e1e1'));
	    $this->_page->setLineWidth(15);
	    $this->_page->drawLine($this->_leftMargin,
	        $this->_yPosition +15,
	        $this->_pageWidth -
	        $this->_leftMargin,
	        $this->_yPosition +15);
	    //------- Separateur actuelle et précédent -------
	    $this->_page->setLineColor(new ZendPdf\Color\Html('#ffffff'))->setLineWidth(15) ->drawLine($this->_leftMargin+386, $this->_yPosition +15, $this->_pageWidth - $this->_leftMargin-110, $this->_yPosition +15);
	    	    
	    
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText('Hématocrite',
	        $this->_leftMargin+15,
	        $this->_yPosition +12);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText($this->prixMill($resultats[1]['champ14']),
	        $this->_leftMargin+115,
	        $this->_yPosition +12);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText('%', $this->_leftMargin+160, $this->_yPosition +12);
	    
	    
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText('(37 - 47)',
	        $this->_leftMargin+255,
	        $this->_yPosition +12);
	    
	    
	    //-----ANTERIORITE -------- ANTERIORITE -------- ANTERIORITE --------
	    //-----ANTERIORITE -------- ANTERIORITE -------- ANTERIORITE --------
	    if($hematocrites){
	    	$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    	$this->_page->drawText($this->prixMill($hematocrites),
	    			$this->_leftMargin+425,
	    			$this->_yPosition +12);
	    }
	    /**
	     * CHAMP 15 -- V.G.M ------ CHAMP 15 -- V.G.M ------ CHAMP 15
	     */
	    
	    $this->_yPosition -= $noteLineHeight-14; //aller a la ligne suivante
	    
	    $this->_page->setLineColor(new ZendPdf\Color\Html('#f6f6f6'));
	    $this->_page->setLineWidth(15);
	    $this->_page->drawLine($this->_leftMargin,
	        $this->_yPosition +15,
	        $this->_pageWidth -
	        $this->_leftMargin,
	        $this->_yPosition +15);
	    //------- Separateur actuelle et précédent -------
	    $this->_page->setLineColor(new ZendPdf\Color\Html('#ffffff'))->setLineWidth(15) ->drawLine($this->_leftMargin+386, $this->_yPosition +15, $this->_pageWidth - $this->_leftMargin-110, $this->_yPosition +15);
	    	     
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', 'V.G.M'),
	        $this->_leftMargin+15,
	        $this->_yPosition +12);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText($this->prixMill($resultats[1]['champ15']),
	        $this->_leftMargin+115,
	        $this->_yPosition +12);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText('fl', $this->_leftMargin+160, $this->_yPosition +12);
	    
	    
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText('(80 - 100)',
	        $this->_leftMargin+255,
	        $this->_yPosition +12);
	    
	    //-----ANTERIORITE -------- ANTERIORITE -------- ANTERIORITE --------
	    //-----ANTERIORITE -------- ANTERIORITE -------- ANTERIORITE --------
	    if($vgm){
	        $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	        $this->_page->drawText($vgm,
	            $this->_leftMargin+425,
	            $this->_yPosition +12);
	    
	        //$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	        //$this->_page->drawText('fl', $this->_leftMargin+415, $this->_yPosition +12);
	    }
	    
	    /**
	     * CHAMP 16 -- T.C.M.H ------ CHAMP 16 -- T.C.M.H ------ CHAMP 16
	     */
	    
	    $this->_yPosition -= $noteLineHeight-14; //aller a la ligne suivante
	    
	    $this->_page->setLineColor(new ZendPdf\Color\Html('#e1e1e1'));
	    $this->_page->setLineWidth(15);
	    $this->_page->drawLine($this->_leftMargin,
	        $this->_yPosition +15,
	        $this->_pageWidth -
	        $this->_leftMargin,
	        $this->_yPosition +15);
	    //------- Separateur actuelle et précédent -------
	    $this->_page->setLineColor(new ZendPdf\Color\Html('#ffffff'))->setLineWidth(15) ->drawLine($this->_leftMargin+386, $this->_yPosition +15, $this->_pageWidth - $this->_leftMargin-110, $this->_yPosition +15);
	    
	    
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', 'T.C.M.H'),
	        $this->_leftMargin+15,
	        $this->_yPosition +12);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText($this->prixMill($resultats[1]['champ16']),
	        $this->_leftMargin+115,
	        $this->_yPosition +12);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText('pg', $this->_leftMargin+160, $this->_yPosition +12);
	    
	    
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText('(27 - 34)',
	        $this->_leftMargin+255,
	        $this->_yPosition +12);
	    
	    
	    //-----ANTERIORITE -------- ANTERIORITE -------- ANTERIORITE --------
	    //-----ANTERIORITE -------- ANTERIORITE -------- ANTERIORITE --------
	    if($tcmh){
	    	$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    	$this->_page->drawText($this->prixMill($tcmh),
	    			$this->_leftMargin+425,
	    			$this->_yPosition +12);
	    }
	    /**
	     * CHAMP 17 -- C.C.M.H ------ CHAMP 17 -- C.C.M.H ------ CHAMP 17
	     */
	    
	    $this->_yPosition -= $noteLineHeight-14; //aller a la ligne suivante
	    
	    $this->_page->setLineColor(new ZendPdf\Color\Html('#f6f6f6'));
	    $this->_page->setLineWidth(15);
	    $this->_page->drawLine($this->_leftMargin,
	        $this->_yPosition +15,
	        $this->_pageWidth -
	        $this->_leftMargin,
	        $this->_yPosition +15);
	    //------- Separateur actuelle et précédent -------
	    $this->_page->setLineColor(new ZendPdf\Color\Html('#ffffff'))->setLineWidth(15) ->drawLine($this->_leftMargin+386, $this->_yPosition +15, $this->_pageWidth - $this->_leftMargin-110, $this->_yPosition +15);
	    	     
	    
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', 'C.C.M.H'),
	        $this->_leftMargin+15,
	        $this->_yPosition +12);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText($this->prixMill($resultats[1]['champ17']),
	        $this->_leftMargin+115,
	        $this->_yPosition +12);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText('g/dl', $this->_leftMargin+160, $this->_yPosition +12);
	    
	    
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText('(32 - 36)',
	        $this->_leftMargin+255,
	        $this->_yPosition +12);
	    
	    
	    //-----ANTERIORITE -------- ANTERIORITE -------- ANTERIORITE --------
	    //-----ANTERIORITE -------- ANTERIORITE -------- ANTERIORITE --------
	    if($ccmh){
	    	$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    	$this->_page->drawText($this->prixMill($ccmh),
	    			$this->_leftMargin+425,
	    			$this->_yPosition +12);
	    }
	    
	    /**
	     * CHAMP 18 -- IDR-CV ------ CHAMP 18 -- IDR-CV ------ CHAMP 18
	     */
	    
	    $this->_yPosition -= $noteLineHeight-14; //aller a la ligne suivante
	    
	    $this->_page->setLineColor(new ZendPdf\Color\Html('#e1e1e1'));
	    $this->_page->setLineWidth(15);
	    $this->_page->drawLine($this->_leftMargin,
	        $this->_yPosition +15,
	        $this->_pageWidth -
	        $this->_leftMargin,
	        $this->_yPosition +15);
	    //------- Separateur actuelle et précédent -------
	    $this->_page->setLineColor(new ZendPdf\Color\Html('#ffffff'))->setLineWidth(15) ->drawLine($this->_leftMargin+386, $this->_yPosition +15, $this->_pageWidth - $this->_leftMargin-110, $this->_yPosition +15);
	    	     
	    
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', 'IDR-CV'),
	        $this->_leftMargin+15,
	        $this->_yPosition +12);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText($this->prixMill($resultats[1]['champ18']),
	        $this->_leftMargin+115,
	        $this->_yPosition +12);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText('%', $this->_leftMargin+160, $this->_yPosition +12);
	    
	    
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText('(11 - 16)',
	        $this->_leftMargin+255,
	        $this->_yPosition +12);
	    
	    //-----ANTERIORITE -------- ANTERIORITE -------- ANTERIORITE --------
	    //-----ANTERIORITE -------- ANTERIORITE -------- ANTERIORITE --------
	    if($idr_cv){
	    	$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    	$this->_page->drawText($this->prixMill($idr_cv),
	    			$this->_leftMargin+425,
	    			$this->_yPosition +12);
	    }
	    
	    
	    /**
	     * CHAMP 19 -- IDR-DS ------ CHAMP 19 -- IDR-DS ------ CHAMP 19
	     */
	    if($resultats[1]['champ19']){
	        
	        $this->_yPosition -= $noteLineHeight-14; //aller a la ligne suivante
	         
	        $this->_page->setLineColor(new ZendPdf\Color\Html('#f6f6f6'));
	        $this->_page->setLineWidth(15);
	        $this->_page->drawLine($this->_leftMargin,
	            $this->_yPosition +15,
	            $this->_pageWidth -
	            $this->_leftMargin,
	            $this->_yPosition +15);

	        //------- Separateur actuelle et précédent -------
	        $this->_page->setLineColor(new ZendPdf\Color\Html('#ffffff'))->setLineWidth(15) ->drawLine($this->_leftMargin+386, $this->_yPosition +15, $this->_pageWidth - $this->_leftMargin-110, $this->_yPosition +15);
	        	         
	        
	        $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	        $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', 'IDR-DS'),
	            $this->_leftMargin+15,
	            $this->_yPosition +12);
	        
	        $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	        $this->_page->drawText($this->prixMill($resultats[1]['champ19']),
	            $this->_leftMargin+115,
	            $this->_yPosition +12);
	        
	        $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	        $this->_page->drawText('fl', $this->_leftMargin+160, $this->_yPosition +12);
	         
	         
	        $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	        $this->_page->drawText('(35 - 56)',
	            $this->_leftMargin+255,
	            $this->_yPosition +12);
	        
	        //-----ANTERIORITE -------- ANTERIORITE -------- ANTERIORITE --------
	        //-----ANTERIORITE -------- ANTERIORITE -------- ANTERIORITE --------
	        if($idr_ds){
	        	$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	        	$this->_page->drawText($this->prixMill($idr_ds),
	        			$this->_leftMargin+425,
	        			$this->_yPosition +12);
	        }
	        
	    }
	    
	    /**
	     * CHAMP 20 -- Plaquettes ------ CHAMP 20 -- Plaquettes ------ CHAMP 20
	     * ON DESCEND *** ON DESCEND *** ON DESCEND
	     */
	    
	    $this->_yPosition -= $noteLineHeight; //aller a la ligne suivante
	    
	    $this->_page->setLineColor(new ZendPdf\Color\Html('#e1e1e1'));
	    $this->_page->setLineWidth(15);
	    $this->_page->drawLine($this->_leftMargin,
	        $this->_yPosition +15,
	        $this->_pageWidth -
	        $this->_leftMargin,
	        $this->_yPosition +15);

	    //------- Separateur actuelle et précédent -------
	    $this->_page->setLineColor(new ZendPdf\Color\Html('#ffffff'))->setLineWidth(15) ->drawLine($this->_leftMargin+386, $this->_yPosition +15, $this->_pageWidth - $this->_leftMargin-110, $this->_yPosition +15);
	    	     
	    
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 10.5);
	    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', 'Plaquettes'),
	        $this->_leftMargin+15,
	        $this->_yPosition +12);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 10.5);
	    $this->_page->drawText($this->prixMill($resultats[1]['champ20']),
	        $this->_leftMargin+115,
	        $this->_yPosition +12);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 10.5);
	    $this->_page->drawText('10', $this->_leftMargin+160, $this->_yPosition +12);
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 8.5);
	    $this->_page->drawText('3', $this->_leftMargin+172, $this->_yPosition +15);
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 10.5);
	    $this->_page->drawText('/mm', $this->_leftMargin+178, $this->_yPosition +12);
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 8.5);
	    $this->_page->drawText('3', $this->_leftMargin+199, $this->_yPosition +15);
	    
	    
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 10.5);
	    $this->_page->drawText('(150 - 450)',
	        $this->_leftMargin+255,
	        $this->_yPosition +12);
	    
	    //-----ANTERIORITE -------- ANTERIORITE -------- ANTERIORITE --------
	    //-----ANTERIORITE -------- ANTERIORITE -------- ANTERIORITE --------
	    if($plaquettes){
	        $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 10.5);
	        $this->_page->drawText($plaquettes,
	            $this->_leftMargin+425,
	            $this->_yPosition +12);
	         
	        //$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 10.5);
	        //$this->_page->drawText('10', $this->_leftMargin+415, $this->_yPosition +12);
	        //$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 8.5);
	        //$this->_page->drawText('3', $this->_leftMargin+427, $this->_yPosition +15);
	        //$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 10.5);
	        //$this->_page->drawText('/mm', $this->_leftMargin+433, $this->_yPosition +12);
	        //$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 8.5);
	        //$this->_page->drawText('3', $this->_leftMargin+451, $this->_yPosition +15);
	    }
	    
	    /**
	     * CHAMP 21 -- VMP ------ CHAMP 21 -- VMP ------ CHAMP 21
	     */
	    
	    $this->_yPosition -= $noteLineHeight-14; //aller a la ligne suivante
	    
	    $this->_page->setLineColor(new ZendPdf\Color\Html('#f6f6f6'));
	    $this->_page->setLineWidth(15);
	    $this->_page->drawLine($this->_leftMargin,
	        $this->_yPosition +15,
	        $this->_pageWidth -
	        $this->_leftMargin,
	        $this->_yPosition +15);
	    //------- Separateur actuelle et précédent -------
	    $this->_page->setLineColor(new ZendPdf\Color\Html('#ffffff'))->setLineWidth(15) ->drawLine($this->_leftMargin+386, $this->_yPosition +15, $this->_pageWidth - $this->_leftMargin-110, $this->_yPosition +15);
	    	     
	    
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', 'VMP'),
	        $this->_leftMargin+15,
	        $this->_yPosition +12);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText($this->prixMill($resultats[1]['champ21']),
	        $this->_leftMargin+115,
	        $this->_yPosition +12);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText('fl', $this->_leftMargin+160, $this->_yPosition +12);
	    
	    
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText('(6,5 - 12,0)',
	        $this->_leftMargin+255,
	        $this->_yPosition +12);
	    
	    //-----ANTERIORITE -------- ANTERIORITE -------- ANTERIORITE --------
	    //-----ANTERIORITE -------- ANTERIORITE -------- ANTERIORITE --------
	    if($vmp){
	    	$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    	$this->_page->drawText($this->prixMill($vmp),
	    			$this->_leftMargin+425,
	    			$this->_yPosition +12);
	    }
	    
	    
	    /**
	     * CHAMP 22 -- IDP ------ CHAMP 22 -- IDP ------ CHAMP 22
	     */
	    
	    $this->_yPosition -= $noteLineHeight-14; //aller a la ligne suivante
	    
	    $this->_page->setLineColor(new ZendPdf\Color\Html('#e1e1e1'));
	    $this->_page->setLineWidth(15);
	    $this->_page->drawLine($this->_leftMargin,
	        $this->_yPosition +15,
	        $this->_pageWidth -
	        $this->_leftMargin,
	        $this->_yPosition +15);
	    //------- Separateur actuelle et précédent -------
	    $this->_page->setLineColor(new ZendPdf\Color\Html('#ffffff'))->setLineWidth(15) ->drawLine($this->_leftMargin+386, $this->_yPosition +15, $this->_pageWidth - $this->_leftMargin-110, $this->_yPosition +15);
	    
	    
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', 'IDP'),
	        $this->_leftMargin+15,
	        $this->_yPosition +12);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText($this->prixMill($resultats[1]['champ22']),
	        $this->_leftMargin+115,
	        $this->_yPosition +12);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText('g/dl', $this->_leftMargin+160, $this->_yPosition +12);
	    
	    
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText('(9,0 - 17,0)',
	        $this->_leftMargin+255,
	        $this->_yPosition +12);
	    
	    //-----ANTERIORITE -------- ANTERIORITE -------- ANTERIORITE --------
	    //-----ANTERIORITE -------- ANTERIORITE -------- ANTERIORITE --------
	    if($idp){
	    	$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    	$this->_page->drawText($this->prixMill($idp),
	    			$this->_leftMargin+425,
	    			$this->_yPosition +12);
	    }
	    
	    /**
	     * CHAMP 23 -- PCT ------ CHAMP 3 -- PCT ------ CHAMP 23
	     */
	    
	    $this->_yPosition -= $noteLineHeight-14; //aller a la ligne suivante
	    
	    $this->_page->setLineColor(new ZendPdf\Color\Html('#f6f6f6'));
	    $this->_page->setLineWidth(15);
	    $this->_page->drawLine($this->_leftMargin,
	        $this->_yPosition +15,
	        $this->_pageWidth -
	        $this->_leftMargin,
	        $this->_yPosition +15);
	    //------- Separateur actuelle et précédent -------
	    $this->_page->setLineColor(new ZendPdf\Color\Html('#ffffff'))->setLineWidth(15) ->drawLine($this->_leftMargin+386, $this->_yPosition +15, $this->_pageWidth - $this->_leftMargin-110, $this->_yPosition +15);
	    	     
	    
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', 'PCT'),
	        $this->_leftMargin+15,
	        $this->_yPosition +12);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText($this->prixMill($resultats[1]['champ23']),
	        $this->_leftMargin+115,
	        $this->_yPosition +12);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText('%', $this->_leftMargin+160, $this->_yPosition +12);
	    
	    
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText('(0,108 - 0,282)',
	        $this->_leftMargin+255,
	        $this->_yPosition +12);
	    
	    //-----ANTERIORITE -------- ANTERIORITE -------- ANTERIORITE --------
	    //-----ANTERIORITE -------- ANTERIORITE -------- ANTERIORITE --------
	    if($pct){
	    	$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    	$this->_page->drawText($this->prixMill($pct),
	    			$this->_leftMargin+425,
	    			$this->_yPosition +12);
	    }
	    
	    
	    /**
	     * CHAMP 24 & 25 -- Taux de réticulocytes ------ CHAMP 24 & 25 -- Taux de réticulocytes ------ 
	     */
	     
	    $this->_yPosition -= $noteLineHeight; //aller a la ligne suivante
	     
	    $this->_page->setLineColor(new ZendPdf\Color\Html('#e1e1e1'));
	    $this->_page->setLineWidth(15);
	    $this->_page->drawLine($this->_leftMargin,
	        $this->_yPosition +15,
	        $this->_pageWidth -
	        $this->_leftMargin,
	        $this->_yPosition +15);
	    //------- Separateur actuelle et précédent -------
	    $this->_page->setLineColor(new ZendPdf\Color\Html('#ffffff'))->setLineWidth(15) ->drawLine($this->_leftMargin+386, $this->_yPosition +15, $this->_pageWidth - $this->_leftMargin-110, $this->_yPosition +15);
	    	    
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 10.5);
	    $this->_page->drawText('Réticulocytes',
	        $this->_leftMargin+15,
	        $this->_yPosition +12);
	    
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 10.5);
	    $this->_page->drawText($this->prixMill($resultats[1]['champ24']),
	        $this->_leftMargin+115,
	        $this->_yPosition +12);
	    
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 10.5);
	    $this->_page->drawText('/mm', $this->_leftMargin+160, $this->_yPosition +12);
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 8.5);
	    $this->_page->drawText('3', $this->_leftMargin+181, $this->_yPosition +15);
	    
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 10.5);
	    $this->_page->drawText($this->prixMill($resultats[1]['champ25'])." %",
	        $this->_leftMargin+210,
	        $this->_yPosition +12);
	    
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 10.5);
	    $this->_page->drawText('(25 000 - 80 000)',
	        $this->_leftMargin+255,
	        $this->_yPosition +12);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 10.5);
	    $this->_page->drawText('(0,5 - 1,5)',
	        $this->_leftMargin+335,
	        $this->_yPosition +12);
	    
	    //-----ANTERIORITE -------- ANTERIORITE -------- ANTERIORITE --------
	    //-----ANTERIORITE -------- ANTERIORITE -------- ANTERIORITE --------
	    if($reticulocytes){
	    	$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 10.5);
	    	$this->_page->drawText($this->prixMill($reticulocytes),
	    			$this->_leftMargin+425,
	    			$this->_yPosition +12);
	    }
	    
	    
	    
	    /**
	     * ------ COMMENTAIRE ------ COMMENTAIRE ------ COMMENTAIRE -------
	     */
	    
	    $this->_yPosition -= $noteLineHeight; //aller a la ligne suivante
	    
	    $this->_page->setLineColor(new ZendPdf\Color\Html('#999999'));
	    $this->_page->setLineWidth(1);
	    $this->_page->drawLine($this->_leftMargin+10,
	        $this->_yPosition +10,
	        $this->_pageWidth -
	        $this->_leftMargin-420,
	        $this->_yPosition +10);
	    
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 10.5);
	    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', 'Commentaire:'),
	        $this->_leftMargin+10,
	        $this->_yPosition +12);
	    
	    $this->_yPosition -= $noteLineHeight-10; //aller a la ligne suivante
	    $this->_page->setLineColor(new ZendPdf\Color\Html('#e1e1e1'));
	    $this->_page->setLineWidth(1);
	    $this->_page->drawLine($this->_leftMargin,
	        $this->_yPosition +10,
	        $this->_pageWidth -
	        $this->_leftMargin,
	        $this->_yPosition +10);

	    $commentaire = $this->scinderText($resultats[1]['commentaire']);
	    
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $commentaire[1]),
	        $this->_leftMargin+10,
	        $this->_yPosition +12);
	    
	    $this->_yPosition -= $noteLineHeight-10; //aller a la ligne suivante
	    $this->_page->setLineColor(new ZendPdf\Color\Html('#e1e1e1'));
	    $this->_page->setLineWidth(1);
	    $this->_page->drawLine($this->_leftMargin,
	        $this->_yPosition +10,
	        $this->_pageWidth -
	        $this->_leftMargin,
	        $this->_yPosition +10);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $commentaire[2]),
	        $this->_leftMargin+10,
	        $this->_yPosition +12);
	    
	    $this->_yPosition -= $noteLineHeight-10; //aller a la ligne suivante
	    $this->_page->setLineColor(new ZendPdf\Color\Html('#e1e1e1'));
	    $this->_page->setLineWidth(1);
	    $this->_page->drawLine($this->_leftMargin,
	        $this->_yPosition +10,
	        $this->_pageWidth -
	        $this->_leftMargin,
	        $this->_yPosition +10);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $commentaire[3]),
	        $this->_leftMargin+10,
	        $this->_yPosition +12);
	    
	    
	    return $this->_page;
	}
	
	
	public function typage_hemoglobine($noteLineHeight, $Designation, $infosAnalyseDemande, $value){
	    
	    //-----------------------------------------------
	    $this->_yPosition -= 15;
	    //----------------------------------------------
	    $this->_page->setFont($this->_newTimeGras, 9);
	    $this->_page->drawText('ETHNIE :',
	        $this->_leftMargin+161,
	        $this->_yPosition);
	    $this->_page->setFont($this->_newTime, 10);
	    $this->_page->drawText(iconv ('UTF-8' ,'ISO-8859-1' , $this->_depistage->current()['ethnie']),
	        $this->_leftMargin+210,
	        $this->_yPosition);
	    
	    //-----------------------------------------------
// 	    $this->_yPosition -= 15;
	    //----------------------------------------------
// 	    $this->_page->setFont($this->_newTimeGras, 9);
// 	    $this->_page->drawText('TELEPHONE :',
// 	        $this->_leftMargin+140,
// 	        $this->_yPosition);
// 	    $this->_page->setFont($this->_newTime, 10);
// 	    $this->_page->drawText(iconv ('UTF-8' ,'ISO-8859-1' , $value->telephone),
// 	        $this->_leftMargin+210,
// 	        $this->_yPosition);
	    
	    $this->_page->setlineColor(new ZendPdf\Color\Html('green'));
	    $this->_page->drawLine($this->_leftMargin,
	        $this->_yPosition-10,
	        $this->_pageWidth -
	        $this->_leftMargin,
	        $this->_yPosition-10);
	    
	    //-----------------------------------------------------------------------
	    //-----------------------------------------------------------------------
	    //-----------------------------------------------------------------------
	     
	    $this->_yPosition -= $noteLineHeight;
	
	    $control = new DateHelper();
	     
	    $resultats = $this->_resultatsAnalysesDemandees;
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 8);
	    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', 'Enregistre le: '),
	        $this->_leftMargin+5,
	        $this->_yPosition+10);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 8.5);
	    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $control->convertDateTime( $infosAnalyseDemande[68]['DateEnregistrementResultat'] )),
	        $this->_leftMargin+50,
	        $this->_yPosition+10);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 8);
	    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', 'validÃ© par: '),
	        $this->_leftMargin+135,
	        $this->_yPosition+10);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 9);
	    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', ''. $infosAnalyseDemande[68]['Prenom'] .' '.$infosAnalyseDemande[68]['Nom']),
	        $this->_leftMargin+173,
	        $this->_yPosition+10);
	     
	    $this->_yPosition -= $noteLineHeight-20; //Allez à la ligne
	     
	    $this->_page->setLineColor(new ZendPdf\Color\Html('#999999'));
	    $this->_page->setLineWidth(1);
	    $this->_page->drawLine($this->_leftMargin+190,
	        $this->_yPosition -3,
	        $this->_pageWidth -
	        $this->_leftMargin-165,
	        $this->_yPosition -3);
	     
	    $this->_page->setLineColor(new ZendPdf\Color\Html('#ffffff'));
	    $this->_page->setLineWidth(0.5);
	
	    $this->_page->drawLine($this->_leftMargin,
	        $this->_yPosition -2,
	        $this->_pageWidth -
	        $this->_leftMargin,
	        $this->_yPosition -2);
	     
	    $this->getNewTime();
	    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $Designation),
	        $this->_leftMargin+190,
	        $this->_yPosition);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_HELVETICA_OBLIQUE), 9);
	    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', 'DÃ©pistage nÃ©onatal'),
	        $this->_leftMargin+5,
	        $this->_yPosition);
	     
	    $this->_yPosition -= $noteLineHeight+15; //aller a la ligne suivante
	     
	    $this->_page->setLineColor(new ZendPdf\Color\Html('#eeeeee'));
	    $this->_page->setLineWidth(16);
	    $this->_page->drawLine($this->_leftMargin,
	        $this->_yPosition +9,
	        $this->_pageWidth -
	        $this->_leftMargin,
	        $this->_yPosition +9);
	
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10);
	    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "Technique utilisÃ©e: " ),
	        $this->_leftMargin+5,
	        $this->_yPosition+5);
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_ITALIC), 11);
	    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $resultats[68]['type_materiel'] ),
	        $this->_leftMargin+85,
	        $this->_yPosition+5);
	    
	    $this->_yPosition -= $noteLineHeight+25; //aller a la ligne suivante
	     
	    $this->_page->setLineColor(new ZendPdf\Color\Html('#e1e1e1'));
	    $this->_page->setLineWidth(19);
	    $this->_page->drawLine($this->_leftMargin,
	        $this->_yPosition +23,
	        $this->_pageWidth -
	        $this->_leftMargin,
	        $this->_yPosition +23);
	    
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 12);
	    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', 'Typage de l\'hÃ©moglobine: '),
	        $this->_leftMargin+85,
	        $this->_yPosition +18);
	    
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 13);
	    $this->_page->drawText($resultats[68]['valeur'],
	        $this->_leftMargin+220,
	        $this->_yPosition +18);
	    
	    
	    return $this->_page;
	}
	
	
}