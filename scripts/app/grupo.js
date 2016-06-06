  angular.module('theme.grupo', ['theme.core.services'])
  .controller('grupoController', ['$scope', '$modal', '$bootbox', '$window', '$http', '$theme', '$log', '$timeout', 'uiGridConstants', 'pinesNotifications', 'grupoServices',  
    function($scope, $modal, $bootbox, $window, $http, $theme, $log, $timeout, uiGridConstants, pinesNotifications, grupoServices ){
    'use strict';
    var paginationOptions = {
      pageNumber: 1,
      firstRow: 0,
      pageSize: 10,
      sort: uiGridConstants.ASC,
      sortName: null
    };
    $scope.mySelectionGrid = [];
    $scope.btnToggleFiltering = function(){
      $scope.gridOptions.enableFiltering = !$scope.gridOptions.enableFiltering;
      $scope.gridApi.core.notifyDataChange( uiGridConstants.dataChange.COLUMN );
    };
    $scope.getTemplateRoles = function (value) {
      return value.iconoRol + value.rol;
    }
    $scope.gridOptions = { 
      paginationPageSizes: [5,10, 50, 100, 500, 1000],
      paginationPageSize: 5,
      useExternalPagination: true,
      useExternalSorting: true,
      enableGridMenu: true,
      enableRowSelection: true,
      enableSelectAll: true,
      enableFiltering: false,
      enableFullRowSelection: true,
      rowHeight: 100,
      multiSelect: true,
      columnDefs: [ 
        { field: 'id', name: 'idgrupo', displayName: 'ID', maxWidth: 100,  sort: { direction: uiGridConstants.ASC} },
        { field: 'nombre', name: 'nombre_grupo', displayName: 'Nombre', maxWidth: 260 },
        { field: 'descripcion', name: 'description_rol', displayName: 'Descripción' },
        { field: 'roles', name: 'roles', type: 'object', displayName: 'Roles del grupo', minWidth: 700, enableFiltering: false, enableSorting: false, 
          cellTemplate: '<span style="box-shadow: 1px 1px 0 black;" class="label label-info mr-xs ml-sm mt-xs" ng-repeat="(key, value) in COL_FIELD">'+
            ' <i class="ti {{ value.iconoRol }}"></i> {{ value.rol }} <a class="btn-xs text-gray" ng-click="grid.appScope.quitarRolDeGrupo(value)">X</a> </span>  ' 
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
      }
    };
    paginationOptions.sortName = $scope.gridOptions.columnDefs[0].name;
    $scope.getPaginationServerSide = function() {
      $scope.datosGrid = {
        paginate : paginationOptions
      };
      grupoServices.sListarGrupos($scope.datosGrid).then(function (rpta) {
        $scope.gridOptions.totalItems = rpta.paginate.totalRows;
        $scope.gridOptions.data = rpta.datos;
         
        angular.forEach($scope.gridOptions.data, function (index,val) {
          
        });
      });
      $scope.mySelectionGrid = [];
    };
    $scope.getPaginationServerSide();

    /* ============= */
    /* MANTENIMIENTO */
    /* ============= */
    $scope.btnEditar = function (size) {
      $modal.open({
        templateUrl: angular.patchURLCI+'grupo/ver_popup_formulario',
        size: size || '',
        controller: function ($scope, $modalInstance,mySelectionGrid,getPaginationServerSide) {
          $scope.mySelectionGrid = mySelectionGrid;
          $scope.getPaginationServerSide = getPaginationServerSide;
          $scope.fData = {};
          //console.log($scope.mySelectionGrid);
          if( $scope.mySelectionGrid.length == 1 ){ 
            $scope.fData = $scope.mySelectionGrid[0];
          }else{
            alert('Seleccione una sola fila');
          }
          $scope.titleForm = 'Edición de grupo';
          $scope.cancel = function () {
            console.log('load me');
            $modalInstance.dismiss('cancel');
            $scope.fData = {};
            
            $scope.getPaginationServerSide();
          }
          $scope.aceptar = function () {
            grupoServices.sEditar($scope.fData).then(function (rpta) { 
              if(rpta.flag == 1){
                pTitle = 'OK!';
                pType = 'success';
                $modalInstance.dismiss('cancel');
              }else if(rpta.flag == 0){
                var pTitle = 'Error!';
                var pType = 'danger';
                $scope.getPaginationServerSide();
              }else{
                alert('Error inesperado');
              }
              $scope.fData = {};
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
        templateUrl: angular.patchURLCI+'grupo/ver_popup_formulario',
        size: size || '',
        controller: function ($scope, $modalInstance, getPaginationServerSide) {
          $scope.getPaginationServerSide = getPaginationServerSide;
          $scope.fData = {};
          $scope.titleForm = 'Registro de grupo';
          $scope.cancel = function () {
            $modalInstance.dismiss('cancel');
          }
          $scope.aceptar = function () {
            //console.log($scope.fData);
            grupoServices.sRegistrar($scope.fData).then(function (rpta) {
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
    $scope.btnAnular = function (mensaje) { 
      var pMensaje = mensaje || '¿Realmente desea realizar la acción?';
      $bootbox.confirm(pMensaje, function(result) {
        if(result){
          grupoServices.sAnular($scope.mySelectionGrid).then(function (rpta) {
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
    $scope.btnAgregarRol = function (size) {
      $modal.open({
        templateUrl: angular.patchURLCI+'grupo/ver_popup_agregar_rol',
        size: size || '',
        controller: function ($scope, $modalInstance,mySelectionGrid,getPaginationServerSide) {
          $scope.mySelectionGrid = mySelectionGrid;
          $scope.getPaginationServerSide = getPaginationServerSide;
          $scope.fDataAdd = {};
          console.log($scope.mySelectionGrid);
          if( $scope.mySelectionGrid.length == 1 ){ 
            $scope.fDataAdd.groupId = $scope.mySelectionGrid[0].id;
          }else{
            alert('Seleccione una sola fila'); return false; 
          }
          /* DATA GRID */ 
          var paginationRolOptions = {
            pageNumber: 1,
            firstRow: 0,
            pageSize: 10,
            sort: uiGridConstants.ASC,
            sortName: null
          };
          $scope.mySelectionRolGrid = [];
          $scope.gridOptionsRoles = {
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
              { field: 'id', displayName: 'ID', name: 'idrol', maxWidth: 80,  sort: { direction: uiGridConstants.ASC} },
              { field: 'icono', displayName: 'Icono', name: 'icono_rol', maxWidth: 100, enableFiltering: false, enableSorting: false, cellTemplate:'<div class="text-center"><i style="font-size:18px;" class="ti {{ COL_FIELD }} " ></i></div>' }, 
              { field: 'descripcion', displayName: 'Descripción', name: 'descripcion_rol' } 
            ],
            onRegisterApi: function(gridApi) {
              $scope.gridApi = gridApi;
              gridApi.selection.on.rowSelectionChanged($scope,function(row){
                $scope.mySelectionRolGrid = gridApi.selection.getSelectedRows();
              });
              gridApi.selection.on.rowSelectionChangedBatch($scope,function(rows){
                $scope.mySelectionRolGrid = gridApi.selection.getSelectedRows();
              });

              $scope.gridApi.core.on.sortChanged($scope, function(grid, sortColumns) {
                //console.log(sortColumns);
                if (sortColumns.length == 0) {
                  paginationRolOptions.sort = null;
                  paginationRolOptions.sortName = null;
                } else {
                  paginationRolOptions.sort = sortColumns[0].sort.direction;
                  paginationRolOptions.sortName = sortColumns[0].name;
                }
                $scope.getPaginationRolesServerSide();
              });
              gridApi.pagination.on.paginationChanged($scope, function (newPage, pageSize) {
                paginationRolOptions.pageNumber = newPage;
                paginationRolOptions.pageSize = pageSize;
                paginationRolOptions.firstRow = (paginationRolOptions.pageNumber - 1) * paginationRolOptions.pageSize;
                $scope.getPaginationRolesServerSide();
              });
            }
          };
          paginationRolOptions.sortName = $scope.gridOptionsRoles.columnDefs[0].name;
          $scope.getPaginationRolesServerSide = function() { 
            $scope.datosGrid = {
              paginate : paginationRolOptions,
              datos : $scope.mySelectionGrid[0]
            };
            grupoServices.sListarRolesNoAgregadosAGrupo($scope.datosGrid).then(function (rpta) {
              $scope.gridOptionsRoles.totalItems = rpta.paginate.totalRows;
              $scope.gridOptionsRoles.data = rpta.datos;

              // $timeout(function() {
              //   if($scope.gridApi.selection.selectRow){
              //     $scope.gridApi.selection.selectRow($scope.gridOptions.data[0]);
              //   }
              // });
            });
            $scope.mySelectionRolGrid = [];
          };
          $scope.getPaginationRolesServerSide();

          
          $scope.titleFormAdd = 'Agregar roles';
          $scope.cancel = function () {
            $modalInstance.dismiss('cancel');
            $scope.fDataAdd = {};
          }
          $scope.aceptar = function () { // console.log('loas me'); 
            //console.log($scope.mySelectionRolGrid);
            $scope.fDataAdd.roles = $scope.mySelectionRolGrid;
            grupoServices.sAgregarRol($scope.fDataAdd).then(function (rpta) { 
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
          mySelectionGrid: function() {
            return $scope.mySelectionGrid;
          },
          getPaginationServerSide: function() {
            return $scope.getPaginationServerSide;
          }
        }
      });
    }
    $scope.quitarRolDeGrupo = function (rolGroupId,mensaje) {
      //console.log(rolGroupId);
      var pMensaje = mensaje || '¿Realmente desea realizar la acción?';
      $bootbox.confirm(pMensaje, function(result) {
        if(result){
          grupoServices.sQuitarRolDeGrupo(rolGroupId).then(function (rpta) { 
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
  }])
  .service("grupoServices",function($http, $q) {
    return({
        sListarGrupos: sListarGrupos,
        sListarGruposCbo: sListarGruposCbo,
        sListarRolesNoAgregadosAGrupo: sListarRolesNoAgregadosAGrupo,
        sAgregarRol: sAgregarRol,
        sQuitarRolDeGrupo: sQuitarRolDeGrupo,
        sRegistrar: sRegistrar,
        sEditar: sEditar,
        sAnular: sAnular,
    });

    function sListarGrupos(datos) { 
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"grupo/lista_grupos", 
            data : datos
      });
      return (request.then( handleSuccess,handleError ));
    }
    function sListarGruposCbo () {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"grupo/lista_grupos_cbo"
      });
      return (request.then( handleSuccess,handleError ));
    }
    function sListarRolesNoAgregadosAGrupo (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"grupo/lista_roles_no_agregados_al_grupo", 
            data : datos
      });
      return (request.then( handleSuccess,handleError ));
    }
    function sAgregarRol (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"grupo/agregar_rol", 
            data : datos
      });
      return (request.then( handleSuccess,handleError ));
    }
    function sQuitarRolDeGrupo (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"grupo/quitar_rol_de_grupo", 
            data : datos
      });
      return (request.then( handleSuccess,handleError ));
    }
    function sRegistrar (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"grupo/registrar", 
            data : datos
      });
      return (request.then( handleSuccess,handleError ));
    }
    function sEditar (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"grupo/editar", 
            data : datos
      });
      return (request.then( handleSuccess,handleError ));
    }
    function sAnular (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"grupo/anular", 
            data : datos
      });
      return (request.then( handleSuccess,handleError ));
    }
  });