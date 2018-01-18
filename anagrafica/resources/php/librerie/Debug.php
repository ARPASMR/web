<?php

    class Debug{

        static $START_TIME;

        /**
         * Setta il tempo di inizio dell'esecuzione dello script
         */
        static function executionStart(){
            $now = microtime(true);
            Debug::$START_TIME = $now;
        }

        /**
         * Stampa il tempo trascorso dall'inizio dell'esecuzione dello script
         * @param string $msg
         */
        static function printExecutionTime($msg=''){
            if(DEBUG==true) {
                $executionTime = microtime(true) - Debug::$START_TIME;
                $executionTime = round($executionTime, 4);
                print '<div>'.
                            Debug::printError('Execution: ' . $executionTime . ' seconds').
                            ($msg!='' ? '(<i>'.$msg.'</i>)' : '')
                     .'</div>';
            }
        }

        /**
         * Stampa una variabile in maniera leggibile
         * @param $var
         */
        static function printVar($var){
            print '<pre>'.print_r($var, true).'</pre>';
        }

        /**
         * Stampa messaggio di errore (grassetto e in rosso)
         * @param $msg
         * @return string
         */
        static function printError($msg){
            return '<span style="color: red; font-weight: bold;" >'.$msg.'</span>';
        }


    }