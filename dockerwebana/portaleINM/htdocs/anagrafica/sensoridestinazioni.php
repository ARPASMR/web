<?php

session_start();
ob_start();
require_once("__init__.php");

require_once("header.php");

    // ###########################
    // #########  Lista  #########
    // ###########################

    print '<h2 class="first">Legenda Destinazioni>';

    $sensoridestinazioni = new SensoriDestinazione();
    $sensoridestinazioni->getAll();
    print '<table id="listaDestinazioni" name="listaDestinazioni" class="lista tablesorter">
                '.$sensoridestinazioni->printListTable().'
           </table>';
    Debug::printExecutionTime('print tabella lista');


require_once("footer.php");
