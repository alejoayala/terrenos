angular.module('theme.login', ['theme.core.services'])
  .controller('loginController', function($scope, $theme, loginServices ){
    //'use strict';
    $theme.set('fullscreen', true);

    $scope.$on('$destroy', function() {
      $theme.set('fullscreen', false);
    });
    $scope.fLogin = {};
    $scope.btnLoginToSystem = function () {
      loginServices.sLoginToSystem($scope.fLogin).then(function (response) { 
        $scope.fAlert = {};
        if( response.flag == 1 ){ // SE LOGEO CORRECTAMENTE 
          $scope.fAlert.type= 'success';
          $scope.fAlert.msg= response.message;
          $scope.fAlert.strStrong = 'OK.';
          $scope.getValidateSession();
          $scope.logIn();
        }else if( response.flag == 0 ){ // NO PUDO INICIAR SESION 
          $scope.fAlert.type= 'danger';
          $scope.fAlert.msg= response.message;
          $scope.fAlert.strStrong = 'Error.';
        }
        $scope.fAlert.flag = response.flag;
        //$scope.fLogin = {};
      });
    }
    
  })
  .service("loginServices",function($http, $q) {
    return({
        sLoginToSystem: sLoginToSystem
    });

    function sLoginToSystem(datos) { 
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"acceso/", 
            data : datos
      });
      return (request.then( handleSuccess,handleError ));
    }
  });