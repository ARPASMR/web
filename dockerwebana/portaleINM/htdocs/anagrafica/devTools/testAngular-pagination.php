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

    <!-- UI Bootstrap -->
    <script src="js/ui-bootstrap-tpls-0.13.0.min.js"></script>

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
        .pagination{
            margin: 0;
        }
        .center{
            text-align: center;
        }
    </style>
    <script>
        var meteoApp = angular.module('meteoApp', ['ui.bootstrap']);

        meteoApp.controller("listaStazioni", function($scope, $http) {

            $scope.campiDaVisualizzare = ["IDstazione","IDrete","Provincia","Comune","Attributo",
                                        "NOMEstazione","ProprietaStazione",
                                        "Allerta","AOaib","AOneve","AOvalanghe",
                                        "Quota","DataInizio","DataFine"];
            $scope.orderBy = 'idstaz';
            $scope.sortDesc = true;

            /**
             * Ottiene dati da API
             */
            $scope.ottieniStazioni = function() {
                $scope.stazioni = [];
                $scope.numeroStaz = 0;
                var apiUrl = '<?php print BASE_URL; ?>api/stazioni.php?mode=anagrafica';
                $http.get(apiUrl)
                    .success(function (data) {
                        $scope.stazioni = data;
                        $scope.numeroStaz = data.length;
                        $scope.paginaStazioni();
                    })
                    .error(function () {
                        // errore
                    });
            };
            $scope.ottieniStazioni();

            /**
             *  Paginazione risultati
             */
            $scope.paginaStazioni = function() {
                $scope.stazioniPaginate = [];
                $scope.currentPage = 1;
                $scope.numPerPage = 20;
                $scope.maxSize = 10;
                $scope.$watch('currentPage + numPerPage', function() {
                    var begin = (($scope.currentPage - 1) * $scope.numPerPage);
                    var end = begin + $scope.numPerPage;
                    $scope.stazioniPaginate = $scope.stazioni.slice(begin, end);
                });
            };
        });

    </script>

</head>

    <body ng-app="meteoApp">

        <div ng-controller="listaStazioni" >
            Stazioni trovate: {{numeroStaz}}
            <table class="lista table table-bordered table-condensed">
                <tr>
                    <th ng-repeat="campo in campiDaVisualizzare">
                        {{campo}}
                    </th>
                </tr>
                <tr ng-repeat=" stazione in stazioniPaginate | orderBy:orderBy:sortDesc | filter:filtraColonna">
                    <td ng-repeat="campo in campiDaVisualizzare">{{stazione[campo]}}</td>
                </tr>
            </table>
            <div class="center">
                <pagination
                    total-items="stazioni.length"
                    ng-model="currentPage"
                    max-size="maxSize"
                    items-per-page="numPerPage"
                    boundary-links="true" previous-text="&lsaquo;" next-text="&rsaquo;" first-text="&laquo;" last-text="&raquo;">
                </pagination>
            </div>
        </div>

    </body>
</html>