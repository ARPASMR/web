<?php
    session_start();
    ob_start();
    require_once("__init__.php");

    // ## Parametri GET ##
    $toDo = isset($_GET['do']) ? $_GET['do'] : 'lista';
    $IDsensore = isset($_GET['id']) ? $_GET['id'] : '';
    $IDstazione = ($IDsensore=='' && isset($_GET['IDstazione'])) ? $_GET['IDstazione'] : '';

    require_once("header.php");
    // ###########################
    // #########  Lista  #########
    // ###########################
    if($toDo=='lista'){
        $sensori = new Sensore();
        /* print '<table id="listaSensori" name="listaSensori" class="lista tablesorter">
                    '.$sensori->printListTable($params).'
               </table>'; */
        print $sensori->printListTable($params);
        print  '<script>
                    $(document).ready(function(){
                        aggiornaFiltri();
                        $("form#filtroAnagrafica input, form#filtroAnagrafica select").on("change", function(){
                            if($(this).attr("id")=="regione"){
                                $("select#provincia").val("ALL");
                            }
                            aggiornaFiltri();
                            aggiornaAnagrafica();
                        });
        				$.tablesorter.defaults.widgets = ["zebra"]; 
		    			$.tablesorter.defaults.widthFixed = true;
        				$("#listasensori").tablesorter();
        				var $count = $("#sensorsCount"),
    						$t = $("#listaSensori"),
    						$tr = $t.find("tbody tr"),
    						update = function(){
        						var t = $tr.filter(":visible").length;
        						$count.html(t);
    						};
        					$t.on("filterEnd", function () {
        						update();
    						})
    						.tablesorter({
        						widgets: ["filter"],
        						initialized: function(){
           							update();
        						}
    						});
                    });
               </script>';
        Debug::printExecutionTime('print tabella lista');

    }

    // ###############################
    // #########  Dettaglio  #########
    // ###############################
    elseif($toDo=="dettaglio"){

        // ERRORE se ID non è fornito
        if($IDsensore==''){
            print '<p class="error">Nessun ID fornito.</p>';
        }
        else {

            $sensore = new Sensore();
            $sensore->getByID($IDsensore);
            $IDstazione = $sensore->__get('IDstazione');
            $listaNera = new ListaNera();

            print '<table style="margin: 5px 5px 5px 0px;">';
            print '<tr>';
            print '<td rowspan="2">';
            // Visualizza i dettagli del sensore
            print '<h2 class="first">Dettaglio sensore</h2>'
                   .$listaNera->isSensoreInListaNera($IDsensore)
                   .$sensore->printSummaryTable();
            Debug::printExecutionTime('print dettagli sensore');
            print '</td>';
            print '<td valign="top">';
            // Visualizza le specifiche del sensore
            $specifiche = new SensoreSpecifiche();
            $specifiche->getBySensore($IDsensore);
            print '<h2 class="first">Specifiche Strumenti</h2>
                    <table class="lista" style="margin: 5px 5px 5px 0px;">
                        '.$specifiche->printListTable().'
                    </table>';
            unset($specifiche);
            Debug::printExecutionTime('print SensoreSpecifiche');
            
            print '</td>';
            print '</tr>';
            print '<tr>';
            print '<td>';
            // Visualizza i dettagli della stazione
            $stazione = new Stazione();
            $stazione->getByID($IDstazione);
            print '<h2>Stazione</h2>
                   '.$stazione->printSummaryTable(true);
            Debug::printExecutionTime('print Stazione');
            print '</td>';
            print '</tr>';
            print '</table>';

            print '<table>';
            print '<tr>';
            print '<td>';
	    	// Visualizza storico delle Destinazioni
            $sensoridestinazioni = new SensoriDestinazione();
            $sensoridestinazioni->getBySensore($IDsensore);
            print '<h2 class="first">Destinazioni</h2>
                   <table class="lista tablesorter" style="margin: 5px 5px 5px 0px;">
                    '.$sensoridestinazioni->printListTable($IDsensore).'
                   </table>';
            unset($sensoridestinazioni);
            Debug::printExecutionTime('print Destinazione');
            print '</td>';
            print '<td valign="top">';

            // Visualizza storico della Lista Nera
            $listaNera = new ListaNera();
            $listaNera->getStoricoBySensore($IDsensore);
            print '<h2 class="first">Lista Nera</h2>
                   <table class="lista tablesorter" style="margin: 5px 5px 5px 0px;">
                    '.$listaNera->printListTable($IDsensore).'
                   </table>';
            Debug::printExecutionTime('print ListaNera');
            print '</td>';
            print '</tr>';
            print '</table>';

            // Visualizza storico delle Annotazioni (Monitoraggio)
            $annotazioni = new Annotazione();
            $annotazioni->getBySensore($IDsensore);
            if(!$annotazioni->isEmpty()){
                print '<h2>Storico Annotazioni</h2>
                       <table id="listaAnnotazioni" name="listaAnnotazioni" class="lista tablesorter">
                        '.$annotazioni->printListTable($IDstazione).'
                       </table>';
            }
            unset($annotazioni);
            Debug::printExecutionTime('print Annotazione');
        }

    }

    // ##############################
    // #########  Modifica  #########
    // ##############################
    elseif($toDo=="modifica"){

        // Verifica permessi
        if($utente->LivelloUtente!="amministratore"){
            HTTP::redirect($_SERVER['SCRIPT_NAME'].'?do=dettaglio&id='.$IDsensore);
        }

        // Inizializza sensore
        $sensore = new Sensore();
        $sensore->getByID($IDsensore);

        // Salvataggio modifiche
        if(isset($_POST) && count($_POST)>0){
			$sensore->save($_POST);
            if($sensore != null && $sensore->getID() > 0){
                print '<p class="green">Salvataggio avvenuto correttamente.</p>'
                    .HTML::getButtonAsLink($_SERVER['SCRIPT_NAME'].'?do=dettaglio&id='.$sensore->getID(), 'Dettagli sensore');
            } else {
				if($IDsensore != null && $IDsensore > 0){
					print '<p class="red">Errore nel salvataggio.</p>'
					.HTML::getButtonAsLink($_SERVER['SCRIPT_NAME'].'?do=modifica&id='.$IDsensore, 'Torna indietro');
				} else {
					print '<p class="red">Errore nel salvataggio.</p>'
					.HTML::getButtonAsLink($_SERVER['SCRIPT_NAME'].'?do=modifica', 'Torna indietro');
				}
				
            }
            die();
        }

        // Titolo pagina
        if($IDsensore!=''){
            print '<h2 class="first">Modifica sensore</h2>';
        } else {
            print '<h2 class="first">Crea nuovo sensore</h2>';
        }

        // Visualizza il form di modifica
        print '<form id="modificaSensore" name="modificaSensore" action="#" method="POST" style="display: inline;">
                  '.$sensore->printEditForm($IDstazione).'
                  <br />
                  <input type="submit" value="Salva" />
               </form>';
        print HTML::getButtonAsLink($_SERVER['HTTP_REFERER'], 'Annulla');

    }

    // ###############################
    // #########  CSV & XLS  #########
    // ###############################
    elseif($toDo=="csv" || $toDo=="xls"){
        $sensori = new Sensore();
        $params = $sensori->parseGET($_GET);
        //$params['ids'] = $_POST['ids'];
		if($params['soloTicketAperti'] == '0'){
			$sensori->getByParams($params, 'ALL');
		} else {
			$sensori->getByParams($params, 'TABLELIST_TICKET');
		}
		
        if($toDo=="csv"){
            $filename = 'sensori.csv';     
			$output = $sensori->generateCSV();
            ob_clean();
            header ("Content-Type: text/csv; charset=utf-8");
            header ("Content-Disposition: attachment; filename=".$filename);
            echo $output;
        }
        elseif($toDo=="xls"){
            $filename = 'sensori.xls';
            ob_clean();
            header('Content-Type: application/octet-stream');
            header("Content-Transfer-Encoding: Binary");
            header("Content-disposition: attachment; filename=".$filename);
            $sensori->generateXLS();

        }
        ob_flush();
        die();
    }



    else {
        HTTP::redirect('index.php');
    }


require_once("footer.php");