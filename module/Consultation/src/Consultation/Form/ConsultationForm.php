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
		
		
		
		//ANTECEDENTS PERSONNELS --- ANTECEDENTS PERSONNELS --- ANTECEDENTS PERSONNELS
		//ANTECEDENTS PERSONNELS --- ANTECEDENTS PERSONNELS --- ANTECEDENTS PERSONNELS
		//ANTENATAUX (GROSSESSE)
		//ANTENATAUX (GROSSESSE)
		//ANTENATAUX (GROSSESSE)
		$this->add ( array (
				'name' => 'nbFoetusAP',
				'type' => 'Number',
				'attributes' => array (
						'id' => 'nbFoetusAP',
						'style' => 'width: 60px;',
						'min' => 1,
						'max' => 9,
				)
		) );
		
		$this->add ( array (
				'name' => 'deroulementAP',
				'type' => 'Select',
				'options' => array (
						'value_options' => array (
								'' => iconv ( 'ISO-8859-1', 'UTF-8', '' ),
								 1 => iconv ( 'ISO-8859-1', 'UTF-8', 'Normal' ),
								 2 => iconv ( 'ISO-8859-1', 'UTF-8', 'Pathologique' ),
						)
				),
				'attributes' => array (
						'onchange' => 'precisonDeroulementAPVue(this.value)',
						'id' => 'deroulementAP'
				)
		) );
		
		$this->add ( array (
				'name' => 'precisonDeroulementAP',
				'type' => 'Select',
				'options' => array (
						'value_options' => array (
								'' => iconv ( 'ISO-8859-1', 'UTF-8', '' ),
								1 => iconv ( 'ISO-8859-1', 'UTF-8', 'Diabète' ),
								2 => iconv ( 'ISO-8859-1', 'UTF-8', 'HTA' ),
								3 => iconv ( 'ISO-8859-1', 'UTF-8', 'Infection génitale' ),
								4 => iconv ( 'ISO-8859-1', 'UTF-8', 'Paludisme' ),
							   -1 => iconv ( 'ISO-8859-1', 'UTF-8', 'Autres ..' ),
						)
				),
				'attributes' => array (
						'id' => 'precisonDeroulementAP',
						'style' => 'width: 40%',
				)
		) );
		
		
		//PERINATAUX (ACCOUCHEMENT)
		//PERINATAUX (ACCOUCHEMENT)
		//PERINATAUX (ACCOUCHEMENT)
		$this->add ( array (
				'name' => 'voieBasseAP',
				'type' => 'Select',
				'options' => array (
						'value_options' => array (
								'' => iconv ( 'ISO-8859-1', 'UTF-8', '' ),
								1 => iconv ( 'ISO-8859-1', 'UTF-8', 'Normal' ),
								2 => iconv ( 'ISO-8859-1', 'UTF-8', 'Dystocique' ),
						)
				),
				'attributes' => array (
						'id' => 'voieBasseAP',
						'style' => 'width: 130px; float: right;'
				)
		) );
		
		$this->add ( array (
				'name' => 'cesarienneAP',
				'type' => 'Select',
				'options' => array (
						'value_options' => array (
							   '' => iconv ( 'ISO-8859-1', 'UTF-8', '' ),
								1 => iconv ( 'ISO-8859-1', 'UTF-8', 'Oui' ),
							   -1 => iconv ( 'ISO-8859-1', 'UTF-8', 'Non' ),
						)
				),
				'attributes' => array (
						'onchange' => 'motifCesarienneAPVue(this.value)',
						'id' => 'cesarienneAP'
				)
		) );
		
		$this->add ( array (
				'name' => 'motifCesarienneAP',
				'type' => 'Text',
				'attributes' => array (
						'id' => 'motifCesarienneAP',
						'style' => 'width: 75%;'
				)
		) );
		
		$this->add ( array (
				'name' => 'manoeuvreObstetricaleAP',
				'type' => 'Select',
				'options' => array (
						'value_options' => array (
							   '' => iconv ( 'ISO-8859-1', 'UTF-8', '' ),
								1 => iconv ( 'ISO-8859-1', 'UTF-8', 'Oui' ),
							   -1 => iconv ( 'ISO-8859-1', 'UTF-8', 'Non' ),
						)
				),
				'attributes' => array (
						'onchange' => 'motifManoeuvreObstetricaleAPVue(this.value)',
						'id' => 'manoeuvreObstetricaleAP',
						'style' => 'float: right;'
				)
		) );
		
		$this->add ( array (
				'name' => 'motifManoeuvreObstetricaleAP',
				'type' => 'Text',
				'attributes' => array (
						'id' => 'motifManoeuvreObstetricaleAP',
						'style' => 'width: 85%;'
				)
		) );
		
		$this->add ( array (
				'name' => 'ageGestationnelATermeAP',
				'type' => 'Select',
				'options' => array (
						'value_options' => array (
							   '' => iconv ( 'ISO-8859-1', 'UTF-8', '' ),
								1 => iconv ( 'ISO-8859-1', 'UTF-8', 'Oui' ),
							   -1 => iconv ( 'ISO-8859-1', 'UTF-8', 'Non' ),
						)
				),
				'attributes' => array (
						'onchange' => 'precisonAgeGestationnelATermeAPVue(this.value)',
						'id' => 'ageGestationnelATermeAP',
						'style' => 'float: right;',
				)
		) );
		
		$this->add ( array (
				'name' => 'precisonAgeGestationnelATermeAP',
				'type' => 'Select',
				'options' => array (
						'value_options' => array (
								'' => iconv ( 'ISO-8859-1', 'UTF-8', '' ),
								1 => iconv ( 'ISO-8859-1', 'UTF-8', 'Prématuré' ),
								2 => iconv ( 'ISO-8859-1', 'UTF-8', 'Postmaturé' ),
						)
				),
				'attributes' => array (
						'id' => 'precisonAgeGestationnelATermeAP'
				)
		) );
		
		$this->add ( array (
				'name' => 'precisonValeurAgeGestationnelAP',
				'type' => 'Number',
				'attributes' => array (
						'id' => 'precisonValeurAgeGestationnelAP',
						'min' => 25,
						'max' => 45, 
						'style' => 'width: 70px; text-align: right;',
				)
		) );
		
		$this->add ( array (
				'name' => 'souffranceFoetaleAigueAP',
				'type' => 'Select',
				'options' => array (
						'value_options' => array (
							   '' => iconv ( 'ISO-8859-1', 'UTF-8', '' ),
								1 => iconv ( 'ISO-8859-1', 'UTF-8', 'Oui' ),
							   -1 => iconv ( 'ISO-8859-1', 'UTF-8', 'Non' ),
						)
				),
				'attributes' => array (
						'onchange' => 'precisonReanimationSouffranceFoetaleAPVue(this.value)',
						'id' => 'souffranceFoetaleAigueAP',
						'style' => 'float: right;'
				)
		) );
		
		$this->add ( array (
				'name' => 'precisonReanimationSouffranceFoetaleAP',
				'type' => 'Select',
				'options' => array (
						'value_options' => array (
							   '' => iconv ( 'ISO-8859-1', 'UTF-8', '' ),
								1 => iconv ( 'ISO-8859-1', 'UTF-8', 'Oui' ),
							   -1 => iconv ( 'ISO-8859-1', 'UTF-8', 'Non' ),
						)
				),
				'attributes' => array (
						'id' => 'precisonReanimationSouffranceFoetaleAP'
				)
		) );
		
		$this->add ( array (
				'name' => 'pathologieNeonataleAP',
				'type' => 'Select',
				'options' => array (
						'value_options' => array (
								'' => iconv ( 'ISO-8859-1', 'UTF-8', '' ),
								1 => iconv ( 'ISO-8859-1', 'UTF-8', 'Infection' ),
								2 => iconv ( 'ISO-8859-1', 'UTF-8', 'Hémorragie' ),
								3 => iconv ( 'ISO-8859-1', 'UTF-8', 'Ictère' ),
							   -1 => iconv ( 'ISO-8859-1', 'UTF-8', 'Autres...' ),
						)
				),
				'attributes' => array (
						'id' => 'pathologieNeonataleAP'
				)
		) );
		
		
		//ALIMENTATION
		//ALIMENTATION
		//ALIMENTATION
		
		$this->add ( array (
				'name' => 'allaitementMatenelExclusifAP',
				'type' => 'Select',
				'options' => array (
						'value_options' => array (
							   '' => iconv ( 'ISO-8859-1', 'UTF-8', '' ),
								1 => iconv ( 'ISO-8859-1', 'UTF-8', 'Oui' ),
							   -1 => iconv ( 'ISO-8859-1', 'UTF-8', 'Non' ),
						)
				),
				'attributes' => array (
						'onchange' => 'typeAllaitementAPVue(this.value)',
						'id' => 'allaitementMatenelExclusifAP',
						'style' => 'float: right;'
				)
		) );
		
		$this->add ( array (
				'name' => 'typeAllaitementAP',
				'type' => 'Select',
				'options' => array (
						'value_options' => array (
								'' => iconv ( 'ISO-8859-1', 'UTF-8', '' ),
								1 => iconv ( 'ISO-8859-1', 'UTF-8', 'Allaitement artificiel seul' ),
								2 => iconv ( 'ISO-8859-1', 'UTF-8', 'Allaitement mixte' ),
						)
				),
				'attributes' => array (
						'onchange' => 'nomAllaitementArtificielAPVue(this.value)',
						'id' => 'typeAllaitementAP',
						'style' => 'float: right;'
				)
		) );
		
		$this->add ( array (
				'name' => 'nomAllaitementArtificielAP',
				'type' => 'Text',
				'attributes' => array (
						'id' => 'nomAllaitementArtificielAP',
						'style' => 'width: 75%;'
				)
		) );
		
		
		$this->add ( array (
				'name' => 'diversificationAlimentaireAP',
				'type' => 'Select',
				'options' => array (
						'value_options' => array (
							   '' => iconv ( 'ISO-8859-1', 'UTF-8', '' ),
								1 => iconv ( 'ISO-8859-1', 'UTF-8', 'Oui' ),
							   -1 => iconv ( 'ISO-8859-1', 'UTF-8', 'Non' ),
						)
				),
				'attributes' => array (
						'onchange' => 'ageDebutDiversificationAlimentaireAPVue(this.value)',
						'id' => 'diversificationAlimentaireAP',
						'style' => 'float: right;'
				)
		) );
		
		$this->add ( array (
				'name' => 'ageDebutDiversificationAlimentaireAP',
				'type' => 'Number',
				'attributes' => array (
						'id' => 'ageDebutDiversificationAlimentaireAP',
						'style' => 'width: 60px; text-align: right;',
						'min' => 1,
						'max' => 30
				)
		) );
		
		//multiselect
		$this->add ( array (
				'name' => 'typesAlimentsAP',
				'type' => 'Select',
				'options' => array (
						'value_options' => array (
								'' => iconv ( 'ISO-8859-1', 'UTF-8', '' ),
								1 => iconv ( 'ISO-8859-1', 'UTF-8', 'Céréale' ),
								2 => iconv ( 'ISO-8859-1', 'UTF-8', 'Légumes' ),
								3 => iconv ( 'ISO-8859-1', 'UTF-8', 'Oeufs' ),
								4 => iconv ( 'ISO-8859-1', 'UTF-8', 'Poissons' ),
								5 => iconv ( 'ISO-8859-1', 'UTF-8', 'Viande' ),
								6 => iconv ( 'ISO-8859-1', 'UTF-8', 'Fruits' ),
								7 => iconv ( 'ISO-8859-1', 'UTF-8', 'Plat familial' ),
							   -1 => iconv ( 'ISO-8859-1', 'UTF-8', 'Autres ...' ),
						)
				),
				'attributes' => array (
						'id' => 'diversificationAlimentaireAP',
						//'multiple' => 'multiple',
						'style' => 'width: 70%'
				)
		) );
		
		
		$this->add ( array (
				'name' => 'sevrageAP',
				'type' => 'Select',
				'options' => array (
						'value_options' => array (
							   '' => iconv ( 'ISO-8859-1', 'UTF-8', '' ),
								1 => iconv ( 'ISO-8859-1', 'UTF-8', 'Oui' ),
							   -1 => iconv ( 'ISO-8859-1', 'UTF-8', 'Non' ),
						)
				),
				'attributes' => array (
						'onchange' => 'ageDebutSevrageAPVue(this.value)',
						'id' => 'sevrageAP',
						'style' => 'float: right;'
				)
		) );
		
		
		$this->add ( array (
				'name' => 'ageDebutSevrageAP',
				'type' => 'Number',
				'attributes' => array (
						'id' => 'ageDebutSevrageAP',
						'style' => 'width: 60px; text-align: right;',
						'min' => 1,
						'max' => 30
				)
		) );
		
		
		//SCOLARITE
		//SCOLARITE
		//SCOLARITE
		$this->add ( array (
				'name' => 'scolariseAP',
				'type' => 'Select',
				'options' => array (
						'value_options' => array (
							   '' => iconv ( 'ISO-8859-1', 'UTF-8', '' ),
								1 => iconv ( 'ISO-8859-1', 'UTF-8', 'Oui' ),
							   -1 => iconv ( 'ISO-8859-1', 'UTF-8', 'Non' ),
						)
				),
				'attributes' => array (
						'onchange' => 'niveauScolariteAPVue(this.value)',
						'id' => 'scolariseAP',
						'style' => 'float: right;'
				)
		) );
		
		
		$this->add ( array (
				'name' => 'niveauScolariteAP',
				'type' => 'Select',
				'options' => array (
						'value_options' => array (
								'' => iconv ( 'ISO-8859-1', 'UTF-8', '' ),
								1 => iconv ( 'ISO-8859-1', 'UTF-8', 'Prescolaire' ),
								2 => iconv ( 'ISO-8859-1', 'UTF-8', 'Primaire' ),
								3 => iconv ( 'ISO-8859-1', 'UTF-8', 'Moyen-Secondaire' ),
								5 => iconv ( 'ISO-8859-1', 'UTF-8', 'Supérieur' ),
						)
				),
				'attributes' => array (
						'id' => 'niveauScolariteAP',
						'style' => 'width: 70%',
				)
		) );
		
		$this->add ( array (
				'name' => 'redoublementAP',
				'type' => 'Select',
				'options' => array (
						'value_options' => array (
							   '' => iconv ( 'ISO-8859-1', 'UTF-8', '' ),
								1 => iconv ( 'ISO-8859-1', 'UTF-8', 'Oui' ),
							   -1 => iconv ( 'ISO-8859-1', 'UTF-8', 'Non' ),
						)
				),
				'attributes' => array (
						'onchange' => 'nombreRedoublementAPVue(this.value)',
						'id' => 'redoublementAP',
						'style' => 'float: right;'
				)
		) );
		
		$this->add ( array (
				'name' => 'nombreRedoublementAP',
				'type' => 'Number',
				'attributes' => array (
						'id' => 'nombreRedoublementAP',
						'style' => 'width: 60px; text-align: right;',
						'min' => 1,
						'max' => 30
				)
		) );
		
		
		//ANTECEDENTS FAMILIAUX --- ANTECEDENTS FAMILIAUX --- ANTECEDENTS FAMILIAUX
		//ANTECEDENTS FAMILIAUX --- ANTECEDENTS FAMILIAUX --- ANTECEDENTS FAMILIAUX
		//CONSANGUINITE
		//CONSANGUINITE
		//CONSANGUINITE
		$this->add ( array (
				'name' => 'consanguiniteAF',
				'type' => 'Select',
				'options' => array (
						'value_options' => array (
							   '' => iconv ( 'ISO-8859-1', 'UTF-8', '' ),
								1 => iconv ( 'ISO-8859-1', 'UTF-8', 'OUI' ),
							   -1 => iconv ( 'ISO-8859-1', 'UTF-8', 'NON' )
						)
				),
				'attributes' => array (
						'id' => 'consanguiniteAF'
				)
		) );
		
		$this->add ( array (
				'name' => 'degreAF',
				'type' => 'Select',
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
				'type' => 'Select',
				'options' => array (
						'value_options' => array (
								'' => iconv ( 'ISO-8859-1', 'UTF-8', '' ),
								'1' => iconv ( 'ISO-8859-1', 'UTF-8', 'AS' ),
								'2' => iconv ( 'ISO-8859-1', 'UTF-8', 'AC' ),
								'3' => iconv ( 'ISO-8859-1', 'UTF-8', 'A-Bth' ),
								'4' => iconv ( 'ISO-8859-1', 'UTF-8', 'SS' ),
								'5' => iconv ( 'ISO-8859-1', 'UTF-8', 'SC' ),
								'6' => iconv ( 'ISO-8859-1', 'UTF-8', 'S-Bth' ),
								'-1' => iconv ( 'ISO-8859-1', 'UTF-8', 'Autres ...' ),
								'-2' => iconv ( 'ISO-8859-1', 'UTF-8', 'Inconnu' ),
						)
				),
				'attributes' => array (
						'id' => 'statutDrepanocytoseMereAF'
				)
		) );
		
		$this->add ( array (
				'name' => 'statutDrepanocytosePereAF',
				'type' => 'Select',
				'options' => array (
						'value_options' => array ('' => iconv ( 'ISO-8859-1', 'UTF-8', '' ),
								'1' => iconv ( 'ISO-8859-1', 'UTF-8', 'AS' ),
								'2' => iconv ( 'ISO-8859-1', 'UTF-8', 'AC' ),
								'3' => iconv ( 'ISO-8859-1', 'UTF-8', 'A-Bth' ),
								'4' => iconv ( 'ISO-8859-1', 'UTF-8', 'SS' ),
								'5' => iconv ( 'ISO-8859-1', 'UTF-8', 'SC' ),
								'6' => iconv ( 'ISO-8859-1', 'UTF-8', 'S-Bth' ),
								'-1' => iconv ( 'ISO-8859-1', 'UTF-8', 'Autres ...' ),
								'-2' => iconv ( 'ISO-8859-1', 'UTF-8', 'Inconnu' ),
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
				'type' => 'Number',
				'attributes' => array (
						'id' => 'fratrieRangAF',
						'min' => 1,
						'max' =>  15,
						'style' => 'text-align: right',
				)
		) );
		
		$this->add ( array (
				'name' => 'nbChoixStatutEnfantAF',
				'type' => 'Hidden',
				'attributes' => array (
						'id' => 'nbChoixStatutEnfantAF',
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
				'type' => 'Select',
				'options' => array (
						'value_options' => array (
								'' => iconv ( 'ISO-8859-1', 'UTF-8', '' ),
								 1 => iconv ( 'ISO-8859-1', 'UTF-8', 'Oui' ),
								-1 => iconv ( 'ISO-8859-1', 'UTF-8', 'Non' ),
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
						'style' => 'width:42px; font-size: 16px;',
						'max' => 20,
						'min' => 1,
				)
		) );
		
		$this->add ( array (
				'name' => 'typeHM',
				'type' => 'Select',
				'options' => array (
						'value_options' => array (
								'' => iconv ( 'ISO-8859-1', 'UTF-8', '' ),
								1 => iconv ( 'ISO-8859-1', 'UTF-8', 'Abdominale' ),
								2 => iconv ( 'ISO-8859-1', 'UTF-8', 'Osseux' ),
								100 => iconv ( 'ISO-8859-1', 'UTF-8', 'Autres..' ),
						)
				),
				'attributes' => array (
						'id' => 'typeHM',
						'style' => 'width:100px; font-size: 13.5px;',
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
						'style' => 'width:100px; font-size: 14px;',
				)
		) );
		
		$this->add ( array (
				'name' => 'facteur_declenchantHM',
				'type' => 'Select',
				'options' => array (
						'value_options' => array (
								'' => iconv ( 'ISO-8859-1', 'UTF-8', '' ),
								'1' => iconv ( 'ISO-8859-1', 'UTF-8', 'Fièvre' ),
								'2' => iconv ( 'ISO-8859-1', 'UTF-8', 'Refroidissement' ),
								'3' => iconv ( 'ISO-8859-1', 'UTF-8', 'Activité intense' ),
								'-1' => iconv ( 'ISO-8859-1', 'UTF-8', 'Néant' ),
								'-2' => iconv ( 'ISO-8859-1', 'UTF-8', 'Autre' ),
						)
				),
				'attributes' => array (
						'id' => 'facteur_declenchantHM',
						'style' => 'width:140px; font-size: 14px;',
				)
		) );
		
		
		$this->add ( array (
				'name' => 'episodeFievreHM',
				'type' => 'Select',
				'options' => array (
						'value_options' => array (
								'' => iconv ( 'ISO-8859-1', 'UTF-8', '' ),
								 1 => iconv ( 'ISO-8859-1', 'UTF-8', 'Oui' ),
								-1 => iconv ( 'ISO-8859-1', 'UTF-8', 'Non' ),
						)
				),
				'attributes' => array (
						'onchange' => 'getInfoEpisodeFievre(this.value)',
						'id' => 'episodeFievreHM',
						'style' => 'float:right;'
				)
		) );
		
		$this->add ( array (
				'name' => 'episodeFievreSiOuiHM',
				'type' => 'Select',
				'options' => array (
						'value_options' => array (
								'' => iconv ( 'ISO-8859-1', 'UTF-8', '' ),
								'1' => iconv ( 'ISO-8859-1', 'UTF-8', 'Signe respiratoire et/ou ORL' ),
								'2' => iconv ( 'ISO-8859-1', 'UTF-8', 'Diarrhée et/ou vomissements' ),
								'-2' => iconv ( 'ISO-8859-1', 'UTF-8', 'Autre..' ),
						)
				),
				'attributes' => array (
						'id' => 'episodeFievreSiOuiHM',
						'style' => 'float:right; font-size: 16px;'
				)
		) );
		
		$this->add ( array (
				'name' => 'hospitalisationHM',
				'type' => 'Select',
				'options' => array (
						'value_options' => array (
								'' => iconv ( 'ISO-8859-1', 'UTF-8', '' ),
								 1 => iconv ( 'ISO-8859-1', 'UTF-8', 'Oui' ),
								-1 => iconv ( 'ISO-8859-1', 'UTF-8', 'Non' ),
						)
				),
				'attributes' => array (
						'onchange' => 'getHospitalisationHM(this.value)',
						'id' => 'hospitalisationHM',
						'style' => 'float:right'
				)
		) );
		
		$this->add ( array (
				'name' => 'nombreHospitalisationHM',
				'type' => 'Number',
				'options' => array (
				),
				'attributes' => array (
						'id' => 'nombreHospitalisationHM',
						'style' => 'width:42px; font-size: 16px;',
						'max' => 20,
						'min' => 1,
				)
		) );
		
		$this->add ( array (
				'name' => 'dateHospitalisationHM',
				'type' => 'Date',
				'attributes' => array (
						'id' => 'dateHospitalisationHM',
						'style' => 'width:170px; padding-left: 5px;'
				)
		) );
		
		$this->add ( array (
				'name' => 'dureeHospitalisationHM',
				'type' => 'Number',
				'attributes' => array (
						'id' => 'dureeHospitalisationHM',
						'style' => 'width:44px;'
				)
		) );
		
		$this->add ( array (
				'name' => 'motifHospitalisationHM',
				'type' => 'Text',
				'attributes' => array (
						'id' => 'motifHospitalisationHM',
						'style' => 'width:180px;'
				)
		) );
		
		$this->add ( array (
				'name' => 'priseEnChargeHospitalisationHM',
				'type' => 'Hidden',
				'attributes' => array (
						'id' => 'priseEnChargeHospitalisationHM',
				)
		) );
		
		$this->add ( array (
				'name' => 'nombrePerfusionHospitalisationHM',
				'type' => 'Hidden',
				'attributes' => array (
						'id' => 'nombrePerfusionHospitalisationHM',
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
				'type' => 'Select',
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
				'type' => 'Select',
				'options' => array (
						'value_options' => array (
								'' => iconv ( 'ISO-8859-1', 'UTF-8', '' ),
								 1 => iconv ( 'ISO-8859-1', 'UTF-8', 'Oui' ),
								-1 => iconv ( 'ISO-8859-1', 'UTF-8', 'Non' ),
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
				'type' => 'Select',
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
				'type' => 'Select',
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
				'type' => 'Select',
				'options' => array (
						'value_options' => array (
								'' => iconv ( 'ISO-8859-1', 'UTF-8', '' ),
								 1 => iconv ( 'ISO-8859-1', 'UTF-8', 'Oui' ),
								-1 => iconv ( 'ISO-8859-1', 'UTF-8', 'Non' ),
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
				'type' => 'Select',
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
				'type' => 'Text',
				'attributes' => array (
						'id' => 'precisionExamenDesPoumonsDonneesExamen',
						'style' => 'float:right;width:85%;'
				)
		) );
		
		/**Examen du coeur**/
		$this->add ( array (
				'name' => 'examenDuCoeurDonneesExamen',
				'type' => 'Select',
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
				'type' => 'Text',
				'attributes' => array (
						'id' => 'precisionExamenDuCoeurDonneesExamen',
						'style' => 'float:right;width:85%;'
				)
		) );
		
		/**Examen du foie et voie biliaire**/
		$this->add ( array (
				'name' => 'examenDuFoieVoieBiliaireDonneesExamen',
				'type' => 'Select',
				'options' => array (
						'value_options' => array (
								'' => iconv ( 'ISO-8859-1', 'UTF-8', '' ),
								'1' => iconv ( 'ISO-8859-1', 'UTF-8', 'Normal' ),
								'2' => iconv ( 'ISO-8859-1', 'UTF-8', "Douleur de l'HCD" ),
								'3' => iconv ( 'ISO-8859-1', 'UTF-8', "Hépatomégalie" ),
						)
				),
				'attributes' => array (
						'id' => 'examenDuFoieVoieBiliaireDonneesExamen',
						'style' => 'float:right; font-size: 12px;'
				)
		) );
		
		/**Hanche**/
		$this->add ( array (
				'name' => 'examenHancheDonneesExamen',
				'type' => 'Select',
				'options' => array (
						'value_options' => array (
								'' => iconv ( 'ISO-8859-1', 'UTF-8', '' ),
								'1' => iconv ( 'ISO-8859-1', 'UTF-8', 'Normal' ),
								'2' => iconv ( 'ISO-8859-1', 'UTF-8', "Douleur" ),
						)
				),
				'attributes' => array (
						'id' => 'examenHancheDonneesExamen',
						'style' => 'float:right; font-size: 16px; '
				)
		) );
		
		/**Epaule**/
		$this->add ( array (
				'name' => 'examenEpauleDonneesExamen',
				'type' => 'Select',
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
				'type' => 'Select',
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
				'type' => 'Text',
				'attributes' => array (
						'id' => 'autresDonneesExamen',
						'style' => 'float:right;width:78%;'
				)
		) );
		
		/**Synth&eagrave;se de la consultation du jour**/
		$this->add ( array (
				'name' => 'syntheseConsultationDuJourDonneesExamen',
				'type' => 'Textarea',
				'attributes' => array (
						'id' => 'syntheseConsultationDuJourDonneesExamen',
						'style' => 'float:right; width:85%; min-width:85%; max-width:85%; height: 100%; min-height: 100%; max-height: 100%;'
				)
		) );
		
		
		/**Examen complémentaire --- Examen complémentaire --- Examen complémentaire**/
		/**Examen complémentaire --- Examen complémentaire --- Examen complémentaire**/
		$this->add ( array (
				'name' => 'nbDemandeExamenComplementaire',
				'type' => 'Hidden',
				'attributes' => array (
						'id' => 'nbDemandeExamenComplementaire',
				)
		) );
		
		
		/**Diagnostic --- Diagnostic --- Diagnostic **/
		/**Diagnostic --- Diagnostic --- Diagnostic **/
		$this->add ( array (
				'name' => 'diagnosticDuJourConsultation',
				'type' => 'Textarea',
				'attributes' => array (
						'id' => 'diagnosticDuJourConsultation',
						'style' => 'float:left; width: 450px; min-width:450px; max-width:450px; height: 205px; min-height: 205px; max-height: 205px;'
				)
		) );
		
		
		/**Complications aigues --- Complications aigues **/
		/**Complications aigues --- Complications aigues **/
		$this->add ( array (
				'name' => 'nbDiagnosticComplicationsAigues',
				'type' => 'Hidden',
				'attributes' => array (
						'id' => 'nbDiagnosticComplicationsAigues',
				)
		) );
		
		$this->add ( array (
				'name' => 'diagnosticComplicationsAigues',
				'type' => 'Select',
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
				'name' => 'nbDiagnosticComplicationsChroniques',
				'type' => 'Hidden',
				'attributes' => array (
						'id' => 'nbDiagnosticComplicationsChroniques',
				)
		) );
		
		$this->add ( array (
				'name' => 'diagnosticComplicationsChroniques',
				'type' => 'Select',
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
		
		
		
		/**Autres (Transfert / Hospitalisation / Rendez-Vous) ---  **/
		/**Autres (Transfert / Hospitalisation / Rendez-Vous) ---  **/
		
		/**
		 * Rendez-Vous --- Rendez-Vous 
		 * Rendez-Vous --- Rendez-Vous
		 */
		$this->add ( array (
				'name' => 'dateHeureRendezVous',
				'type' => 'Text',
				'options' => array (
						'label' => 'Date & Heure'
				),
				'attributes' => array (
						'id' => 'dateHeureRendezVous',
						'style' => 'font-weight: bold; font-size: 17px;'
				)
		) );
		
		$this->add ( array (
				'name' => 'motifRendezVous',
				'type' => 'Textarea',
				'attributes' => array (
						'id' => 'motifRendezVous',
						'style' => 'height: 135px; min-height: 135px; max-height: 135px;'
				)
		) );
		
		
		/**
		 * Hospitalisation --- Hospitalisation
		 * Hospitalisation --- Hospitalisation
		 */
		$this->add ( array (
				'name' => 'motifHospitalisation',
				'type' => 'Textarea',
				'options' => array (
						'label' => 'Motif'
				),
				'attributes' => array (
						'id' => 'motifHospitalisation',
						'style' => 'height: 135px; min-height: 135px; max-height: 135px;'
				)
		) );
		
		$this->add ( array (
				'name' => 'diagnosticRetenuHospitalisation',
				'type' => 'Textarea',
				'options' => array (
						'label' => 'Diagnostic retenu'
				),
				'attributes' => array (
						'id' => 'diagnosticRetenuHospitalisation',
						'style' => 'height: 135px; min-height: 135px; max-height: 135px;'
				)
		) );
		
		
		/**
		 * Transfert --- Transfert --- Transfert
		 * Transfert --- Transfert --- Transfert
		 */
		
		$this->add ( array (
				'name' => 'motifTransfert',
				'type' => 'Textarea',
				'options' => array (
						'label' => 'Motif'
				),
				'attributes' => array (
						'id' => 'motifTransfert',
						'style' => 'height: 135px; min-height: 135px; max-height: 135px;'
				)
		) );
		
		$this->add ( array (
				'name' => 'hopitalAccueil',
				'type' => 'Text',
				'options' => array (
						'label' => 'Hopital'
				),
				'attributes' => array (
						'id' => 'hopitalAccueil',
						'style' => 'font-weight: bold; font-size: 17px;'
				)
		) );
		
		
		$this->add ( array (
				'name' => 'serviceAccueil',
				'type' => 'Text',
				'options' => array (
						'label' => 'Service'
				),
				'attributes' => array (
						'id' => 'serviceAccueil',
						'style' => 'font-weight: bold; font-size: 17px;'
				)
		) );
		
		
		/**
		 * Transfusion --- Transfusion --- Transfusion
		 * Transfusion --- Transfusion --- Transfusion
		 */
		
		$this->add ( array (
				'name' => 'groupeSanguinTransfusion',
				'type' => 'Select',
				'options' => array (
						'value_options' => array (
								'' => iconv ( 'ISO-8859-1', 'UTF-8', '' ),
								'A+' => iconv ( 'ISO-8859-1', 'UTF-8', 'A+' ),
								'A-' => iconv ( 'ISO-8859-1', 'UTF-8', 'A-' ),
								'B+' => iconv ( 'ISO-8859-1', 'UTF-8', 'B+' ),
								'B-' => iconv ( 'ISO-8859-1', 'UTF-8', 'B-' ),
								'AB+' => iconv ( 'ISO-8859-1', 'UTF-8', 'AB+' ),
								'AB-' => iconv ( 'ISO-8859-1', 'UTF-8', 'AB-' ),
								'O+' => iconv ( 'ISO-8859-1', 'UTF-8', 'O+' ),
								'O-' => iconv ( 'ISO-8859-1', 'UTF-8', 'O-' ),
						)
				),
				'attributes' => array (
						'id' => 'groupeSanguinTransfusion',
						'style' => 'font-size: 15px;'
				)
		) );
		
		$this->add ( array (
				'name' => 'produitSanguin1',
				'type' => 'Select',
				'options' => array (
						'value_options' => array (
								'' => iconv ( 'ISO-8859-1', 'UTF-8', '' ),
								'1' => iconv ( 'ISO-8859-1', 'UTF-8', 'Culot globulaire' ),
								'2' => iconv ( 'ISO-8859-1', 'UTF-8', 'Sang total' ),
						)
				),
				'attributes' => array (
						'onchange' => 'getproduitSanguin1Quantite(this.value)',
						'id' => 'produitSanguin1',
						'style' => ''
				)
		) );
		
		$this->add ( array (
				'name' => 'produitSanguin1Quantite',
				'type' => 'Number',
				'attributes' => array (
						'id' => 'produitSanguin1Quantite',
						'style' => 'width: 55px;',
						'min' => 1,
						'max' => 999
				)
		) );
		
		$this->add ( array (
				'name' => 'produitSanguin2',
				'type' => 'Select',
				'options' => array (
						'value_options' => array (
								'' => iconv ( 'ISO-8859-1', 'UTF-8', '' ),
								'1' => iconv ( 'ISO-8859-1', 'UTF-8', 'Plaquette' ),
						)
				),
				'attributes' => array (
						'onchange' => 'getproduitSanguin2Quantite(this.value)',
						'id' => 'produitSanguin2',
						'style' => ''
				)
		) );
		
		$this->add ( array (
				'name' => 'produitSanguin2Quantite',
				'type' => 'Number',
				'attributes' => array (
						'id' => 'produitSanguin2Quantite',
						'style' => 'width: 55px;',
						'min' => 1,
						'max' => 999
				)
		) );
		
		
		$this->add ( array (
				'name' => 'reactionTransfusionnel',
				'type' => 'Select',
				'options' => array (
						'value_options' => array (
								'' => iconv ( 'ISO-8859-1', 'UTF-8', '' ),
								'1' => iconv ( 'ISO-8859-1', 'UTF-8', 'Oui' ),
						)
				),
				'attributes' => array (
						'onchange' => 'getReactionTransfusionnelSiOui(this.value)',
						'id' => 'reactionTransfusionnel',
						'style' => ''
				)
		) );
		
		$this->add ( array (
				'name' => 'reactionTransfusionnelValeur',
				'type' => 'Select',
				'options' => array (
						'value_options' => array (
								'' => iconv ( 'ISO-8859-1', 'UTF-8', '' ),
								'1' => iconv ( 'ISO-8859-1', 'UTF-8', 'Accident par incompatibilité ABO' ),
								'2' => iconv ( 'ISO-8859-1', 'UTF-8', 'Accident par incompatibilité Rhésus/Kell' ),
								'3' => iconv ( 'ISO-8859-1', 'UTF-8', 'Réaction immuno-allergique' ),
								'-1' => iconv ( 'ISO-8859-1', 'UTF-8', 'Autres...' ),
						)
				),
				'attributes' => array (
						'id' => 'reactionTransfusionnelValeur',
						'style' => 'width: 70%;'
				)
		) );
		
		
		
		
		
	}
}