<?php
namespace Consultation\Model;


class DonneesExamen{
	public $idcons;
	public $paleurDonneesExamen;
	public $ictereDonneesExamen;
	public $splenomegalieDonneesExamen;
	public $tailleDonneesExamen;
	public $orlObstructionNasaleDonneesExamen;
	public $orlRhiniteDonneesExamen;
	public $orlHypertrophieAmygdalesDonneesExamen;
	public $orlAngineDonneesExamen;
	public $orlOtiteDonneesExamen;
	public $examenDesPoumonsDonneesExamen;
	public $precisionExamenDesPoumonsDonneesExamen;
	public $examenDuCoeurDonneesExamen;
	public $precisionExamenDuCoeurDonneesExamen;
	public $examenDuFoieVoieBiliaireDonneesExamen;
	public $examenHancheDonneesExamen;
	public $examenEpauleDonneesExamen;
	public $examenJambeDonneesExamen;
	public $autresDonneesExamen;
	public $date_enregistrelent;
	public $idmedecin;

	public function exchangeArray($data) {
		$this->idcons = (! empty ( $data ['idcons'] )) ? $data ['idcons'] : null;
		$this->paleurDonneesExamen = (! empty ( $data ['paleurDonneesExamen'] )) ? $data ['paleurDonneesExamen'] : null;
		$this->ictereDonneesExamen = (! empty ( $data ['ictereDonneesExamen'] )) ? $data ['ictereDonneesExamen'] : null;
		$this->splenomegalieDonneesExamen = (! empty ( $data ['splenomegalieDonneesExamen'] )) ? $data ['splenomegalieDonneesExamen'] : null;
		$this->tailleDonneesExamen = (! empty ( $data ['tailleDonneesExamen'] )) ? $data ['tailleDonneesExamen'] : null;
		$this->orlObstructionNasaleDonneesExamen = (! empty ( $data ['orlObstructionNasaleDonneesExamen'] )) ? $data ['orlObstructionNasaleDonneesExamen'] : null;
		$this->orlRhiniteDonneesExamen = (! empty ( $data ['orlRhiniteDonneesExamen'] )) ? $data ['orlRhiniteDonneesExamen'] : null;
		$this->orlHypertrophieAmygdalesDonneesExamen = (! empty ( $data ['orlHypertrophieAmygdalesDonneesExamen'] )) ? $data ['orlHypertrophieAmygdalesDonneesExamen'] : null;
		$this->orlAngineDonneesExamen = (! empty ( $data ['orlAngineDonneesExamen'] )) ? $data ['orlAngineDonneesExamen'] : null;
		$this->orlOtiteDonneesExamen = (! empty ( $data ['orlOtiteDonneesExamen'] )) ? $data ['orlOtiteDonneesExamen'] : null;
		$this->examenDesPoumonsDonneesExamen = (! empty ( $data ['examenDesPoumonsDonneesExamen'] )) ? $data ['examenDesPoumonsDonneesExamen'] : null;
		$this->precisionExamenDesPoumonsDonneesExamen = (! empty ( $data ['precisionExamenDesPoumonsDonneesExamen'] )) ? $data ['precisionExamenDesPoumonsDonneesExamen'] : null;
		$this->examenDuCoeurDonneesExamen = (! empty ( $data ['examenDuCoeurDonneesExamen'] )) ? $data ['examenDuCoeurDonneesExamen'] : null;
		$this->precisionExamenDuCoeurDonneesExamen = (! empty ( $data ['precisionExamenDuCoeurDonneesExamen'] )) ? $data ['precisionExamenDuCoeurDonneesExamen'] : null;
		$this->examenDuFoieVoieBiliaireDonneesExamen = (! empty ( $data ['examenDuFoieVoieBiliaireDonneesExamen'] )) ? $data ['examenDuFoieVoieBiliaireDonneesExamen'] : null;
		$this->examenHancheDonneesExamen = (! empty ( $data ['examenHancheDonneesExamen'] )) ? $data ['examenHancheDonneesExamen'] : null;
		$this->examenEpauleDonneesExamen = (! empty ( $data ['examenEpauleDonneesExamen'] )) ? $data ['examenEpauleDonneesExamen'] : null;
		$this->examenJambeDonneesExamen = (! empty ( $data ['examenJambeDonneesExamen'] )) ? $data ['examenJambeDonneesExamen'] : null;
		$this->autresDonneesExamen = (! empty ( $data ['autresDonneesExamen'] )) ? $data ['autresDonneesExamen'] : null;
		$this->date_enregistrelent = (! empty ( $data ['date_enregistrement'] )) ? $data ['date_enregistrement'] : null;
		$this->idmedecin = (! empty ( $data ['idmedecin'] )) ? $data ['idmedecin'] : null;
	}

	public function getArrayCopy() {
		return get_object_vars ( $this );
	}

}