<?php

namespace Admin;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Admin\Model\UtilisateursTable;
use Zend\Db\ResultSet\ResultSet;
use Admin\Model\Utilisateurs;
use Zend\Db\TableGateway\TableGateway;
use Zend\EventManager\StaticEventManager;
use Zend\Mvc\MvcEvent;
use Admin\Model\ParametragesTable;
use Admin\Model\Parametrages;

class Module implements AutoloaderProviderInterface
{
	
	/**
	 * Init function
	 *
	 * @return void
	 */
	public function init()
	{
		// Attach Event to EventManager
		$events = StaticEventManager::getInstance();
		$events->attach('Zend\Mvc\Controller\AbstractActionController', 'dispatch', array($this, 'mvcPreDispatch'), 100); //@todo - Go directly to User\Event\Authentication
	    
	}
	
    /**
     * Get Autoloader Configuration
     *
     * @return array
     */
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php'
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__
                )
            )
        );
    }
    
    
	public function getConfig() {
		return include __DIR__ . '/config/module.config.php';
	}
	
	
	/**
	 * MVC preDispatch Event
	 *
	 * @param $event
	 * @return mixed
	 */
	public function mvcPreDispatch($event) {
		$di = $event->getTarget()->getServiceLocator();
		$auth = $di->get('Admin\Event\Authentication');
	
		return  $auth->preDispatch($event);
	}
	
	public function getServiceConfig()
	{
		return array(
				'factories' => array(
						'Admin\Model\UtilisateursTable' =>  function($sm) {
							$tableGateway = $sm->get('UtilisateursTableGateway');
							$table = new UtilisateursTable($tableGateway);
							return $table;
						},
						'UtilisateursTableGateway' => function ($sm) {
							$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
							$resultSetPrototype = new ResultSet();
							$resultSetPrototype->setArrayObjectPrototype(new Utilisateurs());
							return new TableGateway('utilisateurs', $dbAdapter, null, $resultSetPrototype);
						},
						'Admin\Model\ParametragesTable' =>  function($sm) {
							$tableGateway = $sm->get('ParametragesTableGateway');
							$table = new ParametragesTable($tableGateway);
							return $table;
						},
						'ParametragesTableGateway' => function ($sm) {
							$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
							$resultSetPrototype = new ResultSet();
							$resultSetPrototype->setArrayObjectPrototype(new Parametrages());
							return new TableGateway("", $dbAdapter, null, $resultSetPrototype); //Aucune table
						},
				),
		);
	}
	
	
	/** UTILISER PAR TOUS LES MODULES **/
	public function onBootstrap(MvcEvent $e) {
		$serviceManager = $e->getApplication ()->getServiceManager ();
		$viewModel = $e->getApplication ()->getMvcEvent ()->getViewModel ();
	
		$uAuth = $serviceManager->get( 'Admin\Controller\Plugin\UserAuthentication' ); //@todo - We must use PluginLoader $this->userAuthentication()!!
		$username = $uAuth->getAuthService()->getIdentity();
	
		$uTable = $serviceManager->get( 'Admin\Model\UtilisateursTable' );
		$user = $uTable->getUtilisateursWithUsername($username);
		
		if($user) {
			$viewModel->user = $user;
			$viewModel->service = $user['NomService'];
		}
	}
}