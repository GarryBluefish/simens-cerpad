<?php

namespace Laboratoire\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Predicate\In;
use Infirmerie\View\Helper\DateHelper;
use Zend\Db\Sql\Predicate\NotIn;

class TriPrelevementTable {
	protected $tableGateway;
 	public function __construct(TableGateway $tableGateway) {
 		$this->tableGateway = $tableGateway;
 	}
 	
 	
 	public function addTriPrelevement($donnees){
 		
 		if($this->getPrelevementTrieParIddemande($donnees['iddemande'])){
 			return null;
 		}else{
 			return $this->tableGateway->getLastInsertValue( $this->tableGateway->insert($donnees) ); 			
 		}
 	}
 	
 	public function getPrelevementTrieParIddemande($iddemande) {
 		$iddemande = ( int ) $iddemande;
 		return $this->tableGateway->select ( array ('iddemande' => $iddemande ) )->toArray();
 	}
 	
 	public function getPrelevementTrie($idbilan) {
 		$idbilan = ( int ) $idbilan;
 		$rowset = $this->tableGateway->select ( array (
 				'idbilan' => $idbilan
 		) );
 		
 		$row =  $rowset;
 		if (! $row) {
 			return null;
 		}
 		
 		$liste = array();
 		foreach ($row as $list){
 			$liste [] = $list;
 		}
 		
 		return $liste;
 	}
 	
 	
 	public function getPrelevementsTriesNonConformes($idbilan) {
 		$idbilan = ( int ) $idbilan;
 		$rowset = $this->tableGateway->select ( array (
 				'idbilan' => $idbilan,
 				'conformite' => 0
 		) );
 			
 		$row =  $rowset;
 		if (! $row) {
 			return null;
 		}
 			
 		$liste = array();
 		foreach ($row as $list){
 			$liste [] = $list;
 		}
 			
 		return $liste;
 	}
 	
 	
 	public function updateBilanPrelevementTrie($donnees, $iddemande, $idbilan) {
 		
 		$this->tableGateway->update($donnees, array('iddemande' => $iddemande, 'idbilan' => $idbilan) );
 		 	
 	}
 	
 	
 	//Recupérer la liste des analyses triées n'ayant pas encore de résultats par type et pour chaque patient 
 	//pour la feuille de paillasse
 	public function getListeAnalysesTrieesSansResultat() {
 		
 		$db = $this->tableGateway->getAdapter();
 		/* SQL queries
 		* Liste des analyses ayant des résultats
 		*/
 		$sql2 = new Sql ($db );
 		$subselect = $sql2->select ();
 		$subselect->from ( array ( 'rda' => 'resultat_demande_analyse' ) );
 		$subselect->columns (array ( 'iddemande_analyse' ) );
 	
 		/*
 		 * SQL queries
 		* Liste des patients pour lesquels le tri des analyses est déjà fait
 		*/
 		$sql = new Sql($db);
 		$sQuery = $sql->select()
 		->from(array('tp' => 'tri_prelevement'))->columns(array('Idtri' => 'idtri', 'Conformite' => 'conformite'))
 		->join(array('fda' => 'facturation_demande_analyse'), 'fda.iddemande_analyse = tp.iddemande', array('Idfacturation' => 'idfacturation'))
    	->join(array('d' => 'demande_analyse'), 'd.iddemande = fda.iddemande_analyse', array('Iddemande' => 'iddemande'))
    	->join(array('pat' => 'patient'), 'pat.idpersonne = d.idpatient' , array('Idpatient' => 'idpersonne'))
 		->join(array('pers' => 'personne'), 'pers.idpersonne = pat.idpersonne' , array('Nom'=>'nom','Prenom'=>'prenom','Sexe'=>'sexe'))
 		 	
 		->join(array('a' => 'analyse'), 'a.idanalyse = d.idanalyse', array('Idanalyse' => 'idanalyse', 'LibelleAnalyse' => 'designation'))
 		->join(array('t' => 'type_analyse'), 't.idtype = a.idtype_analyse', array('Idtype' => 'idtype', 'LibelleType' => 'libelle'))
 		 	
 		
 		->where( array ( new NotIn( 'd.iddemande', $subselect ) ) )
 		->group('tp.idtri')
 		
 		->order(array('t.idtype' => 'ASC', 'pers.nom' => 'ASC', 'pers.prenom' => 'ASC', 'a.idanalyse' => 'ASC' ));
 		
 		$resultat = $sql->prepareStatementForSqlObject($sQuery)->execute();
 		
 		$tableau = array();
 		
 		foreach ($resultat as $result){
 			$tableau [] = $result;
 		}

 		return $tableau;
 		
 	}
 	
 	
 	public function addTriPrelevementRepris($donnees){
 			
 		$db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
 		$sQuery = $sql->insert() ->into('tri_prelevement_repris') ->values( $donnees );
 		$sql->prepareStatementForSqlObject($sQuery)->execute();
 		
 	}
 	


 	//DERNIERE MODIFICATION  ---  DERNIERE MODIFICATION  ---  DERNIERE MODIFICATION
 	//DERNIERE MODIFICATION  ---  DERNIERE MODIFICATION  ---  DERNIERE MODIFICATION
 	//Recupérer la liste des analyses triées n'ayant pas encore de résultats par type, par analyse
 	//Recupérer la liste des analyses triées n'ayant pas encore de résultats par type, par analyse
 	//pour la feuille de paillasse
 	public function getListeAnalysesTrieesSansResultatTAP() {
 			
 		$db = $this->tableGateway->getAdapter();
 		/* SQL queries
 		 * Liste des analyses ayant des résultats
 		*/
 		$sql2 = new Sql ($db );
 		$subselect = $sql2->select ();
 		$subselect->from ( array ( 'rda' => 'resultat_demande_analyse' ) );
 		$subselect->columns (array ( 'iddemande_analyse' ) );
 	
 		/*
 		 * SQL queries
 		* Liste des patients pour lesquels le tri des analyses est déjà fait
 		*/
 		$sql = new Sql($db);
 		$sQuery = $sql->select()
 		->from(array('tp' => 'tri_prelevement'))->columns(array('Idtri' => 'idtri', 'Conformite' => 'conformite'))
 		->join(array('fda' => 'facturation_demande_analyse'), 'fda.iddemande_analyse = tp.iddemande', array('Idfacturation' => 'idfacturation'))
 		->join(array('d' => 'demande_analyse'), 'd.iddemande = fda.iddemande_analyse', array('Iddemande' => 'iddemande'))
 		->join(array('pat' => 'patient'), 'pat.idpersonne = d.idpatient' , array('Idpatient' => 'idpersonne', 'Ordre' => 'ordre' ))
 		->join(array('pers' => 'personne'), 'pers.idpersonne = pat.idpersonne' , array('Nom'=>'nom','Prenom'=>'prenom','Sexe'=>'sexe'))
 			
 		->join(array('a' => 'analyse'), 'a.idanalyse = d.idanalyse', array('Idanalyse' => 'idanalyse', 'LibelleAnalyse' => 'designation'))
 		->join(array('t' => 'type_analyse'), 't.idtype = a.idtype_analyse', array('Idtype' => 'idtype', 'LibelleType' => 'libelle'))
 			
 			
 		->where( array ( new NotIn( 'd.iddemande', $subselect ) ) )
 		->group('tp.idtri')
 			
 		->order(array('t.idtype' => 'ASC', 'a.idanalyse' => 'ASC', 'pers.nom' => 'ASC', 'pers.prenom' => 'ASC' ));

 		$resultat = $sql->prepareStatementForSqlObject($sQuery)->execute();
 			
 		$tableau = array();
 			
 		foreach ($resultat as $result){
 			$tableau [] = $result;
 		}
 	
 		return $tableau;
 			
 	}
 	
 	
}


