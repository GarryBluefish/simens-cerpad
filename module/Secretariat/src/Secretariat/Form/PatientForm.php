<?php

namespace Secretariat\Form;

use Zend\Form\Form;

class PatientForm extends Form {
	public function __construct($name = null) {
		parent::__construct ();

		$this->add ( array (
				'name' => 'idpersonne',
				'type' => 'Hidden',
		) );

		$this->add ( array (
		    'name' => 'typepatient',
		    'type' => 'Radio',
		    'options' => array (
		        'value_options' => array (
		          array( 'label' => 'Externe', 'value' => 0, 'attributes' => array ( 'id' => 'externe_patient' ) ),
		          array( 'label' => 'Interne', 'value' => 1, 'attributes' => array ( 'id' => 'interne_patient' ) ),
		        )
		    ),
		    'attributes' => array (
		        'id' => 'typepatient',
		    )
		) );
		
		
		$this->add ( array (
		    'name' => 'prenom',
		    'type' => 'Text',
		    'options' => array (
		        'label' => iconv ( 'ISO-8859-1', 'UTF-8', 'Prénom' )
		    ),
		    'attributes' => array (
		        'id' => 'prenom',
		        'required' => true,
		        'autocomplete' => false,
		        'tabindex' => 1,
		    )
		) );
		
		$this->add ( array (
				'name' => 'nom',
				'type' => 'Text',
				'options' => array (
						'label' => 'Nom'
				),
				'attributes' => array (
						'id' => 'nom',
						'required' => true,
						'autocomplete' => false,
						'tabindex' => 2,
				)
		) );
		

		
		$this->add ( array (
				'name' => 'age',
				'type' => 'number',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8', 'Âge' )
				),
				'attributes' => array (
						'id' => 'age',
						'autocomplete' => false,
						'required' => true,
						'tabindex' => 3,
				)
		) );
		

		$this->add ( array (
				'name' => 'date_naissance',
				'type' => 'Text',
				'options' => array (
						'label' => 'Date de naissance'
				),
				'attributes' => array (
						'id' => 'date_naissance',
						'tabindex' => 4,
				)
		) );
		
		$this->add ( array (
		    'name' => 'sexe',
		    'type' => 'Zend\Form\Element\Select',
		    'options' => array (
		        'label' => 'Sexe',
		        'value_options' => array (
		            '' => '',
		            'Masculin' => 'Masculin',
		            iconv ( 'ISO-8859-1', 'UTF-8','Féminin') => iconv ( 'ISO-8859-1', 'UTF-8','Féminin')
		        )
		    ),
		    'attributes' => array (
		        'id' => 'sexe',
		        'required' => true,
		        'tabindex' => 5,
		    )
		) );

		$this->add ( array (
				'name' => 'lieu_naissance',
				'type' => 'Text',
				'options' => array (
						'label' => 'Lieu de naissance'
				),
				'attributes' => array (
						'id' => 'lieu_naissance',
						'tabindex' => 6,
				)
				) );

		$this->add ( array (
				'name' => 'telephone',
				'type' => 'Text',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8', 'Téléphone' )
				),
				'attributes' => array (
						'id' => 'telephone',
						'tabindex' => 7,
				)
		) );
		$this->add ( array (
				'name' => 'profession',
				'type' => 'Text',
				'options' => array (
						'label' => 'Profession'
				),
				'attributes' => array (
						'id' => 'profession',
						'tabindex' => 8,
				)
		) );
		
		$this->add ( array (
				'name' => 'adresse',
				'type' => 'Text',
				'options' => array (
						'label' => 'Adresse'
				),
				'attributes' => array (
						'id' => 'adresse',
						'tabindex' => 9,
				)
		) );
		
		$this->add ( array (
				'name' => 'email',
				'type' => 'Zend\Form\Element\Email',
				'options' => array (
						'label' => 'Email'
				),
				'attributes' => array (
						'placeholder' => 'votre@domain.com',
						'id' => 'email',
						'tabindex' => 10,
				)
		) );
		
		$this->add ( array (
				'name' => 'situation_matrimoniale',
				'type' => 'Zend\Form\Element\Select',
				'options' => array (
						'label' => 'Situation matrimoniale',
						'value_options' => array (
								'' => '',
								iconv ( 'ISO-8859-1', 'UTF-8','Marié') => iconv ( 'ISO-8859-1', 'UTF-8','Marié'),
								iconv ( 'ISO-8859-1', 'UTF-8','Célibataire') => iconv ( 'ISO-8859-1', 'UTF-8','Célibataire')
						)
				),
				'attributes' => array (
						'id' => 'situation_matrimoniale',         
						'tabindex' => 11,
				)
		) );
		
		//Les deux suivants c est l'un ou l'autre qui est affiché
		$this->add ( array (
		    'name' => 'situation_matrimoniale',
		    'type' => 'Zend\Form\Element\Select',
		    'options' => array (
		        'label' => 'Situation matrimoniale',
		        'value_options' => array (
		            '' => '',
		            iconv ( 'ISO-8859-1', 'UTF-8','Marié') => iconv ( 'ISO-8859-1', 'UTF-8','Marié'),
		            iconv ( 'ISO-8859-1', 'UTF-8','Célibataire') => iconv ( 'ISO-8859-1', 'UTF-8','Célibataire')
		        )
		    ),
		    'attributes' => array (
		        'id' => 'situation_matrimoniale',
		        'tabindex' => 11,
		    )
		) );
		
		//Pour le dépistage -------- pour le dépistage
		$this->add ( array (
		    'name' => 'depistage',
		    'type' => 'Hidden',
		    'attributes' => array( 'id' => 'depistage' )
		) );
		
		$this->add ( array (
		    'name' => 'ethnie',
		    'type' => 'Text',
		    'options' => array (
		        'label' => 'Ethnie'
		    ),
		    'attributes' => array (
		        'id' => 'ethnie',
		        'tabindex' => 11,
		    )
		) );
		
		$this->add ( array (
		    'name' => 'nationalite_actuelle',
		    'type' => 'Zend\Form\Element\Select',
		    'options' => array (
		        'label' => iconv ( 'ISO-8859-1', 'UTF-8','Nationalité actuelle'),
		        'value_options' => array (
		            '' => ''
		        )
		    ),
		    'attributes' => array (
		        'id' => 'nationalite_actuelle',
		        'tabindex' => 12,
		    )
		
		) );
		
		$this->add ( array (
				'name' => 'nationalite_origine',
				'type' => 'Zend\Form\Element\Select',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8','Nationalité origine'),
				),
				'attributes' => array (
						'id' => 'nationalite_origine',
						'tabindex' => 13,
				)
		) );
		
		
		$this->add ( array (
				'name' => 'photo',
				'type' => 'Hidden',
		) );
		
		$this->add ( array (
				'name' => 'date_modification',
				'type' => 'Hidden',
		) );
		
		
		$this->add ( array (
				'name' => 'submit',
				'type' => 'Submit',
				'options' => array (
						'label' => 'Sauvegarder'
				)
		) );
	}
}