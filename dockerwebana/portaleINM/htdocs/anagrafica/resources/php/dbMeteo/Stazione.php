<?php

    class Stazione extends GenericEntity {

        private $stazioniStoriche = null;
        private $stazioniTicketAperti = null;

        function __construct() {
            $this->DBTable = 'A_Stazioni';
            $this->IDfield = 'IDstazione';
            parent::__construct();
        }
		
		//Funzione custom causa campo POINT in DB
		public function getById($id){
			global $connection_dbMeteo;
			/*$sql = 'SELECT *, X(CoordUTM) as UTM_EST, Y(CoordUTM) as UTM_NORD, c.Descrizione as Classe FROM A_Stazioni
					LEFT JOIN A_Classificazione2Stazione c2s ON A_Stazioni.IDstazione=c2s.IDstazione 
                	LEFT JOIN A_Classificazione c ON c2s.IDclasse=c.IDclasse
					where A_Stazioni.IDstazione = :id';*/
			$sql = 'SELECT *, X(CoordUTM) as UTM_EST, Y(CoordUTM) as UTM_NORD FROM A_Stazioni
			            where A_Stazioni.IDstazione = :id';
			$pdo = $connection_dbMeteo->getConnectionObject();
			$statement = $pdo->prepare($sql);
			$statement->bindParam(':id', $id, pdo::PARAM_INT);
			$statement->execute();
			$res = $statement->fetchAll();
			$this->List = $res;
			$this->getClassification($id);
		}
		
		protected function getClassification($id) {
			global $connection_dbMeteo;
			
			$sql  = 'select c.IDclasse, c.Descrizione from A_Classificazione2Stazione as cs
					     left join A_Classificazione as c on cs.IDClasse = c.IDclasse where cs.IDstazione = :id
					     order by c.IDclasse';
			$pdo  = $connection_dbMeteo->getConnectionObject();
			$stmt = $pdo->prepare($sql);
			$stmt->bindParam(':id', $id, pdo::PARAM_INT);
			$stmt->execute();
			$res  = $stmt->fetchAll();
			if( count($this->List) > 0 ) {
				if( $this->List[0]["IDstazione"] == $id ) {
					$this->List[0]["Classificazione"] = $res;
					$this->processClassifications($id);
				}
			}
		}
		
		protected function processClassifications($id) {
			if( count($this->List) > 0 ) {
				$a = array();
				foreach( $this->List[0]["Classificazione"] as $item ) {
					$cls = $item["IDclasse"]." - ".$item["Descrizione"];
					array_push($a, $cls);
				}
				$this->List[0]["Classificazione"] = $a;
			}
		}
		
		public function printClassification($id) {
			$output = "";
			if( count($this->List) > 0 ) {
				foreach( $this->List[0]["Classificazione"] as $item ) {
					$output .= $item."<br/>";
				}
			}
			
			return $output;
		}
		
		//overwrite per coordUTM
		public function save($post, $dt = ''){
			$post['CoordUTM'] = "PointFromText('POINT(" . $post['UTM_Est'] ." ". $post['UTM_Nord'] . ")')";
			parent::save($post);
		}
		
		public function getManutentore(){
			if(isset($this->List[0])){
              $item = $this->List[0];
              return $item['Manutenzione'];
            }
            return '';
		}

        public function getDenominazione() {
            if(isset($this->List[0])){
              $item = $this->List[0];
              $Comune = ($item['Comune']!='') ? $item['Comune'] : '&ltComune&gt';
              $Attributo = ($item['Attributo']!='') ? $item['Attributo'] : '&ltAttributo&gt';
              return '<b>#' . $item['IDstazione'] . '</b> ' . $Comune . '-' . $Attributo;
            }
            return '';
        }

        public function parseGET($get) {
            $params['regione'] = (isset($get['regione']) && $get['regione']!='') ? $get['regione'] : 'lombardia';
            $params['provincia'] = (isset($get['provincia']) && $get['provincia']!='') ? $get['provincia'] : 'ALL';
            $params['rete'] = (isset($get['rete']) && $get['rete']!='') ? $get['rete'] : 'ALL';
            $params['allerta'] = (isset($get['allerta']) && $get['allerta']!='') ? $get['allerta'] : 'ALL';
            $params['stazioniStoriche'] = (isset($get['stazioniStoriche']) && $get['stazioniStoriche'] != '') ? '1' : '0';
            $params['quotaDa'] = isset($get['quotaDa']) ? $get['quotaDa'] : '';
            $params['quotaA'] = isset($get['quotaA']) ? $get['quotaA'] : '';
            $params['soloAnnotazioniAperte'] = isset($get['soloAnnotazioniAperte']) ? '1' : '0';
			$params['soloTicketAperti'] = isset($get['soloTicketAperti']) ? '1' : '0';
			$params['columnsFilters'] = isset($get['columnsfilters']) ? $get['columnsfilters'] : '';

            global $utente;
            $defautValueAssegnate =
                ($utente->LivelloUtente!=null && $utente->LivelloUtente!='amministratore')
                    ? 'on'
                    : 'off';
            $params['soloAssegnate'] = (isset($get['soloAssegnate']) && $get['soloAssegnate']!='')
                ? $get['soloAssegnate'] : $defautValueAssegnate;

            // parse dei filtri (chars su colonne tabella)
            // $filters = array();
            // foreach($get as $key=>$value){
                // if(StruttureDati::startsWith($key, "filtro_")){
                    // $filters[str_replace("filtro_", "", $key)] = $value;
                // }
            // }
            //$params['__filtri'] = $filters;

            return $params;
        }

        public function getByParams($params, $columns = 'ALL') {
            if($columns=='ALL') {
                $sql = 'SELECT A_Stazioni.IDstazione, A_Stazioni.NOMEstazione, A_Stazioni.NOMEweb, A_Stazioni.NOMEhydstra, 
						A_Stazioni.CGB_Nord, A_Stazioni.CGB_Est, A_Stazioni.lat, A_Stazioni.lon, 
						A_Stazioni.UTM_Nord, A_Stazioni.UTM_Est, A_Stazioni.Quota, A_Stazioni.IDrete, A_Stazioni.Localita,
                		A_Stazioni.Attributo, A_Stazioni.Comune, A_Stazioni.Provincia, A_Stazioni.ProprietaStazione, 
                		A_Stazioni.ProprietaTerreno, A_Stazioni.Manutenzione, A_Stazioni.NoteManutenzione, A_Stazioni.Allerta, 
                		A_Stazioni.AOaib, A_Stazioni.AOneve, A_Stazioni.AOvalanghe, A_Stazioni.LandUse, A_Stazioni.PVM, 
                		A_Stazioni.UrbanWeight, A_Stazioni.DataLogger, A_Stazioni.NoteDL, A_Stazioni.Connessione, 
                		A_Stazioni.NoteConnessione, A_Stazioni.Fiduciaria, A_Stazioni.Alimentazione, A_Stazioni.NoteAlimentazione, 
                		A_Stazioni.Autore, A_Stazioni.Data, A_Stazioni.IDutente, AsText(A_Stazioni.CoordUTM) as CoordUTM, 
                		A_Stazioni.Fiume, A_Stazioni.Bacino,
                        min(DataInizio) AS DataInizio, max(DataFine) AS DataFine,
                        A_Reti.NOMErete
                          FROM A_Stazioni
                            LEFT JOIN A_Sensori ON A_Sensori.IDstazione=A_Stazioni.IDstazione
                            LEFT JOIN A_Reti ON A_Stazioni.IDrete=A_Reti.IDrete ' ;
            } elseif($columns=='TABLELIST') {
                $sql = 'SELECT A_Stazioni.IDstazione, NOMEstazione, NOMEweb, NOMEhydstra,
                                CGB_Nord, CGB_Est, lat, lon, UTM_Nord, UTM_Est, Quota,
                                A_Stazioni.IDrete,
                                Localita, Comune, Provincia, Attributo,
                                Fiume, Bacino,
                                ProprietaStazione, ProprietaTerreno,
                                Manutenzione, NoteManutenzione,
                                AOaib, AOneve, AOvalanghe,
                                Allerta, LandUse, PVM, UrbanWeight,
                                DataLogger, NoteDL,
                                Connessione, NoteConnessione,
                                Alimentazione, NoteAlimentazione,
                                A_Stazioni.Autore, A_Stazioni.Data,
                                min(A_Sensori.DataInizio) AS DataInizio, max(A_Sensori.DataFine) AS DataFine
                          FROM A_Stazioni
                            LEFT JOIN A_Sensori ON A_Sensori.IDstazione=A_Stazioni.IDstazione ';

            } elseif($columns == 'TABLELIST_TICKET'){
				                $sql = 'SELECT A_Stazioni.IDstazione, NOMEstazione, NOMEweb, NOMEhydstra,
                                CGB_Nord, CGB_Est, lat, lon, UTM_Nord, UTM_Est, Quota,
                                A_Stazioni.IDrete,
                                Localita, Comune, Provincia, Attributo,
                                Fiume, Bacino,
                                ProprietaStazione, ProprietaTerreno,
                                Manutenzione, NoteManutenzione,
                                AOaib, AOneve, AOvalanghe,
                                Allerta, LandUse, PVM, UrbanWeight,
                                DataLogger, NoteDL,
                                Connessione, NoteConnessione,
                                Alimentazione, NoteAlimentazione,
                                A_Stazioni.Autore, A_Stazioni.Data,
                                min(A_Sensori.DataInizio) AS DataInizio, max(A_Sensori.DataFine) AS DataFine,
								A_Monitoraggio.Note, A_Monitoraggio.DataInizio as DataInizioAnnotazione, A_Monitoraggio.IDticket, A_Ticket.DataApertura, Utenti.Cognome
                          FROM A_Stazioni
                            LEFT JOIN A_Sensori ON A_Sensori.IDstazione=A_Stazioni.IDstazione 
							JOIN A_Monitoraggio ON A_Monitoraggio.IDsensore = A_Sensori.IDsensore 
							JOIN A_Ticket ON A_Ticket.IDticket = A_Monitoraggio.IDticket
							LEFT JOIN StazioniAssegnate ON StazioniAssegnate.IDstazione = A_Stazioni.IDstazione 
							LEFT JOIN Utenti ON Utenti.IDutente = StazioniAssegnate.IDutente';
			}
            // condizioni
            $sql .= $this->setQueryConditions($params);
            // ordinamento
            $sql .= ' GROUP BY A_Stazioni.IDstazione
                      ORDER BY NOMEstazione';
            // Esegue query
            $this->List = $this->getBySQLQuery($sql);
            // rimuovi Datafine se stazione non storica (almeno un sensore non storico)
            $this->getStazioniStoriche();
            foreach($this->List as $key=>$record) {
                if(!in_array($record['IDstazione'], $this->stazioniStoriche)){
                    $this->List[$key]['DataFine'] = '';
                }
            }
            return $this->List;
        }

        private function setQueryConditions($params) {
            $where = ' WHERE A_Stazioni.IDstazione IS NOT NULL ';
            // ## filtra per ID ##
            if(isset($params['ids'])) {
                $where .= " AND A_Stazioni.IDstazione IN (".$params['ids'].")";
            }
            //  ## filtra per regione ##
            if($params['regione']=="lombardia") {
                $where .= " AND A_Stazioni.IDrete<>'5'";
            } else if($params['regione']=="extra") {
                $where .= " AND A_Stazioni.IDrete='5'";
            }
            //  ## filtra per provincia ##
            if($params['provincia']!='ALL') {
                $where .= " AND A_Stazioni.Provincia='" . $params['provincia'] . "'";
            }
            //  ## filtra per rete ##
            if($params['rete']!='ALL') {
                switch($params['rete']) {
                    case "INM":
                        $where .= " AND (A_Stazioni.IDrete='4'
                                            OR A_Stazioni.IDrete='7'
                                            OR A_Stazioni.IDrete='8'
                                            OR A_Stazioni.IDrete='9'
                                            OR A_Stazioni.IDrete='10'
                                            OR A_Stazioni.IDrete='11')";
                        break;
                    case "CMG":
                        $where .= " AND A_Stazioni.IDrete='2'";
                        break;
                    case "RRQA":
                        $where .= " AND A_Stazioni.IDrete='1'";
                        break;
                    case "LAMPO":
                        $where .= " AND A_Stazioni.IDrete='3'";
                        break;
                    case "Altro":
                        $where .= " AND A_Stazioni.IDrete='6'";
                        break;
                }
            }
            //  ## filtra per allerta ##
            if($params['allerta']!='ALL') {
                $where .= " AND A_Stazioni.Allerta='" . $params['allerta'] . "'";
            }
            // ## filtra per quota ##
            if($params['quotaDa']=='' && $params['quotaA']!='') {
                $where .= " AND A_Stazioni.Quota<='" . $params['quotaA'] . "'";
            }
            if($params['quotaDa']!='' && $params['quotaA']=='') {
                $where .= " AND A_Stazioni.Quota>='" . $params['quotaDa'] . "'";
            }
            if($params['quotaDa']!='' && $params['quotaA']!='') {
                $where .= " AND A_Stazioni.Quota BETWEEN '" . $params['quotaDa'] . "' AND '" . $params['quotaA'] . "'";
            }
            // ## includi/escludi stazioni storiche ##
            // Stazioni storiche: con tutti i sensori con Storico='Yes'
            if($params['stazioniStoriche']=='0') {
                $this->getStazioniStoriche();
                $where .= ' AND A_Stazioni.IDstazione NOT IN (' . implode(',', $this->stazioniStoriche) . ')';
            }
            // ## solo con annotazioni aperte ##
            if($params['soloAnnotazioniAperte']=='1') {
                $this->getStazioniAnnotazioniAperte();
                $where .= ' AND A_Stazioni.IDstazione IN (' . implode(',', array_column($this->stazioniAnnotazioniAperte, 'IDstazione')) . ')';
            }
			// ## solo con ticket aperti ##
            if($params['soloTicketAperti']=='1') {
                $this->getStazioniTicketAperti();
				if( $this->stazioniTicketAperti != null && count($this->stazioniTicketAperti) > 0){
					$where .= ' AND A_Stazioni.IDstazione IN (' . implode(',', $this->stazioniTicketAperti) . ')';
				} else {
					$where .= ' AND A_Stazioni.IDstazione IN (-1)';
				}
				$where .= ' AND A_Monitoraggio.Chiusura = "NO" AND A_Monitoraggio.Stazione = "SI"';
            }
            // ## Visualizza solo stazioni Assegnate ##
            if($params['soloAssegnate']=='on') {
                global $utente;
                $where .= " AND A_Stazioni.IDstazione IN (
                                        SELECT IDstazione
                                        FROM StazioniAssegnate
                                        WHERE IDutente='" . $utente->getID() . "'
                                  )";
            }

            // applica fitri su stringhe
            if(isset($params['__filtri'])){
                foreach($params['__filtri'] as $key=>$value){

                }
            }
            
            // applica filtri su filtri colonne
            if( $params['columnsFilters'] != "" )
            {
                $filters = json_decode($params['columnsFilters'], false);
                //$filters = explode(',', $params['columnsFilters']);
                
                
                $visualizzazioneConTicket = $params['soloTicketAperti']=='1';
                
                if( !$visualizzazioneConTicket )
                {
                    /*
                     <th id="colonna_IDstazione">ID stazione</th>
                     <th id="colonna_IDrete" class="filter-select" data-placeholder="Tutte">Rete</th>
                     <th id="colonna_Provincia" class="filter-select" data-placeholder="Tutte">Provincia</th>
                     <th id="colonna_Comune" class="filter-match" data-placeholder="Comune">Comune</th>
                     <th id="colonna_Attributo" data-placeholder="Attributo">Attributo</th>
                     <th id="colonna_NOMEhydstra" data-placeholder="Nome">NOMEhydstra</th>
                     <th id="colonna_NOMEstazione" data-placeholder="Nome">NOMEstazione</th>
                     <th id="colonna_Fiume" class="filter-select" data-placeholder="Tutti">Fiume</th>
                     <th id="colonna_Bacino" class="filter-select" data-placeholder="Tutti">Bacino</th>
                     <th id="colonna_ProprietaStazione" class="filter-select" data-placeholder="Tutte">ProprietaStazione</th>
                     <th id="colonna_Manutenzione" class="filter-select" data-placeholder="Tutti">Manutenzione</th>
                     <th id="colonna_Quota">Quota</th>
                     <th id="colonna_DataInizio">DataInizio</th>
                     <th id="colonna_DataFine">DataFine</th>
                    */
                    for( $i = 0; $i < count($filters); $i++ )
                    {
                        $filter = strval($filters[$i]);
                        switch( $i )
                        {
                            case 0:
                            case 1:
                                break;
                            case 2:     // id stazione
                                if( !empty($filter) )
                                    $where .= " AND A_Stazioni.IDstazione like '%" . $filters[$i] . "%'";
                                break;
                            case 3:     // id rete
                                if( !empty($filter) )
                                    $where .= " AND A_Reti.NOMErete = '" . $filters[$i] . "'";
                                break;
                            case 4:     // provincia
                                if( !empty($filter) )
                                    $where .= " AND A_Stazioni.Provincia = '" . $filters[$i] . "'";
                                break;
                            case 5:     // comune
                                if( !empty($filter) )
                                    $where .= " AND A_Stazioni.Comune like '%" . $filters[$i] . "%'";
                                break;
                            case 6:     // attributo
                                if( !empty($filter) )
                                    $where .= " AND A_Stazioni.Attributo like '%" . $filters[$i] . "%'";
                                break;
                            case 7:     // nome hydstra
                                if( !empty($filter) )
                                    $where .= " AND A_Stazioni.NOMEhydstra like '%" . $filters[$i] . "%'";
                                break;
                            case 8:     // nome stazione
                                if( !empty($filter) )
                                    $where .= " AND A_Stazioni.NOMEstazione like '%" . $filters[$i] . "%'";
                                break;
                            case 9:     // fiume
                                if( !empty($filter) )
                                    $where .= " AND A_Stazioni.Fiume = '" . $filters[$i] . "'";
                                break;
                            case 10:     // bacino
                                if( !empty($filter) )
                                    $where .= " AND A_Stazioni.Bacino = '" . $filters[$i] . "'";
                                break;
                            case 11:     // proprietà stazione
                                if( !empty($filter) )
                                    $where .= " AND A_Stazioni.ProprietaStazione = '" . $filters[$i] . "'";
                                break;
                            case 12:    // manutenzione
                                if( !empty($filter) )
                                    $where .= " AND A_Stazioni.Manutenzione = '" . $filters[$i] . "'";
                                break;
                            case 13:    // quota
                                if( !empty($filter) )
                                    $where .= " AND A_Stazioni.Quota like '%" . $filters[$i] . "%'";
                                break;
                            case 14:    // data inizio
                                if( !empty($filter) )
                                    $where .= " AND A_Stazioni.DataInizio like '%" . $filters[$i] . "%'";
                                break;
                            case 15:    // data fine
                                if( !empty($filter) )
                                    $where .= " AND A_Stazioni.DataFine like '%" . $filters[$i] . "%'";
                                break;
                        }
                    }
                }
                else
                {
                    /*
                    <th id="colonna_IDstazione">ID stazione</th>
                    <th id="colonna_IDrete" class="filter-select" data-placeholder="Tutte">Rete</th>
                    <th id="colonna_Provincia" class="filter-select" data-placeholder="Tutte">Provincia</th>
                    <th id="colonna_Comune" class="filter-match" data-placeholder="Comune">Comune</th>
                    <th id="colonna_Attributo" data-placeholder="Attributo">Attributo</th>
                    <th id="colonna_NOMEhydstra" data-placeholder="Nome">NOMEhydstra</th>
                    <th id="colonna_NOMEstazione" data-placeholder="Nome">NOMEstazione</th>
                    <th>Note</th>
                    <th>DataInizio</th>
                    <th>IDticket</th>
                    <th>Data apertura ticket</th>
					<th>Assegnatario</th>
                     */
                    for( $i = 0; $i < count($filters); $i++ )
                    {
                        $filter = strval($filters[$i]);
                        switch( $i )
                        {
                            case 0:
                            case 1:
                                break;
                            case 2:     // id stazione
                                if( !empty($filter) )
                                    $where .= " AND A_Stazioni.IDstazione like '%" . $filters[$i] . "%'";
                                break;
                            case 3:     // id rete
                                if( !empty($filter) )
                                    $where .= " AND A_Reti.NOMErete = '" . $filters[$i] . "'";
                                break;
                            case 4:     // provincia
                                if( !empty($filter) )
                                    $where .= " AND A_Stazioni.Provincia = '" . $filters[$i] . "'";
                                break;
                            case 5:     // comune
                                if( !empty($filter) )
                                    $where .= " AND A_Stazioni.Comune like '%" . $filters[$i] . "%'";
                                break;
                            case 6:     // attributo
                                if( !empty($filter) )
                                    $where .= " AND A_Stazioni.Attributo like '%" . $filters[$i] . "%'";
                                break;
                            case 7:     // nome hydstra
                                if( !empty($filter) )
                                    $where .= " AND A_Stazioni.NOMEhydstra like '%" . $filters[$i] . "%'";
                                break;
                            case 8:     // nome stazione
                                if( !empty($filter) )
                                    $where .= " AND A_Stazioni.NOMEstazione like '%" . $filters[$i] . "%'";
                                break;
                            case 9:     // note
                                if( !empty($filter) )
                                    $where .= " AND A_Monitoraggio.Note like '%" . $filters[$i] . "%'";
                                break;
                            case 10:     // data inizio
                                if( !empty($filter) )
                                    $where .= " AND DataInizioAnnotazione like '%" . $filters[$i] . "%'";
                                break;
                            case 11:     // id ticket
                                if( !empty($filter) )
                                    $where .= " AND A_Monitoraggio.IDticket like '%" . $filters[$i] . "%'";
                                break;
                            case 12:    // data apertura ticket
                                if( !empty($filter) )
                                    $where .= " AND A_Ticket.DataApertura like '%" . $filters[$i] . "%'";
                                break;
                            case 13:    // assegnatario
                                if( !empty($filter) )
                                    $where .= " AND Utenti.Cognome like '%" . $filters[$i] . "%'";
                                break;
                        }
                    }
                }
            }

            return $where;
        }

        private function getStazioniStoriche() {
            if($this->stazioniStoriche==null) {
                $storicheOBJ = new SensoriStorici();
                $this->stazioniStoriche = $storicheOBJ->getStazioniStoriche();
            }
        }

        private function getStazioniAnnotazioniAperte() {
            if($this->stazioniAnnotazioniAperte==null) {
                $Annotazione = new Annotazione();
                $this->stazioniAnnotazioniAperte = $Annotazione->getStazioniAnnotazioniAperte();
            }
        }
		private function getStazioniTicketAperti() {
            if($this->stazioniTicketAperti==null) {
                $ticketOBJ = new Ticket();
                $this->stazioniTicketAperti = $ticketOBJ->getStazioniTicketAperti();
            }
        }

        public function getStazioniWEB(){
            $sql = "SELECT
                        A_Stazioni.IDstazione AS idstaz,
                        NOMEweb AS nome,
                        lat,
                        lon,
                        Quota AS quota,
                        GROUP_CONCAT(NOMEtipologia SEPARATOR ',') AS sens
                    FROM A_Stazioni
                      LEFT JOIN A_Sensori ON A_Sensori.IDstazione=A_Stazioni.IDstazione
                    WHERE Google='Yes'
                      AND lat>43 AND lat<47
                      AND lon>7 AND lon<12
                    GROUP BY A_Stazioni.IDstazione
                    ORDER BY NOMEstazione";
            return $this->getBySQLQuery($sql);
        }

        public function getListaProvince($inLombardia = null) {
            $whereClause = '';
            if($inLombardia===true) {
                $whereClause = 'WHERE IDrete<>5';
            } else if($inLombardia===false) {
                $whereClause = 'WHERE IDrete=5';
            }
            $sql = 'SELECT DISTINCT Provincia FROM '.$this->DBTable.' '.$whereClause.' ORDER BY Provincia;';
            $this->getBySQLQuery($sql);
            return $this->getFieldToArray('Provincia');
        }

        public function getListaAllerte() {
            $sql = 'SELECT DISTINCT Allerta
                        FROM '.$this->DBTable.'
                        WHERE Allerta IS NOT NULL
                            AND Allerta<>\'-\'
                        ORDER BY Allerta;';
            $this->getBySQLQuery($sql);
            return $this->getFieldToArray('Allerta');
        }

        public function getMultipleSelectAssegnate() {
            // ottiene tutte le stazioni
            $sql = 'SELECT A_Stazioni.IDstazione, NOMErete, Comune, Attributo, Cognome, Nome
					  FROM A_Stazioni
					    JOIN A_Reti ON A_Stazioni.IDrete=A_Reti.IDrete
					    LEFT JOIN StazioniAssegnate ON A_Stazioni.IDstazione=StazioniAssegnate.IDstazione
					    LEFT JOIN Utenti ON Utenti.IDutente=StazioniAssegnate.IDutente
					    ORDER BY NOMErete, Comune, Attributo';
            $this->getBySQLQuery($sql);

            $output = '<select id="IDstazione" name="IDstazione[]" multiple="multiple" style="width: 700px; height: 200px;">';
            foreach($this->List as $item) {
                $id = $item['IDstazione'] . str_repeat('&nbsp;', (6 - strlen($item['IDstazione'])) * 2);
                $Rete = $item['NOMErete'] . str_repeat('&nbsp;', (18 - strlen($item['NOMErete'])));
                $Comune = ($item['Comune']!='') ? $item['Comune'] : '&ltComune&gt';
                $Attributo = ($item['Attributo']!='') ? $item['Attributo'] : '&ltAttributo&gt';
                $utenti = str_repeat('&nbsp;', (50 - strlen($item['Attributo']))) . $item['Cognome'] . ' ' . $item['Nome'];

                $output .= '<option value="' . $item['IDstazione'] . '">
                                            #' . $id . ' ' . $Rete . ' ' . $Comune . '-' . $Attributo . ' ' . $utenti . '
                                        </option>';
            }
            $output .= '</select>';
            return $output;

        }

        public function tabellaAggiuntaStazioniAssegnate($IDutente){
            $sql = 'SELECT A_Stazioni.IDstazione, NOMErete, Comune, Attributo, Utenti.Nome as NomeAssegnatario
                    FROM A_Stazioni
                    JOIN A_Reti ON A_Stazioni.IDrete=A_Reti.IDrete
		    LEFT JOIN StazioniAssegnate ON A_Stazioni.IDstazione = StazioniAssegnate.IDstazione
		    LEFT JOIN Utenti ON Utenti.IDutente = StazioniAssegnate.IDutente
                    WHERE A_Stazioni.IDstazione NOT IN
                      (SELECT IDstazione FROM StazioniAssegnate WHERE IDutente=\''.$IDutente.'\')
                    ORDER BY NOMErete, Comune, Attributo';
            $this->getBySQLQuery($sql);

            $result = $this->List;
            $output = '<table id="listaDaAssegnare" name="listaDaAssegnare" class="lista tablesorter">
                        <thead>
                            <tr>
                                <th>
                                    <input type="button" onclick="applicaStazioniAssegnate(\'listaDaAssegnare\', \''.$IDutente.'\', \'assegna\')" value="Aggiungi" />
                                </th>
				<th>ID</th>
                                <th>Stazione</th>
                                <th>Rete</th>
				<th>Assegnatario</th>
                            </tr>
                        </thead>
                        <tbody>';
            foreach($result as $record){
                $Comune = ($record['Comune']!='') ? $record['Comune'] : '&ltComune&gt';
                $Attributo = ($record['Attributo']!='') ? $record['Attributo'] : '&ltAttributo&gt';
                $output .= '<tr>
                                <td style="text-align: center;">
                                    <input type="checkbox" name="stazioneDaAssegnare" value="'.$record['IDstazione'].'">
                                </td>
				<td><b>'.$record['IDstazione'].'</b></td>
                                <td> '.$Comune.' - '.$Attributo.'</td>
                                <td>'.$record['NOMErete'].'</td>
				<td>'.$record['NomeAssegnatario'].'</td>
                            </tr>';
            }
            $output .= '</tbody>
                  </table>
                  ';
            return $output;
        }

        protected function insert($post, $autoIncrementID=false,$returningID=false){
           return parent::insert($post, false);
        }

        public function printListTable($params) {
			$visualizzazioneConTicket = $params['soloTicketAperti']=='1';
			if($visualizzazioneConTicket){
				$numCol = 13;
			} else {
				$numCol = 19;
			}
            // Verifica che la lista richiesta non sia già in SESSION
            if(isset($_SESSION['stazioni']['params']) && $params == $_SESSION['stazioni']['params']){
                $this->List = $_SESSION['stazioni']['lista'];
            } else {
				if($visualizzazioneConTicket){
					$this->getByParams($params, 'TABLELIST_TICKET');
				} else {
				    $this->getByParams($params, 'TABLELIST');
				}
                $_SESSION['stazioni']['params'] = $params;
                $_SESSION['stazioni']['lista'] = $this->List;
            }

            $numItems = count($this->List);

            $output = '<p style="text-align: left; background-color: #FFFFB8; border-width: 1px; border-style: solid; border-bottom-style: none; border-color: Black;"><i><span id="stationsCount">' . $numItems . '</span> stazioni trovate.</i></p>';
            
            $Annotazione = new Annotazione();
            $idStazioni = $Annotazione->getStazioniAnnotazioniAperte();
            unset($Annotazione);
            $stazioni = array_column($idStazioni, 'IDstazione');
            $sm = array_column($idStazioni, 'Metadato', 'IDstazione');
            
            $output .= '<table id="listaStazioni" name="listaStazioni" class="lista tablesorter">';

            $output .= '<thead>
                            <tr>
                                <th class="filter-false sorter-false"></th>
                                <th class="filter-false sorter-false"></th>
                                <th id="colonna_IDstazione">ID stazione</th>
                                <th id="colonna_IDrete" class="filter-select" data-placeholder="Tutte">Rete</th>
                                <th id="colonna_Provincia" class="filter-select" data-placeholder="Tutte">Provincia</th>
                                <th id="colonna_Comune" class="filter-match" data-placeholder="Comune">Comune</th>
                                <th id="colonna_Attributo" data-placeholder="Attributo">Attributo</th>
            					<th id="colonna_NOMEhydstra" data-placeholder="Nome">NOMEhydstra</th>
                                <th id="colonna_NOMEstazione" data-placeholder="Nome">NOMEstazione</th>';
            if(!$visualizzazioneConTicket){
								$output .= '<th id="colonna_Fiume" class="filter-select" data-placeholder="Tutti">Fiume</th>
                                <th id="colonna_Bacino" class="filter-select" data-placeholder="Tutti">Bacino</th>
                                <th id="colonna_ProprietaStazione" class="filter-select" data-placeholder="Tutte">ProprietaStazione</th>
                                <th id="colonna_Manutenzione" class="filter-select" data-placeholder="Tutti">Manutenzione</th>
                                <!--<th id="colonna_Allerta">Allerta</th>
                                <th id="colonna_AOaib">AOaib</th>
                                <th id="colonna_AOneve">AOneve</th>
                                <th id="colonna_AOvalanghe">AOvalanghe</th>-->
                                <th id="colonna_Quota">Quota</th>
                                <th id="colonna_DataInizio">DataInizio</th>
                                <th id="colonna_DataFine">DataFine</th>';
			}
			if($visualizzazioneConTicket){
				$output .= '<th id="colonna_Note">Note</th>
                    <th id="colonna_DataInizio">DataInizio</th>
                    <th id="colonna_IDticket">IDticket</th>
                    <th id="colonna_DataAperturaTicket">Data apertura ticket</th>
					<th id="colonna_Assegnatario">Assegnatario</th>';
			}
            $output .= 		       '</tr>
                        </thead>';
            if($numItems > 0) {
                $this->getStazioniStoriche();
                $output .= '<tbody>';
                $obj = new Rete();
                foreach($this->List as $record) {
                    // verifica se storica
                    $storica = (in_array($record['IDstazione'], $this->stazioniStoriche)) ? '<span class="inStorici">storica</span>' : '';
                    // verifica se ha ticket aperti
                    //$haTicketAperti = in_array($record['IDstazione'], $idStazioni) ? '<span class="annotazioniAperte">annotazioni</span>' : '';
                    if( in_array($record['IDstazione'], $stazioni) )
                    {
                    	$metadata = is_null($sm[$record['IDstazione']]) ? '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' : $sm[$record['IDstazione']];
                    }
                    else
                    {
                    	$metadata = '';
                    }
                    $haTicketAperti = in_array($record['IDstazione'], $stazioni) ? '<span class="annotazioniAperte">'.$metadata.'</span>' : '';
                    $output .= '<tr class="recordLista">';
				                    //<td class="action">
				                    //' . HTML::getButtonAsLink($_SERVER['SCRIPT_NAME'] . '?do=dettaglio&id=' . $record['IDstazione'], 'Dettagli') . '
				                    //</td>
                    $output .= '    <td class="action">
                                        <a href="' . $_SERVER['SCRIPT_NAME'] . '?do=dettaglio&id=' . $record['IDstazione'] . '">Dettagli</a>
                                    </td>
                                    <td style="white-space:nowrap;">
                                        ' . $storica . '
                                        ' . $haTicketAperti . '
                                    </td>
                                    <td><b class="idEntita">' . (isset($record['IDstazione']) ? $record['IDstazione'] : '') . '</b></td>
                                    <td>' . (isset($record['IDrete']) ? htmlentities($obj->getNomeByID($record['IDrete'])) : '') . '</td>
                                    <td>' . (isset($record['Provincia']) ? htmlentities($record['Provincia']) : '') . '</td>
                                    <td><b>' . (isset($record['Comune']) ? htmlentities($record['Comune']) : '') . '</b></td>
                                    <td><b>' . (isset($record['Attributo']) ? $record['Attributo'] : '') . '</b></td>
                                    <td><b>' . (isset($record['NOMEhydstra']) ? $record['NOMEhydstra'] : '') . '</b></td>
                                    <td>' . (isset($record['NOMEstazione']) ? htmlentities($record['NOMEstazione']) : '') . '</td>';
                //if(!$visualizzazioneConTicket){                    
				//					$output .= '<td>' . (isset($record['Fiume']) ? $record['Fiume'] : '') . '</td>
                //                    <td>' . (isset($record['Bacino']) ? $record['Bacino'] : '') . '</td>
                //                    <td>' . (isset($record['ProprietaStazione']) ? htmlentities($record['ProprietaStazione']) : '') . '</td>
                //                    <td>' . (isset($record['Allerta']) ? $record['Allerta'] : '') . '</td>
                //                    <td>' . (isset($record['AOaib']) ? $record['AOaib'] : '') . '</td>
                //                    <td>' . (isset($record['AOneve']) ? $record['AOneve'] : '') . '</td>
                //                    <td>' . (isset($record['AOvalanghe']) ? $record['AOvalanghe'] : '') . '</td>
                //                    <td>' . (isset($record['Quota']) ? $record['Quota'] : '') . '</td>
                //                    <td>' . (isset($record['DataInizio']) ? $record['DataInizio'] : '') . '</td>
                //                    <td>' . (isset($record['DataFine']) ? $record['DataFine'] : '') . '</td>';
				//}
                    if(!$visualizzazioneConTicket){
                    	$output .= '<td>' . (isset($record['Fiume']) ? $record['Fiume'] : '') . '</td>
                                    <td>' . (isset($record['Bacino']) ? $record['Bacino'] : '') . '</td>
                                    <td>' . (isset($record['ProprietaStazione']) ? htmlentities($record['ProprietaStazione']) : '') . '</td>
                                    <td>' . (isset($record['Manutenzione']) ? htmlentities($record['Manutenzione']) : '') . '</td>
                                    <td>' . (isset($record['Quota']) ? $record['Quota'] : '') . '</td>
                                    <td>' . (isset($record['DataInizio']) ? $record['DataInizio'] : '') . '</td>
                                    <td>' . (isset($record['DataFine']) ? $record['DataFine'] : '') . '</td>';
                    }
				if($visualizzazioneConTicket){
				$output .= '<td>' . (isset($record['Note']) ? htmlentities($record['Note']) : '') . '</td>
                                    <td>' . (isset($record['DataInizioAnnotazione']) ? $record['DataInizioAnnotazione'] : '') . '</td>
                                    <td>' . (isset($record['IDticket']) ? $record['IDticket'] : '') . '</td>
                                    <td>' . (isset($record['DataApertura']) ? $record['DataApertura'] : '') . '</td>
									<td>' . (isset($record['Cognome']) ? htmlentities($record['Cognome']) : '') . '</td>';
				}
                    $output .=      '</tr>';
                }
                //$output .= '</tbody>
                //            <tr><th style="text-align: left; background-color: #FFFFB8;" colspan="' . $numCol . '"><i>' . $numItems . ' stazioni trovate.</i></th></tr>';
                $output .= '</tbody>';
            } else {
                $output .= '<tr>' . str_repeat("<td></td>", $numCol) . '</tr>
                            <tr><td style="text-align: center" colspan="' . $numCol . '">Nessun risultato.</td></tr>';
            }
            $output .= '</table>';
            return $output;
        }

        public function printSummaryTable($compact = false) {
            $item = $this->List[0];
            $output = '<table id="tabellaDettaglio" class="summary" style="margin: 5px 5px 5px 0px;">
                            <thead>
                                <tr>
                                    <td>' . $this->IDfield . '</td>
                                    <th><a href="stazioni.php?do=dettaglio&id=' . $item[$this->IDfield] . '">' . $item[$this->IDfield] .'</a></th>
                                </tr>
                                <tr><td>Comune</td><td>' . $item['Comune'] . '</td></tr>
                                <tr><td>Attributo</td><td>' . $item['Attributo'] . '</td></tr>
                            </thead>
                            <tbody>';
            if($compact==false) {
                $output .= '
                               <tr><td>Localit&agrave;</td><td>' . $item['Localita'] . '</td></tr>

                                <tr><td>NOMEstazione</td><td>' . $item['NOMEstazione'] . '</td></tr>
                                <tr><td>NOMEhydstra</td><td>' . $item['NOMEhydstra'] . '</td></tr>

                                <tr><td>IDrete</td><td>' . $item['IDrete'] . '</td></tr>'
                                .'<tr><td>Classe(i)</td><td>' . $this->printClassification($item[$this->IDfield]) . '</td></tr>'
                                .'<tr><td>Provincia</td><td>' . $item['Provincia'] . '</td></tr>

                                <tr><td>UTM_Nord</td><td>' . $item['UTM_NORD'] . '</td></tr>
                                <tr><td>UTM_Est</td><td>' . $item['UTM_EST'] . '</td></tr>
                                <tr><td>Quota</td><td>' . $item['Quota'] . '</td></tr>

                                <tr><td>Fiume</td><td>' . $item['Fiume'] . '</td></tr>
                                <tr><td>Bacino</td><td>' . $item['Bacino'] . '</td></tr>

                                <tr><td>ProprietaStazione</td><td>' . $item['ProprietaStazione'] . '</td></tr>
                                <tr><td>Manutenzione</td><td>' . $item['Manutenzione'] . '</td></tr>
                                <tr><td>ProprietaTerreno</td><td>' . $item['ProprietaTerreno'] . '</td></tr>
                                <tr><td>Manutenzione</td><td>' . $item['Manutenzione'] .
                    (($item['NoteManutenzione']!=='')
                        ? ' ' . $item['NoteManutenzione']
                        : '<br /><i>Note: ' . $item['NoteManutenzione'] . '</i>')
                    . '</td></tr>
                                <tr><td>DataLogger</td><td>' .
                    $item['DataLogger'] .
                    (($item['NoteDL']!=='')
                        ? $item['NoteDL']
                        : '<br /><i>Note: ' . $item['NoteDL'] . '</i>')
                    . '</td></tr>
                                <tr><td>Connessione</td><td>' .
                    $item['Connessione'] .
                    (($item['NoteConnessione']!=='')
                        ? $item['NoteConnessione']
                        : '<br /><i>Note: ' . $item['NoteConnessione'] . '</i>')
                    . '</td></tr>
                                <tr><td>Alimentazione</td><td>' .
                    $item['Alimentazione'] .
                    (($item['NoteAlimentazione']!=='')
                        ? $item['NoteAlimentazione']
                        : '<br /><i>Note: ' . $item['NoteAlimentazione'] . '</i>')
                    . '</td></tr>
                                <tr><td>LandUse</td><td>' . $item['LandUse'] . '</td></tr>
                                <tr><td>PVM</td><td>' . $item['PVM'] . '</td></tr>

                                <tr><td>UrbanWeight</td><td>' . $item['UrbanWeight'] . '</td></tr>
                                <tr><td><i>Ultima Modifica</i></td>
                                    <td>' . $this->getAutore($item['IDutente'], $item['Data']) . '</td>
                                </tr>';

            } else {
                $output .= '    <tr><td>Provincia</td><td>' . $item['Provincia'] . '</td></tr>
                		        <tr><td>Quota</td><td>' . $item['Quota'] . '</td></tr>';
                                //<tr><td>Comune</td><td>' . $item['Comune'] . '</td></tr>
                                //<tr><td>Allerta</td><td>' . $item['Allerta'] . '</td></tr>';
                                
            }

            $output .= '    </tbody>
                        </table>';
            return $output;
        }

        public function printEditForm() {
            $item = sizeof($this->List) > 0 ? $this->List[0] : null;
            $output = '<table id="tabellaModifica" class="summary">
                        <thead>
							<tr>
								<td>' . $this->IDfield . '</td>
								<td>
									<input type="text"  name="IDstazione" id="IDstazione" value="'.$item['IDstazione'].'" />
								</td>
							</tr>
						</thead>
						<tbody>
						    <tr><td>Comune</td><td>' . '<input type="text" id="Comune" name="Comune" value="' . $item['Comune'] . '" />' . '</td></tr>
							<tr><td>Attributo</td><td>' . '<input type="text" id="Attributo" name="Attributo" value="' . $item['Attributo'] . '" />' . '</td></tr>
							<tr><td>Localit&agrave</td><td>' . '<input type="text" id="Localita" name="Localita" value="' . $item['Localita'] . '" />' . '</td></tr>

                            <tr><td>NOMEstazione</td><td><input type="text" id="NOMEstazione" name="NOMEstazione" value="' . $item['NOMEstazione'] . '" />' . '</td></tr>
							<tr><td>NOMEhydstra</td><td>' . '<input type="text" id="NOMEhydstra" name="NOMEhydstra" value="' . $item['NOMEhydstra'] . '" />' . '</td></tr>

							<tr><td>IDrete</td><td>' . Rete::dropdownList('IDrete', $item['IDrete']) . '</td></tr>
							<tr><td>Provincia</td><td>' . '<input type="text" id="Provincia" name="Provincia" value="' . $item['Provincia'] . '" />' . '</td></tr>

							<tr><td>UTM_Nord</td><td>' . '<input type="text" id="CGB_Nord" name="UTM_Nord" value="' . $item['UTM_NORD'] . '" />' . '</td></tr>
							<tr><td>UTM_Est</td><td>' . '<input type="text" id="CGB_Est" name="UTM_Est" value="' . $item['UTM_EST'] . '" />' . '</td></tr>
							<tr><td>Quota</td><td>' . '<input type="text" id="Quota" name="Quota" value="' . $item['Quota'] . '" />' . '</td></tr>

							<tr><td>Fiume</td><td>' . '<input type="text" id="Fiume" name="Fiume" value="' . $item['Fiume'] . '" />' . '</td></tr>
							<tr><td>Bacino</td><td>' . '<input type="text" id="Bacino" name="Bacino" value="' . $item['Bacino'] . '" />' . '</td></tr>

							<tr><td>ProprietaStazione</td><td>' . '<input type="text" id="ProprietaStazione" name="ProprietaStazione" value="' . $item['ProprietaStazione'] . '" />' . '</td></tr>
							<tr><td>ProprietaTerreno</td><td>' . '<input type="text" id="ProprietaTerreno" name="ProprietaTerreno" value="' . $item['ProprietaTerreno'] . '" />' . '</td></tr>
							<tr><td>Manutenzione</td><td>' . '<input type="text" id="Manutenzione" name="Manutenzione" value="' . $item['Manutenzione'] . '" />' . '</td></tr>
							<tr><td>NoteManutenzione</td><td>' . '<input type="text" id="NoteManutenzione" name="NoteManutenzione" value="' . $item['NoteManutenzione'] . '" />' . '</td></tr>
							<tr><td>DataLogger</td><td>' . '<input type="text" id="DataLogger" name="DataLogger" value="' . $item['DataLogger'] . '" />' . '</td></tr>
							<tr><td>NoteDL</td><td>' . '<input type="text" id="NoteDL" name="NoteDL" value="' . $item['NoteDL'] . '" />' . '</td></tr>
							<!--<tr><td>Connessione</td><td>' . '<input type="text" id="Connessione" name="Connessione" value="' . $item['Connessione'] . '" />' . '</td></tr>-->
							<tr><td>Connessione</td><td><select name="Connessione">
									<option value=""> - - </option>
									<option value="GPRS" '.(($item['Connessione'] == "GPRS") ? 'selected' : '').'>GPRS</option>
									<option value="GPRS/radio" '.(($item['Connessione'] == "GPRS/radio") ? 'selected' : '').'>GPRS/radio</option>
									<option value="ISDN" '.(($item['Connessione'] == "ISDN") ? 'selected' : '').'>ISDN</option>
									<option value="radio" '.(($item['Connessione'] == "radio") ? 'selected' : '').'>radio</option>
								</select>'.'</td></tr>
							<tr><td>NoteConnessione</td><td>' . '<input type="text" id="NoteConnessione" name="NoteConnessione" value="' . $item['NoteConnessione'] . '" />' . '</td></tr>
							<!--<tr><td>Alimentazione</td><td>' . '<input type="text" id="Alimentazione" name="Alimentazione" value="' . $item['Alimentazione'] . '" />' . '</td></tr>-->
							<tr><td>Alimentazione</td><td><select name="Alimentazione">
									<option value=""> - - </option>
									<option value="rete" '.(($item['Alimentazione'] == "rete") ? 'selected' : '').'>rete</option>
									<option value="ps" '.(($item['Alimentazione'] == "ps") ? 'selected' : '').'>ps</option>
									<option value="rete+ps" '.(($item['Alimentazione'] == "rete+ps") ? 'selected' : '').'>rete+ps</option>
								</select>'.'</td></tr>
							<tr><td>NoteAlimentazione</td><td>' . '<input type="text" id="NoteAlimentazione" name="NoteAlimentazione" value="' . $item['NoteAlimentazione'] . '" />' . '</td></tr>
						    <tr><td>LandUse</td><td>' . '<input type="text" id="LandUse" name="LandUse" value="' . $item['LandUse'] . '" />' . '</td></tr>
						    <tr><td>PVM</td><td>' . '<input type="text" id="PVM" name="PVM" value="' . $item['PVM'] . '" />' . '</td></tr>
						    <tr><td>UrbanWeight</td><td>' . '<input type="text" id="UrbanWeight" name="UrbanWeight" value="' . $item['UrbanWeight'] . '" />' . '</td></tr>

						</tbody>
					   </table>';
            return $output;
        }

        public function allineamentoCampo($id, $campo, $valore) {
            return 'UPDATE "' . $this->DBTable . '" SET "' . $campo . '"=\'' . $valore . '\' WHERE "' . $this->IDfield . '"=\'' . $id . '\';';
        }

        public function allineamentoRecord($post) {

            $sql = 'INSERT INTO ' . $this->DBTable . ' (';
            foreach(array_keys($post) as $key) {
                $sql .= '"' . $key . '",';
            }
            $sql = rtrim($sql, ',');
            $sql .= ') VALUES (';
            foreach(array_values($post) as $value) {
                $sql .= '\'' . $value . '\',';
            }
            $sql = rtrim($sql, ',');
            $sql .= ');';

            return $sql;
        }

        static public function getListaDropdown($IDstazione = '', $multipleSelect = false) {

            $stazione = new Stazione();

            // ottiene tutte le stazioni
            $stazione->getAll('Comune');

            $id = $name = 'IDstazione';
            // multiple select
            $multiple = '';
            if(($multipleSelect==true)) {
                $multiple = 'multiple="multiple"';
                $name = $name . '[]';
            }
            $output = '<select id="' . $id . '" name="' . $name . '" ' . $multiple . '>
                        <option value=""> - - </option>';
            foreach($stazione->List as $item) {
                $selected = ($IDstazione==$item['IDstazione']) ? ' selected="selected" ' : '';
                $Comune = ($item['Comune']!='') ? $item['Comune'] : '&ltComune&gt';
                $Attributo = ($item['Attributo']!='') ? $item['Attributo'] : '&ltAttributo&gt';
                $output .= '<option value="' . $item['IDstazione'] . '" ' . $selected . '>#' . $item['IDstazione'] . ' ' . $Comune . '-' . $Attributo . '</option>';
            }
            $output .= '</select>';
            return $output;
        }
    }
