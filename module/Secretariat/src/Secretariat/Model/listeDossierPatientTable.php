<?php

namespace Secretariat\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;

class listeDossierPatientTable {
	protected $tableGateway;
	
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}
	
	public function fetchAll() {
		
		//Tous les patients dépistés n'ayant pas encore de demandes d'analyses
		$sql = new Sql ($this->tableGateway->getAdapter());
		$subselect1 = $sql->select ()->from ( array ( 'p' => 'patient' ) )->columns(array('idpersonne'));
		$subselect1->join(array('da'=>'demande_analyse'), 'da.idpatient = p.idpersonne', array());
		
		
		
		//Tous les patients dépistes Sans demande et Avec au moins une demande 
		$listePatientsDepistesSansDemande = $this->tableGateway->select(function (Select $select) use ($subselect1){
 			$select->join('patient' , 'patient.idpersonne = personne.idpersonne' , array('numero_dossier'));
 			$select->join('depistage' , 'depistage.idpatient = patient.idpersonne' , array('typepatient'));
 			$select->where->notIn('patient.idpersonne', $subselect1);
		})->toArray();
		
		$listePatientsDepistesAvecDemande = $this->tableGateway->select(function (Select $select){
			$select->join('patient' , 'patient.idpersonne = personne.idpersonne' , array('numero_dossier'));
			$select->join('depistage' , 'depistage.idpatient = patient.idpersonne' , array('typepatient'));
			$select->join('demande_analyse' , 'demande_analyse.idpatient = patient.idpersonne', array('iddemande'));
			$select->group('patient.idpersonne');
		})->toArray();
		
		
		
		
		//Tous les patients externes Sans demande et Avec au moins une demande 
		$sql = new Sql ($this->tableGateway->getAdapter());
		$subselect2 = $sql->select ()->from ( array ( 'p' => 'patient' ) )->columns(array('idpersonne'));
		$subselect2->join(array('d'=>'depistage'), 'd.idpatient = p.idpersonne', array());
		
		$listePatientsExernesSansDemande = $this->tableGateway->select(function (Select $select) use ($subselect2, $subselect1){
			$select->join('patient' , 'patient.idpersonne = personne.idpersonne' , array('numero_dossier'));
			$select->where->notIn('patient.idpersonne', $subselect2);
			$select->where->notIn('patient.idpersonne', $subselect1);
		})->toArray();
		
		$listePatientsExernesAvecDemande = $this->tableGateway->select(function (Select $select) use ($subselect2, $subselect1){
			$select->join('patient' , 'patient.idpersonne = personne.idpersonne' , array('numero_dossier'));
			$select->join('demande_analyse' , 'demande_analyse.idpatient = patient.idpersonne', array('iddemande'));
			$select->where->notIn('patient.idpersonne', $subselect2);
			$select->group('patient.idpersonne');
		})->toArray();
		

		
		$listeElements = array_merge($listePatientsDepistesSansDemande, $listePatientsExernesSansDemande, $listePatientsDepistesAvecDemande, $listePatientsExernesAvecDemande);
		return array(
				'iTotalDisplayRecords' => count($listeElements),
				'aaData' => $listeElements,
		);
	}
	
	
}