<?php 
namespace Laboratoire\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Json\Json;
use Secretariat\Model\Personne;
use Laboratoire\View\Helper\DateHelper;
use Laboratoire\View\Helper\DocumentPdf;
use Laboratoire\View\Helper\AnalysesDemandeesPdf;
use Secretariat\View\Helper\ResultatsAnalysesDemandeesPdf;
use Secretariat\View\Helper\DocumentResultatsPdf;
use Secretariat\View\Helper\ResultatsNfsPdf;
use Secretariat\View\Helper\ResultatsTypageHemoglobinePdf;
use Laboratoire\View\Helper\ImprimerResultatsAnalysesDemandees;
use Consultation\View\Helper\OsmsMaster\src\Osms;

class BiologisteController extends AbstractActionController {
	protected $personneTable;
	protected $patientTable;
	protected $analyseTable;
	protected $resultatDemandeAnalyseTable;
	
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
			$this->patientTable = $sm->get ( 'Secretariat\Model\PatientTable' );
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

	public function getResultatDemandeAnalyseTable() {
	    if (! $this->resultatDemandeAnalyseTable) {
	        $sm = $this->getServiceLocator ();
	        $this->resultatDemandeAnalyseTable = $sm->get ( 'Laboratoire\Model\ResultatDemandeAnalyseTable' );
	    }
	    return $this->resultatDemandeAnalyseTable;
	}

	
/*****************************************************************************************************************************/
/*****************************************************************************************************************************/
/*****************************************************************************************************************************/
	public function baseUrl(){
		$baseUrl = $_SERVER['SCRIPT_FILENAME'];
		$tabURI  = explode('public', $baseUrl);
		return $tabURI[0];
	}
	
	public function chemin(){
		return $this->getServiceLocator()->get('Request')->getBasePath();
	}
	
	public function listeResultatsAnalysesAjaxAction() {
	
	    $output = $this->getPatientTable ()->getListeResultatsAnalysesPourValidation();
	    
	    return $this->getResponse ()->setContent ( Json::encode ( $output, array (
	        'enableJsonExprFinder' => true
	    ) ) );
	
	}
	
	public function listeResultatsAnalysesAction() {
	    $this->layout ()->setTemplate ( 'layout/biologiste' );
	    
	    return new ViewModel ( );
	}
	
	protected function nbJours($debut, $fin) {
	    //60 secondes X 60 minutes X 24 heures dans une journee
	    $nbSecondes = 60*60*24;
	
	    $debut_ts = strtotime($debut);
	    $fin_ts = strtotime($fin);
	    $diff = $fin_ts - $debut_ts;
	    return ($diff / $nbSecondes);
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
					$typage = "(<span style='color: red;'>".$typageHemoglobine['designation']."</span>)" ;
				}else{
					$typage = "(".$typageHemoglobine['designation'].")" ;
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
	       <table id='numero' style=' padding-top:5px; width: 60%; '>
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
					$typage = "(<span style='color: red;'>".$typageHemoglobine['designation']."</span>)" ;
				}else{
					$typage = "(".$typageHemoglobine['designation'].")" ;
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

	
	public function listeDemandesAnalysesNonValider($id)
	{
		
		$listeDemandesAnalyses = $this->getPatientTable ()->getResultatsDemandesAnalyses($id);
		$listeAnalysesDemandees = $this->getPatientTable ()->getResultatsAnalysesDemandees($id);
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
      					       <hass><img style='padding-left: 10px; cursor: pointer;' class='info_secretaire".$listeDemandes['iddemande']."' src='../images_icons/info_infirmier.png' title='envoyÃ© par: ".$infosSecretaire['prenom'].' '.$infosSecretaire['nom']."' /></hass>
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
 		      
 		      $html .="<div id='imprimerAnalyses' style='cursor: pointer; float:right; margin-top: -5px;'><span style='padding-right: 20px; margin-top: 20px; color: green; font-size: 17px; font-family: times new roman;'> ".$date." </span>";
 		      
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
		      	
 		        $existeResultat = $this->getResultatDemandeAnalyseTable()->getResultatDemandeAnalyseValider($listeAnalyses['iddemande']);
 		          
		      	$html .="<tr style='height:25px; width:100%; font-family: times new roman;'>
					       <td id='typeA' style='font-size: 14px;'> ".$listeAnalyses['Libelle']." </td>
					       <td id='analyseA' style='font-size: 14px;'> ".$listeAnalyses['Designation']." </td> ";
				$html .="<td id='optionA' style='font-size: 17px;'>";
				$html .="<div> 
				         <a href='javascript:resultatAnalyse(".$listeAnalyses['iddemande'].");' style='cursor: pointer;'> 
				             <img class='titre_resultat_".$listeAnalyses['iddemande']."'  src='../images_icons/resultat1.png' title='r&eacute;sultat' /> 
				         </a>";

				$html .="<a class='resultat_existe".$listeAnalyses['iddemande']."'  href='javascript:validerResultatAnalyse(".$listeAnalyses['iddemande'].")' style='margin-left: 10px;'>";
				$html .="<img  id='resultat_existe".$listeAnalyses['iddemande']."'  src='../images_icons/74bis.png' />"; 
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
				 $('img').tooltip({ animation: true, html: true, placement: 'bottom', show: { effect: 'slideDown', } });
				 listeDemandesAnalyses(); listeAnalysesDemandes();
				</script>";
		
		return $html;
	}
	
	public function getInformationsPatientAction()
	{
		$id = ( int ) $this->params ()->fromPost ( 'id', 0 );

		$iddemande = $this->getPatientTable()->getResultatsDemandesAnalyses($id)->current()['iddemande'];
		
		$html = $this->informationPatientAction($id);
		
		$html2 = $this->listeDemandesAnalysesNonValider($id);
		
		$html3 = array($html, $html2,  $iddemande);
		
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $html3 ) );
	}
	
	
	public function getListeAnalysesDemandeesAction()
	{
		$iddemande = ( int ) $this->params ()->fromPost ( 'iddemande', 0 );
	
		$listeAnalysesDemandees = $this->getPatientTable ()->getListeAnalysesDemandeesResultats($iddemande);
		$demande = $this->getPatientTable ()->getDemandeAnalysesAvecIddemande($iddemande);
	
		$controle = new DateHelper();
		$aujourdhui = (new \DateTime() ) ->format('d/m/Y');
		$date = $controle->convertDate( $demande['date'] );
		if($date == $aujourdhui){ $date = "aujourd'hui  - ".$demande['time']; } else { $date = $date.' - '.$demande['time']; }
	
		$total = $this->getPatientTable()->getMontantTotalAnalyses($iddemande);
	
		$diagnostic_demande = $this->getAnalyseTable()->getDiagnosticAnalyse($demande['idpatient'], $demande['date']);
		
		$html ="<div id='imprimerAnalyses' style='cursor: pointer; float:right; margin-top: -5px;'><span style='padding-right: 20px; margin-top: 20px; color: green; font-size: 17px; font-family: times new roman;'> ".$date." </span>";
		
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
			
			$html .="<a class='resultat_existe".$listeAnalyses['iddemande']."'  href='javascript:validerResultatAnalyse(".$listeAnalyses['iddemande'].")' style='margin-left: 10px;'>";
			$html .="<img  id='resultat_existe".$listeAnalyses['iddemande']."'  src='../images_icons/74bis.png' />"; 
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
	
		$html .="<script>
		         $('img').tooltip({ animation: true, html: true, placement: 'bottom', show: { effect: 'slideDown', } });
		         </script>";
		
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $html ) );
	}
	
	
	public function validerDemandeAction(){
		$iddemande = ( int ) $this->params ()->fromPost ( 'iddemande', 0 );
		$idemploye = $this->layout()->user['idemploye'];
		
		$demande = $this->getResultatDemandeAnalyseTable()->getDemandeAnalysesAvecIddemande($iddemande);
		$infoDepistage = array(0,0,0);
		$envoiSms = 0;
		if($demande){
			$depistage = $this->getPatientTable()->getDepistagePatient($demande['idpatient']);
			if($depistage->current()){
				if($demande['idanalyse'] == 68){
					$this->getPatientTable()->validerDepistagePatient($demande['idpatient'], $idemploye);
					
					if($depistage->current()['typepatient'] == 1){
						$infoPatient = $this->getPatientTable()->getPatient($demande['idpatient']);
						$codePatient = str_replace('E', 'I', $infoPatient->numero_dossier);
						
						if($codePatient){
							$this->getPatientTable()->updatePatientCodePatient($demande['idpatient'], $codePatient); 
						}else{
							$this->getPatientTable()->updatePatientCodePatient($demande['idpatient'], null); 
						}
						
						/*
						 * Envoi d'un alert sms
						*/
						$infoDepistage = $this->getPatientTable()->getNbPatientsDepistesExterneInterne($demande['idpatient']);
						$envoiSms = 1;
						//$this->envoiSmsAlert($infoDepistage);
					}
					
				}
					
			}
		}
		
		$this->getResultatDemandeAnalyseTable() ->validerResultDemande($iddemande, $idemploye);
		$nbAnalysesDemandees = $this->getPatientTable ()->getListeAnalysesDemandeesResultats($iddemande)->count();
		
		
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( array($envoiSms, $infoDepistage[0], $infoDepistage[1], $infoDepistage[2], $nbAnalysesDemandees) ) );
	}
	
	
	public function envoiSmsAlertAction(){
		
		$EffectifTotal   = ( int ) $this->params ()->fromPost ( 'EffectifTotal',   0 );
		$EffectifInterne = ( int ) $this->params ()->fromPost ( 'EffectifInterne', 0 );
		$typeProfil      =         $this->params ()->fromPost ( 'typeProfil', 0 );
		
		$credential = array(
				'clientId' => 'ianIQwqkI27VVUXajC9aDfB2qZG2FleL',
				'clientSecret' => 'pxMekLkQx5MRZ6cO'
		);
		$message = "Nouveau profil : ".$typeProfil." ; ".
				   "Patients dÃ©pistÃ©s : ".$EffectifTotal." ; ".
		           "Patients internes : ".$EffectifInterne;
		
		$osms = new Osms($credential);
		$token = $osms->getTokenFromConsumerKey();
		         $osms->sendSMS('tel:+221773139352',
		         		        'tel:+221773139352',$message,'SIMENS');
		         /*
		         $osms->sendSMS('tel:+221773139352',
		         		        'tel:+221775708807',$message,'SIMENS');
		         		        */
	}
	
	
	//PARTIE GESTION DES RESULTATS VALIDES PAR LE BIOLOGISTE
	//PARTIE GESTION DES RESULTATS VALIDES PAR LE BIOLOGISTE
	//PARTIE GESTION DES RESULTATS VALIDES PAR LE BIOLOGISTE
	//PARTIE GESTION DES RESULTATS VALIDES PAR LE BIOLOGISTE
	//PARTIE GESTION DES RESULTATS VALIDES PAR LE BIOLOGISTE
	//PARTIE GESTION DES RESULTATS VALIDES PAR LE BIOLOGISTE
	
	public function listeDemandesAnalysesValidees($idpatient)
	{
		$listeDemandesAnalyses = $this->getPatientTable ()->getResultatsDemandesAnalysesValidees($idpatient);
		$listeAnalysesDemandees = $this->getPatientTable ()->getResultatsAnalysesDemandeesValidees($idpatient);
		$i = 1;
		$iddemande = 0;
		$controle = new DateHelper();
		
		$html ='<table style="width: 100%;" >
 				    <tr style="width: 100%;" >
				        <td class="listeDemandeHauteurChangeModCons" style="width: 35%; height: 350px;" >';
		
		
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
      					       <hass><img style='padding-left: 10px; cursor: pointer;' class='info_secretaire".$listeDemandes['iddemande']."' src='../images_icons/info_infirmier.png' title='envoyÃ© par: ".$infosSecretaire['prenom'].' '.$infosSecretaire['nom']."' /></hass>
			  			   </td>
      					   <td id='datef' class='dateAffichee  dateAffichee_".$listeDemandes['iddemande']."' >". $date ."</td>
			  	           <td id='optionf'>";
		
			if($i == 1){
				$iddemande = $listeDemandes['iddemande'];
				$html .="<a class='iconeListeAffichee visualiser".$listeDemandes['iddemande']."' href='javascript:vueListeAnalysesValidees(".$listeDemandes['iddemande'].")' >";
				$html .="<img style='padding-left: 3px; ' src='../images_icons/transfert_droite2.png' /></a>"; $i = 0;
			}else {
				$html .="<a class='iconeListeAffichee visualiser".$listeDemandes['iddemande']."' href='javascript:vueListeAnalysesValidees(".$listeDemandes['iddemande'].")' >";
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
		
		
		
		$html .="<td style='width: 4%;' > <div class='barreVerticalSeparateurHauteurChangeModCons' style='width: 3px; height: 400px; background: #cccccc; margin-left: 15px;'></div> </td>";
		
		
		$html .="<td id='liste_analyses_demandes' style='width: 61%; height: 50px;' >";
		
		
		$demande = $this->getPatientTable ()->getDemandeAnalysesAvecIddemande($iddemande);
		$controle = new DateHelper();
		$aujourdhui = (new \DateTime() ) ->format('d/m/Y');
		$date = $controle->convertDate( $demande['date'] );
		if($date == $aujourdhui){ $date = "aujourd'hui  - ".$demande['time']; } else { $date = $date.' - '.$demande['time']; }
		
		$total = $this->getPatientTable()->getMontantTotalAnalyses($iddemande);
		
		$diagnostic_demande = $this->getAnalyseTable()->getDiagnosticAnalyse($demande['idpatient'], $demande['date']);
		 
		$html .="<div id='imprimerAnalyses' style='cursor: pointer; float:right; margin-top: -5px;'><span style='padding-right: 20px; margin-top: 20px; color: green; font-size: 17px; font-family: times new roman;'> ".$date." </span>";
		 
		if($diagnostic_demande){
			$text = str_replace("'", "\'", $diagnostic_demande['diagnostic_demande']);
			$html .="<span> <a href='javascript:diagnostic(".$idpatient.")' > <img id='diagnostic_".$idpatient."'  style='padding-left: 3px; padding-right: 7px;' src='../images_icons/detailsd.png' /> </a> </span>";
			$html .="<script> $('#diagnostic_demande_text').val('".preg_replace("/(\r\n|\n|\r)/", " ", $text)."'); </script>";
		}
		
		$html .="<a href='javascript:resultatsDesAnalyses(".$iddemande.");'> <img style='padding-left: 3px; ' src='../images_icons/resultat2.png' title='Resultats' /> </a> <a href='javascript:imprimerResultatsAnalysesDemandees(".$iddemande.");'> <hass> <img style='padding-left: 3px; ' src='../images_icons/pdf.png' title='Imprimer' /> </hass> </a> </div>";
		
		
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
			 
			$existeResultat = $this->getResultatDemandeAnalyseTable()->getResultatDemandeAnalyseValider($listeAnalyses['iddemande']);
			 
			$html .="<tr style='height:25px; width:100%; font-family: times new roman;'>
					       <td id='typeA' style='font-size: 14px;'> ".$listeAnalyses['Libelle']." </td>
					       <td id='analyseA' style='font-size: 14px;'> ".$listeAnalyses['Designation']." </td> ";
			$html .="<td id='optionA' style='font-size: 17px;'>";
			$html .="<div>
				         <a href='javascript:resultatAnalyse(".$listeAnalyses['iddemande'].");' style='cursor: pointer;'>
				             <img class='titre_resultat_".$listeAnalyses['iddemande']."'  src='../images_icons/resultat1.png' title='r&eacute;sultat' />
				         </a>";
		
			$html .="<a class='resultat_existe".$listeAnalyses['iddemande']."'  href='javascript:validerResultatAnalyse(".$listeAnalyses['iddemande'].")' style='margin-left: 10px;'>";
			$html .="<img  id='resultat_existe".$listeAnalyses['iddemande']."'  src='../images_icons/tick_16.png' />";
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
				 $('img').tooltip({ animation: true, html: true, placement: 'bottom', show: { effect: 'slideDown', } });
				 listeDemandesAnalyses(); listeAnalysesDemandes();
				</script>";
		
		return $html;
		
	}
	
	

	public function getListeAnalysesDemandeesValideesAction()
	{
		$iddemande = ( int ) $this->params ()->fromPost ( 'iddemande', 0 );
	
		$listeAnalysesDemandees = $this->getPatientTable ()->getListeResultatsAnalysesDemandeesValidees($iddemande);
		$demande = $this->getPatientTable ()->getDemandeAnalysesAvecIddemande($iddemande);
	
		$controle = new DateHelper();
		$aujourdhui = (new \DateTime() ) ->format('d/m/Y');
		$date = $controle->convertDate( $demande['date'] );
		if($date == $aujourdhui){ $date = "aujourd'hui  - ".$demande['time']; } else { $date = $date.' - '.$demande['time']; }
	
		$total = $this->getPatientTable()->getMontantTotalAnalyses($iddemande);
	
		$diagnostic_demande = $this->getAnalyseTable()->getDiagnosticAnalyse($demande['idpatient'], $demande['date']);
	
		$html ="<div id='imprimerAnalyses' style='cursor: pointer; float:right; margin-top: -5px;'><span style='padding-right: 20px; margin-top: 20px; color: green; font-size: 17px; font-family: times new roman;'> ".$date." </span>";
	
		if($diagnostic_demande){
			$text = str_replace("'", "\'", $diagnostic_demande['diagnostic_demande']);
			$html .="<span> <a href='javascript:diagnostic(".$iddemande.")' > <img id='diagnostic_".$iddemande."'  style='padding-left: 3px; padding-right: 7px;' src='../images_icons/detailsd.png' /> </a> </span>";
			$html .="<script> $('#diagnostic_demande_text').val('".preg_replace("/(\r\n|\n|\r)/", " ", $text)."'); </script>";
		}
	
		$html .="<a href='javascript:resultatsDesAnalyses(".$iddemande.");'> <img style='padding-left: 3px; ' src='../images_icons/resultat2.png' title='Resultats' /> </a> <a href='javascript:imprimerResultatsAnalysesDemandees(".$iddemande.");'> <hass> <img style='padding-left: 3px; ' src='../images_icons/pdf.png' title='Imprimer' /> </hass> </a> </div>";
	
	
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
				
			$html .="<a class='resultat_existe".$listeAnalyses['iddemande']."'  href='javascript:validerResultatAnalyse(".$listeAnalyses['iddemande'].")' style='margin-left: 10px;'>";
			$html .="<img  id='resultat_existe".$listeAnalyses['iddemande']."'  src='../images_icons/tick_16.png' />";
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
	
		$html .="<script>
		         $('img').tooltip({ animation: true, html: true, placement: 'bottom', show: { effect: 'slideDown', } });
		         </script>";
	
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $html ) );
	}
	
	
	
	public function getInformationsResultatsAnalysesValideesPatientAction()
	{
		$idpatient = ( int ) $this->params ()->fromPost ( 'idpatient', 0 );
	
		$iddemande = $this->getPatientTable()->getResultatsDemandesAnalysesValidees($idpatient)->current()['iddemande'];
	
		$html = $this->informationPatientAction($idpatient);
	
		$html2 = $this->listeDemandesAnalysesValidees($idpatient);
	
		$html3 = array($html, $html2,  $iddemande);
	
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $html3 ) );
	}
	
	
	public function listeResultatsValidesAjaxAction() {
	
		$output = $this->getPatientTable ()->getListeResultatsAnalysesValidees();
		return $this->getResponse ()->setContent ( Json::encode ( $output, array (
				'enableJsonExprFinder' => true
		) ) );
	
	}
	
	public function listeResultatsValidesAction() {
		
		$this->layout ()->setTemplate ( 'layout/biologiste' );
		return new ViewModel ( );
		
	}
	
	
	public function retirerValidationAction() {
		$iddemande = ( int ) $this->params ()->fromPost ( 'iddemande', 0 );
		$idemploye = $this->layout()->user['idemploye'];
		

		$demande = $this->getResultatDemandeAnalyseTable()->getDemandeAnalysesAvecIddemande($iddemande);
		if($demande){
			$depistage = $this->getPatientTable()->getDepistagePatient($demande['idpatient']);
			if($depistage->current()){
				if($demande['idanalyse'] == 68){
					$this->getPatientTable()->retrirerValidationDepistagePatient($demande['idpatient'], $idemploye);
						
					if($depistage->current()['typepatient'] == 1){
						$infoPatient = $this->getPatientTable()->getPatient($demande['idpatient']);
						$codePatient = str_replace('I', 'E', $infoPatient->numero_dossier);
		
						if($codePatient){
							$this->getPatientTable()->updatePatientCodePatient($demande['idpatient'], $codePatient);
						}else{
							$this->getPatientTable()->updatePatientCodePatient($demande['idpatient'], null);
						}
					}
						
				}
					
			}
		}
		
		
		$this->getResultatDemandeAnalyseTable() ->retirerValidationResultDemande($iddemande, $idemploye);
		$nbAnalysesDemandees = $this->getPatientTable ()->getListeResultatsAnalysesDemandeesValidees($iddemande)->count();
		
		
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $nbAnalysesDemandees ) );
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	//****** =========== RECUPERER L'ANALYSE =========== *******
	//****** =========== RECUPERER L'ANALYSE =========== *******
	//****** =========== RECUPERER L'ANALYSE =========== *******
	protected function getResultatsNfs($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursNfs($iddemande);
	    $html ="";
	    if($resultat){
	        for($i = 1 ; $i<=25 ; $i++){
	            $html .= "<script> $('#champ".$i."').val('".$resultat['champ'.$i]."'); </script>";
	        }
	        $html .= "<script> $('#type_materiel_nfs').val('".str_replace( "'", "\'",$resultat['type_materiel'] )."'); </script>";
	        $html .= "<script> $('#commentaire_hemogramme').val('".str_replace( "'", "\'", $resultat['commentaire'] )."'); </script>";
	    
	        $html .=
	        "<script>
   	   	        $('.iconeValidationInterfaceVisual_1').html('<span  class=\'resultat_existe_interface_visual_".$iddemande."\' > <img id=\'resultat_existe_interface_visual_".$iddemande."\' onclick=\'validerResultatAnalyseInterfaceVisual(".$iddemande.");\' style=\'float: right; padding-right: 10px; cursor: pointer;\' src=\'../images_icons/74bis.png\' > </span>');
	        </script>";
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
			
			$html .=
			"<script>
   	   	        $('.iconeValidationInterfaceVisual_71').html('<span  class=\'resultat_existe_interface_visual_".$iddemande."\' > <img id=\'resultat_existe_interface_visual_".$iddemande."\' onclick=\'validerResultatAnalyseInterfaceVisual(".$iddemande.");\' style=\'float: right; padding-right: 10px; cursor: pointer;\' src=\'../images_icons/74bis.png\' > </span>');
	        </script>";
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
	            		
   	   	        $('.iconeValidationInterfaceVisual_2').html('<span  class=\'resultat_existe_interface_visual_".$iddemande."\' > <img id=\'resultat_existe_interface_visual_".$iddemande."\' onclick=\'validerResultatAnalyseInterfaceVisual(".$iddemande.");\' style=\'float: right; padding-right: 10px; cursor: pointer;\' src=\'../images_icons/74bis.png\' > </span>');
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
	            		
  	   	        $('.iconeValidationInterfaceVisual_3').html('<span  class=\'resultat_existe_interface_visual_".$iddemande."\' > <img id=\'resultat_existe_interface_visual_".$iddemande."\' onclick=\'validerResultatAnalyseInterfaceVisual(".$iddemande.");\' style=\'float: right; padding-right: 10px; cursor: pointer;\' src=\'../images_icons/74bis.png\' > </span>');
	        </script>";
		}
		return $html;
	}
	
	protected function getResultatsTestCombsDirect($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursTestCombsDirect($iddemande);
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script>
	        	$('#type_materiel_test_combs_direct').val('".str_replace( "'", "\'",$resultat['type_materiel'])."');	
	            $('#test_combs_direct').val('".$resultat['valeur']."');
	            if('".$resultat['valeur']."' == 'Positif'){ 
	                setTimeout(function(){ 
	                   $('.titre_combs_direct').toggle(true); 
	                   $('#titre_combs_direct').val('".$resultat['titre']."'); 
	                }); 
	            }
	                   		
  	   	        $('.iconeValidationInterfaceVisual_4').html('<span  class=\'resultat_existe_interface_visual_".$iddemande."\' > <img id=\'resultat_existe_interface_visual_".$iddemande."\' onclick=\'validerResultatAnalyseInterfaceVisual(".$iddemande.");\' style=\'float: right; padding-right: 10px; cursor: pointer;\' src=\'../images_icons/74bis.png\' > </span>');
	        </script>";
	    }
	    return $html;
	}
	
	protected function getResultatsTestCombsIndirect($iddemande){
		$resultat = $this->getResultatDemandeAnalyseTable()->getValeursTestCombsIndirect($iddemande);
		$html ="<script>$('#test_combs_indirect_1').attr('onchange', 'getTestCombsIndirectBlocTitre(1)'); </script>";
		
		if($resultat){
			$html .="<script> var time = 0; setTimeout(function(){ $('#type_materiel_test_combs_indirect').val('".str_replace( "'", "\'", $resultat[0]['type_materiel'])."').attr('disabled',true); },50); </script>";
		
			for($i = 0 ; $i < count($resultat) ; $i++){
				if($i > 0){
					$html .= "<script> time = time+2; setTimeout(function(){ $('#test_combs_indirect_plus').trigger('click'); }, time); </script>";
				}
				 
				$html .=
				"<script>
	              setTimeout(function(){
	                $('#test_combs_indirect_".($i+1)."').val('".$resultat[$i]['valeur']."').attr('disabled',true);
	                $('#titre_combs_indirect_".($i+1)."').val('".$resultat[$i]['titre']."').attr('disabled',true);
              		$('#titre_combs_temperature_".($i+1)."').val('".$resultat[$i]['temperature']."').attr('disabled',true);
	              }, time);
	    	    </script>";
				 
			}
			$html .="<script> setTimeout(function(){ $('#commentaire_test_combs_indirect').val('".str_replace( "'", "\'", $resultat[0]['commentaire'])."'); $('#test_combs_indirect_mp').toggle(false); },10); </script>";
			 
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
	            		
   	   	        $('.iconeValidationInterfaceVisual_8').html('<span  class=\'resultat_existe_interface_visual_".$iddemande."\' > <img id=\'resultat_existe_interface_visual_".$iddemande."\' onclick=\'validerResultatAnalyseInterfaceVisual(".$iddemande.");\' style=\'float: right; padding-right: 10px; cursor: pointer;\' src=\'../images_icons/74bis.png\' > </span>');
	        </script>";
		}
		return $html;
	}
	
	protected function getResultatsTestCompatibilite($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursTestCompatibilite($iddemande);
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script>
	        	$('#type_materiel_test_compatibilite').val('".str_replace( "'", "\'",$resultat['type_materiel'])."');
	            $('#test_compatibilite').val('".$resultat['valeur']."');
	            if('".$resultat['valeur']."' == 'Compatible'){ 
	              setTimeout(function(){ 
	                 $('.titre_test_compatibilite').toggle(true); 
	                 $('#titre_test_compatibilite').val('".$resultat['poche']."'); 
	              }); 
	            }
	                 		
     	        $('.iconeValidationInterfaceVisual_6').html('<span  class=\'resultat_existe_interface_visual_".$iddemande."\' > <img id=\'resultat_existe_interface_visual_".$iddemande."\' onclick=\'validerResultatAnalyseInterfaceVisual(".$iddemande.");\' style=\'float: right; padding-right: 10px; cursor: pointer;\' src=\'../images_icons/74bis.png\' > </span>');
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

       	        $('.iconeValidationInterfaceVisual_7').html('<span  class=\'resultat_existe_interface_visual_".$iddemande."\' > <img id=\'resultat_existe_interface_visual_".$iddemande."\' onclick=\'validerResultatAnalyseInterfaceVisual(".$iddemande.");\' style=\'float: right; padding-right: 10px; cursor: pointer;\' src=\'../images_icons/74bis.png\' > </span>');
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
	            		
       	        $('.iconeValidationInterfaceVisual_9').html('<span  class=\'resultat_existe_interface_visual_".$iddemande."\' > <img id=\'resultat_existe_interface_visual_".$iddemande."\' onclick=\'validerResultatAnalyseInterfaceVisual(".$iddemande.");\' style=\'float: right; padding-right: 10px; cursor: pointer;\' src=\'../images_icons/74bis.png\' > </span>');
	        </script>";
	    }
	    return $html;
	}
	
	protected function getResultatsGoutteEpaisse($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursGoutteEpaisse($iddemande);
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script>
 	        	$('#type_materiel_goutte_epaisse').val('".str_replace( "'", "\'",$resultat['type_materiel'])."');
	            $('#goutte_epaisse').val('".$resultat['goutte_epaisse']."');
	            if('".$resultat['goutte_epaisse']."' == 'Positif'){ $('#goutte_epaisse_positif').toggle(true); $('#densite_parasitaire').val('".$resultat['densite_parasitaire']."'); }
	            $('.ER_".$iddemande." #commentaire_goutte_epaisse').val('".str_replace( "'", "\'",$resultat['commentaire_goutte_epaisse'])."');		
	        
      	        $('.iconeValidationInterfaceVisual_10').html('<span  class=\'resultat_existe_interface_visual_".$iddemande."\' > <img id=\'resultat_existe_interface_visual_".$iddemande."\' onclick=\'validerResultatAnalyseInterfaceVisual(".$iddemande.");\' style=\'float: right; padding-right: 10px; cursor: pointer;\' src=\'../images_icons/74bis.png\' > </span>');
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
	            		
      	        $('.iconeValidationInterfaceVisual_14').html('<span  class=\'resultat_existe_interface_visual_".$iddemande."\' > <img id=\'resultat_existe_interface_visual_".$iddemande."\' onclick=\'validerResultatAnalyseInterfaceVisual(".$iddemande.");\' style=\'float: right; padding-right: 10px; cursor: pointer;\' src=\'../images_icons/74bis.png\' > </span>');
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
	            		
      	        $('.iconeValidationInterfaceVisual_15').html('<span  class=\'resultat_existe_interface_visual_".$iddemande."\' > <img id=\'resultat_existe_interface_visual_".$iddemande."\' onclick=\'validerResultatAnalyseInterfaceVisual(".$iddemande.");\' style=\'float: right; padding-right: 10px; cursor: pointer;\' src=\'../images_icons/74bis.png\' > </span>');
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
	            		
      	        $('.iconeValidationInterfaceVisual_16').html('<span  class=\'resultat_existe_interface_visual_".$iddemande."\' > <img id=\'resultat_existe_interface_visual_".$iddemande."\' onclick=\'validerResultatAnalyseInterfaceVisual(".$iddemande.");\' style=\'float: right; padding-right: 10px; cursor: pointer;\' src=\'../images_icons/74bis.png\' > </span>');
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
	            		
     	        $('.iconeValidationInterfaceVisual_17').html('<span  class=\'resultat_existe_interface_visual_".$iddemande."\' > <img id=\'resultat_existe_interface_visual_".$iddemande."\' onclick=\'validerResultatAnalyseInterfaceVisual(".$iddemande.");\' style=\'float: right; padding-right: 10px; cursor: pointer;\' src=\'../images_icons/74bis.png\' > </span>');
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
	    	    		
    	        $('.iconeValidationInterfaceVisual_18').html('<span  class=\'resultat_existe_interface_visual_".$iddemande."\' > <img id=\'resultat_existe_interface_visual_".$iddemande."\' onclick=\'validerResultatAnalyseInterfaceVisual(".$iddemande.");\' style=\'float: right; padding-right: 10px; cursor: pointer;\' src=\'../images_icons/74bis.png\' > </span>');
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
	    	    		
    	        $('.iconeValidationInterfaceVisual_19').html('<span  class=\'resultat_existe_interface_visual_".$iddemande."\' > <img id=\'resultat_existe_interface_visual_".$iddemande."\' onclick=\'validerResultatAnalyseInterfaceVisual(".$iddemande.");\' style=\'float: right; padding-right: 10px; cursor: pointer;\' src=\'../images_icons/74bis.png\' > </span>');
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
	    	    		
	    	    $('.iconeValidationInterfaceVisual_20').html('<span  class=\'resultat_existe_interface_visual_".$iddemande."\' > <img id=\'resultat_existe_interface_visual_".$iddemande."\' onclick=\'validerResultatAnalyseInterfaceVisual(".$iddemande.");\' style=\'float: right; padding-right: 10px; cursor: pointer;\' src=\'../images_icons/74bis.png\' > </span>');
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
     	        		
     	        $('.iconeValidationInterfaceVisual_21').html('<span  class=\'resultat_existe_interface_visual_".$iddemande."\' > <img id=\'resultat_existe_interface_visual_".$iddemande."\' onclick=\'validerResultatAnalyseInterfaceVisual(".$iddemande.");\' style=\'float: right; padding-right: 10px; cursor: pointer;\' src=\'../images_icons/74bis.png\' > </span>');
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
	            		
	   	        $('.iconeValidationInterfaceVisual_22').html('<span  class=\'resultat_existe_interface_visual_".$iddemande."\' > <img id=\'resultat_existe_interface_visual_".$iddemande."\' onclick=\'validerResultatAnalyseInterfaceVisual(".$iddemande.");\' style=\'float: right; padding-right: 10px; cursor: pointer;\' src=\'../images_icons/74bis.png\' > </span>');
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
	            		
	   	        $('.iconeValidationInterfaceVisual_23').html('<span  class=\'resultat_existe_interface_visual_".$iddemande."\' > <img id=\'resultat_existe_interface_visual_".$iddemande."\' onclick=\'validerResultatAnalyseInterfaceVisual(".$iddemande.");\' style=\'float: right; padding-right: 10px; cursor: pointer;\' src=\'../images_icons/74bis.png\' > </span>');
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
	            		
	   	        $('.iconeValidationInterfaceVisual_24').html('<span  class=\'resultat_existe_interface_visual_".$iddemande."\' > <img id=\'resultat_existe_interface_visual_".$iddemande."\' onclick=\'validerResultatAnalyseInterfaceVisual(".$iddemande.");\' style=\'float: right; padding-right: 10px; cursor: pointer;\' src=\'../images_icons/74bis.png\' > </span>');
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
                		
    	        $('.ER_".$iddemande." .iconeValidationInterfaceVisual_25').html('<span  class=\'resultat_existe_interface_visual_".$iddemande."\' > <img id=\'resultat_existe_interface_visual_".$iddemande."\' onclick=\'validerResultatAnalyseInterfaceVisual(".$iddemande.");\' style=\'float: right; padding-right: 10px; cursor: pointer;\' src=\'../images_icons/74bis.png\' > </span>');
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
	    	    		
    	        $('.ER_".$iddemande." .iconeValidationInterfaceVisual_26').html('<span  class=\'resultat_existe_interface_visual_".$iddemande."\' > <img id=\'resultat_existe_interface_visual_".$iddemande."\' onclick=\'validerResultatAnalyseInterfaceVisual(".$iddemande.");\' style=\'float: right; padding-right: 10px; cursor: pointer;\' src=\'../images_icons/74bis.png\' > </span>');
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
	    	    		
     	        $('.ER_".$iddemande." .iconeValidationInterfaceVisual_27').html('<span  class=\'resultat_existe_interface_visual_".$iddemande."\' > <img id=\'resultat_existe_interface_visual_".$iddemande."\' onclick=\'validerResultatAnalyseInterfaceVisual(".$iddemande.");\' style=\'float: right; padding-right: 10px; cursor: pointer;\' src=\'../images_icons/74bis.png\' > </span>');
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
	    	    		
    	        $('.ER_".$iddemande." .iconeValidationInterfaceVisual_28').html('<span  class=\'resultat_existe_interface_visual_".$iddemande."\' > <img id=\'resultat_existe_interface_visual_".$iddemande."\' onclick=\'validerResultatAnalyseInterfaceVisual(".$iddemande.");\' style=\'float: right; padding-right: 10px; cursor: pointer;\' src=\'../images_icons/74bis.png\' > </span>');
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
	    	    		
    	        $('.ER_".$iddemande." .iconeValidationInterfaceVisual_31').html('<span  class=\'resultat_existe_interface_visual_".$iddemande."\' > <img id=\'resultat_existe_interface_visual_".$iddemande."\' onclick=\'validerResultatAnalyseInterfaceVisual(".$iddemande.");\' style=\'float: right; padding-right: 10px; cursor: pointer;\' src=\'../images_icons/74bis.png\' > </span>');
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
	    	    		
    	        $('.iconeValidationInterfaceVisual_32').html('<span  class=\'resultat_existe_interface_visual_".$iddemande."\' > <img id=\'resultat_existe_interface_visual_".$iddemande."\' onclick=\'validerResultatAnalyseInterfaceVisual(".$iddemande.");\' style=\'float: right; padding-right: 10px; cursor: pointer;\' src=\'../images_icons/74bis.png\' > </span>');
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
	    	    		
    	        $('.iconeValidationInterfaceVisual_33').html('<span  class=\'resultat_existe_interface_visual_".$iddemande."\' > <img id=\'resultat_existe_interface_visual_".$iddemande."\' onclick=\'validerResultatAnalyseInterfaceVisual(".$iddemande.");\' style=\'float: right; padding-right: 10px; cursor: pointer;\' src=\'../images_icons/74bis.png\' > </span>');
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
	    	    		
    	        $('.iconeValidationInterfaceVisual_34').html('<span  class=\'resultat_existe_interface_visual_".$iddemande."\' > <img id=\'resultat_existe_interface_visual_".$iddemande."\' onclick=\'validerResultatAnalyseInterfaceVisual(".$iddemande.");\' style=\'float: right; padding-right: 10px; cursor: pointer;\' src=\'../images_icons/74bis.png\' > </span>');
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
	    	    		
     	        $('.ER_".$iddemande." .iconeValidationInterfaceVisual_35').html('<span  class=\'resultat_existe_interface_visual_".$iddemande."\' > <img id=\'resultat_existe_interface_visual_".$iddemande."\' onclick=\'validerResultatAnalyseInterfaceVisual(".$iddemande.");\' style=\'float: right; padding-right: 10px; cursor: pointer;\' src=\'../images_icons/74bis.png\' > </span>');
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
	    	    		
     	        $('.ER_".$iddemande." .iconeValidationInterfaceVisual_36').html('<span  class=\'resultat_existe_interface_visual_".$iddemande."\' > <img id=\'resultat_existe_interface_visual_".$iddemande."\' onclick=\'validerResultatAnalyseInterfaceVisual(".$iddemande.");\' style=\'float: right; padding-right: 10px; cursor: pointer;\' src=\'../images_icons/74bis.png\' > </span>');
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
	    	    		
    	        $('.iconeValidationInterfaceVisual_38').html('<span  class=\'resultat_existe_interface_visual_".$iddemande."\' > <img id=\'resultat_existe_interface_visual_".$iddemande."\' onclick=\'validerResultatAnalyseInterfaceVisual(".$iddemande.");\' style=\'float: right; padding-right: 10px; cursor: pointer;\' src=\'../images_icons/74bis.png\' > </span>');
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
	    	    		
     	        $('.iconeValidationInterfaceVisual_39').html('<span  class=\'resultat_existe_interface_visual_".$iddemande."\' > <img id=\'resultat_existe_interface_visual_".$iddemande."\' onclick=\'validerResultatAnalyseInterfaceVisual(".$iddemande.");\' style=\'float: right; padding-right: 10px; cursor: pointer;\' src=\'../images_icons/74bis.png\' > </span>');
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
	    	    		
     	        $('.iconeValidationInterfaceVisual_40').html('<span  class=\'resultat_existe_interface_visual_".$iddemande."\' > <img id=\'resultat_existe_interface_visual_".$iddemande."\' onclick=\'validerResultatAnalyseInterfaceVisual(".$iddemande.");\' style=\'float: right; padding-right: 10px; cursor: pointer;\' src=\'../images_icons/74bis.png\' > </span>');
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
	    	   
	    	    $('.iconeValidationInterfaceVisual_68').html('<span  class=\'resultat_existe_interface_visual_".$iddemande."\' > <img id=\'resultat_existe_interface_visual_".$iddemande."\' onclick=\'validerResultatAnalyseInterfaceVisual(".$iddemande.");\' style=\'float: right; padding-right: 10px; cursor: pointer;\' src=\'../images_icons/74bis.png\' > </span>');
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
	    	    		
    	        $('.iconeValidationInterfaceVisual_70').html('<span  class=\'resultat_existe_interface_visual_".$iddemande."\' > <img id=\'resultat_existe_interface_visual_".$iddemande."\' onclick=\'validerResultatAnalyseInterfaceVisual(".$iddemande.");\' style=\'float: right; padding-right: 10px; cursor: pointer;\' src=\'../images_icons/74bis.png\' > </span>');
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
	    	    		
    	        $('.iconeValidationInterfaceVisual_30').html('<span  class=\'resultat_existe_interface_visual_".$iddemande."\' > <img id=\'resultat_existe_interface_visual_".$iddemande."\' onclick=\'validerResultatAnalyseInterfaceVisual(".$iddemande.");\' style=\'float: right; padding-right: 10px; cursor: pointer;\' src=\'../images_icons/74bis.png\' > </span>');
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
	    	    		
    	        $('.iconeValidationInterfaceVisual_41').html('<span  class=\'resultat_existe_interface_visual_".$iddemande."\' > <img id=\'resultat_existe_interface_visual_".$iddemande."\' onclick=\'validerResultatAnalyseInterfaceVisual(".$iddemande.");\' style=\'float: right; padding-right: 10px; cursor: pointer;\' src=\'../images_icons/74bis.png\' > </span>');
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
   	            		
    	        $('.iconeValidationInterfaceVisual_42').html('<span  class=\'resultat_existe_interface_visual_".$iddemande."\' > <img id=\'resultat_existe_interface_visual_".$iddemande."\' onclick=\'validerResultatAnalyseInterfaceVisual(".$iddemande.");\' style=\'float: right; padding-right: 10px; cursor: pointer;\' src=\'../images_icons/74bis.png\' > </span>');
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
    	        		
    	        $('.iconeValidationInterfaceVisual_43').html('<span  class=\'resultat_existe_interface_visual_".$iddemande."\' > <img id=\'resultat_existe_interface_visual_".$iddemande."\' onclick=\'validerResultatAnalyseInterfaceVisual(".$iddemande.");\' style=\'float: right; padding-right: 10px; cursor: pointer;\' src=\'../images_icons/74bis.png\' > </span>');
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
	                $('#electro_hemo_label_".($i+1)."').val('".$resultat[$i]['libelle']."').attr('readonly',true);
	                $('#electro_hemo_valeur_".($i+1)."').val('".$resultat[$i]['valeur']."').attr('readonly',true);
	              }, 50);
	                		
       	          $('.iconeValidationInterfaceVisual_44').html('<span  class=\'resultat_existe_interface_visual_".$iddemande."\' > <img id=\'resultat_existe_interface_visual_".$iddemande."\' onclick=\'validerResultatAnalyseInterfaceVisual(".$iddemande.");\' style=\'float: right; padding-right: 10px; cursor: pointer;\' src=\'../images_icons/74bis.png\' > </span>');
	    	    </script>";
	        }
	        
	        $html .="<script> setTimeout(function(){ $('#conclusion_electro_hemo_valeur').val('".str_replace( "'", "\'", $resultat[0]['conclusion'])."').attr('readonly',true); },50); </script>";
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
    	        $('#commentaire_electrophorese_proteine').val('".preg_replace("/(\r\n|\n|\r)/", "\\n", str_replace( "'", "\'", $resultat['commentaire']) )."');
    	        		
    	        $('.iconeValidationInterfaceVisual_45').html('<span  class=\'resultat_existe_interface_visual_".$iddemande."\' > <img id=\'resultat_existe_interface_visual_".$iddemande."\' onclick=\'validerResultatAnalyseInterfaceVisual(".$iddemande.");\' style=\'float: right; padding-right: 10px; cursor: pointer;\' src=\'../images_icons/74bis.png\' > </span>');
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
	    	    		
    	        $('.iconeValidationInterfaceVisual_46').html('<span  class=\'resultat_existe_interface_visual_".$iddemande."\' > <img id=\'resultat_existe_interface_visual_".$iddemande."\' onclick=\'validerResultatAnalyseInterfaceVisual(".$iddemande.");\' style=\'float: right; padding-right: 10px; cursor: pointer;\' src=\'../images_icons/74bis.png\' > </span>');
	    	 </script>";
	    }
	    return $html;
	}
	
	protected function getResultatsAlbumineUrinaire($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursAlbumineUrinaire($iddemande);
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script>
	            $('#type_materiel_albumine_urinaire').val('".str_replace( "'", "\'", $resultat['type_materiel'])."');
	    	    $('#albumine_urinaire').val('".$resultat['albumine_urinaire']."');
	    	    $('#sucre_urinaire').val('".$resultat['sucre_urinaire']."');
	    	    $('#corps_cetonique_urinaire').val('".$resultat['corps_cetonique_urinaire']."');
	    	    		
      	        $('.iconeValidationInterfaceVisual_47').html('<span  class=\'resultat_existe_interface_visual_".$iddemande."\' > <img id=\'resultat_existe_interface_visual_".$iddemande."\' onclick=\'validerResultatAnalyseInterfaceVisual(".$iddemande.");\' style=\'float: right; padding-right: 10px; cursor: pointer;\' src=\'../images_icons/74bis.png\' > </span>');
	    	 </script>";
	        
	        if($resultat['albumine_urinaire'] == 'positif'){
	        	$html .=
	        	"<script>
	    	        $('#albumine_urinaire_degres').val('".$resultat['albumine_urinaire_degres']."').toggle(true);
	    	     </script>";
	        	
	        }
	        
	        if($resultat['sucre_urinaire'] == 'positif'){
	            $html .=
	            "<script>
	    	        $('#sucre_urinaire_degres').val('".$resultat['sucre_urinaire_degres']."').toggle(true);
	    	     </script>";
	        
	        }
	        
	        if($resultat['corps_cetonique_urinaire'] == 'positif'){
	            $html .=
	            "<script>
	    	        $('#corps_cetonique_urinaire_degres').val('".$resultat['corps_cetonique_urinaire_degres']."').toggle(true);
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
	    	    		
      	        $('.iconeValidationInterfaceVisual_48').html('<span  class=\'resultat_existe_interface_visual_".$iddemande."\' > <img id=\'resultat_existe_interface_visual_".$iddemande."\' onclick=\'validerResultatAnalyseInterfaceVisual(".$iddemande.");\' style=\'float: right; padding-right: 10px; cursor: pointer;\' src=\'../images_icons/74bis.png\' > </span>');
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
	    	    		
      	        $('.iconeValidationInterfaceVisual_49').html('<span  class=\'resultat_existe_interface_visual_".$iddemande."\' > <img id=\'resultat_existe_interface_visual_".$iddemande."\' onclick=\'validerResultatAnalyseInterfaceVisual(".$iddemande.");\' style=\'float: right; padding-right: 10px; cursor: pointer;\' src=\'../images_icons/74bis.png\' > </span>');
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
    	        		
      	        $('.iconeValidationInterfaceVisual_50').html('<span  class=\'resultat_existe_interface_visual_".$iddemande."\' > <img id=\'resultat_existe_interface_visual_".$iddemande."\' onclick=\'validerResultatAnalyseInterfaceVisual(".$iddemande.");\' style=\'float: right; padding-right: 10px; cursor: pointer;\' src=\'../images_icons/74bis.png\' > </span>');
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
	    	    		
      	        $('.iconeValidationInterfaceVisual_51').html('<span  class=\'resultat_existe_interface_visual_".$iddemande."\' > <img id=\'resultat_existe_interface_visual_".$iddemande."\' onclick=\'validerResultatAnalyseInterfaceVisual(".$iddemande.");\' style=\'float: right; padding-right: 10px; cursor: pointer;\' src=\'../images_icons/74bis.png\' > </span>');
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
   	    				
      	        $('.iconeValidationInterfaceVisual_52').html('<span  class=\'resultat_existe_interface_visual_".$iddemande."\' > <img id=\'resultat_existe_interface_visual_".$iddemande."\' onclick=\'validerResultatAnalyseInterfaceVisual(".$iddemande.");\' style=\'float: right; padding-right: 10px; cursor: pointer;\' src=\'../images_icons/74bis.png\' > </span>');
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
	    	    setTimeout(function(){ $('#optionResultatCrp').attr('disabled', true); });	

     	        $('.iconeValidationInterfaceVisual_53').html('<span  class=\'resultat_existe_interface_visual_".$iddemande."\' > <img id=\'resultat_existe_interface_visual_".$iddemande."\' onclick=\'validerResultatAnalyseInterfaceVisual(".$iddemande.");\' style=\'float: right; padding-right: 10px; cursor: pointer;\' src=\'../images_icons/74bis.png\' > </span>');
	    	    		
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
	    	    		
     	        $('.iconeValidationInterfaceVisual_54').html('<span  class=\'resultat_existe_interface_visual_".$iddemande."\' > <img id=\'resultat_existe_interface_visual_".$iddemande."\' onclick=\'validerResultatAnalyseInterfaceVisual(".$iddemande.");\' style=\'float: right; padding-right: 10px; cursor: pointer;\' src=\'../images_icons/74bis.png\' > </span>');
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
	    	    		
    	        $('.iconeValidationInterfaceVisual_55').html('<span  class=\'resultat_existe_interface_visual_".$iddemande."\' > <img id=\'resultat_existe_interface_visual_".$iddemande."\' onclick=\'validerResultatAnalyseInterfaceVisual(".$iddemande.");\' style=\'float: right; padding-right: 10px; cursor: pointer;\' src=\'../images_icons/74bis.png\' > </span>');
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
	    	    		
  	   	        $('.iconeValidationInterfaceVisual_56').html('<span  class=\'resultat_existe_interface_visual_".$iddemande."\' > <img id=\'resultat_existe_interface_visual_".$iddemande."\' onclick=\'validerResultatAnalyseInterfaceVisual(".$iddemande.");\' style=\'float: right; padding-right: 10px; cursor: pointer;\' src=\'../images_icons/74bis.png\' > </span>');
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
	    	    		
    	        $('.iconeValidationInterfaceVisual_57').html('<span  class=\'resultat_existe_interface_visual_".$iddemande."\' > <img id=\'resultat_existe_interface_visual_".$iddemande."\' onclick=\'validerResultatAnalyseInterfaceVisual(".$iddemande.");\' style=\'float: right; padding-right: 10px; cursor: pointer;\' src=\'../images_icons/74bis.png\' > </span>');
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
	    	$html .="<script> 
	    			  setTimeout(function(){ $('#conclusion_culot_urinaire_valeur').val('".str_replace( "'", "\'", $resultat[0]['conclusion'])."'); },50); 

  	    	          $('.iconeValidationInterfaceVisual_58').html('<span  class=\'resultat_existe_interface_visual_".$iddemande."\' > <img id=\'resultat_existe_interface_visual_".$iddemande."\' onclick=\'validerResultatAnalyseInterfaceVisual(".$iddemande.");\' style=\'float: right; padding-right: 10px; cursor: pointer;\' src=\'../images_icons/74bis.png\' > </span>');
	    			 </script>";
	    
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
	    	    		
    	        $('.iconeValidationInterfaceVisual_59').html('<span  class=\'resultat_existe_interface_visual_".$iddemande."\' > <img id=\'resultat_existe_interface_visual_".$iddemande."\' onclick=\'validerResultatAnalyseInterfaceVisual(".$iddemande.");\' style=\'float: right; padding-right: 10px; cursor: pointer;\' src=\'../images_icons/74bis.png\' > </span>');
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

    	        $('.iconeValidationInterfaceVisual_60').html('<span  class=\'resultat_existe_interface_visual_".$iddemande."\' > <img id=\'resultat_existe_interface_visual_".$iddemande."\' onclick=\'validerResultatAnalyseInterfaceVisual(".$iddemande.");\' style=\'float: right; padding-right: 10px; cursor: pointer;\' src=\'../images_icons/74bis.png\' > </span>');
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
   	    				
    	        $('.iconeValidationInterfaceVisual_61').html('<span  class=\'resultat_existe_interface_visual_".$iddemande."\' > <img id=\'resultat_existe_interface_visual_".$iddemande."\' onclick=\'validerResultatAnalyseInterfaceVisual(".$iddemande.");\' style=\'float: right; padding-right: 10px; cursor: pointer;\' src=\'../images_icons/74bis.png\' > </span>');
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
        	    		
    	        $('.iconeValidationInterfaceVisual_62').html('<span  class=\'resultat_existe_interface_visual_".$iddemande."\' > <img id=\'resultat_existe_interface_visual_".$iddemande."\' onclick=\'validerResultatAnalyseInterfaceVisual(".$iddemande.");\' style=\'float: right; padding-right: 10px; cursor: pointer;\' src=\'../images_icons/74bis.png\' > </span>');
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
	    	    		
    	        $('.iconeValidationInterfaceVisual_63').html('<span  class=\'resultat_existe_interface_visual_".$iddemande."\' > <img id=\'resultat_existe_interface_visual_".$iddemande."\' onclick=\'validerResultatAnalyseInterfaceVisual(".$iddemande.");\' style=\'float: right; padding-right: 10px; cursor: pointer;\' src=\'../images_icons/74bis.png\' > </span>');
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
	    	    		
    	        $('.iconeValidationInterfaceVisual_64').html('<span  class=\'resultat_existe_interface_visual_".$iddemande."\' > <img id=\'resultat_existe_interface_visual_".$iddemande."\' onclick=\'validerResultatAnalyseInterfaceVisual(".$iddemande.");\' style=\'float: right; padding-right: 10px; cursor: pointer;\' src=\'../images_icons/74bis.png\' > </span>');
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
    	        $('#trichomonas_vaginalis_pv').val('".$resultat['trichomonas_vaginalis']."');
    	        $('#levures_filaments_myceliens_pv').val('".$resultat['levures_filaments_myceliens']."');
    	        $('#gardnerella_vaginalis_pv').val('".$resultat['gardnerella_vaginalis']."');
    	        $('#mobiluncus_spp_pv').val('".$resultat['mobiluncus_spp']."');
    	        $('#clue_cells_pv').val('".$resultat['clue_cells']."');
    	        $('#lactobacillus_pv').val('".$resultat['lactobacillus']."');
    	        $('#autre_flore_pv').val('".$resultat['autre_flore']."');
    	        $('#flore_pv').val('".$resultat['flore']."').trigger('change');
    	        $('#flore_note_pv').val('".str_replace( "'", "\'", $resultat['flore_note'])."');
    	        $('#culture_pv').val('".$resultat['culture']."').trigger('change');
    	        $('#identification_culture_pv').val('".$resultat['identification_culture']."').trigger('change');
    	        $('#recherche_directe_antigene_chlamydia_pv').val('".$resultat['recherche_directe_antigene_chlamydia']."');
    	        $('#recherche_directe_mycoplasmes_pv').val('".$resultat['recherche_directe_mycoplasmes']."').trigger('change');
                $('#identification_rdm_positive_pv').val('".$resultat['identification_rdm_positive']."');
    	    
    	  
    	        $('#commentaire_pv').val('".preg_replace("/(\r\n|\n|\r)/", "\\n", str_replace( "'", "\'", $resultat['commentaire'] ))."');
    	    
	    	 </script>";
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
	            $html .= ($resultatAntiBioGramme['ampicillineAM'])   ? '$("#ampicillineAMABG").val("'.$resultatAntiBioGramme['ampicillineAM'].'"); $("#choixAmpicillineAMABG").trigger("click").attr("disabled");' : '';
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
	             * PARTIE Polymyxine
	             */
	            $html .= ($resultatAntiBioGramme['fosfomycineFOS']) ? '$("#fosfomycineFOSABG").val("'.$resultatAntiBioGramme['fosfomycineFOS'].'"); $("#choixFosfomycineFOSABG").trigger("click");' : '';
	            $html .= ($resultatAntiBioGramme['vancomycineVA']) ? '$("#vancomycineVAABG").val("'.$resultatAntiBioGramme['vancomycineVA'].'"); $("#choixVancomycineVAABG").trigger("click");' : '';
	            $html .= ($resultatAntiBioGramme['colistineCS']) ? '$("#colistineCSABG").val("'.$resultatAntiBioGramme['colistineCS'].'"); $("#choixColistineCSABG").trigger("click");' : '';
	    
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
	            $html .= ($resultatAntiBioGramme['tetracyclineTE']) ? '$("#tetracyclineTEABG").val("'.$resultatAntiBioGramme['tetracyclineTE'].'"); $("#choixTetracyclineTEABG").trigger("click");' : '';
	            $html .= ($resultatAntiBioGramme['doxycyclineDO']) ? '$("#doxycyclineDOABG").val("'.$resultatAntiBioGramme['doxycyclineDO'].'"); $("#choixDoxycyclineDOABG").trigger("click");' : '';
	            /**
	             * FIN PARTIE Cyclines
	             */
	             
	            /**
	             * PARTIE Macrolides et apparentés
	             */
	            $html .= ($resultatAntiBioGramme['erythromycineE']) ? '$("#erythromycineEABG").val("'.$resultatAntiBioGramme['erythromycineE'].'"); $("#choixErythromycineEABG").trigger("click");' : '';
	            $html .= ($resultatAntiBioGramme['lincomycineL']) ? '$("#lincomycineLABG").val("'.$resultatAntiBioGramme['lincomycineL'].'"); $("#choixLincomycineLABG").trigger("click");' : '';
	            $html .= ($resultatAntiBioGramme['pristinamycinePT']) ? '$("#pristinamycinePTABG").val("'.$resultatAntiBioGramme['pristinamycinePT'].'"); $("#choixPristinamycinePTABG").trigger("click");' : '';
	            /**
	             * FIN PARTIE Macrolides et apparentés
	             */
	             
	            /**
	             * PARTIE Fluoroquinolones
	             */
	            $html .= ($resultatAntiBioGramme['acideFusidiqueFA']) ? '$("#acideFusidiqueFAABG").val("'.$resultatAntiBioGramme['acideFusidiqueFA'].'"); $("#choixAcideFusidiqueFAABG").trigger("click");' : '';
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
	            $html .= ($resultatAntiBioGramme['rifampicineRA']) ? '$("#rifampicineRAABG").val("'.$resultatAntiBioGramme['rifampicineRA'].'"); $("#choixRifampicineRAABG").trigger("click");' : '';
	            $html .= ($resultatAntiBioGramme['cotrimoxazoleSXT']) ? '$("#cotrimoxazoleSXTABG").val("'.$resultatAntiBioGramme['cotrimoxazoleSXT'].'"); $("#choixCotrimoxazoleSXTABG").trigger("click");' : '';
	            /**
	             * FIN PARTIE Imidazolés
	             */
	             
	            /**
	             * Conclusion
	             */
	             
	            $html .= ($resultatAntiBioGramme['conclusion']) ? '$("#conclusion_pv_ABG").val("'.$resultatAntiBioGramme['conclusion'].'");' : '';
	            /**
	             * ==========
	             */
	             
	             
	            /*
	             * BLOCAGE DE TOUS LES CHAMPS
	             */
	            $html .='$(".blocChampAntiBioGrammeDuPVABG input, .blocChampAntiBioGrammeDuPVABG select").attr("disabled", true)';
	            /*
	             * ==============
	             */
	            
	            $html .='</script>';
	        }
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
		if($analyse['Idanalyse'] == 20){ $html .= $this->dimeres_20(); $html .= $this->getResultatsDDimeres($iddemande); }
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
		if($analyse['Idanalyse'] == 65){ $html .= $this->pv_65(); $html .= $this->getResultatsPV($iddemande);}
		if($analyse['Idanalyse'] == 66){ $html .= $this->ecbu_66(); }
		if($analyse['Idanalyse'] == 67){ $html .= $this->pus_67(); }
		if($analyse['Idanalyse'] == 68){ $html .= $this->typage_hemoglobine_68();  $html .= $this->getResultatsTypageHemoglobine($iddemande); }
		
		if($analyse['Idanalyse'] == 70){ $html .= $this->ldh_70();  $html .= $this->getResultatsLDH($iddemande); }
		if($analyse['Idanalyse'] == 71){ $html .= $this->nfs_tr_71(); $html .= $this->getResultatsNfsTR($iddemande); }
		
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
	        $this->getResultatDemandeAnalyseTable()->updateValeursCommentaireNfs($tab, $iddemande, 1);
	    }
	    else
	        if($idanalyse == 2){
	            //$this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            //$donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursGsrhGroupage($tab, $iddemande);
	    }
	    else 
	        if($idanalyse == 3){
	            //$this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            //$donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursRechercheAntigene($tab, $iddemande);
	    }
	    else 
	        if($idanalyse == 4){
	            //$this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            //$donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursTestCombsDirect($tab, $iddemande);
	    }
	    else 
	        if($idanalyse == 5){
	            $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            $donneesExiste = $this->getResultatDemandeAnalyseTable()->addCommentValeursTestCombsIndirect($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 6){
	            //$this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            //$donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursTestCompatibilite($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 7){
	            //$this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            //$donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursVitesseSedimentation($tab, $iddemande);
	    }
	    else 
	        if($idanalyse == 8){
	            //$this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            //$donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursTestDemmel($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 9){
	            //$this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            //$donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursTauxReticulocyte($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 10){
	            //$this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            //$donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursGoutteEpaisse($tab, $iddemande);
	    }
	    
	    
	    else
	        if($idanalyse == 14){
	            //$this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            //$donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursTpInr($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 15){
	            //$this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            //$donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursTca($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 16){
	            //$this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            //$donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursFibrinemie($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 17){
	            //$this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            //$donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursTempsSaignement($tab, $iddemande);
	    }
	    
	    
	    else
	        if($idanalyse == 21){
	            //$this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            //$donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursGlycemie($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 22){
	            //$this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            //$donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursCreatininemie($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 23){
	            //$this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            //$donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursAzotemie($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 24){
	            //$this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            //$donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursAcideUrique($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 25){
	            //$this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            //$donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursCholesterolTotal($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 26){
	            //$this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            //$donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursTriglycerides($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 27){
	            //$this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            //$donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursCholesterolHDL($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 28){
	            //$this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            //$donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursCholesterolLDL($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 29){
	            //$this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            //$donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeurs_Total_HDL_LDL_Triglycerides($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 30){
	            //$this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            //$donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursLipidesTotaux($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 31){
	            //$this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            //$donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursIonogramme($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 32){
	            //$this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            //$donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursCalcemie($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 33){
	            //$this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            //$donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursMagnesemie($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 34){
	            //$this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            //$donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursPhosphoremie($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 35){
	            //$this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            //$donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursTgoAsat($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 36){
	            //$this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            //$donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursTgpAlat($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 37){
	            //$this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            //$donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursAsatAlat($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 38){
	            //$this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            //$donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursPhosphatageAlcaline($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 39){
	            //$this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            //$donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursGamaGtYgt($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 40){
	            //$this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            //$donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursFerSerique($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 41){
	            //$this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            //$donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursFerritinine($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 42){
	            //$this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            //$donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursBilirubineTotaleDirecte($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 43){
	            //$this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            //$donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursHemoglobineGlyqueeHBAC($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 44){
	            //$this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            //$donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursElectrophoreseHemoglobine($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 45){
	            //$this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            //$donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursElectrophoreseProteines($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 46){
	            //$this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            //$donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursAlbuminemie($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 47){
	            //$this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            //$donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursAlbumineUrinaire($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 47){
	            //$this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            //$donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursProteineTotale($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 48){
	            //$this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            //$donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursProtidemie($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 49){
	            //$this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            //$donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursProteinurie($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 50){
	            //$this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            //$donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursHlmCompteDaddis($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 51){
	            //$this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            //$donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursBetaHcgPlasmatique($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 52){
	            //$this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            //$donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursPsa($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 53){
	            //$this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            //$donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursCrp($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 54){
	            //$this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            //$donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursFacteursRhumatoides($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 55){
	            //$this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            //$donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursRfWaalerRose($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 56){
	            //$this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            //$donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursToxoplasmose($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 57){
	            //$this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            //$donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursRubeole($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 58){
	            //$this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            //$donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursCulotUrinaire($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 59){
	            //$this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            //$donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursSerologieChlamydiae($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 60){
	            //$this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            //$donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursSerologieSyphilitique($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 61){
	            //$this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            //$donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursAslo($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 62){
	            //$this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            //$donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursWidal($tab, $iddemande);
	    }
	    else
	        if($idanalyse == 63){
	            //$this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            //$donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursAgHbs($tab, $iddemande);
	    }
	    
	    
	    else
	        if($idanalyse == 65){
	            $this->getResultatDemandeAnalyseTable()->updateValeursCommentairePV($tab, $iddemande, 65);
	    }
	    
	    
	    
	    
	    else
	        if($idanalyse == 68){
	            //$this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            //$donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursTypageHemoglobine($tab, $iddemande);
	        }
	    
	        
	    else
	        if($idanalyse == 70){
	        	//$this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	        	//$donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursLDH($tab, $iddemande);
	    }
	    
	    else 
	    	if($idanalyse == 71){
	    		$this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	    		$this->getResultatDemandeAnalyseTable()->updateValeursCommentaireNfs($tab, $iddemande, 71);
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
		$typeResultat = ( int ) $this->params ()->fromPost ( 'typeResultat', 0 ); 
	
		$listeAnalyse = $this->getAnalyseTable()->getListeAnalysesDemandeesTrieesPopupBiol($iddemande, $typeResultat);
		
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
			
			$tableauDemandes[] = $liste['iddemande'];
			
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
	  	    if($liste['Idanalyse'] == 18){ $html .= $this->facteur_viii_18(); $html .= $this->getResultatsFacteur8($liste['iddemande']); }
			if($liste['Idanalyse'] == 19){ $html .= $this->facteur_ix_19();  $html .= $this->getResultatsFacteur9($liste['iddemande']); }
			if($liste['Idanalyse'] == 20){ $html .= $this->dimeres_20(); $html .= $this->getResultatsDDimeres($liste['iddemande']);      }
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
			if($liste['Idanalyse'] == 65){ $html .= $this->pv_65(); $html .= $this->getResultatsPV($liste['iddemande']); }
			if($liste['Idanalyse'] == 66){ $html .= $this->ecbu_66(); }
			if($liste['Idanalyse'] == 67){ $html .= $this->pus_67(); }
			if($liste['Idanalyse'] == 68){ $html .= $this->typage_hemoglobine_68();  $html .= $this->getResultatsTypageHemoglobine($liste['iddemande']); }
			
			if($liste['Idanalyse'] == 70){ $html .= $this->ldh_70();  $html .= $this->getResultatsLDH($liste['iddemande']); }
			if($liste['Idanalyse'] == 71){ $html .= $this->nfs_tr_71(); $html .= $this->getResultatsNfsTR($liste['iddemande']); }
			
			$tabAnalyses[] = $liste['Idanalyse'];
			$tabDemandes[] = $liste['iddemande'];
			
			$html .="</table>
				      <div style='width: 100%; height: 20px;'> </div>";
		}
	
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
	    $idemploye = $this->layout()->user['idpersonne'];
	    
	    for ($i = 0 ; $i<count($tabAnalyses) ; $i++){
	        $idanalyse = $tabAnalyses[$i];
	        $iddemande = $tabDemandes[$i];
	        
	        if($idanalyse == 1){
	            $tab = $tableau[$idanalyse];
	            $this->getResultatDemandeAnalyseTable()->updateValeursCommentaireNfs($tab, $iddemande, 1);
	        }
	        else 
	        	if($idanalyse == 71){
	        		$tab = $tableau[$idanalyse];
	        		$this->getResultatDemandeAnalyseTable()->updateValeursCommentaireNfs($tab, $iddemande, 71);
	        	}
	        
	        /*
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
	        */
	        
	        
	        else 
	        	if($idanalyse == 65){
	        		$tab = $tableau[$idanalyse];
	        		$this->getResultatDemandeAnalyseTable()->updateValeursCommentairePV($tab, $iddemande, 65);
	        	}
	        
	        
	        
	        
	        
	        
	        /*
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
	        */
	        
	        
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
		
		foreach ($listeAnalysesType as $liste){
			$tableauDonnees[] = $liste;
			if( !in_array($liste['idpersonne'], $tableauPatients) ) { $tableauPatients[] = $liste['idpersonne']; }
		}
		
		$html ="";
		
		for($i = 0 ; $i < count($tableauPatients) ; $i++){
			$k = 0 ;
				
			$html .="<table class='designEnTeteAnalyse  patientListeAnalyse  pat_".$tableauPatients[$i]."' style='width: 100%;' >";
			for ($j = 0 ; $j < count($tableauDonnees) ; $j++){
		
				if($tableauDonnees[$j]['idpersonne'] == $tableauPatients[$i]){
					
				    if($k == 0){
						$k++;
						$depistage = $this->getPatientTable()->getDepistagePatient($tableauDonnees[$j]['idpersonne']);
						$typepatient = 'E';
						if($depistage->current()){ if($depistage->current()['typepatient'] == 1){ $typepatient = 'I'; } }
						$html .="<tr style='width: 90%;' > <td class='enTete2'>". $tableauDonnees[$j]['prenom'].' '.$tableauDonnees[$j]['nom'] .' ('.$tableauDonnees[$j]['idpersonne']."-".$typepatient.")</td> </tr>";
					}
		
					$html .="<tr> <th class='enTitre'> <div>". $tableauDonnees[$j]['Designation'] ."</div> </th> </tr>";
				    
					
					$tableauDemandes [] = $tableauDonnees[$j]['iddemande'];
					//Pour la reconnaissance de l'analyse demandée
					$html .="<tr><td><table style='width: 100%; margin-left: 0px;'  class='listeAnalyseTAD  ER_".$tableauDonnees[$j]['iddemande']."' >";
					
					
					if($tableauDonnees[$j]['Idanalyse'] ==  1){ $html .= $this->nfs_1();                   }
					if($tableauDonnees[$j]['Idanalyse'] ==  2){ $html .= $this->gsrh_groupage_2();         }
	                if($tableauDonnees[$j]['Idanalyse'] ==  3){ $html .= $this->recherche_antigene_3();    }
	                if($tableauDonnees[$j]['Idanalyse'] ==  4){ $html .= $this->test_combs_direct_4();     }
	                if($tableauDonnees[$j]['Idanalyse'] ==  5){ $html .= $this->test_combs_indirect_5();   }
	                if($tableauDonnees[$j]['Idanalyse'] ==  6){ $html .= $this->test_compatibilite_6();    }
	                if($tableauDonnees[$j]['Idanalyse'] ==  7){ $html .= $this->vitesse_sedimentation_7(); }
	                if($tableauDonnees[$j]['Idanalyse'] ==  8){ $html .= $this->test_demmel_8();           }
	                if($tableauDonnees[$j]['Idanalyse'] ==  9){ $html .= $this->taux_reticulocytes_9();    }
	                if($tableauDonnees[$j]['Idanalyse'] == 10){ $html .= $this->goutte_epaisse_10();       }
	                 
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
	                if($tableauDonnees[$j]['Idanalyse'] == 47){ $html .= $this->albumine_urinaire_47();          }
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
		for($i = 0 ; $i < count($tableauDemandes) ; $i++){
		    $html .="<script> listeDesDemandesSelect[".$i."]=".$tableauDemandes[$i]."; </script>";
		}
		
		
		//Récupération de la liste des codes des patients
		//Récupération de la liste des codes des patients
		$liste_code= "<option>  </option>";
		for($i = 0 ; $i < count($tableauPatients) ; $i++){
		    $liste_code.= "<option value=".$tableauPatients[$i]." > ".$tableauPatients[$i]."</option>";
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
	
	    foreach ($listeAnalysesType as $liste){
	        $tableauDonnees[] = $liste;
	        if( !in_array($liste['idpersonne'], $tableauPatients) ) { $tableauPatients[] = $liste['idpersonne']; }
	    }
	
	    $html ="";
	
	    for($i = 0 ; $i < count($tableauPatients) ; $i++){
	        $k = 0 ;
	
	        $html .="<table class='designEnTeteAnalyse  patientListeAnalyse  pat_".$tableauPatients[$i]."' style='width: 100%;' >";
	        for ($j = 0 ; $j < count($tableauDonnees) ; $j++){
	
	            if($tableauDonnees[$j]['idpersonne'] == $tableauPatients[$i]){
	                	
	                if($k == 0){
	                    $k++;
	                    $depistage = $this->getPatientTable()->getDepistagePatient($tableauDonnees[$j]['idpersonne']);
						$typepatient = 'E';
						if($depistage->current()){ if($depistage->current()['typepatient'] == 1){ $typepatient = 'I'; } }
	                    $html .="<tr style='width: 90%;' > <td class='enTete2'>". $tableauDonnees[$j]['prenom'].' '.$tableauDonnees[$j]['nom'] .' ('.$tableauDonnees[$j]['idpersonne']."-".$typepatient.") </td> </tr>";
	                }
	
	                $html .="<tr> <th class='enTitre'> <div>". $tableauDonnees[$j]['Designation'] ." </div> </th> </tr>";
	
	                
	                $tableauDemandes [] = $tableauDonnees[$j]['iddemande'];
	                //Pour la reconnaissance de l'analyse demandée
	                $html .="<tr><td><table style='width: 100%; margin-left: 0px;'  class='listeAnalyseTAD  ER_".$tableauDonnees[$j]['iddemande']."' >";
	                 
	                
	                if($tableauDonnees[$j]['Idanalyse'] ==  1){ $html .= $this->nfs_1();                   }
	                if($tableauDonnees[$j]['Idanalyse'] ==  2){ $html .= $this->gsrh_groupage_2();         }
	                if($tableauDonnees[$j]['Idanalyse'] ==  3){ $html .= $this->recherche_antigene_3();    }
	                if($tableauDonnees[$j]['Idanalyse'] ==  4){ $html .= $this->test_combs_direct_4();     }
	                if($tableauDonnees[$j]['Idanalyse'] ==  5){ $html .= $this->test_combs_indirect_5();   }
	                if($tableauDonnees[$j]['Idanalyse'] ==  6){ $html .= $this->test_compatibilite_6();    }
	                if($tableauDonnees[$j]['Idanalyse'] ==  7){ $html .= $this->vitesse_sedimentation_7(); }
	                if($tableauDonnees[$j]['Idanalyse'] ==  8){ $html .= $this->test_demmel_8();           }
	                if($tableauDonnees[$j]['Idanalyse'] ==  9){ $html .= $this->taux_reticulocytes_9();    }
	                if($tableauDonnees[$j]['Idanalyse'] == 10){ $html .= $this->goutte_epaisse_10();       }
	                 
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
	                if($tableauDonnees[$j]['Idanalyse'] == 47){ $html .= $this->albumine_urinaire_47();          }
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
	    for($i = 0 ; $i < count($tableauDemandes) ; $i++){
	        $html .="<script> listeDesDemandesSelect[".$i."]=".$tableauDemandes[$i]."; </script>";
	    }
 	    
	    
	    //Récupération de la liste des codes des patients
	    //Récupération de la liste des codes des patients
	    $liste_code= "<option>  </option>";
	    for($i = 0 ; $i < count($tableauPatients) ; $i++){
	        $liste_code.= "<option value=".$tableauPatients[$i]." > ".$tableauPatients[$i]."</option>";
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
	    
	    if($listeAnalysesType){
	        foreach ($listeAnalysesType as $liste){
	            $tableauDonnees[] = $liste;
	            if( !in_array($liste['idpersonne'], $tableauPatients) ) { $tableauPatients[] = $liste['idpersonne']; }
	        }
	         
	        for($i = 0 ; $i < count($tableauPatients) ; $i++){
	            $k = 0 ;
	             
	            $html .="<table class='designEnTeteAnalyse  patientListeAnalyse  pat_".$tableauPatients[$i]."' style='width: 100%;' >";
	            
	            for ($j = 0 ; $j < count($tableauDonnees) ; $j++){
	                 
	                if($tableauDonnees[$j]['idpersonne'] == $tableauPatients[$i]){
	                     
	                    if($k == 0){
	                        $k++;
	                        $depistage = $this->getPatientTable()->getDepistagePatient($tableauDonnees[$j]['idpersonne']);
						    $typepatient = 'E';
						    if($depistage->current()){ if($depistage->current()['typepatient'] == 1){ $typepatient = 'I'; } }
	                        $html .="<tr style='width: 90%;' > <td class='enTete2'>". $tableauDonnees[$j]['prenom'].' '.$tableauDonnees[$j]['nom'] .' ('.$tableauDonnees[$j]['idpersonne']."-".$typepatient.")</td> </tr>";
	                    }
	                     
	                    $html .="<tr> <th class='enTitre'> <div>". $tableauDonnees[$j]['Designation'] ." </div> </th> </tr>";
	                     
	                    
	                    $tableauDemandes [] = $tableauDonnees[$j]['iddemande'];
	                    //Pour la reconnaissance de l'analyse demandée
	                    $html .="<tr><td><table style='width: 100%; margin-left: 0px;'  class='listeAnalyseTAD  ER_".$tableauDonnees[$j]['iddemande']."' >";
	                    
	                    if($tableauDonnees[$j]['Idanalyse'] ==  1){ $html .= $this->nfs_1();                   }
	                    if($tableauDonnees[$j]['Idanalyse'] ==  2){ $html .= $this->gsrh_groupage_2();         }
	                    if($tableauDonnees[$j]['Idanalyse'] ==  3){ $html .= $this->recherche_antigene_3();    }
	                    if($tableauDonnees[$j]['Idanalyse'] ==  4){ $html .= $this->test_combs_direct_4();     }
	                    if($tableauDonnees[$j]['Idanalyse'] ==  5){ $html .= $this->test_combs_indirect_5();   }
	                    if($tableauDonnees[$j]['Idanalyse'] ==  6){ $html .= $this->test_compatibilite_6();    }
	                    if($tableauDonnees[$j]['Idanalyse'] ==  7){ $html .= $this->vitesse_sedimentation_7(); }
	                    if($tableauDonnees[$j]['Idanalyse'] ==  8){ $html .= $this->test_demmel_8();           }
	                    if($tableauDonnees[$j]['Idanalyse'] ==  9){ $html .= $this->taux_reticulocytes_9();    }
	                    if($tableauDonnees[$j]['Idanalyse'] == 10){ $html .= $this->goutte_epaisse_10();       }
	                     
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
	                    if($tableauDonnees[$j]['Idanalyse'] == 47){ $html .= $this->albumine_urinaire_47();          }
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
	        $liste_code.= "<option value=".$tableauPatients[$i]." > ".$tableauPatients[$i]."</option>";
	    }
	    $html .="<script> $('#listeCodesDesPatients').html('".$liste_code."'); </script>";
	    
	    
	    
	    //Récupération de la liste des demandes, pour connaitre les demandes 
	    $html .="<script> var listeDesDemandesSelect = []; </script>";
	    for($i = 0 ; $i < count($tableauDemandes) ; $i++){
	        $html .="<script> listeDesDemandesSelect[".$i."]=".$tableauDemandes[$i]."; </script>";
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
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_nfs' readonly> </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;'  class='iconeValidationInterfaceVisual_1'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' style='font-weight:bold;'><span> Leucocytes <input id='champ1' type='number' step='any' min='1000'  max='20000' readonly> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='font-weight:bold; padding-top: 5px;'> /mm<sup>3</sup> </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='font-weight:bold; padding-top: 5px; width: 80%;'> (4 000 - 10 000) </label></td>";
	    $html .= "</tr>";
	     
	    $html .="<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .=" <td colspan='2' style='width: 55%; background: re;'>";
	    $html .="   <label class='formule_leucocytaire' >";
	    $html .="     <table style='width: 100%; height: 3px;' >";
	    $html .="       <tr>";
	    $html .="         <td style='width: 20%; height: 3px; text-align:right; padding-top: 3px; font-size: 15px;'> P. Neutrophiles </td>";
	    $html .="         <td style='width: 35%;'> <input id='champ2' type='number' readonly  step='any'> /mm<sup>3</sup> </td>";
	    $html .="         <td style='width: 35%;'>  <input id='champ7' type='number' readonly step='any'  min='0' max='100'> % </td>";
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
	    $html .="         <td style='width: 35%; '> <input id='champ3' type='number' readonly  step='any'> /mm<sup>3</sup> </td>";
	    $html .="         <td style='width: 35%;'>  <input id='champ8' type='number' readonly  step='any'  min='0' max='100'> % </td>";
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
	    $html .="         <td style='width: 35%; '> <input id='champ4' type='number' readonly  step='any'> /mm<sup>3</sup> </td>";
	    $html .="         <td style='width: 35%;'>  <input id='champ9' type='number' readonly  step='any'  min='0' max='100'> % </td>";
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
	    $html .="         <td style='width: 35%; '> <input id='champ5' type='number' readonly  step='any'> /mm<sup>3</sup> </td>";
	    $html .="         <td style='width: 35%;'>  <input id='champ10' type='number' readonly step='any'  min='0' max='100'> % </td>";
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
	    $html .="         <td style='width: 35%; '> <input id='champ6' type='number' readonly  step='any'> /mm<sup>3</sup> </td>";
	    $html .="         <td style='width: 35%;'>  <input id='champ11' type='number' readonly step='any'  min='0' max='100'> % </td>";
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
	    $html .= "  <td style='width: 55%;'><label class='lab1' style='font-weight:bold;'><span> H&eacute;maties <input id='champ12' type='number' step='any'  min='0' max='10' readonly> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='font-weight:bold; padding-top: 5px;'> 10<sup>6</sup>/mm<sup>3</sup> </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='font-weight:bold; padding-top: 5px; width: 80%;'> (3,5 - 5,0) </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' style='font-weight:bold;' ><span> H&eacute;moglobine <input id='champ13' type='number' step='any'  min='0' max='30' readonly> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px; font-weight:bold;' > g/dl </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px;  font-weight:bold; width: 80%;'> (11 - 15) </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span> H&eacute;matocrite <input id='champ14' type='number' step='any'  min='0' max='100' readonly> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> % </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> (37 - 50) </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span> V.G.M <input id='champ15' type='number' step='any'  min='0' max='200' readonly> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> fl </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> (80 - 100) </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span> T.C.M.H <input id='champ16' type='number' step='any'  min='0' max='100' readonly> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> pg </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> (27 - 34) </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span> C.C.M.H <input id='champ17' type='number' step='any'  min='0' max='100' readonly> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> g/dl </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> (32 - 36) </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span> IDR - CV <input id='champ18' type='number' step='any'  min='0' max='50' readonly> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> % </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> (11 - 16) </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span> IDR - DS <input id='champ19' type='number' step='any'  min='0' max='100' readonly> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> fl </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> (35 - 56) </label></td>";
	    $html .= "</tr>";
	    
	    
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px;'>";
	    $html .= "  <td style='width: 55%; height: 20px; '> </td>";
	    $html .= "  <td style='width: 15%;'></td>";
	    $html .= "  <td style='width: 30%;'></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' style='font-weight:bold;'><span> Plaquettes <input id='champ20' type='number' step='any'  min='0' max='1000' readonly> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='font-weight:bold; padding-top: 5px;'> 10<sup>3</sup>/mm<sup>3</sup> </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='font-weight:bold; padding-top: 5px; width: 80%;'> (150 - 450) </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span> VMP <input id='champ21' type='number' step='any'  min='0' max='50' readonly> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> fl </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> 6, 5 - 12, 0 </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span> IDP <input id='champ22' type='number' step='any'  min='0' max='50' readonly> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> fl </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> 9, 0 - 17, 0 </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span> PCT <input id='champ23' type='number' step='any'  min='0' max='2' readonly> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> % </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> 0, 108 - 0, 282 </label></td>";
	    $html .= "</tr>";
	    
	    
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px;'>";
	    $html .= "  <td style='width: 55%; height: 20px; '> </td>";
	    $html .= "  <td style='width: 15%;'></td>";
	    $html .= "  <td style='width: 30%;'></td>";
	    $html .= "</tr>";
	    
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    if($this->layout()->user['role'] == 'biologiste'){
	    	$html .= "  <td colspan='2' ><label style='height: 80px;' ><span style='font-size: 16px; float: left;  margin-left: 30px;'> Commentaire:  </span> <textarea id='commentaire_hemogramme' style='max-height: 57px; min-height: 57px; max-width: 400px; min-width: 400px; margin-left: 30px;' > </textarea> </label></td>";
	    }else{
	    	$html .= "  <td colspan='2' ><label style='height: 80px;' ><span style='font-size: 16px; float: left;  margin-left: 30px;'> Commentaire:  </span> <textarea id='commentaire_hemogramme' style='max-height: 57px; min-height: 57px; max-width: 400px; min-width: 400px; margin-left: 30px;' readonly> </textarea> </label></td>";	    	
	    }
	    $html .= "  <td style='width: 30%;'><label style='padding-top: 5px; width: 80%; height: 80px; font-size: 14px;'>  </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "</table> </td> </tr>";
	    
	    
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
		$html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_nfs' readonly> </div> </td>";
		$html .= "  <td colspan='2' style='width: 45%;'  class='iconeValidationInterfaceVisual_1'> </td>";
		$html .= "</tr>";
		//POUR LE NOM DU TYPE DE MATERIEL UTILISE
		 
		$html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
		$html .= "  <td style='width: 55%;'><label class='lab1' style='font-weight:bold;'><span> Leucocytes <input id='champ1' type='number' step='any' min='1000'  max='20000' readonly> </span></label></td>";
		$html .= "  <td style='width: 15%;'><label class='lab2' style='font-weight:bold; padding-top: 5px;'> /mm<sup>3</sup> </label></td>";
		$html .= "  <td style='width: 30%;'><label class='lab3' style='font-weight:bold; padding-top: 5px; width: 80%;'> (4 000 - 10 000) </label></td>";
		$html .= "</tr>";
	
		$html .="<tr class='ligneAnanlyse' style='width: 100%;'>";
		$html .=" <td colspan='2' style='width: 55%; background: re;'>";
		$html .="   <label class='formule_leucocytaire' >";
		$html .="     <table style='width: 100%; height: 3px;' >";
		$html .="       <tr>";
		$html .="         <td style='width: 20%; height: 3px; text-align:right; padding-top: 3px; font-size: 15px;'> P. Neutrophiles </td>";
		$html .="         <td style='width: 35%;'> <input id='champ2' type='number' readonly  step='any'> /mm<sup>3</sup> </td>";
		$html .="         <td style='width: 35%;'>  <input id='champ7' type='number' readonly step='any'  min='0' max='100'> % </td>";
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
		$html .="         <td style='width: 35%; '> <input id='champ3' type='number' readonly  step='any'> /mm<sup>3</sup> </td>";
		$html .="         <td style='width: 35%;'>  <input id='champ8' type='number' readonly  step='any'  min='0' max='100'> % </td>";
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
		$html .="         <td style='width: 35%; '> <input id='champ4' type='number' readonly  step='any'> /mm<sup>3</sup> </td>";
		$html .="         <td style='width: 35%;'>  <input id='champ9' type='number' readonly  step='any'  min='0' max='100'> % </td>";
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
		$html .="         <td style='width: 35%; '> <input id='champ5' type='number' readonly  step='any'> /mm<sup>3</sup> </td>";
		$html .="         <td style='width: 35%;'>  <input id='champ10' type='number' readonly step='any'  min='0' max='100'> % </td>";
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
		$html .="         <td style='width: 35%; '> <input id='champ6' type='number' readonly  step='any'> /mm<sup>3</sup> </td>";
		$html .="         <td style='width: 35%;'>  <input id='champ11' type='number' readonly step='any'  min='0' max='100'> % </td>";
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
		$html .= "  <td style='width: 55%;'><label class='lab1' style='font-weight:bold;'><span> H&eacute;maties <input id='champ12' type='number' step='any'  min='0' max='10' readonly> </span></label></td>";
		$html .= "  <td style='width: 15%;'><label class='lab2' style='font-weight:bold; padding-top: 5px;'> 10<sup>6</sup>/mm<sup>3</sup> </label></td>";
		$html .= "  <td style='width: 30%;'><label class='lab3' style='font-weight:bold; padding-top: 5px; width: 80%;'> (3,5 - 5,0) </label></td>";
		$html .= "</tr>";
		 
		$html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
		$html .= "  <td style='width: 55%;'><label class='lab1' style='font-weight:bold;' ><span> H&eacute;moglobine <input id='champ13' type='number' step='any'  min='0' max='30' readonly> </span></label></td>";
		$html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px; font-weight:bold;' > g/dl </label></td>";
		$html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px;  font-weight:bold; width: 80%;'> (11 - 15) </label></td>";
		$html .= "</tr>";
		 
		$html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
		$html .= "  <td style='width: 55%;'><label class='lab1' ><span> H&eacute;matocrite <input id='champ14' type='number' step='any'  min='0' max='100' readonly> </span></label></td>";
		$html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> % </label></td>";
		$html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> (37 - 50) </label></td>";
		$html .= "</tr>";
		 
		$html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
		$html .= "  <td style='width: 55%;'><label class='lab1' ><span> V.G.M <input id='champ15' type='number' step='any'  min='0' max='200' readonly> </span></label></td>";
		$html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> fl </label></td>";
		$html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> (80 - 100) </label></td>";
		$html .= "</tr>";
		 
		$html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
		$html .= "  <td style='width: 55%;'><label class='lab1' ><span> T.C.M.H <input id='champ16' type='number' step='any'  min='0' max='100' readonly> </span></label></td>";
		$html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> pg </label></td>";
		$html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> (27 - 34) </label></td>";
		$html .= "</tr>";
		 
		$html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
		$html .= "  <td style='width: 55%;'><label class='lab1' ><span> C.C.M.H <input id='champ17' type='number' step='any'  min='0' max='100' readonly> </span></label></td>";
		$html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> g/dl </label></td>";
		$html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> (32 - 36) </label></td>";
		$html .= "</tr>";
		 
		$html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
		$html .= "  <td style='width: 55%;'><label class='lab1' ><span> IDR - CV <input id='champ18' type='number' step='any'  min='0' max='50' readonly> </span></label></td>";
		$html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> % </label></td>";
		$html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> (11 - 16) </label></td>";
		$html .= "</tr>";
		 
		$html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
		$html .= "  <td style='width: 55%;'><label class='lab1' ><span> IDR - DS <input id='champ19' type='number' step='any'  min='0' max='100' readonly> </span></label></td>";
		$html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> fl </label></td>";
		$html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> (35 - 56) </label></td>";
		$html .= "</tr>";
		 
		 
		 
		$html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px;'>";
		$html .= "  <td style='width: 55%; height: 20px; '> </td>";
		$html .= "  <td style='width: 15%;'></td>";
		$html .= "  <td style='width: 30%;'></td>";
		$html .= "</tr>";
		 
		$html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
		$html .= "  <td style='width: 55%;'><label class='lab1' style='font-weight:bold;'><span> Plaquettes <input id='champ20' type='number' step='any'  min='0' max='1000' readonly> </span></label></td>";
		$html .= "  <td style='width: 15%;'><label class='lab2' style='font-weight:bold; padding-top: 5px;'> 10<sup>3</sup>/mm<sup>3</sup> </label></td>";
		$html .= "  <td style='width: 30%;'><label class='lab3' style='font-weight:bold; padding-top: 5px; width: 80%;'> (150 - 450) </label></td>";
		$html .= "</tr>";
		 
		$html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
		$html .= "  <td style='width: 55%;'><label class='lab1' ><span> VMP <input id='champ21' type='number' step='any'  min='0' max='50' readonly> </span></label></td>";
		$html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> fl </label></td>";
		$html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> 6, 5 - 12, 0 </label></td>";
		$html .= "</tr>";
		 
		$html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
		$html .= "  <td style='width: 55%;'><label class='lab1' ><span> IDP <input id='champ22' type='number' step='any'  min='0' max='50' readonly> </span></label></td>";
		$html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> g/dl </label></td>";
		$html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> 9, 0 - 17, 0 </label></td>";
		$html .= "</tr>";
		 
		$html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
		$html .= "  <td style='width: 55%;'><label class='lab1' ><span> PCT <input id='champ23' type='number' step='any'  min='0' max='2' readonly> </span></label></td>";
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
		$html .="         <td style='width: 35%; '> <input id='champ24' type='number' readonly  step='any'> /mm<sup>3</sup> </td>";
		$html .="         <td style='width: 35%; '> <input id='champ25' type='number' readonly step='any'  min='0' max='100'> % </td>";
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
		
		 
		$html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
		if($this->layout()->user['role'] == 'biologiste'){
			$html .= "  <td colspan='2' ><label style='height: 80px;' ><span style='font-size: 16px; float: left;  margin-left: 30px;'> Commentaire:  </span> <textarea id='commentaire_hemogramme' style='max-height: 57px; min-height: 57px; max-width: 400px; min-width: 400px; margin-left: 30px;' > </textarea> </label></td>";
		}else{
			$html .= "  <td colspan='2' ><label style='height: 80px;' ><span style='font-size: 16px; float: left;  margin-left: 30px;'> Commentaire:  </span> <textarea id='commentaire_hemogramme' style='max-height: 57px; min-height: 57px; max-width: 400px; min-width: 400px; margin-left: 30px;' readonly> </textarea> </label></td>";
		}
		$html .= "  <td style='width: 30%;'><label style='padding-top: 5px; width: 80%; height: 80px; font-size: 14px;'>  </label></td>";
		$html .= "</tr>";
		 
		$html .= "</table> </td> </tr>";
		 
		 
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
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_gsrh_groupage' readonly> </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;'  class='iconeValidationInterfaceVisual_2'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> Groupe <select name='groupe' id='groupe' disabled> <option >  </option> <option value='A' >A</option> <option value='B' >B</option> <option value='AB' >AB</option> <option value='O' >O</option> </select></span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'>  </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'>  </label></td>";
	    $html .= "</tr>";
	     
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> Rh&eacute;sus <select name='rhesus' id='rhesus' style='width: 125px;' disabled> <option >  </option> <option value='Rh+' >Rh&eacute;sus positif</option> <option value='Rh-' >Rh&eacute;sus n&eacute;gatif</option> </span></label></td>";
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
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_recherche_antigene' readonly> </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;'  class='iconeValidationInterfaceVisual_3'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> Pr&eacute;sence d'antig&egrave;ne <select name='antigene_d_faible' id='antigene_d_faible' disabled> <option >  </option> <option value='Positif' >Positif</option> <option value='Negatif' >N&eacute;gatif</option> </select></span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'>  </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'>  </label></td>";
	    $html .= "</tr>";
	    $html .= "</table>";
	    
	    $html .= "<table style='width: 100%;'>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 70%;'><label class='lab1'><span style='font-weight: bold; width: 100%; text-align: right;'>&raquo; Conclusion :  <input id='conclusion_antigene_d_faible' type='text' step='any' style='width: 70%; float: right; text-align: left;' maxlength='45' disabled> </span></label></td>";
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
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_test_combs_direct' readonly> </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;'  class='iconeValidationInterfaceVisual_4'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> Test <select name='test_combs_direct' id='test_combs_direct' onchange='getTestCombsDirect(this.value)' disabled> <option >  </option> <option value='Positif' >Positif</option> <option value='Negatif' >N&eacute;gatif</option> </select></span></label></td>";
	    $html .= "  <td style='width: 10%;'><label class='lab2' style='padding-top: 5px; text-align: right;' > <span class='titre_combs_direct' style='display: none;'> Titre </span> </label></td>";
	    $html .= "  <td style='width: 45%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> <input class='titre_combs_direct' id='titre_combs_direct' type='text' style='display: none;' readonly> </label></td>";
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
		$html .= "  <td style='width: 25%;'><label class='lab2' style='padding-top: 5px; text-align: right; '>  Titre <input id='titre_combs_indirect_1' type='text'> </label></td>";
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
		$html .= "  <td colspan='2' class='commentaire_protect' ><label style='height: 80px;' ><span style='font-size: 16px; float: left;  margin-left: 30px;'> Commentaire:  </span> <textarea id='commentaire_test_combs_indirect' style='max-height: 57px; min-height: 57px; max-width: 400px; min-width: 400px; margin-left: 30px;' > </textarea> </label></td>";
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
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_test_compatibilite' readonly> </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;'  class='iconeValidationInterfaceVisual_6'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> Test <select name='test_compatibilite' id='test_compatibilite' onchange='getTestCompatibilite(this.value)' style='width: 127px;' disabled> <option >  </option> <option value='Compatible' >Compatible</option> <option value='Non compatible' >Non compatible</option> </select></span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> <span class='titre_test_compatibilite' style='display: none;'> Poche </span> </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'>  <input class='titre_test_compatibilite' id='titre_test_compatibilite' type='text' style='display: none;' readonly> </label></td>";
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
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_vitesse_sedimentation' readonly> </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;'  class='iconeValidationInterfaceVisual_7'> </td>";
	    $html .= "</tr>";
	    $html .= "</table>";
	    
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    $html .= "<table style='width: 100%;'>";
	     
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 35%;'><label class='lab1' ><span style='font-weight: bold;'>  1<sup>&egrave;re</sup> heure <input type='number' id='vitesse_sedimentation' name='vitesse_sedimentation' style='width: 80px;' readonly > mm </span></label></td>";
	    $html .= "  <td style='width: 35%;'><label class='lab2' ><span style='font-weight: bold;'>  2<sup>&egrave;me</sup> heure <input type='number' id='vitesse_sedimentation_2' name='vitesse_sedimentation_2' style='width: 80px;' readonly > mm </span></label></td>";
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
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_test_demmel' readonly> </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;'  class='iconeValidationInterfaceVisual_8'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> Test <select name='test_demmel' id='test_demmel' disabled> <option >  </option> <option value='Positif' >Positif</option> <option value='Negatif' >N&eacute;gatif</option> </select> </span></label></td>";
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
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_taux_reticulocytes' readonly> </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;'  class='iconeValidationInterfaceVisual_9'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> TR <input name='taux_reticulocyte' id='taux_reticulocyte' type='number' readonly> </span></label></td>";
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
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_goutte_epaisse' readonly> </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;'  class='iconeValidationInterfaceVisual_10'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> Goutte &eacute;paisse <select onchange='getDensiteGE(this.value)' name='goutte_epaisse' id='goutte_epaisse' disabled> <option >  </option> <option value='Positif' >Positif</option> <option value='Negatif' >N&eacute;gatif</option> </select> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'>  </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'>  </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' id='goutte_epaisse_positif' style='width: 100%; display: none;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> Densit&eacute; parasitaire <input name='densite_parasitaire' id='densite_parasitaire' type='number' readonly> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> p/ul </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'>  </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<script>  function getDensiteGE(valeur){ if(valeur == 'Positif'){ $('#goutte_epaisse_positif').toggle(true); }else{ $('#goutte_epaisse_positif').toggle(false); } } </script>";
	    
	    $html .= "</table>";
	     
	    $html .= "<table style='width: 100%;'>";
	     
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td colspan='2' ><label style='height: 80px;' ><span style='font-size: 16px; float: left;  margin-left: 30px;'> Commentaire:  </span> <textarea id='commentaire_goutte_epaisse' style='max-height: 57px; min-height: 57px; max-width: 400px; min-width: 400px; margin-left: 30px;' readonly> </textarea> </label></td>";
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
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_adenogramme' readonly> </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;'  class='iconeValidationInterfaceVisual_11'> </td>";
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
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_medulodramme' readonly> </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;'  class='iconeValidationInterfaceVisual_12'> </td>";
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
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_cytochimie_myeloperoxydase' readonly> </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;'  class='iconeValidationInterfaceVisual_13'> </td>";
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
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_tp_inr' readonly> </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;'  class='iconeValidationInterfaceVisual_14'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    //TQ - TQ - TQ ***************************************
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px;'>";
	    $html .= "  <td style='width: 55%;'>TQ (Temps de Quick) </span></label></td>";
	    $html .= "  <td style='width: 15%;'></td>";
	    $html .= "  <td style='width: 30%;'></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> T&eacute;moin <input type='number' id='temps_quick_temoin' step='any' readonly> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> S </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> N: 11 - 13  </label></td>";
	    $html .= "</tr>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> Patient <input type='number' id='temps_quick_patient' step='any' readonly> </span></label></td>";
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
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> Patient <input type='number' id='taux_prothrombine_patient' step='any' readonly> </span></label></td>";
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
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> Patient  <input type='number' id='inr_patient' step='any' readonly> </span></label></td>";
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
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_tca' readonly> </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;'  class='iconeValidationInterfaceVisual_15'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> Patient <input type='number' id='tca_patient' step='any' readonly> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> S </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> N: 25 &agrave; 41 </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> T&eacute;moin <input type='number' id='temoin_patient' step='any' readonly> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> S </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'>  </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> Ratio <input type='number' id='tca_ratio' step='any' readonly='true' readonly> </span></label></td>";
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
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_fibrinemie' readonly> </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;'  class='iconeValidationInterfaceVisual_16'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> Fibrin&eacute;mie <input id='fibrinemie' type='number' step='any' readonly> </span></label></td>";
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
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_temps_saignement' readonly> </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;'  class='iconeValidationInterfaceVisual_17'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> Temps de saignement <input id='temps_saignement' type='number' step='any' readonly> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> mn </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> N: 2 - 6 mn</label></td>";
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
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_facteur_8' style='padding-left: 8px;' readonly> </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;'  class='iconeValidationInterfaceVisual_18'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> facteur VIII <input id='facteur_8' type='number' step='any' readonly> </span></label></td>";
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
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_facteur_9' style='padding-left: 8px;' readonly> </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;'  class='iconeValidationInterfaceVisual_19'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> facteur IX <input id='facteur_9' type='number' step='any' readonly> </span></label></td>";
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
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_dimeres' style='padding-left: 8px;' readonly> </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;'  class='iconeValidationInterfaceVisual_20'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> d dimeres <input id='d_dimeres' type='number' step='any' readonly> </span></label></td>";
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
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_glycemie' style='padding-left: 8px;' readonly> </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;' class='iconeValidationInterfaceVisual_21' > </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'>  <input id='glycemie_1' type='number' step='any' readonly> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> g/l </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> N: 0,7 &agrave; 1,10 </label></td>";
	    $html .= "</tr>";
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'>  <input id='glycemie_2' type='number' step='any' readonly> </span></label></td>";
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
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_creatininemie' style='padding-left: 8px;' readonly> </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;' class='iconeValidationInterfaceVisual_22' > </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> cr&eacute;atinin&eacute;mie <input id='creatininemie' type='number' step='any' readonly> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> mg/l </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> N: H= 7 &agrave; 13 | F= 6 &agrave; 11</label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'>  <input id='creatininemie_umol' type='number' step='any' readonly> </span></label></td>";
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
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_azotemie' style='padding-left: 8px;' readonly> </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;'  class='iconeValidationInterfaceVisual_23'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> ur&eacute;e sanguine <input id='uree_sanguine' type='number' step='any' readonly> </span></label></td>";
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
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_acide_urique' style='padding-left: 8px;' readonly> </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;' class='iconeValidationInterfaceVisual_24'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> uric&eacute;mie <input id='acide_urique' type='number' step='any' readonly> </span></label></td>";
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
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_cholesterol_total' style='padding-left: 8px;' readonly> </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;'  class='iconeValidationInterfaceVisual_25'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'>  <input id='cholesterol_total_1' type='number' step='any' readonly> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> g/l </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'>  </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'>  <input id='cholesterol_total_2' type='number' step='any' readonly> </span></label></td>";
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
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_triglycerides' style='padding-left: 8px;' readonly> </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;'  class='iconeValidationInterfaceVisual_26'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'>  <input id='triglycerides_1' type='number' step='any' readonly> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> g/l </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%; font-size: 12px;'> H: 0,40 - 1,50 | F: 0,30 - 1,40 </label></td>";
	    $html .= "</tr>";
	     
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'>  <input id='triglycerides_2' type='number' step='any'  readonly> </span></label></td>";
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
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_cholesterol_HDL' style='padding-left: 8px;' readonly> </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;'  class='iconeValidationInterfaceVisual_27'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'>  <input id='cholesterol_HDL_1' type='number' step='any' readonly> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> g/l </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'>  </label></td>";
	    $html .= "</tr>";
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'>  <input id='cholesterol_HDL_2' type='number' step='any' readonly> </span></label></td>";
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
	    $html .= "  <td style='width: 55%;'><label class='lab1' style='background: #f9f9f9; padding-top: 5px; width: 100%; margin-left: 8px;' ><span style='font-weight: bold;'> &raquo; Rapport: CHOL/HDL <input id='rapport_chol_hdl' type='number' step='any' readonly> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='background: #f9f9f9; padding-top: 5px;'>  </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='background: #f9f9f9; padding-top: 5px; width: 75%;'> N: < 4,5 </label></td>";
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
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_cholesterol_LDL' style='padding-left: 8px;' readonly> </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;'  class='iconeValidationInterfaceVisual_28'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    	  
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'>  <input id='cholesterol_LDL_1' type='number' step='any' readonly> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> g/l </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'>  </label></td>";
	    $html .= "</tr>";
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'>  <input id='cholesterol_LDL_2' type='number' step='any'  readonly> </span></label></td>";
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
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_lipides_totaux' readonly> </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;'  class='iconeValidationInterfaceVisual_30'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px;'>";
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 20px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_lipides_totaux' readonly> </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> lipides totaux <input id='lipides_totaux' type='number' step='any' readonly> </span></label></td>";
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
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_ionogramme' readonly> </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;'  class='iconeValidationInterfaceVisual_31'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> Sodium Sanguin ou Natr&eacute;mie <input id='sodium_sanguin' type='number' step='any' readonly> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> mmol/l </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> N: 136 &agrave; 145 </label></td>";
	    $html .= "</tr>";
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> Potassium Sanguin ou Kali&eacute;mie <input id='potassium_sanguin' type='number' step='any' readonly> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> mmol/l </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> N: 3,5 &agrave; 5,1 </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> Chlore Sanguin ou Chlor&eacute;mie <input id='chlore_sanguin' type='number' step='any' readonly> </span></label></td>";
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
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_calcemie' readonly> </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;' class='iconeValidationInterfaceVisual_32'> </td>";
	    $html .= "</tr>";
	    
	    $html .= "</table>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<table style='width: 100%;'>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 45%;'><label class='lab1' ><span style='font-weight: bold;'> Calc&eacute;mie <input id='calcemie' type='number' step='any' readonly> </span></label></td>";
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
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_magnesemie' readonly> </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;' class='iconeValidationInterfaceVisual_33'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold; '> magn&eacute;s&eacute;mie <input id='magnesemie' type='number' step='any' readonly> </span></label></td>";
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
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_phosphoremie' readonly> </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;' class='iconeValidationInterfaceVisual_34'> </td>";
	    $html .= "</tr>";
	    
	    $html .= "</table>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	     
	    $html .= "<table style='width: 100%;'>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 45%;'><label class='lab1' ><span style='font-weight: bold;'> Phosphor&eacute;mie  <input id='phosphoremie' type='number' step='any' readonly> </span></label></td>";
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
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_tgo_asat' readonly> </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;' class='iconeValidationInterfaceVisual_35'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold; '> TGO/ASAT <input id='tgo_asat' type='number' step='any' readonly> </span></label></td>";
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
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_tgp_alat' readonly> </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;' class='iconeValidationInterfaceVisual_36'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold; '> TGP/ALAT <input id='tgp_alat' type='number' step='any' readonly> </span></label></td>";
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
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_phosphatage_alcaline' readonly> </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;' class='iconeValidationInterfaceVisual_38'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 50%;'><label class='lab1' ><span style='font-weight: bold; '> Phosphatage alcaline <input id='phosphatage_alcaline' type='number' step='any' readonly> </span></label></td>";
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
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_gama_gt_ygt' readonly> </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;' class='iconeValidationInterfaceVisual_39'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold; '> Gama GT <input id='gama_gt' type='number' step='any' readonly> </span></label></td>";
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
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_fer_serique' readonly> </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;' class='iconeValidationInterfaceVisual_40'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 50%;'><label class='lab1' ><span style='font-weight: bold; '> fer s&eacute;rique <input id='fer_serique_ug' type='number' step='any' readonly> </span></label></td>";
	    $html .= "  <td style='width: 10%;'><label class='lab2' style='padding-top: 5px;'> ug/dl </label></td>";
	    $html .= "  <td style='width: 40%;'><label class='lab3' style='padding-top: 5px; width: 85%;'> N: H: 64,8  &agrave; 175 - F: 50,3 &agrave; 170 </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 50%;'><label class='lab1' ><span style='font-weight: bold; '> fer s&eacute;rique <input id='fer_serique_umol' type='number' step='any' readonly> </span></label></td>";
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
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_ferritinine' readonly> </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;' class='iconeValidationInterfaceVisual_41'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold; '> ferritinine <input id='ferritinine' type='number' step='any' readonly> </span></label></td>";
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
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_bilirubine_totale_directe' readonly> </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;' class='iconeValidationInterfaceVisual_42'> </td>";
	    $html .= "</tr>";
	    $html .= "</table>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<table style='width: 100%;'>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 8%;'><label class='lab1'><span style='font-weight: bold; '> </span></label></td>";
	    $html .= "  <td style='width: 42%;'><label class='lab1'><span style='font-weight: bold; '> Bilirubine totale <input id='bilirubine_totale' type='number' step='any' readonly> mg/l </span></label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab2' style='padding-top: 5px;'><span style='font-weight: bold; '> <input id='bilirubine_totale_auto' type='number' step='any' tabindex='3' readonly> umol/l </span></label></td>";
	    $html .= "  <td style='width: 20%;'><label class='lab3' style='padding-top: 5px; width: 80%;'>  </label></td>";
	    $html .= "</tr>";
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 8%;'><label class='lab1'><span style='font-weight: bold; '> </span></label></td>";
	    $html .= "  <td style='width: 42%;'><label class='lab1'><span style='font-weight: bold; '> Bilirubine directe <input id='bilirubine_directe' type='number' step='any' readonly> mg/l </span></label></td>";
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
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_hemo_gly_hbac' readonly> </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;' class='iconeValidationInterfaceVisual_43'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 50%;'><label class='lab1'><span style='font-weight: bold; '>  <input id='hemoglobine_glyquee_hbac' type='number' step='any' readonly > </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> % </label></td>";
	    $html .= "  <td style='width: 35%;'><label class='lab3' style='padding-top: 5px; width: 89%; font-size: 13px;'> HbA1C DCCT N: 4,27 - 6,07 </label></td>";
	    $html .= "</tr>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 50%;'><label class='lab1' ><span style='font-weight: bold; '>  <input id='hemoglobine_glyquee_hbac_mmol' type='number' step='any' readonly > </span></label></td>";
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
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_electro_hemo' readonly> </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;' class='iconeValidationInterfaceVisual_44'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "</table> </td> </tr>";
	    
	    
	    $html .= "<tr> <td align='center'>";
	    $html .= "<table id='electro_hemo' style='width: 100%; margin-left: 5px;'>";
	
	    $html .= "<tr id='electro_hemo_1' class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 45%;'><label class='lab1'><span style='font-weight: bold; '>  <input id='electro_hemo_label_1' type='text' style='font-weight: bold; padding-right: 5px; margin-right: 30px;' readonly > </span></label></td>";
	    $html .= "  <td style='width: 35%;'><label class='lab2' style='padding-top: 5px;'> <input id='electro_hemo_valeur_1' type='number' step='any' readonly> % </label></td>";
	    $html .= "  <td style='width: 20%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> </label></td>";
	    $html .= "</tr>";
	    
        $html .= "<tr class='ligneAnanlyse' id='electro_hemo_mp' style='width: 100%; visibility: hidden; line-height: 2px;'>";
	    $html .= "  <td style='width: 45%;'> <div style='float: left; width: 25%; text-align: center; font-weight: bold; font-size: 25px;'> <div style='float: left; width: 50%; cursor: pointer; ' id='electro_hemo_moins'> - </div> <div style=' float: left; width: 45%; cursor: pointer;'  id='electro_hemo_plus'> + </div> </div> </label></td>";
	    $html .= "  <td style='width: 35%;'></td>";
	    $html .= "  <td style='width: 20%;'></td>";
	    $html .= "</tr>";
	    
	    $html .= "</table> ";
	    
	    $html .= "<table id='conclusion_resultat_electro_hemo' style='width: 100%; margin-left: 5px;'>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 70%;'><label class='lab1'><span style='font-weight: bold; width: 100%; text-align: right;'> Conclusion :  <input id='conclusion_electro_hemo_valeur' type='text' step='any' style='width: 70%; float: right; text-align: left;'> </span></label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 86%;'> </label></td>";
	    $html .= "</tr>";
	    $html .= "</table> ";
	    
	    $html .= "</td> </tr>";
	    
		return $html;
	}
	
	/**
	 * analyse 45
	 */
	public function electrophorese_preteines_45(){
	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    $html .= "<tr class='ligneAnanlyse labelTypeMateriel' style='width: 100%; font-family: times new roman; font-size: 15px; margin-top: -45px;'>";
	    $html .= "  <td style='width: 45%;'> <label> Mat&eacute;riel utilis&eacute;</label> </td>";
	    $html .= "  <td colspan='2' style='width: 35%;'> </td>";
	    $html .= "</tr>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px;'>";
	    $html .= "  <td style='width: 35%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_electro_proteine' tabindex='1' readonly> </div> </td>";
	    $html .= "  <td colspan='3' style='width: 45%;' class='iconeValidationInterfaceVisual_45'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 35%;'><label><span style='font-weight: bold; '> Albumine <input id='albumine' type='number' step='any' tabindex='1' readonly > </span></label></td>";
	    $html .= "  <td style='width: 10%;'><label style='padding-top: 5px;'> % </label></td>";
	    $html .= "  <td style='width: 15%;'><label style='padding-top: 5px;'> <input id='albumine_abs' type='number' step='any' readonly='true' readonly > </label></td>";
	    $html .= "  <td style='width: 40%;'><label style='padding-top: 5px; width: 50%; padding-left: 55px; font-size: 14px;'> 40,2 - 47,6 </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td ><label><span style='font-weight: bold; '> Alpha 1 <input id='alpha_1' type='number' step='any' tabindex='2' readonly> </span></label></td>";
	    $html .= "  <td ><label style='padding-top: 5px;'> % </label></td>";
	    $html .= "  <td ><label style='padding-top: 5px;'> <input id='alpha_1_abs' type='number' step='any' readonly > </label></td>";
	    $html .= "  <td ><label style='padding-top: 5px; width: 50%; padding-left: 55px; font-size: 14px;'> 2,1 - 3,5 </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td ><label><span style='font-weight: bold; '> Alpha 2 <input id='alpha_2' type='number' step='any' tabindex='3' readonly > </span></label></td>";
	    $html .= "  <td ><label style='padding-top: 5px;'> % </label></td>";
	    $html .= "  <td ><label style='padding-top: 5px;'> <input id='alpha_2_abs' type='number' step='any' readonly  > </label></td>";
	    $html .= "  <td ><label style='padding-top: 5px; width: 50%; padding-left: 55px; font-size: 14px;'> 5,1 - 8,5 </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td ><label><span style='font-weight: bold; '> Beta 1 <input id='beta_1' type='number' step='any' tabindex='4' readonly> </span></label></td>";
	    $html .= "  <td ><label style='padding-top: 5px;'> % </label></td>";
	    $html .= "  <td ><label style='padding-top: 5px;'> <input id='beta_1_abs' type='number' step='any' readonly > </label></td>";
	    $html .= "  <td ><label style='padding-top: 5px; width: 50%; padding-left: 55px; font-size: 14px;'> 3,4 - 5,2 </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td ><label><span style='font-weight: bold; '> Beta 2 <input id='beta_2' type='number' step='any' tabindex='5' readonly> </span></label></td>";
	    $html .= "  <td ><label style='padding-top: 5px;'> % </label></td>";
	    $html .= "  <td ><label style='padding-top: 5px;'> <input id='beta_2_abs' type='number' step='any' readonly > </label></td>";
	    $html .= "  <td ><label style='padding-top: 5px; width: 50%; padding-left: 55px; font-size: 14px;'> 2,3 - 4,7 </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td ><label><span style='font-weight: bold; '> Gamma <input id='gamma' type='number' step='any' tabindex='6' readonly > </span></label></td>";
	    $html .= "  <td ><label style='padding-top: 5px;'> % </label></td>";
	    $html .= "  <td ><label style='padding-top: 5px;'> <input id='gamma_abs' type='number' step='any' readonly > </label></td>";
	    $html .= "  <td ><label style='padding-top: 5px; width: 50%; padding-left: 55px; font-size: 14px;'> 8,0 - 13,5 </label></td>";
	    $html .= "</tr>";
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td colspan='4' style='height: 3px;'></td>";
	    $html .= "</tr>";
	     
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td colspan='2' ><label><span style='font-size: 16px;'> Proteine totale:  </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label style='padding-top: 5px;'> <input id='proteine_totale' type='number' step='any'  tabindex='7' readonly > </label></td>";
	    $html .= "  <td style='width: 40%;'><label style='padding-top: 5px; width: 80%; font-size: 14px;'> g/dL </label></td>";
	    $html .= "</tr>";

	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td colspan='3' ><label style='height: 80px;' ><span style='font-size: 16px; float: left;  margin-left: 30px;'> Commentaire:  </span> <textarea id='commentaire_electrophorese_proteine' style='max-height: 57px; min-height: 57px; max-width: 400px; min-width: 400px; margin-left: 30px;' tabindex='8' readonly> </textarea> </label></td>";
	    $html .= "  <td style='width: 40%;'><label style='padding-top: 5px; width: 80%; height: 80px; font-size: 14px;'>  </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "</table> </td> </tr>";
	
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
	    $html .= "  <td style='width: 45%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_albuminemie' tabindex='1' readonly> </div> </td>";
	    $html .= "  <td colspan='3' style='width: 45%;' class='iconeValidationInterfaceVisual_46'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '> Albumin&eacute;mie <input id='albuminemie' type='number' step='any' tabindex='2' readonly> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> g/l </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'>N : 35 - 53 </label></td>";
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
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_albumine_urinaire' tabindex='1' readonly> </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;' class='iconeValidationInterfaceVisual_47'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '> Albumine <select id='albumine_urinaire' tabindex='2' onchange='getAlbumineUrinaireVal(this.value)' style='font-size: 14px;' disabled> <option >  </option> <option value='positif'> Positif </option> <option value='negatif'> N&eacute;gatif </option> </select>  </span></label></td>";
	    $html .= "  <td style='width: 5%;'><label class='lab2' style='padding-top: 5px;'>  </label></td>";
	    $html .= "  <td style='width: 40%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> <select id='albumine_urinaire_degres' style='display:none; width: 50px; font-weight: bold; padding-left: 5px;' disabled> <option value='1+'> 1+ </option> <option value='2+'> 2+ </option> <option value='3+'> 3+ </option> <option value='4+'> 4+ </option> </select> </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '> Sucre   <select id='sucre_urinaire' tabindex='3' onchange='getSucreUrinaireVal(this.value)' style='font-size: 14px;' disabled> <option >  </option> <option value='positif'> Positif </option> <option value='negatif'> N&eacute;gatif </option> </select> </span></label></td>";
	    $html .= "  <td style='width: 5%;'><label class='lab2' style='padding-top: 5px;'>  </label></td>";
	    $html .= "  <td style='width: 40%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> <select id='sucre_urinaire_degres' style='display:none; width: 50px; font-weight: bold; padding-left: 5px;' disabled> <option value='1+'> 1+ </option> <option value='2+'> 2+ </option> <option value='3+'> 3+ </option> <option value='4+'> 4+ </option> </select> </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '> Corps c&eacute;tonique <select id='corps_cetonique_urinaire' tabindex='4' onchange='getCorpsCetoniqueUrinaireVal(this.value)' style='font-size: 14px;' disabled> <option >  </option> <option value='positif'> Positif </option> <option value='negatif'> N&eacute;gatif </option> </select> </span></label></td>";
	    $html .= "  <td style='width: 5%;'><label class='lab2' style='padding-top: 5px;'>  </label></td>";
	    $html .= "  <td style='width: 40%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> <select id='corps_cetonique_urinaire_degres' style='display:none; width: 50px; font-weight: bold; padding-left: 5px;' disabled> <option value='1+'> 1+ </option> <option value='2+'> 2+ </option> <option value='3+'> 3+ </option> <option value='4+'> 4+ </option> </select> </label></td>";
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
	    $html .= "  <td style='width: 45%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_protidemie' tabindex='1' readonly> </div> </td>";
	    $html .= "  <td colspan='3' style='width: 45%;' class='iconeValidationInterfaceVisual_48'> </td>";
	    $html .= "</tr>";
	    $html .= "</table>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<table style='width: 100%;'>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 42%;'><label class='lab1'><span style='font-weight: bold; '> Protid&eacute;mie <input id='protidemie' type='number' step='any' tabindex='2' readonly> </span></label></td>";
	    $html .= "  <td style='width: 10%;'><label class='lab2' style='padding-top: 5px;'> g/l </label></td>";
	    $html .= "  <td style='width: 48%;'><label class='lab3' style='padding-top: 5px; width: 89%; font-size: 13px;'> N: Adultes: 66 &agrave; 83 ; Nouveaux n&eacute;s: 52 &agrave; 91 </label></td>";
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
	    $html .= "  <td style='width: 45%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_proteinurie' tabindex='1' readonly > </div> </td>";
	    $html .= "  <td colspan='3' style='width: 45%;' class='iconeValidationInterfaceVisual_49'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style=''> <input id='proteinurie_1' style='margin-right: 5px;' type='number' step='any' tabindex='2' readonly> g/l <input id='proteinurie_2' style='margin-left: 30px;' type='number' step='any' tabindex='2' readonly> </span></label></td>";
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
	    $html .= "  <td style='width: 45%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_hlm_compte_daddis' tabindex='1' readonly> </div> </td>";
	    $html .= "  <td colspan='3' style='width: 45%;' class='iconeValidationInterfaceVisual_50'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '> H&eacute;maties <input id='hematies_hlm' type='number' step='any' tabindex='2' readonly > </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> /min </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> N < 2000 </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '> Leucocytes <input id='leucocytes_hlm' type='number' step='any' tabindex='3' readonly > </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> /min </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> N < 2000 </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table>";
	    
	    $html .= "<table style='width: 100%;'>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td colspan='2' ><label style='height: 80px;' ><span style='font-size: 16px; float: left;  margin-left: 30px;'> Commentaire:  </span> <textarea id='commentaire_hlm_compte_daddis' style='max-height: 57px; min-height: 57px; max-width: 400px; min-width: 400px; margin-left: 30px;' readonly> </textarea> </label></td>";
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
	    $html .= "  <td style='width: 45%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_beta_hcg' tabindex='1' readonly> </div> </td>";
	    $html .= "  <td colspan='3' style='width: 45%;' class='iconeValidationInterfaceVisual_51'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '> B&eacute;ta HCG <select name='beta_hcg_plasmatique' id='beta_hcg_plasmatique' disabled> <option >  </option> <option value='Positif' >Positif</option> <option value='Negatif' >N&eacute;gatif</option> </select>  </span></label></td>";
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
	    $html .= "  <td style='width: 45%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_psa' tabindex='1' readonly> </div> </td>";
	    $html .= "  <td colspan='3' style='width: 45%;' class='iconeValidationInterfaceVisual_52'> </td>";
	    $html .= "</tr>";
	    $html .= "</table>";
	     
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    $html .= "<table style='width: 100%;'>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 30%;'><label class='lab1'><span style='font-weight: bold; '>  <select name='psa_qualitatif' id='psa_qualitatif' tabindex='3' disabled> <option>  </option> <option value='Positif' >Positif</option> <option value='Negatif' >N&eacute;gatif</option> </select> </span></label></td>";
	    $html .= "  <td style='width: 40%;'><label class='lab2' style='padding-top: 5px;'><span style='font-weight: bold; '> Titre <input id='psa' type='number' step='any' tabindex='2' readonly> ng/ml </span></label></td>";
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
	    $html .= "  <td style='width: 45%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_crp' tabindex='1' readonly> </div> </td>";
	    $html .= "  <td colspan='3' style='width: 45%;'  class='iconeValidationInterfaceVisual_53'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "</table>";
	    
	    $html .= "<table style='width: 100%;'>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 45%;'><label class='lab1'><span style='font-weight: bold; '> CRP <select id='optionResultatCrp' onchange='getChoixResultatCrp(this.value);' readonly> <option value=''></option> <option value='positif'> Positif </option> <option value='negatif'> N&eacute;gatif </option> </select> </span></label></td>";
	    $html .= "  <td style='width: 25%;'><label class='lab2' style='padding-top: 5px;'> <span id='crpValeurResultatChamp' style='visibility: hidden;'> <input id='crpValeurResultat' type='number' step='any' tabindex='2' readonly> mg/l </span> </label></td>";
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
	    $html .= "  <td style='width: 45%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_facteurs_rhumatoides' tabindex='1' readonly> </div> </td>";
	    $html .= "  <td colspan='3' style='width: 45%;' class='iconeValidationInterfaceVisual_54'> </td>";
	    $html .= "</tr>";
	    $html .= "</table>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<table style='width: 100%;'>";
	     
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 40%;'><label class='lab1'><span style='font-weight: bold; '> Rf Latex <select name='facteurs_rhumatoides' id='facteurs_rhumatoides' disabled> <option>  </option> <option value='Positif' >Positif</option> <option value='Negatif' >N&eacute;gatif</option> </select>  </span></label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab2' style='padding-top: 5px;'><span style='font-weight: bold; '> Titre <input id='facteurs_rhumatoides_titre' type='number' step='any' style='width: 80px;' tabindex='3' readonly> UI/ml </span></label></td>";
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
	    $html .= "  <td style='width: 45%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_rf_waaler_rose' tabindex='1' readonly> </div> </td>";
	    $html .= "  <td colspan='3' style='width: 45%;' class='iconeValidationInterfaceVisual_55'> </td>";
	    $html .= "</tr>";
	    $html .= "</table>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE

	    
	    $html .= "<table style='width: 100%;'>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 40%;'><label class='lab1'><span style='font-weight: bold; '> Rf Waaler Rose <select name='rf_waaler_rose' id='rf_waaler_rose' disabled> <option>  </option> <option value='Positif' >Positif</option> <option value='Negatif' >N&eacute;gatif</option> </select>  </span></label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab2' style='padding-top: 5px;'><span style='font-weight: bold; '> Titre <input id='rf_waaler_rose_titre' type='number' step='any' style='width: 80px;' tabindex='3' disabled> ng/ml </span></label></td>";
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
	    $html .= "  <td style='width: 45%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_toxoplasmose' tabindex='1' readonly> </div> </td>";
	    $html .= "  <td colspan='3' style='width: 45%;'  class='iconeValidationInterfaceVisual_56'> </td>";
	    $html .= "</tr>";
	    
	    $html .= "</table>";

	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    $html .= "<table style='width: 100%;'>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 40%;'><label class='lab1'><span style='font-weight: bold; '> IGM  <select name='toxoplasmose_igm' id='toxoplasmose_igm' disabled> <option>  </option> <option value='Positif' >Positif</option> <option value='Negatif' >N&eacute;gatif</option> </select> </span></label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab2' style='padding-top: 5px;'><span style='font-weight: bold; '> Titre <input id='toxoplasmose_igm_titre' type='number' step='any' tabindex='2' readonly> </span></label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'>  </label></td>";
	    $html .= "</tr>";
	     
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 40%;'><label class='lab1'><span style='font-weight: bold; '> IGG <select name='toxoplasmose_igg' id='toxoplasmose_igg' disabled> <option>  </option> <option value='Positif' >Positif</option> <option value='Negatif' >N&eacute;gatif</option> </select>  </span></label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab2' style='padding-top: 5px;'><span style='font-weight: bold; '> Titre <input id='toxoplasmose_igg_titre' type='number' step='any' tabindex='3' readonly> </span></label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'>  </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "</table>";
	    
	    $html .= "<table style='width: 100%;'>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td colspan='2' ><label style='height: 80px;' ><span style='font-size: 16px; float: left;  margin-left: 30px;'> Commentaire:  </span> <textarea id='toxoplasmose_commentaire' style='max-height: 57px; min-height: 57px; max-width: 400px; min-width: 400px; margin-left: 30px;' readonly> </textarea> </label></td>";
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
	    $html .= "  <td style='width: 45%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_rubeole' tabindex='1' readonly> </div> </td>";
	    $html .= "  <td colspan='3' style='width: 45%;'  class='iconeValidationInterfaceVisual_57'> </td>";
	    $html .= "</tr>";
	     $html .= "</table>";
	  
	    $html .= "<table style='width: 100%;'>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 40%;'><label class='lab1'><span style='font-weight: bold; '> IGM  <select name='rubeole_igm' id='rubeole_igm' disabled> <option>  </option> <option value='Positif' >Positif</option> <option value='Negatif' >N&eacute;gatif</option> </select> </span></label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab2' style='padding-top: 5px;'><span style='font-weight: bold; '> Titre <input id='rubeole_igm_titre' type='number' step='any' tabindex='2' readonly> </span></label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'>  </label></td>";
	    $html .= "</tr>";
	     
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 40%;'><label class='lab1'><span style='font-weight: bold; '> IGG <select name='rubeole_igg' id='rubeole_igg' disabled> <option>  </option> <option value='Positif' >Positif</option> <option value='Negatif' >N&eacute;gatif</option> </select>  </span></label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab2' style='padding-top: 5px;'><span style='font-weight: bold; '> Titre <input id='rubeole_igg_titre' type='number' step='any' tabindex='3' readonly> </span></label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'>  </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "</table>";
	    
	    $html .= "<table style='width: 100%;'>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td colspan='2' ><label style='height: 80px;' ><span style='font-size: 16px; float: left;  margin-left: 30px;'> Commentaire:  </span> <textarea id='rubeole_commentaire' style='max-height: 57px; min-height: 57px; max-width: 400px; min-width: 400px; margin-left: 30px;' readonly> </textarea> </label></td>";
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
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input disabled type='text' id='type_materiel_culot_urinaire' tabindex='1' > </div> </td>";
	    $html .= "  <td style='width: 45%;'  class='iconeValidationInterfaceVisual_58'> </td>";
	    $html .= "</tr>";
	    $html .= "</table> </td> </tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	     
	    $html .= "<tr> <td align='center'>";
	    $html .= "<table id='culot_urinaire_tableau' style='width: 100%; margin-left: 5px;'>";
	    
	    $html .= "<tr id='culot_urinaire_ligne_1' class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 40%;'><label class='lab1 listeSelect'><span style='font-weight: bold; '> <select disabled onchange='listeElemtsCulotUrinaireSelect(1,this.value);' name='culot_urinaire_select' id='culot_urinaire_select' > <option value=0>  </option> <option value='1' >Leucocytes</option> <option value='2' >H&eacute;maties</option> <option value='3' >Cristaux</option> <option value='4' >Oeufs</option> <option value='5' >Parasites</option> </select> </span></label></td>";
	    $html .= "  <td style='width: 40%;'><label class='lab2 emplaceListeElemtsCUSelect' style='padding-top: 5px;'>  </label></td>";
	    $html .= "  <td style='width: 20%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> </label></td>";
	    $html .= "</tr>";
	     
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; line-height: 5px; visibility: hidden;'>";
	    $html .= "  <td style='width: 40%;'> <div style='float: left; width: 25%; text-align: center; font-weight: bold; font-size: 25px;'> <div style='float: left; width: 50%; cursor: pointer; display: none;' id='culot_urinaire_moins'> - </div> <div style=' float: left; width: 45%; cursor: pointer;'  id='culot_urinaire_plus'> + </div> </div> </label></td>";
	    $html .= "  <td style='width: 40%;'></td>";
	    $html .= "  <td style='width: 20%;'></td>";
	    $html .= "</tr>";
	     
	    $html .= "</table>";
	     
	    $html .= "<table id='conclusion_resultat_culot_urinaire' style='width: 100%; margin-left: 5px;'>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 90%;'><label class='lab1'><span style='font-weight: bold; width: 100%; text-align: right;'> Conclusion :  <input disabled id='conclusion_culot_urinaire_valeur' type='text' step='any' style='width: 80%; float: right; text-align: left;'> </span></label></td>";
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
	    $html .= "  <td style='width: 45%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_serologie_chlamydiae' tabindex='1' readonly> </div> </td>";
	    $html .= "  <td colspan='3' style='width: 45%;'  class='iconeValidationInterfaceVisual_59'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '>  <input id='serologie_chlamydiae' type='number' step='any' tabindex='2' readonly > </span></label></td>";
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
	    $html .= "  <td style='width: 45%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_serologie_syphilitique' tabindex='1' readonly> </div> </td>";
	    $html .= "  <td colspan='3' style='width: 45%;'  class='iconeValidationInterfaceVisual_60'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '> RPR  <select name='serologie_syphilitique_rpr' id='serologie_syphilitique_rpr' disabled> <option>  </option> <option value='Positif' >Positif</option> <option value='Negatif' >N&eacute;gatif</option> </select> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'>  </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'>  </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '> TPHA  <select name='serologie_syphilitique_tpha' id='serologie_syphilitique_tpha' disabled> <option>  </option> <option value='Positif' >Positif</option> <option value='Negatif' >N&eacute;gatif</option> </select> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> Titre <input class='serologie_syphilitique_tpha_titre' id='serologie_syphilitique_tpha_titre' type='text' style='height: 23px; margin-top: -1px; padding-left: 4px; text-align: left;' readonly> </label></td>";
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
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_aslo' tabindex='1' readonly> </div> </td>";
	    $html .= "  <td colspan='3' style='width: 45%;'  class='iconeValidationInterfaceVisual_61'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td colspan='2' style='width: 70%;'><label class='lab1'><span style='font-weight: bold; float: left; padding-left: 100px;'> Aslo <select name='aslo_select' id='aslo_select' disabled='true'> <option>  </option> <option value='Positif' >Positif</option> <option value='Negatif' >N&eacute;gatif</option> </select> </span>    <span style='float: right;'>  Titre <input id='aslo_titre' type='number' step='any' tabindex='2' style='width: 70px;' readonly> UI/ml </span> </label></td>";
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
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_widal' tabindex='1' readonly> </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;'  class='iconeValidationInterfaceVisual_62'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '> Typhi TO  <select name='widal_to' id='widal_to' disabled> <option>  </option> <option value='Positif' >Positif</option> <option value='Negatif' >N&eacute;gatif</option> </select> </span></label></td>";
	    $html .= "  <td colspan='2' style='width: 45%;'><label class='lab2' style='padding-top: 5px; width: 75%; padding-left: 40px;'> Titre: <input disabled id='widal_titre_to' style='width: 60%; padding-right: 5px;' type='text' tabindex='2'> </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '> Typhi TH <select name='widal_th' id='widal_th' disabled> <option>  </option> <option value='Positif' >Positif</option> <option value='Negatif' >N&eacute;gatif</option> </select>  </span></label></td>";
	    $html .= "  <td colspan='2' style='width: 45%;'><label class='lab2' style='padding-top: 5px; width: 75%; padding-left: 40px;'> Titre: <input disabled id='widal_titre_th' style='width: 60%; padding-right: 5px;' type='text' tabindex='3'> </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '> Paratyphi AO <select name='widal_ao' id='widal_ao' disabled> <option>  </option> <option value='Positif' >Positif</option> <option value='Negatif' >N&eacute;gatif</option> </select> </span></label></td>";
	    $html .= "  <td colspan='2' style='width: 45%;'><label class='lab2' style='padding-top: 5px; width: 75%; padding-left: 40px;'> Titre: <input disabled id='widal_titre_ao' style='width: 60%; padding-right: 5px;' type='text' tabindex='4'> </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '> Paratyphi AH <select name='widal_ah' id='widal_ah' disabled> <option>  </option> <option value='Positif' >Positif</option> <option value='Negatif' >N&eacute;gatif</option> </select> </span></label></td>";
	    $html .= "  <td colspan='2' style='width: 45%;'><label class='lab2' style='padding-top: 5px; width: 75%; padding-left: 40px;'> Titre: <input disabled id='widal_titre_ah' style='width: 60%; padding-right: 5px;' type='text' tabindex='5'> </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '> Paratyphi BO <select name='widal_bo' id='widal_bo' disabled> <option>  </option> <option value='Positif' >Positif</option> <option value='Negatif' >N&eacute;gatif</option> </select>  </span></label></td>";
	    $html .= "  <td colspan='2' style='width: 45%;'><label class='lab2' style='padding-top: 5px; width: 75%; padding-left: 40px;'> Titre: <input disabled id='widal_titre_bo' style='width: 60%; padding-right: 5px;' type='text' tabindex='6'> </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '> Paratyphi BH <select name='widal_bh' id='widal_bh' disabled> <option>  </option> <option value='Positif' >Positif</option> <option value='Negatif' >N&eacute;gatif</option> </select>  </span></label></td>";
	    $html .= "  <td colspan='2' style='width: 45%;'><label class='lab2' style='padding-top: 5px; width: 75%; padding-left: 40px;'> Titre: <input disabled id='widal_titre_bh' style='width: 60%; padding-right: 5px;' type='text' tabindex='7'> </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '> Paratyphi CO <select name='widal_co' id='widal_co' disabled> <option>  </option> <option value='Positif' >Positif</option> <option value='Negatif' >N&eacute;gatif</option> </select>  </span></label></td>";
	    $html .= "  <td colspan='2' style='width: 45%;'><label class='lab2' style='padding-top: 5px; width: 75%; padding-left: 40px;'> Titre: <input disabled id='widal_titre_co' style='width: 60%; padding-right: 5px;' type='text' tabindex='8'> </label></td>";
	    $html .= "</tr>";
	     
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '> Paratyphi CH <select name='widal_ch' id='widal_ch' disabled> <option>  </option> <option value='Positif' >Positif</option> <option value='Negatif' >N&eacute;gatif</option> </select>  </span></label></td>";
	    $html .= "  <td colspan='2' style='width: 45%;'><label class='lab2' style='padding-top: 5px; width: 75%; padding-left: 40px;'> Titre: <input disabled id='widal_titre_ch' style='width: 60%; padding-right: 5px;' type='text' tabindex='9'> </label></td>";
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
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_ag_hbs' tabindex='1' readonly> </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;'  class='iconeValidationInterfaceVisual_63'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '> Ag Hbs  <select name='ag_hbs' id='ag_hbs' disabled> <option>  </option> <option value='Positif' >Positif</option> <option value='Negatif' >N&eacute;gatif</option> </select> </span></label></td>";
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
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_hiv' tabindex='1' readonly> </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;'  class='iconeValidationInterfaceVisual_64'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    	  
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 35%;'><label class='lab1'><span style='font-weight: bold; '> HIV <select id='hiv' disabled> <option >  </option> <option value='Positif' >Positif</option> <option value='Negatif' >N&eacute;gatif</option> </select> </span></label></td>";
	    $html .= "  <td style='width: 35%;'><label class='lab2' style='padding-top: 5px;'> <span style='float: left; '> Typage </span> <select id='hiv_typage' style='width: 120px;' disabled> <option >  </option> <option value='hiv_1' >HIV 1</option> <option value='hiv_2' >HIV 2</option>  <option value='indetermine' >Ind&eacute;termin&eacute;</option> </select> </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 40%;'>  </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
	    return $html;
	}

	/**
	 * analyse 65
	 */
	public function pv_65(){

	    $html  = "<tr> <td align='center'>";
	    $html .= "<table style='width: 100%;'>";
	    
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    $html .= "<tr class='ligneAnanlyse labelTypeMateriel' style='width: 100%; font-family: times new roman; font-size: 15px; margin-top: -45px;'>";
	    $html .= "  <td style='width: 55%;'> <label> Mat&eacute;riel utilis&eacute;</label> </td>";
	    $html .= "  <td colspan='2' style='width: 35%;'> </td>";
	    $html .= "</tr>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px;'>";
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_pv' tabindex='1' disabled > </div> </td>";
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
	                      <select id='aspect_pertes_abondance_pv' style='width: 200px;' disabled>
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
	    $html .= "  <td style='width: 55%;'><label class='lab1' style='padding-top: 5px;'><span style='font-weight: bold;'> Aspect des pertes (Couleurs) </span></label></td>";
	    $html .= "  <td style='width: 15%;'>
	                    <label class='lab2' style='padding-top: 5px;'>
	                      <select id='aspect_pertes_couleurs_pv' style='width: 200px;' disabled>
	                        <option> </option>
	                        <option value=1 >Blanch&acirc;tres</option>
	                        <option value=2 >Stri&eacute;es de sang</option>
	                        <option value=3 >Caillebott&eacute;es</option>
	                        <option value=4 >Marron</option>
	                      </select>
	                    </label>
	                </td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> </label></td>";
	    $html .= "</tr>";
	     
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' style='padding-top: 5px;'><span style='font-weight: bold;'> Aspect des pertes (Odeurs) </span></label></td>";
	    $html .= "  <td style='width: 15%;'>
	                    <label class='lab2' style='padding-top: 5px;'>
	                      <select id='aspect_pertes_odeurs_pv' style='width: 200px;' disabled>
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
	                      <select id='aspect_organe_pv' style='width: 200px;' disabled>
	                        <option> </option>
	                        <option value=1 >Col sain</option>
	                        <option value=2 >Col inflamm&eacute;</option>
  	                        <option value=3 >Col saignant au contact</option>
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
	                      <select id='leucocytes_champ_pv' style='width: 90px;' disabled>
	                        <option> </option>
	                        <option value=1 >Rares</option>
	                        <option value=2 >Absentes</option>
	                      </select>
	                    </label>
	                </td>";
	    $html .= "  <td style='width: 17%;'>
	                    <label class='lab2' style='padding-top: 5px;'>
	                      <input type='number' id='leucocytes_champ_valeur_pv' style='width: 45px; padding-left: 2px; height: 20px;' max=999 min=1 disabled>/champs
	                    </label>
	                </td>";
	    $html .= "  <td style='width: 11%;'><label class='lab1' style='font-weight: bold; text-align: right; padding-top: 5px;'> H&eacute;maties </label></td>";
	    $html .= "  <td style='width: 18%;'>
	                    <label class='lab2' style='padding-top: 5px;'>
	                      <select id='hematies_champ_pv' style='width: 90px;' disabled>
	                        <option> </option>
	                        <option value=1 >Absentes</option>
	                      </select>
	                    </label>
	                </td>";
	    $html .= "  <td style='width: 16%;'>
	                    <label class='lab2' style='padding-top: 5px; width: 73%;' title='champs'>
	                      <input type='number' id='hematies_champ_valeur_pv' style='width: 45px; padding-left: 2px; height: 20px;' max=999 min=1 disabled>/c.
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
	                      <select id='cellules_epitheliales_champ_pv' style='width: 90px;' disabled>
	                        <option> </option>
	                        <option value=1 >Absentes</option>
	                      </select>
	                    </label>
	                </td>";
	    $html .= "  <td style='width: 17%;'>
	                    <label class='lab2' style='padding-top: 5px;'>
	                      <input type='number' id='cellules_epitheliales_champ_valeur_pv' style='width: 45px; padding-left: 2px; height: 20px;' max=999 min=1 disabled>/champs
	                    </label>
	                </td>";
	    $html .= "  <td style='width: 20%;'><label class='lab1' style='font-weight: bold; text-align: right; padding-top: 5px;'  title='Trichomonas vaginalis' > Trichomo. vaginalis </label></td>";
	    $html .= "  <td style='width: 25%;'>
	                    <label class='lab2' style='padding-top: 5px; width: 83%;'>
	                      <select id='trichomonas_vaginalis_pv' style='width: 120px;' disabled>
	                        <option> </option>
	                        <option value=1 >Pr&eacute;sence</option>
	                        <option value=2 >Absence</option>
	                      </select>
	                    </label>
	                </td>";
	    $html .= "</tr>";
	    $html .= "</table>";
	    /*
	     * Levures/Filaments mycéliens && Gardnerella vaginalis
	     */
	    $html .= "<table style='width: 100%;'>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 25%;'><label class='lab1' style='padding-top: 5px;' ><span style='font-weight: bold;' title='Levures/Filaments myc&eacute;liens'> Lev./Fil. myc&eacute;liens </span></label></td>";
	    $html .= "  <td style='width: 30%;'>
	                    <label class='lab2' style='padding-top: 5px;'>
	                      <select id='levures_filaments_myceliens_pv' style='width: 120px;' disabled>
	                        <option> </option>
	                        <option value=1 >Pr&eacute;sence</option>
	                        <option value=2 >Absence</option>
	                      </select>
	                    </label>
	                </td>";
	    $html .= "  <td style='width: 20%;'><label class='lab1' style='font-weight: bold; text-align: right; padding-top: 5px;' > Gardnerella vaginalis </label></td>";
	    $html .= "  <td style='width: 25%;'>
	                    <label class='lab2' style='padding-top: 5px; width: 83%;'>
	                      <select id='gardnerella_vaginalis_pv' style='width: 120px;' disabled>
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
	                      <select id='mobiluncus_spp_pv' style='width: 120px;' disabled>
	                        <option> </option>
	                        <option value=1 >Pr&eacute;sence</option>
	                        <option value=2 >Absence</option>
	                      </select>
	                    </label>
	                </td>";
	    $html .= "  <td style='width: 20%;'><label class='lab1' style='font-weight: bold; text-align: right; padding-top: 5px;' > Clue cells </label></td>";
	    $html .= "  <td style='width: 25%;'>
	                    <label class='lab2' style='padding-top: 5px; width: 83%;'>
	                      <select id='clue_cells_pv' style='width: 120px;' disabled>
	                        <option> </option>
	                        <option value=1 >Pr&eacute;sence</option>
	                        <option value=2 >Absence</option>
	                      </select>
	                    </label>
	                </td>";
	    $html .= "</tr>";
	    $html .= "</table>";
	     
	    /*
	     * Lactobacillus && Autre flore
	     */
	    $html .= "<table style='width: 100%;'>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 25%;'><label class='lab1' style='padding-top: 5px;'><span style='font-weight: bold;' > Lactobacillus </span></label></td>";
	    $html .= "  <td style='width: 30%;'>
	                    <label class='lab2' style='padding-top: 5px;'>
	                      <select id='lactobacillus_pv' style='width: 120px;' disabled>
	                        <option> </option>
	                        <option value=1 >Pr&eacute;sence</option>
	                        <option value=2 >Absence</option>
	                      </select>
	                    </label>
	                </td>";
	    $html .= "  <td style='width: 12%;'><label class='lab1' style='font-weight: bold; text-align: right; padding-top: 5px;' > Autre flore </label></td>";
	    $html .= "  <td style='width: 33%;'>
	                    <label class='lab2' style='padding-top: 5px; width: 87%;'>
	                      <select id='autre_flore_pv' style='width: 170px; font-size: 13px;' disabled>
	                        <option> </option>
	                        <option value=1 >Bacille &agrave; gram n&eacute;gatif</option>
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
	    $html .= "  <td style='width: 30%;'>
	                    <label class='lab2' style='padding-top: 5px;'>
	                      <select id='flore_pv' style='width: 120px;' onchange='getChampFloreNote(this.value)' disabled>
	                        <option> </option>
	                        <option value=1 >Type 1</option>
	                        <option value=2 >Type 2</option>
	                        <option value=3 >Type 3</option>
	                        <option value=4 >Type 4</option>
	                      </select>
	                    </label>
	                </td>";
	    $html .= "  <td style='width: 45%;'>
	                    <label class='lab2' style='padding-top: 5px; width: 91%;'>
	                      <span style='font-weight: bold; font-size: 20px; text-align: left; visibility: hidden;' class='flore_note_class_pv'> &#10145;
	                        <input type='text' id='flore_note_pv' style='width: 210px; text-align: left; font-size: 16px; padding-left: 2px;' disabled>
	                      </span>
	                    </label>
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
	                      <select id='culture_pv' style='width: 120px;' onchange='getNombreCultureIdentifier(this.value)' disabled>
	                        <option> </option>
	                        <option value=1 >Positive</option>
	                        <option value=2 >N&eacute;gative</option>
	            
	                      </select>
	                    </label>
	                </td>";
	    $html .= "  <td style='width: 12%;'><label class='lab1' style='font-weight: bold; text-align: right; padding-top: 5px;' > <span class='nombreCultureIdentifierABG' style='visibility:hidden;'>  Nombre </span></label></td>";
	    $html .= "  <td style='width: 36%;'><label class='lab2' style='padding-top: 5px; width: 88%;'><input class='nombreCultureIdentifierABG' id='nombreCultureIdentifierABG' type='number' min=1 max=3 disabled onchange='getChampsCultureIdentifierABG(this.value)' style='width: 35px; font-size: 19px; padding-left: 1px; visibility:hidden;' ></label></td>";
	    $html .= "</tr>";
	    $html .= "</table>";
	     
	    /*
	     * Les champs 'Identification'
	     */
	    $html .= "<table style='width: 100%;  display: none;' class='identificationCultureChamps identificationCultureChampsABR_1'  >";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 25%;'><label class='lab1' style='padding-top: 5px;'><span style='font-weight: bold;'> Identification </span></label></td>";
	    $html .= "  <td style='width: 27%;'>
	                    <label class='lab2' style='padding-top: 5px;'>
	                      <select id='identification_culture_pv' style='width: 190px;' onchange='getIconeAntibiogrammeIdentCulture(this.value,1)' disabled>
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
	     
	     
	    /*
	     * Ajouter les scripts pour la gestion des checkbox non cochés
	     * Pour la première colonne
	     */
	    $html .= '<script>  $("#choixFosfomycineFOSABG").click(function(){ if($(this).get(0).checked){ $("#fosfomycineFOSABG").attr("disabled", false); }else{ $("#fosfomycineFOSABG").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixVancomycineVAABG").click(function(){ if($(this).get(0).checked){ $("#vancomycineVAABG").attr("disabled", false); }else{ $("#vancomycineVAABG").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixColistineCSABG").click(function(){ if($(this).get(0).checked){ $("#colistineCSABG").attr("disabled", false); }else{ $("#colistineCSABG").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixKanamycineKABG").click(function(){ if($(this).get(0).checked){ $("#kanamycineKABG").attr("disabled", false); }else{ $("#kanamycineKABG").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixTobramycineTBABG").click(function(){ if($(this).get(0).checked){ $("#tobramycineTBABG").attr("disabled", false); }else{ $("#tobramycineTBABG").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixAmikacineANABG").click(function(){ if($(this).get(0).checked){ $("#amikacineANABG").attr("disabled", false); }else{ $("#amikacineANABG").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixNetilmycineABG").click(function(){ if($(this).get(0).checked){ $("#netilmycineABG").attr("disabled", false); }else{ $("#netilmycineABG").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixChloramphenicolCABG").click(function(){ if($(this).get(0).checked){ $("#chloramphenicolCABG").attr("disabled", false); }else{ $("#chloramphenicolCABG").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixTetracyclineTEABG").click(function(){ if($(this).get(0).checked){ $("#tetracyclineTEABG").attr("disabled", false); }else{ $("#tetracyclineTEABG").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixDoxycyclineDOABG").click(function(){ if($(this).get(0).checked){ $("#doxycyclineDOABG").attr("disabled", false); }else{ $("#doxycyclineDOABG").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixErythromycineEABG").click(function(){ if($(this).get(0).checked){ $("#erythromycineEABG").attr("disabled", false); }else{ $("#erythromycineEABG").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixLincomycineLABG").click(function(){ if($(this).get(0).checked){ $("#lincomycineLABG").attr("disabled", false); }else{ $("#lincomycineLABG").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixPristinamycinePTABG").click(function(){ if($(this).get(0).checked){ $("#pristinamycinePTABG").attr("disabled", false); }else{ $("#pristinamycinePTABG").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixAcideFusidiqueFAABG").click(function(){ if($(this).get(0).checked){ $("#acideFusidiqueFAABG").attr("disabled", false); }else{ $("#acideFusidiqueFAABG").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixAcideNalidixiqueNAABG").click(function(){ if($(this).get(0).checked){ $("#acideNalidixiqueNAABG").attr("disabled", false); }else{ $("#acideNalidixiqueNAABG").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixPefloxacinePEFABG").click(function(){ if($(this).get(0).checked){ $("#pefloxacinePEFABG").attr("disabled", false); }else{ $("#pefloxacinePEFABG").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixNorfloxacineNORABG").click(function(){ if($(this).get(0).checked){ $("#norfloxacineNORABG").attr("disabled", false); }else{ $("#norfloxacineNORABG").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixCiprofloxacineCIPABG").click(function(){ if($(this).get(0).checked){ $("#ciprofloxacineCIPABG").attr("disabled", false); }else{ $("#ciprofloxacineCIPABG").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixLEVABG").click(function(){ if($(this).get(0).checked){ $("#LEVABG").attr("disabled", false); }else{ $("#LEVABG").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixRifampicineRAABG").click(function(){ if($(this).get(0).checked){ $("#rifampicineRAABG").attr("disabled", false); }else{ $("#rifampicineRAABG").attr("disabled", true).val(""); } }) </script>';
	    $html .= '<script>  $("#choixCotrimoxazoleSXTABG").click(function(){ if($(this).get(0).checked){ $("#cotrimoxazoleSXTABG").attr("disabled", false); }else{ $("#cotrimoxazoleSXTABG").attr("disabled", true).val(""); } }) </script>';
	    
	    /**
	     * Partie recherches particulieres *** Partie recherches particulieres
	     * -------------------------------------------------------------------
	     * Partie recherches particulieres *** Partie recherches particulieres
	     */
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
	    $html .= "  <td style='width: 25%;'><label class='lab1' style='padding-top: 5px;'><span style='font-weight: bold;' title='Recherche directe de l\"antigene de chlamydia'> Rech. dir. ant. chlam. </span></label></td>";
	    $html .= "  <td style='width: 25%;'>
	                    <label class='lab2' style='padding-top: 5px;'>
	                      <select id='recherche_directe_antigene_chlamydia_pv' style='width: 120px;' disabled>
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
	     * Rech. dir. mycoplasmes && identification
	     */
	    $html .= "<table style='width: 100%;'>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 25%;'><label class='lab1' style='padding-top: 5px;'><span style='font-weight: bold;' title='Recherche directe mycoplasmes'> Rech. dir. mycoplasmes </span></label></td>";
	    $html .= "  <td style='width: 25%;'>
	                    <label class='lab2' style='padding-top: 5px;'>
	                      <select id='recherche_directe_mycoplasmes_pv' style='width: 120px;' onchange='getChampIdentificationRdmPositive(this.value)' disabled>
	                        <option> </option>
	                        <option value=1 >Positive</option>
	                        <option value=2 >N&eacute;gative</option>
	                      </select>
	                    </label>
	                </td>";
	    $html .= "  <td style='width: 50%;'>
	                    <label class='lab2' style='padding-top: 5px; width: 92%;'>
	                      <span style='font-weight: bold; text-align: left; visibility: hidden;'  class='identification_rdm_positive_class_pv'>  Identification
    	                    <select id='identification_rdm_positive_pv' style='width: 183px; font-size: 13px;' disabled>
    	                      <option> </option>
    	                      <option value=1 >Ureaplasma urealyticum</option>
    	                      <option value=2 >Mycoplasma hominis</option>
    	                    </select>
	                      </span>
	                    </label>
	                </td>";
	    $html .= "</tr>";
	    $html .= "</table>";
	     
	     
	     
	     
	     
	    /**
	     * Partie commentaire --- Partie commentaire
	     * -----------------------------------------
	     * Partie commentaire --- Partie commentaire
	     */
	     
	    $html .= "<table style='width: 100%; margin-top: 15px;'>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    if($this->layout()->user['role'] == 'biologiste' || $this->layout()->user['role'] == 'technicien'){
	        $html .= "<td style='width: 96%;'><label style='height: 140px;' ><span style='font-size: 16px; float: left;  margin-left: 30px;'> Commentaire:  </span> <textarea id='commentaire_pv' style='max-height: 100px; min-height: 100px; max-width: 560px; min-width: 560px; margin-left: 30px;' > </textarea> </label></td>";
	    }else{
	        $html .= "<td style='width: 96%;'><label style='height: 140px;' ><span style='font-size: 16px; float: left;  margin-left: 30px;'> Commentaire:  </span> <textarea id='commentaire_pv' style='max-height: 100px; min-height: 100px; max-width: 560px; min-width: 560px; margin-left: 30px;' disabled> </textarea> </label></td>";
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
	    $html .= '<table style="width: 100%;" class="designEnTeteAnalyse  blocChampAntiBioGrammeDuPVABG">';
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
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse  blocChampAntiBioGrammeDuPVABG">';
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
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse  blocChampAntiBioGrammeDuPVABG">';
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
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse  blocChampAntiBioGrammeDuPVABG">';
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
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse  blocChampAntiBioGrammeDuPVABG">';
	    $html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
	    $html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" title="Acide clavulanique + Amoxicilline AMC"> Acide clav. + Amoxi. AMC </span></label></td>';
	    $html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixAcideClavulaniqueAmoxicillineAMCABG" > </label></td>';
	    $html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
	    $html .= '      <select id="acideClavulaniqueAmoxicillineAMCABG" style="width: 120px;" disabled> ';
	    $html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
	    $html .= '      </select></label> </td>';
	    $html .= "</tr></table>";
	     
	    /*
	     * Gentamicine GM
	     */
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse  blocChampAntiBioGrammeDuPVABG">';
	    $html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
	    $html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > Gentamicine GM </span></label></td>';
	    $html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixGentamicineGMABG" > </label></td>';
	    $html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
	    $html .= '      <select id="gentamicineGMABG" style="width: 120px;" disabled> ';
	    $html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
	    $html .= '      </select></label> </td>';
	    $html .= "</tr></table>";
	     
	    /*
	     * Tic-Ac-Clav TCC
	     */
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse  blocChampAntiBioGrammeDuPVABG">';
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
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse  blocChampAntiBioGrammeDuPVABG">';
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
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse  blocChampAntiBioGrammeDuPVABG">';
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
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse  blocChampAntiBioGrammeDuPVABG">';
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
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse  blocChampAntiBioGrammeDuPVABG">';
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
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse  blocChampAntiBioGrammeDuPVABG">';
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
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse  blocChampAntiBioGrammeDuPVABG">';
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
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse  blocChampAntiBioGrammeDuPVABG">';
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
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse  blocChampAntiBioGrammeDuPVABG">';
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
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse  blocChampAntiBioGrammeDuPVABG">';
	    $html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
	    $html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > Cefsulodine CFS </span></label></td>';
	    $html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixCefsulodineCFSABG" > </label></td>';
	    $html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
	    $html .= '      <select id="cefsulodineCFSABG" style="width: 120px;" disabled> ';
	    $html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
	    $html .= '      </select></label> </td>';
	    $html .= "</tr></table>";
	     
	    /*
	     * CFP
	     */
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse  blocChampAntiBioGrammeDuPVABG">';
	    $html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
	    $html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > CFP </span></label></td>';
	    $html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixCFPABG" > </label></td>';
	    $html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
	    $html .= '      <select id="CFPABG" style="width: 120px;" disabled> ';
	    $html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
	    $html .= '      </select></label> </td>';
	    $html .= "</tr></table>";
	
	    /*
	     * Ceftazidime CAZ
	     */
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse  blocChampAntiBioGrammeDuPVABG">';
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
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse  blocChampAntiBioGrammeDuPVABG">';
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
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse  blocChampAntiBioGrammeDuPVABG">';
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
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse  blocChampAntiBioGrammeDuPVABG">';
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
	     * Rifampicine RA
	     */
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse  blocChampAntiBioGrammeDuPVABG">';
	    $html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
	    $html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > Rifampicine RA </span></label></td>';
	    $html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixRifampicineRAABG" > </label></td>';
	    $html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
	    $html .= '      <select id="rifampicineRAABG" style="width: 120px;" disabled> ';
	    $html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
	    $html .= '      </select></label> </td>';
	    $html .= "</tr></table>";
	    /*
	     * Cotrimoxazole SXT
	     */
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse  blocChampAntiBioGrammeDuPVABG">';
	    $html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
	    $html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > Cotrimoxazole SXT </span></label></td>';
	    $html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixCotrimoxazoleSXTABG" > </label></td>';
	    $html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
	    $html .= '      <select id="cotrimoxazoleSXTABG" style="width: 120px;" disabled> ';
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
	     * Polymyxine --- Polymyxine
	     */
	    $html .= '<table style="width: 100%; margin-top: 15px;">';
	    $html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
	    $html .= '  <td style="width: 100%; text-align: left; font-weight: bold; font-size: 12px; font-style: italic;"> &#10148; Polymyxine </td>';
	    $html .= '</tr>';
	    $html .= '</table>';
	    /*
	     * Fosfomycine FOS
	     */
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse  blocChampAntiBioGrammeDuPVABG">';
	    $html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
	    $html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > Fosfomycine FOS </span></label></td>';
	    $html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixFosfomycineFOSABG" > </label></td>';
	    $html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
	    $html .= '      <select id="fosfomycineFOSABG" style="width: 120px;" disabled> ';
	    $html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
	    $html .= '      </select></label> </td>';
	    $html .= "</tr></table>";
	     
	    /*
	     * Vancomycine VA
	     */
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse  blocChampAntiBioGrammeDuPVABG">';
	    $html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
	    $html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > Vancomycine VA </span></label></td>';
	    $html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixVancomycineVAABG" > </label></td>';
	    $html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
	    $html .= '      <select id="vancomycineVAABG" style="width: 120px;" disabled> ';
	    $html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
	    $html .= '      </select></label> </td>';
	    $html .= "</tr></table>";
	     
	    /*
	     * Colistine CS
	     */
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse  blocChampAntiBioGrammeDuPVABG">';
	    $html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
	    $html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > Colistine CS </span></label></td>';
	    $html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixColistineCSABG" > </label></td>';
	    $html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
	    $html .= '      <select id="colistineCSABG" style="width: 120px;" disabled> ';
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
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse  blocChampAntiBioGrammeDuPVABG">';
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
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse  blocChampAntiBioGrammeDuPVABG">';
	    $html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
	    $html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > Tobramycine TB </span></label></td>';
	    $html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixTobramycineTBABG" > </label></td>';
	    $html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
	    $html .= '      <select id="tobramycineTBABG" style="width: 120px;" disabled> ';
	    $html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
	    $html .= '      </select></label> </td>';
	    $html .= "</tr></table>";
	     
	    /*
	     * Amikacine AN
	     */
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse  blocChampAntiBioGrammeDuPVABG">';
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
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse  blocChampAntiBioGrammeDuPVABG">';
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
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse  blocChampAntiBioGrammeDuPVABG">';
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
	     * Tétracycline TE
	     */
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse  blocChampAntiBioGrammeDuPVABG">';
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
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse  blocChampAntiBioGrammeDuPVABG">';
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
	     * Erythromycine E
	     */
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse  blocChampAntiBioGrammeDuPVABG">';
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
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse  blocChampAntiBioGrammeDuPVABG">';
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
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse  blocChampAntiBioGrammeDuPVABG">';
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
	     * Acide fusidique FA
	     */
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse  blocChampAntiBioGrammeDuPVABG">';
	    $html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
	    $html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > Acide fusidique FA </span></label></td>';
	    $html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixAcideFusidiqueFAABG" > </label></td>';
	    $html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
	    $html .= '      <select id="acideFusidiqueFAABG" style="width: 120px;" disabled> ';
	    $html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
	    $html .= '      </select></label> </td>';
	    $html .= "</tr></table>";
	    /*
	     * Acide nalidixique NA
	     */
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse  blocChampAntiBioGrammeDuPVABG">';
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
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse  blocChampAntiBioGrammeDuPVABG">';
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
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse  blocChampAntiBioGrammeDuPVABG">';
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
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse  blocChampAntiBioGrammeDuPVABG">';
	    $html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
	    $html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > Ciprofloxacine CIP </span></label></td>';
	    $html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixCiprofloxacineCIPABG" > </label></td>';
	    $html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
	    $html .= '      <select id="ciprofloxacineCIPABG" style="width: 120px;" disabled> ';
	    $html .= '        <option value=-1> </option> <option value=1 >R&eacute;sistante</option><option value=2 >Sensible</option><option value=3 >Interm&eacute;diaire</option>';
	    $html .= '      </select></label> </td>';
	    $html .= "</tr></table>";
	    /*
	     * LEV
	     */
	    $html .= '<table style="width: 100%; margin-top: -1px;" class="designEnTeteAnalyse  blocChampAntiBioGrammeDuPVABG">';
	    $html .= '<tr class="ligneAnanlyse" style="width: 100%;">';
	    $html .= '  <td style="width: 62%;"><label class="lab1 labABGRadius" style="padding-top: 5px;"><span style="font-weight: bold;" > LEV </span></label></td>';
	    $html .= '  <td style="width: 4%;"><label class="lab1" style="padding-top: 5px;"> <input type="checkbox" style="width:20px;" id="choixLEVABG" > </label></td>';
	    $html .= '    <td style="width: 34%;"> <label class="lab2" style="padding-top: 5px;">';
	    $html .= '      <select id="LEVABG" style="width: 120px;" disabled> ';
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
	    $html .= "<table style='width: 100%;'>";
	
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    $html .= "<tr class='ligneAnanlyse labelTypeMateriel' style='width: 100%; font-family: times new roman; font-size: 15px; margin-top: -45px;'>";
	    $html .= "  <td style='width: 55%;'> <label> Mat&eacute;riel utilis&eacute;</label> </td>";
	    $html .= "  <td colspan='2' style='width: 35%;'> </td>";
	    $html .= "</tr>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px;'>";
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_ecbu' tabindex='1' readonly> </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;' class='iconeValidationInterfaceVisual_66'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    	  
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> ecbu (En attente ... ) </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'>  </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
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
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_pus' tabindex='1' readonly> </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;'  class='iconeValidationInterfaceVisual_67'> </td>";
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
	    $html .= "  <td style='width: 55%;'><div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_typage_hemoglobine' readonly> </div></td>";
	    $html .= "  <td colspan='2' style='width: 45%;' class='iconeValidationInterfaceVisual_68' > </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> typage de l'h&eacute;moglobine  <select name='typage_hemoglobine' id='typage_hemoglobine' style='width: 130px;' disabled> ".$liste_select." </select></span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'>  </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 90%; '> <div style='font-weight: bold; float: left' > Autre </div> <div style=' float: left' >  <select name='autre_typage_hemoglobine' id='autre_typage_hemoglobine' style='width: 120px;' disabled> <option> </option> <option value='H-Barts' >H&eacute;mo-Bart's</option> </select> </div></label></td>";
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
		$html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_ldh' readonly> </div> </td>";
		$html .= "  <td colspan='2' style='width: 45%;'  class='iconeValidationInterfaceVisual_70'> </td>";
		$html .= "</tr>";
		//POUR LE NOM DU TYPE DE MATERIEL UTILISE
			
		$html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
		$html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold; '> ldh <input id='valeur_ldh' type='number' step='any' readonly> </span></label></td>";
		$html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> UI/l </label></td>";
		$html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> (324 - 1029)  </label></td>";
		$html .= "</tr>";
	
		$html .= "</table> </td> </tr>";
	
		return $html;
	}
	
	
	/**
	 * ANCIENNE FONCTION D'IMPRESSION AVEC ZEND PDF
	 */
	
	/*
	public function impressionResultatsAnalysesDemandeesAction()
	{
		$service = $this->layout()->user['NomService'];
		$iddemande = $this->params()->fromPost( 'iddemande' );
		 
		$idpatient = $this->getPatientTable()->getDemandeAnalysesAvecIddemande($iddemande)['idpatient'];
		$personne  = $this->getPersonneTable()->getPersonne($idpatient);
		$patient   = $this->getPatientTable()->getPatient($idpatient);
		$depistage = $this->getPatientTable()->getDepistagePatient($idpatient);
		 
		//Recuperation de la liste des analyses pour lesquelles les résultats sont déjà renseignés et validés
		$listeResultats = $this->getResultatDemandeAnalyseTable()->getListeResultatsAnalysesDemandeesImpSecretaire($iddemande);
		 
		
		$analysesDemandees = array();
		$analysesNFS = array();
		$analysesImmunoHemato = array();
		$analysesCytologie = array();
		$analysesHemostase = array();
		$analysesMetabolismeGlucidique = array();
		$analysesBilanLipidique = array();
		$analysesBilanHepatique = array();
		$analysesBilanRenal = array();
		$analysesSerologie = array();
		$analysesTypageHemoProteine = array();
		$analysesTypageHemoglobine = array();
		$analysesMetabolismeProtidique = array();
		$analysesMetabolismeFer = array();
		
		$resultatsAnalysesDemandees = array();
		
		$anteriorite_nfs = array();
		for($j = 0 , $i = 0 ; $i < count($listeResultats) ; $i++ ){
			$idanalyse = $listeResultats[$i]['idanalyse'];
			$iddemande = $listeResultats[$i]['iddemande'];
			 
			//NFS  ---  NFS  ---  NFS  ---  NFS  ---  NFS  ---  NFS
			//NFS  ---  NFS  ---  NFS  ---  NFS  ---  NFS  ---  NFS
			if($idanalyse == 1){ //NFS
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesNFS [] = 1;
				$resultatsAnalysesDemandees[1] = $this->getResultatDemandeAnalyseTable()->getValeursNfs($iddemande);
				 
				//Recupération des antériorites  ----- Récupération des antériorités
				$analysesAvecResult = $this->getResultatDemandeAnalyseTable()->getListeAnalysesNFSDemandeesAyantResultats($idpatient, $iddemande);
				 
				if($analysesAvecResult){
					$anteriorite_nfs['demande']  = $analysesAvecResult[0];
					$anteriorite_nfs['resultat'] = $this->getResultatDemandeAnalyseTable()->getValeursNfs($analysesAvecResult[0]['iddemande']);
				}
			}
			//=========================================================
			//=========================================================
			
			
			//IMMUNO_HEMATO  ---  IMMUNO_HEMATO  ---  IMMUNO_HEMATO
			//IMMUNO_HEMATO  ---  IMMUNO_HEMATO  ---  IMMUNO_HEMATO
			//IMMUNO_HEMATO  ---  IMMUNO_HEMATO  ---  IMMUNO_HEMATO
			elseif($idanalyse == 2){ //GSRH - GROUPAGE RESHUS
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesImmunoHemato [] = 2;
				$resultatsAnalysesDemandees[2] = $this->getResultatDemandeAnalyseTable()->getValeursGsrhGroupage($iddemande);
			}
			
			elseif($idanalyse == 3){ //RECHERCHE DE L'ANTIGENE D FAIBLE
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesImmunoHemato [] = 3;
				$resultatsAnalysesDemandees[3] = $this->getResultatDemandeAnalyseTable()->getValeursRechercheAntigene($iddemande);
			}
			
			elseif($idanalyse == 4){ //TEST DE COOMBS DIRECT
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesImmunoHemato [] = 4;
				$resultatsAnalysesDemandees[4] = $this->getResultatDemandeAnalyseTable()->getValeursTestCombsDirect($iddemande);
			}
			
			elseif($idanalyse == 5){ //TEST DE COOMBS INDIRECT
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesImmunoHemato [] = 5;
				$resultatsAnalysesDemandees[5] = $this->getResultatDemandeAnalyseTable()->getValeursTestCombsIndirect($iddemande);
			}
			
			elseif($idanalyse == 6){ //TEST DE COMPATIBILITE
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesImmunoHemato [] = 6;
				$resultatsAnalysesDemandees[6] = $this->getResultatDemandeAnalyseTable()->getValeursTestCompatibilite($iddemande);
			}
			//=========================================================
			//=========================================================
			
			
			
			//CYTOLOGIE  ---  CYTOLOGIE  ---  CYTOLOGIE  ---  CYTOLOGIE
			//CYTOLOGIE  ---  CYTOLOGIE  ---  CYTOLOGIE  ---  CYTOLOGIE
			//CYTOLOGIE  ---  CYTOLOGIE  ---  CYTOLOGIE  ---  CYTOLOGIE
			elseif($idanalyse == 7){ //VITESSE SE SEDIMENTATION (VS)
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesCytologie [] = 7;
				$resultatsAnalysesDemandees[7] = $this->getResultatDemandeAnalyseTable()->getValeursVitesseSedimentation($iddemande);
			}
			
			elseif($idanalyse == 8){ //TEST D'EMMEL (TE) 
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesCytologie [] = 8;
				$resultatsAnalysesDemandees[8] = $this->getResultatDemandeAnalyseTable()->getValeursTestDemmel($iddemande);
			}
			
			elseif($idanalyse == 50){ //HLM (COMPTE D'ADDIS)
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesCytologie [] = 50;
				$resultatsAnalysesDemandees[50] = $this->getResultatDemandeAnalyseTable()->getValeursHlmCompteDaddis($iddemande);
			}
			
			elseif($idanalyse == 58){ //CULOT URINAIRE
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesCytologie [] = 58;
				$resultatsAnalysesDemandees[58] = $this->getResultatDemandeAnalyseTable()->getValeursCulotUrinaire($iddemande);
			}
			//=========================================================
			//=========================================================
			
			
			
			//HEMOSTASE  ---  HEMOSTASE  ---  HEMOSTASE  --- HEMOSTASE
			//HEMOSTASE  ---  HEMOSTASE  ---  HEMOSTASE  --- HEMOSTASE
			//HEMOSTASE  ---  HEMOSTASE  ---  HEMOSTASE  --- HEMOSTASE
			elseif($idanalyse == 14){ //TP - INR
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesHemostase [] = 14;
				$resultatsAnalysesDemandees[14] = $this->getResultatDemandeAnalyseTable()->getValeursTpInr($iddemande);
			}
			
			elseif($idanalyse == 15){ //TCA
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesHemostase [] = 15;
				$resultatsAnalysesDemandees[15] = $this->getResultatDemandeAnalyseTable()->getValeursTca($iddemande);
			}
			
			elseif($idanalyse == 16){ //FIBRINEMIE
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesHemostase [] = 16;
				$resultatsAnalysesDemandees[16] = $this->getResultatDemandeAnalyseTable()->getValeursFibrinemie($iddemande);
			}
			
			elseif($idanalyse == 17){ //TEMPS DE SAIGNEMENT
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesHemostase [] = 17;
				$resultatsAnalysesDemandees[17] = $this->getResultatDemandeAnalyseTable()->getValeursTempsSaignement($iddemande);
			}
			
			elseif($idanalyse == 18){ //FACTEUR 8
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesHemostase [] = 18;
				$resultatsAnalysesDemandees[18] = $this->getResultatDemandeAnalyseTable()->getValeursFacteur8($iddemande);
			}
			
			elseif($idanalyse == 19){ //FACTEUR 9
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesHemostase [] = 19;
				$resultatsAnalysesDemandees[19] = $this->getResultatDemandeAnalyseTable()->getValeursFacteur9($iddemande);
			}
			
			elseif($idanalyse == 20){ //D-DIMERES
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesHemostase [] = 20;
				$resultatsAnalysesDemandees[20] = $this->getResultatDemandeAnalyseTable()->getValeursDDimeres($iddemande);
			}
			//=========================================================
			//=========================================================
			
			
			//METABOLISME GLUCIDIQUE --- METABOLISME GLUCIDIQUE --- METABOLISME GLUCIDIQUE
			//METABOLISME GLUCIDIQUE --- METABOLISME GLUCIDIQUE --- METABOLISME GLUCIDIQUE
			//METABOLISME GLUCIDIQUE --- METABOLISME GLUCIDIQUE --- METABOLISME GLUCIDIQUE
			elseif($idanalyse == 21){ //Glycemie
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesMetabolismeGlucidique [] = 21;
				$resultatsAnalysesDemandees[21] = $this->getResultatDemandeAnalyseTable()->getValeursGlycemie($iddemande);
			}
			elseif($idanalyse == 43){ //Hemoglobine glyquee
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesMetabolismeGlucidique [] = 43;
				$resultatsAnalysesDemandees[43] = $this->getResultatDemandeAnalyseTable()->getValeursHemoglobineGlyqueeHBAC($iddemande);
			}
			
			//=========================================================
			//=========================================================
			
			
			//BILAN LIPIDIQUE --- BILAN LIPIDIQUE --- BILAN LIPIDIQUE
			//BILAN LIPIDIQUE --- BILAN LIPIDIQUE --- BILAN LIPIDIQUE
			//BILAN LIPIDIQUE --- BILAN LIPIDIQUE --- BILAN LIPIDIQUE
			elseif($idanalyse == 25){ //CHOLESTEROL TOTAL
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesBilanLipidique  [] = 25;
				$resultatsAnalysesDemandees[25] = $this->getResultatDemandeAnalyseTable()->getValeursCholesterolTotal($iddemande);
			}
			elseif($idanalyse == 26){ //TRIGLYCERIDES
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesBilanLipidique  [] = 26;
				$resultatsAnalysesDemandees[26] = $this->getResultatDemandeAnalyseTable()->getValeursTriglycerides($iddemande);
			}
			elseif($idanalyse == 27){ //CHOLESTEROL HDL
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesBilanLipidique  [] = 27;
				$resultatsAnalysesDemandees[27] = $this->getResultatDemandeAnalyseTable()->getValeursCholesterolHDL($iddemande);
			}
			elseif($idanalyse == 28){ //CHOLESTEROL LDL
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesBilanLipidique  [] = 28;
				$resultatsAnalysesDemandees[28] = $this->getResultatDemandeAnalyseTable()->getValeursCholesterolLDL($iddemande);
			}
			elseif($idanalyse == 29){ //CHOLESTEROL (TOTAL - HDL - LDL) Triglyceride
				
				//CHOLESTEROL TOTAL
				$analysesBilanLipidique  [] = 25;
				$resultatsAnalysesDemandees[25] = $this->getResultatDemandeAnalyseTable()->getValeursCholesterolTotal($iddemande);
			
			    //TRIGLYCERIDES
				$analysesBilanLipidique  [] = 26;
				$resultatsAnalysesDemandees[26] = $this->getResultatDemandeAnalyseTable()->getValeursTriglycerides($iddemande);
					
				//CHOLESTEROL HDL
				$analysesBilanLipidique  [] = 27;
				$resultatsAnalysesDemandees[27] = $this->getResultatDemandeAnalyseTable()->getValeursCholesterolHDL($iddemande);
					
				//CHOLESTEROL LDL
				$analysesBilanLipidique  [] = 28;
				$resultatsAnalysesDemandees[28] = $this->getResultatDemandeAnalyseTable()->getValeursCholesterolLDL($iddemande);
					
			}
			elseif($idanalyse == 30){ //LIPIDES - TOTAUX
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$resultatsAnalysesDemandees[30] = $this->getResultatDemandeAnalyseTable()->getValeursLipidesTotaux($iddemande);
			}
			//=========================================================
			//=========================================================
					
			
			//BILAN HEPATIQUE --- BILAN HEPATIQUE --- BILAN HEPATIQUE
			//BILAN HEPATIQUE --- BILAN HEPATIQUE --- BILAN HEPATIQUE
			//BILAN HEPATIQUE --- BILAN HEPATIQUE --- BILAN HEPATIQUE
			elseif($idanalyse == 37){ //TRANSAMINASES (ASAT/ALAT) 
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesBilanHepatique  [] = 37;
				$resultatsAnalysesDemandees[37][1] = $this->getResultatDemandeAnalyseTable()->getValeursTgoAsat($iddemande);
				$resultatsAnalysesDemandees[37][2] = $this->getResultatDemandeAnalyseTable()->getValeursTgpAlat($iddemande);
			}
			
			elseif($idanalyse == 38){ //PHOSPHATAGE ALCALINE (PAL) 
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesBilanHepatique  [] = 38;
				$resultatsAnalysesDemandees[38] = $this->getResultatDemandeAnalyseTable()->getValeursPhosphatageAlcaline($iddemande);
			}
			
			elseif($idanalyse == 39){ //GAMA GT = YGT 
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesBilanHepatique  [] = 39;
				$resultatsAnalysesDemandees[39] = $this->getResultatDemandeAnalyseTable()->getValeursGamaGtYgt($iddemande);
			}
			
			elseif($idanalyse == 42){ //BILIRUBINE TOTALE ET DIRECTE 
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesBilanHepatique  [] = 42;
				$resultatsAnalysesDemandees[42] = $this->getResultatDemandeAnalyseTable()->getValeursBilirubineTotaleDirecte($iddemande);
			}
			//=========================================================
			//=========================================================
			
			
			
			//BILAN RENAL  ---  BILAN RENAL  ---  BILAN RENAL
			//BILAN RENAL  ---  BILAN RENAL  ---  BILAN RENAL
			//BILAN RENAL  ---  BILAN RENAL  ---  BILAN RENAL
			elseif($idanalyse == 22){ //CREATININEMIE
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesBilanRenal  [] = 22;
				$resultatsAnalysesDemandees[22] = $this->getResultatDemandeAnalyseTable()->getValeursCreatininemie($iddemande);
			}
			
			elseif($idanalyse == 23){ //AZOTEMIE = UREE
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesBilanRenal  [] = 23;
				$resultatsAnalysesDemandees[23] = $this->getResultatDemandeAnalyseTable()->getValeursAzotemie($iddemande);
			}
		    
		    elseif($idanalyse == 46){ //ALBUMINEMIE
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesBilanRenal  [] = 46;
				$resultatsAnalysesDemandees[46] = $this->getResultatDemandeAnalyseTable()->getValeursAlbuminemie($iddemande);
			}
			
			//=========================================================
			//=========================================================
			
			
			//SEROLOGIE  ---  SEROLOGIE  ---  SEROLOGIE
			//SEROLOGIE  ---  SEROLOGIE  ---  SEROLOGIE
			//SEROLOGIE  ---  SEROLOGIE  ---  SEROLOGIE
		    elseif($idanalyse == 10){ //GOUTE EPAISSE / GE
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesSerologie  [] = 10;
				$resultatsAnalysesDemandees[10] = $this->getResultatDemandeAnalyseTable()->getValeursGoutteEpaisse($iddemande);
			}
			
			elseif($idanalyse == 53){ //CRP
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesSerologie  [] = 53;
				$resultatsAnalysesDemandees[53] = $this->getResultatDemandeAnalyseTable()->getValeursCrp($iddemande);
			}
			
			elseif($idanalyse == 55){ //RF Waaler Rose
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesSerologie  [] = 55;
				$resultatsAnalysesDemandees[55] = $this->getResultatDemandeAnalyseTable()->getValeursRfWaalerRose($iddemande);
			}
			
		    elseif($idanalyse == 56){ //TOXOPLASMOSE
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesSerologie  [] = 56;
				$resultatsAnalysesDemandees[56] = $this->getResultatDemandeAnalyseTable()->getValeursToxoplasmose($iddemande);
			}
			
			elseif($idanalyse == 57){ //RUBEOLE
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesSerologie  [] = 57;
				$resultatsAnalysesDemandees[57] = $this->getResultatDemandeAnalyseTable()->getValeursRubeole($iddemande);
			}
			
			elseif($idanalyse == 60){ //Serologie Syphilitique
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesSerologie  [] = 60;
				$resultatsAnalysesDemandees[60] = $this->getResultatDemandeAnalyseTable()->getValeursSerologieSyphilitique($iddemande);
			}
			
			elseif($idanalyse == 61){ //ASLO
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesSerologie  [] = 61;
				$resultatsAnalysesDemandees[61] = $this->getResultatDemandeAnalyseTable()->getValeursAslo($iddemande);
			}
			
			elseif($idanalyse == 62){ //WIDAL
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesSerologie  [] = 62;
				$resultatsAnalysesDemandees[62] = $this->getResultatDemandeAnalyseTable()->getValeursWidal($iddemande);
			}
			
			elseif($idanalyse == 63){ //Ag HBS
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesSerologie  [] = 63;
				$resultatsAnalysesDemandees[63] = $this->getResultatDemandeAnalyseTable()->getValeursAgHbs($iddemande);
			}
			//=========================================================
			//=========================================================
			
			
			//TYPAGE DE L'HEMOGLOBINE (Avec ELECTROPHORESE DE L'HEMOGLOBINE et DES PROTEINES)
			//TYPAGE DE L'HEMOGLOBINE (Avec ELECTROPHORESE DE L'HEMOGLOBINE et DES PROTEINES)
			//TYPAGE DE L'HEMOGLOBINE (Avec ELECTROPHORESE DE L'HEMOGLOBINE et DES PROTEINES)
			elseif($idanalyse == 44){ //ELECTROPHORESE DE HEMOGLOBINE
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesTypageHemoProteine[44] = 44;
				$resultatsAnalysesDemandees[44] = $this->getResultatDemandeAnalyseTable()->getValeursElectrophoreseHemoglobine($iddemande);
			}

			//=========================================================
			//=========================================================
			

			//METABOLISME PROTIDIQUE  ---  METABOLISME PROTIDIQUE
			//METABOLISME PROTIDIQUE  ---  METABOLISME PROTIDIQUE
			//METABOLISME PROTIDIQUE  ---  METABOLISME PROTIDIQUE
			elseif($idanalyse == 45){ //ELECTROPHORESE DES PROTEINES
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesMetabolismeProtidique[45] = 45;
				$resultatsAnalysesDemandees[45] = $this->getResultatDemandeAnalyseTable()->getValeursElectrophoreseProteines($iddemande);
			}
				
			elseif($idanalyse == 48){ //PROTEINES TOTAL (PROTIDEMIE)
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesMetabolismeProtidique[48] = 48;
				$resultatsAnalysesDemandees[48] = $this->getResultatDemandeAnalyseTable()->getValeursProtidemie($iddemande);
			}
				
			elseif($idanalyse == 49){ //PROTEINURIE DES 24H (PU 24H)
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesMetabolismeProtidique[49] = 49;
				$resultatsAnalysesDemandees[49] = $this->getResultatDemandeAnalyseTable()->getValeursProteinurie($iddemande);
			}
			
			//=========================================================
			//=========================================================
			
			
			//METABOLISME DU FER --- METABOLISME DU FER
			//METABOLISME DU FER --- METABOLISME DU FER
			//METABOLISME DU FER --- METABOLISME DU FER
			elseif($idanalyse == 40){ //FER SERIQUE
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesMetabolismeFer[40] = 40;
				$resultatsAnalysesDemandees[40] = $this->getResultatDemandeAnalyseTable()->getValeursFerSerique($iddemande);
			}
			elseif($idanalyse == 41){ //FERRITININE
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesMetabolismeFer[41] = 41;
				$resultatsAnalysesDemandees[41] = $this->getResultatDemandeAnalyseTable()->getValeursFerritinine($iddemande);
			}
			//=========================================================
			//=========================================================
			
			
			//TYPAGE DE L'HEMOGLOBINE  ---  TYPAGE DE L'HEMOGLOBINE
			//TYPAGE DE L'HEMOGLOBINE  ---  TYPAGE DE L'HEMOGLOBINE
			//TYPAGE DE L'HEMOGLOBINE  ---  TYPAGE DE L'HEMOGLOBINE
			elseif($idanalyse == 68){ //TYPAGE DE L'HEMOGLOBINE
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesTypageHemoglobine [] = 68;
				$resultatsAnalysesDemandees[68] = $this->getResultatDemandeAnalyseTable()->getValeursTypageHemoglobineLibelle($iddemande);
			}
			//=========================================================
			//=========================================================
			
		}
		
		//******************************************************
		//******************************************************
		//************** Création de l'imprimé pdf *************
		//******************************************************
		//******************************************************
		//Créer le document
		$DocPdf = new DocumentResultatsPdf();
		$temoinEntre = 0;
		
		//========= Créaton de la page 1 ========
		//========= Créaton de la page 1 ========
		//========= Créaton de la page 1 ========
		
		if($analysesNFS){
			$page1 = new ResultatsNfsPdf();
			
			//Envoyer les données sur le patient
			$page1->setPatient($patient);
			$page1->setDonneesPatient($personne);
			$page1->setService($service);
			$page1->setAnalysesDemandees($analysesDemandees);
			$page1->setResultatsAnalysesDemandees($resultatsAnalysesDemandees);
			$page1->setDepistage($depistage);
			$page1->setAnterioriteNFS($anteriorite_nfs);
			
			//Ajouter une note à la page
			$page1->addNote();
			//Ajouter la page au document
			$DocPdf->addPage($page1->getPage());
			$temoinEntre = 1;
		}
		
		//========= Créaton de la page 2 ========
		//========= Créaton de la page 2 ========
		//========= Créaton de la page 2 ========
		
		if($analysesImmunoHemato || $analysesCytologie || $analysesHemostase || $analysesMetabolismeGlucidique ||
		   $analysesBilanLipidique || $analysesBilanHepatique || $analysesBilanRenal || $analysesSerologie || $analysesTypageHemoProteine ||
		   $analysesMetabolismeFer || $analysesMetabolismeProtidique){
			
			$page2 = new ResultatsAnalysesDemandeesPdf();
			
			//Envoyer les données sur le patient
			$page2->setPatient($patient);
			$page2->setDonneesPatient($personne);
			$page2->setService($service);
			$page2->setAnalysesDemandees($analysesDemandees);
			$page2->setResultatsAnalysesDemandees($resultatsAnalysesDemandees);
			$page2->setDepistage($depistage);
			$page2->setAnterioriteNFS($anteriorite_nfs);
				
			//GESTION DES ANALYSES DE L'IMMUNO_HEMATO
			//GESTION DES ANALYSES DE L'IMMUNO_HEMATO
			$page2->setAnalysesImmunoHemato($analysesImmunoHemato);
			
			//GESTION DES ANALYSES DE LA CYTOLOGIE
			//GESTION DES ANALYSES DE LA CYTOLOGIE
			$page2->setAnalysesCytologie($analysesCytologie);
			
			//GESTION DES ANALYSES DE L'HEMOSTASE
			//GESTION DES ANALYSES DE L'HEMOSTASE
			$page2->setAnalysesHemostase($analysesHemostase);
			
			//GESTION DES ANALYSES DU TYPAGE (Helectrophorèse)
			//GESTION DES ANALYSES DU TYPAGE (Helectrophorèse)
			$page2->setAnalysesTypageHemoProteine($analysesTypageHemoProteine);
			
			//GESTION DES ANALYSES DU METABOLISME GLUCIDIQUE
			//GESTION DES ANALYSES DU METABOLISME GLUCIDIQUE
			$page2->setAnalysesMetabolismeGlucidique($analysesMetabolismeGlucidique);
				
			//GESTION DES ANALYSES DU BILAN LIPIDIQUE
			//GESTION DES ANALYSES DU BILAN LIPIDIQUE
			$page2->setAnalysesBilanLipidique($analysesBilanLipidique);
			
			//GESTION DES ANALYSES DU BILAN HEPATIQUE
			//GESTION DES ANALYSES DU BILAN HEPATIQUE
			$page2->setAnalysesBilanHepatique($analysesBilanHepatique);
			
			//GESTION DES ANALYSES DU BILAN RENAL
			//GESTION DES ANALYSES DU BILAN RENAL
			$page2->setAnalysesBilanRenal($analysesBilanRenal);
			
			//GESTION DES ANALYSES DE SEROLOGIE
			//GESTION DES ANALYSES DE SEROLOGIE
			$page2->setAnalysesSerologie($analysesSerologie);
			
			//GESTION DES ANALYSES DE METABOLISME DU FER
			//GESTION DES ANALYSES DE METABOLISME DU FER
			$page2->setAnalysesMetabolismeFer($analysesMetabolismeFer);
			
			//GESTION DES ANALYSES DE METABOLISME PROTIDIQUE
			//GESTION DES ANALYSES DE METABOLISME PROTIDIQUE
			$page2->setAnalysesMetabolismeProtidique($analysesMetabolismeProtidique);
			
			//Ajouter une note à la page
			$page2->addNote();
			//Ajouter la page au document
			$DocPdf->addPage($page2->getPage());
			$temoinEntre = 1;
		}
		//var_dump('eke,nejln'); exit();
		//========= Créaton de la page 3 ========
		//========= Créaton de la page 3 ========
		//========= Créaton de la page 3 ========
		if($analysesTypageHemoglobine){
			
			$page3 = new ResultatsTypageHemoglobinePdf();
				
			//Envoyer les données sur le patient
			$page3->setPatient($patient);
			$page3->setDonneesPatient($personne);
			$page3->setService($service);
			$page3->setAnalysesDemandees($analysesDemandees);
			$page3->setResultatsAnalysesDemandees($resultatsAnalysesDemandees);
			$page3->setDepistage($depistage);
			
			//Ajouter une note à la page
			$page3->addNote();
			$DocPdf->addPage($page3->getPage());
			$temoinEntre = 1;
		}
		
		//Afficher le document contenant la page
		//Afficher le document contenant la page
		//Afficher le document contenant la page
		if($temoinEntre == 1){ 
			$DocPdf->getDocument(); 
		}
		else{ 
			echo('<div align="center"  style="color: red; font-weight: bold;">AUCUNE INFORMATION A AFFICHER</div>'); 
			exit(); 
		}
		
	}
	*/
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	/**
	 * NOUVELLE FONCTION D'IMPRESSION AVEC fpdf
	 */
	public function impressionResultatsAnalysesDemandeesAction()
	{
		$nomService = $this->layout()->user['NomService'];
		$iddemande = $this->params()->fromPost( 'iddemande' );
		
		$idpatient = $this->getPatientTable()->getDemandeAnalysesAvecIddemande($iddemande)['idpatient'];
		$personne  = $this->getPersonneTable()->getPersonne($idpatient);
		$patient   = $this->getPatientTable()->getPatient($idpatient);
		$depistage = $this->getPatientTable()->getDepistagePatient($idpatient);
			
		//Recuperation de la liste des analyses pour lesquelles les résultats sont déjà renseignés et validés
		$listeResultats = $this->getResultatDemandeAnalyseTable()->getListeResultatsAnalysesDemandeesImpSecretaire($iddemande);
			
	
		$analysesDemandees = array();
		$analysesNFS = array();
		$analysesImmunoHemato = array();
		$analysesCytologie = array();
		$analysesHemostase = array();
		$analysesMetabolismeGlucidique = array();
		$analysesBilanLipidique = array();
		$analysesBilanHepatique = array();
		$analysesBilanRenal = array();
		$analysesSerologie = array();
		$analysesTypageHemoProteine = array();
		$analysesTypageHemoglobine = array();
		$analysesMetabolismeProtidique = array();
		$analysesMetabolismeFer = array();
		$analysesBilanElectrolyte = array();
		$analysesSerologieHIV = array();
		$analysesSerologiePV = array();
		$resultatsAntiBioGrammeDuPV = array();
	
		$resultatsAnalysesDemandees = array();
	
		$anteriorite_nfs = array();
		for($j = 0 , $i = 0 ; $i < count($listeResultats) ; $i++ ){
			$idanalyse = $listeResultats[$i]['idanalyse'];
			$iddemande = $listeResultats[$i]['iddemande'];
	
			//NFS  ---  NFS  ---  NFS  ---  NFS  ---  NFS  ---  NFS
			//NFS  ---  NFS  ---  NFS  ---  NFS  ---  NFS  ---  NFS
			if($idanalyse == 1 || $idanalyse == 71){ //NFS
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesNFS [] = 1;
				$resultatsAnalysesDemandees[1] = $this->getResultatDemandeAnalyseTable()->getValeursNfs($iddemande);
					
				//Recupération des antériorites  ----- Récupération des antériorités
				$analysesAvecResult = $this->getResultatDemandeAnalyseTable()->getListeAnalysesNFSDemandeesAyantResultats($idpatient, $iddemande);
					
				if($analysesAvecResult){
					$anteriorite_nfs['demande']  = $analysesAvecResult[0];
					$anteriorite_nfs['resultat'] = $this->getResultatDemandeAnalyseTable()->getValeursNfs($analysesAvecResult[0]['iddemande']);
				}
				
			}
			
			//=========================================================
			//=========================================================
				
				
			//IMMUNO_HEMATO  ---  IMMUNO_HEMATO  ---  IMMUNO_HEMATO
			//IMMUNO_HEMATO  ---  IMMUNO_HEMATO  ---  IMMUNO_HEMATO
			//IMMUNO_HEMATO  ---  IMMUNO_HEMATO  ---  IMMUNO_HEMATO
			elseif($idanalyse == 2){ //GSRH - GROUPAGE RESHUS
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesImmunoHemato [] = 2;
				$resultatsAnalysesDemandees[2] = $this->getResultatDemandeAnalyseTable()->getValeursGsrhGroupage($iddemande);
			}
				
			elseif($idanalyse == 3){ //RECHERCHE DE L'ANTIGENE D FAIBLE
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesImmunoHemato [] = 3;
				$resultatsAnalysesDemandees[3] = $this->getResultatDemandeAnalyseTable()->getValeursRechercheAntigene($iddemande);
			}
				
			elseif($idanalyse == 4){ //TEST DE COOMBS DIRECT
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesImmunoHemato [] = 4;
				$resultatsAnalysesDemandees[4] = $this->getResultatDemandeAnalyseTable()->getValeursTestCombsDirect($iddemande);
			}
				
			elseif($idanalyse == 5){ //TEST DE COOMBS INDIRECT
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesImmunoHemato [] = 5;
				$resultatsAnalysesDemandees[5] = $this->getResultatDemandeAnalyseTable()->getValeursTestCombsIndirect($iddemande);
			}
				
			elseif($idanalyse == 6){ //TEST DE COMPATIBILITE
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesImmunoHemato [] = 6;
				$resultatsAnalysesDemandees[6] = $this->getResultatDemandeAnalyseTable()->getValeursTestCompatibilite($iddemande);
			}
			//=========================================================
			//=========================================================
				
				
				
			//CYTOLOGIE  ---  CYTOLOGIE  ---  CYTOLOGIE  ---  CYTOLOGIE
			//CYTOLOGIE  ---  CYTOLOGIE  ---  CYTOLOGIE  ---  CYTOLOGIE
			//CYTOLOGIE  ---  CYTOLOGIE  ---  CYTOLOGIE  ---  CYTOLOGIE
			elseif($idanalyse == 7){ //VITESSE SE SEDIMENTATION (VS)
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesCytologie [] = 7;
				$resultatsAnalysesDemandees[7] = $this->getResultatDemandeAnalyseTable()->getValeursVitesseSedimentation($iddemande);
			}
				
			elseif($idanalyse == 8){ //TEST D'EMMEL (TE)
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesCytologie [] = 8;
				$resultatsAnalysesDemandees[8] = $this->getResultatDemandeAnalyseTable()->getValeursTestDemmel($iddemande);
			}
				
			elseif($idanalyse == 50){ //HLM (COMPTE D'ADDIS)
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesCytologie [] = 50;
				$resultatsAnalysesDemandees[50] = $this->getResultatDemandeAnalyseTable()->getValeursHlmCompteDaddis($iddemande);
			}
				
			elseif($idanalyse == 58){ //CULOT URINAIRE
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesCytologie [] = 58;
				$resultatsAnalysesDemandees[58] = $this->getResultatDemandeAnalyseTable()->getValeursCulotUrinaire($iddemande);
			}
			//=========================================================
			//=========================================================
				
				
				
			//HEMOSTASE  ---  HEMOSTASE  ---  HEMOSTASE  --- HEMOSTASE
			//HEMOSTASE  ---  HEMOSTASE  ---  HEMOSTASE  --- HEMOSTASE
			//HEMOSTASE  ---  HEMOSTASE  ---  HEMOSTASE  --- HEMOSTASE
			elseif($idanalyse == 14){ //TP - INR
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesHemostase [] = 14;
				$resultatsAnalysesDemandees[14] = $this->getResultatDemandeAnalyseTable()->getValeursTpInr($iddemande);
			}
				
			elseif($idanalyse == 15){ //TCA
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesHemostase [] = 15;
				$resultatsAnalysesDemandees[15] = $this->getResultatDemandeAnalyseTable()->getValeursTca($iddemande);
			}
				
			elseif($idanalyse == 16){ //FIBRINEMIE
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesHemostase [] = 16;
				$resultatsAnalysesDemandees[16] = $this->getResultatDemandeAnalyseTable()->getValeursFibrinemie($iddemande);
			}
				
			elseif($idanalyse == 17){ //TEMPS DE SAIGNEMENT
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesHemostase [] = 17;
				$resultatsAnalysesDemandees[17] = $this->getResultatDemandeAnalyseTable()->getValeursTempsSaignement($iddemande);
			}
				
			elseif($idanalyse == 18){ //FACTEUR 8
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesHemostase [] = 18;
				$resultatsAnalysesDemandees[18] = $this->getResultatDemandeAnalyseTable()->getValeursFacteur8($iddemande);
			}
				
			elseif($idanalyse == 19){ //FACTEUR 9
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesHemostase [] = 19;
				$resultatsAnalysesDemandees[19] = $this->getResultatDemandeAnalyseTable()->getValeursFacteur9($iddemande);
			}
				
			elseif($idanalyse == 20){ //D-DIMERES
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesHemostase [] = 20;
				$resultatsAnalysesDemandees[20] = $this->getResultatDemandeAnalyseTable()->getValeursDDimeres($iddemande);
			}
			//=========================================================
			//=========================================================
				
				
			//METABOLISME GLUCIDIQUE --- METABOLISME GLUCIDIQUE --- METABOLISME GLUCIDIQUE
			//METABOLISME GLUCIDIQUE --- METABOLISME GLUCIDIQUE --- METABOLISME GLUCIDIQUE
			//METABOLISME GLUCIDIQUE --- METABOLISME GLUCIDIQUE --- METABOLISME GLUCIDIQUE
			elseif($idanalyse == 21){ //Glycemie
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesMetabolismeGlucidique [] = 21;
				$resultatsAnalysesDemandees[21] = $this->getResultatDemandeAnalyseTable()->getValeursGlycemie($iddemande);
			}
			elseif($idanalyse == 43){ //Hemoglobine glyquee
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesMetabolismeGlucidique [] = 43;
				$resultatsAnalysesDemandees[43] = $this->getResultatDemandeAnalyseTable()->getValeursHemoglobineGlyqueeHBAC($iddemande);
			}
				
			//=========================================================
			//=========================================================
				
				
			//BILAN LIPIDIQUE --- BILAN LIPIDIQUE --- BILAN LIPIDIQUE
			//BILAN LIPIDIQUE --- BILAN LIPIDIQUE --- BILAN LIPIDIQUE
			//BILAN LIPIDIQUE --- BILAN LIPIDIQUE --- BILAN LIPIDIQUE
			elseif($idanalyse == 25){ //CHOLESTEROL TOTAL
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesBilanLipidique  [] = 25;
				$resultatsAnalysesDemandees[25] = $this->getResultatDemandeAnalyseTable()->getValeursCholesterolTotal($iddemande);
			}
			elseif($idanalyse == 26){ //TRIGLYCERIDES
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesBilanLipidique  [] = 26;
				$resultatsAnalysesDemandees[26] = $this->getResultatDemandeAnalyseTable()->getValeursTriglycerides($iddemande);
			}
			elseif($idanalyse == 27){ //CHOLESTEROL HDL
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesBilanLipidique  [] = 27;
				$resultatsAnalysesDemandees[27] = $this->getResultatDemandeAnalyseTable()->getValeursCholesterolHDL($iddemande);
			}
			elseif($idanalyse == 28){ //CHOLESTEROL LDL
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesBilanLipidique  [] = 28;
				$resultatsAnalysesDemandees[28] = $this->getResultatDemandeAnalyseTable()->getValeursCholesterolLDL($iddemande);
			}
			elseif($idanalyse == 29){ //CHOLESTEROL (TOTAL - HDL - LDL) Triglyceride
	
				//CHOLESTEROL TOTAL
				$analysesBilanLipidique  [] = 25;
				$resultatsAnalysesDemandees[25] = $this->getResultatDemandeAnalyseTable()->getValeursCholesterolTotal($iddemande);
					
				//TRIGLYCERIDES
				$analysesBilanLipidique  [] = 26;
				$resultatsAnalysesDemandees[26] = $this->getResultatDemandeAnalyseTable()->getValeursTriglycerides($iddemande);
					
				//CHOLESTEROL HDL
				$analysesBilanLipidique  [] = 27;
				$resultatsAnalysesDemandees[27] = $this->getResultatDemandeAnalyseTable()->getValeursCholesterolHDL($iddemande);
					
				//CHOLESTEROL LDL
				$analysesBilanLipidique  [] = 28;
				$resultatsAnalysesDemandees[28] = $this->getResultatDemandeAnalyseTable()->getValeursCholesterolLDL($iddemande);
					
			}
			elseif($idanalyse == 30){ //LIPIDES - TOTAUX
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesBilanLipidique  [] = 30;
				$resultatsAnalysesDemandees[30] = $this->getResultatDemandeAnalyseTable()->getValeursLipidesTotaux($iddemande);
			}
			//=========================================================
			//=========================================================
				
				
			//BILAN HEPATIQUE --- BILAN HEPATIQUE --- BILAN HEPATIQUE
			//BILAN HEPATIQUE --- BILAN HEPATIQUE --- BILAN HEPATIQUE
			//BILAN HEPATIQUE --- BILAN HEPATIQUE --- BILAN HEPATIQUE
			elseif($idanalyse == 37){ //TRANSAMINASES (ASAT/ALAT)
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesBilanHepatique  [] = 37;
				$resultatsAnalysesDemandees[37][1] = $this->getResultatDemandeAnalyseTable()->getValeursTgoAsat($iddemande);
				$resultatsAnalysesDemandees[37][2] = $this->getResultatDemandeAnalyseTable()->getValeursTgpAlat($iddemande);
			}
				
			elseif($idanalyse == 38){ //PHOSPHATAGE ALCALINE (PAL)
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesBilanHepatique  [] = 38;
				$resultatsAnalysesDemandees[38] = $this->getResultatDemandeAnalyseTable()->getValeursPhosphatageAlcaline($iddemande);
			}
				
			elseif($idanalyse == 39){ //GAMA GT = YGT
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesBilanHepatique  [] = 39;
				$resultatsAnalysesDemandees[39] = $this->getResultatDemandeAnalyseTable()->getValeursGamaGtYgt($iddemande);
			}
				
			elseif($idanalyse == 42){ //BILIRUBINE TOTALE ET DIRECTE
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesBilanHepatique  [] = 42;
				$resultatsAnalysesDemandees[42] = $this->getResultatDemandeAnalyseTable()->getValeursBilirubineTotaleDirecte($iddemande);
			}
			
			elseif($idanalyse == 70){ //LDH
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesBilanHepatique  [] = 70;
				$resultatsAnalysesDemandees[70] = $this->getResultatDemandeAnalyseTable()->getValeursLDH($iddemande);
			}
			//=========================================================
			//=========================================================
				
				
				
			//BILAN RENAL  ---  BILAN RENAL  ---  BILAN RENAL
			//BILAN RENAL  ---  BILAN RENAL  ---  BILAN RENAL
			//BILAN RENAL  ---  BILAN RENAL  ---  BILAN RENAL
			elseif($idanalyse == 22){ //CREATININEMIE
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesBilanRenal  [] = 22;
				$resultatsAnalysesDemandees[22] = $this->getResultatDemandeAnalyseTable()->getValeursCreatininemie($iddemande);
			}
				
			elseif($idanalyse == 23){ //AZOTEMIE = UREE
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesBilanRenal  [] = 23;
				$resultatsAnalysesDemandees[23] = $this->getResultatDemandeAnalyseTable()->getValeursAzotemie($iddemande);
			}
	
			elseif($idanalyse == 24){ //URICEMIE = ACIDE URIQUE
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesBilanRenal  [] = 24;
				$resultatsAnalysesDemandees[24] = $this->getResultatDemandeAnalyseTable()->getValeursAcideUrique($iddemande);
			}
			
			elseif($idanalyse == 47){ //ALBUMINE URINAIRE (BANDELETTES)
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesBilanRenal [] = 47;
				$resultatsAnalysesDemandees[47] = $this->getResultatDemandeAnalyseTable()->getValeursAlbumineUrinaire($iddemande);
			}
			
			elseif($idanalyse == 49){ //PROTEINURIE DES 24H (PU 24H)
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesBilanRenal [] = 49;
				$resultatsAnalysesDemandees[49] = $this->getResultatDemandeAnalyseTable()->getValeursProteinurie($iddemande);
			}
			//=========================================================
			//=========================================================
				
				
			//SEROLOGIE  ---  SEROLOGIE  ---  SEROLOGIE
			//SEROLOGIE  ---  SEROLOGIE  ---  SEROLOGIE
			//SEROLOGIE  ---  SEROLOGIE  ---  SEROLOGIE
			elseif($idanalyse == 10){ //GOUTE EPAISSE / GE
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesSerologie  [] = 10;
				$resultatsAnalysesDemandees[10] = $this->getResultatDemandeAnalyseTable()->getValeursGoutteEpaisse($iddemande);
			}
			
			elseif($idanalyse == 51){ //BETA HCG PLASMATIQUE
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesSerologie  [] = 51;
				$resultatsAnalysesDemandees[51] = $this->getResultatDemandeAnalyseTable()->getValeursBetaHcgPlasmatique($iddemande);
			}
			
			elseif($idanalyse == 52){ //PSA
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesSerologie  [] = 52;
				$resultatsAnalysesDemandees[52] = $this->getResultatDemandeAnalyseTable()->getValeursPsa($iddemande);
			}
				
			elseif($idanalyse == 53){ //CRP ou C. Protéine Réactive 
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesSerologie  [] = 53;
				$resultatsAnalysesDemandees[53] = $this->getResultatDemandeAnalyseTable()->getValeursCrp($iddemande);
			}
			
			elseif($idanalyse == 54){ //Facteurs Rhumatoides
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesSerologie  [] = 54;
				$resultatsAnalysesDemandees[54] = $this->getResultatDemandeAnalyseTable()->getValeursFacteursRhumatoides($iddemande);
			}
				
			elseif($idanalyse == 55){ //RF Waaler Rose
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesSerologie  [] = 55;
				$resultatsAnalysesDemandees[55] = $this->getResultatDemandeAnalyseTable()->getValeursRfWaalerRose($iddemande);
			}
				
			elseif($idanalyse == 56){ //TOXOPLASMOSE
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesSerologie  [] = 56;
				$resultatsAnalysesDemandees[56] = $this->getResultatDemandeAnalyseTable()->getValeursToxoplasmose($iddemande);
			}
				
			elseif($idanalyse == 57){ //RUBEOLE
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesSerologie  [] = 57;
				$resultatsAnalysesDemandees[57] = $this->getResultatDemandeAnalyseTable()->getValeursRubeole($iddemande);
			}
				
			elseif($idanalyse == 60){ //Serologie Syphilitique
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesSerologie  [] = 60;
				$resultatsAnalysesDemandees[60] = $this->getResultatDemandeAnalyseTable()->getValeursSerologieSyphilitique($iddemande);
			}
				
			elseif($idanalyse == 61){ //ASLO
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesSerologie  [] = 61;
				$resultatsAnalysesDemandees[61] = $this->getResultatDemandeAnalyseTable()->getValeursAslo($iddemande);
			}
				
			elseif($idanalyse == 62){ //WIDAL
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesSerologie  [] = 62;
				$resultatsAnalysesDemandees[62] = $this->getResultatDemandeAnalyseTable()->getValeursWidal($iddemande);
			}
				
			elseif($idanalyse == 63){ //Ag HBS
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesSerologie  [] = 63;
				$resultatsAnalysesDemandees[63] = $this->getResultatDemandeAnalyseTable()->getValeursAgHbs($iddemande);
			}
			
			
			
			//=========================================================
			//=========================================================
				
				
			//TYPAGE DE L'HEMOGLOBINE (Avec ELECTROPHORESE DE L'HEMOGLOBINE et DES PROTEINES)
			//TYPAGE DE L'HEMOGLOBINE (Avec ELECTROPHORESE DE L'HEMOGLOBINE et DES PROTEINES)
			//TYPAGE DE L'HEMOGLOBINE (Avec ELECTROPHORESE DE L'HEMOGLOBINE et DES PROTEINES)
			elseif($idanalyse == 44){ //ELECTROPHORESE DE HEMOGLOBINE
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesTypageHemoProteine[44] = 44;
				$resultatsAnalysesDemandees[44] = $this->getResultatDemandeAnalyseTable()->getValeursElectrophoreseHemoglobine($iddemande);
			}
	
			//=========================================================
			//=========================================================
				
	
			//METABOLISME PROTIDIQUE  ---  METABOLISME PROTIDIQUE
			//METABOLISME PROTIDIQUE  ---  METABOLISME PROTIDIQUE
			//METABOLISME PROTIDIQUE  ---  METABOLISME PROTIDIQUE
			elseif($idanalyse == 45){ //ELECTROPHORESE DES PROTEINES
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesMetabolismeProtidique[45] = 45;
				$resultatsAnalysesDemandees[45] = $this->getResultatDemandeAnalyseTable()->getValeursElectrophoreseProteines($iddemande);
			}
	
			elseif($idanalyse == 48){ //PROTEINES TOTAL (PROTIDEMIE)
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesMetabolismeProtidique[48] = 48;
				$resultatsAnalysesDemandees[48] = $this->getResultatDemandeAnalyseTable()->getValeursProtidemie($iddemande);
			}
	
			elseif($idanalyse == 46){ //ALBUMINEMIE
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesMetabolismeProtidique  [46] = 46;
				$resultatsAnalysesDemandees[46] = $this->getResultatDemandeAnalyseTable()->getValeursAlbuminemie($iddemande);
			}
			//=========================================================
			//=========================================================
				
				
			//METABOLISME DU FER --- METABOLISME DU FER
			//METABOLISME DU FER --- METABOLISME DU FER
			//METABOLISME DU FER --- METABOLISME DU FER
			elseif($idanalyse == 40){ //FER SERIQUE
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesMetabolismeFer[40] = 40;
				$resultatsAnalysesDemandees[40] = $this->getResultatDemandeAnalyseTable()->getValeursFerSerique($iddemande);
			}
			elseif($idanalyse == 41){ //FERRITININE
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesMetabolismeFer[41] = 41;
				$resultatsAnalysesDemandees[41] = $this->getResultatDemandeAnalyseTable()->getValeursFerritinine($iddemande);
			}
			//=========================================================
			//=========================================================
				
			
			//BILAN D'ELECTROLYTE --- BILAN D'ELECTROLYTE
			//BILAN D'ELECTROLYTE --- BILAN D'ELECTROLYTE
			//BILAN D'ELECTROLYTE --- BILAN D'ELECTROLYTE
			elseif($idanalyse == 31){ //IONOGRAMME
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesBilanElectrolyte[31] = 31;
				$resultatsAnalysesDemandees[31] = $this->getResultatDemandeAnalyseTable()->getValeursIonogramme($iddemande);
			}
			elseif($idanalyse == 32){ //CALCEMIE
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesBilanElectrolyte[32] = 32;
				$resultatsAnalysesDemandees[32] = $this->getResultatDemandeAnalyseTable()->getValeursCalcemie($iddemande);
			}
			elseif($idanalyse == 33){ //MAGNESEMIE
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesBilanElectrolyte[33] = 33;
				$resultatsAnalysesDemandees[33] = $this->getResultatDemandeAnalyseTable()->getValeursMagnesemie($iddemande);
			}
			elseif($idanalyse == 34){ //PHOSPHOREMIE
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesBilanElectrolyte[34] = 34;
				$resultatsAnalysesDemandees[34] = $this->getResultatDemandeAnalyseTable()->getValeursPhosphoremie($iddemande);
			}
			//=========================================================
			//=========================================================
			
			
			//SEROLOGIE HIV --- SEROLOGIE HIV --- SEROLOGIE HIV
			//SEROLOGIE HIV --- SEROLOGIE HIV --- SEROLOGIE HIV
			//SEROLOGIE HIV --- SEROLOGIE HIV --- SEROLOGIE HIV
			elseif($idanalyse == 64){ //HIV
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesSerologieHIV [] = 64;
				$resultatsAnalysesDemandees[64] = $this->getResultatDemandeAnalyseTable()->getValeursHIV($iddemande);
			}
			//=========================================================
			//=========================================================
			
			
			//PV --- PV --- PV --- PV
			elseif($idanalyse == 65){ //PV
			    $analysesDemandees  [$j++] = $listeResultats[$i];
			    $analysesSerologiePV [] = 65;
			    $resultatsAnalysesDemandees[65] = $this->getResultatDemandeAnalyseTable()->getValeursPV($iddemande);
			    $resultatsAntiBioGrammeDuPV = $this->getResultatDemandeAnalyseTable()->getValeursAntiBioGramme($iddemande);
			}
			//=========================================================
			//=========================================================
			
				
			//TYPAGE DE L'HEMOGLOBINE  ---  TYPAGE DE L'HEMOGLOBINE
			//TYPAGE DE L'HEMOGLOBINE  ---  TYPAGE DE L'HEMOGLOBINE
			//TYPAGE DE L'HEMOGLOBINE  ---  TYPAGE DE L'HEMOGLOBINE
			elseif($idanalyse == 68){ //TYPAGE DE L'HEMOGLOBINE
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesTypageHemoglobine [] = 68;
				$resultatsAnalysesDemandees[68] = $this->getResultatDemandeAnalyseTable()->getValeursTypageHemoglobineLibelle($iddemande);
			}
			//=========================================================
			//=========================================================
			
			
			
				
		}
	
		//******************************************************
		//******************************************************
		//************** Création de l'imprimé pdf *************
		//******************************************************
		//******************************************************
		//Créer le document
		$pdf = new ImprimerResultatsAnalysesDemandees();
		$pdf->SetMargins(12.5,11.5,12.5);
		
		//Ajout d'informations sur le service et le patient
		$pdf->setNomService($nomService);
		$pdf->setInfosPatients($personne);
		$pdf->setPatient($patient);
		$pdf->setDepistage($depistage);
		
		//Liste de toutes les informations sur les analyses à imprimer
		$pdf->setAnalysesDemandees($analysesDemandees);
		
		//Liste des analyses à imprimer
		$pdf->setResultatsAnalysesDemandees($resultatsAnalysesDemandees);
		
		//========= Créaton de la page 1 ========
		//========= Créaton de la page 1 ========
		//========= Créaton de la page 1 ========
		if($analysesNFS){
			$pdf->setAnterioriteNfs($anteriorite_nfs);

			/*
			 * Envoie des données pour affichage
			*/
			$pdf->affichageResultatAnalyseNFS();
		}
	
		//========= Créaton des autres pages ========
		//========= Créaton des autres pages ========
		//========= Créaton des autres pages ========
		if($analysesImmunoHemato || $analysesCytologie || $analysesHemostase || $analysesMetabolismeGlucidique ||
		$analysesBilanLipidique || $analysesBilanHepatique || $analysesBilanRenal || $analysesSerologie || $analysesTypageHemoProteine ||
		$analysesMetabolismeFer || $analysesMetabolismeProtidique){
		    
			//GESTION DES ANALYSES DE L'IMMUNO_HEMATO
			$pdf->setAnalysesImmunoHemato($analysesImmunoHemato);
		
		    //GESTION DES ANALYSES DE LA CYTOLOGIE
		    $pdf->setAnalysesCytologie($analysesCytologie);
		    
		    //GESTION DES ANALYSES DE L'HEMOSTASE 
		    $pdf->setAnalysesHemostase($analysesHemostase);
		    
		    //GESTION DES ANALYSES DE SEROLOGIE
		    $pdf->setAnalysesSerologie($analysesSerologie);
		    
		    //GESTION DES ANALYSES DU BILAN HEPATIQUE
		    $pdf->setAnalysesBilanHepatique($analysesBilanHepatique);
		    
		    //GESTION DES ANALYSES DU BILAN RENAL
		    $pdf->setAnalysesBilanRenal($analysesBilanRenal);
		    
		    //GESTION DES ANALYSES DU METABOLISME GLUCIDIQUE
		    $pdf->setAnalysesMetabolismeGlucidique($analysesMetabolismeGlucidique);
		    
		    //GESTION DES ANALYSES DU BILAN LIPIDIQUE
		    $pdf->setAnalysesBilanLipidique($analysesBilanLipidique);
		    
		    //GESTION DES ANALYSES DE METABOLISME DU FER
		    $pdf->setAnalysesMetabolismeFer($analysesMetabolismeFer);
		    
		    //GESTION DES ANALYSES DU BILAN D'ELECTROLYTE
		    $pdf->setAnalysesBilanElectrolyte($analysesBilanElectrolyte);
		    
		    //GESTION DES ANALYSES DU TYPAGE (Helectrophorèse)
		    $pdf->setAnalysesTypageHemoProteine($analysesTypageHemoProteine);
		    
		    //GESTION DES ANALYSES DE METABOLISME PROTIDIQUE
		    $pdf->setAnalysesMetabolismeProtidique($analysesMetabolismeProtidique);
		    
		    
		    /*
		     * Envoie des données pour affichage
		    */
		    $pdf->affichageResultatsAnalysesDemandees();
		}
		
		//========= Créaton de la page Sérologie HIV ========
		//========= Créaton de la page Sérologie HIV ========
		//========= Créaton de la page Sérologie HIV ========
		if($analysesSerologieHIV){
		
			$pdf->setAnalysesSerologieHIV($analysesSerologieHIV);
				
			/*
			 * Envoie des données pour affichage
			*/
			$pdf->affichageResultatsSerologieHIV();
		}
		
		//========= Création de la page PV =========
		//========= Création de la page PV =========
		//========= Création de la page PV =========
		if($analysesSerologiePV){
		
		    $pdf->setAnalysesSerologiePV($analysesSerologiePV);
		    $pdf->setResultatsAntiBioGrammePVDemande($resultatsAntiBioGrammeDuPV);
		
		    /*
		     * Envoie des données pour affichage
		     */
		    $pdf->affichageResultatsPV();
		}
		
		
		//========= Créaton de la dernière page ========
		//========= Créaton de la dernière page ========
		//========= Créaton de la dernière page ========
		if($analysesTypageHemoglobine){
				
			$pdf->setAnalysesTypageHemoglobine($analysesTypageHemoglobine);
			
			/*
			 * Envoie des données pour affichage
			 */
			$pdf->affichageResultatsTypageHemoglobine();
		}
	
		
		//Afficher le document contenant les différentes pages
		//Afficher le document contenant les différentes pages
		//Afficher le document contenant les différentes pages
		$pdf->Output('I');
	
	}
	
	
	
}
