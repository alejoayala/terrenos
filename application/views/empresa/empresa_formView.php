<div class="modal-header">
	<h4 class="modal-title"> {{ titleForm }}  </h4>
</div>
<div class="modal-body">
    <form class="row" name="formEmpresa"> 
		<div class="form-group mb-md col-md-6">
			<label class="control-label mb-xs">	Empresa O Razón Social <small class="text-danger">(*)</small> </label>
				<input ng-if="accion=='reg'" type="text" ng-model="fData.empresa" placeholder="Registre la empresa" typeahead="descripcion for descripcion in getEmpresasAutocomplete($viewValue)" 
					typeahead-loading="loading" class="form-control input-sm" tabindex="1" required focus-me />
				<input ng-if="accion=='edit'" type="text" class="form-control input-sm" ng-model="fData.empresa" placeholder="Registre la empresa" tabindex="1" focus-me required /> 
		</div>
		<div class="form-group mb-md col-md-6">
			<label class="control-label mb-xs"> RUC <small class="text-danger">(*)</small> </label>
			<div class="input-group"> 
				<input type="text" class="form-control input-sm" ng-model="fData.ruc_empresa" placeholder="Registre su RUC" required />
				<span class="input-group-btn">
					<button class="btn btn-default btn-sm" type="button" ng-click="verPopupConsultarSUNAT()">CONSULTAR RUC</button>
				</span>
			</div>
		</div>
		<div class="form-group mb-md col-md-6">
			<label class="control-label mb-xs"> Domicilio Fiscal <small class="text-danger">(*)</small> </label>
			<input type="text" class="form-control input-sm" ng-model="fData.domicilio_fiscal" placeholder="Registre su domicilio fiscal" required />
		</div>
		<div class="form-group mb-md col-md-6">
			<label class="control-label mb-xs"> Representante Legal <small class="text-danger">(*)</small> </label>
			<input type="text" class="form-control input-sm" ng-model="fData.representante_legal" placeholder="Registre su representante legal" required />
		</div>
		<div class="form-group mb-md col-md-6">
			<label class="control-label mb-xs"> Sede <small class="text-danger">(*)</small> </label>
			<select class="form-control input-sm" ng-model="fData.idsede" ng-options="item.id as item.descripcion for item in listaSede" required tabindex="2"> </select>
		</div>
		<div class="form-group mb-md col-md-6">
			<label class="control-label mb-xs"> Teléfono </label>
			<input type="text" class="form-control input-sm" ng-model="fData.telefono" placeholder="Registre su teléfono" />
		</div>
	</form>
</div>
<div class="modal-footer">
    <button class="btn btn-primary" ng-click="aceptar(); $event.preventDefault();" ng-disabled="formEmpresa.$invalid">Aceptar</button>
    <button class="btn btn-warning" ng-click="cancel()">Cancelar</button>
</div>