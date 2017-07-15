<?php

namespace Personnel\Form;

use Zend\Form\Form;

class InterventionPersonnelForm extends Form {
	public function __construct($name = null) {
		parent::__construct ();

		$this->add ( array (
				'name' => 'id_verif',
				'type' => 'Hidden',
				'attributes' => array (
						'id' => 'id_verif'
				)
		) );
		
		$this->add ( array (
				'name' => 'id_intervention',
				'type' => 'Hidden',
				'attributes' => array (
						'id' => 'id_intervention'
				)
		) );
		
		$this->add ( array (
				'name' => 'id_personne',
				'type' => 'Hidden',
				'attributes' => array (
						'id' => 'id_personne'
				)
		) );
		
		$this->add ( array (
				'name' => 'type_intervention',
				'type' => 'Zend\Form\Element\Select',
				'options' => array (
						'label' => 'Type intervention',
						'value_options' => array (
								'Interne' => 'Interne',
								'Externe' => 'Externe',
						)
				),
				'attributes' => array (
						'id' => 'type_intervention',
						'onchange' => 'getChamps(this.value)',
				)
		) );
/*************************************************************************/
/*=========================== INTERVENTION INTERNE =========================*/
		
		$this->add ( array (
				'name' => 'service_origine',
				'type' => 'Text',
				'options' => array (
						'label' => 'Service d\'origine',
				),
				'attributes' => array (
						'id' => 'service_origine',
				)
		) );
		
		$this->add ( array (
				'name' => 'id_service',
				'type' => 'Zend\Form\Element\Select',
				'options' => array (
						'label' => 'Service d\'accueil',
				),
				'attributes' => array (
						'id' => 'id_service',
				)
		) );
		
		$this->add ( array (
				'name' => 'date_debut',
				'type' => 'text',
				'options' => array (
						'label' => 'Date debut',
				),
				'attributes' => array (
						'id' => 'date_debut',
				)
		) );
		
		$this->add ( array (
				'name' => 'date_fin',
				'type' => 'text',
				'options' => array (
						'label' => 'Date fin',
				),
				'attributes' => array (
						'id' => 'date_fin',
				)
		) );
		
		$this->add ( array (
				'name' => 'motif_intervention',
				'type' => 'Textarea',
				'options' => array (
						'label' => 'Motif'
				),
				'attributes' => array (
						'id' => 'motif_intervention',
				)
		) );
		
		$this->add ( array (
				'name' => 'note',
				'type' => 'Textarea',
				'options' => array (
						'label' => 'Note'
				),
				'attributes' => array (
						'id' => 'note',
				)
		) );
		
/*************************************************************************/
/*=========================== INTERVENTION EXTERNE =========================*/
		
		$this->add ( array (
				'name' => 'service_origine_externe',
				'type' => 'Text',
				'options' => array (
						'label' => 'Service d\'origine'
				),
				'attributes' => array (
						'id' => 'service_origine_externe',
				)
		) );
		
		$this->add ( array (
				'name' => 'hopital_accueil',
				'type' => 'Zend\Form\Element\Select',
				'options' => array (
						'label' => 'Hopital d\'accueil'
				),
				'attributes' => array (
						'registerInArrrayValidator' => false,
						'id' => 'hopital_accueil',
						'onchange' => 'getservices(this.value)',
				)
		) );
		
		$this->add ( array (
				'name' => 'id_service_externe',
				'type' => 'Zend\Form\Element\Select',
				'options' => array (
						'label' => 'Service d\'accueil'
				),
				'attributes' => array (
						'registerInArrrayValidator' => false,
						'id' => 'id_service_externe',
				)
		) );
		
		$this->add ( array (
				'name' => 'date_debut_externe',
				'type' => 'text',
				'options' => array (
						'label' => 'Date debut',
				),
				'attributes' => array (
						'id' => 'date_debut_externe',
				)
		) );
		
		$this->add ( array (
				'name' => 'date_fin_externe',
				'type' => 'text',
				'options' => array (
						'label' => 'Date fin',
				),
				'attributes' => array (
						'id' => 'date_fin_externe',
				)
		) );
		
		$this->add ( array (
				'name' => 'motif_intervention_externe',
				'type' => 'Textarea',
				'options' => array (
						'label' => 'Motif'
				),
				'attributes' => array (
						'id' => 'motif_intervention_externe',
				)
		) );
		
		$this->add ( array (
				'name' => 'note_externe',
				'type' => 'Textarea',
				'options' => array (
						'label' => 'Note'
				),
				'attributes' => array (
						'id' => 'note_externe',
				)
		) );
		
	}
}