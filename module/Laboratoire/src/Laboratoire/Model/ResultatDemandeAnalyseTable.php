<?php

namespace Laboratoire\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;

class ResultatDemandeAnalyseTable {
	protected $tableGateway;
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}
	
	public function fetchAll() {
		$resultSet = $this->tableGateway->select ();
		return $resultSet;
	}
	
	public function getResultatDemandeAnalyse($iddemande) {
	    $rowset = $this->tableGateway->select ( array ( 'iddemande_analyse' => $iddemande ) );
	    $row =  $rowset->current ();
	    if (! $row) { $row = null; }
	    return $row;
	}
	
	//Ajouter pour le biologiste --- v�rification de la validation du biologiste
	//Ajouter pour le biologiste --- v�rification de la validation du biologiste
	//Ajouter pour le biologiste --- v�rification de la validation du biologiste
	public function getResultatDemandeAnalyseValider($iddemande) {
		$rowset = $this->tableGateway->select ( array ( 'iddemande_analyse' => $iddemande, 'valide' => 1 ) );
		$row =  $rowset->current ();
		if (! $row) { $row = null; }
		return $row;
	}
	
	//Ajouter pour le biologiste
	//Ajouter pour le biologiste
	public function validerResultDemande($iddemande, $idemploye){
		
		$data = array(
				'valide' => 1,
				'valider_par' => $idemploye,
		);
		 
		$this->tableGateway->update($data, array('iddemande_analyse' => $iddemande));
		
	}
	
	//Ajouter pour le biologiste
	//Ajouter pour le biologiste
	public function retirerValidationResultDemande($iddemande, $idemploye){
	
		$data = array(
				'valide' => 0,
				'retrait_validation' => $idemploye,
		);
			
		$this->tableGateway->update($data, array('iddemande_analyse' => $iddemande));
	
	}
	
	public function addResultatDemandeAnalyse($iddemande, $idemploye) {

	    if(! $this->getResultatDemandeAnalyse($iddemande)){
	        $data = array(
	            'iddemande_analyse' => $iddemande,
	            'date' => (new \DateTime() ) ->format('Y-m-d H:i:s'),
	            'idemploye' => $idemploye,
	        );
	        
	        $this->tableGateway->insert($data);
	    }
	    
	}
	
	//****************************************************************************************************
	//****************************************************************************************************
	//****************************************************************************************************
	//****************************************************************************************************
	/**
	 * Indiquer que le r�sultat de la demande est effectu�
	 */
	public function setResultDemandeEffectuee($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->update() ->table('demande_analyse') ->set( array('result' => 1) )
	    ->where(array('iddemande' => $iddemande ));
	    $sql->prepareStatementForSqlObject($sQuery)->execute();
	}
	
	/**
	 * Indiquer que le r�sultat de la demande n'est pas effectu�
	 */
	public function setResultDemandeNonEffectuee($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->update() ->table('demande_analyse') ->set( array('result' => 0) )
	    ->where(array('iddemande' => $iddemande ));
	    $sql->prepareStatementForSqlObject($sQuery)->execute();
	}
	
	
	public function getValeursNfs($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('vs' => 'valeurs_nfs'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	     
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursNfs($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donnees = array();
	    $donneesExiste = 0;
	    
	    //Si les resultats n y sont pas on les ajoute 
	    if(!$this->getValeursNfs($iddemande)){
	        $donnees['idresultat_demande_analyse'] = $iddemande;
	        
	        for($i = 1 ; $i < count($tab)-2 ; $i++){
	            if($tab[$i]){ $donnees['champ'.$i] = $tab[$i]; $donneesExiste = 1; }else{ $donnees['champ'.$i] = null; }
	        }
	        $donnees['type_materiel'] = $tab[$i];
	        $donnees['commentaire'] = $tab[$i+1];
	        
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->insert() ->into('valeurs_nfs') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	    } 
	    //Sinon on effectue des mises a jours
	    else {
	        for($i = 1 ; $i < count($tab)-2 ; $i++){
	            if($tab[$i]){ $donnees['champ'.$i] = $tab[$i]; $donneesExiste = 1; }else{ $donnees['champ'.$i] = null; }
	        }
	        $donnees['type_materiel'] = $tab[$i];
	        $donnees['commentaire'] = $tab[$i+1];
	        
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_nfs') ->set( $donnees )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	    }
	    
	    return $donneesExiste;
	}
	
	public function getValeursGsrhGroupage($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('vg' => 'valeurs_gsrh_groupage'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	     
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursGsrhGroupage($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	    
	    $donnees = array();
	    $donnees['groupe'] = $tab[1];
	    $donnees['rhesus'] = $tab[2];
	    $donnees['type_materiel'] = $tab[3];
	    
	    if($donnees['groupe'] || $donnees['rhesus']){ $donneesExiste = 1; }
	    
	    //Si les resultats n y sont pas on les ajoute
	    if(!$this->getValeursGsrhGroupage($iddemande)){
	        
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $donnees['idresultat_demande_analyse'] = $iddemande;
	            $sQuery = $sql->insert() ->into('valeurs_gsrh_groupage') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }

	    }
	    //Sinon on effectue des mises a jours
	    else {
	        
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_gsrh_groupage') ->set( $donnees )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	    }
	    
	    return $donneesExiste;
	}
	
	//****************************************************************************************************
	//****************************************************************************************************
	public function getValeursRechercheAntigene($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('va' => 'valeurs_antigene_d_faible'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursRechercheAntigene($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	    
	    $donnees = array();
	    $donnees['antigene_d_faible'] = $tab[1];
	    $donnees['type_materiel'] = $tab[2];
	    
	    if($donnees['antigene_d_faible']){ $donneesExiste = 1; }
	    
	    //Si les resultats n y sont pas on les ajoute
	    if(!$this->getValeursRechercheAntigene($iddemande)){
	        
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $donnees['idresultat_demande_analyse'] = $iddemande;
	            $sQuery = $sql->insert() ->into('valeurs_antigene_d_faible') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }

	    }
	    //Sinon on effectue des mises a jours
	    else {
	        
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_antigene_d_faible') ->set( $donnees )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }

	    }
	    
	    return $donneesExiste;
	}
	
	//****************************************************************************************************
	//****************************************************************************************************
	public function getValeursTestCombsDirect($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('va' => 'valeurs_test_combs_direct'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursTestCombsDirect($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	    
	    $donnees = array();
	    $donnees['valeur'] = $tab[1];
	    $donnees['titre']  = $tab[2];
	    $donnees['type_materiel']  = $tab[3];
	
	    if($donnees['valeur']){ $donneesExiste = 1; }
	    
	    //Si les resultats n y sont pas on les ajoute
	    if(!$this->getValeursTestCombsDirect($iddemande)){
	        
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $donnees['idresultat_demande_analyse'] = $iddemande;
	            $sQuery = $sql->insert() ->into('valeurs_test_combs_direct') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }

	    }
	    //Sinon on effectue des mises a jours
	    else {
	        
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_test_combs_direct') ->set( $donnees )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }

	    }
	    
	    return $donneesExiste;
	}
	
	//****************************************************************************************************
	//****************************************************************************************************
	public function getValeursTestCombsIndirect($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('va' => 'valeurs_test_combs_indirect'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursTestCombsIndirect($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	    
	    $donnees = array();
	    $donnees['valeur'] = $tab[1];
	    $donnees['titre']  = $tab[2];
	    $donnees['type_materiel']  = $tab[3];
	
	    if($donnees['valeur']){ $donneesExiste = 1; }
	    
	    //Si les resultats n y sont pas on les ajoute
	    if(!$this->getValeursTestCombsIndirect($iddemande)){
	        
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $donnees['idresultat_demande_analyse'] = $iddemande;
	            $sQuery = $sql->insert() ->into('valeurs_test_combs_indirect') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }

	    }
	    //Sinon on effectue des mises a jours
	    else {
	        
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_test_combs_indirect') ->set( $donnees )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }

	    }
	    
	    return $donneesExiste;
	}
	
	//****************************************************************************************************
	//****************************************************************************************************
	public function getValeursTestDemmel($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('va' => 'valeurs_test_d_emmel'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursTestDemmel($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	    
	    $donnees = array();
	    $donnees['valeur'] = $tab[1];
	    $donnees['type_materiel'] = $tab[2];
	
	    if($donnees['valeur']){ $donneesExiste = 1; }
	    
	    //Si les resultats n y sont pas on les ajoute
	    if(!$this->getValeursTestDemmel($iddemande)){
	        
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $donnees['idresultat_demande_analyse'] = $iddemande;
	            $sQuery = $sql->insert() ->into('valeurs_test_d_emmel') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }

	    }
	    //Sinon on effectue des mises a jours
	    else {
	        
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_test_d_emmel') ->set( $donnees )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }

	    }
	    
	    return $donneesExiste;
	}
	
	//****************************************************************************************************
	//****************************************************************************************************
	public function getValeursTestCompatibilite($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('va' => 'valeurs_test_compatibilite'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursTestCompatibilite($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	    
	    $donnees = array();
	    $donnees['valeur'] = $tab[1];
	    $donnees['poche'] = $tab[2];
	    $donnees['type_materiel'] = $tab[3];
	    
	    if($donnees['valeur']){ $donneesExiste = 1; }
	
	    //Si les resultats n y sont pas on les ajoute
	    if(!$this->getValeursTestCompatibilite($iddemande)){
	        
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $donnees['idresultat_demande_analyse'] = $iddemande;
	            $sQuery = $sql->insert() ->into('valeurs_test_compatibilite') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }

	    }
	    //Sinon on effectue des mises a jours
	    else {
	        
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_test_compatibilite') ->set( $donnees )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }

	    }
	    
	    return $donneesExiste;
	}
	
	//****************************************************************************************************
	//****************************************************************************************************
	public function getValeursVitesseSedimentation($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('va' => 'valeurs_vitesse_sedimentation'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursVitesseSedimentation($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	    
	    $donnees = array();
	    $donnees['valeur'] = $tab[1];
	    $donnees['type_materiel'] = $tab[2];
	    
	    if($donnees['valeur']){ $donneesExiste = 1; }
	
	    //Si les resultats n y sont pas on les ajoute
	    if(!$this->getValeursVitesseSedimentation($iddemande)){
	        
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $donnees['idresultat_demande_analyse'] = $iddemande;
	            $sQuery = $sql->insert() ->into('valeurs_vitesse_sedimentation') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }

	    }
	    //Sinon on effectue des mises a jours
	    else {
	        
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_vitesse_sedimentation') ->set( $donnees )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }

	    }
	    
	    return $donneesExiste;
	}
	
	//****************************************************************************************************
	//****************************************************************************************************
	public function getValeursTauxReticulocyte($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('va' => 'valeurs_taux_reticulocyte'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursTauxReticulocyte($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	    
	    $donnees = array();
	    $donnees['taux_reticulocyte'] = $tab[1];
	    
	    if($donnees['taux_reticulocyte']){ $donneesExiste = 1; $donnees['type_materiel'] = $tab[2];}
	
	    //Si les resultats n y sont pas on les ajoute
	    if(!$this->getValeursTauxReticulocyte($iddemande)){
	        
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $donnees['idresultat_demande_analyse'] = $iddemande;
	            $sQuery = $sql->insert() ->into('valeurs_taux_reticulocyte') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	        
	    }
	    //Sinon on effectue des mises a jours
	    else {
	        
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_taux_reticulocyte') ->set( $donnees )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }

	    }
	    
	    return $donneesExiste;
	}
	
	//****************************************************************************************************
	//****************************************************************************************************
	public function getValeursGoutteEpaisse($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('va' => 'valeurs_goutte_epaisse'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursGoutteEpaisse($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	     
	    $donnees = array();
	    $donnees['goutte_epaisse'] = $tab[1];
	    if($tab[2]){ $donnees['densite_parasitaire'] = $tab[2]; } else { $donnees['densite_parasitaire'] = null; }
	     
	    if( $donnees['goutte_epaisse'] ){ $donneesExiste = 1; $donnees['type_materiel'] = $tab[3]; }
	
	    //Si les resultats n y sont pas on les ajoute
	    if(!$this->getValeursGoutteEpaisse($iddemande)){
	         
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $donnees['idresultat_demande_analyse'] = $iddemande;
	            $sQuery = $sql->insert() ->into('valeurs_goutte_epaisse') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	         
	    }
	    //Sinon on effectue des mises a jours
	    else {
	         
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_goutte_epaisse') ->set( $donnees )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	     
	    return $donneesExiste;
	}
	
	
	//****************************************************************************************************
	//****************************************************************************************************
	public function getValeursTpInr($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('va' => 'valeurs_tp_inr'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursTpInr($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	
	    $donnees = array();
	    
	    if($tab[1]){ $donnees['temps_quick_temoin']        = $tab[1]; } else { $donnees['temps_quick_temoin']        = null; }
	    if($tab[2]){ $donnees['temps_quick_patient']       = $tab[2]; } else { $donnees['temps_quick_patient']       = null; }
	    if($tab[3]){ $donnees['taux_prothrombine_patient'] = $tab[3]; } else { $donnees['taux_prothrombine_patient'] = null; }
	    if($tab[4]){ $donnees['inr_patient']               = $tab[4]; } else { $donnees['inr_patient']               = null; }
	    $donnees['type_materiel'] = $tab[5];
	
	    if( $tab[1] || $tab[2] || $tab[3] || $tab[4] ){ $donneesExiste = 1; }
	
	    //Si les resultats n y sont pas on les ajoute
	    if(!$this->getValeursTpInr($iddemande)){
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $donnees['idresultat_demande_analyse'] = $iddemande;
	            $sQuery = $sql->insert() ->into('valeurs_tp_inr') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	    //Sinon on effectue des mises a jours
	    else {
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_tp_inr') ->set( $donnees )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	
	    return $donneesExiste;
	}
	
	
	//****************************************************************************************************
	//****************************************************************************************************
	public function getValeursTca($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('vt' => 'valeurs_tca'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursTca($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	
	    $donnees = array();
	     
	    if($tab[1]){ $donnees['tca_patient'] = $tab[1]; } else { $donnees['tca_patient'] = null; }
	    if($tab[2]){ $donnees['temoin_patient']   = $tab[2]; } else { $donnees['temoin_patient']   = null; }
	    $donnees['type_materiel'] = $tab[3];
	
	    if( $tab[1] || $tab[2] ){ $donneesExiste = 1; }
	
	    //Si les resultats n y sont pas on les ajoute
	    if(!$this->getValeursTca($iddemande)){
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $donnees['idresultat_demande_analyse'] = $iddemande;
	            $sQuery = $sql->insert() ->into('valeurs_tca') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	    //Sinon on effectue des mises a jours
	    else {
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_tca') ->set( $donnees )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	
	    return $donneesExiste;
	}
	
	
	//****************************************************************************************************
	//****************************************************************************************************
	public function getValeursFibrinemie($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('va' => 'valeurs_fibrinemie'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursFibrinemie($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	     
	    $donnees = array();
	    $donnees['fibrinemie'] = $tab[1];
	     
	    if($donnees['fibrinemie']){ $donneesExiste = 1;  $donnees['type_materiel'] = $tab[2];}
	
	    //Si les resultats n y sont pas on les ajoute
	    if(!$this->getValeursFibrinemie($iddemande)){
	         
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $donnees['idresultat_demande_analyse'] = $iddemande;
	            $sQuery = $sql->insert() ->into('valeurs_fibrinemie') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	    //Sinon on effectue des mises a jours
	    else {
	         
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_fibrinemie') ->set( $donnees )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	     
	    return $donneesExiste;
	}
	
	
	//****************************************************************************************************
	//****************************************************************************************************
	public function getValeursTempsSaignement($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('va' => 'valeurs_temps_saignement'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursTempsSaignement($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	
	    $donnees = array();
	    $donnees['temps_saignement'] = $tab[1];
	
	    if($donnees['temps_saignement']){ $donneesExiste = 1; $donnees['type_materiel'] = $tab[2];}
	
	    //Si les resultats n y sont pas on les ajoute
	    if(!$this->getValeursTempsSaignement($iddemande)){
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $donnees['idresultat_demande_analyse'] = $iddemande;
	            $sQuery = $sql->insert() ->into('valeurs_temps_saignement') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	    //Sinon on effectue des mises a jours
	    else {
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_temps_saignement') ->set( $donnees )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	
	    return $donneesExiste;
	}
	
	//****************************************************************************************************
	//****************************************************************************************************
	public function getValeursFacteur8($iddemande){
		$db = $this->tableGateway->getAdapter();
		$sql = new Sql($db);
		$sQuery = $sql->select()
		->from(array('vt' => 'valeurs_facteur_8'))->columns(array('*'))
		->where(array('idresultat_demande_analyse' => $iddemande));
	
		return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursFacteur8($tab, $iddemande){
		$db = $this->tableGateway->getAdapter();
		$sql = new Sql($db);
		$donneesExiste = 0;
	
		$donnees = array();
	
		if($tab[1]){ $donnees['facteur_8'] = $tab[1]; } else { $donnees['facteur_8'] = null; }
		if($tab[2]){ $donnees['type_materiel'] = $tab[2]; }else{ $donnees['type_materiel'] = null; }
	
		if( $tab[1] ){ $donneesExiste = 1; }
	
		//Si les resultats n y sont pas on les ajoute
		if(!$this->getValeursFacteur8($iddemande)){
	
			if($donneesExiste == 0){
				$this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
				$this->setResultDemandeNonEffectuee($iddemande);
			}else{
				$donnees['idresultat_demande_analyse'] = $iddemande;
				$sQuery = $sql->insert() ->into('valeurs_facteur_8') ->values( $donnees );
				$sql->prepareStatementForSqlObject($sQuery)->execute();
				$this->setResultDemandeEffectuee($iddemande);
			}
	
		}
		//Sinon on effectue des mises a jours
		else {
	
			if($donneesExiste == 0){
				$this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
				$this->setResultDemandeNonEffectuee($iddemande);
			}else{
				$sQuery = $sql->update() ->table('valeurs_facteur_8') ->set( $donnees )
				->where(array('idresultat_demande_analyse' => $iddemande ));
				$sql->prepareStatementForSqlObject($sQuery)->execute();
				$this->setResultDemandeEffectuee($iddemande);
			}
	
		}
	
		return $donneesExiste;
	}
	
	//****************************************************************************************************
	//****************************************************************************************************
	public function getValeursFacteur9($iddemande){
		$db = $this->tableGateway->getAdapter();
		$sql = new Sql($db);
		$sQuery = $sql->select()
		->from(array('vt' => 'valeurs_facteur_9'))->columns(array('*'))
		->where(array('idresultat_demande_analyse' => $iddemande));
	
		return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursFacteur9($tab, $iddemande){
		$db = $this->tableGateway->getAdapter();
		$sql = new Sql($db);
		$donneesExiste = 0;
	
		$donnees = array();
	
		if($tab[1]){ $donnees['facteur_9'] = $tab[1]; } else { $donnees['facteur_9'] = null; }
		if($tab[2]){ $donnees['type_materiel'] = $tab[2]; }else{ $donnees['type_materiel'] = null; }
	
		if( $tab[1] ){ $donneesExiste = 1; }
	
		//Si les resultats n y sont pas on les ajoute
		if(!$this->getValeursFacteur9($iddemande)){
	
			if($donneesExiste == 0){
				$this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
				$this->setResultDemandeNonEffectuee($iddemande);
			}else{
				$donnees['idresultat_demande_analyse'] = $iddemande;
				$sQuery = $sql->insert() ->into('valeurs_facteur_9') ->values( $donnees );
				$sql->prepareStatementForSqlObject($sQuery)->execute();
				$this->setResultDemandeEffectuee($iddemande);
			}
	
		}
		//Sinon on effectue des mises a jours
		else {
	
			if($donneesExiste == 0){
				$this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
				$this->setResultDemandeNonEffectuee($iddemande);
			}else{
				$sQuery = $sql->update() ->table('valeurs_facteur_9') ->set( $donnees )
				->where(array('idresultat_demande_analyse' => $iddemande ));
				$sql->prepareStatementForSqlObject($sQuery)->execute();
				$this->setResultDemandeEffectuee($iddemande);
			}
	
		}
	
		return $donneesExiste;
	}
	

	//****************************************************************************************************
	//****************************************************************************************************
	public function getValeursDDimeres($iddemande){
		$db = $this->tableGateway->getAdapter();
		$sql = new Sql($db);
		$sQuery = $sql->select()
		->from(array('vt' => 'valeurs_ddimeres'))->columns(array('*'))
		->where(array('idresultat_demande_analyse' => $iddemande));
	
		return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursDDimeres($tab, $iddemande){

		$db = $this->tableGateway->getAdapter();
		$sql = new Sql($db);
		$donneesExiste = 0;
		
		$donnees = array();
		
		if($tab[1]){ $donnees['d_dimeres'] = $tab[1]; } else { $donnees['d_dimeres'] = null; }
		if($tab[2]){ $donnees['type_materiel'] = $tab[2]; }else{ $donnees['type_materiel'] = null; }
		
		if( $tab[1] ){ $donneesExiste = 1; }
		
		//Si les resultats n y sont pas on les ajoute
		if(!$this->getValeursDDimeres($iddemande)){
		
			if($donneesExiste == 0){
				$this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
				$this->setResultDemandeNonEffectuee($iddemande);
			}else{
				$donnees['idresultat_demande_analyse'] = $iddemande;
				$sQuery = $sql->insert() ->into('valeurs_ddimeres') ->values( $donnees );
				$sql->prepareStatementForSqlObject($sQuery)->execute();
				$this->setResultDemandeEffectuee($iddemande);
			}
		
		}
		//Sinon on effectue des mises a jours
		else {
		
			if($donneesExiste == 0){
				$this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
				$this->setResultDemandeNonEffectuee($iddemande);
			}else{
				$sQuery = $sql->update() ->table('valeurs_ddimeres') ->set( $donnees )
				->where(array('idresultat_demande_analyse' => $iddemande ));
				$sql->prepareStatementForSqlObject($sQuery)->execute();
				$this->setResultDemandeEffectuee($iddemande);
			}
		
		}
		
		return $donneesExiste;
		
	}
	
	//****************************************************************************************************
	//****************************************************************************************************
	public function getValeursGlycemie($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('vt' => 'valeurs_glycemie'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursGlycemie($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	
	    $donnees = array();
	
	    if($tab[1]){ $donnees['glycemie_1'] = $tab[1]; } else { $donnees['glycemie_1'] = null; }
	    if($tab[2]){ $donnees['glycemie_2'] = $tab[2]; } else { $donnees['glycemie_2'] = null; }
	
	    if( $tab[1] || $tab[2] ){ $donneesExiste = 1; $donnees['type_materiel'] = $tab[3];}
	
	    //Si les resultats n y sont pas on les ajoute
	    if(!$this->getValeursGlycemie($iddemande)){
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $donnees['idresultat_demande_analyse'] = $iddemande;
	            $sQuery = $sql->insert() ->into('valeurs_glycemie') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	    //Sinon on effectue des mises a jours
	    else {
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_glycemie') ->set( $donnees )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	
	    return $donneesExiste;
	}
	
	
	//****************************************************************************************************
	//****************************************************************************************************
	public function getValeursCreatininemie($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('va' => 'valeurs_creatininemie'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursCreatininemie($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	
	    $donnees = array();
	    $donnees['creatininemie'] = $tab[1];
	
	    if($donnees['creatininemie']){ $donneesExiste = 1; $donnees['type_materiel'] = $tab[2];}
	
	    //Si les resultats n y sont pas on les ajoute
	    if(!$this->getValeursCreatininemie($iddemande)){
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $donnees['idresultat_demande_analyse'] = $iddemande;
	            $sQuery = $sql->insert() ->into('valeurs_creatininemie') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	    //Sinon on effectue des mises a jours
	    else {
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_creatininemie') ->set( $donnees )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	
	    return $donneesExiste;
	}
	
	

	//****************************************************************************************************
	//****************************************************************************************************
	public function getValeursAzotemie($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('va' => 'valeurs_azotemie'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursAzotemie($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	
	    $donnees = array();
	    $donnees['valeur'] = $tab[1];
	    $donnees['type_materiel'] = $tab[2];
	
	    if($donnees['valeur']){ $donneesExiste = 1; }
	
	    //Si les resultats n y sont pas on les ajoute
	    if(!$this->getValeursAzotemie($iddemande)){
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $donnees['idresultat_demande_analyse'] = $iddemande;
	            $sQuery = $sql->insert() ->into('valeurs_azotemie') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	    //Sinon on effectue des mises a jours
	    else {
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_azotemie') ->set( $donnees )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	
	    return $donneesExiste;
	}
	
	
	//****************************************************************************************************
	//****************************************************************************************************
	public function getValeursAcideUrique($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('va' => 'valeurs_acide_urique'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursAcideUrique($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	
	    $donnees = array();
	    $donnees['acide_urique'] = $tab[1];
	
	    if($donnees['acide_urique']){ $donneesExiste = 1;  $donnees['type_materiel'] = $tab[2];}
	
	    //Si les resultats n y sont pas on les ajoute
	    if(!$this->getValeursAcideUrique($iddemande)){
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $donnees['idresultat_demande_analyse'] = $iddemande;
	            $sQuery = $sql->insert() ->into('valeurs_acide_urique') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	    //Sinon on effectue des mises a jours
	    else {
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_acide_urique') ->set( $donnees )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	
	    return $donneesExiste;
	}
	
	//****************************************************************************************************
	//****************************************************************************************************
	public function getValeursCholesterolTotal($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('va' => 'valeurs_cholesterol_total'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursCholesterolTotal($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	
	    $donnees = array();
	    
	    if($tab[1]){ $donnees['cholesterol_total_1'] = $tab[1]; }else{ $donnees['cholesterol_total_1'] = null; }
	    if($tab[2]){ $donnees['cholesterol_total_2'] = $tab[2]; }else{ $donnees['cholesterol_total_2'] = null; }
	
	    if($donnees['cholesterol_total_1'] || $donnees['cholesterol_total_2']){ $donneesExiste = 1; $donnees['type_materiel'] = $tab[3];}
	
	    //Si les resultats n y sont pas on les ajoute
	    if(!$this->getValeursCholesterolTotal($iddemande)){
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $donnees['idresultat_demande_analyse'] = $iddemande;
	            $sQuery = $sql->insert() ->into('valeurs_cholesterol_total') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	    //Sinon on effectue des mises a jours
	    else {
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_cholesterol_total') ->set( $donnees )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	
	    return $donneesExiste;
	}
	
	//****************************************************************************************************
	//****************************************************************************************************
	public function getValeursTriglycerides($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('va' => 'valeurs_triglycerides'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursTriglycerides($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	
	    $donnees = array();
	     
	    if($tab[1]){ $donnees['triglycerides_1'] = $tab[1]; }else{ $donnees['triglycerides_1'] = null; }
	    if($tab[2]){ $donnees['triglycerides_2'] = $tab[2]; }else{ $donnees['triglycerides_2'] = null; }
	
	    if($donnees['triglycerides_1'] || $donnees['triglycerides_2']){ $donneesExiste = 1; $donnees['type_materiel'] = $tab[3];}
	
	    //Si les resultats n y sont pas on les ajoute
	    if(!$this->getValeursTriglycerides($iddemande)){
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $donnees['idresultat_demande_analyse'] = $iddemande;
	            $sQuery = $sql->insert() ->into('valeurs_triglycerides') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	    //Sinon on effectue des mises a jours
	    else {
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_triglycerides') ->set( $donnees )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	
	    return $donneesExiste;
	}
	
	//****************************************************************************************************
	//****************************************************************************************************
	public function getValeursCholesterolHDL($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('va' => 'valeurs_cholesterol_hdl'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursCholesterolHDL($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	
	    $donnees = array();
	
	    if($tab[1]){ $donnees['cholesterol_HDL_1'] = $tab[1]; }else{ $donnees['cholesterol_HDL_1'] = null; }
	    if($tab[2]){ $donnees['cholesterol_HDL_2'] = $tab[2]; }else{ $donnees['cholesterol_HDL_2'] = null; }
	
	    if($donnees['cholesterol_HDL_1'] || $donnees['cholesterol_HDL_2']){ $donneesExiste = 1;  $donnees['type_materiel'] = $tab[3];}
	
	    //Si les resultats n y sont pas on les ajoute
	    if(!$this->getValeursCholesterolHDL($iddemande)){
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $donnees['idresultat_demande_analyse'] = $iddemande;
	            $sQuery = $sql->insert() ->into('valeurs_cholesterol_hdl') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	    //Sinon on effectue des mises a jours
	    else {
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_cholesterol_hdl') ->set( $donnees )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	
	    return $donneesExiste;
	}
	
	
	//****************************************************************************************************
	//****************************************************************************************************
	public function getValeursCholesterolLDL($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('va' => 'valeurs_cholesterol_ldl'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursCholesterolLDL($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	
	    $donnees = array();
	
	    if($tab[1]){ $donnees['cholesterol_LDL_1'] = $tab[1]; }else{ $donnees['cholesterol_LDL_1'] = null; }
	    if($tab[2]){ $donnees['cholesterol_LDL_2'] = $tab[2]; }else{ $donnees['cholesterol_LDL_2'] = null; }
	
	    if($donnees['cholesterol_LDL_1'] || $donnees['cholesterol_LDL_2']){ $donneesExiste = 1;   $donnees['type_materiel'] = $tab[3];}
	
	    //Si les resultats n y sont pas on les ajoute
	    if(!$this->getValeursCholesterolLDL($iddemande)){
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $donnees['idresultat_demande_analyse'] = $iddemande;
	            $sQuery = $sql->insert() ->into('valeurs_cholesterol_ldl') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	    //Sinon on effectue des mises a jours
	    else {
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_cholesterol_ldl') ->set( $donnees )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	
	    return $donneesExiste;
	}
	
	
	//****************************************************************************************************
	//****************************************************************************************************
	public function addValeurs_Total_HDL_LDL_Triglycerides($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	    $cmpt = 0;
	
	    $donneesTotal = array();
	    $donneesHDL = array();
	    $donneesLDL = array();
	    $donneesTrigly = array();
	
	    if($tab[1]){ $donneesTotal['cholesterol_total_1'] = $tab[1]; }else{ $donneesTotal['cholesterol_total_1'] = null; $cmpt++;}
	    if($tab[2]){ $donneesTotal['cholesterol_total_2'] = $tab[2]; }else{ $donneesTotal['cholesterol_total_2'] = null; $cmpt++;}
	    
 	    if($tab[3]){ $donneesHDL['cholesterol_HDL_1'] = $tab[3]; }else{ $donneesHDL['cholesterol_HDL_1'] = null; $cmpt++;}
 	    if($tab[4]){ $donneesHDL['cholesterol_HDL_2'] = $tab[4]; }else{ $donneesHDL['cholesterol_HDL_2'] = null; $cmpt++;}
	    
 	    if($tab[5]){ $donneesLDL['cholesterol_LDL_1'] = $tab[5]; }else{ $donneesLDL['cholesterol_LDL_1'] = null; $cmpt++;}
 	    if($tab[6]){ $donneesLDL['cholesterol_LDL_2'] = $tab[6]; }else{ $donneesLDL['cholesterol_LDL_2'] = null; $cmpt++;}
	    
 	    if($tab[7]){ $donneesTrigly['triglycerides_1'] = $tab[7]; }else{ $donneesTrigly['triglycerides_1'] = null; $cmpt++;}
 	    if($tab[8]){ $donneesTrigly['triglycerides_2'] = $tab[8]; }else{ $donneesTrigly['triglycerides_2'] = null; $cmpt++;}
	
	    if($cmpt != 8){
	        $donneesExiste = 1; 
	        
	        /*** CHOLESTEROL TOTAL ----- CHOLESTEROL TOTAL ----- CHOLESTEROL TOTAL ***/
	        /*** CHOLESTEROL TOTAL ----- CHOLESTEROL TOTAL ----- CHOLESTEROL TOTAL ***/
	        if(!$this->getValeursCholesterolTotal($iddemande)){
	            if( $tab[1] || $tab[2] ){
	            	$donneesTotal['type_materiel'] = $tab[9];
	                $donneesTotal['idresultat_demande_analyse'] = $iddemande;
	                $sQuery = $sql->insert() ->into('valeurs_cholesterol_total') ->values( $donneesTotal );
	                $sql->prepareStatementForSqlObject($sQuery)->execute();
	                $this->setResultDemandeEffectuee($iddemande);
	            }
	        }else{
	            if( $tab[1] || $tab[2] ){
	            	$donneesTotal['type_materiel'] = $tab[9];
	                $sQuery = $sql->update() ->table('valeurs_cholesterol_total') ->set( $donneesTotal )
	                ->where(array('idresultat_demande_analyse' => $iddemande ));
	                $sql->prepareStatementForSqlObject($sQuery)->execute();
	                $this->setResultDemandeEffectuee($iddemande);
	            }else 
	                if( !$tab[1] && !$tab[2] ){
	                    $sQuery = $sql->delete() ->from('valeurs_cholesterol_total')
	                    ->where(array('idresultat_demande_analyse' => $iddemande));
	                    $sql->prepareStatementForSqlObject($sQuery)->execute();
	                    $this->setResultDemandeNonEffectuee($iddemande);
	                }
	        }
	    
	        /*** CHOLESTEROL HDL ----- CHOLESTEROL HDL ----- CHOLESTEROL HDL ***/
	        /*** CHOLESTEROL HDL ----- CHOLESTEROL HDL ----- CHOLESTEROL HDL ***/
	        if(!$this->getValeursCholesterolHDL($iddemande)){
	            if( $tab[3] || $tab[4] ){
	            	$donneesHDL['type_materiel'] = $tab[10];
	                $donneesHDL['idresultat_demande_analyse'] = $iddemande;
	                $sQuery = $sql->insert() ->into('valeurs_cholesterol_hdl') ->values( $donneesHDL );
	                $sql->prepareStatementForSqlObject($sQuery)->execute();
	                $this->setResultDemandeEffectuee($iddemande);
	            }
	        }else{
	            if( $tab[3] || $tab[4] ){
	            	$donneesHDL['type_materiel'] = $tab[10];
	                $sQuery = $sql->update() ->table('valeurs_cholesterol_hdl') ->set( $donneesHDL )
	                ->where(array('idresultat_demande_analyse' => $iddemande ));
	                $sql->prepareStatementForSqlObject($sQuery)->execute();
	                $this->setResultDemandeEffectuee($iddemande);
	            }else
	                if( !$tab[3] && !$tab[4] ){
	                    $sQuery = $sql->delete() ->from('valeurs_cholesterol_hdl')
	                    ->where(array('idresultat_demande_analyse' => $iddemande));
	                    $sql->prepareStatementForSqlObject($sQuery)->execute();
	                    $this->setResultDemandeNonEffectuee($iddemande);
	            }
	        }
	        
	        /*** CHOLESTEROL LDL ----- CHOLESTEROL LDL ----- CHOLESTEROL LDL ***/
	        /*** CHOLESTEROL LDL ----- CHOLESTEROL LDL ----- CHOLESTEROL LDL ***/
	        if(!$this->getValeursCholesterolLDL($iddemande)){
	            if( $tab[5] || $tab[6] ){
	            	$donneesLDL['type_materiel'] = $tab[11];
	                $donneesLDL['idresultat_demande_analyse'] = $iddemande;
	                $sQuery = $sql->insert() ->into('valeurs_cholesterol_ldl') ->values( $donneesLDL );
	                $sql->prepareStatementForSqlObject($sQuery)->execute();
	                $this->setResultDemandeEffectuee($iddemande);
	            }
	        }else{
	            if( $tab[5] || $tab[6] ){
	            	$donneesLDL['type_materiel'] = $tab[11];
	                $sQuery = $sql->update() ->table('valeurs_cholesterol_ldl') ->set( $donneesLDL )
	                ->where(array('idresultat_demande_analyse' => $iddemande ));
	                $sql->prepareStatementForSqlObject($sQuery)->execute();
	                $this->setResultDemandeEffectuee($iddemande);
	            }else
	                if( !$tab[5] && !$tab[6] ){
	                    $sQuery = $sql->delete() ->from('valeurs_cholesterol_ldl')
	                    ->where(array('idresultat_demande_analyse' => $iddemande));
	                    $sql->prepareStatementForSqlObject($sQuery)->execute();
	                    $this->setResultDemandeNonEffectuee($iddemande);
	            }
	        }
	        
	        /*** TRIGLYCERIDES ----- TRIGLYCERIDES ----- TRIGLYCERIDES ***/
	        /*** TRIGLYCERIDES ----- TRIGLYCERIDES ----- TRIGLYCERIDES ***/
	        if(!$this->getValeursTriglycerides($iddemande)){
	            if( $tab[7] || $tab[8] ){
	            	$donneesTrigly['type_materiel'] = $tab[12];
	                $donneesTrigly['idresultat_demande_analyse'] = $iddemande;
	                $sQuery = $sql->insert() ->into('valeurs_triglycerides') ->values( $donneesTrigly );
	                $sql->prepareStatementForSqlObject($sQuery)->execute();
	                $this->setResultDemandeEffectuee($iddemande);
	            }
	        }else{
	            if( $tab[7] || $tab[8] ){
	            	$donneesTrigly['type_materiel'] = $tab[12];
	                $sQuery = $sql->update() ->table('valeurs_triglycerides') ->set( $donneesTrigly )
	                ->where(array('idresultat_demande_analyse' => $iddemande ));
	                $sql->prepareStatementForSqlObject($sQuery)->execute();
	                $this->setResultDemandeEffectuee($iddemande);
	            }else
	                if( !$tab[7] && !$tab[8] ){
	                    $sQuery = $sql->delete() ->from('valeurs_triglycerides')
	                    ->where(array('idresultat_demande_analyse' => $iddemande));
	                    $sql->prepareStatementForSqlObject($sQuery)->execute();
	                    $this->setResultDemandeNonEffectuee($iddemande);
	            }
	        }
	    
	        
	        
	    }else{
	        $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) ); 
	    }
	
	    return $donneesExiste;
	}
	
	//****************************************************************************************************
	//****************************************************************************************************
	public function getValeursLipidesTotaux($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('va' => 'valeurs_lipides_totaux'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursLipidesTotaux($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	
	    $donnees = array();
	
	    if($tab[1]){ $donnees['type_materiel'] = $tab[1]; }else{ $donnees['type_materiel'] = null; }
	    if($tab[2]){ $donnees['lipides_totaux'] = $tab[2]; $donneesExiste = 1; }else{ $donnees['lipides_totaux'] = null; }
	
	    //Si les resultats n y sont pas on les ajoute
	    if(!$this->getValeursLipidesTotaux($iddemande)){
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $donnees['idresultat_demande_analyse'] = $iddemande;
	            $sQuery = $sql->insert() ->into('valeurs_lipides_totaux') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	    //Sinon on effectue des mises a jours
	    else {
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_lipides_totaux') ->set( $donnees )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	
	    return $donneesExiste;
	}
	
	//****************************************************************************************************
	//****************************************************************************************************
	public function getValeursIonogramme($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('va' => 'valeurs_ionogramme'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursIonogramme($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	
	    $donnees = array();
	
	    if($tab[1]){ $donnees['sodium_sanguin']    = $tab[1]; }else{ $donnees['sodium_sanguin']    = null; }
	    if($tab[2]){ $donnees['potassium_sanguin'] = $tab[2]; }else{ $donnees['potassium_sanguin'] = null; }
	    if($tab[3]){ $donnees['chlore_sanguin']    = $tab[3]; }else{ $donnees['chlore_sanguin']    = null; }
	
	    if($donnees['sodium_sanguin'] || $donnees['potassium_sanguin'] || $donnees['chlore_sanguin']){ $donneesExiste = 1;  $donnees['type_materiel'] = $tab[4]; }
	
	    //Si les resultats n y sont pas on les ajoute
	    if(!$this->getValeursIonogramme($iddemande)){
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $donnees['idresultat_demande_analyse'] = $iddemande;
	            $sQuery = $sql->insert() ->into('valeurs_ionogramme') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	    //Sinon on effectue des mises a jours
	    else {
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_ionogramme') ->set( $donnees )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	
	    return $donneesExiste;
	}
	
	//****************************************************************************************************
	//****************************************************************************************************
	public function getValeursCalcemie($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('va' => 'valeurs_calcemie'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursCalcemie($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	     
	    $donnees = array();
	    $donnees['calcemie'] = $tab[1];
	
	    if($donnees['calcemie']){ $donneesExiste = 1; $donnees['type_materiel'] = $tab[2];}
	     
	    //Si les resultats n y sont pas on les ajoute
	    if(!$this->getValeursCalcemie($iddemande)){
	         
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $donnees['idresultat_demande_analyse'] = $iddemande;
	            $sQuery = $sql->insert() ->into('valeurs_calcemie') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	    //Sinon on effectue des mises a jours
	    else {
	         
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_calcemie') ->set( $donnees )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	     
	    return $donneesExiste;
	}
	
	
	//****************************************************************************************************
	//****************************************************************************************************
	public function getValeursMagnesemie($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('va' => 'valeurs_magnesemie'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursMagnesemie($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	
	    $donnees = array();
	    $donnees['magnesemie'] = $tab[1];
	
	    if($donnees['magnesemie']){ $donneesExiste = 1; $donnees['type_materiel'] = $tab[2];}
	
	    //Si les resultats n y sont pas on les ajoute
	    if(!$this->getValeursMagnesemie($iddemande)){
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $donnees['idresultat_demande_analyse'] = $iddemande;
	            $sQuery = $sql->insert() ->into('valeurs_magnesemie') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	    //Sinon on effectue des mises a jours
	    else {
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_magnesemie') ->set( $donnees )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	
	    return $donneesExiste;
	}
	
	//****************************************************************************************************
	//****************************************************************************************************
	public function getValeursPhosphoremie($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('va' => 'valeurs_phosphoremie'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursPhosphoremie($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	
	    $donnees = array();
	    $donnees['phosphoremie'] = $tab[1];
	
	    if($donnees['phosphoremie']){ $donneesExiste = 1; $donnees['type_materiel'] = $tab[2];}
	
	    //Si les resultats n y sont pas on les ajoute
	    if(!$this->getValeursPhosphoremie($iddemande)){
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $donnees['idresultat_demande_analyse'] = $iddemande;
	            $sQuery = $sql->insert() ->into('valeurs_phosphoremie') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	    //Sinon on effectue des mises a jours
	    else {
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_phosphoremie') ->set( $donnees )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	
	    return $donneesExiste;
	}
	
	//****************************************************************************************************
	//****************************************************************************************************
	public function getValeursTgoAsat($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('va' => 'valeurs_tgo_asat'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursTgoAsat($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	
	    $donneesTgoAsat = array();
	
	    if($tab[1]){ $donneesTgoAsat['type_materiel'] = $tab[1]; }else{ $donneesTgoAsat['type_materiel'] = null; }
	    if($tab[2]){ $donneesTgoAsat['tgo_asat']      = $tab[2]; }else{ $donneesTgoAsat['tgo_asat']      = null; }
	    
	
	    if($tab[2]){ $donneesExiste = 1; }
	
	    //Si les resultats n y sont pas on les ajoute
	    if(!$this->getValeursTgoAsat($iddemande)){
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $donneesTgoAsat['idresultat_demande_analyse'] = $iddemande;
	            $sQuery = $sql->insert() ->into('valeurs_tgo_asat') ->values( $donneesTgoAsat );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	    //Sinon on effectue des mises a jours
	    else {
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_tgo_asat') ->set( $donneesTgoAsat )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	
	    return $donneesExiste;
	}
	
	//****************************************************************************************************
	//****************************************************************************************************
	public function getValeursTgpAlat($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('va' => 'valeurs_tgp_alat'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursTgpAlat($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	
	    $donneesTgpAlat = array();
	
	    if($tab[1]){ $donneesTgpAlat['type_materiel'] = $tab[1]; }else{ $donneesTgpAlat['type_materiel'] = null; }
	    if($tab[2]){ $donneesTgpAlat['tgp_alat']      = $tab[2]; }else{ $donneesTgpAlat['tgp_alat']      = null; }
	    
	    
	    if($tab[2]){ $donneesExiste = 1; }
	
	    //Si les resultats n y sont pas on les ajoute
	    if(!$this->getValeursTgpAlat($iddemande)){
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $donneesTgpAlat['idresultat_demande_analyse'] = $iddemande;
	            $sQuery = $sql->insert() ->into('valeurs_tgp_alat') ->values( $donneesTgpAlat );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	    //Sinon on effectue des mises a jours
	    else {
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_tgp_alat') ->set( $donneesTgpAlat )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	
	    return $donneesExiste;
	}
	
	
	//****************************************************************************************************
	//****************************************************************************************************
	public function addValeursAsatAlat($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	    $cmpt = 0;
	
	    $donneesTgpAlat = array();
	    $donneesTgoAsat = array();
	
	    if($tab[1]){ $donneesTgpAlat['type_materiel'] = $tab[1]; }else{ $donneesTgpAlat['type_materiel'] = null; $cmpt++;}
	    if($tab[2]){ $donneesTgpAlat['tgp_alat']      = $tab[2]; }else{ $donneesTgpAlat['tgp_alat']      = null; $cmpt++;}
	     
	    if($tab[3]){ $donneesTgoAsat['type_materiel'] = $tab[3]; }else{ $donneesTgoAsat['type_materiel'] = null; $cmpt++;}
	    if($tab[4]){ $donneesTgoAsat['tgo_asat']      = $tab[4]; }else{ $donneesTgoAsat['tgo_asat']      = null; $cmpt++;}
	     
	    if($cmpt != 4){
	         
	        /*** TGP ALAT ----- TGP ALAT ----- TGP ALAT ***/
	        /*** TGP ALAT ----- TGP ALAT ----- TGP ALAT ***/
	        if(!$this->getValeursTgpAlat($iddemande)){
	            if( $tab[2] ){
	                $donneesTgpAlat['idresultat_demande_analyse'] = $iddemande;
	                $sQuery = $sql->insert() ->into('valeurs_tgp_alat') ->values( $donneesTgpAlat );
	                $sql->prepareStatementForSqlObject($sQuery)->execute();
	                $this->setResultDemandeEffectuee($iddemande);
	                $donneesExiste = 1;
	            }
	        }else{
	            if( $tab[2] ){
	                $sQuery = $sql->update() ->table('valeurs_tgp_alat') ->set( $donneesTgpAlat )
	                ->where(array('idresultat_demande_analyse' => $iddemande ));
	                $sql->prepareStatementForSqlObject($sQuery)->execute();
	                $this->setResultDemandeEffectuee($iddemande);
	                $donneesExiste = 1;
	            }else
	            if( !$tab[2] ){
	                $sQuery = $sql->delete() ->from('valeurs_tgp_alat')
	                ->where(array('idresultat_demande_analyse' => $iddemande));
	                $sql->prepareStatementForSqlObject($sQuery)->execute();
	                $this->setResultDemandeNonEffectuee($iddemande);
	                
	            }
	        }
	         
	        /*** TGO ASAT ----- TGO ASAT ----- TGO ASAT ***/
	        /*** TGO ASAT ----- TGO ASAT ----- TGO ASAT ***/
	        if(!$this->getValeursTgoAsat($iddemande)){
	            if( $tab[4] ){
	                $donneesTgoAsat['idresultat_demande_analyse'] = $iddemande;
	                $sQuery = $sql->insert() ->into('valeurs_tgo_asat') ->values( $donneesTgoAsat );
	                $sql->prepareStatementForSqlObject($sQuery)->execute();
	                $this->setResultDemandeEffectuee($iddemande);
	                $donneesExiste = 1;
	            }
	        }else{
	            if( $tab[4] ){
	                $sQuery = $sql->update() ->table('valeurs_tgo_asat') ->set( $donneesTgoAsat )
	                ->where(array('idresultat_demande_analyse' => $iddemande ));
	                $sql->prepareStatementForSqlObject($sQuery)->execute();
	                $this->setResultDemandeEffectuee($iddemande);
	                $donneesExiste = 1;
	            }else
	            if( !$tab[4] ){
	                $sQuery = $sql->delete() ->from('valeurs_tgo_asat')
	                ->where(array('idresultat_demande_analyse' => $iddemande));
	                $sql->prepareStatementForSqlObject($sQuery)->execute();
	                $this->setResultDemandeNonEffectuee($iddemande);
	            }
	        }
	         
	    }else{
	        $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	    }
	
	    return $donneesExiste;
	}
	
	//****************************************************************************************************
	//****************************************************************************************************
	public function getValeursPhosphatageAlcaline($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('va' => 'valeurs_phosphatage_alcaline'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursPhosphatageAlcaline($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	
	    $donnees = array();
	    $donnees['valeur'] = $tab[1];
	    $donnees['type_materiel'] = $tab[2];
	
	    if($donnees['valeur']){ $donneesExiste = 1; }
	
	    //Si les resultats n y sont pas on les ajoute
	    if(!$this->getValeursPhosphatageAlcaline($iddemande)){
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $donnees['idresultat_demande_analyse'] = $iddemande;
	            $sQuery = $sql->insert() ->into('valeurs_phosphatage_alcaline') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	    //Sinon on effectue des mises a jours
	    else {
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_phosphatage_alcaline') ->set( $donnees )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	
	    return $donneesExiste;
	}
	
	//****************************************************************************************************
	//****************************************************************************************************
	public function getValeursGamaGtYgt($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('va' => 'valeurs_gama_gt'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursGamaGtYgt($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	
	    $donnees = array();
	    $donnees['valeur'] = $tab[1];
	    $donnees['type_materiel'] = $tab[2];
	
	    if($donnees['valeur']){ $donneesExiste = 1; }
	
	    //Si les resultats n y sont pas on les ajoute
	    if(!$this->getValeursGamaGtYgt($iddemande)){
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $donnees['idresultat_demande_analyse'] = $iddemande;
	            $sQuery = $sql->insert() ->into('valeurs_gama_gt') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	    //Sinon on effectue des mises a jours
	    else {
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_gama_gt') ->set( $donnees )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	
	    return $donneesExiste;
	}
	
	//****************************************************************************************************
	//****************************************************************************************************
	public function getValeursFerSerique($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('va' => 'valeurs_fer_serique'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursFerSerique($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	
	    $donnees = array();
	    
	    if($tab[1]){ $donnees['valeur_ug'] = $tab[1]; }else{ $donnees['valeur_ug'] = null; }
	    if($tab[2]){ $donnees['valeur_umol'] = $tab[2]; }else{ $donnees['valeur_umol'] = null; }
	
	    if($tab[1] || $tab[2]){ $donneesExiste = 1; $donnees['type_materiel'] = $tab[3]; }
	
	    //Si les resultats n y sont pas on les ajoute
	    if(!$this->getValeursFerSerique($iddemande)){
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $donnees['idresultat_demande_analyse'] = $iddemande;
	            $sQuery = $sql->insert() ->into('valeurs_fer_serique') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	    //Sinon on effectue des mises a jours
	    else {
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_fer_serique') ->set( $donnees )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	
	    return $donneesExiste;
	}
	
	//****************************************************************************************************
	//****************************************************************************************************
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
	
	public function addTypagePatientDepister($idpatient, $typage, $typepatient){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    
	    $sQuery = $sql->update() ->table('depistage') ->set( array('typage' => $typage, 'typepatient' => $typepatient ) ) 
	    ->where(array('idpatient' => $idpatient));
	    $sql->prepareStatementForSqlObject($sQuery)->execute();
	}
	
	public function getValeursTypageHemoglobine($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('va' => 'valeurs_typage_hemoglobine'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function getValeursTypageHemoglobineLibelle($iddemande){
		$db = $this->tableGateway->getAdapter();
		$sql = new Sql($db);
		$sQuery = $sql->select()
		->from(array('va' => 'valeurs_typage_hemoglobine'))->columns(array('*'))
		->join(array('th' => 'typage_hemoglobine') ,'th.idtypage = va.valeur', array('Designation_stat' => 'designation_stat'))
		->where(array('idresultat_demande_analyse' => $iddemande));
	
		return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursTypageHemoglobine($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    
	    $typesPathologiques = $this->getTypageHemoglobineParType(1);
	    
	    $typepatient = 0;
	    
	    $donneesExiste = 0;
	     
	    $donnees = array();
	    
	    if($tab[1]){ $donnees['type_materiel'] = $tab[1]; }else{ $donnees['type_materiel'] = null; }
	    if($tab[2]){ $donnees['valeur'] = $tab[2]; }else{ $donnees['valeur'] = null; }
	    if($tab[3]){ $donnees['valeur_Hbarts'] = $tab[3]; }else{ $donnees['valeur_Hbarts'] = null; }
	    
	    if($tab[2]){
	        $donneesExiste = 1; 
	        if(in_array($tab[2], $typesPathologiques)){ $typepatient = 1; }
	    }
	     
	    //Si les resultats n y sont pas on les ajoute
	    if(!$this->getValeursTypageHemoglobine($iddemande)){
	        $demande = $this->getDemandeAnalysesAvecIddemande($iddemande);
	         
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	            
	            //Ajout des r�sultats dans la table d�pistage lorsqu'il s'agit d'un patient d�pist�
	            $this->addTypagePatientDepister($demande['idpatient'], null, $typepatient);
	        }else{
	            $donnees['idresultat_demande_analyse'] = $iddemande;
	            $sQuery = $sql->insert() ->into('valeurs_typage_hemoglobine') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	            
	            //Ajout des r�sultats dans la table d�pistage lorsqu'il s'agit d'un patient d�pist�
	            $this->addTypagePatientDepister($demande['idpatient'], $tab[2], $typepatient);
	        }
	
	    }
	    //Sinon on effectue des mises a jours
	    else {
	        $demande = $this->getDemandeAnalysesAvecIddemande($iddemande);
	         
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	            
	            //Ajout des r�sultats dans la table d�pistage lorsqu'il s'agit d'un patient d�pist�
	            $this->addTypagePatientDepister($demande['idpatient'], null, $typepatient);
	            
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_typage_hemoglobine') ->set( $donnees )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	            
	            //Ajout des r�sultats dans la table d�pistage lorsqu'il s'agit d'un patient d�pist�
	            $this->addTypagePatientDepister($demande['idpatient'], $tab[2], $typepatient);
	        }
	
	    }
	     
	    return $donneesExiste;
	}
	
	//****************************************************************************************************
	//****************************************************************************************************
	public function getValeursFerritinine($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('va' => 'valeurs_ferritinine'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursFerritinine($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	
	    $donnees = array();
	
	    if($tab[1]){ $donnees['type_materiel'] = $tab[1]; }else{ $donnees['type_materiel'] = null; }
	    if($tab[2]){ $donnees['ferritinine']   = $tab[2]; }else{ $donnees['ferritinine']   = null; }
	     
	
	    if($tab[2]){ $donneesExiste = 1; }
	
	    //Si les resultats n y sont pas on les ajoute
	    if(!$this->getValeursFerritinine($iddemande)){
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $donnees['idresultat_demande_analyse'] = $iddemande;
	            $sQuery = $sql->insert() ->into('valeurs_ferritinine') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	    //Sinon on effectue des mises a jours
	    else {
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_ferritinine') ->set( $donnees )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	
	    return $donneesExiste;
	}
	

	//****************************************************************************************************
	//****************************************************************************************************
	public function getValeursBilirubineTotaleDirecte($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('va' => 'valeurs_bilirubine_totale_directe'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursBilirubineTotaleDirecte($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	
	    $donnees = array();
	
	    if($tab[1]){ $donnees['type_materiel']          = $tab[1]; }else{ $donnees['type_materiel']          = null; }
	    if($tab[2]){ $donnees['bilirubine_totale_mg']   = $tab[2]; }else{ $donnees['bilirubine_totale_mg']   = null; }
	    if($tab[3]){ $donnees['bilirubine_totale_umol'] = $tab[3]; }else{ $donnees['bilirubine_totale_umol'] = null; }
	    if($tab[4]){ $donnees['bilirubine_directe']     = $tab[4]; }else{ $donnees['bilirubine_directe']     = null; }
	
	
	    if($tab[2] || $tab[3] || $tab[4]){ $donneesExiste = 1; }
	
	    //Si les resultats n y sont pas on les ajoute
	    if(!$this->getValeursBilirubineTotaleDirecte($iddemande)){
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $donnees['idresultat_demande_analyse'] = $iddemande;
	            $sQuery = $sql->insert() ->into('valeurs_bilirubine_totale_directe') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	    //Sinon on effectue des mises a jours
	    else {
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_bilirubine_totale_directe') ->set( $donnees )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	
	    return $donneesExiste;
	}
	
	//****************************************************************************************************
	//****************************************************************************************************
	public function getValeursHemoglobineGlyqueeHBAC($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('va' => 'valeurs_hemoglobine_glyquee_hbac'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursHemoglobineGlyqueeHBAC($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	
	    $donnees = array();
	
	    if($tab[1]){ $donnees['type_materiel'] = $tab[1]; }else{ $donnees['type_materiel'] = null; }
	    if($tab[2]){ $donnees['hemoglobine_glyquee_hbac'] = $tab[2]; }else{ $donnees['hemoglobine_glyquee_hbac'] = null; }
	    if($tab[3]){ $donnees['hemoglobine_glyquee_hbac_mmol'] = $tab[3]; }else{ $donnees['hemoglobine_glyquee_hbac_mmol'] = null; }
	
	    if($tab[2] || $tab[3]){ $donneesExiste = 1; }
	
	    //Si les resultats n y sont pas on les ajoute
	    if(!$this->getValeursHemoglobineGlyqueeHBAC($iddemande)){
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $donnees['idresultat_demande_analyse'] = $iddemande;
	            $sQuery = $sql->insert() ->into('valeurs_hemoglobine_glyquee_hbac') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	    //Sinon on effectue des mises a jours
	    else {
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_hemoglobine_glyquee_hbac') ->set( $donnees )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	
	    return $donneesExiste;
	}
	
	//****************************************************************************************************
	//****************************************************************************************************
	public function getValeursElectrophoreseHemoglobine($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('va' => 'valeurs_electrophorese_hemoglobine'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	    $result = $sql->prepareStatementForSqlObject($sQuery)->execute();
	    
	    $donnees = array();
	    foreach ($result as $res){
	        $donnees[] = $res;
	    }
	    
	    return $donnees;
	}
	
	public function addValeursElectrophoreseHemoglobine($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	
	    $donnees = array();
	    
	    //Suppression pr�alable des donn�es
	    $sQuery = $sql->delete() ->from('valeurs_electrophorese_hemoglobine')
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	    $sql->prepareStatementForSqlObject($sQuery)->execute();
	    $this->setResultDemandeNonEffectuee($iddemande);
	    
	    if(count($tab) == 3){
	        $donnees['type_materiel'] = $tab[0];
	         
	        for($i = 1 ; $i < count($tab[1]) ; $i++){
	            if($tab[1][$i]){
	                $donnees['libelle'] = $tab[1][$i];
	                $donnees['valeur']  = $tab[2][$i];
	                 
	                $donnees['idresultat_demande_analyse'] = $iddemande;
	                $sQuery = $sql->insert() ->into('valeurs_electrophorese_hemoglobine') ->values( $donnees );
	                $sql->prepareStatementForSqlObject($sQuery)->execute();
	                $this->setResultDemandeEffectuee($iddemande);
	        
	                $donneesExiste = 1;
	            }
	        }
	    }
	    
	    if($donneesExiste == 0){
	        $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	        $this->setResultDemandeNonEffectuee($iddemande);
	    }
	    
	    return $donneesExiste;
	}
	
	//****************************************************************************************************
	//****************************************************************************************************
	public function getValeursElectrophoreseProteines($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('va' => 'valeurs_electrophorese_proteine'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursElectrophoreseProteines($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	    $k = 0;
	
	    $donnees = array();
	
	    if($tab[1]){  $donnees['type_materiel']  = $tab[1];        }else{ $donnees['type_materiel']  = null; }
	    if($tab[2]){  $donnees['albumine']       = $tab[2];  $k++; }else{ $donnees['albumine']       = null; }
	    if($tab[3]){  $donnees['albumine_abs']   = $tab[3];  $k++; }else{ $donnees['albumine_abs']   = null; }
	    if($tab[4]){  $donnees['alpha_1']        = $tab[4];  $k++; }else{ $donnees['alpha_1']        = null; }
	    if($tab[5]){  $donnees['alpha_1_abs']    = $tab[5];  $k++; }else{ $donnees['alpha_1_abs']    = null; }
	    if($tab[6]){  $donnees['alpha_2']        = $tab[6];  $k++; }else{ $donnees['alpha_2']        = null; }
	    if($tab[7]){  $donnees['alpha_2_abs']    = $tab[7];  $k++; }else{ $donnees['alpha_2_abs']    = null; }
	    if($tab[8]){  $donnees['beta_1']         = $tab[8];  $k++; }else{ $donnees['beta_1']         = null; }
	    if($tab[9]){  $donnees['beta_1_abs']     = $tab[9];  $k++; }else{ $donnees['beta_1_abs']     = null; }
	    if($tab[10]){ $donnees['beta_2']         = $tab[10]; $k++; }else{ $donnees['beta_2']         = null; }
	    if($tab[11]){ $donnees['beta_2_abs']     = $tab[11]; $k++; }else{ $donnees['beta_2_abs']     = null; }
	    if($tab[12]){ $donnees['gamma']          = $tab[12]; $k++; }else{ $donnees['gamma']          = null; }
	    if($tab[13]){ $donnees['gamma_abs']      = $tab[13]; $k++; }else{ $donnees['gamma_abs']      = null; }
	    if($tab[14]){ $donnees['proteine_totale']= $tab[14]; $k++; }else{ $donnees['proteine_totale']= null; }
 	    if($tab[15]){ $donnees['commentaire']    = $tab[15];       }else{ $donnees['commentaire']    = null; }
	
	    if($k != 0){ $donneesExiste = 1; }
	
	    //Si les resultats n y sont pas on les ajoute
	    if(!$this->getValeursElectrophoreseProteines($iddemande)){
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $donnees['idresultat_demande_analyse'] = $iddemande;
	            $sQuery = $sql->insert() ->into('valeurs_electrophorese_proteine') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	    //Sinon on effectue des mises a jours
	    else {
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_electrophorese_proteine') ->set( $donnees )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	
	    return $donneesExiste;
	}
	
	//****************************************************************************************************
	//****************************************************************************************************
	public function getValeursAlbuminemie($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('va' => 'valeurs_albuminemie'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursAlbuminemie($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	
	    $donnees = array();
	
	    if($tab[1]){ $donnees['type_materiel'] = $tab[1]; }else{ $donnees['type_materiel'] = null; }
	    if($tab[2]){ $donnees['albuminemie']   = $tab[2]; }else{ $donnees['albuminemie']   = null; }
	
	    if($tab[2]){ $donneesExiste = 1; }
	
	    //Si les resultats n y sont pas on les ajoute
	    if(!$this->getValeursAlbuminemie($iddemande)){
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $donnees['idresultat_demande_analyse'] = $iddemande;
	            $sQuery = $sql->insert() ->into('valeurs_albuminemie') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	    //Sinon on effectue des mises a jours
	    else {
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_albuminemie') ->set( $donnees )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	
	    return $donneesExiste;
	}
	
	//****************************************************************************************************
	//****************************************************************************************************
	public function getValeursAlbumineUrinaire($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('va' => 'valeurs_albumine_urinaire'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursAlbumineUrinaire($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	
	    $donnees = array();
	
	    if($tab[1]){ $donnees['type_materiel'] = $tab[1]; }else{ $donnees['type_materiel'] = null; }
	    
	    //*****************************************************
	    if($tab[2]){
	        $donnees['albumine_urinaire'] = $tab[2]; 
	        if($tab[2] == 'positif'){ 
	            $donnees['albumine_urinaire_degres'] = $tab[3]; 
	        }
	        else{ 
	            $donnees['albumine_urinaire_degres'] = null; 
	        }
	    }else{
	        $donnees['albumine_urinaire'] = null; 
	        $donnees['albumine_urinaire_degres']  = null; 
	    }
	
	    //*****************************************************
	    if($tab[4]){
	        $donnees['sucre_urinaire'] = $tab[4];
	        if($tab[4] == 'positif'){
	            $donnees['sucre_urinaire_degres'] = $tab[5];
	        }
	        else{
	            $donnees['sucre_urinaire_degres'] = null;
	        }
	    }else{
	        $donnees['sucre_urinaire'] = null;
	        $donnees['sucre_urinaire_degres']  = null;
	    }
	    
	    //*****************************************************
	    if($tab[6]){
	        $donnees['corps_cetonique_urinaire'] = $tab[6];
	        if($tab[6] == 'positif'){
	            $donnees['corps_cetonique_urinaire_degres'] = $tab[7];
	        }
	        else{
	            $donnees['corps_cetonique_urinaire_degres'] = null;
	        }
	    }else{
	        $donnees['corps_cetonique_urinaire'] = null;
	        $donnees['corps_cetonique_urinaire_degres']  = null;
	    }
	    
	    
	    if($tab[2] || $tab[4] || $tab[6]){ $donneesExiste = 1; }
	
	    //Si les resultats n y sont pas on les ajoute
	    if(!$this->getValeursAlbumineUrinaire($iddemande)){
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $donnees['idresultat_demande_analyse'] = $iddemande;
	            $sQuery = $sql->insert() ->into('valeurs_albumine_urinaire') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	    //Sinon on effectue des mises a jours
	    else {
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_albumine_urinaire') ->set( $donnees )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	
	    return $donneesExiste;
	}
	
	//****************************************************************************************************
	//****************************************************************************************************
	public function getValeursProtidemie($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('va' => 'valeurs_protidemie'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursProtidemie($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	
	    $donnees = array();
	
	    if($tab[1]){ $donnees['type_materiel'] = $tab[1]; }else{ $donnees['type_materiel'] = null; }
	    if($tab[2]){ $donnees['protidemie']    = $tab[2]; }else{ $donnees['protidemie']    = null; }
	
	    if($tab[2]){ $donneesExiste = 1; }
	
	    //Si les resultats n y sont pas on les ajoute
	    if(!$this->getValeursProtidemie($iddemande)){
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $donnees['idresultat_demande_analyse'] = $iddemande;
	            $sQuery = $sql->insert() ->into('valeurs_protidemie') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	    //Sinon on effectue des mises a jours
	    else {
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_protidemie') ->set( $donnees )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	
	    return $donneesExiste;
	}
	
	//****************************************************************************************************
	//****************************************************************************************************
	public function getValeursProteinurie($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('va' => 'valeurs_proteinurie'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursProteinurie($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	
	    $donnees = array();
	
	    if($tab[1]){ $donnees['type_materiel'] = $tab[1]; }else{ $donnees['type_materiel'] = null; }
	    if($tab[2]){ $donnees['proteinurie']   = $tab[2]; }else{ $donnees['proteinurie']   = null; }
	
	    if($tab[2]){ $donneesExiste = 1; }
	
	    //Si les resultats n y sont pas on les ajoute
	    if(!$this->getValeursProteinurie($iddemande)){
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $donnees['idresultat_demande_analyse'] = $iddemande;
	            $sQuery = $sql->insert() ->into('valeurs_proteinurie') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	    //Sinon on effectue des mises a jours
	    else {
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_proteinurie') ->set( $donnees )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	
	    return $donneesExiste;
	}
	
	//****************************************************************************************************
	//****************************************************************************************************
	public function getValeursHlmCompteDaddis($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('va' => 'valeurs_hlm_compte_daddis'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursHlmCompteDaddis($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	
	    $donnees = array();
	
	    if($tab[1]){ $donnees['type_materiel'] = $tab[1]; }else{ $donnees['type_materiel'] = null; }
	    if($tab[2]){ $donnees['hematies_hlm']  = $tab[2]; }else{ $donnees['hematies_hlm']  = null; }
	    if($tab[3]){ $donnees['leucocytes_hlm']= $tab[3]; }else{ $donnees['leucocytes_hlm']= null; }
	
	    if($tab[2] || $tab[3]){ $donneesExiste = 1; }
	
	    //Si les resultats n y sont pas on les ajoute
	    if(!$this->getValeursHlmCompteDaddis($iddemande)){
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $donnees['idresultat_demande_analyse'] = $iddemande;
	            $sQuery = $sql->insert() ->into('valeurs_hlm_compte_daddis') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	    //Sinon on effectue des mises a jours
	    else {
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_hlm_compte_daddis') ->set( $donnees )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	
	    return $donneesExiste;
	}
	
	//****************************************************************************************************
	//****************************************************************************************************
	public function getValeursBetaHcgPlasmatique($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('va' => 'valeurs_beta_hcg_plasmatique'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursBetaHcgPlasmatique($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	
	    $donnees = array();
	
	    if($tab[1]){ $donnees['type_materiel']        = $tab[1]; }else{ $donnees['type_materiel']        = null; }
	    if($tab[2]){ $donnees['beta_hcg_plasmatique'] = $tab[2]; }else{ $donnees['beta_hcg_plasmatique'] = null; }
	
	    if($tab[2]){ $donneesExiste = 1; }
	
	    //Si les resultats n y sont pas on les ajoute
	    if(!$this->getValeursBetaHcgPlasmatique($iddemande)){
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $donnees['idresultat_demande_analyse'] = $iddemande;
	            $sQuery = $sql->insert() ->into('valeurs_beta_hcg_plasmatique') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	    //Sinon on effectue des mises a jours
	    else {
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_beta_hcg_plasmatique') ->set( $donnees )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	
	    return $donneesExiste;
	}
	

	//****************************************************************************************************
	//****************************************************************************************************
	public function getValeursPsa($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('va' => 'valeurs_psa'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursPsa($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	
	    $donnees = array();
	
	    if($tab[1]){ $donnees['type_materiel'] = $tab[1]; }else{ $donnees['type_materiel'] = null; }
	    if($tab[2]){ $donnees['psa']           = $tab[2]; }else{ $donnees['psa']           = null; }
	
	    if($tab[2]){ $donneesExiste = 1; }
	
	    //Si les resultats n y sont pas on les ajoute
	    if(!$this->getValeursPsa($iddemande)){
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $donnees['idresultat_demande_analyse'] = $iddemande;
	            $sQuery = $sql->insert() ->into('valeurs_psa') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	    //Sinon on effectue des mises a jours
	    else {
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_psa') ->set( $donnees )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	
	    return $donneesExiste;
	}
	

	//****************************************************************************************************
	//****************************************************************************************************
	public function getValeursCrp($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('va' => 'valeurs_crp'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursCrp($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	
	    $donnees = array();
	
	    if($tab[1]){ $donnees['type_materiel'] = $tab[1]; }else{ $donnees['type_materiel'] = null; }
	    if($tab[2]){ $donnees['crp']           = $tab[2]; }else{ $donnees['crp']           = null; }
	
	    if($tab[2]){ $donneesExiste = 1; }
	
	    //Si les resultats n y sont pas on les ajoute
	    if(!$this->getValeursCrp($iddemande)){
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $donnees['idresultat_demande_analyse'] = $iddemande;
	            $sQuery = $sql->insert() ->into('valeurs_crp') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	    //Sinon on effectue des mises a jours
	    else {
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_crp') ->set( $donnees )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	
	    return $donneesExiste;
	}
	
	//****************************************************************************************************
	//****************************************************************************************************
	public function getValeursFacteursRhumatoides($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('va' => 'valeurs_facteurs_rhumatoides'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursFacteursRhumatoides($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	
	    $donnees = array();
	
	    if($tab[1]){ $donnees['type_materiel']        = $tab[1]; }else{ $donnees['type_materiel']        = null; }
	    if($tab[2]){ $donnees['facteurs_rhumatoides'] = $tab[2]; }else{ $donnees['facteurs_rhumatoides'] = null; }
	
	    if($tab[2]){ $donneesExiste = 1; }
	
	    //Si les resultats n y sont pas on les ajoute
	    if(!$this->getValeursFacteursRhumatoides($iddemande)){
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $donnees['idresultat_demande_analyse'] = $iddemande;
	            $sQuery = $sql->insert() ->into('valeurs_facteurs_rhumatoides') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	    //Sinon on effectue des mises a jours
	    else {
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_facteurs_rhumatoides') ->set( $donnees )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	
	    return $donneesExiste;
	}
	
	//****************************************************************************************************
	//****************************************************************************************************
	public function getValeursRfWaalerRose($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('va' => 'valeurs_rf_waaler_rose'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursRfWaalerRose($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	
	    $donnees = array();
	
	    if($tab[1]){ $donnees['type_materiel']  = $tab[1]; }else{ $donnees['type_materiel']  = null; }
	    if($tab[2]){ $donnees['rf_waaler_rose'] = $tab[2]; }else{ $donnees['rf_waaler_rose'] = null; }
	
	    if($tab[2]){ $donneesExiste = 1; }
	
	    //Si les resultats n y sont pas on les ajoute
	    if(!$this->getValeursRfWaalerRose($iddemande)){
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $donnees['idresultat_demande_analyse'] = $iddemande;
	            $sQuery = $sql->insert() ->into('valeurs_rf_waaler_rose') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	    //Sinon on effectue des mises a jours
	    else {
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_rf_waaler_rose') ->set( $donnees )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	
	    return $donneesExiste;
	}
	
	//****************************************************************************************************
	//****************************************************************************************************
	public function getValeursToxoplasmose($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('va' => 'valeurs_toxoplasmose'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursToxoplasmose($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	
	    $donnees = array();
	
	    if($tab[1]){ $donnees['type_materiel']  = $tab[1]; }else{ $donnees['type_materiel']  = null; }
	    if($tab[2]){ $donnees['toxoplasmose_1'] = $tab[2]; }else{ $donnees['toxoplasmose_1'] = null; }
	    if($tab[3]){ $donnees['toxoplasmose_2'] = $tab[3]; }else{ $donnees['toxoplasmose_2'] = null; }
	
	    if($tab[2] || $tab[3]){ $donneesExiste = 1; }
	
	    //Si les resultats n y sont pas on les ajoute
	    if(!$this->getValeursToxoplasmose($iddemande)){
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $donnees['idresultat_demande_analyse'] = $iddemande;
	            $sQuery = $sql->insert() ->into('valeurs_toxoplasmose') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	    //Sinon on effectue des mises a jours
	    else {
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_toxoplasmose') ->set( $donnees )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	
	    return $donneesExiste;
	}
	

	//****************************************************************************************************
	//****************************************************************************************************
	public function getValeursRubeole($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('va' => 'valeurs_rubeole'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursRubeole($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	
	    $donnees = array();
	
	    if($tab[1]){ $donnees['type_materiel']  = $tab[1]; }else{ $donnees['type_materiel']  = null; }
	    if($tab[2]){ $donnees['rubeole_1'] = $tab[2]; }else{ $donnees['rubeole_1'] = null; }
	    if($tab[3]){ $donnees['rubeole_2'] = $tab[3]; }else{ $donnees['rubeole_2'] = null; }
	
	    if($tab[2] || $tab[3]){ $donneesExiste = 1; }
	
	    //Si les resultats n y sont pas on les ajoute
	    if(!$this->getValeursRubeole($iddemande)){
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $donnees['idresultat_demande_analyse'] = $iddemande;
	            $sQuery = $sql->insert() ->into('valeurs_rubeole') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	    //Sinon on effectue des mises a jours
	    else {
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_rubeole') ->set( $donnees )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	
	    return $donneesExiste;
	}
	
	//****************************************************************************************************
	//****************************************************************************************************
	public function getValeursCulotUrinaire($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('va' => 'valeurs_culot_urinaire'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursCulotUrinaire($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	
	    $donnees = array();
	
	    if($tab[1]){ $donnees['type_materiel']    = $tab[1]; }else{ $donnees['type_materiel']    = null; }
	    if($tab[2]){ $donnees['culot_urinaire_1'] = $tab[2]; }else{ $donnees['culot_urinaire_1'] = null; }
	    if($tab[3]){ $donnees['culot_urinaire_2'] = $tab[3]; }else{ $donnees['culot_urinaire_2'] = null; }
	
	    if($tab[2] || $tab[3]){ $donneesExiste = 1; }
	
	    //Si les resultats n y sont pas on les ajoute
	    if(!$this->getValeursCulotUrinaire($iddemande)){
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $donnees['idresultat_demande_analyse'] = $iddemande;
	            $sQuery = $sql->insert() ->into('valeurs_culot_urinaire') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	    //Sinon on effectue des mises a jours
	    else {
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_culot_urinaire') ->set( $donnees )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	
	    return $donneesExiste;
	}
	

	//****************************************************************************************************
	//****************************************************************************************************
	public function getValeursSerologieChlamydiae($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('va' => 'valeurs_serologie_chlamydiae'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursSerologieChlamydiae($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	
	    $donnees = array();
	
	    if($tab[1]){ $donnees['type_materiel']        = $tab[1]; }else{ $donnees['type_materiel']        = null; }
	    if($tab[2]){ $donnees['serologie_chlamydiae'] = $tab[2]; }else{ $donnees['serologie_chlamydiae'] = null; }
	
	    if($tab[2]){ $donneesExiste = 1; }
	
	    //Si les resultats n y sont pas on les ajoute
	    if(!$this->getValeursSerologieChlamydiae($iddemande)){
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $donnees['idresultat_demande_analyse'] = $iddemande;
	            $sQuery = $sql->insert() ->into('valeurs_serologie_chlamydiae') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	    //Sinon on effectue des mises a jours
	    else {
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_serologie_chlamydiae') ->set( $donnees )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	
	    return $donneesExiste;
	}
	

	//****************************************************************************************************
	//****************************************************************************************************
	public function getValeursSerologieSyphilitique($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('va' => 'valeurs_serologie_syphilitique'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursSerologieSyphilitique($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	
	    $donnees = array();
	
	    if($tab[1]){ $donnees['type_materiel']          = $tab[1]; }else{ $donnees['type_materiel']          = null; }
	    if($tab[2]){ $donnees['serologie_syphilitique'] = $tab[2]; }else{ $donnees['serologie_syphilitique'] = null; }
	
	    if($tab[2]){ $donneesExiste = 1; }
	
	    //Si les resultats n y sont pas on les ajoute
	    if(!$this->getValeursSerologieSyphilitique($iddemande)){
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $donnees['idresultat_demande_analyse'] = $iddemande;
	            $sQuery = $sql->insert() ->into('valeurs_serologie_syphilitique') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	    //Sinon on effectue des mises a jours
	    else {
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_serologie_syphilitique') ->set( $donnees )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	
	    return $donneesExiste;
	}
	
	//****************************************************************************************************
	//****************************************************************************************************
	public function getValeursAslo($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('va' => 'valeurs_aslo'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursAslo($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	
	    $donnees = array();
	
	    if($tab[1]){ $donnees['type_materiel'] = $tab[1]; }else{ $donnees['type_materiel'] = null; }
	    if($tab[2]){ $donnees['aslo']          = $tab[2]; }else{ $donnees['aslo']          = null; }
	
	    if($tab[2]){ $donneesExiste = 1; }
	
	    //Si les resultats n y sont pas on les ajoute
	    if(!$this->getValeursAslo($iddemande)){
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $donnees['idresultat_demande_analyse'] = $iddemande;
	            $sQuery = $sql->insert() ->into('valeurs_aslo') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	    //Sinon on effectue des mises a jours
	    else {
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_aslo') ->set( $donnees )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	
	    return $donneesExiste;
	}
	
	//****************************************************************************************************
	//****************************************************************************************************
	public function getValeursWidal($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('va' => 'valeurs_widal'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursWidal($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	    $cmp = 0;
	
	    $donnees = array();
	
	    if($tab[1] ){ $donnees['type_materiel']  = $tab[1] ; }else{ $donnees['type_materiel']  = null; }
	    if($tab[2] ){ $donnees['widal_to']       = $tab[2] ; }else{ $donnees['widal_to']       = null; $cmp++; }
	    if($tab[3] ){ $donnees['widal_titre_to'] = $tab[3] ; }else{ $donnees['widal_titre_to'] = null; $cmp++; }
	    if($tab[4] ){ $donnees['widal_th']       = $tab[4] ; }else{ $donnees['widal_th']       = null; $cmp++; }
	    if($tab[5] ){ $donnees['widal_titre_th'] = $tab[5] ; }else{ $donnees['widal_titre_th'] = null; $cmp++; }
	    if($tab[6] ){ $donnees['widal_ao']       = $tab[6] ; }else{ $donnees['widal_ao']       = null; $cmp++; }
	    if($tab[7] ){ $donnees['widal_titre_ao'] = $tab[7] ; }else{ $donnees['widal_titre_ao'] = null; $cmp++; }
	    if($tab[8] ){ $donnees['widal_ah']       = $tab[8] ; }else{ $donnees['widal_ah']       = null; $cmp++; }
	    if($tab[9] ){ $donnees['widal_titre_ah'] = $tab[9] ; }else{ $donnees['widal_titre_ah'] = null; $cmp++; }
	    if($tab[10]){ $donnees['widal_bo']       = $tab[10]; }else{ $donnees['widal_bo']       = null; $cmp++; }
	    if($tab[11]){ $donnees['widal_titre_bo'] = $tab[11]; }else{ $donnees['widal_titre_bo'] = null; $cmp++; }
	    if($tab[12]){ $donnees['widal_bh']       = $tab[12]; }else{ $donnees['widal_bh']       = null; $cmp++; }
	    if($tab[13]){ $donnees['widal_titre_bh'] = $tab[13]; }else{ $donnees['widal_titre_bh'] = null; $cmp++; }
	    if($tab[14]){ $donnees['widal_co']       = $tab[14]; }else{ $donnees['widal_co']       = null; $cmp++; }
	    if($tab[15]){ $donnees['widal_titre_co'] = $tab[15]; }else{ $donnees['widal_titre_co'] = null; $cmp++; }
	    if($tab[16]){ $donnees['widal_ch']       = $tab[16]; }else{ $donnees['widal_ch']       = null; $cmp++; }
	    if($tab[17]){ $donnees['widal_titre_ch'] = $tab[17]; }else{ $donnees['widal_titre_ch'] = null; $cmp++; }
	    
	    if($cmp < 16){ $donneesExiste = 1; }
	
	    //Si les resultats n y sont pas on les ajoute
	    if(!$this->getValeursWidal($iddemande)){
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $donnees['idresultat_demande_analyse'] = $iddemande;
	            $sQuery = $sql->insert() ->into('valeurs_widal') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	    //Sinon on effectue des mises a jours
	    else {
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_widal') ->set( $donnees )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	
	    return $donneesExiste;
	}
	
	//****************************************************************************************************
	//****************************************************************************************************
	public function getValeursAgHbs($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('va' => 'valeurs_ag_hbs'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursAgHbs($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	
	    $donnees = array();
	
	    if($tab[1]){ $donnees['type_materiel'] = $tab[1]; }else{ $donnees['type_materiel'] = null; }
	    if($tab[2]){ $donnees['ag_hbs']        = $tab[2]; }else{ $donnees['ag_hbs']        = null; }
	
	    if($tab[2]){ $donneesExiste = 1; }
	
	    //Si les resultats n y sont pas on les ajoute
	    if(!$this->getValeursAgHbs($iddemande)){
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $donnees['idresultat_demande_analyse'] = $iddemande;
	            $sQuery = $sql->insert() ->into('valeurs_ag_hbs') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	    //Sinon on effectue des mises a jours
	    else {
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_ag_hbs') ->set( $donnees )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	
	    return $donneesExiste;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	//GESTION DES IMPRESSIONS DES RESULTATS DES ANALYSES
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
	public function getListeResultatsAnalysesDemandees($iddemande){
	    $dateDemande = $this->getDemandeAnalysesAvecIddemande($iddemande);
	
	    $adapter = $this->tableGateway->getAdapter ();
	    $sql = new Sql($adapter);
	    $select = $sql->select();
	    $select->from(array('d'=>'demande_analyse'));
	    $select->columns(array('*'));
	    $select->join(array('a'=>'analyse') ,'d.idanalyse = a.idanalyse', array('Designation'=>'designation', 'Tarif'=>'tarif'));
	    $select->join(array('t'=>'type_analyse') ,'t.idtype = a.idtype_analyse', array('Libelle'=>'libelle'));
	    $select->join(array('r'=>'resultat_demande_analyse'), 'd.iddemande = r.iddemande_analyse', array('DateEnregistrementResultat'=>'date'));
	    $select->join(array('p'=>'personne'), 'p.idpersonne = r.idemploye', array('Nom'=>'nom', 'Prenom'=>'prenom'));
	    $select->where(array('d.date' => $dateDemande['date'], 'd.idpatient' => $dateDemande['idpatient']));
	    $select->order(array('idanalyse' => 'ASC', 'idtype' =>'ASC'));
	    $result = $sql->prepareStatementForSqlObject($select)->execute();
	    
	    $resultats = array();
	    foreach ($result as $res){
	        $resultats [] = $res;
	    }
	    
	    return $resultats;
	}
	
	//UTILISER DANS LE MODULE DU SECRETAIRE
	//UTILISER DANS LE MODULE DU SECRETAIRE
	//UTILISER DANS LE MODULE DU SECRETAIRE
	//Recuperer la liste des analyses demandees pour la demande $iddemande
	public function getListeResultatsAnalysesDemandeesImpSecretaire($iddemande){
		$dateDemande = $this->getDemandeAnalysesAvecIddemande($iddemande);
	
		$adapter = $this->tableGateway->getAdapter ();
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from(array('d'=>'demande_analyse'));
		$select->columns(array('*'));
		$select->join(array('a'=>'analyse') ,'d.idanalyse = a.idanalyse', array('Designation'=>'designation', 'Tarif'=>'tarif'));
		$select->join(array('t'=>'type_analyse') ,'t.idtype = a.idtype_analyse', array('Libelle'=>'libelle'));
		$select->join(array('r'=>'resultat_demande_analyse'), 'd.iddemande = r.iddemande_analyse', array('DateEnregistrementResultat'=>'date'));
		$select->join(array('p'=>'personne'), 'p.idpersonne = r.idemploye', array('Nom'=>'nom', 'Prenom'=>'prenom'));

		$select->join(array('p2'=>'personne'), 'p2.idpersonne = r.valider_par', array('NomValidateur'=>'nom', 'PrenomValidateur'=>'prenom'));
		
		$select->where(array('d.date' => $dateDemande['date'], 'd.idpatient' => $dateDemande['idpatient']));
		$select->order(array('idanalyse' => 'ASC', 'idtype' =>'ASC'));
		$result = $sql->prepareStatementForSqlObject($select)->execute();
		 
		$resultats = array();
		foreach ($result as $res){
			$resultats [] = $res;
		}
		 
		return $resultats;
	}
	
	//R�cuper la liste des analyses de type NFS du patient pour lesquels les r�sultats sont entr�s
	public function getListeAnalysesNFSDemandeesAyantResultats($idpatient, $iddemande){
	    $adapter = $this->tableGateway->getAdapter ();
	    $sql = new Sql($adapter);
	    $select = $sql->select();
	    $select->from(array('d'=>'demande_analyse'));
	    $select->columns(array('*'));
	    $select->join(array('a'=>'analyse') ,'d.idanalyse = a.idanalyse', array('Designation'=>'designation', 'Tarif'=>'tarif'));
	    $select->join(array('t'=>'type_analyse') ,'t.idtype = a.idtype_analyse', array('Libelle'=>'libelle'));
	    $select->join(array('r'=>'resultat_demande_analyse'), 'd.iddemande = r.iddemande_analyse', array('DateEnregistrementResultat'=>'date'));
	    $select->join(array('p'=>'personne'), 'p.idpersonne = r.idemploye', array('Nom'=>'nom', 'Prenom'=>'prenom'));
	    $select->where(array('d.idpatient' => $idpatient, 'd.idanalyse' => 1, 'd.iddemande < ?' => $iddemande));
	    $select->order(array('d.iddemande' => 'DESC'));
	    $result = $sql->prepareStatementForSqlObject($select)->execute();
	     
	    $resultats = array();
	    foreach ($result as $res){
	        $resultats [] = $res;
	    }

	    return $resultats;
	}
	
}