<div class="modal-header">
	<h4 class="modal-title"> {{ titleForm }}  </h4>
</div>
<div class="modal-body">
    <form class="row" name="formSeccion"> 
		<div class="form-group mb-md col-md-6">
			<label class="control-label mb-xs">Seccion <small class="text-danger">(*)</small> </label>
			<input type="text" class="form-control input-sm" ng-model="fData.nombre_seccion" placeholder="Registre la Seccion" tabindex="1" focus-me required />
		</div>
	</form>
</div>
<div class="modal-footer">
    <button class="btn btn-primary" ng-click="aceptar(); $event.preventDefault();" ng-disabled="formSeccion.$invalid">Aceptar</button>
    <button class="btn btn-warning" ng-click="cancel()">Cancelar</button>
</div>