<div class="modal-header">
	<h4 class="modal-title"> {{ titleForm }} </h4>
</div>
<div class="modal-body">
    <form class="row" id="formFamiliar" name="formFamiliar" novalidate> 
		<div class="form-group mb-md col-md-3" >
			<label class="control-label mb-xs">Nº Documento </small> </label> 
			<input type="text" class="form-control input-sm" ng-model="fData.dni" placeholder="Registre su Nº de doc." focus-me ng-minlength="8" ng-pattern="/^[0-9]*$/" />
		</div>
	
		<div class="form-group mb-md col-md-3">
			<label class="control-label mb-xs">Apellido Paterno <small class="text-danger">(*)</small> </label>
			<input type="text" class="form-control input-sm" ng-model="fData.apellido_paterno" placeholder="Registre su apellido paterno" required />
		</div>
		<div class="form-group mb-md col-md-3">
			<label class="control-label mb-xs">Apellido Materno <small class="text-danger">(*)</small> </label>
			<input type="text" class="form-control input-sm" ng-model="fData.apellido_materno" placeholder="Registre su apellido materno" required />
		</div>
		<div class="form-group mb-md col-md-3">
			<label class="control-label mb-xs">Nombres <small class="text-danger">(*)</small> </label>
			<input type="text" class="form-control input-sm" ng-model="fData.nombres" placeholder="Registre su nombre" required />
		</div>

        <div class="form-group mb-md col-md-3">
            <label class="control-label mb-xs"> Est.Civil <small class="text-danger">(*)</small> </label>
            <select class="form-control input-sm" ng-model="fData.idestadocivil" ng-options="item.id as item.nombre_estadocivil for item in listaEstadoCivil" tabindex="5" required ></select>     
        </div>
        <div class="form-group mb-md col-md-3">
            <label class="control-label mb-xs"> Grado Instr. <small class="text-danger">(*)</small> </label>
            <select class="form-control input-sm" ng-model="fData.idnivelinstruccion" ng-options="item.id as item.nombre_nivelinstruccion for item in listaNivelInstruccion" tabindex="5" required ></select>     
        </div>
		<div class="form-group mb-md col-md-3" >
			<label class="control-label mb-xs">Fecha de Nacimiento </label>
			<div class="input-group" style="width: 200px;"> 
				<input type="text" class="form-control input-sm datepicker" ng-model="fData.fecha_nacimiento" data-inputmask="'alias': 'dd-mm-yyyy'" />
				<!-- <div class="input-group-btn">
					<button type="button" class="btn btn-default btn-sm" ng-click="dateUI.openDP($event)"><i class="ti ti-calendar"></i></button>
				</div> -->
			</div>
		</div>

	</form>
</div>
<div class="modal-footer">
    <button class="btn btn-primary" ng-click="aceptar()" ng-disabled="formFamiliar.$invalid" >Aceptar</button>
    <button class="btn btn-warning" ng-click="cancel()">Cancelar</button>
</div>