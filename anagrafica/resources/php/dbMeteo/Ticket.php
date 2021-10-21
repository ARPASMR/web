<?php
	class Ticket extends GenericEntity{
		
		const tableName = 'A_Ticket';
		const idFieldName = 'IDticket';
		function __construct(){
			$this->DBTable = Ticket::tableName;
			$this->IDfield = Ticket::idFieldName;
			parent::__construct();
		}
		
		static function getStazioniTicketAperti(){
			global $connection_dbMeteo;
			$sql = 'SELECT DISTINCT A_Sensori.IDstazione FROM A_Sensori
					JOIN A_Monitoraggio ON(A_Sensori.IDsensore=A_Monitoraggio.IDsensore)
					JOIN A_Ticket ON(A_Ticket.IDticket=A_Monitoraggio.IDticket)
					where A_Ticket.DataChiusura IS NULL';
			$pdo = $connection_dbMeteo->getConnectionObject();
			$statement = $pdo->prepare($sql);
			$statement->execute();
			$res = $statement->fetchAll();
			$array=array();
            foreach($res as $item){
                $array[] = $item['IDstazione'];
            }
            return $array;
		}
		
		static function getSensoriTicketAperti(){
			global $connection_dbMeteo;
			$sql = 'SELECT DISTINCT A_Sensori.IDsensore FROM A_Sensori
					JOIN A_Monitoraggio ON(A_Sensori.IDsensore=A_Monitoraggio.IDsensore)
					JOIN A_Ticket ON(A_Ticket.IDticket=A_Monitoraggio.IDticket)
					where A_Ticket.DataChiusura IS NULL';
			$pdo = $connection_dbMeteo->getConnectionObject();
			$statement = $pdo->prepare($sql);
			$statement->execute();
			$res = $statement->fetchAll();
			$array=array();
            foreach($res as $item){
                $array[] = $item['IDsensore'];
            }
            return $array;
		}

		static function getManutentore($idTicket){
			global $connection_dbMeteo;
			$sql = 'SELECT Manutenzione FROM A_Stazioni
					JOIN A_Sensori ON(A_Stazioni.IDstazione=A_Sensori.IDstazione)
					JOIN A_Monitoraggio ON(A_Sensori.IDsensore=A_Monitoraggio.IDsensore)
					JOIN A_Ticket ON(A_Ticket.IDticket=A_Monitoraggio.IDticket)
					where A_Ticket.IDticket='.$idTicket. ' limit 1';
			$pdo = $connection_dbMeteo->getConnectionObject();
			$statement = $pdo->prepare($sql);
			$statement->execute();
			$res = $statement->fetchAll();
			if(count($res) > 0){
				return $res[0][0];
			}
			return '';
		}
		
		static function getNomeStazione($idTicket){
			global $connection_dbMeteo;
			$sql = "SELECT CONCAT_WS(' ', Comune, Attributo)Comune FROM A_Stazioni
					JOIN A_Sensori ON(A_Stazioni.IDstazione=A_Sensori.IDstazione)
					JOIN A_Monitoraggio ON(A_Sensori.IDsensore=A_Monitoraggio.IDsensore)
					JOIN A_Ticket ON(A_Ticket.IDticket=A_Monitoraggio.IDticket)
					where A_Ticket.IDticket=". $idTicket;
			$pdo = $connection_dbMeteo->getConnectionObject();
			$statement = $pdo->prepare($sql);
			$statement->execute();
			$res = $statement->fetchAll();
			if(count($res) > 0){
				return $res[0][0];
			}
			return '';
		}
		
		public function getByIDannotazione($IDannotazione){
			if($IDannotazione == null) return null;
			$sqlIdTicket = "SELECT DISTINCT IDticket FROM ". Annotazione::tableName .' WHERE '.Annotazione::idFieldName .' IN('.$IDannotazione.')';
			$sql = 'SELECT * FROM '.$this->DBTable.'
					WHERE IDticket IN('.$sqlIdTicket.')';
			return $this->getBySQLQuery($sql);
		}
		
		
		public function delete($conditions){
			$item = count($this->List) > 0 ? $this->List[0] : NULL;
			if($item == null){return;}
			$idTicket = $item[Ticket::idFieldName];
			$sql = 'DELETE FROM '. Ticket::tableName .' WHERE ' . Ticket::idFieldName . '='.$idTicket;
			global $connection_dbMeteo;
			$pdo = $connection_dbMeteo->getConnectionObject();
			$statement = $pdo->prepare($sql);
			$statement->execute();
		}

		public function getEditForm(){
			$prefissoTestoBottone = '';
			$output = '<h3>Ticket</h3><hr/>';
			$item = count($this->List) > 0 ? $this->List[0] : NULL;
			if($item == null){
				$prefissoTestoBottone = 'Apri';
				$nuovo = 'true';
				$numeroTicket = Ticket::getNuovoId();
			} else {
				$prefissoTestoBottone = 'Modifica';
				$nuovo = 'false';
				$numeroTicket = $item[Ticket::idFieldName];
			}
			$hasEndDate = $item["DataChiusura"] != null && $item["DataChiusura"] != '' ? true : false;
			$output .= '<input id="showTicketButton" type="button" value="'.$prefissoTestoBottone.' ticket" onclick="return apriTicket(event,'.$nuovo.', '.($hasEndDate ? 'true':'false').')" />';
			$output .= '<input id="deleteTicketButton" type="button" value="Elimina" onclick="return eliminaTicket()" hidden/>';
			$output .= '<input name="IDticket" value="'.$item[Ticket::idFieldName].'" hidden /><div id="ticketContainer" hidden></br>';
			$output .= 'Ticket: ' . $numeroTicket . '</br>';
			if($item != null){
				$output .= 'A: '.Ticket::getManutentore($numeroTicket).'</br>';
				$output .= 'Per: '.Ticket::getNomeStazione($numeroTicket).'</br>';
			}
			$output .= '<table class="summary"><tbody><tr><td>Data apertura</td><td><input id="dataAperturaTicket" name="DataApertura" value="'.($item <> null ? date_create($item['DataApertura'])->format('Y-m-d H:i') : '').'"/></td></tr>';
			$output .= '<tr><td>Priorit&agrave;</td><td>
					<select id="prioritaSelect" name="Priorita">
						<option></option>
						<option value="Indifferibile"'.(($item['Priorita']=="Indifferibile") ? 'selected="selected"' : '').'>Indifferibile</option>
						<option value="Urgente"'.(($item['Priorita']=="Urgente") ? 'selected="selected"' : '').'>Urgente</option>
						<option value="Normale"'.(($item['Priorita']=="Normale") ? 'selected="selected"' : '').'>Normale</option>
					</select>
					</td></tr>';
			$output .= '<tr><td>Data chiusura</td><td><input id="dataChiusuraTicket" name="DataChiusura" value="'.($hasEndDate ? (date_create($item['DataChiusura'])->format('Y-m-d H:i')) : '').'"/></td></tr></tbody></table></div>';
			return $output;
		}
		
		public static function getNuovoId(){
			global $connection_dbMeteo;
			$sql = 'SELECT max('.Ticket::idFieldName.') FROM '. Ticket::tableName;
			$pdo = $connection_dbMeteo->getConnectionObject();
			$statement = $pdo->prepare($sql);
			$statement->execute();
			$res = $statement->fetchAll();
			if(count($res) > 0){
				return $res[0][0] + 1;
			}
			return 0;
		}
	}
