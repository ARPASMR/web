<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
    /**
     * Class DBQuery
     *
     * Interfaccia principale alle entità del DataBase
     *
     */
    class DBInterface{

        private $DBConnection;
        private $DBTable;
        public $queryResult;

        function __construct($DBTable=null){
            global $connection_dbMeteo;
            $this->DBConnection = $connection_dbMeteo->getConnectionObject();
            if($DBTable!=null){
                $this->DBTable = $DBTable;
            }
        }

        /**
         *  Interfaccia per esecuzione di query SQL già pronte
         * @param $sql
         * @param bool $returnResult
         * @return mixed
         */
        public function executeQuery($sql, $returnResult=true){
            try{
                $statement = $this->DBConnection->query($sql);
                if($returnResult){
                    $this->queryResult = $statement->fetchAll(PDO::FETCH_ASSOC);
                    Debug::printExecutionTime($sql);
                    return $this->queryResult;
                }
                return true;
            }
            catch(PDOException $Exception) {
				print_r($Exception->getMessage());
                // ### Errore chiave duplicata ###
                if (isset($Exception->errorInfo[1]) && $Exception->errorInfo[1] == 1062){
                    preg_match("/^Duplicate entry \'(\d+)\' for key '(\S+)\'$/i",  $Exception->errorInfo[2], $matches);
					print_r($matches);
                    $id = $matches[1];
                    $field = $matches[2];
                    print Debug::printError('<p>Errore: esiste già un record con "'.$field.'" uguale a "'.$id.'"</p>');
                }
                // ### Errore generico ###
                else {
                    print Debug::printError('<p>Errore nell\'esecuzione della query!</p>');
                    if(DEBUG==true){
                        print Debug::printError($Exception->getMessage()).'<br />'.$sql;
                    }
                    die();
                }
                return false;
            }
        }

        /**
         * Interfaccia principale alla query di SELECT
         * @param array  $conditions
         * @param null   $orderBy
         * @param string $columns
         * @return mixed
         */
        public function select($conditions=array(), $orderBy=null, $columns='*'){
            // costruisce la clausola WHERE
            $whereClause = $this->whereStatement($conditions);
            // costruisce la clausola ORDER BY
            $orderBy = (!empty($orderBy))
                            ? ' ORDER BY '.(is_array($orderBy) ? implode(',',$orderBy) : $orderBy)
                            : '';
            // elenca le colonne da estrarre
            $columnList = is_array($columns) ? implode(',',$columns) : $columns;
            // costruisce la query di SELECT e Lancia l'esecuzione
            $sql = 'SELECT '.$columnList.' FROM '.$this->DBTable.' '.$whereClause.$orderBy.';';
            $this->executeQuery($sql);
            return $this->queryResult;
        }

        /**
         * Interfaccia principale alla query di INSERT
         * @param $values
         * @return mixed
         */
        public function insert($values){
            // Prepara lo statement di INSERT
            $labs = $vals = '';
            $i=0;
            foreach($values as $field=>$value){
                if($value!='')
                {
                	if( is_string($value) )
                	{
                		//$value = $this->DBConnection->quote($value);
                		if( strpos($value, 'PointFromText') === false )
                		{
                			$value = $this->DBConnection->quote($value);
                		}
                	}
                    $labs .= ($i!=0) ? ', '.$field : ''.$field;
                    if(strpos($value, 'PointFromText') !== false){
						$vals .= ($i!=0) ? ', '.$value : $value;
					} else {
						//$vals .= ($i!=0) ? ', \''.$value.'\'' : '\''.$value.'\'';
						$vals .= ($i!=0) ? ', '.$value.'' : ''.$value.'';
					}
                    $i++;
                }
            }

            // costruisce la query di INSERT e Lancia l'esecuzione
            if($labs!=''){
                $sql = 'INSERT
                          INTO '.$this->DBTable
                          .'('.$labs.')
                          VALUES ('.$vals.')';
                return $this->executeQuery($sql, False);
            }
        }
		
		public function insertReturningID($values){
		            // Prepara lo statement di INSERT
            $labs = $vals = '';
            $i=0;
            foreach($values as $field=>$value){
                if($value!='')
                {
                	if( is_string($value) )
                	{
                		//$value = $this->DBConnection->quote($value);
                		if( strpos($value, 'PointFromText') === false )
                		{
                			$value = $this->DBConnection->quote($value);
                		}
                	}
                    $labs .= ($i!=0) ? ', '.$field : ''.$field;
					if(strpos($value, 'PointFromText') !== false){
						$vals .= ($i!=0) ? ', '.$value : $value;
					} else {
						//$vals .= ($i!=0) ? ', \''.$value.'\'' : '\''.$value.'\'';
						$vals .= ($i!=0) ? ', '.$value.'' : ''.$value;
					}
                    $i++;
                }
            }

            // costruisce la query di INSERT e Lancia l'esecuzione
            if($labs!=''){
				try{
                $sql = 'INSERT
                          INTO '.$this->DBTable
                          .'('.$labs.')
                          VALUES ('.$vals.')';
						  $statement = $this->DBConnection->prepare($sql);
						  $statement->execute();
						  $lastID = $this->DBConnection->lastInsertId();
                return $lastID; //ritorna l'ID dell'oggetto appena inserito
				} catch(Exception $ex){
					return -1;
				}
            }
        }

        /**
         * Interfaccia principale alla query di UPDATE
         * @param $values
         * @param $conditions
         * @return mixed
         */
        public function update($values, $conditions){
            // costruisce la clausola WHERE
            $whereClause = $this->whereStatement($conditions);
            // Prepara lo statement di UPDATE
            $updateStatement = '';
            $i=0;
            foreach($values as $field=>$value){
                $updateStatement .= ($i!=0) ? ', ' : ' SET ';
                if($value==''){
                    $updateStatement .= $field.' = NULL';
                } else
                {
                	if( is_string($value) )
                	{
                		if( strpos($value, 'PointFromText') === false )
                		{
                			$value = $this->DBConnection->quote($value);
                		}
                	}
					if(strpos($value, 'PointFromText') !== false){
						$updateStatement .= $field.' = '.$value;
					} else {
						//$updateStatement .= $field.' = \''.$value.'\'';
						$updateStatement .= $field.' = '.$value;
					}
                }
                $i++;
            }
            // costruisce la query di INSERT e Lancia l'esecuzione
            if($updateStatement!=''){
                $sql = 'UPDATE '.$this->DBTable.'
                        '.$updateStatement.'
                        '.$whereClause.';';
                return $this->executeQuery($sql, False);
            }
        }

        /**
         * Interfaccia principale alla query di DELETE
         * @param $conditions
         * @return mixed
         */
        public function delete($conditions){
            // costruisce la clausola WHERE
            $whereClause =$this->whereStatement($conditions);
            // Costruisce la query di DELETE e Lancia l'esecuzione
            if($whereClause!=''){
                $sql = 'DELETE
                      FROM '.$this->DBTable.'
                      '.$whereClause.';';
                return $this->executeQuery($sql, False);
            }
        }

            /**
             * Costruzione della clausola WHERE
             * @param $conditions
             * @return string
             */
            private function whereStatement($conditions){
                $whereClause = '';
                if(!empty($conditions)){
                    foreach($conditions as $field=>$value){
                        $whereClause .= ($whereClause=='') ? ' WHERE ' : ' AND ';
                        if($value==NULL){
                            $whereClause .= $field.' IS NULL';
                        } else {
                            $whereClause .= $field.' = \''.$value.'\'';
                        }

                    }
                }
                return ($whereClause!='') ? $whereClause : '';
            }


    }
