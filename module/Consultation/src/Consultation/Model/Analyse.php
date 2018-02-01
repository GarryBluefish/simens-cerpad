<?php
namespace Consultation\Model;


class Analyse{
	public $idanalyse;
	public $designation;

	public function exchangeArray($data) {
		$this->idanalyse = (! empty ( $data ['idanalyse'] )) ? $data ['idanalyse'] : null;
		$this->designation = (! empty ( $data ['designation'] )) ? $data ['designation'] : null;
	}

	public function getArrayCopy() {
		return get_object_vars ( $this );
	}

}