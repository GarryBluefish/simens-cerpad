<?php
namespace Consultation\Model;


class Transfusion{
	public $groupeSanguinTransfusion;
	public $produitSanguin1;
	public $produitSanguin1Quantite;
	public $produitSanguin2;
	public $produitSanguin2Quantite;
	public $reactionTransfusionnel;
	public $reactionTransfusionnelValeur;

	public function exchangeArray($data) {
		$this->groupeSanguinTransfusion = (! empty ( $data ['groupeSanguin'] )) ? $data ['groupeSanguin'] : null;
		$this->produitSanguin1 = (! empty ( $data ['produitSanguin1'] )) ? $data ['produitSanguin1'] : null;
		$this->produitSanguin1Quantite = (! empty ( $data ['produitSanguin1Quantite'] )) ? $data ['produitSanguin1Quantite'] : null;
		$this->produitSanguin2 = (! empty ( $data ['produitSanguin2'] )) ? $data ['produitSanguin2'] : null;
		$this->produitSanguin2Quantite = (! empty ( $data ['produitSanguin2Quantite'] )) ? $data ['produitSanguin2Quantite'] : null;
		$this->reactionTransfusionnel = (! empty ( $data ['reactionTransfusionnel'] )) ? $data ['reactionTransfusionnel'] : null;
		$this->reactionTransfusionnelValeur = (! empty ( $data ['reactionTransfusionnelValeur'] )) ? $data ['reactionTransfusionnelValeur'] : null;
	}

	public function getArrayCopy() {
		return get_object_vars ( $this );
	}

}