<?php
namespace Personnel\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Personnel\Form\PersonnelForm;
use Personnel\Model\Personnel;
use Personnel\Form\TypePersonnelForm;
use Zend\Json\Json;
use Facturation\View\Helper\DateHelper;
use Personnel\Form\TransfertPersonnelForm;
use Personnel\Model\Transfert1;
use Zend\Form\View\Helper\FormRow;
use Zend\Form\View\Helper\FormText;
use Zend\Form\View\Helper\FormSelect;
use Zend\Form\View\Helper\FormTextarea;
use Zend\Form\View\Helper\FormHidden;
use Personnel\Form\InterventionPersonnelForm;
use Personnel\Model\Intervention1;

class PersonnelController extends AbstractActionController {
	
	protected $formPersonnel;
	protected $dateHelper;
	
	protected $patientTable;
	protected $personnelTable;
	protected $medecinTable;
	protected $medicotechniqueTable;
	protected $logistiqueTable;
	protected $affectationTable;
	protected $typePersonnelTable;
	protected $serviceTable;
	protected $transfertTable;
	protected $hopitalserviceTable;
	protected $interventionTable;
	protected $secretaireTable;
	protected $laborantinTable;
	protected $infirmierTable;

	public function getPatientTable() {
		if (! $this->patientTable) {
			$sm = $this->getServiceLocator ();
			$this->patientTable = $sm->get ( 'Facturation\Model\PatientTable' );
		}
		return $this->patientTable;
	}
	public function getPersonnelTable() {
		if (! $this->personnelTable) {
			$sm = $this->getServiceLocator ();
			$this->personnelTable = $sm->get ( 'Personnel\Model\PersonnelTable' );
		}
		return $this->personnelTable;
	}
	public function getMedecinTable() {
		if (! $this->medecinTable) {
			$sm = $this->getServiceLocator ();
			$this->medecinTable = $sm->get ( 'Personnel\Model\MedecinTable' );
		}
		return $this->medecinTable;
	}
	public function getMedicoTechniqueTable() {
		if (! $this->medicotechniqueTable) {
			$sm = $this->getServiceLocator ();
			$this->medicotechniqueTable = $sm->get ( 'Personnel\Model\MedicotechniqueTable' );
		}
		return $this->medicotechniqueTable;
	}
	public function getLogistiqueTable() {
		if (! $this->logistiqueTable) {
			$sm = $this->getServiceLocator ();
			$this->logistiqueTable = $sm->get ( 'Personnel\Model\LogistiqueTable' );
		}
		return $this->logistiqueTable;
	}
	public function getAffectationTable() {
		if (! $this->affectationTable) {
			$sm = $this->getServiceLocator ();
			$this->affectationTable = $sm->get ( 'Personnel\Model\AffectationTable' );
		}
		return $this->affectationTable;
	}
	public function getTypePersonnelTable() {
		if (! $this->typePersonnelTable) {
			$sm = $this->getServiceLocator ();
			$this->typePersonnelTable = $sm->get ( 'Personnel\Model\TypepersonnelTable' );
		}
		return $this->typePersonnelTable;
	}
	public function getServiceTable() {
		if (! $this->serviceTable) {
			$sm = $this->getServiceLocator ();
			$this->serviceTable = $sm->get ( 'Personnel\Model\ServiceTable' );
		}
		return $this->serviceTable;
	}
	public function getTransfertTable() {
		if (! $this->transfertTable) {
			$sm = $this->getServiceLocator ();
			$this->transfertTable = $sm->get ( 'Personnel\Model\TransfertTable' );
		}
		return $this->transfertTable;
	}
	public function getHopitalServiceTable() {
		if (! $this->hopitalserviceTable) {
			$sm = $this->getServiceLocator ();
			$this->hopitalserviceTable = $sm->get ( 'Personnel\Model\HopitalServiceTable' );
		}
		return $this->hopitalserviceTable;
	}
	public function getInterventionTable() {
		if (! $this->interventionTable) {
			$sm = $this->getServiceLocator ();
			$this->interventionTable = $sm->get ( 'Personnel\Model\InterventionTable' );
		}
		return $this->interventionTable;
	}
	
	public function getSecretaireTable() {
	    if (! $this->secretaireTable) {
	        $sm = $this->getServiceLocator ();
	        $this->secretaireTable = $sm->get ( 'Personnel\Model\SecretaireTable' );
	    }
	    return $this->secretaireTable;
	}

	public function getLaborantinTable() {
	    if (! $this->laborantinTable) {
	        $sm = $this->getServiceLocator ();
	        $this->laborantinTable = $sm->get ( 'Personnel\Model\LaborantinTable' );
	    }
	    return $this->laborantinTable;
	}
	
	public function getInfirmierTable() {
		if (! $this->infirmierTable) {
			$sm = $this->getServiceLocator ();
			$this->infirmierTable = $sm->get ( 'Personnel\Model\InfirmierTable' );
		}
		return $this->infirmierTable;
	}
	
	/**
	 ***************************************************************************
	 *
	 *==========================================================================
	 *
	 * *************************************************************************
	 */
	Public function getDateHelper(){
		$this->dateHelper = new DateHelper();
	}
	
	public function baseUrl(){
		$baseUrl = $_SERVER['REQUEST_URI'];
		$tabURI  = explode('public', $baseUrl);
		return $tabURI[0];
	}
	
	public function baseUrl2(){
	    $baseUrl = $_SERVER['SCRIPT_FILENAME'];
	    $tabURI  = explode('public', $baseUrl);
	    return $tabURI[0];
	}
	
	public function getFormPersonnel() {
		if (! $this->formPersonnel) {
			$this->formPersonnel = new PersonnelForm();
		}
		return $this->formPersonnel;
	}
	
	public function  indexAction(){
		$this->layout()->setTemplate('layout/personnel');
		$view = new ViewModel();
		return $view;
	}
	
	public function dossierPersonnelAction(){
		$user = $this->layout()->user;
		$id_employe = $user['idpersonne'];
		
		$this->layout()->setTemplate('layout/personnel');
		$this->getFormPersonnel();
		$formPersonnel = $this->formPersonnel;
		
		$formPersonnel->get('nationalite')->setvalueOptions($this->getPersonnelTable()->listeDeTousLesPays());
		$data = array('nationalite' => 'Sénégal');
		$formPersonnel->get('service_accueil')->setValueOptions($this->getPersonnelTable()->listeServices());
		$formPersonnel->get('type_personnel')->setValueOptions($this->getPersonnelTable()->getTypePersonnel());

		$formPersonnel->populateValues($data);
		
		$request = $this->getRequest();
 		if ($request->isPost()) {
 			$personnel =  new Personnel();
 			$formPersonnel->setInputFilter($personnel->getInputFilter());
 			$formPersonnel->setData($request->getPost());
 			if ($formPersonnel->isValid()) {
 				$personnel->exchangeArray($formPersonnel->getData());
 				
 				//var_dump($personnel); exit();
				
				//**************************************************************
			
			    //============ ENREGISTREMENT DE L'ETAT CIVIL ==================
			
			    //**************************************************************
				$today = new \DateTime ( 'now' );
				$nomPhoto = $today->format ( 'dmy_His' ). '.jpg';
				$fileBase64 = $this->params ()->fromPost ('fichier_tmp');
				$fileBase64 = substr($fileBase64, 23);
				
				if($fileBase64){
					$img = imagecreatefromstring(base64_decode($fileBase64));
				} else {
					$img = false;
				}
				
			    if ($img != false) {
			    	imagejpeg ( $img, $this->baseUrl2().'public/img/photos_personnel/' . $nomPhoto );

			    	//ON ENREGISTRE AVEC LA PHOTO
			    	$id_personnel = $this->getPersonnelTable()->savePersonnel($personnel,$id_employe,$nomPhoto);
			    	
			    } else {
			    	//ON ENREGISTRE SANS LA PHOTO
			    	$nomPhoto = 'identite.jpg';
			    	$id_personnel = $this->getPersonnelTable()->savePersonnel($personnel,$id_employe,$nomPhoto);
			    	
			    }
			    
			    //***************************************************************
			    	
			    //============ ENREGISTREMENT DES DONNEES DES COMPLEMENTS =======
			    	
			    //***************************************************************
			    
			    if($personnel->type_personnel == 1) {
			    	$this->getMedecinTable()->saveMedecin($personnel, $id_personnel, $id_employe);
			    }else
			    if($personnel->type_personnel == 2){
			    	$this->getMedicoTechniqueTable()->saveMedicoTechnique($personnel, $id_personnel, $id_employe);
			    }else
			    if($personnel->type_personnel == 3){
			    	$this->getLogistiqueTable()->saveLogistique($personnel, $id_personnel, $id_employe);
			    }else
			    if($personnel->type_personnel == 4){
			    	$this->getSecretaireTable()->saveSecretaire($personnel, $id_personnel, $id_employe);
			    }else
			    if($personnel->type_personnel == 5){
			    	$this->getLaborantinTable()->saveLaborantin($personnel, $id_personnel, $id_employe);
			    }else
			    if($personnel->type_personnel == 6){
			    	$this->getInfirmierTable()->saveInfirmier($personnel, $id_personnel, $id_employe);
			    }
			    
 			    //***************************************************************
			    	    
 			    //========== ENREGISTREMENT DES DONNEES SUR L'AFFECTATION =======
			    	    
 			    //***************************************************************
			    
 			    $this->getAffectationTable()->saveAffectation($personnel, $id_personnel, $id_employe);
			    	
 			    
 			    return $this->redirect ()->toRoute ( 'personnel', array ('action' => 'liste-personnel'));
 			} 
 		
 		}
		
		return array (
				'form' =>$formPersonnel,
		);
	}
	
	public function listePersonnelAjaxAction() {
		$output = $this->getPersonnelTable()->getListePersonnel();
		return $this->getResponse ()->setContent ( Json::encode ( $output, array (
				'enableJsonExprFinder' => true
		) ) );
	}
	
	public function listePersonnelAction(){
		$this->layout()->setTemplate('layout/personnel');
		
		$formTypePersonnel = new TypePersonnelForm();
		$formTypePersonnel->get('type_personnel')->setvalueOptions($this->getTypePersonnelTable()->listeTypePersonnel());
		
		return array(
			'form' => $formTypePersonnel,
		);
	}
	
	public function supprimerAction() {
		$id_personne = (int) $this->params() ->fromPost('id');
		$agent = $this->getPersonnelTable()->getPersonne($id_personne);

		if($agent->type_personnel == 'Logistique'){
			$donneesComplement = $this->getLogistiqueTable()->deleteLogistique($id_personne);
		}else
		if($agent->type_personnel == 'Médico-technique'){
			$donneesComplement = $this->getMedicoTechniqueTable()->deleteMedicoTechnique($id_personne);
		}else
		if($agent->type_personnel == 'Médecin'){
			$donneesComplement = $this->getMedecinTable()->deleteMedecin($id_personne);
		}
		
		$this->getAffectationTable()->deleteAffectation($id_personne);
		$this->getPersonnelTable()->deletePersonne($id_personne);
		
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode (  ) );
	}
	
	public function infoPersonnelAction() {
		
		$id_personne = (int) $this->params() ->fromPost('id');
		$this->getDateHelper();
		
		$unAgent = $this->getPersonnelTable()->getPersonne($id_personne);
		
		/****************************************************************
		 * ======= COMPLEMENTS DES INFORMATIONS SUR L'AGENT==============
		 * **************************************************************
		 * **************************************************************/
 		$donneesComplement = "";
 		if($unAgent['id_type_employe'] == 6){
 			$donneesComplement = $this->getInfirmierTable()->getInfirmier($id_personne);
 		}
 		else
 		if($unAgent['id_type_employe'] == 5){
 		    $donneesComplement = $this->getLaborantinTable()->getLaborantin($id_personne);
 		}
 		else
 		if($unAgent['id_type_employe'] == 4){
 		    $donneesComplement = $this->getSecretaireTable()->getSecretaire($id_personne);
 		}
 		else
 		if($unAgent['id_type_employe'] == 3){
 			$donneesComplement = $this->getLogistiqueTable()->getLogistique($id_personne);
 		}
 		else
		if($unAgent['id_type_employe']  == 2){
			$donneesComplement = $this->getMedicoTechniqueTable()->getMedicoTechnique($id_personne);
		}else
		if($unAgent['id_type_employe']  == 1){
			$donneesComplement = $this->getMedecinTable()->getMedecin($id_personne);
		}
		
		/****************************************************************
		 * = COMPLEMENTS DES INFORMATIONS SUR L'AFFECTATION DE L'AGENT ==
		 * **************************************************************
		 * **************************************************************/
		 $donneesAffectation = $this->getAffectationTable()->getAffectation($id_personne);
		 if($donneesAffectation){
		 	$leService = $this->getServiceTable()->getServiceAffectation($donneesAffectation->service_accueil);
		 }
		
		/****************************************************************
		 * ========= AFFICHAGE DES INFORMATION SUR LA VUE ===============
		 * **************************************************************
		 * **************************************************************/
		 
		 $date_naissance = $unAgent['date_naissance'];
		 if($date_naissance){ $date_naissance = $this->dateHelper->convertDate($date_naissance); } else { $date_naissance = ""; } 

		$html ="<div style='width: 100%;'>
		
		       <img id='photo' src='".$this->baseUrl()."public/img/photos_personnel/" . $unAgent['photo'] . "'  style='float:left; margin-left:20px; margin-right:40px; width:105px; height:105px;'/>
		
		       <p style='color: white; opacity: 0.09;'>
		         <img id='photo' src='".$this->baseUrl()."public/img/photos_personnel/" . $unAgent['photo'] . "'   style='float:right; margin-right:15px; width:95px; height:95px;'/>
		       </p>
		         		
		       <table>
                 <tr>
			   	   <td style='width:160px; font-family: police1;font-size: 12px;'>
			   		   <div id='aa'><a style='text-decoration: underline;'>Pr&eacute;nom</a><br><p style='font-weight: bold;font-size: 17px;'>".$unAgent['prenom']."</p></div>
			   	   </td> 

			   	   <td style='width:160px; font-family: police1;font-size: 12px;'>
			   		   <div id='aa'><a style='text-decoration: underline;'>Date de naissance</a><br><p style='font-weight: bold;font-size: 17px;'>".$date_naissance."</p></div>
			   	   </td>

			       <td style='width:160px; font-family: police1;font-size: 12px;'>
			   		   <div id='aa'><a style='text-decoration: underline;'>T&eacute;l&eacute;phone</a><br><p style='font-weight: bold;font-size: 17px;'>".$unAgent['telephone']."</p></div>
			   	   </td>
			   		   		
			   	   <td style='width:160px; font-family: police1;font-size: 12px;'>
			   	   </td>
			   		   		
			      </tr> 
			   		   		
			   	  <tr>
			   	   <td style='width:160px; font-family: police1;font-size: 12px;'>
			   		   <div id='aa'><a style='text-decoration: underline;'>Nom</a><br><p style='font-weight: bold;font-size: 17px;'>".$unAgent['nom']."</p></div>
			   	   </td>

			   	   <td style='width:160px; font-family: police1;font-size: 12px;'>
			   		   <div id='aa'><a style='text-decoration: underline;'>Lieu de naissance</a><br><p style='font-weight: bold;font-size: 17px;'>".$unAgent['lieu_naissance']."</p></div>
			   	   </td>

			       <td style='width:160px; font-family: police1;font-size: 12px;'>
			   		   <div id='aa'><a style='text-decoration: underline;'>Nationalit&eacute;</a><br><p style='font-weight: bold;font-size: 17px;'>".$unAgent['nationalite_actuelle']."</p></div>
			   	   </td>
			   		   		
			   	   <td style='width:160px; font-family: police1;font-size: 12px; padding-left: 15px;'>
			   		   <div id='aa'><a style='text-decoration: underline;'>@-Email</a><br><p style='font-weight: bold;font-size: 17px;'>".$unAgent['email']."</p></div>
			   	   </td>
			   		   		
			      </tr>
 		   		
			   	  <tr>
			   	   <td style='width:160px; font-family: police1;font-size: 12px; vertical-align: top;'>
			   		   <div id='aa'><a style='text-decoration: underline;'>Sexe</a><br><p style='font-weight: bold;font-size: 17px;'>".$unAgent['sexe']."</p></div>
			   	   </td>

			   	   <td style='width:160px; font-family: police1;font-size: 12px; vertical-align: top;'>
			   		   <div id='aa'><a style='text-decoration: underline;'>Profession</a><br><p style='font-weight: bold;font-size: 17px;'>".$unAgent['profession']."</p></div>
			   	   </td>

			       <td style='width:180px; font-family: police1;font-size: 12px; vertical-align: top;'>
			   		   <div id='aa'><a style='text-decoration: underline;'>Adresse</a><br><p style='font-weight: bold;font-size: 17px;'>".$unAgent['adresse']."</p></div>
			   	   </td>
			   		   		
			   	   <td style='width:160px; font-family: police1;font-size: 12px;'>
			   	   </td> 
			   		   		
		          </tr>
			   		   		
		        </table>

			    <div id='titre_info_deces'>Compl&eacute;ments informations (Personnel ".$unAgent['NOM_TYPE'].") </div>
			    <div id='barre'></div>";

		if($unAgent['id_type_employe'] == 6 && $donneesComplement){
			$html .="<table style='margin-top:10px; margin-left:17%;'>";
			$html .="<tr>";
			$html .="<td style='width:200px; vertical-align: top;'><a style='float:left; margin-right: 10px; text-decoration:underline; font-size:13px;'>Matricule:</a><div id='inform' style='float:left; font-weight:bold; font-size:16px;'>".$donneesComplement->matricule_infirmier."</div></td>";
			$html .="<td style='width:190px; vertical-align: top;'><a style='float:left; margin-right: 10px; text-decoration:underline; font-size:13px;'>Grade:</a><div id='inform' style='float:left; font-weight:bold; font-size:16px;'>".$donneesComplement->grade_infirmier."</div></td>";
			$html .="<td style='width:270px; vertical-align: top;'><a style='float:left; margin-right: 10px; text-decoration:underline; font-size:13px;'>Domaine:</a><div id='inform' style='float:left; font-weight:bold; font-size:16px;'>".$donneesComplement->domaine_infirmier."</div></td>";
			$html .="<td style='width:230px; vertical-align: top;'><a style='float:left; margin-right: 10px; text-decoration:underline; font-size:13px;'>Autres:</a><div id='inform' style='float:left; font-weight:bold; font-size:16px;'>".$donneesComplement->autres_infirmier."</div></td>";
			$html .="</tr>";
			$html .="</table>";
		}else
		if($unAgent['id_type_employe'] == 5 && $donneesComplement){
		    $html .="<table style='margin-top:10px; margin-left:17%;'>";
		    $html .="<tr>";
		    $html .="<td style='width:200px; vertical-align: top;'><a style='float:left; margin-right: 10px; text-decoration:underline; font-size:13px;'>Matricule:</a><div id='inform' style='float:left; font-weight:bold; font-size:16px;'>".$donneesComplement->matricule_laborantin."</div></td>";
		    $html .="<td style='width:190px; vertical-align: top;'><a style='float:left; margin-right: 10px; text-decoration:underline; font-size:13px;'>Grade:</a><div id='inform' style='float:left; font-weight:bold; font-size:16px;'>".$donneesComplement->grade_laborantin."</div></td>";
		    $html .="<td style='width:270px; vertical-align: top;'><a style='float:left; margin-right: 10px; text-decoration:underline; font-size:13px;'>Domaine:</a><div id='inform' style='float:left; font-weight:bold; font-size:16px;'>".$donneesComplement->domaine_laborantin."</div></td>";
		    $html .="<td style='width:230px; vertical-align: top;'><a style='float:left; margin-right: 10px; text-decoration:underline; font-size:13px;'>Autres:</a><div id='inform' style='float:left; font-weight:bold; font-size:16px;'>".$donneesComplement->autres_laborantin."</div></td>";
		    $html .="</tr>";
		    $html .="</table>";
		}else
		if($unAgent['id_type_employe'] == 4 && $donneesComplement){
		    $html .="<table style='margin-top:10px; margin-left:17%;'>";
		    $html .="<tr>";
		    $html .="<td style='width:200px; vertical-align: top;'><a style='float:left; margin-right: 10px; text-decoration:underline; font-size:13px;'>Matricule:</a><div id='inform' style='float:left; font-weight:bold; font-size:16px;'>".$donneesComplement->matricule_secretaire."</div></td>";
		    $html .="<td style='width:190px; vertical-align: top;'><a style='float:left; margin-right: 10px; text-decoration:underline; font-size:13px;'>Grade:</a><div id='inform' style='float:left; font-weight:bold; font-size:16px;'>".$donneesComplement->grade_secretaire."</div></td>";
		    $html .="<td style='width:270px; vertical-align: top;'><a style='float:left; margin-right: 10px; text-decoration:underline; font-size:13px;'>Domaine:</a><div id='inform' style='float:left; font-weight:bold; font-size:16px;'>".$donneesComplement->domaine_secretaire."</div></td>";
		    $html .="<td style='width:230px; vertical-align: top;'><a style='float:left; margin-right: 10px; text-decoration:underline; font-size:13px;'>Autres:</a><div id='inform' style='float:left; font-weight:bold; font-size:16px;'>".$donneesComplement->autres_secretaire."</div></td>";
		    $html .="</tr>";
		    $html .="</table>";
		}else
		if($unAgent['id_type_employe'] == 3 && $donneesComplement){
			$html .="<table style='margin-top:10px; margin-left:17%;'>";
			$html .="<tr>";
			$html .="<td style='width:200px; vertical-align: top;'><a style='float:left; margin-right: 10px; text-decoration:underline; font-size:13px;'>Matricule:</a><div id='inform' style='float:left; font-weight:bold; font-size:16px;'>".$donneesComplement->matricule_logistique."</div></td>";
			$html .="<td style='width:190px; vertical-align: top;'><a style='float:left; margin-right: 10px; text-decoration:underline; font-size:13px;'>Grade:</a><div id='inform' style='float:left; font-weight:bold; font-size:16px;'>".$donneesComplement->grade_logistique."</div></td>";
			$html .="<td style='width:270px; vertical-align: top;'><a style='float:left; margin-right: 10px; text-decoration:underline; font-size:13px;'>Domaine:</a><div id='inform' style='float:left; font-weight:bold; font-size:16px;'>".$donneesComplement->domaine_logistique."</div></td>";
			$html .="<td style='width:230px; vertical-align: top;'><a style='float:left; margin-right: 10px; text-decoration:underline; font-size:13px;'></a><div id='inform' style='float:left; font-weight:bold; font-size:16px;'></div></td>";
			$html .="</tr>";
			$html .="</table>";
		}else
		if($unAgent['id_type_employe'] == 2 && $donneesComplement){
			$html .="<table style='margin-top:10px; margin-left:185px;'>";
			$html .="<tr>";
			$html .="<td style='width:200px; vertical-align: top;'><a style='float:left; margin-right: 10px; text-decoration:underline; font-size:13px;'>Matricule:</a><div id='inform' style='float:left; font-weight:bold; font-size:16px;'>".$donneesComplement->matricule_medico."</div></td>";
			$html .="<td style='width:190px; vertical-align: top;'><a style='float:left; margin-right: 10px; text-decoration:underline; font-size:13px;'>Grade:</a><div id='inform' style='float:left; font-weight:bold; font-size:16px;'>".$donneesComplement->grade_medico."</div></td>";
			$html .="<td style='width:270px; vertical-align: top;'><a style='float:left; margin-right: 10px; text-decoration:underline; font-size:13px;'>Domaine:</a><div id='inform' style='float:left; font-weight:bold; font-size:16px;'>".$donneesComplement->domaine_medico."</div></td>";
			$html .="<td style='width:230px; vertical-align: top;'><a style='float:left; margin-right: 10px; text-decoration:underline; font-size:13px;'>Fonction:</a><div id='inform' style='float:left; font-weight:bold; font-size:16px;'>".$donneesComplement->autres."</div></td>";
			$html .="</tr>";
			$html .="</table>";
		}else
		if($unAgent['id_type_employe'] == 1 && $donneesComplement){
			$html .="<table style='margin-top:10px; margin-left:185px;'>";
			$html .="<tr>";
			$html .="<td style='width:200px; vertical-align: top;'><a style='float:left; margin-right: 10px; text-decoration:underline; font-size:13px;'>Matricule:</a><div id='inform' style='float:left; font-weight:bold; font-size:16px;'>".$donneesComplement->matricule."</div></td>";
			$html .="<td style='width:190px; vertical-align: top;'><a style='float:left; margin-right: 10px; text-decoration:underline; font-size:13px;'>Grade:</a><div id='inform' style='float:left; font-weight:bold; font-size:16px;'>".$donneesComplement->grade."</div></td>";
			$html .="<td style='width:270px; vertical-align: top;'><a style='float:left; margin-right: 10px; text-decoration:underline; font-size:13px;'>Specialit&eacute;:</a><div id='inform' style='float:left; font-weight:bold; font-size:16px;'>".$donneesComplement->specialite."</div></td>";
			$html .="<td style='width:230px; vertical-align: top;'><a style='float:left; margin-right: 10px; text-decoration:underline; font-size:13px;'>Fonction:</a><div id='inform' style='float:left; font-weight:bold; font-size:16px;'>".$donneesComplement->fonction."</div></td>";
			$html .="</tr>";
			$html .="</table>";
		}

		
		$html .="<div id='titre_info_deces' style='margin-top: 25px;' >Affectation</div>";
		$html .="<div id='barre'></div>";
		if($donneesAffectation){
			$dateAffectDeb = $donneesAffectation->date_debut;
			$dateAffectFin = $donneesAffectation->date_fin;
			
		$html .="<table style='margin-top:10px; margin-left:185px; margin-bottom: 30px;'>";
		$html .="<tr>";
		$html .="<td style='width:310px; vertical-align: top;'><a style='float:left; margin-right: 10px; text-decoration:underline; font-size:13px;'>Service:</a><div id='inform' style='float:left; font-weight:bold; font-size:15px;'>".$leService->libelle."</div></td>";

		if($dateAffectDeb){ 
			$dateAffectDeb = $this->dateHelper->convertDate($donneesAffectation->date_debut); 
			$html .="<td style='width:190px; vertical-align: top;'><a style='float:left; margin-right: 10px; text-decoration:underline; font-size:13px;'>Date d&eacute;but:</a><div id='inform' style='float:left; font-weight:bold; font-size:16px;'>".$dateAffectDeb."</div></td>";
		} 
		
		if($dateAffectFin){ 
			$dateAffectFin = $this->dateHelper->convertDate($donneesAffectation->date_fin); 
			$html .="<td style='width:190px; vertical-align: top;'><a style='float:left; margin-right: 10px; text-decoration:underline; font-size:13px;'>Date fin:</a><div id='inform' style='float:left; font-weight:bold; font-size:16px;'>".$dateAffectFin."</div></td>";
		} 


		if($donneesAffectation->numero_os){
			$html .="<td style='width:200px; vertical-align: top;'><a style='float:left; margin-right: 10px; text-decoration:underline; font-size:13px;'>Num&eacute;ro OS:</a><div id='inform' style='float:left; font-weight:bold; font-size:16px;'>".$donneesAffectation->numero_os."</div></td>";
		}

		$html .="</tr>";
		$html .="</table>";
		}
		
	    $html .="<div style='width: 100%; height: 100px;'>
	    		 <div style='color: white; opacity: 1; width:95px; height:40px; padding-right:15px; float:right;'>
                    <img  src='".$this->baseUrl()."public/images_icons/fleur1.jpg' />
                 </div>
                
			     <div class='block' id='thoughtbot' style='vertical-align: bottom; padding-left:60%; margin-bottom: 40px; padding-top: 35px; font-size: 18px; font-weight: bold;'><button type='submit' id='terminer'>Terminer</button></div>
                 </div>";

		$html .="</div>"; 
		        
		$html .="<script>listepatient();</script>";
		
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $html ) );
	}
	
	public function modifierDossierAction() {
		$this->layout()->setTemplate('layout/personnel');
		$this->getDateHelper();
		
		$user = $this->layout()->user;
		$idemploye = $user['idpersonne'];
		
		
		$idpersonne = (int) $this->params()->fromRoute('val', 0);
		
		if (!$idpersonne) {
			return $this->redirect()->toRoute('personnel', array(
					'action' => 'dossier-personnel'
			));
		}

		/****************************************************************
		 * ============== INITIALISATION DU FORMULAIRE ==================
		 * **************************************************************
		 * **************************************************************/
		
		$this->getFormPersonnel();
		$formPersonnel = $this->formPersonnel;
		$formPersonnel->get('nationalite')->setvalueOptions($this->getPersonnelTable()->listeDeTousLesPays());
		$formPersonnel->get('type_personnel')->setValueOptions($this->getPersonnelTable()->getTypePersonnel());
		$formPersonnel->get('service_accueil')->setValueOptions($this->getPersonnelTable()->listeServices());
		
		/****************************************************************
		 * ============= ENREGISTREMENT DES MODIFICATIONS ===============
		 * **************************************************************
		 * **************************************************************/
		$request = $this->getRequest();
		
		if ($request->isPost()) {
			$personnel =  new Personnel();
			$formPersonnel->setInputFilter($personnel->getInputFilter());
			
			$formPersonnel->setData($request->getPost());
			
			if ($formPersonnel->isValid()) {
				$personnel->exchangeArray($formPersonnel->getData());

				/*************************************************************
				 ============ ENREGISTREMENT DE L'ETAT CIVIL =================
				 *************************************************************
				 *************************************************************/
				$today = new \DateTime ( 'now' );
				$nomPhoto = $today->format ( 'dmy_His' ). '.jpg';
				$fileBase64 = $this->params ()->fromPost ('fichier_tmp');
				$fileBase64 = substr($fileBase64, 23);
				
				if($fileBase64){
					$img = imagecreatefromstring(base64_decode($fileBase64));
				} else {
					$img = false;
				}
				$anciennePhoto = $this->getPersonnelTable()->getPersonne($idpersonne)['photo'];
				
				if ($img != false) {
					if($anciennePhoto){ //SI LA PHOTO EXISTE BIEN ELLE EST SUPPRIMER DU DOSSIER POUR ETRE REMPLACER PAR LA NOUVELLE
						unlink($this->baseUrl2().'public/img/photos_personnel/'.$anciennePhoto);
					}
					imagejpeg ( $img, $this->baseUrl2().'public/img/photos_personnel/' . $nomPhoto );
				
					//ON ENREGISTRE AVEC LA NOUVELLE PHOTO
					$id_personnel = $this->getPersonnelTable()->savePersonnel($personnel, $idemploye, $nomPhoto);
				} else {
					//PAS DE NOUVELLE PHOTO
					$id_personnel = $this->getPersonnelTable()->savePersonnel($personnel, $idemploye, $anciennePhoto);
				}
				 
				/***************************************************************
				 ============ ENREGISTREMENT DES DONNEES DES COMPLEMENTS =======
				 ***************************************************************
				 ***************************************************************/
				
				if($personnel->type_personnel == 1) { 
					$this->getMedicoTechniqueTable()->deleteMedicoTechnique($idpersonne);
					$this->getLogistiqueTable()->deleteLogistique($idpersonne);
					$this->getSecretaireTable()->deleteSecretaire($idpersonne);
					$this->getLaborantinTable()->deleteLaborantin($idpersonne);
					$this->getInfirmierTable()->deleteInfirmier($idpersonne);
					$this->getMedecinTable()->saveMedecin($personnel, $idpersonne, $idemploye);
				}else
				if($personnel->type_personnel == 2){
					$this->getMedecinTable()->deleteMedecin($idpersonne);
					$this->getLogistiqueTable()->deleteLogistique($idpersonne);
					$this->getSecretaireTable()->deleteSecretaire($idpersonne);
					$this->getLaborantinTable()->deleteLaborantin($idpersonne);
					$this->getInfirmierTable()->deleteInfirmier($idpersonne);
					$this->getMedicoTechniqueTable()->saveMedicoTechnique($personnel, $idpersonne, $idemploye);
				}else
				if($personnel->type_personnel == 3){
					$this->getMedecinTable()->deleteMedecin($idpersonne);
					$this->getMedicoTechniqueTable()->deleteMedicoTechnique($idpersonne);
					$this->getSecretaireTable()->deleteSecretaire($idpersonne);
					$this->getLaborantinTable()->deleteLaborantin($idpersonne);
					$this->getInfirmierTable()->deleteInfirmier($idpersonne);
					$this->getLogistiqueTable()->saveLogistique($personnel, $idpersonne, $idemploye);
				}else
				if($personnel->type_personnel == 4){
					$this->getMedecinTable()->deleteMedecin($idpersonne);
					$this->getMedicoTechniqueTable()->deleteMedicoTechnique($idpersonne);
					$this->getLogistiqueTable()->deleteLogistique($idpersonne);
					$this->getLaborantinTable()->deleteLaborantin($idpersonne);
					$this->getInfirmierTable()->deleteInfirmier($idpersonne);
					$this->getSecretaireTable()->saveSecretaire($personnel, $idpersonne, $idemploye);
				}else
				if($personnel->type_personnel == 5){
					$this->getMedecinTable()->deleteMedecin($idpersonne);
					$this->getMedicoTechniqueTable()->deleteMedicoTechnique($idpersonne);
					$this->getSecretaireTable()->deleteSecretaire($idpersonne);
					$this->getLogistiqueTable()->deleteLogistique($idpersonne);
					$this->getInfirmierTable()->deleteInfirmier($idpersonne);
					$this->getLaborantinTable()->saveLaborantin($personnel, $idpersonne, $idemploye);
				}else
				if($personnel->type_personnel == 6){
					$this->getMedecinTable()->deleteMedecin($idpersonne);
					$this->getMedicoTechniqueTable()->deleteMedicoTechnique($idpersonne);
					$this->getSecretaireTable()->deleteSecretaire($idpersonne);
					$this->getLogistiqueTable()->deleteLogistique($idpersonne);
					$this->getLaborantinTable()->deleteLaborantin($idpersonne);
					$this->getInfirmierTable()->saveInfirmier($personnel, $idpersonne, $idemploye);
				}
				
				
				/***************************************************************
				 ============ ENREGISTREMENT DES DONNEES SUR L'AFFECTATION =====
				 ***************************************************************
				 ***************************************************************/
				 
				$this->getAffectationTable()->saveAffectation($personnel, $idpersonne, $idemploye);
				
				// Redirection a la liste du personnel
				return $this->redirect()->toRoute('personnel', array('action' =>'liste-personnel') );
			}
		}
		
		/****************************************************************
		 * ====== AFFICHAGE DES DONNEES POUR LES MODIFICATIONS ==========
		 * **************************************************************
		 * **************************************************************/
		try {
			$laPersonne = $this->getPersonnelTable()->getPersonne($idpersonne);
			$donneesPersonnel = new Personnel();

			if($laPersonne){
				$donneesPersonnel ->sexe = $laPersonne['sexe'];
				$donneesPersonnel ->prenom = $laPersonne['prenom'];
				$donneesPersonnel ->nom = $laPersonne['nom'];
				$donneesPersonnel ->date_naissance = $laPersonne['date_naissance'];
				$donneesPersonnel ->lieu_naissance = $laPersonne['lieu_naissance'];
				$donneesPersonnel ->nationalite = $laPersonne['nationalite_actuelle'];
				$donneesPersonnel ->situation_matrimoniale = $laPersonne['situation_matrimoniale'];
				$donneesPersonnel ->adresse = $laPersonne['adresse'];
				$donneesPersonnel ->telephone = $laPersonne['telephone'];
				$donneesPersonnel ->email = $laPersonne['email'];
				$donneesPersonnel ->profession = $laPersonne['profession'];
				$donneesPersonnel ->id_personne = $laPersonne['idpersonne'];
			}
			
			$type_personnel = $laPersonne['id_type_employe'];
			$donneesComplement= "";
			
			if($type_personnel == 1){
				$donneesComplement = $this->getMedecinTable()->getMedecin($idpersonne);
			}
			else
			if($type_personnel == 2){
				$donneesComplement = $this->getMedicoTechniqueTable()->getMedicoTechnique($idpersonne);
			}
			else
			if($type_personnel == 3){
				$donneesComplement = $this->getLogistiqueTable()->getLogistique($idpersonne);
			}
			else
			if($type_personnel == 4){
			    $donneesComplement = $this->getSecretaireTable()->getSecretaire($idpersonne);
			}
			else
			if($type_personnel == 5){
			    $donneesComplement = $this->getLaborantinTable()->getLaborantin($idpersonne);
			}
			else
			if($type_personnel == 6){
				$donneesComplement = $this->getInfirmierTable()->getInfirmier($idpersonne);
			}
			
			$donneesAffectation = $this->getAffectationTable()->getAffectation($idpersonne);
			
		}
		catch (\Exception $ex) {
			return $this->redirect()->toRoute('personnel', array(
					'action' => 'liste-personnel'
			));
		}
		
		$data = array();
		if($laPersonne){
			if($laPersonne['date_naissance']){ $data['date_naissance'] = $this->dateHelper->convertDate($laPersonne['date_naissance']); } else { $data['date_naissance'] = ""; }
		}
		if($donneesAffectation){
			if($donneesAffectation->date_debut){ $data['date_debut'] = $this->dateHelper->convertDate($donneesAffectation->date_debut); } else { $data['date_debut'] = ""; }
			if($donneesAffectation->date_fin){ $data['date_fin'] = $this->dateHelper->convertDate($donneesAffectation->date_fin); } else { $data['date_fin'] = ""; }
		}
		
		
 		if($donneesPersonnel){ $formPersonnel->bind($donneesPersonnel); }
 		if($donneesComplement){ $formPersonnel->bind($donneesComplement); }
 		if($donneesAffectation){ $formPersonnel->bind($donneesAffectation); }
 		
 		
 		$formPersonnel->populateValues($data);
 		
		return array (
				'photo' => $laPersonne['photo'],
				'type_personnel' => $type_personnel,
				'id_personne' => $idpersonne,
				'form' => $formPersonnel,
		);
	}
	
	public function listePersonnelTransfertAjaxAction() {
		$personnel = $this->getPersonnelTable();
		$output = $personnel->getListeRechercheTransfertPersonnel();
		return $this->getResponse ()->setContent ( Json::encode ( $output, array (
				'enableJsonExprFinder' => true
		) ) );
	}
	
	/**
	 * Pour avoir une vue sur l'agent
	 */
	public function vueAgentPersonnelAction(){
		
		$id_personne = ( int ) $this->params ()->fromPost ( 'id', 0 );
		
		$unAgent = $this->getPersonnelTable()->getPersonne($id_personne);
 		$photo = $this->getPersonnelTable()->getPhoto($id_personne);
		

 		$affectation = $this->getAffectationTable()->getServiceAgentAffecter($id_personne);
 		$service = $this->getServiceTable()->getServiceAffectation($affectation);
 		if($service){ $nomService = $service->nom;} else {$nomService = null;}
 		
		$this->getDateHelper();
		$date = $this->dateHelper->convertDate( $unAgent->date_naissance );
		
		$html  = "<div style='width:100%;'>";
			
		$html .= "<div style='width: 18%; height: 180px; float:left;'>";
		$html .= "<div id='photo' style='float:left; margin-left:40px; margin-top:10px; margin-right:30px;'> <img style='width:105px; height:105px;' src='".$this->baseUrl()."public/img/photos_personnel/" . $photo . "' ></div>";
		$html .= "</div>";
			
		$html .= "<div style='width: 65%; height: 180px; float:left;'>";
		$html .= "<table style='margin-top:10px; float:left; width: 100%;'>";
		$html .= "<tr style='width: 100%;'>";
		$html .= "<td style='width: 20%; height: 50px;'><a style='text-decoration:underline; font-size:12px;'>Nom:</a><br><p style='font-weight:bold; font-size:17px;'>" . $unAgent->nom . "</p></td>";
		$html .= "<td style='width: 30%; height: 50px;'><a style='text-decoration:underline; font-size:12px;'>Lieu de naissance:</a><br><p style=' font-weight:bold; font-size:17px;'>" . $unAgent->lieu_naissance . "</p></td>";
		$html .= "<td style='width: 20%; height: 50px;'><a style='text-decoration:underline; font-size:12px;'>Nationalit&eacute; d'origine:</a><br><p style=' font-weight:bold; font-size:17px;'>" . $unAgent->nationalite . "</p></td>";
		$html .= "<td style='width: 30%; height: 50px;'></td>";
		$html .= "</tr><tr style='width: 100%;'>";
		$html .= "<td style='width: 20%; height: 50px;'><a style='text-decoration:underline; font-size:12px;'>Pr&eacute;nom:</a><br><p style=' font-weight:bold; font-size:17px;'>" . $unAgent->prenom . "</p></td>";
		$html .= "<td style='width: 30%; height: 50px;'><a style='text-decoration:underline; font-size:12px;'>T&eacute;l&eacute;phone:</a><br><p style=' font-weight:bold; font-size:17px;'>" . $unAgent->telephone . "</p></td>";
		$html .= "<td style='width: 20%; height: 50px;'><a style='text-decoration:underline; font-size:12px;'>Sit. matrimoniale:</a><br><p style=' font-weight:bold; font-size:17px;'>" . $unAgent->situation_matrimoniale . "</p></td>";
		$html .= "<td style='width: 30%; height: 50px;'><a style='text-decoration:underline; font-size:12px;'>Email:</a><br><p style='font-weight:bold; font-size:17px;'>" . $unAgent->email . "</p></td>";
		$html .= "</tr><tr style='width: 100%;'>";
		$html .= "<td style='width: 20%; height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Date de naissance:</a><br><p style=' font-weight:bold; font-size:17px;'>" . $date . "</p></td>";
		$html .= "<td style='width: 30%; height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Adresse:</a><br><p style=' font-weight:bold; font-size:17px;'>" . $unAgent->adresse . "</p></td>";
		$html .= "<td style='width: 20%; height: 50px; vertical-align: top;'><a style='text-decoration:underline; font-size:12px;'>Profession:</a><br><p style=' font-weight:bold; font-size:17px;'>" .  $unAgent->profession . "</p></td>";
		$html .= "<td style='width: 30%; height: 50px;'></td>";
		$html .= "</tr>";
		$html .= "</table>";
		$html .="</div>";
			
		$html .= "<div style='width: 17%; height: 180px; float:left;'>";
		$html .= "<div id='' style='color: white; opacity: 0.09; float:left; margin-right:20px; margin-left:25px; margin-top:5px;'> <img style='width:105px; height:105px;' src='".$this->baseUrl()."public/img/photos_personnel/" . $photo . "'></div>";
		$html .= "</div>";
			
		$html .= "</div>";
		
		//UNIQUEMENT POUR L'INTERFACE DE MODIFICATION
		$identif = ( int ) $this->params ()->fromPost ( 'identif', 0 );
		if($identif == 1){
			$html .= $this->modificationTransfert($id_personne, $affectation);
		}
		//SCRIPT UTILISER UNIQUEMENT DANS L'INTERFACE TRANSFERT ET INTERVENTION D'UN AGENT
		$html .="<script> 
				  //TRANSFERT INTERNE
				    $('#service_origine').val('".$nomService."');
				    $('#service_origine').css({'background':'#eee','border-bottom-width':'0px','border-top-width':'0px','border-left-width':'0px','border-right-width':'0px','font-weight':'bold','color':'#065d10','font-family': 'Times  New Roman','font-size':'17px'});
					$('#service_origine').attr('readonly',true);

				    $('#service_accueil, #id_service').css({'font-weight':'bold','color':'#065d10','font-family': 'Times  New Roman','font-size':'16px'});
				    $('#motif_transfert, #motif_intervention').css({'font-weight':'bold','color':'#065d10','font-family': 'Times  New Roman','font-size':'15px'});
				    $('#note, #note_externe').css({'font-weight':'bold','color':'#065d10','font-family': 'Times  New Roman','font-size':'15px'});
				    
				  //TRANSFERT EXTERNE
				    $('#service_origine_externe').val('".$nomService."');
				    $('#service_origine_externe').css({'background':'#eee','border-bottom-width':'0px','border-top-width':'0px','border-left-width':'0px','border-right-width':'0px','font-weight':'bold','color':'#065d10','font-family': 'Times  New Roman','font-size':'17px'});
					$('#service_origine_externe').attr('readonly',true);

				    $('#hopital_accueil').css({'font-weight':'bold','color':'#065d10','font-family': 'Times  New Roman','font-size':'16px'});
				    $('#service_accueil_externe, #id_service_externe').css({'font-weight':'bold','color':'#065d10','font-family': 'Times  New Roman','font-size':'16px'});
				    $('#motif_transfert_externe, #motif_intervention_externe').css({'font-weight':'bold','color':'#065d10','font-family': 'Times  New Roman','font-size':'16px'});
				 
				    $('#date_debut, #date_fin, #date_debut_externe, #date_fin_externe').css({'font-weight':'bold','color':'#065d10','font-family': 'Times  New Roman','font-size':'16px'});
				 </script>";
		
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $html ) );
	}
	
	/**
	 * Pour la visualisation de quelques d�tails sur l'agent
	 */
	public function popupAgentPersonnelAction() {
		
			$id_personne = (int)$this->params()->fromPost('id');
			$unAgent = $this->getPersonnelTable()->getPersonne($id_personne);
			$photo = $this->getPersonnelTable()->getPhoto($id_personne);
		
			$this->getDateHelper();
			$date = $this->dateHelper->convertDate($unAgent->date_naissance);
		
			$html ="<div id='photo' style='float:left; margin-right:20px;' > <img  style='width:105px; height:105px;' src='/simens/public/img/photos_personnel/".$photo."'></div>";
		
			$html .="<table>";
		
			$html .="<tr>";
			$html .="<td><a style='text-decoration:underline; font-size:12px;'>Nom:</a><br><p style='width:280px; font-weight:bold; font-size:17px;'>".$unAgent->nom."</p></td>";
			$html .="</tr><tr>";
			$html .="<td><a style='text-decoration:underline; font-size:12px;'>Pr&eacute;nom:</a><br><p style='width:280px; font-weight:bold; font-size:17px;'>".$unAgent->prenom."</p></td>";
			$html .="</tr><tr>";
			$html .="<td><a style='text-decoration:underline; font-size:12px;'>Date de naissance:</a><br><p style='width:280px; font-weight:bold; font-size:17px;'>".$date."</p></td>";
			$html .="</tr>";
		
			$html .="<tr>";
			$html .="<td><a style='text-decoration:underline; font-size:12px;'>Adresse:</a><br><p style='width:280px; font-weight:bold; font-size:17px;'>".$unAgent->adresse."</p></td>";
			$html .="</tr><tr>";
			$html .="<td><a style='text-decoration:underline; font-size:12px;'>T&eacute;l&eacute;phone:</a><br><p style='width:280px; font-weight:bold; font-size:17px;'>".$unAgent->telephone."</p></td>";
			$html .= "</tr>";
		
			$html .="</table>";
		
			$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		    return $this->getResponse ()->setContent ( Json::encode ( $html ) );
		    
	}
	
	public function transfertAction(){
		$this->layout()->setTemplate('layout/personnel');
		
		$formTypePersonnel = new TypePersonnelForm();
		$formTypePersonnel->get('type_personnel')->setvalueOptions($this->getTypePersonnelTable()->listeTypePersonnel());
		
		$formTransfertPersonnel = new TransfertPersonnelForm();
		
		$formTransfertPersonnel->get('service_accueil')->setValueOptions($this->getPatientTable()->listeServices());
		$formTransfertPersonnel->get('hopital_accueil')->setValueOptions($this->getPatientTable()->listeHopitaux());
		
		
		/****************************************************************
		 * ======== ENREGISTREMENT DES DONNEES SUR LE TRANSFERT =========
		 * **************************************************************
		 * **************************************************************/
		$request = $this->getRequest();
		if ($request->isPost()) {
			$transfert =  new Transfert1();
			$formTransfertPersonnel->setInputFilter($transfert->getInputFilter());
			$formTransfertPersonnel->setData($request->getPost());
			$donneesPlus = array(
					'id_service_origine' => $this->getServiceTable()->getServiceParNom($this->params()->fromPost('service_origine')),
					'service_accueil_externe' => $this->params()->fromPost('service_accueil_externe'),
			);
			$formTransfertPersonnel->remove('service_accueil_externe');
			$formTransfertPersonnel->remove('hopital_accueil');

			if ($formTransfertPersonnel->isValid()) {
				$transfert->exchangeArray($formTransfertPersonnel->getData());
				
				$this->getTransfertTable()->saveTransfert($transfert, $donneesPlus);
				if($transfert->id_verif == 0){ 
					$this->getPersonnelTable()->updateEtatForTransfert($transfert->id_personne);
				}
				return $this->redirect()->toRoute('personnel', array('action' => 'liste-transfert'));
			}else {
				return $this->redirect()->toRoute('personnel', array('action' => 'transfert'));
			}
		}
		
		return array(
				'formTypePersonnel' => $formTypePersonnel,
				'formTransfertPersonnel' => $formTransfertPersonnel
		);
	}
	
	public function modificationTransfert($id_personne, $id_service_origine){
		$chemin = $this->getServiceLocator()->get('Request')->getBasePath();
		
		$formTransfertPersonnel = new TransfertPersonnelForm();
		$formTransfertPersonnel->get('service_accueil')->setValueOptions($this->getServiceTable()->getServiceHopitalID(2));
		$formTransfertPersonnel->get('hopital_accueil')->setValueOptions($this->getPatientTable()->listeHopitaux());
		
		$formRow = new FormRow();
		$formText = new FormText();
		$formTextara = new FormTextarea();
		$formSelect = new FormSelect();
		$formHidden = new FormHidden();
		
		$data = array(
				'id_personne' => $id_personne,
				'id_service_origine' => $id_service_origine
		);
		$transfert = $this->getTransfertTable()->getTransfert($data);
		
		if($transfert->type_transfert == 'Externe'){
			$id_hopital = $this->getHopitalServiceTable()->getHopitalService($transfert->service_accueil_externe)->id_hopital;
			$formTransfertPersonnel->populateValues(array('hopital_accueil' => $id_hopital));
		    $formTransfertPersonnel->get('service_accueil_externe')->setValueOptions($this->getServiceTable()->getServiceHopitalID($id_hopital));
		}
		
		$formTransfertPersonnel->bind($transfert);
		
		
		$html ="<div id='barre_separateur'></div>";
		$html .="<form  method='post' action='".$chemin."/personnel/transfert'>";
		$html .="<table id='form_patient' style='float: left; margin-left: 30px;'>  
                    <tr class='comment-form-patient'>
                        <td style='width: 270px;'>".$formRow($formTransfertPersonnel->get('type_transfert')).$formSelect($formTransfertPersonnel->get('type_transfert'))."</td> 
                    </tr>
                 </table>		
                    <div class='vider_formulaire' id='vider_champ_interne'>
                        <input title='Vider tout' name='vider' id='vider'> 
                    </div>
               
                    <div class='vider_formulaire' id='vider_champ_externe'>
                        <input title='Vider tout' name='vider' id='vider'> 
                    </div>
                        		
                    <div id='barre_separateur2'>
		            </div>
		            <!--*********************************************************************
		             =========================== TRANSFERT INTERNE =========================-->";
		$html  .=$formHidden($formTransfertPersonnel->get('id_personne'));
		$html  .=$formHidden($formTransfertPersonnel->get('id_verif'));
		$html  .="<table id='form_patient' class='transfert_interne' style='float:left; margin-top:10px; margin-left: 30px;'>
		             
		             <tr style='display: inline-block;  vertical-align: top;'>
		                 <td class='comment-form-patient'>". $formRow($formTransfertPersonnel->get('service_origine')).$formText($formTransfertPersonnel->get('service_origine'))."</td>
		                 <td class='comment-form-patient'>". $formRow($formTransfertPersonnel->get('service_accueil')).$formSelect($formTransfertPersonnel->get('service_accueil'))."</td>
		                 <td class='comment-form-patient'>". $formRow($formTransfertPersonnel->get('motif_transfert')).$formTextara($formTransfertPersonnel->get('motif_transfert'))."</td>
		                 <td class='comment-form-patient'>". $formRow($formTransfertPersonnel->get('note')).$formTextara($formTransfertPersonnel->get('note'))."</td>
		             </tr>
		             
	              </table>	
		            <!--*********************************************************************
		             =========================== TRANSFERT INTERNE =========================-->";
		
		$html  .="<table id='form_patient' class='transfert_externe' style='float:left; margin-top:10px; margin-left: 30px;'>
		             
		             <tr style='display: inline-block;  vertical-align: top;'>
		                 <td class='comment-form-patient'>". $formRow($formTransfertPersonnel->get('service_origine_externe')).$formText($formTransfertPersonnel->get('service_origine_externe'))."</td>
		                 <td class='comment-form-patient'>". $formRow($formTransfertPersonnel->get('hopital_accueil')).$formSelect($formTransfertPersonnel->get('hopital_accueil'))."</td>
		                 <td class='comment-form-patient'>". $formRow($formTransfertPersonnel->get('service_accueil_externe')).$formSelect($formTransfertPersonnel->get('service_accueil_externe'))."</td>
		                 <td class='comment-form-patient'>". $formRow($formTransfertPersonnel->get('motif_transfert_externe')).$formTextara($formTransfertPersonnel->get('motif_transfert_externe'))."</td>
		             </tr>
		             
	              </table>";
		
		$html  .="<div id='terminer_annuler' style='padding-top: 5px;'>
                    <div class='block' id='thoughtbot'>
                       <button type='submit' id='terminer_modif' style='height:35px;'>Terminer</button>
                    </div>
                    
                    <div class='block' id='thoughtbot'>
                       <button id='annuler_modif' style='height:35px;'>Annuler</button>
                    </div>
                </div>";
		
		$html  .="</form>";
		
		$html  .="<script type='text/javascript'>

				  $('#id_verif').val(1);
				
                  /*****TRANSFERT INTERNE******/
                  $('.transfert_externe').toggle(false);
                  $('#vider_champ_externe').toggle(false);
				  
				  getChamps('".$transfert->type_transfert."');
				  		
                  $('#vider_champ_interne').click(function(){
	                $('#service_accueil').val('');
	                $('#motif_transfert').val('');
	                $('#note').val('');
                  });
				
				  $('#vider_champ_externe').click(function(){
    			    $('#hopital_accueil').val('');
    			    $('#service_accueil_externe').val('');
    			    $('#motif_transfert_externe').val('');
    		      });
				
				  if('".$transfert->type_transfert."' == 'Interne'){
				     $('#hopital_accueil').val('');
    			     $('#service_accueil_externe').val('');
    			     $('#motif_transfert_externe').val('');
	              }else {
				  		$('#service_accueil').val('');
	                    $('#motif_transfert').val('');
	                    $('#note').val('');
				  }
				  scriptModification();
				
				 </script>";
		
		return $html;		
	}
	
	public function listeTransfertAjaxAction() {
		$personnel = $this->getPersonnelTable();
		$output = $personnel->getListeTransfertPersonnel();
		return $this->getResponse ()->setContent ( Json::encode ( $output, array (
				'enableJsonExprFinder' => true
		) ) );
	}
	
	public function listeTransfertAction(){
		$this->layout()->setTemplate('layout/personnel');
	
		$formTypePersonnel = new TypePersonnelForm();
		$formTypePersonnel->get('type_personnel')->setvalueOptions($this->getTypePersonnelTable()->listeTypePersonnel());
	
		return array(
				'form' => $formTypePersonnel,
		);
	}
	
	public function supprimerTransfertAction() {
		
		$id_personne = (int) $this->params() ->fromPost('id');
	
		if($id_personne){
			$this->getPersonnelTable()->updateEtatForDeleteTransfert($id_personne);
			$this->getTransfertTable()->deleteTransfert($id_personne);
		}
		
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode (  ) );
	}
	
	/************************************************************************************************************************
	 * =========================================== PARTIE INTERVENTION ======================================================
	 * **********************************************************************************************************************
	 * **********************************************************************************************************************
	 */
	public function listePersonnelInterventionAjaxAction() {
		$personnel = $this->getPersonnelTable();
		$output = $personnel->getListeRechercheInterventionPersonnel();
		return $this->getResponse ()->setContent ( Json::encode ( $output, array (
				'enableJsonExprFinder' => true
		) ) );
	}
	
	public function interventionAction(){
		$this->layout()->setTemplate('layout/personnel');
		
		$formTypePersonnel = new TypePersonnelForm();
		$formTypePersonnel->get('type_personnel')->setvalueOptions($this->getTypePersonnelTable()->listeTypePersonnel());
		
		$formInterventionPersonnel = new InterventionPersonnelForm();
		
		$formInterventionPersonnel->get('id_service')->setValueOptions($this->getPatientTable()->listeServices());
		$formInterventionPersonnel->get('hopital_accueil')->setValueOptions($this->getPatientTable()->listeHopitaux());
		
		
		/****************************************************************
		 * ======== ENREGISTREMENT DES DONNEES SUR L'INTERVENTION =========
		 * **************************************************************
		 * **************************************************************/
		$request = $this->getRequest();
		if ($request->isPost()) {
			$intervention =  new Intervention1();
			$formInterventionPersonnel->setInputFilter($intervention->getInputFilter());
			$formInterventionPersonnel->setData($request->getPost());
 			
 			//A COMPRENDRE LE POURQUOI DE CA
			$formInterventionPersonnel->remove('id_service_externe');
			$formInterventionPersonnel->remove('hopital_accueil');

			if ($formInterventionPersonnel->isValid()) {
				$intervention->exchangeArray($formInterventionPersonnel->getData());
				
				$this->getInterventionTable()->saveIntervention($intervention);
				
				return $this->redirect()->toRoute('personnel', array('action' => 'liste-intervention'));
			}else {
				
				return $this->redirect()->toRoute('personnel', array('action' => 'intervention'));
			}
		}
		
		return array(
				'formTypePersonnel' => $formTypePersonnel,
				'formInterventionPersonnel' => $formInterventionPersonnel
		);
	}
	
	public function listeInterventionAjaxAction() {
		$personnel = $this->getPersonnelTable();
		$output = $personnel->getListeInterventionPersonnel();
		return $this->getResponse ()->setContent ( Json::encode ( $output, array (
				'enableJsonExprFinder' => true
		) ) );
	}
	
	public function listeInterventionAction(){
		$this->layout()->setTemplate('layout/personnel');
		
		$formTypePersonnel = new TypePersonnelForm();
		$formTypePersonnel->get('type_personnel')->setvalueOptions($this->getTypePersonnelTable()->listeTypePersonnel());
		
		return array(
				'form' => $formTypePersonnel,
		);
	}
	
	public function infoPersonnelInterventionAction(){
		
		$id_personne = (int) $this->params() ->fromPost('id');
		$this->getDateHelper();
		
		$unAgent = $this->getPersonnelTable()->getPersonne($id_personne);
		$photoAgent = $this->getPersonnelTable()->getPhoto($id_personne);
		
		/****************************************************************
		 * ======= COMPLEMENTS DES INFORMATIONS SUR L'AGENT==============
		* **************************************************************
		* **************************************************************/
		$donneesComplement = "";
		if($unAgent->type_personnel == 'Logistique'){
			$donneesComplement = $this->getLogistiqueTable()->getLogistique($id_personne);
		}else
		if($unAgent->type_personnel  == 'Médico-technique'){
			$donneesComplement = $this->getMedicoTechniqueTable()->getMedicoTechnique($id_personne);
		}else
		if($unAgent->type_personnel  == 'Médecin'){
			$donneesComplement = $this->getMedecinTable()->getMedecin($id_personne);
		}
		
		/****************************************************************
		 * = COMPLEMENTS DES INFORMATIONS SUR L'AFFECTATION DE L'AGENT ==
		* **************************************************************
		* **************************************************************/
		$donneesAffectation = $this->getAffectationTable()->getAffectation($id_personne);
		if($donneesAffectation){
			$leService = $this->getServiceTable()->getServiceAffectation($donneesAffectation->service_accueil);
		}
		
		/****************************************************************
		 * ========= AFFICHAGE DES INFORMATION SUR LA VUE ===============
		* **************************************************************
		* **************************************************************/
		$html ="<div style='width: 100%;'>
		
		       <img id='photo' src='".$this->baseUrl()."public/img/photos_personnel/" . $photoAgent . "'  style='float:left; margin-left:20px; margin-right:40px; width:105px; height:105px;'/>
		
		       <p style='color: white; opacity: 0.09;'>
		         <img id='photo' src='".$this->baseUrl()."public/img/photos_personnel/" . $photoAgent . "'   style='float:right; margin-right:15px; width:95px; height:95px;'/>
		       </p>
		     
		       <table>
                 <tr>
			   	   <td style='width:160px; font-family: police1;font-size: 12px;'>
			   		   <div id='aa'><a style='text-decoration: underline;'>Pr&eacute;nom</a><br><p style='font-weight: bold;font-size: 17px;'>".$unAgent->prenom."</p></div>
			   	   </td>
		
			   	   <td style='width:160px; font-family: police1;font-size: 12px;'>
			   		   <div id='aa'><a style='text-decoration: underline;'>Date de naissance</a><br><p style='font-weight: bold;font-size: 17px;'>".$this->dateHelper->convertDate($unAgent->date_naissance)."</p></div>
			   	   </td>
		
			       <td style='width:160px; font-family: police1;font-size: 12px;'>
			   		   <div id='aa'><a style='text-decoration: underline;'>T&eacute;l&eacute;phone</a><br><p style='font-weight: bold;font-size: 17px;'>".$unAgent->telephone."</p></div>
			   	   </td>
			   	
			   	   <td style='width:160px; font-family: police1;font-size: 12px;'>
			   	   </td>
			   	
			      </tr>
			   	
			   	  <tr>
			   	   <td style='width:160px; font-family: police1;font-size: 12px;'>
			   		   <div id='aa'><a style='text-decoration: underline;'>Nom</a><br><p style='font-weight: bold;font-size: 17px;'>".$unAgent->nom."</p></div>
			   	   </td>
		
			   	   <td style='width:160px; font-family: police1;font-size: 12px;'>
			   		   <div id='aa'><a style='text-decoration: underline;'>Lieu de naissance</a><br><p style='font-weight: bold;font-size: 17px;'>".$unAgent->lieu_naissance."</p></div>
			   	   </td>
		
			       <td style='width:160px; font-family: police1;font-size: 12px;'>
			   		   <div id='aa'><a style='text-decoration: underline;'>Nationalit&eacute;</a><br><p style='font-weight: bold;font-size: 17px;'>".$unAgent->nationalite."</p></div>
			   	   </td>
			   	
			   	   <td style='width:160px; font-family: police1;font-size: 12px; padding-left: 15px;'>
			   		   <div id='aa'><a style='text-decoration: underline;'>@-Email</a><br><p style='font-weight: bold;font-size: 17px;'>".$unAgent->email."</p></div>
			   	   </td>
			   	
			      </tr>
			   	
			   	  <tr>
			   	   <td style='width:160px; font-family: police1;font-size: 12px; vertical-align: top;'>
			   		   <div id='aa'><a style='text-decoration: underline;'>Sexe</a><br><p style='font-weight: bold;font-size: 17px;'>".$unAgent->sexe."</p></div>
			   	   </td>
		
			   	   <td style='width:160px; font-family: police1;font-size: 12px; vertical-align: top;'>
			   		   <div id='aa'><a style='text-decoration: underline;'>Profession</a><br><p style='font-weight: bold;font-size: 17px;'>".$unAgent->profession."</p></div>
			   	   </td>
		
			       <td style='width:180px; font-family: police1;font-size: 12px; vertical-align: top;'>
			   		   <div id='aa'><a style='text-decoration: underline;'>Adresse</a><br><p style='font-weight: bold;font-size: 17px;'>".$unAgent->adresse."</p></div>
			   	   </td>
			   	
			   	   <td style='width:160px; font-family: police1;font-size: 12px;'>
			   	   </td>
			   	
			      </tr>
			   	
		        </table>
		
			    <div id='titre_info_deces'>Compl&eacute;ments informations (Personnel ".$unAgent->type_personnel.") </div>
			    <div id='barre'></div>";
			
		if($unAgent->type_personnel == 'Logistique' && $donneesComplement){
			$html .="<table style='margin-top:10px; margin-left:185px;'>";
			$html .="<tr>";
			$html .="<td style='width:200px; vertical-align: top;'><a style='float:left; margin-right: 10px; text-decoration:underline; font-size:13px;'>Matricule:</a><div id='inform' style='float:left; font-weight:bold; font-size:16px;'>".$donneesComplement->matricule_logistique."</div></td>";
			$html .="<td style='width:190px; vertical-align: top;'><a style='float:left; margin-right: 10px; text-decoration:underline; font-size:13px;'>Grade:</a><div id='inform' style='float:left; font-weight:bold; font-size:16px;'>".$donneesComplement->grade_logistique."</div></td>";
			$html .="<td style='width:270px; vertical-align: top;'><a style='float:left; margin-right: 10px; text-decoration:underline; font-size:13px;'>Domaine:</a><div id='inform' style='float:left; font-weight:bold; font-size:16px;'>".$donneesComplement->domaine_logistique."</div></td>";
			$html .="<td style='width:230px; vertical-align: top;'><a style='float:left; margin-right: 10px; text-decoration:underline; font-size:13px;'></a><div id='inform' style='float:left; font-weight:bold; font-size:16px;'></div></td>";
			$html .="</tr>";
			$html .="</table>";
		}else
		if($unAgent->type_personnel == 'Médico-technique' && $donneesComplement){
			$html .="<table style='margin-top:10px; margin-left:185px;'>";
			$html .="<tr>";
			$html .="<td style='width:200px; vertical-align: top;'><a style='float:left; margin-right: 10px; text-decoration:underline; font-size:13px;'>Matricule:</a><div id='inform' style='float:left; font-weight:bold; font-size:16px;'>".$donneesComplement->matricule_medico."</div></td>";
			$html .="<td style='width:190px; vertical-align: top;'><a style='float:left; margin-right: 10px; text-decoration:underline; font-size:13px;'>Grade:</a><div id='inform' style='float:left; font-weight:bold; font-size:16px;'>".$donneesComplement->grade_medico."</div></td>";
			$html .="<td style='width:270px; vertical-align: top;'><a style='float:left; margin-right: 10px; text-decoration:underline; font-size:13px;'>Domaine:</a><div id='inform' style='float:left; font-weight:bold; font-size:16px;'>".$donneesComplement->domaine_medico."</div></td>";
			$html .="<td style='width:230px; vertical-align: top;'><a style='float:left; margin-right: 10px; text-decoration:underline; font-size:13px;'>Fonction:</a><div id='inform' style='float:left; font-weight:bold; font-size:16px;'>".$donneesComplement->autres."</div></td>";
			$html .="</tr>";
			$html .="</table>";
		}else
		if($unAgent->type_personnel == 'Médecin' && $donneesComplement){
			$html .="<table style='margin-top:10px; margin-left:185px;'>";
			$html .="<tr>";
			$html .="<td style='width:200px; vertical-align: top;'><a style='float:left; margin-right: 10px; text-decoration:underline; font-size:13px;'>Matricule:</a><div id='inform' style='float:left; font-weight:bold; font-size:16px;'>".$donneesComplement->matricule."</div></td>";
			$html .="<td style='width:190px; vertical-align: top;'><a style='float:left; margin-right: 10px; text-decoration:underline; font-size:13px;'>Grade:</a><div id='inform' style='float:left; font-weight:bold; font-size:16px;'>".$donneesComplement->grade."</div></td>";
			$html .="<td style='width:270px; vertical-align: top;'><a style='float:left; margin-right: 10px; text-decoration:underline; font-size:13px;'>Specialit&eacute;:</a><div id='inform' style='float:left; font-weight:bold; font-size:16px;'>".$donneesComplement->specialite."</div></td>";
			$html .="<td style='width:230px; vertical-align: top;'><a style='float:left; margin-right: 10px; text-decoration:underline; font-size:13px;'>Fonction:</a><div id='inform' style='float:left; font-weight:bold; font-size:16px;'>".$donneesComplement->fonction."</div></td>";
			$html .="</tr>";
			$html .="</table>";
		}
		
		
		$html  .="<div id='titre_info_deces'>Liste des interventions </div>
		          <div id='barre'></div>";
		
		/***********************************************************************************/
		/*========================= LA LISTE DES INTERVENTIONS ============================*/
		/***********************************************************************************/
		$html .="<div id='listeinterventionbis'>";
		$html .="<table class='table table-bordered tab_list_mini'  style='margin-top:10px; margin-bottom:20px; margin-left:185px; width:80%;' id='listeintervention'>";
		
		$html .="<thead style='width: 100%;'>
				  <tr style='height:40px; width:100%; cursor:pointer;'>
					<th style='width: 40%;'>Services</th>
					<th style='width: 15%;'>Date debut</th>
					<th style='width: 15%;'>Date fin</th>
					<th style='width: 18%;'>Intervention</th>
				    <th style='width: 12%;'>Options</th>
				  </tr>
			     </thead>";
		
		$html .="<tbody style='width: 100%;'>";
		
		$listeIntervention = $this->getPersonnelTable()->getListeInterventions($id_personne);
		
		foreach ($listeIntervention as $Liste){
		
		$html .="<tr style='width: 100%;' id='".$Liste['numero_intervention']."'>";
		$html .="<td style='width: 40%;'><div id='inform' style='float:left; font-weight:bold; font-size:17px;'>".$this->getServiceTable()->getServiceAffectation($Liste['id_service'])->nom."</div></td>";
		$html .="<td style='width: 15%;'><div id='inform' style='float:left; font-weight:bold; font-size:17px;'>".$this->dateHelper->convertDate($Liste['date_debut'])."</div></td>";
		$html .="<td style='width: 15%;'><div id='inform' style='float:left; font-weight:bold; font-size:17px;'>".$this->dateHelper->convertDate($Liste['date_fin'])."</div></td>";
		$html .="<td style='width: 18%;'><div id='inform' style='float:left; font-weight:bold; font-size:17px;'>".$Liste['type_intervention']."</div></td>";
		$html .="<td style='width: 12%;'> <a href='javascript:vueintervention(".$Liste['numero_intervention'].") '>
					       <img style='display: inline;' src='/simens/public/images_icons/vue.PNG' alt='Constantes' title='details' />
					  </a>&nbsp;
		
				      <a href='javascript:modifierintervention(".$Liste['numero_intervention'].",".$id_personne.")'>
					    	<img style='display: inline;' src='/simens/public/images_icons/suivant.PNG' alt='Constantes' title='modifier' />
					  </a>&nbsp;
		
				      <a href='javascript:supprimeruneintervention(".$Liste['numero_intervention'].",".$id_personne.")' >
					    	<img  style='display: inline;' src='/simens/public/images_icons/sup.PNG' alt='Constantes' title='annuler' />
					  </a>
				     </td>";
		$html .="</tr>";
		
		}
		$html .="</tbody>";
		
		$html .="</table>";
		$html .="</div>";
		/***********************************************************************************/
		/*========================= FIN FIN LA LISTE DES INTERVENTIONS ====================*/
		/***********************************************************************************/
		
		$html .="<div style='width: 100%; height: 100px;'>
				
				 <div style='margin-left: 185px;'> 
				   <a href='javascript:rechercherintervention(".$id_personne.")'  style='float:left; font-weight:bold; font-size:15px; text-decoration:none; font-style: italic; font-family: Times  New Roman;'><img  src='/simens/public/images_icons/search_16.png' title='Recherche avanc&eacute;e' /> Rechercher</a>
				   <a href='' id='ajouternouvelleintervention' style='float:left; font-weight:bold; font-size:15px; text-decoration:none; font-style: italic; font-family: Times  New Roman;'><span style='margin-right:7px; margin-left:10px; color:green; font-size: 18px;'>|</span><img  src='/simens/public/images_icons/aj.gif' title='Ajouter intervention' /> Ajouter </a>
				 </div>
				
	    		 <div style='color: white; opacity: 1; width:95px; height:40px; padding-right:15px; float:right;'>
                    <img  src='".$this->baseUrl()."public/images_icons/fleur1.jpg' />
                 </div>
		
			     <div class='block' id='thoughtbot' style='vertical-align: bottom; padding-left:60%; margin-bottom: 40px; padding-top: 35px; font-size: 18px; font-weight: bold;'><button type='submit' id='terminer'>Terminer</button></div>
                 </div>";
		
		$html .="</div>";
		
		$html .="<style>  
				  #listeDataTable{
	                margin-left: 175px;
                  }
				  
				  div .dataTables_paginate
                  {
				    margin-right: 30px;
                  }
				 </style>";
		
		$html .="<script>listepatient();
				</script>";
		
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $html ) );
	}
	
	public function vueInterventionAgentAction(){

		$this->getDateHelper();
		
		$numero_intervention = (int) $this->params() ->fromPost('idIntervention');
		
		$intervention = $this->getInterventionTable()->getIntervention($numero_intervention);
		
		if($intervention->type_intervention == 'Interne') {
			$id_service = $intervention->id_service;
			$date_debut = $this->dateHelper->convertDate($intervention->date_debut);
			$date_fin = $this->dateHelper->convertDate($intervention->date_fin);
			$motif_intervention = $intervention->motif_intervention;
			$note = $intervention->note;
		}else {
			$id_service = $intervention->id_service_externe;
			$date_debut = $this->dateHelper->convertDate($intervention->date_debut_externe);
			$date_fin = $this->dateHelper->convertDate($intervention->date_fin_externe);
			$motif_intervention = $intervention->motif_intervention_externe;
			$note = $intervention->note_externe;
		}
	
		$html  ="<table>";
		$html .="<tr>";
		$html .="<td style='width:240px;'><a style='text-decoration:underline; font-size:12px;'>Service d'accueil:</a><br><p style='font-weight:bold; font-size:17px;'>".$this->getServiceTable()->getServiceAffectation($id_service)->nom."</p></td>";
		$html .="<td style='width:100px;'><a style='text-decoration:underline; font-size:12px;'>Date d&eacute;but:</a><br><p style='font-weight:bold; font-size:17px;'>".$date_debut."</p></td>";
		$html .="<td style='width:100px;'><a style='text-decoration:underline; font-size:12px;'>Date fin:</a><br><p style='font-weight:bold; font-size:17px;'>".$date_fin."</p></td>";
		$html .="<td style='width:100px;'><a style='text-decoration:underline; font-size:12px;'>Type:</a><br><p style='font-weight:bold; font-size:17px;'>".$intervention->type_intervention."</p></td>";
		$html .="</tr>";
		$html .="</table>";
	
		$html .="<table>";
		$html .="<tr>";
		$html .="<td style='width:210px; padding-top: 10px; padding-right:25px;'><a style='text-decoration:underline; font-size:13px;'>Motif:</a><br><p id='circonstance_deces' style='background:#f8faf8; font-weight:bold; font-size:17px;'>".$motif_intervention."</p></td>";
		$html .="<td style='width:210px; padding-top: 10px;'><a style='text-decoration:underline; font-size:13px;'>Note:</a><br><p id='circonstance_deces' style='background:#f8faf8; font-weight:bold; font-size:17px;'>".$note."</p></td>";
		$html .= "</tr>";
		$html .="</table>";
	
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $html ) );
	}
	
	public function modifierInterventionAgentAction(){
		$this->getDateHelper();
		$idIntervention = (int) $this->params() ->fromPost('idIntervention');
		$id_personne = (int) $this->params() ->fromPost('idAgent');
		
		$affectation = $this->getAffectationTable()->getServiceAgentAffecter($id_personne);
		$service = $this->getServiceTable()->getServiceAffectation($affectation);
		if($service){ $nomService = $service->nom;} else {$nomService = null;}
		
		$intervention = $this->getInterventionTable()->getIntervention($idIntervention);
		
		$form = new InterventionPersonnelForm();
		
		$form->get('id_service')->setValueOptions($this->getServiceTable()->fetchService());
		$form->get('hopital_accueil')->setValueOptions($this->getPatientTable()->listeHopitaux());
		
		if($intervention){ 
			$form->bind($intervention);
			
			if($intervention->type_intervention == "Interne"){
				$date = array(
						'date_debut' => $this->dateHelper->convertDate($intervention->date_debut),
						'date_fin' => $this->dateHelper->convertDate($intervention->date_fin),
				);
			} else {
				$date = array(
						'date_debut_externe' => $this->dateHelper->convertDate($intervention->date_debut_externe),
						'date_fin_externe' => $this->dateHelper->convertDate($intervention->date_fin_externe),
				);
				
				$id_hopital = $this->getHopitalServiceTable()->getHopitalService($intervention->id_service_externe)->id_hopital;
				$form->populateValues(array('hopital_accueil' => $id_hopital));
				$form->get('id_service_externe')->setValueOptions($this->getServiceTable()->getServiceHopitalID($id_hopital));
			}
		    
		}
		$form->populateValues($date);
		$formRow = new FormRow();
		$formText = new FormText();
		$formSelect = new FormSelect();
		$formTextArea = new FormTextarea();
		
		$html ="<table id='form_patient' style='float: left; margin-left: 30px; margin-bottom:10px;'>
                    <tr class='comment-form-patient'>
                        <td style='width: 270px;'>".$formRow($form->get('type_intervention')).$formSelect($form->get('type_intervention'))."</td>
                    </tr>
                 </table>";
		
		$html .="<div style='float:right; margin-top: 40px;' class='vider_formulaire' id='vider_champ_interne'>
                     <input title='Vider tout' name='vider' id='vider'>
                 </div>
		
                 <div style='float:right; margin-top: 40px;' class='vider_formulaire' id='vider_champ_externe'>
                     <input title='Vider tout' name='vider' id='vider'>
                 </div>";
		
		$html .="<div id='barre_separateur_modif'></div>";
		
		$html .="<table id='form_patient' class='transfert_interne' style='float:left; margin-top:10px; margin-left: 30px; width: 97%;'>
		      
		             <tr style='display: inline-block;  vertical-align: top; width: 97%;'>
		                 <td class='comment-form-patient' style='width: 25%;' >".$formRow($form->get('service_origine')).$formText($form->get('service_origine'))."</td>
		                 <td class='comment-form-patient' style='width: 25%;'>".$formRow($form->get('id_service')).$formSelect($form->get('id_service'))."</td>
		                 <td class='comment-form-patient' style='width: 25%;'>".$formRow($form->get('date_debut')).$formText($form->get('date_debut'))."</td>
		                 <td class='comment-form-patient' style='width: 25%;'>".$formRow($form->get('date_fin')).$formText($form->get('date_fin'))."</td>
		             </tr>
		             <tr style='display: inline-block;  vertical-align: top;'>
		                 <td class='comment-form-patient' style='width: 25%;'>".$formRow($form->get('motif_intervention')).$formTextArea($form->get('motif_intervention'))."</td>
		                 <td class='comment-form-patient' style='width: 25%;'>".$formRow($form->get('note')).$formTextArea($form->get('note'))."</td>
		                 <td class='comment-form-patient' style='width: 25%;'> </td>
		                 <td class='comment-form-patient' style='width: 25%;'> </td>
		             </tr>
		      
	           </table>";
		
		$html .="<table id='form_patient' class='transfert_externe' style=' float:left; margin-top:10px; margin-left: 30px; width: 97%;'>
		      
		             <tr style='display: inline-block;  vertical-align: top; width: 97%;'>
		                 <td class='comment-form-patient' style='width: 25%;'>".$formRow($form->get('service_origine_externe')).$formText($form->get('service_origine_externe'))."</td>
		                 <td class='comment-form-patient' style='width: 25%;'>".$formRow($form->get('hopital_accueil')).$formSelect($form->get('hopital_accueil'))."</td>
		                 <td class='comment-form-patient' style='width: 25%;'>".$formRow($form->get('id_service_externe')).$formSelect($form->get('id_service_externe'))."</td>
		                 <td class='comment-form-patient' style='width: 25%;'>".$formRow($form->get('date_debut_externe')).$formText($form->get('date_debut_externe'))."</td>
		             </tr>
		             <tr style='display: inline-block;  vertical-align: top;'>
		                 <td class='comment-form-patient' style='width: 25%;'>".$formRow($form->get('date_fin_externe')).$formText($form->get('date_fin_externe'))."</td>
		                 <td class='comment-form-patient' style='width: 25%;'>".$formRow($form->get('motif_intervention_externe')).$formTextArea($form->get('motif_intervention_externe'))."</td>
		                 <td class='comment-form-patient' style='width: 25%;'>".$formRow($form->get('note_externe')).$formTextArea($form->get('note_externe'))."</td>
		                 <td class='comment-form-patient' style='width: 25%;'> </td>
		             </tr>
		      
	           </table>	";
		
		if($intervention->type_intervention == 'Interne'){
			$html .="<script>
				      $('.transfert_externe').toggle(false);
				      $('#vider_champ_externe').toggle(false);
		
		    		 $('#hopital_accueil').val('');
		    		 $('#id_service_externe').val('');
		    		 $('#date_debut_externe').val('');
	                 $('#date_fin_externe').val('');
	                 $('#motif_intervention_externe').val('');
	                 $('#note_externe').val('');
		
     				 </script>";
		}else {
			$html .="<script>
				      $('.transfert_interne').toggle(false);
				      $('#vider_champ_interne').toggle(false);
				 
					  $('#id_service').val('');
	                  $('#date_debut').val('');
	                  $('#date_fin').val('');
	                  $('#motif_intervention').val('');
	                  $('#note').val('');
				
					 </script>";
		}
		
		$html .="<script>
				  //TRANSFERT INTERNE
				    $('#service_origine').val('".$nomService."');
				    $('#service_origine').css({'background':'#eee','border-bottom-width':'0px','border-top-width':'0px','border-left-width':'0px','border-right-width':'0px','font-weight':'bold','color':'#065d10','font-family': 'Times  New Roman','font-size':'17px'});
					$('#service_origine').attr('readonly',true);
		
				    $('#service_accueil, #id_service').css({'font-weight':'bold','color':'#065d10','font-family': 'Times  New Roman','font-size':'16px'});
				    $('#motif_intervention').css({'font-weight':'bold','color':'#065d10','font-family': 'Times  New Roman','font-size':'15px'});
				    $('#note, #note_externe').css({'font-weight':'bold','color':'#065d10','font-family': 'Times  New Roman','font-size':'15px'});
		
				  //TRANSFERT EXTERNE
				    $('#service_origine_externe').val('".$nomService."');
				    $('#service_origine_externe').css({'background':'#eee','border-bottom-width':'0px','border-top-width':'0px','border-left-width':'0px','border-right-width':'0px','font-weight':'bold','color':'#065d10','font-family': 'Times  New Roman','font-size':'17px'});
					$('#service_origine_externe').attr('readonly',true);
		
				    $('#hopital_accueil').css({'font-weight':'bold','color':'#065d10','font-family': 'Times  New Roman','font-size':'16px'});
				    $('#id_service_externe').css({'font-weight':'bold','color':'#065d10','font-family': 'Times  New Roman','font-size':'16px'});
				    $('#motif_intervention_externe').css({'font-weight':'bold','color':'#065d10','font-family': 'Times  New Roman','font-size':'16px'});
			
				    $('#date_debut, #date_fin, #date_debut_externe, #date_fin_externe').css({'font-weight':'bold','color':'#065d10','font-family': 'Times  New Roman','font-size':'16px'});
				  
				    		
				    $('#vider_champ_interne').click(function(){
		               $('#id_service').val('');
		               $('#date_debut').val('');
	                   $('#date_fin').val('');
	                   $('#motif_intervention').val('');
	                   $('#note').val('');
                    });

                    $('#vider_champ_externe').click(function(){
                    	$('#hopital_accueil').val('');
                    	$('#id_service_externe').val('');
                    	$('#date_debut_externe').val('');
                    	$('#date_fin_externe').val('');
                    	$('#motif_intervention_externe').val('');
                    	$('#note_externe').val('');
                    });
				    		
				    calendrier();
				    function getservices(cle) {    
        	          $.ajax({
				    		type: 'POST',
				    		url:  '/simens/public/consultation/services',
				    		data: 'id='+cle,
				    		success: function(data) {
				    		   var result = jQuery.parseJSON(data);
				    		   $('#id_service_externe').html(result);
	                        },
        	            error:function(e){console.log(e);alert('Une erreur interne est survenue!');},
        	            dataType: 'html'
        	          });
        	          return false;
        	         }
				  </script>";
		
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $html ) );
	}
	
	public function raffraichirListeIntervention($id_personne){
		$html = "";
		$this->getDateHelper();
		 
		$html .="<table class='table table-bordered tab_list_mini'  style='margin-top:10px; margin-bottom:20px; margin-left:185px; width:80%;' id='listeintervention'>";
		 
		$html .="<thead style='width: 100%;'>
				  <tr style='height:40px; width:100%; cursor:pointer;'>
					<th style='width: 40%;'>Services</th>
					<th style='width: 15%;'>Date debut</th>
					<th style='width: 15%;'>Date fin</th>
					<th style='width: 18%;'>Intervention</th>
				    <th style='width: 12%;'>Options</th>
				  </tr>
			     </thead>";
		 
		$html .="<tbody style='width: 100%;'>";
		 
		$listeIntervention = $this->getPersonnelTable()->getListeInterventions($id_personne);
		 
		foreach ($listeIntervention as $Liste){
			 
			$html .="<tr style='width: 100%;' id='".$Liste['numero_intervention']."'>";
			$html .="<td style='width: 40%;'><div id='inform' style='float:left; font-weight:bold; font-size:17px;'>".$this->getServiceTable()->getServiceAffectation($Liste['id_service'])->nom."</div></td>";
			$html .="<td style='width: 15%;'><div id='inform' style='float:left; font-weight:bold; font-size:17px;'>".$this->dateHelper->convertDate($Liste['date_debut'])."</div></td>";
			$html .="<td style='width: 15%;'><div id='inform' style='float:left; font-weight:bold; font-size:17px;'>".$this->dateHelper->convertDate($Liste['date_fin'])."</div></td>";
			$html .="<td style='width: 18%;'><div id='inform' style='float:left; font-weight:bold; font-size:17px;'>".$Liste['type_intervention']."</div></td>";
			$html .="<td style='width: 12%;'> <a href='javascript:vueintervention(".$Liste['numero_intervention'].") '>
					       <img style='display: inline;' src='/simens/public/images_icons/vue.PNG' alt='Constantes' title='details' />
					  </a>&nbsp;
			 
				      <a href='javascript:modifierintervention(".$Liste['numero_intervention'].",".$id_personne.")'>
					    	<img style='display: inline;' src='/simens/public/images_icons/suivant.PNG' alt='Constantes' title='modifier' />
					  </a>&nbsp;
			 
				      <a href='javascript:supprimeruneintervention(".$Liste['numero_intervention'].",".$id_personne.")' >
					    	<img  style='display: inline;' src='/simens/public/images_icons/sup.PNG' alt='Constantes' title='annuler' />
					  </a>
				     </td>";
			$html .="</tr>";
			 
		}
		$html .="</tbody>";
		 
		$html .="</table>";
		 
		$html .="<script> listepatient (); </script>";
		
		return $html;
	}
	
	public function saveInterventionAction() {

		$formInterventionPersonnel = new InterventionPersonnelForm();
		$request = $this->getRequest();
		if ($request->isPost()) {
			
			$intervention =  new Intervention1();
			$formInterventionPersonnel->setInputFilter($intervention->getInputFilter());
			$formInterventionPersonnel->setData($request->getPost());
		
			//A COMPRENDRE LE POURQUOI DE CA
			$formInterventionPersonnel->remove('id_service');
			$formInterventionPersonnel->remove('id_service_externe');
			$formInterventionPersonnel->remove('hopital_accueil');
		
			if ($formInterventionPersonnel->isValid()) {
				
				$intervention->exchangeArray($formInterventionPersonnel->getData());

			    $this->getInterventionTable()->saveIntervention($intervention);
			    
			    $id_personne = $intervention->id_personne;
			    
			    $html = $this->raffraichirListeIntervention($id_personne);
			    
			}
		}
		
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $html ) );
	}
	
	public function supprimerInterventionAction(){
		$id_personne = (int) $this->params()-> fromPost('id_personne' , 0);
		
		$this->getInterventionTable()->deleteIntervention($id_personne);
		
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode () );
	}
	
	public function supprimerUneInterventionAction(){
		$numero_intervention = (int) $this->params()-> fromPost('numero_intervention' , 0);
		$id_personne = (int) $this->params()-> fromPost('id_personne' , 0);
	
		$this->getInterventionTable()->deleteUneIntervention($numero_intervention);
	
		$html = $this->raffraichirListeIntervention($id_personne);
		
		$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
		return $this->getResponse ()->setContent ( Json::encode ( $html ) );
	}
	
	//******* Recuperer les services correspondants en cliquant sur un hopital
	public function servicesAction()
	{
		$id=(int)$this->params()->fromPost ('id');
	
		if ($this->getRequest()->isPost()){
			$liste_select = "";
			$services= $this->getServiceTable();
			foreach($services->getServiceHopital($id) as $listeServices){
				$liste_select.= "<option value=".$listeServices['Id_service'].">".$listeServices['Nom_service']."</option>";
			}
				
			$this->getResponse()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html' );
			return $this->getResponse ()->setContent(Json::encode ( $liste_select));
		}
	
	}
}