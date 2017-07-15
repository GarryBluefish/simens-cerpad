<?php

namespace Personnel\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;

class Medecin {
	public $matricule;
	public $grade;
	public $specialite;
	public $fonction;
	
	protected $inputFilter;
	
	public function exchangeArray($data) {
 		$this->matricule = (! empty ( $data ['matricule'] )) ? $data ['matricule'] : null;
 		$this->grade = (! empty ( $data ['grade'] )) ? $data ['grade'] : null;
 		$this->specialite = (! empty ( $data ['specialite'] )) ? $data ['specialite'] : null;
 		$this->fonction = (! empty ( $data ['fonction'] )) ? $data ['fonction'] : null;
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
					'name' => 'matricule',
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
					'name' => 'grade',
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
			 		'name' => 'specialite',
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
			 		'name' => 'fonction',
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