<?php
namespace Consultation\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;

class AntecedentsFamiliauxTable {

	protected $tableGateway;
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}

	public function getAntecedentsFamilaux(){
		return $this->tableGateway->select(function (Select $select){})->toArray();
	}
	
	function array_empty($array) {
		$is_empty = true;
		foreach($array as $k) {
			$is_empty = $is_empty && empty($k);
		}
		return $is_empty;
	}
	
	public function getAntecedentsFamilauxParIdpatient($idpatient){
		return $this->tableGateway->select(function (Select $select) use ($idpatient){
			     $select->where(	array('idpatient' => $idpatient) );
		       })->toArray();
	}
	
	public function insertAntecedentsFamiliaux($tabDonnees){
		
		$antecedentsPatientExiste = $this->getAntecedentsFamilauxParIdpatient($tabDonnees['idpatient']); 
		if($antecedentsPatientExiste){
			$this->updateAntecedentsFamiliaux($tabDonnees);
		}else{
			$infosAntFam = array();
			$infosAntFam['idpatient'] = $tabDonnees['idpatient'];
			$infosAntFam['consanguiniteAF'] = $tabDonnees['consanguiniteAF'];
			$infosAntFam['degreAF'] = $tabDonnees['degreAF'];
		    $infosAntFam['statutDrepanocytoseMereAF'] = (! empty ( $tabDonnees['statutDrepanocytoseMereAF'] )) ? $tabDonnees['statutDrepanocytoseMereAF'] : null;
		    $infosAntFam['statutDrepanocytosePereAF'] = (! empty ( $tabDonnees['statutDrepanocytosePereAF'] )) ? $tabDonnees['statutDrepanocytosePereAF'] : null;
			$infosAntFam['fratrieTailleAF'] = $tabDonnees['fratrieTailleAF'];
			$infosAntFam['fratrieTailleFilleAF'] = $tabDonnees['fratrieTailleFilleAF'];
			$infosAntFam['fratrieTailleGarconAF'] = $tabDonnees['fratrieTailleGarconAF'];
			$infosAntFam['fratrieRangAF'] = $tabDonnees['fratrieRangAF'];
			
			$this->tableGateway->insert($infosAntFam);
			
			//Autres maladies familiales
			$autresMaladiesFamiliales = array();
			$autresMaladiesFamiliales['idpatient'] = $tabDonnees['idpatient'];
			$autresMaladiesFamiliales['AllergiesAF'] = $tabDonnees['AllergiesAF'];
			$autresMaladiesFamiliales['AsthmeAF'] = $tabDonnees['AsthmeAF'];
			$autresMaladiesFamiliales['DiabeteAF'] = $tabDonnees['DiabeteAF'];
			$autresMaladiesFamiliales['HtaAF'] = $tabDonnees['HtaAF'];
			
			$this->deleteAutresMaladiesFamiliales($tabDonnees['idpatient']);
			if(!$this->array_empty($autresMaladiesFamiliales)){
				$this->insertAutresMaladiesFamiliales($autresMaladiesFamiliales);
			}
			
		}
		
	}
	
	public function updateAntecedentsFamiliaux($tabDonnees){
		
		$infosAntFam = array();
		$infosAntFam['consanguiniteAF'] = $tabDonnees['consanguiniteAF'];
		$infosAntFam['degreAF'] = $tabDonnees['degreAF'];
		$infosAntFam['statutDrepanocytoseMereAF'] = (! empty ( $tabDonnees['statutDrepanocytoseMereAF'] )) ? $tabDonnees['statutDrepanocytoseMereAF'] : null;
		$infosAntFam['statutDrepanocytosePereAF'] = (! empty ( $tabDonnees['statutDrepanocytosePereAF'] )) ? $tabDonnees['statutDrepanocytosePereAF'] : null;
		$infosAntFam['fratrieTailleAF'] = $tabDonnees['fratrieTailleAF'];
		$infosAntFam['fratrieTailleFilleAF'] = $tabDonnees['fratrieTailleFilleAF'];
		$infosAntFam['fratrieTailleGarconAF'] = $tabDonnees['fratrieTailleGarconAF'];
		$infosAntFam['fratrieRangAF'] = $tabDonnees['fratrieRangAF'];
		
		$this->tableGateway->update($infosAntFam, array('idpatient' => $tabDonnees['idpatient']));
		
		//Autres maladies familiales
		$autresMaladiesFamiliales = array();
		$autresMaladiesFamiliales['idpatient'] = $tabDonnees['idpatient'];
		$autresMaladiesFamiliales['AllergiesAF'] = $tabDonnees['AllergiesAF'];
		$autresMaladiesFamiliales['AsthmeAF'] = $tabDonnees['AsthmeAF'];
		$autresMaladiesFamiliales['DiabeteAF'] = $tabDonnees['DiabeteAF'];
		$autresMaladiesFamiliales['HtaAF'] = $tabDonnees['HtaAF'];
		
		$this->deleteAutresMaladiesFamiliales($tabDonnees['idpatient']);
		if(!$this->array_empty($autresMaladiesFamiliales)){
			$this->insertAutresMaladiesFamiliales($autresMaladiesFamiliales);
		}
	
	}
	
	public function insertAutresMaladiesFamiliales($autresMaladiesFamiliales){
		$sql = new Sql($this->tableGateway->getAdapter());
		$sQuery = $sql->insert() ->into('autres_antecedents_familiaux')->values($autresMaladiesFamiliales);
		$sql->prepareStatementForSqlObject($sQuery)->execute();
	}
	
	public function deleteAutresMaladiesFamiliales($idpatient){
		$sql = new Sql($this->tableGateway->getAdapter());
		$sQuery = $sql->delete()->from('autres_antecedents_familiaux')->where(array('idpatient' => $idpatient));
		$sql->prepareStatementForSqlObject($sQuery)->execute();
	}
	
	public function getAutresMaladiesFamiliales($idpatient){
		$sql = new Sql($this->tableGateway->getAdapter());
		$sQuery = $sql->select()->from('autres_antecedents_familiaux')->where(array('idpatient' => $idpatient));
		return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function insertStatutDrepanocytoseEnfant($tabDonnees){
		$this->deleteStatutDrepanocytoseEnfant($tabDonnees['idpatient']);
		for($i = 1 ; $i <= $tabDonnees['nbChoixStatutEnfantAF'] ; $i++){
			$donnees = array();
			$donnees['idpatient'] = $tabDonnees['idpatient'];
			$donnees['choixstatutenfant'] = $tabDonnees['choixStatutEnfant'.$i];
			$donnees['choixstatutenfantnb'] = $tabDonnees['choixStatutEnfantNb'.$i];
			
			$sql = new Sql($this->tableGateway->getAdapter());
			$sQuery = $sql->insert() ->into('status_drepanocytose_enfant')->values($donnees);
			$sql->prepareStatementForSqlObject($sQuery)->execute();
		}
	}
	
	public function deleteStatutDrepanocytoseEnfant($idpatient){
		$sql = new Sql($this->tableGateway->getAdapter());
		$sQuery = $sql->delete()->from('status_drepanocytose_enfant')->where(array('idpatient' => $idpatient));
		$sql->prepareStatementForSqlObject($sQuery)->execute();
	}
	
	public function getStatutDrepanocytoseEnfant($idpatient){
		$sql = new Sql($this->tableGateway->getAdapter());
		$sQuery = $sql->select()->from('status_drepanocytose_enfant')->where(array('idpatient' => $idpatient));
		return $sql->prepareStatementForSqlObject($sQuery)->execute();
	}
	
	
}

