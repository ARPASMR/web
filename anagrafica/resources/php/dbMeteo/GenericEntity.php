<?php


    abstract class GenericEntity{

        protected $DBTable = null;
        protected $IDfield = null;

        protected $lastUpdateDateField = 'Data';
        protected $lastUpdateUserField = 'IDutente';

        private $DBInterface;
        protected $List;

        /**
         *  Costruttore
         * !!! è necessario includere la chiamata parent::__construct() alla fine
         *     dei costruttori di ciascuna implementazione.
         */
        function __construct(){
            $this->checkProperties();
            $this->DBInterface = new DBInterface($this->DBTable);
        }

            /**
             *  Verifica che nelle implementazioni sia state dichiarate tutte le properties necessarie.
             */
            private function checkProperties(){
                if($this->DBTable==null){
                    die('<p class="error">Errore: e&#768; necessario definire la proprieta&#768; "DBTable" nella classe "'.get_called_class().'".</p>');
                }
                if($this->IDfield==null){
                    die('<p class="error">Errore: e&#768; necessario definire la proprieta&#768; "IDfield" nella classe "'.get_called_class().'".</p>');
                }
            }

        /**
         * Ottiene l'ID corrente
         * @return mixed
         */
        public function getID(){
            return $this->{$this->IDfield};
        }

        /**
         * Ottiene il valore del campo specificato
         * @param $field
         * @return mixed
         */
        public function __get($field){
			if(count($this->List) > 0){
				return $this->List[0][$field];
			} else {
				return null;
			}
        }

        /**
         * Ottiene i dati dal DB in base all'ID
         * @param $id
         * @return mixed
         */
        public function getByID($id){
            $conditions = array($this->IDfield => $id);
            $this->List = $this->get($conditions);
            return $this->List;
        }

        /**
         * Ottiene i dati dal DB in base all'ID
         * @param $fieldName
         * @param $fieldValue
         * @return mixed
         */
        public function getByField($fieldName, $fieldValue, $orderBy=null){
            $conditions = array($fieldName => $fieldValue);
            $this->List = $this->get($conditions, $orderBy);
            return $this->List;
        }

        /**
         * Ottiene tutti i dati DB
         * @param $orderBy
         * @return mixed
         */
        public function getAll($orderBy=null){
            $this->List = $this->get(null, $orderBy);
            return $this->List;
        }

        /**
         * Ottiene i dati dal DB in base a specifiche condizioni
         * @param array  $conditions
         * @param null   $orderBy
         * @param string $columns
         * @return mixed
         */
        public function get($conditions=array(), $orderBy=null, $columns='*'){
            return $this->DBInterface->select($conditions, $orderBy, $columns);
        }
        
        /**
         * Ottiene i dati dal DB con una query specifica
         * @param $sql
         * @return mixed
         */
        public function getBySQLQuery($sql){
            $this->List = $this->DBInterface->executeQuery($sql);
			if($this->List == null) $this->List = array();
            return $this->List;
        }

        /**
         * Esegue una query specifica
         * @param      $sql
         * @param bool $executeAsTransaction
         * @return bool|mixed
         */
        protected function executeStandaloneSQL($sql, $executeAsTransaction=true){
            $return = true;
            if($executeAsTransaction==true){
                $sql = 'BEGIN;'.$sql.'COMMIT;';
                $this->DBInterface->executeQuery($sql, false);
            } else {
                $return = $this->DBInterface->executeQuery($sql, true);
            }
            return $return;
        }

        /**
         * Ottiene dal DB l'ultima ID
         * @return mixed
         */
        private function lastID(){
            $sql = 'SELECT MAX('.$this->IDfield.') AS ID FROM '.$this->DBTable;
            $this->getBySQLQuery($sql);
            $lastID = $this->List[0]['ID'];
            return $lastID;
        }


        /**
         * Verifica se la lista dei risultati è vuota
         * @return bool
         */
        public function isEmpty(){
            return count($this->List)>0 ? false : true;
        }

        /**
         * Salva le modifiche su DB
         * @param $post
         * @return mixed
         */
        public function save($post, $dt = ''){
            foreach($post as $key=>$value){
                if($value==''){
                    $post[$key] = null;
                }
                $value = trim($value);
            }

            if($this->lastUpdateDateField!=null && $this->lastUpdateUserField!=''){
				if(!array_key_exists('IDticket', $post) || array_key_exists('Note', $post)){
					if( isset($dt) && $dt !== '' )
					{
						$post[$this->lastUpdateDateField] = $dt;
					}
					else
					{
						$post[$this->lastUpdateDateField] = date("Y-m-d H:i:s");
					}
					$post[$this->lastUpdateUserField] = $_SESSION['IDutente'];
					$post['Autore'] = Utente::getAcronimoByID($_SESSION['IDutente']);
				}
            }
            if($this->{$this->IDfield}==null){
                $return = $this->insert($post);
            } else {
                $return = $this->update($post, array($this->IDfield=>$this->{$this->IDfield}));
                if($return!==false){
                    $this->{$this->IDfield} = $post[$this->IDfield];
                }
            }
            $this->getByID($this->{$this->IDfield});
            return $return;
		}

            /**
             * Interfaccia all query di INSERT
             * @param      $post
             * @param bool $autoIncrementID
             * @return mixed
             */
            protected function insert($post, $autoIncrementID=true, $returningId = false){
                // genera nuovo ID
                if($autoIncrementID===true){
                    $post[$this->IDfield] = $this->lastID()+1;
                }
				if($this->IDfield != 'xx'){
					$this->{$this->IDfield} = trim($post[$this->IDfield]);
				}
                // esegue query di INSERT
               return $this->DBInterface->insertReturningID($post);
            }

            /**
             * Interfaccia all query di UPDATE
             * @param       $post
             * @param array $conditions
             * @return mixed
             */
            protected function update($post, $conditions=array()){
                // prepara valori da aggiornare (rimuove da $post quelli invariati)
				foreach($post as $key=>$value){
					$value=trim($value);
                    if($value==$this->List[0][$key]){
                        unset($post[$key]);
                    }
                }
                // esegue query di UPDATE
                return $this->DBInterface->update($post, $conditions);
            }

        /**
         * Interfaccia all query di DELETE
         * @param $conditions
         * @return mixed
         */
        public function delete($conditions){
            return $this->DBInterface->delete($conditions);
        }


        /**
         * Fornisce un'array semplice con la lista dei valori del campo specificato ottenuti dal DB
         * @param $field
         * @return array
         */
        public function getFieldToArray($field){
            $array = array();
            foreach($this->List as $record){
                $array[] = $record[$field];
            }
            return $array;
        }

        /**
         * Fornisce un'array associativo con le coppie id/nome
         * @param null $nameField
         * @return array
         */
        public function getPair($nameField=null){
            $array = array();
            foreach($this->List as $record){
                $array[$record[$this->IDfield]] = ($nameField!=null) ? $record[$nameField] : $record[$this->IDfield];
            }
            return $array;
        }

        /**
         * Genera una stringa CSV
         */
        public function generateCSV(){
            $output = '';
            // ## labels ##
            foreach($this->List[0] as $key=>$value){
                $output .= '"'.$key.'",';
            }
            $output = rtrim($output,',');
            $output .= "\r\n";
            // ## values ##
            foreach($this->List as $record){
                foreach($record as $value){
                    $output .= '"'.$value.'",';
                }
                $output = rtrim($output,',');
                $output .= "\r\n";
            }
            return $output;
        }

        /**
         * Genera un file XLS
         */
        public function generateXLS(){

            // import libreria PHPExcel
            require_once(dirname(__FILE__).'/../../external/PHPExcel_1.7.9/Classes/PHPExcel.php');

            $objPHPExcel = new PHPExcel();
            $objPHPExcel->setActiveSheetIndex(0);

            // ## labels ##
            $objPHPExcel->getActiveSheet()->fromArray(array_keys($this->List[0]), NULL,'A1');
            // ## values ##
            foreach($this->List as $r=>$record){
                $objPHPExcel->getActiveSheet()->fromArray(array_values($record), NULL, 'A'.($r+2));
            }

            $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
            $objWriter->save("php://output");
        }

        /**
         * Stampa Autore e data dell'ultima modifica
         * @param $idUtente
         * @param string $data
         * @return string
         */
        protected function getAutore($idUtente, $data=''){
	    $userName = '';
	    if($idUtente != null && $idUtente != ''){
			$user = new Utente($idUtente);
	    		$userName = $user->getNome();
	    }
            
            return '<span class="ultimaModifica">
                        '.($data!='' ? $data.' &nbsp': '').'
                        '.$userName.'
                    </span>';
        }


    }
