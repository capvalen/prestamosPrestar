<?php 
include "php/variablesGlobales.php";
if( !in_array($_COOKIE['ckPower'], $soloAdmis ) ){ header('Location: sinPermiso.php'); exit; }

if (isset($_GET['año']) && isset($_GET['mes']) ){
	$sqlVariables="SELECT * FROM `balanceGeneral` where balaAño = {$_GET['año']} and balaMes = {$_GET['mes']}; ";
	$resultadoVariables=$preferido->query($sqlVariables);
	if($resultadoVariables->num_rows>0){
		include "php/sumaEstados.php";
		while($rowVariables=$resultadoVariables->fetch_assoc()){ 
			switch ($rowVariables['balaVariable']) {
				case 'A111Disponible': $A111Disponible = $rowVariables['balaValor']; break;
				case 'A1111Caja': $A1111Caja = $rowVariables['balaValor']; break;
				case 'A112Bancos': $A112Bancos = $rowVariables['balaValor']; break;
				case 'A112CuentasPorCobrar': $A112CuentasPorCobrar = $rowVariables['balaValor']; break;
				case 'A113OtrasCuentas': $A113OtrasCuentas = $rowVariables['balaValor']; break;
				case 'A114Adelantos': $A114Adelantos = $rowVariables['balaValor']; break;
				case 'A115Mercaderia': $A115Mercaderia = $rowVariables['balaValor']; break;
				case 'A121Inmueble': $A121Inmueble = $rowVariables['balaValor']; break;
				case 'A1211Terrenos': $A1211Terrenos = $rowVariables['balaValor']; break;
				case 'A1213Unidad': $A1213Unidad = $rowVariables['balaValor']; break;
				case 'A211Proveedores': $A211Proveedores = $rowVariables['balaValor']; break;
				case 'A212OrasCuentas': $A212OrasCuentas = $rowVariables['balaValor']; break;
				case 'A31Patrimonio': $A31Patrimonio = $rowVariables['balaValor']; break;
				case 'listaMaquinarias': $listaMaquinarias = $rowVariables['balaJson']; break;
				case 'listaPrestamosMenor': $listaPrestamosMenor = $rowVariables['balaJson']; break;
				case 'listaPrestamos': $listaPrestamos = $rowVariables['balaJson']; break;
				default: break;
			}
		}
	}
	
}

 ?>
<!DOCTYPE html>
<html lang="es">

<head>

		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, shrink-to-fit=no, initial-scale=1">
		<meta name="description" content="">
		<meta name="author" content="">

		<title>Principal - Sistema Préstamos</title>

		<!-- Bootstrap Core CSS -->
		<?php include 'headers.php'; ?>
		<!-- development version, includes helpful console warnings -->
		<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
</head>

<body>

<style>
#ambosBalances .row{
	padding-top: 10px;padding-bottom: 10px;
}
#ambosBalances label{font-weight: 500;}
#ambosBalances .row:hover{
	background-color: #eee;
}
#ambosBalances .titulo,
#ambosBalances .encabezado
{ border:1px solid #ddd;}
#ambosBalances strong{color:#000!important;}
#ambosBalances input{margin-bottom: 0px;}
#ambosBalances .input-sm{height: 30px!important; font-size: 12px!important; }
.close{ color: #fd0303;}
</style>
<div id="wrapper">
	<!-- Sidebar -->
	<?php include 'menu-wrapper.php' ?>
	<!-- /#sidebar-wrapper -->
<!-- Page Content -->
<div id="page-content-wrapper">
	<div class="container-fluid" >
		<div class="row noselect">
			<div class="col-lg-12 contenedorDeslizable ">
				<!-- Empieza a meter contenido principal -->
				<div class="row">
					<h2 class="purple-text text-lighten-1">Balances</h2>
					
					<div class="form-inline">
						<div class="form-group">
							<label for="my-select">Fecha de balance</label>
							<select id="sltAño" class="custom-select form-control" style="margin-bottom: 0px;">
								<option value="-1">Año</option>
								<<?php for ($i=2020; $i <= date('Y') ; $i++) { ?>>
								<option value="<?= $i; ?>" <?php if (isset($_GET['año'])){ if($_GET['año']==$i){echo "selected"; }} ?> ><?= $i; ?></option>
								<?php } ?>
							</select>
						</div>
						<div class="form-group">
							<select id="sltMes" class="custom-select form-control" style="margin-bottom: 0px;">
								<option value="-1">Mes</option>
								<option value="1" <?php if (isset($_GET['mes'])){ if($_GET['mes']==1){echo "selected"; }} ?> >Enero</option>
								<option value="2" <?php if (isset($_GET['mes'])){ if($_GET['mes']==2){echo "selected"; }} ?> >Febrero</option>
								<option value="3" <?php if (isset($_GET['mes'])){ if($_GET['mes']==3){echo "selected"; }} ?> >Marzo</option>
								<option value="4" <?php if (isset($_GET['mes'])){ if($_GET['mes']==4){echo "selected"; }} ?> >Abril</option>
								<option value="5" <?php if (isset($_GET['mes'])){ if($_GET['mes']==5){echo "selected"; }} ?> >Mayo</option>
								<option value="6" <?php if (isset($_GET['mes'])){ if($_GET['mes']==6){echo "selected"; }} ?> >Junio</option>
								<option value="7" <?php if (isset($_GET['mes'])){ if($_GET['mes']==7){echo "selected"; }} ?> >Julio</option>
								<option value="8" <?php if (isset($_GET['mes'])){ if($_GET['mes']==8){echo "selected"; }} ?> >Agosto</option>
								<option value="9" <?php if (isset($_GET['mes'])){ if($_GET['mes']==9){echo "selected"; }} ?> >Septiembre</option>
								<option value="10" <?php if (isset($_GET['mes'])){ if($_GET['mes']==10){echo "selected"; }} ?> >Octubre</option>
								<option value="11" <?php if (isset($_GET['mes'])){ if($_GET['mes']==11){echo "selected"; }} ?> >Noviembre</option>
								<option value="12" <?php if (isset($_GET['mes'])){ if($_GET['mes']==12){echo "selected"; }} ?> >Diciembre</option>
							</select>
							</div>
							<div class="form-group">
							<button class="btn btn-success btn-outline" onclick="verificarMeses()"><i class="icofont icofont-search"></i> Buscar</button>
							</div>
					</div>
					
					
				</div>
				<hr>
				
			<?php if(isset($_GET['año']) && isset($_GET['mes']) && $resultadoVariables->num_rows>0 ): ?>
				<div class="row" id="ambosBalances">
					<div class="col-md-6 col-sm-12 container-fluid" id="divBalanceGeneral">
						<div class="row titulo"> <div class="col-xs-12">
							<h4>Balance General</h4>
						</div></div>
						<div class="row">
							<div class="col-sm-8"><label for=""><strong>A1 Activo</strong></label></div>
							<div class="col-sm-4"><label for="">S/ <span>{{parseFloat(sumaActivo).toFixed(2)}}</span></label></div>
						</div>
						<div class="row">
							<div class="col-sm-8"><label for="">A11 Activo Corriente</label></div>
							<div class="col-sm-4"><label for="">S/ <span>{{parseFloat(sumaActivosCorriente).toFixed(2)}}</span></label></div>
						</div>
						<div class="row">
							<div class="col-sm-8"><label for="">A111 Disponible</label></div>
							<div class="col-sm-4 "><input type="number" class="esMoneda form-control input-sm " v-model="A111Disponible"> </div>
						</div>
						<div class="row">
							<div class="col-sm-8"><label for="">A1111 Caja</label></div>
							<div class="col-sm-4 "><input type="number" class="esMoneda form-control input-sm " v-model="A1111Caja"> </div>
						</div>
						<div class="row">
							<div class="col-sm-8"><label for="">A1112 Bancos</label></div>
							<div class="col-sm-4 "><input type="number" class="esMoneda form-control input-sm " v-model="A112Bancos"> </div>
						</div>
						<div class="row">
							<div class="col-sm-8"><label for="">A112 Cuentas por cobrar</label></div>
							<div class="col-sm-4 "><input type="number" class="esMoneda form-control input-sm " v-model="A112CuentasPorCobrar"> </div>
						</div>
						<div class="row">
							<div class="col-sm-8"><label for="">A113 Otras cuentas por cobrar</label></div>
							<div class="col-sm-4 "><input type="number" class="esMoneda form-control input-sm " v-model="A113OtrasCuentas"> </div>
						</div>
						<div class="row">
							<div class="col-sm-8"><label for="">A114 Adelantos a proveedores</label></div>
							<div class="col-sm-4 "><input type="number" class="esMoneda form-control input-sm " v-model="A114Adelantos"> </div>
						</div>
						<div class="row">
							<div class="col-sm-8"><label for="">A115 Mercadería</label></div>
							<div class="col-sm-4 "><input type="number" class="esMoneda form-control input-sm " v-model="A115Mercaderia"> </div>
						</div>
						<div class="row">
							<div class="col-sm-8"><label for=""><strong>A12 Activo no corriente</strong></label></div>
							<div class="col-sm-4"><label for="">S/ <span>{{parseFloat(sumaNoCorriente).toFixed(2) }}</span></label></div>
						</div>
						<div class="row">
							<div class="col-sm-8"><label for="">A121 Inmueble, maquinaria y equipo</label></div>
							<div class="col-sm-4 "><input type="number" class="esMoneda form-control input-sm " v-model="A121Inmueble"> </div>
						</div>
						<div class="row">
							<div class="col-sm-8"><label for="">A1211 Terrenos, edificios y otras construcciones</label></div>
							<div class="col-sm-4 "><input type="number" class="esMoneda form-control input-sm " v-model="A1211Terrenos"> </div>
						</div>
						<div class="row">
							<div class="col-sm-8"><label for="">A1212 Maquinaria y equipo y otros (muebles y enseres)</label> <button class="btn btn-primary btn-xs btn-outline" onclick="$('#modalMaquinarias').modal('show');"><i class="icofont icofont-plus"></i></button></div>
							<div class="col-sm-4"><label for="">S/ <span>{{sumaMaquinas}}</span></label></div>
						</div>
						<div class="row ">
							<div class="col-xs-2 encabezado"><label for="">Cant.</label></div>
							<div class="col-xs-5 encabezado"><label for="">Descripcion</label></div>
							<div class="col-xs-3 encabezado"><label for="">P. Unit.</label></div>
							<div class="col-xs-2 encabezado"><label for="">SubTotal</label></div>
						</div>
						<div class="row" v-for="(maquinas, index) in listaMaquinarias">
							<div class="col-xs-2"><span>{{maquinas.cantidad}}</span></div>
							<div class="col-xs-5">
								<button class="btn btn-xs btn-danger btn-outline btn-sinBorde" @click="borrarLista(index, 'maquinaria')"><i class="icofont icofont-close"></i></button>
								<label for="">{{maquinas.descripcion}}</label>
								<button class="btn btn-xs btn-primary btn-outline btn-sinBorde" @click="abrirEditar(index, 'maquinaria')"><i class="icofont icofont-edit"></i></button> 
							</div>
							<div class="col-xs-3"><label for="">{{parseFloat(maquinas.precio).toFixed(2)}}</label></div>
							<div class="col-xs-2"><label for="">{{parseFloat(maquinas.precio * maquinas.cantidad).toFixed(2) }}</label></div>
						</div>
						<div class="row">
							<div class="col-sm-8"><label for="">A1213 Unidad de transporte</label></div>
							<div class="col-sm-4 "><input type="number" class="esMoneda form-control input-sm " v-model="A1213Unidad"> </div>
						</div>
						<div class="row">
							<div class="col-sm-8"><label for=""><strong>A2 Pasivo</strong></label></div>
							<div class="col-sm-4"><label for="">S/ <span>{{parseFloat(sumaPasivo).toFixed(2)}}</span></label></div>
						</div>
						<div class="row">
							<div class="col-sm-8"><label for=""><strong>A2 Pasivo corriente</strong></label></div>
							<div class="col-sm-4"><label for="">S/ <span>{{parseFloat(sumaPasivoCorriente).toFixed(2)}}</span></label></div>
						</div>
						<div class="row">
							<div class="col-sm-8"><label for="">A211 Proveedores</label></div>
							<div class="col-sm-4 "><input type="number" class="esMoneda form-control input-sm " v-model="A211Proveedores"> </div>
						</div>
						<div class="row">
							<div class="col-sm-8"><label for="">A212 Otras cuentas por pagar</label></div>
							<div class="col-sm-4 "><input type="number" class="esMoneda form-control input-sm " v-model="A212OrasCuentas"> </div>
						</div>
						<div class="row">
							<div class="col-sm-8"><label for="">A213 Préstamos a CP menor a 12 meses</label> <button class="btn btn-primary btn-xs btn-outline" onclick="$('#modalPrestamoMenor').modal('show');"><i class="icofont icofont-plus"></i></button></div>
							<div class="col-sm-4"><label for="">S/ <span>{{parseFloat(sumaPrestamoMenor).toFixed(2)}}</span></label></div>
						</div>
						<div class="row">
								<div class="col-xs-2 encabezado"><label for="">Cuotas</label></div>
								<div class="col-xs-5 encabezado"><label for="">Institución</label></div>
								<div class="col-xs-3 encabezado"><label for="">Monto de cuota.</label></div>
								<div class="col-xs-2 encabezado"><label for="">Saldo</label></div>
							</div>
							<div class="row" v-for="(prestamoMenor, index) in listaPrestamosMenor">
								<div class="col-xs-2"><label for="">{{prestamoMenor.cuotas}}</label></div>
								<div class="col-xs-5">
									<button class="btn btn-xs btn-danger btn-outline btn-sinBorde" @click="borrarlista(index, 'prestamosMenor')"><i class="icofont icofont-close"></i></button>
									<label for="">{{prestamoMenor.institucion}}</label>
									<button class="btn btn-xs btn-primary btn-outline btn-sinBorde" @click="abrirEditar(index, 'prestamosMenor')"><i class="icofont icofont-edit"></i></button> 
								</div>
								<div class="col-xs-3"><label for="">{{prestamoMenor.monto}}</label></div>
								<div class="col-xs-2"><label for="">{{prestamoMenor.cuotas * prestamoMenor.monto }}</label></div>
							</div>
						<div class="row">
							<div class="col-sm-8"><label for=""><strong>A22 Pasivo no corriente</strong></label></div>
							<div class="col-sm-4"><label for="">S/ <span>{{parseFloat(sumaPasivoNoCorriente).toFixed(2)}}</span></label></div>
						</div>
						<div class="row">
							<div class="col-sm-8"><label for="">A221 Préstamos a LP mayor a 12 meses</label> <button class="btn btn-primary btn-xs btn-outline" onclick="$('#modalPrestamo').modal('show');"><i class="icofont icofont-plus"></i></button></div>
							<div class="col-sm-4"><label for="">S/ <span>{{parseFloat(sumaPasivoNoCorriente).toFixed(2)}}</span></label></div>
						</div>
						<div class="row">
							<div class="col-xs-2 encabezado"><label for="">Cuotas</label></div>
							<div class="col-xs-5 encabezado"><label for="">Institución</label></div>
							<div class="col-xs-3 encabezado"><label for="">Monto de cuota.</label></div>
							<div class="col-xs-2 encabezado"><label for="">Saldo</label></div>
						</div>
						<div class="row" v-for="(prestamo, index) in listaPrestamos">
							<div class="col-xs-2"><label for="">{{prestamo.cuotas}}</label></div>
							<div class="col-xs-5">
								<button class="btn btn-xs btn-danger btn-outline btn-sinBorde" @click="borrarlista(index, 'prestamosMayor')"><i class="icofont icofont-close"></i></button>
								<label for="">{{prestamo.institucion}}</label>
								<button class="btn btn-xs btn-primary btn-outline btn-sinBorde" @click="abrirEditar(index, 'prestamosMayor')"><i class="icofont icofont-edit"></i></button> 
							</div>
							<div class="col-xs-3"><label for="">{{prestamo.monto}}</label></div>
							<div class="col-xs-2"><label for="">{{prestamo.cuotas * prestamo.monto }}</label></div>
						</div>
						<div class="row">
							<div class="col-sm-8"><label for="">A3 Patrimonio</label></div>
							<div class="col-sm-4"><label for="">S/ <span>{{parseFloat(A31Patrimonio).toFixed(2)}}</span></label></div>
						</div>
						<div class="row">
							<div class="col-sm-8"><label for="">A31 Patrimonio</label></div>
							<div class="col-sm-4 "><input type="number" class="esMoneda form-control input-sm " v-model="A31Patrimonio"> </div>
						</div>
						<div class="row">
							<div class="col-sm-8"><label for="">A4 Pasivo + Patrimonio</label></div>
							<div class="col-sm-4"><label for="">S/ <span>{{parseFloat(sumaA4PasivoyPatrimonio).toFixed(2)}}</span></label></div>
						</div>





					</div>
					<div class="col-md-6 col container-fluid" id="divEstadoResultados">
						<div class="row titulo">
							<div class="col-xs-12"><h4>Estado de resultados</h4></div>
						</div>
						<div class="row">
							<div class="col-sm-8"><label for="">Capital + Interés + Com. y Serv.</label></div>
							<div class="col-sm-4"><label for="">S/ <span>{{parseFloat(capitalConInteres).toFixed(2)}}</span></label></div>
						</div>
						<div class="row">
							<div class="col-sm-8"><label for="">Capital</label></div>
							<div class="col-sm-4"><label for="">S/ <span>{{parseFloat(soloCapital).toFixed(2)}}</span></label></div>
						</div>
						<div class="row">
							<div class="col-sm-8"><label for="">Utilidad bruta-margen bruto</label></div>
							<div class="col-sm-4"><label for="">S/ <span>{{parseFloat(sumaUtilidadBruta).toFixed(2)}}</span></label></div>
						</div>
						<div class="row">
							<div class="col-sm-8"><label for="">Gastos operativos del negocio</label></div>
							<div class="col-sm-4"><label for="">S/ <span>{{parseFloat(sumaGastosOperativos).toFixed(2)}}</span></label></div>
						</div>
						<div class="row">
							<div class="col-xs-8"><label for="">Gastos de personal</label> </div>
							<div class="col-sm-4"><label for="">S/ <span>{{parseFloat(sumaGastoPersonal).toFixed(2)}}</span></label></div>
						</div>
						<div class="row">
							<div class="col-xs-6 encabezado"><label for="">Cargo</label></div>
							<div class="col-xs-3 encabezado"><label for="">Sueldo</label></div>
							<div class="col-xs-3 encabezado"><label for="">Total</label></div>
						</div>
						<div class="row" v-for="personal in listaPersonal">
							<div class="col-xs-6"><label class="mayuscula">{{personal.cargo}}</label></div>
							<div class="col-xs-3"><label for="">{{personal.sueldo}}</label></div>
							<div class="col-xs-3"><label for="">{{personal.cantidad * personal.sueldo}}</label></div>
						</div>
						<div class="row">
							<div class="col-sm-8"><label for="">Gastos de servicios</label> </div>
							<div class="col-sm-4"><label for="">S/ <span>{{parseFloat(sumaServicios).toFixed(2)}}</span></label></div>
						</div>
						<div class="row">
							<div class="col-xs-8 encabezado"><label for="">Servicio</label></div>
							<div class="col-xs-4 encabezado"><label for="">Importe</label></div>
						</div>
						<div class="row" v-for="servicio in listaServicios">
							<div class="col-xs-8"><label for="">{{servicio.servicio}}</label></div>
							<div class="col-xs-4"><label for="">{{servicio.monto}}</label></div>
						</div>
						<div class="row">
							<div class="col-sm-8"><label for="">Utilidad operativa</label></div>
							<div class="col-sm-4"><label for="">S/ <span>{{parseFloat(sumaUtilidadOperativa).toFixed(2)}}</span></label></div>
						</div>
						<div class="row">
							<div class="col-sm-8"><label for="">Otros ingresos</label> </div>
							<div class="col-sm-4"><label for="">S/ <span>{{parseFloat(sumaOtrosIngresos).toFixed(2)}}</span></label></div>
						</div>
						<div class="row"  v-for="otros in listaOtrosIngresos">
							<div class="col-sm-8"><label for="">{{otros.descripcion}}</label></div>
							<div class="col-sm-4"><label for="">S/ <span>{{parseFloat(otros.monto).toFixed(2)}}</span></label></div>
						</div>
						<div class="row">
							<div class="col-sm-8"><label for="">Obligaciones del negocio</label></div>
							<div class="col-sm-4"><label for="">S/ <span>{{parseFloat(sumaObligacionesNegocio).toFixed(2)}}</span></label></div>
						</div>
						<div class="row" v-for="prestamo3 in listaPrestamosMenor">
							<div class="col-sm-8"><label for="">{{prestamo3.institucion}}</label></div>
							<div class="col-sm-4"><label for="">S/ <span>{{prestamo3.cuotas * prestamo3.monto }}</span></label></div>
						</div>
						<div class="row" v-for="prestamo2 in listaPrestamos">
							<div class="col-sm-8"><label for="">{{prestamo2.institucion}}</label></div>
							<div class="col-sm-4"><label for="">S/ <span>{{prestamo2.cuotas * prestamo2.monto }}</span></label></div>
						</div>
						<div class="row">
							<div class="col-sm-8"><label for=""><strong>Utilidad neta del negocio</strong></label></div>
							<div class="col-sm-4"><label for="">S/ <span>{{parseFloat(sumaTotal).toFixed(2)}}</span></label></div>
						</div>


						<div class="row col-xs-12 text-right">
							<button class="btn btn-primary " onclick="guardarBalance()"><i class="icofont icofont-save"></i> Guardar cambios en el balance</button>
						</div>




					</div>
				</div>
			<?php else:  //sino resultadoVariables>0? ?>
				<p>No se encuentra ningún balance encontrado en esta fecha, puede cambiar la fecha.</p>
				<?php if (isset($_GET['año']) and isset($_GET['mes']) ){ ?>
					<p>Puede crear un balance:</p>
					<button class="btn btn-primary btn-outline" onclick="inicializarBalance()"><i class="icofont icofont-archive"></i> Crear balance</button>
				<?php } ?>
			<?php endif //Finaliza resultadoVariables>0?>

				
			<!-- Fin de contenido principal -->
			</div>
		</div>
	</div>
<!-- /#page-content-wrapper -->
</div>


<!-- Modal -->
<div class="modal fade" id="modalMaquinarias" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Insertar maquinarias</h4>
      </div>
      <div class="modal-body">
				<label for="">Cantidad</label>
				<input type="number" class="form-control" v-model="maqCantidad">
				<label for="">Descripción</label>
				<input type="text" class="form-control" v-model="maqDescripcion">
				<label for="">Precio Unitario</label>
				<input type="number" class="form-control esMoneda" v-model="maqPrecio">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" onclick="insertarMaquinaria()">Insertar</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal para prestamos menores-->
<div class="modal fade" id="modalPrestamoMenor" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Insertar préstamos menor a 12 meses</h4>
      </div>
      <div class="modal-body">
				<label for="">Cuotas</label>
				<input type="number" class="form-control" v-model="maqCantidad">
				<label for="">Institución</label>
				<input type="text" class="form-control" v-model="maqDescripcion">
				<label for="">Monto de cuota</label>
				<input type="number" class="form-control esMoneda" v-model="maqPrecio">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" onclick="insertarPrestamoMenor()">Insertar</button>
      </div>
    </div>
  </div>
</div>
<!-- Modal para prestamos mayores-->
<div class="modal fade" id="modalPrestamo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Insertar préstamos mayor a 12 meses</h4>
      </div>
      <div class="modal-body">
				<label for="">Cuotas</label>
				<input type="number" class="form-control" v-model="maqCantidad">
				<label for="">Institución</label>
				<input type="text" class="form-control" v-model="maqDescripcion">
				<label for="">Monto de cuota</label>
				<input type="number" class="form-control esMoneda" v-model="maqPrecio">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" onclick="insertarPrestamo()">Insertar</button>
      </div>
    </div>
  </div>
</div>
<!-- Modal para personal-->
<div class="modal fade" id="modalPersonal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Insertar personal</h4>
      </div>
      <div class="modal-body">
				<label for="">Cantidad</label>
				<input type="number" class="form-control" v-model="maqCantidad">
				<label for="">Cargo</label>
				<input type="text" class="form-control" v-model="maqDescripcion">
				<label for="">Sueldo</label>
				<input type="number" class="form-control esMoneda" v-model="maqPrecio">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" onclick="insertarPersonal()">Insertar</button>
      </div>
    </div>
  </div>
</div>
<!-- Modal para servicios-->
<div class="modal fade" id="modalServicio" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Insertar servicio</h4>
      </div>
      <div class="modal-body">
				<label for="">Servicio</label>
				<input type="text" class="form-control" v-model="maqDescripcion">
				<label for="">Importe</label>
				<input type="number" class="form-control esMoneda" v-model="maqPrecio">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" onclick="insertarServicio()">Insertar</button>
      </div>
    </div>
  </div>
</div>
<!-- Modal para otros ingresos-->
<div class="modal fade" id="modalOtros" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Insertar otros ingresos</h4>
      </div>
      <div class="modal-body">
				<label for="">Descripción</label>
				<input type="text" class="form-control" v-model="maqDescripcion">
				<label for="">Monto</label>
				<input type="number" class="form-control esMoneda" v-model="maqPrecio">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" onclick="insertarOtrosIngresos()">Insertar</button>
      </div>
    </div>
  </div>
</div>
<!-- Modal para otros ingresos-->
<div class="modal fade" id="modalEditar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Editar</h4>
      </div>
      <div class="modal-body">
				<label for="">Cantidad</label>
				<input type="text" class="form-control" v-model="maqCantidad">
				<label for="">Descripción</label>
				<input type="text" class="form-control" v-model="maqDescripcion">
				<label for="">Monto</label>
				<input type="number" class="form-control esMoneda" v-model="maqPrecio">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-warning" @click="actualizarDato()">Actualizar</button>
      </div>
    </div>
  </div>
</div>


</div><!-- /#wrapper -->





<?php include 'footer.php'; ?>
<?php include 'php/modals.php'; ?>
<?php include 'php/existeCookie.php'; ?>

<?php if ( isset($_COOKIE['ckidUsuario']) ){?>

<script>
datosUsuario();

$(document).ready(function(){
	
});

<?php if(isset($_GET['año']) && isset($_GET['mes']) && $resultadoVariables->num_rows>0 ): ?>

var app = new Vue({
  el: '#wrapper',
  data: {
		maqCantidad: 1, maqDescripcion:'', maqPrecio: 0, queActualizo: '', elInidice:0,
		A121Inmueble:<?= $A121Inmueble;?>, A1211Terrenos:<?= $A1211Terrenos;?>, A111Disponible:<?= $A111Disponible;?>, A1111Caja:<?= $A1111Caja;?>, A112Bancos:<?= $A112Bancos;?>, A112CuentasPorCobrar:<?= $A112CuentasPorCobrar;?>, A113OtrasCuentas:<?= $A113OtrasCuentas;?>, A114Adelantos:<?= $A114Adelantos;?>, A115Mercaderia:<?= $A115Mercaderia;?>, A1213Unidad:<?= $A1213Unidad;?>, A211Proveedores: <?= $A211Proveedores;?>, A212OrasCuentas: <?= $A212OrasCuentas;?>, A31Patrimonio: <?= $A31Patrimonio;?>,
		capitalConInteres: <?= $sumaTodoCapitales; ?>, soloCapital: <?= $sumaCapital; ?>,
    listaMaquinarias: <?= $listaMaquinarias; ?>, listaPrestamosMenor: <?= $listaPrestamosMenor; ?>, listaPrestamos: <?= $listaPrestamos; ?>, listaPersonal:<?= $planilla; ?>, listaServicios:<?= $servicios; ?>, listaOtrosIngresos: [{descripcion: 'Mora', monto: <?= $sumaMora?>}]
	},
	methods:{
		borrarLista(index, tipo){
			switch (tipo) {
				case 'maquinaria':
					this.listaMaquinarias.splice(index,1);
					break;
				case 'prestamosMenor':
					this.listaPrestamosMenor.splice(index,1);
					break;
				case 'prestamosMayor':
					this.listaPrestamos.splice(index,1);
					break;
				default: console.log( "no se encontro nada" );
					break;
			}
			
		},
		abrirEditar(index, tipo){
			switch (tipo) {
				case 'maquinaria':
					this.maqCantidad = this.listaMaquinarias[index].cantidad;
					this.maqDescripcion = this.listaMaquinarias[index].descripcion;
					this.maqPrecio = this.listaMaquinarias[index].precio;
					break;
				case 'prestamosMenor':
					this.maqCantidad = this.listaPrestamosMenor[index].cuotas;
					this.maqDescripcion = this.listaPrestamosMenor[index].institucion;
					this.maqPrecio = this.listaPrestamosMenor[index].monto;
					break;
				case 'prestamosMayor':
					this.maqCantidad = this.listaPrestamos[index].cuotas;
					this.maqDescripcion = this.listaPrestamos[index].institucion;
					this.maqPrecio = this.listaPrestamos[index].monto;
					break;
				default:
					break;
			}
			
			this.queActualizo = tipo;
			this.elInidice=index;
			$('#modalEditar').modal('show');
		},
		actualizarDato(){
			switch (this.queActualizo) {
				case 'maquinaria':
					this.listaMaquinarias[this.elInidice].cantidad = this.maqCantidad;
					this.listaMaquinarias[this.elInidice].descripcion = this.maqDescripcion;
					this.listaMaquinarias[this.elInidice].precio = this.maqPrecio;
					break;
				case 'prestamosMenor':
					this.listaPrestamosMenor[this.elInidice].cuotas = this.maqCantidad;
					this.listaPrestamosMenor[this.elInidice].institucion = this.maqDescripcion;
					this.listaPrestamosMenor[this.elInidice].monto = this.maqPrecio;
					break;
				case 'prestamosMayor':
					this.listaPrestamos[this.elInidice].cuotas = this.maqCantidad;
					this.listaPrestamos[this.elInidice].institucion = this.maqDescripcion;
					this.listaPrestamos[this.elInidice].monto = this.maqPrecio;
					break;
				default:
					break;
			}
			$('#modalEditar').modal('hide');
		},
		
	},
	computed:{
		sumaMaquinas(){
			var suma =0;
			/* Object.entries() */
			this.listaMaquinarias.forEach(e => {
				suma += e.cantidad * e.precio;
				//console.log( "cant: " + e.cantidad );
			});
			return suma;
		},
		sumaActivosCorriente(){
			return parseFloat(this.A111Disponible) + parseFloat(this.A1111Caja) + parseFloat(this.A112Bancos) + parseFloat(this.A112CuentasPorCobrar) + parseFloat(this.A113OtrasCuentas) + parseFloat(this.A114Adelantos) + parseFloat(this.A115Mercaderia);
		},
		sumaNoCorriente(){
			return parseFloat(this.A121Inmueble) + parseFloat(this.A1211Terrenos) + parseFloat(this.sumaMaquinas) + parseFloat(this.A1213Unidad);
		},
		sumaActivo(){
			return parseFloat(this.sumaActivosCorriente) + parseFloat(this.sumaNoCorriente) ;
		},
		sumaPasivoCorriente(){
			return parseFloat(this.A211Proveedores) + parseFloat(this.A212OrasCuentas) + parseFloat(this.sumaPrestamoMenor);
		},
		sumaPasivoNoCorriente(){
			var pasivos =0;
			/* Object.entries() */
			this.listaPrestamos.forEach(e => {
				pasivos += e.cuotas * e.monto;
				//console.log( "cant: " + e.cantidad );
			});
			return pasivos;
		},
		sumaPrestamoMenor(){
			var menor =0;
			this.listaPrestamosMenor.forEach(e => {
				menor += e.cuotas * e.monto;
			});
			return menor;
		},
		sumaPasivo(){
			return this.sumaPasivoNoCorriente + this.sumaPasivoCorriente;
		},
		sumaA4PasivoyPatrimonio(){
			return parseFloat( this.A31Patrimonio ) + this.sumaPasivo;
		},
		sumaUtilidadBruta(){
			return this.capitalConInteres - this.soloCapital;
		},
		sumaGastoPersonal(){
			var personal =0;
			this.listaPersonal.forEach(e => {
				personal += e.cantidad * e.sueldo;
			});
			return personal;
		},
		sumaServicios(){
			var services =0;
			this.listaServicios.forEach(e => {
				services += parseFloat(e.monto);
			});
			return services;
		},
		sumaOtrosIngresos(){
			var otros =0;
			this.listaOtrosIngresos.forEach(e => {
				otros += parseFloat(e.monto);
			});
			return otros;
		},
		sumaGastosOperativos(){
			return parseFloat(this.sumaGastoPersonal) + parseFloat(this.sumaServicios)
		},
		sumaUtilidadOperativa(){
			return parseFloat(this.sumaUtilidadBruta) - parseFloat(this.sumaGastosOperativos);
		},
		sumaObligacionesNegocio(){
			return this.sumaPasivo;
		},
		sumaTotal(){
			return parseFloat(this.sumaUtilidadOperativa) + parseFloat(this.sumaOtrosIngresos) - parseFloat(this.sumaObligacionesNegocio)
		}

	}
})

function insertarMaquinaria(){
	app.listaMaquinarias.push({cantidad: app.maqCantidad, descripcion: app.maqDescripcion, precio: app.maqPrecio });
	$('#modalMaquinarias').modal('hide');
	limpiarGlobales()
}
function insertarPrestamo(){
	app.listaPrestamos.push({cuotas: app.maqCantidad, institucion: app.maqDescripcion, monto: app.maqPrecio });
	$('#modalPrestamo').modal('hide');
	limpiarGlobales()
}
function insertarPrestamoMenor(){
	app.listaPrestamosMenor.push({cuotas: app.maqCantidad, institucion: app.maqDescripcion, monto: app.maqPrecio });
	$('#modalPrestamoMenor').modal('hide');
	limpiarGlobales()
}
function insertarPersonal(){
	app.listaPersonal.push({cantidad: app.maqCantidad, cargo: app.maqDescripcion, sueldo: app.maqPrecio });
	$('#modalPersonal').modal('hide');
	limpiarGlobales()
}
function insertarServicio(){
	app.listaServicios.push({servicio: app.maqDescripcion, monto: app.maqPrecio });
	$('#modalServicio').modal('hide');
	limpiarGlobales()
}
function insertarOtrosIngresos(){
	app.listaServicios.push({descripcion: app.maqDescripcion, monto: app.maqPrecio });
	$('#modalOtros').modal('hide');
	limpiarGlobales()
}
function limpiarGlobales(){
	app.maqCantidad=1;
	app.maqDescripcion='';
	app.maqPrecio=0;
}
$("body").on('focus', 'input',function(){
  this.select();
});
function guardarBalance(){
	let maquinarias, menor, mayor;
	if(app.listaMaquinarias.length==0){ maquinarias='[]'; }else{ maquinarias=app.listaMaquinarias; }
	if(app.listaPrestamosMenor.length==0){ menor='[]'; }else{ menor=app.listaPrestamosMenor; }
	if(app.listaPrestamos.length==0){ mayor='[]'; }else{ mayor=app.listaPrestamos; }
	$.ajax({url: 'php/guardarBalance.php', type: 'POST', data: { año:'<?= $_GET['año']?>',mes: '<?= $_GET['mes']?>',
		A111Disponible : app.A111Disponible,
		A1111Caja : app.A1111Caja,
		A112Bancos : app.A112Bancos,
		A112CuentasPorCobrar : app.A112CuentasPorCobrar,
		A113OtrasCuentas : app.A113OtrasCuentas,
		A114Adelantos : app.A114Adelantos,
		A115Mercaderia : app.A115Mercaderia,
		A121Inmueble : app.A121Inmueble,
		A1211Terrenos : app.A1211Terrenos,
		A1213Unidad : app.A1213Unidad,
		A211Proveedores : app.A211Proveedores,
		A212OrasCuentas : app.A212OrasCuentas,
		A31Patrimonio : app.A31Patrimonio,
		listaMaquinarias : maquinarias,
		listaPrestamosMenor : menor,
		listaPrestamos : mayor
	}}).done(function(resp) {
		console.log(resp)
		if(resp=='todo ok'){
			$('#modalGuardadoCorrecto #h1Bien').text('Balance guardado correctamente');
			$('#modalGuardadoCorrecto').modal('show');
			
		}
	});
}

	
<?php endif //Finaliza resultadoVariables>0 ?>

<?php if (isset($_GET['año']) && isset($_GET['mes'])): ?>
function inicializarBalance(){
	$.ajax({url: 'php/inicializarBalance.php', type: 'POST', data: { año: '<?= $_GET['año']; ?>', mes: '<?= $_GET['mes']; ?>'}}).done(function(resp) { console.log( resp );
		if(resp=='todo ok'){ console.log( 'balance creado' );
			location.reload();
		}
	});
}
<?php endif ?>

function verificarMeses(){
	if( $('#sltAño').val()==-1 ){
		alert('Seleccione primero un año de la lista.');
	}else if( $('#sltMes').val()==-1 ){
		alert('Seleccione un mes de la lista.');
	}else{
		window.location.href = 'balanceGeneral.php?año='+ $('#sltAño').val() +'&mes=' +$('#sltMes').val();
	}

}
</script>
<?php } ?>
</body>

</html>