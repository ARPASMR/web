<?php

session_start();
ob_start();
require_once("__init__.php");

require_once("header.php");

    // ###########################
    // #########  Lista  #########
    // ###########################

    print '<h2 class="first">Legenda Classificazioni</h2>';

    $classificazioni = new Classificazione();
    $classificazioni->getAll();
    print '<table id="listaClassificazioni" name="listaClassificazioni" class="lista tablesorter">
                '.$classificazioni->printListTable().'
           </table>';
    Debug::printExecutionTime('print tabella lista');


require_once("footer.php");
