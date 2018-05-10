<?php
namespace Consultation\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;

class ExamenTable {

	protected $tableGateway;
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}
	
	function array_empty($array) {
		$is_empty = true;
		foreach($array as $k) {
			$is_empty = $is_empty && empty($k);
		}
		return $is_empty;
	}
	
	/**
	 * Liste des examens à faire obligatoirement par un patient interne
	 */
	public function getListeExamenAFaire(){
		return array(
				1 => 'SCANNER CEREBRAL',
				2 => 'ECHOGRAPHIE ABDOMINALE',
				3 => 'ECHODOPPLER TRANSCRANIEN',
				4 => 'RADIOGRAPHIE THORAX',
				5 => 'RADIOGRAPHIE DU BASSIN',
				6 => 'ECG',
				7 => 'ECHOCARDIOGRAPHIE',
				8 => 'IRM CEREBRAL',
				9 => 'ANGIOGRAPHIE DE LA RETINE',
		);
	}
	
	public function getExamenEffectuees($idpatient){
		$listeExamensEffectuees = $this->tableGateway->select(function (Select $select) use ($idpatient){
 			$select->join('demande_examen_cons' , 'demande_examen_cons.idexamen = examen_imagerie.idexamen' , array('*'));
 			$select->where(array('demande_examen_cons.idpatient' => $idpatient));
 		})->toArray();
 		
 		/*
 		 * Recuperer les examens demandees, faites et validees faisant partie des examens ciblées
 		 * (A revoir avec PROFESSEUR)
 		 */
 		$tabExamensObligatoireAFaire = array(1,2,3,4,5,6,7,8,9);
 		$tabIndexExamens = array();
 		$tabExamens = array();
 		for($i = 0 ; $i < count($listeExamensEffectuees) ; $i++){
 			$idExamen = $listeExamensEffectuees[$i]['idexamen'];
 			if(in_array($idExamen, $tabExamensObligatoireAFaire)){
 				$tabIndexExamens[] = $listeExamensEffectuees[$i]['idexamen'];
 				$tabExamens[] = $listeExamensEffectuees[$i]['designation'];
 			}
 		}
 		
 		/*
 		 * Recuperer les Examens non faites par le patient
 		 */
 		$tabIndexExamensNonFaits = array_values(array_diff($tabExamensObligatoireAFaire, $tabIndexExamens));
 		$tabExamensNonFaits = array();
 		for($i = 0 ; $i < count($tabIndexExamensNonFaits) ; $i++){
 			$tabExamensNonFaits[] = $this->getListeExamenAFaire()[$tabIndexExamensNonFaits[$i]];
 		}
 		
 		//var_dump($tabIndexExamensNonFaits); exit();
 		
 		
 		return array($tabIndexExamens, $tabExamens, $tabIndexExamensNonFaits, $tabExamensNonFaits);
	}
	
	/*
	 * CRUD EXAMEN RADIOLOGIQUE --- CRUD EXAMEN RADIOLOGIQUE
	 */
	function getExamenRadiologiqueParIdconsIdexamen($idcons, $idexamen){
		$sql = new Sql($this->tableGateway->getAdapter());
		$sQuery = $sql->select() ->from('demande_examen_cons')->where( array('idcons' => $idcons, 'idexamen' => $idexamen) );
		return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	function getExamenRadiologique($idcons){
		$sql = new Sql($this->tableGateway->getAdapter());
		$sQuery = $sql->select() ->from('demande_examen_cons')->where( array('idcons' => $idcons) );
		return $sql->prepareStatementForSqlObject($sQuery)->execute();
	}
	
	function deleteExamenRadiologique($idcons){
		$sql = new Sql($this->tableGateway->getAdapter());
		$sQuery = $sql->delete() ->from('demande_examen_cons')->where( array('idcons' => $idcons) );
		$sql->prepareStatementForSqlObject($sQuery)->execute();
	}
	
	
	/**
	 * Inserer des demandes d'examens (radiologique) 
	 */
	public function insertExamenRadiologique($tabDonnees, $idmedecin){
		$idcons = $tabDonnees['idcons'];
		$this->deleteExamenRadiologique($idcons);
		
		
		for($i = 1 ; $i <= $tabDonnees['nbDemandeExamenComplementaire'] ; $i++){
			$examenRadiologique = array();
			
			$idTypeAnalyse = $tabDonnees['type_analyse_name_'.$i];

			if($idTypeAnalyse && $idTypeAnalyse == 6){
				
				$idexamen = explode(",", $tabDonnees['analyse_name_'.$i])[1];
				
				if($idexamen && !$this->getExamenRadiologiqueParIdconsIdexamen($idcons, $idexamen)){
					$date_enreg = (new \DateTime())->format('Y-m-d');
					$heure_enreg = (new \DateTime())->format('H:i:s');
					
					$examenRadiologique['idexamen'] = $idexamen;
					$examenRadiologique['idcons'] = $idcons;
					$examenRadiologique['idmedecin'] = $idmedecin;
					$examenRadiologique['idpatient'] = $tabDonnees['idpatient'];
					$examenRadiologique['date_enreg'] = $date_enreg;
					$examenRadiologique['heure_enreg'] = $heure_enreg;
					
					$sql = new Sql($this->tableGateway->getAdapter());
					$sQuery = $sql->insert() ->into('demande_examen_cons')->values($examenRadiologique);
					$sql->prepareStatementForSqlObject($sQuery)->execute();
				}
				
			}
		}

	}
	
	
	
	
	public function getResultatExamenRadiologique($idcons){
		$db = $this->tableGateway->getAdapter();
		$sql = new Sql($db);
		$sQuery = $sql->select()->from('resultat_examen_radio_cons')->where(array('idcons' => $idcons));
		$resultat = $sql->prepareStatementForSqlObject($sQuery)->execute();
	
		$listeResultatExamenRadio = array();
		foreach ($resultat as $result){
			$listeResultatExamenRadio[] = $result;
		}
		return $listeResultatExamenRadio;
	}
	
	
	function getResultatExamenRadioParIdconsIdexamen($idcons, $idexamen){
		$sql = new Sql($this->tableGateway->getAdapter());
		$sQuery = $sql->select() ->from('resultat_examen_radio_cons')->where( array('idcons' => $idcons, 'idexamen' => $idexamen) );
		return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	function deleteResultatExamenRadio($idcons){
		$sql = new Sql($this->tableGateway->getAdapter());
		$sQuery = $sql->delete() ->from('resultat_examen_radio_cons')->where( array('idcons' => $idcons) );
		$sql->prepareStatementForSqlObject($sQuery)->execute();
	}
	
	
	/**
	 * Inserer les résultats des examens (radiologiques)
	 */
	public function insertResultatExamenRadiologique($tabDonnees, $idmedecin){
		$idcons = $tabDonnees['idcons'];
		$this->deleteResultatExamenRadio($idcons);
	
	
		for($i = 1 ; $i <= $tabDonnees['nbDemandeExamenComplementaire'] ; $i++){
			$tabResultatExamenRadio = array();
				
			$idTypeAnalyse = $tabDonnees['type_analyse_name_'.$i];
	
			if($idTypeAnalyse && $idTypeAnalyse == 6){
	
				$idexamen = explode(",", $tabDonnees['analyse_name_'.$i])[1];
				$resultatExamenRadio = $tabDonnees['resultatExamenRadio_'.$idexamen];
				
				if($idexamen && !$this->getResultatExamenRadioParIdconsIdexamen($idcons, $idexamen)){
						
					$tabResultatExamenRadio['idexamen']  = $idexamen;
					$tabResultatExamenRadio['idcons']    = $idcons;
					$tabResultatExamenRadio['idemploye'] = $idmedecin;
					$tabResultatExamenRadio['resultatExamenRadio'] = $resultatExamenRadio;
						
					$sql = new Sql($this->tableGateway->getAdapter());
					$sQuery = $sql->insert() ->into('resultat_examen_radio_cons')->values($tabResultatExamenRadio);
					$sql->prepareStatementForSqlObject($sQuery)->execute();
				}
			
			}
		}
	
	}
	
	
	
	
	
	/*
	 * CRUD ANALYSE BIOLOGIQUE --- CRUD ANALYSE BIOLOGIQUE
	 */
	public function getDemandeAnalyseDPA($idpatient, $idanalyse){
		$aujourdhui = (new \DateTime() ) ->format('Y-m-d');
		
		$db = $this->tableGateway->getAdapter();
		$sql = new Sql($db);
		$sQuery = $sql->select()
		->from(array('d_a' => 'demande_analyse'))->columns(array('*'))
		->where(array('date' => $aujourdhui, 'idpatient' => $idpatient, 'idanalyse' => $idanalyse));
		$stat = $sql->prepareStatementForSqlObject($sQuery);
		return $stat->execute()->current();
	}
	
	function getAnalyseBiologiqueParIdconsIdanalyse($idcons, $idanalyse){
		$sql = new Sql($this->tableGateway->getAdapter());
		$sQuery = $sql->select()->from(array('dac' => 'demande_analyse_cons'))->columns(array('*'))
                	  ->join( array('da' => 'demande_analyse') , 'da.iddemande = dac.iddemande' , array('*'))
		              ->where( array('dac.idcons' => $idcons, 'da.idanalyse' => $idanalyse) );
		return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	function getAnalyseBiologique($idcons){
		$sql = new Sql($this->tableGateway->getAdapter());
		$sQuery = $sql->select() ->from('demande_analyse_cons')->where( array('idcons' => $idcons) );
		return $sql->prepareStatementForSqlObject($sQuery)->execute();
	}
	
	function getIdFacturation($iddemande){
		$sql = new Sql($this->tableGateway->getAdapter());
		$sQuery = $sql->select() ->from('facturation_demande_analyse')->where( array('iddemande_analyse' => $iddemande) );
		return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	function deleteFacturation($idfacturation){
		$sql = new Sql($this->tableGateway->getAdapter());
		$sQuery = $sql->delete() ->from('facturation')->where( array('idfacturation' => $idfacturation) );
		return $sql->prepareStatementForSqlObject($sQuery)->execute();
	}
	
	function deleteAnalyseBiologique($idcons){
		$sql = new Sql($this->tableGateway->getAdapter());
		$sQuery = $sql->select() ->from('demande_analyse_cons')->where( array('idcons' => $idcons) );
		$listeDemande = $sql->prepareStatementForSqlObject($sQuery)->execute();
		
		foreach ($listeDemande as $liste){
			$this->deleteDemandeAnalyseBiologique($liste['iddemande']);
		}
	}
	
	/**
	 * Inserer des demandes d'analyses (Examen Biologique)
	 */
	public function insertAnalyseBiologique($tabDonnees, $idmedecin){
		$idcons = $tabDonnees['idcons'];
		/* Supprimer uniquement les analyse pour lesquelles 
		 * il n y a pas encore eu de prélèvement effectué et concernant la consultation
		 */
		$this->deleteAnalyseBiologique($idcons);
	
		$listeDemandesAnalyses = array();
	
		for($i = 1 ; $i <= $tabDonnees['nbDemandeExamenComplementaire'] ; $i++){
			$analyseBiologique = array();
	
			$idTypeAnalyse = $tabDonnees['type_analyse_name_'.$i];
			
			if($idTypeAnalyse && $idTypeAnalyse != 6){
	
				$idanalyse = $tabDonnees['analyse_name_'.$i];
	
				/* 
				 * Verifier est ce que l'analyse ne fait pas déjà l'objet d'une demande lors de la consultation ou par le secrétaire
				 * sinon on l'enregistre
				 */
				if($idanalyse && !$this->getAnalyseBiologiqueParIdconsIdanalyse($idcons, $idanalyse) && !$this->getDemandeAnalyseDPA($tabDonnees['idpatient'] , $idanalyse)){
					$date = (new \DateTime())->format('Y-m-d');
					$time = (new \DateTime())->format('H:i:s');
						
					$demandeAnalyse['date'] = $date;
					$demandeAnalyse['time'] = $time;
					$demandeAnalyse['idsecretaire'] = $idmedecin;
					$demandeAnalyse['idpatient'] = $tabDonnees['idpatient'];
					$demandeAnalyse['idanalyse'] = $idanalyse;
					
					$iddemande = $this->insertDemandeAnalyse($demandeAnalyse);
					$demandeAnalyseCons = array();
					$demandeAnalyseCons['idcons'] = $idcons;
					$demandeAnalyseCons['iddemande'] = $iddemande;
					$demandeAnalyseCons['idmedecin'] = $idmedecin;
					
					$sql = new Sql($this->tableGateway->getAdapter());
					$sQuery = $sql->insert() ->into('demande_analyse_cons')->values($demandeAnalyseCons);
					$sql->prepareStatementForSqlObject($sQuery)->execute();
					
					$listeDemandesAnalyses [] = $iddemande;
				}
	
			}
		}
		
		return $listeDemandesAnalyses;
	
	}

	/**
	 * Supprimer les analyses biologiques pour lesquelles les demandes ne font pas l'objet de prelevement
	 */
	public function deleteDemandeAnalyseBiologique($iddemande){
		
		/*
		 * Verifier si la demande fait l'objet de prélèvement
		 */
		$sql = new Sql($this->tableGateway->getAdapter());
		$sQuery = $sql->select()->from(array('fda' => 'facturation_demande_analyse'))->columns(array('*'))
		                        ->join(array('bp' => 'bilan_prelevement') , 'bp.idfacturation = fda.idfacturation' , array('*'))
		                        ->where(array('fda.iddemande_analyse' => $iddemande) );
		$resultat = $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
		
		if(!$resultat){
			/**
			 * Si la demande ne fait pas l'objet de prelevement donc toutes les demandes faisant partie
			 * de la même facture peuvent être supprimer
			 * On supprime la facture
			 */
			$resultat = $this->getIdFacturation($iddemande);
			$this->deleteFacturation($resultat['idfacturation']);
			
			/*
			 * Supprimer cette demande si elle ne fait pas l'objet de prevelement
			*/
			$sql = new Sql($this->tableGateway->getAdapter());
			$sQuery = $sql->delete() ->from('demande_analyse')->where( array('iddemande' => $iddemande) );
			$sql->prepareStatementForSqlObject($sQuery)->execute();
			
			
		}
		
	}
	
	/**
	 * Inserer des demandes d'analyses biologiques dans la table 'demande_analyse'
	 */
	public function insertDemandeAnalyse($demandeAnalyse){
		
		$sql = new Sql($this->tableGateway->getAdapter());
		$sQuery = $sql->insert() ->into('demande_analyse')->values($demandeAnalyse);
		return $sql->prepareStatementForSqlObject($sQuery)->execute()->getGeneratedValue();
	}
	
	
	public function getListeDesExamens(){
		$adapter = $this->tableGateway->getAdapter ();
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from(array('ei'=>'examen_imagerie'));
		$select->columns(array('*'));
		$select->order('idexamen ASC');
		return $sql->prepareStatementForSqlObject($select)->execute();
	}
}
