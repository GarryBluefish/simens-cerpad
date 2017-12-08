<?php

namespace Secretariat\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Predicate\NotIn;
use Zend\Db\Sql\Predicate\In;
use Secretariat\View\Helper\DateHelper;

class AnalyseTable {
	protected $tableGateway;
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}
	
	public function fetchAll() {
		$resultSet = $this->tableGateway->select ();
		return $resultSet;
	}
	
	//Recuperer la demande avec 'D'=date, 'P'=idpatient, 'A'=idanalyse
	/**
	 * Recuperer la liste des analyses du patient à la date donnée
	 * @param unknown $date
	 * @param unknown $idpatient
	 * @param unknown $idanalyse
	 */
	public function getDemandeAnalyseDPA($date, $idpatient, $idanalyse){
		$db = $this->tableGateway->getAdapter();
		$sql = new Sql($db);
		$sQuery = $sql->select()
		->from(array('d_a' => 'demande_analyse'))->columns(array('*'))
		->where(array('date' => $date, 'idpatient' => $idpatient, 'idanalyse' => $idanalyse));
		$stat = $sql->prepareStatementForSqlObject($sQuery);
		return $stat->execute()->current();
	}
	
	public function addDemandeAnalyse($analyses, $idemploye, $idpatient){
		$date = (new \DateTime() ) ->format('Y-m-d');
		$time = (new \DateTime() ) ->format('H:i:s');
		$db = $this->tableGateway->getAdapter();
		$sql = new Sql($db);
		
		for($i = 1 ; $i <= count($analyses) ; $i++){
			
			if(!$this->getDemandeAnalyseDPA($date, $idpatient, $analyses[$i])){
				$donnees = array(
						'date' => $date,
						'time' => $time,
						'idsecretaire' => $idemploye,
						'idpatient' => $idpatient,
						'idanalyse' => $analyses[$i],
				);
					
				$sQuery = $sql->insert() ->into('demande_analyse') ->values( $donnees );
				$sql->prepareStatementForSqlObject($sQuery)->execute();
			}
				
		}
		
	}

	
	public function getResultatAnalysesDemandees($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('r' => 'resultat_demande_analyse'))->columns(array('*'))
	    ->where(array('iddemande_analyse' => $iddemande ));
	    
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	/**
	 * On ne supprime que les analyses pour lesquelles il n'y a pas encore de résultat
	 **/
	public function deleteDemandeAnalyse($idpatient){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);

	    $listeAnalysesDemandees = $this->getListeAnalysesDemandeesDP($idpatient);
	    for($i = 0 ; $i < count($listeAnalysesDemandees) ; $i++){
	        $iddemande = $listeAnalysesDemandees[$i]['iddemande'];
	        
	        if(!$this->getResultatAnalysesDemandees($iddemande)){
	            $sQuery = $sql->delete() ->from('demande_analyse')
	            ->where(array('iddemande' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery) ->execute();
	        }
	    }
	    
	}
	
	
	/**
	 * On ne supprime que les analyses n'ayant pas encore été facturées
	 **/
	public function deleteDemandeAnalyseNonFacturees($idpatient){
		$db = $this->tableGateway->getAdapter();
		$sql = new Sql($db);
	
		$listeAnalysesDemandees = $this->getListeAnalysesDemandeesDP($idpatient);
		
		for($i = 0 ; $i < count($listeAnalysesDemandees) ; $i++){
			$iddemande = $listeAnalysesDemandees[$i]['iddemande'];
			$facturation = $listeAnalysesDemandees[$i]['facturer'];
			
 			if($facturation == 0){
 				$sQuery = $sql->delete() ->from('demande_analyse')
 				->where(array('iddemande' => $iddemande ));
 				$sql->prepareStatementForSqlObject($sQuery) ->execute();
 			}
		}
		 
	}
	
	public function updateDemandeAnalyse($analyses, $idemploye, $idpatient){
	    $date = (new \DateTime() ) ->format('Y-m-d');
	    $time = (new \DateTime() ) ->format('H:i:s');
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	
	    $this->deleteDemandeAnalyseNonFacturees($idpatient);
	    
	    for($i = 1 ; $i <= count($analyses) ; $i++){
	        	
	        if(!$this->getDemandeAnalyseDPA($date, $idpatient, $analyses[$i])){
	            $donnees = array(
	                'date' => $date,
	                'time' => $time,
	                'idsecretaire' => $idemploye,
	                'idpatient' => $idpatient,
	                'idanalyse' => $analyses[$i],
	            );
	            	
	            $sQuery = $sql->insert() ->into('demande_analyse') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	        }
	
	    }
	
	}
	
	public function addDignosticAnalyse($idemploye, $idpatient, $diagnostic_demande){
	    $date = (new \DateTime() ) ->format('Y-m-d');
	    $time = (new \DateTime() ) ->format('H:i:s');
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    
	    $donnees = array(
	        'date' => $date,
	        'time' => $time,
	        'idsecretaire' => $idemploye,
	        'idpatient' => $idpatient,
	        'diagnostic_demande' => $diagnostic_demande,
	    );
	    	
	    $sQuery = $sql->insert() ->into('diagnostic_demande_analyse') ->values( $donnees );
	    $sql->prepareStatementForSqlObject($sQuery)->execute();
	}
	
	public function updateDignosticAnalyse($idemploye, $idpatient, $diagnostic_demande){
	    $date = (new \DateTime() ) ->format('Y-m-d');
	    $time = (new \DateTime() ) ->format('H:i:s');
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	     
	    if($this->getDiagnosticAnalyse($idpatient, $date)){
	        $donnees = array(
	            'time' => $time,
	            'idsecretaire' => $idemploye,
	            'diagnostic_demande' => $diagnostic_demande,
	        );
	        
	        $db = $this->tableGateway->getAdapter();
	        $sql = new Sql($db);
	        $sQuery = $sql->update() ->table('diagnostic_demande_analyse')
	        ->set( $donnees ) ->where(array('idpatient' => $idpatient, 'date' => $date ));
	         
	        $sql->prepareStatementForSqlObject($sQuery)->execute();
	    }else{
	        $this->addDignosticAnalyse($idemploye, $idpatient, $diagnostic_demande);
	    }
	    
	}
	
	public function deleteDignosticAnalyse($idpatient){
	    $date = (new \DateTime() ) ->format('Y-m-d');
	    
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->delete() ->from('diagnostic_demande_analyse')
	    ->where(array('idpatient' => $idpatient, 'date' => $date ));
	    
	    $sql->prepareStatementForSqlObject($sQuery) ->execute();
	}
	
	public function getDiagnosticAnalyse($idpatient, $date){
	    $adapter = $this->tableGateway->getAdapter ();
	    $sql = new Sql($adapter);
	    $select = $sql->select();
	    $select->from(array('dda'=>'diagnostic_demande_analyse'));
	    $select->columns(array('*'));
	    $select->where(array('dda.idpatient' => $idpatient, 'dda.date' => $date));
	    return $sql->prepareStatementForSqlObject($select)->execute()->current();
	}
	
	//Recuperer les informations sur l' analyse de la demande iddemande
	public function getAnalysesDemandees($iddemande){
		$adapter = $this->tableGateway->getAdapter ();
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from(array('d'=>'demande_analyse'));
		$select->columns(array('*'));
		$select->join(array('a'=>'analyse') ,'d.idanalyse = a.idanalyse', array('Idanalyse'=>'idanalyse', 'Designation'=>'designation', 'Tarif'=>'tarif'));
		$select->join(array('t'=>'type_analyse') ,'t.idtype = a.idtype_analyse', array('Libelle'=>'libelle'));
		$select->where(array('d.iddemande' => $iddemande));
		return $sql->prepareStatementForSqlObject($select)->execute()->current();
	}
	
	public function getDemandeAnalysesAvecIddemande($iddemande){
		$adapter = $this->tableGateway->getAdapter ();
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from(array('d'=>'demande_analyse'));
		$select->columns(array('*'));
		$select->where(array('iddemande' => $iddemande));
		return $sql->prepareStatementForSqlObject($select)->execute()->current();
	}
	
	
	//Recuperer la liste des analyses demandees pour la demande $iddemande
	public function getListeAnalysesDemandees($iddemande){
		$demande = $this->getDemandeAnalysesAvecIddemande($iddemande);
	
		$adapter = $this->tableGateway->getAdapter ();
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from(array('d'=>'demande_analyse'));
		$select->columns(array('*'));
		$select->join(array('a'=>'analyse') ,'d.idanalyse = a.idanalyse', array('Idanalyse'=>'idanalyse', 'Designation'=>'designation', 'Tarif'=>'tarif'));
		$select->join(array('t'=>'type_analyse') ,'t.idtype = a.idtype_analyse', array('Libelle'=>'libelle'));
		$select->where(array('date' => $demande['date'], 'idpatient' => $demande['idpatient']));
		$select->order(array('a.Idanalyse' => 'ASC', 'idtype' =>'ASC'));
		return $sql->prepareStatementForSqlObject($select)->execute();
	}
	
	/**
	 * Liste des analyses demandees par type pour toutes les demandes
	 */
	public function getListeAnalysesDemandeesParType($idtype){
		
		$adapter1 = $this->tableGateway->getAdapter ();
		$sql1 = new Sql ($adapter1);
		$subselect = $sql1->select ();
		$subselect->from ( array ( 'rda' => 'resultat_demande_analyse' ) );
		//$subselect->where( array ( 'rda.valide' => 1 ) );
		$subselect->columns (array ( 'iddemande_analyse' ) );
		
		
		$adapter2 = $this->tableGateway->getAdapter ();
		$sql2 = new Sql($adapter2);
		$select2 = $sql2->select();
		$select2->from(array('d'=>'demande_analyse'));
		$select2->columns(array('*'));
		$select2->join(array('tp' => 'tri_prelevement'), 'tp.iddemande = d.iddemande', array('*') );
		$select2->join(array('a'=>'analyse') ,'d.idanalyse = a.idanalyse', array('Idanalyse'=>'idanalyse', 'Designation'=>'designation', 'Tarif'=>'tarif'));
		$select2->join(array('t'=>'type_analyse') ,'t.idtype = a.idtype_analyse', array('Libelle'=>'libelle'));
		$select2->join(array('p'=>'personne') ,'p.idpersonne = d.idpatient', array('*'));
		$select2->join(array('pat'=>'patient') ,'pat.idpersonne = p.idpersonne', array('*'));
		$select2->where(array('t.idtype' => $idtype, new NotIn ( 'd.iddemande', $subselect )));
		$select2->order(array('d.iddemande' => 'ASC', 'a.Idanalyse' => 'ASC'));
		return $sql2->prepareStatementForSqlObject($select2)->execute();
	}
	
	/**
	 * Liste des analyses demandees par type et par analyse pour toutes les demandes
	 */
	public function getListeAnalysesDemandeesParTypeEtAnalyse($idtype, $idanalyse){
	
	    $adapter1 = $this->tableGateway->getAdapter ();
	    $sql1 = new Sql ($adapter1);
	    $subselect = $sql1->select ();
	    $subselect->from ( array ( 'rda' => 'resultat_demande_analyse' ) );
	    //$subselect->where( array ( 'rda.valide' => 1 ) );
	    $subselect->columns (array ( 'iddemande_analyse' ) );
	    
	    $adapter2 = $this->tableGateway->getAdapter ();
	    $sql2 = new Sql($adapter2);
	    $select2 = $sql2->select();
	    $select2->from(array('d'=>'demande_analyse'));
	    $select2->columns(array('*'));
	    $select2->join(array('tp' => 'tri_prelevement'), 'tp.iddemande = d.iddemande', array('*') );
	    $select2->join(array('a'=>'analyse') ,'d.idanalyse = a.idanalyse', array('Idanalyse'=>'idanalyse', 'Designation'=>'designation', 'Tarif'=>'tarif'));
	    $select2->join(array('t'=>'type_analyse') ,'t.idtype = a.idtype_analyse', array('Libelle'=>'libelle'));
	    $select2->join(array('p'=>'personne') ,'p.idpersonne = d.idpatient', array('*'));
	    $select2->join(array('pat'=>'patient') ,'pat.idpersonne = p.idpersonne', array('*'));
	    $select2->where(array('t.idtype' => $idtype, 'a.idanalyse' => $idanalyse, new NotIn ( 'd.iddemande', $subselect )));
	    $select2->order(array('d.iddemande' => 'ASC', 'a.Idanalyse' => 'ASC'));
	    return $sql2->prepareStatementForSqlObject($select2)->execute();
	
	}
	
	/**
	 * Liste des analyses demandees par type et par analyse et par date pour toutes les demandes
	 */
	public function getListeAnalysesDemandeesParTypeEtDate($idtype, $date){
	
	    $adapter1 = $this->tableGateway->getAdapter ();
	    $sql1 = new Sql ($adapter1);
	    $subselect = $sql1->select ();
	    $subselect->from ( array ( 'rda' => 'resultat_demande_analyse' ) );
	    //$subselect->where( array ( 'rda.valide' => 1 ) );
	    $subselect->columns (array ( 'iddemande_analyse' ) );
	    
	    $adapter2 = $this->tableGateway->getAdapter ();
	    $sql2 = new Sql($adapter2);
	    $select2 = $sql2->select();
	    $select2->from(array('d'=>'demande_analyse'));
	    $select2->columns(array('*'));
	    $select2->join(array('tp' => 'tri_prelevement'), 'tp.iddemande = d.iddemande', array('*') );
	    $select2->join(array('a'=>'analyse') ,'d.idanalyse = a.idanalyse', array('Idanalyse'=>'idanalyse', 'Designation'=>'designation', 'Tarif'=>'tarif'));
	    $select2->join(array('t'=>'type_analyse') ,'t.idtype = a.idtype_analyse', array('Libelle'=>'libelle'));
	    $select2->join(array('p'=>'personne') ,'p.idpersonne = d.idpatient', array('*'));
	    $select2->join(array('pat'=>'patient') ,'pat.idpersonne = p.idpersonne', array('*'));
	    $select2->where(array('t.idtype' => $idtype, 'd.date' => $date, new NotIn ( 'd.iddemande', $subselect )));
	    $select2->order(array('d.iddemande' => 'ASC', 'a.Idanalyse' => 'ASC'));
	    return $sql2->prepareStatementForSqlObject($select2)->execute();
	
	}
	
	/**
	 * Liste des analyses demandees par type et par analyse et par date pour toutes les demandes
	 */
	public function getListeAnalysesDemandeesParTypeEtAnalyseEtDate($idtype, $idanalyse, $date){
	
	    $adapter1 = $this->tableGateway->getAdapter ();
	    $sql1 = new Sql ($adapter1);
	    $subselect = $sql1->select ();
	    $subselect->from ( array ( 'rda' => 'resultat_demande_analyse' ) );
	    //$subselect->where( array ( 'rda.valide' => 1 ) );
	    $subselect->columns (array ( 'iddemande' => 'iddemande_analyse' ) );
	    
	    $adapter2 = $this->tableGateway->getAdapter ();
	    $sql2 = new Sql($adapter2);
	    $select2 = $sql2->select();
	    $select2->from(array('d'=>'demande_analyse'));
	    $select2->columns(array('*'));
	    $select2->join(array('tp' => 'tri_prelevement'), 'tp.iddemande = d.iddemande', array('*') );
	    $select2->join(array('a'=>'analyse') ,'d.idanalyse = a.idanalyse', array('Idanalyse'=>'idanalyse', 'Designation'=>'designation', 'Tarif'=>'tarif'));
	    $select2->join(array('t'=>'type_analyse') ,'t.idtype = a.idtype_analyse', array('Libelle'=>'libelle'));
	    $select2->join(array('p'=>'personne') ,'p.idpersonne = d.idpatient', array('*'));
	    $select2->join(array('pat'=>'patient') ,'pat.idpersonne = p.idpersonne', array('*'));
	    $select2->where(array('t.idtype' => $idtype, 'a.idanalyse' => $idanalyse, 'd.date' => $date, new NotIn ( 'd.iddemande', $subselect )));
	    $select2->order(array('d.iddemande' => 'ASC', 'a.Idanalyse' => 'ASC'));
	    return $sql2->prepareStatementForSqlObject($select2)->execute();
	
	}
	
	
	
	

	
	
	
	/**
	 * Liste des analyses demandees par type pour toutes les demandes en regroupant par analyse 
	 */
	public function getListeAnalysesDemandeesParTypeGroupeAnalyse($idtype){
	
	    $adapter1 = $this->tableGateway->getAdapter ();
	    $sql1 = new Sql ($adapter1);
	    $subselect = $sql1->select ();
	    $subselect->from ( array ( 'rda' => 'resultat_demande_analyse' ) );
	    //$subselect->where( array ( 'rda.valide' => 1 ) );
	    $subselect->columns (array ( 'iddemande' => 'iddemande_analyse' ) );
	    
	    
	    $adapter2 = $this->tableGateway->getAdapter ();
	    $sql2 = new Sql($adapter2);
	    $select2 = $sql2->select();
	    $select2->from(array('d'=>'demande_analyse'));
	    $select2->columns(array('*'));
	    $select2->join(array('tp' => 'tri_prelevement'), 'tp.iddemande = d.iddemande', array('*') );
	    $select2->join(array('a'=>'analyse') ,'d.idanalyse = a.idanalyse', array('Idanalyse'=>'idanalyse', 'Designation'=>'designation', 'Tarif'=>'tarif'));
	    $select2->join(array('t'=>'type_analyse') ,'t.idtype = a.idtype_analyse', array('Libelle'=>'libelle'));
	    $select2->join(array('p'=>'personne') ,'p.idpersonne = d.idpatient', array('*'));
	    $select2->join(array('pat'=>'patient') ,'pat.idpersonne = p.idpersonne', array('*'));
	    $select2->where(array('t.idtype' => $idtype, new NotIn ( 'd.iddemande', $subselect )));
	    $select2->order(array('d.iddemande' => 'ASC', 'a.Idanalyse' => 'ASC'));
	    $select2->group('a.Idanalyse');
	    return $sql2->prepareStatementForSqlObject($select2)->execute();
	
	}
	
	
	/**
	 * Liste des analyses demandees par type pour toutes les demandes en regroupant par date
	 */
	public function getListeAnalysesDemandeesParTypeGroupeDate($idtype){
	
	    $adapter1 = $this->tableGateway->getAdapter ();
	    $sql1 = new Sql ($adapter1);
	    $subselect = $sql1->select ();
	    $subselect->from ( array ( 'rda' => 'resultat_demande_analyse' ) );
	    //$subselect->where( array ( 'rda.valide' => 1 ) );
	    $subselect->columns (array ( 'iddemande' => 'iddemande_analyse' ) );
	    
	    
	    $adapter2 = $this->tableGateway->getAdapter ();
	    $sql2 = new Sql($adapter2);
	    $select2 = $sql2->select();
	    $select2->from(array('d'=>'demande_analyse'));
	    $select2->columns(array('*'));
	    $select2->join(array('tp' => 'tri_prelevement'), 'tp.iddemande = d.iddemande', array('*') );
	    $select2->join(array('a'=>'analyse') ,'d.idanalyse = a.idanalyse', array('Idanalyse'=>'idanalyse', 'Designation'=>'designation', 'Tarif'=>'tarif'));
	    $select2->join(array('t'=>'type_analyse') ,'t.idtype = a.idtype_analyse', array('Libelle'=>'libelle'));
	    $select2->join(array('p'=>'personne') ,'p.idpersonne = d.idpatient', array('*'));
	    $select2->join(array('pat'=>'patient') ,'pat.idpersonne = p.idpersonne', array('*'));
	    $select2->where(array('t.idtype' => $idtype, new NotIn ( 'd.iddemande', $subselect )));
	    $select2->order(array('d.date' => 'DESC'));
	    $select2->group('d.date');
	    return $sql2->prepareStatementForSqlObject($select2)->execute();
	
	}
	
	
	/**
	 * Liste des analyses demandees par type pour toutes les demandes en regroupant par date
	 */
	public function getListeAnalysesDemandeesParTypeEtParAnalyseGroupeDate($idtype, $idanalyse){
	
	    $adapter1 = $this->tableGateway->getAdapter ();
	    $sql1 = new Sql ($adapter1);
	    $subselect = $sql1->select ();
	    $subselect->from ( array ( 'rda' => 'resultat_demande_analyse' ) );
	    //$subselect->where( array ( 'rda.valide' => 1 ) );
	    $subselect->columns (array ( 'iddemande' => 'iddemande_analyse' ) );
	    
	    $adapter2 = $this->tableGateway->getAdapter ();
	    $sql2 = new Sql($adapter2);
	    $select2 = $sql2->select();
	    $select2->from(array('d'=>'demande_analyse'));
	    $select2->columns(array('*'));
	    $select2->join(array('tp' => 'tri_prelevement'), 'tp.iddemande = d.iddemande', array('*') );
	    $select2->join(array('a'=>'analyse') ,'d.idanalyse = a.idanalyse', array('Idanalyse'=>'idanalyse', 'Designation'=>'designation', 'Tarif'=>'tarif'));
	    $select2->join(array('t'=>'type_analyse') ,'t.idtype = a.idtype_analyse', array('Libelle'=>'libelle'));
	    $select2->join(array('p'=>'personne') ,'p.idpersonne = d.idpatient', array('*'));
	    $select2->join(array('pat'=>'patient') ,'pat.idpersonne = p.idpersonne', array('*'));
	    $select2->where(array('t.idtype' => $idtype, 'd.idanalyse' => $idanalyse, new NotIn ( 'd.iddemande', $subselect )));
	    $select2->order(array('d.date' => 'DESC'));
	    $select2->group('d.date');
	    return $sql2->prepareStatementForSqlObject($select2)->execute();
	
	}
	
	//Recuperer la liste des analyses demandees avec 'D'=date, 'P'=idpatient
	/**
	 * Recuperer la liste des analyses du patient du jour j
	 * @param $idpatient
	 */
	public function getListeAnalysesDemandeesDP($idpatient){
	    $aujourdhui = (new \DateTime() ) ->format('Y-m-d');
	    
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('d' => 'demande_analyse'))->columns(array('*'))
	    ->join(array('a' => 'analyse'), 'a.idanalyse = d.idanalyse', array('*'))
	    ->join(array('t' => 'type_analyse'), 't.idtype = a.idtype_analyse', array('*'))
	    ->where(array('d.date' => $aujourdhui, 'd.idpatient' => $idpatient))
	    ->order(array('facturer' => 'DESC', 't.idtype' => 'ASC', 'd.idanalyse' => 'ASC'));
	    
	    $resultat = $sql->prepareStatementForSqlObject($sQuery)->execute();
	    
	    $donnees = array();
	    foreach ($resultat as $result){
	        $donnees[] = $result;
	    }
	    
	    return $donnees;
	}
	
	//Recuperer la demande d'analyse sur le typage de l'hémoglobine s'il y en a
	/**
	 * @param $idpatient
	 */
	public function getAnalyseTypageHemoglobineDemande($idpatient){
		$aujourdhui = (new \DateTime() ) ->format('Y-m-d');
		 
		$db = $this->tableGateway->getAdapter();
		$sql = new Sql($db);
		$sQuery = $sql->select()
		->from(array('d' => 'demande_analyse'))->columns(array('*'))
		->where(array('idanalyse' => 68, 'idpatient' => $idpatient, 'date != ?' => $aujourdhui));
		 
		$result = $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
		if($result){ return 1; }else{ return 0; }
	}
	
	
	//FONCTION UTILISER DANS LE MODULE DE LA FACTURATION
	//FONCTION UTILISER DANS LE MODULE DE LA FACTURATION
	
	//Recuperer la liste des analyses demandees qui ne sont pas encore facturées
	public function getListeAnalysesDemandeesNonFacturees($idpatient){
	    $db = $this->tableGateway->getAdapter();
	    
	    $sql2 = new Sql ($db );
	    $subselect = $sql2->select ();
	    $subselect->from ( array ( 'fact' => 'facturation_demande_analyse' ) );
	    $subselect->columns (array ( 'iddemande_analyse' ) );
	    
	    
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('d' => 'demande_analyse'))->columns(array('*'))
	    ->join(array('a' => 'analyse'), 'a.idanalyse = d.idanalyse', array('*'))
	    ->join(array('t' => 'type_analyse'), 't.idtype = a.idtype_analyse', array('*'))
	    ->where(array('d.idpatient' => $idpatient,  new NotIn ( 'd.iddemande', $subselect ) ) )
	    ->order(array('t.idtype' => 'ASC', 'd.idanalyse' => 'ASC'));
	     
	    $resultat = $sql->prepareStatementForSqlObject($sQuery)->execute();
	     
	    $donnees = array();
	    foreach ($resultat as $result){
	        $donnees[] = $result;
	    }
	     
	    return $donnees;
	}
	
	
	//FONCTION UTILISER DANS LE MODULE DE LA FACTURATION 
	//FONCTION UTILISER DANS LE MODULE DE LA FACTURATION
	
	//Pour la récupération de la liste des demandes  
	public function getListeDemandesAnalysesNonFacturees($idpatient){
		$db = $this->tableGateway->getAdapter();
		 
		$sql2 = new Sql ($db );
		$subselect = $sql2->select ();
		$subselect->from ( array ( 'fact' => 'facturation_demande_analyse' ) );
		$subselect->columns (array ( 'iddemande_analyse' ) );
		 
		 
		$sql = new Sql($db);
		$sQuery = $sql->select()
		->from(array('d' => 'demande_analyse'))->columns(array('*'))
		->join(array('a' => 'analyse'), 'a.idanalyse = d.idanalyse', array('*'))
		->join(array('t' => 'type_analyse'), 't.idtype = a.idtype_analyse', array('*'))
		->join(array('p' => 'personne'), 'p.idpersonne = d.idsecretaire', array('NomSecretaire' => 'nom' , 'PrenomSecretaire' => 'prenom'))
		->where(array('d.idpatient' => $idpatient,  new NotIn ( 'd.iddemande', $subselect ) ) )
		->order(array('t.idtype' => 'ASC', 'd.idanalyse' => 'ASC'))
		->group('d.date');
	
		$resultat = $sql->prepareStatementForSqlObject($sQuery)->execute();
	
 		$donnees = array();
 		foreach ($resultat as $result){
 			$donnees[] = $result;
 		}
	
 		return $donnees;
	}
	
	
	//FONCTION UTILISER DANS LE MODULE DE LA FACTURATION
	//FONCTION UTILISER DANS LE MODULE DE LA FACTURATION
	
	//Pour la récupération de la liste des analyses de la dernière demande non facture
	public function getListeAnalysesDerniereDemandeNonFacturees($idpatient, $date){
		$db = $this->tableGateway->getAdapter();
			
		$sql2 = new Sql ($db );
		$subselect = $sql2->select ();
		$subselect->from ( array ( 'fact' => 'facturation_demande_analyse' ) );
		$subselect->columns (array ( 'iddemande_analyse' ) );
			
			
		$sql = new Sql($db);
		$sQuery = $sql->select()
		->from(array('d' => 'demande_analyse'))->columns(array('*'))
		->join(array('a' => 'analyse'), 'a.idanalyse = d.idanalyse', array('*'))
		->join(array('t' => 'type_analyse'), 't.idtype = a.idtype_analyse', array('*'))
		->where(array('d.idpatient' => $idpatient, 'd.date' => $date, new NotIn ( 'd.iddemande', $subselect ) ) )
		->order(array('t.idtype' => 'ASC', 'd.idanalyse' => 'ASC'));
	
		$resultat = $sql->prepareStatementForSqlObject($sQuery)->execute();
	
		$donnees = array();
		foreach ($resultat as $result){
			$donnees[] = $result;
		}
	
		return $donnees;
	}
	
	
	//FONCTION UTILISER DANS LE MODULE DE LA FACTURATION
	//FONCTION UTILISER DANS LE MODULE DE LA FACTURATION
	
	//Pour la récupération de la liste des analyses de la dernière demande non facture
	public function getnbAnalysesDeLaDemandeNonFacturees($idpatient, $date){
		$db = $this->tableGateway->getAdapter();
			
		$sql2 = new Sql ($db );
		$subselect = $sql2->select ();
		$subselect->from ( array ( 'fact' => 'facturation_demande_analyse' ) );
		$subselect->columns (array ( 'iddemande_analyse' ) );
			
			
		$sql = new Sql($db);
		$sQuery = $sql->select()
		->from(array('d' => 'demande_analyse'))->columns(array('*'))
		->join(array('a' => 'analyse'), 'a.idanalyse = d.idanalyse', array('*'))
		->join(array('t' => 'type_analyse'), 't.idtype = a.idtype_analyse', array('*'))
		->where(array('d.idpatient' => $idpatient, 'd.date' => $date, new NotIn ( 'd.iddemande', $subselect ) ) );
	
		return count( $sql->prepareStatementForSqlObject($sQuery)->execute() );
	
	}
	
	
	
	//FONCTION UTILISEE DANS LE MODULE DU LABORATOIRE - PROFIL TECHNICIEN
	//FONCTION UTILISEE DANS LE MODULE DU LABORATOIRE - PROFIL TECHNICIEN

	// C'est l'ensemble des demandes pour lesquelles le biologiste n'a pas encore validé
	public function getDemandesAnalysesTriees($id){
		
		$adapter = $this->tableGateway->getAdapter ();
		
		$sql2 = new Sql ($adapter);
		$subselect = $sql2->select ();
		$subselect->from ( array ( 'rda' => 'resultat_demande_analyse' ) );
		$subselect->where( array ( 'rda.valide' => 1 ) );
		$subselect->columns (array ( 'iddemande_analyse' ) );
			
		
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from(array('d'=>'demande_analyse'))->columns(array('*'));
		$select->join(array('tp' => 'tri_prelevement'), 'tp.iddemande = d.iddemande', array('*') );
		$select->where( array ('d.idpatient' => $id, new NotIn ( 'd.iddemande', $subselect ) ) );
		$select->group('date');
		$select->order('date DESC');
		return $sql->prepareStatementForSqlObject($select)->execute();
	
	}
	
	
	//Recuperer les analyses de la dernière demande du patient $id
	public function getAnalysesDemandeesTriees($id){
		$demande = $this->getDemandesAnalysesTriees($id)->current();
	
		$adapter = $this->tableGateway->getAdapter ();
		
		$sql2 = new Sql ($adapter);
		$subselect = $sql2->select ();
		$subselect->from ( array ( 'rda' => 'resultat_demande_analyse' ) );
		$subselect->where( array ( 'rda.valide' => 1 ) );
		$subselect->columns (array ( 'iddemande_analyse' ) );
		
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from(array('d'=>'demande_analyse'));
		$select->columns(array('*'));
		$select->join(array('a'=>'analyse') ,'d.idanalyse = a.idanalyse', array('Designation'=>'designation', 'Tarif'=>'tarif'));
		$select->join(array('t'=>'type_analyse') ,'t.idtype = a.idtype_analyse', array('Libelle'=>'libelle'));
		$select->where(array('date' => $demande['date'], 'idpatient' => $id, new NotIn ( 'd.iddemande', $subselect ) ));
		$select->order('idanalyse ASC');
		return $sql->prepareStatementForSqlObject($select)->execute();
	}
	
	
	public function getDemandeAnalyseTrieeAvecIddemande($iddemande){
		$adapter = $this->tableGateway->getAdapter ();
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from(array('d'=>'demande_analyse'));
		$select->columns(array('*'));
		$select->where(array('iddemande' => $iddemande));
		return $sql->prepareStatementForSqlObject($select)->execute()->current();
	}
	
	
	//LISTE DES ANALYSES TRIEES N'AYANT PAS DE RESULTATS VALIDES --- technicien
	//LISTE DES ANALYSES TRIEES N'AYANT PAS DE RESULTATS VALIDES --- technicien
	//Recuperer la liste des analyses demandees pour la demande $iddemande
	public function getListeAnalysesDemandeesTriees($iddemande){
		$dateDemande = $this->getDemandeAnalyseTrieeAvecIddemande($iddemande);
	
		$adapter = $this->tableGateway->getAdapter ();
		
		$sql2 = new Sql ($adapter);
		$subselect = $sql2->select ();
		$subselect->from ( array ( 'rda' => 'resultat_demande_analyse' ) );
		$subselect->where( array ( 'rda.valide' => 1 ) );
		$subselect->columns (array ( 'iddemande_analyse' ) );
		
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from(array('d'=>'demande_analyse'))->columns(array('*'));
		$select->join(array('tp' => 'tri_prelevement'), 'tp.iddemande = d.iddemande', array() );
		$select->join(array('a'=>'analyse') ,'d.idanalyse = a.idanalyse', array('Designation'=>'designation', 'Tarif'=>'tarif'));
		$select->join(array('t'=>'type_analyse') ,'t.idtype = a.idtype_analyse', array('Libelle'=>'libelle'));
		$select->where(array('date' => $dateDemande['date'], 'idpatient' => $dateDemande['idpatient'], new NotIn ( 'd.iddemande', $subselect ) ));
		$select->order(array('idanalyse' => 'ASC', 'idtype' =>'ASC'));
		return $sql->prepareStatementForSqlObject($select)->execute();
	}
	
	
	//LISTE DES ANALYSES TRIEES N'AYANT PAS DE RESULTATS VALIDES --- technicien 
	//LISTE DES ANALYSES TRIEES N'AYANT PAS DE RESULTATS VALIDES --- technicien 
	public function getListeAnalysesDemandeesTrieesPopup($iddemande){
		$dateDemande = $this->getDemandeAnalyseTrieeAvecIddemande($iddemande);
	
		$adapter = $this->tableGateway->getAdapter ();
	
		$sql2 = new Sql ($adapter);
		$subselect = $sql2->select ();
		$subselect->from ( array ( 'rda' => 'resultat_demande_analyse' ) );
		$subselect->where( array ( 'rda.valide' => 1 ) );
		$subselect->columns (array ( 'iddemande_analyse' ) );
	
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from(array('d'=>'demande_analyse'))->columns(array('*'));
		$select->join(array('tp' => 'tri_prelevement'), 'tp.iddemande = d.iddemande', array() );
		$select->join(array('a'=>'analyse') ,'d.idanalyse = a.idanalyse', array('Idanalyse'=>'idanalyse', 'Designation'=>'designation', 'Tarif'=>'tarif'));
		$select->join(array('t'=>'type_analyse') ,'t.idtype = a.idtype_analyse', array('Libelle'=>'libelle'));
		$select->where(array('date' => $dateDemande['date'], 'idpatient' => $dateDemande['idpatient'], new NotIn ( 'd.iddemande', $subselect ) ));
		$select->order(array('a.Idanalyse' => 'ASC', 'idtype' =>'ASC'));
		return $sql->prepareStatementForSqlObject($select)->execute();
	}
	
	
	
	//LISTE DES ANALYSES AYANT DES RESULTATS NON ENCORE VALIDER PAR LE BIOLOGISTE 
	//LISTE DES ANALYSES AYANT DES RESULTATS NON ENCORE VALIDER PAR LE BIOLOGISTE
	public function getListeAnalysesDemandeesResultats($iddemande){
		$demande = $this->getDemandeAnalysesAvecIddemande($iddemande);
		
		$adapter = $this->tableGateway->getAdapter ();
	
		$sql2 = new Sql ($adapter);
		$subselect = $sql2->select ();
		$subselect->from ( array ( 'rda' => 'resultat_demande_analyse' ) );
		$subselect->where( array ( 'rda.valide' => 0 ) );
		$subselect->columns (array ( 'iddemande_analyse' ) );
			
	
			
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from(array('d'=>'demande_analyse'));
		$select->columns(array('*'));
		$select->join(array('a'=>'analyse') ,'d.idanalyse = a.idanalyse', array('Idanalyse'=>'idanalyse', 'Designation'=>'designation', 'Tarif'=>'tarif'));
		$select->join(array('t'=>'type_analyse') ,'t.idtype = a.idtype_analyse', array('Libelle'=>'libelle'));
		$select->where(array('date' => $demande['date'], 'idpatient' => $demande['idpatient'],  new In ( 'd.iddemande', $subselect ) ));
		$select->order(array('a.Idanalyse' => 'ASC', 'idtype' =>'ASC'));
		
		return $sql->prepareStatementForSqlObject($select)->execute();
	
	}
	
	
	//LISTE DES ANALYSES TRIEES --- technicien
	//LISTE DES ANALYSES TRIEES --- technicien
	public function getListeAnalysesDemandeesTrieesPopupBiol($iddemande, $typeResultat){
		$dateDemande = $this->getDemandeAnalyseTrieeAvecIddemande($iddemande);
	
		$adapter = $this->tableGateway->getAdapter ();
	
		$sql2 = new Sql ($adapter);
		$subselect = $sql2->select ();
		$subselect->from ( array ( 'rda' => 'resultat_demande_analyse' ) );
		$subselect->where( array ( 'rda.valide' => $typeResultat ) );
		$subselect->columns (array ( 'iddemande_analyse' ) );
	
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from(array('d'=>'demande_analyse'))->columns(array('*'));
		$select->join(array('tp' => 'tri_prelevement'), 'tp.iddemande = d.iddemande', array() );
		$select->join(array('a'=>'analyse') ,'d.idanalyse = a.idanalyse', array('Idanalyse'=>'idanalyse', 'Designation'=>'designation', 'Tarif'=>'tarif'));
		$select->join(array('t'=>'type_analyse') ,'t.idtype = a.idtype_analyse', array('Libelle'=>'libelle'));
		$select->where(array('date' => $dateDemande['date'], 'idpatient' => $dateDemande['idpatient'], new In ( 'd.iddemande', $subselect ) ));
		$select->order(array('a.Idanalyse' => 'ASC', 'idtype' =>'ASC'));
		return $sql->prepareStatementForSqlObject($select)->execute();
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	public function getParentPatient($idpatient){
		$db = $this->tableGateway->getAdapter();
		$sql = new Sql($db);
		$sQuery = $sql->select()
		->from(array('pers' => 'personne'))->columns(array('*'))
		->join(array('par' => 'parent'), ' par.idpersonne = pers.idpersonne', array())
		->where(array('par.idpatient' => $idpatient, 'par.parent' => 'mere'));
		$stat = $sql->prepareStatementForSqlObject($sQuery);
		return $stat->execute()->current();
	}
	
	//********** RECUPERER LA LISTE DES BILANS DES PRELEVEMENTS POUR LESQUELS IL Y A DES ANALYSES NON CONFORMES *********
	//********** RECUPERER LA LISTE DES BILANS DES PRELEVEMENTS POUR LESQUELS IL Y A DES ANALYSES NON CONFORMES *********
	public function getListeBilansAnalysesNonConformePrelevement(){
	
		$db = $this->tableGateway->getAdapter();
			
		$aColumns = array('numero_dossier', 'Nom','Prenom','Datenaissance', 'Telephone', 'Date_enregistrementTri', 'id', 'id2');
			
		/* Indexed column (used for fast and accurate table cardinality) */
		$sIndexColumn = "id";
			
		/*
		 * Paging
		*/
		$sLimit = array();
		if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
		{
			$sLimit[0] = $_GET['iDisplayLength'];
			$sLimit[1] = $_GET['iDisplayStart'];
		}
			
		/*
		 * Ordering
		*/
		if ( isset( $_GET['iSortCol_0'] ) )
		{
			$sOrder = array();
			$j = 0;
			for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
			{
				if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
				{
					$sOrder[$j++] = $aColumns[ intval( $_GET['iSortCol_'.$i] ) ]."
								 	".$_GET['sSortDir_'.$i];
				}
			}
		}
			
		$sql2 = new Sql ($db );
		$subsel = $sql2->select ();
		$subsel->from ( array ( 'bp' => 'bilan_prelevement_repris' ) );
		$subsel->columns (array ( 'idbilan' ) );
		
		/*
		 * SQL queries
		* Liste des patients admis pour lesquels les demandes facturées ont déjà un bilan
		*/
		$sql = new Sql($db);
		$sQuery = $sql->select()
		->from(array('pat' => 'patient'))->columns(array('*'))
		->join(array('pers' => 'personne'), 'pers.idpersonne = pat.idpersonne' , array('Nom'=>'nom','Prenom'=>'prenom','Datenaissance'=>'date_naissance','Sexe'=>'sexe', 'Telephone'=>'telephone','Adresse'=>'adresse','Nationalite'=>'nationalite_actuelle', 'id'=>'idpersonne', 'id2'=>'idpersonne', 'id3'=>'idpersonne') )
		->join(array('fact' => 'facturation'), 'fact.idpatient = pat.idpersonne', array('Idfacturation' => 'idfacturation', 'DateFact' => 'date', 'HeureFact' => 'heure') )
		->join(array('bp' => 'bilan_prelevement'), 'bp.idfacturation = fact.idfacturation', array('Idbilan' => 'idbilan', 'Date' => 'date_enregistrement', 'Rappel_patient' => 'rappel_patient') )
		->join(array('tp' => 'tri_prelevement'), 'tp.idbilan = bp.idbilan', array('IdbilanTri' => 'idbilan', 'Date_enregistrementTri' => 'date_enregistrement') )
		->where(array('tp.conformite' => 0 , new NotIn( 'bp.idbilan', $subsel ) ))
		->group('pat.idpersonne')
		->order('idtri ASC');
			
		/* Data set length after filtering */
		$stat = $sql->prepareStatementForSqlObject($sQuery);
		$rResultFt = $stat->execute();
		$iFilteredTotal = count($rResultFt);
			
		$rResult = $rResultFt;
			
		$output = array(
				//"sEcho" => intval($_GET['sEcho']),
				//"iTotalRecords" => $iTotal,
				"iTotalDisplayRecords" => $iFilteredTotal,
				"aaData" => array()
		);
			
		/*
		 * $Control pour convertir la date en franï¿½ais
		*/
		$Control = new DateHelper();
			
		/*
		 * ADRESSE URL RELATIF
		*/
		$baseUrl = $_SERVER['REQUEST_URI'];
		$tabURI  = explode('public', $baseUrl);
			
		/*
		 * Prï¿½parer la liste
		*/
		foreach ( $rResult as $aRow )
		{
			$row = array();
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				if ( $aColumns[$i] != ' ' )
				{
					/* General output */
					if ($aColumns[$i] == 'Nom'){
						$row[] = "<div id='nomMaj'>".$aRow[ $aColumns[$i]]."</div>";
					}
	
					else if ($aColumns[$i] == 'Prenom'){
						$row[] = "<div>".$aRow[ $aColumns[$i]]."</div>";
					}
	
					else if ($aColumns[$i] == 'Datenaissance') {
							
						$date_naissance = $aRow[ $aColumns[$i] ];
						if($date_naissance){ $row[] = $Control->convertDate($aRow[ $aColumns[$i] ]); }else{ $row[] = null;}
	
					}
					
					else if ($aColumns[$i] == 'Telephone') {
							
						$infosMaman = $this->getParentPatient($aRow[ 'id' ]);

						$row[] = $infosMaman['telephone']; 
					
					}
	
					else if ($aColumns[$i] == 'Date_enregistrementTri') {
						$row[] = $Control->convertDateTimeHm($aRow[ 'Date_enregistrementTri' ]);
					}
	
					else if ($aColumns[$i] == 'id') {

						$html  ="<infoBulleVue> <a id='".$aRow[ 'id' ]."' href='javascript:visualiser(".$aRow[ 'id' ].");'>";
						$html .="<img style='display: inline; margin-right: 14%;' src='".$tabURI[0]."public/images_icons/voir2.png' title='d&eacute;tails'></a></infoBulleVue>";
						
						if ($aRow['Rappel_patient'] == 1) {
						
							$html .= "<span class='rappel_patient_".$aRow[ 'Idbilan' ]."' ><a id='rappel_patient_".$aRow[ 'Idbilan' ]."' href='javascript:patientNonRappeler(".$aRow[ 'Idbilan' ].")' >";
							$html .="<img style='display: inline; margin-right: 5%;' src='".$tabURI[0]."public/images_icons/applik.png' ></a></span>";
						
						}else{
							$html .= "<span class='rappel_patient_".$aRow[ 'Idbilan' ]."' ><a id='rappel_patient_".$aRow[ 'Idbilan' ]."' href='javascript:patientRappeler(".$aRow[ 'Idbilan' ].")' >";
							$html .="<img style='display: inline; margin-right: 5%;' src='".$tabURI[0]."public/images_icons/appel-telephone-25.png' ></a></span>";
						}
						
						$row[] = $html;
					}
	
					else {
						$row[] = $aRow[ $aColumns[$i] ];
					}
	
				}
			}
			$output['aaData'][] = $row;
		}
		return $output;
	}
	
	
	
	
	
	
	
	
	//GESTION DE LA LISTE DES PROFILS DREPANOCYTAIRES 
	//GESTION DE LA LISTE DES PROFILS DREPANOCYTAIRES
	
	/**
	 * Recuperer la liste des profils drépanocytaires
	 * @return \Zend\Db\Adapter\Driver\ResultInterface
	 */
	public function getTypagesHemoglobine(){
		$adapter = $this->tableGateway->getAdapter ();
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from(array('t'=>'typage_hemoglobine'));
		$select->columns(array( 'Idtypage' =>'idtypage' , 'Designation' =>'designation'));
		
		return $sql->prepareStatementForSqlObject($select)->execute();
	}
	
	//Recuperer la facture de la demande (iddemande)
	public function getFactureDelaDemande($iddemande){
		$db = $this->tableGateway->getAdapter();
			
		$sql = new Sql ($db );
		$subselect = $sql->select ();
		$subselect->from ( array ( 'fact' => 'facturation_demande_analyse' ) );
		$subselect->columns (array ( '*' ) );
		$subselect->where(array('iddemande_analyse' => $iddemande ) );
	
		return $sql->prepareStatementForSqlObject($subselect)->execute()->current();
	}
	
	//Annuler la demande s'il n'est pas encore facturée
	public function annulerAnalyseDemandee($iddemande){
		
		if(!$this->getFactureDelaDemande($iddemande)){
			$db = $this->tableGateway->getAdapter();
			$sql = new Sql($db);
			$sQuery = $sql->delete()->from('demande_analyse')
			->where(array( 'iddemande' => $iddemande ));
			$sql->prepareStatementForSqlObject($sQuery) ->execute();
		}
		
	}
	
	
}

