<?php
session_start();
ob_start();
require_once("__init__.php");

// ## Parametri GET ##
$toDo = isset($_GET['do']) ? $_GET['do'] : '';
$IDsensore = isset($_GET['IDsensore']) ? $_GET['IDsensore'] : '';

    require_once("header.php");

    // ### Verifica permessi ###
    if($utente->LivelloUtente!="amministratore" && $utente->LivelloUtente!="gestoreDati"){
        if(isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER']!=''){
            HTTP::redirect($_SERVER['HTTP_REFERER']);
        } else {
            HTTP::redirect('index.php');
        }
    }

    // ##############################
    // #########  Aggiungi  #########
    // ##############################
    if($toDo=='aggiungi'){

        $listaNera = new ListaNera();

        // ### Salvataggio modifiche ###
        if(isset($_POST) && count($_POST)>0){
            $listaNera->aggiungiInListaNera($_POST);
            print '<p class="green">Sensore aggiunto correttamente in Lista Nera.</p>
                    '.HTML::getButtonAsLink('sensori.php?do=dettaglio&id='.$IDsensore,
                    'Dettagli sensore');
            die();
        }

        print '<h2 class="first">Aggiungi sensore #'.$IDsensore.' in Lista Nera</h2>';

        // ### Visualizza il form di modifica ###
        print '<form id="modificaListaNera" name="modificaListaNera" action="#" method="POST" style="display: inline;">
                  '.$listaNera->printEditFormAggiunta($IDsensore).'
                  <br />
                  <input type="submit" value="Aggiungi" />
               </form>';
        print HTML::getButtonAsLink($_SERVER['HTTP_REFERER'], 'Annulla');

    }

    // #############################
    // #########  Rimuovi  #########
    // #############################
    else if($toDo=='rimuovi'){

        $listaNera = new ListaNera();

        // ### Salvataggio modifiche ###
        if(isset($_POST) && count($_POST)>0){
            $listaNera->rimuoviDaListaNera($_POST);
            print '<p class="green">Sensore rimosso correttamente dalla Lista Nera.</p>
                    '.HTML::getButtonAsLink('sensori.php?do=dettaglio&id='.$IDsensore,
                    'Dettagli sensore');
            die();
        }

        print '<h2 class="first">Rimuovi sensore #'.$IDsensore.' dalla Lista Nera</h2>';

        // ### Visualizza il form di modifica ###
        print '<form id="modificaListaNera" name="modificaListaNera" action="#" method="POST" style="display: inline;">
                  '.$listaNera->printEditFormRimozione($IDsensore).'
                  <br />
                  <input type="submit" value="Aggiungi" />
               </form>';
        print HTML::getButtonAsLink($_SERVER['HTTP_REFERER'], 'Annulla');
    }


    else {
        HTTP::redirect('index.php');
    }


        require_once("footer.php");