<?php
session_start();
ob_start();

    require_once('../__init__.php');

    // ## Cattura e verifica parametri ##
    $idStazione = (isset($_GET['idStaz']) && $_GET['idStaz']!='')
                    ? $_GET['idStaz']
                    : die('Parametro "idStaz" necessario.');
    $modalita = (isset($_GET['modalita']) && in_array($_GET['modalita'], array('7gg','24h')))
                    ? $_GET['modalita']
                    : '24h';
    $data = (isset($_GET['data']) && $_GET['data']!='')
                ? $_GET['data']
                : date('Y-m-d H:00');
    $formato = (isset($_GET['formato']) && $_GET['formato']!='')
                ? $_GET['formato']
                : 'JSON';

    // ## Ottieni osservazioni dal DB ##
    $osservazioniObj = new Osservazioni($idStazione);
    $osservazioni = $osservazioniObj->ottieniOsservazioni($data, $modalita);

    // ## Output in JSON ##
    if($formato=='JSON'){
        ob_clean();
        header('Content-Type: application/json');
        print json_encode($osservazioni, JSON_PRETTY_PRINT);
    }

    // ## Download in formato CSV
    elseif($formato=='CSV'){
        ob_clean();
        header('Content-type: text/csv');
        header('Content-Disposition: attachment; filename="dati_'.$idStazione.'.csv"');
        $output = fopen('php://output', 'w');
        // instestazione
        $rigaCSV = array('data');
        foreach(reset($osservazioni) as $sensore=>$dato){
            $rigaCSV[] = $sensore;
        }
        fputs($output, implode($rigaCSV, ',')."\n");
        // dati
        foreach($osservazioni as $data=>$osservazione){
            $rigaCSV = array($data);
            foreach($osservazione as $dato){
                $rigaCSV[] = $dato;
            }
            fputs($output, implode($rigaCSV, ',')."\n");
        }
        fclose($output);
    }
