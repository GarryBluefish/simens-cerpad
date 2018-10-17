<?php

namespace Secretariat\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Json\Json;
use Secretariat\Form\PatientForm;
use Secretariat\Model\Personne;
use Secretariat\View\Helper\DateHelper;
use Secretariat\View\Helper\DocumentPdf;
use Secretariat\View\Helper\DemandeAnalysePdf;
use Secretariat\View\Helper\AnalysesDemandeesPdf;
use Secretariat\Form\ParentPatientForm;
use Secretariat\Model\Parents;
use Secretariat\View\Helper\ResultatsAnalysesDemandeesPdf;
use Secretariat\View\Helper\DocumentResultatsPdf;
use Secretariat\View\Helper\ResultatsNfsPdf;
use Secretariat\View\Helper\ResultatsTypageHemoglobinePdf;
use Laboratoire\View\Helper\ImprimerResultatsAnalysesDemandees;

class SecretariatController extends AbstractActionController {
	protected $personneTable;
	protected $patientTable;
	protected $analyseTable;
	protected $resultatDemandeAnalyseTable;
	protected $listeRechercheTable;
	protected $listeDossierPatientTable;
	
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
	
	public function getListeRechercheTable() {
		if (! $this->listeRechercheTable) {
			$sm = $this->getServiceLocator ();
			$this->listeRechercheTable = $sm->get ( 'Secretariat\Model\listeRechercheTable' );
		}
		return $this->listeRechercheTable;
	}
	
	public function getListeDossierPatientTable() {
		if (! $this->listeDossierPatientTable) {
			$sm = $this->getServiceLocator ();
			$this->listeDossierPatientTable = $sm->get ( 'Secretariat\Model\listeDossierPatientTable' );
		}
		return $this->listeDossierPatientTable;
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
	
	public function numeroOrdrePatient($idpatient) {
		$nbCharNum = 6 - strlen($idpatient);
	
		$chaine ="";
		for ($i=1 ; $i <= $nbCharNum ; $i++){
			$chaine .= '0';
		}
		$chaine .= $idpatient;
	
		return $chaine;
	}
	
	public function ajouterPatientAction() {
		$this->layout ()->setTemplate ( 'layout/secretariat' );
		
		//$verifPatientExiste = $this->getPersonneTable()->verifPatientExiste();
		//var_dump($verifPatientExiste[1][0]); exit();
		
		$form = new PatientForm();
		$formParent = new ParentPatientForm();
		
		$form->get('nationalite_origine')->setvalueOptions($this->getPersonneTable()->listeDeTousLesPays());
		$form->get('nationalite_actuelle')->setvalueOptions($this->getPersonneTable()->listeDeTousLesPays());
		
		$request = $this->getRequest(); 
		if ($request->isPost()) {
			$personne =  new Personne();
			$form->setInputFilter($personne->getInputFilter());
			$form->setData($request->getPost());
			
			if ($form->isValid()) {
				
				$personne->exchangeArray($form->getData());
				$personne->photo = 'identite.jpg';
				
				$fileBase64 = substr($this->params ()->fromPost ('fichier_tmp'), 23);
				if($fileBase64){ $img = imagecreatefromstring(base64_decode($fileBase64)); } else { $img = false; }
				
				if ($img != false) {
					$photo = (new \DateTime ( 'now' )) ->format ( 'dmy_His' ).'.jpg';
					imagejpeg ( $img, $this->baseUrl().'public/img/photos_patients/' . $photo );
					$personne->photo = $photo;
				}
				
 				$idpersonne = $this->getPersonneTable() ->savePersonne($personne);
 				
				if($personne->sexe == 'Masculin'){ $sexe = 1; }else{ $sexe = 2; }
				$numeroOrdrePatient = $this->numeroOrdrePatient($idpersonne);
				$codepatient = $sexe.'-'.$numeroOrdrePatient.'-E';
				
 				$idemploye = $this->layout()->user['idemploye'];
 				//$this->getPatientTable()->savePatient($idpersonne, $idemploye, $codePatient);
 				$this->getPatientTable()->savePatientAvecNumeroDossier($idpersonne, $idemploye, $codepatient, $sexe);
				if($personne->depistage == 1){ 
				    $this->getPatientTable()->saveDepistagePatient($idpersonne, $personne->ethnie, $idemploye); 
				}
				
				//INFORMATIONS PARENTALES
				//INFORMATIONS PARENTALES
				$donnees = $request->getPost();
				$this->getPersonneTable() ->addInfosParentales($donnees, $idpersonne, $idemploye);

				
				return $this->redirect()->toRoute('secretariat' , array('action'=>'liste-patient') );
				
			}
			
		}

		
		$data = array('nationalite_origine' => 'SÃ©nÃ©gal', 'nationalite_actuelle' => 'SÃ©nÃ©gal');
		$form->populateValues($data);
		
		$listeEthniesDepistes = $this->getPatientTable()->getListeEthniesPatientsDepistes();
		
		return new ViewModel ( array (
				'form' => $form,
		        'formParent' => $formParent,
				'listeEthniesDepistes' => $listeEthniesDepistes,
		) );
	}
	
	public function listePatientsAjaxAction() {
	
		//$output = $this->getPatientTable ()->listePatientsAjax();
		$output = $this->getListeDossierPatientTable()->fetchAll();
		return $this->getResponse ()->setContent ( Json::encode ( $output, array (
				'enableJsonExprFinder' => true
		) ) );
		
	}
	
	public function supprimerPatientAction() {
		$id = ( int ) $this->params ()->fromPost ( 'id', 0 );
		$this->getPatientTable ()->deletePatient ( $id );
	
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode (  ) );
	}
	
	
	protected function nbJours($debut, $fin) {
	    //60 secondes X 60 minutes X 24 heures dans une journee
	    $nbSecondes = 60*60*24;
	
	    $debut_ts = strtotime($debut);
	    $fin_ts = strtotime($fin);
	    $diff = $fin_ts - $debut_ts;
	    return ($diff / $nbSecondes);
	}
	
	public function listePatientAction() {
		$this->layout ()->setTemplate ( 'layout/secretariat' );
		
		$timestart = microtime(true);
		
		//$output = $this->getPatientTable ()->listePatientsAjax();
		//var_dump($output); exit();
		
		//$listeDossierPatient = $this->getListeDossierPatientTable()->fetchAll();
		//var_dump($listeDossierPatient); exit();
		
		$timeend = microtime(true);
		$time = $timeend-$timestart;
		
		//var_dump(number_format($time,3)); exit();
		
		
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
		
	    $infoTypageHemoglobine = $this->getPatientTable()->getTypageHemoglobineParType(1);
	     
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
	    			if(!in_array($depistage->current()['typage'], $infoTypageHemoglobine )){
	    				$typage = "(<span style='color: red;'>".$depistage->current()['typage']."</span>)" ;
	    			}else{
	    				$typage = "(".$depistage->current()['typage'].")" ;
	    			}
	    		}
	    	}
	    }
		
		
		$html ="
				
	    <div style='width: 100%;'>

        <img id='photo' src='".$this->chemin()."/img/photos_patients/".$personne->photo."' style='float:left; margin-right:40px; width:105px; height:105px;'/>";

		//Gestion des AGES
		//Gestion des AGES
		if($personne->age && !$personne->date_naissance){
		    $html .="<div style=' left: 70px; top: 235px; font-family: time new romans; position: absolute; '> Age: <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$personne->age." ans </span></div>";
		}else{
			
		    $aujourdhui = (new \DateTime() ) ->format('Y-m-d');
		    $age_jours = $this->nbJours($personne->date_naissance, $aujourdhui);
		    /*
		    if($age_jours < 31){
		        $html .="<div style=' left: 70px; top: 235px; font-family: time new romans; position: absolute; '> Age: <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_jours." jours </span></div>";		        
		    }else if($age_jours >= 31) {
		        
		        $nb_mois = (int)($age_jours/30);
		        $nb_jours = $age_jours - ($nb_mois*30);
		        
		        $html .="<div style=' left: 70px; top: 235px; font-family: time new romans; position: absolute; '> Age: <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$nb_mois."m ".$nb_jours."j </span></div>";
		    }
		    */
		    
		    $age_annees = (int)($age_jours/365);
		    
		    if($age_annees == 0){
		    		
		    	if($age_jours < 31){
		    		$html .="<div style=' left: 70px; top: 235px; font-family: time new romans; position: absolute; '> Age: <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_jours." jours </span></div>";
		    	}else if($age_jours >= 31) {
		    			
		    		$nb_mois = (int)($age_jours/31);
		    		$nb_jours = $age_jours - ($nb_mois*31);
		    		if($nb_jours == 0){
		    			$html .="<div style=' left: 70px; top: 235px; font-family: time new romans; position: absolute; '> Age: <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$nb_mois."m </span></div>";
		    		}else{
		    			$html .="<div style=' left: 70px; top: 235px; font-family: time new romans; position: absolute; '> Age: <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$nb_mois."m ".$nb_jours."j </span></div>";
		    		}

		    	}
		    		
		    }else{
		    	$age_jours = $age_jours - ($age_annees*365);
		    
		    	if($age_jours < 31){
		    
		    		if($age_annees == 1){
		    			if($age_jours == 0){
		    				$html .="<div style=' left: 60px; top: 235px; font-family: time new romans; position: absolute; '> Age: <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_annees."an </span></div>";
		    			}else{
		    				$html .="<div style=' left: 60px; top: 235px; font-family: time new romans; position: absolute; '> Age: <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_annees."an ".$age_jours." j </span></div>";
		    			}
		    		}else{
		    			if($age_jours == 0){
		    				$html .="<div style=' left: 60px; top: 235px; font-family: time new romans; position: absolute; '> Age: <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_annees."ans </span></div>";
		    			}else{
		    				$html .="<div style=' left: 60px; top: 235px; font-family: time new romans; position: absolute; '> Age: <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_annees."ans ".$age_jours."j </span></div>";
		    			}
		    		}
		    			
		    	}else if($age_jours >= 31) {
		    			
		    		$nb_mois = (int)($age_jours/31);
		    		$nb_jours = $age_jours - ($nb_mois*31);
		    
		    		if($age_annees == 1){
		    			if($nb_jours == 0){
		    				$html .="<div style=' left: 50px; top: 235px; font-family: time new romans; position: absolute; '> Age: <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_annees."an ".$nb_mois."m </span></div>";
		    			}else{
		    				$html .="<div style=' left: 50px; top: 235px; font-family: time new romans; position: absolute; '> Age: <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_annees."an ".$nb_mois."m ".$nb_jours."j </span></div>";
		    			}

		    		}else{
		    			if($nb_jours == 0){
		    				$html .="<div style=' left: 50px; top: 235px; font-family: time new romans; position: absolute; '> Age: <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_annees."ans ".$nb_mois."m </span></div>";
		    			}else{
		    				$html .="<div style=' left: 50px; top: 235px; font-family: time new romans; position: absolute; '> Age: <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_annees."ans ".$nb_mois."m ".$nb_jours."j </span></div>";
		    			}
		    		}
		    
		    	}
		    		
		    }
		    
		}
		
		//Fin gestion des ages
		//Fin gestion des ages

		
        $html .="<p>
         <img id='photo' src='".$this->chemin()."/img/photos_patients/".$personne->photo."' style='float:right; margin-right:15px; width:95px; height:95px; color: white; opacity: 0.09;'/>
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
                   <!-- div  id='aa'><a style='text-decoration: underline;'>Type </a><br><p style='font-weight: bold;font-size: 19px;'> ".$type." ".$typage."</p></div-->";
			if($informations_parentales){
			    $html .="<div style='width: 50px; height: 35px; float: right; margin-top: -5px; '><a href='javascript:infos_parentales(".$id.");' > <img id='infos_parentales_".$id."' style='float: right; cursor: pointer; ' src='../images_icons/Infos_parentales.png' /> </a></div>";
			}   
               
       $html .="</td>
			   	
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
	
	public function modifierPatientAction() {

		$this->layout ()->setTemplate ( 'layout/secretariat' );
		
		$idpersonne = (int) $this->params()->fromRoute('val', 0);
		
		$form = new PatientForm();
		$formParent = new ParentPatientForm();
		
		$form->get('nationalite_origine')->setvalueOptions($this->getPersonneTable()->listeDeTousLesPays());
		$form->get('nationalite_actuelle')->setvalueOptions($this->getPersonneTable()->listeDeTousLesPays());
		
		$request = $this->getRequest();
		if ($request->isPost()) {
			$personne =  new Personne();
			$form->setInputFilter($personne->getInputFilter());
			$form->setData($request->getPost());
				
			if ($form->isValid()) {
				
				$personne->exchangeArray($form->getData());
				
				$fileBase64 = substr($this->params ()->fromPost ('fichier_tmp'), 23);
				if($fileBase64){ $img = imagecreatefromstring(base64_decode($fileBase64)); } else { $img = false; }
		
				if ($img != false) {
					$photo = (new \DateTime ( 'now' )) ->format ( 'dmy_His' ).'.jpg';
					imagejpeg ( $img, $this->baseUrl().'public/img/photos_patients/' . $photo );
					$personne->photo = $photo;
				}
				$idpatient = $personne->idpersonne;
				$typepatient = $personne->typepatient; if(!$typepatient){ $typepatient = 0; }
				
				$this->getPersonneTable() ->savePersonne($personne);
				$idemploye = $this->layout()->user['idemploye'];
				$patient = $this->getPatientTable()->getPatient($idpatient);
				if($personne->sexe == 'Masculin'){ $numero_dossier = substr_replace($patient->numero_dossier, 1, 0, 1); }else{  $numero_dossier = substr_replace($patient->numero_dossier, 2, 0, 1);  }
				$this->getPatientTable()->updatePatient($idpatient, $numero_dossier, $idemploye);
				
				if($personne->depistage == 1){ $this->getPatientTable()->updateDepistagePatient($idpatient, $personne->ethnie, $typepatient, $idemploye); }
		
				//INFORMATIONS PARENTALES
				//INFORMATIONS PARENTALES
				$donnees = $request->getPost();
			    $this->getPersonneTable() ->updateInfosParentales($donnees, $idpatient, $idemploye);
				
				return $this->redirect()->toRoute('secretariat' , array('action'=>'liste-patient') );
		
			}
			
		}

		$personne = $this->getPersonneTable()->getPersonne($idpersonne);
		$patient = $this->getPatientTable()->getPatient($idpersonne);
		$depistage = $this->getPatientTable()->getDepistagePatient($idpersonne);
		$typepatient = 0;
		if($depistage->current( )){
		    $personne->ethnie = $depistage->current()['ethnie']; 
		    $personne->typepatient = $depistage->current()['typepatient'];
		    $personne->depistage = 1;
		    $typepatient = $depistage->current()['typepatient'];
		}
		
		$form->bind($personne);
		
		$data = array();
		if($personne->date_naissance){ $data['date_naissance'] = (new DateHelper())->convertDate( $personne->date_naissance ); }
		$form->populateValues($data);
		
		
		//INFORMATIONS PARENTALES
		//INFORMATIONS PARENTALES
		$infosParentales = $this->getPersonneTable()->getInfosParentales($idpersonne);
		if($infosParentales){
		    $donneesInfosParentales = array();
		    //DONNEES MATERNELLES
		    $infosMere = $infosParentales[0];
		    $donneesInfosParentales['prenom_mere'] = $infosMere['prenom'];
		    $donneesInfosParentales['nom_mere'] = $infosMere['nom'];
		    $donneesInfosParentales['profession_mere'] = $infosMere['profession'];
		    $donneesInfosParentales['telephone_mere'] = $infosMere['telephone'];
		    $donneesInfosParentales['fax_mere'] = $infosMere['fax'];
		    $donneesInfosParentales['email_mere'] = $infosMere['email'];
		    
		    //DONNEES PATERNELLES
		    $infosPere = $infosParentales[1];
		    $donneesInfosParentales['prenom_pere'] = $infosPere['prenom'];
		    $donneesInfosParentales['nom_pere'] = $infosPere['nom'];
		    $donneesInfosParentales['profession_pere'] = $infosPere['profession'];
		    $donneesInfosParentales['telephone_pere'] = $infosPere['telephone'];
		    $donneesInfosParentales['fax_pere'] = $infosPere['fax'];
		    $donneesInfosParentales['email_pere'] = $infosPere['email'];
		    
		    $formParent->populateValues($donneesInfosParentales);
		}
		
		return new ViewModel ( array (
		    'form' => $form,
		    'formParent' => $formParent,
		    'photo' => $personne->photo,
		    'typepatient' => $typepatient,
		    'depistage' => $depistage->count(),
		) );
		
	}
	
	
	public function demandesAnalysesAction() {
		
		$this->layout ()->setTemplate ( 'layout/secretariat' );
		
		//$idemploye = $this->layout()->user['idemploye'];
		//var_dump($idemploye); exit();

		//$listeAnalysesDemandees = $this->getAnalyseTable()->getAnalyseTypageHemoglobineDemande(23109);
		//var_dump($listeAnalysesDemandees); exit();
		
		
		
		//$timestart = microtime(true);
		
		//$output = $this->getPatientTable ()->listeRecherchePatientAjax();
		//$liste = $this->getListeRechercheTable()->fetchAll();
		//var_dump($liste); exit();
		//$listeDossierPatient = $this->getListeDossierPatientTable()->fetchAll();
		//var_dump($listeDossierPatient); exit();
		
		//$timeend = microtime(true);
		//$time = $timeend-$timestart;
		
		//var_dump(number_format($time,3)); exit();		
		
		
		return new ViewModel ( array () );
	}
	
	public function listeRecherchePatientAjaxAction() {
	
		$output = $this->getPatientTable ()->listeRecherchePatientAjax();
		return $this->getResponse ()->setContent ( Json::encode ( $output, array (
				'enableJsonExprFinder' => true
		) ) );
		
	}
	
	public function popupVuePatientAction() {

		$idpersonne = ( int ) $this->params ()->fromPost ( 'id', 0 );

		//MISE A JOUR DE L'AGE DU PATIENT
		//$personne = $this->getPatientTable()->miseAJourAgePatient($id);
		//*******************************

		$personne = $this->getPersonneTable ()->getPersonne($idpersonne);
			
		$date = null;

		if($personne->date_naissance){ $date = (new DateHelper())->convertDate( $personne->date_naissance ); }
		
		$html = "<div style='float:left;' ><div id='photo' style='float:left; margin-right:20px; margin-bottom: 10px;'> <img  src='".$this->chemin()."/img/photos_patients/" . $personne->photo . "'  style='width:105px; height:105px;'></div>";
			
		//Gestion des AGE
		if($personne->age && !$personne->date_naissance){
		    $html .= "<div style='margin-left:8px;'> <div style='text-decoration:none; font-size:14px; float:left; padding-right: 7px; '>Age:</div>  <div style='font-weight:bold; font-size:19px; font-family: time new romans; color: green; font-weight: bold;'>" . $personne->age . " ans</div></div></div>";
		    
		}else{
		    $aujourdhui = (new \DateTime() ) ->format('Y-m-d');
		    $age_jours = $this->nbJours($personne->date_naissance, $aujourdhui);
		    
		    $age_annees = (int)($age_jours/365);
		    
		    if($age_annees == 0){
		    
		    	if($age_jours < 31){
		    		$html .="<div style=' left: 30px; top: 145px; font-family: time new romans; position: absolute; '> Age: <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_jours." jours </span></div>";
		    	}else if($age_jours >= 31) {
		    		 
		    		$nb_mois = (int)($age_jours/31);
		    		$nb_jours = $age_jours - ($nb_mois*31);
		    		if($nb_jours == 0){
		    			$html .="<div style=' left: 30px; top: 145px; font-family: time new romans; position: absolute; '> Age: <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$nb_mois."m </span></div>";
		    		}else{
		    			$html .="<div style=' left: 30px; top: 145px; font-family: time new romans; position: absolute; '> Age: <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$nb_mois."m ".$nb_jours."j </span></div>";
		    		}
		    
		    	}
		    
		    }else{
		    	$age_jours = $age_jours - ($age_annees*365);
		    
		    	if($age_jours < 31){
		    
		    		if($age_annees == 1){
		    			if($age_jours == 0){
		    				$html .="<div style=' left: 30px; top: 145px; font-family: time new romans; position: absolute; '> Age: <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_annees."an </span></div>";
		    			}else{
		    				$html .="<div style=' left: 20px; top: 145px; font-family: time new romans; position: absolute; '> Age: <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_annees."an ".$age_jours." j </span></div>";
		    			}
		    		}else{
		    			if($age_jours == 0){
		    				$html .="<div style=' left: 30px; top: 145px; font-family: time new romans; position: absolute; '> Age: <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_annees."ans </span></div>";
		    			}else{
		    				$html .="<div style=' left: 20px; top: 145px; font-family: time new romans; position: absolute; '> Age: <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_annees."ans ".$age_jours."j </span></div>";
		    			}
		    		}
		    		 
		    	}else if($age_jours >= 31) {
		    		 
		    		$nb_mois = (int)($age_jours/31);
		    		$nb_jours = $age_jours - ($nb_mois*31);
		    
		    		if($age_annees == 1){
		    			if($nb_jours == 0){
		    				$html .="<div style=' left: 10px; top: 145px; font-family: time new romans; position: absolute; '> Age: <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_annees."an ".$nb_mois."m </span></div>";
		    			}else{
		    				$html .="<div style=' left: 10px; top: 145px; font-family: time new romans; position: absolute; '> Age: <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_annees."an ".$nb_mois."m ".$nb_jours."j </span></div>";
		    			}
		    
		    		}else{
		    			if($nb_jours == 0){
		    				$html .="<div style=' left: 10px; top: 145px; font-family: time new romans; position: absolute; '> Age: <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_annees."ans ".$nb_mois."m </span></div>";
		    			}else{
		    				$html .="<div style=' left: 10px; top: 145px; font-family: time new romans; position: absolute; '> Age: <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$age_annees."ans ".$nb_mois."m ".$nb_jours."j </span></div>";
		    			}
		    		}
		    
		    	}
		    
		    }
		}
		
			
		$html .= "<table>";
		
		$html .= "<tr>";
		$html .= "<td><a style='text-decoration:underline; font-size:12px;'>aa Nom:</a><br><p style='width:280px; font-weight:bold; font-size:17px;'>" . $personne->nom . "</p></td>";
		$html .= "</tr><tr>";
		$html .= "<td><a style='text-decoration:underline; font-size:12px;'>Pr&eacute;nom:</a><br><p style='width:280px; font-weight:bold; font-size:17px;'>" . $personne->prenom . "</p></td>";
		$html .= "</tr><tr>";
		$html .= "<td><a style='text-decoration:underline; font-size:12px;'>Date de naissance:</a><br><p style='width:280px; font-weight:bold; font-size:17px;'>" . $date . "</p></td>";
		$html .= "</tr><tr>";
		$html .= "<td><a style='text-decoration:underline; font-size:12px;'>Adresse:</a><br><p style='width:280px; font-weight:bold; font-size:17px;'>" . $personne->adresse . "</p></td>";
		$html .= "</tr><tr>";
		$html .= "<td><a style='text-decoration:underline; font-size:12px;'>T&eacute;l&eacute;phone:</a><br><p style='width:280px; font-weight:bold; font-size:17px;'>" . $personne->telephone . "</p></td>";
		$html .= "</tr>";
		
		$html .= "</table>";
		
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $html ) );
		
	}
	
	
	//Pour les listes de recherche **** pour les listes de recherche
	//Pour les listes de recherche **** pour les listes de recherche
	//Pour les listes de recherche **** pour les listes de recherche
	public function informationDuPatient($id) {
		
		$control = new DateHelper();
		$personne = $this->getPersonneTable()->getPersonne($id);
		$patient = $this->getPatientTable()->getPatient($id);
		$depistage = $this->getPatientTable()->getDepistagePatient($id);
		$date_naissance = null;
		if($personne->date_naissance){ $date_naissance = $control->convertDate( $personne->date_naissance ); }
		$informations_parentales = $this->getPersonneTable()->getInfosParentales($id);
		
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
			<tr style='width: 100%'>
				<td colspan='3' style='height: 25px; '> 
				
				  <a id='precedent' style='text-decoration: none; font-family: police2; width:50px; cursor: pointer;'>
	                <img src='".$this->chemin()."/images_icons/transfert_gauche.PNG' />
		            Pr&eacute;c&eacute;dent
		          </a>
	        		
				</td>
				 
			</tr>
			<tr style='width: 100%;' > 
				
			    <td style='width: 13%;' >
				  <img id='photo' src='".$this->chemin()."/img/photos_patients/".$personne->photo."' style='width:105px; height:105px; margin-bottom: 10px;'/>";
		
		//Gestion des AGE
		if($personne->age && !$personne->date_naissance){
		    $html .="<div style=' margin-left: 20px; margin-top: 145px; font-family: time new romans; '> Age: <span style='font-size:19px; font-family: time new romans; color: green; font-weight: bold;'> ".$personne->age." ans </span></div>";
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
		
		
		$html .=" </td>

				<td style='width: 75%;' >

					 <!-- TABLEAU DES INFORMATIONS -->
				     <!-- TABLEAU DES INFORMATIONS -->
					 <!-- TABLEAU DES INFORMATIONS -->
				     <table id='etat_civil'>
                        <tr>
			   	           <td style='width:27%; font-family: police1;font-size: 12px; vertical-align: top;'>
			   		          <div id='aa'><a style='text-decoration: underline;'>Pr&eacute;nom</a><br><p style='font-weight: bold;font-size: 19px;'> ".$personne->prenom." </p></div>
			   	           </td>
		
			   	           <td style='width:35%; font-family: police1;font-size: 12px; vertical-align: top;'>
			   		          <div id='aa'><a style='text-decoration: underline;'>Lieu de naissance</a><br><p style='font-weight: bold;font-size: 19px;'> ".$personne->lieu_naissance."  </p></div>
			   	           </td>
		
			               <td style='width:38%; font-family: police1;font-size: 12px; vertical-align: top;'>
			   		          <div id='aa'><a style='text-decoration: underline;'>T&eacute;l&eacute;phone</a><br><p style='font-weight: bold;font-size: 19px;'> ".$personne->telephone." </p></div>
			   	           </td>
			            </tr>
		
			            <tr>
			               <td style=' font-family: police1;font-size: 12px; vertical-align: top;'>
			   		          <div id='aa'><a style='text-decoration: underline;'>Nom</a><br><p style='font-weight: bold;font-size: 19px;'> ".$personne->nom." </p></div>
			   	           </td>";
		
	             if($depister == 0){
	                 $html .="<td style=' font-family: police1;font-size: 12px; vertical-align: top;'>
			   		            <div id='aa'><a style='text-decoration: underline;'>Nationalit&eacute; origine</a><br><p style='font-weight: bold;font-size: 19px;'> ".$personne->nationalite_origine." </p></div>
			   	              </td>";
	             }else{
	             
	                 $html .="<td style=' font-family: police1;font-size: 12px; vertical-align: top;'>
			   	     	        <div id='aa'><a style='text-decoration: underline;'>Ethnie</a><br><p style='font-weight: bold;font-size: 19px;'> ".$depistage->current()['ethnie']." </p></div>
			   	              </td>";
	              }
	              
			   	  $html .="<td style=' font-family: police1;font-size: 12px; vertical-align: top;'>
			   		          <div id='aa'><a style='text-decoration: underline;'>Email</a><br><p style='font-weight: bold;font-size: 19px;'> ".$personne->email." </p></div>
			   	           </td>
		
			            </tr>
		
			            <tr>
			               <td style=' font-family: police1;font-size: 12px; vertical-align: top;'>
			   		          <div id='aa'><a style='text-decoration: underline;'>Sexe</a><br><p style='font-weight: bold;font-size: 19px;'> ".$personne->sexe." </p></div>
			   	           </td>
		
			               <td style=' font-family: police1;font-size: 12px; vertical-align: top;'>
			   		          <div id='aa'><a style='text-decoration: underline;'>Nationalit&eacute; actuelle</a><br><p style='font-weight: bold;font-size: 19px;'> ".$personne->nationalite_actuelle." </p></div>
			   	           </td>
		
			   	           <td style=' font-family: police1;font-size: 12px; vertical-align: top;'>
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
			               <td style='font-family: police1;font-size: 12px; vertical-align: top;'>
			   		           <div id='aa'><a style='text-decoration: underline;'>Adresse</a><br><p style='font-weight: bold;font-size: 19px;'> ".$personne->adresse." </p></div>
			   	           </td>  
			   		               
			   		       <td style='padding-right: 55px; font-family: police1;font-size: 12px; vertical-align: top; '> 
			   		          <!--div id='aa'><a style='text-decoration: underline;'>Type</a><br><p style='font-weight: bold;font-size: 19px;'> ".$type." ".$typage."</p></div-->";
    	   		           	   
			   	 if($informations_parentales){
			   	     $html .="<div style='width: 30px; height: 35px; float: right; margin-top: -5px; margin-right: 10px;'><a href='javascript:infos_parentales(".$id.");' > <img id='infos_parentales_".$id."' style='float: right; cursor: pointer;' src='../images_icons/Infos_parentales.png' /> </a></div>";
			   	 }               
			   	  
			  $html .="       <div style='width: 20px; height: 35px; float: right; margin-top: -5px; '><a href='javascript:diagnostic(".$id.");' > <img id='diagnostic_".$id."' style='float: right; cursor: pointer; ' src='../images_icons/detailsd.png' /> </a></div>
    	   		           </td>
			            </tr>
			   		  
                     </table> 
 					 <!-- FIN TABLEAU DES INFORMATIONS --> 
           			 <!-- FIN TABLEAU DES INFORMATIONS -->
			   		 <!-- FIN TABLEAU DES INFORMATIONS -->
				</td>
				
				<td style='width: 12%;' >
				  <span style='color: white; '>
                    <img src='".$this->chemin()."/img/photos_patients/".$personne->photo."' style='width:105px; height:105px; opacity: 0.09;'/>
                    <div style='margin-top: 20px; margin-right: 20px; font-size:16px; font-family: Iskoola Pota; color: green; float: right; font-style: italic; opacity: 1;'> ".$patient->numero_dossier." </div>
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
			  
			  
	       </table>";
			  
		   $html .="<script> $('.infos_parentales_tampon').html('".preg_replace("/(\r\n|\n|\r)/", " ",str_replace("'", "\'", $infos_parentales))."'); </script>";
			  
		}
			  
		
		return $html;
		
	}
	
	public function demandesAnalysesVueAction() {
		
		$id = ( int ) $this->params ()->fromPost ( 'id', 0 );

		/*----------------------------------------------------*/
		$vuePatient = $this->informationDuPatient($id);
		
		/*----------------------------------------------------*/
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
		
		/*----------------------------------------------------*/
		$tabListeAnalysesParType = array();
		for($i = 1 ; $i<=5 ; $i++){ // 5 est le nombre de type d'analyse
		    $tabListeAnalysesParType[$i] = $this->getListeAnalysesParType($i);
		}
		
		/*----------------------------------------------------*/
		$aujourdhui = (new \DateTime() ) ->format('Y-m-d');
		$diagnostic_demande = $this->getAnalyseTable()->getDiagnosticAnalyse($id, $aujourdhui);
		
		if($diagnostic_demande){
		    $text = str_replace("'", "\'", $diagnostic_demande['diagnostic_demande']);
		    $vuePatient .="<script> $('#diagnostic_demande_text').val('".preg_replace("/(\r\n|\n|\r)/", " ", $text)."'); modification(); </script>";
		}
		
		/*----------------------------------------------------*/
		$verifTypageHemo = $this->getAnalyseTable()->getAnalyseTypageHemoglobineDemande($id);
		
		/*----------------------------------------------------*/
		/*----------------------------------------------------*/
		/*----------------------------------------------------*/
		
		$donnees = array($vuePatient, $existeADA, $listeAnalysesDemandees, $tabTypesAnalyses, $tabListeAnalysesParType, $verifTypageHemo);
		
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
		foreach($this->getPatientTable()->getListeDesAnalyses($id) as $listeAnalyses){
			$liste_select.= "<option value=".$listeAnalyses['idanalyse'].">".$listeAnalyses['designation']."</option>";
		}
		
		$this->getResponse()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html' );
		return $this->getResponse ()->setContent(Json::encode ( $liste_select));
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
	
	public function getTarifAnalyseAction()
	{
		$id = (int)$this->params()->fromPost ('id');

		$tarif = $this->getPatientTable()->getTarifAnalyse($id);
		$tarifString = $this->prixMill( $tarif );
		
		$html = array((int)$tarif, $tarifString);
		
		$this->getResponse()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html' );
		return $this->getResponse ()->setContent(Json::encode ( $html));
	}
	
	public function envoyerDemandesAnalysesAction()
	{
		$analyses = $this->params()->fromPost ('analyses');
		$idemploye = $this->layout()->user['idemploye'];
		$idpatient = $this->params()->fromPost ('idpatient');
		$diagnostic_demande = $this->params()->fromPost ('diagnostic_demande');
		$verifModifier = $this->params()->fromPost ('verifModifier');
		
		if($verifModifier == 0){
		    if($diagnostic_demande){ $this->getAnalyseTable()->addDignosticAnalyse($idemploye, $idpatient, $diagnostic_demande); }
		    $this->getAnalyseTable()->addDemandeAnalyse($analyses, $idemploye, $idpatient);
		}else{
		    if($diagnostic_demande){ 
		        $this->getAnalyseTable()->updateDignosticAnalyse($idemploye, $idpatient, $diagnostic_demande); 
		    }else{
		        $this->getAnalyseTable()->deleteDignosticAnalyse($idpatient);
		    }
		    $this->getAnalyseTable()->updateDemandeAnalyse($analyses, $idemploye, $idpatient);
		}
		
		$this->getResponse()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html' );
		return $this->getResponse ()->setContent(Json::encode ());
	}
	
	public function listeDemandesAujourdhuiAjaxAction() {
		$output = $this->getPatientTable ()->listeDemandesAujourdhuiAjax();
		return $this->getResponse ()->setContent ( Json::encode ( $output, array (
				'enableJsonExprFinder' => true
		) ) );
	}
	
	public function listeDemandesTousAjaxAction() {
		$output = $this->getPatientTable ()->listeDemandesTousAjax();
		return $this->getResponse ()->setContent ( Json::encode ( $output, array (
				'enableJsonExprFinder' => true
		) ) );
	}
	
	public function listeDemandesAction() {
		$this->layout ()->setTemplate ( 'layout/secretariat' );

		//$liste = $this->getAnalyseTable()->getListeAnalysesDemandeesNonFacturees(2328);
		//$liste = $this->getPatientTable ()->getFactureDelaDemande(8021);
// 		if($liste){
// 			//var_dump('cest pas vide'); exit();
// 			var_dump($liste); exit();
// 		}else{
// 			//var_dump('cest vide'); exit();
// 			var_dump($liste); exit();
// 		}
		
		return new ViewModel ( );
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
         <div style='position: absolute; top: 220px; right: 55px; font-size:17px; font-family: Iskoola Pota; color: green; float: right; font-style: itali; '> ".$patient->numero_dossier." </div>
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
			   				
			   	<td style='padding-right: 25px; font-family: police1;font-size: 12px; '>
			   	    <!--div id='aa'><a style='text-decoration: underline;'>Type</a><br><p style='font-weight: bold;font-size: 19px;'> ".$type." ".$typage." </p></div-->";
	   if($informations_parentales){
	       $html .="<div style='width: 50px; height: 35px; float: right; margin-top: -5px; '><a href='javascript:infos_parentales(".$id.");' > <img id='infos_parentales_".$id."' style='float: right; cursor: pointer;' src='../images_icons/Infos_parentales.png' /> </a></div>";
	   }
    $html .="		</td>			
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
	
	public function listeDemandesAnalyseAction($id)
	{
		
		$listeDemandesAnalyses = $this->getPatientTable ()->getDemandesAnalyses($id);
		$listeAnalysesDemandees = $this->getPatientTable ()->getAnalysesDemandees($id);
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
			  	
			  	//Informations du secretaire
			  	$infosSecretaire = $this->getPatientTable ()->getInfosSecretaire($listeDemandes['idsecretaire']);
			  	
			  	$html .="<tr style='height: 25px; width:100%;'>
			  	           <td id='numerof'>
      					       <hass><img style='padding-left: 10px; cursor: pointer;' class='info_secretaire".$listeDemandes['iddemande']."' src='../images_icons/info_infirmier.png' title='envoyÃ© par ".$infosSecretaire['prenom'].' '.$infosSecretaire['nom']."' /></hass>
			  			   </td>
      					   <td>". $date ."</td>
			  	           <td>";

			  	if($i == 1){
			  		$iddemande = $listeDemandes['iddemande'];
			  		$html .="<a class='iconeListeAffichee visualiser".$listeDemandes['iddemande']."' href='javascript:vueListeAnalyses(".$listeDemandes['iddemande'].") '>";
			  		$html .="<img style='padding-left: 3px; ' src='../images_icons/transfert_droite2.png' />"; $i = 0;
			  		$html .="</a>";
			  	}else {
			  		$html .="<a class='iconeListeAffichee visualiser".$listeDemandes['iddemande']."' href='javascript:vueListeAnalyses(".$listeDemandes['iddemande'].") '>";
			  		$html .="<img style='padding-left: 3px; cursor: pointer;' class='' src='../images_icons/transfert_droite2.png' />";
			  		$html .="</a>";
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
				
				        
				        
		      $html .='<td style="width: 4%;" > <div style="width: 3px; height: 350px; background: #cccccc; margin-left: 15px;"></div> </td>
				        
				        
				        
				       <td id="liste_analyses_demandes" style="width: 61%; height: 50px;" >';
				        
		
		      $demande = $this->getPatientTable ()->getDemandeAnalysesAvecIddemande($iddemande);
		      $controle = new DateHelper();
		      $aujourdhui = (new \DateTime() ) ->format('d/m/Y');
		      $date = $controle->convertDate( $demande['date'] );
		      if($date == $aujourdhui){ $date = "aujourd'hui  - ".$demande['time']; } else { $date = $date.' - '.$demande['time']; }
		      
		      $total = $this->getPatientTable()->getMontantTotalAnalyses($iddemande);

		      $diagnostic_demande = $this->getAnalyseTable()->getDiagnosticAnalyse($demande['idpatient'], $demande['date']);
		      
		      $html .="<div id='imprimerAnalyses' style='cursor: pointer; float:right; margin-top: -5px;'><span style='padding-right: 20px; margin-top: 20px; color: green; font-size: 17px; font-family: times new roman;'> ".$date." </span> | <span style='padding-right: 20px; padding-left: 20px; color: green; font-size: 17px; font-family: times new roman;'> Total &agrave; payer : ".$this->prixMill("$total")." FCFA</span>";
		      
		      if($diagnostic_demande){
		          $text = str_replace("'", "\'", $diagnostic_demande['diagnostic_demande']);
		          $html .="<span> <a href='javascript:diagnostic(".$id.")' > <img id='diagnostic_".$id."'  style='padding-left: 3px;' src='../images_icons/detailsd.png' /> </a> </span>";
		          $html .="<script> $('#diagnostic_demande_text').val('".preg_replace("/(\r\n|\n|\r)/", " ", $text)."'); </script>";
		      }
		      
		      $html .="<a href='javascript:imprimerAnalysesDemandees(".$iddemande.");'> <hass> <img style='padding-left: 3px; ' src='../images_icons/pdf.png' title='Imprimer' /> </hass> </a> </div>";
		      
		      $html .="<table class='table table-bordered tab_list_mini' id='listeAnalyseFiltre' >";
			
		      $html .="<thead style='width: 100%;'>
				         <tr style='height:25px; width:100%; cursor:pointer;'>
					        <th id='typeA' style='cursor: pointer;'>Type</th>
					        <th id='analyseA' style='cursor: pointer;'>Analyse</th>
				            <th id='optionA' style='font-size: 12px;' >Tarif<span style='font-size: 8px;'>(FCFA)</span> </th>
		      		        <th id='optA' style='font-size: 12px;' >Opt.</th>
				         </tr>
			           </thead>";
		
		      $html .="<tbody  class='liste_patient liste_analyses_demandes' style='width: 100%;'>";

		      foreach ($listeAnalysesDemandees as $listeAnalyses){
		      	
		      	$html .="<tr id='AnalyseLigneDemande_".$listeAnalyses['iddemande']."' style='height:25px; width:100%; font-family: times new roman;'>
					       <td id='typeA' style='font-size: 14px;'> ".$listeAnalyses['Libelle']." </td>
					       <td id='analyseA' style='font-size: 14px;'> ".$listeAnalyses['Designation']." </td>
				           <td id='optionA' style='font-size: 17px;'> <div style='float: right;'> ".$this->prixMill($listeAnalyses['Tarif'])." </div>  </td>
				           <td id='optA' style='font-size: 17px;'>";  
		      	
		      	           $facture = $this->getPatientTable ()->getFactureDelaDemande($listeAnalyses['iddemande']);
				           if(!$facture){
				           	  $html .="<a id='AnalyseDemandeePopupCofirmAnnulation_".$listeAnalyses['iddemande']."'  href='javascript:annulerAnalyseDemandee(".$iddemande.",".$listeAnalyses['iddemande'].",".$listeAnalyses['idpatient'].")' >";
				           	  $html .="<img style='margin-left: 20%;' src='../images_icons/non_applik.gif' title='Annuler'></a>";
				           }else{
				           	  $html .="<img style='margin-left: 20%;' src='../images_icons/tick_16.png' title='D&eacute;j&agrave; factur&eacute;e'>";
				           }
				           
				$html .=" </td>
				         </tr>";
		      
		      }
		               
		      $html .="</tbody>";
		      
		      
		      $html .="<tfoot class='foot_style foot_style_analyse' style='width: 100%;'>
		                 <tr style='height:45px; width:100%; cursor:pointer;'>
					       <th id='typeA_'> <input type='text' name='search_browser' value=' Type' class='search_init' /></th>
				           <th id='analyseA_'> <input type='text' name='search_browser' value=' Analyse' class='search_init' /></th>
				           <th id='optionA_'> <input type='hidden' /></th>
		      		       <th id='optA_'> <input type='hidden' /></th>
				         </tr>
		      
		               </tfoot>";
		
		      $html .="</table>
		      		
		      		
		      		
		               </td>
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

		$iddemande = $this->getPatientTable()->getDemandesAnalyses($id)->current()['iddemande'];
		
		$html = $this->informationPatientAction($id);
		
		$html2 = $this->listeDemandesAnalyseAction($id);
		
		$html3 = array($html, $html2, $iddemande);
		
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $html3 ) );
	}
	
	
	public function listeDemandesFiltreAjaxAction()
	{
		$output = $this->getPatientTable ()->listeDemandesFiltreAjax();
		return $this->getResponse ()->setContent ( Json::encode ( $output, array (
				'enableJsonExprFinder' => true
		) ) );
	}
	
	public function getListeAnalysesDemandeesAction()
	{
		$iddemande = ( int ) $this->params ()->fromPost ( 'iddemande', 0 );
		
		$listeAnalysesDemandees = $this->getPatientTable ()->getListeAnalysesDemandees($iddemande);
		$demande = $this->getPatientTable ()->getDemandeAnalysesAvecIddemande($iddemande);
		
		$controle = new DateHelper();
		$aujourdhui = (new \DateTime() ) ->format('d/m/Y');
		$date = $controle->convertDate( $demande['date'] );
		if($date == $aujourdhui){ $date = "aujourd'hui  - ".$demande['time']; } else { $date = $date.' - '.$demande['time']; }
		
		$total = $this->getPatientTable()->getMontantTotalAnalyses($iddemande);
		
		$diagnostic_demande = $this->getAnalyseTable()->getDiagnosticAnalyse($demande['idpatient'], $demande['date']);
		
		$html ="";
				
		$html .="<div id='imprimerAnalyses' style='cursor: pointer; float:right; margin-top: -5px;'><span style='padding-right: 20px; margin-top: 20px; color: green; font-size: 17px; font-family: times new roman;'> ".$date." </span> | <span style='padding-right: 20px; padding-left: 20px; color: green; font-size: 17px; font-family: times new roman;'> Total &agrave; payer : ".$this->prixMill("$total")." FCFA</span>";
		
		if($diagnostic_demande){
		    $text = str_replace("'", "\'", $diagnostic_demande['diagnostic_demande']);
		    $html .="<span> <a href='javascript:diagnostic(".$iddemande.")' > <img id='diagnostic_".$iddemande."'  style='padding-left: 3px;' src='../images_icons/detailsd.png' /> </a> </span>";
		    $html .="<script> $('#diagnostic_demande_text').val('".preg_replace("/(\r\n|\n|\r)/", " ", $text)."'); </script>";
		}
		
		$html .="<a href='javascript:imprimerAnalysesDemandees(".$iddemande.");'> <hass> <img style='padding-left: 3px; ' src='../images_icons/pdf.png' title='Imprimer' /> </hass> </a> </div>";
		
		
		$html .="<table class='table table-bordered tab_list_mini' id='listeAnalyseFiltre' >";
			
		$html .="<thead style='width: 100%;'>
				         <tr style='height:25px; width:100%; cursor:pointer;'>
					        <th id='typeA' style='cursor: pointer;'>Type</th>
					        <th id='analyseA' style='cursor: pointer;'>Analyse</th>
				            <th id='optionA' style='font-size: 12px;' >Tarif<span style='font-size: 8px;'>(FCFA)</span> </th>
		      		        <th id='optA' style='font-size: 12px;' >Opt.</th>
				         </tr>
			           </thead>";
		
		$html .="<tbody  class='liste_patient liste_analyses_demandes' style='width: 100%;'>";
		
		foreach ($listeAnalysesDemandees as $listeAnalyses){
			 
			$html .="<tr id='AnalyseLigneDemande_".$listeAnalyses['iddemande']."' style='height:25px; width:100%; font-family: times new roman;'>
					       <td id='typeA' style='font-size: 14px;'> ".$listeAnalyses['Libelle']." </td>
					       <td id='analyseA' style='font-size: 14px;'> ".$listeAnalyses['Designation']." </td>
				           <td id='optionA' style='font-size: 17px;'> <div style='float: right;'> ".$this->prixMill($listeAnalyses['Tarif'])." </div>  </td>
				           <td id='optA' style='font-size: 17px;'>";
			 
			$facture = $this->getPatientTable ()->getFactureDelaDemande($listeAnalyses['iddemande']);
			if(!$facture){
				$html .="<a id='AnalyseDemandeePopupCofirmAnnulation_".$listeAnalyses['iddemande']."'  href='javascript:annulerAnalyseDemandee(".$iddemande.",".$listeAnalyses['iddemande'].",".$listeAnalyses['idpatient'].")' >";
				$html .="<img style='margin-left: 20%;' src='../images_icons/non_applik.gif' title='Annuler'></a>";
			}else{
				$html .="<img style='margin-left: 20%;' src='../images_icons/tick_16.png' title='D&eacute;j&agrave; factur&eacute;e'>";
			}
			 
			$html .="      </td>
				     </tr>";
		
		}
		 
		$html .="</tbody>";
		
		
		$html .="<tfoot class='foot_style foot_style_analyse' style='width: 100%;'>
		                 <tr style='height:45px; width:100%; cursor:pointer;'>
					       <th id='typeA_'> <input type='text' name='search_browser' value=' Type' class='search_init' /></th>
				           <th id='analyseA_'> <input type='text' name='search_browser' value=' Analyse' class='search_init' /></th>
				           <th id='optionA_'> <input type='hidden' /></th>
		      		       <th id='optA_'> <input type='hidden' /></th>
				         </tr>
		
		               </tfoot>";
		
		$html .="</table>";
		
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $html ) );
	}
	
	public function annulerAnalyseDemandeeAction()
	{
		$iddemande = ( int ) $this->params ()->fromPost ( 'iddemande', 0 );
		
		$this->getAnalyseTable()->annulerAnalyseDemandee($iddemande);
		
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( ) );
	}
	
	public function impressionDemandesAnalysesAction()
	{
		$service = $this->layout()->user['NomService'];
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
		//*************** Création de l'imprimé pdf **************
		//******************************************************
		//******************************************************
		//Créer le document
		$DocPdf = new DocumentPdf();
		//Créer la page
		$page = new DemandeAnalysePdf();
		
		//Envoyer les données sur le patient
		$page->setPatient($patient);
		$page->setDonneesPatient($personne);
		$page->setService($service);
		$page->setAnalyses($analysesTab);
		$page->setTypesAnalyses($typesAnalysesTab);
		$page->setTarifs($tarifsTab);
		$page->setDepistage($depistage);
		
		//Ajouter une note à la page
		$page->addNote();
		//Ajouter la page au document
		$DocPdf->addPage($page->getPage());
		//Afficher le document contenant la page
		$DocPdf->getDocument();
	}
	
	public function impressionAnalysesDemandeesAction()
	{
		$service = $this->layout()->user['NomService'];
		$iddemande = $this->params()->fromPost( 'iddemande' );
		
		$idpatient = $this->getPatientTable()->getDemandeAnalysesAvecIddemande($iddemande)['idpatient'];
		$personne = $this->getPersonneTable()->getPersonne($idpatient);
		$patient = $this->getPatientTable()->getPatient($idpatient);
		$depistage = $this->getPatientTable()->getDepistagePatient($idpatient);

		$listeAnalysesDemandees = $this->getPatientTable()->getListeAnalysesDemandees($iddemande);
		
		//******************************************************
		//******************************************************
		//************** Création de l'imprimé pdf *************
		//******************************************************
		//******************************************************
		//Créer le document
		$DocPdf = new DocumentPdf();
		//Créer la page
		$page = new AnalysesDemandeesPdf();
		
		//Envoyer les données sur le patient
		$page->setPatient($patient);
		$page->setDonneesPatient($personne);
		$page->setService($service);
		$page->setDepistage($depistage);
		$page->setListeAnalysesDemandees($listeAnalysesDemandees);
		
		//Ajouter une note à la page
		$page->addNote();
		//Ajouter la page au document
		$DocPdf->addPage($page->getPage());
		//Afficher le document contenant la page
		$DocPdf->getDocument();
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	//GESTION DES RESULTATS DES ANALYSES --- GESTION DES RESULTATS DES ANALYSES
	//GESTION DES RESULTATS DES ANALYSES --- GESTION DES RESULTATS DES ANALYSES
	//GESTION DES RESULTATS DES ANALYSES --- GESTION DES RESULTATS DES ANALYSES
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
				$html .="<a class='iconeListeAffichee visualiser".$listeDemandes['iddemande']."' href='javascript:vueListeAnalysesDemande(".$listeDemandes['iddemande'].")' >";
				$html .="<img style='padding-left: 3px; ' src='../images_icons/transfert_droite2.png' /></a>"; $i = 0;
			}else {
				$html .="<a class='iconeListeAffichee visualiser".$listeDemandes['iddemande']."' href='javascript:vueListeAnalysesDemande(".$listeDemandes['iddemande'].")' >";
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
	
		$diagnostic_demande = $this->getAnalyseTable()->getDiagnosticAnalyse($demande['idpatient'], $demande['date']);
			
		$html .="<div id='imprimerAnalyses' style='cursor: pointer; float:right; margin-top: -5px;'><span style='padding-right: 20px; margin-top: 20px; color: green; font-size: 17px; font-family: times new roman;'> ".$date." </span>";
			
		if($diagnostic_demande){
			$text = str_replace("'", "\'", $diagnostic_demande['diagnostic_demande']);
			$html .="<span> <a href='javascript:diagnostic(".$idpatient.")' > <img id='diagnostic_".$idpatient."'  style='padding-left: 3px; padding-right: 7px;' src='../images_icons/detailsd.png' /> </a> </span>";
			$html .="<script> $('#diagnostic_demande_text').val('".preg_replace("/(\r\n|\n|\r)/", " ", $text)."'); </script>";
		}
	
		$html .="<!--a href='javascript:resultatsDesAnalyses(".$iddemande.");'> <img style='padding-left: 3px; ' src='../images_icons/resultat2.png' title='Resultats' /> </a--> <a href='javascript:imprimerResultatsAnalysesDemandees(".$iddemande.");'> <hass> <img style='padding-left: 3px; ' src='../images_icons/pdf.png' title='Imprimer' /> </hass> </a> </div>";
	
	
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
			$html .="<div>";
	
			$html .="<a class='resultat_existe".$listeAnalyses['iddemande']."'  style='margin-left: 10px;'>";
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
	
	
	
	public function getResultatsListeAnalysesDemandeesAction()
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
	
		$html .="<!--a href='javascript:resultatsDesAnalyses(".$iddemande.");'> <img style='padding-left: 3px; ' src='../images_icons/resultat2.png' title='Resultats' /> </a--> <a href='javascript:imprimerResultatsAnalysesDemandees(".$iddemande.");'> <hass> <img style='padding-left: 3px; ' src='../images_icons/pdf.png' title='Imprimer' /> </hass> </a> </div>";
	
	
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
			$html .="<div>";
	
			$html .="<a class='resultat_existe".$listeAnalyses['iddemande']."'  style='margin-left: 10px;'>";
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
	
	public function getInformationsResultatsAnalysesPatientAction()
	{
		$idpatient = ( int ) $this->params ()->fromPost ( 'idpatient', 0 );
	
		$iddemande = $this->getPatientTable()->getResultatsDemandesAnalysesValidees($idpatient)->current()['iddemande'];
	
		$html = $this->informationPatientAction($idpatient);
	
		$html2 = $this->listeDemandesAnalysesValidees($idpatient);
	
		$html3 = array($html, $html2,  $iddemande);
	
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $html3 ) );
	}
	
	public function listeResultatsAjaxAction() {
	
		$output = $this->getPatientTable ()->getListeResultatsAnalyses();
		return $this->getResponse ()->setContent ( Json::encode ( $output, array (
				'enableJsonExprFinder' => true
		) ) );
	
	}
	
	public function listeResultatsAction() {
	
		$this->layout ()->setTemplate ( 'layout/secretariat' );
		return new ViewModel ( );
	
	}
	
	

	//ANCIENNE FONCTION D'IMPRESSION DES RESULTATS
	//ANCIENNE FONCTION D'IMPRESSION DES RESULTATS
	//ANCIENNE FONCTION D'IMPRESSION DES RESULTATS
	
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
		$analysesTypageHemoglobine = array();
		$analysesTypageHemoProteine = array();
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
	
	
	
	public function listeNonConformiteAjaxAction() {
		$output = $this->getAnalyseTable() ->getListeBilansAnalysesNonConformePrelevement();
		return $this->getResponse ()->setContent ( Json::encode ( $output, array (
				'enableJsonExprFinder' => true
		) ) );
	}
	
	public function listeNonConformeAction() {
		$this->layout ()->setTemplate ( 'layout/secretariat' );
	
	}
	
	
	//Une fois que le patient est appelé 
	//Une fois que le patient est appelé
	//Une fois que le patient est appelé
	public function patientRappelerAction() {
		$idbilan = ( int ) $this->params ()->fromPost ( 'idbilan', 0 );
		$idemploye = $this->layout()->user['idemploye'];
	
		$this->getPatientTable() ->cocherRappelPatient($idbilan, $idemploye);
	
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $idbilan ) );
	}
	
	//Si le patient n'est pas appelé 
	public function patientNonRappelerAction() {
		$idbilan = ( int ) $this->params ()->fromPost ( 'idbilan', 0 );
		$idemploye = $this->layout()->user['idemploye'];
	
		$this->getPatientTable() ->decocherRappelPatient($idbilan, $idemploye);
	
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $idbilan ) );
	}
	
	
	public function verifierPatientExistantAction() {
		$tabInfos = $this->params ()->fromPost ( 'tabInfos', 0 );
	
		$verifPatientExiste = $this->getPersonneTable()->verifPatientExiste($tabInfos);
		
		$html = "";
		if($verifPatientExiste[0] == 1){
			$html .= "<script>
					   $('#patientInfosSupp').html('". str_replace("'", "\'", "<img style='cursor: pointer; margin-top: 10px;' src='../images_icons/voir2.png' title='".$verifPatientExiste[1][0]['patientPrenom']." ".$verifPatientExiste[1][0]['patientNom']." est nÃ© a ".$verifPatientExiste[1][0]['patientLieuNaissance']." et habite a ".$verifPatientExiste[1][0]['patientAdresse']."' />")."');
					   $('#patientModifierInfos').html('". str_replace("'", "\'", "<a target='_blank' href=' ".'modifier-patient/id_patient/'.$verifPatientExiste[1][0]['patientIdPersonne']."' style='cursor: pointer;'><img style='margin-top: 10px;' src='../images_icons/2.png' /> </a>")."');
				       
					   $('#mereNomPrenom').html('".preg_replace("/(\r\n|\n|\r)/", " ",str_replace("'", "\'", $verifPatientExiste[1][0]['merePrenom'].' '.$verifPatientExiste[1][0]['mereNom'] ))."');
				       $('#mereTelephone').html('".preg_replace("/(\r\n|\n|\r)/", " ",str_replace("'", "\'", $verifPatientExiste[1][0]['mereTelephone'] ))."');
				       $('#pereNomPrenom').html('".preg_replace("/(\r\n|\n|\r)/", " ",str_replace("'", "\'", $verifPatientExiste[1][0]['perePrenom'].' '.$verifPatientExiste[1][0]['pereNom'] ))."');
				       $('#pereTelephone').html('".preg_replace("/(\r\n|\n|\r)/", " ",str_replace("'", "\'", $verifPatientExiste[1][0]['pereTelephone'] ))."');
				       		
				       $('img').tooltip({ animation: true, html: true, placement: 'bottom', show: { effect: 'slideDown', } });		
				      </script>";
		}
		
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( array($verifPatientExiste[0], $html) ) );
	}
	
}
