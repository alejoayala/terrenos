<div class="modal-header">
	<h4 class="modal-title"> {{ titleFormAdd }} </h4>
</div>
<div class="modal-body">
    <form class="row"> 
		<div class="form-group col-md-4 mb-md">
			<label class="control-label mb-n">Grupo: </label>
			<p class="help-block mt-xs"> {{ mySelectionGrid[0].nombre }} </p>
		</div>
		<div class="form-group col-md-8 mb-md">
			<label class="control-label mb-n">Descripción: </label>
			<p class="help-block mt-xs"> {{ mySelectionGrid[0].descripcion }} </p>
		</div>
		<div class="form-group mb-md col-md-12">
			<label class="control-label">Agregar roles</label>
			<div ui-grid="gridOptionsRoles" ui-grid-pagination ui-grid-selection ui-grid-cellNav ui-grid-resize-columns ui-grid-move-columns class="grid table-responsive"></div>
		</div>
	</form>
</div>
<div class="modal-footer">
    <button class="btn btn-primary" ng-click="aceptar(); $event.preventDefault();">Aceptar</button>
    <button class="btn btn-warning" ng-click="cancel()">Cancelar</button>
</div>