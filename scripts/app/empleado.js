angular.module('theme.empleado', ['theme.core.services'])
  .controller('empleadoController', ['$scope', '$sce', '$modal', '$bootbox', '$window', '$http', '$theme', '$log', '$timeout', 'uiGridConstants', 'pinesNotifications', 'hotkeys', 
      'empleadoServices', 'nivelinstruccionServices' , 'estadocivilServices','usuarioServices', 'empleadofamiliarServices' ,
    function($scope, $sce, $modal, $bootbox, $window, $http, $theme, $log, $timeout, uiGridConstants, pinesNotifications, hotkeys, 
      empleadoServices ,nivelinstruccionServices ,estadocivilServices ,usuarioServices ,empleadofamiliarServices
      ){
    'use strict';
    shortcut.remove("F2"); $scope.modulo = 'empleado';
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
        { field: 'dni', name: 'dni', displayName: 'DNI', minWidth: 80 },
        { field: 'nombres', name: 'nombres', displayName: 'Nombres', minWidth: 150 },
        { field: 'apellido_paterno', name: 'apellido_paterno', displayName: 'Apellido Paterno', minWidth: 100,  sort: { direction: uiGridConstants.ASC} },
        { field: 'apellido_materno', name: 'apellido_materno', displayName: 'Apellido Materno', minWidth: 100 },
        { field: 'fecha_nacimiento', name: 'fecha_nacimiento', displayName: 'fec. Nacimiento', minWidth: 100 },
        { field: 'estado_civil', name: 'estado_civil', displayName: 'Est.Civil', minWidth: 50,  sort: { direction: uiGridConstants.ASC} },
        { field: 'estado_em', type: 'object', name: 'estado_em', displayName: 'Estado', maxWidth: 200,
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
          // console.log(grid.columns);
          // console.log(grid.columns[1].filters[0].term);
          paginationOptions.searchColumn = {
            'idempleado' : grid.columns[1].filters[0].term,
            'nombres' : grid.columns[2].filters[0].term
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
      empleadoServices.sListarEmpleados($scope.datosGrid).then(function (rpta) {
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
        templateUrl: angular.patchURLCI+'empleado/ver_popup_formulario',
        size: size || '',
        backdrop: 'static',
        keyboard:false,
        controller: function ($scope, $modalInstance, getPaginationServerSide) {
          $scope.accion = 'reg';
          $scope.getPaginationServerSide = getPaginationServerSide;
          $scope.fData = {};
          $scope.titleForm = 'Registro de Empleado';
          /* AUTOCOMPLETE CARGOS */ 
          $scope.listaSexo = [
            { id: 1 , nombresexo:'HOMBRE' },
            { id: 2 , nombresexo:'MUJER' }
          ]; 
          $scope.fData.sexo = $scope.listaSexo[0].id;

          nivelinstruccionServices.sListarNivelinstruccionCbo().then(function (rpta) {
            $scope.listaNivelInstruccion = rpta.datos;
            $scope.listaNivelInstruccion.splice(0,0,{ id : 'oll', nombre_nivelinstruccion:'--Seleccione Datos--'});
            $scope.fData.idnivelinstruccion = $scope.listaNivelInstruccion[0].id; 
          });

          estadocivilServices.sListarEstadoCivilCbo().then(function (rpta) {
            $scope.listaEstadoCivil = rpta.datos;
            $scope.listaEstadoCivil.splice(0,0,{ id : 'oll', nombre_estadocivil:'--Seleccione Datos--'});
            $scope.fData.idestadocivil = $scope.listaEstadoCivil[0].id; 
          });

          $scope.getEmpleadosAutocomplete = function(val) { 
            var params = {
              search: val,
              sensor: false
            }
            return empleadoServices.sListarEmpleadosCbo(params).then(function(rpta) {
              var empleados = rpta.datos.map(function(e) {
                return e.descripcion;
              });
              return empleados;
            });
          };
          $scope.verPopupListaUsuarios = function (size) { 
            $modal.open({
              templateUrl: angular.patchURLCI+'configuracion/ver_popup_combo',
              size: size || '',
              controller: function ($scope, $modalInstance, arrToModal) {
                $scope.fData = arrToModal.fData;
                usuarioServices.sListarUsuariosCbo().then(function (rpta) {
                  $scope.fpc = {};
                  $scope.fpc.titulo = ' Usuario.';
                  $scope.fpc.lista = rpta.datos;
                  //$scope.selected = 0;
                  $scope.selected = $scope.fData.idusuario || null;
                  $scope.fpc.selectedItem = function (row) { 
                    $scope.selected = row.id;
                    $scope.fData.idusuario = row.id;
                    $scope.fData.usuario = row.descripcion;
                    $modalInstance.dismiss('cancel');
                  }
                  $scope.fpc.buscar = function () { 
                    $scope.fpc.nameColumn = 'nombre_usuario';
                    $scope.fpc.lista = null;
                    usuarioServices.sListarUsuariosCbo($scope.fpc).then(function (rpta) {
                      $scope.fpc.lista = rpta.datos;
                    });
                  }
                });
              },
              resolve: {
                arrToModal: function() {
                  return {
                    fData : $scope.fData
                  }
                }
              }
            });
          }

          $scope.cancel = function () {
            $modalInstance.dismiss('cancel');
          }
          $scope.aceptar = function () { 
            empleadoServices.sRegistrar($scope.fData).then(function (rpta) {
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
        templateUrl: angular.patchURLCI+'empleado/ver_popup_formulario',
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
          $scope.titleForm = 'Edición de Empleado';
          $scope.listaSexo = [
            { id: 1 , nombresexo:'HOMBRE' },
            { id: 2 , nombresexo:'MUJER' }
          ]; 
          /******************************************************/
          $scope.fData.sexo = $scope.listaSexo[$scope.fData.sexo-1].id;

          nivelinstruccionServices.sListarNivelinstruccionCbo().then(function (rpta) {
            $scope.listaNivelInstruccion = rpta.datos;
          });

          estadocivilServices.sListarEstadoCivilCbo().then(function (rpta) {
            $scope.listaEstadoCivil = rpta.datos;
          });

          $scope.cancel = function () {
            //console.log('load me');
            $modalInstance.dismiss('cancel');
            $scope.fData = {};
            
            $scope.getPaginationServerSide();
          }
          $scope.aceptar = function () { 
            empleadoServices.sEditar($scope.fData).then(function (rpta) { 
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
          empleadoServices.sAnular($scope.mySelectionGrid).then(function (rpta) {
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
    /* HABILITAR Y DESHABILITAR     */
    /* ============================ */

    $scope.btnHabilitar = function (mensaje) {
      var pMensaje = mensaje || '¿Realmente desea realizar la acción?';
      $bootbox.confirm(pMensaje, function(result) {
        if(result){
          empleadoServices.sHabilitar($scope.mySelectionGrid).then(function (rpta) {
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
    $scope.btnDeshabilitar = function (mensaje) {
      var pMensaje = mensaje || '¿Realmente desea realizar la acción?';
      $bootbox.confirm(pMensaje, function(result) {
        if(result){
          empleadoServices.sDeshabilitar($scope.mySelectionGrid).then(function (rpta) {
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

    $scope.btnAsignaFamilia = function (size) { 
      $modal.open({
        templateUrl: angular.patchURLCI+'empleado/ver_popup_agregar_familiar',
        size: size || '',
        scope: $scope,
        controller: function ($scope, $modalInstance,arrToModal ) {
          $scope.mySelectionGrid = arrToModal.mySelectionGrid;
          $scope.getPaginationServerSide = arrToModal.getPaginationServerSide;
          $scope.fDataAdd = {};

          if( $scope.mySelectionGrid.length == 1 ){ 
            $scope.fDataAdd.idempleado = $scope.mySelectionGrid[0].id;
          }else{
            alert('Seleccione una sola fila'); return false; 
          }
          $scope.listaTipoConsanguineo = [
            { id: 1 , tipo_consanguineo:'CONYUGE' },
            { id: 2 , tipo_consanguineo:'HIJO(A)' }
          ]; 
          $scope.fDataAdd.tipo_consanguineo = $scope.listaTipoConsanguineo[0].id;
          /* DATA GRID */ 
          var paginationFamiliarOptions = {
            pageNumber: 1,
            firstRow: 0,
            pageSize: 10,
            sort: uiGridConstants.ASC,
            sortName: null,
            search: null
          };
          $scope.mySelectionFamiliaresGrid = [];
          $scope.gridOptionsFamiliares = {
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
              { field: 'id', name: 'idfamiliar', displayName: 'ID', maxWidth: 80,  sort: { direction: uiGridConstants.ASC} },
              { field: 'familiar', name: 'familiar', displayName: 'Familiar',sort: { direction: uiGridConstants.ASC} } 
            ],
            onRegisterApi: function(gridApi) {
              $scope.gridApi = gridApi;
              gridApi.selection.on.rowSelectionChanged($scope,function(row){
                $scope.mySelectionFamiliaresGrid = gridApi.selection.getSelectedRows();
              });
              gridApi.selection.on.rowSelectionChangedBatch($scope,function(rows){
                $scope.mySelectionFamiliaresGrid = gridApi.selection.getSelectedRows();
              });

              $scope.gridApi.core.on.sortChanged($scope, function(grid, sortColumns) {
                //console.log(sortColumns);
                if (sortColumns.length == 0) {
                  paginationFamiliarOptions.sort = null;
                  paginationFamiliarOptions.sortName = null;
                } else {
                  paginationFamiliarOptions.sort = sortColumns[0].sort.direction;
                  paginationFamiliarOptions.sortName = sortColumns[0].name;
                }
                $scope.getPaginationFamiliaresServerSide();
              });
              gridApi.pagination.on.paginationChanged($scope, function (newPage, pageSize) {
                paginationFamiliarOptions.pageNumber = newPage;
                paginationFamiliarOptions.pageSize = pageSize;
                paginationFamiliarOptions.firstRow = (paginationFamiliarOptions.pageNumber - 1) * paginationFamiliarOptions.pageSize;
                $scope.getPaginationFamiliaresServerSide();
              });
            }
          };
          paginationFamiliarOptions.sortName = $scope.gridOptionsFamiliares.columnDefs[0].name;
          $scope.getPaginationFamiliaresServerSide = function() { 
            $scope.datosGrid = {
              paginate : paginationFamiliarOptions,
              datos : $scope.mySelectionGrid[0]
            };
            empleadofamiliarServices.sListarFamiliaresNoAgregadosAEmpleado($scope.datosGrid).then(function (rpta) {
              $scope.gridOptionsFamiliares.totalItems = rpta.paginate.totalRows;
              $scope.gridOptionsFamiliares.data = rpta.datos;
              $scope.buscar = function () { 
                $scope.datosGrid.paginate.search = true;
                $scope.datosGrid.paginate.searchColumn = 'familiar';
                $scope.datosGrid.paginate.searchText = $scope.searchText;
                empleadofamiliarServices.sListarFamiliaresNoAgregadosAEmpleado($scope.datosGrid).then(function (rpta) {
                  $scope.gridOptionsFamiliares.data = rpta.datos;
                });
              }
            });
            $scope.mySelectionFamiliaresGrid = [];
          };
          $scope.getPaginationFamiliaresServerSide();
          $scope.titleFormAdd = 'Agregar Familiares';
          $scope.cancel = function () {
            $modalInstance.dismiss('cancel');
            $scope.fDataAdd = {};
          }
          $scope.aceptar = function () { 
            $scope.fDataAdd.familiares = $scope.mySelectionFamiliaresGrid;
            console.log($scope.fDataAdd);
            console.log("ACA TOY");
            empleadofamiliarServices.sAgregarFamiliarEmpleado($scope.fDataAdd).then(function (rpta) { 
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
    $scope.btnConsultaFamilia = function (size) {
      $modal.open({
        templateUrl: angular.patchURLCI+'empleado/ver_popup_consultar_familiar',
        size: size || '',
        scope: $scope,
        controller: function ($scope, $modalInstance,arrToModal ) {
          $scope.mySelectionGrid = arrToModal.mySelectionGrid;
          $scope.getPaginationServerSide = arrToModal.getPaginationServerSide;
          $scope.fDataAdd = {};

          if( $scope.mySelectionGrid.length == 1 ){ 
            $scope.datosGrid = { 
              datos : $scope.mySelectionGrid[0]
            };
          }else{
            alert('Seleccione una sola fila'); return false; 
          }
          var paginationFamiliarOptions = {
            pageNumber: 1,
            firstRow: 0,
            pageSize: 10,
            sort: uiGridConstants.ASC,
            sortName: null,
            search: null
          };
          /* DATA GRID */ 
          $scope.mySelectionFamiliaresGrid = [];
          $scope.gridOptionsFamiliares = {
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
              { field: 'id', name: 'idfamiliar', displayName: 'ID', maxWidth: 80,  sort: { direction: uiGridConstants.ASC} },
              { field: 'familiar', name: 'familiar', displayName: 'Familiar', sort: { direction: uiGridConstants.ASC} } ,
              { field: 'tipo_consanguineo', name: 'tipo_consanguineo', displayName: 'Tipo Familiar' }

            ],
            onRegisterApi: function(gridApi) {
              $scope.gridApi = gridApi;
              gridApi.selection.on.rowSelectionChanged($scope,function(row){
                $scope.mySelectionFamiliaresGrid = gridApi.selection.getSelectedRows();
              });
              gridApi.selection.on.rowSelectionChangedBatch($scope,function(rows){
                $scope.mySelectionFamiliaresGrid = gridApi.selection.getSelectedRows();
              });
            }
          };
          $scope.getPaginationFamiliaresServerSide = function() { 
            $scope.datosGrid = {
              paginate : paginationFamiliarOptions,
              datos : $scope.mySelectionGrid[0]
            };
            empleadofamiliarServices.sListarFamiliaresDelEmpleado($scope.datosGrid).then(function (rpta) { 
              $scope.gridOptionsFamiliares.data = rpta.datos;
              $scope.buscar = function () { 
                $scope.datosGrid.paginate.search = true;
                $scope.datosGrid.paginate.searchColumn = 'familiar';
                $scope.datosGrid.paginate.searchText = $scope.searchText;
                empleadofamiliarServices.sListarFamiliaresDelEmpleado($scope.datosGrid).then(function (rpta) {
                  $scope.gridOptionsFamiliares.data = rpta.datos;
                });
              }
            });
          }
          $scope.getPaginationFamiliaresServerSide();
          $scope.titleFormAdd = 'Consultar Familiares';
          $scope.btnAnularFamiliarEmpleado = function (mensaje) {
            var pMensaje = mensaje || '¿Realmente desea realizar la acción?';
            $bootbox.confirm(pMensaje, function(result) {
              if(result){
                empleadofamiliarServices.sAnularFamiliarEmpleado($scope.mySelectionFamiliaresGrid).then(function (rpta) {
                  if(rpta.flag == 1){
                      pTitle = 'OK!';
                      pType = 'success';
                      $scope.getPaginationFamiliaresServerSide();
                      //$scope.buscar();
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

          $scope.titleFormAdd = 'Consultar Familiares';
          $scope.cancel = function () {
            $modalInstance.dismiss('cancel');
            $scope.fDataAdd = {};
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
  .service("empleadoServices",function($http, $q) {
    return({
        sListarEmpleados: sListarEmpleados,
        sListarEmpleadosCbo: sListarEmpleadosCbo,
        sListarEmpleadoPorDni : sListarEmpleadoPorDni,
        sListarEmpleadoPorCodigo : sListarEmpleadoPorCodigo,
        sRegistrar: sRegistrar,
        sEditar: sEditar,
        sAnular: sAnular ,
        sHabilitar : sHabilitar ,
        sDeshabilitar : sDeshabilitar
    });

    function sListarEmpleados(datos) { 
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"empleado/lista_empleados", 
            data : datos
      });
      return (request.then( handleSuccess,handleError ));
    }
    function sListarEmpleadosCbo(datos) { 
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"empleado/lista_empleados_cbo", 
            data : datos
      });
      return (request.then( handleSuccess,handleError ));
    }
    function sRegistrar (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"empleado/registrar", 
            data : datos
      });
      return (request.then( handleSuccess,handleError ));
    }
    function sEditar (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"empleado/editar", 
            data : datos
      });
      return (request.then( handleSuccess,handleError ));
    }
    function sAnular (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"empleado/anular", 
            data : datos
      });
      return (request.then( handleSuccess,handleError ));
    }
    function sHabilitar (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"empleado/habilitar", 
            data : datos
      });
      return (request.then( handleSuccess,handleError ));
    }
    function sDeshabilitar (datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"empleado/deshabilitar", 
            data : datos
      });
      return (request.then( handleSuccess,handleError ));
    }
    function sListarEmpleadoPorDni(datos) { 
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"empleado/lista_empleado_por_dni", 
            data : datos
      });
      return (request.then( handleSuccess,handleError ));
    }
    function sListarEmpleadoPorCodigo(datos) { 
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"empleado/lista_empleado_por_codigo", 
            data : datos
      });
      return (request.then( handleSuccess,handleError ));
    }

  });