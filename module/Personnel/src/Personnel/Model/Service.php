<?php
namespace Personnel\Model;

class Service {
	public $idservice;
	Public $libelle;
	public $date_enregistrement;
	public $date_modification;
	public $idemploye;

	public function exchangeArray($data) {
		$this->idservice = (! empty ( $data ['idservice'] )) ? $data ['idservice'] : null;
		$this->libelle = (! empty ( $data ['libelle'] )) ? $data ['libelle'] : null;
		$this->date_enregistrement = (! empty ( $data ['date_enregistrement'] )) ? $data ['date_enregistrement'] : null;
		$this->date_modification = (! empty ( $data ['date_modification'] )) ? $data ['date_modification'] : null;
		$this->idemploye = (! empty ( $data ['idemploye'] )) ? $data ['idemploye'] : null;
	}

	public function getArrayCopy()
	{
		return get_object_vars($this);
	}
}