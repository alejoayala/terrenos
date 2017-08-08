<div class="modal-header">
	<h4 class="modal-title"> {{ titleFormAdd }} </h4>
</div>
<div class="modal-body">
    <form class="row"> 
    	<div class="form-group col-md-4 mb-md">
			<label class="control-label mb-n"> Número de Documento: </label>
			<p class="help-block mt-xs"> {{ mySelectionGrid[0].num_documento }} </p>
		</div>
		<div class="form-group col-md-4 mb-md">
			<label class="control-label mb-n"> Empleado: </label>
			<p class="help-block mt-xs"> {{ mySelectionGrid[0].empleado }} </p>
		</div>
		<div class="form-inline col-md-5 mt-sm">
			<label class="control-label">Agregar Familiar: </label>
		</div>
		<div class="form-group col-md-7 mb-sm" >
			<input type="text" ng-change="buscar()" class="form-control pull-right" ng-model="searchText" 
				placeholder="Busque Familiar" focus-me style="width: 92%;" />
		</div>
		<div class="form-group col-md-6 mb-sm" ng-if="mySelectionFamiliaresGrid.length == 1" >
	   		<label class="control-label mb-xs"> Tipo Consanguineo <small class="text-danger">(*)</small> </label>
            <select class="form-control input-sm " ng-model="fDataAdd.tipo_consanguineo" ng-options="item.id as item.tipo_consanguineo for item in listaTipoConsanguineo" tabindex="9" required ></select>     
		</div>
		<div class="form-group mb-md col-md-12">
			<div ui-grid="gridOptionsFamiliares" ui-grid-pagination ui-grid-selection ui-grid-cellNav ui-grid-resize-columns ui-grid-move-columns class="grid table-responsive"></div>
		</div>
	</form>
</div>
<div class="modal-footer">
    <button class="btn btn-primary" ng-click="aceptar(); $event.preventDefault();">Aceptar</button>
    <button class="btn btn-warning" ng-click="cancel()">Cancelar</button>
</div>