<?php

namespace Personnel\Form;

use Zend\Form\Form;

class TransfertPersonnelForm extends Form {
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
				'name' => 'id_personne',
				'type' => 'Hidden',
				'attributes' => array (
						'id' => 'id_personne'
				)
		) );
		
		$this->add ( array (
				'name' => 'type_transfert',
				'type' => 'Zend\Form\Element\Select',
				'options' => array (
						'label' => 'Type transfert',
						'value_options' => array (
								'Interne' => 'Interne',
								'Externe' => 'Externe',
						)
				),
				'attributes' => array (
						'id' => 'type_transfert',
						'onchange' => 'getChamps(this.value)',
						//'required' => true,
				)
		) );
/*************************************************************************/
/*=========================== TRANSFERT INTERNE =========================*/
		
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
				'name' => 'service_accueil',
				'type' => 'Zend\Form\Element\Select',
				'options' => array (
						'label' => 'Service d\'accueil',
				),
				'attributes' => array (
						'id' => 'service_accueil',
						//'required' => true,
				)
		) );
		
		$this->add ( array (
				'name' => 'motif_transfert',
				'type' => 'Textarea',
				'options' => array (
						'label' => 'Motif'
				),
				'attributes' => array (
						'id' => 'motif_transfert',
						//'required' => true,
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
/*=========================== TRANSFERT EXTERNE =========================*/
		
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
				'name' => 'service_accueil_externe',
				'type' => 'Zend\Form\Element\Select',
				'options' => array (
						'label' => 'Service d\'accueil'
				),
				'attributes' => array (
						'registerInArrrayValidator' => false,
						'id' => 'service_accueil_externe',
				)
		) );
		
		$this->add ( array (
				'name' => 'motif_transfert_externe',
				'type' => 'Textarea',
				'options' => array (
						'label' => 'Motif'
				),
				'attributes' => array (
						'id' => 'motif_transfert_externe',
				)
		) );
		
	}
}