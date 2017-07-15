<?php

namespace Personnel\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;

/**
 * Pour les donnees recuperees du formulaire apres la saise
 * POUR LEUR INSERTION DANS LA BASE DE DONNEES
 * @author hassim
 *
 */
class Transfert1 {
	
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
	//public $hopital_accueil;
	//public $service_accueil_externe;
	public $motif_transfert_externe;
	
	protected $inputFilter;
	
	public function exchangeArray($data) {
 		$this->id_personne = (! empty ( $data ['id_personne'] )) ? $data ['id_personne'] : null;
 		$this->id_verif = (! empty ( $data ['id_verif'] )) ? $data ['id_verif'] : null;
 		
 		$this->type_transfert = (! empty ( $data ['type_transfert'] )) ? $data ['type_transfert'] : null;
 		
        $this->service_origine = (! empty ( $data ['service_origine'] )) ? $data ['service_origine'] : null;
  	    $this->service_accueil = (! empty ( $data ['service_accueil'] )) ? $data ['service_accueil'] : null;
  	    $this->motif_transfert = (! empty ( $data ['motif_transfert'] )) ? $data ['motif_transfert'] : null;
  	    $this->note = (! empty ( $data ['note'] )) ? $data ['note'] : null;
  	    
  	    $this->service_origine_externe = (! empty ( $data ['service_origine_externe'] )) ? $data ['service_origine_externe'] : null;
  	    //$this->hopital_accueil = (! empty ( $data ['hopital_accueil'] )) ? $data ['hopital_accueil'] : null;
  	    //$this->service_accueil_externe = (! empty ( $data ['service_accueil_externe'] )) ? $data ['service_accueil_externe'] : null;
  	    $this->motif_transfert_externe = (! empty ( $data ['motif_transfert_externe'] )) ? $data ['motif_transfert_externe'] : null;
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
			
			/*
			 * ***********************************************************************
			 * ================ TRANSFERT INTERNE ====================================
			 * ***********************************************************************
			 * ***********************************************************************
			 */
			
			$inputFilter->add (array (
			 		'name' => 'type_transfert',
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
			 		'name' => 'service_origine',
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
			 		'name' => 'service_accueil',
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
			 		'name' => 'motif_transfert',
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
			  * ================ TRANSFERT EXTERNE ====================================
			  * ***********************************************************************
			  * ***********************************************************************
			  */
			 
			 $inputFilter->add (array (
			 		'name' => 'service_origine_externe',
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
			 
// 			 $inputFilter->add (array (
// 			 		'name' => 'hopital_accueil',
// 			 		'required' => false,
// 			 		'filters' => array (
// 			 				array (
// 			 						'name' => 'StripTags'
// 			 				),
// 			 				array (
// 			 						'name' => 'StringTrim'
// 			 				)
// 			 		),
// 			 ) );
			 
// 			 $inputFilter->add (array (
// 			 		'name' => 'service_accueil_externe',
// 			 		'required' => false,
// 			 		'filters' => array (
// 			 				array (
// 			 						'name' => 'StripTags'
// 			 				),
// 			 				array (
// 			 						'name' => 'StringTrim'
// 			 				)
// 			 		),
// 			 ) );
			 
			 $inputFilter->add (array (
			 		'name' => 'motif_transfert_externe',
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