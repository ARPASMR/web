<?php

    if(substr_count($_SERVER['SCRIPT_NAME'], 'stazioni.php')>0){
        $stazioni = new Stazione();
        $params = $stazioni->parseGET($_GET);
    }
    else if(substr_count($_SERVER['SCRIPT_NAME'], 'sensori.php')>0){
        $sensori = new Sensore();
        $params = $sensori->parseGET($_GET);
    }

?>

    <br /><br />

    <b>Filtri di Selezione:</b>
    <form id="filtroAnagrafica" name="filtroAnagrafica" action="<?php print $_SERVER['SCRIPT_NAME']; ?>" method="GET" class="menuContainer filtri">

        <!-- Selezione Lombardia/Extra -->
        <select id="regione" name="regione">
            <option value="ALL" <?php print ($params['regione']=="ALL") ? 'selected="selected"' : ''; ?> >Tutte</option>
            <option value="lombardia" <?php print ($params['regione']=="lombardia") ? 'selected="selected"' : ''; ?> >Lombardia</option>
            <option value="extra" <?php print ($params['regione']=="extra") ? 'selected="selected"' : ''; ?> >Extra Lombardia</option>
        </select><br />

        <!-- Selezione province -->
        <label for="provincia" id="label-provincia">Provincia:</label>
        <select id="provincia" name="provincia">
            <option value="ALL" <?php print ($params['provincia']=="ALL") ? 'selected="selected"' : ''; ?> >Tutte</option>
            <?php
            $obj = new Stazione();
            if($params['regione']=="ALL"){
                $listaProvince = $obj->getListaProvince();
            } else if($params['regione']=="lombardia"){
                $listaProvince = $obj->getListaProvince(true);
            } else if($params['regione']=="extra"){
                $listaProvince = $obj->getListaProvince(false);
            }
            foreach($listaProvince as $prov){
                print '<option value="'.$prov.'" ';
                print ($prov==$params['provincia']) ? 'selected="selected"' : '';
                print '>'.$prov.'</option>';
            }
            unset($obj, $listaProvince, $prov);
            ?>
        </select><br />

        <!-- Reti -->
        <label for="rete" id="label-rete">Rete:</label>
        <select id="rete" name="rete">
            <option value="ALL" <?php print ($params['rete']=="ALL") ? 'selected="selected"' : ''; ?> >Tutte</option>
            <option value="INM" <?php print ($params['rete']=="INM") ? 'selected="selected"' : ''; ?> >INM</option>
            <option value="CMG" <?php print ($params['rete']=="CMG") ? 'selected="selected"' : ''; ?> >CMG</option>
            <option value="RRQA" <?php print ($params['rete']=="RRQA") ? 'selected="selected"' : ''; ?> >RRQA</option>
            <option value="Altro" <?php print ($params['rete']=="Altro") ? 'selected="selected"' : ''; ?> >Altro</option>
        </select><br />

        <!-- Allerta -->
        <label for="allerta" id="label-allerta">Allerta:</label>
        <select id="allerta" name="allerta">
            <option value="ALL" <?php print ($params['allerta']=="ALL") ? 'selected="selected"' : ''; ?> >Tutte</option>
            <?php
            $obj = new Stazione();
            $listaAllerte = $obj->getListaAllerte();
            foreach($listaAllerte as $all){
                print '<option value="'.$all.'" ';
                print ($all==$params['allerta']) ? 'selected="selected"' : '';
                print '>'.$all.'</option>';
            }
            unset($obj, $listaAllerte, $all);
            ?>
        </select><br />

        <!-- Quota -->
        <label>Quota</label>
        <label for="quotaDa" id="label-quotaDa">da:</label>
        <input type="text" id="quotaDa" name="quotaDa" style="width: 40px;" value="<?php print $params['quotaDa']; ?>" />
        <label for="quotaA" id="label-quotaA">a:</label>
        <input type="text" id="quotaA" name="quotaA" style="width: 40px;" value="<?php print $params['quotaA']; ?>" />
        <br />


        <?php if(substr_count($_SERVER['SCRIPT_NAME'], 'stazioni.php')>0){ ?>

            <!-- Stazioni storiche -->
            <span class="legendaColor Storici"></span>
            <label for="stazioniStoriche" id="label-stazioniStoriche">Includi stazioni storiche:</label>
            <input type="checkbox" id="stazioniStoriche" name="stazioniStoriche" value="1"
                <?php print ($params['stazioniStoriche']=='1') ? 'checked="checked"' : ''; ?> /><br />


            <!-- Annotazioni Aperte -->
            <span class="legendaColor AnnotazioniAperte"></span>
            <label for="soloAnnotazioniAperte" id="label-soloAnnotazioniAperte">Solo con Annotazioni aperte:</label>
            <input type="checkbox" id="soloAnnotazioniAperte" name="soloAnnotazioniAperte" value="1"
                <?php print ($params['soloAnnotazioniAperte']=='1') ? 'checked="checked"' : ''; ?> /><br />
				
			<!-- Ticket Aperti -->
            <span class="legendaColor TicketAperti"></span>
            <label for="soloTicketAperti" id="label-soloTicketAperti">Solo con Ticket aperti:</label>
            <input type="checkbox" id="soloTicketAperti" name="soloTicketAperti" value="1"
                <?php print ($params['soloTicketAperti']=='1') ? 'checked="checked"' : ''; ?> /><br />


        <?php } else if(substr_count($_SERVER['SCRIPT_NAME'], 'sensori.php')>0){ ?>

            <!-- Tipologia -->
            <label for="tipologia" id="label-tipologia">Tipologia:</label>
            <?php print Sensore::dropdownListNOMEtipologia('tipologia', $params['tipologia']) ?>
            <br />

            <!-- Sensori storici -->
            <span class="legendaColor Storici"></span>
            <label for="sensoriStorici" id="label-sensoriStorici">Includi sensori storici:</label>
            <input type="checkbox" id="sensoriStorici" name="sensoriStorici" value="1"
                <?php print ($params['sensoriStorici']=='1') ? 'checked="checked"' : ''; ?> /><br />
            
            <!-- Solo Lista Nera -->
            <span class="legendaColor ListaNera"></span>
            <label for="soloListaNera" id="label-soloListaNera">Solo in Lista Nera:</label>
            <input type="checkbox" id="soloListaNera" name="soloListaNera" value="1"
                <?php print ($params['soloListaNera']=='1') ? 'checked="checked"' : ''; ?> /><br />

            <!-- Annotazioni Aperte -->
            <span class="legendaColor AnnotazioniAperte"></span>
            <label for="soloAnnotazioniAperte" id="label-soloAnnotazioniAperte">Solo con Annotazioni aperte:</label>
            <input type="checkbox" id="soloAnnotazioniAperte" name="soloAnnotazioniAperte" value="1"
                <?php print ($params['soloAnnotazioniAperte']=='1') ? 'checked="checked"' : ''; ?> /><br />
				
			<!-- Ticket Aperti -->
            <span class="legendaColor TicketAperti"></span>
            <label for="soloTicketAperti" id="label-soloTicketAperti">Solo con Ticket aperti:</label>
            <input type="checkbox" id="soloTicketAperti" name="soloTicketAperti" value="1"
                <?php print ($params['soloTicketAperti']=='1') ? 'checked="checked"' : ''; ?> /><br />


        <?php } ?>

        <!-- Stazione Assegnate -->
        <?php if($utente->LivelloUtente!=null){ ?>
            <label for="soloAssegnate" id="label-soloAssegnate">Mostra:</label>
            <select id="soloAssegnate" name="soloAssegnate">
                <option value="on" <?php print ($params['soloAssegnate']=='on') ? 'selected="selected"' : ''; ?> >Solo assegnate</option>
                <option value="off" <?php print ($params['soloAssegnate']=='off') ? 'selected="selected"' : ''; ?> >Tutte</option>
            </select>
        <?php } ?>

    </form>
