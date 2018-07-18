<?php

namespace Facturation\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;
use Zend\Db\Sql\Predicate\NotIn;
use Facturation\View\Helper\DateHelper;
use Zend\Db\Sql\Predicate\In;
use Zend\Crypt\PublicKey\Rsa\PublicKey;

class PatientTable {
	protected $tableGateway;
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}
	public function fetchAll() {
		$resultSet = $this->tableGateway->select ();
		return $resultSet;
	}
	
	public function getPatient($id) {
		$id = ( int ) $id;
		$rowset = $this->tableGateway->select ( array (
				'idpersonne' => $id
		) );
		$row =  $rowset->current ();
		if (! $row) {
			return null;
		}
		return $row;
	}
	
	public function getInfoPatient($idpersonne) {
		$db = $this->tableGateway->getAdapter();
		$sql = new Sql($db);
		$sQuery = $sql->select()
		->from(array('pat' => 'patient'))
		->columns( array( '*' ))
		->join(array('pers' => 'personne'), 'pers.idpersonne = pat.idpersonne' , array('*'))
		->where(array('pat.idpersonne' => $idpersonne));
		
		$stat = $sql->prepareStatementForSqlObject($sQuery);
		$resultat = $stat->execute()->current();
		
		return $resultat;
	}
	
	//********** RECUPERER LA LISTE DES DEMANDES DES PATIENTS POUR LES ADMISSIONS *********
	//********** RECUPERER LA LISTE DES DEMANDES DES PATIENTS POUR LES ADMISSIONS*********
	public function getListeDemandesDesPatients(){

	    $db = $this->tableGateway->getAdapter();
	    
	    $aColumns = array('numero_dossier','Nom','Prenom','Datenaissance', 'Adresse', 'id', 'Idpatient');
	    
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
	    
	    /*
	     * SQL queries
	     * Liste des resultats des demandes d'analyses
	    */
	    $sql2 = new Sql ($db );
	    $subselect = $sql2->select ();
	    $subselect->from ( array ( 'fact' => 'facturation_demande_analyse' ) );
	    $subselect->columns (array ( 'iddemande_analyse' ) );
	    
	    $date = new \DateTime ("now");
	    $dateDuJour = $date->format ( 'Y-m-d' );
	    
	    /*
	     * SQL queries
	     * Liste des demandes d'analyses sauf celles ayant déjà des résultats
	     */
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('dem' => 'demande_analyse'))->columns(array('*'))
	    ->join(array('pat' => 'patient'), 'pat.idpersonne = dem.idpatient', array('*'))
	    ->join(array('pers' => 'personne'), 'pers.idpersonne = pat.idpersonne', array('Nom'=>'nom','Prenom'=>'prenom','Datenaissance'=>'date_naissance','Sexe'=>'sexe','Adresse'=>'adresse','Nationalite'=>'nationalite_actuelle', 'id'=>'idpersonne','Idpatient'=>'idpersonne'))
	    ->where( array ( new NotIn ( 'dem.iddemande', $subselect ) ) )
	    ->group('pat.idpersonne')
	    ->order('dem.iddemande ASC');
	    
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
	    
	                else if ($aColumns[$i] == 'Adresse') {
	                    $row[] = "<div class='adresseText' >".$aRow[ $aColumns[$i] ]."</div>";
	                }
	    
	                else if ($aColumns[$i] == 'id') {
	                    $html  = "<infoBulleVue> <a href='javascript:visualiser(".$aRow[ $aColumns[$i] ].")' >";
	                    $html .= "<img style='margin-left: 5%; margin-right: 15%;' src='".$tabURI[0]."public/images_icons/voir2.png' title='d&eacute;tails'></a> </infoBulleVue>";
	    
	                    $html .= "<infoBulleVue> <a href='javascript:admettre(".$aRow[ $aColumns[$i] ].")' >";
	                    $html .= "<img style='display: inline; margin-right: 5%;' src='".$tabURI[0]."public/images_icons/transfert_droite.png' title='suivant'></a> </infoBulleVue>";
	    
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
	
	
	//********** RECUPERER LA LISTE DES PATIENTS POUR LES ADMISSIONS DES CONSULTATIONS JOURNALIERES *********
	//********** RECUPERER LA LISTE DES PATIENTS POUR LES ADMISSIONS DES CONSULTATIONS JOURNALIERES *********
	public function getListeDesPatientsAdmissionConsultation(){
	
		$db = $this->tableGateway->getAdapter();
		 
		$aColumns = array('numero_dossier','Nom','Prenom','Datenaissance', 'Adresse', 'id','Idpatient');
		 
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
		 
		
		$dateDuJour = (new \DateTime ("now")) ->format ( 'Y-m-d' );
		/*
		 * SQL queries
		* Liste des resultats des demandes d'analyses
		*/
		$sql2 = new Sql ($db );
		$subselect = $sql2->select ();
		$subselect->from ( array ( 'fact_cons' => 'facturation_cons' ) );
		$subselect->columns (array ( 'idpatient' ) );
		$subselect->where( array( 'date' => $dateDuJour) );
		 
		/*
		 * SQL queries
		* Liste des demandes d'analyses sauf celles ayant déjà des résultats
		*/
		$sql = new Sql($db);
		$sQuery = $sql->select()
		->from(array('pat' => 'patient')) ->columns(array('*'))
		->join(array('pers' => 'personne'), 'pers.idpersonne = pat.idpersonne', array('Nom'=>'nom','Prenom'=>'prenom','Datenaissance'=>'date_naissance','Sexe'=>'sexe','Adresse'=>'adresse','Nationalite'=>'nationalite_actuelle', 'id'=>'idpersonne','Idpatient'=>'idpersonne'))
		->join(array('dep' => 'depistage'), 'dep.idpatient = pat.idpersonne', array('*'))
		->where( array ('typepatient' => 1, 'valide' => 1, new NotIn ( 'pat.idpersonne', $subselect ) ) )
		->order('nom ASC');
		 
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
					 
					else if ($aColumns[$i] == 'Adresse') {
						$row[] = "<div class='adresseText' >".$aRow[ $aColumns[$i] ]."</div>";
					}
					 
					else if ($aColumns[$i] == 'id') {
						$html  = "<infoBulleVue> <a href='javascript:visualiser(".$aRow[ $aColumns[$i] ].")' >";
						$html .= "<img style='margin-left: 5%; margin-right: 15%;' src='".$tabURI[0]."public/images_icons/voir2.png' title='d&eacute;tails'></a> </infoBulleVue>";
						 
						$html .= "<infoBulleVue> <a href='javascript:admettreConsultation(".$aRow[ $aColumns[$i] ].")' >";
						$html .= "<img style='display: inline; margin-right: 5%;' src='".$tabURI[0]."public/images_icons/transfert_droite.png' title='suivant'></a> </infoBulleVue>";
						 
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
	
	
	protected function nbAnnees($debut, $fin) {
	    $nbSecondes = 60*60*24*365;
	    $debut_ts = strtotime($debut);
	    $fin_ts = strtotime($fin);
	    $diff = $fin_ts - $debut_ts;
	    return (int)($diff / $nbSecondes);
	}
	
	public function miseAJourAgePatient($idpatient) {

	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('pat' => 'patient'))
	    ->columns( array( '*' ))
	    ->join(array('pers' => 'personne'), 'pers.idpersonne = pat.idpersonne' , array('*'))
	    ->where(array('pat.idpersonne' => $idpatient));
	    $pat = $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	    
	    $today = (new \DateTime())->format('Y-m-d');
	    
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    	
	    $controle = new DateHelper();
	    
	    if($pat['date_naissance']){
	    
	     			//POUR LES AGES AVEC DATE DE NAISSANCE
	     			//POUR LES AGES AVEC DATE DE NAISSANCE
	        	
	     			$age = $this->nbAnnees($pat['date_naissance'], $today);
	    
	     			$donnees = array('age' => $age, 'date_modification' => $today);
	     			$sQuery = $sql->update()
	     			->table('personne')
	     			->set( $donnees )
	     			->where(array('idpersonne' => $pat['idpersonne'] ));
	     			$sql->prepareStatementForSqlObject($sQuery)->execute();
	     				
	    } else {
	    
	     			//POUR LES AGES SANS DATE DE NAISSANCE
	     			//POUR LES AGES SANS DATE DE NAISSANCE
	        	
	     			$age = $this->nbAnnees($controle->convertDateInAnglais($controle->convertDate($pat['date_modification'])), $today);
	    
	     			if($age != 0) {
	     			    $donnees = array('age' => $age+$pat['age'], 'date_modification' =>$today);
	     			    $sQuery = $sql->update()
	     			    ->table('personne')
	     			    ->set( $donnees )
	     			    ->where(array('idpersonne' => $pat['idpersonne'] ));
	     			    $sql->prepareStatementForSqlObject($sQuery)->execute();
	     			}
	    
	    }
	}
	
	public function getDepistagePatient($idpatient)
	{
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('dep' => 'depistage'))->columns(array('*'))
	    ->where(array('dep.idpatient' => $idpatient));
	    return $sql->prepareStatementForSqlObject($sQuery)->execute();
	}
	
	public function getDepistagePatientTableau()
	{
		$db = $this->tableGateway->getAdapter();
		$sql = new Sql($db);
		$sQuery = $sql->select()
		->from(array('dep' => 'depistage'))->columns(array('*'))
		->where(array('dep.typepatient' => 1));
		$result = $sql->prepareStatementForSqlObject($sQuery) ->execute();
		
		$tableau = array();
		
		foreach ($result as $res){
			$tableau[] = $res['idpatient'];
		}
		
		return $tableau;
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
	
	//********** RECUPERER LA LISTE DES PATIENTS DEJA ADMIS *********
	//********** RECUPERER LA LISTE DES PATIENTS DEJA ADMIS *********
	
	// Cette liste c'est l'ensemble des patients admis pour lesquels leurs analyses n'ont pas encore un bilan
	public function getListePatientsAdmis(){

	    $db = $this->tableGateway->getAdapter();
	    
	    $aColumns = array('numero_dossier','Nom','Prenom','Datenaissance','Adresse', 'Date', 'id', 'Idfacturation');
	    
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
	    
	    /* C EST CE QUI ETAIT FAIT DANS UN PREMIER TEMPS AVANT LA PARTIE INFIRMERIE
 	     * SQL queries
	     * Liste des resultats des demandes d'analyses
	     */
	    //$sql2 = new Sql ($db );
	    //$subselect = $sql2->select ();
	    //$subselect->from ( array ( 'r' => 'resultat_demande_analyse' ) );
	    //$subselect->columns (array ( 'iddemande_analyse' ) );
	     
	    //$date = new \DateTime ("now");
	    //$dateDuJour = $date->format ( 'Y-m-d' );
	     
	    /*
	     * SQL queries
         * Liste des patients admis pour des analyses n'ayant pas encore de résultats
	     */
	    //$sql = new Sql($db);
	    //$sQuery = $sql->select()
	    //->from(array('pat' => 'patient'))->columns(array('*'))
	    //->join(array('pers' => 'personne'), 'pers.idpersonne = pat.idpersonne' , array('Nom'=>'nom','Prenom'=>'prenom','Datenaissance'=>'date_naissance','Sexe'=>'sexe','Adresse'=>'adresse','Nationalite'=>'nationalite_actuelle', 'id'=>'idpersonne', 'id2'=>'idpersonne'))
	    //->join(array('fact' => 'facturation'), 'fact.idpatient = pat.idpersonne', array('Idfacturation' => 'idfacturation', 'Date' => 'date', 'Heure' => 'heure') )
	    //->join(array('factda' => 'facturation_demande_analyse'), 'factda.idfacturation = fact.idfacturation', array() )
	    //->where( array ( new NotIn ( 'factda.iddemande_analyse', $subselect ) ) )
	    //->group('fact.idfacturation')
	    //->order('fact.idfacturation ASC');

	    /* PREMIERE LISTE
	     * SQL queries
	     * Liste des resultats des demandes d'analyses
	     */
	    $sql2 = new Sql ($db );
	    $subselect = $sql2->select ();
	    $subselect->from ( array ( 'bp' => 'bilan_prelevement' ) );
	    $subselect->columns (array ( 'idfacturation' ) );
	    
	    /*
	     * SQL queries
	     * Liste des patients admis pour lesquels les demandes facturées n'ont pas encore de bilan   
	     */
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('pat' => 'patient'))->columns(array('*'))
	    ->join(array('pers' => 'personne'), 'pers.idpersonne = pat.idpersonne' , array('Nom'=>'nom','Prenom'=>'prenom','Datenaissance'=>'date_naissance','Sexe'=>'sexe','Adresse'=>'adresse','Nationalite'=>'nationalite_actuelle', 'id'=>'idpersonne', 'id2'=>'idpersonne'))
	    ->join(array('fact' => 'facturation'), 'fact.idpatient = pat.idpersonne', array('Idfacturation' => 'idfacturation', 'Date' => 'date', 'Heure' => 'heure') )
	    ->where( array ( new NotIn ( 'fact.idfacturation', $subselect ) ) )
	    ->group('fact.idfacturation')
	    ->order('fact.idfacturation DESC');
	    
	    
	    /* Data set length after filtering */
	    $stat = $sql->prepareStatementForSqlObject($sQuery);
	    $rResultFt = $stat->execute();
	    $iFilteredTotal = count($rResultFt);
	    
	    //var_dump($iFilteredTotal); exit();
	    
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
	     * Prï¿½parer la première liste
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
	    
	                else if ($aColumns[$i] == 'Adresse') {
	                    $row[] = "<div class='adresseText' >".$aRow[ $aColumns[$i] ]."</div>";
	                }
	    
	                else if ($aColumns[$i] == 'Date') {
	                	$row[] = "<div>".$Control->convertDate($aRow[ $aColumns[$i] ]).' - '.$Control->decouperTimeHm($aRow[ 'Heure' ])."</div>"; 
	                }
	                
	                else if ($aColumns[$i] == 'id') {
	                    $html ="<infoBulleVue> <a id='".$aRow[ $aColumns[$i] ]."' href='javascript:listeAnalysesFacturees(".$aRow[ 'Idfacturation' ].");'>";
	                    $html .="<img style='display: inline; margin-right: 10%;' src='".$tabURI[0]."public/images_icons/voir2.png' title='d&eacute;tails'></a></infoBulleVue>";
	    
	                    $html .= "<infoBulleVue> <a id='".$aRow[ 'Idfacturation' ]."' href='javascript:supprimer(".$aRow[ 'Idfacturation' ].");'>";
	                    $html .="<img style='display: inline;' src='".$tabURI[0]."public/images_icons/symbol_supprimer.png' title='Annuler'></a></infoBulleVue>";
	                    
	                    $row[] = $html;
	                }
	    
	                else {
	                    $row[] = $aRow[ $aColumns[$i] ];
	                }
	    
	            }
	        }
	        $output['aaData'][] = $row;
	    }
	    
	    
	    
	    
	    
	    //DEUXIEME PARTIE CONCERNANT LES FACTURATIONS POUR LESQUELLES LES PRELEVEMENTS SONT DEJA FAITS 
	    //DEUXIEME PARTIE CONCERNANT LES FACTURATIONS POUR LESQUELLES LES PRELEVEMENTS SONT DEJA FAITS
	    /* PREMIERE LISTE
	     * SQL queries
	    * Liste des resultats des demandes d'analyses
	    */
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
	    ->join(array('pers' => 'personne'), 'pers.idpersonne = pat.idpersonne' , array('Nom'=>'nom','Prenom'=>'prenom','Datenaissance'=>'date_naissance','Sexe'=>'sexe','Adresse'=>'adresse','Nationalite'=>'nationalite_actuelle', 'id'=>'idpersonne', 'id2'=>'idpersonne'))
	    ->join(array('fact' => 'facturation'), 'fact.idpatient = pat.idpersonne', array('Idfacturation' => 'idfacturation', 'Date' => 'date', 'Heure' => 'heure') )
	    ->where( array ('fact.date' => $aujourdhui, new In ( 'fact.idfacturation', $subselect ) ) )
	    ->group('fact.idfacturation')
	    ->order('fact.idfacturation ASC');
	     
	     
	    /* Data set length after filtering */
	    $stat = $sql->prepareStatementForSqlObject($sQuery);
	    $rResultFt = $stat->execute();
	    $iFilteredTotal = count($rResultFt);
	     
	    $rResult = $rResultFt;
	    
	    /*
	     * Prï¿½parer la deuxième liste
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
	    			 
	    			else if ($aColumns[$i] == 'Adresse') {
	    				$row[] = "<div class='adresseText' >".$aRow[ $aColumns[$i] ]."</div>";
	    			}
	    			 
	    			else if ($aColumns[$i] == 'Date') {
	    				$row[] = $Control->convertDate($aRow[ $aColumns[$i] ]).' - '.$Control->decouperTimeHm($aRow[ 'Heure' ]);
	    			}
	    			 
	    			else if ($aColumns[$i] == 'id') {
	    				$html ="<infoBulleVue> <a id='".$aRow[ $aColumns[$i] ]."' href='javascript:listeAnalysesFacturees(".$aRow[ 'Idfacturation' ].");'>";
	    				$html .="<img style='display: inline; margin-right: 10%;' src='".$tabURI[0]."public/images_icons/voir2.png' title='d&eacute;tails'></a></infoBulleVue>";
	    				 
	    				$html .= "<infoBulleVue>";
	    				$html .="<img style='display: inline; margin-left: 10px;' src='".$tabURI[0]."public/images_icons/tick_16.png' title='pr&eacute;l&egrave;vements r&eacute;alis&eacute;s'></infoBulleVue>";
	    				 
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
	
	public function verifierExisteConsultation($idfacturation){
		$adapter = $this->tableGateway->getAdapter ();
		$sql = new Sql ( $adapter );
		$select = $sql->select ( );
		$select->from(array('cons' => 'consultation'))->columns(array('*'))
		->where( array ( 'cons.idfacturation' => $idfacturation) );
			
		return $sql->prepareStatementForSqlObject ( $select )->execute ()->current();
	}
	
	//********** RECUPERER LA LISTE DES PATIENTS DEJA ADMIS POUR CONSULTATION *********
	//********** RECUPERER LA LISTE DES PATIENTS DEJA ADMIS POUR CONSULTATION *********
	
	// Cette liste c'est l'ensemble des patients admis pour lesquels leurs analyses n'ont pas encore de résultats
	public function getListePatientsAdmisConsultation(){
	
		$db = $this->tableGateway->getAdapter();
		 
		$aColumns = array('numero_dossier','Nom','Prenom','Datenaissance','Adresse', 'Date', 'id', 'Idfacturation');
		 
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
		 
		$aujourdhui = (new \DateTime ("now"))->format ( 'Y-m-d' );
		
		/*
		 * SQL queries
		 */
		$sql = new Sql($db);
		$sQuery = $sql->select()
		->from(array('pat' => 'patient'))->columns(array('*'))
		->join(array('pers' => 'personne'), 'pers.idpersonne = pat.idpersonne' , array('Nom'=>'nom','Prenom'=>'prenom','Datenaissance'=>'date_naissance','Sexe'=>'sexe','Adresse'=>'adresse','Nationalite'=>'nationalite_actuelle', 'id'=>'idpersonne', 'id2'=>'idpersonne'))
		->join(array('fact' => 'facturation_cons'), 'fact.idpatient = pat.idpersonne', array('Idfacturation' => 'idfacturation', 'Date' => 'date', 'Heure' => 'heure') )
		->where( array ('date' => $aujourdhui) )
		->order('fact.idfacturation ASC');
		 
		 
		/* Data set length after filtering */
		$stat = $sql->prepareStatementForSqlObject($sQuery);
		$rResultFt = $stat->execute();
		$iFilteredTotal = count($rResultFt);
		 
		//var_dump($iFilteredTotal); exit();
		 
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
		 * Prï¿½parer la première liste
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
					 
					else if ($aColumns[$i] == 'Adresse') {
						$row[] = "<div class='adresseText' >".$aRow[ $aColumns[$i] ]."</div>";
					}
					 
					else if ($aColumns[$i] == 'Date') {
						$row[] = $Control->convertDate($aRow[ $aColumns[$i] ]).' - '.$Control->decouperTimeHm($aRow[ 'Heure' ]);
					}
					 
					else if ($aColumns[$i] == 'id') {
						$html ="<infoBulleVue> <a id='".$aRow[ $aColumns[$i] ]."' href='javascript:vueAdmissionConsultation(".$aRow[ 'Idfacturation' ].");'>";
						$html .="<img style='display: inline; margin-right: 10%;' src='".$tabURI[0]."public/images_icons/voir2.png' title='d&eacute;tails'></a></infoBulleVue>";
						 
						if(!$this->verifierExisteConsultation($aRow[ 'Idfacturation' ])){
							$html .= "<infoBulleVue> <a id='".$aRow[ 'Idfacturation' ]."' href='javascript:supprimer(".$aRow[ 'Idfacturation' ].");'>";
							$html .="<img style='display: inline;' src='".$tabURI[0]."public/images_icons/symbol_supprimer.png' title='Annuler'></a></infoBulleVue>";
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
		 
		 
		 
		 
		 
		//DEUXIEME PARTIE CONCERNANT LES FACTURATIONS POUR LESQUELLES LES PRELEVEMENTS SONT DEJA FAITS
		//DEUXIEME PARTIE CONCERNANT LES FACTURATIONS POUR LESQUELLES LES PRELEVEMENTS SONT DEJA FAITS
		/* PREMIERE LISTE
		 * SQL queries
		* Liste des resultats des demandes d'analyses
		*/
// 		$sql2 = new Sql ($db );
// 		$subselect = $sql2->select ();
// 		$subselect->from ( array ( 'bp' => 'bilan_prelevement' ) );
// 		$subselect->columns (array ( 'idfacturation' ) );
	
// 		$aujourdhui = (new \DateTime ("now"))->format ( 'Y-m-d' );
		 
		/*
		 * SQL queries
		* Liste des patients admis pour lesquels les demandes facturées n'ont pas encore de bilan
		*/
// 		$sql = new Sql($db);
// 		$sQuery = $sql->select()
// 		->from(array('pat' => 'patient'))->columns(array('*'))
// 		->join(array('pers' => 'personne'), 'pers.idpersonne = pat.idpersonne' , array('Nom'=>'nom','Prenom'=>'prenom','Datenaissance'=>'date_naissance','Sexe'=>'sexe','Adresse'=>'adresse','Nationalite'=>'nationalite_actuelle', 'id'=>'idpersonne', 'id2'=>'idpersonne'))
// 		->join(array('fact' => 'facturation'), 'fact.idpatient = pat.idpersonne', array('Idfacturation' => 'idfacturation', 'Date' => 'date', 'Heure' => 'heure') )
// 		->where( array ('fact.date' => $aujourdhui, new In ( 'fact.idfacturation', $subselect ) ) )
// 		->group('fact.idfacturation')
// 		->order('fact.idfacturation ASC');
	
	
		/* Data set length after filtering */
// 		$stat = $sql->prepareStatementForSqlObject($sQuery);
// 		$rResultFt = $stat->execute();
// 		$iFilteredTotal = count($rResultFt);
	
// 		$rResult = $rResultFt;
		 
		/*
		 * Prï¿½parer la deuxième liste
		*/
// 		foreach ( $rResult as $aRow )
// 		{
// 			$row = array();
// 			for ( $i=0 ; $i<count($aColumns) ; $i++ )
// 			{
// 				if ( $aColumns[$i] != ' ' )
// 				{
// 					/* General output */
// 					if ($aColumns[$i] == 'Nom'){
// 						$row[] = "<div id='nomMaj'>".$aRow[ $aColumns[$i]]."</div>";
// 					}
		    
// 					else if ($aColumns[$i] == 'Prenom'){
// 						$row[] = "<div>".$aRow[ $aColumns[$i]]."</div>";
// 					}
		    
// 					else if ($aColumns[$i] == 'Datenaissance') {
	
// 						$date_naissance = $aRow[ $aColumns[$i] ];
// 						if($date_naissance){ $row[] = $Control->convertDate($aRow[ $aColumns[$i] ]); }else{ $row[] = null;}
						 
// 					}
		    
// 					else if ($aColumns[$i] == 'Adresse') {
// 						$row[] = "<div class='adresseText' >".$aRow[ $aColumns[$i] ]."</div>";
// 					}
		    
// 					else if ($aColumns[$i] == 'Date') {
// 						$row[] = $Control->convertDate($aRow[ $aColumns[$i] ]).' - '.$Control->decouperTimeHm($aRow[ 'Heure' ]);
// 					}
		    
// 					else if ($aColumns[$i] == 'id') {
// 						$html ="<infoBulleVue> <a id='".$aRow[ $aColumns[$i] ]."' href='javascript:listeAnalysesFacturees(".$aRow[ 'Idfacturation' ].");'>";
// 						$html .="<img style='display: inline; margin-right: 10%;' src='".$tabURI[0]."public/images_icons/voir2.png' title='d&eacute;tails'></a></infoBulleVue>";
	
// 						$html .= "<infoBulleVue>";
// 						$html .="<img style='display: inline; margin-left: 10px;' src='".$tabURI[0]."public/images_icons/tick_16.png' title='pr&eacute;l&egrave;vements r&eacute;alis&eacute;s'></infoBulleVue>";
	
// 						$row[] = $html;
// 					}
		    
// 					else {
// 						$row[] = $aRow[ $aColumns[$i] ];
// 					}
		    
// 				}
// 			}
// 			$output['aaData'][] = $row;
// 		}
		 
		 
		return $output;
	}
	
	
	
	
	//********** RECUPERER LA LISTE DES HOSTORIQUES DES PATIENTS DEJA ADMIS *********
	//********** RECUPERER LA LISTE DES HISTORIQUES DES PATIENTS DEJA ADMIS *********
	
	public function getHistoriqueListePatientsAdmis(){
	
		$db = $this->tableGateway->getAdapter();
		 
		$aColumns = array('numero_dossier','Nom','Prenom','Datenaissance','Adresse', 'Date', 'id', 'id2');
		 
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
		 
		/* 
		 * SQL queries
		* Liste des resultats des demandes d'analyses
		*/
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
		->join(array('pers' => 'personne'), 'pers.idpersonne = pat.idpersonne' , array('Nom'=>'nom','Prenom'=>'prenom','Datenaissance'=>'date_naissance','Sexe'=>'sexe','Adresse'=>'adresse','Nationalite'=>'nationalite_actuelle', 'id'=>'idpersonne', 'id2'=>'idpersonne'))
		->join(array('fact' => 'facturation'), 'fact.idpatient = pat.idpersonne', array('Idfacturation' => 'idfacturation', 'Date' => 'date', 'Heure' => 'heure') )
		->where( array ('fact.date != ?' => $aujourdhui, new In ( 'fact.idfacturation', $subselect ) ) )
		//->group('fact.idfacturation')
		->group('pat.idpersonne')
		->order('fact.idfacturation DESC');
		
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
		 * Prï¿½parer la première liste
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
					 
					else if ($aColumns[$i] == 'Adresse') {
						$row[] = "<div class='adresseText' >".$aRow[ $aColumns[$i] ]."</div>";
					}
					 
					else if ($aColumns[$i] == 'Date') {
						$row[] = $Control->convertDate($aRow[ $aColumns[$i] ]).' - '.$Control->decouperTimeHm($aRow[ 'Heure' ]);
					}
					 
					else if ($aColumns[$i] == 'id') {
						$html ="<infoBulleVue> <a id='".$aRow[ $aColumns[$i] ]."' href='javascript:listeAnalysesFacturees(".$aRow[ 'id' ].");'>";
						$html .="<img style='display: inline; margin-right: 10%;' src='".$tabURI[0]."public/images_icons/voir2.png' title='d&eacute;tails'></a></infoBulleVue>";
						 
						$html .= "<infoBulleVue>";
	    				$html .="<img style='display: inline; margin-left: 10px;' src='".$tabURI[0]."public/images_icons/tick_16.png' title='pr&eacute;l&egrave;vements r&eacute;alis&eacute;s'></infoBulleVue>";
	    				
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
	
	
	
	
	//********** RECUPERER LA LISTE DES PATIENTS DEJA ADMIS POUR LE PRELEVEMENT *********
	//********** RECUPERER LA LISTE DES PATIENTS DEJA ADMIS POUR LE PRELEVEMENT *********
	public function getListePatientsAdmisPrelevement(){
	
		$db = $this->tableGateway->getAdapter();
		 
		$aColumns = array('numero_dossier','Nom','Prenom','Datenaissance', 'Adresse', 'Date', 'id', 'Idfacturation');
		 
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
		 
		/* LE NOUVEAU CODE AVEC LA PARTIE INFIRMERIE
		 * SQL queries
		* Liste des factures ayant un bilan
		*/
		$sql2 = new Sql ($db );
		$subselect = $sql2->select ();
		$subselect->from ( array ( 'bp' => 'bilan_prelevement' ) );
		$subselect->columns (array ( 'idfacturation' ) );
		 
		/*
		 * SQL queries
		* Liste des patients admis pour lesquels les demandes facturées n'ont pas encore de bilan
		*/
		$sql = new Sql($db);
		$sQuery = $sql->select()
		->from(array('pat' => 'patient'))->columns(array('*'))
		->join(array('pers' => 'personne'), 'pers.idpersonne = pat.idpersonne' , array('Nom'=>'nom','Prenom'=>'prenom','Datenaissance'=>'date_naissance','Sexe'=>'sexe','Adresse'=>'adresse','Nationalite'=>'nationalite_actuelle', 'id'=>'idpersonne', 'id2'=>'idpersonne'))
		->join(array('fact' => 'facturation'), 'fact.idpatient = pat.idpersonne', array('Idfacturation' => 'idfacturation', 'Date' => 'date', 'Heure' => 'heure') )
		->where( array ( new NotIn ( 'fact.idfacturation', $subselect ) ) )
		->group('fact.idfacturation')
		->order('fact.idfacturation ASC');
		 
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
					 
					else if ($aColumns[$i] == 'Adresse') {
						$row[] = "<div class='adresseText' >".$aRow[ $aColumns[$i] ]."</div>";
					}
					 
					else if ($aColumns[$i] == 'Date') {
						$row[] = $Control->convertDate($aRow[ $aColumns[$i] ]).' - '.$Control->decouperTimeHm($aRow[ 'Heure' ]);
					}
					 
					else if ($aColumns[$i] == 'id') {
						$html ="<infoBulleVue> <a id='".$aRow[ $aColumns[$i] ]."' href='javascript:listeAnalyses(".$aRow[ 'Idfacturation' ].");'>";
						$html .="<img style='display: inline; margin-right: 10%;' src='".$tabURI[0]."public/images_icons/voir2.png' title='d&eacute;tails'></a></infoBulleVue>";
						 
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
	
	
	public function verifierExistanceDansTriPrelevement($idbilan){
		$adapter = $this->tableGateway->getAdapter ();
		$sql = new Sql ( $adapter );
		$select = $sql->select ( );
		$select->from(array('tp' => 'tri_prelevement'))->columns(array('*'))
		->where( array ( 'idbilan' => $idbilan ) );
	
		return $sql->prepareStatementForSqlObject ( $select )->execute ()->current();
	}
	
	
	//********** RECUPERER LA LISTE DES BILANS DES PRELEVEMENTS *********
	//********** RECUPERER LA LISTE DES BILANS DES PRELEVEMENTS *********
	public function getListeBilansPrelevement(){
	
		$db = $this->tableGateway->getAdapter();
			
		$aColumns = array('numero_dossier','Nom','Prenom','Datenaissance','Adresse', 'Date', 'id', 'Idfacturation');
			
		/*
		 * SQL queries
		* Liste des patients admis pour lesquels les demandes facturées ont déjà un bilan
		*/
		$sql = new Sql($db);
		$sQuery = $sql->select()
		->from(array('pat' => 'patient'))->columns(array('*'))
		->join(array('pers' => 'personne'), 'pers.idpersonne = pat.idpersonne' , array('Nom'=>'nom','Prenom'=>'prenom','Datenaissance'=>'date_naissance','Sexe'=>'sexe','Adresse'=>'adresse','Nationalite'=>'nationalite_actuelle', 'id'=>'idpersonne', 'id2'=>'idpersonne'))
		->join(array('fact' => 'facturation'), 'fact.idpatient = pat.idpersonne', array('Idfacturation' => 'idfacturation', 'DateFact' => 'date', 'HeureFact' => 'heure') )
		->join(array('bp' => 'bilan_prelevement'), 'bp.idfacturation = fact.idfacturation', array('Idbilan' => 'idbilan', 'Date' => 'date_enregistrement') )
		->group('fact.idfacturation')
		->order('Idbilan DESC');
			
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
	
					else if ($aColumns[$i] == 'Adresse') {
						$row[] = "<div class='adresseText' >".$aRow[ $aColumns[$i] ]."</div>";
					}
	
					else if ($aColumns[$i] == 'Date') {
						$row[] = $Control->convertDateTimeHm($aRow[ $aColumns[$i] ]);
					}
	
					else if ($aColumns[$i] == 'id') {
						$html  ="<infoBulleVue> <a id='".$aRow[ $aColumns[$i] ]."' href='javascript:bilanPrelevement(".$aRow[ 'Idfacturation' ].");'>";
						$html .="<img style='display: inline; margin-right: 10%;' src='".$tabURI[0]."public/images_icons/voir2.png' title='d&eacute;tails'></a></infoBulleVue>";
							
						if(!$this->verifierExistanceDansTriPrelevement($aRow['Idbilan'])){
							$html .="<a href='javascript:modifierBilan(".$aRow[ 'Idfacturation' ].");'>";
							$html .="<img style='display: inline; margin-right: 15%;' src='".$tabURI[0]."public/images_icons/pencil_16.png' title='Modifier'></a>";
							

							$html .="<a id='suppBilan_".$aRow['Idbilan']."' href='javascript:supprimerBilan(".$aRow['Idbilan'].");'>";
							$html .="<img src='".$tabURI[0]."public/images_icons/symbol_supprimer.png' title='Supprimer'></a>";
						}
							
						//if(!$this->verifierExisteResultatFacturation($aRow[ 'Idfacturation' ])){
						//	$html .= "<infoBulleVue> <a id='".$aRow[ 'Idfacturation' ]."' href='javascript:supprimer(".$aRow[ 'Idfacturation' ].");'>";
						//	$html .="<img style='display: inline;' src='".$tabURI[0]."public/images_icons/symbol_supprimer.png' title='Supprimer'></a></infoBulleVue>";
						//}
							
							
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
	
	
	
	//********** RECUPERER LA LISTE DES BILANS DES PRELEVEMENTS POUR LESQUELS IL Y A DES ANALYSES NON CONFORMES *********
	//********** RECUPERER LA LISTE DES BILANS DES PRELEVEMENTS POUR LESQUELS IL Y A DES ANALYSES NON CONFORMES *********
	public function getListeBilansAnalysesNonConformePrelevement(){
	
		$db = $this->tableGateway->getAdapter();
			
		$aColumns = array('numero_dossier', 'Nom','Prenom','Datenaissance', 'Adresse', 'Date', 'id', 'Idfacturation', 'id2');
			
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
			
		/* LE NOUVEAU CODE AVEC LA PARTIE INFIRMERIE
		 * SQL queries
		* Liste des bilans des prélèvements repris
		*/
		$sql2 = new Sql ($db );
		$subselect = $sql2->select ();
		$subselect->from ( array ( 'bp' => 'bilan_prelevement_repris' ) );
		$subselect->columns (array ( 'idbilan' ) );
		
		/*
		 * SQL queries
		* Liste des prélèvements pour lesquels il y a des tris non conformes  
		*/
		$sql = new Sql($db);
		$sQuery = $sql->select()
		->from(array('pat' => 'patient'))->columns(array('*'))
		->join(array('pers' => 'personne'), 'pers.idpersonne = pat.idpersonne' , array('Nom'=>'nom','Prenom'=>'prenom','Datenaissance'=>'date_naissance','Sexe'=>'sexe','Adresse'=>'adresse','Nationalite'=>'nationalite_actuelle', 'id'=>'idpersonne', 'id2'=>'idpersonne') )
		->join(array('fact' => 'facturation'), 'fact.idpatient = pat.idpersonne', array('Idfacturation' => 'idfacturation', 'DateFact' => 'date', 'HeureFact' => 'heure') )
		->join(array('bp' => 'bilan_prelevement'), 'bp.idfacturation = fact.idfacturation', array('Idbilan' => 'idbilan', 'Date' => 'date_enregistrement') )
		->join(array('tp' => 'tri_prelevement'), 'tp.idbilan = bp.idbilan', array('IdbilanTri' => 'idbilan', 'Date_enregistrementTri' => 'date_enregistrement') )
		->where(array('tp.conformite' => 0, new NotIn('tp.idbilan', $subselect))) 
		->group('fact.idfacturation')
		->order('idtri DESC');
			
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
	
					else if ($aColumns[$i] == 'Adresse') {
						$row[] = "<div class='adresseText' >".$aRow[ $aColumns[$i] ]."</div>";
					}
	
					else if ($aColumns[$i] == 'Date') {
						$row[] = $Control->convertDateTimeHm($aRow[ 'Date_enregistrementTri' ]);
					}
	
					else if ($aColumns[$i] == 'id') {
						$html  ="<infoBulleVue> <a id='".$aRow[ $aColumns[$i] ]."' href='javascript:PrelevementTrieNonConforme(".$aRow[ 'id' ].",".$aRow[ 'Idfacturation' ].");'>";
						$html .="<img style='display: inline; margin-right: 10%;' src='".$tabURI[0]."public/images_icons/voir2.png' title='d&eacute;tails'></a></infoBulleVue>";
							
						if(!$this->verifierExistanceDansTriPrelevement($aRow['Idbilan'])){
							$html .= "<infoBulleVue> <a href='javascript:modifierBilan(".$aRow[ 'Idfacturation' ].");'>";
							$html .="<img style='display: inline; margin-right: 10%;' src='".$tabURI[0]."public/images_icons/pencil_16.png' title='Modifier'></a></infoBulleVue>";
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
	
	
	public function getExistanceListeNonConforme() {
		
		$db = $this->tableGateway->getAdapter();
		
		/* LE NOUVEAU CODE AVEC LA PARTIE INFIRMERIE
		 * SQL queries
		* Liste des bilans des prélèvements repris
		*/
		$sql2 = new Sql ($db );
		$subselect = $sql2->select ();
		$subselect->from ( array ( 'bp' => 'bilan_prelevement_repris' ) );
		$subselect->columns (array ( 'idbilan' ) );
		
		
		/*
		 * SQL queries
		* Liste des patients admis pour lesquels les demandes facturées ont au moin un bilan trié et non conforme
		*/
		$sql = new Sql($db);
		$sQuery = $sql->select()
		->from(array('pat' => 'patient'))->columns(array('*'))
		->join(array('pers' => 'personne'), 'pers.idpersonne = pat.idpersonne' , array('Nom'=>'nom','Prenom'=>'prenom','Datenaissance'=>'date_naissance','Sexe'=>'sexe','Adresse'=>'adresse','Nationalite'=>'nationalite_actuelle', 'id'=>'idpersonne', 'id2'=>'idpersonne') )
		->join(array('fact' => 'facturation'), 'fact.idpatient = pat.idpersonne', array('Idfacturation' => 'idfacturation', 'DateFact' => 'date', 'HeureFact' => 'heure') )
		->join(array('bp' => 'bilan_prelevement'), 'bp.idfacturation = fact.idfacturation', array('Idbilan' => 'idbilan', 'Date' => 'date_enregistrement') )
		->join(array('tp' => 'tri_prelevement'), 'tp.idbilan = bp.idbilan', array('IdbilanTri' => 'idbilan') )
		->where(array('tp.conformite' => 0, new NotIn('tp.idbilan', $subselect)))
		->group('fact.idfacturation')
		->order('Idbilan DESC');
			
		/* Data set length after filtering */
		return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	
	/*Liste des demandes d'analyses*/
	/*Liste des demandes d'analyses*/
	public function getDemandesAnalyses($id){
		$adapter = $this->tableGateway->getAdapter ();
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from(array('d'=>'demande_analyse'));
		$select->columns(array('*'));
		$select->where(array('idpatient' => $id));
		$select->group('date');
		$select->order('date DESC');
		return $sql->prepareStatementForSqlObject($select)->execute();
	}
	
	public function getInfosSecretaire($id){
		$adapter = $this->tableGateway->getAdapter ();
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from(array('p'=>'personne'));
		$select->columns(array('*'));
		$select->where(array('idpersonne' => $id));
		return $sql->prepareStatementForSqlObject($select)->execute()->current();
	}
	
	
	//Recuperer les analyses de la dernière demande du patient $id
	public function getAnalysesDemandees($id){
		$demande = $this->getDemandesAnalyses($id)->current();
	
		$adapter = $this->tableGateway->getAdapter ();
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from(array('d'=>'demande_analyse'));
		$select->columns(array('*'));
		$select->join(array('a'=>'analyse') ,'d.idanalyse = a.idanalyse', array('Designation'=>'designation', 'Tarif'=>'tarif'));
		$select->join(array('t'=>'type_analyse') ,'t.idtype = a.idtype_analyse', array('Libelle'=>'libelle'));
		$select->where(array('date' => $demande['date'], 'idpatient' => $id));
		$select->order('idanalyse ASC');
		return $sql->prepareStatementForSqlObject($select)->execute();
	}
	
	public function getListeDesAnalyses($id){
		$adapter = $this->tableGateway->getAdapter ();
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from(array('a'=>'analyse'));
		$select->columns(array('*'));
		$select->where(array('a.idtype_analyse' => $id));
		$select->order('idanalyse ASC');
		return $sql->prepareStatementForSqlObject($select)->execute();
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
		$dateDemande = $this->getDemandeAnalysesAvecIddemande($iddemande);
	
		$adapter = $this->tableGateway->getAdapter ();
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from(array('d'=>'demande_analyse'));
		$select->columns(array('*'));
		$select->join(array('a'=>'analyse') ,'d.idanalyse = a.idanalyse', array('Designation'=>'designation', 'Tarif'=>'tarif'));
		$select->join(array('t'=>'type_analyse') ,'t.idtype = a.idtype_analyse', array('Libelle'=>'libelle'));
		$select->where(array('date' => $dateDemande['date'], 'idpatient' => $dateDemande['idpatient']));
		$select->order(array('idanalyse' => 'ASC', 'idtype' =>'ASC'));
		return $sql->prepareStatementForSqlObject($select)->execute();
	}
	
	/**
	 * Recuperer le montant total des analyses de la demande
	 */
	public function getMontantTotalAnalyses($iddemande){
		$listeAnalyse = $this->getListeAnalysesDemandees($iddemande);
	
		$somme = 0;
		foreach ($listeAnalyse as $resultat){
			$somme += $resultat['Tarif'];
		}
	
		return $somme;
	}
	
	
	//********** RECUPERER LA LISTE DES CONSULTATIONS  *********
	//********** RECUPERER LA LISTE DES CONSULTATIONS  *********
	public function getListeConsultations(){
	
		$db = $this->tableGateway->getAdapter();
			
		$aColumns = array('numero_dossier', 'Nom','Prenom','Datenaissance','Sexe', 'Adresse', 'id');
			
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
			
		
		$dateDuJour = (new \DateTime ("now"))->format ( 'Y-m-d' );
		
		/*
		 * SQL queries
		* Liste des patients consultés aujourd'hui 
		*/
		$sql2 = new Sql ($db );
		$subselect = $sql2->select ();
		$subselect->from ( array ('cons' => 'consultation') );
		$subselect->columns (array ( 'idpatient' ) );
		$subselect->where(array('date' => $dateDuJour) );
		
		
		/*
		 * SQL queries
		* Liste des patients admis pour lesquels la consultation n'est pas encore faite
		*/
		$sql = new Sql($db);
		$sQuery = $sql->select()
		->from(array('pat' => 'patient'))->columns(array('*'))
		->join(array('pers' => 'personne'), 'pers.idpersonne = pat.idpersonne' , array('Nom'=>'nom','Prenom'=>'prenom','Datenaissance'=>'date_naissance','Sexe'=>'sexe','Adresse'=>'adresse','Nationalite'=>'nationalite_actuelle', 'id'=>'idpersonne', 'id2'=>'idpersonne') )
		->join(array('fact' => 'facturation_cons'), 'fact.idpatient = pat.idpersonne', array('Idfacturation' => 'idfacturation', 'Date' => 'date', 'Heure' => 'heure', 'Date_enregistrement'=> 'date_enregistrement') )
		->where(array('Date' => $dateDuJour, new NotIn ( 'fact.idpatient', $subselect )))
		->group('fact.idfacturation')
		->order('fact.idfacturation ASC');
		
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
		
					else if ($aColumns[$i] == 'Adresse') {
						$row[] = "<div class='adresseText' >".$aRow[ $aColumns[$i] ]."</div>";
					}
		
					else if ($aColumns[$i] == 'Date') {
						$row[] = $Control->convertDateTimeHm($aRow[ 'Date_enregistrement' ]);
					}
		
					else if ($aColumns[$i] == 'id') {
						$html  ="<infoBulleVue> <a href='".$tabURI[0]."public/infirmerie/consultation?idpatient=".$aRow[ 'id' ]."&idfacturation=".$aRow[ 'Idfacturation' ]."'>";
						$html .="<img style='display: inline; margin-right: 17%;' src='".$tabURI[0]."public/images_icons/surveillante.png' title='Consulter'></a></infoBulleVue>";
							
						$html .="<infoBulleVue>";
						$html .="<img style='opacity:0.2; margin-right: 17%;' src='".$tabURI[0]."public/images_icons/11modif.png'></a></infoBulleVue>";
						
						$row[] = $html;
					}
		
					else {
						$row[] = $aRow[ $aColumns[$i] ];
					}
		
				}
			}
			$output['aaData'][] = $row;
		}
		
		
		/*
		 * SQL queries
		* Liste des patients admis deja consultés aujourd'hui par l'infirmier
		*/
		$sql = new Sql($db);
		$sQuery = $sql->select()
		->from(array('pat' => 'patient'))->columns(array('*'))
		->join(array('pers' => 'personne'), 'pers.idpersonne = pat.idpersonne' , array('Nom'=>'nom','Prenom'=>'prenom','Datenaissance'=>'date_naissance','Sexe'=>'sexe','Adresse'=>'adresse','Nationalite'=>'nationalite_actuelle', 'id'=>'idpersonne', 'id2'=>'idpersonne') )
		->join(array('cons' => 'consultation'), 'cons.idpatient = pat.idpersonne', array('Idcons' => 'idcons', 'Date' => 'date') )
		->where(array('cons.date' => $dateDuJour) )
		->order('idcons ASC');
			
		/* Data set length after filtering */
		$stat = $sql->prepareStatementForSqlObject($sQuery);
		$rResultFt = $stat->execute();
		$iFilteredTotal = count($rResultFt);
			
		$rResult = $rResultFt;
			
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
	
					else if ($aColumns[$i] == 'Adresse') {
						$row[] = "<div class='adresseText' >".$aRow[ $aColumns[$i] ]."</div>";
					}
	
					else if ($aColumns[$i] == 'Date') {
						$row[] = $Control->convertDateTimeHm($aRow[ 'date_enregistrement' ]);
					}
	
					else if ($aColumns[$i] == 'id') {
						$html  ="<infoBulleVue>";
						$html .="<img style='opacity: 0.2; margin-right: 12%;' src='".$tabURI[0]."public/images_icons/surveillante.png' ></infoBulleVue>";
							
						$html .="<infoBulleVue> <a href='".$tabURI[0]."public/infirmerie/modifier-consultation?idpatient=".$aRow[ 'id' ]."&idcons=".$aRow[ 'Idcons' ]."'>";
						$html .="<img style='display: inline; margin-right: 17%;' src='".$tabURI[0]."public/images_icons/11modif.png' title='Modifier'></a></infoBulleVue>";
						
 						$html .="<infoBulleVue>";
 						$html .="<img style='display: inline;  margin-left: 5%; margin-right: 5%;' src='".$tabURI[0]."public/images_icons/tick_16.png' ></infoBulleVue>";
							
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
	
	
	//********** RECUPERER LA LISTE DES HISTORIQUES DES CONSULTATIONS  *********
	//********** RECUPERER LA LISTE DES HISTORIQUES DES CONSULTATIONS  *********
	public function getListeHistoriquesConsultations(){
	
		$db = $this->tableGateway->getAdapter();
			
		$aColumns = array('numero_dossier', 'Nom','Prenom','Datenaissance','Sexe', 'Adresse', 'id');
			
		$dateDuJour = (new \DateTime ("now"))->format ( 'Y-m-d' );
	
		/*
		 * SQL queries
		* Liste des patients admis deja consultés les jours précédents par l'infirmier
		*/
		$sql = new Sql($db);
		$sQuery = $sql->select()
		->from(array('pat' => 'patient'))->columns(array('*'))
		->join(array('pers' => 'personne'), 'pers.idpersonne = pat.idpersonne' , array('Nom'=>'nom','Prenom'=>'prenom','Datenaissance'=>'date_naissance','Sexe'=>'sexe','Adresse'=>'adresse','Nationalite'=>'nationalite_actuelle', 'id'=>'idpersonne', 'id2'=>'idpersonne') )
		->join(array('cons' => 'consultation'), 'cons.idpatient = pat.idpersonne', array('Idcons' => 'idcons', 'Date' => 'date') )
		->where(array('cons.date < ?' => $dateDuJour) )
		->order('idcons ASC');
			
		/* Data set length after filtering */
		$stat = $sql->prepareStatementForSqlObject($sQuery);
		$rResultFt = $stat->execute();
		$iFilteredTotal = count($rResultFt);
			
		$rResult = $rResultFt;

		$output = array(
				//"sEcho" => intval($_GET['sEcho']),
				//"iTotalRecords" => $iTotal,
				//"iTotalDisplayRecords" => $iFilteredTotal,
				
				"aaData" => array()
		);
		
		/*
		 * ADRESSE URL RELATIF
		*/
		$baseUrl = $_SERVER['REQUEST_URI'];
		$tabURI  = explode('public', $baseUrl);
			
		/*
		 * $Control pour convertir la date en franï¿½ais
		*/
		$Control = new DateHelper();
		
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
	
					else if ($aColumns[$i] == 'Adresse') {
						$row[] = "<div class='adresseText' >".$aRow[ $aColumns[$i] ]."</div>";
					}
	
					else if ($aColumns[$i] == 'Date') {
						$row[] = $Control->convertDateTimeHm($aRow[ 'date_enregistrement' ]);
					}
	
					else if ($aColumns[$i] == 'id') {
						$html  ="<infoBulleVue>";
						$html .="<img style='opacity: 0.2; margin-right: 12%;' src='".$tabURI[0]."public/images_icons/surveillante.png' ></infoBulleVue>";
							
						$html .="<infoBulleVue> <a href='".$tabURI[0]."public/infirmerie/visualiser-historique-consultation?idpatient=".$aRow[ 'id' ]."&idcons=".$aRow[ 'Idcons' ]."'>";
						$html .="<img style='display: inline; margin-right: 17%;' src='".$tabURI[0]."public/images_icons/11modif.png' title='Modifier'></a></infoBulleVue>";
	
						$html .="<infoBulleVue>";
						$html .="<img style='display: inline;  margin-left: 5%; margin-right: 5%;' src='".$tabURI[0]."public/images_icons/tick_16.png' ></infoBulleVue>";
							
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
	
	//FONCTION UTIOLISEE DANS LE MODULE DU BIOLOGISTE
	//FONCTION UTIOLISEE DANS LE MODULE DU BIOLOGISTE
	public function getTypageHemoglobineParType($idtype){
		$db = $this->tableGateway->getAdapter();
		$sql = new Sql($db);
		$sQuery = $sql->select()
		->from(array('th' => 'typage_hemoglobine'))->columns(array('*'))
		->where(array('type' => $idtype));
		$result = $sql->prepareStatementForSqlObject($sQuery)->execute();
	
		$donnee = array();
		foreach ($result as $res){
			$donnee[] = $res['idtypage'];
		}
	
		return $donnee;
	}
	
	public function getTypageHemoglobine($idTypage){
		$db = $this->tableGateway->getAdapter();
		$sql = new Sql($db);
		$sQuery = $sql->select()
		->from(array('th' => 'typage_hemoglobine'))->columns(array('*'))
		->where(array('idtypage' => $idTypage));
		$result = $sql->prepareStatementForSqlObject($sQuery)->execute();
	
		return $result->current();
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	//TABLE UTILISEE DANS LE MODULE 'SECRETARIAT'
	//TABLE UTILISEE DANS LE MODULE 'SECRETARIAT'
	//TABLE UTILISEE DANS LE MODULE 'SECRETARIAT'
	
	//Recupere la liste des actes
	//Recupere la liste des actes
	public function getListeDesTypesAnalyses(){
		$adapter = $this->tableGateway->getAdapter();
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->columns(array('*'));
		$select->from(array('t'=>'type_analyse'));
		return $sql->prepareStatementForSqlObject($select)->execute();
	}
	
	public function getTarifAnalyse($id){
		$adapter = $this->tableGateway->getAdapter ();
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from(array('a'=>'analyse'));
		$select->columns(array('*'));
		$select->where(array('idanalyse' => $id));
		return $sql->prepareStatementForSqlObject($select)->execute()->current()['tarif'];
	}
	
	
	
	//UTILISEE DANS LE MODULE 'CONSULTATION'
	//UTILISEE DANS LE MODULE 'CONSULTATION'
	//UTILISEE DANS LE MODULE 'CONSULTATION'
	public function getListeDesExamenImagerie(){
		$adapter = $this->tableGateway->getAdapter ();
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from(array('ei'=>'examen_imagerie'));
		$select->columns(array('*'));
		$select->order('idexamen ASC');
		return $sql->prepareStatementForSqlObject($select)->execute();
	}
}