angular.module('theme.personafamiliar', ['theme.core.services'])
  .controller('personafamiliarController', ['$scope', '$sce', '$modal', '$bootbox', '$window', '$http', '$theme', '$log', '$timeout', 'uiGridConstants', 'pinesNotifications', 'hotkeys', 
      'personafamiliarServices', 
    function($scope, $sce, $modal, $bootbox, $window, $http, $theme, $log, $timeout, uiGridConstants, pinesNotifications, hotkeys, 
      personafamiliaServices
      ){
    'use strict';
  }])
  .service("personafamiliarServices",function($http, $q) {
    return({
        sListarEmpleadoFamiliarCbo: sListarEmpleadoFamiliarCbo ,
        sListarFamiliaresNoAgregadosAEmpleado : sListarFamiliaresNoAgregadosAEmpleado ,
        sAgregarFamiliarEmpleado : sAgregarFamiliarEmpleado ,
        sListarFamiliaresDelEmpleado : sListarFamiliaresDelEmpleado ,
        sAnularFamiliarEmpleado : sAnularFamiliarEmpleado
    });

    function sListarEmpleadoFamiliarCbo(datos) { 
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"empleado/lista_empleado_familiar_cbo", 
            data : datos
      });
      return (request.then( handleSuccess,handleError ));
    }
    function sListarFamiliaresNoAgregadosAEmpleado(datos) { 
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"empleado/lista_familiares_no_agregados_a_empleado", 
            data : datos
      });
      return (request.then( handleSuccess,handleError ));
    }
    function sAgregarFamiliarEmpleado(datos) { 
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"empleado/agregar_familiar_empleado", 
            data : datos
      });
      return (request.then( handleSuccess,handleError ));
    }
    function sListarFamiliaresDelEmpleado(datos) { 
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"empleado/lista_familiares_del_empleado", 
            data : datos
      });
      return (request.then( handleSuccess,handleError ));
    }
    function sAnularFamiliarEmpleado(datos) { 
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"empleado/anular_familiar_empleado", 
            data : datos
      });
      return (request.then( handleSuccess,handleError ));
    }
  });