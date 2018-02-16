<?php
namespace Consultation\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;

class DiagnosticConsultationTable {

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
	
	function getDiagnosticConsultation($idcons){
		return $this->tableGateway->select( array('idcons' => $idcons) )->toArray();
	}
	
	function deleteDiagnosticConsultation($idcons){
		$this->tableGateway->delete( array('idcons' => $idcons) );
	}
	
	function insertDiagnosticConsultation($tabDonnees, $idmedecin){
		
		$this->deleteDiagnosticConsultation($tabDonnees['idcons']);
		
		$diagnosticConsultation = array();
		$diagnosticConsultation['diagnosticDuJourConsultation'] = $tabDonnees['diagnosticDuJourConsultation'];
		
		if(!$this->array_empty($diagnosticConsultation)){
			$diagnosticConsultation['idcons'] = $tabDonnees['idcons'];
			$diagnosticConsultation['idmedecin'] = $idmedecin;
			$this->tableGateway->insert($diagnosticConsultation);
		}
		
	}
	
	
	//COMPLICATION AIGUES --- COMPLICATIONS AIGUES
	//COMPLICATION AIGUES --- COMPLICATIONS AIGUES
	//COMPLICATION AIGUES --- COMPLICATIONS AIGUES
	function insertComplicationsAigues($tabDonnees, $idmedecin){
		$this->deleteComplicationsAigues($tabDonnees['idcons']);
	
		$nbDiagnosticComplicationsAigues = $tabDonnees['nbDiagnosticComplicationsAigues'];
	
		for($i = 1 ; $i <= $nbDiagnosticComplicationsAigues ; $i++){
			
			$idcomplicationaigue = $tabDonnees['diagnosticComplicationsAiguesChamp_'.$i];
			
			if($idcomplicationaigue && !$this->getComplicationsAiguesAvecIdconsIdcomp($tabDonnees['idcons'], $idcomplicationaigue)){
				$complicationsAigues = array();
				$complicationsAigues['idcons'] = $tabDonnees['idcons'];
				$complicationsAigues['idmedecin'] = $idmedecin;
				$complicationsAigues['complication_aigue'] = $idcomplicationaigue;
				
				$sql = new Sql($this->tableGateway->getAdapter());
				$sQuery = $sql->insert() ->into('complications_aigues_consultation')->values($complicationsAigues);
				$sql->prepareStatementForSqlObject($sQuery)->execute();
			}
		}
	}
	
	function deleteComplicationsAigues($idcons){
		$sql = new Sql($this->tableGateway->getAdapter());
		$sQuery = $sql->delete() ->from('complications_aigues_consultation')->where( array('idcons' => $idcons) );
		$sql->prepareStatementForSqlObject($sQuery)->execute();
	}
	
	function getComplicationsAigues($idcons){
		$sql = new Sql($this->tableGateway->getAdapter());
		$sQuery = $sql->select() ->from('complications_aigues_consultation')->where( array('idcons' => $idcons) );
		return $sql->prepareStatementForSqlObject($sQuery)->execute();
	}
	
	function getComplicationsAiguesAvecIdconsIdcomp($idcons, $idcomplicationaigue){
		$sql = new Sql($this->tableGateway->getAdapter());
		$sQuery = $sql->select() ->from('complications_aigues_consultation')->where( array('idcons' => $idcons, 'complication_aigue' => $idcomplicationaigue) );
		return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	
	
	//COMPLICATION CHRONIQUE --- COMPLICATION CHRONIQUE
	//COMPLICATION CHRONIQUE --- COMPLICATION CHRONIQUE
	//COMPLICATION CHRONIQUE --- COMPLICATION CHRONIQUE
	function insertComplicationsChroniques($tabDonnees, $idmedecin){
		$this->deleteComplicationsChroniques($tabDonnees['idcons']);
	
		$nbDiagnosticComplicationsChroniques = $tabDonnees['nbDiagnosticComplicationsChroniques'];
	
		for($i = 1 ; $i <= $nbDiagnosticComplicationsChroniques ; $i++){
				
			$idcomplicationchronique = $tabDonnees['diagnosticComplicationsChroniquesChamp_'.$i];
				
			if($idcomplicationchronique && !$this->getComplicationsChroniquesAvecIdconsIdcomp($tabDonnees['idcons'], $idcomplicationchronique)){
				$complicationsChroniques = array();
				$complicationsChroniques['idcons'] = $tabDonnees['idcons'];
				$complicationsChroniques['idmedecin'] = $idmedecin;
				$complicationsChroniques['complication_chronique'] = $idcomplicationchronique;
	
				$sql = new Sql($this->tableGateway->getAdapter());
				$sQuery = $sql->insert() ->into('complications_chroniques_consultation')->values($complicationsChroniques);
				$sql->prepareStatementForSqlObject($sQuery)->execute();
			}
		}
	}
	
	function deleteComplicationsChroniques($idcons){
		$sql = new Sql($this->tableGateway->getAdapter());
		$sQuery = $sql->delete() ->from('complications_chroniques_consultation')->where( array('idcons' => $idcons) );
		$sql->prepareStatementForSqlObject($sQuery)->execute();
	}
	
	function getComplicationsChroniques($idcons){
		$sql = new Sql($this->tableGateway->getAdapter());
		$sQuery = $sql->select() ->from('complications_chroniques_consultation')->where( array('idcons' => $idcons) );
		return $sql->prepareStatementForSqlObject($sQuery)->execute();
	}
	
	function getComplicationsChroniquesAvecIdconsIdcomp($idcons, $idcomplicationchronique){
		$sql = new Sql($this->tableGateway->getAdapter());
		$sQuery = $sql->select() ->from('complications_chroniques_consultation')->where( array('idcons' => $idcons, 'complication_chronique' => $idcomplicationchronique) );
		return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
}

