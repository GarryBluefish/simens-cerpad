<?php

namespace Personnel\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;

class Affectation {
	
	public $idemploye;
	public $service_accueil;
	public $date_debut;
	public $date_fin;
	public $numero;
	
	protected $inputFilter;
	
	public function exchangeArray($data) {
		$this->idemploye = (! empty ( $data ['idemploye'] )) ? $data ['idemploye'] : null;
		$this->service_accueil = (! empty ( $data ['idservice'] )) ? $data ['idservice'] : null;
 		$this->date_debut = (! empty ( $data ['date_debut'] )) ? $data ['date_debut'] : null;
 		$this->date_fin = (! empty ( $data ['date_fin'] )) ? $data ['date_fin'] : null;
 		$this->numero_os = (! empty ( $data ['numero_os'] )) ? $data ['numero_os'] : null;
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
			 		'validators' => array (
			 				array (
			 						'name' => 'StringLength',
			 						'options' => array (
			 								'encoding' => 'UTF-8',
			 								'min' => 1,
			 								'max' => 100
			 						)
			 				)
			 		)
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
			 		'validators' => array (
			 				array (
			 						'name' => 'StringLength',
			 						'options' => array (
			 								'encoding' => 'UTF-8',
			 								'min' => 1,
			 								'max' => 100
			 						)
			 				)
			 		)
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
			 		'validators' => array (
			 				array (
			 						'name' => 'StringLength',
			 						'options' => array (
			 								'encoding' => 'UTF-8',
			 								'min' => 1,
			 								'max' => 100
			 						)
			 				)
			 		)
			 ) );
			 
			 $inputFilter->add (array (
			 		'name' => 'numero',
			 		'required' => false,
			 		'filters' => array (
			 				array (
			 						'name' => 'StripTags'
			 				),
			 				array (
			 						'name' => 'StringTrim'
			 				)
			 		),
			 		'validators' => array (
			 				array (
			 						'name' => 'StringLength',
			 						'options' => array (
			 								'encoding' => 'UTF-8',
			 								'min' => 1,
			 								'max' => 100
			 						)
			 				)
			 		)
			 ) );
			 
			$this->inputFilter = $inputFilter;
		}

		return $this->inputFilter;
	}
}