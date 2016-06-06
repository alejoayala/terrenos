angular.module('theme.inicio', ['theme.core.services'])
  .controller('inicioController', function($scope, $theme, inicioServices ){
    //'use strict';
    shortcut.remove("F2"); $scope.modulo = 'inicio'; 
    
  })
  .service("inicioServices",function($http, $q) {
    return({
        //sLoginToSystem: sLoginToSystem
    });

    // function sLoginToSystem(datos) { 
    //   var request = $http({
    //         method : "post",
    //         url : angular.patchURLCI+"acceso/", 
    //         data : datos
    //   });
    //   return (request.then( handleSuccess,handleError ));
    // }
  });