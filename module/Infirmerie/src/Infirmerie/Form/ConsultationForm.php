<?php

namespace Infirmerie\Form;

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
				'name' => 'idfacturation',
				'type' => 'hidden',
				'attributes' => array (
						'id' => 'idfacturation'
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
						'style' => 'text-align: right',
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
						'max' => 100,
						'min' => 1,
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
						'label' => iconv ( 'ISO-8859-1', 'UTF-8', 'Périmètre cranien (cm)'),
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
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		/**
		 * ********DONNEES DE L EXAMEN PHYSIQUE***********
		 */
		/**
		 * ********DONNEES DE L EXAMEN PHYSIQUE***********
		 */
		$this->add ( array (
				'name' => 'examen_donnee1',
				'type' => 'Text',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8','donnée 1') 
				),
				'attributes' => array (
						'readonly' => 'readonly',
				'id'  => 'examen_donnee1'
				)
		) );
		$this->add ( array (
				'name' => 'examen_donnee2',
				'type' => 'Text',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8','donnée 2') 
				),
				'attributes' => array (
						'readonly' => 'readonly',
						'id'  => 'examen_donnee2'
				)
		) );
		$this->add ( array (
				'name' => 'examen_donnee3',
				'type' => 'Text',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8','donnée 3') 
				),
				'attributes' => array (
						'readonly' => 'readonly',
						'id'  => 'examen_donnee3'
				)
		) );
		$this->add ( array (
				'name' => 'examen_donnee4',
				'type' => 'Text',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8','donnée 4') 
				),
				'attributes' => array (
						'readonly' => 'readonly',
						'id'  => 'examen_donnee4'
				)
		) );
		$this->add ( array (
				'name' => 'examen_donnee5',
				'type' => 'Text',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8','donnée 5') 
				),
				'attributes' => array (
						'readonly' => 'readonly',
						'id'  => 'examen_donnee5'
				)
		) );
		
		/**
		 * ********** EXAMENS COMPLEMENTAIRES (EXAMENS ET ANALYSE) *************
		 */
		/**
		 * ********** EXAMENS COMPLEMENTAIRES (EXAMENS ET ANALYSE) *************
		 */
		
		/* C)))*********ACTES******** */
		$this->add ( array (
				'name' => 'doppler_couleur_pulse',
				'type' => 'Text',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8', 'Doppler couleur, pulsé, continu, tissulaire:')
				),
				'attributes' => array (
						'readonly' => 'readonly',
						'id'  => 'doppler_couleur_pulse'
				)
		) );
		
		$this->add ( array (
				'name' => 'echographie_de_stress',
				'type' => 'Text',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8', 'Echographie de stress:')
				),
				'attributes' => array (
						'readonly' => 'readonly',
						'id'  => 'echographie_de_stress'
				)
		) );
		
		$this->add ( array (
				'name' => 'holter_ecg',
				'type' => 'Text',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8', 'Holter ECG:')
				),
				'attributes' => array (
						'readonly' => 'readonly',
						'id'  => 'holter_ecg'
				)
		) );
		
		$this->add ( array (
				'name' => 'holter_tensionnel',
				'type' => 'Text',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8', 'Holter tensionnel:')
				),
				'attributes' => array (
						'readonly' => 'readonly',
						'id'  => 'holter_tensionnel'
				)
		) );
		$this->add ( array (
				'name' => 'fibroscopie_bronchique',
				'type' => 'Text',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8', 'Fibroscopie bronchique:')
				),
				'attributes' => array (
						'readonly' => 'readonly',
						'id'  => 'fibroscopie_bronchique'
				)
		) );
		
		$this->add ( array (
				'name' => 'fibroscopie_gastrique',
				'type' => 'Text',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8', 'Fibroscopie gastrique:')
				),
				'attributes' => array (
						'readonly' => 'readonly',
						'id'  => 'fibroscopie_gastrique'
				)
		) );
		
		$this->add ( array (
				'name' => 'colposcopie',
				'type' => 'Text',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8', 'Colposcopie:')
				),
				'attributes' => array (
						'readonly' => 'readonly',
						'id'  => 'colposcopie'
				)
		) );
		
		$this->add ( array (
				'name' => 'echographie_gynecologique',
				'type' => 'Text',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8', 'Echographie gynécologique:')
				),
				'attributes' => array (
						'readonly' => 'readonly',
						'id'  => 'echographie_gynecologique'
				)
		) );
		
		$this->add ( array (
				'name' => 'echographie_obstetrique',
				'type' => 'Text',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8', 'Echographie obstétrique:')
				),
				'attributes' => array (
						'readonly' => 'readonly',
						'id'  => 'echographie_obstetrique'
				)
		) );
		
		$this->add ( array (
				'name' => 'cpn',
				'type' => 'Text',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8', 'CPN:')
				),
				'attributes' => array (
						'readonly' => 'readonly',
						'id'  => 'cpn'
				)
		) );
		
		$this->add ( array (
				'name' => 'consultation_senologie',
				'type' => 'Text',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8', 'Consultation sénologie:')
				),
				'attributes' => array (
						'readonly' => 'readonly',
						'id'  => 'consultation_senologie'
				)
		) );
		
		$this->add ( array (
				'name' => 'plannification_familiale',
				'type' => 'Text',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8', 'Plannification familiale:')
				),
				'attributes' => array (
						'readonly' => 'readonly',
						'id'  => 'plannification_familiale'
				)
		) );
		
		$this->add ( array (
				'name' => 'ecg',
				'type' => 'Text',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8', 'ECG:')
				),
				'attributes' => array (
						'readonly' => 'readonly',
						'id'  => 'ecg'
				)
		) );
		
		$this->add ( array (
				'name' => 'eeg',
				'type' => 'Text',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8', 'EEG:')
				),
				'attributes' => array (
						'readonly' => 'readonly',
						'id'  => 'eeg'
				)
		) );
		
		$this->add ( array (
				'name' => 'efr',
				'type' => 'Text',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8', 'EFR:')
				),
				'attributes' => array (
						'readonly' => 'readonly',
						'id'  => 'efr'
				)
		) );
		
		$this->add ( array (
				'name' => 'emg',
				'type' => 'Text',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8', 'EMG:')
				),
				'attributes' => array (
						'readonly' => 'readonly',
						'id'  => 'emg'
				)
		) );
		
		$this->add ( array (
				'name' => 'circoncision',
				'type' => 'Text',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8', 'Circoncision:')
				),
				'attributes' => array (
						'readonly' => 'readonly',
						'id'  => 'circoncision'
				)
		) );
		
		$this->add ( array (
				'name' => 'vaccination',
				'type' => 'Text',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8', 'Vaccination:')
				),
				'attributes' => array (
						'readonly' => 'readonly',
						'id'  => 'vaccination'
				)
		) );
		
		$this->add ( array (
				'name' => 'soins_infirmiers',
				'type' => 'Text',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8', 'Soins infirmiers:')
				),
				'attributes' => array (
						'readonly' => 'readonly',
						'id'  => 'soins_infirmiers'
				)
		) );

		/* A)))*********ANALYSE BIOLOGIQUE******** */
		$this->add ( array (
				'name' => 'groupe_sanguin',
				'type' => 'Text',
				'options' => array (
						'label' => 'Groupe Sanguin: '
				),
				'attributes' => array (
						'readonly' => 'readonly',
						'id'  => 'groupe_sanguin'
				)
		) );
		$this->add ( array (
				'name' => 'hemogramme_sanguin',
				'type' => 'Text',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8', 'Hemogramme sanguin' )
				),
				'attributes' => array (
						'readonly' => 'readonly',
						'id'  => 'hemogramme_sanguin'
				)
		) );
		$this->add ( array (
				'name' => 'bilan_hemolyse',
				'type' => 'Text',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8', 'Bilan de l\'hémostase:' )
				),
				'attributes' => array (
						'readonly' => 'readonly',
						'id'  => 'bilan_hemolyse'
				)
		) );
		$this->add ( array (
				'name' => 'bilan_hepatique',
				'type' => 'Text',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8', 'Bilan hépatique:' )
				),
				'attributes' => array (
						'readonly' => 'readonly',
						'id'  => 'bilan_hepatique'
				)
		) );
		$this->add ( array (
				'name' => 'bilan_renal',
				'type' => 'Text',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8', 'Bilan rénal:' )
				),
				'attributes' => array (
						'readonly' => 'readonly',
						'id'  => 'bilan_renal'
				)
		) );
		$this->add ( array (
				'name' => 'bilan_inflammatoire',
				'type' => 'Text',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8', 'Bilan inflammatoire:' )
				),
				'attributes' => array (
						'readonly' => 'readonly',
						'id'  => 'bilan_inflammatoire'
				)
		) );
		/* B)))*********EXAMEN MORPHOLOGIQUE******** */
		/**
		 * * Les balises images dans cette partie ne sont pas utilisï¿½es**
		 */
		$this->add ( array (
				'name' => 'radio',
				'type' => 'Textarea',
				'options' => array (
						'label' => 'Radio:'
				),
				'attributes' => array (
						'readonly' => 'readonly',
						'id' => 'radio'
				)
		) );
		/**
		 * *** image de la radio ****
		 */
		$this->add ( array (
				'name' => 'radio_image',
				'type' => 'Image'
		) );
		/* --->>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> */
		$this->add ( array (
				'name' => 'ecographie',
				'type' => 'Textarea',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8', 'Echographie: ' )
				),
				'attributes' => array (
						'readonly' => 'readonly',
						'id' => 'ecographie'
				)
		) );
		/**
		 * *** image de l'ecographie ****
		 */
		$this->add ( array (
				'name' => 'ecographie_image',
				'type' => 'Image'
		) );
		/* --->>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> */
		$this->add ( array (
				'name' => 'fibrocospie',
				'type' => 'Textarea',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8', 'Fibroscopie: ' )
				),
				'attributes' => array (
						'readonly' => 'readonly',
						'id' => 'fibrocospie'
				)
		) );
		/**
		 * *** image de la fibroscopie ****
		 */
		$this->add ( array (
				'name' => 'fibroscopie_image',
				'type' => 'Image'
		) );
		/* --->>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> */
		$this->add ( array (
				'name' => 'scanner',
				'type' => 'Textarea',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8', 'Scanner: ' )
				),
				'attributes' => array (
						'readonly' => 'readonly',
						'id' => 'scanner'
				)
		) );
		/**
		 * *** image du scanner ****
		 */
		$this->add ( array (
				'name' => 'scanner_image',
				'type' => 'Image'
		) );
		/* --->>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> */
		$this->add ( array (
				'name' => 'irm',
				'type' => 'Textarea',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8', 'IRM: ' )
				),
				'attributes' => array (
						'readonly' => 'readonly',
						'id' => 'irm'
				)
		) );
		/**
		 * *** image de l'irm ****
		 */
		$this->add ( array (
				'name' => '$irm_image',
				'type' => 'Image'
		) );
		/* --->>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> */

		/**
		 * ********************************* DIAGNOSTICS *******************************
		 */
		/**
		 * ********************************* DIAGNOSTICS *******************************
		 */
		$this->add ( array (
				'name' => 'diagnostic1',
				'type' => 'Text',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8', 'Diagnostic 1: ' )
				),
				'attributes' => array (
						'readonly' => 'readonly',
						'id' => 'diagnostic1'
				)
		) );
		$this->add ( array (
				'name' => 'diagnostic2',
				'type' => 'Text',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8', 'Diagnostic 2: ' )
				),
				'attributes' => array (
						'readonly' => 'readonly',
						'id' => 'diagnostic2'
				)
		) );
		$this->add ( array (
				'name' => 'diagnostic3',
				'type' => 'Text',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8', 'Diagnostic 3: ' )
				),
				'attributes' => array (
						'readonly' => 'readonly',
						'id' => 'diagnostic3'
				)
		) );
		$this->add ( array (
				'name' => 'diagnostic4',
				'type' => 'Text',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8', 'Diagnostic 4: ' )
				),
				'attributes' => array (
						'readonly' => 'readonly',
						'id' => 'diagnostic4'
				)
		) );
		
		
		/*** LES TYPES DE BANDELETTES URINAIRES ***/
		/*** LES TYPES DE BANDELETTES URINAIRES ***/
		/*** LES TYPES DE BANDELETTES URINAIRES ***/
		$this->add ( array (
				'name' => 'albumine',
				'type' => 'radio',
				'options' => array (
						'value_options' => array (
								'0' => 'â€“',
								'1' => '+',
						)
				),
				'attributes' => array (
						'id' => 'albumine',
						
				)
		) );
		$this->add ( array (
				'name' => 'croixalbumine',
				'type' => 'radio',
				'options' => array (
						'value_options' => array (
								'1' => '1',
								'2' => '2',
								'3' => '3',
								'4' => '4',
						)
				),
				'attributes' => array (
						'id' => 'croixalbumine',
		
				)
		) );
		
		
		$this->add ( array (
				'name' => 'sucre',
				'type' => 'radio',
				'options' => array (
						'value_options' => array (
								'0' => 'â€“',
								'1' => '+',
						)
				),
				'attributes' => array (
						'id' => 'sucre',
		
				)
		) );
		$this->add ( array (
				'name' => 'croixsucre',
				'type' => 'radio',
				'options' => array (
						'value_options' => array (
								'1' => '1',
								'2' => '2',
								'3' => '3',
								'4' => '4',
						)
				),
				'attributes' => array (
						'id' => 'croixsucre',
		
				)
		) );
		
		
		
		$this->add ( array (
				'name' => 'corpscetonique',
				'type' => 'radio',
				'options' => array (
						'value_options' => array (
								'0' => 'â€“',
								'1' => '+',
						)
				),
				'attributes' => array (
						'id' => 'corpscetonique',
		
				)
		) );
		$this->add ( array (
				'name' => 'croixcorpscetonique',
				'type' => 'radio',
				'options' => array (
						'value_options' => array (
								'1' => '1',
								'2' => '2',
								'3' => '3',
								'4' => '4',
						)
				),
				'attributes' => array (
						'id' => 'croixcorpscetonique',
						'class' => 'croixcorpscetonique',
		
				)
		) );
		/*** FIN LES TYPES DE BANDELETTES URINAIRES ***/
		/*** FIN LES TYPES DE BANDELETTES URINAIRES ***/
		
		
		$this->add ( array (
				'name' => 'observation',
				'type' => 'Textarea',
				'options' => array (
						'label' => 'Observations'
				),
				'attributes' => array (
						'rows' => 1,
						'cols' => 180
				)
		) );
		$this->add ( array (
				'name' => 'submit',
				'type' => 'Submit',
				'options' => array (
						'label' => 'Valider'
				)
		) );
		// ************** TRAITEMENTS *************
		// ************** TRAITEMENTS *************
		// ************** TRAITEMENTS *************
		/**
		 * ************* traitement chirurgicaux ************
		 */
		/**
		 * ************* traitement chirurgicaux ************
		 */
		$this->add ( array (
				'name' => 'diagnostic_traitement_chirurgical',
				'type' => 'Textarea',
				'options' => array (
						'label' => 'Diagnostic :'
				),
				'attributes' => array (
						'readonly' => 'readonly',
						'id' => 'diagnostic_traitement_chirurgical'
				)
		) );
		$this->add ( array (
				'name' => 'type_anesthesie_demande',
				'type' => 'Select',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8', 'Type d\'anesthésie :' ),
						'value_options' => array (
								'1' => iconv ( 'ISO-8859-1', 'UTF-8', 'Anesthésie1' ),
								'2' => iconv ( 'ISO-8859-1', 'UTF-8', 'Anesthésie2' ),
								'3' => iconv ( 'ISO-8859-1', 'UTF-8', 'Anesthésie3' )
						)
				),
				'attributes' => array (
						'readonly' => 'readonly',
						'id' => 'type_anesthesie_demande'
				)
		) );
		$this->add ( array (
				'name' => 'intervention_prevue',
				'type' => 'Text',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8', 'Intervention Prévue :')
				),
				'attributes' => array (
						'readonly' => 'readonly',
						'id' => 'intervention_prevue'
				)
		) );
		$this->add ( array (
				'name' => 'numero_vpa',
				'type' => 'Text',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8', 'VPA Numéro:' )
				),
				'attributes' => array (
						'readonly' => 'readonly',
						'id' => 'numero_vpa'
				)
		) );
		$this->add ( array (
				'name' => 'observation',
				'type' => 'Textarea',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8', 'Observation :' )
				),
				'attributes' => array (
						'readonly' => 'readonly',
						'id' => 'observation'
				)
		) );
		$this->add ( array (
				'name' => 'note_compte_rendu_operatoire',
				'type' => 'Textarea',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8', 'Protocole opératoire' )
				),
				'attributes' => array (
						'id' => 'note_compte_rendu_operatoire'
				)
		) );
		
		$this->add ( array (
				'name' => 'note_compte_rendu_operatoire_instrumental',
				'type' => 'Textarea',
				'options' => array (
						'label' => iconv ( 'ISO-8859-1', 'UTF-8', 'Note :' )
				),
				'attributes' => array (
						'id' => 'note_compte_rendu_operatoire_instrumental'
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
				'type' => 'Select',
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
				'type' => 'Select',
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
						'label' => 'Motif du rendez-vous :'
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
				'type' => 'Select',
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
				'type' => 'Select',
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
				'type' => 'Select',
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
		/**** ANTECEDENTS FAMILIAUX ****/
		/**** ANTECEDENTS FAMILIAUX ****/
		
		/*Diabete*/
		$this->add ( array (
				'name' => 'DiabeteAF',
				'type' => 'checkbox',
				'attributes' => array (
						'id' => 'DiabeteAF'
				)
		) );
		/*Note Diabete*/
		$this->add ( array (
				'name' => 'NoteDiabeteAF',
				'type' => 'text',
				'attributes' => array (
						'id' => 'NoteDiabeteAF'
				)
		) );
		
		/*Drepanocytose*/
		$this->add ( array (
				'name' => 'DrepanocytoseAF',
				'type' => 'checkbox',
				'attributes' => array (
						'id' => 'DrepanocytoseAF'
				)
		) );
		/*Note Drepanocytose*/
		$this->add ( array (
				'name' => 'NoteDrepanocytoseAF',
				'type' => 'text',
				'attributes' => array (
						'id' => 'NoteDrepanocytoseAF'
				)
		) );
		
		/*HTA*/
		$this->add ( array (
				'name' => 'htaAF',
				'type' => 'checkbox',
				'attributes' => array (
						'id' => 'htaAF'
				)
		) );
		/*Note HTA*/
		$this->add ( array (
				'name' => 'NoteHtaAF',
				'type' => 'text',
				'attributes' => array (
						'id' => 'NoteHtaAF'
				)
		) );
		
		/*Autres*/
		$this->add ( array (
				'name' => 'autresAF',
				'type' => 'checkbox',
				'attributes' => array (
						'id' => 'autresAF'
				)
		) );
		/*Note Autres*/
		$this->add ( array (
				'name' => 'NoteAutresAF',
				'type' => 'text',
				'attributes' => array (
						'id' => 'NoteAutresAF'
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