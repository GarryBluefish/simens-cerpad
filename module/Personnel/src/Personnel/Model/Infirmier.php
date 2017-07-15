<?php

namespace Personnel\Model;

use Zend\InputFilter\InputFilterInterface;

class Infirmier {
	
    public $idemploye_infirmier;
	public $matricule_infirmier;
	public $grade_infirmier;
	public $domaine_infirmier;
	public $autres_infirmier;
	public $date_enregistrement;
	public $date_modification;
	public $idpersonne;
	
	protected $inputFilter;
	
	public function exchangeArray($data) {
	    
	    $this->idemploye_infirmier = (! empty ( $data ['idemploye'] )) ? $data ['idemploye'] : null;
 		$this->matricule_infirmier = (! empty ( $data ['matricule'] )) ? $data ['matricule'] : null;
 		$this->grade_infirmier = (! empty ( $data ['grade'] )) ? $data ['grade'] : null;
 		$this->domaine_infirmier = (! empty ( $data ['domaine'] )) ? $data ['domaine'] : null;
 		$this->autres_infirmier = (! empty ( $data ['autres'] )) ? $data ['autres'] : null;
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