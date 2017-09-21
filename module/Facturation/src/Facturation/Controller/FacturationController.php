<?php

namespace Facturation\Controller;

use Zend\Mvc\Controller\AbstractActionController;

use Facturation\View\Helper\DocumentPdf;
use Facturation\View\Helper\FacturePdf;
use Facturation\Form\AdmissionForm;
use Zend\Json\Json;
use Facturation\View\Helper\DateHelper;



class FacturationController extends AbstractActionController {
	protected $patientTable;
	protected $facturationTable;
	protected $analyseTable;
	protected $personneTable;
	protected $codagePrelevement;
	
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
	
	public function getCodagePrelevementTable() {
		if (! $this->codagePrelevement) {
			$sm = $this->getServiceLocator ();
			$this->codagePrelevement = $sm->get ( 'Infirmerie\Model\CodagePrelevementTable' );
		}
		return $this->codagePrelevement;
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
	
	public function numeroFactureConsultation() {
		$derniereFacturation = $this->getFacturationTable()->getDerniereFacturationConsultation();
		if($derniereFacturation){
			return $this->creerNumeroFacturation($derniereFacturation['numero']+1);
		}else{
			return $this->creerNumeroFacturation(1);
		}
	}
	
	public function listeAdmissionAjaxAction() {
	    $output = $this->getPatientTable ()->getListeDemandesDesPatients();
	    return $this->getResponse ()->setContent ( Json::encode ( $output, array (
	        'enableJsonExprFinder' => true
	    ) ) );
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
	
	public function admissionAction() {
		$this->layout ()->setTemplate ( 'layout/facturation' );
		
		$formAdmission = new AdmissionForm ();
		
 		$listeOrganisme = $this->getFacturationTable()->getListeOrganisme();
		 
 		$formAdmission->get ( 'organisme' )->setValueOptions ( $listeOrganisme );
		
		return array (
				'form' => $formAdmission
		);
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
	    $patient = $this->getPatientTable()->getPatient($idpatient);
	    $depistage = $this->getPatientTable()->getDepistagePatient($idpatient);
	    $date_naissance = null;
	    if($personne->date_naissance){ $date_naissance = (new DateHelper())->convertDate( $personne->date_naissance ); }
	    $informations_parentales = $this->getPersonneTable()->getInfosParentales($idpatient);
	     
	    $depister = 0;
	    $type = "Externe";
	    $typage = "";
	    if($depistage->current()){
	        $depister = 1;
	        if($depistage->current()['typepatient'] == 1){
	            $type = "Interne";
	            $typage = "(".$depistage->current()['typage'].")";
	        }
	        else{
	            if($depistage->current()['typage']){
	                if(!in_array($depistage->current()['typage'], array('AA','AB') )){
	                    $typage = "(<span style='color: red;'>".$depistage->current()['typage']."</span>)" ;
	                }else{
	                    $typage = "(".$depistage->current()['typage'].")" ;
	                }
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
	  
				 <td style='width: 75%;' >
	  
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
			   		          <!-- div id='aa'><a style='text-decoration: underline;'>Type</a><br><p style='font-weight: bold; font-size: 19px;'> ".$type." ".$typage."</p></div-->";
	    
	    if($informations_parentales){
	        $html .="<div style='width: 50px; height: 35px; float: right; margin-top: -40px; margin-right: 20px;'><a href='javascript:infos_parentales(".$idpatient.");' > <img id='infos_parentales_".$idpatient."' style='float: right; cursor: pointer;' src='../images_icons/Infos_parentales.png' /> </a></div>";
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
                    <div style='margin-top: 20px; margin-right: 0px; margin-left: -5px; font-size:17px; font-family: Iskoola Pota; color: green; float: right; font-style: itali; opacity: 1;'> ".$patient->numero_dossier." </div>
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
	    	
	    $html .= "<div id='titre_info_liste_analyses'>Liste des demandes</div>";
	    $html .= "<div id='barre_separateur'></div>";
	    
	    $html .= "<table style='margin-top:10px; margin-left:17.5%; width: 80%;'>";
	    $html .= "<tr style='width: 100%;'>";
	    $html .= "<td style='width: 100%;' id='listeDemandesAnalysesTableau'>";
	    
	    
	    $html .="<table class='table table-bordered tab_list_mini'  id='listeDemandesAnalysesTab' style='margin-top:10px; width: 95%;' >";
	    
	    $html .="<thead style='width: 100%;'>
				   <tr style='height:25px; cursor:pointer;'>
				      <th id='numeroD' style='cursor: pointer; text-align: center;' >N&ordm;<minus></minus></th>
	    		      <th id='date_heureD' style='cursor: pointer;' >D<minus>ate & heure</minus></th>
					  <th id='demandee_parD' style='cursor: pointer;' >E<minus>ffectu&Eacute;e par</minus></th>
	    		      <th id='nb_analyse_demandeeD' style='cursor: pointer;' >N<minus>b Analyses </minus></th>
	                  <th id='optionD' style='cursor: pointer;' >A<minus>fficher</minus></th>
				   </tr>
			     </thead>";
	     
	    $html .="<tbody  class='liste_patient liste_demandes' style='width: 100%;'>";
	     
     
	    $listeDemandes = $this->getAnalyseTable() ->getListeDemandesAnalysesNonFacturees($idpatient);
	    
	    $dateDerniereDemande = "";
	    
	    for($i = 0 ; $i < count($listeDemandes) ; $i++){
	    
	    	//Recuperer la date de la derniere demande
	    	if($i == 0){ $dateDerniereDemande = $listeDemandes[$i]['date']; }
	    	$nbAnalyses = $this->getAnalyseTable() ->getnbAnalysesDeLaDemandeNonFacturees($idpatient, $listeDemandes[$i]['date']);
	    	
	    	$dateDemande = str_replace("-", "", $listeDemandes[$i]['date']);
	    	
	    	$html .="<tr style='height:10px; width:100%; font-family: times new roman;'>
	    			    <td id='numeroD_' style='font-size: 15px; text-align: center; '> ".($i+1)." </td>
 					    <td id='date_heureD_' style='font-size: 15px;'> ".(new DateHelper())->convertDate( $listeDemandes[$i]['date'] )." - ".$listeDemandes[$i]['time']." </td>
 					    <td id='demandee_parD_' style='font-size: 15px;'> ".$listeDemandes[$i]['PrenomSecretaire']." ".$listeDemandes[$i]['NomSecretaire']." </td>
 					    <td id='nb_analyse_demandeeD_' style='font-size: 17px; font-weight: bold; '> <div style='float: right; margin-right: 20px;'> ".$nbAnalyses." </div> </td>		
 				        <td id='optionD_' style='font-size: 15px; '>  <div style='display: inline; margin-right: 20px; margin-left: 15px; cursor: pointer;'> <a href='javascript: afficherLaListeDesAnalysesDelaDemande(".$idpatient.",".$dateDemande.",".($i+1).")'> <img  src='../images_icons/Table16X16.png' title='les analyses' /> </a> </div> <div style='display: inline; margin-right: 10px;' class='listeAAfficher listeAAfficher_".($i+1)."'>  </div> </td>
 				     </tr>";
	    	 
	    }
	    
	    $html .="</tbody>";
	     
	    $html .="</table>";
	    
	    
	    $html .= "</td>";
	    $html .= "</tr>";
	    $html .= "</table>";
	    
	    
	    
	    
	    
	    
	    $html .= "<div id='titre_info_liste_analyses'>Liste des analyses de la demande n&ordm; <span id='numOrdre'>1</span> </div>";
	    $html .= "<div id='barre_separateur'></div>";

	    $html .= "<div id='listeDesAnalysesTableau' style=' margin-left:17.5%; '>";
	    $html .= "<table style='margin-top:10px; width: 97%;'>";
	    $html .= "<tr style='width: 100%;'>";
	    $html .= "<td style='width: 100%; '>";
	    
	    
	    $html .="<div style='margin-right: 5px; float:right; font-size: 15px; color: gray; margin-top: 10px;'><a style='text-decoration: none; cursor:pointer;' href='javascript:ChoixDesAnalyses(".$idpatient.")' ><i style='font-family: Times New Roman; font-size: 15px; color: green;'>  </i></a></div>";
	    $html .="<table class='table table-bordered tab_list_mini' >";
	     
	    $html .="<thead style='width: 100%;'>
				   <tr style='height:25px; width:100%; cursor:pointer;'>
				      <th id='typeA' style='cursor: pointer;'>Type</th>
					  <th id='analyseA' style='cursor: pointer;'>Analyse</th>
	                  <th id='tarifA' style='cursor: pointer; ' >Tarif (FCFA)</th>
				      <th id='choixA' style='' > <div style='width: 50%; float: left;'> Choix </div> <div style='width: 50%; float: left; font-size: 10px;' id='choixCheckedTout'> Tout <input type='checkbox' name='nameChoixCheckedTout'> </div> </th>
				   </tr>
			     </thead>";
	    
	    $html .="<tbody  class='liste_patient liste_analyses_demandes' style='width: 100%;'>";
	    
	    $html .="<script> 
	               var tableauIddemande = [];
	               var i = 0; 
	        
	               var tableauIddemandeChoisi = [];
	             </script>";
	    
	    //La liste des analyses de la derniere demande
	    //La liste des analyses de la derniere demande
	    if($dateDerniereDemande){
	    	$listeAnalysesDemandees = $this->getAnalyseTable()->getListeAnalysesDerniereDemandeNonFacturees($idpatient, $dateDerniereDemande);
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
	        
	      	$('.listeAAfficher_1').html('<img  src=\'../images_icons/transfert_bas.png\' title=\'la liste en bas\'/>');
	      	</script>";	
	    $html .= "</div>";
	    
	    $html .=
	      "<script>
	        $('#numero').val('".$this->numeroFacture()."');
	        $('#numero').css({'background':'#eee','border-bottom-width':'0px','border-top-width':'0px','border-left-width':'0px','border-right-width':'0px','font-weight':'bold','color':'green','font-family': 'Times  New Roman','font-size':'18px'});
	        $('#numero').attr('readonly',true);
	    
	        $('#organisme').css({'font-weight':'bold','color':'green','font-family': 'Times  New Roman','font-size':'13px'});
	        $('#service').css({'background':'#eee','border-bottom-width':'0px','border-top-width':'0px','border-left-width':'0px','border-right-width':'0px','font-weight':'bold','color':'green','font-family': 'Times  New Roman','font-size':'18px'});
	        $('#service').html('<option>Infirmier</option>').attr('disabled',true);
	        $('#taux').css({'font-weight':'bold','color':'#065d10','padding-left':'10px','font-family': 'Times  New Roman','font-size':'24px'});
	        $('#montant_avec_majoration').css({'background':'#eee','border-bottom-width':'0px','border-top-width':'0px','border-left-width':'0px','border-right-width':'0px','font-weight':'bold','color':'green','font-family': 'Time  New Romans','font-size':'24px'});
	        //$('#montant_avec_majoration').attr('readonly',true);
	        $('#idpatient').val(".$idpatient.");
	          
	        listeDemandesAnalyses();
	        
	       </script>";

	    $this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
	    return $this->getResponse ()->setContent ( Json::encode ( $html ) );

	}
	
	public function listeDesAnalysesDelaDemandeSelectionneeAction() {
		
		$idpatient = ( int ) $this->params ()->fromPost ( 'idpatient', 0 );
		$date = (new DateHelper())->convertDateFormatyyyymmdd( $this->params ()->fromPost ( 'date', 0 ) );
		$numOrdre = ( int ) $this->params ()->fromPost ( 'numOrdre', 0 );
		
		$html  = "<table style='margin-top:10px; width: 97%;'>";
		$html .= "<tr style='width: 100%;'>";
		$html .= "<td style='width: 100%; '>";
		 
		 
		$html .="<div style='margin-right: 5px; float:right; font-size: 15px; color: gray; margin-top: 10px;'><a style='text-decoration: none; cursor:pointer;' href='javascript:ChoixDesAnalyses(".$idpatient.")' ><i style='font-family: Times New Roman; font-size: 15px; color: green;'>  </i></a></div>";
		$html .="<table class='table table-bordered tab_list_mini' >";
		
		$html .="<thead style='width: 100%;'>
				   <tr style='height:25px; width:100%; cursor:pointer;'>
				      <th id='typeA' style='cursor: pointer;'>Type</th>
					  <th id='analyseA' style='cursor: pointer;'>Analyse</th>
	                  <th id='tarifA' style='cursor: pointer; ' >Tarif (FCFA)</th>
				      <th id='choixA' style='' > <div style='width: 50%; float: left;'> Choix </div> <div style='width: 50%; float: left; font-size: 10px;' id='choixCheckedTout'> Tout <input type='checkbox' name='nameChoixCheckedTout'> </div> </th>
				   </tr>
			     </thead>";
		 
		$html .="<tbody  class='liste_patient liste_analyses_demandes' style='width: 100%;'>";
		 
		$html .="<script>
	               var tableauIddemande = [];
	               var i = 0;
	    
	               var tableauIddemandeChoisi = [];
	             </script>";
		 
		//La liste des analyses de la derniere demande
		//La liste des analyses de la derniere demande
		if($date){
			$listeAnalysesDemandees = $this->getAnalyseTable()->getListeAnalysesDerniereDemandeNonFacturees($idpatient, $date);
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
	    
	        $('#numOrdre').html(".$numOrdre.");   
	      	$('.listeAAfficher').html('');
	      	$('.listeAAfficher_".$numOrdre."').html('<img  src=\'../images_icons/transfert_bas.png\' title=\'la liste en bas\' />');
	      			
	      	$('img').tooltip({ animation: true, html: true, placement: 'bottom',
              show: { effect: 'slideDown', delay: 250 }
            });

	      	var boutons = $('input[name=type_facturation]');
	        $(boutons[0]).trigger('click');
	      	$('.organisme').toggle(false); $('#organisme').val('');
			$('.taux').toggle(false); $('#taux').val('');
   			tarifFact = 0;
	      	$('#montant_avec_majoration').val('');
	      			
	       </script>";
		
		
		
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $html ) );
	} 
	
	public function enregistrerAdmissionAction() {
	    $user = $this->layout()->user;
	    $idemploye = $user['idemploye'];
	
	    $today = new \DateTime ( "now" );
	    $date = $today->format ( 'Y-m-d' );
	    $heure = $today->format ( 'H:i:s' );
	    $date_enregistrement = $today->format ( 'Y-m-d H:i:s' );
	
	    $idpatient = ( int ) $this->params ()->fromPost ( 'idpatient', 0 );
	    $numero = $this->params ()->fromPost ( 'numero' );
	    $montant = $this->params ()->fromPost ( 'montant' );
	    $type_facturation = $this->params ()->fromPost ( 'type_facturation' );
	    $liste_demandes_analyses = explode( "," ,$this->params ()->fromPost ( 'listeanalysesselectionnees' ));
	
	    $donnees = array (
	        'idpatient' => $idpatient,
	        'montant' => (int)$montant,
	        'numero' => $numero,
	        'date' => $date,
	        'heure' => $heure,
	        'date_enregistrement' => $date_enregistrement,
	        'idemploye' => $idemploye,
	    );
	    
	    if($type_facturation == 2){
	        $donnees['id_type_facturation'] = 2;
	        $donnees['organisme'] = $this->params ()->fromPost ( 'organisme' );
	        $donnees['taux_majoration'] = $this->params ()->fromPost ( 'taux' );
	        $donnees['montant_avec_majoration'] = (int)str_replace(" ", "",$this->params ()->fromPost ( 'montant_avec_majoration' ));
	    } 
	    else
	    if($type_facturation == 1){
	        $donnees['id_type_facturation'] = 1;
	    }
	    
	    //Ajouter la facturation
	    $idfacturation = $this->getFacturationTable() ->addFacturation( $donnees );
	    	
	    //Ajouter la liste des analyses pour lesquelles le patient est admis ‡ l'infirmerie pour prÈlËvement
	    $this->getFacturationTable() ->addAnalyses( $idfacturation , $liste_demandes_analyses ); 

	    //Ajouter la liste des codes des tubes et des autres instruments de prÈlËvements
	    $this->creerCodePrelevementAction($idfacturation);
	    
	    
	    return $this->redirect()->toRoute('facturation', array('action' =>'liste-patients-admis' , 'idfacturation' => $idfacturation));
	}


	public function listePatientsAdmisAjaxAction() {
	    $output = $this->getPatientTable ()->getListePatientsAdmis();
	    return $this->getResponse ()->setContent ( Json::encode ( $output, array (
	        'enableJsonExprFinder' => true
	    ) ) );
	}
	
	
	public function listePatientsAdmisAction() {
		
		$this->layout ()->setTemplate ( 'layout/facturation' );
		
		$idfacturation = $this->params ()->fromRoute ( 'idfacturation' , 0 );
		
		//$existeResult = $this->getFacturationTable()->verifierExistePrelevementFacturation(69);
		//var_dump($existeResult); exit();
		
 		return  array (
 				'idfacturation' => $idfacturation
 		);
	}
	
	
	//Liste des analyses facturÈes n'ayant pas enncore de rÈsultats
	public function listeAnalysesFactureesAction() {
	    
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
	                  <th id='tarifA' style='cursor: pointer; ' >Tarif (FCFA)</th>
				   </tr>
			     </thead>";
	     
	    $html .="<tbody  class='liste_patient liste_analyses_demandes' style='width: 100%;'>";
	     
 	    $listeAnalysesDemandees = $this->getFacturationTable()->getListeAnalysesFacturees($idfacturation);
 	    for($i = 0 ; $i < count($listeAnalysesDemandees) ; $i++){
	    
 	    	$html .="<tr style='height:20px; width:100%; font-family: times new roman;'>
 					    <td id='typeA' style='font-size: 13px;'> ".$listeAnalysesDemandees[$i]['libelle']." </td>
  					    <td id='analyseA' style='font-size: 13px;'> ".$listeAnalysesDemandees[$i]['designation']." </td>
  				        <td id='tarifA' style='font-size: 15px;'> <div style='float: right;'> ".$this->prixMill($listeAnalysesDemandees[$i]['tarif'])." </div>  </td>
  				     </tr>";
	    	 
	    	 
 	    }
	    
	    $html .="</tbody>";
	     
	    $html .="</table>";
	     
	     
	    $html .= "</tr>";
	    $html .= "</table>";
	    
	
	    $html .="<div id='titre_info_admis'>Informations sur la facturation <img id='button_pdf' style='width:15px; height:15px; float: right; margin-right: 35px; cursor: pointer;' src='".$this->baseUrl()."public/images_icons/button_pdf.png' title='Imprimer la facture' ></div>";
	    $html .="<div id='barre_separateur'></div>";
	    
	    $html .="<table style='margin-top:10px; margin-left:17.5%; width: 80%; margin-bottom: 60px;'>";
	    
	    $html .="<tr style='width: 80%; '>";
	    $html .="<td style='width: 25%; vertical-align:top; margin-right:10px;'><span id='labelHeureLABEL' style='padding-left: 5px;'>Num&eacute;ro de la facture </span><br><p id='zoneChampInfo1' style='background:#f8faf8; padding-left: 5px; padding-top: 5px;'> ".$facturation['numero']." </p></td>";
	    $html .="<td style='width: 25%; vertical-align:top; margin-right:10px;'><span id='labelHeureLABEL' style='padding-left: 5px;'>Date de la facturation </span><br><p id='zoneChampInfo1' style='background:#f8faf8; padding-left: 5px; padding-top: 5px;'> ".(new DateHelper())->convertDateTime($facturation['date_enregistrement'])." </p></td>";
	    $html .="<td style='width: 25%; vertical-align:top; margin-right:10px;'><span id='labelHeureLABEL' style='padding-left: 5px;'>Tarif (FCFA) </span><br><p id='zoneChampInfo1' style='background:#f8faf8; padding-left: 5px; padding-top: 5px; font-weight:bold; font-size:22px;'> ". $this->prixMill($facturation['montant']) ." </p></td>";
	    $html .="<td style='width: 25%; vertical-align:top; margin-right:10px;'></td>";
	    $html .="</tr>";
	    

 	    if($facturation['id_type_facturation'] == 2){

 	    	$organisme = $this->getFacturationTable()->getOrganisme($facturation['organisme']);
 	    	$html .="<tr style='width: 80%; '>";
 	    	$html .="<td style='width: 25%; vertical-align:top; margin-right:10px;'><span id='labelHeureLABEL' style='padding-left: 5px;'>Facture prise en charge par  </span><br><p id='zoneChampInfo1' style='background:#f8faf8; padding-left: 5px; padding-top: 5px;'> ". $organisme['libelle'] ." </p></td>";
 	     	if($facturation['taux_majoration']){
 	     		$html .="<td style='width: 25%; vertical-align:top; margin-right:10px;'><span id='labelHeureLABEL' style='padding-left: 5px;'>Taux (%) </span><br><p id='zoneChampInfo1' style='background:#f8faf8; padding-left: 5px; padding-top: 5px; font-weight:bold; font-size:22px;'> ". $facturation['taux_majoration'] ." </p></td>";
 	     	}else {
 	     		$html .="<td style='width: 25%; vertical-align:top; margin-right:10px;'><span id='labelHeureLABEL' style='padding-left: 5px;'>Taux (%) </span><br><p id='zoneChampInfo1' style='background:#f8faf8; padding-left: 5px; padding-top: 5px; font-weight:bold; font-size:22px;'> 0 </p></td>";
 	     	}
 	     	$majoration = ($facturation['montant'] * $facturation['taux_majoration'])/100;
 	     	$html .="<td style='width: 25%; vertical-align:top; margin-right:10px;'><span id='labelHeureLABEL' style='padding-left: 5px;'>Majoration (FCFA) </span><br><p id='zoneChampInfo1' style='background:#f8faf8; padding-left: 5px; padding-top: 5px; font-weight:bold; font-size:22px;'> ". $this->prixMill("$majoration") ." </p></td>";
 	     	$html .="<td style='width: 25%; vertical-align:top; margin-right:10px;'><span id='labelHeureLABEL' style='padding-left: 5px;'>Tarif major&eacute; (FCFA) </span><br><p id='zoneChampInfo1' style='background:#f8faf8; padding-left: 5px; padding-top: 5px; font-size:15px; font-weight:bold; font-size:22px;'> ". $this->prixMill( $facturation['montant_avec_majoration'] ) ."  </p></td>";
 	     	$html .="</tr>";

 	    }
	    
	    $html .="</table>";
	    

	    $html .="<div style='color: white; opacity: 1; margin-top: -50px; margin-left:50px; width:95px; height:40px; float:left'>
	                <img  src='../images_icons/fleur1.jpg' />
	             </div>";
	    
	    $html .="<script>
	    
				  $('#button_pdf').click(function(){
				     var vart = '".$this->baseUrl()."public/facturation/impression-facture';
				     var formulaire = document.getElementById('FormulaireImprimerFacture');
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
	    
				  $('a,img,hass').tooltip({
                  animation: true,
                  html: true,
                  placement: 'bottom',
                  show: {
                    effect: 'slideDown',
                      delay: 250
                    }
                  });
	    
				 </script>";
	    
	    $this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
	    return $this->getResponse ()->setContent ( Json::encode ( $html ) );
	}
	
	
	public function impressionFactureAction(){
		$idfacturation = (int)$this->params()->fromPost ('idfacturation');
	
		//Informations sur la facturation
		$facturation = $this->getFacturationTable()->getFacturation($idfacturation);
		$listeAnalysesDemandees = $this->getFacturationTable()->getListeAnalysesFacturees($idfacturation);
		
		if($facturation){
			$idpatient = $facturation['idpatient'];
			$depistage = $this->getPatientTable()->getDepistagePatient($idpatient);

			
			$user = $this->layout()->user;
			$service = $user['NomService'];
			
			//******************************************************
			//******************************************************
			//*********** DONNEES COMMUNES A TOUS LES PDF **********
			//******************************************************
			//******************************************************
			$lePatient = $this->getPatientTable()->getInfoPatient( $idpatient );
				
			$infos = array(
					'numero' => $facturation['numero'],
					'service' => $service,
					'montant' => $facturation['montant'],
					'montant_avec_majoration' => $facturation['montant_avec_majoration'],
					'type_facturation' => $facturation['id_type_facturation'],
					'organisme' => $this->getFacturationTable()->getOrganisme( $facturation['organisme'] )['libelle'],
					'taux' => $facturation['taux_majoration'],
			);
			
			
			//******************************************************
			//******************************************************
			//*************** CrÈation du fichier pdf **************
			//******************************************************
			//******************************************************
			//CrÈer le document
			$DocPdf = new DocumentPdf();
			//CrÈer la page
			$page = new FacturePdf();
				
			//entrez les donnÈes sur le partient
			$page->setDonneesPatient($lePatient);
			
			//Entrer les donnÈes sur la liste des analyses
			$page->setListeAnalysesDemndees($listeAnalysesDemandees);
			
			$page->setService($service);
			$page->setInformations($infos);
			$page->setDepistage($depistage);
			
			//Ajouter une note ‡ la page
			$page->addNote();
			//Ajouter la page au document
			$DocPdf->addPage($page->getPage());
			//Afficher le document contenant la page
				
			$DocPdf->getDocument();
				
		} else {
			var_dump("Impossible d'imprimer"); exit();
		}
	
	}
	
	public function supprimerFacturationAction(){
	
		$idfacturation = (int)$this->params()->fromPost ('idfacturation');
		
		$existeResult = $this->getFacturationTable()->deleteFacturation($idfacturation); 
	
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse()->setContent(Json::encode($existeResult));
	}
	
	public function supprimerFacturationConsultationAction(){
	
		$idfacturation = (int)$this->params()->fromPost ('idfacturation');
	
		$existeResult = $this->getFacturationTable()->deleteFacturationConsultation($idfacturation);
	
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse()->setContent(Json::encode($existeResult));
	}
	
	
	public function historiqueListePatientsAdmisAjaxAction() {
		$output = $this->getPatientTable ()->getHistoriqueListePatientsAdmis();
		return $this->getResponse ()->setContent ( Json::encode ( $output, array (
				'enableJsonExprFinder' => true
		) ) );
	}
	
	
	public function historiqueListePatientsAdmisAction() {
		$this->layout ()->setTemplate ( 'layout/facturation' );
		
		return  array (	);
	}
	
	
	
	//Historique de la liste des analyses facturÈes 
	public function historiqueListeAnalysesFactureesAction() {
		 
		$idpersonne = ( int ) $this->params ()->fromPost ( 'idpersonne', 0 );
		
		$listeFactures = $this->getFacturationTable()->getListeFacturesDuPatient($idpersonne);
		 
		$idfacturation = $listeFactures->current()['idfacturation'];
		
		$listeFactures = $this->getFacturationTable()->getListeFacturesDuPatient($idpersonne);
		
		$facturation = $this->getFacturationTable()->getFacturation( $idfacturation );
		 
		$today = new \DateTime ();
		$dateAujourdhui = $today->format( 'Y-m-d' );
	
		$html = $this->etatCivilPatientAction($idpersonne);
		 
		//LISTE DES FACTURES ----- LISTE DES FACTURES ----- LISTE DES FACTURES
		//LISTE DES FACTURES ----- LISTE DES FACTURES ----- LISTE DES FACTURES
		$html .= "<div id='titre_info_admis'>Liste des factures</div>";
		$html .= "<div id='barre_separateur'></div>";
			
		$html .= "<table style='margin-left:17.5%; width: 80%; margin-top:10px;'>";
		$html .= "<tr style='width: 100%;'>";
		$html .= "<td style='width: 100%; ' id='listeDesFacturesHistoriquesTableau'>";
		
		
		$html .="<table class='table table-bordered tab_list_mini'  id='listeDesFacturesHistoriques' >";
			
		$html .="<thead style='width: 100%;'>
				   <tr style='height:25px; width:100%; cursor:pointer;'>
				      <th id='numeroF' style='cursor: pointer;'>N<minus>um&eacute;ro</minus></th>
					  <th id='dateF' style='cursor: pointer; width: 150px;'>D<minus>ate de la facturation</minus></th>
	                  <th id='factureF' style='cursor: pointer;'>F<minus>actur&eacute;e par</minus></th>      
    				  <th id='tarifF' style='cursor: pointer; width: 120px;' >T<minus>arif total</minus> (FCFA)</th>
				      <th id='afficherF' style='cursor: pointer; width: 80px;'>A<minus>fficher</minus></th>
				   </tr>
			     </thead>";
		
		$html .="<tbody  class='liste_patient liste_analyses_demandes' style='width: 100%;'>";
		
		$numero = "";
		$ij = 0;
		foreach ($listeFactures as $liste){
		
			if($ij == 0){ $ij = 1; $numero = $liste['numero']; }
			
			$html .="<tr style='height:20px; width:100%; font-family: times new roman;'>
 					    <td id='zoneChampInfo1' > ".$liste['numero']." </td>
  					    <td id='zoneChampInfo1' style='width: 150px;'> ".(new DateHelper())->convertDateTime($liste['date_enregistrement'])." </td>
  				        <td id='zoneChampInfo1' > ".$liste['Prenom']." ".$liste['Nom']." </td>
  				        <td id='zoneChampInfo1' style='font-weight:bold; font-size:20px; width: 120px;'> <div style='float: right; margin-right: 15px;'> ".$this->prixMill($liste['montant'])." </div></td>
  				        <td id='zoneChampInfo1' style='width: 80px;'> <a href='javascript:afficherListeAnalysesDeLaFacture(".$liste['idfacturation'].",".(int)$liste['numero'].")'><img  style='width:15px; height:15px; float: right; margin-right: 35px;' src='".$this->baseUrl()."public/images_icons/right_16.png' ></a> </td>
  				     </tr>";
		
		}
		
		$html .="</tbody>";
		
		$html .="</table>";
		
		$html .= "</td>";
		$html .= "</tr>";
		$html .= "</table>";
		
		
		$html .= "<div id='titre_info_admis'>Liste des analyses de la facture n&ordm; <span id='numFacture'>".$numero."</span> <img id='button_pdf' style='width:15px; height:15px; float: right; margin-right: 35px; cursor: pointer;' src='".$this->baseUrl()."public/images_icons/button_pdf.png' title='Imprimer la facture' ></div>";
		$html .= "<div id='barre_separateur'></div>";
		 
		$html .= "<table style='margin-left:17.5%; width: 80%;'>";
		$html .= "<tr style='width: 100%;'>";
		$html .= "<td style='width: 100%;' id='historiqueListeDesAnalysesDemandees'>";
	
		//EMPLACEMENT OU EST PLACE LA LISTE DES ANALYSES DEMANDEES
		$html .="<table class='table table-bordered tab_list_mini' style='margin-top:10px;'>";
			
		$html .="<thead style='width: 100%;'>
				   <tr style='height:25px; width:100%; cursor:pointer;'>
				      <th id='typeA' style='cursor: pointer;'>Type</th>
					  <th id='analyseA' style='cursor: pointer;'>Analyse</th>
	                  <th id='tarifA' style='cursor: pointer; ' >Tarif (FCFA)</th>
				   </tr>
			     </thead>";
		
		$html .="<tbody  class='liste_patient liste_analyses_demandes' style='width: 100%;'>";
		
		$listeAnalysesDemandees = $this->getFacturationTable()->getListeAnalysesFacturees($idfacturation);
		for($i = 0 ; $i < count($listeAnalysesDemandees) ; $i++){
		
			$html .="<tr style='height:20px; width:100%; font-family: times new roman;'>
 					    <td id='typeA' style='font-size: 13px;'> ".$listeAnalysesDemandees[$i]['libelle']." </td>
  					    <td id='analyseA' style='font-size: 13px;'> ".$listeAnalysesDemandees[$i]['designation']." </td>
  				        <td id='tarifA' style='font-size: 15px;'> <div style='float: right;'> ".$this->prixMill($listeAnalysesDemandees[$i]['tarif'])." </div>  </td>
  				     </tr>";
		
		
		}
			
		$html .="</tbody>";
		
		$html .="</table>";
		
		
		$html .= "</td>";
		$html .= "</tr>";
		$html .= "</table>";
		 
	
		$html .="<div style='color: white; opacity: 1; margin-top: -50px; margin-left:50px; width:95px; height:40px; float:left'>
	                <img  src='../images_icons/fleur1.jpg' />
	             </div>";
		 
		$html .="<script>
	  
				  $('#button_pdf').click(function(){
				     vart='".$this->baseUrl()."public/facturation/impression-facture';
				     var formulaire = document.createElement('form');
			         formulaire.setAttribute('action', vart);
			         formulaire.setAttribute('method', 'POST');
			         formulaire.setAttribute('target', '_blank');
	  
				     document.body.appendChild(formulaire);
				     		
				     var champ = document.createElement('input');
				     champ.setAttribute('type', 'hidden');
				     champ.setAttribute('name', 'idfacturation');
				     champ.setAttribute('value', ".$idfacturation.");
				     formulaire.appendChild(champ);
	
				     formulaire.submit();
	              });
	  
				  $('a,img,hass').tooltip({
                  animation: true,
                  html: true,
                  placement: 'bottom',
                  show: {
                    effect: 'slideDown',
                      delay: 250
                    }
                  });
	  
				  listeDesFacturesHistoriques();   		
				 </script>";
		 
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $html ) );
	}
	
	
	public function historiqueListeAnalysesDeLaFactureAction() { 
		$idfacturation = ( int ) $this->params ()->fromPost ( 'idfacturation', 0 );
		$numeroFacture = $this->params ()->fromPost ( 'numeroFacture', 0 );
		
		$html ="<table class='table table-bordered tab_list_mini' style='margin-top:10px;'>";
			
		$html .="<thead style='width: 100%;'>
				   <tr style='height:25px; width:100%; cursor:pointer;'>
				      <th id='typeA' style='cursor: pointer;'>Type</th>
					  <th id='analyseA' style='cursor: pointer;'>Analyse</th>
	                  <th id='tarifA' style='cursor: pointer; ' >Tarif (FCFA)</th>
				   </tr>
			     </thead>";
		
		$html .="<tbody  class='liste_patient liste_analyses_demandes' style='width: 100%;'>";
		
		$listeAnalysesDemandees = $this->getFacturationTable()->getListeAnalysesFacturees($idfacturation);
		for($i = 0 ; $i < count($listeAnalysesDemandees) ; $i++){
		
			$html .="<tr style='height:20px; width:100%; font-family: times new roman;'>
 					    <td id='typeA' style='font-size: 13px;'> ".$listeAnalysesDemandees[$i]['libelle']." </td>
  					    <td id='analyseA' style='font-size: 13px;'> ".$listeAnalysesDemandees[$i]['designation']." </td>
  				        <td id='tarifA' style='font-size: 15px;'> <div style='float: right;'> ".$this->prixMill($listeAnalysesDemandees[$i]['tarif'])." </div>  </td>
  				     </tr>";
		
		
		}
			
		$html .="</tbody>";
		
		$html .="</table>";
		
		
		$html .="<script>
	 
				  $('#button_pdf').click(function(){
				     vart='".$this->baseUrl()."public/facturation/impression-facture';
				     var formulaire = document.createElement('form');
			         formulaire.setAttribute('action', vart);
			         formulaire.setAttribute('method', 'POST');
			         formulaire.setAttribute('target', '_blank');
				     		
				     document.body.appendChild(formulaire);
	 
				     var champ = document.createElement('input');
				     champ.setAttribute('type', 'hidden');
				     champ.setAttribute('name', 'idfacturation');
				     champ.setAttribute('value', ".$idfacturation.");
				     formulaire.appendChild(champ);
		
				     formulaire.submit();
	              });
	 
				  $('a,img,hass').tooltip({
                  animation: true,
                  html: true,
                  placement: 'bottom',
                  show: {
                    effect: 'slideDown',
                      delay: 250
                    }
                  });
	 
				  $('#numFacture').html('".$this->creerNumeroFacturation($numeroFacture)."');   		
				 </script>";
		
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $html ) );
		
	}
	
	

	public function listeAdmissionConsultationAjaxAction() {
		$output = $this->getPatientTable ()->getListeDesPatientsAdmissionConsultation();
		return $this->getResponse ()->setContent ( Json::encode ( $output, array (
				'enableJsonExprFinder' => true
		) ) );
	}
	
	
	public function admissionConsultationAction() {
		$this->layout ()->setTemplate ( 'layout/facturation' );
	
		$formAdmission = new AdmissionForm ();
	
		$listeOrganisme = $this->getFacturationTable()->getListeOrganisme();
			
		$formAdmission->get ( 'organisme' )->setValueOptions ( $listeOrganisme );
	
		return array (
				'form' => $formAdmission
		);
	}
	
	public function vueAdmissionConsultationAction()
	{

		$idpatient = ( int ) $this->params ()->fromPost ( 'idpatient', 0 );
		 
		$today = new \DateTime ();
		$dateAujourdhui = $today->format( 'Y-m-d' );
		 
		$html = $this->etatCivilPatientAction($idpatient);
		
		
		$html .=
		"<script>
	        $('#numero').val('".$this->numeroFactureConsultation()."');
	        $('#numero').css({'background':'#eee','border-bottom-width':'0px','border-top-width':'0px','border-left-width':'0px','border-right-width':'0px','font-weight':'bold','color':'green','font-family': 'Times  New Roman','font-size':'18px'});
	        $('#numero').attr('readonly',true);
	  
	        $('#montant_avec_majoration').css({'background':'#eee','border-bottom-width':'0px','border-top-width':'0px','border-left-width':'0px','border-right-width':'0px','font-weight':'bold','color':'green','font-family': 'Times  New Roman','font-size':'18px'});
	        $('#montant_avec_majoration').val('0').attr('disabled',true);
	        $('#idpatient').val(".$idpatient.");
	     
	        listeDemandesAnalyses();
	    
	       </script>";
		
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $html ) );
		
	}
	
	
	public function enregistrerAdmissionConsultationAction() {
		$user = $this->layout()->user;
		$idemploye = $user['idemploye'];
	
		$today = new \DateTime ( "now" );
		$date = $today->format ( 'Y-m-d' );
		$heure = $today->format ( 'H:i:s' );
		$date_enregistrement = $today->format ( 'Y-m-d H:i:s' );
	
		$idpatient = ( int ) $this->params ()->fromPost ( 'idpatient', 0 );
		$numero = $this->params ()->fromPost ( 'numero' );
	
		
		$donnees = array (
				'idpatient' => $idpatient,
				'numero' => $numero,
				'date' => $date,
				'heure' => $heure,
				'date_enregistrement' => $date_enregistrement,
				'idemploye' => $idemploye,
		);
		
		//Ajouter la facturation
		$idfacturation = $this->getFacturationTable() ->addFacturationConsultation( $donnees );
	
		return $this->redirect()->toRoute('facturation', array('action' =>'liste-patients-admis-consultation'));
	}
	
	
	public function listePatientsAdmisConsultationAjaxAction() {
		$output = $this->getPatientTable ()->getListePatientsAdmisConsultation();
		return $this->getResponse ()->setContent ( Json::encode ( $output, array (
				'enableJsonExprFinder' => true
		) ) );
	}
	
	
    public function listePatientsAdmisConsultationAction() {
		
		$this->layout ()->setTemplate ( 'layout/facturation' );
		
		$idfacturation = $this->params ()->fromRoute ( 'idfacturation' , 0);
		
		$output = $this->getPatientTable ()->getListePatientsAdmisConsultation();
		
 		return  array (
 				'idfacturation' => $idfacturation
 		);
	}
	
	public function vueInfosPatientsAdmissionConsultationAction() {
		 
		$idfacturation = ( int ) $this->params ()->fromPost ( 'idfacturation', 0 );
		 
		$facturation = $this->getFacturationTable()->getFacturationConsultation( $idfacturation );
		 
		$today = new \DateTime ();
		$dateAujourdhui = $today->format( 'Y-m-d' );
	
		$html = $this->etatCivilPatientAction($facturation['idpatient']);
		 
		$html .="<div id='titre_info_admis'>Informations sur la facturation </div>";
		$html .="<div id='barre_separateur'></div>";
		 
		$html .="<table style='margin-top:10px; margin-left:17.5%; width: 80%; margin-bottom: 60px;'>";
		 
		$html .="<tr style='width: 80%; '>";
		$html .="<td style='width: 25%; vertical-align:top; margin-right:10px;'><span id='labelHeureLABEL' style='padding-left: 5px;'>Num&eacute;ro de la facture </span><br><p id='zoneChampInfo1' style='background:#f8faf8; padding-left: 5px; padding-top: 5px;'> ".$facturation['numero']." </p></td>";
		$html .="<td style='width: 25%; vertical-align:top; margin-right:10px;'><span id='labelHeureLABEL' style='padding-left: 5px;'>Date de la facturation </span><br><p id='zoneChampInfo1' style='background:#f8faf8; padding-left: 5px; padding-top: 5px;'> ".(new DateHelper())->convertDateTime($facturation['date_enregistrement'])." </p></td>";
		$html .="<td style='width: 25%; vertical-align:top; margin-right:10px;'><span id='labelHeureLABEL' style='padding-left: 5px;'>Tarif (FCFA) </span><br><p id='zoneChampInfo1' style='background:#f8faf8; padding-left: 5px; padding-top: 5px; font-weight:bold; font-size:22px;'> 0 </p></td>";
		$html .="<td style='width: 25%; vertical-align:top; margin-right:10px;'></td>";
		$html .="</tr>";
		 
		$html .="</table>";
		 
	
		$html .="<div style='color: white; opacity: 1; margin-top: -50px; margin-left:50px; width:95px; height:40px; float:left'>
	                <img  src='../images_icons/fleur1.jpg' />
	             </div>";
		 
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $html ) );
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
	
	

	
}
