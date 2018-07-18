<?php

namespace Secretariat;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\ModuleManager\Feature\ViewHelperProviderInterface;
use Zend\Mvc\MvcEvent;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Secretariat\Model\Patient;
use Secretariat\Model\PatientTable;
use Secretariat\Model\PersonneTable;
use Secretariat\Model\Personne;
use Secretariat\Model\AnalyseTable;
use Secretariat\Model\Analyse;
use Secretariat\Model\listeRecherche;
use Secretariat\Model\listeRechercheTable;
use Secretariat\Model\listeDossierPatientTable;
use Secretariat\Model\listeDossierPatient;

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
						'Secretariat\Model\PersonneTable' => function ($sm) {
							$tableGateway = $sm->get ( 'PersonneTable1Gateway' );
							$table = new PersonneTable ( $tableGateway );
							return $table;
						},
						'PersonneTable1Gateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new Personne () );
							return new TableGateway ( 'personne', $dbAdapter, null, $resultSetPrototype );
						},
						
						'Secretariat\Model\PatientTable' => function ($sm) {
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
						
						'Secretariat\Model\AnalyseTable' => function ($sm) {
							$tableGateway = $sm->get ( 'AnalyseTableGateway' );
							$table = new AnalyseTable ( $tableGateway );
							return $table;
						},
						'AnalyseTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new Analyse () );
							return new TableGateway ( 'analyse', $dbAdapter, null, $resultSetPrototype );
						},
						
						'Secretariat\Model\listeRechercheTable' => function ($sm) {
							$tableGateway = $sm->get ( 'ListeRechercheTableGateway' );
							$table = new listeRechercheTable ( $tableGateway );
							return $table;
						},
						'ListeRechercheTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new listeRecherche() );
							return new TableGateway ( 'personne', $dbAdapter, null, $resultSetPrototype );
						},
						
						'Secretariat\Model\listeDossierPatientTable' => function ($sm) {
							$tableGateway = $sm->get ( 'listeDossierPatientTableGateway' );
							$table = new listeDossierPatientTable ( $tableGateway );
							return $table;
						},
						'listeDossierPatientTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new listeDossierPatient() );
							return new TableGateway ( 'personne', $dbAdapter, null, $resultSetPrototype );
						},
						

				)
		);
	}
	
	public function getViewHelperConfig() {
		return array ();
	}
}