<div class="modal-header">
	<h4 class="modal-title"> {{ titleForm }} </h4>
</div>
<div class="modal-body">
    <form class="row" id="formFamiliar" name="formEmpleado" novalidate> 
		<div class="form-group mb-md col-md-3" >
			<label class="control-label mb-xs">Nº Documento </small> </label> 
			<input type="text" class="form-control input-sm" ng-model="fData.dni" placeholder="Registre su Nº de doc." tabindex="1" focus-me ng-minlength="8" ng-pattern="/^[0-9]*$/" />
		</div>
	
		<div class="form-group mb-md col-md-3">
			<label class="control-label mb-xs">Apellido Paterno <small class="text-danger">(*)</small> </label>
			<input type="text" class="form-control input-sm" ng-model="fData.apellido_paterno" placeholder="Registre su apellido paterno" tabindex="2" required />
		</div>
		<div class="form-group mb-md col-md-3">
			<label class="control-label mb-xs">Apellido Materno <small class="text-danger">(*)</small> </label>
			<input type="text" class="form-control input-sm" ng-model="fData.apellido_materno" placeholder="Registre su apellido materno" tabindex="3" required />
		</div>
		<div class="form-group mb-md col-md-3">
			<label class="control-label mb-xs">Nombres <small class="text-danger">(*)</small> </label>
			<input type="text" class="form-control input-sm" ng-model="fData.nombres" placeholder="Registre su nombre" tabindex="4" required />
		</div>
		<div class="form-group mb-md col-md-3">
			<label class="control-label mb-xs">Direccion </label>
			<input type="text" class="form-control input-sm" ng-model="fData.domicilio" placeholder="Registre su domicilio" tabindex="5" />
		</div>
		<div class="form-group mb-md col-md-3">
			<label class="control-label mb-xs">Email </label>
			<input type="text" class="form-control input-sm" ng-model="fData.email" placeholder="Registre su email" tabindex="6" />
		</div>
		<div class="form-group mb-md col-md-3">
			<label class="control-label mb-xs">Tel.Fijo </label>
			<input type="text" class="form-control input-sm" ng-model="fData.telefono_fijo" placeholder="Registre su telefono" tabindex="7" />
		</div>
		<div class="form-group mb-md col-md-3">
			<label class="control-label mb-xs">Tel.Movil </label>
			<input type="text" class="form-control input-sm" ng-model="fData.telefono_movil" placeholder="Registre su telefono" tabindex="8" />
		</div>

        <div class="form-group mb-md col-md-3">
            <label class="control-label mb-xs"> Est.Civil <small class="text-danger">(*)</small> </label>
            <select class="form-control input-sm" ng-model="fData.idestadocivil" ng-options="item.id as item.nombre_estadocivil for item in listaEstadoCivil" tabindex="9" required ></select>     
        </div>
        <div class="form-group mb-md col-md-3">
            <label class="control-label mb-xs"> Sexo <small class="text-danger">(*)</small> </label>
            <select class="form-control input-sm" ng-model="fData.sexo" ng-options="item.id as item.nombresexo for item in listaSexo" tabindex="10" required ></select>     
        </div>
        <div class="form-group mb-md col-md-3">
            <label class="control-label mb-xs"> Grado Instr. <small class="text-danger">(*)</small> </label>
            <select class="form-control input-sm" ng-model="fData.idnivelinstruccion" ng-options="item.id as item.nombre_nivelinstruccion for item in listaNivelInstruccion" tabindex="11" required ></select>     
        </div>
   		<div class="form-group mb-md col-md-6" >
			<label class="control-label mb-xs">Asignar un Usuario</a></label>
			<div class="input-group">
				<span class="input-group-btn ">
					<input type="text" class="form-control input-sm" style="width:40px;margin-right:4px;" ng-model="fData.idusuario" placeholder="ID" readonly="true" />
				</span>
				<input type="text" class="form-control input-sm" ng-model="fData.usuario" ng-enter="verPopupListaUsuarios('md')" placeholder="Presione ENTER o Click en Seleccionar" />
				<span class="input-group-btn">
					<button class="btn btn-default btn-sm" type="button" ng-click="verPopupListaUsuarios('md')">Seleccionar</button>
				</span>
			</div>
		</div>

		<div class="form-group mb-md col-md-3" >
			<label class="control-label mb-xs">Fecha de Nacimiento </label>
			<div class="input-group" style="width: 200px;"> 
				<input type="text" class="form-control input-sm datepicker" ng-model="fData.fecha_nacimiento" data-inputmask="'alias': 'dd-mm-yyyy'" tabindex="12" />
				<!-- <div class="input-group-btn">
					<button type="button" class="btn btn-default btn-sm" ng-click="dateUI.openDP($event)"><i class="ti ti-calendar"></i></button>
				</div> -->
			</div>
		</div>

	</form>
</div>
<div class="modal-footer">
    <button class="btn btn-primary" ng-click="aceptar()" ng-disabled="formEmpleado.$invalid" >Aceptar</button>
    <button class="btn btn-warning" ng-click="cancel()">Cancelar</button>
</div>