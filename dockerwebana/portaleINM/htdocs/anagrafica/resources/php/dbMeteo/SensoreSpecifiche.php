<?php

    class SensoreSpecifiche extends GenericEntity{

        function __construct(){
            $this->DBTable = 'A_Sensori_specifiche';
            $this->IDfield = 'IDstrumento';
            parent::__construct();
        }

        public function getBySensore($IDsensore){
            return $this->getByField('IDsensore', $IDsensore, 'DataIstallazione DESC');
        }
		
		static function isUserAssigned($IDutente, $IDstrumento){
			$sql = "SELECT DISTINCT StazioniAssegnate.IDutente FROM StazioniAssegnate 
			JOIN A_Stazioni ON(A_Stazioni.IDstazione=StazioniAssegnate.IDstazione) 
			JOIN A_Sensori ON(A_Sensori.IDstazione=A_Sensori.IDstazione) 
			JOIN A_Sensori_specifiche ON(A_Sensori_specifiche.IDsensore=A_Sensori.IDsensore) 
			WHERE A_Sensori_specifiche.IDstrumento=".$IDstrumento;
			global $connection_dbMeteo;
			$pdo = $connection_dbMeteo->getConnectionObject();
			$statement = $pdo->prepare($sql);
			$statement->execute();
			$result = $statement->fetchAll();
			foreach($result as $item){
				if($item[0] == $IDutente){
					return true;
				}
			}
			return false;
		}

        public function printListTable(){
            $output = '<thead>
                            <tr>
                                <th></th>
                                <!--<th>'.$this->IDfield.'</th>
                                <th>IDsensore</th>-->
                                <th>Marca</th>
                                <th>Modello</th>
                                <th>Riscaldatore</th>
                                <th>Note</th>
                                <th>RiscVent</th>
                                <th>DataIstallazione</th>
                                <th>DataDisistallazione</th>
                                <th>Ultima modifica</th>
                            </tr>
                        </thead>';
            if(count($this->List)>0){
                $output .= '<tbody>';
                global $utente;
                foreach($this->List as $item){
                    $sensore = new Sensore();
                    $sensore->getByID($item['IDsensore']);
                    $output .= '<tr>
                                    <td class="action">'
                                        .($utente->LivelloUtente=="amministratore"
                                            ? HTML::getButtonAsLink('strumenti.php?do=modifica&id='.$item[$this->IDfield].'&IDsensore='.$item['IDsensore'], 'Modifica strumento')
                                            : '').'
                                    </td>
                                    <!--<td>'.$item[$this->IDfield].'</td>
                                    <td>'.$item['IDsensore'].'</td>-->
                                    <td>'.$item['Marca'].'</td>
                                    <td>'.$item['Modello'].'</td>'.
                                    (( $sensore->getNomeTipologia() == 'PP' || $sensore->getNomeTipologia() == 'VV' || $sensore->getNomeTipologia() == 'DV' ) ?
                                        '<td>'.$item['Riscaldatore'].'</td>' :
                                        '<td>NA</td>')
                                    .'<td>'.$item['Note'].'</td>'.
                                    (( $sensore->getNomeTipologia() == 'PP' || $sensore->getNomeTipologia() == 'VV' || $sensore->getNomeTipologia() == 'DV' ) ?
                                        '<td>'.$item['RiscVent'].'</td>' :
                                        '<td>NA</td>')
                                    .'<td>'.$item['DataIstallazione'].'</td>
                                    <td>'.$item['DataDisistallazione'].'</td>
                                    <td>'.$this->getAutore($item['IDutente'],$item['Data']).'</td>
                                </tr>';
                }
                $output .= '</tbody>';
            } else {
                $output .= '<tr><td style="text-align: center" colspan="10">Nessun risultato.</td></tr>';
            }
            return $output;
        }

        public function printEditForm($IDsensore){
            global $utente;
            
            if( count($this->List) > 0)
            {
                $item = $this->List[0];
            			
			    $isUserAssigned = SensoreSpecifiche::isUserAssigned($utente->IDutente, $this->IDfield);
		    	$sensore = new Sensore();
			    $sensore->getByID($item['IDsensore']);
			    $disabledString = $isUserAssigned ? '' : 'disabled';

                $output = '<table id="tabellaModifica" class="summary">
                                <thead>';
                    if($item[$this->IDfield]!=''){
                        $output .= '<tr>
                                        <td>IDsensore</td>
                                        <th>'.$item['IDsensore'].'</th>
                                        <input type="hidden" name="IDsensore" value="'.$item['IDsensore'].'" />
                                    </tr>
                                    <tr>
                                        <td>'.$this->IDfield.'</td>
                                        <th>
                                            '.$item[$this->IDfield].'
                                            <input type="hidden" name="'.$this->IDfield.'" value="'.$item[$this->IDfield].'" />
                                        </th>
                                    </tr>';
                    }
                    if($item[$this->IDfield]==''){
                        $output .= '<tr>
                                        <th>IDsensore</th>
                                        <th>
                                            '.$IDsensore.'
                                            <input type="hidden" name="IDsensore" value="'.$IDsensore.'" />
                                        </th>
                                    </tr>';
                    }
                    $output .= '    </thead>
                                    <tbody>
                                        <tr><td>Marca</td><td>'.                '<input type="text" id="Marca" name="Marca" value="'.$item['Marca'].'" '.$disabledString.'/>'.'</td></tr>
                                        <tr><td>Modello</td><td>'.		        '<input type="text" id="Modello" name="Modello" value="'.$item['Modello'].'" '.$disabledString.'/>'.'</td></tr>'.
                                        (( $sensore->getNomeTipologia() == 'PP' || $sensore->getNomeTipologia() == 'VV' || $sensore->getNomeTipologia() == 'DV' ) ?
                                            '<!--<tr><td>Riscaldatore</td><td>'.	        '<input type="text" id="Riscaldatore" name="Riscaldatore" value="'.$item['Riscaldatore'].'" />'.'</td></tr>-->
                                            <tr><td>Riscaldatore</td><td>'.	        '<select id="Riscaldatore" name="Riscaldatore">'.
                                                                                        '<option value="Yes"'.(($item['Riscaldatore']=="Yes") ? 'selected="selected"' : '').'>Yes</option>
                                                                                        <option value="No"'.(($item['Riscaldatore']=="No") ? 'selected="selected"' : '').'>No</option>
                                                                                     </select>'.
                                            '</td></tr>' :
                                            '<tr><td>Riscaldatore</td><td>'.	    '<input type="text"id="Riscaldatore" name="Riscaldatore" value="" disabled></td></tr>').
    
                                        '<tr><td>Note</td><td>'.		            '<input type="text" id="Note" name="Note" value="'.$item['Note'].'" />'.'</td></tr>'.
                                        (( $sensore->getNomeTipologia() == 'PP' || $sensore->getNomeTipologia() == 'VV' || $sensore->getNomeTipologia() == 'DV' ) ?
                                            '<!--<tr><td>RiscVent</td><td>'.		        '<input type="text" id="RiscVent" name="RiscVent" value="'.$item['RiscVent'].'" />'.'</td></tr>-->
                                            <tr><td>RiscVent</td><td>'.	        '<select id="RiscVent" name="RiscVent">'.
                                                                                   '<option value="Yes"'.(($item['RiscVent']=="Yes") ? 'selected="selected"' : '').'>Yes</option>
                                                                                   <option value="No"'.(($item['RiscVent']=="No") ? 'selected="selected"' : '').'>No</option>
                                                                                 </select>'.
                                            '</td></tr>' :
                                            '<tr><td>RiscVent</td><td>'.	    '<input type="text"id="RiscVent" name="RiscVent" value="" disabled></td></tr>').
                                        '<tr><td>DataIstallazione</td><td>'.		'<input type="text" id="DataIstallazione" name="DataIstallazione" value="'.$item['DataIstallazione'].'" />'.'</td></tr>
                                        <tr><td>DataDisistallazione</td><td>'.  '<input type="text" id="DataDisistallazione" name="DataDisistallazione" value="'.$item['DataDisistallazione'].'" />'.'</td></tr>
                                    </tbody>
                                </table>';
            }
            else
            {
                $output = '<table id="tabellaModifica" class="summary">
                                <thead>';
                    
                        $output .= '<tr>
                                            <th>IDsensore</th>
                                            <th>
                                                '.$IDsensore.'
                                                <input type="hidden" name="IDsensore" value="'.$IDsensore.'" />
                                            </th>
                                        </tr>';
                    
                    $output .= '    </thead>
                                        <tbody>
                                            <tr><td>Marca</td><td>'.                '<input type="text" id="Marca" name="Marca" value=""/>'.'</td></tr>
                                            <tr><td>Modello</td><td>'.		        '<input type="text" id="Modello" name="Modello" value=""/>'.'</td></tr>
                                            <tr><td>Riscaldatore</td><td>'.
                                                '<select id="Riscaldatore" name="Riscaldatore">
                                                    <option value="Yes">Yes</option>
                                                    <option value="No">No</option>
                                                </select>'.
                                           '</td></tr>'.
                                                
                                           '<tr><td>Note</td><td>'.		            '<input type="text" id="Note" name="Note" value="" />'.'</td></tr>'.
                                           '<tr><td>RiscVent</td><td>'.	        
                                                '<select id="RiscVent" name="RiscVent">'.
                                                    '<option value="Yes">Yes</option>
                                                    <option value="No">No</option>
                                                 </select>'.
                                           '</td></tr>'.
                                           '<tr><td>DataIstallazione</td><td>'.		'<input type="text" id="DataIstallazione" name="DataIstallazione" value="" />'.'</td></tr>
                                            <tr><td>DataDisistallazione</td><td>'.  '<input type="text" id="DataDisistallazione" name="DataDisistallazione" value="" />'.'</td></tr>
                                        </tbody>
                                    </table>';
            }
            
            return $output;
        }

    }