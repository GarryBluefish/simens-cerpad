<?php
namespace Laboratoire\View\Helper;

use ZendPdf;
use ZendPdf\Page;
use ZendPdf\Font;


class BiochimiePaillasseParAnalyse
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
	protected $_listeHematologie;
	protected $_newTimeItalic;

	
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
 		$this->_newTimeItalic = Font::fontWithName(ZendPdf\Font::FONT_TIMES_ITALIC);
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
		$this->_page->setFont($this->_newTimeItalic, 10);
		$this->_page->setFillColor(new ZendPdf\Color\Html('black'));
		$this->_page->drawText('feuille de paillasse',
				$this->_leftMargin+2,
				$this->_yPosition);
		$this->_page->setFont($this->_newTime, 15);
		$this->_page->setFillColor(new ZendPdf\Color\Html('green'));
		$this->_page->drawText('BIOCHIMIE',
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
	
	public function setListeHematologie($listeHematologie){
		$this->_listeHematologie = $listeHematologie;
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
	
	public function getStyle7(){
		$font = ZendPdf\Font::fontWithName(ZendPdf\Font::FONT_TIMES_ITALIC);
		$this->_page->setFont($font, 10);
	}
	
	
	
 	protected  function getNoteInformations(){
 		$noteLineHeight = 17;
 		
 		$listeHematologie = $this->_listeHematologie;
 		
 		$indice  = 1;
 		$nbLigne = 1; 
 		for($i = 0 ; $i < count($listeHematologie) ; $i++){
 			//Affichage des infos sur le libelle de l'analyse
 			//Affichage des infos sur le libelle de l'analyse
 			$this->_page->setLineColor(new ZendPdf\Color\Html('#cfcfcf'));
 			$this->_page->setLineWidth(14);
 			$this->_page->drawLine($this->_leftMargin,
 					$this->_yPosition -0 ,
 					$this->_pageWidth -
 					$this->_leftMargin,
 					$this->_yPosition -0);
 			
 			$idAnalyse = $listeHematologie[$i][0];
 			$libelleAnalyse = $listeHematologie[$i][1];
 			
 			$idpatient = $listeHematologie[$i][2];
 			$listeNom = $listeHematologie[$i][3];
 			$prenom = $listeHematologie[$i][4];
 			$conformite = $listeHematologie[$i][5];
 			$numOrdrePatient = $listeHematologie[$i][6];
 			
  			$this->_page->setFillColor(new ZendPdf\Color\Html('black'));
  			$this->getStyle();
  			
  			//SEPARATEURS  ---  SEPARATEURS  ---  SEPARATEURS
  			//SEPARATEURS  ---  SEPARATEURS  ---  SEPARATEURS
  			//PARTIE DE LA CONFORMITE
  			$this->_page->setLineColor(new ZendPdf\Color\Html('#ffffff'));
  			$this->_page->setLineWidth(14);
  			$this->_page->drawText( 'C' ,
  					$this->_leftMargin+208,
  					$this->_yPosition - 3.5);
  			$this->_page->drawLine($this->_leftMargin+202,
  					$this->_yPosition -0 ,
  					$this->_pageWidth -
  					$this->_leftMargin-292,
  					$this->_yPosition -0);
  			//------------------------
  			
  			//PARTIE DE =====
  			$this->_page->setLineColor(new ZendPdf\Color\Html('#ffffff'));
  			$this->_page->setLineWidth(14);
  			$this->_page->drawLine($this->_leftMargin+220,
  					$this->_yPosition -0 ,
  					$this->_pageWidth -
  					$this->_leftMargin-276,
  					$this->_yPosition -0);
  			//---------------
  			//============================================
  			//============================================
  			
  			
  			//=== GESTION DES TITRES === GESTION DES TITRES
  			//=== GESTION DES TITRES === GESTION DES TITRES
  			if($idAnalyse == 29){
  				$this->_page->drawText( 'CH (TOTAL-HDL-LDL) TG' ,
  						$this->_leftMargin+10,
  						$this->_yPosition - 3.5);
  			}else {
  				$this->_page->drawText( $libelleAnalyse ,
  						$this->_leftMargin+10,
  						$this->_yPosition - 3.5);
  			}

  			//GESTION DES TITRES DES CHAMPS DES RESULTATS
  			//GESTION DES TITRES DES CHAMPS DES RESULTATS
 		    if($idAnalyse == 31){
  				$this->_page->drawText(" Na ", $this->_leftMargin+245,$this->_yPosition - 3.5);
  				$this->_page->drawText('+', $this->_leftMargin+260, $this->_yPosition - 2);
  				
  				$this->_page->drawText(" K ", $this->_leftMargin+305, $this->_yPosition- 3.5);
  				$this->_page->drawText('+', $this->_leftMargin+315, $this->_yPosition - 2);
  				
  				$this->_page->drawText(" Cl ", $this->_leftMargin+365, $this->_yPosition- 3.5);
  				$this->_page->drawText('-', $this->_leftMargin+378, $this->_yPosition - 0.5);
 		    }else 
 		    if($idAnalyse == 42){
 		    	$this->_page->drawText(" BT ", $this->_leftMargin+245, $this->_yPosition- 3.5);
 		    	$this->_page->drawText(" BD ", $this->_leftMargin+305, $this->_yPosition- 3.5);
 		    }else
 		    if($idAnalyse == 47){
 		    	$this->_page->drawText(" Albumine ", $this->_leftMargin+232, $this->_yPosition- 3.5);
 		    	$this->_page->drawText(" Sucre ", $this->_leftMargin+300, $this->_yPosition- 3.5);
 		    	$this->_page->drawText(" Corps cétonique ", $this->_leftMargin+345, $this->_yPosition- 3.5);
 		    }else 
 		    if($idAnalyse == 49){
 		    	$this->_page->drawText(" (g/l) ", $this->_leftMargin+243, $this->_yPosition- 3.5);
 		    	$this->_page->drawText(" diurèse(l) ", $this->_leftMargin+294, $this->_yPosition- 3.5);
 		    	$this->_page->drawText(" (g/24h) ", $this->_leftMargin+360, $this->_yPosition- 3.5);
 		    }else
 		    if($idAnalyse == 50){
 		    	$this->_page->drawText(" GB ", $this->_leftMargin+245, $this->_yPosition- 3.5);
 		    	$this->_page->drawText(" GR ", $this->_leftMargin+305, $this->_yPosition- 3.5);
 		    	$this->_page->drawText(" (ml) ", $this->_leftMargin+363, $this->_yPosition- 3.5);
 		    }
  			//---------------------------------------------
  			//---------------------------------------------
  			
 			$this->_yPosition -= 13;
 			
 			//GESTION DE LA VALEUR DU CONTROLE
 			//GESTION DE LA VALEUR DU CONTROLE
 			$this->_page->setLineColor(new ZendPdf\Color\Html('#f1f1f1'));
 			$this->_page->setLineWidth(14);
 			$this->_page->drawLine($this->_leftMargin,
 					$this->_yPosition -0 ,
 					$this->_pageWidth -
 					$this->_leftMargin,
 					$this->_yPosition -0);
 			$this->getStyle7();
 			$this->_page->drawText( 'Contrôle' , $this->_leftMargin+50, $this->_yPosition - 3.5);
 			$this->getStyle();
 			
 			$nbLigne++;
 			$this->_yPosition -= 5;
 			$this->_yPosition -= $noteLineHeight;
 			//Affichage de la liste des noms des patients
 			//Affichage de la liste des noms des patients
 			//Affichage de la liste des noms des patients
 			for($a = 0 ; $a < 36 ; $a++){
 				$this->_page->setLineColor(new ZendPdf\Color\Html('#cfcfcf'));
 				$this->_page->setLineWidth(0.5);
 				
 				$this->_page->drawLine($this->_leftMargin+10,
 						$this->_yPosition -2,
 						$this->_pageWidth -
 						$this->_leftMargin,
 						$this->_yPosition -2);
 				
 				$typepatient = "E";
 				if(in_array($idpatient[$a], $this->_depistage)){
 					$typepatient = "I";
 				}

 				$this->getStyle7();
 				$this->_page->drawText($numOrdrePatient[$a].'-'.$typepatient,
 						$this->_leftMargin+10,
 						$this->_yPosition );

 				$conforme ="";
 				if($conformite[$a] == 0){
 					$conforme = "n";
 				}

 				$this->_page->setFillColor(new ZendPdf\Color\Html('black'));
 				$this->getNewTime();
  				$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $listeNom[$a]."   ".$prenom[$a]),
  						$this->_leftMargin+50,
  						$this->_yPosition);

  				$this->getStyle7();
  				$this->_page->drawText( $conforme ,
  						$this->_leftMargin+208,
  						$this->_yPosition );
  				
 			    //POUR TOUTES LES ANALYSES AYANT AU MOINS UNE VALEUR POUR RESULTAT
  				//POUR TOUTES LES ANALYSES AYANT AU MOINS UNE VALEUR POUR RESULTAT
  				if(!in_array($idAnalyse, array(9,14,31,42,47,49,50,56,57,59,60))){
  					
  					$this->_page->setFillColor(new ZendPdf\Color\Html('#000000'));
  					$this->_page->drawText(" _____ ",
  							$this->_leftMargin+240,
  							$this->_yPosition);
  					
  					$this->_page->setFillColor(new ZendPdf\Color\Html('#eeeeee'));
  					$this->_page->drawText(" _____ ",
  							$this->_leftMargin+240,
  							$this->_yPosition+19);
  				}
  				
 				//POUR TOUTES LES ANALYSES AYANT PLUS DE DEUX VALEURS
  				//POUR TOUTES LES ANALYSES AYANT PLUS DE DEUX VALEURS
  				if($idAnalyse == 31){
  						
  					$this->_page->setFillColor(new ZendPdf\Color\Html('#000000'));
  					$this->_page->drawText(" _____ ",
  							$this->_leftMargin+240,
  							$this->_yPosition);
  					$this->_page->drawText(" _____ ",
  							$this->_leftMargin+300,
  							$this->_yPosition);
  					$this->_page->drawText(" _____ ",
  							$this->_leftMargin+360,
  							$this->_yPosition);
  				}else 
  					if($idAnalyse == 42){
  					
  						$this->_page->setFillColor(new ZendPdf\Color\Html('#000000'));
  						$this->_page->drawText(" _____ ",
  								$this->_leftMargin+240,
  								$this->_yPosition);
  						$this->_page->drawText(" _____ ",
  								$this->_leftMargin+300,
  								$this->_yPosition);
  					}else 
  						if($idAnalyse == 47){
  								
  							$this->_page->setFillColor(new ZendPdf\Color\Html('#000000'));
  							$this->_page->drawText(" _____ ",
  									$this->_leftMargin+240,
  									$this->_yPosition);
  							$this->_page->drawText(" _____ ",
  									$this->_leftMargin+300,
  									$this->_yPosition);
  							$this->_page->drawText(" _____ ",
  									$this->_leftMargin+360,
  									$this->_yPosition);
  						}else
  							if($idAnalyse == 49){
  							
  								$this->_page->setFillColor(new ZendPdf\Color\Html('#000000'));
  								$this->_page->drawText(" _____ ",
  										$this->_leftMargin+240,
  										$this->_yPosition);
  								$this->_page->drawText(" _____ ",
  										$this->_leftMargin+300,
  										$this->_yPosition);
  								$this->_page->drawText(" _____ ",
  										$this->_leftMargin+360,
  										$this->_yPosition);
  							}else 
  								if($idAnalyse == 50){
  										
  									$this->_page->setFillColor(new ZendPdf\Color\Html('#000000'));
  									$this->_page->drawText(" _____ ",
  											$this->_leftMargin+240,
  											$this->_yPosition);
  									$this->_page->drawText(" _____ ",
  											$this->_leftMargin+300,
  											$this->_yPosition);
  									$this->_page->drawText(" _____ ",
  											$this->_leftMargin+360,
  											$this->_yPosition);
  								} 
  								
  				
  				$nbLigne++;
 				$this->_yPosition -= $noteLineHeight;
 			}
 			
 			$nbLigne++; 
 			if($nbLigne > 35){ break; }
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
		$this->_page->drawText('Téléphone: 33 726 25 36   BP: 24000',
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