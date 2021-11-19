<?php

	class GruppiSensoriConverter{
				
		public static $WHITE = "#ffffff";
		public static $LIGHTBLUE = "#66ffff";
		public static $GREEN = "#00ff00";
		public static $YELLOW = "#ffff00";
		public static $RED = "#ff0000";
		public static $PINK = "#ff99ff";
		public static $ORANGE = "#ff6600";
		
		public static function convertGruppoToColorHex($gruppo){
			switch($gruppo){
				case "AGRO":
					return self::$WHITE;
					break;
				case "FIRE":
					return self::$RED;
					break;
				case "IDRO":
					return self::$LIGHTBLUE;
					break;
				case "METEO":
					return self::$YELLOW;
					break;
				case "NIVO":
					return self::$GREEN;
					break;
				case "PRESENTE":
					return self::$PINK;
					break;
				case "TURBO":
					return self::$ORANGE;
					break;
			}
		}
		
	}

?>