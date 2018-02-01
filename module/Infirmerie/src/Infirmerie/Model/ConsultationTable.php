<?php
namespace Infirmerie\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;
use Zend\Db\Sql\Predicate\NotIn;
use Infirmerie\View\Helper\DateHelper;
use Zend\Db\Sql\Predicate\In;

class ConsultationTable {

	protected $tableGateway;
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}
	public function getConsultation($idcons){
		$rowset = $this->tableGateway->select ( array (
				'idcons' => $idcons
		) );
		$row =  $rowset->current ();
 		if (! $row) {
 			throw new \Exception ( "Could not find row $idcons" );
 		}
		return $row;
	}
	
	
	public function addConsultation($values, $idemploye){
		$this->tableGateway->getAdapter()->getDriver()->getConnection()->beginTransaction();
		
		$date = (new \DateTime())->format('Y-m-d');
		$heure = (new \DateTime())->format('H:i:s');
		try {
	
			$dataconsultation = array(
					'idcons'=> $values->get ( "idcons" )->getValue (),
					'idinfirmier'=> $values->get ( "idinfirmier" )->getValue (),
					'idpatient'=> $values->get ( "idpatient" )->getValue (),
					'date'=> $date,
					'heure' => $heure,
					'poids' => $values->get ( "poids" )->getValue (),
					'taille' => $values->get ( "taille" )->getValue (),
					'temperature' => $values->get ( "temperature" )->getValue (),
					'perimetre_cranien' => $values->get ( "perimetre_cranien" )->getValue (),
					'idfacturation' => $values->get ( "idfacturation" )->getValue (),
					'date_enreg_infirm' => $date.' '.$heure,
					'idemploye' => $idemploye
			);
				
			$this->tableGateway->insert($dataconsultation);
	
			$this->tableGateway->getAdapter()->getDriver()->getConnection()->commit();
		} catch (\Exception $e) {
			$this->tableGateway->getAdapter()->getDriver()->getConnection()->rollback();
		}
	}
	
	public function updateConsultations($values, $idemploye){
		$this->tableGateway->getAdapter()->getDriver()->getConnection()->beginTransaction();
	
		try {
	
			$idcons = $values->get ( "idcons" )->getValue ();
			
			$dataconsultation = array(
					
					'poids' => $values->get ( "poids" )->getValue (),
					'taille' => $values->get ( "taille" )->getValue (),
					'temperature' => $values->get ( "temperature" )->getValue (),
					'perimetre_cranien' => $values->get ( "perimetre_cranien" )->getValue (),
					
					'idemploye' => $idemploye
			);
	
			$this->tableGateway->update($dataconsultation, array('idcons' => $idcons));
	
			$this->tableGateway->getAdapter()->getDriver()->getConnection()->commit();
		} catch (\Exception $e) {
			$this->tableGateway->getAdapter()->getDriver()->getConnection()->rollback();
		}
	}
	
	
	
	
	//********** RECUPERER LA LISTE DES CONSULTATIONS DU MEDECIN *********
	//********** RECUPERER LA LISTE DES CONSULTATIONS DU MEDECIN *********
	public function getListeConsultations(){
	
		$db = $this->tableGateway->getAdapter();
			
		$aColumns = array('numero_dossier', 'Nom', 'Prenom', 'Datenaissance', 'Sexe', 'Adresse', 'id');
			
		/* Indexed column (used for fast and accurate table cardinality) */
		$sIndexColumn = "id";
			
		/*
		 * Paging
		*/
		$sLimit = array();
		if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
		{
			$sLimit[0] = $_GET['iDisplayLength'];
			$sLimit[1] = $_GET['iDisplayStart'];
		}
			
		/*
		 * Ordering
		*/
		if ( isset( $_GET['iSortCol_0'] ) )
		{
			$sOrder = array();
			$j = 0;
			for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
			{
				if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
				{
					$sOrder[$j++] = $aColumns[ intval( $_GET['iSortCol_'.$i] ) ]."
								 	".$_GET['sSortDir_'.$i];
				}
			}
		}
			
		
		$dateDuJour = (new \DateTime ("now"))->format ( 'Y-m-d' );
		
		/*
		 * SQL queries
		* Liste des patients admis pour lesquels la consultation n'est pas encore faite par le medecin
		*/
		$sql = new Sql($db);
		$sQuery = $sql->select()
        ->from(array('pat' => 'patient'))->columns(array('*'))
		->join(array('pers' => 'personne'), 'pers.idpersonne = pat.idpersonne' , array('Nom'=>'nom','Prenom'=>'prenom','Datenaissance'=>'date_naissance','Sexe'=>'sexe','Adresse'=>'adresse','Nationalite'=>'nationalite_actuelle', 'id'=>'idpersonne', 'id2'=>'idpersonne') )
		->join(array('cons' => 'consultation'), 'cons.idpatient = pat.idpersonne', array('Idcons' => 'idcons', 'Date' => 'date', 'Consprise' => 'consprise') )
		->where(array('cons.date' => $dateDuJour, 'Consprise' => 0) )
		->order('idcons ASC');
		
		/* Data set length after filtering */
		$stat = $sql->prepareStatementForSqlObject($sQuery);
		$rResultFt = $stat->execute();
		$iFilteredTotal = count($rResultFt);
			
		$rResult = $rResultFt;
		
		
		$output = array(
				//"sEcho" => intval($_GET['sEcho']),
				//"iTotalRecords" => $iTotal,
				"iTotalDisplayRecords" => $iFilteredTotal,
				"aaData" => array()
		);
		
		
		/*
		 * $Control pour convertir la date en franï¿½ais
		*/
		$Control = new DateHelper();
			
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
		
					else if ($aColumns[$i] == 'id') {
						$html  ="<infoBulleVue> <a href='".$tabURI[0]."public/consultation/consulter?idpatient=".$aRow[ 'id' ]."&idcons=".$aRow[ 'Idcons' ]."'>";
						$html .="<img style='display: inline; margin-right: 17%;' src='".$tabURI[0]."public/images_icons/doctor_16.png' title='Consulter'></a></infoBulleVue>";
							
						$html .="<infoBulleVue>";
						$html .="<img style='opacity:0.2; margin-right: 17%;' src='".$tabURI[0]."public/images_icons/11modif.png' ></a></infoBulleVue>";
						
						$row[] = $html;
					}
		
					else {
						$row[] = $aRow[ $aColumns[$i] ];
					}
		
				}
 			}
 			$output['aaData'][] = $row;
 		}
		
		
		
		
		/*
		 * SQL queries
		* Liste des patients admis deja consultés aujourd'hui par l'infirmier
		*/
		$sql = new Sql($db);
		$sQuery = $sql->select()
		->from(array('pat' => 'patient'))->columns(array('*'))
		->join(array('pers' => 'personne'), 'pers.idpersonne = pat.idpersonne' , array('Nom'=>'nom','Prenom'=>'prenom','Datenaissance'=>'date_naissance','Sexe'=>'sexe','Adresse'=>'adresse','Nationalite'=>'nationalite_actuelle', 'id'=>'idpersonne', 'id2'=>'idpersonne') )
		->join(array('cons' => 'consultation'), 'cons.idpatient = pat.idpersonne', array('Idcons' => 'idcons', 'Date' => 'date', 'Consprise' => 'consprise') )
		->where(array('cons.date' => $dateDuJour, 'Consprise' => 1) )
		->order('idcons ASC');
			
		/* Data set length after filtering */
		$stat = $sql->prepareStatementForSqlObject($sQuery);
		$rResultFt = $stat->execute();
		$iFilteredTotal = count($rResultFt);
			
		$rResult = $rResultFt;
			
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
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	public function getConsultationPatient($id_pat, $id_cons){
		$adapter = $this->tableGateway->getAdapter ();
		$sql = new Sql ( $adapter );
		$select = $sql->select ();
		$select->columns( array( '*' ));
		$select->from( array( 'c' => 'consultation' ));
		$select->join( array('e1' => 'employe'), 'e1.id_personne = c.ID_MEDECIN' , array());
		$select->join( array('e2' => 'employe'), 'e2.id_personne = c.ID_SURVEILLANT' , array());
		$select->join( array('p1' => 'personne'), 'e1.id_personne = p1.ID_PERSONNE' , array('*'));
		$select->join( array('p2' => 'personne'), 'e2.id_personne = p2.ID_PERSONNE' , array('NomSurveillant' => 'NOM', 'PrenomSurveillant' => 'PRENOM'));
		$select->join( array('s' => 'service'), 's.ID_SERVICE = c.ID_SERVICE' , array('nomService' => 'NOM', 'domaineService' => 'DOMAINE'));

		//On affiche toutes les consultations sauf celle ouverte
		$where = new Where();
		$where->equalTo('c.ID_PATIENT', $id_pat);
		$where->notEqualTo('c.idcons', $id_cons);
		$select->where($where);
		$select->order('DATEONLY DESC');
		
		$stat = $sql->prepareStatementForSqlObject ( $select );
		$result = $stat->execute ();
		
		return $result;
	}
	
	public function getConsultationDuJour(){
		$today = (new \DateTime())->format('Y-m-d');
		$adapter = $this->tableGateway->getAdapter ();
		$sql = new Sql ( $adapter );
		$select = $sql->select ();
		$select->from( array( 'c' => 'consultation' ));
		$select->where(array('DATEONLY' => $today));
		return $sql->prepareStatementForSqlObject ( $select )->execute()->current();
	}
	/** --------------=============================------------------------------ */
	public function getConsultationPatientSaufActu($id_pat, $id_cons){
		$adapter = $this->tableGateway->getAdapter ();
		$sql = new Sql ( $adapter );
		$select = $sql->select ();
		$select->columns( array( '*' ));
		$select->from( array( 'c' => 'consultation' ));
		$select->join( array('e1' => 'employe'), 'e1.id_personne = c.ID_MEDECIN' , array('*'));
		$select->join( array('p1' => 'personne'), 'e1.id_personne = p1.ID_PERSONNE' , array('*'));
		$select->join( array('s' => 'service'), 's.ID_SERVICE = c.ID_SERVICE' , array('nomService' => 'NOM', 'domaineService' => 'DOMAINE'));
	
		//La consultation du jour -- pour éviter d'afficher la consultation du jour
		$id_cons_du_jour = $this->getConsultationDuJour()['idcons'];
		
		//On affiche toutes les consultations sauf celle ouverte
		$where = new Where();
		$where->equalTo('c.ID_PATIENT', $id_pat);
		$where->notEqualTo('c.idcons', $id_cons);
		$where->notEqualTo('c.idcons', $id_cons_du_jour);
		$select->where($where);
		$select->order('DATEONLY DESC');
	
		$stat = $sql->prepareStatementForSqlObject ( $select );
		$result = $stat->execute ();
	
		return $result;
	}
	
	public function getInfosSurveillant($id_personne){
		$adapter = $this->tableGateway->getAdapter ();
		$sql = new Sql ( $adapter );
		$select = $sql->select ();
		$select->columns( array( '*' ));
		$select->from( array('e1' => 'employe'));
		$select->join( array('p1' => 'personne'), 'e1.id_personne = p1.ID_PERSONNE' , array('*'));
	
		$where = new Where();
		$where->equalTo('e1.id_personne', $id_personne);
		$select->where($where);
	
		$stat = $sql->prepareStatementForSqlObject ( $select );
		$result = $stat->execute ();
	
		return $result->current();
	}
	/** --------------=============================-------------------------------*/
	
	public function updateConsultation($values)
	{
		
		$donnees = array(
				'POIDS' => $values->get ( "poids" )->getValue (), 
				'TAILLE' => $values->get ( "taille" )->getValue (), 
				'TEMPERATURE' => $values->get ( "temperature" )->getValue (), 
				'PRESSION_ARTERIELLE' => $values->get ( "tensionmaximale" )->getValue ().'/'.$values->get ( "tensionminimale" )->getValue (),
				'POULS' => $values->get ( "pouls" )->getValue (), 
				'FREQUENCE_RESPIRATOIRE' => $values->get ( "frequence_respiratoire" )->getValue (), 
				'GLYCEMIE_CAPILLAIRE' => $values->get ( "glycemie_capillaire" )->getValue (), 
		);
		$this->tableGateway->update( $donnees, array('idcons'=> $values->get ( "id_cons" )->getValue ()) );
	}
	
	public function validerConsultation($values){
		$donnees = array(
				'CONSPRISE' => $values['VALIDER'],
				'ID_MEDECIN' => $values['ID_MEDECIN']
		);
		$this->tableGateway->update($donnees, array('idcons'=> $values['idcons']));
	}
	
	public function addConsultationEffective($id_cons){
		$db = $this->tableGateway->getAdapter();
		$sql = new Sql($db);
		$sQuery = $sql->insert()
		->into('consultation_effective')
		->values(array('idcons' => $id_cons));
		$requete = $sql->prepareStatementForSqlObject($sQuery);
		$requete->execute();
	}
	
	public function getInfoPatientMedecin($idcons){
		$adapter = $this->tableGateway->getAdapter ();
		$sql = new Sql ( $adapter );
		$select = $sql->select ();
		$select->columns( array( '*' ));
		$select->from( array( 'c' => 'consultation' ));
		$select->join( array('s' => 'service'), 's.ID_SERVICE = c.ID_SERVICE' , array (
				'NomService' => 'NOM',
				'DomaineService' => 'DOMAINE'
		) );
		$select->join( array('p' => 'patient' ), 'p.ID_PERSONNE = c.ID_PATIENT' , array('*'));
		$select->join( array('pers' => 'personne'), 'pers.ID_PERSONNE = p.ID_PERSONNE' , array('*'));
		$select->join( array('m' => 'personne'), 'm.ID_PERSONNE = c.ID_MEDECIN' , array(
				'NomMedecin' => 'NOM', 
				'PrenomMedecin' => 'PRENOM', 
				'AdresseMedecin' => 'ADRESSE',
				'TelephoneMedecin' => 'TELEPHONE'
		));
		$select->where ( array( 'c.idcons' => $idcons));
		
		$stat = $sql->prepareStatementForSqlObject ( $select );
		$result = $stat->execute ();
		
		return $result;
	}
	
	public function addBandelette($bandelettes){
		$values = array();
		if($bandelettes['albumine'] == 1){
			$values[] = array('idtypebandelette'=>1, 'idcons'=>$bandelettes['idcons'], 'croix_bandelette'=>(int)$bandelettes['croixalbumine']);
		}
		if($bandelettes['sucre'] == 1){
			$values[] = array('idtypebandelette'=>2, 'idcons'=>$bandelettes['idcons'], 'croix_bandelette'=>(int)$bandelettes['croixsucre']);
		}
		if($bandelettes['corpscetonique'] == 1){
			$values[] = array('idtypebandelette'=>3, 'idcons'=>$bandelettes['idcons'], 'croix_bandelette'=>(int)$bandelettes['croixcorpscetonique']);
		}
		
		for($i = 0 ; $i < count($values) ; $i++ ){
			$db = $this->tableGateway->getAdapter();
			$sql = new Sql($db);
			$sQuery = $sql->insert()
			->into('bandelette')
			->columns(array('idtypebandelette', 'idcons', 'croix_bandelette'))
			->values($values[$i]);
			$stat = $sql->prepareStatementForSqlObject($sQuery);
			$stat->execute();
		}
	}
	
	public function getBandelette($id_cons){
		$db = $this->tableGateway->getAdapter();
		$sql = new Sql($db);
		$sQuery = $sql->select()
		->from('bandelette')
		->columns(array('*'))
		->where(array('id_cons' => $id_cons));
		$stat = $sql->prepareStatementForSqlObject($sQuery);
		$result = $stat->execute();
		
		$donnees = array();
		$donnees['temoin'] = 0;
		foreach ($result as $resultat){
			if($resultat['idtypebandelette'] == 1){
				$donnees['albumine'] = 1; //C'est à coché
				$donnees['croixalbumine'] = $resultat['croix_bandelette'];
			}
			if($resultat['idtypebandelette'] == 2){
				$donnees['sucre'] = 1; //C'est à coché
				$donnees['croixsucre'] = $resultat['croix_bandelette'];
			}
			if($resultat['idtypebandelette'] == 3){
				$donnees['corpscetonique'] = 1; //C'est à coché
				$donnees['croixcorpscetonique'] = $resultat['croix_bandelette'];
			}
			
			//temoin
			$donnees['temoin'] = 1;
		}
		
		return $donnees;
	}
	
	public function deleteBandelette($id_cons){
		$db = $this->tableGateway->getAdapter();
		$sql = new Sql($db);
		$sQuery = $sql->delete()
		->from('bandelette')
		->where(array('id_cons' => $id_cons));
		$stat = $sql->prepareStatementForSqlObject($sQuery);
		$result = $stat->execute();
	}
	
	//Tous les patients consultes sauf ceux du jour
	public function tousPatientsCons($idService){
		$today = new \DateTime();
		$date = $today->format('Y-m-d');
		$adapter = $this->tableGateway->getAdapter ();
		$sql = new Sql ( $adapter );
		$select = $sql->select ();
		$select->from ( array (
				'p' => 'patient'
		) );
		$select->columns(array () );
		$select->join(array('pers' => 'personne'), 'pers.ID_PERSONNE = p.ID_PERSONNE', array(
				'Nom' => 'NOM',
				'Prenom' => 'PRENOM',
				'Datenaissance' => 'DATE_NAISSANCE',
				'Sexe' => 'SEXE',
				'Adresse' => 'ADRESSE',
				'Nationalite' => 'NATIONALITE_ACTUELLE',
				'Id' => 'ID_PERSONNE'
		));
		
		$select->join(array('c' => 'consultation'), 'p.ID_PERSONNE = c.ID_PATIENT', array('Id_cons' => 'idcons', 'Dateonly' => 'DATEONLY', 'Consprise' => 'CONSPRISE'));
		$select->join(array('s' => 'service'), 'c.ID_SERVICE = s.ID_SERVICE', array('Nomservice' => 'NOM'));
		$select->join(array('cons_eff' => 'consultation_effective'), 'cons_eff.idcons = c.idcons' , array('*'));
		$where = new Where();
		$where->equalTo('s.ID_SERVICE', $idService);
		$where->notEqualTo('DATEONLY', $date);
		$select->order('c.DATE DESC');
		$select->group('c.ID_PATIENT');
		$select->where($where);
		$stmt = $sql->prepareStatementForSqlObject($select);
		$result = $stmt->execute();
		return $result;
	}
	
	//liste des patients Ã  consulter par le medecin dans le service de ce dernier
	public function listePatientsConsParMedecin($idService){
		$today = new \DateTime();
		$date = $today->format('Y-m-d');
		$adapter = $this->tableGateway->getAdapter ();
		$sql = new Sql ( $adapter );
		$select = $sql->select ();
		$select->from ( array (
				'p' => 'patient'
		) );
		$select->columns(array () );
		$select->join(array('pers'=>'personne'), 'pers.ID_PERSONNE = p.ID_PERSONNE', array(
				'Nom' => 'NOM',
				'Prenom' => 'PRENOM',
				'Datenaissance' => 'DATE_NAISSANCE',
				'Sexe' => 'SEXE',
				'Adresse' => 'ADRESSE',
				'Nationalite' => 'NATIONALITE_ACTUELLE',
				'Id' => 'ID_PERSONNE'
		));
		$select->join(array('c' => 'consultation'), 'p.ID_PERSONNE = c.ID_PATIENT', array('Id_cons' => 'idcons', 'dateonly' => 'DATEONLY', 'Consprise' => 'CONSPRISE', 'date' => 'DATE'));
		$select->join(array('cons_eff' => 'consultation_effective'), 'cons_eff.idcons = c.idcons' , array('*'));
		$select->join(array('a' => 'admission'), 'c.ID_PATIENT = a.id_patient', array('Id_admission' => 'id_admission'));
		$select->where(array('c.ID_SERVICE' => $idService, 'DATEONLY' => $date, 'a.date_cons' => $date, 'c.ARCHIVAGE' => 0));
		$select->order('id_admission ASC');
	
		$stmt = $sql->prepareStatementForSqlObject($select);
		$result = $stmt->execute();
		return $result;
	}
	
	public function getPatientsRV($id_service){
		$today = new \DateTime();
		$date = $today->format('Y-m-d');
	
		$adapter = $this->tableGateway->getAdapter();
		$sql = new Sql( $adapter );
		$select = $sql->select();
		$select->from( array(
				'rec' =>  'rendezvous_consultation'
		));
		$select->join(array('cons' => 'consultation'), 'cons.idcons = rec.idcons ', array('*'));
		$select->where( array(
				'rec.DATE' => $date,
				'cons.ID_SERVICE' => $id_service,
		) );
	
		$statement = $sql->prepareStatementForSqlObject( $select );
		$resultat = $statement->execute();
	
		$tab = array();
		foreach ($resultat as $result) {
			$tab[$result['ID_PATIENT']] = $result['HEURE'];
		}
	
		return $tab;
	}
	
	/**
	 * Recuperation de la liste des medicaments
	 */
	public function listeDeTousLesMedicaments(){
		$adapter = $this->tableGateway->getAdapter();
		$sql = new Sql ( $adapter );
		$select = $sql->select('consommable');
		$select->columns(array('ID_MATERIEL','INTITULE'));
		$stat = $sql->prepareStatementForSqlObject($select);
		$result = $stat->execute();
	
		return $result;
	}
	
	/**
	 * RECUPERER LA FORME DES MEDICAMENTS
	 */
	
	public function formesMedicaments(){
		$adapter = $this->tableGateway->getAdapter ();
		$sql = new Sql ( $adapter );
		$select = $sql->select ();
		$select->columns( array('*'));
		$select->from( array( 'forme' => 'forme_medicament' ));
	
		$stat = $sql->prepareStatementForSqlObject ( $select );
		$result = $stat->execute ();
	
		return $result;
	}
	
	/**
	 * RECUPERER LES TYPES DE QUANTITE DES MEDICAMENTS
	 */
	
	public function typeQuantiteMedicaments(){
		$adapter = $this->tableGateway->getAdapter ();
		$sql = new Sql ( $adapter );
		$select = $sql->select ();
		$select->columns( array('*'));
		$select->from( array( 'typeQuantite' => 'quantite_medicament' ));
	
		$stat = $sql->prepareStatementForSqlObject ( $select );
		$result = $stat->execute ();
	
		return $result;
	}
	
	public function getInfoPatient($id_personne) {
		$db = $this->tableGateway->getAdapter();
		$sql = new Sql($db);
		$sQuery = $sql->select()
		->from(array('pat' => 'patient'))
		->columns( array( '*' ))
		->join(array('pers' => 'personne'), 'pers.id_personne = pat.id_personne' , array('*'))
		->where(array('pat.ID_PERSONNE' => $id_personne));
	
		$stat = $sql->prepareStatementForSqlObject($sQuery);
		$resultat = $stat->execute()->current();
	
		return $resultat;
	}
	
	public function getPhoto($id) {
		$donneesPatient =  $this->getInfoPatient( $id );
	
		$nom = null;
		if($donneesPatient){$nom = $donneesPatient['PHOTO'];}
		if ($nom) {
			return $nom . '.jpg';
		} else {
			return 'identite.jpg';
		}
	}
	
	//liste des patients deja consultÃ©s par le medecin pour l'espace recherche
	public function listePatientsConsMedecin($idService){
		$today = new \DateTime();
		$date = $today->format('Y-m-d');
		$adapter = $this->tableGateway->getAdapter ();
		$sql = new Sql ( $adapter );
		$select = $sql->select ();
		$select->from ( array (
				'p' => 'patient'
		) );
		$select->columns(array () );
		$select->join(array('pers' => 'personne'), 'pers.ID_PERSONNE = p.ID_PERSONNE', array(
				'Nom' => 'NOM',
				'Prenom' => 'PRENOM',
				'Datenaissance' => 'DATE_NAISSANCE',
				'Sexe' => 'SEXE',
				'Adresse' => 'ADRESSE',
				'Nationalite' => 'NATIONALITE_ACTUELLE',
				'Id' => 'ID_PERSONNE'
		));
		$select->join(array('c' => 'consultation'), 'p.ID_PERSONNE = c.ID_PATIENT', array('Id_cons' => 'idcons', 'Dateonly' => 'DATEONLY', 'Consprise' => 'CONSPRISE', 'date' => 'DATE'));
		$select->join(array('cons_eff' => 'consultation_effective'), 'cons_eff.idcons = c.idcons' , array('*'));
		$select->join( array('e1' => 'employe'), 'e1.id_personne = c.ID_MEDECIN' , array('*'));
		$select->join(array('s' => 'service'), 'c.ID_SERVICE = s.ID_SERVICE', array('Nomservice' => 'NOM'));
		$where = new Where();
		$where->equalTo('s.ID_SERVICE', $idService);
		$where->notEqualTo('DATEONLY', $date);
		$select->where($where);
		$select->order('c.DATE DESC');
		//$select->group('c.ID_PATIENT');
	
		$stmt = $sql->prepareStatementForSqlObject($select);
		$result = $stmt->execute();
		
		//Recuperation des donnees 
		$tableauDonnees = array();
		$tableauCles = array();
		foreach ($result as $resultat){
 			if(!in_array($resultat['Id'], $tableauCles)){
 				$tableauCles[] = $resultat['Id']; 
 				$tableauDonnees[] = $resultat;
 			}
		}
		
		return $tableauDonnees;
	
	}
	
 	public function addTraitementsInstrumentaux($traitement_instrumental){

 		
 		$db = $this->tableGateway->getAdapter();
 		$sql = new Sql($db);
 		$sQuery = $sql->delete()
 		->from('traitement_instrumental')
 		->where(array('id_cons' => $traitement_instrumental['id_cons']));
 		$stat = $sql->prepareStatementForSqlObject($sQuery);
 		$result = $stat->execute();
 		
		if($traitement_instrumental['endoscopie_interventionnelle'] || $traitement_instrumental['radiologie_interventionnelle'] ||
		 $traitement_instrumental['cardiologie_interventionnelle'] || $traitement_instrumental['autres_interventions']){
			$db = $this->tableGateway->getAdapter();
			$sql = new Sql($db);
			$sQuery = $sql->insert()
			->into('traitement_instrumental')
			->columns(array('id_cons', 'endoscopie_interventionnelle', 'radiologie_interventionnelle', 'cardiologie_interventionnelle', 'autres_interventions'))
			->values($traitement_instrumental);
			$stat = $sql->prepareStatementForSqlObject($sQuery);
			$stat->execute();
		}
 	}
 	
 	public function getTraitementsInstrumentaux($id_cons){
 		$db = $this->tableGateway->getAdapter();
 		$sql = new Sql($db);
 		$sQuery = $sql->select()
 		->from('traitement_instrumental')
 		->where(array('id_cons' => $id_cons));
 		$stat = $sql->prepareStatementForSqlObject($sQuery);
 		$result = $stat->execute()->current();
 		return $result;
 	}
 	
 	
 	public function fetchConsommable(){
 		$adapter = $this->tableGateway->getAdapter();
 		$sql = new Sql ( $adapter );
 		$select = $sql->select('consommable');
 		$select->columns(array('ID_MATERIEL','INTITULE'));
 		$stat = $sql->prepareStatementForSqlObject($select);
 		$result = $stat->execute();
 		foreach ($result as $data) {
 			$options[$data['ID_MATERIEL']] = $data['INTITULE'];
 		}
 		return $options;
 	}
 	
 	
 	//GESTION DES FICHIER MP3
 	//GESTION DES FICHIER MP3
 	//GESTION DES FICHIER MP3
 	public function insererMp3($titre , $nom, $id_cons, $type){
 		$db = $this->tableGateway->getAdapter();
 		$sql = new Sql($db);
 		$sQuery = $sql->insert()
 		->into('fichier_mp3')
 		->columns(array('titre', 'nom', 'id_cons', 'type'))
 		->values(array('titre' => $titre , 'nom' => $nom, 'id_cons'=>$id_cons, 'type'=>$type));
 	
 		$stat = $sql->prepareStatementForSqlObject($sQuery);
 		return $stat->execute();
 	}
 	
 	public function getMp3($id_cons, $type){
 		$db = $this->tableGateway->getAdapter();
 		$sql = new Sql($db);
 		$sQuery = $sql->select()
 		->from(array('f' => 'fichier_mp3'))->columns(array('*'))
 		->where(array('id_cons' => $id_cons, 'type' => $type))
 		->order('id DESC');
 	
 		$stat = $sql->prepareStatementForSqlObject($sQuery);
 		$result = $stat->execute();
 		return $result;
 	}
 	
 	public function supprimerMp3($idLigne, $id_cons, $type){
 		$liste = $this->getMp3($id_cons, $type);
 	
 		$i=1;
 		foreach ($liste as $list){
 			if($i == $idLigne){
 				unlink('C:\wamp\www\simens\public\audios\\'.$list['nom']);
 	
 				$db = $this->tableGateway->getAdapter();
 				$sql = new Sql($db);
 				$sQuery = $sql->delete()
 				->from('fichier_mp3')
 				->where(array('id' => $list['id']));
 	
 				$stat = $sql->prepareStatementForSqlObject($sQuery);
 				$stat->execute();
 	
 				return true;
 			}
 			$i++;
 		}
 		return false;
 	}
 	
 	
 	//GESTION DES FICHIERS VIDEOS
 	//GESTION DES FICHIERS VIDEOS
 	//GESTION DES FICHIERS VIDEOS
 	public function insererVideo($titre , $nom, $format, $id_cons){
 		$db = $this->tableGateway->getAdapter();
 		$sql = new Sql($db);
 		$sQuery = $sql->insert()
 		->into('fichier_video')
 		->columns(array('titre', 'nom', 'format', 'id_cons'))
 		->values(array('titre' => $titre , 'nom' => $nom, 'format' => $format, 'id_cons'=>$id_cons));
 	
 		$stat = $sql->prepareStatementForSqlObject($sQuery);
 		return $stat->execute();
 	}
 	
 	public function getVideos($id_cons){
 		$db = $this->tableGateway->getAdapter();
 		$sql = new Sql($db);
 		$sQuery = $sql->select()
 		->from(array('f' => 'fichier_video'))->columns(array('*'))
 		->where(array('id_cons' => $id_cons))
 		->order('id DESC');
 	
 		$stat = $sql->prepareStatementForSqlObject($sQuery);
 		$result = $stat->execute();
 		return $result;
 	}
 	
 	public function getVideoWithId($id){
 		$db = $this->tableGateway->getAdapter();
 		$sql = new Sql($db);
 		$sQuery = $sql->select()
 		->from(array('f' => 'fichier_video'))->columns(array('*'))
 		->where(array('id' => $id));
 	
 		$stat = $sql->prepareStatementForSqlObject($sQuery);
 		$result = $stat->execute()->current();
 		return $result;
 	}
 	
 	public function supprimerVideo($id){

 		$laVideo = $this->getVideoWithId($id);
 		$result = unlink('C:\wamp\www\simens\public\videos\\'.$laVideo['nom']);
 		
 		$db = $this->tableGateway->getAdapter();
 		$sql = new Sql($db);
 		$sQuery = $sql->delete()->from('fichier_video')->where(array('id' => $id));

 		$stat = $sql->prepareStatementForSqlObject($sQuery);
 		$stat->execute();
 		
 		return $result;
 	}
 	
 	//COMPTE RENDU OPERATOIRE
 	//COMPTE RENDU OPERATOIRE
 	public function deleteCompteRenduOperatoire($id_cons, $type){
 		$db = $this->tableGateway->getAdapter();
 		$sql = new Sql($db);
 		$sQuery = $sql->delete()
 		->from('compte_rendu_operatoire')
 		->where(array('id_cons' => $id_cons, 'type' => $type));
 		$stat = $sql->prepareStatementForSqlObject($sQuery);
 		$result = $stat->execute();
 	}
 	
 	public function addCompteRenduOperatoire($note, $type, $id_cons){
 		$this->deleteCompteRenduOperatoire($id_cons, $type);
 		if($note) {
 			$db = $this->tableGateway->getAdapter();
 			$sql = new Sql($db);
 			$sQuery = $sql->insert()
 			->into('compte_rendu_operatoire')
 			->values(array('note' => $note , 'type' => $type, 'id_cons'=>$id_cons));
 				
 			$stat = $sql->prepareStatementForSqlObject($sQuery);
 			return $stat->execute();
 		}
 	}
 	
 	public function getCompteRenduOperatoire($type, $id_cons){
 		$db = $this->tableGateway->getAdapter();
 		$sql = new Sql($db);
 		$sQuery = $sql->select()
 		->from(array('c' => 'compte_rendu_operatoire'))->columns(array('*'))
 		->where(array('id_cons' => $id_cons, 'type' => $type));
 		
 		$stat = $sql->prepareStatementForSqlObject($sQuery);
 		$result = $stat->execute()->current();
 		return $result;
 	}
 	
 	
 	
 	//GESTION DES EXAMENS DU JOUR LORS D'UNE HOSPITALISATION
 	//GESTION DES EXAMENS DU JOUR LORS D'UNE HOSPITALISATION
 	public function addConsultationExamenDuJour($codeExamen, $values , $IdDuService , $idMedecin){
 		$this->tableGateway->getAdapter()->getDriver()->getConnection()->beginTransaction();
 		$date = new \DateTime();
 		$aujourdhui = $date->format('Y-m-d H:i:s');
 		$dateonly = $date->format('Y-m-d');
 		
 		try {
 			$dataconsultation = array(
					'idcons'=> $codeExamen,
 					'ID_MEDECIN'=> $idMedecin,
 					'ID_PATIENT'=> $values->id_personne,
 					'DATE'=> $aujourdhui,
 					'POIDS' => $values->poids,
 					'TAILLE' => $values->taille,
  					'TEMPERATURE' => $values->temperature,
  					'PRESSION_ARTERIELLE' => $values->pressionarterielle,
 					'POULS' => $values->pouls,
 					'FREQUENCE_RESPIRATOIRE' => $values->frequence_respiratoire,
 					'GLYCEMIE_CAPILLAIRE' => $values->glycemie_capillaire,
  					'DATEONLY' => $dateonly,
  					'ID_SERVICE' => $IdDuService
 			);
 			
 			$this->tableGateway->insert($dataconsultation);
 	
 			$this->tableGateway->getAdapter()->getDriver()->getConnection()->commit();
 		} catch (\Exception $e) {
 			$this->tableGateway->getAdapter()->getDriver()->getConnection()->rollback();
 		}
 	}
 	
 	public function addExamenDuJour($id_cons, $id_hosp){
 		$db = $this->tableGateway->getAdapter();
 		$sql = new Sql($db);
 		$sQuery = $sql->insert()
 		->into('examen_du_jour')
 		->values(array('idcons' => $id_cons, 'ID_HOSP' => $id_hosp));
 		$requete = $sql->prepareStatementForSqlObject($sQuery);
 		$requete->execute();
 	}
 	
 	public function getExamenDuJour($id_hosp){
 		$db = $this->tableGateway->getAdapter();
 		$sql = new Sql($db);
 		$sQuery = $sql->select()
 		->from(array('e' => 'examen_du_jour'))
 		->join(array('c' => 'consultation'), 'c.idcons = e.idcons' , array('*'))
 		->join(array('p' => 'personne'), 'p.ID_PERSONNE = c.ID_MEDECIN' , array('NomMedecin' => 'NOM', 'PrenomMedecin' => 'PRENOM'))
 		->where(array('ID_HOSP' => $id_hosp))
 		->order('DATE DESC');
 		$requete = $sql->prepareStatementForSqlObject($sQuery);
 		return $requete->execute();
 	}
 	
 	public function supprimerExamenDuJour($id_examen_jour){
 		$db = $this->tableGateway->getAdapter();
 		$sql = new Sql($db);
 		$sQuery = $sql->select()
 		->from(array('e' => 'examen_du_jour'))
 		->where(array('ID_EXAMEN_JOUR' => $id_examen_jour));
 		
 		$requete = $sql->prepareStatementForSqlObject($sQuery);
 		$result = $requete->execute()->current();
 		
 		$db2 = $this->tableGateway->getAdapter();
 		$sql2 = new Sql($db2);
 		$sQuery2 = $sql2->delete()
 		->from('consultation')
 		->where(array('idcons' => $result['idcons']));
 			
 		$requete2 = $sql2->prepareStatementForSqlObject($sQuery2);
 		$requete2->execute();
 		
 		return $result['ID_HOSP'];
 	}
 	
 	public function getExamenDuJourParIdExamenJour($id_examen_jour){
 		$db = $this->tableGateway->getAdapter();
 		$sql = new Sql($db);
 		$sQuery = $sql->select()
 		->from(array('e' => 'examen_du_jour'))
 		->join(array('c' => 'consultation'), 'c.idcons = e.idcons' , array('*'))
 		->join(array('p' => 'personne'), 'p.ID_PERSONNE = c.ID_MEDECIN' , array('NomMedecin' => 'NOM', 'PrenomMedecin' => 'PRENOM'))
 		->where(array('ID_EXAMEN_JOUR' => $id_examen_jour));
 		$requete = $sql->prepareStatementForSqlObject($sQuery);
 		return $requete->execute()->current();
 	}
 	
 	public function getConsultationExamenJour($id_cons){
 		$db = $this->tableGateway->getAdapter();
 		$sql = new Sql($db);
 		$sQuery = $sql->select()
 		->from(array('c' => 'consultation'))
 		->where(array('idcons' => $id_cons));
 		$requete = $sql->prepareStatementForSqlObject($sQuery);
 		return $requete->execute()->current();
 	}
 	
 	public function getTarifDeLacte($idActe){
 		$adapter = $this->tableGateway->getAdapter();
 		$sql = new Sql($adapter);
 		$select = $sql->select();
 		$select->from(array('s'=>'actes'));
 		$select->columns(array('*'));
 		$select->where(array('id' => $idActe));
 		$stat = $sql->prepareStatementForSqlObject($select);
 		$result = $stat->execute()->current();
 		return $result;
 	}
 	
 	//Recupere les antecedents médicaux
 	//Recupere les antecedents médicaux
 	public function getAntecedentMedicauxParLibelle($libelle){
 		$adapter = $this->tableGateway->getAdapter();
 		$sql = new Sql($adapter);
 		$select = $sql->select();
 		$select->from(array('am'=>'ant_medicaux'));
 		$select->columns(array('*'));
 		$select->where(array('libelle' => $libelle));
 		return $sql->prepareStatementForSqlObject($select)->execute()->current();
 	}
 	
 	//Ajout des antécédents médicaux
 	//Ajout des antécédents médicaux
 	public function addAntecedentMedicaux($data){
 		$date = (new \DateTime())->format('Y-m-d H:i:s');
 		$db = $this->tableGateway->getAdapter();
 		$sql = new Sql($db);
 		
 		for($i = 0; $i<$data->nbCheckboxAM; $i++){
 			$champ = "champTitreLabel_".$i;
 			$libelle =  $data->$champ;
 			
 			if(!$this->getAntecedentMedicauxParLibelle($libelle)){
 				$sQuery = $sql->insert()
 				->into('ant_medicaux')
 				->values(array('libelle' => $libelle, 'date_enregistrement' => $date, 'id_medecin' => $data->med_id_personne));
 				$sql->prepareStatementForSqlObject($sQuery)->execute();
 			}
 			
 		}
 		
 	}
 	
 	
 	//Recupere l'antecedent médical de la personne
 	//Recupere l'antecedent médical de la personne
 	public function getAntecedentMedicauxPersonneParId($id, $id_patient){
 		$adapter = $this->tableGateway->getAdapter();
 		$sql = new Sql($adapter);
 		$select = $sql->select();
 		$select->from(array('amp'=>'ant_medicaux_personne'));
 		$select->columns(array('*'));
 		$select->where(array('id_ant_medicaux' => $id, 'id_patient' => $id_patient));
 		return $sql->prepareStatementForSqlObject($select)->execute()->current();
 	}
 	
 	
 	//Recuperer les antecedents médicaux du patient
 	//Recuperer les antecedents médicaux du patient
 	public function getAntecedentsMedicauxPatient($id_patient){
 		$adapter = $this->tableGateway->getAdapter();
 		$sql = new Sql($adapter);
 		$select = $sql->select();
 		$select->columns(array('*'));
 		$select->from(array('amp'=>'ant_medicaux_personne'));
 		$select->join( array('am' => 'ant_medicaux'), 'am.id = amp.id_ant_medicaux' , array('*'));
 		$select->where(array('amp.id_patient' => $id_patient));
 		$result = $sql->prepareStatementForSqlObject($select)->execute();
 		
 		$tableau = array();
 		
 		foreach ($result as $resul){
 			$tableau[] = $resul['libelle'];
 		}
 		
 		return $tableau;
 	}
 	
 	
 	//Ajout des antécédents médicaux de la personne
 	//Ajout des antécédents médicaux de la personne
 	public function addAntecedentMedicauxPersonne($data){
 		$date = (new \DateTime())->format('Y-m-d H:i:s');
 		$db = $this->tableGateway->getAdapter();
 		$sql = new Sql($db);
 			
 		//Tableau des nouveaux antecedents ajouter
 		$tableau = array();
 		
 		for($i = 0; $i<$data->nbCheckboxAM; $i++){
 			$champ = "champTitreLabel_".$i;
 			$libelle =  $data->$champ;
 			
 			//Ajout des nouveaux libelles dans le tableau
 			$tableau[] = $libelle;
 			
 			$antecedent = $this->getAntecedentMedicauxParLibelle($libelle);
 			if($antecedent){
 				if(!$this->getAntecedentMedicauxPersonneParId($antecedent['id'], $data->id_patient)){
 					$sQuery = $sql->insert()
 					->into('ant_medicaux_personne')
 					->values(array('id_ant_medicaux' => $antecedent['id'], 'id_patient' => $data->id_patient, 'date_enregistrement' => $date, 'id_medecin' => $data->med_id_personne));
 					$sql->prepareStatementForSqlObject($sQuery)->execute();
 				}
 			}
 		}
 		
 		//Tableau de tous les antecedents medicaux du patient avant la mise à jour
 		$tableau2 = $this->getAntecedentsMedicauxPatient($data->id_patient);
 		
 		//var_dump($data->nbCheckboxAM); exit();
 		//Suppression des antecedents non sélectionnés
 		for($i=0; $i<count($tableau2); $i++){
 			if(!in_array($tableau2[$i], $tableau)){
 				$id_ant_medicaux = $this->getAntecedentMedicauxParLibelle($tableau2[$i])['id'];
 				$sQuery = $sql->delete()
 				->from('ant_medicaux_personne')
 				->where(array('id_ant_medicaux' => $id_ant_medicaux, 'id_patient' => $data->id_patient));
 				$sql->prepareStatementForSqlObject($sQuery)->execute();
 			}
 		}
 			
 	}
 	
 	
 	//Recupere les antecedents médicaux de la personne
 	//Recupere les antecedents médicaux de la personne
 	public function getAntecedentMedicauxPersonneParIdPatient($id_patient){
 		$adapter = $this->tableGateway->getAdapter();
 		$sql = new Sql($adapter);
 		$select = $sql->select();
 		$select->columns(array('*'));
 		$select->from(array('amp'=>'ant_medicaux_personne'));
 		$select->join( array('am' => 'ant_medicaux'), 'am.id = amp.id_ant_medicaux' , array('*'));
 		$select->where(array('amp.id_patient' => $id_patient));
 		return $sql->prepareStatementForSqlObject($select)->execute();
 	}
 	
 	
 	//Recupere les antecedents médicaux 
 	//Recupere les antecedents médicaux 
 	public function getAntecedentsMedicaux(){
 		$adapter = $this->tableGateway->getAdapter();
 		$sql = new Sql($adapter);
 		$select = $sql->select();
 		$select->columns(array('*'));
 		$select->from(array('am'=>'ant_medicaux'));
 		return $sql->prepareStatementForSqlObject($select)->execute();
 	}
 	
 	
 	//Recupere la liste des actes
 	//Recupere la liste des actes
 	public function getListeDesActes(){
 		$adapter = $this->tableGateway->getAdapter();
 		$sql = new Sql($adapter);
 		$select = $sql->select();
 		$select->columns(array('*'));
 		$select->from(array('a'=>'acte'));
 		return $sql->prepareStatementForSqlObject($select)->execute();
 	}
 	
 	
 	//Recupere la laiste des voie_administration selectionnée
 	public function getVoieAdministration($idcons){
 		$adapter = $this->tableGateway->getAdapter();
 		$sql = new Sql($adapter);
 		$select = $sql->select();
 		$select->from(array('voie'=>'voie_administration'));
 		$select->columns(array('*'));
 		$select->where(array('idcons' => $idcons));
 		return $sql->prepareStatementForSqlObject($select)->execute()->current();
 	}
 	
 	//Verifier si un tableau est vide ou pas
 	function array_empty($array) {
 		$is_empty = true;
 		foreach($array as $k) {
 			$is_empty = $is_empty && empty($k);
 		}
 		return $is_empty;
 	}
 	
 	public function addVoieAdministration($data){
 		$idcons = $data['idcons'];
 		if($this->getVoieAdministration($idcons)){
 			$db = $this->tableGateway->getAdapter();
 			$sql = new Sql($db);
 			$sQuery = $sql->delete()->from('voie_administration')
 			->where(array('idcons' => $idcons));
 			$sql->prepareStatementForSqlObject($sQuery)->execute();
 		}
 		
 		$donnees = array();
 		
 		//S'il y a une intensite supérieur à 3 alors il y a des données de prise en charge 
 		if( $data['intensite'] > 3 ){
 			$donnees['m1'] = $data['voie_med_1'];
 		    $donnees['m2'] = $data['voie_med_2'];
 			$donnees['m3'] = $data['voie_med_3'];
 			$donnees['m4'] = $data['voie_med_4'];
 			$donnees['m5'] = $data['voie_med_5'];
 		}
 		
 		//S'il y a un motif 'Fièvre' alors il y a des données de prise en charge 
 		if( $data['motif_admission1'] == 1 || $data['motif_admission2'] == 1 ||
 		    $data['motif_admission3'] == 1 || $data['motif_admission4'] == 1 ||
 			$data['motif_admission5'] == 1){
 			
 			$donnees['m6'] = $data['voie_med_6'];
 		}
 		
 		//S'il y a des infos de prise en charge on insère
 		if(!$this->array_empty($donnees)){
 			$donnees['idcons'] = $idcons;
 			
 			$db = $this->tableGateway->getAdapter();
 			$sql = new Sql($db);
 			$sQuery = $sql->insert()->into('voie_administration')->values($donnees);
 			$sql->prepareStatementForSqlObject($sQuery)->execute();
 		}

 	}
 	
 	
}