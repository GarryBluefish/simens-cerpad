<?php
namespace Infirmerie\Model;

use Secretariat\View\Helper\DateHelper;

class ListeBilanPrelevement {
	
	public $numero_dossier;
	public $nom;
	public $prenom;
	public $date_naissance;
	public $adresse;
	public $date_enregistrement;
	public $idpersonne;
	public $idfacturation;
	public $idbilan;
	
	public function exchangeArray($data) {
		$this->numero_dossier = (! empty ( $data ['numero_dossier'] )) ? $data ['numero_dossier'] : null;
		$this->nom = (! empty ( $data ['nom'] )) ? "<div id='nomMaj' style='max-width: 130px; overflow: hidden;' >".$data ['nom']."</div>" : null;
		$this->prenom = (! empty ( $data ['prenom'] )) ? "<div style='max-width: 160px; overflow: hidden;' >".$data ['prenom']."</div>" : null;
		$this->date_naissance = (! empty ( $data ['date_naissance'] )) ? (new DateHelper())->convertDate($data ['date_naissance']) : null;
		$this->adresse = (! empty ( $data ['adresse'] )) ? "<div class='adresseText' style='max-width: 230px; overflow: hidden;' >".$data ['adresse']."</div>" : null;
		$this->date_enregistrement = (! empty ( $data ['date_enregistrement'] )) ? (new DateHelper())->convertDateTimeHm($data ['date_enregistrement']) : null;
     
		$modification = "";
		$suppression  = "";
		if ($data['bp.idbilan=0'] == 0){
			$modification = "<a href='javascript:modifierBilan(".$data ['idfacturation'].")' ><img style='margin-right: 15%;' src='../images_icons/pencil_16.png' title='Modifier'></a>";
			$suppression  = "<a id='suppBilan_".$data['idbilan']."' href='javascript:supprimerBilan(".$data['idbilan'].");' ><img src='../images_icons/symbol_supprimer.png' title='Supprimer'></a>";				
		}
		
		$this->idpersonne = (! empty ( $data ['idpersonne'] )) ? "<a href='javascript:bilanPrelevement(".$data ['idfacturation'].")' ><img style='margin-right: 10%;' src='../images_icons/voir2.png' title='d&eacute;tails'></a>".$modification.$suppression : null;
	}
	
	public function getArrayCopy() {
		return array_values(get_object_vars ( $this ));
	}

}