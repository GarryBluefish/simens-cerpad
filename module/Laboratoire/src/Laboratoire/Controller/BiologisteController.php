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
	
 	    //$info = $this->getResultatDemandeAnalyseTable()->getDemandeAnalysesAvecIddemande(768);
// 	    $depistage = $this->getPatientTable()->getDepistagePatient(66);
//  	    if($depistage->current()){
//  	    	if($depistage->current()['typepatient'] == 1){
//  	    		$infoPatient = $this->getPatientTable()->getPatient(66);
//  	    		$codepatient = str_replace('E', 'I', $infoPatient->codepatient);
 	    		
//  	    		if($codepatient){ $this->getPatientTable()->updatePatientCodePatient(66, $codepatient); }else{ $this->getPatientTable()->updatePatientCodePatient(66, null); }
 	    		
//  	    		var_dump($codepatient); exit();
 	    		
//  	    	}
//  	    }
	    
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
					}
					
				}
					
			}
		}
		
		$this->getResultatDemandeAnalyseTable() ->validerResultDemande($iddemande, $idemploye);
		$nbAnalysesDemandees = $this->getPatientTable ()->getListeAnalysesDemandeesResultats($iddemande)->count();
		
		
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $nbAnalysesDemandees ) );
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
	        </script>";
	    }
	    return $html;
	}
	
	protected function getResultatsTestCombsIndirect($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursTestCombsIndirect($iddemande);
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script>
	        	$('#type_materiel_test_combs_indirect').val('".str_replace( "'", "\'",$resultat['type_materiel'])."');		
	            $('#test_combs_indirect').val('".$resultat['valeur']."');
	            if('".$resultat['valeur']."' == 'Positif'){ 
	                setTimeout(function(){ 
	                   $('.titre_combs_indirect').toggle(true); 
	                   $('#titre_combs_indirect').val('".$resultat['titre']."'); 
	                }); 
	            }
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
	            $('#vitesse_sedimentation').val('".$resultat['valeur']."');
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
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script>
 	        	$('#type_materiel_goutte_epaisse').val('".str_replace( "'", "\'",$resultat['type_materiel'])."');
	            $('#goutte_epaisse').val('".$resultat['goutte_epaisse']."');
	            if('".$resultat['goutte_epaisse']."' == 'Positif'){ $('#goutte_epaisse_positif').toggle(true); $('#densite_parasitaire').val('".$resultat['densite_parasitaire']."'); }
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
	    	   
	    	    $('.iconeValidationInterfaceVisual').html('<span  class=\'resultat_existe_interface_visual_".$iddemande."\' > <img id=\'resultat_existe_interface_visual_".$iddemande."\' onclick=\'validerResultatAnalyseInterfaceVisual(".$iddemande.");\' style=\'float: right; padding-right: 10px; cursor: pointer;\' src=\'../images_icons/74bis.png\' > </span>');
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
	    	    $('#bilirubine_totale_mg').val('".$resultat['bilirubine_totale_mg']."');
    	        $('#bilirubine_totale_umol').val('".$resultat['bilirubine_totale_umol']."');
   	            $('#bilirubine_directe').val('".$resultat['bilirubine_directe']."');
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
	                $('#electro_hemo_label_".($i+1)."').val('".$resultat[$i]['libelle']."').attr('readonly',true);
	                $('#electro_hemo_valeur_".($i+1)."').val('".$resultat[$i]['valeur']."').attr('readonly',true);
	              }, 50);
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
	    	    $('#proteinurie').val('".$resultat['proteinurie']."');
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
	    	    $('#toxoplasmose_1').val('".$resultat['toxoplasmose_1']."');
	    	    $('#toxoplasmose_2').val('".$resultat['toxoplasmose_2']."');
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
	    	    $('#rubeole_1').val('".$resultat['rubeole_1']."');
	    	    $('#rubeole_2').val('".$resultat['rubeole_2']."');
	    	 </script>";
	    }
	    return $html;
	}
	
	protected function getResultatsCulotUrinaire($iddemande){
	    $resultat = $this->getResultatDemandeAnalyseTable()->getValeursCulotUrinaire($iddemande);
	    $html ="";
	    if($resultat){
	        $html .=
	        "<script>
	            $('#type_materiel_culot_urinaire').val('".str_replace( "'", "\'", $resultat['type_materiel'])."');
	    	    $('#culot_urinaire_1').val('".$resultat['culot_urinaire_1']."');
	    	    $('#culot_urinaire_2').val('".$resultat['culot_urinaire_2']."');
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
	    	    $('#serologie_syphilitique').val('".$resultat['serologie_syphilitique']."');
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
	    	    $('#aslo').val('".$resultat['aslo']."');
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
		if($analyse['Idanalyse'] == 64){ $html .= $this->hiv_64(); }
		if($analyse['Idanalyse'] == 65){ $html .= $this->pv_65(); }
		if($analyse['Idanalyse'] == 66){ $html .= $this->ecbu_66(); }
		if($analyse['Idanalyse'] == 67){ $html .= $this->pus_67(); }
		if($analyse['Idanalyse'] == 68){ $html .= $this->typage_hemoglobine_68();  $html .= $this->getResultatsTypageHemoglobine($iddemande); }
		
		
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
	            //$this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            //$donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursTestCombsIndirect($tab, $iddemande);
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
	        if($idanalyse == 68){
	            //$this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	            //$donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursTypageHemoglobine($tab, $iddemande);
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
			if($liste['Idanalyse'] == 64){ $html .= $this->hiv_64(); }
			if($liste['Idanalyse'] == 65){ $html .= $this->pv_65(); }
			if($liste['Idanalyse'] == 66){ $html .= $this->ecbu_66(); }
			if($liste['Idanalyse'] == 67){ $html .= $this->pus_67(); }
			if($liste['Idanalyse'] == 68){ $html .= $this->typage_hemoglobine_68();  $html .= $this->getResultatsTypageHemoglobine($liste['iddemande']); }
			
			
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
	            if($idanalyse == 68){
	                $tab = $tableau[$idanalyse];
	                $this->getResultatDemandeAnalyseTable()->addResultatDemandeAnalyse($iddemande, $idemploye);
	                $donneesExiste = $this->getResultatDemandeAnalyseTable()->addValeursTypageHemoglobine($tab, $iddemande);
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
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
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
	    $html .="         <td style='width: 35%; '> <input id='champ24' type='number' readonly  step='any' > /mm<sup>3</sup> </td>";
	    $html .="         <td style='width: 35%; '> <input id='champ25' type='number' step='any'  min='0' max='100' readonly> % </td>";
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
	    $html .= "  <td colspan='2' ><label style='height: 80px;' ><span style='font-size: 16px; float: left;  margin-left: 30px;'> Commentaire:  </span> <textarea id='commentaire_hemogramme' style='max-height: 57px; min-height: 57px; max-width: 400px; min-width: 400px; margin-left: 30px;' > </textarea> </label></td>";
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
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
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
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> Pr&eacute;sence d'antig&egrave;ne <select name='antigene_d_faible' id='antigene_d_faible' disabled> <option >  </option> <option value='Present' >Pr&eacute;sent</option> <option value='Absent' >Absent</option> </select></span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'>  </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'>  </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "</table> </td> </tr>";
	    
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
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
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
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_test_combs_indirect' readonly> </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> Test <select name='test_combs_indirect' id='test_combs_indirect' onchange='getTestCombsIndirect(this.value)' disabled> <option >  </option> <option value='Positif' >Positif</option> <option value='Negatif' >N&eacute;gatif</option> </select></span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px; text-align: right; '> <span class='titre_combs_indirect' style='display: none;'> Titre </span> </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> <input class='titre_combs_indirect' id='titre_combs_indirect' type='text' style='display: none;' readonly> </label></td>";
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
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
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
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> Valeur VS 1 heure <input type='number' id='vitesse_sedimentation' name='vitesse_sedimentation' readonly> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> mm </label></td>";
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
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
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
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
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
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
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
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_medulodramme' readonly> </div> </td>";
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
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_cytochimie_myeloperoxydase' readonly> </div> </td>";
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
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_tp_inr' readonly> </div> </td>";
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
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
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
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> fibrin&eacute;mie <input id='fibrinemie' type='number' step='any' readonly> </span></label></td>";
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
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> temps de saignement <input id='temps_saignement' type='number' step='any' readonly> </span></label></td>";
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
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
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
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
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
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
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
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
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
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
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
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> ur&eacute;e sanguine <input id='uree_sanguine' type='number' step='any' readonly> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> g/l </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> N: 0,15 &agrave; 0,45</label></td>";
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
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> uric&eacute;mie <input id='acide_urique' type='number' step='any' readonly> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> mg/l </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 85%;'> N : H= 35  &agrave; 72, F= 26 &agrave; 60 </label></td>";
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
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
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
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
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
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
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
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
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
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
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
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
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
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' style='height: 40px;'><span style='font-weight: bold; '> calc&eacute;mie <input id='calcemie' type='number' step='any' readonly> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;  height: 40px;'> mg/l </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%; height: 40px;'> N: Adultes: 86 &agrave; 103  <spa style='padding-left: 18px;'> Enfants: 100 &agrave; 120 </spa> </label></td>";
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
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
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
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' style='height: 40px;'><span style='font-weight: bold; '> phosphor&eacute;mie <input id='phosphoremie' type='number' step='any' readonly> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;  height: 40px;'> mg/l </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%; height: 40px;'> N: Adultes: 25 &agrave; 50  <spa style='padding-left: 18px;'> Enfants: 40 &agrave; 70 </spa> </label></td>";
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
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
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
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
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
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
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
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold; '> Gama GT <input id='gama_gt' type='number' step='any' readonly> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> u/l </label></td>";
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
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
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
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
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
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '> Bilirubine totale <input id='bilirubine_totale_mg' type='number' step='any' readonly > </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> mg/l </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> </label></td>";
	    $html .= "</tr>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold; '> Bilirubine totale <input id='bilirubine_totale_umol' type='number' step='any' readonly > </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> umol/l </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold; '> Bilirubine directe <input id='bilirubine_directe' type='number' step='any' readonly > </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> mg/l </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> </label></td>";
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
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
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
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
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
	    
        $html .= "<tr class='ligneAnanlyse' id='electro_hemo_mp' style='width: 100%;'>";
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
	    $html .= "  <td colspan='3' style='width: 45%;'> </td>";
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
	    $html .= "  <td colspan='3' ><label style='height: 80px;' ><span style='font-size: 16px; float: left;  margin-left: 30px;'> Commentaire:  </span> <textarea id='commentaire_electrophorese_proteine' style='max-height: 57px; min-height: 57px; max-width: 400px; min-width: 400px; margin-left: 30px;' tabindex='8'> </textarea> </label></td>";
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
	    $html .= "  <td colspan='3' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '> Albumin&eacute;mie <input id='albuminemie' type='number' step='any' tabindex='2' readonly> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> g/l </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> 35 - 53 </label></td>";
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
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
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
	    $html .= "  <td colspan='3' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 45%;'><label class='lab1'><span style='font-weight: bold; '> Protid&eacute;mie <input id='protidemie' type='number' step='any' tabindex='2' readonly> </span></label></td>";
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
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '> Prot&eacute;inurie <input id='proteinurie' type='number' step='any' tabindex='2' readonly > </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> g/24H </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> 0,2 - 0,15 g/24H </label></td>";
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
	    $html .= "  <td colspan='3' style='width: 45%;'> </td>";
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
	    $html .= "  <td colspan='3' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '> B&eacute;ta HCG <input id='beta_hcg_plasmatique' type='number' step='any' tabindex='2' readonly > </span></label></td>";
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
	    $html .= "  <td colspan='3' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '>  <input id='psa' type='number' step='any' tabindex='2' readonly > </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> ng/ml </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> N: 0,1 &agrave;  2,6 </label></td>";
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
	    $html .= "  <td colspan='3' style='width: 45%;'> </td>";
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
	    $html .= "  <td colspan='3' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '> RF <input id='facteurs_rhumatoides' type='number' step='any' tabindex='2' readonly > </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> ul/ml </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> N: 0 - 30 </label></td>";
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
	    $html .= "  <td colspan='3' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '> rf waaler rose <input id='rf_waaler_rose' type='number' step='any' tabindex='2' readonly > </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'> ng/ml </label></td>";
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
	    $html .= "  <td colspan='3' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '>  <input id='toxoplasmose_1' type='number' step='any' tabindex='2' readonly > </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'>  </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'>  </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '>  <input id='toxoplasmose_2' type='number' step='any' tabindex='3' readonly > </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'>  </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'>  </label></td>";
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
	    $html .= "  <td colspan='3' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '>  <input id='rubeole_1' type='number' step='any' tabindex='2' readonly > </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'>  </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'>  </label></td>";
	    $html .= "</tr>";
	     
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '>  <input id='rubeole_2' type='number' step='any' tabindex='3' readonly > </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'>  </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'>  </label></td>";
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
	    $html .= "  <td colspan='2' style='width: 35%;'> </td>";
	    $html .= "</tr>";
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%; font-family: times new roman; font-size: 15px;'>";
	    $html .= "  <td style='width: 45%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_culot_urinaire' tabindex='1' readonly> </div> </td>";
	    $html .= "  <td colspan='3' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '>  <input id='culot_urinaire_1' type='number' step='any' tabindex='2' readonly > </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'>  </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'>  </label></td>";
	    $html .= "</tr>";
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '>  <input id='culot_urinaire_2' type='number' step='any' tabindex='3' readonly > </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'>  </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'>  </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
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
	    $html .= "  <td colspan='3' style='width: 45%;'> </td>";
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
	    $html .= "  <td colspan='3' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '>  <input id='serologie_syphilitique' type='number' step='any' tabindex='2' readonly > </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'>  </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'>  </label></td>";
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
	    $html .= "  <td colspan='3' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '> aslo <input id='aslo' type='number' step='any' tabindex='2' readonly > </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'>  </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> < 200 UI/mL </label></td>";
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
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '> Typhi TO <input id='widal_to' type='number' step='any' tabindex='2'> </span></label></td>";
	    $html .= "  <td colspan='2' style='width: 45%;'><label class='lab2' style='padding-top: 5px; width: 75%; padding-left: 40px;'> Titre: <input id='widal_titre_to' style='width: 60%; padding-right: 5px;' type='text' tabindex='2' readonly > </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '> Typhi TH <input id='widal_th' type='number' step='any' tabindex='3'> </span></label></td>";
	    $html .= "  <td colspan='2' style='width: 45%;'><label class='lab2' style='padding-top: 5px; width: 75%; padding-left: 40px;'> Titre: <input id='widal_titre_th' style='width: 60%; padding-right: 5px;' type='text' tabindex='3' readonly > </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '> Paratyphi AO <input id='widal_ao' type='number' step='any' tabindex='4'> </span></label></td>";
	    $html .= "  <td colspan='2' style='width: 45%;'><label class='lab2' style='padding-top: 5px; width: 75%; padding-left: 40px;'> Titre: <input id='widal_titre_ao' style='width: 60%; padding-right: 5px;' type='text' tabindex='4' readonly > </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '> Paratyphi AH <input id='widal_ah' type='number' step='any' tabindex='5'> </span></label></td>";
	    $html .= "  <td colspan='2' style='width: 45%;'><label class='lab2' style='padding-top: 5px; width: 75%; padding-left: 40px;'> Titre: <input id='widal_titre_ah' style='width: 60%; padding-right: 5px;' type='text' tabindex='5' readonly > </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '> Paratyphi BO <input id='widal_bo' type='number' step='any' tabindex='6'> </span></label></td>";
	    $html .= "  <td colspan='2' style='width: 45%;'><label class='lab2' style='padding-top: 5px; width: 75%; padding-left: 40px;'> Titre: <input id='widal_titre_bo' style='width: 60%; padding-right: 5px;' type='text' tabindex='6' readonly > </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '> Paratyphi BH <input id='widal_bh' type='number' step='any' tabindex='7'> </span></label></td>";
	    $html .= "  <td colspan='2' style='width: 45%;'><label class='lab2' style='padding-top: 5px; width: 75%; padding-left: 40px;'> Titre: <input id='widal_titre_bh' style='width: 60%; padding-right: 5px;' type='text' tabindex='7' readonly > </label></td>";
	    $html .= "</tr>";
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '> Paratyphi CO <input id='widal_co' type='number' step='any' tabindex='8'> </span></label></td>";
	    $html .= "  <td colspan='2' style='width: 45%;'><label class='lab2' style='padding-top: 5px; width: 75%; padding-left: 40px;'> Titre: <input id='widal_titre_co' style='width: 60%; padding-right: 5px;' type='text' tabindex='8' readonly > </label></td>";
	    $html .= "</tr>";
	     
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '> Paratyphi CH <input id='widal_ch' type='number' step='any' tabindex='9'> </span></label></td>";
	    $html .= "  <td colspan='2' style='width: 45%;'><label class='lab2' style='padding-top: 5px; width: 75%; padding-left: 40px;'> Titre: <input id='widal_titre_ch' style='width: 60%; padding-right: 5px;' type='text' tabindex='9' readonly > </label></td>";
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
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '> Ag Hbs <input id='ag_hbs' type='number' step='any' tabindex='2' readonly > </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'>  </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> Quantitatif </label></td>";
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
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	    	  
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1'><span style='font-weight: bold; '> HIV <select id='hiv' disabled> <option >  </option> <option value='positif' >Positif</option> <option value='negatif' >N&eacute;gatif</option> </select> </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'>  </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'>  </label></td>";
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
	    $html .= "  <td style='width: 55%;'> <div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_pv' tabindex='1' readonly> </div> </td>";
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
	    $html .= "</tr>";
	    //POUR LE NOM DU TYPE DE MATERIEL UTILISE
	
	    $html .= "<tr class='ligneAnanlyse' style='width: 100%;'>";
	    $html .= "  <td style='width: 55%;'><label class='lab1' ><span style='font-weight: bold;'> pv (En attente ... ) </span></label></td>";
	    $html .= "  <td style='width: 15%;'><label class='lab2' style='padding-top: 5px;'>  </label></td>";
	    $html .= "  <td style='width: 30%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> </label></td>";
	    $html .= "</tr>";
	
	    $html .= "</table> </td> </tr>";
	
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
	    $html .= "  <td colspan='2' style='width: 45%;'> </td>";
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
	    $html .= "  <td style='width: 55%;'><div class='noteTypeMateriel' style='float: left; height: 30px; width: 70%; padding-left: 10px;'> <input type='text' id='type_materiel_typage_hemoglobine' readonly> </div></td>";
	    $html .= "  <td colspan='2' style='width: 45%;' class='iconeValidationInterfaceVisual' > </td>";
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
	
	
	
	public function impressionResultatsAnalysesDemandeesAction()
	{
		$service = $this->layout()->user['NomService'];
		$iddemande = $this->params()->fromPost( 'iddemande' );
		 
		$idpatient = $this->getPatientTable()->getDemandeAnalysesAvecIddemande($iddemande)['idpatient'];
		$personne  = $this->getPersonneTable()->getPersonne($idpatient);
		$patient   = $this->getPatientTable()->getPatient($idpatient);
		$depistage = $this->getPatientTable()->getDepistagePatient($idpatient);
		 
		//Recuperation de la liste des analyses pour lesquelles les résultats sont déjà renseignés
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
			
			
			elseif($idanalyse == 56){ //TOXOPLASMOSE
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$resultatsAnalysesDemandees[56] = $this->getResultatDemandeAnalyseTable()->getValeursToxoplasmose($iddemande);
			}
			
			elseif($idanalyse == 57){ //RUBEOLE
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$resultatsAnalysesDemandees[57] = $this->getResultatDemandeAnalyseTable()->getValeursRubeole($iddemande);
			}
			//=========================================================
			//=========================================================
			
			
			//SEROLOGIE  ---  SEROLOGIE  ---  SEROLOGIE
			//SEROLOGIE  ---  SEROLOGIE  ---  SEROLOGIE
			//SEROLOGIE  ---  SEROLOGIE  ---  SEROLOGIE
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
				$resultatsAnalysesDemandees[48] = $this->getResultatDemandeAnalyseTable()->getValeursElectrophoreseProteines($iddemande);
			}
				
			elseif($idanalyse == 49){ //PROTEINURIE DES 24H (PU 24H)
				$analysesDemandees  [$j++] = $listeResultats[$i];
				$analysesMetabolismeProtidique[49] = 49;
				$resultatsAnalysesDemandees[49] = $this->getResultatDemandeAnalyseTable()->getValeursElectrophoreseProteines($iddemande);
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
		   $analysesBilanLipidique || $analysesBilanHepatique || $analysesBilanRenal || $analysesSerologie || $analysesTypageHemoProteine){
			
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
			
			
			//Ajouter une note à la page
			$page2->addNote();
			//Ajouter la page au document
			$DocPdf->addPage($page2->getPage());
			$temoinEntre = 1;
		}

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
	
}
