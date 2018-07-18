<?php

namespace Infirmerie\Model;

use Zend\Db\TableGateway\TableGateway;

class ListeBilanPrelevementTable {
	protected $tableGateway;
 	public function __construct(TableGateway $tableGateway) {
 		$this->tableGateway = $tableGateway;
 	}
 	
 	
 	public function getListeBilanPrelevement() {
 		$listeBilans = $this->tableGateway->select()->toArray();
 		
 		return array(
 				//'iTotalDisplayRecords' => count($listeBilans),
 				'aaData' => $listeBilans,
 		);
 	}
 	
 	
}


