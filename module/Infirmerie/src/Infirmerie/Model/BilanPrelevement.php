<?php
namespace Infirmerie\Model;

class BilanPrelevement {
	public $idbilan;
	public $nb_tube;
	public $date_heure;
	public $date_prelevement;
	public $a_jeun;
	public $difficultes;
	public $difficultes_prelevement;
	public $transfuser;
	public $moment_transfusion;
	public $origine_prelevement;
	public $diagnostic;
	public $traitement;
	public $date_enregistrement;
	public $date_modification;
	public $idfacturation;
	public $idemploye;
	
	public function exchangeArray($data) {
		$this->idbilan = (! empty ( $data ['idbilan'] )) ? $data ['idbilan'] : null;
		$this->nb_tube = (! empty ( $data ['nb_tube'] )) ? $data ['nb_tube'] : null;
		$this->date_heure = (! empty ( $data ['date_heure'] )) ? $data ['date_heure'] : null;
		$this->date_prelevement = (! empty ( $data ['date_prelevement'] )) ? $data ['date_prelevement'] : null;
		$this->a_jeun = (! empty ( $data ['a_jeun'] )) ? $data ['a_jeun'] : null;
		$this->difficultes = (! empty ( $data ['difficultes'] )) ? $data ['difficultes'] : null;
		$this->difficultes_prelevement = (! empty ( $data ['difficultes_prelevement'] )) ? $data ['difficultes_prelevement'] : null;
		$this->transfuser = (! empty ( $data ['transfuser'] )) ? $data ['transfuser'] : null; 	
		$this->moment_transfusion = (! empty ( $data ['moment_transfusion'] )) ? $data ['moment_transfusion'] : null;
		$this->origine_prelevement = (! empty ( $data ['origine_prelevement'] )) ? $data ['origine_prelevement'] : null;
		$this->diagnostic = (! empty ( $data ['diagnostic'] )) ? $data ['diagnostic'] : null;
		$this->traitement = (! empty ( $data ['traitement'] )) ? $data ['traitement'] : null;
		
		$this->date_enregistrement = (! empty ( $data ['date_enregistrement'] )) ? $data ['date_enregistrement'] : null;
		$this->date_modification = (! empty ( $data ['date_modification'] )) ? $data ['date_modification'] : null;
		$this->idfacturation = (! empty ( $data ['idfacturation'] )) ? $data ['idfacturation'] : null;
		$this->idemploye = (! empty ( $data ['idemploye'] )) ? $data ['idemploye'] : null;

	}
	public function getArrayCopy() {
		return get_object_vars ( $this );
	}
}