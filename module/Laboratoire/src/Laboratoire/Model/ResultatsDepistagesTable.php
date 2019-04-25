<?php

namespace Laboratoire\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Predicate\NotIn;

class ResultatsDepistagesTable {
	protected $tableGateway;
 	public function __construct(TableGateway $tableGateway) {
 		$this->tableGateway = $tableGateway;
 	}
 	
 	//LES REQUETTES POUR LA GESTION DES STATISTIQUES SUR L'INTERFACE DU technicien et du biologiste
 	//LES REQUETTES POUR LA GESTION DES STATISTIQUES SUR L'INTERFACE DU technicien et du biologiste
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
 			$select->order('date_heure desc');
 			
 			$select->where(array('demande_analyse.idanalyse' => 68));
 			
 		})->toArray();
 	}
 	
 	/**
 	 * Liste des patients dépistés n'ayant pas de résultat (renseigné par un technicien)
 	 */
 	public function getPatientsDepistagesSansResultat(){
 		
 		$sql = new Sql ($this->tableGateway->getAdapter());
 		$subselect = $sql->select ();
 		$subselect->from ( array ( 'rda' => 'resultat_demande_analyse' ) );
 		$subselect->columns (array ( 'iddemande_analyse' ) );
 		$resultat = $sql->prepareStatementForSqlObject($subselect)->execute();
 		$donneesResultat = array();
 		foreach ($resultat as $result){ $donneesResultat[] = $result['iddemande_analyse']; }
 		
 		
 		return $this->tableGateway->select(function (Select $select) use ($donneesResultat){
 			$select->join('patient' , 'patient.idpersonne = demande_analyse.idpatient' , array('*'));
 			$select->join('depistage' , 'depistage.idpatient = patient.idpersonne' , array('*'));
 	
 			$select->join('facturation_demande_analyse' , 'facturation_demande_analyse.iddemande_analyse = demande_analyse.iddemande' , array('*'));
 			$select->join('bilan_prelevement' , 'bilan_prelevement.idfacturation = facturation_demande_analyse.idfacturation' , array('date_heure'));
 			$select->where(array('demande_analyse.idanalyse' => 68,  new NotIn ( 'demande_analyse.iddemande', $donneesResultat )));
 			$select->order('date_heure desc');
 			
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
 			$select->order('date_heure desc');
 			
 			$select->where(array(
 					'date_prelevement >= ?' => $date_debut,
 					'date_prelevement <= ?' => $date_fin,
 					'demande_analyse.idanalyse' => 68,
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
 			$select->order('date_heure desc');
 			$select->where(array('demande_analyse.idanalyse' => 68, 'valide' => 1));
 			
 		})->toArray();
 	}
 	/**
 	 * Liste des patients dépistés ayant déjà un résultat (renseigné par un technicien)
 	 * validé par un biologiste et pour une période donnée
 	 */
 	public function getResultatsDepistagesValidesPourUnePeriode($date_debut=null, $date_fin=null){
 	
 		return $this->tableGateway->select(function (Select $select) use ($date_debut, $date_fin){
 			$select->join('patient' , 'patient.idpersonne = demande_analyse.idpatient' , array('*'));
 			$select->join('depistage' , 'depistage.idpatient = patient.idpersonne' , array('*'));
 			$select->join('typage_hemoglobine' , 'typage_hemoglobine.idtypage = depistage.typage' , array('*'));
 	
 			$select->join('facturation_demande_analyse' , 'facturation_demande_analyse.iddemande_analyse = demande_analyse.iddemande' , array('*'));
 			$select->join('bilan_prelevement' , 'bilan_prelevement.idfacturation = facturation_demande_analyse.idfacturation' , array('date_heure'));
 			$select->order('date_heure desc');
 	
 			$select->where(array(
 					'valide' => 1,
 					'date_prelevement >= ?' => $date_debut,
 					'date_prelevement <= ?' => $date_fin,
 					'demande_analyse.idanalyse' => 68,
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
 			$select->order('date_heure desc');
 			$select->where(array('demande_analyse.idanalyse' => 68, 'valide' => 0));
 			
 		})->toArray();
 	}
 	/**
 	 * Liste des patients dépistés ayant déjà un résultat (renseigné par un technicien)
 	 * non-validé par un biologiste et pour une période donnée
 	 */
 	public function getResultatsDepistagesNonValidesPourUnePeriode($date_debut=null, $date_fin=null){
 	
 		return $this->tableGateway->select(function (Select $select) use ($date_debut, $date_fin){
 			$select->join('patient' , 'patient.idpersonne = demande_analyse.idpatient' , array('*'));
 			$select->join('depistage' , 'depistage.idpatient = patient.idpersonne' , array('*'));
 			$select->join('typage_hemoglobine' , 'typage_hemoglobine.idtypage = depistage.typage' , array('*'));
 	
 			$select->join('facturation_demande_analyse' , 'facturation_demande_analyse.iddemande_analyse = demande_analyse.iddemande' , array('*'));
 			$select->join('bilan_prelevement' , 'bilan_prelevement.idfacturation = facturation_demande_analyse.idfacturation' , array('date_heure'));
 			$select->order('date_heure desc');
 	
 			$select->where(array(
 					'valide' => 0,
 					'date_prelevement >= ?' => $date_debut,
 					'date_prelevement <= ?' => $date_fin,
 					'demande_analyse.idanalyse' => 68,
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
 			$select->order('date_heure desc');
 			
 			$select->where(array('demande_analyse.idanalyse' => 68));
 		})->toArray();
 			
 		$tabDatePrelevement = array();
 		for ($i=0 ; $i<count($listeResultatsDepistages) ; $i++){
 			$tabDatePrelevement [] = $listeResultatsDepistages[$i]['date_prelevement'];
 		}
 			
 		return array(min($tabDatePrelevement), max($tabDatePrelevement));
 	}
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	/**
 	 * Liste des patients dépistés ayant déja un résultat (renseigné par un technicien)
 	 * pour un profil et une période donné
 	 */
 	public function getListeNumeroDossierPatientsDepistagesAvecResultat($profil, $date_debut, $date_fin, $typeInfos){
 			
 		if($typeInfos == 0){
 			return $this->getListeNumeroDossierPatientsDepistagesTR($profil, $date_debut, $date_fin);
 		}else if($typeInfos == 1){
 			return $this->getListeNumeroDossierPatientsDepistagesRV($profil, $date_debut, $date_fin);
 		}else if($typeInfos == 2){
 			return $this->getListeNumeroDossierPatientsDepistagesRX($profil, $date_debut, $date_fin);
 		}
 	}
 	
 	
 	/*
 	 * Liste des patients dépistés ayant déja un résultat (TR)
 	 */
 	public function getListeNumeroDossierPatientsDepistagesTR($profil, $date_debut, $date_fin){
 	
 		$result = $this->tableGateway->select(function (Select $select) use ($profil, $date_debut, $date_fin){
 			$select->join('patient' , 'patient.idpersonne = demande_analyse.idpatient' , array('*'));
 			$select->join('depistage' , 'depistage.idpatient = patient.idpersonne' , array('*'));
 			$select->join('typage_hemoglobine' , 'typage_hemoglobine.idtypage = depistage.typage' , array('*'));
 	
 			$select->join('facturation_demande_analyse' , 'facturation_demande_analyse.iddemande_analyse = demande_analyse.iddemande' , array('*'));
 			$select->join('bilan_prelevement' , 'bilan_prelevement.idfacturation = facturation_demande_analyse.idfacturation' , array('date_heure'));
 			$select->where(array(
 					'date_prelevement >= ?' => $date_debut,
 					'date_prelevement <= ?' => $date_fin,
 					'designation' => $profil,
 					'demande_analyse.idanalyse' => 68
 			));
 			$select->order('demande_analyse.idpatient asc');
 	
 		})->toArray();
 			
 		$numDossierChaine = "";
 		for($i=0 ; $i<count($result) ; $i++){
 			$numDossierChaine .= $result[$i]['numero_dossier'].",";
 		}
 			
 		return $numDossierChaine;
 	}
 	
 	/*
 	 * Liste des patients dépistés ayant déja un résultat (RV)
  	 */
 	public function getListeNumeroDossierPatientsDepistagesRV($profil, $date_debut, $date_fin){
 	
 		$result = $this->tableGateway->select(function (Select $select) use ($profil, $date_debut, $date_fin){
 			$select->join('patient' , 'patient.idpersonne = demande_analyse.idpatient' , array('*'));
 			$select->join('depistage' , 'depistage.idpatient = patient.idpersonne' , array('*'));
 			$select->join('typage_hemoglobine' , 'typage_hemoglobine.idtypage = depistage.typage' , array('*'));
 	
 			$select->join('facturation_demande_analyse' , 'facturation_demande_analyse.iddemande_analyse = demande_analyse.iddemande' , array('*'));
 			$select->join('bilan_prelevement' , 'bilan_prelevement.idfacturation = facturation_demande_analyse.idfacturation' , array('date_heure'));
 			$select->where(array(
 					'valide' => 1,
 					'date_prelevement >= ?' => $date_debut,
 					'date_prelevement <= ?' => $date_fin,
 					'designation' => $profil,
 					'demande_analyse.idanalyse' => 68
 			));
 			$select->order('demande_analyse.idpatient asc');
 	
 		})->toArray();
 	
 		$numDossierChaine = "";
 		for($i=0 ; $i<count($result) ; $i++){
 			$numDossierChaine .= $result[$i]['numero_dossier'].",";
 		}
 	
 		return $numDossierChaine;
 	}
 	
 	/*
 	 * Liste des patients dépistés ayant déja un résultat (RX)
 	 */
 	public function getListeNumeroDossierPatientsDepistagesRX($profil, $date_debut, $date_fin){
 	
 		$result = $this->tableGateway->select(function (Select $select) use ($profil, $date_debut, $date_fin){
 			$select->join('patient' , 'patient.idpersonne = demande_analyse.idpatient' , array('*'));
 			$select->join('depistage' , 'depistage.idpatient = patient.idpersonne' , array('*'));
 			$select->join('typage_hemoglobine' , 'typage_hemoglobine.idtypage = depistage.typage' , array('*'));
 	
 			$select->join('facturation_demande_analyse' , 'facturation_demande_analyse.iddemande_analyse = demande_analyse.iddemande' , array('*'));
 			$select->join('bilan_prelevement' , 'bilan_prelevement.idfacturation = facturation_demande_analyse.idfacturation' , array('date_heure'));
 			$select->where(array(
 					'valide' => 0,
 					'date_prelevement >= ?' => $date_debut,
 					'date_prelevement <= ?' => $date_fin,
 					'designation' => $profil,
 					'demande_analyse.idanalyse' => 68
 			));
 			$select->order('demande_analyse.idpatient asc');
 	
 		})->toArray();
 	
 		$numDossierChaine = "";
 		for($i=0 ; $i<count($result) ; $i++){
 			$numDossierChaine .= $result[$i]['numero_dossier'].",";
 		}
 	
 		return $numDossierChaine;
 	}
 	
 	
 	
 	
 	
 	
}

