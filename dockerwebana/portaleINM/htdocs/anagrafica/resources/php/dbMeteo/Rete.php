<?php

    class Rete extends GenericEntity{

        function __construct(){
            $this->DBTable = 'A_Reti';
            $this->IDfield = 'IDrete';
            parent::__construct();
        }

        public function getNomeByID($id){
            $nomeRete='';
            switch($id){
                case "4":
                case "7":
                case "8":
                case "9":
                case "10":
                    $nomeRete = 'INM';
                    break;
                case "2":
                    $nomeRete = 'CMG';
                    break;
                case "1":
                    $nomeRete = 'RRQA';
                    break;
                case "6":
                    $nomeRete = 'Altro';
                    break;
            }
            return $nomeRete;
        }

        static function dropdownList($listD, $selectedItem){
            $reti = new Rete();
            $reti->getAll();
            $result = $reti->getPair('NOMErete');
            $labels = array_values($result);
            $values = array_keys($result);
            array_unshift($values, "");
            array_unshift($labels, " - - ");
            return HTML::dropdownList($listD, $selectedItem, $values, $labels);
        }

    }