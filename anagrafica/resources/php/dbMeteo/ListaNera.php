<?php

	class ListaNera extends GenericEntity{
		
		function __construct(){
            $this->DBTable = 'A_ListaNera';
            $this->IDfield = 'IDsensore';
            parent::__construct();
        }

        public function getStoricoBySensore($IDsensore){
            return $this->getByField('IDsensore', $IDsensore, ' Data DESC');
        }

        public function printListTable($IDsensore){
            global $utente;
            $output = '<thead>';
            /*if(($this->isEmpty() || $this->List[0]['DataFine']!=NULL)
               && ($utente->LivelloUtente=="amministratore" || $utente->LivelloUtente=="gestoreDati")){
                $output .= '<tr>
                                <td colspan="5" class="action">'.HTML::getButtonAsLink('listaNera.php?do=aggiungi&IDsensore='.$IDsensore, 'Aggiungi in Lista Nera').'</td>
                            </tr>';
            }*/
            $output .= '    <tr>
                                
                                <th>DataInizio</th>
                                <th>DataFine</th>
                                <th>Ultima modifica</th>
                            </tr>
                        </thead>';
            if(count($this->List)>0){
                $output .= '<tbody>';
                global $utente;
                foreach($this->List as $item){
                    $inListaNera = $item['DataFine']==NULL ? 'inListaNera' : '';
                    $output .= '<tr class="'.$inListaNera.'">';
                                    /*<td style="text-align: center;" class="action">';
                                    if($utente->LivelloUtente=="amministratore" || $utente->LivelloUtente=="gestoreDati"){
                                        if($item['DataFine']==NULL || $item['DataFine']==''){
                                            $output .= HTML::getButtonAsLink('listaNera.php?do=rimuovi&IDsensore='.$IDsensore, 'Rimuovi da Nista Nera').' ';
                                        }
                                    }*/
                     $output .= '   
                                    <td>'.$item['DataInizio'].'</td>
                                    <td>'.$item['DataFine'].'</td>
                                    <td>'.$this->getAutore($item['IDutente'],$item['Data']).'</td>
                                </tr>';
                }
                $output .= '</tbody>';
            } else {
                $output .= '<tr><td style="text-align: center" colspan="9">Nessun risultato.</td></tr>';
            }
            return $output;

        }

        public function printEditFormAggiunta($IDsensore){

            $annotazione = new Annotazione();

            $dataInizio = date("Y-m-d"); // oggi
            $update = 'false';

            // Sovrascrivi se esiste già record con DataInizio uguale a oggi
            $this->getStoricoBySensore($IDsensore);
            if(isset($this->List[0]) && $this->List[0]['DataInizio']==$dataInizio){
                $update = 'true';
            }

            $output = '<table id="tabellaModifica" class="summary">
                            <thead>
                            <tr>
                                <tr>
                                    <th>IDsensore</th>
                                    <th>
                                        '.$IDsensore.'
                                        <input type="hidden" name="IDsensore" value="'.$IDsensore.'" />
                                        <input type="hidden" name="update" value="'.$update.'" />
                                    </th>
                                </tr>
                             </thead>
                            <tbody>
                                <tr><td>DataInizio</td><td>'.$dataInizio.'<input type="hidden" id="DataInizio" name="DataInizio" value="'.$dataInizio.'" />'.'</td></tr>
                            </tbody>
                       </table>
                       <br />';

            $output .= '<h2 class="first">Annotazione associata</h2>'
                        .$annotazione->printEditFormInListaNera(true);

            return $output;
        }

        public function printEditFormRimozione($IDsensore){

            $annotazione = new Annotazione();
            $dataFine = date("Y-m-d"); // oggi

            $this->getStoricoBySensore($IDsensore);

            $output = '<table id="tabellaModifica" class="summary">
                            <thead>
                            <tr>
                                <tr>
                                    <th>IDsensore</th>
                                    <th>
                                        '.$IDsensore.'
                                        <input type="hidden" name="IDsensore" value="'.$IDsensore.'" />
                                    </th>
                                </tr>
                             </thead>
                            <tbody>
                                <tr><td>DataInizio</td><td>'.$this->List[0]['DataInizio'].'<input type="hidden" id="DataInizio" name="DataInizio" value="'.$this->List[0]['DataInizio'].'" />'.'</td></tr>
                                <tr><td>DataFine</td><td>'.$dataFine.'<input type="hidden" id="DataFine" name="DataFine" value="'.$dataFine.'" />'.'</td></tr>
                            </tbody>
                       </table>
                       <br />';

            $output .= '<h2 class="first">Annotazione associata</h2>'
                        .$annotazione->printEditFormInListaNera(false);

            return $output;
        }

        public function aggiungiInListaNera($post){

            $post[$this->lastUpdateDateField] = date("Y-m-d H:i:s");
            $post[$this->lastUpdateUserField] = $_SESSION['IDutente'];

            $update = ($post['update']=='true') ? true : false;
            unset($post['update']);

            // ### Salva Annotazione ####
            $postAnnotazione['IDsensore'] = $post['IDsensore'];
            $postAnnotazione['Stazione'] = 'NO';
            $postAnnotazione['Note'] = $post['Note'];
            $postAnnotazione['Chiusura'] = $post['Chiusura'];
            $postAnnotazione['Metadato'] = $post['Metadato'];
            $postAnnotazione[$this->lastUpdateDateField] = $post[$this->lastUpdateDateField];
            $postAnnotazione[$this->lastUpdateUserField] = $post[$this->lastUpdateUserField];
            $annotazione = new Annotazione();
            $annotazione->save($postAnnotazione);

            // ## Inserisci in lista nera ##
            if($update==false){
                $postListaNera['IDsensore'] = $post['IDsensore'];
                $postListaNera['DataInizio'] = $post['DataInizio'];
                $postListaNera[$this->lastUpdateDateField] = $post[$this->lastUpdateDateField];
                $postListaNera[$this->lastUpdateUserField] = $post[$this->lastUpdateUserField];
                $this->insert($postListaNera, false);
            }
            // ## Rimetti in lista nera ##
            else {
                $values = array('DataFine' => NULL,
                                $this->lastUpdateDateField => $post[$this->lastUpdateDateField],
                                $this->lastUpdateUserField => $post[$this->lastUpdateUserField] );
                $conditions = array('IDsensore' => $post['IDsensore'],
                                    'DataInizio'=> $post['DataInizio']);
                $this->update($values, $conditions);
            }

        }

        public function rimuoviDaListaNera($post){

            $lastUpdateDate = date("Y-m-d H:i:s");
            $lastUpdateUser = $_SESSION['IDutente'];

            // ### Salva Annotazione ####
            $postAnnotazione['IDsensore'] = $post['IDsensore'];
            $postAnnotazione['Stazione'] = 'NO';
            $postAnnotazione['Note'] = $post['Note'];
            $postAnnotazione['Chiusura'] = $post['Chiusura'];
            $postAnnotazione['Metadato'] = $post['Metadato'];
            $postAnnotazione[$this->lastUpdateDateField] = $lastUpdateDate;
            $postAnnotazione[$this->lastUpdateUserField] = $lastUpdateUser;
            $annotazione = new Annotazione();
            $annotazione->save($postAnnotazione);


            // ## Rimuovi da lista nera ##
            $values = array('DataFine' => $post['DataFine'],
                            $this->lastUpdateDateField => $lastUpdateDate,
                            $this->lastUpdateUserField => $lastUpdateUser );
            $conditions = array('IDsensore' => $post['IDsensore'],
                                'DataFine'=> NULL);
            $this->update($values, $conditions);
        }

        public function isSensoreInListaNera($IDsensore){
            $sql = 'SELECT IDsensore
						FROM '.$this->DBTable.'
						WHERE DataFine IS NULL
						    AND IDsensore = \''.$IDsensore.'\'
						ORDER BY IDsensore;';
            $this->getBySQLQuery($sql);
            if(count($this->List)>0){
                return '<div class="inListaNera" style="padding: 5px; width: 400px;">Il sensore &egrave; attualmente in Lista Nera</div><br />';
            } else {
                return '';
            }
        }
        
        public function getSensoriInListaNera(){
			$sql = 'SELECT IDsensore 
						FROM '.$this->DBTable.' 
						WHERE DataFine IS NULL
						ORDER BY IDsensore;';
			$this->getBySQLQuery($sql);
			$array=array();
			foreach($this->List as $item){
				$array[] = $item['IDsensore'];
			}
			return $array;
		}

        public function getSensoriInListaNeraByStazione($IDstazione){
            $sql = 'SELECT LN.IDsensore
						FROM '.$this->DBTable.' as LN
						JOIN A_Sensori as SN
						    ON LN.IDsensore = SN.IDsensore
						WHERE LN.DataFine IS NULL
						    AND SN.IDStazione = \''.$IDstazione.'\'
						ORDER BY LN.IDsensore;';
            $this->getBySQLQuery($sql);
            $array=array();
            foreach($this->List as $item){
                $array[] = $item['IDsensore'];
            }
            return $array;
        }

    }
