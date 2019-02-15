<?php

namespace Secretariat\Model;

use Zend\Db\TableGateway\TableGateway;
use Secretariat\View\Helper\DateHelper;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Predicate\NotIn;
use Zend\Db\Sql\Predicate\In;

class PatientTable {
	protected $tableGateway;
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}
	
	public function fetchAll() {
		$resultSet = $this->tableGateway->select ();
		return $resultSet;
	}
	
	public function getPatient($idpersonne) {
		$idpersonne = ( int ) $idpersonne;
		$rowset = $this->tableGateway->select ( array (
				'idpersonne' => $idpersonne
		) );
		$row =  $rowset->current ();
		if (! $row) {
			$row = null;
		}
		return $row;
	}
	
	public function savePatient($idpersonne, $idemploye, $codepatient)
	{
		$data = array( 'idpersonne' => $idpersonne, 'codepatient' => $codepatient ,'date_enregistrement' => (new \DateTime() ) ->format('Y-m-d H:i:s'), 'idemploye' => $idemploye );
		$this->tableGateway->insert($data);
	}
	
	/*
	  NOUBEAU CODE POUR LA GESTION DU NUMERO_DOSSIER 
	  NOUBEAU CODE POUR LA GESTION DU NUMERO_DOSSIER 
	  NOUBEAU CODE POUR LA GESTION DU NUMERO_DOSSIER 
	 */
	public function getDernierPatient($annee){
	
		$db = $this->tableGateway->getAdapter();
		$sql = new Sql($db);
		$sQuery = $sql->select()
		->from(array('pat' => 'patient'))->columns( array( '*' ))
		->where(array('annee' => (int)$annee))
		->order('ordre DESC');
	
		return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	
	}
	//1 00129 2017 E
	public function numeroOrdreCinqChiffre($ordre) {
		$nbCharNum = 5 - strlen($ordre);
	
		$chaine ="";
		for ($i=1 ; $i <= $nbCharNum ; $i++){
			$chaine .= '0';
		}
		$chaine .= $ordre;
	
		return $chaine;
	}
	
	
	public function savePatientAvecNumeroDossier($idpersonne, $idemploye, $codepatient, $sexe){
		$date = new \DateTime();
		$annee = $date ->format('Y');
	
		$dernierPatient = $this->getDernierPatient($annee);
	
		if($dernierPatient){
			$suivant = $this->numeroOrdreCinqChiffre(( (int)$dernierPatient['ordre'] )+1);
			$numeroDossier = $sexe.' '.$suivant.' '.$annee.' E';
			$data = array( 'idpersonne' => $idpersonne, 'codepatient' => $codepatient, 'numero_dossier' => $numeroDossier, 'ordre' => $suivant, 'annee' => $annee ,'date_enregistrement' => (new \DateTime() ) ->format('Y-m-d H:i:s'), 'idemploye' => $idemploye );
			$this->tableGateway->insert ( $data );
		}else{
			$ordre = $this->numeroOrdreCinqChiffre(1);
			$numeroDossier = $sexe.' '.$ordre.' '.$annee.' E';
			$data = array( 'idpersonne' => $idpersonne, 'codepatient' => $codepatient, 'numero_dossier' => $numeroDossier, 'ordre' => $ordre, 'annee' => $annee ,'date_enregistrement' => (new \DateTime() ) ->format('Y-m-d H:i:s'), 'idemploye' => $idemploye );
			$this->tableGateway->insert ( $data );
		}
	}
	
	/*
	 =====================================================================
	 =====================================================================
	 =====================================================================
	 */
	
	
	
	public function updatePatient($idpersonne, $numero_dossier, $idemploye)
	{
	    $data = array('numero_dossier'  => $numero_dossier, 'idemploye' => $idemploye );
	    $this->tableGateway->update($data, array('idpersonne' => $idpersonne) );
	}

	public function saveDepistagePatient($idpatient, $ethnie, $idemploye)
	{
	    $data = array( 'idpatient' => $idpatient, 'ethnie' => $ethnie, 'date_enregistrement' => (new \DateTime() ) ->format('Y-m-d H:i:s'), 'idemploye' => $idemploye);
	    
	    //var_dump($data); exit();
	    
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->insert() ->into('depistage') ->values( $data );
	    $sql->prepareStatementForSqlObject($sQuery)->execute();
	}
	
	public function updateDepistagePatient($idpatient, $ethnie, $typepatient, $idemploye)
	{
	    $data = array( 'ethnie' => $ethnie, 'typepatient' => $typepatient, 'idemploye' => $idemploye);
	    	    
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->update() ->table('depistage') ->set( $data ) ->where( array('idpatient' => $idpatient ) );
	    
	    $sql->prepareStatementForSqlObject($sQuery) ->execute();
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
	
	
	//La liste des patients dépistés de type (SS ou SC ou SB) pour lesquels le typepatient n'est pas mis à 1
	public function getListeDepistagePatient()
	{
	    $typagesPathologiques = array('SS','SC','SB');
	    
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('dep' => 'depistage'))->columns(array('*'))
	    ->where(array('dep.typepatient' => 0, new In('typage', $typagesPathologiques) ));
	    $result = $sql->prepareStatementForSqlObject($sQuery)->execute();
	    
	    $resultat = array();
	    foreach ($result as $res){
	        $resultat[] = $res['idpatient'];
	    }
	    
	    return $resultat;
	}
	
	public function deletePatient($id){
		$db = $this->tableGateway->getAdapter();
		$sql = new Sql($db);
		$sQuery = $sql->delete()->from('personne')
		->where(array('idpersonne' => $id));
	
		$sql->prepareStatementForSqlObject($sQuery)->execute();
	}
	
    public function verifierExisteDemande($id_patient){
		$db = $this->tableGateway->getAdapter();
		$sql = new Sql ($db );
		$subselect = $sql->select ();
		$subselect->from ( array ( 'd' => 'demande_analyse' ) );
		$subselect->columns (array ( '*' ) );
		$subselect->where(array('idpatient' => $id_patient));
	
		return $sql->prepareStatementForSqlObject($subselect)->execute()->current();
	}
	
	public function listePatientsAjax(){
		$db = $this->tableGateway->getAdapter();
		
		$aColumns = array('numero_dossier', 'Nom','Prenom','Datenaissance', 'Sexe', 'Adresse', 'Nationalite', 'id', 'id2');
		
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
		*/
		$sql = new Sql($db);
		$sQuery = $sql->select()
		->from(array('pat' => 'patient'))->columns(array('*'))
		->join(array('pers' => 'personne'), 'pat.idpersonne = pers.idpersonne', array('Nom'=>'nom','Prenom'=>'prenom','Datenaissance'=>'date_naissance','Sexe'=>'sexe','Adresse'=>'adresse','Nationalite'=>'nationalite_actuelle','id'=>'idpersonne','id2'=>'idpersonne'))
		->order('pat.idpersonne DESC');
		
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
					    $nom = "";
					    if(in_array($aRow[ 'id' ], $this->getListeDepistagePatient())){
					        $nom  .= "<div id='alert_depistage_positif_".$aRow[ 'id' ]."' style='width: 10px; height: 22px; margin-left: -7px; margin-right: 0.5px; float:left; display:non' >
				                         <div style='background: red; height: 6px; width: 6px; margin-top: 6px; border-radius: 50%;'></div>
				                      </div>";
					    }
					    $nom .= "<div id='nomMaj' style='max-width: 130px; overflow: hidden;' >".$aRow[ $aColumns[$i]]."</div>";
						$row[] = $nom;
					}
					
					else if ($aColumns[$i] == 'Prenom'){
						$row[] = "<div style='max-width: 160px; overflow: hidden;' >".$aRow[ $aColumns[$i]]."</div>";
					}
		
					else if ($aColumns[$i] == 'Datenaissance') {
						$date_naissance = $aRow[ $aColumns[$i] ];
						if($date_naissance){ $row[] = $Control->convertDate($aRow[ $aColumns[$i] ]); }else{ $row[] = null;}
					}
		
					else if ($aColumns[$i] == 'Adresse') {
						$row[] = "<div class='adresseText' style='max-width: 230px; overflow: hidden;' >".$aRow[ $aColumns[$i] ]."</div>";
					}
		
					else if ($aColumns[$i] == 'id') {
						$html ="<infoBulleVue> <a href='javascript:visualiser(".$aRow[ $aColumns[$i] ].")' >";
						$html .="<img style='margin-right: 10%;' src='".$tabURI[0]."public/images_icons/voir2.png' title='d&eacute;tails'></a> </infoBulleVue>";
		
						$html .= "<infoBulleVue> <a href='javascript:modifierPatient(".$aRow[ $aColumns[$i] ].")' >";
						$html .="<img style='display: inline; margin-right: 15%;' src='".$tabURI[0]."public/images_icons/pencil_16.png' title='Modifier'></a> </infoBulleVue>";
		
						if(!$this->verifierExisteDemande($aRow[ $aColumns[$i] ])){
							$html .= "<a id='".$aRow[ $aColumns[$i] ]."' href='javascript:envoyer(".$aRow[ $aColumns[$i] ].")'>";
							$html .="<img style='display: inline;' src='".$tabURI[0]."public/images_icons/symbol_supprimer.png' title='Supprimer'></a>";
						}
						
						$depistage = $this->getDepistagePatient( $aRow[ $aColumns[$i] ] );
						
						$depister = 0;
						$typepatient = 0;
						if( $depistage->current() ){
						    $depister = 1;
						    $typepatient = $depistage->current()['typepatient'];
						} 
						
						if($depister == 1){
						    $html .="<span style='display: none;'> patient_depister </span>";
						}
						if($typepatient == 0){
						    $html .="<span style='display: none;'> patient_externe </span>";
						}else if($typepatient == 1){
						    $html .="<span style='display: none;'> patient_interne </span>";
						}
						
						
						
						$row[] = $html;
					}
					
					else if ($aColumns[$i] == 'Nationalite'){
						$row[] = "<div>".$aRow[ $aColumns[$i]]."</div>";
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
	
	
	public function listeRecherchePatientAjax()
	{
		$db = $this->tableGateway->getAdapter();
		
		$aColumns = array('numero_dossier', 'Nom', 'Prenom', 'Datenaissance', 'Adresse', 'id', 'Idpatient');
		
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
		
		$sql = new Sql($db);
		$sQuery = $sql->select()
		->from(array('pat' => 'patient'))->columns(array('*'))
		->join(array('pers' => 'personne'), 'pat.idpersonne = pers.idpersonne', array('Nom'=>'nom','Prenom'=>'prenom','Datenaissance'=>'date_naissance','Sexe'=>'sexe','Adresse'=>'adresse','id'=>'idpersonne','Idpatient'=>'idpersonne'))
		->order('pat.idpersonne DESC');
		
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
					if ($aColumns[$i] == 'numero_dossier'){
						$row[] = "<span style='font-size: 19px;'>".$aRow[ $aColumns[$i]]."<span style='display: none;'> ".str_replace(' ', '' ,$aRow[ $aColumns[$i]])."</span></span>";
					}
					
					else if ($aColumns[$i] == 'Nom'){
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
						$html ="<a href='javascript:visualiser(".$aRow[ $aColumns[$i] ].")' >";
						$html .="<img style='margin-left: 5%; margin-right: 15%;' src='".$tabURI[0]."public/images_icons/voir2.png' title='Visualiser'></a>";
		
						$html .= "<a href='javascript:demandesAnalyses(".$aRow[ $aColumns[$i] ].")' >";
						$html .="<img style='display: inline; margin-right: 5%;' src='".$tabURI[0]."public/images_icons/transfert_droite.png' title='Suivant'></a>";
		
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
	//&#9654;
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
	
	public function getListeDesAnalyses($id){
		$adapter = $this->tableGateway->getAdapter ();
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from(array('a'=>'analyse'));
		$select->columns(array('*'));
		$select->where(array('a.idtype_analyse' => $id, 'idanalyse != ?' => 9)); //Sauf Taux de Reticulosyte (TR)
		$select->order('designation ASC');
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
	
	/**
	 * Recuperer la liste des analyses demandees qui ne sont 
	 * pas encore facturées pour le patient en paramètre
	 */
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
	
	//<!-- LISTE DES PATIENTS POUR LES DEMANDES D'AUJOURD'HUI -->
	//<!-- LISTE DES PATIENTS POUR LES DEMANDES D'AUJOURD'HUI -->
	public function listeDemandesAujourdhuiAjax()
	{
		$db = $this->tableGateway->getAdapter();
	
		$aColumns = array('numero_dossier', 'Nom','Prenom','Datenaissance', 'Adresse', 'Date', 'id', 'id2');
	
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
	
		$aujourdhui = (new \DateTime() ) ->format('Y-m-d');
		
		/*
		 * SQL queries
		*/
		$sql = new Sql($db);
		$sQuery = $sql->select()
		->from(array('pat' => 'patient'))->columns(array('*'))
		->join(array('pers' => 'personne'), 'pat.idpersonne = pers.idpersonne', array('Nom'=>'nom','Prenom'=>'prenom','Datenaissance'=>'date_naissance','Sexe'=>'sexe','Adresse'=>'adresse','Nationalite'=>'nationalite_actuelle','id'=>'idpersonne','id2'=>'idpersonne'))
		->join(array('dem_an' => 'demande_analyse'), 'pat.idpersonne = dem_an.idpatient', array('Date'=>'date', 'Time'=>'time'))
		->order(array('dem_an.date' => 'DESC', 'dem_an.time' => 'DESC'))
		->where(array('dem_an.date' => $aujourdhui))
		->group('idpatient');
	
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
					if ($aColumns[$i] == 'numero_dossier'){
						$row[] = "<span style='font-size: 19px;'>".$aRow[ $aColumns[$i]]."<span style='display: none;'> ".str_replace(' ', '' ,$aRow[ $aColumns[$i]])."</span></span>";
					}
					
					else if ($aColumns[$i] == 'Nom'){
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
						$html ="<infoBulleVue> <a href='javascript:visualiser(".$aRow[ $aColumns[$i] ].")' >";
						$html .="<img style='margin-left: 5%; margin-right: 15%;' src='".$tabURI[0]."public/images_icons/voir2.png' title='d&eacute;tails'></a> </infoBulleVue>";
	
						$html .= "<infoBulleVue> <a href='javascript:listeDemandes(".$aRow[ $aColumns[$i] ].")' >";
						$html .="<img style='display: inline; margin-right: 5%;' src='".$tabURI[0]."public/images_icons/details.png' title='liste des demandes'></a> </infoBulleVue>";
	
						$demandesNonFacturees = $this->getListeAnalysesDemandeesNonFacturees($aRow[ $aColumns[$i] ]);
						if($demandesNonFacturees){
							$html .='<img style="margin-left: 10%; width: 7px; height: 10px;" src="'.$tabURI[0].'public/images_icons/desactiver.png" title="Existance d\'analyses demand&eacute;es non encore factur&eacute;es">';
						}
						
						$row[] = $html;
					}
	
					else if ($aColumns[$i] == 'Date'){
						$row[] = "<div>".$Control->convertDate($aRow[ $aColumns[$i] ]).' - '.$Control->getTimeHm($aRow['Time'])."</div>";
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
	
	public function listeDemandesTousAjax()
	{
		$db = $this->tableGateway->getAdapter();
	
		$aColumns = array('numero_dossier', 'Nom','Prenom','Datenaissance', 'Adresse', 'Date', 'id', 'id2');
	
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
		*/
		$sql = new Sql($db);
		$sQuery = $sql->select()
		->from(array('pat' => 'patient'))->columns(array('*'))
		->join(array('pers' => 'personne'), 'pat.idpersonne = pers.idpersonne', array('Nom'=>'nom','Prenom'=>'prenom','Datenaissance'=>'date_naissance','Sexe'=>'sexe','Adresse'=>'adresse','Nationalite'=>'nationalite_actuelle','id'=>'idpersonne','id2'=>'idpersonne'))
		->join(array('dem_an' => 'demande_analyse'), 'pat.idpersonne = dem_an.idpatient', array('Date'=>'date', 'Time'=>'time')) 
		->order(array('dem_an.date' => 'DESC', 'dem_an.time' => 'DESC'))
		->group('idpatient');
	
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
					if ($aColumns[$i] == 'numero_dossier'){
						$row[] = "<span style='font-size: 19px;'>".$aRow[ $aColumns[$i]]."<span style='display: none;'> ".str_replace(' ', '' ,$aRow[ $aColumns[$i]])."</span></span>";
					}
					
					else if ($aColumns[$i] == 'Nom'){
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
						$html ="<infoBulleVue> <a href='javascript:visualiser(".$aRow[ $aColumns[$i] ].")' >";
						$html .="<img style='margin-left: 5%; margin-right: 15%;' src='".$tabURI[0]."public/images_icons/voir2.png' title='d&eacute;tails'></a> </infoBulleVue>";
	
						$html .= "<infoBulleVue> <a href='javascript:listeDemandes(".$aRow[ $aColumns[$i] ].")' >";
						$html .="<img style='display: inline; margin-right: 5%;' src='".$tabURI[0]."public/images_icons/details.png' title='liste des demandes'></a> </infoBulleVue>";
	
						$demandesNonFacturees = $this->getListeAnalysesDemandeesNonFacturees($aRow[ $aColumns[$i] ]);
						if($demandesNonFacturees){
							$html .='<img style="margin-left: 10%; width: 7px; height: 10px;" src="'.$tabURI[0].'public/images_icons/desactiver.png" title="Existance d\'analyses demand&eacute;es non encore factur&eacute;es">';
						}
						
						$row[] = $html;
					}
						
					else if ($aColumns[$i] == 'Date'){
						$row[] = "<div>".$Control->convertDate($aRow[ $aColumns[$i] ]).' - '.$Control->getTimeHm($aRow['Time'])."</div>";
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
	
	
	public function listeDemandesFiltreAjax()
	{
		$db = $this->tableGateway->getAdapter();
	
		$aColumns = array('Iddemande', 'Date', 'Id');
	
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
		*/
		$sql = new Sql($db);
		$sQuery = $sql->select()
		->from(array('dem' => 'demande_analyse'))->columns(array('Date'=>'date', 'Time'=>'time', 'Iddemande'=>'iddemande', 'Id'=>'iddemande'))
		->order('dem.date DESC') ->group('date');
	
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
	
					$html = "";
					if ($aColumns[$i] == 'Id') {
	
						$html .= "<infoBulleVue> <a href='javascript:listeDemandes(".$aRow[ $aColumns[$i] ].")' >";
						$html .= "<img style='display: inline; margin-right: 5%;' src='".$tabURI[0]."public/images_icons/details.png' title='liste des demandes'></a> </infoBulleVue>";
						$row[] = $html;
					}
	
					else if ($aColumns[$i] == 'Date'){
						$row[] = "<div>".$Control->convertDate($aRow[ $aColumns[$i] ]).' '.$aRow['Time']."</div>";
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
	

	// C'est l'ensemble des demandes ayant des résultats pour lesquelles le biologiste n'a pas encore validé
	public function getResultatsDemandesAnalyses($id){
	
		$adapter = $this->tableGateway->getAdapter ();
	
		$sql2 = new Sql ($adapter);
		$subselect = $sql2->select ();
		$subselect->from ( array ( 'rda' => 'resultat_demande_analyse' ) );
		$subselect->where( array ( 'rda.valide' => 0 ) );
		$subselect->columns (array ( 'iddemande_analyse' ) );
	
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from(array('d'=>'demande_analyse'))->columns(array('*'));
		$select->join(array('tp' => 'tri_prelevement'), 'tp.iddemande = d.iddemande', array('*') );
		$select->where( array ('d.idpatient' => $id, new In ( 'd.iddemande', $subselect ) ) );
		$select->group('date');
		$select->order('date DESC');
		return $sql->prepareStatementForSqlObject($select)->execute();
	
	}
	
	// C'est l'ensemble des demandes ayant des résultats pour lesquelles le biologiste a déjà validé
	public function getResultatsDemandesAnalysesValidees($id){
	
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
		$select->where( array ('d.idpatient' => $id, new In ( 'd.iddemande', $subselect ) ) );
		$select->group('date');
		$select->order('date DESC');
		return $sql->prepareStatementForSqlObject($select)->execute();
	
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
	
	
	//Recuperer les résultats des analyses de la dernière demande du patient $id
	//Recuperer les résultats des analyses de la dernière demande du patient $id
	public function getResultatsAnalysesDemandees($id){
		$demande = $this->getResultatsDemandesAnalyses($id)->current();
	
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
		$select->join(array('a'=>'analyse') ,'d.idanalyse = a.idanalyse', array('Designation'=>'designation', 'Tarif'=>'tarif'));
		$select->join(array('t'=>'type_analyse') ,'t.idtype = a.idtype_analyse', array('Libelle'=>'libelle'));
		$select->where(array('date' => $demande['date'], 'idpatient' => $id, new In ( 'd.iddemande', $subselect ) ));
		$select->order('idanalyse ASC');
		return $sql->prepareStatementForSqlObject($select)->execute();
	}
	
	//Recuperer les résultats des analyses de la dernière demande validee du patient $id
	//Recuperer les résultats des analyses de la dernière demande validee du patient $id
	public function getResultatsAnalysesDemandeesValidees($id){
		$demande = $this->getResultatsDemandesAnalysesValidees($id)->current();
	
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
		$select->where(array('date' => $demande['date'], 'idpatient' => $id, new In ( 'd.iddemande', $subselect ) ));
		$select->order('idanalyse ASC');
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
	
	
	public function getDemandeAnalysesAvecIddemande($iddemande){
		$adapter = $this->tableGateway->getAdapter ();
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from(array('d'=>'demande_analyse'));
		$select->columns(array('*'));
		$select->where(array('iddemande' => $iddemande));
		return $sql->prepareStatementForSqlObject($select)->execute()->current();
	}
	
	
	//Recuperer la liste des analyses demandees ayant des resultats non valides pour la demande $iddemande
	public function getListeAnalysesDemandeesResultats($iddemande){
		$dateDemande = $this->getDemandeAnalysesAvecIddemande($iddemande);
	
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
		$select->join(array('a'=>'analyse') ,'d.idanalyse = a.idanalyse', array('Designation'=>'designation', 'Tarif'=>'tarif'));
		$select->join(array('t'=>'type_analyse') ,'t.idtype = a.idtype_analyse', array('Libelle'=>'libelle'));
		$select->where(array('date' => $dateDemande['date'], 'idpatient' => $dateDemande['idpatient'], new In ( 'd.iddemande', $subselect ) ));
		$select->order(array('idanalyse' => 'ASC', 'idtype' =>'ASC'));
		return $sql->prepareStatementForSqlObject($select)->execute();
	}
	
	//Recuperer la liste des analyses demandees ayant des résultats et déjà validés pour la demande $iddemande
	public function getListeResultatsAnalysesDemandeesValidees($iddemande){
		$dateDemande = $this->getDemandeAnalysesAvecIddemande($iddemande);
	
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
		$select->where(array('date' => $dateDemande['date'], 'idpatient' => $dateDemande['idpatient'], new In ( 'd.iddemande', $subselect ) ));
		$select->order(array('idanalyse' => 'ASC', 'idtype' =>'ASC'));
		return $sql->prepareStatementForSqlObject($select)->execute();
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
	
	
	/*La liste des demandes pour lesquelles il existe au moins une analyse n'ayant pas encore un résultat */
	/*La liste des demandes pour lesquelles il existe au moins une analyse n'ayant pas encore un résultat */
	/*La liste des demandes pour lesquelles il existe au moins une analyse n'ayant pas encore un résultat */
	public function listeDemandesAnalyses(){

		$db = $this->tableGateway->getAdapter();
		
		$aColumns = array('Nom','Prenom','Age', 'Sexe', 'Adresse', 'Date', 'id', 'id2');
		
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
		 * SQL querie 1
		*/
		$sql1 = new Sql($db);
		$sQuery1 = $sql1->select()
		->from(array('res' => 'resultat_demande_analyse'))->columns(array('iddemande_analyse'));
		 
		
		/*
		 * SQL queries
		*/
		$sql = new Sql($db);
		$sQuery = $sql->select()
		->from(array('pat' => 'patient'))->columns(array('*'))
		->join(array('pers' => 'personne'), 'pat.idpersonne = pers.idpersonne', array('Nom'=>'nom','Prenom'=>'prenom','Datenaissance'=>'date_naissance', 'Age'=>'age', 'Sexe'=>'sexe','Adresse'=>'adresse','Nationalite'=>'nationalite_actuelle','id'=>'idpersonne','id2'=>'idpersonne'))
		->join(array('dem_an' => 'demande_analyse'), 'pat.idpersonne = dem_an.idpatient', array('Date'=>'date', 'Time'=>'time'))
		->where(array(new NotIn('dem_an.iddemande',$sQuery1)))
		->order(array('dem_an.date' => 'ASC', 'dem_an.time' => 'ASC'))
		->group('idpatient');
		
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
					
					else if ($aColumns[$i] == 'Age') {
						$row[] = "<div style='text-align: center;'>".$aRow[ $aColumns[$i] ]."</div>";
					}
		
					else if ($aColumns[$i] == 'id') {
						$html ="<infoBulleVue> <a href='javascript:visualiser(".$aRow[ $aColumns[$i] ].")' >";
						$html .="<img style='margin-left: 5%; margin-right: 15%;' src='".$tabURI[0]."public/images_icons/voir2.png' title='Visualiser'></a> </infoBulleVue>";
		
						$html .= "<infoBulleVue> <a href='javascript:listeDemandes(".$aRow[ $aColumns[$i] ].")' >";
						$html .="<img style='display: inline; margin-right: 5%;' src='".$tabURI[0]."public/images_icons/details.png' title='Liste demandes'></a> </infoBulleVue>";
		
						$row[] = $html;
					}
		
					else if ($aColumns[$i] == 'Date'){
						$row[] = "<div>".$Control->convertDate($aRow[ $aColumns[$i] ]).' - '.$aRow['Time']."</div>";
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
	
	/*Liste des demandes d'analyses sans résultat*/
	/*Liste des demandes d'analyses sans résultat*/
	public function getDemandesAnalysesSansResultat($id){
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
	
	/** La liste des demandes pour lesquelles toutes les analyses ont des résultats **/
	/** La liste des demandes pour lesquelles toutes les analyses ont des résultats **/
	/** La liste des demandes pour lesquelles toutes les analyses ont des résultats **/
	public function listeResultatsAnalyses(){
	
	    $db = $this->tableGateway->getAdapter();
	
	    $aColumns = array('Nom','Prenom','Age', 'Sexe', 'Adresse', 'Date', 'id', 'id2');
	
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
	     * SQL querie 1
	     */
	    $sql1 = new Sql($db);
	    $sQuery1 = $sql1->select()
	    ->from(array('res' => 'resultat_demande_analyse'))->columns(array('iddemande_analyse'));
	    
	    /*
	     * SQL querie 2
	    */
	    $sql2 = new Sql($db);
	    $sQuery2 = $sql2->select()
	    ->from(array('pat' => 'patient'))->columns(array('idpersonne'))
	    ->join(array('dem_an' => 'demande_analyse'), 'pat.idpersonne = dem_an.idpatient', array())
	    ->where(array(new NotIn('dem_an.iddemande',$sQuery1)))
	    ->group('idpatient');
	    
	    /*
	     * SQL querie 3
	    */
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('pat' => 'patient'))->columns(array('*'))
	    ->join(array('pers' => 'personne'), 'pat.idpersonne = pers.idpersonne', array('Nom'=>'nom','Prenom'=>'prenom','Datenaissance'=>'date_naissance', 'Age'=>'age', 'Sexe'=>'sexe','Adresse'=>'adresse','Nationalite'=>'nationalite_actuelle','id'=>'idpersonne','id2'=>'idpersonne'))
	    ->join(array('dem_an' => 'demande_analyse'), 'pat.idpersonne = dem_an.idpatient', array('Date'=>'date', 'Time'=>'time'))
	    ->where(array(new NotIn('pat.idpersonne',$sQuery2)))
	    ->order(array('dem_an.date' => 'ASC', 'dem_an.time' => 'ASC'))
	    ->group('idpatient');
	
	    /* Data set length after filtering */
	    $stat = $sql->prepareStatementForSqlObject($sQuery);
	    $rResultFt = $stat->execute();
	    $iFilteredTotal = count($rResultFt);
	
// 	    $tab = array();
// 	    foreach ($rResultFt as $res){
// 	        $tab [] = $res;
// 	    }
	    
// 	    var_dump($tab); exit();
	    
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
	                	
	                else if ($aColumns[$i] == 'Age') {
	                    $row[] = "<div style='text-align: center;'>".$aRow[ $aColumns[$i] ]."</div>";
	                }
	
	                else if ($aColumns[$i] == 'id') {
	                    $html ="<infoBulleVue> <a href='javascript:visualiser(".$aRow[ $aColumns[$i] ].")' >";
	                    $html .="<img style='margin-left: 5%; margin-right: 15%;' src='".$tabURI[0]."public/images_icons/voir2.png' title='Visualiser'></a> </infoBulleVue>";
	
	                    $html .= "<infoBulleVue> <a href='javascript:listeDemandes(".$aRow[ $aColumns[$i] ].")' >";
	                    $html .="<img style='display: inline; margin-right: 5%;' src='".$tabURI[0]."public/images_icons/details.png' title='Liste demandes'></a> </infoBulleVue>";
	
	                    $row[] = $html;
	                }
	
	                else if ($aColumns[$i] == 'Date'){
	                    $row[] = "<div>".$Control->convertDate($aRow[ $aColumns[$i] ]).' - '.$aRow['Time']."</div>";
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
	
	
	//FONCTION UTILISER DANS LE MODULE DU BIOLOGISTE
	//FONCTION UTILISER DANS LE MODULE DU BIOLOGISTE
	protected function nbJours($debut, $fin) {
		//60 secondes X 60 minutes X 24 heures dans une journee
		$nbSecondes = 60*60*24;
	
		$debut_ts = strtotime($debut);
		$fin_ts = strtotime($fin);
		$diff = $fin_ts - $debut_ts;
		return ($diff / $nbSecondes);
	}
	
	public function gestionAges($age, $date_naissance) {
		//Gestion des AGE
		if($age && !$date_naissance){
			return $age." ans";
		}else{
			$aujourdhui = (new \DateTime() ) ->format('Y-m-d');
			$age_jours = $this->nbJours($date_naissance, $aujourdhui);
		
			$age_annees = (int)($age_jours/365);
		
			if($age_annees == 0){
		
				if($age_jours < 31){
					return $age_jours." jours";
				}else if($age_jours >= 31) {
		
					$nb_mois = (int)($age_jours/31);
					$nb_jours = $age_jours - ($nb_mois*31);
					if($nb_jours == 0){
						return $nb_mois."m";
					}else{
						return $nb_mois."m ".$nb_jours."j";
					}
		
				}
		
			}else{
				$age_jours = $age_jours - ($age_annees*365);
		
				if($age_jours < 31){
		
					if($age_annees == 1){
						if($age_jours == 0){
							return $age_annees."an";
						}else{
							return $age_annees."an ".$age_jours."j";
						}
					}else{
						if($age_jours == 0){
							return $age_annees."ans";
						}else{
							return $age_annees."ans ".$age_jours."j";
						}
					}
		
				}else if($age_jours >= 31) {
		
					$nb_mois = (int)($age_jours/31);
					$nb_jours = $age_jours - ($nb_mois*31);
		
					if($age_annees == 1){
						if($nb_jours == 0){
							return $age_annees."an ".$nb_mois."m";
						}else{
							return $age_annees."an ".$nb_mois."m ";
						}
		
					}else{
						if($nb_jours == 0){
							return $age_annees."ans ".$nb_mois."m";
						}else{
							return $age_annees."ans ".$nb_mois."m";
						}
					}
		
				}
		
			}
		
		}
		
	}
	//********** RECUPERER LA LISTE DES PATIENTS POUR LESQUELS LEURS ANALYSES ONT DEJA DES RESULTATS   *********
	//********** RECUPERER LA LISTE DES PATIENTS POUR LESQUELS LEURS ANALYSES ONT DEJA DES RESULTATS   *********
	
	public function getListeResultatsAnalysesPourValidation() {
	
		$db = $this->tableGateway->getAdapter();
	
		$aColumns = array('numero_dossier', 'Nom' ,'Prenom' ,'Age' ,'Adresse' ,'DateEnregistrementRda', 'id', 'Idfacturation', 'id2');
	
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
 		* Liste des patients pour lesquels le tri des analyses est déjà fait
 		*/
 		$sql = new Sql($db);
 		$sQuery = $sql->select()
 		->from(array('pat' => 'patient'))->columns(array('*'))
 		->join(array('pers' => 'personne'), 'pers.idpersonne = pat.idpersonne' , array('Nom'=>'nom','Prenom'=>'prenom','Datenaissance'=>'date_naissance','Age'=>'age','Adresse'=>'adresse','Nationalite'=>'nationalite_actuelle', 'id'=>'idpersonne', 'id2'=>'idpersonne', 'id3'=>'idpersonne'))
 		->join(array('fact' => 'facturation'), 'fact.idpatient = pat.idpersonne', array('Idfacturation' => 'idfacturation', 'Date' => 'date', 'Heure' => 'heure') )
 		->join(array('bp' => 'bilan_prelevement'), 'bp.idfacturation = fact.idfacturation', array('Idbilan' => 'idbilan', 'DateEnregistrementBp' => 'date_enregistrement') )
 		->join(array( 'tp' => 'tri_prelevement' ), 'tp.idbilan = bp.idbilan', array('IdbilanTri' => 'idbilan', 'DateEnregistrementTri' => 'date_enregistrement') )
 		->join(array( 'rda' => 'resultat_demande_analyse' ), 'rda.iddemande_analyse = tp.iddemande', array('DateEnregistrementRda' => 'Date') )
 		->where( array ( 'rda.valide' => 0 ) )
 		->group('pat.idpersonne')
 		->order('DateEnregistrementRda ASC');
	
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
	
					else if ($aColumns[$i] == 'Age') {
						$age = $this->gestionAges($aRow[ 'Age' ], $aRow[ 'Datenaissance' ]);
						$row[] = "<div>".$age."</div>";
					}
	
					else if ($aColumns[$i] == 'DateEnregistrementRda') {
						$row[] = $Control->convertDateTimeHm($aRow[ 'DateEnregistrementRda' ]);
					}
	
					else if ($aColumns[$i] == 'Adresse'){
						$row[] = "<div>".$aRow[ $aColumns[$i]]."</div>";
					}
					
					else if ($aColumns[$i] == 'id') {
						$html  ="<infoBulleVue> <a id='".$aRow[ $aColumns[$i] ]."' href='javascript:visualiser(".$aRow[ 'id' ].");'>";
						$html .="<img style='display: inline; margin-right: 10%;' src='".$tabURI[0]."public/images_icons/voir2.png' title='d&eacute;tails'></a></infoBulleVue>";
	
						$html .= "<infoBulleVue> <a href='javascript:listeAnalysesDemandees(".$aRow[ 'id' ].");'>";
						$html .="<img style='display: inline; margin-right: 10%;' src='".$tabURI[0]."public/images_icons/details.png' title='Analyses demand&eacute;es'></a></infoBulleVue>";
	
	
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
	
	
	
	
	//FONCTION UTILISER DANS LE MODULE DU BIOLOGISTE
	//FONCTION UTILISER DANS LE MODULE DU BIOLOGISTE
	
	//********** RECUPERER LA LISTE DES PATIENTS POUR LESQUELS LEURS ANALYSES ONT DEJA DES RESULTATS ET SONT VALIDEES   *********
	//********** RECUPERER LA LISTE DES PATIENTS POUR LESQUELS LEURS ANALYSES ONT DEJA DES RESULTATS ET SONT VALIDEES   *********
	
	public function getListeResultatsAnalysesValidees() {
	
		$db = $this->tableGateway->getAdapter();
	
		$aColumns = array('numero_dossier', 'Nom' ,'Prenom' ,'Age' ,'Adresse' ,'DateValidationRda', 'id', 'Idfacturation', 'id2');
	
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
		* Liste des patients pour lesquels le tri des analyses est déjà fait
		*/
		$sql = new Sql($db);
		$sQuery = $sql->select()
		->from(array('pat' => 'patient'))->columns(array('*'))
		->join(array('pers' => 'personne'), 'pers.idpersonne = pat.idpersonne' , array('Nom'=>'nom','Prenom'=>'prenom','Datenaissance'=>'date_naissance','Age'=>'age','Adresse'=>'adresse','Nationalite'=>'nationalite_actuelle', 'id'=>'idpersonne', 'id2'=>'idpersonne', 'id3'=>'idpersonne'))
		->join(array('fact' => 'facturation'), 'fact.idpatient = pat.idpersonne', array('Idfacturation' => 'idfacturation', 'Date' => 'date', 'Heure' => 'heure') )
		->join(array('bp' => 'bilan_prelevement'), 'bp.idfacturation = fact.idfacturation', array('Idbilan' => 'idbilan', 'DateEnregistrementBp' => 'date_enregistrement') )
		->join(array( 'tp' => 'tri_prelevement' ), 'tp.idbilan = bp.idbilan', array('IdbilanTri' => 'idbilan', 'DateEnregistrementTri' => 'date_enregistrement') )
		->join(array( 'rda' => 'resultat_demande_analyse' ), 'rda.iddemande_analyse = tp.iddemande', array('DateValidationRda' => 'date_validation') )
		->where( array ( 'rda.valide' => 1 ) )
		->group('pat.idpersonne')
		->order('DateValidationRda ASC');
	
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
	
					else if ($aColumns[$i] == 'Age') {
						$age = $this->gestionAges($aRow[ 'Age' ], $aRow[ 'Datenaissance' ]);
						$row[] = "<div>".$age."</div>";
					}
	
					else if ($aColumns[$i] == 'DateValidationRda') {
						$row[] = $Control->convertDateTimeHm($aRow[ 'DateValidationRda' ]);
					}
	
					else if ($aColumns[$i] == 'Adresse'){
						$row[] = "<div>".$aRow[ $aColumns[$i]]."</div>";
					}
					
					else if ($aColumns[$i] == 'id') {
						$html  ="<infoBulleVue> <a id='".$aRow[ $aColumns[$i] ]."' href='javascript:visualiser(".$aRow[ 'id' ].");'>";
						$html .="<img style='display: inline; margin-right: 10%;' src='".$tabURI[0]."public/images_icons/voir2.png' title='d&eacute;tails'></a></infoBulleVue>";
	
						$html .= "<infoBulleVue> <a href='javascript:listeAnalysesValidees(".$aRow[ 'id' ].");'>";
						$html .="<img style='display: inline; margin-right: 10%;' src='".$tabURI[0]."public/images_icons/details.png' title='Analyses demand&eacute;es'></a></infoBulleVue>";
	
	
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
	
	
	
	//FONCTION UTILISER DANS LE MODULE DU SERCETAIRE
	//FONCTION UTILISER DANS LE MODULE DU SECRETAIRE
	
	//********** RECUPERER LA LISTE DES PATIENTS POUR LESQUELS LEURS ANALYSES ONT DEJA DES RESULTATS ET SONT VALIDEES   *********
	//********** RECUPERER LA LISTE DES PATIENTS POUR LESQUELS LEURS ANALYSES ONT DEJA DES RESULTATS ET SONT VALIDEES   *********
	
	public function getListeResultatsAnalyses() {
	
		$db = $this->tableGateway->getAdapter();
	
		$aColumns = array('numero_dossier', 'Nom' ,'Prenom' ,'Age' ,'Adresse' ,'DateValidationRda', 'id', 'Idfacturation', 'id2');
	
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
		* Liste des patients pour lesquels le tri des analyses est déjà fait
		*/
		$sql = new Sql($db);
		$sQuery = $sql->select()
		->from(array('pat' => 'patient'))->columns(array('*'))
		->join(array('pers' => 'personne'), 'pers.idpersonne = pat.idpersonne' , array('Nom'=>'nom','Prenom'=>'prenom','Datenaissance'=>'date_naissance','Age'=>'age','Adresse'=>'adresse','Nationalite'=>'nationalite_actuelle', 'id'=>'idpersonne', 'id2'=>'idpersonne', 'id3'=>'idpersonne'))
		->join(array('fact' => 'facturation'), 'fact.idpatient = pat.idpersonne', array('Idfacturation' => 'idfacturation', 'Date' => 'date', 'Heure' => 'heure') )
		->join(array('bp' => 'bilan_prelevement'), 'bp.idfacturation = fact.idfacturation', array('Idbilan' => 'idbilan', 'DateEnregistrementBp' => 'date_enregistrement') )
		->join(array( 'tp' => 'tri_prelevement' ), 'tp.idbilan = bp.idbilan', array('IdbilanTri' => 'idbilan', 'DateEnregistrementTri' => 'date_enregistrement') )
		->join(array( 'rda' => 'resultat_demande_analyse' ), 'rda.iddemande_analyse = tp.iddemande', array('DateValidationRda' => 'date_validation') )
		->where( array ( 'rda.valide' => 1 ) )
		->group('pat.idpersonne')
		->order('DateValidationRda ASC');
	
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
	
					else if ($aColumns[$i] == 'Age') {
						$row[] = "<div style='text-align: center;' >".$aRow[ $aColumns[$i] ]."</div>";
					}
	
					else if ($aColumns[$i] == 'DateValidationRda') {
						$row[] = $Control->convertDateTimeHm($aRow[ 'DateValidationRda' ]);
					}
	
					else if ($aColumns[$i] == 'id') {
						$html  ="<infoBulleVue> <a id='".$aRow[ $aColumns[$i] ]."' href='javascript:visualiser(".$aRow[ 'id' ].");'>";
						$html .="<img style='display: inline; margin-right: 10%;' src='".$tabURI[0]."public/images_icons/voir2.png' title='d&eacute;tails'></a></infoBulleVue>";
	
						$html .= "<infoBulleVue> <a href='javascript:listeResultatsAnalyses(".$aRow[ 'id' ].");'>";
						$html .="<img style='display: inline; margin-right: 10%;' src='".$tabURI[0]."public/images_icons/details.png' title='Analyses demand&eacute;es'></a></infoBulleVue>";
	
	
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
	
	
	//FONCTION UTILISER DANS LE MODULE DU SERCETAIRE
	//FONCTION UTILISER DANS LE MODULE DU SECRETAIRE
	public function cocherRappelPatient($idbilan, $idemploye){
		
		$date_enregistrement = (new \DateTime() ) ->format('Y-m-d H:i:s');
		
		$db = $this->tableGateway->getAdapter();
		$sql = new Sql($db);
		$sQuery = $sql->update() ->table('bilan_prelevement') ->set( array('rappel_patient' => 1, 'idsecretaire' => $idemploye, 'date_rappel' => $date_enregistrement ) ) 
		->where(array('idbilan' => $idbilan ));
		$sql->prepareStatementForSqlObject($sQuery)->execute();
	}
	
	
	//FONCTION UTILISER DANS LE MODULE DU SERCETAIRE
	//FONCTION UTILISER DANS LE MODULE DU SECRETAIRE
	public function decocherRappelPatient($idbilan, $idemploye){
	
		$date_enregistrement = (new \DateTime() ) ->format('Y-m-d H:i:s');
	
		$db = $this->tableGateway->getAdapter();
		$sql = new Sql($db);
		$sQuery = $sql->update() ->table('bilan_prelevement') ->set( array('rappel_patient' => 0, 'idsecretaire' => $idemploye, 'date_rappel' => $date_enregistrement ) )
		->where(array('idbilan' => $idbilan ));
		$sql->prepareStatementForSqlObject($sQuery)->execute();
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
	
	public function getTypageHemoglobine($idTypage)
	{
		$db = $this->tableGateway->getAdapter();
		$sql = new Sql($db);
		$sQuery = $sql->select()
		->from(array('th' => 'typage_hemoglobine'))->columns(array('*'))
		->where(array('idtypage' => $idTypage));
		$result = $sql->prepareStatementForSqlObject($sQuery)->execute();
	
		return $result->current();
	}
	
	
	public function validerDepistagePatient($idpatient, $idemploye)
	{
		$date_validation = (new \DateTime() ) ->format('Y-m-d H:i:s');
		
		$data = array( 'valide' => 1 , 'validation_date' => $date_validation, 'validation_id_employe' => $idemploye);
	
		$db = $this->tableGateway->getAdapter();
		$sql = new Sql($db);
		$sQuery = $sql->update() ->table('depistage') ->set( $data ) ->where( array('idpatient' => $idpatient ) );
		 
		$sql->prepareStatementForSqlObject($sQuery) ->execute();
	}
	
	public function retrirerValidationDepistagePatient($idpatient, $idemploye)
	{
		$data = array( 'valide' => 0 , 'validation_date' => null, 'validation_id_employe' => $idemploye);
	
		$db = $this->tableGateway->getAdapter();
		$sql = new Sql($db);
		$sQuery = $sql->update() ->table('depistage') ->set( $data ) ->where( array('idpatient' => $idpatient ) );
			
		$sql->prepareStatementForSqlObject($sQuery) ->execute();
	}
	
	
	public function updatePatientCodePatient($idpersonne, $codepatient)
	{
		$data = array( 'numero_dossier' => $codepatient );
		$this->tableGateway->update($data, array('idpersonne' => $idpersonne) );
	}
	
	/**
	 * Liste des ethnies des patients dépistés
	 * @return \Zend\Db\Adapter\Driver\ResultInterface
	 */
	public function getListeEthniesPatientsDepistes()
	{
		$db = $this->tableGateway->getAdapter();
		$sql = new Sql($db);
		$sQuery = $sql->select()
		->from(array('dep' => 'depistage'))->columns(array('*'))
		->group('ethnie');
		return $sql->prepareStatementForSqlObject($sQuery)->execute();
	}
	
	
}