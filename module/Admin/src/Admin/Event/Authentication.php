<?php
/**
 * File for Event Class
 *
 * @category  User
 * @package   User_Event
 * @author    Marco Neumann <webcoder_at_binware_dot_org>
 * @copyright Copyright (c) 2011, Marco Neumann
 * @license   http://binware.org/license/index/type:new-bsd New BSD License
 */

/**
 * @namespace
 */
namespace Admin\Event;

/**
 * @uses Zend\Mvc\MvcEvent
 * @uses User\Controller\Plugin\UserAuthentication
 * @uses User\Acl\Acl
 */
use Zend\Mvc\MvcEvent as MvcEvent,
    Admin\Controller\Plugin\UserAuthentication as AuthPlugin,
    Admin\Acl\Acl as AclClass;
use Zend\Mvc\Controller\AbstractActionController;

/**
 * Authentication Event Handler Class
 *
 * This Event Handles Authentication
 *
 * @category  User
 * @package   User_Event
 * @copyright Copyright (c) 2011, Marco Neumann
 * @license   http://binware.org/license/index/type:new-bsd New BSD License
 */
class Authentication extends AbstractActionController
{
	protected $utilisateurTable;
	
	public function getUtilisateurTable(){
		if(!$this->utilisateurTable){
			$sm = $this->getServiceLocator();
			$this->utilisateurTable = $sm->get('Admin\Model\UtilisateursTable');
		}
		return $this->utilisateurTable;
	}
	
	/** 
	 * ========================================================================= 
	 * ========================================================================= 
	 * ========================================================================= 
	 */
	
    /**
     * @var AuthPlugin
     */
    protected $_userAuth = null;

    /**
     * @var AclClass
     */
    protected $_aclClass = null;

    /**
     * preDispatch Event Handler
     *
     * @param \Zend\Mvc\MvcEvent $event
     * @throws \Exception
     */
    public function preDispatch(MvcEvent $event)
    {
        //@todo - C'est ici qu'on utilise le plugin UserAuthentification pour verifier si le patient est connecté par exemple
        $userAuth = $this->getUserAuthenticationPlugin();
        
        //@todo - On appel la classe ACL pour la gestion des roles et des ressources
        $acl = $this->getAclClass();

        $role  = AclClass::DEFAULT_ROLE;

        if ($userAuth->hasIdentity()) {
        	
            $username = $userAuth->getIdentity();
            $user = $this->getUtilisateurTable()->getUtilisateursWithUsername($username);
            //$role  = 'member'; //@todo - Get role from user!
            $role  = $user['role'] ;
            //si le role n'est pas encore defini dans le jeu d'ACL
            if(!$acl->hasRole($role)){
            	echo '<div style="margin-left: 25%; font-size: 25px; color: green; padding-bottom: 15px;" >Le role ... <i style="color: red;">('.$role.') </i> n\' est pa défini dans le jeu d\'ACL </div>';
            	exit();
            }
        }

        
        $routeMatch = $event->getRouteMatch();
        $controller = $routeMatch->getParam('controller');
        $action     = $routeMatch->getParam('action');

        //Si la ressource n'existe pas on lance une exception 
        if (!$acl->hasResource($controller)) {
        	if (!$userAuth->hasIdentity()) {
        		$chemin = $this->getServiceLocator()->get('Request')->getBasePath();
        		header('Location: '.$chemin.'/admin');
        		exit() ;
        	}
        	echo '<div style="margin-left: 25%; font-size: 25px; color: green; padding-bottom: 15px; float:left; " >La Ressource </div>  <div style="font-size: 25px; padding-bottom: 15px; padding-left: 10px; padding-right: 10px; color: red; float:left;">'.$controller.'</div>  <div style="font-size: 25px; color: green; padding-bottom: 15px; float:left; ">n\'est pas defini </div>';
        	exit();
        }

        //Si la ressource existe et que l'utilisateur n'en est pas accès
        if (!$acl->isAllowed($role, $controller, $action)) {

        	if (!$userAuth->hasIdentity()) {
        		$chemin = $this->getServiceLocator()->get('Request')->getBasePath();
        		header('Location: '.$chemin.'/admin');
        		exit() ;
        	}
            echo '<div style="font-size: 25px; color: green; padding-bottom: 15px;" >vous n\'avez pas accès a cette page ... <i style="color: red;">('.$action.') </i> vous n\' avez pas les privilèges requis </div>';
            exit();
        }
        
        //Sinon c 'est bon
    }

    /**
     * Sets Authentication Plugin
     *
     * @param \User\Controller\Plugin\UserAuthentication $userAuthenticationPlugin
     * @return Authentication
     */
    public function setUserAuthenticationPlugin(AuthPlugin $userAuthenticationPlugin)
    {
        $this->_userAuth = $userAuthenticationPlugin;

        return $this;
    }

    /**
     * Gets Authentication Plugin
     *
     * @return \User\Controller\Plugin\UserAuthentication
     */
    public function getUserAuthenticationPlugin()
    {
        if ($this->_userAuth === null) {
            $this->_userAuth = new AuthPlugin();
        }

        return $this->_userAuth;
    }

    /**
     * Sets ACL Class
     *
     * @param \User\Acl\Acl $aclClass
     * @return Authentication
     */
    public function setAclClass(AclClass $aclClass)
    {
        $this->_aclClass = $aclClass;

        return $this;
    }

    /**
     * Gets ACL Class
     *
     * @return \User\Acl\Acl
     */
    public function getAclClass()
    {
        if ($this->_aclClass === null) {
            $this->_aclClass = new AclClass(array());
        }

        return $this->_aclClass;
    }
}
