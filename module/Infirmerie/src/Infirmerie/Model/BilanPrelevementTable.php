<?php

namespace Infirmerie\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Predicate\In;
use Infirmerie\View\Helper\DateHelper;
use Zend\Db\Sql\Predicate\NotIn;

class BilanPrelevementTable {
	protected $tableGateway;
 	public function __construct(TableGateway $tableGateway) {
 		$this->tableGateway = $tableGateway;
 	}
 	
 	
 	public function addBilanPrelevement($donnees){
 		
 		if($this->getBilanPrelevement($donnees['idfacturation'])){
 			return null;			
 		}else{
 			return $this->tableGateway->getLastInsertValue( $this->tableGateway->insert($donnees) );
 		}
 	}
 	
 	
 	public function getBilanPrelevement($idfacturation) {
 		$idfacturation = ( int ) $idfacturation;
 		$rowset = $this->tableGateway->select ( array (
 				'idfacturation' => $idfacturation
 		) );
 		
 		$row =  $rowset->current ();
 		if (! $row) {
 			return null;
 		}
 		return $row;
 	}
 	
 	
 	public function updateBilanPrelevement($donnees, $idfacturation) {
 		
 		$this->tableGateway->update($donnees, array('idfacturation' => $idfacturation) );
 		 	
 	}
 	

 	public function addBilanPrelevementRepris($donnees) {

 		$db = $this->tableGateway->getAdapter();
 		$sql = new Sql($db);
 		$sQuery = $sql->insert()
 		->into('bilan_prelevement_repris')
 		->values($donnees);
 		return $sql->prepareStatementForSqlObject($sQuery)->execute() ->getGeneratedValue();
 		
 	}
 	
 	
 	public function getBilanPrelevementRepris($idbilan) {
 		$db = $this->tableGateway->getAdapter();
 		$sql = new Sql($db);
 		$sQuery = $sql->select()
 		->from('bilan_prelevement_repris')
 		->where(array('idbilan'=>$idbilan));
 		return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
 	}
 	
 	
 	public function getBilanPrelevementIdbilan($idbilan) {
 		$idbilan = ( int ) $idbilan;
 		$rowset = $this->tableGateway->select ( array (
 				'idbilan' => $idbilan
 		) );
 			
 		$row =  $rowset->current ();
 		if (! $row) {
 			return null;
 		}
 		return $row;
 	}
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	//FONCTION UTILISER DANS LE MODULE DU TECHNICIEN
 	//FONCTION UTILISER DANS LE MODULE DU TECHNICIEN
 	
 	//********** RECUPERER LA LISTE DES PATIENTS AYANT DES BILANS DE PRELEVEMENTS  REPRIS *********
 	//********** RECUPERER LA LISTE DES PATIENTS AYANT DES BILANS DE PRELEVEMENTS  REPRIS *********
 	
 	public function getListeBilansPrelevement() {
 	
 		$db = $this->tableGateway->getAdapter();
 			
 		$aColumns = array('numero_dossier', 'Nom','Prenom','Datenaissance', 'Adresse', 'DateEnregistrementBp', 'id', 'Idfacturation');
 			
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
 		 * LISTE DES BILANS REPRIS
 		 * LISTE DES BILANS REPRIS
 		 * LISTE DES BILANS REPRIS
 		 */
 		$sql2 = new Sql ($db );
 		$subsel = $sql2->select ();
 		$subsel->from ( array ( 'tp' => 'tri_prelevement_repris' ) );
 		$subsel->columns (array ( 'idbilan' ) );
 		
 		/*
 		 * SQL queries
   		 * Liste des patients pour lesquels le bilan est repris
 		 */
 		$sql = new Sql($db);
 		$sQuery = $sql->select()
 		->from(array('pat' => 'patient'))->columns(array('*'))
 		->join(array('pers' => 'personne'), 'pers.idpersonne = pat.idpersonne' , array('Nom'=>'nom','Prenom'=>'prenom','Datenaissance'=>'date_naissance','Sexe'=>'sexe','Adresse'=>'adresse','Nationalite'=>'nationalite_actuelle', 'id'=>'idpersonne', 'id2'=>'idpersonne'))
 		->join(array('fact' => 'facturation'), 'fact.idpatient = pat.idpersonne', array('Idfacturation' => 'idfacturation', 'Date' => 'date', 'Heure' => 'heure') )
 		->join(array('bp' => 'bilan_prelevement'), 'bp.idfacturation = fact.idfacturation', array('Idbilan' => 'idbilan') )
 		->join(array('bpr' => 'bilan_prelevement_repris'), 'bpr.idbilan = bp.idbilan', array('Idbilanrepris' => 'idbilanrepris', 'DateEnregistrementBp' => 'date_enregistrement') )
 		->where( array ( new NotIn( 'bp.idbilan', $subsel ) ) )
 		->group('fact.idfacturation')
 		->order('bpr.idbilanrepris ASC');
 		
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
 						$row[] = "<div>".$aRow[ $aColumns[$i] ]."</div>";
 					}
 		
 					else if ($aColumns[$i] == 'DateEnregistrementBp') {
 						$row[] = $Control->convertDateTimeHm($aRow[ 'DateEnregistrementBp' ]);
 					}
 		
 					else if ($aColumns[$i] == 'id') {
 						$html  ="<infoBulleVue> <a id='".$aRow[ $aColumns[$i] ]."' href='javascript:bilanPrelevement(".$aRow[ 'Idfacturation' ].");'>";
 						$html .="<img style='display: inline; margin-right: 10%;' src='".$tabURI[0]."public/images_icons/voir2.png' title='d&eacute;tails'></a></infoBulleVue>";
 		
 						$html .="<infoBulleVue> <a href='javascript:trierPrelevementRepris(".$aRow[ 'id' ].",".$aRow[ 'Idfacturation' ].");'>";
 						$html .="<img style='display: inline; margin-right: 10%;' src='".$tabURI[0]."public/images_icons/wallet_16.png' title='Tri des pr&eacute;l&egrave;vements'></a> <i style='margin-left: 7%; color: green;'>r</i></infoBulleVue>";
 		
 						$row[] = $html;
 					}
 		
 					else {
 						$row[] = $aRow[ $aColumns[$i] ];
 					}
 		
 				}
 			}
 			$output['aaData'][] = $row;
 		}
 		
 		
 		
 		
 		
 		
 		
 		
 		
 		
 		
 		
 		
 		
 		
 		
 		
 		
 		
 		
 		
 		
 		
 		
 		
 		
 		
 		
 		
 		
 		
 		
 		
 		
 		
 		
 		
 		
 		
 		
 		/* LE NOUVEAU CODE AVEC LA PARTIE INFIRMERIE
 		 * SQL queries
 		* Liste des bilans faisant l'objet de tris
 		*/
 		$sql2 = new Sql ($db );
 		$subselect = $sql2->select ();
 		$subselect->from ( array ( 'tp' => 'tri_prelevement' ) );
 		$subselect->columns (array ( 'idbilan' ) );
 			
 		/*
 		 * SQL queries
 		* Liste des patients pour lesquels le tri des analyses n'est pas encore fait
 		*/
 		$sql = new Sql($db);
 		$sQuery = $sql->select()
 		->from(array('pat' => 'patient'))->columns(array('*'))
 		->join(array('pers' => 'personne'), 'pers.idpersonne = pat.idpersonne' , array('Nom'=>'nom','Prenom'=>'prenom','Datenaissance'=>'date_naissance','Sexe'=>'sexe','Adresse'=>'adresse','Nationalite'=>'nationalite_actuelle', 'id'=>'idpersonne', 'id2'=>'idpersonne'))
 		->join(array('fact' => 'facturation'), 'fact.idpatient = pat.idpersonne', array('Idfacturation' => 'idfacturation', 'Date' => 'date', 'Heure' => 'heure') )
 		->join(array('bp' => 'bilan_prelevement'), 'bp.idfacturation = fact.idfacturation', array('Idbilan' => 'idbilan', 'DateEnregistrementBp' => 'date_enregistrement') )
 		->where( array ( new NotIn( 'bp.idbilan', $subselect ) ) )
 		->group('fact.idfacturation')
 		->order('bp.idbilan ASC');
 			
 		/* Data set length after filtering */
 		$stat = $sql->prepareStatementForSqlObject($sQuery);
 		$rResultFt = $stat->execute();
 		$iFilteredTotal = count($rResultFt);
 			
 			
 		$rResult = $rResultFt;
 			
//  		$output = array(
//  				//"sEcho" => intval($_GET['sEcho']),
//  				//"iTotalRecords" => $iTotal,
//  				"iTotalDisplayRecords" => $iFilteredTotal,
//  				"aaData" => array()
//  		);
 			
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
 						$row[] = "<div>".$aRow[ $aColumns[$i] ]."</div>";
 					}
 	
 					else if ($aColumns[$i] == 'DateEnregistrementBp') {
 						$row[] = $Control->convertDateTimeHm($aRow[ 'DateEnregistrementBp' ]);
 					}
 	
 					else if ($aColumns[$i] == 'id') {
 						$html  ="<infoBulleVue> <a id='".$aRow[ $aColumns[$i] ]."' href='javascript:bilanPrelevement(".$aRow[ 'Idfacturation' ].");'>";
 						$html .="<img style='display: inline; margin-right: 10%;' src='".$tabURI[0]."public/images_icons/voir2.png' title='d&eacute;tails'></a></infoBulleVue>";
 							
 						$html .= "<infoBulleVue> <a href='javascript:trierPrelevement(".$aRow[ 'id' ].",".$aRow[ 'Idfacturation' ].");'>";
 						$html .="<img style='display: inline; margin-right: 10%;' src='".$tabURI[0]."public/images_icons/wallet_16.png' title='Tri des pr&eacute;l&egrave;vements'></a></infoBulleVue>";
 							
 							
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
	
 	
 	//FONCTION UTILISER DANS LE MODULE DU TECHNICIEN
 	//FONCTION UTILISER DANS LE MODULE DU TECHNICIEN
 	public function getListeAnalysesDemandeesAyantUnBilan($idfacturation){
 		$db = $this->tableGateway->getAdapter();
 		$sql = new Sql($db);
 		$sQuery = $sql->select()
 		->from(array('f' => 'facturation'))->columns(array('*'))
 		->join(array('fda' => 'facturation_demande_analyse'), 'f.idfacturation = fda.idfacturation', array('*'))
 		->join(array('d' => 'demande_analyse'), 'd.iddemande = fda.iddemande_analyse', array('*'))
 		->join(array('a' => 'analyse'), 'a.idanalyse = d.idanalyse', array('*'))
 		->join(array('t' => 'type_analyse'), 't.idtype = a.idtype_analyse', array('*'))
 		->join(array('tb' => 'tube'), 'tb.idtube = a.idtube', array('Idtube' =>'idtube', 'LibelleTube' =>'libelle'))
 		->join(array('bp' => 'bilan_prelevement'), 'bp.idfacturation = f.idfacturation', array('DateEnregistrementBp' => 'date_enregistrement') )
 		->where(array('f.idfacturation' => $idfacturation))
 		->order(array('t.idtype' => 'ASC', 'd.idanalyse' => 'ASC'));
 	
 		$resultat = $sql->prepareStatementForSqlObject($sQuery)->execute();
 	
 		$donnees = array();
 		foreach ($resultat as $result){
 			$donnees[] = $result;
 		}
 	
 		return $donnees;
 	}
 	
 	//FONCTION UTILISER DANS LE MODULE DU TECHNICIEN
 	//FONCTION UTILISER DANS LE MODULE DU TECHNICIEN
 	public function getListeAnalysesDemandeesAyantUnBilanNonConforme($idbilan){
 		$db = $this->tableGateway->getAdapter();
 		$sql = new Sql($db);
 		$sQuery = $sql->select()
 		->from(array('bp' => 'bilan_prelevement'))
 		->join(array('tp' => 'tri_prelevement' ), 'tp.idbilan = bp.idbilan', array('IdbilanTri' => 'idbilan', 'Conformite' => 'conformite', 'DateEnregistrementTri' => 'date_enregistrement') )
        ->join(array('d' => 'demande_analyse'), 'd.iddemande = tp.iddemande', array('*'))
 		->join(array('a' => 'analyse'), 'a.idanalyse = d.idanalyse', array('*'))
 		->join(array('t' => 'type_analyse'), 't.idtype = a.idtype_analyse', array('*'))
 		->join(array('tb' => 'tube'), 'tb.idtube = a.idtube', array('Idtube' =>'idtube', 'LibelleTube' =>'libelle'))
 		->where(array('bp.idbilan' => $idbilan, 'conformite' => 0))
 		->order(array('t.idtype' => 'ASC', 'd.idanalyse' => 'ASC'));
 	
 		$resultat = $sql->prepareStatementForSqlObject($sQuery)->execute();
 	
 		$donnees = array();
 		foreach ($resultat as $result){
 			$donnees[] = $result;
 		}
 	
 		return $donnees;
 	}
 	
 	

 	//FONCTION UTILISER DANS LE MODULE DU TECHNICIEN
 	//FONCTION UTILISER DANS LE MODULE DU TECHNICIEN
 	
 	//********** RECUPERER LA LISTE DES PATIENTS AYANT DES BILANS DE PRELEVEMENTS DEJA TRIES *********
 	//********** RECUPERER LA LISTE DES PATIENTS AYANT DES BILANS DE PRELEVEMENTS DEJA TRIES *********
 	
 	public function getListeBilansPrelevementTries() {
 	
 		$db = $this->tableGateway->getAdapter();
 	
 		$aColumns = array('numero_dossier', 'Nom', 'Prenom', 'Datenaissance', 'Adresse', 'DateEnregistrementTri', 'id', 'Idfacturation');
 	
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
 		 * Liste des patients pour lesquels le bilan est repris et le tris est refais pour les analyses reprises 
 		 */
 		$sql = new Sql($db);
 		$sQuery = $sql->select()
 		->from(array('pat' => 'patient'))->columns(array('*'))
 		->join(array('pers' => 'personne'), 'pers.idpersonne = pat.idpersonne' , array('Nom'=>'nom','Prenom'=>'prenom','Datenaissance'=>'date_naissance','Sexe'=>'sexe','Adresse'=>'adresse','Nationalite'=>'nationalite_actuelle', 'id'=>'idpersonne', 'id2'=>'idpersonne'))
 		->join(array('fact' => 'facturation'), 'fact.idpatient = pat.idpersonne', array('Idfacturation' => 'idfacturation', 'Date' => 'date', 'Heure' => 'heure') )
 		->join(array('bp' => 'bilan_prelevement'), 'bp.idfacturation = fact.idfacturation', array('Idbilan' => 'idbilan', 'DateEnregistrementBp' => 'date_enregistrement') )
 		->join(array( 'tp' => 'tri_prelevement_repris' ), 'tp.idbilan = bp.idbilan', array('IdtriRepris' => 'idtrirepris', 'IdbilanTri' => 'idbilan', 'DateEnregistrementTri' => 'date_enregistrement') )
 		->group('tp.idbilan')
 		->order('tp.idtrirepris ASC');
 		
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
 						$row[] = "<div>".$aRow[ $aColumns[$i] ]."</div>";
 					}
 						
 					else if ($aColumns[$i] == 'DateEnregistrementTri') {
 						$row[] = $Control->convertDateTimeHm($aRow[ 'DateEnregistrementTri' ]);
 					}
 						
 					else if ($aColumns[$i] == 'id') {
 						$html  ="<infoBulleVue> <a id='".$aRow[ $aColumns[$i] ]."' href='javascript:bilanPrelevement(".$aRow[ 'Idfacturation' ].");'>";
 						$html .="<img style='display: inline; margin-right: 10%;' src='".$tabURI[0]."public/images_icons/voir2.png' title='d&eacute;tails'></a></infoBulleVue>";
 							
 						$html .="<infoBulleVue> <a href='javascript:modifiertriPrelevementRepris(".$aRow[ 'id' ].",".$aRow[ 'Idfacturation' ].");'>";
 						$html .="<img style='display: inline; margin-right: 10%;' src='".$tabURI[0]."public/images_icons/edit_item_btn.png' title='Tri des pr&eacute;l&egrave;vements'></a> <i style='margin-left: 7%; color: green;'>r</i></infoBulleVue>";
 							
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
 		* Liste des patients pour lesquels le tri des analyses est déjà fait
 		*/
 		$sql = new Sql($db);
 		$sQuery = $sql->select()
 		->from(array('pat' => 'patient'))->columns(array('*'))
 		->join(array('pers' => 'personne'), 'pers.idpersonne = pat.idpersonne' , array('Nom'=>'nom','Prenom'=>'prenom','Datenaissance'=>'date_naissance','Sexe'=>'sexe','Adresse'=>'adresse','Nationalite'=>'nationalite_actuelle', 'id'=>'idpersonne', 'id2'=>'idpersonne'))
 		->join(array('fact' => 'facturation'), 'fact.idpatient = pat.idpersonne', array('Idfacturation' => 'idfacturation', 'Date' => 'date', 'Heure' => 'heure') )
 		->join(array('bp' => 'bilan_prelevement'), 'bp.idfacturation = fact.idfacturation', array('Idbilan' => 'idbilan', 'DateEnregistrementBp' => 'date_enregistrement') )
 		->join(array( 'tp' => 'tri_prelevement' ), 'tp.idbilan = bp.idbilan', array('IdbilanTri' => 'idbilan', 'DateEnregistrementTri' => 'date_enregistrement') )
 		->group('tp.idbilan')
 		->order('tp.idtri DESC');
 	
 		/* Data set length after filtering */
 		$stat = $sql->prepareStatementForSqlObject($sQuery);
 		$rResultFt = $stat->execute();
 		$iFilteredTotal = count($rResultFt);
 	
 	
 		$rResult = $rResultFt;
 	
//  		$output = array(
//  				//"sEcho" => intval($_GET['sEcho']),
//  				//"iTotalRecords" => $iTotal,
//  				"iTotalDisplayRecords" => $iFilteredTotal,
//  				"aaData" => array()
//  		);
 	
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
 	
 					else if ($aColumns[$i] == 'DateEnregistrementTri') {
 						$row[] = $Control->convertDateTimeHm($aRow[ 'DateEnregistrementTri' ]);
 					}
 	
 					else if ($aColumns[$i] == 'id') {
 						$html  ="<infoBulleVue> <a id='".$aRow[ $aColumns[$i] ]."' href='javascript:bilanPrelevement(".$aRow[ 'Idfacturation' ].");'>";
 						$html .="<img style='display: inline; margin-right: 10%;' src='".$tabURI[0]."public/images_icons/voir2.png' title='d&eacute;tails'></a></infoBulleVue>";
 	
 						$html .= "<infoBulleVue> <a href='javascript:modifierPrelevementTrie(".$aRow[ 'id' ].",".$aRow[ 'Idfacturation' ].");'>";
 						$html .="<img style='display: inline; margin-right: 10%;' src='".$tabURI[0]."public/images_icons/edit_item_btn.png' title='Modifier les Tris'></a></infoBulleVue>";
 	
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
 	
 	
 	//FONCTION UTILISER DANS LE MODULE DU TECHNICIEN
 	//FONCTION UTILISER DANS LE MODULE DU TECHNICIEN
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
 	
 	//********** RECUPERER LA LISTE DES PATIENTS AYANT DES BILANS DE PRELEVEMENTS DEJA TRIES *********
 	//********** RECUPERER LA LISTE DES PATIENTS AYANT DES BILANS DE PRELEVEMENTS DEJA TRIES *********
 	
 	public function getListeBilansPrelevementTriesPourResultats() {
 	
 		$db = $this->tableGateway->getAdapter();
 	
 		$aColumns = array('numero_dossier', 'Nom' ,'Prenom' ,'Age' ,'Adresse' ,'DateEnregistrementTri', 'id', 'Idfacturation', 'id3');
 	
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
 	
 		$sql2 = new Sql ($db);
 		$subselect = $sql2->select ();
 		$subselect->from ( array ( 'rda' => 'resultat_demande_analyse' ) );
 		$subselect->where( array ( 'rda.valide' => 1 ) );
 		$subselect->columns (array ( 'iddemande_analyse' ) );
 		
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
 		->where( array (new NotIn ( 'tp.iddemande', $subselect ) ) )
 		->group('pat.idpersonne')
 		->order('tp.idtri ASC');
 	
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
 	
 					else if ($aColumns[$i] == 'DateEnregistrementTri') {
 						$row[] = $Control->convertDateTimeHm($aRow[ 'DateEnregistrementTri' ]);
 					}
 					
 					else if ($aColumns[$i] == 'Adresse'){
 						$row[] = "<div>".$aRow[ $aColumns[$i]]."</div>";
 					}
 	
 					else if ($aColumns[$i] == 'id') {
 						$html  ="<infoBulleVue> <a id='".$aRow[ $aColumns[$i] ]."' href='javascript:visualiser(".$aRow[ 'id' ].");'>";
 						$html .="<img style='display: inline; margin-right: 10%;' src='".$tabURI[0]."public/images_icons/voir2.png' title='d&eacute;tails'></a></infoBulleVue>";
 	
 						$html .= "<infoBulleVue> <a href='javascript:listeAnalysesTriees(".$aRow[ 'id' ].");'>";
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
 	
 	
 	//FONCTION UTILISER DANS LE MODULE DU TECHNICIEN
 	//FONCTION UTILISER DANS LE MODULE DU TECHNICIEN
 	public function getListeAnalysesTrieesDuBilanAyantResultats($idbilan) {
 		
 		$db = $this->tableGateway->getAdapter();
 			
 		$sql2 = new Sql ($db);
 		$subselect = $sql2->select ();
 		$subselect->from ( array ( 'rda' => 'resultat_demande_analyse' ) );
 		$subselect->columns (array ( 'iddemande_analyse' ) );
 			
 		$sql = new Sql($db);
 		$sQuery = $sql->select ()
 		->from(array('tp' => 'tri_prelevement'))->columns(array('*'))
 		->where( array ('idbilan' => $idbilan , new In ( 'tp.iddemande', $subselect ) ) );
 		
 		$resultat = $sql->prepareStatementForSqlObject($sQuery) ->execute();

 		$tab = array();
 		foreach ($resultat as $result){
 			$tab [] = $result['idanalyse'];
 		}
 		
 		return $tab;
 		
 	}
 	
 	
 	//FONCTION UTILISER DANS LE MODULE DU TECHNICIEN POUR ALERTER
 	//FONCTION UTILISER DANS LE MODULE DU TECHNICIEN POUR ALERTER 
 	public function getVerifierBilanRepris($idbilan) {
 			
 		$db = $this->tableGateway->getAdapter();
 	
 		$sql2 = new Sql ($db);
 		$subselect = $sql2->select ();
 		$subselect->from ( array ( 'bpr' => 'bilan_prelevement_repris' ) );
 		$subselect->columns (array ( 'idbilan' ) );
 	
 		$sql = new Sql($db);
 		$sQuery = $sql->select ()
 		->from(array('tp' => 'tri_prelevement'))->columns(array('*'))
 		->where( array ('tp.idbilan' => $idbilan, new In ( 'tp.idbilan', $subselect ) ) );
 			
 		$resultat = $sql->prepareStatementForSqlObject($sQuery) ->execute();
 	
 		$tab = array();
 		foreach ($resultat as $result){
 			$tab [] = $result['idanalyse'];
 		}
 			
 		return $tab;
 			
 	}
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	function pourCorrection(){
 		$liste_prelevement = $this->tableGateway->select()->toArray();
 		/**
 		 * Commenter le var_dump pour corriger la BD
 		 */
 		var_dump($liste_prelevement); exit();
 		
 		for($i=0; $i<count($liste_prelevement); $i++){
 			$date_convertie = (new DateHelper())->convertDateInAnglais( substr($liste_prelevement[$i]['date_heure'], 0, 10) );
 			$this->tableGateway->update(array('date_prelevement' => $date_convertie), array('idbilan' => $liste_prelevement[$i]['idbilan']) );
 		}
 		
 		return $this->tableGateway->select()->toArray();
 	} 
 	
 	
}


