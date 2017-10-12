<?php
namespace Secretariat\View\Helper;

use ZendPdf;
use ZendPdf\Page;
use ZendPdf\Font;

class ResultatsTypageHemoglobinePdf
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
	
	protected $_analysesImmunoHemato;
	protected $_analysesCytologie;
	protected $_analysesHemostase;
	protected $_analysesBilanHepatique;
	protected $_analysesBilanRenal;
	
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
		$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', 'République du Sénégal'),
				$this->_leftMargin,
				$this->_pageHeight - 50);
		$this->_page->setFont($this->_newTime, 10);
		$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', 'Université Gaston Berger / UFR 2S'),
				$this->_leftMargin,
				$this->_pageHeight - 65);
		$this->_page->setFont($this->_newTime, 10);
		$this->_page->drawText('Centre de Recherche et de Prise en Charge - ',
				$this->_leftMargin,
				$this->_pageHeight - 80);
		$this->_page->setFont($this->_newTime, 10);
		$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', 'Ambulatoire de la Drépanocytose (CERPAD)'),
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
	
	public function setPatient($patient){
		$this->_patient = $patient;
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

	public function setAnalysesImmunoHemato($analysesImmunoHemato){
		$this->_analysesImmunoHemato = $analysesImmunoHemato;
	}
	
	public function setAnalysesCytologie($analysesCytologie){
		$this->_analysesCytologie = $analysesCytologie;
	}
	
	public function setAnalysesHemostase($analysesHemostase){
		$this->_analysesHemostase = $analysesHemostase;
	}
	
	public function setAnalysesBilanHepatique($analysesBilanHepatique){
		$this->_analysesBilanHepatique = $analysesBilanHepatique;
	}
	
	public function setAnalysesBilanRenal($analysesBilanRenal){
		$this->_analysesBilanRenal = $analysesBilanRenal;
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
		$this->_page->drawText(iconv ('UTF-8' , 'ISO-8859-1' , ''.$this->_patient->numero_dossier),
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
		//-------- TYPAGE-HEMO --------- TYPAGE-HEMO ---------- TYPAGE-HEMO------
		//-------- TYPAGE-HEMO --------- TYPAGE-HEMO ---------- TYPAGE-HEMO------
		//-------- TYPAGE-HEMO --------- TYPAGE-HEMO ---------- TYPAGE-HEMO------
		//-----------------------------------------------------------------------
		//-----------------------------------------------------------------------
		if(in_array('DEPISTAGE', $typesAnalyses)){
			$this->_page = $this->typage_hemoglobine($noteLineHeight, $analyses[68], $infosAnalyseDemande, $value);
		}
			
		//-----------------------------------------------------------------
		//-----------------------------------------------------------------
		//-----------------------------------------------------------------
		//-----------------------------------------------------------------
		//-----------------------------------------------------------------
		//-----------------------------------------------------------------
		
			
		$this->_yPosition -= $noteLineHeight;
	} 
	
	public function getPiedPage(){
		
		$this->_page->setFont($this->_newTimeGras, 9);
		$this->_page->drawText('Cachet et signature',
				$this->_leftMargin+400,
				$this->_pageWidth - ( 140 + 320));
		
		$this->_page->setlineColor(new ZendPdf\Color\Html('green'));
		$this->_page->setLineWidth(1.5);
		$this->_page->drawLine($this->_leftMargin,
				50,
				$this->_pageWidth -
				$this->_leftMargin,
				50);
		
		$this->_page->setFont($this->_newTime, 10);
		$this->_page->drawText('Téléphone: 33 726 25 36   BP: 24000',
				$this->_leftMargin,
				$this->_pageWidth - ( 140 + 420),
				'UTF-8'
		);
		
		$this->_page->setFont($this->_newTime, 10);
		$this->_page->drawText('SIMENS+: ',
				$this->_leftMargin + 355,
				$this->_pageWidth - ( 140 + 420));
		$this->_page->setFont($this->_newTimeGras, 11);
		$this->_page->drawText('www.simens.sn',
				$this->_leftMargin + 405,
				$this->_pageWidth - ( 140 + 420));
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
	    //$this->_yPosition -= 15;
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
	    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', 'Enregistré le : '),
	        $this->_leftMargin+5,
	        $this->_yPosition+10);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 8.5);
	    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $control->convertDateTime( $infosAnalyseDemande[68]['DateEnregistrementResultat'] )),
	        $this->_leftMargin+50,
	        $this->_yPosition+10);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 8);
	    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', ' par : '),
	        $this->_leftMargin+135,
	        $this->_yPosition+10);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 9);
	    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', ''. $infosAnalyseDemande[68]['Prenom'] .' '.$infosAnalyseDemande[68]['Nom'].'  -  validé par : '.$infosAnalyseDemande[68]['PrenomValidateur'] .' '.$infosAnalyseDemande[68]['NomValidateur']),
	        $this->_leftMargin+155,
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
	    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', 'Dépistage néonatal'),
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
	    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "Technique utilisée: " ),
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
	    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', 'Typage de l\'hémoglobine: '),
	        $this->_leftMargin+85,
	        $this->_yPosition +18);
	    
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 13);
	    $this->_page->drawText( $resultats[68]['Designation_stat']."",
	        $this->_leftMargin+220,
	        $this->_yPosition +18,
	    		'UTF-8'
		);
	    
	    
	    return $this->_page;
	}
	
	
}