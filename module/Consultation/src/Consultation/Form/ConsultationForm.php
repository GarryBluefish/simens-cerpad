<?php

namespace Consultation\Form;

use Zend\Form\Form;

class ConsultationForm extends Form {
	public $decor = array (
			'ViewHelper'
	);
	public function __construct($name = null) {
		parent::__construct ();
		$codeConsultations = (new \DateTime ( 'now' ) )->format ( 'dmy_His' );
		$date = (new \DateTime ( 'now' ) )->format ( 'Y-m-d' );
		$heure = (new \DateTime ( 'now' ) )->format ( 'H:i:s' );

		
		$this->add ( array (
				'name' => 'idcons',
				'type' => 'hidden',
				'options' => array (
						'label' => 'Code consultation'
				),
				'attributes' => array (
						'readonly' => 'readonly',
						'value' => 'c_'.$codeConsultations,
						'id' => 'idcons'
				)
		) );
		
		$this->add ( array (
				'name' => 'date',
				'type' => 'Hidden',
				'attributes' => array (
						'value' => $date
				)
		) );
		
		$this->add ( array (
				'name' => 'heure',
				'type' => 'Hidden',
				'attributes' => array (
						'value' => $heure
				)
		) );
		
		$this->add ( array (
				'name' => 'idmedecin',
				'type' => 'Hidden',
				'attributes' => array (
						'id' => 'idmedecin'
				)
		) );
		
		$this->add ( array (
				'name' => 'idinfirmier',
				'type' => 'Hidden',
				'attributes' => array (
						'id' => 'idinfirmier'
				)
		) );
		
		$this->add ( array (
				'name' => 'idpatient',
				'type' => 'Hidden',
				'attributes' => array (
						'id' => 'idpatient'
				)
		) );
		
		/**
		 * ********* LES MOTIFS D ADMISSION *************
		 */
		/**
		 * ********* LES MOTIFS D ADMISSION *************
		 */
		$this->add ( array (
				'name' => 'motif_admission1',
				'type' => 'Select',
				'options' => array (
						'label' => 'motif 1'
				),
				'attributes' => array (
						'readonly' => 'readonly',
						'id' => 'motif_admission1',
						'class' => 'motif_admission_liste_fixe',
						'onchange' => 'getMotifAdmissionDouleurFievre(this.value)',
				)
		) );
		$this->add ( array (
				'name' => 'motif_admission2',
				'type' => 'Select',
				'options' => array (
						'label' => 'motif 2'
				),
				'attributes' => array (
						'readonly' => 'readonly',
						'id' => 'motif_admission2',
						'class' => 'motif_admission_liste_fixe',
						'onchange' => 'getMotifAdmissionDouleurFievre(this.value)',
				)
		) );
		$this->add ( array (
				'name' => 'motif_admission3',
				'type' => 'Select',
				'options' => array (
						'label' => 'motif 3'
				),
				'attributes' => array (
						'readonly' => 'readonly',
						'id' => 'motif_admission3',
						'class' => 'motif_admission_liste_fixe',
						'onchange' => 'getMotifAdmissionDouleurFievre(this.value)',
				)
		) );
		$this->add ( array (
				'name' => 'motif_admission4',
				'type' => 'Select',
				'options' => array (
						'label' => 'motif 4'
				),
				'attributes' => array (
						'readonly' => 'readonly',
						'id' => 'motif_admission4',
						'class' => 'motif_admission_liste_fixe',
						'onchange' => 'getMotifAdmissionDouleurFievre(this.value)',
				)
		) );
		$this->add ( array (
				'name' => 'motif_admission5',
				'type' => 'Select',
				'options' => array (
						'label' => 'motif 5'
				),
				'attributes' => array (
						'readonly' => 'readonly',
						'id' => 'motif_admission5',
						'class' => 'motif_admission_liste_fixe',
						'onchange' => 'getMotifAdmissionDouleurFievre(this.value)',
				)
		) );
		
		$this->add ( array (
				'name' => 'siege',
				'type' => 'Select',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8', 'Siège')
				),
				'attributes' => array (
						'id' => 'siege'
				)
		) );
		$this->add ( array (
				'name' => 'intensite',
				'type' => 'number',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8', 'Intensité échelle (EVA)'),
				),
				'attributes' => array (
						'id' => 'intensite',
						'class' => 'intensiteClassStyle',
						'max' => 10,
						'min' => 1,
						'step' => 'any',
				)
		) );
		
		/**
		 * ************************* CONSTANTES *****************************************************
		 */
		/**
		 * ************************* CONSTANTES *****************************************************
		 */
		/**
		 * ************************* CONSTANTES *****************************************************
		 */
		$this->add ( array (
				'name' => 'poids',
				'type' => 'number',
				'options' => array (
						'label' => 'Poids (kg)'
				),
				'attributes' => array (
						'max' => 500,
						'min' => 0,
						'id' => 'poids',
						'class' => 'poids',
						'required' => true,
				)
		) );
		$this->add ( array (
				'name' => 'taille',
				'type' => 'number',
				'options' => array (
						'label' => 'Taille (cm)'
				),
				'attributes' => array (
						'max' => 200,
						'min' => 45,
						'id' => 'taille',
						'required' => true,
				)
		) );
		$this->add ( array (
				'name' => 'temperature',
				'type' => 'number',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8', 'Température (°C)' )
				),
				'attributes' => array (
						'max' => 45,
						'min' => 34,
						'id' => 'temperature',
						'step' => 'any',
						'required' => true,
				)
		) );
		
		$this->add ( array (
				'name' => 'perimetre_cranien',
				'type' => 'number',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8', 'Perimètre cranien (cm)'),
				),
				'attributes' => array (
						'min' => 30,
						'max' => 55,
						'id' => 'perimetre_cranien',
				)
		) );
		
		$this->add ( array (
				'name' => 'tensionmaximale',
				'type' => 'Text',
				'attributes' => array (
						'class' => 'tension_only_numeric',
						'id' => 'tensionmaximale'
				)
		) );
		
		$this->add ( array (
				'name' => 'tensionminimale',
				'type' => 'Text',
				'attributes' => array (
						'class' => 'tension_only_numeric',
						'id' => 'tensionminimale'
				)
		) );
		
		$this->add ( array (
				'name' => 'pouls',
				'type' => 'Text',
				'options' => array (
						'label' => 'Pouls (bat/min)'
				),
				'attributes' => array (
						'class' => 'pouls_only_numeric',
						'readonly' => 'readonly',
						'id' => 'pouls'
				)
		) );
		$this->add ( array (
				'name' => 'frequence_respiratoire',
				'type' => 'Text',
				'options' => array (
						'label' => iconv('ISO-8859-1', 'UTF-8','Fréquence respiratoire')
				),
				'attributes' => array (
						'class' => 'frequence_only_numeric',
						'readonly' => 'readonly',
						'id' => 'frequence_respiratoire'
				)
		) );
		$this->add ( array (
				'name' => 'glycemie_capillaire',
				'type' => 'Text',
				'options' => array (
						'label' => iconv('ISO-8859-1', 'UTF-8', 'Glycémie capillaire (g/l)')
				),
				'attributes' => array (
						'class' => 'glycemie_only_numeric',
						'readonly' => 'readonly',
						'id' => 'glycemie_capillaire'
				)
		) );
		
		$this->add ( array (
				'name' => 'perimetre_brachial',
				'type' => 'Text',
				'options' => array (
						'label' => 'Perimetre brachial'
				),
				'attributes' => array (
						'id' => 'perimetre_brachial'
				)
		) );

		
		$this->add ( array (
				'name' => 'perimetre_thoracique',
				'type' => 'Text',
				'options' => array (
						'label' => 'Perimetre thoracique'
				),
				'attributes' => array (
						'id' => 'perimetre_thoracique'
				)
		) );
		
		
		$this->add ( array (
				'name' => 'voie_med_1',
				'type' => 'hidden',
				'attributes' => array (
						'id' => 'voie_med_1'
				)
		) );
		$this->add ( array (
				'name' => 'voie_med_2',
				'type' => 'hidden',
				'attributes' => array (
						'id' => 'voie_med_2'
				)
		) );
		$this->add ( array (
				'name' => 'voie_med_3',
				'type' => 'hidden',
				'attributes' => array (
						'id' => 'voie_med_3'
				)
		) );
		$this->add ( array (
				'name' => 'voie_med_4',
				'type' => 'hidden',
				'attributes' => array (
						'id' => 'voie_med_4'
				)
		) );
		$this->add ( array (
				'name' => 'voie_med_5',
				'type' => 'hidden',
				'attributes' => array (
						'id' => 'voie_med_5'
				)
		) );
		$this->add ( array (
				'name' => 'voie_med_6',
				'type' => 'hidden',
				'attributes' => array (
						'id' => 'voie_med_6'
				)
		) );
		
		
		//ANTECEDENTS FAMILIAUX --- ANTECEDENTS FAMILIAUX --- ANTECEDENTS FAMILIAUX
		//ANTECEDENTS FAMILIAUX --- ANTECEDENTS FAMILIAUX --- ANTECEDENTS FAMILIAUX
		//CONSANGUINITE
		//CONSANGUINITE
		//CONSANGUINITE
		$this->add ( array (
				'name' => 'consanguiniteAF',
				'type' => 'Zend\Form\Element\Select',
				'options' => array (
						'value_options' => array (
								0 => iconv ( 'ISO-8859-1', 'UTF-8', '' ),
								1 => iconv ( 'ISO-8859-1', 'UTF-8', 'OUI' ),
								2 => iconv ( 'ISO-8859-1', 'UTF-8', 'NON' )
						)
				),
				'attributes' => array (
						'id' => 'consanguiniteAF'
				)
		) );
		
		$this->add ( array (
				'name' => 'degreAF',
				'type' => 'Zend\Form\Element\Select',
				'options' => array (
						'value_options' => array (
								0 => iconv ( 'ISO-8859-1', 'UTF-8', '' ),
								1 => iconv ( 'ISO-8859-1', 'UTF-8', '1' ),
								2 => iconv ( 'ISO-8859-1', 'UTF-8', '2' ),
								3 => iconv ( 'ISO-8859-1', 'UTF-8', '3' ),
								4 => iconv ( 'ISO-8859-1', 'UTF-8', 'Sup' ),
						)
				),
				'attributes' => array (
						'id' => 'degreAF'
				)
		) );
		
		//STATUT DREPANOCYTOSE PARENTS
		//STATUT DREPANOCYTOSE PARENTS
		//STATUT DREPANOCYTOSE PARENTS
		$this->add ( array (
				'name' => 'statutDrepanocytoseMereAF',
				'type' => 'Zend\Form\Element\Select',
				'options' => array (
						'value_options' => array (
								'' => iconv ( 'ISO-8859-1', 'UTF-8', '' ),
								'AS' => iconv ( 'ISO-8859-1', 'UTF-8', 'AS' ),
								'AC' => iconv ( 'ISO-8859-1', 'UTF-8', 'AC' ),
								'A-Bth' => iconv ( 'ISO-8859-1', 'UTF-8', 'A-Bth' ),
								'SS' => iconv ( 'ISO-8859-1', 'UTF-8', 'SS' ),
								'SC' => iconv ( 'ISO-8859-1', 'UTF-8', 'SC' ),
								'S-Bth' => iconv ( 'ISO-8859-1', 'UTF-8', 'S-Bth' ),
								'Autres' => iconv ( 'ISO-8859-1', 'UTF-8', 'Autres ...' ),
								'Inconnu' => iconv ( 'ISO-8859-1', 'UTF-8', 'Inconnu' ),
						)
				),
				'attributes' => array (
						'id' => 'statutDrepanocytoseMereAF'
				)
		) );
		
		$this->add ( array (
				'name' => 'statutDrepanocytosePereAF',
				'type' => 'Zend\Form\Element\Select',
				'options' => array (
						'value_options' => array (
								'' => iconv ( 'ISO-8859-1', 'UTF-8', '' ),
								'AS' => iconv ( 'ISO-8859-1', 'UTF-8', 'AS' ),
								'AC' => iconv ( 'ISO-8859-1', 'UTF-8', 'AC' ),
								'A-Bth' => iconv ( 'ISO-8859-1', 'UTF-8', 'A-Bth' ),
								'SS' => iconv ( 'ISO-8859-1', 'UTF-8', 'SS' ),
								'SC' => iconv ( 'ISO-8859-1', 'UTF-8', 'SC' ),
								'S-Bth' => iconv ( 'ISO-8859-1', 'UTF-8', 'S-Bth' ),
								'Autres' => iconv ( 'ISO-8859-1', 'UTF-8', 'Autres ...' ),
								'Inconnu' => iconv ( 'ISO-8859-1', 'UTF-8', 'Inconnu' ),
						)
				),
				'attributes' => array (
						'id' => 'statutDrepanocytosePereAF'
				)
		) );
		
		//FRATRIE
		//FRATRIE
		//FRATRIE
		$this->add ( array (
				'name' => 'fratrieTailleAF',
				'type' => 'number',
				'attributes' => array (
						'id' => 'fratrieTailleAF',
						'min' => 1,
						'max' =>  15,
				)
		) );
		
		$this->add ( array (
				'name' => 'fratrieTailleFilleAF',
				'type' => 'number',
				'attributes' => array (
						'id' => 'fratrieTailleFilleAF',
						'min' => 0,
						'max' =>  15,
				)
		) );
		
		$this->add ( array (
				'name' => 'fratrieTailleGarconAF',
				'type' => 'number',
				'attributes' => array (
						'id' => 'fratrieTailleGarconAF',
						'min' => 0,
						'max' =>  15,
				)
		) );
		
		$this->add ( array (
				'name' => 'fratrieRangAF',
				'type' => 'Zend\Form\Element\Select',
				'options' => array (
						'value_options' => array (
								'' => iconv ( 'ISO-8859-1', 'UTF-8', '' ),
								'1er' => iconv ( 'ISO-8859-1', 'UTF-8', '1er' ),
								'2eme' => iconv ( 'ISO-8859-1', 'UTF-8', '2ème' ),
								'3eme' => iconv ( 'ISO-8859-1', 'UTF-8', '3ème' ),
						)
				),
				'attributes' => array (
						'id' => 'fratrieRangAF',
						'min' => 1,
						'max' =>  15,
				)
		) );
		
		//AUTRES MALADIES FAMILIALES
		//AUTRES MALADIES FAMILIALES
		//AUTRES MALADIES FAMILIALES
		
		/**Allergies**/
		$this->add ( array (
				'name' => 'AllergiesAF',
				'type' => 'checkbox',
				'attributes' => array (
						'id' => 'AllergiesAF',
				)
		) );
		
		/**Asthme**/
		$this->add ( array (
				'name' => 'AsthmeAF',
				'type' => 'checkbox',
				'attributes' => array (
						'id' => 'AsthmeAF'
				)
		) );
		
		/**Diabete**/
		$this->add ( array (
				'name' => 'DiabeteAF',
				'type' => 'checkbox',
				'attributes' => array (
						'id' => 'DiabeteAF'
				)
		) );
		
		/**HTA**/
		$this->add ( array (
				'name' => 'HtaAF',
				'type' => 'checkbox',
				'attributes' => array (
						'id' => 'HtaAF'
				)
		) );
		
		/*Autres*/
		$this->add ( array (
				'name' => 'AutresAF',
				'type' => 'checkbox',
				'attributes' => array (
						'id' => 'AutresAF'
				)
		) );
		
		
		//CONSULTATION DU JOUR --- CONSULTATION DU JOUR --- CONSULTATION DU JOUR --- CONSULTATION DU JOUR
		//CONSULTATION DU JOUR --- CONSULTATION DU JOUR --- CONSULTATION DU JOUR --- CONSULTATION DU JOUR

		/**** HISTOIRE DE LA MALADIE ****/
		/**** HISTOIRE DE LA MALADIE ****/
		$this->add ( array (
				'name' => 'criseHM',
				'type' => 'Zend\Form\Element\Select',
				'options' => array (
						'value_options' => array (
								'' => iconv ( 'ISO-8859-1', 'UTF-8', '' ),
								'1' => iconv ( 'ISO-8859-1', 'UTF-8', 'Oui' ),
								'0' => iconv ( 'ISO-8859-1', 'UTF-8', 'Non' ),
						)
				),
				'attributes' => array (
						'onchange' => 'getInfoCrise(this.value)',
						'id' => 'criseHM',
						'style' => 'float:right'
				)
		) );
		
		$this->add ( array (
				'name' => 'nombre_criseHM',
				'type' => 'Number',
				'options' => array (
				),
				'attributes' => array (
						'id' => 'nombre_criseHM',
				)
		) );
		
		$this->add ( array (
				'name' => 'dureeHM',
				'type' => 'Select',
				'options' => array (
						'value_options' => array (
								'' => iconv ( 'ISO-8859-1', 'UTF-8', '' ),
								'< 24h' => iconv ( 'ISO-8859-1', 'UTF-8', '< 24h' ),
								'24h a 48h' => iconv ( 'ISO-8859-1', 'UTF-8', '24h à 48h' ),
								'>= 72h' => iconv ( 'ISO-8859-1', 'UTF-8', '>= 72h' ),
								'72h a 7j' => iconv ( 'ISO-8859-1', 'UTF-8', '72h à 7j' ),
								'> 7j' => iconv ( 'ISO-8859-1', 'UTF-8', '> 7j' ),
						)
				),
				'attributes' => array (
						'id' => 'dureeHM',
						'style' => 'width:120px',
				)
		) );
		
		$this->add ( array (
				'name' => 'facteur_declenchantHM',
				'type' => 'Select',
				'options' => array (
						'value_options' => array (
								'' => iconv ( 'ISO-8859-1', 'UTF-8', '' ),
								'fievre' => iconv ( 'ISO-8859-1', 'UTF-8', 'Fièvre' ),
								'refroidissement' => iconv ( 'ISO-8859-1', 'UTF-8', 'Refroidissement' ),
								'activite_intense' => iconv ( 'ISO-8859-1', 'UTF-8', 'Activité intense' ),
								'Neant' => iconv ( 'ISO-8859-1', 'UTF-8', 'Néant' ),
								'Autre' => iconv ( 'ISO-8859-1', 'UTF-8', 'Autre' ),
						)
				),
				'attributes' => array (
						'id' => 'facteur_declenchantHM',
						'style' => 'width:170px;font-size: 16px;',
				)
		) );
		
		
		$this->add ( array (
				'name' => 'episodeFievreHM',
				'type' => 'Zend\Form\Element\Select',
				'options' => array (
						'value_options' => array (
								'' => iconv ( 'ISO-8859-1', 'UTF-8', '' ),
								'1' => iconv ( 'ISO-8859-1', 'UTF-8', 'Oui' ),
								'0' => iconv ( 'ISO-8859-1', 'UTF-8', 'Non' ),
						)
				),
				'attributes' => array (
						'onchange' => 'getInfoEpisodeFievre(this.value)',
						'id' => 'episodeFievreHM',
						'style' => 'float:right'
				)
		) );
		
		$this->add ( array (
				'name' => 'episodeFievreSiOuiHM',
				'type' => 'Zend\Form\Element\Select',
				'options' => array (
						'value_options' => array (
								'' => iconv ( 'ISO-8859-1', 'UTF-8', '' ),
								'1' => iconv ( 'ISO-8859-1', 'UTF-8', 'Signe respiratoire et/ou ORL' ),
								'2' => iconv ( 'ISO-8859-1', 'UTF-8', 'Diarrhée et/ou vomissements' ),
								'3' => iconv ( 'ISO-8859-1', 'UTF-8', 'Autre..' ),
						)
				),
				'attributes' => array (
						'id' => 'episodeFievreSiOuiHM',
						'style' => 'float:right'
				)
		) );
		
		$this->add ( array (
				'name' => 'hospitalisationHM',
				'type' => 'Zend\Form\Element\Select',
				'options' => array (
						'value_options' => array (
								'' => iconv ( 'ISO-8859-1', 'UTF-8', '' ),
								'1' => iconv ( 'ISO-8859-1', 'UTF-8', 'Oui' ),
								'0' => iconv ( 'ISO-8859-1', 'UTF-8', 'Non' ),
						)
				),
				'attributes' => array (
						'onchange' => 'getHospitalisationHM(this.value)',
						'id' => 'hospitalisationHM',
						'style' => 'float:right'
				)
		) );
		
		$this->add ( array (
				'name' => 'dateHospitalisationHM',
				'type' => 'Date',
				'attributes' => array (
						'id' => 'dateHospitalisationHM',
						'style' => 'float:right;width: 75%;'
				)
		) );
		
		$this->add ( array (
				'name' => 'dureeHospitalisationHM',
				'type' => 'Number',
				'attributes' => array (
						'id' => 'dureeHospitalisationHM',
						'style' => 'float:right;width: 40%;'
				)
		) );
		
		$this->add ( array (
				'name' => 'motifHospitalisationHM',
				'type' => 'Text',
				'attributes' => array (
						'id' => 'motifHospitalisationHM',
						'style' => 'float:right;width: 75%;'
				)
		) );
		
		$this->add ( array (
				'name' => 'priseEnChargeHospitalisationHM',
				'type' => 'Zend\Form\Element\Select',
				'options' => array (
						'value_options' => array (
								'' => iconv ( 'ISO-8859-1', 'UTF-8', '' ),
								'1' => iconv ( 'ISO-8859-1', 'UTF-8', 'Transfusion' ),
								'2' => iconv ( 'ISO-8859-1', 'UTF-8', 'Perfusion' ),
								'3' => iconv ( 'ISO-8859-1', 'UTF-8', 'Antibiotique' ),
								'4' => iconv ( 'ISO-8859-1', 'UTF-8', 'Autre..' ),
						)
				),
				'attributes' => array (
						'onchange' => 'getPriseEnChargeHospitalisationHM(this.value)',
						'id' => 'priseEnChargeHospitalisationHM',
						'style' => 'float:right;width: 50%;'
				)
		) );
		
		$this->add ( array (
				'name' => 'nombreHospitalisationHM',
				'type' => 'Number',
				'attributes' => array (
						'id' => 'nombreHospitalisationHM',
						'style' => 'float:right;width: 40%;'
				)
		) );
		/*** Interrogatoire (description des symptômes) ***/
		/*** Interrogatoire (description des symptômes) ***/
		
		$this->add ( array (
				'name' => 'motif_interrogatoire_1',
				'type' => 'Text',
				'attributes' => array (
						'id' => 'motif_interrogatoire_1',
						'style' => 'float:right;width: 60%;'
				)
		) );
		
		$this->add ( array (
				'name' => 'motif_interrogatoire_2',
				'type' => 'Text',
				'attributes' => array (
						'id' => 'motif_interrogatoire_2',
						'style' => 'float:right;width: 60%;'
				)
		) );
		
		$this->add ( array (
				'name' => 'motif_interrogatoire_3',
				'type' => 'Text',
				'attributes' => array (
						'id' => 'motif_interrogatoire_3',
						'style' => 'float:right;width: 60%;'
				)
		) );
		
		$this->add ( array (
				'name' => 'motif_interrogatoire_4',
				'type' => 'Text',
				'attributes' => array (
						'id' => 'motif_interrogatoire_4',
						'style' => 'float:right;width: 60%;'
				)
		) );
		
		$this->add ( array (
				'name' => 'motif_interrogatoire_5',
				'type' => 'Text',
				'attributes' => array (
						'id' => 'motif_interrogatoire_5',
						'style' => 'float:right;width: 60%;'
				)
		) );
		
		
		/*** Suivi des traitements ***/
		/*** Suivi des traitements ***/
		
		$this->add ( array (
				'name' => 'suiviDesTraitements',
				'type' => 'Zend\Form\Element\Select',
				'options' => array (
						'value_options' => array (
								'' => iconv ( 'ISO-8859-1', 'UTF-8', '' ),
								'1' => iconv ( 'ISO-8859-1', 'UTF-8', 'Bien suivi' ),
								'2' => iconv ( 'ISO-8859-1', 'UTF-8', 'Mal suivi' ),
								'3' => iconv ( 'ISO-8859-1', 'UTF-8', 'Non suivi' ),
						)
				),
				'attributes' => array (
						'onchange' => 'getSuiviDesTraitements(this.value)',
						'id' => 'suiviDesTraitements',
						'style' => 'float:right'
				)
		) );
		
		$this->add ( array (
				'name' => 'suiviDesTraitementsPrecision',
				'type' => 'Text',
				'options' => array (
				),
				'attributes' => array (
						'id' => 'suiviDesTraitementsPrecision',
						'style' => 'float:right;width: 90%;',
				)
		) );
		
		/*** Mise à jour des vaccins ***/
		/*** Mise à jour des vaccins ***/
		
		$this->add ( array (
				'name' => 'misesAJourDesVaccins',
				'type' => 'Zend\Form\Element\Select',
				'options' => array (
						'value_options' => array (
								'' => iconv ( 'ISO-8859-1', 'UTF-8', '' ),
								'1' => iconv ( 'ISO-8859-1', 'UTF-8', 'Oui' ),
								'2' => iconv ( 'ISO-8859-1', 'UTF-8', 'Non' ),
						)
				),
				'attributes' => array (
						'onchange' => 'getMisesAJourDesVaccins(this.value)',
						'id' => 'misesAJourDesVaccins',
						'style' => 'float:right'
				)
		) );
		
		$this->add ( array (
				'name' => 'misesAJourDesVaccinsPrecision',
				'type' => 'Text',
				'options' => array (
				),
				'attributes' => array (
						'id' => 'misesAJourDesVaccinsPrecision',
						'style' => 'float:right;width: 90%;',
				)
		) );
		
		/*** Données de l'examen ***/
		/*** Données de l'examen ***/
		
		$this->add ( array (
				'name' => 'paleurDonneesExamen',
				'type' => 'Zend\Form\Element\Select',
				'options' => array (
						'value_options' => array (
								'' => iconv ( 'ISO-8859-1', 'UTF-8', '' ),
								'1' => iconv ( 'ISO-8859-1', 'UTF-8', 'Absente' ),
								'2' => iconv ( 'ISO-8859-1', 'UTF-8', 'Légère' ),
								'3' => iconv ( 'ISO-8859-1', 'UTF-8', 'Modérée' ),
								'4' => iconv ( 'ISO-8859-1', 'UTF-8', 'Sévère' ),
						)
				),
				'attributes' => array (
						'id' => 'paleurDonneesExamen',
						'style' => 'float:right'
				)
		) );
		
		$this->add ( array (
				'name' => 'ictereDonneesExamen',
				'type' => 'Zend\Form\Element\Select',
				'options' => array (
						'value_options' => array (
								'' => iconv ( 'ISO-8859-1', 'UTF-8', '' ),
								'1' => iconv ( 'ISO-8859-1', 'UTF-8', 'Absent' ),
								'2' => iconv ( 'ISO-8859-1', 'UTF-8', 'Subictère' ),
								'3' => iconv ( 'ISO-8859-1', 'UTF-8', 'Ictère franc' ),
						)
				),
				'attributes' => array (
						'id' => 'ictereDonneesExamen',
						'style' => 'float:right'
				)
		) );
		
		$this->add ( array (
				'name' => 'splenomegalieDonneesExamen',
				'type' => 'Zend\Form\Element\Select',
				'options' => array (
						'value_options' => array (
								'' => iconv ( 'ISO-8859-1', 'UTF-8', '' ),
								'1' => iconv ( 'ISO-8859-1', 'UTF-8', 'Oui' ),
								'0' => iconv ( 'ISO-8859-1', 'UTF-8', 'Non' ),
						)
				),
				'attributes' => array (
						'onchange' => 'getSplenomegalieDonneesExamen(this.value)',
						'id' => 'splenomegalieDonneesExamen',
						'style' => 'float:right'
				)
		) );
		
		$this->add ( array (
				'name' => 'tailleDonneesExamen',
				'type' => 'Number',
				'attributes' => array (
						'id' => 'tailleDonneesExamen',
						'style' => 'float:right;width: 35%;',
						'max' => 10,
						'min' => 1,
				)
		) );
		
		/**ORL**/
		$this->add ( array (
				'name' => 'orlObstructionNasaleDonneesExamen',
				'type' => 'checkbox',
				'attributes' => array (
						'id' => 'orlObstructionNasaleDonneesExamen',
				)
		) );
		
		$this->add ( array (
				'name' => 'orlRhiniteDonneesExamen',
				'type' => 'checkbox',
				'attributes' => array (
						'id' => 'orlRhiniteDonneesExamen',
				)
		) );
		
		$this->add ( array (
				'name' => 'orlHypertrophieAmygdalesDonneesExamen',
				'type' => 'checkbox',
				'attributes' => array (
						'id' => 'orlHypertrophieAmygdalesDonneesExamen',
				)
		) );
		
		$this->add ( array (
				'name' => 'orlAngineDonneesExamen',
				'type' => 'checkbox',
				'attributes' => array (
						'id' => 'orlAngineDonneesExamen',
				)
		) );
		
		$this->add ( array (
				'name' => 'orlOtiteDonneesExamen',
				'type' => 'checkbox',
				'attributes' => array (
						'id' => 'orlOtiteDonneesExamen',
				)
		) );
		
		/**Examen des poumons**/
		$this->add ( array (
				'name' => 'examenDesPoumonsDonneesExamen',
				'type' => 'Zend\Form\Element\Select',
				'options' => array (
						'value_options' => array (
								'' => iconv ( 'ISO-8859-1', 'UTF-8', '' ),
								'1' => iconv ( 'ISO-8859-1', 'UTF-8', 'Normal' ),
								'-1' => iconv ( 'ISO-8859-1', 'UTF-8', 'Anormal' ),
						)
				),
				'attributes' => array (
						'onchange' => 'getExamenDesPoumonsDonneesExamen(this.value)',
						'id' => 'examenDesPoumonsDonneesExamen',
						'style' => 'float:right'
				)
		) );
		
		$this->add ( array (
				'name' => 'precisionExamenDesPoumonsDonneesExamen',
				'type' => 'Zend\Form\Element\Text',
				'attributes' => array (
						'id' => 'precisionExamenDesPoumonsDonneesExamen',
						'style' => 'float:right;width:85%;'
				)
		) );
		
		/**Examen du coeur**/
		$this->add ( array (
				'name' => 'examenDuCoeurDonneesExamen',
				'type' => 'Zend\Form\Element\Select',
				'options' => array (
						'value_options' => array (
								'' => iconv ( 'ISO-8859-1', 'UTF-8', '' ),
								'1' => iconv ( 'ISO-8859-1', 'UTF-8', 'Normal' ),
								'-1' => iconv ( 'ISO-8859-1', 'UTF-8', 'Anormal' ),
						)
				),
				'attributes' => array (
						'onchange' => 'getExamenDuCoeurDonneesExamen(this.value)',
						'id' => 'examenDuCoeurDonneesExamen',
						'style' => 'float:right'
				)
		) );
		
		$this->add ( array (
				'name' => 'precisionExamenDuCoeurDonneesExamen',
				'type' => 'Zend\Form\Element\Text',
				'attributes' => array (
						'id' => 'precisionExamenDuCoeurDonneesExamen',
						'style' => 'float:right;width:85%;'
				)
		) );
		
		/**Examen du foie et voie biliaire**/
		$this->add ( array (
				'name' => 'examenDuFoieVoieBiliaireDonneesExamen',
				'type' => 'Zend\Form\Element\Select',
				'options' => array (
						'value_options' => array (
								'' => iconv ( 'ISO-8859-1', 'UTF-8', '' ),
								'1' => iconv ( 'ISO-8859-1', 'UTF-8', 'Normal' ),
								'2' => iconv ( 'ISO-8859-1', 'UTF-8', "Douleur de l'HCD" ),
						)
				),
				'attributes' => array (
						'id' => 'examenDuFoieVoieBiliaireDonneesExamen',
						'style' => 'float:right'
				)
		) );
		
		/**Hanche**/
		$this->add ( array (
				'name' => 'examenHancheDonneesExamen',
				'type' => 'Zend\Form\Element\Select',
				'options' => array (
						'value_options' => array (
								'' => iconv ( 'ISO-8859-1', 'UTF-8', '' ),
								'1' => iconv ( 'ISO-8859-1', 'UTF-8', 'Normal' ),
								'2' => iconv ( 'ISO-8859-1', 'UTF-8', "Douleur" ),
						)
				),
				'attributes' => array (
						'id' => 'examenHancheDonneesExamen',
						'style' => 'float:right'
				)
		) );
		
		/**Epaule**/
		$this->add ( array (
				'name' => 'examenEpauleDonneesExamen',
				'type' => 'Zend\Form\Element\Select',
				'options' => array (
						'value_options' => array (
								'' => iconv ( 'ISO-8859-1', 'UTF-8', '' ),
								'1' => iconv ( 'ISO-8859-1', 'UTF-8', 'Normal' ),
								'2' => iconv ( 'ISO-8859-1', 'UTF-8', "Douleur" ),
						)
				),
				'attributes' => array (
						'id' => 'examenEpauleDonneesExamen',
						'style' => 'float:right'
				)
		) );
		
		/**Jambe**/
		$this->add ( array (
				'name' => 'examenJambeDonneesExamen',
				'type' => 'Zend\Form\Element\Select',
				'options' => array (
						'value_options' => array (
								'' => iconv ( 'ISO-8859-1', 'UTF-8', '' ),
								'1' => iconv ( 'ISO-8859-1', 'UTF-8', 'Normal' ),
								'2' => iconv ( 'ISO-8859-1', 'UTF-8', "Ulcère" ),
						)
				),
				'attributes' => array (
						'id' => 'examenJambeDonneesExamen',
						'style' => 'float:right'
				)
		) );
		
		/**Autres données d'examen**/
		$this->add ( array (
				'name' => 'autresDonneesExamen',
				'type' => 'Zend\Form\Element\Text',
				'attributes' => array (
						'id' => 'autresDonneesExamen',
						'style' => 'float:right;width:78%;'
				)
		) );
		
		/**Synth&eagrave;se de la consultation du jour**/
		$this->add ( array (
				'name' => 'syntheseConsultationDuJourDonneesExamen',
				'type' => 'Zend\Form\Element\Textarea',
				'attributes' => array (
						'id' => 'syntheseConsultationDuJourDonneesExamen',
						'style' => 'float:right; width:85%; min-width:85%; max-width:85%; height: 100%; min-height: 100%; max-height: 100%;'
				)
		) );
		
		/**Diagnostic --- Diagnostic --- Diagnostic **/
		/**Diagnostic --- Diagnostic --- Diagnostic **/
		$this->add ( array (
				'name' => 'diagnosticDuJourConsultation',
				'type' => 'Zend\Form\Element\Textarea',
				'attributes' => array (
						'id' => 'diagnosticDuJourConsultation',
						'style' => 'float:right; width: 305px; min-width:305px; max-width:305px; height: 205px; min-height: 205px; max-height: 205px;'
				)
		) );
		
		
		/**Complications aigues --- Complications aigues **/
		/**Complications aigues --- Complications aigues **/
		$this->add ( array (
				'name' => 'diagnosticComplicationsAigues',
				'type' => 'Zend\Form\Element\Select',
				'options' => array (
						'value_options' => array (
								'' => iconv ( 'ISO-8859-1', 'UTF-8', '' ),
								'1' => iconv ( 'ISO-8859-1', 'UTF-8', 'Anémie aigue' ),
								'2' => iconv ( 'ISO-8859-1', 'UTF-8', "Pneumonie" ),
						)
				),
				'attributes' => array (
						'id' => 'diagnosticComplicationsAigues',
						'style' => 'float:left; width: 98%'
				)
		) );
		
		/**Complications chroniques --- Complications chroniques **/
		/**Complications chroniques --- Complications chroniques **/
		$this->add ( array (
				'name' => 'diagnosticComplicationsChroniques',
				'type' => 'Zend\Form\Element\Select',
				'options' => array (
						'value_options' => array (
								'' => iconv ( 'ISO-8859-1', 'UTF-8', '' ),
								'1' => iconv ( 'ISO-8859-1', 'UTF-8', 'Anémie aigue' ),
								'2' => iconv ( 'ISO-8859-1', 'UTF-8', "Pneumonie" ),
						)
				),
				'attributes' => array (
						'id' => 'diagnosticComplicationsChroniques',
						'style' => 'float:left; width: 98%;'
				)
		) );
		
		
		
		
		
		
		
		

		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		/**
		 * ************* Autres (Transfert / hospitalisation / Rendez-vous! ************
		 */
		/**
		 * ************* Autres (Transfert / hospitalisation / Rendez-vous! ************
		 */
		/**
		 * ************* Autres (Transfert / hospitalisation / Rendez-vous! ************
		 */

		/* A))************** Transfert ************ */
		/*A))************** Transfert *************/
		$this->add ( array (
				'name' => 'hopital_accueil',
				'type' => 'Zend\Form\Element\Select',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8', 'Hopital d\'accueil :' ),
// 						'value_options' => array (
// 								'zzz' => 'zzz'
// 						)
				),
				'attributes' => array (
						'registerInArrrayValidator' => false,
						'onchange' => 'getservices(this.value)',
						'id' => 'hopital_accueil'
				)
		) );
		$this->add ( array (
				'name' => 'service_accueil',
				'type' => 'Zend\Form\Element\Select',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8', 'Service d\'accueil :' )
// 						'value_options' => array (
// 								'' => ''
// 						)
				),
				'attributes' => array (
						'registerInArrrayValidator' => false,
						'id' => 'service_accueil'
				)
		) );
		$this->add ( array (
				'name' => 'motif_transfert',
				'type' => 'Textarea',
				'options' => array (
						'label' => 'Motif du transfert :'
				),
				'attributes' => array (
						'readonly' => 'readonly',
						'id' => 'motif_transfert'
				)
		) );
		/* B))************** Hospitalisation ************ */
		/*B))************** Hospitalisation *************/
		$this->add ( array (
				'name' => 'motif_hospitalisation',
				'type' => 'Textarea',
				'options' => array (
						'label' => 'Motif hospitalisation :'
				),
				'attributes' => array (
						'id' => 'motif_hospitalisation'
				)
		) );
		
		$this->add ( array (
				'name' => 'date_fin_hospitalisation_prevue',
				'type' => 'Text',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8', 'Date fin prévue :'),
				),
				'attributes' => array (
						'id' => 'date_fin_hospitalisation_prevue'
				)
		) );
		
		/* C))************** Rendez-vous ************ */
		/*C))************** Rendez-vous *************/
		$this->add ( array (
				'name' => 'motif_rv',
				'type' => 'Textarea',
				'options' => array (
						//'label' => 'Motif du rendez-vous :'
				),
				'attributes' => array (
						'readonly' => 'readonly',
						'id' => 'motif_rv'
				)
		) );
		$this->add ( array (
				'name' => 'habitude_vie1',
				'type' => 'Text',
				'options' => array (
						'label' => 'Habitude de vie 1'
				),
				'attributes' => array (
						'id'  => 'habitude_vie1'
				)
		) );
		$this->add ( array (
				'name' => 'habitude_vie2',
				'type' => 'Text',
				'options' => array (
						'label' => 'Habitude de vie 2'
				),
				'attributes' => array (
						'id'  => 'habitude_vie2'
				)
		) );
		$this->add ( array (
				'name' => 'habitude_vie3',
				'type' => 'Text',
				'options' => array (
						'label' => 'Habitude de vie 3'
				),
				'attributes' => array (
						'id'  => 'habitude_vie3'
				)
		) );
		$this->add ( array (
				'name' => 'habitude_vie4',
				'type' => 'Text',
				'options' => array (
						'label' => 'Habitude de vie 4'
				),
				'attributes' => array (
						'id'  => 'habitude_vie4'
				)
		) );
		$this->add ( array (
				'name' => 'antecedent_familial1',
				'type' => 'Text',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8','Antécédent 1')
				),
				'attributes' => array (
						'id'  => 'antecedent_familial1'
				)
		) );
		$this->add ( array (
				'name' => 'antecedent_familial2',
				'type' => 'Text',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8','Antécédent 2')
				),
				'attributes' => array (
						'id'  => 'antecedent_familial2'
				)
		) );
		$this->add ( array (
				'name' => 'antecedent_familial3',
				'type' => 'Text',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8','Antécédent 3')
				),
				'attributes' => array (
						'id'  => 'antecedent_familial3'
				)
		) );
		$this->add ( array (
				'name' => 'antecedent_familial4',
				'type' => 'Text',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8','Antécédent 4')
				),
				'attributes' => array (
						'id'  => 'antecedent_familial4'
				)
		) );
		$this->add ( array (
				'name' => 'date_rv',
				'type' => 'Text',
				'options' => array (
						'label' => 'Date :'
				),
				'attributes' => array (
						'id' => 'date_rv',
				)
		) );
		$this->add ( array (
				'name' => 'heure_rv',
				'type' => 'Zend\Form\Element\Select',
				'options' => array (
						'label' => 'Heure :',
						'empty_option' => 'Choisir',
						'value_options' => array (
								'08:00' => '08:00',
								'09:00' => '09:00',
								'10:00' => '10:00',
								'15:00' => '15:00',
								'16:00' => '16:00'
						)
				),
				'attributes' => array (
						'id' => 'heure_rv'
				)
		) );
		
		/**
		 * LES HISTORIQUES OU TERRAINS PARTICULIERS
		 * LES HISTORIQUES OU TERRAINS PARTICULIERS
		 * LES HISTORIQUES OU TERRAINS PARTICULIERS
		 */
		/**** ANTECEDENTS PERSONNELS ****/
		/**** ANTECEDENTS PERSONNELS ****/
		
		/*LES HABITUDES DE VIE DU PATIENTS*/
		/*Alcoolique*/
		$this->add ( array (
				'name' => 'AlcooliqueHV',
				'type' => 'checkbox',
				'attributes' => array (
						'id' => 'AlcooliqueHV'
				)
		) );
		$this->add ( array (
				'name' => 'DateDebutAlcooliqueHV',
				'type' => 'text',
				'attributes' => array (
						'id' => 'DateDebutAlcooliqueHV'
				)
		) );
		$this->add ( array (
				'name' => 'DateFinAlcooliqueHV',
				'type' => 'text',
				'attributes' => array (
						'id' => 'DateFinAlcooliqueHV'
				)
		) );
		$this->add ( array (
				'name' => 'AutresHV',
				'type' => 'checkbox',
				'attributes' => array (
						'id' => 'AutresHV'
				)
		) );
		$this->add ( array (
				'name' => 'NoteAutresHV',
				'type' => 'text',
				'attributes' => array (
						'id' => 'NoteAutresHV'
				)
		) );
		/*Fumeur*/
		$this->add ( array (
				'name' => 'FumeurHV',
				'type' => 'checkbox',
				'attributes' => array (
						'id' => 'FumeurHV'
				)
		) );
		$this->add ( array (
				'name' => 'DateDebutFumeurHV',
				'type' => 'text',
				'attributes' => array (
						'id' => 'DateDebutFumeurHV'
				)
		) );
		$this->add ( array (
				'name' => 'DateFinFumeurHV',
				'type' => 'text',
				'attributes' => array (
						'id' => 'DateFinFumeurHV'
				)
		) );
		$this->add ( array (
				'name' => 'nbPaquetFumeurHV',
				'type' => 'text',
				'attributes' => array (
						'id' => 'nbPaquetFumeurHV'
				)
		) );
		$this->add ( array (
				'name' => 'nbPaquetAnneeFumeurHV',
				'type' => 'text',
				'attributes' => array (
						'id' => 'nbPaquetAnneeFumeurHV'
				)
		) );
		/*Drogué*/
		$this->add ( array (
				'name' => 'DroguerHV',
				'type' => 'checkbox',
				'attributes' => array (
						'id' => 'DroguerHV'
				)
		) );
		$this->add ( array (
				'name' => 'DateDebutDroguerHV',
				'type' => 'text',
				'attributes' => array (
						'id' => 'DateDebutDroguerHV'
				)
		) );
		$this->add ( array (
				'name' => 'DateFinDroguerHV',
				'type' => 'text',
				'attributes' => array (
						'id' => 'DateFinDroguerHV'
				)
		) );
		/*LES ANTECEDENTS MEDICAUX*/
		/*Diabete*/
		$this->add ( array (
				'name' => 'DiabeteAM',
				'type' => 'checkbox',
				'attributes' => array (
						'id' => 'DiabeteAM'
				)
		) );
		/*HTA*/
		$this->add ( array (
				'name' => 'htaAM',
				'type' => 'checkbox',
				'attributes' => array (
						'id' => 'htaAM'
				)
		) );
		/*Drepanocytose*/
		$this->add ( array (
				'name' => 'drepanocytoseAM',
				'type' => 'checkbox',
				'attributes' => array (
						'id' => 'drepanocytoseAM'
				)
		) );
		/*Dislipidemie*/
		$this->add ( array (
				'name' => 'dislipidemieAM',
				'type' => 'checkbox',
				'attributes' => array (
						'id' => 'dislipidemieAM'
				)
		) );
		/*Asthme*/
		$this->add ( array (
				'name' => 'asthmeAM',
				'type' => 'checkbox',
				'attributes' => array (
						'id' => 'asthmeAM'
				)
		) );
		/*Autre*/
		$this->add ( array (
				'name' => 'autresAM',
				'type' => 'text',
				'attributes' => array (
						'id' => 'autresAM',
						'maxlength' => 13,
				)
		) );
		/*nbCheckbox*/
		$this->add ( array (
				'name' => 'nbCheckboxAM',
				'type' => 'hidden',
				'attributes' => array (
						'id' => 'nbCheckboxAM',
				)
		) );
		/*GYNECO-OBSTETRIQUE*/
		/*Menarche*/
		$this->add ( array (
				'name' => 'MenarcheGO',
				'type' => 'checkbox',
				'attributes' => array (
						'id' => 'MenarcheGO'
				)
		) );
		/*Note Menarche*/
		$this->add ( array (
				'name' => 'NoteMenarcheGO',
				'type' => 'text',
				'attributes' => array (
						'id' => 'NoteMenarcheGO'
				)
		) );
		
		/*Gestite*/
		$this->add ( array (
				'name' => 'GestiteGO',
				'type' => 'checkbox',
				'attributes' => array (
						'id' => 'GestiteGO'
				)
		) );
		/*Note Gestite*/
		$this->add ( array (
				'name' => 'NoteGestiteGO',
				'type' => 'text',
				'attributes' => array (
						'id' => 'NoteGestiteGO'
				)
		) );
		

		/*Parite*/
		$this->add ( array (
				'name' => 'PariteGO',
				'type' => 'checkbox',
				'attributes' => array (
						'id' => 'PariteGO'
				)
		) );
		/*Note Parite*/
		$this->add ( array (
				'name' => 'NotePariteGO',
				'type' => 'text',
				'attributes' => array (
						'id' => 'NotePariteGO'
				)
		) );
		
		/*Cycle*/
		$this->add ( array (
				'name' => 'CycleGO',
				'type' => 'checkbox',
				'attributes' => array (
						'id' => 'CycleGO'
				)
		) );
		/*Duree Cycle*/
		$this->add ( array (
				'name' => 'DureeCycleGO',
				'type' => 'text',
				'attributes' => array (
						'id' => 'DureeCycleGO'
				)
		) );
		/*Regularite cycle*/
		$this->add ( array (
				'name' => 'RegulariteCycleGO',
				'type' => 'Zend\Form\Element\Select',
				'options' => array (
						'value_options' => array (
								' ' => '',
								'1' => 'Oui',
								'0' => 'Non',
						)
				),
				'attributes' => array (
						'id' => 'RegulariteCycleGO'
				)
		) );
		/*Dysmenorrhee cycle*/
		$this->add ( array (
				'name' => 'DysmenorrheeCycleGO',
				'type' => 'Zend\Form\Element\Select',
				'options' => array (
						'value_options' => array (
								' ' => '',
								'1' => 'Oui',
								'0' => 'Non',
						)
				),
				'attributes' => array (
						'id' => 'DysmenorrheeCycleGO'
				)
		) );
		
		/*Autres*/
		$this->add ( array (
				'name' => 'AutresGO',
				'type' => 'checkbox',
				'attributes' => array (
						'id' => 'AutresGO'
				)
		) );
		/*Note Autres*/
		$this->add ( array (
				'name' => 'NoteAutresGO',
				'type' => 'text',
				'attributes' => array (
						'id' => 'NoteAutresGO'
				)
		) );
		
		/**** TRAITEMENTS CHIRURGICAUX ****/
		/**** TRAITEMENTS CHIRURCICAUX ****/
		$this->add ( array (
				'name' => 'endoscopieInterventionnelle',
				'type' => 'Text',
				'options' => array (
						'label' => 'Endoscopie Interventionnelle :'
				),
				'attributes' => array (
						'id' => 'endoscopieInterventionnelle',
				)
		) );
		
		$this->add ( array (
				'name' => 'radiologieInterventionnelle',
				'type' => 'Text',
				'options' => array (
						'label' => 'Radiologie Interventionnelle :'
				),
				'attributes' => array (
						'id' => 'radiologieInterventionnelle',
				)
		) );
		
		$this->add ( array (
				'name' => 'cardiologieInterventionnelle',
				'type' => 'Text',
				'options' => array (
						'label' => 'Cardiologie Interventionnelle :'
				),
				'attributes' => array (
						'id' => 'cardiologieInterventionnelle',
				)
		) );
		
		$this->add ( array (
				'name' => 'autresIntervention',
				'type' => 'Text',
				'options' => array (
						'label' => 'Autres interventions:'
				),
				'attributes' => array (
						'id' => 'autresIntervention',
				)
		) );
		
		
		
		
		
		
	}
}