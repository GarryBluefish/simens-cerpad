<?php
namespace Consultation\Model;


class DiagnosticConsultation{
	public $idcons;
	public $diagnosticDuJourConsultation;
	public $date_enregistrelent;
	public $idmedecin;

	public function exchangeArray($data) {
		$this->idcons = (! empty ( $data ['idcons'] )) ? $data ['idcons'] : null;
		$this->diagnosticDuJourConsultation = (! empty ( $data ['diagnosticDuJourConsultation'] )) ? $data ['diagnosticDuJourConsultation'] : null;
		$this->date_enregistrelent = (! empty ( $data ['date_enregistrement'] )) ? $data ['date_enregistrement'] : null;
		$this->idmedecin = (! empty ( $data ['idmedecin'] )) ? $data ['idmedecin'] : null;
	}

	public function getArrayCopy() {
		return get_object_vars ( $this );
	}

}