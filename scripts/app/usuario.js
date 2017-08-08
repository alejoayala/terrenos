angular.module('theme.usuario', ['theme.core.services'])
  .controller('usuarioController', ['$scope', '$sce', '$modal', '$bootbox', '$window', '$http', '$theme', '$log', '$timeout', 'uiGridConstants', 'pinesNotifications', 
    'usuarioServices', 
    'grupoServices', 
    function($scope, $sce, $modal, $bootbox, $window, $http, $theme, $log, $timeout, uiGridConstants, pinesNotifications, 
        usuarioServices,
        grupoServices 
      ){ 
    'use strict';
    shortcut.remove("F2"); $scope.modulo = 'usuario';
    var paginationOptions = {
      pageNumber: 1,
      firstRow: 0,
      pageSize: 10,
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
        { field: 'idusuario', name: 'idusuario', displayName: 'ID', maxWidth: 50,  sort: { direction: uiGridConstants.ASC} },
        { field: 'usuario', name: 'usuario', displayName: 'Usuario', minWidth: 120 }, 
        { field: 'email', name: 'email', displayName: 'E-mail', minWidth: 200 },
        { field: 'grupo', name: 'grupo', displayName: 'Grupo', maxWidth: 140 }, 
        { field: 'estado', type: 'object', name: 'estado_usuario', displayName: 'Estado', enableFiltering: false, enableSorting: false, maxWidth: 250, cellTemplate:'<label style="box-shadow: 1px 1px 0 black; margin: 6px auto; display: block; width: 120px;" class="label {{ COL_FIELD.clase }} ">{{ COL_FIELD.string }}</label>' }
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
            'idusers' : grid.columns[1].filters[0].term,
            'username' : grid.columns[2].filters[0].term,
            'email' : grid.columns[3].filters[0].term,
            'name' : grid.columns[4].filters[0].term
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
      usuarioServices.sListarUsuarios($scope.datosGrid).then(function (rpta) {
        
        $scope.gridOptions.totalItems = rpta.paginate.totalRows;
        // console.log('filas: '+rpta.paginate.totalRows);
        $scope.gridOptions.data = rpta.datos;
         
      });
      $scope.mySelectionGrid = [];
    };
    $scope.getPaginationServerSide();

    /* ============= */
    /* MANTENIMIENTO */
    /* ============= */
    $scope.btnEditar = function (size) {
      $modal.open({
        templateUrl: angular.patchURLCI+'usuario/ver_popup_formulario',
        size: size || '',
        controller: function ($scope, $modalInstance,mySelectionGrid,getPaginationServerSide) {
          $scope.mySelectionGrid = mySelectionGrid;
          $scope.getPaginationServerSide = getPaginationServerSide;
          $scope.fDataUsuario = {};
          $scope.boolForm = 'edit';
          //console.log($scope.mySelectionGrid);
          if( $scope.mySelectionGrid.length == 1 ){ 
            $scope.fDataUsuario = $scope.mySelectionGrid[0];
          }else{
            alert('Seleccione una sola fila');
          }
          $scope.titleForm = 'Edición de usuario';
          grupoServices.sListarGruposCbo().then(function (rpta) {
            $scope.listaGrupos = rpta.datos;
            $scope.fDataUsuario.grupoId = $scope.mySelectionGrid[0].groupId;
          });
          $scope.cancel = function () {
            $modalInstance.dismiss('cancel');
            $scope.fDataUsuario = {};
            $scope.getPaginationServerSide();
          }
          $scope.aceptar = function () {
            usuarioServices.sEditar($scope.fDataUsuario).then(function (rpta) { 
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
              $scope.getPaginationServerSide();
              $scope.fDataUsuario = {};
              pinesNotifications.notify({ title: pTitle, text: rpta.message, type: pType, delay: 1000 });
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
    $scope.btnNuevo = function (size) {
      $modal.open({
        templateUrl: angular.patchURLCI+'usuario/ver_popup_formulario',
        size: size || '',
        controller: function ($scope, $modalInstance, getPaginationServerSide,listarGrupos) { 
          $scope.fDataUsuario = {};
          $scope.boolForm = 'reg';
          $scope.titleForm = 'Registro de usuario';
          $scope.getPaginationServerSide = getPaginationServerSide;
          grupoServices.sListarGruposCbo().then(function (rpta) {
            $scope.listaGrupos = rpta.datos;
            $scope.listaGrupos.splice(0,0,{ id : '', descripcion:'--Seleccione grupo--'});
            $scope.fDataUsuario.grupoId = $scope.listaGrupos[0].id;
          });
          $scope.cancel = function () {
            $modalInstance.dismiss('cancel');
          }
          $scope.aceptar = function () {
            usuarioServices.sRegistrar($scope.fDataUsuario).then(function (rpta) {
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
          
        }, 
        resolve: {
          getPaginationServerSide: function() {
            return $scope.getPaginationServerSide;
          },
          listarGrupos : function () {
            return $scope.listarGrupos;
          }
        }
      });
    }
    $scope.btnAgregarSede = function (size) {
      $modal.open({
        templateUrl: angular.patchURLCI+'usuario/ver_popup_agregar_sede',
        size: size || '',
        controller: function ($scope, $modalInstance,arrToModal ) {
          $scope.mySelectionGrid = arrToModal.mySelectionGrid;
          $scope.getPaginationServerSide = arrToModal.getPaginationServerSide;
          $scope.fDataUsuarioAdd = {};
          //console.log($scope.mySelectionGrid);

          if( $scope.mySelectionGrid.length == 1 ){ 
            $scope.fDataUsuarioAdd.usuarioId = $scope.mySelectionGrid[0].id;
          }else{
            alert('Seleccione una sola fila'); return false; 
          }
          //console.log($scope.fDataUsuarioAdd);
          /* DATA GRID */ 
          var paginationSedeOptions = {
            pageNumber: 1,
            firstRow: 0,
            pageSize: 10,
            sort: uiGridConstants.ASC,
            sortName: null,
            search: null
          };
          $scope.mySelectionSedeGrid = [];
          $scope.gridOptionsSede = {
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
              { field: 'id', name: 'idsede', displayName: 'ID', maxWidth: 80,  sort: { direction: uiGridConstants.ASC } },
              { field: 'descripcion', name: 'descripcion', displayName: 'Sede' } 
            ],
            onRegisterApi: function(gridApi) {
              $scope.gridApi = gridApi;
              gridApi.selection.on.rowSelectionChanged($scope,function(row){
                $scope.mySelectionSedeGrid = gridApi.selection.getSelectedRows();
              });
              gridApi.selection.on.rowSelectionChangedBatch($scope,function(rows){
                $scope.mySelectionSedeGrid = gridApi.selection.getSelectedRows();
              });

              $scope.gridApi.core.on.sortChanged($scope, function(grid, sortColumns) {
                //console.log(sortColumns);
                if (sortColumns.length == 0) {
                  paginationSedeOptions.sort = null;
                  paginationSedeOptions.sortName = null;
                } else {
                  paginationSedeOptions.sort = sortColumns[0].sort.direction;
                  paginationSedeOptions.sortName = sortColumns[0].name;
                }
                $scope.getPaginationSedeServerSide();
              });
              gridApi.pagination.on.paginationChanged($scope, function (newPage, pageSize) {
                paginationSedeOptions.pageNumber = newPage;
                paginationSedeOptions.pageSize = pageSize;
                paginationSedeOptions.firstRow = (paginationSedeOptions.pageNumber - 1) * paginationSedeOptions.pageSize;
                $scope.getPaginationSedeServerSide();
              });
            }
          };
          paginationSedeOptions.sortName = $scope.gridOptionsSede.columnDefs[0].name;
          $scope.getPaginationSedeServerSide = function() { 
            $scope.datosGrid = {
              paginate : paginationSedeOptions,
              datos : $scope.mySelectionGrid[0]
            };
            usuarioServices.sListarSedesNoAgregadosAUsuario($scope.datosGrid).then(function (rpta) {
              $scope.gridOptionsSede.totalItems = rpta.paginate.totalRows;
              $scope.gridOptionsSede.data = rpta.datos;
              $scope.buscar = function () { 
                $scope.datosGrid.paginate.search = true;
                $scope.datosGrid.paginate.searchColumn = 'descripcion';
                $scope.datosGrid.paginate.searchText = $scope.searchText;
                usuarioServices.sListarSedesNoAgregadosAUsuario($scope.datosGrid).then(function (rpta) {
                  $scope.gridOptionsSede.data = rpta.datos;
                });
              }
            });
            $scope.mySelectionSedeGrid = [];
          };
          $scope.getPaginationSedeServerSide();

          
          $scope.titleFormAdd = 'Agregar Sedes';
          $scope.cancel = function () {
            $modalInstance.dismiss('cancel');
            $scope.fDataUsuarioAdd = {};
          }
          $scope.aceptar = function () { 
            $scope.fDataUsuarioAdd.sedes = $scope.mySelectionSedeGrid;
            usuarioServices.sAgregarSede($scope.fDataUsuarioAdd).then(function (rpta) { 
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
              $scope.fDataUsuarioAdd = {};
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
    $scope.btnDeshabilitar = function (mensaje) { 
      var pMensaje = mensaje || '¿Realmente desea realizar la acción?';
      $bootbox.confirm(pMensaje, function(result) {
        if(result){
          usuarioServices.sDeshabilitar($scope.mySelectionGrid).then(function (rpta) {
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
    $scope.btnHabilitar = function (mensaje) { 
      var pMensaje = mensaje || '¿Realmente desea realizar la acción?';
      $bootbox.confirm(pMensaje, function(result) {
        if(result){
          usuarioServices.sHabilitar($scope.mySelectionGrid).then(function (rpta) {
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
    $scope.quitarSedeDeUsuario = function (id,mensaje) {
      var pMensaje = mensaje || '¿Realmente desea realizar la acción?';
      $bootbox.confirm(pMensaje, function(result) {
        if(result){
          usuarioServices.sQuitarSedeDeUsuario(id).then(function (rpta) { 
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
    /* COMBOS */
    $scope.listarGrupos = function (callback) {
      var pCallback = callback || function () { }
      
    }
  }])
  .service("usuarioServices",function($http, $q) {
    return({
        sListarUsuarios: sListarUsuarios,
        sListarUsuariosCbo: sListarUsuariosCbo,
        sListarSedesNoAgregadosAUsuario: sListarSedesNoAgregadosAUsuario,
        sRegistrar: sRegistrar,
        sEditar: sEditar,
        sAgregarSede: sAgregarSede,
        sQuitarSedeDeUsuario: sQuitarSedeDeUsuario,
        sHabilitar: sHabilitar,
        sDeshabilitar: sDeshabilitar
    });

    function sListarUsuarios(datos) { 
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"usuario/lista_usuarios", 
            data : datos
      });
      return (request.then( handleSuccess,handleError ));
    }
    function sListarUsuariosCbo(pDatos) { 
      var datos = pDatos || {};
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"usuario/lista_usuario_cbo", 
            data : datos
      });
      return (request.then( handleSuccess,handleError ));
    }
    function sListarSedesNoAgregadosAUsuario (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"usuario/lista_sedes_no_agregados_a_usuario", 
            data : datos
      });
      return (request.then( handleSuccess,handleError ));
    }
    function sRegistrar (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"usuario/registrar", 
            data : datos
      });
      return (request.then( handleSuccess,handleError ));
    }
    function sEditar (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"usuario/editar", 
            data : datos
      });
      return (request.then( handleSuccess,handleError ));
    }
    function sAgregarSede (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"usuario/agregar_sede_a_usuario", 
            data : datos
      });
      return (request.then( handleSuccess,handleError ));
    }
    function sQuitarSedeDeUsuario (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"usuario/quitar_sede_de_usuario", 
            data : datos
      });
      return (request.then( handleSuccess,handleError ));
    }
    function sHabilitar (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"usuario/habilitar", 
            data : datos
      });
      return (request.then( handleSuccess,handleError ));
    }
    function sDeshabilitar (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"usuario/deshabilitar", 
            data : datos
      });
      return (request.then( handleSuccess,handleError ));
    }
  });