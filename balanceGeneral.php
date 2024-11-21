<!DOCTYPE html>
<html lang="es">
<head>

		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, shrink-to-fit=no, initial-scale=1">
		<meta name="description" content="">
		<meta name="author" content="">

		<title>Balance - Sistema Préstamos</title>

		<!-- Bootstrap Core CSS -->
		<?php include 'headers.php'; ?>
		<!-- development version, includes helpful console warnings -->
		<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
</head>

<body>

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
						<label for="my-select">Fecha de balance</label>
						<div class="form-group">
							<select id="sltAño" class="custom-select form-control" style="margin-bottom: 0px;">
								<option value="-1">Año</option>
								<?php for ($i=2024; $i <= date('Y') ; $i++) { ?>
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
								<button class="btn btn-warning btn-outline" onclick="IrAReporte()"><i class="icofont icofont-search"></i> Ver reporte</button>
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

</div><!-- /#wrapper -->





<?php include 'footer.php'; ?>
<?php include 'php/existeCookie.php'; ?>

<?php if ( isset($_COOKIE['ckidUsuario']) ){?>
	<script>
		function IrAReporte(){
			let año = document.getElementById('sltAño').value
			let mes = document.getElementById('sltMes').value
			window.location = `balance-mensual.php?año=${año}&mes=${mes}`

		}
	</script>
<?php } ?>
</body>

</html>