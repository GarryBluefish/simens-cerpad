<?php

namespace Personnel\Model;

use Zend\InputFilter\InputFilterInterface;

class Secretaire {
	
    public $idemploye_secretaire;
	public $matricule_secretaire;
	public $grade_secretaire;
	public $domaine_secretaire;
	public $autres_secretaire;
	public $date_enregistrement;
	public $date_modification;
	public $idpersonne;
	
	protected $inputFilter;
	
	public function exchangeArray($data) {
	    
	    $this->idemploye_secretaire = (! empty ( $data ['idemploye'] )) ? $data ['idemploye'] : null;
 		$this->matricule_secretaire = (! empty ( $data ['matricule'] )) ? $data ['matricule'] : null;
 		$this->grade_secretaire = (! empty ( $data ['grade'] )) ? $data ['grade'] : null;
 		$this->domaine_secretaire = (! empty ( $data ['domaine'] )) ? $data ['domaine'] : null;
 		$this->autres_secretaire = (! empty ( $data ['autres'] )) ? $data ['autres'] : null;
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