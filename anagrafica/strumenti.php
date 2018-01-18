<?php
session_start();
require_once("__init__.php");

// ## Parametri GET ##
$toDo = isset($_GET['do']) ? $_GET['do'] : 'modifica';
$IDstrumento = isset($_GET['id']) ? $_GET['id'] : '';
$IDsensore = isset($_GET['IDsensore']) ? $_GET['IDsensore'] : '';

    require_once("header.php");

    // ##############################
    // #########  Modifica  #########
    // ##############################
    if($toDo=="modifica"){

        // verifica permessi
        if($utente->LivelloUtente!="amministratore"){
            HTTP::redirect('sensori.php?do=dettaglio&id='.$IDsensore);
        }

        // Verifica parametri
        if($IDsensore=='' && $IDstrumento==''){
            HTTP::redirect('sensori.php');
        }

        // Inizializza Strumento
        $strumento = new SensoreSpecifiche();
        $strumento->getByID($IDstrumento);

        // Salvataggio modifiche
        if(isset($_POST) && count($_POST)>0){
            $strumento->save($_POST);
            print '<p class="green">Salvataggio avvenuto correttamente.</p>'
                .HTML::getButtonAsLink('sensori.php?do=dettaglio&id='.$IDsensore, 'Torna a dettagli sensore');
            die();
        }

        // Titolo pagina
        print '<h2 class="first">Modifica strumento</h2>';

        // Visualizza il form di modifica
        print '<form id="modificaStrumento" name="modificaStrumento" action="#" method="POST" style="display: inline;">
                  '.$strumento->printEditForm($IDsensore).'
                  <br />
                  <input type="submit" value="Salva" />
               </form>';
        print HTML::getButtonAsLink($_SERVER['HTTP_REFERER'], 'Annulla');


    }


    else {
        HTTP::redirect('index.php');
    }


require_once("footer.php");
