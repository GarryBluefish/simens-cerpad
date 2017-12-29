<?php

namespace Laboratoire\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

class ResultatsDepistagesTable {
	protected $tableGateway;
 	public function __construct(TableGateway $tableGateway) {
 		$this->tableGateway = $tableGateway;
 	}
 	
 	//LES REQUETTES POUR LA GESTION DES STATISTIQUES A L'INTERFACE DU technicien et du biologiste
 	//LES REQUETTES POUR LA GESTION DES STATISTIQUES A L'INTERFACE DU technicien et du biologiste
 	/**
 	 * Liste des patients dépistés ayant déjà un résultat (renseigné par un technicien)
 	 * validé ou non-validé par un biologiste
 	 */
 	public function getResultatsDepistages(){
 		
 		return $this->tableGateway->select(function (Select $select){
 			$select->join('patient' , 'patient.idpersonne = demande_analyse.idpatient' , array('*'));
 			$select->join('depistage' , 'depistage.idpatient = patient.idpersonne' , array('*'));
 			$select->join('typage_hemoglobine' , 'typage_hemoglobine.idtypage = depistage.typage' , array('*'));
 			
 			$select->join('facturation_demande_analyse' , 'facturation_demande_analyse.iddemande_analyse = demande_analyse.iddemande' , array('*'));
 			$select->join('bilan_prelevement' , 'bilan_prelevement.idfacturation = facturation_demande_analyse.idfacturation' , array('date_heure'));                  
 			$select->order('date_heure asc');
 		})->toArray();
 	}
 	/**
 	 * Liste des patients dépistés ayant déjà un résultat (renseigné par un technicien)
 	 * validé ou non-validé par un biologiste pour une période donnée
 	 */
 	public function getResultatsDepistagesPourUnePeriode($date_debut=null, $date_fin=null){
 	
 		return $this->tableGateway->select(function (Select $select) use ($date_debut, $date_fin){
 			$select->join('patient' , 'patient.idpersonne = demande_analyse.idpatient' , array('*'));
 			$select->join('depistage' , 'depistage.idpatient = patient.idpersonne' , array('*'));
 			$select->join('typage_hemoglobine' , 'typage_hemoglobine.idtypage = depistage.typage' , array('*'));
 	
 			$select->join('facturation_demande_analyse' , 'facturation_demande_analyse.iddemande_analyse = demande_analyse.iddemande' , array('*'));
 			$select->join('bilan_prelevement' , 'bilan_prelevement.idfacturation = facturation_demande_analyse.idfacturation' , array('date_heure'));
 			$select->order('date_heure asc');
 			
 			$select->where(array(
 					'date_prelevement >= ?' => $date_debut,
 					'date_prelevement <= ?' => $date_fin,
 			));
 			
 		})->toArray();
 	}
 	
 	
 	
 	
 	
 	/**
 	 * Liste des patients dépistés ayant déjà un résultat (renseigné par un technicien)
 	 * validé par un biologiste
 	 */
 	public function getResultatsDepistagesValides(){
 			
 		return $this->tableGateway->select(function (Select $select){
 			$select->join('patient' , 'patient.idpersonne = demande_analyse.idpatient' , array('*'));
 			$select->join('depistage' , 'depistage.idpatient = patient.idpersonne' , array('*'));
 			$select->join('typage_hemoglobine' , 'typage_hemoglobine.idtypage = depistage.typage' , array('*'));
 	
 			$select->join('facturation_demande_analyse' , 'facturation_demande_analyse.iddemande_analyse = demande_analyse.iddemande' , array('*'));
 			$select->join('bilan_prelevement' , 'bilan_prelevement.idfacturation = facturation_demande_analyse.idfacturation' , array('date_heure'));
 			$select->order('date_heure asc');
 			$select->where(array('valide' => 1));
 		})->toArray();
 	}
 	/**
 	 * Liste des patients dépistés ayant déjà un résultat (renseigné par un technicien)
 	 * validé ou non-validé par un biologiste et pour une période donnée
 	 */
 	public function getResultatsDepistagesValidesPourUnePeriode($date_debut=null, $date_fin=null){
 	
 		return $this->tableGateway->select(function (Select $select) use ($date_debut, $date_fin){
 			$select->join('patient' , 'patient.idpersonne = demande_analyse.idpatient' , array('*'));
 			$select->join('depistage' , 'depistage.idpatient = patient.idpersonne' , array('*'));
 			$select->join('typage_hemoglobine' , 'typage_hemoglobine.idtypage = depistage.typage' , array('*'));
 	
 			$select->join('facturation_demande_analyse' , 'facturation_demande_analyse.iddemande_analyse = demande_analyse.iddemande' , array('*'));
 			$select->join('bilan_prelevement' , 'bilan_prelevement.idfacturation = facturation_demande_analyse.idfacturation' , array('date_heure'));
 			$select->order('date_heure asc');
 	
 			$select->where(array(
 					'valide' => 1,
 					'date_prelevement >= ?' => $date_debut,
 					'date_prelevement <= ?' => $date_fin,
 			));
 	
 		})->toArray();
 	}
 	
 	
 	
 	
 	
 	/**
 	 * Liste des patients dépistés ayant déjà un résultat (renseigné par un technicien)
 	 * non-validé par un biologiste
 	 */
 	public function getResultatsDepistagesNonValides(){
 	
 		return $this->tableGateway->select(function (Select $select){
 			$select->join('patient' , 'patient.idpersonne = demande_analyse.idpatient' , array('*'));
 			$select->join('depistage' , 'depistage.idpatient = patient.idpersonne' , array('*'));
 			$select->join('typage_hemoglobine' , 'typage_hemoglobine.idtypage = depistage.typage' , array('*'));
 	
 			$select->join('facturation_demande_analyse' , 'facturation_demande_analyse.iddemande_analyse = demande_analyse.iddemande' , array('*'));
 			$select->join('bilan_prelevement' , 'bilan_prelevement.idfacturation = facturation_demande_analyse.idfacturation' , array('date_heure'));
 			$select->order('date_heure asc');
 			$select->where(array('valide' => 0));
 		})->toArray();
 	}
 	/**
 	 * Liste des patients dépistés ayant déjà un résultat (renseigné par un technicien)
 	 * validé ou non-validé par un biologiste et pour une période donnée
 	 */
 	public function getResultatsDepistagesNonValidesPourUnePeriode($date_debut=null, $date_fin=null){
 	
 		return $this->tableGateway->select(function (Select $select) use ($date_debut, $date_fin){
 			$select->join('patient' , 'patient.idpersonne = demande_analyse.idpatient' , array('*'));
 			$select->join('depistage' , 'depistage.idpatient = patient.idpersonne' , array('*'));
 			$select->join('typage_hemoglobine' , 'typage_hemoglobine.idtypage = depistage.typage' , array('*'));
 	
 			$select->join('facturation_demande_analyse' , 'facturation_demande_analyse.iddemande_analyse = demande_analyse.iddemande' , array('*'));
 			$select->join('bilan_prelevement' , 'bilan_prelevement.idfacturation = facturation_demande_analyse.idfacturation' , array('date_heure'));
 			$select->order('date_heure asc');
 	
 			$select->where(array(
 					'valide' => 0,
 					'date_prelevement >= ?' => $date_debut,
 					'date_prelevement <= ?' => $date_fin,
 			));
 	
 		})->toArray();
 	}
 	
 	
 	
 	
 	
 	/**
 	 * Retrouver la première et la dernière date de prélèvement
 	 */
 	
 	public function getMinMaxDateResultatsDepistages(){
 		
 		$listeResultatsDepistages = $this->tableGateway->select(function (Select $select){
 			$select->join('patient' , 'patient.idpersonne = demande_analyse.idpatient' , array('*'));
 			$select->join('depistage' , 'depistage.idpatient = patient.idpersonne' , array('*'));
 			$select->join('typage_hemoglobine' , 'typage_hemoglobine.idtypage = depistage.typage' , array('*'));
 			
 			$select->join('facturation_demande_analyse' , 'facturation_demande_analyse.iddemande_analyse = demande_analyse.iddemande' , array('*'));
 			$select->join('bilan_prelevement' , 'bilan_prelevement.idfacturation = facturation_demande_analyse.idfacturation' , array('date_heure'));                  
 			$select->order('date_heure asc');
 		
 		})->toArray();
 			
 		$tabDatePrelevement = array();
 		for ($i=0 ; $i<count($listeResultatsDepistages) ; $i++){
 			$tabDatePrelevement [] = $listeResultatsDepistages[$i]['date_prelevement'];
 		}
 			
 		return array(min($tabDatePrelevement), max($tabDatePrelevement));
 	}
 	
 	
 	
}

