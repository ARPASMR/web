<?php


    /**
     * Class DBConnection
     *
     * Connessione con il Database (tramite PDO)
     */
    class DBConnection {

        private $pdo = null;
        private $dsn = 'mysql';
        private $connectionString;
        private $user = array();

        function __construct($params){
            $this->getConnectionString($params);
            $this->connect();
        }

        /**
         * Setta i parametri per la connessione al Database
         * @param $params
         */
        private function getConnectionString($params){
            // crea la string di connessione
            $this->connectionString = $this->dsn.':'
                .'host='.$params['host'].';'
                .'dbname='.$params['db'].';';
            $this->connectionString .= isset($params['port']) ? 'port='.$params['port'] : '';
            $this->user['username'] = $params['username'].'';
            $this->user['password'] = $params['password'];
        }

        /**
         * Instaura la connessione al Database
         */
        private function connect(){
            try {
                $this->pdo = new PDO($this->connectionString, $this->user['username'], $this->user['password']);
                $this->pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            }
            catch(PDOException $e) {
                print '<p class="error">Connessione al database fallita!';
                if(DEBUG==true){
                    print '<br />'.$e->getMessage();
                }
                print '</p>';
                die();
            }
        }

        /**
         * Chiude la connessione aperta
         * @method
         */
        public function close(){
            $this->pdo = NULL;
        }

        /**
         * Ritorna la connessione aperta
         * @method
         * @return {PDO} database resource object or NULL if there was an error
         */
        public function getConnectionObject(){
            return $this->pdo;
        }

    }