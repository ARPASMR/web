<?php

    // ####  Setta timezone  ####
	date_default_timezone_set('Europe/Rome');

    // ####  Autoload funzioni PHP  ####
    require_once('php/__autoload__.php');
    
    if( version_compare(PHP_VERSION, '5.5', '<') )
    {
    	if (!function_exists('array_column')) {
    		function array_column($array, $columnKey, $indexKey = null)
    		{
    			$result = array();
    			foreach ($array as $subArray) {
    				if (is_null($indexKey) && array_key_exists($columnKey, $subArray)) {
    					$result[] = is_object($subArray)?$subArray->$columnKey: $subArray[$columnKey];
    				} elseif (array_key_exists($indexKey, $subArray)) {
    					if (is_null($columnKey)) {
    						$index = is_object($subArray)?$subArray->$indexKey: $subArray[$indexKey];
    						$result[$index] = $subArray;
    					} elseif (array_key_exists($columnKey, $subArray)) {
    						$index = is_object($subArray)?$subArray->$indexKey: $subArray[$indexKey];
    						$result[$index] = is_object($subArray)?$subArray->$columnKey: $subArray[$columnKey];
    					}
    				}
    			}
    			return $result;
    		}
    	}
    }

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