<?php

    class Sensore extends GenericEntity{

        private $sensoriStorici = null;
        private $sensoriTicketAperti = null;
        private $sensoriListaNera = null;

        function __construct(){
            $this->DBTable = 'A_Sensori';
            $this->IDfield = 'IDsensore';
            parent::__construct();
        }
        
        public function parseGET($get){
            $params['regione'] =    (isset($get['regione']) && $get['regione']!='')         ? $get['regione'] : 'lombardia';
            $params['provincia'] =  (isset($get['provincia']) && $get['provincia']!='')     ? $get['provincia'] : 'ALL';
            $params['rete'] =       (isset($get['rete']) && $get['rete']!='')               ? $get['rete'] : 'ALL';
            $params['tipologia'] =  isset($get['tipologia'])        ? $get['tipologia'] : '';
            $params['allerta'] =    (isset($get['allerta']) && $get['allerta']!='')         ? $get['allerta'] : 'ALL';
            $params['quotaDa'] =   isset($get['quotaDa']) ? $get['quotaDa'] : '';
            $params['quotaA'] =   isset($get['quotaA']) ? $get['quotaA'] : '';
            $params['sensoriStorici'] =    isset($get['sensoriStorici']) ? '1' : '0';
            $params['soloListaNera'] =     isset($get['soloListaNera']) ? '1' : '0';
            $params['soloTicketAperti'] =  isset($get['soloTicketAperti']) ? '1' : '0';
			$params['soloAnnotazioniAperte'] =  isset($get['soloAnnotazioniAperte']) ? '1' : '0';
			
			
            global $utente;
            $defautValueAssegnate = 'off';
            //   ($utente->LivelloUtente!=null && $utente->LivelloUtente!='amministratore')
            //        ? 'on'
            //        : 'off';
            $params['soloAssegnate'] =  (isset($get['soloAssegnate']) && $get['soloAssegnate']!='')
                ? $get['soloAssegnate'] : $defautValueAssegnate;

            return $params;
        }
		
				//Funzione custom causa campo POINT in DB
		public function getById($id){
			global $connection_dbMeteo;
			$sql = 'SELECT *, X(CoordUTM) as UTM_EST, Y(CoordUTM) as UTM_NORD FROM A_Sensori
					where A_Sensori.IDsensore = :id';
			$pdo = $connection_dbMeteo->getConnectionObject();
			$statement = $pdo->prepare($sql);
			$statement->bindParam(':id', $id, pdo::PARAM_INT);
			$statement->execute();
			$res = $statement->fetchAll();
			$this->List = $res;
			return $res;
		}
		
				//overwrite per coordUTM
		public function save($post, $dt = ''){
			$post['CoordUTM'] = "PointFromText('POINT(" . $post['UTM_Est'] ." ". $post['UTM_Nord'] . ")')";
			unset($post['UTM_Est']);
			unset($post['UTM_Nord']);
			parent::save($post);
		}

        public function getByParams($params, $columns='ALL'){
            if($columns=='ALL'){
                $sql = 'SELECT A_Sensori.IDsensore, 
                		A_Sensori.Aggregazione AS Aggregazione, 
                		A_Sensori.IDstazione, 
                		A_Sensori.NOMEtipologia, 
                		A_Sensori.DataInizio, 
                		A_Sensori.DataFine, 
                		A_Sensori.QuotaSensore, 
                		A_Sensori.QSedificio, 
                		A_Sensori.QSsupporto, 
                		A_Sensori.NoteQS, 
                		A_Sensori.Storico, 
                		A_Sensori.Importato, 
                		A_Sensori.AggregazioneTemporale, 
                		A_Sensori.NoteAT, 
                		A_Sensori.Autore, 
                		A_Sensori.Data, 
                		A_Sensori.IDutente, 
                		AsText(A_Sensori.CoordUTM) as CoordUTM,
						A_Stazioni.IDstazione, 
                		A_Stazioni.NOMEstazione, 
                		A_Stazioni.NOMEweb, 
                		A_Stazioni.NOMEhydstra, 
                		A_Stazioni.CGB_Nord, 
                		A_Stazioni.CGB_Est, 
                		A_Stazioni.lat, 
                		A_Stazioni.lon, 
                		A_Stazioni.UTM_Nord, 
                		A_Stazioni.UTM_Est, 
                		A_Stazioni.Quota, 
                		A_Stazioni.IDrete, 
                		A_Stazioni.Localita, 
                		A_Stazioni.Attributo, 
                		A_Stazioni.Comune, 
                		A_Stazioni.Provincia, 
                		A_Stazioni.ProprietaStazione, 
                		A_Stazioni.ProprietaTerreno, 
                		A_Stazioni.Manutenzione, 
                		A_Stazioni.NoteManutenzione, 
                		A_Stazioni.Allerta, 
                		A_Stazioni.AOaib, 
                		A_Stazioni.AOneve, 
                		A_Stazioni.AOvalanghe, 
                		A_Stazioni.LandUse, 
                		A_Stazioni.PVM, 
                		A_Stazioni.UrbanWeight, 
                		A_Stazioni.DataLogger, 
                		A_Stazioni.NoteDL, 
                		A_Stazioni.Connessione, 
                		A_Stazioni.NoteConnessione, 
                		A_Stazioni.Fiduciaria, 
                		A_Stazioni.Alimentazione, 
                		A_Stazioni.NoteAlimentazione, 
                		A_Stazioni.Autore, 
                		A_Stazioni.Data, 
                		A_Stazioni.IDutente, 
                		AsText(A_Stazioni.CoordUTM) as CoordUTM, 
                		A_Stazioni.Fiume, 
                		A_Stazioni.Bacino 
                          FROM A_Sensori
                            LEFT JOIN A_Stazioni ON A_Stazioni.IDstazione=A_Sensori.IDstazione';
           } elseif($columns=='TABLELIST') {
                $sql = 'SELECT IDsensore, A_Sensori.Aggregazione AS Aggregazione, A_Sensori.IDstazione, NOMEtipologia,
                                IDrete,
								Provincia, Comune, Attributo, NOMEstazione,
								Allerta,
								DataInizio, DataFine,
								QuotaSensore, Qsedificio, Qssupporto, NoteQS,
								Storico, Importato,
								AggregazioneTemporale, NoteAT,
								A_Sensori.Autore, A_Sensori.Data
                          FROM A_Sensori
                            LEFT JOIN A_Stazioni ON A_Stazioni.IDstazione=A_Sensori.IDstazione';
            } elseif($columns="TABLELIST_TICKET"){
				$sql = 'SELECT A_Sensori.IDsensore, A_Sensori.Aggregazione AS Aggregazione, A_Sensori.IDstazione, A_Sensori.NOMEtipologia, A_Sensori.DataInizio, A_Sensori.DataFine, A_Sensori.QuotaSensore, A_Sensori.QSedificio, A_Sensori.QSsupporto, A_Sensori.NoteQS, A_Sensori.Storico, A_Sensori.Importato, A_Sensori.AggregazioneTemporale, A_Sensori.NoteAT, A_Sensori.Autore, A_Sensori.Data, A_Sensori.IDutente, AsText(A_Sensori.CoordUTM) as CoordUTM,
				A_Stazioni.IDstazione, A_Stazioni.NOMEstazione, A_Stazioni.NOMEweb, A_Stazioni.NOMEhydstra, A_Stazioni.CGB_Nord, A_Stazioni.CGB_Est, A_Stazioni.lat, A_Stazioni.lon, A_Stazioni.UTM_Nord, A_Stazioni.UTM_Est, A_Stazioni.Quota, A_Stazioni.IDrete, A_Stazioni.Localita, A_Stazioni.Attributo, A_Stazioni.Comune, A_Stazioni.Provincia, A_Stazioni.ProprietaStazione, A_Stazioni.ProprietaTerreno, A_Stazioni.Manutenzione, A_Stazioni.NoteManutenzione, A_Stazioni.Allerta, A_Stazioni.AOaib, A_Stazioni.AOneve, A_Stazioni.AOvalanghe, A_Stazioni.LandUse, A_Stazioni.PVM, A_Stazioni.UrbanWeight, A_Stazioni.DataLogger, A_Stazioni.NoteDL, A_Stazioni.Connessione, A_Stazioni.NoteConnessione, A_Stazioni.Fiduciaria, A_Stazioni.Alimentazione, A_Stazioni.NoteAlimentazione, A_Stazioni.Autore, A_Stazioni.Data, A_Stazioni.IDutente, AsText(A_Stazioni.CoordUTM) as CoordUTM, A_Stazioni.Fiume, A_Stazioni.Bacino,
							A_Monitoraggio.Note, A_Monitoraggio.DataInizio, A_Monitoraggio.IDticket, A_Ticket.DataApertura, Utenti.Cognome 
                          FROM A_Sensori
                            LEFT JOIN A_Stazioni ON A_Stazioni.IDstazione=A_Sensori.IDstazione JOIN A_Monitoraggio ON A_Monitoraggio.IDsensore = A_Sensori.IDsensore
							INNER JOIN A_Ticket ON A_Ticket.IDticket = A_Monitoraggio.IDticket 
							LEFT JOIN StazioniAssegnate ON StazioniAssegnate.IDstazione = A_Stazioni.IDstazione 
							LEFT JOIN Utenti ON Utenti.IDutente = StazioniAssegnate.IDUtente';
				//$params['soloAnnotazioniAperte']=='1';
			}
            // condizioni
            $sql .= $this->setQueryConditions($params);
            // ordinamento
            $sql .= ' ORDER BY NOMEstazione';
            // Esegue query
            return $this->getBySQLQuery($sql);
        }

            private function setQueryConditions($params){
                $where = ' WHERE A_Sensori.IDsensore IS NOT NULL ';
                // ## filtra per ID ##
                if(isset($params['ids'])) {
                    $where .= " AND A_Sensori.IDsensore IN (".$params['ids'].")";
                }
                //  ## filtra per regione ##
                if($params['regione']=="lombardia"){
                    $where .= " AND A_Stazioni.IDrete<>'5'";
                } else if($params['regione']=="extra"){
                    $where .= " AND A_Stazioni.IDrete='5'";
                }
                //  ## filtra per provincia ##
                if($params['provincia']!='ALL'){
                    $where .= " AND A_Stazioni.Provincia='".$params['provincia']."'";
                }
                //  ## filtra per rete ##
                if($params['rete']!='ALL'){
                    switch($params['rete']){
                        case "INM":
                            $where .= " AND (A_Stazioni.IDrete='4'
                                                OR A_Stazioni.IDrete='7'
                                                OR A_Stazioni.IDrete='8'
                                                OR A_Stazioni.IDrete='9'
                                                OR A_Stazioni.IDrete='10')";
                            break;
                        case "CMG":
                            $where .= " AND A_Stazioni.IDrete='2'";
                            break;
                        case "RRQA":
                            $where .= " AND A_Stazioni.IDrete='1'";
                            break;
                        case "Altro":
                            $where .= " AND A_Stazioni.IDrete='6'";
                            break;
                    }
                }
                //  ## filtra per tipologia ##
                if($params['tipologia']!=''){
                    switch($params['tipologia']){
						case "--":
							break;
                        case "PP-PPR":
                            $where .= " AND (NOMEtipologia='PP' OR NOMEtipologia='PPR') ";
                            break;
                        case "T-TV":
                            $where .= " AND (NOMEtipologia='T' OR NOMEtipologia='TV') ";
                            break;
                        case "DV-DVQ":
                            $where .= " AND (NOMEtipologia='DV' OR NOMEtipologia='DVQ') ";
                            break;
                        case "VV-VVQ":
                            $where .= " AND (NOMEtipologia='VV' OR NOMEtipologia='VVQ') ";
                            break;
                        default:
                            $where .= " AND NOMEtipologia='".$params['tipologia']."'";
                            break;
                    }
                }
                //  ## filtra per allerta ##
                if($params['allerta']!='ALL'){
                    $where .= " AND A_Stazioni.Allerta='".$params['allerta']."'";
                }
                // ## filtra per quota ##
                if($params['quotaDa']=='' && $params['quotaA']!=''){
                    $where .= " AND A_Sensori.QuotaSensore<='".$params['quotaA']."'";
                }
                if($params['quotaDa']!='' && $params['quotaA']==''){
                    $where .= " AND A_Sensori.QuotaSensore>='".$params['quotaDa']."'";
                }
                if($params['quotaDa']!='' && $params['quotaA']!=''){
                    $where .= " AND A_Sensori.QuotaSensore BETWEEN '".$params['quotaDa']."' AND '".$params['quotaA']."'";
                }
                //  ## filtra per stazione ##
                if(isset($params['IDstazione'])){
                    $where .= " AND A_Sensori.IDstazione='".$params['IDstazione']."'";
                }
                // ## escludi sensori storiche ##
                if(!isset($params['sensoriStorici']) || $params['sensoriStorici']=='0'){
                    $where .= "AND Storico!= 'Yes'";
                }
                // ## solo in lista nera ##
                if($params['soloListaNera']=='1'){
                    $this->getSensoriListaNera();
                    $where .= 'AND A_Sensori.IDsensore IN ('.implode(',', $this->sensoriListaNera).')';
                }
				// ## solo con annotazioni aperte ##
                if($params['soloAnnotazioniAperte']=='1'){
                    $this->getSensoriAnnotazioniAperte();
                    $where .= 'AND A_Sensori.IDsensore IN ('.implode(',', $this->sensoriAnnotazioniAperte).')';
                }
                // ## solo con ticket aperti ##
                if($params['soloTicketAperti']=='1'){
                    $this->getSensoriTicketAperti();
					if($this->sensoriTicketAperti != null && count($this->sensoriTicketAperti) > 0){
						$where .= 'AND A_Sensori.IDsensore IN ('.implode(',', $this->sensoriTicketAperti).')';
					} else {
						$where .= 'AND A_Sensori.IDsensore IN (-1)';
					}
					$where .= ' AND A_Monitoraggio.Chiusura = "NO"';
                }
                // ## solo Assegnate ##
                if($params['soloAssegnate']=='on'){
                    global $utente;
                    $where .= " AND A_Stazioni.IDstazione IN (
                                        SELECT IDstazione
                                        FROM StazioniAssegnate
                                        WHERE IDutente='".$utente->getID()."'
                                  )";
                }
                return $where;
            }
            
        public function getNomeTipologia(){
            return $this->List[0]['NOMEtipologia'];
        }

        public function getByStazione($IDstazione){
            return $this->getByField('IDstazione', $IDstazione);
        }

            public function getSensoriByStazione($IDstazione){
                $this->getByStazione($IDstazione);
                $array = array();
                foreach($this->List as $item){
                    $array[] = $item['IDsensore'];
                }
                return $array;
            }
			
			private function getSensoriAnnotazioniAperte(){
                if($this->sensoriAnnotazioniAperte==null){
                    $Annotazione = new Annotazione();
                    $this->sensoriAnnotazioniAperte = array_column($Annotazione->getIdSensoriAnnotazioniAperte(), 'IDsensore');
                }
            }

            private function getSensoriTicketAperti(){
                if($this->sensoriTicketAperti==null){
                    $ticketOBJ = new Ticket();
                    $this->sensoriTicketAperti = $ticketOBJ->getSensoriTicketAperti();
                }
            }

            private function getSensoriListaNera(){
                if($this->sensoriListaNera==null){
                    $listaNeraOBJ = new ListaNera();
                    $this->sensoriListaNera = $listaNeraOBJ->getSensoriInListaNera();
                }
            }

        protected function insert($post, $autoIncrementID=false,$returningID=false){
           return parent::insert($post, false);
        }

        public function printListTable($params){
			// ticket aperti
			$visualizzazioneTicket = $params['soloTicketAperti']=='1';
			if($visualizzazioneTicket){
				$numCol = 14;
			} else {
				$numCol = 20;
			}
            // Verifica che la lista richiesta non sia già in SESSION
            if(isset($_SESSION['sensori']['params']) && $params == $_SESSION['sensori']['params']){
                $this->List = $_SESSION['sensori']['lista'];
            } else {
				if($visualizzazioneTicket){
					$this->getByParams($params, 'TABLELIST_TICKET');
				} else {
					$this->getByParams($params, 'TABLELIST');
				}
                $_SESSION['sensori']['params'] = $params;
                $_SESSION['sensori']['lista'] = $this->List;
            }

            $numItems = count($this->List);
            
            $output = '<p style="text-align: left; background-color: #FFFFB8; border-width: 1px; border-style: solid; border-bottom-style: none; border-color: Black;"><i><span id="sensorsCount">' . $numItems . '</span> sensori trovati.</i></p>';
            
            $ListaNeraObj = new ListaNera();
            $listaNera = $ListaNeraObj->getSensoriInListaNera();
            unset($ListaNeraObj);

            $Annotazione = new Annotazione();
            //$idSensori = $Annotazione->getIdSensoriAnnotazioniAperte();
            $idSensori = $Annotazione->getIdSensoriAnnotazioniAperte();
            unset($Annotazione);
            $sensori = array_column($idSensori, 'IDsensore');
            $sm = array_column($idSensori, 'Metadato', 'IDsensore');
			
			$Ticket = new Ticket();
			$idSensoriTicketAperti = $Ticket->getSensoriTicketAperti();
			unset($Ticket);
			
			$output .= '<table id="listaSensori" name="listaSensori" class="lista tablesorter">';

            $output .= '<thead>
                            <tr>
                                <th class="filter-false sorter-false"></th>
                                <th class="filter-false sorter-false"></th>
                                <th>Rete</th>
                                <th>Provincia</th>
                                <th>Comune</th>
                                <th>Attributo</th>
                                <th>NOMEstazione</th>';
            if(!$visualizzazioneTicket){ $output .= '<th>Allerta</th>';}
            $output .= '<th>IDsensore</th>
                        <th>NOMEtipologia</th>';
            if(!$visualizzazioneTicket){
								$output .= '<th>DataInizio</th>
                                <th>DataFine</th>
                                <th>AggregazioneTemporale</th>
                                <th>PianoCampagna (QuotaSensore)</th>
                                <th>Qsedificio</th>
                                <th>Qssupporto</th>
                                <th>NoteQS</th>
                                <th>Storico</th>
                                <th>Importato</th>';
			}
			if($visualizzazioneTicket){
				$output .= '<th>Note</th>
                                <th>DataInizio</th>
                                <th>IDticket</th>
                                <th>Data apertura ticket</th>
								<th>Assegnatario</th>';
			}
			
            $output .=          '</tr>
                        </thead>';
            if($numItems>0){
                $output .= '<tbody>';
                $obj = new Rete();
                foreach($this->List as $record){
                    // verifica se storico
                    $storico = (isset($record['Storico']) && $record['Storico']=='Yes') ? '<span class="inStorici">storico</span>' : '';
                    // verifica se in Lista Nera
                    $inListaNera = in_array($record['IDsensore'], $listaNera) ? '<span class="inListaNera">lista nera</span>' : '';
                    // verifica se ha annotazioni aperte
                    if( in_array($record['IDsensore'], $sensori) )
                    {
                    	$metadata = is_null($sm[$record['IDsensore']]) ? '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' : $sm[$record['IDsensore']];
                    }
                    else
                    {
                    	$metadata = '';
                    }
                    //$haAnnotazioniAperte = in_array($record['IDsensore'], $idSensori) ? '<span class="annotazioniAperte">annotazioni</span>' : '';
                    $haAnnotazioniAperte = in_array($record['IDsensore'], $sensori) ? '<span class="annotazioniAperte">'.$metadata.'</span>' : '';
					// verifica se ha ticket aperti
					$haTicketAperti = in_array($record['IDsensore'], $idSensoriTicketAperti) ? '<span class="ticketAperti">ticket</span>' : '';
					
                    $output .= '<tr class="recordLista">';
                    
                    /* $output .= '<td class="action">
                                        '.HTML::getButtonAsLink($_SERVER['SCRIPT_NAME'].'?do=dettaglio&id='.$record['IDsensore'], 'Dettagli').'
                                    </td> */
                        $output .= '<td class="action">
                                        <a href="'.$_SERVER['SCRIPT_NAME'].'?do=dettaglio&id='.$record['IDsensore'] . '">Dettagli</a>
                                    </td>
                                    <td style="white-space:nowrap;">
                                        '.$storico.'
                                        '.$inListaNera.'
                                        '.$haAnnotazioniAperte.'
										'.$haTicketAperti.'
                                    </td>
                                    <td>' . (isset($record['IDrete']) ? htmlentities($obj->getNomeByID($record['IDrete'])) : '') . '</td>
                                    <td>' . (isset($record['Provincia']) ? htmlentities($record['Provincia']) : '') . '</td>
                                    <td>' . (isset($record['Comune']) ? htmlentities($record['Comune']) : '') . '</td>
                                    <td>' . (isset($record['Attributo']) ? htmlentities($record['Attributo']) : '') . '</td>
                                    <td><b>' . (isset($record['NOMEstazione']) ? htmlentities($record['NOMEstazione']) : '') . '</b></td>';
                    if(!$visualizzazioneTicket){ $output .= '<td>' . (isset($record['Allerta']) ? htmlentities($record['Allerta']) : '') . '</td>';}
                                    $output .= '<td><b class="idEntita">' . (isset($record['IDsensore']) ? $record['IDsensore'] : '') . '</b></td>
                                    <td><b>' . (isset($record['NOMEtipologia']) ? $record['NOMEtipologia'] : '') . (isset($record['Aggregazione']) ? ' - '.$record['Aggregazione'] : ''). '</b></td>';
                    if(!$visualizzazioneTicket){
						$output .= '<td>' . (isset($record['DataInizio']) ? $record['DataInizio'] : '') . '</td>
                                    <td>' . (isset($record['DataFine']) ? $record['DataFine'] : '') . '</td>
                                    <td>'
                                        . (isset($record['AggregazioneTemporale']) ? $record['AggregazioneTemporale'] : '') . ' '
                                        . (isset($record['NoteAT']) ? htmlentities($record['NoteAT']) : '') . '
                                    </td>
                                    <td>' . (isset($record['QuotaSensore']) ? $record['QuotaSensore'] : '') . '</td>
                                    <td>' . (isset($record['Qsedificio']) ? $record['Qsedificio'] : '') . '</td>
                                    <td>' . (isset($record['Qssupporto']) ? $record['Qssupporto'] : '') . '</td>
                                    <td>' . (isset($record['NoteQS']) ? htmlentities($record['NoteQS']) : '') . '</td>
                                    <td>' . (isset($record['Storico']) ? htmlentities($record['Storico']) : '') . '</td>
                                    <td>' . (isset($record['Importato']) ? $record['Importato'] : '') . '</td>';
					}
					if($visualizzazioneTicket){
						$output .= '<td>' . (isset($record['Note']) ? htmlentities($record['Note']) : '') . '</td>
                                    <td>' . (isset($record['DataInizio']) ? $record['DataInizio'] : '') . '</td>
                                    <td>' . (isset($record['IDticket']) ? $record['IDticket'] : '') . '</td>
                                    <td>' . (isset($record['DataApertura']) ? $record['DataApertura'] : '') . '</td>
									<td>' . (isset($record['Cognome']) ? htmlentities($record['Cognome']) : '') . '</td>';
					}
                    $output .=      '</tr>';
                }
                unset($obj);
                $output .= '</tbody>
                            <tr><th style="text-align: left; background-color: #FFFFB8;" colspan="'.$numCol.'"><i>'.$numItems.' sensori trovati.</i></th></tr>';
            } else {
                $output .= '<tr>'.str_repeat("<td></td>", $numCol).'</tr>
                            <tr><td style="text-align: center" colspan="'.$numCol.'">Nessun risultato.</td></tr>';

            }
            $output .= '</table>';
            return $output;
        }

        public function printCompactListTable(){

            $ListaNeraObj = new ListaNera();
            $listaNera = count($this->List) > 0 ? $ListaNeraObj->getSensoriInListaNeraByStazione($this->List[0]['IDstazione']) : null;
            unset($ListaNeraObj);

            $output = '<thead>
                            <tr>
                                <th></th>
                                <th></th>
                                <th>IDsensore</th>
                                <th>NOMEtipologia</th>
                                <th>Storico</th>
                                <th>Importato</th>
                            </tr>
                        </thead>';
            if(count($this->List)>0){
                $output .= '<tbody>';
				$separator = '';
                foreach($this->List as $record){
                    // verifica se in Lista Nera
                    $inListaNera = in_array($record['IDsensore'], $listaNera) ? '<span class="inListaNera">lista nera</span>' : '';
					$separator = $record['Aggregazione'] != null ? " - " : "";
                    $output .= '<tr>
                                    <td class="action">
                                        '.HTML::getButtonAsLink('sensori.php?do=dettaglio&id='.$record['IDsensore'], 'Dettagli').'
                                    </td>
                                    <td style="white-space:nowrap;">
                                        '.$inListaNera.'
                                    </td>
                                    <td>'.$record['IDsensore'].'</td>
                                    <td>'.$record['NOMEtipologia']. $separator . $record['Aggregazione'].'</td>
                                    <td>'.$record['Storico'].'</td>
                                    <td>'.$record['Importato'].'</td>
                                </tr>';
                }
                $output .= '</tbody>';
            } else {
                $numCol = 6;
                $output .= '<tr><td style="text-align: center" colspan="'.$numCol.'">Nessun risultato.</td></tr>';

            }
            return $output;
        }

        public function printSummaryTable(){
            $item = $this->List[0];
	    $aggregazioneRow = '';
	    if($item['Aggregazione'] != null && $item['Aggregazione'] != ''){
	    	$aggregazioneRow = '<tr><td>Aggregazione</td><td>'.$item['Aggregazione'].'</td></tr>';
	    }
            $output = '<table id="tabellaDettaglio" class="summary" style="margin: 5px 5px 5px 0px;">
                            <thead>
                                <tr>
                                    <td>'.$this->IDfield.'</td>
                                    <th>'.$item[$this->IDfield].'</th>
                                </tr>
                                <tr>
                                    <td>NOMEtipologia</td>
                                    <th>'.$item['NOMEtipologia'].'</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr><td>DataInizio</td><td>'.$item['DataInizio'].'</td></tr>
                                <tr><td>DataFine</td><td>'.$item['DataFine'].'</td></tr>' .
				$aggregazioneRow .
                                '<tr><td>AggregazioneTemporale</td><td>'.
                                                        $item['AggregazioneTemporale'].
                                                        (($item['NoteAT']!=='')
                                                            ? '<br /><i>Note: '.$item['NoteAT'].'</i>'
                                                            : '')
                                                        .'</td></tr>

                                <tr><td>UTM_Nord</td><td>' . $item['UTM_NORD'] . '</td></tr>
                                <tr><td>UTM_Est</td><td>' . $item['UTM_EST'] . '</td></tr>
								<tr><td>PianoCampagna (QuotaSensore)</td><td>'.$item['QuotaSensore'].'</td></tr>
                                <tr><td>QSedificio</td><td>'.$item['QSedificio'].'</td></tr>
                                <tr><td>QSsupporto</td><td>'.
                                                                $item['QSsupporto'].
                                                                (($item['NoteQS']!=='')
                                                                    ? '<br /><i>Note: '.$item['NoteQS'].'</i>'
                                                                    : '')
                                                                .'</td></tr>

                                <tr><td>Storico</td><td>'.$item['Storico'].'</td></tr>
                                <tr><td>Importato</td><td>'.$item['Importato'].'</td></tr>
                                <tr><td><i>Ultima Modifica</i></td>
                                    <td>'.$this->getAutore($item['IDutente'],$item['Data']).'</td>
                                </tr>';

            $output .= '    </tbody>
                        </table>';
            return $output;
        }

        public function printEditForm($IDstazione){
            $item = count($this->List) > 0 ? $this->List[0] : null;
			if($IDstazione == null){ $IDstazione = $item['IDstazione'];}
            return '<table id="tabellaModifica" class="summary">
                            <thead>
                                    <tr>
                                        <td>IDstazione</td>
                                        <td>'.Stazione::getListaDropdown($IDstazione).'</td>
                                    </tr>
                                    <tr>
                                        <td>IDsensore</td>
                                        <td>
                                            <input type="text" id="IDsensore" name="IDsensore" value="'.$item['IDsensore'].'" />
                                        </td>
                                    </tr>
                            </thead>
                            <tbody>
                                <tr><td>NOMEtipologia</td><td>'.        Tipologia::dropdownListNOMEtipologia('NOMEtipologia', $item['NOMEtipologia']).'</td></tr>
                                <tr><td>DataInizio</td><td>'.		    '<input type="text" id="DataInizio" name="DataInizio" value="'.$item['DataInizio'].'" />'.'</td></tr>
                                <tr><td>DataFine</td><td>'.		        '<input type="text" id="DataFine" name="DataFine" value="'.$item['DataFine'].'" />'.'</td></tr>
								<tr><td>Aggregazione</td><td><select name="Aggregazione">
									<option></option>
									<option value="V" '.(($item["Aggregazione"] == "V") ? 'selected' : '').'>V</option>
									<option value="S" '.(($item["Aggregazione"] == "S") ? 'selected' : '').'>S</option>
									<option value="P" '.(($item["Aggregazione"] == "P") ? 'selected' : '').'>P</option>
								</select>'.'</td></tr>
                                <tr><td>AggregazioneTemporale</td><td>'.'<input type="text" id="AggregazioneTemporale" name="AggregazioneTemporale" value="'.$item['AggregazioneTemporale'].'" />'.'</td></tr>
                                <tr><td>NoteAT</td><td>'.		        '<input type="text" id="NoteAT" name="NoteAT" value="'.$item['NoteAT'].'" />'.'</td></tr>
                                <tr><td>UTM_Nord</td><td>' . '<input type="text" id="CGB_Nord" name="UTM_Nord" value="' . $item['UTM_NORD'] . '" />' . '</td></tr>
								<tr><td>UTM_Est</td><td>' . '<input type="text" id="CGB_Est" name="UTM_Est" value="' . $item['UTM_EST'] . '" />' . '</td></tr>
								<tr><td>PianoCampagna (QuotaSensore)</td><td>'.		    '<input type="text" id="QuotaSensore" name="QuotaSensore" value="'.$item['QuotaSensore'].'" />'.'</td></tr>
                                <tr><td>QSedificio</td><td>'.		    '<input type="text" id="QSedificio" name="QSedificio" value="'.$item['QSedificio'].'" />'.'</td></tr>
                                <tr><td>QSsupporto</td><td>'.		    '<input type="text" id="QSsupporto" name="QSsupporto" value="'.$item['QSsupporto'].'" />'.'</td></tr>
                                <tr><td>NoteQS</td><td>'.		        '<input type="text" id="NoteQS" name="NoteQS" value="'.$item['NoteQS'].'" />'.'</td></tr>
                                <tr><td>Storico</td><td>'.		        '<select id="Storico" name="Storico">
                                                                            <option value=""> - - </option>
                                                                            <option value="Yes" '.(($item['Storico']=="Yes") ? 'selected="selected"' : '').'>Si</option>
                                                                            <option value="No" '.(($item['Storico']=="No") ? 'selected="selected"' : '').'>No</option>
                                                                         </select>'.'</td></tr>
                                <tr><td>Importato</td><td>'.		    '<select id="Importato" name="Importato">
                                                                            <option value=""> - - </option>
                                                                            <option value="Yes" '.(($item['Importato']=="Yes") ? 'selected="selected"' : '').'>Si</option>
                                                                            <option value="No" '.(($item['Importato']=="No") ? 'selected="selected"' : '').'>No</option>
                                                                         </select>'.'</td></tr>
                            </tbody>
					   </table>';
        }

        public static function dropdownListNOMEtipologia($listD, $selectedItem){
            $output = '<select id="'.$listD.'" name="'.$listD.'">';
                $listTypes = array(
		    array('color'=>'LightGrey',	    'types'=>array('--')),
		    array('color'=>'White',         'types'=>array('BF', 'T0', 'T10', 'T15', 'T30', 'T50', 'T75')),
		    array('color'=>'Red',           'types'=>array('FM', 'FT', 'HM', 'LM', 'LT')),
		    array('color'=>'LightSkyBlue',  'types'=>array('I', 'IP', 'PO', 'Q', 'R', 'TA', 'TC', 'TD', 'TLT')),
		    array('color'=>'Yellow',        'types'=>array('B', 'DV', 'PA', 'PP', 'RG', 'T', 'UR', 'VV')),
		    array('color'=>'Green',         'types'=>array('N', 'RN', 'RR', 'RU')),
		    array('color'=>'Pink',          'types'=>array('TP', 'TPN', 'TPP', 'TPV')),
		    array('color'=>'Orange',        'types'=>array('DVS', 'FC', 'H0', 'SGT', 'SGX', 'SGY', 'SGZ', 'TKE', 'TS','TST','UST','VVS','ZL'))
                );
                foreach($listTypes as $group){
                    $color = $group['color'];
                    foreach($group['types'] as $item){
                        $output .= '<option value="'.$item.'" ';
                        $output .= 'style="background-color: '.$color.'" ';
                        $output .= ($item==$selectedItem) ? 'selected="selected" ' : '';
                        $output .= '>'.($item!='' ? $item : 'Tutte').'</option>';
                    }
                }
            $output .= '</select>';
            return $output;
        }

    }
