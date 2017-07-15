<?php

namespace Personnel\Model;

use Zend\Db\TableGateway\TableGateway;
use Facturation\View\Helper\DateHelper;

class InterventionTable {
	protected $tableGateway;
	protected $conversionDate;
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}
	
	public function getConversionDate(){
		$this->conversionDate = new DateHelper();
		
		return $this->conversionDate;
	}
	
	public function getIntervention($numero_intervention)
	{
		$rowset = $this->tableGateway->select(array(
				'numero_intervention' => (int) $numero_intervention,
		));
		$row = $rowset->current();
		if (!$row) {
			$row = null;
		}
		return $row;
	}
	
	public function saveIntervention(Intervention1 $intervention)
	{
		$this->getConversionDate();
		
		$id_verif = (int) $intervention->id_verif;
		
		if($intervention->type_intervention == "Interne"){
			$data = array(
					'id_personne' => $intervention->id_personne,
					'id_service' => $intervention->id_service,
					'date_debut' => $this->conversionDate->convertDateInAnglais($intervention->date_debut),
					'date_fin' => $this->conversionDate->convertDateInAnglais($intervention->date_fin),
					'motif_intervention' => $intervention->motif_intervention,
					'note' => $intervention->note,
					'type_intervention' => $intervention->type_intervention,
			);
		}else if($intervention->type_intervention == "Externe"){
			$data = array(
					'id_personne' => $intervention->id_personne,
					'id_service' => $intervention->id_service_externe,
					'date_debut' => $this->conversionDate->convertDateInAnglais($intervention->date_debut_externe),
					'date_fin' => $this->conversionDate->convertDateInAnglais($intervention->date_fin_externe),
					'motif_intervention' => $intervention->motif_intervention_externe,
					'note' => $intervention->note_externe,
					'type_intervention' => $intervention->type_intervention,
			);
		}
		
		if($id_verif == 0){
			$this->tableGateway->insert($data);
 		} else {
 			if($this->getIntervention($intervention->numero_intervention)) {
 				$this->tableGateway->update($data, array(
 						'numero_intervention' => $intervention->numero_intervention,
 				));
 			} 
 		}
	}
	
	public function deleteIntervention($id_personne){
		$id_personne = (int) $id_personne;

		$this->tableGateway->delete( array('id_personne' => $id_personne));
	}
	
	public function deleteUneIntervention($numero_intervention){
		$numero_intervention = (int) $numero_intervention;
	
		$this->tableGateway->delete( array('numero_intervention' => $numero_intervention));
	}
}