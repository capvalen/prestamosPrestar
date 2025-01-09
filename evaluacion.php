
<?php 
include 'php/conkarl.php';
require_once('vendor/autoload.php');
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<?php include 'headers.php'; ?>

	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Prestamos Prestar</title>
	
	<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
	<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
	<link rel="stylesheet" href="css/awesome-bootstrap-checkbox.css?version=1.0.1">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.15.10/dist/sweetalert2.all.min.js
		"></script>
		<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.15.10/dist/sweetalert2.min.css
		" rel="stylesheet">
</head>
<body>
	<div id="wrapper">
		<?php include 'menu-wrapper.php' ?>
		<div id="page-content-wrapper" >
			<div class="container-fluid" id="app">
				<div class="row ">
					<section class="col-lg-12 contenedorDeslizable " >
						<h1 class="text-center">Evaluación por Crédito</h1>
						<div class="card mb-3">
							<div class="card-body">
								<div class="row">
									<div class="col-md-9">
										<template v-if="evaluacion">
											<p><span class="fw-bold text-capitalize">DNI:</span> <span>{{evaluacion.dni}}</span></p>
											<p><span class="fw-bold text-capitalize">Cliente:</span> <span>{{evaluacion.nombres}}</span></p>
											<p><span class="fw-bold text-capitalize">Giro de negocio:</span> <span>{{evaluacion.giro}}</span></p>
											<p><span class="fw-bold text-capitalize">Dirección del negocio:</span> <span>{{evaluacion.direccion}}</span></p>
										</template>
									</div>
									<div class="col-md-3">
										<button class="btn btn-primary btn-outline" @click="editables.giro = evaluacion.giro" data-toggle="modal" data-target="#modalCabeceras"><i class="bi bi-trash"></i> Editar datos básicos</button>
									</div>
									</div>
								</div>
							</div>
						</div>
						<div class="d-flex justify-content-between">
							<h5 class="fw-bold mt-4">Ingresos del cliente</h5>
							<div class="d-flex d-grid align-items-center"><button class="btn btn-sm btn-outline btn-primary" @click="addIngreso"><i class="bi bi-plus-lg"></i> Agregar ingreso</button></div>
						</div>
						<div class="card mb-3">
							<div class="card-body">
								<table class="table table-hover table-bordered">
									<tbody>
										<tr v-for="(ingreso, index) in ingresos">
											<td class="text-center">{{index+1}}</td>
											<td>{{ingreso.razon}}</td>
											<td>{{dosDecimales(ingreso.monto)}}</td>
											<td>
												<button class="btn btn-outline btn-danger btn-sm border-0" @click="borrarIngreso(index)"><i class="bi bi-trash"></i></button>
											</td>
										</tr>
									</tbody>
									<tfoot class="table-secondary">
										<td class="text-end fw-bold" colspan="2">Total Ingresos</td>
										<td class="fw-bold" colspan="2">S/ {{sumaIngresos}}</td>
									</tfoot>
								</table>
							</div>
						</div>
						<div class="row">
							<div class="col col-md-6">
								<div class="card mb-3">
									<div class="card-body">
										<div class="d-flex justify-content-between">
											<h5 class="fw-bold mt-4">Deudas del cliente</h5>
											<div class="d-flex d-grid align-items-center"><button class="btn btn-sm btn-outline btn-primary" @click="addDeuda()"><i class="bi bi-plus-lg"></i> Agregar deuda</button></div>
										</div>
										<table class="table table-hover table-bordered">
											<tbody>
												<tr v-for="(deuda, index) in deudas">
													<td class="text-center">{{index+1}}</td>
													<td>{{deuda.razon}}</td>
													<td>{{dosDecimales(deuda.monto)}}</td>
													<td>
														<button class="btn btn-outline btn-danger btn-sm border-0" @click="borrarDeuda(index)"><i class="bi bi-trash"></i></button>
													</td>
												</tr>
											</tbody>
											<tfoot class="table-secondary">
												<td class="text-end fw-bold" colspan="2">Total deudas</td>
												<td class="fw-bold" colspan="2">S/ {{sumaDeudas}} </td>
											</tfoot>
										</table>
									</div>
								</div>
								<div class="card my-3">
									<div class="card-body">
										<table class="table table-hover table-bordered my-3">
											<tbody>
												<tr>
													<td>Total de Deudas</td>
													<td>{{dosDecimales(sumaDeudas)}}</td>
												</tr>
												<tr>
													<td>Total de Gastos</td>
													<td>{{dosDecimales(sumaGastos)}}</td>
												</tr>
											</tbody>
											<tfoot class="table-secondary">
												<td class="text-end fw-bold" >Total Egresos</td>
												<td class="fw-bold">S/ {{dosDecimales(totalEgresos)}} </td>
											</tfoot>
										</table>
										<table class="table table-hover table-bordered my-3">
											<tbody>
												<tr>
													<td>Total de Ingresos</td>
													<td>{{dosDecimales(sumaIngresos)}}</td>
												</tr>
												<tr>
													<td>Total de Egresos</td>
													<td>{{dosDecimales(totalEgresos)}}</td>
												</tr>
											</tbody>
											<tfoot class="table-secondary">
												<td class="text-end fw-bold" >Resultado</td>
												<td class="fw-bold">S/ {{dosDecimales(sumaIngresos-totalEgresos)}} </td>
											</tfoot>
										</table>
									</div>
								</div>
							</div>
							<div class="col col-md-6">
								<div class="card">
									<div class="card-body">
										<div class="d-flex justify-content-between">
											<h5 class="fw-bold mt-4">Gastos del cliente</h5>
											<div class="d-flex d-grid align-items-center"><button class="btn btn-sm btn-outline btn-primary" @click="addGasto()"><i class="bi bi-plus-lg"></i> Agregar gasto</button></div>
										</div>
										<table class="table table-hover table-bordered">
											<tbody>
												<tr v-for="(gasto, index) in gastos">
													<td class="text-center">{{index+1}}</td>
													<td>{{gasto.razon}}</td>
													<td>{{dosDecimales(gasto.monto)}}</td>
													<td>
														<button class="btn btn-outline btn-danger btn-sm border-0" @click="borrarGasto(index)"><i class="bi bi-trash"></i></button>
													</td>
												</tr>
											</tbody>
											<tfoot class="table-secondary">
												<td class="text-end fw-bold" colspan="2">Total Gastos</td>
												<td class="fw-bold" colspan="2">S/ {{sumaGastos}} </td>
											</tfoot>
										</table>
									</div>
								</div>
								<div class="col-md-6">
									<p class="fs-5 mt-5 text-center fw-bold">Cuota a prestar:</p>
									<input type="text" class="form-control">
								</div>
							</div>
						</div>
					</section>

					<!-- Modal -->
					<div class="modal fade" id="modalCabeceras" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
						<div class="modal-dialog" role="document">
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
									<h4 class="modal-title" id="myModalLabel">Editar datos</h4>
								</div>
								<div class="modal-body">
									<p>Edite los datos:</p>
									<p class="fw-bold">Giro de negocio:</p>
									<input type="text" class="form-control" v-model="editables.giro">
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
									<button type="button" class="btn btn-primary" data-dismiss="modal" @click="updateHeader()">Actualizar</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- Fin de app -->
		</div>
	</div>
	
	<?php include 'footer.php'; ?>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/moment.min.js"></script>
	<script>
		const { createApp, ref, onMounted, computed } = Vue
	
		createApp({
			setup() {
				const servidor = 'https://intranet.citobiolab.com/api/'
				const ingresos = ref([])
				const deudas = ref([])
				const gastos = ref([])
				const idPrestamo = ref(-1)
				const idCliente = ref(-1)
				const idEvaluacion = ref(-1)
				const editables = ref({
					giro:'', direccion:''
				})
				const evaluacion = ref([])
				
				onMounted(()=>{
					const urlObj = new URL(url);
					idEvaluacion.value = urlObj.searchParams.get("idEvaluacion");
					idPrestamo.value = urlObj.searchParams.get("idPrestamo");
					pedirDatos()
					/*
					if(!localStorage.getItem('idUsuario')) window.location = 'index.html'
					else{
					}
					*/
				})

				function pedirDatos(){
					axios.post('php/Evaluacion.php',{
						pedir: 'listar',
						idEvaluacion: idEvaluacion.value, idPrestamo: idPrestamo.value
					})
					.then(serv => {
						idCliente.value = parseInt(serv.data.cliente.idCliente);
						evaluacion.value = serv.data.cliente;
						ingresos.value = serv.data.ingresos;
						deudas.value = serv.data.deudas;
						gastos.value = serv.data.gastos;
					})
				}

				function addDeuda(){
					Swal.fire({
						title: 'Ingrese sus datos',
						html: `
							<label>Razón de la deuda</label>
							<input type="text" id="txtRazonDeuda" class="form-control" placeholder="Razón" autocomplete=off>
							<label>Monto de la deuda</label>
							<input type="number" id="txtMontoDeuda" class="form-control" placeholder="Monto" autocomplete=off>
						`,
						showCancelButton: true,
						confirmButtonText: 'Aceptar',
						cancelButtonText: 'Cancelar',
						preConfirm: () => {
							// Obtener los valores de los inputs
							const input1 = document.getElementById('txtRazonDeuda').value;
							const input2 = document.getElementById('txtMontoDeuda').value;

							if (!input1 || !input2) {
								// Validar que los inputs no estén vacíos
								Swal.showValidationMessage('Por favor, complete ambos campos');
							}

							return { input1, input2 };
						}
					}).then((result) => {
						if (result.isConfirmed) {
							// Mostrar los valores ingresados
							const { input1, input2 } = result.value;

							deudas.value.push({
								razon: input1, monto: input2
							})
							
							actualizar()
						}
					});
				}

				function addIngreso(){
					Swal.fire({
						title: 'Ingrese sus datos',
						html: `
							<label>Razón del ingreso</label>
							<input type="text" id="txtRazonIngreso" class="form-control" placeholder="Razón" autocomplete=off>
							<label>Monto del ingreso</label>
							<input type="number" id="txtMontoIngreso" class="form-control" placeholder="Monto" autocomplete=off>
						`,
						showCancelButton: true,
						confirmButtonText: 'Aceptar',
						cancelButtonText: 'Cancelar',
						preConfirm: () => {
							// Obtener los valores de los inputs
							const input1 = document.getElementById('txtRazonIngreso').value;
							const input2 = document.getElementById('txtMontoIngreso').value;

							if (!input1 || !input2) {
								// Validar que los inputs no estén vacíos
								Swal.showValidationMessage('Por favor, complete ambos campos');
							}

							return { input1, input2 };
						}
					}).then((result) => {
						if (result.isConfirmed) {
							// Mostrar los valores ingresados
							const { input1, input2 } = result.value;

							ingresos.value.push({
								razon: input1, monto: input2
							})
							
							actualizar()
						}
					});
				}

				function addGasto(){
					Swal.fire({
						title: 'Ingrese sus datos',
						html: `
							<label>Razón del gasto</label>
							<input type="text" id="txtRazonGasto" class="form-control" placeholder="Razón" autocomplete=off>
							<label>Monto del gasto</label>
							<input type="number" id="txtMontoGasto" class="form-control" placeholder="Monto" autocomplete=off>
						`,
						showCancelButton: true,
						confirmButtonText: 'Aceptar',
						cancelButtonText: 'Cancelar',
						preConfirm: () => {
							// Obtener los valores de los inputs
							const input1 = document.getElementById('txtRazonGasto').value;
							const input2 = document.getElementById('txtMontoGasto').value;

							if (!input1 || !input2) {
								// Validar que los inputs no estén vacíos
								Swal.showValidationMessage('Por favor, complete ambos campos');
							}

							return { input1, input2 };
						}
					}).then((result) => {
						if (result.isConfirmed) {
							// Mostrar los valores ingresados
							const { input1, input2 } = result.value;

							gastos.value.push({
								razon: input1, monto: input2
							})
							
							actualizar()
						}
					});
				}

				function actualizar(){
					axios.post('php/Evaluacion.php',{
						pedir: 'actualizar',
						idPrestamo: idPrestamo.value, idEvaluacion: idEvaluacion.value,
						ingresos: ingresos.value,
						deudas: deudas.value,
						gastos: gastos.value,
					}).then(serv=>{

						if(serv.data.accion == 'ok'){
							Swal.fire({
								title: 'Datos ingresados y actualizados',
								icon: 'success'
							});
							if(idEvaluacion.value == 'no'){
								window.location = 'evaluacion.php?idEvaluacion='+serv.data.idEvaluacion+'&idPrestamo='+idPrestamo.value
							}
						}
						else
							Swal.fire({
								title: 'Error al actualizar',
								text: `No se insertaron Razón: ${input1}, Monto: S/ ${input2}`,
								icon: 'danger'
							});

						
					})

					
				}

				function borrarDeuda(index){
					Swal.fire({
						title: `¿Estás seguro de borrar "${deudas.value[index].razon}" de monto S/ ${deudas.value[index].monto}?`,
						icon: 'warning',
						showCancelButton: true,
						confirmButtonColor: '#3085d6',
						cancelButtonColor: '#d33',
						confirmButtonText: 'Sí, eliminar',
						cancelButtonText: 'Cancelar'
					}).then((result) => {
						if (result.isConfirmed) {
							deudas.value.splice(index,1)
							actualizar()
						}
					});
				}

				function borrarIngreso(index){
					Swal.fire({
						title: `¿Estás seguro de borrar "${deudas.value[index].razon}" de monto S/ ${deudas.value[index].monto}?`,
						icon: 'warning',
						showCancelButton: true,
						confirmButtonColor: '#3085d6',
						cancelButtonColor: '#d33',
						confirmButtonText: 'Sí, eliminar',
						cancelButtonText: 'Cancelar'
					}).then((result) => {
						if (result.isConfirmed) {
							ingresos.value.splice(index,1)
							actualizar()
						}
					});
				}

				function borrarGasto(index){
					Swal.fire({
						title: `¿Estás seguro de borrar "${deudas.value[index].razon}" de monto S/ ${deudas.value[index].monto}?`,
						icon: 'warning',
						showCancelButton: true,
						confirmButtonColor: '#3085d6',
						cancelButtonColor: '#d33',
						confirmButtonText: 'Sí, eliminar',
						cancelButtonText: 'Cancelar'
					}).then((result) => {
						if (result.isConfirmed) {
							gastos.value.splice(index,1)
							actualizar()
						}
					});
				}

				function updateHeader(){
					axios.post('php/Evaluacion.php',{
						pedir: 'updateHeader', giro: editables.value.giro, idCliente: idCliente.value
					}).then(resp => {
						if(resp.data == 'ok'){
							evaluacion.value.giro = editables.value.giro
							Swal.fire({
								title: 'Actualizado con éxito',
								icon:'success'
							})
						}else{
							Swal.fire({
								title: 'Hubo un error al actualizar',
								icon:'error'
							})
						}
					})
				}

				function dosDecimales(num){
					return parseFloat(num).toFixed(2)
				}
				const sumaIngresos = computed(()=>{
					return dosDecimales(ingresos.value.reduce( (acc, item) => acc + parseFloat(item.monto) , 0 ))
				})
				const sumaGastos = computed(()=>{
					return dosDecimales(gastos.value.reduce( (acc, item) => acc + parseFloat(item.monto) , 0 ))
				})

				const sumaDeudas = computed(()=>{
					return dosDecimales(deudas.value.reduce( (acc, item) => acc + parseFloat(item.monto) , 0 ))
				})

				const totalEgresos = computed(()=>{
					return parseFloat(sumaDeudas.value) + parseFloat(sumaGastos.value)
				})

				return {
					servidor, dosDecimales, sumaIngresos, sumaDeudas, sumaGastos, idEvaluacion, idPrestamo, idCliente,
					pedirDatos, editables, evaluacion, ingresos, deudas, gastos,
					addDeuda, borrarDeuda, addGasto, addIngreso, totalEgresos, updateHeader
				}
			}
		}).mount('#app')
	</script>
	<style>
		.card{
			border:1px solid #ccc;
			border-radius: 5px;
		}
		.card-body{
			padding:10px;
		}
		.mt-5{margin: 50px 0 0  0;}
		.mb-3{margin: 0 0 30px  0;}
		.my-3{margin: 30px 0;}
		.justify-content-between{
			display: flex;
   		justify-content: space-between;
		}
		.fw-bold{font-weight: bold;}
		.swal2-modal{
			width: 40em;
		}
	</style>
</body>
</html>