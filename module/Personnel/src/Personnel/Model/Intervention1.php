<?php

namespace Personnel\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;

/*
 * POUR LA RECUPERATION DES DONNEES DU FORMULAIRE
 */

class Intervention1 {
	
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
 		//INTERVENTION INTERNE
        $this->id_service = (! empty ( $data ['id_service'] )) ? $data ['id_service'] : null;
  	    $this->date_debut = (! empty ( $data ['date_debut'] )) ? $data ['date_debut'] : null;
  	    $this->date_fin = (! empty ( $data ['date_fin'] )) ? $data ['date_fin'] : null;
  	    $this->motif_intervention = (! empty ( $data ['motif_intervention'] )) ? $data ['motif_intervention'] : null;
  	    $this->note = (! empty ( $data ['note'] )) ? $data ['note'] : null;
  	    
  	    //INTERVENTION EXTERNE
  	    $this->id_service_externe = (! empty ( $data ['id_service_externe'] )) ? $data ['id_service_externe'] : null;
  	    $this->date_debut_externe = (! empty ( $data ['date_debut_externe'] )) ? $data ['date_debut_externe'] : null;
  	    $this->date_fin_externe = (! empty ( $data ['date_fin_externe'] )) ? $data ['date_fin_externe'] : null;
  	    $this->motif_intervention_externe = (! empty ( $data ['motif_intervention_externe'] )) ? $data ['motif_intervention_externe'] : null;
  	    $this->note_externe = (! empty ( $data ['note_externe'] )) ? $data ['note_externe'] : null;
	}
	
	public function getArrayCopy() {
		return get_object_vars ( $this );
	}
	
	public function setInputFilter(InputFilterInterface $inputFilter) {
		throw new \Exception ( "Not used" );
	}
	
	public function getInputFilter() {
		if (! $this->inputFilter) {

			$inputFilter = new InputFilter ();
			$inputFilter->add (array (
					'name' => 'id_verif',
					'required' => false,
					'filters' => array (
							array (
									'name' => 'StripTags'
							),
							array (
									'name' => 'StringTrim'
							)
					),
			) );

			$inputFilter->add (array (
					'name' => 'id_personne',
					'required' => true,
					'filters' => array (
							array (
									'name' => 'StripTags'
							),
							array (
									'name' => 'StringTrim'
							)
					),
			) );
			
			
			$inputFilter->add (array (
					'name' => 'type_intervention',
					'required' => false,
					'filters' => array (
							array (
									'name' => 'StripTags'
							),
							array (
									'name' => 'StringTrim'
							)
					),
			) );
			
			$inputFilter->add (array (
					'name' => 'numero_intervention',
					'required' => false,
					'filters' => array (
							array (
									'name' => 'StripTags'
							),
							array (
									'name' => 'StringTrim'
							)
					),
			) );
			
			/*
			 * ***********************************************************************
			 * ================ INTERVENTION INTERNE ====================================
			 * ***********************************************************************
			 * ***********************************************************************
			 */
			
			 $inputFilter->add (array (
			 		'name' => 'id_service',
			 		'required' => false,
			 		'filters' => array (
			 				array (
			 						'name' => 'StripTags'
			 				),
			 				array (
			 						'name' => 'StringTrim'
			 				)
			 		),
			 ) );
			 
			 $inputFilter->add (array (
			 		'name' => 'date_debut',
			 		'required' => false,
			 		'filters' => array (
			 				array (
			 						'name' => 'StripTags'
			 				),
			 				array (
			 						'name' => 'StringTrim'
			 				)
			 		),
			 ) );
			 
			 $inputFilter->add (array (
			 		'name' => 'date_fin',
			 		'required' => false,
			 		'filters' => array (
			 				array (
			 						'name' => 'StripTags'
			 				),
			 				array (
			 						'name' => 'StringTrim'
			 				)
			 		),
			 ) );
			 
			 $inputFilter->add (array (
			 		'name' => 'motif_intervention',
			 		'required' => false,
			 		'filters' => array (
			 				array (
			 						'name' => 'StripTags'
			 				),
			 				array (
			 						'name' => 'StringTrim'
			 				)
			 		),
			 ) );
			 
			 $inputFilter->add (array (
			 		'name' => 'note',
			 		'required' => false,
			 		'filters' => array (
			 				array (
			 						'name' => 'StripTags'
			 				),
			 				array (
			 						'name' => 'StringTrim'
			 				)
			 		),
			 ) );
			 
			 /*
			  * ***********************************************************************
			  * ================ INTERVENTION EXTERNE ====================================
			  * ***********************************************************************
			  * ***********************************************************************
			  */
			 
			 $inputFilter->add (array (
			 		'name' => 'id_service_externe',
			 		'required' => false,
			 		'filters' => array (
			 				array (
			 						'name' => 'StripTags'
			 				),
			 				array (
			 						'name' => 'StringTrim'
			 				)
			 		),
			 ) );
			 
			 $inputFilter->add (array (
			 		'name' => 'date_debut_externe',
			 		'required' => false,
			 		'filters' => array (
			 				array (
			 						'name' => 'StripTags'
			 				),
			 				array (
			 						'name' => 'StringTrim'
			 				)
			 		),
			 ) );
			 
			 $inputFilter->add (array (
			 		'name' => 'date_fin_externe',
			 		'required' => false,
			 		'filters' => array (
			 				array (
			 						'name' => 'StripTags'
			 				),
			 				array (
			 						'name' => 'StringTrim'
			 				)
			 		),
			 ) );
			 
			 $inputFilter->add (array (
			 		'name' => 'motif_intervention_externe',
			 		'required' => false,
			 		'filters' => array (
			 				array (
			 						'name' => 'StripTags'
			 				),
			 				array (
			 						'name' => 'StringTrim'
			 				)
			 		),
			 ) );
			 
			 $inputFilter->add (array (
			 		'name' => 'note_externe',
			 		'required' => false,
			 		'filters' => array (
			 				array (
			 						'name' => 'StripTags'
			 				),
			 				array (
			 						'name' => 'StringTrim'
			 				)
			 		),
			 ) );

			$this->inputFilter = $inputFilter;
		}

		return $this->inputFilter;
	}
}