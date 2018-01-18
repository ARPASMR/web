<?php

    // ####  Setta timezone  ####
	date_default_timezone_set('Europe/Rome');

    // ####  Autoload funzioni PHP  ####
    require_once('php/__autoload__.php');

    // ####  Verifica requisiti server  ####
    Config::checkPHPVersion('5.2', '>');
    Config::checkPHPModule('xml');
    Config::checkPHPModule('gd');

    // ####  Setta modalità DEBUG  ####
    if(isset($_GET['debug']) && $_GET['debug']=='true'){
        define('DEBUG', true);
        Debug::executionStart();
    } else {
        define('DEBUG', false);
    }