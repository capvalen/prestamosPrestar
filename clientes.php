<?php 
include 'php/conkarl.php';
date_default_timezone_set('America/Lima');
include "php/variablesGlobales.php";
require_once('vendor/autoload.php');
$base58 = new StephenHill\Base58();?>
<!DOCTYPE html>
<html lang="es">

<head>

		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, shrink-to-fit=no, initial-scale=1">
		<meta name="description" content="">
		<meta name="author" content="">

		<title>Clientes - <?= $Htitle;?></title>

		<!-- Bootstrap Core CSS -->
		<?php include 'headers.php'; ?>
		<link rel="stylesheet" href="css/awesome-bootstrap-checkbox.css?version=1.0.6">
</head>

<body>

<style>
/* input{margin-bottom: 0px;} */
.tdVigente{border-bottom: 1px solid #545454;}
</style>
<div id="wrapper">
	<!-- Sidebar -->
	<?php include 'menu-wrapper.php' ?>
	<!-- /#sidebar-wrapper -->
<!-- Page Content -->
<div id="page-content-wrapper">
	<div class="container-fluid ">
		<div class="row noselect">
			<div class="col-lg-12 contenedorDeslizable ">
			<!-- Empieza a meter contenido principal -->
			<h2 class="purple-text text-lighten-1"><i class="icofont-users"></i> Zona clientes</h2><hr>
			
			<button class="btn btn-infocat btn-outline btnSinBorde" id="btnAddClientes"><i class="icofont-ui-add"></i> Nuevo cliente</button>
		<? /* if( isset($_GET['idCliente']) && in_array($_COOKIE['ckPower'], $soloDios)){ */ ?>
			<button class="btn btn-dark btn-outline btnSinBorde" id="btnEditClientes"><i class="icofont-edit"></i> Editar cliente</button>
		<? /* } */ ?>
			<div class="form-inline hidden">
				<div class="form-group"><label for="" style='margin-top:-3px'>Filtro de clientes:</label> <input type="text" class='form-control' id="txtClientesZon" placeholder='Clientes' autocomplete="off" style="margin-bottom: 0px;">
				<button class="btn btn-infocat btn-outline btnSinBorde" id="btnFiltrarClientes"><i class="icofont-search"></i></button>

			</div></div>

			<div class="listarTodosClientes">
				<?php if(!isset($_GET['buscar']) && !isset($_GET['idCliente'])):
					$_POST['restriccion'] = ($_COOKIE['ckPower'] != 1) ? $_COOKIE['ckidUsuario'] : -1;
					include 'php/listarTodosClientes.php';
				endif; ?>
			</div>

			<?php if( isset($_GET['buscar'])){ ?>
			<div class="container-fluid row"><br>
				<h4><?php if(isset($_GET['buscar'])){echo 'Resultado de la búsqueda';}else{ echo 'Últimos clientes registrados';} ?></h4>
				<div class="table-responsive">
					<table class="table ">
					<thead>
						<tr>
							<th>Cod.</th>
							<th>D.N.I.</th>
							<th>Apellidos y nombres</th>
							<th>Recurrente</th>
							<th>Dirección</th>
							<th>Celular</th>
							<th>Estado civil</th>
							<th>@</th>
						</tr>
					</thead>
					<tbody>
						<?php 
						if (isset($_GET['buscar'])){
							require 'php/buscarCliente.php';
						}else{
							//require 'php/listarUltimos20Clientes.php';
						}
						?>
					</tbody>
					</table>
				</div>
				<?php }
				if( isset( $_GET['idCliente'] )){
				$idCli= $base58->decode($_GET['idCliente']);
				$sqlDato= "SELECT `idCliente`, `cliDni`, lower(`cliNombres`) as `cliNombres`, lower(`cliApellidoPaterno`) as `cliApellidoPaterno`, lower(`cliApellidoMaterno`) as `cliApellidoMaterno`, `cliSexo`, `cliNumHijos`, `cliDireccionesIgual`,
				ca.calDescripcion, lower(a.addrDireccion) as addrDireccion, lower(a.addrReferencia) as addrReferencia, a.addrNumero,
				lower(di.distrito) as distrito, lower(pro.provincia) as provincia, lower(de.departamento) as departamento,
				lower(can.calDescripcion) as ncalDescripcion, lower(an.addrDireccion) as naddrDireccion, lower(an.addrReferencia) as naddrReferencia, an.addrNumero as naddrNumero, 
				lower(din.distrito) as ndistrito, lower(pron.provincia) as nprovincia, lower(den.departamento) as ndepartamento,
				`cliCelularPersonal`, `cliCelularReferencia`, ec.civDescripcion, `cliActivo`
				FROM `cliente` c
				inner join address a on a.idAddress= c.`cliDireccionCasa`
				inner join distrito di on di.idDistrito = a.idDistrito
				inner join provincia pro on pro.idProvincia = a.idProvincia
				inner join departamento de on de.idDepartamento = a.idDepartamento
				inner join calles ca on ca.idCalle = a.idCalle
				
				inner join address an on an.idAddress= c.`cliDireccionNegocio`
				inner join distrito din on din.idDistrito = an.idDistrito
				inner join provincia pron on pron.idProvincia = an.idProvincia
				inner join departamento den on den.idDepartamento = an.idDepartamento
				inner join calles can on can.idCalle = a.idCalle
				
				inner join estadocivil ec on ec.idEstadoCivil = c.idEstadoCivil
				where idCliente = {$idCli}";
				$respDato = $cadena->query($sqlDato);
				$rowDato=$respDato->fetch_assoc();
				?>
				<hr>
				<h4>Cliente CL-<?= $idCli ?></h4>

				<div class="container-fluid row">
				<div class="col-sm-3">
					<p><strong>D.N.I.:</strong> <span class="mayuscula"><?= $rowDato['cliDni']; ?></span></p>
					<p><strong>Sexo:</strong> <span class="mayuscula"><?php if($rowDato['cliSexo']==0): echo 'Femenino'; else: echo 'Masculino'; endif; ?></span></p>
					<p><strong>Celular Personal:</strong> <span><?= $rowDato['cliCelularPersonal']; ?></span></p>
				</div>
				<div class="col-sm-3">
					<p><strong>Apellidos:</strong> <span class="mayuscula"><?= $rowDato['cliApellidoPaterno'].' '.$rowDato['cliApellidoMaterno']; ?></span></p>
					<p><strong>N° Hijos:</strong> <span class="mayuscula"><?= $rowDato['cliNumHijos']; ?></span></p>
					<p><strong>Celular Referencial:</strong> <span><?= $rowDato['cliCelularReferencia']; ?></span></p>
				</div>
				<div class="col-sm-3">
				<p><strong>Nombres:</strong> <span class="mayuscula"><?= $rowDato['cliNombres']; ?></span></p>
				<p><strong>Cónyugue:</strong> <span id="pConyug"></span> </p>
				</div>
				</div> <!-- fin de row -->
				<div class="container-fluid row">
					<div class="col-sm-12">
						<p><strong>Dirección de Hogar:</strong> <span class="mayuscula"><?= $rowDato['calDescripcion']." ".$rowDato['addrDireccion']. ' #'.$rowDato['addrNumero']." - ".$rowDato['distrito']." - ".$rowDato['provincia']." - ".$rowDato['departamento'] ; ?></span></p>
						<p>Referencia: <em class="mayuscula"><?= $rowDato['addrReferencia']; ?></em></p>
					</div>
				<?php if($rowDato['cliDireccionesIgual']==0): ?>
					<div class="col-sm-12">
						<p><strong>Dirección del Negocio:</strong> <span class="mayuscula"><?= $rowDato['ncalDescripcion']." ".$rowDato['naddrDireccion']. ' #'.$rowDato['naddrNumero']." - ".$rowDato['ndistrito']." - ".$rowDato['nprovincia']." - ".$rowDato['ndepartamento'] ; ?></span></p>
						<p>Referencia: <em class="mayuscula"><?= $rowDato['naddrReferencia']; ?></em></p>
					</div>
				<?php endif; ?>
				</div>
				
				<a class="btn btn-success btn-outline btn-lg btn-sinBorde" href="creditos.php?record=<?= $_GET['idCliente'];?>">Ver record del cliente</a>

				</div>

				<?php } ?>
			</div>
			
			<!-- Fin de contenido principal -->
			</div>
		</div>
</div>
<!-- /#page-content-wrapper -->
</div><!-- /#wrapper -->

<!-- Modal para mostrar buscar esposa -->
<div class="modal fade" id="mostrarAsignarEsposa" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
<div class="modal-dialog modal-sm" role="document">
	<div class="modal-content">
		<div class="modal-header-danger">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title" id="myModalLabel"><i class="icofont icofont-help-robot"></i> Buscar esposa</h4>
		</div>
		<div class="modal-body ">
			<div class="row"><div class="col-xs-12">
				<p>Ingrese el DNI, y luego presione <kbd>Enter</kbd></p>
				<input type="text" id="txtDniConyugue" class="form-control input-lg text-center inputGrande" placeholder="DNI">
			</div></div>
			<label for="">Resultado:</label>
			<h4 class="text-center"><strong class="mayuscula" id="strNombreConyugue"></strong></h4>
			

		</div>
		<div class="modal-footer">
			<button class="btn btn-default btn-outline hidden" id="btnGuardarConyugue"><i class="icofont-love"></i> Agregar conyugue</button>
		</div>
	</div>
	</div>
</div>

<? if( isset($_GET['idCliente']) && in_array($_COOKIE['ckPower'], $soloDios)){?>
<!-- Modal para Crear un edit cliente  -->
<div class="modal fade" id="modalEditCliente" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
<div class="modal-dialog modal-lg" role="document">
	<div class="modal-content">
		<div class="modal-header-infocat">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title" id="myModalLabel"><i class="icofont icofont-animal-cat-alt-4"></i> Editar datos de cliente</h4>
		</div>
		<div class="modal-body">
			<div class="container-fluid">
			<div class="row">
				<div class="row "><div class="col-xs-6">
					<label for=""><span class="obligatorio">*</span> D.N.I.</label> <input type="text" id='txtDniClienteUpd' maxlength="8" class='form-control soloNumeros' value="<?= $rowDato['cliDni']; ?>">
				</div>
				<div class="col-xs-6"><br><label for="" class="hidden red-text text-darken-1" id="lblAlertDuplicadoUpd"></label></div>
				</div>
				<div class="row">
					<div class="col-xs-4"><span class="obligatorio">*</span> <label for="">Apellido paterno</label><input type="text" id="txtPaternoClienteUpd" class="form-control mayuscula" autocomplete='nope'></div>
					<div class="col-xs-4"><span class="obligatorio">*</span> <label for="">Apellido materno</label><input type="text" id="txtMaternoClienteUpd" class="form-control mayuscula" autocomplete='nope'></div>
					<div class="col-xs-4"><span class="obligatorio">*</span> <label for="">Nombres</label> <input type="text" id="txtNombresClienteUpd" class='form-control mayuscula' autocomplete='nope'></div>
				</div>
				<div class="row">
					<div class="col-xs-4"><label for=""><span class="obligatorio">*</span> Sexo</label>
						<select class="selectpicker" id="sltSexoUpd" title="Seleccione un sexo" data-width="100%" data-live-search="true" data-size="15">
							<option value="0">Femenino</option>
							<option value="1">Masculino</option>
						</select>
					</div>
			
					<div class="col-xs-4">
						<label for=""><span class="obligatorio">*</span> Estado civil</label>
						<select class="selectpicker" title="Estado civil" id="sltEstadoCivilUpd" data-width="100%" data-live-search="true" data-size="15">
							<?php include 'php/OPTEstadoCivil.php'; ?>
						</select>
					</div>
					<div class="col-xs-4">
						<label for="">N° de hijos dependientes</label>
						<input type="number" class="form-control" value="0" id="txtNumHijosUpd" min=0>
					</div>
				</div>
				<div class="row container-fluid hidden" id="divSoloCasadoUpd">
					<label for="">Anexar cónyugue</label>
					<select class="selectpicker" title="Parejas solteras" id="sltBuscarParejaUpd" data-width="100%" data-live-search="true" data-size="15">
						<option value="0">Busque al cliente</option>
					</select>
					
				</div>
				<div class="row container-fluid" id="divDireccionCasaUpd">
					<label for=""><span class="obligatorio">*</span> Dirección domiciliar</label>
					<div class="row container-fluid">
						<div class="col-xs-3" id="divCallesUpd"><select id="slpCallesUpd" class="selectpicker" data-width="100%" data-live-search="true" data-size="15" title="Calle"><?php include 'php/OPTCalles.php'; ?></select></div>
						<div class="col-xs-12 col-sm-7"><input type="text" class="form-control mayuscula" id="txtDireccionCasaUpd"  placeholder='Dirección de hogar' autocomplete='nope'></div>
						<div class="col-xs-2"><input type="text" class="form-control mayuscula" id="txtNumeroCasaUpd" placeholder='#' autocomplete='nope'></div>
						<div class="col-xs-4" id="divDireccionExtraUpd"><select class="selectpicker" title="Zona" id="sltDireccionExtraUpd" data-width="100%" data-live-search="true" data-size="15"><?php include 'php/OPTZona.php'; ?></select></div> 
						<div class="col-xs-8"><input type="text" id='txtReferenciaCasaUpd' class='form-control mayuscula' placeholder='Referencia de la casa' autocomplete='nope'></div>
						<div class="col-xs-4" id="divDepartamentosUpd"><select id="slpDepartamentosUpd" class="selectpicker" data-width="100%" data-live-search="true"  data-size="15" title="Departamento"><?php include 'php/OPTDepartamento.php'; ?></select></div>
						<div class="col-xs-4" id="idProvinciasUpd"><select id="slpProvinciasUpd" class="selectpicker" data-width="100%" data-live-search="true" title="Provincia"></select></div>
						<div class="col-xs-4" id="idDistritosUpd"><select id="slpDistritosUpd" class="selectpicker" data-width="100%" data-live-search="true" title="Distrito"></select></div>
					</div>
					<div class="checkbox checkbox-infocat checkbox-circle">
						<input type="checkbox" class="styled" checked id="chkDireccionUpd">
						<label for="chkDireccionUpd">Dirección de hogar y de negocio son iguales</label>
					</div>
				</div>
				<div class="row container-fluid hidden" id="divDireccionNegocioUpd">
				<label style="display: table;">Dirección de negocio</label>
					<div class="col-xs-3" id="divCallesNegUpd"><select id="slpCallesNegUpd" class="selectpicker" data-width="100%" data-live-search="true"  data-size="15" title="Calle"><?php include 'php/OPTCalles.php'; ?></select></div>
				    
				    <div class="col-xs-12 col-sm-7"><input type="text" class="form-control mayuscula" id="txtDireccionNegocioUpd" placeholder='Dirección de negocio' autocomplete='nope'></div>
						<div class="col-xs-2"><input type="text" class="form-control mayuscula" id="txtNumeroNegocUpd" placeholder='#' autocomplete='nope'></div>
						<div class="col-xs-4"  id="divDireccionExtraNegUpd"><select class="selectpicker" title="Zona" id="sltDireccionExtraNegocUpd" data-width="100%" data-live-search="true" data-size="15"><?php include 'php/OPTZona.php'; ?></select></div>
						<div class="col-xs-8"><input type="text" id='txtReferenciaNegocUpd' class='form-control mayuscula' placeholder='Referencia del negocio' autocomplete='nope'></div>
						<div class="col-xs-4" id="divDepartamentosNegocUpd"><select id="slpDepartamentosNegocUpd" class="selectpicker" data-width="100%" data-live-search="true"  data-size="15" title="Departamento"><?php include 'php/OPTDepartamento.php'; ?></select></div>
						<div class="col-xs-4" id="idProvinciasNegocUpd"><select id="slpProvinciasNegocUpd" class="selectpicker" data-width="100%" data-live-search="true"  title="Provincia"></select></div>
						<div class="col-xs-4" id="idDistritosNegocUpd"><select id="slpDistritosNegocUpd" class="selectpicker" data-width="100%" data-live-search="true" title="Distrito"></select></div>
				</div>
				<div class="row">
					<div class="col-xs-6"><label for=""><span class="obligatorio">*</span> Celular personal</label> <input type="text" id="txtCelPersonalUpd" class="form-control" autocomplete='nope'></div>
					<div class="col-xs-6"><label for=""><span class="obligatorio">*</span> Celular referencial</label> <input type="text" id="txtCelReferenciaUpd" class="form-control" autocomplete='nope'></div>
				</div>
			
		</div>
			
		<div class="modal-footer">
			<div class="divError text-left animated fadeIn hidden" style="margin-bottom: 20px;"><i class="icofont-cat-alt-3"></i> Lo sentimos, <span class="spanError"></span></div>
			<button class="btn btn-infocat btn-outline" id="btnGuardarClienteUpd" ><i class="icofont icofont-refresh"></i> Actualizar</button>

		</div>
	</div>
	</div>
</div>
</div>
</div>
<? } ?>


<?php include 'footer.php'; ?>
<?php include 'php/modals.php'; ?>
<?php include 'php/existeCookie.php'; ?>

<?php if ( isset($_COOKIE['ckidUsuario']) ){?>
<script>
datosUsuario();

$(document).ready(function(){
	$('.selectpicker').selectpicker();
	<? if(isset($_GET['idCliente'])):?>
	agregarClienteCanasta('<?= $idCli; ?>', 1);
	<? endif;?>
	$('#slpDepartamentos').change(function() {
		var depa = $('.optDepartamento:contains("'+$('#slpDepartamentos').val()+'")').attr('data-tokens');  //$('#divDepartamentos').find('.selected a').attr('data-tokens');
		$.ajax({url: 'php/OPTProvincia.php', type: 'POST', data: { depa: depa }}).done(function(resp) {
			$('#slpProvincias').html(resp).selectpicker('refresh');
		});
	});
	$('#slpProvincias').change(function() {
		var distri = $('.optProvincia:contains("'+$('#slpProvincias').val()+'")').attr('data-tokens');  //$('#divDepartamentos').find('.selected a').attr('data-tokens');
		
		$.ajax({url: 'php/OPTDistrito.php', type: 'POST', data: { distri: distri }}).done(function(resp) {
			$('#slpDistritos').html(resp).selectpicker('refresh');
		});
	});
	$('#slpDepartamentosUpd').change(function() { console.log( 'depa' );
		var depa = $('.optDepartamento:contains("'+$('#slpDepartamentosUpd').val()+'")').attr('data-tokens');  //$('#divDepartamentos').find('.selected a').attr('data-tokens');
		$.ajax({url: 'php/OPTProvincia.php', type: 'POST', data: { depa: depa }}).done(function(resp) {
			$('#slpProvinciasUpd').html(resp).selectpicker('refresh');
			$('#slpDistritosUpd').html('').selectpicker('refresh');
		});
	});
	$('#slpProvinciasUpd').change(function() {
		var distri = $('.optProvincia:contains("'+$('#slpProvinciasUpd').val()+'")').attr('data-tokens');  //$('#divDepartamentos').find('.selected a').attr('data-tokens');
		
		$.ajax({url: 'php/OPTDistrito.php', type: 'POST', data: { distri: distri }}).done(function(resp) {
			$('#slpDistritosUpd').html(resp).selectpicker('refresh');
		});
	});
	$('#slpDepartamentosNegoc').change(function() {
		var depa = $('.optDepartamento:contains("'+$('#slpDepartamentosNegoc').val()+'")').attr('data-tokens');  //$('#divDepartamentos').find('.selected a').attr('data-tokens');
		$.ajax({url: 'php/OPTProvincia.php', type: 'POST', data: { depa: depa }}).done(function(resp) {
			$('#slpProvinciasNegoc').html(resp).selectpicker('refresh');
		});
	});
	$('#slpProvinciasNegoc').change(function() {
		var distri = $('.optProvincia:contains("'+$('#slpProvinciasNegoc').val()+'")').attr('data-tokens');  //$('#divDepartamentos').find('.selected a').attr('data-tokens');
		$.ajax({url: 'php/OPTDistrito.php', type: 'POST', data: { distri: distri }}).done(function(resp) {
			$('#slpDistritosNegoc').html(resp).selectpicker('refresh');
		});
	});
});//fin de document ready
$('#btnAddClientes').click(function() {
	$('#txtDniCliente').val('');
	$('#modalNewCliente').modal('show');
});
<? if(isset($_GET['idCliente'])){ ?>
$('#btnEditClientes').click(function() {
	$.ajax({url: 'php/solicitarTodoCliente.php', type: 'POST', data: { idCli: '<?= $idCli; ?>'}}).done(function(resp) { console.log(JSON.parse(resp)[0]);
		var jCliente = JSON.parse(resp)[0];
		$('#txtPaternoClienteUpd').val(jCliente.cliApellidoPaterno);
		$('#txtMaternoClienteUpd').val(jCliente.cliApellidoMaterno);
		$('#txtNombresClienteUpd').val(jCliente.cliNombres);
		$('#sltSexoUpd').selectpicker('val',jCliente.cliSexo);
		$('#sltEstadoCivilUpd').selectpicker('val',jCliente.idEstadoCivil);

		//Datos de casa:
		$('#divDireccionCasaUpd').attr('data-id', jCliente.cliDireccionCasa);
		$('#slpCallesUpd').selectpicker('val', $("#slpCallesUpd option:contains('" + jCliente.calDescripcion + "')").attr('value'));
		$('#txtDireccionCasaUpd').val(jCliente.addrDireccion);
		$('#txtNumeroCasaUpd').val(jCliente.addrNumero);
		$('#sltDireccionExtraUpd').selectpicker('val',jCliente.idZona);
		$('#txtReferenciaCasaUpd').val(jCliente.addrReferencia);
		$('#slpDepartamentosUpd').val(jCliente.departamento).selectpicker('refresh');
		$.ajax({url: 'php/OPTProvincia.php', type: 'POST', data: { depa: jCliente.idDepartamento }}).done(function(resp) {
			$('#slpProvinciasUpd').html(resp).selectpicker('refresh');
			$('#slpProvinciasUpd').val(jCliente.provincia).selectpicker('refresh');
		});
		$.ajax({url: 'php/OPTDistrito.php', type: 'POST', data: { distri: jCliente.idProvincia }}).done(function(resp) {
			$('#slpDistritosUpd').html(resp).selectpicker('refresh');
			$('#slpDistritosUpd').val(jCliente.distrito).selectpicker('refresh');
		});

		$('#chkDireccionUpd').prop('checked', jCliente.cliDireccionesIgual)

		//Datos de Negocio:
		$('#divDireccionNegocioUpd').attr('data-id', jCliente.cliDireccionNegocio);
		$('#slpCallesNegUpd').selectpicker('val', $("#slpCallesNegUpd option:contains('" + jCliente.ncalDescripcion + "')").attr('value'));
		$('#txtDireccionNegocioUpd').val(jCliente.naddrDireccion);
		$('#txtNumeroNegocUpd').val(jCliente.naddrNumero);
		$('#sltDireccionExtraNegocUpd').selectpicker('val',jCliente.nidZona);
		$('#txtReferenciaNegocUpd').val(jCliente.naddrReferencia);
		$('#slpDepartamentosNegocUpd').val(jCliente.ndepartamento).selectpicker('refresh');
		$.ajax({url: 'php/OPTProvincia.php', type: 'POST', data: { depa: jCliente.nidDepartamento }}).done(function(resp) {
			$('#slpProvinciasNegocUpd').html(resp).selectpicker('refresh');
			$('#slpProvinciasNegocUpd').val(jCliente.nprovincia).selectpicker('refresh');
		});
		$.ajax({url: 'php/OPTDistrito.php', type: 'POST', data: { distri: jCliente.nidProvincia }}).done(function(resp) {
			$('#slpDistritosNegocUpd').html(resp).selectpicker('refresh');
			$('#slpDistritosNegocUpd').val(jCliente.ndistrito).selectpicker('refresh');
		});
		$('#txtCelPersonalUpd').val(jCliente.cliCelularPersonal);
		$('#txtCelReferenciaUpd').val(jCliente.cliCelularReferencia);
		
	});
	$('#modalEditCliente').modal('show');
});
<? } ?>
$('#chkDireccion').change(function() {
	if( $('#chkDireccion').is(':checked') ){
		$(this).parent().find('label').text('Dirección de hogar y de negocio son iguales');
		$('#divDireccionNegocio').addClass('hidden');
		$('#txtCelPersonal').focus();
	}else{
		$(this).parent().find('label').text('Dirección de hogar y de negocio son diferentes');
		$('#divDireccionNegocio').removeClass('hidden');
		$('#txtDireccionNegocio').focus();
	}
});
$('#chkDireccionUpd').change(function() {
	if( $('#chkDireccionUpd').is(':checked') ){
		$(this).parent().find('label').text('Dirección de hogar y de negocio son iguales');
		$('#divDireccionNegocioUpd').addClass('hidden');
		$('#txtCelPersonalUpd').focus();
	}else{
		$(this).parent().find('label').text('Dirección de hogar y de negocio son diferentes');
		$('#divDireccionNegocioUpd').removeClass('hidden');
		$('#txtDireccionNegocioUpd').focus();
	}
});
$('#txtDniCliente').focusout(function () {
	if( $('#txtDniCliente').val().length<8 ){
		$('#lblAlertDuplicado').html('<i class="icofont-exclamation-tringle"></i> El DNI, debe tener 8 dígitos.').removeClass('hidden');
		bloquearModalCliente(true);
	}else{
		$.post('php/verificarClienteRegistrado.php', {texto: $('#txtDniCliente').val()}, function (resp) {
			if(resp!='0'){
				$('#lblAlertDuplicado').html('<i class="icofont-exclamation-tringle"></i> Cliente ya registrado: ' + resp).removeClass('hidden');
				bloquearModalCliente(true);
			}else{
				$('#lblAlertDuplicado').addClass('hidden');
				bloquearModalCliente(false);
			}
		});
	}
});
$('#btnGuardarClienteNew').click(function() {
	var idParej='';
		if( $('#sltEstadoCivil').val()== 2 ){ idParej = $('#sltBuscarPareja').val(); }

	if( $('#txtDniCliente').val()=='' || $('#txtDniCliente').val().length<8 ){
		$('#modalNewCliente .divError .spanError').text('Falta ingresar un D.N.I. válido').parent().removeClass('hidden');
	}else if( $('#txtPaternoCliente').val()=='' || $('#txtMaternoCliente').val()=='' || $('#txtNombresCliente').val()==''  ){
		$('#modalNewCliente .divError .spanError').text('Falta ingresar nombres válidos').parent().removeClass('hidden');
	}else if( !$('#sltDireccionExtra').find(':selected').data('tokens') ){
		$('#modalNewCliente .divError .spanError').text('Falta seleccionar una zona').parent().removeClass('hidden');
	}else if( !$('#slpDepartamentos').find(':selected').data('tokens') ){
		$('#modalNewCliente .divError .spanError').text('Falta seleccionar un departamento').parent().removeClass('hidden');
	}else if( !$('#slpProvincias').find(':selected').data('tokens') ){
		$('#modalNewCliente .divError .spanError').text('Falta seleccionar una provicincia').parent().removeClass('hidden');
	}else if( !$('#slpDistritos').find(':selected').data('tokens') ){
		$('#modalNewCliente .divError .spanError').text('Falta seleccionar un distrito').parent().removeClass('hidden');
	}else if( !$('#sltEstadoCivil').find(':selected').data('tokens') ){
		$('#modalNewCliente .divError .spanError').text('Falta seleccionar un estado civil').parent().removeClass('hidden');
	} else if( $('#sltEstadoCivil').val()== 2 && idParej=='' ){
		$('#modalNewCliente .divError .spanError').text('Falta seleccionar una pareja casada').parent().removeClass('hidden');
	}else{
		var casa =0;
	
		if( $('#chkDireccion').is(':checked') ){//true
			casa=0;}else{ casa=1;}
		
		
			
		$.ajax({url: 'php/insertarCliente.php', type: 'POST', data: {
			direccion: $('#txtDireccionCasa').val(),
			zona: $('#sltDireccionExtra').val(),
			referencia: $('#txtReferenciaCasa').val(),
			numero: $('#txtNumeroCasa').val(),
			departam: $('#divDireccionCasa .optDepartamento:contains("'+$('#slpDepartamentos').val()+'")').attr('data-tokens'),
			provinc: $('#divDireccionCasa .optProvincia:contains("'+$('#slpProvincias').val()+'")').attr('data-tokens'),
			distrit: $('#divDireccionCasa .optDistrito:contains("'+$('#slpDistritos').val()+'")').attr('data-tokens'),
			calle: $('#slpCalles').val(),
	
			direccionNeg: $('#txtDireccionNegocio').val(),
			zonaNeg: $('#sltDireccionExtraNegoc').val(),
			referenciaNeg: $('#txtReferenciaNegoc').val(),
			numeroNeg: $('#txtNumeroNegoc').val(),
			departamNeg: $('#divDireccionNegocio .optDepartamento:contains("'+$('#slpDepartamentosNegoc').val()+'")').attr('data-tokens'),
			provincNeg: $('#divDireccionNegocio .optProvincia:contains("'+$('#slpProvinciasNegoc').val()+'")').attr('data-tokens'),
			distritNeg: $('#divDireccionNegocio .optDistrito:contains("'+$('#slpDistritosNegoc').val()+'")').attr('data-tokens'),
			calleNeg: $('#slpCallesNeg').val(),
	
			dni: $('#txtDniCliente').val(),
			nombres: $('#txtNombresCliente').val(),
			paterno: $('#txtPaternoCliente').val(),
			materno: $('#txtMaternoCliente').val(),
			hijos: $('#txtNumHijos').val(),
			sexo: $('#sltSexo').val(),
			celularPers: $('#txtCelPersonal').val(),
			celularRef: $('#txtCelReferencia').val(),
			civil: $('#sltEstadoCivil').val(),
			pareja: parseFloat(idParej),
	
			casa: casa}}).done(function(resp) { console.log(resp)
				if( parseInt(resp)>0 ){
					//location.reload();
					window.location.href = 'clientes.php?buscar='+resp;
				}
		});

	}
	
});
$('#btnGuardarClienteUpd').click(function() {
	if( $('#txtDniClienteUpd').val().length <8 ){
		$('#modalEditCliente .divError').removeClass('hidden').find('.spanError').text('El DNI no es correcto');
	}else if( $('#txtPaternoClienteUpd').val()=='' || $('#txtMaternoClienteUpd').val()=='' || $('#txtNombresClienteUpd').val()=='' ){
		$('#modalEditCliente .divError').removeClass('hidden').find('.spanError').text('Verifique los campos de nombres');
	}else if( $('#txtDireccionCasaUpd').val()=='' ||  $('#txtNumeroCasaUpd').val()=='' ){
		$('#modalEditCliente .divError').removeClass('hidden').find('.spanError').text('Verifique el campo de dirección');
	}else if($('#chkDireccionUpd').is('checked')){ //Verificar todo el negocio
		if( $('#txtDireccionNegocioUpd').val()=='' ||  $('#txtNumeroNegocUpd').val()=='' ){
			$('#modalEditCliente .divError').removeClass('hidden').find('.spanError').text('Verifique el campo de dirección del negocio');
		}
	}
	else if( $('#txtCelPersonalUpd').val()=='' || $('#txtCelReferenciaUpd').val()=='' ){
		$('#modalEditCliente .divError').removeClass('hidden').find('.spanError').text('Verifique los campos de teléfono');
	}else{
		var hijos =0;
		if( $('#txtNumHijosUpd').val()=='' ){hijos = $('#txtNumHijosUpd').val();}
		var jClienteupd = {
			idCliente: '<?php if(isset($_GET['idCliente'])){ echo $idCli;}else{ echo '""';}?>',
			dni: $('#txtDniClienteUpd').val(),
			apellidoPaterno: $('#txtPaternoClienteUpd').val(),
			apellidoMaterno: $('#txtMaternoClienteUpd').val(),
			nombres: $('#txtNombresClienteUpd').val(),
			sexo: $('#sltSexoUpd').val(),
			estadocivil: $('#sltEstadoCivilUpd').val(),
			hijos: hijos,
			casaId: $('#divDireccionCasaUpd').attr('data-id'),
			calleCasa: $("#slpCallesUpd option:contains('"+$('#divCallesUpd .dropdown-toggle').attr('title')+"')").val(),
			direccionCasa: $('#txtDireccionCasaUpd').val(),
			numeroCasa: $('#txtNumeroCasaUpd').val(),
			calleCasa: $("#sltDireccionExtraUpd option:contains('"+$('#divDireccionExtraUpd .dropdown-toggle').attr('title')+"')").val(),
			referenciaCasa: $('#txtReferenciaCasaUpd').val(),
			departamentoCasa: $("#slpDepartamentosUpd option:contains('"+$('#divDepartamentosUpd .dropdown-toggle').attr('title')+"')").attr('data-tokens'),
			provinciaCasa: $("#slpProvinciasUpd option:contains('"+$('#idProvinciasUpd .dropdown-toggle').attr('title')+"')").attr('data-tokens'),
			distritoCasa: $("#slpDistritosUpd option:contains('"+$('#idDistritosUpd .dropdown-toggle').attr('title')+"')").attr('data-tokens'),
			esCasa: $('#chkDireccionUpd').prop('checked'),
			negocioId: $('#divDireccionNegocioUpd').attr('data-id'),
			calleNeg: $("#slpCallesNegUpd option:contains('"+$('#divCallesNegUpd .dropdown-toggle').attr('title')+"')").val(),
			direccionNeg: $('#txtDireccionNegocioUpd').val(),
			numeroNeg: $('#txtNumeroNegocUpd').val(),
			calleNeg: $("#sltDireccionExtraNegocUpd option:contains('"+$('#divDireccionExtraNegUpd .dropdown-toggle').attr('title')+"')").val(),
			referenciaNeg: $('#txtReferenciaNegocUpd').val(),
			departamentoNeg: $("#slpDepartamentosNegocUpd option:contains('"+$('#divDepartamentosNegocUpd .dropdown-toggle').attr('title')+"')").attr('data-tokens'),
			provinciaNeg: $("#slpProvinciasNegocUpd option:contains('"+$('#idProvinciasNegocUpd .dropdown-toggle').attr('title')+"')").attr('data-tokens'),
			distritoNeg: $("#slpDistritosNegocUpd option:contains('"+$('#idDistritosNegocUpd .dropdown-toggle').attr('title')+"')").attr('data-tokens'),
			celPersonal: $('#txtCelPersonalUpd').val(),
			celRefencia: $('#txtCelReferenciaUpd').val()

		}
		$.ajax({url: 'php/updateCliente.php', type: 'POST', data: { jcCliente: jClienteupd }}).done(function(resp) {
			//console.log(resp)
			location.reload();
		});
		//console.log(jClienteupd);
	}
});
// $('.soloNumeros').on('input', function () {
// 	this.value = this.value.replace(/[^0-9]/g,'');
// });
$('.soloNumeros').keypress(function (e) {
	if( !(e.which >= 48 /* 0 */ && e.which <= 90 /* 9 */)  ) { e.preventDefault(); }
});
$('#txtClientesZon').keypress(function (e) { if(e.keyCode == 13){ $('#btnFiltrarClientes').click(); } });
$('#btnFiltrarClientes').click(function() {
	if( $('#txtClientesZon').val()!=''){
		window.location.href = 'clientes.php?buscar='+encodeURIComponent($('#txtClientesZon').val());
	}else{
		window.location.href = 'clientes.php';
	}
});
$('#sltSexo').on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
	cargarDataPosiblesParejas() 
});
$('#sltEstadoCivil').on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
	cargarDataPosiblesParejas() 
});
function cargarDataPosiblesParejas() {
	if( $.inArray($('#sltEstadoCivil').val(), ["2", "3"])!=-1 && $('#sltEstadoCivil').val()!='' ){
		$('#divSoloCasado').removeClass('hidden');
		var sexOpu = '';
		if( $('#sltSexo').val()==0 ){
			sexOpu = 1;
		}else{
			sexOpu = 0;
		}
		$.ajax({url: 'php/OPTListarClienteOpuesto.php', type: 'POST', data: { sexoContra: sexOpu}}).done(function(resp) { //console.log( resp );
			$('#sltBuscarPareja').html(resp).selectpicker('refresh');
		});
	}
	else{
		$('#sltBuscarPareja').children().remove().selectpicker('refresh');
		$('#divSoloCasado').addClass('hidden');
	}
}
$('.btnLlamarEsposo').click(function() {
	var id= $(this).attr('data-id')
	var elId= $(this).attr('data-sex')
	$('#strNombreConyugue').attr('data-llama', id );
	$('#strNombreConyugue').attr('data-idSexLlama', elId );
	$('#mostrarAsignarEsposa').modal('show');
});
$('#mostrarAsignarEsposa').on('shown.bs.modal', function () { $('#txtDniConyugue').focus(); });
$('#txtDniConyugue').keypress(function(e){
	var sex=0;
	if($('#strNombreConyugue').attr('data-idSexLlama')==1){
		sex=0;
	}else{
		sex=1
	}
	if(e.keyCode == 13){ 
		$.ajax({url: 'php/encontrarConyugue.php', type: 'POST', data: { dni: $('#txtDniConyugue').val(), sex: sex}}).done(function(resp) {
			var dato=JSON.parse(resp);
			
			if(dato.length==0){
				$('#btnGuardarConyugue').addClass('hidden');
				$('#strNombreConyugue').text('Aún no hay clientes de género opuesto con éste DNI');
			}else if(dato.length>1){
				$('#btnGuardarConyugue').addClass('hidden');
				$('#strNombreConyugue').text('Éste DNI está duplicado, comuníquelo con soporte');
			}else{
				$('#btnGuardarConyugue').removeClass('hidden');
				$('#strNombreConyugue').html('<i class="icofont-gavel"></i> ' +dato[0].cliApellidoPaterno.toLowerCase() + ' ' + dato[0].cliApellidoMaterno.toLowerCase() + ', ' +dato[0].cliNombres.toLowerCase());
				$('#strNombreConyugue').attr('data-id', dato[0].idCliente );
				$('#strNombreConyugue').attr('data-sex', dato[0].cliSexo );
			}
		});
	 }
});
$('#btnGuardarConyugue').click(function() {
	var idVaron =0, idDama =0;

	if( $('#strNombreConyugue').attr('data-sex')==1 ){
		idVaron=$('#strNombreConyugue').attr('data-id');
		idDama= $('#strNombreConyugue').attr('data-llama');
	}else{
		idDama=$('#strNombreConyugue').attr('data-id');
		idVaron= $('#strNombreConyugue').attr('data-llama');
	}
	$.ajax({url: 'php/insertarMatrimonio.php', type: 'POST', data: {idDama: idDama,
idVaron: idVaron }}).done(function(resp) {
		console.log(resp)
		location.reload();
	});
});
function agregarClienteCanasta(idCl, cargo) { //console.log( idCl );

if(cargo==1){
	$.ajax({url: 'php/listarMatrimonio.php', type: 'POST', data: { conyugue: idCl }}).done(function(resp) { //console.log(resp)
		var datoMatri= JSON.parse(resp);
		if(datoMatri.length==1){

			if(datoMatri[0].idEsposo==parseFloat(idCl)){
			//	console.info('esposo') //listar a la esposa
				agregarClienteCanasta(datoMatri[0].idEsposa, 2);
			}else{
				//console.info('esposa') //listar al esposo
				agregarClienteCanasta(datoMatri[0].idEsposo, 2);
			}
		}else{
			$('#pConyug').text('-');
		}
	});
}else{
	$.ajax({url: 'php/ubicarDatosCliente.php', type: 'POST', data: { idCli: idCl }}).done(function(resp2) { 
		var dato = JSON.parse(resp2); //console.log( dato );
		$.post('php/58encode.php', {texto: dato[0].idCliente }, function(resp) {
			$('#pConyug').html(`<span class="mayuscula"><a href="clientes.php?idCliente=${resp}">${dato[0].cliApellidoPaterno} ${dato[0].cliApellidoMaterno} ${dato[0].cliNombres}</a> </span>`);
		});
		
	});
}
}//fin de function
$('#modalNewCliente').on('shown.bs.modal', function () { 
	bloquearModalCliente(true);
});
function bloquearModalCliente(estado) {
	$('#txtPaternoCliente').attr('disabled', estado);
	$('#txtMaternoCliente').attr('disabled', estado);
	$('#txtNombresCliente').attr('disabled', estado);
	$('#txtDireccionCasa').attr('disabled', estado);
	$('#txtNumeroCasa').attr('disabled', estado);
	$('#txtCelPersonal').attr('disabled', estado);
	$('#txtCelReferencia').attr('disabled', estado);

}
$('#sltBuscarPareja').on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
  $.ajax({url: 'php/listarDireccionPareja.php', type: 'POST', data: { idCli: $('#sltBuscarPareja').val() }}).done(function(resp) {
		var dato = JSON.parse(resp)[0];
		console.log(dato)
		$('#slpCalles').val(dato.idCalle).selectpicker('refresh');
		$('#sltDireccionExtra').val(dato.idZona).selectpicker('refresh');

		$('#slpDepartamentos').selectpicker('val', $('#slpDepartamentos .optDepartamento[data-tokens= "'+dato.idDepartamento+'" ]').text());
		$('#slpProvincias').selectpicker('val', $('#slpProvincias .optProvincia[data-tokens= "'+dato.idProvincia+'" ]').text());
		$('#slpDistritos').selectpicker('val', $('#slpDistritos .optDistrito[data-tokens= "'+dato.idDistrito+'" ]').text());

		$('#txtDireccionCasa').val(dato.addrDireccion);
		$('#txtNumeroCasa').val(dato.addrNumero);
		$('#txtReferenciaCasa').val(dato.addrReferencia);
		
		if( dato.cliDireccionesIgual == 1 ){
			$('#chkDireccion').prop('checked', true)
		}else{
			$('#chkDireccion').prop('checked', false)
		}
		$('#slpCallesNeg').val(dato.idCalle).selectpicker('refresh');
		$('#sltDireccionExtraNegoc').val(dato.idZona).selectpicker('refresh');

		$('#slpDepartamentosNegoc').selectpicker('val', $('#slpDepartamentos .optDepartamento[data-tokens= "'+dato.nidDepartamento+'" ]').text());
		$('#slpProvinciasNegoc').selectpicker('val', $('#slpProvincias .optProvincia[data-tokens= "'+dato.nidProvincia+'" ]').text());
		$('#slpDistritosNegoc').selectpicker('val', $('#slpDistritos .optDistrito[data-tokens= "'+dato.nidDistrito+'" ]').text());

		$('#txtDireccionNegocio').val(dato.naddrDireccion);
		$('#txtNumeroNegoc').val(dato.naddrNumero);
		$('#txtReferenciaNegoc').val(dato.naddrReferencia);

	});
});


</script>
<?php } ?>
</body>

</html>