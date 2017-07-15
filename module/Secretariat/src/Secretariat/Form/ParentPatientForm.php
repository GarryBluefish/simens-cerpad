<?php

namespace Secretariat\Form;

use Zend\Form\Form;

class ParentPatientForm extends Form {
	public function __construct($name = null) {
		parent::__construct ();

		$this->add ( array (
				'name' => 'idparent',
				'type' => 'Hidden',
		) );

		//******* Gestion des informations parentales ******
		//******* Gestion des informations parentales ******
		//******* Gestion des informations parentales ******
		
		//******* Informations maternelles ******
		//******* Informations maternelles ******
		$this->add ( array (
		    'name' => 'prenom_mere',
		    'type' => 'Text',
		    'options' => array (
		        'label' => iconv ( 'ISO-8859-1', 'UTF-8', 'Prénom')
		    ),
		    'attributes' => array (
		        'id' => 'prenom_mere',
		        'tabindex' => 14,
		    )
		) );
		
		$this->add ( array (
		    'name' => 'nom_mere',
		    'type' => 'Text',
		    'options' => array (
		        'label' => 'Nom'
		    ),
		    'attributes' => array (
		        'id' => 'nom_mere',
		        'tabindex' => 15,
		    )
		) );
		
		$this->add ( array (
		    'name' => 'profession_mere',
		    'type' => 'Text',
		    'options' => array (
		        'label' => iconv ( 'ISO-8859-1', 'UTF-8', 'Profession')
		    ),
		    'attributes' => array (
		        'id' => 'profession_mere',
		        'tabindex' => 16,
		    )
		) );
		
		$this->add ( array (
		    'name' => 'telephone_mere',
		    'type' => 'Text',
		    'options' => array (
		        'label' => iconv ( 'ISO-8859-1', 'UTF-8', 'Téléphone')
		    ),
		    'attributes' => array (
		        'id' => 'telephone_mere',
		        'tabindex' => 17,
		    )
		) );

		$this->add ( array (
		    'name' => 'fax_mere',
		    'type' => 'Text',
		    'options' => array (
		        'label' => iconv ( 'ISO-8859-1', 'UTF-8', 'Fax')
		    ),
		    'attributes' => array (
		        'id' => 'fax_mere',
		        'tabindex' => 18,
		    )
		) );
		
		$this->add ( array (
		    'name' => 'email_mere',
		    'type' => 'Text',
		    'options' => array (
		        'label' => iconv ( 'ISO-8859-1', 'UTF-8', 'Email')
		    ),
		    'attributes' => array (
		        'id' => 'email_mere',
		        'tabindex' => 19,
		    )
		) );
		
		
		//******* Informations paternelles ******
		//******* Informations paternelles ******
		$this->add ( array (
		    'name' => 'prenom_pere',
		    'type' => 'Text',
		    'options' => array (
		        'label' => iconv ( 'ISO-8859-1', 'UTF-8', 'Prénom')
		    ),
		    'attributes' => array (
		        'id' => 'prenom_pere',
		        'tabindex' => 20,
		    )
		) );
		
		$this->add ( array (
		    'name' => 'nom_pere',
		    'type' => 'Text',
		    'options' => array (
		        'label' => 'Nom'
		    ),
		    'attributes' => array (
		        'id' => 'nom_pere',
		        'tabindex' => 21,
		    )
		) );
		
		$this->add ( array (
		    'name' => 'profession_pere',
		    'type' => 'Text',
		    'options' => array (
		        'label' => iconv ( 'ISO-8859-1', 'UTF-8', 'Profession')
		    ),
		    'attributes' => array (
		        'id' => 'profession_pere',
		        'tabindex' => 22,
		    )
		) );
		
		$this->add ( array (
		    'name' => 'telephone_pere',
		    'type' => 'Text',
		    'options' => array (
		        'label' => iconv ( 'ISO-8859-1', 'UTF-8', 'Téléphone')
		    ),
		    'attributes' => array (
		        'id' => 'telephone_pere',
		        'tabindex' => 23,
		    )
		) );
		
		$this->add ( array (
		    'name' => 'fax_pere',
		    'type' => 'Text',
		    'options' => array (
		        'label' => iconv ( 'ISO-8859-1', 'UTF-8', 'Fax')
		    ),
		    'attributes' => array (
		        'id' => 'fax_pere',
		        'tabindex' => 24,
		    )
		) );
		
		$this->add ( array (
		    'name' => 'email_pere',
		    'type' => 'Text',
		    'options' => array (
		        'label' => iconv ( 'ISO-8859-1', 'UTF-8', 'Email')
		    ),
		    'attributes' => array (
		        'id' => 'email_pere',
		        'tabindex' => 25,
		    )
		) );
		
		//**************************************************
		//**************************************************
		
	}
}