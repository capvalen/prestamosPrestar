<?php 
include "php/variablesGlobales.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Simulador - <?= $Htitle;?></title>
	<?php include 'headers.php'; ?>

</head>
<body>
	<div id="wrapper">
	<?php include 'menu-wrapper.php' ?>

	<div id="page-content-wrapper">
		<div class="container-fluid">
			<h3 class="text-center">Balance del mes de JUNIO 2024</h3>

			<table class="table table-bordered ">
				<thead>
					<tr>
						<th class="text-center" colspan="2">INGRESOS DEL MES DE JUNIO 2024</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>Ingreso de intereses</td>
						<td>S/ 0.00</td>
					</tr>
					<tr>
						<td>Ingreso de moras</td>
						<td>S/ 0.00</td>
					</tr>
					<tr>
						<td>Ingreso de comisión y servicios</td>
						<td>S/ 0.00</td>
					</tr>
					<tr>
						<td>Otros ingresos</td>
						<td>S/ 0.00</td>
					</tr>
				</tbody>
				<tfoot>
					<tr>
						<th>Total ingresos</th>
						<th>S/ 0.00</th>
					</tr>
				</tfoot>
			</table>
			
			<div class="row">
				<div class="col-12 col-md-6">
					<table class="table table-bordered">
						<thead>
							<tr>
								<th class="text-center" colspan="2">DEUDAS PRESTAR HUANCAYO JUNIO</th>
							</tr>
						</thead>
						<tbody>
							<tr v-for="deuda in bancos">
								<td>Bancaria: {{deuda.cajaObservacion}}</td>
								<td>{{parseFloat(deuda.pagoMonto).toFixed(2)}}</td>
							</tr>
						</tbody>
						<tfoot>
							<tr>
								<th>Total de deudas</th>
								<th>0.00</th>
							</tr>
						</tfoot>
					</table>

					<table class="table table-bordered">
						<thead>
							<tr>
								<th class="text-center" colspan="2">EGRESOS JUNIO</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>Deudas prestar Hyo</td>
								<td>0.00</td>
							</tr>
							<tr>
								<td>Gastos prestar Hyo 2</td>
								<td>0.00</td>
							</tr>
						</tbody>
						<tfoot>
							<tr>
								<th>Total de egresos</th>
								<th>0.00</th>
							</tr>
						</tfoot>
					</table>

					<table class="table table-bordered">
					<thead>
							<tr>
								<th class="text-center" colspan="2">MOVIMIENTOS EN JUNIO</th>
							</tr>
						</thead
						<tbody>
							<tr>
								<td>Ingresos de cuotas</td>
								<td>0.00</td>
							</tr>
							<tr>
								<td>Capital recuperado</td>
								<td>0.00</td>
							</tr>
							<tr>
								<td>Saldo Capital</td>
								<td>0.00</td>
							</tr>
							<tr>
								<td>Intereses por cobrar</td>
								<td>0.00</td>
							</tr>
							<tr>
								<td>Comisiones</td>
								<td>0.00</td>
							</tr>
							<tr>
								<td>Saldo a reinvertir para el mes siguiente</td>
								<td>0.00</td>
							</tr>
							<tr>
								<td>Inyección Capital</td>
								<td>0.00</td>
							</tr>
						</tbody>
						<tfoot>
							<tr>
								<th>Total de egresos</th>
								<th>0.00</th>
							</tr>
						</tfoot>
					</table>
				</div>
				<div class="col-12 col-md-6">
					<table class="table table-bordered">
						<thead>
							<tr>
								<th class="text-center" colspan="2">GASTOS PRESTAR HUANCAYO JUNIO</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>Sueldo 1</td>
								<td>0.00</td>
							</tr>
							<tr>
								<td>Sueldo 2</td>
								<td>0.00</td>
							</tr>
							<tr>
								<td>Luz</td>
								<td>0.00</td>
							</tr>
							<tr>
								<td>Agua</td>
								<td>0.00</td>
							</tr>
						</tbody>
						<tfoot>
							<tr>
								<th>Total de deudas</th>
								<th>0.00</th>
							</tr>
						</tfoot>
					</table>

					<table class="table table-bordered">
						<thead>
							<tr>
								<th class="text-center" colspan="2">RESUMEN JUNIO 2024</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>Total ingresos</td>
								<td>0.00</td>
							</tr>
							<tr>
								<td>Total egresos</td>
								<td>0.00</td>
							</tr>
						</tbody>
						<tfoot>
							<tr>
								<th>Resultado final</th>
								<th>0.00</th>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	</div>
	</div>
	<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

	<script>
	const { createApp, ref, onMounted } = Vue

	createApp({
		setup() {
			const entradas = ref([])
			const salidas = ref([])
			const fecha = ref({ mes:-1, año:-1 })
			const bancos = ref([])
			const mes = ref(-1)
			const año = ref(-1)

			onMounted(()=>{
				const urlParams = new URLSearchParams(new URL(window.location.href).search);
				fecha.value.año = parseInt(urlParams.get('año'));
				fecha.value.mes = parseInt(urlParams.get('mes'));
				pedirDatos()
			})

			function pedirDatos(){
				axios.post('php/pedirDatosMensuales.php',{ fecha:fecha.value})
				.then(serv =>{
					console.log(serv.data)
					entradas.value = serv.data.entradas
					salidas.value = serv.data.salidas
					bancos.value = salidas.value.filter(x=> x.idTipoProceso === 93)
					console.log('bancos', bancos.value)
				})
			}

			return {
				mes, año,
				entradas, salidas, bancos
			}
		}
	}).mount('#wrapper')
</script>
	
</body>
</html>