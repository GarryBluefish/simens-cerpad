<?php

namespace Personnel\Model;

use Zend\Db\TableGateway\TableGateway;
use Facturation\View\Helper\DateHelper;
use Zend\Db\Sql\Sql;

class TransfertTable {
	protected $tableGateway;
	protected $conversionDate;
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}
	
	public function getConversionDate(){
		$this->conversionDate = new DateHelper();
		
		return $this->conversionDate;
	}
	
	public function getTransfert($data)
	{
		$rowset = $this->tableGateway->select(array(
				'id_personne' => (int) $data['id_personne'],
				'id_service_origine'=> $data['id_service_origine'],
				//'id_service_accueil'=> $data['id_service_accueil'],
		));
		$row = $rowset->current();
		if (!$row) {
			$row = null;
		}
		return $row;
	}
	
	public function saveTransfert(Transfert1 $transfert, $donneesPlus)
	{

		$today = new \DateTime ( 'now' );
		$date = $today->format ( 'Y-m-d' );
		
		$id_verif = (int) $transfert->id_verif;
		
		if($transfert->type_transfert == "Interne"){
			$data = array(
					'id_personne' => $transfert->id_personne,
					'id_service_origine' => $donneesPlus['id_service_origine']['ID_SERVICE'],
					'id_service_accueil' => $transfert->service_accueil,
					'motif_transfert' => $transfert->motif_transfert,
					'note' => $transfert->note,
					'type_transfert' => $transfert->type_transfert,
					'date_debut' => $date
			);
		}else if($transfert->type_transfert == "Externe"){
			$data = array(
					'id_personne' => $transfert->id_personne,
					'id_service_origine' => $donneesPlus['id_service_origine']['ID_SERVICE'],
					'id_service_accueil' => $donneesPlus['service_accueil_externe'],
					'motif_transfert' => $transfert->motif_transfert_externe,
					'type_transfert' => $transfert->type_transfert,
					'date_debut' => $date
			);
		}
		if($id_verif == 0){
			$this->tableGateway->insert($data);
 		} else {
 			if($this->getTransfert($data)) {
 				$this->tableGateway->update($data, array(
 						'id_personne' => $data['id_personne'],
 						'id_service_origine'=> $data['id_service_origine'],
 						//'id_service_accueil'=> $data['id_service_accueil'],
 				));
 			} 
 		}
	}
	
	public function deleteTransfert($id_personne){
		$id_personne = (int) $id_personne;

		$this->tableGateway->delete( array('id_personne' => $id_personne));
	}
}