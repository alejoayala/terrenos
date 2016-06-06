<div class="modal-header">
	<h4 class="modal-title"> {{ titleForm }} </h4>
</div>
<div class="modal-body">
    <form class="row" name="formUsuario" novalidate > 
    	<div class="form-group mb-md col-md-6">
			<label class="control-label mb-xs">Grupo <small class="text-danger">(*)</small> </label>
			<select required class="form-control input-sm"  ng-model="fDataUsuario.grupoId" ng-options="item.id as item.descripcion for item in listaGrupos" focus-me ></select>
		</div>
		<div class="form-group mb-md col-md-6">
			<label class="control-label mb-xs">E-mail </label>
			<input type="email" class="form-control input-sm" ng-model="fDataUsuario.email" placeholder="Registre su correo electrónico" />
		</div>
		<div class="form-group mb-md col-md-6">
			<label class="control-label mb-xs">Usuario <small class="text-danger">(*)</small> </label>
			<input required ng-minlength="4" type="text" class="form-control input-sm" ng-model="fDataUsuario.usuario" placeholder="Registre su usuario" />
		</div>
		<div class="form-group mb-md col-md-6" ng-if="boolForm == 'reg'">
			<label class="control-label mb-xs">Clave <small class="text-danger">(*)</small> </label> 
			<input required ng-minlength="6" type="password" class="form-control input-sm" ng-model="fDataUsuario.clave" placeholder="Registre su clave" />
		</div>
	</form>
</div>
<div class="modal-footer">
    <button class="btn btn-primary" ng-click="aceptar(); $event.preventDefault();" ng-disabled="formUsuario.$invalid">Aceptar</button>
    <button class="btn btn-warning" ng-click="cancel()">Cancelar</button>
</div>