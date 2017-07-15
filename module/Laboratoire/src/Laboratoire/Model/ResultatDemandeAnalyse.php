<?php

namespace Laboratoire\Model;

class ResultatDemandeAnalyse {
	public $iddemande_analyse;
	public $date;
	public $idemploye;
	
	protected $inputFilter;
	
	public function exchangeArray($data) {
		$this->iddemande_analyse = (! empty ( $data ['iddemande_analyse'] )) ? $data ['iddemande_analyse'] : null;
 		$this->date = (! empty ( $data ['date'] )) ? $data ['date'] : null;
 		$this->idemploye = (! empty ( $data ['idemploye'] )) ? $data ['idemploye'] : null;
 		
	}
	
}