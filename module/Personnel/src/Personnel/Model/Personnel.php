<?php

namespace Personnel\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;

class Personnel {
	public $id_personne;
	public $civilite;
	public $nom;
	public $prenom;
	public $date_naissance;
	public $lieu_naissance;
	public $adresse;
	public $sexe;
	public $situation_matrimoniale;
	public $nationalite;
	public $photo;
	public $telephone;
	public $email;
	public $profession;
	public $date_enregistrement;
	
	public $type_personnel;
	public $matricule;
	public $grade;
	public $specialite;
	public $fonction;
	
	public $matricule_medico;
	public $grade_medico;
	public $domaine_medico;
	public $autres;
	
	public $matricule_logistique;
	public $grade_logistique;
	public $domaine_logistique;
	public $autres_logistique;
	
	public $matricule_secretaire;
	public $grade_secretaire;
	public $domaine_secretaire;
	public $autres_secretaire;
	
	public $matricule_laborantin;
	public $grade_laborantin;
	public $domaine_laborantin;
	public $autres_laborantin;
	
	public $matricule_infirmier;
	public $grade_infirmier;
	public $domaine_infirmier;
	public $autres_infirmier;
	
	public $service_accueil;
	public $date_debut;
	public $date_fin;
	public $numero_os;
	
	public $fichier_tmp;
	
	protected $inputFilter;
	
	public function exchangeArray($data) {
		$this->id_personne = (! empty ( $data ['id_personne'] )) ? $data ['id_personne'] : null;
 		$this->civilite = (! empty ( $data ['civilite'] )) ? $data ['civilite'] : null;
 		$this->lieu_naissance = (! empty ( $data ['lieu_naissance'] )) ? $data ['lieu_naissance'] : null;
		$this->nom = (! empty ( $data ['nom'] )) ? $data ['nom'] : null;
		$this->prenom = (! empty ( $data ['prenom'] )) ? $data ['prenom'] : null;
 		$this->date_naissance = (! empty ( $data ['date_naissance'] )) ? $data ['date_naissance'] : null;
 		$this->adresse = (! empty ( $data ['adresse'] )) ? $data ['adresse'] : null;
 		$this->sexe = (! empty ( $data ['sexe'] )) ? $data ['sexe'] : null;
 		$this->situation_matrimoniale = (! empty ( $data ['situation_matrimoniale'] )) ? $data ['situation_matrimoniale'] : null;
 		$this->telephone = (! empty ( $data ['telephone'] )) ? $data ['telephone'] : null;
 		$this->email = (! empty ( $data ['email'] )) ? $data ['email'] : null;
 		$this->profession = (! empty ( $data ['profession'] )) ? $data ['profession'] : null;
 		$this->date_enregistrement = (! empty ( $data ['date_enregistrement'] )) ? $data ['date_enregistrement'] : null;
 		$this->nationalite = (! empty ( $data ['nationalite'] )) ? $data ['nationalite'] : null;
 		$this->photo = (! empty ( $data ['photo'] )) ? $data ['photo'] : null;

 		//COMPLEMENT PERSONNEL
 		//COMPLEMENT PERSONNEL
 		//COMPLEMENT PERSONNEL
 		$this->type_personnel = (! empty ( $data ['type_personnel'] )) ? $data ['type_personnel'] : null;
 		$this->matricule = (! empty ( $data ['matricule'] )) ? $data ['matricule'] : null;
 		$this->grade = (! empty ( $data ['grade'] )) ? $data ['grade'] : null;
 		$this->specialite = (! empty ( $data ['specialite'] )) ? $data ['specialite'] : null;
 		$this->fonction = (! empty ( $data ['fonction'] )) ? $data ['fonction'] : null;
 		
 		$this->matricule_medico = (! empty ( $data ['matricule_medico'] )) ? $data ['matricule_medico'] : null;
 		$this->grade_medico = (! empty ( $data ['grade_medico'] )) ? $data ['grade_medico'] : null;
 		$this->domaine_medico = (! empty ( $data ['domaine_medico'] )) ? $data ['domaine_medico'] : null;
 		$this->autres = (! empty ( $data ['autres'] )) ? $data ['autres'] : null;
 		
 		$this->matricule_logistique = (! empty ( $data ['matricule_logistique'] )) ? $data ['matricule_logistique'] : null;
 		$this->grade_logistique = (! empty ( $data ['grade_logistique'] )) ? $data ['grade_logistique'] : null;
 		$this->domaine_logistique = (! empty ( $data ['domaine_logistique'] )) ? $data ['domaine_logistique'] : null;
 		$this->autres_logistique = (! empty ( $data ['autres_logistique'] )) ? $data ['autres_logistique'] : null;
 		
 		$this->matricule_secretaire = (! empty ( $data ['matricule_secretaire'] )) ? $data ['matricule_secretaire'] : null;
 		$this->grade_secretaire = (! empty ( $data ['grade_secretaire'] )) ? $data ['grade_secretaire'] : null;
 		$this->domaine_secretaire = (! empty ( $data ['domaine_secretaire'] )) ? $data ['domaine_secretaire'] : null;
 		$this->autres_secretaire = (! empty ( $data ['autres_secretaire'] )) ? $data ['autres_secretaire'] : null;
 			
 		$this->matricule_laborantin = (! empty ( $data ['matricule_laborantin'] )) ? $data ['matricule_laborantin'] : null;
 		$this->grade_laborantin = (! empty ( $data ['grade_laborantin'] )) ? $data ['grade_laborantin'] : null;
 		$this->domaine_laborantin = (! empty ( $data ['domaine_laborantin'] )) ? $data ['domaine_laborantin'] : null;
 		$this->autres_laborantin = (! empty ( $data ['autres_laborantin'] )) ? $data ['autres_laborantin'] : null;
 		
 		$this->matricule_infirmier = (! empty ( $data ['matricule_infirmier'] )) ? $data ['matricule_infirmier'] : null;
 		$this->grade_infirmier = (! empty ( $data ['grade_infirmier'] )) ? $data ['grade_infirmier'] : null;
 		$this->domaine_infirmier = (! empty ( $data ['domaine_infirmier'] )) ? $data ['domaine_infirmier'] : null;
 		$this->autres_infirmier = (! empty ( $data ['autres_infirmier'] )) ? $data ['autres_infirmier'] : null;
 			
 		
 		//AFFECTATION
 		//AFFECTATION
 		//AFFECTATION
 		$this->service_accueil = (! empty ( $data ['service_accueil'] )) ? $data ['service_accueil'] : null;
 		$this->date_debut = (! empty ( $data ['date_debut'] )) ? $data ['date_debut'] : null;
 		$this->date_fin = (! empty ( $data ['date_fin'] )) ? $data ['date_fin'] : null;
 		$this->numero_os = (! empty ( $data ['numero_os'] )) ? $data ['numero_os'] : null;
 		
 		$this->fichier_tmp = (! empty ( $data ['fichier_tmp'] )) ? $data ['fichier_tmp'] : null;
	}
	public function getArrayCopy() {
		return get_object_vars ( $this );
	}
	public function setInputFilter(InputFilterInterface $inputFilter) {
		throw new \Exception ( "Not used" );
	}
	public function getInputFilter() {
		if (! $this->inputFilter) {

			$inputFilter = new InputFilter ();
			$inputFilter->add(array(
					'name'     => 'id_personne',
					'required' => true, //Par défaut c'est TRUE
					'filters'  => array(
							array('name' => 'Int'),
					),
			));

			$inputFilter->add (array (
					'name' => 'date_enregistrement',
					'required' => false,
					'filters' => array (
							array (
									'name' => 'StripTags'
							),
							array (
									'name' => 'StringTrim'
							)
					)
			) );
			
			$inputFilter->add (array (
					'name' => 'civilite',
					'required' => false, //true
					'filters' => array (
							array (
									'name' => 'StripTags'
							),
							array (
									'name' => 'StringTrim'
							)
					),
					
			) );
			
			$inputFilter->add (array (
					'name' => 'nom',
					'required' => true, //true
					'filters' => array (
							array (
									'name' => 'StripTags'
							),
							array (
									'name' => 'StringTrim'
							)
					),
					'validators' => array (
							array (
									'name' => 'StringLength',
									'options' => array (
											'encoding' => 'UTF-8',
											'min' => 1,
											'max' => 100
									)
							)
					)
			 ) );

			$inputFilter->add (array (
					'name' => 'prenom',
					'required' => true, //True
					'filters' => array (
							array (
									'name' => 'StripTags'
							),
							array (
									'name' => 'StringTrim'
							)
					),
					'validators' => array (
							array (
									'name' => 'StringLength',
									'options' => array (
											'encoding' => 'UTF-8',
											'min' => 1,
											'max' => 100
									)
							)
					)
			 ) );
			
			 $inputFilter->add (array (
					'name' => 'sexe',
					'required' => false, //true
					'filters' => array (
							array (
									'name' => 'StripTags'
							),
							array (
									'name' => 'StringTrim'
							)
					),
					'validators' => array (
							array (
									'name' => 'StringLength',
									'options' => array (
											'encoding' => 'UTF-8',
											'min' => 1,
											'max' => 100
									)
							)
					)
			 ) );
			 
			 
			 $inputFilter->add (array (
			 		'name' => 'date_naissance',
			 		'required' => false,
			 		'filters' => array (
			 				array (
			 						'name' => 'StripTags'
			 				),
			 				array (
			 						'name' => 'StringTrim'
			 				)
			 		),
			 		'validators' => array (
			 				array (
			 						'name' => 'StringLength',
			 						'options' => array (
			 								'encoding' => 'UTF-8',
			 								'min' => 1,
			 								'max' => 100
			 						)
			 				)
			 		)
			 ) );
			 
			 
			 $inputFilter->add (array (
			 		'name' => 'lieu_naissance',
			 		'required' => false,
			 		'filters' => array (
			 				array (
			 						'name' => 'StripTags'
			 				),
			 				array (
			 						'name' => 'StringTrim'
			 				)
			 		),
			 		'validators' => array (
			 				array (
			 						'name' => 'StringLength',
			 						'options' => array (
			 								'encoding' => 'UTF-8',
			 								'min' => 1,
			 								'max' => 100
			 						)
			 				)
			 		)
			 ) );
			 
			 
 
 $inputFilter->add (array (
					'name' => 'situation_matrimoniale',
					'required' => false,
					'filters' => array (
							array (
									'name' => 'StripTags'
							),
							array (
									'name' => 'StringTrim'
							)
					),
					'validators' => array (
							array (
									'name' => 'StringLength',
									'options' => array (
											'encoding' => 'UTF-8',
											'min' => 1,
											'max' => 100
									)
							)
					)
			 ) );
			 
			 
			 $inputFilter->add (array (
					'name' => 'nationalite',
					'required' => false,
					'filters' => array (
							array (
									'name' => 'StripTags'
							),
							array (
									'name' => 'StringTrim'
							)
					),
					'validators' => array (
							array (
									'name' => 'StringLength',
									'options' => array (
											'encoding' => 'UTF-8',
											'min' => 1,
											'max' => 100
									)
							)
					)
			 ) );
			 
			 
			 $inputFilter->add (array (
					'name' => 'adresse',
					'required' => false,
					'filters' => array (
							array (
									'name' => 'StripTags'
							),
							array (
									'name' => 'StringTrim'
							)
					),
					'validators' => array (
							array (
									'name' => 'StringLength',
									'options' => array (
											'encoding' => 'UTF-8',
											'min' => 1,
											'max' => 100
									)
							)
					)
			 ) );
			 
			 
			 $inputFilter->add (array (
					'name' => 'telephone',
					'required' => false,
					'filters' => array (
							array (
									'name' => 'StripTags'
							),
							array (
									'name' => 'StringTrim'
							)
					),
					'validators' => array (
							array (
									'name' => 'StringLength',
									'options' => array (
											'encoding' => 'UTF-8',
											'min' => 1,
											'max' => 100
									)
							)
					)
			 ) );
			 
			 
			 $inputFilter->add (array (
					'name' => 'email',
					'required' => false,
					'filters' => array (
							array (
									'name' => 'StripTags'
							),
							array (
									'name' => 'StringTrim'
							)
					),
					'validators' => array (
							array (
									'name' => 'StringLength',
									'options' => array (
											'encoding' => 'UTF-8',
											'min' => 1,
											'max' => 100
									)
							)
					)
			 ) );
			 
			 
			 $inputFilter->add (array (
					'name' => 'profession',
					'required' => false,
					'filters' => array (
							array (
									'name' => 'StripTags'
							),
							array (
									'name' => 'StringTrim'
							)
					),
					'validators' => array (
							array (
									'name' => 'StringLength',
									'options' => array (
											'encoding' => 'UTF-8',
											'min' => 1,
											'max' => 100
									)
							)
					)
			 ) );
			 
			 
			 $inputFilter->add (array (
					'name' => 'type_personnel',
					'required' => false,
					'filters' => array (
							array (
									'name' => 'StripTags'
							),
							array (
									'name' => 'StringTrim'
							)
					),
					'validators' => array (
							array (
									'name' => 'StringLength',
									'options' => array (
											'encoding' => 'UTF-8',
											'min' => 1,
											'max' => 100
									)
							)
					)
			 ) );
			 
		 
			 $inputFilter->add (array (
					'name' => 'matricule',
					'required' => false,
					'filters' => array (
							array (
									'name' => 'StripTags'
							),
							array (
									'name' => 'StringTrim'
							)
					),
					'validators' => array (
							array (
									'name' => 'StringLength',
									'options' => array (
											'encoding' => 'UTF-8',
											'min' => 1,
											'max' => 100
									)
							)
					)
			 ) );
			 
			 
			 $inputFilter->add (array (
					'name' => 'grade',
					'required' => false,
					'filters' => array (
							array (
									'name' => 'StripTags'
							),
							array (
									'name' => 'StringTrim'
							)
					),
					'validators' => array (
							array (
									'name' => 'StringLength',
									'options' => array (
											'encoding' => 'UTF-8',
											'min' => 1,
											'max' => 100
									)
							)
					)
			 ) );
			 
			 $inputFilter->add (array (
			 		'name' => 'specialite',
			 		'required' => false,
			 		'filters' => array (
			 				array (
			 						'name' => 'StripTags'
			 				),
			 				array (
			 						'name' => 'StringTrim'
			 				)
			 		),
			 		'validators' => array (
			 				array (
			 						'name' => 'StringLength',
			 						'options' => array (
			 								'encoding' => 'UTF-8',
			 								'min' => 1,
			 								'max' => 100
			 						)
			 				)
			 		)
			 ) );
		 
			 
			 $inputFilter->add (array (
			 		'name' => 'fonction',
			 		'required' => false,
			 		'filters' => array (
			 				array (
			 						'name' => 'StripTags'
			 				),
			 				array (
			 						'name' => 'StringTrim'
			 				)
			 		),
			 		'validators' => array (
			 				array (
			 						'name' => 'StringLength',
			 						'options' => array (
			 								'encoding' => 'UTF-8',
			 								'min' => 1,
			 								'max' => 100
			 						)
			 				)
			 		)
			 ) );

			 $inputFilter->add (array (
			 		'name' => 'matricule_medico',
			 		'required' => false,
			 		'filters' => array (
			 				array (
			 						'name' => 'StripTags'
			 				),
			 				array (
			 						'name' => 'StringTrim'
			 				)
			 		),
			 		'validators' => array (
			 				array (
			 						'name' => 'StringLength',
			 						'options' => array (
			 								'encoding' => 'UTF-8',
			 								'min' => 1,
			 								'max' => 100
			 						)
			 				)
			 		)
			 ) );
	
			 $inputFilter->add (array (
			 		'name' => 'grade_medico',
			 		'required' => false,
			 		'filters' => array (
			 				array (
			 						'name' => 'StripTags'
			 				),
			 				array (
			 						'name' => 'StringTrim'
			 				)
			 		),
			 		'validators' => array (
			 				array (
			 						'name' => 'StringLength',
			 						'options' => array (
			 								'encoding' => 'UTF-8',
			 								'min' => 1,
			 								'max' => 100
			 						)
			 				)
			 		)
			 ) );
			 
			 $inputFilter->add (array (
			 		'name' => 'domaine_medico',
			 		'required' => false,
			 		'filters' => array (
			 				array (
			 						'name' => 'StripTags'
			 				),
			 				array (
			 						'name' => 'StringTrim'
			 				)
			 		),
			 		'validators' => array (
			 				array (
			 						'name' => 'StringLength',
			 						'options' => array (
			 								'encoding' => 'UTF-8',
			 								'min' => 1,
			 								'max' => 100
			 						)
			 				)
			 		)
			 ) );
			 
			 $inputFilter->add (array (
			 		'name' => 'autres',
			 		'required' => false,
			 		'filters' => array (
			 				array (
			 						'name' => 'StripTags'
			 				),
			 				array (
			 						'name' => 'StringTrim'
			 				)
			 		),
			 		'validators' => array (
			 				array (
			 						'name' => 'StringLength',
			 						'options' => array (
			 								'encoding' => 'UTF-8',
			 								'min' => 1,
			 								'max' => 100
			 						)
			 				)
			 		)
			 ) );
			 
			 $inputFilter->add (array (
			 		'name' => 'matricule_logistique',
			 		'required' => false,
			 		'filters' => array (
			 				array (
			 						'name' => 'StripTags'
			 				),
			 				array (
			 						'name' => 'StringTrim'
			 				)
			 		),
			 		'validators' => array (
			 				array (
			 						'name' => 'StringLength',
			 						'options' => array (
			 								'encoding' => 'UTF-8',
			 								'min' => 1,
			 								'max' => 100
			 						)
			 				)
			 		)
			 ) );
			 
			 $inputFilter->add (array (
			 		'name' => 'grade_logistique',
			 		'required' => false,
			 		'filters' => array (
			 				array (
			 						'name' => 'StripTags'
			 				),
			 				array (
			 						'name' => 'StringTrim'
			 				)
			 		),
			 		'validators' => array (
			 				array (
			 						'name' => 'StringLength',
			 						'options' => array (
			 								'encoding' => 'UTF-8',
			 								'min' => 1,
			 								'max' => 100
			 						)
			 				)
			 		)
			 ) );
			 
			 $inputFilter->add (array (
			 		'name' => 'domaine_logistique',
			 		'required' => false,
			 		'filters' => array (
			 				array (
			 						'name' => 'StripTags'
			 				),
			 				array (
			 						'name' => 'StringTrim'
			 				)
			 		),
			 		'validators' => array (
			 				array (
			 						'name' => 'StringLength',
			 						'options' => array (
			 								'encoding' => 'UTF-8',
			 								'min' => 1,
			 								'max' => 100
			 						)
			 				)
			 		)
			 ) );
			 
			 $inputFilter->add (array (
			 		'name' => 'autres_logistique',
			 		'required' => false,
			 		'filters' => array (
			 				array (
			 						'name' => 'StripTags'
			 				),
			 				array (
			 						'name' => 'StringTrim'
			 				)
			 		),
			 		'validators' => array (
			 				array (
			 						'name' => 'StringLength',
			 						'options' => array (
			 								'encoding' => 'UTF-8',
			 								'min' => 1,
			 								'max' => 100
			 						)
			 				)
			 		)
			 ) );
		 
			 
			 $inputFilter->add (array (
			     'name' => 'matricule_secretaire',
			     'required' => false,
			     'filters' => array (
			         array (
			             'name' => 'StripTags'
			         ),
			         array (
			             'name' => 'StringTrim'
			         )
			     ),
			     'validators' => array (
			         array (
			             'name' => 'StringLength',
			             'options' => array (
			                 'encoding' => 'UTF-8',
			                 'min' => 1,
			                 'max' => 100
			             )
			         )
			     )
			 ) );
			 
			 $inputFilter->add (array (
			     'name' => 'grade_secretaire',
			     'required' => false,
			     'filters' => array (
			         array (
			             'name' => 'StripTags'
			         ),
			         array (
			             'name' => 'StringTrim'
			         )
			     ),
			     'validators' => array (
			         array (
			             'name' => 'StringLength',
			             'options' => array (
			                 'encoding' => 'UTF-8',
			                 'min' => 1,
			                 'max' => 100
			             )
			         )
			     )
			 ) );
			 
			 $inputFilter->add (array (
			     'name' => 'domaine_secretaire',
			     'required' => false,
			     'filters' => array (
			         array (
			             'name' => 'StripTags'
			         ),
			         array (
			             'name' => 'StringTrim'
			         )
			     ),
			     'validators' => array (
			         array (
			             'name' => 'StringLength',
			             'options' => array (
			                 'encoding' => 'UTF-8',
			                 'min' => 1,
			                 'max' => 100
			             )
			         )
			     )
			 ) );
			 
			 $inputFilter->add (array (
			     'name' => 'autres_secretaire',
			     'required' => false,
			     'filters' => array (
			         array (
			             'name' => 'StripTags'
			         ),
			         array (
			             'name' => 'StringTrim'
			         )
			     ),
			     'validators' => array (
			         array (
			             'name' => 'StringLength',
			             'options' => array (
			                 'encoding' => 'UTF-8',
			                 'min' => 1,
			                 'max' => 100
			             )
			         )
			     )
			 ) );
			 
			 
			 
			 $inputFilter->add (array (
			 		'name' => 'service_accueil',
			 		'required' => false,
			 		'filters' => array (
			 				array (
			 						'name' => 'StripTags'
			 				),
			 				array (
			 						'name' => 'StringTrim'
			 				)
			 		)
			 		
			 ) );
			 
			 $inputFilter->add (array (
			 		'name' => 'date_debut',
			 		'required' => false,
			 		'filters' => array (
			 				array (
			 						'name' => 'StripTags'
			 				),
			 				array (
			 						'name' => 'StringTrim'
			 				)
			 		),
			 		'validators' => array (
			 				array (
			 						'name' => 'StringLength',
			 						'options' => array (
			 								'encoding' => 'UTF-8',
			 								'min' => 1,
			 								'max' => 100
			 						)
			 				)
			 		)
			 ) );
			 
			 $inputFilter->add (array (
			 		'name' => 'date_fin',
			 		'required' => false,
			 		'filters' => array (
			 				array (
			 						'name' => 'StripTags'
			 				),
			 				array (
			 						'name' => 'StringTrim'
			 				)
			 		),
			 		'validators' => array (
			 				array (
			 						'name' => 'StringLength',
			 						'options' => array (
			 								'encoding' => 'UTF-8',
			 								'min' => 1,
			 								'max' => 100
			 						)
			 				)
			 		)
			 ) );
			 
			 $inputFilter->add (array (
			 		'name' => 'numero_os',
			 		'required' => false,
			 		'filters' => array (
			 				array (
			 						'name' => 'StripTags'
			 				),
			 				array (
			 						'name' => 'StringTrim'
			 				)
			 		),
			 		'validators' => array (
			 				array (
			 						'name' => 'StringLength',
			 						'options' => array (
			 								'encoding' => 'UTF-8',
			 								'min' => 1,
			 								'max' => 100
			 						)
			 				)
			 		)
			 ) );
			 
			 $inputFilter->add (array (
			 		'name' => 'fichier_tmp',
			 		'required' => false,
			 		'filters' => array (
			 				array (
			 						'name' => 'StripTags'
			 				),
			 				array (
			 						'name' => 'StringTrim'
			 				)
			 		)
			 ) );
			 
			 
			$this->inputFilter = $inputFilter;
		}

		return $this->inputFilter;
	}
}