<?php
namespace Personnel\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;

class HopitalTable{
	protected $tableGateway;

	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}
// 	public function getServiceAffectation($id){
// 		$id = ( int ) $id;
// 		$select = $this->tableGateway->select(array('ID_SERVICE' => $id));
// 		$serviceRow = $select->current();
// 		if (! $serviceRow) {
// 			throw new \Exception ( "Could not find row $id" );
// 		}
// 		return $serviceRow;
// 	}
// 	public function listeService(){
// 		$adapter = $this->tableGateway->getAdapter();
// 		$sql = new Sql($adapter);
// 		$select = $sql->select();
// 		$select->from(array('type_pers'=>'service'));
// 		$select->columns(array('NOM', 'NOM'));
// 		$select->order('ID_SERVICE ASC');
// 		$stat = $sql->prepareStatementForSqlObject($select);
// 		$result = $stat->execute();
// 		foreach ($result as $data) {
// 			$options[$data['NOM']] = $data['NOM'];
// 		}
// 		return $options;
// 	}
// 	public function fetchService()
// 	{
// 		$adapter = $this->tableGateway->getAdapter ();
// 		$sql = new Sql($adapter);
// 		$select = $sql->select('service');
// 		$select->columns(array('ID_SERVICE', 'NOM'));
// 		$stat = $sql->prepareStatementForSqlObject($select);
// 		$result = $stat->execute();
// 		foreach ($result as $data) {
// 			$options[$data['ID_SERVICE']] = $data['NOM'];
// 		}
// 		return $options;
// 	}
// 	public function getServiceParNom($nom){
// 		$adapter = $this->tableGateway->getAdapter();
// 		$sql = new Sql($adapter);
// 		$select = $sql->select();
// 		$select->from(array('les_services'=>'service'));
// 		$select->where(array('NOM'=>$nom));
// 		$stat = $sql->prepareStatementForSqlObject($select);
// 		$result = $stat->execute()->current();
// 		return $result;
// 	}
// 	public function getServiceHopital($idHopital){
// 		$adapter = $this->tableGateway->getAdapter ();
// 		$sql = new Sql($adapter);
// 		$select = $sql->select();
// 		$select->from(array('s'=>'service'));
// 		$select->columns(array( 'Id_service' =>'ID_SERVICE','Nom_service' =>'NOM'));
// 		$select->join(array('hs'=>'hopital_service'), 's.ID_SERVICE = hs.ID_SERVICE');
// 		$select->join(array('h'=>'hopital'), 'hs.ID_HOPITAL = h.ID_HOPITAL');
// 		$select->where(array('h.ID_HOPITAL'=>$idHopital));
// 		$stat = $sql->prepareStatementForSqlObject($select);
// 		$result = $stat->execute();
// 		return $result;
// 	}
	public function getHopitalParId($id){
		$adapter = $this->tableGateway->getAdapter();
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from(array('h'=>'hopital'));
		$select->where(array('ID_HOPITAL'=>$id));
		
		$stat = $sql->prepareStatementForSqlObject($select);
		$result = $stat->execute()->current();
		
		return $result;
	}
}