<?php

    class Utente extends GenericEntity {

        protected $DBTable = 'Utenti';
        protected $IDfield = 'IDutente';

        public $IDutente;
        public $Autenticazione;

        protected $lastUpdateDateField = null;
        protected $lastUpdateUserField = null;

        public $Nome;
        public $Cognome;
        public $Email;
        public $LivelloUtente=null;

        public function __construct($IDutente=''){
            parent::__construct();
            if($IDutente!=''){
                $this->getByID($IDutente);
                $this->getInfoUtente();
            }
        }

        /**
         * Estra l'utente dal DB in base all'ID
         * @param $IDutente
         * @return mixed|void
         */
        public function getByID($IDutente){
            parent::getByID($IDutente);
            $this->IDutente = $IDutente;
        }

        /**
         * Verifica che la email (username) inserita sia corretta
         * @param string $email
         */
        private function getByEmail($email=''){
            if($email!=''){
                $this->getByField('Email',$email);
                if(count($this->List)>0){
                    $this->IDutente = $this->List[0]['IDutente'];
                }
            }
        }

        /**
         * Check if the email already exist in the DB
         * @param string $email
         * @return string
         */
        public function checkIfEmailExist($email=''){
            $this->getByField('Email', $email);
            if(count($this->List)>0){
                return 'false';
            } else {
                return 'true';
            }
        }

            /**
             * Ottiene le informazioni dell'utente
             */
            private function getInfoUtente(){
                $this->IDutente = $this->List[0]['IDutente'];
                $this->Nome = $this->List[0]['Nome'];
                $this->Cognome = $this->List[0]['Cognome'];
                $this->Email = $this->List[0]['Email'];
                $this->LivelloUtente = $this->List[0]['LivelloUtente'];
            }

        public function getDenominazione(){
            $item = $this->List[0];
            return '#'.$item['IDutente'].' '.$item['Cognome'].' '.$item['Nome'];
        }
        public function getNome(){
            $item = $this->List[0];
            return $item['Nome'].' '.$item['Cognome'];
        }

        /**
         * Disattiva utente (elimina password e livello)
         */
        function disattiva(){
            $values = array('Password'=>NULL, 'LivelloUtente'=>NULL);
            $conditions = array($this->IDfield => $this->{$this->IDfield});
            $this->update($values, $conditions);
        }

        /**
         * Verifica le credenziali ed effutta il login
         * @param $email
         * @param $password
         */
        public function Autentica($email, $password){
            if ($email=='' && $password==''){
                $this->Autenticazione = "EMPTYFIELDS";
            }
            else if ($email=='' || $password==''){
                $this->Autenticazione = "MISSINGFIELD";
            } else {
                $this->getByEmail($email);
                // # username not correct #
                if($this->IDutente==''){
                    $this->Autenticazione = "WRONGUSERNAME";
                } else {
                    // # password not corrected #
                    if($this->checkPassword($password)==FALSE)
                        $this->Autenticazione = "WRONGPASSWORD";
                    // ##  username & password CORRECT  ##
                    else if($this->checkPassword($password)==TRUE)
                        $this->Autenticazione = "CORRECT";
                        $_SESSION['IDutente'] = $this->IDutente;
                }
            }
        }

            /**
             * Verifica che la password inserita sia corretta
             * @param $password
             * @return bool
             */
            private function checkPassword($password){
                /*if(md5($password)!=$this->List[0]['Password'])
                    return FALSE;
                else if(md5($password)==$this->List[0]['Password'])
                    return TRUE;*/
                return $password===$this->List[0]['Password'] ? TRUE : FALSE;
            }

        /**
         * Stampa il form di Login
         * @return string
         */
        public function stampaLogin(){
			if($this->IDutente==''){
				$output = '<form id="loginForm" action="#" method="POST">
								Email:      <br /><input type="text" id="Email" name="Email" /><br />
								Password:   <br /><input type="password" id="Password" name="Password" />
								<input type="hidden" id="tkn" name="tkn" value="'.session_id().'"/>
								<input type="submit" value="Login" />
						   </form>
						   <div id="loginError" style="display:none;" class="error">Dati di autenticazione non corretti.</div>';
			} else {
				$output = '<div id="loginInfo">
						        '.$this->printLoginInfo().'
							    <input type="hidden" id="tkn" name="tkn" value="'.session_id().'"/>
							    <input id="logoutButton" type="button" value="Logout" />
						   </div>';
			}
			$output .= '<script language="javascript" type="text/javascript" src="resources/js/utenti.js" ></script>';
            return $output;
        }

            private function printLoginInfo(){
                $this->getInfoUtente();
                $output = '<span style="font-weight: bold; color: Green;">
                                '.$this->Nome.' '.$this->Cognome.'
                           </span><br />';
                if($this->LivelloUtente=="amministratore"){
                    $output .= '(<i>Amministratore</i>)<br />
                                <a href="utenti.php">Gestione utenti</a><br />';
                }
                $output .= '<a href="utenti.php?do=modifica&id='.$this->IDutente.'">Gestione profilo personale</a><br />';
                return $output;
            }


        public function printListTable($params=null){
            $this->getAll();
            $numCol = 6;
            $numItems = 0;

            $output = '<thead>
                            <tr>
                                <th></th>
                                <th>Nome</th>
                                <th>Cognome</th>
				<th>Acronimo</th>
                                <th>Email</th>
                                <th>Tipologia Utente</th>
                                <th>Stazioni Assegnate</th>
                            </tr>
                        </thead>
                        <tbody>';
                foreach($this->List as $utente){
                    $stazioniAssegnate = new StazioniAssegnate();
                    $stazioniAssegnate->getByUtente($utente['IDutente']);
                    $stazioniAssegnate->getStatistiche();
		    $bottoneDisattiva = '';
		    $rowClass = '';
		    if($utente['Password'] != null && $utente['Password'] != ''){
			$bottoneDisattiva = HTML::getButtonAsLink($_SERVER['SCRIPT_NAME'].'?do=confermaDisattivazione&id='.$utente['IDutente'], 'Disattiva');
		    } else {
			$rowClass = 'class="disabledUtenteRow"';
		    }
                    $output .= '<tr '.$rowClass.'>
                                    <td>
                                        '.HTML::getButtonAsLink($_SERVER['SCRIPT_NAME'].'?do=modifica&id='.$utente['IDutente'], 'Modifica').'
								        '.$bottoneDisattiva.'
								    </td>
                                    <td>'.$utente['Nome'].'</td>
                                    <td><b>'.$utente['Cognome'].'</b></td>
				    <td>'.$utente['Acronimo'].'</td>
                                    <td>'.$utente['Email'].'</td>
                                    <td>'.$utente['LivelloUtente'].'</td>
                                    <td>';
                                    if($utente['LivelloUtente']!=''){
                    $output .= '        Numero stazioni assegnate: <b>'.$stazioniAssegnate->numeroStazioniAssegnate.'</b><br />
                                        Numero sensori per tipologia (totale <b>'.$stazioniAssegnate->numeroSensoriTotali.'</b>):<br />';
                                        foreach($stazioniAssegnate->sensoriPerTipologia as $tipologia=>$numero){
                                            $output .= $tipologia.': '.$numero.'<br />';
                                        }
                    $output .=         HTML::getButtonAsLink('stazioniAssegnate.php?IDutente='.$utente['IDutente'], 'Gestione Stazioni Assegnate');
                                    }

                    $output .=  '   </td>
                                </tr>';
                    $numItems++;
                }
            $output .= '</tbody>
						<tr>
							<th colspan="'.$numCol.'">
								<i>'.$numItems.' utenti trovati.</i>
							</th>
						</tr>';
            return $output;
        }

        public function printEditForm(){
            $item = $this->List[0];
            $output = '<table id="tabellaModifica" class="summary">
                        <thead>
							<tr>
								<td>'.$this->IDfield.'</td>
								<th>
									'.$item[$this->IDfield].'
									<input type="hidden" name="'.$this->IDfield.'" value="'.$item[$this->IDfield].'" />
								</th>
							</tr>
						</thead>
						<tbody>
                            <tr><td>Nome</td><td>'.		'<input type="text" id="Nome" name="Nome" value="'.$item['Nome'].'" />'.'</td></tr>
                            <tr><td>Cognome</td><td>'.	'<input type="text" id="Cognome" name="Cognome" value="'.$item['Cognome'].'" />'.'</td></tr>
                            <tr><td>Email</td><td>'.	'<input type="text" id="Email" name="Email" value="'.$item['Email'].'" />'.'</td></tr>
                            <tr><td>Password</td><td>'.	'<input type="text" id="Password" name="Password" value="'.$item['Password'].'" />'.'</td></tr>';
                            global $utente;
                            if($utente->LivelloUtente=="amministratore"){
                                $output .= '<tr><td>Livello Utente</td><td>'.
                                                    '<select id="LivelloUtente" name="LivelloUtente">
                                                        <option value=""> - - </option>
                                                        <option value="amministratore" '.($item['LivelloUtente']=="amministratore" ? 'selected="selected"': '').'>Amministratore</option>
                                                        <option value="gestoreDati" '.($item['LivelloUtente']=="gestoreDati" ? 'selected="selected"': '').'>Gestore Dati</option>
                                                    </select>
                                            </td></tr>';
                            }
			    $output .= '<tr><td>Acronimo</td><td><input type="text" id="Acronimo" name="Acronimo" value="'.$item['Acronimo'].'" /></td></tr>';
			$output .= '</tbody>
					   </table>';
            return $output;
        }

        public function deleteByID(){
            $stazioniAssegnate = new StazioniAssegnate();
            $stazioniAssegnate->eliminaUtente($this->IDutente);
            $conditions = array( $this->IDfield => $this->{$this->IDfield} );
            parent::delete($conditions);
        }

        static public function getAcronimoByID($IDutente){
            $utente = new Utente();
            $utente->getByID($IDutente);
            return $utente->List[0]['Acronimo'];
        }

    }

