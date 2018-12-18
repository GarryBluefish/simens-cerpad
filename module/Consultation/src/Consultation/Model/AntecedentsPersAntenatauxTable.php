<?php
namespace Consultation\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;

class AntecedentsPersAntenatauxTable {

	protected $tableGateway;
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}

	public function getAntecedentsPersAntenataux(){
		return $this->tableGateway->select(function (Select $select){})->toArray();
	}
	
	function array_empty($array) {
		$is_empty = true;
		foreach($array as $k) {
			$is_empty = $is_empty && empty($k);
		}
		return $is_empty;
	}
	
	public function getAntecedentsPersAntenatauxParIdpatient($idpatient){
		return $this->tableGateway->select(function (Select $select) use ($idpatient){
			     $select->where(	array('idpatient' => $idpatient) );
		       })->toArray();
	}
	
	public function insertAntecedentsPersAntenataux($tabDonnees, $idmedecin){
		
		$antecedentsAntenatauxExiste = $this->getAntecedentsPersAntenatauxParIdpatient($tabDonnees['idpatient']); 
		
		if($antecedentsAntenatauxExiste){
			$this->updateAntecedentsPersAntenataux($tabDonnees, $idmedecin);
		}else{
			
			$infosAntPersAntenataux = array();
			$infosAntPersAntenataux['nbFoetusAP'] = $tabDonnees['nbFoetusAP'];
			$infosAntPersAntenataux['deroulementAP'] = $tabDonnees['deroulementAP'];
		    $infosAntPersAntenataux['precisonDeroulementAP'] = (( $tabDonnees['deroulementAP'] )==2) ? $tabDonnees['precisonDeroulementAP'] : null;
			
		    if(!$this->array_empty($infosAntPersAntenataux)){
		    	$infosAntPersAntenataux['idpatient'] = $tabDonnees['idpatient'];
		    	$infosAntPersAntenataux['idmedecin'] = $idmedecin;
		    	$infosAntPersAntenataux['date_enregistrement'] = (new \DateTime() ) ->format('Y-m-d H:i:s');
		    	
		    	$this->tableGateway->insert($infosAntPersAntenataux);
		    }
		    
		}
		
	}
	
	
	public function updateAntecedentsPersAntenataux($tabDonnees, $idmedecin){
	
		$infosAntPersAntenataux = array(); 
		$infosAntPersAntenataux['nbFoetusAP'] = $tabDonnees['nbFoetusAP'];
		$infosAntPersAntenataux['deroulementAP'] = $tabDonnees['deroulementAP'];
		$infosAntPersAntenataux['precisonDeroulementAP'] = (( $tabDonnees['deroulementAP'] ) == 2) ? $tabDonnees['precisonDeroulementAP'] : null;
		$infosAntPersAntenataux['idmedecin'] = $idmedecin;

		$this->tableGateway->update($infosAntPersAntenataux, array('idpatient' => $tabDonnees['idpatient']));			
	}
	
	
}

