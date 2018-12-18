<?php
namespace Consultation\Model;


class AntecedentsPersScolarite{
	public $idpatient;
	public $scolariseAP;
	public $niveauScolariteAP;
	public $redoublementAP;
	public $nombreRedoublementAP;

	public $date_modification;
	public $date_enregistrement;
	public $idmedecin;


	public function exchangeArray($data) {
		$this->idpatient = (! empty ( $data ['idpatient'] )) ? $data ['idpatient'] : null;
		$this->scolariseAP = (! empty ( $data ['scolariseAP'] )) ? $data ['scolariseAP'] : null;
		$this->niveauScolariteAP = (! empty ( $data ['niveauScolariteAP'] )) ? $data ['niveauScolariteAP'] : null;
		$this->redoublementAP = (! empty ( $data ['redoublementAP'] )) ? $data ['redoublementAP'] : null;
		$this->nombreRedoublementAP = (! empty ( $data ['nombreRedoublementAP'] )) ? $data ['nombreRedoublementAP'] : null;
		
		$this->date_modification = (! empty ( $data ['date_modification'] )) ? $data ['date_modification'] : null;
		$this->date_enregistrement = (! empty ( $data ['date_enregistrement'] )) ? $data ['date_enregistrement'] : null;
		$this->idmedecin = (! empty ( $data ['idmedecin'] )) ? $data ['idmedecin'] : null;
	}

	public function getArrayCopy() {
		return get_object_vars ( $this );
	}

}