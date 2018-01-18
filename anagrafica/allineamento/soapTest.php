<?php
require_once("__init__.php");
?><!DOCTYPE html>
<html>
    <head></head>
    <body>
    <?php


        $wsdlURL = 'http://support.smartbear.com/samples/testcomplete/webservices/Service.asmx?WSDL';
        $soap = new SoapClient($wsdlURL);

        $functions = $soap->__getFunctions();
        $response = $soap->__soapCall('GetArray', array());

        print '<pre>';
        print_r($functions);
        print_r($response);
        die();
    ?>
    </body>

</html>