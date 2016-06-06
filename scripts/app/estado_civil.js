angular.module('theme.estadocivil', ['theme.core.services'])
  .controller('estadocivilController', ['$scope', '$sce', '$modal', '$bootbox', '$window', '$http', '$theme', '$log', '$timeout', 'uiGridConstants', 'pinesNotifications', 'hotkeys', 
      'estadocivilServices', 
    function($scope, $sce, $modal, $bootbox, $window, $http, $theme, $log, $timeout, uiGridConstants, pinesNotifications, hotkeys, 
      estadocivilServices
      ){
    'use strict';
  }])
  .service("estadocivilServices",function($http, $q) {
    return({
        sListarEstadoCivilCbo: sListarEstadoCivilCbo
    });

    function sListarEstadoCivilCbo(datos) { 
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"estadocivil/lista_estado_civil_cbo", 
            data : datos
      });
      return (request.then( handleSuccess,handleError ));
    }
  });