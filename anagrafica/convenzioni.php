<?php
session_start();
require_once("__init__.php");

// ## Parametri GET ##
$toDo = isset($_GET['do']) ? $_GET['do'] : 'modifica';
$IDconvenzione = isset($_GET['id']) ? $_GET['id'] : '';
$IDstazione = isset($_GET['IDstazione']) ? $_GET['IDstazione'] : '';

    require_once("header.php");

    // ##############################
    // #########  Modifica  #########
    // ##############################
    if($toDo=="modifica"){

        // verifica permessi
        if($utente->LivelloUtente!="amministratore"){
            HTTP::redirect('stazioni.php?do=dettaglio&id='.$IDstazione);
        }

        // Verifica parametri
        if($IDstazione=='' && $IDconvenzione==''){
            HTTP::redirect('stazioni.php');
        }

        // Inizializza Convenzione
        $convenzione = new Convenzione();
        $convenzione->getByID($IDconvenzione);

        // Salvataggio modifiche
        if(isset($_POST) && count($_POST)>0){
            $convenzione->save($_POST);
            print '<p class="green">Salvataggio avvenuto correttamente.</p>'
                .HTML::getButtonAsLink('stazioni.php?do=dettaglio&id='.$IDstazione, 'Torna a dettagli stazione');
            die();
        }

        // Titolo pagina
        print '<h2 class="first">Modifica convenzione</h2>';

        // Visualizza il form di modifica
        print '<form id="modificaConvenzione" name="modificaConvenzione" action="#" method="POST" style="display: inline;">
                  '.$convenzione->printEditForm($IDstazione).'
                  <br />
                  <input type="submit" value="Salva" />
               </form>';
        print HTML::getButtonAsLink($_SERVER['HTTP_REFERER'], 'Annulla');

    }


    else {
        HTTP::redirect('index.php');
    }


require_once("footer.php");
