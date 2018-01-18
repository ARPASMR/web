<?php

    class Annotazione extends GenericEntity{
		
		const tableName = 'A_Monitoraggio';
		const idFieldName = "IDannotazione"; 
		
        function __construct(){
            $this->DBTable = Annotazione::tableName;
            $this->IDfield = Annotazione::idFieldName;
            parent::__construct();
        }
		
		// public function isInListaNera(){
			// $item = count($this->List) > 0 ? $this->List[0] : NULL;
			// if($item == null){return;}
			// $sql = 'SELECT COUNT(*) FROM A_ListaNera WHERE IDsensore IN (SELECT IDsensore FROM A_Monitoraggio WHERE Stazione=\''.$item['Stazione'].'\' AND DataInizio=\''.$item['DataInizio'].'\' AND Note=\''.$item['Note'].'\') AND DataFine IS NULL';
			// global $connection_dbMeteo;
			// $pdo = $connection_dbMeteo->getConnectionObject();
			// $statement = $pdo->prepare($sql);
			// $statement->execute();
			// $res = $statement->fetchAll();
			// return $res[0][0] > 0;
		// }
		
		public function aggiungiInListaNera(){
			global $utente;
			$item = count($this->List) > 0 ? $this->List[0] : NULL;
			if($item == null){return;}
			$sql = 'INSERT INTO A_ListaNera (IDsensore, DataInizio, Autore, Data, IDutente) VALUES (:idSensore, :dataInizio, :autore, :data, :idUtente )';
			global $connection_dbMeteo;
			$pdo = $connection_dbMeteo->getConnectionObject();
			$statement = $pdo->prepare($sql);
			$statement->execute(array(':idSensore'=>$item['IDsensore'],':dataInizio'=>$item['DataInizio'], ':autore'=>$utente->Acronimo, ':data'=>date('Y-m-d H:i:s'),':idUtente'=>$utente->IDutente));
		}
		
		public function rimuoviDaListaNera(){
			global $utente;
			$item = count($this->List) > 0 ? $this->List[0] : NULL;
			if($item == null){return;}
			$sql = 'UPDATE A_ListaNera SET DataFine=:dataFine,Data=:data WHERE IDsensore=:idSensore AND DataFine IS NULL';
			global $connection_dbMeteo;
			$pdo = $connection_dbMeteo->getConnectionObject();
			$statement = $pdo->prepare($sql);
			$statement->execute(array(':idSensore'=>$item['IDsensore'],':data'=>date('Y-m-d H:i:s'),':dataFine'=>$item['DataFine']));
		}
		
		public static function deleteByIds($ids){
			$idsString = '';
			foreach($ids as $id){
				$idsString .= $id.',';
			}
			$idsString = rtrim($idsString, ",");
			
			global $connection_dbMeteo;
			$pdo = $connection_dbMeteo->getConnectionObject();
			$selectIdTickets = 'SELECT '.Ticket::idFieldName.' FROM ' .Annotazione::tableName.' WHERE '.Annotazione::idFieldName.' IN('.$idsString.')';
			$statement = $pdo->prepare($selectIdTickets);
			$statement->execute();
			$res = $statement->fetchAll();
			$ticketIds = array();
			foreach($res as $ticket){
				array_push($ticketIds, $ticket[0]);
			}
			$prefix = $idsTicketList = '';
			foreach ($ticketIds as $ticketId)
			{
				$idsTicketList .= $prefix . '"' . $ticketId . '"';
				$prefix = ', ';
			}
			$sqldeleteTickets = 'DELETE FROM '. Ticket::tableName .' WHERE '.Ticket::idFieldName.' IN('.$idsTicketList.')';
			$statement = $pdo->prepare($sqldeleteTickets);
			$statement->execute();
			
			$sql = 'DELETE FROM '. Annotazione::tableName .' WHERE '.Annotazione::idFieldName.' IN('.$idsString.')';
			$statement = $pdo->prepare($sql);
			$statement->execute();
		}

        public function getBySensore($IDsensore){
            $sql = 'SELECT * FROM '.$this->DBTable.'
                        WHERE IDsensore=\''.$IDsensore.'\'
                    ORDER BY Data DESC';
            return $this->getBySQLQuery($sql);
        }

        public function getByStazione($IDstazione){
            $sql = 'SELECT * FROM '.$this->DBTable.'
                        WHERE IDSensore IN
                            (SELECT IDSensore FROM A_Sensori WHERE IDstazione=\''.$IDstazione.'\')
                    ORDER BY Data DESC;';
            return $this->getBySQLQuery($sql);
        }

        public function getIdSensoriAnnotazioniAperte(){
            $this->List = $this->getByField('Chiusura', 'No');
            $array=array();
            foreach($this->List as $item){
                $array[] = $item['IDsensore'];
            }
            $this->List = array();
            return $array;
        }
		
		public function getIdsAnnotazioneByIdTicket($idTicket){
			$this->List = $this->getByField('IDticket', $idTicket);
            $array=array();
            foreach($this->List as $item){
                $array[] = $item['IDannotazione'];
            }
            $this->List = array();
            return $array;
		}

        public function getStazioniAnnotazioniAperte(){
            $sql = "SELECT DISTINCT A_Stazioni.IDstazione
                        FROM ".$this->DBTable."
                        JOIN A_Sensori ON A_Sensori.IDsensore = ".$this->DBTable.".IDsensore
                        JOIN A_Stazioni ON A_Sensori.IDstazione = A_Stazioni.IDstazione
                        WHERE Chiusura='No'
                        ;";
            $this->getBySQLQuery($sql);
            $array=array();
            foreach($this->List as $item){
                $array[] = $item['IDstazione'];
            }
            $this->List = array();
            return $array;
        }
		
		public function setIDticketAndSave($idTicket){
			$item = count($this->List) > 0 ? $this->List[0] : NULL;
			if($item == null){return;}
			$sql = 'UPDATE '. $this->DBTable .' SET IDticket='.$idTicket.' WHERE '.$this->IDfield.'='.$item[$this->IDfield];
			global $connection_dbMeteo;
			$pdo = $connection_dbMeteo->getConnectionObject();
			$statement = $pdo->prepare($sql);
			$statement->execute();
		}

        public function printListTable($IDstazione=''){

            global $utente;
            $numList = count($this->List);

            $output = '<thead>
                            <tr>
                                <th class="sorter-false"></th>
                                <th>IDsensore</th>
                                <th>Stazione</th>
                                <th>Note</th>
								<th>Data inizio</th>
								<th>Data fine</th>
                                <th>Metadato</th>
                                <th>Chiusura</th>
                                <th style="width: 100px;">Ultima Modifica</th>
								<th>IDticket</th>
								<th>Data apertura ticket</th>
								<th>Data chiusura ticket</th>
								<th>Priorità</th>
                            </tr>
                        </thead>';

            if($numList>0){
		
                $list = array();
                $j=0;
				$ticket = new Ticket();
                for($i=0; $i<$numList; $i++){
                    if(isset($list[$j]) 					
                        && $list[$j]['Note']==$this->List[$i]['Note']		
						&& $list[$j]['Stazione'] == 'SI' && $this->List[$i]['Stazione'] == 'SI'
						&& $list[$j]['Data'] == $this->List[$i]['Data']
                    ){
                        $list[$j]['IDannotazione'] .= ','.$this->List[$i]['IDannotazione'];
                    } else {
                        $j++;
                        $list[$j]['IDannotazione'] = $this->List[$i]['IDannotazione'];
                        $list[$j]['Stazione'] = $this->List[$i]['Stazione'];
                        $list[$j]['IDsensore'] = $list[$j]['Stazione']=='NO' ? $this->List[$i]['IDsensore'] : '';
                        $list[$j]['Note'] = $this->List[$i]['Note'];
						$list[$j]['DataInizio'] = $this->List[$i]['DataInizio'];
						$list[$j]['DataFine'] = $this->List[$i]['DataFine'];
                        $list[$j]['Metadato'] = $this->List[$i]['Metadato'];
                        $list[$j]['Chiusura'] = $this->List[$i]['Chiusura'];
                        $list[$j]['IDutente'] = $this->List[$i]['IDutente'];
                        $list[$j]['Data'] = $this->List[$i]['Data'];
                    }
					$list[$j]['Ticket'] = reset($ticket->getByIDannotazione($list[$j]['IDannotazione']));
                }
		
                $output .= '<tbody>';
                foreach($list as $item){
					$dataInizio = $item['DataInizio'] <> '' ? date_create($item['DataInizio'])->format('Y-m-d H:i') : '';
					$dataFine = $item['DataFine'] <> '' ? date_create($item['DataFine'])->format('Y-m-d H:i') : '';
					$dataApertura = isset($item['Ticket']['DataApertura']) && $item['Ticket']['DataApertura'] <> '' ? date_create($item['Ticket']['DataApertura'])->format('Y-m-d') : '';
					$dataChiusura = isset($item['Ticket']['DataChiusura']) && $item['Ticket']['DataChiusura'] <> '' ? date_create($item['Ticket']['DataChiusura'])->format('Y-m-d') : '';
                    $editURL = ($item['Stazione']=='SI')
                                    ? '&id='.$item['IDannotazione'].'&IDstazione='.$IDstazione
                                    : '&id='.$item['IDannotazione'].'&IDsensore='.$item['IDsensore'];
					if($item['Chiusura']=='NO'){
						if($dataApertura != '' && $dataChiusura== ''){
							$styleAperto = 'class="ticketAperti"';
						} else {$styleAperto = 'class="annotazioneAperta"';}
					} else {$styleAperto = '';}
                    
                    $output .= '<tr '.$styleAperto.'>
                                    <td class="action">'
                                        .(($utente->LivelloUtente=="amministratore" || $utente->LivelloUtente=="gestoreDati")
                                            ? HTML::getButtonAsLink('ticket.php?do=modifica'.$editURL, 'Modifica').HTML::getButtonAsLink('ticket.php?do=elimina'.$editURL,'Elimina')
                                            : '').'
                                    </td>
                                    <td>'.$item['IDsensore'].'</td> 			
                                    <td>'.$item['Stazione'].'</td>
                                    <td>'.$item['Note'].'</td>
				    <td>'.$dataInizio.'</td>
				    <td>'.$dataFine.'</td>
                                    <td>'.$item['Metadato'].'</td>
                                    <td>'.$item['Chiusura'].'</td>
                                    <td>'.$this->getAutore($item['IDutente'],$item['Data']).'</td>
									<td>'.$item['Ticket']['IDticket'].'</td>
									<td>'.$item['Ticket']['DataApertura'].'</td>
									<td>'.$item['Ticket']['DataChiusura'].'</td>
									<td>'.$item['Ticket']['Priorita'].'</td>
                                </tr>';
                }
                $output .= '</tbody>';
            } else {
                $output = '<tr><td style="text-align: center" colspan="8">Nessuna annotazione.</td></tr>';
            }
            return $output;
        }

        public function printEditForm($IDannotazione, $IDsensore='', $IDstazione=''){
	    //$disabledString = $IDannotazione != null ? 'disabled' : '';
        $item = count($this->List) > 0 ? $this->List[0] : null;
	    $dataInizio = $item['DataInizio'] <> null ? date_create($item['DataInizio'])->format('Y-m-d H:i') : "";
	    $dataFine = $item['DataFine'] <> '' ? date_create($item['DataFine'])->format('Y-m-d H:i') : '';
		$isInListaNera;
		$listaNera = new ListaNera();
		if($IDsensore != ''){
			$isInListaNera = $listaNera->isSensoreInListaNera($IDsensore);
		} else if($IDstazione != ''){
			$isInListaNera = count($listaNera->getSensoriInListaNeraByStazione($IDstazione)) > 0;
		}
		$giaInListaNeraString = $isInListaNera ? "Sensore gi&agrave; presente in lista nera." : "";
            $output = '<input type="hidden" name="'.$this->IDfield.'" value="'.$IDannotazione.'" />
                        <table id="tabellaModifica" class="summary">
                            <thead>';
            if($IDsensore!=''){
                $output .= '<tr>
                                <th>IDsensore</th>
                                <th>'.$IDsensore.'</th>
                                <input type="hidden" name="IDsensore" value="'.$IDsensore.'" />
                                <input type="hidden" name="Stazione" value="NO" />
                            </tr>';
            } else if($IDstazione!=''){
                $output .= '<tr>
                                <td>IDstazione</td>
                                <th>'.$IDstazione.'</th>
                                <input type="hidden" name="IDstazione" value="'.$IDstazione.'" />
                                <input type="hidden" name="Stazione" value="SI" />
                            </tr>';
            }
            $output .= '    </thead>
                            <tbody>
                                <tr><td>Annotazione</td><td>'.'<textarea id="Note" name="Note">'.$item['Note'].'</textarea></td></tr>
								<tr><td>Aggiungi in lista nera</td><td><input name="inListaNera" type="checkbox" style="width:3em"/><label><u>'.$giaInListaNeraString.'<u></label></td></tr>
								<tr><td>Metadato</td><td>
								<select id="attivitaSelect" name="Metadato">
									<option></option>
									<option value="Manutenzione Ordinaria" '.(($item['Metadato'] == "Manutenzione Ordinaria") ? 'selected="selected"' : '').'>Manutenzione Ordinaria</option>
									<option value="Intervento" '.(($item['Metadato'] == "Intervento") ? 'selected="selected"' : '').'>Intervento</option>
									<option value="Verifica" '.(($item['Metadato'] == "Verifica") ? 'selected="selected"' : '').'>Verifica</option>
									<option value="Calibrazione" '.(($item['Metadato'] == "Calibrazione") ? 'selected="selected"' : '').'>Calibrazione</option>
									<option value="Sostituzione Sensore" '.(($item['Metadato'] == "Sostituzione Sensore") ? 'selected="selected"' : '').'>Sostituzione sensore</option>
									<option value="Evolutiva" '.(($item['Metadato'] == "Evolutiva") ? 'selected="selected"' : '').'>Evolutiva</option>
								</select>
								</td></tr>
				<tr><td>Data inizio</td><td><input id="DataInizio" name="DataInizio" value="'.$dataInizio.'" class="" required></td></tr>
				<tr><td>Data fine</td><td><input id="DataFine" name="DataFine" value="'.$dataFine.'" class="" '. ($item['Chiusura'] == "NO" ? "disabled" : "") .'></td></tr>
                                <tr><td>Risolto</td>
                                    <td>
                                        <select id="Chiusura" name="Chiusura" value="'.$item['Chiusura'].'">
                                            <option value="NO" '.(($item['Chiusura']=="NO") ? 'selected="selected"' : '').'>NO</option>
                                            <option value="SI" '.(($item['Chiusura']=="SI") ? 'selected="selected"' : '').'>SI</option>
                                        </select>
                                    </td>
                                </tr>
                                </tr>
                                </tbody>
                       </table>';
            return $output;
        }

        public function printEditFormInListaNera($aggiuntaInListaNera=true){
            return '<table id="tabellaModifica" class="summary">
                            <tbody>
                                <tr><td>Note</td><td>'.'<textarea id="Note" name="Note"></textarea></td></tr>
                                <tr><td>Risolto</td>
                                    <td>
                                        <select id="Chiusura" name="Chiusura">
                                            <option value="NO" '.( $aggiuntaInListaNera===true ? 'selected="selected"' : '').'>NO</option>
                                            <option value="SI" '.( $aggiuntaInListaNera===false ? 'selected="selected"' : '').'>SI</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr><td>Metadato</td>
                                    <td>
                                        <select id="Metadato" name="Metadato">
                                            <option value="No">NO</option>
                                            <option value="Yes">SI</option>
                                        </select>
                                    </td>
                                </tr>
                                </tbody>
                       </table>';
        }

    }