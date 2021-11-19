<?php

class AutoLoadClasses{

    public static function librerie($className){
        $classDir = dirname(__FILE__)."/librerie/";
        AutoLoadClasses::register($classDir, $className);
    }

    public static function entita($className){
        $classDir = dirname(__FILE__)."/dbMeteo/";
        AutoLoadClasses::register($classDir, $className);
    }

    private static function register($classDir, $className){
        if( is_readable($classDir.$className.".php")  ) {
            require $classDir.$className.".php";
        }
    }
}

spl_autoload_register( array('AutoLoadClasses', 'librerie') );
spl_autoload_register( array('AutoLoadClasses', 'entita') );