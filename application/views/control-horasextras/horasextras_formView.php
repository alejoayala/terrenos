<div class="modal-header">
	<h4 class="modal-title"> {{ titleForm }}  </h4>
</div>
<div class="modal-body">
<form class="row" name="formControlHorasExtras">
    
    	<div class="form-group mb-md col-md-8" >
			<label class="control-label mb-xs"> Empleado <small class="text-danger">(*)</small></label>
			<div class="input-group">
				<span class="input-group-btn" ng-hide="true" >
					<input id="idempleado" type="text" class="form-control input-sm" style="width:100px;margin-right:4px;" ng-model="fData.idempleado" placeholder="ID" tabindex="1" min-length="1" required/>
				</span>
				<span class="input-group-btn">
					<input id="dniempleado" type="text" class="form-control input-sm" style="width:100px;margin-right:4px;" ng-model="fData.dni" placeholder="DNI" tabindex="2" ng-enter="obtenerEmpleadoPorDni(); " min-length="1" />
				</span>
				<input id="empleado" type="text" class="form-control input-sm" ng-model="fData.empleado" placeholder="Ingrese el Nombre del Empleado" typeahead-loading="loadingLocationsReaIns" typeahead="item.id as item.empleado for item in getEmpleadoAutocomplete($viewValue)"  typeahead-on-select="getSelectedEmpleado($item, $model, $label)" typeahead-min-length="2" tabindex="3" required/>
			</div>
		</div>
		<div class="form-group mb-md col-md-2" >
			<label class="control-label mb-xs">Fecha<small class="text-danger">(*)</small> </label>  
			<div class="input-group col-md-12"> 
				<input id="fecha" type="text" class="form-control input-sm mask" data-inputmask="'alias': 'dd-mm-yyyy'" ng-model="fData.fecha" tabindex="4" /> 
			</div>
		</div>
		<div class="form-group mb-md col-md-2" >
			<label class="control-label mb-xs">Hora<small class="text-danger">(*)</small> </label>  
			<div class="input-group col-md-12"> 
				<input id="hora_entrada" type="text" class="form-control input-sm mask" data-inputmask="'alias': 'hh:mm'" ng-model="fData.hora" tabindex="4" /> 
			</div>
		</div>

    
</form>
</div>
<div class="modal-footer">
    <button class="btn btn-primary" ng-click="aceptar(); $event.preventDefault();" ng-disabled="formControlHorasExtras.$invalid">Aceptar</button>
    <button class="btn btn-warning" ng-click="cancel()">Cancelar</button>
</div>