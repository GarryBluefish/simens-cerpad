<?php
namespace Consultation\Model;


class HistoireMaladie{
	public $idcons;
	public $criseHM;
	public $episodeFievreHM;
	public $hospitalisationHM;
	public $date_enregistrement;
	public $idmedecin;

	public function exchangeArray($data) {
		$this->idcons = (! empty ( $data ['idcons'] )) ? $data ['idcons'] : null;
		$this->criseHM = (! empty ( $data ['criseHM'] )) ? $data ['criseHM'] : null;
		$this->episodeFievreHM = (! empty ( $data ['episodeFievreHM'] )) ? $data ['episodeFievreHM'] : null;
		$this->hospitalisationHM = (! empty ( $data ['hospitalisationHM'] )) ? $data ['hospitalisationHM'] : null;
		$this->date_enregistrement = (! empty ( $data ['date_enregistrement'] )) ? $data ['date_enregistrement'] : null;
		$this->idmedecin = (! empty ( $data ['idmedecin'] )) ? $data ['idmedecin'] : null;
	}

	public function getArrayCopy() {
		return get_object_vars ( $this );
	}

}