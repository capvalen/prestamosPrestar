<?php 
header('Content-Type: text/html; charset=utf8');
date_default_timezone_set('America/Lima');
include 'php/conkarl.php';
require_once('vendor/autoload.php');
$base58 = new StephenHill\Base58();
include "php/variablesGlobales.php";
$hayCaja= require_once("php/comprobarCajaHoy.php");
$fechaHoy = new DateTime();
$estadoMora = null;

$codCredito='';
if( isset($_GET['credito']) )
	$codCredito=$base58->decode($_GET['credito']);


if($_COOKIE['ckPower']!='1' && isset($_GET['credito']) ){
	//echo 'evaluar';
	if($_COOKIE['ckPower']=='2'){ //vista asesor
		$sqlVista="SELECT * FROM `vistas` WHERE idUsuario ={$_COOKIE['ckidUsuario']} and idPrestamo = {$codCredito} and ver=1 and activo=1;";
		$resultVista = $preferido->query($sqlVista);
		if($resultVista->num_rows==0){ header('Location: sinPermiso.php'); die(); }
	}

	if($_COOKIE['ckPower']=='4'){ //vista asesor
		$sqlVista="SELECT * FROM `vistas` WHERE idUsuario ={$_COOKIE['ckidUsuario']} and idPrestamo = {$codCredito} and ver=0 and activo=1;";
		$resultVista = $preferido->query($sqlVista);
		if($resultVista->num_rows==1){ header('Location: sinPermiso.php'); die(); }
	}
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
	<?php include 'headers.php'; ?>
	<title>Créditos Prestar Huancayo - CR-<?= $codCredito;?> </title>
	<link rel="stylesheet" href="css/awesome-bootstrap-checkbox.css?version=1.0.1">
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.11.3/datatables.min.css"/>

</head>

<body>


<div id="wrapper">
	<!-- Sidebar -->
	<?php include 'menu-wrapper.php' ?>
	<!-- /#sidebar-wrapper -->
<!-- Page Content -->
<div id="page-content-wrapper">
	<div class="container-fluid ">
		<div class="row noselect">
			<div class="col-lg-12 contenedorDeslizable " id="app">
			<!-- Empieza a meter contenido principal -->
			<div class="panel panel-default hidden">
			<div class="panel-body">
				<div class="row col-sm-6 col-md-3">
					<p><strong>Filtro de Créditos:</strong></p>
					<input type="text" class="form-control" placeholder="CR-00**" id="txtSoloBuscaCreditos">
				</div>
			</div>
		</div>
	<?php
	if( isset($_GET['credito']) ):
		?>

		<h3 class="purple-text text-lighten-1" id="h3Codigo" data-id="<?= $codCredito; ?>">Crédito CR-<?= $codCredito; ?></h3>

	<?php

		$sqlCr="SELECT presFechaAutom, presMontoDesembolso, presPeriodo, tpr.tpreDescipcion,
		u.usuNombres, preInteresPers, pre.idUsuario,
		case presFechaDesembolso when '0000-00-00' then 'Desembolso pendiente' else presFechaDesembolso end as `presFechaDesembolso`,
		case presAprobado when 0 then 'Sin aprobar' when 2 then 'Rechazado' else 'Aprobado' end as `presAprobado`, 
		case when ua.usuNombres is Null then '-' else ua.usuNombres end  as `usuarioAprobador`, pre.idTipoPrestamo,
		`preMoraFija`, `preMoraFecha`, `intSimple`
		FROM `prestamo` pre
		inner join usuario u on u.idUsuario = pre.idUsuario
		left join usuario ua on ua.idUsuario = pre.idUsuarioAprobador
		inner join tipoprestamo tpr on tpr.idTipoPrestamo = pre.idTipoPrestamo
		where pre.idPrestamo='{$codCredito}'"; ?>
		<!-- <table class="table table-hover">
		<thead>
			<tr>
				<th></th>
			</tr>
		</thead>
		<tbody>
		</tbody> -->
		<?php if( $respuesta = $conection->query($sqlCr)){
			$contadorF = $respuesta->num_rows;
			$rowCr = $respuesta->fetch_assoc();
			$estadoMora =false;
				
			if($contadorF==0){ ?>
				<p>El código solicitado no está asociado a ningún crédito, revise el código o comuníquelo al área responsable. </p>
				<?php die();
			}
			$_POST['plazos'] = $rowCr['presPeriodo'];
			$_POST['periodo'] = $rowCr['presPeriodo'];
			$_POST['monto']= $rowCr['presMontoDesembolso'];
			$_POST['modo']= $rowCr['idTipoPrestamo'];
			$intBase = $rowCr['presMontoDesembolso']*$rowCr['preInteresPers']/100;
			?>
		<div class="container-fluid" id="contenedorCreditosFluid">
			<p><strong>Datos de crédito</strong></p>
			<div class="row">
				<div class="col-sm-2"><label for="">Verificación</label><p><?= $rowCr['presAprobado']; ?> 
				<?php if( $rowCr['presAprobado']=='Rechazado' && in_array($_COOKIE['ckPower'], $soloAdmis)):?>
					<span><button class="btn btn-xs mitoolTip" id="btnReactivarPrestamo" data-toggle="tooltip" data-placement="bottom" title="Reactivar préstamo"><i class="icofont icofont-refresh"></i></button></span> </p> 
				<?php endif; ?>
				</div>
				<div class="col-sm-2"><label for="">Verificador</label><p><?= $rowCr['usuarioAprobador']; ?></p></div>
				<?php if(in_array($_COOKIE['ckPower'], $soloAdmis )){ ?>
				<div class="col-sm-2"><label for="">Nuevo Asesor:</label> <br>
					<select name="" id="sltNuevoAsesr" class="form-control input-sm" style="margin-bottom: 0px;">
						<?php include "php/OPTUsuarios.php"; ?>
					</select>
				</div>
				<div class="col-sm-2"><label for="">Mora fija: <i class="icofont icofont-info-circle mitoolTip" data-toggle="tooltip" data-placement="top" title="Ponga el valor y luego presione enter"></i></label> <br>
					<input type="text"  id="txtMoraFijaAsignar" class="form-control input-sm" min="0" step="0.5" style="margin-bottom: 0px; height: 30px!important;" value="<?php if ( $rowCr['preMoraFecha']>0 ){
						if( $rowCr['preMoraFecha'] == date('Y-m-d')){ $estadoMora=true; echo $rowCr['preMoraFija']; } else{ echo ""; $estadoMora=false; }
					}else{ echo ''; $estadoMora=false; }
					?>">
					<button class="btn btn-negro btn-outline" id="btnMoraFijaAsignar"><i class="icofont-exchange"></i></button>
				</div>
				<?php } ?>
				<?php if(in_array($_COOKIE['ckPower'], $soloCaja )){ if ( $rowCr['preMoraFecha']>0 ){ 
						if( $rowCr['preMoraFecha'] == date('Y-m-d')){ $estadoMora=true; echo "Mora fija: ". number_format($rowCr['preMoraFija'],2); } else{ echo "Mora normal"; $estadoMora=false; }
					}else{ echo ''; $estadoMora=false; }} ?>
			</div>
			<div class="row">
				<div class="col-sm-2"><label for="">Fecha préstamo</label><p><?php $fechaAut= new DateTime($rowCr['presFechaAutom']); echo $fechaAut->format('j/m/Y h:m a'); ?></p></div>
				<div class="col-sm-2"><label for="">Fecha desemboslo</label><p><?php if($rowCr['presFechaDesembolso']=='Desembolso pendiente'){echo $rowCr['presFechaDesembolso'];}else{$fechaDes= new DateTime($rowCr['presFechaDesembolso']); echo $fechaDes->format('j/m/Y h:m a');} ?></p></div>
				<div class="col-sm-2"><label for="">Desembolso</label><p>S/ <?= number_format($rowCr['presMontoDesembolso'],2); ?></p> <span class="hidden" id="spanMontoDado" data-monto=<?= $base58->encode($rowCr['presMontoDesembolso']);?>><?= $rowCr['presMontoDesembolso']; ?></span></div>
				<div class="col-sm-2"><label for="">Meses</label>
					<p class="hidden" id="spanTipoDescpago"><?= $rowCr['tpreDescipcion']; ?></p>
					<select name="" id="cmbPeriodos" class="form-control input-sm" style="margin-bottom:0px;">
						<option value="1">Diario</option>
						<option value="2">Semanal</option>
						<option value="3">Mensual</option>
						<option value="4">Quincenal</option>
					</select>
				</div>
				<div class="col-sm-2"><label for="">Interés</label><p id="pinteresGlobal" data-int="<?= $base58->encode($rowCr['preInteresPers']."%");?>"><?= $rowCr['preInteresPers']."%"; ?> <span>(Int. <?= $rowCr['intSimple']==0 ? 'Normal' : 'Francés'; ?>)</span></p></div>
				<div class="col-sm-2"><label for="">Analista</label><p><?= $rowCr['usuNombres']; ?></p></div>
			</div>

			<hr>
			
			<p><strong>Clientes asociados a éste préstamo:</strong>
			 <button onclick="$('#btnAsociarDNI').removeClass('hidden'); $('#btnSiAsociarDNI').addClass('hidden'); $('#siSocioAdd').parent().addClass('hidden'); $('#noSocioAdd').parent().addClass('hidden'); $('#modalLlamarDNISocio').modal('show')" class="btn btn-sm btn-success btn-outline"><div class="icofont icofont-plus"></div></button>
		</p>

			<div class="row">
				<ul>
				<?php $sqlInv= "SELECT i.idPrestamo, lower(concat(c.cliApellidoPaterno, ' ', c.cliApellidoMaterno, ', ', c.cliNombres)) as `datosCliente` , tpc.tipcDescripcion, i.idCliente FROM `involucrados` i
				inner join cliente c on i.idCliente = c.idCliente
				inner join tipocliente tpc on tpc.idTipoCliente = i.idTipoCliente
				where idPrestamo ='{$codCredito}'";
				$k=0;
				if( $respuestaInv=$conection->query($sqlInv) ){
					while( $rowInv=$respuestaInv->fetch_assoc() ){  ?>
						<li class="mayuscula" style="padding: 20px 0;">
							<a href="clientes.php?idCliente=<?= $base58->encode(substr('000000'.$rowInv['idCliente'], -7));?>"><span id="<? if($k==0){echo 'spanTitular';} ?>" ><?= $rowInv['datosCliente']; ?></span><?= " [".$rowInv['tipcDescripcion']."]"?></a> <?php if($k>0){ ?><span>
								<?php if(in_array($_COOKIE['ckPower'], $soloAdmis )){ ?>
								<button class="btn btn-danger btn-outline btn-xs" onclick="$.idBorrarSocio = <?=$rowInv['idCliente'];?>; $('#modalBorrarSocioClick').modal('show'); "><i class="icofont icofont-close"></i></button>
								<?php } ?>
							</span><?php }?>
						</li>
			<?php $k++; }
				}
			?>
				</ul>
			</div>

			<hr>

			<div class="container row" id="rowBotonesMaestros">
				<div class="col-xs-12 col-md-8">
				<div class="btn-group">
				<?php if($rowCr['presFechaDesembolso']!='Desembolso pendiente'): ?>
					<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style='margin-bottom: 0px;'  ><i class="icofont-print"></i>  Impresiones <span class="caret"></span>
					</button>
					<ul class="dropdown-menu">
						<li id="btnImpresionPrevia" data-pre="<?= $_GET['credito'];?>"><a href="#!"><i class="icofont-paper"></i> Cronograma</a></li>
						<li class="hidden" id="btnImpresionPreviaPDF" data-pre="<?= $_GET['credito'];?>"><a href="#!"><i class="icofont-paper"></i> Cronograma PDF</a></li>
						<li id="btnImpresionContrato" data-pre="<?= $_GET['credito'];?>"><a href="#!"><i class="icofont-paper"></i> Contrato</a></li>
						<li class="hidden" id="btnImpresionContratoPDF" data-pre="<?= $_GET['credito'];?>"><a href="#!"><i class="icofont-paper"></i> Contrato PDF</a></li>
					</ul>
				<?php endif;//de desembolso pendiente ?>
			</div>
				<?php //Botón para evaluación
					$sqlEval = $db->prepare("SELECT id FROM `evaluacion` where idPrestamo = ? and activo = 1");
					$sqlEval->execute([ intval($codCredito) ]);
					if($sqlEval->rowCount()==0) $idEvaluacion ='no';
					else{
						$rowEval = $sqlEval->fetch(PDO::FETCH_ASSOC);
						$idEvaluacion = $rowEval['id'];
					}
				?>
				<a class="btn btn-infocat btn-outline" href="evaluacion.php?idEvaluacion=<?=$idEvaluacion?>&idPrestamo=<?= intval($codCredito)?>"><i class="icofont-shield-alt"></i> Ver evaluación</a>
				<?php if(isset($_GET['credito']) && $rowCr['presAprobado']== 'Sin aprobar' && in_array($_COOKIE['ckPower'], $soloAdmis)): ?>
					<button class="btn btn-success btn-outline " id="btnShowVerificarCredito"><i class="icofont-check-circled"></i> Aprobar crédito</button>
					<button class="btn btn-danger btn-outline " id="btnDenyVerificarCredito"><i class="icofont-thumbs-down"></i> Denegar crédito</button>
				<?php else: 
					if( in_array( $_COOKIE['ckPower'], $soloAdmis) &&  $rowCr['presAprobado']<>"Rechazado"):?>
					<button class="btn btn-rojoFresa btn-outline" id="btnAnularCredito"><i class="icofont-ui-delete"></i> Anular crédito</button>
					<button class="btn btn-dark btn-outline" id="btnReprogramarFechas"><i class="icofont-ui-reply"></i> Fijar fechas</button>
				<?php endif; endif; ?>
				</div>

			<?php if(isset($_GET['credito']) && $rowCr['presAprobado']<> 'Sin aprobar' && $rowCr['presAprobado']<> "Rechazado" && in_array($_COOKIE['ckPower'], $soloCajas)): ?>
			<?php if( $hayCaja==true ):
				if($rowCr['presFechaDesembolso']=='Desembolso pendiente'): ?>
				<button class="btn btn-warning btn-outline" id="btnDesembolsar"><i class="icofont-money"></i> Desembolsar</button>
			<?php else:?>
				<button class="btn btn-infocat btn-outline" id="btnsolicitarDeuda"><i class="icofont-money"></i> Pago global</button>
				<button class="btn btn-infocat btn-outline hidden" id="btnMoraExtra"><i class="icofont-shield-alt"></i> Mora extraordinaria</button>
			<?php endif; ?>
			<?php else: ?> 
				<div class="col-xs-12 col-md-4"><br>
					<div class="alert alert-morado container-fluid" role="alert">
						<div class="col-xs-4 col-sm-2 col-md-3">
							<img src="images/ghost.png" alt="img-responsive" width="100%">
						</div>
						<div class="col-xs-8">
							<strong>Alerta</strong> <p>No se encuentra ninguna caja aperturada.</p>
							<a class="btn btn-dark btn-outline btn-xs pull-left" href="caja.php" style="color:#333"><i class="icofont icofont-rounded-double-right"></i> Ir a caja</a>
						</div>
					</div>
				</div>
			<?php endif; //if de hay caja ?>
			<?php endif; //if de soloCajas  ?>
		
			</div>
			<hr>

			<p><strong>Cuotas planificadas:</strong></p>
			<div class="table-responsive">
			<?php
				if($rowCr['intSimple']==0):
					include 'php/listaCuotaNormal.php';
				else:
					include 'php/listaCuotaFrances.php';
				endif;
			?>
			</div>

			<?php $_POST['credito']=$_GET['credito']; include 'php/listarOtrospagos.php'; ?>
			
		</div><!-- Fin de contenedorCreditosFluid -->
		<?php 
		} //Fin de if $respuesta 	?>
		<!-- </table> -->

	<?php else: //else de si existe GET['credidto]
		if(isset($_GET['record'])):
			$idCli = $base58->decode($_GET['record']);
			$_GET['idCliente'] = $_GET['record']; 
			$sql="SELECT  `cliDni`, lower(`cliNombres`) as `cliNombres`, lower(`cliApellidoPaterno`) as `cliApellidoPaterno`, lower(`cliApellidoMaterno`) as `cliApellidoMaterno`
			FROM `cliente` WHERE `idCliente`={$idCli} and `cliActivo`=1";
			$resultado=$cadena->query($sql);
			$row=$resultado->fetch_assoc();

			?>
			<h3 class="purple-text text-lighten-1">Record de créditos</h3><hr>
				<p><strong>Código de cliente:</strong> <a href="clientes.php?idCliente=<?= $_GET['record']?>">CL-<?= $idCli; ?></a></p>
				<p><strong>Nombres completos: </strong> <span class="mayuscula"><a href="clientes.php?idCliente=<?= $_GET['record']?>"><?= $row['cliApellidoPaterno'].' '.$row['cliApellidoMaterno'].", ".$row['cliNombres']; ?></a></span></p>
				<p><strong>D.N.I.: </strong> <?= $row['cliDni']; ?></p>
				<div class="container-fluid row">
					<label for="">Préstamos solicitados:</label>
					<div class="table-responsive">
						<table class="table table-hover">
							<thead>
								<tr>
									<th>Agencia</th>
									<th>N° Crédito</th>
									<th>Monto desembolsado</th>
									<th>Cuota</th>
									<th>Saldo</th>
									<th>F. Desembolso</th>
									<th>Estado</th>
									<th>Forma de pago</th>
									<?php 
									for ($i=0; $i < 15 ; $i++) { 
										echo "<th>". ($i+1)."</th>";
									}
									?>
								</tr>
							</thead>
							<tbody>
								<?php include 'php/listarHistorialPagos.php' ?>
							</tbody>
						</table>
					</div>

		
		<?php endif; //if de GET record
		if(isset( $_GET['titular'])): ?>
			<h3 class="purple-text text-lighten-1">Asignar crédito</h3><hr>
			<div class="panel panel-default">
				<div class="panel-body">
				<p><strong>Involucrar más clientes:</strong></p>
					<div class="row">
						<div class="col-xs-6 col-sm-3">
							<input type="text" id="txtAddCliente" class="form-control" placeholder="Apellidos o DNI">
						</div>
						<div class="col-xs-3">
							<button class="btn btn-primary btn-outline" id="btnBuscarClientesDni"><i class="icofont-search-1"></i> Buscar</button>
						</div>
					</div>
				</div>
			</div>

			<div class="panel panel-default">
				<div class="panel-body">
					<p><strong>Involucrados</strong></p>
					<table class="table" style="margin-bottom: 0px;">
						<thead>
							<tr>
								<th>D.N.I.</th>
								<th>Apellidos y nombres</th>
								<th>Estado civil</th>
								<th>Cargo</th>
							</tr>
						</thead>
						<tbody id="tbodySocios"></tbody>
					</table>

				</div>
			</div>

			<div class="panel panel-default">
				<div class="panel-body">
					<p><strong>Cálculos</strong></p>
					<div class="row">
						<div class="col-xs-6 col-sm-3">
							<label for="">Tipo de préstamo:</label>
							<select class="form-control selectpicker" id="sltTipoPrestamo" title="Seleccione un préstamo" data-width="100%" data-live-search="true" data-size="15">
								<?php include 'php/OPTTipoPrestamo.php'; ?>
							</select>
						</div>
						<div class="col-xs-6 col-sm-3">
							<label for="">Monto</label>
							<input type="number" class="form-control esMoneda text-center" id="txtMontoPrinc" value=0.00>
						</div>
						<div class="col-xs-6 col-sm-3">
							<label for="">Meses</label>
							<input type="number" class="form-control esDecimal text-center" id="txtPeriodo" value=0 >
						</div>
						<div class="col-xs-6 col-sm-3">
							<label for="">Interés</label>
							<input type="number" class="form-control esNumero esDecimal text-center" id="txtInteres" value=0>
						</div>
						<div class="col-xs-6 col-sm-3">
							<label for="">Fecha Desembolso</label>
							<input type="text" id="dtpFechaIniciov3" class="form-control text-center" placeholder="Fecha para controlar citas" autocomplete="off">
						</div>
						<div class="col-xs-6 col-sm-3" id="divTipoInteres">
							<label for="">Tipo de interés</label>
							<div class="form-group">
								<select id="sltTipoInteres" class="form-control" name="">
									<option value="1">Simple (Clásico)</option>
									<option value="2">Francés (Acumulado)</option>
								</select>
							</div>
						</div>
						<div class="col-xs-6 col-sm-3">
							<label for="">¿Fechas fijas?</label>
							<select class="form-control" id="sltFijas">
								<option value="no">No, autocalcular</option>
								<option value="si">Si, fijar</option>
							</select>
						</div>
						<div class="col-xs-6 col-sm-3 hidden" id="divPrimerPago">
							<label for="">Fecha primer pago</label>
							<input type="text" id="dtpFechaPrimerv3" class="form-control text-center" placeholder="Fecha para controlar citas" autocomplete="off">
						</div>
						<div class="col-xs-6 col-sm-4 hidden">
							<label for="">¿Alguna prenda?</label>
							<input type="text" id="txtPrendaSimple" class="form-control" placeholder="Rellene si hay algùn item prendario" autocomplete="off">
						</div>
						
						<div class="col-xs-6 col-md-3">
							<button class="btn btn-azul btn-outline" style="margin-top: 10px;" id="btnSimularPagos"><i class="icofont-support-faq"></i> Simular</button>
							<button class="btn btn-infocat btn-outline" style="margin-top: 10px;" id="btnGuardarCred"><i class="icofont-save"></i> Guardar</button>
						
						</div>
						<label class="orange-text text-darken-1 hidden" id="labelFaltaCombos" for=""><i class="icofont-warning"></i> Todas las casillas tienen que estar rellenadas para proceder</label>
					</div>
				</div>
			
			</div>
			<div class="panel panel-default">
			<div class="panel-body">
				<p><strong>Resultados:</strong></p>
				<div class="container row" id="divVariables">
				</div>
				<div  id="tableSimulacion">
				<!-- <thead id="theadResultados">
				</thead>
				<tbody id="tbodyResultados"></tbody>-->
				</div> 
				</div>
			</div>
		
			
		<?php endif; //fin de get titular ?>
		<?php endif; //fin de get Credito ?>
		<?php if( !isset($_GET['titular']) && !isset($_GET['credito']) && !isset($_GET['record']) ): ?>
		<h3 class="purple-text text-lighten-1">Zona créditos</h3><hr>
		<h4 class="purple-text text-lighten-1">Mi cartera</h4><hr>
		<p>Comience buscando un crédito en la parte superior o seleccione uno de todos los créditos activos:</p>
			<table class="table table-hover" id="tableCreditosTodos">
			<thead>
				<tr>
					<th>N°</th>
					<th>Crédito</th>
					<th>Cliente</th>
					<th>Tipo</th>
					<th>Monto</th>
					<th>Periodo</th>
					<th>Vencido</th>
					<th>Interés</th>
					<th>Desembolso</th>
				</tr>
			</thead>
				<? include 'php/listarTodosPrestamos.php';?>
			</table>
		<? endif; ?>
				
			<!-- Fin de contenido principal -->
			</div>
		</div>
</div>
</div></div>
<!-- /#page-content-wrapper -->
</div><!-- /#wrapper -->
	

<!-- Modal para mostrar los clientes coincidentes -->
<div class="modal fade" id="mostrarResultadosClientes" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
<div class="modal-dialog modal-lg" role="document">
	<div class="modal-content">
		<div class="modal-header-indigo">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title" id="myModalLabel"><i class="icofont icofont-help-robot"></i> Resultados de la búsqueda</h4>
		</div>
		<div class="modal-body">
			<table class="table table-hover">
				<thead>
					<tr>
						<th>D.N.I.</th>
						<th>Apellidos y nombres</th>
						<th>@</th>
					</tr>
				</thead>
				<tbody id="rowClientesEncontrados">
				</tbody>
			</table>
		</div>
	</div>
</div>
</div>

<?php if(isset($_GET['credito']) && $rowCr['presAprobado']<> 'Sin aprobar' && $rowCr['presAprobado']<> "Rechazado" && $rowCr['presFechaDesembolso']<>'Desembolso pendiente' && in_array($_COOKIE['ckPower'], $soloCajas)): ?>
<!-- Modal para realizar un pago automtico combo -->
<div class="modal fade" id="mostrarRealizarPagoCombo" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
<div class="modal-dialog modal-sm" role="document">
	<div class="modal-content">
		<div class="modal-header-infocat">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title" id="myModalLabel"><i class="icofont icofont-help-robot"></i> Deudas pendientes</h4>
		</div>
		<div class="modal-body">
			<p>Los siguientes cálculos son calculados al día de hoy:</p>
			<div style="padding-left:20px">
				<p>Cuotas pendientes: <strong><span id="spaCPendientes"></span></strong></p>
				<p>Costo de cuota: <strong><span id="spaCCosto"></span></strong></p>
				<!-- <p>Com. y Serv.: <strong><span id="spaCSeguro"></span></strong></p> -->
				<!-- <p>Cuota: <strong>S/ <span id="spaCPrecioCuota"></span></strong></p> -->
				<p class="hidden">Días de mora: <strong><span id="spaCMora"></span></strong></p>
				<p>Mora diaria: <strong>S/ <span id="spaCPrecioMoraDiaria"></span></strong></p>
				<p>Mora a pagar: <strong>S/ <span id="spaCPrecioMora"></span></strong></p>
				<hr style="margin-top: 10px; margin-bottom: 10px; border-top: 1px solid #c1c1c1;margin-right: 50px;">
				<p>Pago total: <strong>S/ <span id="spaCTotal"></span></strong></p>
			</div>
			<div class="">
				<label class="hidden" for="">Mora</label>
				<input type="number" class="form-control input-lg text-center inputGrande esMoneda hidden" id="txtPagaClienteMora" style="margin: 0;" readonly>
				<div class="checkbox checkbox-infocat checkbox-circle">
					<input id="chkExonerar" class="styled" type="checkbox" >
					<label for="chkExonerar"> Exonerar mora </label>
				</div>
				<label for="">¿Cuánto dinero dispone el cliente?</label>
				<input type="number" class="form-control input-lg text-center inputGrande esMoneda" id="txtPagaClienteVariable" style="margin: 0;">
				<label for="">Tipo de pago</label>
				<select id="sltMonedaUpd" class="form-control" name="">
					<option value="1">Efectivo</option>
					<option value="2">Deposito bancario</option>
					<option value="3">Tarjeta Mastercard</option>
					<option value="4">Tarjeta Visa</option>
				</select>
			</div>
		</div>
		<div class="modal-footer">
			<div class="divError text-left animated fadeIn hidden" style="margin-bottom: 20px;"><i class="icofont-cat-alt-2"></i> Lo sentimos, <span class="spanError">La cantidad de ingresada no puede ser cero o negativo.</span></div>
			<button class="btn btn-infocat btn-outline" id="btnRealizarDeposito" data-dismiss="modal"><i class="icofont-ui-rate-add"></i> Realizar depósito</button>
		</div>
	
	</div>
</div>
</div>
<!-- Modal para realizar un pago automtico combo -->
<div class="modal fade" id="modalMorasExtras" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
<div class="modal-dialog modal-sm" role="document">
	<div class="modal-content">
		<div class="modal-header-infocat">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title" id="myModalLabel"><i class="icofont icofont-help-robot"></i> Moras extras</h4>
		</div>
		<div class="modal-body">
			<p>¿Cuánto pagó el cliente de mora?</p>
			<input type="number" class="form-control inputGrande text-center esMoneda" id="txtMoraExtra">
		</div>
		<div class="modal-footer">
			<button class="btn btn-infocat btn-outline" id="btnInsertarMoraExtra" data-dismiss="modal"><i class="icofont-ui-rate-add"></i> Insertar mora</button>
		</div>
	
	</div>
</div>
</div>

<?php endif; ?>

<?php include 'php/modals.php'; ?>
<?php include 'footer.php'; ?>
<script src="js/bootstrap-material-datetimepicker.js?version=2.0.1"></script>
<?php include 'php/existeCookie.php'; ?>

<script type="text/javascript" src="https://cdn.datatables.net/v/dt/jszip-2.5.0/dt-1.11.3/b-2.0.1/b-html5-2.0.1/datatables.min.js"></script>

<?php if ( !isset($_COOKIE['ckidUsuario']) ) return false;?>

<script>
datosUsuario();
$('.selectpicker').selectpicker();
$('.mitoolTip').tooltip();

$(document).ready(function(){
	$('#sltNuevoAsesr').val(-1);
	<?php if (isset($_GET['credito'])){ ?>
	$('#cmbPeriodos').val('<?= $rowCr['idTipoPrestamo']; ?>');
	<?php } ?>
<?php
if(isset($_GET['titular'])){
?>
agregarClienteCanasta('<?= $_GET['titular']; ?>', 1);
<?php
}
$hoy = new DateTime();
$saltoDia = new DateInterval('P1D');
$mañana = new DateTime();
$mañana->add($saltoDia);
?>

/* $('#tableSubIds').DataTable( {
	dom: 'Bfrtip',
	buttons: [
		{ extend: 'excel', text: '<i class="icofont-file-excel"></i> Exportar Excel', className: 'btn btn-outline btn-success' }
	]
}); */

$('#dtpFechaIniciov3').val('<?=  $hoy->format('d/m/Y');?>'); 
$('#dtpFechaIniciov3').bootstrapMaterialDatePicker({ /* shortTime : true, */
	format: 'DD/MM/YYYY',
	lang: 'es',
	time: false, 
	weekStart: 1,
	nowButton : true,
	switchOnClick : true,
	//minDate : new Date(),
	// okButton: false,
	okText: '<i class="icofont-check-alt"></i> Aceptar',
	nowText: '<i class="icofont-bubble-down"></i> Hoy',
	cancelText : '<i class="icofont-close"></i> Cerrar'
});
$('#dtpFechaPrimerv3').val('<?= $mañana->format('d/m/Y'); ?>');
$('#dtpFechaPrimerv3').bootstrapMaterialDatePicker({
	format: 'DD/MM/YYYY',
	lang: 'es',
	time: false,
	weekStart: 1,
	nowButton : false,
	switchOnClick : true,
	minDate :  moment().add(1, 'days'),
	disabledDays: [6,7],
	// okButton: false,
	okText: '<i class="icofont-check-alt"></i> Aceptar',
	nowText: '<i class="icofont-bubble-down"></i> Hoy',
	cancelText : '<i class="icofont-close"></i> Cerrar'
});
$('#txtAddCliente').keypress(function (e) {
	if(e.keyCode == 13){ $('#btnBuscarClientesDni').click(); }
});
$('#btnBuscarClientesDni').click(function () {
	if( $('#txtAddCliente').val()!='' ){
		
			$('#rowClientesEncontrados').children().remove();
			$.ajax({url: 'php/ubicarCliente.php', type: 'POST', data: { buscar: $('#txtAddCliente').val() }}).done(function(resp) {
				//console.log(resp);
				var json=JSON.parse(resp);
				if(json.length==0){
					$('#rowClientesEncontrados').append(`<tr">
							<td>No se encontraron coincidencias</td>
						</tr>`);
				}else{
					$.each( JSON.parse(resp) , function(i, dato){
						$('#rowClientesEncontrados').append(`<tr data-cli="${dato.idCliente}">
								<td>${dato.cliDni}</td>
								<td class="mayuscula">${dato.cliApellidoPaterno} ${dato.cliApellidoMaterno} ${dato.cliNombres} </td>
								<td><button class="btn btn-success btn-sm btn-outline btnSelectCliente" data-id="${dato.idCliente}" ><i class="icofont-ui-add"></i></button></td>
							</tr>`);				
					});
					}
				});
			$('#mostrarResultadosClientes').modal('show');
		
	}
});
$('#rowClientesEncontrados').on('click','.btnSelectCliente', function() {
	agregarClienteCanasta($(this).attr('data-id'), 3);
	$('#mostrarResultadosClientes').modal('hide');
});
$('#tbodySocios').on('click','.btnRemoveCanasta',function() {
	$(this).parent().parent().remove();
	//console.log( $(this).parent().parent().html() );
});
$('#tableSubIds tr').last().find('td').eq(5).text('0.00');

/* $('#sltTipoPrestamo').on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
  switch( $('#sltTipoPrestamo').selectpicker('val') ){
	  case "1": $('#txtPeriodo').attr( "max", "90" )
	  break;
  }
}); */

$('#txtDniSocioAdd').change(function() {
	$('#btnAsociarDNI').removeClass('hidden');
	$('#btnSiAsociarDNI').addClass('hidden');
	$('#siSocioAdd').parent().addClass('hidden');
	$('#noSocioAdd').parent().addClass('hidden');
});
$('#btnAsociarDNI').click(function() {
	$('#btnAsociarDNI').removeClass('hidden');
	$('#btnSiAsociarDNI').addClass('hidden');
	$('#siSocioAdd').parent().addClass('hidden');
	$('#noSocioAdd').parent().addClass('hidden');
	$.ajax({url: 'php/buscarSocio.php', type: 'POST', data: {idPrestamo: '<?= $codCredito;?>', dni: $('#txtDniSocioAdd').val() }}).done(function(resp) {
		console.log(resp);
		if( resp.includes('Libre: ') ){
			$('#siSocioAdd').text( resp.substring(0, resp.indexOf('//-//')));
			
			$codigoSocio = parseInt(resp.substring(resp.indexOf('//-//')+5, resp.length ));
			
			$('#btnSiAsociarDNI').removeClass('hidden');
			$('#siSocioAdd').parent().removeClass('hidden');
			$('#btnAsociarDNI').addClass('hidden');
			console.log( $codigoSocio );
		}else{
			
			$('#noSocioAdd').parent().removeClass('hidden');
			$('#noSocioAdd').text(resp);
			$codigoSocio ='';
		}
	});
});
$('#btnSiAsociarDNI').click(function() {
	if($codigoSocio !== ''){
		$.ajax({url: 'php/insertarSocioAPrestamo.php', type: 'POST', data: {prestamo: '<?= $codCredito;?>', socio: $codigoSocio }}).done(function(resp) {
			console.log(resp)
			if(resp=='ok'){
				location.reload();
			}
		});
	}
});
<?php if(in_array($_COOKIE['ckPower'], $soloAdmis )){ ?>
$('#sltNuevoAsesr').change(function() { console.log( 'cambio' );
	$.ajax({url: 'php/cambiarAsesorCredito.php', type: 'POST', data: { codPrest: '<?= $codCredito;?>', idAsesor: $('#sltNuevoAsesr').val() }}).done(function(resp) {
		console.log(resp)
		if(resp==1){
			location.reload();
		}
	});
});
$('#btnMoraFijaAsignar').click(function (){
	cambiarDatosMora();
})
$('#txtMoraFijaAsignar').keypress(function (e) { 
	if(e.keyCode == 13){ 
		cambiarDatosMora()
	}
});
function cambiarDatosMora(){
	$.ajax({url: 'php/guardarMoraFijo.php', type: 'POST', data: { mora:$('#txtMoraFijaAsignar').val(), idPrestamo: '<?= $codCredito;?>'  }}).done(function(resp) {
			//console.log(resp)
			if(resp=='ok'){
				$('#spanBien').text('Mora fija, guardado correctamente')
				$('#h1Bien').html(``);
				$('#modalGuardadoCorrecto').modal('show');
				$('#modalGuardadoCorrecto').on('hidden.bs.modal', function () { 
					location.reload();
				});
			}
		});
}

$('#btnBorrarDefSocio').click(function() {
	$.ajax({url: 'php/borrarrSocioDePrestamo.php', type: 'POST', data: {prestamo: '<?= $codCredito;?>', socio: $.idBorrarSocio }}).done(function(resp) {
			console.log(resp)
			if(resp=='ok'){
				location.reload();
			}
		});
});
<?php if (isset($_GET['credito'])){ ?>
$('#cmbPeriodos').change(function() {
	//if($('#cmbPeriodos').val() != '<?= $rowCr['idTipoPrestamo']?>' ){
		//console.log('seguro?' );
		$('#modalCambiarTipoFechas').modal('show');
	//}
});
<?php } ?>
$('#btnCambioTipoFechas').click(function() {
	$.ajax({url: 'php/cambiarTipoFechas.php', type: 'POST', data: {idPrestamo: '<?= $codCredito;?>', modo: $('#cmbPeriodos').val(), fechaNueva: $('#fechaNuevaReprogramacion').val() }}).done(function(resp) {
		console.log(resp)
		if(resp=='ok'){
			location.reload();
		}
	});
});
<?php } ?>

$('#txtPeriodo').keyup(function() {
	switch( $('#sltTipoPrestamo').selectpicker('val') ){
	  case "1":
	  	if( $('#txtPeriodo').val() >=90 ){
			$('#txtPeriodo').val(90);
		}
		break;
		case "2":
	  	if( $('#txtPeriodo').val() >=48 ){
			$('#txtPeriodo').val(48);
		}
		break;
		case "3":
	  	if( $('#txtPeriodo').val() >=60 ){
			$('#txtPeriodo').val(60);
		}
		break;
		case "4":
	  	if( $('#txtPeriodo').val() >=24 ){
			$('#txtPeriodo').val(24);
		}
	  break;
  }
});

}); //Fin de Document ready

$('#btnSimularPagos').click(function() {
	if( $('#sltTipoPrestamo').val()=='' || $('#txtPeriodo').val()=='' || $('#txtMontoPrinc').val()=='' ||  parseFloat($('#txtPeriodo').val())==0 || parseFloat($('#txtMontoPrinc').val())==0 ){
		//console.log('falta algo')
		$('#labelFaltaCombos').removeClass('hidden');
	}else{
		$('#labelFaltaCombos').addClass('hidden');
		if( $('#sltTipoInteres').val()==1 ){
			$.ajax({url: 'php/simularPrestamoOnline.php', type: 'POST', data: {
				modo: $('#sltTipoPrestamo').val(),
				periodo: $('#txtPeriodo').val(),
				monto: $('#txtMontoPrinc').val(),
				tasaInt: $('#txtInteres').val(),
				fDesembolso: moment($('#dtpFechaIniciov3').val(), 'DD/MM/YYYY').format('YYYY-MM-DD'),
				primerPago: moment($('#dtpFechaPrimerv3').val(), 'DD/MM/YYYY').format('YYYY-MM-DD'),
				fijar: $('#sltFijas').val()
				}}).done(function(resp) { //console.log(resp)
				$('#tableSimulacion').html(resp);
			//	$('#tbodyResultados td').last().text('0.00');
			});
			$('#divVariables').children().remove();
		}else if( $('#sltTipoInteres').val()==2 ){
			$.ajax({url: 'php/simularInteresCompuesto.php', type: 'POST', data: {
				modo: $('#sltTipoPrestamo').val(),
				periodo: $('#txtPeriodo').val(),
				monto: $('#txtMontoPrinc').val(),
				tasaInt: $('#txtInteres').val(),
				fDesembolso: moment($('#dtpFechaIniciov3').val(), 'DD/MM/YYYY').format('YYYY-MM-DD'),
				fijar: $('#sltFijas').val()
			 }}).done(function(resp) {
				//console.log(resp)
				$('#tableSimulacion').html(resp);
			});
		}

	} //fin de else
});
$('#txtSoloBuscaCreditos').keypress(function (e) { 
	var valor = $('#txtSoloBuscaCreditos').val().toUpperCase();
	if(e.keyCode == 13){ 
		if( valor.indexOf('CR-')==0 ){
			$.post('php/58encode.php', {texto: valor.replace('CR-', '') }, function(resp) {
				window.location.href = 'creditos.php?credito='+resp;
			});
		}
	}
});
function agregarClienteCanasta(idCl, cargo) { //console.log( idCl );
	$.ajax({url: 'php/ubicarDatosCliente.php', type: 'POST', data: { idCli: idCl }}).done(function(resp) {
	//console.log(resp);
	var dato = JSON.parse(resp);
	var botonDelete;
	if(cargo!=1){
		botonDelete='<button class="btn btn-danger btn-sm btn-outline btn-sinBorde btn-circle btnRemoveCanasta" data-id="${dato.idCliente}" ><i class="icofont-close"></i></button>';
	}else{botonDelete="";}
	$('#tbodySocios').append(`<tr data-cli="${dato[0].idCliente}">
			<td>${dato[0].cliDni}</td>
			<td class="mayuscula">${dato[0].cliApellidoPaterno} ${dato[0].cliApellidoMaterno} ${dato[0].cliNombres} </td>
			<td>${dato[0].civDescripcion}</td>
			<td><select class="form-control"><?php include 'php/OPTTipoCliente.php';?></select></td>
			<td>${botonDelete}</td>
		</tr>`);

		if(cargo==1 || cargo==2){
			$(`[data-cli="${dato[0].idCliente}"]`).find('select').val(cargo).attr('disabled','true');
		}else{
			$(`[data-cli="${dato[0].idCliente}"]`).find('select').val(cargo);
			$(`[data-cli="${dato[0].idCliente}"]`).find('select').find('[value="1"]').attr('disabled', 'true');
			$(`[data-cli="${dato[0].idCliente}"]`).find('select').find('[value="2"]').attr('disabled', 'true');
		}
			
		
});
if(cargo==1){
	$.ajax({url: 'php/listarMatrimonio.php', type: 'POST', data: { conyugue: idCl }}).done(function(resp) { //console.log(resp)
		var datoMatri= JSON.parse(resp); console.log( idCl ); 
		if(datoMatri.length==1){

			if(datoMatri[0].idEsposo==parseFloat(idCl)){
			//	console.info('esposo') //listar a la esposa
				agregarClienteCanasta(datoMatri[0].idEsposa, 2);
			}else{
				//console.info('esposa') //listar al esposo
				agregarClienteCanasta(datoMatri[0].idEsposo, 2);
			}
		}
	});
}
}//fin de function
$('#btnGuardarCred').click(function() {
	if( $('#sltTipoPrestamo').val()=='' || $('#txtPeriodo').val()=='' || $('#txtInteres').val()=='' || $('#txtMontoPrinc').val()=='' ||  parseFloat($('#txtPeriodo').val())==0 || parseFloat($('#txtMontoPrinc').val())==0 ){
		//console.log('falta algo')
		$('#labelFaltaCombos').removeClass('hidden');
	}else{
		$('#labelFaltaCombos').addClass('hidden');

		var clientArr = [];
		$.each( $('#tbodySocios tr') , function(i, objeto){
			clientArr.push( { 'id': $(objeto).attr('data-cli'), 'grado':  $(objeto).find('select').val()}  )
		});

		if( $('#sltTipoInteres').val()==1 ){
			$.ajax({url: 'php/insertarPrestamoOnline.php', type: 'POST', data: {
				clientes: clientArr,
				modo: $('#sltTipoPrestamo').val(),
				periodo: $('#txtPeriodo').val(),
				monto: $('#txtMontoPrinc').val(),
				tasaInt: $('#txtInteres').val(),
				fDesembolso: moment($('#dtpFechaIniciov3').val(), 'DD/MM/YYYY').format('YYYY-MM-DD'),
				primerPago: moment($('#dtpFechaPrimerv3').val(), 'DD/MM/YYYY').format('YYYY-MM-DD'),
				prendaSimple: $('#txtPrendaSimple').val(),
				fijar: $('#sltFijas').val()
			}}).done(function(resp) {
				console.log(resp)
				if( parseInt(resp)>0 ){

					$.post("php/58decode.php", {texto: resp}, function(data){ console.log(data);
						$('#spanBien').text('Código de préstamo:')
						$('#h1Bien').html(`<a href="creditos.php?credito=`+resp+`">CR-`+data+`</a> <br> <button class="btn btn-default " id="btnImpresionPrevia" data-pre="`+resp+`"><i class="icofont-print"></i> Imprimir Impresora</button>`);
						$('#modalGuardadoCorrecto').modal('show');
						$('#modalGuardadoCorrecto').on('hidden.bs.modal', function () {
							window.location.href = `creditos.php?credito=`+resp;
						});
					});
				}
			});
		}else if( $('#sltTipoInteres').val()==2 ){ 
			
			$.ajax({url: 'php/insertarPrestamoFrances.php', type: 'POST', data: {
				clientes: clientArr,
				modo: $('#sltTipoPrestamo').val(),
				periodo: $('#txtPeriodo').val(),
				monto: $('#txtMontoPrinc').val(),
				tasaInt: $('#txtInteres').val(),
				fDesembolso: moment($('#dtpFechaIniciov3').val(), 'DD/MM/YYYY').format('YYYY-MM-DD'),
				fijar: $('#sltFijas').val()
			}}).done(function(resp) {
				console.log(resp)
				if( parseInt(resp)>0 ){

					$.post("php/58decode.php", {texto: resp}, function(data){ console.log(data);
						$('#spanBien').text('Código de préstamo:')
						$('#h1Bien').html(`<a href="creditos.php?credito=`+resp+`">CR-`+data+`</a> <br> <button class="btn btn-default " id="btnImpresionPrevia" data-pre="`+resp+`"><i class="icofont-print"></i> Imprimir Impresora</button>`);
						$('#modalGuardadoCorrecto').modal('show');
						$('#modalGuardadoCorrecto').on('hidden.bs.modal', function () {
							window.location.href = `creditos.php?credito=`+resp;
						});
					});
				}
			});
		}
	}
});
$('#h1Bien').on('click', '#btnImpresionPrevia', function(){
	var dataUrl="php/printCronogramaPagos.php?prestamo="+$(this).attr('data-pre');
	window.open(dataUrl, '_blank' );
});
$('#contenedorCreditosFluid').on('click', '#btnImpresionPreviaPDF', function(){
	//console.log( 'aca' );
	var dataUrl="http://infocatsoluciones.com/app/prestamosHuancayo/php/printCronogramaPagos.php?prestamo="+$(this).attr('data-pre')+"&pdf=true";
	console.log( dataUrl );
	window.open("http://api.pdflayer.com/api/convert?access_key=a6cde55f1e5fc702ab0c70874cb60846&document_url="+encodeURIComponent(dataUrl), '_blank' );
});
$('body').on('click', '#btnImpresionContrato', function(){
	var monto = $('#spanMontoDado').attr('data-monto');
	var fecha1 = moment($('#tableSubIds tbody tr').first().children().eq(2).text(), 'DD/MM/YYYY').format('YYYY-MM-DD');
	var fechaPri = moment($('#tableSubIds tbody tr').eq(1).children().eq(2).text(), 'DD/MM/YYYY').format('YYYY-MM-DD');
	var fecha2 = moment($('#tableSubIds tbody tr').last().children().eq(2).text(), 'DD/MM/YYYY').format('YYYY-MM-DD');
	var interes = $('#pinteresGlobal').attr('data-int');
	var cantCuotas = $('#tableSubIds tbody tr').length-1;
	var tipoPago = '';
	switch ($('#spanTipoDescpago').text()) {
		case "Mensual": tipoPago='MENSUALES'; break;
		case "Diario": tipoPago='DIARIOS'; break;
		case "Quincenal": tipoPago='QUINCENALES'; break;
		case "Semana": tipoPago='SEMANALES'; break;
		case "Semanal": tipoPago='SEMANALES'; break;
		default: break;
	}
	
	var dataUrl="impresion/printContrato.php?credito="+$(this).attr('data-pre')+"&monto="+monto+"&fecha1="+fecha1+"&fecha2="+fecha2+"&fechaPri="+fechaPri+"&interes="+interes+"&cantCuota="+cantCuotas+"&tPago="+tipoPago;
	window.open(dataUrl, '_blank' );
});
$('body').on('click', '#btnImpresionContratoPDF', function(){
	var monto = $('#spanMontoDado').attr('data-monto');
	var fecha1 = moment($('#tableSubIds tbody tr').first().children().eq(2).text(), 'DD/MM/YYYY').format('YYYY-MM-DD');
	var fechaPri = moment($('#tableSubIds tbody tr').eq(1).children().eq(2).text(), 'DD/MM/YYYY').format('YYYY-MM-DD');
	var fecha2 = moment($('#tableSubIds tbody tr').last().children().eq(2).text(), 'DD/MM/YYYY').format('YYYY-MM-DD');
	var interes = $('#pinteresGlobal').attr('data-int');
	var cantCuotas = $('#tableSubIds tbody tr').length-1;
	var tipoPago = '';
	switch ($('#spanTipoDescpago').text()) {
		case "Mensual": tipoPago='MENSUALES'; break;
		case "Diario": tipoPago='DIARIOS'; break;
		case "Quincenal": tipoPago='QUINCENALES'; break;
		case "Semana": tipoPago='SEMANALES'; break;
		case "Semanal": tipoPago='SEMANALES'; break;
		default:break;
	}
	
	var dataUrl="http://infocatsoluciones.com/app/prestamosHuancayo/impresion/printContrato.php?credito="+$(this).attr('data-pre')+"&monto="+monto+"&fecha1="+fecha1+"&fecha2="+fecha2+"&fechaPri="+fechaPri+"&interes="+interes+"&cantCuota="+cantCuotas+"&tPago="+tipoPago+"&pdf=true"+'&document_name=Contrato-CR-<?= $codCredito;?>';
	console.log( dataUrl );
	window.open("http://api.pdflayer.com/api/convert?access_key=a6cde55f1e5fc702ab0c70874cb60846&document_url="+encodeURIComponent(dataUrl), '_blank' );
});
$('#rowBotonesMaestros').on('click', '#btnImpresionPrevia', function(){
	var dataUrl="php/printCronogramaPagos.php?prestamo="+$(this).attr('data-pre');
	window.open(dataUrl, '_blank' );
});
$('#sltTipoPrestamo').change(function() {
/* 	if( $(this).val()==3 ){
		$('#divPrimerPago').removeClass('hidden');
	}else{
		$('#divPrimerPago').addClass('hidden');
	} */
});
$('#dtpFechaIniciov3').change(function() {
	$('#dtpFechaPrimerv3').val('01/'+moment($('#dtpFechaIniciov3').val(), 'DD/MM/YYYY' ).add(1, 'month').format('MM/YYYY'))
	$('#dtpFechaPrimerv3').bootstrapMaterialDatePicker( 'setMinDate', moment($('#dtpFechaIniciov3').val(), 'DD/MM/YYYY').add(1, 'days') );
});
<?php if(isset( $_GET['credito'])): ?>
$('#btnDesembolsar').click(function() {
	$.ajax({url: 'php/updateDesembolsoDia.php', type: 'POST', data:{ credito: '<?= $_GET['credito'];?>' }}).done(function(resp) {
		console.log(resp)
		if(resp==true){
			//location.reload();
			var seguro = "0.00"; //parseFloat($('#spanMontoDado').text()*0.015).toFixed(2);
			$('#h1Bien').html(''); //`Cobre S/ ${seguro} de seguro al cliente.`
			$('#modalGuardadoCorrecto').modal('show');
			$.ajax({url: '<?= $serverLocal;?>impresion/ticketDesembolso.php', type: 'POST', data: { queMichiEs: 'Crédito nuevo', codPrest: '<?= $codCredito;?>', cliente: $('#spanTitular').text(), monto: parseFloat($('#spanMontoDado').text()).toFixed(2), seguro: seguro, hora: moment().format('DD/MM/YYYY hh:mm a'), usuario: '<?= $_COOKIE['ckAtiende'];?>', ckcelularEmpresa: '<?= $_COOKIE['ckcelularEmpresa']; ?>' }}).done(function(resp) {
				console.log(resp)
			});
			$('#modalGuardadoCorrecto').on('hidden.bs.modal', function () {
				location.reload();
			});
		}
	});
});
$('#chkExonerar').change(function(){
	
	var total = parseFloat($('#spaCTotal').text());

	if( $('#chkExonerar').prop('checked') ){
		var mora= parseFloat($('#spaCPrecioMora').text());
		$('#spaCPrecioMora').attr('data-mora', $('#spaCPrecioMora').text());
		$('#spaCTotal').text((total-mora).toFixed(2));
		$('#spaCPrecioMora').text('0.00');
		$('#txtPagaClienteMora').val('0.00');
	}else{
		$('#spaCPrecioMora').text( $('#spaCPrecioMora').attr('data-mora'));
		var mora= parseFloat($('#spaCPrecioMora').text());
		$('#spaCTotal').text((total+mora).toFixed(2));
		$('#txtPagaClienteMora').val((mora).toFixed(2));
	}
});
$('.spanPrint').click(function() {
	var padre = $(this).parent().parent().parent();
	var queEs= $(this).attr('data-print');
	
	switch(queEs){
		case 'parcial':
			$.post("http://localhost/prestamosPrestar/impresion/ticketCuotaParcial.php", {
				cknombreEmpresa: '<?= $_COOKIE['cknombreEmpresa'];?>',
				ckLemaEmpresa: '<?= $_COOKIE['ckLemaEmpresa'];?>',
				queMichiEs: 'Adelanto de cuota',
				hora: moment().format('DD/MM/YYYY h:mm a'),
				cliente: $('#spanTitular').text(),
				codPrest: $('#h3Codigo').attr('data-id'),
				monto: padre.find('.tdPagoCli').attr('data-pago'),
				usuario: '<?= $_COOKIE["ckAtiende"];?>',
				ckcelularEmpresa: '<?= $_COOKIE['ckcelularEmpresa'];?>',
				cktelefonoEmpresa: '<?= $_COOKIE['cktelefonoEmpresa'];?>'
			}, function(resp){ console.log(resp)});
		break;
		case 'completo':
		    
			$.post("http://localhost/prestamosPrestar/impresion/ticketCuotaParcial.php", {
				cknombreEmpresa: '<?= $_COOKIE['cknombreEmpresa'];?>',
				ckLemaEmpresa: '<?= $_COOKIE['ckLemaEmpresa'];?>',
				queMichiEs: 'Cuota cancelada',
				hora: moment().format('DD/MM/YYYY h:mm a'),
				cliente: $('#spanTitular').text(),
				codPrest: $('#h3Codigo').attr('data-id'),
				monto: padre.find('.tdPagoCli').attr('data-pago'),
				usuario: '<?= $_COOKIE["ckAtiende"];?>',
				ckcelularEmpresa: '<?= $_COOKIE['ckcelularEmpresa'];?>',
				cktelefonoEmpresa: '<?= $_COOKIE['cktelefonoEmpresa'];?>'
			}, function(resp){ console.log(resp)});
		break;
	}
});
<?php endif; ?>
<?php if(isset($_GET['credito']) && $rowCr['presAprobado']=== 'Sin aprobar'): ?>
$('#btnShowVerificarCredito').click(function() {
	$('#modalVerificarCredito').modal('show');
});
$('#btnDenyVerificarCredito').click(function() {
	$('#modalDenegarCredito').modal('show');
});
$('#btnVerificarCredito').click(function() {
	$.ajax({url: 'php/updateVerificarCredito.php', type: 'POST', data: { credit: '<?= $codCredito; ?>' }}).done(function(resp) { //console.log(resp)
		if(resp==1)
			location.reload();
	});
});
<?php endif;
if( in_array($_COOKIE['ckPower'], $soloAdmis)){ ?>
$('#btnAnularCredito').click(function() {
	$('#modalDenegarCredito').modal('show');
});
$('#btnDenegarCredito').click(function() {
	$.ajax({url: 'php/updateDenegarCredito.php', type: 'POST', data: { credit: '<?= $codCredito; ?>', razon: $('#txtDenegarRazon').val() }}).done(function(resp) { //console.log(resp)
		if(resp==1){
			location.reload();
		}
	});
});
$('#btnReprogramarFechas').click(function() {
	$("#modalReprogramarFechas").modal('show');
});
$('#btnCalibrarFechas').click(function() {
	$.post('php/reprogramacionFechas_v2.php', {nuevaFecha: $('#fechaReprogramacion').val(), idPrestamo: '<?= $codCredito; ?>' }, function(resp){
		let respuesta = JSON.parse(resp)
		if(respuesta.respuesta == 'ok'){
			$('#modalGuardadoCorrecto #spanBien').text(respuesta.mensaje);
			$('#modalGuardadoCorrecto').modal('show')
		}
		//location.reload();
	});
});
$('#btnReactivarPrestamo').click(function() {
	$.ajax({url: 'php/reactivarCredito.php', type: 'POST', data: { idPrestamo: '<?php if(isset ($_GET['credito'])){echo $_GET['credito'];}else{echo '';}; ?>' }}).done(function(resp) {
		console.log(resp)
		if(resp =='ok'){
			location.reload();
		}
	});
});
async function limpiarPago(idCuota, indice){
	if(confirm('¿Desea limpiar el registro de pago?')){
		$.ajax({
			url: 'php/limpiarPago.php', type: 'POST', data:{ idCuota }
		}).done( resp => {
			if(resp=='ok')
			 location.reload();
		})
	}
}
<?php }
if( in_array($_COOKIE['ckPower'], $soloCajas) ){ ?>

$('.btnPagarCuota').click(function() {
	var code= $(this).parent().parent().children().first().text();
	$('#strSubCredito').text( code );
	$('#btnPagarCreditoCompleto').attr('data-id', code.replace('SP-', ''));
	$('#modalPagoCreditoCompleto').modal('show');
});
$('#btnPagarCreditoCompleto').click(function() {
	$.ajax({url: 'php/pagarCreditoCompleto.php', type: 'POST', data: { idCred: $(this).attr('data-id') }}).done(function(resp) {
		console.log(resp)
		if(resp==true){
			location.reload();
		}
	});
});
$('#btnMoraExtra').click(function() {
	$('#modalMorasExtras').modal('show');
});
$('#btnInsertarMoraExtra').click(function() {
	$.ajax({url: 'php/insertarMoraExtra.php', type: 'POST', data: { credito: '<?php if(isset ($_GET['credito'])){echo $_GET['credito'];}else{echo '';}; ?>', mora: $('#txtMoraExtra').val() }}).done(function(resp) {
		console.log(resp)
		if(resp==1){
			var linea = "Mora extra: S/ " + $('#txtMoraExtra').val() ;
			$.ajax({url: '<?= $serverLocal;?>impresion/ticketCuotas.php', type: 'POST', data: { queMichiEs: linea, codPrest: '<?= $codCredito;?>', cliente: $('#spanTitular').text(), hora: moment().format('DD/MM/YYYY hh:mm a'), usuario: '<?= $_COOKIE['ckAtiende'];?>', ckcelularEmpresa: '<?= $_COOKIE['ckcelularEmpresa']; ?>' }}).done(function(resp) {
					console.log(resp)
				});			
			$('#h1Bien2').append(`Se guardó correctamente la mora extraordinaria.`);
			$('#btnPrintTicketPagoGlo').addClass('hidden');
			$('#modalGuardadoCorrecto2').modal('show');
			$('#modalGuardadoCorrecto2').on('hidden.bs.modal', function () { 
				location.reload();
			});
		}
	});
});

$('#btnsolicitarDeuda').click(function() {
	$.ajax({url: 'php/solicitarDeudasHoy.php', type: 'POST', data: { credito: '<?php if(isset ($_GET['credito'])){echo $_GET['credito'];}else{echo '';}; ?>' }}).done(function(resp) {
		console.log(resp);
		var data=JSON.parse(resp);
		console.log( data );
		if(data.diasMora==0){
			//$('#spaCMora').parent().parent().addClass("hidden");
			$('#spaCPrecioMora').parent().parent().addClass("hidden");
			$('#spaCPrecioMoraDiaria').parent().parent().addClass("hidden");
		}else{
			//$('#spaCMora').parent().parent().removeClass("hidden");
			$('#spaCPrecioMora').parent().parent().removeClass("hidden");
			$('#spaCPrecioMoraDiaria').parent().parent().removeClass("hidden");
		}
		$('#spaCPendientes').text(data.tantasCuotas);
		$('#spaCCosto').text(data.precioCuotas.toFixed(2));
		//$('#spaCMora').text(data.diasMora);
		$('#spaCPrecioCuota').text(data.deudaCuotas.toFixed(2));
		$('#spaCSeguro').text(data.seguro.toFixed(2));
		
		<?php if( $estadoMora ): ?>
			$('#spaCPrecioMora').text(<?= $rowCr['preMoraFija'];?>);
			$('#spaCTotal').text(parseFloat(parseFloat(data.deudaCuotas.toFixed(2)) + parseFloat('<?= $rowCr['preMoraFija']; ?>')).toFixed(2) );
			$.laMora = parseFloat(<?= $rowCr['preMoraFija'];?>);
		<?php else: ?>
			$('#spaCPrecioMora').text(data.precioMora.toFixed(2));
			$('#spaCPrecioMoraDiaria').text(data.mora_neta.toFixed(2));
			$('#spaCTotal').text(data.paraFinalizar.toFixed(2));
			$.laMora=data.precioMora;
		<?php endif; ?>
		$('#txtPagaClienteMora').val($.laMora.toFixed(2));
		$('#mostrarRealizarPagoCombo').modal('show');
		
	});
});
$('#btnRealizarDeposito').click(function() {
	pantallaOver(true);
	$('#h1Bien2').children().remove();
	if( $('#txtPagaClienteVariable').val()<=0 ){
		$('#mostrarRealizarPagoCombo .divError').removeClass('hidden').find('.spanError').text('No se permiten valores negativos o ceros.');
	}/* else if($('#txtPagaClienteVariable').val() > parseFloat($('#spaCTotal').text())  ){
		$('#mostrarRealizarPagoCombo .divError').removeClass('hidden').find('.spanError').html('El monto máximo que se puede depositar es <strong>S/ '+$('#spaCTotal').text()+'</strong> .');
	} else if( $('#txtPagaClienteVariable').val() < parseFloat($('#spaCPrecioMora').text()) ){
		$('#mostrarRealizarPagoCombo .divError').removeClass('hidden').find('.spanError').html('Debe adeltar y cubrir mínimo la mora <strong>S/ '+$('#spaCPrecioMora').text()+'</strong> .');
	}*/
	else{
		var linea ='';
		$.ajax({url: 'php/pagarCreditoCombo.php', type: 'POST', data: {credito: '<?php if(isset ($_GET['credito'])){echo $_GET['credito'];}else{echo '';}; ?>', dinero: $('#txtPagaClienteVariable').val(), exonerar: $('#chkExonerar').prop('checked'), cliMora: $.laMora, moneda: $('#sltMonedaUpd').val() }}).done(function(resp) { console.log( resp ); 
			var data = JSON.parse(resp); console.log(data)
			var sumAcumulado=0, sumaMoras=0;
			if( data.length >0 ){
				if(data[0].diasMora>0){ console.log( 'con mora' );
					$('#tituloPeque2').text('Items cancelados');
					$('#h1Bien2').append(`<span  data-quees='${data[0].queEs}' data-monto='${data[0].montoCuota}' data-id='0'>Mora: S/ `+ parseFloat(data[0].sumaMora).toFixed(2) +`</span><br>`);
					linea = linea + data[0].queEs +': S/ '+parseFloat(data[0].sumaMora).toFixed(2)+"\n";
					sumaMoras = parseFloat(data[0].sumaMora);
				}else{ console.log( 'sin mora' );
					/* for(i=1; i<data.length; i++){$('#h1Bien2').append(`<span data-quees='${data[i].queEs}' data-monto='${data[i].montoCuota}' data-id='${data[i].cuota}'>SP-`+ data[i].cuota +`: S/ `+ parseFloat(data[i].montoCuota).toFixed(2) +`</span><br>`);} */
				}
				
				for(i=1; i<data.length; i++){
					$('#h1Bien2').append(`<span data-quees='${data[i].queEs}' data-monto='${data[i].montoCuota}' data-id='${data[i].cuota}'>SP-`+ data[i].cuota +`: S/ `+ parseFloat(data[i].montoCuota).toFixed(2) +`</span><br>`);
					linea = linea + data[i].queEs +': S/ '+parseFloat(data[i].montoCuota).toFixed(2)+"\n";
					sumAcumulado+=data[i].montoCuota
				}
				linea = linea + "Total: S/ " + parseFloat(sumAcumulado + sumaMoras ).toFixed(2);
				
				if(data[0].faltan>0){
					linea = linea + "\nTiene " + data[0].faltan + " cuotas pendientes.\n";
				}else if(data[0].faltan==0){
					linea = linea + "\nUd. Acaba de finalizar todas sus cuotas.\n";
				}
				//console.log( linea );
				$.ajax({url: '<?= $serverLocal;?>impresion/ticketCuotas.php', type: 'POST', data: { queMichiEs: linea, codPrest: '<?= $codCredito;?>', cliente: $('#spanTitular').text(), hora: moment().format('DD/MM/YYYY hh:mm a'), usuario: '<?= $_COOKIE['ckAtiende'];?>', ckcelularEmpresa: '<?= $_COOKIE['ckcelularEmpresa']; ?>' }}).done(function(resp) {
					console.log(resp)
				});
				$('#modalGuardadoCorrecto2').modal('show');
				$('#modalGuardadoCorrecto2').on('hidden.bs.modal', function () { 
					location.reload();
				});
				
			}
			// if(resp==true){
			// 	location.reload();
			// }

		});
	}
	pantallaOver(false);
});
$('#btnPrintTicketPagoGlo').click(function() {
	var texto='';
	$.each( $('#h1Bien2 span'), function (i, elem) {
		texto=texto+ $(elem).text()+"<br>";
	});
	window.open('php/printComprobanteCuota.php?tipo=Pago%20de%20cuotas&texto='+encodeURIComponent(texto)+"&codigo=<?=$codCredito?>", '_blank' );
	
});
<?php } ?>
</script>

<style>
	#contenedorCreditosFluid label{font-weight: 500;}
	#contenedorCreditosFluid p, #contenedorCreditosFluid table{color: #a35bb4;}
	.modal p{color: #333;}
	.spanIcono{font-size:16px; margin: 0 5px;}
	.text-danger {
			color: #f9221e;
	}.text-primary {
			color: #1388ec;
	}.text-success {
			color: #00b303;
	}
	.dataTables_filter, .dataTables_info, .dataTables_paginate {
	display: none;
	}
</style>
</body>


</html>