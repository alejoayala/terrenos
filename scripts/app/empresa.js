angular.module('theme.empresa', ['theme.core.services'])
  .controller('empresaController', ['$scope', '$sce', '$modal', '$bootbox', '$window', '$http', '$theme', '$log', '$timeout', 'uiGridConstants', 'pinesNotifications', 'hotkeys', 
      'empresaServices', 
      'sedeServices',
    function($scope, $sce, $modal, $bootbox, $window, $http, $theme, $log, $timeout, uiGridConstants, pinesNotifications, hotkeys, 
      empresaServices,
      sedeServices ){
    'use strict';
    shortcut.remove("F2"); $scope.modulo = 'empresa';
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
      rowHeight: 100,
      multiSelect: true,
      columnDefs: [
        { field: 'idempresa', name: 'idempresa', displayName: 'ID', width: '8%',  sort: { direction: uiGridConstants.ASC} },
        { field: 'empresa', name: 'empresa', displayName: 'Empresa', width: '20%' },
        { field: 'sede', name: 'sede', displayName: 'Sede', width: '16%' },
        { field: 'especialidades', name: 'especialidades', displayName: 'Especialidades', enableFiltering: false, enableSorting: false, 
          cellTemplate: '<span style="box-shadow: 1px 1px 0 black;" class="label label-info mr-xs ml-sm mt-xs" ng-repeat="(key, value) in COL_FIELD">'+ 
            ' {{ value.especialidad }} <a class="btn-xs text-danger" ng-click="grid.appScope.quitarEspecialidadDeEmpresa(value)">X</a></span>' 
        }
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
          // console.log(grid.columns);
          // console.log(grid.columns[1].filters[0].term);
          paginationOptions.searchColumn = {
            'e.idempresa' : grid.columns[1].filters[0].term,
            'e.descripcion' : grid.columns[2].filters[0].term,
            's.descripcion' : grid.columns[3].filters[0].term
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
      empresaServices.sListarEmpresas($scope.datosGrid).then(function (rpta) {
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
        templateUrl: angular.patchURLCI+'empresa/ver_popup_formulario',
        size: size || '',
        backdrop: 'static',
        keyboard:false,
        controller: function ($scope, $modalInstance, getPaginationServerSide) {
          $scope.accion = 'reg';
          $scope.getPaginationServerSide = getPaginationServerSide;
          $scope.fData = {};
          $scope.titleForm = 'Registro de empresa';
          // SEDE    
          sedeServices.sListarSedeCbo().then(function (rpta) {
            $scope.listaSede = rpta.datos;
            $scope.listaSede.splice(0,0,{ id : '', descripcion:'--Seleccione Sede--'});
            $scope.fData.idsede = $scope.listaSede[0].id;
          });
          /* AUTOCOMPLETE EMPRESAS */ 
          $scope.getEmpresasAutocomplete = function(val) { 
            var params = {
              search: val,
              sensor: false
            }
            return empresaServices.sListarEmpresasCbo(params).then(function(rpta) {
              var empresas = rpta.datos.map(function(e) {
                return e.descripcion;
              });
              return empresas;
            });
          };
          $scope.cancel = function () {
            $modalInstance.dismiss('cancel');
          }
          $scope.aceptar = function () { 
            empresaServices.sRegistrar($scope.fData).then(function (rpta) {
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
        templateUrl: angular.patchURLCI+'empresa/ver_popup_formulario',
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
          }else{
            alert('Seleccione una sola fila');
          }
          $scope.titleForm = 'Edición de empresa';
          // SEDE    
          sedeServices.sListarSedeCbo().then(function (rpta) {
            $scope.listaSede = rpta.datos;
            $scope.listaSede.splice(0,0,{ id : '', descripcion:'--Seleccione Sede--'});

          });
          $scope.cancel = function () {
            //console.log('load me');
            $modalInstance.dismiss('cancel');
            $scope.fData = {};
            
            $scope.getPaginationServerSide();
          }
          $scope.aceptar = function () { 
            empresaServices.sEditar($scope.fData).then(function (rpta) { 
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
    $scope.btnAgregarEspecialidad = function (size) {
      $modal.open({
        templateUrl: angular.patchURLCI+'empresa/ver_popup_agregar_especialidad',
        size: size || '',
        controller: function ($scope, $modalInstance,arrToModal ) {
          $scope.mySelectionGrid = arrToModal.mySelectionGrid;
          $scope.getPaginationServerSide = arrToModal.getPaginationServerSide;
          $scope.fDataAdd = {};
          console.log($scope.mySelectionGrid);

          if( $scope.mySelectionGrid.length == 1 ){ 
            $scope.fDataAdd.empresaId = $scope.mySelectionGrid[0].idempresa;
            $scope.fDataAdd.sedeId = $scope.mySelectionGrid[0].idsede;
          }else{
            alert('Seleccione una sola fila'); return false; 
          }
          //console.log($scope.fDataAdd);
          /* DATA GRID */ 
          var paginationEspecialidadOptions = {
            pageNumber: 1,
            firstRow: 0,
            pageSize: 10,
            sort: uiGridConstants.ASC,
            sortName: null,
            search: null
          };
          $scope.mySelectionEspecialidadesGrid = [];
          $scope.gridOptionsEspecialidades = {
            paginationPageSizes: [10, 50, 100, 500, 1000],
            paginationPageSize: 10,
            useExternalPagination: true,
            useExternalSorting: true,
            enableGridMenu: true,
            enableRowSelection: true,
            enableSelectAll: true,
            enableFiltering: false,
            enableFullRowSelection: true,
            multiSelect: true,
            columnDefs: [
              { field: 'id', name: 'idespecialidad', displayName: 'ID', maxWidth: 80,  sort: { direction: uiGridConstants.ASC} },
              { field: 'nombre', name: 'nombre', displayName: 'Especialidad' } 
            ],
            onRegisterApi: function(gridApi) {
              $scope.gridApi = gridApi;
              gridApi.selection.on.rowSelectionChanged($scope,function(row){
                $scope.mySelectionEspecialidadesGrid = gridApi.selection.getSelectedRows();
              });
              gridApi.selection.on.rowSelectionChangedBatch($scope,function(rows){
                $scope.mySelectionEspecialidadesGrid = gridApi.selection.getSelectedRows();
              });

              $scope.gridApi.core.on.sortChanged($scope, function(grid, sortColumns) {
                //console.log(sortColumns);
                if (sortColumns.length == 0) {
                  paginationEspecialidadOptions.sort = null;
                  paginationEspecialidadOptions.sortName = null;
                } else {
                  paginationEspecialidadOptions.sort = sortColumns[0].sort.direction;
                  paginationEspecialidadOptions.sortName = sortColumns[0].name;
                }
                $scope.getPaginationEspecialidadesServerSide();
              });
              gridApi.pagination.on.paginationChanged($scope, function (newPage, pageSize) {
                paginationEspecialidadOptions.pageNumber = newPage;
                paginationEspecialidadOptions.pageSize = pageSize;
                paginationEspecialidadOptions.firstRow = (paginationEspecialidadOptions.pageNumber - 1) * paginationEspecialidadOptions.pageSize;
                $scope.getPaginationEspecialidadesServerSide();
              });
            }
          };
          paginationEspecialidadOptions.sortName = $scope.gridOptionsEspecialidades.columnDefs[0].name;
          $scope.getPaginationEspecialidadesServerSide = function() { 
            $scope.datosGrid = {
              paginate : paginationEspecialidadOptions,
              datos : $scope.mySelectionGrid[0]
            };
            empresaServices.sListarEspecialidadesNoAgregadosAEmpresa($scope.datosGrid).then(function (rpta) {
              $scope.gridOptionsEspecialidades.totalItems = rpta.paginate.totalRows;
              $scope.gridOptionsEspecialidades.data = rpta.datos;
              $scope.buscar = function () { 
                $scope.datosGrid.paginate.search = true;
                $scope.datosGrid.paginate.searchColumn = 'nombre';
                $scope.datosGrid.paginate.searchText = $scope.searchText;
                empresaServices.sListarEspecialidadesNoAgregadosAEmpresa($scope.datosGrid).then(function (rpta) {
                  $scope.gridOptionsEspecialidades.data = rpta.datos;
                });
              }
            });
            $scope.mySelectionEspecialidadesGrid = [];
          };
          $scope.getPaginationEspecialidadesServerSide();

          
          $scope.titleFormAdd = 'Agregar Especialidades';
          $scope.cancel = function () {
            $modalInstance.dismiss('cancel');
            $scope.fDataAdd = {};
          }
          $scope.aceptar = function () { 
            $scope.fDataAdd.especialidades = $scope.mySelectionEspecialidadesGrid;
            empresaServices.sAgregarEspecialidad($scope.fDataAdd).then(function (rpta) { 
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
              $scope.fDataAdd = {};
              pinesNotifications.notify({ title: pTitle, text: rpta.message, type: pType, delay: 1000 });
              $scope.getPaginationServerSide();
            });
          }
          //console.log($scope.mySelectionGrid); 
        }, 
        resolve: {
          arrToModal : function () {
            return {
              mySelectionGrid : $scope.mySelectionGrid,
              getPaginationServerSide : $scope.getPaginationServerSide
            }
          }
        }
      });
    }
    $scope.quitarEspecialidadDeEmpresa = function (id,mensaje) {
      var pMensaje = mensaje || '¿Realmente desea realizar la acción?';
      $bootbox.confirm(pMensaje, function(result) {
        if(result){
          empresaServices.sQuitarEspecialidadDeEmpresa(id).then(function (rpta) { 
            if(rpta.flag == 1){
              pTitle = 'OK!';
              pType = 'success';
            }else if(rpta.flag == 0){
              var pTitle = 'Error!';
              var pType = 'danger';
            }else{
              alert('Error inesperado');
            }
            pinesNotifications.notify({ title: pTitle, text: rpta.message, type: pType, delay: 1000 });
            $scope.getPaginationServerSide();
          });
        }
      });
    }
    $scope.btnAnular = function (mensaje) { 
      var pMensaje = mensaje || '¿Realmente desea realizar la acción?';
      $bootbox.confirm(pMensaje, function(result) {
        if(result){
          empresaServices.sAnular($scope.mySelectionGrid).then(function (rpta) {
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
        description: 'Nueva empresa',
        callback: function() {
          $scope.btnNuevo();
        }
      })
      .add ({ 
        combo: 'e',
        description: 'Editar empresa',
        callback: function() {
          if( $scope.mySelectionGrid.length == 1 ){
            $scope.btnEditar();
          }
        }
      })
      .add ({ 
        combo: 'del',
        description: 'Anular empresa',
        callback: function() {
          if( $scope.mySelectionGrid.length > 0 ){
            $scope.btnAnular();
          }
        }
      })
      .add ({ 
        combo: 'b',
        description: 'Buscar empresa',
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
  .service("empresaServices",function($http, $q) {
    return({
        sListarEmpresas: sListarEmpresas,
        sListarEmpresasCbo: sListarEmpresasCbo,
        sListarEspecialidadesNoAgregadosAEmpresa: sListarEspecialidadesNoAgregadosAEmpresa,
        sRegistrar: sRegistrar,
        sAgregarEspecialidad: sAgregarEspecialidad,
        sQuitarEspecialidadDeEmpresa: sQuitarEspecialidadDeEmpresa,
        sEditar: sEditar,
        sAnular: sAnular
    });

    function sListarEmpresas(datos) { 
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"empresa/lista_empresas", 
            data : datos
      });
      return (request.then( handleSuccess,handleError ));
    }
    function sListarEmpresasCbo(datos) { 
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"empresa/lista_empresas_cbo", 
            data : datos
      });
      return (request.then( handleSuccess,handleError ));
    }
    function sListarEspecialidadesNoAgregadosAEmpresa (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"empresa/lista_especialidades_no_agregados_a_empresa", 
            data : datos
      });
      return (request.then( handleSuccess,handleError ));
    }
    function sRegistrar (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"empresa/registrar", 
            data : datos
      });
      return (request.then( handleSuccess,handleError ));
    }
    function sAgregarEspecialidad (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"empresa/agregar_especialidad", 
            data : datos
      });
      return (request.then( handleSuccess,handleError ));
    }
    function sQuitarEspecialidadDeEmpresa (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"empresa/quitar_especialidad_de_empresa", 
            data : datos
      });
      return (request.then( handleSuccess,handleError ));
    }
    function sEditar (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"empresa/editar", 
            data : datos
      });
      return (request.then( handleSuccess,handleError ));
    }
    function sAnular (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"empresa/anular", 
            data : datos
      });
      return (request.then( handleSuccess,handleError ));
    }
  });