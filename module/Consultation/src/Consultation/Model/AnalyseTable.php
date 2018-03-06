<?php
namespace Consultation\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

class AnalyseTable {

	protected $tableGateway;
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}
	
	/**
	 * Liste des analyses à faire obligatoirement par un patient interne
	 */
	public function getListeAnalyseAFaire(){
		return array(
				1  => array(1, 'HEMOGRAMME', '5000'),
				9  => array(1, 'TAUX DE RETICULOCYTES (TR)', '2000'),
				22 => array(2, 'CREATININEMIE', '2500'),
				40 => array(2, 'FER SERIQUE', '5000'),
				41 => array(2, 'FERRITININE', '10000'),
				44 => array(2, 'ELECTROPHORESE DE HEMOGLOBINE', '15000'),
				49 => array(2, 'PROTEINURIE DES 24H (PU 24H)', '15000'),
				69 => array(2, 'MICRO-ALBUMINURIE', '0'),
				70 => array(2, 'LDH', '0'),
		);
	}
	
	public function getAnalyseEffectuees($idpatient){
		$listeAnalysesEffectuees = $this->tableGateway->select(function (Select $select) use ($idpatient){
 			$select->join('demande_analyse' , 'demande_analyse.idanalyse = analyse.idanalyse' , array('*'));
 			$select->where(array('demande_analyse.idpatient' => $idpatient));
 			$select->group('analyse.idanalyse');
 		})->toArray();
 		
 		/*
 		 * Recuperer les analyses demandees, faites et validees faisant partie des analyses ciblées
 		 * (A revoir avec PROFESSEUR)
 		 */
 		$tabAnalysesObligatoireAFaire = array(1,9,22,40,41,44,49,69,70);
 		$tabIndexAnalysesFaites = array();
 		$tabAnalysesFaites = array();
 		for($i = 0 ; $i < count($listeAnalysesEffectuees) ; $i++){
 			$idanalyse = $listeAnalysesEffectuees[$i]['idanalyse'];
 			if(in_array($idanalyse, $tabAnalysesObligatoireAFaire)){
 				$tabIndexAnalysesFaites[] = $listeAnalysesEffectuees[$i]['idanalyse'];
 				$tabAnalysesFaites[] = $listeAnalysesEffectuees[$i]['designation'];
 			}
 		}
 		
 		/*
 		 * Recuperer les analyses non faites par le patient
 		 */
 		$tabIndexAnalysesNonFaits = array_values(array_diff($tabAnalysesObligatoireAFaire, $tabIndexAnalysesFaites));
 		$tabTypesAnalysesFaites = array();
 		$tabAnalysesNonFaits = array();
 		$tabTarifAnalysesFaites = array();
 		for($i = 0 ; $i < count($tabIndexAnalysesNonFaits) ; $i++){
 			$tabTypesAnalysesFaites[] = $this->getListeAnalyseAFaire()[$tabIndexAnalysesNonFaits[$i]][0];
 			$tabAnalysesNonFaits[] = $this->getListeAnalyseAFaire()[$tabIndexAnalysesNonFaits[$i]][1];
 			$tabTarifAnalysesFaites[] = $this->getListeAnalyseAFaire()[$tabIndexAnalysesNonFaits[$i]][2]; 
 		}
 		
 		//var_dump($tabIndexAnalysesNonFaits); exit();
 		
 		
 		return array($tabIndexAnalysesFaites, $tabAnalysesFaites, $tabIndexAnalysesNonFaits, $tabAnalysesNonFaits, $tabTypesAnalysesFaites, $tabTarifAnalysesFaites);
	}
	
	
}






































/*
 
 public function getDemandeAnalyse(){
 		return $this->tableGateway->select(function (Select $select){
 			$select->join('depistage' , 'depistage.idpatient = demande_analyse.idpatient' , array('*'));
 			$select->join('patient' , 'patient.idpersonne = depistage.idpatient' , array('*'));
 			$select->join('personne' , 'personne.idpersonne = patient.idpersonne' , array('date_naissance'));
 			$select->order('date_naissance asc');
 		})->toArray();
 	}
 	
 	public function getDemandeAnalyseParPeriode($date_debut, $date_fin){
 		return $this->tableGateway->select(function (Select $select) use ($date_debut, $date_fin){
 			$select->join('depistage' , 'depistage.idpatient = demande_analyse.idpatient' , array('*'));
 			$select->join('patient' , 'patient.idpersonne = depistage.idpatient' , array('*'));
 			$select->join('personne' , 'personne.idpersonne = patient.idpersonne' , array('date_naissance'));
 			$select->order('date_naissance asc');
 			$select->where(array(
					'date_naissance >= ?' => $date_debut,
					'date_naissance <= ?' => $date_fin,
			));
 		})->toArray();
 	}
 
 
 */