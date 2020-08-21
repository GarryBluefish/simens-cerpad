<?php
namespace Consultation\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
//use Zend\XmlRpc\Value\String;
use Consultation\Model;
class OrdonConsommableTable{
	protected $tableGateway;
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}

	public function getOrdonnance($id){
		$rowset = $this->tableGateway->select ( array (
				'ID_CONS' => $id
		) );
		$row =  $rowset->current ();
		if (! $row) {
			return $row = null;
		}
		return $row;
	}
	
	public function getMedicamentsParIdOrdonnance($idOrdonnance){
		$adapter = $this->tableGateway->getAdapter ();
		$sql = new Sql ( $adapter );
		$select = $sql->select ();
		$select->columns( array('*'));
		$select->from( array( 'oc' => 'ordon_consommable' ));
		$select->join( array( 'o' => 'ordonnance'
		), 'oc.ID_DOCUMENT = o.ID_DOCUMENT' , array ('*') );
		$select->where ( array( 'o.ID_DOCUMENT' => $idOrdonnance));
		
		$stat = $sql->prepareStatementForSqlObject ( $select );
		$result = $stat->execute ();
		
		return $result;
	}

	/**
	 * 
	 * @param $tab : Tableau des médicaments
	 * @param $donnees : Duree traitement et id_cons
	 */
	public function updateOrdonConsommable($tab, $idOrdonnance){
		/**
		 * On supprime d'abord tous les medicaments
		 */
		$this->tableGateway->delete(array("id_document" =>$idOrdonnance));
		
		/**
		 * S'il y a des medicaments on les ajoute sinon on supprime l'ordonnance
		 */
		if($tab) {
			for($i = 1; $i<count($tab); $i++){
				$data = array(
						'id_document'=>$idOrdonnance,
						'id_materiel'=>$tab[$i++],
						'forme'=>$tab[$i++],
						'quantite'=>$tab[$i++].' '.$tab[$i++],
				        'posologie'=>$tab[$i],
				);
				$this->tableGateway->insert($data);
			}
			return true;
		}else{
			return false;
		}
	}
	
	/**
	 * Recuperer les medicaments par leur nom
	 */
	public function getMedicamentByName($intitule){
		$adapter = $this->tableGateway->getAdapter ();
		$sql = new Sql ( $adapter );
		$select = $sql->select ();
		$select->columns( array('*'));
		$select->from( array( 'c' => 'medicament' ));
		$select->where ( array( 'c.INTITULE' => $intitule));
		
		$stat = $sql->prepareStatementForSqlObject ( $select );
		$result = $stat->execute ()->current();
		
		return $result;
	}
	
	
	/**
	 * Ajouter des medicaments dans la base de données
	 */
	public function addMedicaments($medicament){
		$db = $this->tableGateway->getAdapter();
		$sql = new Sql($db);
		$sQuery = $sql->insert()
		->into('medicament')
		->values(array('INTITULE' => $medicament));
		$requete = $sql->prepareStatementForSqlObject($sQuery);
		return $requete->execute()->getGeneratedValue();
	}
	
	public function existeFormes($libelleForme){
		$adapter = $this->tableGateway->getAdapter ();
		$sql = new Sql ( $adapter );
		$select = $sql->select ();
		$select->columns( array('*'));
		$select->from( array( 'fm' => 'forme_medicament' ));
		$select->where ( array( 'fm.libelleForme' => $libelleForme));
	
		$stat = $sql->prepareStatementForSqlObject ( $select );
		return $stat->execute ()->current();
	}
	
	/**
	 * Ajouter des formes
	 */
	public function addFormes($libelleForme){
		if( $this->existeFormes($libelleForme) == false ){
			$db = $this->tableGateway->getAdapter();
			$sql = new Sql($db);
			$sQuery = $sql->insert()
			->into('forme_medicament')
			->values(array('libelleForme' => $libelleForme));
			$sql->prepareStatementForSqlObject($sQuery)->execute();
		}
	}
	
	
	public function existeQuantites($libelleQuantite){
		$adapter = $this->tableGateway->getAdapter ();
		$sql = new Sql ( $adapter );
		$select = $sql->select ();
		$select->columns( array('*'));
		$select->from( array( 'qm' => 'quantite_medicament' ));
		$select->where ( array( 'qm.libelleQuantite' => $libelleQuantite));
	
		$stat = $sql->prepareStatementForSqlObject ( $select );
		return $stat->execute ()->current();
	}
	
	/**
	 * Ajouter des formes
	 */
	public function addQuantites($libelleQuantite){
		if( $this->existeQuantites($libelleQuantite) == false ){
			$db = $this->tableGateway->getAdapter();
			$sql = new Sql($db);
			$sQuery = $sql->insert()
			->into('quantite_medicament')
			->values(array('libelleQuantite' => $libelleQuantite));
			$sql->prepareStatementForSqlObject($sQuery)->execute();
		}
	}
}
