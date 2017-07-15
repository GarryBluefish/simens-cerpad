<?php

namespace Personnel\Model;

use Zend\Db\TableGateway\TableGateway;
use Facturation\View\Helper\DateHelper;
use Zend\Db\Sql\Sql;

class TypepersonnelTable {
	protected $tableGateway;
	protected $conversionDate;
	public function __construct(TableGateway $tableGateway) 
	{
		$this->tableGateway = $tableGateway;
	}
	
	public function getConversionDate()
	{
		$this->conversionDate = new DateHelper();
		return $this->conversionDate;
	}
	
	public function getTypePersonnel($id_type)
	{
		$id_type  = (int) $id_type;
		$rowset = $this->tableGateway->select(array('id_type' => $id_type));
		$row = $rowset->current();
		if (!$row) {
			throw new \Exception("Could not find row $id_type");
		}
		return $row;
	}
	
	public function listeTypePersonnel()
	{
		$adapter = $this->tableGateway->getAdapter ();
		$sql = new Sql ( $adapter );
		$select = $sql->select ();
		$select->from(array('type_pers'=>'type_employe'));
		$select->columns(array ('id_type', 'libelle'));
		$select->order('id_type ASC');
		$stmt = $sql->prepareStatementForSqlObject($select);
		$result = $stmt->execute();
		foreach ($result as $data) {
			$options[$data['libelle']] = $data['libelle'];
		}
		
		return $options;
	}
	
// 	public function saveTypePersonnel(Personnel $personnel, $id_personnel)
// 	{
// 		$this->getConversionDate();
		
//  		$data = array(
//  				'id_personne' => $id_personnel,
//  				'id_service' => $personnel->service_accueil,
//  				'date_debut' => $this->conversionDate->convertDateInAnglais($personnel->date_debut),
//  				'date_fin' => $this->conversionDate->convertDateInAnglais($personnel->date_fin),
//  				'numero_os' => $personnel->numero_os,
//  		);
 		
//  		$id_personne = (int)$personnel->id_personne;
//  		if($id_personne == 0){
//  			$this->tableGateway->insert($data);
//  		} else {
//  			if($this->getAffectation($id_personne)) {
//  				$this->tableGateway->update($data, array('ID_PERSONNE' => $id_personne));
//  			} else {
//  				throw new \Exception('Cette personne n existe pas');
//  			}
//  		}
// 	}
}