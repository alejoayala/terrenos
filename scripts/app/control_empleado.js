angular.module('theme.controlempleado', ['theme.core.services'])
  .controller('controlempleadoController', ['$scope', '$sce', '$modal', '$bootbox', '$window', '$http', '$theme', '$log', '$timeout', 'uiGridConstants', 'pinesNotifications', 'hotkeys', 
      'empleadoServices', 'bancoServices','afpServices','cargoServices','seccionServices', 'usuarioServices', 'controlempleadoServices' ,
    function($scope, $sce, $modal, $bootbox, $window, $http, $theme, $log, $timeout, uiGridConstants, pinesNotifications, hotkeys, empleadoServices , bancoServices,afpServices,cargoServices,seccionServices,usuarioServices ,controlempleadoServices
      ){
    'use strict';
    shortcut.remove("F2"); $scope.modulo = 'control empleado';
    var paginationOptions = {
      pageNumber: 1,
      firstRow: 0,
      pageSize: 5,
      sort: uiGridConstants.ASC,
      sortName: null,
      search: null
    };
    $scope.fData = {};
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
        { field: 'id', name: 'idempleado', displayName: 'ID', maxWidth: 50,  sort: { direction: uiGridConstants.ASC} },
        { field: 'empleado', name: 'empleado', displayName: 'Empleado', minWidth: 80 },
        { field: 'fecha_inicio', name: 'fecha_inicio', displayName: 'Fec.inicio', minWidth: 150 },
        { field: 'fecha_final', name: 'fecha_final', displayName: 'Fec.Final', minWidth: 100,  sort: { direction: uiGridConstants.ASC} },
        { field: 'cargo', name: 'cargo', displayName: 'Cargo', minWidth: 100 },
        { field: 'seccion', name: 'seccion', displayName: 'Seccion', minWidth: 100 },
        { field: 'estado_asegurado', name: 'estado_asegurado' , displayName: 'Asegurado', minWidth: 50,  sort: { direction: uiGridConstants.ASC} },
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
            'idempleado' : grid.columns[1].filters[0].term,
            'empleado' : grid.columns[2].filters[0].term
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
      controlempleadoServices.sListarContratoEmpleados($scope.datosGrid).then(function (rpta) {
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
    $scope.btnNuevo = function (size) {
      $modal.open({
        templateUrl: angular.patchURLCI+'controlempleado/ver_popup_formulario',
        size: size || '',
        backdrop: 'static',
        keyboard:false,
        controller: function ($scope, $modalInstance, getPaginationServerSide) {
          $scope.accion = 'reg';
          $scope.getPaginationServerSide = getPaginationServerSide;
          $scope.fData = {};
          $scope.titleForm = 'Registro de Contrato';
         /* AUTOCOMPLETE CARGOS */ 
          $scope.listaAsegurado = [
            { id: 1 , estado_asegurado:'SI' },
            { id: 2 , estado_asegurado:'NO' }
          ]; 
          $scope.fData.estado_asegurado = $scope.listaAsegurado[1].id;

          cargoServices.sListarCargosCbo().then(function (rpta) {
            $scope.listaFiltroCargo = rpta.datos;
            $scope.listaFiltroCargo.splice(0,0,{ id : 'oll', nombre_cargo:'--Seleccione Datos--'});
            $scope.fData.idcargo = $scope.listaFiltroCargo[0].id; 
          });
          seccionServices.sListarSeccionesCbo().then(function (rpta) {
            $scope.listaFiltroSeccion = rpta.datos;
            $scope.listaFiltroSeccion.splice(0,0,{ id : 'oll', nombre_seccion:'--Seleccione Datos--'});
            $scope.fData.idseccion = $scope.listaFiltroSeccion[0].id; 
          });
          bancoServices.sListarBancosCbo().then(function (rpta) {
            $scope.listaFiltroBanco = rpta.datos;
            $scope.listaFiltroBanco.splice(0,0,{ id : 'oll', nombre_banco:'--Seleccione Datos--'});
            $scope.fData.idbanco = $scope.listaFiltroBanco[0].id; 
          });
          afpServices.sListarAfpCbo().then(function (rpta) {
            $scope.listaFiltroAfp = rpta.datos;
            $scope.listaFiltroAfp.splice(0,0,{ id : 'oll', nombre_afp:'--Seleccione Datos--'});
            $scope.fData.idafp = $scope.listaFiltroAfp[0].id; 
          });


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
              console.log(rpta);
              if( rpta.flag == 1){
                $scope.fData.idempleado = rpta.datos.id;
                $scope.fData.dni = rpta.datos.dni;
                $scope.fData.empleado = rpta.datos.empleado;
                $('#fechainicio').focus();
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
                  //$('#fDataAlmacenreactivoInsumo').focus();
                  $('#fechainicio').focus();
                  pTitle = 'OK!';
                  pType = 'success';
                  //$scope.OnChangeUnidadLaboratorio($scope.fDataAlmacen.idreactivoInsumo);
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
            controlempleadoServices.sRegistrarContrato($scope.fData).then(function (rpta) {
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
    $scope.btnEditar = function (size) {
      var parentScope = $scope.$new();
      $modal.open({
        templateUrl: angular.patchURLCI+'controlempleado/ver_popup_formulario',
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
          //console.log($scope.mySelectionGrid);
          if( $scope.mySelectionGrid.length == 1 ){ 
            $scope.fData = $scope.mySelectionGrid[0];
            console.log($scope.fData);
          }else{
            alert('Seleccione una sola fila');
          }
          $scope.titleForm = 'Edición de Empleado';

          cargoServices.sListarCargosCbo().then(function (rpta) {
            $scope.listaFiltroCargo = rpta.datos;
          });
          seccionServices.sListarSeccionesCbo().then(function (rpta) {
            $scope.listaFiltroSeccion = rpta.datos;
          });
          bancoServices.sListarBancosCbo().then(function (rpta) {
            $scope.listaFiltroBanco = rpta.datos;
          });
          afpServices.sListarAfpCbo().then(function (rpta) {
            $scope.listaFiltroAfp = rpta.datos;
          });
          $scope.listaAsegurado = [
            { id: 1 , estado_asegurado:'SI' },
            { id: 2 , estado_asegurado:'NO' }
          ]; 
          $scope.fData.estado_asegurado = $scope.listaAsegurado[$scope.fData.idestado_asegurado-1].id;

          $scope.cancel = function () {
            //console.log('load me');
            $modalInstance.dismiss('cancel');
            $scope.fData = {};
            
            $scope.getPaginationServerSide();
          }
          $scope.aceptar = function () { 
            controlempleadoServices.sEditarContrato($scope.fData).then(function (rpta) { 
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
    $scope.btnAnular = function (mensaje) { 
      var pMensaje = mensaje || '¿Realmente desea realizar la acción?';
      $bootbox.confirm(pMensaje, function(result) {
        if(result){
          controlempleadoServices.sAnularContrato($scope.mySelectionGrid).then(function (rpta) {
            if(rpta.flag == 1){
                pTitle = 'OK!';
                pType = 'success';
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
      });
    }
    /* ============================ */
    /* ATAJOS DE TECLADO NAVEGACION */
    /* ============================ */
    hotkeys.bindTo($scope)
      .add({
        combo: 'alt+n',
        description: 'Nuevo empleado',
        callback: function() {
          $scope.btnNuevo();
        }
      })
      .add ({ 
        combo: 'e',
        description: 'Editar empleado',
        callback: function() {
          if( $scope.mySelectionGrid.length == 1 ){
            $scope.btnEditar();
          }
        }
      })
      .add ({ 
        combo: 'del',
        description: 'Anular empleado',
        callback: function() {
          if( $scope.mySelectionGrid.length > 0 ){
            $scope.btnAnular();
          }
        }
      })
      .add ({ 
        combo: 'b',
        description: 'Buscar empleado',
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
  .service("controlempleadoServices",function($http, $q) {
    return({
        sListarContratoEmpleados: sListarContratoEmpleados,
        sRegistrarContrato: sRegistrarContrato,
        sEditarContrato: sEditarContrato,
        sAnularContrato: sAnularContrato 
    });

    function sListarContratoEmpleados(datos) { 
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"controlempleado/lista_contrato_empleados", 
            data : datos
      });
      return (request.then( handleSuccess,handleError ));
    }
    function sRegistrarContrato(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"controlempleado/registrar_contrato", 
            data : datos
      });
      return (request.then( handleSuccess,handleError ));
    }
    function sEditarContrato (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"controlempleado/editar_contrato", 
            data : datos
      });
      return (request.then( handleSuccess,handleError ));
    }
    function sAnularContrato (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"controlempleado/anular_contrato", 
            data : datos
      });
      return (request.then( handleSuccess,handleError ));
    }
  });