<?php
/**
 * File for User Controller Class
 *
 * @category  User
 * @package   User_Controller
 * @author    Marco Neumann <webcoder_at_binware_dot_org>
 * @copyright Copyright (c) 2011, Marco Neumann
 * @license   http://binware.org/license/index/type:new-bsd New BSD License
 */

/**
 * @namespace
 */
namespace Admin\Controller;

/**
 * @uses Zend\Mvc\Controller\ActionController
 * @uses User\Form\Login
 */
use Zend\Mvc\Controller\AbstractActionController,
    Admin\Form\LoginForm as LoginForm;
use Zend\Json\Json;
use Admin\Form\UtilisateurForm;
use Admin\Form\ModifierUtilisateurForm;
use Zend\Form\View\Helper\FormButton;
use Zend\Form\View\Helper\FormRow;
use Zend\Form\View\Helper\FormText;
use Zend\Form\View\Helper\FormSubmit;
use Admin\Form\HopitalForm;
use Zend\Form\View\Helper\FormSelect;
use Admin\Form\ServiceForm;
use Admin\Form\ActeForm;

/**
 * User Controller Class
 *
 * User Controller
 *
 * @category  User
 * @package   User_Controller
 * @copyright Copyright (c) 2011, Marco Neumann
 * @license   http://binware.org/license/index/type:new-bsd New BSD License
 */
class AdminController extends AbstractActionController
{
	protected $utilisateurTable;
	protected $serviceTable;
	protected $parametragesTable;
	
	public function getUtilisateurTable(){
		if(!$this->utilisateurTable){
			$sm = $this->getServiceLocator();
			$this->utilisateurTable = $sm->get('Admin\Model\UtilisateursTable');
		}
		return $this->utilisateurTable;
	}

	public function getServiceTable() {
		if (! $this->serviceTable) {
			$sm = $this->getServiceLocator ();
			$this->serviceTable = $sm->get ( 'Personnel\Model\ServiceTable' );
		}
		return $this->serviceTable;
	}
	
	public function getParametragesTable() {
		if (! $this->parametragesTable) {
			$sm = $this->getServiceLocator ();
			$this->parametragesTable = $sm->get ( 'Admin\Model\ParametragesTable' );
		}
		return $this->parametragesTable;
	}
	/**
	 * =========================================================================
	 * =========================================================================
	 * =========================================================================
	 */
	
    /**
     * Index Action
     */
    public function indexAction()
    {
        //@todo - Implement indexAction
    }

    /**
     * Login Action
     *
     * @return array
     */
    public function loginAction()
    {
    	$erreur_message = $this->params()->fromRoute('id', 0);
    	$uAuth = $this->getServiceLocator()->get('Admin\Controller\Plugin\UserAuthentication'); //@todo - We must use PluginLoader $this->userAuthentication()!!
    	if ($uAuth->getAuthService()->hasIdentity()) {
    		return $this->redirect ()->toRoute ('admin', array('action'=>'bienvenue') );
    	}
    	
        $form = new LoginForm();
        $request = $this->getRequest();

        if ($request->isPost()){ 
        
            $form->setData ( $request->getPost () );
            
        	if($form->isValid()) {
        
            $uAuth = $this->getServiceLocator()->get('Admin\Controller\Plugin\UserAuthentication'); //@todo - We must use PluginLoader $this->userAuthentication()!!
            $authAdapter = $uAuth->getAuthAdapter();

            $username = $request->getPost ( 'username' );
            $password = $request->getPost ( 'password' );
            
            if($username && $password) {
            	$authAdapter->setIdentity($username);
            	$authAdapter->setCredential($this->getUtilisateurTable()->encryptPassword($password));
            	
            	if( $uAuth->getAuthService()->authenticate($authAdapter)->isValid()) {
            		return $this->redirect()->toRoute('admin', array('action' => 'bienvenue'));
            	}else {
            		return $this->redirect()->toRoute('admin', array('action' => 'login', 'id' => '1'));
            	}
            }
        	
        	}
        }
        
        return array(
        		'loginForm' => $form,
        		'erreur_message' => $erreur_message
        );
    }

    /**
     * Logout Action
     */
    public function logoutAction()
    {
        $uAuth = $this->getServiceLocator()->get('Admin\Controller\Plugin\UserAuthentication'); //@todo - We must use PluginLoader $this->userAuthentication()!!

        $uAuth->getAuthService()->clearIdentity();
        
        return $this->redirect()->toRoute('admin', array('action' => 'login'));
    }
    
    public function bienvenueAction() 
    {
    	$uAuth = $this->getServiceLocator()->get('Admin\Controller\Plugin\UserAuthentication'); //@todo - We must use PluginLoader $this->userAuthentication()!!
    	
    	$username = $uAuth->getAuthService()->getIdentity();
    	
    	$user = $this->getUtilisateurTable()->getUtilisateursWithUsername($username);
    	
    	if(!$user){
    		return $this->redirect()->toRoute('admin', array('action' => 'login'));
    	}
    	
    	if($user['role'] == "superAdmin")
    	{
    		return array(
    				'user' => $user,
    		);
    	}
    	//********** CERPAD ********* CERPAD *********** CERPAD ******************
    	//********** CERPAD ********* CERPAD *********** CERPAD ******************
    	//********** CERPAD ********* CERPAD *********** CERPAD ******************
    	else if($user['role'] == "secretariat")
    	{
    		return $this->redirect()->toRoute('secretariat', array('action' => 'demandes-analyses'));
    	}
    	else if($user['role'] == "facturation")
    	{
    	    return $this->redirect()->toRoute('facturation', array('action' => 'admission'));
    	}
    	else if($user['role'] == "infirmier")
    	{
    		return $this->redirect()->toRoute('infirmerie', array('action' => 'liste-patient'));
    	}
    	else if($user['role'] == "technicien")
    	{
    		return $this->redirect()->toRoute('technicien');
    	}
    	else if($user['role'] == "biologiste")
    	{
    		return $this->redirect()->toRoute('biologiste');
    	}
    	else if($user['role'] == "medecin")
    	{
    		return $this->redirect()->toRoute('consultation');
    	}
    	else if($user['role'] == "admin")
    	{
    		return array(
    				'user' => $user,
    		);
    	}
    	
    	echo '<div style="font-size: 25px; color: green; padding-bottom: 15px;" >vous n\'avez aucun privilège. Contacter l\'administrateur ----> Merci !!! </div>'; 
    	echo '<a style="font-size: 20px; color: red;" href="http://localhost/simens/public/admin/logout">Terminer</a>';
    	exit();
    }
    
    
    /**
     * GESTION DES UTILISATEURS
     */
    public function modifierUtilisateurAction() 
    {
    	$id = $this->params()->fromPost('id');
    	$chemin = $this->getServiceLocator()->get('Request')->getBasePath();
    	
    	$utilisateur = $this->getUtilisateurTable()->getUtilisateurs($id);
    	$unAgent = $this->getUtilisateurTable()->getAgentPersonnel($utilisateur->idpersonne);
    	
    	$date = $this->convertDate ( $unAgent['date_naissance'] );
    	
    	$serviceAgent = $this->getUtilisateurTable()->getServiceAgent($utilisateur->idpersonne);
    	
    	$html ="<script> 
    			  $('#id').val('".$utilisateur->id."');
    			  $('#username').val('".$utilisateur->username."');
    			  $('#nomUtilisateur').val('".$unAgent['nom']."');
    			  $('#prenomUtilisateur').val('".$unAgent['prenom']."');
    			  $('#idService').val('".$serviceAgent['IdService']."');
    			  $('#fonction').val('".$utilisateur->fonction."');
    			  $('#idPersonne').val('".$utilisateur->idpersonne."'); 
    			  $('#LesChoixRadio input[name=role]').attr('checked', false);
    			  $('#LesChoixRadio input[name=role][value=".$utilisateur->role."] ').attr('checked', true);
    			  $('#LesChoixRadioCerpad input[name=rolecerpad][value=".$utilisateur->role."] ').attr('checked', true);
    			 
    			  $('.nom').text('".$unAgent['nom']."');
    			  $('.prenom').text('".$unAgent['prenom']."');
    			  $('.date_naissance').html('".$date."');		
    			  $('.adresse').html('".$unAgent['adresse']."');
    			  $('.service').html('".$serviceAgent['NomService']."');
    			  $('#photo').html('<img style=\'width:105px; height:105px;\' src=\'".$chemin."/img/photos_personnel/" . $unAgent['photo'] . "  \' >');
    			  		
    			</script>"; 
    	
    	
    	$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
    	return $this->getResponse ()->setContent ( Json::encode ($html) );
    }
    
    public function listeUtilisateursAjaxAction() {
    	$output = $this->getUtilisateurTable()->getListeUtilisateurs();
    	return $this->getResponse ()->setContent ( Json::encode ( $output, array (
    			'enableJsonExprFinder' => true
    	) ) );
    }
    
    public function utilisateursAction()
    {
    	$this->layout ()->setTemplate ( 'layout/layoutUtilisateur' );
    	
    	$formUtilisateur = new UtilisateurForm();
    	
    	$listeService = $this->getUtilisateurTable()->fetchService();
    	$formUtilisateur->get ( 'service' )->setValueOptions ( array_merge( array(""), $listeService ) );
    	
    	$request = $this->getRequest();
    	
    	if ($request->isPost()){
    	
    		$donnees = $request->getPost ();
    		$this->getUtilisateurTable()->saveDonnees($donnees);
    	    
    		return $this->redirect()->toRoute('admin' , array('action' => 'utilisateurs'));
    		
    	}
    	    	
    	return array(
    		'formUtilisateur' => $formUtilisateur
    	);
    }
    
    public function baseUrlRacine() {
    	$baseUrl = $_SERVER ['SCRIPT_FILENAME'];
    	$tabURI = explode ( 'public', $baseUrl );
    	return $tabURI[0];
    }
    
    //************************************************************************************
    //************************************************************************************
    //************************************************************************************
    public function modifierPasswordAction()
    {
    	$uAuth = $this->getServiceLocator()->get('Admin\Controller\Plugin\UserAuthentication'); //@todo - We must use PluginLoader $this->userAuthentication()!!
    	$username = $uAuth->getAuthService()->getIdentity();
    	$user = $this->getUtilisateurTable()->getUtilisateursWithUsername($username);
    	 
    	if(!$user){
    		return $this->redirect()->toRoute('admin', array('action' => 'login'));
    	}

    	$this->layout ()->setTemplate ( 'layout/secretariat' );
    	
    	$controller = $this->params()->fromRoute('id', 0); 
    
    	$formUtilisateur = new ModifierUtilisateurForm(); 
    		
    	$listeService = $this->getUtilisateurTable()->fetchService (); 
    	$formUtilisateur->get ( 'service' )->setValueOptions ( array_merge( array(""), $listeService ) );
    	
 		$request = $this->getRequest();
 		//var_dump($user); exit();
    		
  		if ($request->isPost()){
  			$donnees = $request->getPost ();
  			
  			//ENREGISTREMENT DE L'IMAGE DE L'AGENT --- ENREGISTREMENT DE L'IMAGE DE L'AGENT
  			//ENREGISTREMENT DE L'IMAGE DE L'AGENT --- ENREGISTREMENT DE L'IMAGE DE L'AGENT
  			$today = new \DateTime ( 'now' );
  			$nomPhoto = $today->format ( 'dmy_His' ).'.jpg';
  			$fileBase64 = $this->params ()->fromPost ('fichier_tmp');
  			$fileBase64 = substr($fileBase64, 23);

  			if($fileBase64){
  				$img = imagecreatefromstring(base64_decode($fileBase64));
  			} else {
  				$img = false;
  			}
  			$anciennePhoto = $user['Photo'];
  			
  			if ($img != false) {
  				if($anciennePhoto){ //SI LA PHOTO EXISTE BIEN ELLE EST SUPPRIMER DU DOSSIER POUR ETRE REMPLACER PAR LA NOUVELLE
  					if($anciennePhoto != 'identite.jpg'){ unlink ( $this->baseUrlRacine().'public/img/photos_personnel/' . $anciennePhoto ); }
  				}
  				imagejpeg ( $img, $this->baseUrlRacine().'public/img/photos_personnel/' . $nomPhoto );
  					
  				//ON ENREGISTRE AVEC LA NOUVELLE PHOTO
  				$this->getUtilisateurTable()->modifierPersonne($donnees, $user['idemploye'], $nomPhoto);
  			} else {
  				//PAS DE NOUVELLE PHOTO
  				$this->getUtilisateurTable()->modifierPersonne($donnees, $user['idemploye'], $anciennePhoto);
  			}
  			
  			
  			
  			if($controller == 1){
  				return $this->redirect()->toRoute('secretariat' , array('action' => 'demandes-analyses'));
  			} else if($controller == 2){
  				return $this->redirect()->toRoute('laboratoire' , array('action' => 'liste-demandes-analyses'));
  			} else if($controller == 3){
  				return $this->redirect()->toRoute('facturation' , array('action' => 'admission'));
  			} else if($controller == 4){
  				return $this->redirect()->toRoute('infirmerie' , array('action' => 'liste-patient'));
  			} else if($controller == 5){
  				return $this->redirect()->toRoute('technicien' , array('action' => 'liste-bilans'));
  			} else if($controller == 6){
  				return $this->redirect()->toRoute('biologiste' , array('action' => 'liste-resultats-analyses'));
  			} else if($controller == 7){
  				return $this->redirect()->toRoute('admin' , array('action' => 'bienvenue'));
  			} 
  			
  		}
    
  		$data = array(
  				'id' => $user['id'],
  				'nomUtilisateur' => $user['Nom'],
  				'prenomUtilisateur' => $user['Prenom'],
  				'username' => $user['username'],
  				'fonction' => $user['fonction'],
  				'service' => $user['IdService'],
  		);
  		
  		$formUtilisateur->populateValues($data);
  		
    	return array(
    			'formUtilisateur' => $formUtilisateur,
    			'controller' => $controller,
    			'photo' => $user ['Photo'],
    	);
    }
    
    public function verifierPasswordAction()
    {
    	$id = $this->params()->fromPost('id');
    	$password = $this->params()->fromPost('password');
    
    	$utilisateur = $this->getUtilisateurTable()->getUtilisateurs($id);
    	$passwordDecrypte = $this->getUtilisateurTable()->encryptPassword($password);
    	$resultComparer = 0;
    	if($passwordDecrypte == $utilisateur->password) {
    		$resultComparer = 1;
    	}
    	
    	$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
    	return $this->getResponse ()->setContent ( Json::encode ($resultComparer) );
    }
    
    public function verifierUsernameAction()
    {
    	$username = $this->params()->fromPost('username');
    	
    	$utilisateur = $this->getUtilisateurTable()->getUtilisateursWithUsername($username);
    	
    	$resultExistance = 0;
    	if($utilisateur) {
    		$resultExistance = 1;
    	}
    	 
    	$this->getResponse ()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html; charset=utf-8' );
    	return $this->getResponse ()->setContent ( Json::encode ($resultExistance) );
    }
    
    //Liste des AGENTS DU PERSONNEL
    public function listeAgentPersonnelAjaxAction() {
    	$output = $this->getUtilisateurTable()->getListeAgentPersonnelAjax();
    	return $this->getResponse ()->setContent ( Json::encode ( $output, array (
    			'enableJsonExprFinder' => true
    	) ) );
    }
    
    public function convertDate($date) {
    	$nouv_date = substr ( $date, 8, 2 ) . '/' . substr ( $date, 5, 2 ) . '/' . substr ( $date, 0, 4 );
    	return $nouv_date;
    }
    
    public function visualisationAction() {
    	
    	$chemin = $this->getServiceLocator()->get('Request')->getBasePath();
    		
    	$id = ( int ) $this->params ()->fromPost ( 'id', 0 );
    	
    	$unAgent = $this->getUtilisateurTable()->getAgentPersonnel($id);
    	
    	$date = $this->convertDate ( $unAgent['date_naissance'] );
    	
    	$html = "<div id='photo' style='float:left; margin-right:20px;' > <img  style='width:105px; height:105px;' src='".$chemin."/img/photos_personnel/" . $unAgent['photo'] . "'></div>";
    	
    	$html .= "<table id='PopupVisualisation'>";
    	
    	$html .= "<tr>";
    	$html .= "<td><a style='text-decoration:underline; font-size:12px;'>Nom:</a><br><p style='width:280px; font-weight:bold; font-size:17px;'>" . $unAgent['nom'] . "</p></td>";
    	$html .= "</tr><tr>";
    	$html .= "<td><a style='text-decoration:underline; font-size:12px;'>Pr&eacute;nom:</a><br><p style='width:280px; font-weight:bold; font-size:17px;'>" . $unAgent['prenom'] . "</p></td>";
    	$html .= "</tr><tr>";
    	$html .= "<td><a style='text-decoration:underline; font-size:12px;'>Date de naissance:</a><br><p style='width:280px; font-weight:bold; font-size:17px;'>" . $date . "</p></td>";
    	$html .= "</tr>";
    	$html .= "<tr>";
    	$html .= "<td><a style='text-decoration:underline; font-size:12px;'>Adresse:</a><br><p style='width:280px; font-weight:bold; font-size:17px;'>" . $unAgent['adresse'] . "</p></td>";
    	$html .= "</tr><tr>";
    	$html .= "<td><a style='text-decoration:underline; font-size:12px;'>T&eacute;l&eacute;phone:</a><br><p style='width:280px; font-weight:bold; font-size:17px;'>" . $unAgent['telephone'] . "</p></td>";
    	$html .= "</tr>";
    	
    	$html .= "</table>";
    	
    	$html .= "<script> $('#PopupVisualisation tr').css({'background':'white'}); </script>";
    	
    	$this->getResponse ()->setMetadata ( 'Content-Type', 'application/html' );
    	return $this->getResponse ()->setContent ( Json::encode ( $html ) );
    }
    
    public function nouvelUtilisateurAction() {

    	$chemin = $this->getServiceLocator()->get('Request')->getBasePath();
    	
    	$id = ( int ) $this->params ()->fromPost ( 'id', 0 );
    	 
    	$unAgent = $this->getUtilisateurTable()->getAgentPersonnel($id);
    	
    	$date = $this->convertDate ( $unAgent['date_naissance'] );
    	 
    	$serviceAgent = $this->getUtilisateurTable()->getServiceAgent($id);
    	
    	$html = "<script> 
    			   $('.nom').text('".$unAgent['nom']."');
    			   $('.prenom').text('".$unAgent['prenom']."');
    			   $('.date_naissance').text('".$date."');		
    			   $('.adresse').text('".$unAgent['adresse']."');
    			   $('.service').text('".$serviceAgent['NomService']."');
    			   		
    			   $('#nomUtilisateur').val('".$unAgent['nom']."');
    			   $('#prenomUtilisateur').val('".$unAgent['prenom']."');
    			   $('#idService').val('".$serviceAgent['IdService']."');
    			   $('#idPersonne').val('".$id."');
    			   		 
    			   $('#photo').html('<img style=\'width:105px; height:105px;\' src=\'".$chemin."/img/photos_personnel/" . $unAgent['photo'] . " \' >');
    			 </script>";
    	
    	$this->getResponse ()->setMetadata ( 'Content-Type', 'application/html' );
    	return $this->getResponse ()->setContent ( Json::encode ( $html ) );
    }
    
    public function miseAJourUserPasswordAction()
    {
    	$ancienUsername = $this->params()->fromPost ('ancienUsername');
    	$nouveauUsername = $this->params()->fromPost ('nouveauUsername');
    	$nouveaupassword = $this->params()->fromPost ('nouveaupassword');
    		
    	$this->getUtilisateurTable()->modifierPasswordAjax($ancienUsername, $nouveauUsername, $nouveaupassword);
    	
    	$uAuth = $this->getServiceLocator()->get('Admin\Controller\Plugin\UserAuthentication'); //@todo - We must use PluginLoader $this->userAuthentication()!!
    	$uAuth->getAuthService()->clearIdentity();
    	
    	$this->getResponse()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html' );
    	return $this->getResponse ()->setContent(Json::encode ( $nouveaupassword ));
    }
    
    //**** PARTIE POUR LA GESTION DES PARAMETRAGES ******
    //**** PARTIE POUR LA GESTION DES PARAMETRAGES ******
    //**** PARTIE POUR LA GESTION DES PARAMETRAGES ******
    //**** PARTIE POUR LA GESTION DES PARAMETRAGES ******
    //**** PARTIE POUR LA GESTION DES PARAMETRAGES ******
    public function parametragesAction() {
    	$this->layout ()->setTemplate ( 'layout/layoutUtilisateur' );
    	
    	//$test = $this->getParametragesTable()->getInfosHopital(25);
    	//var_dump($test['Nom']); exit();
    	//$this->getParametragesTable()->getListeRegions();
    }
    
    public function gestionDesHopitauxAction() {
    	
    	$id = ( int ) $this->params ()->fromPost ( 'id', 0 );
    	
    	$formHopital = new HopitalForm();
    	
    	$formRow = new FormRow();
    	$formButton = new FormButton();
    	$formText = new FormText();
    	$formSubmit = new FormSubmit();
    	$formSelect = new FormSelect();
    	
    	$listeRegions = $this->getParametragesTable()->getListeRegions();
    	$formHopital->get ( 'region' )->setValueOptions ( $listeRegions );
    	$html ="
    			<table style='width: 100%;'>
                  <tr style='width: 100%; background: white;'>  
                      
    			
    			      <!-- FORMULAIRE DE SAISIE DES DONNEES POUR AJOUT D'UN NOUVEAU HOPITAL -->
    			      <!-- FORMULAIRE DE SAISIE DES DONNEES POUR AJOUT D'UN NOUVEAU HOPITAL -->
    			      <!-- FORMULAIRE DE SAISIE DES DONNEES POUR AJOUT D'UN NOUVEAU HOPITAL -->
    			      <!-- FORMULAIRE DE SAISIE DES DONNEES POUR AJOUT D'UN NOUVEAU HOPITAL -->
    			
    			      <td style='width: 42%; height: 350px; vertical-align: top;'>
    			          <!-- AFFICHAGE VISUALISATION -->
    			          <!-- AFFICHAGE VISUALISATION -->
                          <table id='VueDetailsHopital' style='width: 95%; margin-top: 7px; border: 1px solid #cccccc; box-shadow: 0pt 1pt 8px rgba(0, 0, 1, 0.4);'>
                            
    			            <tr style='vertical-align: top; background: #efefef; border: 1px solid #cccccc;'>
                               <td colspan='3' style='font-size: 15px; padding: 7px; font-family: times new roman; color: green; font-weight: bold;'> D&eacute;tails des infos sur l'h&ocirc;pital <img id='PlusFormulaireAjouterHopitaux' style='float: right; cursor:pointer;' src='/simens/public/images_icons/Add14X14.png' title='Ajouter' /></td>
                            </tr>
    			
    			            <tr style='vertical-align: top; background: white;'>
                               <td style='width: 33%; height: 50px; padding: 7px;'>
                                  <a style='text-decoration:underline; font-size:12px;'>Nom:</a><br>
                                  <p style='font-weight:bold; font-size:17px;' id='nomVue'> Nom </p>
                               </td>
                       
                               <td style='width: 33%; height: 50px; padding: 7px;'>
                                  <a style='text-decoration:underline; font-size:12px;'>R&eacute;gion:</a><br>
                                  <p style='font-weight:bold; font-size:17px;' id='regionVue'> R&eacute;gion </p>
                               </td>
                       
                               <td style='width: 33%; height: 50px; padding: 7px;'>
                                  <a style='text-decoration:underline; font-size:12px;'>D&eacute;partement:</a><br>
                                  <p style='font-weight:bold; font-size:17px;' id='departementVue'> D&eacute;partement </p>
                               </td>
                            </tr> 
    			
    			            <tr style='vertical-align: top; background: white;'>
                               <td colspan='2' style='width: 66%; height: 50px; padding: 7px;'>
                                  <a style='text-decoration:underline; font-size:12px;'>Directeur:</a><br>
                                  <p style='font-weight:bold; font-size:17px;' id='directeurVue'> Directeur </p>
                               </td>
                       
                               <td style='width: 33%; height: 50px; padding: 7px;'>
                                  <a style='text-decoration:underline; font-size:12px;'> </a><br>
                                  <p style='font-weight:bold; font-size:17px;'> </p>
                               </td>
                            </tr> 
    			
    			            <tr style='vertical-align: top; background: white;'>
                 	           <td colspan='3' style='padding-top: 10px; padding-bottom: 0px; padding-right: 30px; width: 20%; padding: 7px;'>
                 	              <a style='text-decoration:underline; font-size:13px;'>Note:</a>
                 	              <p id='noteVue' style='background:#f8faf8; font-weight:bold; font-size:17px;'> Note </p>
                 	           </td>
                            </tr> 
    			
                          </table>
    			          
    			          <!-- FORMULAIRE DE SAISIE -->
                		  <!-- FORMULAIRE DE SAISIE -->
    			          <form id='FormulaireAjouterHopitaux'> 
    			           <table style='width: 95%; border: 1px solid #cccccc; margin-top: 7px; box-shadow: 0pt 1pt 5px rgba(0, 0, 0, 0.4);'>
    			            
    			            <tr style='width: 100%; vertical-align: top; background: #efefef; border-bottom: 1px solid #cccccc;'>
                               <td colspan='2' style='font-size: 15px; padding: 7px; font-family: times new roman; color: green; font-weight: bold;'> <span id='labelInfos'> Cr&eacute;ation d'un nouvel h&ocirc;pital </span> <img style='float: right; cursor:pointer;' src='/simens/public/images_icons/infos.png'/></td>
                            </tr>
    			
    			            <tr id='form_patient' style='vertical-align: top; background: white;'>
                               <td colspan='2' class='comment-form-patient' style='padding: 8px;'>
                                   ". $formRow($formHopital->get ( 'nom' )) . $formText($formHopital->get ( 'nom' )) ."
                               </td>
                            </tr> 
                                   		
                            <tr id='form_patient' style='vertical-align: top; background: white;'>
                               <td class='comment-form-patient' style='width: 50%; padding: 8px;'>
                                   ". $formRow($formHopital->get ( 'region' )) . $formSelect($formHopital->get ( 'region' )) ."
                               </td>
                               <td class='comment-form-patient' style='width: 50%; padding: 8px;'>
                                   ". $formRow($formHopital->get ( 'departement' )) . $formSelect($formHopital->get ( 'departement' )) ."
                               </td>
                            </tr> 
                                   		
                            <tr  id='form_patient' style='vertical-align: top; background: white;'>
                               <td  class='comment-form-patient' style='width: 50%; padding: 8px; '>
                                   ". $formRow($formHopital->get ( 'directeur' )) . $formText($formHopital->get ( 'directeur' )) ."
                               </td>
                               <td  class='comment-form-patient' style='width: 50%; padding: 8px;'>
                                   ". $formRow($formHopital->get ( 'note' )) . $formText($formHopital->get ( 'note' )) ."
                               </td>
                            </tr> 
                                   		
                            <tr  id='form_patient' style='vertical-align: top; background: white;'>
                               <td colspan='2' class='comment-form-patient' style='width: 100%; padding: 8px;'>
                                   <table style='width: 100%;'>
                                   		<tr style='background: white;'>
                                   		
                                   		    <td style='width: 50%;'> 
                                   		       <div style='float:right'>
                                   		         ". $formSubmit($formHopital->get ( 'annuler' )) ."
                                               </div>
                                   		    </td>
                                   		         		
                                   		    <td style='width: 50%;'>
                                   		       <div style='margin-left: 5px;'>
                                   		         ". $formSubmit($formHopital->get ( 'enregistrer' )) ."
                                               </div>
                                   		    </td>
                                   		       
                                   		</tr>
                                   </table>
                               </td>
                            </tr> 
                           </table>
                                   		         		
                          </form> 
                                   		
                      </td>
    			
    			      <!-- LISTE DES HOPITAUX -->
                      <!-- LISTE DES HOPITAUX -->
                      <!-- LISTE DES HOPITAUX -->
                      <!-- LISTE DES HOPITAUX -->             		         		
                      <td style='width: 58%; vertical-align: top;'>
                          <table id='listeDesHopitauxAjax' style=' margin-top:5px;' class='table table-bordered tab_list_mini' >
				            <thead>
					          <tr style='height: 40px; width:100%; cursor: pointer; font-family: times new roman;'>
						        <th style='width:30%; font-size:17px; '>N<minus>om</minus></th>
						        <th style='width:30%; font-size:17px; '>R<minus>&eacute;gion</minus></th>
						        <th style='width:30%; font-size:17px; '>D<minus>&eacute;partement</minus></th>
						        <th style='width:10%; font-size:17px; '>O<minus>ptions</minus></th>
					          </tr>
				            </thead>
    			
    			            <tbody>

					           <!-- ************ On affiche les patients en une liste ordonnï¿½e************ -->

				            </tbody>

				            <tfoot id='foot' class='foot_style'>
					          <tr style='height: 35px;'>
						        <th id='nom' style='width: 30%;'><input type='text' name='search_browser'
							       value=' Nom' class='search_init' /></th>
						        <th id='region' style='width: 30%;'><input type='text' name='search_browser'
							       value=' R&eacute;gion' class='search_init' /></th>
						        <th id='departement' style='width: 30%;'><input type='text' name='search_browser'
							       value=' D&eacute;partement' class='search_init' /></th>
						        <th id='options' style='width: 10%;'><input type='hidden' name='search_browser'/></th>
					          </tr>
				            </tfoot>
			              </table>
			          </td>
			
		          </tr>
    			
    			  <tr style='width: 100%; background: white;' >
    			      <td style='width: 42%;'> </td>
    			
    			      <td style='width: 58%; padding-left: 20px;'>". $formSubmit($formHopital->get ( 'terminer' )) ."</td>
    			  </tr>    
              </table>
    		  <div id='scriptVue'> </div>  
 
    	      <script>
    			//EMPECHER LA TOUCHE ENTREE DE SOUMETTRE DE FORMULAIRE 
    			$('table tr td input').keypress(function(event) {
	               if (event.keyCode == 13) {
		             return false;
	               }
                });    
    		  </script>
    	      ";
    	
    	$this->getResponse ()->setMetadata ( 'Content-Type', 'application/html' );
    	return $this->getResponse ()->setContent ( Json::encode ( $html ) );
    }
    
    public function listeHopitauxAjaxAction() {
    	$output = $this->getParametragesTable()->getListeHopitaux();
    	return $this->getResponse ()->setContent ( Json::encode ( $output, array (
    			'enableJsonExprFinder' => true
    	) ) );
    }
    
    public function getDepartementsAction()
    {
    	$id = (int)$this->params()->fromPost ('id');
    
    	if ($this->getRequest()->isPost()){
    		$liste_select = "<option value=''></option>";
    		foreach($this->getParametragesTable()->getListeDepartements($id) as $listeDepartement){
    			$liste_select.= "<option style='color: black;' value=".$listeDepartement['id'].">".$listeDepartement['nom']."</option>";
    		}
    
    		$this->getResponse()->getHeaders ()->addHeaderLine ( 'Content-Type', 'application/html' );
    		return $this->getResponse ()->setContent(Json::encode ( $liste_select));
    	}
    
    }
    
    public function ajouterHopitalAction()
    {
    	$nom = $this->params()->fromPost ('nom');
    	$id_departement = (int)$this->params()->fromPost ('departement');
    	$directeur = $this->params()->fromPost ('directeur');
    	$note = $this->params()->fromPost ('note');
    	$updateHopital = $this->params()->fromPost ('updateHopital', 0);
    	
    	 
    	$uAuth = $this->getServiceLocator()->get('Admin\Controller\Plugin\UserAuthentication'); //@todo - We must use PluginLoader $this->userAuthentication()!!
    	$username = $uAuth->getAuthService()->getIdentity();
    	$user = $this->getUtilisateurTable()->getUtilisateursWithUsername($username);
    	 
    	if($updateHopital == 0){
    		$this->getParametragesTable()->addHopital($nom, $id_departement, $user['id_personne'], $directeur, $note);
    	} else {
    		$this->getParametragesTable()->updateHopital($updateHopital, $nom, $id_departement, $user['id_personne'], $directeur, $note);
    	}

    	 
    	$this->getResponse ()->setMetadata ( 'Content-Type', 'application/html' );
    	return $this->getResponse ()->setContent ( Json::encode ( ) );
    }
    
    public function getInfosHopitalAction() {
    	$id_hopital = $this->params()->fromPost ('id');
    	
    	$infos = $this->getParametragesTable()->getInfosHopital($id_hopital);
    	
    	$html ="<script> 
    			$('#nomVue').html('".str_replace("'", "\'", $infos['Nom'])."'); 
    			$('#departementVue').html('".$infos['Departement']."');
    			$('#regionVue').html('".$infos['Region']."');	
    		    $('#directeurVue').html('".str_replace("'", "\'", $infos['Directeur'])."');	
    		    $('#noteVue').html('".str_replace("'", "\'", $infos['Note'])."');	
    		    </script>";
    	
    	
    	$this->getResponse ()->setMetadata ( 'Content-Type', 'application/html' );
    	return $this->getResponse ()->setContent ( Json::encode ( $html ) );
    }
    
    public function getInfosModificationHopitalAction() {
    	$id_hopital = $this->params()->fromPost ('id');
    	 
    	$infos = $this->getParametragesTable()->getInfosHopital($id_hopital);
    	 
    	$liste_select = '<option value=" "></option>';
    	foreach($this->getParametragesTable()->getListeDepartements($infos['Id_region']) as $listeDepartement){
    		$liste_select.= '<option style="color: black;" value='.$listeDepartement['id'].' >'.$listeDepartement['nom']."</option>";
    	}
    	
    	$html ="<script>
    			 $('#nom').val('".str_replace("'", "\'", $infos['Nom'])."');
    			 $('#region').val('".$infos['Id_region']."');
    			 $('#departement').html('".$liste_select."');               //On charge la liste
    			 $('#departement').val('".$infos['Id_departement']."');     //On selectionne le departement
    			 		
    			 $('#directeur').val('".str_replace("'", "\'", $infos['Directeur'])."').css({'font-size':'13px'});
    	         $('#note').val('".str_replace("'", "\'", $infos['Note'])."').css({'font-size':'13px'});
    		    </script>";
    	 
    	$this->getResponse ()->setMetadata ( 'Content-Type', 'application/html' );
    	return $this->getResponse ()->setContent ( Json::encode ( $html ) );
    }
    
    public function listeServicesAjaxAction() {
    	$output = $this->getParametragesTable()->getListeService();
    	return $this->getResponse ()->setContent ( Json::encode ( $output, array (
    			'enableJsonExprFinder' => true
    	) ) );
    }
    
    public function gestionDesServicesAction() {

    	$id = ( int ) $this->params ()->fromPost ( 'id', 0 );
    	 
     	$formHopital = new ServiceForm();
    	 
     	$formRow = new FormRow();
     	$formText = new FormText();
     	$formSubmit = new FormSubmit();
     	$formSelect = new FormSelect();
    	 
    	$html ="
    			<table style='width: 100%;'>
                  <tr style='width: 100%; background: white;'>
    	
    
    			      <!-- FORMULAIRE DE SAISIE DES DONNEES POUR AJOUT D'UN NOUVEAU SERVICE -->
    			      <!-- FORMULAIRE DE SAISIE DES DONNEES POUR AJOUT D'UN NOUVEAU SERVICE -->
    			      <!-- FORMULAIRE DE SAISIE DES DONNEES POUR AJOUT D'UN NOUVEAU SERVICE -->
    			      <!-- FORMULAIRE DE SAISIE DES DONNEES POUR AJOUT D'UN NOUVEAU SERVICE -->
    
    			      <td style='width: 42%; height: 350px; vertical-align: top;'>
    			          <!-- AFFICHAGE VISUALISATION -->
    			          <!-- AFFICHAGE VISUALISATION -->
                          <table id='VueDetailsService' style='width: 95%; margin-top: 7px; border: 1px solid #cccccc; box-shadow: 0pt 1pt 8px rgba(0, 0, 1, 0.4);'>
    	
    			            <tr style='vertical-align: top; background: #efefef; border: 1px solid #cccccc;'>
                               <td colspan='2' style='font-size: 15px; padding: 7px; font-family: times new roman; color: green; font-weight: bold;'> D&eacute;tails des infos sur le service <img id='PlusFormulaireAjouterService' style='float: right; cursor:pointer;' src='/simens/public/images_icons/Add14X14.png' title='Ajouter' /></td>
                            </tr>
    
    			            <tr style='vertical-align: top; background: white;'>
                               <td colspan='2' style='width: 100%; height: 50px; padding: 7px;'>
                                  <a style='text-decoration:underline; font-size:12px;'>Nom:</a><br>
                                  <p style='font-weight:bold; font-size:17px;' id='nomVue'> Nom </p>
                               </td>
                            </tr>
    
    			            <tr style='vertical-align: top; background: white;'>
                               
    			               <td style='width: 50%; height: 50px; padding: 7px;'>
                                  <a style='text-decoration:underline; font-size:12px;'>Domaine:</a><br>
                                  <p style='font-weight:bold; font-size:17px;' id='domaineVue'> Domaine </p>
                               </td>
    			
    			               <td style='width: 50%; height: 50px; padding: 7px;'>
                                  <a style='text-decoration:underline; font-size:12px;'>Tarif (frs):</a><br>
                                  <p style='font-weight:bold; font-size:17px;' id='tarifVue'> Tarif </p>
                               </td>
            
                            </tr>
    
    			            <tr style='vertical-align: top; background: white;'>
                 	           <td colspan='2' style='padding-top: 10px; padding-bottom: 0px; padding-right: 30px; width: 20%; padding: 7px;'>
                 	              <a style='text-decoration:underline; font-size:13px;'>Note:</a>
                 	              <p id='noteVue' style='background:#f8faf8; font-weight:bold; font-size:17px;'> Note </p>
                 	           </td>
                            </tr>
    
                          </table>
    			  
    			 <!-- FORMULAIRE_DE_SAISIE -->
                 <!-- FORMULAIRE_DE_SAISIE -->
    			 <!-- FORMULAIRE_DE_SAISIE -->
    			 <!-- FORMULAIRE_DE_SAISIE -->
    			 <form id='FormulaireAjouterService'>
    			   <table style='width: 95%; border: 1px solid #cccccc; margin-top: 7px; box-shadow: 0pt 1pt 5px rgba(0, 0, 0, 0.4);'>
    			   
    			     <tr style='width: 100%; vertical-align: top; background: #efefef; border-bottom: 1px solid #cccccc;'>
                         <td colspan='2' style='font-size: 15px; padding: 7px; font-family: times new roman; color: green; font-weight: bold;'> <span id='labelInfos'> Cr&eacute;ation d'un nouveau service </span> <img style='float: right; cursor:pointer;' src='/simens/public/images_icons/infos.png'/></td>
                     </tr>
    
    			     <tr id='form_patient' style='vertical-align: top; background: white;'>
                        <td colspan='2' class='comment-form-patient' style='padding: 8px;'>
                          ". $formRow($formHopital->get ( 'nom' )) . $formText($formHopital->get ( 'nom' )) ."
                        </td>
                     </tr>
                   
                     <tr  id='form_patient' style='vertical-align: top; background: white;'>
                        <td  class='comment-form-patient' style='width: 50%; padding: 8px; '>
                          ". $formRow($formHopital->get ( 'domaine' )) . $formSelect($formHopital->get ( 'domaine' )) ."
                        </td>
                        <td  class='comment-form-patient' style='width: 50%; padding: 8px;'>
                          ". $formRow($formHopital->get ( 'tarif' )) . $formText($formHopital->get ( 'tarif' )) ."
                        </td>
                     </tr>
                   
                     <tr  id='form_patient' style='vertical-align: top; background: white;'>
                        <td colspan='2' class='comment-form-patient' style='width: 100%; padding: 8px;'>
                                   <table style='width: 100%;'>
                                   		<tr style='background: white;'>
                   
                                   		    <td style='width: 50%;'>
                                   		       <div style='float:right'>
                                   		         ". $formSubmit($formHopital->get ( 'annuler' )) ."
                                               </div>
                                   		    </td>
    	
                                   		    <td style='width: 50%;'>
                                   		       <div style='margin-left: 5px;'>
                                   		         ". $formSubmit($formHopital->get ( 'enregistrer' )) ."
                                               </div>
                                   		    </td>
    	
                                   		</tr>
                                   </table>
                               </td>
                     </tr>
                   </table>
                  </form>
                   
                  </td>
    
    			      <!-- LISTE DES SERVICES -->
                      <!-- LISTE DES SERVICES -->
                      <!-- LISTE DES SERVICES -->
                      <!-- LISTE DES SERVICES -->
                      <td style='width: 58%; vertical-align: top;'>
                          <table id='listeDesServicesAjax' style=' margin-top:5px;' class='table table-bordered tab_list_mini' >
				            <thead>
					          <tr style='height: 40px; width:100%; cursor: pointer; font-family: times new roman;'>
						        <th style='width:45%; font-size:17px; '>N<minus>om du service</minus></th>
						        <th style='width:25%; font-size:17px; '>D<minus>omaine</minus></th>
						        <th style='width:15%; font-size:17px; '>T<minus>arif <span style='font-size: 13px;'>(frs)</span> </minus></th>
						        <th style='width:15%; font-size:17px; '>O<minus>ptions</minus></th>
					          </tr>
				            </thead>
    
    			            <tbody>
    	
					           <!-- ************ On affiche les patients en une liste ordonnï¿½e************ -->
    	
				            </tbody>
    	
				            <tfoot id='foot' class='foot_style'>
					          <tr style='height: 35px;'>
						        <th id='nom' style='width: 45%;'><input type='text' name='search_browser'
							       value=' Nom du service' class='search_init' /></th>
						        <th id='region' style='width: 25%;'><input type='text' name='search_browser'
							       value=' Domaine' class='search_init' /></th>
						        <th id='departement' style='width: 15%;'><input type='text' name='search_browser'
							       value=' Tarif' class='search_init' /></th>
						        <th id='options' style='width: 15%;'><input type='hidden' name='search_browser'/></th>
					          </tr>
				            </tfoot>
			              </table>
			          </td>
		
		          </tr>
    
    			  <tr style='width: 100%; background: white;' >
    			      <td style='width: 42%;'> </td>
    
    			      <td style='width: 58%; padding-left: 20px;' id='tester'>". $formSubmit($formHopital->get ( 'terminer' )) ."</td>
    			  </tr>
              </table>
    		  <div id='scriptVueService'> </div>
    	
    	      <script>
     			//EMPECHER LA TOUCHE ENTREE DE SOUMETTRE DE FORMULAIRE
    			//EMPECHER LA TOUCHE ENTREE DE SOUMETTRE DE FORMULAIRE
    			$('table tr td input').keypress(function(event) {
	               if (event.keyCode == 13) {
		             return false;
	               }
                });
    		  </script>
    	      "; 
    	 
    	$this->getResponse ()->setMetadata ( 'Content-Type', 'application/html' );
    	return $this->getResponse ()->setContent ( Json::encode ( $html ) );
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
    	return($str);
    }
    	
    public function getInfosServiceAction() {
    	$id_service = $this->params()->fromPost ('id');
    	 
    	$infos = $this->getParametragesTable()->getInfosService($id_service);
    	 
    	$html ="<script> 
    			  $('#nomVue').html('".str_replace("'", "\'", $infos['NOM'])."');
    			  $('#domaineVue').html('".$infos['DOMAINE']."');
    			  $('#tarifVue').html('".$this->prixMill($infos['TARIF'] )."');
    		    </script>";
    	 
    	 
    	$this->getResponse ()->setMetadata ( 'Content-Type', 'application/html' );
    	return $this->getResponse ()->setContent ( Json::encode ( $html ) );
    }
    
    public function getInfosModificationServiceAction() {
    	$id_service = $this->params()->fromPost ('id');
    
    	$infos = $this->getParametragesTable()->getInfosService($id_service);
    
    	$html ="<script>
    			 $('#nom').val('".str_replace("'", "\'", $infos['NOM'])."');
    			 $('#domaine').val('".$infos['DOMAINE']."');
    			 $('#tarif').val('".$infos['TARIF']."');           
    		    </script>";
    
    	$this->getResponse ()->setMetadata ( 'Content-Type', 'application/html' );
    	return $this->getResponse ()->setContent ( Json::encode ( $html ) );
    }
    
    public function ajouterServiceAction()
    {
    	$nom = $this->params()->fromPost ('nom');
    	$domaine = $this->params()->fromPost ('domaine');
    	$tarif = $this->params()->fromPost ('tarif');
     	$updateService = (int) $this->params()->fromPost ('updateService', 0);
    	 
    
    	$uAuth = $this->getServiceLocator()->get('Admin\Controller\Plugin\UserAuthentication'); //@todo - We must use PluginLoader $this->userAuthentication()!!
    	$username = $uAuth->getAuthService()->getIdentity();
    	$user = $this->getUtilisateurTable()->getUtilisateursWithUsername($username);
    	
    	$html = "";
    
    	if($updateService == 0){
    		$this->getParametragesTable()->addService($nom, $domaine, $tarif, $user['id_personne']);
    	} else {
    		$this->getParametragesTable()->updateService($updateService, $nom, $domaine, $tarif, $user['id_personne']);
    		$html .="entre";
    	}
    
    
    	$this->getResponse ()->setMetadata ( 'Content-Type', 'application/html' );
    	return $this->getResponse ()->setContent ( Json::encode (  ) );
    }
    
    public function supprimerServiceAction(){
    	$id_service = $this->params()->fromPost ('id');
    	
    	$result = $this->getParametragesTable()->supprimerService($id_service);
    	
    	$this->getResponse ()->setMetadata ( 'Content-Type', 'application/html' );
    	return $this->getResponse ()->setContent ( Json::encode ( $result ) );
    }
    
    public function gestionDesActesAction(){

    	$id = ( int ) $this->params ()->fromPost ( 'id', 0 );
    	
    	$formActe = new ActeForm();
    	
    	$formRow = new FormRow();
    	$formText = new FormText();
    	$formSubmit = new FormSubmit();
    	$formSelect = new FormSelect();
    	
    	$html ="
    			<table style='width: 100%;'>
                  <tr style='width: 100%; background: white;'>
   
    	
    			      <!-- FORMULAIRE DE SAISIE DES DONNEES POUR AJOUT D'UN NOUVEAU ACTE -->
    			      <!-- FORMULAIRE DE SAISIE DES DONNEES POUR AJOUT D'UN NOUVEAU ACTE -->
    			      <!-- FORMULAIRE DE SAISIE DES DONNEES POUR AJOUT D'UN NOUVEAU ACTE -->
    			      <!-- FORMULAIRE DE SAISIE DES DONNEES POUR AJOUT D'UN NOUVEAU ACTE -->
    	
    			      <td style='width: 42%; height: 350px; vertical-align: top;'>
    			          <!-- AFFICHAGE VISUALISATION -->
    			          <!-- AFFICHAGE VISUALISATION -->
                          <table id='VueDetailsActe' style='width: 95%; margin-top: 7px; border: 1px solid #cccccc; box-shadow: 0pt 1pt 8px rgba(0, 0, 1, 0.4);'>
   
    			            <tr style='vertical-align: top; background: #efefef; border: 1px solid #cccccc;'>
                               <td colspan='2' style='font-size: 15px; padding: 7px; font-family: times new roman; color: green; font-weight: bold;'> D&eacute;tails des infos sur l'acte <img id='PlusFormulaireAjouterActe' style='float: right; cursor:pointer;' src='/simens/public/images_icons/Add14X14.png' title='Ajouter' /></td>
                            </tr>
    	
    			            <tr style='vertical-align: top; background: white;'>
                               <td colspan='2' style='width: 100%; height: 50px; padding: 7px;'>
                                  <a style='text-decoration:underline; font-size:12px;'>Designation:</a><br>
                                  <p style='font-weight:bold; font-size:17px;' id='designationVue'> Designation </p>
                               </td>
                            </tr>
    	
    			            <tr style='vertical-align: top; background: white;'>
                
    			               <td style='width: 50%; height: 50px; padding: 7px;'>
                                  <a style='text-decoration:underline; font-size:12px;'>Tarif (frs):</a><br>
                                  <p style='font-weight:bold; font-size:17px;' id='tarifVue'> Tarif </p>
                               </td>
    			
    			               <td style='width: 50%; height: 50px; padding: 7px;'>
                                  <a style='text-decoration:underline; font-size:12px;'></a><br>
                                  <p style='font-weight:bold; font-size:17px;'> </p>
                               </td>
    
                            </tr>
    	
    			            <tr style='vertical-align: top; background: white;'>
                 	           <td colspan='2' style='padding-top: 10px; padding-bottom: 0px; padding-right: 30px; width: 20%; padding: 7px;'>
                 	              <a style='text-decoration:underline; font-size:13px;'>Note:</a>
                 	              <p id='noteVue' style='background:#f8faf8; font-weight:bold; font-size:17px;'> Note </p>
                 	           </td>
                            </tr>
    	
                          </table>
    	
    			 <!-- FORMULAIRE_DE_SAISIE -->
                 <!-- FORMULAIRE_DE_SAISIE -->
    			 <!-- FORMULAIRE_DE_SAISIE -->
    			 <!-- FORMULAIRE_DE_SAISIE -->
    			 <form id='FormulaireAjouterActe'>
    			   <table style='width: 95%; border: 1px solid #cccccc; margin-top: 7px; box-shadow: 0pt 1pt 5px rgba(0, 0, 0, 0.4);'>
    	
    			     <tr style='width: 100%; vertical-align: top; background: #efefef; border-bottom: 1px solid #cccccc;'>
                         <td colspan='2' style='font-size: 15px; padding: 7px; font-family: times new roman; color: green; font-weight: bold;'> <span id='labelInfos'> Cr&eacute;ation d'un nouvel acte </span> <img style='float: right; cursor:pointer;' src='/simens/public/images_icons/infos.png'/></td>
                     </tr>
    	
    			     <tr id='form_patient' style='vertical-align: top; background: white;'>
                        <td colspan='2' class='comment-form-patient' style='padding: 8px;'>
                          ". $formRow($formActe->get ( 'designation' )) . $formText($formActe->get ( 'designation' )) ."
                        </td>
                     </tr>
          
                     <tr  id='form_patient' style='vertical-align: top; background: white;'>
                        <td  class='comment-form-patient' style='width: 50%; padding: 8px; '>
                          ". $formRow($formActe->get ( 'tarif' )) . $formText($formActe->get ( 'tarif' )) ."
                        </td>
                        <td  class='comment-form-patient' style='width: 50%; padding: 8px;'>
                        </td>
                     </tr>
          
                     <tr  id='form_patient' style='vertical-align: top; background: white;'>
                        <td colspan='2' class='comment-form-patient' style='width: 100%; padding: 8px;'>
                                   <table style='width: 100%;'>
                                   		<tr style='background: white;'>
          
                                   		    <td style='width: 50%;'>
                                   		       <div style='float:right'>
                                   		         ". $formSubmit($formActe->get ( 'annuler' )) ."
                                               </div>
                                   		    </td>
   
                                   		    <td style='width: 50%;'>
                                   		       <div style='margin-left: 5px;'>
                                   		         ". $formSubmit($formActe->get ( 'enregistrer' )) ."
                                               </div>
                                   		    </td>
   
                                   		</tr>
                                   </table>
                               </td>
                     </tr>
                   </table>
                  </form>
          
                  </td>
    	
    			      <!-- LISTE DES ACTES -->
                      <!-- LISTE DES ACTES -->
                      <!-- LISTE DES ACTES -->
                      <!-- LISTE DES ACTES -->
                      <td style='width: 58%; vertical-align: top;'>
                          <table id='listeDesActesAjax' style=' margin-top:5px;' class='table table-bordered tab_list_mini' >
				            <thead>
					          <tr style='height: 40px; width:100%; cursor: pointer; font-family: times new roman;'>
						        <th style='width:60%; font-size:17px; '>D<minus>&eacute;signation </minus></th>
						        <th style='width:22%; font-size:17px; '>T<minus>arif <span style='font-size: 13px;'>(frs)</span> </minus></th>
						        <th style='width:18%; font-size:17px; '>O<minus>ptions</minus></th>
					          </tr>
				            </thead>
    	
    			            <tbody>
   
					           <!-- ************ On affiche les patients en une liste ordonnï¿½e************ -->
   
				            </tbody>
   
				            <tfoot id='foot' class='foot_style'>
					          <tr style='height: 35px;'>
						        <th id='nom' style='width: 60%;'><input type='text' name='search_browser'
							       value=' D&eacute;signation' class='search_init' /></th>
						        <th id='departement' style='width: 22%;'><input type='text' name='search_browser'
							       value=' Tarif' class='search_init' /></th>
						        <th id='options' style='width: 18%;'><input type='hidden' name='search_browser'/></th>
					          </tr>
				            </tfoot>
			              </table>
			          </td>
    	
		          </tr>
    	
    			  <tr style='width: 100%; background: white;' >
    			      <td style='width: 42%;'> </td>
    	
    			      <td style='width: 58%; padding-left: 20px;' id='tester'>". $formSubmit($formActe->get ( 'terminer' )) ."</td>
    			  </tr>
              </table>
    		  <div id='scriptVueActe'> </div>
   
    	      <script>
     			//EMPECHER LA TOUCHE ENTREE DE SOUMETTRE DE FORMULAIRE
    			//EMPECHER LA TOUCHE ENTREE DE SOUMETTRE DE FORMULAIRE
    			$('table tr td input').keypress(function(event) {
	               if (event.keyCode == 13) {
		             return false;
	               }
                });
    		  </script>
    	      ";
    	
    	$this->getResponse ()->setMetadata ( 'Content-Type', 'application/html' );
    	return $this->getResponse ()->setContent ( Json::encode ( $html ) );
    }
    
    public function listeActesAjaxAction() {
    	$output = $this->getParametragesTable()->getListeActes();
    	return $this->getResponse ()->setContent ( Json::encode ( $output, array (
    			'enableJsonExprFinder' => true
    	) ) );
    }
    
    public function getInfosActeAction() {
    	$id_acte = $this->params()->fromPost ('id');
    
    	$infos = $this->getParametragesTable()->getInfosActe($id_acte);
    
    	$html ="<script>
    			  $('#designationVue').html('".str_replace("'", "\'", $infos['designation'])."');
    			  $('#tarifVue').html('".$this->prixMill($infos['tarif'] )."');
    		    </script>";
    
    
    	$this->getResponse ()->setMetadata ( 'Content-Type', 'application/html' );
    	return $this->getResponse ()->setContent ( Json::encode ( $html ) );
    }
    
    public function getInfosModificationActeAction() {

    	$id_acte = $this->params()->fromPost ('id');
    	
    	$infos = $this->getParametragesTable()->getInfosActe($id_acte);
    	
    	$html ="<script>
    			 $('#designation').val('".str_replace("'", "\'", $infos['designation'])."');
    			 $('#tarif').val('".$infos['tarif']."');
    		    </script>";
    	
    	$this->getResponse ()->setMetadata ( 'Content-Type', 'application/html' );
    	return $this->getResponse ()->setContent ( Json::encode ( $html ) );
    	
    }
    
    public function ajouterActeAction(){ 
    	
    	$designation = $this->params()->fromPost ('designation');
    	$tarif = $this->params()->fromPost ('tarif');
    	$updateActe = (int) $this->params()->fromPost ('updateActe', 0);
    
    
    	$uAuth = $this->getServiceLocator()->get('Admin\Controller\Plugin\UserAuthentication'); //@todo - We must use PluginLoader $this->userAuthentication()!!
    	$username = $uAuth->getAuthService()->getIdentity();
    	$user = $this->getUtilisateurTable()->getUtilisateursWithUsername($username);
    	 
    	if($updateActe == 0){
    		$this->getParametragesTable()->addActe($designation, $tarif, $user['id_personne']);
    	} else {
    		$this->getParametragesTable()->updateActe($updateActe, $designation, $tarif, $user['id_personne']);
    	}
    
    
    	$this->getResponse ()->setMetadata ( 'Content-Type', 'application/html' );
    	return $this->getResponse ()->setContent ( Json::encode (  ) );
    }
    
    public function supprimerActeAction(){
    	$id_acte = (int)$this->params()->fromPost ('id');
    	 
    	$result = $this->getParametragesTable()->supprimerActe($id_acte);
    	 
    	$this->getResponse ()->setMetadata ( 'Content-Type', 'application/html' );
    	return $this->getResponse ()->setContent ( Json::encode ( $result ) );
    }
}
