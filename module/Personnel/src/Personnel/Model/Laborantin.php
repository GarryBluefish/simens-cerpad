<?php

namespace Personnel\Model;

use Zend\InputFilter\InputFilterInterface;

class Laborantin {
	
    public $idemploye_laborantin;
	public $matricule_laborantin;
	public $grade_laborantin;
	public $domaine_laborantin;
	public $autres_laborantin;
	public $date_enregistrement;
	public $date_modification;
	public $idpersonne;
	
	protected $inputFilter;
	
	public function exchangeArray($data) {
	    
	    $this->idemploye_laborantin = (! empty ( $data ['idemploye'] )) ? $data ['idemploye'] : null;
 		$this->matricule_laborantin = (! empty ( $data ['matricule'] )) ? $data ['matricule'] : null;
 		$this->grade_laborantin = (! empty ( $data ['grade'] )) ? $data ['grade'] : null;
 		$this->domaine_laborantin = (! empty ( $data ['domaine'] )) ? $data ['domaine'] : null;
 		$this->autres_laborantin = (! empty ( $data ['autres'] )) ? $data ['autres'] : null;
 		$this->date_enregistrement = (! empty ( $data ['date_enregistrement'] )) ? $data ['date_enregistrement'] : null;
 		$this->date_modification = (! empty ( $data ['date_modification'] )) ? $data ['date_modification'] : null;
 		$this->idpersonne = (! empty ( $data ['idpersonne'] )) ? $data ['idpersonne'] : null;
 			
	}
	
	public function getArrayCopy() {
		return get_object_vars ( $this );
	}
	
	public function setInputFilter(InputFilterInterface $inputFilter) {
		throw new \Exception ( "Not used" );
	}	

}