<?php
namespace Consultation\Model;


class Analyse{
	public $idanalyse;
	public $designation;
	public $idtype_analyse;

	public function exchangeArray($data) {
		$this->idanalyse = (! empty ( $data ['idanalyse'] )) ? $data ['idanalyse'] : null;
		$this->designation = (! empty ( $data ['designation'] )) ? $data ['designation'] : null;
		$this->idtype_analyse = (! empty ( $data ['idtype_analyse'] )) ? $data ['idtype_analyse'] : null;
	}

	public function getArrayCopy() {
		return get_object_vars ( $this );
	}

}