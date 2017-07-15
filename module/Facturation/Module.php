<?php

namespace Facturation;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\ModuleManager\Feature\ViewHelperProviderInterface;
use Zend\Mvc\MvcEvent;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Facturation\Model\Patient;
use Facturation\Model\PatientTable;
use Facturation\Model\Deces;
use Facturation\Model\DecesTable;
use Facturation\Model\Facturation;
use Facturation\Model\Naissance;
use Facturation\Model\NaissanceTable;
use Facturation\Model\TarifConsultationTable;
use Facturation\Model\TarifConsultation;
use Facturation\Model\Admission;
use Facturation\Model\AdmissionTable;
use Facturation\Model\ServiceTable;
use Facturation\Model\Service;
use Facturation\Model\FacturationTable;

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
						'Facturation\Model\PatientTable' => function ($sm) {
							$tableGateway = $sm->get ( 'PatientTable1Gateway' );
							$table = new PatientTable ( $tableGateway );
							return $table;
						},
						'PatientTable1Gateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new Patient () );
							return new TableGateway ( 'patient', $dbAdapter, null, $resultSetPrototype );
						},
						'Facturation\Model\FacturationTable' => function ($sm) {
						    $tableGateway = $sm->get( 'FacturationTableGateway' );
						    $table = new FacturationTable( $tableGateway );
						    return $table;
						},
						'FacturationTableGateway' => function ($sm) {
						    $dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
						    $resultSetPrototype = new ResultSet ();
						    $resultSetPrototype->setArrayObjectPrototype ( new Facturation() );
						    return new TableGateway ( 'facturation', $dbAdapter, null, $resultSetPrototype );
						},
// 						'Facturation\Model\DecesTable' => function ($sm) {
// 							$tableGateway = $sm->get ( 'DecesTableGateway' );
// 							$table = new DecesTable ( $tableGateway );
// 							return $table;
// 						},
// 						'DecesTableGateway' => function ($sm) {
// 							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
// 							$resultSetPrototype = new ResultSet ();
// 							$resultSetPrototype->setArrayObjectPrototype ( new Deces () );
// 							return new TableGateway ( 'deces', $dbAdapter, null, $resultSetPrototype );
// 						},
// 						'Facturation\Model\NaissanceTable' => function ($sm) {
// 							$tableGateway = $sm->get( 'NaissanceTable1Gateway' );
// 							$table = new NaissanceTable( $tableGateway );
// 							return $table;
// 						},
// 						'NaissanceTable1Gateway' => function ($sm) {
// 							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
// 							$resultSetPrototype = new ResultSet ();
// 							$resultSetPrototype->setArrayObjectPrototype ( new Naissance() );
// 							return new TableGateway ( 'naissance', $dbAdapter, null, $resultSetPrototype );
// 						},
// 						'Facturation\Model\TarifConsultationTable' => function ($sm) {
// 							$tableGateway = $sm->get( 'TarifConsultationTableGateway' );
// 							$table = new TarifConsultationTable( $tableGateway );
// 							return $table;
// 						},
// 						'TarifConsultationTableGateway' => function ($sm) {
// 							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
// 							$resultSetPrototype = new ResultSet ();
// 							$resultSetPrototype->setArrayObjectPrototype (new TarifConsultation());
// 							return new TableGateway ( 'tarif_consultation', $dbAdapter, null, $resultSetPrototype );
// 						},
// 						'Facturation\Model\ServiceTable' => function ($sm) {
// 							$tableGateway = $sm->get('ServiceTableFactGateway');
// 							$table = new ServiceTable($tableGateway);
// 							return $table;
// 						},
// 						'ServiceTableFactGateway' => function($sm) {
// 							$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
// 							$resultSetPrototype = new ResultSet();
// 							$resultSetPrototype->setArrayObjectPrototype(new Service());
// 							return new TableGateway('service', $dbAdapter, null, $resultSetPrototype);
// 						},

				)
		);
	}
	
	public function getViewHelperConfig() {
		return array ();
	}
}