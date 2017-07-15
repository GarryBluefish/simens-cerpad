<?php
namespace Personnel\Model;

use Zend\Db\TableGateway\TableGateway;

class HopitalServiceTable{
	protected $tableGateway;

	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}
	/*
	 *Recuperer l'id d'un hopital connaissant un des services de l'hopital 
	 */
	public function getHopitalService($id){
		$id = ( int ) $id;
		$select = $this->tableGateway->select(array('ID_SERVICE' => $id));
		$serviceRow = $select->current();
		if (! $serviceRow) {
			return null;
		}
		return $serviceRow;
	}
}