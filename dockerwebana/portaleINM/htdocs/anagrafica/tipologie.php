<?php

session_start();
ob_start();
require_once("__init__.php");

require_once("header.php");

    // ###########################
    // #########  Lista  #########
    // ###########################

    print '<h2 class="first">Legenda Tipologie</h2>';

    $tipologie = new Tipologia();
    $tipologie->getAll();
    print '<table id="listaTipologie" name="listaTipologie" class="lista tablesorter">
                '.$tipologie->printListTable().'
           </table>';
    Debug::printExecutionTime('print tabella lista');


require_once("footer.php");