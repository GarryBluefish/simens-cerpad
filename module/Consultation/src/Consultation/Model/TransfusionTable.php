<?php
namespace Consultation\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;

class TransfusionTable {

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
	
	public function insertTransfusion($tabDonnees){
		$this->tableGateway->delete(array('idcons' => $tabDonnees['idcons']));
		
		$donnees ['idcons'] = $tabDonnees['idcons'];
		$donnees ['groupeSanguin'] = $tabDonnees['groupeSanguinTransfusion'];
		$donnees ['produitSanguin1'] = (! empty ( $tabDonnees['produitSanguin1'] )) ? $tabDonnees['produitSanguin1'] : null;
		$donnees ['produitSanguin1Quantite'] = (! empty ( $tabDonnees['produitSanguin1'] )) ? $tabDonnees['produitSanguin1Quantite'] : null;
		$donnees ['produitSanguin2'] = (! empty ( $tabDonnees['produitSanguin2'] )) ? $tabDonnees['produitSanguin2'] : null;
		$donnees ['produitSanguin2Quantite'] = (! empty ( $tabDonnees['produitSanguin2'] )) ? $tabDonnees['produitSanguin2Quantite'] : null;
		$donnees ['reactionTransfusionnel'] = $tabDonnees['reactionTransfusionnel'];
		$donnees ['reactionTransfusionnelValeur'] = $tabDonnees['reactionTransfusionnelValeur'];
		
		$this->tableGateway->insert($donnees);
	}
	
	public function getTransfusion($idcons){
		return $this->tableGateway->select(array('idcons' => $idcons))->toArray();
	}
	
	
	
	
}
