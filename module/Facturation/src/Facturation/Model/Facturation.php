<?php
namespace Facturation\Model;

class Facturation {
	public $id_admission;
	public $id_patient;
	public $id_service;
	public $date_cons;
	public $date;
	public $montant;
	public $numero;
	public $montant_avec_majoration;
	public $taux_majoration;
	public $id_type_facturation;
	public $organisme;
	public $idemploye;
	public $date_enregistrement;
	public $date_archivage;
	public $heure_archivage;
	public $cons_archive_applique;
	public $archivage;

	public function exchangeArray($data) {
		$this->id_admission = (! empty ( $data ['id_admission'] )) ? $data ['id_admission'] : null;
		$this->id_patient = (! empty ( $data ['id_patient'] )) ? $data ['id_patient'] : null;
		$this->id_service = (! empty ( $data ['id_service'] )) ? $data ['id_service'] : null;
		$this->date_cons = (! empty ( $data ['date_cons'] )) ? $data ['date_cons'] : null;
		$this->date = (! empty ( $data ['date'] )) ? $data ['date'] : null;
		$this->montant = (! empty ( $data ['montant'] )) ? $data ['montant'] : null;
		$this->numero = (! empty ( $data ['numero'] )) ? $data ['numero'] : null;
		$this->idemploye = (! empty ( $data ['idemploye'] )) ? $data ['idemploye'] : null; 	
		$this->date_enregistrement = (! empty ( $data ['date_enregistrement'] )) ? $data ['date_enregistrement'] : null;
		$this->montant_avec_majoration = (! empty ( $data ['montant_avec_majoration'] )) ? $data ['montant_avec_majoration'] : null;
		$this->id_type_facturation = (! empty ( $data ['id_type_facturation'] )) ? $data ['id_type_facturation'] : null;
		$this->taux_majoration = (! empty ( $data ['taux_majoration'] )) ? $data ['taux_majoration'] : null;
		$this->organisme = (! empty ( $data ['organisme'] )) ? $data ['organisme'] : null;
		$this->date_archivage = (! empty ( $data ['date_archivage'] )) ? $data ['date_archivage'] : null;
		$this->heure_archivage = (! empty ( $data ['heure_archivage'] )) ? $data ['heure_archivage'] : null;
		$this->cons_archive_applique = (! empty ( $data ['cons_archive_applique'] )) ? $data ['cons_archive_applique'] : null;
		$this->archivage = (! empty ( $data ['archivage'] )) ? $data ['archivage'] : null;
		
	}
	public function getArrayCopy() {
		return get_object_vars ( $this );
	}
}