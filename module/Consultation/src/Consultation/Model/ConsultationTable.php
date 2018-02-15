<?php
namespace Consultation\Model;

use Zend\Db\TableGateway\TableGateway;
use Infirmerie\View\Helper\DateHelper;
use Zend\Db\Sql\Sql;

class ConsultationTable {

	protected $tableGateway;
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}
	
	/**
	 * Récupérer la liste des patients déjà consultés par le médecin
	 */
	public function getListePatientsConsultes() {
		
		$aColumns = array('numero_dossier', 'Nom', 'Prenom', 'Datenaissance', 'Sexe', 'Adresse', 'id');
		
		$dateDuJour = (new \DateTime ("now"))->format ( 'Y-m-d' );
		/*
		 * SQL queries
		* Liste des patients admis deja consultés aujourd'hui par l'infirmier
		*/
		$sql = new Sql($this->tableGateway->getAdapter());
		$sQuery = $sql->select()
		->from(array('pat' => 'patient'))->columns(array('*'))
		->join(array('pers' => 'personne'), 'pers.idpersonne = pat.idpersonne' , array('Nom'=>'nom','Prenom'=>'prenom','Datenaissance'=>'date_naissance','Sexe'=>'sexe','Adresse'=>'adresse','Nationalite'=>'nationalite_actuelle', 'id'=>'idpersonne', 'id2'=>'idpersonne') )
		->join(array('cons' => 'consultation'), 'cons.idpatient = pers.idpersonne', array('Idcons' => 'idcons', 'Date' => 'date', 'Consprise' => 'consprise') )
		->where(array('cons.date != ?' => $dateDuJour, 'Consprise' => 1) )
		->group(array('idpersonne'))
		->order('idcons ASC');
			
		/* Data set length after filtering */
		$stat = $sql->prepareStatementForSqlObject($sQuery);
		$rResultFt = $stat->execute();
		$iFilteredTotal = count($rResultFt);
		
		$rResult = $rResultFt;
			
		$Control = new DateHelper();
		
		$output = array(
				//"sEcho" => intval($_GET['sEcho']),
				//"iTotalRecords" => $iTotal,
				"iTotalDisplayRecords" => $iFilteredTotal,
				"aaData" => array()
		);
		
		/*
		 * ADRESSE URL RELATIF
		*/
		$baseUrl = $_SERVER['REQUEST_URI'];
		$tabURI  = explode('public', $baseUrl);
			
		/*
		 * Prï¿½parer la liste
		*/
		foreach ( $rResult as $aRow )
		{
			$row = array();
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				if ( $aColumns[$i] != ' ' )
				{
					/* General output */
					if ($aColumns[$i] == 'Nom'){
						$row[] = "<div id='nomMaj'>".$aRow[ $aColumns[$i]]."</div>";
					}
		
					else if ($aColumns[$i] == 'Prenom'){
						$row[] = "<div>".$aRow[ $aColumns[$i]]."</div>";
					}
		
					else if ($aColumns[$i] == 'Datenaissance') {
							
						$date_naissance = $aRow[ $aColumns[$i] ];
						if($date_naissance){ $row[] = $Control->convertDate($aRow[ $aColumns[$i] ]); }else{ $row[] = null;}
		
					}
		
					else if ($aColumns[$i] == 'Adresse') {
						$row[] = "<div class='adresseText' >".$aRow[ $aColumns[$i] ]."</div>";
					}
		
					else if ($aColumns[$i] == 'Date') {
						$row[] = $Control->convertDateTimeHm($aRow[ 'date_enregistrement' ]);
					}
						
					else if ($aColumns[$i] == 'id') {
						$html  ="<infoBulleVue>";
						$html .="<img style='opacity: 0.2; margin-right: 12%;' src='".$tabURI[0]."public/images_icons/doctor_16.png'></infoBulleVue>";
							
						$html .="<infoBulleVue> <a href='".$tabURI[0]."public/consultation/modifier-consultation?idpatient=".$aRow[ 'id' ]."&idcons=".$aRow[ 'Idcons' ]."'>";
						$html .="<img style='display: inline; margin-right: 17%;' src='".$tabURI[0]."public/images_icons/11modif.png' title='Modifier'></a></infoBulleVue>";
		
						$html .="<infoBulleVue>";
						$html .="<img style='display: inline; margin-left: 5%; margin-right: 5%;' src='".$tabURI[0]."public/images_icons/tick_16.png' ></infoBulleVue>";
							
						$row[] = $html;
					}
		
					else {
						$row[] = $aRow[ $aColumns[$i] ];
					}
		
				}
			}
			$output['aaData'][] = $row;
		}
		
		return $output;
		
	}
	
}
