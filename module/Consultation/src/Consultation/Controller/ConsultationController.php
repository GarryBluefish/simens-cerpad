<?php

namespace Consultation\Controller;

use Zend\Mvc\Controller\AbstractActionController;

use Infirmerie\View\Helper\DateHelper;
use Zend\Json\Json;
use Consultation\Form\ConsultationForm;
use Consultation\View\Helper\DocumentPdf;
use Consultation\View\Helper\DemandeAnalysePdf;
use Consultation\View\Helper\imprimerOrdonnance;
use Consultation\View\Helper\imprimerDemandesExamens;
use Consultation\View\Helper\ImprimerUnExamenDemande;
use Consultation\View\Helper\ImprimerExamensRadioDemandes;
use Consultation\View\Helper\ImprimerExamensBioDemandes;

class ConsultationController extends AbstractActionController {
	
	protected $consultation;
	protected $personneTable;
	protected $patientTable;
	protected $motifAdmissionTable;
	protected $depistageTable;
	protected $analyseAFaireTable;
	protected $analyseTable;
	protected $antecedentsFamiliauxTable;
	protected $histoireMaladieTable;
	protected $donneesExamenTable;
	protected $diagnosticConsultation;
	protected $consultationTable;
	protected $examenTable;
	protected $facturationTable;
	protected $codagePrelevement;
	protected $ordonnanceTable;
	protected $ordonConsommableTable;
	
	public function getConsultationTable() {
		if (! $this->consultation) {
			$sm = $this->getServiceLocator ();
			$this->consultation = $sm->get ( 'Infirmerie\Model\ConsultationTable' );
		}
		return $this->consultation;
	}
	
	public function getConsultationModConsTable() {
		if (! $this->consultationTable) {
			$sm = $this->getServiceLocator ();
			$this->consultationTable = $sm->get ( 'Consultation\Model\ConsultationTable' );
		}
		return $this->consultationTable;
	}
	
	public function getPersonneTable() {
		if (! $this->personneTable) {
			$sm = $this->getServiceLocator ();
			$this->personneTable = $sm->get ( 'Secretariat\Model\PersonneTable' );
		}
		return $this->personneTable;
	}
	
	public function getPatientTable() {
		if (! $this->patientTable) {
			$sm = $this->getServiceLocator ();
			$this->patientTable = $sm->get ( 'Facturation\Model\PatientTable' );
		}
		return $this->patientTable;
	}
	
	public function getMotifAdmissionTable() {
		if (! $this->motifAdmissionTable) {
			$sm = $this->getServiceLocator ();
			$this->motifAdmissionTable = $sm->get ( 'Infirmerie\Model\MotifAdmissionTable' );
		}
		return $this->motifAdmissionTable;
	}
	
	public function getDepistageTable() {
		if (! $this->depistageTable) {
			$sm = $this->getServiceLocator ();
			$this->depistageTable = $sm->get ( 'Consultation\Model\DepistageTable' );
		}
		return $this->depistageTable;
	}
	
	public function getAnalyseAFaireTable() {
		if (! $this->analyseAFaireTable) {
			$sm = $this->getServiceLocator ();
			$this->analyseAFaireTable = $sm->get ( 'Consultation\Model\AnalyseTable' );
		}
		return $this->analyseAFaireTable;
	}
	
	public function getAnalyseTable() {
		if (! $this->analyseTable) {
			$sm = $this->getServiceLocator ();
			$this->analyseTable = $sm->get ( 'Secretariat\Model\AnalyseTable' );
		}
		return $this->analyseTable;
	}
	
	public function getAntecedentsFamiliauxTable() {
		if (! $this->antecedentsFamiliauxTable) {
			$sm = $this->getServiceLocator ();
			$this->antecedentsFamiliauxTable = $sm->get ( 'Consultation\Model\AntecedentsFamiliauxTable' );
		}
		return $this->antecedentsFamiliauxTable;
	}
	
	public function getHistoireMaladieTable() {
		if (! $this->histoireMaladieTable) {
			$sm = $this->getServiceLocator ();
			$this->histoireMaladieTable = $sm->get ( 'Consultation\Model\HistoireMaladieTable' );
		}
		return $this->histoireMaladieTable;
	}
	
	public function getDonneesExamenTable() {
		if (! $this->donneesExamenTable) {
			$sm = $this->getServiceLocator ();
			$this->donneesExamenTable = $sm->get ( 'Consultation\Model\DonneesExamenTable' );
		}
		return $this->donneesExamenTable;
	}
	
	public function getDiagnosticConsultationTable() {
		if (! $this->diagnosticConsultation) {
			$sm = $this->getServiceLocator ();
			$this->diagnosticConsultation = $sm->get ( 'Consultation\Model\DiagnosticConsultationTable' );
		}
		return $this->diagnosticConsultation;
	}
	
	public function getExamenTable() {
		if (! $this->examenTable) {
			$sm = $this->getServiceLocator ();
			$this->examenTable = $sm->get ( 'Consultation\Model\ExamenTable' );
		}
		return $this->examenTable;
	}
	
	public function getFacturationTable() {
		if (! $this->facturationTable) {
			$sm = $this->getServiceLocator ();
			$this->facturationTable = $sm->get ( 'Facturation\Model\FacturationTable' );
		}
		return $this->facturationTable;
	}
	
	public function getCodagePrelevementTable() {
		if (! $this->codagePrelevement) {
			$sm = $this->getServiceLocator ();
			$this->codagePrelevement = $sm->get ( 'Infirmerie\Model\CodagePrelevementTable' );
		}
		return $this->codagePrelevement;
	}
	
	public function getOrdonnanceTable() {
		if (! $this->ordonnanceTable) {
			$sm = $this->getServiceLocator ();
			$this->ordonnanceTable = $sm->get ( 'Consultation\Model\OrdonnanceTable' );
		}
		return $this->ordonnanceTable;
	}
	
	public function getOrdonConsommableTable() {
		if (! $this->ordonConsommableTable) {
			$sm = $this->getServiceLocator ();
			$this->ordonConsommableTable = $sm->get ( 'Consultation\Model\OrdonConsommableTable' );
		}
		return $this->ordonConsommableTable;
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
	
	public function numeroFacture() {
		$derniereFacturation = $this->getFacturationTable()->getDerniereFacturation();
		if($derniereFacturation){
			return $this->creerNumeroFacturation($derniereFacturation['numero']+1);
		}else{
			return $this->creerNumeroFacturation(1);
		} 
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
				  <img id='photo' src='".$this->baseUrl()."public/img/photos_patients/".$personne->photo."' style='width:105px; height:105px; margin-bottom: 10px; margin-top: -20px;'/>";
	     
	    //Gestion des AGE
	    if($personne->age){
	        $html .="<div style=' margin-left: 15px; margin-top: 125px; font-family: time new romans; '> Age: <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$personne->age." ans </span></div>";
	    }else{
	        $aujourdhui = (new \DateTime() ) ->format('Y-m-d');
	        $age_jours = $this->nbJours($personne->date_naissance, $aujourdhui);
	        if($age_jours < 31){
	            $html .="<div style=' margin-left: 15px; margin-top: 125px; font-family: time new romans; '> Age: <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_jours." jours </span></div>";
	        }else if($age_jours >= 31) {
	             
	            $nb_mois = (int)($age_jours/30);
	            $nb_jours = $age_jours - ($nb_mois*30);
	             
	            $html .="<div style=' margin-left: 15px; margin-top: 125px; font-family: time new romans; '> Age: <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$nb_mois."m ".$nb_jours."j </span></div>";
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
                    <div style='margin-top: 20px; margin-right: 40px; font-size:17px; font-family: Iskoola Pota; color: green; float: right; font-style: italic; opacity: 1;'> N&deg;: ".$idpatient." </div>
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
	
	
	//GESTION DES CONSULTATIONS --- GESTION DES CONSULTATIONS --- GESTION DES CONSULTATIONS
	//GESTION DES CONSULTATIONS --- GESTION DES CONSULTATIONS --- GESTION DES CONSULTATIONS
	//GESTION DES CONSULTATIONS --- GESTION DES CONSULTATIONS --- GESTION DES CONSULTATIONS
	
	public function listeConsultationsAjaxAction() {
		$output = $this->getConsultationTable ()->getListeConsultations();
		return $this->getResponse ()->setContent ( Json::encode ( $output, array ( 'enableJsonExprFinder' => true ) ) );
	}
	
	
	public function listeConsultationsAction() {
		$this->layout ()->setTemplate ( 'layout/consultation' );
	}
	
	
	public function consulterAction() {
		
		//$output = $this->getConsultationModConsTable()->getHistoriqueDesConsultations(744);
		//var_dump($output); exit();
		
		
		  //DEBUT --- DEBUT --- DEBUT
		  $timestart = microtime(true);
		  //-------------------------
		
		$this->layout ()->setTemplate ( 'layout/consultation' );
	
		$user = $this->layout()->user;
		$IdDuService = $user['IdService'];
		$idmedecin = $user['idemploye']; 
		
		$idpatient = $this->params ()->fromQuery ( 'idpatient', 0 );
		$idcons = $this->params ()->fromQuery ( 'idcons' );
		$patient = $this->getPatientTable()->getPatient($idpatient);
		$informations_parentales = $this->getPersonneTable()->getInfosParentales($idpatient);
	
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
		//---- FIN GESTION DU TYPE DE PATIENT ----
		
		$personne = $this->getPersonneTable()->getPersonne($idpatient);
		$consultation = $this->getConsultationTable()->getConsultation($idcons)->getArrayCopy();
		//---- Gestion des AGE ----
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
							$age ="<span style='font-size:17px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_annees."an ".$nb_mois."m ".$nb_jours."j </span>";
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
		//---- FIN Gestion des AGE ----
		//---- FIN Gestion des AGE ----
		

		$data = array(
				'idpatient' => $idpatient,
				'idmedecin' => $idmedecin,
		);
		
		$consultation = $this->getConsultationTable()->getConsultation($idcons)->getArrayCopy();
		
		//==================================================================================
		//==================================================================================
		//==================================================================================
		// instancier le motif d'admission et recuperer l'enregistrement
		$motif_admission = $this->getMotifAdmissionTable ()->getMotifAdmission ( $idcons );
		$nbMotif = $this->getMotifAdmissionTable ()->nbMotifs ( $idcons );
		
		$data = array();
		$mDouleur = array(1 => 0,2 => 0,3 => 0,4 => 0);
		//POUR LES MOTIFS D'ADMISSION
		$k = 1;
	    foreach ( $motif_admission as $Motifs ) {
			$data ['motif_admission' . $k] = $Motifs ['idlistemotif'];
			
			//Recuperation des infos supplÈmentaires du motif douleur
			if($Motifs ['idlistemotif'] == 2){
				$mDouleur[1] = 1;
				$mDouleur[2] = $k;
			}
			
			$k ++;
		}
		
		//Siege --- Siege --- Siege
		$motif_douleur_precision = $this->getMotifAdmissionTable ()->getMotifDouleurPrecision ( $idcons );
		if($motif_douleur_precision){
			$mDouleur[3] = $motif_douleur_precision['siege'];
			$mDouleur[4] = $motif_douleur_precision['intensite'];
		}
		
		//==================================================================================
		//==================================================================================
		//==================================================================================
		$form = new ConsultationForm();
		$form->populateValues($data);
		$form->populateValues($consultation);
		
		$listeMotifConsultation = $this->getMotifAdmissionTable() ->getListeSelectMotifConsultation();
		$form->get('motif_admission1')->setvalueOptions($listeMotifConsultation);
		$form->get('motif_admission2')->setvalueOptions($listeMotifConsultation);
		$form->get('motif_admission3')->setvalueOptions($listeMotifConsultation);
		$form->get('motif_admission4')->setvalueOptions($listeMotifConsultation);
		$form->get('motif_admission5')->setvalueOptions($listeMotifConsultation);
		
		$listeSiege = $this->getMotifAdmissionTable() ->getListeSelectSiege();
		$form->get('siege')->setvalueOptions($listeSiege);
		
		//RECUPERER LA LISTE DES VOIES ADMINISTRATION DES MEDICAMENTS
		$listeVoieAdministration = $this->getConsultationTable()->getVoieAdministration($idcons);
		
		//RECUPERER LA LISTE DES ACTES
		$listeActes = $this->getConsultationTable()->getListeDesActes();
		
		//RECUPERER LES ANALYSES EFFECTUEES PAR LE PATIENT FAISANT PARTIE DES ANALYSES OBLIGATOIRES A FAIRE 
		$donneesExamensEffectues = $this->getAnalyseAFaireTable()->getAnalyseEffectuees($idpatient);

		
		
		
		
		/**
		 * Recuperer les historiques et les antecedents du patient
		 * Recuperer les historiques et les antecedents du patient
		 * Recuperer les historiques et les antecedents du patient
		 */
		/*
		 * ANTECEDENTS FAMILIAUX --- ANTECEDENTS FAMILIAUX
		*/
		$infosAntecedentsFamiliaux = $this->getAntecedentsFamiliauxTable()->getAntecedentsFamilauxParIdpatient($idpatient);
		$infosAutresMaladiesFamiliales = $this->getAntecedentsFamiliauxTable()->getAutresMaladiesFamiliales($idpatient);
		if($infosAntecedentsFamiliaux){ $form->populateValues($infosAntecedentsFamiliaux[0]); };
		if($infosAutresMaladiesFamiliales){ $form->populateValues($infosAutresMaladiesFamiliales); }
		$listeChoixStatutDrepanoEnfant = $this->getAntecedentsFamiliauxTable()->getStatutDrepanocytoseEnfant($idpatient);
		
		
		
		
		
		
		/*
		 * TRAITEMENT MEDICAMENTEUX --- TRAITEMENT MEDICAMENTEUX
		 */
		$listeMedicament = $this->getConsultationModConsTable()->listeDeTousLesMedicaments();
		$listeForme = $this->getConsultationModConsTable()->formesMedicaments();
		$listetypeQuantiteMedicament = $this->getConsultationModConsTable()->typeQuantiteMedicaments();
		//var_dump($data); exit();
		
		//var_dump($listeChoixStatutDrepanoEnfant->current()); exit();
		//FIN --- FIN --- FIN --- FIN --- FIN --- FIN --- FIN
		//$timeend = microtime(true);
		//$time = $timeend-$timestart;
		//var_dump(number_format($time,3)); exit();
		//---------------------------------------------------
		
		
		return array(
				
				'idcons' => $idcons,
				'lesdetails' => $personne,
				'date' => $consultation['date'],
				'heure' => $consultation['heure'],
				'age' => $age,
				'typage' => $type.' '.$typage,
				'nbMotifs' => $nbMotif,
				'form' => $form,
				'patient' => $patient,
				'donneesExamensEffectues' => $donneesExamensEffectues,
				
				'mDouleur' => $mDouleur,
				'listeVoieAdministration' => $listeVoieAdministration,
				'listeActesCons' => $listeActes,
				'listeMotifConsultation' => $listeMotifConsultation,
				'listeChoixStatutDrepanoEnfant' => $listeChoixStatutDrepanoEnfant,
				
				'listeMedicament' => $listeMedicament,
				'listeFormeMedicament' => $listeForme,
				'listeTypeQuantiteMedicament'  => $listetypeQuantiteMedicament,
				
				'informations_parentales' => $informations_parentales,
		);

	}
	
	public function demandesAnalysesVueAction() {
	
		$id = ( int ) $this->params ()->fromPost ( 'id', 0 );
		$idcons =  $this->params ()->fromPost ( 'idcons', 0 );
	
		/*----------------------------------------------------*/
		/*----------------------------------------------------*/
		$existeADA = 0; //Existance d'Analyses DemandÈes Aujourdhui
		$listeAnalysesDemandees = $this->getAnalyseTable()->getListeAnalysesDemandeesDansConsDP($idcons, $id);
		if($listeAnalysesDemandees){ $existeADA = 1; }
	
		/*----------------------------------------------------*/
		$listeTypesAnalyses = $this->getPatientTable()->getListeDesTypesAnalyses();
		$tabTypesAnalyses = array(0 => '');
		foreach ($listeTypesAnalyses as $listeTA){
			$tabTypesAnalyses[$listeTA['idtype']] =  $listeTA['libelle'];
		}
		/*--Ajout du dernier type 'Imagerie'--*/
		$tabTypesAnalyses[6] = 'IMAGERIE';
	
		/*----------------------------------------------------*/
		$tabListeAnalysesParType = array();
		for($i = 1 ; $i<=5 ; $i++){ // 5 est le nombre de type d'analyse
			$tabListeAnalysesParType[$i] = $this->getListeAnalysesParType($i);
		}
		//-------- Liste des examens radiologiques -----------
		$tabListeAnalysesParType[6] = $this->getListeExamensImagerie();
		/*----------------------------------------------------*/
	
		/*----------------------------------------------------*/
		/*----------------------------------------------------*/
		$verifTypageHemo = $this->getAnalyseTable()->getAnalyseTypageHemoglobineDemande($id);
	
		/*----------------------------------------------------*/
		/*----------------------------------------------------*/
		/*----------------------------------------------------*/
	
		$donnees = array('', $existeADA, $listeAnalysesDemandees, $tabTypesAnalyses, $tabListeAnalysesParType, $verifTypageHemo);
	
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $donnees ) );
	}
	
	public function getListeAnalysesParType($type)
	{
		$liste_select = "";
		foreach($this->getPatientTable()->getListeDesAnalyses($type) as $listeAnalyses){
			$liste_select.= "<option value=".$listeAnalyses['idanalyse'].">".$listeAnalyses['designation']."</option>";
		}
		return $liste_select;
	}
	
	public function getListeExamensImagerie()
	{
		$liste_select = "";
		foreach($this->getExamenTable()->getListeDesExamens() as $listeExamens){
			$liste_select.= "<option id='examen_".$listeExamens['idexamen']."' value='0,".$listeExamens['idexamen']."'>".$listeExamens['designation']."</option>";
		}
		return $liste_select;
	}
	
	public function getListeAnalysesAction()
	{
		$id = (int)$this->params()->fromPost ('id');
		$liste_select = "";
		if($id == 6){
			foreach($this->getPatientTable()->getListeDesExamenImagerie() as $listeExamens){
				$liste_select.= "<option id='examen_".$listeExamens['idexamen']."' value='0,".$listeExamens['idexamen']."'>".$listeExamens['designation']."</option>";
			}
		}else{
			foreach($this->getPatientTable()->getListeDesAnalyses($id) as $listeAnalyses){
				$liste_select.= "<option value='".$listeAnalyses['idanalyse']."'>".$listeAnalyses['designation']."</option>";
			}
		}
		$this->getResponse()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html' );
		return $this->getResponse ()->setContent(Json::encode ( $liste_select));
	}
	
	public function getTarifAnalyseAction()
	{
		$id = (int)$this->params()->fromPost ('id');
	
		$tarif = $this->getPatientTable()->getTarifAnalyse($id);
		$tarifString = $this->prixMill( $tarif );
	
		$html = array((int)$tarif, $tarifString);
	
		$this->getResponse()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html' );
		return $this->getResponse ()->setContent(Json::encode ( $html));
		
	}
	
	/**
	 * GESTION DE LA FACTURATION --- GESTION DE LA FACTURATION --- GESTION DE LA FACTURATION
	 * Facturer automatiquement par le mÈdecin puisqu'elle est gratuite pour les patients internes
	 */
	public function facturationAnalysesDemandees($tabDonnees, $idmedecin, $liste_demandes_analyses)
	{
		$today = new \DateTime ( "now" );
		$date = $today->format ( 'Y-m-d' );
		$heure = $today->format ( 'H:i:s' );
		$date_enregistrement = $today->format ( 'Y-m-d H:i:s' );
		
		$donnees = array (
				'idpatient' => $tabDonnees['idpatient'],
				'montant' => 0,
				'numero' => '00000000000',
				'date' => $date,
				'heure' => $heure,
				'date_enregistrement' => $date_enregistrement,
				'idemploye' => $idmedecin,
				'id_type_facturation' => 1,
		);
		 
		//Ajouter la facturation
		$idfacturation = $this->getFacturationTable() ->addFacturation( $donnees );
		
		//Ajouter la liste des analyses pour lesquelles le patient est admis ‡ l'infirmerie pour prÈlËvement
		$this->getFacturationTable() ->addAnalyses( $idfacturation , $liste_demandes_analyses );
		
		//Ajouter la liste des codes des tubes et des autres instruments de prÈlËvements
		$this->creerCodePrelevementAction($idfacturation);
		 
	}
	

	//**********************************************
	//**********************************************
	//**********************************************
	//GESTION DES CODAGES DES TUBES DES PRELEVEMENTS
	//GESTION DES CODAGES DES TUBES DES PRELEVEMENTS
	//GESTION DES CODAGES DES TUBES DES PRELEVEMENTS
	//**********************************************
	//**********************************************
	//**********************************************
	public function creerCodePrelevementAction($idfacturation) {
	
		$Annee = ( new \DateTime () ) ->format( 'Y' );
	
		$listeAnalysesDemandees = $this->getFacturationTable()->getListeAnalysesFactureesPourInfirmerie($idfacturation);
		$Prelevements = array();
		for($i = 0 ; $i < count($listeAnalysesDemandees) ; $i++){
	
			if(!in_array($listeAnalysesDemandees[$i]['LibelleTube'], $Prelevements)){ $Prelevements [] = $listeAnalysesDemandees[$i]['LibelleTube']; }
	
		}
	
		$dernierCodePrelevement = $this->getCodagePrelevementTable() ->getDernierPrelevement($Annee);
	
		if($dernierCodePrelevement){
			$numeroOrdreSuivant = $this->creerNumeroOrdrePrelevement($dernierCodePrelevement['numero']+1);
		}else{
			$numeroOrdreSuivant = $this->creerNumeroOrdrePrelevement(1);
		}
	
		$anneePrelevement = array();
		$numeroOrdrePrelevement = array();
		$lettrePrelevement = array();
	
		for( $i = 0 ; $i < count($Prelevements) ; $i++ ){
	
			//Tableau des codes ‡ inserer dans la BD
			$anneePrelevement [] = $Annee;
			$numeroOrdrePrelevement [] = $numeroOrdreSuivant;
			$lettrePrelevement [] = $this->prelevementLettreTableau($Prelevements[$i]);
				
		}
	
		$user = $this->layout()->user;
		$idemploye = $user['idemploye'];
	
		$today = new \DateTime ( "now" );
		$date_enregistrement = $today->format ( 'Y-m-d H:i:s' );
	
		//Ajouter les codes des prÈlËvements
		$this->getCodagePrelevementTable() ->addCodagePrelevementLorsDeLaFacturation($anneePrelevement, $numeroOrdrePrelevement, $lettrePrelevement, $date_enregistrement, $idfacturation, $idemploye);
	
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
	
	public function prelevementLettreTableau($Prelevements){
		if($Prelevements == "Sec"){
			return "S";
		}
		if($Prelevements == "Citrate"){
			return "C";
		}
		if($Prelevements == "H√©parine"){
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
		if($Prelevements == "<span style='color: red'> non determin√© </span>"){
			return "I";
		}
	}
	
	
	public function enregistrerConsultationAction(){
		$idmedecin = $this->layout()->user['idemploye'];
		
		$tabDonnees = $this->params ()->fromPost();
		
		/**
		 *ANTECEDENT FAMILIAUX --- ANTECEDENTS FAMILIAUX
		 *ANTECEDENT FAMILIAUX --- ANTECEDENTS FAMILIAUX
		 */
		$this->getAntecedentsFamiliauxTable()->insertAntecedentsFamiliaux($tabDonnees);
		$this->getAntecedentsFamiliauxTable()->insertStatutDrepanocytoseEnfant($tabDonnees);
		
		/**
		 * CONSULTATION DU JOUR --- CONSULTATION DU JOUR
		 * CONSULTATION DU JOUR --- CONSULTATION DU JOUR
		 */
		/** Histoire de la maladie **/
		$this->getHistoireMaladieTable()->insertHistoireMaladie($tabDonnees, $idmedecin);
		
		/** Interrogatoire (description des symptomes) **/
		$this->getHistoireMaladieTable()->insertInterrogatoireMotif($tabDonnees, $idmedecin);
		
		
		/** Suivi des traitements **/
		$this->getHistoireMaladieTable()->insertSuiviDesTraitements($tabDonnees, $idmedecin);
		
		/** Mise ‡ jour des vaccins**/
		$this->getHistoireMaladieTable()->insertMiseAJourVaccin($tabDonnees, $idmedecin);
		
		/** DonnÈes de l'examen **/
		$this->getDonneesExamenTable()->insertDonneesExamen($tabDonnees, $idmedecin);
		
		/** SynthËse de la consultation du jour **/
		$this->getDonneesExamenTable()->insertSyntheseConsultation($tabDonnees, $idmedecin);
		
		/**
		 * EXAMENS COMPLEMENTAIRES --- EXAMENS COMPLEMENTAIRES --- EXAMENS COMPLEMENTAIRES
		 * EXAMENS COMPLEMENTAIRES --- EXAMENS COMPLEMENTAIRES --- EXAMENS COMPLEMENTAIRES
		 */
		/** Demande d'examens complÈmentaires (Examens radiologiques) **/
		$this->getExamenTable()->insertExamenRadiologique($tabDonnees, $idmedecin);
		
		/** Demande d'examens complÈmentaires (Analyses biologiques) **/
		$listeDemandesAnalyses = $this->getExamenTable()->insertAnalyseBiologique($tabDonnees, $idmedecin);
		
		/** Factutation des demandes d'analyses biologiques **/
		if($listeDemandesAnalyses){
			$this->facturationAnalysesDemandees($tabDonnees, $idmedecin, $listeDemandesAnalyses);			
		}
		
		/** RÈsultats des examens complÈmentaires (Examens radiologiques) **/
		$this->getExamenTable()->insertResultatExamenRadiologique($tabDonnees, $idmedecin);
		
		//var_dump($tabDonnees); exit();
		
		/**
		 * MOTIFS DES DEMANDES D'EXAMENS --- MOTIFS DES DEMANDES D'EXAMENS
		 * MOTIFS DES DEMANDES D'EXAMENS --- MOTIFS DES DEMANDES D'EXAMENS
		 */
		$this->getFacturationTable()->addMotifsExamensRadioDemandes($tabDonnees, $idmedecin);
		$this->getFacturationTable()->addMotifsExamensBioDemandes($tabDonnees, $idmedecin);
		
		
		
		/**
		 * DIAGNOSTIC --- DIAGNOSTIC --- DIAGNOSTIC 
 		 * DIAGNOSTIC --- DIAGNOSTIC --- DIAGNOSTIC 
		 */
		/** Diagnostics du jour **/
		$this->getDiagnosticConsultationTable()->insertDiagnosticConsultation($tabDonnees, $idmedecin);
		
		/** Complications aigues **/
		$this->getDiagnosticConsultationTable()->insertComplicationsAigues($tabDonnees, $idmedecin);
		
		/** Complications chroniques **/
		$this->getDiagnosticConsultationTable()->insertComplicationsChroniques($tabDonnees, $idmedecin);
		

		
				
		/**
		 *POUR LES TRAITEMENTS MEDICAMENTEUX --- POUR LES TRAITEMENTS MEDICAMENTEUX
		 *POUR LES TRAITEMENTS MEDICAMENTEUX --- POUR LES TRAITEMENTS MEDICAMENTEUX
		 *POUR LES TRAITEMENTS MEDICAMENTEUX --- POUR LES TRAITEMENTS MEDICAMENTEUX
		 */
		
		/**** MEDICAUX ****/
		/**** MEDICAUX ****/
		$dureeTraitement = $this->params()->fromPost('duree_traitement_ord');
		$donnees = array('id_cons' => $tabDonnees['idcons'], 'duree_traitement' => $dureeTraitement);
		
		$Consommable = $this->getOrdonConsommableTable();
		$tab = array();
		$j = 1;
		
		$nomMedicament = "";
		$formeMedicament = "";
		$quantiteMedicament = "";
		for($i = 1 ; $i < 10 ; $i++ ){
			if($this->params()->fromPost("medicament_0".$i)){
		
				$nomMedicament = $this->params()->fromPost("medicament_0".$i);
				$formeMedicament = $this->params()->fromPost("forme_".$i);
				$quantiteMedicament = $this->params()->fromPost("quantite_".$i);
		
				if($this->params()->fromPost("medicament_0".$i)){
		
					$result = $Consommable->getMedicamentByName($this->params()->fromPost("medicament_0".$i))['ID_MATERIEL'];
		
					if($result){
						$tab[$j++] = $result;
						$tab[$j++] = $formeMedicament; $Consommable->addFormes($formeMedicament);
						$tab[$j++] = $this->params()->fromPost("nb_medicament_".$i);
						$tab[$j++] = $quantiteMedicament; $Consommable->addQuantites($quantiteMedicament);
					} else {
						$idMedicaments = $Consommable->addMedicaments($nomMedicament);
						$tab[$j++] = $idMedicaments;
						$tab[$j++] = $formeMedicament; $Consommable->addFormes($formeMedicament);
						$tab[$j++] = $this->params()->fromPost("nb_medicament_".$i);
						$tab[$j++] = $quantiteMedicament; $Consommable->addQuantites($quantiteMedicament);
					}
				}
		
			}
		}
		
		/*Mettre a jour la duree du traitement de l'ordonnance*/
		$idOrdonnance = $this->getOrdonnanceTable()->updateOrdonnance($tab, $donnees);
		
		/*Mettre a jour les medicaments*/
		$resultat = $Consommable->updateOrdonConsommable($tab, $idOrdonnance, $nomMedicament);
			
		/*si aucun mÈdicament n'est ajoutÈ ($resultat = false) on supprime l'ordonnance*/
		if($resultat == false){ $this->getOrdonnanceTable()->deleteOrdonnance($idOrdonnance);}
		
		/**
		 * FIN FIN FIN POUR LES TRAITEMENTS MEDICAMENTEUX --- POUR LES TRAITEMENTS MEDICAMENTEUX
		 * FIN FIN FIN POUR LES TRAITEMENTS MEDICAMENTEUX --- POUR LES TRAITEMENTS MEDICAMENTEUX
		 */
		
		
		/**
		 * HOSPITALISATION --- HOSPITALISATION --- HOSPITALISATION
		 * HOSPITALISATION --- HOSPITALISATION --- HOSPITALISATION
		 */
		$this->getFacturationTable()->addHospitalisation($tabDonnees, $idmedecin);
		
		/**
		 * RENDEZ-VOUS --- RENDEZ-VOUS --- RENDEZ-VOUS
		 * RENDEZ-VOUS --- RENDEZ-VOUS --- RENDEZ-VOUS
		 */
		$this->getFacturationTable()->addRendezVous($tabDonnees, $idmedecin);
		
		
		
		return $this->redirect ()->toRoute ('consultation', array ('action' => 'liste-consultations' ));
	}
 
	
	
    public function modifierConsultationAction(){
    	
    	
    	//$listeAnalysesDemandees = $this->getAnalyseTable()->getListeAnalysesDemandeesDansConsDP('c_130218_123059', 744);
    	//var_dump($listeAnalysesDemandees); exit();
    	
		  //DEBUT --- DEBUT --- DEBUT
		  $timestart = microtime(true);
		  //-------------------------
		
		$this->layout ()->setTemplate ( 'layout/consultation' );
	
		$user = $this->layout()->user;
		$IdDuService = $user['IdService'];
		$idmedecin = $user['idemploye']; 
		
		$idpatient = $this->params ()->fromQuery ( 'idpatient', 0 );
		$idcons = $this->params ()->fromQuery ( 'idcons' );
		$patient = $this->getPatientTable()->getPatient($idpatient);
		$informations_parentales = $this->getPersonneTable()->getInfosParentales($idpatient);
	
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
		//---- FIN GESTION DU TYPE DE PATIENT ----
		
		$personne = $this->getPersonneTable()->getPersonne($idpatient);
		$consultation = $this->getConsultationTable()->getConsultation($idcons)->getArrayCopy();
		//---- Gestion des AGE ----
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
							$age ="<span style='font-size:17px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_annees."an ".$nb_mois."m ".$nb_jours."j </span>";
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
		//---- FIN Gestion des AGE ----
		//---- FIN Gestion des AGE ----
		

		$data = array(
				'idpatient' => $idpatient,
				'idmedecin' => $idmedecin,
		);
		
		$consultation = $this->getConsultationTable()->getConsultation($idcons)->getArrayCopy();
		
		//==================================================================================
		//==================================================================================
		//==================================================================================
		// instancier le motif d'admission et recuperer l'enregistrement
		$motif_admission = $this->getMotifAdmissionTable ()->getMotifAdmission ( $idcons );
		$nbMotif = $this->getMotifAdmissionTable ()->nbMotifs ( $idcons );
		
		$data = array();
		$mDouleur = array(1 => 0,2 => 0,3 => 0,4 => 0);
		//POUR LES MOTIFS D'ADMISSION
		$k = 1;
	    foreach ( $motif_admission as $Motifs ) {
			$data ['motif_admission' . $k] = $Motifs ['idlistemotif'];
			
			//Recuperation des infos supplÈmentaires du motif douleur
			if($Motifs ['idlistemotif'] == 2){
				$mDouleur[1] = 1;
				$mDouleur[2] = $k;
			}
			
			$k ++;
		}
		
		//Siege --- Siege --- Siege
		$motif_douleur_precision = $this->getMotifAdmissionTable ()->getMotifDouleurPrecision ( $idcons );
		if($motif_douleur_precision){
			$mDouleur[3] = $motif_douleur_precision['siege'];
			$mDouleur[4] = $motif_douleur_precision['intensite'];
		}
		
		//==================================================================================
		//==================================================================================
		//==================================================================================
		$form = new ConsultationForm();
		$form->populateValues($data);
		$form->populateValues($consultation);
		
		$listeMotifConsultation = $this->getMotifAdmissionTable() ->getListeSelectMotifConsultation();
		$form->get('motif_admission1')->setvalueOptions($listeMotifConsultation);
		$form->get('motif_admission2')->setvalueOptions($listeMotifConsultation);
		$form->get('motif_admission3')->setvalueOptions($listeMotifConsultation);
		$form->get('motif_admission4')->setvalueOptions($listeMotifConsultation);
		$form->get('motif_admission5')->setvalueOptions($listeMotifConsultation);
		
		$listeSiege = $this->getMotifAdmissionTable() ->getListeSelectSiege();
		$form->get('siege')->setvalueOptions($listeSiege);
		
		//RECUPERER LA LISTE DES VOIES ADMINISTRATION DES MEDICAMENTS
		$listeVoieAdministration = $this->getConsultationTable()->getVoieAdministration($idcons);
		
		//RECUPERER LA LISTE DES ACTES
		$listeActes = $this->getConsultationTable()->getListeDesActes();
		
		//RECUPERER LES ANALYSES EFFECTUEES PAR LE PATIENT FAISANT PARTIE DES ANALYSES OBLIGATOIRES A FAIRE 
		$donneesExamensEffectues = $this->getAnalyseAFaireTable()->getAnalyseEffectuees($idpatient);

		
		//var_dump($donneesExamensEffectues); exit();
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		//RECUPERATION DES INFORMATIONS POUR LA MODIFICATION 
		//RECUPERATION DES INFORMATIONS POUR LA MODIFICATION
		//RECUPERATION DES INFORMATIONS POUR LA MODIFICATION
		/**
		 * Recuperer les historiques et les antecedents du patient
		 */
		/*
		 * ANTECEDENTS FAMILIAUX --- ANTECEDENTS FAMILIAUX 
		 */
		$infosAntecedentsFamiliaux = $this->getAntecedentsFamiliauxTable()->getAntecedentsFamilauxParIdpatient($idpatient);
		$infosAutresMaladiesFamiliales = $this->getAntecedentsFamiliauxTable()->getAutresMaladiesFamiliales($idpatient);
    	if($infosAntecedentsFamiliaux){ $form->populateValues($infosAntecedentsFamiliaux[0]); }; 
		if($infosAutresMaladiesFamiliales){ $form->populateValues($infosAutresMaladiesFamiliales); }
		$listeChoixStatutDrepanoEnfant = $this->getAntecedentsFamiliauxTable()->getStatutDrepanocytoseEnfant($idpatient);
    	
		
		/**
		 * Recuperer la consultation du jour
		 */
		/*
		 * HISTOIRE DE LA MALADIE --- HITOIRE DE LA MALADIE
		 */
		$infosHistoireMaladie = $this->getHistoireMaladieTable()->getHistoireMaladie($idcons);
		$listeCrisesVasOcclusivesHM = null;
		if($infosHistoireMaladie){ 
			$form->populateValues($infosHistoireMaladie[0]); 
			if($infosHistoireMaladie[0]['criseHM'] == 1){ 
				$infosCriseVasoOcclusiveHm = $this->getHistoireMaladieTable()->getCriseVasoOcclusiveHm($idcons); 
				$form->populateValues($infosCriseVasoOcclusiveHm);
				
				$listeCrisesVasOcclusivesHM = $this->getHistoireMaladieTable()->getCriseVasoOcclusiveListeHm($idcons);
			}
			if($infosHistoireMaladie[0]['episodeFievreHM'] == 1){
				$infosEpisodeFievreHm = $this->getHistoireMaladieTable()->getEpisodeFievreHm($idcons);
				$form->populateValues($infosEpisodeFievreHm);
			}
			if($infosHistoireMaladie[0]['hospitalisationHM'] == 1){
				$infosHospitalisationHm = $this->getHistoireMaladieTable()->getHospitalisationHm($idcons);
				$form->populateValues($infosHospitalisationHm);
			}
		}
		
		/*
		 * INTERROGATOIRE (Description des symptomes)
		 */
		$infosInterrogatoireMotif = $this->getHistoireMaladieTable()->getInterrogatoireMotif($idcons);
		if($infosInterrogatoireMotif){  
			$tabInfosInter = array();
			$indiceEmp = 1;
			foreach ($infosInterrogatoireMotif as $infosInter){
				$tabInfosInter['motif_interrogatoire_'.$indiceEmp++] = $infosInter['motif_interrogatoire'];
			}
			$form->populateValues($tabInfosInter);  
		}
		
		/*
		 * SUIVI DES TRAITEMENTS
		 */
		$infosSuiviDesTraitements = $this->getHistoireMaladieTable()->getSuiviDesTraitements($idcons);
		if($infosSuiviDesTraitements){ $form->populateValues($infosSuiviDesTraitements); }
		
		/*
		 * MISE A JOUR DES VACCINS
		 */
		$infosMiseAJourVaccin = $this->getHistoireMaladieTable()->getMiseAJourVaccin($idcons);
		if($infosMiseAJourVaccin){ $form->populateValues($infosMiseAJourVaccin); }
    	
    	/*
    	 * DONNEES DE L'EXAMEN
    	 */
		$infosDonneesExamen = $this->getDonneesExamenTable()->getDonneesExamen($idcons);
    	if($infosDonneesExamen){ $form->populateValues($infosDonneesExamen[0]); }
    	
    	/*
    	 * SYNTHESE DE LA CONSULTATION
    	 */
    	$infosSyntheseConsultation = $this->getDonneesExamenTable()->getSyntheseConsultation($idcons);
    	if($infosSyntheseConsultation){ $form->populateValues($infosSyntheseConsultation); }
    	
    	

    	/**
    	 * RecupÈrer les demandes d'analyses
    	 */
    	/*
    	 * MOTIFS DES DEMANDES D'EXAMENS
    	*/
    	$motifsExamensRadioDemandes = $this->getFacturationTable()->getMotifsExamensRadioDemandes($idcons);
    	$motifsExamensBioDemandes   = $this->getFacturationTable()->getMotifsExamensBioDemandes($idcons);
    	 
    	 
    	 
    	/**
    	 * RÈcupÈrer les rÈsultats des demandes d'analyses radiologiques
    	*/
    	/*
    	 * RESULTATS DES EXAMENS RADIOLOGIQUES
    	*/
    	$resultatExamenRadio = $this->getExamenTable()->getResultatExamenRadiologique($idcons);
    	
    	
    	/**
    	 * Recuperer le diagnostic
    	 */
    	/*
    	 * INFOS SUR LE DIAGNOSTIC DU JOUR
    	 */
    	$tabInfosDiagnosticDuJour = array(1=>0, 2=>0, 3=>0);
    	
    	/*
    	 * DIAGNOSTIC DU JOUR
    	 */
    	$infosDiagnosticConsultation = $this->getDiagnosticConsultationTable()->getDiagnosticConsultation($idcons);
    	if($infosDiagnosticConsultation){ $form->populateValues($infosDiagnosticConsultation[0]); $tabInfosDiagnosticDuJour[3] = 1; }
    	/*
    	 * COMPLICATIONS AIGUES
    	 */
    	$infosComplicationsAigues = $this->getDiagnosticConsultationTable()->getComplicationsAigues($idcons);
    	if($infosComplicationsAigues->count() != 0){ 
    		$nbDiagnosticComplicationsAigues = array('nbDiagnosticComplicationsAigues' => $infosComplicationsAigues->count());
    		$form->populateValues($nbDiagnosticComplicationsAigues); 
    		$tabInfosDiagnosticDuJour[1] = 1;
    	}
    	/*
    	 * COMPLICATIONS CHRONIQUES
    	 */
    	$infosComplicationsChroniques = $this->getDiagnosticConsultationTable()->getComplicationsChroniques($idcons);
    	if($infosComplicationsChroniques->count() != 0){
    		$nbDiagnosticComplicationsChroniques = array('nbDiagnosticComplicationsChroniques' => $infosComplicationsChroniques->count());
    		$form->populateValues($nbDiagnosticComplicationsChroniques);
    		$tabInfosDiagnosticDuJour[2] = 1;
    	}
    	
    	
    	/**
    	 * RÈcupÈrer les informations sur le traitement mÈdicamenteux
    	 */
    	 
    	/*
    	 * TRAITEMENT MEDICAMENTEUX --- TRAITEMENT MEDICAMENTEUX
    	*/
    	$listeMedicament = $this->getConsultationModConsTable()->listeDeTousLesMedicaments();
    	$listeForme = $this->getConsultationModConsTable()->formesMedicaments();
    	$listetypeQuantiteMedicament = $this->getConsultationModConsTable()->typeQuantiteMedicaments();
    	 
    	
    	/*
    	 * INSTANTIATION DE L'ORDONNANCE
    	*/
    	$infoOrdonnance = $this->getOrdonnanceTable()->getOrdonnance($idcons);
    	 
    	if($infoOrdonnance) {
    		$idOrdonnance = $infoOrdonnance->id_document;
    		$duree_traitement = $infoOrdonnance->duree_traitement;
    		//LISTE DES MEDICAMENTS PRESCRITS
    		$listeMedicamentsPrescrits = $this->getOrdonnanceTable()->getMedicamentsParIdOrdonnance($idOrdonnance);
    		$nbMedPrescrit = $listeMedicamentsPrescrits->count();
    	}else{
    		$nbMedPrescrit = null;
    		$listeMedicamentsPrescrits =null;
    		$duree_traitement = null;
    	}
    	 
    	
    	
    	
    	
    	
    	
    	/**
    	 * RecupÈrer les Autres (Transfert/Hospitalisation/ Rendez-Vous )
    	 */
    	/*
    	 * TRANSFERT
    	 */
    	$transfert = $this->getFacturationTable()->getTransfert($idcons);
    	if($transfert){
    		
    	}
    	
    	/*
    	 * HOSPITALISATION
    	 */
    	$hospitalisation = $this->getFacturationTable()->getHospitalisation($idcons);
    	//var_dump($hospitalisation); exit();
    	
    	if($hospitalisation){
    		$donneesHospitalisation = array(
    				'motifHospitalisation' => $hospitalisation['motifHospitalisation'],
    		);
    		$form->populateValues($donneesHospitalisation);
    	}
    	
    	/*
    	 * RENDEZ-VOUS
    	 */
    	$rendezVous = $this->getFacturationTable()->getRendezVous($idcons);
    	$motifRendezVous = null;
    	if($rendezVous){
    		$donneesRendezVous = array(
    				'dateHeureRendezVous' => (new DateHelper())->convertDate($rendezVous['dateRv']).' - '.substr($rendezVous['heureRv'], 0, 5),
    		);
    		$motifRendezVous = $rendezVous['motifRv'];
    		$form->populateValues($donneesRendezVous);
    		
    	}
    	
    	
    	//var_dump($rendezVous); exit();
    	
    	
    	
		
		//FIN --- FIN --- FIN --- FIN --- FIN --- FIN --- FIN
		//$timeend = microtime(true);
		//$time = $timeend-$timestart;
		//var_dump(number_format($time,3)); exit();
		//---------------------------------------------------
		
		
		return array(
				
				'idcons' => $idcons,
				'lesdetails' => $personne,
				'date' => $consultation['date'],
				'heure' => $consultation['heure'],
				'age' => $age,
				'typage' => $type.' '.$typage,
				'nbMotifs' => $nbMotif,
				'form' => $form,
				'patient' => $patient,
				'donneesExamensEffectues' => $donneesExamensEffectues,
				
				'mDouleur' => $mDouleur,
				'listeVoieAdministration' => $listeVoieAdministration,
				'listeActesCons' => $listeActes,
				'listeMotifConsultation' => $listeMotifConsultation,
				'listeChoixStatutDrepanoEnfant' => $listeChoixStatutDrepanoEnfant,
				
				'infosComplicationsAigues' => $infosComplicationsAigues,
				'infosComplicationsChroniques' => $infosComplicationsChroniques,
				'tabInfosDiagnosticDuJour' => $tabInfosDiagnosticDuJour,
				
				'motifsExamensRadioDemandes' => $motifsExamensRadioDemandes,
				'motifsExamensBioDemandes' => $motifsExamensBioDemandes,
				
				'listeCrisesVasOcclusivesHM' => $listeCrisesVasOcclusivesHM,
				'motifRendezVous' => $motifRendezVous,
				
				'listeMedicament' => $listeMedicament,
				'listeFormeMedicament' => $listeForme,
				'listeTypeQuantiteMedicament'  => $listetypeQuantiteMedicament,
				
				'informations_parentales' => $informations_parentales,
				'resultatExamenRadio' => $resultatExamenRadio,
				
				'nb_med_prescrit' => $nbMedPrescrit,
				'liste_med_prescrit' => $listeMedicamentsPrescrits,
				'duree_traitement' => $duree_traitement,
		);

	}
	
	public function enregistrerModificationConsultationAction(){
		$idmedecin = $this->layout()->user['idemploye'];

		$tabDonnees = $this->params ()->fromPost();
		/**
		 *ANTECEDENT FAMILIAUX --- ANTECEDENTS FAMILIAUX
		 *ANTECEDENT FAMILIAUX --- ANTECEDENTS FAMILIAUX
		*/
				
		$this->getAntecedentsFamiliauxTable()->insertAntecedentsFamiliaux($tabDonnees);
		$this->getAntecedentsFamiliauxTable()->insertStatutDrepanocytoseEnfant($tabDonnees);

		//var_dump($tabDonnees); exit();
		
		/**
		 * CONSULTATION DU JOUR --- CONSULTATION DU JOUR
		 * CONSULTATION DU JOUR --- CONSULTATION DU JOUR
		 */
		/** Histoire de la maladie **/
		$this->getHistoireMaladieTable()->insertHistoireMaladie($tabDonnees, $idmedecin);
		
		/** Interrogatoire (description des symptomes) **/
		$this->getHistoireMaladieTable()->insertInterrogatoireMotif($tabDonnees, $idmedecin);
		
		/** Suivi des traitements **/
		$this->getHistoireMaladieTable()->insertSuiviDesTraitements($tabDonnees, $idmedecin);
		
		/** Mise ‡ jour des vaccins**/
		$this->getHistoireMaladieTable()->insertMiseAJourVaccin($tabDonnees, $idmedecin);
		
		/** DonnÈes de l'examen **/
		$this->getDonneesExamenTable()->insertDonneesExamen($tabDonnees, $idmedecin);
		
		/** SynthËse de la consultation du jour **/
		$this->getDonneesExamenTable()->insertSyntheseConsultation($tabDonnees, $idmedecin);
		
		
		
		
		
				
		
		/**
		 * EXAMENS COMPLEMENTAIRES --- EXAMENS COMPLEMENTAIRES --- EXAMENS COMPLEMENTAIRES
		 * EXAMENS COMPLEMENTAIRES --- EXAMENS COMPLEMENTAIRES --- EXAMENS COMPLEMENTAIRES
		 */
		
		/** Demande d'examens complÈmentaires (Examens radiologiques) **/
		$this->getExamenTable()->insertExamenRadiologique($tabDonnees, $idmedecin);
		
		/** Demande d'examens complÈmentaires (Analyses biologiques) **/
		$listeDemandesAnalyses = $this->getExamenTable()->insertAnalyseBiologique($tabDonnees, $idmedecin);
		
		/** Factutation des demandes d'analyses biologiques **/
		if($listeDemandesAnalyses){
			$this->facturationAnalysesDemandees($tabDonnees, $idmedecin, $listeDemandesAnalyses);
		}
		
		/**
		 * MOTIFS DES DEMANDES D'EXAMENS --- MOTIFS DES DEMANDES D'EXAMENS
		 * MOTIFS DES DEMANDES D'EXAMENS --- MOTIFS DES DEMANDES D'EXAMENS
		 */
		$this->getFacturationTable()->addMotifsExamensRadioDemandes($tabDonnees, $idmedecin);
		$this->getFacturationTable()->addMotifsExamensBioDemandes($tabDonnees, $idmedecin);
		
		/**
		 * RÈsultats des examens complÈmentaires (Examens radiologiques)
		 */
		$this->getExamenTable()->insertResultatExamenRadiologique($tabDonnees, $idmedecin);
		 
		
		/**
		 * DIAGNOSTIC --- DIAGNOSTIC --- DIAGNOSTIC
		 * DIAGNOSTIC --- DIAGNOSTIC --- DIAGNOSTIC
		 */
		/** Diagnostics du jour **/
		$this->getDiagnosticConsultationTable()->insertDiagnosticConsultation($tabDonnees, $idmedecin);
		
		/** Complications aigues **/
		$this->getDiagnosticConsultationTable()->insertComplicationsAigues($tabDonnees, $idmedecin);
		
		/** Complications chroniques **/
		$this->getDiagnosticConsultationTable()->insertComplicationsChroniques($tabDonnees, $idmedecin);
		
		
		
		/**
		 *POUR LES TRAITEMENTS MEDICAMENTEUX --- POUR LES TRAITEMENTS MEDICAMENTEUX
		 *POUR LES TRAITEMENTS MEDICAMENTEUX --- POUR LES TRAITEMENTS MEDICAMENTEUX
		 *POUR LES TRAITEMENTS MEDICAMENTEUX --- POUR LES TRAITEMENTS MEDICAMENTEUX
		 */
		
		/**** MEDICAUX ****/
		/**** MEDICAUX ****/
		$dureeTraitement = $this->params()->fromPost('duree_traitement_ord');
		$donnees = array('id_cons' => $tabDonnees['idcons'], 'duree_traitement' => $dureeTraitement);
		
		$Consommable = $this->getOrdonConsommableTable();
		$tab = array();
		$j = 1;
		
		$nomMedicament = "";
		$formeMedicament = "";
		$quantiteMedicament = "";
		for($i = 1 ; $i < 10 ; $i++ ){
			if($this->params()->fromPost("medicament_0".$i)){
		
				$nomMedicament = $this->params()->fromPost("medicament_0".$i);
				$formeMedicament = $this->params()->fromPost("forme_".$i);
				$quantiteMedicament = $this->params()->fromPost("quantite_".$i);
		
				if($this->params()->fromPost("medicament_0".$i)){
		
					$result = $Consommable->getMedicamentByName($this->params()->fromPost("medicament_0".$i))['ID_MATERIEL'];
		
					if($result){
						$tab[$j++] = $result;
						$tab[$j++] = $formeMedicament; $Consommable->addFormes($formeMedicament);
						$tab[$j++] = $this->params()->fromPost("nb_medicament_".$i);
						$tab[$j++] = $quantiteMedicament; $Consommable->addQuantites($quantiteMedicament);
					} else {
						$idMedicaments = $Consommable->addMedicaments($nomMedicament);
						$tab[$j++] = $idMedicaments;
						$tab[$j++] = $formeMedicament; $Consommable->addFormes($formeMedicament);
						$tab[$j++] = $this->params()->fromPost("nb_medicament_".$i);
						$tab[$j++] = $quantiteMedicament; $Consommable->addQuantites($quantiteMedicament);
					}
				}
		
			}
		}
		
		/*Mettre a jour la duree du traitement de l'ordonnance*/
		$idOrdonnance = $this->getOrdonnanceTable()->updateOrdonnance($tab, $donnees);
		
		/*Mettre a jour les medicaments*/
		$resultat = $Consommable->updateOrdonConsommable($tab, $idOrdonnance, $nomMedicament);
			
		/*si aucun mÈdicament n'est ajoutÈ ($resultat = false) on supprime l'ordonnance*/
		if($resultat == false){ $this->getOrdonnanceTable()->deleteOrdonnance($idOrdonnance);}
		
		/**
		 * POUR LES TRAITEMENTS MEDICAMENTEUX --- POUR LES TRAITEMENTS MEDICAMENTEUX
		 * POUR LES TRAITEMENTS MEDICAMENTEUX --- POUR LES TRAITEMENTS MEDICAMENTEUX
		 */
		
		
		
		
		/**
		 * HOSPITALISATION --- HOSPITALISATION --- HOSPITALISATION
		 * HOSPITALISATION --- HOSPITALISATION --- HOSPITALISATION
		 */
		$this->getFacturationTable()->addHospitalisation($tabDonnees, $idmedecin);
		
		/**
		 * RENDEZ-VOUS --- RENDEZ-VOUS --- RENDEZ-VOUS
		 * RENDEZ-VOUS --- RENDEZ-VOUS --- RENDEZ-VOUS
		*/
		$this->getFacturationTable()->addRendezVous($tabDonnees, $idmedecin);
		
		
		
		
		
		//var_dump($tabDonnees); exit();
		
		return $this->redirect ()->toRoute ('consultation', array ('action' => 'liste-consultations' ));
	}
	
	
	//HISTORIQUE DES PATIENTS CONSULTES
	//HISTORIQUE DES PATIENTS CONSULTES
	public function listePatientsConsultesAjaxAction() {
		$output = $this->getConsultationModConsTable ()->getListePatientsConsultes();
		return $this->getResponse ()->setContent ( Json::encode ( $output, array ( 'enableJsonExprFinder' => true ) ) );
	}
	
	public function listePatientsConsultesAction() {
		$this->layout ()->setTemplate ( 'layout/consultation' );
		
		//$output = $this->getConsultationModConsTable ()->getListePatientsConsultes();
		//var_dump($output); exit();
	}
	
	
	
	
	
	
	//GESTION DES HISTORIQUES --- GESTION DES HISTORIQUES
	//GESTION DES HISTORIQUES --- GESTION DES HISTORIQUES
	/**
	 * Afficher la liste des historiques consultations du patient
	 */
	public function historiquesDesConsultationsDuPatientAjaxAction() {
		$idpatient = $this->params ()->fromRoute ( 'idpatient', 0 );
		$output = $this->getConsultationModConsTable()->getHistoriqueDesConsultations($idpatient);
		return $this->getResponse ()->setContent ( Json::encode ( $output, array (
				'enableJsonExprFinder' => true
		) ) );
	}
	
	/**
	 * Visualiser l'historique d'une consultation donnÈe (Avec idcons et idpatient)
	 */
	public function visualisationHistoriqueConsultationAction() {
		
		/**
		 * UTILISER LA MEME FONCTION UTILISEE DANS 'modifier-consultation' 
		 * 
		 * la seule chose qui change pour le moment
		 * POUR LE MENU GAUCHE dans -elementgauche.phtml
		 */
		 



		//$listeAnalysesDemandees = $this->getAnalyseTable()->getListeAnalysesDemandeesDansConsDP('c_130218_123059', 744);
		//var_dump($listeAnalysesDemandees); exit();
		 
		//DEBUT --- DEBUT --- DEBUT
		$timestart = microtime(true);
		//-------------------------
		
		$this->layout ()->setTemplate ( 'layout/consultation' );
		
		$user = $this->layout()->user;
		$IdDuService = $user['IdService'];
		$idmedecin = $user['idemploye'];
		
		$idpatient = $this->params ()->fromQuery ( 'idpatient', 0 );
		$idcons = $this->params ()->fromQuery ( 'idcons' );
		$patient = $this->getPatientTable()->getPatient($idpatient);
		$informations_parentales = $this->getPersonneTable()->getInfosParentales($idpatient);
		
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
		//---- FIN GESTION DU TYPE DE PATIENT ----
		
		$personne = $this->getPersonneTable()->getPersonne($idpatient);
		$consultation = $this->getConsultationTable()->getConsultation($idcons)->getArrayCopy();
		//---- Gestion des AGE ----
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
							$age ="<span style='font-size:17px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_annees."an ".$nb_mois."m ".$nb_jours."j </span>";
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
		//---- FIN Gestion des AGE ----
		//---- FIN Gestion des AGE ----
		
		
		$data = array(
				'idpatient' => $idpatient,
				'idmedecin' => $idmedecin,
		);
		
		$consultation = $this->getConsultationTable()->getConsultation($idcons)->getArrayCopy();
		
		//==================================================================================
		//==================================================================================
		//==================================================================================
		// instancier le motif d'admission et recuperer l'enregistrement
		$motif_admission = $this->getMotifAdmissionTable ()->getMotifAdmission ( $idcons );
		$nbMotif = $this->getMotifAdmissionTable ()->nbMotifs ( $idcons );
		
		$data = array();
		$mDouleur = array(1 => 0,2 => 0,3 => 0,4 => 0);
		//POUR LES MOTIFS D'ADMISSION
		$k = 1;
		foreach ( $motif_admission as $Motifs ) {
			$data ['motif_admission' . $k] = $Motifs ['idlistemotif'];
				
			//Recuperation des infos supplÈmentaires du motif douleur
			if($Motifs ['idlistemotif'] == 2){
				$mDouleur[1] = 1;
				$mDouleur[2] = $k;
			}
				
			$k ++;
		}
		
		//Siege --- Siege --- Siege
		$motif_douleur_precision = $this->getMotifAdmissionTable ()->getMotifDouleurPrecision ( $idcons );
		if($motif_douleur_precision){
			$mDouleur[3] = $motif_douleur_precision['siege'];
			$mDouleur[4] = $motif_douleur_precision['intensite'];
		}
		
		//==================================================================================
		//==================================================================================
		//==================================================================================
		$form = new ConsultationForm();
		$form->populateValues($data);
		$form->populateValues($consultation);
		
		$listeMotifConsultation = $this->getMotifAdmissionTable() ->getListeSelectMotifConsultation();
		$form->get('motif_admission1')->setvalueOptions($listeMotifConsultation);
		$form->get('motif_admission2')->setvalueOptions($listeMotifConsultation);
		$form->get('motif_admission3')->setvalueOptions($listeMotifConsultation);
		$form->get('motif_admission4')->setvalueOptions($listeMotifConsultation);
		$form->get('motif_admission5')->setvalueOptions($listeMotifConsultation);
		
		$listeSiege = $this->getMotifAdmissionTable() ->getListeSelectSiege();
		$form->get('siege')->setvalueOptions($listeSiege);
		
		//RECUPERER LA LISTE DES VOIES ADMINISTRATION DES MEDICAMENTS
		$listeVoieAdministration = $this->getConsultationTable()->getVoieAdministration($idcons);
		
		//RECUPERER LA LISTE DES ACTES
		$listeActes = $this->getConsultationTable()->getListeDesActes();
		
		//RECUPERER LES ANALYSES EFFECTUEES PAR LE PATIENT FAISANT PARTIE DES ANALYSES OBLIGATOIRES A FAIRE
		$donneesExamensEffectues = $this->getAnalyseAFaireTable()->getAnalyseEffectuees($idpatient);
		
		
		//var_dump($donneesExamensEffectues); exit();
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		//RECUPERATION DES INFORMATIONS POUR LA MODIFICATION
		//RECUPERATION DES INFORMATIONS POUR LA MODIFICATION
		//RECUPERATION DES INFORMATIONS POUR LA MODIFICATION
		/**
		 * Recuperer les historiques et les antecedents du patient
		*/
		/*
		 * ANTECEDENTS FAMILIAUX --- ANTECEDENTS FAMILIAUX
		*/
		$infosAntecedentsFamiliaux = $this->getAntecedentsFamiliauxTable()->getAntecedentsFamilauxParIdpatient($idpatient);
		$infosAutresMaladiesFamiliales = $this->getAntecedentsFamiliauxTable()->getAutresMaladiesFamiliales($idpatient);
		if($infosAntecedentsFamiliaux){ $form->populateValues($infosAntecedentsFamiliaux[0]); };
		if($infosAutresMaladiesFamiliales){ $form->populateValues($infosAutresMaladiesFamiliales); }
		$listeChoixStatutDrepanoEnfant = $this->getAntecedentsFamiliauxTable()->getStatutDrepanocytoseEnfant($idpatient);
		 
		
		/**
		 * Recuperer la consultation du jour
		*/
		/*
		 * HISTOIRE DE LA MALADIE --- HITOIRE DE LA MALADIE
		*/
		$infosHistoireMaladie = $this->getHistoireMaladieTable()->getHistoireMaladie($idcons);
		$listeCrisesVasOcclusivesHM = null;
		if($infosHistoireMaladie){
			$form->populateValues($infosHistoireMaladie[0]);
			if($infosHistoireMaladie[0]['criseHM'] == 1){
				$infosCriseVasoOcclusiveHm = $this->getHistoireMaladieTable()->getCriseVasoOcclusiveHm($idcons);
				$form->populateValues($infosCriseVasoOcclusiveHm);
		
				$listeCrisesVasOcclusivesHM = $this->getHistoireMaladieTable()->getCriseVasoOcclusiveListeHm($idcons);
			}
			if($infosHistoireMaladie[0]['episodeFievreHM'] == 1){
				$infosEpisodeFievreHm = $this->getHistoireMaladieTable()->getEpisodeFievreHm($idcons);
				$form->populateValues($infosEpisodeFievreHm);
			}
			if($infosHistoireMaladie[0]['hospitalisationHM'] == 1){
				$infosHospitalisationHm = $this->getHistoireMaladieTable()->getHospitalisationHm($idcons);
				$form->populateValues($infosHospitalisationHm);
			}
		}
		
		/*
		 * INTERROGATOIRE (Description des symptomes)
		*/
		$infosInterrogatoireMotif = $this->getHistoireMaladieTable()->getInterrogatoireMotif($idcons);
		if($infosInterrogatoireMotif){
			$tabInfosInter = array();
			$indiceEmp = 1;
			foreach ($infosInterrogatoireMotif as $infosInter){
				$tabInfosInter['motif_interrogatoire_'.$indiceEmp++] = $infosInter['motif_interrogatoire'];
			}
			$form->populateValues($tabInfosInter);
		}
		
		/*
		 * SUIVI DES TRAITEMENTS
		*/
		$infosSuiviDesTraitements = $this->getHistoireMaladieTable()->getSuiviDesTraitements($idcons);
		if($infosSuiviDesTraitements){ $form->populateValues($infosSuiviDesTraitements); }
		
		/*
		 * MISE A JOUR DES VACCINS
		*/
		$infosMiseAJourVaccin = $this->getHistoireMaladieTable()->getMiseAJourVaccin($idcons);
		if($infosMiseAJourVaccin){ $form->populateValues($infosMiseAJourVaccin); }
		 
		/*
		 * DONNEES DE L'EXAMEN
		*/
		$infosDonneesExamen = $this->getDonneesExamenTable()->getDonneesExamen($idcons);
		if($infosDonneesExamen){ $form->populateValues($infosDonneesExamen[0]); }
		 
		/*
		 * SYNTHESE DE LA CONSULTATION
		*/
		$infosSyntheseConsultation = $this->getDonneesExamenTable()->getSyntheseConsultation($idcons);
		if($infosSyntheseConsultation){ $form->populateValues($infosSyntheseConsultation); }
		 
		 
		
		/**
		 * RecupÈrer les demandes d'analyses
		 */
		/*
		 * MOTIFS DES DEMANDES D'EXAMENS
		*/
		$motifsExamensRadioDemandes = $this->getFacturationTable()->getMotifsExamensRadioDemandes($idcons);
		$motifsExamensBioDemandes   = $this->getFacturationTable()->getMotifsExamensBioDemandes($idcons);
		
		
		
		/**
		 * RÈcupÈrer les rÈsultats des demandes d'analyses radiologiques
		*/
		/*
		 * RESULTATS DES EXAMENS RADIOLOGIQUES
		*/
		$resultatExamenRadio = $this->getExamenTable()->getResultatExamenRadiologique($idcons);
		 
		 
		/**
		 * Recuperer le diagnostic
		*/
		/*
		 * INFOS SUR LE DIAGNOSTIC DU JOUR
		*/
		$tabInfosDiagnosticDuJour = array(1=>0, 2=>0, 3=>0);
		 
		/*
		 * DIAGNOSTIC DU JOUR
		*/
		$infosDiagnosticConsultation = $this->getDiagnosticConsultationTable()->getDiagnosticConsultation($idcons);
		if($infosDiagnosticConsultation){ $form->populateValues($infosDiagnosticConsultation[0]); $tabInfosDiagnosticDuJour[3] = 1; }
		/*
		 * COMPLICATIONS AIGUES
		*/
		$infosComplicationsAigues = $this->getDiagnosticConsultationTable()->getComplicationsAigues($idcons);
		if($infosComplicationsAigues->count() != 0){
			$nbDiagnosticComplicationsAigues = array('nbDiagnosticComplicationsAigues' => $infosComplicationsAigues->count());
			$form->populateValues($nbDiagnosticComplicationsAigues);
			$tabInfosDiagnosticDuJour[1] = 1;
		}
		/*
		 * COMPLICATIONS CHRONIQUES
		*/
		$infosComplicationsChroniques = $this->getDiagnosticConsultationTable()->getComplicationsChroniques($idcons);
		if($infosComplicationsChroniques->count() != 0){
			$nbDiagnosticComplicationsChroniques = array('nbDiagnosticComplicationsChroniques' => $infosComplicationsChroniques->count());
			$form->populateValues($nbDiagnosticComplicationsChroniques);
			$tabInfosDiagnosticDuJour[2] = 1;
		}
		 
		 
		/**
		 * RÈcupÈrer les informations sur le traitement mÈdicamenteux
		 */
		
		/*
		 * TRAITEMENT MEDICAMENTEUX --- TRAITEMENT MEDICAMENTEUX
		*/
		$listeMedicament = $this->getConsultationModConsTable()->listeDeTousLesMedicaments();
		$listeForme = $this->getConsultationModConsTable()->formesMedicaments();
		$listetypeQuantiteMedicament = $this->getConsultationModConsTable()->typeQuantiteMedicaments();
		
		 
		/*
		 * INSTANTIATION DE L'ORDONNANCE
		*/
		$infoOrdonnance = $this->getOrdonnanceTable()->getOrdonnance($idcons);
		
		if($infoOrdonnance) {
			$idOrdonnance = $infoOrdonnance->id_document;
			$duree_traitement = $infoOrdonnance->duree_traitement;
			//LISTE DES MEDICAMENTS PRESCRITS
			$listeMedicamentsPrescrits = $this->getOrdonnanceTable()->getMedicamentsParIdOrdonnance($idOrdonnance);
			$nbMedPrescrit = $listeMedicamentsPrescrits->count();
		}else{
			$nbMedPrescrit = null;
			$listeMedicamentsPrescrits =null;
			$duree_traitement = null;
		}
		
		 
		 
		 
		 
		 
		 
		/**
		 * RecupÈrer les Autres (Transfert/Hospitalisation/ Rendez-Vous )
		 */
		/*
		 * TRANSFERT
		*/
		$transfert = $this->getFacturationTable()->getTransfert($idcons);
		if($transfert){
		
		}
		 
		/*
		 * HOSPITALISATION
		*/
		$hospitalisation = $this->getFacturationTable()->getHospitalisation($idcons);
		//var_dump($hospitalisation); exit();
		 
		if($hospitalisation){
			$donneesHospitalisation = array(
					'motifHospitalisation' => $hospitalisation['motifHospitalisation'],
			);
			$form->populateValues($donneesHospitalisation);
		}
		 
		/*
		 * RENDEZ-VOUS
		*/
		$rendezVous = $this->getFacturationTable()->getRendezVous($idcons);
		$motifRendezVous = null;
		if($rendezVous){
			$donneesRendezVous = array(
					'dateHeureRendezVous' => (new DateHelper())->convertDate($rendezVous['dateRv']).' - '.substr($rendezVous['heureRv'], 0, 5),
			);
			$motifRendezVous = $rendezVous['motifRv'];
			$form->populateValues($donneesRendezVous);
		
		}
		 
		 
		//var_dump($rendezVous); exit();
		 
		 
		 
		
		//FIN --- FIN --- FIN --- FIN --- FIN --- FIN --- FIN
		//$timeend = microtime(true);
		//$time = $timeend-$timestart;
		//var_dump(number_format($time,3)); exit();
		//---------------------------------------------------
		
		
		return array(
		
				'idcons' => $idcons,
				'lesdetails' => $personne,
				'date' => $consultation['date'],
				'heure' => $consultation['heure'],
				'age' => $age,
				'typage' => $type.' '.$typage,
				'nbMotifs' => $nbMotif,
				'form' => $form,
				'patient' => $patient,
				'donneesExamensEffectues' => $donneesExamensEffectues,
		
				'mDouleur' => $mDouleur,
				'listeVoieAdministration' => $listeVoieAdministration,
				'listeActesCons' => $listeActes,
				'listeMotifConsultation' => $listeMotifConsultation,
				'listeChoixStatutDrepanoEnfant' => $listeChoixStatutDrepanoEnfant,
		
				'infosComplicationsAigues' => $infosComplicationsAigues,
				'infosComplicationsChroniques' => $infosComplicationsChroniques,
				'tabInfosDiagnosticDuJour' => $tabInfosDiagnosticDuJour,
		
				'motifsExamensRadioDemandes' => $motifsExamensRadioDemandes,
				'motifsExamensBioDemandes' => $motifsExamensBioDemandes,
		
				'listeCrisesVasOcclusivesHM' => $listeCrisesVasOcclusivesHM,
				'motifRendezVous' => $motifRendezVous,
		
				'listeMedicament' => $listeMedicament,
				'listeFormeMedicament' => $listeForme,
				'listeTypeQuantiteMedicament'  => $listetypeQuantiteMedicament,
		
				'informations_parentales' => $informations_parentales,
				'resultatExamenRadio' => $resultatExamenRadio,
		
				'nb_med_prescrit' => $nbMedPrescrit,
				'liste_med_prescrit' => $listeMedicamentsPrescrits,
				'duree_traitement' => $duree_traitement,
		);
		
		
		
	}
	
	
	
	
	
	
	//GESTION DES IMPRESSIONS --- GESTION DES IMPRESSION
	//GESTION DES IMPRESSIONS --- GESTION DES IMPRESSION
	public function impressionDemandesAnalysesAction() {

		$nomService = $this->layout()->user['NomService'];
		$idpatient = $this->params()->fromPost( 'idpatient' );
		$personne = $this->getPersonneTable()->getPersonne($idpatient);
		$patient = $this->getPatientTable()->getPatient($idpatient);
		$depistage = $this->getPatientTable()->getDepistagePatient($idpatient);
		
		$formData = $this->getRequest ()->getPost ();
		$analyses = $formData['analyses'];
		$typesAnalyses = $formData['typesAnalyses'];
		$tarifs = $formData['tarifs'];
		
		$analysesTab = explode(',', $analyses);
		$typesAnalysesTab = explode(',', $typesAnalyses);
		$tarifsTab = explode(',', $tarifs);
		
		//******************************************************
		//******************************************************
		//*************** CrÈation de l'imprimÈ pdf **************
		//******************************************************
		//******************************************************
		
		//CrÈer le document
		$DocPdf = new DocumentPdf();
		//CrÈer la page
		$page = new DemandeAnalysePdf();
		
		//Envoyer les donnÈes sur le patient
		$page->setPatient($patient);
		$page->setDonneesPatient($personne);
		$page->setService($nomService);
		$page->setAnalyses($analysesTab);
		$page->setTypesAnalyses($typesAnalysesTab);
		$page->setTarifs($tarifsTab);
		$page->setDepistage($depistage);
		
		//Ajouter une note ‡ la page
		$page->addNote();
		//Ajouter la page au document
		$DocPdf->addPage($page->getPage());
		//Afficher le document contenant la page
		$DocPdf->getDocument();
		
		
	}	
	
	
	
	//GESTION DES IMPRESSIONS --- GESTION DES IMPRESSION
	//GESTION DES IMPRESSIONS --- GESTION DES IMPRESSION
	public function impressionDemandesExamensAction() {
	
		$nomService = $this->layout()->user['NomService'];
		$idpatient = $this->params()->fromPost( 'idpatient' );
		$personne = $this->getPersonneTable()->getPersonne($idpatient);
		$patient = $this->getPatientTable()->getPatient($idpatient);
		$depistage = $this->getPatientTable()->getDepistagePatient($idpatient);
	
		$formData = $this->getRequest ()->getPost ();
		$analyses = $formData['analyses'];
		$typesAnalyses = $formData['typesAnalyses'];
		$tarifs = $formData['tarifs'];
	
		$analysesTab = explode(',', $analyses);
		$typesAnalysesTab = explode(',', $typesAnalyses);
		$tarifsTab = explode(',', $tarifs);
	
		//******************************************************
		//******************************************************
		//*************** CrÈation de l'imprimÈ pdf **************
		//******************************************************
		//******************************************************
	
		$pdf = new imprimerDemandesExamens();
		$pdf->SetMargins(13.5,13.5,13.5);
		$pdf->setNomService($nomService);
		$pdf->setInfosPatients($personne);
		$pdf->setInfosDetailsPatients($patient);
		
		$pdf->impressionDemandesAnalyses();
		$pdf->Output('I');
	}
	
	
	
	
	
	
	
	
	/**
	 * IMPRESSION DES ORDONNANCES --- IMPRESSION DES ORDONNANCES
	 */
	public function impressionOrdonnanceAction() {
	
		$nomService = $this->layout()->user['NomService'];
		$idpatient = $this->params()->fromPost( 'idpatient' );
		$personne = $this->getPersonneTable()->getPersonne($idpatient);
		$patient = $this->getPatientTable()->getPatient($idpatient);
		$depistage = $this->getPatientTable()->getDepistagePatient($idpatient);
	
		$idcons = $this->params()->fromPost( 'idcons' );
		
		$medicamentLibelle = explode( "," , $this->params()->fromPost( 'medicamentLibelle' ));
		$formeMedicament = explode( "," , $this->params()->fromPost( 'formeMedicament' ));
		$nbMedicament = explode( "," , $this->params()->fromPost( 'nbMedicament' ));
		$quantiteMedicament = explode( "," , $this->params()->fromPost( 'quantiteMedicament' ));
		
		//var_dump($medicamentLibelle); exit();
		
		$pdf = new imprimerOrdonnance();
		$pdf->SetMargins(13.5,13.5,13.5);
		$pdf->setNomService($nomService);
		$pdf->setInfosPatients($personne);
		
		$pdf->setMedicamentLibelle($medicamentLibelle);
		$pdf->setFormeMedicament($formeMedicament);
		$pdf->setNbMedicament($nbMedicament);
		$pdf->setQuantiteMedicament($quantiteMedicament);
		
		$pdf->impressionOrdonnance();
		$pdf->Output('I');
		
	}
	
	
	/**
	 * IMPRESSION DES EXAMENS DEMANDES DU Popup --- IMPRESSION DES EXAMENS DEMANDES DU Popup
	 */
	
	public function impressionUnExamenDemandeAction() {
		$nomService = $this->layout()->user['NomService'];
		$idpatient = $this->params()->fromPost( 'idpatient' );
		$personne = $this->getPersonneTable()->getPersonne($idpatient);
		$patient = $this->getPatientTable()->getPatient($idpatient);
		$depistage = $this->getPatientTable()->getDepistagePatient($idpatient);
		
		$motifExamenDem = $this->params()->fromPost( 'motifExamenDem' );
		$typeExamen = $this->params()->fromPost( 'typeExamen' );
		$idExamen = $this->params()->fromPost( 'idExamen' );
		$libelleExamen = $this->params()->fromPost( 'libelleExamen' );
		
		$pdf = new ImprimerUnExamenDemande('L','mm',array(210,150));
		$pdf->SetMargins(13.5,13.5,13.5);
		$pdf->setNomService($nomService);
		$pdf->setInfosPatients($personne);
		
		$pdf->setMotifExamenDem($motifExamenDem);
		$pdf->setTypeExamen($typeExamen);
		$pdf->setIdExamen($idExamen);
		$pdf->setLibelleExamen($libelleExamen);
		
		$pdf->impressionUnExamenDemande();
		$pdf->Output('I');
		
	}
	
	
	public function impressionExamensRadioDemandesAction() {
	
		$nomService = $this->layout()->user['NomService'];
		$idpatient = $this->params()->fromPost( 'idpatient' );
		$personne = $this->getPersonneTable()->getPersonne($idpatient);
		$patient = $this->getPatientTable()->getPatient($idpatient);
		$depistage = $this->getPatientTable()->getDepistagePatient($idpatient);

		$motifExamenDem = $this->params()->fromPost( 'tabMotifExamenDem' );
		$typeExamen = $this->params()->fromPost( 'tabTypesExamens' );
		$idExamen = $this->params()->fromPost( 'tabIdExamens' );
		$libelleExamen = $this->params()->fromPost( 'tabExamens' );
		
		$pdf = new ImprimerExamensRadioDemandes('L','mm',array(210,150));
		$pdf->SetMargins(13.5,13.5,13.5);
		$pdf->setNomService($nomService);
		$pdf->setInfosPatients($personne);
		
		$pdf->setMotifExamenDem($motifExamenDem);
		$pdf->setTypeExamen($typeExamen);
		$pdf->setIdExamen($idExamen);
		$pdf->setLibelleExamen($libelleExamen);
		
		$pdf->impressionExamensRadioDemandes();
		
		$pdf->Output('I');
	
	}
	
	
	public function impressionExamensBioDemandesAction() {
	
		$nomService = $this->layout()->user['NomService'];
		$idpatient = $this->params()->fromPost( 'idpatient' );
		$personne = $this->getPersonneTable()->getPersonne($idpatient);
		$patient = $this->getPatientTable()->getPatient($idpatient);
		$depistage = $this->getPatientTable()->getDepistagePatient($idpatient);
	
	
		$motifExamenDem = $this->params()->fromPost( 'tabMotifExamenDem' );
		$typeExamen = $this->params()->fromPost( 'tabTypesExamens' );
		$idExamen = $this->params()->fromPost( 'tabIdExamens' );
		$libelleExamen = $this->params()->fromPost( 'tabExamens' );
	
		$pdf = new ImprimerExamensBioDemandes('L','mm',array(210,150));
		$pdf->SetMargins(13.5,13.5,13.5);
		$pdf->setNomService($nomService);
		$pdf->setInfosPatients($personne);
	
		$pdf->setMotifExamenDem($motifExamenDem);
		$pdf->setTypeExamen($typeExamen);
		$pdf->setIdExamen($idExamen);
		$pdf->setLibelleExamen($libelleExamen);
	
		$pdf->impressionExamensBioDemandes();
		$pdf->Output('I');
	
	}
	
	
	
	
	
	
	
	
	
	
	

	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function item_percentage($item, $total){
	
		if($total){
			return number_format(($item * 100 / $total), 1,',', ' ');
		}else{
			return 0;
		}
	
	}
	
	function pourcentage_element_tab($tableau, $total){
		$resultat = array();
	
		foreach ($tableau as $tab){
			$resultat [] = $this->item_percentage($tab, $total);
		}
	
		return $resultat;
	}
	
	public function informationsStatistiquesAction() {
		$this->layout ()->setTemplate ( 'layout/consultation' );
		
		/*
		 * INFOS GENERAL --- INFOS GENERAL --- INFOS GENERAL
		 * INFOS GENERAL --- INFOS GENERAL --- INFOS GENERAL
		 */
		$nbPatientD   = $this->getDepistageTable()->getNbPatientsDepistes();
		$nbPatientDN  = $this->getDepistageTable()->getNbPatientsDepistesNegatifs();
		$nbPatientDP  = $this->getDepistageTable()->getNbPatientsDepistesPositifs();
		$nbPatientDPF = $this->getDepistageTable()->getNbPatientsDepistesPositifsFeminin();
		$nbPatientDPM = $this->getDepistageTable()->getNbPatientsDepistesPositifsMasculin();
		
		$typagesPatientsInternes = $this->getDepistageTable()->getListeFormesGravesDepistes();
		
		//Pourcentage des patients dÈpistÈs
		//Pourcentage des patients dÈpistÈs
		$tabNbPatientDepister = array($nbPatientDN, $nbPatientDP);
		$pourcentageDepister = $this->pourcentage_element_tab($tabNbPatientDepister, $nbPatientD);
		
		//Pourcentage des patients dÈpistÈs positifs et nÈgatifs
		//Pourcentage des patients dÈpistÈs positifs et nÈgatifs
		$tabNbPatientDepisterPositif = array($nbPatientDPM, $nbPatientDPF);
		$pourcentageDepisterPositif = $this->pourcentage_element_tab($tabNbPatientDepisterPositif, $nbPatientDP);
		
		
		//Pourcentage des patients par profil de SDM
		//Pourcentage des patients par profil de SDM
		$totalProfilsPatientsInterne = array_sum($typagesPatientsInternes[1]);
		$tableau = array_values($typagesPatientsInternes[1]);
		$pourcentageProfilsPatientsInterne = $this->pourcentage_element_tab($tableau, $totalProfilsPatientsInterne);
		
		
		/*
		 * AUTRES INFOS --- AUTRES INFOS --- AUTRES INFOS
		 * AUTRES INFOS --- AUTRES INFOS --- AUTRES INFOS
		 */
		/**
		  Nouveau-nÈs dÈpistÈs avec sex-ratio
		  Nouveau-nÈs dÈpistÈs avec sex-ratio
		  Nouveau-nÈs dÈpistÈs avec sex-ratio*/
		$nbPatientDVSF = $this->getDepistageTable()->getNbPatientsDepistesValidesSexeFeminin();
		$nbPatientDVSM = $this->getDepistageTable()->getNbPatientsDepistesValidesSexeMasculin();
		
		$nbPatientDnVSF = $this->getDepistageTable()->getNbPatientsDepistesNonValidesSexeFeminin(); 
		$nbPatientDnVSM = $this->getDepistageTable()->getNbPatientsDepistesNonValidesSexeMasculin(); 

		//Pourcentage des dÈpistages validÈs
		//Pourcentage des dÈpistages validÈs
		$tabNbDepistagesValides = array($nbPatientDVSM, $nbPatientDVSF);
		$pourcentageDepistagesValides = $this->pourcentage_element_tab($tabNbDepistagesValides, ($nbPatientDVSM+$nbPatientDVSF));
		
		
		/**
		 RÈpartition des nouveau-nÈs selon leurs ethnies
		 RÈpartition des nouveau-nÈs selon leurs ethnies
		 RÈpartition des nouveau-nÈs selon leurs ethnies*/
		$peresNouveauNesSelonEthnies = $this->getDepistageTable()->getRepartitionDesPeresSelonEthnies();
		$totalNouveauNes = array_sum($peresNouveauNesSelonEthnies[1]);
		$tableauNouveauNes = array_values($peresNouveauNesSelonEthnies[1]);
		
		$pourcentagePeresNouveauNesSelonEthnies = $this->pourcentage_element_tab($tableauNouveauNes, $totalNouveauNes);
		
		/**
		  Les diffÈrents types d'hÈmoglobine rencontrÈs
		  Les diffÈrents types d'hÈmoglobine rencontrÈs
		  Les diffÈrents types d'hÈmoglobine rencontrÈs*/
		$differentsTypesProfils = $this->getDepistageTable()->getDifferentsTypesProfils();
		$totalDifferentsTypages = array_sum($differentsTypesProfils[1]);
		$tableauDifferentsTypages = array_values($differentsTypesProfils[1]);
		
		$pourcentageDifferentsTypesProfils = $this->pourcentage_element_tab($tableauDifferentsTypages, $totalDifferentsTypages);
		
		/**
		 * RÈpartition des diffÈrents types d'hÈmoglobine selon les ethnies
		 * RÈpartition des diffÈrents types d'hÈmoglobine selon les ethnies
		 * RÈpartition des diffÈrents types d'hÈmoglobine selon les ethnies*/
		
		$repartitionTypesProfilsSelonEthnies = $this->getDepistageTable()->getRepartitionTypesProfilsSelonEthnies();
		
		
		/**
		 * Les professions rencontrÈes chez les mËres et chez les pËres
		 * Les professions rencontrÈes chez les mËres et chez les pËres
		 * Les professions rencontrÈes chez les mËres et chez les pËres*/
		/** MËres --- MËres*/
		$repartitionProfessionChezLesMeres = $this->getDepistageTable()->getRepartitionProfessionChezLesMeres();
		$totalDifferentsProfessionMeres = array_sum($repartitionProfessionChezLesMeres[1]);
		$tableauDifferentsProfessionMeres = array_values($repartitionProfessionChezLesMeres[1]);
		
		$pourcentageDifferentsProfessionMeres = $this->pourcentage_element_tab($tableauDifferentsProfessionMeres, $totalDifferentsProfessionMeres);
		
		/** PËres --- PËres */
		$repartitionProfessionChezLesPeres = $this->getDepistageTable()->getRepartitionProfessionChezLesPeres();
		$totalDifferentsProfessionPeres = array_sum($repartitionProfessionChezLesPeres[1]);
		$tableauDifferentsProfessionPeres = array_values($repartitionProfessionChezLesPeres[1]);
		
		$pourcentageDifferentsProfessionPeres = $this->pourcentage_element_tab($tableauDifferentsProfessionPeres, $totalDifferentsProfessionPeres);
		
		//var_dump($pourcentageDifferentsProfessionMeres); exit();
		
		
		
		return array (
				'nbPatientD'   => $nbPatientD,
				'nbPatientDN'  => $nbPatientDN,
				'nbPatientDP'  => $nbPatientDP,
				'nbPatientDPF' => $nbPatientDPF,
				'nbPatientDPM' => $nbPatientDPM,
				'pourcentageDepister' => $pourcentageDepister,
				'pourcentageDepisterPositif' => $pourcentageDepisterPositif,
				'pourcentageProfilsPatientsInterne' => $pourcentageProfilsPatientsInterne,
				'typagesPatientsInternes' => $typagesPatientsInternes,
				
				
				'nbPatientDVSF' => $nbPatientDVSF,
				'nbPatientDVSM' => $nbPatientDVSM,
				'pourcentageDepistagesValides' => $pourcentageDepistagesValides,
				'peresNouveauNesSelonEthnies' => $peresNouveauNesSelonEthnies,
				'pourcentagePeresNouveauNesSelonEthnies' => $pourcentagePeresNouveauNesSelonEthnies,
				
				'differentsTypesProfils' => $differentsTypesProfils,
				'pourcentageDifferentsTypesProfils' => $pourcentageDifferentsTypesProfils,
				'repartitionTypesProfilsSelonEthnies' => $repartitionTypesProfilsSelonEthnies,
				
				'repartitionProfessionChezLesMeres' => $repartitionProfessionChezLesMeres,
				'pourcentageDifferentsProfessionMeres' => $pourcentageDifferentsProfessionMeres,
				
				'repartitionProfessionChezLesPeres' => $repartitionProfessionChezLesPeres,
				'pourcentageDifferentsProfessionPeres' => $pourcentageDifferentsProfessionPeres,
		);
		
	}
}
