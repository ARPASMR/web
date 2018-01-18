<?php
require_once("__init__.php");
?><!DOCTYPE html>
<html>

    <head>
        <title>Strumento di Allineamento dbUnico -> dbMeteo</title>
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
        <!-- librerie esterne (online) -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
        <!-- fogli di stile -->
        <link rel="stylesheet" type="text/css" href="../resources/stile.css"  />
        <link rel="stylesheet" type="text/css" href="stile.allineamento.css"  />
        <script>
            function applicaUPDATE(id, campo, valore){
                $.post("ajax.allineamento.php",
                        {   toDo: 'UPDATE',
                            id: id,
                            campo: campo,
                            valore: valore
                        },
                        function(data) {
                            alert(data);
                        }
                );
            }
            function applicaINSERT(clickedButton){
                $.post("ajax.allineamento.php",
                    {   toDo: 'INSERT',
                        jsonData: $(clickedButton).prev()[0].innerText
                    },
                    function(data) {
                        alert(data);
                    }
                );
            }
        </script>
    </head>

    <body>
    <?php

    /**
     * #########################################
     * #######  Ottieni dati da dbUnico  #######
     * #########################################
     */

    $wsdl = 'https://remws.arpa.local/Anagrafica.svc?singleWsdl';
    $client = new SoapClient($wsdl);

    print '<pre>';

    $result = $client->__getFunctions();
    var_dump($result);

    $result = $client->__soapCall("ElencoStazioni", array());
    var_dump($result);

    print '</pre>';
    die();

/*
    $file = 'test.dbUnico.stazioni.xml';
    $xml = simplexml_load_file($file);
    $stazioni_dbUnico = $xml->ElencoStazioniResult->Stazione;
    //print '<pre>'.print_r($stazioni_dbUnico, true).'</pre>';
*/

    /**
     * #########################################
     * #######  Ottieni dati da dbMeteo  #######
     * #########################################
     */
    $stazioni_dbMeteo_OBJ = new Stazione();
    $stazioni_dbMeteo = $stazioni_dbMeteo_OBJ->getAll();


    /**
     * ############################################
     * #####  Confronta dati dbUnico/dbMeteo  #####
     * ############################################
     */

    $insert = $update = '';
    $numNuove=0;
    $numDifferenze=$numStazioni=0;

    foreach($stazioni_dbUnico as $stazione_dbUnico){

        $id = $stazione_dbUnico->Id;

        /**
         * #######  UPDATE  #######
         * */
        $key = StruttureDati::searchArrayForId($id, 'IDstazione', $stazioni_dbMeteo);
        if($key!=null){
            $stazione_dbMeteo = $stazioni_dbMeteo[$key];
            $differenze = false;
            $tr = '';
            if($stazione_dbUnico->Nome!=$stazione_dbMeteo['NOMEstazione']){     // NOMEstazione sostituire con Comune+Attributo
                $tr .= rigaUPDATE($id, 'Nome', $stazione_dbUnico->Nome, $stazione_dbMeteo['NOMEstazione']);
                $differenze=true;
                $numDifferenze++;
            }
            if($stazione_dbUnico->CGB_nord!=$stazione_dbMeteo['CGB_Nord']){
                $tr .= rigaUPDATE($id, 'CGB_nord', $stazione_dbUnico->CGB_nord, $stazione_dbMeteo['CGB_Nord']);
                $differenze=true;
                $numDifferenze++;
            }
            if($differenze==true){
                $numStazioni++;
                $label = '#'.$id.' '.$stazione_dbUnico->Nome;
                $update .= tabellaUPDATE($label, $tr);
            }
        }
        /**
         * #######  INSERT  #######
         * */
        else {
            $numNuove++;
            $label = '#'.$id.' '.$stazione_dbUnico->Nome;
            $insert .= tabellaINSERT($label, $stazione_dbUnico);
        }

    }


    /**
     * ########################################
     * #####  Stampa resoconto confronto  #####
     * ########################################
     */
    if($numDifferenze>0){
        print '<h1>Differenze:</h1>';
        print '<p><b>'.$numDifferenze.'</b> differenze in <b>'.$numStazioni.'</b> stazioni.</p>';
        print $update;
    }
    if($numNuove>0){
        print '<h1>Nuove:</h1>';
        print '<p><b>'.$numNuove.'</b> nuove stazioni.</p>';
        print $insert;
    }
    if($numDifferenze==0 && $numNuove==0){
        print '<h1>Nessuna differenza riscontrata</h1>';
    }


    ?>


    </body>

</html>