<?php
namespace Infirmerie\Model;

class MotifAdmission{
	public $idmotif;
	public $idcons;
	public $libelle_motif;

	public function exchangeArray($data) {
		$this->idmotif = (! empty ( $data ['idmotif'] )) ? $data ['idmotif'] : null;
		$this->idcons = (! empty ( $data ['idcons'] )) ? $data ['idcons'] : null;
		$this->libelle_motif = (! empty ( $data ['libelle_motif'] )) ? $data ['libelle_motif'] : null;
	}
}