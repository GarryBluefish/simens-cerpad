<?php

namespace Secretariat\Model;

class Analyse {
	public $idpersonne;
	public $date_enregistrement;
	public $date_modification;
	public $idemploye;
	
	protected $inputFilter;
	
	public function exchangeArray($data) {
		$this->idpersonne = (! empty ( $data ['idpersonne'] )) ? $data ['idpersonne'] : null;
 		$this->date_enregistrement = (! empty ( $data ['date_enregistrement'] )) ? $data ['date_enregistrement'] : null;
 		$this->date_modification = (! empty ( $data ['date_modification'] )) ? $data ['date_modification'] : null;
 		$this->idemploye = (! empty ( $data ['idemploye'] )) ? $data ['idemploye'] : null;
 		
	}
	
}