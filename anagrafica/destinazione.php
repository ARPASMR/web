<?php
session_start();
ob_start();
require_once("__init__.php");

// ## Parametri GET ##
$toDo = isset($_GET['do']) ? $_GET['do'] : 'lista';
$IDsensore = isset($_GET['IDsensore']) ? $_GET['IDsensore'] : '';
$IDdestinazione = isset($_GET['Destinazione']) ? $_GET['Destinazione'] : '';
$DataInizio = isset($_GET['DataInizio']) ? $_GET['DataInizio'] : '';


    require_once("header.php");

    // ##############################
    // #########  Modifica  #########
    // ##############################
    if($toDo=="modifica"){

        // ### Verifica permessi ###
        if($utente->LivelloUtente!="amministratore"){
            if(isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER']!=''){
                HTTP::redirect($_SERVER['HTTP_REFERER']);
            } else {
                HTTP::redirect('index.php');
            }
        }

        $destinazione = new Destinazione();
        if($IDsensore!='' && $IDdestinazione!='' && $DataInizio!=''){
            $destinazione->getDestinazione($IDsensore, $IDdestinazione, $DataInizio);
        }

        // ### Salvataggio modifiche ###
        if(isset($_POST) && count($_POST)>0){

            $destinazione->save($_POST);
            unset($destinazione);

            print '<p class="green">Salvataggio avvenuto correttamente.</p>'
                  .HTML::getButtonAsLink('sensori.php?do=dettaglio&id='.$IDsensore, 'Torna a dettagli sensore');
            die();

        }



        // ### Visualizza il form di modifica ###
        if($IDsensore!='' && $IDdestinazione!='' && $DataInizio!=''){
            print '<h2 class="first">Modifica Destinazione</h2>';
        } else {
            print '<h2 class="first">Crea nuova Destinazione</h2>';
        }
        print '<br />
               <form id="modificaDestinazione" name="modificaDestinazione" action="#" method="POST" style="display: inline;">
                  '.$destinazione->printEditForm($IDsensore).'
                  <br />
                  <input type="submit" value="Salva" />
               </form>
               '.HTML::getButtonAsLink('sensori.php?do=dettaglio&id='.$IDsensore, 'Annulla');



    } else {
        HTTP::redirect('index.php');
    }


require_once("footer.php");
