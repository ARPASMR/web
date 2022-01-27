<?php

session_start();
ob_start();
require_once("__init__.php");

require_once("header.php");

// ###############################
// #########  Lista  #########
// ###########################

print '<h2 class="first">Legenda Destinazioni</h2>';

$destinazione = new Destinazione();
$destinazione->getAll();
print '<table id="listaDestinazioni" name="listaDestinazioni" class="lista tablesorter">
                './*$destinazione->printListTable()*/$destinazione->printLegendaDestinazioni().'
           </table>';
Debug::printExecutionTime('print tabella lista');


require_once("footer.php");