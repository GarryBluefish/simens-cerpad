<?php
namespace Consultation\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;

class AntecedentsPersAlimentationTable {

	protected $tableGateway;
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}

	public function getAntecedentsPersAlimentation(){
		return $this->tableGateway->select(function (Select $select){})->toArray();
	}
	
	function array_empty($array) {
		$is_empty = true;
		foreach($array as $k) {
			$is_empty = $is_empty && empty($k);
		}
		return $is_empty;
	}
	
	public function getAntecedentsPersAlimentationParIdpatient($idpatient){
		return $this->tableGateway->select(function (Select $select) use ($idpatient){
			     $select->where(	array('idpatient' => $idpatient) );
		       })->toArray();
	}
	
	public function insertAntecedentsPersAlimentation($tabDonnees, $idmedecin){
		
		
		$antecedentsAlimentationExiste = $this->getAntecedentsPersAlimentationParIdpatient($tabDonnees['idpatient']); 
		
		if($antecedentsAlimentationExiste){
			$this->updateAntecedentsPersAlimentation($tabDonnees, $idmedecin);
		}else{
			
			$infosAntPersAlimentation = array();
			$infosAntPersAlimentation['allaitementMatenelExclusifAP'] = $tabDonnees['allaitementMatenelExclusifAP'];
			$infosAntPersAlimentation['typeAllaitementAP'] = (( $tabDonnees['allaitementMatenelExclusifAP'] ) == -1 && (! empty($tabDonnees['typeAllaitementAP']))) ? $tabDonnees['typeAllaitementAP'] : null;
			$infosAntPersAlimentation['nomAllaitementArtificielAP'] = ($tabDonnees['allaitementMatenelExclusifAP'] == -1 && $tabDonnees['typeAllaitementAP'] == 1 && (! empty($tabDonnees['nomAllaitementArtificielAP']))) ? $tabDonnees['nomAllaitementArtificielAP'] : null;
			$infosAntPersAlimentation['diversificationAlimentaireAP'] = $tabDonnees['diversificationAlimentaireAP'];
			$infosAntPersAlimentation['ageDebutDiversificationAlimentaireAP'] = ($tabDonnees['diversificationAlimentaireAP'] == 1 && (! empty($tabDonnees['ageDebutDiversificationAlimentaireAP']))) ? $tabDonnees['ageDebutDiversificationAlimentaireAP'] : null;
			$infosAntPersAlimentation['typesAlimentsAP'] = ($tabDonnees['diversificationAlimentaireAP'] == 1 && (! empty($tabDonnees['typesAlimentsAP']))) ? $tabDonnees['typesAlimentsAP'] : null;
			$infosAntPersAlimentation['sevrageAP'] = $tabDonnees['sevrageAP'];
			$infosAntPersAlimentation['ageDebutSevrageAP'] = ($tabDonnees['sevrageAP'] == 1 && (! empty($tabDonnees['ageDebutSevrageAP']))) ? $tabDonnees['ageDebutSevrageAP'] : null;
			
		    if(!$this->array_empty($infosAntPersAlimentation)){
		    	$infosAntPersAlimentation['idpatient'] = $tabDonnees['idpatient'];
		    	$infosAntPersAlimentation['idmedecin'] = $idmedecin;
		    	$infosAntPersAlimentation['date_enregistrement'] = (new \DateTime() ) ->format('Y-m-d H:i:s');
		    	
		    	$this->tableGateway->insert($infosAntPersAlimentation);
		    }
		    
		}
		
	}
	
	
	public function updateAntecedentsPersAlimentation($tabDonnees, $idmedecin){
	
		$infosAntPersAlimentation = array();
		$infosAntPersAlimentation['allaitementMatenelExclusifAP'] = $tabDonnees['allaitementMatenelExclusifAP'];
		$infosAntPersAlimentation['typeAllaitementAP'] = (( $tabDonnees['allaitementMatenelExclusifAP'] ) == -1 && (! empty($tabDonnees['typeAllaitementAP']))) ? $tabDonnees['typeAllaitementAP'] : null;
		$infosAntPersAlimentation['nomAllaitementArtificielAP'] = ($tabDonnees['allaitementMatenelExclusifAP'] == -1 && $tabDonnees['typeAllaitementAP'] == 1 && (! empty($tabDonnees['nomAllaitementArtificielAP']))) ? $tabDonnees['nomAllaitementArtificielAP'] : null;
		$infosAntPersAlimentation['diversificationAlimentaireAP'] = $tabDonnees['diversificationAlimentaireAP'];
		$infosAntPersAlimentation['ageDebutDiversificationAlimentaireAP'] = ($tabDonnees['diversificationAlimentaireAP'] == 1 && (! empty($tabDonnees['ageDebutDiversificationAlimentaireAP']))) ? $tabDonnees['ageDebutDiversificationAlimentaireAP'] : null;
		$infosAntPersAlimentation['typesAlimentsAP'] = ($tabDonnees['diversificationAlimentaireAP'] == 1 && (! empty($tabDonnees['typesAlimentsAP']))) ? $tabDonnees['typesAlimentsAP'] : null;
		$infosAntPersAlimentation['sevrageAP'] = $tabDonnees['sevrageAP'];
		$infosAntPersAlimentation['ageDebutSevrageAP'] = ($tabDonnees['sevrageAP'] == 1 && (! empty($tabDonnees['ageDebutSevrageAP']))) ? $tabDonnees['ageDebutSevrageAP'] : null;
		$infosAntPersAlimentation['idmedecin'] = $idmedecin;
		
		$this->tableGateway->update($infosAntPersAlimentation, array('idpatient' => $tabDonnees['idpatient']));			
	}
	
	
}

