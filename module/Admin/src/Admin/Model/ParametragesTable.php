<?php
namespace Admin\Model;

use Zend\Db\TableGateway\TableGateway;
use Facturation\View\Helper\DateHelper;
use Zend\Db\Sql\Sql;

class ParametragesTable
{
	protected $tableGateway;

	public function __construct(TableGateway $tableGateway)
	{
		$this->tableGateway = $tableGateway;
	}

	/**
	 * Recuperer la liste des hopitaux
	 */
	public function getListeHopitaux()
	{
		$db = $this->tableGateway->getAdapter();
		
		$aColumns = array('Nom','Region','Departement', 'Id');
		
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
		->from(array('h' => 'hopital'))->columns(array('Nom'=>'NOM_HOPITAL','Id'=>'ID_HOPITAL'))
		->join(array('d' => 'departement') ,'d.id = h.ID_DEPARTEMENT' , array('Departement'=>'nom') )
		->join(array('r' => 'region') ,'r.id = d.id_region' , array('Region'=>'nom') )
		->order('h.ID_HOPITAL DESC');
		
		
		/* Data set length after filtering */
		$stat = $sql->prepareStatementForSqlObject($sQuery);
		$rResultFt = $stat->execute();
		$iFilteredTotal = count($rResultFt);
		
		$rResult = $rResultFt;
		
		$output = array(
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
				if ( $aColumns[$i] != ' ')
				{ 
					/* General output */
					if ($aColumns[$i] == 'Id') {
						$html  ="<a href='javascript:visualiserDetails(".$aRow[ $aColumns[$i] ].")'>";
						$html .="<img style='display: inline; margin-right: 15%;' src='".$tabURI[0]."public/images_icons/voir2.png' title='dÃ©tails'></a>";
		
						$html  .="<a href='javascript:modifier(".$aRow[ $aColumns[$i] ].")'>";
						$html .="<img style='display: inline; margin-right: 5%;' src='".$tabURI[0]."public/images_icons/pencil_16.png' title='modifier'></a>";
		
						$html .="<input id='".$aRow[ $aColumns[$i] ]."'   type='hidden' value='".$aRow[ 'Id' ]."'>";
		
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
	
	/**
	 * Liste des regions
	 */
	public function getListeRegions()
	{
		$db = $this->tableGateway->getAdapter();
		$sql = new Sql($db);
		$sQuery = $sql->select()
		->from(array('r' => 'region'))->columns(array('*'));
		
		$stat = $sql->prepareStatementForSqlObject($sQuery);
		$Result = $stat->execute();
		
		$region = array('' => '');
		foreach ($Result as $resultat){
			$region[$resultat['id']] = $resultat['nom'];
		} 
		return $region;
	}
	
	/**
	 * Liste des departements
	 */
	public function getListeDepartements($id)
	{
		$db = $this->tableGateway->getAdapter();
		$sql = new Sql($db);
		$sQuery = $sql->select()
		->from(array('d' => 'departement'))->columns(array('*'))
		->where(array('id_region' => $id));
	
		$stat = $sql->prepareStatementForSqlObject($sQuery);
		$Result = $stat->execute();
	
		return $Result;
	}
	
	/**
	 * Ajout d'un hopital
	 */
	public function addHopital($nom, $id_departement, $id_personne, $directeur, $note)
	{
		$db = $this->tableGateway->getAdapter();
		$sql = new Sql($db);
		$sQuery = $sql->insert()
		->into('hopital')
		->values(array(
				'NOM_HOPITAL' => $nom, 
				'ID_DEPARTEMENT' => $id_departement, 
				'ID_PERSONNE' => $id_personne,
				'DIRECTEUR' => $directeur,
				'NOTE' => $note,
		));
	
		$stat = $sql->prepareStatementForSqlObject($sQuery);
		$stat->execute();
	}
	
	/**
	 * Mis à jour d'un hopital
	 */
	public function updateHopital($updateHopital, $nom, $id_departement, $id_personne, $directeur, $note)
	{
		$db = $this->tableGateway->getAdapter();
		$sql = new Sql($db);
		$sQuery = $sql->update()
		->table('hopital')
		->set(array(
				'NOM_HOPITAL' => $nom,
				'ID_DEPARTEMENT' => $id_departement,
				'ID_PERSONNE' => $id_personne,
				'DIRECTEUR' => $directeur,
				'NOTE' => $note,
		))
		->where(array('ID_HOPITAL' => $updateHopital));
	
		$stat = $sql->prepareStatementForSqlObject($sQuery);
		$stat->execute();
	}
	
	/**
	 * Ajout d'un service
	 */
	public function addService($nom, $domaine, $tarif, $id_employe)
	{
		$db = $this->tableGateway->getAdapter();
		$sql = new Sql($db);
		$sQuery = $sql->insert()
		->into('service')
		->values(array(
				'NOM' => $nom,
				'DOMAINE' => $domaine,
				'TARIF' => $tarif,
				'ID_EMPLOYE' => $id_employe,
		));
	
		$stat = $sql->prepareStatementForSqlObject($sQuery);
		$stat->execute();
	}
	
	
	/**
	 * Mis à jour d'un service
	 */
	public function updateService($updateService, $nom, $domaine, $tarif, $id_employe)
	{
		$db = $this->tableGateway->getAdapter();
		$sql = new Sql($db);
		$sQuery = $sql->update()
		->table('service')
		->set(array(
				'NOM' => $nom,
				'DOMAINE' => $domaine,
				'TARIF' => $tarif,
				'ID_EMPLOYE' => $id_employe,
		))
		->where(array('ID_SERVICE' => $updateService));
	
		$stat = $sql->prepareStatementForSqlObject($sQuery);
		$stat->execute();
	}
	
	
	/**
	 * Informations d'un hopital
	 */
	public function getInfosHopital($id)
	{
		$db = $this->tableGateway->getAdapter();
		$sql = new Sql($db);
		$sQuery = $sql->select()
		->from(array('h' => 'hopital'))->columns(array('Nom'=>'NOM_HOPITAL','Id'=>'ID_HOPITAL','Directeur'=>'DIRECTEUR','Note'=>'NOTE'))
		->join(array('d' => 'departement') ,'d.id = h.ID_DEPARTEMENT' , array('Departement'=>'nom', 'Id_departement'=>'id') )
		->join(array('r' => 'region') ,'r.id = d.id_region' , array('Region'=>'nom', 'Id_region'=>'id') )
		->where(array('h.ID_HOPITAL' => $id));
		$stat = $sql->prepareStatementForSqlObject($sQuery);
		return $stat->execute()->current();
	}
	

	/**
	 * Informations d'un service
	 */
	public function getInfosService($id)
	{
		$db = $this->tableGateway->getAdapter();
		$sql = new Sql($db);
		$sQuery = $sql->select()
		->from(array('s' => 'service'))->columns(array('*'))
		->where(array('s.ID_SERVICE' => $id));
		$stat = $sql->prepareStatementForSqlObject($sQuery);
		return $stat->execute()->current();
	}
	
	public function prixMill($prix) {
		$str="";
		$long =strlen($prix)-1;
	
		for($i = $long ; $i>=0; $i--)
		{
		$j=$long -$i;
		if( ($j%3 == 0) && $j!=0)
		{ $str= " ".$str;   }
		$p= $prix[$i];
	
		$str = $p.$str;
		}
		return($str);
	}
	
	
    public function getListeService()
    {
    	$db = $this->tableGateway->getAdapter();
    	
    	$aColumns = array('Nom','Domaine','Tarif', 'Id');
    	
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
    	->from(array('s' => 'service'))->columns(array('Nom'=>'NOM','Domaine'=>'DOMAINE','Tarif'=>'TARIF','Id'=>'ID_SERVICE'))
    	->order('s.ID_SERVICE DESC');
    	
    	
    	/* Data set length after filtering */
    	$stat = $sql->prepareStatementForSqlObject($sQuery);
    	$rResultFt = $stat->execute();
    	$iFilteredTotal = count($rResultFt);
    	
    	$rResult = $rResultFt;
    	
    	$output = array(
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
    			if ( $aColumns[$i] != ' ')
    			{
    				/* General output */
    				if ($aColumns[$i] == 'Id') {
    					$html  ="<a style='float: left; padding-right: 10%;' href='javascript:visualiserDetailsService(".$aRow[ $aColumns[$i] ].")'>";
    					$html .="<img style='display: inline; ' src='".$tabURI[0]."public/images_icons/voir2.png' title='dÃ©tails'></a>";
    	
    					$html  .="<a style='float: left; padding-right: 10%;' href='javascript:modifierService(".$aRow[ $aColumns[$i] ].")'>";
    					$html .="<img style='display: inline;' src='".$tabURI[0]."public/images_icons/pencil_16.png' title='modifier'></a>";
    	
    					$html  .="<a id='service_".$aRow[ $aColumns[$i] ]."' style='float: left;' href='javascript:supprimerService(".$aRow[ $aColumns[$i] ].")'>";
    					$html .="<img style='display: inline; width: 15px; height: 15px;' src='".$tabURI[0]."public/images_icons/sup2.png' title='supprimer'></a>";
    					$html .="<input id='".$aRow[ $aColumns[$i] ]."'   type='hidden' value='".$aRow[ 'Id' ]."'>";
    	
    					$row[] = $html;
    				}
    				else 
    					if ($aColumns[$i] == 'Tarif') {
    						$row[] = "<div style='width: 100%; height: 10px;'><span style='float: right;'>".$this->prixMill( $aRow[ $aColumns[$i] ] )."</span></div>";
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
    
    public function existeServiceEmploye($id) {
    	$db = $this->tableGateway->getAdapter();
    	$sql = new Sql($db);
    	$sQuery = $sql->select()
    	->from('service_employe')
    	->where(array('id_service' => $id));
    	
    	return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
    }
    
    public function supprimerService($id) {
    	
    	if(!$this->existeServiceEmploye($id)){ //Cela veut dire personne n'est encore affecter dans ce service
    		$db = $this->tableGateway->getAdapter();
    		$sql = new Sql($db);
    		$sQuery = $sql->delete()
    		->from('service')
    		->where(array('ID_SERVICE' => $id));
    		$sql->prepareStatementForSqlObject($sQuery)->execute();
    		return 1;
    	}
    	return 0;
    	
    }
    
    
    public function getListeActes()
    {
    	$db = $this->tableGateway->getAdapter();
    	 
    	$aColumns = array('Designation','Tarif', 'Id');
    	 
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
    	->from(array('a' => 'actes'))->columns(array('Designation'=>'designation','Tarif'=>'tarif','Id'=>'id'))
    	->order('a.id DESC');
    	 
    	 
    	/* Data set length after filtering */
    	$stat = $sql->prepareStatementForSqlObject($sQuery);
    	$rResultFt = $stat->execute();
    	$iFilteredTotal = count($rResultFt);
    	 
    	$rResult = $rResultFt;
    	 
    	$output = array(
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
    			if ( $aColumns[$i] != ' ')
    			{
    				/* General output */
    				if ($aColumns[$i] == 'Id') {
    					$html  ="<a style='float: left; padding-right: 15%;' href='javascript:visualiserDetailsActe(".$aRow[ $aColumns[$i] ].")'>";
    					$html .="<img style='display: inline; ' src='".$tabURI[0]."public/images_icons/voir2.png' title='dÃ©tails'></a>";
    					 
    					$html  .="<a style='float: left; padding-right: 15%;' href='javascript:modifierActe(".$aRow[ $aColumns[$i] ].")'>";
    					$html .="<img style='display: inline;' src='".$tabURI[0]."public/images_icons/pencil_16.png' title='modifier'></a>";
    					 
    					$html  .="<a id='acte_".$aRow[ $aColumns[$i] ]."' style='float: left;' href='javascript:supprimerActe(".$aRow[ $aColumns[$i] ].")'>";
    					$html .="<img style='display: inline; width: 15px; height: 15px;' src='".$tabURI[0]."public/images_icons/sup2.png' title='supprimer'></a>";
    
    					$html .="<input id='".$aRow[ $aColumns[$i] ]."'   type='hidden' value='".$aRow[ 'Id' ]."'>";
    					 
    					$row[] = $html;
    				}
    				else
    				if ($aColumns[$i] == 'Tarif') {
    					$row[] = "<div style='width: 100%; height: 10px;'><span style='float: right;'>".$this->prixMill( $aRow[ $aColumns[$i] ] )."</span></div>";
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
    
    /**
     * Informations d'un acte
     */
    public function getInfosActe($id)
    {
    	$db = $this->tableGateway->getAdapter();
    	$sql = new Sql($db);
    	$sQuery = $sql->select()
    	->from(array('a' => 'actes'))->columns(array('*'))
    	->where(array('a.id' => $id));
    	$stat = $sql->prepareStatementForSqlObject($sQuery);
    	return $stat->execute()->current();
    }
    
    /**
     * Ajout d'un acte
     */
    public function addActe($designation, $tarif, $id_employe)
    {
    	$db = $this->tableGateway->getAdapter();
    	$sql = new Sql($db);
    	$sQuery = $sql->insert()
    	->into('actes')
    	->values(array(
    			'designation' => $designation,
    			'tarif' => $tarif,
    			'id_employe' => $id_employe,
    	));
    
    	$stat = $sql->prepareStatementForSqlObject($sQuery);
    	$stat->execute();
    }
    
    /**
     * Mis à jour d'un acte
     */
    public function updateActe($updateActe, $designation, $tarif, $id_employe)
    {
    	$today = (new \DateTime ())->format ( 'Y-m-d H:i:s' );
    	
    	$db = $this->tableGateway->getAdapter();
    	$sql = new Sql($db);
    	$sQuery = $sql->update()
    	->table('actes')
    	->set(array(
    			'designation' => $designation,
    			'tarif' => $tarif,
    			'date_modification' => $today,
    			'id_employe' => $id_employe,
    	))
    	->where(array('id' => $updateActe));
    
    	$stat = $sql->prepareStatementForSqlObject($sQuery);
    	$stat->execute();
    }
    
    public function existeDemandeActe($id) {
    	$db = $this->tableGateway->getAdapter();
    	$sql = new Sql($db);
    	$sQuery = $sql->select()
    	->from('demande_acte')
    	->where(array('idActe' => $id));
    	 
    	return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
    }
    
    public function supprimerActe($id) {
    	 
    	if(!$this->existeDemandeActe($id)){ //Cela veut dire cet acte n'existe pas dans les demandes 
    		$db = $this->tableGateway->getAdapter();
    		$sql = new Sql($db);
    		$sQuery = $sql->delete()
    		->from('actes')
    		->where(array('id' => $id));
    		$sql->prepareStatementForSqlObject($sQuery)->execute();
    		return 1;
    	}
    	return 0;
    	 
    }
}
