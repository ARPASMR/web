<?php

    session_start();
    require_once("__init__.php");

    // ## Parametri GET ##
    $toDo = isset($_GET['do']) ? $_GET['do'] : 'lista';
    $IDutente = isset($_GET['IDutente']) ? $_GET['IDutente'] : '';
    $IDstazioni = isset($_GET['IDstazione']) ? $_GET['IDstazione'] : '';

    // verifica permessi
    if($utente->LivelloUtente!="amministratore"){
        HTTP::redirect('index.php');
    }
    // verifica parametri
    if($IDutente==''){
        HTTP::redirect('utenti.php');
    }

    require_once("header.php");


    // ###########################
    // #########  Lista  #########
    // ###########################
    if($toDo=='lista'){

        $utenteObj = new Utente();
        $utenteObj->getByID($IDutente);
        $denominazioneUtente = $utenteObj->getDenominazione();
        unset($utenteObj);


        // Visualizza la lista delle stazioni assegnate
        $stazioniAssegnate = new StazioniAssegnate();
        $stazioniAssegnate->getByUtente($IDutente);
        print '<h2 class="first">Gestione Stazioni Assegnate (<span style="color: blue">'.$denominazioneUtente.'</span>)</h2>
               <table id="listaAssegnate" name="listaAssegnate" class="lista tablesorter">
                    '.$stazioniAssegnate->printListTable($IDutente).'
               </table>';

        print '<br />';

        // Visualizza la lista per l'assegnazione di stazioni
        $stazioni = new Stazione();
        print '<h2>Assegnazione nuove stazioni</h2>'.
                $stazioni->tabellaAggiuntaStazioniAssegnate($IDutente).
                '<input type="button" onclick="applicaStazioniAssegnate(\'listaDaAssegnare\', \''.$IDutente.'\', \'assegna\')" value="Aggiungi" />';

    }

    // ##############################
    // #########  Assegna  #########
    // ##############################
    elseif($toDo=='assegna'){
        $IDstazioni = explode(",", $IDstazioni);
        $stazioniAssegnate = new StazioniAssegnate();
        $stazioniAssegnate->assegnaStazione($IDstazioni, $IDutente);
        print '<p class="green">Stazione aggiunta correttamente.</p>';
        print HTML::getButtonAsLink($_SERVER['SCRIPT_NAME'].'?IDutente='.$IDutente, 'Continua');
    }

    // ####################################
    // ######  Rimuovi assegnazione  ######
    // ####################################
    elseif($toDo=='rimuoviAssegnazione'){
        $IDstazioni = explode(",", $IDstazioni);
        $stazioniAssegnate = new StazioniAssegnate();
        $stazioniAssegnate->rimuoviAssegnazione($IDstazioni, $IDutente);
        print '<p class="green">Stazione eliminata correttamente.</p>';
        print HTML::getButtonAsLink($_SERVER['SCRIPT_NAME'].'?IDutente='.$IDutente, 'Continua');
        die();
    }

echo '<script language="javascript" type="text/javascript" src="resources/js/stazioniAssegnate.js" ></script>';


require_once("footer.php");
