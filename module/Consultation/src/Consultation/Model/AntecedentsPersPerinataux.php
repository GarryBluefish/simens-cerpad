<?php
namespace Consultation\Model;


class AntecedentsPersPerinataux{
	public $idpatient;
	public $voieBasseAP;
	public $cesarienneAP;
	public $motifCesarienneAP;
	public $manoeuvreObstetricaleAP;
	public $motifManoeuvreObstetricaleAP;
	public $ageGestationnelATermeAP;
	public $precisonAgeGestationnelATermeAP;
	public $precisonValeurAgeGestationnelAP;
	public $souffranceFoetaleAigueAP;
	public $precisonReanimationSouffranceFoetaleAP;
	public $pathologieNeonataleAP;
	
	public $date_modification;
	public $date_enregistrement;
	public $idmedecin;


	public function exchangeArray($data) {
		$this->idpatient = (! empty ( $data ['idpatient'] )) ? $data ['idpatient'] : null;
		$this->voieBasseAP = (! empty ( $data ['voieBasseAP'] )) ? $data ['voieBasseAP'] : null;
		$this->cesarienneAP = (! empty ( $data ['cesarienneAP'] )) ? $data ['cesarienneAP'] : null;
		$this->motifCesarienneAP = (! empty ( $data ['motifCesarienneAP'] )) ? $data ['motifCesarienneAP'] : null;
		$this->manoeuvreObstetricaleAP = (! empty ( $data ['manoeuvreObstetricaleAP'] )) ? $data ['manoeuvreObstetricaleAP'] : null;
		$this->motifManoeuvreObstetricaleAP = (! empty ( $data ['motifManoeuvreObstetricaleAP'] )) ? $data ['motifManoeuvreObstetricaleAP'] : null;
		$this->ageGestationnelATermeAP = (! empty ( $data ['ageGestationnelATermeAP'] )) ? $data ['ageGestationnelATermeAP'] : null;
		$this->precisonAgeGestationnelATermeAP = (! empty ( $data ['precisonAgeGestationnelATermeAP'] )) ? $data ['precisonAgeGestationnelATermeAP'] : null;
		$this->precisonValeurAgeGestationnelAP = (! empty ( $data ['precisonValeurAgeGestationnelAP'] )) ? $data ['precisonValeurAgeGestationnelAP'] : null;
		$this->souffranceFoetaleAigueAP = (! empty ( $data ['souffranceFoetaleAigueAP'] )) ? $data ['souffranceFoetaleAigueAP'] : null;
		$this->precisonReanimationSouffranceFoetaleAP = (! empty ( $data ['precisonReanimationSouffranceFoetaleAP'] )) ? $data ['precisonReanimationSouffranceFoetaleAP'] : null;
		$this->pathologieNeonataleAP = (! empty ( $data ['pathologieNeonataleAP'] )) ? $data ['pathologieNeonataleAP'] : null;
		
		$this->date_modification = (! empty ( $data ['date_modification'] )) ? $data ['date_modification'] : null;
		$this->date_enregistrement = (! empty ( $data ['date_enregistrement'] )) ? $data ['date_enregistrement'] : null;
		$this->idmedecin = (! empty ( $data ['idmedecin'] )) ? $data ['idmedecin'] : null;
	}

	public function getArrayCopy() {
		return get_object_vars ( $this );
	}

}