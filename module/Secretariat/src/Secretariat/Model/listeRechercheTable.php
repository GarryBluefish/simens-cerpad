<?php

namespace Secretariat\Model;

use Zend\Db\TableGateway\TableGateway;
class listeRechercheTable {
	protected $tableGateway;
	
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}
	
	public function fetchAll() {
		$resultSet = $this->tableGateway->select ()->toArray();
		return $resultSet;
	}
	
}