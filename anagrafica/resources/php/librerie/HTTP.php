<?php

    class HTTP{

        /**
         *  Redirect
         *
         *  @param	{String}	$url			the URL address
         *  @param	{Integer}	$sleepTime		[optional] wait before redirect in seconds (default: 0)
         */
		static function redirect($url, $sleepTime=0){
			if(!headers_sent()){
				if($sleepTime>0){
					header('Refresh:' . $sleepTime . ';' . $url);
				} else {
					header('Location:' . $url);
				}
			} else {
				$sleepTime = (($sleepTime==FALSE)) ? 0 : $sleepTime;
				echo "<meta http-equiv=\"refresh\" content=\"".$sleepTime.";url=".$url."\">";
			}
		}

    }