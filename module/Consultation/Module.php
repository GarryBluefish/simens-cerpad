<?php

namespace Consultation;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\ModuleManager\Feature\ViewHelperProviderInterface;
use Zend\Mvc\MvcEvent;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Consultation\Model\Depistage;
use Consultation\Model\DepistageTable;
use Consultation\Model\AnalyseTable;
use Consultation\Model\Analyse;
use Consultation\Model\AntecedentsFamiliauxTable;
use Consultation\Model\AntecedentsFamiliaux;
use Consultation\Model\HistoireMaladieTable;
use Consultation\Model\HistoireMaladie;
use Consultation\Model\DonneesExamenTable;
use Consultation\Model\DonneesExamen;
use Consultation\Model\DiagnosticConsultationTable;
use Consultation\Model\DiagnosticConsultation;
use Consultation\Model\ConsultationTable;
use Consultation\Model\Consultation;
use Consultation\Model\ExamenTable;
use Consultation\Model\Examen;

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
						'Consultation\Model\DepistageTable' => function ($sm) {
						    $tableGateway = $sm->get( 'DepistageModuleConsultationTableGateway' );
						    $table = new DepistageTable( $tableGateway );
						    return $table;
						},
						'DepistageModuleConsultationTableGateway' => function ($sm) {
						    $dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
						    $resultSetPrototype = new ResultSet ();
						    $resultSetPrototype->setArrayObjectPrototype ( new Depistage() );
						    return new TableGateway ( 'depistage', $dbAdapter, null, $resultSetPrototype );
						},
						
						'Consultation\Model\AnalyseTable' => function ($sm) {
							$tableGateway = $sm->get( 'AnalyseModuleConsultationTableGateway' );
							$table = new AnalyseTable( $tableGateway );
							return $table;
						},
						'AnalyseModuleConsultationTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new Analyse() );
							return new TableGateway ( 'analyse', $dbAdapter, null, $resultSetPrototype );
						},
						
						'Consultation\Model\AntecedentsFamiliauxTable' => function ($sm) {
							$tableGateway = $sm->get( 'AntecedentsFamiliauxModuleConsultationTableGateway' );
							$table = new AntecedentsFamiliauxTable( $tableGateway );
							return $table;
						},
						'AntecedentsFamiliauxModuleConsultationTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new AntecedentsFamiliaux() );
							return new TableGateway ( 'antecedents_familiaux', $dbAdapter, null, $resultSetPrototype );
						},
						
						'Consultation\Model\HistoireMaladieTable' => function ($sm) {
							$tableGateway = $sm->get( 'HistoireMaladieModuleConsultationTableGateway' );
							$table = new HistoireMaladieTable( $tableGateway );
							return $table;
						},
						'HistoireMaladieModuleConsultationTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new HistoireMaladie() );
							return new TableGateway ( 'histoire_maladie_patient', $dbAdapter, null, $resultSetPrototype );
						},

						'Consultation\Model\DonneesExamenTable' => function ($sm) {
							$tableGateway = $sm->get( 'DonneesExamenModuleConsultationTableGateway' );
							$table = new DonneesExamenTable( $tableGateway );
							return $table;
						},
						'DonneesExamenModuleConsultationTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new DonneesExamen() );
							return new TableGateway ( 'donnees_de_examen_patient', $dbAdapter, null, $resultSetPrototype );
						},
						
						'Consultation\Model\DiagnosticConsultationTable' => function ($sm) {
							$tableGateway = $sm->get( 'DiagnosticConsultationModuleConsultationTableGateway' );
							$table = new DiagnosticConsultationTable( $tableGateway );
							return $table;
						},
						'DiagnosticConsultationModuleConsultationTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new DiagnosticConsultation() );
							return new TableGateway ( 'diagnostic_jour_consultation', $dbAdapter, null, $resultSetPrototype );
						},
						
						'Consultation\Model\ConsultationTable' => function ($sm) {
							$tableGateway = $sm->get( 'ConsultationModuleConsultationTableGateway' );
							$table = new ConsultationTable($tableGateway);
							return $table;
						},
						'ConsultationModuleConsultationTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new Consultation() );
							return new TableGateway ( 'consultation', $dbAdapter, null, $resultSetPrototype );
						},
						
						'Consultation\Model\ExamenTable' => function ($sm) {
							$tableGateway = $sm->get( 'ExamenModuleConsultationTableGateway' );
							$table = new ExamenTable($tableGateway);
							return $table;
						},
						'ExamenModuleConsultationTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new Examen() );
							return new TableGateway ( 'examen_imagerie', $dbAdapter, null, $resultSetPrototype );
						},
						
				)
		);
	}
	
	public function getViewHelperConfig() {
		return array ();
	}
}