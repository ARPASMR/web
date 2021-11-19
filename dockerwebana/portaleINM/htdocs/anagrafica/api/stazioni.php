<?php
session_start();
ob_start();

    require_once('../__init__.php');

    $mode = (isset($_GET['mode']) && $_GET['mode']) ? $_GET['mode'] : 'meteogrammi';


    $jsonObj = array();

    // ######################################################
    // ########### Stazioni per mappa meteogrammi ###########
    // ######################################################
    if($mode=="meteogrammi"){

        // ### Inizializza oggetti ###
        $tipologia = new Tipologia();
        $stazioniObj = new Stazione();

        // ### Ottiene elenco stazioni ###
        $stazioni = $stazioniObj->getStazioniWEB();

        // ### Prepara output in JSON ###
        foreach($stazioni as $stazione){
            $i = array();
            foreach($stazione as $field=>$value){
                $i[$field] = utf8_encode(trim($value));
            }
            $i['sens'] = $tipologia->formattaStringaSensoriWEB(explode(',',  $stazione['sens']));
            array_push($jsonObj, $i);
        }

    }

    // ######################################################
    // ########### Stazioni per Anagrafica (test) ###########
    // ######################################################
    if($mode=="anagrafica"){

        // ### Ottiene elenco stazioni ###
        $stazioniObj = new Stazione();
        $params = $stazioniObj->parseGET($_GET);
        $stazioni = $stazioniObj->getByParams($params, 'ALL');

        // ### Prepara output in JSON ###
        foreach($stazioni as $stazione){
            $i = array();
            foreach($stazione as $field=>$value){
                $i[$field] = utf8_encode(trim($value));
            }
            array_push($jsonObj, $i);
        }

    }



    // ## print JSON ##
    ob_clean();
    header('Content-Type: application/json');
    print json_encode($jsonObj);
