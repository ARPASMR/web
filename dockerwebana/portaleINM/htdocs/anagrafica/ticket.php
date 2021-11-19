<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
session_start();
ob_start();
require_once("__init__.php");
require_once("header.php");

?>
<link rel="stylesheet" type="text/css" href="resources/external/jquery.datetimepicker.min.css"/>
<script language="javascript" type="text/javascript" src="resources/external/jquery.datetimepicker.full.min.js" ></script>
<script language="javascript" type="text/javascript" src="resources/js/Annotazioni.js"></script>
<?php
// ## Parametri GET ##
$toDo = isset($_GET['do']) ? $_GET['do'] : 'lista';
$IDannotazione = isset($_GET['id']) ? $_GET['id'] : '';
$IDsensore = isset($_GET['IDsensore']) ? $_GET['IDsensore'] : '';
$IDstazione = isset($_GET['IDstazione']) ? $_GET['IDstazione'] : '';
$Stazione = null;
$Sensori = null;
$DisabledString = $IDannotazione == null ? "" : "disabled";
$stazioneCheckString = '';
$sensoriCheckStrings = null;

// Check variables for saved values
$saved = false;
$itemsSaved = 0;
$expectedItems = 0;
$errors = false;

if($IDsensore <> ''){
	$Sensore = new Sensore();
	$Sensore = $Sensore->getById($IDsensore);
	$IDstazione = $Sensore[0]['IDstazione'];
}
if($toDo == "elimina"){
	$annotazioniIDs = explode(",", $IDannotazione);
	Annotazione::deleteByIds($annotazioniIDs);
	HTTP::redirect('stazioni.php?do=dettaglio&id='.$IDstazione);
	exit();
}

$Stazione = new Stazione();
$Stazione->getById($IDstazione);
$Sensore = new Sensore();
$IdSensori = $Sensore->getSensoriByStazione($IDstazione);
$Sensori = array(count($IdSensori));
$outcomes = array(count($IdSensori));
$sensoriCheckStrings = array(count($IdSensori));
for($i = 0; $i < count($IdSensori); $i++){
	$Sensori[$i] = $Sensore->getById($IdSensori[$i]);
	$Sensori[$i] = $Sensori[$i][0];
	$sensoriCheckStrings[$i] = $IDsensore == $Sensori[$i]['IDsensore'] ? 'checked' : '';
	$outcomes[$IdSensori[$i]] = true;
}
if(isset($_GET['IDstazione'])){
	$stazioneCheckString = 'checked';
	for($i = 0; $i < count($IdSensori); $i++){
		$sensoriCheckStrings[$i] = 'checked';
	}
}

    

    // ##############################
    // #########  Modifica  #########
    // ##############################
    if($toDo=="modifica"){
    	// Data corrente da utilizzare per tutti gli inserimenti/aggiornamenti
    	$dt = date("Y-m-d H:i:s");

        // ### Verifica permessi ###
        if($utente->LivelloUtente!="amministratore" && $utente->LivelloUtente!="gestoreDati"){
            if(isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER']!=''){
                HTTP::redirect($_SERVER['HTTP_REFERER']);
            } else {
                HTTP::redirect('index.php');
            }
        }

        // ### Salvataggio modifiche ###
        if(isset($_POST) && count($_POST)>0){
			//print('Inizio salvataggio:' . date_create()->format('Y-m-d H:i:s'));
			// Controllo se bisogna inserire in lista nera
			$isClosed = $_POST['Chiusura'] == 'SI';
			$inListaNera = isset($_POST['inListaNera']) && !$isClosed ? true : false;
			$listaNera = new ListaNera();
			unset($_POST['inListaNera']);
			
			// Divido parametri ticket
			$ticketParameters = array(
			"IDticket" => $_POST['IDticket'],
			"DataApertura" => $_POST['DataApertura'],
			"DataChiusura" => $_POST['DataChiusura'],
			"Priorita" => $_POST['Priorita']
			);
			
			$_POST = array_diff_key($_POST, $ticketParameters);
				
			$idAnnotazioniSalvate = array();
            // annotazione sensore
            if(!isset($_POST['IDstazioneCheck']) && isset($_POST['IDsensoreCheck']) && count($_POST['IDsensoreCheck']) > 0){
				unset($_POST['IDstazione']);
				$_POST["Stazione"] = 'NO';
				$IdSensori = $_POST['IDsensoreCheck'];
				unset($_POST['IDsensoreCheck']);
				//Aggiornamento e nuovi
				$expectedItems = count($IdSensori);
				for($i = 0; $i < count($IdSensori); $i++){
					$_POST['IDsensore'] = $IdSensori[$i];	
					//$IdSensori[$i];			
					$annotazione = new Annotazione();
					$isInListaNera = $listaNera->isSensoreInListaNera($_POST['IDsensore']);
					if($IDannotazione!==''){
						$annotazione->getByID($IDannotazione);
						$saved = $annotazione->save($_POST, $dt);
						array_push($idAnnotazioniSalvate, $IDannotazione);
						if($inListaNera && !$isInListaNera){
							$annotazione->aggiungiInListaNera();
						} else if($isClosed && $isInListaNera){
							$annotazione->rimuoviDaListaNera();
						}
						if( $saved === true )
						{
							$itemsSaved++;
						}
						else
						{
							$outcomes[$IdSensori[$i]] = false;
						}
					} else {
						$idNuovaAnnotazione = $annotazione->save($_POST);
						if( $idNuovaAnnotazione > 0 )
						{
							$itemsSaved++;
						}
						else
						{
							$outcomes[$IdSensori[$i]] = false;
						}
						$nuovaAnnotazione = new Annotazione();
						$nuovaAnnotazione->getByID($idNuovaAnnotazione);
						if($inListaNera && !$isInListaNera){
							$nuovaAnnotazione->aggiungiInListaNera();
						}
						array_push($idAnnotazioniSalvate, $idNuovaAnnotazione);
					}
					unset($annotazione);
				}
				if( $itemsSaved === $expectedItems )
				{
					if( $itemsSaved === 1 )
					{
					print '<p class="green">Sensore #'. $_POST['IDsensore'] .' - Salvataggio avvenuto correttamente.</p>'
							.HTML::getButtonAsLink('sensori.php?do=dettaglio&id='.$_POST['IDsensore'], 'Torna a dettagli sensore') . '<hr/>';
					}
					else
					{
						for($i = 0; $i < count($IdSensori); $i++)
						{
							print '<p class="green">Sensore #'. $IdSensori[$i] .' - Salvataggio avvenuto correttamente.</p>'
									.HTML::getButtonAsLink('sensori.php?do=dettaglio&id='.$IdSensori[$i], 'Torna a dettagli sensore') . '<hr/>';
						}
					}
				}
				else
				{
					$errors = true;
					if( count($IdSensori) === 1 )
					{
						print '<p class="red">Sensore #' . $_POST['IDsensore'] . ' - Errore nel salvataggio dei dati.</p>'
								.HTML::getButtonAsLink('sensori.php?do=dettaglio%id=' .$_POST['IdSensore'], 'Torna a dettagli sensore') . '<hr/>';
					}
					else
					{
						print '<p class="red">Saved items: '.$itemsSaved.' - Expected items: '.$expectedItems.'</p>';
						print '<p class="red">Errore nel salvataggio dei dati.</p>';
						foreach( $outcomes as $key => $value )
						{
							if( !$value )
							{
								print '<p class="red">Sensore #' . $key . ' - Errore nel salvataggio dei dati.</p>'
										.HTML::getButtonAsLink('sensori.php?do=dettaglio%id=' .$key, 'Torna a dettagli sensore') . '<hr/>';
							}
							else
							{
								print '<p class="green">Sensore #'. $key .' - Salvataggio avvenuto correttamente.</p>'
										.HTML::getButtonAsLink('sensori.php?do=dettaglio&id='.$key, 'Torna a dettagli sensore') . '<hr/>';
							}
						}
					}
				}
            }
            // annotazione stazione
            else if (isset($_POST['IDstazioneCheck']) && $_POST['IDstazioneCheck']!=''){
				$_POST["Stazione"] = 'SI';
				$_POST['IDstazione'] = $_POST['IDstazioneCheck'];
				unset($_POST['IDstazioneCheck']);
				unset($_POST['IDsensoreCheck']);
                $post = $_POST;

                // modifica
                if($IDannotazione!==''){
					unset($post['IDstazione']);
                    $annotazioneIDs = explode(",", $IDannotazione);
                    $expectedItems = count($annotazioneIDs);
                    foreach($annotazioneIDs as $annotazioneID){
                        $annotazione = new Annotazione();
                        $post['IDannotazione'] = $annotazioneID;
                        $annotazione->getByID($annotazioneID);
                        $saved = $annotazione->save($post, $dt);
						$isInListaNera = $listaNera->isSensoreInListaNera($annotazione->__get('IDsensore'));
						if($inListaNera && !$isInListaNera){
							$annotazione->aggiungiInListaNera();
						} else if($isClosed && $isInListaNera){
							$annotazione->rimuoviDaListaNera();
						}
						array_push($idAnnotazioniSalvate, $annotazioneID);
                        unset($annotazione);
                        if( $saved === True )
                        {
                        	$itemsSaved++;
                        }
                        else
                        {
                        	$outcomes[$annotazione['IDsensore']] = false;
                        }
                    }
                }
                // crea
                else {
                    $sensore = new Sensore();
                    $listaSensori = $sensore->getSensoriByStazione($post['IDstazione']);
                    unset($sensore, $post['IDstazione']);
                    $expectedItems = count($listaSensori);
                    foreach($listaSensori as $sensore){
                        $annotazione = new Annotazione();
                        $post['IDsensore'] = $sensore;
						$idNuovaAnnotazione = $annotazione->save($post, $dt);
						if( $idNuovaAnnotazione > 0 )
						{
							$saved = true;
						}
                        array_push($idAnnotazioniSalvate, $idNuovaAnnotazione);
						$annotazione->getByID($idNuovaAnnotazione);
						$isInListaNera = $listaNera->isSensoreInListaNera($annotazione->__get('IDsensore'));
						if($inListaNera && !$isInListaNera){
							$annotazione->aggiungiInListaNera();
						}
                        unset($annotazione);
                        if( $saved === true )
                        {
                        	$itemsSaved++;
                        }
                        else
                        {
                        	$outcomes[$annotazione['IDsensore']] = false;
                        }
                    }
                }
                
                if( $expectedItems === $itemsSaved )
                {
                	print '<p class="green">Salvataggio avvenuto correttamente.</p>'
                   			.HTML::getButtonAsLink('stazioni.php?do=dettaglio&id='.$IDstazione, 'Torna a dettagli stazione');
                }
                else
                {
                	$errors = true;
                	print '<p class="red">Errore nel salvataggio dei dati.</p>'
                        	.HTML::getButtonAsLink('stazioni.php?do=dettaglio&id='.$IDstazione, 'Torna a dettaglinstazione');
                	foreach( $outcomes as $key => $value )
                	{
                		if( $value )
                		{
                			print '<p class="green">Salvataggio annotazione sensore '.$key.' avvenuto correttamente.</p>';
                		}
                		else
                		{
                			print '<p class="red">Errore nel salvataggio dell\'annotazione relativa al sensore '.$key.'.</p>';
                		}
                	}
                }
            }
			
            if( !$errors )
            {
				// Salvo ticket se sono state salvate annotazioni
				if(count($idAnnotazioniSalvate) > 0){
					$ticketID = null;
					// Se ticket è valorizzato allora salvo
					$ticket = new Ticket();
					if( strpos($ticketParameters['DataChiusura'],'_') !== false) { unset($ticketParameters['DataChiusura']);}
					if( strpos($ticketParameters['DataApertura'],'_') === false && $ticketParameters['DataApertura'] != null && $ticketParameters['DataApertura'] != ''){
						if($ticketParameters['IDticket'] != null && $ticketParameters['IDticket'] != ''){ //Aggiorno
							$ticket->getByID($ticketParameters['IDticket']);
							$ticket->save($ticketParameters);
							$ticketID = $ticketParameters['IDticket'];
						} else { // Nuovo
							$ticketID = Ticket::getNuovoId();
							$ticket->save($ticketParameters);
						}
						unset($ticket);
					} else if($ticketParameters['IDticket'] != null && $ticketParameters['IDticket'] != ''){ // Altrimenti elimino ticket
						$ticket->getByID($ticketParameters['IDticket']);
						$ticket->delete();
						$ticketID = null;
					}
					
					
					// Assegno ticket a stazione
					if($ticketID != null){
						foreach($idAnnotazioniSalvate as $idAnnotazione){
							$annotazione = new Annotazione();
							$annotazione->getByID($idAnnotazione);
							$annotazione->setIDticketAndSave($ticketID);
						}
					}
				}
            }
            else
            {
            	print '<p class="red">Errore nel salvataggio dei dati.</p>';
            }
			//print('Fine salvataggio:' . date_create()->format('Y-m-d H:i:s'));
			die();
        }

        // ### Visualizza il form di modifica ###
        if($IDsensore!=''){
            $annotazione = new Annotazione();
            $annotazione->getByID($IDannotazione);
            // Titolo
            print '<h2 class="first">'.($IDannotazione!='' ? 'Modifica' : 'Crea nuova').' Annotazione</h2>';
        } else {
            $annotazioneIDs = explode(",", $IDannotazione);
            $annotazione = new Annotazione();
            $annotazione->getByID($annotazioneIDs[0]);
            // Titolo
            print '<h2 class="first">'.($IDannotazione!='' ? 'Modifica' : 'Crea nuova').' Annotazione</h2>';
        }
        $form = '</br><form onsubmit="enableCheckboxes()" id="modificaTicket" name="modificaTicket" action="#" method="POST" style="display: inline;"><table class="summary"><thead><tr><th>Stazione</th>';
		foreach($Sensori as $sensore){
			$form .= '<th>Sensore</th>';
		};
		$form .= '</tr></thead><tbody><tr><td>ID: '. $Stazione->__get('IDstazione') .'</td>';
			foreach($Sensori as $sensore){
			$form .= '<td>ID: '.$sensore['IDsensore'].'</br>Tipologia: '. $sensore['NOMEtipologia'] .'</td>';
		};
		$form .= '</tr><tr><td><input id="stazioneCheckbox" type="checkbox" value="'. $Stazione->__get('IDstazione') .'" '.$stazioneCheckString.' name="IDstazioneCheck" onclick="onCheckboxStazioneChange(event)" '.$DisabledString.'/></td>';
		foreach($Sensori as $key => $sensore){
			$form .= '<td><input class="sensoriCheckbox" type="checkbox" value="'.$sensore['IDsensore'].'" '.$sensoriCheckStrings[$key].' name="IDsensoreCheck[]" onclick="onCheckboxSensoreChange(event)" '.$DisabledString.'/></td>';	
		}
		$form .= '</tr></tbody></table></br>'.$annotazione->printEditForm($IDannotazione, $IDsensore, $IDstazione).'
                  </br>';
		// ### Visualizza form ticket ###
		$Ticket = new Ticket();
		$Ticket->getByIDannotazione($IDannotazione);
		$form .= $Ticket->getEditForm();
		$form .= '<br/><input type="submit" value="Salva" />
				   </form>'.HTML::getButtonAsLink($_SERVER['HTTP_REFERER'], 'Annulla');
	print($form);
    }

    else {
        HTTP::redirect('index.php');
    }

require_once("footer.php");
?>