<?php

namespace Personnel\Model;

use Zend\Db\TableGateway\TableGateway;

class LogistiqueTable {
	protected $tableGateway;
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}
	
	public function getLogistique($idpersonne)
	{
		$idpersonne  = (int) $idpersonne;
		$rowset = $this->tableGateway->select(array('idemploye' => $idpersonne));
		$row = $rowset->current();
		if (!$row) {
			return null;
		}
		return $row;
	}
	
	public function saveLogistique(Personnel $personnel, $idpersonnel, $idemploye)
	{
		$today = (new \DateTime ( 'now' ))->format ( 'Y-m-d H:i:s' );
		
 		$data = array(
 				'idemploye' => $idpersonnel,
 				'matricule' => $personnel->matricule_logistique,
 				'grade' => $personnel->grade_logistique,
 				'domaine' => $personnel->domaine_logistique,
 				'autres' => $personnel->autres_logistique,
 				'idpersonne' => $idemploye,
 				'date_enregistrement' => $today,
 		);
 		
 		$idpersonne = (int)$personnel->id_personne;
 		if($idpersonne == 0){
 			$this->tableGateway->insert($data);
 		} else {
 			if($this->getLogistique($idpersonne)) {
 				$data = array_splice($data, 0, -1); //Pour enlever la date d'enregistrement
 				$this->tableGateway->update($data, array('idemploye' => $idpersonne));
 			} else {
 				if($personnel->matricule_logistique){
 					$this->tableGateway->insert($data);
 				}
 			}
 		}
	}
	
	public function deleteLogistique($idpersonne){
		$idpersonne = (int) $idpersonne;
	
		if ($this->getLogistique($idpersonne)) {
			$this->tableGateway->delete( array('idemploye' => $idpersonne));
		} else {
			return null;
		}
	}
}