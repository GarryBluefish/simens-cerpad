<?php
namespace Consultation\Model;

class Ordonnance{
	public $id_document;
	public $id_cons;
	public $duree_traitement;
	public $date_prescription;

	public function exchangeArray($data) {
		$this->id_document = (! empty ( $data ['ID_DOCUMENT'] )) ? $data ['ID_DOCUMENT'] : null;
		$this->id_cons = (! empty ( $data ['ID_CONS'] )) ? $data ['ID_CONS'] : null;
		$this->duree_traitement = (! empty ( $data ['DUREE_TRAITEMENT'] )) ? $data ['DUREE_TRAITEMENT'] : null;
		$this->date_prescription = (! empty ( $data ['DATE_PRESCRIPTION'] )) ? $data ['DATE_PRESCRIPTION'] : null;
	}
}