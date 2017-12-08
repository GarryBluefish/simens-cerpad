<?php

namespace Infirmerie\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

class DemandeAnalyseTable {
	protected $tableGateway;
 	public function __construct(TableGateway $tableGateway) {
 		$this->tableGateway = $tableGateway;
 	}
 	
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
 	
 	public function getMinMaxDateDemandeAnalyse(){
 		$listeDemande = $this->tableGateway->select(function (Select $select){
 			$select->join('depistage' , 'depistage.idpatient = demande_analyse.idpatient' , array('*'));
 			$select->join('patient' , 'patient.idpersonne = depistage.idpatient' , array('*'));
 			$select->join('personne' , 'personne.idpersonne = patient.idpersonne' , array('date_naissance'));
 			$select->order('date_naissance asc');
 		})->toArray();
 		
 		$tabDateNaissance = array();
 		for ($i=0 ; $i<count($listeDemande) ; $i++){
 			$tabDateNaissance [] = $listeDemande[$i]['date_naissance'];
 		}
 		
 		return array(min($tabDateNaissance), max($tabDateNaissance));
 	}
}


