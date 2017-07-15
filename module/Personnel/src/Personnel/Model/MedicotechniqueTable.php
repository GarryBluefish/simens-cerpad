<?php

namespace Personnel\Model;

use Zend\Db\TableGateway\TableGateway;

class MedicotechniqueTable {
	protected $tableGateway;
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}
	
	public function getMedicoTechnique($idpersonne)
	{
		$idpersonne  = (int) $idpersonne;
		$rowset = $this->tableGateway->select(array('idemploye' => $idpersonne));
		$row = $rowset->current();
		if (!$row) {
			return null;
		}
		return $row;
	}
	
	public function saveMedicoTechnique(Personnel $personnel, $idpersonnel, $idemploye)
	{
		$today = (new \DateTime ( 'now' ))->format ( 'Y-m-d H:i:s' );
		
 		$data = array(
 				'idemploye' => $idpersonnel,
 				'matricule' => $personnel->matricule_medico,
 				'grade' => $personnel->grade_medico,
 				'domaine' => $personnel->domaine_medico,
 				'autres' => $personnel->autres,
 				'idpersonne' => $idemploye,
 				'date_enregistrement' => $today,
 		);
 		
 		$idpersonne = (int)$personnel->id_personne;
 		if($idpersonne == 0){
 			$this->tableGateway->insert($data);
 		} else {
 			if($this->getMedicoTechnique($idpersonne)) {
 				$data = array_splice($data, 0, -1); //Pour enlever la date d'enregistrement
 				$this->tableGateway->update($data, array('idemploye' => $idpersonne));
 			} else if($personnel->matricule_medico){
 					$this->tableGateway->insert($data);
 				}
 		}
 		
	}
	
	public function deleteMedicoTechnique($idpersonne){
		$idpersonne = (int) $idpersonne;
	
		if ($this->getMedicoTechnique($idpersonne)) {
			$this->tableGateway->delete( array('idemploye' => $idpersonne));
		} else {
			return null;
		}
	}
}