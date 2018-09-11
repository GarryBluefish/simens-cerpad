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
	
 	//Le nombre de patients d�pist�s 
 	public function getNbPatientsDepistes(){
 		$adapter = $this->tableGateway->getAdapter();
 		$sql = new Sql($adapter);
 		$select = $sql->select();
 		$select->from(array('p' => 'patient'));
 		$select->join(array('d' => 'depistage') ,'d.idpatient = p.idpersonne');
 		return $sql->prepareStatementForSqlObject($select)->execute()->count();
 	}
 	
 	//Le nombre de patients d�pist�s positif (INTERNE)
 	public function getNbPatientsDepistesPositifs(){
 		$adapter = $this->tableGateway->getAdapter();
 		$sql = new Sql($adapter);
 		$select = $sql->select();
 		$select->from(array('p' => 'patient'));
 		$select->join(array('d' => 'depistage') ,'d.idpatient = p.idpersonne');
 		$select->where(array('d.typepatient' => 1, 'd.valide' => 1));
 		return $sql->prepareStatementForSqlObject($select)->execute()->count();
 	}
 	
 	//Le nombre de patients d�pist�s n�gatif (EXTERNE)
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
 	
 	//Le nombre de patients d�pist�s positif (INTERNE) Sexe Feminin
 	public function getNbPatientsDepistesPositifsFeminin(){
 		$adapter = $this->tableGateway->getAdapter();
 		$sql = new Sql($adapter);
 		$select = $sql->select();
 		$select->from(array('p' => 'patient'));
 		$select->join(array('pers' => 'personne') ,'pers.idpersonne = p.idpersonne');
 		$select->join(array('d' => 'depistage') ,'d.idpatient = p.idpersonne');
 		$select->where(array('d.typepatient' => 1, 'd.valide' => 1, 'sexe' => 'Féminin'));
 		return $sql->prepareStatementForSqlObject($select)->execute()->count();
 	}
 	
 	//Le nombre de patients d�pist�s positif (INTERNE) Sexe Masculin
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
 	
 	//Les formes graves d�pist�es actuellement
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
 	
 	
 	
 	
 	
 	
 	
 	/**
 	 * =================================================
 	 */
 	 //D�pistage n�onatal de la dr�panocytose - MENU N�2 
 	 //Nouveau-n�s d�pist�s avec sex-ratio
 	/**
 	 * -------------------------------------------------
 	 */ 
 	
 	//Le nombre de patients d�pist�s et valid�s de Sexe Feminin
 	public function getNbPatientsDepistesValidesSexeFeminin(){
 		$adapter = $this->tableGateway->getAdapter();
 		$sql = new Sql($adapter);
 		$select = $sql->select();
 		$select->from(array('p' => 'patient'));
 		$select->join(array('pers' => 'personne') ,'pers.idpersonne = p.idpersonne');
 		$select->join(array('d' => 'depistage') ,'d.idpatient = p.idpersonne');
 		$select->where(array('d.valide' => 1, 'sexe' => 'Féminin'));
 		return $sql->prepareStatementForSqlObject($select)->execute()->count();
 	}
 	
 	//Le nombre de patients d�pist�s et valid�s de Sexe Masculin
 	public function getNbPatientsDepistesValidesSexeMasculin(){
 		$adapter = $this->tableGateway->getAdapter();
 		$sql = new Sql($adapter);
 		$select = $sql->select();
 		$select->from(array('p' => 'patient'));
 		$select->join(array('pers' => 'personne') ,'pers.idpersonne = p.idpersonne');
 		$select->join(array('d' => 'depistage') ,'d.idpatient = p.idpersonne');
 		$select->where(array('d.valide' => 1, 'sexe' => 'Masculin'));
 		return $sql->prepareStatementForSqlObject($select)->execute()->count();
 	}
 	
 	//Le nombre de patients d�pist�s et non valid�s de Sexe Feminin
 	public function getNbPatientsDepistesNonValidesSexeFeminin(){
 		$adapter = $this->tableGateway->getAdapter();
 		$sql = new Sql($adapter);
 		$select = $sql->select();
 		$select->from(array('p' => 'patient'));
 		$select->join(array('pers' => 'personne') ,'pers.idpersonne = p.idpersonne');
 		$select->join(array('d' => 'depistage') ,'d.idpatient = p.idpersonne');
 		$select->where(array('d.valide' => 0, 'sexe' => 'Féminin'));
 		return $sql->prepareStatementForSqlObject($select)->execute()->count();
 	}
 	
 	//Le nombre de patients d�pist�s et valid�s de Sexe Masculin
 	public function getNbPatientsDepistesNonValidesSexeMasculin(){
 		$adapter = $this->tableGateway->getAdapter();
 		$sql = new Sql($adapter);
 		$select = $sql->select();
 		$select->from(array('p' => 'patient'));
 		$select->join(array('pers' => 'personne') ,'pers.idpersonne = p.idpersonne');
 		$select->join(array('d' => 'depistage') ,'d.idpatient = p.idpersonne');
 		$select->where(array('d.valide' => 0, 'sexe' => 'Masculin'));
 		return $sql->prepareStatementForSqlObject($select)->execute()->count();
 	}
 	
 	
 	/**
 	 * =================================================
 	 */
 	//D�pistage n�onatal de la dr�panocytose - MENU N�2
 	//Pour les parents des nouveau-n�s
 	/**
 	 * -------------------------------------------------
 	 */
 	
 	/**
 	 * La r�partition selon les ethnies des nouveau-n�s
 	 */
 	public function getRepartitionDesPeresSelonEthnies(){
 		$adapter = $this->tableGateway->getAdapter();
 		$sql = new Sql($adapter);
 		$select = $sql->select();
 		$select->from(array('p' => 'patient'));
 		$select->join(array('pers' => 'personne') ,'pers.idpersonne = p.idpersonne');
 		$select->join(array('d' => 'depistage') ,'d.idpatient = p.idpersonne');
 		
 		$select->join(array('fda' => 'facturation_demande_analyse') , 'fda.iddemande_analyse = d.iddemande_analyse' , array('*'));
 		$select->join(array('bp' => 'bilan_prelevement') , 'bp.idfacturation = fda.idfacturation' , array('date_prelevement'));
 		
 		$select->join(array('pr' => 'parent') ,'pr.idpatient = p.idpersonne');
 		$select->where(array('d.valide' => 1,'parent' => 'pere', 'ethnie  != ?' => ''));
 		$select->order('ethnie ASC');
 		
 		$resultat = $sql->prepareStatementForSqlObject($select)->execute();
 		
 		$tabResultat = array();
 		$tabEthnies = array();
 		$tabListeEthnies = array();
 		
 		foreach ($resultat as $result){
 			$tabResultat[] = $result;
 			
 			$tabListeEthnies[] = $result['ethnie'];
 			if(!in_array($result['ethnie'], $tabEthnies)){
 				$tabEthnies[] = $result['ethnie'];
 			}
 		}
 		
 		return array($tabEthnies, array_count_values($tabListeEthnies));
 	}
 	
 	
 	/**
 	 * Les diff�rents types de profils rencontr�s
 	 */
 	public function getDifferentsTypesProfils(){
 		$adapter = $this->tableGateway->getAdapter();
 		$sql = new Sql($adapter);
 		$select = $sql->select();
 		$select->from(array('p' => 'patient'));
 		$select->join(array('pers' => 'personne') ,'pers.idpersonne = p.idpersonne');
 		$select->join(array('d' => 'depistage') ,'d.idpatient = p.idpersonne');
 		$select->join(array('th' => 'typage_hemoglobine') ,'th.idtypage = d.typage');
 		
 		$select->join(array('fda' => 'facturation_demande_analyse') , 'fda.iddemande_analyse = d.iddemande_analyse' , array('*'));
 		$select->join(array('bp' => 'bilan_prelevement') , 'bp.idfacturation = fda.idfacturation' , array('date_prelevement'));
 		
 		$select->where(array('d.valide' => 1));
 		$select->order('designation_stat ASC');
 		$resultat = $sql->prepareStatementForSqlObject($select)->execute();
 		
 		$tabListeTypages = array();
 		$tabTypages = array();
 		foreach ($resultat as $res){
 			$tabListeTypages[] = $res['designation_stat'];
 			if(!in_array($res['designation_stat'], $tabTypages)){
 				$tabTypages[] = $res['designation_stat'];
 			}
 		}
 			
 		return array($tabTypages, array_count_values($tabListeTypages));
 	}
 	
 	/**
 	 * R�partition des diff�rents types d'h�moglobine selon les ethnies
 	 */
 	public function getRepartitionTypesProfilsSelonEthnies(){
 		$adapter = $this->tableGateway->getAdapter();
 		$sql = new Sql($adapter);
 		$select = $sql->select();
 		$select->from(array('p' => 'patient'));
 		$select->join(array('pers' => 'personne') ,'pers.idpersonne = p.idpersonne');
 		$select->join(array('d' => 'depistage') ,'d.idpatient = p.idpersonne');
 		$select->join(array('th' => 'typage_hemoglobine') ,'th.idtypage = d.typage');
 			
 		$select->join(array('fda' => 'facturation_demande_analyse') , 'fda.iddemande_analyse = d.iddemande_analyse' , array('*'));
 		$select->join(array('bp' => 'bilan_prelevement') , 'bp.idfacturation = fda.idfacturation' , array('date_prelevement'));
 			
 		$select->where(array('d.valide' => 1, 'ethnie  != ?' => ''));
 		$select->order(array('ethnie' => 'ASC'));
 		$resultat = $sql->prepareStatementForSqlObject($select)->execute();
 			
 		$tabProfils = array();
 		$tabEthnies = array();
 		$tabProfilsParEthnie = array();
 		
 		foreach ($resultat as $result){
 			$profil = $result['designation_stat'];
 			if(!in_array($profil, $tabProfils)){
 				$tabProfils[] = $profil;
 			}
 			
 			if(!in_array($result['ethnie'], $tabEthnies)){
 				$ethnie = $result['ethnie'];
 				$tabEthnies[] = $ethnie;
 				$tabProfilsParEthnie [$ethnie] = array();
 			}
 			
 			$tabProfilsParEthnie [$ethnie][] = $profil;
 		}

 		sort($tabProfils);
 		
 		return array($tabProfils, $tabEthnies, $tabProfilsParEthnie);
 	}
 	
 	
 	
 	
 	
 	
 	
 	
 	
}