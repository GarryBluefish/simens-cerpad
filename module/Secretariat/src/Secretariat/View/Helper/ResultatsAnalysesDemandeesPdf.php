<?php
namespace Secretariat\View\Helper;

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
	
	protected $_analysesImmunoHemato;
	protected $_analysesCytologie;
	protected $_analysesHemostase;
	protected $_analysesMetabolismeGlucidique;
	protected $_analysesBilanLipidique;
	protected $_analysesBilanHepatique;
	protected $_analysesBilanRenal;
	protected $_analysesSerologie;
	protected $_analysesTypageHemoProteine;
	
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
	
	public function setAnalysesTypageHemoProteine($analysesTypageHemoProteine){
		$this->_analysesTypageHemoProteine = $analysesTypageHemoProteine;
	}
	
	public function setAnalysesMetabolismeGlucidique($analysesMetabolismeGlucidique){
		$this->_analysesMetabolismeGlucidique = $analysesMetabolismeGlucidique;
	}
	
	public function setAnalysesBilanLipidique($analysesBilanLipidique){
		$this->_analysesBilanLipidique = $analysesBilanLipidique;
	}
	
	public function setAnalysesBilanHepatique($analysesBilanHepatique){
		$this->_analysesBilanHepatique = $analysesBilanHepatique;
	}
	
	public function setAnalysesBilanRenal($analysesBilanRenal){
		$this->_analysesBilanRenal = $analysesBilanRenal;
	}
	
	public function setAnalysesSerologie($analysesSerologie){
		$this->_analysesSerologie = $analysesSerologie;
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
		$this->_page->setFont($this->_newTime, 10); //NÂ°: 
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
		//-----------------------------------------------
			
    	
    	$this->_page->setlineColor(new ZendPdf\Color\Html('green'));
    	$this->_page->drawLine($this->_leftMargin,
    			$this->_yPosition-10,
    			$this->_pageWidth -
    			$this->_leftMargin,
    			$this->_yPosition-10);
    	$this->_yPosition -= $noteLineHeight-10;
    	
    	
		//-----------------------------------------------------------------------
		//-----------------------------------------------------------------------
		//-----------------------------------------------------------------------
	    
		$analyses = array();
		$idAnalyses = array();
		$typesAnalyses = array();
		$infosAnalyseDemande = array();
			
		$listeAnalysesDemandees = $this->_analysesDemandees;
		
			//-----------------------------------------------------------------
			//-----------------------------------------------------------------
			//-----------------------------------------------------------------
			//-----------------------------------------------------------------
			//-----------------------------------------------------------------
			//-----------------------------------------------------------------
			

			//Liste des analyses pour l'IMMUNO-HEMATO
			//Liste des analyses pour l'IMMUNO-HEMATO
			$idanalysesImmunoHemato = $this->_analysesImmunoHemato;
			
			//Affichage des analyses concernant l'IMMUNO_HEMATO
			if(array_intersect(array(2,3,4,5,6), $idanalysesImmunoHemato)){
				$this->_page = $this->getImmunoHemato($noteLineHeight, $infosAnalyseDemande, $value, $idanalysesImmunoHemato);
			}
			//=======================================
			//=======================================
			
			
			//-----------------------------------------------------------------
			//-----------------------------------------------------------------
			//-----------------------------------------------------------------
			//-----------------------------------------------------------------
			//-----------------------------------------------------------------
			//-----------------------------------------------------------------
			
			//Liste des analyses pour la CYTOLOGIE
			//Liste des analyses pour la CYTOLOGIE
			$idanalysesCytologie = $this->_analysesCytologie;
				
			//Affichage des analyses concernant l'IMMUNO_HEMATO
			if(array_intersect(array(7,8,50), $idanalysesCytologie)){
				$this->_page = $this->getCytologie($noteLineHeight, $value, $idanalysesCytologie);
			}
			//====================================
			//====================================
			
			
			//-----------------------------------------------------------------
			//-----------------------------------------------------------------
			//-----------------------------------------------------------------
			//-----------------------------------------------------------------
			//-----------------------------------------------------------------
			//-----------------------------------------------------------------
			
			//Liste des analyses pour l'HEMOSTASE 
			//Liste des analyses pour l'HEMOSTASE
			$idanalysesHemostase = $this->_analysesHemostase;
			
			//Affichage des analyses concernant l'HEMOSTASE 
			if(array_intersect(array(14,15,16,17,18,19), $idanalysesHemostase)){
				$this->_page = $this->getHemostase($noteLineHeight, $idanalysesHemostase);
			}
			//===================================
			//===================================
			
			
			//-----------------------------------------------------------------
			//-----------------------------------------------------------------
			//-----------------------------------------------------------------
			//-----------------------------------------------------------------
			//-----------------------------------------------------------------
			//-----------------------------------------------------------------
			
			//Liste des analyses pour le BILAN HEPATIQUE
			//Liste des analyses pour le BILAN HEPATIQUE
			$idanalysesBilanHepatique = $this->_analysesBilanHepatique;
				
			//Affichage des analyses concernant le BILAN HEPATIQUE
			if(array_intersect(array(37,38,39,42), $idanalysesBilanHepatique)){
				$this->_page = $this->getBilanHepatique($noteLineHeight, $idanalysesBilanHepatique);
			}
			//=========================================
			//=========================================
			
			//-----------------------------------------------------------------
			//-----------------------------------------------------------------
			//-----------------------------------------------------------------
			//-----------------------------------------------------------------
			//-----------------------------------------------------------------
			//-----------------------------------------------------------------
			
			//Liste des analyses pour le BILAN RENAL
			//Liste des analyses pour le BILAN RENAL
			$idanalysesBilanRenal = $this->_analysesBilanRenal;
			
			//Affichage des analyses concernant le BILAN RENAL
			if(array_intersect(array(23,46), $idanalysesBilanRenal)){
				$this->_page = $this->getBilanRenal($noteLineHeight, $value, $idanalysesBilanRenal);
			}
			//=========================================
			//=========================================
			
			//-----------------------------------------------------------------
			//-----------------------------------------------------------------
			//-----------------------------------------------------------------
			//-----------------------------------------------------------------
			//-----------------------------------------------------------------
			//-----------------------------------------------------------------
 		
			
			//Liste des analyses pour la serologie
			//Liste des analyses pour la serologie
			$idanalysesSerologie = $this->_analysesSerologie;
			
			//Affichage des analyses concernant la Serologie
			if(array_intersect(array(53, 55, 60, 61), $idanalysesSerologie)){
				$this->_page = $this->getSerologie($noteLineHeight, $idanalysesSerologie);
			}
			//=========================================
			//=========================================
			
			//-----------------------------------------------------------------
			//-----------------------------------------------------------------
			//-----------------------------------------------------------------
			//-----------------------------------------------------------------
			//-----------------------------------------------------------------
			//-----------------------------------------------------------------
			
			//Liste des analyses pour le TYPAGE ELETROPHORESE DE HEMO-PROTEINE
			//Liste des analyses pour le TYPAGE ELETROPHORESE DE HEMO-PROTEINE
			$idanalysesTypageHemoProteine = $this->_analysesTypageHemoProteine;
				
			//Affichage des analyses concernant le TYPAGE ELETROPHORESE DE HEMO-PROTEINE
			if(array_intersect(array(44), $idanalysesTypageHemoProteine)){
				$this->_page = $this->getTypageHemoProteine($noteLineHeight, $infosAnalyseDemande, $value, $idanalysesTypageHemoProteine);
			}
			//===================================
			//===================================
				
			
			//-----------------------------------------------------------------
			//-----------------------------------------------------------------
			//-----------------------------------------------------------------
			//-----------------------------------------------------------------
			//-----------------------------------------------------------------
			//-----------------------------------------------------------------
			
			//Liste des analyses pour le METABOLISME GLUCIDIQUE
			//Liste des analyses pour le METABOLISME GLUCIDIQUE
			$idanalysesMetabolismeGlucidique = $this->_analysesMetabolismeGlucidique;
			
			//Affichage des analyses concernant le METABOLISME GLUCIDIQUE
			if(array_intersect(array(21,43), $idanalysesMetabolismeGlucidique)){
				$this->_page = $this->getMetabolismeGlucidique($noteLineHeight, $idanalysesMetabolismeGlucidique);
			}
			//===================================
			//===================================
			
				
			//-----------------------------------------------------------------
			//-----------------------------------------------------------------
			//-----------------------------------------------------------------
			//-----------------------------------------------------------------
			//-----------------------------------------------------------------
			//-----------------------------------------------------------------
						
			//Liste des analyses pour le BILAN LIPIDIQUE
			//Liste des analyses pour le BILAN LIPIDIQUE
			$idanalysesBilanLipidique = $this->_analysesBilanLipidique;
				
			//Affichage des analyses concernant le BILAN LIPIDIQUE
			if(array_intersect(array(25,26,27,28,29), $idanalysesBilanLipidique)){
				$this->_page = $this->getBilanLipidique($noteLineHeight, $infosAnalyseDemande, $value, $idanalysesBilanLipidique);
			}
			//===================================
			//===================================
				
			
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
				$this->_pageWidth - ( 140 + 360));
		
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
	
	//--- GESTION DE L'AFFICHAGE DES RESULTATS DES ANALYSES --- GESTION DE L'AFFICHAGE DES RESULTATS DES ANALYSES    
	//--- GESTION DE L'AFFICHAGE DES RESULTATS DES ANALYSES --- GESTION DE L'AFFICHAGE DES RESULTATS DES ANALYSES
	//--- GESTION DE L'AFFICHAGE DES RESULTATS DES ANALYSES --- GESTION DE L'AFFICHAGE DES RESULTATS DES ANALYSES
	
	public function getImmunoHemato($noteLineHeight, $infosAnalyseDemande, $value, $idanalysesImmunoHemato){
		$tabAnalyses = array_intersect(array(2,3,4,5,6), $idanalysesImmunoHemato);
		$tabAnalyses = array_reverse($tabAnalyses);
	
		$this->_page->setLineColor(new ZendPdf\Color\Html('#999999'));
		$this->_page->setLineWidth(1);
		$this->_page->drawLine($this->_leftMargin+182,
				$this->_yPosition -18,
				$this->_pageWidth -
				$this->_leftMargin-194,
				$this->_yPosition -18);
	
		$this->getNewTime();
		$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', 'IMMUNO-HEMATOLOGIE' ),
				$this->_leftMargin+182,
				$this->_yPosition- 15);
	
		//-------------PLACER LES LIGNES DES EXAMENS A AFFICHER ---------
		//-------------PLACER LES LIGNES DES EXAMENS A AFFICHER ---------
		//-------------PLACER LES LIGNES DES EXAMENS A AFFICHER ---------
		$j = 36;
	
		for($i = 0 ; $i < count($idanalysesImmunoHemato) ; $i++){
	
			if($i%2 == 0){
				$this->_page->setLineColor(new ZendPdf\Color\Html('#e1e1e1'));
				$this->_page->setLineWidth(17);
				$this->_page->drawLine($this->_leftMargin,
						$this->_yPosition -$j,
						$this->_pageWidth -
						$this->_leftMargin,
						$this->_yPosition -$j);
			}else{
				$this->_page->setLineColor(new ZendPdf\Color\Html('#f1f1f1'));
				$this->_page->setLineWidth(17);
				$this->_page->drawLine($this->_leftMargin,
						$this->_yPosition -$j,
						$this->_pageWidth -
						$this->_leftMargin,
						$this->_yPosition -$j);
			}
	
			$j += 19;
		}
	
		$j = 40;
		$resultats = $this->_resultatsAnalysesDemandees;

		if(in_array(2, $idanalysesImmunoHemato)){
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10);
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "GSRH / GROUPAGE RESHUS : " ),
					$this->_leftMargin+5,
					$this->_yPosition-$j);
					
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_ITALIC), 9);
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', " Groupe : " ),
					$this->_leftMargin+182,
					$this->_yPosition-$j);
	
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 11);
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $resultats[2]['groupe'] ),
					$this->_leftMargin+225,
					$this->_yPosition-$j);
	
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_ITALIC), 9);
			$this->_page->drawText(" Rhésus : " ,
					$this->_leftMargin+260,
					$this->_yPosition-$j);
	
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 11);
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $resultats[2]['rhesus'] ),
					$this->_leftMargin+300,
					$this->_yPosition-$j);
			
			$this->_yPosition -= $noteLineHeight-12;
			$j += 1;
		}
		
		if(in_array(3, $idanalysesImmunoHemato)){
			
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10);
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "RECHERCHE ANTIGENE D FAIBLE : " ),
					$this->_leftMargin+5,
					$this->_yPosition-$j);
				
			$rechAntDFaible = $resultats[3]['antigene_d_faible'];
			if($rechAntDFaible == 'Present'){ $rechAntDFaible = 'Présent'; }
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 11);
			$this->_page->drawText($rechAntDFaible,
					$this->_leftMargin+185,
					$this->_yPosition-$j);

			$this->_yPosition -= $noteLineHeight-12; //aller a la ligne suivante
			$j += 1;
		}
		
		if(in_array(4, $idanalysesImmunoHemato)){
				
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10);
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "TEST DE COOMBS DIRECT : " ),
					$this->_leftMargin+5,
					$this->_yPosition-$j);
		
			$testCoombsDirect = $resultats[4]['valeur'];
			if($testCoombsDirect == 'Negatif'){ $testCoombsDirect = 'Négatif'; }
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 11);
			$this->_page->drawText($testCoombsDirect,
					$this->_leftMargin+185,
					$this->_yPosition-$j);
		
			if($testCoombsDirect == 'Positif'){
				$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_ITALIC), 9);
				$this->_page->drawText(" Titre : " ,
						$this->_leftMargin+260,
						$this->_yPosition-$j);
					
				$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 11);
				$this->_page->drawText($resultats[4]['titre'],
						$this->_leftMargin+300,
						$this->_yPosition-$j);
			}
			
			$this->_yPosition -= $noteLineHeight-12; //aller a la ligne suivante
			$j += 1;
		}
	
		if(in_array(5, $idanalysesImmunoHemato)){
				
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10);
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "TEST DE COOMBS INDIRECT : " ),
					$this->_leftMargin+5,
					$this->_yPosition-$j);
				
			$testCoombsIndirect = $resultats[5]['valeur'];
			if($testCoombsIndirect == 'Negatif'){ $testCoombsIndirect = 'Négatif'; }
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 11);
			$this->_page->drawText($testCoombsIndirect ,
					$this->_leftMargin+185,
					$this->_yPosition-$j);
				
			if($testCoombsIndirect == 'Positif'){
				$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_ITALIC), 9);
				$this->_page->drawText(" Titre : " ,
						$this->_leftMargin+260,
						$this->_yPosition-$j);
		
				$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 11);
				$this->_page->drawText( $resultats[5]['titre'] ,
						$this->_leftMargin+300,
						$this->_yPosition-$j);
			}
		
			$this->_yPosition -= $noteLineHeight-12; //aller a la ligne suivante
			$j += 1;
		}
		
		if(in_array(6, $idanalysesImmunoHemato)){
		
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10);
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "TEST DE COMPATIBILITE : " ),
					$this->_leftMargin+5,
					$this->_yPosition-$j);
		
			$testCompatibilite = $resultats[6]['valeur'];
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 11);
			$this->_page->drawText($testCompatibilite ,
					$this->_leftMargin+185,
					$this->_yPosition-$j);
		
			
			if($testCompatibilite == 'Compatible'){
				$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_ITALIC), 9);
				$this->_page->drawText(" Poche : " ,
						$this->_leftMargin+260,
						$this->_yPosition-$j);
		
				$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 11);
				$this->_page->drawText( $resultats[6]['poche'] ,
						$this->_leftMargin+300,
						$this->_yPosition-$j);
			}
		
			$this->_yPosition -= $noteLineHeight-12; //aller a la ligne suivante
			$j += 1;
		}
		
		$this->_yPosition -= $noteLineHeight+20;
		return $this->_page;
	}
	
	public function getCytologie($noteLineHeight, $value, $idanalysesCytologie){
		
		$this->_yPosition -= $noteLineHeight-45; //Allez à la ligne
		
		$this->_page->setLineColor(new ZendPdf\Color\Html('#999999'));
		$this->_page->setLineWidth(1);
		$this->_page->drawLine($this->_leftMargin+210,
				$this->_yPosition -18,
				$this->_pageWidth -
				$this->_leftMargin-228,
				$this->_yPosition -18);
		
		$this->getNewTime();
		$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', 'CYTOLOGIE' ),
				$this->_leftMargin+210,
				$this->_yPosition- 15);
		
		//-------------PLACER LES LIGNES DES EXAMENS A AFFICHER ---------
		//-------------PLACER LES LIGNES DES EXAMENS A AFFICHER ---------
		//-------------PLACER LES LIGNES DES EXAMENS A AFFICHER ---------
		$j = 36;
		
		for($i = 0 ; $i < count($idanalysesCytologie) ; $i++){
		
			if($i%2 == 0){
				$this->_page->setLineColor(new ZendPdf\Color\Html('#e1e1e1'));
				$this->_page->setLineWidth(17);
				$this->_page->drawLine($this->_leftMargin,
						$this->_yPosition -$j,
						$this->_pageWidth -
						$this->_leftMargin,
						$this->_yPosition -$j);
			}else{
				$this->_page->setLineColor(new ZendPdf\Color\Html('#f1f1f1'));
				$this->_page->setLineWidth(17);
				$this->_page->drawLine($this->_leftMargin,
						$this->_yPosition -$j,
						$this->_pageWidth -
						$this->_leftMargin,
						$this->_yPosition -$j);
			}
		
			$j += 19;
		}
		
		$j = 40;
		$resultats = $this->_resultatsAnalysesDemandees;
		
		if(in_array(7, $idanalysesCytologie)){
		
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10);
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "VITESSE SE SEDIMENTATION (VS) : " ),
					$this->_leftMargin+5,
					$this->_yPosition-$j);
		
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 11);
			$this->_page->drawText( $resultats[7]['valeur'] ,
					$this->_leftMargin+185,
					$this->_yPosition-$j);
		
			$this->_yPosition -= $noteLineHeight-12; //aller a la ligne suivante
			$j += 1;
		}
		
		if(in_array(8, $idanalysesCytologie)){
		
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10);
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "TEST D'EMMEL (TE) : " ),
					$this->_leftMargin+5,
					$this->_yPosition-$j);
		
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 11);
			$this->_page->drawText( $resultats[8]['valeur'] ,
					$this->_leftMargin+185,
					$this->_yPosition-$j);
		
			$this->_yPosition -= $noteLineHeight-12; //aller a la ligne suivante
			$j += 1;
		}
		
		if(in_array(50, $idanalysesCytologie)){
		
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10);
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "HLM (COMPTE D'ADDIS) : " ),
					$this->_leftMargin+5,
					$this->_yPosition-$j);
			
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_ITALIC), 9);
			$this->_page->drawText(" Hématies : " ,
					$this->_leftMargin+180,
					$this->_yPosition-$j);
		
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 11);
			$this->_page->drawText( $resultats[50]['hematies_hlm'] ,
					$this->_leftMargin+235,
					$this->_yPosition-$j);
			
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_ITALIC), 9);
			$this->_page->drawText(" Leucocytes : " ,
					$this->_leftMargin+290,
					$this->_yPosition-$j);
			
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 11);
			$this->_page->drawText( $resultats[50]['leucocytes_hlm'] ,
					$this->_leftMargin+345,
					$this->_yPosition-$j);
		
			$this->_yPosition -= $noteLineHeight-12; //aller a la ligne suivante
			$j += 1;
		}
		
		$this->_yPosition -= $noteLineHeight+10;
		return $this->_page;
	}
	
	public function getTypageHemoProteine($noteLineHeight, $infosAnalyseDemande, $value, $idanalysesTypageHemoProteine){
		$tabAnalyses = array_intersect(array(44), $idanalysesTypageHemoProteine);
		$tabAnalyses= array_reverse($tabAnalyses);
		
		//-------------PLACER LES LIGNES DES EXAMENS A AFFICHER ---------
		//-------------PLACER LES LIGNES DES EXAMENS A AFFICHER ---------
		//-------------PLACER LES LIGNES DES EXAMENS A AFFICHER ---------
		$j = 30;
		for($i = 0 ; $i < count($idanalysesTypageHemoProteine) ; $i++){
		
			if($i%2 == 0){
				$this->_page->setLineColor(new ZendPdf\Color\Html('#e1e1e1'));
				$this->_page->setLineWidth(30);
				$this->_page->drawLine($this->_leftMargin,
						$this->_yPosition -$j,
						$this->_pageWidth -
						$this->_leftMargin,
						$this->_yPosition -$j);
			}else{
				$this->_page->setLineColor(new ZendPdf\Color\Html('#f1f1f1'));
				$this->_page->setLineWidth(30);
				$this->_page->drawLine($this->_leftMargin,
						$this->_yPosition -$j,
						$this->_pageWidth -
						$this->_leftMargin,
						$this->_yPosition -$j);
			}
		
			$j += 32;
		}
			
		//-----------------------------------------------------------------------
		//-----------------------------------------------------------------------
		//-----------------------------------------------------------------------
		
		$control = new DateHelper();
		
		$resultats = $this->_resultatsAnalysesDemandees;
		
		$this->_yPosition -= $noteLineHeight-25; //Allez à la ligne
		
		$this->_page->setLineColor(new ZendPdf\Color\Html('#999999'));
		$this->_page->setLineWidth(1);
		$this->_page->drawLine($this->_leftMargin+175,
				$this->_yPosition -2,
				$this->_pageWidth -
				$this->_leftMargin-183,
				$this->_yPosition -2);
		
		$this->getNewTime();
		$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "TYPAGE DE L'HEMOGLOBINE" ),
				$this->_leftMargin+175,
				$this->_yPosition+1);
		
		$this->_yPosition -= $noteLineHeight+5;    //aller a la ligne suivante
		
		
		if(in_array(44, $idanalysesTypageHemoProteine)){
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10);
			$this->_page->drawText("ELECTROPHORESE DE L'HEMOGLOBINE : ",
					$this->_leftMargin+5,
					$this->_yPosition+13);
		
			$indiceEmpl = 210;
			for($i = 0 ; $i < count($resultats[44]) ; $i++){
				
				$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 11);
				$this->_page->drawText($resultats[44][$i]['libelle'],
						$this->_leftMargin+$indiceEmpl,
						$this->_yPosition+13);
				
				$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 11);
				$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $resultats[44][$i]['valeur'] ),
						$this->_leftMargin+$indiceEmpl+30,
						$this->_yPosition+13);
				
				$indiceEmpl += 70;
				
			}
			
			
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10);
			$this->_page->drawText("conclusion : ". iconv ( 'UTF-8', 'ISO-8859-1', ' '.$resultats[44][0]['conclusion']),
					$this->_leftMargin+150,
					$this->_yPosition);
			
			$this->_yPosition -= $noteLineHeight-12; //aller a la ligne suivante
		}
		
		/*
		if(in_array(45, $idanalysesTypageHemoProteine)){
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10);
			$this->_page->drawText("ELECTROPHORESE DES PROTEINES : ",
					$this->_leftMargin+5,
					$this->_yPosition+9);
				
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_ITALIC), 10);
			$this->_page->drawText("Albumine : ",
					$this->_leftMargin+190,
					$this->_yPosition+9);
			
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 11);
			$this->_page->drawText($resultats[45]['albumine'].' %',
					$this->_leftMargin+240,
					$this->_yPosition+9);
		
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 11);
			$this->_page->drawText($resultats[45]['albumine_abs'],
					$this->_leftMargin+280,
					$this->_yPosition+9);
		
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_ITALIC), 10);
			$this->_page->drawText("Alpha 1 : ",
					$this->_leftMargin+320,
					$this->_yPosition+9);
			
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 11);
			$this->_page->drawText($resultats[45]['alpha_1'].' %',
					$this->_leftMargin+370,
					$this->_yPosition+9);
			
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 11);
			$this->_page->drawText($resultats[45]['alpha_1_abs'],
					$this->_leftMargin+410,
					$this->_yPosition+9);
			
			
			$this->_yPosition -= $noteLineHeight-12; //aller a la ligne suivante
		}
		*/
		
		
		return $this->_page;
	}
	
	
	public function getMetabolismeGlucidique($noteLineHeight, $idanalysesMetabolismeGlucidique){
		//-------------PLACER LES LIGNES DES EXAMENS A AFFICHER ---------
		//-------------PLACER LES LIGNES DES EXAMENS A AFFICHER ---------
		//-------------PLACER LES LIGNES DES EXAMENS A AFFICHER ---------
		$j = 28;
		for($i = 0 ; $i < count($idanalysesMetabolismeGlucidique) ; $i++){
				
			if($i%2 == 0){
				$this->_page->setLineColor(new ZendPdf\Color\Html('#e1e1e1'));
				$this->_page->setLineWidth(17);
				$this->_page->drawLine($this->_leftMargin,
						$this->_yPosition -$j,
						$this->_pageWidth -
						$this->_leftMargin,
						$this->_yPosition -$j);
			}else{
				$this->_page->setLineColor(new ZendPdf\Color\Html('#f1f1f1'));
				$this->_page->setLineWidth(17);
				$this->_page->drawLine($this->_leftMargin,
						$this->_yPosition -$j,
						$this->_pageWidth -
						$this->_leftMargin,
						$this->_yPosition -$j);
			}
		
			$j += 19;
		}
			
		//-----------------------------------------------------------------------
		//-----------------------------------------------------------------------
		//-----------------------------------------------------------------------
		
		$control = new DateHelper();
		
		$resultats = $this->_resultatsAnalysesDemandees;
		
		$this->_yPosition -= $noteLineHeight-20; //Allez à la ligne
		
		$this->_page->setLineColor(new ZendPdf\Color\Html('#999999'));
		$this->_page->setLineWidth(1);
		$this->_page->drawLine($this->_leftMargin+175,
				$this->_yPosition -2,
				$this->_pageWidth -
				$this->_leftMargin-183,
				$this->_yPosition -2);
		
		$this->getNewTime();
		$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', 'METABOLISME GLUCIDIQUE' ),
				$this->_leftMargin+175,
				$this->_yPosition+1);
		
		$this->_yPosition -= $noteLineHeight;    //aller a la ligne suivante
		
		//--- GLYCEMIE  --- GLYCEMIE --- GLYCEMIE --- GLYCEMIE ---
		//--- GLYCEMIE  --- GLYCEMIE --- GLYCEMIE --- GLYCEMIE ---
		if(in_array(21, $idanalysesMetabolismeGlucidique)){
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10);
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "GLYCEMIE : " ),
					$this->_leftMargin+5,
					$this->_yPosition+9);
				
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 11);
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $resultats[21]['glycemie_1'] ),
					$this->_leftMargin+110,
					$this->_yPosition+9);
			
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_ITALIC), 9);
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "g/l " ),
					$this->_leftMargin+145,
					$this->_yPosition+9);
				
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 11);
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $resultats[21]['glycemie_2'] ),
					$this->_leftMargin+200,
					$this->_yPosition+9);
				
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_ITALIC), 9);
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "mmol/l " ),
					$this->_leftMargin+235,
					$this->_yPosition+9);
			
			$this->_yPosition -= $noteLineHeight-12; //aller a la ligne suivante
		}
		
		//--- Hemoglobine glyquee  --- Hemoglobine glyquee --- Hemoglobine glyquee
		//--- Hemoglobine glyquee  --- Hemoglobine glyquee --- Hemoglobine glyquee
		if(in_array(43, $idanalysesMetabolismeGlucidique)){
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10);
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "HEMOGLOBINE GLYQUEE HbA1c : " ),
					$this->_leftMargin+5,
					$this->_yPosition+9);
		
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 11);
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $resultats[43]['hemoglobine_glyquee_hbac'] ),
					$this->_leftMargin+200,
					$this->_yPosition+9);
				
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_ITALIC), 9);
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "% " ),
					$this->_leftMargin+220,
					$this->_yPosition+9);
		
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 11);
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $resultats[43]['hemoglobine_glyquee_hbac_mmol'] ),
					$this->_leftMargin+290,
					$this->_yPosition+9);
		
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_ITALIC), 9);
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "mmol " ),
					$this->_leftMargin+315,
					$this->_yPosition+9);
				
			$this->_yPosition -= $noteLineHeight-12; //aller a la ligne suivante
		}
		
		
		return $this->_page;
	}
	
	public function getBilanLipidique($noteLineHeight, $infosAnalyseDemande, $value, $idanalysesBilanLipidique){

		$tabAnalyses = array_intersect(array(25,26,27,28,29), $idanalysesBilanLipidique);
		$tabAnalyses= array_reverse($tabAnalyses);
		
		//-------------PLACER LES LIGNES DES EXAMENS A AFFICHER ---------
		//-------------PLACER LES LIGNES DES EXAMENS A AFFICHER ---------
		//-------------PLACER LES LIGNES DES EXAMENS A AFFICHER ---------
		$j = 30;
		for($i = 0 ; $i < count($idanalysesBilanLipidique) ; $i++){
		
			if($i%2 == 0){
				$this->_page->setLineColor(new ZendPdf\Color\Html('#e1e1e1'));
				$this->_page->setLineWidth(30);
				$this->_page->drawLine($this->_leftMargin,
						$this->_yPosition -$j,
						$this->_pageWidth -
						$this->_leftMargin,
						$this->_yPosition -$j);
			}else{
				$this->_page->setLineColor(new ZendPdf\Color\Html('#f1f1f1'));
				$this->_page->setLineWidth(30);
				$this->_page->drawLine($this->_leftMargin,
						$this->_yPosition -$j,
						$this->_pageWidth -
						$this->_leftMargin,
						$this->_yPosition -$j);
			}
		
			$j += 32;
		}
			
		//--- CHOLESTEROL TOTAL & HDL  --- CHOLESTEROL TOTAL & HDL --- CHOLESTEROL TOTAL & HDL
		//--- CHOLESTEROL TOTAL & HDL  --- CHOLESTEROL TOTAL & HDL --- CHOLESTEROL TOTAL & HDL
		if(in_array(25, $idanalysesBilanLipidique) && in_array(27, $idanalysesBilanLipidique)){
			$j -= 6;
			if($i%2 == 0){
				$this->_page->setLineColor(new ZendPdf\Color\Html('#e1e1e1'));
				$this->_page->setLineWidth(17);
				$this->_page->drawLine($this->_leftMargin,
						$this->_yPosition -$j,
						$this->_pageWidth -
						$this->_leftMargin,
						$this->_yPosition -$j);
			}else{
				$this->_page->setLineColor(new ZendPdf\Color\Html('#f1f1f1'));
				$this->_page->setLineWidth(17);
				$this->_page->drawLine($this->_leftMargin,
						$this->_yPosition -$j,
						$this->_pageWidth -
						$this->_leftMargin,
						$this->_yPosition -$j);
			}
		}
		
		//-----------------------------------------------------------------------
		//-----------------------------------------------------------------------
		//-----------------------------------------------------------------------
		
		$control = new DateHelper();
		
		$resultats = $this->_resultatsAnalysesDemandees;
		
		$this->_yPosition -= $noteLineHeight-25; //Allez à la ligne
		
		$this->_page->setLineColor(new ZendPdf\Color\Html('#999999'));
		$this->_page->setLineWidth(1);
		$this->_page->drawLine($this->_leftMargin+202,
				$this->_yPosition -2,
				$this->_pageWidth -
				$this->_leftMargin-210,
				$this->_yPosition -2);
		
		$this->getNewTime();
		$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', 'BILAN LIPIDIQUE' ),
				$this->_leftMargin+202,
				$this->_yPosition+1);
		
		$this->_yPosition -= $noteLineHeight+5;    //aller a la ligne suivante
		
		//--- CHOLESTEROL TOTAL  --- CHOLESTEROL TOTAL --- CHOLESTEROL TOTAL
		//--- CHOLESTEROL TOTAL  --- CHOLESTEROL TOTAL --- CHOLESTEROL TOTAL
		if(in_array(25, $idanalysesBilanLipidique)){
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10);
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "CHOLESTEROL TOTAL : " ),
					$this->_leftMargin+5,
					$this->_yPosition+9);
		
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 11);
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $resultats[25]['cholesterol_total_1'] ),
					$this->_leftMargin+130,
					$this->_yPosition+9);
				
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_ITALIC), 9);
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "g/l " ),
					$this->_leftMargin+160,
					$this->_yPosition+9);
		
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 11);
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $resultats[25]['cholesterol_total_2'] ),
					$this->_leftMargin+195,
					$this->_yPosition+9);
		
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_ITALIC), 9);
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "mmol/l " ),
					$this->_leftMargin+225,
					$this->_yPosition+9);
				
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_ITALIC), 7.2);
			$this->_page->drawText("Moins de 30 ans <1, 80 (4, 7 mmol/l) - Plus de 30 ans < 2, 00 (< 5, 2mmol/l) ",
					$this->_leftMargin+270,
					$this->_yPosition+17);
			$this->_page->drawText( "Interprétation clinique : suspect supérieur à 2, 20 (5, 7 mmol/l) ",
					$this->_leftMargin+270,
					$this->_yPosition+8);
			$this->_page->drawText( "Risque élevé supérieur à 2, 60 (6, 7 mmol/l) ",
					$this->_leftMargin+270,
					$this->_yPosition-1);
			
			
			
			$this->_yPosition -= $noteLineHeight+2; //aller a la ligne suivante
		}
		
		//--- CHOLESTEROL HDL  --- CHOLESTEROL HDL --- CHOLESTEROL HDL
		//--- CHOLESTEROL HDL  --- CHOLESTEROL HDL --- CHOLESTEROL HDL
		if(in_array(27, $idanalysesBilanLipidique)){
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10);
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "CHOLESTEROL HDL : " ),
					$this->_leftMargin+5,
					$this->_yPosition+9);
		
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 11);
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $resultats[27]['cholesterol_HDL_1'] ),
					$this->_leftMargin+130,
					$this->_yPosition+9);
		
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_ITALIC), 9);
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "g/l " ),
					$this->_leftMargin+160,
					$this->_yPosition+9);
		
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 11);
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $resultats[27]['cholesterol_HDL_2'] ),
					$this->_leftMargin+195,
					$this->_yPosition+9);
		
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_ITALIC), 9);
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "mmol/l " ),
					$this->_leftMargin+225,
					$this->_yPosition+9);
		
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_ITALIC), 7.2);
			$this->_page->drawText("N: < 0, 35 ( < 0, 9 mmol/l) facteur de risque pour coronaropathies ",
					$this->_leftMargin+270,
					$this->_yPosition+16);
			$this->_page->drawText( "N: > 0, 60 ( > 1, 5 mmol/l) risque réduit pour coronaropathies ",
					$this->_leftMargin+270,
					$this->_yPosition+6);
			
			$this->_yPosition -= $noteLineHeight+2; //aller a la ligne suivante
		}
		
		//--- CHOLESTEROL LDL  --- CHOLESTEROL LDL --- CHOLESTEROL LDL
		//--- CHOLESTEROL LDL  --- CHOLESTEROL LDL --- CHOLESTEROL LDL
		if(in_array(28, $idanalysesBilanLipidique)){
			$this->_yPosition -= 2;
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10);
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "CHOLESTEROL LDL : " ),
					$this->_leftMargin+5,
					$this->_yPosition+9);
		
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 11);
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $resultats[28]['cholesterol_LDL_1'] ),
					$this->_leftMargin+130,
					$this->_yPosition+9);
		
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_ITALIC), 9);
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "g/l " ),
					$this->_leftMargin+160,
					$this->_yPosition+9);
		
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 11);
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $resultats[28]['cholesterol_LDL_2'] ),
					$this->_leftMargin+195,
					$this->_yPosition+9);
		
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_ITALIC), 9);
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "mmol/l " ),
					$this->_leftMargin+225,
					$this->_yPosition+9);
			
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_ITALIC), 7.2);
			$this->_page->drawText("N: H < 0,50 (< 1,3 mmol/l); F: < 0,63 (< 1,6 mmol/l) risque réduit pour cor.. ",
					$this->_leftMargin+270,
					$this->_yPosition+18);
			$this->_page->drawText("N: H > 1,72 (> 4,5 mmol/l); F: > 1,67 (4,3 mmol/l) risque accru pour cor.. ",
					$this->_leftMargin+270,
					$this->_yPosition+8);
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD_ITALIC), 7.2);
			$this->_page->drawText("(cor = coronaropathies) ",
					$this->_leftMargin+270,
					$this->_yPosition);
		
			$this->_yPosition -= $noteLineHeight+2; //aller a la ligne suivante
		}
		
		//--- TRIGLYCERIDES  --- TRIGLYCERIDES --- TRIGLYCERIDES
		//--- TRIGLYCERIDES  --- TRIGLYCERIDES --- TRIGLYCERIDES
		if(in_array(26, $idanalysesBilanLipidique)){
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10);
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "TRIGLYCERIDES : " ),
					$this->_leftMargin+5,
					$this->_yPosition+9);
		
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 11);
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $resultats[26]['triglycerides_1'] ),
					$this->_leftMargin+130,
					$this->_yPosition+9);
		
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_ITALIC), 9);
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "g/l " ),
					$this->_leftMargin+160,
					$this->_yPosition+9);
		
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 11);
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $resultats[26]['triglycerides_2'] ),
					$this->_leftMargin+195,
					$this->_yPosition+9);
		
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_ITALIC), 9);
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "mmol/l " ),
					$this->_leftMargin+225,
					$this->_yPosition+9);
			
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 7.2);
			$this->_page->drawText("Suspect supérieur à 1,50 (1,71 mmol/l) ",
					$this->_leftMargin+270,
					$this->_yPosition+18);
			$this->_page->setLineColor(new ZendPdf\Color\Html('#000000'));
			$this->_page->setLineWidth(0.5);
			$this->_page->drawLine($this->_leftMargin+270,
					$this->_yPosition +17,
					$this->_pageWidth -
					$this->_leftMargin-107,
					$this->_yPosition +17);
			
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_ITALIC), 7.2);
			$this->_page->drawText("Interprétation clinique pour risque d'athéroclérose ",
					$this->_leftMargin+270,
					$this->_yPosition+8);
			$this->_page->drawText("Risque accru supérieur à 2,00 (2,28 mmol/l)",
					$this->_leftMargin+270,
					$this->_yPosition);
		
			$this->_yPosition -= $noteLineHeight-4; //aller a la ligne suivante
		}
		
		//--- CHOLESTEROL TOTAL & HDL  --- CHOLESTEROL TOTAL & HDL --- CHOLESTEROL TOTAL & HDL
		//--- CHOLESTEROL TOTAL & HDL  --- CHOLESTEROL TOTAL & HDL --- CHOLESTEROL TOTAL & HDL
		if(in_array(25, $idanalysesBilanLipidique) && in_array(27, $idanalysesBilanLipidique)){
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10);
			$this->_page->drawText("» Rapport: CHOL/HDL : ",
					$this->_leftMargin+5,
					$this->_yPosition+9);
			
			$rapportCHOL = $resultats[25]['cholesterol_total_1']/$resultats[27]['cholesterol_HDL_1'];
			
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 11);
			$this->_page->drawText(number_format($rapportCHOL,2),
					$this->_leftMargin+130,
					$this->_yPosition+9);
			
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_ITALIC), 9);
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "N:< 4,5" ),
					$this->_leftMargin+195,
					$this->_yPosition+9);
			
			//Affichage de la conclusion du rapport
			if($rapportCHOL >= 4.5 && $rapportCHOL <= 5){
				$conclusion_rapport_chol_hdl = "Risque d'athérogène faible";
			}else if($rapportCHOL > 5 && $rapportCHOL <= 6.5){
				$conclusion_rapport_chol_hdl = "Risque d'athérogène modéré";
			}else if($rapportCHOL > 6.5){
				$conclusion_rapport_chol_hdl = "Risque d'athérogène élevé";
			}else{
				$conclusion_rapport_chol_hdl = "RAS";
			}
			
			
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 9);
			$this->_page->drawText("Conclusion :",
					$this->_leftMargin+270,
					$this->_yPosition+9);
			$this->_page->setLineColor(new ZendPdf\Color\Html('#000000'));
			$this->_page->setLineWidth(0.5);
			$this->_page->drawLine($this->_leftMargin+270,
					$this->_yPosition +8,
					$this->_pageWidth -
					$this->_leftMargin-177,
					$this->_yPosition +8);
			
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 11);
			$this->_page->drawText($conclusion_rapport_chol_hdl,
					$this->_leftMargin+330,
					$this->_yPosition+8);
			
			$this->_yPosition -= $noteLineHeight+2; //aller a la ligne suivante
		}
		
		return $this->_page;
	}
	
	
	public function getHemostase($noteLineHeight, $idanalysesHemostase){
	
		//-------------PLACER LES LIGNES DES EXAMENS A AFFICHER ---------
		//-------------PLACER LES LIGNES DES EXAMENS A AFFICHER ---------
		//-------------PLACER LES LIGNES DES EXAMENS A AFFICHER ---------
		$j = 28;
		for($i = 0 ; $i < count($idanalysesHemostase) ; $i++){
				
			if($i%2 == 0){
				$this->_page->setLineColor(new ZendPdf\Color\Html('#e1e1e1'));
				$this->_page->setLineWidth(17);
				$this->_page->drawLine($this->_leftMargin,
						$this->_yPosition -$j,
						$this->_pageWidth -
						$this->_leftMargin,
						$this->_yPosition -$j);
			}else{
				$this->_page->setLineColor(new ZendPdf\Color\Html('#f1f1f1'));
				$this->_page->setLineWidth(17);
				$this->_page->drawLine($this->_leftMargin,
						$this->_yPosition -$j,
						$this->_pageWidth -
						$this->_leftMargin,
						$this->_yPosition -$j);
			}
	
			$j += 19;
		}
			
		//-----------------------------------------------------------------------
		//-----------------------------------------------------------------------
		//-----------------------------------------------------------------------
	
		$control = new DateHelper();
	
		$resultats = $this->_resultatsAnalysesDemandees;
	
		$this->_yPosition -= $noteLineHeight-20; //Allez à la ligne
	
		$this->_page->setLineColor(new ZendPdf\Color\Html('#999999'));
		$this->_page->setLineWidth(1);
		$this->_page->drawLine($this->_leftMargin+210,
				$this->_yPosition -2,
				$this->_pageWidth -
				$this->_leftMargin-224,
				$this->_yPosition -2);
	
		$this->getNewTime();
		$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', 'HEMOSTASE' ),
				$this->_leftMargin+210,
				$this->_yPosition+1);
	
		$this->_yPosition -= $noteLineHeight;    //aller a la ligne suivante
	
		//--- TP-INR  ---  TP-INR ---  TP-INR  ---  TP-INR  ---
		//--- TP-INR  ---  TP-INR ---  TP-INR  ---  TP-INR  ---
		if(in_array(14, $idanalysesHemostase)){
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10);
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "TP - INR : " ),
					$this->_leftMargin+5,
					$this->_yPosition+9);
				
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_ITALIC), 8);
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "temps quick temoin : " ),
					$this->_leftMargin+80,
					$this->_yPosition+9);
				
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 11);
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $resultats[14]['temps_quick_temoin'] ),
					$this->_leftMargin+150,
					$this->_yPosition+9);
				
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_ITALIC), 8);
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "temps quick patient : " ),
					$this->_leftMargin+180,
					$this->_yPosition+9);
				
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 11);
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $resultats[14]['temps_quick_patient'] ),
					$this->_leftMargin+250,
					$this->_yPosition+9);
				
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_ITALIC), 8);
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "taux prothrombine patient : " ),
					$this->_leftMargin+310,
					$this->_yPosition+9);
				
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 11);
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $resultats[14]['taux_prothrombine_patient'] ),
					$this->_leftMargin+400,
					$this->_yPosition+9);
				
			$this->_yPosition -= $noteLineHeight-12; //aller a la ligne suivante
		}
	
		//--- TCA  ---  TCA ---  TCA  ---  TCA  ---
		//--- TCA  ---  TCA ---  TCA  ---  TCA  ---
		if(in_array(15, $idanalysesHemostase)){
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10);
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "TCA : " ),
					$this->_leftMargin+5,
					$this->_yPosition+8);
				
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_ITALIC), 8);
			$this->_page->drawText("patient: ",
					$this->_leftMargin+80,
					$this->_yPosition+8);
				
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 11);
			$this->_page->drawText($resultats[15]['tca_patient'],
					$this->_leftMargin+120,
					$this->_yPosition+8);
				
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_ITALIC), 8);
			$this->_page->drawText("témoin : ",
					$this->_leftMargin+180,
					$this->_yPosition+8);
				
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 11);
			$this->_page->drawText($resultats[15]['temoin_patient'],
					$this->_leftMargin+220,
					$this->_yPosition+8);
				
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_ITALIC), 8);
			$this->_page->drawText("ratio : ",
					$this->_leftMargin+280,
					$this->_yPosition+8);
				
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 11);
			$this->_page->drawText(round($resultats[15]['tca_patient']/$resultats[15]['temoin_patient'], 3, PHP_ROUND_HALF_ODD) ,
					$this->_leftMargin+310,
					$this->_yPosition+8);
				
			$this->_yPosition -= $noteLineHeight-12; //aller a la ligne suivante
				
		}
	
		//--- FIBRINEMIE  ---  FIBRINEMIE ---  FIBRINEMIE  ---  FIBRINEMIE  ---
		//--- FIBRINEMIE  ---  FIBRINEMIE ---  FIBRINEMIE  ---  FIBRINEMIE  ---
		if(in_array(16, $idanalysesHemostase)){
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10);
			$this->_page->drawText("FIBRINEMIE : ",
					$this->_leftMargin+5,
					$this->_yPosition+6);
	
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 11);
			$this->_page->drawText($resultats[16]['fibrinemie'],
					$this->_leftMargin+100,
					$this->_yPosition+6);
	
			$this->_yPosition -= $noteLineHeight-12; //aller a la ligne suivante
	
		}
			
		//--- TEMPS DE SAIGNEMENT  ---  TEMPS DE SAIGNEMENT ---  TEMPS DE SAIGNEMENT
		//--- TEMPS DE SAIGNEMENT  ---  TEMPS DE SAIGNEMENT ---  TEMPS DE SAIGNEMENT
		if(in_array(17, $idanalysesHemostase)){
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10);
			$this->_page->drawText("TEMPS DE SAIGNEMENT : ",
					$this->_leftMargin+5,
					$this->_yPosition+6);
	
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 11);
			$this->_page->drawText($resultats[17]['temps_saignement'],
					$this->_leftMargin+150,
					$this->_yPosition+6);
	
			$this->_yPosition -= $noteLineHeight-12; //aller a la ligne suivante
	
		}
	
		//FACTEUR 8  ---  FACTEUR 8  ---  FACTEUR 8  ---  FACTEUR 8
		//FACTEUR 8  ---  FACTEUR 8  ---  FACTEUR 8  ---  FACTEUR 8
		if(in_array(18, $idanalysesHemostase)){
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10);
			$this->_page->drawText("FACTEUR VIII : ",
					$this->_leftMargin+5,
					$this->_yPosition+6);
	
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 11);
			$this->_page->drawText($resultats[18]['facteur_8'],
					$this->_leftMargin+100,
					$this->_yPosition+6);
	
			$this->_yPosition -= $noteLineHeight-12; //aller a la ligne suivante
	
		}
	
		//FACTEUR 9  ---  FACTEUR 9  ---  FACTEUR 9  ---  FACTEUR 9
		//FACTEUR 9  ---  FACTEUR 9  ---  FACTEUR 9  ---  FACTEUR 9
		if(in_array(19, $idanalysesHemostase)){
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10);
			$this->_page->drawText("FACTEUR IX : ",
					$this->_leftMargin+5,
					$this->_yPosition+5);
	
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 11);
			$this->_page->drawText($resultats[19]['facteur_9'],
					$this->_leftMargin+100,
					$this->_yPosition+5);
	
			$this->_yPosition -= $noteLineHeight-12; //aller a la ligne suivante
	
		}
	
		//D-DIMERES  ---  D-DIMERES  ---  D-DIMERES  ---  D-DIMERES
		//D-DIMERES  ---  D-DIMERES  ---  D-DIMERES  ---  D-DIMERES
		if(in_array(20, $idanalysesHemostase)){
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10);
			$this->_page->drawText("D-DIMERES : ",
					$this->_leftMargin+5,
					$this->_yPosition+5);
	
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 11);
			$this->_page->drawText($resultats[20]['d_dimeres'],
					$this->_leftMargin+100,
					$this->_yPosition+5);
				
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_ITALIC), 8);
			$this->_page->drawText("ug/ml ",
					$this->_leftMargin+120,
					$this->_yPosition+5);
				
			$this->_yPosition -= $noteLineHeight-12; //aller a la ligne suivante
	
		}
			
		return $this->_page;
	}

	public function getBilanHepatique($noteLineHeight, $idanalysesBilanHepatique){
		//-------------PLACER LES LIGNES DES EXAMENS A AFFICHER ---------
		//-------------PLACER LES LIGNES DES EXAMENS A AFFICHER ---------
		//-------------PLACER LES LIGNES DES EXAMENS A AFFICHER ---------
		$j = 28;
		for($i = 0 ; $i < count($idanalysesBilanHepatique) ; $i++){
	
			if($i%2 == 0){
				$this->_page->setLineColor(new ZendPdf\Color\Html('#e1e1e1'));
				$this->_page->setLineWidth(17);
				$this->_page->drawLine($this->_leftMargin,
						$this->_yPosition -$j,
						$this->_pageWidth -
						$this->_leftMargin,
						$this->_yPosition -$j);
			}else{
				$this->_page->setLineColor(new ZendPdf\Color\Html('#f1f1f1'));
				$this->_page->setLineWidth(17);
				$this->_page->drawLine($this->_leftMargin,
						$this->_yPosition -$j,
						$this->_pageWidth -
						$this->_leftMargin,
						$this->_yPosition -$j);
			}
	
			$j += 19;
		}
			
		//-----------------------------------------------------------------------
		//-----------------------------------------------------------------------
		//-----------------------------------------------------------------------
	
		$control = new DateHelper();
	
		$resultats = $this->_resultatsAnalysesDemandees;
	
		$this->_yPosition -= $noteLineHeight-20; //Allez à la ligne
	
		$this->_page->setLineColor(new ZendPdf\Color\Html('#999999'));
		$this->_page->setLineWidth(1);
		$this->_page->drawLine($this->_leftMargin+198,
				$this->_yPosition -2,
				$this->_pageWidth -
				$this->_leftMargin-207,
				$this->_yPosition -2);
	
		$this->getNewTime();
		$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', 'BILAN HEPATIQUE' ),
				$this->_leftMargin+198,
				$this->_yPosition+1);
	
		$this->_yPosition -= $noteLineHeight;    //aller a la ligne suivante
	
	
	
		if(in_array(37, $idanalysesBilanHepatique)){
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10);
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "TRANSAMINASES : " ),
					$this->_leftMargin+5,
					$this->_yPosition+9);
	
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_ITALIC), 9);
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', " TGO/ASAT : " ),
					$this->_leftMargin+120,
					$this->_yPosition+9);
	
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 11);
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $resultats[37][1]['tgo_asat'] ),
					$this->_leftMargin+180,
					$this->_yPosition+9);
	
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_ITALIC), 9);
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', " TGP/ALAT : " ),
					$this->_leftMargin+240,
					$this->_yPosition+9);
	
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 11);
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $resultats[37][2]['tgp_alat'] ),
					$this->_leftMargin+300,
					$this->_yPosition+9);
	
			$this->_yPosition -= $noteLineHeight-12; //aller a la ligne suivante
		}
	
		if(in_array(38, $idanalysesBilanHepatique)){
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10);
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "PHOSPHATAGE ALCALINE (PAL) : " ),
					$this->_leftMargin+5,
					$this->_yPosition+9);
	
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 11);
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $resultats[38]['valeur'] ),
					$this->_leftMargin+180,
					$this->_yPosition+9);
	
			$this->_yPosition -= $noteLineHeight-12; //aller a la ligne suivante
		}
	
		if(in_array(39, $idanalysesBilanHepatique)){
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10);
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "GAMA GT = YGT : " ),
					$this->_leftMargin+5,
					$this->_yPosition+9);
	
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 11);
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $resultats[39]['valeur'] ),
					$this->_leftMargin+180,
					$this->_yPosition+9);
	
			$this->_yPosition -= $noteLineHeight-12; //aller a la ligne suivante
		}
	
		if(in_array(42, $idanalysesBilanHepatique)){
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10);
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "BILIRUBINE TOTALE ET DIRECTE : " ),
					$this->_leftMargin+5,
					$this->_yPosition+9);
	
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_ITALIC), 9);
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', " mg : " ),
					$this->_leftMargin+180,
					$this->_yPosition+9);
				
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 11);
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $resultats[42]['bilirubine_totale_mg'] ),
					$this->_leftMargin+200,
					$this->_yPosition+9);
				
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_ITALIC), 9);
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', " umol : " ),
					$this->_leftMargin+240,
					$this->_yPosition+9);
				
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 11);
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $resultats[42]['bilirubine_totale_umol'] ),
					$this->_leftMargin+270,
					$this->_yPosition+9);
				
				
			$this->_yPosition -= $noteLineHeight-12; //aller a la ligne suivante
		}
	
		return $this->_page;
	}
	
	
	public function getBilanRenal($noteLineHeight, $value, $idanalysesBilanRenal){
		
		$j = 28;
		for($i = 0 ; $i < count($idanalysesBilanRenal) ; $i++){
		
			if($i%2 == 0){
				$this->_page->setLineColor(new ZendPdf\Color\Html('#e1e1e1'));
				$this->_page->setLineWidth(17);
				$this->_page->drawLine($this->_leftMargin,
						$this->_yPosition -$j,
						$this->_pageWidth -
						$this->_leftMargin,
						$this->_yPosition -$j);
			}else{
				$this->_page->setLineColor(new ZendPdf\Color\Html('#f1f1f1'));
				$this->_page->setLineWidth(17);
				$this->_page->drawLine($this->_leftMargin,
						$this->_yPosition -$j,
						$this->_pageWidth -
						$this->_leftMargin,
						$this->_yPosition -$j);
			}
		
			$j += 19;
		}
			
		//-----------------------------------------------------------------------
		//-----------------------------------------------------------------------
		//-----------------------------------------------------------------------
		
		$control = new DateHelper();
		
		$resultats = $this->_resultatsAnalysesDemandees;

		$this->_yPosition -= $noteLineHeight-20; //Allez à la ligne
		
		$this->_page->setLineColor(new ZendPdf\Color\Html('#999999'));
		$this->_page->setLineWidth(1);
		$this->_page->drawLine($this->_leftMargin+210,
				$this->_yPosition -2,
				$this->_pageWidth -
				$this->_leftMargin-218,
				$this->_yPosition -2);
		
		$this->getNewTime();
		$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', 'BILAN RENAL' ),
				$this->_leftMargin+210,
				$this->_yPosition+1);
		
		$this->_yPosition -= $noteLineHeight;    //aller a la ligne suivante
		
		//--- AZOTEMIE = UREE --- AZOTEMIE = UREE --- AZOTEMIE = UREE --- AZOTEMIE = UREE ---
		//--- AZOTEMIE = UREE --- AZOTEMIE = UREE --- AZOTEMIE = UREE --- AZOTEMIE = UREE ---
		if(in_array(23, $idanalysesBilanRenal)){
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10);
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "AZOTEMIE = UREE : " ),
					$this->_leftMargin+5,
					$this->_yPosition+9);
		
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 11);
			$this->_page->drawText($resultats[23]['valeur'],
					$this->_leftMargin+160,
					$this->_yPosition+9);
				
			$this->_yPosition -= $noteLineHeight-12; //aller a la ligne suivante
		}
		
		//--- ALBUMINEMIE --- ALBUMINEMIE --- ALBUMINEMIE --- ALBUMINEMIE
		//--- ALBUMINEMIE --- ALBUMINEMIE --- ALBUMINEMIE --- ALBUMINEMIE
		if(in_array(46, $idanalysesBilanRenal)){
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10);
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "ALBUMINEMIE : " ),
					$this->_leftMargin+5,
					$this->_yPosition+9);
		
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 11);
			$this->_page->drawText($resultats[46]['albuminemie'],
					$this->_leftMargin+160,
					$this->_yPosition+9);
		
			$this->_yPosition -= $noteLineHeight-12; //aller a la ligne suivante
		}
	
		return $this->_page;
	}
	

	public function getSerologie($noteLineHeight, $idanalysesSerologie){

		//-------------PLACER LES LIGNES DES EXAMENS A AFFICHER ---------
		//-------------PLACER LES LIGNES DES EXAMENS A AFFICHER ---------
		//-------------PLACER LES LIGNES DES EXAMENS A AFFICHER ---------
		$j = 28;
		for($i = 0 ; $i < count($idanalysesSerologie) ; $i++){
		
			if($i%2 == 0){
				$this->_page->setLineColor(new ZendPdf\Color\Html('#e1e1e1'));
				$this->_page->setLineWidth(17);
				$this->_page->drawLine($this->_leftMargin,
						$this->_yPosition -$j,
						$this->_pageWidth -
						$this->_leftMargin,
						$this->_yPosition -$j);
			}else{
				$this->_page->setLineColor(new ZendPdf\Color\Html('#f1f1f1'));
				$this->_page->setLineWidth(17);
				$this->_page->drawLine($this->_leftMargin,
						$this->_yPosition -$j,
						$this->_pageWidth -
						$this->_leftMargin,
						$this->_yPosition -$j);
			}
		
			$j += 19;
		}
			
		//-----------------------------------------------------------------------
		//-----------------------------------------------------------------------
		//-----------------------------------------------------------------------
		
		$control = new DateHelper();
		
		$resultats = $this->_resultatsAnalysesDemandees;
		
		$this->_yPosition -= $noteLineHeight-20; //Allez à la ligne
		
		$this->_page->setLineColor(new ZendPdf\Color\Html('#999999'));
		$this->_page->setLineWidth(1);
		$this->_page->drawLine($this->_leftMargin+210,
				$this->_yPosition -2,
				$this->_pageWidth -
				$this->_leftMargin-229,
				$this->_yPosition -2);
		
		$this->getNewTime();
		$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', 'SEROLOGIE' ),
				$this->_leftMargin+210,
				$this->_yPosition+1);
		
		$this->_yPosition -= $noteLineHeight;    //aller a la ligne suivante
		
		//--- CRP  --- CRP --- CRP --- CRP ---
		//--- CRP  --- CRP --- CRP --- CRP ---
		if(in_array(53, $idanalysesSerologie)){
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10);
			$this->_page->drawText("CRP ou C. Protéine Réactive ",
					$this->_leftMargin+5,
					$this->_yPosition+9);
		
			if($resultats[53]['optionResultatCrp'] == 'positif'){
				$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 11);
				$this->_page->drawText('Positif ',
						$this->_leftMargin+160,
						$this->_yPosition+9);
				
				$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 11);
				$this->_page->drawText($resultats[53]['crpValeurResultat'],
						$this->_leftMargin+240,
						$this->_yPosition+9);
				
				$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_ITALIC), 9);
				$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "mg/l " ),
						$this->_leftMargin+265,
						$this->_yPosition+9);
			}else{
				
				$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 11);
				$this->_page->drawText('Négatif ',
						$this->_leftMargin+160,
						$this->_yPosition+9);
				
			}

			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_ITALIC), 9);
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "N : < 6 mg/l " ),
					$this->_leftMargin+360,
					$this->_yPosition+9);
			
			$this->_yPosition -= $noteLineHeight-12; //aller a la ligne suivante
		}
		
		//--- RF WAALER ROSE --- RF WAALER ROSE --- RF WAALER ROSE --- RF WAALER ROSE 
		//--- RF WAALER ROSE --- RF WAALER ROSE --- RF WAALER ROSE --- RF WAALER ROSE
		if(in_array(55, $idanalysesSerologie)){
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10);
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "RF WAALER ROSE : " ),
					$this->_leftMargin+5,
					$this->_yPosition+9);
		
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 11);
			$this->_page->drawText($resultats[55]['rf_waaler_rose'],
					$this->_leftMargin+130,
					$this->_yPosition+9);
		
			$this->_yPosition -= $noteLineHeight-12; //aller a la ligne suivante
		}
		
		//--- SEROLOGIE SYPHILITIQUE BW --- SEROLOGIE SYPHILITIQUE BW --- SEROLOGIE SYPHILITIQUE BW
		//--- SEROLOGIE SYPHILITIQUE BW --- SEROLOGIE SYPHILITIQUE BW --- SEROLOGIE SYPHILITIQUE BW
		if(in_array(60, $idanalysesSerologie)){
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10);
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "SEROLOGIE SYPHILITIQUE BW : " ),
					$this->_leftMargin+5,
					$this->_yPosition+9);
		
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_ITALIC), 8);
			$this->_page->drawText("TPHA ",
					$this->_leftMargin+180,
					$this->_yPosition+9);
				
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 11);
			$this->_page->drawText($resultats[60]['serologie_syphilitique'],
					$this->_leftMargin+215,
					$this->_yPosition+9);
				
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_ITALIC), 8);
			$this->_page->drawText("RPR ",
					$this->_leftMargin+300,
					$this->_yPosition+9);
				
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 11);
			$this->_page->drawText($resultats[60]['serologie_syphilitique'],
					$this->_leftMargin+335,
					$this->_yPosition+9);
		
			$this->_yPosition -= $noteLineHeight-12; //aller a la ligne suivante
		}
		
		//--- ASLO --- ASLO --- ASLO --- ASLO --- ASLO --- ASLO --- ASLO --- ASLO	
		//--- ASLO --- ASLO --- ASLO --- ASLO --- ASLO --- ASLO --- ASLO --- ASLO
		if(in_array(61, $idanalysesSerologie)){
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10);
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "ASLO : " ),
					$this->_leftMargin+5,
					$this->_yPosition+9);
		
			$this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 11);
			$this->_page->drawText($resultats[61]['aslo'],
					$this->_leftMargin+80,
					$this->_yPosition+9);
		
			$this->_yPosition -= $noteLineHeight-12; //aller a la ligne suivante
		}
		
		return $this->_page;
		
	}
	
}