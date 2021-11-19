<?php

class Destinazione extends GenericEntity{

	function __construct(){
		$this->DBTable = 'A_Sensori2Destinazione';
		$this->IDfield = 'xx';
		parent::__construct();
	}

	public function getBySensore($IDsensore){
		$sql = 'SELECT IDsensore, s2d.Destinazione as IDdestinazione, d.Note as Tipo, d.Destinazione, DataInizio, DataFine, s2d.Note, s2d.Autore, s2d.Data, s2d.IDutente
				FROM '.$this->DBTable.' as s2d
						JOIN A_Destinazioni as d
						ON  s2d.Destinazione=d.IDdestinazione
						WHERE IDsensore=\''.$IDsensore.'\' AND DataFine IS NULL 
								ORDER BY Data DESC';
		return $this->getBySQLQuery($sql);
	}

	public function getDestinazione($IDsensore, $IDdestinazione, $DataInizio){
		$sql = 'SELECT IDsensore, s2d.Destinazione as IDdestinazione, d.Destinazione, DataInizio, DataFine, s2d.Note, s2d.Autore, s2d.Data, s2d.IDutente
				FROM '.$this->DBTable.' as s2d
						JOIN A_Destinazioni as d
						ON  s2d.Destinazione=d.IDdestinazione
						WHERE IDsensore=\''.$IDsensore.'\' AND IDdestinazione = \''.$IDdestinazione.'\' AND DataInizio = \''.$DataInizio.'\';';
		$this->List = $this->getBySQLQuery($sql);
	}

	public function printListTable($IDsensore){

		global $utente;

		$output = '<thead>';

		if(/*($this->isEmpty() || $this->List[0]['DataFine']!=NULL)
		&& */$utente->LivelloUtente=="amministratore"){
		$output .= '<tr>
				<td colspan="7" class="action">'.HTML::getButtonAsLink('destinazione.php?do=modifica&IDsensore='.$IDsensore, 'Aggiungi Destinazione').'</td>
						</tr>';
		}

		$output .= '    <tr>
				<th></th>
				<!--<th>IDsensore</th>-->
				<th>Destinazione</th>
				<!--<th>Tipo</th>-->
				<th>DataInizio</th>
				<!--<th>DataFine</th>
				<th>Note</th>-->
				<th style="width: 100px;">Ultima Modifica</th>
				</tr>
				</thead>';

		if(count($this->List)>0){
			$output .= '<tbody>';
			foreach($this->List as $item){
				$editURL = 'destinazione.php?do=modifica&IDsensore='.$IDsensore.'&Destinazione='.$item['IDdestinazione'].'&DataInizio='.$item['DataInizio'];
				$output .= '<tr>
								<td class="action">'
								.(($utente->LivelloUtente=="amministratore")
								? HTML::getButtonAsLink($editURL, 'Modifica destinazione')
								: '').'
								</td>
								<!--<td>'.$item['IDsensore'].'</td>-->
								<td>'.$item['Destinazione'].'</td>
								<!--<td>'.$item['Tipo'].'</td>-->
								<td>'.$item['DataInizio'].'</td>
								<!--<td>'.$item['DataFine'].'</td>
								<td>'.$item['Note'].'</td>-->
								<td>'.$this->getAutore($item['IDutente'],$item['Data']).'</td>
							</tr>';
			}
			$output .= '</tbody>';
		} else {
			$output .= '<tr><td style="text-align: center" colspan="8">Nessuna destinazione.</td></tr>';
		}
		return $output;

	}

	public function printEditForm($IDsensore){
		$item = $this->List[0];
		$output = '<table id="tabellaModifica" class="summary">';
		if(count($this->List)>0) {
			$output .= '<thead>
					<tr>
					<td>IDsensore</td>
					<th>
					' . $item['IDsensore'] . '
							<input type="hidden" name="IDsensore" value="' . $item['IDsensore'] . '" />
									</th>
									</tr>
									<tr>
									<td>Destinazione</td>
									<th>
									'. $item['Destinazione'] . ' (ID:'.$item['IDdestinazione'].')
											<input type="hidden" name="Destinazione" value="' . $item['IDdestinazione'] .'" />
													</th>
													</tr>
													<tr>
													<td>DataInizio</td>
													<th>
													' . $item['DataInizio'] . '
															<input type="hidden" name="DataInizio" value="' . $item['DataInizio'] . '" />
																	</th>
																	</tr>
																	</thead>
																	<tbody>';
		} else {
			$output .= '<thead>
					<tr>
					<td>IDsensore</td>
					<th>
					' .$IDsensore . '
							<input type="hidden" name="IDsensore" value="' . $IDsensore . '" />
									</th>
									</tr>
									</thead>
									<tbody>
									<tr>
									<td>Destinazione</td>
									<th>
									'.Destinazione::dropdownList('Destinazione', $item['Destinazione']).'
											</th>
											</tr>
											<tr>
											<td>DataInizio</td>
											<th>
											<input type="text" id="DataInizio" name="DataInizio" value="'. date('Y-m-d H:m:s') .'" />
													</th>
													</tr>';
		}
		$output .='        <tr>
				<td>DataFine</td>
				<th>
				<input type="text" id="DataFine" name="DataFine" value="'.$item['DataFine'].'" />
						</th>
						</tr>
						<tr>
						<td>Note</td>
						<td>'.'<textarea id="Note" name="Note">';
						if( $item['Note'] == "" )
						{
							$output .= 'storico, doppio, non significativo, ...';
						}
						else
						{
							$output .= $item['Note'];
						}
						$output .= '</textarea></td>
								</tr>
								</tbody>
								</table>';

		return $output;
	}

	/**
	 * Override: Salva le modifiche su DB
	 * @param $post
	 * @return void
	 */
	public function save($post, $dt = ''){
		if(count($this->List)==0){
			$post[$this->lastUpdateUserField] = $_SESSION['IDutente'];
			$post['Autore'] = Utente::getAcronimoByID($_SESSION['IDutente']);
			$post['Data'] = date('Y-m-d H:m:s');
			$this->insert($post, false);
		} else {
			$updates = array( 'IDsensore'=>$post['IDsensore'], 'Destinazione'=>$post['Destinazione'], 'DataInizio'=>$post['DataInizio'] );
			$post['Data'] = date('Y-m-d H:m:s');
			$post[$this->lastUpdateUserField] = $_SESSION['IDutente'];
			$this->update($post, $updates);
		}
	}

	function getListaDestinazioni(){
		$sql = 'SELECT DISTINCT Destinazione, Note, IDdestinazione FROM A_Destinazioni ORDER BY Destinazione;';
		$records = $this->executeStandaloneSQL($sql, false);

		$labels = $values = array();
		foreach($records as $record){
			$lbl = $record['Destinazione'];
			if( $record["Note"] != "" )
			{
				$lbl .= " - ".$record["Note"];
			}
			//$labels[] = $record['Destinazione'];
			$labels[] = $lbl;
			$values[] = $record['IDdestinazione'];
		}
		return array($values, $labels);
	}

	static function dropdownList($listD, $selectedItem){
		$destinazioni = new Destinazione();
		list($values, $labels) = $destinazioni->getListaDestinazioni();
		return HTML::dropdownList($listD, $selectedItem, $values, $labels);
	}
}

