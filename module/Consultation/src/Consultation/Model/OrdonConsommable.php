<?php
namespace Consultation\Model;

class OrdonConsommable{
	public $id_document;
	public $id_materiel;
	public $posologie;
	public $duree_traitement;
	public $quantite;

	public function exchangeArray($data) {
		$this->id_document = (! empty ( $data ['ID_DOCUMENT'] )) ? $data ['ID_DOCUMENT'] : null;
		$this->id_materiel = (! empty ( $data ['ID_MATERIEL'] )) ? $data ['ID_MATERIEL'] : null;
		$this->posologie = (! empty ( $data ['POSOLOGIE'] )) ? $data ['POSOLOGIE'] : null;
		$this->duree_traitement = (! empty ( $data ['DUREE_TRAITEMENT'] )) ? $data ['DUREE_TRAITEMENT'] : null;
		$this->quantite = (! empty ( $data ['QUANTITE'] )) ? $data ['QUANTITE'] : null;
	}
}