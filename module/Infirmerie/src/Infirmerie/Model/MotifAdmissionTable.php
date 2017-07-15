<?php
namespace Infirmerie\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
class MotifAdmissionTable{
	protected $tableGateway;
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}
	public function getMotifAdmission($id){
		$adapter = $this->tableGateway->getAdapter();
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from('motif_admission');
		$select->columns(array('*'));
		$select->where(array('idcons'=>$id));
		$select->order('idmotif ASC');
		$result = $sql->prepareStatementForSqlObject($select)->execute();
		return $result;
	}
	public function nbMotifs($id){
		$adapter = $this->tableGateway->getAdapter();
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from('motif_admission');
		$select->columns(array('idmotif'));
		$select->where(array('idcons'=>$id));
		$stat = $sql->prepareStatementForSqlObject($select);
		$result = $stat->execute()->count();
		return $result;
	}
	
	public function deleteMotifAdmission($id){
		$this->tableGateway->delete(array('idcons'=>$id));
	}
	
	public function getSiegeMotifDouleur($idsiege){
		$adapter = $this->tableGateway->getAdapter();
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from('liste_siege');
		$select->where(array('idsiege' => $idsiege));
		$result = $sql->prepareStatementForSqlObject($select)->execute()->current();
	
		return $result;
	}
	
	public function getMotifDouleurPrecision($idcons){
		$adapter = $this->tableGateway->getAdapter();
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from('motif_douleur_precision');
		$select->where(array('idcons' => $idcons));
		$result = $sql->prepareStatementForSqlObject($select)->execute()->current();
	
		return $result;
	}
	
	public function getSiegeDouleur($nomSiege){
		$adapter = $this->tableGateway->getAdapter();
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from('liste_siege');
		$select->where(array('libelle' => $nomSiege));
		$result = $sql->prepareStatementForSqlObject($select)->execute()->current();
	
		return $result;
	}
	
	public function insertMotifDouleurPrecision($idcons, $siege, $intensite, $idemploye){
		$adapter = $this->tableGateway->getAdapter();
		$sql = new Sql($adapter);
		
		$idSiege = null;
		if($this->getSiegeDouleur($siege)){
			$infossiege = $this->getSiegeDouleur($siege);
			$idSiege = $infossiege['idsiege'];
		}
		
		if($idSiege) {
			$donnees = array(
					'idcons'    => $idcons,
					'siege'     => $idSiege,
					'intensite' => $intensite,
					'idemploye' => $idemploye,
			);
			
			$sQuery = $sql->insert() ->into('motif_douleur_precision') ->values( $donnees );
			$sql->prepareStatementForSqlObject($sQuery)->execute();
		}

	}
	
	public function getMotifConsultation($nomMotif){
		$adapter = $this->tableGateway->getAdapter();
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from('liste_motif_consultation');
		$select->where(array('libelle' => $nomMotif));
		$result = $sql->prepareStatementForSqlObject($select)->execute()->current();
	
		return $result;
	}
	
	public function addMotifAdmission($values, $idemploye){
		
		for($i=1 ; $i<=5; $i++){

			if($values->get ( 'motif_admission'.$i )->getValue ()){ 
				$idMotif = null;
				$libelle_motif = $values->get ( 'motif_admission'.$i )->getValue ();
				if($this->getMotifConsultation($libelle_motif)){
					$infosmotif = $this->getMotifConsultation($libelle_motif);
					$idMotif = $infosmotif['idmotifconsultation'];
				}
				
				if($idMotif){
					$idcons    = $values->get ( 'idcons' )->getValue ();
					$siege     = $values->get ( 'siege' )->getValue ();
					$intensite = $values->get ( 'intensite' )->getValue ();
					$datamotifadmission	 = array(
							'idcons' => $idcons,
							'idlistemotif' => $idMotif,
							'idemploye' => $idemploye,
					);
					$this->tableGateway->insert($datamotifadmission);
					
					if($idMotif == 2){ $this->insertMotifDouleurPrecision($idcons, $siege, $intensite, $idemploye); }
					
				}
				
			}

		}
		
	}
	
	
	public function updateMotifAdmission($values, $idemploye){
	
		$idcons = $values->get ( 'idcons' )->getValue ();
		
		$this->deleteMotifAdmission($idcons);
		$this->addMotifAdmission($values, $idemploye);
		
	}
	
	public function getNomMotifConsultation($idMotif){
		$adapter = $this->tableGateway->getAdapter();
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from('liste_motif_consultation');
		$select->where(array('idmotifconsultation' => $idMotif));
		$result = $sql->prepareStatementForSqlObject($select)->execute()->current();
	
		return $result;
	}
	
	
	public function addMotifAdmissionPourExamenJour($values, $codeExamen){
		$tabMotif = array(
				1 => $values->motif_admission1,
				2 => $values->motif_admission2,
				3 => $values->motif_admission3,
				4 => $values->motif_admission4,
				5 => $values->motif_admission5,
		);
		for($i=1 ; $i<=5; $i++){
			if($tabMotif[$i]){
				$datamotifadmission	 = array(
						'libelle_motif' => $tabMotif[$i],
						'idcons' => $codeExamen,
				);
				$this->tableGateway->insert($datamotifadmission);
			}
		}
	}
	
	public function getListeMotifConsultation(){
		$adapter = $this->tableGateway->getAdapter();
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from('liste_motif_consultation');
		$select->order('idmotifconsultation ASC');
		$result = $sql->prepareStatementForSqlObject($select)->execute();
		
		return $result;
	}
	
	public function getListeSiege(){
		$adapter = $this->tableGateway->getAdapter();
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from('liste_siege');
		$select->order('idsiege ASC');
		$result = $sql->prepareStatementForSqlObject($select)->execute();
	
		return $result;
	}
}
