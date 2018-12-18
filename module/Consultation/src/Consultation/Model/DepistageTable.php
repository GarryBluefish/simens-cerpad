<?php
namespace Consultation\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;

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
	
 	//Le nombre de patients dépistés 
 	public function getNbPatientsDepistes(){
 		$adapter = $this->tableGateway->getAdapter();
 		$sql = new Sql($adapter);
 		$select = $sql->select();
 		$select->from(array('p' => 'patient'));
 		$select->join(array('d' => 'depistage') ,'d.idpatient = p.idpersonne');
 		return $sql->prepareStatementForSqlObject($select)->execute()->count();
 	}
 	
 	//Le nombre de patients dépistés positif (INTERNE)
 	public function getNbPatientsDepistesPositifs(){
 		$adapter = $this->tableGateway->getAdapter();
 		$sql = new Sql($adapter);
 		$select = $sql->select();
 		$select->from(array('p' => 'patient'));
 		$select->join(array('d' => 'depistage') ,'d.idpatient = p.idpersonne');
 		$select->where(array('d.typepatient' => 1, 'd.valide' => 1));
 		return $sql->prepareStatementForSqlObject($select)->execute()->count();
 	}
 	
 	//Le nombre de patients dépistés négatif (EXTERNE)
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
 	
 	//Le nombre de patients dépistés positif (INTERNE) Sexe Feminin
 	public function getNbPatientsDepistesPositifsFeminin(){
 		$adapter = $this->tableGateway->getAdapter();
 		$sql = new Sql($adapter);
 		$select = $sql->select();
 		$select->from(array('p' => 'patient'));
 		$select->join(array('pers' => 'personne') ,'pers.idpersonne = p.idpersonne');
 		$select->join(array('d' => 'depistage') ,'d.idpatient = p.idpersonne');
 		$select->where(array('d.typepatient' => 1, 'd.valide' => 1, 'sexe' => 'FÃ©minin'));
 		return $sql->prepareStatementForSqlObject($select)->execute()->count();
 	}
 	
 	//Le nombre de patients dépistés positif (INTERNE) Sexe Masculin
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
 	
 	//Les formes graves dépistées actuellement
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
 	 //Dépistage néonatal de la drépanocytose - MENU N°2 
 	 //Nouveau-nés dépistés avec sex-ratio
 	/**
 	 * -------------------------------------------------
 	 */ 
 	
 	//Le nombre de patients dépistés et validés de Sexe Feminin
 	public function getNbPatientsDepistesValidesSexeFeminin(){
 		$adapter = $this->tableGateway->getAdapter();
 		$sql = new Sql($adapter);
 		$select = $sql->select();
 		$select->from(array('p' => 'patient'));
 		$select->join(array('pers' => 'personne') ,'pers.idpersonne = p.idpersonne');
 		$select->join(array('d' => 'depistage') ,'d.idpatient = p.idpersonne');
 		
 		
 		$select->join(array('fda' => 'facturation_demande_analyse') , 'fda.iddemande_analyse = d.iddemande_analyse' , array('*'));
 		$select->join(array('bp' => 'bilan_prelevement') , 'bp.idfacturation = fda.idfacturation' , array('date_prelevement'));
 		
 		
 		//$select->where(array('sexe' => 'FÃ©minin', 'date_prelevement >=?' => '2017-04-25', 'date_prelevement <=?' => '2018-04-24'));
 		$select->where(array('d.valide' => 1, 'sexe' => 'FÃ©minin'));
 		return $sql->prepareStatementForSqlObject($select)->execute()->count();
 	}
 	
 	//Le nombre de patients dépistés et validés de Sexe Masculin
 	public function getNbPatientsDepistesValidesSexeMasculin(){
 		$adapter = $this->tableGateway->getAdapter();
 		$sql = new Sql($adapter);
 		$select = $sql->select();
 		$select->from(array('p' => 'patient'));
 		$select->join(array('pers' => 'personne') ,'pers.idpersonne = p.idpersonne');
 		$select->join(array('d' => 'depistage') ,'d.idpatient = p.idpersonne');
 		
 		$select->join(array('fda' => 'facturation_demande_analyse') , 'fda.iddemande_analyse = d.iddemande_analyse' , array('*'));
 		$select->join(array('bp' => 'bilan_prelevement') , 'bp.idfacturation = fda.idfacturation' , array('date_prelevement'));
 		
 		//$select->where(array('sexe' => 'Masculin', 'date_prelevement >=?' => '2017-04-25', 'date_prelevement <=?' => '2018-04-24'));
 		$select->where(array('d.valide' => 1, 'sexe' => 'Masculin'));
 		return $sql->prepareStatementForSqlObject($select)->execute()->count();
 	}
 	
 	//Le nombre de patients dépistés et non validés de Sexe Feminin
 	public function getNbPatientsDepistesNonValidesSexeFeminin(){
 		$adapter = $this->tableGateway->getAdapter();
 		$sql = new Sql($adapter);
 		$select = $sql->select();
 		$select->from(array('p' => 'patient'));
 		$select->join(array('pers' => 'personne') ,'pers.idpersonne = p.idpersonne');
 		$select->join(array('d' => 'depistage') ,'d.idpatient = p.idpersonne');
 		$select->where(array('d.valide' => 0, 'sexe' => 'FÃ©minin'));
 		return $sql->prepareStatementForSqlObject($select)->execute()->count();
 	}
 	
 	//Le nombre de patients dépistés et validés de Sexe Masculin
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
 	//Dépistage néonatal de la drépanocytose - MENU N°2
 	//Pour les parents des nouveau-nés
 	/**
 	 * -------------------------------------------------
 	 */
 	
 	/**
 	 * La répartition selon les ethnies des nouveau-nés
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
 		
 		//$select->where(array('parent' => 'pere', 'ethnie  != ?' => '', 'date_prelevement >=?' => '2017-04-25', 'date_prelevement <=?' => '2018-04-24'));
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
 	 * Les différents types de profils rencontrés
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

 		
 		//$select->where(array('date_prelevement >=?' => '2017-04-25', 'date_prelevement <=?' => '2018-04-24'));
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
 	 * Répartition des différents types d'hémoglobine selon les ethnies
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
 			
 		//$select->where(array( 'ethnie  != ?' => '', 'date_prelevement >=?' => '2017-04-25', 'date_prelevement <=?' => '2018-04-24'));
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
 	
 	
 	/**
 	 * Les professions rencontrées chez les mères
 	 */
 	public function getRepartitionProfessionChezLesMeres(){
 		$adapter = $this->tableGateway->getAdapter();
 		$sql = new Sql($adapter);
 		$select = $sql->select();
 		$select->from(array('p' => 'patient'));
 		$select->join(array('pers' => 'personne') ,'pers.idpersonne = p.idpersonne');
 		$select->join(array('d' => 'depistage') ,'d.idpatient = p.idpersonne');
 		$select->join(array('th' => 'typage_hemoglobine') ,'th.idtypage = d.typage');
 		$select->join(array('fda' => 'facturation_demande_analyse') , 'fda.iddemande_analyse = d.iddemande_analyse' , array('*'));
 		$select->join(array('bp' => 'bilan_prelevement') , 'bp.idfacturation = fda.idfacturation' , array('date_prelevement'));
 	
 		$select->join(array('pr' => 'parent') ,'pr.idpatient = p.idpersonne');
 		$select->join(array('pers2' => 'personne') ,'pers2.idpersonne = pr.idpersonne', array('Profession' =>'profession'));
 			
 		//$select->where(array('parent' => 'pere', 'ethnie  != ?' => '', 'date_prelevement >=?' => '2017-04-25', 'date_prelevement <=?' => '2018-04-24'));
 		$select->where(array('d.valide' => 1,'parent' => 'mere', 'ethnie  != ?' => ''));
 		$select->order(array('pers2.profession' => 'ASC'));

 		$resultat = $sql->prepareStatementForSqlObject($select)->execute();
 	
 		$difProfessions = array();
 		$listeProfession = array();
 			
 		foreach ($resultat as $result){
 			$profession = $result['Profession'];
 			if(!in_array($profession, $difProfessions)){
 				$difProfessions[] = $profession;
 			}
 			$listeProfession [] = $profession;
 		}
 	
 		return array($difProfessions, array_count_values($listeProfession));
 	}
 	
 	
 	/**
 	 * Les professions rencontrées chez les pères
 	 */
 	public function getRepartitionProfessionChezLesPeres(){
 		$adapter = $this->tableGateway->getAdapter();
 		$sql = new Sql($adapter);
 		$select = $sql->select();
 		$select->from(array('p' => 'patient'));
 		$select->join(array('pers' => 'personne') ,'pers.idpersonne = p.idpersonne');
 		$select->join(array('d' => 'depistage') ,'d.idpatient = p.idpersonne');
 		$select->join(array('th' => 'typage_hemoglobine') ,'th.idtypage = d.typage');
 		$select->join(array('fda' => 'facturation_demande_analyse') , 'fda.iddemande_analyse = d.iddemande_analyse' , array('*'));
 		$select->join(array('bp' => 'bilan_prelevement') , 'bp.idfacturation = fda.idfacturation' , array('date_prelevement'));
 	
 		$select->join(array('pr' => 'parent') ,'pr.idpatient = p.idpersonne');
 		$select->join(array('pers2' => 'personne') ,'pers2.idpersonne = pr.idpersonne', array('Profession' =>'profession'));
 	
 		//$select->where(array('parent' => 'pere', 'ethnie  != ?' => '', 'date_prelevement >=?' => '2017-04-25', 'date_prelevement <=?' => '2018-04-24'));
 		$select->where(array('d.valide' => 1,'parent' => 'pere', 'ethnie  != ?' => ''));
 		$select->order(array('pers2.profession' => 'ASC'));
 	
 		$resultat = $sql->prepareStatementForSqlObject($select)->execute();
 	
 		$difProfessions = array();
 		$listeProfession = array();
 	
 		foreach ($resultat as $result){
 			$profession = $result['Profession'];
 			if(!in_array($profession, $difProfessions)){
 				$difProfessions[] = $profession;
 			}
 			$listeProfession [] = $profession;
 		}
 	
 		return array($difProfessions, array_count_values($listeProfession));
 	}
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	/**
 	 * Les nouveaux dépistés de j=0 à j=8
 	 */
 	public function getEffectifPatientDepistesAges0_8(){
 		$adapter = $this->tableGateway->getAdapter();
 		$sql = new Sql($adapter);
 		$select = $sql->select();
 		$select->from(array('p' => 'patient'));
 		$select->join(array('pers' => 'personne') ,'pers.idpersonne = p.idpersonne');
 		$select->join(array('d' => 'depistage') ,'d.idpatient = p.idpersonne');
 		$select->join(array('th' => 'typage_hemoglobine') ,'th.idtypage = d.typage');
 		$select->join(array('fda' => 'facturation_demande_analyse') , 'fda.iddemande_analyse = d.iddemande_analyse' , array('*'));
 		$select->join(array('bp' => 'bilan_prelevement') , 'bp.idfacturation = fda.idfacturation' , array('date_prelevement'));
 	
 		$select->where(array('date_prelevement >=?' => '2017-04-25', 'date_prelevement <=?' => '2018-04-24'));
 		$select->group('d.idpatient');
 		$resultat = $sql->prepareStatementForSqlObject($select)->execute();
 	
 		$listeAgesPatients = array();
 		foreach ($resultat as $result){
 			$date_naissance = $result['date_naissance'];
 			$date_prelevement = $result['date_prelevement'];
 			
 			$ageJour = $this->nbJours($date_naissance, $date_prelevement);
 			if($ageJour >= 0 && $ageJour <= 8){
 				$listeAgesPatients [] = $ageJour;
 			}
 		}
 	
 		$effectifPatientsDepistesParAge = array_count_values($listeAgesPatients); 
 		ksort($effectifPatientsDepistesParAge);
 		$listeDesAges = array_values(array_flip($effectifPatientsDepistesParAge));
 		
 		return array(array_values($effectifPatientsDepistesParAge), array_sum($effectifPatientsDepistesParAge), $effectifPatientsDepistesParAge, $listeDesAges);
 	}
 	
 	
 	protected function nbJours($debut, $fin) {
 		$jourSecondes = 60*60*24;
 		$debut_ts = strtotime($debut);
 		$fin_ts = strtotime($fin);
 		$diff = $fin_ts - $debut_ts;
 		
 		return (int)($diff/$jourSecondes);
 	}
 	
 	
 	
 	/**
 	 * Répartition suivant les adresses des nouveaux dépistés
 	 */
 	public function getRepartitionPatientDepistesParAdresses(){
 		$adapter = $this->tableGateway->getAdapter();
 		$sql = new Sql($adapter);
 		$select = $sql->select();
 		$select->from(array('p' => 'patient'));
 		$select->join(array('pers' => 'personne') ,'pers.idpersonne = p.idpersonne');
 		$select->join(array('d' => 'depistage') ,'d.idpatient = p.idpersonne');
 		$select->join(array('th' => 'typage_hemoglobine') ,'th.idtypage = d.typage');
 		$select->join(array('fda' => 'facturation_demande_analyse') , 'fda.iddemande_analyse = d.iddemande_analyse' , array('*'));
 		$select->join(array('bp' => 'bilan_prelevement') , 'bp.idfacturation = fda.idfacturation' , array('date_prelevement'));
 	
 		$select->where(array('date_prelevement >=?' => '2017-04-25', 'date_prelevement <=?' => '2018-04-24'));
 		$select->group('d.idpatient');
 		$resultat = $sql->prepareStatementForSqlObject($select)->execute();
 	
 		$listeAdressesPatientsDepistes = array();
 		$diffAdressesPatientsDepistes = array();
 		foreach ($resultat as $result){
 			$adresse = $result['adresse'];
 			
 			($adresse) ? $listeAdressesPatientsDepistes [] = $adresse: null;
 			
 			if(!in_array($adresse, $diffAdressesPatientsDepistes)){
 				($adresse) ? $diffAdressesPatientsDepistes[] = $adresse: null;
 			}
 		}
 	
 		$effectifPatientsDepistesParAdresse = array_count_values($listeAdressesPatientsDepistes);
 			
 		return array($diffAdressesPatientsDepistes, $effectifPatientsDepistesParAdresse);
 	}
 	
 	
 	
}