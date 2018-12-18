<?php
namespace Laboratoire\View\Helper;

use ZendPdf;
use ZendPdf\Page;
use ZendPdf\Font;


class DepistagePaillasse
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
	protected $_depistage;
	protected $_listeDepistage;

	
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
		
		$this->_yPosition -= 35;
		$this->_page->setFont($this->_newTime, 15);
		$this->_page->setFillColor(new ZendPdf\Color\Html('green'));
		$this->_page->drawText('DEPISTAGE',
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
	}
	
	public function setDepistage($depistage){
		$this->_depistage = $depistage;
	}
	
	public function setListeDepistage($listeDepistage){
		$this->_listeDepistage = $listeDepistage;
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
		$this->_page->setFont($font, 9);
	}
	
	public function getNewTimeBold(){
		$font = ZendPdf\Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD);
		$this->_page->setFont($font, 16);
	}
	
	public function getStyle(){
		$font = ZendPdf\Font::fontWithName(ZendPdf\Font::FONT_TIMES);
		$this->_page->setFont($font, 10);
	}
	
	public function getNewTime2(){
		$font = ZendPdf\Font::fontWithName(ZendPdf\Font::FONT_TIMES_ROMAN);
		$this->_page->setFont($font, 12);
	}
	
	public function getNewTime3(){
		$font = ZendPdf\Font::fontWithName(ZendPdf\Font::FONT_TIMES_ROMAN);
		$this->_page->setFont($font, 10);
	}
	
	public function getStyle6(){
		$font = ZendPdf\Font::fontWithName(ZendPdf\Font::FONT_TIMES_ROMAN);
		$this->_page->setFont($font, 12);
	}
	
	
	
 	protected  function getNoteInformations(){
 		$noteLineHeight = 17;
 		
 		$listeDepistage = $this->_listeDepistage;
 		
 		$indice = 1;
 		for($i = 0 ; $i < count($listeDepistage) ; $i++){
 			//Affichage des infos sur le nom et prenom du patient
 			$this->_page->setLineColor(new ZendPdf\Color\Html('#cccecd'));
 			$this->_page->setLineWidth(14);
 			$this->_page->drawLine($this->_leftMargin,
 					$this->_yPosition -0 ,
 					$this->_pageWidth -
 					$this->_leftMargin,
 					$this->_yPosition -0);

 			$idpatient = $listeDepistage[$i][0];
 			$nom = $listeDepistage[$i][1];
 			$prenom = $listeDepistage[$i][2];
 			$listeAnalyses = $listeDepistage[$i][3];
 			$conformite = $listeDepistage[$i][4];
 			
 			$typepatient = "E";
 			if(in_array($idpatient, $this->_depistage)){
 				$typepatient = "I";
 			}
 			
 			$this->_page->setFillColor(new ZendPdf\Color\Html('black'));
 			$this->getStyle();
 			$this->_page->drawText($idpatient.'-'.$typepatient,
 					$this->_leftMargin+10,
 					$this->_yPosition - 3.5);
 				
 			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $prenom." "."  "." ".$nom ),
 					$this->_leftMargin+50,
 					$this->_yPosition - 3.5);
 			
 			$this->_yPosition -= 5;
 			$this->_yPosition -= $noteLineHeight;
 			//Affichage de la liste des analyses
 			for($a = 0 ; $a < count($listeAnalyses) ; $a++){
 				$this->_page->setLineColor(new ZendPdf\Color\Html('#efefef'));
 				$this->_page->setLineWidth(0.5);
 				
 				$this->_page->drawLine($this->_leftMargin,
 						$this->_yPosition -2,
 						$this->_pageWidth -
 						$this->_leftMargin,
 						$this->_yPosition -2);

 				$conforme ="";
 				if($conformite[$a] == 0){
 					$conforme = "(NC)";
 				}

 				$this->_page->setFillColor(new ZendPdf\Color\Html('black'));
 				$this->getNewTime();
  				$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $indice++.")  ".$listeAnalyses[$a]."   ".$conforme),
  						$this->_leftMargin+20,
  						$this->_yPosition);
 					
  				$this->_page->setFillColor(new ZendPdf\Color\Html('#797979'));
 				$this->getNewTime2();
 				$this->_page->drawText(" _____ ",
 						$this->_leftMargin+300,
 						$this->_yPosition);
 					
 				$this->_yPosition -= $noteLineHeight;
 			}
 			
 		}
 		
 	} 
	
 	
	public function getPiedPage(){
		$this->_page->setlineColor(new ZendPdf\Color\Html('green'));
		$this->_page->setLineWidth(1.5);
		$this->_page->setLineDashingPattern(array(0, 0));
		$this->_page->drawLine($this->_leftMargin,
				60,
				$this->_pageWidth -
				$this->_leftMargin,
				60);
		
		$this->_page->setFont($this->_newTime, 10);
		$this->_page->drawText('Téléphone: 77 680 69 69',
				$this->_leftMargin,
				$this->_pageWidth - ( 100 + 450));
		
		$this->_page->setFont($this->_newTime, 10);
		$this->_page->drawText('SIMENS+: ',
				$this->_leftMargin + 355,
				$this->_pageWidth - ( 100 + 450));
		$this->_page->setFont($this->_newTimeGras, 11);
		$this->_page->drawText('www.simens.sn',
				$this->_leftMargin + 405,
				$this->_pageWidth - ( 100 + 450));
	}
	
}