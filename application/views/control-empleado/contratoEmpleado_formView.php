<div class="modal-header">
	<h4 class="modal-title"> {{ titleForm }}  </h4>
</div>
<div class="modal-body">
<form class="row" name="formControlEmpleado">
    
    	<div class="form-group mb-md col-md-8" >
			<label class="control-label mb-xs"> Empleado <small class="text-danger">(*)</small></label>
			<div class="input-group">
				<span class="input-group-btn" ng-hide="true" >
					<input id="idempleado" type="text" class="form-control input-sm" style="width:100px;margin-right:4px;" ng-model="fData.idempleado" placeholder="ID" tabindex="1" min-length="1" />
				</span>
				<span class="input-group-btn">
					<input id="dniempleado" type="text" class="form-control input-sm" style="width:100px;margin-right:4px;" ng-model="fData.dni" placeholder="DNI" tabindex="2" ng-enter="obtenerEmpleadoPorDni(); " min-length="1" />
				</span>
				<input id="empleado" type="text" class="form-control input-sm" ng-model="fData.empleado" placeholder="Ingrese el Nombre del Empleado" typeahead-loading="loadingLocationsReaIns" typeahead="item.id as item.empleado for item in getEmpleadoAutocomplete($viewValue)"  typeahead-on-select="getSelectedEmpleado($item, $model, $label)" typeahead-min-length="2" tabindex="3"/>
			</div>
		</div>
		<div class="form-group mb-md col-md-2" >
			<label class="control-label mb-xs">Fec.Inicio<small class="text-danger">(*)</small> </label>  
			<div class="input-group col-md-12"> 
				<input id="fechainicio" type="text" class="form-control input-sm mask" data-inputmask="'alias': 'dd-mm-yyyy'" ng-model="fData.fecha_inicio" tabindex="4" /> 
			</div>
		</div>
		<div class="form-group mb-md col-md-2" >
			<label class="control-label mb-xs">Fec.Final<small class="text-danger">(*)</small> </label>  
			<div class="input-group col-md-12"> 
				<input id="fechafinal" type="text" class="form-control input-sm mask" data-inputmask="'alias': 'dd-mm-yyyy'" ng-model="fData.fecha_final" tabindex="5" /> 
			</div>
		</div>
		<div class="form-group mb-md col-md-3">
			<label class="control-label mb-xs"> Cargo <small class="text-danger">(*)</small> </label>
			<select class="form-control input-sm" ng-model="fData.idcargo" ng-options="item.id as item.nombre_cargo for item in listaFiltroCargo" tabindex="6" required ></select> 		
		</div>
		<div class="form-group mb-md col-md-3">
			<label class="control-label mb-xs"> Seccion <small class="text-danger">(*)</small> </label>
			<select class="form-control input-sm" ng-model="fData.idseccion" ng-options="item.id as item.nombre_seccion for item in listaFiltroSeccion" tabindex="7" required></select> 		
		</div>
		<div class="form-group mb-md col-md-3">
			<label class="control-label mb-xs"> AFP <small class="text-danger">(*)</small> </label>
			<select class="form-control input-sm" ng-model="fData.idafp" ng-options="item.id as item.nombre_afp for item in listaFiltroAfp" tabindex="8" required></select> 		
		</div>
		<div class="form-group mb-md col-md-3">
			<label class="control-label mb-xs"> Banco <small class="text-danger">(*)</small> </label>
			<select class="form-control input-sm" ng-model="fData.idbanco" ng-options="item.id as item.nombre_banco for item in listaFiltroBanco" tabindex="9" required ></select> 		
		</div>
		<div class="form-group mb-md col-md-2">
			<label class="control-label mb-xs"> Asegurado <small class="text-danger">(*)</small> </label>
			<select class="form-control input-sm" ng-model="fData.estado_asegurado" ng-options="item.id as item.estado_asegurado for item in listaAsegurado" tabindex="9" required ></select> 		
		</div>
		<div class="form-group mb-md col-md-2">
			<label class="control-label mb-xs"> Numero Cuenta </label>
			<input type="text" class="form-control input-sm" ng-model="fData.numero_cuenta" placeholder="Registre Num.Cuenta" tabindex="10" required />
		</div>
		<div class="form-group mb-md col-md-2">
			<label class="control-label mb-xs"> Monto Salario </label>
			<input type="text" class="form-control input-sm" ng-model="fData.monto_salario" placeholder="Registre Salario" tabindex="11" required />	
		</div>
        <div class="form-group mb-md col-md-6">
			<label class="control-label mb-xs"> Observaciones </label>
            <textarea class="form-control input-sm" ng-model="fData.observaciones" placeholder="Observaciones" cols="50" tabindex="12"></textarea> 
        </div>

    
</form>
</div>
<div class="modal-footer">
    <button class="btn btn-primary" ng-click="aceptar(); $event.preventDefault();" ng-disabled="formControlEmpleado.$invalid">Aceptar</button>
    <button class="btn btn-warning" ng-click="cancel()">Cancelar</button>
</div>