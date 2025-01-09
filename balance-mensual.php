<?php 
include "php/variablesGlobales.php";
include 'php/conkarl.php';

$mes = $_GET['mes'];
switch ($mes) {
	case '1': $mesTexto = 'Enero'; break;
	case '2': $mesTexto = 'Febrero'; break;
	case '3': $mesTexto = 'Marzo'; break;
	case '4': $mesTexto = 'Abril'; break;
	case '5': $mesTexto = 'Mayo'; break;
	case '6': $mesTexto = 'Junio'; break;
	case '7': $mesTexto = 'Julio'; break;
	case '8': $mesTexto = 'Agosto'; break;
	case '9': $mesTexto = 'Septiembre'; break;
	case '10': $mesTexto = 'Octubre'; break;
	case '11': $mesTexto = 'Noviembre'; break;
	case '12': $mesTexto = 'Diciembre'; break;
	default: $mesTexto = 'Mes inválido';
}
$sql=$db->prepare("SELECT `id`, campos FROM `vistacongelada` WHERE año = ? and mes = ? and activo =1;");
$sql->execute([ $_GET['año'], $_GET['mes'] ]);
$cantReporte = $sql->rowCount();
//echo "cant reporte ". $cantReporte;
if($cantReporte==1){
	$row = $sql->fetch(PDO::FETCH_ASSOC);
	$campos = $row['campos'];
}
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
			<h3 class="text-center" id="titulo">Balance del mes de <?= $mesTexto; ?> <?= $_GET['año']; ?></h3>			
			
			<div class="row">
				<div class="col-12 col-md-12">
				<table class="table table-bordered ">
				<thead>
					<tr>
						<th class="text-center text-uppercase" colspan="2">INGRESOS DEL MES DE <?= $mesTexto; ?> <?= $_GET['año']; ?></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>Ingreso de intereses</td>
						<td>S/ {{moneda(sumaIntereses)}}</td>
					</tr>
					<tr>
						<td>Ingreso de moras</td>
						<td>S/ {{moneda(sumaMoras)}}</td>
					</tr>
					<tr>
						<td>Ingreso de comisión y servicios</td>
						<td>S/ {{moneda(sumaComisiones)}}</td>
					</tr>
					<tr>
						<td>Otros ingresos</td>
						<td>S/ {{moneda(sumaOtrosIngresos)}}</td>
					</tr>
				</tbody>
				<tfoot>
					<tr>
						<th>Total ingresos</th>
						<th>S/ {{moneda(sumaTotalIngresos)}}</th>
					</tr>
				</tfoot>
			</table>
				</div>
				<div class="col-6 col-md-6">
					<table class="table table-bordered">
						<thead>
							<tr>
								<th class="text-center text-uppercase" colspan="2">DEUDAS PRESTAR HUANCAYO <?= $mesTexto; ?></th>
							</tr>
						</thead>
						<tbody>
							<tr v-for="banco in bancos">
								<td>Bancaria: {{banco.cajaObservacion}}</td>
								<td>S/ {{moneda(banco.cajaValor)}}</td>
							</tr>
						</tbody>
						<tfoot>
							<tr>
								<th>Total de deudas</th>
								<th>S/ {{moneda(sumaBancos)}}</th>
							</tr>
						</tfoot>
					</table>

					<table class="table table-bordered">
						<thead>
							<tr>
								<th class="text-center text-uppercase" colspan="2">EGRESOS <?= $mesTexto; ?></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>Deudas prestar Hyo</td>
								<td>S/ {{moneda(sumaBancos)}}</td>
							</tr>
							<tr>
								<td>Gastos prestar Hyo</td>
								<td>S/ {{moneda(sumaTodosGastos)}}</td>
							</tr>
						</tbody>
						<tfoot>
							<tr>
								<th>Total de egresos</th>
								<th>S/ {{moneda(sumaTotalEgresos)}}</th>
							</tr>
						</tfoot>
					</table>

					<table class="table table-bordered">
					<thead>
							<tr>
								<th class="text-center text-uppercase" colspan="2">MOVIMIENTOS EN <?= $mesTexto; ?></th>
							</tr>
						</thead
						<tbody>
							<tr>
								<td>Ingresos de cuotas</td>
								<td>S/ {{moneda(sumaCuotas)}}</td>
							</tr>
							<tr>
								<td>Capital recuperado</td>
								<td>S/ {{moneda(sumaCapital)}}</td>
							</tr>
							<tr>
								<td>Saldo Capital por cobrar</td>
								<td>S/ {{moneda(sumaPorCobrarCapital)}}</td>
							</tr>
							<tr>
								<td>Intereses por cobrar</td>
								<td>S/ {{moneda(sumaPorCobrarInteres)}}</td>
							</tr>
							<tr>
								<td>Comisión y Serv. por cobrar</td>
								<td>S/ {{moneda(sumaPorCobrarComision)}}</td>
							</tr>
							<tr>
								<td>Cuotas por cobrar</td>
								<td>S/ {{moneda(sumaPorCobrarCuota)}}</td>
							</tr>
							<tr>
								<td>Saldo a reinvertir para el mes siguiente</td>
								<td>
									<input type="number" class="form-control form-control-sm" id="txtSaldoInvertir" v-model="saldo" <?= $cantReporte==1 ? 'readonly':''?>>
								</td>
							</tr>
							<tr>
								<td>Inyección Capital</td>
								<td>
									<input type="number" class="form-control form-control-sm" id="txtCapitalInvertir" v-model="inyeccion" <?= $cantReporte==1 ? 'readonly':''?>>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div class="col-6 col-md-6">
					<table class="table table-bordered">
						<thead>
							<tr>
								<th class="text-center text-uppercase" colspan="2">GASTOS PRESTAR HUANCAYO <?= $mesTexto; ?></th>
							</tr>
						</thead>
						<tbody>
							<tr v-for="sueldo in sueldos">
								<td class="text-capitalize">Sueldo: {{sueldo.cajaObservacion}}</td>
								<td>S/ {{moneda(sueldo.cajaValor)}}</td>
							</tr>							
							<tr v-for="servicio in servicios">
								<td class="text-capitalize">Servicio: {{servicio.cajaObservacion}}</td>
								<td>S/ {{moneda(servicio.cajaValor)}}</td>
							</tr>
						</tbody>
						<tfoot>
							<tr>
								<th>Total de deudas</th>
								<th>S/ {{moneda(sumaTodosGastos)}}</th>
							</tr>
						</tfoot>
					</table>

					<table class="table table-bordered">
						<thead>
							<tr>
								<th class="text-center text-uppercase" colspan="2">RESUMEN <?= $mesTexto; ?> <?= $_GET['año']; ?></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>Total ingresos</td>
								<td>S/ {{moneda(sumaTotalIngresos)}}</td>
							</tr>
							<tr>
								<td>Total egresos</td>
								<td>S/ {{moneda(sumaTotalEgresos)}}</td>
							</tr>
						</tbody>
						<tfoot>
							<tr>
								<th>Resultado final</th>
								<th>S/ {{moneda(resultadoFinal)}}</th>
							</tr>
						</tfoot>
					</table>
					<div class="d-flex justify-content-center text-center">
						<?php if($cantReporte==0): ?>
						<button class="btn btn-primary btn-outline" @click="congelar()"><i class="icofont-file-text"></i> Congelar reporte</button>
						<?php else: ?>
							<p>Este reporte ya se guardó previamente</p>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	</div>
	<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

	<script>
	const { createApp, ref, onMounted, computed } = Vue

	createApp({
		setup() {
			const entradas = ref([])
			const salidas = ref([])
			const fecha = ref({ mes:-1, año:-1 })
			const intereses = ref([])
			const moras = ref([])
			const cuotas = ref([])
			const otrosIngresos = ref([])
			const bancos = ref([])
			const servicios = ref([])
			const sueldos = ref([])
			const otrosGastos = ref([])
			const falta = ref([])
			const mes = ref(-1)
			const año = ref(-1)
			const saldo = ref(0)
			const inyeccion = ref(0)
			const recuperar = ref([])

			onMounted(()=>{
				const urlParams = new URLSearchParams(new URL(window.location.href).search);
				fecha.value.año = parseInt(urlParams.get('año'));
				fecha.value.mes = parseInt(urlParams.get('mes'));
				pedirDatos()
			})

			function pedirDatos(){
				
				axios.post('php/<?= $cantReporte==0? "pedirReporteGlobal.php" : "congelarReporte.php"?>', {
					pedir: 'listar',
					fecha:fecha.value})
				.then(serv=>{
					intereses.value = serv.data.intereses
					moras.value = serv.data.moras
					cuotas.value = serv.data.cuotas
					otrosIngresos.value = serv.data.otrosIngresos
					bancos.value = serv.data.bancos
					servicios.value = serv.data.servicios
					sueldos.value = serv.data.sueldos
					otrosGastos.value = serv.data.otrosGastos
					falta.value = serv.data.falta
					recuperar.value = serv.data.recuperar ?? []
					saldo.value = serv.data.saldo ?? 0
					inyeccion.value = serv.data.inyeccion ?? 0
				})
			}

			function congelar(){
				axios.post('php/congelarReporte.php', {
					pedir: 'congelar',
					fecha:fecha.value,
					campos: JSON.stringify({
						intereses: intereses.value,
						moras: moras.value,
						cuotas: cuotas.value,
						otrosIngresos: otrosIngresos.value,
						bancos: bancos.value,
						servicios: servicios.value,
						sueldos: sueldos.value,
						otrosGastos: otrosGastos.value,
						falta: falta.value,
						saldo: saldo.value,
						inyeccion: inyeccion.value
					})
				})
				.then(serv=>{
					if(serv.data=='ok'){
						location.reload()
					}else{
						alert('Hubo un error')
					}
				})
			}

			function moneda(valor){
				return parseFloat(valor).toFixed(2)
			}
			const sumaIntereses = computed( ()=> {
				return cuotas.value.reduce( (acc, item) => acc + parseFloat(item.cuotInteres ?? 0) * item.porcentaje, 0)
			})
			const sumaMoras = computed( ()=> {
				return moras.value.reduce( (acc, item) => acc + item.suma, 0)
			})
			const sumaCuotas = computed( ()=> {
				return cuotas.value.reduce( (acc, item) => acc + parseFloat(item.cajaValor) , 0)
				//(parseFloat(item.cuotCuota ?? 0) + parseFloat(item.cuotSeg ?? 0) ) * item.porcentaje 
			})
			const sumaComisiones = computed( ()=> {
				return cuotas.value.reduce( (acc, item) => acc + parseFloat(item.cuotSeg ?? 0) * item.porcentaje , 0)
			})
			const sumaCapital = computed( ()=> {
				return cuotas.value.reduce( (acc, item) => acc + (parseFloat(item.cuotCuota ?? 0) - parseFloat(item.cuotInteres ?? 0)) * item.porcentaje, 0)
			})			
			const sumaOtrosIngresos = computed( ()=> {
				return otrosIngresos.value.reduce( (acc, item) => acc + parseFloat(item.suma), 0)
			})
			const sumaTotalIngresos = computed(()=>{
				return parseFloat(sumaIntereses.value) + parseFloat(sumaMoras.value) + parseFloat(sumaComisiones.value) + parseFloat(sumaOtrosIngresos.value)
			})
			const sumaBancos = computed( ()=> {
				return bancos.value.reduce( (acc, item) => acc + parseFloat(item.cajaValor), 0)
			})
			const sumaSueldos = computed( ()=> {
				return sueldos.value.reduce( (acc, item) => acc + parseFloat(item.cajaValor), 0)
			})
			const sumaServicios = computed( ()=> {
				return servicios.value.reduce( (acc, item) => acc + parseFloat(item.cajaValor), 0)
			})
			const sumaTodosGastos = computed(()=>{
				return sumaSueldos.value + sumaServicios.value
			})			
			const sumaTotalEgresos = computed(()=>{
				return parseFloat(sumaBancos.value) + parseFloat(sumaTodosGastos.value)
			})
			const sumaPorCobrarCapital = computed( ()=> {
				return recuperar.value.reduce( (acc, item) => {
					if(parseFloat(item.adelanto)==0){				
						return acc + parseFloat(item.capital)
					}
					else{
						let porcentaje = parseFloat(item.adelanto)  / (parseFloat(item.capital) + parseFloat(item.intereses) + parseFloat(item.seguro) )
						return acc + parseFloat(parseFloat(item.capital) * (1-porcentaje))
					}
				}, 0)
			})
			const sumaPorCobrarInteres = computed( ()=> {
				return recuperar.value.reduce( (acc, item) => {
					if(parseFloat(item.adelanto)==0){
						return acc + parseFloat(item.intereses)
					}
					else{
						let porcentaje = parseFloat(item.adelanto)  / (parseFloat(item.capital) + parseFloat(item.intereses) + parseFloat(item.seguro) )
						return acc + parseFloat(parseFloat(item.intereses) * (1-porcentaje))
					}
				}, 0)
			})
			const sumaPorCobrarComision = computed( ()=> {
				return recuperar.value.reduce( (acc, item) => {
					if(parseFloat(item.adelanto)==0){
						return acc + parseFloat(item.seguro)
					}
					else{
						let porcentaje = parseFloat(item.adelanto)  / (parseFloat(item.capital) + parseFloat(item.intereses) + parseFloat(item.seguro) )
						return acc + parseFloat(parseFloat(item.seguro) * (1-porcentaje))
					}
				}, 0)
			})
			/*const sumaPorCobrarInteres = computed( ()=> {
				return recuperar.value.reduce( (acc, item) => acc + parseFloat(item.intereses ?? 0) , 0)
			})
			const sumaPorCobrarComision = computed( ()=> {
				return recuperar.value.reduce( (acc, item) => acc + parseFloat(item.seguro ?? 0) , 0)
			})*/
			const sumaPorCobrarCuota = computed( ()=> {
				return recuperar.value.reduce( (acc, item) => acc + parseFloat(item.cuota ?? 0) , 0)
				//return sumaPorCobrarCapital.value + sumaPorCobrarInteres.value + sumaPorCobrarComision.value
			})
			const resultadoFinal = computed(()=>{
				return parseFloat(sumaTotalIngresos.value ) - parseFloat(sumaTotalEgresos.value )
			})

			return {
				mes, año,
				entradas, salidas,
				intereses, moras, cuotas, otrosIngresos, bancos, servicios, recuperar, sueldos, otrosGastos, falta,
				moneda,
				sumaIntereses, sumaMoras, sumaComisiones, sumaOtrosIngresos, sumaTotalIngresos,
				sumaBancos, sumaSueldos, sumaServicios, sumaTodosGastos,
				sumaCuotas, sumaCapital,
				sumaTotalEgresos, sumaPorCobrarCapital, sumaPorCobrarInteres, sumaPorCobrarComision, sumaPorCobrarCuota,
				resultadoFinal,
				congelar, saldo, inyeccion
			}
		}
	}).mount('#wrapper')
</script>
	
</body>
<style>
	#txtSaldoInvertir, #txtCapitalInvertir{
		margin-bottom:0px!important;
	}
	@media print {
  .col-6 {
    width: 49%;
		
		float:left;
  }
	#titulo{font-size: 20px;}
}
</style>
</html>