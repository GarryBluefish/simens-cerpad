<?php
namespace Infirmerie\Form;

use Zend\Form\Form;


class BilanForm extends Form{

	public function __construct() {

		parent::__construct ();

		$this->add ( array (
				'name' => 'idfacturation',
				'type' => 'Hidden',
				'attributes' => array(
						'id' => 'idfacturation'
				)
		) );

		
		$this->add ( array (
				'name' => 'nb_tube',
				'type' => 'Text',
				'options' => array (
						'label' => iconv('ISO-8859-1', 'UTF-8','Nombre de prélèvements'),
				),
				'attributes' => array (
						'id' =>'nb_tube',
						'required' => true,
				)
		) );
		
		
		$this->add ( array (
				'name' => 'date_heure',
				'type' => 'Text',
				'options' => array (
						'label' => iconv('ISO-8859-1', 'UTF-8','Date & Heure '),
				),
				'attributes' => array (
						'id' =>'date_heure',
						'required' => true,
				)
		) );
		
		
		$this->add ( array (
				'name' => 'a_jeun',
				'type' => 'Select',
				'options' => array (
						'label' => iconv('ISO-8859-1', 'UTF-8','A jeun'),
						'value_options' => array (
								'1'=>'Oui',
								'0'=>'Non',
						)
				),
				'attributes' => array (
						'id' =>'a_jeun',
						'required' => true,
				)
		) );
		
		
		$this->add ( array (
				'name' => 'difficultes',
				'type' => 'Select',
				'options' => array (
						'label' => iconv('ISO-8859-1', 'UTF-8','Difficultés'),
						'value_options' => array (
								'1'=>'Oui',
								'0'=>'Non',
						)
				),
				'attributes' => array (
						'registerInArrrayValidator' => true,
						'onchange' => 'getdifficultes(this.value)',
						'id' =>'difficultes',
						'required' => true,
				)
		) );
		
		
		$this->add ( array (
				'name' => 'difficultes_prelevement',
				'type' => 'Textarea',
				'options' => array (
						'label' => iconv('ISO-8859-1', 'UTF-8','Difficultés rencontrées'),
				),
				'attributes' => array (
						'id' =>'difficultes_prelevement',
				)
		) );

		
		$this->add ( array (
				'name' => 'transfuser',
				'type' => 'Select',
				'options' => array (
						'label' => iconv('ISO-8859-1', 'UTF-8','Transfuser'),
						'value_options' => array (
								'1'=>'Oui',
								'0'=>'Non',
						)
				),
				'attributes' => array (
						'registerInArrrayValidator' => true,
						'onchange' => 'getMomentTransfusion(this.value)',
						'id' =>'transfuser',
						'required' => true,
				)
		) );
		
		
		$this->add ( array (
				'name' => 'moment_transfusion',
				'type' => 'Select',
				'options' => array (
						'label' => iconv('ISO-8859-1', 'UTF-8','3 derniers mois'),
						'value_options' => array (
								'1'=>'Oui',
								'0'=>'Non',
						)
				),
				'attributes' => array (
						'id' =>'moment_transfusion',
				)
		) );
		
		
		$this->add ( array (
				'name' => 'diagnostic',
				'type' => 'Textarea',
				'options' => array (
						'label' => iconv('ISO-8859-1', 'UTF-8','Diagnostic'),
				),
				'attributes' => array (
						'id' =>'diagnostic',
				)
		) );
		
		
		$this->add ( array (
				'name' => 'traitement',
				'type' => 'Textarea',
				'options' => array (
						'label' => iconv('ISO-8859-1', 'UTF-8','Traitement'),
				),
				'attributes' => array (
						'id' =>'traitement',
				)
		) );
		
	}
}