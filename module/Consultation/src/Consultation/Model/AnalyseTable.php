<?php
namespace Consultation\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Consultation\View\Helper\DateHelper;

class AnalyseTable {

	protected $tableGateway;
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}
	
	public function getInfosPatient($idpatient){
		
		$sql = new Sql($this->tableGateway->getAdapter());
		$sQuery = $sql->select()
		->from(array('p' => 'patient'))->columns(array('*'))
		->join(array('pers' => 'personne'), 'pers.idpersonne = p.idpersonne' , array('date_naissance'))
		->where(array('p.idpersonne' => $idpatient));
		$infosPatient = $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
		
		return $infosPatient;
	}
	
	/**
	 * -----------------------------------------------------------------------------------
	 * PARTIE GESTION DES ANALYSES BIOLOGIQUES --- PARTIE GESTION DES ANALYSES BIOLOGIQUES
	 * PARTIE GESTION DES ANALYSES BIOLOGIQUES --- PARTIE GESTION DES ANALYSES BIOLOGIQUES
	 * -----------------------------------------------------------------------------------
	 */
	
	public function getAnalysesObligatoiresDemandeesParPatient($idpatient){
		
		$sql = new Sql($this->tableGateway->getAdapter());
		$sQuery = $sql->select()
		->from(array('a' => 'analyse'))->columns(array('designation'))
		->join(array('da' => 'demande_analyse'), 'da.idanalyse = a.idanalyse' , array('dateDemande' => 'date', 'heureDemande' => 'time'))
		->join(array('laopi' => 'liste_analyse_obligatoire_patient_interne'), 'laopi.idanalyse = da.idanalyse', array('*') )
		->where(array('da.idpatient' => $idpatient))
		->order(array('da.idanalyse' => 'ASC', 'dateDemande' => 'DESC'));
		$listeAnalysesDemandees = $sql->prepareStatementForSqlObject($sQuery)->execute();
		
		return $listeAnalysesDemandees;
	}
	
	/**
	 * Liste des analyses à faire obligatoirement par un patient interne
	 */
	public function getListeAnalyseAFaire(){
		return array(
				1  => array(1, 'HEMOGRAMME', '5000'),
				9  => array(1, 'TAUX DE RETICULOCYTES (TR)', '2000'),
				22 => array(2, 'CREATININEMIE', '2500'),
				40 => array(2, 'FER SERIQUE', '5000'),
				41 => array(2, 'FERRITININE', '10000'),
				44 => array(2, 'ELECTROPHORESE DE HEMOGLOBINE', '15000'),
				49 => array(2, 'PROTEINURIE DES 24H (PU 24H)', '15000'),
				69 => array(2, 'MICRO-ALBUMINURIE', '0'),
				70 => array(2, 'LDH', '0'),
		);
	}
	
	/**
	 * LISTE INDEX DES ANALYSES OBLIGATOIRES
	 * @param
	 */
	public function getListeIndexAnalyseObligatoire(){
		$sql = new Sql($this->tableGateway->getAdapter());
		$sQuery = $sql->select()
		->from(array('a' => 'analyse'))->columns(array('designation', 'idtype_analyse', 'tarif'))
		->join(array('laopi' => 'liste_analyse_obligatoire_patient_interne'), 'laopi.idanalyse = a.idanalyse', array('*') );
		$listeToutesAnalysesObligatoires = $sql->prepareStatementForSqlObject($sQuery)->execute();
	
		$tabIndexAnalysesObligatoires = array();
	
		foreach ($listeToutesAnalysesObligatoires as $liste){
			$tabIndexAnalysesObligatoires [] = $liste['idanalyse'];
		}
	
		return $tabIndexAnalysesObligatoires;
	}
	
	/**
	 * LISTE DES ANALYSES OBLIGATOIRE
	 * @param 
	 */
	public function getListeAnalyseObligatoire(){
		$sql = new Sql($this->tableGateway->getAdapter());
		$sQuery = $sql->select()
		->from(array('a' => 'analyse'))->columns(array('designation', 'idtype_analyse', 'tarif'))
		->join(array('laopi' => 'liste_analyse_obligatoire_patient_interne'), 'laopi.idanalyse = a.idanalyse', array('*') );
		$listeToutesAnalysesObligatoires = $sql->prepareStatementForSqlObject($sQuery)->execute();
		
		$tabInfosAnalysesObligatoires = array();
		
		foreach ($listeToutesAnalysesObligatoires as $liste){
			$tabInfosAnalysesObligatoires [$liste['idanalyse']] = array();
			
			$tabInfosAnalysesObligatoires [$liste['idanalyse']][] = $liste['idtype_analyse'];
			$tabInfosAnalysesObligatoires [$liste['idanalyse']][] = $liste['designation'];
			$tabInfosAnalysesObligatoires [$liste['idanalyse']][] = $liste['tarif'];
		}
		
		return $tabInfosAnalysesObligatoires;
	}
	
	/**
	 * LES ANALYSES BIOLOGIQUES A FAIRE EN URGENCE
	 */
	
	public function getAnalysesAFaireEnUrgence($idpatient){
		
		$infosPatient = $this->getInfosPatient($idpatient);
		
		/* 1°°°)
		 * Les analyses demandées parmis les obligatoires
		 * Les analyses demandées parmis les obligatoires
		 */
		$listeAnalysesDemandees = $this->getAnalysesObligatoiresDemandeesParPatient($idpatient);
		
		$tabIndexAnalysesDemandees = array();
		$tabIndexProchaineDateAlerter = array();
		$tabIndexAnalysesDemandeesProchaineDate = array();
		$tabAnalyseUrgentFaireAlerter = array();
		$tabDateAnalyseUrgentFaireAlerter = array();
		
		foreach ($listeAnalysesDemandees as $liste){
				
			$idanalyse = $liste['idanalyse'];
				
			if(!in_array($idanalyse, $tabIndexAnalysesDemandees)){
		
				$tabIndexAnalysesDemandees [] = $idanalyse;
				$age = $this->Age((new DateHelper())->convertDate($infosPatient['date_naissance']));
		
				//Recuperer les prochaines dates
				$nbJours = 0;
				$incAnalyseTabProchaine = 0;
				$dateProchaine = '0000-00-00';
		
				//Chaque 3mois dès la naissance
				if ($idanalyse == 1 || $idanalyse == 9 ){
					$nbJours = 91;
					$incAnalyseTabProchaine = 1;
					$dateProchaine = $this->getDateProchaine($liste['dateDemande'], $nbJours);
				}
		
				//Chaque annee dès la naissance
				elseif ($idanalyse == 40 || $idanalyse == 41 || $idanalyse == 44 || $idanalyse == 70){
					$nbJours = 365;
					$incAnalyseTabProchaine = 1;
					$dateProchaine = $this->getDateProchaine($liste['dateDemande'], $nbJours);
				}
		
				//Chaque annee à partir 5 ans
				elseif (($idanalyse == 22 || $idanalyse == 49 || $idanalyse == 69) && $age >= 5 ){
						
					//Date après 5 ans
					$dateApresCinqAns = $this->getDateProchaine($infosPatient['date_naissance'], 1825);
						
					if($liste['dateDemande'] >= $dateApresCinqAns){
						$nbJours = 365;
						$incAnalyseTabProchaine = 1;
						$dateProchaine = $this->getDateProchaine($liste['dateDemande'], $nbJours);
					}else{
						$nbJours = 1825;
						$incAnalyseTabProchaine = 1;
						$dateProchaine = $this->getDateProchaine($infosPatient['date_naissance'], $nbJours);
					}
		
				}
		
		
				if($incAnalyseTabProchaine == 1){
					$dateActuellement = (new \DateTime ( "now" ))->format ( 'Y-m-d' );
					$tabIndexAnalysesDemandeesProchaineDate [] = $idanalyse;
					
					if($dateActuellement >= $dateProchaine){ 
						$tabIndexProchaineDateAlerter[] = $idanalyse;
						$tabAnalyseUrgentFaireAlerter[$idanalyse] = 1; // 1= alerte rouge
						$tabDateAnalyseUrgentFaireAlerter[$idanalyse] = $dateProchaine;
					}
				}
			
			}
		
		}
		
		/* 2°°°)
		 * Les analyses non demandées parmis les obligatoires
		 * Les analyses non demandées parmis les obligatoires
		*/
		$tabListeIndexAnalysesObligatoires = $this->getListeIndexAnalyseObligatoire();
		//var_dump($tabIndexAnalysesDemandeesProchaineDate); exit();
		
		for($i=0 ; $i<count($tabListeIndexAnalysesObligatoires); $i++){
			$idanalyse = $tabListeIndexAnalysesObligatoires[$i];
			if(!in_array($tabListeIndexAnalysesObligatoires[$i], $tabIndexAnalysesDemandeesProchaineDate)){
				
				//Recuperer les prochaines dates
				$nbJours = 0;
				if     ($idanalyse == 1 ){ $nbJours = 91;  } //Chaque 3mois dès la naissance
				elseif ($idanalyse == 9 ){ $nbJours = 91;  } //Chaque 3mois dès la naissance
				elseif ($idanalyse == 40){ $nbJours = 365; } //Chaque annee dès la naissance
				elseif ($idanalyse == 41){ $nbJours = 365; } //Chaque annee dès la naissance
				elseif ($idanalyse == 44){ $nbJours = 365; } //Chaque annee dès la naissance
				elseif ($idanalyse == 70){ $nbJours = 365; } //Chaque annee dès la naissance
				elseif ($idanalyse == 22){ $nbJours = 1825;} //Chaque annee à partir 5 ans
				elseif ($idanalyse == 49){ $nbJours = 1825;} //Chaque annee à partir 5 ans
				elseif ($idanalyse == 69){ $nbJours = 1825;} //Chaque annee à partir 5 ans
		
				$dateActuellement = (new \DateTime ( "now" ))->format ( 'Y-m-d' );
				$dateProchaine = $this->getDateProchaine($infosPatient['date_naissance'], $nbJours);
				
				if(($dateProchaine > $dateActuellement) && ((new DateHelper())->getDateProchaine($dateActuellement, 30) >= $dateProchaine)){
					$tabIndexProchaineDateAlerter[] = $idanalyse;
					$tabAnalyseUrgentFaireAlerter[$idanalyse] = 0; //0 = alerte orange
					$tabDateAnalyseUrgentFaireAlerter[$idanalyse] = $dateProchaine;
				}
				elseif($dateProchaine <= $dateActuellement){
					$tabIndexProchaineDateAlerter[] = $idanalyse;
					$tabAnalyseUrgentFaireAlerter[$idanalyse] = 1; //1 = alerte rouge
					$tabDateAnalyseUrgentFaireAlerter[$idanalyse] = $dateProchaine;
				}
				
			}
		}
		
		
		
		return array($tabIndexProchaineDateAlerter, $tabAnalyseUrgentFaireAlerter, $tabDateAnalyseUrgentFaireAlerter);
		
	}
	
	/**
	 * LES ANALYSES BIOLOGIQUES EFFECTUEES ET A FAIRE
	 * @param unknown $idpatient
	 */
	public function getAnalysesBiologiquesEffectuees($idpatient){
		
		/* 1°°°)
		 * Liste de toutes les analyses obligatoires
		 * Liste de toutes les analyses obligatoires
		 */
		$sql = new Sql($this->tableGateway->getAdapter());
		$sQuery = $sql->select()
		->from(array('a' => 'analyse'))->columns(array('designation'))
		->join(array('laopi' => 'liste_analyse_obligatoire_patient_interne'), 'laopi.idanalyse = a.idanalyse', array('*') );
		$listeToutesAnalysesObligatoires = $sql->prepareStatementForSqlObject($sQuery)->execute();
		
		$tabToutesAnalysesObligatoires = array();
		$tabToutesDesignationAnalysesObligatoires = array();
		foreach ($listeToutesAnalysesObligatoires as $liste){
			$idanalyse = $liste['idanalyse'];
			if(!in_array($idanalyse, $tabToutesAnalysesObligatoires)){
				$tabToutesAnalysesObligatoires [] = $idanalyse;
				$tabToutesDesignationAnalysesObligatoires [$idanalyse] = $liste['designation'];
			}
		}
		
		/* 2°°°)
		 * Liste des analyses effectuées
 		 * Liste des analyses effectuées
		 */
		$listeAnalysesEffectuees = $this->tableGateway->select(function (Select $select) use ($idpatient){
 			$select->join('demande_analyse' , 'demande_analyse.idanalyse = analyse.idanalyse' , array('*'));
 			$select->where(array('demande_analyse.idpatient' => $idpatient));
 			$select->group('analyse.idanalyse');
 		})->toArray();
 		
 		$tabIndexAnalysesFaites = array();
 		$tabAnalysesFaites = array();
 		for($i = 0 ; $i < count($listeAnalysesEffectuees) ; $i++){
 			$idanalyse = $listeAnalysesEffectuees[$i]['idanalyse'];
 			if(in_array($idanalyse, $tabToutesAnalysesObligatoires)){
 				$tabIndexAnalysesFaites[] = $listeAnalysesEffectuees[$i]['idanalyse'];
 				$tabAnalysesFaites[] = $listeAnalysesEffectuees[$i]['designation'];
 			}
 		}
 		
 		
 		/* 3°°°)
 		 * Recuperer les analyses non faites par le patient
 		 * Recuperer les analyses non faites par le patient
 		 */
 		$tabIndexAnalysesAFaireEnUrgence = $this->getAnalysesAFaireEnUrgence($idpatient)[0];
 		
 		$tabTypesAnalysesNonFaites = array();
 		$tabAnalysesNonFaits = array();
 		$tabTarifAnalysesNonFaites = array();
 		for($i = 0 ; $i < count($tabIndexAnalysesAFaireEnUrgence) ; $i++){
 			$tabTypesAnalysesNonFaites[] = $this->getListeAnalyseObligatoire()[$tabIndexAnalysesAFaireEnUrgence[$i]][0];
 			$tabAnalysesNonFaits[] = $this->getListeAnalyseObligatoire()[$tabIndexAnalysesAFaireEnUrgence[$i]][1];
 			$tabTarifAnalysesNonFaites[] = $this->getListeAnalyseObligatoire()[$tabIndexAnalysesAFaireEnUrgence[$i]][2]; 
 		}
 		
 		$tabInfosCompletesAnalysesAFaireEnUrgence = $this->getAnalysesAFaireEnUrgence($idpatient);

 		//var_dump($tabInfosCompletesAnalysesAFaireEnUrgence); exit();
 	
 		
 		return array($tabIndexAnalysesFaites, $tabAnalysesFaites, 
 				     $tabIndexAnalysesAFaireEnUrgence, $tabAnalysesNonFaits, 
 				     $tabTypesAnalysesNonFaites, $tabTarifAnalysesNonFaites, $tabInfosCompletesAnalysesAFaireEnUrgence);
	}
	
	
	/**
	 * 
	 * GESTION DU PROGRAMME DES ANALYSES OBLIGATOIRES 
	 * @param $idpatient
	 */
	public function getProgrammeAnalysesObligatoiresEffectuees($idpatient){
		
		
		/* 0°°°)
		 * Les informations du patient
		 */
		$sql = new Sql($this->tableGateway->getAdapter());
		$sQuery = $sql->select()
		->from(array('p' => 'patient'))->columns(array('*'))
		->join(array('pers' => 'personne'), 'pers.idpersonne = p.idpersonne' , array('date_naissance'))
		->where(array('p.idpersonne' => $idpatient));
		$infosPatient = $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
		
		
		
		/* 1°°°)
		 * Liste de toutes les analyses obligatoires
		 * Liste de toutes les analyses obligatoires
		 */
		$sql = new Sql($this->tableGateway->getAdapter());
		$sQuery = $sql->select()
		->from(array('a' => 'analyse'))->columns(array('designation'))
		->join(array('laopi' => 'liste_analyse_obligatoire_patient_interne'), 'laopi.idanalyse = a.idanalyse', array('*') );
		$listeToutesAnalysesObligatoires = $sql->prepareStatementForSqlObject($sQuery)->execute();
		
		$tabToutesAnalysesObligatoires = array();
		$tabToutesDesignationAnalysesObligatoires = array();
		foreach ($listeToutesAnalysesObligatoires as $liste){
			$idanalyse = $liste['idanalyse'];
			if(!in_array($idanalyse, $tabToutesAnalysesObligatoires)){
				$tabToutesAnalysesObligatoires [] = $idanalyse;
				$tabToutesDesignationAnalysesObligatoires [$idanalyse] = $liste['designation'];
			}
		}

		
		
		/* 2°°°)
		 * Liste de toutes les analyses demandées 
		 * Liste de toutes les analyses demandées 
		 */
		$sql = new Sql($this->tableGateway->getAdapter());
		$sQuery = $sql->select()
		->from(array('a' => 'analyse'))->columns(array('designation'))
		->join(array('da' => 'demande_analyse'), 'da.idanalyse = a.idanalyse' , array('dateDemande' => 'date', 'heureDemande' => 'time'))
		->join(array('laopi' => 'liste_analyse_obligatoire_patient_interne'), 'laopi.idanalyse = da.idanalyse', array('*') )
		->where(array('da.idpatient' => $idpatient))
		->order(array('da.idanalyse' => 'ASC', 'dateDemande' => 'DESC'));
		$listeAnalysesDemandees = $sql->prepareStatementForSqlObject($sQuery)->execute();
		
		$tabToutesAnalysesDemandees = array();
		$tabNbFoisAnalyseDemandees = array();
		$tabDateDemandeAnalyses = array();
		$tabIndexAnalysesDemandees = array();
		$tabProchaineDateAnalysesDemandeesAyantResultat = array();
		$tabIndexAnalysesDemandeesProchaineDate = array();
		
		foreach ($listeAnalysesDemandees as $liste){
			
			//Gérer les nombres de fois 
			$idanalyse = $liste['idanalyse'];
			$tabToutesAnalysesDemandees [] = $idanalyse;
			
			//Gérer les dates des demandes
			if(!in_array($idanalyse, $tabIndexAnalysesDemandees)){
				$idanalyseTamp = $idanalyse;
				$tabIndexAnalysesDemandees[] = $idanalyseTamp;
				$tabDateDemandeAnalyses [$idanalyseTamp] = array();
				
				$age = $this->Age((new DateHelper())->convertDate($infosPatient['date_naissance']));
				
				//Recuperer les prochaines dates
				$nbJours = 0;
				$incAnalyseTabProchaine = 0;
				$dateProchaine = '0000-00-00';
				
				//Chaque 3mois dès la naissance
				if ($idanalyse == 1 || $idanalyse == 9 ){ 
					$nbJours = 91; 
					$incAnalyseTabProchaine = 1; 
					$dateProchaine = $this->getDateProchaine($liste['dateDemande'], $nbJours); 
				} 
				
				//Chaque annee dès la naissance
				elseif ($idanalyse == 40 || $idanalyse == 41 || $idanalyse == 44 || $idanalyse == 70){ 
					$nbJours = 365; 
					$incAnalyseTabProchaine = 1; 
					$dateProchaine = $this->getDateProchaine($liste['dateDemande'], $nbJours); 
				} 
				
				//Chaque annee à partir 5 ans
				elseif (($idanalyse == 22 || $idanalyse == 49 || $idanalyse == 69) && $age >= 5 ){ 
					
					//Date après 5 ans 
					$dateApresCinqAns = $this->getDateProchaine($infosPatient['date_naissance'], 1825);
					
					if($liste['dateDemande'] >= $dateApresCinqAns){
						$nbJours = 365;
						$incAnalyseTabProchaine = 1;
						$dateProchaine = $this->getDateProchaine($liste['dateDemande'], $nbJours);
					}else{
						$nbJours = 1825;
						$incAnalyseTabProchaine = 1;
						$dateProchaine = $this->getDateProchaine($infosPatient['date_naissance'], $nbJours);
					}

				} 

				
				if($incAnalyseTabProchaine == 1){
					$tabIndexAnalysesDemandeesProchaineDate [] = $idanalyse;
					$tabProchaineDateAnalysesDemandeesAyantResultat[$idanalyse] = $dateProchaine;
				}

				
			}
			$tabDateDemandeAnalyses [$idanalyseTamp] [] = $liste['dateDemande'];
			
		}
		
		$tabNbFoisAnalyseDemandees = array_count_values($tabToutesAnalysesDemandees);
		
		
		/* 3°°°)
		 * Les analyses non demandées parmis les obligatoires
 		 * Les analyses non demandées parmis les obligatoires
		 */
		for($i=0 ; $i<count($tabToutesAnalysesObligatoires); $i++){
			$idanalyse = $tabToutesAnalysesObligatoires[$i];
			if(!in_array($tabToutesAnalysesObligatoires[$i], $tabIndexAnalysesDemandeesProchaineDate)){
				
				//Recuperer les prochaines dates
				$nbJours = 0;
				if     ($idanalyse == 1 ){ $nbJours = 91;  } //Chaque 3mois dès la naissance
				elseif ($idanalyse == 9 ){ $nbJours = 91;  } //Chaque 3mois dès la naissance
				elseif ($idanalyse == 40){ $nbJours = 365; } //Chaque annee dès la naissance
				elseif ($idanalyse == 41){ $nbJours = 365; } //Chaque annee dès la naissance
				elseif ($idanalyse == 44){ $nbJours = 365; } //Chaque annee dès la naissance
				elseif ($idanalyse == 70){ $nbJours = 365; } //Chaque annee dès la naissance
				elseif ($idanalyse == 22){ $nbJours = 1825;} //Chaque annee à partir 5 ans
				elseif ($idanalyse == 49){ $nbJours = 1825;} //Chaque annee à partir 5 ans
				elseif ($idanalyse == 69){ $nbJours = 1825;} //Chaque annee à partir 5 ans
				
				$tabProchaineDateAnalysesDemandeesAyantResultat[$idanalyse] = $this->getDateProchaine($infosPatient['date_naissance'], $nbJours);
				
			}
		}
		

		
		/* 4°°°)
		 * Liste des analyses demandées ayant deja des résutlats
		 * Liste des analyses demandées ayant deja des résutlats
		 */
		$sql = new Sql($this->tableGateway->getAdapter());
		$sQuery = $sql->select()
		->from(array('a' => 'analyse'))->columns(array('designation'))
		->join(array('da' => 'demande_analyse'), 'da.idanalyse = a.idanalyse' , array('idDemande' => 'iddemande', 'dateDemande' => 'date', 'heureDemande' => 'time'))
		->join(array('laopi' => 'liste_analyse_obligatoire_patient_interne'), 'laopi.idanalyse = da.idanalyse', array('*') )
		->join(array('rda' => 'resultat_demande_analyse'), 'rda.iddemande_analyse = da.iddemande', array('*') )
		->where(array('da.idpatient' => $idpatient))
		->order(array('da.idanalyse' => 'ASC', 'dateDemande' => 'DESC'));
		$listeAnalysesDemandeesAyantResultat = $sql->prepareStatementForSqlObject($sQuery)->execute();
		
		$tabAnalysesDemandeesAyantResultat = array();
		$tabIndexAnalysesDemandeesResultat = array();
		$tabDatesAnalysesDemandeesAyantResultat = array();
		$tabIdDemandeAnalysesDemandeesAyantResultat = array();
		
		foreach ($listeAnalysesDemandeesAyantResultat as $liste){

			//Gerer les nombres de fois
			$idanalyse = $liste['idanalyse'];
			$tabAnalysesDemandeesAyantResultat [] = $idanalyse;
			
			//Gérer les dates des demandes ayant des resultats
			if(!in_array($idanalyse, $tabIndexAnalysesDemandeesResultat)){
				$idanalyseTamp = $idanalyse;
				$tabIndexAnalysesDemandeesResultat[] = $idanalyseTamp;
				$tabDatesAnalysesDemandeesAyantResultat [$idanalyseTamp] = array();
			}
			$tabDatesAnalysesDemandeesAyantResultat [$idanalyseTamp] [] = $liste['dateDemande'];
			$tabIdDemandeAnalysesDemandeesAyantResultat [$idanalyseTamp] [] = $liste['idDemande'];
		}
		$tabNbFoisAnalyseDemandeesAyantResultat = array_count_values($tabAnalysesDemandeesAyantResultat);
		
		
		
		return array($tabToutesAnalysesObligatoires, $tabToutesDesignationAnalysesObligatoires, 
				     $tabNbFoisAnalyseDemandees, $tabDateDemandeAnalyses,
		             $tabNbFoisAnalyseDemandeesAyantResultat, $tabDatesAnalysesDemandeesAyantResultat, $tabIdDemandeAnalysesDemandeesAyantResultat,
				     $tabProchaineDateAnalysesDemandeesAyantResultat
		);
		
	}
	
	function getDateProchaine($date,$jour){ // la date doit avoir cette format 'Y-m-d'
		list($y,$m,$d)= explode("-",$date);
		$date = mktime(0,0,0,$m,$d+$jour,$y);
		$date = gmdate("Y-m-d", $date);
		return $date;
	}
	
	function Age($date_naissance){
		$am = explode('/', $date_naissance);
		$an = explode('/', date('d/m/Y'));
		if(($am[1] < $an[1]) || (($am[1] == $an[1]) && ($am[0] <= $an[0]))) return $an[2] - $am[2];
		return $an[2] - $am[2] - 1;
	}
	
	
	
	
	
	/**
	 * ---------------------------------------------------------------------------------------
	 * PARTIE GESTION DES ANALYSES RADIOLOGIQUES --- PARTIE GESTION DES ANALYSES RADIOLOGIQUES
	 * PARTIE GESTION DES ANALYSES RADIOLOGIQUES --- PARTIE GESTION DES ANALYSES RADIOLOGIQUES
	 * ---------------------------------------------------------------------------------------
	 */
	public function getListeAnalysesRadiosObligatoires(){
		$sql = new Sql($this->tableGateway->getAdapter());
		$sQuery = $sql->select()
		->from(array('ei' => 'examen_imagerie'))->columns(array('idexamen', 'designation'))
		->join(array('leopi' => 'liste_examen_obligatoire_patient_interne'), 'leopi.idexamen = ei.idexamen', array('*'));
		$listeToutesAnalysesObligatoires = $sql->prepareStatementForSqlObject($sQuery)->execute();
	
		$tabInfosAnalysesObligatoires = array();
	
		foreach ($listeToutesAnalysesObligatoires as $liste){
			$tabInfosAnalysesObligatoires [$liste['idexamen']] = array();
				
			$tabInfosAnalysesObligatoires [$liste['idexamen']][] = $liste['idexamen'];
			$tabInfosAnalysesObligatoires [$liste['idexamen']][] = $liste['designation'];
			$tabInfosAnalysesObligatoires [$liste['idexamen']][] = 0;
		}
	
		return $tabInfosAnalysesObligatoires;
	}
	
	public function getListeIndexAnalysesRadiologiquesObligatoires(){
		$sql = new Sql($this->tableGateway->getAdapter());
		$sQuery = $sql->select()
		->from(array('ei' => 'examen_imagerie'))->columns(array('idexamen', 'designation'))
		->join(array('leopi' => 'liste_examen_obligatoire_patient_interne'), 'leopi.idexamen = ei.idexamen', array('*'));
		$listeToutesAnalysesRadioObligatoires = $sql->prepareStatementForSqlObject($sQuery)->execute();
	
		$tabIndexAnalysesRadioObligatoires = array();
		foreach ($listeToutesAnalysesRadioObligatoires as $liste){
			$tabIndexAnalysesRadioObligatoires [] = $liste['idexamen'];
		}
		
		return $tabIndexAnalysesRadioObligatoires;
	}
	
	
	public function getAnalysesRadiologiquesObligatoires(){
		$sql = new Sql($this->tableGateway->getAdapter());
		$sQuery = $sql->select()
		->from(array('ei' => 'examen_imagerie'))->columns(array('idexamen', 'designation'))
		->join(array('leopi' => 'liste_examen_obligatoire_patient_interne'), 'leopi.idexamen = ei.idexamen', array('*'));
		return $sql->prepareStatementForSqlObject($sQuery)->execute();
	}
	
	public function getAnalysesRadiologiquesObligatoiresDemandeesParPatient($idpatient){
		$sql = new Sql($this->tableGateway->getAdapter());
		$sQuery = $sql->select()
		->from(array('ei' => 'examen_imagerie'))->columns(array('idexamen', 'designation'))
		->join(array('leopi' => 'liste_examen_obligatoire_patient_interne'), 'leopi.idexamen = ei.idexamen', array('*'))
		->join(array('dec' => 'demande_examen_cons'), 'dec.idexamen = leopi.idexamen', array('dateDemande' => 'date_enreg', 'heureDemande' => 'heure_enreg') )
		->where(array('dec.idpatient' => $idpatient))
		->order(array('ei.idexamen' => 'ASC', 'dateDemande' => 'DESC'));
		return $sql->prepareStatementForSqlObject($sQuery)->execute();
	}
	
	
	/**
	 * LES ANALYSES RADIOLOGIQUES A FAIRE EN URGENCE
	 */
	
	public function getAnalysesRadiologiquesAFaireEnUrgence($idpatient){
	
		$infosPatient = $this->getInfosPatient($idpatient);
	
		/* 1°°°)
		 * Les analyses demandées parmis les obligatoires
		 * Les analyses demandées parmis les obligatoires
		 */
		$listeAnalysesDemandees = $this->getAnalysesRadiologiquesObligatoiresDemandeesParPatient($idpatient);
	
		$tabIndexAnalysesRadioDemandees = array();
		$tabIndexProchaineDateAlerter = array();
		$tabIndexAnalysesRadioDemandeesProchaineDate = array();
		$tabAnalyseRadioUrgentFaireAlerter = array();
		$tabDateAnalyseRadioUrgentFaireAlerter = array();
	
		foreach ($listeAnalysesDemandees as $liste){
	
			$idexamen = $liste['idexamen'];
	
			if(!in_array($idexamen, $tabIndexAnalysesRadioDemandees)){
	
				$tabIndexAnalysesRadioDemandees [] = $idexamen;
				$age = $this->Age((new DateHelper())->convertDate($infosPatient['date_naissance']));
	
				//Recuperer les prochaines dates
				$nbJours = 0;
				$incAnalyseTabProchaine = 0;
				$dateProchaine = '0000-00-00';
	
				//Tous les ans à partir de 5 ans
				if (($idexamen == 2) && $age >= 5){
					$nbJours = 1825;
					$incAnalyseTabProchaine = 1;
					$dateProchaine = $this->getDateProchaine($liste['dateDemande'], $nbJours);
				}
				//Tous les ans à partir de 2 ans
				elseif (($idexamen == 3) && $age >= 2){
					$nbJours = 730;
					$incAnalyseTabProchaine = 1;
					$dateProchaine = $this->getDateProchaine($liste['dateDemande'], $nbJours);
				}
				//Tous les ans à partir de 10 ans
				elseif (($idexamen == 7 || $idexamen == 9) && $age >= 10){
					$nbJours = 3650;
					$incAnalyseTabProchaine = 1;
					$dateProchaine = $this->getDateProchaine($liste['dateDemande'], $nbJours);
				}
				
				
				if($incAnalyseTabProchaine == 1){
					$dateActuellement = (new \DateTime ( "now" ))->format ( 'Y-m-d' );
					$tabIndexAnalysesRadioDemandeesProchaineDate [] = $idexamen;
						
					if(($dateProchaine > $dateActuellement) && ((new DateHelper())->getDateProchaine($dateActuellement, 30) >= $dateProchaine)){
						$tabIndexProchaineDateAlerter[] = $idexamen;
						$tabAnalyseRadioUrgentFaireAlerter[$idexamen] = 0; // 0= alerte orange
						$tabDateAnalyseRadioUrgentFaireAlerter[$idexamen] = $dateProchaine;
					}
					elseif($dateProchaine <= $dateActuellement){
						$tabIndexProchaineDateAlerter[] = $idexamen;
						$tabAnalyseRadioUrgentFaireAlerter[$idexamen] = 1; // 1= alerte rouge
						$tabDateAnalyseRadioUrgentFaireAlerter[$idexamen] = $dateProchaine;
					}
				}
					
			}
	
		}
		
	
		/* 2°°°)
		 * Les analyses radiologiques non demandées parmis les obligatoires
		 * Les analyses radiologiques non demandées parmis les obligatoires
		 */
		$tabListeIndexAnalysesObligatoires = $this->getListeIndexAnalysesRadiologiquesObligatoires();
		
		for($i=0 ; $i<count($tabListeIndexAnalysesObligatoires); $i++){
			$idexamen = $tabListeIndexAnalysesObligatoires[$i];
			if(!in_array($tabListeIndexAnalysesObligatoires[$i], $tabIndexAnalysesRadioDemandeesProchaineDate)){
	
				//Recuperer les prochaines dates
				$nbJours = 0;
				if     ($idexamen == 2 ){ $nbJours = 1825; } //Tous les ans à partir de 5 ans
				elseif ($idexamen == 3 ){ $nbJours =  730; } //Tous les ans à partir de 2 ans
				elseif ($idexamen == 7 ){ $nbJours = 3650; } //Tous les ans à partir de 10 ans
				elseif ($idexamen == 9 ){ $nbJours = 3650; } //Tous les ans à partir de 10 ans
				
				$dateActuellement = (new \DateTime ( "now" ))->format ( 'Y-m-d' );
				$dateProchaine = $this->getDateProchaine($infosPatient['date_naissance'], $nbJours);
				
				if(($dateProchaine > $dateActuellement) && ((new DateHelper())->getDateProchaine($dateActuellement, 30) >= $dateProchaine)){
					$tabIndexProchaineDateAlerter[] = $idexamen;
					$tabAnalyseRadioUrgentFaireAlerter[$idexamen] = 0; // 0= alerte orange
					$tabDateAnalyseRadioUrgentFaireAlerter[$idexamen] = $dateProchaine;
				}
				elseif($dateProchaine <= $dateActuellement){
					$tabIndexProchaineDateAlerter[] = $idexamen;
					$tabAnalyseRadioUrgentFaireAlerter[$idexamen] = 1; // 1= alerte rouge
					$tabDateAnalyseRadioUrgentFaireAlerter[$idexamen] = $dateProchaine;
				}
				
			}
		}
		
		return array($tabIndexProchaineDateAlerter, $tabAnalyseRadioUrgentFaireAlerter, $tabDateAnalyseRadioUrgentFaireAlerter);
	
	}
	
	/**
	 * LES ANALYSES RADIOLOGIQUES EFFECTUEES ET A FAIRE
	 * @param unknown $idpatient
	 */
	public function getAnalysesRadiologiquesEffectuees($idpatient){
	
		/* 1°°°)
		 * Liste de toutes les analyses obligatoires
		 * Liste de toutes les analyses obligatoires
		 */
		$listeAnalysesRadiologiquesObligatoires = $this->getAnalysesRadiologiquesObligatoires();
		
		$tabAnalysesRadioObligatoires = array();
		$tabDesignationAnalysesRadioObligatoires = array();
		foreach ($listeAnalysesRadiologiquesObligatoires as $liste){
			$idexamen = $liste['idexamen'];
			if(!in_array($idexamen, $tabAnalysesRadioObligatoires)){
				$tabAnalysesRadioObligatoires [] = $idexamen;
				$tabDesignationAnalysesRadioObligatoires [$idexamen] = $liste['designation'];
			}
		}
		
		/* 2°°°)
		 * Liste des analyses radiologiques demandées
		 * Liste des analyses radiologiques demandées
		 */
		$listeAnalysesRadiologiquesEffectuees = $this->getAnalysesRadiologiquesObligatoiresDemandeesParPatient($idpatient);
			
		$tabIndexAnalysesRadiologiquesFaites = array();
		$tabAnalysesRadiologiquesFaites = array();
		foreach ($listeAnalysesRadiologiquesEffectuees as $liste){
			$idexamen = $liste['idexamen'];
			if(in_array($idexamen, $tabAnalysesRadioObligatoires)){
				$tabIndexAnalysesRadiologiquesFaites[] = $idexamen;
				$tabAnalysesRadiologiquesFaites[] = $liste['designation'];
			}
		}
			
		/* 3°°°)
		 * Recuperer les analyses radiologiques non faites par le patient
		 * Recuperer les analyses radiologiques non faites par le patient
		 */
		$tabIndexAnalysesRadioAFaireEnUrgence = $this->getAnalysesRadiologiquesAFaireEnUrgence($idpatient)[0];

		$tabTypesAnalysesNonFaites = array();
		$tabAnalysesNonFaits = array();
		$tabTarifAnalysesNonFaites = array();
		for($i = 0 ; $i < count($tabIndexAnalysesRadioAFaireEnUrgence) ; $i++){
			$tabTypesAnalysesNonFaites[] = $this->getListeAnalysesRadiosObligatoires()[$tabIndexAnalysesRadioAFaireEnUrgence[$i]][0];
			$tabAnalysesNonFaits[] = $this->getListeAnalysesRadiosObligatoires()[$tabIndexAnalysesRadioAFaireEnUrgence[$i]][1];
			$tabTarifAnalysesNonFaites[] = $this->getListeAnalysesRadiosObligatoires()[$tabIndexAnalysesRadioAFaireEnUrgence[$i]][2];
		}
			
		$tabInfosCompletesAnalysesAFaireEnUrgence = $this->getAnalysesRadiologiquesAFaireEnUrgence($idpatient);
	
		//var_dump($tabInfosCompletesAnalysesAFaireEnUrgence); exit();
		
		return array($tabIndexAnalysesRadiologiquesFaites, $tabAnalysesRadiologiquesFaites,
   			         $tabIndexAnalysesRadioAFaireEnUrgence, $tabAnalysesNonFaits,	
				     $tabTypesAnalysesNonFaites, $tabTarifAnalysesNonFaites, $tabInfosCompletesAnalysesAFaireEnUrgence);
	}
	
	
	
	
	/**
	 *
	 * GESTION DU PROGRAMME DES ANALYSES RADIOLOGIQUES
	 * @param $idpatient
	 */
	public function getProgrammeAnalysesRadiologiquesObligatoiresEffectuees($idpatient){
	
	
		/* 0°°°)
		 * Les informations du patient
		 * Les informations du patient
		 */
		$infosPatient = $this->getInfosPatient($idpatient);
	
	
		/* 1°°°)
		 * Liste de toutes les analyses obligatoires
		 * Liste de toutes les analyses obligatoires
		 */
		$listeToutesAnalysesObligatoires = $this->getAnalysesRadiologiquesObligatoires();
		
		$tabToutesAnalysesObligatoires = array();
		$tabToutesDesignationAnalysesObligatoires = array();
		foreach ($listeToutesAnalysesObligatoires as $liste){
			$idexamen = $liste['idexamen'];
			if(!in_array($idexamen, $tabToutesAnalysesObligatoires)){
				$tabToutesAnalysesObligatoires [] = $idexamen;
				$tabToutesDesignationAnalysesObligatoires [$idexamen] = $liste['designation'];
			}
		}
	
		/* 2°°°)
		 * Liste de toutes les analyses demandées
		 * Liste de toutes les analyses demandées
		 */
		$listeAnalysesDemandees = $this->getAnalysesRadiologiquesObligatoiresDemandeesParPatient($idpatient);

		
		$tabToutesAnalysesDemandees = array();
		$tabNbFoisAnalyseDemandees = array();
		$tabDateDemandeAnalyses = array();
		$tabIndexAnalysesDemandees = array();
		$tabProchaineDateAnalysesDemandeesAyantResultat = array();
		$tabIndexAnalysesDemandeesProchaineDate = array();
	
		foreach ($listeAnalysesDemandees as $liste){
				
			//Gérer les nombres de fois
			$idexamen = $liste['idexamen'];
			$tabToutesAnalysesDemandees [] = $idexamen;
			
			if(!in_array($idexamen, $tabIndexAnalysesDemandees)){
			
				$idanalyseTamp = $idexamen;
				$tabIndexAnalysesDemandees[] = $idanalyseTamp;
				$tabDateDemandeAnalyses [$idanalyseTamp] = array();
				
				$age = $this->Age((new DateHelper())->convertDate($infosPatient['date_naissance']));
			
				//Recuperer les prochaines dates
				$nbJours = 0;
				$incAnalyseTabProchaine = 0;
				$dateProchaine = '0000-00-00';
			
				//Tous les ans à partir de 5 ans, 2 ans, 10 ans et 10 ans
				if ($idexamen == 2 || $idexamen == 3 || $idexamen == 7 || $idexamen == 9){
					$nbJours = 365;
					$incAnalyseTabProchaine = 1;
					$dateProchaine = $this->getDateProchaine($liste['dateDemande'], $nbJours);
				}
			
			
				if($incAnalyseTabProchaine == 1){
					$dateActuellement = (new \DateTime ( "now" ))->format ( 'Y-m-d' );
					$tabIndexAnalysesRadioDemandeesProchaineDate [] = $idexamen;
			
					if(($dateProchaine > $dateActuellement) && ((new DateHelper())->getDateProchaine($dateActuellement, 30) >= $dateProchaine)){
						$tabIndexAnalysesDemandeesProchaineDate[] = $idexamen;
						$tabProchaineDateAnalysesDemandeesAyantResultat[$idexamen] = $dateProchaine;
					}
					elseif($dateProchaine <= $dateActuellement){
						$tabIndexAnalysesDemandeesProchaineDate[] = $idexamen;
						$tabProchaineDateAnalysesDemandeesAyantResultat[$idexamen] = $dateProchaine;
					}
				}
					
			}
			$tabDateDemandeAnalyses [$idanalyseTamp] [] = $liste['dateDemande'];
			
			
			
			
		}
	
		$tabNbFoisAnalyseDemandees = array_count_values($tabToutesAnalysesDemandees);
	
		/* 3°°°)
		 * Les analyses non demandées parmis les obligatoires
	 	 * Les analyses non demandées parmis les obligatoires
		 */
		for($i=0 ; $i<count($tabToutesAnalysesObligatoires); $i++){
			$idexamen = $tabToutesAnalysesObligatoires[$i];
			if(!in_array($tabToutesAnalysesObligatoires[$i], $tabIndexAnalysesDemandeesProchaineDate)){
			
				//Recuperer les prochaines dates
				$nbJours = 0;
				if     ($idexamen == 2 ){ $nbJours = 1825; } //Tous les ans à partir de 5 ans
				elseif ($idexamen == 3 ){ $nbJours =  730; } //Tous les ans à partir de 2 ans
				elseif ($idexamen == 7 ){ $nbJours = 3650; } //Tous les ans à partir de 10 ans
				elseif ($idexamen == 9 ){ $nbJours = 3650; } //Tous les ans à partir de 10 ans
				
				$tabProchaineDateAnalysesDemandeesAyantResultat[$idexamen] = $this->getDateProchaine($infosPatient['date_naissance'], $nbJours);
			}
			
		}
	
		
		/* 4°°°)
		 * Liste des analyses demandées ayant deja des résutlats
	 	 * Liste des analyses demandées ayant deja des résutlats
		 */
		$sql = new Sql($this->tableGateway->getAdapter());
		$sQuery = $sql->select()
		->from(array('ei' => 'examen_imagerie'))->columns(array('idexamen', 'designation'))
		->join(array('leopi' => 'liste_examen_obligatoire_patient_interne'), 'leopi.idexamen = ei.idexamen', array('*'))
		->join(array('dec' => 'demande_examen_cons'), 'dec.idexamen = leopi.idexamen', array('dateDemande' => 'date_enreg', 'heureDemande' => 'heure_enreg') )
		->join(array('rerc' => 'resultat_examen_radio_cons'), 'rerc.idcons = dec.idcons', array('*') )
		->where(array('dec.idpatient' => $idpatient, 'rerc.idexamen = dec.idexamen'))
		->order(array('ei.idexamen' => 'ASC', 'dateDemande' => 'DESC'));
		$listeAnalysesDemandeesAyantResultat = $sql->prepareStatementForSqlObject($sQuery)->execute();
		
		$tabAnalysesDemandeesAyantResultat = array();
		$tabIndexAnalysesDemandeesResultat = array();
		$tabDatesAnalysesDemandeesAyantResultat = array();
		$tabIdDemandeAnalysesDemandeesAyantResultat = array();
	
		foreach ($listeAnalysesDemandeesAyantResultat as $liste){
	
			//Gerer les nombres de fois
			$idanalyse = $liste['idexamen'];
			$tabAnalysesDemandeesAyantResultat [] = $idanalyse;
				
			//Gérer les dates des demandes ayant des resultats
			if(!in_array($idanalyse, $tabIndexAnalysesDemandeesResultat)){
				$idanalyseTamp = $idanalyse;
				$tabIndexAnalysesDemandeesResultat[] = $idanalyseTamp;
				$tabDatesAnalysesDemandeesAyantResultat [$idanalyseTamp] = array();
			}
			$tabDatesAnalysesDemandeesAyantResultat [$idanalyseTamp] [] = $liste['dateDemande'];
			//$tabIdDemandeAnalysesDemandeesAyantResultat [$idanalyseTamp] [] = $liste['idDemande'];
		}
		$tabNbFoisAnalyseDemandeesAyantResultat = array_count_values($tabAnalysesDemandeesAyantResultat);
	
		//var_dump($tabNbFoisAnalyseDemandeesAyantResultat); exit();
	
		return array($tabToutesAnalysesObligatoires, $tabToutesDesignationAnalysesObligatoires,
				$tabNbFoisAnalyseDemandees, $tabDateDemandeAnalyses,
				$tabNbFoisAnalyseDemandeesAyantResultat, $tabDatesAnalysesDemandeesAyantResultat, /*$tabIdDemandeAnalysesDemandeesAyantResultat,*/
				$tabProchaineDateAnalysesDemandeesAyantResultat
		);
	
	}
	
}

