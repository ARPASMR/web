<?php

    require_once("__init__.php");

    $toDo = $_POST['toDo'];

    if($toDo=='UPDATE'){

        $id = $_POST['id'];
        $campo = $_POST['campo'];
        $valore = $_POST['valore'];

        print allineamentoUPDATE($id, $campo, $valore);
    }

    elseif($toDo=="INSERT"){

        $jsonData = $_POST['jsonData'];
        $jsonDataObj = json_decode($jsonData);

        $post=array();
        foreach($jsonDataObj as $k=>$v){
            $key = (string) $k;
            $value = (string) $v;
            $post[$key] = $value;
        }

        print allineamentoINSERT($post);

    }