<?php
namespace Consultation\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;

class AntecedentsPersScolariteTable {

	protected $tableGateway;
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}

	public function getAntecedentsPersScolarite(){
		return $this->tableGateway->select(function (Select $select){})->toArray();
	}
	
	function array_empty($array) {
		$is_empty = true;
		foreach($array as $k) {
			$is_empty = $is_empty && empty($k);
		}
		return $is_empty;
	}
	
	public function getAntecedentsPersScolariteParIdpatient($idpatient){
		return $this->tableGateway->select(function (Select $select) use ($idpatient){
			     $select->where(	array('idpatient' => $idpatient) );
		       })->toArray();
	}
	
	public function insertAntecedentsPersScolarite($tabDonnees, $idmedecin){
		
		$antecedentsScolariteExiste = $this->getAntecedentsPersScolariteParIdpatient($tabDonnees['idpatient']); 
		
		if($antecedentsScolariteExiste){
			$this->updateAntecedentsPersScolarite($tabDonnees, $idmedecin);
		}else{
			
			$infosAntPersScolarite = array();
		    $infosAntPersScolarite['scolariseAP']          = (! empty ( $tabDonnees['scolariseAP'] )) ? $tabDonnees['scolariseAP'] : null;
		    $infosAntPersScolarite['niveauScolariteAP']    = ( $tabDonnees['scolariseAP'] == 1 && ! empty ( $tabDonnees['niveauScolariteAP'] ) ) ? $tabDonnees['niveauScolariteAP'] : null;
		    $infosAntPersScolarite['redoublementAP']       = ( $tabDonnees['scolariseAP'] == 1 && ! empty ( $tabDonnees['redoublementAP'] ) ) ? $tabDonnees['redoublementAP'] : null;
		    $infosAntPersScolarite['nombreRedoublementAP'] = ( $tabDonnees['scolariseAP'] == 1 && $tabDonnees['redoublementAP'] == 1 && ! empty ( $tabDonnees['nombreRedoublementAP'] ) ) ? $tabDonnees['nombreRedoublementAP'] : null;
		    
		    if(!$this->array_empty($infosAntPersScolarite)){
		    	$infosAntPersScolarite['idpatient'] = $tabDonnees['idpatient'];
		    	$infosAntPersScolarite['idmedecin'] = $idmedecin;
		    	$infosAntPersScolarite['date_enregistrement'] = (new \DateTime() ) ->format('Y-m-d H:i:s');
		    	
		    	$this->tableGateway->insert($infosAntPersScolarite);
		    }
		    
		}
		
	}
	
	
	public function updateAntecedentsPersScolarite($tabDonnees, $idmedecin){
	
		$infosAntPersScolarite = array();
		$infosAntPersScolarite['scolariseAP']          = (! empty ( $tabDonnees['scolariseAP'] )) ? $tabDonnees['scolariseAP'] : null;
		$infosAntPersScolarite['niveauScolariteAP']    = ( $tabDonnees['scolariseAP'] == 1 && ! empty ( $tabDonnees['niveauScolariteAP'] ) ) ? $tabDonnees['niveauScolariteAP'] : null;
		$infosAntPersScolarite['redoublementAP']       = ( $tabDonnees['scolariseAP'] == 1 && ! empty ( $tabDonnees['redoublementAP'] ) ) ? $tabDonnees['redoublementAP'] : null;
		$infosAntPersScolarite['nombreRedoublementAP'] = ( $tabDonnees['scolariseAP'] == 1 && $tabDonnees['redoublementAP'] == 1 && ! empty ( $tabDonnees['nombreRedoublementAP'] ) ) ? $tabDonnees['nombreRedoublementAP'] : null;
		$infosAntPersScolarite['idmedecin'] = $idmedecin;
		
		$this->tableGateway->update($infosAntPersScolarite, array('idpatient' => $tabDonnees['idpatient']));			
	}
	
	
	
}

