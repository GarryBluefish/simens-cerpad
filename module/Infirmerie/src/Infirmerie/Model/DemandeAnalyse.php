<?php
namespace Infirmerie\Model;

class DemandeAnalyse {
	public $iddemande;
	public $idpatient;
	public $mois_naissance;
	public $annee_naissance;
	public $date_naissance;
	
	public function exchangeArray($data) {
		$this->iddemande = (! empty ( $data ['iddemande'] )) ? $data ['iddemande'] : null;
		$this->idpatient = (! empty ( $data ['idpatient'] )) ? $data ['idpatient'] : null;
		$this->mois_naissance = (! empty ( $data ['date_naissance'] )) ? (int)substr($data ['date_naissance'], 5, 2) : null;
		$this->annee_naissance = (! empty ( $data ['date_naissance'] )) ? substr($data ['date_naissance'], 0, 4) : null;
		$this->date_naissance = (! empty ( $data ['date_naissance'] )) ? $data['date_naissance'] : null;
	}
	
	public function getArrayCopy() {
		return get_object_vars ( $this );
	}
	
}