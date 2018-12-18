<?php
namespace Consultation\Model;


class AntecedentsPersAntenataux{
	public $idpatient;
	public $nbFoetusAP;
	public $deroulementAP;
	public $precisonDeroulementAP;
	public $date_enregistrement;
	public $idmedecin;


	public function exchangeArray($data) {
		$this->idpatient = (! empty ( $data ['idpatient'] )) ? $data ['idpatient'] : null;
		$this->nbFoetusAP = (! empty ( $data ['nbFoetusAP'] )) ? $data ['nbFoetusAP'] : null;
		$this->deroulementAP = (! empty ( $data ['deroulementAP'] )) ? $data ['deroulementAP'] : null;
		$this->precisonDeroulementAP = (! empty ( $data ['precisonDeroulementAP'] )) ? $data ['precisonDeroulementAP'] : null;
		$this->date_enregistrement = (! empty ( $data ['date_enregistrement'] )) ? $data ['date_enregistrement'] : null;
		$this->idmedecin = (! empty ( $data ['idmedecin'] )) ? $data ['idmedecin'] : null;
	}

	public function getArrayCopy() {
		return get_object_vars ( $this );
	}

}