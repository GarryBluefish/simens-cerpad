<?php
namespace Consultation\Model;


class Examen{
	public $idexamen;
	public $designation;

	public function exchangeArray($data) {
		$this->idexamen = (! empty ( $data ['idexamen'] )) ? $data ['idexamen'] : null;
		$this->designation = (! empty ( $data ['designation'] )) ? $data ['designation'] : null;
	}

	public function getArrayCopy() {
		return get_object_vars ( $this );
	}

}