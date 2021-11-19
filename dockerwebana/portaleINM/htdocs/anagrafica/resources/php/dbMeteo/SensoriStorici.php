<?php

    class SensoriStorici extends GenericEntity{

        function __construct(){
            $this->DBTable = 'A_Sensori';
            $this->IDfield = 'IDsensore';
            parent::__construct();
        }

        public function getStazioniStoriche(){

            // ### Tutti sensori storici ###
            $sql = 'SELECT IDstazione
                            FROM '.$this->DBTable.'
                            WHERE Storico=\'Yes\';';
            $this->getBySQLQuery($sql);
            $sensoriStorici=array();
            foreach($this->List as $item){
                if(!in_array($item['IDstazione'], $sensoriStorici)){
                    $sensoriStorici[] = $item['IDstazione'];
                }
            }
            $this->List = array();

            // ### Tutti sensori NON storici ###
            $sql = 'SELECT IDstazione
                            FROM '.$this->DBTable.'
                            WHERE Storico=\'No\';';
            $this->getBySQLQuery($sql);
            $sensoriNONStorici=array();
            foreach($this->List as $item){
                if(!in_array($item['IDstazione'], $sensoriNONStorici)){
                    $sensoriNONStorici[] = $item['IDstazione'];
                }
            }
            $this->List = array();

            // ### intersezione ###
            $stazioniStoriche = array();
            foreach($sensoriStorici as $stazione){
                if(!in_array($stazione, $sensoriNONStorici)){
                    $stazioniStoriche[] = $stazione;
                }
            }

            return $stazioniStoriche;
        }

    }