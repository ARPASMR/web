<?php
session_id($_POST['tkn']);    // get SESSION ID
session_start();

require_once("__init__.php");

    // #### LogOut ####
    if(isset($_POST['logout']) && $_POST['logout']=='true'){
        unset($_SESSION['IDutente']);
        die('{}');
    }

	
    // #### LogIn: Autentica utente ####
    if(isset($_POST['login']) && $_POST['login']=='true'){
		$Email = (isset($_POST['Email']) && $_POST['Email']!='') ? $_POST['Email'] : '';
		$Password = (isset($_POST['Password']) && $_POST['Password']!='') ? $_POST['Password'] : '';
		$utente->Autentica($Email, $Password);
		print ($utente->Autenticazione=='CORRECT') ? 'OK' : 'ERROR';
	}
	
	// #### Modifica Utente: verifica esistenza email ####
	if(isset($_POST['verificaEmail']) && $_POST['verificaEmail']=='true'){
        $utente = new Utente();
        print $utente->checkIfEmailExist($_POST['Email']);
	}
