angular.patchURL = dirWebRoot;
angular.patchURLCI = dirWebRoot+'ci.php/';
angular.dirViews = angular.patchURL+'/application/views/';
function handleError( response ) {
    if ( ! angular.isObject( response.data ) || ! response.data.message ) {
        return( $q.reject( "An unknown error occurred." ) );
    }
    return( $q.reject( response.data.message ) );
}
function handleSuccess( response ) {
    return( response.data );
}

appRoot = angular.module('theme.core.main_controller', ['theme.core.services', 'blockUI'])
  .controller('MainController', ['$scope', '$route', '$document', '$theme', '$timeout', 'progressLoader', 'wijetsService', '$routeParams', '$location','rootServices', 'blockUI', 'uiGridConstants', 'pinesNotifications', 
    function($scope, $route, $document, $theme, $timeout, progressLoader, wijetsService, $routeParams, $location, rootServices, blockUI, uiGridConstants, pinesNotifications) {
    //'use strict';

    $scope.fAlert = {};
    $scope.fSessionCI = {};
    $scope.fSessionCI.listaEspecialidadesSession = [];
    //$scope.listaEspecialidadesSession = [];
    $scope.layoutFixedHeader = $theme.get('fixedHeader');
    $scope.layoutPageTransitionStyle = $theme.get('pageTransitionStyle');
    $scope.layoutDropdownTransitionStyle = $theme.get('dropdownTransitionStyle');
    $scope.layoutPageTransitionStyleList = ['bounce',
      'flash',
      'pulse',
      'bounceIn',
      'bounceInDown',
      'bounceInLeft',
      'bounceInRight',
      'bounceInUp',
      'fadeIn',
      'fadeInDown',
      'fadeInDownBig',
      'fadeInLeft',
      'fadeInLeftBig',
      'fadeInRight',
      'fadeInRightBig',
      'fadeInUp',
      'fadeInUpBig',
      'flipInX',
      'flipInY',
      'lightSpeedIn',
      'rotateIn',
      'rotateInDownLeft',
      'rotateInDownRight',
      'rotateInUpLeft',
      'rotateInUpRight',
      'rollIn',
      'zoomIn',
      'zoomInDown',
      'zoomInLeft',
      'zoomInRight',
      'zoomInUp'
    ];
    $scope.dirImages = angular.patchURL+'/assets/img/';
    $scope.layoutLoading = true;
    $scope.blockUI = blockUI;
    $scope.getLayoutOption = function(key) {
      return $theme.get(key);
    };

    $scope.setNavbarClass = function(classname, $event) {
      $event.preventDefault();
      $event.stopPropagation();
      $theme.set('topNavThemeClass', classname);
    };

    $scope.setSidebarClass = function(classname, $event) {
      $event.preventDefault();
      $event.stopPropagation();
      $theme.set('sidebarThemeClass', classname);
    };

    $scope.$watch('layoutFixedHeader', function(newVal, oldval) {
      if (newVal === undefined || newVal === oldval) {
        return;
      }
      $theme.set('fixedHeader', newVal);
    });
    $scope.$watch('layoutLayoutBoxed', function(newVal, oldval) {
      if (newVal === undefined || newVal === oldval) {
        return;
      }
      $theme.set('layoutBoxed', newVal);
    });
    $scope.$watch('layoutLayoutHorizontal', function(newVal, oldval) {
      if (newVal === undefined || newVal === oldval) {
        return;
      }
      $theme.set('layoutHorizontal', newVal);
    });
    $scope.$watch('layoutPageTransitionStyle', function(newVal) {
      $theme.set('pageTransitionStyle', newVal);
    });
    $scope.$watch('layoutDropdownTransitionStyle', function(newVal) {
      $theme.set('dropdownTransitionStyle', newVal);
    });
    $scope.$watch('layoutLeftbarCollapsed', function(newVal, oldVal) {
      if (newVal === undefined || newVal === oldVal) {
        return;
      }
      $theme.set('leftbarCollapsed', newVal);
    });
    //$theme.set('leftbarCollapsed', false);
    $scope.toggleLeftBar = function() {
      $theme.set('leftbarCollapsed', !$theme.get('leftbarCollapsed'));
    };

    $scope.$on('themeEvent:maxWidth767', function(event, newVal) {
      $timeout(function() {
          $theme.set('leftbarCollapsed', newVal);
      });
    });
    $scope.$on('themeEvent:changed:fixedHeader', function(event, newVal) {
      $scope.layoutFixedHeader = newVal;
    });
    $scope.$on('themeEvent:changed:layoutHorizontal', function(event, newVal) {
      $scope.layoutLayoutHorizontal = newVal;
    });
    $scope.$on('themeEvent:changed:layoutBoxed', function(event, newVal) {
      $scope.layoutLayoutBoxed = newVal;
    });
    $scope.$on('themeEvent:changed:leftbarCollapsed', function(event, newVal) {
      $scope.layoutLeftbarCollapsed = newVal;
    });

    $scope.toggleSearchBar = function($event) {
      $event.stopPropagation();
      $event.preventDefault();
      $theme.set('showSmallSearchBar', !$theme.get('showSmallSearchBar'));
    };

    // there are better ways to do this, e.g. using a dedicated service
    // but for the purposes of this demo this will do
    $scope.isLoggedIn = false;
    $scope.logOut = function() {
      $scope.isLoggedIn = false;
    };
    $scope.logIn = function() {
      $scope.isLoggedIn = true;
    };

    $scope.$on('$routeChangeStart', function() {
      if ($location.path() === '') {
        return $location.path('/');
      }
      progressLoader.start();
      progressLoader.set(50);
    });
    $scope.$on('$routeChangeSuccess', function() {
      progressLoader.end();
      if ($scope.layoutLoading) {
        $scope.layoutLoading = false;
      }
      wijetsService.make();
    });

    $scope.dateUI = {} ;
    $scope.dateUI.formats = ['dd-MMMM-yyyy','yyyy/MM/dd','dd.MM.yyyy','shortDate'];
    $scope.dateUI.format = $scope.dateUI.formats[0]; // formato por defecto
    $scope.dateUI.datePikerOptions = {
      formatYear: 'yy',
      format: 'dd-MMMM-yyyy',
      startingDay: 1,
      'show-weeks': false
    };
    $scope.dateUI.openDP = function($event) {
      //console.log($event);
      $event.preventDefault();
      $event.stopPropagation();
      $scope.dateUI.opened = true;
    };

    $scope.closeAlert = function () {
      $scope.fAlert = {};
    }
    $scope.alerts = [];
    $scope.goToUrl = function ( path ) {
      $location.path( path );
    };
    
    $scope.btnLogoutToSystem = function () {
      rootServices.sLogoutSessionCI().then(function () { 
        $scope.fSessionCI = {};
        $scope.logOut();
        $scope.goToUrl('/login');
      });
    }
    $scope.getValidateSession = function () { 

      rootServices.sGetSessionCI().then(function (response) {
        if(response.flag == 1){ // console.log(response.datos); 
          $scope.fSessionCI = response.datos;
          $scope.logIn();
          $scope.getMenu();
          if( $location.path() == '/login' ){
            $scope.goToUrl('/');
          }
          //console.log($scope.isLoggedIn);
        }else{
          $scope.fSessionCI = {};
          $scope.logOut();
          $scope.goToUrl('/login');
          
        }
        
      });
    }
    $scope.getMenu = function(){
      //console.log($scope.fSessionCI);
      rootServices.sRoles($scope.fSessionCI).then(function (rpta){
        $scope.menu = rpta.datos;
        var setParent = function(children, parent) {
          angular.forEach(children, function(child) {
            child.parent = parent;
            if (child.children !== undefined) {
              setParent(child.children, child);
            }
          });
        };

        $scope.findItemByUrl = function(children, url) {
          for (var i = 0, length = children.length; i < length; i++) {
            if (children[i].url && children[i].url.replace('#', '') === url) {
              return children[i];
            }
            if (children[i].children !== undefined) {
              var item = $scope.findItemByUrl(children[i].children, url);
              if (item) {
                return item;
              }
            }
          }
        };

        setParent($scope.menu, null);

        $scope.openItems = []; $scope.selectedItems = []; $scope.selectedFromNavMenu = false;

        $scope.select = function(item) {
          // close open nodes
          if (item.open) {
            item.open = false;
            return;
          }
          for (var i = $scope.openItems.length - 1; i >= 0; i--) {
            $scope.openItems[i].open = false;
          }
          $scope.openItems = [];
          var parentRef = item;
          while (parentRef !== null) {
            parentRef.open = true;
            $scope.openItems.push(parentRef);
            parentRef = parentRef.parent;
          }

          // handle leaf nodes
          if (!item.children || (item.children && item.children.length < 1)) {
            $scope.selectedFromNavMenu = true;
            for (var j = $scope.selectedItems.length - 1; j >= 0; j--) {
              $scope.selectedItems[j].selected = false;
            }
            $scope.selectedItems = [];
            parentRef = item;
            while (parentRef !== null) {
              parentRef.selected = true;
              $scope.selectedItems.push(parentRef);
              parentRef = parentRef.parent;
            }
          }
        };
      });
      $scope.highlightedItems = [];
      var highlight = function(item) {
        var parentRef = item;
        while (parentRef !== null) {
          if (parentRef.selected) {
            parentRef = null;
            continue;
          }
          parentRef.selected = true;
          $scope.highlightedItems.push(parentRef);
          parentRef = parentRef.parent;
        }
      };
      $scope.$on('$routeChangeSuccess', function() {
      if ($scope.selectedFromNavMenu === false) {
        var item = $scope.findItemByUrl($scope.menu, $location.path());
        if (item) {
          $timeout(function() {
            $scope.select(item);
          });
        }
      }
      $scope.selectedFromNavMenu = false;
      $scope.searchQuery = '';
    });
    }

    $scope.getInfoEmpresa = function () { 
      rootServices.sGetEmpresaActiva().then(function (response) { 
        if(response.flag == 1){
          $scope.fEmpresa = response.datos;
        }
      });
    }
    $scope.reloadPage = function () {
      $route.reload();
    }
    // $scope.getValidateSession();
    // $scope.getInfoEmpresa();
    /* ARRAYS GENERALES */
    $scope.listaSexos = [
      { id:'', descripcion:'--Seleccione sexo--' },
      { id:'M', descripcion:'Masculino' },
      { id:'F', descripcion:'Femenino' }
    ];
    $scope.parseCurrency = function(num) { 
      var pNum = num;
      return parseFloat( pNum.replace( 'S/. ' , '') );
    }
    
    /* PREVENIR BACK NAVEGABILIDAD */
    // $document.on('keydown', function(e){
    //   if(e.which === 8 && e.target.nodeName !== "INPUT" || e.target.nodeName !== "SELECT"){ // you can add others here.
    //       e.preventDefault();
    //   }
    // })
  }])
  .service("rootServices", function($http, $q) {
    return({
        sGetSessionCI: sGetSessionCI,
        sLogoutSessionCI: sLogoutSessionCI,
        sListarEspecialidadesSession: sListarEspecialidadesSession,
        sGetEmpresaActiva: sGetEmpresaActiva,
        sRecargarUsuarioSession: sRecargarUsuarioSession,
        sRoles: sRoles
    });

    function sGetSessionCI() { 
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"acceso/getSessionCI"
      });
      return (request.then( handleSuccess,handleError ));
    }
    function sLogoutSessionCI() { 
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"acceso/logoutSessionCI"
      });
      return (request.then( handleSuccess,handleError ));
    }
    function sListarEspecialidadesSession (datos) { 
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"acceso/lista_especialidades_session", 
            data : datos
      });
      return (request.then( handleSuccess,handleError ));
    }
    function sGetEmpresaActiva () {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"configuracion/getEmpresaActiva"
      });
      return (request.then( handleSuccess,handleError ));
    }
    function sRecargarUsuarioSession (datos) { 
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"acceso/recargar_usuario_session", 
            data : datos
      });
      return (request.then( handleSuccess,handleError ));
    }
    function sRoles(datos) { 
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"rol/lista_roles_session", 
            data : datos
      });
      return (request.then( handleSuccess,handleError ));
    }
  });
/* DIRECTIVAS */
appRoot.
  directive('ngEnter', function() {
    return function(scope, element, attrs) {
      element.bind("keydown", function(event) {
          
          if(event.which === 13) { 
            //event.preventDefault();
            scope.$apply(function(){
              scope.$eval(attrs.ngEnter);
            });
            //event.stopPropagation();
          }
          //event.stopPropagation();
          //event.preventDefault();
      });
    };
  })
  .directive('fileModel', ['$parse', function ($parse) {
    return {
        restrict: 'A',
        link: function(scope, element, attrs) {
          var model = $parse(attrs.fileModel);
          var modelSetter = model.assign;
          element.bind('change', function(){ 
            scope.$apply(function(){ 
                modelSetter(scope, element[0].files[0]);
            });
          });
        }
    };
  }])
  .directive('focusMe', function($timeout, $parse) {
    return {
      link: function(scope, element, attrs) {
        var model = $parse(attrs.focusMe);

        scope.$watch(model, function(pValue) {
            value = pValue || 0;
            $timeout(function() {
              element[value].focus(); 
              // console.log(element[value]);
            });
        });
      }
    };
  })
  .directive('stringToNumber', function() {
    return {
      require: 'ngModel',
      link: function(scope, element, attrs, ngModel) {
        // console.log(scope);
        ngModel.$parsers.push(function(value) {
          // console.log('p '+value);
          return '' + value;
        });
        ngModel.$formatters.push(function(value) { 
          // console.log('f '+value);
          return parseFloat(value, 10);
        });
      }
    };
  })
  .config(function(blockUIConfig) {
    blockUIConfig.message = 'Cargando datos...';
    blockUIConfig.delay = 0;
    blockUIConfig.autoBlock = false;
    //i18nService.setCurrentLang('es');
  })
  .filter('getRowSelect', function() {
    return function(arraySelect, item) {
      var fSelected = {}; 
      angular.forEach(arraySelect,function(val,index) {
        if( val.id == item ){
          fSelected = val;
        }
      })
      return fSelected;
    }
  })
  .filter('numberFixedLen', function () {
      return function (n, len) {
          var num = parseInt(n, 10);
          len = parseInt(len, 10);
          if (isNaN(num) || isNaN(len)) {
              return n;
          }
          num = ''+num;
          while (num.length < len) {
              num = '0'+num;
          }
          return num;
      };
  })
  .factory("ModalReporteFactory", function($modal,$http){ 
    var interfazReporte = {
        getPopupReporte: function(arrParams){ 
          $modal.open({
            templateUrl: angular.patchURLCI+'CentralReportes/ver_popup_reporte',
            size: 'xlg',
            controller: function ($scope,$modalInstance,arrParams) { 
              $scope.titleModalReporte = arrParams.titulo;
              $scope.cancel = function () {
                $modalInstance.dismiss('cancel'); 
              } 
              $http.post(arrParams.url, arrParams.datos)
                .success(function(data, status) { 
                  if( arrParams.metodo == 'php' ){ 
                    $('#frameReporte').attr("src", data.urlTempPDF); 
                  }else if( arrParams.metodo == 'js' ){
                    var docDefinition = data.dataPDF 
                    pdfMake.createPdf(docDefinition).getBuffer(function(buffer) { 
                      var blob = new Blob([buffer]);
                      var reader = new FileReader(); 
                      reader.onload = function(event) {
                        var fd = new FormData();
                        fd.append('fname', 'temp.pdf');
                        fd.append('data', event.target.result);
                        $.ajax({
                          type: 'POST',
                          url: angular.patchURLCI+'CentralReportes/guardar_pdf_en_temporal', // Change to PHP filename
                          data: fd,
                          processData: false,
                          contentType: false
                        }).done(function(data) { 
                          $('#frameReporte').attr("src", data.urlTempPDF); 
                        });
                      }; 
                      reader.readAsDataURL(blob);
                    });
                  }
                  

                });
            }, 
            resolve: {
              arrParams: function() {
                return arrParams;
              }
            }
          });
        }
    }
    return interfazReporte;
  });


  // Prevent the backspace key from navigating back.
$(document).unbind('keydown').bind('keydown', function (event) {
  var doPrevent = false;
  if (event.keyCode === 8) {
      var d = event.srcElement || event.target;
      if ((d.tagName.toUpperCase() === 'INPUT' && 
           (
               d.type.toUpperCase() === 'TEXT' ||
               d.type.toUpperCase() === 'PASSWORD' || 
               d.type.toUpperCase() === 'FILE' || 
               d.type.toUpperCase() === 'SEARCH' || 
               d.type.toUpperCase() === 'EMAIL' || 
               d.type.toUpperCase() === 'NUMBER' || 
               d.type.toUpperCase() === 'TEL' || 
               d.type.toUpperCase() === 'DATE' )
           ) || 
           d.tagName.toUpperCase() === 'TEXTAREA') {
          doPrevent = d.readOnly || d.disabled;
      }
      else {
          doPrevent = true;
      }
  }

  if (doPrevent) {
      event.preventDefault();
  }
});
