<div class="modal-header pt-sm pb-xs">
	<h4 class="modal-title"> {{ titleForm }} </h4>
</div>
<div class="modal-body pt-n pb-n">
    <form class="row" name="formPersona" novalidate>
		<div class="form-group col-md-6 col-lg-6 pt-md pb-md mb-n" style="border-right: 1px solid black;">
			<div class="row">
				<div class="form-group mb-md col-md-7" >
					<div class="fileinput fileinput-new" style="width: 100%;" data-provides="fileinput">
						<div class="fileinput-preview thumbnail mb20" data-trigger="fileinput" style="width: 100%; height: 120px;">
							<img ng-if="fData.nombre_foto" ng-src="{{ dirImages + 'dinamic/empleado/' + fData.nombre_foto }}" />
						</div>
						<!--<div>
							<a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Quitar</a> 
							<span class="btn btn-default btn-file"><span class="fileinput-new">Seleccionar imagen</span> 
								<span class="fileinput-exists">Cambiar</span> 
								<input type="file" name="file" file-model="fData.fotoEmpleado" /> 
							</span>
						</div>-->
					</div>
				</div>
			</div>
			<div class="row">
	    		<div class="form-group mb-md col-md-4">
					<label class="control-label mb-xs"> Credencial </label>
					<input type="text" class="form-control input-sm" ng-model="fData.credencial" placeholder="Registre num. credencial" tabindex="1" focus-me ng-minlength="3" ng-pattern="/^[0-9]*$/" required/> 
				</div>
	    		<div class="form-group mb-md col-md-4">
					<label class="control-label mb-xs"> DNI </label>
					<input ng-init="verificaDNI();" type="text" class="form-control input-sm" ng-model="fData.dni" placeholder="Registre su dni" tabindex="2" ng-minlength="8" ng-pattern="/^[0-9]*$/" ng-change="verificaDNI();" /> 
				</div>
	    		<div class="form-group mb-md col-md-4">
					<label class="control-label mb-xs">Nombres <small class="text-danger">(*)</small> </label>
					<input type="text" class="form-control input-sm" ng-model="fData.nombres" placeholder="Registre su nombre" required tabindex="3" />
				</div>
	    	</div>
	    	<div class="row">
				<div class="form-group mb-md col-md-4">
					<label class="control-label mb-xs">Apellido Paterno <small class="text-danger">(*)</small> </label>
					<input type="text" class="form-control input-sm" ng-model="fData.apellido_paterno" placeholder="Registre su apellido paterno" required tabindex="4" />
				</div>
				<div class="form-group mb-md col-md-4">
					<label class="control-label mb-xs">Apellido Materno <small class="text-danger">(*)</small> </label>
					<input type="text" class="form-control input-sm" ng-model="fData.apellido_materno" placeholder="Registre su apellido materno" required tabindex="5" /> 
				</div>
				<div class="form-group mb-md col-md-4">
					<label class="control-label mb-xs">Teléfono </label>
					<input type="tel" class="form-control input-sm" ng-model="fData.telefono" placeholder="Registre su teléfono" ng-minlength="6" tabindex="6" />
				</div>
			</div>
			<div class="row">
				<div class="form-group mb-md col-md-4">
					<label class="control-label mb-xs">Celular </label>
					<input type="tel" class="form-control input-sm" ng-model="fData.celular" placeholder="Registre su celular" ng-minlength="9" tabindex="7" />
				</div>
				<div class="form-group mb-md col-md-4" >
					<label class="control-label mb-xs">E-mail</label>
					<input type="email" class="form-control input-sm" ng-model="fData.email" placeholder="Registre su e-mail" tabindex="8" />
				</div>
				<div class="form-group mb-md col-md-4" >
					<label class="control-label mb-xs">Fecha de Nacimiento <small class="text-danger">(*)</small> </label>  
					<div class="input-group" style="width: 150px;"> 
						<input type="text" class="form-control input-sm mask" data-inputmask="'alias': 'dd-mm-yyyy'" ng-model="fData.fec_nacimiento" required tabindex="9"/> 
					</div>
				</div>
			</div>
			<div class="row">
				<div class="form-group mb-md col-md-4" >
					<label class="control-label mb-xs"> Departamento </label>
					<div class="input-group">
						<span class="input-group-btn ">
							<input type="text" class="form-control input-sm" style="width:30px;margin-right:4px;" ng-model="fData.iddepartamento" placeholder="ID" tabindex="10" ng-change="obtenerDepartamentoPorCodigo(); $event.preventDefault();" min-length="2" />
						</span>
						<input id="fDatadepartamento" type="text" class="form-control input-sm" ng-model="fData.departamento" placeholder="Ingrese el Departamento" typeahead-loading="loadingLocationsDpto" typeahead="item as item.descripcion for item in getDepartamentoAutocomplete($viewValue)" typeahead-on-select="getSelectedDepartamento($item, $model, $label)" typeahead-min-length="2" tabindex="11"/>
					</div>
					<i ng-show="loadingLocationsDpto" class="fa fa-refresh"></i>
	                <div ng-show="noResultsLD">
	                  <i class="fa fa-remove"></i> No se encontró resultados 
	                </div>
				</div>

				<div class="form-group mb-md col-md-4" >
					<label class="control-label mb-xs"> Provincia </label>
					<div class="input-group">
						<span class="input-group-btn ">
							<input type="text" class="form-control input-sm" style="width:30px;margin-right:4px;" ng-model="fData.idprovincia" placeholder="ID"tabindex="12" ng-change="obtenerProvinciaPorCodigo(); $event.preventDefault();" min-length="2" />
						</span>
						<input id="fDataprovincia" type="text" class="form-control input-sm" ng-model="fData.provincia" placeholder="Ingrese la Provincia"   typeahead-loading="loadingLocationsProv" 
	                  typeahead="item as item.descripcion for item in getProvinciaAutocomplete($viewValue)" typeahead-on-select="getSelectedProvincia($item, $model, $label)" typeahead-min-length="2" tabindex="13"/>
					</div>
					<i ng-show="loadingLocationsProv" class="fa fa-refresh"></i>
	                <div ng-show="noResultsLP">
	                  <i class="fa fa-remove"></i> No se encontró resultados 
	                </div>
				</div>
				<div class="form-group mb-md col-md-4" >
					<label class="control-label mb-xs"> Distrito </label>
					<div class="input-group">
						<span class="input-group-btn ">
							<input type="text" class="form-control input-sm" style="width:30px;margin-right:4px;" ng-model="fData.iddistrito" placeholder="ID"tabindex="14" ng-change="obtenerDistritoPorCodigo(); $event.preventDefault();" min-length="2" />
						</span>
						<input id="fDatadistrito" type="text" class="form-control input-sm" ng-model="fData.distrito" placeholder="Ingrese el Distrito"  typeahead-loading="loadingLocationsDistr" typeahead="item as item.descripcion for item in getDistritoAutocomplete($viewValue)" typeahead-on-select="getSelectedDistrito($item, $model, $label)" typeahead-min-length="2" tabindex="15"/>
					</div>
					<i ng-show="loadingLocationsDistr" class="fa fa-refresh"></i>
	                <div ng-show="noResultsLDis">
	                  <i class="fa fa-remove"></i> No se encontró resultados 
	                </div>
				</div>
			</div>
			<div class="row">
				<div class="form-group mb-md col-md-4" >
					<label class="control-label mb-xs">Lugar de nacimiento</label>
					<input type="text" class="form-control input-sm" ng-model="fData.lugar_nacimiento" placeholder="Registre su lugar nacimiento" tabindex="16"  />
				</div>
				<div class="form-group mb-md col-md-4" >
					<label class="control-label mb-xs">Ocupación </label>
					<input type="text" class="form-control input-sm" ng-model="fData.ocupacion" placeholder="Registre su ocupacion" tabindex="17"  />
				</div>
				<div class="form-group mb-md col-md-4" >
					<label class="control-label mb-xs">Grado de Instrucción</label> {{ fSelected }}
					<select class="form-control input-sm" ng-model="fData.idtipozona" ng-options="item.id as item.descripcion for item in listaTipoZonas" tabindex="18"> </select>
				</div>
			</div>
			<div class="row">
				<div class="form-group mb-md col-md-4" >
					<label class="block" style="margin-bottom: 4px;"> Sexo <small class="text-danger">(*)</small> </label>
					<select class="form-control input-sm" ng-model="fData.sexo" ng-options="item.id as item.descripcion for item in listaSexos" tabindex="19" required > </select>
				</div>
				<div class="form-group mb-md col-md-4" >
					<label class="control-label mb-xs">Estado Civil </label>
					<select class="form-control input-sm" ng-model="fData.idestadocivil" ng-options="item.id as item.descripcion for item in listaSexos" tabindex="20" required > </select>
				</div>
				<div class="form-group mb-md col-md-4" >
					<label class="control-label mb-xs">Profesión</label>
					<input type="text" class="form-control input-sm" ng-model="fData.profesion" placeholder="Registre su profesion" tabindex="21" />
				</div>

			</div>

		</div>
		<!------------------------------------------- CONYUGE ------------------------------------------ -->
		<div class="form-group col-md-6 col-lg-6 pt-md pb-md mb-n" >
			<div class="row">
	    		<div class="form-group mb-md col-md-4">

				</div>
	    		<div class="form-group mb-md col-md-4">
					<label class="control-label mb-xs"> DNI </label>
					<input ng-init="verificaDNI();" type="text" class="form-control input-sm" ng-model="fData.num_documento" placeholder="Registre su dni" tabindex="22" ng-minlength="8" ng-pattern="/^[0-9]*$/" ng-change="verificaDNI();" /> 
				</div>
	    		<div class="form-group mb-md col-md-4">
					<label class="control-label mb-xs">Nombres <small class="text-danger">(*)</small> </label>
					<input type="text" class="form-control input-sm" ng-model="fData.nombres" placeholder="Registre su nombre" required tabindex="23" />
				</div>
	    	</div>
	    	<div class="row">
				<div class="form-group mb-md col-md-4">
					<label class="control-label mb-xs">Apellido Paterno <small class="text-danger">(*)</small> </label>
					<input type="text" class="form-control input-sm" ng-model="fData.apellido_paterno" placeholder="Registre su apellido paterno" required tabindex="24" />
				</div>
				<div class="form-group mb-md col-md-4">
					<label class="control-label mb-xs">Apellido Materno <small class="text-danger">(*)</small> </label>
					<input type="text" class="form-control input-sm" ng-model="fData.apellido_materno" placeholder="Registre su apellido materno" required tabindex="25" /> 
				</div>
				<div class="form-group mb-md col-md-4">
					<label class="control-label mb-xs">Teléfono </label>
					<input type="tel" class="form-control input-sm" ng-model="fData.telefono" placeholder="Registre su teléfono" ng-minlength="6" tabindex="26" />
				</div>
			</div>
			<div class="row">
				<div class="form-group mb-md col-md-4">
					<label class="control-label mb-xs">Celular </label>
					<input type="tel" class="form-control input-sm" ng-model="fData.celular" placeholder="Registre su celular" ng-minlength="9" tabindex="27" />
				</div>
				<div class="form-group mb-md col-md-4" >
					<label class="control-label mb-xs">E-mail</label>
					<input type="email" class="form-control input-sm" ng-model="fData.email" placeholder="Registre su e-mail" tabindex="28" />
				</div>
				<div class="form-group mb-md col-md-4" >
					<label class="control-label mb-xs">Fecha de Nacimiento <small class="text-danger">(*)</small> </label>  
					<div class="input-group" style="width: 150px;"> 
						<input type="text" class="form-control input-sm mask" data-inputmask="'alias': 'dd-mm-yyyy'" ng-model="fData.fecha_nacimiento" required tabindex="29"/> 
					</div>
				</div>
			</div>
			<div class="row">
				<div class="form-group mb-md col-md-4" >
					<label class="control-label mb-xs"> Departamento </label>
					<div class="input-group">
						<span class="input-group-btn ">
							<input type="text" class="form-control input-sm" style="width:30px;margin-right:4px;" ng-model="fData.iddepartamento" placeholder="ID"tabindex="30" ng-change="obtenerDepartamentoPorCodigo(); $event.preventDefault();" min-length="2" />
						</span>
						<input id="fDatadepartamento" type="text" class="form-control input-sm" ng-model="fData.departamento" placeholder="Ingrese el Departamento" typeahead-loading="loadingLocationsDpto" typeahead="item as item.descripcion for item in getDepartamentoAutocomplete($viewValue)" typeahead-on-select="getSelectedDepartamento($item, $model, $label)" typeahead-min-length="2" tabindex="31"/>
					</div>
					<i ng-show="loadingLocationsDpto" class="fa fa-refresh"></i>
	                <div ng-show="noResultsLD">
	                  <i class="fa fa-remove"></i> No se encontró resultados 
	                </div>
				</div>

				<div class="form-group mb-md col-md-4" >
					<label class="control-label mb-xs"> Provincia </label>
					<div class="input-group">
						<span class="input-group-btn ">
							<input type="text" class="form-control input-sm" style="width:30px;margin-right:4px;" ng-model="fData.idprovincia" placeholder="ID"tabindex="32" ng-change="obtenerProvinciaPorCodigo(); $event.preventDefault();" min-length="2" />
						</span>
						<input id="fDataprovincia" type="text" class="form-control input-sm" ng-model="fData.provincia" placeholder="Ingrese la Provincia"   typeahead-loading="loadingLocationsProv" 
	                  typeahead="item as item.descripcion for item in getProvinciaAutocomplete($viewValue)" typeahead-on-select="getSelectedProvincia($item, $model, $label)" typeahead-min-length="2" tabindex="33"/>
					</div>
					<i ng-show="loadingLocationsProv" class="fa fa-refresh"></i>
	                <div ng-show="noResultsLP">
	                  <i class="fa fa-remove"></i> No se encontró resultados 
	                </div>
				</div>
				<div class="form-group mb-md col-md-4" >
					<label class="control-label mb-xs"> Distrito </label>
					<div class="input-group">
						<span class="input-group-btn ">
							<input type="text" class="form-control input-sm" style="width:30px;margin-right:4px;" ng-model="fData.iddistrito" placeholder="ID"tabindex="34" ng-change="obtenerDistritoPorCodigo(); $event.preventDefault();" min-length="2" />
						</span>
						<input id="fDatadistrito" type="text" class="form-control input-sm" ng-model="fData.distrito" placeholder="Ingrese el Distrito"  typeahead-loading="loadingLocationsDistr" typeahead="item as item.descripcion for item in getDistritoAutocomplete($viewValue)" typeahead-on-select="getSelectedDistrito($item, $model, $label)" typeahead-min-length="2" tabindex="35"/>
					</div>
					<i ng-show="loadingLocationsDistr" class="fa fa-refresh"></i>
	                <div ng-show="noResultsLDis">
	                  <i class="fa fa-remove"></i> No se encontró resultados 
	                </div>
				</div>
			</div>
			<div class="row">
				<div class="form-group mb-md col-md-4" >
					<label class="control-label mb-xs">Lugar de nacimiento</label>
					<input type="text" class="form-control input-sm" ng-model="fData.lugar_nacimiento" placeholder="Registre su lugar nacimiento" tabindex="16"  />
				</div>
				<div class="form-group mb-md col-md-4" >
					<label class="control-label mb-xs">Ocupación </label>
					<input type="text" class="form-control input-sm" ng-model="fData.ocupacion" placeholder="Registre su ocupación" tabindex="37"  />
				</div>
				<div class="form-group mb-md col-md-4" >
					<label class="control-label mb-xs">Grado de Instrucción</label> {{ fSelected }}
					<select class="form-control input-sm" ng-model="fData.idnivelinstruccion" ng-options="item.id as item.descripcion for item in listaTipoZonas" tabindex="38"> </select>
				</div>
			</div>
			<div class="row">
				<div class="form-group mb-md col-md-4" >
					<label class="block" style="margin-bottom: 4px;"> Sexo <small class="text-danger">(*)</small> </label>
					<select class="form-control input-sm" ng-model="fData.sexo" ng-options="item.id as item.descripcion for item in listaSexos" tabindex="39" required > </select>
				</div>
				<div class="form-group mb-md col-md-4" >
					<label class="control-label mb-xs">Estado Civil </label>
					<select class="form-control input-sm" ng-model="fData.idestadocivil" ng-options="item.id as item.descripcion for item in listaSexos" tabindex="20" required > </select>
				</div>
				<div class="form-group mb-md col-md-4" >
					<label class="control-label mb-xs">Profesión</label>
					<input type="text" class="form-control input-sm" ng-model="fData.numero" placeholder="Registre su profesión" tabindex="41" />
				</div>

			</div>
			
		</div>
	</form>
</div>
<div class="modal-footer">
    <button class="btn btn-success" ng-click="imprimir()" tabindex="42" > <i class="fa fa-print"></i> Imprimir </button>
    <button class="btn btn-primary" ng-click="verificarCli(); $event.preventDefault();" ng-disabled="formPersona.$invalid" tabindex="43" > Aceptar </button>
    <button class="btn btn-warning" ng-click="cancel()" tabindex="44"> Cancelar </button>
</div>
