angular.module('theme.asistencia', ['theme.core.services'])
  .controller('asistenciaController', ['$scope', '$filter' , '$sce', '$modal', '$bootbox', '$window', '$http', '$theme', '$log', '$timeout', 'uiGridConstants', 'pinesNotifications', 'hotkeys', 
      'asistenciaServices', 'controlempleadoServices' , 'empleadoServices' ,
    function($scope, $filter, $sce,  $modal, $bootbox, $window, $http, $theme, $log, $timeout, uiGridConstants, pinesNotifications, hotkeys, 
      asistenciaServices ,controlempleadoServices , empleadoServices
      ){
    'use strict';
    shortcut.remove("F2"); $scope.modulo = 'asistencia';
    var paginationOptions = {
      pageNumber: 1,
      firstRow: 0,
      pageSize: 5,
      sort: uiGridConstants.ASC,
      sortName: null,
      search: null
    };
    $scope.mySelectionGrid = [];
    $scope.btnToggleFiltering = function(){
      $scope.gridOptions.enableFiltering = !$scope.gridOptions.enableFiltering;
      $scope.gridApi.core.notifyDataChange( uiGridConstants.dataChange.COLUMN );
    };
    $scope.navegateToCell = function( rowIndex, colIndex ) {
      $scope.gridApi.cellNav.scrollToFocus( $scope.gridOptions.data[rowIndex], $scope.gridOptions.columnDefs[colIndex]);
    };
    $scope.gridOptions = {
      paginationPageSizes: [5, 10, 50, 100, 500, 1000],
      paginationPageSize: 5,
      minRowsToShow: 6,
      useExternalPagination: true,
      useExternalSorting: true,
      useExternalFiltering : true,
      enableGridMenu: true,
      enableRowSelection: true,
      enableSelectAll: true,
      enableFiltering: false,
      enableFullRowSelection: true,
      //rowHeight: 100,
      multiSelect: true,
      columnDefs: [
        { field: 'id', name: 'idcontrolasistencia', displayName: 'ID', minWidth: 10,  sort: { direction: uiGridConstants.ASC} },
        { field: 'empleado', name: 'empleado', displayName: 'Empleado', minWidth: 250 },
        { field: 'fecha', name: 'fecha', displayName: 'Fecha', minWidth: 150 },
        { field: 'hora', name: 'hora', displayName: 'Hora', minWidth: 150 },
        { field: 'tipo', name: 'tipo', displayName: 'Tipo', minWidth: 150 },
        { field: 'estado_co', type: 'object', name: 'estado_co', displayName: 'Estado', maxWidth: 200,
          cellTemplate:'<label style="box-shadow: 1px 1px 0 black; margin: 6px auto; display: block; width: 120px;" class="label {{ COL_FIELD.clase }} ">{{ COL_FIELD.string }}</label>' }
      ],
      onRegisterApi: function(gridApi) {
        $scope.gridApi = gridApi;
        gridApi.selection.on.rowSelectionChanged($scope,function(row){
          $scope.mySelectionGrid = gridApi.selection.getSelectedRows();
        });
        gridApi.selection.on.rowSelectionChangedBatch($scope,function(rows){
          $scope.mySelectionGrid = gridApi.selection.getSelectedRows();
        });

        $scope.gridApi.core.on.sortChanged($scope, function(grid, sortColumns) {
          //console.log(sortColumns);
          if (sortColumns.length == 0) {
            paginationOptions.sort = null;
            paginationOptions.sortName = null;
          } else {
            paginationOptions.sort = sortColumns[0].sort.direction;
            paginationOptions.sortName = sortColumns[0].name;
          }
          $scope.getPaginationServerSide();
        });
        gridApi.pagination.on.paginationChanged($scope, function (newPage, pageSize) {
          paginationOptions.pageNumber = newPage;
          paginationOptions.pageSize = pageSize;
          paginationOptions.firstRow = (paginationOptions.pageNumber - 1) * paginationOptions.pageSize;
          $scope.getPaginationServerSide();
        });
        $scope.gridApi.core.on.filterChanged( $scope, function(grid, searchColumns) {
          var grid = this.grid;
          paginationOptions.search = true;
          paginationOptions.searchColumn = {
            'idcontrolasistencia' : grid.columns[1].filters[0].term,
            'idempleado' : grid.columns[2].filters[0].term,
            'hora_entrada' : grid.columns[3].filters[0].term,
            'hora_salida' : grid.columns[4].filters[0].term
          }
          $scope.getPaginationServerSide();
        });
      }
    };
    paginationOptions.sortName = $scope.gridOptions.columnDefs[0].name;
    $scope.getPaginationServerSide = function() {
      $scope.datosGrid = {
        paginate : paginationOptions
      };
      asistenciaServices.sListarAsistencias($scope.datosGrid).then(function (rpta) {
        console.log(rpta);
        $scope.gridOptions.totalItems = rpta.paginate.totalRows;
        $scope.gridOptions.data = rpta.datos;
      });
      $scope.mySelectionGrid = [];
    };
    $scope.getPaginationServerSide();
    $scope.accion22 = 'edit';
    /* ============= */
    /* MANTENIMIENTO */
    /* ============= */
    $scope.btnIngreso = function (size) {
      $modal.open({
        templateUrl: angular.patchURLCI+'asistencia/ver_popup_formulario',
        size: size || '',
        backdrop: 'static',
        keyboard:false,
        controller: function ($scope, $modalInstance, getPaginationServerSide) {
          $scope.accion = 'reg';
          $scope.getPaginationServerSide = getPaginationServerSide;
          $scope.fData = {};
          $scope.fData.tipo = 1 ;
          $scope.fData.fecha = $filter('date')(new Date(),'dd-MM-yyyy');
          $scope.fData.hora = $filter('date')(new Date(),'HH:mm');
          $scope.titleForm = 'Registro de Entradas';
         /* AUTOCOMPLETE CARGOS */ 
          $scope.getEmpleadoAutocomplete = function(val) { 
            var params = {
              search: val,
              sensor: false
            }
            return empleadoServices.sListarEmpleadosCbo(params).then(function(rpta) {
              console.log(rpta.datos)
              $scope.noResultsLPSC = false;
              if( rpta.flag === 0 ){
                $scope.noResultsLPSC = true;
              }
              return rpta.datos; 
            });
          };
          $scope.getSelectedEmpleado = function ($item, $model, $label) {
            $scope.fData.idempleado = $item.id;
            var arrData = {
              'id': $scope.fData.idempleado
            }
            console.log(arrData);
            empleadoServices.sListarEmpleadoPorCodigo(arrData).then(function (rpta) {
              if( rpta.flag == 1){
                $scope.fData.idempleado = rpta.datos.id;
                $scope.fData.dni = rpta.datos.dni;
                $scope.fData.empleado = rpta.datos.empleado;
                $('#fecha').focus();
              }
            });
          };

          $scope.obtenerEmpleadoPorDni = function () {
            if( $scope.fData.dni ){
              var arrData = {
                'dni': $scope.fData.dni
              }
              empleadoServices.sListarEmpleadoPorDni(arrData).then(function (rpta) {
                if( rpta.flag == 1){
                  $scope.fData.idempleado = rpta.datos[0].id;
                  $scope.fData.empleado = rpta.datos[0].empleado;
                  $scope.fData.dniempleado = rpta.datos[0].dni;
                  $('#fechainicio').focus();
                  pTitle = 'OK!';
                  pType = 'success';
                }else{
                  $scope.fData.id = null ;
                  $scope.fData.empleado = null ;
                  var pTitle = 'Error!';
                  var pType = 'danger';

                }
                pinesNotifications.notify({ title: pTitle, text: rpta.message, type: pType, delay: 1000 });
              });

            }
          }

          $scope.cancel = function () {
            $modalInstance.dismiss('cancel');
          }
          $scope.aceptar = function () { 
            asistenciaServices.sRegistrar($scope.fData).then(function (rpta) {
              if(rpta.flag == 1){
                pTitle = 'OK!';
                pType = 'success';
                $modalInstance.dismiss('cancel');
                $scope.getPaginationServerSide();
              }else if(rpta.flag == 0){
                var pTitle = 'Error!';
                var pType = 'danger';
              }else{
                alert('Error inesperado');
              }
              pinesNotifications.notify({ title: pTitle, text: rpta.message, type: pType, delay: 1000 });
            });
          }
          //console.log($scope.mySelectionGrid);
        }, 
        resolve: {
          getPaginationServerSide: function() {
            return $scope.getPaginationServerSide;
          }
        }
      });
    }
    $scope.btnSalida = function (size) {
      var parentScope = $scope.$new();
      $modal.open({
        templateUrl: angular.patchURLCI+'asistencia/ver_popup_formulario',
        size: size || '',
        backdrop: 'static',
        keyboard:false,
        scope: parentScope,
        controller: function ($scope, $modalInstance,mySelectionGrid,getPaginationServerSide) {
          console.log(parentScope.gridOptions,$scope.gridOptions);
          $scope.accion = 'edit';
          $scope.mySelectionGrid = mySelectionGrid;
          $scope.getPaginationServerSide = getPaginationServerSide;
          $scope.fData = {};
          $scope.fData.tipo = 2 ;
          $scope.fData.fecha = $filter('date')(new Date(),'dd-MM-yyyy');
          $scope.fData.hora = $filter('date')(new Date(),'HH:mm');

          $scope.getEmpleadoAutocomplete = function(val) { 
            var params = {
              search: val,
              sensor: false
            }
            return empleadoServices.sListarEmpleadosCbo(params).then(function(rpta) {
              console.log(rpta.datos)
              $scope.noResultsLPSC = false;
              if( rpta.flag === 0 ){
                $scope.noResultsLPSC = true;
              }
              return rpta.datos; 
            });
          };
          $scope.getSelectedEmpleado = function ($item, $model, $label) {
            $scope.fData.idempleado = $item.id;
            var arrData = {
              'id': $scope.fData.idempleado
            }
            console.log(arrData);
            empleadoServices.sListarEmpleadoPorCodigo(arrData).then(function (rpta) {
              if( rpta.flag == 1){
                $scope.fData.idempleado = rpta.datos.id;
                $scope.fData.dni = rpta.datos.dni;
                $scope.fData.empleado = rpta.datos.empleado;
                $('#fecha').focus();
              }
            });
          };

          $scope.obtenerEmpleadoPorDni = function () {
            if( $scope.fData.dni ){
              var arrData = {
                'dni': $scope.fData.dni
              }
              empleadoServices.sListarEmpleadoPorDni(arrData).then(function (rpta) {
                if( rpta.flag == 1){
                  $scope.fData.idempleado = rpta.datos[0].id;
                  $scope.fData.empleado = rpta.datos[0].empleado;
                  $scope.fData.dniempleado = rpta.datos[0].dni;
                  $('#fechainicio').focus();
                  pTitle = 'OK!';
                  pType = 'success';
                }else{
                  $scope.fData.id = null ;
                  $scope.fData.empleado = null ;
                  var pTitle = 'Error!';
                  var pType = 'danger';

                }
                pinesNotifications.notify({ title: pTitle, text: rpta.message, type: pType, delay: 1000 });
              });

            }
          }


          //console.log($scope.mySelectionGrid);
          $scope.titleForm = 'Registro de Salidas';
          $scope.cancel = function () {
            //console.log('load me');
            $modalInstance.dismiss('cancel');
            $scope.fData = {};
            
            $scope.getPaginationServerSide();
          }
          $scope.aceptar = function () { 
            asistenciaServices.sRegistrar($scope.fData).then(function (rpta) { 
              if(rpta.flag == 1){
                pTitle = 'OK!';
                pType = 'success';
                $modalInstance.dismiss('cancel');
              }else if(rpta.flag == 0){
                var pTitle = 'Error!';
                var pType = 'danger';
                
              }else{
                alert('Error inesperado');
              }
              $scope.fData = {};
              pinesNotifications.notify({ title: pTitle, text: rpta.message, type: pType, delay: 1000 });
              $scope.getPaginationServerSide();
            });
          }
          //console.log($scope.mySelectionGrid);
        }, 
        resolve: {
          mySelectionGrid: function() {
            return $scope.mySelectionGrid;
          },
          getPaginationServerSide: function() {
            return $scope.getPaginationServerSide;
          }
        }
      });
    }


    /* ============================ */
    /* ATAJOS DE TECLADO NAVEGACION */
    /* ============================ */
    hotkeys.bindTo($scope)
      .add({
        combo: 'alt+n',
        description: 'Nuevo seccion',
        callback: function() {
          $scope.btnNuevo();
        }
      })
      .add ({ 
        combo: 'e',
        description: 'Editar seccion',
        callback: function() {
          if( $scope.mySelectionGrid.length == 1 ){
            $scope.btnEditar();
          }
        }
      })
      .add ({ 
        combo: 'del',
        description: 'Anular seccion',
        callback: function() {
          if( $scope.mySelectionGrid.length > 0 ){
            $scope.btnAnular();
          }
        }
      })
      .add ({ 
        combo: 'b',
        description: 'Buscar seccion',
        callback: function() {
          $scope.btnToggleFiltering();
        }
      })
      .add ({ 
        combo: 's',
        description: 'Selección y Navegación',
        callback: function() {
          $scope.navegateToCell(0,0);
        }
      });
  }])
  .service("asistenciaServices",function($http, $q) {
    return({
        sListarAsistencias: sListarAsistencias,
        sListarAsistenciaPorEmpleado: sListarAsistenciaPorEmpleado,
        sRegistrar: sRegistrar,
        sEditar: sEditar
    });

    function sListarAsistencias(datos) { 
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"asistencia/lista_asistencia", 
            data : datos
      });
      return (request.then( handleSuccess,handleError ));
    }
    function sListarAsistenciaPorEmpleado(datos) { 
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"asistencia/lista_asistencia_por_empleado", 
            data : datos
      });
      return (request.then( handleSuccess,handleError ));
    }
    function sRegistrar (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"asistencia/registrar", 
            data : datos
      });
      return (request.then( handleSuccess,handleError ));
    }
    function sEditar (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"asistencia/editar", 
            data : datos
      });
      return (request.then( handleSuccess,handleError ));
    }
  });