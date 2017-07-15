<?php

namespace Personnel\Model;

use Zend\Db\TableGateway\TableGateway;
use Facturation\View\Helper\DateHelper;
use Zend\Db\Sql\Sql;

class AffectationTable {
	protected $tableGateway;
	protected $conversionDate;
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}
	
	public function getConversionDate(){
		$this->conversionDate = new DateHelper();
		
		return $this->conversionDate;
	}
	
	public function getAffectation($idpersonne)
	{
		$idpersonne  = (int) $idpersonne;
		$rowset = $this->tableGateway->select(array('idemploye' => $idpersonne));
		$row = $rowset->current();
		if (!$row) {
			$row = null;
		}
		return $row;
	}
	
	public function modifierServiceEmploye($idemploye, $idservice){
		$db = $this->tableGateway->getAdapter();
		$sql = new Sql($db);
		
		$donnees = array( 'idservice' => $idservice );
		
		$sQuery = $sql->update()
		->table('service_employe')
		->set( $donnees )->where(array('idemploye' => $idemploye ));

		$sql->prepareStatementForSqlObject($sQuery)->execute();
	}
	
	
	public function saveAffectation(Personnel $personnel, $idpersonnel, $idemploye)
	{
		$today = (new \DateTime ( 'now' ))->format ( 'Y-m-d H:i:s' );
		$this->getConversionDate();
		
		$date_debut = $personnel->date_debut;
		if($date_debut){ $date_debut = $this->conversionDate->convertDateInAnglais($date_debut); } else { $date_debut = null; }
		
		$date_fin = $personnel->date_fin;
		if($date_fin){ $date_fin = $this->conversionDate->convertDateInAnglais($date_fin); } else { $date_fin = null; }
		
 		$data = array(
 				'idemploye' => $idpersonnel,
 				'idservice' => $personnel->service_accueil,
 				'date_debut' => $date_debut,
 				'date_fin' => $date_fin,
 				'numero_os' => $personnel->numero_os,
 				'date_enregistrement' => $today,
    		    'idpersonne' => $idemploye,
 		);
 		
 		$idpersonne = (int)$personnel->id_personne;
 		if($idpersonne == 0){
 			$this->tableGateway->insert($data);
 		} else {
 			if($this->getAffectation($idpersonne)) {
 				$data = array_splice($data, 0, -1); //Pour enlever la date d'enregistrement
 				$this->tableGateway->update($data, array('idemploye' => $idpersonne));
 				$this->modifierServiceEmploye($idpersonnel, $personnel->service_accueil);
 			}
 		}
	}
	
	public function deleteAffectation($idpersonne){
		$idpersonne = (int) $idpersonne;
	
		if ($this->getAffectation($idpersonne)) {
			$this->tableGateway->delete( array('idemploye' => $idpersonne));
		} else {
			return null;
		}
	}
	
	/*
	 * Recuperer le service ou l'agent est affecte
	 */
	public function getServiceAgentAffecter($idpersonne){
		$idpersonne = (int) $idpersonne;
		
		$row = $this->getAffectation($idpersonne);
		
		if ($row) {
			return $row->idservice;
		} else {
			return null;
		}
	}
}