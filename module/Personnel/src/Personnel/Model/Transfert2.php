<?php

namespace Personnel\Model;

use Zend\InputFilter\InputFilterInterface;

/**
 * POUR LA RECUPERATION DES DONNEES DE LA BASE
 * @author hassim
 *
 */
class Transfert2 {
	
	public $id_personne;
	public $id_verif;
	/***********************************/
	public $type_transfert;
	/***********************************/
	public $service_origine;
	public $service_accueil;
	public $motif_transfert;
	public $note;
	/***********************************/
	public $service_origine_externe;
	public $service_accueil_externe;
	public $motif_transfert_externe;
	
	/**********************************/
	public $date_debut;
	
	protected $inputFilter;
	
	public function exchangeArray($data) {
 		$this->id_personne = (! empty ( $data ['ID_PERSONNE'] )) ? $data ['ID_PERSONNE'] : null;
 		
 		$this->type_transfert = (! empty ( $data ['TYPE_TRANSFERT'] )) ? $data ['TYPE_TRANSFERT'] : null;
 		
 		$this->date_debut = (! empty ( $data ['DATE_DEBUT'] )) ? $data ['DATE_DEBUT'] : null;
 		
 		if($this->type_transfert == "Interne") {
 			$this->service_origine = (! empty ( $data ['ID_SERVICE_ORIGINE'] )) ? $data ['ID_SERVICE_ORIGINE'] : null;
 			$this->service_accueil = (! empty ( $data ['ID_SERVICE_ACCUEIL'] )) ? $data ['ID_SERVICE_ACCUEIL'] : null;
 			$this->motif_transfert = (! empty ( $data ['MOTIF_TRANSFERT'] )) ? $data ['MOTIF_TRANSFERT'] : null;
 			$this->note = (! empty ( $data ['NOTE'] )) ? $data ['NOTE'] : null;
 		}else
 		if($this->type_transfert == "Externe") {
 			$this->service_origine_externe = (! empty ( $data ['ID_SERVICE_ORIGINE'] )) ? $data ['ID_SERVICE_ORIGINE'] : null;
 			$this->service_accueil_externe = (! empty ( $data ['ID_SERVICE_ACCUEIL'] )) ? $data ['ID_SERVICE_ACCUEIL'] : null;
 			$this->motif_transfert_externe = (! empty ( $data ['MOTIF_TRANSFERT'] )) ? $data ['MOTIF_TRANSFERT'] : null;
 		}
	}
	
	public function getArrayCopy() {
		return get_object_vars ( $this );
	}
	
	public function setInputFilter(InputFilterInterface $inputFilter) {
		throw new \Exception ( "Not used" );
	}
}