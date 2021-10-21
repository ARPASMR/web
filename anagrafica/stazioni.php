<?php

    session_start();
    ob_start();
    require_once("__init__.php");

    // ## Parametri GET ##
    $toDo = isset($_GET['do']) ? $_GET['do'] : 'lista';
    $IDstazione = isset($_GET['id']) ? $_GET['id'] : '';

    require_once("header.php");

    // ###########################
    // #########  Lista  #########
    // ###########################
    if($toDo=='lista'){

        $stazioni = new Stazione();
        //print '<table id="listaStazioni" name="listaStazioni" class="lista tablesorter">
        //            '.$stazioni->printListTable($params).'
        //       </table>
        //       <script>
        //            $(document).ready(function(){
        //                aggiornaFiltri();
        //                $("form#filtroAnagrafica input, form#filtroAnagrafica select").on("change", function(){
        //                    if($(this).attr("id")=="regione"){
        //                        $("select#provincia").val("ALL");
        //                    }
        //                    aggiornaFiltri();
        //                    aggiornaAnagrafica();
        //                });
        //            });
        //       </script>';
        print $stazioni->printListTable($params);
		print '<script>
                    $(document).ready(function(){
                        aggiornaFiltri();
                        $("form#filtroAnagrafica input, form#filtroAnagrafica select").on("change", function(){
                            if($(this).attr("id")=="regione"){
                                $("select#provincia").val("ALL");
                            }
                            aggiornaFiltri();
                            aggiornaAnagrafica();
                        });
						var $count = $("#stationsCount"),
    						$t = $("#listaStazioni"),
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

        // Errore se ID non è fornito
        if($IDstazione==''){
            print '<p class="error">Nessun ID fornito.</p>';
        }
        else {

            // Visualizza i dettagli della stazione
            $stazione = new Stazione();
            $stazione->getByID($IDstazione);
            print '<table colums="2"><tr><td valign="top">';
            print '<h2 class="first">Dettaglio stazione</h2>';

            // utenti a cui la stazione è assegnata
            $stazioneAssegnate = new StazioniAssegnate();
            print 'Assegnata a: '.$stazioneAssegnate->getUtentiByStazione($IDstazione).'
                     <br /><br />';;
            unset($stazioneAssegnate);

            // dettagli della stazione
            print $stazione->printSummaryTable();
            unset($stazione);
            print '</td><td valign="top">';
            Debug::printExecutionTime('print dettagli stazione');

            // Visualizza tutti i sensori della stazione
            $sensori = new Sensore();
            $sensori->getByStazione($IDstazione);
            print '<h2 class="first">Sensori</h2>&nbsp;&nbsp;<br /><br />
                   <table class="lista tablesorter" style="margin: 5px 5px 5px 0px;">
                    '.$sensori->printCompactListTable().'
                   </table>';
            Debug::printExecutionTime('print Sensore');
            print '</td></tr></table>';	
            
            // Visualizza le convenzioni della stazione
            $convenzione = new Convenzione();
            $convenzione->getByStazione($IDstazione);
            if(!$convenzione->isEmpty()){
            	print '<h2>Convenzione Proprieta&#768;</h2>
                       <table class="lista tablesorter">
                        '.$convenzione->printListTable().'
                       </table>';
            }
            unset($convenzione);
            Debug::printExecutionTime('print Convenzione');

            // Visualizza storico delle Annotazioni (Monitoraggio)
            $annotazioni = new Annotazione();
            $annotazioni->getByStazione($IDstazione);
            
            if(!$annotazioni->isEmpty()){
                print '<h2>Storico Annotazioni</h2>
                       <table class="lista tablesorter" id="listaAnnotazioni">
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
            HTTP::redirect($_SERVER['SCRIPT_NAME'].'?do=dettaglio&id='.$IDstazione);
        }

        // Inizializza stazione
        $stazione = new Stazione();
        $stazione->getByID($IDstazione);

        // Salvataggio modifiche
        if(isset($_POST) && count($_POST)>0){
			$stazione->save($_POST);
            if($stazione != null && $stazione->getID() > 0) {
                print '<p class="green">Salvataggio avvenuto correttamente.</p>
                    ' . HTML::getButtonAsLink($_SERVER['SCRIPT_NAME'] . '?do=dettaglio&id=' . $stazione->getID(),
                        'Dettagli stazione');
            } else {
				$id = $stazione->getID();
				if($id > 0){
					print HTML::getButtonAsLink($_SERVER['SCRIPT_NAME'].'?do=modifica&id='.$id, 'Torna indietro');
				} else {
					print HTML::getButtonAsLink($_SERVER['SCRIPT_NAME'].'?do=modifica', 'Torna indietro');
				}
            }
            die();
        }

        // Titolo pagina
        if($IDstazione!=''){
            print '<h2 class="first">Modifica stazione</h2>';
        } else {
            print '<h2 class="first">Crea nuova stazione</h2>';
        }

        // Visualizza il form di modifica
        print '<form id="modificaStazione" name="modificaStazione" action="#" method="POST" style="display: inline;">
                  '.$stazione->printEditForm().'
                  <br />
                  <input type="submit" value="Salva" />
               </form>';
        print HTML::getButtonAsLink($_SERVER['HTTP_REFERER'], 'Annulla');

    }

    // ###############################
    // #########  CSV & XLS  #########
    // ###############################
    elseif($toDo=="csv" || $toDo=="xls"){

        $stazioni = new Stazione();
        $params = $stazioni->parseGET($_GET);
        $params['ids'] = $_POST['ids'];
		if($params['soloTicketAperti'] == '0'){
			$stazioni->getByParams($params, 'ALL');
		} else {
			$stazioni->getByParams($params, 'TABLELIST_TICKET');
		}

        if($toDo=="csv"){
            $filename = $title.'stazioni.csv';
            $output = $stazioni->generateCSV();
            ob_clean();
            header ("Content-Type: application/csv");
            header ("Content-disposition: attachment; filename=".$filename);
            echo $output;
        }
        elseif($toDo=="xls"){
            $filename = 'stazioni.xls';
            ob_clean();
            header('Content-Type: application/octet-stream');
            header("Content-Transfer-Encoding: Binary");
            header("Content-disposition: attachment; filename=".$filename);
            $stazioni->generateXLS();
        }
        ob_flush();
        die();

    }


    else {
        HTTP::redirect('index.php');
    }


require_once("footer.php");
