<?php
return array(
    'acl' => array(
    		
        'roles' => array(
        		'guest'   => null,
                'admin' => 'guest',
         		'superAdmin'  => 'guest',
        		
        		
        		
        		//***Cerpad
        		//***Cerpad
        		'secretariat' => 'guest',
                'facturation' => 'guest',
        		'infirmier'   => 'guest',
        		'technicien'  => 'guest',
        		'biologiste'  => 'guest',
        		'medecin'     => 'guest',
        		//***************
        		//***************
        ),
    		

        'resources' => array(
            
            'allow' => array(

                /***
                 *AdminController
                 */

                'Admin\Controller\Admin' => array(

                    'login' => 'guest',
                    'logout' => 'guest',
                    'bienvenue' => 'guest',
                    'modifier-password' => 'guest',
                    'verifier-password' => 'guest',
                    'mise-a-jour-user-password' => 'guest',
                    

                    'utilisateurs' => array('admin','superAdmin'),
                    'liste-utilisateurs-ajax' => array('admin','superAdmin'),
                    'modifier-utilisateur' => array('admin','superAdmin'),
                    'liste-agent-personnel-ajax' => array('admin','superAdmin'),
                    'visualisation' => array('admin','superAdmin'),
                    'nouvel-utilisateur' => array('admin','superAdmin'),
                    'verifier-username' => array('admin','superAdmin'),
                    'parametrages' => array('admin','superAdmin'),
                    'gestion-des-hopitaux' => array('admin','superAdmin'),
                    'liste-hopitaux-ajax' => array('admin','superAdmin'),

                    'get-departements' => array('admin','superAdmin'),
                    'ajouter-hopital' => array('admin','superAdmin'),
                    'get-infos-hopital' => array('admin','superAdmin'),
                    'get-infos-modification-hopital' => array('admin','superAdmin'),

                    

                    'gestion-des-batiments' => array('admin','superAdmin'),
                    'gestion-des-services' => array('admin','superAdmin'),
                    'liste-services-ajax' => array('admin','superAdmin'),
                    'get-infos-service' => array('admin','superAdmin'),

                    'get-infos-modification-service' => array('admin','superAdmin'),
                    'ajouter-service' => array('admin','superAdmin'),
                    'supprimer-service' => array('admin','superAdmin'),


                    'gestion-des-actes' => array('admin','superAdmin'),
                    'liste-actes-ajax' => array('admin','superAdmin'),
                    'get-infos-acte' => array('admin','superAdmin'),
                    'get-infos-modification-acte' => array('admin','superAdmin'),
                    'ajouter-acte' => array('admin','superAdmin'),
                    'supprimer-acte'  => array('admin','superAdmin'),

                ),
                
                /***
                 * PersonnelController
                */
                
                'Personnel\Controller\Personnel' => array(
                    'liste-personnel' => array('admin','superAdmin'),
                    'liste-personnel-ajax' => array('admin','superAdmin'),
                    'info-personnel' => array('admin','superAdmin'),
                    'supprimer' => array('admin','superAdmin'),
                    'modifier-dossier' => array('admin','superAdmin'),
                    'dossier-personnel' => array('admin','superAdmin'),
                ),
         				    
         	    //*************** CERPAD ********************
                //*************** CERPAD ********************
    	    	//*************** CERPAD ********************
    			/***
    			 * SecretariatController
    			 */
                'Secretariat\Controller\Secretariat' => array(

                    'ajouter-patient' => 'secretariat',
                    'enregistrement' => 'secretariat',
                    'liste-patient' => 'secretariat',
                    'liste-patients-ajax' => 'secretariat',
                    'infos-patient' => 'secretariat',
                    'modifier-patient' => 'secretariat',
                    'demandes-analyses' => 'secretariat',
                    'liste-recherche-patient-ajax' => 'secretariat',
                    'popup-vue-patient' => 'secretariat',
                    'demandes-analyses-vue' => 'secretariat',
                    'get-liste-analyses' => 'secretariat',
                    'get-tarif-analyse' => 'secretariat',
                    'envoyer-demandes-analyses' => 'secretariat',
                	'supprimer-patient' => 'secretariat', 	
    								
                    //'liste-demandes-ajax' => 'secretariat',
                	'liste-demandes-aujourdhui-ajax' => 'secretariat',	
                	'liste-demandes-tous-ajax' => 'secretariat',
                    'liste-demandes' => 'secretariat',
                    'get-informations-patient' => 'secretariat',
                    'liste-demandes-filtre-ajax' => 'secretariat',
                    'liste-demandes-analyse' => 'secretariat',
                    'get-liste-analyses-demandees' => 'secretariat',
                    'impression-demandes-analyses' => array('secretariat', 'biologiste'),
                    'impression-analyses-demandees' => array('secretariat', 'biologiste'),
                		
                	'liste-resultats' => 'secretariat',	
                	'liste-resultats-ajax' => 'secretariat',
                	'get-informations-resultats-analyses-patient' => 'secretariat',
                	'impression-resultats-analyses-demandees' => 'secretariat',
                	'get-resultats-liste-analyses-demandees' => 'secretariat',
                	'liste-non-conforme' => 'secretariat',	
                	'liste-non-conformite-ajax' => 'secretariat',

                	'patient-rappeler' => 'secretariat',	
               		'patient-non-rappeler' => 'secretariat',
    								
                ),
    						
                /***
                 * LaboratoireController  ---  LaboratoireController  ---  LaboratoireController
 				 */

            	/*
            	 * Le Biologiste   ----   Le Biologiste
            	 */	
                'Laboratoire\Controller\Biologiste' => array(

                    'liste-demandes-analyses' => 'biologiste', 
                    'liste-demandes-analyses-ajax' => 'biologiste',
                    'infos-patient' => 'biologiste', 
                    'get-informations-patient' => 'biologiste', 
                    'get-liste-analyses-demandees' => 'biologiste',
                    'recuperer-analyse' => 'biologiste',
                    'recuperer-les-analyses-de-la-demande' => 'biologiste',
                    'recuperer-les-analyses-demandees-par-type' => 'biologiste',
                    'enregistrer-resultat' => 'biologiste',
                    'enregistrer-resultats-demande' => 'biologiste',
                    'get-liste-analyses' => 'biologiste',
                    'recuperer-les-analyses-demandees-par-type-et-analyse' => 'biologiste',
                    'recuperer-les-analyses-demandees-par-type-et-analyse-et-date' => 'biologiste',
                    'impression-resultats-analyses-demandees' => 'biologiste',
    						    
                    'liste-resultats-analyses' => 'biologiste',
                    'liste-resultats-analyses-ajax' => 'biologiste',
                		
                	'valider-demande' => 'biologiste',
                	'liste-resultats-valides' => 'biologiste',	
                	'liste-resultats-valides-ajax' => 'biologiste',		
                		
                	'get-informations-resultats-analyses-validees-patient' => 'biologiste',
                	'get-liste-analyses-demandees-validees' => 'biologiste',
                	'retirer-validation' => 'biologiste',			
                ),

            	/*
            	 * Le Technicien   ----   Le Technicien
            	 */	
            	'Laboratoire\Controller\Technicien' => array(
            			
            		'liste-bilans' => 'technicien',
            		'liste-bilan-ajax' => 'technicien',	
            		'vue-liste-prelevement-tri' => 'technicien',	
            		'enregistrer-tri-prelevement' => 'technicien',
            		'liste-bilans-tries' => 'technicien',
            		'liste-bilans-tries-ajax' => 'technicien',
            		'vue-modifier-liste-prelevement-tri' => 'technicien',	
            		'modifier-tri-prelevement' => 'technicien',				
            		'impression-feuille-paillasse' => 'technicien', 	
            		'impression-feuille-paillasse-par-patient' => 'technicien',
            		'liste-patients' => 'technicien',	
            		'liste-bilans-tries-resultats-ajax' => 'technicien',	
            		'get-informations-patient' => 'technicien',	
            		'infos-patient' => 'technicien',
            		'get-liste-analyses-demandees' => 'technicien',
            		'impression-resultats-analyses-demandees' => 'technicien',		
            		'recuperer-analyse' => 'technicien',	
            		'enregistrer-resultats-demande' => 'technicien',
            		'recuperer-les-analyses-de-la-demande' => 'technicien',
            			
            		'recuperer-les-analyses-demandees-par-type' => 'technicien',	
            		'recuperer-les-analyses-demandees-par-type-et-analyse' => 'technicien',
            		'recuperer-les-analyses-demandees-par-type-et-analyse-et-date' => 'technicien',
            		
            		'get-liste-analyses' => 'technicien',	
            		'enregistrer-resultat' => 'technicien',						
            			
            		'vue-liste-prelevement-tri-repris' => 'technicien',
            		'prelevement-precedent' => 'technicien',		
            		'enregistrer-tri-prelevement-repris' => 'technicien',	
            	),	
            		
            		
                /***
    			 * FacturationController
    			 */
                'Facturation\Controller\Facturation' => array(
    				        
                    'admission' => 'facturation',
                    'liste-admission-ajax' => 'facturation',
                    'vue-popup' => 'facturation',
                    'vue-admission' => 'facturation',
                    'enregistrer-admission' => 'facturation',
                    'liste-patients-admis' => 'facturation',
                    'liste-patients-admis-ajax' => 'facturation',
                    'liste-analyses-facturees' => 'facturation',
               		'liste-des-analyses-dela-demande-selectionnee' => 'facturation',
                		
                		
                	'impression-facture' => 'facturation',	
                	'supprimer-facturation' => 'facturation',
                	'supprimer-facturation-consultation' => 'facturation',	
                		
               		'historique-liste-patients-admis' => 'facturation',
               		'historique-liste-patients-admis-ajax' => 'facturation',
                	'historique-liste-analyses-facturees' => 'facturation',
                	'historique-liste-analyses-de-la-facture' => 'facturation',		
                		
                	'admission-consultation' => 'facturation',	
                	'liste-admission-consultation-ajax' => 'facturation',
                	'vue-admission-consultation' => 'facturation',	
                	'enregistrer-admission-consultation' => 'facturation',
                	'liste-patients-admis-consultation' => 'facturation',	
                	'liste-patients-admis-consultation-ajax' => 'facturation',	
                	'vue-infos-patients-admission-consultation' => 'facturation',	

                		
                		
                		

                ),

            		
            	/***
            	 * InfirmerieController
            	 */
            	'Infirmerie\Controller\Infirmerie' => array(

            		'liste-patient' => 'infirmier',	
            		'liste-patient-ajax' => 'infirmier',	
            		'liste-analyses-facturees' => 'infirmier',	
            		'enregistrer-bilan' => 'infirmier',	
            			
            		'liste-bilan' => 'infirmier',	            
            		'liste-bilan-ajax' => 'infirmier',
            		'bilan-analyses-facturees' => array('infirmier', 'technicien'),	
            		'modifier-bilan-analyses' => 'infirmier',
            		'modifier-bilan' => 'infirmier',

            		'liste-non-conforme' => 'infirmier',
            		'liste-non-conformite-ajax' => 'infirmier',		
            		'liste-analyses-triees-non-conformes' => 'infirmier',	
            		'historique-liste-bilans' => 'infirmier',	
            			
            			
            		'liste-consultations' => 'infirmier',
            		'liste-consultations-ajax' => 'infirmier',
            		'consultation' => 'infirmier',		
            		'enregistrer-motifs-constantes' => 'infirmier',	
            		'modifier-consultation' => 'infirmier',	

            		'reprise-prelevement' => 'infirmier', 	
            		'enregistrer-bilan-repris' => 'infirmier',
            		'enregistrer-modification' => 'infirmier',	
            	),
                
                
            		
            	/***
            	 * ConsultationController
            	*/
            	'Consultation\Controller\Consultation' => array(

            		'liste-consultations-ajax' => 'medecin',	
            		'liste-consultations' =>	'medecin',
            		'consulter' => 'medecin',
            		'modifier-consultation' => 'medecin',	

            		'informations-statistiques' => 'medecin',	
            				
            	)
                
            		
            		

            	
            	),

            ),
    
        )

);