<?php

    class StruttureDati{

        /**
         * Search for an ID in a multidimensional array
         * @param $idValue
         * @param $idKey
         * @param $array
         * @return int|null|string
         */
        static function searchArrayForId($idValue, $idKey, $array) {
            foreach ($array as $key => $val) {
                if ($val[$idKey] == $idValue) {
                    return $key;
                }
            }
            return null;
        }

        /**
         * Check if haystack start with needle
         * @param $haystack
         * @param $needle
         * @return bool
         */
        static function startsWith($haystack, $needle) {
            return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
        }
    }