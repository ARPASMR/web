<?php

    class Osservazioni{

        private $sensori = array();
        private $DBInterface;

        function __construct($IDstazione=null){
            if(intval($IDstazione)!=false){
                $sensori = new Sensore();
                $sensori->getByStazione($IDstazione);
                $this->sensori = $sensori->getPair('NOMEtipologia');
            }
            $this->DBInterface = new DBInterface();
        }

        /**
         * Ottiene le osserviazioni dal DB
         * @param $date
         * @param $mode
         * @return array
         */
        public function ottieniOsservazioni($date, $mode){

            $sensori = array('PA',/*'PA2',*/'UR','PP','T',/*'T',*/'DVS','DV','VVS','VV','RG','RN','N') ;  // Ordinati come nel vecchio CSV

            // ### Calcola intervallo di tempo ###
            $date = ($date==null) ? date('Y-m-d H:00') : $date;
            $numDays = ($mode=='24h') ? 1 : 7;
                // Data di inizio
                $startDate = getdate(strtotime($date)-60*60*24*$numDays);
                $startDate['hours'] = 0;
                // Data di fine
                $endDate = getdate(strtotime($date)-60*60*24);
                $endDate['hours'] = 23;

            // ### Inizializza array osservazioni ###
            $osservazioni = $this->inizializzaOsservazioni($sensori, $startDate, $endDate);

            // ### Ottiene le osserviazioni dal DB ###
            foreach($this->sensori as $id=>$tipo) {
                if(in_array($tipo, $sensori)){
                    $tabellaDB = $this->ottieniTabelladaTipoSensore($tipo, $startDate['year']);
                    $sql = $this->querySql($tabellaDB, $id, $startDate, $endDate);
                    $result = $this->DBInterface->executeQuery($sql);
                    foreach($result as $record) {
                        $osservazioni[substr($record['Data_e_ora'], 0, 16)][$this->sensori[$record['IDsensore']]] = $record['Misura'];
                    }
                }
            }
            return $osservazioni;
        }

            /**
             * Inizializza un contenitore vuoto per le osservazioni (valori a -999.0)
             * @param $sensori
             * @param $startDate
             * @param $endDate
             * @return mixed
             */
            private function inizializzaOsservazioni($sensori, $startDate, $endDate){

                // $startDate e $endDate in format Unix timestamp
                $startTime = mktime($startDate['hours'], $startDate['minutes'], $startDate['seconds'], $startDate['mon'], $startDate['mday'], $startDate['year']);
                $endTime = mktime($endDate['hours'], $endDate['minutes'], $endDate['seconds'], $endDate['mon'], $endDate['mday'], $endDate['year']);

                // Genera Array con tutti i sensori (inilializza i valori a -999.0)
                $arraySensori = array();
                foreach($sensori as $sensore){
                    $arraySensori[$sensore] = '-999.0';
                }
                // Array dei sensori per ogni ora
                $osservazioni = array();
                for($t=$startTime; $t<=$endTime; $t+=360){
                    $osservazioni[$this->formattaDataPerAPI(getdate($t))] = $arraySensori;
                }
                return $osservazioni;
            }

            /**
             * Genera la query SQL per interrogare le tabelle delle osservazioni
             * @param $tabellaDB
             * @param $idSensore
             * @param $startDate
             * @param $endDate
             * @return string
             */
            private function querySql($tabellaDB, $idSensore, $startDate, $endDate){
                return 'SELECT * FROM `'.$tabellaDB.'`
                            WHERE IDsensore='.$idSensore.'
                            AND Data_e_ora BETWEEN "'.$this->formattaDataPerQuery($startDate).'"
                                            AND "'.$this->formattaDataPerQuery($endDate).';"
                            ORDER BY Data_e_ora;';
            }

            /**
             * Restitutisce la tabella del DB da interrogare
             * @param $tipoSensore
             * @param $anno
             * @return string
             */
            private function ottieniTabelladaTipoSensore($tipoSensore, $anno){
                $tipologia  = new Tipologia();
                $tabellaDB = $tipologia->ottieniTabellaOsservazioni($tipoSensore);
                $tabellaDB .= $anno;
                return $tabellaDB;
            }

            /**
             * Converte una data in stringa (es. 2015-12-31 23:00:00)
             * @param $date
             * @return string
             */
            private function formattaDataPerQuery($date){
                return $date['year']
                        .'-'.str_pad($date['mon'], 2, "0", STR_PAD_LEFT)
                        .'-'.str_pad($date['mday'], 2, "0", STR_PAD_LEFT)
                        .' '.str_pad($date['hours'], 2, "0", STR_PAD_LEFT)
                        .':00:00';
            }

            /**
             * Converte una data in stringa (es. 2015-12-31 23:00)
             * @param $date
             * @return string
             */
            private function formattaDataPerAPI($date){
                return $date['year']
                        .'-'.str_pad($date['mon'], 2, "0", STR_PAD_LEFT)
                        .'-'.str_pad($date['mday'], 2, "0", STR_PAD_LEFT)
                        .' '.str_pad($date['hours'], 2, "0", STR_PAD_LEFT)
                        .':00';
            }

    }


