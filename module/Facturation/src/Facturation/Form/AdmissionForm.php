<?php
namespace Facturation\Form;

use Zend\Form\Form;


class AdmissionForm extends Form{

	//protected $serviceTable;
	public function __construct() {
		//$this->serviceTable = $serviceTable;
		parent::__construct ();

		$this->add ( array (
				'name' => 'idpatient',
				'type' => 'Hidden',
				'attributes' => array(
						'id' => 'idpatient'
				)
		) );

		$this->add ( array (
		    'name' => 'listeanalysesselectionnees',
		    'type' => 'Hidden',
		    'attributes' => array(
		        'id' => 'listeanalysesselectionnees'
		    )
		) );
		
		$this->add ( array (
				'name' => 'service',
				'type' => 'Select',
				'options' => array (
						'label' => iconv('ISO-8859-1', 'UTF-8','Service'),
						'value_options' => array (
								''=>''
						)
				),
				'attributes' => array (
						'registerInArrrayValidator' => true,
						'onchange' => 'getmontant(this.value)',
						'id' =>'service',
						'required' => true,
				)
		) );

		$this->add ( array (
				'name' => 'grand_total_majoration',
				'type' => 'Text',
				'options' => array (
						'label' => iconv('ISO-8859-1', 'UTF-8','Grand Total (FCFA)')
				),
				'attributes' => array (
						'id' => 'grand_total_majoration',
						'style' => 'width: 70%;',
						'readonly' => true,
				)
		) );
		
		$this->add ( array (
				'name' => 'montant_avec_majoration',
				'type' => 'Hidden',
				'attributes' => array (
						'id' => 'montant_avec_majoration',
				)
				
		) );
		
		$this->add ( array (
				'name' => 'montant_avec_majoration_vue',
				'type' => 'Text',
				'options' => array (
						'label' => iconv('ISO-8859-1', 'UTF-8','Tarif à régler (FCFA)')
				),
				'attributes' => array (
						'id' => 'montant_avec_majoration_vue',
						'required' => true,
						'style' => 'width: 70%;',
						'readonly' => true,
				)
		) );
		
		$this->add ( array (
				'name' => 'montant',
				'type' => 'Hidden',
				'attributes' => array (
						'id' => 'montant',
				)
		) );

		$this->add ( array (
				'name' => 'numero',
				'type' => 'Text',
				'options' => array (
						'label' => iconv('ISO-8859-1', 'UTF-8','Numéro facture')
				),
				'attributes' => array (
						'id' => 'numero'
				)
		) );
		$this->add ( array (
				'name' => 'liste_service',
				'type' => 'Select',
				'options' => array (
						'value_options' => array (
								''=>''
						)
				),
				'attributes' => array (
						'id' => 'liste_service',
				)
		) );
		
		$this->add(array(
				'name' => 'type_facturation',
				'type' => 'Radio',
				'options' => array (
						'value_options' => array(
								1 => 'Normal',
								2 => iconv ( 'ISO-8859-1', 'UTF-8','Prise en charge') ,
						),
				),
				'attributes' => array(
						'id' => 'type_facturation',
						'required' => true,
				),
		));
		
		$this->add(array(
				'name' => 'organisme',
				'type' => 'Select',
				'options' => array (
						'label' => iconv('ISO-8859-1', 'UTF-8','Organisme')
				),
				'attributes' => array(
						'onchange' => 'getTauxOrganisme(this.value)',
						'id' => 'organisme',
				),
		));
		
		$this->add(array(
				'name' => 'taux',
				'type' => 'Select',
				'options' => array (
						'label' => iconv('ISO-8859-1', 'UTF-8','Taux (%)'),
						'value_options' => array(
								20 => '20',
						),
				),
				'attributes' => array(
						'registerInArrrayValidator' => true,
						'onchange' => 'getTarif(this.value)',
						'id' => 'taux',
						'disabled' => true,
				),
		));
		
		$this->add(array(
				'name' => 'taux_defaut',
				'type' => 'Hidden',
				'attributes' => array(
						'id' => 'taux_defaut',
				),
		));
		
	}
}