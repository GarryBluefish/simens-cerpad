<?php

namespace Infirmerie;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\ModuleManager\Feature\ViewHelperProviderInterface;
use Zend\Mvc\MvcEvent;
use Infirmerie\Model\BilanPrelevementTable;
use Zend\Db\ResultSet\ResultSet;
use Infirmerie\Model\BilanPrelevement;
use Zend\Db\TableGateway\TableGateway;
use Infirmerie\Model\CodagePrelevementTable;
use Infirmerie\Model\CodagePrelevement;
use Infirmerie\Model\ConsultationTable;
use Infirmerie\Model\Consultation;
use Infirmerie\Model\MotifAdmissionTable;
use Infirmerie\Model\MotifAdmission;
use Infirmerie\Model\DemandeAnalyseTable;
use Infirmerie\Model\DemandeAnalyse;
use Infirmerie\Model\ListeBilanPrelevementTable;
use Infirmerie\Model\ListeBilanPrelevement;

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
						'Infirmerie\Model\BilanPrelevementTable' => function ($sm) {
							$tableGateway = $sm->get ( 'BilanPrelevementTableGateway' );
							$table = new BilanPrelevementTable( $tableGateway );
							return $table;
						},
						'BilanPrelevementTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new BilanPrelevement () );
							return new TableGateway( 'bilan_prelevement', $dbAdapter, null, $resultSetPrototype );
						},
						'Infirmerie\Model\CodagePrelevementTable' => function ($sm) {
							$tableGateway = $sm->get ( 'CodagePrelevementTableGateway' );
							$table = new CodagePrelevementTable( $tableGateway );
							return $table;
						},
						'CodagePrelevementTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new CodagePrelevement () );
							return new TableGateway( 'codage_prelevement', $dbAdapter, null, $resultSetPrototype );
						},
						'Infirmerie\Model\ConsultationTable' => function ($sm) {
							$tableGateway = $sm->get ( 'ConsultationTableGateway' );
							$table = new ConsultationTable( $tableGateway );
							return $table;
						},
						'ConsultationTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet();
							$resultSetPrototype->setArrayObjectPrototype ( new Consultation());
							return new TableGateway ( 'consultation', $dbAdapter, null, $resultSetPrototype );
						},
						'Infirmerie\Model\MotifAdmissionTable' => function ($sm) {
							$tableGateway = $sm->get ( 'MotifAdmissionTableGateway' );
							$table = new MotifAdmissionTable($tableGateway);
							return $table;
						},
						'MotifAdmissionTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet();
							$resultSetPrototype->setArrayObjectPrototype ( new MotifAdmission());
							return new TableGateway ( 'motif_admission', $dbAdapter, null, $resultSetPrototype );
						},
						'Infirmerie\Model\DemandeAnalyseTable' => function ($sm) {
							$tableGateway = $sm->get ( 'DemandeAnalyseTableGateway' );
							$table = new DemandeAnalyseTable($tableGateway);
							return $table;
						},
						'DemandeAnalyseTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet();
							$resultSetPrototype->setArrayObjectPrototype ( new DemandeAnalyse());
							return new TableGateway ( 'demande_analyse', $dbAdapter, null, $resultSetPrototype );
						},
						'Infirmerie\Model\ListeBilanPrelevementTable' => function ($sm) {
							$tableGateway = $sm->get ( 'ListeBilanPrelevementTableGateway' );
							$table = new ListeBilanPrelevementTable($tableGateway);
							return $table;
						},
						'ListeBilanPrelevementTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );//vue_test_liste_bilans_15000
							$resultSetPrototype = new ResultSet(); //vue_liste_bilan_infirmerie
							$resultSetPrototype->setArrayObjectPrototype ( new ListeBilanPrelevement());
							return new TableGateway ( 'vue_liste_bilan_infirmerie', $dbAdapter, null, $resultSetPrototype );
						},
				)
		);
	}
	
	public function getViewHelperConfig() {
		return array ();
	}
	
	public function onBootstrap(MvcEvent $e) {
		$serviceManager = $e->getApplication ()->getServiceManager ();
		$viewModel = $e->getApplication ()->getMvcEvent ()->getViewModel ();
	
		$uTable = $serviceManager->get( 'Facturation\Model\PatientTable' );
		$nonConformite = $uTable->getExistanceListeNonConforme();

		if($nonConformite) {
			$viewModel->nonConformite = $nonConformite;
		}
	}
}