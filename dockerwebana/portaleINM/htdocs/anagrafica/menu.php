
    <h1>Archivio Misure Meteorologiche @ Sinergico</h1>

    <span class="labelMenuContainer">Consultazione Anagrafica</span>
    <div class="menuContainer" style="font-size: 10pt;">
        - <a href="stazioni.php">Stazioni</a><br />
        - <a href="sensori.php">Sensori</a><br />
        &nbsp;&nbsp;&nbsp; - <a href="tipologie.php">Tipologie</a><br />
        &nbsp;&nbsp;&nbsp; - <a href="destinazioni.php">Destinazioni</a><br />
        &nbsp;&nbsp;&nbsp; - <a href="classificazioni.php">Classificazioni</a>
    </div>

    <?php
        $toDo = (isset($toDo)) ? $toDo : '';


        // ##### Filtri di selezione ANAGRAFICA #####
        if($toDo=='lista'
            && (substr_count($_SERVER['SCRIPT_NAME'], 'stazioni.php')>0
                || substr_count($_SERVER['SCRIPT_NAME'], 'sensori.php')>0)){
            include_once('menu.filtriAnagrafica.php');
        }

        $azioni='';


        //  ###########################
        //  ########  Stazioni  #######
        //  ###########################
        if(substr_count($_SERVER['SCRIPT_NAME'], 'stazioni.php')>0){

            if($toDo=='lista'){
                $azioni .= '<input type="button" onclick="esportaAnagrafica(\'csv\');" value="Genera CSV" /><br />
                            <input type="button" onclick="esportaAnagrafica(\'xls\');" value="Genera XLS" />';
                if($utente->LivelloUtente=="amministratore"){
                    $azioni .= '<br />'.HTML::getButtonAsLink($_SERVER['SCRIPT_NAME'].'?do=modifica', 'Crea nuova stazione');
                }
            }
            elseif($toDo=='dettaglio'){
                if($utente->LivelloUtente=="amministratore"){
                    $azioni .= HTML::getButtonAsLink($_SERVER['SCRIPT_NAME'].'?do=modifica&id='.$_GET['id'], 'Modifica stazione').'<br />
		                       '.HTML::getButtonAsLink('convenzioni.php?do=modifica&IDstazione='.$_GET['id'], 'Crea nuova convenzione').'<br />
		                       '.HTML::getButtonAsLink('sensori.php?do=modifica&IDstazione='.$_GET['id'], 'Crea nuovo sensore').'<br />';
                }
                if($utente->LivelloUtente=="amministratore" || $utente->LivelloUtente=="gestoreDati"){
                    $azioni .= HTML::getButtonAsLink('ticket.php?do=modifica&IDstazione='.$_GET['id'], 'Crea Annotazione').'';
                }
            }
            elseif($toDo=='modifica' && isset($_GET['id'])){
                $azioni .= HTML::getButtonAsLink($_SERVER['SCRIPT_NAME'].'?do=dettaglio&id='.$_GET['id'], 'Dettagli stazione');
            }

        }

        //  ##########################
        //  ######  Convenzioni  #####
        //  ##########################
        if(substr_count($_SERVER['SCRIPT_NAME'], 'convenzioni.php')>0){
            if($toDo=='modifica'){
                if($IDstazione=='' || !isset($IDstazione)){
                    $convenzione = new Convenzione();
                    $convenzione->getByID($_GET['id']);
                    $linkId = $convenzione->__get('IDstazione');
                    unset($convenzione);
                } else {
                    $linkId = $IDstazione;
                }
                $azioni .= HTML::getButtonAsLink('stazioni.php?do=dettaglio&id='.$linkId, 'Dettagli stazione');
            }
        }

        //  ##########################
        //  ########  Sensori  #######
        //  ##########################
        if(substr_count($_SERVER['SCRIPT_NAME'], 'sensori.php')>0){

            if($toDo=='lista'){
                $azioni .= '<input type="button" onclick="esportaAnagrafica(\'csv\');" value="Genera CSV" /><br />
                            <input type="button" onclick="esportaAnagrafica(\'xls\');" value="Genera XLS" />';
                if($utente->LivelloUtente=="amministratore"){
                    $azioni .= '<br />'.HTML::getButtonAsLink($_SERVER['SCRIPT_NAME'].'?do=modifica', 'Crea nuovo sensore');
                }
            }
            elseif($toDo=='dettaglio'){
                $sensore = new Sensore();
                $sensore->getByID($_GET['id']);
                $IDstazione = $sensore->__get('IDstazione');
                unset($sensore);
                $azioni .= HTML::getButtonAsLink('stazioni.php?do=dettaglio&id='.$IDstazione, 'Dettagli stazione');
                if($utente->LivelloUtente=="amministratore"){
                    $azioni .= HTML::getButtonAsLink($_SERVER['SCRIPT_NAME'].'?do=modifica&id='.$_GET['id'], 'Modifica sensore').'<br />'
                               .HTML::getButtonAsLink('strumenti.php?do=modifica&IDsensore='.$_GET['id'], 'Crea nuovo strumento').'<br />';
                }
                if($utente->LivelloUtente=="amministratore" || $utente->LivelloUtente=="gestoreDati"){
                    $azioni .= HTML::getButtonAsLink('ticket.php?do=modifica&IDsensore='.$_GET['id'], 'Crea Annotazione').'';
                }
            }
            elseif($toDo=='modifica' && array_key_exists('id', $_GET)){
                $azioni .= HTML::getButtonAsLink($_SERVER['SCRIPT_NAME'].'?do=dettaglio&id='.$_GET['id'], 'Dettagli sensore');
            }

        }

        //  ##########################
        //  #######  Strumenti  ######
        //  ##########################
        if(substr_count($_SERVER['SCRIPT_NAME'], 'strumenti.php')>0){
            if($toDo=='modifica'){
                if($IDsensore=='' || !isset($IDsensore)){
                    $strumento = new SensoreSpecifiche();
                    $strumento->getByID($_GET['id']);
                    $linkId = $strumento->__get('IDsensore');
                    unset($sensore);
                } else {
                    $linkId = $IDsensore;
                }
                $azioni .= HTML::getButtonAsLink('sensori.php?do=dettaglio&id='.$linkId, 'Dettagli sensore');
                unset($linkId);
            }
        }


        //  #########################
        //  ########  Utenti  #######
        //  #########################
        else if(substr_count($_SERVER['SCRIPT_NAME'], 'utenti.php')>0
                && $utente->LivelloUtente=="amministratore"){
            $azioni .= HTML::getButtonAsLink('utenti.php?do=modifica', 'Crea nuovo utente');
        }


            if($azioni!=''){
                print '<span class="labelMenuContainer">Azioni Disponibili:</span>
                               <div class="menuContainer">
                                    '.$azioni.'
                               </div>';
            }


        //  ##########################
        //  ######  Manutentori  #####
        //  ##########################
	print '<span class="labelMenuContainer">Manutentori:</span>
               <div class="menuContainer">
                  -  CAE (<a target="_blank" href="https://portale.cae.it/it/account/login">ticket</a>, dati)<br />
                  -  Project Automation (<a target="_blank" href="http://callcenter.p-a.it/">ticket</a>, <a target="_blank" href="https://ecomanager.arpalombardia.it/ecomanagerwebpg/">dati</a>)<br />
                  -  Corr-tek (<a target="_blank" href="http://www.corr-tek.it/web/ott_i.nsf/id/pa_myott_i.html">ticket, dati</a>)<br />
                  -  ETG (<a target="_blank" href="https://etgfirenzecloud.com:3030/etgpga/login.asp">ticket</a>, <a target="_blank" href="http://nim.arpalombardia.it:8081/winnet6/Index.php">dati</a>)<br />
                  <!-- -  <a target="_blank" href="http://backup.naturalert.com:8080/#/login">EnvEve</a> -->
               </div>';

        //  ##########################
        //  ########## Links  ########
        //  ##########################
        print '<span class="labelMenuContainer">Links:</span>
               <div class="menuContainer">
                  -  <a target="_blank" href="https://arpalombardia-my.sharepoint.com/:x:/g/personal/m_ranci_arpalombardia_it/ESlry4EYdtJLqHTfCofwopUBetIYkzlflRafWmFRx0aFiQ?e=M2pRZt">Collaudo per PC</a><br />
                  -  <a target="_blank" href="https://arpalombardia.sharepoint.com/:f:/r/sites/grpsc-sopralluoghiretiinm/Rete%20di%20Monitoraggio/Schede_stazioni?csf=1&e=aL2w1S">Schede Stazioni</a><br />
                  -  <a target="_blank" href="http://10.10.0.6/applications/convenzioni/">Stato Convenzioni</a><br />
                  -  <a target="_blank" href="http://10.10.0.6/applications/ortofoto_stazioni/">Foto e Ortofoto Stazioni</a><br>
				  -  <a target="_blank" href="http://10.10.0.6/applications/controlli">Controlli</a>
               </div>';

        //  ##########################
        //  ###### Login Utente  #####
        //  ##########################
        print '<span class="labelMenuContainer">Area Riservata:</span>
               <div class="menuContainer">
                '.$utente->stampaLogin().'
               </div>';






