<?php

namespace Personnel\Model;

use Zend\Db\TableGateway\TableGateway;

class SecretaireTable {
	protected $tableGateway;
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}
	
	public function getSecretaire($idpersonne)
	{
		$idpersonne  = (int) $idpersonne;
		$rowset = $this->tableGateway->select(array('idemploye' => $idpersonne));
		$row = $rowset->current();
		if (!$row) {
			return null;
		}
		return $row;
	}
	
	public function saveSecretaire(Personnel $personnel, $idpersonnel, $idemploye)
	{
		$today = (new \DateTime ( 'now' ))->format ( 'Y-m-d H:i:s' );
		
 		$data = array(
 				'idemploye' => $idpersonnel,
 				'matricule' => $personnel->matricule_secretaire,
 				'grade' => $personnel->grade_secretaire,
 				'domaine' => $personnel->domaine_secretaire,
 				'autres' => $personnel->autres_secretaire,
    		    'date_enregistrement' => $today,
 				'idpersonne' => $idemploye,
 		);
 		
 		$idpersonne = (int)$personnel->id_personne;
 		if($idpersonne == 0){
 			$this->tableGateway->insert($data);
 		} else {
 			if($this->getSecretaire($idpersonne)) {
 				$data = array_splice($data, 0, -1); //Pour enlever la date d'enregistrement
 				$this->tableGateway->update($data, array('idemploye' => $idpersonne));
 			} else {
 				if($personnel->matricule_secretaire){
 					$this->tableGateway->insert($data);
 				}
 			}
 		}
	}
	
	public function deleteSecretaire($idpersonne){
		$idpersonne = (int) $idpersonne;
	
		if ($this->getSecretaire($idpersonne)) {
			$this->tableGateway->delete( array('idemploye' => $idpersonne));
		} else {
			return null;
		}
	}
}