angular.module('theme.nivelinstruccion', ['theme.core.services'])
  .controller('nivelinstruccionController', ['$scope', '$sce', '$modal', '$bootbox', '$window', '$http', '$theme', '$log', '$timeout', 'uiGridConstants', 'pinesNotifications', 'hotkeys', 
      'nivelinstruccionServices', 
    function($scope, $sce, $modal, $bootbox, $window, $http, $theme, $log, $timeout, uiGridConstants, pinesNotifications, hotkeys, 
      nivelinstruccionServices
      ){
    'use strict';
  }])
  .service("nivelinstruccionServices",function($http, $q) {
    return({
        sListarNivelinstruccionCbo: sListarNivelinstruccionCbo
    });

    function sListarNivelinstruccionCbo(datos) { 
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"nivelinstruccion/lista_nivel_instruccion_cbo", 
            data : datos
      });
      return (request.then( handleSuccess,handleError ));
    }
  });