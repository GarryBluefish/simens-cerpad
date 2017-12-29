<?php

namespace Infirmerie\Controller;

use Zend\Mvc\Controller\AbstractActionController;

use Infirmerie\View\Helper\DateHelper;
use Zend\Json\Json;
use Zend\Form\View\Helper\FormRow;
use Zend\Form\View\Helper\FormSelect;
use Zend\Form\View\Helper\FormText;
use Zend\Form\View\Helper\FormHidden;
use Infirmerie\Form\BilanForm;
use Zend\Form\View\Helper\FormTextarea;
use Infirmerie\Form\ConsultationForm;
use Infirmerie\View\Helper\infosStatistiquePdf;

class InfirmerieController extends AbstractActionController {
	protected $patientTable;
	protected $facturationTable;
	protected $analyseTable;
	protected $personneTable;
	protected $bilanPrelevement;
	protected $codagePrelevement;
	protected $triPrelevement;
	protected $consultation;
	protected $motifAdmissionTable;
	protected $demandeAnalyseTable;
	
	public function getFacturationTable() {
	    if (! $this->facturationTable) {
	        $sm = $this->getServiceLocator ();
	        $this->facturationTable = $sm->get ( 'Facturation\Model\FacturationTable' );
	    }
	    return $this->facturationTable;
	}
	
	public function getPatientTable() {
		if (! $this->patientTable) {
			$sm = $this->getServiceLocator ();
			$this->patientTable = $sm->get ( 'Facturation\Model\PatientTable' );
		}
		return $this->patientTable;
	}
	
	public function getAnalyseTable() {
	    if (! $this->analyseTable) {
	        $sm = $this->getServiceLocator ();
	        $this->analyseTable = $sm->get ( 'Secretariat\Model\AnalyseTable' );
	    }
	    return $this->analyseTable;
	}
	
	public function getPersonneTable() {
	    if (! $this->personneTable) {
	        $sm = $this->getServiceLocator ();
	        $this->personneTable = $sm->get ( 'Secretariat\Model\PersonneTable' );
	    }
	    return $this->personneTable;
	}

	public function getBilanPrelevementTable() {
		if (! $this->bilanPrelevement) {
			$sm = $this->getServiceLocator ();
			$this->bilanPrelevement = $sm->get ( 'Infirmerie\Model\BilanPrelevementTable' );
		}
		return $this->bilanPrelevement;
	}
	
	public function getCodagePrelevementTable() {
		if (! $this->codagePrelevement) {
			$sm = $this->getServiceLocator ();
			$this->codagePrelevement = $sm->get ( 'Infirmerie\Model\CodagePrelevementTable' );
		}
		return $this->codagePrelevement;
	}
	
	public function getTriPrelevementTable() {
		if (! $this->triPrelevement) {
			$sm = $this->getServiceLocator ();
			$this->triPrelevement = $sm->get ( 'Laboratoire\Model\TriPrelevementTable' );
		}
		return $this->triPrelevement;
	}
	
	public function getConsultationTable() {
		if (! $this->consultation) {
			$sm = $this->getServiceLocator ();
			$this->consultation = $sm->get ( 'Infirmerie\Model\ConsultationTable' );
		}
		return $this->consultation;
	}
	
	public function getMotifAdmissionTable() {
		if (! $this->motifAdmissionTable) {
			$sm = $this->getServiceLocator ();
			$this->motifAdmissionTable = $sm->get ( 'Infirmerie\Model\MotifAdmissionTable' );
		}
		return $this->motifAdmissionTable;
	}
	
	public function getDemandeAnalyseTable() {
		if (! $this->demandeAnalyseTable) {
			$sm = $this->getServiceLocator ();
			$this->demandeAnalyseTable = $sm->get ( 'Infirmerie\Model\DemandeAnalyseTable' );
		}
		return $this->demandeAnalyseTable;
	}
	//=============================================================================================
	//---------------------------------------------------------------------------------------------
	//=============================================================================================
	
	public function baseUrl(){
	    $baseUrl = $_SERVER['REQUEST_URI'];
	    $tabURI  = explode('public', $baseUrl);
	    return $tabURI[0];
	}
	
	public function creerNumeroFacturation($numero) {
		$nbCharNum = 10 - strlen($numero);
		
		$chaine ="";
		for ($i=1 ; $i <= $nbCharNum ; $i++){
			$chaine .= '0';
		}
		$chaine .= $numero;
		
		return $chaine;
	}
	
	public function creerNumeroOrdrePrelevement($numero) {
		$nbCharNum = 5 - strlen($numero);
	
		$chaine ="";
		for ($i=1 ; $i <= $nbCharNum ; $i++){
			$chaine .= '0';
		}
		$chaine .= $numero;
	
		return $chaine;
	}
	
	public function listePatientAjaxAction() {
		$output = $this->getPatientTable ()->getListePatientsAdmisPrelevement();
		return $this->getResponse ()->setContent ( Json::encode ( $output, array (
				'enableJsonExprFinder' => true
		) ) );
	}
	
	public function moisEnLettre($mois){
		$lesMois = array('','Janvier','Fevrier','Mars','Avril',
				'Mais','Juin','Juillet','Aout','Septembre','Octobre','Novembre','Decembre');
		return $lesMois[$mois];
	}
	
	public function listePatientAction() {
	
		$this->layout ()->setTemplate ( 'layout/infirmerie' );
		
		//$intervalleDate = $this->getDemandeAnalyseTable()->getMinMaxDateDemandeAnalyse();
		//var_dump($intervalleDate); exit();
		
		return  array ();
	}
	
	
	public function numeroFacture() {
		$derniereFacturation = $this->getFacturationTable()->getDerniereFacturation();
		if($derniereFacturation){
			return $this->creerNumeroFacturation($derniereFacturation['numero']+1);
		}else{
			return $this->creerNumeroFacturation(1);
		} 
	}
	
	
	public function vuePopupAction() {
	     
	    $idpatient = ( int ) $this->params ()->fromPost ( 'id', 0 );
	    //MISE A JOUR DE L'AGE DU PATIENT
	    //MISE A JOUR DE L'AGE DU PATIENT
	    //MISE A JOUR DE L'AGE DU PATIENT
	    $personne = $this->getPatientTable()->miseAJourAgePatient($idpatient);
	    //*******************************
	    //*******************************
	    //*******************************
	    $unPatient = $this->getPatientTable ()->getInfoPatient ( $idpatient );
	     
	    $date_naissance = $unPatient['date_naissance'];
	    if($date_naissance){ $date_naissance = (new DateHelper())->convertDate($date_naissance); }else{ $date_naissance = null;}
	     
	    $html  = "<div style='float:left;' ><div id='photo' style='float:left; margin-right:20px; margin-bottom: 10px;'> <img  src='".$this->baseUrl()."public/img/photos_patients/" . $unPatient['photo'] . "'  style='width:105px; height:105px;'></div>";
	    $html .= "<div style='margin-left:6px;'> <div style='text-decoration:none; font-size:14px; float:left; padding-right: 7px; '>Age:</div>  <div style='font-weight:bold; font-size:19px; font-family: time new romans; color: green; font-weight: bold;'>" . $unPatient['age'] . " ans</div></div></div>";
	     
	     
	    $html .= "<table>";
	     
	    $html .= "<tr>";
	    $html .= "<td><a style='text-decoration:underline; font-size:12px;'>Nom:</a><br><p style='width:280px; font-weight:bold; font-size:17px;'>" . $unPatient['nom'] . "</p></td>";
	    $html .= "</tr><tr>";
	    $html .= "<td><a style='text-decoration:underline; font-size:12px;'>Pr&eacute;nom:</a><br><p style='width:280px; font-weight:bold; font-size:17px;'>" . $unPatient['prenom'] . "</p></td>";
	    $html .= "</tr><tr>";
	    $html .= "<td><a style='text-decoration:underline; font-size:12px;'>Date de naissance:</a><br><p style='width:280px; font-weight:bold; font-size:17px;'>" . $date_naissance . "</p></td>";
	    $html .= "</tr><tr>";
	    $html .= "<td><a style='text-decoration:underline; font-size:12px;'>Adresse:</a><br><p style='width:280px; font-weight:bold; font-size:17px;'>" . $unPatient['adresse'] . "</p></td>";
	    $html .= "</tr><tr>";
	    $html .= "<td><a style='text-decoration:underline; font-size:12px;'>T&eacute;l&eacute;phone:</a><br><p style='width:280px; font-weight:bold; font-size:17px;'>" . $unPatient['telephone'] . "</p></td>";
	    $html .= "</tr>";
	     
	    $html .= "</table>";
	    $this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
	    return $this->getResponse ()->setContent ( Json::encode ( $html ) );
	     
	}
	
	protected function nbJours($debut, $fin) {
	    //60 secondes X 60 minutes X 24 heures dans une journee
	    $nbSecondes = 60*60*24;
	
	    $debut_ts = strtotime($debut);
	    $fin_ts = strtotime($fin);
	    $diff = $fin_ts - $debut_ts;
	    return ($diff / $nbSecondes);
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
	
	public function etatCivilPatientAction($idpatient) {
	    
	    //MISE A JOUR DE L'AGE DU PATIENT
	    //MISE A JOUR DE L'AGE DU PATIENT
	    //MISE A JOUR DE L'AGE DU PATIENT
	    $personne = $this->getPatientTable()->miseAJourAgePatient($idpatient);
	    //*******************************
	    //*******************************
	    //*******************************
	     
	    $patient = $this->getPatientTable()->getPatient($idpatient);
	    $personne = $this->getPersonneTable()->getPersonne($idpatient);
	    $depistage = $this->getPatientTable()->getDepistagePatient($idpatient);
	    $date_naissance = null;
	    if($personne->date_naissance){ $date_naissance = (new DateHelper())->convertDate( $personne->date_naissance ); }
	    $informations_parentales = $this->getPersonneTable()->getInfosParentales($idpatient);
	     
	    $depister = 0;
		$type = "Externe";
		$typage = "";
		
		if($depistage->current()){
			$depister = 1;
			if($depistage->current()['valide'] == 1){
				$idTypage = $depistage->current()['typage'];
				$typageHemoglobine = $this->getPatientTable()->getTypageHemoglobine($idTypage);
					
				if($depistage->current()['typepatient'] == 1){
					$type = "Interne";
					$typage = "(<span style='color: red;'>".$typageHemoglobine['designation']."</span>)" ;
				}else{
					$typage = "(".$typageHemoglobine['designation'].")" ;
				}
			}
		}
	     
	    $html ="
	  
	    <div style='width: 100%;' align='center'>
	  
	    <table style='width: 94%; height: 100px; margin-top: 2px;' >
		
			<tr style='width: 100%;' >
	  
			    <td style='width: 15%;' >
				  <img id='photo' src='".$this->baseUrl()."public/img/photos_patients/".$personne->photo."' style='width:105px; height:105px; margin-bottom: 10px; margin-top: 0px;'/>";
	     
	    //Gestion des AGE
	    if($personne->age && !$personne->date_naissance){
	    	$html .="<div style=' margin-left: 15px; margin-top: 135px; font-family: time new romans;'> Age: <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'>".$personne->age." ans </span></div>";
	    }else{
	        $aujourdhui = (new \DateTime() ) ->format('Y-m-d');
	        $age_jours = $this->nbJours($personne->date_naissance, $aujourdhui);
	        $age_annees = (int)($age_jours/365);
	        
	        if($age_annees == 0){
	        
	        	if($age_jours < 31){
	        		$html .="<div style='margin-left: 20px; margin-top: 145px; font-family: time new romans; '> Age: <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_jours." jours </span></div>";
	        	}else if($age_jours >= 31) {
	        		 
	        		$nb_mois = (int)($age_jours/31);
	        		$nb_jours = $age_jours - ($nb_mois*31);
	        		if($nb_jours == 0){
	        			$html .="<div style='margin-left: 20px; margin-top: 145px; font-family: time new romans; '> Age: <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$nb_mois."m </span></div>";
	        		}else{
	        			$html .="<div style='margin-left: 20px; margin-top: 145px; font-family: time new romans; '> Age: <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$nb_mois."m ".$nb_jours."j </span></div>";
	        		}
	        
	        	}
	        
	        }else{
	        	$age_jours = $age_jours - ($age_annees*365);
	        
	        	if($age_jours < 31){
	        
	        		if($age_annees == 1){
	        			if($age_jours == 0){
	        				$html .="<div style='margin-left: 15px; margin-top: 145px; font-family: time new romans; '> <span style='font-size: 14px;'> Age: </span> <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_annees."an </span></div>";
	        			}else{
	        				$html .="<div style='margin-left: 10px; margin-top: 145px; font-family: time new romans; '> <span style='font-size: 14px;'> Age: </span> <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_annees."an ".$age_jours." j </span></div>";
	        			}
	        		}else{
	        			if($age_jours == 0){
	        				$html .="<div style='margin-left: 15px; margin-top: 145px; font-family: time new romans; '> <span style='font-size: 14px;'> Age: </span> <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_annees."ans </span></div>";
	        			}else{
	        				$html .="<div style='margin-left: 10px; margin-top: 145px; font-family: time new romans; '> <span style='font-size: 14px;'> Age: </span> <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_annees."ans ".$age_jours."j </span></div>";
	        			}
	        		}
	        		 
	        	}else if($age_jours >= 31) {
	        		 
	        		$nb_mois = (int)($age_jours/31);
	        		$nb_jours = $age_jours - ($nb_mois*31);
	        
	        		if($age_annees == 1){
	        			if($nb_jours == 0){
	        				$html .="<div style='margin-left: 5px; margin-top: 145px; font-family: time new romans; '> <span style='font-size: 13px;'> Age: </span> <span style='font-size:18px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_annees."an ".$nb_mois."m </span></div>";
	        			}else{
	        				$html .="<div style='margin-left: 2px; margin-top: 145px; font-family: time new romans; '> <span style='font-size: 13px;'> Age: </span> <span style='font-size:17px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_annees."an ".$nb_mois."m ".$nb_jours."j </span></div>";
	        			}
	        
	        		}else{
	        			if($nb_jours == 0){
	        				$html .="<div style='margin-left: 5px; margin-top: 145px; font-family: time new romans; '> <span style='font-size: 13px;'> Age: </span> <span style='font-size:18px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_annees."ans ".$nb_mois."m </span></div>";
	        			}else{
	        				$html .="<div style='margin-left: 2px; margin-top: 145px; font-family: time new romans; '> <span style='font-size: 13px;'> Age: </span> <span style='font-size:17px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_annees."ans ".$nb_mois."m ".$nb_jours."j </span></div>";
	        			}
	        		}
	        
	        	}
	        
	        }
	        
	    }
	     
	    $html .="</td>
	  
				 <td style='width: 72%;' >
	  
					 <!-- TABLEAU DES INFORMATIONS -->
				     <!-- TABLEAU DES INFORMATIONS -->
					 <!-- TABLEAU DES INFORMATIONS -->
	    
				     <table id='etat_civil' style='width: 100%;'>
                        <tr style='width: 100%;'>
			   	           <td style='width:27%; font-family: police1;font-size: 12px;'>
			   		          <div id='aa'><a style='text-decoration: underline;'>Pr&eacute;nom</a><br><p style='font-weight: bold; font-size: 19px;'> ".$personne->prenom." </p></div>
			   	           </td>
	  
			   	           <td style='width:35%; font-family: police1;font-size: 12px; vertical-align: top;'>
			   		          <div id='aa'><a style='text-decoration: underline;'>Lieu de naissance</a><br><p style='font-weight: bold; font-size: 19px;'> ".$personne->lieu_naissance."  </p></div>
			   	           </td>
	  
			               <td style='width:38%; font-family: police1;font-size: 12px; vertical-align: top;'>
			   		          <div id='aa'><a style='text-decoration: underline;'>T&eacute;l&eacute;phone</a><br><p style='font-weight: bold; font-size: 19px;'> ".$personne->telephone." </p></div>
			   	           </td>
			            </tr>
	  
			            <tr style='width: 100%;'>
			               <td style='width:27%; font-family: police1;font-size: 12px; vertical-align: top;'>
			   		          <div id='aa'><a style='text-decoration: underline;'>Nom</a><br><p style='font-weight: bold; font-size: 19px;'> ".$personne->nom." </p></div>
			   	           </td>";
	     
	    if($depister == 0){
	        $html .="<td style='width:35%; font-family: police1;font-size: 12px; vertical-align: top;'>
			   		            <div id='aa'><a style='text-decoration: underline;'>Nationalit&eacute; origine</a><br><p style='font-weight: bold; font-size: 19px;'> ".$personne->nationalite_origine." </p></div>
			   	              </td>";
	    }else{
	         
	        $html .="<td style='width:35%; font-family: police1;font-size: 12px; vertical-align: top;'>
			   	     	        <div id='aa'><a style='text-decoration: underline;'>Ethnie</a><br><p style='font-weight: bold; font-size: 19px;'> ".$depistage->current()['ethnie']." </p></div>
			   	              </td>";
	    }
	    
	    $html .="<td style='width:38%; font-family: police1;font-size: 12px; vertical-align: top;'>
			   		          <div id='aa'><a style='text-decoration: underline;'>Email</a><br><p style='font-weight: bold; font-size: 19px;'> ".$personne->email." </p></div>
			   	           </td>
	  
			            </tr>
	  
			            <tr style='width: 100%;'>
			               <td style='width:27%; font-family: police1;font-size: 12px; vertical-align: top;'>
			   		          <div id='aa'><a style='text-decoration: underline;'>Sexe</a><br><p style='font-weight: bold; font-size: 19px;'> ".$personne->sexe." </p></div>
			   	           </td>
	  
			               <td style='width:35%; font-family: police1;font-size: 12px; vertical-align: top;'>
			   		          <div id='aa'><a style='text-decoration: underline;'>Nationalit&eacute; actuelle</a><br><p style='font-weight: bold; font-size: 19px;'> ".$personne->nationalite_actuelle." </p></div>
			   	           </td>
	  
			   	           <td style='width:38%; font-family: police1;font-size: 12px; vertical-align: top;'>
			   		           <div id='aa'><a style='text-decoration: underline;'>Profession</a><br><p style='font-weight: bold; font-size: 19px;'> ".$personne->profession." </p></div>
			   	           </td>
	  
			            </tr>
	  
			            <tr style='width: 100%;'>
			   	           <td style='width: 27%; font-family: police1;font-size: 12px; vertical-align: top;'>
			   		           <div id='aa'><a style='text-decoration: underline; '>Date de naissance</a><br>
			   		              <p style='font-weight: bold;font-size: 19px;'>
			   		              ".$date_naissance."
			   		              </p>
			   		           </div>
			   	           </td>
			               <td style='width:35%; font-family: police1;font-size: 12px; vertical-align: top;'>
			   		           <div id='aa'><a style='text-decoration: underline;'>Adresse</a><br><p style='font-weight: bold; font-size: 19px;'> ".$personne->adresse." </p></div>
			   	           </td>
	    
			   		       <td style='width:38%; padding-right: 25px; font-family: police1;font-size: 12px; vertical-align: top;'>
			   		          <div id='aa'><a style='text-decoration: underline;'>Type</a><br><p style='font-weight: bold; font-size: 19px;'> ".$type." ".$typage."</p></div>";
	    
	    if($informations_parentales){
	        $html .="<div style='width: 50px; height: 35px; float: right; margin-top: -40px; '><a href='javascript:infos_parentales(".$idpatient.");' > <img id='infos_parentales_".$idpatient."' style='float: right; cursor: pointer;' src='".$this->baseUrl()."public/images_icons/Infos_parentales.png' /> </a></div>";
	    }
	    
	    $html .="          </td>
			            </tr>
	  
                     </table>
 					 <!-- FIN TABLEAU DES INFORMATIONS -->
           			 <!-- FIN TABLEAU DES INFORMATIONS -->
			   		 <!-- FIN TABLEAU DES INFORMATIONS -->
				</td>
	  
				<td style='width: 10%;' >
				  <span style='color: white; '>
                    <img src='".$this->baseUrl()."public/img/photos_patients/".$personne->photo."' style='width:105px; height:105px; opacity: 0.09; margin-top: -20px;'/>
                    <div style='margin-top: 20px; margin-right: 0px; font-size:17px; font-family: Iskoola Pota; color: green; float: right; font-style: itali; opacity: 1;'> ".$patient->numero_dossier." </div>
                  </span>
				</td>
	  
			</tr>
		</table>
	  
		</div>";
	     
	     
	    //GESTION DES INFORMATIONS PARENTALES
	    //GESTION DES INFORMATIONS PARENTALES
	    if($informations_parentales){
	        $infos_parentales ="
	       <table style='width: 100%' class='infos_parentales_tab'>
	         <tr style='width: 100%'>
	             <td colspan='3' style='width: 100%;' > <div class='titreParentLab' > <div class='titreParents' > </div> INFOS MATERNELLES </div> </td>
	         </tr>
	         <tr>
	             <td style='width: 44%; font-family: police1;font-size: 13px; padding-top: 5px;' >
   			   		<div class='zoneColor' style='padding-left: 7px;' ><a style='text-decoration: underline; color: black; '>Pr&eacute;nom & Nom</a><br><p style='font-weight: bold; font-size: 17px; color: green; height: 25px;'> ".$informations_parentales[0]['prenom']." ".$informations_parentales[0]['nom']." </p></div>
	             </td>
	             <td style='width: 28%; font-family: police1;font-size: 13px; padding-top: 5px;' >
   			   		<div class='zoneColor' ><a style='text-decoration: underline; color: black; '>T&eacute;l&eacute;phone</a><br><p style='font-weight: bold;font-size: 17px; color: green; height: 25px;'> ".$informations_parentales[0]['telephone']." </p></div>
	             </td>
   			     <td style='width: 28%; font-family: police1;font-size: 13px; padding-top: 5px;' >
   			   		<div class='zoneColor' ><a style='text-decoration: underline; color: black; '>Fax</a><br><p style='font-weight: bold;font-size: 17px; color: green; height: 25px; '> ".$informations_parentales[0]['fax']." </p></div>
	             </td>
	         </tr>
		
	         <tr>
	             <td style='width: 44%; font-family: police1;font-size: 13px; padding-top: 5px;' >
   			   		<div class='zoneColor' style='padding-left: 7px; margin-bottom: 10px;' ><a style='text-decoration: underline; color: black; '>Profession</a><br><p style='font-weight: bold; font-size: 17px; color: green; height: 25px;'> ".$informations_parentales[0]['profession']." </p></div>
	             </td>
	             <td colspan='2' style='width: 56%; font-family: police1;font-size: 13px; padding-top: 5px;' >
   			   		<div class='zoneColor' ><a style='text-decoration: underline; color: black; '>@-Email</a><br><p style='font-weight: bold;font-size: 17px; color: green; height: 25px;'> ".$informations_parentales[0]['email']." </p></div>
	             </td>
	         </tr>
		
		
	  
   	         <tr style='width: 100%;'>
	             <td colspan='3' style='width: 100%;' > <div class='titreParentLab' > <div class='titreParents' > </div> INFOS PATERNELLES </div> </td>
	         </tr>
	         <tr>
	             <td style='width: 44%; font-family: police1;font-size: 13px; padding-top: 5px;' >
   			   		<div class='zoneColor' style='padding-left: 7px;' ><a style='text-decoration: underline; color: black; '>Pr&eacute;nom & Nom</a><br><p style='font-weight: bold; font-size: 17px; color: green; height: 25px;'> ".$informations_parentales[1]['prenom']." ".$informations_parentales[1]['nom']."</p></div>
	             </td>
	             <td style='width: 28%; font-family: police1;font-size: 13px; padding-top: 5px;' >
   			   		<div class='zoneColor' ><a style='text-decoration: underline; color: black; '>T&eacute;l&eacute;phone</a><br><p style='font-weight: bold;font-size: 17px; color: green; height: 25px;'> ".$informations_parentales[1]['telephone']." </p></div>
	             </td>
   			     <td style='width: 28%; font-family: police1;font-size: 13px; padding-top: 5px;' >
   			   		<div class='zoneColor' ><a style='text-decoration: underline; color: black; '>Fax</a><br><p style='font-weight: bold;font-size: 17px; color: green; height: 25px; '>  ".$informations_parentales[1]['fax']." </p></div>
	             </td>
	         </tr>
		
	         <tr>
	             <td style='width: 44%; font-family: police1;font-size: 13px; padding-top: 5px;' >
   			   		<div class='zoneColor' style='padding-left: 7px; margin-bottom: 10px;' ><a style='text-decoration: underline; color: black; '>Profession</a><br><p style='font-weight: bold; font-size: 17px; color: green; height: 25px;'>  ".$informations_parentales[1]['profession']." </p></div>
	             </td>
	             <td colspan='2' style='width: 56%; font-family: police1;font-size: 13px; padding-top: 5px;' >
   			   		<div class='zoneColor' ><a style='text-decoration: underline; color: black; '>@-Email</a><br><p style='font-weight: bold;font-size: 17px; color: green; height: 25px;'> ".$informations_parentales[1]['email']." </p></div>
	             </td>
	         </tr>
		
		
	       </table>
	       ";
	    
	        $html .="<script> $('.infos_parentales_tampon').html('".preg_replace("/(\r\n|\n|\r)/", " ",str_replace("'", "\'", $infos_parentales))."'); </script>";
	    
	    }
	    
	    return $html;
	}
	
	public function vueAdmissionAction() {
	    
	    $idpatient = ( int ) $this->params ()->fromPost ( 'idpatient', 0 );
	    
	    $today = new \DateTime ();
	    $dateAujourdhui = $today->format( 'Y-m-d' );
	    
	    $html = $this->etatCivilPatientAction($idpatient);
	    	
	    $html .= "<div id='titre_info_liste_analyses'>Liste des analyses demand&eacute;es</div>";
	    $html .= "<div id='barre_separateur'></div>";
	     
	    $html .= "<table style='margin-top:10px; margin-left:17.5%; width: 80%;'>";
	    $html .= "<tr style='width: 100%;'>";
	    $html .= "<td style='width: 100%; '>";
	    
	    
	    $html .="<div style='margin-right: 5px; float:right; font-size: 15px; color: gray; margin-top:10px;'><a style='text-decoration: none; cursor:pointer;' href='javascript:ChoixDesAnalyses(".$idpatient.")' ><i style='font-family: Times New Roman; font-size: 15px; color: green;'>  </i></a></div>";
	    $html .="<table class='table table-bordered tab_list_mini'  id='listeAnalyseDemandeesTa' >";
	     
	    $html .="<thead style='width: 100%;'>
				   <tr style='height:25px; width:100%; cursor:pointer;'>
				      <th id='typeA' style='cursor: pointer;'>Type</th>
					  <th id='analyseA' style='cursor: pointer;'>Analyse</th>
	                  <th id='tarifA' style='cursor: pointer; ' >Tarif (frs)</th>
				      <th id='choixA' style='' > <div style='width: 50%; float: left;'> Choix </div> <div style='width: 50%; float: left; font-size: 10px;' id='choixCheckedTout'> Tout <input type='checkbox' name='nameChoixCheckedTout'> </div> </th>
				   </tr>
			     </thead>";
	    
	    $html .="<tbody  class='liste_patient liste_analyses_demandes' style='width: 100%;'>";
	    
	    $html .="<script> 
	               var tableauIddemande = [];
	               var i = 0; 
	        
	               var tableauIddemandeChoisi = [];
	             </script>";
	    
	    $listeAnalysesDemandees = $this->getAnalyseTable()->getListeAnalysesDemandeesNonFacturees($idpatient);
	    for($i = 0 ; $i < count($listeAnalysesDemandees) ; $i++){
	         
	        $html .="<tr style='height:20px; width:100%; font-family: times new roman;'>
 					    <td id='typeA' style='font-size: 13px;'> ".$listeAnalysesDemandees[$i]['libelle']." </td>
 					    <td id='analyseA' style='font-size: 13px;'> ".$listeAnalysesDemandees[$i]['designation']." </td>
 				        <td id='tarifA' style='font-size: 15px;'> <div style='float: right;'> ".$this->prixMill($listeAnalysesDemandees[$i]['tarif'])." </div>  </td>
 				        <td id='choixA' style='font-size: 15px;'> <div style='float: left; width: 45%;' id='choixChecked".$listeAnalysesDemandees[$i]['iddemande']."' > <input type='checkbox' name='nameChoixChecked_".$listeAnalysesDemandees[$i]['iddemande']."' > <div id='cocher_".$listeAnalysesDemandees[$i]['iddemande']."' style='float: right;'> <!-- Emplacement de limage cocher--> </div> </div> </td>
 				     </tr>";
	        
	        $iddemande = $listeAnalysesDemandees[$i]['iddemande'];
	        
	        $html .="<script>
	                   tableauIddemande [i++] = ".$iddemande.";
	                   var choixAnalyseChecked".$iddemande." = $('#choixChecked".$iddemande."  input[name=\'nameChoixChecked_".$iddemande."\']');
	                   $(choixAnalyseChecked".$iddemande.").click(function(){
	             
	                       if(choixAnalyseChecked".$iddemande."[0].checked){
	                           $('#cocher_".$iddemande."').html('<img  src=\'".$this->baseUrl()."public/images_icons/tick_16.png\' >');
	                           ajouterFacturation(".$listeAnalysesDemandees[$i]['tarif'].",".$iddemande.");

	                       }else{
                               $('#cocher_".$iddemande."').html('');
                               $('#choixCheckedTout input[name=\'nameChoixCheckedTout\']').removeAttr('checked');
                               reduireFacturation(".$listeAnalysesDemandees[$i]['tarif'].",".$iddemande.");
                                   
	                       }
	                   
	                   }); 
	                 </script>";
	    
	    }
	     
	    $html .="</tbody>";
	    
	    $html .="</table>";
	    
	    
	    $html .= "</tr>";
	    $html .= "</table>";
	    
	    $html .= 
	      "<script>
	        var choixCheckedTout = $('#choixCheckedTout input[name=\'nameChoixCheckedTout\']');
	        
	        $(choixCheckedTout).click(function(){
	            tableauAnalysesSelectionnees = [];
	            if(choixCheckedTout[0].checked){
	                tarifFact = 0;
	                for(var k = 0 ; k < tableauIddemande.length ; k++){
	                    $('#choixChecked'+tableauIddemande[k]+' input').removeAttr('checked');
	                    $('#choixChecked'+tableauIddemande[k]+' input').trigger('click');
	                }
	        
	            }else{
	        	    for(var k = 0 ; k < tableauIddemande.length ; k++){
	        	        $('#choixChecked'+tableauIddemande[k]+' input').trigger('click');
	                }
	        	            
	            }
	        });
	        
	        $('#numero').val('".$this->numeroFacture()."');
	        $('#numero').css({'background':'#eee','border-bottom-width':'0px','border-top-width':'0px','border-left-width':'0px','border-right-width':'0px','font-weight':'bold','color':'green','font-family': 'Times  New Roman','font-size':'18px'});
	        $('#numero').attr('readonly',true);
	    
	        $('#organisme').css({'font-weight':'bold','color':'green','font-family': 'Times  New Roman','font-size':'13px'});
	        $('#service').css({'background':'#eee','border-bottom-width':'0px','border-top-width':'0px','border-left-width':'0px','border-right-width':'0px','font-weight':'bold','color':'green','font-family': 'Times  New Roman','font-size':'18px'});
	        $('#service').html('<option>Infirmier</option>').attr('disabled',true);
	        $('#taux').css({'font-weight':'bold','color':'#065d10','padding-left':'10px','font-family': 'Times  New Roman','font-size':'24px'});
	        $('#montant_avec_majoration').css({'background':'#eee','border-bottom-width':'0px','border-top-width':'0px','border-left-width':'0px','border-right-width':'0px','font-weight':'bold','color':'green','font-family': 'Time  New Romans','font-size':'24px'});
	        $('#montant_avec_majoration').attr('readonly',true);
	        $('#idpatient').val(".$idpatient.");
	          
	        listeAnalysesDemandees();
	        
	      </script>";

	    $this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
	    return $this->getResponse ()->setContent ( Json::encode ( $html ) );

	}

	
	//Liste des analyses facturées n'ayant pas encore de résultats
	public function listeAnalysesFactureesAction() {
		 
		$idfacturation = ( int ) $this->params ()->fromPost ( 'idfacturation', 0 );
		 
		$facturation = $this->getFacturationTable()->getFacturation( $idfacturation );
		 
		$Annee = ( new \DateTime () ) ->format( 'Y' );
	
		$html = $this->etatCivilPatientAction($facturation['idpatient']);
		 
		$infoAgentFacturation = $this->getFacturationTable()->getInfoPersonne($facturation['idemploye']);
		
		 
		$html .= "<div id='titre_info_admis'>Infos sur la facturation</div>";
		$html .= "<div id='barre_separateur'></div>";
		
		$html .="<table id='form_patient' style='margin-top:10px; margin-left:17.5%; width: 80%; margin-bottom: 10px;'>";
			
		$html .="<tr style='width: 80%; '>";
		$html .="<td style='width: 25%; vertical-align:top; margin-right:10px;'><span id='labelHeureLABEL' style='padding-left: 5px;'>Num&eacute;ro</span><br><p id='zoneChampInfo1' style='background:#f8faf8; padding-left: 10px; padding-top: 5px; font-size:19px;'> ". $facturation['numero'] ." </p></td>";
		$html .="<td style='width: 25%; vertical-align:top; margin-right:30px;'><span id='labelHeureLABEL' style='padding-left: 5px;'>Date & heure </span><br><p id='zoneChampInfo1' style='background:#f8faf8; padding-left: 10px; padding-top: 5px; font-size:19px;'> ".(new DateHelper())->convertDate($facturation['date'])." - ".$facturation['heure']." </p></td>";
		$html .="<td style='width: 35%; vertical-align:top; margin-right:10px;'><span id='labelHeureLABEL' style='padding-left: 5px;'>Effectu&eacute;e par </span><br><p id='zoneChampInfo1' style='background:#f8faf8; padding-left: 10px; padding-top: 5px; font-size:19px; '> ".$infoAgentFacturation['prenom']."  ".$infoAgentFacturation['nom']." </p></td>";
		$html .="<td style='width: 15%; vertical-align:top; margin-right:10px;'></td>";
		$html .="</tr>";
			
		$html .="</table>";
		
		
		$html .= "<div id='titre_info_admis'>Liste des analyses demand&eacute;es</div>";
		$html .= "<div id='barre_separateur'></div>";
		 
		$html .= "<table style='margin-left:17.5%; width: 80%;'>";
		$html .= "<tr style='width: 100%;'>";
		$html .= "<td style='width: 100%; '>";
	
	
		$html .="<div style='margin-right: 5px; float:right; font-size: 15px; color: gray; margin-top:10px;'><a style='text-decoration: none; cursor:pointer;' href='' ><i style='font-family: Times New Roman; font-size: 15px; color: green;'>  </i></a></div>";
		$html .="<table class='table table-bordered tab_list_mini'  id='listeAnalyseDemandeesTa' >";
		 
		$html .="<thead style='width: 100%;'>
				   <tr style='height:25px; width:100%; cursor:pointer;'>
				      <th id='typeA' style='cursor: pointer;'>Type</th>
					  <th id='analyseA' style='cursor: pointer;'>Analyse</th>
	                  <th id='tubeA' style='cursor: pointer; ' >Tube</th>
    				  <th id='autreA' style='cursor: pointer; ' >Autre</th>
				   </tr>
			     </thead>";
	
		$html .="<tbody  class='liste_patient liste_analyses_demandes' style='width: 100%;'>";
	
		$listeAnalysesDemandees = $this->getFacturationTable()->getListeAnalysesFactureesPourInfirmerie($idfacturation);
		$Prelevements = array();
		for($i = 0 ; $i < count($listeAnalysesDemandees) ; $i++){
			
 			if(!in_array($listeAnalysesDemandees[$i]['LibelleTube'], $Prelevements)){ $Prelevements [] = $listeAnalysesDemandees[$i]['LibelleTube']; }
			
			
		  
			$html .="<tr style='height:20px; width:100%; font-family: times new roman;'>
 					    <td id='typeA' style='font-size: 13px;'> ".$listeAnalysesDemandees[$i]['libelle']." </td>
  					    <td id='analyseA' style='font-size: 13px;'> ".$listeAnalysesDemandees[$i]['designation']." </td>";
			
			            if($listeAnalysesDemandees[$i]['Idtube'] == 5){
			            	$html .="<td id='tubeA' style='font-size: 15px;'> <div style='float: left; cursor: pointer;' title='Sans anticoagulant'> ".$listeAnalysesDemandees[$i]['LibelleTube']." </div>  </td>";
			            }else
			            if($listeAnalysesDemandees[$i]['Idtube'] == 6 || $listeAnalysesDemandees[$i]['Idtube'] == 7 || $listeAnalysesDemandees[$i]['Idtube'] == 8){
			            	$html .="<td id='tubeA' style='font-size: 15px;'> <div style='float: left;' > __ </div>   </td>";
			            }else{
			            	$html .="<td id='tubeA' style='font-size: 15px;'> <div style='float: left;' > ".$listeAnalysesDemandees[$i]['LibelleTube']." </div>   </td>";
			            } 

			            //Les autres qui ne sont pas des tubes
			            if($listeAnalysesDemandees[$i]['Idtube'] == 6 || $listeAnalysesDemandees[$i]['Idtube'] == 7 || $listeAnalysesDemandees[$i]['Idtube'] == 8){
			            	$html .="<td id='autreA' style='font-size: 15px;'> <div style='float: left;' > ".$listeAnalysesDemandees[$i]['LibelleTube']." </div>  </td>";
 			            }else{
 			            	$html .="<td id='autreA' style='font-size: 15px;'> <div style='float: left;' > __ </div>   </td>";
 			            }
  				        
  				     $html .="</tr>";
			 
  				     
			 
		}
		 
		$html .="</tbody>";
	
		$html .="</table>";
	
	
		$html .= "</tr>";
		$html .= "</table>";
		 
	
		$html .="<div id='titre_info_admis'>Bilan du jour <!--img id='button_pdf' style='width:15px; height:15px; float: right; margin-right: 35px; cursor: pointer;' src='".$this->baseUrl()."public/images_icons/button_pdf.png' title='Imprimer la facture' --></div>";
		$html .="<div id='barre_separateur'></div>";
		 
		$form = new BilanForm();
		
		$formRow = new FormRow();
		$formSelect = new FormSelect();
		$formText = new FormText();
		$formTextarea = new FormTextarea();
		$formHidden = new FormHidden();
			
		$html .="<form  id='formEnregistrementBilan' method='post' action='../infirmerie/enregistrer-bilan'>";
		$html .= $formHidden($form->get( 'idfacturation' )); 
		
		$html .="<table id='form_patient' style='margin-top:10px; margin-left:17.5%; width: 80%;'>
		           <tr>
		             <td class='comment-form-patient reductText' style='width: 25%; vertical-align:top; margin-right:10px; '>" . $formRow($form->get ( 'nb_tube' )) . $formText($form->get ( 'nb_tube' )) . "</td>
		             <td class='comment-form-patient reductText' style='width: 25%; vertical-align:top; margin-right:10px; '>" . $formRow($form->get ( 'date_heure' )) . $formText($form->get ( 'date_heure' )) . "</td>
		             <td class='comment-form-patient reductSelect' style='width: 17%; vertical-align:top; margin-right:10px; '>" . $formRow($form->get ( 'a_jeun' )) . $formSelect($form->get ( 'a_jeun' )) . "</td>";
		             		
		    $html .="<td class='comment-form-patient reductSelect' style='width: 33%; vertical-align:top; margin-right:10px; ' id='codageTubelabel'>";

		             		//Affichage des codes des prélèvements
		             		//Affichage des codes des prélèvements
		             		$html .="<label style='font-size: 17px; padding-bottom: 15px; margin-top: -10px;'>Codage des pr&eacute;l&egrave;vements</label><br>";
		             		$html .="<div id='codageTubePrelevement'>
 					  <p> ";
		             		
		             		$listeCodes = $this->getCodagePrelevementTable() ->getCodagesPrelevements($idfacturation);
		             			
		             		$i = 1;
		             		foreach ($listeCodes as $codes){
		             		
		             			$codage = "<i>p".$i.":</i> &nbsp;<span id='pr".$i."'> ".$codes->annee."-".$codes->numero;
		             			$LettrePrelevement = $codes->prelevement;
		             			$html .= $this->codesPrelevements($codage, $LettrePrelevement);
		             		
		             			$html .="<style> #pr".$i.":hover{font-weight: bold;}; </style>";
		             		
		             			$i++;
		             		}
		             		
		             		//----------------------------------
		             		//----------------------------------
		             				             		
		$html .="   </td>";		             		
		$html .="   </tr>";
		$html .="</table>";
		
		
		$html .="<table id='form_patient' style='margin-left:17.5%; width: 80%; border-top: 1px solid #cccccc;'>
		           <tr style='background: gree;'>
		             <td class='comment-form-patient reductSelect' style='width: 18%; vertical-align:top; margin-right:10px;'>" . $formRow($form->get ( 'difficultes' )) . $formSelect($form->get ( 'difficultes' )) . "</td>
		             <td class='comment-form-patient reductTextarea' style='background: re; width: 32%; vertical-align:top; margin-right:10px;'>" . $formRow($form->get ( 'difficultes_prelevement' )) . $formTextarea($form->get ( 'difficultes_prelevement' )) . "</td>
		             <td class='comment-form-patient reductSelect' style='width: 20%; vertical-align:top; margin-right:10px;'>" . $formRow($form->get ( 'transfuser' )) . $formSelect($form->get ( 'transfuser' )) . "</td>
			         <td class='comment-form-patient reductSelect2' style='width: 30%; vertical-align:top; margin-right:10px;'>" . $formRow($form->get ( 'moment_transfusion' )) . $formSelect($form->get ( 'moment_transfusion' )) . "</td>
		           </tr>";
		$html .="</table>";
		
		$html .="<table id='form_patient' style='margin-left:17.5%; width: 80%; border-top: 1px solid #cccccc;'>
		           <tr style='vertical-align: top; background: re;'>
					 <td class='comment-form-patient ' style='width: 30%; vertical-align:top; margin-right:0px;'>" . $formRow($form->get ( 'origine_prelevement' )) . $formText($form->get ( 'origine_prelevement' )) . "</td>
		             <td class='comment-form-patient ' style='width: 35%; vertical-align:top; margin-right:0px;'>" . $formRow($form->get ( 'diagnostic' )) . $formTextarea($form->get ( 'diagnostic' )) . "</td>
		             <td class='comment-form-patient ' style='width: 35%; vertical-align:top; margin-right:0px;'>" . $formRow($form->get ( 'traitement' )) . $formTextarea($form->get ( 'traitement' )) . "</td>
		           </tr>";
		$html .="</table>";
		
		$html .="<input type='hidden' id='anneePrelevement'       name='anneePrelevement'       style='display: none;'>";
		$html .="<input type='hidden' id='numeroOrdrePrelevement' name='numeroOrdrePrelevement' style='display: none;'>";
		$html .="<input type='hidden' id='lettrePrelevement'      name='lettrePrelevement'      style='display: none;'>";
		
		$html .="<button id='validerForm' style='display: none;'> </button>";
		
		$html .="</form>";
		
		
		$html .="<div style='color: white; opacity: 1; margin-top: -50px; margin-left:50px; width:95px; height:40px; float:left'>
	                <img  src='".$this->baseUrl()."public/images_icons/fleur1.jpg' />
	             </div>";
		 
		$html .="<script> 
				
				  $('#nb_tube').attr({'readonly':true}).val(".count($Prelevements).").css({'background':'#eee','border-bottom-width':'0px','border-top-width':'0px','border-left-width':'0px','border-right-width':'0px','font-weight':'bold','font-family': 'Times  New Roman','font-size':'22px'});;
	              $('#idfacturation').val(".$idfacturation.");
				  		
				  initForm();
				  //$('#button_pdf').click(function(){
				     //vart='".$this->baseUrl()."public/facturation/impression-facture';
				     //var formulaire = document.createElement('form');
			         //formulaire.setAttribute('action', vart);
			         //formulaire.setAttribute('method', 'POST');
			         //formulaire.setAttribute('target', '_blank');
	  
				     //var champ = document.createElement('input');
				     //champ.setAttribute('type', 'hidden');
				     //champ.setAttribute('name', 'idfacturation');
				     //champ.setAttribute('value', ".$idfacturation.");
				     //formulaire.appendChild(champ);
	
				     //formulaire.submit();
	              //});
	  
				 </script>";

		
 		$html .="<script>
 				  $('a,img,span,div').tooltip({ animation: true, html: true, placement: 'bottom', show: { effect: 'slideDown', delay: 250 } });
		         </script>";
		
		//----------------------------------
		//----------------------------------
		//----------------------------------
		
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $html ) );
	}
	
	
	public function codageAnalyse($codage, $Prelevements) {
		
		if($Prelevements == "Sec"){
			return $codage."-<span title=\'Sec\' style=\'cursor:pointer;\'>S</span></span><br>";
		}
		if($Prelevements == "Citrate"){
			return $codage."-<span title=\'Citrate\' style=\'cursor:pointer;\'>C</span></span><br>";
		}
		if($Prelevements == "HÃ©parine"){
			return $codage."-<span title=\'HÃ©parine\' style=\'cursor:pointer;\'>H</span></span><br>";
		}
		if($Prelevements == "EDTA"){
			return $codage."-<span title=\'EDTA\' style=\'cursor:pointer;\'>E</span></span><br>";
		}
		if($Prelevements == "Fluorure"){
			return $codage."-<span title=\'Fluorure\' style=\'cursor:pointer;\'>F</span></span><br>";
		}
		if($Prelevements == "Papier buvard"){
			return $codage."-<span title=\'Papier buvard\' style=\'cursor:pointer;\'>Pb</span></span><br>";
		}
		if($Prelevements == "Lame"){
			return $codage."-<span title=\'Lame\' style=\'cursor:pointer;\'>L</span></span><br>";
		}
		if($Prelevements == "Urine"){
			return $codage."-<span title=\'Urine\' style=\'cursor:pointer;\'>U</span></span><br>";
		}
		if($Prelevements == "Selles"){
			return $codage."-<span title=\'Selles\' style=\'cursor:pointer;\'>Sl</span></span><br>";
		}
		if($Prelevements == "<span style='color: red'> non determinÃ© </span>"){
			return $codage."-<span title=\'non determinÃ©\' style=\'cursor:pointer;\'>I</span></span><br>";
		}
		
	}
	
	
	public function prelevementLettreTableau($Prelevements){
		if($Prelevements == "Sec"){
			return "S";
		}
		if($Prelevements == "Citrate"){
			return "C";
		}
		if($Prelevements == "HÃ©parine"){
			return "H";
		}
		if($Prelevements == "EDTA"){
			return "E";
		}
		if($Prelevements == "Fluorure"){
			return "F";
		}
		if($Prelevements == "Papier buvard"){
			return "Pb";
		}
		if($Prelevements == "Lame"){
			return "L";
		}
		if($Prelevements == "Urine"){
			return "U";
		}
		if($Prelevements == "Selles"){
			return "Sl";
		}
		if($Prelevements == "<span style='color: red'> non determinÃ© </span>"){
			return "I";
		}
	}
	
	
	public function enregistrerBilanAction() {
		
		$user = $this->layout()->user;
		$idemploye = $user['idemploye'];
		
		$today = new \DateTime ( "now" );
		$date_enregistrement = $today->format ( 'Y-m-d H:i:s' );
		
		$nb_tube = $this->params ()->fromPost ( 'nb_tube' );
		$date_heure = $this->params ()->fromPost ( 'date_heure' );
		$a_jeun = $this->params ()->fromPost ( 'a_jeun' );
		$difficultes = $this->params ()->fromPost ( 'difficultes' );
		$difficultes_prelevement = $this->params ()->fromPost ( 'difficultes_prelevement' );
		$transfuser = $this->params ()->fromPost ( 'transfuser' );
		$moment_transfusion = $this->params ()->fromPost ( 'moment_transfusion' );
		$diagnostic = $this->params ()->fromPost ( 'diagnostic' );
		$traitement = $this->params ()->fromPost ( 'traitement' );
		$idfacturation = $this->params ()->fromPost ( 'idfacturation' );
		$origine_prelevement = $this->params ()->fromPost ( 'origine_prelevement' );
		$date_prelevement = (new DateHelper())->convertDateInAnglais( substr($date_heure, 0, 10) ); 
		
		$donnees = array (
				'nb_tube' => $nb_tube,
				'date_heure' => $date_heure,
				'date_prelevement' => $date_prelevement,
				'a_jeun' => $a_jeun,
				'difficultes' => $difficultes,
				'difficultes_prelevement' => $difficultes_prelevement,
				'transfuser' => $transfuser,
				'moment_transfusion' => $moment_transfusion,
				'origine_prelevement' => $origine_prelevement,
				'diagnostic' => $diagnostic,
				'traitement' => $traitement,
				
				'date_enregistrement' => $date_enregistrement,
				'idemploye' => $idemploye,
				'idfacturation' => $idfacturation,
		);
		
		//Ajouter le bilan du prélèvement
		$idbilan = $this->getBilanPrelevementTable()->addBilanPrelevement( $donnees );
		
	    return $this->redirect()->toRoute('infirmerie', array('action' =>'liste-patient'));
	}

	

	public function listeBilanAjaxAction() {
		$output = $this->getPatientTable ()->getListeBilansPrelevement();
		return $this->getResponse ()->setContent ( Json::encode ( $output, array (
				'enableJsonExprFinder' => true
		) ) );
	}
	
	
	public function listeBilanAction() {
	
		$this->layout ()->setTemplate ( 'layout/infirmerie' );
	
		return  array ();
	}
	
	public function codesPrelevements($codage, $Prelevements) {
	
		if($Prelevements == "S"){
			return $codage."-<span title='Sec' style='cursor:pointer;'>S</span></span><br>";
		}
		if($Prelevements == "C"){
			return $codage."-<span title='Citrate' style='cursor:pointer;'>C</span></span><br>";
		}
		if($Prelevements == "H"){
			return $codage."-<span title='HÃ©parine' style='cursor:pointer;'>H</span></span><br>";
		}
		if($Prelevements == "E"){
			return $codage."-<span title='EDTA' style='cursor:pointer;'>E</span></span><br>";
		}
		if($Prelevements == "F"){
			return $codage."-<span title='Fluorure' style='cursor:pointer;'>F</span></span><br>";
		}
		if($Prelevements == "Pb"){
			return $codage."-<span title='Papier buvard' style='cursor:pointer;'>Pb</span></span><br>";
		}
		if($Prelevements == "L"){
			return $codage."-<span title='Lame' style='cursor:pointer;'>L</span></span><br>";
		}
		if($Prelevements == "U"){
			return $codage."-<span title='Urine' style='cursor:pointer;'>U</span></span><br>";
		}
		if($Prelevements == "Sl"){
			return $codage."-<span title=\'Selles\' style=\'cursor:pointer;\'>Sl</span></span><br>";
		}
		if($Prelevements == "I"){
			return $codage."-<span title='non determinÃ©' style='cursor:pointer;'>I</span></span><br>";
		}
		
	}
	
	public function bilanAnalysesFactureesAction() {
		
		$idfacturation = ( int ) $this->params ()->fromPost ( 'idfacturation', 0 );
			
		$facturation = $this->getFacturationTable()->getFacturation( $idfacturation );
			
		$today = new \DateTime ();
		$dateAujourdhui = $today->format( 'Y-m-d' );
		
		$html = $this->etatCivilPatientAction($facturation['idpatient']);
			
			
		$html .= "<div id='titre_info_admis'>Liste des analyses demand&eacute;es</div>";
		$html .= "<div id='barre_separateur'></div>";
			
		$html .= "<table style='margin-left:17.5%; width: 80%;'>";
		$html .= "<tr style='width: 100%;'>";
		$html .= "<td style='width: 100%; '>";
		
		
		$html .="<div style='margin-right: 5px; float:right; font-size: 15px; color: gray; margin-top:10px;'><a style='text-decoration: none; cursor:pointer;' href='' ><i style='font-family: Times New Roman; font-size: 15px; color: green;'>  </i></a></div>";
		$html .="<table class='table table-bordered tab_list_mini'  id='listeAnalyseDemandeesTa' >";
			
		$html .="<thead style='width: 100%;'>
				   <tr style='height:25px; width:100%; cursor:pointer;'>
				      <th id='typeA' style='cursor: pointer;'>Type</th>
					  <th id='analyseA' style='cursor: pointer;'>Analyse</th>
	                  <th id='tubeA' style='cursor: pointer; ' >Tube</th>
    				  <th id='autreA' style='cursor: pointer; ' >Autre</th>
				   </tr>
			     </thead>";
		
		$html .="<tbody  class='liste_patient liste_analyses_demandes' style='width: 100%;'>";
		
		$listeAnalysesDemandees = $this->getFacturationTable()->getListeAnalysesFactureesPourInfirmerie($idfacturation);
		$Prelevements = array();
		for($i = 0 ; $i < count($listeAnalysesDemandees) ; $i++){
				
			if(!in_array($listeAnalysesDemandees[$i]['LibelleTube'], $Prelevements)){ $Prelevements [] = $listeAnalysesDemandees[$i]['LibelleTube']; }
				
				
		
			$html .="<tr style='height:20px; width:100%; font-family: times new roman;'>
 					    <td id='typeA' style='font-size: 13px;'> ".$listeAnalysesDemandees[$i]['libelle']." </td>
  					    <td id='analyseA' style='font-size: 13px;'> ".$listeAnalysesDemandees[$i]['designation']." </td>";
				
			if($listeAnalysesDemandees[$i]['Idtube'] == 5){
				$html .="<td id='tubeA' style='font-size: 15px;'> <div style='float: left; cursor: pointer;' title='Sans anticoagulant'> ".$listeAnalysesDemandees[$i]['LibelleTube']." </div>  </td>";
			}else
			if($listeAnalysesDemandees[$i]['Idtube'] == 6 || $listeAnalysesDemandees[$i]['Idtube'] == 7 || $listeAnalysesDemandees[$i]['Idtube'] == 8){
				$html .="<td id='tubeA' style='font-size: 15px;'> <div style='float: left;' > __ </div>   </td>";
			}else{
				$html .="<td id='tubeA' style='font-size: 15px;'> <div style='float: left;' > ".$listeAnalysesDemandees[$i]['LibelleTube']." </div>   </td>";
			}
		
			//Les autres qui ne sont pas des tubes
			if($listeAnalysesDemandees[$i]['Idtube'] == 6 || $listeAnalysesDemandees[$i]['Idtube'] == 7 || $listeAnalysesDemandees[$i]['Idtube'] == 8){
				$html .="<td id='autreA' style='font-size: 15px;'> <div style='float: left;' > ".$listeAnalysesDemandees[$i]['LibelleTube']." </div>  </td>";
			}else{
				$html .="<td id='autreA' style='font-size: 15px;'> <div style='float: left;' > __ </div>   </td>";
			}
		
			$html .="</tr>";
		
				
		
		}
			
		$html .="</tbody>";
		
		$html .="</table>";
		
		
		$html .= "</tr>";
		$html .= "</table>";
			
		$bilanPrelevement = $this->getBilanPrelevementTable() ->getBilanPrelevement($idfacturation);
		
		if($bilanPrelevement){
			
			if($bilanPrelevement->a_jeun == 1){ $a_jeun = "Oui"; }else{ $a_jeun = "Non"; }
			if($bilanPrelevement->difficultes == 1){ 
				$difficultes = "Oui"; 
				$difficultesRencontrees = $bilanPrelevement->difficultes_prelevement;
			}else{ 
				$difficultes = "Non"; 
				$difficultesRencontrees = "N&eacute;ant";
			} 
			if($bilanPrelevement->transfuser == 1){ 
				$transfuser = "Oui";
				if($bilanPrelevement->moment_transfusion == 1){
					$moment_transfusion = "Oui"; 
				
				}else{
					$moment_transfusion = "Non";
				}
			}else{ 
				$transfuser = "Non"; 
				$moment_transfusion = ""; 
			}
			
			
			$html .="<div id='titre_info_admis'>Bilan du jour </div>";
			$html .="<div id='barre_separateur'></div>";
				
			$html .="<table id='form_patient' style='margin-top:10px; margin-left:17.5%; width: 80%; margin-bottom: 10px;'>";
			
			$html .="<tr style='width: 80%; '>";
			$html .="<td style='width: 25%; vertical-align:top; margin-right:10px;'><span id='labelHeureLABEL' style='padding-left: 5px;'>Nombre de pr&eacute;l&egrave;vements</span><br><p id='zoneChampInfo1' style='background:#f8faf8; padding-left: 15px; padding-top: 5px; font-size:19px; width: 70%;'> ".count($Prelevements)." </p></td>";
			$html .="<td style='width: 25%; vertical-align:top; margin-right:30px;'><span id='labelHeureLABEL' style='padding-left: 5px;'>Date & heure </span><br><p id='zoneChampInfo1' style='background:#f8faf8; padding-left: 5px; padding-top: 5px; font-size:19px; width: 80%;'> ".$bilanPrelevement->date_heure." </p></td>";
			$html .="<td style='width: 15%; vertical-align:top; margin-right:10px;'><span id='labelHeureLABEL' style='padding-left: 5px;'>A jeun </span><br><p id='zoneChampInfo1' style='background:#f8faf8; padding-left: 15px; padding-top: 5px; font-size:19px; width: 60%;'> ".$a_jeun." </p></td>";
			$html .="<td style='width: 35%; vertical-align:top; margin-right:10px;'>";
			
			
			//Afichage des codes des prélèvements
			//Afichage des codes des prélèvements
			$html .="<span id='labelCodagePrelevement' style='padding-left: 5px; '>Codage des pr&eacute;l&egrave;vements  </span><br>";
			$html .="<div id='codageTubePrelevement'> 
					  <p> ";

			$listeCodes = $this->getCodagePrelevementTable() ->getCodagesPrelevements( $idfacturation );
			
			$i = 1; 
			foreach ($listeCodes as $codes){
				
				$codage = "<i>p".$i.":</i> &nbsp;<span id='pr".$i."'> ".$codes->annee."-".$codes->numero;
				$Prelevements = $codes->prelevement;
				$html .= $this->codesPrelevements($codage, $Prelevements);
				
				$html .="<style> #pr".$i.":hover{font-weight: bold;}; </style>";
				
				$i++;
			}
			
			$html .=" </p> 
					 </div> ";
			
			$html .="</td>";
			
			
			$html .="</td>";
			$html .="</tr>";
			
			$html .="</table>";
			
			
			$html .="<table id='form_patient' style='margin-top:10px; margin-left:17.5%; width: 80%; margin-bottom: 10px;'>";
				
			$html .="<tr style='width: 80%; '>";
			$html .="<td style='width: 15%; vertical-align:top; margin-right:10px;'><span id='labelHeureLABEL' style='padding-left: 5px;'>Difficult&eacute;s</span><br><p id='zoneChampInfo1' style='background:#f8faf8; padding-left: 15px; padding-top: 5px; font-size:19px; width: 60%;'> ".$difficultes." </p></td>";
			$html .="<td style='width: 35%; vertical-align:top; margin-right:10px;'><span id='labelHeureLABEL' style='padding-left: 5px;'>Difficult&eacute;s rencontr&eacute;es </span><br><p id='zoneChampInfo1' style='background:#f8faf8; padding-left: 5px; padding-top: 5px; font-size:17px; max-height: 120px; width: 85%;'> ".$difficultesRencontrees." </p></td>";
			$html .="<td style='width: 15%; vertical-align:top; margin-right:10px;'><span id='labelHeureLABEL' style='padding-left: 5px;'>Transfuser </span><br><p id='zoneChampInfo1' style='background:#f8faf8; padding-left: 15px; padding-top: 5px; font-size:19px; width: 60%;'> ".$transfuser." </p></td>";
			$html .="<td style='width: 35%; vertical-align:top; margin-right:10px;'>";
					
			if($moment_transfusion){
				$html .="<span id='labelHeureLABEL' style='padding-left: 5px;'>Dans les 3 derniers mois </span><br><p id='zoneChampInfo1' style='background:#f8faf8; padding-left: 15px; padding-top: 5px; font-size:19px; width: 60%;'> ".$moment_transfusion." </p>";
			}
					
			$html .="</td>";
			$html .="</tr>";
				
			$html .="</table>";
			
			$diagnostic = $bilanPrelevement->diagnostic;
			$traitement = $bilanPrelevement->traitement;
			if($diagnostic){
				$html .="<table id='form_patient' style='margin-top:10px; margin-left:17.5%; width: 80%; margin-bottom: 10px;'>";
				$html .="<td style='width: 35%; vertical-align:top; margin-right:10px;'><span id='labelHeureLABEL' style='padding-left: 5px;'>Diagnostic </span><br><p id='zoneChampInfo1' style='background:#f8faf8; padding-left: 5px; padding-top: 5px; font-size:17px; max-height: 90px; width: 95%;'> ".$diagnostic." </p></td>";
				$html .="<td style='width: 35%; vertical-align:top; margin-right:10px;'><span id='labelHeureLABEL' style='padding-left: 5px;'>Traitement </span><br><p id='zoneChampInfo1' style='background:#f8faf8; padding-left: 5px; padding-top: 5px; font-size:17px; max-height: 90px; width: 95%;'> ".$traitement." </p></td>";
				$html .="</table>";
			}

		}

		$html .="<div style='color: white; opacity: 1; margin-top: -50px; margin-left:50px; width:95px; height:40px; float:left'>
	                <img  src='".$this->baseUrl()."public/images_icons/fleur1.jpg' />
	             </div>";
			
		$html .="<script>
			        $('a,img,span,div').tooltip({ animation: true, html: true, placement: 'bottom', show: { effect: 'slideDown', delay: 250 } });
				 </script>";
		
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $html ) );
		
	}
	
	
	public function modifierBilanAnalysesAction()
	{
		$idfacturation = ( int ) $this->params ()->fromPost ( 'idfacturation', 0 );
			
		$facturation = $this->getFacturationTable()->getFacturation( $idfacturation );
			
		$today = new \DateTime ();
		$dateAujourdhui = $today->format( 'Y-m-d' );
		
		$html = $this->etatCivilPatientAction($facturation['idpatient']);
			
			
		$html .= "<div id='titre_info_admis'>Liste des analyses demand&eacute;es</div>";
		$html .= "<div id='barre_separateur'></div>";
			
		$html .= "<table style='margin-left:17.5%; width: 80%;'>";
		$html .= "<tr style='width: 100%;'>";
		$html .= "<td style='width: 100%; '>";
		
		
		$html .="<div style='margin-right: 5px; float:right; font-size: 15px; color: gray; margin-top:10px;'><a style='text-decoration: none; cursor:pointer;' href='' ><i style='font-family: Times New Roman; font-size: 15px; color: green;'>  </i></a></div>";
		$html .="<table class='table table-bordered tab_list_mini'  id='listeAnalyseDemandeesTa' >";
			
		$html .="<thead style='width: 100%;'>
				   <tr style='height:25px; width:100%; cursor:pointer;'>
				      <th id='typeA' style='cursor: pointer;'>Type</th>
					  <th id='analyseA' style='cursor: pointer;'>Analyse</th>
	                  <th id='tubeA' style='cursor: pointer; ' >Tube</th>
    				  <th id='autreA' style='cursor: pointer; ' >Autre</th>
				   </tr>
			     </thead>";
		
		$html .="<tbody  class='liste_patient liste_analyses_demandes' style='width: 100%;'>";
		
		$listeAnalysesDemandees = $this->getFacturationTable()->getListeAnalysesFactureesPourInfirmerie($idfacturation);
		$Prelevements = array();
		for($i = 0 ; $i < count($listeAnalysesDemandees) ; $i++){
				
			if(!in_array($listeAnalysesDemandees[$i]['LibelleTube'], $Prelevements)){ $Prelevements [] = $listeAnalysesDemandees[$i]['LibelleTube']; }
				
				
		
			$html .="<tr style='height:20px; width:100%; font-family: times new roman;'>
 					    <td id='typeA' style='font-size: 13px;'> ".$listeAnalysesDemandees[$i]['libelle']." </td>
  					    <td id='analyseA' style='font-size: 13px;'> ".$listeAnalysesDemandees[$i]['designation']." </td>";
				
			if($listeAnalysesDemandees[$i]['Idtube'] == 5){
				$html .="<td id='tubeA' style='font-size: 15px;'> <div style='float: left; cursor: pointer;' title='Sans anticoagulant'> ".$listeAnalysesDemandees[$i]['LibelleTube']." </div>  </td>";
			}else
			if($listeAnalysesDemandees[$i]['Idtube'] == 6 || $listeAnalysesDemandees[$i]['Idtube'] == 7 || $listeAnalysesDemandees[$i]['Idtube'] == 8){
				$html .="<td id='tubeA' style='font-size: 15px;'> <div style='float: left;' > __ </div>   </td>";
			}else{
				$html .="<td id='tubeA' style='font-size: 15px;'> <div style='float: left;' > ".$listeAnalysesDemandees[$i]['LibelleTube']." </div>   </td>";
			}
		
			//Les autres qui ne sont pas des tubes
			if($listeAnalysesDemandees[$i]['Idtube'] == 6 || $listeAnalysesDemandees[$i]['Idtube'] == 7 || $listeAnalysesDemandees[$i]['Idtube'] == 8){
				$html .="<td id='autreA' style='font-size: 15px;'> <div style='float: left;' > ".$listeAnalysesDemandees[$i]['LibelleTube']." </div>  </td>";
			}else{
				$html .="<td id='autreA' style='font-size: 15px;'> <div style='float: left;' > __ </div>   </td>";
			}
		
			$html .="</tr>";
		
		}
			
		$html .="</tbody>";
		
		$html .="</table>";
		
		
		$html .= "</tr>";
		$html .= "</table>";
			
		
		$html .="<div id='titre_info_admis'>Bilan du jour <!--img id='button_pdf' style='width:15px; height:15px; float: right; margin-right: 35px; cursor: pointer;' src='".$this->baseUrl()."public/images_icons/button_pdf.png' title='Imprimer la facture' --></div>";
		$html .="<div id='barre_separateur'></div>";
			
		$form = new BilanForm();
		
		$formRow = new FormRow();
		$formSelect = new FormSelect();
		$formText = new FormText();
		$formTextarea = new FormTextarea();
		$formHidden = new FormHidden();
			
		$html .="<form id='formEnregistrementModificationBilan' method='post' action='../infirmerie/modifier-bilan'>";
		$html .= $formHidden($form->get( 'idfacturation' ));
		
		$html .="<table id='form_patient' style='margin-top:10px; margin-left:17.5%; width: 80%;'>
		           <tr>
		             <td class='comment-form-patient reductText' style='width: 25%; vertical-align:top; margin-right:10px;'>" . $formRow($form->get ( 'nb_tube' )) . $formText($form->get ( 'nb_tube' )) . "</td>
		             <td class='comment-form-patient reductText' style='width: 25%; vertical-align:top; margin-right:10px;'>" . $formRow($form->get ( 'date_heure' )) . $formText($form->get ( 'date_heure' )) . "</td>
		             <td class='comment-form-patient reductSelect' style='width: 16%; vertical-align:top; margin-right:10px;'>" . $formRow($form->get ( 'a_jeun' )) . $formSelect($form->get ( 'a_jeun' )) . "</td>
			         <td class='comment-form-patient reductSelect' style='width: 34%; vertical-align:top; margin-right:10px;'>";

		$bilanPrelevement = $this->getBilanPrelevementTable() ->getBilanPrelevement($idfacturation);
		
		//Affichage des codes des prélèvements
		//Affichage des codes des prélèvements
 		$html .="<span id='labelCodagePrelevement' style='padding-left: 5px; '>Codage des pr&eacute;l&egrave;vements  </span><br>";
 		$html .="<div id='codageTubePrelevement'>
 					  <p> ";
		
 		$listeCodes = $this->getCodagePrelevementTable() ->getCodagesPrelevements( $idfacturation );
			
 		$i = 1;
 		foreach ($listeCodes as $codes){
		
 			$codage = "<i>p".$i.":</i> &nbsp;<span id='pr".$i."'> ".$codes->annee."-".$codes->numero;
 			$LettrePrelevement = $codes->prelevement;
 			$html .= $this->codesPrelevements($codage, $LettrePrelevement);
		
 			$html .="<style> #pr".$i.":hover{font-weight: bold;}; </style>";
		
 			$i++;
 		}
		
		//----------------------------------
		//----------------------------------
		
		
		
		$html .="    </td>
		           </tr>";
		$html .="</table>";
		
		
		$html .="<table id='form_patient' style='margin-left:17.5%; width: 80%; border-top: 1px solid #cccccc;'>
		           <tr>
		             <td class='comment-form-patient reductSelect' style='width: 18%; vertical-align:top; margin-right:10px;'>" . $formRow($form->get ( 'difficultes' )) . $formSelect($form->get ( 'difficultes' )) . "</td>
		             <td class='comment-form-patient reductTextarea' style='width: 32%; vertical-align:top; margin-right:10px; font-size: 10px;'>" . $formRow($form->get ( 'difficultes_prelevement' )) . $formTextarea($form->get ( 'difficultes_prelevement' )) . "</td>
		             <td class='comment-form-patient reductSelect' style='width: 20%; vertical-align:top; margin-right:10px;'>" . $formRow($form->get ( 'transfuser' )) . $formSelect($form->get ( 'transfuser' )) . "</td>
			         <td class='comment-form-patient reductSelect2' style='width: 30%; vertical-align:top; margin-right:10px;'>" . $formRow($form->get ( 'moment_transfusion' )) . $formSelect($form->get ( 'moment_transfusion' )) . "</td>
		           </tr>";
		$html .="</table>";
		
		
		$html .="<table id='form_patient' style='margin-left:17.5%; width: 80%; border-top: 1px solid #cccccc;'>
		           <tr>
  					 <td class='comment-form-patient ' style='width: 30%; vertical-align:top; margin-right:0px;'>" . $formRow($form->get ( 'origine_prelevement' )) . $formText($form->get ( 'origine_prelevement' )) . "</td>
		             <td class='comment-form-patient ' style='width: 35%; vertical-align:top; margin-right:10px;'>" . $formRow($form->get ( 'diagnostic' )) . $formTextarea($form->get ( 'diagnostic' )) . "</td>
		             <td class='comment-form-patient ' style='width: 35%; vertical-align:top; margin-right:10px;'>" . $formRow($form->get ( 'traitement' )) . $formTextarea($form->get ( 'traitement' )) . "</td>
		           </tr>";
		$html .="</table>";
		
		$html .="<button id='validerForm' style='display: none;'> </button>";
		
		$html .="</form>";
		
		if($bilanPrelevement){
			
			$html .="<script> 
					
				       $('#date_heure').val('".$bilanPrelevement->date_heure."');
				       $('#a_jeun').val(".(int)$bilanPrelevement->a_jeun.");	

				       var difficultes = ".(int)$bilanPrelevement->difficultes.";		
				       $('#difficultes').val(difficultes);		
				       if(difficultes == 0){ $('#difficultes_prelevement').val('NÃ©ant').attr({'readonly': true}); }	
				       else{ $('#difficultes_prelevement').val('".preg_replace("/(\r\n|\n|\r)/", " ", str_replace("'", "\'", $bilanPrelevement->difficultes_prelevement))."'); } 

				       		
				       var transfuser = ".(int)$bilanPrelevement->transfuser.";
				       $('#transfuser').val(transfuser);		
				       if(transfuser == 0){ $('.reductSelect2 select').val('').attr({'disabled':true}); }	
				       else{ $('.reductSelect2 select').val(".(int)$bilanPrelevement->moment_transfusion.").attr({'disabled':false}); } 
				       		
				       $('#origine_prelevement').val('".preg_replace("/(\r\n|\n|\r)/", " ", str_replace("'", "\'", $bilanPrelevement->origine_prelevement))."');		
				       $('#diagnostic').val('".preg_replace("/(\r\n|\n|\r)/", " ", str_replace("'", "\'", $bilanPrelevement->diagnostic))."');		
				       $('#traitement').val('".preg_replace("/(\r\n|\n|\r)/", " ", str_replace("'", "\'", $bilanPrelevement->traitement))."');		
				       		
	              	 </script>";
		}
		
		
		$html .="<div style='color: white; opacity: 1; margin-top: -50px; margin-left:50px; width:95px; height:40px; float:left'>
	                <img  src='../images_icons/fleur1.jpg' />
	             </div>";
			
		$html .="<script>
		
				  $('#nb_tube').attr({'readonly':true}).val(".count($Prelevements).").css({'background':'#eee','border-bottom-width':'0px','border-top-width':'0px','border-left-width':'0px','border-right-width':'0px','font-weight':'bold','font-family': 'Times  New Roman','font-size':'18px'});;
	              $('#idfacturation').val(".$idfacturation.");
		
				  initForm();
				  $('#button_pdf').click(function(){
				     vart='".$this->baseUrl()."public/facturation/impression-facture';
				     var formulaire = document.createElement('form');
			         formulaire.setAttribute('action', vart);
			         formulaire.setAttribute('method', 'POST');
			         formulaire.setAttribute('target', '_blank');
	 
				     var champ = document.createElement('input');
				     champ.setAttribute('type', 'hidden');
				     champ.setAttribute('name', 'idfacturation');
				     champ.setAttribute('value', ".$idfacturation.");
				     formulaire.appendChild(champ);
		
				     formulaire.submit();
	              });
	 
				  $('a,img,hass,span').tooltip({ animation: true, html: true, placement: 'bottom', show: { effect: 'slideDown', delay: 250 } });
	 
				 </script>";
			
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $html ) );
		
	}
	
	
	public function modifierBilanAction() {
	
		$user = $this->layout()->user;
		$idemploye = $user['idemploye'];
	
		$today = new \DateTime ( "now" );
		$date_enregistrement = $today->format ( 'Y-m-d H:i:s' );
	
		$nb_tube = $this->params ()->fromPost ( 'nb_tube' );
		$date_heure = $this->params ()->fromPost ( 'date_heure' );
		$a_jeun = $this->params ()->fromPost ( 'a_jeun' );
		$difficultes = $this->params ()->fromPost ( 'difficultes' );
		$difficultes_prelevement = $this->params ()->fromPost ( 'difficultes_prelevement' );
		$transfuser = $this->params ()->fromPost ( 'transfuser' );
		$moment_transfusion = $this->params ()->fromPost ( 'moment_transfusion' );
		$diagnostic = $this->params ()->fromPost ( 'diagnostic' );
		$traitement = $this->params ()->fromPost ( 'traitement' );
		$idfacturation = $this->params ()->fromPost ( 'idfacturation' );
		$date_prelevement = (new DateHelper())->convertDateInAnglais( substr($date_heure, 0, 10) );
	
		$donnees = array (
				'nb_tube' => $nb_tube,
				'date_heure' => $date_heure,
				'date_prelevement' => $date_prelevement,
				'a_jeun' => $a_jeun,
				'difficultes' => $difficultes,
				'difficultes_prelevement' => $difficultes_prelevement,
				'transfuser' => $transfuser,
				'moment_transfusion' => $moment_transfusion,
				'diagnostic' => $diagnostic,
				'traitement' => $traitement,
				'idemploye' => $idemploye,
		);
	
		//Modifier le bilan du prelevement
		$idfacturation = $this->getBilanPrelevementTable() ->updateBilanPrelevement($donnees , $idfacturation);
	
		return $this->redirect()->toRoute('infirmerie', array('action' =>'liste-bilan'));
	}
	
	public function historiqueListeBilansAction() {
	
		$this->layout ()->setTemplate ( 'layout/infirmerie' );
		
	}
	
	

	public function listeNonConformiteAjaxAction() {
		$output = $this->getPatientTable ()->getListeBilansAnalysesNonConformePrelevement();
		return $this->getResponse ()->setContent ( Json::encode ( $output, array (
				'enableJsonExprFinder' => true
		) ) );
	}
	
	public function listeNonConformeAction() {
		$this->layout ()->setTemplate ( 'layout/infirmerie' );
		
		//Il faut vérifier encore s'il y'a une non-conformité
		//Il faut vérifier encore s'il y'a une non-conformité
		if(!$this->layout()->nonConformite){
			return $this->redirect()->toRoute('infirmerie', array('action' =>'liste-patient'));
		}
		
		
		//$listeAnalysesDemandees = $this->getCodagePrelevementTable()->getListeCodagePrelevementsNonConforme(1);
		//var_dump($listeAnalysesDemandees); exit();
		
		
		$form = new BilanForm();
		
		return array(
				'form' => $form,
		);
	}
	
	
	public function listeAnalysesTrieesNonConformesAction() {

		$idpatient = ( int ) $this->params ()->fromPost ( 'idpatient', 0 );
		 
		$idfacturation = ( int ) $this->params ()->fromPost ( 'idfacturation', 0 );
		 
		$dateAujourdhui = ( new \DateTime () ) ->format( 'Y-m-d' );
		 
		$html = $this->etatCivilPatientAction($idpatient);
		 
		$bilanPrelevement = $this->getBilanPrelevementTable() ->getBilanPrelevement($idfacturation);
		 
		if($bilanPrelevement){
			 
			if($bilanPrelevement->a_jeun == 1){ $a_jeun = "Oui"; }else{ $a_jeun = "Non"; }
			if($bilanPrelevement->difficultes == 1){
				$difficultes = "Oui";
				$difficultesRencontrees = $bilanPrelevement->difficultes_prelevement;
			}else{
				$difficultes = "Non";
				$difficultesRencontrees = "N&eacute;ant";
			}
			if($bilanPrelevement->transfuser == 1){
				$transfuser = "Oui";
				if($bilanPrelevement->moment_transfusion == 1){
					$moment_transfusion = "Oui";
					 
				}else{
					$moment_transfusion = "Non";
				}
			}else{
				$transfuser = "Non";
				$moment_transfusion = "";
			}
			 
			 
			$html .="<div id='titre_info_admis'>Bilan des pr&eacute;l&egrave;vements <span class='reprendrePrelevement' style='margin-left: 15px; font-size: 17px; color: red;' onclick='popupReprisePrelevement(".$idfacturation.",".$bilanPrelevement->idbilan.");' > (Reprendre) </span></div>";
			$html .="<div id='barre_separateur'></div>";
			 
			$html .="<table id='form_patient' style='margin-top:10px; margin-left:17.5%; width: 80%; margin-bottom: 10px;'>";
			 
			$html .="<tr style='width: 80%; '>";
			$html .="<td style='width: 25%; vertical-align:top; margin-right:10px;'><span id='labelHeureLABEL' style='padding-left: 5px;'>Nombre de pr&eacute;l&egrave;vement</span><br><p id='zoneChampInfo1' style='background:#f8faf8; padding-left: 15px; padding-top: 5px; font-size:19px; width: 70% '> ".$bilanPrelevement->nb_tube." </p></td>";
			$html .="<td style='width: 25%; vertical-align:top; margin-right:30px;'><span id='labelHeureLABEL' style='padding-left: 5px;'>Date & heure </span><br><p id='zoneChampInfo1' style='background:#f8faf8; padding-left: 5px; padding-top: 5px; font-size:19px; width: 80%;'> ".$bilanPrelevement->date_heure." </p></td>";
			$html .="<td style='width: 15%; vertical-align:top; margin-right:10px;'><span id='labelHeureLABEL' style='padding-left: 5px;'>A jeun </span><br><p id='zoneChampInfo1' style='background:#f8faf8; padding-left: 15px; padding-top: 5px; font-size:19px; width: 60%;'> ".$a_jeun." </p></td>";
			$html .="<td style='width: 35%; vertical-align:top; margin-right:10px;'>";
			 
		
			//Afichage des codes des prélèvements
			//Afichage des codes des prélèvements
			$html .="<span id='labelCodagePrelevement' style='padding-left: 5px; '>Codage des pr&eacute;l&egrave;vements  </span><br>";
			$html .="<div id='codageTubePrelevement'>
					  <p> ";
		
			$listeCodes = $this->getCodagePrelevementTable() ->getCodagesPrelevements($idfacturation);
			 
			$i = 1;
			foreach ($listeCodes as $codes){
		
				$codage = "<i>p".$i.":</i> &nbsp;<span id='pr".$i."'> ".$codes->annee."-".$codes->numero;
				$Prelevements = $codes->prelevement;
				$html .= $this->codesPrelevements($codage, $Prelevements);
		
				$html .="<style> #pr".$i.":hover{font-weight: bold;}; </style>";
		
				$i++;
			}
			 
			$html .=" </p>
					</div> ";
		
		
			$html .="</td>";
			$html .="</tr>";
			 
			$html .="</table>";
			 
			 
			$html .="<table id='form_patient' style='margin-top:10px; margin-left:17.5%; width: 80%; margin-bottom: 10px;'>";
			 
			$html .="<tr style='width: 80%; '>";
			$html .="<td style='width: 15%; vertical-align:top; margin-right:10px;'><span id='labelHeureLABEL' style='padding-left: 5px;'>Difficult&eacute;s</span><br><p id='zoneChampInfo1' style='background:#f8faf8; padding-left: 15px; padding-top: 5px; font-size:19px; width: 60%;'> ".$difficultes." </p></td>";
			$html .="<td style='width: 35%; vertical-align:top; margin-right:10px;'><span id='labelHeureLABEL' style='padding-left: 5px;'>Difficult&eacute;s rencontr&eacute;es </span><br><p id='zoneChampInfo1' style='background:#f8faf8; padding-left: 5px; padding-top: 5px; font-size:17px; max-height: 120px; width: 85%:'> ".$difficultesRencontrees." </p></td>";
			$html .="<td style='width: 15%; vertical-align:top; margin-right:10px;'><span id='labelHeureLABEL' style='padding-left: 5px;'>Transfuser </span><br><p id='zoneChampInfo1' style='background:#f8faf8; padding-left: 15px; padding-top: 5px; font-size:19px; width: 60%;'> ".$transfuser." </p></td>";
			$html .="<td style='width: 35%; vertical-align:top; margin-right:10px;'>";
			 
			if($moment_transfusion){
				$html .="<span id='labelHeureLABEL' style='padding-left: 5px;'>Dans les 3 derniers mois </span><br><p id='zoneChampInfo1' style='background:#f8faf8; padding-left: 15px; padding-top: 5px; font-size:19px; width: 60%;'> ".$moment_transfusion." </p>";
			}
			 
			$html .="</td>";
			$html .="</tr>";
			 
			$html .="</table>";
			 
 		}
		 
		 
		$html .="
	    <div style='width: 100%;' align='center' >
	    <div style='width: 94%; margin-bottom: 30px; margin-top: 30px;'>
	    <div id='accordions' align='left' >
	    <div style='font-family: police2; font-size: 18px; font-weight: bold; background: #efefef;'>Liste des analyses &agrave; trier  </div>
	       <div style='min-height: 300px; border-top: 1px solid #cccccc;' id='listeAnalysesPreleveesTableau'>";
		
		 
		 
		$html .="<div style='margin-right: 5px; float:right; font-size: 15px; color: gray; margin-top:0px;'><a ><i style='font-family: Times New Roman; font-size: 15px; color: green;'>  </i></a></div>";
		$html .="<table class='table table-bordered tab_list_mini'  id='listeAnalysesPreleveesT' >";
		
		$html .="<thead style='width: 100%;'>
				   <tr style='height:25px; width:100%; cursor:pointer; '>
				      <th id='typeA' style='cursor: pointer; font-size: 13px;'>T<minus>ype</minus></th>
					  <th id='analyseA' style='cursor: pointer; font-size: 13px;'>A<minus>nalyse</minus></th>
	                  <th id='tubeA' style='cursor: pointer; font-size: 13px;' >T<minus>ube</minus></th>
    				  <th id='autreA' style='cursor: pointer; font-size: 13px;' >A<minus>utre</minus></th>
	    		      <th id='conformiteA' style='cursor: pointer; font-size: 13px;' >C<minus>onformit&eacute;</minus></th>
    				  <th id='noteConformiteA' style='cursor: pointer; font-size: 13px;' >N<minus>ote</minus></th>
				   </tr>
			     </thead>";
		 
 		$html .="<tbody  class='liste_patient liste_analyses_demandes' style='width: 100%;'>";
		 
 		$listeAnalysesDemandees = $this->getBilanPrelevementTable()->getListeAnalysesDemandeesAyantUnBilan($idfacturation);
		
 		for($i = 0 ; $i < count($listeAnalysesDemandees) ; $i++){
			 
			$html .="<tr style='height:20px; width:100%; font-family: times new roman;'>
 					    <td id='typeA' style='font-size: 11px;'> ".$listeAnalysesDemandees[$i]['libelle']." </td>
  					    <td id='analyseA' style='font-size: 11px;'> ".$listeAnalysesDemandees[$i]['designation']." </td>";
			 
			if($listeAnalysesDemandees[$i]['Idtube'] == 5){
				$html .="<td id='tubeA' style='font-size: 13px;'> <div style='float: left; cursor: pointer;' title='Sans anticoagulant'> ".$listeAnalysesDemandees[$i]['LibelleTube']." </div>  </td>";
			}else
			if($listeAnalysesDemandees[$i]['Idtube'] == 6 || $listeAnalysesDemandees[$i]['Idtube'] == 7 || $listeAnalysesDemandees[$i]['Idtube'] == 8){
				$html .="<td id='tubeA' style='font-size: 13px;'> <div style='float: left;' > __ </div>   </td>";
			}else{
				$html .="<td id='tubeA' style='font-size: 13px;'> <div style='float: left;' > ".$listeAnalysesDemandees[$i]['LibelleTube']." </div>   </td>";
			}
			 
			//Les autres qui ne sont pas des tubes
			if($listeAnalysesDemandees[$i]['Idtube'] == 6 || $listeAnalysesDemandees[$i]['Idtube'] == 7 || $listeAnalysesDemandees[$i]['Idtube'] == 8){
				$html .="<td id='autreA' style='font-size: 13px;'> <div style='float: left;' > ".$listeAnalysesDemandees[$i]['LibelleTube']." </div>  </td>";
			}else{
				$html .="<td id='autreA' style='font-size: 13px;'> <div style='float: left;' > __ </div>   </td>";
			}
			 
		
			$html .="<input type='hidden' name='analyse_".$i."'  value='".$listeAnalysesDemandees[$i]['idanalyse']."'>";
			$html .="<td id='conformiteA' style='font-size: 10px;'> <div class='conformitePrelevement'  id='conformitePrelevement_".$listeAnalysesDemandees[$i]['idanalyse']."' > <select name='conformite_".$listeAnalysesDemandees[$i]['idanalyse']."' id='conformite_".$listeAnalysesDemandees[$i]['idanalyse']."' required=true onchange='getConformite(this.value,".$listeAnalysesDemandees[$i]['idanalyse'].")'> <option value='-1'>  </option> <option value='1' >Conforme</option> <option value='0' style='font-size: 10px; color: red;' >Non conforme</option>  </select> </div> </td>";
			$html .="<td id='noteConformiteA' style='font-size: 12px;'> <div  id='noteConformite_".$listeAnalysesDemandees[$i]['idanalyse']."' >  </div> </td>";
		
		
			$html .="</tr>";
			 
 		}
		
		 
 		$html .="</tbody>";
		 
		$html .="</table>";
		 
		$html .= "<span id='nbAnalysesATrier' style='float: left;'> </span>";
		 
 		$html .="
 		        </div>
 	    </div>
	    </div>
 	    </div>";
		
 		$html .="
 	    <script>
 	      $( '#accordions' ).accordion();
 	      $('#listeAnalysesPreleveesTableau .listeDataTable').toggle(false);
 	      $('#listeAnalysesPreleveesTableau div .dataTables_paginate').css({'margin-top':'-15px'});
		
 		  $('#nbAnalysesATrier').html('".$i." analyses');
 		  $('a,img,span,div').tooltip({ animation: true, html: true, placement: 'bottom', show: { effect: 'slideDown', delay: 250 } });
 	    </script>";
		
 		

 		//SELECTION DES CONFORMITES  ---  SELECTION DES CONFORMITES ---
 		//SELECTION DES CONFORMITES  ---  SELECTION DES CONFORMITES ---
 		$tri = $this->getTriPrelevementTable() ->getPrelevementTrie($bilanPrelevement->idbilan);
 		
 		for($i = 0 ; $i < count($tri) ; $i++ ){
 				
 			$conformite = (int)$tri[$i] ->conformite;
 			$idanalyse  = (int)$tri[$i] ->idanalyse;
 				
 			//Selection de la conformité
 			//Selection de la conformité
 			$html .="
				<script>
					$('#conformite_".$idanalyse."').val('".$conformite."').attr({'disabled':true});
				</script>
				";
 				
 				
 			if($conformite == 0){
 		
 				$note_non_conformite = $tri[$i] ->note_non_conformite;
 		
 		
 				$html .="
				    <script>
			           $('#conformitePrelevement_".$idanalyse." select').css({'color':'red'});
    		           $('#noteConformite_".$idanalyse."').html('<input name=\'noteNonConformite_".$idanalyse."\' id=\'noteNonConformite_".$idanalyse."\' required=true  style=\'width: 100%; height: 100%; padding-left: 5px; font-size: 13px; font-family: time new romans;\'>');
				       $('#noteNonConformite_".$idanalyse."').val('".str_replace("'", "\'", $note_non_conformite)."').attr({'readonly': true});
    		        </script>
				    ";
 			}else{
 				$html .="
				    <script>
			           $('#noteConformite_".$idanalyse."').html('<img style=\'margin-left: 15px;\' src=\'../images_icons/tick_16.png\'  >');
				    </script>
				    ";
 			}
 				
 		}
 		
		 
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $html ) );
		
	}
	
	public function codesPrelevementsRepris($codage, $Prelevements) {
	
		if($Prelevements == "S"){
			return $codage."-<span title='Sec' style='cursor:pointer;'>S</span>-R</span><br>";
		}
		if($Prelevements == "C"){
			return $codage."-<span title='Citrate' style='cursor:pointer;'>C</span>-R</span><br>";
		}
		if($Prelevements == "H"){
			return $codage."-<span title='HÃ©parine' style='cursor:pointer;'>H</span>-R</span><br>";
		}
		if($Prelevements == "E"){
			return $codage."-<span title='EDTA' style='cursor:pointer;'>E</span>-R</span><br>";
		}
		if($Prelevements == "F"){
			return $codage."-<span title='Fluorure' style='cursor:pointer;'>F</span>-R</span><br>";
		}
		if($Prelevements == "Pb"){
			return $codage."-<span title='Papier buvard' style='cursor:pointer;'>Pb</span>-R</span><br>";
		}
		if($Prelevements == "L"){
			return $codage."-<span title='Lame' style='cursor:pointer;'>L</span>-R</span><br>";
		}
		if($Prelevements == "U"){
			return $codage."-<span title='Urine' style='cursor:pointer;'>U</span>-R</span><br>";
		}
		if($Prelevements == "Sl"){
			return $codage."-<span title=\'Selles\' style=\'cursor:pointer;\'>Sl</span>-R</span><br>";
		}
		if($Prelevements == "I"){
			return $codage."-<span title='non determinÃ©' style='cursor:pointer;'>I</span>-R</span><br>";
		}
		
	}
	
	
	public function reprisePrelevementAction() {
	
		$idbilan = ( int ) $this->params ()->fromPost ( 'idbilan', 0 );
		$idfacturation = ( int ) $this->params ()->fromPost ( 'idfacturation', 0 );
	
		
		$form = new BilanForm();
	
		$formRow = new FormRow();
		$formSelect = new FormSelect();
		$formText = new FormText();
		$formTextarea = new FormTextarea();
		$formHidden = new FormHidden();
	
		$html  ="<div id='titre_info_admis_reprise'>Bilan de la reprise </div>";
		$html .="<div id='barre_separateur_reprise'></div>";
		
		$html .="<script> var AnneeTab = []; var i=0;  </script>";
		$html .="<script> var OrdreTab = []; var j=0;  </script>";
		$html .="<script> var PrelevementTab = []; var k=0;  </script>";
			
		$html .="<form method='post' action='../infirmerie/enregistrer-bilan-repris'>";

		
		$html .="<input type='hidden' name='idbilan'  value='".$idbilan."'>";
		$html .="<input type='hidden' name='idfacturation'  value='".$idfacturation."'>";
		$html .="<input type='hidden' id='anneePrelevement'       name='anneePrelevement'       style='display: none;'>";
		$html .="<input type='hidden' id='numeroOrdrePrelevement' name='numeroOrdrePrelevement' style='display: none;'>";
		$html .="<input type='hidden' id='lettrePrelevement'      name='lettrePrelevement'      style='display: none;'>";
		
		
		$html .="<table id='form_patient' style='margin-top:10px; width: 100%; margin-bottom: 10px;'>
		           <tr>
		             <td class='comment-form-patient reductText' style='width: 25%; vertical-align:top; margin-right:10px;'>" . $formRow($form->get ( 'nb_tube' )) . $formText($form->get ( 'nb_tube' )) . "</td>
		             <td class='comment-form-patient reductText' style='width: 27%; vertical-align:top; margin-right:10px;'>" . $formRow($form->get ( 'date_heure' )) . $formText($form->get ( 'date_heure' )) . "</td>
		             <td class='comment-form-patient reductSelect' style='width: 14%; vertical-align:top; margin-right:10px;'>" . $formRow($form->get ( 'a_jeun' )) . $formSelect($form->get ( 'a_jeun' )) . "</td>
			         <td class='comment-form-patient reductSelect' style='width: 34%; vertical-align:top; '>";
	
		//Afichage des codes des prélèvements
		//Afichage des codes des prélèvements
		$html .="<span id='labelCodagePrelevement' style='padding-left: 5px; '>Codage des pr&eacute;l&egrave;vements  </span><br>";
		$html .="<div id='codageTubePrelevementReprise'>
					  <p> ";
		
		$tabCodageBilanRepris = $this->getCodagePrelevementTable()->getListeCodagePrelevementsNonConforme($idbilan);
	
		$listeCodes = $this->getCodagePrelevementTable() ->getCodagesPrelevements( $idfacturation );
		
		$i = 1;
		foreach ($listeCodes as $codes){
	
			if(in_array($codes->prelevement, $tabCodageBilanRepris)){
				$codage = "<i>p".$i.":</i> &nbsp;<span id='pr".$i."'> ".$codes->annee."-".$codes->numero;
				$Prelevements = $codes->prelevement;
				$html .= $this->codesPrelevementsRepris($codage, $Prelevements);
				
				$html .="<style> #pr".$i.":hover{font-weight: bold;}; </style>";
				
				
				//Tableau des codes à inserer dans la BD
				$html .="<script> AnneeTab[i++] = '".$codes->annee."' ; </script>";
				$html .="<script> OrdreTab[j++] = '".$codes->numero."' ; </script>";
				$html .="<script> PrelevementTab[k++] = '".$Prelevements."' ; </script>";
				
				
				$i++;
			}
			
		}
	
		$html .=" </p>
					</div> ";
	
		$html .="</td>";
		//Fin afichage des codes des prélèvements
		//Fin afichage des codes des prélèvements
	
	
		$html .="  </tr>";
		$html .="</table>";
	
		$html .="<table id='form_patient' style='margin-top:10px; width: 85%; margin-bottom: 20px;'>
		           <tr>
		             <td class='comment-form-patient reductSelect' style='width: 18%; vertical-align:top; margin-right:10px;'>" . $formRow($form->get ( 'difficultes' )) . $formSelect($form->get ( 'difficultes' )) . "</td>
		             <td class='comment-form-patient reductTextarea' style='width: 32%; vertical-align:top; margin-right:10px; font-size: 10px;'>" . $formRow($form->get ( 'difficultes_prelevement' )) . $formTextarea($form->get ( 'difficultes_prelevement' )) . "</td>
		             <td class='comment-form-patient reductSelect' style='width: 20%; vertical-align:top; margin-right:10px;'>" . $formRow($form->get ( 'transfuser' )) . $formSelect($form->get ( 'transfuser' )) . "</td>
			         <td class='comment-form-patient reductSelect2' style='width: 30%; vertical-align:top; margin-right:10px;'>" . $formRow($form->get ( 'moment_transfusion' )) . $formSelect($form->get ( 'moment_transfusion' )) . "</td>
		           </tr>";
		$html .="</table>";
	
	
		$html .="<button id='formulaireBilanRepris' style='display: none;'></button>";
		$html .="</form>";
	
	
		$html .="<div id='titre_info_admis_reprise'>Liste des analyses non conformes </div>";
		$html .="<div id='barre_separateur_reprise'></div>";
	
		$html .="<table class='table table-bordered tab_list_mini'  id='listeAnalysesPreleveesT' style='margin-top: 10px;'>";
	
		$html .="<thead style='width: 100%;'>
		           <tr style='height:25px; width:100%; cursor:pointer; '>
		             <th id='typeAR' style='cursor: pointer; font-size: 13px;'>T<minus>ype</minus></th>
		             <th id='analyseAR' style='cursor: pointer; font-size: 13px;'>A<minus>nalyse</minus></th>
		             <th id='tubeAR' style='cursor: pointer; font-size: 13px;' >T<minus>ube</minus></th>
		             <th id='autreAR' style='cursor: pointer; font-size: 13px;' >A<minus>utre</minus></th>
		             <th id='noteConformiteAR' style='cursor: pointer; font-size: 13px;' >N<minus>ote non conformit&eacute;</minus></th>
		           </tr>
		         </thead>";
			
		$html .="<tbody  class='liste_patient liste_analyses_demandes' style='width: 100%;'>";
			
		$listeAnalysesDemandees = $this->getBilanPrelevementTable()->getListeAnalysesDemandeesAyantUnBilanNonConforme($idbilan);
	
	
		for($i = 0 ; $i < count($listeAnalysesDemandees) ; $i++){
	
			$html .="<tr style='height:20px; width:100%; font-family: times new roman;'>
    		           <td id='typeAR' style='font-size: 11px;'> ".$listeAnalysesDemandees[$i]['libelle']." </td>
	    	           <td id='analyseAR' style='font-size: 11px;'> ".$listeAnalysesDemandees[$i]['designation']." </td>";
	
			if($listeAnalysesDemandees[$i]['Idtube'] == 5){
				$html .="<td id='tubeAR' style='font-size: 13px;'> <div style='float: left; cursor: pointer;' title='Sans anticoagulant'> ".$listeAnalysesDemandees[$i]['LibelleTube']." </div>  </td>";
	
			}else
			if($listeAnalysesDemandees[$i]['Idtube'] == 6 || $listeAnalysesDemandees[$i]['Idtube'] == 7 || $listeAnalysesDemandees[$i]['Idtube'] == 8){
				$html .="<td id='tubeAR' style='font-size: 13px;'> <div style='float: left;' > __ </div>   </td>";
			}else{
				$html .="<td id='tubeAR' style='font-size: 13px;'> <div style='float: left;' > ".$listeAnalysesDemandees[$i]['LibelleTube']." </div>   </td>";
			}
	
			//Les autres qui ne sont pas des tubes
			if($listeAnalysesDemandees[$i]['Idtube'] == 6 || $listeAnalysesDemandees[$i]['Idtube'] == 7 || $listeAnalysesDemandees[$i]['Idtube'] == 8){
				$html .="<td id='autreAR' style='font-size: 13px;'> <div style='float: left;' > ".$listeAnalysesDemandees[$i]['LibelleTube']." </div>  </td>";
			}else{
				$html .="<td id='autreAR' style='font-size: 13px;'> <div style='float: left;' > __ </div>   </td>";
			}
	
			$html .="<input type='hidden' name='analyseR_".$i."'  value='".$listeAnalysesDemandees[$i]['idanalyse']."'>";
			$html .="<td id='noteConformiteAR' style='font-size: 12px;'> <div  id='noteConformiteR_".$listeAnalysesDemandees[$i]['idanalyse']."' >  </div> </td>";
	
			$html .="</tr>";
	
		}
			
		$html .="</tbody>";
			
		$html .="</table>";
		
		$tri = $this->getTriPrelevementTable() ->getPrelevementsTriesNonConformes($idbilan);
			
		for($i = 0 ; $i < count($tri) ; $i++ ){
				
			$conformite = (int)$tri[$i] ->conformite;
			$idanalyse  = (int)$tri[$i] ->idanalyse;
				
			if($conformite == 0){
					
				$note_non_conformite = $tri[$i] ->note_non_conformite;
					
					
				$html .="
				    <script>
    		           $('#noteConformiteR_".$idanalyse."').html('<input name=\'noteNonConformiteR_".$idanalyse."\' id=\'noteNonConformiteR_".$idanalyse."\' required=true  style=\'width: 100%; height: 100%; padding-left: 5px; font-size: 13px; font-family: time new romans;\'>');
				       $('#noteNonConformiteR_".$idanalyse."').val('".str_replace("'", "\'", $note_non_conformite)."').attr({'readonly': true});
    		        </script>
				    ";
				
			}
				
		}
		
		
		
		$html .="<script>
          		  $('#anneePrelevement').val(AnneeTab);
		          $('#numeroOrdrePrelevement').val(OrdreTab);
		          $('#lettrePrelevement').val(PrelevementTab);

				  initForm();
				  $('#nb_tube').attr({'readonly':true}).val(".count($tabCodageBilanRepris).").css({'background':'#eee','border-bottom-width':'0px','border-top-width':'0px','border-left-width':'0px','border-right-width':'0px','font-weight':'bold','font-family': 'Times  New Roman','font-size':'18px'});;
			     </script>";
		
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $html ) );
	}
	
	
	public function enregistrerBilanReprisAction() {
	
		$user = $this->layout()->user;
		$idemploye = $user['idemploye'];
	
		$today = new \DateTime ( "now" );
		$date_enregistrement = $today->format ( 'Y-m-d H:i:s' );
	
		$nb_tube = $this->params ()->fromPost ( 'nb_tube' );
		$date_heure = $this->params ()->fromPost ( 'date_heure' );
		$a_jeun = $this->params ()->fromPost ( 'a_jeun' );
		$difficultes = $this->params ()->fromPost ( 'difficultes' );
		$difficultes_prelevement = $this->params ()->fromPost ( 'difficultes_prelevement' );
		$transfuser = $this->params ()->fromPost ( 'transfuser' );
		$moment_transfusion = $this->params ()->fromPost ( 'moment_transfusion' );
		$idbilan = $this->params ()->fromPost ( 'idbilan' , 0);
		$idfacturation = $this->params ()->fromPost ( 'idfacturation' , 0);
		
		//Infos sur le codage des prelevements
		$anneePrelevement = $this->params ()->fromPost ( 'anneePrelevement' );
		$numeroOrdrePrelevement = $this->params ()->fromPost ( 'numeroOrdrePrelevement' );
		$lettrePrelevement = $this->params ()->fromPost ( 'lettrePrelevement' );
	
		$anneePrelevement = explode(",", $anneePrelevement);
		$numeroOrdrePrelevement = explode(",", $numeroOrdrePrelevement);
		$lettrePrelevement = explode(",", $lettrePrelevement);
		
		$donnees = array (
				'nb_tube' => $nb_tube,
				'date_heure' => $date_heure,
				'a_jeun' => $a_jeun,
				'difficultes' => $difficultes,
				'difficultes_prelevement' => $difficultes_prelevement,
				'transfuser' => $transfuser,
				'moment_transfusion' => $moment_transfusion,
	
				'date_enregistrement' => $date_enregistrement,
				'idemploye' => $idemploye,
				'idbilan' => $idbilan,
		);
	
		
		//Ajouter le bilan du prélèvement repris
		$idbilanrepris = $this->getBilanPrelevementTable() ->addBilanPrelevementRepris( $donnees );
	
		//Ajouter les codes des prélèvements repris
		$this->getCodagePrelevementTable() ->addCodageAllPrelevementRepris($anneePrelevement, $numeroOrdrePrelevement, $lettrePrelevement, $date_enregistrement, $idbilanrepris, $idemploye);
	
		return $this->redirect()->toRoute('infirmerie', array('action' =>'liste-non-conforme'));
	}
	
	
	
	
	
	
	
	
	//GESTION DES CONSULTATIONS --- GESTION DES CONSULTATIONS --- GESTION DES CONSULTATIONS
	//GESTION DES CONSULTATIONS --- GESTION DES CONSULTATIONS --- GESTION DES CONSULTATIONS
	//GESTION DES CONSULTATIONS --- GESTION DES CONSULTATIONS --- GESTION DES CONSULTATIONS
	
	public function listeConsultationsAjaxAction() {
		$output = $this->getPatientTable ()->getListeConsultations();
		return $this->getResponse ()->setContent ( Json::encode ( $output, array ( 'enableJsonExprFinder' => true ) ) );
	}
	
	public function listeConsultationsAction() {
		$this->layout ()->setTemplate ( 'layout/infirmerie' );
	}
	
	public function consultationAction() {
		$this->layout ()->setTemplate ( 'layout/infirmerie' );
		$idpatient = $this->params ()->fromQuery ( 'idpatient' , 0);
		$idfacturation = $this->params ()->fromQuery ( 'idfacturation' , 0);
		$patient = $this->getPatientTable()->getPatient($idpatient);
		
		//---- GESTION DU TYPE DE PATIENT ----
		//---- GESTION DU TYPE DE PATIENT ----
		//---- GESTION DU TYPE DE PATIENT ----
		$depistage = $this->getPatientTable()->getDepistagePatient($idpatient);
		$depister = 0;
		$type = "Externe";
		$typage = "";
		
		if($depistage->current()){
			$depister = 1;
			if($depistage->current()['valide'] == 1){
				$idTypage = $depistage->current()['typage'];
				$typageHemoglobine = $this->getPatientTable()->getTypageHemoglobine($idTypage);
					
				if($depistage->current()['typepatient'] == 1){
					$type = "Interne";
					$typage = "(<span style='color: red;'>".$typageHemoglobine['designation']."</span>)" ;
				}else{
					$typage = "(".$typageHemoglobine['designation'].")" ;
				}
			}
		}
		
		//---- FIN GESTION DU TYPE DE PATIENT ----
		
		$personne = $this->getPersonneTable()->getPersonne($idpatient);
		//---- Gestion des AGES ----
		//---- Gestion des AGES ----
		//---- Gestion des AGES ----
		if($personne->age && !$personne->date_naissance){
			$age = $personne->age." ans ";
		}else{

			$aujourdhui = (new \DateTime() ) ->format('Y-m-d');
			$age_jours = $this->nbJours($personne->date_naissance, $aujourdhui);
			$age_annees = (int)($age_jours/365);
			 
			if($age_annees == 0){
				 
				if($age_jours < 31){
					$age ="<span style='font-size:18px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_jours." jours </span>";
				}else if($age_jours >= 31) {
			
					$nb_mois = (int)($age_jours/31);
					$nb_jours = $age_jours - ($nb_mois*31);
					if($nb_jours == 0){
						$age ="<span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$nb_mois."m </span>";
					}else{
						$age ="<span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$nb_mois."m ".$nb_jours."j </span>";
					}
					 
				}
				 
			}else{
				$age_jours = $age_jours - ($age_annees*365);
				 
				if($age_jours < 31){
					 
					if($age_annees == 1){
						if($age_jours == 0){
							$age ="<span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_annees."an </span>";
						}else{
							$age ="<span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_annees."an ".$age_jours." j </span>";
						}
					}else{
						if($age_jours == 0){
							$age =" <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_annees."ans </span>";
						}else{
							$age ="<span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_annees."ans ".$age_jours."j </span>";
						}
					}
			
				}else if($age_jours >= 31) {
			
					$nb_mois = (int)($age_jours/31);
					$nb_jours = $age_jours - ($nb_mois*31);
					 
					if($age_annees == 1){
						if($nb_jours == 0){
							$age ="<span style='font-size:18px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_annees."an ".$nb_mois."m </span>";
						}else{
							$html .=" <span style='font-size:17px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_annees."an ".$nb_mois."m ".$nb_jours."j </span>";
						}
						 
					}else{
						if($nb_jours == 0){
							$age ="<span style='font-size:18px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_annees."ans ".$nb_mois."m </span>";
						}else{
							$age ="<span style='font-size:17px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_annees."ans ".$nb_mois."m ".$nb_jours."j </span>";
						}
					}
					 
				}
				 
			}
			 
		}
		//---- FIN Gestion des AGES ---- ----
		//---- FIN Gestion des AGES ---- ----
		//---- FIN Gestion des AGES ---- ----
		
		$user = $this->layout()->user;
		$idinfirmier = $user['idemploye'];
		
		$data = array(
				'idpatient' => $idpatient,
				'idinfirmier' => $idinfirmier,
				'idfacturation' => $idfacturation,
		);
		
		$form = new ConsultationForm();
		$form->populateValues($data);
		
		$listeMotifConsultation = $this->getMotifAdmissionTable() ->getListeMotifConsultation();
		$listeSiege = $this->getMotifAdmissionTable() ->getListeSiege();
		
		return array(
				'idcons' => $form->get ( 'idcons' )->getValue (),
				'lesdetails' => $personne,
				'date' => $form->get ( 'date' )->getValue (),
				'heure' => $form->get ( 'heure' )->getValue (),
				'age' => $age,
				'typage' => $type.' '.$typage,
				'form' => $form,
				'patient' => $patient,
				
				'listeMotifConsultation' => $listeMotifConsultation,
				'listeSiege' => $listeSiege,
		);
	}
	
	
	public function enregistrerMotifsConstantesAction() {

		$form = new ConsultationForm ();
		$formData = $this->getRequest ()->getPost ();
		$form->setData ( $formData );
		
		$user = $this->layout()->user;
		$idemploye = $user['idemploye'];
		
		//var_dump($formData); exit();
		
		$this->getConsultationTable()->addConsultation($form, $idemploye);
		$this->getMotifAdmissionTable ()->addMotifAdmission ( $form, $idemploye );
		
		$this->getConsultationTable()->addVoieAdministration($formData);
		
		return $this->redirect()->toRoute('infirmerie', array('action' =>'liste-consultations'));
	}
	
	
	public function modifierConsultationAction() {
		$this->layout ()->setTemplate ( 'layout/infirmerie' );
		$idpatient = $this->params ()->fromQuery ( 'idpatient' , 0);
		$idcons = $this->params ()->fromQuery ( 'idcons' , 0);
		$patient = $this->getPatientTable()->getPatient($idpatient);
		
		//---- GESTION DU TYPE DE PATIENT ----
		//---- GESTION DU TYPE DE PATIENT ----
		//---- GESTION DU TYPE DE PATIENT ----
		$depistage = $this->getPatientTable()->getDepistagePatient($idpatient);
	    $depister = 0;
		$type = "Externe";
		$typage = "";
		
		if($depistage->current()){
			$depister = 1;
			if($depistage->current()['valide'] == 1){
				$idTypage = $depistage->current()['typage'];
				$typageHemoglobine = $this->getPatientTable()->getTypageHemoglobine($idTypage);
					
				if($depistage->current()['typepatient'] == 1){
					$type = "Interne";
					$typage = "(<span style='color: red;'>".$typageHemoglobine['designation']."</span>)" ;
				}else{
					$typage = "(".$typageHemoglobine['designation'].")" ;
				}
			}
		}
		//---- FIN GESTION DU TYPE DE PATIENT ----
		
		$personne = $this->getPersonneTable()->getPersonne($idpatient);
		//---- Gestion des AGE ----
		if($personne->age && !$personne->date_naissance){
			$age = $personne->age." ans ";
		}else{
			
			$aujourdhui = (new \DateTime() ) ->format('Y-m-d');
			$age_jours = $this->nbJours($personne->date_naissance, $aujourdhui);
			$age_annees = (int)($age_jours/365);
			
			if($age_annees == 0){
					
				if($age_jours < 31){
					$age ="<span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_jours." jours </span>";
				}else if($age_jours >= 31) {
						
					$nb_mois = (int)($age_jours/31);
					$nb_jours = $age_jours - ($nb_mois*31);
					if($nb_jours == 0){
						$age ="<span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$nb_mois."m </span>";
					}else{
						$age ="<span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$nb_mois."m ".$nb_jours."j </span>";
					}
			
				}
					
			}else{
				$age_jours = $age_jours - ($age_annees*365);
					
				if($age_jours < 31){
			
					if($age_annees == 1){
						if($age_jours == 0){
							$age ="<span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_annees."an </span>";
						}else{
							$age ="<span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_annees."an ".$age_jours." j </span>";
						}
					}else{
						if($age_jours == 0){
							$age ="<span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_annees."ans </span>";
						}else{
							$age ="<span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_annees."ans ".$age_jours."j </span>";
						}
					}
						
				}else if($age_jours >= 31) {
						
					$nb_mois = (int)($age_jours/31);
					$nb_jours = $age_jours - ($nb_mois*31);
			
					if($age_annees == 1){
						if($nb_jours == 0){
							$age ="<span style='font-size:18px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_annees."an ".$nb_mois."m </span>";
						}else{
							$html .="<span style='font-size:17px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_annees."an ".$nb_mois."m ".$nb_jours."j </span>";
						}
							
					}else{
						if($nb_jours == 0){
							$age ="<span style='font-size:18px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_annees."ans ".$nb_mois."m </span>";
						}else{
							$age ="<span style='font-size:17px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_annees."ans ".$nb_mois."m ".$nb_jours."j </span>";
						}
					}
			
				}
					
			}
		}
		//---- FIN GESTION DU TYPE DE PATIENT ----
		//---- FIN GESTION DU TYPE DE PATIENT ----
		//---- FIN GESTION DU TYPE DE PATIENT ----
		
		$consultation = $this->getConsultationTable()->getConsultation($idcons)->getArrayCopy();
		
		/* COMMENTER POUR LE MOMENT JUSQU'A SON UTILISATION
		 * 
		$pos = strpos($consultation['pression_arterielle'], '/') ;
		$tensionmaximale = substr($consultation['pression_arterielle'], 0, $pos);
		$tensionminimale = substr($consultation['pression_arterielle'], $pos+1);
		$data = array (
				'tensionmaximale' => $tensionmaximale,
				'tensionminimale' => $tensionminimale,
		);
		*/
		
		//POUR LES MOTIFS D'ADMISSION
		//POUR LES MOTIFS D'ADMISSION
		//POUR LES MOTIFS D'ADMISSION
		// instancier le motif d'admission et recupï¿½rer l'enregistrement
		$motif_admission = $this->getMotifAdmissionTable ()->getMotifAdmission ( $idcons );
 		$nbMotif = $this->getMotifAdmissionTable ()->nbMotifs ( $idcons );
		
		$data = array();
		$mDouleur = array(1 => 0,2 => 0,3 => 0,4 => 0);
		//POUR LES MOTIFS D'ADMISSION
		$k = 1;
		foreach ( $motif_admission as $Motifs ) {
			$le_motif_admission = $this->getMotifAdmissionTable ()->getNomMotifConsultation($Motifs ['idlistemotif'])['libelle'];
			$data ['motif_admission' . $k] = $le_motif_admission;
			
			//Recuperation des infos supplémentaires du motif douleur
			if($Motifs ['idlistemotif'] == 2){
				$mDouleur[1] = 1;
				$mDouleur[2] = $k;
			}
			
			$k ++;
		}
		
		//Siege --- Siege --- Siege
		$motif_douleur_precision = $this->getMotifAdmissionTable ()->getMotifDouleurPrecision ( $idcons );
		if($motif_douleur_precision){
			$siege_motif_douleur = $this->getMotifAdmissionTable ()->getSiegeMotifDouleur($motif_douleur_precision['siege']);
			$mDouleur[3] = $siege_motif_douleur['libelle'];
			$mDouleur[4] = $motif_douleur_precision['intensite'];
		}

		
		$form = new ConsultationForm();
		$form->populateValues($data);
		$form->populateValues($consultation);
		
		$listeMotifConsultation = $this->getMotifAdmissionTable() ->getListeMotifConsultation();
		$listeSiege = $this->getMotifAdmissionTable() ->getListeSiege();


		//RECUPERER LA LISTE DES VOIES ADMINISTRATION DES MEDICAMENTS
		$listeVoieAdministration = $this->getConsultationTable()->getVoieAdministration($idcons);
		
		
		return array(
				'idcons' => $idcons,
				'lesdetails' => $personne,
				'consultation' => $consultation,
				'nbMotifs' => $nbMotif,
				'age' => $age,
				'typage' => $type.' '.$typage,
				'form' => $form,
				'patient' => $patient,
				
				'listeMotifConsultation' => $listeMotifConsultation,
				'listeSiege' => $listeSiege,
				'mDouleur' => $mDouleur,
				'listeVoieAdministration' => $listeVoieAdministration,
				
		);
		
	}
	
	public function enregistrerModificationAction()	{

		$form = new ConsultationForm ();
		$formData = $this->getRequest ()->getPost ();
		$form->setData ( $formData );
		
		$user = $this->layout()->user;
		$idemploye = $user['idemploye'];
		
		$this->getConsultationTable()->updateConsultations($form, $idemploye);
		$this->getMotifAdmissionTable ()->updateMotifAdmission( $form, $idemploye );
		
		$this->getConsultationTable()->addVoieAdministration($formData);
		
		return $this->redirect()->toRoute('infirmerie', array('action' =>'liste-consultations'));
	} 
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	public function infosStatistiquesDepistageMensuelAction(){
		
		$listeDemande = $this->getDemandeAnalyseTable()->getDemandeAnalyse();
		$intervalleDate = $this->getDemandeAnalyseTable()->getMinMaxDateDemandeAnalyse();
		
		$tabDonneesAnnuelle = array();
		$tabAnnees = array();
		$tabMois = array();
		for($i=0 ; $i<count($listeDemande) ; $i++){
			
			$annee_naissance = $listeDemande[$i]['annee_naissance'];
			if(!in_array($annee_naissance, $tabAnnees)){
				$tabAnnees[] = $annee_naissance;
				$tabDonneesAnnuelle[$annee_naissance] = array();
				$tabMois[$annee_naissance] = array();
			}
				
			$mois_naissance = $listeDemande[$i]['mois_naissance'];
			$tabDonneesAnnuelle[$annee_naissance][] = $mois_naissance;
				
			if(!in_array($mois_naissance, $tabMois[$annee_naissance])){
				$tabMois[$annee_naissance][] = $mois_naissance;
			}
			
		}
		
		$html = '<table class="titreTableauInfosStatistiques">
				   <tr class="ligneTitreTableauInfos">
				     <td style="width: 40%; height: 40px;">P&eacute;riodes</td>
				     <td style="width: 30%; height: 40px;">Nombre de d&eacute;pist&eacute;s</td>
				     <td style="width: 30%; height: 40px;">Nombre de naissances</td>
				   </tr>
				 </table>';
		
		$nombrePatientDepistes = 0;
		$kligne = 0;
		$html .="<script> var Pile = new Array(); </script>";
		
		$html .="<div id='listeTableauInfosStatistiques'>
		           <table class='tableauInfosStatistiques'>";
		
		for($i=0 ; $i<count($tabAnnees) ; $i++){
			
			$annee = $tabAnnees[$i];
			$tabDonneesAnnee = array_count_values($tabDonneesAnnuelle[$annee]);
			$tabIndexDonnees = $tabMois[$annee];
			for($ij=0 ; $ij<count($tabIndexDonnees) ; $ij++){
				$mois = $tabIndexDonnees[$ij];
				$dernierJourMois = cal_days_in_month(CAL_GREGORIAN, $mois, $annee);
				
				
				
				if($ij==0){
					$html .='<tr style="width: 100%; " class="couleurLigne_'.$kligne.'">
				           <td class="infosPath periodeInfosLigne" style="width: 40%; height: 40px; padding-left: 15px; font-family: police2; font-size: 20px;"> du <span class="jourPrem">1<sup>er</sup></span> au <span class="jourDernier">'.$dernierJourMois.'</span> '. $this->moisEnLettre($mois).' '.$annee.' </td>
				           <td class="infosPath" style="width: 30%; height: 40px; text-align: right; padding-right: 15px; font-family: Goudy Old Style; font-size: 21px; font-weight: bold;">'.$tabDonneesAnnee[$mois].'</td>
				           <td class="infosPath" style="width: 30%; height: 40px; text-align: right; padding-right: 15px; font-family: Goudy Old Style; font-size: 14px; font-weight: bold; color: red;">A renseigner</td>
				         </tr>';
					
					$html .="<script> Pile.push({ y: ".$tabDonneesAnnee[$mois]." , label: '".$this->moisEnLettre($mois)." ".$annee."' }); </script>";
				}else{
					$html .='<tr style="width: 100%; " class="couleurLigne_'.$kligne.'">
				           <td class="infosPath periodeInfosLigne" style="width: 40%; height: 40px; padding-left: 15px; font-family: police2; font-size: 20px;"><div style="width: 28%; height: 20px; float: left; text-align: center;"> du 1<sup>er</sup> au </div>'.' '.$dernierJourMois.' '. $this->moisEnLettre($mois).' '.$annee.' </td>
				           <td class="infosPath" style="width: 30%; height: 40px; text-align: right; padding-right: 15px; font-family: Goudy Old Style; font-size: 21px; font-weight: bold;">'.$tabDonneesAnnee[$mois].'</td>
				           <td class="infosPath" style="width: 30%; height: 40px; text-align: right; padding-right: 15px; font-family: Goudy Old Style; font-size: 12px; font-weight: bold;">-----</td>
				         </tr>';
					if(($ij+1) == count($tabIndexDonnees)){
						$html .="<script> Pile.push({ y: ".$tabDonneesAnnee[$mois]." , label: '".$this->moisEnLettre($mois)." ".$annee."' }); </script>";
					}else{
						$html .="<script> Pile.push({ y: ".$tabDonneesAnnee[$mois]." , label: 'du 1er au ".$dernierJourMois." ".$this->moisEnLettre($mois)." ".$annee."' }); </script>";
					}

				}
				
				
				
				if(($kligne%2)==0){
					$html .='<script>$(".couleurLigne_'.$kligne.'").css({"background":"#f9f9f9"}); </script>';
				}
				
				$kligne++;
				$nombrePatientDepistes += $tabDonneesAnnee[$mois];
			}
			
		}
		
		$html .="  </table>
                 </div>";
     
		
		//GESTION DE LA PREMIERE ET DE LA DERNIERE LIGNE
		//GESTION DE LA PREMIERE ET DE LA DERNIERE LIGNE
		if(($kligne-1) == 0){ //S'il n y a qu'une seule ligne
			$jourPrem = substr($intervalleDate[0], 8, 2);
			$jourDern = substr($intervalleDate[1], 8, 2);
			$html .="<script> $('.couleurLigne_0 .periodeInfosLigne .jourPrem').html('".$jourPrem."'); </script>";
			$html .="<script> $('.couleurLigne_0 .periodeInfosLigne .jourDernier').html('".$jourDern."'); </script>";
		}else{
			/**premiere période **/
			$annee = substr($intervalleDate[0], 0, 4);
			$mois = (int)substr($intervalleDate[0], 5, 2);
			$jour = substr($intervalleDate[0], 8, 2);
			$dernierJourMois = cal_days_in_month(CAL_GREGORIAN, $mois, $annee);
			$html .="<script> $('.couleurLigne_0 .periodeInfosLigne').html('"."du ".$jour." au ".$dernierJourMois." ". $this->moisEnLettre($mois)." ".$annee."'); </script>";
			/**deuxième période **/
			$annee = substr($intervalleDate[1], 0, 4);
			$mois = (int)substr($intervalleDate[1], 5, 2);
			$jour = substr($intervalleDate[1], 8, 2);
			$dernierJourMois = cal_days_in_month(CAL_GREGORIAN, $mois, $annee);
			$html .="<script> $('.couleurLigne_".($kligne-1)." .periodeInfosLigne').html('"."du 1<sup>er</sup> au ".$jour." ". $this->moisEnLettre($mois)." ".$annee."'); </script>";
		}

		if(($kligne) > 10){
			$html .="<script> setTimeout(function(){ $('.affichageInfosTotalDepistage').css({'margin-right':'12px'}); }); </script>";
		}else{
			$html .="<script> setTimeout(function(){ $('.affichageInfosTotalDepistage').css('margin-right', '0px'); }); </script>";
		}
		
		$html .="<script> $('.infosPathTotalDepiste span').html('".$nombrePatientDepistes."'); </script>";
		$html .="<script> $('.champOP1 input, .champOP2 input').attr({'min':'".$intervalleDate[0]."', 'max':'".$intervalleDate[1]."'}); </script>";
		
		$control = new DateHelper();
		$html .="<script> $('#dateDebutPeriodeDiag div').html('".$control->convertDate($intervalleDate[0])."'); </script>";
		$html .="<script> $('#dateFinPeriodeDiag div').html('".$control->convertDate($intervalleDate[1])."'); </script>";
		$html .="<script> var nbkligne = ".$kligne."; </script>";
		
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $html ) );
	}
	
	public function infosStatistiquesOptionnellesDepistageMensuelAction(){
	
		$date_debut = $this->params ()->fromPost ( 'date_debut', 0 );
		$date_fin = $this->params ()->fromPost ( 'date_fin', 0 );
		
		$listeDemande = $this->getDemandeAnalyseTable()->getDemandeAnalyseParPeriode($date_debut, $date_fin);
		$intervalleDate = $this->getDemandeAnalyseTable()->getMinMaxDateDemandeAnalyse();
		
		$tabDonneesAnnuelle = array();
		$tabAnnees = array();
		$tabMois = array();
		for($i=0 ; $i<count($listeDemande) ; $i++){

			$annee_naissance = $listeDemande[$i]['annee_naissance'];
			if(!in_array($annee_naissance, $tabAnnees)){
				$tabAnnees[] = $annee_naissance;
				$tabDonneesAnnuelle[$annee_naissance] = array();
				$tabMois[$annee_naissance] = array();
			}
			
			$mois_naissance = $listeDemande[$i]['mois_naissance'];
			$tabDonneesAnnuelle[$annee_naissance][] = $mois_naissance;
			
			if(!in_array($mois_naissance, $tabMois[$annee_naissance])){
				$tabMois[$annee_naissance][] = $mois_naissance;
			}
				
		}
	
		$html = '<table class="titreTableauInfosStatistiques">
				   <tr class="ligneTitreTableauInfos">
				     <td style="width: 40%; height: 40px;">P&eacute;riodes</td>
				     <td style="width: 30%; height: 40px;">Nombre de d&eacute;pist&eacute;s</td>
				     <td style="width: 30%; height: 40px;">Nombre de naissances</td>
				   </tr>
				 </table>';
	
	
	
		$nombrePatientDepistes = 0;
		$kligne = 0;
		$html .="<script> var Pile = new Array(); </script>";
	
		$html .="<div id='listeTableauInfosStatistiques'>
		           <table class='tableauInfosStatistiques'>";
	
		for($i=0 ; $i<count($tabAnnees) ; $i++){
				
			$annee = $tabAnnees[$i];
			$tabDonneesAnnee = array_count_values($tabDonneesAnnuelle[$annee]);
			$tabIndexDonnees = $tabMois[$annee];
			for($ij=0 ; $ij<count($tabIndexDonnees) ; $ij++){
				$mois = $tabIndexDonnees[$ij];
				$dernierJourMois = cal_days_in_month(CAL_GREGORIAN, $mois, $annee);
	
				if($ij==0){
					$html .='<tr style="width: 100%; " class="couleurLigne_'.$kligne.'">
				           <td class="infosPath infoPeriodeLibelle" style="width: 40%; height: 40px; padding-left: 15px; font-family: police2; font-size: 20px;"> du <span class="jourPrem">1<sup>er</sup></span> au <span class="jourDernier">'.$dernierJourMois.'</span> '. $this->moisEnLettre($mois).' '.$annee.' </td>
				           <td class="infosPath" style="width: 30%; height: 40px; text-align: right; padding-right: 15px; font-family: Goudy Old Style; font-size: 21px; font-weight: bold;">'.$tabDonneesAnnee[$mois].'</td>
				           <td class="infosPath" style="width: 30%; height: 40px; text-align: right; padding-right: 15px; font-family: Goudy Old Style; font-size: 14px; font-weight: bold; color: red;">A renseigner</td>
				         </tr>';
					
					$html .="<script> Pile.push({ y: ".$tabDonneesAnnee[$mois]." , label: '".$this->moisEnLettre($mois)." ".$annee."' }); </script>";
				
				}else{
					$html .='<tr style="width: 100%; " class="couleurLigne_'.$kligne.'">
				           <td class="infosPath infoPeriodeLibelle" style="width: 40%; height: 40px; padding-left: 15px; font-family: police2; font-size: 20px;"><div style="width: 28%; height: 20px; float: left; text-align: center;">du 1<sup>er</sup> au </div>'.$dernierJourMois.' '. $this->moisEnLettre($mois).' '.$annee.' </td>
				           <td class="infosPath" style="width: 30%; height: 40px; text-align: right; padding-right: 15px; font-family: Goudy Old Style; font-size: 21px; font-weight: bold;">'.$tabDonneesAnnee[$mois].'</td>
				           <td class="infosPath" style="width: 30%; height: 40px; text-align: right; padding-right: 15px; font-family: Goudy Old Style; font-size: 12px; font-weight: bold;">-----</td>
				         </tr>';
					
					if(($ij+1) == count($tabIndexDonnees)){
						$html .="<script> Pile.push({ y: ".$tabDonneesAnnee[$mois]." , label: '".$this->moisEnLettre($mois)." ".$annee."' }); </script>";
					}else{
						$html .="<script> Pile.push({ y: ".$tabDonneesAnnee[$mois]." , label: 'du 1er au ".$dernierJourMois." ".$this->moisEnLettre($mois)." ".$annee."' }); </script>";
					}
					
				}
	
				if(($kligne%2)==0){
					$html .='<script>$(".couleurLigne_'.$kligne.'").css({"background":"#f9f9f9"}); </script>';
				}
				
				$kligne++;
				$nombrePatientDepistes += $tabDonneesAnnee[$mois];
			}
				
		}
		
		//GESTION DE LA PREMIERE ET DE LA DERNIERE LIGNE
		//GESTION DE LA PREMIERE ET DE LA DERNIERE LIGNE
		if(($kligne-1) == 0){
			$html .="<script> $('.couleurLigne_0 .infoPeriodeLibelle .jourPrem').html('".substr($date_debut, 8, 2)."'); </script>";
			$html .="<script> $('.couleurLigne_0 .infoPeriodeLibelle .jourDernier').html('".substr($date_fin, 8, 2)."'); </script>";
		}else
		
		if(($kligne-1) != -1){
			$dernierMois = $mois;
			/**premiere période **/
			$annee = substr($date_debut, 0, 4);
			$mois = (int)substr($date_debut, 5, 2);
			$jour = substr($date_debut, 8, 2);
			$dernierJourMois = cal_days_in_month(CAL_GREGORIAN, $mois, $annee);
			$html .="<script> $('.couleurLigne_0 .infoPeriodeLibelle').html('"."du ".$jour." au ".$dernierJourMois." ". $this->moisEnLettre($mois)." ".$annee."'); </script>";
			/**dernière période **/
			$annee = substr($date_fin, 0, 4);
			$mois = (int)substr($date_fin, 5, 2);
			$jour = substr($date_fin, 8, 2);
			$dernierJourMois = cal_days_in_month(CAL_GREGORIAN, $mois, $annee);
			if($dernierMois != $mois){ 
				$dernierJourMois = cal_days_in_month(CAL_GREGORIAN, $dernierMois, $annee);
				$html .="<script> $('.couleurLigne_".($kligne-1)." .infoPeriodeLibelle').html('"."du 1<sup>er</sup> au ".$dernierJourMois." ". $this->moisEnLettre($dernierMois)." ".$annee."'); </script>";
			}
			else{
				$html .="<script> $('.couleurLigne_".($kligne-1)." .infoPeriodeLibelle').html('"."du 1<sup>er</sup> au ".$jour." ". $this->moisEnLettre($mois)." ".$annee."'); </script>";
			}

		}
		
		if(($kligne) > 10){
			$html .="<script> setTimeout(function(){ $('.affichageInfosTotalDepistage').css({'margin-right':'12px'}); }); </script>";
		}else{
			$html .="<script> setTimeout(function(){ $('.affichageInfosTotalDepistage').css('margin-right', '0px'); }); </script>";
		}
	
		$html .="  </table>
                 </div>";
		
		$html .="<script> $('.infosPathTotalDepiste span').html('".$nombrePatientDepistes."'); </script>";
		$html .="<script> $('.champOP1 input, .champOP2 input').attr({'min':'".$intervalleDate[0]."', 'max':'".$intervalleDate[1]."'}); </script>";
		
		$control = new DateHelper();
		$html .="<script> $('#dateDebutPeriodeDiag div').html('".$control->convertDate($date_debut)."'); </script>";
		$html .="<script> $('#dateFinPeriodeDiag div').html('".$control->convertDate($date_fin)."'); </script>";
		$html .="<script> var nbkligne = ".$kligne."; </script>";
		
		
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $html ) );
	}
	
	
	
	
	
	
	
	
	public function infosStatistiquesParametreesAction($date_debut=null, $date_fin=null)
	{
		if($date_debut && $date_fin){
			$listeDemande = $this->getDemandeAnalyseTable()->getDemandeAnalyseParPeriode($date_debut, $date_fin);
		}else{
			$listeDemande = $this->getDemandeAnalyseTable()->getDemandeAnalyse();
		}
		
		$tabDonneesAnnuelle = array();
		$tabAnnees = array();
		$tabMois = array();
		for($i=0 ; $i<count($listeDemande) ; $i++){
		
			$annee_naissance = $listeDemande[$i]['annee_naissance'];
			if(!in_array($annee_naissance, $tabAnnees)){
				$tabAnnees[] = $annee_naissance;
				$tabDonneesAnnuelle[$annee_naissance] = array();
				$tabMois[$annee_naissance] = array();
			}
				
			$mois_naissance = $listeDemande[$i]['mois_naissance'];
			$tabDonneesAnnuelle[$annee_naissance][] = $mois_naissance;
				
			if(!in_array($mois_naissance, $tabMois[$annee_naissance])){
				$tabMois[$annee_naissance][] = $mois_naissance;
			}
		
		}
		
		return array($tabAnnees, $tabDonneesAnnuelle, $tabMois);
	}
	
	
	//impression des informations statistiques 
	//impression des informations statistiques
 	public function imprimerInformationsStatistiquesDepistagesAction(){
 		$user = $this->layout()->user;
 		$nomService = $user['NomService'];
 		$infosComp['dateDuJour'] = (new \DateTime ())->format( 'd/m/Y' );
 		
 		$date_debut = $this->params ()->fromPost (  'date_debut' );
 		$date_fin = $this->params ()->fromPost (  'date_fin' );
 		$periodePrelevement = array();
 		$infosStatistique = array();
 		
 		if($date_debut && $date_fin){
 			$infosStatistique = $this->infosStatistiquesParametreesAction($date_debut, $date_fin);
 			$periodePrelevement[] = $date_debut;
 			$periodePrelevement[] = $date_fin;
 		}else{
 			$intervalleDate = $this->getDemandeAnalyseTable()->getMinMaxDateDemandeAnalyse();
 			$date_debut = $intervalleDate[0];
 			$date_fin = $intervalleDate[1];
 			$periodePrelevement[] = $date_debut;
 			$periodePrelevement[] = $date_fin;
 			$infosStatistique = $this->infosStatistiquesParametreesAction($date_debut, $date_fin);
 		}

 		$pdf = new infosStatistiquePdf();
 		$pdf->SetMargins(13.5,13.5,13.5);
 		$pdf->setTabInformations($infosStatistique);
 		
 		$pdf->setNomService($nomService);
 		$pdf->setInfosComp($infosComp);
 		$pdf->setPeriodePrelevement($periodePrelevement);
 		
 		$pdf->ImpressionInfosStatistiques();
 		$pdf->Output('I');
 		
 	}
	
	
}
