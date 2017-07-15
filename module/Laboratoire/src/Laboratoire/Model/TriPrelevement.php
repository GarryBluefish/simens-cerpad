<?php
namespace Laboratoire\Model;

class TriPrelevement {
	public $idtri;
	public $idanalyse;
	public $idbilan;
	public $conformite;
	public $note_non_conformite;
	public $date_enregistrement;
	public $date_modification;
	public $idemploye;
	
	public function exchangeArray($data) {
		$this->idtri = (! empty ( $data ['idtri'] )) ? $data ['idtri'] : null;
		$this->idanalyse = (! empty ( $data ['idanalyse'] )) ? $data ['idanalyse'] : null;
		$this->idbilan = (! empty ( $data ['idbilan'] )) ? $data ['idbilan'] : null;
		$this->conformite = (! empty ( $data ['conformite'] )) ? $data ['conformite'] : null;
		$this->note_non_conformite = (! empty ( $data ['note_non_conformite'] )) ? $data ['note_non_conformite'] : null;
		$this->date_enregistrement = (! empty ( $data ['date_enregistrement'] )) ? $data ['date_enregistrement'] : null;
		$this->date_modification = (! empty ( $data ['date_modification'] )) ? $data ['date_modification'] : null;
		$this->idemploye = (! empty ( $data ['idemploye'] )) ? $data ['idemploye'] : null;

	}
	public function getArrayCopy() {
		return get_object_vars ( $this );
	}
}