<?php
namespace Consultation\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;

class HistoireMaladieTable {

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
	 * Indiquer que la consultation est effectuée par le médecin
	 */
	function validerConsultation($idcons, $idmedecin){
		$sql = new Sql($this->tableGateway->getAdapter());
		$sQuery = $sql->update()->table('consultation')->set(array('idmedecin' => $idmedecin , 'consprise' => 1))
		              ->where(array('idcons' => $idcons ));
		$sql->prepareStatementForSqlObject($sQuery)->execute();
	}
	
	function getHistoireMaladie($idcons){
		return $this->tableGateway->select( array('idcons' => $idcons) )->toArray();
	}
	
	function deleteHistoireMaladie($idcons){
		$this->tableGateway->delete( array('idcons' => $idcons) );
	}
	
	function insertCriseVasoOcclusiveHm($tabDonnees, $idmedecin){
		$criseVasoOcclusiveHm = array();
		$criseVasoOcclusiveHm['idcons'] =  (! empty ( $tabDonnees['idcons'] )) ? $tabDonnees['idcons'] : null ;
		$criseVasoOcclusiveHm['nombre_criseHM'] = (! empty ( $tabDonnees['nombre_criseHM'] )) ? $tabDonnees['nombre_criseHM'] : null ;
		$criseVasoOcclusiveHm['typeHM'] = (! empty ( $tabDonnees['typeHM'] )) ? $tabDonnees['typeHM'] : null ;
		$criseVasoOcclusiveHm['dureeHM'] = (! empty ( $tabDonnees['dureeHM'] )) ? $tabDonnees['dureeHM'] : null ;
		$criseVasoOcclusiveHm['facteur_declenchantHM'] = (! empty ( $tabDonnees['facteur_declenchantHM'] )) ? $tabDonnees['facteur_declenchantHM'] : null ;
		$criseVasoOcclusiveHm['idmedecin'] = $idmedecin;
		
		if($criseVasoOcclusiveHm['idcons']){
			$sql = new Sql($this->tableGateway->getAdapter());
			$sQuery = $sql->insert() ->into('crise_vaso_occlusive_hm')->values($criseVasoOcclusiveHm);
			$sql->prepareStatementForSqlObject($sQuery)->execute();
			
			//Ajout de la liste des crises vasocclusives s'il y en a
			//Ajout de la liste des crises vasocclusives s'il y en a
			$nombreCriseHM = $tabDonnees['nombre_criseHM'];
			if($nombreCriseHM > 1){
				$tabInfosListCrisesHMType = explode(',', $tabDonnees['tabInfosListCrisesHMType']);
				$tabInfosListCrisesHMDuree = explode(',', $tabDonnees['tabInfosListCrisesHMDuree']);
				$tabInfosListCrisesHMFacteurDeclenchant = explode(',', $tabDonnees['tabInfosListCrisesHMFacteurDeclenchant']);
				
				for($i=1 ; $i<count($tabInfosListCrisesHMType) ; $i++){
					if($tabInfosListCrisesHMType[$i]){
						$criseVasOcclusive = array(
								'idcons'  => $tabDonnees['idcons'],
								'typeHM'  => $tabInfosListCrisesHMType[$i],
								'dureeHM' => $tabInfosListCrisesHMDuree[$i],
								'facteur_declenchantHM' => $tabInfosListCrisesHMFacteurDeclenchant[$i],
						);
						
						$sql = new Sql($this->tableGateway->getAdapter());
						$sQuery = $sql->insert() ->into('crise_vaso_occlusive_liste_hm')->values($criseVasOcclusive);
						$sql->prepareStatementForSqlObject($sQuery)->execute();
					}
				}
				
			}
			 
		}

	}
	
	function insertEpisodeFievreHm($tabDonnees, $idmedecin){
		$episodeFievreHm = array();
		$episodeFievreHm['idcons'] = $tabDonnees['idcons'];
		$episodeFievreHm['episodeFievreSiOuiHM'] = (! empty ( $tabDonnees['episodeFievreSiOuiHM'] )) ? $tabDonnees['episodeFievreSiOuiHM'] : null ;
		$episodeFievreHm['idmedecin'] = $idmedecin;
		
		if($episodeFievreHm['idcons']){
			$sql = new Sql($this->tableGateway->getAdapter());
			$sQuery = $sql->insert() ->into('episode_fievre_hm')->values($episodeFievreHm);
			$sql->prepareStatementForSqlObject($sQuery)->execute();
		}
	}
	
	function insertHospitalisationHm($tabDonnees, $idmedecin){
		$hospitalisationHm = array();
		$hospitalisationHm['idcons'] = $tabDonnees['idcons'];
		$hospitalisationHm['nombreHospitalisationHM'] = (! empty ( $tabDonnees['nombreHospitalisationHM'] )) ? $tabDonnees['nombreHospitalisationHM'] : null ;
		$hospitalisationHm['dateHospitalisationHM'] = (! empty ( $tabDonnees['dateHospitalisationHM'] )) ? $tabDonnees['dateHospitalisationHM'] : null ;
		$hospitalisationHm['dureeHospitalisationHM'] = (! empty ( $tabDonnees['dureeHospitalisationHM'] )) ? $tabDonnees['dureeHospitalisationHM'] : null ;
		$hospitalisationHm['motifHospitalisationHM'] = (! empty ( $tabDonnees['motifHospitalisationHM'] )) ? $tabDonnees['motifHospitalisationHM'] : null ;
		$hospitalisationHm['idmedecin'] = $idmedecin;
		
		if($hospitalisationHm['idcons']){
			$sql = new Sql($this->tableGateway->getAdapter());
			$sQuery = $sql->insert() ->into('hospitalisation_hm')->values($hospitalisationHm);
			$sql->prepareStatementForSqlObject($sQuery)->execute();
				
			//Ajout de la liste des autres hospitalisations s'il y en a
			//Ajout de la liste des autres hospitalisations s'il y en a
			$nombreHospitalisationHM = $hospitalisationHm['nombreHospitalisationHM'];
			if($nombreHospitalisationHM >= 1){
				$tabInfosListAutresHospHMDate  = explode(',', $tabDonnees['tabInfosListAutresHospHMDate']);
				$tabInfosListAutresHospHMDuree = explode(',', $tabDonnees['tabInfosListAutresHospHMDuree']);
				$tabInfosListAutresHospHMMotif = explode(',', $tabDonnees['tabInfosListAutresHospHMMotif']);
				$tabInfosListAutresHospHMPriseEnCharge = explode(',', $tabDonnees['tabInfosListAutresHospHMPriseEnCharge']);
				$tabInfosListAutresHospHMNombre = explode(',', $tabDonnees['tabInfosListAutresHospHMNombrePerfusion']);
		
				for($i=1 ; $i<count($tabInfosListAutresHospHMDate) ; $i++){
					if($tabInfosListAutresHospHMDate[$i]){
						$autresHospitalisationsHm = array(
								'idcons'  => $tabDonnees['idcons'],
								'dateHospitalisationHM'  => $tabInfosListAutresHospHMDate[$i],
								'dureeHospitalisationHM' => $tabInfosListAutresHospHMDuree[$i],
								'motifHospitalisationHM' => $tabInfosListAutresHospHMMotif[$i],
								'priseEnChargeHospitalisationHM' => $tabInfosListAutresHospHMPriseEnCharge[$i],
								'nombrePerfusionHospitalisationHM' => (! empty ( $tabInfosListAutresHospHMNombre[$i] )) ? $tabInfosListAutresHospHMNombre[$i] : null ,
						);
						
						$sql = new Sql($this->tableGateway->getAdapter());
						$sQuery = $sql->insert() ->into('hospitalisation_liste_hm')->values($autresHospitalisationsHm);
						$sql->prepareStatementForSqlObject($sQuery)->execute();
						
					}
				}
		
			}
		
		}
		
	}
	
	/**
	 * Evènements depuis la dernière consultation
	 * @param $tabDonnees
	 * @param $idmedecin
	 */
	function insertHistoireMaladie($tabDonnees, $idmedecin){
		
		$this->deleteHistoireMaladie($tabDonnees['idcons']);
		
		$histoireMaladie = array();
		$histoireMaladie['criseHM'] = $tabDonnees['criseHM'];
		$histoireMaladie['episodeFievreHM'] = $tabDonnees['episodeFievreHM'];
		$histoireMaladie['hospitalisationHM'] = $tabDonnees['hospitalisationHM'];
		
		if(!$this->array_empty($histoireMaladie)){
			$histoireMaladie['idcons'] = $tabDonnees['idcons'];
			$histoireMaladie['idmedecin'] = $idmedecin;
			$this->tableGateway->insert($histoireMaladie);
			
			$this->validerConsultation($tabDonnees['idcons'], $idmedecin);
			
			//Inserer les infos sur les crises vaso-occlusive
			if($histoireMaladie['criseHM'] == 1){ $this->insertCriseVasoOcclusiveHm($tabDonnees, $idmedecin); }

			//Inserer les infos sur les episodes de fievre
			if($histoireMaladie['episodeFievreHM'] == 1){ $this->insertEpisodeFievreHm($tabDonnees, $idmedecin); }

			//Inserer les infos sur les hospitalisations
			if($histoireMaladie['hospitalisationHM'] == 1){ $this->insertHospitalisationHm($tabDonnees, $idmedecin); }
		}
		
	}
	
	function getCriseVasoOcclusiveHm($idcons){
		$sql = new Sql($this->tableGateway->getAdapter());
		$sQuery = $sql->select() ->from('crise_vaso_occlusive_hm')->where( array('idcons' => $idcons) );
		return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}

	function getEpisodeFievreHm($idcons){
		$sql = new Sql($this->tableGateway->getAdapter());
		$sQuery = $sql->select() ->from('episode_fievre_hm')->where( array('idcons' => $idcons) );
		return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	function getHospitalisationHm($idcons){
		$sql = new Sql($this->tableGateway->getAdapter());
		$sQuery = $sql->select() ->from('hospitalisation_hm')->where( array('idcons' => $idcons) );
		return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	function getCriseVasoOcclusiveListeHm($idcons){
		$sql = new Sql($this->tableGateway->getAdapter());
		$sQuery = $sql->select() ->from('crise_vaso_occlusive_liste_hm')->where( array('idcons' => $idcons) );
		return $sql->prepareStatementForSqlObject($sQuery)->execute();
	}
	
	function getHospitalisationListeHm($idcons){
		$sql = new Sql($this->tableGateway->getAdapter());
		$sQuery = $sql->select() ->from('hospitalisation_liste_hm')->where( array('idcons' => $idcons) );
		return $sql->prepareStatementForSqlObject($sQuery)->execute();
	}
	
	
	//GESTION DES INTERROGATOIRE (Description des symptomes)
	//GESTION DES INTERROGATOIRE (Description des symptomes)
	//GESTION DES INTERROGATOIRE (Description des symptomes)
	function insertInterrogatoireMotif($tabDonnees, $idmedecin){

		$this->deleteInterrogatoireMotif($tabDonnees['idcons']);
		for($i = 1 ; $i <= 5 ; $i++){
			$motif_interrogatoire = $tabDonnees['motif_interrogatoire_'.$i];
			if($motif_interrogatoire){
				$interrogatoireMotif = array();
				$interrogatoireMotif['idlistemotif'] = $tabDonnees['motif_admission'.$i];
				$interrogatoireMotif['motif_interrogatoire'] = $motif_interrogatoire;
				
				$interrogatoireMotif['idcons'] = $tabDonnees['idcons'];
				$interrogatoireMotif['idmedecin'] = $idmedecin;
				
				$sql = new Sql($this->tableGateway->getAdapter());
				$sQuery = $sql->insert() ->into('motif_interrogatoire')->values($interrogatoireMotif);
				$sql->prepareStatementForSqlObject($sQuery)->execute();
			}
		}
		
	}
	
	
	function deleteInterrogatoireMotif($idcons){
		$sql = new Sql($this->tableGateway->getAdapter());
		$sQuery = $sql->delete() ->from('motif_interrogatoire')->where( array('idcons' => $idcons) );
		$sql->prepareStatementForSqlObject($sQuery)->execute();
	}
	
	function getInterrogatoireMotif($idcons){
		$sql = new Sql($this->tableGateway->getAdapter());
		$sQuery = $sql->select() ->from('motif_interrogatoire')->where( array('idcons' => $idcons) );
		return $sql->prepareStatementForSqlObject($sQuery)->execute();
	}
	
	//SUIVI DES TRAITEMENTS --- SUIVI DES TRAITEMENTS
	//SUIVI DES TRAITEMENTS --- SUIVI DES TRAITEMENTS
	//SUIVI DES TRAITEMENTS --- SUIVI DES TRAITEMENTS
	function insertSuiviDesTraitements($tabDonnees, $idmedecin){
		$this->deleteSuiviDesTraitements($tabDonnees['idcons']);
		
		$suiviTraitement = array();
		$suiviTraitement['suiviDesTraitements'] = $tabDonnees['suiviDesTraitements'];

		if(!$this->array_empty($suiviTraitement)){
			$suiviTraitement['suiviDesTraitementsPrecision'] = $tabDonnees['suiviDesTraitementsPrecision'];
			$suiviTraitement['idcons'] = $tabDonnees['idcons'];
			$suiviTraitement['idmedecin'] = $idmedecin;
			
			$sql = new Sql($this->tableGateway->getAdapter());
			$sQuery = $sql->insert() ->into('suivi_traitement')->values($suiviTraitement);
			$sql->prepareStatementForSqlObject($sQuery)->execute();
		}
	}
	
	function deleteSuiviDesTraitements($idcons){
		$sql = new Sql($this->tableGateway->getAdapter());
		$sQuery = $sql->delete() ->from('suivi_traitement')->where( array('idcons' => $idcons) );
		$sql->prepareStatementForSqlObject($sQuery)->execute();
	}
	
	function getSuiviDesTraitements($idcons){
		$sql = new Sql($this->tableGateway->getAdapter());
		$sQuery = $sql->select() ->from('suivi_traitement')->where( array('idcons' => $idcons) );
		return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	//MISE A JOUR DES VACCIN --- MISE A JOUR DES VACCINS
	//MISE A JOUR DES VACCIN --- MISE A JOUR DES VACCINS
	//MISE A JOUR DES VACCIN --- MISE A JOUR DES VACCINS
	function insertMiseAJourVaccin($tabDonnees, $idmedecin){
		$this->deleteMiseAJourVaccin($tabDonnees['idcons']);
	
		$miseAJourVaccin = array();
		$miseAJourVaccin['misesAJourDesVaccins'] = $tabDonnees['misesAJourDesVaccins'];
	
		if(!$this->array_empty($miseAJourVaccin)){
			$miseAJourVaccin['misesAJourDesVaccinsPrecision'] = $tabDonnees['misesAJourDesVaccinsPrecision'];
			$miseAJourVaccin['idcons'] = $tabDonnees['idcons'];
			$miseAJourVaccin['idmedecin'] = $idmedecin;
				
			$sql = new Sql($this->tableGateway->getAdapter());
			$sQuery = $sql->insert() ->into('mise_a_jour_vaccin')->values($miseAJourVaccin);
			$sql->prepareStatementForSqlObject($sQuery)->execute();
		}
	}
	
	function deleteMiseAJourVaccin($idcons){
		$sql = new Sql($this->tableGateway->getAdapter());
		$sQuery = $sql->delete() ->from('mise_a_jour_vaccin')->where( array('idcons' => $idcons) );
		$sql->prepareStatementForSqlObject($sQuery)->execute();
	}
	
	function getMiseAJourVaccin($idcons){
		$sql = new Sql($this->tableGateway->getAdapter());
		$sQuery = $sql->select() ->from('mise_a_jour_vaccin')->where( array('idcons' => $idcons) );
		return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	
}

