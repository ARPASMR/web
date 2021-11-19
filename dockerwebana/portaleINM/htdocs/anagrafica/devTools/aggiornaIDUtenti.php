<?php

    session_start();
    require_once("../__init__.php");

    $DBConnection = $connection_dbMeteo->getConnectionObject();

    // Corrispondenza Autore -> IDutente
    $utenti = array(
        'MR'=>'1',
        'Ranci'=>'1',
        'EV'=>'1',
        'EB'=>'4',
        'LC'=>'6',
        'US'=>'7',
        'allineamento.R'=>'8',
        'CA'=>'11',
        'CL'=>'13',
        'Lussana','13',
        //'AM'=>'XX',
        'A_Stazioni.R'=>'14',
        'DMA-DV.R'=>'15',
        'DMA-DV_offline.'=>'16',
        'DMA-PA.R'=>'17',
        'DMA-PA_offline.'=>'18',
        'DMA-PP.R'=>'19',
        'DMA-PP_offline.'=>'20',
        'DMA-RG.R'=>'21',
        'DMA-RG_offline.'=>'22',
        'DMA-RN.R'=>'23',
        'DMA-RN_offline.'=>'24',
        'DMA-T.R'=>'25',
        'DMA-T.R_offline'=>'26',
        'DMA-UR.R'=>'27',
        'DMA-UR_offline.'=>'28',
        'DMA-VV.R'=>'29',
        'DMA-VV_offline.'=>'30',
        'OIt2mdqc_v04'=>'31',
        'RHtestT3.R'=>'32',
        'RHtestT4.R'=>'33',
        'RHtestT5.R'=>'34',
        'TestRange'=>'35',
        'aggiornamento_ftp'=>'36',
        'erre_gestione'=>'37',
        'putcsv_to_ftp.sh'=>'38',
        'rec_insert.R'=>'39',
        'rec_update.R'=>'40',
        'test_T1_offline.R'=>'41',
        'test_T1_recenti.R'=>'42',
        'test_T1a.R'=>'43',
        'test_T1a_recenti.R'=>'44',
        'test_T2_offline.R'=>'45',
        'test_T2_recenti.R'=>'46',
        'test_T2a_recenti.R'=>'47',
        'aggiornamento_f'=>'48'
    );


    $sql='';


    /**
     * #######################################################
     *    Ottieni la lista di tutte le tabelle del DB
     *     che contengono la colonna 'Autore'
     * #######################################################
     */
    $selectSQL = "SELECT *
                FROM INFORMATION_SCHEMA.COLUMNS
                WHERE
                    TABLE_SCHEMA = 'METEO'
                    AND COLUMN_NAME = 'Autore'";
    $result = $DBConnection->query($selectSQL);
    $records = $result->fetchAll(PDO::FETCH_ASSOC);
    $tables = array();
    foreach($records as $record){
        $tables[] = $record['TABLE_NAME'];
    }
    unset($record, $records, $result, $selectSQL);

    /**
     * #######################################################
     *    Aggiungi il campo 'IDutente' alla tabella
     *     nel caso questi non esista
     * #######################################################
     */
    $selectSQL = "SELECT *
                FROM INFORMATION_SCHEMA.COLUMNS
                WHERE
                    TABLE_SCHEMA = 'METEO'
                    AND COLUMN_NAME = 'IDUtente'";
    $result = $DBConnection->query($selectSQL);
    $records = $result->fetchAll(PDO::FETCH_ASSOC);
    $tablesToAddColumn = $tables;
    foreach($records as $record){
       if(in_array($record['TABLE_NAME'], $tablesToAddColumn)){
            unset($tablesToAddColumn[array_search($record['TABLE_NAME'],$tablesToAddColumn)]);
       }
    }
    unset($record, $records, $result, $selectSQL);
    foreach($tablesToAddColumn as $table){
        $sql .= "ALTER TABLE $table ADD IDutente integer;";
    }

    /**
     * #######################################################
     *    Aggiorna il campo 'IDutente' di ciascuna tabella
     *     con la relativa corrispondenza del campo 'Autore'
     * #######################################################
     */
    $case='';
    foreach($utenti as $code=>$id){
        $case .= "WHEN Autore = '".$code."' THEN ".$id."\n";
    }
    foreach($tables as $table){
        $sql .= "UPDATE $table SET IDutente =
                    CASE
                        $case
                        ELSE IDutente
                    END;
                 ";
    }

    /**
     * #######################################################
     *     Stampa/Esegui la query SQL
     * #######################################################
     */

    $sql = 'BEGIN;'.$sql.'COMMIT;';
    //$result = $DBConnection->query($sql);
    print str_replace(';',';<br /><br />', $sql);

