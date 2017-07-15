<?php

namespace Personnel\Model;

use Zend\Db\TableGateway\TableGateway;

class LaborantinTable {
	protected $tableGateway;
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}
	
	public function getLaborantin($idpersonne)
	{
		$idpersonne  = (int) $idpersonne;
		$rowset = $this->tableGateway->select(array('idemploye' => $idpersonne));
		$row = $rowset->current();
		if (!$row) {
			return null;
		}
		return $row;
	}
	
	public function saveLaborantin(Personnel $personnel, $idpersonnel, $idemploye)
	{
		$today = (new \DateTime ( 'now' ))->format ( 'Y-m-d H:i:s' );
		
 		$data = array(
 				'idemploye' => $idpersonnel,
 				'matricule' => $personnel->matricule_laborantin,
 				'grade' => $personnel->grade_laborantin,
 				'domaine' => $personnel->domaine_laborantin,
 				'autres' => $personnel->autres_laborantin,
    		    'date_enregistrement' => $today,
 				'idpersonne' => $idemploye,
 		);
 		
 		$idpersonne = (int)$personnel->id_personne;
 		if($idpersonne == 0){
 			$this->tableGateway->insert($data);
 		} else {
 			if($this->getLaborantin($idpersonne)) {
 				$data = array_splice($data, 0, -1); //Pour enlever la date d'enregistrement
 				$this->tableGateway->update($data, array('idemploye' => $idpersonne));
 			} else {
 				if($personnel->matricule_laborantin){
 					$this->tableGateway->insert($data);
 				}
 			}
 		}
	}
	
	public function deleteLaborantin($idpersonne){
		$idpersonne = (int) $idpersonne;
	
		if ($this->getLaborantin($idpersonne)) {
			$this->tableGateway->delete( array('idemploye' => $idpersonne));
		} else {
			return null;
		}
	}
}