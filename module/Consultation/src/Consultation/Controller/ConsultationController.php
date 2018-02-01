<?php

namespace Consultation\Controller;

use Zend\Mvc\Controller\AbstractActionController;

use Infirmerie\View\Helper\DateHelper;
use Zend\Json\Json;
use Consultation\Form\ConsultationForm;

class ConsultationController extends AbstractActionController {
	
	protected $consultation;
	protected $personneTable;
	protected $patientTable;
	protected $motifAdmissionTable;
	protected $depistageTable;
	protected $analyseAFaireTable;
	protected $analyseTable;
	
	public function getConsultationTable() {
		if (! $this->consultation) {
			$sm = $this->getServiceLocator ();
			$this->consultation = $sm->get ( 'Infirmerie\Model\ConsultationTable' );
		}
		return $this->consultation;
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
				
				

				
				
				'liste_med' => null, //$listeMedicament,
				'temoin' => 0, //$bandelettes['temoin'],
				// 				'listeForme' => $listeForme,
				// 				'listetypeQuantiteMedicament'  => $listetypeQuantiteMedicament,
				'donneesAntecedentsPersonnels' => null, //$donneesAntecedentsPersonnels,
				'donneesAntecedentsFamiliaux'  => null, //$donneesAntecedentsFamiliaux,
				'liste' => null, //$listeConsultation,
				// 				'resultRV' => $resultRV,
				'listeHospitalisation' => null, //$listeHospitalisation,
				'listeDesExamensBiologiques' => null, //$listeDesExamensBiologiques,
				'listeDesExamensMorphologiques' => null, //$listeDesExamensMorphologiques,
				'listeAntMed' => null, //$listeAntMed,
				'antMedPat' => null, //$antMedPat,
				'nbAntMedPat' => 0, //$antMedPat->count(),
				'listeActes' => null, //$listeActes,
		);

	}
	
	public function demandesAnalysesVueAction() {
	
		$id = ( int ) $this->params ()->fromPost ( 'id', 0 );
	
		/*----------------------------------------------------*/
		/*----------------------------------------------------*/
		$existeADA = 0; //Existance d'Analyses Demandées Aujourdhui
		$listeAnalysesDemandees = $this->getAnalyseTable()->getListeAnalysesDemandeesDP($id);
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
	
	public function getListeAnalysesAction()
	{
		$id = (int)$this->params()->fromPost ('id');
		$liste_select = "";
		if($id == 6){
			foreach($this->getPatientTable()->getListeDesExamenImagerie() as $listeExamens){
				$liste_select.= "<option value='0,".$listeExamens['idexamen']."'>".$listeExamens['designation']."</option>";
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
	
	public function modifierConsultationAction() {
		
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	

	function item_percentage($item, $total){
	
		if($total){
			return number_format(($item * 100 / $total), 1);
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
		
		$nbPatientD   = $this->getDepistageTable()->getNbPatientsDepistes();
		$nbPatientDN  = $this->getDepistageTable()->getNbPatientsDepistesNegatifs();
		$nbPatientDP  = $this->getDepistageTable()->getNbPatientsDepistesPositifs();
		$nbPatientDPF = $this->getDepistageTable()->getNbPatientsDepistesPositifsFeminin();
		$nbPatientDPM = $this->getDepistageTable()->getNbPatientsDepistesPositifsMasculin();
		
		$typagesPatientsInternes = $this->getDepistageTable()->getListeFormesGravesDepistes();
		
		//Pourcentage des patients dépistés
		//Pourcentage des patients dépistés
		$tabNbPatientDepister = array($nbPatientDN, $nbPatientDP);
		$pourcentageDepister = $this->pourcentage_element_tab($tabNbPatientDepister, $nbPatientD);
		
		//Pourcentage des patients dépistés positifs et négatifs
		//Pourcentage des patients dépistés positifs et négatifs
		$tabNbPatientDepisterPositif = array($nbPatientDPM, $nbPatientDPF);
		$pourcentageDepisterPositif = $this->pourcentage_element_tab($tabNbPatientDepisterPositif, $nbPatientDP);
		
		
		//Pourcentage des patients par profil de SDM
		//Pourcentage des patients par profil de SDM
		$totalProfilsPatientsInterne = array_sum($typagesPatientsInternes[1]);
		$tableau = array_values($typagesPatientsInternes[1]);
		$pourcentageProfilsPatientsInterne = $this->pourcentage_element_tab($tableau, $totalProfilsPatientsInterne);
		
		
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
		);
		
	}
}
