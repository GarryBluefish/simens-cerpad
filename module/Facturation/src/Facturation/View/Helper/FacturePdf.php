<?php
namespace Facturation\View\Helper;

use ZendPdf;
use ZendPdf\Page;
use ZendPdf\Font;
use Consultation\Model\Consultation;
use Facturation\View\Helper\DateHelper; 


class FacturePdf
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
	protected $_infos;
	protected $_listeAnalysesDemndees;
	protected $_depistage;
	
	
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
	
	public function setInformations($infos){
		$this->_infos = $infos;
	}
	
	public function getNewItalique(){
		$font = ZendPdf\Font::fontWithName(ZendPdf\Font::FONT_HELVETICA_OBLIQUE);
		$this->_page->setFont($font, 12);
	}
	
	public function getNewItalique2(){
		$font = ZendPdf\Font::fontWithName(ZendPdf\Font::FONT_HELVETICA_OBLIQUE);
		$this->_page->setFont($font, 10);
	}
	
	public function getNewTime(){
		$font = ZendPdf\Font::fontWithName(ZendPdf\Font::FONT_TIMES_ROMAN);
		$this->_page->setFont($font, 13);
	}
	
	public function getNewTimeBold(){
		$font = ZendPdf\Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD);
		$this->_page->setFont($font, 14);
	}
	
	public function getStyle(){
		$font = ZendPdf\Font::fontWithName(ZendPdf\Font::FONT_TIMES_ITALIC);
		$this->_page->setFont($font, 14);
	}
	
	protected function nbAnnees($debut, $fin) {
		//60 secondes X 60 minutes X 24 heures dans une journee
		$nbSecondes = 60*60*24*365;
	
		$debut_ts = strtotime($debut);
		$fin_ts = strtotime($fin);
		$diff = $fin_ts - $debut_ts;
		return round($diff / $nbSecondes);
	}
	
	public function getNewTime2(){
		$font = ZendPdf\Font::fontWithName(ZendPdf\Font::FONT_TIMES_ROMAN);
		$this->_page->setFont($font, 12);
	}
	
	public function getNewTime3(){
		$font = ZendPdf\Font::fontWithName(ZendPdf\Font::FONT_TIMES_ROMAN);
		$this->_page->setFont($font, 10);
	}
	
	public function getNewTime4(){
		$font = ZendPdf\Font::fontWithName(ZendPdf\Font::FONT_TIMES_ROMAN);
		$this->_page->setFont($font, 13);
	}
	
	public function getStyle6(){
		$font = ZendPdf\Font::fontWithName(ZendPdf\Font::FONT_TIMES_ROMAN);
		$this->_page->setFont($font, 12);
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
	
	protected function getListeAnalysesDemndees() {
		return $this->_listeAnalysesDemndees;
	}
	
	public function setDepistage($depistage){
		$this->_depistage = $depistage;
	}
	
	public function setListeAnalysesDemndees($listeAnalysesDemndees) {
		$this->_listeAnalysesDemndees = $listeAnalysesDemndees;
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
		$this->_page->drawText('FACTURE',
				$this->_leftMargin+200,
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
		
		$this->_page->setFillColor(new ZendPdf\Color\Html('black')); //Pour le text
		$this->_page->setLineColor(new ZendPdf\Color\Html('#999999')); //Pour les lignes

		$l = 1;
		$i = 0;
		$d = 1;
		$cpt = 0;
		
		//-----------------------------------------------
		$value = $this->_DonneesPatient;
		
		//-----------------------------------------------
		
		$typepatient = "E";
		if($this->_depistage->current()){
			if($this->_depistage->current()['typepatient'] == 1){ $typepatient = "I"; }
		}
		
		//***** numéro de la facture ******
		//***** numéro de la facture ******
		$this->_page->setFont($this->_newTime, 10);
		$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', 'NÂ°: '.$this->_infos['numero']),
				$this->_leftMargin,
				715);
		
		
		
		//-----------------------------------------------
		$this->_page->setFont($this->_newTime, 10);
		$this->_page->drawText(iconv ('UTF-8' , 'ISO-8859-1' , $this->_DonneesPatient['numero_dossier'] ),
				$this->_leftMargin,
				$this->_yPosition);
			//-----------------------------------------------
			$this->_page->setFont($this->_newTimeGras, 9);
			$this->_page->drawText('PRENOM & NOM :',
					$this->_leftMargin+123,
					$this->_yPosition);
			$this->_page->setFont($this->_newTime, 10);
			$this->_page->drawText(iconv ('UTF-8' ,'ISO-8859-1' , $value['prenom'].'  '.$value['nom']),
					$this->_leftMargin+210,
					$this->_yPosition);
			//-----------------------------------------------
			$this->_yPosition -= 15;// allez a ligne suivante
			//----------------------------------------------
			$this->_page->setFont($this->_newTimeGras, 9);
			$this->_page->drawText('SEXE :',
					$this->_leftMargin+173,
					$this->_yPosition);
			$this->_page->setFont($this->_newTime, 10);
			$this->_page->drawText(iconv ('UTF-8' ,'ISO-8859-1' , $value['sexe']),
					$this->_leftMargin+210,
					$this->_yPosition);
 			//-----------------------------------------------
    		$this->_yPosition -= 15;// allez a ligne suivante
 			//----- -----------------------------------------
			$date_naissance = $value['date_naissance'];
			if($date_naissance){ $date_naissance = $Control->convertDate($date_naissance); } else {$date_naissance = null; }
				
			if($date_naissance){
				$this->_page->setFont($this->_newTimeGras, 9);
				$this->_page->drawText('DATE DE NAISSANCE :',
						$this->_leftMargin+102,
						$this->_yPosition);
				$this->_page->setFont($this->_newTime, 10);
			
				
				$age = $value['age']; 
				//GESTION DES AGES
				//GESTION DES AGES
				if(!$age){
				
					$aujourdhui = (new \DateTime() ) ->format('Y-m-d');
					$age_jours = $this->nbJours($value['date_naissance'], $aujourdhui); 
					if($age_jours < 31) {
						$age = $age_jours." jours";
					}
					else
					if($age_jours >= 31) {
						$nb_mois = (int)($age_jours/30);
						$nb_jours = $age_jours - ($nb_mois*30);
						$age = $nb_mois."m ".$nb_jours."j ";
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
			
				$this->_page->drawText($value['age']." ans",
						$this->_leftMargin+210,
						$this->_yPosition);
			}
			
			//-----------------------------------------------
			$this->_yPosition -= 15;// allez a ligne suivante
			//----------------------------------------------
			$this->_page->setFont($this->_newTimeGras, 9);
			$this->_page->drawText('ADRESSE :',
					$this->_leftMargin+155,
					$this->_yPosition);
			$this->_page->setFont($this->_newTime, 10);
			$this->_page->drawText(iconv ('UTF-8' ,'ISO-8859-1' , $value['adresse']),
					$this->_leftMargin+210,
					$this->_yPosition);
			//-----------------------------------------------
			
			$this->_page->setlineColor(new ZendPdf\Color\Html('green'));
			//$this->_page->setLineWidth(0.2);
			$this->_page->drawLine($this->_leftMargin,
					$this->_yPosition-10,
					$this->_pageWidth -
					$this->_leftMargin,
					$this->_yPosition-10);

			$this->_page->setLineColor(new ZendPdf\Color\Html('#999999')); //Pour les ligne
			$this->_page->setLineWidth(0.0);
			
		//-----------------------------------------------
		$this->_yPosition -= $noteLineHeight; //aller a la ligne suivante
		
		$this->getStyle6();
		$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "DESIGNATION "),
				$this->_leftMargin+15,
				$this->_yPosition);
		
		$this->_page->setLineColor(new ZendPdf\Color\Html('#999999'));
		$this->_page->setLineWidth(1);
		$this->_page->drawLine($this->_leftMargin+15,
				$this->_yPosition -5,
				$this->_pageWidth -
				$this->_leftMargin-400,
				$this->_yPosition -5);
		
		$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "TARIF (FCFA)"),
				$this->_leftMargin+325,
				$this->_yPosition);
		
		$this->_page->setLineColor(new ZendPdf\Color\Html('#999999'));
		$this->_page->setLineWidth(1);
		$this->_page->drawLine($this->_leftMargin+325,
				$this->_yPosition -5,
				$this->_pageWidth -
				$this->_leftMargin-90,
				$this->_yPosition -5);
		
		
		$this->_yPosition -= $noteLineHeight;
		
		
		$analyses = array();
		$typesAnalyses = array();
		$tarifs = array();
		
		$listeAnalysesDemandees = $this->getListeAnalysesDemndees();
		
		$i = 1;
		for( $j = 0 ; $j < count($listeAnalysesDemandees) ; $j++){
			$analyses[$i] = $listeAnalysesDemandees[$j]['designation'];
			$typesAnalyses[$i] = $listeAnalysesDemandees[$j]['libelle'];
			$tarifs[$i++] = $listeAnalysesDemandees[$j]['tarif'];
		}
		
		//-----------------------------------------------------------------------
		//-----------------------------------------------------------------------
		//-------- HEMATOLOGIE --------- HEMATOLOGIE ---------- HEMATOLOGIE------
		//-------- HEMATOLOGIE --------- HEMATOLOGIE ---------- HEMATOLOGIE------
		//-------- HEMATOLOGIE --------- HEMATOLOGIE ---------- HEMATOLOGIE------
		//-----------------------------------------------------------------------
		//-----------------------------------------------------------------------
		
		if(in_array('HEMATOLOGIE', $typesAnalyses)){
			
			$this->getNewItalique2();
			$this->_page->drawText("(Hématologie)",
					$this->_leftMargin+15,
					$this->_yPosition+10);
			$this->_yPosition -= $noteLineHeight-20;
			
			
			$j = 0;
				
			for ($i = 1 ; $i <= count($analyses) ; $i++){
					
				if( $typesAnalyses[$i] == 'HEMATOLOGIE'){
		
					$this->_page->setLineColor(new ZendPdf\Color\Html('#efefef'));
					$this->_page->setLineWidth(0);
		
					$this->_page->drawLine($this->_leftMargin,
							$this->_yPosition -2,
							$this->_pageWidth -
							$this->_leftMargin,
							$this->_yPosition -2);
						
					$this->getNewTime3();
					$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', ++$j.')  '.$analyses[$i]),
							$this->_leftMargin+10,
							$this->_yPosition);
						
					$this->getNewTime4();
					$this->_page->drawText($this->prixMill( $tarifs[$i] ),
							$this->_leftMargin+330,
							$this->_yPosition);
						
					$this->_yPosition -= -15;
					$this->_yPosition -= $noteLineHeight;
				}
					
			}
			
			$this->_yPosition -= $noteLineHeight;//aller a la ligne suivante
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
		
		
		if(in_array('BIOCHIMIE', $typesAnalyses)){
				
			$this->getNewItalique2();
			$this->_page->drawText("(Biochimie)",
					$this->_leftMargin+15,
					$this->_yPosition+10);
			$this->_yPosition -= $noteLineHeight-20;
				
				
			$j = 0;
		
			for ($i = 1 ; $i <= count($analyses) ; $i++){
					
				if( $typesAnalyses[$i] == 'BIOCHIMIE'){
		
					$this->_page->setLineColor(new ZendPdf\Color\Html('#efefef'));
					$this->_page->setLineWidth(0);
		
					$this->_page->drawLine($this->_leftMargin,
							$this->_yPosition -2,
							$this->_pageWidth -
							$this->_leftMargin,
							$this->_yPosition -2);
		
					$this->getNewTime3();
					$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', ++$j.')  '.$analyses[$i]),
							$this->_leftMargin+10,
							$this->_yPosition);
		
					$this->getNewTime4();
					$this->_page->drawText($this->prixMill( $tarifs[$i] ),
							$this->_leftMargin+330,
							$this->_yPosition);
		
					$this->_yPosition -= -15;
					$this->_yPosition -= $noteLineHeight;
				}
					
			}
				
			$this->_yPosition -= $noteLineHeight;//aller a la ligne suivante
		}
		
		
		//-----------------------------------------------------------------
		//-----------------------------------------------------------------
		//-----------------------------------------------------------------
		//-----------------------------------------------------------------
		//-----------------------------------------------------------------
		//-----------------------------------------------------------------
			
			
		//-----------------------------------------------------------------
		//-----------------------------------------------------------------
		//----- PARASITOLOGIE ---- PARASITOLOGIE ------ PARASITOLOGIE------
		//----- PARASITOLOGIE ---- PARASITOLOGIE ------ PARASITOLOGIE------
		//----- PARASITOLOGIE ---- PARASITOLOGIE ------ PARASITOLOGIE------
		//-----------------------------------------------------------------
		//-----------------------------------------------------------------
		
		
		if(in_array('PARASITOLOGIE', $typesAnalyses)){
		
			$this->getNewItalique2();
			$this->_page->drawText("(Parasitologie)",
					$this->_leftMargin+15,
					$this->_yPosition+10);
			$this->_yPosition -= $noteLineHeight-20;
		
		
			$j = 0;
		
			for ($i = 1 ; $i <= count($analyses) ; $i++){
					
				if( $typesAnalyses[$i] == 'PARASITOLOGIE'){
		
					$this->_page->setLineColor(new ZendPdf\Color\Html('#efefef'));
					$this->_page->setLineWidth(0);
		
					$this->_page->drawLine($this->_leftMargin,
							$this->_yPosition -2,
							$this->_pageWidth -
							$this->_leftMargin,
							$this->_yPosition -2);
		
					$this->getNewTime3();
					$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', ++$j.')  '.$analyses[$i]),
							$this->_leftMargin+10,
							$this->_yPosition);
		
					$this->getNewTime4();
					$this->_page->drawText($this->prixMill( $tarifs[$i] ),
							$this->_leftMargin+330,
							$this->_yPosition);
		
					$this->_yPosition -= -15;
					$this->_yPosition -= $noteLineHeight;
				}
					
			}
		
			$this->_yPosition -= $noteLineHeight;//aller a la ligne suivante
		}
		
		
		//-----------------------------------------------------------------
		//-----------------------------------------------------------------
		//-----------------------------------------------------------------
		//-----------------------------------------------------------------
		//-----------------------------------------------------------------
		//-----------------------------------------------------------------
		
		
		//-----------------------------------------------------------------
		//-----------------------------------------------------------------
		//----- BACTERIOLOGIE ---- BACTERIOLOGIE ------ BACTERIOLOGIE------
		//----- BACTERIOLOGIE ---- BACTERIOLOGIE ------ BACTERIOLOGIE------
		//----- BACTERIOLOGIE ---- BACTERIOLOGIE ------ BACTERIOLOGIE------
		//-----------------------------------------------------------------
		//-----------------------------------------------------------------
		
		
		if(in_array('BACTERIOLOGIE', $typesAnalyses)){
		
			$this->getNewItalique2();
			$this->_page->drawText("(Bactériologie)",
					$this->_leftMargin+15,
					$this->_yPosition+10);
			$this->_yPosition -= $noteLineHeight-20;
		
		
			$j = 0;
		
			for ($i = 1 ; $i <= count($analyses) ; $i++){
					
				if( $typesAnalyses[$i] == 'BACTERIOLOGIE'){
		
					$this->_page->setLineColor(new ZendPdf\Color\Html('#efefef'));
					$this->_page->setLineWidth(0);
		
					$this->_page->drawLine($this->_leftMargin,
							$this->_yPosition -2,
							$this->_pageWidth -
							$this->_leftMargin,
							$this->_yPosition -2);
		
					$this->getNewTime3();
					$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', ++$j.')  '.$analyses[$i]),
							$this->_leftMargin+10,
							$this->_yPosition);
		
					$this->getNewTime4();
					$this->_page->drawText($this->prixMill( $tarifs[$i] ),
							$this->_leftMargin+330,
							$this->_yPosition);
		
					$this->_yPosition -= -15;
					$this->_yPosition -= $noteLineHeight;
				}
					
			}
		
			$this->_yPosition -= $noteLineHeight;//aller a la ligne suivante
		}
		
		
		//-----------------------------------------------------------------
		//-----------------------------------------------------------------
		//-----------------------------------------------------------------
		//-----------------------------------------------------------------
		//-----------------------------------------------------------------
		//-----------------------------------------------------------------
		
			
		//-----------------------------------------------------------------
		//-----------------------------------------------------------------
		//-------- DEPISTAGE --------- DEPISTAGE ---------- DEPISTAGE------
		//-------- DEPISTAGE --------- DEPISTAGE ---------- DEPISTAGE------
		//-------- DEPISTAGE --------- DEPISTAGE ---------- DEPISTAGE------
		//-----------------------------------------------------------------
		//-----------------------------------------------------------------
		
		
		if(in_array('DEPISTAGE', $typesAnalyses)){
		
			$this->getNewItalique2();
			$this->_page->drawText("(Dépistage)",
					$this->_leftMargin+15,
					$this->_yPosition+10);
			$this->_yPosition -= $noteLineHeight-20;
		
		
			$j = 0;
		
			for ($i = 1 ; $i <= count($analyses) ; $i++){
					
				if( $typesAnalyses[$i] == 'DEPISTAGE'){
		
					$this->_page->setLineColor(new ZendPdf\Color\Html('#efefef'));
					$this->_page->setLineWidth(0);
		
					$this->_page->drawLine($this->_leftMargin,
							$this->_yPosition -2,
							$this->_pageWidth -
							$this->_leftMargin,
							$this->_yPosition -2);
		
					$this->getNewTime3();
					$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', ++$j.')  '.$analyses[$i]),
							$this->_leftMargin+10,
							$this->_yPosition);
		
					$this->getNewTime4();
					$this->_page->drawText($this->prixMill( $tarifs[$i] ),
							$this->_leftMargin+330,
							$this->_yPosition);
		
					$this->_yPosition -= -15;
					$this->_yPosition -= $noteLineHeight;
				}
					
			}
		
			$this->_yPosition -= $noteLineHeight;//aller a la ligne suivante
		}
		
		
		
		//-----------------------------------------------------------------
		//-----------------------------------------------------------------
		//-----------------------------------------------------------------
		//-----------------------------------------------------------------
		//-----------------------------------------------------------------
		//-----------------------------------------------------------------
		
		
		
		
		
		
		
		
		
		
		
		
		
		$noteLineHeight -= 10;
		//-----------------------------------------------
		//-----------------------------------------------
// 		$this->_page->drawLine($this->_leftMargin,
// 				$this->_yPosition - 2,
// 				$this->_pageWidth -
// 				$this->_leftMargin,
// 				$this->_yPosition - 2);
		
// 		$this->getStyle();
// 		$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "NumÃ©ro de la facture : "),
// 				$this->_leftMargin+5,
// 				$this->_yPosition);
		
// 		$this->getNewTime();
// 		$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $this->_infos['numero']),
// 				$this->_leftMargin+150,
// 				$this->_yPosition);

// 		$this->_yPosition -= $noteLineHeight;
		
		//-----------------------------------------------
		//-----------------------------------------------
// 		$this->_page->drawLine($this->_leftMargin,
// 				$this->_yPosition - 2,
// 				$this->_pageWidth -
// 				$this->_leftMargin,
// 				$this->_yPosition - 2);
		
// 		$this->getStyle();
// 		$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "Service d'admission : "),
// 				$this->_leftMargin+5,
// 				$this->_yPosition);
		
// 		$this->getNewTime();
// 		$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "Infirmerie"),
// 				$this->_leftMargin+140,
// 				$this->_yPosition);
		
// 		$this->_yPosition -= $noteLineHeight;
		
		//-----------------------------------------------
		//-----------------------------------------------
		$this->_page->drawLine($this->_leftMargin,
				$this->_yPosition - 2,
				$this->_pageWidth -
				$this->_leftMargin,
				$this->_yPosition - 2);
		
		$this->getStyle6();
		$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "TOTAL  : "),
				$this->_leftMargin+260,
				$this->_yPosition);
		
		$this->getNewTimeBold();
		$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $this->prixMill( $this->_infos['montant']) ),
				$this->_leftMargin+330,
				$this->_yPosition);
		$this->getNewTime();
		$this->_page->drawText('  FCFA',
				$this->_leftMargin+370,
				$this->_yPosition);
		
		$this->_yPosition -= $noteLineHeight;
		
		
		
		$this->_page->setLineColor(new ZendPdf\Color\Html('#ffffff')); //Pour les lignes
		$this->_page->setLineWidth(0.0);
		//-----------------------------------------------
		//-----------------------------------------------
		
		if($this->_infos['type_facturation'] == 2){
			
			$this->_page->drawLine($this->_leftMargin,
					$this->_yPosition,
					$this->_pageWidth -
					$this->_leftMargin,
					$this->_yPosition);
			$this->_yPosition -= $noteLineHeight;
			

			$this->_page->setLineColor(new ZendPdf\Color\Html('#efefef'));
			$this->_page->setLineWidth(0);
			
			//-----------------------------------------------
			//-----------------------------------------------
			$this->_page->drawLine($this->_leftMargin,
					$this->_yPosition - 2,
					$this->_pageWidth -
					$this->_leftMargin,
					$this->_yPosition - 2);
			
			$this->getStyle();
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "Prise en charge par : "),
					$this->_leftMargin+5,
					$this->_yPosition);
			
			$this->getNewTime();
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $this->_infos['organisme']),
					$this->_leftMargin+160,
					$this->_yPosition);
			
			$this->_yPosition -= $noteLineHeight;
			//-----------------------------------------------
			//-----------------------------------------------
			
			$this->_page->drawLine($this->_leftMargin,
					$this->_yPosition,
					$this->_pageWidth -
					$this->_leftMargin,
					$this->_yPosition);
			
			$this->getStyle();
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "Taux de la majoration : "),
					$this->_leftMargin+5,
					$this->_yPosition);
				
			$this->getNewTime();
			
			if($this->_infos['taux']){
				$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $this->_infos['taux'].' %'),
						$this->_leftMargin+160,
						$this->_yPosition);
			}else {
				$this->_page->drawText( '00 %',
						$this->_leftMargin+160,
						$this->_yPosition);
			}
			
			$this->_yPosition -= $noteLineHeight;
			
			//-----------------------------------------------
			//-----------------------------------------------
			$this->_page->drawLine($this->_leftMargin,
					$this->_yPosition,
					$this->_pageWidth -
					$this->_leftMargin,
					$this->_yPosition);
			
			$this->getStyle();
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "valeur de la majoration : "),
					$this->_leftMargin+5,
					$this->_yPosition);
			$valeur_majoration = ($this->_infos['montant']/100) * $this->_infos['taux'];
			$this->getNewTime();
			$this->_page->drawText( $this->prixMill( "$valeur_majoration" ) .' FCFA',
					$this->_leftMargin+160,
					$this->_yPosition);
			
			$this->_yPosition -= $noteLineHeight;
			
			//-----------------------------------------------
			//-----------------------------------------------
			$this->_page->drawLine($this->_leftMargin,
					$this->_yPosition,
					$this->_pageWidth -
					$this->_leftMargin,
					$this->_yPosition);
			
			$this->getStyle();
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "Tarif avec majoration : "),
					$this->_leftMargin+5,
					$this->_yPosition);
			
			$this->getNewTimeBold();
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $this->prixMill( $this->_infos['montant_avec_majoration']) ),
					$this->_leftMargin+160,
					$this->_yPosition);
			$this->getNewTime();
			$this->_page->drawText('  FCFA',
					$this->_leftMargin+200,
					$this->_yPosition);
			
			$this->_yPosition -= $noteLineHeight;
		}
		
		//-----------------------------------------------
		//-----------------------------------------------
	} 
	
	public function getPiedPage(){
		$this->_page->setlineColor(new ZendPdf\Color\Html('green'));
		$this->_page->setLineWidth(1.5);
		$this->_page->setLineDashingPattern(array(0, 0));
		$this->_page->drawLine($this->_leftMargin,
				90,
				$this->_pageWidth -
				$this->_leftMargin,
				90);
		
		$this->_page->setFont($this->_newTime, 10);
		$this->_page->drawText('Téléphone: 33 726 25 36   BP: 24000',
				$this->_leftMargin,
				$this->_pageWidth - ( 100 + 420));
		
		$this->_page->setFont($this->_newTime, 10);
		$this->_page->drawText('SIMENS+: ',
				$this->_leftMargin + 355,
				$this->_pageWidth - ( 100 + 420));
		$this->_page->setFont($this->_newTimeGras, 11);
		$this->_page->drawText('www.simens.sn',
				$this->_leftMargin + 405,
				$this->_pageWidth - ( 100 + 420));
	}
	
}