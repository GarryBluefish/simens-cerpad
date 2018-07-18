<?php
namespace Laboratoire\Model;

use Laboratoire\View\Helper\DateHelper;
class ResultatsDepistages {
	public $iddemande;
	public $idpatient;
	public $numero_dossier;
	public $mois_prelevement;
	public $annee_prelevement;
	public $date_prelevement;
	public $profil;
	
	public function exchangeArray($data) {
		$this->iddemande = (! empty ( $data ['iddemande'] )) ? $data ['iddemande'] : null;
		$this->idpatient = (! empty ( $data ['idpatient'] )) ? $data ['idpatient'] : null;
		$this->numero_dossier = (! empty ( $data ['numero_dossier'] )) ? $data ['numero_dossier'] : null;
		$this->mois_prelevement = (! empty ( $data ['date_heure'] )) ? (int)substr($data ['date_heure'], 3, 2) : null;
		$this->annee_prelevement = (! empty ( $data ['date_heure'] )) ? substr($data ['date_heure'], 6, 4) : null;
		$this->date_prelevement = (! empty ( $data ['date_heure'] )) ?  (new DateHelper())->convertDateInAnglais( substr($data ['date_heure'], 0, 10) ) : null;
		$this->profil = (! empty ( $data ['designation'] )) ? $data['designation'] : 'NON';
	}
	
	public function getArrayCopy() {
		return get_object_vars ( $this );
	}
	
}