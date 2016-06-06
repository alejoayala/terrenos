<div class="modal-header">
	<h4 class="modal-title"> {{ titleForm }}  </h4>
</div>
<div class="modal-body">
    <form class="row" name="formEmpresaAdmin">
		<div class="form-group mb-md col-md-6">
			<label class="control-label mb-xs">Razón Social <small class="text-danger">(*)</small> </label>
			<input type="text" class="form-control input-sm" ng-model="fData.razon_social" placeholder="Registre Razón Social" tabindex="1" focus-me required />
		</div>

		<div class="form-group mb-md col-md-6">
			<label class="control-label mb-xs">Nombre Legal <small class="text-danger">(*)</small> </label>
			<input type="text" class="form-control input-sm" ng-model="fData.nombre_legal" placeholder="Registre Nombre Legal" tabindex="2" required />
		</div>

		<div class="form-group mb-md col-md-6">
			<label class="control-label mb-xs">Domicilio Fiscal </label>
			<input type="text" class="form-control input-sm" ng-model="fData.domicilio_fiscal" placeholder="Registre Domicilio Fiscal" tabindex="3"  />
		</div>

		<div class="form-group mb-md col-md-6">
			<label class="control-label mb-xs">Dirección </label>
			<input type="text" class="form-control input-sm" ng-model="fData.direccion" placeholder="Registre Nombre Legal" tabindex="4"  />
		</div>

		<div class="form-group mb-md col-md-6">
			<label class="control-label mb-xs">RUC </label>
			<input type="text" class="form-control input-sm" ng-model="fData.ruc" placeholder="Registre Nombre Legal" tabindex="5" ng-minlength="11" ng-maxlength="11" maxlength="11" pattern="[0-9]{11}"/>
		</div>



		<div class="form-group mb-md col-md-6" >
			<label class="control-label mb-xs">Logo de la Empresa </label>
			<div class="fileinput fileinput-new" style="width: 100%;" data-provides="fileinput">
				<div class="fileinput-preview thumbnail mb20" data-trigger="fileinput" style="width: 100%; height: 150px;">
					<img ng-if="fData.nombre_logo" ng-src="{{ dirImages + 'dinamic/empresa/' + fData.nombre_logo }}" />
				</div>
				<div>

					<a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Quitar</a>
					<span class="btn btn-default btn-file">
						<span class="fileinput-new">Seleccionar imagen</span>
						<span class="fileinput-exists">Cambiar</span>
						<input type="file" name="file" file-model="fData.fotoEmpresa" />
					</span>
				</div>
			</div>
		</div>
		<fieldset style="width: 100%;">
		<legend style="font-size: 18px;">Redes Sociales</legend>
			<div class="form-group mb-md col-md-4">
			<label class="control-label mb-xs">Facebook </label>
			<input type="url" class="form-control input-sm" ng-model="fData.rs_facebook" placeholder="Registre el facebook de la Empresa" tabindex="6"  />
			</div>
			<div class="form-group mb-md col-md-4">
				<label class="control-label mb-xs">Twitter </label>
				<input type="url" class="form-control input-sm" ng-model="fData.rs_twitter" placeholder="Registre el twitter de la Empresa" tabindex="7"  />
			</div>
			<div class="form-group mb-md col-md-4">
				<label class="control-label mb-xs">Youtube </label>
				<input type="url" class="form-control input-sm" ng-model="fData.rs_youtube" placeholder="Registre el canal youtube de la Empresa" tabindex="8"  />
			</div>

		</fieldset>


	</form>
</div>
<div class="modal-footer">
    <button class="btn btn-primary" ng-click="aceptar(); $event.preventDefault();" ng-disabled="formEmpresaAdmin.$invalid">Aceptar</button>
    <button class="btn btn-warning" ng-click="cancel()">Cancelar</button>
</div>