<?php

namespace Personnel\Model;


use Zend\InputFilter\InputFilterInterface;
/*
 * POUR LA RECUPERATION DES DONNEES DE LA BASE
 */

class Intervention2 {
	
	public $id_personne;
	public $id_verif;
	/***********************************/
	public $type_intervention;
	public $numero_intervention;
	
	/***********************************/
	public $id_service;
	public $date_debut;
	public $date_fin;
	public $motif_intervention;
	public $note;
	/**********************************/
	public $id_service_externe;
	public $date_debut_externe;
	public $date_fin_externe;
	public $motif_intervention_externe;
	public $note_externe;
	
	protected $inputFilter;
	
	public function exchangeArray($data) {
 		$this->id_personne = (! empty ( $data ['id_personne'] )) ? $data ['id_personne'] : null;
 		$this->id_verif = (! empty ( $data ['id_verif'] )) ? $data ['id_verif'] : null;
 		
 		$this->type_intervention = (! empty ( $data ['type_intervention'] )) ? $data ['type_intervention'] : null;
 		$this->numero_intervention = (! empty ( $data ['numero_intervention'] )) ? $data ['numero_intervention'] : null;
 		
 		if($this->type_intervention == 'Interne') {
 		    //INTERVENTION INTERNE
 			$this->id_service = (! empty ( $data ['id_service'] )) ? $data ['id_service'] : null;
 			$this->date_debut = (! empty ( $data ['date_debut'] )) ? $data ['date_debut'] : null;
 			$this->date_fin = (! empty ( $data ['date_fin'] )) ? $data ['date_fin'] : null;
 			$this->motif_intervention = (! empty ( $data ['motif_intervention'] )) ? $data ['motif_intervention'] : null;
 			$this->note = (! empty ( $data ['note'] )) ? $data ['note'] : null;
 		} else if($this->type_intervention == 'Externe') {
      	    //INTERVENTION EXTERNE
 			$this->id_service_externe = (! empty ( $data ['id_service'] )) ? $data ['id_service'] : null;
 			$this->date_debut_externe = (! empty ( $data ['date_debut'] )) ? $data ['date_debut'] : null;
 			$this->date_fin_externe = (! empty ( $data ['date_fin'] )) ? $data ['date_fin'] : null;
 			$this->motif_intervention_externe = (! empty ( $data ['motif_intervention'] )) ? $data ['motif_intervention'] : null;
 			$this->note_externe = (! empty ( $data ['note'] )) ? $data ['note'] : null;
 		}
	}
	
	public function getArrayCopy() {
		return get_object_vars ( $this );
	}
	
	public function setInputFilter(InputFilterInterface $inputFilter) {
		throw new \Exception ( "Not used" );
	}
}