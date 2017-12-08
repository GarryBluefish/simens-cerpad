<?php

namespace Secretariat\Model;

class listeRecherche {
	public $idpersonne;
	public $nom;
	public $prenom;
	public $date_naissance;
	public $lieu_naissance;
	public $adresse;
	public $sexe;
	public $age;
	public $situation_matrimoniale;
	public $nationalite_actuelle;
	public $nationalite_origine;
	public $telephone;
	public $email;
	public $profession;
	public $date_modification;
	public $photo;
	public $typepatient;
	
	public $ethnie;
	public $depistage;

	
	public function exchangeArray($data) {
		
		$this->idpersonne = (! empty ( $data ['idpersonne'] )) ? $data ['idpersonne'] : null;
 		$this->lieu_naissance = (! empty ( $data ['lieu_naissance'] )) ? $data ['lieu_naissance'] : null;
		$this->nom = (! empty ( $data ['nom'] )) ? $data ['nom'] : null;
		$this->prenom = (! empty ( $data ['prenom'] )) ? $data ['prenom'] : null;
 		$this->date_naissance = (! empty ( $data ['date_naissance'] )) ? $data ['date_naissance'] : null;
 		$this->adresse = (! empty ( $data ['adresse'] )) ? $data ['adresse'] : null;
 		$this->sexe = (! empty ( $data ['sexe'] )) ? $data ['sexe'] : null;
 		$this->age = (! empty ( $data ['age'] )) ? $data ['age'] : null;
 		$this->situation_matrimoniale = (! empty ( $data ['situation_matrimoniale'] )) ? $data ['situation_matrimoniale'] : null;
 		$this->telephone = (! empty ( $data ['telephone'] )) ? $data ['telephone'] : null;
 		$this->email = (! empty ( $data ['email'] )) ? $data ['email'] : null;
 		$this->profession = (! empty ( $data ['profession'] )) ? $data ['profession'] : null;
 		$this->nationalite_actuelle = (! empty ( $data ['nationalite_actuelle'] )) ? $data ['nationalite_actuelle'] : null;
 		$this->nationalite_origine = (! empty ( $data ['nationalite_origine'] )) ? $data ['nationalite_origine'] : null;
 		$this->photo = (! empty ( $data ['photo'] )) ? $data ['photo'] : null;
 		$this->date_modification = (! empty ( $data ['date_modification'] )) ? $data ['date_modification'] : null;
 		$this->typepatient = (! empty ( $data ['typepatient'] )) ? $data ['typepatient'] : null;
 		
 		$this->ethnie = (! empty ( $data ['ethnie'] )) ? $data ['ethnie'] : null;
 		$this->depistage = (! empty ( $data ['depistage'] )) ? $data ['depistage'] : null;
 			
	}
	
	public function getArrayCopy() {
		return array_values(get_object_vars ( $this ));
	}
	
}