<?php

    class Convenzione extends GenericEntity{

        function __construct(){
            $this->DBTable = 'A_Convenzioni';
            $this->IDfield = 'IDconvenzione';
            parent::__construct();
        }

        public function getByStazione($IDstazione){
            return $this->getByField('IDstazione', $IDstazione);
        }

        public function printListTable(){
            $output = '<thead>
                            <tr>
                                <th></th>
                                <th>'.$this->IDfield.'</th>
                                <th>IDstazione</th>
                                <th>Stipula</th>
                                <th>Scadenza</th>
                                <th>CodiceArch</th>
                                <th>Riferimento</th>
                                <th>Note</th>
                                <th>Ultima Modifica</th>
                            </tr>
                        </thead>';
            if(count($this->List)>0){
                $output .= '<tbody>';
                global $utente;
                foreach($this->List as $item){
                    $output .= '<tr>
                                    <td>'
                                    .($utente->LivelloUtente=="amministratore"
                                        ? HTML::getButtonAsLink('convenzioni.php?do=modifica&id='.$item[$this->IDfield].'&IDstazione='.$item['IDstazione'], 'Modifica convenzione')
                                        : '').'
                                    </td>
                                    <td>'.$item[$this->IDfield].'</td>
                                    <td><b>'.$item['IDstazione'].'</b></td>
                                    <td>'.$item['Stipula'].'</td>
                                    <td>'.$item['Scadenza'].'</td>
                                    <td>'.$item['CodiceArch'].'</td>
                                    <td>'.$item['Riferimento'].'</td>
                                    <td>'.$item['Note'].'</td>
                                    <td>'.$this->getAutore($item['IDutente'],$item['Data']).'</td>
                                </tr>';
                }
                $output .= '</tbody>';
            } else {
                $output .= '<tr><td style="text-align: center" colspan="8">Nessun risultato.</td></tr>';
            }
            return $output;
        }

        public function printEditForm($IDstazione){
            $item = $this->List[0];
            $output = '<table id="tabellaModifica" class="summary">
                        <thead>';
                if($item[$this->IDfield]!=''){
                    $output .= '<tr>
                                        <td>IDstazione</td>
                                        <th>'.$item['IDstazione'].'</th>
                                        <input type="hidden" name="IDstazione" value="'.$item['IDstazione'].'" />
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
                                        <th>IDstazione</th>
                                        <th>
                                            '.$IDstazione.'
                                            <input type="hidden" name="IDstazione" value="'.$IDstazione.'" />
                                        </th>
                                    </tr>';
                }


			$output .= '</thead>
						<tbody>
						    <tr><td>Stipula</td><td>'.		'<input type="text" id="Stipula" name="Stipula" value="'.$item['Stipula'].'" />'.'</td></tr>
							<tr><td>Scadenza</td><td>'.		'<input type="text" id="Scadenza" name="Scadenza" value="'.$item['Scadenza'].'" />'.'</td></tr>
                            <tr><td>CodiceArch</td><td>'.   '<input type="text" id="CodiceArch"name="CodiceArch" value="'.$item['CodiceArch'].'" />'.'</td></tr>
                            <tr><td>ArchValDif</td><td>'.   '<select id="ArchValDif" name="ArchValDif">
                                                                <option value=""> - - </option>
                                                                <option value="Yes" '.(($item['ArchValDif']=="Yes") ? 'selected="selected"' : '').'>Si</option>
                                                                <option value="No" '.(($item['ArchValDif']=="No") ? 'selected="selected"' : '').'>No</option>
                                                             </select>'.'</td></tr>
                            <tr><td>Riferimento</td><td>'.  '<textarea id="Riferimento" name="Riferimento">'.$item['Riferimento'].'</textarea>'.'</td></tr>
                            <tr><td>Note</td><td>'.			'<textarea id="Note" name="Note">'.$item['Note'].'</textarea>'.'</td></tr>
						</tbody>
					   </table>';
            return $output;
        }

    }