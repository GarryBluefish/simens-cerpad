<?php
namespace Consultation\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;

class AntecedentsPersPerinatauxTable {

	protected $tableGateway;
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}

	public function getAntecedentsPersPerinataux(){
		return $this->tableGateway->select(function (Select $select){})->toArray();
	}
	
	function array_empty($array) {
		$is_empty = true;
		foreach($array as $k) {
			$is_empty = $is_empty && empty($k);
		}
		return $is_empty;
	}
	
	public function getAntecedentsPersPerinatauxParIdpatient($idpatient){
		return $this->tableGateway->select(function (Select $select) use ($idpatient){
			     $select->where(	array('idpatient' => $idpatient) );
		       })->toArray();
	}
	
	public function insertAntecedentsPersPerinataux($tabDonnees, $idmedecin){
		
		$antecedentsPerinatauxExiste = $this->getAntecedentsPersPerinatauxParIdpatient($tabDonnees['idpatient']); 
		
		if($antecedentsPerinatauxExiste){
			$this->updateAntecedentsPersPerinataux($tabDonnees, $idmedecin);
		}else{
			
			$infosAntPersPerinataux = array();
		    $infosAntPersPerinataux['voieBasseAP'] =  (! empty ( $tabDonnees['voieBasseAP'] )) ? $tabDonnees['voieBasseAP'] : null;
		    $infosAntPersPerinataux['cesarienneAP'] = (! empty ( $tabDonnees['cesarienneAP'] )) ? $tabDonnees['cesarienneAP'] : null;
		    $infosAntPersPerinataux['motifCesarienneAP'] = (( $tabDonnees['cesarienneAP'] ) == 1 && ! empty ( $tabDonnees['motifCesarienneAP'] ) ) ? $tabDonnees['motifCesarienneAP'] : null;
		    $infosAntPersPerinataux['manoeuvreObstetricaleAP'] = (! empty ( $tabDonnees['manoeuvreObstetricaleAP'] )) ? $tabDonnees['manoeuvreObstetricaleAP'] : null;
		    $infosAntPersPerinataux['motifManoeuvreObstetricaleAP'] = (( $tabDonnees['manoeuvreObstetricaleAP'] ) == 1 && ! empty ( $tabDonnees['motifManoeuvreObstetricaleAP'] )) ? $tabDonnees['motifManoeuvreObstetricaleAP'] : null;
		    $infosAntPersPerinataux['ageGestationnelATermeAP'] = (! empty ( $tabDonnees['ageGestationnelATermeAP'] )) ? $tabDonnees['ageGestationnelATermeAP'] : null;
		    $infosAntPersPerinataux['precisonAgeGestationnelATermeAP'] = (( $tabDonnees['ageGestationnelATermeAP'] ) == -1 && ! empty ( $tabDonnees['precisonAgeGestationnelATermeAP'] )) ? $tabDonnees['precisonAgeGestationnelATermeAP'] : null;
		    $infosAntPersPerinataux['precisonValeurAgeGestationnelAP'] = (( $tabDonnees['ageGestationnelATermeAP'] ) == -1 && ! empty ( $tabDonnees['precisonValeurAgeGestationnelAP'] )) ? $tabDonnees['precisonValeurAgeGestationnelAP'] : null;
		    $infosAntPersPerinataux['souffranceFoetaleAigueAP'] =  (! empty ( $tabDonnees['souffranceFoetaleAigueAP'] )) ? $tabDonnees['souffranceFoetaleAigueAP'] : null;
		    $infosAntPersPerinataux['precisonReanimationSouffranceFoetaleAP'] =  (( $tabDonnees['souffranceFoetaleAigueAP'] ) == 1 && ! empty ( $tabDonnees['precisonReanimationSouffranceFoetaleAP'] )) ? $tabDonnees['precisonReanimationSouffranceFoetaleAP'] : null;
		    $infosAntPersPerinataux['pathologieNeonataleAP'] =  (! empty ( $tabDonnees['pathologieNeonataleAP'] )) ? $tabDonnees['pathologieNeonataleAP'] : null;
		     
		    if(!$this->array_empty($infosAntPersPerinataux)){
		    	$infosAntPersPerinataux['idpatient'] = $tabDonnees['idpatient'];
		    	$infosAntPersPerinataux['idmedecin'] = $idmedecin;
		    	$infosAntPersPerinataux['date_enregistrement'] = (new \DateTime() ) ->format('Y-m-d H:i:s');
		    	
		    	$this->tableGateway->insert($infosAntPersPerinataux);
		    }
		    
		}
		
	}
	
	
	public function updateAntecedentsPersPerinataux($tabDonnees, $idmedecin){
	
		$infosAntPersPerinataux = array();
		$infosAntPersPerinataux['voieBasseAP'] =  (! empty ( $tabDonnees['voieBasseAP'] )) ? $tabDonnees['voieBasseAP'] : null;
		$infosAntPersPerinataux['cesarienneAP'] = (! empty ( $tabDonnees['cesarienneAP'] )) ? $tabDonnees['cesarienneAP'] : null;
		$infosAntPersPerinataux['motifCesarienneAP'] = (( $tabDonnees['cesarienneAP'] ) == 1 && ! empty ( $tabDonnees['motifCesarienneAP'] ) ) ? $tabDonnees['motifCesarienneAP'] : null;
		$infosAntPersPerinataux['manoeuvreObstetricaleAP'] = (! empty ( $tabDonnees['manoeuvreObstetricaleAP'] )) ? $tabDonnees['manoeuvreObstetricaleAP'] : null;
		$infosAntPersPerinataux['motifManoeuvreObstetricaleAP'] = (( $tabDonnees['manoeuvreObstetricaleAP'] ) == 1 && ! empty ( $tabDonnees['motifManoeuvreObstetricaleAP'] )) ? $tabDonnees['motifManoeuvreObstetricaleAP'] : null;
		$infosAntPersPerinataux['ageGestationnelATermeAP'] = (! empty ( $tabDonnees['ageGestationnelATermeAP'] )) ? $tabDonnees['ageGestationnelATermeAP'] : null;
		$infosAntPersPerinataux['precisonAgeGestationnelATermeAP'] = (( $tabDonnees['ageGestationnelATermeAP'] ) == -1 && ! empty ( $tabDonnees['precisonAgeGestationnelATermeAP'] )) ? $tabDonnees['precisonAgeGestationnelATermeAP'] : null;
		$infosAntPersPerinataux['precisonValeurAgeGestationnelAP'] = (( $tabDonnees['ageGestationnelATermeAP'] ) == -1 && ! empty ( $tabDonnees['precisonValeurAgeGestationnelAP'] )) ? $tabDonnees['precisonValeurAgeGestationnelAP'] : null;
		$infosAntPersPerinataux['souffranceFoetaleAigueAP'] =  (! empty ( $tabDonnees['souffranceFoetaleAigueAP'] )) ? $tabDonnees['souffranceFoetaleAigueAP'] : null;
		$infosAntPersPerinataux['precisonReanimationSouffranceFoetaleAP'] =  (( $tabDonnees['souffranceFoetaleAigueAP'] ) == 1 && ! empty ( $tabDonnees['precisonReanimationSouffranceFoetaleAP'] )) ? $tabDonnees['precisonReanimationSouffranceFoetaleAP'] : null;
		$infosAntPersPerinataux['pathologieNeonataleAP'] =  (! empty ( $tabDonnees['pathologieNeonataleAP'] )) ? $tabDonnees['pathologieNeonataleAP'] : null;
		$infosAntPersPerinataux['idmedecin'] = $idmedecin;
		
		$this->tableGateway->update($infosAntPersPerinataux, array('idpatient' => $tabDonnees['idpatient']));			
	}
	
	
	
}

