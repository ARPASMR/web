<?php

/**
 * \file	__init__.php
 * \brief	Upper level configuration file
 * 			Contains database credentials setup 
 * 			for both production and development
 * 			machines
 */

    //setup php for working with Unicode data
    mb_internal_encoding('UTF-8');
    mb_http_output('UTF-8');
    mb_http_input('UTF-8');
    mb_language('uni');
    mb_regex_encoding('UTF-8');
    ob_start('mb_output_handler');

	/**
	 *	\var	$dbParams
	 *	\brief	Array contenente i parametri per la connessione a database
	 */
    $dbParams = array();

     // ###############################################
     // #####  Macchina di PRODUZIONE (Sinergico) #####
     // ###############################################

        if(substr_count($_SERVER['HTTP_HOST'], '10.10.')>0){

            // Error reporting
            ini_set('error_reporting', 0);
            ini_set('display_errors', 0);
            error_reporting(E_ERROR);

            // Parametri connessione al DATABASE (dbMeteo)
            $dbParams['host'] =     '10.10.0.25';
            $dbParams['port'] =     '3308';
            $dbParams['db'] =       'METEO';
            $dbParams['username'] = 'meteo';
            $dbParams['password'] = 'dbunicocrackers';
        }

     // ###############################################
     // #####  Swarm di PRODUZIONE (Sinergico)    #####
     // ###############################################

        else if(substr_count($_SERVER['HTTP_HOST'], '172.18.')>0 ||
                substr_count($_SERVER['HTTP_HOST'], '10.0.')>0){

            // Error reporting
            ini_set('error_reporting', 0);
            ini_set('display_errors', 0);
            error_reporting(E_ERROR);

            // Parametri connessione al DATABASE (dbMeteo)
            $dbParams['host'] =     '10.10.0.25';
            $dbParams['port'] =     '3308';
            $dbParams['db'] =       'METEO';
            $dbParams['username'] = 'meteo';
            $dbParams['password'] = 'dbunicocrackers';
        }

     // ##########################################
     // #####  Macchina di SVILUPPO (locale) #####
     // ##########################################

        else if(substr_count($_SERVER['HTTP_HOST'], 'localhost')>0
            || substr_count($_SERVER['HTTP_HOST'], '127.0.0.1')>0
            || substr_count($_SERVER['HTTP_HOST'], '192.168.')>0){

            // Error reporting
            ini_set('error_reporting', E_ALL);
            ini_set('display_errors', 1);

            // Parametri connessione al DATABASE (dbMeteo)
            $dbParams['host'] =     '127.0.0.1';
            $dbParams['db'] =       'METEO';
            $dbParams['username'] = 'root';
            $dbParams['password'] = 'chi66rone;';
        }
        //else if(substr_count($_SERVER['HTTP_HOST'], 'sinergicoweb.')>0
        else{

            // Error reporting
            ini_set('error_reporting', 0);
            ini_set('display_errors', 0);
            error_reporting(E_ERROR);

            // Parametri connessione al DATABASE (dbMeteo)
            $dbParams['host'] =     '10.10.0.25';
            $dbParams['port'] =     '3308';
            $dbParams['db'] =       'METEO';
            $dbParams['username'] = 'meteo';
            $dbParams['password'] = 'dbunicocrackers';
        }

    // ####  Importa librerie e classi  ####
    require_once("resources/__init__.php");

    // ####  Set web root PATH and URL  ####
    define("BASE_PATH", Config::initServerRoot(__FILE__, 'PATH'));
    define("BASE_URL", Config::initServerRoot(__FILE__, 'URL'));

    // ####  Instaura la connessione al database  ####
    $connection_dbMeteo = new DBConnection($dbParams);
    unset($dbParams);

    // ####  Autenticazione Utente  ####
    if(isset($_SESSION['IDutente']) && $_SESSION['IDutente']!=''){
        $IDutente = $_SESSION['IDutente'];
    } else {
        $IDutente = '';
        unset($_SESSION['IDutente']);
    }
    $utente = new Utente($IDutente);
