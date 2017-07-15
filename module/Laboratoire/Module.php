<?php

namespace Laboratoire;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\ModuleManager\Feature\ViewHelperProviderInterface;
use Zend\Mvc\MvcEvent;
use Laboratoire\Model\ResultatDemandeAnalyseTable;
use Zend\Db\ResultSet\ResultSet;
use Laboratoire\Model\ResultatDemandeAnalyse;
use Zend\Db\TableGateway\TableGateway;
use Laboratoire\Model\TriPrelevementTable;
use Laboratoire\Model\TriPrelevement;

class Module implements AutoloaderProviderInterface, ConfigProviderInterface, ServiceProviderInterface, ViewHelperProviderInterface {

	public function registerJsonStrategy(MvcEvent $e)
	{
		$app          = $e->getTarget();
		$locator      = $app->getServiceManager();
		$view         = $locator->get('Zend\View\View');
		$jsonStrategy = $locator->get('ViewJsonStrategy');

		// Attach strategy, which is a listener aggregate, at high priority
		$view->getEventManager()->attach($jsonStrategy, 100);
	}

	public function getAutoloaderConfig() {
		return array (
				'Zend\Loader\StandardAutoloader' => array (
						'namespaces' => array (
								__NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__
						)
				)
		);
	}
	public function getConfig() {
		return include __DIR__ . '/config/module.config.php';
	}
	public function getServiceConfig() {
		return array (
				'factories' => array (
						'Laboratoire\Model\ResultatDemandeAnalyseTable' => function ($sm) {
							$tableGateway = $sm->get ( 'ResultatDemandeAnalyseTableGateway' );
							$table = new ResultatDemandeAnalyseTable( $tableGateway );
							return $table;
						},
						'ResultatDemandeAnalyseTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet();
							$resultSetPrototype->setArrayObjectPrototype ( new ResultatDemandeAnalyse() );
							return new TableGateway( 'resultat_demande_analyse', $dbAdapter, null, $resultSetPrototype );
						},
						'Laboratoire\Model\TriPrelevementTable' => function ($sm) {
							$tableGateway = $sm->get ( 'TriPrelevementTableTableGateway' );
							$table = new TriPrelevementTable( $tableGateway );
							return $table;
						},
						'TriPrelevementTableTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet();
							$resultSetPrototype->setArrayObjectPrototype ( new TriPrelevement() );
							return new TableGateway( 'tri_prelevement', $dbAdapter, null, $resultSetPrototype );
						},
 				)
		);
	}
	
	public function getViewHelperConfig() {
		return array ();
	}
}