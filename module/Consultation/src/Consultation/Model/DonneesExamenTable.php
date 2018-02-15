<?php
namespace Consultation\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;

class DonneesExamenTable {

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
	
	function getDonneesExamen($idcons){
		return $this->tableGateway->select( array('idcons' => $idcons) )->toArray();
	}
	
	function deleteDonneesExamen($idcons){
		$this->tableGateway->delete( array('idcons' => $idcons) );
	}
	
	function insertDonneesExamen($tabDonnees, $idmedecin){
		
		$this->deleteDonneesExamen($tabDonnees['idcons']);
		
		$donneesExamen = array();
		
		$donneesExamen['paleurDonneesExamen'] = $tabDonnees['paleurDonneesExamen'];
		$donneesExamen['ictereDonneesExamen'] = $tabDonnees['ictereDonneesExamen'];
		$donneesExamen['splenomegalieDonneesExamen'] = $tabDonnees['splenomegalieDonneesExamen'];
		$donneesExamen['tailleDonneesExamen'] = $tabDonnees['tailleDonneesExamen'];
		$donneesExamen['orlObstructionNasaleDonneesExamen'] = $tabDonnees['orlObstructionNasaleDonneesExamen'];
		$donneesExamen['orlRhiniteDonneesExamen'] = $tabDonnees['orlRhiniteDonneesExamen'];
		$donneesExamen['orlHypertrophieAmygdalesDonneesExamen'] = $tabDonnees['orlHypertrophieAmygdalesDonneesExamen'];
		$donneesExamen['orlAngineDonneesExamen'] = $tabDonnees['orlAngineDonneesExamen'];
		$donneesExamen['orlOtiteDonneesExamen'] = $tabDonnees['orlOtiteDonneesExamen'];
		$donneesExamen['examenDesPoumonsDonneesExamen'] = $tabDonnees['examenDesPoumonsDonneesExamen'];
		$donneesExamen['precisionExamenDesPoumonsDonneesExamen'] = $tabDonnees['precisionExamenDesPoumonsDonneesExamen'];
		$donneesExamen['examenDuCoeurDonneesExamen'] = $tabDonnees['examenDuCoeurDonneesExamen'];
		$donneesExamen['precisionExamenDuCoeurDonneesExamen'] = $tabDonnees['precisionExamenDuCoeurDonneesExamen'];
		$donneesExamen['examenDuFoieVoieBiliaireDonneesExamen'] = $tabDonnees['examenDuFoieVoieBiliaireDonneesExamen'];
		$donneesExamen['examenHancheDonneesExamen'] = $tabDonnees['examenHancheDonneesExamen'];
		$donneesExamen['examenEpauleDonneesExamen'] = $tabDonnees['examenEpauleDonneesExamen'];
		$donneesExamen['examenJambeDonneesExamen'] = $tabDonnees['examenJambeDonneesExamen'];
		$donneesExamen['autresDonneesExamen'] = $tabDonnees['autresDonneesExamen'];
		
		if(!$this->array_empty($donneesExamen)){
			$donneesExamen['idcons'] = $tabDonnees['idcons'];
			$donneesExamen['idmedecin'] = $idmedecin;
			$this->tableGateway->insert($donneesExamen);
		}
		
	}
	
	//SYNTHESE DE LA CONSULTATION -- SYNTHESE DE LA CONSULTATION
	//SYNTHESE DE LA CONSULTATION -- SYNTHESE DE LA CONSULTATION
	//SYNTHESE DE LA CONSULTATION -- SYNTHESE DE LA CONSULTATION
	function insertSyntheseConsultation($tabDonnees, $idmedecin){
		$this->deleteSyntheseConsultation($tabDonnees['idcons']);
	
		$syntheseConsultation = array();
		$syntheseConsultation['syntheseConsultationDuJourDonneesExamen'] = $tabDonnees['syntheseConsultationDuJourDonneesExamen'];
		
		if(!$this->array_empty($syntheseConsultation)){
			$syntheseConsultation['idcons'] = $tabDonnees['idcons'];
			$syntheseConsultation['idmedecin'] = $idmedecin;
			
			$sql = new Sql($this->tableGateway->getAdapter());
			$sQuery = $sql->insert() ->into('synthese_consultation')->values($syntheseConsultation);
			$sql->prepareStatementForSqlObject($sQuery)->execute();
		}
	}
	
	function deleteSyntheseConsultation($idcons){
		$sql = new Sql($this->tableGateway->getAdapter());
		$sQuery = $sql->delete() ->from('synthese_consultation')->where( array('idcons' => $idcons) );
		$sql->prepareStatementForSqlObject($sQuery)->execute();
	}
	
	function getSyntheseConsultation($idcons){
		$sql = new Sql($this->tableGateway->getAdapter());
		$sQuery = $sql->select() ->from('synthese_consultation')->where( array('idcons' => $idcons) );
		return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
}

