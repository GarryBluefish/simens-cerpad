<?php
namespace Consultation\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;

class DepistageTable {

	protected $tableGateway;
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}
	
	public function getConsultation($idcons){
		
// 		var_dump('$expression'); exit();
		
// 		$rowset = $this->tableGateway->select ( array (
// 				'idcons' => $idcons
// 		) );
// 		$row =  $rowset->current ();
//  		if (! $row) {
//  			throw new \Exception ( "Could not find row $idcons" );
//  		}
// 		return $row;
	}
	
 	//Le nombre de patients dépistés 
 	public function getNbPatientsDepistes(){
 		$adapter = $this->tableGateway->getAdapter();
 		$sql = new Sql($adapter);
 		$select = $sql->select();
 		$select->from(array('p' => 'patient'));
 		$select->join(array('d' => 'depistage') ,'d.idpatient = p.idpersonne');
 		return $sql->prepareStatementForSqlObject($select)->execute()->count();
 	}
 	
 	//Le nombre de patients dépistés positif (INTERNE)
 	public function getNbPatientsDepistesPositifs(){
 		$adapter = $this->tableGateway->getAdapter();
 		$sql = new Sql($adapter);
 		$select = $sql->select();
 		$select->from(array('p' => 'patient'));
 		$select->join(array('d' => 'depistage') ,'d.idpatient = p.idpersonne');
 		$select->where(array('d.typepatient' => 1, 'd.valide' => 1));
 		return $sql->prepareStatementForSqlObject($select)->execute()->count();
 	}
 	
 	//Le nombre de patients dépistés négatif (EXTERNE)
 	public function getNbPatientsDepistesNegatifs(){
 		$adapter = $this->tableGateway->getAdapter();
 		$sql = new Sql($adapter);
 		$select = $sql->select();
 		$select->from(array('p' => 'patient'));
 		$select->join(array('d' => 'depistage') ,'d.idpatient = p.idpersonne');
 		$select->where(array('d.typepatient' => 0));
 		$nbReq1 = $sql->prepareStatementForSqlObject($select)->execute()->count();
 		
 		$select = $sql->select();
 		$select->from(array('p' => 'patient'));
 		$select->join(array('d' => 'depistage') ,'d.idpatient = p.idpersonne');
 		$select->where(array('d.typepatient' => 1, 'd.valide' => 0));
 		$nbReq2 = $sql->prepareStatementForSqlObject($select)->execute()->count();
 			
 		
 		return  ($nbReq1 + $nbReq2);
 	}
 	
 	//Le nombre de patients dépistés positif (INTERNE) Sexe Feminin
 	public function getNbPatientsDepistesPositifsFeminin(){
 		$adapter = $this->tableGateway->getAdapter();
 		$sql = new Sql($adapter);
 		$select = $sql->select();
 		$select->from(array('p' => 'patient'));
 		$select->join(array('pers' => 'personne') ,'pers.idpersonne = p.idpersonne');
 		$select->join(array('d' => 'depistage') ,'d.idpatient = p.idpersonne');
 		$select->where(array('d.typepatient' => 1, 'd.valide' => 1, 'sexe' => 'FÃ©minin'));
 		return $sql->prepareStatementForSqlObject($select)->execute()->count();
 	}
 	
 	//Le nombre de patients dépistés positif (INTERNE) Sexe Masculin
 	public function getNbPatientsDepistesPositifsMasculin(){
 		$adapter = $this->tableGateway->getAdapter();
 		$sql = new Sql($adapter);
 		$select = $sql->select();
 		$select->from(array('p' => 'patient'));
 		$select->join(array('pers' => 'personne') ,'pers.idpersonne = p.idpersonne');
 		$select->join(array('d' => 'depistage') ,'d.idpatient = p.idpersonne');
 		$select->where(array('d.typepatient' => 1, 'd.valide' => 1, 'sexe' => 'Masculin'));
 		return $sql->prepareStatementForSqlObject($select)->execute()->count();
 	}
 	
 	//Les formes graves dépistées actuellement
 	public function getListeFormesGravesDepistes(){
 		$adapter = $this->tableGateway->getAdapter();
 		$sql = new Sql($adapter);
 		$select = $sql->select();
 		$select->from(array('p' => 'patient'));
 		$select->join(array('d' => 'depistage') ,'d.idpatient = p.idpersonne');
 		$select->join(array('th' => 'typage_hemoglobine') ,'th.idtypage = d.typage');
 		$select->where(array('d.typepatient' => 1, 'd.valide' => 1));
 		$resultat = $sql->prepareStatementForSqlObject($select)->execute();
 		 
 		$typages = array();
 		$groupetypages = array();
 		foreach ($resultat as $res){
 			$typages[] = $res['designation_stat'];
 			if(!in_array($res['designation_stat'], $groupetypages)){
 				$groupetypages[] = $res['designation_stat'];
 			}
 		}
 		
 		return array($groupetypages, array_count_values($typages));
 	}
 	
}