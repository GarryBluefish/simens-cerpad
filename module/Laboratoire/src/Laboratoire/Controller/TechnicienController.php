<?php 
namespace Laboratoire\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Json\Json;
use Infirmerie\View\Helper\DateHelper;
use Laboratoire\View\Helper\DocumentPdf;
use Laboratoire\View\Helper\DocumentPaillasse;
use Laboratoire\View\Helper\HematologiePaillasse;
use Laboratoire\View\Helper\BiochimiePaillasse;
use Laboratoire\View\Helper\ParasitologiePaillasse;
use Laboratoire\View\Helper\BacteriologiePaillasse;
use Laboratoire\View\Helper\DepistagePaillasse;
use Laboratoire\View\Helper\ResultatsAnalysesDemandeesPdf;
use Infirmerie\Form\BilanForm;
use Zend\Form\View\Helper\FormRow;
use Zend\Form\View\Helper\FormSelect;
use Zend\Form\View\Helper\FormText;
use Zend\Form\View\Helper\FormTextarea;
use Zend\Form\View\Helper\FormHidden;
use Laboratoire\View\Helper\HematologiePaillasseParAnalyse;
use Laboratoire\View\Helper\BiochimiePaillasseParAnalyse;
use Laboratoire\View\Helper\ParasitologiePaillasseParAnalyse;
use Laboratoire\View\Helper\BacteriologiePaillasseParAnalyse;
use Laboratoire\View\Helper\DepistagePaillasseParAnalyse;
use Laboratoire\View\Helper\infosStatistiquePdf;


class TechnicienController extends AbstractActionController {

	protected $bilanPrelevementTable;
	protected $personneTable;
	protected $patientTable;
	protected $codagePrelevement;
	protected $triPrelevement;
	protected $analyseTable;
	protected $resultatDemandeAnalyseTable;
	protected $resultatsDepistagesTable;
	
	
	public function getBilanPrelevementTable() {
		if (! $this->bilanPrelevementTable) {
			$sm = $this->getServiceLocator ();
			$this->bilanPrelevementTable = $sm->get ( 'Infirmerie\Model\BilanPrelevementTable' );
		}
		return $this->bilanPrelevementTable;
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
	
	public function getAnalyseTable() {
		if (! $this->analyseTable) {
			$sm = $this->getServiceLocator ();
			$this->analyseTable = $sm->get ( 'Secretariat\Model\AnalyseTable' );
		}
		return $this->analyseTable;
	}
	
	public function getResultatDemandeAnalyseTable() {
		if (! $this->resultatDemandeAnalyseTable) {
			$sm = $this->getServiceLocator ();
			$this->resultatDemandeAnalyseTable = $sm->get ( 'Laboratoire\Model\ResultatDemandeAnalyseTable' );
		}
		return $this->resultatDemandeAnalyseTable;
	}
	
	public function getResultatsDepistagesTable() {
		if (! $this->resultatsDepistagesTable) {
			$sm = $this->getServiceLocator ();
			$this->resultatsDepistagesTable = $sm->get ( 'Laboratoire\Model\ResultatsDepistagesTable' );
		}
		return $this->resultatsDepistagesTable;
	}
	
/*****************************************************************************************************************************/
/*****************************************************************************************************************************/
/*****************************************************************************************************************************/

	public function baseUrl(){
		$baseUrl = $_SERVER['REQUEST_URI'];
		$tabURI  = explode('public', $baseUrl);
		return $tabURI[0];
	}
	
	public function listeBilanAjaxAction() {
		$output = $this->getBilanPrelevementTable() ->getListeBilansPrelevement();
		return $this->getResponse ()->setContent ( Json::encode ( $output, array (
				'enableJsonExprFinder' => true
		) ) );
	}
	
	public function moisEnLettre($mois){
		$lesMois = array('','Janvier','F&eacute;vrier','Mars','Avril',
				'Mai','Juin','Juillet','Aout','Septembre','Octobre','Novembre','D&eacute;cembre');
		return $lesMois[$mois];
	}
	
	public function moisAbregesEnLettre($mois){
	    $lesMois = array('','Jan','Fev','Mar','Avr','Mai','Jun','Jul','Aou','Sep','Oct','Nov','Dec');
	    return $lesMois[$mois];
	}
	
	public function listeBilansAction() {
		$this->layout ()->setTemplate ( 'layout/technicien' );
		
		
		//$listeBilan = $this->getBilanPrelevementTable()->pourCorrection();
		//var_dump($listeBilan); exit();
		
		
		//$listeResultatsDepistages = $this->getResultatsDepistagesTable()->getResultatsDepistagesPourUnePeriode('2018-01-01','2018-01-10');
		//var_dump($listeResultatsDepistages); exit();
		
		//$listeResultatsDepistages = $this->getResultatsDepistagesTable()->getResultatsDepistages();
		//var_dump($listeResultatsDepistages); exit();
		
		//$bilanPrelevement = $this->getBilanPrelevementTable() ->getBilanPrelevementRepris(12);
		
		
		//$listeResultatsDepistages = $this->getResultatsDepistagesTable()->getPatientsDepistagesSansResultat();
		
		//var_dump($listeResultatsDepistages); exit();
		
		return new ViewModel ( );
		
	}

	protected function nbJours($debut, $fin) {
		//60 secondes X 60 minutes X 24 heures dans une journee
		$nbSecondes = 60*60*24;
	
		$debut_ts = strtotime($debut);
		$fin_ts = strtotime($fin);
		$diff = $fin_ts - $debut_ts;
		return (int)($diff / $nbSecondes);
	}
	
	public function etatCivilPatientAction($idpatient) {
		 
		$personne = $this->getPersonneTable()->getPersonne($idpatient);
		$depistage = $this->getPatientTable()->getDepistagePatient($idpatient);
		$patient = $this->getPatientTable()->getPatient($idpatient);
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
					
					$designation = $typageHemoglobine['designation'];
					if($designation == 'SB+thal'){ $designation = "S&beta;<sup>+</sup> thalass&eacute;mie"; }else
					if($designation == 'SBÂ°thal'){ $designation = "S&beta;&deg; thalass&eacute;mie"; }
					
					$typage = "(<span style='color: red;'>".$designation."</span>)" ;
						
				}else{
					$designation = $typageHemoglobine['designation'];
					if($designation == 'AB+thal'){ $designation = "A&beta;<sup>+</sup> thalass&eacute;mie"; }else
					if($designation == 'ABÂ°thal'){ $designation = "A&beta;&deg; thalass&eacute;mie"; }
						
					$typage = "(".$designation.")" ;
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
                    <div style='margin-top: 20px; margin-right: 0px; font-size:16px; font-family: Iskoola Pota; color: green; float: right; font-style: italic; opacity: 1;'> ".$patient->numero_dossier." </div>
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
	
	public function codesPrelevements($codage, $Prelevements, $infoLibelle=null) {
	
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
		if($Prelevements == "F-p0"){
			$infoBulle = ($infoLibelle == "GPP") ? "la GPP":"l'HPVO";
			return $codage.'-<span title="Fluorure" style="cursor:pointer;">F</span> - <span title="Premier prÃ©l&egrave;vement pour '.$infoBulle.'" style="cursor:pointer;">p0</span></span><br>';
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
	
	
	public function vueListePrelevementTriAction() {
	    
	    $idpatient = ( int ) $this->params ()->fromPost ( 'idpatient', 0 );
	    
	    $idfacturation = ( int ) $this->params ()->fromPost ( 'idfacturation', 0 );
	    
	    $dateAujourdhui = ( new \DateTime () ) ->format( 'Y-m-d' );
	    
	    $html = $this->etatCivilPatientAction($idpatient);
	    
	    $listeAnalysesDemandees = $this->getBilanPrelevementTable()->getListeAnalysesDemandeesAyantUnBilan($idfacturation);
	    $analyseFluorure = array();
	    
	    for($i = 0 ; $i < count($listeAnalysesDemandees) ; $i++){
	    
	    	$idanalyse = $listeAnalysesDemandees[$i]['idanalyse'];
	    	if($idanalyse == 72 || $idanalyse == 73){ $analyseFluorure[] = $idanalyse; }
	    		
	    }
	    
	    
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
	    		
	    		
	    	$html .="<div id='titre_info_admis'>Bilan des pr&eacute;l&egrave;vements </div>";
	    	$html .="<div id='barre_separateur'></div>";
	    
	    	$html .="<table id='form_patient' style='margin-top:10px; margin-left:17.5%; width: 80%; margin-bottom: 10px;'>";
	    		
	    	$html .="<tr style='width: 80%; '>";
	    	$html .="<td style='width: 25%; vertical-align:top; margin-right:10px;'><span id='labelHeureLABEL' style='padding-left: 5px;'>Nombre de pr&eacute;l&egrave;vement</span><br><p id='zoneChampInfo1' style='background:#f8faf8; padding-left: 15px; padding-top: 5px; font-size:19px;  width: 70%;'> ".$bilanPrelevement->nb_tube." </p></td>";
	    	$html .="<td style='width: 25%; vertical-align:top; margin-right:30px;'><span id='labelHeureLABEL' style='padding-left: 5px;'>Date & heure </span><br><p id='zoneChampInfo1' style='background:#f8faf8; padding-left: 5px; padding-top: 5px; font-size:19px;  width: 80%;'> ".$bilanPrelevement->date_heure." </p></td>";
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
	    	
	    		$codage = "<i>p".$i." :</i> &nbsp;<span id='pr".$i."'> ".$codes->annee."-".$codes->numero;
	    		$LettrePrelevement = $codes->prelevement;
	    		if($LettrePrelevement == "F-p0"){ if(in_array(72, $analyseFluorure)){ $infoLibelle = "HPVO"; }else{ $infoLibelle = "GPP"; } }else{ $infoLibelle = null; }
	    		$html .= $this->codesPrelevements($codage, $LettrePrelevement, $infoLibelle);
	    	
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
	    	$html .="<td style='width: 35%; vertical-align:top; margin-right:10px;'><span id='labelHeureLABEL' style='padding-left: 5px;'>Difficult&eacute;s rencontr&eacute;es </span><br><p id='zoneChampInfo1' style='background:#f8faf8; padding-left: 5px; padding-top: 5px; font-size:17px; max-height: 120px;  width: 85%;'> ".$difficultesRencontrees." </p></td>";
	    	$html .="<td style='width: 15%; vertical-align:top; margin-right:10px;'><span id='labelHeureLABEL' style='padding-left: 5px;'>Transfuser </span><br><p id='zoneChampInfo1' style='background:#f8faf8; padding-left: 15px; padding-top: 5px; font-size:19px;  width: 60%;'> ".$transfuser." </p></td>";
	    	$html .="<td style='width: 35%; vertical-align:top; margin-right:10px;'>";
	    		
	    	if($moment_transfusion){
	    		$html .="<span id='labelHeureLABEL' style='padding-left: 5px;'>Dans les 3 derniers mois </span><br><p id='zoneChampInfo1' style='background:#f8faf8; padding-left: 15px; padding-top: 5px; font-size:19px;  width: 60%;'> ".$moment_transfusion." </p>";
	    	}
	    		
	    	$html .="</td>";
	    	$html .="</tr>";
	    
	    	$html .="</table>";
	    		
	    	$diagnostic = $bilanPrelevement->diagnostic;
	    	$traitement = $bilanPrelevement->traitement;
	    	if($diagnostic){
	    		$html .="<table id='form_patient' style='margin-top:10px; margin-left:17.5%; width: 80%; margin-bottom: 10px;'>";
	    		$html .="<td style='width: 35%; vertical-align:top; margin-right:10px;'><span id='labelHeureLABEL' style='padding-left: 5px;'>Diagnostic </span><br><p id='zoneChampInfo1' style='background:#f8faf8; padding-left: 5px; padding-top: 5px; font-size:17px; max-height: 90px;  width: 95%;'> ".$diagnostic." </p></td>";
	    		$html .="<td style='width: 35%; vertical-align:top; margin-right:10px;'><span id='labelHeureLABEL' style='padding-left: 5px;'>Traitement </span><br><p id='zoneChampInfo1' style='background:#f8faf8; padding-left: 5px; padding-top: 5px; font-size:17px; max-height: 90px;  width: 95%;'> ".$traitement." </p></td>";
	    		$html .="</table>";
	    	}
	    }
	    
	    
	    $html .="
	    <div style='width: 100%;' align='center' >   		
	    <div style='width: 94%; margin-bottom: 30px; margin-top: 30px;'>
	    <div id='accordions' align='left' >
	    <div style='font-family: police2; font-size: 18px; font-weight: bold; background: #efefef;'>Liste des analyses &agrave; trier

	       <div style='float: right;'>
		     <img id='iconeValidTriPrevAnalyse' onclick='triSelectValeurToutConforme();' style='margin-right: 25px;  width:16px; height:16px;' src='../../public/images_icons/valider_tout.png' title='Tous conformes'>	
		   </div>
	    		
	    </div>
	    <div style='min-height: 300px; border-top: 1px solid #cccccc;' id='listeAnalysesPreleveesTableau'>
	    <form  id='formEnregistrementTri'  method='post' action='../technicien/enregistrer-tri-prelevement'>";		

	    		
	    		
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
	    
	    	$html .="<input type='hidden' name='demande_".$i."'  value='".$listeAnalysesDemandees[$i]['iddemande']."'>";
	    	$html .="<input type='hidden' name='analyse_".$i."'  value='".$listeAnalysesDemandees[$i]['idanalyse']."'>";
	    	$html .="<td id='conformiteA' style='font-size: 10px;'> <div class='conformitePrelevement'  id='conformitePrelevement_".$listeAnalysesDemandees[$i]['idanalyse']."' > <select name='conformite_".$listeAnalysesDemandees[$i]['idanalyse']."' id='conformite' required=true onchange='getConformite(this.value,".$listeAnalysesDemandees[$i]['idanalyse'].")' class='conformitePrelevTousConforme'> <option>  </option> <option value='1' >Conforme</option> <option value='0' style='font-size: 10px; color: red;' >Non conforme</option>  </select> </div> </td>";
	    	$html .="<td id='noteConformiteA' style='font-size: 12px;'> <div  id='noteConformite_".$listeAnalysesDemandees[$i]['idanalyse']."' >  </div> </td>";
	    	
	    	
	    	$html .="</tr>";
	    
	    }
	    	
	    
	    $html .="</tbody>";
	    
	    $html .="</table>";
	    
	    
	    $html .= "</tr>";
	    $html .= "</table>";
	    		
	    $html .= "<span id='nbAnalysesATrier' style='float: left;'> </span>"; 
	    
	    		
	    $html .="
		   <input type='hidden' name='nbAnalyse' value='".$i."'>
		   <input type='hidden' name='idbilan' value='".$bilanPrelevement->idbilan."'>		
	       <button id='validerConformitePrelevementForm' style='display:none;' ></button>		
	       </form>		
	       </div>		
	    </div>
	    </div>
	    </div>

	    		
	    <script>		
	      $( '#accordions' ).accordion();
	      listeAnalysesPreleveesDataTable();
	      $('#listeAnalysesPreleveesTableau .listeDataTable').toggle(false);
	      $('#listeAnalysesPreleveesTableau div .dataTables_paginate').css({'margin-top':'-15px'});			

		  $('#nbAnalysesATrier').html('".$i." analyses');		
		  $('a,img,span,div').tooltip({ animation: true, html: true, placement: 'bottom', show: { effect: 'slideDown', delay: 250 } }); 		
	    </script>";

	    
	    $this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
	    return $this->getResponse ()->setContent ( Json::encode ( $html ) );
	}
	
	
	public function enregistrerTriPrelevementAction(){
		
		$donnees = $this->getRequest()->getPost()->toArray();
		$nbAnalyse = $donnees['nbAnalyse'];
		$idbilan = $donnees['idbilan'];
		
		$date_enregistrement = ( new \DateTime () ) ->format( 'Y-m-d H:i:s' );
		$idemploye = $this->layout()->user['idemploye'];
		
		for($i = 0 ; $i < $nbAnalyse ; $i++){

			$iddemande  = $donnees['demande_'.$i];
			$idanalyse  = $donnees['analyse_'.$i];
			$conformite = $donnees['conformite_'.$idanalyse];
			
			$infoTri = array(
					'iddemande' => $iddemande,
					'idanalyse' => $idanalyse,
					'idbilan' => $idbilan,
					'conformite' => $conformite,
					
					'date_enregistrement' => $date_enregistrement,
					'idemploye' => $idemploye,
			);
			
			if($conformite == 0){ $infoTri['note_non_conformite'] = $donnees['noteNonConformite_'.$idanalyse ]; }

			//insertion dans la base de données
			//insertion dans la base de données
			$this->getTriPrelevementTable() ->addTriPrelevement($infoTri);
		}
		
		return $this->redirect()->toRoute('technicien', array('action' =>'liste-bilans'));
		
	}
	
	
	public function listeBilansTriesAjaxAction(){
		$output = $this->getBilanPrelevementTable() ->getListeBilansPrelevementTries();
		return $this->getResponse ()->setContent ( Json::encode ( $output, array (
				'enableJsonExprFinder' => true
		) ) );
	}
	
	
	public function listeBilansTriesAction(){

		$this->layout ()->setTemplate ( 'layout/technicien' );
		
		/*
		$timestart = microtime(true);
		
		$output = $this->getBilanPrelevementTable() ->getListeBilansPrelevementTries();
		
		$timeend = microtime(true);
		$time = $timeend-$timestart;
		
		var_dump(number_format($time,3)); exit();
		*/
		
		return new ViewModel ( );
	}
	
	
	public function vueModifierListePrelevementTriAction() {
		 
		$idpatient = ( int ) $this->params ()->fromPost ( 'idpatient', 0 );
		 
		$idfacturation = ( int ) $this->params ()->fromPost ( 'idfacturation', 0 );
		 
		$dateAujourdhui = ( new \DateTime () ) ->format( 'Y-m-d' );
		 
		$html = $this->etatCivilPatientAction($idpatient);
		 
		$listeAnalysesDemandees = $this->getBilanPrelevementTable()->getListeAnalysesDemandeesAyantUnBilan($idfacturation);
		$analyseFluorure = array();
		 
		for($i = 0 ; $i < count($listeAnalysesDemandees) ; $i++){
			 
			$idanalyse = $listeAnalysesDemandees[$i]['idanalyse'];
			if($idanalyse == 72 || $idanalyse == 73){ $analyseFluorure[] = $idanalyse; }
			 
		}
		
		
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
			 
			 
			$html .="<div id='titre_info_admis'>Bilan des pr&eacute;l&egrave;vements </div>";
			$html .="<div id='barre_separateur'></div>";
		  
			$html .="<table id='form_patient' style='margin-top:10px; margin-left:17.5%; width: 80%; margin-bottom: 10px;'>";
			 
			$html .="<tr style='width: 80%; '>";
			$html .="<td style='width: 25%; vertical-align:top; margin-right:10px;'><span id='labelHeureLABEL' style='padding-left: 5px;'>Nombre de pr&eacute;l&egrave;vement</span><br><p id='zoneChampInfo1' style='background:#f8faf8; padding-left: 15px; padding-top: 5px; font-size:19px; width: 70%;'> ".$bilanPrelevement->nb_tube." </p></td>";
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
	
				$codage = "<i>p".$i." :</i> &nbsp;<span id='pr".$i."'> ".$codes->annee."-".$codes->numero;
				$LettrePrelevement = $codes->prelevement;
				if($LettrePrelevement == "F-p0"){ if(in_array(72, $analyseFluorure)){ $infoLibelle = "HPVO"; }else{ $infoLibelle = "GPP"; } }else{ $infoLibelle = null; }
				$html .= $this->codesPrelevements($codage, $LettrePrelevement, $infoLibelle);
				
	
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
			$html .="<td style='width: 35%; vertical-align:top; margin-right:10px;'><span id='labelHeureLABEL' style='padding-left: 5px;'>Difficult&eacute;s rencontr&eacute;es </span><br><p id='zoneChampInfo1' style='background:#f8faf8; padding-left: 5px; padding-top: 5px; font-size:17px; max-height: 120px; width: 85%;'> ".$difficultesRencontrees." </p></td>";
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
	    <div style='font-family: police2; font-size: 18px; font-weight: bold; background: #efefef;'>Liste des analyses &agrave; trier   

		   <div style='float: right;'>
			 <img id='iconeSuppTriPrevAnalyse'  onclick='supprimerTriDesPrelevementsDesAnalyses(".$bilanPrelevement->idbilan.");' style='margin-left: -10px; margin-right: 25px; width:16px; height:16px;' src='../../public/images_icons/suppTri.png' title='Annuler le tri'>
		   </div>
		   		
	    </div>
	    <div style='min-height: 300px; border-top: 1px solid #cccccc;' id='listeAnalysesPreleveesTableau'>
	       <form  id='formEnregistrementTri'  method='post' action='../technicien/modifier-tri-prelevement'>";
	
		 
		 
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
		  
			$html .="<input type='hidden' name='demande_".$i."'  value='".$listeAnalysesDemandees[$i]['iddemande']."'>";
			$html .="<input type='hidden' name='analyse_".$i."'  value='".$listeAnalysesDemandees[$i]['idanalyse']."'>";
			$html .="<td id='conformiteA' style='font-size: 10px;'> <div class='conformitePrelevement'  id='conformitePrelevement_".$listeAnalysesDemandees[$i]['idanalyse']."' > <select name='conformite_".$listeAnalysesDemandees[$i]['idanalyse']."' id='conformite_".$listeAnalysesDemandees[$i]['idanalyse']."' required=true onchange='getConformite(this.value,".$listeAnalysesDemandees[$i]['idanalyse'].")'> <option>  </option> <option value='1' >Conforme</option> <option value='0' style='font-size: 10px; color: red;' >Non conforme</option>  </select> </div> </td>";
			$html .="<td id='noteConformiteA' style='font-size: 12px;'> <div  id='noteConformite_".$listeAnalysesDemandees[$i]['idanalyse']."' class='noteConformite_'>  </div> </td>";
	
	
			$html .="</tr>";
		  
		}
	
		 
		$html .="</tbody>";
		 
		$html .="</table>";
		 
		 
		$html .= "<span id='nbAnalysesATrier' style='float: left;'> </span>";
		 
		 
		$html .="
		   <input type='hidden' name='nbAnalyse' value='".$i."'>
		   <input type='hidden' name='idbilan' value='".$bilanPrelevement->idbilan."'>
	       <button id='validerConformitePrelevementForm' style='display:none;' ></button>
	       </form>
	       </div>
	    </div>
	    </div>
	    </div>
	
	   
	    <script>
	      $( '#accordions' ).accordion();
	      listeAnalysesPreleveesDataTable();
	      $('#listeAnalysesPreleveesTableau .listeDataTable').toggle(false);
	      $('#listeAnalysesPreleveesTableau div .dataTables_paginate').css({'margin-top':'-15px'});
	
		  $('#nbAnalysesATrier').html('".$i." analyses');
	    </script>";
	
		
		//SELECTION DES CONFORMITES  ---  SELECTION DES CONFORMITES ---
		//SELECTION DES CONFORMITES  ---  SELECTION DES CONFORMITES ---
		$tri = $this->getTriPrelevementTable() ->getPrelevementTrie($bilanPrelevement->idbilan);
		$tabAnalysesAyantResultats = $this->getBilanPrelevementTable() ->getListeAnalysesTrieesDuBilanAyantResultats($bilanPrelevement->idbilan);
		$tabAnalysesBilanRepris = $this->getBilanPrelevementTable() ->getVerifierBilanRepris($bilanPrelevement->idbilan);
		
		for($i = 0 ; $i < count($tri) ; $i++ ){
			
			$conformite = (int)$tri[$i] ->conformite;
			$idanalyse  = (int)$tri[$i] ->idanalyse;
			
			//Selection de la conformité
			//Selection de la conformité
			if(in_array($idanalyse, $tabAnalysesAyantResultats) || in_array($idanalyse, $tabAnalysesBilanRepris)){
				$html .="
				<script> 
					$('#conformite_".$idanalyse."').val('".$conformite."').attr('disabled', true);
					$('#iconeSuppTriPrevAnalyse').toggle(false);
				</script>
				";
				
			}else {
				$html .="
				<script> 
					$('#conformite_".$idanalyse."').val('".$conformite."');
				</script>
				";
			
			}
			
			
			if($conformite == 0){ 
				
				$note_non_conformite = $tri[$i] ->note_non_conformite;
				
				if(in_array($idanalyse, $tabAnalysesAyantResultats)  || in_array($idanalyse, $tabAnalysesBilanRepris)){
					
 					$html .="
 				    <script>
 			           $('#conformitePrelevement_".$idanalyse." select').css({'color':'red'});
     		           $('#noteConformite_".$idanalyse."').html('<input name=\'noteNonConformite_".$idanalyse."\' title=\'Impossible de modifier\' id=\'noteNonConformite_".$idanalyse."\' required=true  style=\'width: 100%; height: 100%; padding-left: 5px; font-size: 13px; font-family: time new romans;\'>');
 				       $('#noteNonConformite_".$idanalyse."').val('".str_replace("'", "\'",$note_non_conformite)."').attr('disabled',true);
 				    </script>
 				    ";
					
				}else {
					
					$html .="
				    <script>
			           $('#conformitePrelevement_".$idanalyse." select').css({'color':'red'});
    		           $('#noteConformite_".$idanalyse."').html('<input name=\'noteNonConformite_".$idanalyse."\' id=\'noteNonConformite_".$idanalyse."\' required=true  style=\'width: 100%; height: 100%; padding-left: 5px; font-size: 13px; font-family: time new romans;\'>');
				       $('#noteNonConformite_".$idanalyse."').val('".str_replace("'", "\'",$note_non_conformite)."');
    		        </script>
				    ";
					
				}

			}else{
				$html .="
				    <script>
			           $('#noteConformite_".$idanalyse."').html('<img style=\'margin-left: 15px;\' src=\'../images_icons/tick_16.png\'  >');
				    </script>
				    ";
			}
			
		}
		
		
		$html .="
				<script>
				  $('a,img,span,div').tooltip({ animation: true, html: true, placement: 'bottom', show: { effect: 'slideDown', delay: 250 } });
				</script>
				";
		
		 
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $html ) );
	}
	
	public function supprimerUnTriAction(){

		$idbilan = ( int ) $this->params ()->fromPost ( 'idbilan', 0 );
		
		$tabAnalysesAyantResultats = $this->getBilanPrelevementTable() ->getListeAnalysesTrieesDuBilanAyantResultats($idbilan);
		
		$result = 0;
		if(!$tabAnalysesAyantResultats){
			$this->getTriPrelevementTable()->deleteTriPrelevement($idbilan);
			$result = 1;
		}
		
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $result ) );
		
	}
	
	public function modifierTriPrelevementAction(){
	
		$donnees = $this->getRequest()->getPost()->toArray();
		$nbAnalyse = $donnees['nbAnalyse'];
		$idbilan = $donnees['idbilan'];
	
		$date_enregistrement = ( new \DateTime () ) ->format( 'Y-m-d H:i:s' );
		$idemploye = $this->layout()->user['idemploye'];
	
		for($i = 0 ; $i < $nbAnalyse ; $i++){
				
			$iddemande  = $donnees['demande_'.$i];
			$idanalyse  = $donnees['analyse_'.$i];
			
			if(array_key_exists('conformite_'.$idanalyse, $donnees)){
				
				$conformite = $donnees['conformite_'.$idanalyse];

				$infoTri = array(
					'conformite' => $conformite,
					'idemploye' => $idemploye,
				);
				
				if($conformite == 0){ $infoTri['note_non_conformite'] = $donnees['noteNonConformite_'.$idanalyse ]; }
				else{ $infoTri['note_non_conformite'] = ""; }
					
				//modification dans la base de données
				//modification dans la base de données
				$this->getTriPrelevementTable() ->updateBilanPrelevementTrie($infoTri, $iddemande, $idbilan);
			}
			
		}
		
		return $this->redirect()->toRoute('technicien', array('action' =>'liste-bilans-tries'));
	
	}
	

	//IMPRIMER EN REGROUPANT PAR ANALYSE 
	//IMPRIMER EN REGROUPANT PAR ANALYSE 
	//IMPRIMER EN REGROUPANT PAR ANALYSE 
	public function impressionFeuillePaillasseAction(){

		//Liste des patients ayant des analyses triées pour lesquelles il n y a pas encore de résultats
		$listeAnalysesTriees = $this->getTriPrelevementTable() ->getListeAnalysesTrieesSansResultatTAP();
		
		$hematologie = array();
		$biochimie = array();
		$parasitologie = array();
		$bacteriologie = array();
		$depistage = array();
		
		for($i = 0 ; $i < count($listeAnalysesTriees) ; $i++){
			if($listeAnalysesTriees[$i]['Idtype'] == 1){
				$hematologie[] = $listeAnalysesTriees[$i];
			}else
			if($listeAnalysesTriees[$i]['Idtype'] == 2){
				$biochimie[] = $listeAnalysesTriees[$i];
			}else
			if($listeAnalysesTriees[$i]['Idtype'] == 3){
				$parasitologie[] = $listeAnalysesTriees[$i];
			}else
			if($listeAnalysesTriees[$i]['Idtype'] == 4){
				$bacteriologie[] = $listeAnalysesTriees[$i];
			}else
			if($listeAnalysesTriees[$i]['Idtype'] == 5){
				$depistage[] = $listeAnalysesTriees[$i];
			}
		}
		
		
		//LISTE DES PATIENTS DEPISTES POSITIFS
		$listePatientsDepistes = $this->getPatientTable()->getDepistagePatientTableau();
		//CREATION DU DOCUMENT
		$DocPdf = new DocumentPaillasse();
		$entreeVerif = 0;
		
		//=============================================
		//*********************************************
		//=============================================
		
		//LISTE DES ANALYSES HEMATOLOGIQUES PAR ANALYSE 
		//LISTE DES ANALYSES HEMATOLOGIQUES PAR ANALYSE 
		
		//*********************************************
		//=============================================
		
		$listeId = array();
		$listeHematologie = array();
		$k=0;
		$l=0;
		
		for($i = 0 ; $i < count($hematologie) ; $i++){
		
			if(!in_array($hematologie[$i]['Idanalyse'], $listeId)){
				$Idanalyse = $hematologie[$i]['Idanalyse'];
				$listeId [] = $Idanalyse;
				

				for($j = 0 ; $j < count($hematologie) ; $j++){
					if($Idanalyse == $hematologie[$j]['Idanalyse']){
				
						if($l++ == 0){
							$listeHematologie[$k][] = $Idanalyse;
							$listeHematologie[$k][] = $hematologie[$j]['LibelleAnalyse'];
						}
				
						$listeHematologie[$k][2][] = $hematologie[$j]['Idpatient'];
						$listeHematologie[$k][3][] = $hematologie[$j]['Nom'];
						$listeHematologie[$k][4][] = $hematologie[$j]['Prenom'];
						$listeHematologie[$k][5][] = $hematologie[$j]['Conformite'];
						$listeHematologie[$k][6][] = $hematologie[$j]['Ordre'];
							
					}
				}
				
				$l=0;
				$k++;
				
			}
			
		}
		
		if($hematologie){
			$page = new HematologiePaillasseParAnalyse();
			$page->setDepistage($listePatientsDepistes);
			$page->setListeHematologie($listeHematologie);
			$page->addNote();
			$DocPdf->addPage($page->getPage());
			$entreeVerif = 1;
		}
		//===========================================
		//*******************************************
		//===========================================
		
		//LISTE DES ANALYSES BIOCHIMIQUES PAR ANALYSE 
		//LISTE DES ANALYSES BIOCHIMIQUES PAR ANALYSE 
		
		//*******************************************
		//===========================================
		$listeId = array();
		$listeBiochimie = array();
		$k=0;
		$l=0;
		for($i = 0 ; $i < count($biochimie) ; $i++){
		
			if(!in_array($biochimie[$i]['Idanalyse'], $listeId)){
				$Idanalyse = $biochimie[$i]['Idanalyse'];
				$listeId [] = $Idanalyse;
		
		
				for($j = 0 ; $j < count($biochimie) ; $j++){
					if($Idanalyse == $biochimie[$j]['Idanalyse']){
		
						if($l++ == 0){
							$listeBiochimie[$k][] = $Idanalyse;
							$listeBiochimie[$k][] = $biochimie[$j]['LibelleAnalyse'];
						}
		
						$listeBiochimie[$k][2][] = $biochimie[$j]['Idpatient'];
						$listeBiochimie[$k][3][] = $biochimie[$j]['Nom'];
						$listeBiochimie[$k][4][] = $biochimie[$j]['Prenom'];
						$listeBiochimie[$k][5][] = $biochimie[$j]['Conformite'];
						$listeBiochimie[$k][6][] = $biochimie[$j]['Ordre'];
							
					}
				}
		
				$l=0;
				$k++;
		
			}
				
		}
		
		if($biochimie){
			$page2 = new BiochimiePaillasseParAnalyse();
			$page2->setDepistage($listePatientsDepistes);
			$page2->setListeHematologie($listeBiochimie);
			$page2->addNote();
			$DocPdf->addPage($page2->getPage());
			$entreeVerif = 1;
		}

		
		//===========================================
		//*******************************************
		//===========================================
		
		//LISTE DES ANALYSES PARASITOLOGIE PAR ANALYSE
		//LISTE DES ANALYSES PARASITOLOGIE PAR ANALYSE
		
		//*******************************************
		//===========================================
		$listeId = array();
		$listeParasitologie = array();
		$k=0;
		$l=0;
		for($i = 0 ; $i < count($parasitologie) ; $i++){
		
			if(!in_array($parasitologie[$i]['Idanalyse'], $listeId)){
				$Idanalyse = $parasitologie[$i]['Idanalyse'];
				$listeId [] = $Idanalyse;
		
		
				for($j = 0 ; $j < count($parasitologie) ; $j++){
					if($Idanalyse == $parasitologie[$j]['Idanalyse']){
		
						if($l++ == 0){
							$listeParasitologie[$k][] = $Idanalyse;
							$listeParasitologie[$k][] = $parasitologie[$j]['LibelleAnalyse'];
						}
		
						$listeParasitologie[$k][2][] = $parasitologie[$j]['Idpatient'];
						$listeParasitologie[$k][3][] = $parasitologie[$j]['Nom'];
						$listeParasitologie[$k][4][] = $parasitologie[$j]['Prenom'];
						$listeParasitologie[$k][5][] = $parasitologie[$j]['Conformite'];
						$listeParasitologie[$k][6][] = $parasitologie[$j]['Ordre'];
							
					}
				}
		
				$l=0;
				$k++;
		
			}
		
		}
		
		if($parasitologie){
			$page3 = new ParasitologiePaillasseParAnalyse();
			$page3->setDepistage($listePatientsDepistes);
			$page3->setListeHematologie($listeParasitologie);
			$page3->addNote();
			$DocPdf->addPage($page3->getPage());
			$entreeVerif = 1;
		}
		
		
		//===========================================
		//*******************************************
		//===========================================
		
		//LISTE DES ANALYSES BACTERIOLOGIE PAR ANALYSE
		//LISTE DES ANALYSES BACTERIOLOGIE PAR ANALYSE
		
		//*******************************************
		//===========================================
		$listeId = array();
		$listeBacteriologie = array();
		$k=0;
		$l=0;
		for($i = 0 ; $i < count($bacteriologie) ; $i++){
		
			if(!in_array($bacteriologie[$i]['Idanalyse'], $listeId)){
				$Idanalyse = $bacteriologie[$i]['Idanalyse'];
				$listeId [] = $Idanalyse;
		
		
				for($j = 0 ; $j < count($bacteriologie) ; $j++){
					if($Idanalyse == $bacteriologie[$j]['Idanalyse']){
		
						if($l++ == 0){
							$listeBacteriologie[$k][] = $Idanalyse;
							$listeBacteriologie[$k][] = $bacteriologie[$j]['LibelleAnalyse'];
						}
		
						$listeBacteriologie[$k][2][] = $bacteriologie[$j]['Idpatient'];
						$listeBacteriologie[$k][3][] = $bacteriologie[$j]['Nom'];
						$listeBacteriologie[$k][4][] = $bacteriologie[$j]['Prenom'];
						$listeBacteriologie[$k][5][] = $bacteriologie[$j]['Conformite'];
						$listeBacteriologie[$k][6][] = $bacteriologie[$j]['Ordre'];
							
					}
				}
		
				$l=0;
				$k++;
		
			}
		
		}
		
		if($bacteriologie){
			$page4 = new BacteriologiePaillasseParAnalyse();
			$page4->setDepistage($listePatientsDepistes);
			$page4->setListeBacteriologie($listeBacteriologie);
			$page4->addNote();
			$DocPdf->addPage($page4->getPage());
			$entreeVerif = 1;
		}
		
		
		//===========================================
		//*******************************************
		//===========================================
		
		//LISTE DES ANALYSES DEPISTAGE PAR ANALYSE
		//LISTE DES ANALYSES DEPISTAGE PAR ANALYSE
		
		//*******************************************
		//===========================================
		$listeId = array();
		$listeDepistage = array();
		$k=0;
		$l=0;
		for($i = 0 ; $i < count($depistage) ; $i++){
		
			if(!in_array($depistage[$i]['Idanalyse'], $listeId)){
				$Idanalyse = $depistage[$i]['Idanalyse'];
				$listeId [] = $Idanalyse;
		
		
				for($j = 0 ; $j < count($depistage) ; $j++){
					if($Idanalyse == $depistage[$j]['Idanalyse']){
		
						if($l++ == 0){
							$listeDepistage[$k][] = $Idanalyse;
							$listeDepistage[$k][] = $depistage[$j]['LibelleAnalyse'];
						}
		
						$listeDepistage[$k][2][] = $depistage[$j]['Idpatient'];
						$listeDepistage[$k][3][] = $depistage[$j]['Nom'];
						$listeDepistage[$k][4][] = $depistage[$j]['Prenom'];
						$listeDepistage[$k][5][] = $depistage[$j]['Conformite'];
						$listeDepistage[$k][6][] = $depistage[$j]['Ordre'];
							
					}
				}
		
				$l=0;
				$k++;
		
			}
		
		}
		
		if($depistage){
			$page5 = new DepistagePaillasseParAnalyse();
			$page5->setDepistage($listePatientsDepistes);
			$page5->setListeDepistage($listeDepistage);
			$page5->addNote();
			$DocPdf->addPage($page5->getPage());
			$entreeVerif = 1;
		}
		
		if($entreeVerif == 1){ 
			$DocPdf->getDocument();
		}else{ echo "<div align='center' style='color: red;' >AUCUNE INFORMATION A AFFICHER</span>"; exit(); }
		
	}
	
	//IMPRIMER EN REGROUPANT PAR PATIENT
	//IMPRIMER EN REGROUPANT PAR PATIENT
	//IMPRIMER EN REGROUPANT PAR PATIENT
	public function impressionFeuillePaillasseParPatientAction() {

		//Liste des patients ayant des analyses triées pour lesquelles il n y a pas encore de résultats
		$listeAnalysesTriees = $this->getTriPrelevementTable() ->getListeAnalysesTrieesSansResultat();
		
		$hematologie = array();
		$biochimie = array();
		$parasitologie = array();
		$bacteriologie = array();
		$depistage = array();
		
		for($i = 0 ; $i < count($listeAnalysesTriees) ; $i++){
			if($listeAnalysesTriees[$i]['Idtype'] == 1){
				$hematologie[] = $listeAnalysesTriees[$i];
			}else
			if($listeAnalysesTriees[$i]['Idtype'] == 2){
				$biochimie[] = $listeAnalysesTriees[$i];
			}else
			if($listeAnalysesTriees[$i]['Idtype'] == 3){
				$parasitologie[] = $listeAnalysesTriees[$i];
			}else
			if($listeAnalysesTriees[$i]['Idtype'] == 4){
				$bacteriologie[] = $listeAnalysesTriees[$i];
			}else
			if($listeAnalysesTriees[$i]['Idtype'] == 5){
				$depistage[] = $listeAnalysesTriees[$i];
			}
		}
		
		//LISTE DES PATIENTS DEPISTES POSITIFS
		$listePatientsDepistes = $this->getPatientTable()->getDepistagePatientTableau();
		
		//LISTE DES ANALYSES HEMATOLOGIQUES PAR PATIENT
		//LISTE DES ANALYSES HEMATOLOGIQUES PAR PATIENT
		$listeId = array();
		$listeHematologie = array();
		$k=0;
		$l=0;
		for($i = 0 ; $i < count($hematologie) ; $i++){
		
			if(!in_array($hematologie[$i]['Idpatient'], $listeId)){
				$Idpatient = $hematologie[$i]['Idpatient'];
				$listeId [] = $Idpatient;
		
		
				for($j = 0 ; $j < count($hematologie) ; $j++){
					if($Idpatient == $hematologie[$j]['Idpatient']){
		
						if($l++ == 0){
							$listeHematologie[$k][] = $Idpatient;
							$listeHematologie[$k][] = $hematologie[$j]['Nom'];
							$listeHematologie[$k][] = $hematologie[$j]['Prenom'];
						}
		
						$listeHematologie[$k][3][] = $hematologie[$j]['LibelleAnalyse'];
						$listeHematologie[$k][4][] = $hematologie[$j]['Conformite'];
							
					}
				}
		
				$l=0;
				$k++;
			}
		
		}
		
		$DocPdf = new DocumentPaillasse();
		
		$page = new HematologiePaillasse();
		$page->setDepistage($listePatientsDepistes);
		$page->setListeHematologie($listeHematologie);
		$page->addNote();
		$DocPdf->addPage($page->getPage());
		
		
		
		//LISTE DES ANALYSES BIOCHIMIQUES PAR PATIENT
		//LISTE DES ANALYSES BIOCHIMIQUES PAR PATIENT
		
		$listeId = array();
		$listeBiochimie = array();
		$k=0;
		$l=0;
		for($i = 0 ; $i < count($biochimie) ; $i++){
		
			if(!in_array($biochimie[$i]['Idpatient'], $listeId)){
				$Idpatient = $biochimie[$i]['Idpatient'];
				$listeId [] = $Idpatient;
		
				for($j = 0 ; $j < count($biochimie) ; $j++){
					if($Idpatient == $biochimie[$j]['Idpatient']){
						if($l++ == 0){
							$listeBiochimie[$k][] = $Idpatient;
							$listeBiochimie[$k][] = $biochimie[$j]['Nom'];
							$listeBiochimie[$k][] = $biochimie[$j]['Prenom'];
						}
		
						$listeBiochimie[$k][3][] = $biochimie[$j]['LibelleAnalyse'];
						$listeBiochimie[$k][4][] = $biochimie[$j]['Conformite'];
							
					}
				}
		
				$l=0;
				$k++;
			}
		
		}
		
		
		$page2 = new BiochimiePaillasse();
		$page2->setDepistage($listePatientsDepistes);
		$page2->setListeBiochimie($listeBiochimie);
		$page2->addNote();
		$DocPdf->addPage($page2->getPage());
		
		
		
		//LISTE DES ANALYSES PARASITOLOGIQUES PAR PATIENT
		//LISTE DES ANALYSES PARASITOLOGIQUES PAR PATIENT
		
		$listeId = array();
		$listeParasitologie = array();
		$k=0;
		$l=0;
		for($i = 0 ; $i < count($parasitologie) ; $i++){
		
			if(!in_array($parasitologie[$i]['Idpatient'], $listeId)){
				$Idpatient = $parasitologie[$i]['Idpatient'];
				$listeId [] = $Idpatient;
		
				for($j = 0 ; $j < count($parasitologie) ; $j++){
					if($Idpatient == $parasitologie[$j]['Idpatient']){
						if($l++ == 0){
							$listeParasitologie[$k][] = $Idpatient;
							$listeParasitologie[$k][] = $parasitologie[$j]['Nom'];
							$listeParasitologie[$k][] = $parasitologie[$j]['Prenom'];
						}
		
						$listeParasitologie[$k][3][] = $parasitologie[$j]['LibelleAnalyse'];
						$listeParasitologie[$k][4][] = $parasitologie[$j]['Conformite'];
							
					}
				}
		
				$l=0;
				$k++;
			}
		
		}
		
		
		$page3 = new ParasitologiePaillasse();
		$page3->setDepistage($listePatientsDepistes);
		$page3->setListeParasitologie($listeParasitologie);
		$page3->addNote();
		$DocPdf->addPage($page3->getPage());
		
		
		
		//LISTE DES ANALYSES BACTERIOLOGIQUES PAR PATIENT
		//LISTE DES ANALYSES BACTERIOLOGIQUES PAR PATIENT
		
		
		$listeId = array();
		$listeBacteriologie = array();
		$k=0;
		$l=0;
		for($i = 0 ; $i < count($bacteriologie) ; $i++){
		
			if(!in_array($bacteriologie[$i]['Idpatient'], $listeId)){
				$Idpatient = $bacteriologie[$i]['Idpatient'];
				$listeId [] = $Idpatient;
		
				for($j = 0 ; $j < count($bacteriologie) ; $j++){
					if($Idpatient == $bacteriologie[$j]['Idpatient']){
						if($l++ == 0){
							$listeBacteriologie[$k][] = $Idpatient;
							$listeBacteriologie[$k][] = $bacteriologie[$j]['Nom'];
							$listeBacteriologie[$k][] = $bacteriologie[$j]['Prenom'];
						}
		
						$listeBacteriologie[$k][3][] = $bacteriologie[$j]['LibelleAnalyse'];
						$listeBacteriologie[$k][4][] = $bacteriologie[$j]['Conformite'];
							
					}
				}
		
				$l=0;
				$k++;
			}
		
		}
		
		
		$page4 = new BacteriologiePaillasse();
		$page4->setDepistage($listePatientsDepistes);
		$page4->setListeBacteriologie($listeBacteriologie);
		$page4->addNote();
		$DocPdf->addPage($page4->getPage());
		
		
		//LISTE DES ANALYSES DEPISTEES PAR PATIENT
		//LISTE DES ANALYSES DEPISTEES PAR PATIENT
		$listeId = array();
		$listeDepistage = array();
		$k=0;
		$l=0;
		for($i = 0 ; $i < count($depistage) ; $i++){
		
			if(!in_array($depistage[$i]['Idpatient'], $listeId)){
				$Idpatient = $depistage[$i]['Idpatient'];
				$listeId [] = $Idpatient;
		
				for($j = 0 ; $j < count($depistage) ; $j++){
					if($Idpatient == $depistage[$j]['Idpatient']){
						if($l++ == 0){
							$listeDepistage[$k][] = $Idpatient;
							$listeDepistage[$k][] = $depistage[$j]['Nom'];
							$listeDepistage[$k][] = $depistage[$j]['Prenom'];
						}
		
						$listeDepistage[$k][3][] = $depistage[$j]['LibelleAnalyse'];
						$listeDepistage[$k][4][] = $depistage[$j]['Conformite'];
							
					}
				}
		
				$l=0;
				$k++;
			}
		
		}
		
		
		$page5 = new DepistagePaillasse();
		$page5->setDepistage($listePatientsDepistes);
		$page5->setListeDepistage($listeDepistage);
		$page5->addNote();
		$DocPdf->addPage($page5->getPage());
		
		$DocPdf->getDocument();
		
	}
	
	public function listeBilansTriesResultatsAjaxAction() {
	
		$output = $this->getBilanPrelevementTable() ->getListeBilansPrelevementTriesPourResultats();
		return $this->getResponse ()->setContent ( Json::encode ( $output, array (
				'enableJsonExprFinder' => true
		) ) );
	
	}
	
	//Verifier si un tableau est vide ou pas
	function array_empty($array) {
	    $is_empty = true;
	    foreach($array as $k) {
	        $is_empty = $is_empty && empty($k);
	    }
	    return $is_empty;
	}
	
	public function listePatientsAction (){
		$this->layout ()->setTemplate ( 'layout/technicien' );
		
		return new ViewModel ( );
	}
	

	/*
	 * Liste des souches --- Liste des souches --- Liste des souches 
	 * Liste des souches --- Liste des souches --- Liste des souches 
	 */
	public function getListeDesSouchesAction()
	{
		$listeSouchesIdentif = $this->getResultatDemandeAnalyseTable()->getListeIdentificationSouchesIdDESC();
		$html  = "<table>";
		for($i = 0 ; $i <  count($listeSouchesIdentif); $i++){
			$html .="<tr><td class='LTPE1' >".($i+1)."</td><td class='LTPE2  LTPE2_".$listeSouchesIdentif[$i][0]."' ><span class='libelleID_".$listeSouchesIdentif[$i][0]."'>".str_replace("'", "'", $listeSouchesIdentif[$i][1])."</span><img onclick='modifierInfosSoucheECBU(".$listeSouchesIdentif[$i][0].",".($i+1).");' class='imgLTPE2' src='../img/light/pencil.png'> </td></tr>";
		}
		$html .= "</table>";
		
		$listeSouchesIdentif = $this->getResultatDemandeAnalyseTable()->getListeIdentificationSouchesLibelleASC();
		
		$html  .= "<script> var optionsListesIdentifSouchesECBU = '<option value=0></option>'; </script>";
		for($i = 0; $i<count($listeSouchesIdentif) ; $i++){
			$html .= "<script> optionsListesIdentifSouchesECBU += '<option value=".$listeSouchesIdentif[$i][0]."  class=".'SouchesCultIdentifID_'.$listeSouchesIdentif[$i][0]." >".str_replace("'", "\'", $listeSouchesIdentif[$i][1])."</option>'; </script>";
		}
		$html .= "<script> $('#identification_culture_select_ecbu').html(optionsListesIdentifSouchesECBU); </script>";
		 
	
		$this->getResponse()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html' );
		return $this->getResponse ()->setContent(Json::encode ( $html ));
	}
	
	public function insertNouvelleSoucheAction()
	{
		$idemploye = $this->layout()->user['idemploye'];
		$nouvelleSouche = $this->params ()->fromPost ( 'nouvelleSouche' );
		
		$this->getResultatDemandeAnalyseTable()->insertNouvelleSouche($nouvelleSouche, $idemploye);
		
		$this->getResponse()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html' );
		return $this->getResponse ()->setContent(Json::encode ( 1 ));
	}
	
	public function modifierSoucheSelectionneeAction()
	{
		$idemploye = $this->layout()->user['idemploye'];
		$idSouche = $this->params ()->fromPost ( 'idSouche' );
		$nouveauNomSouche = $this->params ()->fromPost ( 'nouveauNomSouche' );
		
		$this->getResultatDemandeAnalyseTable()->modifierSoucheSelectionnee($idSouche, $nouveauNomSouche, $idemploye);
		
		$this->getResponse()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html' );
		return $this->getResponse ()->setContent(Json::encode ( 1 ));
	}
	
	
	/*
	 * Liste des parasites --- Liste des parasites --- Liste des parasites
	 * Liste des parasites --- Liste des parasites --- Liste des parasites
	 */
	public function getListeDesParasitesAction()
	{
		$listeParasites = $this->getResultatDemandeAnalyseTable()->getListeParasitesIdDESC();
		$html  = "<table>";
		for($i = 0 ; $i <  count($listeParasites); $i++){
			$html .="<tr><td class='LTPE1' >".($i+1)."</td><td class='LTPE2  LTPE2_".$listeParasites[$i][0]."' ><span class='libelleID_".$listeParasites[$i][0]."'>".str_replace("'", "'", $listeParasites[$i][1])."</span><img onclick='modifierInfosParasiteECBU(".$listeParasites[$i][0].",".($i+1).");' class='imgLTPE2' src='../img/light/pencil.png'> </td></tr>";
		}
		$html .= "</table>";
	
		$listeParasites = $this->getResultatDemandeAnalyseTable()->getListeParasitesLibelleASC();
	
		$html  .= "<script> var optionsListeParasitesECBU = '<option value=0></option>'; </script>";
		for($i = 0; $i<count($listeParasites) ; $i++){
			$html .= "<script> optionsListeParasitesECBU += '<option value=".$listeParasites[$i][0].">".str_replace("'", "\'", $listeParasites[$i][1])."</option>'; </script>";
		}

		$html .= "<script> $('#parasites_ecbu').html(optionsListeParasitesECBU); </script>";
		
	
		$this->getResponse()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html' );
		return $this->getResponse ()->setContent(Json::encode ( $html ));
	}
	
	public function insertNouveauParasiteAction()
	{
		$idemploye = $this->layout()->user['idemploye'];
		$nouveauParasite = $this->params ()->fromPost ( 'nouveauParasite' );
	
		$this->getResultatDemandeAnalyseTable()->insertNouveauParasite($nouveauParasite, $idemploye);
	
		$this->getResponse()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html' );
		return $this->getResponse ()->setContent(Json::encode ( 1 ));
	}
	
	public function modifierParasiteSelectionneAction()
	{
		$idemploye = $this->layout()->user['idemploye'];
		$idParasite = $this->params ()->fromPost ( 'idParasite' );
		$nouveauNomParasite = $this->params ()->fromPost ( 'nouveauNomParasite' );
	
		$this->getResultatDemandeAnalyseTable()->modifierParasiteSelectionne($idParasite, $nouveauNomParasite, $idemploye);
	
		$this->getResponse()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html' );
		return $this->getResponse ()->setContent(Json::encode ( 1 ));
	}
	
	
	
	
	
	
	
	
	
	
	public function vueListePrelevementTriReprisAction(){

		$idpatient = ( int ) $this->params ()->fromPost ( 'idpatient', 0 );
		 
		$idfacturation = ( int ) $this->params ()->fromPost ( 'idfacturation', 0 );
		 
		$dateAujourdhui = ( new \DateTime () ) ->format( 'Y-m-d' );
		 
		$html = $this->etatCivilPatientAction($idpatient);
		 
		 
		$bilanPrelevement = $this->getBilanPrelevementTable() ->getBilanPrelevement($idfacturation);
		
		$bilanPrelevementRepris = $this->getBilanPrelevementTable() ->getBilanPrelevementRepris( $bilanPrelevement->idbilan );
		
		 
		if($bilanPrelevementRepris){
			 
			if($bilanPrelevementRepris['a_jeun'] == 1){ $a_jeun = "Oui"; }else{ $a_jeun = "Non"; }
			if($bilanPrelevementRepris['difficultes'] == 1){
				$difficultes = "Oui";
				$difficultesRencontrees = $bilanPrelevementRepris['difficultes_prelevement'];
			}else{
				$difficultes = "Non";
				$difficultesRencontrees = "N&eacute;ant";
			}
			if($bilanPrelevementRepris['transfuser'] == 1){
				$transfuser = "Oui";
				if($bilanPrelevementRepris['moment_transfusion'] == 1){
					$moment_transfusion = "Oui";
					 
				}else{
					$moment_transfusion = "Non";
				}
			}else{
				$transfuser = "Non";
				$moment_transfusion = "";
			}
			 
			 
			$html .="<div id='titre_info_admis'>Bilan des pr&eacute;l&egrave;vements repris <span class='reprendrePrelevement' style='margin-left: 15px; font-size: 17px; color: red; cursor: pointer;' onclick='popupPrelevementPrecedent(".$idfacturation.",".$bilanPrelevement->idbilan.");' > (Pr&eacute;l&egrave;vements pr&eacute;c&eacute;dents) </span></div>";
			$html .="<div id='barre_separateur'></div>";
			 
			$html .="<table id='form_patient' style='margin-top:10px; margin-left:17.5%; width: 80%; margin-bottom: 10px;'>";
			 
			$html .="<tr style='width: 80%; '>";
			$html .="<td style='width: 25%; vertical-align:top; margin-right:10px;'><span id='labelHeureLABEL' style='padding-left: 5px;'>Nombre de pr&eacute;l&egrave;vement</span><br><p id='zoneChampInfo1' style='background:#f8faf8; padding-left: 15px; padding-top: 5px; font-size:19px;'> ".$bilanPrelevementRepris['nb_tube']." </p></td>";
			$html .="<td style='width: 25%; vertical-align:top; margin-right:30px;'><span id='labelHeureLABEL' style='padding-left: 5px;'>Date & heure </span><br><p id='zoneChampInfo1' style='background:#f8faf8; padding-left: 5px; padding-top: 5px; font-size:19px;'> ".$bilanPrelevementRepris['date_heure']." </p></td>";
			$html .="<td style='width: 15%; vertical-align:top; margin-right:10px;'><span id='labelHeureLABEL' style='padding-left: 5px;'>A jeun </span><br><p id='zoneChampInfo1' style='background:#f8faf8; padding-left: 15px; padding-top: 5px; font-size:19px; width: 70px;'> ".$a_jeun." </p></td>";
			$html .="<td style='width: 35%; vertical-align:top; margin-right:10px;'>";
			 
		
			//Afichage des codes des prélèvements
			//Afichage des codes des prélèvements
			$html .="<span id='labelCodagePrelevement' style='padding-left: 5px; '>Codage des pr&eacute;l&egrave;vements  </span><br>";
			$html .="<div id='codageTubePrelevement'>
					  <p> ";
		
			$listeCodes = $this->getCodagePrelevementTable() ->getCodagePrelevementRepris( $bilanPrelevementRepris['idbilanrepris'] );
			 
			$i = 1;
			foreach ($listeCodes as $codes){
		
				$codage = "<i>p".$i." :</i> &nbsp;<span id='pr".$i."'> ".$codes['annee']."-".$codes['numero'];
				$Prelevements = $codes['prelevement'];
				$html .= $this->codesPrelevementsRepris($codage, $Prelevements);
		
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
			$html .="<td style='width: 15%; vertical-align:top; margin-right:10px;'><span id='labelHeureLABEL' style='padding-left: 5px;'>Difficult&eacute;s</span><br><p id='zoneChampInfo1' style='background:#f8faf8; padding-left: 15px; padding-top: 5px; font-size:19px; width: 80px;'> ".$difficultes." </p></td>";
			$html .="<td style='width: 35%; vertical-align:top; margin-right:10px;'><span id='labelHeureLABEL' style='padding-left: 5px;'>Difficult&eacute;s rencontr&eacute;es </span><br><p id='zoneChampInfo1' style='background:#f8faf8; padding-left: 5px; padding-top: 5px; font-size:17px; max-height: 120px;'> ".$difficultesRencontrees." </p></td>";
			$html .="<td style='width: 15%; vertical-align:top; margin-right:10px;'><span id='labelHeureLABEL' style='padding-left: 5px;'>Transfuser </span><br><p id='zoneChampInfo1' style='background:#f8faf8; padding-left: 15px; padding-top: 5px; font-size:19px; width: 80px;'> ".$transfuser." </p></td>";
			$html .="<td style='width: 35%; vertical-align:top; margin-right:10px;'>";
			 
			if($moment_transfusion){
				$html .="<span id='labelHeureLABEL' style='padding-left: 5px;'>Dans les 3 derniers mois </span><br><p id='zoneChampInfo1' style='background:#f8faf8; padding-left: 15px; padding-top: 5px; font-size:19px; width: 160px;'> ".$moment_transfusion." </p>";
			}
			 
			$html .="</td>";
			$html .="</tr>";
			 
			$html .="</table>";
			
			$diagnostic = $bilanPrelevement->diagnostic;
			$traitement = $bilanPrelevement->traitement;
			if($diagnostic){
				$html .="<table id='form_patient' style='margin-top:10px; margin-left:17.5%; width: 80%; margin-bottom: 10px;'>";
				$html .="<td style='width: 35%; vertical-align:top; margin-right:10px;'><span id='labelHeureLABEL' style='padding-left: 5px;'>Diagnostic </span><br><p id='zoneChampInfo1' style='background:#f8faf8; padding-left: 5px; padding-top: 5px; font-size:17px; max-height: 90px;'> ".$diagnostic." </p></td>";
				$html .="<td style='width: 35%; vertical-align:top; margin-right:10px;'><span id='labelHeureLABEL' style='padding-left: 5px;'>Traitement </span><br><p id='zoneChampInfo1' style='background:#f8faf8; padding-left: 5px; padding-top: 5px; font-size:17px; max-height: 90px;'> ".$traitement." </p></td>";
				$html .="</table>";
			}
			 
		}
		 
		 
		$html .="
	    <div style='width: 100%;' align='center' >
	    <div style='width: 94%; margin-bottom: 30px; margin-top: 30px;'>
	    <div id='accordions' align='left' >
	    <div style='font-family: police2; font-size: 18px; font-weight: bold; background: #efefef;'>Liste des analyses &agrave; trier </div>
	       <div style='min-height: 300px; border-top: 1px solid #cccccc;' id='listeAnalysesPreleveesTableau'>
	       <form id='formEnregistrementTri' method='post' action='../technicien/enregistrer-tri-prelevement-repris'>";
		
		 
		 
		 $html .="<div style='margin-right: 5px; float:right; font-size: 15px; color: gray; margin-top:0px;'><a ><i style='font-family: Times New Roman; font-size: 15px; color: green;'>  </i></a></div>";
		
		 
		 $html .="<table class='table table-bordered tab_list_mini'  id='listeAnalysesPreleveesT' style='margin-top: 10px;'>";
		 
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
		 	
		 $listeAnalysesDemandees = $this->getBilanPrelevementTable()->getListeAnalysesDemandeesAyantUnBilanNonConforme($bilanPrelevement->idbilan);
		 
		 
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
		 
		 	$html .="<input type='hidden' name='demande_".$i."'  value='".$listeAnalysesDemandees[$i]['iddemande']."'>";
		 	$html .="<input type='hidden' name='analyse_".$i."'  value='".$listeAnalysesDemandees[$i]['idanalyse']."'>";
		 	$html .="<td id='conformiteA' style='font-size: 10px;'> <div class='conformitePrelevement'  id='conformitePrelevement_".$listeAnalysesDemandees[$i]['idanalyse']."' > <select name='conformite_".$listeAnalysesDemandees[$i]['idanalyse']."' id='conformite' disabled onchange='getConformit(this.value,".$listeAnalysesDemandees[$i]['idanalyse'].")'> <option value='1' >Conforme</option> <option value='0' style='font-size: 10px; color: red;' >Non conforme</option>  </select> </div> </td>";
		 	$html .="<td id='noteConformiteA' style='font-size: 12px;'> <div  id='noteConformite_".$listeAnalysesDemandees[$i]['idanalyse']."' > <img style='margin-left: 15px;' src='../../public/images_icons/tick_16.png'  > </div> </td>";
		 
		 
		 	$html .="</tr>";
		 
		 }
		 
		 	
		 $html .="</tbody>";
		 	
		 $html .="</table>";
		 
		 
		
		 $html .= "<span id='nbAnalysesATrier' style='float: left;'> </span>";
		 
   		 $html .="
		   <input type='hidden' name='nbAnalyse' value='".$i."'>
		   <input type='hidden' name='idbilan' value='".$bilanPrelevement->idbilan."'>
		   <input type='hidden' name='verifiernbClick' value='0'>
	       <button id='validerConformitePrelevementForm' style='display:none;' ></button>
	       </form>
	       </div>
	     </div>
	     </div>
	     </div>
		
	   
	     <script>
	      $( '#accordions' ).accordion();
	      listeAnalysesPreleveesDataTable();
	      $('#listeAnalysesPreleveesTableau .listeDataTable').toggle(false);
	      $('#listeAnalysesPreleveesTableau div .dataTables_paginate').css({'margin-top':'-15px'});
		
		  $('#nbAnalysesATrier').html('".$i." analyses');
		  $('a,img,span,div').tooltip({ animation: true, html: true, placement: 'bottom', show: { effect: 'slideDown', delay: 250 } });
	     </script>";
		
		 
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $html ) );
		
	}
	
	
	public function prelevementPrecedentAction()
	{

		$idbilan = ( int ) $this->params ()->fromPost ( 'idbilan', 0 );
		$idfacturation = ( int ) $this->params ()->fromPost ( 'idfacturation', 0 );
		
		$dateAujourdhui = ( new \DateTime () ) ->format( 'Y-m-d' );
		
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
		
		
			$html ="<div id='titre_info_admis_popup'>Bilan des pr&eacute;l&egrave;vements </div>";
			$html .="<div id='barre_separateur_popup'></div>";
		
			$html .="<table id='form_patient' style='margin-top:10px; width: 100%; margin-bottom: 10px;'>";
		
			$html .="<tr style='width: 80%; '>";
			$html .="<td style='width: 25%; vertical-align:top; margin-right:10px;'><span id='labelHeureLABEL' style='padding-left: 5px;'>Nombre de pr&eacute;l&egrave;vement</span><br><p id='zoneChampInfo1' style='background:#f8faf8; padding-left: 15px; padding-top: 5px; font-size:19px;'> ".$bilanPrelevement->nb_tube." </p></td>";
			$html .="<td style='width: 25%; vertical-align:top; margin-right:30px;'><span id='labelHeureLABEL' style='padding-left: 5px;'>Date & heure </span><br><p id='zoneChampInfo1' style='background:#f8faf8; padding-left: 5px; padding-top: 5px; font-size:19px;'> ".$bilanPrelevement->date_heure." </p></td>";
			$html .="<td style='width: 15%; vertical-align:top; margin-right:10px;'><span id='labelHeureLABEL' style='padding-left: 5px;'>A jeun </span><br><p id='zoneChampInfo1' style='background:#f8faf8; padding-left: 15px; padding-top: 5px; font-size:19px; width: 70px;'> ".$a_jeun." </p></td>";
			$html .="<td style='width: 35%; vertical-align:top; margin-right:10px;'>";
		
		
			//Afichage des codes des prélèvements
			//Afichage des codes des prélèvements
			$html .="<span id='labelCodagePrelevement' style='padding-left: 5px; '>Codage des pr&eacute;l&egrave;vements  </span><br>";
			$html .="<div id='codageTubePrelevement'>
					  <p> ";
		
			$listeCodes = $this->getCodagePrelevementTable() ->getCodagesPrelevements( $idfacturation );
		
			$i = 1;
			foreach ($listeCodes as $codes){
		
				$codage = "<i>p".$i." :</i> &nbsp;<span id='pr".$i."'> ".$codes->annee."-".$codes->numero;
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
		
			$html .="<table id='form_patient' style='margin-top:10px; width: 100%; margin-bottom: 10px;'>";
			
			$html .="<tr style='width: 80%; '>";
			$html .="<td style='width: 15%; vertical-align:top; margin-right:10px;'><span id='labelHeureLABEL' style='padding-left: 5px;'>Difficult&eacute;s</span><br><p id='zoneChampInfo1' style='background:#f8faf8; padding-left: 15px; padding-top: 5px; font-size:19px; width: 80px;'> ".$difficultes." </p></td>";
			$html .="<td style='width: 35%; vertical-align:top; margin-right:10px;'><span id='labelHeureLABEL' style='padding-left: 5px;'>Difficult&eacute;s rencontr&eacute;es </span><br><p id='zoneChampInfo1' style='background:#f8faf8; padding-left: 5px; padding-top: 5px; font-size:17px; max-height: 120px;'> ".$difficultesRencontrees." </p></td>";
			$html .="<td style='width: 15%; vertical-align:top; margin-right:10px;'><span id='labelHeureLABEL' style='padding-left: 5px;'>Transfuser </span><br><p id='zoneChampInfo1' style='background:#f8faf8; padding-left: 15px; padding-top: 5px; font-size:19px; width: 80px;'> ".$transfuser." </p></td>";
			$html .="<td style='width: 35%; vertical-align:top; margin-right:10px;'>";
			
			if($moment_transfusion){
				$html .="<span id='labelHeureLABEL' style='padding-left: 5px;'>Dans les 3 derniers mois </span><br><p id='zoneChampInfo1' style='background:#f8faf8; padding-left: 15px; padding-top: 5px; font-size:19px; width: 160px;'> ".$moment_transfusion." </p>";
			}
			
			$html .="</td>";
			$html .="</tr>";
			
			$html .="</table>";
		
		
		}
		
		
		
		$html .="
	    <div style='width: 100%;' align='center' >
	    <div style='width: 94%; margin-bottom: 30px; margin-top: 30px;'>
	    <div id='accordionss' align='left' >
	    <div style='font-family: police2; font-size: 18px; font-weight: bold; background: #efefef;'>Liste des analyses &agrave; trier </div>
	       <div style='min-height: 300px; border-top: 1px solid #cccccc;' id='listeAnalysesPreleveesTableau'>
	       <form id='formEnregistrementTri' method='post' action='../technicien/modifier-tri-prelevement'>";
		
		
		
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
		
			$html .="<input type='hidden' name='demande_".$i."'  value='".$listeAnalysesDemandees[$i]['iddemande']."'>";
			$html .="<input type='hidden' name='analyse_".$i."'  value='".$listeAnalysesDemandees[$i]['idanalyse']."'>";
			$html .="<td id='conformiteA' style='font-size: 10px;'> <div class='conformitePrelevement'  id='vue_conformitePrelevement_".$listeAnalysesDemandees[$i]['idanalyse']."' > <select name='conformite_".$listeAnalysesDemandees[$i]['idanalyse']."' id='vue_conformite_".$listeAnalysesDemandees[$i]['idanalyse']."' required=true onchange='getConformite(this.value,".$listeAnalysesDemandees[$i]['idanalyse'].")'> <option>  </option> <option value='1' >Conforme</option> <option value='0' style='font-size: 10px; color: red;' >Non conforme</option>  </select> </div> </td>";
			$html .="<td id='noteConformiteA' style='font-size: 12px;'> <div  id='vue_noteConformite_".$listeAnalysesDemandees[$i]['idanalyse']."' class='noteConformite_'>  </div> </td>";
		
		
			$html .="</tr>";
		
		}
		
			
		$html .="</tbody>";
		
		$html .="</table>";
		
		
		$html .= "<span id='nbAnalysesATrier' style='float: left;'> </span>";
		
		
		$html .="
		   <input type='hidden' name='nbAnalyse' value='".$i."'>
		   <input type='hidden' name='idbilan' value='".$bilanPrelevement->idbilan."'>
				   <button id='validerConformitePrelevementForm' style='display:none;' ></button>
				   </form>
				   </div>
			    </div>
			    </div>
			    </div>
			
			
		
		<script>
	      $( '#accordionss' ).accordion();
	      listeAnalysesPreleveesDataTable();
	      $('#listeAnalysesPreleveesTableau .listeDataTable').toggle(false);
	      $('#listeAnalysesPreleveesTableau div .dataTables_paginate').css({'margin-top':'-15px'});
	
		  $('#nbAnalysesATrier').html('".$i." analyses');
	    </script>";
		
		

		//SELECTION DES CONFORMITES  ---  SELECTION DES CONFORMITES ---
		//SELECTION DES CONFORMITES  ---  SELECTION DES CONFORMITES ---
		$tri = $this->getTriPrelevementTable() ->getPrelevementTrie($bilanPrelevement->idbilan);
		$tabAnalysesAyantResultats = $this->getBilanPrelevementTable() ->getListeAnalysesTrieesDuBilanAyantResultats($bilanPrelevement->idbilan);
		$tabAnalysesBilanRepris = $this->getBilanPrelevementTable() ->getVerifierBilanRepris($bilanPrelevement->idbilan);
		
		for($i = 0 ; $i < count($tri) ; $i++ ){
				
			$conformite = (int)$tri[$i] ->conformite;
			$idanalyse  = (int)$tri[$i] ->idanalyse;
				
			//Selection de la conformité
			//Selection de la conformité
			if(in_array($idanalyse, $tabAnalysesAyantResultats) || in_array($idanalyse, $tabAnalysesBilanRepris)){
				$html .="
				<script>
					$('#vue_conformite_".$idanalyse."').val('".$conformite."').attr('disabled', true);
				</script>
				";
			}else {
				$html .="
				<script>
					$('#vue_conformite_".$idanalyse."').val('".$conformite."');
				</script>
				";
			}
				
				
			if($conformite == 0){
		
				$note_non_conformite = $tri[$i] ->note_non_conformite;
		
				if(in_array($idanalyse, $tabAnalysesAyantResultats)  || in_array($idanalyse, $tabAnalysesBilanRepris)){
						
					$html .="
 				    <script>
 			           $('#vue_conformitePrelevement_".$idanalyse." select').css({'color':'red'});
     		           $('#vue_noteConformite_".$idanalyse."').html('<input name=\'noteNonConformite_".$idanalyse."\' title=\'Impossible de modifier\' id=\'vue_noteNonConformite_".$idanalyse."\' required=true  style=\'width: 100%; height: 100%; padding-left: 5px; font-size: 13px; font-family: time new romans;\'>');
 				       $('#vue_noteNonConformite_".$idanalyse."').val('".str_replace("'", "\'",$note_non_conformite)."').attr('disabled',true);
 				    </script>
 				    ";
						
				}else {
						
					$html .="
				    <script>
			           $('#vue_conformitePrelevement_".$idanalyse." select').css({'color':'red'});
    		           $('#vue_noteConformite_".$idanalyse."').html('<input name=\'noteNonConformite_".$idanalyse."\' id=\'vue_noteNonConformite_".$idanalyse."\' required=true  style=\'width: 100%; height: 100%; padding-left: 5px; font-size: 13px; font-family: time new romans;\'>');
				       $('#vue_noteNonConformite_".$idanalyse."').val('".str_replace("'", "\'",$note_non_conformite)."');
    		        </script>
				    ";
						
				}
		
			}else{
				$html .="
				    <script>
			           $('#noteConformite_".$idanalyse."').html('<img style=\'margin-left: 15px;\' src=\'../images_icons/tick_16.png\'  >');
				    </script>
				    ";
			}
				
		}
		
		$html .="
				<script>
				  $('a,img,span,div').tooltip({ animation: true, html: true, placement: 'bottom', show: { effect: 'slideDown', delay: 250 } });
				</script>
				";
	
		
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $html ) );
		
	}
	
	public function enregistrerTriPrelevementReprisAction(){
		
		$donnees = $this->getRequest()->getPost()->toArray();
		$nbAnalyse = $donnees['nbAnalyse'];
		$idbilan = $donnees['idbilan'];
	
		$date_enregistrement = ( new \DateTime () ) ->format( 'Y-m-d H:i:s' );
		$idemploye = $this->layout()->user['idemploye'];
	
		//var_dump($idbilan); exit();
		
		for($i = 0 ; $i < $nbAnalyse ; $i++){
				
			$iddemande  = $donnees['demande_'.$i];
			$idanalyse  = $donnees['analyse_'.$i];
			$conformite = $donnees['conformite_'.$idanalyse];
				
			$infoTri = array(
					'iddemande' => $iddemande,
					'idanalyse' => $idanalyse,
					'idbilan' => $idbilan,
					'conformite' => $this->entre,
						
					'date_enregistrement' => $date_enregistrement,
					'idemploye' => $idemploye,
			);
				
			//if($conformite == 0){ $infoTri['note_non_conformite'] = $donnees['noteNonConformite_'.$idanalyse ]; }
				
			//insertion dans la base de données
			//insertion dans la base de données
			$this->getTriPrelevementTable() ->addTriPrelevementRepris($infoTri);
				
		}
		
		return $this->redirect()->toRoute('technicien', array('action' =>'liste-bilans-tries'));
	
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	/*****************************************************************************************************************************/
	/*****************************************************************************************************************************/
	/*****************************************************************************************************************************/
	
	public function chemin(){
		return $this->getServiceLocator()->get('Request')->getBasePath();
	}
	
	public function listeDemandesAnalysesAction() {
		$this->layout ()->setTemplate ( 'layout/laboratoire' );
		
		return new ViewModel ( );
	}

	public function listeResultatsAnalysesAjaxAction() {
	
	    $output = $this->getPatientTable ()->listeResultatsAnalyses();
	    return $this->getResponse ()->setContent ( Json::encode ( $output, array (
	        'enableJsonExprFinder' => true
	    ) ) );
	
	}
	
	public function listeResultatsAnalysesAction() {
	    $this->layout ()->setTemplate ( 'layout/laboratoire' );
	
	    return new ViewModel ( );
	}
	
	public function infosPatientAction() {
		
		$id = ( int ) $this->params ()->fromPost ( 'id', 0 );
		
		$control = new DateHelper();
		
		$patient = $this->getPatientTable()->getPatient($id);
		$personne = $this->getPersonneTable()->getPersonne($id);
		$depistage = $this->getPatientTable()->getDepistagePatient($id);
		$date_naissance = null;
		if($personne->date_naissance){ $date_naissance = $control->convertDate( $personne->date_naissance ); }
		$informations_parentales = $this->getPersonneTable()->getInfosParentales($id);
		

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
					
					$designation = $typageHemoglobine['designation'];
					if($designation == 'SB+thal'){ $designation = "S&beta;<sup>+</sup> thalass&eacute;mie"; }else
					if($designation == 'SBÂ°thal'){ $designation = "S&beta;&deg; thalass&eacute;mie"; }
					
					$typage = "(<span style='color: red;'>".$designation."</span>)" ;
					
				}else{
					
					$designation = $typageHemoglobine['designation'];
					if($designation == 'AB+thal'){ $designation = "A&beta;<sup>+</sup> thalass&eacute;mie"; }else
					if($designation == 'ABÂ°thal'){ $designation = "A&beta;&deg; thalass&eacute;mie"; }
					
					$typage = "(".$designation.")" ;
				}
			}
		}
		
		
		$html ="
				
	    <div style='width: 100%;'>

        <img id='photo' src='".$this->chemin()."/img/photos_patients/".$personne->photo."' style='float:left; margin-right:40px; width:105px; height:105px;'/>";

        
        //Gestion des AGE
		if($personne->age && !$personne->date_naissance){
			$html .="<div style=' left: 70px; top: 235px; font-family: time new romans; position: absolute; '> Age: <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$personne->age." ans </span></div>";
		}else{
			$aujourdhui = (new \DateTime() ) ->format('Y-m-d');
			$age_jours = $this->nbJours($personne->date_naissance, $aujourdhui);
		
			$age_annees = (int)($age_jours/365);
		
			if($age_annees == 0){
		
				if($age_jours < 31){
					$html .="<div style='left: 70px; top: 235px; position: absolute; font-family: time new romans; '> Age: <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_jours." jours </span></div>";
				}else if($age_jours >= 31) {
					 
					$nb_mois = (int)($age_jours/31);
					$nb_jours = $age_jours - ($nb_mois*31);
					if($nb_jours == 0){
						$html .="<div style='left: 70px; top: 235px; position: absolute; font-family: time new romans; '> Age: <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$nb_mois."m </span></div>";
					}else{
						$html .="<div style='left: 70px; top: 235px; position: absolute; font-family: time new romans; '> Age: <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$nb_mois."m ".$nb_jours."j </span></div>";
					}
		
				}
		
			}else{
				$age_jours = $age_jours - ($age_annees*365);
		
				if($age_jours < 31){
		
					if($age_annees == 1){
						if($age_jours == 0){
							$html .="<div style='left: 60px; top: 235px; position: absolute; font-family: time new romans; '> <span style='font-size: 14px;'> Age: </span> <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_annees."an </span></div>";
						}else{
							$html .="<div style='left: 60px; top: 235px; position: absolute; font-family: time new romans; '> <span style='font-size: 14px;'> Age: </span> <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_annees."an ".$age_jours." j </span></div>";
						}
					}else{
						if($age_jours == 0){
							$html .="<div style='left: 60px; top: 235px; position: absolute; font-family: time new romans; '> <span style='font-size: 14px;'> Age: </span> <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_annees."ans </span></div>";
						}else{
							$html .="<div style='left: 60px; top: 235px; position: absolute; font-family: time new romans; '> <span style='font-size: 14px;'> Age: </span> <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_annees."ans ".$age_jours."j </span></div>";
						}
					}
					 
				}else if($age_jours >= 31) {
					 
					$nb_mois = (int)($age_jours/31);
					$nb_jours = $age_jours - ($nb_mois*31);
		
					if($age_annees == 1){
						if($nb_jours == 0){
							$html .="<div style='left: 60px; top: 235px; position: absolute; font-family: time new romans; '> <span style='font-size: 13px;'> Age: </span> <span style='font-size:18px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_annees."an ".$nb_mois."m </span></div>";
						}else{
							$html .="<div style='left: 50px; top: 235px; position: absolute; font-family: time new romans; '> <span style='font-size: 13px;'> Age: </span> <span style='font-size:17px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_annees."an ".$nb_mois."m ".$nb_jours."j </span></div>";
						}
		
					}else{
						if($nb_jours == 0){
							$html .="<div style='left: 60px; top: 235px; position: absolute; font-family: time new romans; '> <span style='font-size: 13px;'> Age: </span> <span style='font-size:18px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_annees."ans ".$nb_mois."m </span></div>";
						}else{
							$html .="<div style='left: 50px; top: 235px; position: absolute; font-family: time new romans; '> <span style='font-size: 13px;'> Age: </span> <span style='font-size:17px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_annees."ans ".$nb_mois."m ".$nb_jours."j </span></div>";
						}
					}
		
				}
		
			}
		
		}
        
        
        $html .="<p style='color: white; opacity: 0.09;'>
         <img id='photo' src='".$this->chemin()."/img/photos_patients/".$personne->photo."' style='float:right; margin-right:15px; width:95px; height:95px;'/>
        </p>
        <table id='etat_civil'>
             <tr>
			   	<td style='width:27%; font-family: police1;font-size: 12px;'>
			   		<div id='aa'><a style='text-decoration: underline;'>Pr&eacute;nom</a><br><p style='font-weight: bold;font-size: 19px;'> ".$personne->prenom." </p></div>
			   	</td>

			   	<td style='width:35%; font-family: police1;font-size: 12px;'>
			   		<div id='aa'><a style='text-decoration: underline;'>Lieu de naissance</a><br><p style='font-weight: bold;font-size: 19px;'> ".$personne->lieu_naissance."  </p></div>
			   	</td>

			    <td style='width:38%; font-family: police1;font-size: 12px;'>
			   		<div id='aa'><a style='text-decoration: underline;'>T&eacute;l&eacute;phone</a><br><p style='font-weight: bold;font-size: 19px;'> ".$personne->telephone." </p></div>
			   	</td>

			 </tr>

			 <tr>
			    <td style=' font-family: police1;font-size: 12px;'>
			   		<div id='aa'><a style='text-decoration: underline;'>Nom</a><br><p style='font-weight: bold;font-size: 19px;'> ".$personne->nom." </p></div>
			   	</td>";


		if($depister == 0){
		    $html .="<td style=' font-family: police1;font-size: 12px;'>
			   		<div id='aa'><a style='text-decoration: underline;'>Nationalit&eacute; origine</a><br><p style='font-weight: bold;font-size: 19px;'> ".$personne->nationalite_origine." </p></div>
			   	</td>";
		}else{
		    $html .="<td style=' font-family: police1;font-size: 12px;'>
			   		<div id='aa'><a style='text-decoration: underline;'>Ethnie</a><br><p style='font-weight: bold;font-size: 19px;'> ".$depistage->current()['ethnie']." </p></div>
			   	</td>";
		}
		

	   $html .="<td style=' font-family: police1;font-size: 12px;'>
			   		<div id='aa'><a style='text-decoration: underline;'>Email</a><br><p style='font-weight: bold;font-size: 19px;'> ".$personne->email." </p></div>
			   	</td>

			 </tr>

			 <tr>
			    <td style=' font-family: police1;font-size: 12px;'>
			   		<div id='aa'><a style='text-decoration: underline;'>Sexe</a><br><p style='font-weight: bold;font-size: 19px;'> ".$personne->sexe." </p></div>
			   	</td>

			    <td style=' font-family: police1;font-size: 12px;'>
			   		<div id='aa'><a style='text-decoration: underline;'>Nationalit&eacute; actuelle</a><br><p style='font-weight: bold;font-size: 19px;'> ".$personne->nationalite_actuelle." </p></div>
			   	</td>

			   	<td style=' font-family: police1;font-size: 12px;'>
			   		<div id='aa'><a style='text-decoration: underline;'>Profession</a><br><p style='font-weight: bold;font-size: 19px;'> ".$personne->profession." </p></div>
			   	</td>

			 </tr>

			  <tr>
			   	<td style='width: 27%; font-family: police1;font-size: 12px; vertical-align: top;'>
			   	
			   		<div id='aa'><a style='text-decoration: underline; '>Date de naissance</a><br>
			   		  <p style='font-weight: bold;font-size: 19px;'>
			   		     ".$date_naissance."
			   		     
			   		  </p>
			   		</div>
			   		
			   	</td>

			    <td style='width: 195px; font-family: police1;font-size: 12px;'>
			   		<div id='aa'><a style='text-decoration: underline;'>Adresse</a><br><p style='font-weight: bold;font-size: 19px;'> ".$personne->adresse." </p></div>
			   	</td>
			   				
			   	<td  style='width: 195px; font-family: police1;font-size: 12px;'>
                   <div  id='aa'><a style='text-decoration: underline;'>Type   </a><br><p style='font-weight: bold;font-size: 19px;'> ".$type." ".$typage."</p></div>";
	   
	   if($informations_parentales){
	       $html .="<div style='width: 50px; height: 35px; float: right; margin-top: -40px; '><a href='javascript:infos_parentales(".$id.");' > <img id='infos_parentales_".$id."' style='float: right; cursor: pointer;' src='../images_icons/Infos_parentales.png' /> </a></div>";
	   }

     $html .="  </td>			
			  </tr>
			  	 <tr>

			  	 </tr>
           </table>

           <div id='barre'></div>

           <div style='color: white; opacity: 1; width:95px; height:40px; float:right'>
             <img  src='".$this->chemin()."/images_icons/fleur1.jpg' />
           </div>
	       <table id='numero' style=' padding-top:5px; width: 60%;  '>
           <tr>
             <td style='padding-top:3px; padding-left:25px; padding-right:5px; font-size: 14px; width: 95px;'> Code du patient: </td>
             <td style='font-weight: bold; '>".$patient->numero_dossier."</td>
             <td style='font-weight: bold;padding-left:20px;'>|</td>
             <td style='padding-top:5px; padding-left:10px; font-size: 14px;'> Date d'enregistrement: </td>
             <td style='font-weight: bold;'>". $control->convertDateTime( $patient->date_enregistrement ) ."</td>
           </tr>
           </table>

	    <div class='block' id='thoughtbot' style=' vertical-align: bottom; padding-left:60%; padding-top: 35px; font-size: 18px; font-weight: bold;'><button id='terminer'>Terminer</button></div>
             		
        <div style=' height: 80px; width: 100px; '> </div>
     
        </div> ";
     
     


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
		
		
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $html ) );
	}
	
	//Pour les listes **** pour les listes **** pour les listes
	//Pour les listes **** pour les listes **** pour les listes
	//Pour les listes **** pour les listes **** pour les listes
	public function informationPatientAction($id)
	{
		$control = new DateHelper();
		
		$patient = $this->getPatientTable()->getPatient($id);
		$personne = $this->getPersonneTable()->getPersonne($id);
		$depistage = $this->getPatientTable()->getDepistagePatient($id);
		$date_naissance = null;
		if($personne->date_naissance){ $date_naissance = $control->convertDate( $personne->date_naissance ); }
		$informations_parentales = $this->getPersonneTable()->getInfosParentales($id);
		
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
					
					$designation = $typageHemoglobine['designation'];
					if($designation == 'SB+thal'){ $designation = "S&beta;<sup>+</sup> thalass&eacute;mie"; }else
					if($designation == 'SBÂ°thal'){ $designation = "S&beta;&deg; thalass&eacute;mie"; }
						
					$typage = "(<span style='color: red;'>".$designation."</span>)" ;
						
				}else{
					
					$designation = $typageHemoglobine['designation'];
					if($designation == 'AB+thal'){ $designation = "A&beta;<sup>+</sup> thalass&eacute;mie"; }else
					if($designation == 'ABÂ°thal'){ $designation = "A&beta;&deg; thalass&eacute;mie"; }
						
					$typage = "(".$designation.")" ;
				}
			}
		}
		
		
		$html ="
		
	    <div style='width: 100%;'>
		
        <img id='photo' src='".$this->chemin()."/img/photos_patients/".$personne->photo."' style='float:left; margin-right:40px; width:105px; height:105px;'/>";
		
        
        //Gestion des AGE
		if($personne->age && !$personne->date_naissance){
			$html .="<div style=' left: 70px; top: 235px; font-family: time new romans; position: absolute; '> Age: <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$personne->age." ans </span></div>";
		}else{
			$aujourdhui = (new \DateTime() ) ->format('Y-m-d');
			$age_jours = $this->nbJours($personne->date_naissance, $aujourdhui);
		
			$age_annees = (int)($age_jours/365);
		
			if($age_annees == 0){
		
				if($age_jours < 31){
					$html .="<div style='left: 70px; top: 235px; position: absolute; font-family: time new romans; '> Age: <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_jours." jours </span></div>";
				}else if($age_jours >= 31) {
		
					$nb_mois = (int)($age_jours/31);
					$nb_jours = $age_jours - ($nb_mois*31);
					if($nb_jours == 0){
						$html .="<div style='left: 70px; top: 235px; position: absolute; font-family: time new romans; '> Age: <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$nb_mois."m </span></div>";
					}else{
						$html .="<div style='left: 70px; top: 235px; position: absolute; font-family: time new romans; '> Age: <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$nb_mois."m ".$nb_jours."j </span></div>";
					}
		
				}
		
			}else{
				$age_jours = $age_jours - ($age_annees*365);
		
				if($age_jours < 31){
		
					if($age_annees == 1){
						if($age_jours == 0){
							$html .="<div style='left: 60px; top: 235px; position: absolute; font-family: time new romans; '> <span style='font-size: 14px;'> Age: </span> <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_annees."an </span></div>";
						}else{
							$html .="<div style='left: 60px; top: 235px; position: absolute; font-family: time new romans; '> <span style='font-size: 14px;'> Age: </span> <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_annees."an ".$age_jours." j </span></div>";
						}
					}else{
						if($age_jours == 0){
							$html .="<div style='left: 60px; top: 235px; position: absolute; font-family: time new romans; '> <span style='font-size: 14px;'> Age: </span> <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_annees."ans </span></div>";
						}else{
							$html .="<div style='left: 60px; top: 235px; position: absolute; font-family: time new romans; '> <span style='font-size: 14px;'> Age: </span> <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_annees."ans ".$age_jours."j </span></div>";
						}
					}
		
				}else if($age_jours >= 31) {
		
					$nb_mois = (int)($age_jours/31);
					$nb_jours = $age_jours - ($nb_mois*31);
		
					if($age_annees == 1){
						if($nb_jours == 0){
							$html .="<div style='left: 60px; top: 235px; position: absolute; font-family: time new romans; '> <span style='font-size: 13px;'> Age: </span> <span style='font-size:18px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_annees."an ".$nb_mois."m </span></div>";
						}else{
							$html .="<div style='left: 50px; top: 235px; position: absolute; font-family: time new romans; '> <span style='font-size: 13px;'> Age: </span> <span style='font-size:17px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_annees."an ".$nb_mois."m ".$nb_jours."j </span></div>";
						}
		
					}else{
						if($nb_jours == 0){
							$html .="<div style='left: 60px; top: 235px; position: absolute; font-family: time new romans; '> <span style='font-size: 13px;'> Age: </span> <span style='font-size:18px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_annees."ans ".$nb_mois."m </span></div>";
						}else{
							$html .="<div style='left: 50px; top: 235px; position: absolute; font-family: time new romans; '> <span style='font-size: 13px;'> Age: </span> <span style='font-size:17px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_annees."ans ".$nb_mois."m ".$nb_jours."j </span></div>";
						}
					}
		
				}
		
			}
		
		}
		
		
        
        $html .="<p style='color: white; opacity: 0.09;'>
         <img id='photo' src='".$this->chemin()."/img/photos_patients/".$personne->photo."' style='float:right; margin-right:15px; width:95px; height:95px;'/>
         <div style='position: absolute; top: 220px; right: 55px; font-size:17px; font-family: Iskoola Pota; color: green; float: right; font-style: italic; '> ".$patient->numero_dossier." </div>
        </p>
        <table id='etat_civil'>
             <tr>
			   	<td style='width:27%; font-family: police1;font-size: 12px;'>
			   		<div id='aa'><a style='text-decoration: underline;'>Pr&eacute;nom</a><br><p style='font-weight: bold;font-size: 19px;'> ".$personne->prenom." </p></div>
			   	</td>
		
			   	<td style='width:35%; font-family: police1;font-size: 12px;'>
			   		<div id='aa'><a style='text-decoration: underline;'>Lieu de naissance</a><br><p style='font-weight: bold;font-size: 19px;'> ".$personne->lieu_naissance."  </p></div>
			   	</td>
		
			    <td style='width:38%; font-family: police1;font-size: 12px;'>
			   		<div id='aa'><a style='text-decoration: underline;'>T&eacute;l&eacute;phone</a><br><p style='font-weight: bold;font-size: 19px;'> ".$personne->telephone." </p></div>
			   	</td>
		
			 </tr>
		
			 <tr>
			    <td style=' font-family: police1;font-size: 12px;'>
			   		<div id='aa'><a style='text-decoration: underline;'>Nom</a><br><p style='font-weight: bold;font-size: 19px;'> ".$personne->nom." </p></div>
			   	</td>";
		

		if($depister == 0){
		    $html .="<td style=' font-family: police1;font-size: 12px;'>
			   		<div id='aa'><a style='text-decoration: underline;'>Nationalit&eacute; origine</a><br><p style='font-weight: bold;font-size: 19px;'> ".$personne->nationalite_origine." </p></div>
			   	</td>";
		}else{
		    $html .="<td style=' font-family: police1;font-size: 12px;'>
			   		<div id='aa'><a style='text-decoration: underline;'>Ethnie</a><br><p style='font-weight: bold;font-size: 19px;'> ".$depistage->current()['ethnie']." </p></div>
			   	</td>";
		}
		
	   $html .="<td style=' font-family: police1;font-size: 12px;'>
			   		<div id='aa'><a style='text-decoration: underline;'>Email</a><br><p style='font-weight: bold;font-size: 19px;'> ".$personne->email." </p></div>
			   	</td>
		
			 </tr>
		
			 <tr>
			    <td style=' font-family: police1;font-size: 12px;'>
			   		<div id='aa'><a style='text-decoration: underline;'>Sexe</a><br><p style='font-weight: bold;font-size: 19px;'> ".$personne->sexe." </p></div>
			   	</td>
		
			    <td style=' font-family: police1;font-size: 12px;'>
			   		<div id='aa'><a style='text-decoration: underline;'>Nationalit&eacute; actuelle</a><br><p style='font-weight: bold;font-size: 19px;'> ".$personne->nationalite_actuelle." </p></div>
			   	</td>
		
			   	<td style=' font-family: police1;font-size: 12px;'>
			   		<div id='aa'><a style='text-decoration: underline;'>Profession</a><br><p style='font-weight: bold;font-size: 19px;'> ".$personne->profession." </p></div>
			   	</td>
		
			 </tr>
		
			  <tr>
			   	<td style='width: 27%; font-family: police1;font-size: 12px; vertical-align: top;'>
			 
			   		<div id='aa'><a style='text-decoration: underline; '>Date de naissance</a><br>
			   		  <p style='font-weight: bold;font-size: 19px;'>
			   		     ".$date_naissance."
			   	
			   		  </p>
			   		</div>
		
			   	</td>
		
			    <td style='width: 195px; font-family: police1;font-size: 12px;'>
			   		<div id='aa'><a style='text-decoration: underline;'>Adresse</a><br><p style='font-weight: bold;font-size: 19px;'> ".$personne->adresse." </p></div>
			   	</td>
			   				
			   	<td  style='width: 195px; font-family: police1;font-size: 12px;'>
                   <div  id='aa'><a style='text-decoration: underline;'>Type   </a><br><p style='font-weight: bold;font-size: 19px;'> ".$type." ".$typage." </p></div>";
			  	
	   if($informations_parentales){
	       $html .="<div style='width: 50px; height: 35px; float: right; margin-top: -40px; '><a href='javascript:infos_parentales(".$id.");' > <img id='infos_parentales_".$id."' style='float: right; cursor: pointer;' src='../images_icons/Infos_parentales.png' /> </a></div>";
	   }
                       
        $html .="</td>				
			  </tr>
			  	 <tr>
		
			  	 </tr>
           </table>
           
         
		   <div style=' height: 10px; width: 100px; '> </div>
   
           </div> ";
        
        
        


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

	/*
	 * La liste des demandes d'analyses pour lesquelles les resultats ne sont pas encore validés par le biologiste 
	 */
	public function listeDemandesAnalyseNonTraiter($id)
	{
		
		$listeDemandesAnalyses = $this->getAnalyseTable ()->getDemandesAnalysesTriees($id);
		$listeAnalysesDemandees = $this->getAnalyseTable ()->getAnalysesDemandeesTriees($id);
		$i = 1;
		$iddemande = 0;
		$controle = new DateHelper();
		
		$html ='<table style="width: 100%;" >
 				    <tr style="width: 100%;" >
				        <td style="width: 35%; height: 350px;" >';
				        
				        
		      $html .="<table class='table table-bordered tab_list_mini' id='listeDemandesFiltre' >";
				        	
			  $html .="<thead style='width: 100%;'>
				         <tr style='height:25px; width:100%; cursor:pointer;'>
					        <th id='numerof' style='cursor: pointer;'>Inf</th>
					        <th id='datef' style='cursor: pointer;'>Date de la demande</th>
				            <th id='optionf' >Opt</th>
				         </tr>
			           </thead>";
				        
			  $html .="<tbody id='donnees' class='liste_patient' style='width: 100%;'>";
			  
			  foreach ($listeDemandesAnalyses as $listeDemandes){
			  	
			  	$aujourdhui = (new \DateTime() ) ->format('d/m/Y');
			  	$date = $controle->convertDate( $listeDemandes['date'] );
			  	if($date == $aujourdhui){ $date = "aujourd'hui  - ".$listeDemandes['time']; } else { $date = $date.' - '.$listeDemandes['time']; } 
			  	
			  	//Informations sur le secretaire
			  	$infosSecretaire = $this->getPatientTable ()->getInfosSecretaire($listeDemandes['idsecretaire']);
			  	
			  	$html .="<tr style='height: 25px; width:100%;'>
			  	           <td id='numerof'>
      					       <hass><img style='padding-left: 10px; cursor: pointer;' class='info_secretaire".$listeDemandes['iddemande']."' src='../images_icons/info_infirmier.png' title='effectuÃ©e par: ".$infosSecretaire['prenom'].' '.$infosSecretaire['nom']."' /></hass>
			  			   </td>
      					   <td id='datef' class='dateAffichee  dateAffichee_".$listeDemandes['iddemande']."' >". $date ."</td>
			  	           <td id='optionf'>";

			  	if($i == 1){
			  		$iddemande = $listeDemandes['iddemande'];
			  		$html .="<a class='iconeListeAffichee visualiser".$listeDemandes['iddemande']."' href='javascript:vueListeAnalyses(".$listeDemandes['iddemande'].")' >";
			  		$html .="<img style='padding-left: 3px; ' src='../images_icons/transfert_droite2.png' /></a>"; $i = 0;
			  	}else {
			  		$html .="<a class='iconeListeAffichee visualiser".$listeDemandes['iddemande']."' href='javascript:vueListeAnalyses(".$listeDemandes['iddemande'].")' >";
			  		$html .="<img style='padding-left: 3px; cursor: pointer;' class='' src='../images_icons/transfert_droite2.png' /></a>";
			  	}
				
				$html .=" </td>
			  	        </tr>";
			  	
			  	$html .="<script> $('.info_secretaire".$listeDemandes['iddemande']."').tooltip({ animation: true, html: true, placement: 'bottom', show: { effect: 'slideDown', } }); </script>";
			  }
			  
		      $html .="</tbody>";
				        
				        
			  $html .="<tfoot class='foot_style foot_style_demande' style='width: 100%;'>
		                 <tr style='height:45px; width:100%; cursor:pointer;'>
					       <th id='numerof_' > <input type='hidden' /> </th>
				           <th id='datef_' > <input type='text' name='search_browser' value=' Date' class='search_init' /></th>
				           <th id='optionf_' > <input type='hidden' /></th>
				         </tr>
				        
		               </tfoot>";
				        
			  $html .="</table>";
				         
				
			  $html.="</td>";
				
				        
				        
 		      $html .="<td style='width: 4%;' > <div style='width: 3px; height: 400px; background: #cccccc; margin-left: 15px;'></div> </td>";
				        
 			
			  $html .="<td id='liste_analyses_demandes' style='width: 61%; height: 50px;' >";
				        
		
              $demande = $this->getPatientTable ()->getDemandeAnalysesAvecIddemande($iddemande);
		      $controle = new DateHelper();
		      $aujourdhui = (new \DateTime() ) ->format('d/m/Y');
		      $date = $controle->convertDate( $demande['date'] );
		      if($date == $aujourdhui){ $date = "aujourd'hui  - ".$demande['time']; } else { $date = $date.' - '.$demande['time']; }
		      
 		      $total = $this->getPatientTable()->getMontantTotalAnalyses($iddemande);
		      
 		      $diagnostic_demande = $this->getAnalyseTable()->getDiagnosticAnalyse($demande['idpatient'], $demande['date']);
 		      
 		      $html .="<div id='imprimerAnalyses' style='cursor: pointer; float:right; margin-top: -5px;'><span style='padding-right: 20px; margin-top: 20px; color: green; font-size: 17px; font-family: times new roman;'> ".$date." </span> ";
 		      
 		      if($diagnostic_demande){
 		          $text = str_replace("'", "\'", $diagnostic_demande['diagnostic_demande']);
 		          $html .="<span> <a href='javascript:diagnostic(".$id.")' > <img id='diagnostic_".$id."'  style='padding-left: 3px; padding-right: 7px;' src='../images_icons/detailsd.png' /> </a> </span>";
 		          $html .="<script> $('#diagnostic_demande_text').val('".preg_replace("/(\r\n|\n|\r)/", " ", $text)."'); </script>";
 		      }
		      
 		      $html .="<a href='javascript:resultatsDesAnalyses(".$iddemande.");'> <img style='padding-left: 3px; ' src='../images_icons/resultat2.png' title='Resultats' /> </a> <!--a href='javascript:imprimerResultatsAnalysesDemandees(".$iddemande.");'> <hass> <img style='padding-left: 3px; ' src='../images_icons/pdf.png' title='Imprimer' /> </hass> </a--> </div>";
		      
		      
		      $html .="<table class='table table-bordered tab_list_mini' id='listeAnalyseFiltre' >";
			
		      $html .="<thead style='width: 100%;'>
				         <tr style='height:25px; width:100%; cursor:pointer;'>
					        <th id='typeA' style='cursor: pointer;'>Type</th>
					        <th id='analyseA' style='cursor: pointer;'>Analyse</th>
				            <th id='optionA' style='font-size: 12px;' >Options</th>
				         </tr>
			           </thead>";
		
		      $html .="<tbody  class='liste_patient liste_analyses_demandes' style='width: 100%;'>";

 		      foreach ($listeAnalysesDemandees as $listeAnalyses){
		      	
 		        $existeResultat = $this->getResultatDemandeAnalyseTable()->getResultatDemandeAnalyse($listeAnalyses['iddemande']);
 		          
		      	$html .="<tr style='height:25px; width:100%; font-family: times new roman;'>
					       <td id='typeA' style='font-size: 14px;'> ".$listeAnalyses['Libelle']." </td>
					       <td id='analyseA' style='font-size: 14px;'> ".$listeAnalyses['Designation']." </td> ";
				$html .="<td id='optionA' style='font-size: 17px;'>";
				$html .="<div> 
				         <a href='javascript:resultatAnalyse(".$listeAnalyses['iddemande'].");' style='cursor: pointer;'> 
				             <img class='titre_resultat_".$listeAnalyses['iddemande']."'  src='../images_icons/resultat1.png' title='r&eacute;sultat' /> 
				         </a>";

				$html .="<a class='resultat_existe".$listeAnalyses['iddemande']."' style='margin-left: 10px;'>";
				if($existeResultat){ $html .= "<img  src='../images_icons/tick_16.png' />"; }
				$html .="</a>";
				
				$html .="</div>
				         </td>
				         </tr>";
				
		      	$html .="<script> $('.titre_resultat_".$listeAnalyses['iddemande']."').tooltip({ animation: true, html: true, placement: 'bottom', show: { effect: 'slideDown', } }); </script>";
		      	 
 		      }
		               
 		      $html .="</tbody>";
		      
		      
		      $html .="<tfoot class='foot_style foot_style_analyse' style='width: 100%;'>
		                 <tr style='height:45px; width:100%; cursor:pointer;'>
					       <th id='typeA_'> <input type='text' name='search_browser' value=' Type' class='search_init' /></th>
				           <th id='analyseA_'> <input type='text' name='search_browser' value=' Analyse' class='search_init' /></th>
				           <th id='optionA_'> <input type='hidden' /></th>
				         </tr>
		      
		               </tfoot>";
		
		      $html .="</table>";
		      		
		      		
		      		
   	          $html .="</td>
				       </tr>
				      </table>";
		
		
		$html .="<script> 
				 listeDemandesAnalyses(); listeAnalysesDemandes();
				</script>";
		
		return $html;
	}
	
	public function getInformationsPatientAction()
	{
		$id = ( int ) $this->params ()->fromPost ( 'id', 0 );

		$iddemande = $this->getAnalyseTable()->getDemandesAnalysesTriees($id) ->current()['iddemande'];
		
		$html = $this->informationPatientAction($id);
		
		$html2 = $this->listeDemandesAnalyseNonTraiter($id);
		
		$html3 = array($html, $html2,  $iddemande);
		
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $html3 ) );
	}
	
	
 	public function getListeAnalysesDemandeesAction()
	{
		$iddemande = ( int ) $this->params ()->fromPost ( 'iddemande', 0 );
	
		$listeAnalysesDemandees = $this->getAnalyseTable ()->getListeAnalysesDemandeesTriees($iddemande);
		$demande = $this->getPatientTable ()->getDemandeAnalysesAvecIddemande($iddemande);
	
		$controle = new DateHelper();
		$aujourdhui = (new \DateTime() ) ->format('d/m/Y');
		$date = $controle->convertDate( $demande['date'] );
		if($date == $aujourdhui){ $date = "aujourd'hui  - ".$demande['time']; } else { $date = $date.' - '.$demande['time']; }
	
		$total = $this->getPatientTable()->getMontantTotalAnalyses($iddemande);
	
		$diagnostic_demande = $this->getAnalyseTable()->getDiagnosticAnalyse($demande['idpatient'], $demande['date']);
		
		$html ="<div id='imprimerAnalyses' style='cursor: pointer; float:right; margin-top: -5px;'><span style='padding-right: 20px; margin-top: 20px; color: green; font-size: 17px; font-family: times new roman;'> ".$date." </span> ";
		
		if($diagnostic_demande){
		    $text = str_replace("'", "\'", $diagnostic_demande['diagnostic_demande']);
		    $html .="<span> <a href='javascript:diagnostic(".$iddemande.")' > <img id='diagnostic_".$iddemande."'  style='padding-left: 3px; padding-right: 7px;' src='../images_icons/detailsd.png' /> </a> </span>";
		    $html .="<script> $('#diagnostic_demande_text').val('".preg_replace("/(\r\n|\n|\r)/", " ", $text)."'); </script>";
		}
		
		$html .="<a href='javascript:resultatsDesAnalyses(".$iddemande.");'> <img style='padding-left: 3px; ' src='../images_icons/resultat2.png' title='Resultats' /> </a> <!--a href='javascript:imprimerResultatsAnalysesDemandees(".$iddemande.");'> <hass> <img style='padding-left: 3px; ' src='../images_icons/pdf.png' title='Imprimer' /> </hass> </a--> </div>";
		
		
		$html .="<table class='table table-bordered tab_list_mini' id='listeAnalyseFiltre' >";
			
		$html .="<thead style='width: 100%;'>
				         <tr style='height:25px; width:100%; cursor:pointer;'>
					        <th id='typeA' style='cursor: pointer;'>Type</th>
					        <th id='analyseA' style='cursor: pointer;'>Analyse</th>
				            <th id='optionA' style='font-size: 12px;' >Options</th>
				         </tr>
			     </thead>";
		
	
		$html .="<tbody id='liste_analyses_demandes' class='liste_patient liste_analyses_demandes' style='width: 100%;'>";
	
		foreach ($listeAnalysesDemandees as $listeAnalyses){
	
		    $existeResultat = $this->getResultatDemandeAnalyseTable()->getResultatDemandeAnalyse($listeAnalyses['iddemande']);
		    
			$html .="<tr style='height:25px; width:100%; font-family: times new roman;'>
					       <td id='typeA' style='font-size: 14px;'> ".$listeAnalyses['Libelle']." </td>
					       <td id='analyseA' style='font-size: 14px;'> ".$listeAnalyses['Designation']." </td> ";
			$html .="<td id='optionA' style='font-size: 17px;'>";
			$html .="<div>
				     <a href='javascript:resultatAnalyse(".$listeAnalyses['iddemande'].");' style='cursor: pointer;'> 
				         <img class='titre_resultat_".$listeAnalyses['iddemande']."' src='../images_icons/resultat1.png' title='r&eacute;sultat' /> 
				     </a>";
			
			$html .="<a class='resultat_existe".$listeAnalyses['iddemande']."' style='margin-left: 10px;'>";
			if($existeResultat){ $html .= "<img src='../images_icons/tick_16.png'  />"; }
			$html .="</a>";
			
			$html .="</div>
				     </td>
				     </tr>";
			
			
			$html .="<script> $('.titre_resultat_".$listeAnalyses['iddemande']."').tooltip({ animation: true, html: true, placement: 'bottom', show: { effect: 'slideDown', } }); </script>";
	
		}
			
		$html .="</tbody>";
	
	
		$html .="<tfoot class='foot_style foot_style_analyse' style='width: 100%;'>
		                 <tr style='height:45px; width:100%; cursor:pointer;'>
					       <th id='typeA_'> <input type='text' name='search_browser' value=' Type' class='search_init' /></th>
				           <th id='analyseA_'> <input type='text' name='search_browser' value=' Analyse' class='search_init' /></th>
				           <th id='optionA_'> <input type='hidden' /></th>
				         </tr>
	
		         </tfoot>";
	
		$html .="</table>";
	
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $html ) );
	}
	
	
	
	//****** =========== RECUPERER L'ANALYSE =========== *******
	//****** =========== RECUPERER L'ANALYSE =========== *******
	//****** =========== RECUPERER L'ANALYSE =========== *******
	protected function getResultatsNfs($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursNfs($iddemande);
	    $html ="";
	    if($resultat){
	        for($i = 1 ; $i<=25 ; $i++){
	            $html .= "<script> $('.ER_".$iddemande." #champ".$i."').val('".$resultat['champ'.$i]."'); </script>";
	        }
	        $html .= "<script> $('.ER_".$iddemande." #type_materiel_nfs').val('".str_replace( "'", "\'",$resultat['type_materiel'] )."'); </script>";
	        $html .= "<script> $('.ER_".$iddemande." #commentaire_hemogramme').val('".str_replace( "'", "\'", $resultat['commentaire'] )."'); </script>";
	    }
	    
	    return $html;
	}
	
	protected function getResultatsNfsTR($iddemande){
		$resultat = $this->getResultatDemandeAnalyseTable()->getValeursNfs($iddemande);
		$html ="";
		if($resultat){
			for($i = 1 ; $i<=25 ; $i++){
				$html .= "<script> $('.ER_".$iddemande." #champ".$i."').val('".$resultat['champ'.$i]."'); </script>";
			}
			$html .= "<script> $('.ER_".$iddemande." #type_materiel_nfs').val('".str_replace( "'", "\'",$resultat['type_materiel'] )."'); </script>";
			$html .= "<script> $('.ER_".$iddemande." #commentaire_hemogramme').val('".str_replace( "'", "\'", $resultat['commentaire'] )."'); </script>";
		}
		 
		return $html;
	}
	
	protected function getResultatsGsrhGroupage($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursGsrhGroupage($iddemande);
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script> 
	        	$('#type_materiel_gsrh_groupage').val('".str_replace( "'", "\'",$resultat['type_materiel'])."');	
	            $('#groupe').val('".$resultat['groupe']."'); 
	            $('#rhesus').val('".$resultat['rhesus']."');
	        </script>";
	    }
	    return $html;
	}
	
	protected function getResultatsRechercheAntigene($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursRechercheAntigene($iddemande);
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script>
	            $('#type_materiel_recherche_antigene').val('".str_replace( "'", "\'",$resultat['type_materiel'])."');		
	            $('#antigene_d_faible').val('".$resultat['antigene_d_faible']."');
	            $('#conclusion_antigene_d_faible').val('".str_replace( "'", "\'",$resultat['conclusion_antigene_d_faible'])."');
	        </script>";
	    }
	    return $html;
	}
	
	protected function getResultatsTestCombsDirect($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursTestCombsDirect($iddemande);
	    $html ="<script>$('.ER_".$iddemande." #test_combs_direct').attr('onchange', 'getTestCombsDirect(this.value,".$iddemande.")');</script>";
	    if($resultat){
	        $html .=
	        "<script>
	        	$('.ER_".$iddemande." #type_materiel_test_combs_direct').val('".str_replace( "'", "\'",$resultat['type_materiel'])."');	
	            $('.ER_".$iddemande." #test_combs_direct').val('".$resultat['valeur']."');
	            if('".$resultat['valeur']."' == 'Positif'){ 
	                setTimeout(function(){ 
	                   $('.ER_".$iddemande." .titre_combs_direct').toggle(true); 
	                   $('.ER_".$iddemande." #titre_combs_direct').val('".$resultat['titre']."'); 
	                }); 
	            }
	        </script>";
	    }
	    return $html;
	}
	
	protected function getResultatsTestCombsIndirect($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursTestCombsIndirect($iddemande);
	    $html ="<script>$('#test_combs_indirect_1').attr('onchange', 'getTestCombsIndirectBlocTitre(1)'); </script>";

	    if($resultat){
	    	$html .="<script> var time = 0; setTimeout(function(){ $('#type_materiel_test_combs_indirect').val('".str_replace( "'", "\'", $resultat[0]['type_materiel'])."'); },50); </script>";
	    	
	    	for($i = 0 ; $i < count($resultat) ; $i++){
	    		if($i > 0){
	    			$html .= "<script> time = time+10; setTimeout(function(){ $('#test_combs_indirect_plus').trigger('click'); }, time); </script>";
	    		}
	    		
	    		$html .=
	    		"<script>
	              setTimeout(function(){
	                $('#test_combs_indirect_".($i+1)."').val('".$resultat[$i]['valeur']."');
	                $('#titre_combs_indirect_".($i+1)."').val('".$resultat[$i]['titre']."');
              		$('#titre_combs_temperature_".($i+1)."').val('".$resultat[$i]['temperature']."');
	              }, time);
	    	    </script>";
	    		
	    			$html .= "<script>setTimeout(function(){ $('#test_combs_indirect_".($i+1)."').trigger('change'); }, time)</script>";
	    	}
	    	$html .="<script> setTimeout(function(){ $('#commentaire_test_combs_indirect').val('".str_replace( "'", "\'", $resultat[0]['commentaire'])."'); },100); </script>";
	    
	    }
	    
	    return $html;
	}
	
	protected function getResultatsTestCompatibilite($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursTestCompatibilite($iddemande);
	    $html ="<script>$('.ER_".$iddemande." #test_compatibilite').attr('onchange', 'getTestCompatibilite(this.value,".$iddemande.")');</script>";
	    if($resultat){
	        $html .=
	        "<script>
	        	$('.ER_".$iddemande." #type_materiel_test_compatibilite').val('".str_replace( "'", "\'",$resultat['type_materiel'])."');		
	            $('.ER_".$iddemande." #test_compatibilite').val('".$resultat['valeur']."');
	            if('".$resultat['valeur']."' == 'Compatible'){
	              setTimeout(function(){
	                 $('.ER_".$iddemande." .titre_test_compatibilite').toggle(true); 
	                 $('.ER_".$iddemande." #titre_test_compatibilite').val('".$resultat['poche']."'); 
	              }); 
	            }
	        </script>";
	    }
	    return $html;
	}
	
	protected function getResultatsVitesseSedimentation($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursVitesseSedimentation($iddemande);
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script>
	        	$('#type_materiel_vitesse_sedimentation').val('".str_replace( "'", "\'",$resultat['type_materiel'])."');	
	            $('#vitesse_sedimentation').val('".$resultat['valeur1']."');
	            $('#vitesse_sedimentation_2').val('".$resultat['valeur2']."');		
	        </script>";
	    }
	    return $html;
	}
	
	protected function getResultatsTestDemmel($iddemande){
		$resultat = $this->getResultatDemandeAnalyseTable()->getValeursTestDemmel($iddemande);
		$html ="";
		if($resultat){
			$html .=
			"<script>
	        	$('#type_materiel_test_demmel').val('".str_replace( "'", "\'",$resultat['type_materiel'])."');
	            $('#test_demmel').val('".$resultat['valeur']."');
	        </script>";
		}
		return $html;
	}
	
	protected function getResultatsTauxReticulocyte($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursTauxReticulocyte($iddemande);
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script>
   	        	$('#type_materiel_taux_reticulocytes').val('".str_replace( "'", "\'",$resultat['type_materiel'])."');
	            $('#taux_reticulocyte').val('".$resultat['taux_reticulocyte']."');
	        </script>";
	    }
	    return $html;
	}
	
	protected function getResultatsGoutteEpaisse($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursGoutteEpaisse($iddemande);
	    $html ="<script> $('.ER_".$iddemande." #goutte_epaisse').attr('onchange','getDensiteGoutteEpaisse(this.value,".$iddemande.")');</script>";
	    if($resultat){
	        $html .=
	        "<script>
 	        	$('.ER_".$iddemande." #type_materiel_goutte_epaisse').val('".str_replace( "'", "\'",$resultat['type_materiel'])."');
	            $('.ER_".$iddemande." #goutte_epaisse').val('".$resultat['goutte_epaisse']."');
	            if('".$resultat['goutte_epaisse']."' == 'Positif'){ $('.ER_".$iddemande." #goutte_epaisse_positif').toggle(true); $('.ER_".$iddemande." #densite_parasitaire').val('".$resultat['densite_parasitaire']."'); }
	            $('.ER_".$iddemande." #commentaire_goutte_epaisse').val('".str_replace( "'", "\'",$resultat['commentaire_goutte_epaisse'])."');		
	        </script>";
	    }
	    return $html;
	}
	
	protected function getResultatsTpInr($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursTpInr($iddemande);
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script>
	        	$('#type_materiel_tp_inr').val('".str_replace( "'", "\'",$resultat['type_materiel'])."');	
	            $('#temps_quick_temoin').val('".$resultat['temps_quick_temoin']."');
	            $('#temps_quick_patient').val('".$resultat['temps_quick_patient']."');
	            $('#taux_prothrombine_patient').val('".$resultat['taux_prothrombine_patient']."');
	            $('#inr_patient').val('".$resultat['inr_patient']."');
	        </script>";
	    }
	    return $html;
	}
	
	
	protected function getResultatsTca($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursTca($iddemande);
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script>
	        	$('#type_materiel_tca').val('".str_replace( "'", "\'",$resultat['type_materiel'])."');		
	            $('#tca_patient').val('".$resultat['tca_patient']."');
	            $('#temoin_patient').val('".$resultat['temoin_patient']."');
	        </script>";
	    }
	    return $html;
	}
	
	
	protected function getResultatsFibrinemie($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursFibrinemie($iddemande);
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script>
	        	$('#type_materiel_fibrinemie').val('".str_replace( "'", "\'",$resultat['type_materiel'])."');		
	            $('#fibrinemie').val('".$resultat['fibrinemie']."');
	        </script>";
	    }
	    return $html;
	}
	
	protected function getResultatsTempsSaignement($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursTempsSaignement($iddemande);
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script>
	        	$('#type_materiel_temps_saignement').val('".str_replace( "'", "\'",$resultat['type_materiel'])."');		
	            $('#temps_saignement').val('".$resultat['temps_saignement']."');
	        </script>";
	    }
	    return $html;
	}

	protected function getResultatsFacteur8($iddemande){
		$resultat = $this->getResultatDemandeAnalyseTable()->getValeursFacteur8($iddemande);
		$html ="";
		if($resultat){
			$html .=
			"<script>
	            $('#type_materiel_facteur_8').val('".str_replace( "'", "\'", $resultat['type_materiel'])."');
	    	    $('#facteur_8').val('".$resultat['facteur_8']."');
	    	 </script>";
		}
		return $html;
	}
	
	protected function getResultatsFacteur9($iddemande){
		$resultat = $this->getResultatDemandeAnalyseTable()->getValeursFacteur9($iddemande);
		$html ="";
		if($resultat){
			$html .=
			"<script>
	            $('#type_materiel_facteur_9').val('".str_replace( "'", "\'", $resultat['type_materiel'])."');
	    	    $('#facteur_9').val('".$resultat['facteur_9']."');
	    	 </script>";
		}
		return $html;
	}
	
	protected function getResultatsDDimeres($iddemande){
		$resultat = $this->getResultatDemandeAnalyseTable()->getValeursDDimeres($iddemande);
		$html ="";
		if($resultat){
			$html .=
			"<script>
	            $('#type_materiel_dimeres').val('".str_replace( "'", "\'", $resultat['type_materiel'])."');
	    	    $('#d_dimeres').val('".$resultat['d_dimeres']."');
	    	 </script>";
		}
		return $html;
	}
	
	protected function getResultatsGlycemie($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursGlycemie($iddemande);
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script>
	            $('#type_materiel_glycemie').val('".str_replace( "'", "\'", $resultat['type_materiel'])."');
	            $('#glycemie_1').val('".$resultat['glycemie_1']."');
     	        $('#glycemie_2').val('".$resultat['glycemie_2']."');
	        </script>";
	    }
	    return $html;
	}
	
	protected function getResultatsCreatininemie($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursCreatininemie($iddemande);
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script>
                $('#type_materiel_creatininemie').val('".str_replace( "'", "\'", $resultat['type_materiel'])."');
	            $('#creatininemie').val('".$resultat['creatininemie']."');
	        </script>";
	    }
	    return $html;
	}
	
	protected function getResultatsAzotemie($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursAzotemie($iddemande);
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script>
	        	$('#type_materiel_azotemie').val('".str_replace( "'", "\'", $resultat['type_materiel'])."');	
	            $('#uree_sanguine').val('".$resultat['valeur']."');
	            $('#uree_sanguine_mmol').val('".$resultat['valeur_mmol']."');
	        </script>";
	    }
	    return $html;
	}
	
	protected function getResultatsAcideUrique($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursAcideUrique($iddemande);
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script>
        		$('#type_materiel_acide_urique').val('".str_replace( "'", "\'", $resultat['type_materiel'])."');
	            $('#acide_urique').val('".$resultat['acide_urique']."');
	            $('#acide_urique_umol').val('".$resultat['acide_urique_umol']."');
	        </script>";
	    }
	    return $html;
	}
	
	protected function getResultatsCholesterolTotal($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursCholesterolTotal($iddemande);
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script>
	            $('.ER_".$iddemande." #type_materiel_cholesterol_total').val('".str_replace( "'", "\'", $resultat['type_materiel'])."');	
	            $('.ER_".$iddemande." #cholesterol_total_1').val('".$resultat['cholesterol_total_1']."');
                $('.ER_".$iddemande." #cholesterol_total_2').val('".$resultat['cholesterol_total_2']."');
	        </script>";
	    }
	    return $html;
	}
	
	protected function getResultatsTriglycerides($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursTriglycerides($iddemande);
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script>
	        	$('.ER_".$iddemande." #type_materiel_triglycerides').val('".str_replace( "'", "\'", $resultat['type_materiel'])."');	
	    	    $('.ER_".$iddemande." #triglycerides_1').val('".$resultat['triglycerides_1']."');
	    	    $('.ER_".$iddemande." #triglycerides_2').val('".$resultat['triglycerides_2']."');
	    	</script>";
	    }
	    return $html;
	}
	
	protected function getResultatsCholesterolHDL($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursCholesterolHDL($iddemande);
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script>
	        	$('.ER_".$iddemande." #type_materiel_cholesterol_HDL').val('".str_replace( "'", "\'", $resultat['type_materiel'])."');	
	    	    $('.ER_".$iddemande." #cholesterol_HDL_1').val('".$resultat['cholesterol_HDL_1']."');
	    	    $('.ER_".$iddemande." #cholesterol_HDL_2').val('".$resultat['cholesterol_HDL_2']."');
	    	</script>";
	    }
	    return $html;
	}
	
	protected function getResultatsCholesterolLDL($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursCholesterolLDL($iddemande);
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script>
	        	$('.ER_".$iddemande." #type_materiel_cholesterol_LDL').val('".str_replace( "'", "\'", $resultat['type_materiel'])."');	
	    	    $('.ER_".$iddemande." #cholesterol_LDL_1').val('".$resultat['cholesterol_LDL_1']."');
	    	    $('.ER_".$iddemande." #cholesterol_LDL_2').val('".$resultat['cholesterol_LDL_2']."');
	    	 </script>";
	    }
	    return $html;
	}
	
	protected function getResultatsIonogramme($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursIonogramme($iddemande);
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script>
	            $('#type_materiel_ionogramme').val('".str_replace( "'", "\'", $resultat['type_materiel'])."');			
	    	    $('#sodium_sanguin').val('".$resultat['sodium_sanguin']."');
	    	    $('#potassium_sanguin').val('".$resultat['potassium_sanguin']."');
	    	    $('#chlore_sanguin').val('".$resultat['chlore_sanguin']."');
	    	 </script>";
	    }
	    return $html;
	}
	
	protected function getResultatsCalcemie($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursCalcemie($iddemande);
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script>
   	            $('#type_materiel_calcemie').val('".str_replace( "'", "\'", $resultat['type_materiel'])."');			
	    	    $('#calcemie').val('".$resultat['calcemie']."');
	    	    $('#calcemie_mmol').val('".$resultat['calcemie_mmol']."');
	    	 </script>";
	    }
	    return $html;
	}
	
	protected function getResultatsMagnesemie($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursMagnesemie($iddemande);
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script>
   	            $('#type_materiel_magnesemie').val('".str_replace( "'", "\'", $resultat['type_materiel'])."');			
	    	    $('#magnesemie').val('".$resultat['magnesemie']."');
	    	 </script>";
	    }
	    return $html;
	}
	
	protected function getResultatsPhosphoremie($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursPhosphoremie($iddemande);
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script>
   	            $('#type_materiel_phosphoremie').val('".str_replace( "'", "\'", $resultat['type_materiel'])."');			
	    	    $('#phosphoremie').val('".$resultat['phosphoremie']."');
	    	    $('#phosphoremie_mmol').val('".$resultat['phosphoremie_mmol']."');
	    	 </script>";
	    }
	    return $html;
	}

	protected function getResultatsTgoAsat($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursTgoAsat($iddemande);
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script>
	            $('.ER_".$iddemande." #type_materiel_tgo_asat').val('".str_replace( "'", "\'", $resultat['type_materiel'])."');
	    	    $('.ER_".$iddemande." #tgo_asat').val('".$resultat['tgo_asat']."');
	    	 </script>";
	    }
	    return $html;
	}
	
	protected function getResultatsTgpAlat($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursTgpAlat($iddemande);
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script>
	            $('.ER_".$iddemande." #type_materiel_tgp_alat').val('".str_replace( "'", "\'", $resultat['type_materiel'])."');
	    	    $('.ER_".$iddemande." #tgp_alat').val('".$resultat['tgp_alat']."');
	    	 </script>";
	    }
	    return $html;
	}
	
	protected function getResultatsPhosphatageAlcaline($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursPhosphatageAlcaline($iddemande);
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script>
	        	$('#type_materiel_phosphatage_alcaline').val('".str_replace( "'", "\'", $resultat['type_materiel'])."');	
	    	    $('#phosphatage_alcaline').val('".$resultat['valeur']."');
	    	 </script>";
	    }
	    return $html;
	}
	
	protected function getResultatsGamaGtYgt($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursGamaGtYgt($iddemande);
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script>
	        	$('#type_materiel_gama_gt_ygt').val('".str_replace( "'", "\'", $resultat['type_materiel'])."');	
	    	    $('#gama_gt').val('".$resultat['valeur']."');
	    	 </script>";
	    }
	    return $html;
	}
	
	protected function getResultatsFerSerique($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursFerSerique($iddemande);
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script>
	        	$('#type_materiel_fer_serique').val('".str_replace( "'", "\'", $resultat['type_materiel'])."');	
	    	    $('#fer_serique_ug').val('".$resultat['valeur_ug']."');
	    	    $('#fer_serique_umol').val('".$resultat['valeur_umol']."');
	    	 </script>";
	    }
	    return $html;
	}
	
	protected function getResultatsTypageHemoglobine($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursTypageHemoglobine($iddemande);
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script>
	    	    $('#type_materiel_typage_hemoglobine').val('".str_replace( "'", "\'", $resultat['type_materiel'])."');
	    	    $('#typage_hemoglobine').val('".$resultat['valeur']."');
	    	    $('#autre_typage_hemoglobine').val('".$resultat['valeur_Hbarts']."');
	    	 </script>";
	    }
	    return $html;
	}
	
	protected function getResultatsLDH($iddemande){
		$resultat = $this->getResultatDemandeAnalyseTable()->getValeursLDH($iddemande);
		$html ="";
		if($resultat){
			$html .=
			"<script>
	    	    $('#type_materiel_ldh').val('".str_replace( "'", "\'", $resultat['type_materiel'])."');
	    	    $('#valeur_ldh').val('".$resultat['valeur_ldh']."');
	    	 </script>";
		}
		return $html;
	}
	
	protected function getResultatsLipidesTotaux($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursLipidesTotaux($iddemande);
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script>
	            $('#type_materiel_lipides_totaux').val('".str_replace( "'", "\'", $resultat['type_materiel'])."');
	    	    $('#lipides_totaux').val('".$resultat['lipides_totaux']."');
	    	 </script>";
	    }
	    return $html;
	}
	
	protected function getResultatsFerritinine($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursFerritinine($iddemande);
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script>
	            $('#type_materiel_ferritinine').val('".str_replace( "'", "\'", $resultat['type_materiel'])."');
	    	    $('#ferritinine').val('".$resultat['ferritinine']."');
	    	 </script>";
	    }
	    return $html;
	}
	
	protected function getResultatsBilirubineTotaleDirecte($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursBilirubineTotaleDirecte($iddemande);
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script>
	            $('#type_materiel_bilirubine_totale_directe').val('".str_replace( "'", "\'", $resultat['type_materiel'])."');
	    	    $('#bilirubine_totale').val('".$resultat['bilirubine_totale']."');
    	        $('#bilirubine_totale_auto').val('".$resultat['bilirubine_totale_auto']."');
   	            $('#bilirubine_directe').val('".$resultat['bilirubine_directe']."');
   	            $('#bilirubine_directe_auto').val('".$resultat['bilirubine_directe_auto']."');
   	            $('#bilirubine_indirecte').val('".$resultat['bilirubine_indirecte']."');
   	            $('#bilirubine_indirecte_auto').val('".$resultat['bilirubine_indirecte_auto']."');
	    	 </script>";
	    }
	    return $html;
	}
	
	protected function getResultatsHemoglobineGlyqueeHBAC($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursHemoglobineGlyqueeHBAC($iddemande);
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script>
	            $('#type_materiel_hemo_gly_hbac').val('".str_replace( "'", "\'", $resultat['type_materiel'])."');
	    	    $('#hemoglobine_glyquee_hbac').val('".$resultat['hemoglobine_glyquee_hbac']."');
    	        $('#hemoglobine_glyquee_hbac_mmol').val('".$resultat['hemoglobine_glyquee_hbac_mmol']."');
	    	 </script>";
	    }
	    return $html;
	}
	

	protected function getResultatsElectrophoreseHemoglobine($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursElectrophoreseHemoglobine($iddemande);
	    $html ="";
	    if($resultat){
	        $html .="<script> setTimeout(function(){ $('#type_materiel_electro_hemo').val('".str_replace( "'", "\'", $resultat[0]['type_materiel'])."'); },50); </script>";
	        for($i = 0 ; $i < count($resultat) ; $i++){
	        	if($i > 0){
	        		$html .= "<script> setTimeout(function(){ $('#electro_hemo_plus').trigger('click'); }, 50); </script>";
	        	}
	            $html .=
	            "<script>
	              setTimeout(function(){ 
	                $('#electro_hemo_label_".($i+1)."').val('".$resultat[$i]['libelle']."');
	                $('#electro_hemo_valeur_".($i+1)."').val('".$resultat[$i]['valeur']."');
	              }, 50);
	    	    </script>";
	        }
	        $html .="<script> setTimeout(function(){ $('#conclusion_electro_hemo_valeur').val('".str_replace( "'", "\'", $resultat[0]['conclusion'])."'); },50); </script>";
	         
	    }
	    return $html;
	}
	
	protected function getResultatsElectrophoreseProteine($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursElectrophoreseProteines($iddemande);
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script>
	            $('#type_materiel_electro_proteine').val('".str_replace( "'", "\'", $resultat['type_materiel'])."');
	    	    $('#albumine').val('".$resultat['albumine']."');
    	        $('#albumine_abs').val('".$resultat['albumine_abs']."');
    	        $('#alpha_1').val('".$resultat['alpha_1']."');
    	        $('#alpha_1_abs').val('".$resultat['alpha_1_abs']."');
    	        $('#alpha_2').val('".$resultat['alpha_2']."');
    	        $('#alpha_2_abs').val('".$resultat['alpha_2_abs']."');
    	        $('#beta_1').val('".$resultat['beta_1']."');
    	        $('#beta_1_abs').val('".$resultat['beta_1_abs']."');
    	        $('#beta_2').val('".$resultat['beta_2']."');
    	        $('#beta_2_abs').val('".$resultat['beta_2_abs']."');
    	        $('#gamma').val('".$resultat['gamma']."');
    	        $('#gamma_abs').val('".$resultat['gamma_abs']."');
    	        $('#proteine_totale').val('".$resultat['proteine_totale']."');
    	        $('#commentaire_electrophorese_proteine').val('".str_replace( "'", "\'", $resultat['commentaire'])."');
	    	 </script>";
	    }
	    return $html;
	}
	
	protected function getResultatsAlbuminemie($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursAlbuminemie($iddemande);
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script>
	            $('#type_materiel_albuminemie').val('".str_replace( "'", "\'", $resultat['type_materiel'])."');
	    	    $('#albuminemie').val('".$resultat['albuminemie']."');
	    	    $('#albuminemie_umol').val('".$resultat['albuminemie_umol']."');
	    	 </script>";
	    }
	    return $html;
	}
	
	protected function getResultatsAlbumineUrinaire($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursAlbumineUrinaire($iddemande);
	    $html  ="<script>$('.ER_".$iddemande." #albumine_urinaire').attr('onchange', 'getAlbumineUrinaireVal(this.value,".$iddemande.")');</script>";
	    $html .="<script>$('.ER_".$iddemande." #sucre_urinaire').attr('onchange', 'getSucreUrinaireVal(this.value,".$iddemande.")');</script>";
	    $html .="<script>$('.ER_".$iddemande." #corps_cetonique_urinaire').attr('onchange', 'getCorpsCetoniqueUrinaireVal(this.value,".$iddemande.")');</script>";

	    
	    if($resultat){
	        $html .=
	        "<script>
	            $('.ER_".$iddemande." #type_materiel_albumine_urinaire').val('".str_replace( "'", "\'", $resultat['type_materiel'])."');
	    	    $('.ER_".$iddemande." #albumine_urinaire').val('".$resultat['albumine_urinaire']."');
	    	    $('.ER_".$iddemande." #sucre_urinaire').val('".$resultat['sucre_urinaire']."');
	    	    $('.ER_".$iddemande." #corps_cetonique_urinaire').val('".$resultat['corps_cetonique_urinaire']."');
	    	 </script>";
	        
	        if($resultat['albumine_urinaire'] == 'positif'){
	        	$html .=
	        	"<script>
	    	        $('.ER_".$iddemande." #albumine_urinaire_degres').val('".$resultat['albumine_urinaire_degres']."').toggle(true);
	    	     </script>";
	        	
	        }
	        
	        if($resultat['sucre_urinaire'] == 'positif'){
	            $html .=
	            "<script>
	    	        $('.ER_".$iddemande." #sucre_urinaire_degres').val('".$resultat['sucre_urinaire_degres']."').toggle(true);
	    	     </script>";
	        
	        }
	        
	        if($resultat['corps_cetonique_urinaire'] == 'positif'){
	            $html .=
	            "<script>
	    	        $('.ER_".$iddemande." #corps_cetonique_urinaire_degres').val('".$resultat['corps_cetonique_urinaire_degres']."').toggle(true);
	    	     </script>";
	        
	        }
	    }
	    
	    return $html;
	}
	

	protected function getResultatsProtidemie($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursProtidemie($iddemande);
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script>
	            $('#type_materiel_protidemie').val('".str_replace( "'", "\'", $resultat['type_materiel'])."');
	    	    $('#protidemie').val('".$resultat['protidemie']."');
	    	 </script>";
	    }
	    return $html;
	}

	protected function getResultatsProteinurie($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursProteinurie($iddemande);
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script>
	            $('#type_materiel_proteinurie').val('".str_replace( "'", "\'", $resultat['type_materiel'])."');
	    	    $('#proteinurie_1').val('".$resultat['proteinurie_1']."');
	    	    $('#proteinurie_2').val('".$resultat['proteinurie_2']."');
	    	    $('#proteinurie_g24h').val('".$resultat['proteinurie_g24h']."');				
	    	 </script>";
	    }
	    return $html;
	}
	
	protected function getResultatsHlmCompteDaddis($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursHlmCompteDaddis($iddemande);
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script>
	            $('#type_materiel_hlm_compte_daddis').val('".str_replace( "'", "\'", $resultat['type_materiel'])."');
	    	    $('#hematies_hlm').val('".$resultat['hematies_hlm']."');
    	        $('#leucocytes_hlm').val('".$resultat['leucocytes_hlm']."');
    	        $('#commentaire_hlm_compte_daddis').val('".$resultat['commentaire_hlm_compte_daddis']."');
	    	 </script>";
	    }
	    return $html;
	}
	
	protected function getResultatsBetaHcgPlasmatique($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursBetaHcgPlasmatique($iddemande);
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script>
	            $('#type_materiel_beta_hcg').val('".str_replace( "'", "\'", $resultat['type_materiel'])."');
	    	    $('#beta_hcg_plasmatique').val('".$resultat['beta_hcg_plasmatique']."');
	    	 </script>";
	    }
	    return $html;
	}
	
	protected function getResultatsPsa($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursPsa($iddemande);
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script>
	            $('#type_materiel_psa').val('".str_replace( "'", "\'", $resultat['type_materiel'])."');
	    	    $('#psa').val('".$resultat['psa']."');
	    	    $('#psa_qualitatif').val('".$resultat['psa_qualitatif']."');
	    	 </script>";
	    }
	    return $html;
	}
	
	protected function getResultatsCrp($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursCrp($iddemande);
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script>
	            $('#type_materiel_crp').val('".str_replace( "'", "\'", $resultat['type_materiel'])."');
	    	    $('#optionResultatCrp').val('".$resultat['optionResultatCrp']."');
	    	    $('#crpValeurResultat').val('".$resultat['crpValeurResultat']."');
	    	    $('#optionResultatCrp').trigger('change');		
	    	 </script>";
	    }
	    return $html;
	}
	
	protected function getResultatsFacteursRhumatoides($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursFacteursRhumatoides($iddemande);
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script>
	            $('#type_materiel_facteurs_rhumatoides').val('".str_replace( "'", "\'", $resultat['type_materiel'])."');
	    	    $('#facteurs_rhumatoides').val('".$resultat['facteurs_rhumatoides']."');
   	    		$('#facteurs_rhumatoides_titre').val('".$resultat['facteurs_rhumatoides_titre']."');
	    	 </script>";
	    }
	    return $html;
	}
	
	protected function getResultatsRfWaalerRose($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursRfWaalerRose($iddemande);
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script>
	            $('#type_materiel_rf_waaler_rose').val('".str_replace( "'", "\'", $resultat['type_materiel'])."');
	    	    $('#rf_waaler_rose').val('".$resultat['rf_waaler_rose']."');
	    	    $('#rf_waaler_rose_titre').val('".$resultat['rf_waaler_rose_titre']."');
	    	 </script>";
	    }
	    return $html;
	}
	
	protected function getResultatsToxoplasmose($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursToxoplasmose($iddemande);
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script>
	            $('#type_materiel_toxoplasmose').val('".str_replace( "'", "\'", $resultat['type_materiel'])."');
	    	    $('#toxoplasmose_igm').val('".$resultat['toxoplasmose_igm']."');
	    	    $('#toxoplasmose_igm_titre').val('".$resultat['toxoplasmose_igm_titre']."');
	    	    $('#toxoplasmose_igg').val('".$resultat['toxoplasmose_igg']."');
	    	    $('#toxoplasmose_igg_titre').val('".$resultat['toxoplasmose_igg_titre']."');
	    	    $('#toxoplasmose_commentaire').val('".$resultat['toxoplasmose_commentaire']."');
	    	 </script>";
	    }
	    return $html;
	}

	protected function getResultatsRubeole($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursRubeole($iddemande);
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script>
	            $('#type_materiel_rubeole').val('".str_replace( "'", "\'", $resultat['type_materiel'])."');
	    	    $('#rubeole_igm').val('".$resultat['rubeole_igm']."');
	    	    $('#rubeole_igm_titre').val('".$resultat['rubeole_igm_titre']."');
	    	    $('#rubeole_igg').val('".$resultat['rubeole_igg']."');
	    	    $('#rubeole_igg_titre').val('".$resultat['rubeole_igg_titre']."');
	    	    $('#rubeole_commentaire').val('".$resultat['rubeole_commentaire']."');
	    	 </script>";
	    }
	    return $html;
	}
	
	protected function getResultatsCulotUrinaire($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursCulotUrinaire($iddemande);
	    $html ="";
	    
	    if($resultat){
	    	$html .="<script> setTimeout(function(){ $('#type_materiel_culot_urinaire').val('".str_replace( "'", "\'", $resultat[0]['type_materiel'])."'); },50); </script>";
	    	for($i = 0 ; $i < count($resultat) ; $i++){
	    		if($i > 0){
	    			$html .= "<script> setTimeout(function(){ $('#culot_urinaire_plus').trigger('click'); }, 50); </script>";
	    		}
	    		
	    		if(in_array($resultat[$i]['culot_urinaire_1'], array(1,2)) ){
	    			$html .=
	    			"<script>
	                  setTimeout(function(){
	                    $('#culot_urinaire_ligne_".($i+1)." .listeSelect select').val('".$resultat[$i]['culot_urinaire_1']."').trigger('onchange');
	                    $('#culot_urinaire_ligne_".($i+1)." .emplaceListeElemtsCUSelect input').val('".$resultat[$i]['culot_urinaire_2']."');
	                  }, 50);
	    	         </script>";
	    		}else{
	    			$html .=
	    			"<script>
	                  setTimeout(function(){
	                    $('#culot_urinaire_ligne_".($i+1)." .listeSelect select').val('".$resultat[$i]['culot_urinaire_1']."').trigger('onchange');
	                    $('#culot_urinaire_ligne_".($i+1)." .emplaceListeElemtsCUSelect select').val('".$resultat[$i]['culot_urinaire_2']."');
	                  }, 50);
	    	         </script>";
	    		}
	    		
	    	}
	    	$html .="<script> setTimeout(function(){ $('#conclusion_culot_urinaire_valeur').val('".str_replace( "'", "\'", $resultat[0]['conclusion'])."'); },50); </script>";
	    
	    }
	    
	    return $html;
	}
	
	protected function getResultatsSerologieChlamydiae($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursSerologieChlamydiae($iddemande);
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script>
	            $('#type_materiel_serologie_chlamydiae').val('".str_replace( "'", "\'", $resultat['type_materiel'])."');
	    	    $('#serologie_chlamydiae').val('".$resultat['serologie_chlamydiae']."');
	    	 </script>";
	    }
	    return $html;
	}
	
	protected function getResultatsSerologieSyphilitique($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursSerologieSyphilitique($iddemande);
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script>
	            $('#type_materiel_serologie_syphilitique').val('".str_replace( "'", "\'", $resultat['type_materiel'])."');
	    	    $('#serologie_syphilitique_rpr').val('".$resultat['serologie_syphilitique_rpr']."');
	    	    $('#serologie_syphilitique_tpha').val('".$resultat['serologie_syphilitique_tpha']."');
	    	    $('#serologie_syphilitique_tpha_titre').val('".$resultat['serologie_syphilitique_tpha_titre']."');				
	    	 </script>";
	    }
	    return $html;
	}
	
	protected function getResultatsAslo($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursAslo($iddemande);
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script>
	            $('#type_materiel_aslo').val('".str_replace( "'", "\'", $resultat['type_materiel'])."');
	    	    $('#aslo_select').val('".$resultat['aslo']."');
   	    		$('#aslo_titre').val('".$resultat['titre']."');
	    	 </script>";
	    }
	    return $html;
	}
	
	protected function getResultatsWidal($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursWidal($iddemande);
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script>
	            $('#type_materiel_widal').val('".str_replace( "'", "\'", $resultat['type_materiel'])."');
	    	    $('#widal_to').val('".$resultat['widal_to']."'); $('#widal_titre_to').val('".$resultat['widal_titre_to']."');
	    	    $('#widal_th').val('".$resultat['widal_th']."'); $('#widal_titre_th').val('".$resultat['widal_titre_th']."');
	    	    $('#widal_ao').val('".$resultat['widal_ao']."'); $('#widal_titre_ao').val('".$resultat['widal_titre_ao']."');
	    	    $('#widal_ah').val('".$resultat['widal_ah']."'); $('#widal_titre_ah').val('".$resultat['widal_titre_ah']."');
	    	    $('#widal_bo').val('".$resultat['widal_bo']."'); $('#widal_titre_bo').val('".$resultat['widal_titre_bo']."');
        	    $('#widal_bh').val('".$resultat['widal_bh']."'); $('#widal_titre_bh').val('".$resultat['widal_titre_bh']."');
        	    $('#widal_co').val('".$resultat['widal_co']."'); $('#widal_titre_co').val('".$resultat['widal_titre_co']."');
        	    $('#widal_ch').val('".$resultat['widal_ch']."'); $('#widal_titre_ch').val('".$resultat['widal_titre_ch']."');
	    	 </script>";
	    }
	    return $html;
	}
	
	protected function getResultatsAgHbs($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursAgHbs($iddemande);
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script>
	            $('#type_materiel_ag_hbs').val('".str_replace( "'", "\'", $resultat['type_materiel'])."');
	    	    $('#ag_hbs').val('".$resultat['ag_hbs']."');
	    	 </script>";
	    }
	    return $html;
	}
	
	protected function getResultatsHIV($iddemande){
		$resultat = $this->getResultatDemandeAnalyseTable()->getValeursHIV($iddemande);
		$html ="";
		if($resultat){
			$html .=
			"<script>
	            $('#type_materiel_hiv').val('".str_replace( "'", "\'", $resultat['type_materiel'])."');
	    	    $('#hiv').val('".$resultat['hiv']."');
	    	    $('#hiv_typage').val('".$resultat['hiv_typage']."');
	    	 </script>";
		}
		return $html;
	}
	
	protected function getResultatsPV($iddemande){
	    
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursPV($iddemande);
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script>
	            $('#type_materiel_pv').val('".str_replace( "'", "\'", $resultat['type_materiel'])."');
	    	    $('#aspect_pertes_abondance_pv').val('".$resultat['aspect_pertes_abondance']."');
	    	    $('#aspect_pertes_couleurs_pv').val('".$resultat['aspect_pertes_couleurs']."');
	    	    $('#aspect_pertes_odeurs_pv').val('".$resultat['aspect_pertes_odeurs']."');
	    	    $('#aspect_organe_pv').val('".$resultat['aspect_organe_col']."');
	    	        
    	        $('#leucocytes_champ_pv').val('".$resultat['leucocytes_champ']."');
    	        $('#leucocytes_champ_valeur_pv').val('".$resultat['leucocytes_champ_valeur']."');
    	        $('#hematies_champ_pv').val('".$resultat['hematies_champ']."');
    	        $('#hematies_champ_valeur_pv').val('".$resultat['hematies_champ_valeur']."');
    	        $('#cellules_epitheliales_champ_pv').val('".$resultat['cellules_epitheliales']."');
    	        $('#cellules_epitheliales_champ_valeur_pv').val('".$resultat['cellules_epitheliales_champ_valeur']."');    	            
    	        $('#trichomonas_vaginalis_pv').val('".$resultat['trichomonas_vaginalis']."').trigger('change');
    	        $('#levures_pv').val('".$resultat['levures']."');
   	            $('#filaments_myceliens_pv').val('".$resultat['filaments_myceliens']."');
    	        $('#gardnerella_vaginalis_pv').val('".$resultat['gardnerella_vaginalis']."').trigger('change');
    	        $('#mobiluncus_spp_pv').val('".$resultat['mobiluncus_spp']."');
    	        $('#clue_cells_pv').val('".$resultat['clue_cells']."');
    	        $('#lactobacillus_pv').val('".$resultat['lactobacillus']."');
    	        $('#autre_flore_pv').val('".$resultat['autre_flore']."').trigger('change');
    	        $('#flore_pv').val('".$resultat['flore']."').trigger('change');
    	        $('#flore_note_pv').val('".str_replace( "'", "\'", $resultat['flore_note'])."');
    	        $('#culture_pv').val('".$resultat['culture']."').trigger('change');
    	        $('#identification_culture_pv').val('".$resultat['identification_culture']."').trigger('change');
    	        $('#recherche_directe_antigene_chlamydia_pv').val('".$resultat['recherche_directe_antigene_chlamydia']."');
    	        $('#recherche_directe_mycoplasmes_pv').val('".$resultat['recherche_directe_mycoplasmes']."').trigger('change');
                    
    	        $('#commentaire_pv').val('".preg_replace("/(\r\n|\n|\r)/", "\\n", str_replace( "'", "\'", $resultat['commentaire'] ))."');
    	        $('#autres_precisions_commentaire_pv').val('".preg_replace("/(\r\n|\n|\r)/", "\\n", str_replace( "'", "\'", $resultat['precision_commentaire'] ))."');
    	        		

    	        		
    	        		
    	        tabCommentaireSelect = new Array();
	    	 </script>";
	        
	         $tabCommentaire = explode("\\n", preg_replace("/(\r\n|\n|\r)/", "\\n", str_replace( "'", "\'", $resultat['commentaire'])));
	         for($i=0 ; $i<count($tabCommentaire) ; $i++){
	         	$html .="<script> var indexCommentChoicePV = tabCommentaireChoicePV.indexOf('".$tabCommentaire[$i].'\r\n'."'); </script>";
	         	$html .="<script> if(indexCommentChoicePV != -1){ tabCommentaireSelect[indexCommentChoicePV] = 1;}  </script>";
	         }
	        
	         $html .= ($resultat['identification_rdm_positive_choix1'])   ? '<script> $("#identification_rdm_positive_Choix1_pv").trigger("click"); </script>' : '';
	         $html .= ($resultat['identification_rdm_positive_choix2'])   ? '<script> $("#identification_rdm_positive_Choix2_pv").trigger("click"); </script>' : '';
	         
	         $html .= ($resultat['autre_flore_cocci_pos_Choix1'])   ? '<script> $("#autre_flore_cocci_pos_Choix1_pv").trigger("click"); </script>' : '';
	         $html .= ($resultat['autre_flore_cocci_pos_Choix2'])   ? '<script> $("#autre_flore_cocci_pos_Choix2_pv").trigger("click"); </script>' : '';
	         
	    }
	    
	    /**
	     * Recuperer les donnees de l'antibiogramme
	     */
	    
	    if($resultat['identification_culture'] != 0){
	        $resultatAntiBioGramme = $this->getResultatDemandeAnalyseTable()->getValeursAntiBioGramme($iddemande);
	        if($resultatAntiBioGramme){
	            $html .= '<script> /*alert("'.$resultatAntiBioGramme['ampicillineAM'].'");*/';

	            /**
	             * PARTIE B-lactamines
	             */
	            $html .= ($resultatAntiBioGramme['ampicillineAM'])   ? '$("#ampicillineAMABG").val("'.$resultatAntiBioGramme['ampicillineAM'].'"); $("#choixAmpicillineAMABG").trigger("click");' : '';
	            $html .= ($resultatAntiBioGramme['amoxillineAMX'])   ? '$("#amoxillineAMXABG").val("'.$resultatAntiBioGramme['amoxillineAMX'].'"); $("#choixAmoxillineAMXABG").trigger("click");' : '';
	            $html .= ($resultatAntiBioGramme['ticarcillineTIC']) ? '$("#ticarcillineTICABG").val("'.$resultatAntiBioGramme['ticarcillineTIC'].'"); $("#choixTicarcillineTICABG").trigger("click");' : '';
	            $html .= ($resultatAntiBioGramme['piperacillinePIP']) ? '$("#piperacillinePIPABG").val("'.$resultatAntiBioGramme['piperacillinePIP'].'"); $("#choixPiperacillinePIPABG").trigger("click");' : '';
	            $html .= ($resultatAntiBioGramme['acideClavulaniqueAmoxicillineAMC']) ? '$("#acideClavulaniqueAmoxicillineAMCABG").val("'.$resultatAntiBioGramme['acideClavulaniqueAmoxicillineAMC'].'"); $("#choixAcideClavulaniqueAmoxicillineAMCABG").trigger("click");' : '';
	            
	            $html .= ($resultatAntiBioGramme['gentamicineGM']) ? '$("#gentamicineGMABG").val("'.$resultatAntiBioGramme['gentamicineGM'].'"); $("#choixGentamicineGMABG").trigger("click");' : '';
	            $html .= ($resultatAntiBioGramme['ticAcClavTCC']) ? '$("#ticAcClavTCCABG").val("'.$resultatAntiBioGramme['ticAcClavTCC'].'"); $("#choixTicAcClavTCCABG").trigger("click");' : '';
	            $html .= ($resultatAntiBioGramme['ertapenemeETP']) ? '$("#ertapenemeETPABG").val("'.$resultatAntiBioGramme['ertapenemeETP'].'"); $("#choixErtapenemeETPABG").trigger("click");' : '';
	            $html .= ($resultatAntiBioGramme['imipenemeIPM']) ? '$("#imipenemeIPMABG").val("'.$resultatAntiBioGramme['imipenemeIPM'].'"); $("#choixImipenemeIPMABG").trigger("click");' : '';
	            $html .= ($resultatAntiBioGramme['oxacillineOX']) ? '$("#oxacillineOXABG").val("'.$resultatAntiBioGramme['oxacillineOX'].'"); $("#choixOxacillineOXABG").trigger("click");' : '';
	             
	            $html .= ($resultatAntiBioGramme['penicillineP']) ? '$("#penicillinePABG").val("'.$resultatAntiBioGramme['penicillineP'].'"); $("#choixPenicillinePABG").trigger("click");' : '';
	            $html .= ($resultatAntiBioGramme['cefalotineCF']) ? '$("#cefalotineCFABG").val("'.$resultatAntiBioGramme['cefalotineCF'].'"); $("#choixCefalotineCFABG").trigger("click");' : '';
	            $html .= ($resultatAntiBioGramme['cefoxitineFOX']) ? '$("#cefoxitineFOXABG").val("'.$resultatAntiBioGramme['cefoxitineFOX'].'"); $("#choixCefoxitineFOXABG").trigger("click");' : '';
	            $html .= ($resultatAntiBioGramme['piperacillineTazobactamePPT']) ? '$("#piperacillineTazobactamePPTABG").val("'.$resultatAntiBioGramme['piperacillineTazobactamePPT'].'"); $("#choixPiperacillineTazobactamePPTABG").trigger("click");' : '';
	            $html .= ($resultatAntiBioGramme['cefotaximeCTX']) ? '$("#cefotaximeCTXABG").val("'.$resultatAntiBioGramme['cefotaximeCTX'].'"); $("#choixCefotaximeCTXABG").trigger("click");' : '';
	            
	            $html .= ($resultatAntiBioGramme['cefsulodineCFS']) ? '$("#cefsulodineCFSABG").val("'.$resultatAntiBioGramme['cefsulodineCFS'].'"); $("#choixCefsulodineCFSABG").trigger("click");' : '';
	            $html .= ($resultatAntiBioGramme['CFP']) ? '$("#CFPABG").val("'.$resultatAntiBioGramme['CFP'].'"); $("#choixCFPABG").trigger("click");' : '';
	            $html .= ($resultatAntiBioGramme['ceftazidimeCAZ']) ? '$("#ceftazidimeCAZABG").val("'.$resultatAntiBioGramme['ceftazidimeCAZ'].'"); $("#choixCeftazidimeCAZABG").trigger("click");' : '';
	            $html .= ($resultatAntiBioGramme['ceftriaxoneCRO']) ? '$("#ceftriaxoneCROABG").val("'.$resultatAntiBioGramme['ceftriaxoneCRO'].'"); $("#choixCeftriaxoneCROABG").trigger("click");' : '';
	            $html .= ($resultatAntiBioGramme['cefepimeFEP']) ? '$("#cefepimeFEPABG").val("'.$resultatAntiBioGramme['cefepimeFEP'].'"); $("#choixCefepimeFEPABG").trigger("click");' : '';
	            $html .= ($resultatAntiBioGramme['aztreonamATM']) ? '$("#aztreonamATMABG").val("'.$resultatAntiBioGramme['aztreonamATM'].'"); $("#choixAztreonamATMABG").trigger("click");' : '';
	            /**
	             * FIN PARTIE B-lactamines
	             */
	            
	            /**
	             * PARTIE Glycopeptides
	             */
	            $html .= ($resultatAntiBioGramme['vancomycineVA']) ? '$("#vancomycineVAABG").val("'.$resultatAntiBioGramme['vancomycineVA'].'"); $("#choixVancomycineVAABG").trigger("click");' : '';
	            $html .= ($resultatAntiBioGramme['teicoplanine']) ? '$("#teicoplanineABG").val("'.$resultatAntiBioGramme['teicoplanine'].'"); $("#choixTeicoplanineABG").trigger("click");' : '';
	            /**
	             * FIN PARTIE Glycopeptides
	             */

	            
	            /**
	             * PARTIE Produits nitrés
	             */
	            $html .= ($resultatAntiBioGramme['nitrofurane']) ? '$("#nitrofuraneABG").val("'.$resultatAntiBioGramme['nitrofurane'].'"); $("#choixNitrofuraneABG").trigger("click");' : '';
	            $html .= ($resultatAntiBioGramme['metronidazole']) ? '$("#metronidazoleABG").val("'.$resultatAntiBioGramme['metronidazole'].'"); $("#choixMetronidazoleABG").trigger("click");' : '';
	            /**
	             * FIN PARTIE Produits nitrés
	             */

	            /**
	             * PARTIE Sulfamides
	             */
	            $html .= ($resultatAntiBioGramme['trimethoprimeSulfametoxazoleSXT']) ? '$("#trimethoprimeSulfametoxazoleSXTABG").val("'.$resultatAntiBioGramme['trimethoprimeSulfametoxazoleSXT'].'"); $("#choixTrimethoprimeSulfametoxazoleSXTABG").trigger("click");' : '';
	            /**
	             * FIN PARTIE Sulfamides
	             */
	            
	            /**
	             * PARTIE Polymyxine
	             */
	            $html .= ($resultatAntiBioGramme['colistineCS']) ? '$("#colistineCSABG").val("'.$resultatAntiBioGramme['colistineCS'].'"); $("#choixColistineCSABG").trigger("click");' : '';
	            $html .= ($resultatAntiBioGramme['polymicine']) ? '$("#polymicineABG").val("'.$resultatAntiBioGramme['polymicine'].'"); $("#choixPolymicineABG").trigger("click");' : '';
	            /**
	             * FIN PARTIE Polymyxine
	             */
	            
	            /**
	             * PARTIE Aminosides
	             */
	            $html .= ($resultatAntiBioGramme['kanamycineK']) ? '$("#kanamycineKABG").val("'.$resultatAntiBioGramme['kanamycineK'].'"); $("#choixKanamycineKABG").trigger("click");' : '';
	            $html .= ($resultatAntiBioGramme['tobramycineTB']) ? '$("#tobramycineTBABG").val("'.$resultatAntiBioGramme['tobramycineTB'].'"); $("#choixTobramycineTBABG").trigger("click");' : '';
	            $html .= ($resultatAntiBioGramme['amikacineAN']) ? '$("#amikacineANABG").val("'.$resultatAntiBioGramme['amikacineAN'].'"); $("#choixAmikacineANABG").trigger("click");' : '';
	            $html .= ($resultatAntiBioGramme['netilmycine']) ? '$("#netilmycineABG").val("'.$resultatAntiBioGramme['netilmycine'].'"); $("#choixNetilmycineABG").trigger("click");' : '';
	            /**
	             * FIN PARTIE Aminosides
	             */
	            
	            /**
	             * PARTIE Phénicolés
	             */
	            $html .= ($resultatAntiBioGramme['chloramphenicolC']) ? '$("#chloramphenicolCABG").val("'.$resultatAntiBioGramme['chloramphenicolC'].'"); $("#choixChloramphenicolCABG").trigger("click");' : '';
	            /**
	             * FIN PARTIE Phénicolés
	             */
	            
	            /**
	             * PARTIE Cyclines
	             */
	            $html .= ($resultatAntiBioGramme['minocycline']) ? '$("#minocyclineABG").val("'.$resultatAntiBioGramme['minocycline'].'"); $("#choixMinocyclineABG").trigger("click");' : '';
	            $html .= ($resultatAntiBioGramme['tetracyclineTE']) ? '$("#tetracyclineTEABG").val("'.$resultatAntiBioGramme['tetracyclineTE'].'"); $("#choixTetracyclineTEABG").trigger("click");' : '';
	            $html .= ($resultatAntiBioGramme['doxycyclineDO']) ? '$("#doxycyclineDOABG").val("'.$resultatAntiBioGramme['doxycyclineDO'].'"); $("#choixDoxycyclineDOABG").trigger("click");' : '';
	            /**
	             * FIN PARTIE Cyclines
	             */
	            
	            /**
	             * PARTIE Macrolides et apparentés
	             */
	            $html .= ($resultatAntiBioGramme['azithromycineAZT']) ? '$("#azithromycineAZTABG").val("'.$resultatAntiBioGramme['azithromycineAZT'].'"); $("#choixAzithromycineAZTABG").trigger("click");' : '';
	            $html .= ($resultatAntiBioGramme['erythromycineE']) ? '$("#erythromycineEABG").val("'.$resultatAntiBioGramme['erythromycineE'].'"); $("#choixErythromycineEABG").trigger("click");' : '';
	            $html .= ($resultatAntiBioGramme['lincomycineL']) ? '$("#lincomycineLABG").val("'.$resultatAntiBioGramme['lincomycineL'].'"); $("#choixLincomycineLABG").trigger("click");' : '';
	            $html .= ($resultatAntiBioGramme['pristinamycinePT']) ? '$("#pristinamycinePTABG").val("'.$resultatAntiBioGramme['pristinamycinePT'].'"); $("#choixPristinamycinePTABG").trigger("click");' : '';
	            /**
	             * FIN PARTIE Macrolides et apparentés
	             */
	            
	            /**
	             * PARTIE Fluoroquinolones
	             */
	            $html .= ($resultatAntiBioGramme['acideNalidixiqueNA']) ? '$("#acideNalidixiqueNAABG").val("'.$resultatAntiBioGramme['acideNalidixiqueNA'].'"); $("#choixAcideNalidixiqueNAABG").trigger("click");' : '';
	            $html .= ($resultatAntiBioGramme['pefloxacinePEF']) ? '$("#pefloxacinePEFABG").val("'.$resultatAntiBioGramme['pefloxacinePEF'].'"); $("#choixPefloxacinePEFABG").trigger("click");' : '';
	            $html .= ($resultatAntiBioGramme['norfloxacineNOR']) ? '$("#norfloxacineNORABG").val("'.$resultatAntiBioGramme['norfloxacineNOR'].'"); $("#choixNorfloxacineNORABG").trigger("click");' : '';
	            $html .= ($resultatAntiBioGramme['ciprofloxacineCIP']) ? '$("#ciprofloxacineCIPABG").val("'.$resultatAntiBioGramme['ciprofloxacineCIP'].'"); $("#choixCiprofloxacineCIPABG").trigger("click");' : '';
	            $html .= ($resultatAntiBioGramme['LEV']) ? '$("#LEVABG").val("'.$resultatAntiBioGramme['LEV'].'"); $("#choixLEVABG").trigger("click");' : '';
	            /**
	             * FIN PARTIE Fluoroquinolones
	             */
	            
	            
	            /**
	             * PARTIE Imidazolés
	             */
	            $html .= ($resultatAntiBioGramme['cotrimoxazoleSXT']) ? '$("#cotrimoxazoleSXTABG").val("'.$resultatAntiBioGramme['cotrimoxazoleSXT'].'"); $("#choixCotrimoxazoleSXTABG").trigger("click");' : '';
	            /**
	             * FIN PARTIE Imidazolés
	             */
	            
	            
	            /**
	             * PARTIE Autres
	             */
	            $html .= ($resultatAntiBioGramme['fosfomycineFOS']) ? '$("#fosfomycineFOSABG").val("'.$resultatAntiBioGramme['fosfomycineFOS'].'"); $("#choixFosfomycineFOSABG").trigger("click");' : '';
	            $html .= ($resultatAntiBioGramme['acideFusidiqueFA']) ? '$("#acideFusidiqueFAABG").val("'.$resultatAntiBioGramme['acideFusidiqueFA'].'"); $("#choixAcideFusidiqueFAABG").trigger("click");' : '';
	            $html .= ($resultatAntiBioGramme['rifampicineRA']) ? '$("#rifampicineRAABG").val("'.$resultatAntiBioGramme['rifampicineRA'].'"); $("#choixRifampicineRAABG").trigger("click");' : '';
	            /**
	             * FIN PARTIE Autres
	             */
	            
	            /**
	             * Conclusion
	             */
	            
	            $html .= ($resultatAntiBioGramme['conclusion']) ? '$("#conclusion_pv_ABG").val("'.preg_replace("/(\r\n|\n|\r)/", "\\n", str_replace( "'", "\'",$resultatAntiBioGramme['conclusion'])).'");' : '';
	            /**
	             * ==========
	             */
	            
	            
	            $html .='</script>';
	        }
	    }
	    
	    return $html;
	}
	
	
	protected function getResultatsECBU($iddemande){

		$resultat = $this->getResultatDemandeAnalyseTable()->getValeursECBU($iddemande);
		
		$html = "";
		
		if($resultat['Parasites']){
			/*Parasites --- Parasites --- Parasites*/
			$html .= '<script> resultParasitesEcbu = '.$resultat['Parasites'].';  </script>';
		}else { $html .= '<script> resultParasitesEcbu = "";  </script>'; }
		
		$html .= "<script> getListeDesSouchesIdentificationCultureECBU(); </script>";
		$html .= "<script> getListeDesParasitesDansBdECBU(); </script>";
		$html .= "<script> $('a,div').tooltip({ animation: true, html: true, placement: 'bottom', show: { effect: 'slideDown', } }); </script>";
		
		
		$html .="<script> commentCultPosValECBU=''; </script>";
		if($resultat){
			$html .=
			"<script>
   	            commentCultPosValECBU = '".str_replace( "'", "\'", $resultat['conclusion'])."';
	            $('#type_materiel_ecbu').val('".str_replace( "'", "\'", $resultat['type_materiel'])."');
   	            $('#urines_ecbu').val('".$resultat['Urines']."');
   	            $('#leucocytes_ecbu').val('".$resultat['Leucocytes']."');
   	           	$('#leucocytes_champ_ecbu').val('".$resultat['LeucocytesChamp']."');
   	           	$('#hematies_ecbu').val('".$resultat['Hematies']."');
   	           	$('#hematies_champ_ecbu').val('".$resultat['HematiesChamp']."');
   	           	$('#levures_ecbu').val('".$resultat['Levures']."').trigger('change');
   	           	$('#filaments_myceliens_ecbu').val('".$resultat['FilamentsMyceliens']."');
   	           	$('#cellules_epitheliales_ecbu').val('".$resultat['CellulesEpitheliales']."');
   	           	$('#cellules_epitheliales_champ_ecbu').val('".$resultat['CellulesEpithelialesChamp']."');
         		/*-Flore-*/	
   	           	$('#flore_ecbu').val('".$resultat['Flore']."').trigger('change');
   	           	/*-Culot-*/
   	            $('#culot_ecbu').val('".$resultat['Culot']."').trigger('change');
   	            /*-Culture-*/
   	            $('#culture_ecbu').val('".$resultat['Culture']."').trigger('change');
   	            		
   	            $('#autres_precisions_commentaire_ecbu').val('".preg_replace("/(\r\n|\n|\r)/", "\\n", str_replace( "'", "\'", $resultat['precision_commentaire'] ))."');
    	        
             </script>";     
			
			/*Flore --- Flore --- Flore*/
			$html .= ($resultat['FloreAmas'])      ? '<script> $("#flore_cocci_pos_Choix1_ecbu").trigger("click"); </script>' : '';
			$html .= ($resultat['FloreChainette']) ? '<script> $("#flore_cocci_pos_Choix2_ecbu").trigger("click"); </script>' : '';

			/*Culture --- Culture --- Culture*/			
			$html .= ($resultat['CulturePos1']) ? '<script> $("#culture_pos_Choix1_ecbu").trigger("click"); </script>' : '';
			$html .= ($resultat['CulturePos2']) ? '<script> $("#culture_pos_Choix2_ecbu").trigger("click"); </script>' : '';
			$html .= ($resultat['CulturePos1']) ? '<script> setTimeout(function(){ $("#identification_culture_select_ecbu").val('.$resultat['IdentificationCulture'].').trigger("change"); },500); </script>' : '';
			
			/*Culot --- Culot --- Culot*/
			if($resultat['Culot'] == 1){
				$resultatCulot = $this->getResultatDemandeAnalyseTable()->getValeursECBUCulot($iddemande);
				$html .= '<script> setTimeout(function(){ '; $i=1;
				foreach ($resultatCulot as $resultCul){
					
					//if(next($resultatCulot)){
						$html .= '$("#culot_ecbu_plus").trigger("click");';						
					//}
					
					
					$typeCulot = $resultCul['type_culot'];
					$html .= '$("#culot_ecbu_select_'.$i.'").val('.$typeCulot.').trigger("change");';
					if($typeCulot == 4){
						$html .= '$("#culot_ecbu_valsel_'.$i++.'  input").val("'.str_replace( '"', '\"', $resultCul['info_culot']).'");';
					}else{
						$html .= '$("#culot_ecbu_valsel_'.$i++.' select").val('.$resultCul['valeur_culot'].');';
					}
				}
				$html .= '$("#culot_ecbu_moins").trigger("click");';
				$html .= '}); </script>';
			}
		}
		
		
		/**
		 * Recuperer les donnees de l'antibiogramme
		 */
		 
		if($resultat['IdentificationCulture'] != 0){
			$resultatAntiBioGramme = $this->getResultatDemandeAnalyseTable()->getValeursAntiBioGrammeECBU($iddemande);
			if($resultatAntiBioGramme){
				$html .= '<script> /*alert("'.$resultatAntiBioGramme['ampicillineAM'].'");*/';
		
				/**
				 * PARTIE B-lactamines
				 */
				$html .= ($resultatAntiBioGramme['ampicillineAM'])   ? '$("#ampicillineAMABGecbu").val("'.$resultatAntiBioGramme['ampicillineAM'].'"); $("#choixAmpicillineAMABGecbu").trigger("click");' : '';
				$html .= ($resultatAntiBioGramme['amoxillineAMX'])   ? '$("#amoxillineAMXABGecbu").val("'.$resultatAntiBioGramme['amoxillineAMX'].'"); $("#choixAmoxillineAMXABGecbu").trigger("click");' : '';
				$html .= ($resultatAntiBioGramme['ticarcillineTIC']) ? '$("#ticarcillineTICABGecbu").val("'.$resultatAntiBioGramme['ticarcillineTIC'].'"); $("#choixTicarcillineTICABGecbu").trigger("click");' : '';
				$html .= ($resultatAntiBioGramme['piperacillinePIP']) ? '$("#piperacillinePIPABGecbu").val("'.$resultatAntiBioGramme['piperacillinePIP'].'"); $("#choixPiperacillinePIPABGecbu").trigger("click");' : '';
				$html .= ($resultatAntiBioGramme['acideClavulaniqueAmoxicillineAMC']) ? '$("#acideClavulaniqueAmoxicillineAMCABGecbu").val("'.$resultatAntiBioGramme['acideClavulaniqueAmoxicillineAMC'].'"); $("#choixAcideClavulaniqueAmoxicillineAMCABGecbu").trigger("click");' : '';
				 
				$html .= ($resultatAntiBioGramme['gentamicineGM']) ? '$("#gentamicineGMABGecbu").val("'.$resultatAntiBioGramme['gentamicineGM'].'"); $("#choixGentamicineGMABGecbu").trigger("click");' : '';
				$html .= ($resultatAntiBioGramme['ticAcClavTCC']) ? '$("#ticAcClavTCCABGecbu").val("'.$resultatAntiBioGramme['ticAcClavTCC'].'"); $("#choixTicAcClavTCCABGecbu").trigger("click");' : '';
				$html .= ($resultatAntiBioGramme['ertapenemeETP']) ? '$("#ertapenemeETPABGecbu").val("'.$resultatAntiBioGramme['ertapenemeETP'].'"); $("#choixErtapenemeETPABGecbu").trigger("click");' : '';
				$html .= ($resultatAntiBioGramme['imipenemeIPM']) ? '$("#imipenemeIPMABGecbu").val("'.$resultatAntiBioGramme['imipenemeIPM'].'"); $("#choixImipenemeIPMABGecbu").trigger("click");' : '';
				$html .= ($resultatAntiBioGramme['oxacillineOX']) ? '$("#oxacillineOXABGecbu").val("'.$resultatAntiBioGramme['oxacillineOX'].'"); $("#choixOxacillineOXABGecbu").trigger("click");' : '';
		
				$html .= ($resultatAntiBioGramme['penicillineP']) ? '$("#penicillinePABGecbu").val("'.$resultatAntiBioGramme['penicillineP'].'"); $("#choixPenicillinePABGecbu").trigger("click");' : '';
				$html .= ($resultatAntiBioGramme['cefalotineCF']) ? '$("#cefalotineCFABGecbu").val("'.$resultatAntiBioGramme['cefalotineCF'].'"); $("#choixCefalotineCFABGecbu").trigger("click");' : '';
				$html .= ($resultatAntiBioGramme['cefoxitineFOX']) ? '$("#cefoxitineFOXABGecbu").val("'.$resultatAntiBioGramme['cefoxitineFOX'].'"); $("#choixCefoxitineFOXABGecbu").trigger("click");' : '';
				$html .= ($resultatAntiBioGramme['piperacillineTazobactamePPT']) ? '$("#piperacillineTazobactamePPTABGecbu").val("'.$resultatAntiBioGramme['piperacillineTazobactamePPT'].'"); $("#choixPiperacillineTazobactamePPTABGecbu").trigger("click");' : '';
				$html .= ($resultatAntiBioGramme['cefotaximeCTX']) ? '$("#cefotaximeCTXABGecbu").val("'.$resultatAntiBioGramme['cefotaximeCTX'].'"); $("#choixCefotaximeCTXABGecbu").trigger("click");' : '';
				 
				$html .= ($resultatAntiBioGramme['cefsulodineCFS']) ? '$("#cefsulodineCFSABGecbu").val("'.$resultatAntiBioGramme['cefsulodineCFS'].'"); $("#choixCefsulodineCFSABGecbu").trigger("click");' : '';
				$html .= ($resultatAntiBioGramme['CFP']) ? '$("#CFPABGecbu").val("'.$resultatAntiBioGramme['CFP'].'"); $("#choixCFPABGecbu").trigger("click");' : '';
				$html .= ($resultatAntiBioGramme['ceftazidimeCAZ']) ? '$("#ceftazidimeCAZABGecbu").val("'.$resultatAntiBioGramme['ceftazidimeCAZ'].'"); $("#choixCeftazidimeCAZABGecbu").trigger("click");' : '';
				$html .= ($resultatAntiBioGramme['ceftriaxoneCRO']) ? '$("#ceftriaxoneCROABGecbu").val("'.$resultatAntiBioGramme['ceftriaxoneCRO'].'"); $("#choixCeftriaxoneCROABGecbu").trigger("click");' : '';
				$html .= ($resultatAntiBioGramme['cefepimeFEP']) ? '$("#cefepimeFEPABGecbu").val("'.$resultatAntiBioGramme['cefepimeFEP'].'"); $("#choixCefepimeFEPABGecbu").trigger("click");' : '';
				$html .= ($resultatAntiBioGramme['aztreonamATM']) ? '$("#aztreonamATMABGecbu").val("'.$resultatAntiBioGramme['aztreonamATM'].'"); $("#choixAztreonamATMABGecbu").trigger("click");' : '';
				/**
				 * FIN PARTIE B-lactamines
				 */
				 
				/**
				 * PARTIE Glycopeptides
				 */
				$html .= ($resultatAntiBioGramme['vancomycineVA']) ? '$("#vancomycineVAABGecbu").val("'.$resultatAntiBioGramme['vancomycineVA'].'"); $("#choixVancomycineVAABGecbu").trigger("click");' : '';
				$html .= ($resultatAntiBioGramme['teicoplanine']) ? '$("#teicoplanineABGecbu").val("'.$resultatAntiBioGramme['teicoplanine'].'"); $("#choixTeicoplanineABGecbu").trigger("click");' : '';
				/**
				 * FIN PARTIE Glycopeptides
				 */
		
				 
				/**
				 * PARTIE Produits nitrés
				 */
				$html .= ($resultatAntiBioGramme['nitrofurane']) ? '$("#nitrofuraneABGecbu").val("'.$resultatAntiBioGramme['nitrofurane'].'"); $("#choixNitrofuraneABGecbu").trigger("click");' : '';
				$html .= ($resultatAntiBioGramme['metronidazole']) ? '$("#metronidazoleABGecbu").val("'.$resultatAntiBioGramme['metronidazole'].'"); $("#choixMetronidazoleABGecbu").trigger("click");' : '';
				/**
				 * FIN PARTIE Produits nitrés
				 */
		
				/**
				 * PARTIE Sulfamides
				 */
				$html .= ($resultatAntiBioGramme['trimethoprimeSulfametoxazoleSXT']) ? '$("#trimethoprimeSulfametoxazoleSXTABGecbu").val("'.$resultatAntiBioGramme['trimethoprimeSulfametoxazoleSXT'].'"); $("#choixTrimethoprimeSulfametoxazoleSXTABGecbu").trigger("click");' : '';
				/**
				 * FIN PARTIE Sulfamides
				 */
				 
				/**
				 * PARTIE Polymyxine
				 */
				$html .= ($resultatAntiBioGramme['colistineCS']) ? '$("#colistineCSABGecbu").val("'.$resultatAntiBioGramme['colistineCS'].'"); $("#choixColistineCSABGecbu").trigger("click");' : '';
				$html .= ($resultatAntiBioGramme['polymicine']) ? '$("#polymicineABGecbu").val("'.$resultatAntiBioGramme['polymicine'].'"); $("#choixPolymicineABGecbu").trigger("click");' : '';
				/**
				 * FIN PARTIE Polymyxine
				 */
				 
				/**
				 * PARTIE Aminosides
				 */
				$html .= ($resultatAntiBioGramme['kanamycineK']) ? '$("#kanamycineKABGecbu").val("'.$resultatAntiBioGramme['kanamycineK'].'"); $("#choixKanamycineKABGecbu").trigger("click");' : '';
				$html .= ($resultatAntiBioGramme['tobramycineTB']) ? '$("#tobramycineTBABGecbu").val("'.$resultatAntiBioGramme['tobramycineTB'].'"); $("#choixTobramycineTBABGecbu").trigger("click");' : '';
				$html .= ($resultatAntiBioGramme['amikacineAN']) ? '$("#amikacineANABGecbu").val("'.$resultatAntiBioGramme['amikacineAN'].'"); $("#choixAmikacineANABGecbu").trigger("click");' : '';
				$html .= ($resultatAntiBioGramme['netilmycine']) ? '$("#netilmycineABGecbu").val("'.$resultatAntiBioGramme['netilmycine'].'"); $("#choixNetilmycineABGecbu").trigger("click");' : '';
				/**
				 * FIN PARTIE Aminosides
				 */
				 
				/**
				 * PARTIE Phénicolés
				 */
				$html .= ($resultatAntiBioGramme['chloramphenicolC']) ? '$("#chloramphenicolCABGecbu").val("'.$resultatAntiBioGramme['chloramphenicolC'].'"); $("#choixChloramphenicolCABGecbu").trigger("click");' : '';
				/**
				 * FIN PARTIE Phénicolés
				 */
				 
				/**
				 * PARTIE Cyclines
				 */
				$html .= ($resultatAntiBioGramme['minocycline']) ? '$("#minocyclineABGecbu").val("'.$resultatAntiBioGramme['minocycline'].'"); $("#choixMinocyclineABGecbu").trigger("click");' : '';
				$html .= ($resultatAntiBioGramme['tetracyclineTE']) ? '$("#tetracyclineTEABGecbu").val("'.$resultatAntiBioGramme['tetracyclineTE'].'"); $("#choixTetracyclineTEABGecbu").trigger("click");' : '';
				$html .= ($resultatAntiBioGramme['doxycyclineDO']) ? '$("#doxycyclineDOABGecbu").val("'.$resultatAntiBioGramme['doxycyclineDO'].'"); $("#choixDoxycyclineDOABGecbu").trigger("click");' : '';
				/**
				 * FIN PARTIE Cyclines
				 */
				 
				/**
				 * PARTIE Macrolides et apparentés
				 */
				$html .= ($resultatAntiBioGramme['azithromycineAZT']) ? '$("#azithromycineAZTABGecbu").val("'.$resultatAntiBioGramme['azithromycineAZT'].'"); $("#choixAzithromycineAZTABGecbu").trigger("click");' : '';
				$html .= ($resultatAntiBioGramme['erythromycineE']) ? '$("#erythromycineEABGecbu").val("'.$resultatAntiBioGramme['erythromycineE'].'"); $("#choixErythromycineEABGecbu").trigger("click");' : '';
				$html .= ($resultatAntiBioGramme['lincomycineL']) ? '$("#lincomycineLABGecbu").val("'.$resultatAntiBioGramme['lincomycineL'].'"); $("#choixLincomycineLABGecbu").trigger("click");' : '';
				$html .= ($resultatAntiBioGramme['pristinamycinePT']) ? '$("#pristinamycinePTABGecbu").val("'.$resultatAntiBioGramme['pristinamycinePT'].'"); $("#choixPristinamycinePTABGecbu").trigger("click");' : '';
				/**
				 * FIN PARTIE Macrolides et apparentés
				 */
				 
				/**
				 * PARTIE Fluoroquinolones
				 */
				$html .= ($resultatAntiBioGramme['acideNalidixiqueNA']) ? '$("#acideNalidixiqueNAABGecbu").val("'.$resultatAntiBioGramme['acideNalidixiqueNA'].'"); $("#choixAcideNalidixiqueNAABGecbu").trigger("click");' : '';
				$html .= ($resultatAntiBioGramme['pefloxacinePEF']) ? '$("#pefloxacinePEFABGecbu").val("'.$resultatAntiBioGramme['pefloxacinePEF'].'"); $("#choixPefloxacinePEFABGecbu").trigger("click");' : '';
				$html .= ($resultatAntiBioGramme['norfloxacineNOR']) ? '$("#norfloxacineNORABGecbu").val("'.$resultatAntiBioGramme['norfloxacineNOR'].'"); $("#choixNorfloxacineNORABGecbu").trigger("click");' : '';
				$html .= ($resultatAntiBioGramme['ciprofloxacineCIP']) ? '$("#ciprofloxacineCIPABGecbu").val("'.$resultatAntiBioGramme['ciprofloxacineCIP'].'"); $("#choixCiprofloxacineCIPABGecbu").trigger("click");' : '';
				$html .= ($resultatAntiBioGramme['LEV']) ? '$("#LEVABGecbu").val("'.$resultatAntiBioGramme['LEV'].'"); $("#choixLEVABGecbu").trigger("click");' : '';
				/**
				 * FIN PARTIE Fluoroquinolones
				 */
				 
				 
				/**
				 * PARTIE Imidazolés
				 */
				$html .= ($resultatAntiBioGramme['cotrimoxazoleSXT']) ? '$("#cotrimoxazoleSXTABGecbu").val("'.$resultatAntiBioGramme['cotrimoxazoleSXT'].'"); $("#choixCotrimoxazoleSXTABGecbu").trigger("click");' : '';
				/**
				 * FIN PARTIE Imidazolés
				 */
				 
				 
				/**
				 * PARTIE Autres
				 */
				$html .= ($resultatAntiBioGramme['fosfomycineFOS']) ? '$("#fosfomycineFOSABGecbu").val("'.$resultatAntiBioGramme['fosfomycineFOS'].'"); $("#choixFosfomycineFOSABGecbu").trigger("click");' : '';
				$html .= ($resultatAntiBioGramme['acideFusidiqueFA']) ? '$("#acideFusidiqueFAABGecbu").val("'.$resultatAntiBioGramme['acideFusidiqueFA'].'"); $("#choixAcideFusidiqueFAABGecbu").trigger("click");' : '';
				$html .= ($resultatAntiBioGramme['rifampicineRA']) ? '$("#rifampicineRAABGecbu").val("'.$resultatAntiBioGramme['rifampicineRA'].'"); $("#choixRifampicineRAABGecbu").trigger("click");' : '';
				/**
				 * FIN PARTIE Autres
				 */
				 
				/**
				 * Conclusion
				 */
				 
				$html .= ($resultatAntiBioGramme['conclusion']) ? '$("#conclusion_ecbu_ABG").val("'.preg_replace("/(\r\n|\n|\r)/", "\\n", str_replace( "'", "\'",$resultatAntiBioGramme['conclusion'])).'");' : '';
				/**
				 * ==========
				 */
				 
				 
				$html .='</script>';
			}
		}
		
        return $html;
	}
	
	protected function getCommentaireBilan($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getCommentaireDuBilan($iddemande);
	    $html ="";
	    if($resultat){
	        $html .="<script>";
	        $html .= ($resultat['conclusion_bilan']) ? '$("#commentaireBilan").val("'.preg_replace("/(\r\n|\n|\r)/", "\\n", str_replace( '"', '\"',$resultat['conclusion_bilan'])).'");' : '';
	        $html .='</script>';
	    }
	    return $html;
	}
	        
	//***************** ========== RECUPERER UNE ANALYSE ========== ***************
	//***************** ========== RECUPERER UNE ANALYSE ========== ***************
	//***************** ========== RECUPERER UNE ANALYSE ========== ***************
	public function recupererAnalyseAction(){
		$iddemande = ( int ) $this->params ()->fromPost ( 'iddemande', 0 );

		$analyse = $this->getAnalyseTable()->getAnalysesDemandees($iddemande);
		
		$html  = "<table class='designEnTeteAnalyse ER_".$iddemande."' style='width: 100%;' > 
		           <input type='hidden' id='idanalyse' value='".$analyse['Idanalyse']."'>
				   <tr style='width: 100%;' > <td class='enTete'>". $analyse['Libelle'] ."</td> </tr>
				   <tr> <th class='enTitre'> <div>". $analyse['Designation'] ."</div> </th> </tr>";
		
		if($analyse['Idanalyse'] ==  1){ $html .= $this->nfs_1(); $html .= $this->getResultatsNfs($iddemande); } 
		if($analyse['Idanalyse'] ==  2){ $html .= $this->gsrh_groupage_2(); $html .= $this->getResultatsGsrhGroupage($iddemande); }
		if($analyse['Idanalyse'] ==  3){ $html .= $this->recherche_antigene_3(); $html .= $this->getResultatsRechercheAntigene($iddemande); }
		if($analyse['Idanalyse'] ==  4){ $html .= $this->test_combs_direct_4(); $html .= $this->getResultatsTestCombsDirect($iddemande); }
		if($analyse['Idanalyse'] ==  5){ $html .= $this->test_combs_indirect_5(); $html .= $this->getResultatsTestCombsIndirect($iddemande); }
		if($analyse['Idanalyse'] ==  6){ $html .= $this->test_compatibilite_6(); $html .= $this->getResultatsTestCompatibilite($iddemande); }
		if($analyse['Idanalyse'] ==  7){ $html .= $this->vitesse_sedimentation_7(); $html .= $this->getResultatsVitesseSedimentation($iddemande); }
		if($analyse['Idanalyse'] ==  8){ $html .= $this->test_demmel_8(); $html .= $this->getResultatsTestDemmel($iddemande); }
		if($analyse['Idanalyse'] ==  9){ $html .= $this->taux_reticulocytes_9(); $html .= $this->getResultatsTauxReticulocyte($iddemande); }
		if($analyse['Idanalyse'] == 10){ $html .= $this->goutte_epaisse_10(); $html .= $this->getResultatsGoutteEpaisse($iddemande); }
		
		   if($analyse['Idanalyse'] == 11){ $html .= $this->adenogramme_11();                 }
		   if($analyse['Idanalyse'] == 12){ $html .= $this->medulodramme_12();                }
		   if($analyse['Idanalyse'] == 13){ $html .= $this->cytochimie_myeloperoxydase_13();  }
		
		if($analyse['Idanalyse'] == 14){ $html .= $this->tp_inr_14(); $html .= $this->getResultatsTpInr($iddemande); }
		if($analyse['Idanalyse'] == 15){ $html .= $this->tca_15(); $html .= $this->getResultatsTca($iddemande); }
		if($analyse['Idanalyse'] == 16){ $html .= $this->fibrinemie_16(); $html .= $this->getResultatsFibrinemie($iddemande); }
		if($analyse['Idanalyse'] == 17){ $html .= $this->temps_saignement_17(); $html .= $this->getResultatsTempsSaignement($iddemande); }
		if($analyse['Idanalyse'] == 18){ $html .= $this->facteur_viii_18(); $html .= $this->getResultatsFacteur8($iddemande); }
		if($analyse['Idanalyse'] == 19){ $html .= $this->facteur_ix_19(); $html .= $this->getResultatsFacteur9($iddemande);  }
		if($analyse['Idanalyse'] == 20){ $html .= $this->dimeres_20();  $html .= $this->getResultatsDDimeres($iddemande);    }
		if($analyse['Idanalyse'] == 21){ $html .= $this->glycemie_21(); $html .= $this->getResultatsGlycemie($iddemande); }
		if($analyse['Idanalyse'] == 22){ $html .= $this->creatininemie_22(); $html .= $this->getResultatsCreatininemie($iddemande); }
		if($analyse['Idanalyse'] == 23){ $html .= $this->azotemie_23(); $html .= $this->getResultatsAzotemie($iddemande); }
		if($analyse['Idanalyse'] == 24){ $html .= $this->acide_urique_24(); $html .= $this->getResultatsAcideUrique($iddemande); }
		if($analyse['Idanalyse'] == 25){ $html .= $this->cholesterol_total_25(); $html .= $this->getResultatsCholesterolTotal($iddemande); }
		if($analyse['Idanalyse'] == 26){ $html .= $this->triglycerides_26(); $html .= $this->getResultatsTriglycerides($iddemande); }
		if($analyse['Idanalyse'] == 27){ $html .= $this->cholesterol_HDL_27(); $html .= $this->getResultatsCholesterolHDL($iddemande); }
		if($analyse['Idanalyse'] == 28){ $html .= $this->cholesterol_LDL_28(); $html .= $this->getResultatsCholesterolLDL($iddemande); }
		if($analyse['Idanalyse'] == 29){ 
		    $html .="<tr> <th class='enTitre'> <div> CHOLESTEROL TOTAL </div> </th> </tr>";
		    $html .= $this->cholesterol_total_25(); $html .= $this->getResultatsCholesterolTotal($iddemande);
 		    $html .="<tr> <th class='enTitre'> <div> CHOLESTEROL HDL </div> </th> </tr>";
 		    $html .= $this->cholesterol_HDL_27(); $html .= $this->getResultatsCholesterolHDL($iddemande);
 		    $html .="<tr> <th class='enTitre'> <div> CHOLESTEROL LDL </div> </th> </tr>";
 		    $html .= $this->cholesterol_LDL_28(); $html .= $this->getResultatsCholesterolLDL($iddemande); 
 		    $html .="<tr> <th class='enTitre'> <div> TRIGLYCERIDES </div> </th> </tr>";
 		    $html .= $this->triglycerides_26(); $html .= $this->getResultatsTriglycerides($iddemande);
		}
		if($analyse['Idanalyse'] == 30){ $html .= $this->lipides_totaux_30();  $html .= $this->getResultatsLipidesTotaux($iddemande); }
		if($analyse['Idanalyse'] == 31){ $html .= $this->ionogramme_31(); $html .= $this->getResultatsIonogramme($iddemande); }
		if($analyse['Idanalyse'] == 32){ $html .= $this->calcemie_32(); $html .= $this->getResultatsCalcemie($iddemande); }
		if($analyse['Idanalyse'] == 33){ $html .= $this->magnesemie_33(); $html .= $this->getResultatsMagnesemie($iddemande); }
		if($analyse['Idanalyse'] == 34){ $html .= $this->phosphoremie_34(); $html .= $this->getResultatsPhosphoremie($iddemande); }
		if($analyse['Idanalyse'] == 35){ $html .= $this->tgo_asat_35(); $html .= $this->getResultatsTgoAsat($iddemande); }
		if($analyse['Idanalyse'] == 36){ $html .= $this->tgp_alat_36(); $html .= $this->getResultatsTgpAlat($iddemande); }
		
		     if($analyse['Idanalyse'] == 37){
		         $html .="<tr> <th class='enTitre'> <div> ASAT </div> </th> </tr>";
		         $html .= $this->tgo_asat_35(); $html .= $this->getResultatsTgoAsat($iddemande);     
		         $html .="<tr> <th class='enTitre'> <div> ALAT </div> </th> </tr>";
		         $html .= $this->tgp_alat_36(); $html .= $this->getResultatsTgpAlat($iddemande);
		     }
		
		if($analyse['Idanalyse'] == 38){ $html .= $this->phosphatage_alcaline_38(); $html .= $this->getResultatsPhosphatageAlcaline($iddemande); }
		if($analyse['Idanalyse'] == 39){ $html .= $this->gama_gt_ygt_39(); $html .= $this->getResultatsGamaGtYgt($iddemande); }
		if($analyse['Idanalyse'] == 40){ $html .= $this->fer_serique_40(); $html .= $this->getResultatsFerSerique($iddemande); }
		if($analyse['Idanalyse'] == 41){ $html .= $this->ferritinine_41(); $html .= $this->getResultatsFerritinine($iddemande); }
		if($analyse['Idanalyse'] == 42){ $html .= $this->bilirubine_totale_directe_42(); $html .= $this->getResultatsBilirubineTotaleDirecte($iddemande); }
		if($analyse['Idanalyse'] == 43){ $html .= $this->hemoglobine_glyquee_hbac_43(); $html .= $this->getResultatsHemoglobineGlyqueeHBAC($iddemande); }
		if($analyse['Idanalyse'] == 44){ $html .= $this->electrophorese_hemoglobine_44(); $html .= $this->getResultatsElectrophoreseHemoglobine($iddemande); }
		if($analyse['Idanalyse'] == 45){ $html .= $this->electrophorese_preteines_45();  $html .= $this->getResultatsElectrophoreseProteine($iddemande); }
		if($analyse['Idanalyse'] == 46){ $html .= $this->albuminemie_46(); $html .= $this->getResultatsAlbuminemie($iddemande); }
		if($analyse['Idanalyse'] == 47){ $html .= $this->albumine_urinaire_47(); $html .= $this->getResultatsAlbumineUrinaire($iddemande); }
		if($analyse['Idanalyse'] == 48){ $html .= $this->protidemie_48(); $html .= $this->getResultatsProtidemie($iddemande); }
		if($analyse['Idanalyse'] == 49){ $html .= $this->proteinurie_49(); $html .= $this->getResultatsProteinurie($iddemande); }
		if($analyse['Idanalyse'] == 50){ $html .= $this->hlm_compte_daddis_50(); $html .= $this->getResultatsHlmCompteDaddis($iddemande); }
		if($analyse['Idanalyse'] == 51){ $html .= $this->beta_hcg_plasmatique_51();  $html .= $this->getResultatsBetaHcgPlasmatique($iddemande); }
		if($analyse['Idanalyse'] == 52){ $html .= $this->psa_52(); $html .= $this->getResultatsPsa($iddemande); }
		if($analyse['Idanalyse'] == 53){ $html .= $this->crp_53(); $html .= $this->getResultatsCrp($iddemande); }
		if($analyse['Idanalyse'] == 54){ $html .= $this->facteurs_rhumatoides_54(); $html .= $this->getResultatsFacteursRhumatoides($iddemande); }
		if($analyse['Idanalyse'] == 55){ $html .= $this->rf_waaler_rose_55(); $html .= $this->getResultatsRfWaalerRose($iddemande); }
		if($analyse['Idanalyse'] == 56){ $html .= $this->toxoplasmose_56();  $html .= $this->getResultatsToxoplasmose($iddemande); }
		if($analyse['Idanalyse'] == 57){ $html .= $this->rubeole_57();  $html .= $this->getResultatsRubeole($iddemande); }
		if($analyse['Idanalyse'] == 58){ $html .= $this->culot_urinaire_58();  $html .= $this->getResultatsCulotUrinaire($iddemande); }
		if($analyse['Idanalyse'] == 59){ $html .= $this->serologie_chlamydiae_59(); $html .= $this->getResultatsSerologieChlamydiae($iddemande); }
		if($analyse['Idanalyse'] == 60){ $html .= $this->serologie_syphilitique_60(); $html .= $this->getResultatsSerologieSyphilitique($iddemande); }
		if($analyse['Idanalyse'] == 61){ $html .= $this->aslo_61(); $html .= $this->getResultatsAslo($iddemande); }
		if($analyse['Idanalyse'] == 62){ $html .= $this->widal_62(); $html .= $this->getResultatsWidal($iddemande); }
		if($analyse['Idanalyse'] == 63){ $html .= $this->ag_hbs_63(); $html .= $this->getResultatsAgHbs($iddemande); }
		if($analyse['Idanalyse'] == 64){ $html .= $this->hiv_64(); $html .= $this->getResultatsHIV($iddemande); }
		if($analyse['Idanalyse'] == 65){ $html .= $this->pv_65(65);  $html .= $this->getResultatsPV($iddemande); }
		if($analyse['Idanalyse'] == 66){ $html .= $this->ecbu_66(); $html .= $this->getResultatsECBU($iddemande); }
		if($analyse['Idanalyse'] == 67){ $html .= $this->pus_67(); }
		if($analyse['Idanalyse'] == 68){ $html .= $this->typage_hemoglobine_68();  $html .= $this->getResultatsTypageHemoglobine($iddemande); }
		
		if($analyse['Idanalyse'] == 70){ $html .= $this->ldh_70();  $html .= $this->getResultatsLDH($iddemande); }
		if($analyse['Idanalyse'] == 71){ $html .= $this->nfs_tr_71(); $html .= $this->getResultatsNfsTR($iddemande); }
		
		if($analyse['Idanalyse'] == 74){ $html .= $this->pv_65(74);  $html .= $this->getResultatsPV($iddemande); }
		
		
		$html .= "</table>";
		
		$donnees = array($analyse['Idanalyse'], $html);
		
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $donnees ) );
	}
	
	//****** ENREGISTRER LES RESULTATS DE L'ANALYSE ******
	//****** ENREGISTRER LES RESULTATS DE L'ANALYSE ******
	//****** ENREGISTRER LES RESULTATS DE L'ANALYSE ******
	public function enregistrerResultatAction()
	{
	    $idanalyse = ( int ) $this->params ()->fromPost ( 'idanalyse', 0 );
	    $iddemande = ( int ) $this->params ()->fromPost ( 'iddemande', 0 );
	    $tab = $this->params ()->fromPost ( 'tab' );
	    $idemploye = $this->layout()->user['idemploye'];
	    
	    $donneesExiste = 0;
	    
	    if($idanalyse == 1){
	        $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	        $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursNfs($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 2){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursGsrhGroupage($tab, $iddemande);
	    }
	    else 
	        if($idanalyse == 3){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursRechercheAntigene($tab, $iddemande);
	    }
	    else 
	        if($idanalyse == 4){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursTestCombsDirect($tab, $iddemande);
	    }
	    else 
	        if($idanalyse == 5){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursTestCombsIndirect($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 6){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursTestCompatibilite($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 7){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursVitesseSedimentation($tab, $iddemande);
	    }
	    else 
	        if($idanalyse == 8){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursTestDemmel($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 9){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursTauxReticulocyte($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 10){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursGoutteEpaisse($tab, $iddemande);
	    }
	    
	    
	    else
	        if($idanalyse == 14){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursTpInr($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 15){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursTca($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 16){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursFibrinemie($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 17){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursTempsSaignement($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 18){
	        	$this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	        	$donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursFacteur8($tab, $iddemande);
	        }
	    else
	        if($idanalyse == 19){
	           	$this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	           	$donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursFacteur9($tab, $iddemande);
	        }
	    else 
	    	if($idanalyse == 20){
	    		$this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	    		$donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursDDimeres($tab, $iddemande);
	    	}
	    else
	        if($idanalyse == 21){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursGlycemie($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 22){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursCreatininemie($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 23){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursAzotemie($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 24){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursAcideUrique($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 25){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursCholesterolTotal($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 26){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursTriglycerides($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 27){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursCholesterolHDL($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 28){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursCholesterolLDL($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 29){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeurs_Total_HDL_LDL_Triglycerides($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 30){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursLipidesTotaux($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 31){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursIonogramme($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 32){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursCalcemie($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 33){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursMagnesemie($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 34){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursPhosphoremie($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 35){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursTgoAsat($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 36){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursTgpAlat($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 37){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursAsatAlat($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 38){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursPhosphatageAlcaline($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 39){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursGamaGtYgt($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 40){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursFerSerique($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 41){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursFerritinine($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 42){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursBilirubineTotaleDirecte($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 43){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursHemoglobineGlyqueeHBAC($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 44){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursElectrophoreseHemoglobine($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 45){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursElectrophoreseProteines($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 46){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursAlbuminemie($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 47){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursAlbumineUrinaire($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 47){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursProteineTotale($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 48){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursProtidemie($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 49){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursProteinurie($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 50){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursHlmCompteDaddis($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 51){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursBetaHcgPlasmatique($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 52){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursPsa($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 53){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursCrp($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 54){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursFacteursRhumatoides($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 55){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursRfWaalerRose($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 56){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursToxoplasmose($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 57){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursRubeole($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 58){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursCulotUrinaire($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 59){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursSerologieChlamydiae($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 60){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursSerologieSyphilitique($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 61){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursAslo($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 62){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursWidal($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 63){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursAgHbs($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 64){
	        	$this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	        	$donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursHIV($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 65){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursPV($tab, $iddemande);
	    }
	    else
	   		if($idanalyse == 66){
	   			$this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	   			$donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursECBU($tab, $iddemande);
	    }
	    
	    
	    
	    
	    
	    
	    
	    
	    
	    else
	        if($idanalyse == 68){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursTypageHemoglobine($tab, $iddemande);
	    }
	    

	    else
	        if($idanalyse == 70){
	        	$this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	        	$donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursLDH($tab, $iddemande);
	    }

	    else 
	        if($idanalyse == 71){
	        	$this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	        	$donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursNfs($tab, $iddemande);
	    }
	    
	    else
	    if($idanalyse == 74){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursPV($tab, $iddemande);
	    }
	        
	    $donnees = array($iddemande, $donneesExiste);
	    $this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
	    return $this->getResponse ()->setContent ( Json::encode ( $donnees ) );
	}
	
	
	//****** ========== RECUPERER LES ANALYSES DE LA DEMANDE ========== ******
	//****** ========== RECUPERER LES ANALYSES DE LA DEMANDE ========== ******
	//****** ========== RECUPERER LES ANALYSES DE LA DEMANDE ========== ******
	public function recupererLesAnalysesDeLaDemandeAction()
	{
		$iddemande = ( int ) $this->params ()->fromPost ( 'iddemande', 0 );
	
		$listeAnalyse = $this->getAnalyseTable()->getListeAnalysesDemandeesTrieesPopup($iddemande);
		
		$html = "";
		$libelle = "";
		$tabAnalyses = array();
		$tabDemandes = array();
		$tableauDemandes = array();
		
		foreach ($listeAnalyse as $liste) {

			$html .="<table class='designEnTeteAnalyse  ER_".$liste['iddemande']."' style='width: 100%;' >";
			
			if($libelle != $liste['Libelle']){
			    $html .="<tr style='width: 100%;' > <td class='enTete'>". $liste['Libelle'] ."</td> </tr>";
			    $libelle = $liste['Libelle'];
			}
			
			$html .="<tr> <th class='enTitre'> <div>". $liste['Designation'] ."</div> </th> </tr>";
			
			$tableauDemandes [] = $liste['iddemande'];
			
			if($liste['Idanalyse'] ==  1){ $html .= $this->nfs_1(); $html .= $this->getResultatsNfs($liste['iddemande']); }
			if($liste['Idanalyse'] ==  2){ $html .= $this->gsrh_groupage_2(); $html .= $this->getResultatsGsrhGroupage($liste['iddemande']); }
			if($liste['Idanalyse'] ==  3){ $html .= $this->recherche_antigene_3(); $html .= $this->getResultatsRechercheAntigene($liste['iddemande']); }
			if($liste['Idanalyse'] ==  4){ $html .= $this->test_combs_direct_4(); $html .= $this->getResultatsTestCombsDirect($liste['iddemande']); }
			if($liste['Idanalyse'] ==  5){ $html .= $this->test_combs_indirect_5(); $html .= $this->getResultatsTestCombsIndirect($liste['iddemande']); }
			if($liste['Idanalyse'] ==  6){ $html .= $this->test_compatibilite_6(); $html .= $this->getResultatsTestCompatibilite($liste['iddemande']); }
			if($liste['Idanalyse'] ==  7){ $html .= $this->vitesse_sedimentation_7(); $html .= $this->getResultatsVitesseSedimentation($liste['iddemande']); }
			if($liste['Idanalyse'] ==  8){ $html .= $this->test_demmel_8(); $html .= $this->getResultatsTestDemmel($liste['iddemande']); }
			if($liste['Idanalyse'] ==  9){ $html .= $this->taux_reticulocytes_9(); $html .= $this->getResultatsTauxReticulocyte($liste['iddemande']); }
			if($liste['Idanalyse'] == 10){ $html .= $this->goutte_epaisse_10(); $html .= $this->getResultatsGoutteEpaisse($liste['iddemande']); }
			
			   if($liste['Idanalyse'] == 11){ $html .= $this->adenogramme_11();                 }
			   if($liste['Idanalyse'] == 12){ $html .= $this->medulodramme_12();                }
			   if($liste['Idanalyse'] == 13){ $html .= $this->cytochimie_myeloperoxydase_13();  }
			
			if($liste['Idanalyse'] == 14){ $html .= $this->tp_inr_14(); $html .= $this->getResultatsTpInr($liste['iddemande']); }
			if($liste['Idanalyse'] == 15){ $html .= $this->tca_15(); $html .= $this->getResultatsTca($liste['iddemande']); }
			if($liste['Idanalyse'] == 16){ $html .= $this->fibrinemie_16(); $html .= $this->getResultatsFibrinemie($liste['iddemande']); }
			if($liste['Idanalyse'] == 17){ $html .= $this->temps_saignement_17(); $html .= $this->getResultatsTempsSaignement($liste['iddemande']); }
			if($liste['Idanalyse'] == 18){ $html .= $this->facteur_viii_18();  $html .= $this->getResultatsFacteur8($liste['iddemande']); }
			if($liste['Idanalyse'] == 19){ $html .= $this->facteur_ix_19(); $html .= $this->getResultatsFacteur9($liste['iddemande']);  }
			if($liste['Idanalyse'] == 20){ $html .= $this->dimeres_20();  $html .= $this->getResultatsDDimeres($liste['iddemande']);    }
			if($liste['Idanalyse'] == 21){ $html .= $this->glycemie_21(); $html .= $this->getResultatsGlycemie($liste['iddemande']); }
			if($liste['Idanalyse'] == 22){ $html .= $this->creatininemie_22(); $html .= $this->getResultatsCreatininemie($liste['iddemande']); }
			if($liste['Idanalyse'] == 23){ $html .= $this->azotemie_23(); $html .= $this->getResultatsAzotemie($liste['iddemande']); }
			if($liste['Idanalyse'] == 24){ $html .= $this->acide_urique_24(); $html .= $this->getResultatsAcideUrique($liste['iddemande']); }
			if($liste['Idanalyse'] == 25){ $html .= $this->cholesterol_total_25(); $html .= $this->getResultatsCholesterolTotal($liste['iddemande']); }
			if($liste['Idanalyse'] == 26){ $html .= $this->triglycerides_26(); $html .= $this->getResultatsTriglycerides($liste['iddemande']); }
			if($liste['Idanalyse'] == 27){ $html .= $this->cholesterol_HDL_27(); $html .= $this->getResultatsCholesterolHDL($liste['iddemande']); }
			if($liste['Idanalyse'] == 28){ $html .= $this->cholesterol_LDL_28(); $html .= $this->getResultatsCholesterolLDL($liste['iddemande']); }
			if($liste['Idanalyse'] == 29){
			    $html .="<tr> <th class='enTitre'> <div> CHOLESTEROL TOTAL </div> </th> </tr>";
			    $html .= $this->cholesterol_total_25(); $html .= $this->getResultatsCholesterolTotal($liste['iddemande']);
			    $html .="<tr> <th class='enTitre'> <div> CHOLESTEROL HDL </div> </th> </tr>";
			    $html .= $this->cholesterol_HDL_27(); $html .= $this->getResultatsCholesterolHDL($liste['iddemande']);
			    $html .="<tr> <th class='enTitre'> <div> CHOLESTEROL LDL </div> </th> </tr>";
			    $html .= $this->cholesterol_LDL_28(); $html .= $this->getResultatsCholesterolLDL($liste['iddemande']);
			    $html .="<tr> <th class='enTitre'> <div> TRIGLYCERIDES </div> </th> </tr>";
			    $html .= $this->triglycerides_26(); $html .= $this->getResultatsTriglycerides($liste['iddemande']);
			}
			if($liste['Idanalyse'] == 30){ $html .= $this->lipides_totaux_30(); $html .= $this->getResultatsLipidesTotaux($liste['iddemande']); }
			if($liste['Idanalyse'] == 31){ $html .= $this->ionogramme_31(); $html .= $this->getResultatsIonogramme($liste['iddemande']); }
			if($liste['Idanalyse'] == 32){ $html .= $this->calcemie_32(); $html .= $this->getResultatsCalcemie($liste['iddemande']); }
			if($liste['Idanalyse'] == 33){ $html .= $this->magnesemie_33(); $html .= $this->getResultatsMagnesemie($liste['iddemande']); }
			if($liste['Idanalyse'] == 34){ $html .= $this->phosphoremie_34(); $html .= $this->getResultatsPhosphoremie($liste['iddemande']); }
			if($liste['Idanalyse'] == 35){ $html .= $this->tgo_asat_35(); $html .= $this->getResultatsTgoAsat($liste['iddemande']); }
			if($liste['Idanalyse'] == 36){ $html .= $this->tgp_alat_36(); $html .= $this->getResultatsTgpAlat($liste['iddemande']); }
			if($liste['Idanalyse'] == 37){
			    $html .="<tr> <th class='enTitre'> <div> ASAT </div> </th> </tr>";
			    $html .= $this->tgo_asat_35(); $html .= $this->getResultatsTgoAsat($liste['iddemande']);
			    $html .="<tr> <th class='enTitre'> <div> ALAT </div> </th> </tr>";
			    $html .= $this->tgp_alat_36(); $html .= $this->getResultatsTgpAlat($liste['iddemande']);
			}
			if($liste['Idanalyse'] == 38){ $html .= $this->phosphatage_alcaline_38(); $html .= $this->getResultatsPhosphatageAlcaline($liste['iddemande']); }
			if($liste['Idanalyse'] == 39){ $html .= $this->gama_gt_ygt_39(); $html .= $this->getResultatsGamaGtYgt($liste['iddemande']); }
			if($liste['Idanalyse'] == 40){ $html .= $this->fer_serique_40(); $html .= $this->getResultatsFerSerique($liste['iddemande']); }
			if($liste['Idanalyse'] == 41){ $html .= $this->ferritinine_41(); $html .= $this->getResultatsFerritinine($liste['iddemande']); }
			if($liste['Idanalyse'] == 42){ $html .= $this->bilirubine_totale_directe_42(); $html .= $this->getResultatsBilirubineTotaleDirecte($liste['iddemande']); }
			if($liste['Idanalyse'] == 43){ $html .= $this->hemoglobine_glyquee_hbac_43(); $html .= $this->getResultatsHemoglobineGlyqueeHBAC($liste['iddemande']); }
			if($liste['Idanalyse'] == 44){ $html .= $this->electrophorese_hemoglobine_44(); $html .= $this->getResultatsElectrophoreseHemoglobine($liste['iddemande']); }
			if($liste['Idanalyse'] == 45){ $html .= $this->electrophorese_preteines_45(); $html .= $this->getResultatsElectrophoreseProteine($liste['iddemande']); }	
			if($liste['Idanalyse'] == 46){ $html .= $this->albuminemie_46(); $html .= $this->getResultatsAlbuminemie($liste['iddemande']); }
			if($liste['Idanalyse'] == 47){ $html .= $this->albumine_urinaire_47(); $html .= $this->getResultatsAlbumineUrinaire($liste['iddemande']); }
			if($liste['Idanalyse'] == 48){ $html .= $this->protidemie_48(); $html .= $this->getResultatsProtidemie($liste['iddemande']); }
			if($liste['Idanalyse'] == 49){ $html .= $this->proteinurie_49();  $html .= $this->getResultatsProteinurie($liste['iddemande']); }
			if($liste['Idanalyse'] == 50){ $html .= $this->hlm_compte_daddis_50();  $html .= $this->getResultatsHlmCompteDaddis($liste['iddemande']); }
			if($liste['Idanalyse'] == 51){ $html .= $this->beta_hcg_plasmatique_51();  $html .= $this->getResultatsBetaHcgPlasmatique($liste['iddemande']); }
			if($liste['Idanalyse'] == 52){ $html .= $this->psa_52(); $html .= $this->getResultatsPsa($liste['iddemande']); }
			if($liste['Idanalyse'] == 53){ $html .= $this->crp_53(); $html .= $this->getResultatsCrp($liste['iddemande']); }
			if($liste['Idanalyse'] == 54){ $html .= $this->facteurs_rhumatoides_54(); $html .= $this->getResultatsFacteursRhumatoides($liste['iddemande']); }
			if($liste['Idanalyse'] == 55){ $html .= $this->rf_waaler_rose_55(); $html .= $this->getResultatsRfWaalerRose($liste['iddemande']); }
			if($liste['Idanalyse'] == 56){ $html .= $this->toxoplasmose_56(); $html .= $this->getResultatsToxoplasmose($liste['iddemande']); }
			if($liste['Idanalyse'] == 57){ $html .= $this->rubeole_57(); $html .= $this->getResultatsRubeole($liste['iddemande']); }
			if($liste['Idanalyse'] == 58){ $html .= $this->culot_urinaire_58(); $html .= $this->getResultatsCulotUrinaire($liste['iddemande']); }
			if($liste['Idanalyse'] == 59){ $html .= $this->serologie_chlamydiae_59();  $html .= $this->getResultatsSerologieChlamydiae($liste['iddemande']); }
			if($liste['Idanalyse'] == 60){ $html .= $this->serologie_syphilitique_60();  $html .= $this->getResultatsSerologieSyphilitique($liste['iddemande']); }
			if($liste['Idanalyse'] == 61){ $html .= $this->aslo_61();  $html .= $this->getResultatsAslo($liste['iddemande']); }
			if($liste['Idanalyse'] == 62){ $html .= $this->widal_62(); $html .= $this->getResultatsWidal($liste['iddemande']); }
			if($liste['Idanalyse'] == 63){ $html .= $this->ag_hbs_63(); $html .= $this->getResultatsAgHbs($liste['iddemande']); }
			if($liste['Idanalyse'] == 64){ $html .= $this->hiv_64(); $html .= $this->getResultatsHIV($liste['iddemande']); }
			if($liste['Idanalyse'] == 65){ $html .= $this->pv_65(65);  $html .= $this->getResultatsPV($liste['iddemande']);}
			if($liste['Idanalyse'] == 66){ $html .= $this->ecbu_66(); $html .= $this->getResultatsECBU($liste['iddemande']); }
			if($liste['Idanalyse'] == 67){ $html .= $this->pus_67(); }
			if($liste['Idanalyse'] == 68){ $html .= $this->typage_hemoglobine_68();  $html .= $this->getResultatsTypageHemoglobine($liste['iddemande']); }
			
			if($liste['Idanalyse'] == 70){ $html .= $this->ldh_70();  $html .= $this->getResultatsLDH($liste['iddemande']); }
			if($liste['Idanalyse'] == 71){ $html .= $this->nfs_tr_71(); $html .= $this->getResultatsNfsTR($liste['iddemande']); }
			
			if($liste['Idanalyse'] == 74){ $html .= $this->pv_65(74);  $html .= $this->getResultatsPV($liste['iddemande']);}
			
			
			$tabAnalyses[] = $liste['Idanalyse'];
			$tabDemandes[] = $liste['iddemande'];
			
			$html .="</table>";
		}
		
		$html .="<div id='champCommentaireDuBilanTextArea' class='designEnTeteAnalyse' style='width: 100%; height: 170px; background: gray; padding-top: 20px; margin-top: 20px; border-radius: 10px;' align='left'> 
		           <table style='width: 95%; margin-top: 0px;'>
    			     <tr class='ligneAnanlyse' style='width: 100%;'>
    			     	<td style='width: 100%;'><label style='height: 140px;' ><span style='font-size: 17px; float: left; margin-left: 30px; font-weight: bold;'> Conclusion du bilan  </span> <textarea id='commentaireBilan' style='max-height: 100px; min-height: 100px; max-width: 560px; min-width: 560px; margin-left: 30px; text-align: justify; padding: 5px;'> </textarea> </label></td>
    			     </tr>
	               </table>
		         </div>";
		
		$html .= $this->getCommentaireBilan($iddemande);
		
		
		//Récupération de la liste des demandes, pour connaitre les demandes
		$html .="<script> var listeDesDemandesSelect = []; </script>";
		for($i = 0 ; $i < count($tableauDemandes) ; $i++){
			$html .="<script> listeDesDemandesSelect[".$i."]=".$tableauDemandes[$i]."; </script>";
		}
	
		$donnees = array($html, $tabAnalyses, $tabDemandes);
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $donnees ) );
	}
	
	//****** ENREGISTRER LES RESULTATS DE L'ANALYSE ******
	//****** ENREGISTRER LES RESULTATS DE L'ANALYSE ******
	//****** ENREGISTRER LES RESULTATS DE L'ANALYSE ******
	public function enregistrerResultatsDemandeAction()
	{
	    $tabAnalyses = $this->params ()->fromPost ( 'tabAnalyses' );
	    $tabDemandes = $this->params ()->fromPost ( 'tabDemandes' );
	    $tableau = $this->params ()->fromPost ( 'tab' );
	    $idemploye = $this->layout()->user['idemploye'];
	    
	    
	    /*
	     * Nouveau code ajouté 11/12/19
	     */
	    $iddemande = (int) $this->params ()->fromPost ( 'iddemande' );
	    $commentaireBilan = $this->params ()->fromPost ( 'commentaireBilan' );
	    /** enregistrement du bilan de l'analyse */
	    $this->getResultatDemandeAnalyseTable()->addCommentaireDuBilan($commentaireBilan, $iddemande);
	    
	    /*Fin nouveau code*/
	    
	    
	    for ($i = 0 ; $i<count($tabAnalyses) ; $i++){
	        $idanalyse = $tabAnalyses[$i];
	        $iddemande = $tabDemandes[$i];
	        
	        if($idanalyse == 1){
	            $tab = $tableau[$idanalyse];
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $this->getResultatDemandeAnalyseTable()->addValeursNfs($tab, $iddemande);
	        }
	        else
	            if($idanalyse == 2){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $this->getResultatDemandeAnalyseTable()->addValeursGsrhGroupage($tab, $iddemande);
	        }
	        else
	            if($idanalyse == 3){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $this->getResultatDemandeAnalyseTable()->addValeursRechercheAntigene($tab, $iddemande);
	        }
	        else
	            if($idanalyse == 4){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $this->getResultatDemandeAnalyseTable()->addValeursTestCombsDirect($tab, $iddemande);
	        }
	        else
	            if($idanalyse == 5){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $this->getResultatDemandeAnalyseTable()->addValeursTestCombsIndirect($tab, $iddemande);
	        }
	        else
	            if($idanalyse == 6){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $this->getResultatDemandeAnalyseTable()->addValeursTestCompatibilite($tab, $iddemande);
	        }
	        else
	            if($idanalyse == 7){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $this->getResultatDemandeAnalyseTable()->addValeursVitesseSedimentation($tab, $iddemande);
	        }
	        else
	            if($idanalyse == 8){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $this->getResultatDemandeAnalyseTable()->addValeursTestDemmel($tab, $iddemande);
	        }
	        else
	            if($idanalyse == 9){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $this->getResultatDemandeAnalyseTable()->addValeursTauxReticulocyte($tab, $iddemande);
	        }
	        else
	            if($idanalyse == 10){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursGoutteEpaisse($tab, $iddemande);
	        }
	        
	   
	        else
	            if($idanalyse == 14){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursTpInr($tab, $iddemande);
	        }
	        else
	            if($idanalyse == 15){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursTca($tab, $iddemande);
	        }
	        else
	            if($idanalyse == 16){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursFibrinemie($tab, $iddemande);
	        }
	        else
	            if($idanalyse == 17){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursTempsSaignement($tab, $iddemande);
	        }
	        else
	            if($idanalyse == 18){
	            	$tab = $tableau[$idanalyse];
	            	$this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            	$donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursFacteur8($tab, $iddemande);
	        }
	        else
	            if($idanalyse == 19){
	            	$tab = $tableau[$idanalyse];
	            	$this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            	$donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursFacteur9($tab, $iddemande);
	        }
	        else 
	        	if($idanalyse == 20){
	        		$tab = $tableau[$idanalyse];
	        		$this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	        		$donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursDDimeres($tab, $iddemande);
	        }
	        else
	            if($idanalyse == 21){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursGlycemie($tab, $iddemande);
	        }
	        else
	            if($idanalyse == 22){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursCreatininemie($tab, $iddemande);
	        }
	        else
	            if($idanalyse == 23){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursAzotemie($tab, $iddemande);
	        }
	        else
	            if($idanalyse == 24){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursAcideUrique($tab, $iddemande);
	        }
	        else
	            if($idanalyse == 25){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursCholesterolTotal($tab, $iddemande);
	        }
	        else
	            if($idanalyse == 26){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursTriglycerides($tab, $iddemande);
	        }
	        else
	            if($idanalyse == 27){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursCholesterolHDL($tab, $iddemande);
	        }
	        else
	            if($idanalyse == 28){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursCholesterolLDL($tab, $iddemande);
	        }
	        else
	            if($idanalyse == 29){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeurs_Total_HDL_LDL_Triglycerides($tab, $iddemande);
	        }
	        else
	            if($idanalyse == 30){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursLipidesTotaux($tab, $iddemande);
	        }
	        else
	            if($idanalyse == 31){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursIonogramme($tab, $iddemande);
	        }
	        else
	            if($idanalyse == 32){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursCalcemie($tab, $iddemande);
	        }
	        else
	            if($idanalyse == 33){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursMagnesemie($tab, $iddemande);
	        }
	        else
	            if($idanalyse == 34){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursPhosphoremie($tab, $iddemande);
	        }
	        else
	            if($idanalyse == 35){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursTgoAsat($tab, $iddemande);
	        }
	        else
	            if($idanalyse == 36){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursTgpAlat($tab, $iddemande);
	        }
	        else
	            if($idanalyse == 37){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursAsatAlat($tab, $iddemande);
	        }
	        else
	            if($idanalyse == 38){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursPhosphatageAlcaline($tab, $iddemande);
	        }
	        else
	            if($idanalyse == 39){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursGamaGtYgt($tab, $iddemande);
	        }
	        else
	            if($idanalyse == 40){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursFerSerique($tab, $iddemande);
	        }
	        else
	            if($idanalyse == 41){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursFerritinine($tab, $iddemande);
	        }
	        else
	            if($idanalyse == 42){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursBilirubineTotaleDirecte($tab, $iddemande);
	        }
	        else
	            if($idanalyse == 43){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursHemoglobineGlyqueeHBAC($tab, $iddemande);
	        }
	        else
	            if($idanalyse == 44){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursElectrophoreseHemoglobine($tab, $iddemande);
	        }
	        else
	            if($idanalyse == 45){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursElectrophoreseProteines($tab, $iddemande);
	        }
	        else
	            if($idanalyse == 46){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursAlbuminemie($tab, $iddemande);
	        }
	        else
	            if($idanalyse == 47){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursAlbumineUrinaire($tab, $iddemande);
	        }
	        else
	            if($idanalyse == 48){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursProtidemie($tab, $iddemande);
	        }
	        else
	            if($idanalyse == 49){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursProteinurie($tab, $iddemande);
	        }
	        else
	            if($idanalyse == 50){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursHlmCompteDaddis($tab, $iddemande);
	        }
	        else
	            if($idanalyse == 51){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursBetaHcgPlasmatique($tab, $iddemande);
	        }
	        else
	            if($idanalyse == 52){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursPsa($tab, $iddemande);
	        }
	        else
	            if($idanalyse == 53){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursCrp($tab, $iddemande);
	        }
	        else
	            if($idanalyse == 54){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursFacteursRhumatoides($tab, $iddemande);
	        }
	        else
	            if($idanalyse == 55){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursRfWaalerRose($tab, $iddemande);
	        }
	        else
	            if($idanalyse == 56){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursToxoplasmose($tab, $iddemande);
	        }
	        else
	            if($idanalyse == 57){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursRubeole($tab, $iddemande);
	        }
	        else
	            if($idanalyse == 58){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursCulotUrinaire($tab, $iddemande);
	        }
	        else
	            if($idanalyse == 59){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursSerologieChlamydiae($tab, $iddemande);
	        }  
	        else
	            if($idanalyse == 60){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursSerologieSyphilitique($tab, $iddemande);
	        }
	        else
	            if($idanalyse == 61){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursAslo($tab, $iddemande);
	        }
	        else
	            if($idanalyse == 62){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursWidal($tab, $iddemande);
	        }
	        else
	            if($idanalyse == 63){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursAgHbs($tab, $iddemande);
	        }
	        else
	            if($idanalyse == 64){
	            	$tab = $tableau[$idanalyse];
	            	$this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            	$donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursHIV($tab, $iddemande);
	        }
	        else
	            if($idanalyse == 65){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursPV($tab, $iddemande);
	        }
	        else
	        	if($idanalyse == 66){
	        		$tab = $tableau[$idanalyse];
	        		$this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	        		$donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursECBU($tab, $iddemande);
	        }
	        
	        
	        
	        
	        
	        
	        else
	            if($idanalyse == 68){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursTypageHemoglobine($tab, $iddemande);
	        }
	        
	        else
	            if($idanalyse == 70){
	            	$tab = $tableau[$idanalyse];
	            	$this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            	$donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursLDH($tab, $iddemande);
	        }
	        else 
	        	if($idanalyse == 71){
	        		$tab = $tableau[$idanalyse];
	        		$this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	        		$this->getResultatDemandeAnalyseTable()->addValeursNfs($tab, $iddemande);
	        }
	        
	        
	        else
		        if($idanalyse == 74){
		        	$tab = $tableau[$idanalyse];
		        	$this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
		        	$donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursPV($tab, $iddemande);
		        }
	    }
	    
	    //Le plus petit iddemande du tableau
	    $iddemande =  min($tabDemandes);
	    
	    $this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
	    return $this->getResponse ()->setContent ( Json::encode ( $iddemande ) );
	}
	
	
	//****** ========== RECUPERER LES RESULTATS DES ANALYSES DEMANDEES PAR TYPE ========== ******
	//****** ========== RECUPERER LES RESULTATS DES ANALYSES DEMANDEES PAR TYPE ========== ******
	//****** ========== RECUPERER LES RESULTATS DES ANALYSES DEMANDEES PAR TYPE ========== ******
	public function recupererLesAnalysesDemandeesParTypeAction()
	{
		$idtype = ( int ) $this->params ()->fromPost ( 'idtype', 0 );
	
		$listeAnalysesType = $this->getAnalyseTable() ->getListeAnalysesDemandeesParType($idtype);
		
		$tableauDonnees = array();
		$tableauPatients = array();
		$tableauDemandes = array();
		$tableauAnalyses = array();
		$tableauNumerosDossiers = array();
		
		foreach ($listeAnalysesType as $liste){
			$tableauDonnees[] = $liste;
			if( !in_array($liste['idpersonne'], $tableauPatients) ) { $tableauPatients[] = $liste['idpersonne']; $tableauNumerosDossiers[] = $liste['numero_dossier'];}
		}
		
		$html ="";
		
		for($i = 0 ; $i < count($tableauPatients) ; $i++){
			$k = 0 ;
				
			$html .="<table class='designEnTeteAnalyse  patientListeAnalyse  pat_".$tableauPatients[$i]."' style='width: 100%;' >";
			for ($j = 0 ; $j < count($tableauDonnees) ; $j++){
		
				if($tableauDonnees[$j]['idpersonne'] == $tableauPatients[$i]){
					
				    if($k == 0){
						$k++;
						$html .="<tr style='width: 90%;' > <td class='enTete2'>". $tableauDonnees[$j]['prenom'].' '.$tableauDonnees[$j]['nom'] .' ('.$tableauDonnees[$j]['numero_dossier'].")</td> </tr>";
					}
		
					$html .="<tr> <th class='enTitre'> <div>". $tableauDonnees[$j]['Designation'] ."<span class='examenSaveTick_".$tableauDonnees[$j]['iddemande']."'> </span></div> </th> </tr>";
				    
					
					$tableauDemandes [] = $tableauDonnees[$j]['iddemande'];
					$tableauAnalyses [] = $tableauDonnees[$j]['Idanalyse'];
					//Pour la reconnaissance de l'analyse demandée
					$html .="<tr><td><table style='width: 100%; margin-left: 0px;'  class='listeAnalyseTAD  ER_".$tableauDonnees[$j]['iddemande']."' >";
					
					
					if($tableauDonnees[$j]['Idanalyse'] ==  1){ $html .= $this->nfs_1();                   }
					if($tableauDonnees[$j]['Idanalyse'] ==  2){ $html .= $this->gsrh_groupage_2();         }
	                if($tableauDonnees[$j]['Idanalyse'] ==  3){ $html .= $this->recherche_antigene_3();    }
	                if($tableauDonnees[$j]['Idanalyse'] ==  4){ $html .= $this->test_combs_direct_4();    $html .= $this->getResultatsTestCombsDirect($tableauDonnees[$j]['iddemande']);   }
	                if($tableauDonnees[$j]['Idanalyse'] ==  5){ $html .= $this->test_combs_indirect_5();  $html .= $this->getResultatsTestCombsIndirect($tableauDonnees[$j]['iddemande']); }
	                if($tableauDonnees[$j]['Idanalyse'] ==  6){ $html .= $this->test_compatibilite_6();   $html .= $this->getResultatsTestCompatibilite($tableauDonnees[$j]['iddemande']); }
	                if($tableauDonnees[$j]['Idanalyse'] ==  7){ $html .= $this->vitesse_sedimentation_7(); }
	                if($tableauDonnees[$j]['Idanalyse'] ==  8){ $html .= $this->test_demmel_8();           }
	                if($tableauDonnees[$j]['Idanalyse'] ==  9){ $html .= $this->taux_reticulocytes_9();    }
	                if($tableauDonnees[$j]['Idanalyse'] == 10){ $html .= $this->goutte_epaisse_10();      $html .= $this->getResultatsGoutteEpaisse($tableauDonnees[$j]['iddemande']); }
	                 
	                   if($tableauDonnees[$j]['Idanalyse'] == 11){ $html .= $this->adenogramme_11();                 }
	                   if($tableauDonnees[$j]['Idanalyse'] == 12){ $html .= $this->medulodramme_12();                }
	                   if($tableauDonnees[$j]['Idanalyse'] == 13){ $html .= $this->cytochimie_myeloperoxydase_13();  }
	                
	                if($tableauDonnees[$j]['Idanalyse'] == 14){ $html .= $this->tp_inr_14();               }
	                if($tableauDonnees[$j]['Idanalyse'] == 15){ $html .= $this->tca_15();                  }
	                if($tableauDonnees[$j]['Idanalyse'] == 16){ $html .= $this->fibrinemie_16();           }
	                if($tableauDonnees[$j]['Idanalyse'] == 17){ $html .= $this->temps_saignement_17();     }
	                
	                   if($tableauDonnees[$j]['Idanalyse'] == 18){ $html .= $this->facteur_viii_18(); }
	                   if($tableauDonnees[$j]['Idanalyse'] == 19){ $html .= $this->facteur_ix_19();   }
	                   if($tableauDonnees[$j]['Idanalyse'] == 20){ $html .= $this->dimeres_20();      }
	                
	                if($tableauDonnees[$j]['Idanalyse'] == 21){ $html .= $this->glycemie_21();             }
	                if($tableauDonnees[$j]['Idanalyse'] == 22){ $html .= $this->creatininemie_22();        }
	                if($tableauDonnees[$j]['Idanalyse'] == 23){ $html .= $this->azotemie_23();             }
	                if($tableauDonnees[$j]['Idanalyse'] == 24){ $html .= $this->acide_urique_24();         }
	                if($tableauDonnees[$j]['Idanalyse'] == 25){ $html .= $this->cholesterol_total_25();    }
	                if($tableauDonnees[$j]['Idanalyse'] == 26){ $html .= $this->triglycerides_26();        } 
	                if($tableauDonnees[$j]['Idanalyse'] == 27){ $html .= $this->cholesterol_HDL_27();      }
	                if($tableauDonnees[$j]['Idanalyse'] == 28){ $html .= $this->cholesterol_LDL_28();      }
	                if($tableauDonnees[$j]['Idanalyse'] == 29){
	                    $html .="<tr> <th class='enTitre'> <div> CHOLESTEROL TOTAL </div> </th> </tr>";
	                    $html .= $this->cholesterol_total_25();
	                    $html .="<tr> <th class='enTitre'> <div> CHOLESTEROL HDL </div> </th> </tr>";
	                    $html .= $this->cholesterol_HDL_27();
	                    $html .="<tr> <th class='enTitre'> <div> CHOLESTEROL LDL </div> </th> </tr>";
	                    $html .= $this->cholesterol_LDL_28();
	                    $html .="<tr> <th class='enTitre'> <div> TRIGLYCERIDES </div> </th> </tr>";
	                    $html .= $this->triglycerides_26();
	                }
	                   if($tableauDonnees[$j]['Idanalyse'] == 30){ $html .= $this->lipides_totaux_30(); }
	                if($tableauDonnees[$j]['Idanalyse'] == 31){ $html .= $this->ionogramme_31();           }
	                if($tableauDonnees[$j]['Idanalyse'] == 32){ $html .= $this->calcemie_32();             }
	                if($tableauDonnees[$j]['Idanalyse'] == 33){ $html .= $this->magnesemie_33();           } 
	                if($tableauDonnees[$j]['Idanalyse'] == 34){ $html .= $this->phosphoremie_34();         }
	                if($tableauDonnees[$j]['Idanalyse'] == 35){ $html .= $this->tgo_asat_35();             }
	                if($tableauDonnees[$j]['Idanalyse'] == 36){ $html .= $this->tgp_alat_36();             }

	                   if($tableauDonnees[$j]['Idanalyse'] == 37){
	                      $html .="<tr> <th class='enTitre'> <div> ASAT </div> </th> </tr>";
	                      $html .= $this->tgo_asat_35();
	                      $html .="<tr> <th class='enTitre'> <div> ALAT </div> </th> </tr>";
 	                      $html .= $this->tgp_alat_36();
	                   }
	                
	                if($tableauDonnees[$j]['Idanalyse'] == 38){ $html .= $this->phosphatage_alcaline_38(); }
	                if($tableauDonnees[$j]['Idanalyse'] == 39){ $html .= $this->gama_gt_ygt_39();          }
	                if($tableauDonnees[$j]['Idanalyse'] == 40){ $html .= $this->fer_serique_40();          }
	                if($tableauDonnees[$j]['Idanalyse'] == 41){ $html .= $this->ferritinine_41();          }
	                if($tableauDonnees[$j]['Idanalyse'] == 42){ $html .= $this->bilirubine_totale_directe_42();  }
	                if($tableauDonnees[$j]['Idanalyse'] == 43){ $html .= $this->hemoglobine_glyquee_hbac_43();   } 
	                if($tableauDonnees[$j]['Idanalyse'] == 44){ $html .= $this->electrophorese_hemoglobine_44(); }
	                if($tableauDonnees[$j]['Idanalyse'] == 45){ $html .= $this->electrophorese_preteines_45();   }
	                if($tableauDonnees[$j]['Idanalyse'] == 46){ $html .= $this->albuminemie_46();                }
	                if($tableauDonnees[$j]['Idanalyse'] == 47){ $html .= $this->albumine_urinaire_47();     $html .= $this->getResultatsAlbumineUrinaire($tableauDonnees[$j]['iddemande']);       }
	                if($tableauDonnees[$j]['Idanalyse'] == 48){ $html .= $this->protidemie_48();                 }
	                if($tableauDonnees[$j]['Idanalyse'] == 49){ $html .= $this->proteinurie_49();                }
	                if($tableauDonnees[$j]['Idanalyse'] == 50){ $html .= $this->hlm_compte_daddis_50();          }
	                if($tableauDonnees[$j]['Idanalyse'] == 51){ $html .= $this->beta_hcg_plasmatique_51();       }
	                if($tableauDonnees[$j]['Idanalyse'] == 52){ $html .= $this->psa_52();                        }
	                if($tableauDonnees[$j]['Idanalyse'] == 53){ $html .= $this->crp_53();                        }
	                if($tableauDonnees[$j]['Idanalyse'] == 54){ $html .= $this->facteurs_rhumatoides_54();       }
	                if($tableauDonnees[$j]['Idanalyse'] == 55){ $html .= $this->rf_waaler_rose_55();             }
	                if($tableauDonnees[$j]['Idanalyse'] == 56){ $html .= $this->toxoplasmose_56();               }
	                if($tableauDonnees[$j]['Idanalyse'] == 57){ $html .= $this->rubeole_57();                    }
	                if($tableauDonnees[$j]['Idanalyse'] == 58){ $html .= $this->culot_urinaire_58();             }
	                if($tableauDonnees[$j]['Idanalyse'] == 59){ $html .= $this->serologie_chlamydiae_59();       }
	                if($tableauDonnees[$j]['Idanalyse'] == 60){ $html .= $this->serologie_syphilitique_60();     }
	                if($tableauDonnees[$j]['Idanalyse'] == 61){ $html .= $this->aslo_61();                       }
	                if($tableauDonnees[$j]['Idanalyse'] == 62){ $html .= $this->widal_62();                      }
	                if($tableauDonnees[$j]['Idanalyse'] == 63){ $html .= $this->ag_hbs_63();                     }
	                if($tableauDonnees[$j]['Idanalyse'] == 64){ $html .= $this->hiv_64();                        }
	                if($tableauDonnees[$j]['Idanalyse'] == 65){ $html .= $this->pv_65(); }
	                if($tableauDonnees[$j]['Idanalyse'] == 66){ $html .= $this->ecbu_66(); }
	                if($tableauDonnees[$j]['Idanalyse'] == 67){ $html .= $this->pus_67(); }
	                if($tableauDonnees[$j]['Idanalyse'] == 68){ $html .= $this->typage_hemoglobine_68(); } 
	                
	                if($tableauDonnees[$j]['Idanalyse'] == 70){ $html .= $this->ldh_70(); }
	                
					
	                $html .="</table></td></tr>";
				}
				
			}
			
			$html .="<tr><td style='height: 20px;'></td></tr></table>";
		}
		//Demande d'analyse et nombre de patients en info bulle
		$nbPatient = count($tableauPatients);
		if($nbPatient == 0 ){ $nbPatient = $nbPatient.' patient'; $html .="--- Aucun ---"; }
		else if($nbPatient == 1){ $nbPatient = $nbPatient.' patient'; }else{ $nbPatient = $nbPatient.' patients'; }
		$html .="<script> $('#infosNbPatientParType img').attr('title', '".$nbPatient."'); </script>";
		
		//Liste des analyses demandées dans la liste des analyses existantes
		//Liste des analyses demandées dans la liste des analyses existantes
		$listeAnalysesTypeGroup = $this->getAnalyseTable() ->getListeAnalysesDemandeesParTypeGroupeAnalyse($idtype);
		$listeAnalysesTab = array();
		foreach ($listeAnalysesTypeGroup as $liste){ $listeAnalysesTab[] = $liste['Idanalyse']; }
	
		$liste_select = "<option>  </option>";
		foreach($this->getPatientTable()->getListeDesAnalyses($idtype) as $listeAnalyses){
		    if(in_array($listeAnalyses['idanalyse'], $listeAnalysesTab)){
		        $liste_select.= "<option style=\'color: red;\' value=".$listeAnalyses['idanalyse']." > ".str_replace("'", "\'", $listeAnalyses['designation'])." </option>";
		    }else {
		        $liste_select.= "<option value=".$listeAnalyses['idanalyse']." > ".str_replace("'", "\'", $listeAnalyses['designation'])." </option>";
		    }
		}
		
		//Liste des dates de demandes d'analyses
		//Liste des dates de demandes d'analyses
		$listeAnalysesTypeGroupDate = $this->getAnalyseTable() ->getListeAnalysesDemandeesParTypeGroupeDate($idtype);
		$listeAnalysesTabDate = array();
		$control = new DateHelper();
		$liste_date = "<option>  </option>";
		foreach ($listeAnalysesTypeGroupDate as $liste){
		    $aujourdhui = (new \DateTime() ) ->format('d/m/Y');
		    $hier = date("d/m/Y", strtotime('-1 day'));
		    
		    $laDate = $control->convertDate($liste['date']);
		    if($laDate == $aujourdhui){ $laDate = "Aujourd\'hui"; }
		    elseif($laDate == $hier){ $laDate = "Hier"; }
		    
		    $liste_date.= "<option value=".$liste['date']." > ".$laDate." </option>";
		}
		
		$html .="<script> $('#listeAnalyseParType').html('".$liste_select."'); </script>";
		$html .="<script> $('#listeAnalyseParTypeParDate').html('".$liste_date."'); </script>";
	
		
		//Récupération de la liste des demandes, pour savoir les patients pour qui on a entré des résultats
		$html .="<script> var listeDesDemandesSelect = []; </script>";
		$html .="<script> var listeDesAnalysesSelect = []; </script>";
		for($i = 0 ; $i < count($tableauDemandes) ; $i++){
		    $html .="<script> listeDesDemandesSelect[".$i."]=".$tableauDemandes[$i]."; </script>";
		    $html .="<script> listeDesAnalysesSelect[".$i."]=".$tableauAnalyses[$i]."; </script>";
		}
		
		
		//Récupération de la liste des codes des patients
		//Récupération de la liste des codes des patients
		$liste_code= "<option>  </option>";
		for($i = 0 ; $i < count($tableauPatients) ; $i++){
		    $liste_code.= "<option value=".$tableauPatients[$i]." > ".$tableauNumerosDossiers[$i]."</option>";
		}
		$html .="<script> $('#listeCodesDesPatients').html('".$liste_code."'); </script>";
		
		
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $html ) );
	}
	
	//****** ========== RECUPERER LES RESULTATS DES ANALYSES DEMANDEES PAR TYPE PAR ANALYSE ========== ******
	//****** ========== RECUPERER LES RESULTATS DES ANALYSES DEMANDEES PAR TYPE PAR ANALYSE ========== ******
	//****** ========== RECUPERER LES RESULTATS DES ANALYSES DEMANDEES PAR TYPE PAR ANALYSE ========== ******
	public function recupererLesAnalysesDemandeesParTypeEtAnalyseAction()
	{
	    $idtype = ( int ) $this->params ()->fromPost ( 'idtype', 0 );
	    $idanalyse = ( int ) $this->params ()->fromPost ( 'idanalyse', 0 );
	
	    $listeAnalysesType = null;
	    if($idanalyse == 0){
	        $listeAnalysesType = $this->getAnalyseTable() ->getListeAnalysesDemandeesParType($idtype);
	    }else{ 
	        $listeAnalysesType = $this->getAnalyseTable() ->getListeAnalysesDemandeesParTypeEtAnalyse($idtype, $idanalyse); 
	    }
	
	    $tableauDonnees = array();
	    $tableauPatients = array();
	    $tableauDemandes = array();
	    $tableauAnalyses = array();
	    $tableauNumerosDossiers = array();
	
	    foreach ($listeAnalysesType as $liste){
	        $tableauDonnees[] = $liste;
	        if( !in_array($liste['idpersonne'], $tableauPatients) ) { $tableauPatients[] = $liste['idpersonne']; $tableauNumerosDossiers[] = $liste['numero_dossier'];}
	    }
	
	    $html ="";
	
	    for($i = 0 ; $i < count($tableauPatients) ; $i++){
	        $k = 0 ;
	
	        $html .="<table class='designEnTeteAnalyse  patientListeAnalyse  pat_".$tableauPatients[$i]."' style='width: 100%;' >";
	        for ($j = 0 ; $j < count($tableauDonnees) ; $j++){
	
	            if($tableauDonnees[$j]['idpersonne'] == $tableauPatients[$i]){
	                	
	                if($k == 0){
	                    $k++;
	                    $html .="<tr style='width: 90%;' > <td class='enTete2'>". $tableauDonnees[$j]['prenom'].' '.$tableauDonnees[$j]['nom'] .' ('.$tableauDonnees[$j]['numero_dossier'].") </td> </tr>";
	                }
	
	                $html .="<tr> <th class='enTitre'> <div>". $tableauDonnees[$j]['Designation'] ."<span class='examenSaveTick_".$tableauDonnees[$j]['iddemande']."'> </span></div> </th> </tr>";
	                
	                	
	                $tableauDemandes [] = $tableauDonnees[$j]['iddemande'];
	                $tableauAnalyses [] = $tableauDonnees[$j]['Idanalyse'];
	                //Pour la reconnaissance de l'analyse demandée
	                $html .="<tr><td><table style='width: 100%; margin-left: 0px;'  class='listeAnalyseTAD  ER_".$tableauDonnees[$j]['iddemande']."' >";
	                 
	                
	                if($tableauDonnees[$j]['Idanalyse'] ==  1){ $html .= $this->nfs_1();                   }
	                if($tableauDonnees[$j]['Idanalyse'] ==  2){ $html .= $this->gsrh_groupage_2();         }
	                if($tableauDonnees[$j]['Idanalyse'] ==  3){ $html .= $this->recherche_antigene_3();    }
	                if($tableauDonnees[$j]['Idanalyse'] ==  4){ $html .= $this->test_combs_direct_4();    $html .= $this->getResultatsTestCombsDirect($tableauDonnees[$j]['iddemande']); }
	                if($tableauDonnees[$j]['Idanalyse'] ==  5){ $html .= $this->test_combs_indirect_5();  $html .= $this->getResultatsTestCombsIndirect($tableauDonnees[$j]['iddemande']); }
	                if($tableauDonnees[$j]['Idanalyse'] ==  6){ $html .= $this->test_compatibilite_6();   $html .= $this->getResultatsTestCompatibilite($tableauDonnees[$j]['iddemande']);  }
	                if($tableauDonnees[$j]['Idanalyse'] ==  7){ $html .= $this->vitesse_sedimentation_7(); }
	                if($tableauDonnees[$j]['Idanalyse'] ==  8){ $html .= $this->test_demmel_8();           }
	                if($tableauDonnees[$j]['Idanalyse'] ==  9){ $html .= $this->taux_reticulocytes_9();    }
	                if($tableauDonnees[$j]['Idanalyse'] == 10){ $html .= $this->goutte_epaisse_10();      $html .= $this->getResultatsGoutteEpaisse($tableauDonnees[$j]['iddemande']); }
	                 
	                   if($tableauDonnees[$j]['Idanalyse'] == 11){ $html .= $this->adenogramme_11();                 }
	                   if($tableauDonnees[$j]['Idanalyse'] == 12){ $html .= $this->medulodramme_12();                }
	                   if($tableauDonnees[$j]['Idanalyse'] == 13){ $html .= $this->cytochimie_myeloperoxydase_13();  }
	                
	                if($tableauDonnees[$j]['Idanalyse'] == 14){ $html .= $this->tp_inr_14();               }
	                if($tableauDonnees[$j]['Idanalyse'] == 15){ $html .= $this->tca_15();                  }
	                if($tableauDonnees[$j]['Idanalyse'] == 16){ $html .= $this->fibrinemie_16();           }
	                if($tableauDonnees[$j]['Idanalyse'] == 17){ $html .= $this->temps_saignement_17();     }
	                 
	                   if($tableauDonnees[$j]['Idanalyse'] == 18){ $html .= $this->facteur_viii_18(); }
	                   if($tableauDonnees[$j]['Idanalyse'] == 19){ $html .= $this->facteur_ix_19();   }
	                   if($tableauDonnees[$j]['Idanalyse'] == 20){ $html .= $this->dimeres_20();      }
	                
	                if($tableauDonnees[$j]['Idanalyse'] == 21){ $html .= $this->glycemie_21();             }
	                if($tableauDonnees[$j]['Idanalyse'] == 22){ $html .= $this->creatininemie_22();        }
	                if($tableauDonnees[$j]['Idanalyse'] == 23){ $html .= $this->azotemie_23();             }
	                if($tableauDonnees[$j]['Idanalyse'] == 24){ $html .= $this->acide_urique_24();         }
	                if($tableauDonnees[$j]['Idanalyse'] == 25){ $html .= $this->cholesterol_total_25();    }
	                if($tableauDonnees[$j]['Idanalyse'] == 26){ $html .= $this->triglycerides_26();        }
	                if($tableauDonnees[$j]['Idanalyse'] == 27){ $html .= $this->cholesterol_HDL_27();      }
	                if($tableauDonnees[$j]['Idanalyse'] == 28){ $html .= $this->cholesterol_LDL_28();      }
	                if($tableauDonnees[$j]['Idanalyse'] == 29){
	                    $html .="<tr> <th class='enTitre'> <div> CHOLESTEROL TOTAL </div> </th> </tr>";
	                    $html .= $this->cholesterol_total_25();
	                    $html .="<tr> <th class='enTitre'> <div> CHOLESTEROL HDL </div> </th> </tr>";
	                    $html .= $this->cholesterol_HDL_27();
	                    $html .="<tr> <th class='enTitre'> <div> CHOLESTEROL LDL </div> </th> </tr>";
	                    $html .= $this->cholesterol_LDL_28();
	                    $html .="<tr> <th class='enTitre'> <div> TRIGLYCERIDES </div> </th> </tr>";
	                    $html .= $this->triglycerides_26();
	                }
	                   if($tableauDonnees[$j]['Idanalyse'] == 30){ $html .= $this->lipides_totaux_30();    }
	                if($tableauDonnees[$j]['Idanalyse'] == 31){ $html .= $this->ionogramme_31();           }
	                if($tableauDonnees[$j]['Idanalyse'] == 32){ $html .= $this->calcemie_32();             }
	                if($tableauDonnees[$j]['Idanalyse'] == 33){ $html .= $this->magnesemie_33();           }
	                if($tableauDonnees[$j]['Idanalyse'] == 34){ $html .= $this->phosphoremie_34();         }
	                if($tableauDonnees[$j]['Idanalyse'] == 35){ $html .= $this->tgo_asat_35();             }
	                if($tableauDonnees[$j]['Idanalyse'] == 36){ $html .= $this->tgp_alat_36();             }
	                
	                   if($tableauDonnees[$j]['Idanalyse'] == 37){
	                      $html .="<tr> <th class='enTitre'> <div> ASAT </div> </th> </tr>";
	                      $html .= $this->tgo_asat_35();
	                      $html .="<tr> <th class='enTitre'> <div> ALAT </div> </th> </tr>";
	                      $html .= $this->tgp_alat_36();
	                   }
	                
	                if($tableauDonnees[$j]['Idanalyse'] == 38){ $html .= $this->phosphatage_alcaline_38(); }
	                if($tableauDonnees[$j]['Idanalyse'] == 39){ $html .= $this->gama_gt_ygt_39();          }
	                if($tableauDonnees[$j]['Idanalyse'] == 40){ $html .= $this->fer_serique_40();          }
	                if($tableauDonnees[$j]['Idanalyse'] == 41){ $html .= $this->ferritinine_41();          }
	                if($tableauDonnees[$j]['Idanalyse'] == 42){ $html .= $this->bilirubine_totale_directe_42();  }
	                if($tableauDonnees[$j]['Idanalyse'] == 43){ $html .= $this->hemoglobine_glyquee_hbac_43();   }
	                if($tableauDonnees[$j]['Idanalyse'] == 44){ $html .= $this->electrophorese_hemoglobine_44(); }
	                if($tableauDonnees[$j]['Idanalyse'] == 45){ $html .= $this->electrophorese_preteines_45();   }
	                if($tableauDonnees[$j]['Idanalyse'] == 46){ $html .= $this->albuminemie_46();                }
	                if($tableauDonnees[$j]['Idanalyse'] == 47){ $html .= $this->albumine_urinaire_47();  $html .= $this->getResultatsAlbumineUrinaire($tableauDonnees[$j]['iddemande']); }
	                if($tableauDonnees[$j]['Idanalyse'] == 48){ $html .= $this->protidemie_48();                 }
	                if($tableauDonnees[$j]['Idanalyse'] == 49){ $html .= $this->proteinurie_49();                }
	                if($tableauDonnees[$j]['Idanalyse'] == 50){ $html .= $this->hlm_compte_daddis_50();          }
	                if($tableauDonnees[$j]['Idanalyse'] == 51){ $html .= $this->beta_hcg_plasmatique_51();       }
	                if($tableauDonnees[$j]['Idanalyse'] == 52){ $html .= $this->psa_52();                        }
	                if($tableauDonnees[$j]['Idanalyse'] == 53){ $html .= $this->crp_53();                        }
	                if($tableauDonnees[$j]['Idanalyse'] == 54){ $html .= $this->facteurs_rhumatoides_54();       }
	                if($tableauDonnees[$j]['Idanalyse'] == 55){ $html .= $this->rf_waaler_rose_55();             }
	                if($tableauDonnees[$j]['Idanalyse'] == 56){ $html .= $this->toxoplasmose_56();               }
	                if($tableauDonnees[$j]['Idanalyse'] == 57){ $html .= $this->rubeole_57();                    }
	                if($tableauDonnees[$j]['Idanalyse'] == 58){ $html .= $this->culot_urinaire_58();             }
	                if($tableauDonnees[$j]['Idanalyse'] == 59){ $html .= $this->serologie_chlamydiae_59();       }
	                if($tableauDonnees[$j]['Idanalyse'] == 60){ $html .= $this->serologie_syphilitique_60();     }
	                if($tableauDonnees[$j]['Idanalyse'] == 61){ $html .= $this->aslo_61();                       }
	                if($tableauDonnees[$j]['Idanalyse'] == 62){ $html .= $this->widal_62();                      }
	                if($tableauDonnees[$j]['Idanalyse'] == 63){ $html .= $this->ag_hbs_63();                     }
	                if($tableauDonnees[$j]['Idanalyse'] == 64){ $html .= $this->hiv_64();                        }
	                if($tableauDonnees[$j]['Idanalyse'] == 65){ $html .= $this->pv_65(); }
	                if($tableauDonnees[$j]['Idanalyse'] == 66){ $html .= $this->ecbu_66(); }
	                if($tableauDonnees[$j]['Idanalyse'] == 67){ $html .= $this->pus_67(); }
	                if($tableauDonnees[$j]['Idanalyse'] == 68){ $html .= $this->typage_hemoglobine_68(); }
	                 
	                if($tableauDonnees[$j]['Idanalyse'] == 70){ $html .= $this->ldh_70(); }
	                
	                
	                $html .="</table></td></tr>";
	            }
	
	        }
	        	
	        $html .="<tr><td style='height: 20px;'></td></tr></table>";
	    }
	
	    if($idanalyse != 0){
	        $nbPatient = count($tableauPatients);
	        if($nbPatient == 0 ){ $nbPatient = $nbPatient.' patient'; $html .="--- Aucun ---"; }
	        else if($nbPatient == 1 ){ $nbPatient = $nbPatient.' patient'; }
	        else { $nbPatient = $nbPatient.' patients'; }
	        $html .="<script> $('#infosNbPatientParAnalyse img').attr('title', '".$nbPatient."').css({'opacity' : '1'}); </script>";
	    }else{
	        $html .="<script> $('#infosNbPatientParAnalyse img').attr('title', '').css({'opacity' : '0'}); </script>";
	    }
	    
	    
	    //Liste des analyses demandées dans la liste des analyses existantes
	    //Liste des analyses demandées dans la liste des analyses existantes
	    $listeAnalysesTypeGroup = $this->getAnalyseTable() ->getListeAnalysesDemandeesParTypeGroupeAnalyse($idtype);
	    $listeAnalysesTab = array();
	    foreach ($listeAnalysesTypeGroup as $liste){ $listeAnalysesTab[] = $liste['Idanalyse']; }
	    
	    $liste_select = "<option>  </option>";
	    foreach($this->getPatientTable()->getListeDesAnalyses($idtype) as $listeAnalyses){
	    	if(in_array($listeAnalyses['idanalyse'], $listeAnalysesTab)){
	    		$liste_select.= "<option style=\'color: red;\' value=".$listeAnalyses['idanalyse']." > ".str_replace("'", "\'", $listeAnalyses['designation'])." </option>";
	    	}else {
	    		$liste_select.= "<option value=".$listeAnalyses['idanalyse']." > ".str_replace("'", "\'", $listeAnalyses['designation'])." </option>";
	    	}
	    }
	    $html .="<script> $('#listeAnalyseParType').html('".$liste_select."'); $('#listeAnalyseParType').val('".$idanalyse."'); </script>";
	    
	    //Liste des dates de demandes d'analyses
	    //Liste des dates de demandes d'analyses
	    $listeAnalysesTypeGroupDate = null;
	    if($idanalyse == 0){
	        $listeAnalysesTypeGroupDate = $this->getAnalyseTable() ->getListeAnalysesDemandeesParTypeGroupeDate($idtype);
	    }else{
	        $listeAnalysesTypeGroupDate = $this->getAnalyseTable() ->getListeAnalysesDemandeesParTypeEtParAnalyseGroupeDate($idtype, $idanalyse);
	    }

	    $listeAnalysesTabDate = array();
	    $control = new DateHelper();
	    $liste_date = "<option>  </option>";
	    foreach ($listeAnalysesTypeGroupDate as $liste){
	        $aujourdhui = (new \DateTime() ) ->format('d/m/Y');
	        $hier = date("d/m/Y", strtotime('-1 day'));
	        
	        $laDate = $control->convertDate($liste['date']);
	        if($laDate == $aujourdhui){ $laDate = "Aujourd\'hui"; }
	        elseif ($laDate == $hier){ $laDate = "Hier"; }
	        
	        $liste_date.= "<option value=".$liste['date']." > ".$laDate." </option>";
	    }
	    $html .="<script> $('#listeAnalyseParTypeParDate').html('".$liste_date."'); </script>";
	    
	    
	    //Récupération de la liste des demandes, pour savoir les patients pour qui on a entré des résultats
	    $html .="<script> var listeDesDemandesSelect = []; </script>";
	    $html .="<script> var listeDesAnalysesSelect = []; </script>";
	    for($i = 0 ; $i < count($tableauDemandes) ; $i++){
	    	$html .="<script> listeDesDemandesSelect[".$i."]=".$tableauDemandes[$i]."; </script>";
	    	$html .="<script> listeDesAnalysesSelect[".$i."]=".$tableauAnalyses[$i]."; </script>";
	    }
	    
	    
	    
	    //Récupération de la liste des codes des patients
	    //Récupération de la liste des codes des patients
	    $liste_code= "<option>  </option>";
	    for($i = 0 ; $i < count($tableauPatients) ; $i++){
	        $liste_code.= "<option value=".$tableauPatients[$i]." > ".$tableauNumerosDossiers[$i]."</option>";
	    }
	    $html .="<script> $('#listeCodesDesPatients').html('".$liste_code."'); </script>";
	    
	    $this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
	    return $this->getResponse ()->setContent ( Json::encode ( $html ) );
	}
	
	//****** ========== RECUPERER LES RESULTATS DES ANALYSES DEMANDEES PAR TYPE PAR ANALYSE ET DATE ========== ******
	//****** ========== RECUPERER LES RESULTATS DES ANALYSES DEMANDEES PAR TYPE PAR ANALYSE ET DATE ========== ******
	//****** ========== RECUPERER LES RESULTATS DES ANALYSES DEMANDEES PAR TYPE PAR ANALYSE ET DATE ========== ******
	public function recupererLesAnalysesDemandeesParTypeEtAnalyseEtDateAction()
	{
	 
	    $idtype = ( int ) $this->params ()->fromPost ( 'idtype', 0 );
	    $idanalyse = ( int ) $this->params ()->fromPost ( 'idanalyse', 0 );
	    $date = $this->params ()->fromPost ( 'date' );
	    
	    $html ="";
	    $listeAnalysesType = null;
	    
	    if($idanalyse && $date){
	        $listeAnalysesType = $this->getAnalyseTable() ->getListeAnalysesDemandeesParTypeEtAnalyseEtDate($idtype, $idanalyse, $date);
	    }elseif ($idanalyse && !$date){
	        $listeAnalysesType = $this->getAnalyseTable() ->getListeAnalysesDemandeesParTypeEtAnalyse($idtype, $idanalyse);
	    }elseif (!$idanalyse && $date){
	        $listeAnalysesType = $this->getAnalyseTable() ->getListeAnalysesDemandeesParTypeEtDate($idtype, $date);
	    }elseif (!$idanalyse && !$date){
	        $listeAnalysesType = $this->getAnalyseTable() ->getListeAnalysesDemandeesParType($idtype);
	    }
	    
	    
	    $tableauDonnees = array();
	    $tableauPatients = array();
	    $tableauDemandes = array();
	    $tableauAnalyses = array();
	    $tableauNumerosDossiers = array();
	    
	    if($listeAnalysesType){
	        foreach ($listeAnalysesType as $liste){
	            $tableauDonnees[] = $liste;
	            if( !in_array($liste['idpersonne'], $tableauPatients) ) { $tableauPatients[] = $liste['idpersonne']; $tableauNumerosDossiers[] = $liste['numero_dossier']; }
	        }
	         
	        for($i = 0 ; $i < count($tableauPatients) ; $i++){
	            $k = 0 ;
	             
	            $html .="<table class='designEnTeteAnalyse  patientListeAnalyse  pat_".$tableauPatients[$i]."' style='width: 100%;' >";
	            
	            for ($j = 0 ; $j < count($tableauDonnees) ; $j++){
	                 
	                if($tableauDonnees[$j]['idpersonne'] == $tableauPatients[$i]){
	                     
	                    if($k == 0){
	                        $k++;
	                        $html .="<tr style='width: 90%;' > <td class='enTete2'>". $tableauDonnees[$j]['prenom'].' '.$tableauDonnees[$j]['nom'] .' ('.$tableauDonnees[$j]['numero_dossier'].")</td> </tr>";
	                    }
	                     
	                    $html .="<tr> <th class='enTitre'> <div>". $tableauDonnees[$j]['Designation'] ."<span class='examenSaveTick_".$tableauDonnees[$j]['iddemande']."'> </span></div> </th> </tr>";
	                    
	                    $tableauDemandes [] = $tableauDonnees[$j]['iddemande'];
	                    $tableauAnalyses [] = $tableauDonnees[$j]['Idanalyse'];
	                    //Pour la reconnaissance de l'analyse demandée
	                    $html .="<tr><td><table style='width: 100%; margin-left: 0px;'  class='listeAnalyseTAD  ER_".$tableauDonnees[$j]['iddemande']."' >";
	                    
	                    if($tableauDonnees[$j]['Idanalyse'] ==  1){ $html .= $this->nfs_1();                   }
	                    if($tableauDonnees[$j]['Idanalyse'] ==  2){ $html .= $this->gsrh_groupage_2();         }
	                    if($tableauDonnees[$j]['Idanalyse'] ==  3){ $html .= $this->recherche_antigene_3();    }
	                    if($tableauDonnees[$j]['Idanalyse'] ==  4){ $html .= $this->test_combs_direct_4();      $html .= $this->getResultatsTestCombsDirect($tableauDonnees[$j]['iddemande']);   }
	                    if($tableauDonnees[$j]['Idanalyse'] ==  5){ $html .= $this->test_combs_indirect_5();    $html .= $this->getResultatsTestCombsIndirect($tableauDonnees[$j]['iddemande']); }
	                    if($tableauDonnees[$j]['Idanalyse'] ==  6){ $html .= $this->test_compatibilite_6();     $html .= $this->getResultatsTestCompatibilite($tableauDonnees[$j]['iddemande']); }
	                    if($tableauDonnees[$j]['Idanalyse'] ==  7){ $html .= $this->vitesse_sedimentation_7(); }
	                    if($tableauDonnees[$j]['Idanalyse'] ==  8){ $html .= $this->test_demmel_8();           }
	                    if($tableauDonnees[$j]['Idanalyse'] ==  9){ $html .= $this->taux_reticulocytes_9();    }
	                    if($tableauDonnees[$j]['Idanalyse'] == 10){ $html .= $this->goutte_epaisse_10();       $html .= $this->getResultatsGoutteEpaisse($tableauDonnees[$j]['iddemande']);     }
	                     
    	                   if($tableauDonnees[$j]['Idanalyse'] == 11){ $html .= $this->adenogramme_11();                 }
	                       if($tableauDonnees[$j]['Idanalyse'] == 12){ $html .= $this->medulodramme_12();                }
	                       if($tableauDonnees[$j]['Idanalyse'] == 13){ $html .= $this->cytochimie_myeloperoxydase_13();  }
	                    
	                    if($tableauDonnees[$j]['Idanalyse'] == 14){ $html .= $this->tp_inr_14();               }
	                    if($tableauDonnees[$j]['Idanalyse'] == 15){ $html .= $this->tca_15();                  }
	                    if($tableauDonnees[$j]['Idanalyse'] == 16){ $html .= $this->fibrinemie_16();           }
	                    if($tableauDonnees[$j]['Idanalyse'] == 17){ $html .= $this->temps_saignement_17();     }
	                     
	                       if($tableauDonnees[$j]['Idanalyse'] == 18){ $html .= $this->facteur_viii_18(); }
	                       if($tableauDonnees[$j]['Idanalyse'] == 19){ $html .= $this->facteur_ix_19();   }
	                       if($tableauDonnees[$j]['Idanalyse'] == 20){ $html .= $this->dimeres_20();      }
	                    
	                    if($tableauDonnees[$j]['Idanalyse'] == 21){ $html .= $this->glycemie_21();             }
	                    if($tableauDonnees[$j]['Idanalyse'] == 22){ $html .= $this->creatininemie_22();        }
	                    if($tableauDonnees[$j]['Idanalyse'] == 23){ $html .= $this->azotemie_23();             }
	                    if($tableauDonnees[$j]['Idanalyse'] == 24){ $html .= $this->acide_urique_24();         }
	                    if($tableauDonnees[$j]['Idanalyse'] == 25){ $html .= $this->cholesterol_total_25();    }
	                    if($tableauDonnees[$j]['Idanalyse'] == 26){ $html .= $this->triglycerides_26();        }
	                    if($tableauDonnees[$j]['Idanalyse'] == 27){ $html .= $this->cholesterol_HDL_27();      }
	                    if($tableauDonnees[$j]['Idanalyse'] == 28){ $html .= $this->cholesterol_LDL_28();      }
	                    if($tableauDonnees[$j]['Idanalyse'] == 29){
	                        $html .="<tr> <th class='enTitre'> <div> CHOLESTEROL TOTAL </div> </th> </tr>";
	                        $html .= $this->cholesterol_total_25(); 
	                        $html .="<tr> <th class='enTitre'> <div> CHOLESTEROL HDL </div> </th> </tr>";
	                        $html .= $this->cholesterol_HDL_27();
	                        $html .="<tr> <th class='enTitre'> <div> CHOLESTEROL LDL </div> </th> </tr>";
	                        $html .= $this->cholesterol_LDL_28(); 
	                        $html .="<tr> <th class='enTitre'> <div> TRIGLYCERIDES </div> </th> </tr>";
	                        $html .= $this->triglycerides_26();
	                    }
	                    if($tableauDonnees[$j]['Idanalyse'] == 30){ $html .= $this->lipides_totaux_30();       }
	                    if($tableauDonnees[$j]['Idanalyse'] == 31){ $html .= $this->ionogramme_31();           }
	                    if($tableauDonnees[$j]['Idanalyse'] == 32){ $html .= $this->calcemie_32();             }
	                    if($tableauDonnees[$j]['Idanalyse'] == 33){ $html .= $this->magnesemie_33();           }
	                    if($tableauDonnees[$j]['Idanalyse'] == 34){ $html .= $this->phosphoremie_34();         }
	                    if($tableauDonnees[$j]['Idanalyse'] == 35){ $html .= $this->tgo_asat_35();             }
	                    if($tableauDonnees[$j]['Idanalyse'] == 36){ $html .= $this->tgp_alat_36();             }
	                    
	                       if($tableauDonnees[$j]['Idanalyse'] == 37){
	                          $html .="<tr> <th class='enTitre'> <div> ASAT </div> </th> </tr>";
	                          $html .= $this->tgo_asat_35();
	                          $html .="<tr> <th class='enTitre'> <div> ALAT </div> </th> </tr>";
	                          $html .= $this->tgp_alat_36();
	                       }
	                    
	                    if($tableauDonnees[$j]['Idanalyse'] == 38){ $html .= $this->phosphatage_alcaline_38(); }
	                    if($tableauDonnees[$j]['Idanalyse'] == 39){ $html .= $this->gama_gt_ygt_39();          }
	                    if($tableauDonnees[$j]['Idanalyse'] == 40){ $html .= $this->fer_serique_40();          }
	                    if($tableauDonnees[$j]['Idanalyse'] == 41){ $html .= $this->ferritinine_41();          }
	                    if($tableauDonnees[$j]['Idanalyse'] == 42){ $html .= $this->bilirubine_totale_directe_42();  }
	                    if($tableauDonnees[$j]['Idanalyse'] == 43){ $html .= $this->hemoglobine_glyquee_hbac_43();   }
	                    if($tableauDonnees[$j]['Idanalyse'] == 44){ $html .= $this->electrophorese_hemoglobine_44(); }
	                    if($tableauDonnees[$j]['Idanalyse'] == 45){ $html .= $this->electrophorese_preteines_45();   }
	                    if($tableauDonnees[$j]['Idanalyse'] == 46){ $html .= $this->albuminemie_46();                }
	                    if($tableauDonnees[$j]['Idanalyse'] == 47){ $html .= $this->albumine_urinaire_47();    $html .= $this->getResultatsAlbumineUrinaire($tableauDonnees[$j]['iddemande']);        }
	                    if($tableauDonnees[$j]['Idanalyse'] == 48){ $html .= $this->protidemie_48();                 }
	                    if($tableauDonnees[$j]['Idanalyse'] == 49){ $html .= $this->proteinurie_49();                }
	                    if($tableauDonnees[$j]['Idanalyse'] == 50){ $html .= $this->hlm_compte_daddis_50();          }
	                    if($tableauDonnees[$j]['Idanalyse'] == 51){ $html .= $this->beta_hcg_plasmatique_51();       }
	                    if($tableauDonnees[$j]['Idanalyse'] == 52){ $html .= $this->psa_52();                        }
	                    if($tableauDonnees[$j]['Idanalyse'] == 53){ $html .= $this->crp_53();                        }
	                    if($tableauDonnees[$j]['Idanalyse'] == 54){ $html .= $this->facteurs_rhumatoides_54();       }
	                    if($tableauDonnees[$j]['Idanalyse'] == 55){ $html .= $this->rf_waaler_rose_55();             }
	                    if($tableauDonnees[$j]['Idanalyse'] == 56){ $html .= $this->toxoplasmose_56();               }
	                    if($tableauDonnees[$j]['Idanalyse'] == 57){ $html .= $this->rubeole_57();                    }
	                    if($tableauDonnees[$j]['Idanalyse'] == 58){ $html .= $this->culot_urinaire_58();             }
	                    if($tableauDonnees[$j]['Idanalyse'] == 59){ $html .= $this->serologie_chlamydiae_59();       }
	                    if($tableauDonnees[$j]['Idanalyse'] == 60){ $html .= $this->serologie_syphilitique_60();     }
	                    if($tableauDonnees[$j]['Idanalyse'] == 61){ $html .= $this->aslo_61();                       }
	                    if($tableauDonnees[$j]['Idanalyse'] == 62){ $html .= $this->widal_62();                      }
	                    if($tableauDonnees[$j]['Idanalyse'] == 63){ $html .= $this->ag_hbs_63();                     }
	                    if($tableauDonnees[$j]['Idanalyse'] == 64){ $html .= $this->hiv_64();                        }
	                    if($tableauDonnees[$j]['Idanalyse'] == 65){ $html .= $this->pv_65(); }
	                    if($tableauDonnees[$j]['Idanalyse'] == 66){ $html .= $this->ecbu_66(); }
	                    if($tableauDonnees[$j]['Idanalyse'] == 67){ $html .= $this->pus_67(); }
	                    if($tableauDonnees[$j]['Idanalyse'] == 68){ $html .= $this->typage_hemoglobine_68(); }
	                     
	                    if($tableauDonnees[$j]['Idanalyse'] == 70){ $html .= $this->ldh_70(); }
	                    
	                    
	                    $html .="</table></td></tr>";
	                }
	                 
	            }
	             
	            $html .="<tr><td style='height: 20px;'></td></tr></table>";
	        }
	        if($date){
	            $nbPatient = count($tableauPatients);
	            if( $nbPatient == 1 ){ $nbPatient = $nbPatient.' patient'; } else { $nbPatient = $nbPatient.' patients'; }
	            $html .="<script> $('#infosNbPatientParAnalyseParDate img').attr('title', '".$nbPatient."').css({'opacity' : '1'}); </script>";
	        }else{
	            $html .="<script> $('#infosNbPatientParAnalyseParDate img').attr('title', '').css({'opacity' : '0'}); </script>";
	        }
	    }
	    

	    //Récupération de la liste des codes des patients
	    //Récupération de la liste des codes des patients
	    $liste_code= "<option>  </option>";
	    for($i = 0 ; $i < count($tableauPatients) ; $i++){
	        $liste_code.= "<option value=".$tableauPatients[$i]." > ".$tableauNumerosDossiers[$i]."</option>";
	    }
	    $html .="<script> $('#listeCodesDesPatients').html('".$liste_code."'); </script>";
	    
	    
	    
	    //Récupération de la liste des demandes, pour connaitre les demandes 
	    $html .="<script> var listeDesDemandesSelect = []; </script>";
	    $html .="<script> var listeDesAnalysesSelect = []; </script>";
	    for($i = 0 ; $i < count($tableauDemandes) ; $i++){
	    	$html .="<script> listeDesDemandesSelect[".$i."]=".$tableauDemandes[$i]."; </script>";
	    	$html .="<script> listeDesAnalysesSelect[".$i."]=".$tableauAnalyses[$i]."; </script>";
	    }
	    
	    
	    $this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
	    return $this->getResponse ()->setContent ( Json::encode ( $html ) );
	}
	
	
	public function getListeAnalysesAction()
	{
	    $id = (int)$this->params()->fromPost ('id');
	    $liste_select = "";
	    foreach($this->getPatientTable()->getListeDesAnalyses($id) as $listeAnalyses){
	        $liste_select.= "<option value=".$listeAnalyses['idanalyse'].">".$listeAnalyses['designation']."</option>";
	    }
	
	    $this->getResponse()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html' );
	    return $this->getResponse ()->setContent(Json::encode ( $liste_select));
	}
	
	
	
	/*
	 * GESTION DES CHAMPS DES DIFFERENTES ANALYSES *********** GESTION DES CHAMPS DES DIFFERENTES ANALYSES
	 * GESTION DES CHAMPS DES DIFFERENTES ANALYSES *********** GESTION DES CHAMPS DES DIFFERENTES ANALYSES
	 * GESTION DES CHAMPS DES DIFFERENTES ANALYSES *********** GESTION DES CHAMPS DES DIFFERENTES ANALYSES
	 * GESTION DES CHAMPS DES DIFFERENTES ANALYSES *********** GESTION DES CHAMPS DES DIFFERENTES ANALYSES
	 */
	/**
	 * analyse 1
	 */
	public function nfs_1(){
	    
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	    $html .= "<input id='nfs_1' type='hidden' value='1'>";
	    
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    $html .= "<tr class='ligneAnanlyse labelTypeMateriel' style='width: 100%; font-family: times new roman; font-size: 15px; margin-top: -45px;'>";
	    $html .= "  <td style='width: 55%;'> <label> Mat&eacute;riel utilis&eacute;</label> </td>";
	    $html .= "  <td colspan='2' style='width: 35%;'> </td>";
	    $html .= "</tr>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px;'>";
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_nfs' > </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' style='font-weight:bold;'><span> Leucocytes <input id='champ1' type='number' step='any' min='1000'  max='20000'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='font-weight:bold; padding-top: 5px;'> /mm<sup>3</sup> </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='font-weight:bold; padding-top: 5px; width: 80%;'> (4 000 - 10 000) </label></td>";
	    $html .= "</tr>";
	     
	    $html .="<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .=" <td colspan='2' style='width: 55%; background: re;'>";
	    $html .="   <label class='formule_leucocytaire' >";
	    $html .="     <table style='width: 100%; height: 3px;' >";
	    $html .="       <tr>";
	    $html .="         <td style='width: 20%; height: 3px; text-align:right; padding-top: 3px; font-size: 15px;'> P. Neutrophiles </td>";
	    $html .="         <td style='width: 35%;'> <input id='champ2' type='number' readonly='true'  step='any'> /mm<sup>3</sup> </td>";
	    $html .="         <td style='width: 35%;'>  <input id='champ7' type='number' step='any'  min='0' max='100'> % </td>";
	    $html .="       </tr>";
	    $html .="     </table>";
	    $html .="   </label>";
	    $html .=" </td>";
	    $html .=" <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'><div style='width:65%; float:left; '>(2000 - 7000)</div> <div style='width: 35%; float:left;'>(45 - 70)</div> </label></td>";
	    $html .="</tr>";
	    
	    $html .="<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .=" <td colspan='2' style='width: 55%;'>";
	    $html .="   <label class='formule_leucocytaire' >";
	    $html .="     <table style='width: 100%; height: 3px;' >";
	    $html .="       <tr>";
	    $html .="         <td style='width: 20%; height: 3px; text-align:right; padding-top: 3px;  font-size: 15px;'> P. Eosinophiles </td>";
	    $html .="         <td style='width: 35%; '> <input id='champ3' type='number' readonly='true'  step='any'> /mm<sup>3</sup> </td>";
	    $html .="         <td style='width: 35%;'>  <input id='champ8' type='number' step='any'  min='0' max='100'> % </td>";
	    $html .="       </tr>";
	    $html .="     </table>";
	    $html .="   </label>";
	    $html .=" </td>";
	    $html .=" <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> <div style='width:65%; float:left; '>(20 - 500)</div> <div style='width: 35%; float:left;'>(0 - 5)</div> </label></td>";
	    $html .="</tr>";
	    
	    $html .="<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .=" <td colspan='2' style='width: 55%;'>";
	    $html .="   <label class='formule_leucocytaire' >";
	    $html .="     <table style='width: 100%; height: 3px;' >";
	    $html .="       <tr>";
	    $html .="         <td style='width: 20%; height: 3px; text-align:right; padding-top: 3px;'> P. Basophiles </td>";
	    $html .="         <td style='width: 35%; '> <input id='champ4' type='number' readonly='true'  step='any'> /mm<sup>3</sup> </td>";
	    $html .="         <td style='width: 35%;'>  <input id='champ9' type='number' step='any'  min='0' max='100'> % </td>";
	    $html .="       </tr>";
	    $html .="     </table>";
	    $html .="   </label>";
	    $html .=" </td>";
	    $html .=" <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> <div style='width:65%; float:left; '>(0 - 100)</div> <div style='width: 35%; float:left;'>(0 - 3)</div> </label></td>";
	    $html .="</tr>";
	    
	    $html .="<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .=" <td colspan='2' style='width: 55%;'>";
	    $html .="   <label class='formule_leucocytaire' >";
	    $html .="     <table style='width: 100%; height: 3px;' >";
	    $html .="       <tr>";
	    $html .="         <td style='width: 20%; height: 3px; text-align:right; padding-top: 3px;'> Lymphocytes </td>";
	    $html .="         <td style='width: 35%; '> <input id='champ5' type='number' readonly='true'  step='any'> /mm<sup>3</sup> </td>";
	    $html .="         <td style='width: 35%;'>  <input id='champ10' type='number' step='any'  min='0' max='100'> % </td>";
	    $html .="       </tr>";
	    $html .="     </table>";
	    $html .="   </label>";
	    $html .=" </td>";
	    $html .=" <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> <div style='width:65%; float:left; '>(800 - 4000)</div> <div style='width: 35%; float:left;'>(20 - 40)</div> </label></td>";
	    $html .="</tr>";
	    
	    $html .="<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .=" <td colspan='2' style='width: 55%;'>";
	    $html .="   <label class='formule_leucocytaire' >";
	    $html .="     <table style='width: 100%; height: 3px;' >";
	    $html .="       <tr>";
	    $html .="         <td style='width: 20%; height: 3px; text-align:right; padding-top: 3px;'> Monocytes </td>";
	    $html .="         <td style='width: 35%; '> <input id='champ6' type='number' readonly='true'  step='any'> /mm<sup>3</sup> </td>";
	    $html .="         <td style='width: 35%;'>  <input id='champ11' type='number' step='any'  min='0' max='100'> % </td>";
	    $html .="       </tr>";
	    $html .="     </table>";
	    $html .="   </label>";
	    $html .=" </td>";
	    $html .=" <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> <div style='width:65%; float:left; '>(120 - 1200)</div> <div style='width: 35%; float:left;'>(3 - 15)</div> </label></td>";
	    $html .="</tr>";
	     
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px;'>";
	    $html .= "  <td style='width: 55%; height: 20px; '> </td>";
	    $html .= "  <td style='width: 15%;'></td>";
	    $html .= "  <td style='width: 30%;'></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' style='font-weight:bold;'><span> H&eacute;maties <input id='champ12' type='number' step='any'  min='0' max='10'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='font-weight:bold; padding-top: 5px;'> 10<sup>6</sup>/mm<sup>3</sup> </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='font-weight:bold; padding-top: 5px; width: 80%;'> (3,5 - 5,0) </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' style='font-weight:bold;' ><span> H&eacute;moglobine <input id='champ13' type='number' step='any'  min='0' max='30'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px; font-weight:bold;' > g/dl </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px;  font-weight:bold; width: 80%;'> (11 - 15) </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span> H&eacute;matocrite <input id='champ14' type='number' step='any'  min='0' max='100'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> % </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> (37 - 50) </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span> V.G.M <input id='champ15' type='number' step='any'  min='0' max='200'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> fl </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> (80 - 100) </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span> T.C.M.H <input id='champ16' type='number' step='any'  min='0' max='100'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> pg </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> (27 - 34) </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span> C.C.M.H <input id='champ17' type='number' step='any'  min='0' max='100'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> g/dl </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> (32 - 36) </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span> IDR - CV <input id='champ18' type='number' step='any'  min='0' max='50'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> % </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> (11 - 16) </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span> IDR - DS <input id='champ19' type='number' step='any'  min='0' max='100'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> fl </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> (35 - 56) </label></td>";
	    $html .= "</tr>";
	    
	    
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px;'>";
	    $html .= "  <td style='width: 55%; height: 20px; '> </td>";
	    $html .= "  <td style='width: 15%;'></td>";
	    $html .= "  <td style='width: 30%;'></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' style='font-weight:bold;'><span> Plaquettes <input id='champ20' type='number' step='any'  min='0' max='1000'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='font-weight:bold; padding-top: 5px;'> 10<sup>3</sup>/mm<sup>3</sup> </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='font-weight:bold; padding-top: 5px; width: 80%;'> (150 - 450) </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span> VMP <input id='champ21' type='number' step='any'  min='0' max='50'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> fl </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> 6, 5 - 12, 0 </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span> IDP <input id='champ22' type='number' step='any'  min='0' max='50'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> fl </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> 9, 0 - 17, 0 </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span> PCT <input id='champ23' type='number' step='any'  min='0' max='2'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> % </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> 0, 108 - 0, 282 </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px;'>";
	    $html .= "  <td style='width: 55%; height: 20px; '> </td>";
	    $html .= "  <td style='width: 15%;'></td>";
	    $html .= "  <td style='width: 30%;'></td>";
	    $html .= "</tr>";

	    //COMMENTAIRE  --- COMMENTAIRE
	    $html .= "</table>";
	    
	    $html .= "<table style='width: 100%; margin-top: 0px;'>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    if($this->layout()->user['role'] == 'biologiste' || $this->layout()->user['role'] == 'technicien'){
	    	$html .= "<td style='width: 96%;'><label style='height: 140px;' ><span style='font-size: 16px; float: left;  margin-left: 30px;'> Commentaire:  </span> <textarea id='commentaire_hemogramme' style='max-height: 100px; min-height: 100px; max-width: 560px; min-width: 560px; margin-left: 30px; text-align: justify; padding: 5px;' > </textarea> </label></td>";
	    }else{
	    	$html .= "<td style='width: 96%;'><label style='height: 140px;' ><span style='font-size: 16px; float: left;  margin-left: 30px;'> Commentaire:  </span> <textarea id='commentaire_hemogramme' style='max-height: 100px; min-height: 100px; max-width: 560px; min-width: 560px; margin-left: 30px; text-align: justify; padding: 5px;' disabled> </textarea> </label></td>";
	    }
	    $html .= "<td style='width: 5%;'></td>";
	    $html .= "</tr>";
	    $html .= "</table>";
	    
	    
	    $html .= "</td> </tr>";
	    
	    
	    
	    return $html;
	}
	
	/**
	 * analyse 71
	 */
	public function nfs_tr_71(){
		 
		$html  = "<tr> <td align='center'>";
		$html .= "<table style='width: 100%;'>";
		$html .= "<input id='nfs_1' type='hidden' value='1'>";
		 
		//POUR LE NOM DU TYPE DE MATERIEL UTILISE
		$html .= "<tr class='ligneAnanlyse labelTypeMateriel' style='width: 100%; font-family: times new roman; font-size: 15px; margin-top: -45px;'>";
		$html .= "  <td style='width: 55%;'> <label> Mat&eacute;riel utilis&eacute;</label> </td>";
		$html .= "  <td colspan='2' style='width: 35%;'> </td>";
		$html .= "</tr>";
		$html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px;'>";
		$html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_nfs' > </div> </td>";
		$html .= "  <td colspan='2' style='width: 45%;'> </td>";
		$html .= "</tr>";
		//POUR LE NOM DU TYPE DE MATERIEL UTILISE
		 
		$html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
		$html .= "  <td style='width: 55%;'><label class='lab1' style='font-weight:bold;'><span> Leucocytes <input id='champ1' type='number' step='any' min='1000'  max='20000'> </span></label></td>";
		$html .= "  <td style='width: 15%;'><label class='lab2' style='font-weight:bold; padding-top: 5px;'> /mm<sup>3</sup> </label></td>";
		$html .= "  <td style='width: 30%;'><label class='lab3' style='font-weight:bold; padding-top: 5px; width: 80%;'> (4 000 - 10 000) </label></td>";
		$html .= "</tr>";
	
		$html .="<tr class='ligneAnanlyse' style='width: 100%;'>";
		$html .=" <td colspan='2' style='width: 55%; background: re;'>";
		$html .="   <label class='formule_leucocytaire' >";
		$html .="     <table style='width: 100%; height: 3px;' >";
		$html .="       <tr>";
		$html .="         <td style='width: 20%; height: 3px; text-align:right; padding-top: 3px; font-size: 15px;'> P. Neutrophiles </td>";
		$html .="         <td style='width: 35%;'> <input id='champ2' type='number' readonly='true'  step='any'> /mm<sup>3</sup> </td>";
		$html .="         <td style='width: 35%;'>  <input id='champ7' type='number' step='any'  min='0' max='100'> % </td>";
		$html .="       </tr>";
		$html .="     </table>";
		$html .="   </label>";
		$html .=" </td>";
		$html .=" <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'><div style='width:65%; float:left; '>(2000 - 7000)</div> <div style='width: 35%; float:left;'>(45 - 70)</div> </label></td>";
		$html .="</tr>";
		 
		$html .="<tr class='ligneAnanlyse' style='width: 100%;'>";
		$html .=" <td colspan='2' style='width: 55%;'>";
		$html .="   <label class='formule_leucocytaire' >";
		$html .="     <table style='width: 100%; height: 3px;' >";
		$html .="       <tr>";
		$html .="         <td style='width: 20%; height: 3px; text-align:right; padding-top: 3px;  font-size: 15px;'> P. Eosinophiles </td>";
		$html .="         <td style='width: 35%; '> <input id='champ3' type='number' readonly='true'  step='any'> /mm<sup>3</sup> </td>";
		$html .="         <td style='width: 35%;'>  <input id='champ8' type='number' step='any'  min='0' max='100'> % </td>";
		$html .="       </tr>";
		$html .="     </table>";
		$html .="   </label>";
		$html .=" </td>";
		$html .=" <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> <div style='width:65%; float:left; '>(20 - 500)</div> <div style='width: 35%; float:left;'>(0 - 5)</div> </label></td>";
		$html .="</tr>";
		 
		$html .="<tr class='ligneAnanlyse' style='width: 100%;'>";
		$html .=" <td colspan='2' style='width: 55%;'>";
		$html .="   <label class='formule_leucocytaire' >";
		$html .="     <table style='width: 100%; height: 3px;' >";
		$html .="       <tr>";
		$html .="         <td style='width: 20%; height: 3px; text-align:right; padding-top: 3px;'> P. Basophiles </td>";
		$html .="         <td style='width: 35%; '> <input id='champ4' type='number' readonly='true'  step='any'> /mm<sup>3</sup> </td>";
		$html .="         <td style='width: 35%;'>  <input id='champ9' type='number' step='any'  min='0' max='100'> % </td>";
		$html .="       </tr>";
		$html .="     </table>";
		$html .="   </label>";
		$html .=" </td>";
		$html .=" <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> <div style='width:65%; float:left; '>(0 - 100)</div> <div style='width: 35%; float:left;'>(0 - 3)</div> </label></td>";
		$html .="</tr>";
		 
		$html .="<tr class='ligneAnanlyse' style='width: 100%;'>";
		$html .=" <td colspan='2' style='width: 55%;'>";
		$html .="   <label class='formule_leucocytaire' >";
		$html .="     <table style='width: 100%; height: 3px;' >";
		$html .="       <tr>";
		$html .="         <td style='width: 20%; height: 3px; text-align:right; padding-top: 3px;'> Lymphocytes </td>";
		$html .="         <td style='width: 35%; '> <input id='champ5' type='number' readonly='true'  step='any'> /mm<sup>3</sup> </td>";
		$html .="         <td style='width: 35%;'>  <input id='champ10' type='number' step='any'  min='0' max='100'> % </td>";
		$html .="       </tr>";
		$html .="     </table>";
		$html .="   </label>";
		$html .=" </td>";
		$html .=" <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> <div style='width:65%; float:left; '>(800 - 4000)</div> <div style='width: 35%; float:left;'>(20 - 40)</div> </label></td>";
		$html .="</tr>";
		 
		$html .="<tr class='ligneAnanlyse' style='width: 100%;'>";
		$html .=" <td colspan='2' style='width: 55%;'>";
		$html .="   <label class='formule_leucocytaire' >";
		$html .="     <table style='width: 100%; height: 3px;' >";
		$html .="       <tr>";
		$html .="         <td style='width: 20%; height: 3px; text-align:right; padding-top: 3px;'> Monocytes </td>";
		$html .="         <td style='width: 35%; '> <input id='champ6' type='number' readonly='true'  step='any'> /mm<sup>3</sup> </td>";
		$html .="         <td style='width: 35%;'>  <input id='champ11' type='number' step='any'  min='0' max='100'> % </td>";
		$html .="       </tr>";
		$html .="     </table>";
		$html .="   </label>";
		$html .=" </td>";
		$html .=" <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> <div style='width:65%; float:left; '>(120 - 1200)</div> <div style='width: 35%; float:left;'>(3 - 15)</div> </label></td>";
		$html .="</tr>";
	
		$html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px;'>";
		$html .= "  <td style='width: 55%; height: 20px; '> </td>";
		$html .= "  <td style='width: 15%;'></td>";
		$html .= "  <td style='width: 30%;'></td>";
		$html .= "</tr>";
		 
		$html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
		$html .= "  <td style='width: 55%;'><label class='lab1' style='font-weight:bold;'><span> H&eacute;maties <input id='champ12' type='number' step='any'  min='0' max='10'> </span></label></td>";
		$html .= "  <td style='width: 15%;'><label class='lab2' style='font-weight:bold; padding-top: 5px;'> 10<sup>6</sup>/mm<sup>3</sup> </label></td>";
		$html .= "  <td style='width: 30%;'><label class='lab3' style='font-weight:bold; padding-top: 5px; width: 80%;'> (3,5 - 5,0) </label></td>";
		$html .= "</tr>";
		 
		$html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
		$html .= "  <td style='width: 55%;'><label class='lab1' style='font-weight:bold;' ><span> H&eacute;moglobine <input id='champ13' type='number' step='any'  min='0' max='30'> </span></label></td>";
		$html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px; font-weight:bold;' > g/dl </label></td>";
		$html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px;  font-weight:bold; width: 80%;'> (11 - 15) </label></td>";
		$html .= "</tr>";
		 
		$html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
		$html .= "  <td style='width: 55%;'><label class='lab1' ><span> H&eacute;matocrite <input id='champ14' type='number' step='any'  min='0' max='100'> </span></label></td>";
		$html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> % </label></td>";
		$html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> (37 - 50) </label></td>";
		$html .= "</tr>";
		 
		$html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
		$html .= "  <td style='width: 55%;'><label class='lab1' ><span> V.G.M <input id='champ15' type='number' step='any'  min='0' max='200'> </span></label></td>";
		$html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> fl </label></td>";
		$html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> (80 - 100) </label></td>";
		$html .= "</tr>";
		 
		$html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
		$html .= "  <td style='width: 55%;'><label class='lab1' ><span> T.C.M.H <input id='champ16' type='number' step='any'  min='0' max='100'> </span></label></td>";
		$html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> pg </label></td>";
		$html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> (27 - 34) </label></td>";
		$html .= "</tr>";
		 
		$html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
		$html .= "  <td style='width: 55%;'><label class='lab1' ><span> C.C.M.H <input id='champ17' type='number' step='any'  min='0' max='100'> </span></label></td>";
		$html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> g/dl </label></td>";
		$html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> (32 - 36) </label></td>";
		$html .= "</tr>";
		 
		$html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
		$html .= "  <td style='width: 55%;'><label class='lab1' ><span> IDR - CV <input id='champ18' type='number' step='any'  min='0' max='50'> </span></label></td>";
		$html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> % </label></td>";
		$html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> (11 - 16) </label></td>";
		$html .= "</tr>";
		 
		$html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
		$html .= "  <td style='width: 55%;'><label class='lab1' ><span> IDR - DS <input id='champ19' type='number' step='any'  min='0' max='100'> </span></label></td>";
		$html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> fl </label></td>";
		$html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> (35 - 56) </label></td>";
		$html .= "</tr>";
		 
		 
		 
		$html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px;'>";
		$html .= "  <td style='width: 55%; height: 20px; '> </td>";
		$html .= "  <td style='width: 15%;'></td>";
		$html .= "  <td style='width: 30%;'></td>";
		$html .= "</tr>";
		 
		$html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
		$html .= "  <td style='width: 55%;'><label class='lab1' style='font-weight:bold;'><span> Plaquettes <input id='champ20' type='number' step='any'  min='0' max='1000'> </span></label></td>";
		$html .= "  <td style='width: 15%;'><label class='lab2' style='font-weight:bold; padding-top: 5px;'> 10<sup>3</sup>/mm<sup>3</sup> </label></td>";
		$html .= "  <td style='width: 30%;'><label class='lab3' style='font-weight:bold; padding-top: 5px; width: 80%;'> (150 - 450) </label></td>";
		$html .= "</tr>";
		 
		$html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
		$html .= "  <td style='width: 55%;'><label class='lab1' ><span> VMP <input id='champ21' type='number' step='any'  min='0' max='50'> </span></label></td>";
		$html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> fl </label></td>";
		$html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> 6, 5 - 12, 0 </label></td>";
		$html .= "</tr>";
		 
		$html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
		$html .= "  <td style='width: 55%;'><label class='lab1' ><span> IDP <input id='champ22' type='number' step='any'  min='0' max='50'> </span></label></td>";
		$html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> g/dl </label></td>";
		$html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> 9, 0 - 17, 0 </label></td>";
		$html .= "</tr>";
		 
		$html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
		$html .= "  <td style='width: 55%;'><label class='lab1' ><span> PCT <input id='champ23' type='number' step='any'  min='0' max='2'> </span></label></td>";
		$html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> % </label></td>";
		$html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> 0, 108 - 0, 282 </label></td>";
		$html .= "</tr>";
		 
		 
		 
		$html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px;'>";
		$html .= "  <td style='width: 55%; height: 20px; '> </td>";
		$html .= "  <td style='width: 15%;'></td>";
		$html .= "  <td style='width: 30%;'></td>";
		$html .= "</tr>";
	
		$html .="<tr class='ligneAnanlyse' style='width: 100%;'>";
		$html .=" <td colspan='2' style='width: 55%;'>";
		$html .="   <label class='formule_leucocytaire' >";
		$html .="     <table style='width: 100%; height: 3px; font-weight: bold;' >";
		$html .="       <tr>";
		$html .="         <td style='width: 20%; height: 3px; text-align:right; padding-top: 3px;'> R&eacute;ticulocytes </td>";
		$html .="         <td style='width: 35%; '> <input id='champ24' type='number' readonly='true'  step='any'> /mm<sup>3</sup> </td>";
		$html .="         <td style='width: 35%; '> <input id='champ25' type='number' step='any'  min='0' max='100'> % </td>";
		$html .="       </tr>";
		$html .="     </table>";
		$html .="   </label>";
		$html .=" </td>";
		$html .=" <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> <div style='width:65%; float:left; font-weight: bold; font-size: 13px;'>(25000 - 80000)</div> <div style='width: 35%; float:left;  font-size: 13px; font-weight: bold;'>(0,5 - 1,5)</div> </label></td>";
		$html .="</tr>";
		 
		 
		 
		$html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px;'>";
		$html .= "  <td style='width: 55%; height: 20px; '> </td>";
		$html .= "  <td style='width: 15%;'></td>";
		$html .= "  <td style='width: 30%;'></td>";
		$html .= "</tr>";
		
		$html .= "</table>";
		 
		 
		$html .= "<table style='width: 100%; margin-top: 0px;'>";
		$html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
		if($this->layout()->user['role'] == 'biologiste' || $this->layout()->user['role'] == 'technicien'){
			$html .= "<td style='width: 96%;'><label style='height: 140px;' ><span style='font-size: 16px; float: left;  margin-left: 30px;'> Commentaire:  </span> <textarea id='commentaire_hemogramme' style='max-height: 100px; min-height: 100px; max-width: 560px; min-width: 560px; margin-left: 30px; text-align: justify; padding: 5px;' > </textarea> </label></td>";
		}else{
			$html .= "<td style='width: 96%;'><label style='height: 140px;' ><span style='font-size: 16px; float: left;  margin-left: 30px;'> Commentaire:  </span> <textarea id='commentaire_hemogramme' style='max-height: 100px; min-height: 100px; max-width: 560px; min-width: 560px; margin-left: 30px; text-align: justify; padding: 5px;' disabled> </textarea> </label></td>";
		}
		$html .= "<td style='width: 5%;'></td>";
		$html .= "</tr>";
		$html .= "</table>";
		 
		 
		$html .= "</td> </tr>";
		
		
		return $html;
	}
	
	/**
	 * analyse 2
	 */
	public function gsrh_groupage_2(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	    
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    $html .= "<tr class='ligneAnanlyse labelTypeMateriel' style='width: 100%; font-family: times new roman; font-size: 15px; margin-top: -45px;'>";
	    $html .= "  <td style='width: 55%;'> <label> Mat&eacute;riel utilis&eacute;</label> </td>";
	    $html .= "  <td colspan='2' style='width: 35%;'> </td>";
	    $html .= "</tr>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px;'>";
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_gsrh_groupage' > </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> Groupe <select name='groupe' id='groupe' > <option >  </option> <option value='A' >A</option> <option value='B' >B</option> <option value='AB' >AB</option> <option value='O' >O</option> </select></span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'>  </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'>  </label></td>";
	    $html .= "</tr>";
	     
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> Rh&eacute;sus <select name='rhesus' id='rhesus' style='width: 125px;'> <option >  </option> <option value='Rh+' >Rh&eacute;sus positif</option> <option value='Rh-' >Rh&eacute;sus n&eacute;gatif</option> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'>  </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'>  </label></td>";
	    $html .= "</tr>";
	     
	    $html .= "</table> </td> </tr>";
	     
	    return $html;
	}
	
	
	/**
	 * analyse 3
	 */
	public function recherche_antigene_3(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	     
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    $html .= "<tr class='ligneAnanlyse labelTypeMateriel' style='width: 100%; font-family: times new roman; font-size: 15px; margin-top: -45px;'>";
	    $html .= "  <td style='width: 55%;'> <label> Mat&eacute;riel utilis&eacute;</label> </td>";
	    $html .= "  <td colspan='2' style='width: 35%;'> </td>";
	    $html .= "</tr>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px;'>";
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_recherche_antigene' > </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> Pr&eacute;sence d'antig&egrave;ne <select name='antigene_d_faible' id='antigene_d_faible' > <option >  </option> <option value='Positif' >Positif</option> <option value='Negatif' >N&eacute;gatif</option> </select></span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'>  </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'>  </label></td>";
	    $html .= "</tr>";
	    $html .= "</table>";
	     
	    $html .= "<table style='width: 100%;'>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 70%;'><label class='lab1'><span style='font-weight: bold; width: 100%; text-align: right;'>&raquo; Conclusion :  <input id='conclusion_antigene_d_faible' type='text' step='any' style='width: 70%; float: right; text-align: left;' maxlength='45'> </span></label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> </label></td>";
	    $html .= "</tr>";
	    $html .= "</table> ";
	    
	    $html .= "</td> </tr>";
	    
	    return $html;
	}
	
	
	/**
	 * analyse 4
	 */
	public function test_combs_direct_4(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    $html .= "<tr class='ligneAnanlyse labelTypeMateriel' style='width: 100%; font-family: times new roman; font-size: 15px; margin-top: -45px;'>";
	    $html .= "  <td style='width: 55%;'> <label> Mat&eacute;riel utilis&eacute;</label> </td>";
	    $html .= "  <td colspan='2' style='width: 35%;'> </td>";
	    $html .= "</tr>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px;'>";
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_test_combs_direct' > </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> Test <select name='test_combs_direct' id='test_combs_direct' > <option>  </option> <option value='Positif' >Positif</option> <option value='Negatif' >N&eacute;gatif</option> </select></span></label></td>";
	    $html .= "  <td style='width: 10%;'><label class='lab2' style='padding-top: 5px; text-align: right;' > <span class='titre_combs_direct' style='display: none;'> Titre </span> </label></td>";
	    $html .= "  <td style='width: 45%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> <input class='titre_combs_direct' id='titre_combs_direct' type='text' style='display: none;' > </label></td>";
	    $html .= "</tr>";
	     
	    $html .= "</table> </td> </tr>";
	     
	    return $html;
	}
	
	
	/**
	 * analyse 5
	 */
	public function test_combs_indirect_5(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    $html .= "<tr class='ligneAnanlyse labelTypeMateriel' style='width: 100%; font-family: times new roman; font-size: 15px; margin-top: -45px;'>";
	    $html .= "  <td style='width: 55%;'> <label> Mat&eacute;riel utilis&eacute;</label> </td>";
	    $html .= "  <td colspan='2' style='width: 35%;'> </td>";
	    $html .= "</tr>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px;'>";
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_test_combs_indirect' > </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    $html .= "</table> </td> </tr>";
	     
	     
	    $html .= "<tr> <td align='center'>";
	    $html .= "<table id='test_combs_rai' style='width: 100%;'>";
	    
	    $html .= "<tr id='test_combs_rai_1' class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 30%;'><label class='lab1' ><span style='font-weight: bold;'> RAI <select name='test_combs_indirect_1' id='test_combs_indirect_1' > <option >  </option> <option value='Positif' >Positif</option> <option value='Negatif' >N&eacute;gatif</option> </select></span></label></td>";
	    $html .= "  <td style='width: 25%;'><label class='lab2' style='padding-top: 5px; text-align: right; '>  Titre <input class='titre_combs_indirect' id='titre_combs_indirect_1' type='text'> </label></td>";
	    $html .= "  <td style='width: 45%;'><label class='lab3' style='padding-top: 5px; width: 80%; padding-left: 25px;'> Temp&eacute;rature <input id='titre_combs_temperature_1' type='number' > </label></td>";
	    $html .= "</tr>";
	
	    $html .= "<tr class='ligneAnanlyse' id='test_combs_indirect_mp' style='width: 100%;'>";
	    $html .= "  <td style='width: 30%;'> <div style='float: left; width: 25%; text-align: center; font-weight: bold; font-size: 25px;'> <div style='float: left; width: 50%; cursor: pointer; color:green;' id='test_combs_indirect_moins'> - </div> <div style=' float: left; width: 45%; cursor: pointer; color:green;'  id='test_combs_indirect_plus'> + </div> </div> </label></td>";
	    $html .= "  <td style='width: 25%;'></td>";
	    $html .= "  <td style='width: 45%;'></td>";
	    $html .= "</tr>";
	    
	    $html .= "</table>";
	    
	    
	    $html .= "<table style='width: 100%;'>";
	     
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td colspan='2' ><label style='height: 80px;' ><span style='font-size: 16px; float: left;  margin-left: 30px;'> Commentaire:  </span> <textarea id='commentaire_test_combs_indirect' readonly style='max-height: 57px; min-height: 57px; max-width: 400px; min-width: 400px; margin-left: 30px;' > </textarea> </label></td>";
	    $html .= "  <td style='width: 30%;'><label style='padding-top: 5px; width: 80%; height: 80px; font-size: 14px;'>  </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "</table> </td> </tr>";
	    
	    
	
	    return $html;
	}
	
	
	/**
	 * analyse 6
	 */
	public function test_compatibilite_6(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    $html .= "<tr class='ligneAnanlyse labelTypeMateriel' style='width: 100%; font-family: times new roman; font-size: 15px; margin-top: -45px;'>";
	    $html .= "  <td style='width: 55%;'> <label> Mat&eacute;riel utilis&eacute;</label> </td>";
	    $html .= "  <td colspan='2' style='width: 35%;'> </td>";
	    $html .= "</tr>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px;'>";
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_test_compatibilite' > </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> Test <select name='test_compatibilite' id='test_compatibilite' onchange='getTestCompatibilite(this.value)' style='width: 127px;'> <option >  </option> <option value='Compatible' >Compatible</option> <option value='Non compatible' >Non compatible</option> </select></span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> <span class='titre_test_compatibilite' style='display: none;'> Poche </span> </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'>  <input class='titre_test_compatibilite' id='titre_test_compatibilite' type='text' style='display: none;' > </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	
	/**
	 * analyse 7
	 */
	public function vitesse_sedimentation_7(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    $html .= "<tr class='ligneAnanlyse labelTypeMateriel' style='width: 100%; font-family: times new roman; font-size: 15px; margin-top: -45px;'>";
	    $html .= "  <td style='width: 55%;'> <label> Mat&eacute;riel utilis&eacute;</label> </td>";
	    $html .= "  <td colspan='2' style='width: 35%;'> </td>";
	    $html .= "</tr>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px;'>";
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_vitesse_sedimentation' > </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    $html .= "</table>";
	    
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    $html .= "<table style='width: 100%;'>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 35%;'><label class='lab1' ><span style='font-weight: bold;'>  1<sup>&egrave;re</sup> heure <input type='number' id='vitesse_sedimentation' name='vitesse_sedimentation' style='width: 80px;'> mm </span></label></td>";
	    $html .= "  <td style='width: 35%;'><label class='lab2' ><span style='font-weight: bold;'>  2<sup>&egrave;me</sup> heure <input type='number' id='vitesse_sedimentation_2' name='vitesse_sedimentation_2' style='width: 80px;'> mm </span></label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 85%;'> H<15 | H>20 ; +60ans <30 </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	    return $html;
	}
	
	
	/**
	 * analyse 8
	 */
	public function test_demmel_8(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    $html .= "<tr class='ligneAnanlyse labelTypeMateriel' style='width: 100%; font-family: times new roman; font-size: 15px; margin-top: -45px;'>";
	    $html .= "  <td style='width: 55%;'> <label> Mat&eacute;riel utilis&eacute;</label> </td>";
	    $html .= "  <td colspan='2' style='width: 35%;'> </td>";
	    $html .= "</tr>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px;'>";
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_test_demmel' > </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> Test <select name='test_demmel' id='test_demmel' > <option >  </option> <option value='Positif' >Positif</option> <option value='Negatif' >N&eacute;gatif</option> </select> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'>  </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'>  </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	
	/**
	 * analyse 9
	 */
	public function taux_reticulocytes_9(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    $html .= "<tr class='ligneAnanlyse labelTypeMateriel' style='width: 100%; font-family: times new roman; font-size: 15px; margin-top: -45px;'>";
	    $html .= "  <td style='width: 55%;'> <label> Mat&eacute;riel utilis&eacute;</label> </td>";
	    $html .= "  <td colspan='2' style='width: 35%;'> </td>";
	    $html .= "</tr>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px;'>";
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_taux_reticulocytes' > </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> TR <input name='taux_reticulocyte' id='taux_reticulocyte' type='number' > </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> mm&sup3; </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> 25 000 &agrave; 80 000 </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	/**
	 * analyse 10
	 */
	public function goutte_epaisse_10(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    $html .= "<tr class='ligneAnanlyse labelTypeMateriel' style='width: 100%; font-family: times new roman; font-size: 15px; margin-top: -45px;'>";
	    $html .= "  <td style='width: 55%;'> <label> Mat&eacute;riel utilis&eacute;</label> </td>";
	    $html .= "  <td colspan='2' style='width: 35%;'> </td>";
	    $html .= "</tr>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px;'>";
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_goutte_epaisse' > </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> Goutte &eacute;paisse <select name='goutte_epaisse' id='goutte_epaisse' > <option >  </option> <option value='Positif' >Positif</option> <option value='Negatif' >N&eacute;gatif</option> </select> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'>  </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'>  </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' id='goutte_epaisse_positif' style='width: 100%; display: none;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> Densit&eacute; parasitaire <input name='densite_parasitaire' id='densite_parasitaire' type='number' > </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> p/ul </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'>  </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "</table>";
	    
	    $html .= "<table style='width: 100%;'>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td colspan='2' ><label style='height: 80px;' ><span style='font-size: 16px; float: left;  margin-left: 30px;'> Commentaire:  </span> <textarea id='commentaire_goutte_epaisse' style='max-height: 57px; min-height: 57px; max-width: 400px; min-width: 400px; margin-left: 30px;' > </textarea> </label></td>";
	    $html .= "  <td style='width: 30%;'><label style='padding-top: 5px; width: 80%; height: 80px; font-size: 14px;'>  </label></td>";
	    $html .= "</tr>";
	    	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	/**
	 * analyse 11
	 */
	public function adenogramme_11(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    $html .= "<tr class='ligneAnanlyse labelTypeMateriel' style='width: 100%; font-family: times new roman; font-size: 15px; margin-top: -45px;'>";
	    $html .= "  <td style='width: 55%;'> <label> Mat&eacute;riel utilis&eacute;</label> </td>";
	    $html .= "  <td colspan='2' style='width: 35%;'> </td>";
	    $html .= "</tr>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px;'>";
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_adenogramme' > </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> adenogramme (En attente ... ) </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'>  </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	/**
	 * analyse 12
	 */
	public function medulodramme_12(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    $html .= "<tr class='ligneAnanlyse labelTypeMateriel' style='width: 100%; font-family: times new roman; font-size: 15px; margin-top: -45px;'>";
	    $html .= "  <td style='width: 55%;'> <label> Mat&eacute;riel utilis&eacute;</label> </td>";
	    $html .= "  <td colspan='2' style='width: 35%;'> </td>";
	    $html .= "</tr>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px;'>";
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_medulodramme' > </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> medulodramme (En attente ... ) </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'>  </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	/**
	 * analyse 13
	 */
	public function cytochimie_myeloperoxydase_13(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    $html .= "<tr class='ligneAnanlyse labelTypeMateriel' style='width: 100%; font-family: times new roman; font-size: 15px; margin-top: -45px;'>";
	    $html .= "  <td style='width: 55%;'> <label> Mat&eacute;riel utilis&eacute;</label> </td>";
	    $html .= "  <td colspan='2' style='width: 35%;'> </td>";
	    $html .= "</tr>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px;'>";
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_cytochimie_myeloperoxydase' > </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> cytochimie myeloperoxydase (En attente ... ) </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'>  </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	/**
	 * analyse 14
	 */
	public function tp_inr_14(){
	     
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";

	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    $html .= "<tr class='ligneAnanlyse labelTypeMateriel' style='width: 100%; font-family: times new roman; font-size: 15px; margin-top: -45px;'>";
	    $html .= "  <td style='width: 55%;'> <label> Mat&eacute;riel utilis&eacute;</label> </td>";
	    $html .= "  <td colspan='2' style='width: 35%;'> </td>";
	    $html .= "</tr>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px;'>";
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_tp_inr' > </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    //TQ - TQ - TQ ***************************************
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px;'>";
	    $html .= "  <td style='width: 55%;'>TQ (Temps de Quick) </span></label></td>";
	    $html .= "  <td style='width: 15%;'></td>";
	    $html .= "  <td style='width: 30%;'></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> T&eacute;moin <input type='number' id='temps_quick_temoin' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> S </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> N: 11 - 13  </label></td>";
	    $html .= "</tr>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> Patient <input type='number' id='temps_quick_patient' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> S </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'>  </label></td>";
	    $html .= "</tr>";
	    
	    //TP - TP - TP ***************************************
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px;'>";
	    $html .= "  <td style='width: 55%;'>TP (Taux de Prothrombine) </span></label></td>";
	    $html .= "  <td style='width: 15%;'></td>";
	    $html .= "  <td style='width: 30%;'></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> Patient <input type='number' id='taux_prothrombine_patient' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> % </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> N: 70 - 100 </label></td>";
	    $html .= "</tr>";
	    
	    //INR - INR - INR ***************************************
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px;'>";
	    $html .= "  <td style='width: 55%;'>INR (International Normalised Ratio) </span></label></td>";
	    $html .= "  <td style='width: 15%;'></td>";
	    $html .= "  <td style='width: 30%;'></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> Patient  <input type='number' id='inr_patient' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'>  </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> N: < 1,2 </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' style='background: #fdfdfd;' ><span style='font-weight: bold;'> NB: Patient sous sintron N = {2;3} </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='background: #fdfdfd; padding-top: 5px;'>  </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='background: #fdfdfd; padding-top: 5px; width: 80%;'>  </label></td>";
	    $html .= "</tr>";
	    
	 
	    $html .= "</table> </td> </tr>";
	    
	    return $html;
	}
	
	
	/**
	 * analyse 15
	 */
	public function tca_15(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    $html .= "<tr class='ligneAnanlyse labelTypeMateriel' style='width: 100%; font-family: times new roman; font-size: 15px; margin-top: -45px;'>";
	    $html .= "  <td style='width: 55%;'> <label> Mat&eacute;riel utilis&eacute;</label> </td>";
	    $html .= "  <td colspan='2' style='width: 35%;'> </td>";
	    $html .= "</tr>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px;'>";
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_tca' > </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> Patient <input type='number' id='tca_patient' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> S </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> N: 25 &agrave; 41 </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> T&eacute;moin <input type='number' id='temoin_patient' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> S </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'>  </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> Ratio <input type='number' id='tca_ratio' step='any' readonly='true'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'>  </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> < 1,2 </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	
	/**
	 * analyse 16
	 */
	public function fibrinemie_16(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    $html .= "<tr class='ligneAnanlyse labelTypeMateriel' style='width: 100%; font-family: times new roman; font-size: 15px; margin-top: -45px;'>";
	    $html .= "  <td style='width: 55%;'> <label> Mat&eacute;riel utilis&eacute;</label> </td>";
	    $html .= "  <td colspan='2' style='width: 35%;'> </td>";
	    $html .= "</tr>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px;'>";
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_fibrinemie' > </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> Fibrin&eacute;mie <input id='fibrinemie' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> g/l </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> N: 2 - 4</label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	
	/**
	 * analyse 17
	 */
	public function temps_saignement_17(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    $html .= "<tr class='ligneAnanlyse labelTypeMateriel' style='width: 100%; font-family: times new roman; font-size: 15px; margin-top: -45px;'>";
	    $html .= "  <td style='width: 55%;'> <label> Mat&eacute;riel utilis&eacute;</label> </td>";
	    $html .= "  <td colspan='2' style='width: 35%;'> </td>";
	    $html .= "</tr>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px;'>";
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_temps_saignement' > </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> Temps de saignement <input id='temps_saignement' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> min </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> N: 2 - 8 min</label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	
	/**
	 * analyse 18
	 */
	public function facteur_viii_18(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE 
	    $html .= "<tr class='ligneAnanlyse labelTypeMateriel' style='width: 100%; font-family: times new roman; font-size: 15px; margin-top: -45px;'>";
	    $html .= "  <td style='width: 55%;'> <label> Mat&eacute;riel utilis&eacute;</label> </td>";
	    $html .= "  <td colspan='2' style='width: 35%;'> </td>";
	    $html .= "</tr>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px; '>";
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_facteur_8' style='padding-left: 8px;'> </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> facteur VIII <input id='facteur_8' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'>  </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	/**
	 * analyse 19
	 */
	public function facteur_ix_19(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    $html .= "<tr class='ligneAnanlyse labelTypeMateriel' style='width: 100%; font-family: times new roman; font-size: 15px; margin-top: -45px;'>";
	    $html .= "  <td style='width: 55%;'> <label> Mat&eacute;riel utilis&eacute;</label> </td>";
	    $html .= "  <td colspan='2' style='width: 35%;'> </td>";
	    $html .= "</tr>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px; '>";
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_facteur_9' style='padding-left: 8px;'> </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    	  
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> facteur IX <input id='facteur_9' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'>  </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	
	/**
	 * analyse 20
	 */
	public function dimeres_20(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    $html .= "<tr class='ligneAnanlyse labelTypeMateriel' style='width: 100%; font-family: times new roman; font-size: 15px; margin-top: -45px;'>";
	    $html .= "  <td style='width: 55%;'> <label> Mat&eacute;riel utilis&eacute;</label> </td>";
	    $html .= "  <td colspan='2' style='width: 35%;'> </td>";
	    $html .= "</tr>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px; '>";
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_dimeres' style='padding-left: 8px;' > </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> d dimeres <input id='d_dimeres' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'>  </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}

	
	/**
	 * analyse 21
	 */
	public function glycemie_21(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    $html .= "<tr class='ligneAnanlyse labelTypeMateriel' style='width: 100%; font-family: times new roman; font-size: 15px; margin-top: -45px;'>";
	    $html .= "  <td style='width: 55%;'> <label> Mat&eacute;riel utilis&eacute;</label> </td>";
	    $html .= "  <td colspan='2' style='width: 35%;'> </td>";
	    $html .= "</tr>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px; '>";
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_glycemie' style='padding-left: 8px;' > </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'>  <input id='glycemie_1' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> g/l </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> N: 0,7 &agrave; 1,10 </label></td>";
	    $html .= "</tr>";
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'>  <input id='glycemie_2' type='number' step='any' readonly='true' > </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> mmol/l </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> N: 4,1 &agrave; 5,9 </label></td>";
	    $html .= "</tr>";
	    
	    
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	/**
	 * analyse 22
	 */
	public function creatininemie_22(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    $html .= "<tr class='ligneAnanlyse labelTypeMateriel' style='width: 100%; font-family: times new roman; font-size: 15px; margin-top: -45px;'>";
	    $html .= "  <td style='width: 55%;'> <label> Mat&eacute;riel utilis&eacute;</label> </td>";
	    $html .= "  <td colspan='2' style='width: 35%;'> </td>";
	    $html .= "</tr>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px; '>";
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_creatininemie' style='padding-left: 8px;' > </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> cr&eacute;atinin&eacute;mie <input id='creatininemie' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> mg/l </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> N: H= 7 &agrave; 13 | F= 6 &agrave; 11</label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'>  <input id='creatininemie_umol' type='number' step='any' readonly='true' > </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> umol/l </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	/**
	 * analyse 23
	 */
	public function azotemie_23(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    $html .= "<tr class='ligneAnanlyse labelTypeMateriel' style='width: 100%; font-family: times new roman; font-size: 15px; margin-top: -45px;'>";
	    $html .= "  <td style='width: 55%;'> <label> Mat&eacute;riel utilis&eacute;</label> </td>";
	    $html .= "  <td colspan='2' style='width: 35%;'> </td>";
	    $html .= "</tr>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px; '>";
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_azotemie' style='padding-left: 8px;' > </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> ur&eacute;e sanguine <input id='uree_sanguine' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> g/l </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> N: 0,15 &agrave; 0,45</label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'>  <input id='uree_sanguine_mmol' type='number' step='any' readonly> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> mmol/l </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> ( 2,49 &agrave; 7,49 )</label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	/**
	 * analyse 24
	 */
	public function acide_urique_24(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    $html .= "<tr class='ligneAnanlyse labelTypeMateriel' style='width: 100%; font-family: times new roman; font-size: 15px; margin-top: -45px;'>";
	    $html .= "  <td style='width: 55%;'> <label> Mat&eacute;riel utilis&eacute;</label> </td>";
	    $html .= "  <td colspan='2' style='width: 35%;'> </td>";
	    $html .= "</tr>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px; '>";
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_acide_urique' style='padding-left: 8px;' > </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> uric&eacute;mie <input id='acide_urique' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 12%;'><label class='lab2' style='padding-top: 5px;'> mg/l </label></td>";
	    $html .= "  <td style='width: 33%;'><label class='lab3' style='padding-top: 5px; width: 85%;'> ( H= 35  &agrave; 72, F= 26 &agrave; 60 ) </label></td>";
	    $html .= "</tr>";
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> <input id='acide_urique_umol' type='number' step='any' readonly> </span></label></td>";
	    $html .= "  <td style='width: 12%;'><label class='lab2' style='padding-top: 5px;'> umol/l </label></td>";
	    $html .= "  <td style='width: 33%;'><label class='lab3' style='padding-top: 5px; width: 85%;'> ( H= 208  &agrave; 428, F= 154 &agrave; 356 ) </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	/**
	 * analyse 25
	 */
	public function cholesterol_total_25(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    $html .= "<tr class='ligneAnanlyse labelTypeMateriel' style='width: 100%; font-family: times new roman; font-size: 15px; margin-top: -45px;'>";
	    $html .= "  <td style='width: 55%;'> <label> Mat&eacute;riel utilis&eacute;</label> </td>";
	    $html .= "  <td colspan='2' style='width: 35%;'> </td>";
	    $html .= "</tr>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px; '>";
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_cholesterol_total' style='padding-left: 8px;' > </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'>  <input id='cholesterol_total_1' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> g/l </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'>  </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'>  <input id='cholesterol_total_2' type='number' step='any' readonly='true'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> mmol/l </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'>  </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	    
	    $html .= "<tr> <td align='center'>";
	    $html .= "<table style='width: 90%; margin-left: -20px;'>";
	    
	    $html .= "<tr style='width: 100%;'>";
	    $html .= "  <td style='width: 100%;'>
	                  <label class='lab1' style='background: #f9f9f9; height: 85px; padding-left: 10px; font-size: 13px;' >
	                      <span style='text-decoration: underline;'> Commentaire </span> <br>
	    
                          Moins de 30 ans <1, 80 (4, 7 mmol/l) - Plus de 30 ans < 2, 00 (< 5, 2mmol/l) <br>
                          Interpr&eacute;tation clinique : <span style='font-weight: bold;'> suspect </span> sup&eacute;rieur &agrave;  2, 20 (5, 7 mmol/l)<br>
                          <span style='font-weight: bold;'> Risque &eacute;lev&eacute;</span> sup&eacute;rieur &agrave; 2, 60 (6, 7 mmol/l)
	                  </label>
	                </td>";
	    $html .= "</tr>";
	    
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	/**
	 * analyse 26
	 */
	public function triglycerides_26(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	    
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    $html .= "<tr class='ligneAnanlyse labelTypeMateriel' style='width: 100%; font-family: times new roman; font-size: 15px; margin-top: -45px;'>";
	    $html .= "  <td style='width: 55%;'> <label> Mat&eacute;riel utilis&eacute;</label> </td>";
	    $html .= "  <td colspan='2' style='width: 35%;'> </td>";
	    $html .= "</tr>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px; '>";
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_triglycerides' style='padding-left: 8px;' > </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'>  <input id='triglycerides_1' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> g/l </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%; font-size: 12px;'> H: 0,40 - 1,50 | F: 0,30 - 1,40 </label></td>";
	    $html .= "</tr>";
	     
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'>  <input id='triglycerides_2' type='number' step='any'  readonly='true'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> mmol/l </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%; font-size: 12px;'> H: 0,45 - 1,70 | F: 0,35 - 1,60 </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	     
	    return $html;
	}
	
	/**
	 * analyse 27
	 */
	public function cholesterol_HDL_27(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    $html .= "<tr class='ligneAnanlyse labelTypeMateriel' style='width: 100%; font-family: times new roman; font-size: 15px; margin-top: -45px;'>";
	    $html .= "  <td style='width: 55%;'> <label> Mat&eacute;riel utilis&eacute;</label> </td>";
	    $html .= "  <td colspan='2' style='width: 35%;'> </td>";
	    $html .= "</tr>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px; '>";
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_cholesterol_HDL' style='padding-left: 8px;' > </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'>  <input id='cholesterol_HDL_1' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> g/l </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'>  </label></td>";
	    $html .= "</tr>";
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'>  <input id='cholesterol_HDL_2' type='number' step='any' readonly='true'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> mmol/l </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'>  </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	    
	    $html .= "<tr> <td align='center'>";
	    $html .= "<table style='width: 90%; margin-left: -20px;'>";
	     
	    $html .= "<tr style='width: 100%;'>";
	    $html .= "  <td style='width: 100%;'>
	                  <label class='lab1' style='background: #f9f9f9; height: 65px; padding-left: 10px; font-size: 13px;' >
	                      <span style='text-decoration: underline;'> Commentaire </span> <br>
	  
                          N: < 0, 35 ( < 0, 9 mmol/l) facteur de risque pour coronaropathies <br>
                          N: > 0, 60 ( > 1, 5 mmol/l) risque r&eacute;duit pour coronaropathies <br>
	        
	                  </label>
	                </td>";
	    $html .= "</tr>";
	     
	    $html .= "</table> </td> </tr>";
	
	    
	    $html .= "<tr class='rapport_chol_hdl' style='display: none;'> <td align='center'>";
	    $html .= "<table style='width: 100%; margin-top: -10px;' >";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' style='background: #f9f9f9; padding-top: 5px; width: 100%; margin-left: 8px;' ><span style='font-weight: bold;'> &raquo; Rapport: CHOLT/HDL <input id='rapport_chol_hdl' type='number' step='any' readonly='true'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='background: #f9f9f9; padding-top: 5px;'>  </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='background: #f9f9f9; padding-top: 5px; width: 75%;'> N: < 3,5 </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; '>";
	    $html .= "  <td colspan='3' style='width: 100%;'>";
	    $html .= "  <label class='lab1' style='background: #f9f9f9; margin-top: -10px; width: 91.5%; margin-left: 8px; text-align:right; height: 30px;' >";
	    
	    $html .= "  <div style='width: 39%; background: re; float:left; padding-top: 10px; font-weight: bold; font-size: 13px;' > &raquo; Conclusion: </div>";
	    $html .= "  <div style='width: 56%; background: yello; float:left; text-align: left; padding-left: 12px; padding-top: 10px; font-size: 14px;' id='conclusion_rapport_chol_hdl' align:left;>  </div>";
	    
	    $html .= "  </label>";
	    $html .= "  </td>";
	    $html .= "</tr>";
	    
	    $html .= "</table> </td> </tr>";
	    
	    return $html;
	}
	

	/**
	 * analyse 28
	 */
	public function cholesterol_LDL_28(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    $html .= "<tr class='ligneAnanlyse labelTypeMateriel' style='width: 100%; font-family: times new roman; font-size: 15px; margin-top: -45px;'>";
	    $html .= "  <td style='width: 55%;'> <label> Mat&eacute;riel utilis&eacute;</label> </td>";
	    $html .= "  <td colspan='2' style='width: 35%;'> </td>";
	    $html .= "</tr>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px; '>";
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_cholesterol_LDL' style='padding-left: 8px;' > </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'>  <input id='cholesterol_LDL_1' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> g/l </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'>  </label></td>";
	    $html .= "</tr>";
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'>  <input id='cholesterol_LDL_2' type='number' step='any'  readonly='true'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> mmol/l </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'>  </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	     
	    $html .= "<tr> <td align='center'>";
	    $html .= "<table style='width: 90%; margin-left: -20px;'>";
	
	    $html .= "<tr style='width: 100%;'>";
	    $html .= "  <td style='width: 100%;'>
	                  <label class='lab1' style='background: #f9f9f9; height: 175px; padding-left: 10px; font-size: 13px;' >
	                      <span style='text-decoration: underline;'> Commentaire </span> <br>
	 
                          N: H < 0, 50 (< 1, 3 mmol/l); F: < 0, 63 (< 1,6 mmol/l) risque r&eacute;duit pour coronaropathies <br>
                          N: H > 1, 72 (> 4,5 mmol/l); F: > 1, 67 (4, 3 mmol/l) risque accru pour coronaropathies <br>
                          CIBLE THERAPEUTIQUE : <br>
                          En pr&eacute;sence d'un seul facteur de risque < 4,9 mmol/L < 1,90 g/L <br>
                          En pr&eacute;sence de 2 facteurs de risque < 4,1 mmol/L < 1,60 g/L <br>
                          En pr&eacute;sence de plus de 2 facteurs de risque < 3,4 mmol/L < 1,30 g/L <br>
                          En cas d'ant&eacute;c&eacute;dent cardiovasculaire < 2,6 mmol/L < 1,00 g/L <br>
	        
	                  </label>
	                </td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	
	/**
	 * analyse 30
	 */
	public function lipides_totaux_30(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    $html .= "<tr class='ligneAnanlyse labelTypeMateriel' style='width: 100%; font-family: times new roman; font-size: 15px; margin-top: -45px;'>";
	    $html .= "  <td style='width: 55%;'> <label> Mat&eacute;riel utilis&eacute;</label> </td>";
	    $html .= "  <td colspan='2' style='width: 35%;'> </td>";
	    $html .= "</tr>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px;'>";
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_lipides_totaux' > </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> lipides totaux <input id='lipides_totaux' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> g/l </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	/**
	 * analyse 31
	 */
	public function ionogramme_31(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    $html .= "<tr class='ligneAnanlyse labelTypeMateriel' style='width: 100%; font-family: times new roman; font-size: 15px; margin-top: -45px;'>";
	    $html .= "  <td style='width: 55%;'> <label> Mat&eacute;riel utilis&eacute;</label> </td>";
	    $html .= "  <td colspan='2' style='width: 35%;'> </td>";
	    $html .= "</tr>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px;'>";
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_ionogramme' > </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> Sodium Sanguin ou Natr&eacute;mie <input id='sodium_sanguin' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> mmol/l </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> N: 136 &agrave; 145 </label></td>";
	    $html .= "</tr>";
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> Potassium Sanguin ou Kali&eacute;mie <input id='potassium_sanguin' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> mmol/l </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> N: 3,5 &agrave; 5,1 </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> Chlore Sanguin ou Chlor&eacute;mie <input id='chlore_sanguin' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> mmol/l </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> N: 97 &agrave; 111 </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	/**
	 * analyse 32
	 */
	public function calcemie_32(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    $html .= "<tr class='ligneAnanlyse labelTypeMateriel' style='width: 100%; font-family: times new roman; font-size: 15px; margin-top: -45px;'>";
	    $html .= "  <td style='width: 55%;'> <label> Mat&eacute;riel utilis&eacute;</label> </td>";
	    $html .= "  <td colspan='2' style='width: 35%;'> </td>";
	    $html .= "</tr>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px;'>";
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_calcemie' > </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    
	    $html .= "</table>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<table style='width: 100%;'>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 45%;'><label class='lab1' ><span style='font-weight: bold;'> Calc&eacute;mie <input id='calcemie' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 10%;'><label class='lab2' style='padding-top: 5px;'> mg/l </label></td>";
	    $html .= "  <td style='width: 45%;'><label class='lab3' style='padding-top: 5px; width: 90%;'> ( Adultes: 86 &agrave; 103 ; Enfants: 100 &agrave; 120 ) </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 45%;'><label class='lab1' ><span style='font-weight: bold;'> Calc&eacute;mie <input id='calcemie_mmol' type='number' step='any' readonly> </span></label></td>";
	    $html .= "  <td style='width: 10%;'><label class='lab2' style='padding-top: 5px;'> mmol/l </label></td>";
	    $html .= "  <td style='width: 45%;'><label class='lab3' style='padding-top: 5px; width: 90%; font-size: 13px;'> ( Adultes: 2,14 &agrave; 2,56 ; Enfants: 2,49 &agrave; 2,98 ) </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	/**
	 * analyse 33
	 */
	public function magnesemie_33(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    $html .= "<tr class='ligneAnanlyse labelTypeMateriel' style='width: 100%; font-family: times new roman; font-size: 15px; margin-top: -45px;'>";
	    $html .= "  <td style='width: 55%;'> <label> Mat&eacute;riel utilis&eacute;</label> </td>";
	    $html .= "  <td colspan='2' style='width: 35%;'> </td>";
	    $html .= "</tr>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px;'>";
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_magnesemie' > </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold; '> magn&eacute;s&eacute;mie <input id='magnesemie' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> mg/l </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> N: 17 &agrave; 24 </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	/**
	 * analyse 34
	 */
	public function phosphoremie_34(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    $html .= "<tr class='ligneAnanlyse labelTypeMateriel' style='width: 100%; font-family: times new roman; font-size: 15px; margin-top: -45px;'>";
	    $html .= "  <td style='width: 55%;'> <label> Mat&eacute;riel utilis&eacute;</label> </td>";
	    $html .= "  <td colspan='2' style='width: 35%;'> </td>";
	    $html .= "</tr>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px;'>";
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_phosphoremie' > </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    
	    $html .= "</table>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<table style='width: 100%;'>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 45%;'><label class='lab1' ><span style='font-weight: bold;'> Phosphor&eacute;mie  <input id='phosphoremie' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 10%;'><label class='lab2' style='padding-top: 5px;'> mg/l </label></td>";
	    $html .= "  <td style='width: 45%;'><label class='lab3' style='padding-top: 5px; width: 90%;'> ( Adultes: 25 &agrave; 50 ;  Enfants: 40 &agrave; 70 ) </label></td>";
	    $html .= "</tr>";
	     
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 45%;'><label class='lab1' ><span style='font-weight: bold;'> <input id='phosphoremie_mmol' type='number' step='any' readonly> </span></label></td>";
	    $html .= "  <td style='width: 10%;'><label class='lab2' style='padding-top: 5px;'> mmol/l </label></td>";
	    $html .= "  <td style='width: 45%;'><label class='lab3' style='padding-top: 5px; width: 90%;  font-size: 13px;'> ( Adultes: 8,07 &agrave; 16,15 ; Enfants: 12,92 &agrave; 22,61 ) </label></td>";
	    $html .= "</tr>";
	    
	    
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	/**
	 * analyse 35
	 */
	public function tgo_asat_35(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    $html .= "<tr class='ligneAnanlyse labelTypeMateriel' style='width: 100%; font-family: times new roman; font-size: 15px; margin-top: -45px;'>";
	    $html .= "  <td style='width: 55%;'> <label> Mat&eacute;riel utilis&eacute;</label> </td>";
	    $html .= "  <td colspan='2' style='width: 35%;'> </td>";
	    $html .= "</tr>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px;'>";
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_tgo_asat' > </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold; '> TGO/ASAT <input id='tgo_asat' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> u/l </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> N: H < 35 , F < 31 </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	/**
	 * analyse 36
	 */
	public function tgp_alat_36(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    $html .= "<tr class='ligneAnanlyse labelTypeMateriel' style='width: 100%; font-family: times new roman; font-size: 15px; margin-top: -45px;'>";
	    $html .= "  <td style='width: 55%;'> <label> Mat&eacute;riel utilis&eacute;</label> </td>";
	    $html .= "  <td colspan='2' style='width: 35%;'> </td>";
	    $html .= "</tr>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px;'>";
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_tgp_alat' > </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold; '> TGP/ALAT <input id='tgp_alat' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> u/l </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> N: H < 45 , F < 34 </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	/**
	 * analyse 38
	 */
	public function phosphatage_alcaline_38(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    $html .= "<tr class='ligneAnanlyse labelTypeMateriel' style='width: 100%; font-family: times new roman; font-size: 15px; margin-top: -45px;'>";
	    $html .= "  <td style='width: 55%;'> <label> Mat&eacute;riel utilis&eacute;</label> </td>";
	    $html .= "  <td colspan='2' style='width: 35%;'> </td>";
	    $html .= "</tr>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px;'>";
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_phosphatage_alcaline' > </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 50%;'><label class='lab1' ><span style='font-weight: bold; '> Phosphatage alcaline <input id='phosphatage_alcaline' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> u/l </label></td>";
	    $html .= "  <td style='width: 35%;'><label class='lab3' style='padding-top: 5px; width: 85%;'> N: H: 40 &agrave; 129 ; F: 35 &agrave; 104 </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	/**
	 * analyse 39
	 */
	public function gama_gt_ygt_39(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    $html .= "<tr class='ligneAnanlyse labelTypeMateriel' style='width: 100%; font-family: times new roman; font-size: 15px; margin-top: -45px;'>";
	    $html .= "  <td style='width: 55%;'> <label> Mat&eacute;riel utilis&eacute;</label> </td>";
	    $html .= "  <td colspan='2' style='width: 35%;'> </td>";
	    $html .= "</tr>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px;'>";
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_gama_gt_ygt' > </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold; '> Gama GT <input id='gama_gt' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> UI/l </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> N: H: 11 &agrave; 50 ; F: 7 &agrave; 32  </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	/**
	 * analyse 40
	 */
	public function fer_serique_40(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    $html .= "<tr class='ligneAnanlyse labelTypeMateriel' style='width: 100%; font-family: times new roman; font-size: 15px; margin-top: -45px;'>";
	    $html .= "  <td style='width: 55%;'> <label> Mat&eacute;riel utilis&eacute;</label> </td>";
	    $html .= "  <td colspan='2' style='width: 35%;'> </td>";
	    $html .= "</tr>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px;'>";
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_fer_serique' > </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 50%;'><label class='lab1' ><span style='font-weight: bold; '> Fer s&eacute;rique <input id='fer_serique_ug' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 10%;'><label class='lab2' style='padding-top: 5px;'> ug/dl </label></td>";
	    $html .= "  <td style='width: 40%;'><label class='lab3' style='padding-top: 5px; width: 85%;'> N: H: 64,8  &agrave; 175 - F: 50,3 &agrave; 170 </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 50%;'><label class='lab1' ><span style='font-weight: bold; '>  <input id='fer_serique_umol' type='number' step='any' readonly> </span></label></td>";
	    $html .= "  <td style='width: 10%;'><label class='lab2' style='padding-top: 5px;'> umol/l </label></td>";
	    $html .= "  <td style='width: 40%;'><label class='lab3' style='padding-top: 5px; width: 85%;'> N: H: 11,6 &agrave; 31,3 - F: 9,0 &agrave; 30,4 </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	/**
	 * analyse 41
	 */
	public function ferritinine_41(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    $html .= "<tr class='ligneAnanlyse labelTypeMateriel' style='width: 100%; font-family: times new roman; font-size: 15px; margin-top: -45px;'>";
	    $html .= "  <td style='width: 55%;'> <label> Mat&eacute;riel utilis&eacute;</label> </td>";
	    $html .= "  <td colspan='2' style='width: 35%;'> </td>";
	    $html .= "</tr>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px;'>";
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_ferritinine' > </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold; '> ferritinine <input id='ferritinine' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> ng/ml </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'>  </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    
	    $html .= "<tr> <td align='center'>";
	    $html .= "<table style='width: 90%; margin-left: -20px;'>";
	    
	    $html .= "<tr style='width: 100%;'>";
	    $html .= "  <td style='width: 100%;'>
	                  <label class='lab1' style='background: #f9f9f9; height: 90px; padding-left: 10px; font-size: 13px;' >
	                      <span style='text-decoration: underline;'> Valeurs Attendues: </span> <br>
	    
                          - hommes: 70 -  435    ng/ml <br>   
                          - Femmes cycliques: 10 - 160    ng/ml <br>    
                          - Femmes m&eacute;nopaus&eacute;es: 25 - 280    ng/ml <br>   
	        
	                  </label>
	                </td>";
	    $html .= "</tr>";
	    
	    $html .= "</table> </td> </tr>";
	    
	    return $html;
	}
	
	/**
	 * analyse 42
	 */
	public function bilirubine_totale_directe_42(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    $html .= "<tr class='ligneAnanlyse labelTypeMateriel' style='width: 100%; font-family: times new roman; font-size: 15px; margin-top: -45px;'>";
	    $html .= "  <td style='width: 55%;'> <label> Mat&eacute;riel utilis&eacute;</label> </td>";
	    $html .= "  <td colspan='2' style='width: 35%;'> </td>";
	    $html .= "</tr>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px;'>";
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_bilirubine_totale_directe' > </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    $html .= "</table>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<table style='width: 100%;'>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 8%;'><label class='lab1'><span style='font-weight: bold; '> </span></label></td>";
	    $html .= "  <td style='width: 42%;'><label class='lab1'><span style='font-weight: bold; '> Bilirubine totale <input id='bilirubine_totale' type='number' step='any'> mg/l </span></label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab2' style='padding-top: 5px;'><span style='font-weight: bold; '> <input id='bilirubine_totale_auto' type='number' step='any' tabindex='3' readonly> umol/l </span></label></td>";
	    $html .= "  <td style='width: 20%;'><label class='lab3' style='padding-top: 5px; width: 80%;'>  </label></td>";
	    $html .= "</tr>";
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 8%;'><label class='lab1'><span style='font-weight: bold; '> </span></label></td>";
	    $html .= "  <td style='width: 42%;'><label class='lab1'><span style='font-weight: bold; '> Bilirubine directe <input id='bilirubine_directe' type='number' step='any'> mg/l </span></label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab2' style='padding-top: 5px;'><span style='font-weight: bold; '> <input id='bilirubine_directe_auto' type='number' step='any' tabindex='3' readonly> umol/l </span></label></td>";
	    $html .= "  <td style='width: 20%;'><label class='lab3' style='padding-top: 5px; width: 80%;'>  </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 8%;'><label class='lab1'><span style='font-weight: bold; '> </span></label></td>";
	    $html .= "  <td style='width: 42%;'><label class='lab1'><span style='font-weight: bold; '> Bilirubine indirecte <input id='bilirubine_indirecte' type='number' step='any' readonly> mg/l </span></label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab2' style='padding-top: 5px;'><span style='font-weight: bold; '> <input id='bilirubine_indirecte_auto' type='number' step='any' tabindex='3' readonly> umol/l </span></label></td>";
	    $html .= "  <td style='width: 20%;'><label class='lab3' style='padding-top: 5px; width: 80%;'>  </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	/**
	 * analyse 43
	 */
	public function hemoglobine_glyquee_hbac_43(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%; margin-left: 5px;'>";
	
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    $html .= "<tr class='ligneAnanlyse labelTypeMateriel' style='width: 100%; font-family: times new roman; font-size: 15px; margin-top: -45px;'>";
	    $html .= "  <td style='width: 55%;'> <label> Mat&eacute;riel utilis&eacute;</label> </td>";
	    $html .= "  <td colspan='2' style='width: 35%;'> </td>";
	    $html .= "</tr>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px;'>";
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_hemo_gly_hbac' > </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 50%;'><label class='lab1'><span style='font-weight: bold; '>  <input id='hemoglobine_glyquee_hbac' type='number' step='any'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> % </label></td>";
	    $html .= "  <td style='width: 35%;'><label class='lab3' style='padding-top: 5px; width: 89%; font-size: 13px;'> HbA1C DCCT N: 4,27 - 6,07 </label></td>";
	    $html .= "</tr>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 50%;'><label class='lab1' ><span style='font-weight: bold; '>  <input id='hemoglobine_glyquee_hbac_mmol' type='number' step='any' readonly> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> mmol/mol </label></td>";
	    $html .= "  <td style='width: 35%;'><label class='lab3' style='padding-top: 5px; width: 89%; font-size: 13px;'> HbA1C IFCC N: 23 - 42 </label></td>";
	    $html .= "</tr>";
	     
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	/**
	 * analyse 44
	 */
	public function electrophorese_hemoglobine_44(){

	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%; margin-left: 5px;'>";
	    
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    $html .= "<tr class='ligneAnanlyse labelTypeMateriel' style='width: 100%; font-family: times new roman; font-size: 15px; margin-top: -45px;'>";
	    $html .= "  <td style='width: 55%;'> <label> Mat&eacute;riel utilis&eacute;</label> </td>";
	    $html .= "  <td colspan='2' style='width: 35%;'> </td>";
	    $html .= "</tr>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px;'>";
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_electro_hemo' > </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "</table> </td> </tr>";
	    
	    
	    $html .= "<tr> <td align='center'>";
	    $html .= "<table id='electro_hemo' style='width: 100%; margin-left: 5px;'>";
	
	    $html .= "<tr id='electro_hemo_1' class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 45%;'><label class='lab1'><span style='font-weight: bold; '>  <input id='electro_hemo_label_1' type='text' style='font-weight: bold; padding-right: 5px; margin-right: 30px;' maxlength=4  onkeydown='if(event.keyCode==32) return false;'> </span></label></td>";
	    $html .= "  <td style='width: 35%;'><label class='lab2' style='padding-top: 5px;'> <input id='electro_hemo_valeur_1' type='number' step='any'> % </label></td>";
	    $html .= "  <td style='width: 20%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> </label></td>";
	    $html .= "</tr>";
	    
        $html .= "<tr class='ligneAnanlyse' id='electro_hemo_mp' style='width: 100%;'>";
	    $html .= "  <td style='width: 45%;'> <div style='float: left; width: 25%; text-align: center; font-weight: bold; font-size: 25px;'> <div style='float: left; width: 50%; cursor: pointer; ' id='electro_hemo_moins'> - </div> <div style=' float: left; width: 45%; cursor: pointer;'  id='electro_hemo_plus'> + </div> </div> </label></td>";
	    $html .= "  <td style='width: 35%;'></td>";
	    $html .= "  <td style='width: 20%;'></td>";
	    $html .= "</tr>";
	    
	    $html .= "</table>";

	    
	    $html .= "<table id='conclusion_resultat_electro_hemo' style='width: 100%; margin-left: 5px; margin-top: 15px;'>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "<td style='width: 96%;'><label style='height: 140px;' ><span style='font-size: 16px; float: left;  margin-left: 30px;'> Commentaire:  </span> <textarea id='conclusion_electro_hemo_valeur' style='max-height: 100px; min-height: 100px; max-width: 560px; min-width: 560px; margin-left: 30px;'> </textarea> </label></td>";
	    $html .= "<td style='width: 5%;'></td>";
	    $html .= "</tr>";
	    $html .= "</table>";
	    
	    
	    $html .= "</td> </tr>";
	     
	    
		return $html;
	}
	
	/**
	 * analyse 45
	 */
	public function electrophorese_preteines_45(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%; margin-left: 5px;'>";
	
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    $html .= "<tr class='ligneAnanlyse labelTypeMateriel' style='width: 100%; font-family: times new roman; font-size: 15px; margin-top: -45px;'>";
	    $html .= "  <td style='width: 45%;'> <label> Mat&eacute;riel utilis&eacute;</label> </td>";
	    $html .= "  <td colspan='2' style='width: 35%;'> </td>";
	    $html .= "</tr>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px;'>";
	    $html .= "  <td style='width: 35%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_electro_proteine' tabindex='1' > </div> </td>";
	    $html .= "  <td colspan='3' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 35%;'><label><span style='font-weight: bold; '> Albumine <input id='albumine' type='number' step='any' tabindex='1'> </span></label></td>";
	    $html .= "  <td style='width: 10%;'><label style='padding-top: 5px;'> % </label></td>";
	    $html .= "  <td style='width: 15%;'><label style='padding-top: 5px;'> <input id='albumine_abs' type='number' step='any' readonly='true' > </label></td>";
	    $html .= "  <td style='width: 40%;'><label style='padding-top: 5px; width: 50%; padding-left: 55px; font-size: 14px;'> 40,2 - 47,6 </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td ><label><span style='font-weight: bold; '> Alpha 1 <input id='alpha_1' type='number' step='any' tabindex='2'> </span></label></td>";
	    $html .= "  <td ><label style='padding-top: 5px;'> % </label></td>";
	    $html .= "  <td ><label style='padding-top: 5px;'> <input id='alpha_1_abs' type='number' step='any' readonly='true' > </label></td>";
	    $html .= "  <td ><label style='padding-top: 5px; width: 50%; padding-left: 55px; font-size: 14px;'> 2,1 - 3,5 </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td ><label><span style='font-weight: bold; '> Alpha 2 <input id='alpha_2' type='number' step='any' tabindex='3'> </span></label></td>";
	    $html .= "  <td ><label style='padding-top: 5px;'> % </label></td>";
	    $html .= "  <td ><label style='padding-top: 5px;'> <input id='alpha_2_abs' type='number' step='any' readonly='true' > </label></td>";
	    $html .= "  <td ><label style='padding-top: 5px; width: 50%; padding-left: 55px; font-size: 14px;'> 5,1 - 8,5 </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td ><label><span style='font-weight: bold; '> Beta 1 <input id='beta_1' type='number' step='any' tabindex='4'> </span></label></td>";
	    $html .= "  <td ><label style='padding-top: 5px;'> % </label></td>";
	    $html .= "  <td ><label style='padding-top: 5px;'> <input id='beta_1_abs' type='number' step='any' readonly='true' > </label></td>";
	    $html .= "  <td ><label style='padding-top: 5px; width: 50%; padding-left: 55px; font-size: 14px;'> 3,4 - 5,2 </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td ><label><span style='font-weight: bold; '> Beta 2 <input id='beta_2' type='number' step='any' tabindex='5'> </span></label></td>";
	    $html .= "  <td ><label style='padding-top: 5px;'> % </label></td>";
	    $html .= "  <td ><label style='padding-top: 5px;'> <input id='beta_2_abs' type='number' step='any' readonly='true' > </label></td>";
	    $html .= "  <td ><label style='padding-top: 5px; width: 50%; padding-left: 55px; font-size: 14px;'> 2,3 - 4,7 </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td ><label><span style='font-weight: bold; '> Gamma <input id='gamma' type='number' step='any' tabindex='6'> </span></label></td>";
	    $html .= "  <td ><label style='padding-top: 5px;'> % </label></td>";
	    $html .= "  <td ><label style='padding-top: 5px;'> <input id='gamma_abs' type='number' step='any' readonly='true' > </label></td>";
	    $html .= "  <td ><label style='padding-top: 5px; width: 50%; padding-left: 55px; font-size: 14px;'> 8,0 - 13,5 </label></td>";
	    $html .= "</tr>";
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td colspan='4' style='height: 3px;'></td>";
	    $html .= "</tr>";
	     
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td colspan='2' ><label><span style='font-size: 16px;'> Proteine totale:  </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label style='padding-top: 5px;'> <input id='proteine_totale' type='number' step='any'  tabindex='7'> </label></td>";
	    $html .= "  <td style='width: 45%;'><label style='padding-top: 5px; width: 80%; font-size: 14px;'> g/dL </label></td>";
	    $html .= "</tr>";

	    $html .= "</table>";
	    
	    
	    $html .= "<table style='width: 100%; margin-left: 5px;'>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "<td style='width: 96%;'><label style='height: 140px;' ><span style='font-size: 16px; float: left;  margin-left: 30px;'> Commentaire:  </span> <textarea id='commentaire_electrophorese_proteine' style='max-height: 100px; min-height: 100px; max-width: 560px; min-width: 560px; margin-left: 30px;' tabindex='8'> </textarea> </label></td>";
	    $html .= "<td style='width: 5%;'></td>";
	    $html .= "</tr>";
	    $html .= "</table>";
	    
	    
	    $html .="</td> </tr>";
	
	    
	    
	    
	    return $html;
	}
	
	/**
	 * analyse 46
	 */
	public function albuminemie_46(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    $html .= "<tr class='ligneAnanlyse labelTypeMateriel' style='width: 100%; font-family: times new roman; font-size: 15px; margin-top: -45px;'>";
	    $html .= "  <td style='width: 55%;'> <label> Mat&eacute;riel utilis&eacute;</label> </td>";
	    $html .= "  <td colspan='2' style='width: 35%;'> </td>";
	    $html .= "</tr>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px;'>";
	    $html .= "  <td style='width: 45%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_albuminemie' tabindex='1' > </div> </td>";
	    $html .= "  <td colspan='3' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '> Albumin&eacute;mie <input id='albuminemie' type='number' step='any' tabindex='2'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> g/l </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'>( 35 - 53 )</label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '>  <input id='albuminemie_umol' type='number' step='any' tabindex='2' readonly> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> umol/l </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> ( 507,25 - 768,12 ) </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	/**
	 * analyse 47
	 */
	public function albumine_urinaire_47(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    $html .= "<tr class='ligneAnanlyse labelTypeMateriel' style='width: 100%; font-family: times new roman; font-size: 15px; margin-top: -45px;'>";
	    $html .= "  <td style='width: 55%;'> <label> Mat&eacute;riel utilis&eacute;</label> </td>";
	    $html .= "  <td colspan='2' style='width: 35%;'> </td>";
	    $html .= "</tr>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px;'>";
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_albumine_urinaire' tabindex='1' > </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '> Albumine <select id='albumine_urinaire' tabindex='2' onchange='getAlbumineUrinaireVal(this.value)' style='font-size: 14px;'> <option >  </option> <option value='positif'> Positif </option> <option value='negatif'> N&eacute;gatif </option> </select>  </span></label></td>";
	    $html .= "  <td style='width: 5%;'><label class='lab2' style='padding-top: 5px;'>  </label></td>";
	    $html .= "  <td style='width: 40%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> <select id='albumine_urinaire_degres' style='display:none; width: 50px; font-weight: bold; padding-left: 5px;'> <option value='1+'> 1+ </option> <option value='2+'> 2+ </option> <option value='3+'> 3+ </option> <option value='4+'> 4+ </option> </select> </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '> Sucre   <select id='sucre_urinaire' tabindex='3' onchange='getSucreUrinaireVal(this.value)' style='font-size: 14px;'> <option >  </option> <option value='positif'> Positif </option> <option value='negatif'> N&eacute;gatif </option> </select> </span></label></td>";
	    $html .= "  <td style='width: 5%;'><label class='lab2' style='padding-top: 5px;'>  </label></td>";
	    $html .= "  <td style='width: 40%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> <select id='sucre_urinaire_degres' style='display:none; width: 50px; font-weight: bold; padding-left: 5px;'> <option value='1+'> 1+ </option> <option value='2+'> 2+ </option> <option value='3+'> 3+ </option> <option value='4+'> 4+ </option> </select> </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '> Corps c&eacute;tonique <select id='corps_cetonique_urinaire' tabindex='4' onchange='getCorpsCetoniqueUrinaireVal(this.value)' style='font-size: 14px;'> <option >  </option> <option value='positif'> Positif </option> <option value='negatif'> N&eacute;gatif </option> </select> </span></label></td>";
	    $html .= "  <td style='width: 5%;'><label class='lab2' style='padding-top: 5px;'>  </label></td>";
	    $html .= "  <td style='width: 40%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> <select id='corps_cetonique_urinaire_degres' style='display:none; width: 50px; font-weight: bold; padding-left: 5px;'> <option value='1+'> 1+ </option> <option value='2+'> 2+ </option> <option value='3+'> 3+ </option> <option value='4+'> 4+ </option> </select> </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	/**
	 * analyse 48
	 */
	public function protidemie_48(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    $html .= "<tr class='ligneAnanlyse labelTypeMateriel' style='width: 100%; font-family: times new roman; font-size: 15px; margin-top: -45px;'>";
	    $html .= "  <td style='width: 55%;'> <label> Mat&eacute;riel utilis&eacute;</label> </td>";
	    $html .= "  <td colspan='2' style='width: 35%;'> </td>";
	    $html .= "</tr>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px;'>";
	    $html .= "  <td style='width: 45%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_protidemie' tabindex='1' > </div> </td>";
	    $html .= "  <td colspan='3' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 45%;'><label class='lab1'><span style='font-weight: bold; '> Protid&eacute;mie <input id='protidemie' type='number' step='any' tabindex='2'> </span></label></td>";
	    $html .= "  <td style='width: 10%;'><label class='lab2' style='padding-top: 5px;'> g/l </label></td>";
	    $html .= "  <td style='width: 45%;'><label class='lab3' style='padding-top: 5px; width: 89%; font-size: 13px;'> N: Adultes: 66 &agrave; 83 ; Nouveaux n&eacute;s: 52 &agrave; 91 </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	/**
	 * analyse 49
	 */
	public function proteinurie_49(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    $html .= "<tr class='ligneAnanlyse labelTypeMateriel' style='width: 100%; font-family: times new roman; font-size: 15px; margin-top: -45px;'>";
	    $html .= "  <td style='width: 55%;'> <label> Mat&eacute;riel utilis&eacute;</label> </td>";
	    $html .= "  <td colspan='2' style='width: 35%;'> </td>";
	    $html .= "</tr>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px;'>";
	    $html .= "  <td style='width: 45%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_proteinurie' tabindex='1' > </div> </td>";
	    $html .= "  <td colspan='3' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style=''> <input id='proteinurie_1' style='margin-right: 5px;' type='number' step='any' tabindex='2'> g/l <input id='proteinurie_2' style='margin-left: 30px;' type='number' step='any' tabindex='2'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> l </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'>  </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '> <input id='proteinurie_g24h' type='number' step='any' tabindex='2' readonly> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> g/24H </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> <  0,15 g/24H </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	/**
	 * analyse 50
	 */
	public function hlm_compte_daddis_50(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    $html .= "<tr class='ligneAnanlyse labelTypeMateriel' style='width: 100%; font-family: times new roman; font-size: 15px; margin-top: -45px;'>";
	    $html .= "  <td style='width: 55%;'> <label> Mat&eacute;riel utilis&eacute;</label> </td>";
	    $html .= "  <td colspan='2' style='width: 35%;'> </td>";
	    $html .= "</tr>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px;'>";
	    $html .= "  <td style='width: 45%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_hlm_compte_daddis' tabindex='1' > </div> </td>";
	    $html .= "  <td colspan='3' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '> H&eacute;maties <input id='hematies_hlm' type='number' step='any' tabindex='2'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> /min </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> N < 2000 </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '> Leucocytes <input id='leucocytes_hlm' type='number' step='any' tabindex='3'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> /min </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> N < 2000 </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "</table>";
	
	    $html .= "<table style='width: 100%;'>";
	     
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td colspan='2' ><label style='height: 80px;' ><span style='font-size: 16px; float: left;  margin-left: 30px;'> Commentaire:  </span> <textarea id='commentaire_hlm_compte_daddis' style='max-height: 57px; min-height: 57px; max-width: 400px; min-width: 400px; margin-left: 30px;' > </textarea> </label></td>";
	    $html .= "  <td style='width: 30%;'><label style='padding-top: 5px; width: 80%; height: 80px; font-size: 14px;'>  </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	/**
	 * analyse 51
	 */
	public function beta_hcg_plasmatique_51(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    $html .= "<tr class='ligneAnanlyse labelTypeMateriel' style='width: 100%; font-family: times new roman; font-size: 15px; margin-top: -45px;'>";
	    $html .= "  <td style='width: 55%;'> <label> Mat&eacute;riel utilis&eacute;</label> </td>";
	    $html .= "  <td colspan='2' style='width: 35%;'> </td>";
	    $html .= "</tr>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px;'>";
	    $html .= "  <td style='width: 45%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_beta_hcg' tabindex='1' > </div> </td>";
	    $html .= "  <td colspan='3' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '> B&eacute;ta HCG <select name='beta_hcg_plasmatique' id='beta_hcg_plasmatique' > <option >  </option> <option value='Positif' >Positif</option> <option value='Negatif' >N&eacute;gatif</option> </select> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'>  </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'>  </label></td>";
	    $html .= "</tr>";
	     
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	/**
	 * analyse 52
	 */
	public function psa_52(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    $html .= "<tr class='ligneAnanlyse labelTypeMateriel' style='width: 100%; font-family: times new roman; font-size: 15px; margin-top: -45px;'>";
	    $html .= "  <td style='width: 55%;'> <label> Mat&eacute;riel utilis&eacute;</label> </td>";
	    $html .= "  <td colspan='2' style='width: 35%;'> </td>";
	    $html .= "</tr>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px;'>";
	    $html .= "  <td style='width: 45%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_psa' tabindex='1' > </div> </td>";
	    $html .= "  <td colspan='3' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    $html .= "</table>";
	     
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    $html .= "<table style='width: 100%;'>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 30%;'><label class='lab1'><span style='font-weight: bold; '>  <select name='psa_qualitatif' id='psa_qualitatif' tabindex='3'> <option>  </option> <option value='Positif' >Positif</option> <option value='Negatif' >N&eacute;gatif</option> </select> </span></label></td>";
	    $html .= "  <td style='width: 40%;'><label class='lab2' style='padding-top: 5px;'><span style='font-weight: bold; '> Titre <input id='psa' type='number' step='any' tabindex='2'> ng/ml </span></label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> Qualitatif </label></td>";
	    $html .= "</tr>";
	     
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	/**
	 * analyse 53
	 */
	public function crp_53(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    $html .= "<tr class='ligneAnanlyse labelTypeMateriel' style='width: 100%; font-family: times new roman; font-size: 15px; margin-top: -45px;'>";
	    $html .= "  <td style='width: 55%;'> <label> Mat&eacute;riel utilis&eacute;</label> </td>";
	    $html .= "  <td colspan='2' style='width: 35%;'> </td>";
	    $html .= "</tr>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px;'>";
	    $html .= "  <td style='width: 45%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_crp' tabindex='1' > </div> </td>";
	    $html .= "  <td colspan='3' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "</table>";
	    
	    $html .= "<table style='width: 100%;'>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 45%;'><label class='lab1'><span style='font-weight: bold; '> CRP <select id='optionResultatCrp' onchange='getChoixResultatCrp(this.value);'> <option value=''></option> <option value='positif'> Positif </option> <option value='negatif'> N&eacute;gatif </option> </select> </span></label></td>";
	    $html .= "  <td style='width: 25%;'><label class='lab2' style='padding-top: 5px;'> <span id='crpValeurResultatChamp' style='visibility: hidden;'> <input id='crpValeurResultat' type='number' step='any' tabindex='2'> mg/l </span> </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%; text-align: center;'> < 6 mg/l </label></td>";
	    $html .= "</tr>";
	     
	    $html .= "</table>";
	    
	    $html .= "</td> </tr>";
	
	    return $html;
	}
	
	/**
	 * analyse 54
	 */
	public function facteurs_rhumatoides_54(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    $html .= "<tr class='ligneAnanlyse labelTypeMateriel' style='width: 100%; font-family: times new roman; font-size: 15px; margin-top: -45px;'>";
	    $html .= "  <td style='width: 55%;'> <label> Mat&eacute;riel utilis&eacute;</label> </td>";
	    $html .= "  <td colspan='2' style='width: 35%;'> </td>";
	    $html .= "</tr>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px;'>";
	    $html .= "  <td style='width: 45%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_facteurs_rhumatoides' tabindex='1' > </div> </td>";
	    $html .= "  <td colspan='3' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    
	    $html .= "</table>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<table style='width: 100%;'>";
	     
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 40%;'><label class='lab1'><span style='font-weight: bold; '> Rf Latex <select name='facteurs_rhumatoides' id='facteurs_rhumatoides' > <option>  </option> <option value='Positif' >Positif</option> <option value='Negatif' >N&eacute;gatif</option> </select>  </span></label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab2' style='padding-top: 5px;'><span style='font-weight: bold; '> Titre <input id='facteurs_rhumatoides_titre' type='number' step='any' style='width: 80px;' tabindex='3'> UI/ml </span></label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> N : 0 - 30 </label></td>";
	    $html .= "</tr>";
	     
	    $html .= "</table> </td> </tr>";
	    
	
	    return $html;
	}
	
	/**
	 * analyse 55
	 */
	public function rf_waaler_rose_55(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    $html .= "<tr class='ligneAnanlyse labelTypeMateriel' style='width: 100%; font-family: times new roman; font-size: 15px; margin-top: -45px;'>";
	    $html .= "  <td style='width: 55%;'> <label> Mat&eacute;riel utilis&eacute;</label> </td>";
	    $html .= "  <td colspan='2' style='width: 35%;'> </td>";
	    $html .= "</tr>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px;'>";
	    $html .= "  <td style='width: 45%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_rf_waaler_rose' tabindex='1' > </div> </td>";
	    $html .= "  <td colspan='3' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    $html .= "</table>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE

	    
	    $html .= "<table style='width: 100%;'>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 40%;'><label class='lab1'><span style='font-weight: bold; '> Rf Waaler Rose <select name='rf_waaler_rose' id='rf_waaler_rose' > <option>  </option> <option value='Positif' >Positif</option> <option value='Negatif' >N&eacute;gatif</option> </select>  </span></label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab2' style='padding-top: 5px;'><span style='font-weight: bold; '> Titre <input id='rf_waaler_rose_titre' type='number' step='any' style='width: 80px;' tabindex='3'> ng/ml </span></label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> < 8UI/mL </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	/**
	 * analyse 56
	 */
	public function toxoplasmose_56(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    $html .= "<tr class='ligneAnanlyse labelTypeMateriel' style='width: 100%; font-family: times new roman; font-size: 15px; margin-top: -45px;'>";
	    $html .= "  <td style='width: 55%;'> <label> Mat&eacute;riel utilis&eacute;</label> </td>";
	    $html .= "  <td colspan='2' style='width: 35%;'> </td>";
	    $html .= "</tr>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px;'>";
	    $html .= "  <td style='width: 45%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_toxoplasmose' tabindex='1' > </div> </td>";
	    $html .= "  <td colspan='3' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    $html .= "</table>";
	    
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    $html .= "<table style='width: 100%;'>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 40%;'><label class='lab1'><span style='font-weight: bold; '> IgM  <select name='toxoplasmose_igm' id='toxoplasmose_igm' > <option>  </option> <option value='Positif' >Positif</option> <option value='Negatif' >N&eacute;gatif</option> </select> </span></label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab2' style='padding-top: 5px;'><span style='font-weight: bold; '> Titre <input id='toxoplasmose_igm_titre' type='number' step='any' tabindex='2'> </span></label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'>  </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 40%;'><label class='lab1'><span style='font-weight: bold; '> IgG <select name='toxoplasmose_igg' id='toxoplasmose_igg' > <option>  </option> <option value='Positif' >Positif</option> <option value='Negatif' >N&eacute;gatif</option> </select>  </span></label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab2' style='padding-top: 5px;'><span style='font-weight: bold; '> Titre <input id='toxoplasmose_igg_titre' type='number' step='any' tabindex='3'> </span></label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'>  </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table>";
	     
	    $html .= "<table style='width: 100%;'>";
	     
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td colspan='2' ><label style='height: 80px;' ><span style='font-size: 16px; float: left;  margin-left: 30px;'> Commentaire:  </span> <textarea id='toxoplasmose_commentaire' style='max-height: 57px; min-height: 57px; max-width: 400px; min-width: 400px; margin-left: 30px;' > </textarea> </label></td>";
	    $html .= "  <td style='width: 30%;'><label style='padding-top: 5px; width: 80%; height: 80px; font-size: 14px;'>  </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	/**
	 * analyse 57
	 */
	public function rubeole_57(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    $html .= "<tr class='ligneAnanlyse labelTypeMateriel' style='width: 100%; font-family: times new roman; font-size: 15px; margin-top: -45px;'>";
	    $html .= "  <td style='width: 55%;'> <label> Mat&eacute;riel utilis&eacute;</label> </td>";
	    $html .= "  <td colspan='2' style='width: 35%;'> </td>";
	    $html .= "</tr>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px;'>";
	    $html .= "  <td style='width: 45%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_rubeole' tabindex='1' > </div> </td>";
	    $html .= "  <td colspan='3' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    $html .= "</table>";
	  
	    $html .= "<table style='width: 100%;'>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 40%;'><label class='lab1'><span style='font-weight: bold; '> IgM  <select name='rubeole_igm' id='rubeole_igm' > <option>  </option> <option value='Positif' >Positif</option> <option value='Negatif' >N&eacute;gatif</option> </select> </span></label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab2' style='padding-top: 5px;'><span style='font-weight: bold; '> Titre <input id='rubeole_igm_titre' type='number' step='any' tabindex='2'> </span></label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'>  </label></td>";
	    $html .= "</tr>";
	     
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 40%;'><label class='lab1'><span style='font-weight: bold; '> IgG <select name='rubeole_igg' id='rubeole_igg' > <option>  </option> <option value='Positif' >Positif</option> <option value='Negatif' >N&eacute;gatif</option> </select>  </span></label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab2' style='padding-top: 5px;'><span style='font-weight: bold; '> Titre <input id='rubeole_igg_titre' type='number' step='any' tabindex='3'> </span></label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'>  </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "</table>";
	     
	    $html .= "<table style='width: 100%;'>";
	     
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td colspan='2' ><label style='height: 80px;' ><span style='font-size: 16px; float: left;  margin-left: 30px;'> Commentaire:  </span> <textarea id='rubeole_commentaire' style='max-height: 57px; min-height: 57px; max-width: 400px; min-width: 400px; margin-left: 30px;' > </textarea> </label></td>";
	    $html .= "  <td style='width: 30%;'><label style='padding-top: 5px; width: 80%; height: 80px; font-size: 14px;'>  </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	/**
	 * analyse 58
	 */
	public function culot_urinaire_58(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    $html .= "<tr class='ligneAnanlyse labelTypeMateriel' style='width: 100%; font-family: times new roman; font-size: 15px; margin-top: -45px;'>";
	    $html .= "  <td style='width: 55%;'> <label> Mat&eacute;riel utilis&eacute;</label> </td>";
	    $html .= "  <td style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px;'>";
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_culot_urinaire' tabindex='1' > </div> </td>";
	    $html .= "  <td style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    $html .= "</table> </td> </tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	     
	    $html .= "<tr> <td align='center'>";
	    $html .= "<table id='culot_urinaire_tableau' style='width: 100%; margin-left: 5px;'>";
	    
	    $html .= "<tr id='culot_urinaire_ligne_1' class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 40%;'><label class='lab1 listeSelect'><span style='font-weight: bold; '> <select onchange='listeElemtsCulotUrinaireSelect(1,this.value);' name='culot_urinaire_select' id='culot_urinaire_select' > <option value=0>  </option> <option value='1' >Leucocytes</option> <option value='2' >H&eacute;maties</option> <option value='3' >Cristaux</option> <option value='4' >Oeufs</option> <option value='5' >Parasites</option> </select> </span></label></td>";
	    $html .= "  <td style='width: 40%;'><label class='lab2 emplaceListeElemtsCUSelect' style='padding-top: 5px;'>  </label></td>";
	    $html .= "  <td style='width: 20%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> </label></td>";
	    $html .= "</tr>";
	     
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 40%;'> <div style='float: left; width: 25%; text-align: center; font-weight: bold; font-size: 25px;'> <div style='float: left; width: 50%; cursor: pointer; display: none;' id='culot_urinaire_moins'> - </div> <div style=' float: left; width: 45%; cursor: pointer;'  id='culot_urinaire_plus'> + </div> </div> </label></td>";
	    $html .= "  <td style='width: 40%;'></td>";
	    $html .= "  <td style='width: 20%;'></td>";
	    $html .= "</tr>";
	     
	    $html .= "</table>";
	     
	    $html .= "<table id='conclusion_resultat_culot_urinaire' style='width: 100%; margin-left: 5px;'>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 90%;'><label class='lab1'><span style='font-weight: bold; width: 100%; text-align: right;'> Conclusion :  <input id='conclusion_culot_urinaire_valeur' type='text' step='any' style='width: 80%; float: right; text-align: left;'> </span></label></td>";
	    $html .= "  <td style='width: 10%;'><label class='lab3' style='padding-top: 5px; width: 56%;'> </label></td>";
	    $html .= "</tr>";
	    $html .= "</table> ";
	    
	    $html .= "</td> </tr>";
	    
	    return $html;
	}
	
	/**
	 * analyse 59
	 */
	public function serologie_chlamydiae_59(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    $html .= "<tr class='ligneAnanlyse labelTypeMateriel' style='width: 100%; font-family: times new roman; font-size: 15px; margin-top: -45px;'>";
	    $html .= "  <td style='width: 55%;'> <label> Mat&eacute;riel utilis&eacute;</label> </td>";
	    $html .= "  <td colspan='2' style='width: 35%;'> </td>";
	    $html .= "</tr>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px;'>";
	    $html .= "  <td style='width: 45%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_serologie_chlamydiae' tabindex='1' > </div> </td>";
	    $html .= "  <td colspan='3' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '>  <input id='serologie_chlamydiae' type='number' step='any' tabindex='2'> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'>  </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'>  </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	/**
	 * analyse 60
	 */
	public function serologie_syphilitique_60(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    $html .= "<tr class='ligneAnanlyse labelTypeMateriel' style='width: 100%; font-family: times new roman; font-size: 15px; margin-top: -45px;'>";
	    $html .= "  <td style='width: 55%;'> <label> Mat&eacute;riel utilis&eacute;</label> </td>";
	    $html .= "  <td colspan='2' style='width: 35%;'> </td>";
	    $html .= "</tr>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px;'>";
	    $html .= "  <td style='width: 45%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_serologie_syphilitique' tabindex='1' > </div> </td>";
	    $html .= "  <td colspan='3' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '> RPR  <select name='serologie_syphilitique_rpr' id='serologie_syphilitique_rpr' > <option>  </option> <option value='Positif' >Positif</option> <option value='Negatif' >N&eacute;gatif</option> </select> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'>  </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'>  </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '> TPHA  <select name='serologie_syphilitique_tpha' id='serologie_syphilitique_tpha' > <option>  </option> <option value='Positif' >Positif</option> <option value='Negatif' >N&eacute;gatif</option> </select> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> Titre <input class='serologie_syphilitique_tpha_titre' id='serologie_syphilitique_tpha_titre' type='text' style='height: 23px; margin-top: -1px; padding-left: 4px; text-align: left;' > </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	/**
	 * analyse 61
	 */
	public function aslo_61(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    $html .= "<tr class='ligneAnanlyse labelTypeMateriel' style='width: 100%; font-family: times new roman; font-size: 15px; margin-top: -45px;'>";
	    $html .= "  <td style='width: 55%;'> <label> Mat&eacute;riel utilis&eacute;</label> </td>";
	    $html .= "  <td colspan='2' style='width: 35%;'> </td>";
	    $html .= "</tr>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px;'>";
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_aslo' tabindex='1' > </div> </td>";
	    $html .= "  <td colspan='3' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td colspan='2' style='width: 70%;'><label class='lab1'><span style='font-weight: bold; float: left; padding-left: 100px;'> Aslo <select name='aslo_select' id='aslo_select' > <option>  </option> <option value='Positif' >Positif</option> <option value='Negatif' >N&eacute;gatif</option> </select> </span>    <span style='float: right;'>  Titre <input id='aslo_titre' type='number' step='any' tabindex='2' style='width: 70px;'> UI/ml </span> </label></td>";
	    $html .= "  <!-- td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> Titre <input id='aslo' type='number' step='any' tabindex='2' style='width: 80px;'> </label></td-->";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> <span style='padding-left: 30px;'> < 200 UI/mL </span> </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	/**
	 * analyse 62
	 */
	public function widal_62(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	    
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    $html .= "<tr class='ligneAnanlyse labelTypeMateriel' style='width: 100%; font-family: times new roman; font-size: 15px; margin-top: -45px;'>";
	    $html .= "  <td style='width: 55%;'> <label> Mat&eacute;riel utilis&eacute;</label> </td>";
	    $html .= "  <td colspan='2' style='width: 35%;'> </td>";
	    $html .= "</tr>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px;'>";
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_widal' tabindex='1' > </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '> Typhi TO  <select name='widal_to' id='widal_to' > <option>  </option> <option value='Positif' >Positif</option> <option value='Negatif' >N&eacute;gatif</option> </select> </span></label></td>";
	    $html .= "  <td colspan='2' style='width: 45%;'><label class='lab2' style='padding-top: 5px; width: 75%; padding-left: 40px;'> Titre: <input id='widal_titre_to' style='width: 60%; padding-right: 5px;' type='text' tabindex='2'> </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '> Typhi TH <select name='widal_th' id='widal_th' > <option>  </option> <option value='Positif' >Positif</option> <option value='Negatif' >N&eacute;gatif</option> </select>  </span></label></td>";
	    $html .= "  <td colspan='2' style='width: 45%;'><label class='lab2' style='padding-top: 5px; width: 75%; padding-left: 40px;'> Titre: <input id='widal_titre_th' style='width: 60%; padding-right: 5px;' type='text' tabindex='3'> </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '> Paratyphi AO <select name='widal_ao' id='widal_ao' > <option>  </option> <option value='Positif' >Positif</option> <option value='Negatif' >N&eacute;gatif</option> </select> </span></label></td>";
	    $html .= "  <td colspan='2' style='width: 45%;'><label class='lab2' style='padding-top: 5px; width: 75%; padding-left: 40px;'> Titre: <input id='widal_titre_ao' style='width: 60%; padding-right: 5px;' type='text' tabindex='4'> </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '> Paratyphi AH <select name='widal_ah' id='widal_ah' > <option>  </option> <option value='Positif' >Positif</option> <option value='Negatif' >N&eacute;gatif</option> </select> </span></label></td>";
	    $html .= "  <td colspan='2' style='width: 45%;'><label class='lab2' style='padding-top: 5px; width: 75%; padding-left: 40px;'> Titre: <input id='widal_titre_ah' style='width: 60%; padding-right: 5px;' type='text' tabindex='5'> </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '> Paratyphi BO <select name='widal_bo' id='widal_bo' > <option>  </option> <option value='Positif' >Positif</option> <option value='Negatif' >N&eacute;gatif</option> </select>  </span></label></td>";
	    $html .= "  <td colspan='2' style='width: 45%;'><label class='lab2' style='padding-top: 5px; width: 75%; padding-left: 40px;'> Titre: <input id='widal_titre_bo' style='width: 60%; padding-right: 5px;' type='text' tabindex='6'> </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '> Paratyphi BH <select name='widal_bh' id='widal_bh' > <option>  </option> <option value='Positif' >Positif</option> <option value='Negatif' >N&eacute;gatif</option> </select>  </span></label></td>";
	    $html .= "  <td colspan='2' style='width: 45%;'><label class='lab2' style='padding-top: 5px; width: 75%; padding-left: 40px;'> Titre: <input id='widal_titre_bh' style='width: 60%; padding-right: 5px;' type='text' tabindex='7'> </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '> Paratyphi CO <select name='widal_co' id='widal_co' > <option>  </option> <option value='Positif' >Positif</option> <option value='Negatif' >N&eacute;gatif</option> </select>  </span></label></td>";
	    $html .= "  <td colspan='2' style='width: 45%;'><label class='lab2' style='padding-top: 5px; width: 75%; padding-left: 40px;'> Titre: <input id='widal_titre_co' style='width: 60%; padding-right: 5px;' type='text' tabindex='8'> </label></td>";
	    $html .= "</tr>";
	     
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '> Paratyphi CH <select name='widal_ch' id='widal_ch' > <option>  </option> <option value='Positif' >Positif</option> <option value='Negatif' >N&eacute;gatif</option> </select>  </span></label></td>";
	    $html .= "  <td colspan='2' style='width: 45%;'><label class='lab2' style='padding-top: 5px; width: 75%; padding-left: 40px;'> Titre: <input id='widal_titre_ch' style='width: 60%; padding-right: 5px;' type='text' tabindex='9'> </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}

	/**
	 * analyse 63
	 */
	public function ag_hbs_63(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    $html .= "<tr class='ligneAnanlyse labelTypeMateriel' style='width: 100%; font-family: times new roman; font-size: 15px; margin-top: -45px;'>";
	    $html .= "  <td style='width: 55%;'> <label> Mat&eacute;riel utilis&eacute;</label> </td>";
	    $html .= "  <td colspan='2' style='width: 35%;'> </td>";
	    $html .= "</tr>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px;'>";
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_ag_hbs' tabindex='1' > </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '> Ag Hbs  <select name='ag_hbs' id='ag_hbs' > <option>  </option> <option value='Positif' >Positif</option> <option value='Negatif' >N&eacute;gatif</option> </select> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'>  </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> Qualitatif </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	/**
	 * analyse 64
	 */
	public function hiv_64(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    $html .= "<tr class='ligneAnanlyse labelTypeMateriel' style='width: 100%; font-family: times new roman; font-size: 15px; margin-top: -45px;'>";
	    $html .= "  <td style='width: 55%;'> <label> Mat&eacute;riel utilis&eacute;</label> </td>";
	    $html .= "  <td colspan='2' style='width: 35%;'> </td>";
	    $html .= "</tr>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px;'>";
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_hiv' tabindex='1' > </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    	  
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 35%;'><label class='lab1'><span style='font-weight: bold; '> HIV <select id='hiv' > <option >  </option> <option value='Positif' >Positif</option> <option value='Negatif' >N&eacute;gatif</option> </select> </span></label></td>";
	    $html .= "  <td style='width: 35%;'><label class='lab2' style='padding-top: 5px;'> <span style='float: left; '> Typage </span> <select id='hiv_typage' style='width: 120px;'> <option >  </option> <option value='hiv_1' >HIV 1</option> <option value='hiv_2' >HIV 2</option>  <option value='indetermine' >Ind&eacute;termin&eacute;</option> </select> </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 40%;'>  </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}

	/**
	 * analyse 65
	 */
	public function pv_65($idanalyse=null){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    $html .= "<tr class='ligneAnanlyse labelTypeMateriel' style='width: 100%; font-family: times new roman; font-size: 15px; margin-top: -45px;'>";
	    $html .= "  <td style='width: 55%;'> <label> Mat&eacute;riel utilis&eacute;</label> </td>";
	    $html .= "  <td colspan='2' style='width: 35%;'> </td>";
	    $html .= "</tr>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px;'>";
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_pv' tabindex='1' > </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    	  
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td colspan='3' style='width: 100%; text-align: left; font-weight: bold; font-size: 12px; font-style: italic;'> &#10148; Examen macroscopique</td>";
	    $html .= "</tr>";
	    $html .= "</table>";
	    
	    $html .= "<table style='width: 100%;'>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' style='padding-top: 5px;'><span style='font-weight: bold;'> Aspect des pertes (Abondance) </span></label></td>";
	    $html .= "  <td style='width: 15%;'>
	                    <label class='lab2' style='padding-top: 5px;'> 
	                      <select id='aspect_pertes_abondance_pv' style='width: 200px;'> 
	                        <option> </option> 
	                        <option value=1 >Peu abondante</option>
	                        <option value=2 >Abondante</option>
	                        <option value=3 >Tr&egrave;s abondante</option>
	                      </select>
	                    </label>
	                </td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' style='padding-top: 5px;'><span style='font-weight: bold;'> Aspect des pertes (Couleur) </span></label></td>";
	    $html .= "  <td style='width: 15%;'>
	                    <label class='lab2' style='padding-top: 5px;'>
	                      <select id='aspect_pertes_couleurs_pv' style='width: 200px;'>
	                        <option> </option>
	                        <option value=1 >Blanch&acirc;tres</option>
	                        <option value=2 >Stri&eacute;es de sang</option>
	                        <option value=3 >Caillebott&eacute;es</option>
	                        <option value=4 >Marron</option>
	                        <option value=5 >Gris&acirc;tre</option>
	                        <option value=6 >Verd&acirc;tre</option>
	                      </select>
	                    </label>
	                </td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' style='padding-top: 5px;'><span style='font-weight: bold;'> Aspect des pertes (Odeur) </span></label></td>";
	    $html .= "  <td style='width: 15%;'>
	                    <label class='lab2' style='padding-top: 5px;'>
	                      <select id='aspect_pertes_odeurs_pv' style='width: 200px;'>
	                        <option> </option>
	                        <option value=1 >F&eacute;tides</option>
	                        <option value=2 >Non f&eacute;tides</option>
	                      </select>
	                    </label>
	                </td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' style='padding-top: 5px;'><span style='font-weight: bold;'> Aspect organe (Col) </span></label></td>";
	    $html .= "  <td style='width: 15%;'>
	                    <label class='lab2' style='padding-top: 5px;'>
	                      <select id='aspect_organe_pv' style='width: 200px;'>
	                        <option> </option>
	                        <option value=1 >Col sain</option>
	                        <option value=2 >Col inflamm&eacute;</option>
  	                        <option value=3 >Col saignant au contact</option>
	                        <option value=4 >Col l&eacute;g&egrave;rement inflamm&eacute;</option>
	                      </select>
	                    </label>
	                </td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "</table>";
	     
	    
	    /**
	     * Partie examen microscopique *** Partie examen microscopique
	     * -----------------------------------------------------------
	     * Partie examen microscopique *** Partie examen microscopique
	     */
	    $html .= "<table style='width: 100%;'>";
	    /**
	     * Examen microscopique --- Examen microscopique --- Examen microscopique
	     */
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td colspan='3' style='width: 100%; text-align: left; font-weight: bold; font-size: 12px; font-style: italic;'> &#10148; Examen microscopique</td>";
	    $html .= "</tr>";
	    $html .= "</table>";
	    /*
	     * Leucocytes/champ && Hématies/champ
	     */
	    $html .= "<table style='width: 100%;'>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 20%;'><label class='lab1' style='padding-top: 5px;'><span style='font-weight: bold;'> Leucocytes </span></label></td>";
	    $html .= "  <td style='width: 18%;'>
	                    <label class='lab2' style='padding-top: 5px;'>
	                      <select id='leucocytes_champ_pv' style='width: 100px;'>
	                        <option> </option>
	                        <option value=1 >Pr&eacute;sentes</option>
	                        <option value=2 >Absentes</option>
	                      </select>
	                    </label>
	                </td>";
	    $html .= "  <td style='width: 17%;'>
	                    <label class='lab2' style='padding-top: 5px;'>
	                      <input type='text' id='leucocytes_champ_valeur_pv' style='width: 45px; padding-left: 2px; height: 20px;' >/champs
	                    </label>
	                </td>";
	    $html .= "  <td style='width: 11%;'><label class='lab1' style='font-weight: bold; text-align: right; padding-top: 5px;'> H&eacute;maties </label></td>";
	    $html .= "  <td style='width: 18%;'>
	                    <label class='lab2' style='padding-top: 5px;'>
	                      <select id='hematies_champ_pv' style='width: 100px;'>
	                        <option> </option>
	                        <option value=1 >Pr&eacute;sentes</option>
	                        <option value=2 >Absentes</option>
	                      </select>
	                    </label>
	                </td>";
	    $html .= "  <td style='width: 16%;'>
	                    <label class='lab2' style='padding-top: 5px; width: 73%;' title='champs'>
	                      <input type='text' id='hematies_champ_valeur_pv' style='width: 45px; padding-left: 2px; height: 20px;' >/c.
	                    </label>
	                </td>";
	    $html .= "</tr>";
	    $html .= "</table>";
	    /*
	     * Cellules épithéliales && Trichomonas vaginalis
	     */
	    $html .= "<table style='width: 100%;'>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 20%;'><label class='lab1' style='padding-top: 5px;'><span style='font-weight: bold;'> Cellules &eacute;pitheliales </span></label></td>";
	    $html .= "  <td style='width: 18%;'>
	                    <label class='lab2' style='padding-top: 5px;'>
	                      <select id='cellules_epitheliales_champ_pv' style='width: 100px;'>
	                        <option> </option>
	                        <option value=1 >Pr&eacute;sentes</option>
	                        <option value=2 >Absentes</option>
	                      </select>
	                    </label>
	                </td>";
	    $html .= "  <td style='width: 17%;'>
	                    <label class='lab2' style='padding-top: 5px;'>
	                      <input type='text' id='cellules_epitheliales_champ_valeur_pv' style='width: 45px; padding-left: 2px; height: 20px;' >/champs
	                    </label>
	                </td>";
	    $html .= "  <td style='width: 20%;'><label class='lab1' style='font-weight: bold; text-align: right; padding-top: 5px;'  title='Trichomonas vaginalis' > Trichomo. vaginalis </label></td>";
	    $html .= "  <td style='width: 25%;'>
	                    <label class='lab2' style='padding-top: 5px; width: 83%;'>
	                      <select id='trichomonas_vaginalis_pv' style='width: 120px;' onchange='getTrichomonasVaginalisAutoPvABG(this.value);'>
	                        <option> </option>
	                        <option value=1 >Pr&eacute;sence</option>
	                        <option value=2 >Absence</option>
	                      </select>
	                    </label>
	                </td>";
	    $html .= "</tr>";
	    $html .= "</table>";
	    
	    /*
	     * Levures && Filaments mycéliens
	     */
	    $html .= "<table style='width: 100%;'>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 25%;'><label class='lab1' style='padding-top: 5px;' ><span style='font-weight: bold;'> Levures </span></label></td>";
	    $html .= "  <td style='width: 30%;'>
	                    <label class='lab2' style='padding-top: 5px;'>
	                      <select id='levures_pv' style='width: 120px;'>
	                        <option> </option>
	                        <option value=1 >Pr&eacute;sence</option>
	                        <option value=2 >Absence</option>
	                      </select>
	                    </label>
	                </td>";
	    $html .= "  <td style='width: 20%;'><label class='lab1' style='font-weight: bold; text-align: right; padding-top: 5px;' > Filaments myc&eacute;liens </label></td>";
	    $html .= "  <td style='width: 25%;'>
	                    <label class='lab2' style='padding-top: 5px; width: 83%;'>
	                      <select id='filaments_myceliens_pv' style='width: 120px;'>
	                        <option> </option>
	                        <option value=1 >Pr&eacute;sence</option>
	                        <option value=2 >Absence</option>
	                      </select>
	                    </label>
	                </td>";
	    $html .= "</tr>";
	    
	    /*
	     * Lactobacillus && Gardnerella vaginalis
	     */
	    $html .= "<table style='width: 100%;'>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 25%;'><label class='lab1' style='padding-top: 5px;' ><span style='font-weight: bold;' > Lactobacillus </span></label></td>";
	    $html .= "  <td style='width: 30%;'>
	                    <label class='lab2' style='padding-top: 5px;'>
	                      <select id='lactobacillus_pv' style='width: 120px;'>
	                        <option> </option>
	                        <option value=1 >Pr&eacute;sence</option>
	                        <option value=2 >Absence</option>
	                      </select>
	                    </label>
	                </td>";
	    $html .= "  <td style='width: 20%;'><label class='lab1' style='font-weight: bold; text-align: right; padding-top: 5px;' > Gardnerella vaginalis </label></td>";
	    $html .= "  <td style='width: 25%;'>
	                    <label class='lab2' style='padding-top: 5px; width: 83%;'>
	                      <select id='gardnerella_vaginalis_pv' style='width: 120px;' onchange='getCommentaireAutoPvABG(this.value);'>
	                        <option> </option>
	                        <option value=1 >Pr&eacute;sence</option>
	                        <option value=2 >Absence</option>
	                      </select>
	                    </label>
	                </td>";
	    $html .= "</tr>";
	    
	    /*
	     * Mobiluncus spp && Clue cells
	     */
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 25%;'><label class='lab1' style='padding-top: 5px;'><span style='font-weight: bold;' > Mobiluncus spp </span></label></td>";
	    $html .= "  <td style='width: 30%;'>
	                    <label class='lab2' style='padding-top: 5px;'>
	                      <select id='mobiluncus_spp_pv' style='width: 120px;'>
	                        <option> </option>
	                        <option value=1 >Pr&eacute;sence</option>
	                        <option value=2 >Absence</option>
	                      </select>
	                    </label>
	                </td>";
	    $html .= "  <td style='width: 20%;'><label class='lab1' style='font-weight: bold; text-align: right; padding-top: 5px;' > Clue cells </label></td>";
	    $html .= "  <td style='width: 25%;'>
	                    <label class='lab2' style='padding-top: 5px; width: 83%;'>
	                      <select id='clue_cells_pv' style='width: 120px;'>
	                        <option> </option>
	                        <option value=1 >Pr&eacute;sence</option>
	                        <option value=2 >Absence</option>
	                      </select>
	                    </label>
	                </td>";
	    $html .= "</tr>";
	    $html .= "</table>";
	    
	    /*
	     * Flore
	     */
	    $html .= "<table style='width: 100%;'>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 25%;'><label class='lab1' style='padding-top: 5px;'><span style='font-weight: bold;' > Flore </span></label></td>";
	    $html .= "  <td style='width: 22%;'>
	                    <label class='lab2' style='padding-top: 5px;'>
	                      <select id='flore_pv' style='width: 120px;' onchange='getChampFloreNote(this.value)'>
	                        <option> </option>
	                        <option value=1 class='disabledOrEnableGroup1'>Type 1</option>
	                        <option value=2 class='disabledOrEnableGroup1'>Type 2</option>
	                        <option value=3 class='disabledOrEnableGroup2'>Type 3</option>
	                        <option value=4 class='disabledOrEnableGroup2'>Type 4</option>
	                      </select>
	                    </label>
	                </td>";
	    $html .= "  <td style='width: 53%;'>
	                    <label class='lab2' style='padding-top: 5px; width: 92%;'>
	                      <span style='font-weight: bold; font-size: 20px; text-align: left; visibility: hidden;' class='flore_note_class_pv'> &#10145;
	                        <input type='text' id='flore_note_pv' style='width: 260px; text-align: left; font-size: 16px; padding-left: 2px;'>
	                      </span>
	                    </label>
	                </td>";
	    $html .= "</tr>";
	    $html .= "</table>";
	    
	    /*
	     * Autre flore
	     */
	    $html .= "<table style='width: 100%;'>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 25%;'><label class='lab1' style='padding-top: 5px;'><span style='font-weight: bold;' > Autre flore </span></label></td>";
	    $html .= "  <td style='width: 38%;'>
	                    <label class='lab2' style='padding-top: 5px;'>
	                      <select id='autre_flore_pv' style='width: 220px; font-size: 13px;'  onchange='getAutreFloreCocciPositif(this.value)'>
	                        <option> </option>
	                        <option value=1 >Bacilles &agrave; Gram n&eacute;gatif</option>
	                        <option value=2 >Bacilles &agrave; Gram positif</option>
                	        <option value=3 >Cocci &agrave; Gram positif</option>
                	        <option value=4 >Diplocoques &agrave; Gram n&eacute;gatif</option>
	                      </select>
	                    </label>
	                </td>";
	   
	    $html .= "  <td style='width: 14%;'>
	                    <label style='padding-top: 5px;'><span class='autre_flore_cocci_positif_pv' style='visibility: hidden;'> <input type='checkbox' style='width:20px;' id='autre_flore_cocci_pos_Choix1_pv' > en amas </span></label>
	                </td>";
	    $html .= "  <td style='width: 23%;'>
	                    <label style='padding-top: 5px; width: 80%;'><span class='autre_flore_cocci_positif_pv' style='visibility: hidden;'> <input type='checkbox' style='width:20px;' id='autre_flore_cocci_pos_Choix2_pv' > en chainettes </span></label>
	                </td>";
	    
	    $html .= "</tr>";
	    $html .= "</table>";
	    
	    

	    
	    /**
	     * Partie Culture *** Partie Culture
	     * ---------------------------------
	     * Partie Culture *** Partie Culture
	     */
	    $html .= "<table style='width: 100%;'>";
	    /**
	     * Culture --- Culture --- Culture
	     */
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td colspan='3' style='width: 100%; text-align: left; font-weight: bold; font-size: 12px; font-style: italic;'> &#10148; Culture </td>";
	    $html .= "</tr>";
	    $html .= "</table>";
	    /*
	     * Culture && Identification
	     */
	    $html .= "<table style='width: 100%;'>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 25%;'><label class='lab1' style='padding-top: 5px;'><span style='font-weight: bold;'> Culture </span></label></td>";
	    $html .= "  <td style='width: 27%;'>
	                    <label class='lab2' style='padding-top: 5px;'>
	                      <select id='culture_pv' style='width: 120px;' onchange='getNombreCultureIdentifier(this.value)'>
	                        <option> </option>
	                        <option value=1 >Positive</option>
	                        <option value=2 >N&eacute;gative</option>
	                        
	                      </select>
	                    </label>
	                </td>";
	    $html .= "  <td style='width: 12%;'><label class='lab1' style='font-weight: bold; text-align: right; padding-top: 5px;' > <span class='nombreCultureIdentifierABG' style='visibility:hidden;'>  Nombre </span></label></td>";
	    $html .= "  <td style='width: 36%;'><label class='lab2' style='padding-top: 5px; width: 88%;'><input class='nombreCultureIdentifierABG' id='nombreCultureIdentifierABG' type='number' min=1 max=3 onchange='getChampsCultureIdentifierABG(this.value)' style='width: 35px; font-size: 19px; padding-left: 1px; visibility:hidden;' ></label></td>";
	    $html .= "</tr>";
	    $html .= "</table>";
	    
	    /*
	     * Les champs 'Identification'
	     */
	    $html .= "<table style='width: 100%; display: none;' class='identificationCultureChamps identificationCultureChampsABR_1'  >";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 25%;'><label class='lab1' style='padding-top: 5px;'><span style='font-weight: bold;'> Identification </span></label></td>";
	    $html .= "  <td style='width: 27%;'>
	                    <label class='lab2' style='padding-top: 5px;'>
	                      <select id='identification_culture_pv' style='width: 190px;' onchange='getIconeAntibiogrammeIdentCulture(this.value,1)'>
	                        <option value=0 > </option>
	                        <option value=1 >Candida albicans</option>
	                        <option value=2 >Escherichia coli</option>
	                        <option value=3 >Staphylococcus aureus</option>
	                      </select>
	                    </label>
	                </td>";
	    $html .= "  <td style='width: 48%;'><label onclick='antibiogrammeAfficherInterface()' class='lab1 antibiogrammeButtonAffInterface1' style='padding-top: 0px; margin-top: 3px; margin-left: 10px; width: 30%; height: 15px; font-style: italic; border-radius: 35%; border: 3px solid #d8d8d8; padding-left: 10px; display: none;'> Antibiogramme </label></td>";
	    $html .= "</tr>";
	    $html .= "</table>";
	    
	    /**
	     * Les autres à placer ici 
	     */
	    
	    /*
	     * Préparer l'interface de saisie des données de l'antibiogramme
	     */
	    $interfaceSaisieDonneesABG = $this->getAntiBioGrammeDuPV();
	    $html .= "<script> $('#contenuResultatsAnalysesPVAntiBioGramme div').html('".$interfaceSaisieDonneesABG."'); </script>";
	    /*
	     * Ajouter les scripts pour la gestion des checkbox non cochés
	     * Pour la première colonne
	     */
	    $html .= '<script>  $("#choixAmpicillineAMABG").click(function(){ if($("#choixAmpicillineAMABG").get(0).checked){ $("#ampicillineAMABG").attr("disabled", false); }else{ $("#ampicillineAMABG").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixAmoxillineAMXABG").click(function(){ if($("#choixAmoxillineAMXABG").get(0).checked){ $("#amoxillineAMXABG").attr("disabled", false); }else{ $("#amoxillineAMXABG").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixTicarcillineTICABG").click(function(){ if($("#choixTicarcillineTICABG").get(0).checked){ $("#ticarcillineTICABG").attr("disabled", false); }else{ $("#ticarcillineTICABG").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixPiperacillinePIPABG").click(function(){ if($("#choixPiperacillinePIPABG").get(0).checked){ $("#piperacillinePIPABG").attr("disabled", false); }else{ $("#piperacillinePIPABG").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixAcideClavulaniqueAmoxicillineAMCABG").click(function(){ if($("#choixAcideClavulaniqueAmoxicillineAMCABG").get(0).checked){ $("#acideClavulaniqueAmoxicillineAMCABG").attr("disabled", false); }else{ $("#acideClavulaniqueAmoxicillineAMCABG").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixGentamicineGMABG").click(function(){ if($("#choixGentamicineGMABG").get(0).checked){ $("#gentamicineGMABG").attr("disabled", false); }else{ $("#gentamicineGMABG").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixTicAcClavTCCABG").click(function(){ if($("#choixTicAcClavTCCABG").get(0).checked){ $("#ticAcClavTCCABG").attr("disabled", false); }else{ $("#ticAcClavTCCABG").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixErtapenemeETPABG").click(function(){ if($("#choixErtapenemeETPABG").get(0).checked){ $("#ertapenemeETPABG").attr("disabled", false); }else{ $("#ertapenemeETPABG").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixImipenemeIPMABG").click(function(){ if($("#choixImipenemeIPMABG").get(0).checked){ $("#imipenemeIPMABG").attr("disabled", false); }else{ $("#imipenemeIPMABG").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixOxacillineOXABG").click(function(){ if($("#choixOxacillineOXABG").get(0).checked){ $("#oxacillineOXABG").attr("disabled", false); }else{ $("#oxacillineOXABG").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixPenicillinePABG").click(function(){ if($("#choixPenicillinePABG").get(0).checked){ $("#penicillinePABG").attr("disabled", false); }else{ $("#penicillinePABG").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixCefalotineCFABG").click(function(){ if($("#choixCefalotineCFABG").get(0).checked){ $("#cefalotineCFABG").attr("disabled", false); }else{ $("#cefalotineCFABG").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixCefoxitineFOXABG").click(function(){ if($("#choixCefoxitineFOXABG").get(0).checked){ $("#cefoxitineFOXABG").attr("disabled", false); }else{ $("#cefoxitineFOXABG").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixPiperacillineTazobactamePPTABG").click(function(){ if($("#choixPiperacillineTazobactamePPTABG").get(0).checked){ $("#piperacillineTazobactamePPTABG").attr("disabled", false); }else{ $("#piperacillineTazobactamePPTABG").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixCefotaximeCTXABG").click(function(){ if($("#choixCefotaximeCTXABG").get(0).checked){ $("#cefotaximeCTXABG").attr("disabled", false); }else{ $("#cefotaximeCTXABG").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixCefsulodineCFSABG").click(function(){ if($(this).get(0).checked){ $("#cefsulodineCFSABG").attr("disabled", false); }else{ $("#cefsulodineCFSABG").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixCFPABG").click(function(){ if($(this).get(0).checked){ $("#CFPABG").attr("disabled", false); }else{ $("#CFPABG").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixCeftazidimeCAZABG").click(function(){ if($(this).get(0).checked){ $("#ceftazidimeCAZABG").attr("disabled", false); }else{ $("#ceftazidimeCAZABG").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixCeftriaxoneCROABG").click(function(){ if($(this).get(0).checked){ $("#ceftriaxoneCROABG").attr("disabled", false); }else{ $("#ceftriaxoneCROABG").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixCefepimeFEPABG").click(function(){ if($(this).get(0).checked){ $("#cefepimeFEPABG").attr("disabled", false); }else{ $("#cefepimeFEPABG").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixAztreonamATMABG").click(function(){ if($(this).get(0).checked){ $("#aztreonamATMABG").attr("disabled", false); }else{ $("#aztreonamATMABG").attr("disabled", true).val(""); } }) </script>';
	    /* PARTIE Glycopeptides */
	    $html .= '<script>  $("#choixVancomycineVAABG").click(function(){ if($(this).get(0).checked){ $("#vancomycineVAABG").attr("disabled", false); }else{ $("#vancomycineVAABG").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixTeicoplanineABG").click(function(){ if($(this).get(0).checked){ $("#teicoplanineABG").attr("disabled", false); }else{ $("#teicoplanineABG").attr("disabled", true).val(""); } }) </script>';
	    /* PARTIE Produits nitrés */
	    $html .= '<script>  $("#choixNitrofuraneABG").click(function(){ if($(this).get(0).checked){ $("#nitrofuraneABG").attr("disabled", false); }else{ $("#nitrofuraneABG").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixMetronidazoleABG").click(function(){ if($(this).get(0).checked){ $("#metronidazoleABG").attr("disabled", false); }else{ $("#metronidazoleABG").attr("disabled", true).val(""); } }) </script>';
	    /* PARTIE Sulfamides */
	    $html .= '<script>  $("#choixTrimethoprimeSulfametoxazoleSXTABG").click(function(){ if($(this).get(0).checked){ $("#trimethoprimeSulfametoxazoleSXTABG").attr("disabled", false); }else{ $("#trimethoprimeSulfametoxazoleSXTABG").attr("disabled", true).val(""); } }) </script>';
	     
	    
	    /*
	     * Ajouter les scripts pour la gestion des checkbox non cochés
	     * Pour la première colonne
	     */
	    
	    /* PARTIE Polymyxine */
	    $html .= '<script>  $("#choixColistineCSABG").click(function(){ if($(this).get(0).checked){ $("#colistineCSABG").attr("disabled", false); }else{ $("#colistineCSABG").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixPolymicineABG").click(function(){ if($(this).get(0).checked){ $("#polymicineABG").attr("disabled", false); }else{ $("#polymicineABG").attr("disabled", true).val(""); } }) </script>';
	    /* PARTIE Aminosides */
	    $html .= '<script>  $("#choixKanamycineKABG").click(function(){ if($(this).get(0).checked){ $("#kanamycineKABG").attr("disabled", false); }else{ $("#kanamycineKABG").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixTobramycineTBABG").click(function(){ if($(this).get(0).checked){ $("#tobramycineTBABG").attr("disabled", false); }else{ $("#tobramycineTBABG").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixAmikacineANABG").click(function(){ if($(this).get(0).checked){ $("#amikacineANABG").attr("disabled", false); }else{ $("#amikacineANABG").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixNetilmycineABG").click(function(){ if($(this).get(0).checked){ $("#netilmycineABG").attr("disabled", false); }else{ $("#netilmycineABG").attr("disabled", true).val(""); } }) </script>';
	    /* PARTIE Phénicolés */
	    $html .= '<script>  $("#choixChloramphenicolCABG").click(function(){ if($(this).get(0).checked){ $("#chloramphenicolCABG").attr("disabled", false); }else{ $("#chloramphenicolCABG").attr("disabled", true).val(""); } }) </script>';
	    /* PARTIE Cyclines */
	    $html .= '<script>  $("#choixMinocyclineABG").click(function(){ if($(this).get(0).checked){ $("#minocyclineABG").attr("disabled", false); }else{ $("#minocyclineABG").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixTetracyclineTEABG").click(function(){ if($(this).get(0).checked){ $("#tetracyclineTEABG").attr("disabled", false); }else{ $("#tetracyclineTEABG").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixDoxycyclineDOABG").click(function(){ if($(this).get(0).checked){ $("#doxycyclineDOABG").attr("disabled", false); }else{ $("#doxycyclineDOABG").attr("disabled", true).val(""); } }) </script>';
	    /* PARTIE Macrolides et apparentés */
	    $html .= '<script>  $("#choixAzithromycineAZTABG").click(function(){ if($(this).get(0).checked){ $("#azithromycineAZTABG").attr("disabled", false); }else{ $("#azithromycineAZTABG").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixErythromycineEABG").click(function(){ if($(this).get(0).checked){ $("#erythromycineEABG").attr("disabled", false); }else{ $("#erythromycineEABG").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixLincomycineLABG").click(function(){ if($(this).get(0).checked){ $("#lincomycineLABG").attr("disabled", false); }else{ $("#lincomycineLABG").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixPristinamycinePTABG").click(function(){ if($(this).get(0).checked){ $("#pristinamycinePTABG").attr("disabled", false); }else{ $("#pristinamycinePTABG").attr("disabled", true).val(""); } }) </script>';
	    /* PARTIE Fluoroquinolones*/
	    $html .= '<script>  $("#choixAcideNalidixiqueNAABG").click(function(){ if($(this).get(0).checked){ $("#acideNalidixiqueNAABG").attr("disabled", false); }else{ $("#acideNalidixiqueNAABG").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixPefloxacinePEFABG").click(function(){ if($(this).get(0).checked){ $("#pefloxacinePEFABG").attr("disabled", false); }else{ $("#pefloxacinePEFABG").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixNorfloxacineNORABG").click(function(){ if($(this).get(0).checked){ $("#norfloxacineNORABG").attr("disabled", false); }else{ $("#norfloxacineNORABG").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixCiprofloxacineCIPABG").click(function(){ if($(this).get(0).checked){ $("#ciprofloxacineCIPABG").attr("disabled", false); }else{ $("#ciprofloxacineCIPABG").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixLEVABG").click(function(){ if($(this).get(0).checked){ $("#LEVABG").attr("disabled", false); }else{ $("#LEVABG").attr("disabled", true).val(""); } }) </script>';
	    /* PARTIE Imidazolés*/
	    $html .= '<script>  $("#choixRifampicineRAABG").click(function(){ if($(this).get(0).checked){ $("#rifampicineRAABG").attr("disabled", false); }else{ $("#rifampicineRAABG").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixCotrimoxazoleSXTABG").click(function(){ if($(this).get(0).checked){ $("#cotrimoxazoleSXTABG").attr("disabled", false); }else{ $("#cotrimoxazoleSXTABG").attr("disabled", true).val(""); } }) </script>';
	     
	    /* PARTIE Autres*/
	    $html .= '<script>  $("#choixFosfomycineFOSABG").click(function(){ if($(this).get(0).checked){ $("#fosfomycineFOSABG").attr("disabled", false); }else{ $("#fosfomycineFOSABG").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixAcideFusidiqueFAABG").click(function(){ if($(this).get(0).checked){ $("#acideFusidiqueFAABG").attr("disabled", false); }else{ $("#acideFusidiqueFAABG").attr("disabled", true).val(""); } }) </script>';
	     
	    /**
	     * Partie recherches particulieres *** Partie recherches particulieres
	     * -------------------------------------------------------------------
	     * Partie recherches particulieres *** Partie recherches particulieres
	     */
	    if($idanalyse == 74){
	    	
	    	$html .= "<table style='width: 100%;'>";
	    	/**
	    	 * Recherches particulieres --- Recherches particulieres
	    	 */
	    	$html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    	$html .= "  <td style='width: 100%; text-align: left; font-weight: bold; font-size: 12px; font-style: italic;'> &#10148; Recherches particuli&egrave;res </td>";
	    	$html .= "</tr>";
	    	$html .= "</table>";
	    	 
	    	/*
	    	 * Recherche directe de l'antigène de chlamydia
	    	*/
	    	$html .= "<table style='width: 100%;'>";
	    	$html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    	$html .= '  <td style="width: 25%;"><label class="lab1" style="padding-top: 5px;"><span style="font-weight: bold;" title="Recherche d\'Ag de Chlamydia trachomatis"> Rech. d\'Ag Chlam. tracho. </span></label></td>';
	    	$html .= "  <td style='width: 25%;'>
	                    <label class='lab2' style='padding-top: 5px;'>
	                      <select id='recherche_directe_antigene_chlamydia_pv' style='width: 120px;'>
	                        <option> </option>
	                        <option value=1 >Positive</option>
	                        <option value=2 >N&eacute;gative</option>
	   
	                      </select>
	                    </label>
	                </td>";
	    	$html .= "  <td style='width: 25%;'><label class='lab1' style='padding-top: 5px;'><span style='font-weight: bold;' >  </span></label></td>";
	    	$html .= "  <td style='width: 25%;'>
	                    <label class='lab2' style='padding-top: 5px; width: 83%;'> </label>
	                </td>";
	    	$html .= "</tr>";
	    	$html .= "</table>";
	    	 
	    	 
	    	/*
	    	 * Recherche des Mycoplasmes && Identification
	    	*/
	    	/* Recherche des Mycoplasmes */
	    	$html .= "<table style='width: 100%;'>";
	    	$html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    	$html .= "  <td style='width: 25%;'><label class='lab1' style='padding-top: 5px;'><span style='font-weight: bold;' title='Recherche des Mycoplasmes'> Rech. des Mycoplasmes </span></label></td>";
	    	$html .= "  <td style='width: 25%;'>
	                    <label class='lab2' style='padding-top: 5px;'>
	                      <select id='recherche_directe_mycoplasmes_pv' style='width: 120px;' onchange='getChampIdentificationRdmPositive(this.value)'>
	                        <option> </option>
	                        <option value=1 >Positive</option>
	                        <option value=2 >N&eacute;gative</option>
	                      </select>
	                    </label>
	                </td>";
	    	$html .= "  <td style='width: 50%;'>
	                    <label style='padding-top: 5px; width: 92%; '>
	                      <span style='  display: none; float: left;'  class='identification_rdm_positive_class_titre_pv'>  Titre :
    	                    <span style='font-size: 15px; font-weight: bold; padding-left: 10px;' >  &#8805; 10<sup>4</sup> ucc/ml</span>
	                      </span>
	                    </label>
	                </td>";
	    	$html .= "</tr>";
	    	$html .= "</table>";
	    	/* Identification  -- Identification */
	    	$html .= "<table style='width: 100%; display: none;' class='identification_rdm_positive_class_pv'>";
	    	$html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    	$html .= "  <td style='width: 25%;'>
	                    <label class='lab2' style='padding-top: 5px;'>
	                      <span style='font-weight: bold; float: left; padding-left: 80px;' > Identification </span>
	                    </label>
	                </td>";
	    	$html .= "  <td style='width: 30%;'>
	                    <label style='padding-top: 5px;'> <input type='checkbox' style='width:20px;' id='identification_rdm_positive_Choix1_pv' > Ureaplasma urealyticum </label>
	                </td>";
	    	$html .= "  <td style='width: 45%;'>
	                    <label style='padding-top: 5px; width: 91%;'> <input type='checkbox' style='width:20px;' id='identification_rdm_positive_Choix2_pv' > Mycoplasma hominis </label>
	                </td>";
	    	$html .= "</tr>";
	    	$html .= "</table>";
	    	
	    }
	    
	    
	    
	    
	    /**
	     * Partie commentaire --- Partie commentaire
	     * -----------------------------------------
	     * Partie commentaire --- Partie commentaire
	     */
	    
	    $html .= "<table style='width: 100%; margin-top: 15px;'>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    if($this->layout()->user['role'] == 'biologiste' || $this->layout()->user['role'] == 'technicien'){
	        $html .= "<td style='width: 96%;'><label style='height: 140px;' ><span style='font-size: 16px; float: left;  margin-left: 30px;'> Commentaire:  </span> <textarea id='commentaire_pv' style='max-height: 100px; min-height: 100px; max-width: 560px; min-width: 560px; margin-left: 30px;' readonly> </textarea> <div style='float: right; font-family: Agency FB; font-size: 15px; margin-right: 10px; color: green; font-weight: bold;' onclick='commentaireChoiceCheckPV();' id='idCommentaireChoiceCheckPV'>Com.</div> </label></td>";
	    }else{
	        $html .= "<td style='width: 96%;'><label style='height: 140px;' ><span style='font-size: 16px; float: left;  margin-left: 30px;'> Commentaire:  </span> <textarea id='commentaire_pv' style='max-height: 100px; min-height: 100px; max-width: 560px; min-width: 560px; margin-left: 30px;' disabled> </textarea> </label></td>";
	    }
	    $html .= "<td style='width: 5%;'></td>";
	    $html .= "</tr>";
	    $html .= "</table>";
	    
	    
	    /**
	     * PRECISION A APPORTER SUR LE COMMENTAIRE
	     */
	    $html .= "<table style='width: 100%; margin-top: 2px;'>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    if($this->layout()->user['role'] == 'biologiste' || $this->layout()->user['role'] == 'technicien'){
	    	$html .= "<td style='width: 96%;'><label style='height: 140px;' ><span style='font-size: 16px; float: left;  margin-left: 30px;'> Autres Pr&eacute;cisions :  </span> <textarea id='autres_precisions_commentaire_pv' style='max-height: 100px; min-height: 100px; max-width: 560px; min-width: 560px; margin-left: 30px; text-align: justify; padding: 5px;' > </textarea> </label></td>";
	    }else{
	    	$html .= "<td style='width: 96%;'><label style='height: 140px;' ><span style='font-size: 16px; float: left;  margin-left: 30px;'> Autres Pr&eacute;cisions :  </span> <textarea id='autres_precisions_commentaire_pv' style='max-height: 100px; min-height: 100px; max-width: 560px; min-width: 560px; margin-left: 30px; text-align: justify; padding: 5px;' disabled> </textarea> </label></td>";
	    }
	    $html .= "<td style='width: 5%;'></td>";
	    $html .= "</tr>";
	    $html .= "</table>";
	    
	    
	    $html .= " </td> </tr>";
	
	    return $html;
	}
	
	/**
	 * interface pour l'antibiogramme de l'analyse 65
	 */
	public function getAntiBioGrammeDuPV()
	{
	    /*
	     * Entete
	     */
	    $html  = '<table style="width: 100%;"><tr style="width: 100%;"><td  style="width: 100%;" align=center> <div style="border-radius: 25%; border: 3px solid #d8d8d8; background-color: #f1f1f1; width: 40%; font-weight: bold; "> Antibiogramme </div> </td></tr></table>';
	    $html .= '<table style="width: 100%; margin-top: 15px;"><tr style="width: 100%;"><td  style="width: 100%; font-family: time new" align=left> <div style="background-color: #f1f1f1; width: 50%; font-weight: bold; float: left; font-size: 15px; ">&#9883; Souche isol&eacute;e : <span id="valeurSoucheIsoleeIdentificationCulturePV" style="font-size: 17px; font-weight: normal;"> Nom de la souche</span> </div> </td></tr></table>';
	    /*
	     * =======
	     */
	    
	    $html .= '<table style="width: 100%;">';
	    $html .= '<tr style="width: 100%;">';
	    /* 
	     * Premiere colonne
	     */
	    $html .= '  <td style="width: 49%; height: 20px; background: re; vertical-align: top;">'; 
	    /*
	     * ================
	     */
	    
	    /**
	     * B-lactamines --- B-lactamines
	     */
	    $html .= '<table style="width: 100%; margin-top: 15px;">';
	    $html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
	    $html .= '  <td style="width: 100%; text-align: left; font-weight: bold; font-size: 12px; font-style: italic;"> &#10148; &#42933;-lactamines </td>';
	    $html .= '</tr>';
	    $html .= '</table>';
	    /*
	     * Ampicilline AM
	     */
	    $html .= '<table style="width: 100%;" class="designEnTeteAnalyse">';
	    $html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
	    $html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > Ampicilline AM </span></label></td>';
	    $html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixAmpicillineAMABG" > </label></td>';
	    $html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
	    $html .= '      <select id="ampicillineAMABG" style="width: 120px;" disabled> ';
	    $html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
	    $html .= '      </select></label> </td>';
	    $html .= '</tr></table>';

	    /*
	     * Amoxilline AMX
	     */
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
	    $html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
	    $html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > Amoxilline AMX </span></label></td>';
	    $html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixAmoxillineAMXABG" > </label></td>';
	    $html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
	    $html .= '      <select id="amoxillineAMXABG" style="width: 120px;" disabled> ';
	    $html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
	    $html .= '      </select></label> </td>';
	    $html .= "</tr></table>";
	    
	    /*
	     * Ticarcilline TIC
	     */
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
	    $html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
	    $html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > Ticarcilline TIC </span></label></td>';
	    $html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixTicarcillineTICABG" > </label></td>';
	    $html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
	    $html .= '      <select id="ticarcillineTICABG" style="width: 120px;" disabled> ';
	    $html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
	    $html .= '      </select></label> </td>';
	    $html .= "</tr></table>";
	    
	    /*
	     * Piperacilline PIP
	     */
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
	    $html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
	    $html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > Piperacilline PIP </span></label></td>';
	    $html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixPiperacillinePIPABG" > </label></td>';
	    $html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
	    $html .= '      <select id="piperacillinePIPABG" style="width: 120px;" disabled> ';
	    $html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
	    $html .= '      </select></label> </td>';
	    $html .= "</tr></table>";
	    
	    /*
	     * Acide clavulanique + Amoxicilline AMC
	     */
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
	    $html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
	    $html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" title="Acide clavulanique + Amoxicilline AMC"> Acide clav. + Amoxi. AMC </span></label></td>';
	    $html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixAcideClavulaniqueAmoxicillineAMCABG" > </label></td>';
	    $html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
	    $html .= '      <select id="acideClavulaniqueAmoxicillineAMCABG" style="width: 120px;" disabled> ';
	    $html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
	    $html .= '      </select></label> </td>';
	    $html .= "</tr></table>";
	    
	    /*
	     * Tic-Ac-Clav TCC
	     */
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
	    $html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
	    $html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > Tic-Ac-Clav TCC </span></label></td>';
	    $html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixTicAcClavTCCABG" > </label></td>';
	    $html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
	    $html .= '      <select id="ticAcClavTCCABG" style="width: 120px;" disabled> ';
	    $html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
	    $html .= '      </select></label> </td>';
	    $html .= "</tr></table>";
	    
	    /*
	     * Ertapénème ETP
	     */
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
	    $html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
	    $html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > Ertap&eacute;n&egrave;me ETP </span></label></td>';
	    $html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixErtapenemeETPABG" > </label></td>';
	    $html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
	    $html .= '      <select id="ertapenemeETPABG" style="width: 120px;" disabled> ';
	    $html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
	    $html .= '      </select></label> </td>';
	    $html .= "</tr></table>";
	    
	    /*
	     * Imipénème IPM
	     */
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
	    $html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
	    $html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > Imip&eacute;n&egrave;me IPM </span></label></td>';
	    $html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixImipenemeIPMABG" > </label></td>';
	    $html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
	    $html .= '      <select id="imipenemeIPMABG" style="width: 120px;" disabled> ';
	    $html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
	    $html .= '      </select></label> </td>';
	    $html .= "</tr></table>";
	    
	    /*
	     * Oxacilline OX
	     */
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
	    $html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
	    $html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > Oxacilline OX </span></label></td>';
	    $html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixOxacillineOXABG" > </label></td>';
	    $html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
	    $html .= '      <select id="oxacillineOXABG" style="width: 120px;" disabled> ';
	    $html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
	    $html .= '      </select></label> </td>';
	    $html .= "</tr></table>";
	    
	    /*
	     * Penicilline P
	     */
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
	    $html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
	    $html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > Penicilline P </span></label></td>';
	    $html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixPenicillinePABG" > </label></td>';
	    $html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
	    $html .= '      <select id="penicillinePABG" style="width: 120px;" disabled> ';
	    $html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
	    $html .= '      </select></label> </td>';
	    $html .= "</tr></table>";
	    
	    /*
	     * Céfalotine CF
	     */
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
	    $html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
	    $html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > C&eacute;falotine CF </span></label></td>';
	    $html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixCefalotineCFABG" > </label></td>';
	    $html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
	    $html .= '      <select id="cefalotineCFABG" style="width: 120px;" disabled> ';
	    $html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
	    $html .= '      </select></label> </td>';
	    $html .= "</tr></table>";
	    
	    /*
	     * Céfoxitine FOX
	     */
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
	    $html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
	    $html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > C&eacute;foxitine FOX </span></label></td>';
	    $html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixCefoxitineFOXABG" > </label></td>';
	    $html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
	    $html .= '      <select id="cefoxitineFOXABG" style="width: 120px;" disabled> ';
	    $html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
	    $html .= '      </select></label> </td>';
	    $html .= "</tr></table>";
	    
	    /*
	     * Pipéracilline + Tazobactame PPT
	     */
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
	    $html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
	    $html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" title="Pip&eacute;racilline + Tazobactame PPT"> Pip&eacute;racil. + Tazobact. PPT </span></label></td>';
	    $html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixPiperacillineTazobactamePPTABG" > </label></td>';
	    $html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
	    $html .= '      <select id="piperacillineTazobactamePPTABG" style="width: 120px;" disabled> ';
	    $html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
	    $html .= '      </select></label> </td>';
	    $html .= "</tr></table>";

	    /*
	     * Céfotaxime CTX
	     */
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
	    $html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
	    $html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > C&eacute;fotaxime CTX </span></label></td>';
	    $html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixCefotaximeCTXABG" > </label></td>';
	    $html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
	    $html .= '      <select id="cefotaximeCTXABG" style="width: 120px;" disabled> ';
	    $html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
	    $html .= '      </select></label> </td>';
	    $html .= "</tr></table>";
	    
	    /*
	     * Cefsulodine CFS
	     */
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
	    $html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
	    $html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > Cefsulodine CFS </span></label></td>';
	    $html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixCefsulodineCFSABG" > </label></td>';
	    $html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
	    $html .= '      <select id="cefsulodineCFSABG" style="width: 120px;" disabled> ';
	    $html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
	    $html .= '      </select></label> </td>';
	    $html .= "</tr></table>";
	    
	    /*
	     * Cefopérazone CFP
	     */
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
	    $html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
	    $html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > C&eacute;fop&eacute;razone CFP </span></label></td>';
	    $html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixCFPABG" > </label></td>';
	    $html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
	    $html .= '      <select id="CFPABG" style="width: 120px;" disabled> ';
	    $html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
	    $html .= '      </select></label> </td>';
	    $html .= "</tr></table>";
	     
	    /*
	     * Ceftazidime CAZ
	     */
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
	    $html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
	    $html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > Ceftazidime CAZ </span></label></td>';
	    $html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixCeftazidimeCAZABG" > </label></td>';
	    $html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
	    $html .= '      <select id="ceftazidimeCAZABG" style="width: 120px;" disabled> ';
	    $html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
	    $html .= '      </select></label> </td>';
	    $html .= "</tr></table>";
	    
	    /*
	     * Ceftriaxone CRO
	     */
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
	    $html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
	    $html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > Ceftriaxone CRO </span></label></td>';
	    $html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixCeftriaxoneCROABG" > </label></td>';
	    $html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
	    $html .= '      <select id="ceftriaxoneCROABG" style="width: 120px;" disabled> ';
	    $html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
	    $html .= '      </select></label> </td>';
	    $html .= "</tr></table>";
	    
	    /*
	     * Céfépime FEP
	     */
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
	    $html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
	    $html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > C&eacute;f&eacute;pime FEP </span></label></td>';
	    $html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixCefepimeFEPABG" > </label></td>';
	    $html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
	    $html .= '      <select id="cefepimeFEPABG" style="width: 120px;" disabled> ';
	    $html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
	    $html .= '      </select></label> </td>';
	    $html .= "</tr></table>";
	    
	    /*
	     * Aztréonam ATM
	     */
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
	    $html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
	    $html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > Aztr&eacute;onam ATM </span></label></td>';
	    $html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixAztreonamATMABG" > </label></td>';
	    $html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
	    $html .= '      <select id="aztreonamATMABG" style="width: 120px;" disabled> ';
	    $html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
	    $html .= '      </select></label> </td>';
	    $html .= "</tr></table>";
	    
	    /**
	     * Imidazolés --- Imidazolés
	     */
	    $html .= '<table style="width: 100%; margin-top: 14px;">';
	    $html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
	    $html .= '  <td style="width: 100%; text-align: left; font-weight: bold; font-size: 12px; font-style: italic;"> &#10148; Imidazol&eacute;s </td>';
	    $html .= '</tr>';
	    $html .= '</table>';
	    /*
	     * Cotrimoxazole
	     */
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
	    $html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
	    $html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > Cotrimoxazole </span></label></td>';
	    $html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixCotrimoxazoleSXTABG" > </label></td>';
	    $html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
	    $html .= '      <select id="cotrimoxazoleSXTABG" style="width: 120px;" disabled> ';
	    $html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
	    $html .= '      </select></label> </td>';
	    $html .= "</tr></table>";
	    
	    /**
	     * Glycopeptides --- Glycopeptides
	     */
	    $html .= '<table style="width: 100%; margin-top: 14px;">';
	    $html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
	    $html .= '  <td style="width: 100%; text-align: left; font-weight: bold; font-size: 12px; font-style: italic;"> &#10148; Glycopeptides </td>';
	    $html .= '</tr>';
	    $html .= '</table>';
	    /*
	     * Vancomycine VA
	     */
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
	    $html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
	    $html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > Vancomycine VA </span></label></td>';
	    $html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixVancomycineVAABG" > </label></td>';
	    $html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
	    $html .= '      <select id="vancomycineVAABG" style="width: 120px;" disabled> ';
	    $html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
	    $html .= '      </select></label> </td>';
	    $html .= "</tr></table>";
	    /*
	     * Teicoplanine
	     */
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
	    $html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
	    $html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > Teicoplanine </span></label></td>';
	    $html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixTeicoplanineABG" > </label></td>';
	    $html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
	    $html .= '      <select id="teicoplanineABG" style="width: 120px;" disabled> ';
	    $html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
	    $html .= '      </select></label> </td>';
	    $html .= "</tr></table>";
	    
	    /**
	     * Produits nitrés --- Produits nitrés
	     */
	    $html .= '<table style="width: 100%; margin-top: 14px;">';
	    $html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
	    $html .= '  <td style="width: 100%; text-align: left; font-weight: bold; font-size: 12px; font-style: italic;"> &#10148; Produits nitr&eacute;s </td>';
	    $html .= '</tr>';
	    $html .= '</table>';
	    /*
	     * Nitrofurane
	     */
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
	    $html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
	    $html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > Nitrofurane </span></label></td>';
	    $html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixNitrofuraneABG" > </label></td>';
	    $html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
	    $html .= '      <select id="nitrofuraneABG" style="width: 120px;" disabled> ';
	    $html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
	    $html .= '      </select></label> </td>';
	    $html .= "</tr></table>";
	    /*
	     * Métronidazole
	     */
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
	    $html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
	    $html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > M&eacute;tronidazole </span></label></td>';
	    $html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixMetronidazoleABG" > </label></td>';
	    $html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
	    $html .= '      <select id="metronidazoleABG" style="width: 120px;" disabled> ';
	    $html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
	    $html .= '      </select></label> </td>';
	    $html .= "</tr></table>";
	    
	    
	    /**
	     * Sulfamides --- Sulfamides
	     */
	    $html .= '<table style="width: 100%; margin-top: 14px;">';
	    $html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
	    $html .= '  <td style="width: 100%; text-align: left; font-weight: bold; font-size: 12px; font-style: italic;"> &#10148; Sulfamides </td>';
	    $html .= '</tr>';
	    $html .= '</table>';
	    /*
	     * Triméthoprime + Sulfamétoxazole (SXT)
	     */
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
	    $html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
	    $html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" title="Trim&eacute;thoprime + Sulfam&eacute;toxazole (SXT)"> Trim&eacute;tho. + Sulfa. (SXT) </span></label></td>';
	    $html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixTrimethoprimeSulfametoxazoleSXTABG" > </label></td>';
	    $html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
	    $html .= '      <select id="trimethoprimeSulfametoxazoleSXTABG" style="width: 120px;" disabled> ';
	    $html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
	    $html .= '      </select></label> </td>';
	    $html .= "</tr></table>";
	    
	    
	    
        /*
         * Fermeture Première colonne
         */
	    $html .= '  </td>';
	    /*
	     * ==========================
	     */
	    
	    
	    /* Séparateur des colonnes --- Séparateur des colonnes */
	    $html .= '  <td style="width:  1.5%; height: 20px; background: indig;"> </td>';
	    /*=====================================================*/
	    
	    
	    /*
	     * Deuxième colonne
	     */
	    $html .= '  <td style="width: 49.5%; height: 20px; background: yello; vertical-align: top;">';
	    /*
	     * ====================
	     */
	    
	    /**
	     * Polymyxines --- Polymyxines
	     */
	    $html .= '<table style="width: 100%; margin-top: 15px;">';
	    $html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
	    $html .= '  <td style="width: 100%; text-align: left; font-weight: bold; font-size: 12px; font-style: italic;"> &#10148; Polymyxines </td>';
	    $html .= '</tr>';
	    $html .= '</table>';
	    
	    /*
	     * Colistine CS
	     */
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
	    $html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
	    $html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > Colistine CS </span></label></td>';
	    $html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixColistineCSABG" > </label></td>';
	    $html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
	    $html .= '      <select id="colistineCSABG" style="width: 120px;" disabled> ';
	    $html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
	    $html .= '      </select></label> </td>';
	    $html .= "</tr></table>";
	    
	    /*
	     * Polymicine
	     */
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
	    $html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
	    $html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > Polymicine </span></label></td>';
	    $html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixPolymicineABG" > </label></td>';
	    $html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
	    $html .= '      <select id="polymicineABG" style="width: 120px;" disabled> ';
	    $html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
	    $html .= '      </select></label> </td>';
	    $html .= "</tr></table>";
	    
	    
	    /**
	     * Aminosides --- Aminosides
	     */
	    $html .= '<table style="width: 100%; margin-top: 17px;">';
	    $html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
	    $html .= '  <td style="width: 100%; text-align: left; font-weight: bold; font-size: 12px; font-style: italic;"> &#10148; Aminosides </td>';
	    $html .= '</tr>';
	    $html .= '</table>';
	    /*
	     * Kanamycine K
	     */
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
	    $html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
	    $html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > Kanamycine K </span></label></td>';
	    $html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixKanamycineKABG" > </label></td>';
	    $html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
	    $html .= '      <select id="kanamycineKABG" style="width: 120px;" disabled> ';
	    $html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
	    $html .= '      </select></label> </td>';
	    $html .= "</tr></table>";
	    
	    /*
	     * Tobramycine TB
	     */
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
	    $html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
	    $html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > Tobramycine TB </span></label></td>';
	    $html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixTobramycineTBABG" > </label></td>';
	    $html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
	    $html .= '      <select id="tobramycineTBABG" style="width: 120px;" disabled> ';
	    $html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
	    $html .= '      </select></label> </td>';
	    $html .= "</tr></table>";
	    
	    /*
	     * Gentamicine GM
	     */
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
	    $html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
	    $html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > Gentamicine GM </span></label></td>';
	    $html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixGentamicineGMABG" > </label></td>';
	    $html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
	    $html .= '      <select id="gentamicineGMABG" style="width: 120px;" disabled> ';
	    $html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
	    $html .= '      </select></label> </td>';
	    $html .= "</tr></table>";
	    
	    /*
	     * Amikacine AN
	     */
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
	    $html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
	    $html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > Amikacine AN </span></label></td>';
	    $html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixAmikacineANABG" > </label></td>';
	    $html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
	    $html .= '      <select id="amikacineANABG" style="width: 120px;" disabled> ';
	    $html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
	    $html .= '      </select></label> </td>';
	    $html .= "</tr></table>";
	    
	    /*
	     * Netilmycine
	     */
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
	    $html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
	    $html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > Netilmycine </span></label></td>';
	    $html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixNetilmycineABG" > </label></td>';
	    $html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
	    $html .= '      <select id="netilmycineABG" style="width: 120px;" disabled> ';
	    $html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
	    $html .= '      </select></label> </td>';
	    $html .= "</tr></table>";
	    
	    /**
	     * Phénicolés --- Phénicolés
	     */
	    $html .= '<table style="width: 100%; margin-top: 17px;">';
	    $html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
	    $html .= '  <td style="width: 100%; text-align: left; font-weight: bold; font-size: 12px; font-style: italic;"> &#10148; Ph&eacute;nicol&eacute;s </td>';
	    $html .= '</tr>';
	    $html .= '</table>';
	    /*
	     * Chloramphénicol C
	     */
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
	    $html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
	    $html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > Chloramph&eacute;nicol C </span></label></td>';
	    $html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixChloramphenicolCABG" > </label></td>';
	    $html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
	    $html .= '      <select id="chloramphenicolCABG" style="width: 120px;" disabled> ';
	    $html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
	    $html .= '      </select></label> </td>';
	    $html .= "</tr></table>";
	    
	    /**
	     * Cyclines --- Cyclines
	     */
	    $html .= '<table style="width: 100%; margin-top: 17px;">';
	    $html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
	    $html .= '  <td style="width: 100%; text-align: left; font-weight: bold; font-size: 12px; font-style: italic;"> &#10148; Cyclines </td>';
	    $html .= '</tr>';
	    $html .= '</table>';
	    /*
	     * Minocycline 
	     */
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
	    $html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
	    $html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > Minocycline </span></label></td>';
	    $html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixMinocyclineABG" > </label></td>';
	    $html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
	    $html .= '      <select id="minocyclineABG" style="width: 120px;" disabled> ';
	    $html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
	    $html .= '      </select></label> </td>';
	    $html .= "</tr></table>";
	    
	    /*
	     * Tétracycline TE
	     */
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
	    $html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
	    $html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > T&eacute;tracycline TE </span></label></td>';
	    $html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixTetracyclineTEABG" > </label></td>';
	    $html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
	    $html .= '      <select id="tetracyclineTEABG" style="width: 120px;" disabled> ';
	    $html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
	    $html .= '      </select></label> </td>';
	    $html .= "</tr></table>";
	    
	    /*
	     * Doxycycline DO
	     */
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
	    $html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
	    $html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > Doxycycline DO </span></label></td>';
	    $html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixDoxycyclineDOABG" > </label></td>';
	    $html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
	    $html .= '      <select id="doxycyclineDOABG" style="width: 120px;" disabled> ';
	    $html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
	    $html .= '      </select></label> </td>';
	    $html .= "</tr></table>";
	    
	    /**
	     * Macrolides et apparentés --- Macrolides et apparentés
	     */
	    $html .= '<table style="width: 100%; margin-top: 17px;">';
	    $html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
	    $html .= '  <td style="width: 100%; text-align: left; font-weight: bold; font-size: 12px; font-style: italic;"> &#10148; Macrolides et apparent&eacute;s </td>';
	    $html .= '</tr>';
	    $html .= '</table>';
	    /*
	     * Azithromycine AZT
	     */
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
	    $html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
	    $html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > Azithromycine AZT </span></label></td>';
	    $html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixAzithromycineAZTABG" > </label></td>';
	    $html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
	    $html .= '      <select id="azithromycineAZTABG" style="width: 120px;" disabled> ';
	    $html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
	    $html .= '      </select></label> </td>';
	    $html .= "</tr></table>";
	    /*
	     * Erythromycine E
	     */
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
	    $html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
	    $html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > Erythromycine E </span></label></td>';
	    $html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixErythromycineEABG" > </label></td>';
	    $html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
	    $html .= '      <select id="erythromycineEABG" style="width: 120px;" disabled> ';
	    $html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
	    $html .= '      </select></label> </td>';
	    $html .= "</tr></table>";
	    /*
	     * Lincomycine L
	     */
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
	    $html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
	    $html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > Lincomycine L </span></label></td>';
	    $html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixLincomycineLABG" > </label></td>';
	    $html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
	    $html .= '      <select id="lincomycineLABG" style="width: 120px;" disabled> ';
	    $html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
	    $html .= '      </select></label> </td>';
	    $html .= "</tr></table>";
	    /*
	     * Pristinamycine PT
	     */
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
	    $html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
	    $html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > Pristinamycine PT </span></label></td>';
	    $html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixPristinamycinePTABG" > </label></td>';
	    $html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
	    $html .= '      <select id="pristinamycinePTABG" style="width: 120px;" disabled> ';
	    $html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
	    $html .= '      </select></label> </td>';
	    $html .= "</tr></table>";
	    
	    /**
	     * Fluoroquinolones --- Fluoroquinolones
	     */
	    $html .= '<table style="width: 100%; margin-top: 17px;">';
	    $html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
	    $html .= '  <td style="width: 100%; text-align: left; font-weight: bold; font-size: 12px; font-style: italic;"> &#10148; Fluoroquinolones </td>';
	    $html .= '</tr>';
	    $html .= '</table>';
	    
	    /*
	     * Acide nalidixique NA
	     */
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
	    $html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
	    $html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > Acide nalidixique NA </span></label></td>';
	    $html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixAcideNalidixiqueNAABG" > </label></td>';
	    $html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
	    $html .= '      <select id="acideNalidixiqueNAABG" style="width: 120px;" disabled> ';
	    $html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
	    $html .= '      </select></label> </td>';
	    $html .= "</tr></table>";
	    /*
	     * Péfloxacine PEF
	     */
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
	    $html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
	    $html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > P&eacute;floxacine PEF </span></label></td>';
	    $html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixPefloxacinePEFABG" > </label></td>';
	    $html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
	    $html .= '      <select id="pefloxacinePEFABG" style="width: 120px;" disabled> ';
	    $html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
	    $html .= '      </select></label> </td>';
	    $html .= "</tr></table>";
	    /*
	     * Norfloxacine NOR
	     */
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
	    $html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
	    $html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > Norfloxacine NOR </span></label></td>';
	    $html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixNorfloxacineNORABG" > </label></td>';
	    $html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
	    $html .= '      <select id="norfloxacineNORABG" style="width: 120px;" disabled> ';
	    $html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
	    $html .= '      </select></label> </td>';
	    $html .= "</tr></table>";
	    /*
	     * Ciprofloxacine CIP
	     */
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
	    $html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
	    $html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > Ciprofloxacine CIP </span></label></td>';
	    $html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixCiprofloxacineCIPABG" > </label></td>';
	    $html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
	    $html .= '      <select id="ciprofloxacineCIPABG" style="width: 120px;" disabled> ';
	    $html .= '        <option value=-1 > </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
	    $html .= '      </select></label> </td>';
	    $html .= "</tr></table>";
	    /*
	     * Lévofloxacine LEV
	     */
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
	    $html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
	    $html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > L&eacute;vofloxacine LEV </span></label></td>';
	    $html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixLEVABG" > </label></td>';
	    $html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
	    $html .= '      <select id="LEVABG" style="width: 120px;" disabled> ';
	    $html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
	    $html .= '      </select></label> </td>';
	    $html .= "</tr></table>";
	    
	    

	    /**
	     * Autres --- Autres
	     */
	    $html .= '<table style="width: 100%; margin-top: 15px;">';
	    $html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
	    $html .= '  <td style="width: 100%; text-align: left; font-weight: bold; font-size: 12px; font-style: italic;"> &#10148; Autres </td>';
	    $html .= '</tr>';
	    $html .= '</table>';
	     
	    /*
	     * Fosfomycine FOS
	     */
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
	    $html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
	    $html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > Fosfomycine FOS </span></label></td>';
	    $html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixFosfomycineFOSABG" > </label></td>';
	    $html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
	    $html .= '      <select id="fosfomycineFOSABG" style="width: 120px;" disabled> ';
	    $html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
	    $html .= '      </select></label> </td>';
	    $html .= "</tr></table>";
	     
	    
	    /*
	     * Acide fusidique FA
	     */
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
	    $html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
	    $html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > Acide fusidique FA </span></label></td>';
	    $html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixAcideFusidiqueFAABG" > </label></td>';
	    $html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
	    $html .= '      <select id="acideFusidiqueFAABG" style="width: 120px;" disabled> ';
	    $html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
	    $html .= '      </select></label> </td>';
	    $html .= "</tr></table>";
	    
	    
	    /*
	     * Rifampycine RIF - Rifampycine RIF
	     */
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
	    $html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
	    $html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > Rifampycine RIF </span></label></td>';
	    $html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixRifampicineRAABG" > </label></td>';
	    $html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
	    $html .= '      <select id="rifampicineRAABG" style="width: 120px;" disabled> ';
	    $html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
	    $html .= '      </select></label> </td>';
	    $html .= "</tr></table>";
	    
	    
	    
	    /*
	     * Fermeture Deuxième colonne
	     */
	    $html .= '  </td>';
	    /*
	     * ==========================
	     */

	    
	    $html .= '</tr>';
	    
	    $html .= '</table>';
	    /*
	     * ==================
	     */
	    
	    
	    $html .= "</tr>";
	    
	    $html .= "</table>";
	    
	    
	    /**
	     * Partie commentaire --- Partie commentaire
	     * -----------------------------------------
	     * Partie commentaire --- Partie commentaire
	     */
	     
	    $html .= '<table style="width: 100%; margin-top: 15px;" class="designEnTeteAnalyse">';
	    $html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
	    if($this->layout()->user['role'] == 'biologiste' || $this->layout()->user['role'] == 'technicien'){
	        $html .= '<td style="width: 100%;"><label class="labABGRadius" style="height: 140px;" ><span style="font-size: 16px; float: left;  margin-left: 30px;"> Conclusion :  </span> <textarea id="conclusion_pv_ABG" style="max-height: 100px; min-height: 100px; max-width: 630px; min-width: 630px; margin-left: 30px;" > </textarea> </label></td>';
	    }else{
	        $html .= '<td style="width: 100%;"><label class="labABGRadius" style="height: 140px;" ><span style="font-size: 16px; float: left;  margin-left: 30px;"> Conclusion :  </span> <textarea id="conclusion_pv_ABG" style="max-height: 100px; min-height: 100px; max-width: 630px; min-width: 630px; margin-left: 30px;" disabled> </textarea> </label></td>';
	    }
	    $html .= '<td style="width: 5%;"></td>';
	    $html .= '</tr>';
	    $html .= '</table>';
	    
	    
	    
	    return $html;
	}
	
	
	
	/**
	 * analyse 66
	 */
	public function ecbu_66(){
	    $html  = "<tr> <td align='center'>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    $html .= "<table style='width: 100%;'>";
	    $html .= "<tr class='ligneAnanlyse labelTypeMateriel' style='width: 100%; font-family: times new roman; font-size: 15px; margin-top: -45px;'>";
	    $html .= "  <td style='width: 55%;'> <label> Mat&eacute;riel utilis&eacute;</label> </td>";
	    $html .= "  <td colspan='2' style='width: 35%;'> </td>";
	    $html .= "</tr>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px;'>";
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_ecbu' tabindex='1' > </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    $html .= "</table>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<table style='width: 100%;'>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td colspan='3' style='width: 100%; text-align: left; font-weight: bold; font-size: 12px; font-style: italic;'> &#10148; Examen macroscopique</td>";
	    $html .= "</tr>";
	    $html .= "</table>";
	     
	    $html .= "<table style='width: 100%;'>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 30%;'><label class='lab1' style='padding-top: 5px;'><span style='font-weight: bold;'> Urines </span></label></td>";
	    $html .= "  <td style='width: 40%;'>
	                    <label class='lab2' style='padding-top: 5px;'>
	                      <select id='urines_ecbu' style='width: 200px;'>
	                        <option> </option>
	                        <option value=1 >Claires</option>
	                        <option value=2 >L&eacute;g&egrave;rement troubles</option>
	                        <option value=3 >Troubles</option>
                	        <option value=4 >H&eacute;matiques</option>
                	        <option value=5 >Purulentes</option>
	                      </select>
	                    </label>
	                </td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 85%;'> </label></td>";
	    $html .= "</tr>";
	    $html .= "</table>";
	    
	     
	    /**
	     * Partie examen microscopique *** Partie examen microscopique
	     * -----------------------------------------------------------
	     * Partie examen microscopique *** Partie examen microscopique
	     */
	    $html .= "<table style='width: 100%;'>";
	    /**
	     * Examen microscopique --- Examen microscopique --- Examen microscopique
	     */
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td colspan='3' style='width: 100%; text-align: left; font-weight: bold; font-size: 12px; font-style: italic;'> &#10148; Examen microscopique</td>";
	    $html .= "</tr>";
	    $html .= "</table>";
	    /*
	     * Leucocytes/champ && Hématies/champ
	     */
	    $html .= "<table style='width: 100%;'>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 20%;'><label class='lab1' style='padding-top: 5px;'><span style='font-weight: bold;'> Leucocytes </span></label></td>";
	    $html .= "  <td style='width: 18%;'>
	                    <label class='lab2' style='padding-top: 5px;'>
	                      <select id='leucocytes_ecbu' style='width: 100px;'>
	                        <option> </option>
	                        <option value=1 >Pr&eacute;sentes</option>
	                        <option value=2 >Absentes</option>
	                      </select>
	                    </label>
	                </td>";
	    $html .= "  <td style='width: 17%;'>
	                    <label class='lab2' style='padding-top: 5px;'>
	                      <input type='text' id='leucocytes_champ_ecbu' style='width: 45px; padding-left: 2px; height: 20px;' >
	                    </label>
	                </td>";
	    $html .= "  <td style='width: 11%;'><label class='lab1' style='font-weight: bold; text-align: right; padding-top: 5px;'> H&eacute;maties </label></td>";
	    $html .= "  <td style='width: 18%;'>
	                    <label class='lab2' style='padding-top: 5px;'>
	                      <select id='hematies_ecbu' style='width: 100px;'>
	                        <option> </option>
	                        <option value=1 >Pr&eacute;sentes</option>
	                        <option value=2 >Absentes</option>
	                      </select>
	                    </label>
	                </td>";
	    $html .= "  <td style='width: 16%;'>
	                    <label class='lab2' style='padding-top: 5px; width: 73%;' title='champs'>
	                      <input type='text' id='hematies_champ_ecbu' style='width: 45px; padding-left: 2px; height: 20px;' >
	                    </label>
	                </td>";
	    $html .= "</tr>";
	    $html .= "</table>";
	     
	    /*
	     * Levures && Filaments Myceliens
	     */
	    $html .= "<table style='width: 100%;'>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 20%;'><label class='lab1' style='padding-top: 5px;' ><span style='font-weight: bold;'> Levures </span></label></td>";
	    $html .= "  <td style='width: 18%;'>
	                    <label class='lab2' style='padding-top: 5px;'>
	                      <select id='levures_ecbu' style='width: 100px;'  onchange='getLevuresFilMycEcbuPositif(this.value)'>
	                        <option> </option>
	                        <option value=1 >Pr&eacute;sence</option>
	                        <option value=2 >Absence</option>
	                      </select>
	                    </label>
	                </td>";
	    $html .= "  <td style='width: 28%;'><label class='lab1' style='font-weight: bold; text-align: right; padding-top: 5px;' ><span class='filamMycECBUToggle' style='visibility: hidden;'> Filaments Myc&eacute;liens </span> </label></td>";
	    $html .= "  <td style='width: 34%;'>
	                    <label class='lab2' style='padding-top: 5px; width: 87%;'>
	                      <select class='filamMycECBUToggle' id='filaments_myceliens_ecbu' style='width: 100px; visibility: hidden;'>
	                        <option> </option>
	                        <option value=1 >Pr&eacute;sence</option>
	                        <option value=2 >Absence</option>
	                      </select>
	                    </label>
	                </td>";
	    $html .= "</tr>";
	     
	    /*
	     * Cellules épithéliales 
	    */
	    $html .= "<table style='width: 100%;'>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 20%;'><label class='lab1' style='padding-top: 5px;'><span style='font-weight: bold;'> Cellules &eacute;pitheliales </span></label></td>";
	    $html .= "  <td style='width: 18%;'>
	                    <label class='lab2' style='padding-top: 5px;'>
	                      <select id='cellules_epitheliales_ecbu' style='width: 100px;'>
	                        <option> </option>
	                        <option value=1 >Pr&eacute;sentes</option>
	                        <option value=2 >Absentes</option>
	                      </select>
	                    </label>
	                </td>";
	    $html .= "  <td style='width: 17%;'>
	                    <label class='lab2' style='padding-top: 5px;'>
	                      <input type='text' id='cellules_epitheliales_champ_ecbu' style='width: 45px; padding-left: 2px; height: 20px;' >/champs
	                    </label>
	                </td>";
	    $html .= "  <td style='width: 20%;'><label class='lab1' style='font-weight: bold; text-align: right; padding-top: 5px;' > </label></td>";
	    $html .= "  <td style='width: 25%;'>
	                    <label class='lab2' style='padding-top: 5px; width: 83%;'>
	                    </label>
	                </td>";
	    $html .= "</tr>";
	    $html .= "</table>";
	    
	     
	    /*
	     * Flore
	     */
	    $html .= "<table style='width: 100%;'>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 20%;'><label class='lab1' style='padding-top: 5px;'><span style='font-weight: bold;' > Flore </span></label></td>";
	    $html .= "  <td style='width: 35%;'>
	                    <label class='lab2' style='padding-top: 5px;'>
	                      <select id='flore_ecbu' style='width: 220px; font-size: 13px;'  onchange='getFloreCocciEcbuPositif(this.value)'>
	                        <option> </option>
	    		            <option value=5 >Absente</option>
	                        <option value=1 >Bacilles &agrave; Gram n&eacute;gatif</option>
	                        <option value=2 >Bacilles &agrave; Gram positif</option>
                	        <option value=3 >Cocci &agrave; Gram positif</option>
                	        <option value=4 >Diplocoques &agrave; Gram n&eacute;gatif</option>
	                      </select>
	                    </label>
	                </td>";
	    
	    $html .= "  <td style='width: 20%;'>
	                    <label style='padding-top: 5px;'><span class='flore_cocci_positif_ecbu' style='visibility: hidden;'> <input type='checkbox' style='width:20px;' id='flore_cocci_pos_Choix1_ecbu' > en amas </span></label>
	                </td>";
	    $html .= "  <td style='width: 25%;'>
	                    <label style='padding-top: 5px; width: 82%;'><span class='flore_cocci_positif_ecbu' style='visibility: hidden;'> <input type='checkbox' style='width:20px;' id='flore_cocci_pos_Choix2_ecbu' > en chainettes </span></label>
	                </td>";
	     
	    $html .= "</tr>";
	    $html .= "</table>";
	    
	    /*
	     * Parasites --- Parasites --- Parasites
	     */
	    $html .= "<table style='width: 100%;'>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 20%;'><label class='lab1' style='padding-top: 5px;'><div style='position: absolute; left: 30px; font-weight: bold; font-size: 25px; color: green;' onclick='ajouterNouvelParasiteECBU();' >&plus;</div> <span style='font-weight: bold;' > Parasites </span></label></td>";
	    $html .= "  <td style='width: 35%;'>
	                    <label class='lab2' style='padding-top: 5px;'>
	                      <select id='parasites_ecbu' style='width: 220px;' >
	                        <option> </option>
	                      </select>
	                    </label>
	                </td>";
	    $html .= "  <td style='width: 45%;'>
	                    <label style='padding-top: 5px; width: 90%;'></label>
	                </td>";
	    $html .= "</tr>";
	    $html .= "</table>";

	    
	    
	    
	    /*
	     * Culot --- Culot --- Culot --- Culot
	     */
	    $html .= "<table style='width: 100%;'>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 20%;'><label class='lab1' style='padding-top: 5px;'><span style='font-weight: bold;' > Culot </span></label></td>";
	    $html .= "  <td style='width: 35%;'>
	                    <label class='lab2' style='padding-top: 5px;'>
	                      <select id='culot_ecbu' style='width: 100px;'  onchange='getCulotEcbuPositif(this.value)'>
	                        <option> </option>
	                        <option value=1 >Positif</option>
	                        <option value=2 >N&eacute;gatif</option>
	                      </select>
	                    </label>
	                </td>";
	    $html .= "  <td style='width: 45%;'>
	                    <label style='padding-top: 5px; width: 90%;'></label>
	                </td>";
	    $html .= "</tr>";
	    $html .= "</table>";
	    
	    $html .= "<table id='culot_ecbu_tableau' style='width: 100%; display: none'>";
	    $html .= "<tr id='culot_ecbu_ligne_1' class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 20%;'></td>";
	    $html .= "  <td style='width: 20%;'>
	                  <label class='lab1 listeSelect'>
                          <select onchange='listeElemtsCulotEcbuSelect(1,this.value);' name='culot_ecbu_select_1' id='culot_ecbu_select_1'> 
                            <option value=0 > </option> 
                            <option value=1 >Oeufs</option> 
                            <option value=2 >Cristaux</option> 
                            <option value=3 >Cylindres</option> 
	                        <option value=4 >Parasites</option> 
                          </select> 
	                  </label>
	                </td>";
	    $html .= "  <td style='width: 40%;'><label class='lab2 emplaceListeElemtsCUSelect' id='culot_ecbu_valsel_1' style='padding-top: 5px;'>  </label></td>";
	    $html .= "  <td style='width: 20%;'></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 20%;'></td>";
	    $html .= "  <td style='width: 80%;' colspan='3'> <div style='float: left; width: 20%; text-align: center; font-weight: bold; font-size: 25px;'> <div style='float: left; width: 30%; cursor: pointer; display: none;' id='culot_ecbu_moins' class='culot_ecbu_pm'> &minus; </div> <div style=' float: left; width: 30%; cursor: pointer;'  id='culot_ecbu_plus' class='culot_ecbu_pm'> + </div> </div></td>";
	    $html .= "</tr>";
	    $html .= "</table>";
	    
	    /**
	     * Partie Culture *** Partie Culture
	     * ---------------------------------
	     * Partie Culture *** Partie Culture
	     */
	    $html .= "<table style='width: 100%;'>";
	    /**
	     * Culture --- Culture --- Culture
	     */
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td colspan='3' style='width: 100%; text-align: left; font-weight: bold; font-size: 12px; font-style: italic;'> &#10148; Culture </td>";
	    $html .= "</tr>";
	    $html .= "</table>";
	    /*
	     * Culture 
	     */
	    $html .= "<table style='width: 100%;'>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 20%;'><label class='lab1' style='padding-top: 5px;'><span style='font-weight: bold;' > Culture </span></label></td>";
	    $html .= "  <td style='width: 25%;'>
	                    <label class='lab2' style='padding-top: 5px;'>
	                      <select id='culture_ecbu' style='width: 100px; font-size: 13px;'  onchange='getCultureEcbuPositif(this.value)'>
	                        <option> </option>
	                        <option value=1 >Positif</option>
	                        <option value=2 >N&eacute;gatif </option>
	                      </select>
	                    </label>
	                </td>";
	    $html .= "  <td style='width: 20%;'>
	                    <label style='padding-top: 5px;'> 
	                      <span class='culture_ecbu_negatif' style='visibility: hidden;'> 
	                        DGU &#60; 10<sup>4</sup> germes/ml 
	                      </span> 
	                    </label>
	                </td>";
	    $html .= "  <td style='width: 35%;'>
	                    <label style='padding-top: 5px; width: 87%;'> </label>
	                </td>";
	    
	    $html .= "</tr>";
	    $html .= "</table>";
	    
	    /*
	     * Culture positive
	     */
	    $html .= "<table id='culture_ecbu_choix12_positif' style='width: 100%; display: none;'>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 40%;'>
	                    <label style='padding-top: 5px;' id='cul_label_pos_Choix1_ecbu' >
	                      <span class='culture_ecbu_positif' > 
	                        <input type='checkbox' style='width:20px;' id='culture_pos_Choix1_ecbu' onclick='getCultPosChoix1Ecbu();'> 
	                        DGU &#8805; 10<sup>5</sup> germes/ml 
	                      </span>
	                    </label>
	                </td>";
	    $html .= "  <td style='width: 40%;'>
	                    <label style='padding-top: 5px;' id='cul_label_pos_Choix2_ecbu' >
	                      <span class='culture_ecbu_positif' > 
	                        <input type='checkbox' style='width:20px;' id='culture_pos_Choix2_ecbu' onclick='getCultPosChoix2Ecbu();'>  
	                        10<sup>4</sup> < DGU < 10<sup>5</sup> germes/ml
	                      </span>
	                    </label>
	                </td>";
	    $html .= "  <td style='width: 20%;'> <label style='padding-top: 5px; width: 78%;'></label> </td>";
	    $html .= "</tr>";
	    $html .= "</table>";
	     
	    /*
	     * Les champs 'Identification'
	     */
	    $html .= "<table style='width: 100%; display: none;' class='identificationCultureChampsECBU identificationCultureChampsECBUABR_1'  >";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 25%;'><label class='lab1' style='padding-top: 5px;'><div style='position: absolute; left: 30px; font-weight: bold; font-size: 25px; color: green; ' onclick='ajouterNouvelleSoucheECBU();' >&plus;</div><span style='font-weight: bold;'> Identification </span></label></td>";
	    $html .= "  <td style='width: 27%;'>
	                    <label class='lab2' style='padding-top: 5px;'>
	                      <select id='identification_culture_select_ecbu' style='width: 190px;' onchange='getIconeAntibiogrammeCulturePosECBU(this.value,1)'>
	                      </select>
	                    </label>
	                </td>";
	    $html .= "  <td style='width: 48%;'><label onclick='antibiogrammeAfficherInterfaceECBU()' class='lab1 antiBGButAffInterfaceCultECBU1' style='padding-top: 0px; margin-top: 3px; margin-left: 10px; width: 30%; height: 15px; font-style: italic; border-radius: 35%; border: 3px solid #d8d8d8; padding-left: 10px; display: none;'> Antibiogramme </label></td>";
	    $html .= "</tr>";
	    $html .= "</table>";
	    
	    
	    
	    
	    /**
	     * Les autres à placer ici
	     */
	     
	    /*
	     * Préparer l'interface de saisie des données de l'antibiogramme
	    */
	    $interfaceSaisieDonneesABG = $this->getAntiBioGrammeDeECBU();
	    $html .= "<script> $('#contenuResultatsAnalysesECBUAntiBioGramme div').html('".$interfaceSaisieDonneesABG."'); </script>";
	    /*
	     * Ajouter les scripts pour la gestion des checkbox non cochés
	     * Pour la première colonne
	     */
	    $html .= '<script>  $("#choixAmpicillineAMABGecbu").click(function(){ if($("#choixAmpicillineAMABGecbu").get(0).checked){ $("#ampicillineAMABGecbu").attr("disabled", false); }else{ $("#ampicillineAMABGecbu").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixAmoxillineAMXABGecbu").click(function(){ if($("#choixAmoxillineAMXABGecbu").get(0).checked){ $("#amoxillineAMXABGecbu").attr("disabled", false); }else{ $("#amoxillineAMXABGecbu").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixTicarcillineTICABGecbu").click(function(){ if($("#choixTicarcillineTICABGecbu").get(0).checked){ $("#ticarcillineTICABGecbu").attr("disabled", false); }else{ $("#ticarcillineTICABGecbu").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixPiperacillinePIPABGecbu").click(function(){ if($("#choixPiperacillinePIPABGecbu").get(0).checked){ $("#piperacillinePIPABGecbu").attr("disabled", false); }else{ $("#piperacillinePIPABGecbu").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixAcideClavulaniqueAmoxicillineAMCABGecbu").click(function(){ if($("#choixAcideClavulaniqueAmoxicillineAMCABGecbu").get(0).checked){ $("#acideClavulaniqueAmoxicillineAMCABGecbu").attr("disabled", false); }else{ $("#acideClavulaniqueAmoxicillineAMCABGecbu").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixGentamicineGMABGecbu").click(function(){ if($("#choixGentamicineGMABGecbu").get(0).checked){ $("#gentamicineGMABGecbu").attr("disabled", false); }else{ $("#gentamicineGMABGecbu").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixTicAcClavTCCABGecbu").click(function(){ if($("#choixTicAcClavTCCABGecbu").get(0).checked){ $("#ticAcClavTCCABGecbu").attr("disabled", false); }else{ $("#ticAcClavTCCABGecbu").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixErtapenemeETPABGecbu").click(function(){ if($("#choixErtapenemeETPABGecbu").get(0).checked){ $("#ertapenemeETPABGecbu").attr("disabled", false); }else{ $("#ertapenemeETPABGecbu").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixImipenemeIPMABGecbu").click(function(){ if($("#choixImipenemeIPMABGecbu").get(0).checked){ $("#imipenemeIPMABGecbu").attr("disabled", false); }else{ $("#imipenemeIPMABGecbu").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixOxacillineOXABGecbu").click(function(){ if($("#choixOxacillineOXABGecbu").get(0).checked){ $("#oxacillineOXABGecbu").attr("disabled", false); }else{ $("#oxacillineOXABGecbu").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixPenicillinePABGecbu").click(function(){ if($("#choixPenicillinePABGecbu").get(0).checked){ $("#penicillinePABGecbu").attr("disabled", false); }else{ $("#penicillinePABGecbu").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixCefalotineCFABGecbu").click(function(){ if($("#choixCefalotineCFABGecbu").get(0).checked){ $("#cefalotineCFABGecbu").attr("disabled", false); }else{ $("#cefalotineCFABGecbu").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixCefoxitineFOXABGecbu").click(function(){ if($("#choixCefoxitineFOXABGecbu").get(0).checked){ $("#cefoxitineFOXABGecbu").attr("disabled", false); }else{ $("#cefoxitineFOXABGecbu").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixPiperacillineTazobactamePPTABGecbu").click(function(){ if($("#choixPiperacillineTazobactamePPTABGecbu").get(0).checked){ $("#piperacillineTazobactamePPTABGecbu").attr("disabled", false); }else{ $("#piperacillineTazobactamePPTABGecbu").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixCefotaximeCTXABGecbu").click(function(){ if($("#choixCefotaximeCTXABGecbu").get(0).checked){ $("#cefotaximeCTXABGecbu").attr("disabled", false); }else{ $("#cefotaximeCTXABGecbu").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixCefsulodineCFSABGecbu").click(function(){ if($(this).get(0).checked){ $("#cefsulodineCFSABGecbu").attr("disabled", false); }else{ $("#cefsulodineCFSABGecbu").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixCFPABGecbu").click(function(){ if($(this).get(0).checked){ $("#CFPABGecbu").attr("disabled", false); }else{ $("#CFPABGecbu").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixCeftazidimeCAZABGecbu").click(function(){ if($(this).get(0).checked){ $("#ceftazidimeCAZABGecbu").attr("disabled", false); }else{ $("#ceftazidimeCAZABGecbu").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixCeftriaxoneCROABGecbu").click(function(){ if($(this).get(0).checked){ $("#ceftriaxoneCROABGecbu").attr("disabled", false); }else{ $("#ceftriaxoneCROABGecbu").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixCefepimeFEPABGecbu").click(function(){ if($(this).get(0).checked){ $("#cefepimeFEPABGecbu").attr("disabled", false); }else{ $("#cefepimeFEPABGecbu").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixAztreonamATMABGecbu").click(function(){ if($(this).get(0).checked){ $("#aztreonamATMABGecbu").attr("disabled", false); }else{ $("#aztreonamATMABGecbu").attr("disabled", true).val(""); } }) </script>';
	    /* PARTIE Glycopeptides */
	    $html .= '<script>  $("#choixVancomycineVAABGecbu").click(function(){ if($(this).get(0).checked){ $("#vancomycineVAABGecbu").attr("disabled", false); }else{ $("#vancomycineVAABGecbu").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixTeicoplanineABGecbu").click(function(){ if($(this).get(0).checked){ $("#teicoplanineABGecbu").attr("disabled", false); }else{ $("#teicoplanineABGecbu").attr("disabled", true).val(""); } }) </script>';
	    /* PARTIE Produits nitrés */
	    $html .= '<script>  $("#choixNitrofuraneABGecbu").click(function(){ if($(this).get(0).checked){ $("#nitrofuraneABGecbu").attr("disabled", false); }else{ $("#nitrofuraneABGecbu").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixMetronidazoleABGecbu").click(function(){ if($(this).get(0).checked){ $("#metronidazoleABGecbu").attr("disabled", false); }else{ $("#metronidazoleABGecbu").attr("disabled", true).val(""); } }) </script>';
	    /* PARTIE Sulfamides */
	    $html .= '<script>  $("#choixTrimethoprimeSulfametoxazoleSXTABGecbu").click(function(){ if($(this).get(0).checked){ $("#trimethoprimeSulfametoxazoleSXTABGecbu").attr("disabled", false); }else{ $("#trimethoprimeSulfametoxazoleSXTABGecbu").attr("disabled", true).val(""); } }) </script>';
	    
	     
	    /*
	     * Ajouter les scripts pour la gestion des checkbox non cochés
	    * Pour la première colonne
	    */
	     
	    /* PARTIE Polymyxine */
	    $html .= '<script>  $("#choixColistineCSABGecbu").click(function(){ if($(this).get(0).checked){ $("#colistineCSABGecbu").attr("disabled", false); }else{ $("#colistineCSABGecbu").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixPolymicineABGecbu").click(function(){ if($(this).get(0).checked){ $("#polymicineABGecbu").attr("disabled", false); }else{ $("#polymicineABGecbu").attr("disabled", true).val(""); } }) </script>';
	    /* PARTIE Aminosides */
	    $html .= '<script>  $("#choixKanamycineKABGecbu").click(function(){ if($(this).get(0).checked){ $("#kanamycineKABGecbu").attr("disabled", false); }else{ $("#kanamycineKABGecbu").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixTobramycineTBABGecbu").click(function(){ if($(this).get(0).checked){ $("#tobramycineTBABGecbu").attr("disabled", false); }else{ $("#tobramycineTBABGecbu").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixAmikacineANABGecbu").click(function(){ if($(this).get(0).checked){ $("#amikacineANABGecbu").attr("disabled", false); }else{ $("#amikacineANABGecbu").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixNetilmycineABGecbu").click(function(){ if($(this).get(0).checked){ $("#netilmycineABGecbu").attr("disabled", false); }else{ $("#netilmycineABGecbu").attr("disabled", true).val(""); } }) </script>';
	    /* PARTIE Phénicolés */
	    $html .= '<script>  $("#choixChloramphenicolCABGecbu").click(function(){ if($(this).get(0).checked){ $("#chloramphenicolCABGecbu").attr("disabled", false); }else{ $("#chloramphenicolCABGecbu").attr("disabled", true).val(""); } }) </script>';
	    /* PARTIE Cyclines */
	    $html .= '<script>  $("#choixMinocyclineABGecbu").click(function(){ if($(this).get(0).checked){ $("#minocyclineABGecbu").attr("disabled", false); }else{ $("#minocyclineABGecbu").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixTetracyclineTEABGecbu").click(function(){ if($(this).get(0).checked){ $("#tetracyclineTEABGecbu").attr("disabled", false); }else{ $("#tetracyclineTEABGecbu").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixDoxycyclineDOABGecbu").click(function(){ if($(this).get(0).checked){ $("#doxycyclineDOABGecbu").attr("disabled", false); }else{ $("#doxycyclineDOABGecbu").attr("disabled", true).val(""); } }) </script>';
	    /* PARTIE Macrolides et apparentés */
	    $html .= '<script>  $("#choixAzithromycineAZTABGecbu").click(function(){ if($(this).get(0).checked){ $("#azithromycineAZTABGecbu").attr("disabled", false); }else{ $("#azithromycineAZTABGecbu").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixErythromycineEABGecbu").click(function(){ if($(this).get(0).checked){ $("#erythromycineEABGecbu").attr("disabled", false); }else{ $("#erythromycineEABGecbu").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixLincomycineLABGecbu").click(function(){ if($(this).get(0).checked){ $("#lincomycineLABGecbu").attr("disabled", false); }else{ $("#lincomycineLABGecbu").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixPristinamycinePTABGecbu").click(function(){ if($(this).get(0).checked){ $("#pristinamycinePTABGecbu").attr("disabled", false); }else{ $("#pristinamycinePTABGecbu").attr("disabled", true).val(""); } }) </script>';
	    /* PARTIE Fluoroquinolones*/
	    $html .= '<script>  $("#choixAcideNalidixiqueNAABGecbu").click(function(){ if($(this).get(0).checked){ $("#acideNalidixiqueNAABGecbu").attr("disabled", false); }else{ $("#acideNalidixiqueNAABGecbu").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixPefloxacinePEFABGecbu").click(function(){ if($(this).get(0).checked){ $("#pefloxacinePEFABGecbu").attr("disabled", false); }else{ $("#pefloxacinePEFABGecbu").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixNorfloxacineNORABGecbu").click(function(){ if($(this).get(0).checked){ $("#norfloxacineNORABGecbu").attr("disabled", false); }else{ $("#norfloxacineNORABGecbu").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixCiprofloxacineCIPABGecbu").click(function(){ if($(this).get(0).checked){ $("#ciprofloxacineCIPABGecbu").attr("disabled", false); }else{ $("#ciprofloxacineCIPABGecbu").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixLEVABGecbu").click(function(){ if($(this).get(0).checked){ $("#LEVABGecbu").attr("disabled", false); }else{ $("#LEVABGecbu").attr("disabled", true).val(""); } }) </script>';
	    /* PARTIE Imidazolés*/
	    $html .= '<script>  $("#choixRifampicineRAABGecbu").click(function(){ if($(this).get(0).checked){ $("#rifampicineRAABGecbu").attr("disabled", false); }else{ $("#rifampicineRAABGecbu").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixCotrimoxazoleSXTABGecbu").click(function(){ if($(this).get(0).checked){ $("#cotrimoxazoleSXTABGecbu").attr("disabled", false); }else{ $("#cotrimoxazoleSXTABGecbu").attr("disabled", true).val(""); } }) </script>';
	    
	    /* PARTIE Autres*/
	    $html .= '<script>  $("#choixFosfomycineFOSABGecbu").click(function(){ if($(this).get(0).checked){ $("#fosfomycineFOSABGecbu").attr("disabled", false); }else{ $("#fosfomycineFOSABGecbu").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixAcideFusidiqueFAABGecbu").click(function(){ if($(this).get(0).checked){ $("#acideFusidiqueFAABGecbu").attr("disabled", false); }else{ $("#acideFusidiqueFAABGecbu").attr("disabled", true).val(""); } }) </script>';
	    
	    
	    
	    
	    
	    /**
	     * Partie commentaire --- Partie commentaire
	     * -----------------------------------------
	     * Partie commentaire --- Partie commentaire
	     */
	     
	    $html .= "<table style='width: 100%; margin-top: 15px;'>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    if($this->layout()->user['role'] == 'biologiste' || $this->layout()->user['role'] == 'technicien'){
	    	$html .= "<td style='width: 96%;'><label style='height: 140px;' ><span style='font-size: 16px; float: left;  margin-left: 30px;'> Commentaire:  </span> <textarea id='commentaire_ecbu' style='max-height: 100px; min-height: 100px; max-width: 560px; min-width: 560px; margin-left: 30px;' readonly> </textarea> </label></td>";
	    }else{
	    	$html .= "<td style='width: 96%;'><label style='height: 140px;' ><span style='font-size: 16px; float: left;  margin-left: 30px;'> Commentaire:  </span> <textarea id='commentaire_ecbu' style='max-height: 100px; min-height: 100px; max-width: 560px; min-width: 560px; margin-left: 30px;' disabled> </textarea> </label></td>";
	    }
	    $html .= "<td style='width: 5%;'></td>";
	    $html .= "</tr>";
	    $html .= "</table>";
	     
	    
	    /**
	     * PRECISION A APPORTER SUR LE COMMENTAIRE
	     */
	    $html .= "<table style='width: 100%; margin-top: 2px;'>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    if($this->layout()->user['role'] == 'biologiste' || $this->layout()->user['role'] == 'technicien'){
	    	$html .= "<td style='width: 96%;'><label style='height: 140px;' ><span style='font-size: 16px; float: left;  margin-left: 30px;'> Autres Pr&eacute;cisions :  </span> <textarea id='autres_precisions_commentaire_ecbu' style='max-height: 100px; min-height: 100px; max-width: 560px; min-width: 560px; margin-left: 30px; text-align: justify; padding: 5px;' > </textarea> </label></td>";
	    }else{
	    	$html .= "<td style='width: 96%;'><label style='height: 140px;' ><span style='font-size: 16px; float: left;  margin-left: 30px;'> Autres Pr&eacute;cisions :  </span> <textarea id='autres_precisions_commentaire_ecbu' style='max-height: 100px; min-height: 100px; max-width: 560px; min-width: 560px; margin-left: 30px; text-align: justify; padding: 5px;' disabled> </textarea> </label></td>";
	    }
	    $html .= "<td style='width: 5%;'></td>";
	    $html .= "</tr>";
	    $html .= "</table>";
	    
	    
	    $html .="</td> </tr>";
	    
	
	    return $html;
	}
	
	
	
	/**
	 * interface pour l'antibiogramme de l'analyse 65
	 */
	public function getAntiBioGrammeDeECBU()
	{
		/*
		 * Entete
		*/
		$html  = '<table style="width: 100%;"><tr style="width: 100%;"><td  style="width: 100%;" align=center> <div style="border-radius: 25%; border: 3px solid #d8d8d8; background-color: #f1f1f1; width: 40%; font-weight: bold; "> Antibiogramme </div> </td></tr></table>';
		$html .= '<table style="width: 100%; margin-top: 15px;"><tr style="width: 100%;"><td  style="width: 100%; font-family: time new" align=left> <div style="background-color: #f1f1f1; width: 50%; font-weight: bold; float: left; font-size: 15px; ">&#9883; Souche isol&eacute;e : <span id="valeurSoucheIsoleeIdentificationCultureECBU" style="font-size: 17px; font-weight: normal;"> Nom de la souche</span> </div> </td></tr></table>';
		/*
		 * =======
		*/
		 
		$html .= '<table style="width: 100%;">';
		$html .= '<tr style="width: 100%;">';
		/*
		 * Premiere colonne
		*/
		$html .= '  <td style="width: 49%; height: 20px; background: re; vertical-align: top;">';
		/*
		 * ================
		*/
		 
		/**
		 * B-lactamines --- B-lactamines
		 */
		$html .= '<table style="width: 100%; margin-top: 15px;">';
		$html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
		$html .= '  <td style="width: 100%; text-align: left; font-weight: bold; font-size: 12px; font-style: italic;"> &#10148; &#42933;-lactamines </td>';
		$html .= '</tr>';
		$html .= '</table>';
		/*
		 * Ampicilline AM
		*/
		$html .= '<table style="width: 100%;" class="designEnTeteAnalyse">';
		$html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
		$html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > Ampicilline AM </span></label></td>';
		$html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixAmpicillineAMABGecbu" > </label></td>';
		$html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
		$html .= '      <select id="ampicillineAMABGecbu" style="width: 120px;" disabled> ';
		$html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
		$html .= '      </select></label> </td>';
		$html .= '</tr></table>';
	
		/*
		 * Amoxilline AMX
		*/
		$html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
		$html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
		$html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > Amoxilline AMX </span></label></td>';
		$html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixAmoxillineAMXABGecbu" > </label></td>';
		$html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
		$html .= '      <select id="amoxillineAMXABGecbu" style="width: 120px;" disabled> ';
		$html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
		$html .= '      </select></label> </td>';
		$html .= "</tr></table>";
		 
		/*
		 * Ticarcilline TIC
		*/
		$html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
		$html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
		$html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > Ticarcilline TIC </span></label></td>';
		$html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixTicarcillineTICABGecbu" > </label></td>';
		$html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
		$html .= '      <select id="ticarcillineTICABGecbu" style="width: 120px;" disabled> ';
		$html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
		$html .= '      </select></label> </td>';
		$html .= "</tr></table>";
		 
		/*
		 * Piperacilline PIP
		*/
		$html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
		$html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
		$html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > Piperacilline PIP </span></label></td>';
		$html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixPiperacillinePIPABGecbu" > </label></td>';
		$html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
		$html .= '      <select id="piperacillinePIPABGecbu" style="width: 120px;" disabled> ';
		$html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
		$html .= '      </select></label> </td>';
		$html .= "</tr></table>";
		 
		/*
		 * Acide clavulanique + Amoxicilline AMC
		*/
		$html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
		$html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
		$html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" title="Acide clavulanique + Amoxicilline AMC"> Acide clav. + Amoxi. AMC </span></label></td>';
		$html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixAcideClavulaniqueAmoxicillineAMCABGecbu" > </label></td>';
		$html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
		$html .= '      <select id="acideClavulaniqueAmoxicillineAMCABGecbu" style="width: 120px;" disabled> ';
		$html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
		$html .= '      </select></label> </td>';
		$html .= "</tr></table>";
		 
		/*
		 * Tic-Ac-Clav TCC
		*/
		$html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
		$html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
		$html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > Tic-Ac-Clav TCC </span></label></td>';
		$html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixTicAcClavTCCABGecbu" > </label></td>';
		$html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
		$html .= '      <select id="ticAcClavTCCABGecbu" style="width: 120px;" disabled> ';
		$html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
		$html .= '      </select></label> </td>';
		$html .= "</tr></table>";
		 
		/*
		 * Ertapénème ETP
		*/
		$html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
		$html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
		$html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > Ertap&eacute;n&egrave;me ETP </span></label></td>';
		$html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixErtapenemeETPABGecbu" > </label></td>';
		$html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
		$html .= '      <select id="ertapenemeETPABGecbu" style="width: 120px;" disabled> ';
		$html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
		$html .= '      </select></label> </td>';
		$html .= "</tr></table>";
		 
		/*
		 * Imipénème IPM
		*/
		$html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
		$html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
		$html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > Imip&eacute;n&egrave;me IPM </span></label></td>';
		$html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixImipenemeIPMABGecbu" > </label></td>';
		$html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
		$html .= '      <select id="imipenemeIPMABGecbu" style="width: 120px;" disabled> ';
		$html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
		$html .= '      </select></label> </td>';
		$html .= "</tr></table>";
		 
		/*
		 * Oxacilline OX
		*/
		$html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
		$html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
		$html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > Oxacilline OX </span></label></td>';
		$html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixOxacillineOXABGecbu" > </label></td>';
		$html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
		$html .= '      <select id="oxacillineOXABGecbu" style="width: 120px;" disabled> ';
		$html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
		$html .= '      </select></label> </td>';
		$html .= "</tr></table>";
		 
		/*
		 * Penicilline P
		*/
		$html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
		$html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
		$html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > Penicilline P </span></label></td>';
		$html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixPenicillinePABGecbu" > </label></td>';
		$html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
		$html .= '      <select id="penicillinePABGecbu" style="width: 120px;" disabled> ';
		$html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
		$html .= '      </select></label> </td>';
		$html .= "</tr></table>";
		 
		/*
		 * Céfalotine CF
		*/
		$html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
		$html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
		$html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > C&eacute;falotine CF </span></label></td>';
		$html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixCefalotineCFABGecbu" > </label></td>';
		$html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
		$html .= '      <select id="cefalotineCFABGecbu" style="width: 120px;" disabled> ';
		$html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
		$html .= '      </select></label> </td>';
		$html .= "</tr></table>";
		 
		/*
		 * Céfoxitine FOX
		*/
		$html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
		$html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
		$html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > C&eacute;foxitine FOX </span></label></td>';
		$html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixCefoxitineFOXABGecbu" > </label></td>';
		$html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
		$html .= '      <select id="cefoxitineFOXABGecbu" style="width: 120px;" disabled> ';
		$html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
		$html .= '      </select></label> </td>';
		$html .= "</tr></table>";
		 
		/*
		 * Pipéracilline + Tazobactame PPT
		*/
		$html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
		$html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
		$html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" title="Pip&eacute;racilline + Tazobactame PPT"> Pip&eacute;racil. + Tazobact. PPT </span></label></td>';
		$html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixPiperacillineTazobactamePPTABGecbu" > </label></td>';
		$html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
		$html .= '      <select id="piperacillineTazobactamePPTABGecbu" style="width: 120px;" disabled> ';
		$html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
		$html .= '      </select></label> </td>';
		$html .= "</tr></table>";
	
		/*
		 * Céfotaxime CTX
		*/
		$html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
		$html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
		$html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > C&eacute;fotaxime CTX </span></label></td>';
		$html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixCefotaximeCTXABGecbu" > </label></td>';
		$html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
		$html .= '      <select id="cefotaximeCTXABGecbu" style="width: 120px;" disabled> ';
		$html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
		$html .= '      </select></label> </td>';
		$html .= "</tr></table>";
		 
		/*
		 * Cefsulodine CFS
		*/
		$html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
		$html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
		$html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > Cefsulodine CFS </span></label></td>';
		$html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixCefsulodineCFSABGecbu" > </label></td>';
		$html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
		$html .= '      <select id="cefsulodineCFSABGecbu" style="width: 120px;" disabled> ';
		$html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
		$html .= '      </select></label> </td>';
		$html .= "</tr></table>";
		 
		/*
		 * Cefopérazone CFP
		*/
		$html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
		$html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
		$html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > C&eacute;fop&eacute;razone CFP </span></label></td>';
		$html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixCFPABGecbu" > </label></td>';
		$html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
		$html .= '      <select id="CFPABGecbu" style="width: 120px;" disabled> ';
		$html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
		$html .= '      </select></label> </td>';
		$html .= "</tr></table>";
	
		/*
		 * Ceftazidime CAZ
		*/
		$html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
		$html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
		$html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > Ceftazidime CAZ </span></label></td>';
		$html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixCeftazidimeCAZABGecbu" > </label></td>';
		$html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
		$html .= '      <select id="ceftazidimeCAZABGecbu" style="width: 120px;" disabled> ';
		$html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
		$html .= '      </select></label> </td>';
		$html .= "</tr></table>";
		 
		/*
		 * Ceftriaxone CRO
		*/
		$html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
		$html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
		$html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > Ceftriaxone CRO </span></label></td>';
		$html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixCeftriaxoneCROABGecbu" > </label></td>';
		$html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
		$html .= '      <select id="ceftriaxoneCROABGecbu" style="width: 120px;" disabled> ';
		$html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
		$html .= '      </select></label> </td>';
		$html .= "</tr></table>";
		 
		/*
		 * Céfépime FEP
		*/
		$html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
		$html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
		$html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > C&eacute;f&eacute;pime FEP </span></label></td>';
		$html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixCefepimeFEPABGecbu" > </label></td>';
		$html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
		$html .= '      <select id="cefepimeFEPABGecbu" style="width: 120px;" disabled> ';
		$html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
		$html .= '      </select></label> </td>';
		$html .= "</tr></table>";
		 
		/*
		 * Aztréonam ATM
		*/
		$html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
		$html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
		$html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > Aztr&eacute;onam ATM </span></label></td>';
		$html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixAztreonamATMABGecbu" > </label></td>';
		$html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
		$html .= '      <select id="aztreonamATMABGecbu" style="width: 120px;" disabled> ';
		$html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
		$html .= '      </select></label> </td>';
		$html .= "</tr></table>";
		 
		/**
		 * Imidazolés --- Imidazolés
		 */
		$html .= '<table style="width: 100%; margin-top: 14px;">';
		$html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
		$html .= '  <td style="width: 100%; text-align: left; font-weight: bold; font-size: 12px; font-style: italic;"> &#10148; Imidazol&eacute;s </td>';
		$html .= '</tr>';
		$html .= '</table>';
		/*
		 * Cotrimoxazole
		*/
		$html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
		$html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
		$html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > Cotrimoxazole </span></label></td>';
		$html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixCotrimoxazoleSXTABGecbu" > </label></td>';
		$html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
		$html .= '      <select id="cotrimoxazoleSXTABGecbu" style="width: 120px;" disabled> ';
		$html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
		$html .= '      </select></label> </td>';
		$html .= "</tr></table>";
		 
		/**
		 * Glycopeptides --- Glycopeptides
		 */
		$html .= '<table style="width: 100%; margin-top: 14px;">';
		$html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
		$html .= '  <td style="width: 100%; text-align: left; font-weight: bold; font-size: 12px; font-style: italic;"> &#10148; Glycopeptides </td>';
		$html .= '</tr>';
		$html .= '</table>';
		/*
		 * Vancomycine VA
		*/
		$html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
		$html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
		$html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > Vancomycine VA </span></label></td>';
		$html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixVancomycineVAABGecbu" > </label></td>';
		$html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
		$html .= '      <select id="vancomycineVAABGecbu" style="width: 120px;" disabled> ';
		$html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
		$html .= '      </select></label> </td>';
		$html .= "</tr></table>";
		/*
		 * Teicoplanine
		*/
		$html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
		$html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
		$html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > Teicoplanine </span></label></td>';
		$html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixTeicoplanineABGecbu" > </label></td>';
		$html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
		$html .= '      <select id="teicoplanineABGecbu" style="width: 120px;" disabled> ';
		$html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
		$html .= '      </select></label> </td>';
		$html .= "</tr></table>";
		 
		/**
		 * Produits nitrés --- Produits nitrés
		 */
		$html .= '<table style="width: 100%; margin-top: 14px;">';
		$html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
		$html .= '  <td style="width: 100%; text-align: left; font-weight: bold; font-size: 12px; font-style: italic;"> &#10148; Produits nitr&eacute;s </td>';
		$html .= '</tr>';
		$html .= '</table>';
		/*
		 * Nitrofurane
		*/
		$html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
		$html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
		$html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > Nitrofurane </span></label></td>';
		$html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixNitrofuraneABGecbu" > </label></td>';
		$html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
		$html .= '      <select id="nitrofuraneABGecbu" style="width: 120px;" disabled> ';
		$html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
		$html .= '      </select></label> </td>';
		$html .= "</tr></table>";
		/*
		 * Métronidazole
		*/
		$html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
		$html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
		$html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > M&eacute;tronidazole </span></label></td>';
		$html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixMetronidazoleABGecbu" > </label></td>';
		$html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
		$html .= '      <select id="metronidazoleABGecbu" style="width: 120px;" disabled> ';
		$html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
		$html .= '      </select></label> </td>';
		$html .= "</tr></table>";
		 
		 
		/**
		 * Sulfamides --- Sulfamides
		 */
		$html .= '<table style="width: 100%; margin-top: 14px;">';
		$html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
		$html .= '  <td style="width: 100%; text-align: left; font-weight: bold; font-size: 12px; font-style: italic;"> &#10148; Sulfamides </td>';
		$html .= '</tr>';
		$html .= '</table>';
		/*
		 * Triméthoprime + Sulfamétoxazole (SXT)
		*/
		$html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
		$html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
		$html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" title="Trim&eacute;thoprime + Sulfam&eacute;toxazole (SXT)"> Trim&eacute;tho. + Sulfa. (SXT) </span></label></td>';
		$html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixTrimethoprimeSulfametoxazoleSXTABGecbu" > </label></td>';
		$html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
		$html .= '      <select id="trimethoprimeSulfametoxazoleSXTABGecbu" style="width: 120px;" disabled> ';
		$html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
		$html .= '      </select></label> </td>';
		$html .= "</tr></table>";
		 
		 
		 
		/*
		 * Fermeture Première colonne
		*/
		$html .= '  </td>';
		/*
		 * ==========================
		*/
		 
		   
		/* Séparateur des colonnes --- Séparateur des colonnes */
		$html .= '  <td style="width:  1.5%; height: 20px; background: indig;"> </td>';
		/*=====================================================*/
		  
		    
		/*
		 * Deuxième colonne
		*/
		$html .= '  <td style="width: 49.5%; height: 20px; background: yello; vertical-align: top;">';
		/*
		 * ====================
		*/
		 
		/**
		 * Polymyxines --- Polymyxines
		 */
		$html .= '<table style="width: 100%; margin-top: 15px;">';
		$html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
		$html .= '  <td style="width: 100%; text-align: left; font-weight: bold; font-size: 12px; font-style: italic;"> &#10148; Polymyxines </td>';
		$html .= '</tr>';
		$html .= '</table>';
		 
		/*
		 * Colistine CS
		*/
		$html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
		$html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
		$html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > Colistine CS </span></label></td>';
		$html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixColistineCSABGecbu" > </label></td>';
		$html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
		$html .= '      <select id="colistineCSABGecbu" style="width: 120px;" disabled> ';
		$html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
		$html .= '      </select></label> </td>';
		$html .= "</tr></table>";
		 
		/*
		 * Polymicine
		*/
		$html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
		$html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
		$html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > Polymicine </span></label></td>';
		$html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixPolymicineABGecbu" > </label></td>';
		$html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
		$html .= '      <select id="polymicineABGecbu" style="width: 120px;" disabled> ';
		$html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
		$html .= '      </select></label> </td>';
		$html .= "</tr></table>";
		 
		 
		/**
		 * Aminosides --- Aminosides
		 */
		$html .= '<table style="width: 100%; margin-top: 17px;">';
		$html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
		$html .= '  <td style="width: 100%; text-align: left; font-weight: bold; font-size: 12px; font-style: italic;"> &#10148; Aminosides </td>';
		$html .= '</tr>';
		$html .= '</table>';
		/*
		 * Kanamycine K
		*/
		$html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
		$html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
		$html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > Kanamycine K </span></label></td>';
		$html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixKanamycineKABGecbu" > </label></td>';
		$html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
		$html .= '      <select id="kanamycineKABGecbu" style="width: 120px;" disabled> ';
		$html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
		$html .= '      </select></label> </td>';
		$html .= "</tr></table>";
		 
		/*
		 * Tobramycine TB
		*/
		$html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
		$html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
		$html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > Tobramycine TB </span></label></td>';
		$html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixTobramycineTBABGecbu" > </label></td>';
		$html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
		$html .= '      <select id="tobramycineTBABGecbu" style="width: 120px;" disabled> ';
		$html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
		$html .= '      </select></label> </td>';
		$html .= "</tr></table>";
		 
		/*
		 * Gentamicine GM
		*/
		$html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
		$html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
		$html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > Gentamicine GM </span></label></td>';
		$html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixGentamicineGMABGecbu" > </label></td>';
		$html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
		$html .= '      <select id="gentamicineGMABGecbu" style="width: 120px;" disabled> ';
		$html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
		$html .= '      </select></label> </td>';
		$html .= "</tr></table>";
		 
		/*
		 * Amikacine AN
		*/
		$html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
		$html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
		$html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > Amikacine AN </span></label></td>';
		$html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixAmikacineANABGecbu" > </label></td>';
		$html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
		$html .= '      <select id="amikacineANABGecbu" style="width: 120px;" disabled> ';
		$html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
		$html .= '      </select></label> </td>';
		$html .= "</tr></table>";
		 
		/*
		 * Netilmycine
		*/
		$html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
		$html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
		$html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > Netilmycine </span></label></td>';
		$html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixNetilmycineABGecbu" > </label></td>';
		$html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
		$html .= '      <select id="netilmycineABGecbu" style="width: 120px;" disabled> ';
		$html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
		$html .= '      </select></label> </td>';
		$html .= "</tr></table>";
		 
		/**
		 * Phénicolés --- Phénicolés
		 */
		$html .= '<table style="width: 100%; margin-top: 17px;">';
		$html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
		$html .= '  <td style="width: 100%; text-align: left; font-weight: bold; font-size: 12px; font-style: italic;"> &#10148; Ph&eacute;nicol&eacute;s </td>';
		$html .= '</tr>';
		$html .= '</table>';
		/*
		 * Chloramphénicol C
		*/
		$html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
		$html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
		$html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > Chloramph&eacute;nicol C </span></label></td>';
		$html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixChloramphenicolCABGecbu" > </label></td>';
		$html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
		$html .= '      <select id="chloramphenicolCABGecbu" style="width: 120px;" disabled> ';
		$html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
		$html .= '      </select></label> </td>';
		$html .= "</tr></table>";
		 
		/**
		 * Cyclines --- Cyclines
		 */
		$html .= '<table style="width: 100%; margin-top: 17px;">';
		$html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
		$html .= '  <td style="width: 100%; text-align: left; font-weight: bold; font-size: 12px; font-style: italic;"> &#10148; Cyclines </td>';
		$html .= '</tr>';
		$html .= '</table>';
		/*
		 * Minocycline
		*/
		$html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
		$html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
		$html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > Minocycline </span></label></td>';
		$html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixMinocyclineABGecbu" > </label></td>';
		$html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
		$html .= '      <select id="minocyclineABGecbu" style="width: 120px;" disabled> ';
		$html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
		$html .= '      </select></label> </td>';
		$html .= "</tr></table>";
		 
		/*
		 * Tétracycline TE
		*/
		$html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
		$html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
		$html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > T&eacute;tracycline TE </span></label></td>';
		$html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixTetracyclineTEABGecbu" > </label></td>';
		$html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
		$html .= '      <select id="tetracyclineTEABGecbu" style="width: 120px;" disabled> ';
		$html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
		$html .= '      </select></label> </td>';
		$html .= "</tr></table>";
		 
		/*
		 * Doxycycline DO
		*/
		$html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
		$html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
		$html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > Doxycycline DO </span></label></td>';
		$html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixDoxycyclineDOABGecbu" > </label></td>';
		$html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
		$html .= '      <select id="doxycyclineDOABGecbu" style="width: 120px;" disabled> ';
		$html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
		$html .= '      </select></label> </td>';
		$html .= "</tr></table>";
		 
		/**
		 * Macrolides et apparentés --- Macrolides et apparentés
		 */
		$html .= '<table style="width: 100%; margin-top: 17px;">';
		$html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
		$html .= '  <td style="width: 100%; text-align: left; font-weight: bold; font-size: 12px; font-style: italic;"> &#10148; Macrolides et apparent&eacute;s </td>';
		$html .= '</tr>';
		$html .= '</table>';
		/*
		 * Azithromycine AZT
		*/
		$html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
		$html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
		$html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > Azithromycine AZT </span></label></td>';
		$html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixAzithromycineAZTABGecbu" > </label></td>';
		$html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
		$html .= '      <select id="azithromycineAZTABGecbu" style="width: 120px;" disabled> ';
		$html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
		$html .= '      </select></label> </td>';
		$html .= "</tr></table>";
		/*
		 * Erythromycine E
		*/
		$html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
		$html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
		$html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > Erythromycine E </span></label></td>';
		$html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixErythromycineEABGecbu" > </label></td>';
		$html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
		$html .= '      <select id="erythromycineEABGecbu" style="width: 120px;" disabled> ';
		$html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
		$html .= '      </select></label> </td>';
		$html .= "</tr></table>";
		/*
		 * Lincomycine L
		*/
		$html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
		$html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
		$html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > Lincomycine L </span></label></td>';
		$html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixLincomycineLABGecbu" > </label></td>';
		$html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
		$html .= '      <select id="lincomycineLABGecbu" style="width: 120px;" disabled> ';
		$html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
		$html .= '      </select></label> </td>';
		$html .= "</tr></table>";
		/*
		 * Pristinamycine PT
		*/
		$html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
		$html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
		$html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > Pristinamycine PT </span></label></td>';
		$html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixPristinamycinePTABGecbu" > </label></td>';
		$html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
		$html .= '      <select id="pristinamycinePTABGecbu" style="width: 120px;" disabled> ';
		$html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
		$html .= '      </select></label> </td>';
		$html .= "</tr></table>";
		 
		/**
		 * Fluoroquinolones --- Fluoroquinolones
		 */
		$html .= '<table style="width: 100%; margin-top: 17px;">';
		$html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
		$html .= '  <td style="width: 100%; text-align: left; font-weight: bold; font-size: 12px; font-style: italic;"> &#10148; Fluoroquinolones </td>';
		$html .= '</tr>';
		$html .= '</table>';
		 
		/*
		 * Acide nalidixique NA
		*/
		$html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
		$html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
		$html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > Acide nalidixique NA </span></label></td>';
		$html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixAcideNalidixiqueNAABGecbu" > </label></td>';
		$html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
		$html .= '      <select id="acideNalidixiqueNAABGecbu" style="width: 120px;" disabled> ';
		$html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
		$html .= '      </select></label> </td>';
		$html .= "</tr></table>";
		/*
		 * Péfloxacine PEF
		*/
		$html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
		$html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
		$html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > P&eacute;floxacine PEF </span></label></td>';
		$html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixPefloxacinePEFABGecbu" > </label></td>';
		$html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
		$html .= '      <select id="pefloxacinePEFABGecbu" style="width: 120px;" disabled> ';
		$html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
		$html .= '      </select></label> </td>';
		$html .= "</tr></table>";
		/*
		 * Norfloxacine NOR
		*/
		$html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
		$html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
		$html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > Norfloxacine NOR </span></label></td>';
		$html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixNorfloxacineNORABGecbu" > </label></td>';
		$html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
		$html .= '      <select id="norfloxacineNORABGecbu" style="width: 120px;" disabled> ';
		$html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
		$html .= '      </select></label> </td>';
		$html .= "</tr></table>";
		/*
		 * Ciprofloxacine CIP
		*/
		$html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
		$html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
		$html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > Ciprofloxacine CIP </span></label></td>';
		$html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixCiprofloxacineCIPABGecbu" > </label></td>';
		$html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
		$html .= '      <select id="ciprofloxacineCIPABGecbu" style="width: 120px;" disabled> ';
		$html .= '        <option value=-1 > </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
		$html .= '      </select></label> </td>';
		$html .= "</tr></table>";
		/*
		 * Lévofloxacine LEV
		*/
		$html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
		$html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
		$html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > L&eacute;vofloxacine LEV </span></label></td>';
		$html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixLEVABGecbu" > </label></td>';
		$html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
		$html .= '      <select id="LEVABGecbu" style="width: 120px;" disabled> ';
		$html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
		$html .= '      </select></label> </td>';
		$html .= "</tr></table>";
		 
		 
	
		/**
		 * Autres --- Autres
		 */
		$html .= '<table style="width: 100%; margin-top: 15px;">';
		$html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
		$html .= '  <td style="width: 100%; text-align: left; font-weight: bold; font-size: 12px; font-style: italic;"> &#10148; Autres </td>';
		$html .= '</tr>';
		$html .= '</table>';
	
		/*
		 * Fosfomycine FOS
		*/
		$html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
		$html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
		$html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > Fosfomycine FOS </span></label></td>';
		$html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixFosfomycineFOSABGecbu" > </label></td>';
		$html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
		$html .= '      <select id="fosfomycineFOSABGecbu" style="width: 120px;" disabled> ';
		$html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
		$html .= '      </select></label> </td>';
		$html .= "</tr></table>";
	
		 
		/*
		 * Acide fusidique FA
		*/
		$html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
		$html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
		$html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > Acide fusidique FA </span></label></td>';
		$html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixAcideFusidiqueFAABGecbu" > </label></td>';
		$html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
		$html .= '      <select id="acideFusidiqueFAABGecbu" style="width: 120px;" disabled> ';
		$html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
		$html .= '      </select></label> </td>';
		$html .= "</tr></table>";
		 
		 
		/*
		 * Rifampycine RIF - Rifampycine RIF
		*/
		$html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse">';
		$html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
		$html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > Rifampycine RIF </span></label></td>';
		$html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixRifampicineRAABGecbu" > </label></td>';
		$html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
		$html .= '      <select id="rifampicineRAABGecbu" style="width: 120px;" disabled> ';
		$html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
		$html .= '      </select></label> </td>';
		$html .= "</tr></table>";
		 
		 
		 
		/*
		 * Fermeture Deuxième colonne
		*/
		$html .= '  </td>';
		/*
		 * ==========================
		*/
	
		 
		$html .= '</tr>';
		 
		$html .= '</table>';
		/*
		 * ==================
		*/
		 
		 
		$html .= "</tr>";
		 
		$html .= "</table>";
		 
		 
		/**
		 * Partie commentaire --- Partie commentaire
		 * -----------------------------------------
		 * Partie commentaire --- Partie commentaire
		 */
	
		$html .= '<table style="width: 100%; margin-top: 15px;" class="designEnTeteAnalyse">';
		$html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
		if($this->layout()->user['role'] == 'biologiste' || $this->layout()->user['role'] == 'technicien'){
			$html .= '<td style="width: 100%;"><label class="labABGRadius" style="height: 140px;" ><span style="font-size: 16px; float: left;  margin-left: 30px;"> Conclusion :  </span> <textarea id="conclusion_ecbu_ABG" style="max-height: 100px; min-height: 100px; max-width: 630px; min-width: 630px; margin-left: 30px;" > </textarea> </label></td>';
		}else{
			$html .= '<td style="width: 100%;"><label class="labABGRadius" style="height: 140px;" ><span style="font-size: 16px; float: left;  margin-left: 30px;"> Conclusion :  </span> <textarea id="conclusion_ecbu_ABG" style="max-height: 100px; min-height: 100px; max-width: 630px; min-width: 630px; margin-left: 30px;" disabled> </textarea> </label></td>';
		}
		$html .= '<td style="width: 5%;"></td>';
		$html .= '</tr>';
		$html .= '</table>';
		 
		 
		 
		return $html;
	}
	
	
	/**
	 * analyse 67
	 */
	public function pus_67(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	    
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    $html .= "<tr class='ligneAnanlyse labelTypeMateriel' style='width: 100%; font-family: times new roman; font-size: 15px; margin-top: -45px;'>";
	    $html .= "  <td style='width: 55%;'> <label> Mat&eacute;riel utilis&eacute;</label> </td>";
	    $html .= "  <td colspan='2' style='width: 35%;'> </td>";
	    $html .= "</tr>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px;'>";
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_pus' tabindex='1' > </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> pus (En attente ... ) </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'>  </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	/**
	 * analyse 68
	 */
	public function typage_hemoglobine_68 (){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    $liste_select = "<option> </option>";
	    foreach($this->getAnalyseTable()->getTypagesHemoglobine() as $listeServices){
	    	$liste_select.= "<option value=".$listeServices['Idtypage'].">".$listeServices['Designation']."</option>";
	    }
	    
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    $html .= "<tr class='ligneAnanlyse labelTypeMateriel' style='width: 100%; font-family: times new roman; font-size: 15px; margin-top: -45px;'>";
	    $html .= "  <td style='width: 55%;'> <label> Mat&eacute;riel utilis&eacute;</label> </td>";
	    $html .= "  <td colspan='2' style='width: 35%;'> </td>";
	    $html .= "</tr>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px;'>";
	    $html .= "  <td style='width: 55%;'><div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_typage_hemoglobine' > </div></td>";
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> typage de l'h&eacute;moglobine  <select name='typage_hemoglobine' id='typage_hemoglobine' style='width: 130px;'> ".$liste_select." </select></span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'>  </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 90%; '> <div style='font-weight: bold; float: left' > Autre </div> <div style=' float: left' >  <select name='autre_typage_hemoglobine' id='autre_typage_hemoglobine' style='width: 120px;'> <option> </option> <option value='H-Barts' >H&eacute;mo-Bart's</option> </select> </div></label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}
	
	/**
	 * analyse 70
	 */
	public function ldh_70(){
		$html  = "<tr> <td align='center'>";
		$html .= "<table style='width: 100%;'>";
	
		//POUR LE NOM DU TYPE DE MATERIEL UTILISE
		$html .= "<tr class='ligneAnanlyse labelTypeMateriel' style='width: 100%; font-family: times new roman; font-size: 15px; margin-top: -45px;'>";
		$html .= "  <td style='width: 55%;'> <label> Mat&eacute;riel utilis&eacute;</label> </td>";
		$html .= "  <td colspan='2' style='width: 35%;'> </td>";
		$html .= "</tr>";
		$html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px;'>";
		$html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_ldh' > </div> </td>";
		$html .= "  <td colspan='2' style='width: 45%;'> </td>";
		$html .= "</tr>";
		//POUR LE NOM DU TYPE DE MATERIEL UTILISE
		 
		$html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
		$html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold; '> ldh <input id='valeur_ldh' type='number' step='any'> </span></label></td>";
		$html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> UI/l </label></td>";
		$html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> (324 - 1029)  </label></td>";
		$html .= "</tr>";
	
		$html .= "</table> </td> </tr>";
	
		return $html;
	}
	
	
	public function impressionResultatsAnalysesDemandeesAction()
	{
	    $service = $this->layout()->user['NomService'];
	    $iddemande = $this->params()->fromPost( 'iddemande' );
	    
	    $idpatient = $this->getPatientTable()->getDemandeAnalysesAvecIddemande($iddemande)['idpatient'];
	    $personne  = $this->getPersonneTable()->getPersonne($idpatient);
	    $depistage = $this->getPatientTable()->getDepistagePatient($idpatient);
	    
	    
	    //Recuperation de la liste des analyses pour lesquelles les résultats sont déjà renseignés
	    $listeResultats = $this->getResultatDemandeAnalyseTable()->getListeResultatsAnalysesDemandees($iddemande);
	    
	    $analysesDemandees = array();
	    $resultatsAnalysesDemandees = array();
	    $anteriorite_nfs = array();
	    for($j = 0 , $i = 0 ; $i < count($listeResultats) ; $i++ ){
	        $idanalyse = $listeResultats[$i]['idanalyse'];
	        $iddemande = $listeResultats[$i]['iddemande'];
	        
	        if($idanalyse == 1){ //NFS
	            $analysesDemandees  [$j++] = $listeResultats[$i];
	            $resultatsAnalysesDemandees[1] = $this->getResultatDemandeAnalyseTable()->getValeursNfs($iddemande);
	            
	            //Recupération des antériorites  ----- Récupération des antériorités
	            $analysesAvecResult = $this->getResultatDemandeAnalyseTable()->getListeAnalysesNFSDemandeesAyantResultats($idpatient, $iddemande);
	            
	            if($analysesAvecResult){ 
	                $anteriorite_nfs['demande']  = $analysesAvecResult[0];
	                $anteriorite_nfs['resultat'] = $this->getResultatDemandeAnalyseTable()->getValeursNfs($analysesAvecResult[0]['iddemande']); 
	            }
	        }
	        
	        elseif($idanalyse == 68){ //TYPAGE DE L'HEMOGLOBINE
	            $analysesDemandees  [$j++] = $listeResultats[$i];
	            $resultatsAnalysesDemandees[68] = $this->getResultatDemandeAnalyseTable()->getValeursTypageHemoglobine($iddemande);
	        }
	        
	    }
	    
	    //var_dump($analysesDemandees); exit();
	    //******************************************************
	    //******************************************************
	    //************** Création de l'imprimé pdf *************
	    //******************************************************
	    //******************************************************
	    //Créer le document
	    $DocPdf = new DocumentPdf();
	    //Créer la page
	    $page = new ResultatsAnalysesDemandeesPdf();
	
	    //Envoyer les données sur le patient
	    $page->setDonneesPatient($personne);
	    $page->setService($service);
	    $page->setAnalysesDemandees($analysesDemandees);
	    $page->setResultatsAnalysesDemandees($resultatsAnalysesDemandees);
	    $page->setDepistage($depistage);
	    $page->setAnterioriteNFS($anteriorite_nfs);
	    
	
	    //Ajouter une note à la page
	    $page->addNote();
	    //Ajouter la page au document
	    $DocPdf->addPage($page->getPage());
	    //Afficher le document contenant la page
	    $DocPdf->getDocument();
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function item_percentage($item, $total){
	
		if($total){
			
			$valeur = ($item * 100 / $total);
			if(fmod($valeur, 1) !== 0.00){$valeur = number_format($valeur, 2, ',', ' ');}
				
			return $valeur;
			
			//return number_format(($item * 100 / $total), 1, ',', ' ');
			
		}else{
			return 0;
		}
	
	}
	
	function item_percentage_virgule_unchiffre($item, $total){
	
		if($total){
			return number_format(($item * 100 / $total), 1, ',', ' ');
		}else{
			return 0;
		}
	
	}
	
	function pourcentage_element_tab($tableau, $total){
		$resultat = array();
	
		foreach ($tableau as $tab){
			$resultat [] = $this->item_percentage_virgule_unchiffre($tab, $total);
		}
	
		return $resultat;
	}

	public function infosStatistiquesResultatsDepistagesAction(){
	
		$listeResultatsDepistages = $this->getResultatsDepistagesTable()->getResultatsDepistages();
		$intervalleDate = $this->getResultatsDepistagesTable() ->getMinMaxDateResultatsDepistages();
		
		$tabAnnees = array();
		$tabDonneesAnnuelle = array();
		$tabMois = array();
		$tabProfils = array();
		$tabProfilsAnneesMois = array();
		
		$tabTotalColonneDesLignes = array();
		
		for($i=0 ; $i<count($listeResultatsDepistages) ; $i++){
		
			$annee_naissance = $listeResultatsDepistages[$i]['annee_prelevement'];
			if(!in_array($annee_naissance, $tabAnnees)){
				$tabAnnees[] = $annee_naissance;
				$tabDonneesAnnuelle[$annee_naissance] = array();
				$tabMois[$annee_naissance] = array();
			}
		
			$mois_naissance = $listeResultatsDepistages[$i]['mois_prelevement'];
			$tabDonneesAnnuelle[$annee_naissance][] = $mois_naissance;
		
			if(!in_array($mois_naissance, $tabMois[$annee_naissance])){
				$tabMois[$annee_naissance][] = $mois_naissance;
				$tabProfilsAnneesMois[$annee_naissance][$mois_naissance] = array();
			}
				
			$profil = $listeResultatsDepistages[$i]['profil'];
			$tabProfilsAnneesMois[$annee_naissance][$mois_naissance][] = $profil;
				
			if(!in_array($profil, $tabProfils)){
				$tabProfils[] = $profil;
			}
				
		}
		sort($tabProfils);
		sort($tabAnnees);
		
		$totalCol = array();
		
		
		
		$html = '<table class="titreTableauInfosStatistiques">
				   <tr class="ligneTitreTableauInfos">
				     <td style="width: 14%; height: 40px;">P&eacute;riodes</td>';
		
		$html .= '<script> var ListeProfilsDataAnyChartLine = new Array(); var ilisteProfilsAnyChart = 0; ListeProfilsDataAnyChartLine[ilisteProfilsAnyChart++]="#"; </script>';
		
		        if(count($tabProfils) == 0){ $largeur = 74; }else{ $largeur = 74/count($tabProfils); }
		
				for($iProf=0 ; $iProf<count($tabProfils) ;$iProf++){
					$html .='
				    <td style="width: '.$largeur.'%; height: 40px; text-align: center; color: green; font-family: times new roman; font-size: 18px; font-weight: bold; ">'.$tabProfils[$iProf].'</td>';
					
					$totalCol[$iProf] = 0;

					
					$html .= '<script> ListeProfilsDataAnyChartLine [ilisteProfilsAnyChart++] = "'.$tabProfils[$iProf].'"; </script>';
				}
				
				
		$html .='     <td style="width: 12%; height: 40px; text-align: center; font-family: Goudy Old Style; font-size: 20px; ">Total</td>
				   </tr>
				 </table>';
	
		
		$nombrePatientDepistes = 0;
		$kligne = 0;
		$indTabDataACL = 0;
		
		$html .="<script> var Pile = new Array(); var PileAnyChart = new Array();  var DataAnyChartLine = new Array();  </script>";
	
		$html .="<div id='listeTableauInfosStatistiques'>
		           <table class='tableauInfosStatistiques'>";
	
		
		for($i=0 ; $i<count($tabAnnees) ; $i++){
				
			$annee = $tabAnnees[$i];
			$tabDonneesAnnee = array_count_values($tabDonneesAnnuelle[$annee]);
			sort($tabMois[$annee]); //ordonne la liste des mois
			$tabIndexDonnees = $tabMois[$annee];
			
			
			for($ij=0 ; $ij<count($tabIndexDonnees) ; $ij++){
				$mois = $tabIndexDonnees[$ij];
				$listeProfils = array_count_values($tabProfilsAnneesMois[$annee][$mois]);
				
				/*-----------------*/
				$periodeMoisAnnee = $this->moisAbregesEnLettre($mois)." ".$annee;
				$html .="<script> DataAnyChartLine [".$indTabDataACL."] = new Array();  DataAnyChartLine [".$indTabDataACL."][0] = '".$periodeMoisAnnee."';  </script>";
				/*----------------*/
				
				if($ij==0){
					$html .='<tr style="width: 100%; " class="couleurLigne_'.$kligne.'">
				             <td class="infosPath periodeInfosLigne" style="width: 16%; height: 40px; padding-left: 15px; font-family: police2; font-size: 20px;">'. $this->moisEnLettre($mois).' '.$annee.' </td>';
					
					
					for($iProf=0 ; $iProf<count($tabProfils) ;$iProf++){
						
						$leProfil = $tabProfils[$iProf];
 						if(array_key_exists($leProfil, $listeProfils)){
 							
 							//Pourcentage pour chaque valeur
 							$pourValeur = $this->item_percentage($listeProfils[$leProfil], $tabDonneesAnnee[$mois]);
 							
 							$html .='<td class="infosPath" style="width: '.$largeur.'%; height: 40px; text-align: right; padding-right: 15px; font-family: Goudy Old Style; font-size: 21px; font-weight: normal; color: green;">'.$listeProfils[$leProfil].'<span class="valPourcentage" style="font-size: 13px; color: black;">  ('.$pourValeur.'%)</span><span class="voirPlusNumDossierSTAT voirPlusNumDossierSTAT_'.$i.''.$ij.''.$iProf.'" onclick="voirPlusNumeroDossierStatInfo('.$i.','.$ij.','.$iProf.','.$mois.','.$annee.',\''.$leProfil.'\',\''.$intervalleDate[0].'\',\''.$intervalleDate[1].'\');" style="position: relative; margin-right: -15px; cursor: pointer; color: red;">&#11206;</span></td>';
 							$totalCol[$iProf] += $listeProfils[$leProfil];
 							
 							/*-----------------*/
 							$html .="<script> DataAnyChartLine [".$indTabDataACL."][".($iProf+1)."] = ".$listeProfils[$leProfil]."; </script>";
 							/*----------------*/
 							
 						}else{
							$html .='<td class="infosPath" style="width: '.$largeur.'%; height: 40px; text-align: right; padding-right: 15px; font-family: Goudy Old Style; font-size: 21px; font-weight: normal;">0</td>';
							
							/*-----------------*/
							$html .="<script> DataAnyChartLine [".$indTabDataACL."][".($iProf+1)."] = 0; </script>";
							/*----------------*/
 						}

					}
				           
				    $html .='<td class="infosPath DepPourcentageTotalEnLigne_'.$kligne.'" style="width: 12%; height: 40px; text-align: right; padding-right: 15px; font-family: Goudy Old Style; font-size: 22px; font-weight: bold; border-left: 2.5px solid #cccccc;"><span class="valAbsolue">'.$tabDonneesAnnee[$mois].'</span><span class="valPourcentage" style="font-size: 13px;"></span></td>
				             </tr>';
				    
				    $tabTotalColonneDesLignes[] = $tabDonneesAnnee[$mois];
						
				}else{
					$html .='<tr style="width: 100%; " class="couleurLigne_'.$kligne.'">
				             <td class="infosPath periodeInfosLigne" style="width: 16%; height: 40px; padding-left: 15px; font-family: police2; font-size: 20px;">'. $this->moisEnLettre($mois).' '.$annee.' </td>';
				           
					
					for($iProf=0 ; $iProf<count($tabProfils) ;$iProf++){
					
 						$leProfil = $tabProfils[$iProf];
 						if(array_key_exists($leProfil, $listeProfils)){
 							
 							//Pourcentage pour chaque valeur
 							$pourValeur = $this->item_percentage($listeProfils[$leProfil], $tabDonneesAnnee[$mois]);
 							
 							$html .='<td class="infosPath" style="width: '.$largeur.'%; height: 40px; text-align: right; padding-right: 15px; font-family: Goudy Old Style; font-size: 21px; font-weight: normal; color: green;">'.$listeProfils[$leProfil].'<span class="valPourcentage" style="font-size: 13px; color: black;">  ('.$pourValeur.'%)</span><span class="voirPlusNumDossierSTAT voirPlusNumDossierSTAT_'.$i.''.$ij.''.$iProf.'" onclick="voirPlusNumeroDossierStatInfo('.$i.','.$ij.','.$iProf.','.$mois.','.$annee.',\''.$leProfil.'\',\''.$intervalleDate[0].'\',\''.$intervalleDate[1].'\');" style="position: relative; margin-right: -15px; cursor: pointer; color: red;">&#11206;</span></td>';
 							$totalCol[$iProf] += $listeProfils[$leProfil];
 							
 							/*-----------------*/
 							$html .="<script> DataAnyChartLine [".$indTabDataACL."][".($iProf+1)."] = ".$listeProfils[$leProfil]."; </script>";
 							/*----------------*/
 							
 						}else{
							$html .='<td class="infosPath" style="width: '.$largeur.'%; height: 40px; text-align: right; padding-right: 15px; font-family: Goudy Old Style; font-size: 21px; font-weight: normal;">0</td>';
							
							/*-----------------*/
							$html .="<script> DataAnyChartLine [".$indTabDataACL."][".($iProf+1)."] = 0; </script>";
							/*----------------*/
							
 						}
					
					}
				           
				    $html .='<td class="infosPath DepPourcentageTotalEnLigne_'.$kligne.'" style="width: 12%; height: 40px; text-align: right; padding-right: 15px; font-family: Goudy Old Style; font-size: 22px; font-weight: bold; border-left: 2.5px solid #cccccc;"><span class="valAbsolue">'.$tabDonneesAnnee[$mois].'</span><span class="valPourcentage" style="font-size: 13px;"></span></td>
				         </tr>';
	
				    $tabTotalColonneDesLignes[] = $tabDonneesAnnee[$mois];
				}
	
	
				if(($kligne%2)==0){
					$html .='<script>$(".couleurLigne_'.$kligne.'").css({"background":"#f9f9f9"}); </script>';
				}
	
				$kligne++;
				$indTabDataACL++;
				$nombrePatientDepistes += $tabDonneesAnnee[$mois];
			}
			
				
		}
	
		$html .="  </table>
                 </div>";
		 
	
		$grandTotal = 0;
		$piedTotal ='<td class="infosPath infosPathTotalDepiste" style="width: 16%; height: 40px; text-align: right; padding-right: 15px; font-family: Goudy Old Style; font-size: 21px; font-weight: bold;">Total <span></span></td>';
		for($i=0 ; $i<count($totalCol) ; $i++){
			$piedTotal .= '<td class="infosPath DepPourcentageTotalEnColonne_'.$i.'" style="width: '.$largeur.'%; height: 40px; text-align: right; padding-right: 15px; font-family: Goudy Old Style; font-size: 21px; font-weight: bold;">'.$totalCol[$i].'<span class="valPourcentage" style="font-size: 13px;"></span></td>';
			$grandTotal += $totalCol[$i];
			
			//Gestion des statistiques pour les profils
			$html .="<script> Pile.push({ y: ".$totalCol[$i]." , label: '".$tabProfils[$i]."' }); </script>";
			
			/*-----------------*/
			$html .="<script> var valeurCouple = ['".$tabProfils[$i]."','".$totalCol[$i]."']; </script>";
			$html .="<script> PileAnyChart.push(valeurCouple); </script>";
			/*----------------*/
			
		}
		
		$piedTotal .='<td class="infosPath " style="width: 12%; height: 40px; text-align: right; padding-right: 15px; font-family: times new roman; font-size: 20px; font-weight: bold; color: green; border-left: 2.5px solid #cccccc;">'.$grandTotal.'</td>';
		
		
		$control = new DateHelper();
		$html .="<script> $('#dateDebutPeriodeDiag div').html('".$control->convertDate($intervalleDate[0])."'); </script>";
		$html .="<script> $('#dateFinPeriodeDiag div').html('".$control->convertDate($intervalleDate[1])."'); </script>";
		$html .="<script> $('.champOP1 input, .champOP2 input').attr({'min':'".$intervalleDate[0]."', 'max':'".$intervalleDate[1]."'}); </script>";
		$html .="<script> $('.champOP1 input').val('".$intervalleDate[0]."'); </script>";
		$html .="<script> $('.champOP2 input').val('".$intervalleDate[1]."'); </script>";
		
		
		$html .="<script> var nbkligne = ".$kligne."; $('.tableauInfosTotalDepistage tr').html('".$piedTotal."'); </script>";
	
		
		
		//Ajouter des valeurs en pourcentage
		//Ajouter des valeurs en pourcentage
		/*
		 * Pourcentage dernière colonne
		 */
		for($i = 0 ; $i < $kligne ; $i++){
			$pourValeur = $this->item_percentage($tabTotalColonneDesLignes[$i], $grandTotal);
			$html .="<script> setTimeout(function(){ $('.DepPourcentageTotalEnLigne_".$i." .valPourcentage').html('  (".$pourValeur."%)'); }); </script>";
		}
		/*
		 * Pourcentage dernière ligne
		 */
		for($i=0 ; $i<count($totalCol) ; $i++){
			$pourValeur = $this->item_percentage($totalCol[$i], $grandTotal);
			$html .="<script> setTimeout(function(){ $('.DepPourcentageTotalEnColonne_".$i." .valPourcentage').html('  (".$pourValeur."%)'); }); </script>";
		}
		
		
		
		
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $html ) );
	}
	
	public function infosStatistiquesResultatsDepistagesOptionnellesAction(){
		$date_debut = $this->params ()->fromPost ( 'date_debut' );
		$date_fin = $this->params ()->fromPost ( 'date_fin' );
		$typeInfos = $this->params ()->fromPost ( 'typeInfos', 0 );
		
		if($typeInfos == 1){
			if($date_debut && $date_fin){
				$listeResultatsDepistages = $this->getResultatsDepistagesTable()->getResultatsDepistagesValidesPourUnePeriode($date_debut, $date_fin);
			}else{
				$listeResultatsDepistages = $this->getResultatsDepistagesTable()->getResultatsDepistagesValides();
			}
		}else if($typeInfos == 2){
			if($date_debut && $date_fin){
				$listeResultatsDepistages = $this->getResultatsDepistagesTable()->getResultatsDepistagesNonValidesPourUnePeriode($date_debut, $date_fin);
			}else{
				$listeResultatsDepistages = $this->getResultatsDepistagesTable()->getResultatsDepistagesNonValides();
			}
		}else{
			if($date_debut && $date_fin){
				$listeResultatsDepistages = $this->getResultatsDepistagesTable()->getResultatsDepistagesPourUnePeriode($date_debut, $date_fin);
			}else{
				$listeResultatsDepistages = $this->getResultatsDepistagesTable()->getResultatsDepistages();
			}
		}
	
		$tabAnnees = array();
		$tabDonneesAnnuelle = array();
		$tabMois = array();
		$tabProfils = array();
		$tabProfilsAnneesMois = array();
		
		$tabTotalColonneDesLignes = array();
		
		for($i=0 ; $i<count($listeResultatsDepistages) ; $i++){
	
			$annee_naissance = $listeResultatsDepistages[$i]['annee_prelevement'];
			if(!in_array($annee_naissance, $tabAnnees)){
				$tabAnnees[] = $annee_naissance;
				$tabDonneesAnnuelle[$annee_naissance] = array();
				$tabMois[$annee_naissance] = array();
			}
	
			$mois_naissance = $listeResultatsDepistages[$i]['mois_prelevement'];
			$tabDonneesAnnuelle[$annee_naissance][] = $mois_naissance;
	
			if(!in_array($mois_naissance, $tabMois[$annee_naissance])){
				$tabMois[$annee_naissance][] = $mois_naissance;
				$tabProfilsAnneesMois[$annee_naissance][$mois_naissance] = array();
			}
	
			$profil = $listeResultatsDepistages[$i]['profil'];
			$tabProfilsAnneesMois[$annee_naissance][$mois_naissance][] = $profil;
	
			if(!in_array($profil, $tabProfils)){
				$tabProfils[] = $profil;
			}
	
		}
		sort($tabProfils);
		sort($tabAnnees);
	
		$totalCol = array();
	
		$html = '<table class="titreTableauInfosStatistiques">
				   <tr class="ligneTitreTableauInfos">
				     <td style="width: 14%; height: 40px;">P&eacute;riodes</td>';

		$html .= '<script> var ListeProfilsDataAnyChartLine = new Array(); var ilisteProfilsAnyChart = 0; ListeProfilsDataAnyChartLine[ilisteProfilsAnyChart++]="#"; </script>';
		
		if(count($tabProfils) == 0){ $largeur = 74; }else{ $largeur = 74/count($tabProfils); }
		
		for($iProf=0 ; $iProf<count($tabProfils) ;$iProf++){
			$html .='
				    <td style="width: '.$largeur.'%; height: 40px; text-align: center; color: green; font-family: times new roman; font-size: 18px; font-weight: bold; ">'.$tabProfils[$iProf].'</td>';
				
			$totalCol[$iProf] = 0;
			
			$html .= '<script> ListeProfilsDataAnyChartLine [ilisteProfilsAnyChart++] = "'.$tabProfils[$iProf].'"; </script>';
			
		}
	
	
		$html .='     <td style="width: 12%; height: 40px; text-align: center; font-family: Goudy Old Style; font-size: 20px; ">Total</td>
				   </tr>
				 </table>';
	
	
		$nombrePatientDepistes = 0;
		$kligne = 0;
		$indTabDataACL = 0;
		
		$html .="<script> var Pile = new Array(); var PileAnyChart = new Array();  var DataAnyChartLine = new Array();  </script>";
		
		$html .="<div id='listeTableauInfosStatistiques'>
		           <table class='tableauInfosStatistiques'>";
	
		for($i=0 ; $i<count($tabAnnees) ; $i++){
	
			$annee = $tabAnnees[$i];
			$tabDonneesAnnee = array_count_values($tabDonneesAnnuelle[$annee]);
			sort($tabMois[$annee]); //ordonne la liste des mois
			$tabIndexDonnees = $tabMois[$annee];
				
				
			for($ij=0 ; $ij<count($tabIndexDonnees) ; $ij++){
				$mois = $tabIndexDonnees[$ij];
				$listeProfils = array_count_values($tabProfilsAnneesMois[$annee][$mois]);
	
				/*-----------------*/
				$periodeMoisAnnee = $this->moisAbregesEnLettre($mois)." ".$annee;
				$html .="<script> DataAnyChartLine [".$indTabDataACL."] = new Array();  DataAnyChartLine [".$indTabDataACL."][0] = '".$periodeMoisAnnee."';  </script>";
				/*----------------*/
				
				
				if($ij==0){
					$html .='<tr style="width: 100%; " class="couleurLigne_'.$kligne.'">
				             <td class="infosPath periodeInfosLigne" style="width: 16%; height: 40px; padding-left: 15px; font-family: police2; font-size: 20px;">'. $this->moisEnLettre($mois).' '.$annee.' </td>';
						
					for($iProf=0 ; $iProf<count($tabProfils) ;$iProf++){
	
						$leProfil = $tabProfils[$iProf];
						if(array_key_exists($leProfil, $listeProfils)){
							
							//Pourcentage pour chaque valeur
							$pourValeur = $this->item_percentage($listeProfils[$leProfil], $tabDonneesAnnee[$mois]);
							
							$html .='<td class="infosPath" style="width: '.$largeur.'%; height: 40px; text-align: right; padding-right: 15px; font-family: Goudy Old Style; font-size: 21px; font-weight: normal; color: green;">'.$listeProfils[$leProfil].'<span class="valPourcentage" style="font-size: 13px; color: black;">  ('.$pourValeur.'%)</span><span class="voirPlusNumDossierSTAT voirPlusNumDossierSTAT_'.$i.''.$ij.''.$iProf.'" onclick="voirPlusNumeroDossierStatInfo('.$i.','.$ij.','.$iProf.','.$mois.','.$annee.',\''.$leProfil.'\',\''.$date_debut.'\',\''.$date_fin.'\');" style="position: relative; margin-right: -15px; cursor: pointer; color: red;">&#11206;</span></td>';
							$totalCol[$iProf] += $listeProfils[$leProfil];
							
							/*-----------------*/
							$html .="<script> DataAnyChartLine [".$indTabDataACL."][".($iProf+1)."] = ".$listeProfils[$leProfil]."; </script>";
							/*----------------*/
							
						}else{
							$html .='<td class="infosPath" style="width: '.$largeur.'%; height: 40px; text-align: right; padding-right: 15px; font-family: Goudy Old Style; font-size: 21px; font-weight: normal;">0</td>';
						
							/*-----------------*/
							$html .="<script> DataAnyChartLine [".$indTabDataACL."][".($iProf+1)."] = 0; </script>";
							/*----------------*/
											
						}
	
					}

					$html .='<td class="infosPath DepPourcentageTotalEnLigne_'.$kligne.'" style="width: 12%; height: 40px; text-align: right; padding-right: 15px; font-family: Goudy Old Style; font-size: 22px; font-weight: bold; border-left: 2.5px solid #cccccc;"><span class="valAbsolue">'.$tabDonneesAnnee[$mois].'</span><span class="valPourcentage" style="font-size: 13px;"></span></td>
				             </tr>';
					
					$tabTotalColonneDesLignes[] = $tabDonneesAnnee[$mois];
	
				}else{
					$html .='<tr style="width: 100%; " class="couleurLigne_'.$kligne.'">
				             <td class="infosPath periodeInfosLigne" style="width: 16%; height: 40px; padding-left: 15px; font-family: police2; font-size: 20px;">'. $this->moisEnLettre($mois).' '.$annee.' </td>';
					 
					for($iProf=0 ; $iProf<count($tabProfils) ;$iProf++){
							
						$leProfil = $tabProfils[$iProf];
						if(array_key_exists($leProfil, $listeProfils)){
							
							//Pourcentage pour chaque valeur
							$pourValeur = $this->item_percentage($listeProfils[$leProfil], $tabDonneesAnnee[$mois]);
							
							$html .='<td class="infosPath" style="width: '.$largeur.'%; height: 40px; text-align: right; padding-right: 15px; font-family: Goudy Old Style; font-size: 21px; font-weight: normal; color: green;">'.$listeProfils[$leProfil].'<span class="valPourcentage" style="font-size: 13px; color: black;">  ('.$pourValeur.'%)</span><span class="voirPlusNumDossierSTAT voirPlusNumDossierSTAT_'.$i.''.$ij.''.$iProf.'" onclick="voirPlusNumeroDossierStatInfo('.$i.','.$ij.','.$iProf.','.$mois.','.$annee.',\''.$leProfil.'\',\''.$date_debut.'\',\''.$date_fin.'\');" style="position: relative; margin-right: -15px; cursor: pointer; color: red;">&#11206;</span></td>';
							
							$totalCol[$iProf] += $listeProfils[$leProfil];
							
							/*-----------------*/
							$html .="<script> DataAnyChartLine [".$indTabDataACL."][".($iProf+1)."] = ".$listeProfils[$leProfil]."; </script>";
							/*----------------*/
							
						}else{
							$html .='<td class="infosPath" style="width: '.$largeur.'%; height: 40px; text-align: right; padding-right: 15px; font-family: Goudy Old Style; font-size: 21px; font-weight: normal;">0</td>';
						
							/*-----------------*/
							$html .="<script> DataAnyChartLine [".$indTabDataACL."][".($iProf+1)."] = 0; </script>";
							/*----------------*/
						}
							
					}
					 
					$html .='<td class="infosPath DepPourcentageTotalEnLigne_'.$kligne.'" style="width: 12%; height: 40px; text-align: right; padding-right: 15px; font-family: Goudy Old Style; font-size: 22px; font-weight: bold; border-left: 2.5px solid #cccccc;"><span class="valAbsolue">'.$tabDonneesAnnee[$mois].'</span><span class="valPourcentage" style="font-size: 13px;"></span></td>
				         </tr>';
					
					$tabTotalColonneDesLignes[] = $tabDonneesAnnee[$mois];
				         
	
				}
	
	
				if(($kligne%2)==0){
					$html .='<script>$(".couleurLigne_'.$kligne.'").css({"background":"#f9f9f9"}); </script>';
				}
	
				$kligne++;
				$indTabDataACL++;
				$nombrePatientDepistes += $tabDonneesAnnee[$mois];
			}
				
	
		}
	
		$html .="  </table>
                 </div>";
			
	
		$grandTotal = 0;
		$piedTotal ='<td class="infosPath infosPathTotalDepiste" style="width: 16%; height: 40px; text-align: right; padding-right: 15px; font-family: Goudy Old Style; font-size: 21px; font-weight: bold;">Total <span></span></td>';
		for($i=0 ; $i<count($totalCol) ; $i++){
			//$piedTotal .= '<td class="infosPath " style="width: '.$largeur.'%; height: 40px; text-align: right; padding-right: 15px; font-family: Goudy Old Style; font-size: 21px; font-weight: bold;">'.$totalCol[$i].'</td>';
			$piedTotal .= '<td class="infosPath DepPourcentageTotalEnColonne_'.$i.'" style="width: '.$largeur.'%; height: 40px; text-align: right; padding-right: 15px; font-family: Goudy Old Style; font-size: 21px; font-weight: bold;">'.$totalCol[$i].'<span class="valPourcentage" style="font-size: 13px;"></span></td>';
			$grandTotal += $totalCol[$i];
			
			//Gestion des statistiques pour les profils
			$html .="<script> Pile.push({ y: ".$totalCol[$i]." , label: '".$tabProfils[$i]."' }); </script>";
			
			/*-----------------*/
			$html .="<script> var valeurCouple = ['".$tabProfils[$i]."','".$totalCol[$i]."']; </script>";
			$html .="<script> PileAnyChart.push(valeurCouple); </script>";
			/*----------------*/
			
		}
		$piedTotal .='<td class="infosPath " style="width: 12%; height: 40px; text-align: right; padding-right: 15px; font-family: times new roman; font-size: 20px; font-weight: bold; color: green; border-left: 2.5px solid #cccccc;">'.$grandTotal.'</td>';
	
	
		$control = new DateHelper();
		$html .="<script> $('#dateDebutPeriodeDiag div').html('".$control->convertDate($date_debut)."'); </script>";
		$html .="<script> $('#dateFinPeriodeDiag div').html('".$control->convertDate($date_fin)."'); </script>";
	
		if(!$date_debut || !$date_fin){
			$intervalleDate = $this->getResultatsDepistagesTable() ->getMinMaxDateResultatsDepistages();
			$html .="<script> setTimeout(function(){ $('.champOP1 input').val('".$intervalleDate[0]."'); },100); </script>";
			$html .="<script> setTimeout(function(){ $('.champOP2 input').val('".$intervalleDate[1]."'); },100); </script>";
		}
	
		$html .="<script> var nbkligne = ".$kligne."; $('.tableauInfosTotalDepistage tr').html('".$piedTotal."'); </script>";
	
		
		
		
		
		
		
		//Ajouter des valeurs en pourcentage
		//Ajouter des valeurs en pourcentage
		/*
		 * Pourcentage dernière colonne
		*/
		for($i = 0 ; $i < $kligne ; $i++){
			$pourValeur = $this->item_percentage($tabTotalColonneDesLignes[$i], $grandTotal);
			$html .="<script> setTimeout(function(){ $('.DepPourcentageTotalEnLigne_".$i." .valPourcentage').html('  (".$pourValeur."%)'); }); </script>";
		}
		/*
		 * Pourcentage dernière ligne
		*/
		for($i=0 ; $i<count($totalCol) ; $i++){
			$pourValeur = $this->item_percentage($totalCol[$i], $grandTotal);
			$html .="<script> setTimeout(function(){ $('.DepPourcentageTotalEnColonne_".$i." .valPourcentage').html('  (".$pourValeur."%)'); }); </script>";
		}
		
		
		
		
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $html ) );
	}
	
	
	
	

	public function infosStatistiquesParametreesAction($typeInfos, $date_debut=null, $date_fin=null)
	{
		if($typeInfos == 1){
			if($date_debut && $date_fin){
				$listeResultatsDepistages = $this->getResultatsDepistagesTable()->getResultatsDepistagesValidesPourUnePeriode($date_debut, $date_fin);
			}else{
				$listeResultatsDepistages = $this->getResultatsDepistagesTable()->getResultatsDepistagesValides();
			}
		}else if($typeInfos == 2){
			if($date_debut && $date_fin){
				$listeResultatsDepistages = $this->getResultatsDepistagesTable()->getResultatsDepistagesNonValidesPourUnePeriode($date_debut, $date_fin);
			}else{
				$listeResultatsDepistages = $this->getResultatsDepistagesTable()->getResultatsDepistagesNonValides();
			}
		}else{
			if($date_debut && $date_fin){
				$listeResultatsDepistages = $this->getResultatsDepistagesTable()->getResultatsDepistagesPourUnePeriode($date_debut, $date_fin);
			}else{
				$listeResultatsDepistages = $this->getResultatsDepistagesTable()->getResultatsDepistages();
			}
		}
	
		$tabAnnees = array();
		$tabDonneesAnnuelle = array();
		$tabMois = array();
		$tabProfils = array();
		$tabProfilsAnneesMois = array();
	
		for($i=0 ; $i<count($listeResultatsDepistages) ; $i++){
	
			$annee_naissance = $listeResultatsDepistages[$i]['annee_prelevement'];
			if(!in_array($annee_naissance, $tabAnnees)){
				$tabAnnees[] = $annee_naissance;
				$tabDonneesAnnuelle[$annee_naissance] = array();
				$tabMois[$annee_naissance] = array();
			}
	
			$mois_naissance = $listeResultatsDepistages[$i]['mois_prelevement'];
			$tabDonneesAnnuelle[$annee_naissance][] = $mois_naissance;
	
			if(!in_array($mois_naissance, $tabMois[$annee_naissance])){
				$tabMois[$annee_naissance][] = $mois_naissance;
				$tabProfilsAnneesMois[$annee_naissance][$mois_naissance] = array();
			}
	
			$profil = $listeResultatsDepistages[$i]['profil'];
			$tabProfilsAnneesMois[$annee_naissance][$mois_naissance][] = $profil;
	
			if(!in_array($profil, $tabProfils)){
				$tabProfils[] = $profil;
			}
	
		}
		sort($tabProfils);
		sort($tabAnnees);
	
		return array($tabAnnees, $tabDonneesAnnuelle, $tabMois, $tabProfils, $tabProfilsAnneesMois, count($listeResultatsDepistages));
	}
	
	
	
	//impression des informations statistiques
	//impression des informations statistiques
	public function imprimerInformationsStatistiquesDepistagesAction(){
		$user = $this->layout()->user;
		$nomService = $user['NomService'];
		$infosComp['dateDuJour'] = (new \DateTime ())->format( 'd/m/Y' );
			
		$date_debut = $this->params ()->fromPost (  'date_debut' );
		$date_fin = $this->params ()->fromPost (  'date_fin' );
		$typeInfos = $this->params ()->fromPost ( 'typeInfos', 0 );
		
		$periodePrelevement = array();
		$infosStatistique = array();
		
		if($date_debut && $date_fin){
			$infosStatistique = $this->infosStatistiquesParametreesAction($typeInfos, $date_debut, $date_fin);
			$periodePrelevement[] = $date_debut;
			$periodePrelevement[] = $date_fin;
		}else{
			$intervalleDate = $this->getResultatsDepistagesTable() ->getMinMaxDateResultatsDepistages();
			$date_debut = $intervalleDate[0];
			$date_fin = $intervalleDate[1];
			
			$periodePrelevement[] = $date_debut;
			$periodePrelevement[] = $date_fin;
			$infosStatistique = $this->infosStatistiquesParametreesAction($typeInfos, $date_debut, $date_fin);
		}
	
		$pdf = new infosStatistiquePdf('L','mm','A4');
		$pdf->SetMargins(13.5,13.5,13.5);
		$pdf->setTabInformations($infosStatistique);
			
		$pdf->setNomService($nomService);
		$pdf->setInfosComp($infosComp);
		$pdf->setPeriodePrelevement($periodePrelevement);
		$pdf->setTypeInfos($typeInfos);
			
		$pdf->ImpressionInfosStatistiques();
		$pdf->Output('I');
			
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	/**
	 * Liste des patients dépistés n'ayant pas de résultat (renseigné par un technicien)
	 * Liste des patients dépistés n'ayant pas de résultat (renseigné par un technicien)
	 * Liste des patients dépistés n'ayant pas de résultat (renseigné par un technicien)
	 */
	public function infosStatistiquesDepistagesNayantPasResultatAction(){
	
		$listeResultatsDepistages = $this->getResultatsDepistagesTable()->getPatientsDepistagesSansResultat();
		$intervalleDate = $this->getResultatsDepistagesTable() ->getMinMaxDateResultatsDepistages();
	
		$tabAnnees = array();
		$tabDonneesAnnuelle = array();
		$tabMois = array();
		$tabProfils = array();
		$tabProfilsAnneesMois = array();
		
		$tabNumDossier = array();
		$tabNumDossierAnneesMois = array();
	
		for($i=0 ; $i<count($listeResultatsDepistages) ; $i++){
	
			$annee_naissance = $listeResultatsDepistages[$i]['annee_prelevement'];
			if(!in_array($annee_naissance, $tabAnnees)){
				$tabAnnees[] = $annee_naissance;
				$tabDonneesAnnuelle[$annee_naissance] = array();
				$tabMois[$annee_naissance] = array();
			}
	
			$mois_naissance = $listeResultatsDepistages[$i]['mois_prelevement'];
			$tabDonneesAnnuelle[$annee_naissance][] = $mois_naissance;
	
			if(!in_array($mois_naissance, $tabMois[$annee_naissance])){
				$tabMois[$annee_naissance][] = $mois_naissance;
				$tabProfilsAnneesMois[$annee_naissance][$mois_naissance] = array();
				$tabNumDossierAnneesMois[$annee_naissance][$mois_naissance] = array();
			}
	
			$profil = $listeResultatsDepistages[$i]['profil'];
			$tabProfilsAnneesMois[$annee_naissance][$mois_naissance][] = $profil;
			
			$tabNumDossierAnneesMois[$annee_naissance][$mois_naissance][] = $listeResultatsDepistages[$i]['numero_dossier'];
	
			if(!in_array($profil, $tabProfils)){
				$tabProfils[] = $profil;
			}
	
			
		}
	
		$html = '<table class="titreTableauInfosStatistiquesDNPR">
				   <tr class="ligneTitreTableauInfosDNPR">
				     <td style="width: 30%; height: 40px;">P&eacute;riodes</td>';
		 
		$html .='<td style="width: 50%; height: 40px; text-align: center; color: black; font-family: times new roman; font-size: 18px; font-weight: bold; "> Num&eacute;ro dossier </td>';
		$html .='<td style="width: 20%; height: 40px; text-align: center; font-family: Goudy Old Style; font-size: 20px; ">Total</td>
				 </tr>
				 </table>';
	
	
		$nombrePatientDepistes = 0;
		$kligne = 0;
	
		$html .="<div id='listeTableauInfosStatistiquesDNPR'>
		         <table class='tableauInfosStatistiquesDNPR'>";
	
		sort($tabAnnees);
		for($i=0 ; $i<count($tabAnnees) ; $i++){
	
			$annee = $tabAnnees[$i];
			$tabDonneesAnnee = array_count_values($tabDonneesAnnuelle[$annee]);
			sort($tabMois[$annee]); //ordonne la liste des mois
			$tabIndexDonnees = $tabMois[$annee];
				
				
			for($ij=0 ; $ij<count($tabIndexDonnees) ; $ij++){
				$mois = $tabIndexDonnees[$ij];
				$listeProfils = array_count_values($tabProfilsAnneesMois[$annee][$mois]);
				
				$listeNumDossier = $tabNumDossierAnneesMois[$annee][$mois];
	
				if($ij==0){
					$html .='<tr style="width: 100%; " class="couleurLigne_'.$kligne.'">
				             <td class="infosPath periodeInfosLigne" style="width: 30%; height: 40px; padding-left: 15px; font-family: police2; font-size: 20px;">'. $this->moisEnLettre($mois).' '.$annee.' </td>';

					
					$listeNumDossierChaine = "";
					
				    for($iNumDossier=0 ; $iNumDossier<count($listeNumDossier) ;$iNumDossier++){
				    	
				    	$listeNumDossierChaine .= $listeNumDossier[$iNumDossier].",";
				    	if($iNumDossier+1 == count($listeNumDossier)){
				    		$voirPlus = '<span style="font-size: 25px; opacity: 0;">+</span>';
				    		if(count($listeNumDossier) > 1){ $voirPlus='<span class="voirPlusNumDossier_'.$i.''.$ij.'" style="margin-top: 22px;"></span> <span onclick="voirPlusNumeroDossier('.$i.','.$ij.',\''.$listeNumDossierChaine.'\');" style="position: relative; font-size: 25px; color: red; cursor: pointer; font-weight: bold; ">+</span>'; }
				    		$html .='<td class="infosPath" style="width: 50%; height: 40px; text-align: center; padding-right: 15px; font-family: Goudy Old Style; font-size: 21px; font-weight: normal; color: green;"> '.$listeNumDossier[0].' <span style="font-size: 13px; color: black; align: right;"> '.$voirPlus.' </span></td>';		
				    	}
					}
					 
					
					$html .='<td class="infosPath DepPourcentageTotalEnLigne_'.$kligne.'" style="width: 20%; height: 40px; text-align: right; padding-right: 15px; font-family: Goudy Old Style; font-size: 22px; font-weight: bold; border-left: 2.5px solid #cccccc;"><span>'.$tabDonneesAnnee[$mois].'</span></td>
				             </tr>';
	
	
				}else{
					$html .='<tr style="width: 100%; " class="couleurLigne_'.$kligne.'">
				             <td class="infosPath periodeInfosLigne" style="width: 30%; height: 40px; padding-left: 15px; font-family: police2; font-size: 20px;">'. $this->moisEnLettre($mois).' '.$annee.' </td>';
					 
					
					$listeNumDossierChaine = "";
					
				    for($iNumDossier=0 ; $iNumDossier<count($listeNumDossier) ;$iNumDossier++){
				    	
				    	$listeNumDossierChaine .= $listeNumDossier[$iNumDossier].",";
				    	if($iNumDossier+1 == count($listeNumDossier)){
				    		$voirPlus = '<span style="font-size: 25px; opacity: 0;">+</span>';
				    		if(count($listeNumDossier) > 1){ $voirPlus='<span class="voirPlusNumDossier_'.$i.''.$ij.'" style="margin-top: 22px;"></span> <span onclick="voirPlusNumeroDossier('.$i.','.$ij.',\''.$listeNumDossierChaine.'\');" style="position: relative; font-size: 25px; color: red; cursor: pointer; font-weight: bold; ">+</span>'; }
				    		$html .='<td class="infosPath" style="width: 50%; height: 40px; text-align: center; padding-right: 15px; font-family: Goudy Old Style; font-size: 21px; font-weight: normal; color: green;"> '.$listeNumDossier[0].' <span style="font-size: 13px; color: black; align: right;"> '.$voirPlus.' </span></td>';		
				    	}
					}
					 
					
					$html .='<td class="infosPath DepPourcentageTotalEnLigne_'.$kligne.'" style="width: 20%; height: 40px; text-align: right; padding-right: 15px; font-family: Goudy Old Style; font-size: 22px; font-weight: bold; border-left: 2.5px solid #cccccc;"><span>'.$tabDonneesAnnee[$mois].'</span></td>
				         </tr>';
	
				}
	
	
				if(($kligne%2)==0){
					$html .='<script>$(".tableauInfosStatistiquesDNPR .couleurLigne_'.$kligne.'").css({"background":"#f9f9f9"}); </script>';
				}
	
				$kligne++;
				$nombrePatientDepistes += $tabDonneesAnnee[$mois];
				
			}
				
	
		}
	
		$html .="</table>
                 </div>";

		
		$html .= '<table class="titreTableauInfosStatistiquesDNPR">
				   <tr class="ligneTitreTableauInfosDNPR">
				     <td style="width: 30%; height: 40px;"></td>';
			
		$html .='<td style="width: 50%; height: 40px; text-align: right; color: black; font-family: times new roman; font-size: 18px; font-weight: bold; "> Total </td>';
		$html .='<td style="width: 20%; height: 40px; text-align: right; font-family: Goudy Old Style; font-size: 22px; padding-right: 16px; font-weight: bold;">'.$nombrePatientDepistes.'</td>
				 </tr>
				 </table>';

		
		
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $html ) );
	}
	
	
	
	
	public function infosStatistiquesVoirPlusNumeroDossierAction(){
		$typeInfos = $this->params ()->fromPost ( 'typeInfos' );
		$date_debut = $this->params ()->fromPost ( 'date_debut' );
		$date_fin = $this->params ()->fromPost ( 'date_fin' );
		$profil = $this->params ()->fromPost ( 'profil' );
		$annee = $this->params ()->fromPost ( 'annee' );
		$mois = $this->params ()->fromPost ( 'mois' );
		
		$dateDebutChamp = $date_debut;
		$dateFinChamp = $date_fin;
		
		if($mois<10){ $mois = '0'.$mois; }
		$dernierJourMois = cal_days_in_month(CAL_GREGORIAN, $mois, $annee);
		$dateDebut = $annee.'-'.$mois.'-'.'01';
		$dateFin = $annee.'-'.$mois.'-'.$dernierJourMois;
		
		if($dateDebut < $dateDebutChamp){
			$jourDateDebutChamp = (int)substr($dateDebutChamp, 8, 2);
			$dateDebut = $annee.'-'.$mois.'-'.$jourDateDebutChamp;
		}
		
		if($dateFin > $dateFinChamp){
			$jourDateFinChamp = (int)substr($dateFinChamp, 8, 2);
			$dateFin = $annee.'-'.$mois.'-'.$jourDateFinChamp;
		}
		
		
		$listeNumeroDossier = $this->getResultatsDepistagesTable()->getListeNumeroDossierPatientsDepistagesAvecResultat($profil, $dateDebut, $dateFin, $typeInfos);
		
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $listeNumeroDossier ) );
	}


}
