<?php

    class HTML{

        /**
         * Genera codice HTML di un bottone che funzioni come un link
         * @param $url
         * @param $label
         * @return string
         */
        static function getButtonAsLink($url, $label){

            // ottieni URL e parametri GET
            $pairsArray = array();
            if(substr_count($url,'?')>0){
                list($baseURL, $queryString) = explode('?', $url);
                parse_str($queryString, $pairsArray);
            } else {
                $baseURL = $url;
            }

            // crea codice HTML del bottone
            $output = '<form action="'.$baseURL.'" method="GET" style="display: inline;">';
            foreach($pairsArray as $key=>$value){
                $output .= '<input type="hidden" name="'.$key.'" value="'.$value.'" />';
            }
            $output .='    <input type="submit" value="'.$label.'">
                       </form>';

            return $output;
        }

        /**
         * Genera codice HTML di una dropdownlist
         * @param $listID
         * @param null $selectedItem
         * @param $values
         * @param $labels
         * @return string
         */
        static function dropdownList($listID, $selectedItem=null, $values, $labels){

            $output = '<select id="'.$listID.'" name="'.$listID.'" >';
            foreach($values as $key=>$value){
                $selected = ($value==$selectedItem) ? ' selected="selected"' : '';
                $output .= '<option value="'.$value.'" '.$selected.'>'.$labels[$key].'</option>';
            }
            $output .= '</select>';
            return $output;

        }

    }
