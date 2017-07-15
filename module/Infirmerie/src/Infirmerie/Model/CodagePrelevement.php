<?php
namespace Infirmerie\Model;

class CodagePrelevement {
	public $idcodage;
	public $annee;
	public $numero;
	public $prelevement;
	public $code_prelevement;
	public $date_enregistrement;
	public $idbilan;
	public $idemploye;
	
	public function exchangeArray($data) {
		$this->idcodage = (! empty ( $data ['idcodage'] )) ? $data ['idcodage'] : null;
		$this->annee = (! empty ( $data ['annee'] )) ? $data ['annee'] : null;
		$this->numero = (! empty ( $data ['numero'] )) ? $data ['numero'] : null;
		$this->prelevement = (! empty ( $data ['prelevement'] )) ? $data ['prelevement'] : null;
		$this->code_prelevement = (! empty ( $data ['code_prelevement'] )) ? $data ['code_prelevement'] : null;
		$this->date_enregistrement = (! empty ( $data ['date_enregistrement'] )) ? $data ['date_enregistrement'] : null;
		$this->idbilan = (! empty ( $data ['idbilan'] )) ? $data ['idbilan'] : null;
		$this->idemploye = (! empty ( $data ['idemploye'] )) ? $data ['idemploye'] : null;
	}
	
	public function getArrayCopy() {
		return get_object_vars ( $this );
	}
	
}