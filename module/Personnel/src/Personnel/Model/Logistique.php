<?php

namespace Personnel\Model;

use Zend\InputFilter\InputFilterInterface;

class Logistique {
	
	public $matricule_logistique;
	public $grade_logistique;
	public $domaine_logistique;
	public $autres_logistique;
	
	protected $inputFilter;
	
	public function exchangeArray($data) {
 		$this->matricule_logistique = (! empty ( $data ['matricule'] )) ? $data ['matricule'] : null;
 		$this->grade_logistique = (! empty ( $data ['grade'] )) ? $data ['grade'] : null;
 		$this->domaine_logistique = (! empty ( $data ['domaine'] )) ? $data ['domaine'] : null;
 		$this->autres_logistique = (! empty ( $data ['autres'] )) ? $data ['autres'] : null;
	}
	
	public function getArrayCopy() {
		return get_object_vars ( $this );
	}
	
	public function setInputFilter(InputFilterInterface $inputFilter) {
		throw new \Exception ( "Not used" );
	}
	
// 	public function getInputFilter() {
// 		if (! $this->inputFilter) {

// 			$inputFilter = new InputFilter ();

// 			 $inputFilter->add (array (
// 			 		'name' => 'matricule',
// 			 		'required' => false,
// 			 		'filters' => array (
// 			 				array (
// 			 						'name' => 'StripTags'
// 			 				),
// 			 				array (
// 			 						'name' => 'StringTrim'
// 			 				)
// 			 		),
// 			 		'validators' => array (
// 			 				array (
// 			 						'name' => 'StringLength',
// 			 						'options' => array (
// 			 								'encoding' => 'UTF-8',
// 			 								'min' => 1,
// 			 								'max' => 100
// 			 						)
// 			 				)
// 			 		)
// 			 ) );
	
// 			 $inputFilter->add (array (
// 			 		'name' => 'grade',
// 			 		'required' => false,
// 			 		'filters' => array (
// 			 				array (
// 			 						'name' => 'StripTags'
// 			 				),
// 			 				array (
// 			 						'name' => 'StringTrim'
// 			 				)
// 			 		),
// 			 		'validators' => array (
// 			 				array (
// 			 						'name' => 'StringLength',
// 			 						'options' => array (
// 			 								'encoding' => 'UTF-8',
// 			 								'min' => 1,
// 			 								'max' => 100
// 			 						)
// 			 				)
// 			 		)
// 			 ) );
			 
// 			 $inputFilter->add (array (
// 			 		'name' => 'domaine',
// 			 		'required' => false,
// 			 		'filters' => array (
// 			 				array (
// 			 						'name' => 'StripTags'
// 			 				),
// 			 				array (
// 			 						'name' => 'StringTrim'
// 			 				)
// 			 		),
// 			 		'validators' => array (
// 			 				array (
// 			 						'name' => 'StringLength',
// 			 						'options' => array (
// 			 								'encoding' => 'UTF-8',
// 			 								'min' => 1,
// 			 								'max' => 100
// 			 						)
// 			 				)
// 			 		)
// 			 ) );
			 
// 			 $inputFilter->add (array (
// 			 		'name' => 'autres',
// 			 		'required' => false,
// 			 		'filters' => array (
// 			 				array (
// 			 						'name' => 'StripTags'
// 			 				),
// 			 				array (
// 			 						'name' => 'StringTrim'
// 			 				)
// 			 		),
// 			 		'validators' => array (
// 			 				array (
// 			 						'name' => 'StringLength',
// 			 						'options' => array (
// 			 								'encoding' => 'UTF-8',
// 			 								'min' => 1,
// 			 								'max' => 100
// 			 						)
// 			 				)
// 			 		)
// 			 ) );
			 
// 			$this->inputFilter = $inputFilter;
// 		}

// 		return $this->inputFilter;
// 	}
}