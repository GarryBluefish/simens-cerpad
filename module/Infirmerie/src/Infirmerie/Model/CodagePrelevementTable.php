<?php

namespace Infirmerie\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Predicate\In;
use Infirmerie\View\Helper\DateHelper;

class CodagePrelevementTable {
	
	protected $tableGateway;
	
 	public function __construct(TableGateway $tableGateway) {
 		$this->tableGateway = $tableGateway;
 	}
 	
 	
 	public function addCodagePrelevement($donnees){
 	    return $this->tableGateway->getLastInsertValue( $this->tableGateway->insert($donnees) );
 	}
 	
 	public function addCodageAllPrelevement($anneePrelevement, $numeroOrdrePrelevement, $lettrePrelevement, $date_enregistrement, $idbilan, $idemploye){
 		
 		for($i = 0 ; $i < count($anneePrelevement) ; $i++){
 			
 			$code_prelevement = $anneePrelevement[$i].'-'.$numeroOrdrePrelevement[$i].'-'.$lettrePrelevement[$i];
 			
 			$donnees = array(
 					'annee' => $anneePrelevement[$i], 
 					'numero' => $numeroOrdrePrelevement[$i],
 					'prelevement' => $lettrePrelevement[$i],
 					'code_prelevement' => $code_prelevement,
 					'date_enregistrement' => $date_enregistrement,
 					'idbilan' => $idbilan,
 					'idemploye' => $idemploye,
 			);
 			
 			$this->tableGateway->insert($donnees);
 		}

 	}
 	
 	public function addCodagePrelevementLorsDeLaFacturation($anneePrelevement, $numeroOrdrePrelevement, $lettrePrelevement, $date_enregistrement, $idfacturation, $idemploye){
 			
 		for($i = 0 ; $i < count($anneePrelevement) ; $i++){
 	
 			$code_prelevement = $anneePrelevement[$i].'-'.$numeroOrdrePrelevement[$i].'-'.$lettrePrelevement[$i];
 	
 			$donnees = array(
 					'annee' => $anneePrelevement[$i],
 					'numero' => $numeroOrdrePrelevement[$i],
 					'prelevement' => $lettrePrelevement[$i],
 					'code_prelevement' => $code_prelevement,
 					'date_enregistrement' => $date_enregistrement,
 					'idfacturation' => $idfacturation,
 					'idemploye' => $idemploye,
 			);
 	
 			$this->tableGateway->insert($donnees);
 		}
 	
 	}
 	
 	public function addCodageAllPrelevementRepris($anneePrelevement, $numeroOrdrePrelevement, $lettrePrelevement, $date_enregistrement, $idbilanrepris, $idemploye){
 			
 		for($i = 0 ; $i < count($anneePrelevement) ; $i++){
 	
 			$code_prelevement = $anneePrelevement[$i].'-'.$numeroOrdrePrelevement[$i].'-'.$lettrePrelevement[$i];
 	
 			$donnees = array(
 					'annee' => $anneePrelevement[$i],
 					'numero' => $numeroOrdrePrelevement[$i],
 					'prelevement' => $lettrePrelevement[$i],
 					'code_prelevement' => $code_prelevement,
 					'date_enregistrement' => $date_enregistrement,
 					'idbilanrepris' => $idbilanrepris,
 					'idemploye' => $idemploye,
 			);
 	
 			
 			
 			$db = $this->tableGateway->getAdapter();
 			$sql = new Sql($db);
 			$sQuery = $sql->insert()
 			->into('codage_prelevement_repris')
 			->values($donnees);
 			$sql->prepareStatementForSqlObject($sQuery)->execute();
 			
 		}
 	
 	}
 	
 	public function getCodagesPrelevements($idfacturation) {
 		$idfacturation = ( int ) $idfacturation;
 		$rowset = $this->tableGateway->select ( array ( 'idfacturation' => $idfacturation ) );
 		if (! $rowset) { return null; }
 			
 		return $rowset;
 	}
 	
 	public function getCodagePrelevement($idbilan) {
 		$idbilan = ( int ) $idbilan;
 		$rowset = $this->tableGateway->select ( array ( 'idbilan' => $idbilan ) );
 		if (! $rowset) { return null; }
 		
 		return $rowset;
 	}
 	
 	public function getCodagePrelevementRepris($idbilanrepris) {
 		$db = $this->tableGateway->getAdapter();
 		$sql = new Sql($db);
 		$sQuery = $sql->select()
 		->from('codage_prelevement_repris')
 		->where(array('idbilanrepris'=>$idbilanrepris));
 		return $sql->prepareStatementForSqlObject($sQuery)->execute();
 	}
 	
 	
 	public function getListeCodagePrelevement() {
 		$idcodage = ( int ) $idcodage;
 		$rowset = $this->tableGateway->select ( );
 			
 		if (! $rowset) {
 			return null;
 		}
 		return $rowset;
 	}
 	
 	
 	public function getDernierPrelevement($Annee){
 		$db = $this->tableGateway->getAdapter();
 		$sql = new Sql($db);
 		$sQuery = $sql->select('codage_prelevement')->where(array('annee'=>$Annee))->order('idcodage DESC');
 		
 		return $sql->prepareStatementForSqlObject($sQuery) ->execute()->current();
 	}
 	
 	
 	public function updateBilanPrelevement($donnees, $idfacturation) {
 		
 		$this->tableGateway->update($donnees, array('idfacturation' => $idfacturation) );
 		 	
 	}
 	
 	//FONCTION UTILISER DANS LE MODULE DU TECHNICIEN
 	//FONCTION UTILISER DANS LE MODULE DU TECHNICIEN
 	public function getListeCodagePrelevementsNonConforme($idbilan){
 		$db = $this->tableGateway->getAdapter();
 		$sql = new Sql($db);
 		$sQuery = $sql->select()
 		->from(array('bp' => 'bilan_prelevement'))
 		->join(array('tp' => 'tri_prelevement' ), 'tp.idbilan = bp.idbilan', array('IdbilanTri' => 'idbilan', 'Conformite' => 'conformite', 'DateEnregistrementTri' => 'date_enregistrement') )
 		->join(array('d' => 'demande_analyse'), 'd.iddemande = tp.iddemande', array('*'))
 		->join(array('a' => 'analyse'), 'a.idanalyse = d.idanalyse', array('*'))
 		->join(array('t' => 'type_analyse'), 't.idtype = a.idtype_analyse', array('*'))
 		->join(array('tb' => 'tube'), 'tb.idtube = a.idtube', array('Idtube' =>'idtube', 'LibelleTube' =>'libelle', 'Lettre' =>'lettre'))
 		->where(array('bp.idbilan' => $idbilan, 'conformite' => 0))
 		->order(array('t.idtype' => 'ASC', 'd.idanalyse' => 'ASC'))
 		->group('Lettre');
 	
 		$resultat = $sql->prepareStatementForSqlObject($sQuery)->execute();
 	
 		$donnees = array();
 		foreach ($resultat as $result){
 			$donnees[] = $result['Lettre'];;
 		}
 	
 		return $donnees;
 	}
 	
}


