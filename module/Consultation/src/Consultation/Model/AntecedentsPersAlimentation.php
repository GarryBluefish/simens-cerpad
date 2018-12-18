<?php
namespace Consultation\Model;


class AntecedentsPersAlimentation{
	public $idpatient;
	public $allaitementMatenelExclusifAP;
	public $typeAllaitementAP;
	public $nomAllaitementArtificielAP;
	public $diversificationAlimentaireAP;
	public $ageDebutDiversificationAlimentaireAP;
	public $typesAlimentsAP;
	public $sevrageAP;
	public $ageDebutSevrageAP;
	
	public $date_modification;
	public $date_enregistrement;
	public $idmedecin;


	public function exchangeArray($data) {
		$this->idpatient = (! empty ( $data ['idpatient'] )) ? $data ['idpatient'] : null;
		$this->allaitementMatenelExclusifAP = (! empty ( $data ['allaitementMatenelExclusifAP'] )) ? $data ['allaitementMatenelExclusifAP'] : null;
		$this->typeAllaitementAP = (! empty ( $data ['typeAllaitementAP'] )) ? $data ['typeAllaitementAP'] : null;
		$this->nomAllaitementArtificielAP = (! empty ( $data ['nomAllaitementArtificielAP'] )) ? $data ['nomAllaitementArtificielAP'] : null;
		$this->diversificationAlimentaireAP = (! empty ( $data ['diversificationAlimentaireAP'] )) ? $data ['diversificationAlimentaireAP'] : null;
		$this->ageDebutDiversificationAlimentaireAP = (! empty ( $data ['ageDebutDiversificationAlimentaireAP'] )) ? $data ['ageDebutDiversificationAlimentaireAP'] : null;
		$this->typesAlimentsAP = (! empty ( $data ['typesAlimentsAP'] )) ? $data ['typesAlimentsAP'] : null;
		$this->sevrageAP = (! empty ( $data ['sevrageAP'] )) ? $data ['sevrageAP'] : null;
		$this->ageDebutSevrageAP = (! empty ( $data ['ageDebutSevrageAP'] )) ? $data ['ageDebutSevrageAP'] : null;
		
		$this->date_modification = (! empty ( $data ['date_modification'] )) ? $data ['date_modification'] : null;
		$this->date_enregistrement = (! empty ( $data ['date_enregistrement'] )) ? $data ['date_enregistrement'] : null;
		$this->idmedecin = (! empty ( $data ['idmedecin'] )) ? $data ['idmedecin'] : null;
	}

	public function getArrayCopy() {
		return get_object_vars ( $this );
	}

}