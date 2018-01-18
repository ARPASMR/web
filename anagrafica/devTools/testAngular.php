<?php
session_start();
require_once("../__init__.php");
?>
<!DOCTYPE html>
<html>
<head lang="en">

    <meta charset="UTF-8">
    <title>Test dbMeteo con AngularJS e Bootstrap</title>

    <!-- jQuery -->
    <script src="js/jquery-1.11.3.min.js"></script>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="js/bootstrap-3.3.4.min.css">
    <script src="js/bootstrap-3.3.4.min.js"></script>

    <!-- AngularJS -->
    <script src="js/angular-1.3.15.min.js"></script>

    <!-- Local JS and CSS files -->
    <style>
        body{
            font-size: 8pt;
            margin: 10px;
        }
            table.lista th{
                font-size: 10pt;
                background-color: #eeeeee;
                vertical-align: middle;
            }
    </style>
    <script>
        var meteoApp = angular.module('meteoApp', []);

        meteoApp.controller("listaStazioni", function($scope, $http) {

            $scope.stazioni = [];
            $scope.numeroStaz = 0;
            var apiUrl = '<?php print BASE_URL; ?>api/stazioni.php?mode=anagrafica';

            /**
             * Ottiene i dati dal file JSON
             */
            $http.get(apiUrl)
                .success(function(data) {
                    $scope.stazioni = data;
                    $scope.numeroStaz = data.length;
                })
                .error(function() {
                    // errore
                });

            $scope.campiDaVisualizzare = ["IDstazione","IDrete","Provincia","Comune","Attributo",
                                            "NOMEstazione","ProprietaStazione",
                                            "Allerta","AOaib","AOneve","AOvalanghe",
                                            "Quota","DataInizio","DataFine"];
            $scope.orderBy = 'idstaz';
            $scope.sortDesc = true;
        });

    </script>

</head>

    <body ng-app="meteoApp">

        <div ng-controller="listaStazioni" >
            {{numeroStaz}}
            <table class="lista table table-bordered table-condensed">
                <tr>
                    <th ng-repeat="campo in campiDaVisualizzare">
                        {{campo}}
                    </th>
                </tr>
                <tr ng-repeat=" stazione in stazioni | orderBy:orderBy:sortDesc | filter:filtraColonna">
                    <td ng-repeat="campo in campiDaVisualizzare">{{stazione[campo]}}</td>
                </tr>
            </table>

        </div>

    </body>
</html>