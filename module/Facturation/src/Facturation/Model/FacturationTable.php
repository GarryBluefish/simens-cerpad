<?php

namespace Facturation\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Predicate\In;
use Zend\Crypt\PublicKey\Rsa\PublicKey;
use Infirmerie\View\Helper\DateHelper;

class FacturationTable {
	protected $tableGateway;
 	public function __construct(TableGateway $tableGateway) {
 		$this->tableGateway = $tableGateway;
 	}
 	
 	
 	public function addFacturation($donnees){
 	    return $this->tableGateway->getLastInsertValue( $this->tableGateway->insert($donnees) );
 	}
 	
 	
 	public function addAnalyses($idfacturation , $liste_demandes_analyses){
 	    $db = $this->tableGateway->getAdapter();
 	    $sql = new Sql($db);

 	    for($i = 0 ; $i < count($liste_demandes_analyses) ;  $i++){
 	        $sQuery = $sql->insert()->into('facturation_demande_analyse') ->values(array('idfacturation' => $idfacturation, 'iddemande_analyse' => $liste_demandes_analyses[$i]));
 	        $sql->prepareStatementForSqlObject($sQuery)->execute();
 	        
 	        //Mise à jour de la table 'demande_analyse' pour mentionner que la demande est facturée
 	        $db2 = $this->tableGateway->getAdapter();
 	        $sql2 = new Sql($db2);
 	        $sQuery2 = $sql->update()->table('demande_analyse')
 	        ->set(  array( 'facturer' => 1 )  )->where(array('iddemande' => $liste_demandes_analyses[$i] ));
 	        $sql2->prepareStatementForSqlObject($sQuery2)->execute();
 	    }

 	}
 	
 	public function addFacturationConsultation($donnees){
 		$db = $this->tableGateway->getAdapter();
 		$sql = new Sql($db);
 	
 		$sQuery = $sql->insert()->into('facturation_cons') 
 		->values($donnees);
 		$sql->prepareStatementForSqlObject($sQuery)->execute();
 	
 	}
 	
 	public function getDerniereFacturation(){
 		$db = $this->tableGateway->getAdapter();
		$sql = new Sql($db);
		$sQuery = $sql->select('facturation')
		->order('idfacturation DESC');
		$requete = $sql->prepareStatementForSqlObject($sQuery);
		return $requete->execute()->current();
 	}
 	
 	public function getDerniereFacturationConsultation(){
 		$db = $this->tableGateway->getAdapter();
 		$sql = new Sql($db);
 		$sQuery = $sql->select('facturation_cons')
 		->order('idfacturation DESC');
 		$requete = $sql->prepareStatementForSqlObject($sQuery);
 		return $requete->execute()->current();
 	}
 	
 	
 	public function getFacturation($idfacturation){
 		$db = $this->tableGateway->getAdapter();
 		$sql = new Sql($db);
 		$sQuery = $sql->select('facturation')
 		->where(array('idfacturation' => $idfacturation));
 		$requete = $sql->prepareStatementForSqlObject($sQuery);
 		return $requete->execute()->current();
 	}
 	
 	public function getFacturationConsultation($idfacturation){
 		$db = $this->tableGateway->getAdapter();
 		$sql = new Sql($db);
 		$sQuery = $sql->select('facturation_cons')
 		->where(array('idfacturation' => $idfacturation));
 		$requete = $sql->prepareStatementForSqlObject($sQuery);
 		return $requete->execute()->current();
 	}
 	
 	public function verifierExisteResultatFacturation($idfacturation){
 		$adapter = $this->tableGateway->getAdapter ();
 		/*
 		 * SQL queries
 		* Liste des resultats des demandes d'analyses
 		*/
 		$sql2 = new Sql ($adapter );
 		$subselect = $sql2->select ();
 		$subselect->from ( array ( 'r' => 'resultat_demande_analyse' ) );
 		$subselect->columns (array ( 'iddemande_analyse' ) );
 		
 		
 		$sql = new Sql ( $adapter );
 		$select = $sql->select ( );
 		$select->from(array('fact' => 'facturation'))->columns(array('*'))
 		->join(array('factda' => 'facturation_demande_analyse'), 'factda.idfacturation = fact.idfacturation', array( '*' ) )
 		->where( array ( 'fact.idfacturation' => $idfacturation, new In ( 'factda.iddemande_analyse', $subselect ) ) );
 	
 		return $sql->prepareStatementForSqlObject ( $select )->execute ()->current();
 	}
 	
 	/**
 	 * Verifier existence de prélèvements pour la facturation
 	 * @param $idfacturation
 	 */
 	public function verifierExistePrelevementFacturation($idfacturation){
 		$db = $this->tableGateway->getAdapter();
 		$sql = new Sql ($db );
 		$select = $sql->select ();
 		$select->from ( array ( 'bp' => 'bilan_prelevement' ) );
 		$select->columns (array ( 'idfacturation' ) )
 		->where( array ( 'bp.idfacturation' => $idfacturation ) );
 		
 		return $sql->prepareStatementForSqlObject ( $select )->execute ()->current();
 	}
 	
 	public function deleteFacturation($idfacturation){
 		//if($this->verifierExisteResultatFacturation($idfacturation)){
 		if($this->verifierExistePrelevementFacturation($idfacturation)){
 			return 1;
 		} else {
 			$this->annulerFacturation($idfacturation);
 			$this->tableGateway->delete(array('idfacturation'=> $idfacturation));
 			return 0;
 		}
 	}

 	public function annulerFacturation($idfacturation){
 		$db  = $this->tableGateway->getAdapter();
 		$sql = new Sql($db);
 		
 		$listeDemandesFacturees = $this->getListeDemandesFacturees($idfacturation);
 		foreach ($listeDemandesFacturees as $liste){
 			$sQuery = $sql->update()->table('demande_analyse')
 			->set(  array( 'facturer' => 0 )  )->where(array('iddemande' => $liste['iddemande_analyse'] ));
 			$sql->prepareStatementForSqlObject($sQuery)->execute();
 		}
 	}
 	
 	public function getListeDemandesFacturees($idfacturation){
 		$adapter = $this->tableGateway->getAdapter();
 		$sql = new Sql ( $adapter );
 		$select = $sql->select ( );
 		$select->from(array('fact' => 'facturation'))->columns(array('*'))
 		->join(array('factda' => 'facturation_demande_analyse'), 'factda.idfacturation = fact.idfacturation', array( '*' ) )
 		->where( array ( 'fact.idfacturation' => $idfacturation ) );
 		return $sql->prepareStatementForSqlObject($select)->execute();
 	}
 	
 	
 	
 	
 	
 	
 	
 	
 	public function verifierExisteConsultation($idfacturation){
 		$adapter = $this->tableGateway->getAdapter ();
 		$sql = new Sql ( $adapter );
 		$select = $sql->select ( );
 		$select->from(array('cons' => 'consultation'))->columns(array('*'))
 		->where( array ( 'cons.idfacturation' => $idfacturation) );
 		
 		return $sql->prepareStatementForSqlObject ( $select )->execute ()->current();
 	} 		
 	
 	public function deleteFacturationConsultation($idfacturation){
 		if($this->verifierExisteConsultation($idfacturation)){
 			return 1;
 		} else {
 			$db = $this->tableGateway->getAdapter();
 			$sql = new Sql($db);
 			$sQuery = $sql->delete()
 			->from('facturation_cons')
 			->where(array('idfacturation' => $idfacturation));
 			$sql->prepareStatementForSqlObject ( $sQuery )->execute ();
 			
 			return 0;
 		}
 	}
 	
 	
 	public function getListeAnalysesFacturees($idfacturation){
 		$db = $this->tableGateway->getAdapter();
 		$sql = new Sql($db);
 		$sQuery = $sql->select()
 		->from(array('f' => 'facturation'))->columns(array('*'))
 		->join(array('fda' => 'facturation_demande_analyse'), 'f.idfacturation = fda.idfacturation', array('*'))
 		->join(array('d' => 'demande_analyse'), 'd.iddemande = fda.iddemande_analyse', array('*'))
 		->join(array('a' => 'analyse'), 'a.idanalyse = d.idanalyse', array('*'))
 		->join(array('t' => 'type_analyse'), 't.idtype = a.idtype_analyse', array('*'))
 		->where(array('f.idfacturation' => $idfacturation))
 		->order(array('t.idtype' => 'ASC', 'd.idanalyse' => 'ASC'));
 		
 		$resultat = $sql->prepareStatementForSqlObject($sQuery)->execute();
 		
 		$donnees = array();
 		foreach ($resultat as $result){
 			$donnees[] = $result;
 		}
 		
 		return $donnees;
 	}
 	
 	
 	public function getListeOrganisme(){
 		$db = $this->tableGateway->getAdapter();
 		$sql = new Sql($db);
 		$sQuery = $sql->select('organisme')
 		->order('idorganisme ASC');
 		$requete = $sql->prepareStatementForSqlObject($sQuery);
 		$result = $requete->execute();
 		
 		$options = array(null);
 		
 		foreach ($result as $data) {
 			$options[$data['idorganisme']] = $data['libelle'];
 		}
 		
 		return $options;
 	}
 	
 	
 	public function getOrganisme($idorganisme){
 		$db = $this->tableGateway->getAdapter();
 		$sql = new Sql($db);
 		$sQuery = $sql->select('organisme')
 		->where(array('idorganisme' => $idorganisme));
 		$requete = $sql->prepareStatementForSqlObject($sQuery);
 		return $requete->execute() ->current();
 	}
 	
 	//RECUPERER LA LISTE DES FACTURES FAISANT L'OBJET DE PRELEVEMENT
 	public function getListeFacturesDuPatient($idpersonne){
 		$db = $this->tableGateway->getAdapter();
 		
 		$sql2 = new Sql ($db );
 		$subselect = $sql2->select ();
 		$subselect->from ( array ( 'bp' => 'bilan_prelevement' ) );
 		$subselect->columns (array ( 'idfacturation' ) );
 		
 		$aujourdhui = (new \DateTime ("now"))->format ( 'Y-m-d' );
 			
 		/*
 		 * SQL queries
 		* Liste des patients admis pour lesquels les demandes facturées n'ont pas encore de bilan
 		*/
 		$sql = new Sql($db);
 		$sQuery = $sql->select()
 		->from(array('pat' => 'patient'))->columns(array('*'))
 		->join(array('pers' => 'personne'), 'pers.idpersonne = pat.idpersonne' , array('Nom'=>'nom','Prenom'=>'prenom','Datenaissance'=>'date_naissance','Sexe'=>'sexe','Adresse'=>'adresse','Nationalite'=>'nationalite_actuelle', 'id'=>'idpersonne', 'id2'=>'idpersonne') )
 		->join(array('fact' => 'facturation'), 'fact.idpatient = pat.idpersonne', array('*') )
 		->join(array('pers2' => 'personne'), 'pers2.idpersonne = fact.idemploye' , array('Nom'=>'nom','Prenom'=>'prenom') )
 		->where( array ('fact.date != ?' => $aujourdhui, 'pat.idpersonne' => $idpersonne, new In ( 'fact.idfacturation', $subselect ) ) )
 		->order('fact.idfacturation DESC');
 		
 		return $sql->prepareStatementForSqlObject($sQuery)->execute();
 	}
 	
 	
 	
 	//Utiliser uniquement dans le module Infirmerie
 	//Utiliser uniquement dans le module Infirmerie
 	public function getListeAnalysesFactureesPourInfirmerie($idfacturation){
 		$db = $this->tableGateway->getAdapter();
 		$sql = new Sql($db);
 		$sQuery = $sql->select()
 		->from(array('f' => 'facturation'))->columns(array('*'))
 		->join(array('fda' => 'facturation_demande_analyse'), 'f.idfacturation = fda.idfacturation', array('*'))
 		->join(array('d' => 'demande_analyse'), 'd.iddemande = fda.iddemande_analyse', array('*'))
 		->join(array('a' => 'analyse'), 'a.idanalyse = d.idanalyse', array('*'))
 		->join(array('t' => 'type_analyse'), 't.idtype = a.idtype_analyse', array('*'))
 		->join(array('tb' => 'tube'), 'tb.idtube = a.idtube', array('Idtube' =>'idtube', 'LibelleTube' =>'libelle'))
 		->where(array('f.idfacturation' => $idfacturation))
 		->order(array('t.idtype' => 'ASC', 'd.idanalyse' => 'ASC'));
 			
 		$resultat = $sql->prepareStatementForSqlObject($sQuery)->execute();
 			
 		$donnees = array();
 		foreach ($resultat as $result){
 			$donnees[] = $result;
 		}
 			
 		return $donnees;
 	}
 	
 	
 	public function getInfoPersonne($idemploye) {
 		$db = $this->tableGateway->getAdapter();
 		$sql = new Sql($db);
 		$sQuery = $sql->select()
 		->from(array('e' => 'employe'))->columns( array( '*' ))
 		->join(array('p' => 'personne'), 'p.idpersonne = e.idpersonne' , array('*'))
 		->where(array('e.idpersonne' => $idemploye));
 	
 		$stat = $sql->prepareStatementForSqlObject($sQuery);
 		$resultat = $stat->execute()->current();
 	
 		return $resultat;
 	}
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	//Utiliser dans le module de la consultation
 	//Utiliser dans le module de la consultation
 	public function deleteMotifsExamensRadio($idcons){
 		$db = $this->tableGateway->getAdapter();
 		$sql = new Sql($db);
 		$sQuery = $sql->delete()->from('motif_examens_radio_demandes')->where(array('idcons' => $idcons));
 		$sql->prepareStatementForSqlObject($sQuery)->execute();
 	}
 	
 	
 	public function addMotifsExamensRadioDemandes($tabDonnees, $idemploye){
 		$this->deleteMotifsExamensRadio($tabDonnees['idcons']);
 		
 		$db = $this->tableGateway->getAdapter();
 		$sql = new Sql($db);
 		$nbExamenRadio = $tabDonnees['nbExamenRadio'];
 		
 		for($i = 0 ; $i < $nbExamenRadio ;  $i++){
 			$donnee = array('idcons' => $tabDonnees['idcons'], 'idexamen' => $tabDonnees['idExamenRadio_'.$i], 'motif' => $tabDonnees['motifExamenRadio_'.$i], 'idemploye' => $idemploye);
 			$sQuery = $sql->insert()->into('motif_examens_radio_demandes')->values($donnee);
 			$sql->prepareStatementForSqlObject($sQuery)->execute();
 		}
 	
 	}
 	
 	
 	public function deleteMotifsExamensBio($idcons){
 		$db = $this->tableGateway->getAdapter();
 		$sql = new Sql($db);
 		$sQuery = $sql->delete()->from('motif_examens_bio_demandes')->where(array('idcons' => $idcons));
 		$sql->prepareStatementForSqlObject($sQuery)->execute();
 	}
 	
 	
 	public function addMotifsExamensBioDemandes($tabDonnees, $idemploye){
 		$this->deleteMotifsExamensBio($tabDonnees['idcons']);
 		
 		$db = $this->tableGateway->getAdapter();
 		$sql = new Sql($db);
 		$nbExamenBio = $tabDonnees['nbExamenBio'];
 			
 		for($i = 0 ; $i < $nbExamenBio ;  $i++){
 			$donnee = array('idcons' => $tabDonnees['idcons'], 'idexamen' => $tabDonnees['idExamenBio_'.$i], 'motif' => $tabDonnees['motifExamenBio_'.$i], 'idemploye' => $idemploye);
 			$sQuery = $sql->insert()->into('motif_examens_bio_demandes')->values($donnee);
 			$sql->prepareStatementForSqlObject($sQuery)->execute();
 		}
 	
 	}
 	
 	
 	public function getMotifsExamensRadioDemandes($idcons){
 		$db = $this->tableGateway->getAdapter();
 		$sql = new Sql($db);
 		$sQuery = $sql->select()->from('motif_examens_radio_demandes')->where(array('idcons' => $idcons));
 		$resultat = $sql->prepareStatementForSqlObject($sQuery)->execute();
 		
 		$listeMotifs = array();
 		foreach ($resultat as $result){
 			$listeMotifs[] = array($result['idexamen'], $result['motif']);
 		}
 		return $listeMotifs;
 	}
 	
 	
 	public function getMotifsExamensBioDemandes($idcons){
 		$db = $this->tableGateway->getAdapter();
 		$sql = new Sql($db);
 		$sQuery = $sql->select()->from('motif_examens_bio_demandes')->where(array('idcons' => $idcons));
 		$resultat = $sql->prepareStatementForSqlObject($sQuery)->execute();
 			
 		$listeMotifs = array();
 		foreach ($resultat as $result){
 			$listeMotifs[] = array($result['idexamen'], $result['motif']);
 		}
 		return $listeMotifs;
 	}
 	
 	
 	public function deleteRendezVous($idcons){
 		$db = $this->tableGateway->getAdapter();
 		$sql = new Sql($db);
 		$sQuery = $sql->delete()->from('rendez_vous_cons')->where(array('idcons' => $idcons));
 		$sql->prepareStatementForSqlObject($sQuery)->execute();
 	}
 	
 	
 	public function addRendezVous($tabDonnees, $idemploye){

 		$dateRendezvous = $tabDonnees['dateHeureRendezVous'];
 		$Convert = new DateHelper();
 		$DateRv = $Convert->convertDateInAnglais($Convert->decouperDateInDatepicker($dateRendezvous));
 		$HeureRv = $Convert->decouperHeureInDatepicker($dateRendezvous);
 		$motifRendezVous = $tabDonnees['motifRendezVous'];
 		
 		$donneesRV = array(
 				'idcons'    => $tabDonnees['idcons'], 
 				'dateRv'    => $DateRv,
 				'heureRv'   => $HeureRv,
 				'motifRv'   => $motifRendezVous,
 				'idemploye' => $idemploye,
 		);
 		
 		$this->deleteRendezVous($tabDonnees['idcons']);
 		
 		$db = $this->tableGateway->getAdapter();
 		$sql = new Sql($db);
 		$sQuery = $sql->insert()->into('rendez_vous_cons')->values($donneesRV);
 		$sql->prepareStatementForSqlObject($sQuery)->execute();
 	}
 	
 	
}

