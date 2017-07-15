<?php

namespace Personnel\Model;

use Zend\Db\TableGateway\TableGateway;

class InfirmierTable {
	protected $tableGateway;
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}
	
	public function getInfirmier($idpersonne)
	{
		$idpersonne  = (int) $idpersonne;
		$rowset = $this->tableGateway->select(array('idemploye' => $idpersonne));
		$row = $rowset->current();
		if (!$row) {
			return null;
		}
		return $row;
	}
	
	public function saveInfirmier(Personnel $personnel, $idpersonnel, $idemploye)
	{
		$today = (new \DateTime ( 'now' ))->format ( 'Y-m-d H:i:s' );
		
 		$data = array(
 				'idemploye' => $idpersonnel,
 				'matricule' => $personnel->matricule_infirmier,
 				'grade' => $personnel->grade_infirmier,
 				'domaine' => $personnel->domaine_infirmier,
 				'autres' => $personnel->autres_infirmier,
    		    'date_enregistrement' => $today,
 				'idpersonne' => $idemploye,
 		);
 		
 		$idpersonne = (int)$personnel->id_personne;
 		if($idpersonne == 0){
 			$this->tableGateway->insert($data);
 		} else {
 			if($this->getInfirmier($idpersonne)) {
 				$data = array_splice($data, 0, -1); //Pour enlever la date d'enregistrement
 				$this->tableGateway->update($data, array('idemploye' => $idpersonne));
 			} else {
 				if($personnel->matricule_infirmier){
 					$this->tableGateway->insert($data);
 				}
 			}
 		}
	}
	
	public function deleteInfirmier($idpersonne){
		$idpersonne = (int) $idpersonne;
	
		if ($this->getInfirmier($idpersonne)) {
			$this->tableGateway->delete( array('idemploye' => $idpersonne));
		} else {
			return null;
		}
	}
}