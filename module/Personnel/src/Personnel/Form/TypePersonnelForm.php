<?php

namespace Personnel\Form;

use Zend\Form\Form;

class TypePersonnelForm extends Form {
	public function __construct($name = null) {
		parent::__construct ();

		$this->add ( array (
				'name' => 'id_personne',
				'type' => 'Hidden',
				'attributes' => array (
						'id' => 'id_personne'
				)
		) );
		
		$this->add ( array (
				'name' => 'type_personnel',
				'type' => 'Select',
				'options' => array (
				),
				'attributes' => array (
						'id' => 'type_personnel',
				)
		) );
	}
}