<?php
namespace Consultation\Model;


class Consultation{
	public $idcons;
	public $idmedecin;
	public $idinfirmier;
	public $idpatient;
	public $date;
	public $heure;
	public $poids;
	public $taille;
	public $temperature;
	public $perimetre_cranien;
	public $consprise;
	public $idfacturation;
	public $date_enreg_infirm;
	public $date_enreg_medecin;
	public $date_modification;
	public $idemploye; 

	public function exchangeArray($data) {
		$this->idcons = (! empty ( $data ['idcons'] )) ? $data ['idcons'] : null;
		$this->idmedecin = (! empty ( $data ['idmedecin'] )) ? $data ['idmedecin'] : null;
		
		$this->idinfirmier = (! empty ( $data ['idinfirmier'] )) ? $data ['idinfirmier'] : null;
		$this->idpatient = (! empty ( $data ['idpatient'] )) ? $data ['idpatient'] : null;
		$this->date = (! empty ( $data ['date'] )) ? $data ['date'] : null;
		$this->heure = (! empty ( $data ['heure'] )) ? $data ['heure'] : null;
		$this->poids = (! empty ( $data ['poids'] )) ? $data ['poids'] : null;
		$this->taille = (! empty ( $data ['taille'] )) ? $data ['taille'] : null;
		$this->temperature = (! empty ( $data ['temperature'] )) ? $data ['temperature'] : null;
		$this->perimetre_cranien = (! empty ( $data ['perimetre_cranien'] )) ? $data ['perimetre_cranien'] : null;
		$this->consprise = (! empty ( $data ['consprise'] )) ? $data ['consprise'] : null;
		$this->idfacturation = (! empty ( $data ['idfacturation'] )) ? $data ['idfacturation'] : null;
		$this->date_enreg_infirm = (! empty ( $data ['date_enreg_infirm'] )) ? $data ['date_enreg_infirm'] : null;
		$this->date_enreg_medecin = (! empty ( $data ['date_enreg_medecin'] )) ? $data ['date_enreg_medecin'] : null;
		$this->date_modification = (! empty ( $data ['date_modification'] )) ? $data ['date_modification'] : null;
		$this->idemploye = (! empty ( $data ['idemploye'] )) ? $data ['idemploye'] : null;
		
	}

	public function getArrayCopy() {
		return get_object_vars ( $this );
	}

}