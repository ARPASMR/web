<?php
session_start();
require_once("__init__.php");

// ## Parametri GET ##
$toDo = isset($_GET['do']) ? $_GET['do'] : 'lista';
$IDutente = isset($_GET['id']) ? $_GET['id'] : '';

    require_once("header.php");

    // ###########################
    // #########  Lista  #########
    // ###########################
    if($toDo=='lista'){

        // Verifica permessi
        if($utente->LivelloUtente!="amministratore"){
            HTTP::redirect('index.php');
        }

        $utenti = new Utente();
        $utenti->getAll();
        print '<table id="listaUtenti" name="listaUtenti" class="lista tablesorter">
                    '.$utenti->printListTable().'
               </table>';

    }

    // ##############################
    // #########  Modifica  #########
    // ##############################
    elseif($toDo=="modifica"){

        // Verifica permessi
        if($utente->LivelloUtente!="amministratore" && $IDutente==''){
            HTTP::redirect('index.php');
        }

        // Inizializza utente
        $utenteObj = new Utente();
        $utenteObj->getByID($IDutente);

        // #### Salvataggio modifiche ####
        if(isset($_POST) && count($_POST)>0){
            $utenteObj->save($_POST);
            print '<p class="green">Salvataggio avvenuto correttamente.</p>
                    '.HTML::getButtonAsLink($_SERVER['SCRIPT_NAME'],
                    'Torna a Gestione Utenti');
            die();
        }

        // ## titolo pagina ##
        if($IDutente==''){
            print '<h2 class="first">Crea nuovo utente</h2>';
        } else if($IDutente==$_SESSION['IDutente']){
            print '<h2 class="first">Modifica profilo personale</h2>';
        } else {
            print '<h2 class="first">Modifica utente</h2>';
        }

        // Visualizza il form di modifica
        print '<form id="modificaUtente" name="modificaUtente" action="#" method="POST" style="display: inline;">
                  '.$utenteObj->printEditForm().'
                  <br />
                  <input type="submit" value="Salva" />
               </form>';
        print HTML::getButtonAsLink($_SERVER['HTTP_REFERER'], 'Annulla');

    }

    // ################################
    // #######  Disattivazione  #######
    // ################################
    elseif($toDo=="confermaDisattivazione"){

        // Verifica permessi
        if($utente->LivelloUtente!="amministratore"){
            HTTP::redirect('index.php');
        }

        // Inizializza utente
        $utenteObj = new Utente();
        $utenteObj->getByID($IDutente);

        print '<h1>Confermare disattivazione dell\'utente <span class="error">'.$utenteObj->__get('Cognome').' '.$utenteObj->__get('Nome').'</span> (#'.$IDutente.')?</h1>'
              .HTML::getButtonAsLink($_SERVER['SCRIPT_NAME'], 'Torna a Gestione Utenti')
              .HTML::getButtonAsLink($_SERVER['SCRIPT_NAME'].'?do=disattiva&id='.$IDutente, 'Disattiva');
    }
    elseif($toDo=="disattiva"){

        // Verifica permessi
        if($utente->LivelloUtente!="amministratore"){
            HTTP::redirect('index.php');
        }

        // Inizializza utente
        $utenteObj = new Utente();
        $utenteObj->getByID($IDutente);

        // Disattiva utente
        $utenteObj->disattiva();
        print '<p class="green">Utente Disattivto.</p>'
               .HTML::getButtonAsLink($_SERVER['SCRIPT_NAME'], 'Torna a Gestione Utenti');

    }




    else {
        HTTP::redirect('index.php');
    }


require_once("footer.php");