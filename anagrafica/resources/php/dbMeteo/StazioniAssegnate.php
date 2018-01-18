<?php

    class StazioniAssegnate extends GenericEntity{

        public $numeroStazioniAssegnate = 0;
        public $numeroSensoriTotali = 0;
        public $sensoriPerTipologia = array();

        function __construct(){
            $this->DBTable = 'StazioniAssegnate';
            $this->IDfield = 'IDutente';
            parent::__construct();
        }

        /**
         * Alias di getByID()
         * @param $IDutente
         * @return mixed
         */
        public function getByUtente($IDutente){
            return $this->getByID($IDutente);
        }

        public function getStatistiche(){
            foreach($this->List as $item){
                $stazione = new Sensore();
                $stazione->getByStazione($item['IDstazione']);
                foreach($stazione->List as $sensore){
                    $this->numeroSensoriTotali++;
                    if(!isset($this->sensoriPerTipologia[$sensore['NOMEtipologia']])){
                        $this->sensoriPerTipologia[$sensore['NOMEtipologia']] = 0;
                    }
                    $this->sensoriPerTipologia[$sensore['NOMEtipologia']]++;
                }
                $this->numeroStazioniAssegnate++;
            }
        }

        public function getUtentiByStazione($IDstazione){
            $this->getByField('IDstazione',$IDstazione);
            $output='';
            foreach($this->List as $utente){
                $utenteOBJ = new Utente($utente['IDutente']);
                $output .= $utenteOBJ->getNome().', ';
            }
            $output = rtrim($output, ', ');
            return '<span style="color: Blue;">'.$output.'</span>';
        }

        /**
         * Stampa lista delle stazioni Assegnate
         */
        public function printListTable($IDutente){
            $result = $this->List;
            $output = '<thead>
                            <tr>
                                <th>
                                    <input type="button" onclick="applicaStazioniAssegnate(\'listaAssegnate\', \''.$IDutente.'\', \'rimuoviAssegnazione\')" value="Rimuovi" />
                                </th>
				<th>ID</th>
                                <th>Stazione</th>
                                <th>Rete</th>
                            </tr>
                        </thead>';
            if(count($result)>0){
                $output .= '<tbody>';
                $obj = new Rete();
                foreach($result as $record){

                    $stazione = new Stazione();
                    $stazione->getByID($record['IDstazione']);
                    //$denominazioneStazione = $stazione->getDenominazione();
		    $Comune = ($stazione->List[0]['Comune']!='') ? $stazione->List[0]['Comune'] : '&ltComune&gt';
              	    $Attributo = ($stazione->List[0]['Attributo']!='') ? $stazione->List[0]['Attributo'] : '&ltAttributo&gt';
              	    $denominazioneStazione = $Comune . '-' . $Attributo;
		    $idStazione = $stazione->List[0]["IDstazione"];

                    $rete = new Rete();
                    $rete->getByID($stazione->List[0]['IDrete']);
                    $denominazioneRete = $rete->List[0]['NOMErete'];

                    unset($stazione, $rete);

                    $output .= '<tr>
                                        <td style="text-align: center;">
                                            <input type="checkbox" name="stazioneDaRimuovereAssegnazione" value="'.$record['IDstazione'].'">
                                        </td>
					<td><b>'.$idStazione.'</b></td>
                                        <td>'.$denominazioneStazione.'</td>
                                        <td>'.$denominazioneRete.'</td>
                                    </tr>';
                }
                unset($obj);
                $output .= '</tbody>';
            } else {
                $numCol = 4;
                $output .= '<tr>'.str_repeat("<td></td>", $numCol).'</tr>
                                <tr><td style="text-align: center" colspan="'.$numCol.'">Nessun risultato.</td></tr>';

            }
            return $output;
        }

        /**
         * Aggiungi stazione dalla lista delle stazioni Assegnate
         * @param $IDstazioni
         * @param $IDutente
         */
        public function assegnaStazione($IDstazioni, $IDutente){

            if(empty($IDstazioni)){
                return;
            }

            if(!is_array($IDstazioni)){
                $IDstazioni = array($IDstazioni);
            }

            // ottiene stazioni già Assegnate
            $this->getByUtente($IDutente);
            $stazioniDB = array();
            foreach($this->List as $stazione){
                $stazioniDB[] = $stazione['IDstazione'];
            }

            // inserisci stazioni (solo nuove)
            $sql = '';
            foreach($IDstazioni as $IDstazione){
                if(!in_array($IDstazione, $stazioniDB)){
                    $sql .= "INSERT INTO ".$this->DBTable." (IDstazione, IDutente) VALUES ('".$IDstazione."', '".$IDutente."');";
                }
            }

            $this->executeStandaloneSQL($sql);
            return;
        }

        /**
         * Rimuovi stazione dalla lista delle Assegnate
         * @param $IDstazioni
         * @param $IDutente
         */
        public function rimuoviAssegnazione($IDstazioni, $IDutente){
            foreach($IDstazioni as $IDstazione){
                $conditions = array('IDstazione' => $IDstazione, 'IDutente' => $IDutente);
                $this->delete($conditions);
            }
        }

        public function eliminaUtente($IDutente){
            $conditions = array('IDutente' => $IDutente);
            $this->delete($conditions);
        }
    }