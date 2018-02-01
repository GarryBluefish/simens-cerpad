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
				1  => 'HEMOGRAMME',
				9  => 'TAUX DE RETICULOCYTES (TR)',
				22 => 'CREATININEMIE',
				40 => 'FER SERIQUE',
				41 => 'FERRITININE',
				44 => 'ELECTROPHORESE DE HEMOGLOBINE',
				49 => 'PROTEINURIE DES 24H (PU 24H)'
		);
	}
	
	public function getAnalyseEffectuees($idpatient){
		$listeAnalysesEffectuees = $this->tableGateway->select(function (Select $select) use ($idpatient){
 			$select->join('demande_analyse' , 'demande_analyse.idanalyse = analyse.idanalyse' , array('*'));
 			$select->where(array('demande_analyse.idpatient' => $idpatient));
 		})->toArray();
 		
 		/*
 		 * Recuperer les analyses demandees, faites et validees faisant partie des analyses ciblées
 		 * (A revoir avec PROFESSEUR)
 		 */
 		$tabAnalysesObligatoireAFaire = array(1,9,22,40,41,44,49);
 		$tabIndexAnalyses = array();
 		$tabAnalyses = array();
 		for($i = 0 ; $i < count($listeAnalysesEffectuees) ; $i++){
 			$idanalyse = $listeAnalysesEffectuees[$i]['idanalyse'];
 			if(in_array($idanalyse, $tabAnalysesObligatoireAFaire)){
 				$tabIndexAnalyses[] = $listeAnalysesEffectuees[$i]['idanalyse'];
 				$tabAnalyses[] = $listeAnalysesEffectuees[$i]['designation'];
 			}
 		}
 		
 		/*
 		 * Recuperer les analyses non faites par le patient
 		 */
 		$tabIndexAnalysesNonFaits = array_values(array_diff($tabAnalysesObligatoireAFaire, $tabIndexAnalyses));
 		$tabAnalysesNonFaits = array();
 		for($i = 0 ; $i < count($tabIndexAnalysesNonFaits) ; $i++){
 			$tabAnalysesNonFaits[] = $this->getListeAnalyseAFaire()[$tabIndexAnalysesNonFaits[$i]];
 		}
 		
 		//var_dump($tabIndexAnalysesNonFaits); exit();
 		
 		
 		return array($tabIndexAnalyses, $tabAnalyses, $tabIndexAnalysesNonFaits, $tabAnalysesNonFaits);
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