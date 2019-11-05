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
 		
 		
 		$select->join(array('fda' => 'facturation_demande_analyse') , 'fda.iddemande_analyse = d.iddemande_analyse' , array('*'));
 		$select->join(array('bp' => 'bilan_prelevement') , 'bp.idfacturation = fda.idfacturation' , array('date_prelevement'));
 		
 		
 		//$select->where(array('sexe' => 'Féminin', 'date_prelevement >=?' => '2017-04-25', 'date_prelevement <=?' => '2019-02-26'));
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
 		
 		$select->join(array('fda' => 'facturation_demande_analyse') , 'fda.iddemande_analyse = d.iddemande_analyse' , array('*'));
 		$select->join(array('bp' => 'bilan_prelevement') , 'bp.idfacturation = fda.idfacturation' , array('date_prelevement'));
 		
 		//$select->where(array('sexe' => 'Masculin', 'date_prelevement >=?' => '2017-04-25', 'date_prelevement <=?' => '2019-02-26'));
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
 		
 		//$select->where(array('parent' => 'pere', 'ethnie  != ?' => '', 'date_prelevement >=?' => '2017-04-25', 'date_prelevement <=?' => '2019-02-26'));
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

 		
 		//$select->where(array('date_prelevement >=?' => '2017-04-25', 'date_prelevement <=?' => '2019-02-26'));
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
 			
 		//$select->where(array( 'ethnie  != ?' => '', 'date_prelevement >=?' => '2017-04-25', 'date_prelevement <=?' => '2019-02-26'));
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
 	 * Les professions rencontr�es chez les m�res
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
 			
 		//$select->where(array('parent' => 'mere', 'ethnie  != ?' => '', 'date_prelevement >=?' => '2017-04-25', 'date_prelevement <=?' => '2019-02-26'));
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
 	 * Les professions rencontr�es chez les p�res
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
 	
 		//$select->where(array('parent' => 'pere', 'ethnie  != ?' => '', 'date_prelevement >=?' => '2017-04-25', 'date_prelevement <=?' => '2019-02-26'));
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
 	 * Les nouveaux d�pist�s de j=0 � j=8
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
 	
 		//$select->where(array('date_prelevement >=?' => '2017-04-25', 'date_prelevement <=?' => '2019-02-26'));
 		$select->group('d.idpatient');
 		$resultat = $sql->prepareStatementForSqlObject($select)->execute();
 	
 		$listeAgesPatients = array();
 		$tabAgesRecup = array();
 		foreach ($resultat as $result){
 			$date_naissance = $result['date_naissance'];
 			$date_prelevement = $result['date_prelevement'];
 			
 			$ageJour = $this->nbJours($date_naissance, $date_prelevement);
 			if($ageJour >= 0 && $ageJour <= 1000){
 				$listeAgesPatients [] = $ageJour;
 				if(!in_array($ageJour, $tabAgesRecup)){ $tabAgesRecup[] = $ageJour; }
 			}
 		}
 	
 		$effectifPatientsDepistesParAge = array_count_values($listeAgesPatients); 
 		ksort($effectifPatientsDepistesParAge);
 		sort($tabAgesRecup);
 		
 		//var_dump($tabAgesRecup); exit();

 		$listeDesAges = $tabAgesRecup; //array_values(array_flip($effectifPatientsDepistesParAge));
 		
 		
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
 	 * R�partition suivant les adresses des nouveaux d�pist�s
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
 	
 		//$select->where(array('date_prelevement >=?' => '2017-04-25', 'date_prelevement <=?' => '2019-02-26'));
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
 	
 	
 	/**
 	 * R�partition des differentes analyses par patients Externes ou Internes
 	 */
 	public function getRepartitionAnalysesParPatient($typepatient=0){
 	    $adapter = $this->tableGateway->getAdapter();
 	    $sql = new Sql($adapter);
 	    $select = $sql->select();
 	    $select->from(array('p' => 'patient'));
 	    $select->join(array('pers' => 'personne') ,'pers.idpersonne = p.idpersonne');
 	    $select->join(array('d' => 'depistage') ,'d.idpatient = p.idpersonne');
 	    $select->join(array('da' => 'demande_analyse') ,'da.idpatient = p.idpersonne');
 	    $select->join(array('a' => 'analyse') ,'a.idanalyse = da.idanalyse', array('designation'));
 	    
 	    $select->where(array('typepatient' => $typepatient));
 	    
 	    $resultat = $sql->prepareStatementForSqlObject($select)->execute();
 	
 	    $listeAnalysesPatientsExternes = array();
 	    $diffAnalysesPatientsExternes = array();
 	    foreach ($resultat as $result){
 	        $designation = $result['designation'];
 	
 	        ($designation) ? $listeAnalysesPatientsExternes [] = $designation: null;
 	
 	        if(!in_array($designation, $diffAnalysesPatientsExternes)){
 	            ($designation) ? $diffAnalysesPatientsExternes[] = $designation: null;
 	        }
 	    }
 	    $effectifAnalysesPatientsExternes = array_count_values($listeAnalysesPatientsExternes);
 	
 	    return array($diffAnalysesPatientsExternes, $effectifAnalysesPatientsExternes);
 	}
 	
 	
 	/**
 	 * R�partition des differentes analyses par patients Externes et Internes
 	 */
 	public function getRepartitionAnalysesParPatientsDepistes(){
 	    $adapter = $this->tableGateway->getAdapter();
 	    $sql = new Sql($adapter);
 	    $select = $sql->select();
 	    $select->from(array('p' => 'patient'));
 	    $select->join(array('pers' => 'personne') ,'pers.idpersonne = p.idpersonne');
 	    $select->join(array('d' => 'depistage') ,'d.idpatient = p.idpersonne');
 	    $select->join(array('da' => 'demande_analyse') ,'da.idpatient = p.idpersonne');
 	    $select->join(array('a' => 'analyse') ,'a.idanalyse = da.idanalyse', array('designation'));
 	
 	    $resultat = $sql->prepareStatementForSqlObject($select)->execute();
 	
 	    $listeAnalysesPatientsExternes = array();
 	    $diffAnalysesPatientsExternes = array();
 	    foreach ($resultat as $result){
 	        $designation = $result['designation'];
 	
 	        ($designation) ? $listeAnalysesPatientsExternes [] = $designation: null;
 	
 	        if(!in_array($designation, $diffAnalysesPatientsExternes)){
 	            ($designation) ? $diffAnalysesPatientsExternes[] = $designation: null;
 	        }
 	    }
 	    $effectifAnalysesPatientsExternes = array_count_values($listeAnalysesPatientsExternes);
 	
 	    return array($diffAnalysesPatientsExternes, $effectifAnalysesPatientsExternes);
 	}
 	
 	
 	
 	/**
 	 * R�partition des differentes analyses par parent de patients Internes
 	 */
 	public function getRepartitionAnalysesParParentsPatients(){
 	    $adapter = $this->tableGateway->getAdapter();
 	    $sql = new Sql($adapter);
 	    $select = $sql->select();
 	    $select->from(array('pa' => 'parent'));
 	    $select->join(array('p' => 'patient'), 'p.idpersonne = pa.idpatient');
 	    $select->join(array('pers' => 'personne') ,'pers.idpersonne = p.idpersonne');
 	    $select->join(array('d' => 'depistage') ,'d.idpatient = p.idpersonne');
 	    $select->join(array('da' => 'demande_analyse') ,'da.idpatient = p.idpersonne');
 	    $select->join(array('a' => 'analyse') ,'a.idanalyse = da.idanalyse', array('designation'));
 	
 	    $select->where(array('typepatient' => 1));
 	
 	    $resultat = $sql->prepareStatementForSqlObject($select)->execute();
 	
 	    $listeAnalysesPatientsExternes = array();
 	    $diffAnalysesPatientsExternes = array();
 	    foreach ($resultat as $result){
 	        $designation = $result['designation'];
 	
 	        ($designation) ? $listeAnalysesPatientsExternes [] = $designation: null;
 	
 	        if(!in_array($designation, $diffAnalysesPatientsExternes)){
 	            ($designation) ? $diffAnalysesPatientsExternes[] = $designation: null;
 	        }
 	    }
 	    $effectifAnalysesPatientsExternes = array_count_values($listeAnalysesPatientsExternes);
 	
 	    return array($diffAnalysesPatientsExternes, $effectifAnalysesPatientsExternes);
 	}
 	
}


