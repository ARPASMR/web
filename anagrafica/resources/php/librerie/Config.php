<?php

    class Config{

        /**
         * Ottiene PATH e URL relativo alla posizione del file __init__
         * @param $initFileDirname
         * @param $mode
         * @return string
         */
        static function initServerRoot($initFileDirname, $mode){

            $docRoot = (strtoupper(substr(PHP_OS, 0, 3))==='WIN')
                        ? $_SERVER['DOCUMENT_ROOT']
                        : $_SERVER['DOCUMENT_ROOT'].'/';

            $serverPath = str_replace('\\', '/', dirname($initFileDirname)).'/';
            $serverUrl = 'http://'.$_SERVER['HTTP_HOST'].'/'.str_replace($docRoot, '', $serverPath);
            
            return ($mode=="PATH") ? $serverPath : $serverUrl;

        }

        /**
         * Verifica che il modulo PHP specificato sia attivo
         * @param $moduleName
         */
        static function checkPHPModule($moduleName){
            if(!extension_loaded($moduleName)){
                print Debug::printError('Modulo "'.$moduleName.'" richiesto.').' Verificare la configurazione di PHP.';
            }
        }

        static function checkPHPVersion($version, $operator='>='){
            if (version_compare($version, phpversion(),  $operator)) {
                print Debug::printError('Versione di PHP '.$operator.$version.' richiesta.');
            }
        }

    }