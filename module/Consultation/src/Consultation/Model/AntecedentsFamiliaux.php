<?php
namespace Consultation\Model;


class AntecedentsFamiliaux{
	public $idpatient;
	public $consanguiniteAF;
	public $degreAF;
	public $statutDrepanocytoseMereAF;
	public $statutDrepanocytosePereAF;
	public $fratrieTailleAF;
	public $fratrieTailleFilleAF;
	public $fratrieTailleGarconAF;
	public $fratrieRangAF;
	public $nbChoixStatutEnfantAF;

	public function exchangeArray($data) {
		$this->idpatient = (! empty ( $data ['idpatient'] )) ? $data ['idpatient'] : null;
		$this->consanguiniteAF = (! empty ( $data ['consanguiniteAF'] )) ? $data ['consanguiniteAF'] : null;
		$this->degreAF = (! empty ( $data ['degreAF'] )) ? $data ['degreAF'] : null;
		$this->statutDrepanocytoseMereAF = (! empty ( $data ['statutDrepanocytoseMereAF'] )) ? $data ['statutDrepanocytoseMereAF'] : null;
		$this->statutDrepanocytosePereAF = (! empty ( $data ['statutDrepanocytosePereAF'] )) ? $data ['statutDrepanocytosePereAF'] : null;
		$this->fratrieTailleAF = (! empty ( $data ['fratrieTailleAF'] )) ? $data ['fratrieTailleAF'] : null;
		$this->fratrieTailleFilleAF = (! empty ( $data ['fratrieTailleFilleAF'] )) ? $data ['fratrieTailleFilleAF'] : null;
		$this->fratrieTailleGarconAF = (! empty ( $data ['fratrieTailleGarconAF'] )) ? $data ['fratrieTailleGarconAF'] : null;
		$this->fratrieRangAF = (! empty ( $data ['fratrieRangAF'] )) ? $data ['fratrieRangAF'] : null;
		$this->nbChoixStatutEnfantAF = (! empty ( $data ['nbChoixStatutEnfantAF'] )) ? $data ['nbChoixStatutEnfantAF'] : null;
	}

	public function getArrayCopy() {
		return get_object_vars ( $this );
	}

}