<?php

    /**
     * Stampa la tabella di resoconto dei record esistenti in dbUnico ma non in dbMeteo
     * Ogni record riporta gli strumenti per eseguire la query di INSERT
     *
     * @param $label
     * @param $stazione_dbUnico
     * @return string
     */
    function tabellaINSERT($label, $stazione_dbUnico){
        $colspan = '3';

        $post=array();
        foreach($stazione_dbUnico as $k=>$v){
            $key = (string) $k;
            $value = (string) $v;
            $post[$key] = $value;
        }

        return '<table class="allineamento insert">
                    <thead>
                        <tr>
                            <th colspan="'.$colspan.'">Stazione non presente in dbMeteo:</th>
                        </tr>
                        <tr>
                            <th colspan="'.$colspan.'" class="label" >'.$label.'</th>
                        <tr>
                    <thead>
                    <tbody>
                        <tr>
                            <th class="query">Query:</th>
                            <td>
                                '.allineamentoINSERT($post).'
                            </td>
                            <td class="applica">
                                <span style="display: none;">'.json_encode($post).'</span>
                                <button onclick="applicaINSERT($(this));" >Applica modifica</button>
                            </td>
                        </tr>
                    </tbody>
                </table>';
    }


        /**
         * Ritorna la query di allineamento per l'INSERT
         * @param $post
         * @return string
         */
        function allineamentoINSERT($post){
            $stazione = new Stazione();
            return $stazione->allineamentoRecord($post);
        }

    /**
     * Stampa la tabella di resoconto delle differenze tra dbUnico e dbMeteo
     * Ogni record riporta gli strumenti per eseguire la query di UPDATE
     *
     * @param $label
     * @param $righe
     * @return string
     */
    function tabellaUPDATE($label, $righe){
        $colspan = '9';
        return '<table class="allineamento update">
                    <thead>
                        <tr>
                            <th colspan="'.$colspan.'">Differenze rilevate su:</th>
                        </tr>
                        <tr>
                            <th colspan="'.$colspan.'" class="label" >'.$label.'</th>
                        <tr>
                    <thead>
                    <tbody>
                        '.$righe.'
                    </tbody>
                </table>';
    }

        /**
         * Stampa il record di confronto tra dbUnico e dbMeteo del singolo campo
         *
         * @param $id
         * @param $campo
         * @param $valore_dbUnico
         * @param $valore_dbMeteo
         * @return string
         */
        function rigaUPDATE($id, $campo, $valore_dbUnico, $valore_dbMeteo){

            $query = allineamentoUPDATE($id, $campo, $valore_dbUnico);
            $query = str_replace('SET','<br />SET',$query);
            $query = str_replace('WHERE','<br />WHERE',$query);

            return '<tr>
                        <th class="campo">Campo:</th>
                        <td>'.$campo.'</td>
                        <th class="valore">Valore su dbUnico:</th>
                        <td>'.$valore_dbUnico.'</td>
                        <th class="valore">Valore su dbMeteo:</th>
                        <td>'.$valore_dbMeteo.'</td>
                        <th class="query">Query:</th>
                        <td>'.$query.'</td>
                        <td class="applica">
                            <button onclick="applicaUPDATE(\''.$id.'\', \''.$campo.'\', \''.$valore_dbUnico.'\');" >Applica modifica</button>
                        </td>
                    </tr>
                    ';
        }


        /**
         * Ritorna la query di allineamento per l'UPDATE
         *
         * @param $id
         * @param $campo
         * @param $valore
         * @return string
         */
        function allineamentoUPDATE($id, $campo, $valore){
            $stazione = new Stazione();
            return $stazione->allineamentoCampo($id, $campo, $valore);
        }
